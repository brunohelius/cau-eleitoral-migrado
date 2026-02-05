<?php
/*
 * ContrarrazaoRecursoImpugnacao.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\ContrarrazaoRecursoImpugnacaoBO;
use App\Exceptions\NegocioException;
use App\To\ContrarrazaoRecursoImpugnacaoTO;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'ContrarrazaoRecursoImpugnacao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class ContrarrazaoRecursoImpugnacaoController extends Controller
{

    /**
     * @var ContrarrazaoRecursoImpugnacaoBO
     */
    private $contrarrazaoRecursoImpugnacaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salvar dados do Contrarrazao da recurso do pedido de impugnação
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/contrarrazaoRecursoImpugnacao/salvar",
     *     tags={"Contrarrazao Recurso do Pedido de Impugnação"},
     *     summary="Salvar dados da Contrarrazao do recurso do pedido de impugnação",
     *     description="Salvar dados do Contrarrazao do recurso do pedido de impugnação",
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

        $contrarrazaoRecursoImpugnacaoTO = ContrarrazaoRecursoImpugnacaoTO::newInstance($data);

        $julgamento = $this->getContrarrazaoRecursoImpugnacaoBO()->salvar($contrarrazaoRecursoImpugnacaoTO);
        return $this->toJson($julgamento);
    }

    /**
     * Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.
     *
     * @param $id
     *
     * @return Response
     *
     * @OA\Get(
     *     path="/contrarrazaoRecursoImpugnacao/{id}/download",
     *     tags={"Contrarrazao Recurso do Pedido de Impugnação"},
     *     summary="Download do Documento da Contrarrazao do Recurso do Pedido de Impugnação",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Contrarrazao",
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
        $arquivoTO = $this->getContrarrazaoRecursoImpugnacaoBO()->getArquivoContrarrazaoRecursoImpugnacao($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna uma nova instância de 'ContrarrazaoRecursoImpugnacaoBO'.
     *
     * @return ContrarrazaoRecursoImpugnacaoBO
     */
    private function getContrarrazaoRecursoImpugnacaoBO()
    {
        if (empty($this->contrarrazaoRecursoImpugnacaoBO)) {
            $this->contrarrazaoRecursoImpugnacaoBO = app()->make(ContrarrazaoRecursoImpugnacaoBO::class);
        }

        return $this->contrarrazaoRecursoImpugnacaoBO;
    }
}
