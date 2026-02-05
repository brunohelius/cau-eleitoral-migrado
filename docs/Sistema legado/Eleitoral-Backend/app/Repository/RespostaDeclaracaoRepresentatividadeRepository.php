<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 17/10/2019
 * Time: 11:32
 */

namespace App\Repository;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'RespostaDeclaracao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class RespostaDeclaracaoRepresentatividadeRepository extends AbstractRepository
{

    public function excluirPorMembro($membroChapa)
    {
        $query = $this->createQueryBuilder('respostaDeclaracaoRepresentatividade');
        $query->delete();
        $query->where('respostaDeclaracaoRepresentatividade.membroChapa = :membroChapa');
        $query->setParameter('membroChapa', $membroChapa);

        $query->getQuery()->execute();
    }

}
