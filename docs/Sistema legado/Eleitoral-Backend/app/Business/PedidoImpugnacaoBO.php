<?php
/*
 * PedidoImpugnacaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Calendario;
use App\Entities\ChapaEleicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\Profissional;
use App\Entities\StatusPedidoImpugnacao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailPedidoImpugnacaoJob;
use App\Mail\AtividadeSecundariaMail;
use App\Repository\AtividadeSecundariaCalendarioRepository;
use App\Repository\CalendarioRepository;
use App\Repository\PedidoImpugnacaoRepository;
use App\Service\CorporativoService;
use App\To\AtividadePrincipalCalendarioTO;
use App\To\AtividadeSecundariaCalendarioTO;
use App\To\EleicaoTO;
use App\To\PedidoImpugnacaoTO;
use App\To\PedidoImpugnacaoUfTO;
use App\To\QuantidadePedidoImpugnacaoPorUfTO;
use App\To\QuantidadePedidoSubstituicaoPorUfTO;
use App\To\RespostaDeclaracaoTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'PedidoImpugnacao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class PedidoImpugnacaoBO extends AbstractBO
{

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var DeclaracaoAtividadeBO
     */
    private $declaracaoAtividadeBO;

    /**
     * @var HistoricoProfissionalBO
     */
    private $historicoProfissionalBO;

    /**
     * @var RespostaDeclaracaoBO
     */
    private $respostaDeclaracaoBO;

    /**
     * @var ArquivoPedidoImpugnacaoBO
     */
    private $arquivoPedidoImpugnacaoBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var PedidoImpugnacaoRepository
     */
    private $pedidoImpugnacaoRepository;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var JulgamentoImpugnacaoBO
     */
    private $julgamentoImpugnacaoBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var AtividadeSecundariaCalendarioRepository
     */
    private $atividadeSecundariaRepository;

    /**
     * @var CalendarioRepository
     */
    private $calendarioRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->pedidoImpugnacaoRepository = $this->getRepository(PedidoImpugnacao::class);
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
     * Recupera a eleição de acordo com o id do pedido de impugnação
     *
     * @param $idPedidoImpugnacao
     * @return EleicaoTO
     */
    public function getEleicaoPorPedidoImpugnacao($idPedidoImpugnacao)
    {
        return $this->getEleicaoBO()->getEleicaoPorPedidoImpugnacao($idPedidoImpugnacao);
    }

    /**
     * Retorna a quantidade de Pedidos de Substituiçao agrupados por UF
     *
     * @param $idCalendario
     * @return QuantidadePedidoImpugnacaoPorUfTO[]
     * @throws NegocioException
     * @throws Exception
     */
    public function getQuantidadePedidosParaCadaUf($idCalendario = null, $isProfissionalSolicitante = false)
    {
        //Caso nao seja enviado o calendario, a eleicao vigente e buscada
        if (empty($idCalendario)) {
            $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();
            $idCalendario = $eleicaoVigente->getCalendario()->getId();
        }

        $isProfissional = $this->getUsuarioFactory()->hasPermissao(Constants::ROLE_PROFISSIONAL);

        $isMembroComissaoCEN = false;
        if ($isProfissional && !$isProfissionalSolicitante) {
            $membroComissao = $this->getMembroComissaoBO()->getMembroComissaoParaVerificacaoPorCalendario($idCalendario);

            if ($membroComissao->getIdCauUf() != Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                //Exceção para redirecionar na tela para detalhamento
                throw new NegocioException(Message::MSG_VISUALIZACAO_PEDIDOS_APENAS_MEMBROS_COMISSAO_CEN_BR);
            }

            $isMembroComissaoCEN = true;
        }

        /** @var QuantidadePedidoSubstituicaoPorUfTO[] $pedidosSubstituicaoEmAnalise */
        $pedidosSubstituicaoEmAnalise = [];
        $this->recuperaPedidosEmAnalise(
            $pedidosSubstituicaoEmAnalise, $idCalendario, $isMembroComissaoCEN, $isProfissionalSolicitante
        );

        if (empty($pedidosSubstituicaoEmAnalise) && $this->getUsuarioFactory()->isCorporativo()) {
            //Não há pedidos de impugnação cadastrados (Corporativo).
            throw new NegocioException(Message::NENHUM_REGISTRO_ENCONTRADO);
        }

        if (empty($pedidosSubstituicaoEmAnalise)) {
            //Não há pedidos de impugnação cadastrados.
            throw new NegocioException(Message::MSG_SEM_PEDIDOS_IMPUGNACAO);
        }

        //Para cada filial cria-se uma instancia de QuantidadePedidoSubstituicaoPorUfTO com as quantidades vazias
        /** @var QuantidadePedidoSubstituicaoPorUfTO[] $pedidosSubstituicaoEmAndamento */
        foreach ($pedidosSubstituicaoEmAnalise as $pedido) {
            if ($pedido->getIdCauUf() == 0) {
                $pedido->setSiglaUf(Constants::PREFIXO_IES);
            } else {
                $filial = $this->getFilialBO()->getPorId($pedido->getIdCauUf());
                $pedido->setSiglaUf($this->getFilialBO()->getPorId($pedido->getIdCauUf())->getDescricao());
            }
        }

        //Retorna os dados ordenados pela UF
        return array_values(Arr::sort($pedidosSubstituicaoEmAnalise, function ($value) {
            /** @var QuantidadePedidoSubstituicaoPorUfTO $value */
            return $value->getSiglaUf();
        }));
    }

    /**
     * Retorna a quantidade de Pedidos de para uma UF
     *
     * @param int|null $idCauUf
     * @return PedidoImpugnacaoUfTO[]
     * @throws Exception
     */
    public function getPedidosPorUf(int $idCauUf = null)
    {
        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();
        $isProfissional = $this->getUsuarioFactory()->isProfissional();

        $pedidosImpugnacao = [];

        if ($isProfissional) {
            $membroComissao = $this->getMembroComissaoBO()->getMembroComissaoParaVerificacaoPorCalendario(
                $eleicaoVigente->getCalendario()->getId(), null, false
            );

            if (empty($membroComissao)) {
                throw new NegocioException(Message::MSG_PEDIDOS_IMPUGNACAO_APENAS_MEMBROS_COMISSAO);
            }

            if ($membroComissao->getIdCauUf() != Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                $idCauUf = $membroComissao->getIdCauUf();
            }

            if ($idCauUf === 0) {
                $pedidosImpugnacao = $this->pedidoImpugnacaoRepository->getPedidosPorIes(
                    $eleicaoVigente->getCalendario()->getId()
                );
            } else {
                $pedidosImpugnacao = $this->pedidoImpugnacaoRepository->getPedidosPorUf(
                    $idCauUf,
                    $eleicaoVigente->getCalendario()->getId()
                );
            }
        }

        if (empty($pedidosImpugnacao)) {
            //Não há pedidos de impugnação cadastrados.
            throw new NegocioException(Message::MSG_SEM_PEDIDOS_IMPUGNACAO);
        }

        $this->ordernarPedidosImpugnacao($pedidosImpugnacao);

        return [
            'idCalendario' => $eleicaoVigente->getCalendario()->getId(),
            'pedidosImpugnacao' => $pedidosImpugnacao
        ];
    }

    /**
     * Retorna a quantidade de Pedidos de para uma UF
     *
     * @param $idCalendario
     * @param $idCauUF
     * @return PedidoImpugnacaoUfTO[]
     * @throws NegocioException
     */
    public function getPedidosPorCalendarioUf($idCalendario, $idCauUF)
    {
        $pedidosImpugnacao = [];
        if ($this->getUsuarioFactory()->isCorporativo()) {
            $isAcessorCEN = $this->getUsuarioFactory()->isCorporativoAssessorCEN();
            $isAcessorCE = !$isAcessorCEN && $this->getUsuarioFactory()->isCorporativoAssessorCEUF();

            $idCauUfPedidos = ($isAcessorCE) ? $this->getUsuarioFactory()->getUsuarioLogado()->idCauUf : $idCauUF;

            if ($idCauUfPedidos == 0) {
                $pedidosImpugnacao = $this->pedidoImpugnacaoRepository->getPedidosPorIes(
                    $idCalendario
                );
            } else {
                $pedidosImpugnacao = $this->pedidoImpugnacaoRepository->getPedidosPorUf(
                    $idCauUF,
                    $idCalendario
                );
            }

            if (empty($pedidosImpugnacao)) {
                //Não há pedidos de impugnação cadastrados
                throw new NegocioException(Message::NENHUM_REGISTRO_ENCONTRADO);
            }

            $this->ordernarPedidosImpugnacao($pedidosImpugnacao);
        }

        return [
            'idCalendario' => $idCalendario,
            'pedidosImpugnacao' => $pedidosImpugnacao
        ];
    }

    /**
     * Retorna a quantidade de Pedidos de Substituiçao por chapa do Usuario Logado
     *
     * @return PedidoImpugnacaoUfTO[]|null
     * @throws NegocioException
     * @throws Exception
     */
    public function getPedidosPorResponsavelChapa()
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();

        $idChapaEleicao = $this->getChapaEleicaoBO()->getIdChapaEleicaoPorCalendarioEResponsavel(
            $eleicaoVigente->getCalendario()->getId(),
            $usuario->idProfissional
        );

        if (empty($idChapaEleicao)) {
            throw new NegocioException(Message::MSG_PEDIDOS_IMPUGNACAO_APENAS_RESPONSAVEL_CHAPA);
        }

        $pedidosImpugnacao = $this->pedidoImpugnacaoRepository->getPedidosPorResponsavelChapa(
            $eleicaoVigente->getCalendario()->getId(),
            $usuario->idProfissional
        );

        if (empty($pedidosImpugnacao)) {
            throw new NegocioException(Message::MSG_SEM_PEDIDOS_IMPUGNACAO);
        }

        $this->ordernarPedidosImpugnacao($pedidosImpugnacao);

        return [
            'idCalendario' => $eleicaoVigente->getCalendario()->getId(),
            'pedidosImpugnacao' => $pedidosImpugnacao
        ];
    }

    /**
     * Retorna a quantidade de Pedidos de Substituiçao por chapa do Usuario Responsável pela solicitação
     *
     * @return PedidoImpugnacaoUfTO[]|null
     * @throws NegocioException
     * @throws Exception
     */
    public function getPedidosPorProfissionalSolicitante($idCauUf)
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();

        $idTipoCandidatura = Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR;
        if ($idCauUf == 0) {
            $idCauUf = null;
            $idTipoCandidatura = Constants::TIPO_CANDIDATURA_IES;
        }
        $pedidosImpugnacao = $this->pedidoImpugnacaoRepository->getPedidosPorProfissionalSolicitante(
            $eleicaoVigente->getCalendario()->getId(),
            $usuario->idProfissional,
            $idTipoCandidatura,
            $idCauUf
        );

        if (empty($pedidosImpugnacao)) {
            throw new NegocioException(Message::MSG_SEM_PEDIDOS_IMPUGNACAO);
        }

        $this->ordernarPedidosImpugnacao($pedidosImpugnacao);

        return [
            'idCalendario' => $eleicaoVigente->getCalendario()->getId(),
            'pedidosImpugnacao' => $pedidosImpugnacao
        ];
    }

    /**
     * Retorna o pedido de substituição chapa conforme o id informado.
     *
     * @param $id
     *
     * @return PedidoImpugnacaoTO
     * @throws Exception
     */
    public function getPorId($id)
    {
        $pedido = $this->pedidoImpugnacaoRepository->getPorId($id);

        $pedidoImpugnacaoTO = null;
        if (!empty($pedido)) {
            $pedidoImpugnacaoTO = PedidoImpugnacaoTO::newInstance($pedido);

            $eleicao = $this->getEleicaoBO()->getEleicaoPorPedidoImpugnacao($id, true);

            $pedidoImpugnacaoTO->iniciarFlags();

            /** @var AtividadePrincipalCalendarioTO $atividadePrincipalTO */
            foreach ($eleicao->getCalendario()->getAtividadesPrincipais() as $atividadePrincipalTO) {

                /** @var AtividadeSecundariaCalendarioTO $atividadeSecundariaTO */
                foreach ($atividadePrincipalTO->getAtividadesSecundarias() as $atividadeSecundariaTO) {
                    $dataInicio = Utils::getDataHoraZero($atividadeSecundariaTO->getDataInicio());
                    $isIniciadoAtividade = Utils::getDataHoraZero() >= $dataInicio;

                    $dataFim = Utils::getDataHoraZero($atividadeSecundariaTO->getDataFim());
                    $isFinalizadoAtividade = Utils::getDataHoraZero() > $dataFim;

                    if ($atividadePrincipalTO->getNivel() == 3 && $atividadeSecundariaTO->getNivel() == 2) {
                        $pedidoImpugnacaoTO->setIsIniciadoAtividadeDefesa($isIniciadoAtividade);
                        $pedidoImpugnacaoTO->setIsFinalizadoAtividadeDefesa($isFinalizadoAtividade);
                    }

                    if ($atividadePrincipalTO->getNivel() == 3 && $atividadeSecundariaTO->getNivel() == 4) {
                        $pedidoImpugnacaoTO->setIsIniciadoAtividadeRecurso($isIniciadoAtividade);
                        $pedidoImpugnacaoTO->setIsFinalizadoAtividadeRecurso($isFinalizadoAtividade);
                    }

                    if ($atividadePrincipalTO->getNivel() == 3 && $atividadeSecundariaTO->getNivel() == 5) {
                        $pedidoImpugnacaoTO->setIsIniciadoAtividadeContrarrazao($isIniciadoAtividade);
                        $pedidoImpugnacaoTO->setIsFinalizadoAtividadeContrarrazao($isFinalizadoAtividade);
                    }

                    if ($atividadePrincipalTO->getNivel() == 3 && $atividadeSecundariaTO->getNivel() == 6) {
                        $pedidoImpugnacaoTO->setIsIniciadoAtividadeJulgamentoRecurso($isIniciadoAtividade);
                        $pedidoImpugnacaoTO->setIsFinalizadoAtividadeJulgamentoRecurso($isFinalizadoAtividade);
                    }

                    if ($atividadePrincipalTO->getNivel() == 3 && $atividadeSecundariaTO->getNivel() == 7) {
                        $pedidoImpugnacaoTO->setIsIniciadoAtividadeSubstituicao($isIniciadoAtividade);
                        $pedidoImpugnacaoTO->setIsFinalizadoAtividadeSubstituicao($isFinalizadoAtividade);
                    }
                }
            }
        }

        return $pedidoImpugnacaoTO;
    }

    /**
     * Retorna o pedido de substituição chapa conforme o id informado.
     *
     * @param $id
     *
     * @return ChapaEleicao
     * @throws Exception
     */
    public function getChapaEleicapPorPedido($id)
    {
        return $this->getChapaEleicaoBO()->getPorPedidoImpugnacao($id);
    }

    /**
     * Método busca os pedidos que estejam com status em analise
     *
     * @param array $pedidosSubstituicaoEmAnalise
     * @param int $idCalendario
     * @param bool $isMembroComissaoCEN
     * @param bool $isProfissionalSolicitante
     * @return void QuantidadePedidoSubstituicaoPorUfTO
     */
    private function recuperaPedidosEmAnalise(
        array &$pedidosSubstituicaoEmAnalise,
        int $idCalendario,
        $isMembroComissaoCEN = false,
        $isProfissionalSolicitante = false
    ) {
        //Caso seja Assessor CE UF deve trazer apenas a quantidade de pedidos das chapas da UF do usuario
        if (($this->getUsuarioFactory()->isCorporativo() &&
                $this->getUsuarioFactory()->isCorporativoAssessorCEN()) || $isMembroComissaoCEN || $isProfissionalSolicitante) {

            $idProfissional = null;
            if ($isProfissionalSolicitante) {
                $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
                $idProfissional = $usuario->idProfissional;
            }

            //Caso seja conselheiro CEN ou Asessor CEN exibe os pedidos de todas as UF's, incluindo a IES
            $dadosPorUfEmAnalise = $this->pedidoImpugnacaoRepository->getQuantidadePedidosParaCadaUf(
                $idCalendario,
                false,
                Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR,
                $idProfissional);

            if (!empty($dadosPorUfEmAnalise)) {
                $pedidosSubstituicaoEmAnalise = array_merge(
                    $pedidosSubstituicaoEmAnalise,
                    is_array($dadosPorUfEmAnalise) ? $dadosPorUfEmAnalise : [$dadosPorUfEmAnalise]
                );
            }

            $dadosIESEmAnalise = $this->pedidoImpugnacaoRepository->getQuantidadePedidosParaCadaUf(
                $idCalendario,
                false,
                Constants::TIPO_CANDIDATURA_IES, $idProfissional);

            if (!empty($dadosIESEmAnalise)) {
                array_push($pedidosSubstituicaoEmAnalise, $dadosIESEmAnalise);
            }
        } else {
            $idCauUf = $this->getUsuarioFactory()->getUsuarioLogado()->idCauUf;

            $dadosParaUfEmAnalise = $this->pedidoImpugnacaoRepository->getQuantidadePedidosParaUf(
                $idCalendario,
                false,
                $idCauUf);

            if (!empty($dadosParaUfEmAnalise)) {
                array_push($pedidosSubstituicaoEmAnalise, $dadosParaUfEmAnalise);
            }
        }
    }

    /**
     * Retorna o pedido de impugnação chapa conforme o id informado.
     *
     * @param $id
     *
     * @return PedidoImpugnacao|null
     */
    public function findById($id)
    {
        /** @var PedidoImpugnacao $pedidoImpugnacao */
        $pedidoImpugnacao = $this->pedidoImpugnacaoRepository->find($id);

        return $pedidoImpugnacao;
    }

    /**
     * Salva o pedido de impugnação da chapa/membro
     *
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @param RespostaDeclaracaoTO[] $respostasDeclaracaoTO
     * @return mixed
     * @throws NegocioException
     * @throws \Exception
     */
    public function salvar($pedidoImpugnacao, $respostasDeclaracaoTO)
    {
        $eleicaoTO = $this->getChapaEleicaoBO()->getEleicaoVigenteCadastroImpugnacaoChapa();

        $this->validarCadastroPedidoImpugnacao($eleicaoTO, $pedidoImpugnacao);

        $atividadeSecundaria = $this->getAtividadeSecundariaRepository()->getPorCalendario(
            $eleicaoTO->getCalendario()->getId(), 3, 1
        );

        $declaracoesAtividade = $this->getDeclaracaoAtividadeBO()->getDeclaracoesAtividadePorAtividadeSecundaria(
            $atividadeSecundaria->getId(), true
        );

        if (!empty($declaracoesAtividade)) {
            $this->getRespostaDeclaracaoBO()->validarRespostasDeclaracao($respostasDeclaracaoTO, $declaracoesAtividade);
        }

        $this->getArquivoPedidoImpugnacaoBO()->validarArquivosDocumentoComprobatorio(
            $pedidoImpugnacao->getArquivosPedidoImpugnacao()
        );

        try {
            $this->beginTransaction();

            $arquivosPedidoImpugnacao = $pedidoImpugnacao->getArquivosPedidoImpugnacao();

            $this->prepararPedidoImpugnacaoIncluir($pedidoImpugnacao, $eleicaoTO);

            /** @var PedidoImpugnacao $pedidoImpugnacaoSalvo */
            $pedidoImpugnacaoSalvo = $this->pedidoImpugnacaoRepository->persist($pedidoImpugnacao);

            if (!empty($declaracoesAtividade)) {
                $this->getRespostaDeclaracaoBO()->salvarParaPedidoImpugnacao(
                    $pedidoImpugnacaoSalvo, $respostasDeclaracaoTO, $declaracoesAtividade
                );
            }

            $this->getArquivoPedidoImpugnacaoBO()->salvar($pedidoImpugnacaoSalvo, $arquivosPedidoImpugnacao);

            $this->salvarHistoricoPedidoImpugnacao($pedidoImpugnacaoSalvo);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailPedidoImpugnacaoJob($pedidoImpugnacaoSalvo->getId()));

        return PedidoImpugnacaoTO::newInstanceFromEntity($pedidoImpugnacaoSalvo);
    }

    /**
     *
     *
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @param int $idStatusJulgamento
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function atualizarPedidoImpugnacaoPosJulgamento(
        $pedidoImpugnacao,
        $idStatusJulgamento
    ) {
        $idStatusPedidoImpugnacao = $idStatusJulgamento == Constants::STATUS_JULG_IMPUGNACAO_PROCEDENTE
            ? Constants::STATUS_IMPUGNACAO_PROCEDENTE
            : Constants::STATUS_IMPUGNACAO_IMPROCEDENTE;

        $this->atualizarStatusPedido($pedidoImpugnacao, $idStatusPedidoImpugnacao);
    }

    /**
     * Salva o status do pedido de impugnação após julgamento de reucrso
     *
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @param int $idStatusJulgamento
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function atualizarPedidoImpugnacaoPosJulgamentoRecurso($pedidoImpugnacao, $idStatusJulgamento)
    {
        $idStatusPedidoImpugnacao = $idStatusJulgamento == Constants::STATUS_JULG_IMPUGNACAO_PROCEDENTE
            ? Constants::STATUS_IMPUGNACAO_RECURSO_PROCEDENTE
            : Constants::STATUS_IMPUGNACAO_RECURSO_IMPROCEDENTE;

        $this->atualizarStatusPedido($pedidoImpugnacao, $idStatusPedidoImpugnacao);
    }

    /**
     * Atualiza o status do pedido de acrodo com o id do status passado
     *
     * @param $pedidoImpugnacao
     * @param $idStatusPedidoImpugnacao
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function atualizarStatusPedido($pedidoImpugnacao, $idStatusPedidoImpugnacao)
    {
        $pedidoImpugnacao->setStatusPedidoImpugnacao(StatusPedidoImpugnacao::newInstance([
            'id' => $idStatusPedidoImpugnacao
        ]));

        $this->pedidoImpugnacaoRepository->persist($pedidoImpugnacao);
    }

    /**
     * Responsável por enviar emails após cadastrar pedido substituição chapa
     *
     * @param $idPedidoImpugnacao
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailsPedidoImpugnacaoIncluido($idPedidoImpugnacao)
    {
        /** @var PedidoImpugnacao $pedidoImpugnacao */
        $pedidoImpugnacao = $this->pedidoImpugnacaoRepository->find($idPedidoImpugnacao);

        $idAtivSecundaria = $this->pedidoImpugnacaoRepository->getIdAtividadeSecundariaPedidoImpugnacao(
            $idPedidoImpugnacao
        );

        $calendario = $this->getCalendarioRepository()->getPorAtividadeSecundaria($idAtivSecundaria);

        $parametrosEmail = $this->prepararParametrosEmailCadastroPedidoImpugnacao(
            $pedidoImpugnacao, $calendario
        );

        // enviar e-mail informativo para responsável cadastro
        $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
            $idAtivSecundaria,
            [$pedidoImpugnacao->getProfissional()->getPessoa()->getEmail()],
            Constants::EMAIL_IMPUGNACAO_RESPONSAVEL_CADASTRO,
            Constants::TEMPLATE_EMAIL_PEDIDO_IMPUGNACAO,
            $parametrosEmail
        );

        // enviar e-mail informativo para conselheiros CEN e a comissão UF
        /*$this->getEmailAtividadeSecundariaBO()->enviarEmailConselheirosCoordenadoresComissao(
            $idAtivSecundaria,
            Constants::EMAIL_IMPUGNACAO_COMISSAO_ELEITORAL,
            Constants::TEMPLATE_EMAIL_PEDIDO_IMPUGNACAO,
            $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getIdCauUf(),
            $parametrosEmail
        );*/

        // enviar e-mail informativo para os acessores CEN/BR e CE
        $this->enviarEmailAcessoresCenAndAcessoresCE($idAtivSecundaria, $pedidoImpugnacao, $parametrosEmail);
    }

    /**
     * Retorna a atividade de secundária do pedido de impugnação
     *
     * @throws \Exception
     */
    public function getAtividadeSecundarioPedidoImpugnacao()
    {
        $eleicaoTO = $this->getChapaEleicaoBO()->getEleicaoVigenteCadastroImpugnacaoChapa();

        if (empty($eleicaoTO)) {
            throw new NegocioException(Message::MSG_PERIODO_CADASTRO_IMPUGNACAO_NAO_VIGENTE);
        }

        $atividadeSecundaria = $this->getAtividadeSecundariaRepository()->getPorCalendario(
            $eleicaoTO->getCalendario()->getId(), 3, 1
        );

        return AtividadeSecundariaCalendarioTO::newInstanceFromEntity($atividadeSecundaria);
    }

    /**
     * Retorna todos os pedidos de acordo com o id do calendário
     *
     * @param $idCalendario
     * @return PedidoImpugnacao[]|null
     */
    public function getPorCalendario($idCalendario)
    {
        return $this->pedidoImpugnacaoRepository->getPorCalendario($idCalendario);
    }

    /**
     * Retorna os ids das cau ufs que possuem pedidos em andamento
     *
     * @param $idCalendario
     * @return array
     */
    public function getIdsCauUfPedidosEmAndamentoPorCalendario($idCalendario, $idStatusPedidoImpugnacao)
    {
        $pedidosEmAndamentos = $this->pedidoImpugnacaoRepository->getPorCalendario(
            $idCalendario,
            $idStatusPedidoImpugnacao
        );

        $idsCauUf = [];
        if (!empty($pedidosEmAndamentos)) {
            /** @var PedidoImpugnacao $pedidoImpugnacao */
            foreach ($pedidosEmAndamentos as $pedidoImpugnacao) {
                $idCauUf = $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getIdCauUf();
                $idsCauUf[$idCauUf] = $idCauUf;
            }
        }
        return $idsCauUf;
    }

    /**
     * Recupera os destinatarios do email de acordo com o pedido de impugnaçao e atividade secundaria
     *
     * @param PedidoImpugnacao|null $pedidoImpugnacao
     * @param int $idAtividadeSecundaria
     * @param bool $isAdicionarEmailsResponsaveis
     * @return array
     * @throws NegocioException
     */
    public function recuperaDestinatariosPorPedidoImpugnacao(
        PedidoImpugnacao $pedidoImpugnacao,
        int $idAtividadeSecundaria,
        bool $isAdicionarEmailsResponsaveis = true
    ): array {
        $idTipoCandidatura = $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getTipoCandidatura()->getId();

        $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
        $idCauUf = $isIES ? null : $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getIdCauUf();

        $destinariosComissao = [];
        /*$destinariosComissao = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $idAtividadeSecundaria, $idCauUf
        );*/

        $destinariosAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        $emailsResponsaveis = [];
        if ($isAdicionarEmailsResponsaveis) {
            $emailsResponsaveis = $this->getEmailsResponsaveis($pedidoImpugnacao);
        }

        return array_unique(array_merge($destinariosComissao, $destinariosAssessores, $emailsResponsaveis));
    }

    /**
     * Responsáveis por retornar os e-mails dos responsveis chapa e impugnante
     *
     * @param PedidoImpugnacao|PedidoImpugnacaoTO $pedidoImpugnacao
     */
    public function getEmailsResponsaveis($pedidoImpugnacao)
    {
        $emailsResponsaveis = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa(
            $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getId()
        );

        $emailsResponsaveis[] = $pedidoImpugnacao->getProfissional()->getPessoa()->getEmail();

        return $emailsResponsaveis;
    }

    /**
     * Método realiza o envio de e-mail no fim das atividades de julgamento
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailFimPeriodoJulgamento(
        $nivelAtivPrincipal,
        $nivelAtivSecundaria,
        $idStatusPedidoImpugnacao,
        $idTipoEmail
    ) {
        $dataFim = Utils::adicionarDiasData(Utils::getDataHoraZero(), 1);

        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            null, $dataFim, $nivelAtivPrincipal, $nivelAtivSecundaria
        );

        foreach ($atividades as $atividadeSecundariaCalendario) {

            $idsCauUf = $this->getIdsCauUfPedidosEmAndamentoPorCalendario(
                $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId(),
                $idStatusPedidoImpugnacao
            );

            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE($idsCauUf);

            if (!empty($destinatarios)) {
                $emailAtivSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                    $atividadeSecundariaCalendario->getId(), $idTipoEmail
                );

                if (!empty($emailAtivSecundaria)) {
                    $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtivSecundaria);
                    $emailTO->setDestinatarios($destinatarios);

                    Email::enviarMail(new AtividadeSecundariaMail($emailTO));
                }
            }
        }
    }

    /**
     * Método retorna os pedidos cadastrados de acorodo com o id da chapa informado
     * @param $idChapa
     */
    public function getPedidosSolicitadosPorChapa($idChapa)
    {
        return $this->pedidoImpugnacaoRepository->getPedidosSolicitadosPorChapa($idChapa);
    }

    /**
     * Método faz o envio de e-mails para os acessores CEN/BR e CE/UF após o cadatro do pedido de substituição
     *
     * @param int $idAtivSecundaria
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @param array $parametrosEmail
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailAcessoresCenAndAcessoresCE(
        int $idAtivSecundaria,
        PedidoImpugnacao $pedidoImpugnacao,
        array $parametrosEmail
    ) {
        $idTipoCandidatura = $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getTipoCandidatura()->getId();

        $idsCauUf = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR
            ? [$pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getIdCauUf()]
            : null;

        $this->getEmailAtividadeSecundariaBO()->enviarEmailAcessoresCenAndAcessoresCE(
            $idAtivSecundaria,
            Constants::EMAIL_IMPUGNACAO_ASSESSOR_CEN_E_CE,
            Constants::TEMPLATE_EMAIL_PEDIDO_IMPUGNACAO,
            $idsCauUf,
            $parametrosEmail
        );
    }

    /**
     * Validação inicial do cadastro de pedido de impugnação
     *
     * @param EleicaoTO $eleicaoTO
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @throws NegocioException
     */
    private function validarCadastroPedidoImpugnacao($eleicaoTO, $pedidoImpugnacao)
    {
        if (empty($eleicaoTO)) {
            throw new NegocioException(Message::MSG_PERIODO_CADASTRO_IMPUGNACAO_NAO_VIGENTE);
        }

        $isProfissional = $this->getUsuarioFactory()->hasPermissao(Constants::ROLE_PROFISSIONAL);
        if (!$isProfissional) {
            throw new NegocioException(Message::MSG_VISUALIZACAO_APENAS_ARQUITETOS_URBANISTAS);
        }

        if (empty($pedidoImpugnacao->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($pedidoImpugnacao->getMembroChapa()) || empty($pedidoImpugnacao->getMembroChapa()->getId())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $this->verificarDuplicacaoPedidoImpugnacao(
            $eleicaoTO->getCalendario()->getId(), $pedidoImpugnacao->getMembroChapa()->getId()
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param PedidoImpugnacao $pedidoImpugnacaoSalvo
     * @throws \Exception
     */
    private function salvarHistoricoPedidoImpugnacao(PedidoImpugnacao $pedidoImpugnacaoSalvo): void
    {
        $historico = $this->getHistoricoProfissionalBO()->criarHistorico(
            $pedidoImpugnacaoSalvo->getId(),
            Constants::HISTORICO_PROF_TIPO_PEDIDO_IMPUGNACAO,
            Constants::HISTORICO_PROF_ACAO_INSERIR,
            Constants::HISTORICO_PROF_DESCRICAO_ACAO_INSERIR
        );
        $this->getHistoricoProfissionalBO()->salvar($historico);
    }

    /**
     * M<étodo auxiliar que prepara pedido impugnação para inclusão
     *
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @param EleicaoTO $eleicaoTO
     * @throws \Exception
     */
    private function prepararPedidoImpugnacaoIncluir(
        PedidoImpugnacao $pedidoImpugnacao,
        EleicaoTO $eleicaoTO
    ): void {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        $ultimoProtocolo = $this->pedidoImpugnacaoRepository->getUltimoProtocoloPorCalendario(
            $eleicaoTO->getCalendario()->getId()
        );

        $pedidoImpugnacao->setDataCadastro(Utils::getData());
        $pedidoImpugnacao->setNumeroProtocolo(++$ultimoProtocolo);
        $pedidoImpugnacao->setProfissional(Profissional::newInstance([
            'id' => $usuarioLogado->idProfissional
        ]));
        $pedidoImpugnacao->setStatusPedidoImpugnacao(StatusPedidoImpugnacao::newInstance([
            'id' => Constants::STATUS_IMPUGNACAO_EM_ANALISE
        ]));

        $pedidoImpugnacao->setArquivosPedidoImpugnacao(null);
        $pedidoImpugnacao->setRespostasDeclaracaoPedidoImpugnacao(null);
    }

    /**
     * Método auxiliar que prepara os parâmetros para o envio de e-mails
     *
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @param Calendario $calendario
     * @return array
     * @throws NegocioException
     */
    public function prepararParametrosEmailCadastroPedidoImpugnacao(
        PedidoImpugnacao $pedidoImpugnacao,
        Calendario $calendario
    ): array {

        $this->getChapaEleicaoBO()->definirFilialChapa($pedidoImpugnacao->getMembroChapa()->getChapaEleicao());
        $descricaoCauUF = $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getCauUf()->getDescricao();
        $numeroChapa = $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getNumeroChapa();

        $profissional = $pedidoImpugnacao->getMembroChapa()->getProfissional();

        $parametrosEmail = [
            Constants::PARAMETRO_EMAIL_NM_PROTOCOLO => $pedidoImpugnacao->getNumeroProtocolo(),
            Constants::PARAMETRO_EMAIL_DS_ELEICAO => $calendario->getEleicao()->getDescricao(),
            Constants::PARAMETRO_EMAIL_NOME_CANDIDATO => $profissional->getNome(),
            Constants::PARAMETRO_EMAIL_NUM_CHAPA => $numeroChapa,
            Constants::PARAMETRO_EMAIL_PREFIXO_UF => $descricaoCauUF,
            Constants::PARAMETRO_EMAIL_JUSTIFICATIVA => $pedidoImpugnacao->getDescricao(),
        ];
        return $parametrosEmail;
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
     * Retorna uma nova instância de 'RespostaDeclaracaoBO'.
     *
     * @return RespostaDeclaracaoBO
     */
    private function getRespostaDeclaracaoBO()
    {
        if (empty($this->respostaDeclaracaoBO)) {
            $this->respostaDeclaracaoBO = app()->make(RespostaDeclaracaoBO::class);
        }

        return $this->respostaDeclaracaoBO;
    }

    /**
     * Retorna uma nova instância de 'ArquivoPedidoImpugnacaoBO'.
     *
     * @return ArquivoPedidoImpugnacaoBO
     */
    private function getArquivoPedidoImpugnacaoBO()
    {
        if (empty($this->arquivoPedidoImpugnacaoBO)) {
            $this->arquivoPedidoImpugnacaoBO = app()->make(ArquivoPedidoImpugnacaoBO::class);
        }

        return $this->arquivoPedidoImpugnacaoBO;
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
     * Retorna uma nova instância de 'AtividadeSecundariaCalendarioRepository'.
     *
     * @return AtividadeSecundariaCalendarioRepository|mixed
     */
    private function getAtividadeSecundariaRepository()
    {
        if (empty($this->atividadeSecundariaRepository)) {
            $this->atividadeSecundariaRepository = $this->getRepository(AtividadeSecundariaCalendario::class);
        }

        return $this->atividadeSecundariaRepository;
    }

    /**
     * Retorna uma nova instância de 'CalendarioRepository'.
     *
     * @return CalendarioRepository|mixed
     */
    private function getCalendarioRepository()
    {
        if (empty($this->calendarioRepository)) {
            $this->calendarioRepository = $this->getRepository(Calendario::class);
        }

        return $this->calendarioRepository;
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
     * @param array $pedidosImpugnacao
     */
    public function ordernarPedidosImpugnacao(array &$pedidosImpugnacao)
    {
        usort($pedidosImpugnacao, function ($obj1, $obj2) {
            return $obj1->getId() > $obj2->getId();
        });
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
     * Veririca se tem duplicidade do pedido de impugnação
     *
     * @param $idCalendario
     * @param $idMembroChapa
     * @throws NegocioException
     */
    public function verificarDuplicacaoPedidoImpugnacao($idCalendario, $idMembroChapa): void
    {
        $pedidos = $this->pedidoImpugnacaoRepository->getPorCalendarioAndMembroChapa(
            $idCalendario,
            $idMembroChapa,
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );
        if (!empty($pedidos)) {
            throw new NegocioException(Message::MSG_DUPLICACAO_PEDIDO_IMPUGNACAO);
        }
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
     * Retorna uma nova instância de 'JulgamentoImpugnacaoBO'.
     *
     * @return JulgamentoImpugnacaoBO
     */
    private function getJulgamentoImpugnacaoBO()
    {
        if (empty($this->julgamentoImpugnacaoBO)) {
            $this->julgamentoImpugnacaoBO = app()->make(JulgamentoImpugnacaoBO::class);
        }

        return $this->julgamentoImpugnacaoBO;
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

}




