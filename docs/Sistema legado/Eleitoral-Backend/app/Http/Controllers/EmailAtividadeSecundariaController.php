<?php

namespace App\Http\Controllers;

use App\Business\AtividadeSecundariaCalendarioBO;
use App\Business\EmailAtividadeSecundariaBO;

class EmailAtividadeSecundariaController extends Controller
{
    /**
     * @var \App\Business\EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;
    
    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;
    
    /**
     * Construtor da Classe.
     */
    public function __construct()
    {
        $this->emailAtividadeSecundariaBO = app()->make(EmailAtividadeSecundariaBO::class);
        $this->atividadeSecundariaCalendarioBO = app()->make(AtividadeSecundariaCalendarioBO::class);
    }

    /**
     * Verifica se exisite definição de e-mail conforme o id do vinculo do email com a atividade secundária
     *
     * @param $id
     * @return string
     *
     * @OA\Get(
     *     path="/emailAtividadeSecundaria/{id}/hasDefinicao",
     *     tags={"Email Atividade Secundária"},
     *     summary="Verificação se existe definição de e-mail",
     *     description="Verifica se exisite definição de e-mail conforme o id do vinculo do email com a atividade secundária",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Email/Atividade Secundaria",
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
    public function hasDefinicaoEmail($id)
    {
        $hasDefinicao = $this->emailAtividadeSecundariaBO->hasDefinicaoEmail($id);
        return $this->toJson($hasDefinicao);
    }
}

