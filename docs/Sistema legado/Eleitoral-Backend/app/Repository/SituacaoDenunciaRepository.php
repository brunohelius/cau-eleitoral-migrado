<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 27/02/2020
 * Time: 14:45
 */

namespace App\Repository;


use App\Entities\DenunciaSituacao;
use App\Entities\SituacaoDenuncia;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Historico da Denúncia'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class SituacaoDenunciaRepository extends AbstractRepository
{
    /**
     * @param $denuncia
     * @return SituacaoDenuncia
     * @throws
     */
    public function getSituacaoAtualDenuncia($denuncia)
    {
        $sq = $this->_em->createQueryBuilder()
            ->from(DenunciaSituacao::class, 'ds1')
            ->select('max(ds1.id)')
            ->andWhere('ds1.denuncia = :denuncia');

        return $this->createQueryBuilder('sd')
            ->join('sd.denunciaSituacao', 'ds')
            ->where("ds.id = ({$sq})")
            ->setParameter('denuncia', $denuncia)
            ->getQuery()
            ->getSingleResult();
    }
}
