<?php
/*
 * AbstractBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Business;

use App\Factory\UsuarioFactory;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * Classe abstract Business.
 *
 * @author Squadra Tecnologia S/A.
 */
abstract class AbstractBO
{

    /**
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var UsuarioFactory
     */
    private $usuarioFactory;

    /**
     * Retorna a instância do 'Repository' conforme o nome da 'entity'.
     *
     * @param string $entityName
     * @return EntityRepository
     */
    protected function getRepository($entityName)
    {
        return $this->getEntityManager()->getRepository($entityName);
    }

    /**
     * Inicia uma transação suspendendo o modo de confirmação automática.
     */
    protected function beginTransaction()
    {
        $this->getEntityManager()->beginTransaction();
    }

    /**
     * Confirma a transação atual.
     */
    protected function commitTransaction()
    {
        $this->getEntityManager()->commit();
    }

    /**
     * Clears the EntityManager.
     * @throws MappingException
     */
    protected function clearEntityTransaction()
    {
        $this->getEntityManager()->clear();
    }

    /**
     * Cancela quaisquer alterações de banco de dados feitas durante a transação atual.
     */
    protected function rollbackTransaction()
    {
        $this->getEntityManager()->rollback();
    }

    /**
     * Retorna a instância de EntityManager.
     *
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if (empty($this->entityManager)) {
            $this->entityManager = app()->make('em');
        }
        return $this->entityManager;
    }

    /**
     * Retorna o usuário conforme o padrão lazy Inicialization.
     *
     * @return UsuarioFactory | null
     */
    protected function getUsuarioFactory()
    {
        if ($this->usuarioFactory == null) {
            $this->usuarioFactory = app()->make(UsuarioFactory::class);
        }

        return $this->usuarioFactory;
    }
}