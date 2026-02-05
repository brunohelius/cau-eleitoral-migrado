<?php
/*
 * NegocioException.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Exceptions;

use App\Util\Utils;
use Exception;

/**
 * Exceção a ser lançada na ocorrência de falhas no fluxo de negócio.
 *
 * @package App\Exceptions
 * @author Squadra Tecnologia
 */
class NegocioException extends Exception
{
    /**
     * @var Message
     */
    private $responseMessage;

    /**
     * Construtor da classe.
     *
     * NegocioException constructor.
     *
     * @param string $code
     * @param null $params
     * @param bool $concatParams
     */
    public function __construct($code = null, $params = null, $concatParams = false)
    {
        if (! empty($code)) {
            $description = Utils::getValue($code, Message::$descriptions);

            if (empty($description)) {
                $description = $code;
                $code = null;
            }
            $description = Utils::getMessageFormated($description, $params, $concatParams);
            $this->responseMessage = Message::newInstance($description, $code, $params);
            $this->message = $description;
        }
    }

    /**
     * @return Message
     */
    public function getResponseMessage(): Message
    {
        return $this->responseMessage;
    }
}
