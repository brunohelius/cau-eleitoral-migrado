<?php

namespace App\Http\Controllers;

use App\Business\DenunciaAdmitidaBO;

/**
 * Class DenunciaAdmitidaController
 * @package App\Http\Controllers
 */
class DenunciaAdmitidaController extends Controller
{

    /**
     * @param $idDenuncia
     * @param DenunciaAdmitidaBO $denunciaAdmitidaBO
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \App\Exceptions\NegocioException
     *
     * @OA\POST(
     *     path="denuncia/{idDenuncia}/relator",
     *     tags={"denuncia", "profissional"},
     *     summary="Verificar Julgamento",
     *     description="Retorna informações de julgamento",
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
    public function inserirRelator($idDenuncia, DenunciaAdmitidaBO $denunciaAdmitidaBO)
    {
        $denunciaAdmitidaBO->inserirRelator($idDenuncia);
        return response(null, 201);
    }

    /**
     * @param $idDenuncia
     * @param DenunciaAdmitidaBO $denunciaAdmitidaBO
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\NegocioException
     *
     * @OA\Get(
     *     path="denuncia/{idDenuncia}/relator/create",
     *     tags={"denuncia", "profissional"},
     *     summary="Criar relator",
     *     description="Retorna dados para criacao de relator",
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
    public function createRelator($idDenuncia, DenunciaAdmitidaBO $denunciaAdmitidaBO)
    {
        return response()->json([
            'membros_comissao' => $denunciaAdmitidaBO->getMembrosComissao($idDenuncia),
        ]);
    }
}