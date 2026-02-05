<?php
/*
 * UfRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\To\JulgamentoAlegacaoImpugResultadoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'JulgamentoAlegacaoImpugResultado'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoAlegacaoImpugResultadoRepository extends AbstractRepository
{
    public function getJulgamentoAlegacaoPorImpugnacaoResultado($idImpugnacao)
    {
        try {
            $query = $this->createQueryBuilder("julgamentoAlegacaoResultado");
            $query->join("julgamentoAlegacaoResultado.statusJulgamentoAlegacaoResultado", "statusJulgamentoAlegacaoResultado")->addSelect('statusJulgamentoAlegacaoResultado');
            $query->join("julgamentoAlegacaoResultado.impugnacaoResultado", "impugnacaoResultado")->addSelect('impugnacaoResultado');
            $query->join("impugnacaoResultado.calendario", "calendario")->addSelect('calendario');
            $query->join("julgamentoAlegacaoResultado.usuario", "usuario")->addSelect('usuario');
            $query->where("impugnacaoResultado.id = :idImpugnacao");
            $query->setParameter('idImpugnacao', $idImpugnacao);

            $julgamento = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return JulgamentoAlegacaoImpugResultadoTO::newInstance($julgamento);
        } catch (NoResultException $e) {
            return null;
        }
    }
}
