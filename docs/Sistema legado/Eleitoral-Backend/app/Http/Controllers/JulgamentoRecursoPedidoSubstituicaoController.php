<?php
/*
 * JulgamentoSegundaInstanciaSubstituicaoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\JulgamentoRecursoPedidoSubstituicaoBO;
use App\Business\JulgamentoSegundaInstanciaRecursoPedidoSubstituicaoBO;
use App\Business\JulgamentoSegundaInstanciaSubstituicaoBO;
use App\Exceptions\NegocioException;
use App\To\JulgamentoRecursoPedidoSubstituicaoTO;
use App\To\JulgamentoSegundaInstanciaSubstituicaoTO;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'JulgamentoRecursoPedidoSubstituicaoController'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class JulgamentoRecursoPedidoSubstituicaoController extends Controller
{

    /**
     * @var JulgamentoRecursoPedidoSubstituicaoBO
     */
    private $julgamentoRecursoPedidoSubstituicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {}

    /**
     * Salva o Julgamento de Segunda Instancia de Substituição.
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/julgamentoRecursoPedidoSubstituicao/salvar",
     *     tags={"Julgamento 2ª instância de Recurso de Pedido de Substituição"},
     *     summary="Salva o Julgamento de Segunda Instancia Recurso de Pedido de Substituição.",
     *     description="Salva o Julgamento de Segunda Instancia Recurso de Pedido de Substituição.",
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
        $resp = $this->getJulgamentoRecursoPedidoSubstituicaoBO()->salvar(
            JulgamentoRecursoPedidoSubstituicaoTO::newInstance(Input::all())
        );
        return $this->toJson($resp);
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
     *     path="/julgamentoRecursoPedidoSubstituicao/{id}/download",
     *     tags={"Julgamento 2ª instância do Recurso da Substituição"},
     *     summary="Download do documento do julgamento do recurso da substituição",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Julgamento do Recurso de Segunda Instância",
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
        $arquivoTO = $this->getJulgamentoRecursoPedidoSubstituicaoBO()->getArquivo($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna o julgamento do recurso da substituicao de segunda instância conforme o id do recurso do pedido de substituição.
     *
     * @param $idRecursoPedidoSubstituicao
     * @return string
     * @throws Exception
     *
     * @OA\Get(
     *     path="/julgamentoRecursoPedidoSubstituicao/retificacoes/{idRecursoPedidoSubstituicao}",
     *     tags={"Julgamento 2ª instância do Recurso da Substituição"},
     *     summary="Dados da Julgamento do Recurso do Pedido de Substituicao",
     *     description="Retorna o julgamento do recurso da substituicao de segunda instância conforme o id do recurso do pedido de substituição",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idRecursoPedidoSubstituicao",
     *         in="path",
     *         description="Id do Recurso do Pedido de Substituição",
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
    public function getRetificacoes($idRecursoPedidoSubstituicao)
    {
        $resp = $this->getJulgamentoRecursoPedidoSubstituicaoBO()->getRetificacoes($idRecursoPedidoSubstituicao);
        return $this->toJson($resp);
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoPedidoSubstituicaoBO'.
     *
     * @return JulgamentoRecursoPedidoSubstituicaoBO
     */
    private function getJulgamentoRecursoPedidoSubstituicaoBO()
    {
        if (empty($this->julgamentoRecursoPedidoSubstituicaoBO)) {
            $this->julgamentoRecursoPedidoSubstituicaoBO = app()->make(JulgamentoRecursoPedidoSubstituicaoBO::class);
        }

        return $this->julgamentoRecursoPedidoSubstituicaoBO;
    }
}
