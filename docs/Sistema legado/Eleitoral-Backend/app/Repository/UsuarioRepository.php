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

use App\Entities\Usuario;
use App\To\UsuarioFiltroTO;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Uf'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class UsuarioRepository extends AbstractRepository
{
    /**
     * Recupera os 'Usuários' de acordo com o 'Filtro' informado.
     *
     * @param $idsUsuario
     * @return Usuario[]
     */
    public function getUsuariosPorIds($idsUsuario)
    {
        try {
            $query = $this->createQueryBuilder("usuario");
            $query->where("1 = :true");
            $query->setParameter("true", 1);

            if(!empty($idsUsuario)){
                $query->andWhere("usuario.id IN (:ids)");
                $query->setParameter("ids", $idsUsuario, Connection::PARAM_INT_ARRAY);
            }

            return $query->getQuery()->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
