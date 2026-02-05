<?php
/*
 * CustomCamelCaseNamingStrategy.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Util\Json;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

/**
 * Implementação para Serialização de Entidades no formato CamelCase.
 *
 * @package App\Util\JSON
 * @author Squadra Tecnologia
 */
class CustomCamelCaseNamingStrategy implements PropertyNamingStrategyInterface
{
    /**
     * Translates the name of the property to the serialized version.
     */
    public function translateName(PropertyMetadata $property): string
    {
        return $property->name;
    }
}