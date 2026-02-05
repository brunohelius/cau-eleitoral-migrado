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
use App\Entities\ChapaEleicao;
use App\Entities\Filial;
use App\Entities\MembroChapa;
use App\Entities\MembroChapaSubstituicao;
use App\Entities\MembroComissao;
use App\Entities\PedidoSubstituicaoChapa;
use App\Entities\SituacaoMembroChapa;
use App\Entities\StatusParticipacaoChapa;
use App\Entities\StatusSubstituicaoChapa;
use App\Entities\StatusValidacaoMembroChapa;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\PDFFActory;
use App\Jobs\EnviarPedidoSubstituicaoChapaCadastradaJob;
use App\Repository\ChapaEleicaoRepository;
use App\Repository\MembroChapaRepository;
use App\Repository\MembroChapaSubstituicaoRepository;
use App\Repository\MembroComissaoRepository;
use App\Repository\PedidoSubstituicaoChapaRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoTO;
use App\To\AtividadePrincipalCalendarioTO;
use App\To\AtividadeSecundariaCalendarioTO;
use App\To\EleicaoTO;
use App\To\MembroChapaSubstituicaoTO;
use App\To\PedidoSubstituicaoChapaTO;
use App\To\QuantidadePedidoSubstituicaoPorUfTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use stdClass;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'PedidoSubstituicaoChapa'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class PedidoSubstituicaoChapaBO extends AbstractBO
{

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var HistoricoProfissionalBO
     */
    private $historicoProfissionalBO;

    /**
     * @var JulgamentoSubstituicaoBO
     */
    private $julgamentoSubstituicaoBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var PedidoSubstituicaoChapaRepository
     */
    private $pedidoSubstituicaoChapaRepository;

    /**
     * @var MembroChapaSubstituicaoRepository
     */
    private $membroChapaSubstituicaoRepository;

    /**
     * @var ChapaEleicaoRepository
     */
    private $chapaEleicaoRepository;

    /**
     * @var MembroChapaRepository
     */
    private $membroChapaRepository;

    /**
     * @var MembroComissaoRepository
     */
    private $membroComissaoRepository;

    /**
     * @var PDFFActory
     */
    private $pdfFactory;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->pedidoSubstituicaoChapaRepository = $this->getRepository(PedidoSubstituicaoChapa::class);
    }

    /**
     * Retorna o pedido de substituição chapa conforme o id informado.
     *
     * @param $id
     * @param bool $addDadosComplementares
     *
     * @return PedidoSubstituicaoChapaTO
     * @throws NonUniqueResultException
     */
    public function getPorId($id, $addDadosComplementares = true)
    {
        $pedidoSubstituicaoChapaTO = $this->pedidoSubstituicaoChapaRepository->getPorId($id);

        if (!empty($pedidoSubstituicaoChapaTO)) {
            $pedidoSubstituicaoChapaTO->getChapaEleicao()->definirStatusChapaVigente();
            $pedidoSubstituicaoChapaTO->getChapaEleicao()->setChapaEleicaoStatus(null);

            $eleicao = $this->getEleicaoBO()->getEleicaoPorPedidoSubstituicao($id, true);

            $pedidoSubstituicaoChapaTO->iniciarFlags();

            /** @var AtividadePrincipalCalendarioTO $atividadePrincipalTO */
            foreach ($eleicao->getCalendario()->getAtividadesPrincipais() as $atividadePrincipalTO) {

                /** @var AtividadeSecundariaCalendarioTO $atividadeSecundariaTO */
                foreach ($atividadePrincipalTO->getAtividadesSecundarias() as $atividadeSecundariaTO) {
                    $dataInicio = Utils::getDataHoraZero($atividadeSecundariaTO->getDataInicio());
                    $isIniciadoAtividade = Utils::getDataHoraZero() >= $dataInicio;

                    $dataFim = Utils::getDataHoraZero($atividadeSecundariaTO->getDataFim());
                    $isFinalizadoAtividade = Utils::getDataHoraZero() > $dataFim;

                    if ($atividadePrincipalTO->getNivel() == 2 && $atividadeSecundariaTO->getNivel() == 5) {
                        $pedidoSubstituicaoChapaTO->setIsIniciadoAtividadeRecurso($isIniciadoAtividade);
                        $pedidoSubstituicaoChapaTO->setIsFinalizadoAtividadeRecurso($isFinalizadoAtividade);
                    }

                    if ($atividadePrincipalTO->getNivel() == 2 && $atividadeSecundariaTO->getNivel() == 6) {
                        $pedidoSubstituicaoChapaTO->setIsIniciadoAtividadeJulgamentoRecurso($isIniciadoAtividade);
                        $pedidoSubstituicaoChapaTO->setIsFinalizadoAtividadeJulgamentoRecurso($isFinalizadoAtividade);
                    }
                }
            }

            if ($this->getUsuarioFactory()->isCorporativo() && $addDadosComplementares) {
                $pedidoSubstituicaoChapaTO->setIsPermissaoJulgamento(
                    $this->getJulgamentoSubstituicaoBO()->verificarPedidoSubstituicaoPodeSerJulgado($pedidoSubstituicaoChapaTO)
                );
            }
        }

        return $pedidoSubstituicaoChapaTO;
    }

    /**
     * Retorna o pedido de substituição chapa conforme o id informado.
     *
     * @param $id
     *
     * @return PedidoSubstituicaoChapa|null
     */
    public function findById($id)
    {
        /** @var PedidoSubstituicaoChapa $pedidoSubstituicaoChapa */
        $pedidoSubstituicaoChapa = $this->pedidoSubstituicaoChapaRepository->find($id);

        return $pedidoSubstituicaoChapa;
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
     * Recupera a eleição de acordo com o id do pedido de substituição
     *
     * @param $idPedidoSubstituicao
     * @return EleicaoTO
     */
    public function getEleicaoPorPedidoSubstituicaoChapa($idPedidoSubstituicao)
    {
        return $this->getEleicaoBO()->getEleicaoPorPedidoSubstituicao($idPedidoSubstituicao);
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
     * Salva o pedido de subsdtituição chapa
     *
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @return PedidoSubstituicaoChapaTO
     * @throws \Exception
     */
    public function salvar(PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO)
    {
        $eleicaoTO = $this->getEleicaoVigenteSubstituicaoMembroChapa();

        /** @var ChapaEleicao $chapaEleicao */
        $chapaEleicao = $this->getChapaEleicaoRepository()->find($pedidoSubstituicaoChapaTO->getIdChapaEleicao());

        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        if (!$this->getMembroChapaBO()->isMembroResponsavelChapa($chapaEleicao->getId(),
            $usuarioLogado->idProfissional)) {
            throw new NegocioException(Message::MSG_VISUALIZACAO_ATIV_APENAS_MEMBROS_RESPONSAVEIS_CHAPA);
        }

        $this->validarCamposSalvarPedido($chapaEleicao->getId(), $pedidoSubstituicaoChapaTO);

        try {
            $this->beginTransaction();

            $ultimoProtocolo = $this->getPedidoSubstituicaoChapaRepository()->getUltimoProtocoloPorCalendario(
                $eleicaoTO->getCalendario()->getId()
            );

            $pedidoSubstituicaoChapa = $this->prepararPedidoSalvar(
                $pedidoSubstituicaoChapaTO, $ultimoProtocolo, $chapaEleicao
            );

            /** @var PedidoSubstituicaoChapa $pedidoSubstituicaoSalvo */
            $pedidoSubstituicaoSalvo = $this->getPedidoSubstituicaoChapaRepository()->persist($pedidoSubstituicaoChapa);

            if (!empty($pedidoSubstituicaoChapaTO->getNomeArquivo())) {
                $this->salvarArquivo(
                    $pedidoSubstituicaoSalvo->getId(),
                    $pedidoSubstituicaoChapaTO->getArquivo(),
                    $pedidoSubstituicaoChapa->getNomeArquivoFisico()
                );
            }

            $membrosChapaSubstituicao = $this->salvarMembrosChapaSubstituicao(
                $chapaEleicao, $pedidoSubstituicaoSalvo, $pedidoSubstituicaoChapaTO
            );

            $this->salvarHistoricoPedidoSubstituicaoChapa($pedidoSubstituicaoSalvo);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarPedidoSubstituicaoChapaCadastradaJob($pedidoSubstituicaoSalvo->getId()));

        return PedidoSubstituicaoChapaTO::newInstanceFromEntity($pedidoSubstituicaoSalvo);
    }

    /**
     * Retorna uma nova instância de 'ChapaEleicaoRepository'.
     *
     * @return ChapaEleicaoRepository
     */
    private function getChapaEleicaoRepository()
    {
        if (empty($this->chapaEleicaoRepository)) {
            $this->chapaEleicaoRepository = $this->getRepository(ChapaEleicao::class);
        }

        return $this->chapaEleicaoRepository;
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
     * Verifica se os campos obrigatórios foram preenchidos.
     *
     * @param $idChapaEleicao
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function validarCamposSalvarPedido($idChapaEleicao, PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO)
    {
        if (empty($pedidoSubstituicaoChapaTO->getJustificativa())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (!empty($pedidoSubstituicaoChapaTO->getNomeArquivo())) {
            if (empty($pedidoSubstituicaoChapaTO->getTamanho())) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
            }

            if (empty($pedidoSubstituicaoChapaTO->getArquivo())) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
            }

            $dadosTOValidarArquivo = new stdClass();
            $dadosTOValidarArquivo->nome = $pedidoSubstituicaoChapaTO->getNomeArquivo();
            $dadosTOValidarArquivo->tamanho = $pedidoSubstituicaoChapaTO->getTamanho();
            $dadosTOValidarArquivo->tipoValidacao = Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_10MB;
            $this->getArquivoService()->validarArquivo($dadosTOValidarArquivo);
        }

        if (empty($pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular())
            || empty($pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente())
        ) {
            throw new NegocioException(Message::MSG_CAMPO_SUBSTITUTO_CHAPA_OBRIGATORIO);
        }

        $this->validarCamposMembroSubstituto($pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular());
        $this->validarCamposMembroSubstituto($pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente());

        $totalResposnsaveisChapa = $this->getMembroChapaRepository()->totalMembrosResponsaveisChapa($idChapaEleicao);
        $totalResposnsaveisAnteriores = $this->getMembroChapaRepository()->totalMembrosResponsaveisChapa(
            $idChapaEleicao,
            $pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->getTipoMembroChapa()->getId(),
            null,
            $pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->getNumeroOrdem()
        );

        $isResponsavelTitular = $pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->isSituacaoResponsavel();
        $isResponsavelSuplente = $pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente()->isSituacaoResponsavel();

        $totalResposnsaveisSubstituto = json_decode($isResponsavelTitular) ? 1 : 0;
        $totalResposnsaveisSubstituto += json_decode($isResponsavelSuplente) ? 1 : 0;

        if ($totalResposnsaveisChapa == $totalResposnsaveisAnteriores && $totalResposnsaveisSubstituto == 0) {
            throw new NegocioException(Message::MSG_DESABILTOU_TODOS_RESPONSAVEL_CHAPA);
        }

        $membro = !empty($pedidoSubstituicaoChapaTO->getMembroSubstituidoTitular())
            ? $pedidoSubstituicaoChapaTO->getMembroSubstituidoTitular()
            : $pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente();

        $idPedido = $this->getPedidoSubstituicaoChapaRepository()->getIdPedidoSubstituicaoPorTipoMembroChapa(
            $idChapaEleicao,
            $membro->getTipoMembroChapa()->getId(),
            $membro->getNumeroOrdem()
        );
        if (!empty($idPedido)) {
            throw new NegocioException(Message::MSG_MEMBRO_CHAPA_JA_TEM_PEDIDO_SUBSTITUICAO);
        }
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
     * Verifica se os campos obrigatórios foram preenchidos.
     *
     * @param MembroChapa $membroChapa
     * @throws NegocioException
     */
    private function validarCamposMembroSubstituto($membroChapa)
    {
        if (!($membroChapa instanceof MembroChapa)) {
            throw new NegocioException(Message::MSG_CAMPO_SUBSTITUTO_CHAPA_OBRIGATORIO);
        }

        if (empty($membroChapa->getProfissional()) || empty($membroChapa->getProfissional()->getId())) {
            throw new NegocioException(Message::MSG_CAMPO_SUBSTITUTO_CHAPA_OBRIGATORIO);
        }

        if (empty($membroChapa->getTipoMembroChapa()) || empty($membroChapa->getTipoMembroChapa()->getId())) {
            throw new NegocioException(Message::MSG_CAMPO_SUBSTITUTO_CHAPA_OBRIGATORIO);
        }

        if (empty($membroChapa->getTipoParticipacaoChapa()) || empty($membroChapa->getTipoParticipacaoChapa()->getId())) {
            throw new NegocioException(Message::MSG_CAMPO_SUBSTITUTO_CHAPA_OBRIGATORIO);
        }

        if (is_null($membroChapa->getNumeroOrdem())) {
            throw new NegocioException(Message::MSG_CAMPO_SUBSTITUTO_CHAPA_OBRIGATORIO);
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
     * Retorna uma nova instância de 'PedidoSubstituicaoChapaRepository'.
     *
     * @return PedidoSubstituicaoChapaRepository
     */
    private function getPedidoSubstituicaoChapaRepository()
    {
        if (empty($this->pedidoSubstituicaoChapaRepository)) {
            $this->pedidoSubstituicaoChapaRepository = $this->getRepository(PedidoSubstituicaoChapa::class);
        }

        return $this->pedidoSubstituicaoChapaRepository;
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $idPedidoSubstituicaoChapa
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idPedidoSubstituicaoChapa, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioPedidoSubstituicaoChapa($idPedidoSubstituicaoChapa),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Salva os Membros Chapa Substituicao do pedido de substituiçao da chapa
     *
     * @param ChapaEleicao $chapaEleicao
     * @param PedidoSubstituicaoChapa $pedidoSubstituicaoChapa
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @return array
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function salvarMembrosChapaSubstituicao(
        ChapaEleicao $chapaEleicao,
        PedidoSubstituicaoChapa $pedidoSubstituicaoChapa,
        PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
    ) {
        $membroTitularAnterior = $this->getMembroChapaBO()->getMembroChapaPorTipoNumeroOrdem(
            $pedidoSubstituicaoChapa->getChapaEleicao()->getId(),
            $pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->getTipoMembroChapa()->getId(),
            $pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->getTipoParticipacaoChapa()->getId(),
            $pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->getNumeroOrdem()
        );

        $membroSuplenteAnterior = $this->getMembroChapaBO()->getMembroChapaPorTipoNumeroOrdem(
            $pedidoSubstituicaoChapa->getChapaEleicao()->getId(),
            $pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente()->getTipoMembroChapa()->getId(),
            $pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente()->getTipoParticipacaoChapa()->getId(),
            $pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente()->getNumeroOrdem()
        );

        $membroSuplenteSubstituto = null;
        $idProfSuplenteSubstituto = $pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente()->getProfissional()->getId();
        if (
            empty($membroSuplenteAnterior) ||
            (
                !empty($membroSuplenteAnterior)
                && $membroSuplenteAnterior->getProfissional()->getId() != $idProfSuplenteSubstituto
            )
        ) {
            $membroSuplenteSubstituto = $this->salvarMembroChapaSubstituto(
                $chapaEleicao,
                $pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente()
            );
        }

        $membroTitularSubstituto = null;
        if (!empty($pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->getProfissional())
            && !empty($pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->getProfissional()->getId())) {

            $idProfTitularSubstituto = $pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->getProfissional()->getId();
            if (
                empty($membroTitularAnterior) ||
                (!empty($membroTitularAnterior) && $membroTitularAnterior->getProfissional()->getId() != $idProfTitularSubstituto)
            ) {

                if (!empty($membroSuplenteSubstituto)) {
                    $pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->setSuplente($membroSuplenteSubstituto);
                }

                $membroTitularSubstituto = $this->salvarMembroChapaSubstituto(
                    $chapaEleicao,
                    $pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()
                );
            }
        }

        $membrosChapaSubstituicao = [];

        $membrosChapaSubstituicao[] = $this->salvarMembroChapaSubstituicao(
            $pedidoSubstituicaoChapa,
            $membroTitularAnterior,
            $membroTitularSubstituto
        );

        $membrosChapaSubstituicao[] = $this->salvarMembroChapaSubstituicao(
            $pedidoSubstituicaoChapa,
            $membroSuplenteAnterior,
            $membroSuplenteSubstituto
        );

        return $membrosChapaSubstituicao;
    }

    /**
     * Salva o MembroChapa que será um dos substituto
     *
     * @param ChapaEleicao $chapaEleicao
     * @param MembroChapa $membroChapa
     * @return MembroChapa
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function salvarMembroChapaSubstituto(
        ChapaEleicao $chapaEleicao,
        MembroChapa $membroChapa
    )
    {
        $profissionalTO = $this->getProfissionalBO()->getPorId($membroChapa->getProfissional()->getId(), true);

        $this->getMembroChapaBO()->validarImpedimentosIncluirMembro(
            $chapaEleicao,
            $profissionalTO,
            $membroChapa->getTipoMembroChapa()->getId(),
            true
        );

        $membroChapaSubstituicaoValidarTO = MembroChapaSubstituicaoTO::newInstance([
            "numeroOrdem" => $membroChapa->getNumeroOrdem(),
            "idTipoMembro" => $membroChapa->getTipoMembroChapa()->getId(),
            "idProfissional" => $membroChapa->getProfissional()->getId(),
            "situacaoResponsavel" => $membroChapa->isSituacaoResponsavel(),
            "idTipoParticipacaoChapa" => $membroChapa->getTipoParticipacaoChapa()->getId()
        ]);
        $this->getMembroChapaBO()->verificarProfissionalPodeSerSubstitutoChapa(
            $chapaEleicao->getId(),
            $membroChapaSubstituicaoValidarTO,
            $chapaEleicao->getAtividadeSecundariaCalendario()->getId()
        );

        $membroChapa->setId(null);
        if (empty($membroChapa->isSituacaoResponsavel())) {
            $membroChapa->setSituacaoResponsavel(false);
        }
        $membroChapa->setChapaEleicao($chapaEleicao);
        $membroChapa->setStatusValidacaoMembroChapa(StatusValidacaoMembroChapa::newInstance([
            "id" => Constants::STATUS_VALIDACAO_MEMBRO_PENDENTE
        ]));
        $membroChapa->setStatusParticipacaoChapa(StatusParticipacaoChapa::newInstance([
            "id" => Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR
        ]));
        $membroChapa->setSituacaoMembroChapa(SituacaoMembroChapa::newInstance([
            "id" => Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO
        ]));

        $membroChapa->setPendencias(null);

        $this->getMembroChapaRepository()->persist($membroChapa);

        $this->getMembroChapaBO()->salvarPendenciasMembro($membroChapa, $profissionalTO);

        return $membroChapa;
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
     * Método auxiliar que cria uma instância de MembroChapaSubstituicao e faz a persistência da entidade
     *
     * @param PedidoSubstituicaoChapa $pedidoSubstituicaoChapa
     * @param MembroChapa|null $membroTitularAnterior
     * @param MembroChapa|null $membroTitularSubstituto
     * @return MembroChapaSubstituicao
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function salvarMembroChapaSubstituicao(
        PedidoSubstituicaoChapa $pedidoSubstituicaoChapa,
        ?MembroChapa $membroTitularAnterior,
        ?MembroChapa $membroTitularSubstituto
    )
    {
        $membroChapaSubstituicaoTitular = MembroChapaSubstituicao::newInstance();
        $membroChapaSubstituicaoTitular->setPedidoSubstituicaoChapa($pedidoSubstituicaoChapa);
        $membroChapaSubstituicaoTitular->setMembroChapaSubstituido($membroTitularAnterior);
        $membroChapaSubstituicaoTitular->setMembroChapaSubstituto($membroTitularSubstituto);
        $this->getMembroChapaSubstituicaoRepository()->persist($membroChapaSubstituicaoTitular);

        return $membroChapaSubstituicaoTitular;
    }

    /**
     * Retorna uma nova instância de 'MembroChapaSubstituicaoRepository'.
     *
     * @return MembroChapaSubstituicaoRepository
     */
    private function getMembroChapaSubstituicaoRepository()
    {
        if (empty($this->membroChapaSubstituicaoRepository)) {
            $this->membroChapaSubstituicaoRepository = $this->getRepository(MembroChapaSubstituicao::class);
        }

        return $this->membroChapaSubstituicaoRepository;
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param PedidoSubstituicaoChapa $pedidoSubstituicaoSalvo
     * @throws Exception
     */
    private function salvarHistoricoPedidoSubstituicaoChapa(PedidoSubstituicaoChapa $pedidoSubstituicaoSalvo): void
    {
        $historico = $this->getHistoricoProfissionalBO()->criarHistorico(
            $pedidoSubstituicaoSalvo->getId(),
            Constants::HISTORICO_PROF_TIPO_PEDIDO_SUBSTITUICAO,
            Constants::HISTORICO_PROF_ACAO_INSERIR,
            Constants::HISTORICO_PROF_DESCRICAO_ACAO_INSERIR
        );
        $this->getHistoricoProfissionalBO()->salvar($historico);
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
     * Responsável por enviar emails após cadastrar pedido substituição chapa
     *
     * @param $idChapaEleicao
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function enviarEmailsPedidoSubstituicaoIncluido($idPedidoSubstituicao)
    {
        $pedidoSubstituicaoChapaTO = $this->getPorId($idPedidoSubstituicao);

        $idAtivSecundaria = $this->getPedidoSubstituicaoChapaRepository()->getIdAtividadeSecundariPedidoSubstituicao(
            $idPedidoSubstituicao
        );

        $parametrosEmail = $this->prepararParametrosEmailPedidoSubstituicao($pedidoSubstituicaoChapaTO);
        $idTipoCndidatura = $pedidoSubstituicaoChapaTO->getChapaEleicao()->getTipoCandidatura()->getId();

        // enviar e-mail informativo para responsável chapa uf ou IES
        $this->enviarEmailResponsavelChapa($idAtivSecundaria, $pedidoSubstituicaoChapaTO, $parametrosEmail);

        // enviar e-mail informativo para membro substituido uf ou IES
        $this->enviarEmailMembroSubstituido($idAtivSecundaria, $pedidoSubstituicaoChapaTO, $parametrosEmail);

        // enviar e-mail informativo para membro substituto uf ou IES
        $this->enviarEmailMembroSubstituto($idAtivSecundaria, $pedidoSubstituicaoChapaTO, $parametrosEmail);

        // enviar e-mail informativo para conselheiros CEN e a comissão UF
        /*if ($idTipoCndidatura == Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR) {
            $this->getEmailAtividadeSecundariaBO()->enviarEmailConselheirosCoordenadoresComissao(
                $idAtivSecundaria,
                Constants::EMAIL_SUBST_MEMBRO_CHAPA_PARA_CONSELHEIROS_CEN_E_CEUF,
                Constants::TEMPLATE_EMAIL_PEDIDO_SUBSTITUICAO_CHAPA,
                $pedidoSubstituicaoChapaTO->getChapaEleicao()->getIdCauUf(),
                $parametrosEmail
            );
        }*/

        // enviar e-mail informativo para os acessores CEN/BR e CE
        $this->enviarEmailAcessoresCenAndAcessoresCE($idAtivSecundaria, $pedidoSubstituicaoChapaTO, $parametrosEmail);
    }

    /**
     * Retorna uma nova instância de 'JulgamentoSubstituicaoBO'.
     *
     * @return JulgamentoSubstituicaoBO
     */
    private function getJulgamentoSubstituicaoBO()
    {
        if (empty($this->julgamentoSubstituicaoBO)) {
            $this->julgamentoSubstituicaoBO = app()->make(JulgamentoSubstituicaoBO::class);
        }

        return $this->julgamentoSubstituicaoBO;
    }

    /**
     * Método auxiliar que prepara os parâmetros para o envio de e-mails
     *
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @return array
     */
    public function prepararParametrosEmailPedidoSubstituicao(
        PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
    ): array
    {
        $nomeSubstituidoTitular = !empty($pedidoSubstituicaoChapaTO->getMembroSubstituidoTitular())
            ? $pedidoSubstituicaoChapaTO->getMembroSubstituidoTitular()->getProfissional()->getNome()
            : null;

        $nomeSubstituidoSuplente = !empty($pedidoSubstituicaoChapaTO->getMembroSubstituidoSuplente())
            ? $pedidoSubstituicaoChapaTO->getMembroSubstituidoSuplente()->getProfissional()->getNome()
            : null;

        $nomeSubstitutoTitular = !empty($pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular())
            ? $pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->getProfissional()->getNome()
            : $nomeSubstituidoTitular;

        $nomeSubstitutoSuplente = !empty($pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente())
            ? $pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente()->getProfissional()->getNome()
            : $nomeSubstituidoSuplente;

        $parametrosEmail = [
            Constants::PARAMETRO_EMAIL_NM_PROTOCOLO => $pedidoSubstituicaoChapaTO->getNumeroProtocolo(),
            Constants::PARAMETRO_EMAIL_NM_SUBSTITUIDO_TITULAR => $nomeSubstituidoTitular,
            Constants::PARAMETRO_EMAIL_NM_SUBSTITUIDO_SUPLENTE => $nomeSubstituidoSuplente,
            Constants::PARAMETRO_EMAIL_NM_SUBSTITUTO_TITULAR => $nomeSubstitutoTitular,
            Constants::PARAMETRO_EMAIL_NM_SUBSTITUTO_SUPLENTE => $nomeSubstitutoSuplente,
        ];
        return $parametrosEmail;
    }

    /**
     * Método faz o envio de e-mails para os responsáveis pela chapa após o cadatro do pedido de substituição
     *
     * @param int $idAtivSecundaria
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @param array $parametrosEmail
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailResponsavelChapa(
        int $idAtivSecundaria,
        PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO,
        array $parametrosEmail
    )
    {
        $responsaveis = $this->getMembroChapaBO()->getMembrosResponsaveisChapa(
            $pedidoSubstituicaoChapaTO->getChapaEleicao()->getId()
        );

        $destinatarios = $this->getMembroChapaBO()->getListEmailsDestinatarios($responsaveis);

        $idTipoCandidatura = $pedidoSubstituicaoChapaTO->getChapaEleicao()->getTipoCandidatura()->getId();

        $idTipoEmail = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES
            ? Constants::EMAIL_SUBST_MEMBRO_CHAPA_RESPONSAVEIS_IES
            : Constants::EMAIL_SUBST_MEMBRO_CHAPA_RESPONSAVEIS_UF;

        $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
            $idAtivSecundaria,
            array_unique($destinatarios),
            $idTipoEmail,
            Constants::TEMPLATE_EMAIL_PEDIDO_SUBSTITUICAO_CHAPA,
            $parametrosEmail
        );
        if (!empty($idPedido)) {
            throw new NegocioException(Message::MSG_MEMBRO_CHAPA_JA_TEM_PEDIDO_SUBSTITUICAO);
        }
    }

    /**
     * @param array|null $pedidosSubstituicaoChapa
     */
    public function ordenarPedidosSubstituicao(?array &$pedidosSubstituicaoChapa)
    {
        usort($pedidosSubstituicaoChapa, function ($obj1, $obj2) {
            return $obj1->getId() > $obj2->getId();
        });
    }

    /**
     * Retorna uma nova instância de 'ChapaEleicaoBO'.
     *
     * @return ChapaEleicaoBO|mixed
     */
    private function getChapaEleicaoBO()
    {
        if (empty($this->chapaEleicaoBO)) {
            $this->chapaEleicaoBO = app()->make(ChapaEleicaoBO::class);
        }

        return $this->chapaEleicaoBO;
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
     * Método faz o envio de e-mails para os membros substituidos após o cadatro do pedido de substituição
     *
     * @param int $idAtivSecundaria
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @param array $parametrosEmail
     * @throws NonUniqueResultException
     */
    private function enviarEmailMembroSubstituido(
        int $idAtivSecundaria,
        PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO,
        array $parametrosEmail
    )
    {
        $destinatarios = [];
        if (!empty($pedidoSubstituicaoChapaTO->getMembroSubstituidoTitular())) {
            $destinatarios[] = $pedidoSubstituicaoChapaTO->getMembroSubstituidoTitular()->getProfissional()->getPessoa()->getEmail();
        }

        if (!empty($pedidoSubstituicaoChapaTO->getMembroSubstituidoSuplente())) {
            $destinatarios[] = $pedidoSubstituicaoChapaTO->getMembroSubstituidoSuplente()->getProfissional()->getPessoa()->getEmail();
        }

        $idTipoCandidatura = $pedidoSubstituicaoChapaTO->getChapaEleicao()->getTipoCandidatura()->getId();

        $idTipoEmail = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES
            ? Constants::EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUIDO_IES
            : Constants::EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUIDO_UF;

        $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
            $idAtivSecundaria,
            array_unique($destinatarios),
            $idTipoEmail,
            Constants::TEMPLATE_EMAIL_PEDIDO_SUBSTITUICAO_CHAPA,
            $parametrosEmail
        );
    }

    /**
     * Método faz o envio de e-mails para os membros substitutos após o cadatro do pedido de substituição
     *
     * @param int $idAtivSecundaria
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @param array $parametrosEmail
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailMembroSubstituto(
        int $idAtivSecundaria,
        PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO,
        array $parametrosEmail
    )
    {
        $destinatarios = [];
        if (!empty($pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular())) {
            $destinatarios[] = $pedidoSubstituicaoChapaTO->getMembroSubstitutoTitular()->getProfissional()->getPessoa()->getEmail();
        }

        if (!empty($pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente())) {
            $destinatarios[] = $pedidoSubstituicaoChapaTO->getMembroSubstitutoSuplente()->getProfissional()->getPessoa()->getEmail();
        }

        $idTipoCandidatura = $pedidoSubstituicaoChapaTO->getChapaEleicao()->getTipoCandidatura()->getId();

        $idTipoEmail = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES
            ? Constants::EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUTO_IES
            : Constants::EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUTO_UF;

        $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
            $idAtivSecundaria,
            array_unique($destinatarios),
            $idTipoEmail,
            Constants::TEMPLATE_EMAIL_PEDIDO_SUBSTITUICAO_CHAPA,
            $parametrosEmail
        );
    }

    /**
     * Método faz o envio de e-mails para os acessores CEN/BR e CE/UF após o cadatro do pedido de substituição
     *
     * @param int $idAtivSecundaria
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @param array $parametrosEmail
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailAcessoresCenAndAcessoresCE(
        int $idAtivSecundaria,
        PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO,
        array $parametrosEmail
    )
    {

        $idTipoCandidatura = $pedidoSubstituicaoChapaTO->getChapaEleicao()->getTipoCandidatura()->getId();

        $idsCauUf = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR
            ? [$pedidoSubstituicaoChapaTO->getChapaEleicao()->getIdCauUf()]
            : null;

        $this->getEmailAtividadeSecundariaBO()->enviarEmailAcessoresCenAndAcessoresCE(
            $idAtivSecundaria,
            Constants::EMAIL_SUBST_MEMBRO_CHAPA_PARA_ASSESSOR_CEN_E_CEUF,
            Constants::TEMPLATE_EMAIL_PEDIDO_SUBSTITUICAO_CHAPA,
            $idsCauUf,
            $parametrosEmail
        );
    }

    /**
     * Atualiza o pedido de substituição após o julgamento de primeira instância
     *
     * @param int $idCalendario
     * @param PedidoSubstituicaoChapa $pedidoSubstituicaoChapa
     * @param int $idStatusJulgamento
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function atualizarPedidoSubstituicaoPosJulgamento(
        $idCalendario,
        $pedidoSubstituicaoChapa,
        $idStatusJulgamento
    )
    {
        $idStatusPedidoSubstituicao = $idStatusJulgamento == Constants::STATUS_JULGAMENTO_DEFERIDO
            ? Constants::STATUS_SUBSTITUICAO_CHAPA_DEFERIDO
            : Constants::STATUS_SUBSTITUICAO_CHAPA_INDEFERIDO;

        $this->atualizarStatusPedidoSubstituicao($pedidoSubstituicaoChapa, $idStatusPedidoSubstituicao);
    }

    /**
     * Atualiza o status do pedido de substituição
     *
     * @param PedidoSubstituicaoChapa $pedidoSubstituicaoChapa
     * @param int $idStatusPedidoSubstituicao
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function atualizarStatusPedidoSubstituicao($pedidoSubstituicaoChapa, $idStatusPedidoSubstituicao)
    {
        $pedidoSubstituicaoChapa->setStatusSubstituicaoChapa(StatusSubstituicaoChapa::newInstance([
            'id' => $idStatusPedidoSubstituicao
        ]));

        $this->getPedidoSubstituicaoChapaRepository()->persist($pedidoSubstituicaoChapa);
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
     * Atualiza o pedido de substituição após o julgamento de primeira instância
     *
     * @param int $idCalendario
     * @param PedidoSubstituicaoChapa $pedidoSubstituicaoChapa
     * @param int $idStatusJulgamento
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function atualizarPedidoSubstituicaoPosJulgamentoRecurso(
        $idCalendario,
        $pedidoSubstituicaoChapa,
        $idStatusJulgamento
    )
    {
        $idStatusPedidoSubstituicao = $idStatusJulgamento == Constants::STATUS_JULGAMENTO_DEFERIDO
            ? Constants::STATUS_SUBSTITUICAO_CHAPA_RECURSO_DEFERIDO
            : Constants::STATUS_SUBSTITUICAO_CHAPA_RECURSO_INDEFERIDO;

        $this->atualizarStatusPedidoSubstituicao($pedidoSubstituicaoChapa, $idStatusPedidoSubstituicao);
    }

    /**
     * Método realiza no final da atividade de julgamento de substituição
     * - Envia no julgamento 1ª instância
     * - Envia no julgamento 2ª instância
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailFimPeriodoJulgamento(
        $nivelAtividadePrincipal,
        $nivelAtividadeSecundaria,
        $idStatusPedidoSubstituicao,
        $idTipoEmail
    )
    {
        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            null,
            Utils::adicionarDiasData(Utils::getDataHoraZero(), 1),
            $nivelAtividadePrincipal,
            $nivelAtividadeSecundaria
        );

        foreach ($atividades as $atividadeSecundariaCalendario) {
            $idsCauUf = $this->getIdsCauUfPedidosEmAndamentoPorCalendario(
                $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId(),
                $idStatusPedidoSubstituicao
            );

            $this->getEmailAtividadeSecundariaBO()->enviarEmailAcessoresCenAndAcessoresCE(
                $atividadeSecundariaCalendario->getId(),
                $idTipoEmail,
                Constants::TEMPLATE_EMAIL_PADRAO,
                $idsCauUf
            );
        }
    }

    /**
     * Retorna os ids das cau ufs que possuem pedidos em andamento
     *
     * @param $idCalendario
     * @return array
     */
    public function getIdsCauUfPedidosEmAndamentoPorCalendario($idCalendario, $idStatusPedidoSubstituicao)
    {
        $pedidosEmAndamentos = $this->getPedidoSubstituicaoChapaRepository()->getPorCalendario(
            $idCalendario,
            $idStatusPedidoSubstituicao
        );

        $idsCauUf = [];
        if (!empty($pedidosEmAndamentos)) {
            /** @var PedidoSubstituicaoChapa $pedidoSubstituicaoChapa */
            foreach ($pedidosEmAndamentos as $pedidoSubstituicaoChapa) {
                $idCauUf = $pedidoSubstituicaoChapa->getChapaEleicao()->getIdCauUf();
                $idsCauUf[$idCauUf] = $idCauUf;
            }
        }
        return $idsCauUf;
    }

    /**
     * Retorna uma nova instância de 'MembroComissaoRepository'.
     *
     * @return MembroComissaoRepository
     */
    public function getMembroComissaoRepository(): MembroComissaoRepository
    {
        if (empty($this->membroComissaoRepository)) {
            $this->membroComissaoRepository = $this->getRepository(MembroComissao::class);
        }
        return $this->membroComissaoRepository;
    }

    /**
     * Retorna a quantidade de Pedidos de Substituiçao agrupados por UF
     *
     * @param $idCalendario
     * @return QuantidadePedidoSubstituicaoPorUfTO[]
     * @throws NegocioException
     * @throws Exception
     */
    public function getQuantidadePedidosParaCadaUf($idCalendario)
    {
        //Caso nao seja enviado o calendario, a eleicao vigente e buscada
        if (empty($idCalendario)) {
            $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();
            $idCalendario = $eleicaoVigente->getCalendario()->getId();
        }

        $isProfissional = $this->getUsuarioFactory()->hasPermissao(Constants::ROLE_PROFISSIONAL);

        if ($isProfissional) {
            $membroComissao = $this->getMembroComissaoBO()->getMembroComissaoParaVerificacaoPorCalendario($idCalendario);

            if ($membroComissao->getIdCauUf() != Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                //Exceção para redirecionar na tela para detalhamento
                throw new NegocioException(Message::MSG_VISUALIZACAO_PEDIDOS_APENAS_MEMBROS_COMISSAO_CEN_BR);
            }
        }

        /** @var QuantidadePedidoSubstituicaoPorUfTO[] $pedidosSubstituicaoEmAndamento */
        $pedidosSubstituicaoEmAndamento = [];
        $this->recuperaPedidosEmAndamento($pedidosSubstituicaoEmAndamento, $idCalendario);

        $pedidosSubstituicaoJulgados = [];
        $this->recuperaPedidosJulgados($pedidosSubstituicaoJulgados, $idCalendario);

        //Obtém todas as filiais para montar o retorno
        $filiais = $this->getFilialBO()->getListaFiliaisFormata();

        //Remoçao da CEN/BR (165) e IES(1000) da listagem de filiais
        Arr::forget($filiais, Constants::ID_CAU_BR);
        Arr::forget($filiais, Constants::ID_CAUBR_IES);

        //Para cada filial cria-se uma instancia de QuantidadePedidoSubstituicaoPorUfTO com as quantidades vazias
        /** @var QuantidadePedidoSubstituicaoPorUfTO[] $pedidosSubstituicaoComQuantidade */
        $pedidosSubstituicaoComQuantidade = array_map(function ($item) {
            return QuantidadePedidoSubstituicaoPorUfTO::newInstanceFromFilial($item);
        }, $filiais);

        //Criação da QuantidadePedidoSubstituicaoPorUfTO com a informação da IES
        $pedidoIES = QuantidadePedidoSubstituicaoPorUfTO::criaQuantidadePedidoIES();
        array_push($pedidosSubstituicaoComQuantidade, $pedidoIES);

        //Itera no array com todas as filiais com as quantidades vazias para setar as quantidades de pedidos salvos/
        foreach ($pedidosSubstituicaoComQuantidade as $pedidoSubstituicao) {
            foreach ($pedidosSubstituicaoEmAndamento as $pedidoEmAndamento) {
                if ($pedidoEmAndamento->compareTo($pedidoSubstituicao)) {
                    $pedidoSubstituicao->setQuantidadePedidos($pedidoEmAndamento->getQuantidadePedidos());
                    continue;
                }
            }
            foreach ($pedidosSubstituicaoJulgados as $pedidoJulgado) {
                if ($pedidoJulgado->compareTo($pedidoSubstituicao)) {
                    $pedidoSubstituicao->setQuantidadePedidosJulgados($pedidoJulgado->getQuantidadePedidosJulgados());
                    continue;
                }
            }
        }

        //Retorna os dados ordenados pela UF
        return array_values(Arr::sort($pedidosSubstituicaoComQuantidade, function ($value) {
            /** @var QuantidadePedidoSubstituicaoPorUfTO $value */
            return $value->getSiglaUf();
        }));
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
     * Método busca os pedidos que estejam com status em andamento
     *
     * @param array $pedidosSubstituicaoEmAndamento
     * @param int $idCalendario
     * @return void QuantidadePedidoSubstituicaoPorUfTO
     */
    private function recuperaPedidosEmAndamento(
        array &$pedidosSubstituicaoEmAndamento,
        int $idCalendario
    ): void
    {
        $dadosPorUf = $this->pedidoSubstituicaoChapaRepository->getQuantidadePedidosParaCadaUf(
            $idCalendario,
            false,
            Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR);

        if (!empty($dadosPorUf)) {
            $pedidosSubstituicaoEmAndamento = array_merge(
                $pedidosSubstituicaoEmAndamento,
                is_array($dadosPorUf) ? $dadosPorUf : [$dadosPorUf]
            );
        }

        $dadosIES = $this->pedidoSubstituicaoChapaRepository->getQuantidadePedidosParaCadaUf(
            $idCalendario,
            false,
            Constants::TIPO_CANDIDATURA_IES);

        if (!empty($dadosIES)) {
            $pedidosSubstituicaoEmAndamento = array_merge(
                $pedidosSubstituicaoEmAndamento,
                is_array($dadosIES) ? $dadosIES : [$dadosIES]
            );
        }
    }

    /**
     * Método busca os pedidos que estejam com status igual a julgado (deferido ou indeferido)
     *
     * @param array $pedidosSubstituicaoJulgados
     * @param int $idCalendario
     * @return void QuantidadePedidoSubstituicaoPorUfTO
     */
    private function recuperaPedidosJulgados(array &$pedidosSubstituicaoJulgados, $idCalendario): void
    {
        /** @var $dadosPorUfJulgado PedidoSubstituicaoChapaTO[] */
        $dadosPorUfJulgado = $this->pedidoSubstituicaoChapaRepository->getQuantidadePedidosParaCadaUf(
            $idCalendario,
            true,
            Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR);

        if (!empty($dadosPorUfJulgado)) {
            $pedidosSubstituicaoJulgados = array_merge(
                $pedidosSubstituicaoJulgados,
                is_array($dadosPorUfJulgado) ? $dadosPorUfJulgado : [$dadosPorUfJulgado]
            );
        }

        $dadosIESJulgado = $this->pedidoSubstituicaoChapaRepository->getQuantidadePedidosParaCadaUf(
            $idCalendario,
            true,
            Constants::TIPO_CANDIDATURA_IES);

        if (!empty($dadosIESJulgado)) {
            $pedidosSubstituicaoJulgados = array_merge(
                $pedidosSubstituicaoJulgados,
                is_array($dadosIESJulgado) ? $dadosIESJulgado : [$dadosIESJulgado]
            );
        }
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
     * Retorna a quantidade de Pedidos de Substituiçao agrupados por UF
     *
     * @param int|null $idCauUf
     * @return PedidoSubstituicaoChapaTO[]
     * @throws Exception
     */
    public function getPedidosPorUf(int $idCauUf = null)
    {
        $isProfissional = $this->getUsuarioFactory()->hasPermissao(Constants::ROLE_PROFISSIONAL);
        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();

        $isConselheiroCauUf = false;
        $pedidosSubstituicaoChapa = [];

        if ($isProfissional) {
            $membroComissao = $this->getMembroComissaoBO()->getMembroComissaoParaVerificacaoPorCalendario(
                $eleicaoVigente->getCalendario()->getId()
            );

            if ($membroComissao->getIdCauUf() != Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                $idCauUf = $membroComissao->getIdCauUf();
                $isConselheiroCauUf = true;
            }

            if ($idCauUf == 0) {
                $pedidosSubstituicaoChapa = $this->pedidoSubstituicaoChapaRepository->getPedidosPorIes(
                    $eleicaoVigente->getCalendario()->getId()
                );
            } else {
                $pedidosSubstituicaoChapa = $this->pedidoSubstituicaoChapaRepository->getPedidosPorUf(
                    $idCauUf,
                    $eleicaoVigente->getCalendario()->getId()
                );
            }

            if (empty($pedidosSubstituicaoChapa)) {
                if ($isConselheiroCauUf) {
                    //Caro(a) sr.(a), não existe pedido de substituição cadastrado para a UF, a qual o senhor é um conselheiro CE/UF!
                    throw new NegocioException(Message::MSG_SUBSTITUICAO_MEMBRO_COMISSAO_CEUF_SEM_PEDIDOS);
                } else {
                    //Caro(a) sr.(a), não existe pedido de substituição cadastrado para a eleição que o senhor é um conselheiro CEN/BR!
                    throw new NegocioException(Message::MSG_SUBSTITUICAO_MEMBRO_COMISSAO_CENBR_SEM_PEDIDOS);
                }
            }

            $this->ordenarPedidosSubstituicao($pedidosSubstituicaoChapa);

            $this->atribuirCauUfsPedidosSubstituicaoChapa($idCauUf, $pedidosSubstituicaoChapa);
        }

        return $pedidosSubstituicaoChapa;
    }

    /**
     * @param int $idCauUf
     * @param $pedidosSubstituicaoChapa
     * @throws NegocioException
     */
    private function atribuirCauUfsPedidosSubstituicaoChapa(int $idCauUf, $pedidosSubstituicaoChapa): void
    {
        $filiaisFormatadas = [];
        if (empty($idCauUf)) {
            $filiaisFormatadas = $this->getFilialBO()->getListaFiliaisFormata();
        } else {
            $filiaisFormatadas[$idCauUf] = $this->getFilialBO()->getPorId($idCauUf);
        }

        /** @var PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapa */
        foreach ($pedidosSubstituicaoChapa as $pedidoSubstituicaoChapa) {
            /** @var Filial $filial */
            $filial = $filiaisFormatadas[$pedidoSubstituicaoChapa->getChapaEleicao()->getIdCauUf()];
            $idTipoCandidatura = $pedidoSubstituicaoChapa->getChapaEleicao()->getTipoCandidatura()->getId();

            if (!empty($filial) && $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES) {
                $filial->setDescricao(sprintf('%s/%s', Constants::PREFIXO_IES, $filial->getPrefixo()));
            }
            $pedidoSubstituicaoChapa->getChapaEleicao()->setCauUf($filial);
        }
    }

    /**
     * Retorna a quantidade de Pedidos de Substituiçao agrupados por UF
     *
     * @param $idCalendario
     * @param $idCauUF
     * @return PedidoSubstituicaoChapaTO[]
     * @throws NegocioException
     */
    public function getPedidosPorCalendarioUf($idCalendario, $idCauUF)
    {
        $isCorporativo = $this->getUsuarioFactory()->isCorporativo();

        $pedidosSubstituicaoChapa = [];
        if ($isCorporativo) {
            $isAcessorCEN = $this->getUsuarioFactory()->isCorporativoAssessorCEN();
            $isAcessorCE = !$isAcessorCEN && $this->getUsuarioFactory()->isCorporativoAssessorCEUF();

            $idCauUfPedidos = ($isAcessorCE) ? $this->getUsuarioFactory()->getUsuarioLogado()->idCauUf : $idCauUF;

            if ($idCauUfPedidos == 0) {
                $pedidosSubstituicaoChapa = $this->pedidoSubstituicaoChapaRepository->getPedidosPorIes(
                    $idCalendario
                );
            } else {
                $pedidosSubstituicaoChapa = $this->pedidoSubstituicaoChapaRepository->getPedidosPorUf(
                    $idCauUfPedidos,
                    $idCalendario
                );
            }
            if (!empty($pedidosSubstituicaoChapa)) {
                $this->ordenarPedidosSubstituicao($pedidosSubstituicaoChapa);
                $this->atribuirCauUfsPedidosSubstituicaoChapa($idCauUfPedidos, $pedidosSubstituicaoChapa);
            }
        }

        return $pedidosSubstituicaoChapa;
    }

    /**
     * Retorna a quantidade de Pedidos de Substituiçao por chapa do Usuario Logado
     *
     * @return PedidoSubstituicaoChapaTO[]|null
     * @throws NegocioException
     * @throws Exception
     */
    public function getPedidosChapaPorResponsavelChapa()
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();

        $idChapaEleicao = $this->getChapaEleicaoRepository()->getIdChapaEleicaoPorCalendarioEResponsavel(
            $eleicaoVigente->getCalendario()->getId(),
            $usuario->idProfissional
        );

        if (empty($idChapaEleicao)) {
            throw new NegocioException(Message::MSG_VISUALIZACAO_ATIV_APENAS_MEMBROS_RESPONSAVEIS_CHAPA);
        }

        $pedidosSubstituicaoChapaTO = $this->pedidoSubstituicaoChapaRepository->getPedidosChapaPorResponsavelChapa(
            $usuario->idProfissional,
            $eleicaoVigente->getCalendario()->getId());

        if (empty($pedidosSubstituicaoChapaTO)) {
            throw new NegocioException(Message::MSG_SUBSTITUICAO_CHAPA_RESPONSAVEL_SEM_PEDIDOS);
        }

        $this->ordenarPedidosSubstituicao($pedidosSubstituicaoChapaTO);

        return $pedidosSubstituicaoChapaTO;
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoPedidoSubstituicao($id)
    {
        /** @var PedidoSubstituicaoChapa $pedidoSubstituicaoChapa */
        $pedidoSubstituicaoChapa = $this->getPedidoSubstituicaoChapaRepository()->find($id);

        if (!empty($pedidoSubstituicaoChapa)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioPedidoSubstituicaoChapa(
                $pedidoSubstituicaoChapa->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $pedidoSubstituicaoChapa->getNomeArquivoFisico(),
                $pedidoSubstituicaoChapa->getNomeArquivo()
            );
        }
    }

    /**
     *  Gerar PDF do pedido de substituição de membro da chapa.
     *
     * @param $id
     * @return ArquivoTO
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Mpdf\MpdfException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function gerarDocumentoPDFPedidoSubstituicaoMembro($id)
    {
        $pedidoSubstituicaoChapa = $this->getPorId($id);

        $filial = $this->getFilialBO()->getPorId($pedidoSubstituicaoChapa->getChapaEleicao()->getIdCauUf());
        $pedidoSubstituicaoChapa->getChapaEleicao()->setCauUf($filial);

        if (!empty($pedidoSubstituicaoChapa->getIdProfissionalInclusao())) {
            $profissionalTO = $this->getProfissionalBO()->getPorId(
                $pedidoSubstituicaoChapa->getIdProfissionalInclusao()
            );

            if (!empty($profissionalTO)) {
                $pedidoSubstituicaoChapa->setNomeProfissionalInclusao($profissionalTO->getNome());
            }
        }
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        return $this->getPdfFactory()->gerarDocumentoPDFPedidoSubstituicaoMembro(
            $pedidoSubstituicaoChapa,
            $usuarioLogado
        );
    }

    /**
     * Método retorna os pedidos cadastrados de acorodo com o id da chapa informado
     * @param $idChapa
     */
    public function getPedidosSolicitadosPorChapa($idChapa)
    {
        return $this->getPedidoSubstituicaoChapaRepository()->getPedidosSolicitadosPorChapa($idChapa);
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
     * Método auxiliar para prepara entidade para salvar
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @param int|null $ultimoProtocolo
     * @param ChapaEleicao $chapaEleicao
     * @return PedidoSubstituicaoChapa
     * @throws Exception
     */
    private function prepararPedidoSalvar($pedidoSubstituicaoChapaTO, $ultimoProtocolo, $chapaEleicao)
    {
        $nomeArquivoFisico = null;
        if (!empty($pedidoSubstituicaoChapaTO->getNomeArquivo())) {
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $pedidoSubstituicaoChapaTO->getNomeArquivo(),
                Constants::PREFIXO_ARQ_PEDIDO_SUBSTITUICAO_CHAPA
            );
        }

        $pedidoSubstituicaoChapa = PedidoSubstituicaoChapa::newInstance([
            'dataCadastro' => Utils::getData(),
            'numeroProtocolo' => $ultimoProtocolo + 1,
            'justificativa' => $pedidoSubstituicaoChapaTO->getJustificativa(),
            'nomeArquivo' => $pedidoSubstituicaoChapaTO->getNomeArquivo(),
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'statusSubstituicaoChapa' => ['id' => Constants::STATUS_SUBSTITUICAO_CHAPA_EM_ANDAMENTO]
        ]);
        $pedidoSubstituicaoChapa->setChapaEleicao($chapaEleicao);
        $pedidoSubstituicaoChapa->setIdProfissionalInclusao(
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );

        return $pedidoSubstituicaoChapa;
    }
}




