<?php
/*
 * HistoricoInformacaoComissaoMembroRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\HistoricoInformacaoComissaoMembro;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade
 * 'HistoricoInformacaoComissaoMembro'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class HistoricoInformacaoComissaoMembroRepository extends AbstractRepository
{
    /**
     * Retorna os dados de Histórico de Informação de Comissão de Membros pelo id da Informação
     *
     * @param $idInformacaoComissaoMembro
     * @return array|null
     */
    public function getPorInformacaoComissaoMembro($idInformacaoComissaoMembro)
    {
        try {
            $query = $this->createQueryBuilder('historicoInformacaoComissaoMembro');
            $query->innerJoin("historicoInformacaoComissaoMembro.informacaoComissaoMembro", "informacaoComissaoMembro")->addSelect("informacaoComissaoMembro");
            $query->where("informacaoComissaoMembro.id = :id");
            $query->andWhere("historicoInformacaoComissaoMembro.histComissao = :histComissao");
            $query->orderBy("historicoInformacaoComissaoMembro.dataHistorico", "DESC");

            $query->setParameter("id", $idInformacaoComissaoMembro);
            $query->setParameter("histComissao", true);

            $arrayHistorico = $query->getQuery()->getArrayResult();

            return array_map(function ($historico) {
                return HistoricoInformacaoComissaoMembro::newInstance($historico);
            }, $arrayHistorico);

        } catch (NoResultException $e) {
            return null;
        }
    }
}
