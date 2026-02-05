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

use App\Config\AppConfig;
use App\Config\Constants;
use App\Entities\ArquivoRespostaDeclaracaoChapa;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Calendario;
use App\Entities\ChapaEleicao;
use App\Entities\ChapaEleicaoStatus;
use App\Entities\Declaracao;
use App\Entities\Filial;
use App\Entities\HistoricoChapaEleicao;
use App\Entities\JulgamentoFinal;
use App\Entities\MembroChapa;
use App\Entities\ParametroConselheiro;
use App\Entities\PedidoImpugnacao;
use App\Entities\Profissional;
use App\Entities\StatusChapa;
use App\Entities\StatusChapaJulgamentoFinal;
use App\Entities\UfCalendario;
use App\Entities\Usuario;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\PDFFActory;
use App\Jobs\EnviarEmailChapaConfirmadaJob;
use App\Mail\AtividadeSecundariaMail;
use App\Repository\ArquivoRespostaDeclaracaoChapaRepository;
use App\Repository\AtividadeSecundariaCalendarioRepository;
use App\Repository\ChapaEleicaoRepository;
use App\Repository\ChapaEleicaoStatusRepository;
use App\Repository\MembroChapaRepository;
use App\Repository\ParametroConselheiroRepository;
use App\Repository\StatusChapaRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoTO;
use App\To\ArquivoValidarTO;
use App\To\AtividadePrincipalCalendarioTO;
use App\To\AtividadeSecundariaCalendarioTO;
use App\To\ChapaEleicaoExtratoFiltroTO;
use App\To\ChapaEleicaoTO;
use App\To\ChapaQuantidadeMembrosTO;
use App\To\ConfirmarChapaTO;
use App\To\EleicaoTO;
use App\To\EnvioEmailMembroIncuidoChapaTO;
use App\To\MembroChapaFiltroTO;
use App\To\PedidosChapaTO;
use App\To\PedidoSolicitadoTO;
use App\To\ProfissionalTO;
use App\To\QuantidadeChapasEstadoTO;
use App\To\StatusChapaEleicaoTO;
use App\Util\Email;
use App\Util\JsonUtils;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Mpdf\MpdfException;
use stdClass;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'Chapa'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class ChapaEleicaoBO extends AbstractBO
{

    private const MSG_HISTORICO_STATUS_CHAPA_ALTERADO = "Status da Chapa alterado de %s para %s";

    private const MSG_HISTORICO_EXCLUSAO_CHAPA_RESPONSAVEL_CPF = "Exclusão da Chapa do responsável de CPF %s";

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var DeclaracaoAtividadeBO
     */
    private $declaracaoAtividadeBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var CalendarioBO
     */
    private $calendarioBO;

    /**
     * @var DeclaracaoBO
     */
    private $declaracaoBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var JulgamentoFinalBO
     */
    private $julgamentoFinalBO;

    /**
     * @var RecursoJulgamentoFinalBO
     */
    private $recursoJulgamentoFinalBO;

    /**
     * @var SubstituicaoJulgamentoFinalBO
     */
    private $substituicaoJulgamentoFinalBO;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var UfCalendarioBO
     */
    private $ufCalendarioBO;

    /**
     * @var PlataformaChapaHistoricoBO
     */
    private $plataformaChapaHistoricoBO;

    /**
     * @var RedeSocialChapaBO
     */
    private $redeSocialChapaBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var HistoricoChapaEleicaoBO
     */
    private $historicoChapaEleicaoBO;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var ArquivoRespostaDeclaracaoChapaRepository
     */
    private $arquivoRespostaDeclaracaoChapaRepository;

    /**
     * @var ChapaEleicaoRepository
     */
    private $chapaEleicaoRepository;

    /**
     * @var ChapaEleicaoStatusRepository
     */
    private $chapaEleicaoStatusRepository;

    /**
     * @var ProporcaoConselheiroExtratoBO
     */
    private $proporcaoConselheiroExtratoBO;

    /**
     * @var MembroChapaRepository
     */
    private $membroChapaRepository;

    /**
     * @var StatusChapaRepository
     */
    private $statusChapaRepository;

    /**
     * @var AtividadeSecundariaCalendarioRepository
     */
    private $atividadeSecundariaCalendarioRepository;

    /**
     * @var ParametroConselheiroRepository
     */
    private $parametroConselheiroRepository;

    /**
     * @var PDFFActory
     */
    private $pdfFactory;

    /**
     * @var DenunciaBO
     */
    private $denunciaBO;

    /**
     * @var PedidoSubstituicaoChapaBO
     */
    private $pedidoSubstituicaoChapaBO;

    /**
     * @var PedidoImpugnacaoBO
     */
    private $pedidoImpugnacaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->chapaEleicaoRepository = $this->getRepository(ChapaEleicao::class);
        $this->atividadeSecundariaCalendarioRepository = $this->getRepository(AtividadeSecundariaCalendario::class);
        $this->parametroConselheiroRepository = $this->getRepository(ParametroConselheiro::class);
    }

    /**
     * Retorna a chapa da eleição conforme o id informado.
     *
     * @param $id
     * @param bool $addMembrosRetorno
     *
     * @return ChapaEleicao|null
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getPorId($id, $addMembrosRetorno = false)
    {
        $chapaEleicao = $this->chapaEleicaoRepository->getPorId($id, $addMembrosRetorno);

        if (!empty($chapaEleicao)) {
            $chapaEleicao->definirStatusChapaVigente();

            $this->definirInformacoesComplementarChapa($chapaEleicao);

            $this->atribuirProfissionalChapa($chapaEleicao);

        }

        return $chapaEleicao;
    }

    /**
     * Retorna a chapa da eleição de acordo com o id do pedido de impugnação
     *
     * @param $id
     *
     * @return ChapaEleicao
     * @throws Exception
     */
    public function getPorPedidoImpugnacao($id)
    {
        $chapaEleicao = $this->chapaEleicaoRepository->getPorPedidoImpugnacao($id);
        if (!empty($chapaEleicao)) {
            $chapaEleicao->definirStatusChapaVigente();
        }

        return $chapaEleicao;
    }

    /**
     * Recupera a eleição chapa vigente com o 'id' mais antigo.
     *
     * @return EleicaoTO
     * @throws Exception
     */
    public function getEleicaoVigenteCadastroChapa()
    {
        return $this->getEleicaoBO()->getEleicaoVigentePorNivelAtividade(2, 1);
    }

    /**
     * Recupera a eleição chapa vigente com o 'id' mais antigo.
     *
     * @return EleicaoTO
     * @throws Exception
     */
    public function getEleicaoAtivaCadastroChapa()
    {
        return $this->getEleicaoBO()->getEleicaoAtivaPorNivelAtividade(2, 1);
    }

    /**
     * Recupera a eleição chapa vigente com o 'id' mais antigo.
     *
     * @return EleicaoTO
     * @throws Exception
     */
    public function getEleicaoVigenteSubstituicaoMembroChapa()
    {
        return $this->getEleicaoBO()->getEleicaoVigentePorNivelAtividade(2, 3);
    }

    /**
     * Recupera a eleição chapa vigente com o 'id' mais antigo pelo nível da atividade secundária de impugnação.
     *
     * @return EleicaoTO
     * @throws Exception
     */
    public function getEleicaoVigenteCadastroImpugnacaoChapa()
    {
        return $this->getEleicaoBO()->getEleicaoVigentePorNivelAtividade(3, 1);
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
     * Recupera a chapa eleição de acordo com o uf do profissional logado.
     *
     * @return array
     * @throws NegocioException
     * @throws Exception
     */
    public function getPorProfissionalLogado()
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        $this->validaProfissionalLogado($usuarioLogado);

        $chapaEleicao = null;

        // Enquanto a eleição estiver vigente ele pode visualizar a chapa
        $eleicaoChapaTO = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();

        $this->validarEleicaoECauUfProfisional($eleicaoChapaTO, $usuarioLogado->idCauUf);

        $idsSituacoes = [
            Constants::ST_MEMBRO_CHAPA_CADASTRADO,
            Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO,
            Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO,
        ];
        $membrosChapa = $this->getMembroChapaBO()->getMembrosChapaPorCalendarioProfissioal(
            $eleicaoChapaTO->getCalendario()->getId(),
            $usuarioLogado->idProfissional,
            $usuarioLogado->idCauUf,
            $idsSituacoes
        );

        if (!empty($membrosChapa)) {
            foreach ($membrosChapa as $membroChapa) {
                $idStatusParticipacao = !empty($membroChapa) ? $membroChapa->getStatusParticipacaoChapa()->getId() : null;
                if ($idStatusParticipacao == Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR) {
                    throw new NegocioException(
                        Message::MSG_PROFISSIONAL_VISUALIZAR_POSSUI_CONVITE_PART_CHAPA_A_CONFIRMAR
                    );
                }
            }

            if (count($membrosChapa) == 1) {
                $membroChapa = reset($membrosChapa);
                $chapaEleicao = $this->getPorId($membroChapa->getChapaEleicao()->getId(), true);
            }

            $this->validarCriteriosCotistasRepresentatividade($chapaEleicao, $eleicaoChapaTO->getCalendario()->getId());
        } else {
            $eleicaoChapaTO = $this->getEleicaoAtivaCadastroChapa();
            if (empty($eleicaoChapaTO)) {
                throw new NegocioException(Message::MSG_SEM_PERIODO_VIGENTE_INCLUIR_CHAPA);
            }

            $profissional = $this->getProfissionalBO()->getPorId($usuarioLogado->idProfissional);
            $chapaEleicao = ChapaEleicao::newInstance([
                'idCauUf' => $profissional->getIdCauUf(),
                'profissional' => $profissional
            ]);
            $filial = $this->getFilialBO()->getPorId($profissional->getIdCauUf());
            $chapaEleicao->setCauUf($filial);
        }

        return [
            'chapaEleicao' => $chapaEleicao,
            'eleicaoVigente' => $eleicaoChapaTO
        ];
    }

    /**
     * Valida se o usuário logado é um profissional
     *
     * @param $usuarioLogado
     * @throws NegocioException
     */
    private function validaProfissionalLogado($usuarioLogado): void
    {
        if (empty($usuarioLogado)) {
            throw new NegocioException(Message::TOKEN_INVALIDO);
        }

        if (empty($usuarioLogado->idProfissional)) {
            throw new NegocioException(Message::MSG_RESPONSAVEL_NAO_PROFISSIONAL);
        }
    }

    /**
     * Valida se tem eleição com período vigente e se a uf do profissional está incluída no calendário
     *
     * @param EleicaoTO|null $eleicaoChapaTO
     * @param integer $idCauUf
     * @throws NegocioException
     */
    private function validarEleicaoECauUfProfisional($eleicaoChapaTO, $idCauUf): void
    {
        if (empty($eleicaoChapaTO)) {
            throw new NegocioException(Message::MSG_SEM_PERIODO_VIGENTE_INCLUIR_CHAPA);
        }

        $idCaledario = $eleicaoChapaTO->getCalendario()->getId();

        if (!$this->getUfCalendarioBO()->isCauUfIncluidaCalendario($idCaledario, $idCauUf)) {
            throw new NegocioException(Message::MSG_UF_SEM_ELEICAO_ATIVA, [], true);
        }
    }

    /**
     * Retorna uma nova instância de 'UfCalendarioBO'.
     *
     * @return UfCalendarioBO|mixed
     */
    private function getUfCalendarioBO()
    {
        if (empty($this->ufCalendarioBO)) {
            $this->ufCalendarioBO = app()->make(UfCalendarioBO::class);
        }

        return $this->ufCalendarioBO;
    }

    /**
     * Retorna uma nova instância de 'PlataformaChapaHistoricoBO'.
     *
     * @return PlataformaChapaHistoricoBO|mixed
     */
    private function getPlataformaChapaHistoricoBO()
    {
        if (empty($this->plataformaChapaHistoricoBO)) {
            $this->plataformaChapaHistoricoBO = app()->make(PlataformaChapaHistoricoBO::class);
        }

        return $this->plataformaChapaHistoricoBO;
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
     * Retorna uma nova instância de 'JulgamentoFinalBO'.
     *
     * @return JulgamentoFinalBO|mixed
     */
    private function getJulgamentoFinalBO()
    {
        if (empty($this->julgamentoFinalBO)) {
            $this->julgamentoFinalBO = app()->make(JulgamentoFinalBO::class);
        }

        return $this->julgamentoFinalBO;
    }

    /**
     * Retorna uma nova instância de 'RecursoJulgamentoFinalBO'.
     *
     * @return RecursoJulgamentoFinalBO|mixed
     */
    private function getRecursoJulgamentoFinalBO()
    {
        if (empty($this->recursoJulgamentoFinalBO)) {
            $this->recursoJulgamentoFinalBO = app()->make(RecursoJulgamentoFinalBO::class);
        }

        return $this->recursoJulgamentoFinalBO;
    }

    /**
     * Retorna uma nova instância de 'SubstituicaoJulgamentoFinalBO'.
     *
     * @return SubstituicaoJulgamentoFinalBO|mixed
     */
    private function getSubstituicaoJulgamentoFinalBO()
    {
        if (empty($this->substituicaoJulgamentoFinalBO)) {
            $this->substituicaoJulgamentoFinalBO = app()->make(SubstituicaoJulgamentoFinalBO::class);
        }

        return $this->substituicaoJulgamentoFinalBO;
    }

    /**
     * Método auxiliar para definir informações complementares de chapa
     *
     * @param ChapaEleicao|null $chapaEleicao
     * @param bool $isDefinirFilial
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function definirInformacoesComplementarChapa(?ChapaEleicao $chapaEleicao, $isDefinirFilial = true): void
    {
        $numeroProporcao = 0;

        if ($chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR) {
            $numeroProporcao = $this->getProporcaoConselheiroExtratoBO()->getProporcaoConselheirosPorAtividadeEIdCauUf(
                $chapaEleicao->getAtividadeSecundariaCalendario()->getAtividadePrincipalCalendario()->getId(),
                $chapaEleicao->getIdCauUf()
            );
        }
        $chapaEleicao->setNumeroProporcaoConselheiros($numeroProporcao);

        if ($isDefinirFilial) {
            $this->definirFilialChapa($chapaEleicao);
        }

        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();
        $this->validarCriteriosCotistasRepresentatividade($chapaEleicao, $eleicaoVigente->getId());
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
     * Retorna uma nova instância de 'ExportarChapaXMLBO'.
     *
     * @return ExportarChapaXMLBO
     */
    private function getExportarChapaXMLBO()
    {
        if (empty($this->exportarChapaXMLBO)) {
            $this->exportarChapaXMLBO = app()->make(ExportarChapaXMLBO::class);
        }

        return $this->exportarChapaXMLBO;
    }

    /**
     * Retorna uma nova instância de 'ExportarChapaCSVBO'.
     *
     * @return ExportarChapaCSVBO
     */
    private function getExportarChapaCSVBO()
    {
        if (empty($this->exportarChapaCSVBO)) {
            $this->exportarChapaCSVBO = app()->make(ExportarChapaCSVBO::class);
        }

        return $this->exportarChapaCSVBO;
    }

    /**
     * Método auxiliar para definir filial na chapa
     *
     * @param ChapaEleicao|null $chapaEleicao
     * @throws NegocioException
     */
    public function definirFilialChapa(?ChapaEleicao $chapaEleicao): void
    {
        $filial = $this->getFilialBO()->getPorId($chapaEleicao->getIdCauUf());

        if (!empty($filial) && $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES) {
            $filial->setDescricao(Constants::PREFIXO_IES);
        }

        $chapaEleicao->setCauUf($filial);
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
     * Método auxiliar que busca e seta o profissional em cada membro da lista
     *
     * @param ChapaEleicao $chapaEleicao
     * @throws NonUniqueResultException
     */
    public function atribuirProfissionalChapa(ChapaEleicao $chapaEleicao)
    {
        $profissional = $this->getProfissionalBO()->getPorId($chapaEleicao->getIdProfissionalInclusao());

        $chapaEleicao->setProfissional($profissional);

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

    /**
     * Recupera a chapa eleição de acordo com o uf do profissional logado.
     *
     * @return mixed
     * @throws NegocioException
     * @throws Exception
     */
    public function getPorProfissionalInclusao()
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        $this->validaProfissionalLogado($usuarioLogado);

        $eleicaoChapaTO = $this->getEleicaoVigenteCadastroChapa();

        $this->validarEleicaoECauUfProfisional($eleicaoChapaTO, $usuarioLogado->idCauUf);

        $chapaEleicao = $this->chapaEleicaoRepository->getChapaEleicaoPorResponsavelInclusao(
            $usuarioLogado->idProfissional,
            $usuarioLogado->idCauUf,
            [Constants::ETAPA_PLATAF_ELEITORAL_REDES_SOCIAIS_INCLUIDA, Constants::ETAPA_MEMBROS_CHAPA_INCLUIDA]
        );

        if (empty($chapaEleicao)) {

            $this->validarMembroParticipaOutraChapa(
                $eleicaoChapaTO->getCalendario()->getId(),
                $usuarioLogado->idProfissional,
                $usuarioLogado->idCauUf
            );
            $profissional = $this->getProfissionalBO()->getPorId($usuarioLogado->idProfissional);

            $chapaEleicao = ChapaEleicao::newInstance([
                'idCauUf' => $profissional->getIdCauUf(),
                'profissional' => $profissional
            ]);
            $filial = $this->getFilialBO()->getPorId($profissional->getIdCauUf());
            $chapaEleicao->setCauUf($filial);
        } else {
            $this->atribuirProfissionalChapa($chapaEleicao);

            $this->definirInformacoesComplementarChapa($chapaEleicao);
        }

        return [
            'chapaEleicao' => $chapaEleicao,
            'eleicaoVigente' => $eleicaoChapaTO
        ];
    }

    /**
     * Valida se membro já está incluido em outra chapa
     *
     * @param integer $idCalendario
     * @param integer $idProfissional
     * @param integer $idCauUf
     * @throws NegocioException
     * @throws Exception
     */
    private function validarMembroParticipaOutraChapa($idCalendario, $idProfissional, $idCauUf): void
    {
        $membrosChapa = $this->getMembroChapaBO()->getMembrosChapaPorCalendarioProfissioal(
            $idCalendario,
            $idProfissional,
            $idCauUf
        );

        foreach ($membrosChapa as $membroChapa) {
            $idStatusParticipacao = !empty($membroChapa) ? $membroChapa->getStatusParticipacaoChapa()->getId() : null;

            if ($idStatusParticipacao == Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR) {
                throw new NegocioException(Message::MSG_PROFISSIONAL_POSSUI_CONVITE_PART_CHAPA_A_CONFIRMAR);
            }

            if ($idStatusParticipacao == Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO) {
                throw new NegocioException(Message::MSG_PROFISSIONAL_JA_CONFIRMOU_PART_CHAPA);
            }
        }
    }

    /**
     * Altera o status da Chapa Eleição dado um id chapa eleição.
     *
     * @param StatusChapaEleicaoTO $statusChapaEleicaoTO
     *
     * @return void
     * @throws Exception
     */
    public function alterarStatus(StatusChapaEleicaoTO $statusChapaEleicaoTO)
    {
        if (!$this->getUsuarioFactory()->hasPermissao(Constants::PERMISSAO_ACESSOR_CEN)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if (empty($statusChapaEleicaoTO->getJustificativa())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        try {
            $this->beginTransaction();

            $chapaEleicao = $this->getPorId($statusChapaEleicaoTO->getIdChapaEleicao());
            if (!empty($chapaEleicao)) {

                $statusChapaAnterior = $chapaEleicao->getStatusChapaVigente();

                $this->salvarChapaEleicaoStatus(
                    $chapaEleicao->getId(),
                    $statusChapaEleicaoTO->getIdStatusChapa(),
                    Constants::TP_ALTERACAO_MANUAL
                );

                $statusChapaAtualizado = $this->getStatusChapaRepository()->find(
                    $statusChapaEleicaoTO->getIdStatusChapa()
                );

                $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
                    $chapaEleicao,
                    Constants::ORIGEM_CORPORATIVO,
                    sprintf(
                        self::MSG_HISTORICO_STATUS_CHAPA_ALTERADO,
                        $statusChapaAnterior->getDescricao(),
                        $statusChapaAtualizado->getDescricao()
                    ),
                    $statusChapaEleicaoTO->getJustificativa()
                );
                $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);
            }

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Responsável por salvar status da chapa eleição
     *
     * @param $idChapaEleicao
     * @param $idStatusChapa
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function salvarChapaEleicaoStatus($idChapaEleicao, $idStatusChapa, $idTipoAlteracao)
    {
        $chapaEleicaoStatus = ChapaEleicaoStatus::newInstance([
            'data' => Utils::getData(),
            'chapaEleicao' => ['id' => $idChapaEleicao],
            'statusChapa' => ['id' => $idStatusChapa],
            'tipoAlteracao' => ['id' => $idTipoAlteracao]
        ]);

        $this->getChapaEleicaoStatusRepository()->persist($chapaEleicaoStatus);
    }

    /**
     * Atualiza a chapa após o julgamento final 1ª instância
     * @param $idChapaEleicao
     * @param $idStatusJulgamentoFinal
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function atualizarChapaEleicaoPosJulgamentoFinal($idChapaEleicao, $idStatusJulgamentoFinal)
    {
        $idStatusChapaJulgamentoFinal = Constants::STATUS_CHAPA_JULG_FINAL_DEFERIDO;
        if ($idStatusJulgamentoFinal == Constants::STATUS_JULG_FINAL_INDEFERIDO) {
            $idStatusChapaJulgamentoFinal = Constants::STATUS_CHAPA_JULG_FINAL_INDEFERIDO;
        }

        $this->atualizarStatusChapaJulgamentoFinal($idChapaEleicao, $idStatusChapaJulgamentoFinal);
    }

    /**
     * Atualiza o status chapa julgamento finak
     * @param $idChapaEleicao
     * @param $idStatusChapaJulgamentoFinal
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function atualizarStatusChapaJulgamentoFinal($idChapaEleicao, $idStatusChapaJulgamentoFinal)
    {
        /** @var ChapaEleicao $chapaEleicao */
        $chapaEleicao = $this->chapaEleicaoRepository->find($idChapaEleicao);

        $chapaEleicao->setStatusChapaJulgamentoFinal(StatusChapaJulgamentoFinal::newInstance([
            'id' => $idStatusChapaJulgamentoFinal
        ]));

        $this->chapaEleicaoRepository->persist($chapaEleicao);
    }

    /**
     * Retorna uma nova instância de 'ChapaEleicaoStatusRepository'.
     *
     * @return ChapaEleicaoStatusRepository
     */
    private function getChapaEleicaoStatusRepository()
    {
        if (empty($this->chapaEleicaoStatusRepository)) {
            $this->chapaEleicaoStatusRepository = $this->getRepository(ChapaEleicaoStatus::class);
        }

        return $this->chapaEleicaoStatusRepository;
    }

    /**
     * Retorna uma nova instância de 'StatusChapaRepository'.
     *
     * @return StatusChapaRepository
     */
    private function getStatusChapaRepository()
    {
        if (empty($this->statusChapaRepository)) {
            $this->statusChapaRepository = $this->getRepository(StatusChapa::class);
        }

        return $this->statusChapaRepository;
    }

    /**
     * Retorna uma nova instância de 'HistoricoChapaEleicaoBO'.
     *
     * @return HistoricoChapaEleicaoBO
     */
    private function getHistoricoChapaEleicaoBO()
    {
        if (empty($this->historicoChapaEleicaoBO)) {
            $this->historicoChapaEleicaoBO = app()->make(HistoricoChapaEleicaoBO::class);
        }

        return $this->historicoChapaEleicaoBO;
    }

    /**
     * Salva a entidade 'ChapaEleicao' e suas 'RedeSocialChapa'
     *
     * @param ChapaEleicao $chapaEleicao
     *
     * @return ChapaEleicao|null
     * @throws Exception
     */
    public function salvar(ChapaEleicao $chapaEleicao, $justificativa = null)
    {
        $isInclusao = (empty($chapaEleicao->getId())) ? true : false;

        $isAcessorCEN = $this->getUsuarioFactory()->hasPermissao(Constants::PERMISSAO_ACESSOR_CEN);


        $chapaEleicaoRetorno = null;
        try {
            $this->beginTransaction();

            if (!$isAcessorCEN) {
                $this->validarPeriodoVigenteAtividadeSecundariaIncruirChapa($chapaEleicao);
            }

            $idEtapa = null;
            if (!$isInclusao) {
                $chapaEleicaoRetorno = $this->getPorId($chapaEleicao->getId());

                if (empty($chapaEleicaoRetorno->getId())) {
                    throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
                }

                $idEtapa = $chapaEleicaoRetorno->getIdEtapa();
            }

            $redesSociais = $chapaEleicao->getRedesSociaisChapa();

            $descricaoHistorico = ($isAcessorCEN) ? Constants::HISTORICO_ALTERACAO_PLATAFORMA_ELEITORAL : null;
            if ($isAcessorCEN or $idEtapa != Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA) {

                $this->validarCamposObrigatorios($chapaEleicao, $idEtapa);

                if ($isInclusao) {

                    $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

                    $profissional = $this->getProfissionalBO()->getPorId(
                        $usuarioLogado->idProfissional, true
                    );

                    $chapaEleicao->setIdProfissionalInclusao($profissional->getId());
                    $this->validarConviteParticipacaoIncluirChapa($chapaEleicao, $profissional);

                    $chapaEleicao->setRedesSociaisChapa(new ArrayCollection());
                    $chapaEleicao->setIdCauUf($profissional->getIdCauUf());
                    $chapaEleicao->setIdEtapa(Constants::ETAPA_PLATAF_ELEITORAL_REDES_SOCIAIS_INCLUIDA);
                    $chapaEleicao->setStatusChapaJulgamentoFinal(StatusChapaJulgamentoFinal::newInstance([
                        'id' => Constants::STATUS_CHAPA_JULG_FINAL_AGUARDANDO
                    ]));

                    $chapaEleicao->setProfissionalInclusaoPlataforma(Profissional::newInstance([
                        'id' => $usuarioLogado->idProfissional
                    ]));

                    $chapaEleicaoRetorno = $this->chapaEleicaoRepository->persist($chapaEleicao);

                    $descricaoHistorico = sprintf(
                        Constants::HISTORICO_INCLUSAO_DADOS_ABA,
                        Constants::ABA_PLATAFORMA_ELEITORAL
                    );
                } else {
                    $chapaEleicaoRetorno->setDescricaoPlataforma($chapaEleicao->getDescricaoPlataforma());
                    $chapaEleicaoRetorno->setRedesSociaisChapa(new ArrayCollection());
                    $chapaEleicaoRetorno = $this->chapaEleicaoRepository->persist($chapaEleicaoRetorno);
                }
            }

            $this->getRedeSocialChapaBO()->salvarRedesSociais($chapaEleicao, $redesSociais, $isAcessorCEN);

            if (!empty($descricaoHistorico)) {
                $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
                    $chapaEleicao,
                    $isAcessorCEN ? Constants::ORIGEM_CORPORATIVO : Constants::ORIGEM_PROFISSIONAL,
                    $descricaoHistorico,
                    $justificativa
                );
                $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);
            }

            $this->commitTransaction();

        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return $this->getPorId($chapaEleicaoRetorno->getId(), true);
    }

    /**
     * Altera a entidade 'ChapaEleicao' e suas 'RedeSocialChapa'
     *
     * @param ChapaEleicao $chapaEleicao
     *
     * @return ChapaEleicaoTO|null
     * @throws Exception
     */
    public function alterarPlataforma(ChapaEleicao $chapaEleicao, $justificativa = null, $alterarAposDataFim = null)
    {
        $isAcessorCEN = $this->getUsuarioFactory()->isCorporativoAssessorCEN();
        $chapaEleicaoRetorno = null;

        $this->validarCamposAlteracaoPlataforma($chapaEleicao, $justificativa);
        $this->verificaPermissaoDeAlterarPlataforma($chapaEleicao, $isAcessorCEN);

        /** @var ChapaEleicao $chapaEleicaoAnterior */
        $chapaEleicaoAnterior = $this->chapaEleicaoRepository->find($chapaEleicao->getId());

        try {
            $this->beginTransaction();

            $isProfissionalAlteracaoAposDataFim = false;
            if (!$isAcessorCEN && $alterarAposDataFim != true) {
                $isProfissionalAlteracaoAposDataFim = !$this->isPeriodoVigenteAtividadeSecundariaIncruirChapa(
                    $chapaEleicaoAnterior
                );
            }

            if (!$isProfissionalAlteracaoAposDataFim) {
                $this->getPlataformaChapaHistoricoBO()->salvar($chapaEleicaoAnterior);

                $this->prepararChapaAlteracaoPlataforma($chapaEleicao, $chapaEleicaoAnterior, $isAcessorCEN);

                $this->chapaEleicaoRepository->persist($chapaEleicaoAnterior);
            }

            $this->getRedeSocialChapaBO()->salvarRedesSociais(
                $chapaEleicaoAnterior,
                $chapaEleicao->getRedesSociaisChapa(),
                $isAcessorCEN,
                false,
                false
            );

            $this->salvarHistoricoAlteracaoPlataforma($chapaEleicao, $justificativa, $isAcessorCEN);

            $this->commitTransaction();

        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return ChapaEleicaoTO::newInstance($this->getPorId($chapaEleicaoAnterior->getId(), true));
    }

    /**
     * Valida so campos obrigatórios informados na hora de alteração da Plataforma
     * @param ChapaEleicao $chapaEleicao
     * @param null $justificativa
     * @throws NegocioException
     */
    public function validarCamposAlteracaoPlataforma(ChapaEleicao $chapaEleicao, $justificativa = null)
    {
        if (empty($chapaEleicao->getId())) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        if (empty($chapaEleicao->getDescricaoPlataforma())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
        }
    }

    /**
     * Verifica se a data atual está dentro do período vigente da atividade secundária
     *
     * @param ChapaEleicao $chapaEleicao
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function validarPeriodoVigenteAtividadeSecundariaIncruirChapa(ChapaEleicao $chapaEleicao)
    {
        if (empty($chapaEleicao->getAtividadeSecundariaCalendario()) or
            empty($chapaEleicao->getAtividadeSecundariaCalendario()->getId())
        ) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, [], true);
        }

        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorId(
            $chapaEleicao->getAtividadeSecundariaCalendario()->getId()
        );

        if ((Utils::getDataHoraZero($atividadeSecundaria->getDataFim()) < Utils::getDataHoraZero()) or
            (Utils::getDataHoraZero($atividadeSecundaria->getDataInicio()) > Utils::getDataHoraZero())) {
            throw new NegocioException(Message::MSG_SEM_PERIODO_VIGENTE_INCLUIR_CHAPA);
        }
    }

    /**
     * Verifica se a data atual está dentro do período vigente da atividade secundária
     *
     * @param ChapaEleicao $chapaEleicao
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function isPeriodoVigenteAtividadeSecundariaIncruirChapa(ChapaEleicao $chapaEleicao)
    {
        if (empty($chapaEleicao->getAtividadeSecundariaCalendario()) or
            empty($chapaEleicao->getAtividadeSecundariaCalendario()->getId())
        ) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, [], true);
        }

        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorId(
            $chapaEleicao->getAtividadeSecundariaCalendario()->getId()
        );

        return (
            (Utils::getDataHoraZero($atividadeSecundaria->getDataInicio()) <= Utils::getDataHoraZero()) &&
            (Utils::getDataHoraZero($atividadeSecundaria->getDataFim()) >= Utils::getDataHoraZero())
        );
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
     * Verifica se os campos obrigatórios foram preenchidos.
     *
     * @param ChapaEleicao $chapaEleicao
     * @param integer|null $idEtatpa
     *
     * @throws NegocioException
     */
    private function validarCamposObrigatorios(ChapaEleicao $chapaEleicao, $idEtatpa = null)
    {
        $atividadeSecundaria = $chapaEleicao->getAtividadeSecundariaCalendario();
        if (empty($idEtatpa) and (empty($atividadeSecundaria) or empty($atividadeSecundaria->getId()))) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
        }

        $tipoCandidatura = $chapaEleicao->getTipoCandidatura();
        if (empty($idEtatpa) and (empty($tipoCandidatura) or empty($tipoCandidatura->getId()))) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
        }

        if (empty($chapaEleicao->getDescricaoPlataforma())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
        }
    }

    /**
     * Verifica se o profissional já confirmou ou possui convites a confirmar de participação em alguma chapa.
     *
     * @param ChapaEleicao $chapaEleicao
     * @param ProfissionalTO $profissionalTO
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function validarConviteParticipacaoIncluirChapa(ChapaEleicao $chapaEleicao, $profissionalTO)
    {

        $chapaEleicaoResponsavel = $this->chapaEleicaoRepository->getChapaEleicaoPorAtividadeProfissional(
            $chapaEleicao->getAtividadeSecundariaCalendario()->getId(),
            $profissionalTO->getId(),
            $profissionalTO->getIdCauUf()
        );
        if (!empty($chapaEleicaoResponsavel) && $chapaEleicaoResponsavel->getIdEtapa() != Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        $totalConvitesConfirmados = $this->getMembroChapaBO()->totalConvitePorStatusParticipacao(
            $chapaEleicao->getIdProfissionalInclusao(),
            $chapaEleicao->getAtividadeSecundariaCalendario()->getId(),
            Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
        );

        if (!empty($totalConvitesConfirmados) and $totalConvitesConfirmados > 0) {
            throw new NegocioException(Message::MSG_PROFISSIONAL_JA_CONFIRMOU_PART_CHAPA, [], true);
        }

        $totalConvitesAConfirmar = $this->getMembroChapaBO()->totalConvitePorStatusParticipacao(
            $chapaEleicao->getIdProfissionalInclusao(),
            $chapaEleicao->getAtividadeSecundariaCalendario()->getId(),
            Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR
        );

        if (!empty($totalConvitesAConfirmar) and $totalConvitesAConfirmar > 0) {
            throw new NegocioException(Message::MSG_PROFISSIONAL_POSSUI_CONVITE_PART_CHAPA_A_CONFIRMAR, [], true);
        }
    }

    /**
     * Retorna uma nova instância de 'RedeSocialChapaBO'.
     *
     * @return RedeSocialChapaBO|mixed
     */
    private function getRedeSocialChapaBO()
    {
        if (empty($this->redeSocialChapaBO)) {
            $this->redeSocialChapaBO = app()->make(RedeSocialChapaBO::class);
        }

        return $this->redeSocialChapaBO;
    }

    /**
     * Salva a etapa de inclusão de membros na inclusão de Chapa Eleição.
     *
     * @param integer $idChapaEleicao
     * @param array $listaMembrosChapaTO
     *
     * @return ChapaEleicao|null
     * @throws Exception
     */
    public function salvarMembros($idChapaEleicao, $listaMembrosChapaTO)
    {

        $chapaEleicao = $this->getPorId($idChapaEleicao);

        $this->validarSalvarMembros($chapaEleicao, $listaMembrosChapaTO);

        try {
            $this->beginTransaction();

            $this->getMembroChapaBO()->alterarSituacaoResponsavelMembrosChapa($chapaEleicao, $listaMembrosChapaTO);

            if ($chapaEleicao->getIdEtapa() == Constants::ETAPA_PLATAF_ELEITORAL_REDES_SOCIAIS_INCLUIDA) {
                $chapaEleicao->setIdEtapa(Constants::ETAPA_MEMBROS_CHAPA_INCLUIDA);
                $chapaEleicaoSalvo = $this->chapaEleicaoRepository->persist($chapaEleicao);

                $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
                    $chapaEleicao,
                    Constants::ORIGEM_PROFISSIONAL,
                    sprintf(Constants::HISTORICO_INCLUSAO_DADOS_ABA, Constants::ABA_MEMBROS_DA_CHAPA)
                );
                $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);
            }

            $this->commitTransaction();

        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        return $this->getPorId($idChapaEleicao);
    }

    /**
     * Verifica etapa de salvar membros
     *
     * @param ChapaEleicao $chapaEleicao
     * @param array $listaMembrosChapaTO
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function validarSalvarMembros(ChapaEleicao $chapaEleicao, $listaMembrosChapaTO)
    {
        if (empty($chapaEleicao) or $chapaEleicao->getIdEtapa() == Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        $totalMembrosIncluidos = $this->getMembroChapaRepository()->totalMembrosChapa($chapaEleicao->getId());

        if ($totalMembrosIncluidos != count($listaMembrosChapaTO)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        if ($totalMembrosIncluidos == 0) {
            throw new NegocioException(Message::MSG_CRIACAO_CHAPA_DEVE_TER_NO_MIN_UM_MEMBRO, [], true);
        }

        $membroCriadorChapa = $this->getMembroChapaRepository()->getMembroChapaPorProfissional(
            $chapaEleicao->getId(),
            $chapaEleicao->getIdProfissionalInclusao(),
            [Constants::ST_MEMBRO_CHAPA_CADASTRADO, Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO]
        );
        if (empty($membroCriadorChapa)) {
            throw new NegocioException(Message::MSG_CPF_CRIADOR_CHAPA_NAO_CONSTA_NOS_MEMBROS, [], true);
        }

        $idTipoParticipacao = $membroCriadorChapa->getTipoParticipacaoChapa()->getId();
        $isTitular = Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR == $idTipoParticipacao;
        if ($chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES and !$isTitular) {
            throw new NegocioException(Message::MSG_CRIADOR_CHAPA_IES_DEVE_SER_TITULAR, [], true);
        }

        $totalResponsaveis = 1;
        foreach ($listaMembrosChapaTO as $membroChapa) {
            if ($membroChapa->getId() != $membroCriadorChapa->getId() and $membroChapa->isSituacaoResponsavel()) {
                $totalResponsaveis++;
            }
        }
        if ($totalResponsaveis > 3) {
            throw new NegocioException(Message::MSG_PERMITIDO_ATE_TRES_RESPONSAVEIS_CHAPA, [], true);
        }
    }

    /**
     * Retorna uma nova instância de 'MembroChapaRepository'.
     *
     * @return MembroChapaRepository
     */
    private function getMembroChapaRepository()
    {
        if (empty($this->membroChapaRepository)) {
            $this->membroChapaRepository = $this->getRepository(MembroChapa::class);
        }

        return $this->membroChapaRepository;
    }

    /**
     * Confirma a criação da Chapa Eleição e salva a respospa da declaração.
     *
     * @param integer $idChapaEleicao
     * @param ConfirmarChapaTO $confirmarChapaTO
     *
     * @return ChapaEleicao|null
     * @throws Exception
     */
    public function confirmarChapa($idChapaEleicao, $confirmarChapaTO)
    {
        $chapaEleicao = $this->chapaEleicaoRepository->getPorId($idChapaEleicao);

        if (empty($chapaEleicao) or $chapaEleicao->getIdEtapa() != Constants::ETAPA_MEMBROS_CHAPA_INCLUIDA) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        $declaracaoAtividade = $this->getDeclaracaoAtividadeBO()->getDeclaracaoPorChapa($chapaEleicao);

        /** @var Declaracao $declaracao */
        $declaracao = $declaracaoAtividade->getDeclaracao();

        try {
            $this->beginTransaction();

            $this->validarRespostaDeclaracaoChapa($confirmarChapaTO, $declaracao);

            $chapaEleicao->setIdEtapa(Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA);
            $chapaEleicao->setSituacaoRespostaDeclaracao(true);
            $chapaConfirmada = $this->chapaEleicaoRepository->persist($chapaEleicao);

            $this->salvarChapaEleicaoStatus(
                $idChapaEleicao,
                Constants::SITUACAO_CHAPA_PENDENTE,
                Constants::TP_ALTERACAO_AUTOMATICO
            );

            $this->salvarArquivosDeclaracaoChapa(
                $chapaEleicao,
                $confirmarChapaTO->getArquivosRespostaDeclaracaoChapa(),
                $declaracao
            );

            $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
                $chapaEleicao,
                Constants::ORIGEM_PROFISSIONAL,
                Constants::HISTORICO_CONFIRMACAO_CRIACAO_CHAPA
            );
            $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);

            $this->concluirChapa($chapaEleicao, Constants::TP_ALTERACAO_AUTOMATICO);

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        $chapaEleicao = $this->chapaEleicaoRepository->getPorId($idChapaEleicao);

        Utils::executarJOB(new EnviarEmailChapaConfirmadaJob($idChapaEleicao));

        return $chapaEleicao;
    }

    /**
     * Retorna uma nova instância de 'DeclaracaoAtividadeBO'.
     *
     * @return DeclaracaoAtividadeBO|mixed
     */
    private function getDeclaracaoAtividadeBO()
    {
        if (empty($this->declaracaoAtividadeBO)) {
            $this->declaracaoAtividadeBO = app()->make(DeclaracaoAtividadeBO::class);
        }

        return $this->declaracaoAtividadeBO;
    }

    /**
     * Valida as respostas da declaração da chapa
     *
     * @param ConfirmarChapaTO $confirmarChapaTO
     * @param Declaracao $declaracao
     *
     * @throws NegocioException
     */
    private function validarRespostaDeclaracaoChapa($confirmarChapaTO, Declaracao $declaracao)
    {
        if (empty($confirmarChapaTO->getItensDeclaracao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $isRespostaUnica = Constants::TIPO_RESPOSTA_DECLARACAO_UNICA === $declaracao->getTipoResposta();
        $isRespostaMultipla = Constants::TIPO_RESPOSTA_DECLARACAO_MULTIPLA === $declaracao->getTipoResposta();

        $totalItensDeclaracao = count($confirmarChapaTO->getItensDeclaracao());
        if (
            $totalItensDeclaracao < 1
            or ($isRespostaUnica && $totalItensDeclaracao > 1)
            or ($isRespostaMultipla && $totalItensDeclaracao !== count($declaracao->getItensDeclaracao()))
        ) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $idsItensDeclaracaoPreenchida = $confirmarChapaTO->getItensDeclaracao();
        $idsItensDeclaracaoPreenchida = array_map(static function ($item) {
            return $item->getId();
        }, $idsItensDeclaracaoPreenchida);

        $itemDeclaracao = $declaracao->getItensDeclaracao();
        $idsItemDeclaracao = [];
        foreach ($itemDeclaracao as $item) {
            $idsItemDeclaracao[] = $item->getId();
        }

        if (!empty(array_diff($idsItensDeclaracaoPreenchida, $idsItemDeclaracao))) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }
    }

    /**
     * Valida arquivos resposta da declaração da chapa
     *
     * @param array $arquivosRespostaDeclaracaoChapa
     * @param Declaracao $declaracao
     * @throws NegocioException
     */
    private function validarArquivosRespostaDeclaracaoChapa($arquivosRespostaDeclaracaoChapa, Declaracao $declaracao)
    {
        if ($declaracao->getUploadObrigatorio() && empty($arquivosRespostaDeclaracaoChapa)) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
        }

        if (!empty($arquivosRespostaDeclaracaoChapa)) {

            if (count($arquivosRespostaDeclaracaoChapa) > Constants::QUANTIDADE_MAX_ARQUIVO_DECLARACAO_CHAPA) {
                throw new NegocioException(Message::MSG_PERMISSAO_DOIS_UPLOADS, [], true);
            }

            /** @var ArquivoRespostaDeclaracaoChapa $arquivoRespostaDeclaracaoChapa */
            foreach ($arquivosRespostaDeclaracaoChapa as $arquivoRespostaDeclaracaoChapa) {

                $this->validarArquivoPorDeclaracao($arquivoRespostaDeclaracaoChapa, $declaracao);

                if (empty($arquivoRespostaDeclaracaoChapa->getArquivo())) {
                    throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
                }

            }
        }
    }

    /**
     * Faz a validação de tamanho e extensão. Também valida campo obrigatório nome e tamnaho.
     *
     * @param ArquivoRespostaDeclaracaoChapa $arquivoRespostaDeclaracaoChapa
     * @param Declaracao $declaracao
     * @throws NegocioException
     */
    private function validarArquivoPorDeclaracao($arquivoRespostaDeclaracaoChapa, $declaracao)
    {
        if (empty($arquivoRespostaDeclaracaoChapa->getNome())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($arquivoRespostaDeclaracaoChapa->getTamanho())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $this->getArquivoService()->validarArquivoDeclaracao($declaracao, ArquivoValidarTO::newInstance([
            "nomeArquivo" => $arquivoRespostaDeclaracaoChapa->getNome(),
            "tamanhoArquivo" => $arquivoRespostaDeclaracaoChapa->getTamanho(),
            "tamanhoPermitido" => Constants::TAMANHO_LIMITE_ARQUIVO_DECLARACAO_CHAPA,
            "codigoMsgTamanhoArquivo" => Message::MSG_LIMITE_TAMANHO_ARQUIVO_DECLARACAO_CHAPA
        ]));
    }

    /**
     * Retorna a instância do 'ArquivoService'.
     *
     * @return ArquivoService
     */
    private function getArquivoService()
    {
        if ($this->arquivoService == null) {
            $this->arquivoService = app()->make(ArquivoService::class);
        }
        return $this->arquivoService;
    }

    /**
     * Retorna uma nova instância de 'ArquivoRespostaDeclaracaoChapaRepository'.
     *
     * @return ArquivoRespostaDeclaracaoChapaRepository
     */
    private function getArquivoRespostaDeclaracaoChapaRepository()
    {
        if (empty($this->arquivoRespostaDeclaracaoChapaRepository)) {
            $this->arquivoRespostaDeclaracaoChapaRepository = $this->getRepository(
                ArquivoRespostaDeclaracaoChapa::class
            );
        }

        return $this->arquivoRespostaDeclaracaoChapaRepository;
    }

    /**
     * @param ChapaEleicao|null $chapaEleicao
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function concluirChapa(?ChapaEleicao $chapaEleicao, $idTipoAlteracao): void
    {
        if ($this->verificarChapaPodeSerConcluida($chapaEleicao)) {
            $this->salvarChapaEleicaoStatus(
                $chapaEleicao->getId(),
                Constants::SITUACAO_CHAPA_CONCLUIDA,
                $idTipoAlteracao
            );
        }
    }

    /**
     * Valida se chapa pode ser concluida
     * - Proporção de membros para conselheiros estuduais preenchidos
     * - Todos os membros terem aceitos o convite
     *
     * @param ChapaEleicao $chapaEleicao
     * @return bool
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function verificarChapaPodeSerConcluida(ChapaEleicao $chapaEleicao)
    {
        $conclusaoOk = false;

        $chapaEleicao->definirStatusChapaVigente();

        if (
            $chapaEleicao->getIdEtapa() == Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA
            && !empty($chapaEleicao->getStatusChapaVigente())
            && $chapaEleicao->getStatusChapaVigente()->getId() == Constants::SITUACAO_CHAPA_PENDENTE
        ) {
            $proporcao = 2;
            $totalMembrosChapa = $this->getMembroChapaRepository()->totalMembrosChapa($chapaEleicao->getId());

            if ($chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR) {
                $numeroProporcao = $this->getProporcaoConselheiroExtratoBO()->getProporcaoConselheirosPorAtividadeEIdCauUf(
                    $chapaEleicao->getAtividadeSecundariaCalendario()->getAtividadePrincipalCalendario()->getId(),
                    $chapaEleicao->getIdCauUf()
                );

                $proporcao += ($numeroProporcao * 2);
            }

            $totalConvitesConfirmados = $this->getMembroChapaRepository()->totalConvitesChapaPorStatusParticipacao(
                $chapaEleicao->getId(),
                Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
            );

            if ($totalMembrosChapa == $proporcao && $totalConvitesConfirmados == $proporcao) {
                $conclusaoOk = true;
            }
        }

        return $conclusaoOk;
    }

    /**
     * Atualiza Chapa Eleição forçando a validação dos membros e da chapa.
     *
     * @param integer $idChapaEleicao
     *
     * @return ChapaEleicao|null
     * @throws Exception
     */
    public function atualizarChapa($idChapaEleicao)
    {
        $chapaEleicao = $this->chapaEleicaoRepository->getPorId($idChapaEleicao);

        if (empty($chapaEleicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $atividadeSecundaria = $chapaEleicao->getAtividadeSecundariaCalendario();
        $isPeriodoVigente = (
            Utils::getDataHoraZero($atividadeSecundaria->getDataInicio()) <= Utils::getDataHoraZero()
            && Utils::getDataHoraZero($atividadeSecundaria->getDataFim()) >= Utils::getDataHoraZero()
        );

        if ($isPeriodoVigente) {
            try {
                $this->beginTransaction();

                $this->getMembroChapaBO()->atualizarPendenciasMembro($idChapaEleicao);

                $this->commitTransaction();
            } catch (Exception $e) {
                $this->rollbackTransaction();
                throw $e;
            }
        }

        return $this->getPorId($idChapaEleicao, true);
    }

    /**
     * Retorna as chapas que houveram calendários eleitorais
     *
     * @param $idCalendario
     * @param bool $isAddQtdChapaConcluida
     * @return array
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getChapasPorCalendario($idCalendario, $isAddQtdChapaConcluida = true)
    {
        $chapasEleicao = $this->chapaEleicaoRepository->getQtdChapasCalendario($idCalendario, $isAddQtdChapaConcluida);

        $calendario = $this->getCalendarioBO()->getPorId($idCalendario);

        $ufsCalendario = $this->getUfCalendarioBO()->getUfsCalendario($idCalendario);
        $ufsFiliais = $this->getUfsChapasChaveValorComPrefixo($ufsCalendario);

        if ($calendario->isSituacaoIES()) {
            $ufsFiliais[Constants::COMISSAO_MEMBRO_CAU_BR_ID]['descricao'] = Constants::PREFIXO_IES;
            $ufsFiliais[Constants::COMISSAO_MEMBRO_CAU_BR_ID]['prefixo'] = Constants::PREFIXO_IES;
        }

        $chapasEleicaoFormatadas = $this->getChapasEleicaoIesFormatada($chapasEleicao);
        return $this->getListaQuantidadeChapasEstadoTO($ufsFiliais, $chapasEleicaoFormatadas, $isAddQtdChapaConcluida);
    }

    /**
     * Retorna quantitativo de chapas eleição para cada uf da eleição atual
     * com verificação para membro comissão CEN.
     *
     * @return array
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getQuantidadeChapasPorMembroComissao()
    {
        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Lang::get('messages.permissao.visualizacao_membro_comissao'));
        }

        $eleicoes = $this->getEleicaoBO()->getEleicoesVigenteComCalendario();

        $eleicaoAtual = null;
        if (empty($eleicoes)) {
            throw new NegocioException(Lang::get('messages.eleicao.periodo_fechado'));
        }

        $membroComissao = null;
        foreach ($eleicoes as $eleicao) {
            $membroComissao = $this->getMembroComissaoBO()->getMembroComissaoParaVerificacaoPorCalendario(
                $eleicao->getCalendario()->getId()
            );

            if (!empty($membroComissao)) {
                $eleicaoAtual = $eleicao;
                break;
            }
        }

        if (empty($membroComissao)) {
            throw new NegocioException(Lang::get('messages.permissao.visualizacao_membro_comissao'));
        }

        if ($membroComissao->getIdCauUf() != Constants::ID_CAU_BR) {
            throw new NegocioException(Message::MSG_VISUALIZACAO_PEDIDOS_APENAS_MEMBROS_COMISSAO_CEN_BR);
        }

        return $this->getChapasPorCalendario($eleicaoAtual->getCalendario()->getId(), false);
    }

    /**
     * Retorna chapa da eleição que o ator logado por realizar substituição de membros.
     *
     * @return ChapaEleicao|null
     * @throws NegocioException
     * @throws Exception
     */
    public function getChapaParaSubstituicao()
    {
        $eleicaoTO = $this->getEleicaoVigenteSubstituicaoMembroChapa();

        if (empty($eleicaoTO)) {
            throw new NegocioException(Message::MSG_ATIV_SELECIONADA_SEM_VIGENCIA);
        }

        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        $chapaEleicao = $this->chapaEleicaoRepository->getChapaEleicaoPorCalendarioEResponsavel(
            $eleicaoTO->getCalendario()->getId(),
            $usuarioLogado->idProfissional
        );

        if (empty($chapaEleicao)) {
            throw new NegocioException(Message::MSG_VISUALIZACAO_ATIV_APENAS_MEMBROS_RESPONSAVEIS_CHAPA);
        }

        $chapaEleicao->definirStatusChapaVigente();
        $chapaEleicao->setChapaEleicaoStatus(null);

        return $chapaEleicao;
    }

    /**
     * Retorna as chapas que houveram calendários eleitorais de um estado específico.
     *
     * @param integer|null $idCalendario
     * @param integer|null $idCauUf
     *
     * @return array
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getChapasQuantidadeMembrosPorCalendarioCauUf($idCalendario, $idCauUf)
    {
        $tipoCandidatura = Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR;

        $quantidadeTotalMembrosChapa = 2;

        if ($idCauUf === 0) {
            $idCauUf = null;
            $tipoCandidatura = Constants::TIPO_CANDIDATURA_IES;
        } else {
            $numeroProporcao = $this->getProporcaoConselheiroExtratoBO()->getProporcaoConselheirosPorCalendarioEIdCauUf(
                $idCalendario,
                $idCauUf
            );

            $quantidadeTotalMembrosChapa += ($numeroProporcao * 2);
        }

        $membrosChapas = $this->getMembroChapaRepository()->getMembrosResponsaveisChapasCalendarioCauUf(
            $idCalendario,
            $tipoCandidatura,
            $idCauUf
        );

        $qtdMembrosChapas = $this->chapaEleicaoRepository->getQuantidadeMembrosChapasCalendarioCauUf(
            $idCalendario,
            $tipoCandidatura,
            $idCauUf
        );

        $ufsFiliais = $this->getUfsChapasChaveValor();

        foreach ($qtdMembrosChapas as $index => $chapa) {
            $chapaEleicao = $this->chapaEleicaoRepository->getPorId($chapa['idChapaEleicao']);
            $chapaEleicao->definirChapaEleicaoStatusVigente();
            $qtdMembrosChapas[$index]['idStatusChapa'] = $chapaEleicao->getStatusChapaVigente()->getStatusChapa()->getId();
            $qtdMembrosChapas[$index]['dataStatusChapa'] = $chapaEleicao->getStatusChapaVigente()->getData();
        }

        $membrosResponsaveisChapa = $this->getNomesMembrosChapa($membrosChapas);
        $chapasQuantidadeMembros = $this->getChapasComQuantidadeMembrosResponsaveisUf(
            $qtdMembrosChapas,
            $membrosResponsaveisChapa,
            $ufsFiliais,
            $quantidadeTotalMembrosChapa
        );

        return $this->getListaChapaQuantidadeMembrosTO($chapasQuantidadeMembros);
    }

    /**
     * Retorna as chapas cadastradas dado um id de calendário e uf.
     *
     * @param int|null $idCalendario
     * @param int|null $idCauUf
     * @return ChapaEleicaoTO[]|null
     */
    public function getChapasEleicaoJulgamentoFinalPorCalendarioCauUf($idCalendario, $idCauUf)
    {
        $isAssessorCEN = $this->getUsuarioFactory()->isCorporativoAssessorCEN();
        $isAssessorCE = $this->getUsuarioFactory()->isCorporativoAssessorCEUF();

        if ($this->getUsuarioFactory()->isCorporativo() && !($isAssessorCEN || $isAssessorCE)) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }

        if ($isAssessorCE && !$isAssessorCEN && $idCauUf != $this->getUsuarioFactory()->getUsuarioLogado()->idCauUf) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }

        $tipoCandidatura = Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR;
        if ($idCauUf === 0) {
            $idCauUf = null;
            $tipoCandidatura = Constants::TIPO_CANDIDATURA_IES;
        }

        $chapasEleicao = $this->chapaEleicaoRepository->getChapasEleicaoPorCalendarioTpCandidaturaAndCauUf(
            $idCalendario, $tipoCandidatura, false, $idCauUf
        );

        return $this->converterChapaJulgamentoFinalParaChapaEleicaoTO($chapasEleicao);
    }

    /**
     * Rertorna as chapas da eleição que possuem julgamento final com verificação para membro da comissão
     *
     * @param null $idCauUf
     * @return ChapaEleicaoTO[]|array
     * @throws NegocioException
     */
    public function getChapasEleicaoJulgamentoFinalPorMembroComissao($idCauUf = null)
    {
        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Lang::get('messages.permissao.visualizacao_membro_comissao'));
        }

        $eleicoes = $this->getEleicaoBO()->getEleicoesVigenteComCalendario();

        $eleicaoAtual = null;
        if (empty($eleicoes)) {
            throw new NegocioException(Lang::get('messages.eleicao.periodo_fechado'));
        }

        $membroComissao = null;
        foreach ($eleicoes as $eleicao) {
            $membroComissao = $this->getMembroComissaoBO()->getMembroComissaoParaVerificacaoPorCalendario(
                $eleicao->getCalendario()->getId()
            );

            if (!empty($membroComissao)) {
                $eleicaoAtual = $eleicao;
                break;
            }
        }

        if (empty($membroComissao)) {
            throw new NegocioException(Lang::get('messages.permissao.visualizacao_membro_comissao'));
        }

        $isMembroComissaoCEN = $membroComissao->getIdCauUf() == Constants::ID_CAU_BR;

        if (!$isMembroComissaoCEN && !empty($idCauUf) && $idCauUf != $membroComissao->getIdCauUf()) {
            throw new NegocioException(Lang::get('messages.permissao.visualizacao_membro_comissao'));
        }

        if (!$isMembroComissaoCEN) {
            $idCauUf = $membroComissao->getIdCauUf();
        }
        $isIES = $idCauUf == Constants::ID_CAU_BR || $idCauUf == 0;

        $chapasEleicao = $this->chapaEleicaoRepository->getChapasEleicaoPorCalendarioTpCandidaturaAndCauUf(
            $eleicaoAtual->getCalendario()->getId(),
            $isIES ? Constants::TIPO_CANDIDATURA_IES : Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR,
            false,
            $isIES ? null : $idCauUf
        );

        return $this->converterChapaJulgamentoFinalParaChapaEleicaoTO($chapasEleicao);
    }

    /**
     * Retorna a chapa eleição para julgamento final do responsável da chapa que está autenticado.
     * @return ChapaEleicaoTO
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getChapaEleicaoJulgamentoFinalPorResponsavelChapa()
    {
        $eleicao = $this->getEleicaoAtual(true, true);
        $idProfissional = $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional;

        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Lang::get('messages.permissao.visualizacao_responsaveis_chapa_convite_aceito'));
        }

        $idChapaEleicao = $this->getIdChapaEleicaoPorCalendarioEResponsavel(
            $eleicao->getCalendario()->getId(), $idProfissional
        );

        if (empty($idChapaEleicao)) {
            throw new NegocioException(Lang::get('messages.permissao.visualizacao_responsaveis_chapa_convite_aceito'));
        }

        $atividadeJulgamento = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao(
            $idChapaEleicao, 5, 1
        );
        if (
            empty($atividadeJulgamento)
            || Utils::getDataHoraZero() <= Utils::getDataHoraZero($atividadeJulgamento->getDataFim())
        ) {
            throw new NegocioException(Lang::get('messages.julgamento_final.aguardar_finalizar_julgamento'));
        }

        return $this->getChapaEleicaoJulgamentoFinalPorIdChapa($idChapaEleicao, $eleicao, true);
    }

    /**
     * Retorna a chapa eleição para julgamento final do responsável da chapa que está autenticado.
     * @return ChapaEleicaoTO
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getChapaJulgFinalVerificacaoMembroComissaoPorIdChapa($idChapa)
    {
        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Lang::get('messages.permissao.visualizacao_membro_comissao'));
        }

        $eleicoes = $this->getEleicaoBO()->getEleicoesVigenteComCalendario(true);

        $eleicaoAtual = null;
        if (empty($eleicoes)) {
            throw new NegocioException(Lang::get('messages.eleicao.periodo_fechado'));
        }

        $membroComissao = null;
        foreach ($eleicoes as $eleicao) {
            $membroComissao = $this->getMembroComissaoBO()->getMembroComissaoParaVerificacaoPorCalendario(
                $eleicao->getCalendario()->getId()
            );

            if (!empty($membroComissao)) {
                $eleicaoAtual = $eleicao;
                break;
            }
        }

        if (empty($membroComissao)) {
            throw new NegocioException(Lang::get('messages.permissao.visualizacao_membro_comissao'));
        }

        $chapaEleicaoTO = $this->getChapaEleicaoJulgamentoFinalPorIdChapa($idChapa, $eleicaoAtual, false, true);

        $isMembroComissaoCEN = $membroComissao->getIdCauUf() == Constants::ID_CAU_BR;
        $isIES = $chapaEleicaoTO->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;
        if (!$isMembroComissaoCEN && ($chapaEleicaoTO->getIdCauUf() != $membroComissao->getIdCauUf() || $isIES)) {
            throw new NegocioException(Lang::get('messages.permissao.visualizacao_membro_comissao'));
        }

        return $chapaEleicaoTO;
    }

    /**
     * Retorna a chapa eleição para julgamento final de acordo com id da chapa da eleição.
     *
     * @param $idChapa
     * @param EleicaoTO|null $eleicao
     * @return ChapaEleicaoTO
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getChapaEleicaoJulgamentoFinalPorIdChapa(
        $idChapa,
        $eleicao = null,
        $isResponsalvelChapa = false,
        $isMembroComissao = false
    )
    {
        /** @var ChapaEleicao $chapaEleicao */
        $chapaEleicao = $this->chapaEleicaoRepository->getPorId($idChapa, true, true, true);

        if (empty($chapaEleicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $chapaELeicaoTO = ChapaEleicaoTO::newInstanceFromEntityByJulgamentoFinal($chapaEleicao);

        $julgamentoFinal = $this->getJulgamentoFinalBO()->findByIdChapa($chapaELeicaoTO->getId());

        if (!$isMembroComissao) {
            if (empty($julgamentoFinal) && $isResponsalvelChapa) {
                throw new NegocioException(Lang::get('messages.julgamento_final.sem_julgamento_responsavel_chapa'));
            } elseif (empty($julgamentoFinal) && $this->getUsuarioFactory()->isProfissional()) {
                throw new NegocioException(Lang::get('messages.julgamento_final.aguardar_finalizar_julgamento'));
            }
        }

        $this->atribuirInformacoesComplementarParaJulgamento($chapaELeicaoTO, $eleicao, $julgamentoFinal);

        return $chapaELeicaoTO;
    }

    /**
     * @param ChapaEleicaoTO $chapaELeicaoTO
     * @param EleicaoTO|null $eleicao
     * @param JulgamentoFinal|null $julgamentoFinal
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function atribuirInformacoesComplementarParaJulgamento($chapaELeicaoTO, $eleicao, $julgamentoFinal)
    {
        if (empty($eleicao)) {
            $eleicao = $this->getEleicaoBO()->getEleicaoPorChapaEleicao($chapaELeicaoTO->getId(), true);
        }

        $numeroProporcao = 0;
        if ($chapaELeicaoTO->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR) {
            $numeroProporcao = $this->getProporcaoConselheiroExtratoBO()->getProporcaoConselheirosPorCalendarioEIdCauUf(
                $eleicao->getCalendario()->getId(),
                $chapaELeicaoTO->getIdCauUf()
            );
        }
        $chapaELeicaoTO->setNumeroProporcaoConselheiros($numeroProporcao);

        $chapaELeicaoTO->inicializarFlags();

        /** @var AtividadePrincipalCalendarioTO $atividadePrincipalTO */
        foreach ($eleicao->getCalendario()->getAtividadesPrincipais() as $atividadePrincipalTO) {

            /** @var AtividadeSecundariaCalendarioTO $atividadeSecundariaTO */
            foreach ($atividadePrincipalTO->getAtividadesSecundarias() as $atividadeSecundariaTO) {
                $dataInicio = Utils::getDataHoraZero($atividadeSecundariaTO->getDataInicio());
                $isIniciadoAtividade = Utils::getDataHoraZero() >= $dataInicio;

                $dataFim = Utils::getDataHoraZero($atividadeSecundariaTO->getDataFim());
                $isFinalizadoAtividade = Utils::getDataHoraZero() > $dataFim;

                if ($atividadePrincipalTO->getNivel() == 5 && $atividadeSecundariaTO->getNivel() == 1) {
                    $chapaELeicaoTO->setIsIniciadoAtivJulgFinal($isIniciadoAtividade);
                    $chapaELeicaoTO->setIsFinalizadoAtivJulgFinal($isFinalizadoAtividade);
                }

                if ($atividadePrincipalTO->getNivel() == 5 && $atividadeSecundariaTO->getNivel() == 2) {
                    $chapaELeicaoTO->setIsIniciadoAtivRecursoJulgamentoFinal($isIniciadoAtividade);
                    $chapaELeicaoTO->setIsFinalizadoAtivRecursoJulgamentoFinal($isFinalizadoAtividade);
                }

                if ($atividadePrincipalTO->getNivel() == 5 && $atividadeSecundariaTO->getNivel() == 3) {
                    $chapaELeicaoTO->setIsIniciadoAtivSubstituicaoJulgFinal($isIniciadoAtividade);
                    $chapaELeicaoTO->setIsFinalizadoAtivSubstituicaoJulgFinal($isFinalizadoAtividade);
                }

                if ($atividadePrincipalTO->getNivel()
                    == Constants::ATIVIDADE_PRIMARIA_JULGAMENTO_FINAL_SEGUNDA_INSTANCIA &&
                    $atividadeSecundariaTO->getNivel()
                    == Constants::ATIVIDADE_SECUNDARIA_JULGAMENTO_FINAL_SEGUNDA_INSTANCIA) {
                    $chapaELeicaoTO->setIsIniciadoAtivJulgSegundaInstancia($isIniciadoAtividade);
                    $chapaELeicaoTO->setIsFinalizadoAtivJulgSegundaInstancia($isFinalizadoAtividade);
                }
            }
        }

        if (!empty($julgamentoFinal)) {
            $chapaELeicaoTO->setIsCadastradoJulgamentoFinal(true);

            $ultimoJulgamentoTO = $this->getJulgamentoFinalBO()->getUltimoJulgamentoPorChapa($chapaELeicaoTO->getId());

            $chapaELeicaoTO->setIsJulgamentoFinalIndeferido(
                !empty($ultimoJulgamentoTO) &&
                $ultimoJulgamentoTO->getStatusJulgamentoFinal()->getId() == Constants::STATUS_JULG_FINAL_INDEFERIDO
            );

            $recurso = $this->getRecursoJulgamentoFinalBO()->findPorChapaEleicao($chapaELeicaoTO->getId());
            if (!empty($recurso)) {
                $chapaELeicaoTO->setIsCadastradoRecursoJulgamentoFinal(true);
            }

            $hasSubstituicaoPorChapa = $this->getSubstituicaoJulgamentoFinalBO()->hasSubstituicaoPorChapa(
                $chapaELeicaoTO->getId()
            );
            if ($hasSubstituicaoPorChapa) {
                $chapaELeicaoTO->setIsCadastradoSubstituicaoJulgamentoFinal(true);
            }
        }
    }

    /**
     * Retorna as UFs das chapas em formato Chave/Valor com uf já formatado.
     *
     *
     * @return array
     * @throws NegocioException
     */
    private function getUfsChapasChaveValor(): array
    {
        $filiais = $this->getFilialBO()->getFiliais();

        $filiaisChaveValor = $this->getUfsFiliaisChaveValor($filiais);

        return $filiaisChaveValor;
    }

    /**
     * Retorna as UFs das filiais em formato Chave/Valor.
     *
     * @param array $filiais
     *
     * @return array
     */
    private function getUfsFiliaisChaveValor($filiais): array
    {
        $filiaisChaveValor = [];

        /** @var Filial $filial */
        foreach ($filiais as $filial) {
            $filiaisChaveValor[$filial->getId()] = $filial->getPrefixo();
        }

        asort($filiaisChaveValor);

        return $filiaisChaveValor;
    }

    /**
     * Retorna todas as chapas de um calendário e cau uf por um determinado status.
     *
     * @param ChapaEleicaoExtratoFiltroTO $chapaEleicaoExtratoFiltroTO
     *
     * @return array
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getChapasParaExtratoPorCalendarioCauUf(ChapaEleicaoExtratoFiltroTO $chapaEleicaoExtratoFiltroTO, bool $isRetornaFoto = true)
    {
        /** Se o filtro idCauUF for igual a zero, o filtro de tipo candidatura igual IES deve ser aplicado. */
        $idCauUf = $chapaEleicaoExtratoFiltroTO->getIdCauUf() != 0 ? $chapaEleicaoExtratoFiltroTO->getIdCauUf() : null;
        $idTipoCandidatura = empty($idCauUf) ? Constants::TIPO_CANDIDATURA_IES : Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR;
        $chapaEleicaoExtratoFiltroTO->setIdTipoCandidatura($idTipoCandidatura);

        $chapas = $this->chapaEleicaoRepository->getChapasExtratoPorFiltro($chapaEleicaoExtratoFiltroTO);

        $proporcao = 0;
        if (!empty($idCauUf)) {
            $proporcao = $this->getProporcaoConselheiroExtratoBO()->getProporcaoConselheirosPorCalendarioEIdCauUf(
                $chapaEleicaoExtratoFiltroTO->getIdCalendario(), $chapaEleicaoExtratoFiltroTO->getIdCauUf()
            );
        }

        if (!empty($chapas)) {

            /** @var ChapaEleicao $chapaEleicao */
            foreach ($chapas as $chapaEleicao) {
                $chapaEleicao->definirStatusChapaVigente();
                $filial = $chapaEleicao->getFilial();

                if (!empty($filial) && $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES) {
                    $filial->setDescricao(Constants::PREFIXO_IES);
                } elseif (!empty($filial) && $chapaEleicao->getTipoCandidatura()->getId() != Constants::TIPO_CANDIDATURA_IES) {
                    $filial->setDescricao(sprintf('%s/%s', Constants::PREFIXO_CONSELHO_ELEITORAL, $filial->getPrefixo()));
                }
                $chapaEleicao->setCauUf($filial);

                $membrosResponsaveis = [];
                $membrosFormatados = [];

                if (!empty($chapaEleicao->getMembrosChapa())) {

                    /** @var MembroChapa $membroChapa */
                    foreach ($chapaEleicao->getMembrosChapa() as $membroChapa) {

                        if (Str::contains($membroChapa->getSinteseCurriculo(), '<img')) {
                            $curriculo = $membroChapa->getSinteseCurriculo();
                            $curriculo = preg_replace("/<img[^>]+\>/i", "", $curriculo);

                            /** @var MembroChapa $membro */
                            $membro = $this->getMembroChapaRepository()->find($membroChapa->getId());
                            $membro->setSinteseCurriculo($curriculo);
                            $this->getMembroChapaRepository()->persist($membro);
                            $membroChapa = $membro;
                        }

                        if ($membroChapa->isSituacaoResponsavel()) {
                            array_push($membrosResponsaveis, $membroChapa);
                        }

                        if ($isRetornaFoto) {
                            $this->getMembroChapaBO()->setArquivoFotoMembroChapa($membroChapa, $chapaEleicao->getId());
                        }

                        $ordem = $membroChapa->getNumeroOrdem();
                        $idTipoParticipacao = $membroChapa->getTipoParticipacaoChapa()->getId();
                        $membrosFormatados[$ordem][$idTipoParticipacao] = $membroChapa;
                    }
                    $chapaEleicao->setMembrosResponsaveis($membrosResponsaveis);
                }
                $chapaEleicao->setMembrosChapa($membrosFormatados);

                $descricaoPosicoesSemMembro = null;
                if (!empty($idCauUf)) {
                    $posicoesSemMembro = array_diff(range(1, $proporcao), array_keys($membrosFormatados));
                    $descricaoPosicoesSemMembro = join(' e ', array_filter(array_merge(array(join(', ', array_slice
                    ($posicoesSemMembro, 0, -1))), array_slice($posicoesSemMembro, -1)), 'strlen'));
                }

                $chapaEleicao->setDescricaoPosicoesSemMembros($descricaoPosicoesSemMembro);
            }
        }
        return ['chapasEleicao' => $chapas,
            'proporcaoConselheiros' => $proporcao];
    }

    /**
     * Retorna os nomes dos membros da chapa.
     *
     * @param $membrosChapas
     *
     * @return array
     * @throws NegocioException
     */
    private function getNomesMembrosChapa($membrosChapas): array
    {
        $idsProfissionais = array_map(function ($membroChapa) {
            return $membroChapa['idProfissional'];
        }, $membrosChapas);

        $profissionais = $this->getProfissionalBO()->getListaProfissionaisFormatadaPorIds($idsProfissionais);

        $chapasMembros = [];
        foreach ($membrosChapas as $membroChapa) {
            /** @var Profissional $profissional */
            $profissional = !empty($profissionais) && !empty($profissionais[$membroChapa['idProfissional']])
                ? $profissionais[$membroChapa['idProfissional']]
                : null;

            $chapasMembros[$membroChapa['idChapaEleicao']][] = $profissional ? $profissional->getNome() : null;
        }

        return $chapasMembros;
    }

    /**
     * Retorna as chapas com membros responsáveis.
     *
     * @param $quantidadeMembrosChapas
     * @param $membrosResponsaveisPorChapa
     *
     * @return array
     */
    private function getChapasComQuantidadeMembrosResponsaveisUf(
        $quantidadeMembrosChapas,
        $membrosResponsaveisPorChapa,
        $ufsFiliais,
        $quantidadeTotalMembrosChapa
    ): array
    {
        return array_map(static function ($chapaQuantidadeMembros) use (
            $membrosResponsaveisPorChapa,
            $ufsFiliais,
            $quantidadeTotalMembrosChapa
        ) {
            $chapaQuantidadeMembros['uf'] = $ufsFiliais[$chapaQuantidadeMembros['idCauUf']];
            $chapaQuantidadeMembros['quantidadeTotalMembrosChapa'] = $quantidadeTotalMembrosChapa;
            if (array_key_exists($chapaQuantidadeMembros['idChapaEleicao'], $membrosResponsaveisPorChapa)) {
                $chapaQuantidadeMembros['membrosResponsaveis'] = $membrosResponsaveisPorChapa[$chapaQuantidadeMembros['idChapaEleicao']];
            }
            return $chapaQuantidadeMembros;
        }, $quantidadeMembrosChapas);
    }

    /**
     * Retorna as chapas com quantidade de membros e seus responsáveis em formato de ChapaQuantidadeMembrosTO.
     *
     * @param array $chapasQuantidadeMembros
     * @return array
     */
    private function getListaChapaQuantidadeMembrosTO(array $chapasQuantidadeMembros): array
    {
        $chapaQuantidadeMembrosTO = array_map(static function ($chapaQuantidadeMembros) {
            return ChapaQuantidadeMembrosTO::newInstance($chapaQuantidadeMembros);
        }, $chapasQuantidadeMembros);

        return array_values(Arr::sort($chapaQuantidadeMembrosTO, function ($value) {
            /** @var ChapaQuantidadeMembrosTO $value */
            return $value->getUf();
        }));
    }


    /**
     * Exclui a ChapaEleicao fisicamente pelo Id
     *
     * @param int $idChapaEleicao
     *
     * @throws Exception
     */
    public function excluir($idChapaEleicao)
    {
        try {
            $this->beginTransaction();

            $chapaEleicao = $this->chapaEleicaoRepository->find($idChapaEleicao);

            if (!empty($chapaEleicao) and $chapaEleicao->getIdEtapa() !== Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA) {

                $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

                $isProfissionalInclusao = $chapaEleicao->getIdProfissionalInclusao() == $usuarioLogado->idProfissional;
                $isResponsavel = $this->getMembroChapaBO()->isMembroResponsavelChapa(
                    $idChapaEleicao,
                    $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
                );

                if ($isProfissionalInclusao or $isResponsavel) {
                    $this->chapaEleicaoRepository->delete($chapaEleicao);
                }
            }

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Exclui logicamente a ChapaEleicao pelo Id e salva histórico.
     *
     * @param int $idChapaEleicao
     * @param array $dados
     *
     * @throws NegocioException
     * @throws Exception
     */
    public function inativar(int $idChapaEleicao, $dados)
    {
        if (!isset($dados['justificativa']) or empty($dados['justificativa'])) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        try {
            $this->beginTransaction();

            $chapaEleicao = $this->chapaEleicaoRepository->find($idChapaEleicao);

            if (!empty($chapaEleicao) and $chapaEleicao->getIdEtapa() == Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA) {
                $chapaEleicao->setExcluido(true);
                $this->chapaEleicaoRepository->persist($chapaEleicao);

                $profissional = $this->getProfissionalBO()->getPorId(
                    $chapaEleicao->getIdProfissionalInclusao()
                );

                $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
                    $chapaEleicao,
                    Constants::ORIGEM_CORPORATIVO,
                    sprintf(self::MSG_HISTORICO_EXCLUSAO_CHAPA_RESPONSAVEL_CPF,
                        Utils::getCpfFormatado($profissional->getCpf())),
                    $dados['justificativa']
                );

                $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);
            }

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Recupera o histórico referente ao 'id' do calendario informado.
     *
     * @param int $idCalendario
     *
     * @return HistoricoChapaEleicao[]
     * @throws NegocioException
     */
    public function getHistorico(int $idCalendario)
    {
        return $this->getHistoricoChapaEleicaoBO()->getHistoricoPorCalendario($idCalendario);
    }

    /**
     * Valida se o idCauUf do profissional é o mesmo da chapa eleição dado um id de chapa eleição e o id do usuário
     * logado.
     *
     * @param int $idChapaEleicao
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function validarUfProfissionalConvidadoChapa(int $idChapaEleicao)
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        $chapaEleicao = $this->chapaEleicaoRepository->getPorId($idChapaEleicao);
        if (
            $chapaEleicao->getTipoCandidatura()->getId() !== Constants::TIPO_CANDIDATURA_IES
            and $chapaEleicao->getIdCauUf() !== $usuarioLogado->idCauUf
        ) {
            throw new NegocioException(Message::MSG_UF_CHAPA_DIFERENTE_PROFISSIONAL);
        }
    }

    /**
     * Valida Chapas da Eleição e Membros da Chapa dentro do período vigente da eleição
     *
     * @throws Exception
     */
    public function validarChapasEMembrosChapa()
    {
        $chapasVigentes = $this->chapaEleicaoRepository->getChapasConfirmadasPeriodoVigente();

        if (!empty($chapasVigentes)) {
            try {
                $this->beginTransaction();

                /** @var ChapaEleicao $chapaEleicao */
                foreach ($chapasVigentes as $chapaEleicao) {
                    $this->getMembroChapaBO()->atualizarPendenciasMembro($chapaEleicao->getId());
                }

                $this->commitTransaction();

            } catch (Exception $e) {
                $this->rollbackTransaction();
                throw $e;
            }
        }
    }

    /**
     * Exclui as Chapas Eleição que não foram confirmadas a criação após o fim da atividade secundária.
     *
     * @throws Exception
     */
    public function excluirChapasNaoConfirmadasForaPeriodoVigente()
    {
        $chapasNaoConfirmadas = $this->chapaEleicaoRepository->getChapasNaoConfirmadasForaPeriodoVigente();

        if (!empty($chapasNaoConfirmadas)) {
            try {
                $this->beginTransaction();

                /** @var ChapaEleicao $chapaEleicao */
                foreach ($chapasNaoConfirmadas as $chapaEleicao) {
                    $chapaEleicaoExcluir = $this->chapaEleicaoRepository->find($chapaEleicao->getId());
                    $this->chapaEleicaoRepository->delete($chapaEleicaoExcluir);
                }

                $this->commitTransaction();

            } catch (Exception $e) {
                $this->rollbackTransaction();
                throw $e;
            }
        }
    }

    /**
     * Método para envio de e-mails cinco dias antes do fim da atividade sencundária 2.1
     *
     * @throws Exception
     */
    public function enviarEmailCincoDiasAntesFimAtividadeSecundaria()
    {
        $dataFimAtividadeCincoDiasPosterior = Utils::adicionarDiasData(Utils::getDataHoraZero(), 5);
        $chapasEleicao = $this->chapaEleicaoRepository->getChapasPorDataFimAtividadeSecundaria(
            $dataFimAtividadeCincoDiasPosterior
        );

        foreach ($chapasEleicao as $chapaEleicao) {
            $this->enviarEmailsCincoDiasAntesFimParaResponsaveis($chapaEleicao);
            $this->enviarEmailCincoDiasAntesFimMembrosComPendencias($chapaEleicao);
        }
    }

    /**
     * Faz a validação prévia de tamanho e extensão.
     *
     * @param ArquivoRespostaDeclaracaoChapa $arquivoRespostaDeclaracaoChapa
     * @param $idDeclaracao
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function validarPreviamenteArquivoRespostaDeclaracao($arquivoRespostaDeclaracaoChapa, $idDeclaracao)
    {
        $declaracao = $this->getDeclaracaoBO()->getDeclaracao($idDeclaracao);

        $this->validarArquivoPorDeclaracao($arquivoRespostaDeclaracaoChapa, $declaracao);
    }

    /**
     * Retorna todas as UFs que possuem chapas vigentes
     */
    public function getUfsDeChapas()
    {
        $ufsChapa = $this->chapaEleicaoRepository->getUfsDeChapas();

        if (!empty($ufsChapa)) {
            $cauUfs = $this->getFilialBO()->getFiliais();
            $cauUfs[] = $this->getFilialBO()->getFilialIES();
            $ufsChapa = $this->organizeIdCauUfParaLista($cauUfs, $ufsChapa);
            $ufsChapa = !empty($ufsChapa) ? array_values($ufsChapa) : [];
        }
        return $ufsChapa;
    }

    /**
     * Retornando as chapas ativas por id cau uf
     *
     * @param int $idCauUf
     */
    public function getPorCauUf($idCauUf)
    {
        return $this->chapaEleicaoRepository->getPorCauUf($idCauUf);
    }

    /**
     * Responsável por salvar as arquivos de resposta da declaração de criação da chapa
     *
     * @param ChapaEleicao $chapaEleicao
     * @param $arquivosRespostaDeclaracaoChapa
     * @param Declaracao $declaracao
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function salvarArquivosDeclaracaoChapa($chapaEleicao, $arquivosRespostaDeclaracaoChapa, $declaracao)
    {
        $this->validarArquivosRespostaDeclaracaoChapa($arquivosRespostaDeclaracaoChapa, $declaracao);

        if (!empty($arquivosRespostaDeclaracaoChapa)) {
            /** @var ArquivoRespostaDeclaracaoChapa $arquivoRespostaDeclaracao */
            foreach ($arquivosRespostaDeclaracaoChapa as $arquivoRespostaDeclaracao) {
                $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                    $arquivoRespostaDeclaracao->getNome(),
                    Constants::PREFIXO_ARQ_RESPOSTA_DECLARACAO_CHAPA
                );

                $arquivoRespostaDeclaracao->setId(null);
                $arquivoRespostaDeclaracao->setChapaEleicao($chapaEleicao);
                $arquivoRespostaDeclaracao->setNomeFisico($nomeArquivoFisico);

                $this->getArquivoRespostaDeclaracaoChapaRepository()->persist($arquivoRespostaDeclaracao);

                $this->getArquivoService()->salvar(
                    $this->getArquivoService()->getCaminhoRepositorioRespDeclaracaoChapa($chapaEleicao->getId()),
                    $nomeArquivoFisico,
                    $arquivoRespostaDeclaracao->getArquivo()
                );
            }
        }
    }

    /**
     * @param bool $hasException
     * @param bool $isAddAtividades
     * @return EleicaoTO|null
     * @throws NegocioException
     */
    public function getEleicaoAtual($hasException = true, $isAddAtividades = false)
    {
        $eleicao = $this->getEleicaoBO()->getEleicaoVigenteComCalendario($isAddAtividades);

        if (empty($eleicao) && $hasException) {
            throw new NegocioException(Lang::get('messages.eleicao.periodo_fechado'));
        }
        return $eleicao;
    }

    /**
     * Converte chapa eleição entidade para to com as informações necessárias na tela
     * @param array|null $chapasEleicao
     * @return ChapaEleicaoTO[]|array
     */
    public function converterChapaJulgamentoFinalParaChapaEleicaoTO(?array $chapasEleicao)
    {
        $chapasEleicaoTO = [];
        if (!empty($chapasEleicao)) {
            $chapasEleicaoTO = array_map(function ($chapaEleicao) {
                return ChapaEleicaoTO::newInstanceFromEntityByListagemJulgamentoFinal($chapaEleicao);
            }, $chapasEleicao);
        }
        return $chapasEleicaoTO;
    }

    /**
     * Retorna as UFs das chapas em formato Chave/Valor com uf com prefixo.
     *
     * @param array $ufsCalendario
     *
     * @return array
     * @throws NegocioException
     */
    private function getUfsChapasChaveValorComPrefixo($ufsCalendario): array
    {
        $ufsChaveValor = $this->getUfsChapasChaveValor();

        $filiaisChaveValor = [];
        /** @var UfCalendario $ufCalendario */
        foreach ($ufsCalendario as $ufCalendario) {
            $idCauUf = $ufCalendario->getIdCauUf();

            if (array_key_exists($idCauUf, $ufsChaveValor) && $idCauUf !== Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                $filiaisChaveValor[$idCauUf]['descricao'] = sprintf('%s/%s', Constants::PREFIXO_CONSELHO_ELEITORAL,
                    $ufsChaveValor[$idCauUf]);
                $filiaisChaveValor[$idCauUf]['prefixo'] = $ufsChaveValor[$idCauUf];
            }
        }

        return array_sort($filiaisChaveValor);
    }

    /**
     * Retorna as chapas com chapa Ies formatada.
     *
     * @param array|null $chapasEleicao
     *
     * @return array
     */
    private function getChapasEleicaoIesFormatada(?array $chapasEleicao): array
    {
        $chapasEleicaoCandidaturaIes = array_filter($chapasEleicao, static function ($chapaEleicao) {
            return Constants::TIPO_CANDIDATURA_IES === $chapaEleicao['idTipoCandidatura'];
        });

        $chapasEleicaoCandidaturaConselheiros = array_filter($chapasEleicao, static function ($chapaEleicao) {
            return Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR === $chapaEleicao['idTipoCandidatura'];
        });

        $chapaEleicaoIes = [
            'idCauUf' => Constants::COMISSAO_MEMBRO_CAU_BR_ID,
            'idTipoCandidatura' => Constants::TIPO_CANDIDATURA_IES,
            'quantidadeTotalChapas' => array_sum(array_column($chapasEleicaoCandidaturaIes,
                'quantidadeTotalChapas')),
            'quantidadeChapasPendentes' => array_sum(array_column($chapasEleicaoCandidaturaIes,
                'quantidadeChapasPendentes')),
            'quantidadeChapasConcluidas' => array_sum(array_column($chapasEleicaoCandidaturaIes,
                'quantidadeChapasConcluidas'))
        ];

        return array_merge($chapasEleicaoCandidaturaConselheiros, [$chapaEleicaoIes]);
    }

    /**
     * Retorna as chapas de eleições em formato de QuantidadeChapasEstadoTO.
     *
     * @param array $ufsFiliais
     * @param array|null $chapasEleicao
     * @param $isAddQtdChapaConcluida
     * @return array
     */
    private function getListaQuantidadeChapasEstadoTO($ufsFiliais, $chapasEleicao, $isAddQtdChapaConcluida = true): array
    {
        $chapasPorEleicao = [];

        foreach ($ufsFiliais as $idFilial => $ufFilial) {
            /** @var ChapaEleicao $chapaEleicao */
            $chapaEleicao = array_first(array_filter($chapasEleicao, static function ($chapaEleicao) use ($idFilial) {
                return $chapaEleicao['idCauUf'] === $idFilial;
            }));
            $qtdTotal = $isAddQtdChapaConcluida ? ($chapaEleicao['quantidadeChapasConcluidas'] ?? 0) : null;

            array_push($chapasPorEleicao, QuantidadeChapasEstadoTO::newInstance([
                'uf' => $ufFilial['descricao'],
                'prefixoUf' => $ufFilial['prefixo'],
                'idCauUf' => Constants::COMISSAO_MEMBRO_CAU_BR_ID !== $idFilial ? $idFilial : 0,
                'quantidadeTotalChapas' => $chapaEleicao['quantidadeTotalChapas'] ?? 0,
                'quantidadeChapasPendentes' => $chapaEleicao['quantidadeChapasPendentes'] ?? 0,
                'quantidadeChapasConcluidas' => $qtdTotal
            ]));
        }

        return $chapasPorEleicao;
    }

    /**
     * Envia emails para os responsáveis cinco dias antes do fim da atividade secundaria
     * informando que a chapa não foi confirmada/criada e chapa
     *
     * @param ChapaEleicao $chapaEleicao
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailsCincoDiasAntesFimParaResponsaveis(ChapaEleicao $chapaEleicao)
    {
        $chapaEleicao->definirStatusChapaVigente();

        $responsaveis = $this->getMembroChapaBO()
            ->getMembrosResponsaveisChapa(
                $chapaEleicao->getId(),
                Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
            );

        $destinatariosEmail = $this->getMembroChapaBO()->getListEmailsDestinatarios($responsaveis);

        $destinatariosChapaNaoConfirmada = $destinatariosEmail;

        if ($chapaEleicao->getIdEtapa() != Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA) {
            if (empty($destinatariosChapaNaoConfirmada)) {
                $profissionalInclusao = $this->getProfissionalBO()->getPorId(
                    $chapaEleicao->getIdProfissionalInclusao()
                );

                $destinatariosChapaNaoConfirmada[] = $profissionalInclusao->getEmail();
            }

            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
                $chapaEleicao->getAtividadeSecundariaCalendario()->getId(),
                $destinatariosChapaNaoConfirmada,
                Constants::EMAIL_CHAPA_NAO_CONFIRMADA,
                Constants::TEMPLATE_EMAIL_PADRAO
            );
        }

        if (!empty($destinatariosEmail) &&
            $chapaEleicao->getStatusChapaVigente()->getId() == Constants::SITUACAO_CHAPA_PENDENTE) {

            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
                $chapaEleicao->getAtividadeSecundariaCalendario()->getId(),
                $destinatariosEmail,
                Constants::EMAIL_CHAPA_NAO_CONFIRMADA,
                Constants::TEMPLATE_EMAIL_PADRAO
            );
        }
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
     * Envia email para os membros da chapa com pendências, cinco dias antes do fim da atividade secundaria
     *
     * @param ChapaEleicao $chapaEleicao
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailCincoDiasAntesFimMembrosComPendencias(ChapaEleicao $chapaEleicao)
    {
        $membros = $this->getMembroChapaRepository()->getMembrosPorFiltro(
            MembroChapaFiltroTO::newInstance([
                'idChapaEleicao' => $chapaEleicao->getId()
            ])
        );

        if (!empty($membros)) {
            foreach ($membros as $membroChapa) {
                $this->getMembroChapaBO()->prepararParamsEmailPendenciasEfetivarEnvio($membroChapa);
            }
        }
    }

    /**
     * Retorna uma nova instância de 'DeclaracaoBO'.
     *
     * @return DeclaracaoBO|mixed
     */
    private function getDeclaracaoBO()
    {
        if (empty($this->declaracaoBO)) {
            $this->declaracaoBO = app()->make(DeclaracaoBO::class);
        }

        return $this->declaracaoBO;
    }

    /**
     * Responsável por enviar emails após a confirmação da chapa
     *
     * @param $idChapaEleicao
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailsChapaConfirmada($idChapaEleicao)
    {
        $chapaEleicao = $this->chapaEleicaoRepository->getPorId($idChapaEleicao);

        // enviar e-mail informativo para comissao eleitoral
        /*$this->getEmailAtividadeSecundariaBO()->enviarEmailConselheirosCoordenadoresComissao(
            $chapaEleicao->getAtividadeSecundariaCalendario()->getId(),
            Constants::EMAIL_CHAPA_CRIADA_CENBR_CEUF,
            Constants::TEMPLATE_EMAIL_PADRAO,
            $chapaEleicao->getIdCauUf()
        );*/

        // enviar e-mail informativo para membros incluidos
        $this->enviarEmailChapaCriadaParaMembrosIncluidos($chapaEleicao);
    }

    /**'
     * Responsável por enviar email para os membros incluidos na chapa informando que a chapa foi criada
     *
     * @param ChapaEleicao $chapaEleicao
     *
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function enviarEmailChapaCriadaParaMembrosIncluidos(ChapaEleicao $chapaEleicao)
    {
        $membros = $this->getMembroChapaRepository()->getMembrosPorFiltro(
            MembroChapaFiltroTO::newInstance([
                'idChapaEleicao' => $chapaEleicao->getId(),
                'idTipoParticipacaoChapa' => Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR,
                'incluirSuplenteConsulta' => true
            ])
        );

        $responsavelTO = $this->getProfissionalBO()->getPorId($chapaEleicao->getIdProfissionalInclusao());

        $anoEleicao = $this->chapaEleicaoRepository->getAnoEleicaoChapa($chapaEleicao->getId());

        $envioEmailMembroIncuidoChapaTO = EnvioEmailMembroIncuidoChapaTO::newInstance([
            'idAtividadeSecundaria' => $chapaEleicao->getAtividadeSecundariaCalendario()->getId(),
            'nomeResponsavel' => $responsavelTO->getNome(),
            'anoEleicao' => $anoEleicao
        ]);

        $responsaveisEmails = [];

        /** @var MembroChapa $membro */
        foreach ($membros as $membro) {
            $profissionalTitular = $membro->getProfissional();
            if ($membro->isSituacaoResponsavel()) {
                $responsaveisEmails[] = $profissionalTitular->getPessoa()->getEmail();
            }

            $profissionalSuplente = null;
            if (!empty($membro->getSuplente())) {
                $profissionalSuplente = $membro->getSuplente()->getProfissional();
                if ($membro->getSuplente()->isSituacaoResponsavel()) {
                    $responsaveisEmails[] = $profissionalSuplente->getPessoa()->getEmail();
                }
            }

            $descricoesTiposMembros = Constants::$descricoesTiposMembrosChapa[$membro->getTipoMembroChapa()->getId()];

            $envioEmailMembroIncuidoChapaTO->setPosicao($membro->getNumeroOrdem());
            $envioEmailMembroIncuidoChapaTO->setDescricaoTitular(
                $descricoesTiposMembros[Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR]
            );
            $envioEmailMembroIncuidoChapaTO->setDescricaoSuplente(
                $descricoesTiposMembros[Constants::TIPO_PARTICIPACAO_CHAPA_SUPLENTE]
            );
            $envioEmailMembroIncuidoChapaTO->setNomeTitular($profissionalTitular->getNome());
            $envioEmailMembroIncuidoChapaTO->setNomeSuplente(
                (!empty($profissionalSuplente)) ? $profissionalSuplente->getNome() : ''
            );
            $envioEmailMembroIncuidoChapaTO->setEmailDestinatario($profissionalTitular->getPessoa()->getEmail());
            $envioEmailMembroIncuidoChapaTO->setNomeMembro($profissionalTitular->getNome());

            $this->enviarEmailMembroIncluidoChapa($envioEmailMembroIncuidoChapaTO);

            if (!empty($profissionalSuplente)) {
                $envioEmailMembroIncuidoChapaTO->setNomeMembro($profissionalSuplente->getNome());
                $envioEmailMembroIncuidoChapaTO->setEmailDestinatario($profissionalSuplente->getPessoa()->getEmail());
                $envioEmailMembroIncuidoChapaTO->setPosicao($membro->getSuplente()->getNumeroOrdem());

                $this->enviarEmailMembroIncluidoChapa($envioEmailMembroIncuidoChapaTO);
            }
        }
        // enviar e-mail informativo para os responsáveis pela chapa
        $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
            $chapaEleicao->getAtividadeSecundariaCalendario()->getId(),
            $responsaveisEmails,
            Constants::EMAIL_CHAPA_CRIADA_RESPONSAVEIS,
            Constants::TEMPLATE_EMAIL_PADRAO
        );
    }

    /**
     * Responsável por enviar email para o membro incluido na chapa informando que a chapa foi criada
     *
     * @param EnvioEmailMembroIncuidoChapaTO $envioEmailMembroIncuidoChapaTO
     * @throws NonUniqueResultException
     */
    public function enviarEmailMembroIncluidoChapa(EnvioEmailMembroIncuidoChapaTO $envioEmailMembroIncuidoChapaTO)
    {
        $descricaoPosicaoFormatada = '';
        if (!empty($envioEmailMembroIncuidoChapaTO->getPosicao())) {
            $descricaoPosicaoFormatada = sprintf(
                Constants::DS_PARAMETRO_EMAIL_POSICAO_MEMBRO, $envioEmailMembroIncuidoChapaTO->getPosicao()
            );
        }

        $parametrosExtras = [
            Constants::PARAMETRO_EMAIL_POSICAO_MEMBRO => $descricaoPosicaoFormatada,
            Constants::PARAMETRO_EMAIL_NM_MEMBRO => $envioEmailMembroIncuidoChapaTO->getNomeMembro(),
            Constants::PARAMETRO_EMAIL_NM_TITULAR => $envioEmailMembroIncuidoChapaTO->getNomeTitular(),
            Constants::PARAMETRO_EMAIL_ANO_ELEICAO => $envioEmailMembroIncuidoChapaTO->getAnoEleicao(),
            Constants::PARAMETRO_EMAIL_NM_SUPLENTE => $envioEmailMembroIncuidoChapaTO->getNomeSuplente(),
            Constants::PARAMETRO_EMAIL_DS_TITULAR => $envioEmailMembroIncuidoChapaTO->getDescricaoTitular(),
            Constants::PARAMETRO_EMAIL_DS_SUPLENTE => $envioEmailMembroIncuidoChapaTO->getDescricaoSuplente(),
            Constants::PARAMETRO_EMAIL_NM_RESPONSAVEL => $envioEmailMembroIncuidoChapaTO->getNomeResponsavel(),
        ];

        $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
            $envioEmailMembroIncuidoChapaTO->getIdAtividadeSecundaria(),
            [$envioEmailMembroIncuidoChapaTO->getEmailDestinatario()],
            Constants::EMAIL_MEMBRO_INCLUIDO_CHAPA,
            Constants::TEMPLATE_EMAIL_MEMBRO_INCLUIDO_CHAPA,
            $parametrosExtras
        );
    }

    /**
     * Gerar Extrato de quantidades de chapas da eleição.
     *
     * @param int $idCalendario
     * @return ArquivoTO
     * @throws NegocioException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function gerarDocumentoPDFExtratoQuantidadeChapa(int $idCalendario)
    {
        $listaQuantidadeChapasEstadoTO = $this->getChapasPorCalendario($idCalendario);
        return $this->getPdfFactory()->gerarDocumentoPDFExtratoQuantidadeChapa($listaQuantidadeChapasEstadoTO);
    }

    public function gerarXMLChapas($idCalendario, $statusChapaJulgamentoFinal)
    {
        $registros = $this->chapaEleicaoRepository->getxportarXMLorCSV([
            'calendario' => $idCalendario,
            'statusChapaJulgamentoFinal' => $statusChapaJulgamentoFinal
        ]);

        if (empty($registros)) {
            throw new \Exception('Nenhum dado cadastrado');
        }

        return $this->getExportarChapaXMLBO()->exportar($registros);
    }

    public function gerarCSVChapas($idCalendario, $statusChapaJulgamentoFinal)
    {
        $registros = $this->chapaEleicaoRepository->getxportarXMLorCSV([
            'calendario' => $idCalendario,
            'statusChapaJulgamentoFinal' => $statusChapaJulgamentoFinal
        ]);

        if (empty($registros)) {
            throw new \Exception('Nenhum dado cadastrado');
        }

        return $this->getExportarChapaCSVBO()->exportar($registros);
    }

    /**
     * Gera um CSV com as chapas por UF
     *
     * @param $idCalendario
     * @param $idCauUf
     * @return ArquivoTO
     * @throws Exception
     */
    public function gerarCSVChapasPorUf($idCalendario, $idCauUf)
    {
        $idCauUf != 0 ? $idCauUf : null;
        $registros = $this->chapaEleicaoRepository->getChapasPorUf($idCalendario, $idCauUf);

        if (empty($registros)) {
            throw new \Exception('Nenhum dado cadastrado');
        }

        return $this->getExportarChapaCSVBO()->exportarPorUf($registros, $idCauUf);
    }

    /**
     * Retorna uma nova instância de 'CalendarioBO'.
     *
     * @return CalendarioBO|mixed
     */
    private function getCalendarioBO()
    {
        if (empty($this->calendarioBO)) {
            $this->calendarioBO = app()->make(CalendarioBO::class);
        }

        return $this->calendarioBO;
    }

    /**
     * Retorna uma nova instância de 'MembroComissaoBO'.
     *
     * @return MembroComissaoBO|mixed
     */
    private function getMembroComissaoBO()
    {
        if (empty($this->membroComissaoBO)) {
            $this->membroComissaoBO = app()->make(MembroComissaoBO::class);
        }

        return $this->membroComissaoBO;
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
     * Retorna a instância de PDFFactory conforme o padrão Lazy Initialization.
     *
     * @return PDFFActory
     */
    private function getPdfFactory()
    {
        if (empty($this->pdfFactory)) {
            $this->pdfFactory = app()->make(PDFFActory::class);
        }

        return $this->pdfFactory;
    }

    /**
     * Gerar Extrato de chapas da eleição.
     *
     * @param ChapaEleicaoExtratoFiltroTO $chapaEleicaoExtratoFiltroTO
     * @return Response
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function gerarDocumentoPDFExtratoChapa(ChapaEleicaoExtratoFiltroTO $chapaEleicaoExtratoFiltroTO)
    {
        $listaChapasExtrato = $this->getChapasParaExtratoPorCalendarioCauUf($chapaEleicaoExtratoFiltroTO);
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        return $this->getPdfFactory()->gerarDocumentoPDFExtratoChapa($listaChapasExtrato, $usuarioLogado);
    }

    /**
     * Gera o Extrato de chapas em Json.
     *
     * @param ChapaEleicaoExtratoFiltroTO $chapaEleicaoExtratoFiltroTO
     * @return array
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function gerarDadosExtratoChapaJson(ChapaEleicaoExtratoFiltroTO $chapaEleicaoExtratoFiltroTO)
    {
        $listaChapasExtrato = $this->getChapasParaExtratoPorCalendarioCauUf($chapaEleicaoExtratoFiltroTO);
        $listaChapasExtrato['idCauUf'] = $chapaEleicaoExtratoFiltroTO->getIdCauUf();
        $listaChapasExtrato['idCalendario'] = $chapaEleicaoExtratoFiltroTO->getIdCalendario();

        $jsonChapaExtrato = JsonUtils::toJson($listaChapasExtrato);
        $this->getArquivoService()->salvaArquivoExtratoChapaJson($jsonChapaExtrato, $chapaEleicaoExtratoFiltroTO);

        return $listaChapasExtrato;
    }

    /**
     * Gerar Extrato de chapas para todos UF.
     *
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function gerarDadosExtratoChapaJsonParaTodosCauUf()
    {
        /**
         * @var Calendario[] $calendarios
         * @var Calendario $calendario
         * @var UfCalendario $cauUf
         */

        $calendarios = $this->getCalendarioBO()->getCalendariosVigentes();

        foreach ($calendarios as $calendario) {
            $calendario = $this->getCalendarioBO()->getPorId($calendario->getId());
            foreach ($calendario->getCauUf() as $cauUf) {
                $filtroTo = ChapaEleicaoExtratoFiltroTO::newInstance([
                    'idCalendario' => $calendario->getId(),
                    'idCauUf' => $cauUf->getIdCauUf() == Constants::UF_CAU_BR ? '0' : $cauUf->getIdCauUf(),
                    'idStatus' => Constants::STATUS_EXTRATO_CHAPA_CONCLUIDA
                ]);
                $this->gerarDadosExtratoChapaJson($filtroTo);
            }
        }

    }

    /**
     * Gera o Extrato de chapas em Json.
     *
     * @param int $idCauUf
     * @return string
     * @throws Exception
     */
    public function getDadosExtratoChapaJson(int $idCauUf)
    {

        $eleicao = $this->getEleicaoBO()->getEleicoesVigenteComCalendarioPorUf($idCauUf);
        $chapaEleicaoExtratoFiltroTO = ChapaEleicaoExtratoFiltroTO::newInstance(['idCauUf' => $idCauUf, 'idCalendario' => $eleicao->getCalendario()->getId()]);

        $json = $this->getArquivoService()->getArquivoExtratoChapaJson($chapaEleicaoExtratoFiltroTO);

        return $json;
    }

    /**
     * Salva o número para a chapa informada
     *
     * @param ChapaEleicaoTO $chapaEleicaoTO
     * @return ChapaEleicaoTO
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarNumeroChapa(ChapaEleicaoTO $chapaEleicaoTO)
    {
        if (empty($chapaEleicaoTO->getId()) || empty($chapaEleicaoTO->getNumeroChapa())) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        //Busca a Chapa de acordo com o Id
        /** @var  $chapaEleicao  ChapaEleicao */
        $chapaEleicao = $this->chapaEleicaoRepository->find($chapaEleicaoTO->getId());

        if (empty($chapaEleicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        /**
         * Realiza validação de número já informado.
         * Caso a chapa seja do tipo UFBR serão considerados apenas chapas do mesmo UF.
         * Caso a chapa sejá do tipo IES serão considerados todas as UF da eleição.
         */
        $msgNumeroJaCadastrado = Message::MSG_NUMERO_CHAPA_JA_CADASTRADO_PARA_UF;
        if ($chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR) {
            $qtdChapasComNumero = $this->chapaEleicaoRepository->getCountChapaEleicaoPorNumeroChapaAndCauUf($chapaEleicao, $chapaEleicaoTO->getNumeroChapa());
        } else {
            $qtdChapasComNumero = $this->chapaEleicaoRepository->getCountChapaEleicaoPorNumeroChapa($chapaEleicao, $chapaEleicaoTO->getNumeroChapa());
            $msgNumeroJaCadastrado = Message::MSG_NUMERO_CHAPA_JA_CADASTRADO;
        }

        //Caso já exista com o número informado
        if ($qtdChapasComNumero > 0) {
            throw new NegocioException($msgNumeroJaCadastrado);
        }

        //Seta o número da chapa
        $chapaEleicao->setNumeroChapa($chapaEleicaoTO->getNumeroChapa());
        $this->chapaEleicaoRepository->persist($chapaEleicao);

        return ChapaEleicaoTO::newInstanceFromEntity($chapaEleicao);
    }

    /**
     * Retorna a chapa em que o usuario logado e responsavel
     *
     * @param int $idCalendario
     * @param int $idProfissionalResponsavel
     * @return ChapaEleicao
     * @throws Exception
     */
    public function getChapaEleicaoPorCalendarioEResponsavel($idCalendario, $idProfissionalResponsavel): ?ChapaEleicao
    {
        return $this->chapaEleicaoRepository->getChapaEleicaoPorCalendarioEResponsavel(
            $idCalendario,
            $idProfissionalResponsavel);
    }

    /**
     * Retorna a chapa em que o usuario logado e responsavel
     *
     * @param int $idCalendario
     * @param int $idProfissionalResponsavel
     * @return integer
     * @throws Exception
     */
    public function getIdChapaEleicaoPorCalendarioEResponsavel($idCalendario, $idProfissionalResponsavel)
    {
        return $this->chapaEleicaoRepository->getIdChapaEleicaoPorCalendarioEResponsavel(
            $idCalendario,
            $idProfissionalResponsavel);
    }

    /**
     * Recebe uma lista de cau uf e atribui os nomes (prefixo e descricao) para o array recebido
     *
     * @param $filiaisCauUf
     * @param $lista
     * @return array
     */
    private static function organizeIdCauUfParaLista($filiaisCauUf, $lista)
    {
        $listaNova = array();
        if (!empty($filiaisCauUf)) {
            $itemCAUBR = [];
            $itemIES = [];
            foreach ($lista as $i => $item) {
                foreach ($filiaisCauUf as $filialCauUf) {
                    if ($filialCauUf->getId() == $item['idCauUf']) {
                        if ($filialCauUf->getId() == Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                            $itemCAUBR['idCauUf'] = $filialCauUf->getId();
                            $itemCAUBR['prefixo'] = Constants::PREFIXO_CONSELHO_ELEITORAL_NACIONAL;
                            $itemCAUBR['descricao'] = Constants::PREFIXO_CONSELHO_ELEITORAL_NACIONAL;
                        } else if ($filialCauUf->getId() == Constants::IES_ID) {
                            $itemIES['idCauUf'] = $filialCauUf->getId();
                            $itemIES['prefixo'] = $filialCauUf->getPrefixo();
                            $itemIES['descricao'] = $filialCauUf->getDescricao();
                        } else {
                            $itemTemp['idCauUf'] = $filialCauUf->getId();
                            $itemTemp['prefixo'] = $filialCauUf->getPrefixo();
                            $itemTemp['descricao'] = $filialCauUf->getDescricao();

                            $listaNova[] = $itemTemp;
                        }
                    }
                }
            }
            uasort($listaNova, function ($a, $b) {
                return strnatcmp($a['prefixo'], $b['prefixo']);
            });

            if (!empty($itemCAUBR)) {
                $listaNova[] = $itemCAUBR;
            }
            if (!empty($itemIES)) {
                $listaNova[] = $itemIES;
            }
        }
        return $listaNova;
    }

    /**
     * Retorna a chapa em que o usuario logado e responsavel
     *
     * @param int $idCalendario
     * @param int $idProfissionalResponsavel
     * @return integer
     * @throws Exception
     */
    public function getIdChapaEleicaoPorChapaEResponsavel($idChapa, $idProfissionalResponsavel)
    {
        return $this->chapaEleicaoRepository->getIdChapaEleicaoPorChapaEResponsavel(
            $idChapa,
            $idProfissionalResponsavel);
    }

    /**
     * Retorna instância de Filial por Chapa.
     *
     * @param $idChapaEleicao
     * @return object|null
     */
    public function getFilial($idChapaEleicao)
    {
        return $this->chapaEleicaoRepository->findOneBy(['id' => $idChapaEleicao])->getFilial();
    }

    /**
     * Retorna os ids das cau ufs que possuem pedidos em andamento
     *
     * @param $idCalendario
     * @return array
     */
    public function getIdsCauUfChapasPorCalendarioSemJulgamento($idCalendario)
    {
        $chapasEleicao = $this->chapaEleicaoRepository->getChapasEleicaoPorCalendarioSemJulgamento($idCalendario);

        $idsCauUf = [];

        if (!empty($chapasEleicao)) {
            /** @var ChapaEleicao $chapaEleicao */
            foreach ($chapasEleicao as $chapaEleicao) {
                if ($chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES) {
                    $idsCauUf[$chapaEleicao->getIdCauUf()] = $chapaEleicao->getIdCauUf();
                } else {
                    $idsCauUf[Constants::ID_CAU_BR] = Constants::ID_CAU_BR;
                }
            }
        }

        return $idsCauUf;
    }

    /**
     * Responsável por retornar todos os pedidos (substituição, impugnação e denúncia) de uma chapa
     * @param $idChapaEleicao
     * @return PedidosChapaTO
     * @throws Exception
     */
    public function getPedidosSolicitadosPorChapa($idChapaEleicao)
    {
        $pedidosChapaTO = PedidosChapaTO::newInstance(['id' => $idChapaEleicao]);

        $pedidosImpugnacao = $this->getPedidosImpugnacaoSolicitados($idChapaEleicao);
        $pedidosChapaTO->setPedidosImpugnacao($pedidosImpugnacao ?? []);

        $pedidosSubstituicao = $this->getPedidosSubstituicaoSolicitados($idChapaEleicao);
        $pedidosChapaTO->setPedidosSubstituicao($pedidosSubstituicao ?? []);

        $pedidosDenuncia = $this->getDenunciaBO()->getPedidosSolicitadosPorChapa($idChapaEleicao);
        $pedidosChapaTO->setPedidosDenuncia($pedidosDenuncia ?? []);

        return $pedidosChapaTO;
    }

    /**
     * Método auxiliar para buscar e organizar os pedidos substituição solicitado por chapa
     * @param $idChapaEleicao
     */
    public function getPedidosSubstituicaoSolicitados($idChapaEleicao)
    {
        $pedidosSubstituicao = $this->getPedidoSubstituicaoChapaBO()->getPedidosSolicitadosPorChapa($idChapaEleicao);

        if (!empty($pedidosSubstituicao)) {
            $pedidosSubstituicao = array_map(function ($pedido) {
                /** @var PedidoSolicitadoTO $pedido */
                if (in_array($pedido->getStatus()->getId(), Constants::$statusSubstituicaoChapaEmAndamento)) {
                    $pedido->setStatusEmAnalise();
                }

                if (in_array($pedido->getStatus()->getId(), Constants::$statusSubstituicaoChapaDeferido)) {
                    $pedido->setStatusDeferido();
                }

                if (in_array($pedido->getStatus()->getId(), Constants::$statusSubstituicaoChapaIndeferido)) {
                    $pedido->setStatusIndeferido();
                }
                return $pedido;
            }, $pedidosSubstituicao);
        }
        return $pedidosSubstituicao;
    }

    /**
     * Método auxiliar para buscar e organizar os pedidos impugnacao solicitado por chapa
     * @param $idChapaEleicao
     */
    public function getPedidosImpugnacaoSolicitados($idChapaEleicao)
    {
        $pedidosImpugnacao = $this->getPedidoImpugnacaoBO()->getPedidosSolicitadosPorChapa($idChapaEleicao);

        if (!empty($pedidosImpugnacao)) {
            $pedidosImpugnacao = array_map(function ($pedido) {
                /** @var PedidoSolicitadoTO $pedido */
                if (in_array($pedido->getStatus()->getId(), Constants::$statusImpugnacaoChapaEmAndamento)) {
                    $pedido->setStatusEmAnalise();
                }

                if (in_array($pedido->getStatus()->getId(), Constants::$statusImpugnacaoChapaProcedente)) {
                    $pedido->setStatusProcedente();
                }

                if (in_array($pedido->getStatus()->getId(), Constants::$statusImpugnacaoChapaImprocedente)) {
                    $pedido->setStatusImprocedente();
                }
                return $pedido;
            }, $pedidosImpugnacao);
        }
        return $pedidosImpugnacao;
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
     * Retorna uma nova instância de 'DenunciaBO'.
     *
     * @return DenunciaBO
     */
    private function getDenunciaBO()
    {
        if (empty($this->denunciaBO)) {
            $this->denunciaBO = app()->make(DenunciaBO::class);
        }

        return $this->denunciaBO;
    }

    public function downloadFotoMembro($idMembro)
    {
        $membroChapa = $this->getMembroChapaRepository()->find($idMembro);
        $diretorioSinteseCurriculo = $this->getArquivoService()->getCaminhoRepositorioSinteseCurriculo(sprintf(
            '%s/%s',
            $membroChapa->getChapaEleicao()->getId(),
            $membroChapa->getId()
        ));

        $path = AppConfig::getRepositorio($diretorioSinteseCurriculo, $membroChapa->getNomeArquivoFoto());
        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($path, $membroChapa->getNomeArquivoFoto());
    }

    /**
     * @param $idChapa
     * @return mixed
     */
    public function getRetificacoesPlataforma($idChapa)
    {
        return $this->getPlataformaChapaHistoricoBO()->getRetificacoesPlataforma($idChapa);
    }

    /**
     * Retorna a plataforma e os meios de propraganda da chapa eleição conforme o id informado
     * @param $idChapa
     * @return ChapaEleicao|null
     * @throws Exception
     */
    public function getPlataformaAndMeiosPropaganda($idChapa)
    {
        /** @var ChapaEleicao $chapa */
        $chapa = $this->chapaEleicaoRepository->getPlataformaAndMeiosPropagandaPorId($idChapa);

        $chapaEleicaoRetorno = null;

        if (!empty($chapa)) {
            $chapaEleicaoRetorno = ChapaEleicao::newInstance();
            $chapaEleicaoRetorno->setId($chapa->getId());
            $chapaEleicaoRetorno->setDescricaoPlataforma($chapa->getDescricaoPlataforma());
            $chapaEleicaoRetorno->setRedesSociaisChapa($chapa->getRedesSociaisChapa());
            $chapaEleicaoRetorno->setTipoCandidatura($chapa->getTipoCandidatura());
        }

        return $chapaEleicaoRetorno;
    }

    /**
     * Salva o histórico da alteração
     * @param ChapaEleicao $chapaEleicao
     * @param $justificativa
     * @param bool $isAcessorCEN
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    private function salvarHistoricoAlteracaoPlataforma(ChapaEleicao $chapaEleicao, $justificativa, bool $isAcessorCEN)
    {

        $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
            $chapaEleicao,
            $isAcessorCEN ? Constants::ORIGEM_CORPORATIVO : Constants::ORIGEM_PROFISSIONAL,
            Constants::HISTORICO_ALTERACAO_PLATAFORMA_ELEITORAL,
            $justificativa
        );
        $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);

    }

    /**
     * Prepara a entidade Chapa para salvar a alteração da plataforma
     * @param ChapaEleicao $chapaEleicao
     * @param ChapaEleicao $chapaEleicaoAnterior
     * @param bool $isAcessorCEN
     */
    private function prepararChapaAlteracaoPlataforma(ChapaEleicao $chapaEleicao, ChapaEleicao $chapaEleicaoAnterior, bool $isAcessorCEN)
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $chapaEleicaoAnterior->setDescricaoPlataforma($chapaEleicao->getDescricaoPlataforma());

        $chapaEleicaoAnterior->setProfissionalInclusaoPlataforma(
            !$isAcessorCEN
                ? Profissional::newInstance(['id' => $usuarioLogado->idProfissional]) : null
        );

        $chapaEleicaoAnterior->setUsuarioInclusaoPlataforma(
            $isAcessorCEN
                ? Usuario::newInstance(['id' => $usuarioLogado->id]) : null
        );
    }

    /**
     * Verifica permissao do usuário logado para alteração da plataforma
     * @param ChapaEleicao $chapaEleicao
     * @param bool $isAcessorCEN
     * @throws NegocioException
     */
    private function verificaPermissaoDeAlterarPlataforma(ChapaEleicao $chapaEleicao, bool $isAcessorCEN)
    {
        if ($this->getUsuarioFactory()->isCorporativo() && !$isAcessorCEN) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        if ($this->getUsuarioFactory()->isProfissional()) {
            $isResponsavel = $this->getMembroChapaBO()->isMembroResponsavelChapa(
                $chapaEleicao->getId(),
                $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
            );

            if (!$isResponsavel) {
                throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
            }
        }
    }

    /**
     * Valida as regras sobre as cotas de representatividade
     *
     * @param ChapaEleicao $chapaEleicao
     * @param $idCalendario
     */
    private function validarCriteriosCotistasRepresentatividade(ChapaEleicao $chapaEleicao, $idCalendario)
    {
        if (!empty($chapaEleicao->getMembrosChapa())) {
            $atividade16 = $this->atividadeSecundariaCalendarioRepository->getPorCalendario($idCalendario, 1, 6);

            $filtroTO = new stdClass();
            $filtroTO->idAtividadeSecundaria = $atividade16->getId();
            $filtroTO->idsCauUf = $chapaEleicao->getIdCauUf();
            $primeiroTercoMembros = [];

            $parametroConselheiro = $this->parametroConselheiroRepository->getParametroConselheiroPorFiltro($filtroTO);
            /* Um terço do número de Conselheiros Estaduais Titulares + 1 Conselheiro Federal Titular */
            $chapaEleicaoTitular = array_filter($chapaEleicao->getMembrosChapa(), function ($chapa) {
                return $chapa->getTipoParticipacaoChapa()->getId() == Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR;
            });

            $membros = new \ArrayObject($chapaEleicaoTitular);
            $iterator = $membros->getIterator();

            $iterator->uasort(function ($a, $b) {
                if ($a->getNumeroOrdem() == $b->getNumeroOrdem()) {
                    return ($a->getTipoParticipacaoChapa()->getId() < $b->getTipoParticipacaoChapa()->getId()) ? -1 : 1;
                } else {
                    return ($a->getNumeroOrdem() < $b->getNumeroOrdem()) ? -1 : 1;
                }
            });

            $membros = new ArrayCollection(iterator_to_array($iterator));
            $chapaEleicaoTitularOrdenado = $membros->getValues();

            if ($chapaEleicaoTitularOrdenado) {
                $primeiroTercoMembros = array_slice($chapaEleicaoTitularOrdenado, 0, ceil(($parametroConselheiro[0]['numeroProporcaoConselheiro'] + 1) / 3));
            }

            $membrosRepresentatividade = array_filter($primeiroTercoMembros, function ($membroChapa) {
                return !empty($membroChapa->getRespostaDeclaracaoRepresentatividade());
            });

            /* 1ª Validação: Critérios. Cada chapa deverá atender no mínimo x critério de representatividade */
            $arrRespostaDeclaracaoRepresentatividade = [];

            foreach ($membrosRepresentatividade as $membro) {
                foreach ($membro->getRespostaDeclaracaoRepresentatividade() as $respostaDeclaracaoRepresentatividade) {
                    $arrRespostaDeclaracaoRepresentatividade[] = $respostaDeclaracaoRepresentatividade->getItemDeclaracao()->getId();
                }
            }

            $arrUniqueRespostaDeclaracaoRepresentatividade = array_unique($arrRespostaDeclaracaoRepresentatividade);
            $chapaEleicao->setAtendeCriteriosRepresentatividade(count($arrUniqueRespostaDeclaracaoRepresentatividade) >= $parametroConselheiro[0]['qtdMinimaCriterio']);

            /* 2ª Validação: Cotistas. O número de cotistas da chapa deverá ser atendido. */
            $chapaEleicao->setAtendeCotistasRepresentatividade(count($membrosRepresentatividade) >= $parametroConselheiro[0]['qtdMinimaCotista']);
        }
    }

    public function gerarCSVChapasTrePorUf($idCalendario, $idCauUf)
    {
        $tipoCandidatura = Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR;
        $chapasEleicao = $this->chapaEleicaoRepository->getChapasEleicaoPorCalendarioTpCandidaturaAndCauUf(
            $idCalendario, $tipoCandidatura, false, $idCauUf
        );
        $lista = $this->converterChapaJulgamentoFinalParaChapaEleicaoTO($chapasEleicao);
        $registros = [];
        foreach ($lista as $chapa) {            
            if($chapa->getStatusChapaVigente()->getId() == Constants::ID_STATUS_PEDIDO_CHAPA_DEFERIDO){
                $resp = $this->getChapaEleicaoJulgamentoFinalPorIdChapaTre($chapa->getId());
                $registros[] = $resp;                
            }            
        }

        if (empty($registros)) {
            throw new \Exception('Nenhum dado cadastrado');
        }
        
        return $this->getExportarChapaCSVBO()->exportarTre($registros);
    }

    public function getChapaEleicaoJulgamentoFinalPorIdChapaTre(
        $idChapa,
        $eleicao = null,
        $isResponsalvelChapa = false,
        $isMembroComissao = false
    )
    {
        /** @var ChapaEleicao $chapaEleicao */
        $chapaEleicao = $this->chapaEleicaoRepository->getPorIdTre($idChapa, true, true, true);

        if (empty($chapaEleicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $chapaELeicaoTO = ChapaEleicaoTO::newInstanceFromEntityByJulgamentoFinal($chapaEleicao);

        return $chapaELeicaoTO;
    }

}
