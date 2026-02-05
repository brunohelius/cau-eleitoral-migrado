<?php
/*
 * ConselheiroController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade do CAU/BR.
 * Não é permitida a distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Exceptions\NegocioException;
use App\Services\ConselheiroService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Classe de controle referente à entidade 'Conselheiro'.
 */
class ConselheiroController extends Controller
{
    /**
     * @var ConselheiroService
     */
    private $conselheiroService;

    /**
     * @param ConselheiroService $conselheiroService
     */
    public function __construct(ConselheiroService $conselheiroService)
    {
        $this->conselheiroService = $conselheiroService;
    }

    /**
     * Salva os dados do Conselheiro
     *
     * @OA\Post(
     *     path="/cadastroTipoFinalizacaoMandato",
     *     summary="Salva os dados do Conselheiro.",
     *     tags={"cadastroTipoFinalizacaoMandato"},
     *     description="Salva os dados do Conselheiro.",
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
        $validator = Validator::make($request['membro'], [
            'nome' => 'required|string',
            'cpf' => 'required|string',
            'ano' => 'required|int',
            'uf' => 'required|string',
            'idFilial' => 'required|int',
            'representacao' => 'required|string',
            'idRepresentacao' => 'required|int',
            'tipo' => 'required|string',
            'idTipo' => 'required|int',
            'diploma' => 'required|bool',
            'termo' => 'required|bool',
            'pessoa_id' => 'required|int',
            'email' => 'required|string',
        ]);
     
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
           
        $model = $this->conselheiroService->create($request);

        return response()->json($model);
    }

}
