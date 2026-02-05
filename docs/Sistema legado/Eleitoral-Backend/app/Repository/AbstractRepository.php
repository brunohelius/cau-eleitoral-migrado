<?php
/*
 * AbstractRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Repository;

use App\Entities\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;

/**
 * Generic Repository.
 *
 * @author Squadra Tecnologia S/A.
 */
abstract class AbstractRepository extends EntityRepository
{

    /**
     * Salva a 'Entity' na base de dados.
     *
     * @param Entity $entity
     * @param boolean $flush
     * @return \App\Entities\Entity|object|array|ObjectManagerAware|boolean
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function persist(Entity $entity, $flush = TRUE)
    {
        $this->findDetach($entity);
        if ($entity->getId() == null) {
            $this->_em->persist($entity);
        } else {
            $entity = $this->_em->merge($entity);
        }

        if ($flush) {
            $this->_em->flush($entity);
        }

        return $entity;
    }

    /**
     * Salva a 'Entity' na base de dados.
     *
     * @param Entity $entity
     * @param boolean $flush
     * @return \App\Entities\Entity|object|array|ObjectManagerAware|boolean
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function flush(Entity $entity, $flush = TRUE)
    {
        $this->findDetach($entity);
        if ($entity->getId() == null) {
            $this->_em->persist($entity);
        } else {
            $entity = $this->_em->merge($entity);
        }

        if ($flush) {
            $this->_em->flush($entity);
        }

        return $entity;
    }


    /**
     * Salva as 'Entities' em lote na base de dados.
     *
     * @param array $entities
     * @return \App\Entities\Entity[]
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function persistEmLote($entities)
    {
        $result = array();

        foreach ($entities as $entity) {
            $result[] = $this->persist($entity);
        }
        return $result;
    }

    /**
     * Deleta a 'Entity' da base de dados.
     *
     * @param Entity $entity
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Entity $entity)
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    /**
     * Deleta as 'Entities' em lote na base de dados.
     *
     * @param array $entities
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteEmLote($entities)
    {
        foreach ($entities as $entity) {
            $this->delete($entity);
        }
    }

    /**
     * Remove a 'entity' do contexto persistênte.
     *
     * @param Entity $entity
     */
    public function detach(Entity $entity)
    {
        $this->_em->detach($entity);
    }

    /**
     * Recupera as associações 'Detached' para evitar falhas na persistência.
     *
     * @param Entity $entity
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function findDetach(Entity $entity)
    {
        $class = $this->_em->getClassMetadata(get_class($entity));
        $associationMappings = $class->associationMappings;

        foreach ($associationMappings as $assoc) {

            $relatedEntities = $class->reflFields[$assoc['fieldName']]->getValue($entity);
            $cascade = $assoc['cascade'];

            if ($relatedEntities instanceof \App\Entities\Entity) {

                if ($relatedEntities->getId() != null) {
                    $targetEntity = $assoc['targetEntity'];
                    $newInstance = $this->_em->find($targetEntity, $relatedEntities->getId());

                    $fieldName = $assoc['fieldName'];
                    $class->reflFields[$fieldName]->setValue($entity, $newInstance);
                } else if (! empty($cascade)) {
                    $this->findDetach($relatedEntities);
                }
            } else if ($relatedEntities instanceof ArrayCollection && ! empty($cascade)) {

                forEach ($relatedEntities as $instance) {
                    $this->findDetach($instance);
                }
            }
        }
    }

    /**
     * Retorna o objeto de conexão de banco de dados usado pelo EntityManager.
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->getEntityManager()->getConnection();
    }
}