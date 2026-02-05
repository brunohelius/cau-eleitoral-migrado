<?php

namespace App\Annotations;

/**
 * Cadastro de Requests
 *
 *  @OA\Post(
 *     path="/auth/login",
 *     tags={"Login"},
 *     summary="Retorna o Token do Usuário",
 *     description="Retorna o token do usuário pela chave de acesso informado.<br/>Nota: Selecione o servidor acima (Servers) como: Recuperar Token",
 *     @OA\Parameter(
 *         name="chaveAcesso",
 *         in="query",
 *         description="Chave de Acesso",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Resposta Operacional Normal",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 allOf={
 *                     @OA\Schema(ref="#/components/schemas/accessToken")
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(response=400, description="Não foi possível acessar a funcionalidade, erro de autenticação"),
 *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
 *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
 * )
 *
 */