<?php
/*
 * ChapaEleicaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\JulgamentoImpugnacao;
use App\Entities\PedidoImpugnacao;
use App\Entities\RecursoImpugnacao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\PDFFActory;
use App\Jobs\EnviarEmailJulgamentoImpugnacaoJob;
use App\Mail\JulgamentoImpugnacaoCadastradoMail;
use App\Repository\JulgamentoImpugnacaoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\AtividadeSecundariaCalendarioTO;
use App\To\EleicaoTO;
use App\To\JulgamentoImpugnacaoTO;
use App\To\PedidoImpugnacaoTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Mpdf\MpdfException;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'JulgamentoImpugnacao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoImpugnacaoBO extends AbstractBO
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
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var PedidoImpugnacaoBO
     */
    private $pedidoImpugnacaoBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var JulgamentoImpugnacaoRepository
     */
    private $julgamentoImpugnacaoRepository;

    /**
     * @var PDFFActory
     */
    private $pdfFactory;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var UfCalendarioBO
     */
    private $ufCalendarioBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Retorna um Julgamento de Substituição conforme o id informado.
     *
     * @param $id
     *
     * @param bool $addPedidoImpugnacao
     * @return JulgamentoImpugnacaoTO
     * @throws Exception
     */
    public function getPorId($id, $addPedidoImpugnacao = false)
    {
        /** @var JulgamentoImpugnacaoTO $julgamentoImpugnacaoTO */
        $julgamentoImpugnacaoTO = $this->getJulgamentoImpugnacaoRepository()->getPorId($id, $addPedidoImpugnacao);

        return $julgamentoImpugnacaoTO;
    }

    /**
     * Retorna um Julgamento de impugnação conforme o id do pedido de impugnação informado.
     *
     * @param $idPedidoImpugnacao
     *
     * @param bool $addPedidoImpugnacao
     * @param bool $verificarUsuarioResponsavel
     * @param bool $verificarUsuarioMembroComissao
     * @return JulgamentoImpugnacaoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function getPorPedidoImpugnacao(
        $idPedidoImpugnacao,
        $addPedidoImpugnacao = false,
        $verificarUsuarioResponsavel = false,
        $verificarUsuarioMembroComissao = false
    ) {
        $julgamentoImpugnacaoTO = null;

        $isProfissional = $this->getUsuarioFactory()->isProfissional();
        if ($isProfissional && ($verificarUsuarioResponsavel || $verificarUsuarioMembroComissao)) {
            $julgamentoImpugnacaoTO = $this->getPorPedidoImpugnacaoComVerificacaoUsuario(
                $idPedidoImpugnacao,
                $verificarUsuarioResponsavel,
                $verificarUsuarioMembroComissao
            );
        } else {
            $julgamentoImpugnacaoTO = $this->getJulgamentoImpugnacaoRepository()->getPorPedidoImpugnacao(
                $idPedidoImpugnacao, $addPedidoImpugnacao
            );
        }

        if (!empty($julgamentoImpugnacaoTO)) {
            if (!$addPedidoImpugnacao) {
                $julgamentoImpugnacaoTO->setPedidoImpugnacao(null);
            }
        }

        return $julgamentoImpugnacaoTO;
    }

    /**
     * Verifica se usuário pode visualizar julgamento de acordo com as seguinte regras:
     * - Deve ser um membro da comissão CEN ou Ce da Cau UF da chapa vinculada ao julgamento
     *   se parâmetro para verificar estiver habilitado
     * - Deve ser um responsável da chapa ou impugnante vinculada ao julgamento
     *   se parâmetro para verificar estiver habilitado e a atividade de recurso dete estar iniciada
     *
     * @param $idPedidoImpugnacao
     * @param bool $verificarUsuarioResponsavel
     * @param bool $verificarUsuarioMembroComissao
     * @return JulgamentoImpugnacaoTO|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function getPorPedidoImpugnacaoComVerificacaoUsuario(
        $idPedidoImpugnacao,
        $verificarUsuarioResponsavel,
        $verificarUsuarioMembroComissao
    ) {
        $isPermitidoVisualizar = false;
        $isFinalizadoAtividadeRecurso = false;

        /** @var JulgamentoImpugnacao $julgamentoImpugnacao */
        $julgamentoImpugnacao = $this->getJulgamentoImpugnacaoRepository()->findOneBy([
            'pedidoImpugnacao' => $idPedidoImpugnacao
        ]);

        if (!empty($julgamentoImpugnacao)) {
            $idCalendario = $this->getIdCalendarioJulgamentoImpugnacao($julgamentoImpugnacao->getId());
            $atividadeSecundariaRecurso = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                $idCalendario, 3, 4
            );
            $dataFim = Utils::getDataHoraZero($atividadeSecundariaRecurso->getDataFim());
            $isFinalizadoAtividadeRecurso = Utils::getDataHoraZero() > $dataFim;

            $chapaEleicao = $julgamentoImpugnacao->getPedidoImpugnacao()->getMembroChapa()->getChapaEleicao();

            if ($verificarUsuarioResponsavel) {
                $idProfissionalLogado = $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional;

                if ($julgamentoImpugnacao->getPedidoImpugnacao()->getProfissional()->getId() == $idProfissionalLogado) {
                    $isPermitidoVisualizar = true;
                } else {
                    $idChapaEleicao = $this->getChapaEleicaoBO()->getIdChapaEleicaoPorCalendarioEResponsavel(
                        $idCalendario, $idProfissionalLogado
                    );
                    if (!empty($idChapaEleicao) && $idChapaEleicao == $chapaEleicao->getId()) {
                        $isPermitidoVisualizar = true;
                    }
                }

                if (
                    $isPermitidoVisualizar
                    && !(
                        !empty($atividadeSecundariaRecurso)
                        && Utils::getDataHoraZero() >= Utils::getDataHoraZero($atividadeSecundariaRecurso->getDataInicio())
                    )
                ) {
                    $isPermitidoVisualizar = false;
                }
            }

            if ($verificarUsuarioMembroComissao) {
                $isIES = $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;

                $isMembroComissao = $this->getMembroComissaoBO()->verificarMembroComissaoPorCauUf(
                    $idCalendario, $chapaEleicao->getIdCauUf(), $isIES
                );
                if ($isMembroComissao) {
                    $isPermitidoVisualizar = true;
                }
            }
        }

        $julgamentoImpugnacaoTO = null;
        if ($isPermitidoVisualizar) {
            $julgamentoImpugnacaoTO = JulgamentoImpugnacaoTO::newInstanceFromEntity($julgamentoImpugnacao);

            $julgamentoImpugnacaoTO->setIsConcluidoSubstituicao(false);
            $julgamentoImpugnacaoTO->setIsFinalizadoAtividadeRecurso($isFinalizadoAtividadeRecurso);
            $julgamentoImpugnacaoTO->setIsConcluidoRecursoImpugnante(false);
            $julgamentoImpugnacaoTO->setIsConcluidoRecursoResponsavel(false);

            if ($verificarUsuarioResponsavel) {
                $recursosImpugnacao = $julgamentoImpugnacao->getRecursosImpugnacao();

                $substituicao = $julgamentoImpugnacao->getPedidoImpugnacao()->getSubstituicaoImpugnacao();
                if (!empty($substituicao)) {
                    $julgamentoImpugnacaoTO->setIsConcluidoSubstituicao(true);
                }

                if (!empty($recursosImpugnacao)) {

                    /** @var RecursoImpugnacao $recursoImpugnacao */
                    foreach ($recursosImpugnacao as $recursoImpugnacao) {
                        $idTipoSolitacao = $recursoImpugnacao->getTipoSolicitacaoRecursoImpugnacao()->getId();
                        if ($idTipoSolitacao == Constants::TP_SOLICITACAO_RECURSO_RESPONSAVEL_CHAPA) {
                            $julgamentoImpugnacaoTO->setIsConcluidoRecursoResponsavel(true);
                        } else {
                            $julgamentoImpugnacaoTO->setIsConcluidoRecursoImpugnante(true);
                        }
                    }
                }
            }
        }
        return $julgamentoImpugnacaoTO;
    }

    /**
     * Retorna o id do calendário de acordo com o id do julgamento
     *
     * @param $idJulgamentoImpugnacao
     * @return integer
     */
    public function getIdCalendarioJulgamentoImpugnacao($idJulgamentoImpugnacao)
    {
        return $this->getJulgamentoImpugnacaoRepository()->getIdCalendarioJulgamento(
            $idJulgamentoImpugnacao
        );
    }

    /**
     * Retorna a atividade de secundária do julgamento de substituição
     *
     * @param $idPedidoImpugnacao
     * @return AtividadeSecundariaCalendarioTO
     * @throws NonUniqueResultException
     */
    public function getAtividadeSecundariaCadastroJulgamento($idPedidoImpugnacao)
    {
        $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoImpugnacao($idPedidoImpugnacao);

        $atividadeSecundariaTO = null;
        if (!empty($eleicaoTO)) {
            $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                $eleicaoTO->getCalendario()->getId(), 3, 3
            );

            if (!empty($atividadeSecundaria)) {
                $atividadeSecundariaTO = AtividadeSecundariaCalendarioTO::newInstanceFromEntity($atividadeSecundaria);
            }
        }

        return $atividadeSecundariaTO;
    }

    /**
     * Salva o julgamento do pedido de impugnação
     *
     * @param JulgamentoImpugnacaoTO $julgamentoImpugnacaoTO
     * @return JulgamentoImpugnacaoTO
     * @throws Exception
     */
    public function salvar(JulgamentoImpugnacaoTO $julgamentoImpugnacaoTO)
    {
        $pedidoImpugnacao = $this->getPedidoImpugnacaoBO()->findById(
            $julgamentoImpugnacaoTO->getIdPedidoImpugnacao()
        );
        $arquivos = $julgamentoImpugnacaoTO->getArquivos();

        $this->validarSalvarJulgamento($julgamentoImpugnacaoTO, $pedidoImpugnacao);
        $this->validarArquivoJulgamento($arquivos);

        $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoImpugnacao(
            $julgamentoImpugnacaoTO->getIdPedidoImpugnacao()
        );

        $this->validacaoComplementarSalvarJulgamento($eleicaoTO);

        try {
            $this->beginTransaction();

            /** @var ArquivoGenericoTO $arquivo */
            $arquivo = array_shift($arquivos);

            $julgamentoImpugnacao = $this->prepararSalvar(
                $julgamentoImpugnacaoTO, $pedidoImpugnacao, $arquivo
            );

            $this->getJulgamentoImpugnacaoRepository()->persist(
                $julgamentoImpugnacao
            );

            $this->salvarHistoricoJulgamentoImpugnacao(
                $julgamentoImpugnacao,
                false
            );

            $this->salvarArquivo(
                $julgamentoImpugnacao->getId(),
                $arquivo->getArquivo(),
                $julgamentoImpugnacao->getNomeArquivoFisico()
            );

            $this->getPedidoImpugnacaoBO()->atualizarPedidoImpugnacaoPosJulgamento(
                $pedidoImpugnacao,
                $julgamentoImpugnacaoTO->getIdStatusJulgamentoImpugnacao()
            );

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailJulgamentoImpugnacaoJob($julgamentoImpugnacao->getId()));

        return JulgamentoImpugnacaoTO::newInstanceFromEntity($julgamentoImpugnacao);
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoJulgamentoImpugnacao($id)
    {
        /** @var JulgamentoImpugnacao $julgamentoImpugnacao */
        $julgamentoImpugnacao = $this->getJulgamentoImpugnacaoRepository()->find($id);

        if (!empty($julgamentoImpugnacao)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoImpugnacao(
                $julgamentoImpugnacao->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $julgamentoImpugnacao->getNomeArquivoFisico(),
                $julgamentoImpugnacao->getNomeArquivo()
            );
        }
    }

    /**
     * Realiza o envio de e-mails após o julgamento
     *
     * @param $idJulgamentoImpugnacao
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function enviarEmailCadastroJulgamento($idJulgamentoImpugnacao)
    {
        /** @var JulgamentoImpugnacao $julgamentoImpugnacao */
        $julgamentoImpugnacao = $this->getJulgamentoImpugnacaoRepository()->find($idJulgamentoImpugnacao);

        $idCalendario = $this->getJulgamentoImpugnacaoRepository()->getIdCalendarioJulgamento($idJulgamentoImpugnacao);

        $atividadeJulgamento = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $idCalendario, 3, 3
        );

        $atividadeRecurso = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $idCalendario, 3, 4
        );

        $paramsEmailJulgamento = $this->prepararParametrosEmailJulgamentoSubs($julgamentoImpugnacao);

        $idTipoEmail = $this->getTipoEmailCadastroJulgamento(
            $julgamentoImpugnacao->getStatusJulgamentoImpugnacao()->getId()
        );

        $chapaEleicao = $julgamentoImpugnacao->getPedidoImpugnacao()->getMembroChapa()->getChapaEleicao();
        $idTipoCandidaturaChapa = $chapaEleicao->getTipoCandidatura()->getId();

        $destinariosComissao = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $atividadeJulgamento->getId(),
            ($idTipoCandidaturaChapa == Constants::TIPO_CANDIDATURA_IES) ? null : $chapaEleicao->getIdCauUf()
        );

        $destinariosAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            ($idTipoCandidaturaChapa == Constants::TIPO_CANDIDATURA_IES) ? null : [$chapaEleicao->getIdCauUf()]
        );

        $destinariosResponsaveis = [];
        if (!empty($atividadeRecurso) && Utils::getDataHoraZero($atividadeRecurso->getDataInicio()) <= Utils::getDataHoraZero()) {
            $destinariosResponsaveis = $this->getEmailsResponsaveisChapaEImpugnante(
                $julgamentoImpugnacao->getPedidoImpugnacao()
            );
        }

        $destinatarios = array_merge($destinariosComissao, $destinariosAssessores, $destinariosResponsaveis);

        $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
            $atividadeJulgamento->getId(),
            $destinatarios,
            $idTipoEmail,
            Constants::TEMPLATE_EMAIL_JULGAMENTO_IMPUGNACAO,
            $paramsEmailJulgamento
        );
    }

    /**
     * Envia e-mail para o julgamento slertando cadastro fim atividade julgamento
     *
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function enviarEmailCadastroJulgamentoFimPeriodo()
    {
        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            Utils::getDataHoraZero(), null, 3, 4
        );

        foreach ($atividades as $atividadeSecundaria) {

            $julgamentosImpugnacaoTO = $this->getJulgamentoImpugnacaoRepository()->getPorCalendario(
                $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getId(), true
            );

            foreach ($julgamentosImpugnacaoTO as $julgamentoImpugnacaoTO) {
                $destinatarios = $this->getPedidoImpugnacaoBO()->getEmailsResponsaveis(
                    $julgamentoImpugnacaoTO->getPedidoImpugnacao()
                );

                if (!empty($destinatarios)) {
                    $idStatusJulgamento = $julgamentoImpugnacaoTO->getStatusJulgamentoImpugnacao()->getId();
                    $emailAtivSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                        $atividadeSecundaria->getId(),
                        $this->getTipoEmailCadastroJulgamento($idStatusJulgamento)
                    );

                    if (!empty($emailAtivSecundaria)) {
                        $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao(
                            $emailAtivSecundaria
                        );
                        $emailTO->setDestinatarios($destinatarios);

                        Email::enviarMail(new JulgamentoImpugnacaoCadastradoMail(
                            $emailTO, $julgamentoImpugnacaoTO
                        ));
                    }
                }
            }
        }
    }

    /**
     * Método realiza o envio de e-mail no ínicio da atividade 3.3 de de cadastro de julgamento
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailIncioPeriodoJulgamento()
    {
        $idTipo = Constants::EMAIL_JULGAMENTO_IMPUGNACAO_ASSESSORES_PERIODO_SERA_ABERTO;
        $this->getEmailAtividadeSecundariaBO()->enviarEmailIncioPeriodoJulgamentoChapa(3, 3, $idTipo);
    }

    /**
     * Método realiza o envio de e-mail no fim da atividade 3.3
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailFimPeriodoJulgamento()
    {
        $idTipo = Constants::EMAIL_JULGAMENTO_IMPUGNACAO_ASSESSORES_PERIODO_SERA_FECHADO;
        $idStatusPedido = Constants::STATUS_IMPUGNACAO_EM_ANALISE;
        $this->getPedidoImpugnacaoBO()->enviarEmailFimPeriodoJulgamento(3, 3, $idStatusPedido, $idTipo);
    }

    /**
     * Verifica se o pedido de substituição pode ser julgado
     *
     * @param $idCauUf
     * @param $idTipoCandidatura
     * @param $idAtividadeSecundaria
     * @return bool
     * @throws Exception
     */
    public function verificarPedidoSubstituicaoPodeSerJulgado(
        $idCauUf,
        $idTipoCandidatura,
        $idAtividadeSecundaria,
        $idStatusPedidoImpugnacao
    ) {
        $podeSerJulgado = false;

        $isPermissaoJulgar = $this->isUsuarioComPermissaoJulgar(
            $idCauUf, $idTipoCandidatura
        );

        $isStatusEmAnalise = $idStatusPedidoImpugnacao == Constants::STATUS_IMPUGNACAO_EM_ANALISE;

        if ($isPermissaoJulgar && $isStatusEmAnalise) {
            $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorAtividadeSecundaria(
                $idAtividadeSecundaria, 3, 2
            );

            if (!empty($atividade) && Utils::getDataHoraZero() >= Utils::getDataHoraZero($atividade->getDataFim())) {
                $podeSerJulgado = true;
            }
        }

        return $podeSerJulgado;
    }

    /**
     *  Gerar PDF do pedido de substituição de membro da chapa.
     *
     * @param $id
     * @return ArquivoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws Exception
     */
    public function gerarDocumentoPDFJulgamentoImpugnacao($id)
    {
        /** @var JulgamentoImpugnacao $julgamentoImpugnacao */
        $julgamentoImpugnacao = $this->getJulgamentoImpugnacaoRepository()->find($id);

        $this->getChapaEleicaoBO()->definirFilialChapa(
            $julgamentoImpugnacao->getPedidoImpugnacao()->getMembroChapa()->getChapaEleicao()
        );
        $julgamentoImpugnacao->getPedidoImpugnacao()->getMembroChapa()->getChapaEleicao()->definirStatusChapaVigente();

        $registroImpugnante = $julgamentoImpugnacao->getPedidoImpugnacao()->getProfissional()->getRegistroNacional();
        $julgamentoImpugnacao->getPedidoImpugnacao()->getProfissional()->setRegistroNacional(
            Utils::getRegistroNacionalFormatado($registroImpugnante)
        );

        $registroImpugnado = $julgamentoImpugnacao->getPedidoImpugnacao()->getMembroChapa()->getProfissional()->getRegistroNacional();
        $julgamentoImpugnacao->getPedidoImpugnacao()->getMembroChapa()->getProfissional()->setRegistroNacional(
            Utils::getRegistroNacionalFormatado($registroImpugnado)
        );

        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        return $this->getPdfFactory()->gerarDocumentoPDFJulgamentoImpugnnacao(
            $julgamentoImpugnacao,
            $usuarioLogado
        );
    }

    /**
     * Método auxiliar que prepara os parâmetros para o envio de e-mails
     *
     * @param JulgamentoImpugnacao $julgamentoImpugnacao
     * @return array
     * @throws Exception
     */
    private function prepararParametrosEmailJulgamentoSubs(
        JulgamentoImpugnacao $julgamentoImpugnacao
    ) {
        $pedidoImpugnacaoTO = PedidoImpugnacaoTO::newInstance([
            'numeroProtocolo' => $julgamentoImpugnacao->getPedidoImpugnacao()->getNumeroProtocolo()
        ]);
        $desicao = $julgamentoImpugnacao->getStatusJulgamentoImpugnacao()->getDescricao();

        return array(
            Constants::PARAMETRO_EMAIL_JULGAMENTO_DESICAO => $desicao,
            Constants::PARAMETRO_EMAIL_NM_PROTOCOLO => $pedidoImpugnacaoTO->getNumeroProtocolo(),
            Constants::PARAMETRO_EMAIL_JULGAMENTO_PARECER => $julgamentoImpugnacao->getDescricao()
        );
    }

    /**
     * Retorna lista de e-mails dos responsáveis pela chapa e do impugnanate
     *
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @return array|null
     * @throws NegocioException
     */
    private function getEmailsResponsaveisChapaEImpugnante(
        $pedidoImpugnacao
    ) {
        $destinatarios = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa(
            $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getId()
        );

        $destinatarios[] = $pedidoImpugnacao->getProfissional()->getPessoa()->getEmail();

        return $destinatarios;
    }

    /**
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param JulgamentoImpugnacaoTO $julgamentoImpugnacaoTO
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @throws NegocioException
     */
    private function validarSalvarJulgamento($julgamentoImpugnacaoTO, $pedidoImpugnacao)
    {
        if (empty($pedidoImpugnacao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $isPermissaoJulgar = $this->isUsuarioComPermissaoJulgar(
            $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getIdCauUf(),
            $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getTipoCandidatura()->getId()
        );

        if (empty($julgamentoImpugnacaoTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $statusValidos = [Constants::STATUS_JULG_IMPUGNACAO_PROCEDENTE, Constants::STATUS_JULG_IMPUGNACAO_IMPROCEDENTE];
        if (
            empty($julgamentoImpugnacaoTO->getIdStatusJulgamentoImpugnacao())
            || !in_array($julgamentoImpugnacaoTO->getIdStatusJulgamentoImpugnacao(), $statusValidos)
        ) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $idStatusPedido = $pedidoImpugnacao->getStatusPedidoImpugnacao()->getId();
        $isStatusEmAnalise = $idStatusPedido == Constants::STATUS_IMPUGNACAO_EM_ANALISE;

        if (!$isStatusEmAnalise) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if (!$isPermissaoJulgar) {
            throw new NegocioException(Message::MSG_SEM_MERMISSAO_VISUALIZACAO_ATIV_SELECIONADA);
        }
    }

    /**
     * Método faz validação complementar do cadastrado do julgamento de impugnação
     *
     * @param EleicaoTO $eleicaoTO
     */
    private function validacaoComplementarSalvarJulgamento($eleicaoTO)
    {
        if (empty($eleicaoTO)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $dataInicioVigencia = Utils::getDataHoraZero($eleicaoTO->getCalendario()->getDataInicioVigencia());
        $dataFimVigencia = Utils::getDataHoraZero($eleicaoTO->getCalendario()->getDataFimVigencia());

        if (!(Utils::getDataHoraZero() >= $dataInicioVigencia && Utils::getDataHoraZero() <= $dataFimVigencia)) {
            throw new NegocioException(Message::MSG_PERIODO_VIGENTE_ELEICAO_FECHADO);
        }
    }

    /**
     * Método auxiliar que verifica se usuário tem permissão de julgar
     *
     * @param $idCauUf
     * @return bool
     */
    private function isUsuarioComPermissaoJulgar($idCauUf, $idTipoCandidatura)
    {
        $isAssessorCE = false;
        if ($idTipoCandidatura != Constants::TIPO_CANDIDATURA_IES) {
            $isAssessorCE = $this->getUsuarioFactory()->isCorporativoAssessorCeUfPorCauUf($idCauUf);
        }

        return ($this->getUsuarioFactory()->isCorporativoAssessorCEN() || $isAssessorCE);
    }

    /**
     * Método auxiliar para preparar entidade JulgamentoImpugnacao para cadastro
     *
     * @param JulgamentoImpugnacaoTO $julgamentoImpugnacaoTO
     * @param PedidoImpugnacao|null $pedidoImpugnacao
     * @param ArquivoGenericoTO $arquivo
     * @return JulgamentoImpugnacao
     * @throws Exception
     */
    private function prepararSalvar($julgamentoImpugnacaoTO, $pedidoImpugnacao, $arquivo)
    {
        $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
            $arquivo->getNome(), Constants::PREFIXO_ARQ_JULGAMENTO_IMPUGNACAO
        );

        $julgamentoImpugnacao = JulgamentoImpugnacao::newInstance([
            'dataCadastro' => Utils::getData(),
            'statusJulgamentoImpugnacao' => ['id' => $julgamentoImpugnacaoTO->getIdStatusJulgamentoImpugnacao()],
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'descricao' => $julgamentoImpugnacaoTO->getDescricao(),
            'nomeArquivo' => $arquivo->getNome(),
            'usuario' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->id]
        ]);
        $julgamentoImpugnacao->setPedidoImpugnacao($pedidoImpugnacao);

        return $julgamentoImpugnacao;
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $idJulgamentoImpugnacao
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idJulgamentoImpugnacao, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioJulgamentoImpugnacao($idJulgamentoImpugnacao),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param JulgamentoImpugnacao $julgamentoImpugnacao
     * @param bool $isAlteracao
     * @throws Exception
     */
    private function salvarHistoricoJulgamentoImpugnacao(
        JulgamentoImpugnacao $julgamentoImpugnacao,
        $isAlteracao = false
    ): void {
        $historico = $this->getHistoricoBO()->criarHistorico(
            $julgamentoImpugnacao,
            Constants::HISTORICO_ID_TIPO_JULGAMENTO_IMPUGNACAO,
            $isAlteracao ? Constants::HISTORICO_ACAO_ALTERAR : Constants::HISTORICO_ACAO_INSERIR,
            $isAlteracao ? Constants::HISTORICO_DESCRICAO_ACAO_ALTERAR : Constants::HISTORICO_DESCRICAO_ACAO_INSERIR
        );
        $this->getHistoricoBO()->salvar($historico);
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
     * Retorna uma nova instância de 'JulgamentoImpugnacaoRepository'.
     *
     * @return JulgamentoImpugnacaoRepository
     */
    private function getJulgamentoImpugnacaoRepository()
    {
        if (empty($this->julgamentoImpugnacaoRepository)) {
            $this->julgamentoImpugnacaoRepository = $this->getRepository(JulgamentoImpugnacao::class);
        }

        return $this->julgamentoImpugnacaoRepository;
    }

    /**
     * Retorna a instância de PDFFactory conforme o padrão Lazy Initialization.
     *
     * @return PDFFActory
     */
    private function getPdfFactory(): PDFFActory
    {
        if (empty($this->pdfFactory)) {
            $this->pdfFactory = app()->make(PDFFActory::class);
        }

        return $this->pdfFactory;
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
     * Retorna uma nova instância de 'PedidoImpugnacaoBO'.
     *
     * @return PedidoImpugnacaoBO
     */
    private function getPedidoImpugnacaoBO()
    {
        if (empty($this->pedidoImpugnacaoBO)) {
            $this->pedidoImpugnacaoBO = app()->make(PedidoImpugnacaoBO::class);
        }

        return $this->pedidoImpugnacaoBO;
    }

    /**
     * @param $arquivos
     * @throws NegocioException
     */
    private function validarArquivoJulgamento($arquivos): void
    {
        if (empty($arquivos) || !is_array($arquivos)) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        /** @var ArquivoGenericoTO $arquivo */
        $arquivo = array_shift($arquivos);

        $dadosTOValidarArquivo = new \stdClass();
        $dadosTOValidarArquivo->nome = $arquivo->getNome();
        $dadosTOValidarArquivo->tamanho = $arquivo->getTamanho();
        $dadosTOValidarArquivo->tipoValidacao = Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_10MB;
        $this->getArquivoService()->validarArquivo($dadosTOValidarArquivo);

        if (empty($arquivo->getArquivo())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }
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
     * @param int $idStatusJulgamento
     * @return int
     */
    public function getTipoEmailCadastroJulgamento(int $idStatusJulgamento): int
    {
        $idTipoEmail = $idStatusJulgamento == Constants::STATUS_JULG_IMPUGNACAO_PROCEDENTE
            ? Constants::EMAIL_JULGAMENTO_IMPUGNACAO_COMISSAO_ELEITORAL_PEDIDO_PROCEDENTE
            : Constants::EMAIL_JULGAMENTO_IMPUGNACAO_COMISSAO_ELEITORAL_PEDIDO_INPROCEDENTE;
        return $idTipoEmail;
    }

    /**
     * Retorna uma nova instância de 'FilialBO'.
     *
     * @return FilialBO|mixed
     */
    private function getFilialBO()
    {
        if (empty($this->filialBO)) {
            $this->filialBO = app()->make(FilialBO::class);
        }

        return $this->filialBO;
    }

    /**
     * Retorna a intancia de 'UfCalendarioBO'.
     *
     * @return UfCalendarioBO
     */
    private function getUfCalendarioBO()
    {
        if ($this->ufCalendarioBO == null) {
            $this->ufCalendarioBO = app()->make(UfCalendarioBO::class);
        }
        return $this->ufCalendarioBO;
    }

    /**
     * Retorna o julgamento de impugnacao chapa conforme o id informado.
     *
     * @param $id
     *
     * @return JulgamentoImpugnacao|null
     */
    public function findById($id)
    {
        /** @var JulgamentoImpugnacao $julgamentoSubstituicao */
        $julgamentoSubstituicao = $this->getJulgamentoImpugnacaoRepository()->find($id);

        return $julgamentoSubstituicao;
    }

}




