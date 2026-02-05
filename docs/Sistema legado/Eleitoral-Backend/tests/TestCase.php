<?php
/*
 * TestCase.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

use Illuminate\Contracts\Console\Kernel;
use \Laravel\Lumen\Testing\TestCase as BaseTestCase;

/**
 * Clase abstrata de referente � Testes de Unidade.
 *
 * @package Tests
 * @author Squadra Tecnologia S/A.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Define o 'valor' retornado para a 'propriedade' (privada' informada, presente no 'objeto' especificado.
     *
     * @param mixed $object
     * @param string $property
     * @param mixed $value
     *
     * @throws ReflectionException
     */
    protected function setPrivateProperty($object, $property, $value)
    {
        $reflection = new ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }
}
