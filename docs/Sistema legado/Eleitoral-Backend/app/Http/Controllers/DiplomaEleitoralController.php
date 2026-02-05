<?php
/*
 * DiplomaEleitoralController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade do CAU/BR.
 * Não é permitida a distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Exceptions\NegocioException;
use App\Services\DiplomaEleitoralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Classe de controle referente à entidade 'Diploma Eleitoral'.
 */
class DiplomaEleitoralController extends Controller
{
    /**
     * @var DiplomaEleitoralService
     */
    private $diplomaEleitoralService;

    /**
     * @param DiplomaEleitoralService $diplomaEleitoralService
     */
    public function __construct(DiplomaEleitoralService $diplomaEleitoralService)
    {
        $this->diplomaEleitoralService = $diplomaEleitoralService;
    }

    /**
     * Salva os dados do Diploma Eleitoral
     *
     * @OA\Post(
     *     path="/cadastroDiplomaEleitoral",
     *     summary="Salva os dados do Diploma Eleitoral.",
     *     tags={"cadastroDiplomaEleitoral"},
     *     description="Salva os dados do Diploma Eleitoral.",
     *     @OA\Response(
     *         response=200,
     *         description="A solicitação foi bem-sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/diplomaEleitoral")
     *         )
     *     ),
     *     @OA\Response(response=400, description="A solicitação não pôde ser entendida pelo servidor devido a uma sintaxe incorreta."),
     *     @OA\Response(response=403, description="O servidor entendeu a solicitação, mas se recusa a atendê-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que o impediu de atender à solicitação."),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws NegocioException
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request['diploma'], [
            'diaRealizacao' => 'required|int',
            'mesRealizacao' => 'required|int',
            'anoRealizacao' => 'required|int',
            'numeroResolucao' => 'required|int',
            'diaResolucao' => 'required|int',
            'mesResolucao' => 'required|int',
            'anoResolucao' => 'required|int',
            'cpfConselheiro' => 'required|string',
            'nomeConselheiro' => 'required|string',
            'UfConselheiro' => 'required|string',
            'tipoConselheiro' => 'required|int',
            'diaEmissao' => 'required|int',
            'mesEmissao' => 'required|int',
            'anoEmissao' => 'required|int',
            'cidadeEmissao' => 'required|string',
            'UfEmissao' => 'required|string',
            'cpfCoordenador' => 'required|string',
            'nomeCoordenador' => 'required|string',
            'UfComissao' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        
        $model = $this->diplomaEleitoralService->create($request);
        
        return response()->json($model);
    }

    /**
     * Busca o Diploma Eleitoral filtrando pelo ID
     *
     * @OA\Get(
     *     path="/diploma/{id}",
     *     summary="Busca o Diploma Eleitoral filtrando pelo ID.",
     *     tags={"Diploma"},
     *     description="Busca o Diploma Eleitoral filtrando pelo ID.",
     *     @OA\Response(
     *         response=200,
     *         description="A solicitação foi bem-sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/diploma")
     *         )
     *     ),
     *     @OA\Response(response=400, description="A solicitação não pôde ser entendida pelo servidor devido a uma sintaxe incorreta."),
     *     @OA\Response(response=403, description="O servidor entendeu a solicitação, mas se recusa a atendê-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que o impediu de atender à solicitação."),
     * )
     *
     * @param int $id
     * @return JsonResponse
     * @throws NegocioException
     */
    public function show(int $id): JsonResponse
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $model = $this->diplomaEleitoralService->getById($id);
        return response()->json($model);
    }

    /**
     * Atualiza os dados do Diploma Eleitoral
     *
     * @OA\Put(
     *     path="/diploma/{id}",
     *     summary="Atualiza os dados do Diploma Eleitoral.",
     *     tags={"diploma"},
     *     description="Atualiza os dados do Diploma Eleitoral.",
     *     @OA\Response(
     *         response=200,
     *         description="A solicitação foi bem-sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/diploma")
     *         )
     *     ),
     *     @OA\Response(response=400, description="A solicitação não pôde ser entendida pelo servidor devido a uma sintaxe incorreta."),
     *     @OA\Response(response=403, description="O servidor entendeu a solicitação, mas se recusa a atendê-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que o impediu de atender à solicitação."),
     * )
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws NegocioException
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $validator = Validator::make($request['diploma'], [
            'diaRealizacao' => 'required|int',
            'mesRealizacao' => 'required|int',
            'anoRealizacao' => 'required|int',
            'numeroResolucao' => 'required|int',
            'diaResolucao' => 'required|int',
            'mesResolucao' => 'required|int',
            'anoResolucao' => 'required|int',
            'cpfConselheiro' => 'required|string',
            'nomeConselheiro' => 'required|string',
            'UfConselheiro' => 'required|string',
            'tipoConselheiro' => 'required|int',
            'diaEmissao' => 'required|int',
            'mesEmissao' => 'required|int',
            'anoEmissao' => 'required|int',
            'cidadeEmissao' => 'required|string',
            'UfEmissao' => 'required|string',
            'cpfCoordenador' => 'required|string',
            'nomeCoordenador' => 'required|string',
            'UfComissao' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        
        $model = $this->diplomaEleitoralService->update($id, $request);
        return response()->json($model);
    }

    /**
     * Imprimir Diploma Eleitoral
     *
     * @OA\Get(
     *     path="/imprimir/{id}",
     *     summary="Imprimir Diploma Eleitoral.",
     *     tags={"Diploma"},
     *     description="Imprimir Diploma Eleitoral.",
     *     @OA\Response(
     *         response=200,
     *         description="A solicitação foi bem-sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/diploma")
     *         )
     *     ),
     *     @OA\Response(response=400, description="A solicitação não pôde ser entendida pelo servidor devido a uma sintaxe incorreta."),
     *     @OA\Response(response=403, description="O servidor entendeu a solicitação, mas se recusa a atendê-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que o impediu de atender à solicitação."),
     * )
     *
     * @param int $id
     * @throws NegocioException
     */
    public function imprimir(int $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $arquivo = $this->diplomaEleitoralService->imprimir($id);

        return $arquivo;
    }
}
