<?php
/*
 * HistoricoChapaEleicaoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\HistoricoChapaEleicao;
use App\To\HistoricoChapaEleicaoTO;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'HistoricoChapaEleicao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class HistoricoChapaEleicaoRepository extends AbstractRepository
{

    /**
     * Recupera os históricos do calendário de acordo com o 'id' do calendário informado.
     *
     * @param int $idCalendario
     *
     * @return HistoricoChapaEleicao[]
     */
    public function getHistoricoPorCalendario(int $idCalendario)
    {
        $query = $this->createQueryBuilder('historico');
        $query->innerJoin('historico.chapaEleicao', 'chapaEleicao')->addSelect('chapaEleicao');
        $query->leftJoin('historico.usuario', 'usuario')->addSelect('usuario');
        $query->leftJoin('historico.profissional', 'profissional')->addSelect('profissional');
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
        $query->innerJoin("chapaEleicao.filial", "filial")->addSelect('filial');;
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.calendario", "calendario");
        $query->where('calendario.id = :id');
        $query->setParameter('id', $idCalendario);
        $query->orderBy('historico.data', 'asc');

        $result = $query->getQuery()->getArrayResult();
        return array_map(static function($historico) {
            return HistoricoChapaEleicaoTO::newInstance($historico);
        }, $result);
    }
}
