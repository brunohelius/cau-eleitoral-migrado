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
use App\Business\JulgamentoImpugnacaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Business\PedidoSubstituicaoChapaBO;
use App\Business\RecursoSubstituicaoBO;
use App\Entities\JulgamentoImpugnacao;
use App\Entities\JulgamentoSubstituicao;
use App\Exceptions\NegocioException;
use App\To\JulgamentoImpugnacaoTO;
use App\To\JulgamentoSubstituicaoTO;
use App\To\PedidoSubstituicaoChapaTO;
use App\To\RecursoSubstituicaoTO;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'RecursoSubstituicao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class RecursoSubstituicaoController extends Controller
{

    /**
     * @var RecursoSubstituicaoBO
     */
    private $recursoSubstituicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salvar dados do recurso do julgamento de pedido de substituição 1ª instância
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/recursosSubstituicao/salvar",
     *     tags={"Recurso do Julgamento Substituição"},
     *     summary="Salvar dados do recurso do julgamento de pedido de substituição 1ª instância",
     *     description="Salvar dados do recurso do julgamento de pedido de substituição 1ª instância",
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

        $recursoSubstituicaoTO = RecursoSubstituicaoTO::newInstance($data);

        $recursoSubstituicaoSalvo = $this->getRecursoSubstituicaoBO()->salvar($recursoSubstituicaoTO);
        return $this->toJson($recursoSubstituicaoSalvo);
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
     *     path="/recursosSubstituicao/{id}/download",
     *     tags={"Recurso do Julgamento Substituição"},
     *     summary="Download de Documento do Recurso do Julgamento de Substituição Chapa",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Recurso de Substituição",
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
        $arquivoTO = $this->getRecursoSubstituicaoBO()->getArquivoRecursoSubstituicao($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna o Recurso do julgamento de substituição conforme o id do pedido substituição informado.
     *
     * @param $idPedidoSubstituicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/recursosSubstituicao/pedidoSubstituicao/{idPedidoSubstituicao}",
     *     tags={"Recurso do Julgamento Substituição"},
     *     summary="Dados da Recurso Julgamento de Substituição",
     *     description="Retorna o Recurso do julgamento de substituição conforme o id do pedido substituição informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idPedidoSubstituicao",
     *         in="path",
     *         description="Id do Pedido de Substituição",
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
    public function getPorPedidoSubstituicao($idPedidoSubstituicao)
    {
        $resp = $this->getRecursoSubstituicaoBO()->getPorPedidoSubstituicao($idPedidoSubstituicao);
        return $this->toJson($resp);
    }

    /**
     * Retorna a atividade secundária do recurso do julgamento do pedido de substituição.
     *
     * @param $idPedidoSubstituicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/recursosSubstituicao/{idPedidoSubstituicao}/atividadeSecundariaRecurso",
     *     tags={"Atividade Secundária do Recurso do Julgamento Substituição"},
     *     summary="Retorna a atividade secundária do recurso do julgamento do pedido de substituição.",
     *     description="Retorna a atividade secundária do recurso do julgamento do pedido de substituição.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idPedidoSubstituicao",
     *         in="path",
     *         description="Id do Pedido de Substituição",
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
    public function getAtividadeSecundariaRecursoPorPedidoSubstituicao($idPedidoSubstituicao)
    {
        $atividadeSecundario = $this->getRecursoSubstituicaoBO()->getAtividadeSecundariaRecursoPorPedidoSubstituicao(
            $idPedidoSubstituicao
        );

        return $this->toJson($atividadeSecundario);
    }

    /**
     * Retorna uma nova instância de 'RecursoSubstituicaoBO'.
     *
     * @return RecursoSubstituicaoBO
     */
    private function getRecursoSubstituicaoBO()
    {
        if (empty($this->recursoSubstituicaoBO)) {
            $this->recursoSubstituicaoBO = app()->make(RecursoSubstituicaoBO::class);
        }

        return $this->recursoSubstituicaoBO;
    }
}
