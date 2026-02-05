<?php
/*
 * RecursoJulgamentoFinalBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\JulgamentoFinal;
use App\Entities\RecursoJulgamentoFinal;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailRecursoJulgamentoFinalJob;
use App\Mail\RecursoJulgamentoFinalCadastradoMail;
use App\Repository\RecursoJulgamentoFinalRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\RecursoJulgamentoFinalTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'JulgamentoFinal'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class RecursoJulgamentoFinalBO extends AbstractBO
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
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var HistoricoProfissionalBO
     */
    private $historicoProfissionalBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var RecursoJulgamentoFinalRepository
     */
    private $recursoJulgamentoFinalRepository;

    /**
     * @var JulgamentoSegundaInstanciaRecursoBO
     */
    private $julgamentoSegundaInstanciaRecursoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Recupera o Recurso Julgamento Final de acordo com ID informado.
     *
     * @param $idRecursoJulgamentoFinal
     * @return RecursoJulgamentoFinalTO|null
     * @throws Exception
     */
    public function getRecursoJulgamentoFinalPorId($idRecursoJulgamentoFinal){
        return $this->getRecursoJulgamentoFinalRepository()->getRecursoJulgamentoFinalPorId($idRecursoJulgamentoFinal);
    }

    /**
     * Retorna um Julgamento Final conforme o id informado da chapa.
     *
     * @param $idChapaEleicao
     * @return RecursoJulgamentoFinalTO
     * @throws Exception
     */
    public function getRecursoJulgamentoFinalPorChapaEleicao($idChapaEleicao)
    {
        $julgamentoRecurso = $this->getRecursoJulgamentoFinalRepository()->getPorChapaEleicao($idChapaEleicao);

        if($julgamentoRecurso) {
            $julgamentoSegundaInstancia = $this->getJulgamentoSegundaInstanciaRecursoBO()
                ->getIdJulgamentoSegundaInstanciaRecursoPorRecursoFinal($julgamentoRecurso->getId());

            if(!empty($julgamentoSegundaInstancia)){
                $julgamentoRecurso->setHasJulgamentoSegundaInstancia(true);
            }
        }

        return $julgamentoRecurso;
    }

    /**
     * Retorna um Julgamento Final conforme o id informado da chapa.
     *
     * @param $idChapaEleicao
     * @return RecursoJulgamentoFinal
     * @throws Exception
     */
    public function findPorChapaEleicao($idChapaEleicao)
    {
        return $this->getRecursoJulgamentoFinalRepository()->findPorChapaEleicao($idChapaEleicao);
    }

    /**
     * Salva o julgamento final da chapa da eleição
     *
     * @param recursoJulgamentoFinalTO $recursoJulgamentoFinalTO
     * @return RecursoJulgamentoFinalTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function salvar(recursoJulgamentoFinalTO $recursoJulgamentoFinalTO)
    {
        $arquivos = $recursoJulgamentoFinalTO->getArquivos();

        /** @var ArquivoGenericoTO|null $arquivo */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validacaoIncialSalvarRecursoJulgamentoFinal($recursoJulgamentoFinalTO);

        /** @var JulgamentoFinal|null $julgamentoFinal */
        $julgamentoFinal = $this->getJulgamentoFinalBO()->getPorId($recursoJulgamentoFinalTO->getIdJulgamentoFinal());

        $this->validacaoComplementarSalvarRecursoJulgamentoFinal($recursoJulgamentoFinalTO, $julgamentoFinal);

        try {
            $this->beginTransaction();

            $recursoJulgamentoFinal = $this->prepararRecursoSalvar($recursoJulgamentoFinalTO, $julgamentoFinal,
                $arquivo);

            $this->getRecursoJulgamentoFinalRepository()->persist($recursoJulgamentoFinal);

            $this->getRecursoIndentificacaoBO()->salvarIndicacoes($recursoJulgamentoFinal, $recursoJulgamentoFinalTO);

            $this->salvarHistoricoRecursoJulgamentoFinal($recursoJulgamentoFinal);

            $this->getChapaEleicaoBO()->atualizarStatusChapaJulgamentoFinal(
                $julgamentoFinal->getChapaEleicao()->getId(), Constants::STATUS_CHAPA_JULG_FINAL_AGUARDANDO
            );

            if (!empty($arquivo)) {
                $this->salvarArquivo(
                    $recursoJulgamentoFinal->getId(),
                    $arquivo->getArquivo(),
                    $recursoJulgamentoFinal->getNomeArquivoFisico()
                );
            }

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailRecursoJulgamentoFinalJob($recursoJulgamentoFinal->getId()));

        return RecursoJulgamentoFinalTO::newInstanceFromEntity($recursoJulgamentoFinal);
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
        /** @var RecursoJulgamentoFinal $recursoJulgamentoFinal */
        $recursoJulgamentoFinal = $this->getRecursoJulgamentoFinalRepository()->find($id);

        if (!empty($recursoJulgamentoFinal)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioRecursoJulgamentoFinal($recursoJulgamentoFinal->getId());

            return $this->getArquivoService()->getArquivo(
                $caminho, $recursoJulgamentoFinal->getNomeArquivoFisico(), $recursoJulgamentoFinal->getNomeArquivo()
            );
        }
    }

    /**
     * Responsável por realizar envio de e-mail após o cadastro do julgamento
     *
     * @param $idRecursoJulgamentoFinal
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailCadastroRecursoJulgamento($idRecursoJulgamentoFinal)
    {
        /** @var RecursoJulgamentoFinal $recursoJulgamento */
        $recursoJulgamento = $this->getRecursoJulgamentoFinalRepository()->find($idRecursoJulgamentoFinal);
        $recursoJulgamentoFinalTO = RecursoJulgamentoFinalTO::newInstanceFromEntity($recursoJulgamento);

        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao(
            $recursoJulgamento->getJulgamentoFinal()->getChapaEleicao()->getId(), 5, 2
        );

        $isAddEmailsResponsaveis = Utils::getDataHoraZero() > Utils::getDataHoraZero($atividade->getDataFim());

        $destinatarios = $this
            ->getDestinatariosEnvioEmailCadastro($recursoJulgamento, $atividade, $isAddEmailsResponsaveis);

        if (!empty($destinatarios)) {

            $idTipoCandidatura = $recursoJulgamento->getJulgamentoFinal()->getChapaEleicao()->getTipoCandidatura()->getId();

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

                Email::enviarMail(new RecursoJulgamentoFinalCadastradoMail($emailTO, $recursoJulgamentoFinalTO));
            }
        }
    }

    /**
     * Método auxiliar para buscar os e-mails dos destinatários
     * @param RecursoJulgamentoFinal $recursoJulgamento
     * @param AtividadeSecundariaCalendario $atividade
     * @param bool $isAdicionarEmailsResponsaveis
     * @return array
     * @throws NegocioException
     */
    private function getDestinatariosEnvioEmailCadastro(
        $recursoJulgamento,
        $atividade,
        $isAdicionarEmailsResponsaveis
    ): array {
        $idTipoCandidatura = $recursoJulgamento->getJulgamentoFinal()->getChapaEleicao()->getTipoCandidatura()->getId();

        $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
        $idCauUf = $isIES ? null : $recursoJulgamento->getJulgamentoFinal()->getChapaEleicao()->getIdCauUf();

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
                $recursoJulgamento->getJulgamentoFinal()->getChapaEleicao()->getId()
            );
        }
        $destinatarios = array_merge($emailsComissao, $emailsAssessores, $emailsResponsaveis);
        return $destinatarios;
    }

    /**
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param RecursoJulgamentoFinalTO $julgamentoFinalTO
     * @throws NegocioException
     */
    private function validacaoIncialSalvarRecursoJulgamentoFinal($julgamentoFinalTO)
    {

        if (empty($julgamentoFinalTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }
    }

    /**
     * Método faz validação complementar do cadastrado do julgamento do recurso
     *
     * @param RecursoJulgamentoFinalTO $recursoJulgamentoFinalTO
     * @param JulgamentoFinal $julgamentoFinal
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function validacaoComplementarSalvarRecursoJulgamentoFinal($recursoJulgamentoFinalTO, $julgamentoFinal)
    {
        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }

        if (!empty($julgamentoFinal->getRecursoJulgamentoFinal())) {
            throw new NegocioException(Lang::get('messages.recurso_julgamento_final.ja_realizado'));
        }

        if ($julgamentoFinal->getStatusJulgamentoFinal()->getId() == Constants::STATUS_JULG_FINAL_DEFERIDO) {
            throw new NegocioException(Lang::get('messages.julgamento_final.julgamento_final_deferido'));
        }

        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao(
            $julgamentoFinal->getChapaEleicao()->getId(), 5, 2
        );

        $inicioVigente = Utils::getDataHoraZero() >= Utils::getDataHoraZero($atividadeSecundaria->getDataInicio());
        $fimVigente = Utils::getDataHoraZero() <= Utils::getDataHoraZero($atividadeSecundaria->getDataFim());
        if (!($inicioVigente && $fimVigente)) {
            throw new NegocioException(Lang::get('messages.recurso_julgamento_final.vigencia_fechada'));
        }

        $hasIndicacoesJulgamento = (
            (is_array($julgamentoFinal->getIndicacoes()) && !empty($julgamentoFinal->getIndicacoes())) ||
            ($julgamentoFinal->getIndicacoes() instanceof Collection && $julgamentoFinal->getIndicacoes()->count() > 0)
        );

        if ($hasIndicacoesJulgamento && empty($recursoJulgamentoFinalTO->getIndicacoes())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $isResponsavel = $this->getMembroChapaBO()->isMembroResponsavelChapa(
            $julgamentoFinal->getChapaEleicao()->getId(),
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );

        if (!$isResponsavel) {
            throw new NegocioException(Lang::get('messages.permissao.visualizacao_membros_responsaveis_chapa'));
        }
    }

    /**
     * Método auxiliar para preparar entidade RecursoJulgamentoRecursoImpugnacao para cadastro
     *
     * @param RecursoJulgamentoFinalTO $recursoJulgamentoFinalTO
     * @param JulgamentoFinal|null $julgamentoFinal
     * @param ArquivoGenericoTO|null $arquivo
     * @return RecursoJulgamentoFinal
     * @throws Exception
     */
    private function prepararRecursoSalvar($recursoJulgamentoFinalTO, $julgamentoFinal, $arquivo)
    {
        $nomeArquivoFisico = empty($arquivo) ? '' : $this->getArquivoService()->getNomeArquivoFormatado(
            $arquivo->getNome(), Constants::PREFIXO_ARQ_RECURSO_JULGAMENTO_FINAL
        );
        $nomeArquivo = empty($arquivo) ? '' : $arquivo->getNome();

        return RecursoJulgamentoFinal::newInstance([
            'descricao' => $recursoJulgamentoFinalTO->getDescricao(),
            'nomeArquivo' => $nomeArquivo,
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'dataCadastro' => Utils::getData(),
            'profissional' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional],
            'julgamentoFinal' => ['id' => $julgamentoFinal->getId()],
        ]);
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
            $this->getArquivoService()->getCaminhoRepositorioRecursoJulgamentoFinal($idJulgamentoFinal),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param RecursoJulgamentoFinal $recursoJulgamentoFinal
     * @throws Exception
     */
    private function salvarHistoricoRecursoJulgamentoFinal(RecursoJulgamentoFinal $recursoJulgamentoFinal): void
    {
        $historico = $this->getHistoricoProfissionalBO()->criarHistorico(
            $recursoJulgamentoFinal->getId(),
            Constants::HISTORICO_PROF_TIPO_RECURSO_JUGAMENTO_FINAL,
            Constants::HISTORICO_PROF_ACAO_INSERIR,
            Constants::HISTORICO_INCLUSAO_REC_JULG
        );
        $this->getHistoricoProfissionalBO()->salvar($historico);
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
     * Retorna uma nova instância de 'HistoricoProfissionalBO'.
     *
     * @return HistoricoProfissionalBO
     */
    private function getHistoricoProfissionalBO()
    {
        if (empty($this->historicoProfissionalBO)) {
            $this->historicoProfissionalBO = app()->make(HistoricoProfissionalBO::class);
        }

        return $this->historicoProfissionalBO;
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
     * Retorna uma nova instância de 'RecursoJulgamentoFinalRepository'.
     *
     * @return RecursoJulgamentoFinalRepository
     */
    private function getRecursoJulgamentoFinalRepository()
    {
        if (empty($this->recursoJulgamentoFinalRepository)) {
            $this->recursoJulgamentoFinalRepository = $this->getRepository(RecursoJulgamentoFinal::class);
        }

        return $this->recursoJulgamentoFinalRepository;
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
     * @return JulgamentoFinalBO
     */
    private function getJulgamentoFinalBO()
    {
        if (empty($this->julgamentoFinalBO)) {
            $this->julgamentoFinalBO = app()->make(JulgamentoFinalBO::class);
        }

        return $this->julgamentoFinalBO;
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
     * Retorna uma nova instância de 'JulgamentoSegundaInstanciaRecursoBO'.
     *
     * @return JulgamentoSegundaInstanciaRecursoBO
     */
    private function getJulgamentoSegundaInstanciaRecursoBO()
    {
        if (empty($this->julgamentoSegundaInstanciaRecursoBO)) {
            $this->julgamentoSegundaInstanciaRecursoBO = app()->make(JulgamentoSegundaInstanciaRecursoBO::class);
        }

        return $this->julgamentoSegundaInstanciaRecursoBO;
    }
}
