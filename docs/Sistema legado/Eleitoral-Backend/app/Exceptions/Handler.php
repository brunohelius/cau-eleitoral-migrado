<?php
/*
 * Handler.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Exceptions;

use App\Config\AppConfig;
use App\Util\Utils;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

/**
 *
 * @author Squadra Tecnologia
 */
class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        if(AppConfig::isEnvTesteCAU() && !$exception instanceof NegocioException && !$exception instanceof MethodNotAllowedHttpException){
            Log::channel("discord")->error($exception, ['Ambiente' => App::environment()]);
        }
        parent::report($exception);

    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param \Exception $e
     * @return \Illuminate\Http\Response|JsonResponse
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof NegocioException) {
            $response = $this->sendMessage(Response::HTTP_BAD_REQUEST, $e->getResponseMessage());
        } else {
            if (AppConfig::isEnvDev()) {
                $response = parent::render($request, $e);
            } else {
                $responseMessage = $this->getMessageInternalError($e);
                $response = $this->sendMessage(Response::HTTP_INTERNAL_SERVER_ERROR, $responseMessage);
            }
        }
        return $response;
    }

    /**
     * Retorna a instância de Message (Internal Server Error) considerando o parâmetro informado.
     *
     * @param Throwable $e
     * @return Message
     */
    private function getMessageInternalError(Throwable $e)
    {
        $code = Message::APLICACAO_ENCONTROU_ERRO_INESPERADO;
        $description = Utils::getValue($code, Message::$descriptions);
        $description = Utils::getMessageFormated($description, $e->getMessage());

        $message = Message::newInstance($description, $code);
        $message->setStackTrace($e->getTraceAsString());
        return $message;
    }

    /**
     * Retorna a instância de Response conforme os parâmetros informados.
     *
     * @param $status
     * @param $message
     * @return \Illuminate\Http\Response
     */
    private function sendMessage($status, Message $message)
    {
        $response = response()->make('');
        $response->setStatusCode($status);
        $response->header('Content-Type', 'application/json; charset=utf-8');
        $response->setContent($message->__toString());
        return $response;
    }
}
