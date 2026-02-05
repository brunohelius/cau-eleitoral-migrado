<?php
/*
 * TipoFinalizacaoMandatoController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade do CAU/BR.
 * Não é permitida a distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Exceptions\NegocioException;
use App\Services\TipoFinalizacaoMandatoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Classe de controle referente à entidade 'Tipo Finalizacao Mandato'.
 */
class TipoFinalizacaoMandatoController extends Controller
{
    /**
     * @var TipoFinalizacaoMandatoService
     */
    private $tipoFinalizacaoMandatoService;

    /**
     * @param TipoFinalizacaoMandatoService $tipoFinalizacaoMandatoService
     */
    public function __construct(TipoFinalizacaoMandatoService $tipoFinalizacaoMandatoService)
    {
        $this->tipoFinalizacaoMandatoService = $tipoFinalizacaoMandatoService;
    }

    /**
     * Salva os dados do TipoFinalizacaoMandato
     *
     * @OA\Post(
     *     path="/cadastroTipoFinalizacaoMandato",
     *     summary="Salva os dados do TipoFinalizacaoMandato.",
     *     tags={"cadastroTipoFinalizacaoMandato"},
     *     description="Salva os dados do TipoFinalizacaoMandato.",
     *     @OA\Response(
     *         response=200,
     *         description="A solicitação foi bem-sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/TipoFinalizacaoMandato")
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
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string',
            'ativo' => 'required|bool',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $model = $this->tipoFinalizacaoMandatoService->create($request);
        return response()->json($model);
    }


    /**
     * Atualiza os dados TipoFinalizacaoMandato
     *
     * @OA\Put(
     *     path="/tipoFInalizacaoMandato/{id}",
     *     summary="Atualiza os dados TipoFinalizacaoMandato.",
     *     tags={"tipoFInalizacaoMandato"},
     *     description="Atualiza os dados TipoFinalizacaoMandato.",
     *     @OA\Response(
     *         response=200,
     *         description="A solicitação foi bem-sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/tipoFInalizacaoMandato")
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
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string',
            'ativo' => 'required|bool',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $model = $this->tipoFinalizacaoMandatoService->update($id, $request);
        return response()->json($model);
    }

    /**
     * Busca o TipoFinalizacaoMandato filtrando pelo ID
     *
     * @OA\Get(
     *     path="/tipoFInalizacaoMandato/{id}",
     *     summary="Busca o TipoFinalizacaoMandato filtrando pelo ID.",
     *     tags={"tipoFInalizacaoMandato"},
     *     description="Busca o TipoFinalizacaoMandato filtrando pelo ID.",
     *     @OA\Response(
     *         response=200,
     *         description="A solicitação foi bem-sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/tipoFInalizacaoMandato")
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

        $model = $this->tipoFinalizacaoMandatoService->getById($id);
        return response()->json($model);
    }

     /**
     * Busca o TipoFinalizacaoMandato Utilizando filtro
     *
     * @OA\Post(
     *     path="/tipoFInalizacaoMandato/filter",
     *     summary="Busca o TipoFinalizacaoMandato Utilizando filtro.",
     *     tags={"tipoFInalizacaoMandato"},
     *     description="Busca o TipoFinalizacaoMandato Utilizando filtro.",
     *     @OA\Response(
     *         response=200,
     *         description="A solicitação foi bem-sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/tipoFInalizacaoMandato")
     *         )
     *     ),
     *     @OA\Response(response=400, description="A solicitação não pôde ser entendida pelo servidor devido a uma sintaxe incorreta."),
     *     @OA\Response(response=403, description="O servidor entendeu a solicitação, mas se recusa a atendê-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que o impediu de atender à solicitação."),
     * )
     *
     * @param request $request
     * @return JsonResponse
     * @throws NegocioException
     */
    public function index(request $request): JsonResponse
    {
        return response()->json($this->tipoFinalizacaoMandatoService->getByFilter($request));
    }

    /**
     * Deletar o TipoFinalizacaoMandato
     *
     * @OA\Delete(
     *     path="/TipoFinalizacaoMandato/delete",
     *     summary="Deletar o TipoFinalizacaoMandato.",
     *     tags={"TipoFinalizacaoMandato"},
     *     description="Deletar o TipoFinalizacaoMandato.",
     *     @OA\Response(
     *         response=200,
     *         description="A solicitação foi bem-sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/TipoFinalizacaoMandato")
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
    public function delete(int $id): JsonResponse
    {
        return response()->json($this->tipoFinalizacaoMandatoService->delete($id));
    }
}
