<?php
/*
 * PedidoImpugnacaoController* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\ArquivoPedidoImpugnacaoBO;
use App\Business\EleicaoBO;
use App\Business\PedidoImpugnacaoBO;
use App\Entities\PedidoImpugnacao;
use App\Exceptions\NegocioException;
use App\To\PedidoSubstituicaoChapaTO;
use App\To\RespostaDeclaracaoTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Exception;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Classe de controle referente a entidade 'PedidoImpugnacao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class PedidoImpugnacaoController extends Controller
{
    /**
     * @var PedidoImpugnacaoBO
     */
    private $pedidoImpugnacaoBO;

    /**
     * @var ArquivoPedidoImpugnacaoBO
     */
    private $arquivoPedidoImpugnacaoBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->pedidoImpugnacaoBO = app()->make(PedidoImpugnacaoBO::class);
        $this->eleicaoBO = app()->make(EleicaoBO::class);
    }

    /**
     * Salvar dados do pedido de impugnação da Chapa Eleição.
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/pedidosImpugnacao/salvar",
     *     tags={"Pedido de Impugnação"},
     *     summary="Salvar dados do pedido de impugnação da Chapa Eleição",
     *     description="Salvar dados do pedido de impugnação da Chapa Eleição",
     *     security={{ "Authorization": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function salvar()
    {
        $data = Input::all();

        $pedidoImpugnacao = PedidoImpugnacao::newInstance($data);
        $respostasDeclaracaoTO = $this->getRespostasDeclaracaoTO($data);

        $pedidoImpugnacao = $this->pedidoImpugnacaoBO->salvar($pedidoImpugnacao, $respostasDeclaracaoTO);
        return $this->toJson($pedidoImpugnacao);
    }

    /**
     * Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' do documento informado.
     *
     * @param $idArquivoPedidoImpugnacao
     *
     * @return Response
     * @throws NegocioException
     * @OA\Get(
     *     path="/pedidosImpugnacao/documento/{idArquivoPedidoImpugnacao}/download",
     *     tags={"Arquivo Pedido de Impugnação"},
     *     summary="Download de Documento do Pedido de Impugnação Chapa",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' do documento informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idArquivoPedidoImpugnacao",
     *         in="path",
     *         description="Id do Pedido de Impugnação Chapa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function downloadDocumento($idArquivoPedidoImpugnacao)
    {
        $arquivoTO = $this->getArquivoPedidoImpugnacaoBO()->getArquivoPedidoImpugnacao($idArquivoPedidoImpugnacao);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna a atividade secundária do pedido de impugnação.
     *
     * @return string
     * @throws Exception

     */
    public function getAtividadeSecundariaCadastroPedidoImpugnacao()
    {
        $atividadeSecundario = $this->pedidoImpugnacaoBO->getAtividadeSecundarioPedidoImpugnacao();
        return $this->toJson($atividadeSecundario);
    }

    /**
     * Método para preparar dados respostas declaração salvar pedido
     *
     * @param $data
     * @return RespostaDeclaracaoTO[]
     */
    public function getRespostasDeclaracaoTO($data)
    {
        $respostasDeclaracao = Utils::getValue('respostasDeclaracao', $data);

        $respostasDeclaracaoTO = [];

        if (!empty($respostasDeclaracao)) {
            $respostasDeclaracaoTO = array_map(function ($respostaDeclaracao) {
                return RespostaDeclaracaoTO::newInstance($respostaDeclaracao);
            }, $respostasDeclaracao);
        }

        return $respostasDeclaracaoTO;
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
     * Recupera a quantidade de pedidos de impugnaçao para cada UF, além da IES
     *
     * @param int $idCalendario
     * @return string
     * @OA\Get(
     *     path="/pedidosImpugnacao/quantidadePedidosParaCadaUf/{idCalendario}",
     *     tags={"Pedido de Substituição Chapa"},
     *     summary="Recupera a quantidades de pedidos de substituição",
     *     description="Recupera a quantidades de pedidos de substituição",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCalendario",
     *         in="path",
     *         description="Id do Calendário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     * @throws NegocioException
     */
    public function getQuantidadePedidosParaCadaUf($idCalendario = null)
    {
        return $this->toJson($this->pedidoImpugnacaoBO->getQuantidadePedidosParaCadaUf($idCalendario));
    }

    /**
     * Recupera a quantidade de pedidos de impugnaçao para cada UF, além da IES
     *
     * @param int $idCalendario
     * @return string
     * @throws NegocioException
     */
    public function getQuantidadePedidosParaCadaUfSolicitante($idCalendario = null)
    {
        return $this->toJson($this->pedidoImpugnacaoBO->getQuantidadePedidosParaCadaUf($idCalendario, true));
    }


    /**
     * Recupera a quantidade de pedidos de impugnaçao para cada UF, além da IES
     *
     * @param null $idCauUF
     * @return string
     * @throws Exception
     *
     * @OA\Get(
     *     path="/pedidosImpugnacao/pedidosPorUf/{idCauUF}",
     *     tags={"Pedido de Substituição Chapa"},
     *     summary="Recupera a quantidades de pedidos de substituição",
     *     description="Recupera a quantidades de pedidos de substituição",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCauUF",
     *         in="path",
     *         description="Id do Cau UF",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getPedidosPorUf($idCauUF = null)
    {
        return $this->toJson($this->pedidoImpugnacaoBO->getPedidosPorUf($idCauUF));
    }

    /**
     * Recupera os pedidos para uma UF de um calendário específico
     * @param $idCalendario
     * @param $idCauUF
     *
     * @return string
     * @OA\Get(
     *     path="/pedidosImpugnacao/calendario/{idCalendario}/pedidosPorUf[/{idCauUF}]",
     *     tags={"Pedido de Impugnação"},
     *     summary="Recupera os pedidos para uma UF de um calendário específico",
     *     description="Recupera os pedidos para uma UF de um calendário específico",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCalendario",
     *         in="path",
     *         description="Id do Calendário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idCauUF",
     *         in="path",
     *         description="Id do Cau UF",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     * @throws NegocioException
     */
    public function getPedidosPorCalendarioUf($idCalendario, $idCauUF = null)
    {
        return $this->toJson($this->pedidoImpugnacaoBO->getPedidosPorCalendarioUf($idCalendario, $idCauUF));
    }

    /**
     * Recupera os pedidos para a chapa que o usuario logado e responsavel
     *
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/pedidosImpugnacao/pedidosPorResponsavelChapa",
     *     tags={"Pedido de Impugnação"},
     *     summary="Recupera os pedidos para a chapa que o usuario logado e responsavel",
     *     description="Recupera os pedidos para a chapa que o usuario logado e responsavel",
     *     security={{ "Authorization": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getPedidosPorResponsavelChapa()
    {
        return $this->toJson($this->pedidoImpugnacaoBO->getPedidosPorResponsavelChapa());
    }

    /**
     * Recupera os pedidos para a chapa que o usuario logado e responsavel pela solicitação
     *
     * @param $idCauUf
     * @return string
     * @throws NegocioException
     * @OA\Get(
     *     path="/pedidosImpugnacao/pedidosPorProfissionalSolicitante/{idCauUf}",
     *     tags={"Pedido de Impugnação"},
     *     summary="Recupera os pedidos para a chapa que o usuario logado e responsavel por realizar a solicitação",
     *     description="Recupera os pedidos para a chapa que o usuario logado e responsavel",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCauUf",
     *         in="path",
     *         description="Id do Cau Uf",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getPedidosPorProfissionalSolicitante($idCauUf)
    {
        return $this->toJson($this->pedidoImpugnacaoBO->getPedidosPorProfissionalSolicitante($idCauUf));
    }

    /**
     * Retorna a chapa eleição conforme o id informado.
     *
     * @param $id
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/pedidosImpugnacao/{id}",
     *     tags={"Chapa Eleição"},
     *     summary="Dados da chapa da eleição",
     *     description="Retorna a chapa eleição conforme o id informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Pedido de Impugnação Chapa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getPorId($id)
    {
        $resp = $this->pedidoImpugnacaoBO->getPorId($id);
        return $this->toJson($resp);
    }

    /**
     * Retorna a chapa eleição conforme o id informado.
     *
     * @param $id
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/pedidosImpugnacao/{id}/chapaEleicao",
     *     tags={"Chapa Eleição"},
     *     summary="Dados da chapa da eleição",
     *     description="Retorna a chapa eleição conforme o id informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Pedido de Impugnação Chapa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getChapaEleicapPorPedido($id)
    {
        $resp = $this->pedidoImpugnacaoBO->getChapaEleicapPorPedido($id);
        return $this->toJson($resp);
    }

    /**
     * Recupera as eleicoes que possuem pedidos
     *
     * @return string
     * @throws Exception
     *
     * @OA\Get(
     *     path="/pedidosImpugnacao/eleicoes",
     *     tags={"Pedido de Substituição Chapa"},
     *     summary="Recupera as eleicoes que possuem pedidos",
     *     description="Recupera as eleicoes que possuem pedidos",
     *     security={{ "Authorization": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getEleicoesComPedido()
    {
        $eleicoes = $this->eleicaoBO->getEleicoesComPedidoImpugnacao();
        return $this->toJson($eleicoes);
    }
}
