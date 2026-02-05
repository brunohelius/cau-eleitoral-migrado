<?php
/*
 * JulgamentoFinalBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\IndicacaoJulgamentoRecursoPedidoSubstituicao;
use App\Entities\IndicacaoJulgamentoSegundaInstanciaSubstituicao;
use App\Entities\JulgamentoRecursoPedidoSubstituicao;
use App\Entities\SubstituicaoJulgamentoFinal;
use App\Jobs\EnviarEmailJulgamentoSegundaIntanciaSubstituicaoJob;
use App\Mail\JulgamentoSegundaInstanciaSubstituicaoDecisaoMail;
use App\To\IndicacaoJulgamentoSegundaInstanciaSubstituicaoTO;
use App\To\SubstituicaoRecursoTO;
use Exception;
use App\Util\Email;
use App\Util\Utils;
use App\To\ArquivoTO;
use App\Config\Constants;
use App\To\MembroChapaTO;
use App\Exceptions\Message;
use App\Entities\MembroChapa;
use App\To\ArquivoGenericoTO;
use App\To\JulgamentoFinalTO;
use App\Entities\ChapaEleicao;
use App\To\ListMembrosChapaTO;
use App\Service\ArquivoService;
use App\Entities\JulgamentoFinal;
use App\Service\CorporativoService;
use Doctrine\ORM\NoResultException;
use App\Exceptions\NegocioException;
use Illuminate\Support\Facades\Lang;
use App\Mail\AtividadeSecundariaMail;
use App\To\IndicacaoJulgamentoFinalTO;
use App\Entities\RecursoJulgamentoFinal;
use Doctrine\ORM\NonUniqueResultException;
use App\Jobs\EnviarEmailJulgamentoFinalJob;
use App\Mail\JulgamentoFinalCadastradoMail;
use App\Repository\JulgamentoFinalRepository;
use App\Entities\AtividadeSecundariaCalendario;
use App\To\JulgamentoSegundaInstanciaSubstituicaoTO;
use App\Entities\JulgamentoSegundaInstanciaSubstituicao;
use App\Repository\JulgamentoSegundaInstanciaSubstituicaoRepository;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'JulgamentoSegundaInstanciaSubstituicao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoSegundaInstanciaSubstituicaoBO extends AbstractBO
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
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var IndicacaoJulgamentoSegundaInstanciaSubstituicaoBO
     */
    private $indicacaoJulgamentoSegundaInstanciaSubstituicaoBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var ProporcaoConselheiroExtratoBO
     */
    private $proporcaoConselheiroExtratoBO;

    /**
     * @var SubstituicaoJulgamentoFinalBO
     */
    private $substituicaoJulgamentoFinalBO;

    /**
     * @var JulgamentoSegundaInstanciaSubstituicaoRepository
     */
    private $julgamentoSegundaInstanciaSubstituicaoRepository;


    /**
     * Construtor da classe.
     */
    public function __construct()
    {}

    /**
     * Salva o julgamento final da chapa da eleição
     *
     * @param JulgamentoSegundaInstanciaSubstituicaoTO $julgamentoSegundaInstanciaSubstituicaoTO
     * @return JulgamentoSegundaInstanciaSubstituicaoTO
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function salvar(JulgamentoSegundaInstanciaSubstituicaoTO $julgamentoSegundaInstanciaSubstituicaoTO)
    {
        $arquivos = $julgamentoSegundaInstanciaSubstituicaoTO->getArquivos();
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validarCamposObrigatorios($julgamentoSegundaInstanciaSubstituicaoTO, $arquivo);

        $substituicaoJulgamentoFinal = $this->getSubstituicaoJulgamentoFinalBO()->getRecursoJulgamentoFinalPorId(
                $julgamentoSegundaInstanciaSubstituicaoTO->getIdSubstituicaoJulgamentoFinal()
        );

        $julgamentoSegundaInstanciaSubstAnterior = $this->getJulgamentoPaiParaSalvar(
            $julgamentoSegundaInstanciaSubstituicaoTO
        );

        if (
            !empty($substituicaoJulgamentoFinal->getJulgamentoFinalTo())
            && !empty($substituicaoJulgamentoFinal->getJulgamentoFinalTo()->getChapaEleicao())
        ) {
            $idChapaEleicao = $substituicaoJulgamentoFinal->getJulgamentoFinalTo()->getChapaEleicao()->getId();

            $chapaEleicao = $this->getChapaEleicaoBO()->getPorId($idChapaEleicao, true);
            $eleicao = $this->getEleicaoBO()->getEleicaoPorChapaEleicao($idChapaEleicao);
        } else {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $this->validacaoComplementarSalvarJulgamentoFinal($julgamentoSegundaInstanciaSubstituicaoTO, $chapaEleicao, $eleicao);

        try {
            $this->beginTransaction();

            $julgSegundaInstancia = $this->prepararJulgamentoSalvar(
                $julgamentoSegundaInstanciaSubstituicaoTO,
                $substituicaoJulgamentoFinal,
                $arquivo,
                $julgamentoSegundaInstanciaSubstAnterior
            );

            $this->getJulgamentoSegundaInstanciaSubstituicaoRepository()->persist($julgSegundaInstancia);

            $this->salvarIndicacoes($julgamentoSegundaInstanciaSubstituicaoTO->getIndicacoes(), $julgSegundaInstancia);

            $this->salvarHistoricoJulgamento($julgSegundaInstancia);

            $this->getChapaEleicaoBO()->atualizarChapaEleicaoPosJulgamentoFinal(
                $chapaEleicao, $julgamentoSegundaInstanciaSubstituicaoTO->getIdStatusJulgamentoFinal()
            );

            if (
                empty($julgamentoSegundaInstanciaSubstituicaoTO->getIdJulgamentoSegundaInstanciaSubstituicaoPai())
                && $julgamentoSegundaInstanciaSubstituicaoTO->getIdStatusJulgamentoFinal() == Constants::STATUS_JULG_FINAL_INDEFERIDO
            ) {
                $this->getMembroChapaBO()->rejeitarConvitesAposCadastroJulgFinalSegundaInstancia($chapaEleicao->getId());
            }

            $this->salvarArquivo($julgSegundaInstancia, $arquivo, $julgamentoSegundaInstanciaSubstAnterior);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if (empty($julgamentoSegundaInstanciaSubstituicaoTO->getIdJulgamentoSegundaInstanciaSubstituicaoPai())) {
            Utils::executarJOB(new EnviarEmailJulgamentoSegundaIntanciaSubstituicaoJob($julgSegundaInstancia->getId()));
        }

        return JulgamentoSegundaInstanciaSubstituicaoTO::newInstanceFromEntity($julgSegundaInstancia);
    }

    /**
     * Retorna o Julgamento Final por ID.
     *
     * @param $id
     * @return JulgamentoFinal | mixed
     */
    public function getPorId($id)
    {
        return $this->getJulgamentoSegundaInstanciaSubstituicaoRepository()->find($id);
    }

    /**
     * Retorna todos os julgamento do pedido de substituição de uma chapa
     * @param $idChapa
     * @return array
     * @throws NonUniqueResultException
     */
    public function getPorChapaEleicao($idChapa)
    {
        $julgamentos = $this->getJulgamentoSegundaInstanciaSubstituicaoRepository()->getPorChapa($idChapa);

        return $this->converterJulgamentoSubstituicaoRecursoTO($julgamentos);
    }

    /**
     * Retorna todos os julgamento do pedido de substituição de uma chapa
     * @param $idSubstituicaoJulgFinal
     * @return array
     */
    public function getRetificacoes($idSubstituicaoJulgFinal)
    {
        $julgamentos = $this->getJulgamentoSegundaInstanciaSubstituicaoRepository()->getPorSubstituicao(
            $idSubstituicaoJulgFinal
        );

        $julgamentosTO = [];
        if (!empty($julgamentos) && count($julgamentos) > 1) {
            array_pop($julgamentos);

            $julgamentosTO = $this->converterJulgamentoSubstituicaoRecursoTO($julgamentos);
        }

        return $julgamentosTO;
    }

    /**
     * @param JulgamentoSegundaInstanciaSubstituicao[] $julgamentos
     */
    private function converterJulgamentoSubstituicaoRecursoTO($julgamentos)
    {
        $julgamentosTO = [];
        if (!empty($julgamentos)) {
            $sequencia = 1;
            foreach ($julgamentos as $julgamento) {
                $julgamentoTO = SubstituicaoRecursoTO::newInstanceFromJulgamentoSegundaInstanciaSubstituicao($julgamento);
                $julgamentoTO->setSequencia($sequencia);
                $julgamentosTO[] = $julgamentoTO;
                $sequencia++;
            }
        }

        return $julgamentosTO;
    }

    /**
     * Retorna o Julgamento Segunda Instancia Substituição pelo ID do Julgamento Final.
     *
     * @param $idChapaEleicao
     * @return JulgamentoSegundaInstanciaSubstituicao | mixed
     * @throws NonUniqueResultException
     */
    public function getUltimoPorChapa($idChapaEleicao)
    {
        return $this->getJulgamentoSegundaInstanciaSubstituicaoRepository()->getUltimoPorChapa($idChapaEleicao);
    }

    /**
     * Responsável por realizar envio de e-mail após o cadastro do julgamento
     *
     * @param $idJulgamentoSubstituicao
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailCadastroJulgamento($idJulgamentoSubstituicao)
    {
        /** @var JulgamentoSegundaInstanciaSubstituicao $julgamento */
        $julgamento = $this->getJulgamentoSegundaInstanciaSubstituicaoRepository()->getPorSubstituicaoSegundaInstancia(
            $idJulgamentoSubstituicao
        );
        $julgamentoFinalTO = JulgamentoSegundaInstanciaSubstituicaoTO::newInstanceFromEntity($julgamento);

        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao(
            $julgamento->getSubstituicaoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao()->getId(),
            Constants::ATIVIDADE_PRIMARIA_JULGAMENTO_FINAL_SEGUNDA_INSTANCIA,
            Constants::ATIVIDADE_SECUNDARIA_JULGAMENTO_FINAL_SEGUNDA_INSTANCIA
        );

        $destinatarios = $this->getDestinatariosEnvioEmailCadastro($julgamento, $atividade);

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(),
                $this->getTipoEmailCadastroJulgamento($julgamento->getStatusJulgamentoFinal()->getId())
            );

            if (!empty($emailAtividadeSecundaria)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios($destinatarios);

                Email::enviarMail(new JulgamentoSegundaInstanciaSubstituicaoDecisaoMail($emailTO, $julgamentoFinalTO));
            }
        }
    }

    /**
     * Retorna o Julgamento Segunda Instancia Substituição pelo ID do Julgamento Final.
     *
     * @param $idJulgamentoFinal
     * @return JulgamentoSegundaInstanciaSubstituicao | mixed
     * @throws NonUniqueResultException
     */
    public function getPorJulgamentoFinal($idJulgamentoFinal)
    {
        return $this->getJulgamentoSegundaInstanciaSubstituicaoRepository()->getPorJulgamentoFinal($idJulgamentoFinal);
    }

    /**
     * Retorna o id do Julgamento de Segunda Instancia pela Substituição informada.
     *
     * @param $idSubstituicao
     * @return JulgamentoSegundaInstanciaSubstituicao | mixed
     */
    public function getIdJulgamentoPorSubstituicaoFinal($idSubstituicao)
    {
        return $this->getJulgamentoSegundaInstanciaSubstituicaoRepository()->getIdJulgamentoPorSubstituicaoFinal(
            $idSubstituicao
        );
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
        /** @var JulgamentoSegundaInstanciaSubstituicao $julgamentoFinal */
        $julgamentoSegundaInstanciaSubstituicao = $this->getJulgamentoSegundaInstanciaSubstituicaoRepository()->find($id);

        if (!empty($julgamentoSegundaInstanciaSubstituicao)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoSegundaInstanciaSubstituicao($julgamentoSegundaInstanciaSubstituicao->getId());

            return $this->getArquivoService()->getArquivo(
                $caminho, $julgamentoSegundaInstanciaSubstituicao->getNomeArquivoFisico(), $julgamentoSegundaInstanciaSubstituicao->getNomeArquivo()
            );
        }
    }

    /**
     * Método auxiliar para buscar os e-mails dos destinatários
     * @param JulgamentoSegundaInstanciaSubstituicao $julgamento
     * @param AtividadeSecundariaCalendario $atividade
     * @return array
     * @throws NegocioException
     */
    private function getDestinatariosEnvioEmailCadastro($julgamento, $atividade)
    {
        $idTipoCandidatura =   $julgamento->getSubstituicaoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao()->getTipoCandidatura()->getId();

        $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
        $idCauUf = $isIES ? null : $julgamento->getSubstituicaoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao()->getIdCauUf();

        $emailsComissao = [];
        /*$emailsComissao = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $atividade->getId(), $idCauUf
        );*/

        $emailsAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        $emailsResponsaveis = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa(
            $julgamento->getSubstituicaoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao()->getId()
        );

        $emailsIndicacoes = [];

        foreach ($julgamento->getIndicacoes() as $indicacao){
            if(!empty($indicacao->getMembroChapa())){
                $emailsIndicacoes[$indicacao->getMembroChapa()->getId()]
                    =  $indicacao->getMembroChapa()->getProfissional()->getPessoa()->getEmail();
            }
        }

        $destinatarios = array_merge($emailsComissao, $emailsAssessores, $emailsResponsaveis, $emailsIndicacoes);
        return $destinatarios;
    }

    /**
     * Método auxiliar para retornar o tipo de e-mail a ser enviado
     * @param $idStatusJulgamento
     * @return int
     */
    private function getTipoEmailCadastroJulgamento($idStatusJulgamento)
    {
        $idTipoEmail = Constants::EMAIL_JULGAMENTO_FINAL_SUBSTITUICAO_DEFERIDO;
        if ($idStatusJulgamento == Constants::STATUS_JULG_FINAL_INDEFERIDO) {
            $idTipoEmail = Constants::EMAIL_JULGAMENTO_FINAL_SUBSTITUICAO_INDEFERIDO;
        }
        return $idTipoEmail;
    }

    /**
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param JulgamentoSegundaInstanciaSubstituicaoTO $julgamentoFinalTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     */
    private function validarCamposObrigatorios($julgamentoFinalTO, $arquivo)
    {
        if (empty($julgamentoFinalTO->getIdSubstituicaoJulgamentoFinal())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($julgamentoFinalTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $statusValidos = [Constants::STATUS_JULG_FINAL_DEFERIDO, Constants::STATUS_JULG_FINAL_INDEFERIDO];
        if (
            empty($julgamentoFinalTO->getIdStatusJulgamentoFinal())
            || !in_array($julgamentoFinalTO->getIdStatusJulgamentoFinal(), $statusValidos)
        ) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (
            empty($julgamentoFinalTO->getIdJulgamentoSegundaInstanciaSubstituicaoPai())
            || (!empty($julgamentoFinalTO->getIdJulgamentoSegundaInstanciaSubstituicaoPai()) && !empty($arquivo))
        ) {
            if (empty($arquivo)) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
            }

            if (empty($arquivo->getArquivo())) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
            }

            $this->getArquivoService()->validarArquivoGenrico(
                $arquivo, Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_10MB
            );
        }

        $emptyJulstificativa = empty($julgamentoFinalTO->getRetificacaoJustificativa());
        if (!empty($julgamentoFinalTO->getIdJulgamentoSegundaInstanciaSubstituicaoPai()) && $emptyJulstificativa) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }
    }

    /**
     * Método faz validação complementar do cadastrado do julgamento do recurso
     *
     * @param JulgamentoSegundaInstanciaSubstituicaoTO $julgSegundaInstanciaSubstTO
     * @param ChapaEleicao $chapaEleicao
     * @param $eleicao
     * @throws NegocioException
     */
    private function validacaoComplementarSalvarJulgamentoFinal($julgSegundaInstanciaSubstTO, $chapaEleicao, $eleicao)
    {
        if (empty($chapaEleicao) || empty($eleicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $this->verificaPermissaoRealizarJulgamento($chapaEleicao, $julgSegundaInstanciaSubstTO);

        $idJulgamentoPai = $julgSegundaInstanciaSubstTO->getIdJulgamentoSegundaInstanciaSubstituicaoPai();
        if (empty($idJulgamentoPai)) {
            $idJulgamento = $this->getJulgamentoSegundaInstanciaSubstituicaoRepository()
                ->getIdJulgamentoPorSubstituicaoFinal(
                $julgSegundaInstanciaSubstTO->getIdSubstituicaoJulgamentoFinal()
            );

            if (!empty($idJulgamento)) {
                throw new NegocioException(Lang::get('messages.julgamento_segunda_instancia_substituicao.ja_realizado'));
            }
        } else {
        $julgamentoPai = $this->getJulgamentoSegundaInstanciaSubstituicaoRepository()->findBy(
            ['julgamentoSegundaInstanciaSubstituicaoPai' => $idJulgamentoPai]
        );
        if (!empty($julgamentoPai)) {
            throw new NegocioException(Lang::get('messages.julgamento_final_segunda_instancia.ja_realizado_alteracao'));
        }
    }

        $this->validaIndicacoes($julgSegundaInstanciaSubstTO, $chapaEleicao);
    }

    /**
     * Método auxiliar para verificar a permissão do usuário autenticado de realizar julgamento
     * @param ChapaEleicao $chapaEleicao
     * @param JulgamentoSegundaInstanciaSubstituicaoTO $julgSegundaInstanciaSubstTO
     * @throws NegocioException
     */
    private function verificaPermissaoRealizarJulgamento(ChapaEleicao $chapaEleicao, $julgSegundaInstanciaSubstTO): void
    {
        $isAlteracao = !empty($julgSegundaInstanciaSubstTO->getIdJulgamentoSegundaInstanciaSubstituicaoPai());

        $isAssessorCEN = $this->getUsuarioFactory()->isCorporativoAssessorCEN();
        $isAssessorCEUF = $this->getUsuarioFactory()->isCorporativoAssessorCeUfPorCauUf($chapaEleicao->getIdCauUf());
        $isIES = $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;

        if (($isAlteracao && !$isAssessorCEN) || (!$isAssessorCEN && !$isAssessorCEUF) || ($isIES && !$isAssessorCEN)) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }
    }

    /**
     * Método responsável de validar as indicações do julgamento
     *
     * @param $julgamentoFinalTO
     * @param $chapaEleicao
     * @throws NegocioException
     */
    private function validaIndicacoes($julgamentoFinalTO, $chapaEleicao)
    {
        if (
            $julgamentoFinalTO->getIdStatusJulgamentoFinal() == Constants::STATUS_JULG_FINAL_INDEFERIDO
            && !empty($julgamentoFinalTO->getIndicacoes())
        ) {
            /** @var IndicacaoJulgamentoFinalTO $indicacao */
            foreach ($julgamentoFinalTO->getIndicacoes() as $indicacao) {

                if (is_null($indicacao->getNumeroOrdem())) {
                    throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
                }

                if (empty($indicacao->getIdTipoParicipacaoChapa())) {
                    throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
                }

                $membroSelecionado = null;
                foreach ($chapaEleicao->getMembrosChapa() as $membroChapa) {
                    if (
                        $membroChapa->getTipoParticipacaoChapa()->getId() == $indicacao->getIdTipoParicipacaoChapa()
                        && $membroChapa->getNumeroOrdem() == $indicacao->getNumeroOrdem()
                    ) {
                        $membroSelecionado = $membroChapa;
                        break;
                    }
                }

                if (
                    (!empty($membroSelecionado) && empty($indicacao->getIdMembroChapa()))
                    || (empty($membroSelecionado) && !empty($indicacao->getIdMembroChapa()))
                    || (!empty($membroSelecionado) && $indicacao->getIdMembroChapa() != $membroSelecionado->getId())
                ) {
                    throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
                }
            }
        }
    }

    /**
     * Método auxiliar para salvar as indicações do julgamento
     *
     * @param IndicacaoJulgamentoSegundaInstanciaSubstituicaoTO[] $indicacoesTO
     * @param JulgamentoSegundaInstanciaSubstituicao $julgamentoFinal
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function salvarIndicacoes($indicacoesTO, $julgamentoFinal)
    {
        if (
            $julgamentoFinal->getStatusJulgamentoFinal()->getId() == Constants::STATUS_JULG_FINAL_INDEFERIDO
            && !empty($indicacoesTO)
        ) {
            $this->getIndicacaoJulgamentoSegundaInstanciaSubstituicaoBO()->salvarIndicacoes(
                $indicacoesTO,
                $julgamentoFinal
            );
        }
    }

    /**
     * Método auxiliar para preparar entidade JulgamentoSubstituicaoSegundaIntancia para cadastro
     *
     * @param JulgamentoSegundaInstanciaSubstituicaoTO $julgamentoFinalTO
     * @param $substituicaoJulgamentoFinal
     * @param ArquivoGenericoTO|null $arquivo
     * @param JulgamentoSegundaInstanciaSubstituicao|null $julgamentoSegundaInstanciaSubstAnterior
     * @return JulgamentoSegundaInstanciaSubstituicao
     * @throws Exception
     */
    private function prepararJulgamentoSalvar(
        $julgamentoFinalTO,
        $substituicaoJulgamentoFinal,
        $arquivo = null,
        $julgamentoSegundaInstanciaSubstAnterior = null
    ) {
        if (empty($arquivo) || empty($arquivo->getArquivo())) {
            $nomeArquivo = $julgamentoSegundaInstanciaSubstAnterior->getNomeArquivo();
            $nomeArquivoFisico = $julgamentoSegundaInstanciaSubstAnterior->getNomeArquivoFisico();
        } else {
            $nomeArquivo = $arquivo->getNome();
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getNome(),
                Constants::PREFIXO_ARQ_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA
            );
        }

        $julgamentoFinal = JulgamentoSegundaInstanciaSubstituicao::newInstance([
            'dataCadastro' => Utils::getData(),
            'nomeArquivo' => $nomeArquivo,
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'statusJulgamentoFinal' => ['id' => $julgamentoFinalTO->getIdStatusJulgamentoFinal()],
            'descricao' => $julgamentoFinalTO->getDescricao(),
            'usuario' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->id],
            'substituicaoJulgamentoFinal' => ['id' => $substituicaoJulgamentoFinal->getId()]
        ]);

        if (!empty($julgamentoFinalTO->getIdJulgamentoSegundaInstanciaSubstituicaoPai())) {
            $idJulgamentoPai = $julgamentoFinalTO->getIdJulgamentoSegundaInstanciaSubstituicaoPai();
            $julgamentoFinal->setRetificacaoJustificativa($julgamentoFinalTO->getRetificacaoJustificativa());
            $julgamentoFinal->setJulgamentoSegundaInstanciaSubstituicaoPai(
                JulgamentoSegundaInstanciaSubstituicao::newInstance(['id' => $idJulgamentoPai])
            );
        }

        return $julgamentoFinal;
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param JulgamentoSegundaInstanciaSubstituicao $julgamentoSegundaInstanciaSubst
     * @param ArquivoGenericoTO $arquivo
     * @param JulgamentoSegundaInstanciaSubstituicao $julgamentoSegundaInstanciaSubstAnterior
     */
    private function salvarArquivo($julgamentoSegundaInstanciaSubst, $arquivo, $julgamentoSegundaInstanciaSubstAnterior)
    {
        $caminhoDestino = $this->getArquivoService()->getCaminhoRepositorioJulgamentoSegundaInstanciaSubstituicao(
            $julgamentoSegundaInstanciaSubst->getId()
        );

        if (!empty($arquivo)) {
            $this->getArquivoService()->salvar(
                $caminhoDestino, $julgamentoSegundaInstanciaSubst->getNomeArquivoFisico(), $arquivo->getArquivo()
            );
        } else {
            $caminhoOrigem = $this->getArquivoService()->getCaminhoRepositorioJulgamentoSegundaInstanciaSubstituicao(
                $julgamentoSegundaInstanciaSubstAnterior->getId()
            );

            $hasArquivo = $this->getArquivoService()->hasArquivo(
                $caminhoOrigem,
                $julgamentoSegundaInstanciaSubstAnterior->getNomeArquivoFisico()
            );

            if ($hasArquivo) {
                $this->getArquivoService()->copiar(
                    $caminhoOrigem,
                    $caminhoDestino,
                    $julgamentoSegundaInstanciaSubstAnterior->getNomeArquivoFisico(),
                    $julgamentoSegundaInstanciaSubst->getNomeArquivoFisico()
                );
            }
        }

    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param JulgamentoSegundaInstanciaSubstituicao $julgamentoFinal
     * @throws Exception
     */
    private function salvarHistoricoJulgamento(JulgamentoSegundaInstanciaSubstituicao $julgamentoFinal): void
    {
        $isInclusao = empty($julgamentoFinal->getJulgamentoSegundaInstanciaSubstituicaoPai());

        $descricao = $isInclusao
            ? Constants::HISTORICO_INCLUSAO_JULG_SEGUNDA_INSTANCIA
            : Constants::HISTORICO_ALTERACAO_JULG_SEGUNDA_INSTANCIA;

        $historico = $this->getHistoricoBO()->criarHistorico(
            $julgamentoFinal,
            Constants::HISTORICO_ID_TIPO_JUGAMENTO_SEGUNDA_INSTANCIA,
            $isInclusao ? Constants::HISTORICO_ACAO_INSERIR : Constants::HISTORICO_ACAO_ALTERAR,
            $descricao,
            $isInclusao ? null : $julgamentoFinal->getRetificacaoJustificativa()
        );
        $this->getHistoricoBO()->salvar($historico);
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
     * Retorna uma nova instância de 'JulgamentoSegundaInstanciaSubstituicao'.
     *
     * @return JulgamentoSegundaInstanciaSubstituicaoRepository|\Doctrine\ORM\EntityRepository
     */
    private function getJulgamentoSegundaInstanciaSubstituicaoRepository()
    {
        if (empty($this->julgamentoSegundaInstanciaSubstituicaoRepository)) {
            $this->julgamentoSegundaInstanciaSubstituicaoRepository = $this->getRepository(JulgamentoSegundaInstanciaSubstituicao::class);
        }

        return $this->julgamentoSegundaInstanciaSubstituicaoRepository;
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
     * Retorna uma nova instância de 'IndicacaoJulgamentoSegundaInstanciaSubstituicaoBO'.
     *
     * @return IndicacaoJulgamentoSegundaInstanciaSubstituicaoBO|mixed
     */
    private function getIndicacaoJulgamentoSegundaInstanciaSubstituicaoBO()
    {
        if (empty($this->indicacaoJulgamentoSegundaInstanciaSubstituicaoBO)) {
            $this->indicacaoJulgamentoSegundaInstanciaSubstituicaoBO = app()->make(IndicacaoJulgamentoSegundaInstanciaSubstituicaoBO::class);
        }

        return $this->indicacaoJulgamentoSegundaInstanciaSubstituicaoBO;
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
     * Retorna uma nova instância de 'ProporcaoConselheiroExtratoBO'.
     *
     * @return ProporcaoConselheiroExtratoBO
     */
    private function getProporcaoConselheiroExtratoBO()
    {
        if (empty($this->proporcaoConselheiroExtratoBO)) {
            $this->proporcaoConselheiroExtratoBO = app()->make(ProporcaoConselheiroExtratoBO::class);
        }

        return $this->proporcaoConselheiroExtratoBO;
    }

    /**
     * Retorna uma nova instância de 'SubstituicaoJulgamentoFinalBO'.
     *
     * @return SubstituicaoJulgamentoFinalBO
     */
    private function getSubstituicaoJulgamentoFinalBO()
    {
        if (empty($this->substituicaoJulgamentoFinalBO)) {
            $this->substituicaoJulgamentoFinalBO = app()->make(SubstituicaoJulgamentoFinalBO::class);
        }

        return $this->substituicaoJulgamentoFinalBO;
    }

    /**
     * Método auxiliar para buscar o julgamento pai para salvar quando for alteração
     * @param JulgamentoSegundaInstanciaSubstituicaoTO $julgamentoSegundaInstanciaSubstituicaoTO
     * @return JulgamentoSegundaInstanciaSubstituicao|null
     * @throws NegocioException
     */
    private function getJulgamentoPaiParaSalvar($julgamentoSegundaInstanciaSubstituicaoTO)
    {
        /** @var JulgamentoSegundaInstanciaSubstituicao $julgamentoSegundaInstanciaSubstAnterior */
        $julgamentoSegundaInstanciaSubstAnterior = null;
        if (!empty($julgamentoSegundaInstanciaSubstituicaoTO->getIdJulgamentoSegundaInstanciaSubstituicaoPai())) {
            $julgamentoSegundaInstanciaSubstAnterior = $this->getJulgamentoSegundaInstanciaSubstituicaoRepository()->find(
                $julgamentoSegundaInstanciaSubstituicaoTO->getIdJulgamentoSegundaInstanciaSubstituicaoPai()
            );

            if (empty($julgamentoSegundaInstanciaSubstAnterior)) {
                throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
            }
        }
        return $julgamentoSegundaInstanciaSubstAnterior;
    }
}






