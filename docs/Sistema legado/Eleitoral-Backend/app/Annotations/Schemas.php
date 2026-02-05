<?php

namespace App\Annotations;

/**
 * Lista de Schemas
 *
 * @OA\Schema(
 *   schema="accessToken",
 *   type="object",
 *   description= "Resultado esperado da resposta",
 *   title="Retorno do Token",
 *   @OA\Property(property="accessToken", type="string", description="Token de Acesso")
 * )
 *
 *  @OA\SecurityScheme(
 *     securityScheme="Authorization",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Token de Acesso do Usuário. Bearer {accessToken}."
 * )
 *
 */