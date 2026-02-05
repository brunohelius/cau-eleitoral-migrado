<?php
/*
 * ChapaEleicaoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\EleicaoBO;
use App\Business\PedidoSubstituicaoChapaBO;
use App\Exceptions\NegocioException;
use App\To\PedidoSubstituicaoChapaTO;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Mpdf\MpdfException;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe de controle referente a entidade 'PedidoSubstituicaoChapa'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class PedidoSubstituicaoChapaController extends Controller
{

    /**
     * @var PedidoSubstituicaoChapaBO
     */
    private $pedidoSubstituicaoChapaBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->pedidoSubstituicaoChapaBO = app()->make(PedidoSubstituicaoChapaBO::class);
        $this->eleicaoBO = app()->make(EleicaoBO::class);
    }

    /**
     * Retorna a chapa eleição conforme o id informado.
     *
     * @param $id
     * @return string
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/pedidosSubstituicaoChapa/{id}",
     *     tags={"Chapa Eleição"},
     *     summary="Dados da chapa da eleição",
     *     description="Retorna a chapa eleição conforme o id informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Pedido de Substituição Chapa",
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
        $resp = $this->pedidoSubstituicaoChapaBO->getPorId($id);
        return $this->toJson($resp);
    }

    /**
     * Salvar dados do pedido de substituição da Chapa Eleição.
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/pedidosSubstituicaoChapa/salvar",
     *     tags={"Pedido de Substituição Chapa"},
     *     summary="Salvar dados do pedido de substituição da Chapa Eleição",
     *     description="Salvar dados do pedido de substituição da Chapa Eleição",
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

        $pedidoSubstituicaoChapaTO = PedidoSubstituicaoChapaTO::newInstance($data);

        $chapaEleicaoSalvo = $this->pedidoSubstituicaoChapaBO->salvar($pedidoSubstituicaoChapaTO);
        return $this->toJson($chapaEleicaoSalvo);
    }

    /**
     * Recupera a quantidade de pedidos de substiuição para cada UF, além da IES, com a informação de pedidos já julgados.
     *
     * @param int $idCalendario
     * @return string
     * @throws NegocioException
     * @OA\Get(
     *     path="/pedidosSubstituicaoChapa/getQuantidadePedidosParaCadaUf/{idCalendario}",
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
     */
    public function getQuantidadePedidosParaCadaUf($idCalendario = null)
    {
        return $this->toJson($this->pedidoSubstituicaoChapaBO->getQuantidadePedidosParaCadaUf($idCalendario));
    }

    /**
     * Recupera os pedidos para uma UF
     * @param $idCauUF
     *
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/pedidosSubstituicaoChapa/pedidosPorUf/{idCauUF}",
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
        return $this->toJson($this->pedidoSubstituicaoChapaBO->getPedidosPorUf($idCauUF));
    }

    /**
     * Recupera os pedidos para uma UF de um calendário específico
     * @param $idCalendario
     * @param $idCauUF
     *
     * @return string
     * @OA\Get(
     *     path="/pedidosSubstituicaoChapa/calendario/{idCalendario}/pedidosPorUf[/{idCauUF}]",
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
        return $this->toJson($this->pedidoSubstituicaoChapaBO->getPedidosPorCalendarioUf($idCalendario, $idCauUF));
    }

    /**
     * Recupera os pedidos para a chapa que o usuario logado e responsavel
     *
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/pedidosSubstituicaoChapa/getPedidosChapaPorResponsavelChapa",
     *     tags={"Pedido de Substituição Chapa"},
     *     summary="Recupera a quantidades de pedidos de substituição",
     *     description="Recupera a quantidades de pedidos de substituição",
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
    public function getPedidosChapaPorResponsavelChapa()
    {
        return $this->toJson($this->pedidoSubstituicaoChapaBO->getPedidosChapaPorResponsavelChapa());
    }

    /**
     * Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.
     *
     * @param $id
     *
     * @return Response
     * @throws NegocioException
     *
     * @OA\Get(
     *     path="/pedidosSubstituicaoChapa/{id}/download",
     *     tags={"Pedido de Substituição Chapa"},
     *     summary="Download de Documento do Pedido de Substituição Chapa",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Pedido de Substituição Chapa",
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
    public function downloadDocumento($id)
    {
        $arquivoTO = $this->pedidoSubstituicaoChapaBO->getArquivoPedidoSubstituicao($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Recupera as eleicoes que possuem pedidos
     *
     * @return string
     * @throws Exception
     *
     * @OA\Get(
     *     path="/pedidosSubstituicaoChapa/eleicoes",
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
        $eleicoes = $this->eleicaoBO->getEleicoesComPedidoSubstituicao();
        return $this->toJson($eleicoes);
    }

    /**
     * Gera e retorna documento no formato PDF do pedido de substituição.
     *
     * @param $idPedidoSubstituicao
     * @return Response
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @OA\Get(
     *     path="/pedidosSubstituicaoChapa/{idPedidoSubstituicao}/pdf",
     *     tags={"Pedido de Substituição Chapa"},
     *     summary="Recupera pedido de substituição em PDF..",
     *     description="Recupera documento no formato pdf do pedido de substituição do membro da chapa",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idPedidoSubstituicao",
     *         in="path",
     *         description="Id do Pedido de Substituição Chapa",
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
    public function getDocumentoPDFPedidoSubstituicaoMembro($idPedidoSubstituicao)
    {
        $documentoPDF = $this->pedidoSubstituicaoChapaBO->gerarDocumentoPDFPedidoSubstituicaoMembro($idPedidoSubstituicao);
        return $this->toFile($documentoPDF);
    }
}
