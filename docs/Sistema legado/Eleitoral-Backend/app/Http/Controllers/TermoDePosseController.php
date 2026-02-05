<?php
/*
 * TermoDePosseController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade do CAU/BR.
 * Não é permitida a distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Exceptions\NegocioException;
use App\Services\TermoDePosseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Classe de controle referente à entidade 'Termo de Posse'.
 */
class TermoDePosseController extends Controller
{
    /**
     * @var TermoDePosseService
     */
    private $termoDePosseService;

    /**
     * @param TermoDePosseService $termoDePosseService
     */
    public function __construct(TermoDePosseService $termoDePosseService)
    {
        $this->termoDePosseService = $termoDePosseService;
    }

    /**
     * Salva os dados do Termo de Posse
     *
     * @OA\Post(
     *     path="/cadastroTermoPosse",
     *     summary="Salva os dados do Termo de Posse.",
     *     tags={"cadastroTermoPosse"},
     *     description="Salva os dados do Termo de Posse.",
     *     @OA\Response(
     *         response=200,
     *         description="A solicitação foi bem-sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/termoDePosse")
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
        $validator = Validator::make($request['termo'], [
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
            'cpfPresidente' => 'required|string',
            'nomePresidente' => 'required|string',
            'ufPresidente' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
       
        $model = $this->termoDePosseService->create($request);
        
        return response()->json($model);
    }

    /**
     * Busca o Termo de Posse filtrando pelo ID
     *
     * @OA\Get(
     *     path="/termo/{id}",
     *     summary="Busca o Termo de Posse filtrando pelo ID.",
     *     tags={"Termo"},
     *     description="Busca o Termo de Posse filtrando pelo ID.",
     *     @OA\Response(
     *         response=200,
     *         description="A solicitação foi bem-sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/termo")
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

        $model = $this->termoDePosseService->getById($id);
        return response()->json($model);
    }

    /**
     * Atualiza os dados do Termo de Posse
     *
     * @OA\Put(
     *     path="/termo/{id}",
     *     summary="Atualiza os dados do Termo de Posse.",
     *     tags={"termo"},
     *     description="Atualiza os dados do Termo de Posse.",
     *     @OA\Response(
     *         response=200,
     *         description="A solicitação foi bem-sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/termo")
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
        $validator = Validator::make($request['termo'], [
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
            'cpfPresidente' => 'required|string',
            'nomePresidente' => 'required|string',
            'ufPresidente' => 'required|string',
            'idConselheiro' => 'required|int'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        
        $model = $this->termoDePosseService->update($id, $request);
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
     *             @OA\Schema(ref="#/components/schemas/termo")
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

        $arquivo = $this->termoDePosseService->imprimir($id);

        return $arquivo;
    }
}
