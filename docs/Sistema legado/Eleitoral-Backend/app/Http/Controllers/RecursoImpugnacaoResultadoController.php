<?php


namespace App\Http\Controllers;

use App\Business\RecursoImpugnacaoResultadoBO;
use App\Exceptions\NegocioException;
use App\To\RecursoImpugnacaoResultadoTO;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'RecursoImpugnacaoResultado'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class RecursoImpugnacaoResultadoController extends Controller
{

    /**
     * @var RecursoImpugnacaoResultadoBO
     */
    private $recursoImpugnacaoResultadoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->recursoImpugnacaoResultadoBO = app()->make(RecursoImpugnacaoResultadoBO::class);
    }

    /**
     * Salvar Recurso do julgamento do Impugnacao de Resultado
     *
     * @return string
     * @throws Exception
     * @throws \Exception
     * @OA\Post(
     *     path="/recursoJulgamentoImpugnacaoResultado/salvar",
     *     tags={"Recurso julgamento Impugnacao Resultado"},
     *     summary="Salvar Recurso do julgamento do Impugnacao de Resultado",
     *     description="Salvar Recurso do julgamento do Impugnacao de Resultado",
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
        $recursoImpugnacaoResultadoTO = $this->recursoImpugnacaoResultadoBO->salvar(RecursoImpugnacaoResultadoTO::newInstance($data));
    }

    /**
     * Retorna o Recurso do Julgamento a partir do id da Impugnacao Resultado e tipo de Recurso.
     *
     * @param $idImpugnacao
     * @param $idTipoRecurso
     *
     * @return string
     * @OA\Get(
     *     path="/recursoJulgamentoImpugnacaoResultado/{idImpugnacao}/recurso/{idTipoRecurso}",
     *     tags={"Impugnacao de Resultado", "RecursoJulgamento"},
     *     summary="Retorna o Recurso do Julgamento a partir do id da Impugnacao Resultado e tipo de Recurso.",
     *     description="Retorna o Recurso do Julgamento a partir do id da Impugnacao Resultado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idImpugnacao",
     *         in="path",
     *         description="Id da Impugnacao Resultado",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idTipoRecurso",
     *         in="path",
     *         description="Id do Tipo de Recurso",
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
    public function getRecursoJulgamentoPorIdImpugnacao($idImpugnacao, $idTipoRecurso)
    {
        $resp = $this->recursoImpugnacaoResultadoBO->getRecursoJulgamentoPorIdImpugnacao($idImpugnacao, $idTipoRecurso);
        return $this->toJson($resp);
    }

    /**
     * Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' do documento informado.
     *
     * @param $id
     *
     * @return Response
     * @throws NegocioException
     * @OA\Get(
     *     path="/recursoJulgamentoImpugnacaoResultado/documento/{$idRecursoJulgamento}/download",
     *     tags={"Arquivo do recurso do julgamento de Impugnacao de Resultado"},
     *     summary="Download de Documento do recurso de julgamento de Impugnação de Resultado",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' do recurso do julgamento informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Pedido de Impugnação Resultado",
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
    public function downloadDocumento($idRecursoJulgamento)
    {
        $arquivoTO = $this->recursoImpugnacaoResultadoBO->downloadDocumento($idRecursoJulgamento);
        return $this->toFile($arquivoTO);
    }
}
