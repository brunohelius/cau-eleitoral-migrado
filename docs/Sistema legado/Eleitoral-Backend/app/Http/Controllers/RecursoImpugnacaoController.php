<?php
/*
 * ChapaEleicaoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\RecursoImpugnacaoBO;
use App\Exceptions\NegocioException;
use App\To\RecursoImpugnacaoTO;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'RecursoImpugnacao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class RecursoImpugnacaoController extends Controller
{

    /**
     * @var RecursoImpugnacaoBO
     */
    private $recursoImpugnacaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }

    /**
     * Salvar dados do recurso do julgamento de pedido de impugnação 1ª instância
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/recursosImpugnacao/salvar",
     *     tags={"Recurso do Julgamento do Pedido de Impugnação"},
     *     summary="Salvar dados do recurso do julgamento do pedido de impugnação",
     *     description="Salvar dados do recurso do julgamento do pedido de impugnação",
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

        $recursoImpugnacaoTO = RecursoImpugnacaoTO::newInstance($data);

        $recursoImpugnacaoSalvo = $this->getRecursoImpugnacaoBO()->salvar($recursoImpugnacaoTO);
        return $this->toJson($recursoImpugnacaoSalvo);
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
     *     path="/recursosImpugnacao/{id}/download",
     *     tags={"Recurso do Julgamento Substituição"},
     *     summary="Download de Documento do Recurso do Julgamento de Substituição Chapa",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Recurso de Impugnação",
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
    public function download($id)
    {
        $arquivoTO = $this->getRecursoImpugnacaoBO()->getArquivoRecursoImpugnacao($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna o Recurso do julgamento de impugnação conforme o id do pedido de impugnação informado.
     *
     * @param $idPedidoImpugnacao
     * @param $idTipoSolicitacao
     * @return string
     * @OA\Get(
     *     path="/recursosImpugnacao/pedidoImpugnacao/{idPedidoImpugnacao}/tipoSolicitacao/{idTipoSolicitacao}",
     *     tags={"Recurso do Julgamento Impugnação"},
     *     summary="Dados da Recurso Julgamento de Impugnação",
     *     description="Retorna o Recurso do julgamento de impugnação conforme o id do pedido de impugnação informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idPedidoImpugnacao",
     *         in="path",
     *         description="Id do Pedido de Impugnação",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idTipoSolicitacao",
     *         in="path",
     *         description="Id do Tipo de Solicitação",
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
    public function getPorPedidoImpugnacaoAndTipoSolicitacao($idPedidoImpugnacao, $idTipoSolicitacao)
    {
        $resp = $this->getRecursoImpugnacaoBO()->getPorPedidoImpugnacaoAndTipoSolicitacao(
            $idPedidoImpugnacao, $idTipoSolicitacao, true
        );
        return $this->toJson($resp);
    }

    /**
     * Retorna uma nova instância de 'RecursoImpugnacaoBO'.
     *
     * @return RecursoImpugnacaoBO
     */
    private function getRecursoImpugnacaoBO()
    {
        if (empty($this->recursoImpugnacaoBO)) {
            $this->recursoImpugnacaoBO = app()->make(RecursoImpugnacaoBO::class);
        }

        return $this->recursoImpugnacaoBO;
    }
}
