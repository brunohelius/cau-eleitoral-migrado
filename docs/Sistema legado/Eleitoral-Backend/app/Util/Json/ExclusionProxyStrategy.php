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

use App\Entities\Entity;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Proxy\Proxy;
use JMS\Serializer\Context;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\SerializationContext;
use ReflectionException;
use ReflectionProperty;

/**
 * Implementação para Serialização de Entidades ignorando atributos 'Lazy'.
 *
 * @package App\Util\JSON
 * @author Squadra Tecnologia
 */
class ExclusionProxyStrategy implements ExclusionStrategyInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \JMS\Serializer\Exclusion\ExclusionStrategyInterface::shouldSkipClass()
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $context): bool
    {
        return false;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @throws ReflectionException
     * @see \JMS\Serializer\Exclusion\ExclusionStrategyInterface::shouldSkipProperty()
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $context): bool
    {
        $skip = false;
        if ($context instanceof SerializationContext) {
            $visitingSet = $context->getVisitingSet();

            $current = null;
            foreach ($visitingSet as $visiting) {
                $current = $visiting;
            }

            $propertyValue = $this->getPropertyValue($property, $current);

            if ($propertyValue instanceof PersistentCollection ) {
                $teste = $propertyValue->count();
            }

            $skip = $propertyValue != null && (($propertyValue instanceof Proxy && !$propertyValue->__isInitialized__)
                    || ($propertyValue instanceof PersistentCollection && !$propertyValue->isInitialized()));
        }

        return $skip;
    }

    /**
     * Retorna o valor da propriedade conforme os valores informados.
     *
     * @param PropertyMetadata $property
     * @param $current
     * @return mixed
     * @throws ReflectionException
     */
    public function getPropertyValue(PropertyMetadata $property, $current)
    {
        $getter = "";
        $value = null;
        $class = (new ReflectionProperty($property->class, $property->name))->getDeclaringClass();

        if ($class->hasMethod('get' . $property->name)
            && $class->getMethod('get' . $property->name)->isPublic()) {
            $getter = 'get' . $property->name;
        } elseif ($class->hasMethod('is' . $property->name)
            && $class->getMethod('is' . $property->name)->isPublic()) {
            $getter = 'is' . $property->name;
        } elseif ($class->hasMethod('has' . $property->name)
            && $class->getMethod('has' . $property->name)->isPublic()) {
            $getter = 'has' . $property->name;
        }

        if (!empty($getter)) {
            $value = $current->{$getter}();
        }

        return $value;
    }
}
