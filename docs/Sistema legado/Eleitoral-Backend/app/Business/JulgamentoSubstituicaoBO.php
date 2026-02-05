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
use App\Entities\JulgamentoSubstituicao;
use App\Entities\PedidoSubstituicaoChapa;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\PDFFActory;
use App\Jobs\EnviarEmailJulgamentoSubstituicaoJob;
use App\Repository\JulgamentoSubstituicaoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoTO;
use App\To\AtividadeSecundariaCalendarioTO;
use App\To\EleicaoTO;
use App\To\JulgamentoSubstituicaoTO;
use App\To\PedidoSubstituicaoChapaTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Mpdf\MpdfException;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'JulgamentoSubstituicao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoSubstituicaoBO extends AbstractBO
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
     * @var CalendarioBO
     */
    private $calendarioBO;

    /**
     * @var PedidoSubstituicaoChapaBO
     */
    private $pedidoSubstituicaoChapaBO;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var UfCalendarioBO
     */
    private $ufCalendarioBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var JulgamentoSubstituicaoRepository
     */
    private $julgamentoSubstituicaoRepository;

    /**
     * @var PDFFActory
     */
    private $pdfFactory;

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
     * @param bool $addPedidoSubstituicao
     * @return JulgamentoSubstituicaoTO
     * @throws Exception
     */
    public function getPorId($id, $addPedidoSubstituicao = false)
    {
        $julgamentoSubstituicaoTO = $this->getJulgamentoSubstituicaoRepository()->getPorId($id, $addPedidoSubstituicao);

        if (!empty($julgamentoSubstituicaoTO)) {

            if ($addPedidoSubstituicao) {
                $julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()->getChapaEleicao()->definirStatusChapaVigente();
                $julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()->getChapaEleicao()->setChapaEleicaoStatus(null);
            }
        }

        return $julgamentoSubstituicaoTO;
    }

    /**
     * Retorna o julgamento de substituição chapa conforme o id informado.
     *
     * @param $id
     *
     * @return JulgamentoSubstituicao|null
     */
    public function findById($id)
    {
        /** @var JulgamentoSubstituicao $julgamentoSubstituicao */
        $julgamentoSubstituicao = $this->getJulgamentoSubstituicaoRepository()->find($id);

        return $julgamentoSubstituicao;
    }

    /**
     * Retorna um Julgamento de Substituição conforme o id informado.
     *
     * @param $idPedidoSubstituicao
     *
     * @param bool $addPedidoSubstituicao
     * @param bool $verificarUsuarioResponsavelChapa
     * @param bool $verificarUsuarioMembroComissao
     * @return JulgamentoSubstituicaoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getPorPedidoSubstituicao(
        $idPedidoSubstituicao,
        $addPedidoSubstituicao = false,
        $verificarUsuarioResponsavelChapa = false,
        $verificarUsuarioMembroComissao = false
    ) {
        $julgamentoSubstituicaoTO = null;

        $isProfissional = $this->getUsuarioFactory()->isProfissional();
        if ($isProfissional && ($verificarUsuarioResponsavelChapa || $verificarUsuarioMembroComissao)) {
            $julgamentoSubstituicaoTO = $this->getPorPedidoSubstituicaoComVerificacaoUsuario(
                $idPedidoSubstituicao,
                $verificarUsuarioResponsavelChapa,
                $verificarUsuarioMembroComissao
            );
        } else {
            $julgamentoSubstituicaoTO = $this->getJulgamentoSubstituicaoRepository()->getPorPedidosSubstituicao(
                $idPedidoSubstituicao, $addPedidoSubstituicao
            );
        }

        if (!empty($julgamentoSubstituicaoTO)) {
            if ($addPedidoSubstituicao) {
                $julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()->getChapaEleicao()->definirStatusChapaVigente();
                $julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()->getChapaEleicao()->setChapaEleicaoStatus(null);
            } else {
                $julgamentoSubstituicaoTO->setPedidoSubstituicaoChapa(null);
            }
        }

        return $julgamentoSubstituicaoTO;
    }

    /**
     * Retorna a atividade de secundária do julgamento de substituição
     *
     * @return AtividadeSecundariaCalendarioTO
     * @throws Exception
     */
    public function getAtividadeSecundariaCadastroJulgamento($idPedidoSubstituicao)
    {
        $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoSubstituicao($idPedidoSubstituicao);

        $atividadeSecundariaTO = null;
        if (!empty($eleicaoTO)) {
            $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                $eleicaoTO->getCalendario()->getId(), 2, 4
            );

            if (!empty($atividadeSecundaria)) {
                $atividadeSecundariaTO = AtividadeSecundariaCalendarioTO::newInstanceFromEntity($atividadeSecundaria);
            }
        }

        return $atividadeSecundariaTO;
    }

    /**
     * Verifica se o pedido de substituição pode ser julgado
     *
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapa
     * @return bool
     * @throws Exception
     */
    public function verificarPedidoSubstituicaoPodeSerJulgado(PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapa)
    {
        $podeSerJulgado = false;

        $isPermissaoJulgar = $this->isUsuarioComPermissaoJulgar(
            $pedidoSubstituicaoChapa->getChapaEleicao()->getIdCauUf()
        );

        $idStatusPedido = $pedidoSubstituicaoChapa->getStatusSubstituicaoChapa()->getId();
        $isStatusEmAndanmento = $idStatusPedido == Constants::STATUS_SUBSTITUICAO_CHAPA_EM_ANDAMENTO;

        if ($isPermissaoJulgar && $isStatusEmAndanmento) {
            $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoSubstituicao($pedidoSubstituicaoChapa->getId());

            $dataInicioVigencia = Utils::getDataHoraZero($eleicaoTO->getCalendario()->getDataInicioVigencia());
            $dataFimVigencia = Utils::getDataHoraZero($eleicaoTO->getCalendario()->getDataFimVigencia());

            if (
                !empty($eleicaoTO)
                || (Utils::getDataHoraZero() >= $dataInicioVigencia && Utils::getDataHoraZero() <= $dataFimVigencia)
            ) {
                $podeSerJulgado = true;
            }
        }

        return $podeSerJulgado;
    }

    /**
     * Salva o pedido de subsdtituição chapa
     *
     * @param JulgamentoSubstituicaoTO $julgamentoSubstituicaoTO
     * @return JulgamentoSubstituicaoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function salvar(JulgamentoSubstituicaoTO $julgamentoSubstituicaoTO)
    {
        $pedidoSubstituicaoChapa = $this->getPedidoSubstituicaoChapaBO()->findById(
            $julgamentoSubstituicaoTO->getIdPedidoSubstituicaoChapa()
        );

        $this->validarSalvarJulgamento($julgamentoSubstituicaoTO, $pedidoSubstituicaoChapa);
        
        $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoSubstituicao($pedidoSubstituicaoChapa->getId());

        $this->validacaoComplementarSalvarJulgamento($julgamentoSubstituicaoTO, $eleicaoTO);
        
        try {
            $this->beginTransaction();

            $julgamentoSubstituicao = $this->prepararJulgamentoSalvar(
                $julgamentoSubstituicaoTO, $pedidoSubstituicaoChapa
            );

            /** @var JulgamentoSubstituicao $julgamentoSubstituicaoSalvo */
            $this->getJulgamentoSubstituicaoRepository()->persist(
                $julgamentoSubstituicao
            );

            $this->salvarHistoricoJulgamentoSubstituicao(
                $julgamentoSubstituicao,
                !empty($julgamentoSubstituicaoTO->getId())
            );

            if (empty($julgamentoSubstituicaoTO->getId())) {
                if ($julgamentoSubstituicaoTO->getArquivo()) {
                    $this->salvarArquivo(
                        $julgamentoSubstituicao->getId(),
                        $julgamentoSubstituicaoTO->getArquivo(),
                        $julgamentoSubstituicao->getNomeArquivoFisico()
                    );
                }
               
                $this->getPedidoSubstituicaoChapaBO()->atualizarPedidoSubstituicaoPosJulgamento(
                    $eleicaoTO->getCalendario()->getId(),
                    $pedidoSubstituicaoChapa,
                    $julgamentoSubstituicaoTO->getIdStatusJulgamentoSubstituicao()
                );
            }
            
            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new  EnviarEmailJulgamentoSubstituicaoJob($julgamentoSubstituicao->getId()));

        return $this->getPorId($julgamentoSubstituicao->getId(), true);
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoJulgamentoSubstituicao($id)
    {
        /** @var JulgamentoSubstituicao $julgamentoSubstituicao */
        $julgamentoSubstituicao = $this->getJulgamentoSubstituicaoRepository()->find($id);

        if (!empty($julgamentoSubstituicao)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoSubstituicao(
                $julgamentoSubstituicao->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $julgamentoSubstituicao->getNomeArquivoFisico(),
                $julgamentoSubstituicao->getNomeArquivo()
            );
        }
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
    public function gerarDocumentoPDFJulgamentoSubstituicao($id)
    {
        $julgamentoSubstituicaoTO = $this->getPorId($id, true);

        $filial = $this->getFilialBO()->getPorId(
            $julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()->getChapaEleicao()->getIdCauUf()
        );
        $julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()->getChapaEleicao()->setCauUf($filial);

        if (!empty($julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()->getIdProfissionalInclusao())) {
            $profissionalTO = $this->getProfissionalBO()->getPorId(
                $julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()->getIdProfissionalInclusao()
            );

            if (!empty($profissionalTO)) {
                $julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()->setNomeProfissionalInclusao(
                    $profissionalTO->getNome()
                );
            }
        }

        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        return $this->getPdfFactory()->gerarDocumentoPDFJulgamentoSubstituicao(
            $julgamentoSubstituicaoTO,
            $usuarioLogado
        );
    }

    /**
     * Método realiza o envio de e-mail no ínicio da atividade 2.4 de de cadastro de julgamento de substituição
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailIncioPeriodoJulgamento()
    {
        $idTipo = Constants::EMAIL_JULGAMENTO_SUBST_ASSESSORES_PERIODO_SERA_ABERTO;
        $this->getEmailAtividadeSecundariaBO()->enviarEmailIncioPeriodoJulgamentoChapa(2, 4, $idTipo);
    }

    /**
     * Método realiza o envio de e-mail no ínicio da atividade 3.2 de de cadastro de defesa de impúgnação
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailFimPeriodoJulgamento()
    {
        $idStatus = Constants::STATUS_SUBSTITUICAO_CHAPA_EM_ANDAMENTO;
        $idTipo = Constants::EMAIL_JULGAMENTO_SUBST_ASSESSORES_PERIODO_SERA_FECHADO;
        $this->getPedidoSubstituicaoChapaBO()->enviarEmailFimPeriodoJulgamento(2, 4, $idStatus, $idTipo);
    }

    /**
     * Realiza o envio de e-mails após o julgamento
     *
     * @param $idJulgamentoSubstituicao
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function enviarEmailCadastroJulgamento($idJulgamentoSubstituicao)
    {
        $julgamentoSubstituicaoTO = $this->getPorId($idJulgamentoSubstituicao, true);

        $idCalendario = $this->getJulgamentoSubstituicaoRepository()->getIdCalendarioJulgamento(
            $idJulgamentoSubstituicao
        );

        $atividadeJulgamento = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $idCalendario, 2, 4
        );

        $atividadeRecurso = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $idCalendario, 2, 5
        );

        $paramsEmail = $this->prepararParametrosEmailJulgamentoSubs($julgamentoSubstituicaoTO);

        $idTipoEmail = $julgamentoSubstituicaoTO->getStatusJulgamentoSubstituicao()->getId() == Constants::STATUS_JULGAMENTO_DEFERIDO
            ? Constants::EMAIL_JULGAMENTO_SUBST_COMISSAO_ELEITORAL_PEDIDO_DEFERIDO
            : Constants::EMAIL_JULGAMENTO_SUBST_COMISSAO_ELEITORAL_PEDIDO_INDEFERIDO;

        $chapaEleicao = $julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()->getChapaEleicao();
        $idTipoCandidatura = $chapaEleicao->getTipoCandidatura()->getId();

        $destinariosComissao = [];
        /*$destinariosComissao = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $atividadeJulgamento->getId(),
            ($idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES) ? null : $chapaEleicao->getIdCauUf()
        );*/

        $destinariosAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            ($idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES) ? null : [$chapaEleicao->getIdCauUf()]
        );

        $destinariosResponsaveisEMembros = [];
        if (!empty($atividadeRecurso) && Utils::getDataHoraZero($atividadeRecurso->getDataInicio()) <= Utils::getDataHoraZero()) {
            $destinariosResponsaveisEMembros = $this->getEmailsResponsaveisChapaEMembrosSubstituicao(
                $julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()
            );
        }

        $destinatarios = array_merge($destinariosComissao, $destinariosAssessores, $destinariosResponsaveisEMembros);

        $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
            $atividadeJulgamento->getId(),
            $destinatarios,
            $idTipoEmail,
            Constants::TEMPLATE_EMAIL_JULGAMENTO_SUBSTITUICAO,
            $paramsEmail
        );
    }

    /**
     * Envia e-mail para o julgamento no ínicio da atividade de recurso
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function enviarEmailJulgamentoInicioRecurso()
    {
        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            Utils::getDataHoraZero(), null, 2, 5
        );

        foreach ($atividades as $atividadeSecundaria) {

            $atividadeJulgamento = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getId(), 2, 4
            );

            $julgamentosSubstituicaoTO = $this->getJulgamentoSubstituicaoRepository()->getPorCalendario(
                $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getId(), true
            );

            foreach ($julgamentosSubstituicaoTO as $julgamentoSubstituicaoTO) {
                $parametrosEmail = $this->prepararParametrosEmailJulgamentoSubs($julgamentoSubstituicaoTO);

                $idTipoEmail = $julgamentoSubstituicaoTO->getStatusJulgamentoSubstituicao()->getId() == Constants::STATUS_JULGAMENTO_DEFERIDO
                    ? Constants::EMAIL_JULGAMENTO_SUBST_COMISSAO_ELEITORAL_PEDIDO_DEFERIDO
                    : Constants::EMAIL_JULGAMENTO_SUBST_COMISSAO_ELEITORAL_PEDIDO_INDEFERIDO;

                $destinariosResponsaveisEMembros = $this->getEmailsResponsaveisChapaEMembrosSubstituicao(
                    $julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()
                );

                $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
                    $atividadeJulgamento->getId(),
                    $destinariosResponsaveisEMembros,
                    $idTipoEmail,
                    Constants::TEMPLATE_EMAIL_JULGAMENTO_SUBSTITUICAO,
                    $parametrosEmail
                );
            }
        }
    }

    /**
     * Envia e-mail para os responsáveis pela chapa e para os membros substituição
     *
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @return array|null
     * @throws NegocioException
     */
    public function getEmailsResponsaveisChapaEMembrosSubstituicao(
        $pedidoSubstituicaoChapaTO
    ) {

        $destinatarios = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa(
            $pedidoSubstituicaoChapaTO->getChapaEleicao()->getId()
        );

        if (!empty($pedidoSubstituicaoChapaTO->getMembroSubstituidoTitular())) {
            $destinatarios[] = $pedidoSubstituicaoChapaTO->getMembroSubstituidoTitular()->getProfissional()->getPessoa()->getEmail();
        }

        if (!empty($pedidoSubstituicaoChapaTO->getMembroSubstituidoSuplente())) {
            $destinatarios[] = $pedidoSubstituicaoChapaTO->getMembroSubstituidoSuplente()->getProfissional()->getPessoa()->getEmail();
        }

        if (!empty($pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular())) {
            $destinatarios[] = $pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->getProfissional()->getPessoa()->getEmail();
        }

        if (!empty($pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente())) {
            $destinatarios[] = $pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente()->getProfissional()->getPessoa()->getEmail();
        }

        return $destinatarios;
    }

    /**
     * Retorna o id do calendário de acordo com o id do julgamento
     *
     * @param $idJulgamentoSubstituicao
     * @return integer
     */
    public function getIdCalendarioJulgamentoSubstituicao($idJulgamentoSubstituicao)
    {
        return $this->getJulgamentoSubstituicaoRepository()->getIdCalendarioJulgamento(
            $idJulgamentoSubstituicao
        );
    }

    /**
     * Método auxiliar que prepara os parâmetros para o envio de e-mails
     *
     * @param JulgamentoSubstituicaoTO $julgamentoSubstituicaoTO
     * @return array
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function prepararParametrosEmailJulgamentoSubs(
        JulgamentoSubstituicaoTO $julgamentoSubstituicaoTO
    ) {

        $parametrosEmailPedido = $this->getPedidoSubstituicaoChapaBO()->prepararParametrosEmailPedidoSubstituicao(
            $julgamentoSubstituicaoTO->getPedidoSubstituicaoChapa()
        );

        $parametrosEmailJulgamento = [
            Constants::PARAMETRO_EMAIL_JULGAMENTO_PARECER => $julgamentoSubstituicaoTO->getParecer(),
            Constants::PARAMETRO_EMAIL_JULGAMENTO_DESICAO => $julgamentoSubstituicaoTO->getStatusJulgamentoSubstituicao()->getDescricao()
        ];

        return array_merge($parametrosEmailPedido, $parametrosEmailJulgamento);
    }

    /**
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param JulgamentoSubstituicaoTO $julgamentoSubstituicaoTO
     * @param PedidoSubstituicaoChapa $pedidoSubstituicaoChapa
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function validarSalvarJulgamento($julgamentoSubstituicaoTO, $pedidoSubstituicaoChapa)
    {
        if (empty($pedidoSubstituicaoChapa)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $isPermissaoJulgar = $this->isUsuarioComPermissaoJulgar(
            $pedidoSubstituicaoChapa->getChapaEleicao()->getIdCauUf()
        );

        if (empty($julgamentoSubstituicaoTO->getParecer())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($julgamentoSubstituicaoTO->getId())) {
            $statusValidos = [Constants::STATUS_JULGAMENTO_DEFERIDO, Constants::STATUS_JULGAMENTO_INDEFERIDO];
            if (
                empty($julgamentoSubstituicaoTO->getIdStatusJulgamentoSubstituicao())
                && !in_array($julgamentoSubstituicaoTO->getIdStatusJulgamentoSubstituicao(), $statusValidos)
            ) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
            }

            $idStatusPedido = $pedidoSubstituicaoChapa->getStatusSubstituicaoChapa()->getId();
            $isStatusEmAndanmento = $idStatusPedido == Constants::STATUS_SUBSTITUICAO_CHAPA_EM_ANDAMENTO;

            if (!$isStatusEmAndanmento) {
                throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
            }
        }

        if (!$isPermissaoJulgar) {
            throw new NegocioException(Message::MSG_SEM_MERMISSAO_VISUALIZACAO_ATIV_SELECIONADA);
        }
    }

    /**
     * Método faz validação complementar do cadastrado do julgamento de substituição
     *
     * @param $julgamentoSubstituicaoTO
     * @param EleicaoTO $eleicaoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function validacaoComplementarSalvarJulgamento($julgamentoSubstituicaoTO, $eleicaoTO)
    {
        if (empty($eleicaoTO)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $dataInicioVigencia = Utils::getDataHoraZero($eleicaoTO->getCalendario()->getDataInicioVigencia());
        $dataFimVigencia = Utils::getDataHoraZero($eleicaoTO->getCalendario()->getDataFimVigencia());

        if (!(Utils::getDataHoraZero() >= $dataInicioVigencia && Utils::getDataHoraZero() <= $dataFimVigencia)) {
            throw new NegocioException(Message::MSG_PERIODO_VIGENTE_ELEICAO_FECHADO);
        }

        if (!empty($julgamentoSubstituicaoTO->getId())) {
            if (!$this->getUsuarioFactory()->isCorporativoAssessorCEN()) {
                throw new NegocioException(Message::MSG_SEM_MERMISSAO_VISUALIZACAO_ATIV_SELECIONADA);
            }

            $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                $eleicaoTO->getCalendario()->getId(), 2, 4
            );

            if (Utils::getDataHoraZero($atividadeSecundaria->getDataFim()) < Utils::getDataHoraZero()) {
                throw new NegocioException(Message::MSG_PERIODO_JULGAMENTO_ADMISSIBILIDADE_FINALIZADO);
            }
        }
    }

    /**
     * Método auxiliar que verifica se usuário tem permissão de julgar
     *
     * @param $idCauUf
     * @return bool
     */
    private function isUsuarioComPermissaoJulgar($idCauUf)
    {
        $isAssessorCE = $this->getUsuarioFactory()->isCorporativoAssessorCeUfPorCauUf($idCauUf);

        return ($this->getUsuarioFactory()->isCorporativoAssessorCEN() || $isAssessorCE);
    }

    /**
     * Método auxiliar para preparar entidade JulgamentoSubstituicao para cadastro
     *
     * @param JulgamentoSubstituicaoTO $julgamentoSubstituicaoTO
     * @param PedidoSubstituicaoChapa|null $pedidoSubstituicaoChapa
     * @return JulgamentoSubstituicao
     * @throws Exception
     */
    private function prepararJulgamentoSalvar($julgamentoSubstituicaoTO, $pedidoSubstituicaoChapa)
    {
        if (!empty($julgamentoSubstituicaoTO->getId())) {
            $julgamentoSubstituicao = $this->getJulgamentoSubstituicaoRepository()->find(
                $julgamentoSubstituicaoTO->getId()
            );
            $julgamentoSubstituicao->setParecer($julgamentoSubstituicaoTO->getParecer());
        } else {
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $julgamentoSubstituicaoTO->getNomeArquivo(), Constants::PREFIXO_ARQ_JULGAMENTO_SUBSTITUICAO
            );

            $julgamentoSubstituicao = JulgamentoSubstituicao::newInstance([
                'dataCadastro' => Utils::getData(),
                'statusJulgamentoSubstituicao' => ['id' => $julgamentoSubstituicaoTO->getIdStatusJulgamentoSubstituicao()],
                'nomeArquivoFisico' => $nomeArquivoFisico,
                'parecer' => $julgamentoSubstituicaoTO->getParecer(),
                'nomeArquivo' => $julgamentoSubstituicaoTO->getNomeArquivo(),
                'usuario' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->id]
            ]);
            $julgamentoSubstituicao->setPedidoSubstituicaoChapa($pedidoSubstituicaoChapa);
        }

        return $julgamentoSubstituicao;
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $idJulgamentoSubstituicao
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idJulgamentoSubstituicao, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioJulgamentoSubstituicao($idJulgamentoSubstituicao),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param JulgamentoSubstituicao $julgamentoSubstituicao
     * @param bool $isAlteracao
     * @throws Exception
     */
    private function salvarHistoricoJulgamentoSubstituicao(
        JulgamentoSubstituicao $julgamentoSubstituicao,
        $isAlteracao = false
    ): void {
        $historico = $this->getHistoricoBO()->criarHistorico(
            $julgamentoSubstituicao,
            Constants::HISTORICO_ID_TIPO_JULGAMENTO_SUBSTITUICAO,
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
     * Retorna uma nova instância de 'JulgamentoSubstituicaoRepository'.
     *
     * @return JulgamentoSubstituicaoRepository
     */
    private function getJulgamentoSubstituicaoRepository()
    {
        if (empty($this->julgamentoSubstituicaoRepository)) {
            $this->julgamentoSubstituicaoRepository = $this->getRepository(JulgamentoSubstituicao::class);
        }

        return $this->julgamentoSubstituicaoRepository;
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
     * Retorna uma nova instância de 'CalendarioBO'.
     *
     * @return CalendarioBO
     */
    private function getCalendarioBO()
    {
        if (empty($this->calendarioBO)) {
            $this->calendarioBO = app()->make(CalendarioBO::class);
        }

        return $this->calendarioBO;
    }

    /**
     * Retorna uma nova instância de 'PedidoSubstituicaoChapaBO'.
     *
     * @return PedidoSubstituicaoChapaBO
     */
    private function getPedidoSubstituicaoChapaBO()
    {
        if (empty($this->pedidoSubstituicaoChapaBO)) {
            $this->pedidoSubstituicaoChapaBO = app()->make(PedidoSubstituicaoChapaBO::class);
        }

        return $this->pedidoSubstituicaoChapaBO;
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
     * Retorna a intancia de 'ProfissionalBO'.
     *
     * @return ProfissionalBO
     */
    private function getProfissionalBO()
    {
        if ($this->profissionalBO == null) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }
        return $this->profissionalBO;
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
     * @param JulgamentoSubstituicaoTO $julgamentoSubstituicaoTO
     * @return bool
     * @throws NonUniqueResultException
     */
    private function verificarPermitidoAlterarParecerJulgamento(JulgamentoSubstituicaoTO $julgamentoSubstituicaoTO
    ): bool {
        $idCalendario = $this->getJulgamentoSubstituicaoRepository()->getIdCalendarioJulgamento(
            $julgamentoSubstituicaoTO->getId()
        );

        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $idCalendario, 2, 4
        );

        $isPermitoAlterar = false;
        if (
            Utils::getDataHoraZero($atividadeSecundaria->getDataFim()) >= Utils::getDataHoraZero()
            && $this->getUsuarioFactory()->isCorporativoAssessorCEN()
        ) {
            $isPermitoAlterar = true;
        }
        return $isPermitoAlterar;
    }

    /**
     * Verifica se usuário pode visualizar julgamento de acordo com as seguinte regras:
     * - A atividade de recurso dete estar iniciada
     * - Deve ser um membro da comissão CEN ou Ce da Cau UF da chapa vinculada ao julgamento
     *   se parâmetro para verificar estiver habilitado
     * - Deve ser um responsável da chapa vinculada ao julgamento
     *   se parâmetro para verificar estiver habilitado
     *
     * @param $idPedidoSubstituicao
     * @param bool $verificarUsuarioResponsavelChapa
     * @param bool $verificarUsuarioMembroComissao
     * @return JulgamentoSubstituicaoTO|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function getPorPedidoSubstituicaoComVerificacaoUsuario(
        $idPedidoSubstituicao,
        $verificarUsuarioResponsavelChapa,
        $verificarUsuarioMembroComissao
    ) {
        $isPermitidoVisualizar = false;

        /** @var JulgamentoSubstituicao $julgamentoSubstituicao */
        $julgamentoSubstituicao = $this->getJulgamentoSubstituicaoRepository()->findOneBy([
            'pedidoSubstituicaoChapa' => $idPedidoSubstituicao
        ]);

        if (!empty($julgamentoSubstituicao)) {
            $idCalendario = $this->getJulgamentoSubstituicaoRepository()->getIdCalendarioJulgamento(
                $julgamentoSubstituicao->getId()
            );

            $atividadeSecundariaRecurso = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                $idCalendario, 2, 5
            );

            if (
                !empty($atividadeSecundariaRecurso)
                && Utils::getDataHoraZero() >= Utils::getDataHoraZero($atividadeSecundariaRecurso->getDataInicio())
            ) {
                $chapaEleicao = $julgamentoSubstituicao->getPedidoSubstituicaoChapa()->getChapaEleicao();
                $isIES = $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;

                if ($verificarUsuarioMembroComissao) {
                    $isMembroComissao = $this->getMembroComissaoBO()->verificarMembroComissaoPorCauUf(
                        $idCalendario, $chapaEleicao->getIdCauUf(), $isIES
                    );

                    if ($isMembroComissao) {
                        $isPermitidoVisualizar = true;
                    }
                }

                if ($verificarUsuarioResponsavelChapa) {
                    $idChapaEleicao = $this->getChapaEleicaoBO()->getIdChapaEleicaoPorCalendarioEResponsavel(
                        $idCalendario,
                        $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
                    );

                    if (!empty($idChapaEleicao) && $idChapaEleicao == $chapaEleicao->getId()) {
                        $isPermitidoVisualizar = true;
                    }
                }
            }
        }

        return $isPermitidoVisualizar ? JulgamentoSubstituicaoTO::newInstanceFromEntity($julgamentoSubstituicao) : null;
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
}




