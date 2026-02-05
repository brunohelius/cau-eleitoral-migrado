<?php

/*
 * RecursoJulgamentoFinalController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\RecursoJulgamentoFinalBO;
use App\Exceptions\NegocioException;
use App\To\RecursoJulgamentoFinalTO;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'JulgamentoRecursoSubstituicao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class RecursoJulgamentoFinalController extends Controller
{

    /**
     * @var RecursoJulgamentoFinalBO
     */
    private $recursoJulgamentoFinalBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salvar dados do julgamento final da Chapa Eleição
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/recursoJulgamentoFinal/salvar",
     *     tags={"Recurso Julgamento Final"},
     *     summary="Salvar dados do recurso julgamento final",
     *     description="Salvar dados do recurso julgamento final",
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
        $julgamento = $this->getRecursoJulgamentoFinalBO()->salvar(RecursoJulgamentoFinalTO::newInstance(Input::all()));
        return $this->toJson($julgamento);
    }

    /**
     * Retorna o julgamento final conforme o id da chapa da eleição com verificação para responsável chapa.
     *
     * @param $idChapaEleicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/recursoJulgamentoFinal/chapa/{idChapaEleicao}",
     *     tags={"Julgamentos Finais"},
     *     summary="Dados da Julgamento Final da Chapa",
     *     description="Retorna o julgamento final conforme o id da chapa da eleição com verificação para responsável chapa.",
     *     @OA\Parameter(
     *         name="idChapaEleicao",
     *         in="path",
     *         description="Id da Chapa da Eleição",
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
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição."),
     *     security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getRecursoJulgamentoFinalPorChapaEleicao($idChapaEleicao)
    {
        $resp = $this->getRecursoJulgamentoFinalBO()->getRecursoJulgamentoFinalPorChapaEleicao($idChapaEleicao);
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
     *     path="/recursoJulgamentoFinal/{id}/download",
     *     tags={"Julgamentos Finais"},
     *     summary="Download do Documento do Julgamento Final da Chapa",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.",
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
    public function download($id)
    {
        $arquivoTO = $this->getRecursoJulgamentoFinalBO()->getArquivo($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna uma nova instância de 'RecursoJulgamentoFinalBO'.
     *
     * @return RecursoJulgamentoFinalBO
     */
    private function getRecursoJulgamentoFinalBO()
    {
        if (empty($this->recursoJulgamentoFinalBO)) {
            $this->recursoJulgamentoFinalBO = app()->make(RecursoJulgamentoFinalBO::class);
        }

        return $this->recursoJulgamentoFinalBO;
    }
}
