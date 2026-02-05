<?php
/*
 * HistoricoCalendarioRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\HistoricoCalendario;
use Illuminate\Support\Facades\Date;
use App\Config\Constants;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'HistoricoCalendario'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class HistoricoCalendarioRepository extends AbstractRepository
{

    /**
     * Recupera os históricos do calendário de acordo com o 'id' do calendário informado.
     *
     * @param integer $idCalendario
     * @return HistoricoCalendario[]
     */
    public function getHistoricoPorCalendario($idCalendario)
    {
        $query = $this->createQueryBuilder('historico');
        $query->innerJoin('historico.calendario', 'calendario')->addSelect('calendario');
        $query->leftJoin('historico.justificativaAlteracao', 'justificativaAlteracao')
            ->addSelect('justificativaAlteracao');

        $query->where('calendario.id = :id');
        $query->setParameter('id', $idCalendario);
        $query->orderBy('historico.id', 'desc');
        return $query->getQuery()->getResult();
    }

}
