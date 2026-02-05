<?php
/**
 * InformacaoComissaoMembroRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\Calendario;
use App\Entities\InformacaoComissaoMembro;
use App\To\EmailAtividadeSemParametrizacaoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Exception;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'InformacaoComissaoMembro'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class InformacaoComissaoMembroRepository extends AbstractRepository
{
    /**
     * Retorna a Informacao de Comissao conforme o Id do calendário informado.
     *
     * @param integer $idCalendario
     * @return InformacaoComissaoMembro|null
     * @throws NonUniqueResultException
     */
    public function getPorCalendario($idCalendario)
    {
        try {
            $query = $this->createQueryBuilder('informacaoComissaoMembro');
            $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria")->addSelect("atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario")->addSelect("atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario")->addSelect("calendario");
            $query->innerJoin("calendario.eleicao", "eleicao")->addSelect("eleicao");
            $query->leftJoin("calendario.cauUf", "cauUf")->addSelect("cauUf");
            $query->where("calendario.id = :id");

            $query->setParameter("id", $idCalendario);

            return $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a listagem de emails para as informaçãoes não parametrizadas e que se
     * encontram dentro do prazo de vencimento.
     *
     * @param $idInformacaoComissao
     * @return array
     * @throws Exception
     */
    public function getEmailsSemParametrizacao($idInformacaoComissao)
    {
        $query = $this->createQueryBuilder('informacaoComissaoMembro');
        $query->innerJoin("informacaoComissaoMembro.email", "email")->addSelect("email");
        $query->where("informacaoComissaoMembro.id = :id");

        $query->setParameter("id", $idInformacaoComissao);

        $emailsArray = $query->getQuery()->getArrayResult();

        return $query->getQuery()->getArrayResult();
    }


    /**
     * Retorna o calendario conforme o id informado.
     *
     * @param $id
     * @return InformacaoComissaoMembro
     * @throws Exception
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('informacaoComissaoMembro');
            $query->leftJoin("informacaoComissaoMembro.documentoComissaoMembro", "documentoComissaoMembro")->addSelect("documentoComissaoMembro");
            $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria")->addSelect("atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario")->addSelect("atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario")->addSelect("calendario");
            $query->innerJoin("calendario.eleicao", "eleicao")->addSelect("eleicao");

            $query->where("informacaoComissaoMembro.id = :id");
            $query->setParameter("id", $id);

            return InformacaoComissaoMembro::newInstance($query->getQuery()->getArrayResult()[0]);

        } catch (NoResultException $e) {
            return null;
        }
    }


}
