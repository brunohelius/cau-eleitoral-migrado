<?php
/*
 * PlataformaChapaHistoricoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\To\PlataformaChapaHistoricoTO;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'PlataformaChapaHistorico'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class PlataformaChapaHistoricoRepository extends AbstractRepository
{
    public function getPorChapaEleicao($idChapaEleicao)
    {
        try {
            $query = $this->createQueryBuilder('plataformaChapaHistorico');
            $query->innerJoin('plataformaChapaHistorico.chapaEleicao', 'chapaEleicao');
            $query->leftJoin('plataformaChapaHistorico.profissionalInclusaoPlataforma', 'profissional')->addSelect('profissional');
            $query->leftJoin('plataformaChapaHistorico.usuarioInclusaoPlataforma', 'usuario')->addSelect('usuario');
            $query->leftJoin('plataformaChapaHistorico.redesSociaisHistoricoPlataforma', 'redesSociais')->addSelect('redesSociais');
            $query->leftJoin('redesSociais.tipoRedeSocial', 'tipoRedeSocial')->addSelect('tipoRedeSocial');

            $query->where('chapaEleicao.id = :id');

            $query->setParameter('id', $idChapaEleicao);
            $query->orderBy('plataformaChapaHistorico.id', 'ASC');
            $retificacoes = $query->getQuery()->getArrayResult();

            return array_map(function($retificacao) {
                return PlataformaChapaHistoricoTO::newInstance($retificacao);
            }, $retificacoes);

        } catch ( NoResultException $e) {
            return null;
        }
    }
}
