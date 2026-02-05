<?php

namespace App\Repository;

use App\Entities\MembroChapa;
use App\Entities\MembroChapaPendencia;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'MembroChapaPendencia'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class MembroChapaPendenciaRepository extends AbstractRepository
{

    /**
     * Retorna os membro chapa pendências pelo id do membro chapa
     *
     * @param integer $idMembroChapa
     *
     * @return array|null
     */
    public function getPorMembroChapa($idMembroChapa)
    {
        $query = $this->createQueryBuilder("membroChapaPendencia");
        $query->leftJoin("membroChapaPendencia.membroChapa", "membroChapa");
        $query->leftJoin("membroChapaPendencia.tipoPendencia", "tipoPendencia")
            ->addSelect("tipoPendencia");

        $query->where("membroChapa.id = :idMembroChapa");
        $query->setParameter('idMembroChapa', $idMembroChapa);

        $arrayMembrosChapaPendencias = $query->getQuery()->getArrayResult();

        return array_map(function ($dadosMembroChapaPendencia) {
            return MembroChapaPendencia::newInstance($dadosMembroChapaPendencia);
        }, $arrayMembrosChapaPendencias);
    }

    public function excluirPorMembroChapa(MembroChapa $membroChapa)
    {
        $query = $this->createQueryBuilder('membroChapaPendencia');
        $query->delete();
        $query->where('membroChapaPendencia.membroChapa = :membroChapa');
        $query->setParameter('membroChapa', $membroChapa);
//
//        $query = $this->getDoctrine()->getManager()->createQuery('delete FROM LesDataBundle:News n where n.id = '.$newsID);
//        $result = $query->getResult();

        $query->getQuery()->execute();
    }
}