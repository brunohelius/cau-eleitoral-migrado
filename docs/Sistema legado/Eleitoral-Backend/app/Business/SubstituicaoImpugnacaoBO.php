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
use App\Entities\MembroChapa;
use App\Entities\PedidoImpugnacao;
use App\Entities\RecursoImpugnacao;
use App\Entities\SubstituicaoImpugnacao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailSubstituicaoImpugnacaoJob;
use App\Mail\JulgamentoRecursoImpugnacaoCadastradoMail;
use App\Mail\SubstituicaoImpugnacaoCadastradoMail;
use App\Repository\SubstituicaoImpugnacaoRepository;
use App\Service\CorporativoService;
use App\To\MembroChapaSubstituicaoTO;
use App\To\SubstituicaoImpugnacaoFiltroTO;
use App\To\SubstituicaoImpugnacaoTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'SubstituicaoImpugnacao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class SubstituicaoImpugnacaoBO extends AbstractBO
{

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var PedidoImpugnacaoBO
     */
    private $pedidoImpugnacaoBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var SubstituicaoImpugnacaoRepository
     */
    private $substituicaoImpugnacaoRepository;

    /**
     * @var HistoricoProfissionalBO
     */
    private $historicoProfissionalBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Retorna a solicitação de substituição do Pedido de Impugnação conforme o id do pedido informado.
     *
     * @param $idPedidoImpugnacao
     *
     * @return SubstituicaoImpugnacaoTO
     * @throws Exception
     */
    public function getPorPedidoImpugnacao($idPedidoImpugnacao)
    {
        return $this->getSubstituicaoImpugnacaoRepository()->getPorPedidoImpugnacao($idPedidoImpugnacao);
    }

    /**
     * Consulta membro substituto para uma solicitação de impugnação procedente.
     *
     * @param SubstituicaoImpugnacaoFiltroTO $filtroTO
     * @return MembroChapa|null
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function consultarMembroParaSubstituto(SubstituicaoImpugnacaoFiltroTO $filtroTO)
    {
        if (empty($filtroTO->getIdPedidoImpugnacao()) || empty($filtroTO->getIdProfissional())) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $pedidoImpugnacao = $this->getPedidoImpugnacaoBO()->findById($filtroTO->getIdPedidoImpugnacao());
        if (empty($pedidoImpugnacao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $this->validacaoComplementarSolicitarSubstituicao($pedidoImpugnacao, $filtroTO->getIdProfissional());

        $membroChapaSubstituicaoTO = MembroChapaSubstituicaoTO::newInstance([
            'idProfissional' => $filtroTO->getIdProfissional(),
            'numeroOrdem' => $pedidoImpugnacao->getMembroChapa()->getNumeroOrdem(),
            'idTipoMembro' => $pedidoImpugnacao->getMembroChapa()->getTipoMembroChapa()->getId(),
            'idTipoParticipacaoChapa' => $pedidoImpugnacao->getMembroChapa()->getTipoParticipacaoChapa()->getId(),
        ]);

        $membroChapaSubstituto = $this->getMembroChapaBO()->prepararSubstituto(
            $pedidoImpugnacao->getMembroChapa()->getChapaEleicao(),
            $membroChapaSubstituicaoTO
        );

        return $membroChapaSubstituto;
    }

    /**
     * Salva o recurso de julgamento de pedido de impugnação
     * @param SubstituicaoImpugnacaoTO $substituicaoImpugnacaoTO
     * @return SubstituicaoImpugnacaoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function salvar(SubstituicaoImpugnacaoTO $substituicaoImpugnacaoTO)
    {
        $this->validacaoInicialSalvarSubstituicao($substituicaoImpugnacaoTO);

        $pedidoImpugnacao = $this->getPedidoImpugnacaoBO()->findById(
            $substituicaoImpugnacaoTO->getIdPedidoImpugnacao()
        );

        $this->validacaoComplementarSolicitarSubstituicao(
            $pedidoImpugnacao, $substituicaoImpugnacaoTO->getIdProfissional()
        );

        try {
            $this->beginTransaction();

            $membroSubstituto = $this->salvarMembroChapaSubstituto($substituicaoImpugnacaoTO, $pedidoImpugnacao);

            $substituicaoImpugnacao = $this->prepararSubstituicaoImpugnacaoSalvar($pedidoImpugnacao, $membroSubstituto);

            $this->getSubstituicaoImpugnacaoRepository()->persist($substituicaoImpugnacao);

            $this->salvarHistoricoSubstituicaoImpugnacao($substituicaoImpugnacao);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new  EnviarEmailSubstituicaoImpugnacaoJob($substituicaoImpugnacao->getId()));

        return SubstituicaoImpugnacaoTO::newInstanceFromEntity($substituicaoImpugnacao);
    }

    /**
     * Responsável por enviar emails após cadastrar pedido substituição chapa
     *
     * @param $idChapaEleicao
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function enviarEmailsSubstituicaoIncluida($idSubstituicaoImpugnacao)
    {
        /** @var SubstituicaoImpugnacao $substituicaoImpugnacao */
        $substituicaoImpugnacao = $this->getSubstituicaoImpugnacaoRepository()->find($idSubstituicaoImpugnacao);
        $pedidoImpugnacao = $substituicaoImpugnacao->getPedidoImpugnacao();
        $chapaEleicao = $pedidoImpugnacao->getMembroChapa()->getChapaEleicao();

        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorAtividadeSecundaria(
            $chapaEleicao->getAtividadeSecundariaCalendario()->getId(), 2, 3
        );

        $isIES = $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;

        // enviar e-mail informativo para responsável chapa uf ou IES
        $this->enviarEmailResponsavelChapa($atividade->getId(), $substituicaoImpugnacao, $isIES);

        // enviar e-mail informativo para membro substituido uf ou IES
        $this->enviarEmailMembroSubstituido($atividade->getId(), $substituicaoImpugnacao, $isIES);

        // enviar e-mail informativo para membro substituto uf ou IES
        $this->enviarEmailMembroSubstituto($atividade->getId(), $substituicaoImpugnacao, $isIES);

        // enviar e-mail informativo para conselheiros CEN e a comissão UF
        // Constants::EMAIL_SUBST_MEMBRO_CHAPA_PARA_CONSELHEIROS_CEN_E_CEUF
        //$this->enviarEmailConselheirosCoordenadoresComissao($atividade->getId(), $substituicaoImpugnacao, $isIES);

        // enviar e-mail informativo para os acessores CEN/BR e CE
        $this->enviarEmailAcessoresCenAndAcessoresCE($atividade->getId(), $substituicaoImpugnacao, $isIES);
    }

    /**
     * Responsável por enviar e-mail informativo para responsável chapa uf ou IES
     * @param int $idAtivSecundaria
     * @param SubstituicaoImpugnacao $substituicaoImpugnacao
     * @param bool $isIES
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailResponsavelChapa(
        int $idAtivSecundaria,
        SubstituicaoImpugnacao $substituicaoImpugnacao,
        bool $isIES
    ) {
        $chapaEleicao = $substituicaoImpugnacao->getPedidoImpugnacao()->getMembroChapa()->getChapaEleicao();
        $destinatarios = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa($chapaEleicao->getId());

        if (!empty($destinatarios)) {
            $idTipoEmail = $isIES ? Constants::EMAIL_SUBST_MEMBRO_CHAPA_RESPONSAVEIS_IES
                : Constants::EMAIL_SUBST_MEMBRO_CHAPA_RESPONSAVEIS_UF;

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $idAtivSecundaria, $idTipoEmail
            );

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $substituicaoImpugnacao);
        }
    }

    /**
     * Responsável por enviar e-mail informativo para membro substituido uf ou IES
     * @param int $idAtivSecundaria
     * @param SubstituicaoImpugnacao $substituicaoImpugnacao
     * @param bool $isIES
     */
    private function enviarEmailMembroSubstituido(
        int $idAtivSecundaria,
        SubstituicaoImpugnacao $substituicaoImpugnacao,
        bool $isIES
    ) {
        $destinatarios = [
            $substituicaoImpugnacao->getPedidoImpugnacao()->getMembroChapa()->getProfissional()->getPessoa()->getEmail()
        ];

        if (!empty($destinatarios)) {
            $idTipoEmail = $isIES ? Constants::EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUIDO_IES
                : Constants::EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUIDO_UF;

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $idAtivSecundaria, $idTipoEmail
            );

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $substituicaoImpugnacao);
        }
    }

    /**
     * Responsável por enviar e-mail informativo para membro substituto uf ou IES
     * @param int $idAtivSecundaria
     * @param SubstituicaoImpugnacao $substituicaoImpugnacao
     * @param bool $isIES
     */
    private function enviarEmailMembroSubstituto(
        int $idAtivSecundaria,
        SubstituicaoImpugnacao $substituicaoImpugnacao,
        bool $isIES
    ) {
        $destinatarios = [$substituicaoImpugnacao->getMembroChapaSubstituto()->getProfissional()->getPessoa()->getEmail()];

        if (!empty($destinatarios)) {
            $idTipoEmail = $isIES ? Constants::EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUTO_IES
                : Constants::EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUTO_UF;

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $idAtivSecundaria, $idTipoEmail
            );

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $substituicaoImpugnacao);
        }
    }

    /**
     * Responsável por enviar e-mail informativo para conselheiros CEN e a comissão UF
     * @param int $idAtivSecundaria
     * @param SubstituicaoImpugnacao $substituicaoImpugnacao
     * @param bool $isIES
     */
    private function enviarEmailConselheirosCoordenadoresComissao(
        int $idAtivSecundaria,
        SubstituicaoImpugnacao $substituicaoImpugnacao,
        bool $isIES
    ) {
        $pedidoImpugnacao = $substituicaoImpugnacao->getPedidoImpugnacao();
        $idCauUf = $isIES ? null : $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getIdCauUf();

        $destinatarios = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $idAtivSecundaria, $idCauUf
        );

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $idAtivSecundaria, Constants::EMAIL_SUBST_MEMBRO_CHAPA_PARA_CONSELHEIROS_CEN_E_CEUF
            );

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $substituicaoImpugnacao);
        }
    }

    /**
     * Responsável por enviar e-mail informativo para os acessores CEN/BR e CE
     * @param int $idAtivSecundaria
     * @param SubstituicaoImpugnacao $substituicaoImpugnacao
     * @param bool $isIES
     */
    private function enviarEmailAcessoresCenAndAcessoresCE(
        int $idAtivSecundaria,
        SubstituicaoImpugnacao $substituicaoImpugnacao,
        bool $isIES
    ) {
        $pedidoImpugnacao = $substituicaoImpugnacao->getPedidoImpugnacao();
        $idCauUf = $isIES ? null : $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getIdCauUf();

        $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $idAtivSecundaria, Constants::EMAIL_SUBST_MEMBRO_CHAPA_PARA_ASSESSOR_CEN_E_CEUF
            );

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $substituicaoImpugnacao);
        }
    }

    /**
     * @param $emailAtividadeSecundaria
     * @param $destinatarios
     * @param SubstituicaoImpugnacao $substituicaoImpugnacao
     * @throws Exception
     */
    private function enviarEmail(
        $emailAtividadeSecundaria,
        $destinatarios,
        SubstituicaoImpugnacao $substituicaoImpugnacao
    ): void {
        if (!empty($emailAtividadeSecundaria)) {
            $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
            $emailTO->setDestinatarios($destinatarios);

            Email::enviarMail(new SubstituicaoImpugnacaoCadastradoMail(
                $emailTO, SubstituicaoImpugnacaoTO::newInstanceFromEntity($substituicaoImpugnacao, false)
            ));
        }
    }

    /**
     * Salva o MembroChapa que será um dos substituto
     *
     * @param SubstituicaoImpugnacaoTO $substituicaoImpugnacaoTO
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @return MembroChapa
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NoResultException
     */
    private function salvarMembroChapaSubstituto(
        SubstituicaoImpugnacaoTO $substituicaoImpugnacaoTO,
        PedidoImpugnacao $pedidoImpugnacao
    ) {
        $profissionalTO = $this->getProfissionalBO()->getPorId($substituicaoImpugnacaoTO->getIdProfissional(), true);

        $this->getMembroChapaBO()->validarImpedimentosIncluirMembro(
            $pedidoImpugnacao->getMembroChapa()->getChapaEleicao(),
            $profissionalTO,
            $pedidoImpugnacao->getMembroChapa()->getTipoMembroChapa()->getId(),
            true
        );

        $this->getMembroChapaBO()->verificarProfissionalPodeSerSubstitutoPorMembro(
            $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getId(),
            $profissionalTO->getId(),
            $pedidoImpugnacao->getMembroChapa(),
            $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getAtividadeSecundariaCalendario()->getId()
        );

        $membroChapa = MembroChapa::newInstance([
            'numeroOrdem' => $pedidoImpugnacao->getMembroChapa()->getNumeroOrdem(),
            'situacaoResponsavel' => false,
            'profissional' => ['id' => $substituicaoImpugnacaoTO->getIdProfissional()],
            'chapaEleicao' => ['id' => $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getId()],
            'tipoMembroChapa' => ['id' => $pedidoImpugnacao->getMembroChapa()->getTipoMembroChapa()->getId()],
            'statusValidacaoMembroChapa' => ['id' => Constants::STATUS_VALIDACAO_MEMBRO_PENDENTE],
            'tipoParticipacaoChapa' => ['id' => $pedidoImpugnacao->getMembroChapa()->getTipoParticipacaoChapa()->getId()],
            'statusParticipacaoChapa' => ['id' => Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR],
            'situacaoMembroChapa' => ['id' => Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO]
        ]);

        $this->getMembroChapaBO()->persist($membroChapa);

        $this->getMembroChapaBO()->salvarPendenciasMembro($membroChapa, $profissionalTO);

        return $membroChapa;
    }

    /**
     *
     * @param SubstituicaoImpugnacaoTO $substituicaoImpugnacaoTO
     */
    private function validacaoInicialSalvarSubstituicao(SubstituicaoImpugnacaoTO $substituicaoImpugnacaoTO)
    {
        if (empty($substituicaoImpugnacaoTO->getIdProfissional())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($substituicaoImpugnacaoTO->getIdPedidoImpugnacao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }
    }

    /**
     * Método responsável por fazer validações complementares antes de salvar a substituição.
     *
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @param $idProfissional
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function validacaoComplementarSolicitarSubstituicao(
        PedidoImpugnacao $pedidoImpugnacao,
        $idProfissional
    ) {
        if ($pedidoImpugnacao->getMembroChapa()->getProfissional()->getId() == $idProfissional) {
            throw new NegocioException(Lang::get('messages.substituicao_impugnacao.mesmo_profissional'));
        }

        $recursosImpugnacao = $pedidoImpugnacao->getJulgamentoImpugnacao()->getRecursosImpugnacao();
        $isCadastradoRecurso = false;

        if (!empty($recursosImpugnacao)) {
            /** @var RecursoImpugnacao $recursoImpugnacao */
            foreach ($recursosImpugnacao as $recursoImpugnacao) {
                $idTipoSolicitacao = $recursoImpugnacao->getTipoSolicitacaoRecursoImpugnacao()->getId();
                if ($idTipoSolicitacao == Constants::TP_SOLICITACAO_RECURSO_RESPONSAVEL_CHAPA) {
                    $isCadastradoRecurso = true;
                }
            }
        }
        $idStatusPedido = $pedidoImpugnacao->getStatusPedidoImpugnacao()->getId();

        if ($isCadastradoRecurso && $idStatusPedido != Constants::STATUS_IMPUGNACAO_RECURSO_PROCEDENTE) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $statusProcedentes = [Constants::STATUS_IMPUGNACAO_PROCEDENTE, Constants::STATUS_IMPUGNACAO_RECURSO_PROCEDENTE];

        $isEmAnaliseRecurso = $idStatusPedido == Constants::STATUS_IMPUGNACAO_RECURSO_EM_ANALISE;
        $idStatusJulgamento = $pedidoImpugnacao->getJulgamentoImpugnacao()->getStatusJulgamentoImpugnacao()->getId();

        if (
            (!$isEmAnaliseRecurso && !in_array($idStatusPedido, $statusProcedentes))
            || ($isEmAnaliseRecurso && $idStatusJulgamento == Constants::STATUS_JULG_IMPUGNACAO_IMPROCEDENTE)
        ) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $isResponsavelChapa = $this->getMembroChapaBO()->isMembroResponsavelChapa(
            $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getId(),
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );
        if (!$isResponsavelChapa) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $eleicao = $this->getEleicaoBO()->getEleicaoPorPedidoImpugnacao($pedidoImpugnacao->getId());

        $nivelAtividadeSecundaria = 4;
        if ($pedidoImpugnacao->getStatusPedidoImpugnacao()->getId() == Constants::STATUS_IMPUGNACAO_RECURSO_PROCEDENTE) {
            $nivelAtividadeSecundaria = 7;
        }

        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $eleicao->getCalendario()->getId(), 3, $nivelAtividadeSecundaria
        );

        $inicioVigente = Utils::getDataHoraZero() >= Utils::getDataHoraZero($atividadeSecundaria->getDataInicio());
        $fimVigente = Utils::getDataHoraZero() <= Utils::getDataHoraZero($atividadeSecundaria->getDataFim());
        if (!($inicioVigente && $fimVigente)) {
            throw new NegocioException(Message::MSG_PERIODO_VIGENCIA_FECHADO_PARA_RESPONSAVEL);
        }

        $substituicao = $this->getSubstituicaoImpugnacaoRepository()->findBy([
            "pedidoImpugnacao" => $pedidoImpugnacao->getId()
        ]);
        if (!empty($substituicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão da substituição de impugnação
     *
     * @param SubstituicaoImpugnacao $substituicaoImpugnacao
     * @throws Exception
     */
    private function salvarHistoricoSubstituicaoImpugnacao(
        SubstituicaoImpugnacao $substituicaoImpugnacao
    ): void {
        $historico = $this->getHistoricoProfissionalBO()->criarHistorico(
            $substituicaoImpugnacao->getId(),
            Constants::HISTORICO_PROF_TIPO_SUBSTITUICAO_IMPUGNACAO,
            Constants::HISTORICO_PROF_ACAO_INSERIR,
            Constants::HISTORICO_INCLUSAO_SUBSTITUICAO_IMPUGNACAO
        );
        $this->getHistoricoProfissionalBO()->salvar($historico);
    }

    /**
     * Método auxiliar para preparar entidade SubstituicaoImpugnacao para cadastro
     *
     * @param $substituicaoImpugnacaoTO
     * @param $pedidoImpugnacao
     * @param $membroChapaSubstituto
     * @return SubstituicaoImpugnacao
     * @throws Exception
     */
    private function prepararSubstituicaoImpugnacaoSalvar(
        $pedidoImpugnacao,
        $membroChapaSubstituto
    ) {
        $substituicaoImpugnacao = SubstituicaoImpugnacao::newInstance([
            'dataCadastro' => Utils::getData(),
            'profissional' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional],
        ]);
        $substituicaoImpugnacao->setPedidoImpugnacao($pedidoImpugnacao);
        $substituicaoImpugnacao->setMembroChapaSubstituto($membroChapaSubstituto);

        return $substituicaoImpugnacao;
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
     * Retorna uma nova instância de 'PedidoImpugnacaoBO'.
     *
     * @return PedidoImpugnacaoBO|mixed
     */
    private function getPedidoImpugnacaoBO()
    {
        if (empty($this->pedidoImpugnacaoBO)) {
            $this->pedidoImpugnacaoBO = app()->make(PedidoImpugnacaoBO::class);
        }

        return $this->pedidoImpugnacaoBO;
    }

    /**
     * Retorna uma nova instância de 'SubstituicaoImpugnacaoRepository'.
     *
     * @return SubstituicaoImpugnacaoRepository
     */
    private function getSubstituicaoImpugnacaoRepository()
    {
        if (empty($this->substituicaoImpugnacaoRepository)) {
            $this->substituicaoImpugnacaoRepository = $this->getRepository(SubstituicaoImpugnacao::class);
        }

        return $this->substituicaoImpugnacaoRepository;
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
     * Retorna a instância do 'CorporativoService'.
     *
     * @return CorporativoService
     */
    private function getCorporativoService()
    {
        if ($this->corporativoService == null) {
            $this->corporativoService = app()->make(CorporativoService::class);
        }
        return $this->corporativoService;
    }
}




