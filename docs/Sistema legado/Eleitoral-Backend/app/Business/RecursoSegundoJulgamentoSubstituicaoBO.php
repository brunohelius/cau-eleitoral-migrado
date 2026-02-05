<?php
/*
 * RecursoSegundoJulgamentoSubstituicaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ChapaEleicao;
use App\Entities\JulgamentoFinal;
use App\Entities\JulgamentoSegundaInstanciaSubstituicao;
use App\Entities\RecursoJulgamentoFinal;
use App\Entities\RecursoSegundoJulgamentoSubstituicao;
use App\Entities\SubstituicaoJulgamentoFinal;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailRecursoJulgamentoFinalJob;
use App\Jobs\EnviarEmailRecursoSegundoJulgamentoSubstituicaoJob;
use App\Mail\RecursoJulgamentoFinalCadastradoMail;
use App\Mail\RecursoSegundoJulgamentoSubstituicaoMail;
use App\Repository\JulgamentoFinalRepository;
use App\Repository\RecursoSegundoJulgamentoSubstituicaoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\JulgamentoFinalTO;
use App\To\ProfissionalTO;
use App\To\RecursoJulgamentoFinalTO;
use App\To\RecursoSegundoJulgamentoSubstituicaoTO;
use App\To\SubstituicaoJulgamentoFinalTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'JulgamentoFinal'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class RecursoSegundoJulgamentoSubstituicaoBO extends AbstractBO
{

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * @var JulgamentoFinalRepository
     */
    private $julgamentoFinalRepository;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var IndicacaoJulgamentoFinalBO
     */
    private $indicacaoJulgamentoFinalBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * @param $id
     * @return RecursoSegundoJulgamentoSubstituicao|null
     */
    public function findById($id)
    {
        /** @var RecursoSegundoJulgamentoSubstituicao $recurso */
        $recurso = $this->getRecursoSegundoJulgamentoSubstituicaoRepository()->find($id);

        return $recurso;
    }

    /**
     * Salva o julgamento final da chapa da eleição
     *
     * @param recursoSegundoJulgamentoSubstituicaoTO $recursoSegundoJulgamentoSubstituicaoTO
     * @return RecursoSegundoJulgamentoSubstituicaoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function salvar(RecursoSegundoJulgamentoSubstituicaoTO $recursoSegundoJulgamentoSubstituicaoTO)
    {
        $arquivos = $recursoSegundoJulgamentoSubstituicaoTO->getArquivos();

        /** @var ArquivoGenericoTO|null $arquivo */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validacaoIncialSalvarJulgamentoFinal($recursoSegundoJulgamentoSubstituicaoTO);

        /** @var JulgamentoSegundaInstanciaSubstituicao|null $julgamentoSubstituicao */
        $julgamentoSubstituicao = $this->getJulgamentoSegundaInstanciaSubstituicaoBO()->getPorId(
            $recursoSegundoJulgamentoSubstituicaoTO->getIdJulgamentoSegundaInstanciaSubstituicao()
        );
        $idChapaEleicao = $julgamentoSubstituicao->getSubstituicaoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao()->getId();

        $profissional = $this->getProfissionalBO()->getPorId($recursoSegundoJulgamentoSubstituicaoTO->getIdProfissional());

        $this->validacaoComplementarSalvarJulgamentoFinal($recursoSegundoJulgamentoSubstituicaoTO);

        try {
            $this->beginTransaction();

            $recursoSegundoJulgamento =
                $this->prepararRecursoSalvar($recursoSegundoJulgamentoSubstituicaoTO, $julgamentoSubstituicao, $profissional, $arquivo);

            $recursoSegundoJulgamento = $this->getRecursoSegundoJulgamentoSubstituicaoRepository()->persist($recursoSegundoJulgamento);

            $this->salvarHistoricoRecursoJulgamentoFinal($recursoSegundoJulgamento);

            $this->getChapaEleicaoBO()->atualizarStatusChapaJulgamentoFinal(
                $idChapaEleicao, Constants::STATUS_CHAPA_JULG_FINAL_AGUARDANDO
            );

            if (!empty($arquivo)) {
                $this->salvarArquivo(
                    $recursoSegundoJulgamento->getId(),
                    $arquivo->getArquivo(),
                    $recursoSegundoJulgamento->getNomeArquivoFisico()
                );
            }

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailRecursoSegundoJulgamentoSubstituicaoJob($recursoSegundoJulgamento->getId()));

        return RecursoSegundoJulgamentoSubstituicaoTO::newInstanceFromEntity($recursoSegundoJulgamento);
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivo($id)
    {
        /** @var RecursoSegundoJulgamentoSubstituicao $recursoSegundoJulgamentoSubstituicao */
        $recursoSegundoJulgamentoSubstituicao = $this->getRecursoSegundoJulgamentoSubstituicaoRepository()->find($id);

        if (!empty($recursoSegundoJulgamentoSubstituicao)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioRecursoSegundoJulgamentoSubstituicao($recursoSegundoJulgamentoSubstituicao->getId());
            echo($caminho);
            return $this->getArquivoService()->getArquivo(
                $caminho, $recursoSegundoJulgamentoSubstituicao->getNomeArquivoFisico(), $recursoSegundoJulgamentoSubstituicao->getNomeArquivo()
            );
        }
    }

    /**
     * Responsável por realizar envio de e-mail após o cadastro Recurso do Julgamento
     *
     * @param $idRecursoSegundoJulgamentoSubstituicao
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailCadastroRecursoJulgamentoSubstituicao($idRecursoSegundoJulgamentoSubstituicao)
    {
        /** @var RecursoSegundoJulgamentoSubstituicao $recursoSegundoJulgamentoSubstituicao */
        $recursoSegundoJulgamentoSubstituicao = $this->getRecursoSegundoJulgamentoSubstituicaoRepository()->find($idRecursoSegundoJulgamentoSubstituicao);
        $recursoSegundoJulgamentoSubstituicaoTO = RecursoSegundoJulgamentoSubstituicaoTO::newInstanceFromEntity($recursoSegundoJulgamentoSubstituicao);

        $chapaEleicao = $recursoSegundoJulgamentoSubstituicao->getJulgamentoSegundaInstanciaSubstituicao()->
        getSubstituicaoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao();

        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao(
            $chapaEleicao->getId(), 5, 5
        );

        $isAddEmailsResponsaveis = Utils::getDataHoraZero() > Utils::getDataHoraZero($atividade->getDataFim());

        $destinatarios = $this
            ->getDestinatariosEnvioEmailCadastro($chapaEleicao, $atividade, $isAddEmailsResponsaveis);

        if (!empty($destinatarios)) {

            $idTipoCandidatura = $chapaEleicao->getTipoCandidatura()->getId();

            $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
            $constantEmail =
                $isIES ? Constants::EMAIL_RECONSIDERACAO_JULGAMENTO_FINAL : Constants::EMAIL_RECURSO_JULGAMENTO_FINAL;

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(),
                $constantEmail
            );

            if (!empty($emailAtividadeSecundaria)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios($destinatarios);

                Email::enviarMail(new RecursoSegundoJulgamentoSubstituicaoMail($emailTO, $recursoSegundoJulgamentoSubstituicaoTO));
            }
        }
    }

    /**
     * Método auxiliar para buscar os e-mails dos destinatários
     * @param ChapaEleicao $chapaEleicao
     * @param AtividadeSecundariaCalendario $atividade
     * @param bool $isAdicionarEmailsResponsaveis
     * @return array
     * @throws NegocioException
     */
    private function getDestinatariosEnvioEmailCadastro(
        $chapaEleicao,
        $atividade,
        $isAdicionarEmailsResponsaveis
    ): array
    {
        $idTipoCandidatura = $chapaEleicao->getTipoCandidatura()->getId();

        $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
        $idCauUf = $isIES ? null : $chapaEleicao->getIdCauUf();

        $emailsComissao = [];
        /*$emailsComissao = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $atividade->getId(), $idCauUf
        );*/

        $emailsAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        $emailsResponsaveis = [];
        if ($isAdicionarEmailsResponsaveis) {
            $emailsResponsaveis = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa(
                $chapaEleicao->getId()
            );
        }
        $destinatarios = array_merge($emailsComissao, $emailsAssessores, $emailsResponsaveis);
        return $destinatarios;
    }

    /**
     * Método auxiliar para retornar o tipo de e-mail a ser enviado
     * @param $idStatusJulgamento
     * @return int
     */
    private function getTipoEmailCadastroJulgamento($idStatusJulgamento): int
    {
        $idTipoEmail = Constants::EMAIL_JULGAMENTO_FINAL_DEFERIDO;
        if ($idStatusJulgamento == Constants::STATUS_JULG_FINAL_INDEFERIDO) {
            $idTipoEmail = Constants::EMAIL_JULGAMENTO_FINAL_INDEFERIDO;
        }
        return $idTipoEmail;
    }

    /**
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param RecursoSegundoJulgamentoSubstituicaoTO $recursoSegundoJulgamentoSubstituicaoTO
     * @throws NegocioException
     */
    private function validacaoIncialSalvarJulgamentoFinal($recursoSegundoJulgamentoSubstituicaoTO)
    {
        if (empty($recursoSegundoJulgamentoSubstituicaoTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }
    }

    /**
     * Método faz validação complementar do cadastrado do julgamento do recurso
     *
     * @param RecursoSegundoJulgamentoSubstituicaoTO $recursoSegundoJulgamentoSubstituicaoTO
     * @param ChapaEleicao $chapaEleicao
     * @param $eleicao
     * @throws NegocioException
     */
    private function validacaoComplementarSalvarJulgamentoFinal($recursoSegundoJulgamentoSubstituicaoTO)
    {
        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }

        $idJulgamentoSegundaInstancia = $recursoSegundoJulgamentoSubstituicaoTO->getIdJulgamentoSegundaInstanciaSubstituicao();
        $idJulgamento = $this->getRecursoSegundoJulgamentoSubstituicaoRepository()->findBy
        (['julgamentoSegundaInstanciaSubstituicao' => $idJulgamentoSegundaInstancia]);

        if (!empty($idJulgamento)) {
            throw new NegocioException(Lang::get('messages.recurso_julgamento_segunda_instancia_substituicao.ja_realizado'));
        }
    }

    /**
     * Método auxiliar para preparar entidade RecursoJulgamentoRecursoImpugnacao para cadastro
     *
     * @param RecursoSegundoJulgamentoSubstituicaoTO $recursoSegundoJulgamentoSubstituicaoTO
     * @param JulgamentoSegundaInstanciaSubstituicao|null $julgamentoSubstituicao
     * @param ProfissionalTO|null $profissional
     * @param ArquivoGenericoTO|null $arquivo
     * @return RecursoSegundoJulgamentoSubstituicao
     * @throws Exception
     */
    private function prepararRecursoSalvar(
        $recursoSegundoJulgamentoSubstituicaoTO,
        $julgamentoSubstituicao,
        $profissional,
        $arquivo)
    {
        $nomeArquivoFisico = empty($arquivo)? '' : $this->getArquivoService()->getNomeArquivoFormatado(
            $arquivo->getNome(), Constants::PREFIXO_ARQ_RECURSO_SEGUNDO_JULGAMENTO
        );
        $nomeArquivo = empty($arquivo)? '' : $arquivo->getNome();

        $recursoSegundoJulgamentoSubstituicao = RecursoSegundoJulgamentoSubstituicao::newInstance([
            'descricao' => $recursoSegundoJulgamentoSubstituicaoTO->getDescricao(),
            'nomeArquivo' => $nomeArquivo,
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'dataCadastro' => Utils::getData(),
            'profissional' => ['id' => $profissional->getId()],
            'julgamentoSegundaInstanciaSubstituicao' => ['id' => $julgamentoSubstituicao->getId()],
        ]);

        return $recursoSegundoJulgamentoSubstituicao;
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $idJulgamentoFinal
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idJulgamentoFinal, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioRecursoSubstituicao($idJulgamentoFinal),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param RecursoSegundoJulgamentoSubstituicao $recursoSegundoJulgamento
     * @throws Exception
     */
    private function salvarHistoricoRecursoJulgamentoFinal(RecursoSegundoJulgamentoSubstituicao $recursoSegundoJulgamento): void
    {
        $historico = $this->getHistoricoBO()->criarHistorico(
            $recursoSegundoJulgamento,
            Constants::HISTORICO_PROF_TIPO_RECURSO_SEGUNDO_JUGAMENTO_SUBSTITUICAO,
            Constants::HISTORICO_PROF_ACAO_INSERIR,
            Constants::HISTORICO_INCLUSAO_REC_JULG_SUB
        );
        $this->getHistoricoBO()->salvar($historico);
    }

    /**
     * @param $idChapa
     * @return array
     * @throws Exception
     */
    public function getPorChapa($idChapa)
    {
        $recursos = $this->getRecursoSegundoJulgamentoSubstituicaoRepository()->getPorChapa($idChapa);

        $recursosSubstituicoesTO = [];
        $sequencia = 0;

        if (!empty($recursos)) {
            /** @var RecursoSegundoJulgamentoSubstituicao $recurso */
            foreach ($recursos as $recurso) {
                $substituicaoJulgamentoFinalTO = SubstituicaoJulgamentoFinalTO::newInstanceFromEntityRecurso($recurso);
                $sequencia++;
                $substituicaoJulgamentoFinalTO->setSequencia($sequencia);

                $julgamentoSegundaInstancia = Arr::get($recurso, 'julgamentoRecursoPedidoSubstituicao');

                if (!empty($recurso->getJulgamentoRecursoPedidoSubstituicao())) {
                    $substituicaoJulgamentoFinalTO->setHasJulgamentoSegundaInstancia(true);
                }

                array_push($recursosSubstituicoesTO, $substituicaoJulgamentoFinalTO);
            }
        }

        return $recursosSubstituicoesTO;
    }

    /**
     * Retorna uma nova instância de 'MembroComissaoBO'.
     *
     * @return MembroComissaoBO
     */
    private function getMembroComissaoBO()
    {
        if (empty($this->membroComissaoBO)) {
            $this->membroComissaoBO = app()->make(MembroComissaoBO::class);
        }

        return $this->membroComissaoBO;
    }

    /**
     * Retorna uma nova instância de 'ChapaEleicaoBO'.
     *
     * @return ChapaEleicaoBO
     */
    private function getChapaEleicaoBO()
    {
        if (empty($this->chapaEleicaoBO)) {
            $this->chapaEleicaoBO = app()->make(ChapaEleicaoBO::class);
        }

        return $this->chapaEleicaoBO;
    }

    /**
     * Retorna uma nova instância de 'EleicaoBO'.
     *
     * @return EleicaoBO|mixed
     */
    private function getEleicaoBO()
    {
        if (empty($this->eleicaoBO)) {
            $this->eleicaoBO = app()->make(EleicaoBO::class);
        }

        return $this->eleicaoBO;
    }

    /**
     * Retorna uma nova instância de 'EmailAtividadeSecundariaBO'.
     *
     * @return EmailAtividadeSecundariaBO|mixed
     */
    private function getEmailAtividadeSecundariaBO()
    {
        if (empty($this->emailAtividadeSecundariaBO)) {
            $this->emailAtividadeSecundariaBO = app()->make(EmailAtividadeSecundariaBO::class);
        }

        return $this->emailAtividadeSecundariaBO;
    }

    /**
     * Retorna uma nova instância de 'HistoricoChapaEleicaoBO'.
     *
     * @return HistoricoBO
     */
    private function getHistoricoBO()
    {
        if (empty($this->historicoBO)) {
            $this->historicoBO = app()->make(HistoricoBO::class);
        }

        return $this->historicoBO;
    }

    /**
     * Retorna uma nova instância de 'ArquivoService'.
     *
     * @return ArquivoService|mixed
     */
    private function getArquivoService()
    {
        if (empty($this->arquivoService)) {
            $this->arquivoService = app()->make(ArquivoService::class);
        }

        return $this->arquivoService;
    }

    /**
     * Retorna uma nova instância de 'RecursoSegundoJulgamentoSubstituicaoRepository'.
     *
     * @return RecursoSegundoJulgamentoSubstituicaoRepository|\Doctrine\ORM\EntityRepository
     */
    private function getRecursoSegundoJulgamentoSubstituicaoRepository()
    {
        if (empty($this->recursoSegundoJulgamentoSubstituicaoRepository)) {
            $this->recursoSegundoJulgamentoSubstituicaoRepository = $this->getRepository(RecursoSegundoJulgamentoSubstituicao::class);
        }

        return $this->recursoSegundoJulgamentoSubstituicaoRepository;
    }

    /**
     * Retorna uma nova instância de 'AtividadeSecundariaCalendarioBO'.
     *
     * @return AtividadeSecundariaCalendarioBO|mixed
     */
    private function getAtividadeSecundariaCalendarioBO()
    {
        if (empty($this->atividadeSecundariaCalendarioBO)) {
            $this->atividadeSecundariaCalendarioBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }

        return $this->atividadeSecundariaCalendarioBO;
    }

    /**
     * Retorna uma nova instância de 'IndicacaoJulgamentoFinalBO'.
     *
     * @return RecursoIndicacaoBO|mixed
     */
    private function getRecursoIndentificacaoBO()
    {
        if (empty($this->recursoIndentificacaoBO)) {
            $this->recursoIndentificacaoBO = app()->make(RecursoIndicacaoBO::class);
        }

        return $this->recursoIndentificacaoBO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalBO'.
     *
     * @return JulgamentoSegundaInstanciaSubstituicaoBO
     */
    private function getJulgamentoSegundaInstanciaSubstituicaoBO()
    {
        if (empty($this->julgamentoSegundaInstanciaSubstituicaoBO)) {
            $this->julgamentoSegundaInstanciaSubstituicaoBO = app()->make(JulgamentoSegundaInstanciaSubstituicaoBO::class);
        }

        return $this->julgamentoSegundaInstanciaSubstituicaoBO;
    }

    /**
     * Retorna uma nova instância de 'RecursoIndicacaoBO'.
     *
     * @return RecursoIndicacaoBO
     */
    private function getRecursoIndicacaoBO()
    {
        if (empty($this->recursoIndicacaoBO)) {
            $this->recursoIndicacaoBO = app()->make(RecursoIndicacaoBO::class);
        }

        return $this->recursoIndicacaoBO;
    }

    /**
     * Retorna uma nova instância de 'CorporativoService'.
     *
     * @return CorporativoService|mixed
     */
    private function getCorporativoService()
    {
        if (empty($this->corporativoService)) {
            $this->corporativoService = app()->make(CorporativoService::class);
        }

        return $this->corporativoService;
    }

    /**
     * Retorna uma nova instância de 'MembroChapaBO'.
     *
     * @return MembroChapaBO|mixed
     */
    private function getMembroChapaBO()
    {
        if (empty($this->membroChapaBO)) {
            $this->membroChapaBO = app()->make(MembroChapaBO::class);
        }

        return $this->membroChapaBO;
    }

    /**
     * Retorna uma nova instância de 'ProfissionalBO'.
     *
     * @return ProfissionalBO|mixed
     */
    private function getProfissionalBO()
    {
        if (empty($this->profissionalBO)) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }

        return $this->profissionalBO;
    }
}
