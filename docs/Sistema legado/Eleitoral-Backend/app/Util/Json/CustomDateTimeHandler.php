<?php
/*
 * CustomDateTimeHandler.php
 * Copyright (c) Ministério da Educação.
 * Este software é confidencial e propriedade do MEC.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do MEC.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Util\Json;

use App\Util\Utils;
use DateTime;
use DateTimeZone;
use Exception;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;

/**
 * Implementação para Serialização de atributos do tipo DateTime.
 *
 * @author Squadra Tecnologia S/A.
 */
class CustomDateTimeHandler implements SubscribingHandlerInterface
{

    const FORMATO_PADRAO_UTC = "Y-m-d\TH:i:s.000\Z";

    /**
     *
     * {@inheritdoc}
     *
     * @see \JMS\Serializer\Handler\SubscribingHandlerInterface::getSubscribingMethods()
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'DateTime',
                'method' => 'serializeDateTimeToJson'
            )
        );
    }

    /**
     * Converte o valor 'DateTime' no padrão 'JSON'.
     *
     * @param JsonSerializationVisitor $visitor
     * @param DateTime $date
     * @param array $type
     * @param Context $context
     * @return string|null
     * @throws Exception
     */
    public function serializeDateTimeToJson(
        JsonSerializationVisitor $visitor,
        DateTime $date,
        array $type,
        Context $context
    )
    {
        $jsonDate = null;

        if ($date !== null) {
            $date->setTimezone(new DateTimeZone(Utils::TIMEZONE_PADRAO));
            $jsonDate = $date->format(static::FORMATO_PADRAO_UTC);
        }

        return $jsonDate;
    }
}
