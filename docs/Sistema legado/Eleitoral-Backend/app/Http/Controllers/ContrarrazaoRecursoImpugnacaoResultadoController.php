<?php
/*
 * ChapaEleicaoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\ContrarrazaoRecursoImpugnacaoResultadoBO;
use App\Exceptions\NegocioException;
use App\To\ContrarrazaoRecursoImpugnacaoResultadoTO;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'ContrarrazaoRecursoImpugnacaoResultado'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class ContrarrazaoRecursoImpugnacaoResultadoController extends Controller
{

    /**
     * @var ContrarrazaoRecursoImpugnacaoResultadoBO
     */
    private $contrarrazaoRecursoImpugnacaoResultadoBO;
    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salvar dados da contrarrazão da impugnação de resultado
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/contrarrazoesImpugnacaoResultado/salvar",
     *     tags={"Contrarrazões Impugnação de Resultado"},
     *     summary="Salvar dados da contrarrazão da impugnação de resultado",
     *     description="Salvar dados da contrarrazão da impugnação de resultado",
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
        $contrarrazao = $this->getContrarrazaoRecursoImpugnacaoResultadoBO()->salvar(
            ContrarrazaoRecursoImpugnacaoResultadoTO::newInstance(Input::all())
        );
        return $this->toJson($contrarrazao);
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
     *     path="/contrarrazoesImpugnacaoResultado/documento/{idContrarrazao}/download",
     *     tags={"Contrarrazões Impugnação de Resultado"},
     *     summary="Download do Documento da Contrarrazão da impugnação de resultado",
     *     description="Download do Documento da Contrarrazão da impugnação de resultado",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Julgamento Final",
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
    public function download($idContrarrazao)
    {
        $arquivoTO = $this->getContrarrazaoRecursoImpugnacaoResultadoBO()->getArquivoContrarrazao($idContrarrazao);

        if (!empty($arquivoTO)) {
            return $this->toFile($arquivoTO);
        }
    }

    /**
     * Retorna uma nova instância de 'ContrarrazaoRecursoImpugnacaoResultadoBO'.
     *
     * @return ContrarrazaoRecursoImpugnacaoResultadoBO
     */
    private function getContrarrazaoRecursoImpugnacaoResultadoBO()
    {
        if (empty($this->contrarrazaoRecursoImpugnacaoResultadoBO)) {
            $this->contrarrazaoRecursoImpugnacaoResultadoBO = app()->make(ContrarrazaoRecursoImpugnacaoResultadoBO::class);
        }

        return $this->contrarrazaoRecursoImpugnacaoResultadoBO;
    }
}
