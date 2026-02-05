<?php

namespace App\Annotations;

/**
 * Lista de Servidores
 *
 * @OA\Server(url="http://eleitoral.local", description="Ambiente de Desenvolvimento com Host")
 * @OA\Server(url="http://localhost:8000", description="Ambiente de Desenvolvimento sem Host")
 *
 * @OA\Server(url="{schema}://plataforma-backend-dev.caubr.gov.br", description="Recuperar Token",
 *    @OA\ServerVariable(serverVariable="schema", enum={"https", "http"}, default="http")
 * )
 *
 */
