<?php
/*
 * JsonUtils.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Util;

use App\Util\Json\CustomCamelCaseNamingStrategy;
use App\Util\Json\CustomDateTimeHandler;
use App\Util\Json\ExclusionProxyStrategy;
use DateTime;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Handler\ArrayCollectionHandler;
use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\IteratorHandler;
use JMS\Serializer\Handler\StdClassHandler;

/**
 * Classe utilitária da aplicação para facilitar a manipulação de dados no formato JSON.
 *
 * @package App\Util
 * @author Squadra Tecnologia
 */
class JsonUtils
{

    /**
     * Construtor privado para garantir o Singleton.
     */
    private function __construct()
    {
    }

    /**
     * Retorna o 'Objeto' serializado em 'json'.
     *
     * @param $object
     *
     * @return mixed|string
     */
    public static function toJson($object)
    {
        $dataJson = '';
        if (!is_null($object)) {
            $context = new SerializationContext();
            $context->addExclusionStrategy(new ExclusionProxyStrategy());
            $builder = SerializerBuilder::create();
            $builder->setPropertyNamingStrategy(new CustomCamelCaseNamingStrategy());

            $builder->configureHandlers(function (HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new DateHandler(DateTime::ATOM, Utils::TIMEZONE_PADRAO));
                $registry->registerSubscribingHandler(new StdClassHandler());
                $registry->registerSubscribingHandler(new ArrayCollectionHandler());
                $registry->registerSubscribingHandler(new IteratorHandler());
                $registry->registerSubscribingHandler(new CustomDateTimeHandler());
            });

            $dataJson = $builder->build()->serialize($object, 'json', $context);
        }
        return $dataJson;
    }
}
