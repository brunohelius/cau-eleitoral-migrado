<?php
/*
 * RetificacaoJulgamentoDenunciaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\RetificacaoJulgamentoDenuncia;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'RetificacaoJulgamentoDenuncia'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class RetificacaoJulgamentoDenunciaRepository extends AbstractRepository
{

    /**
     * Retorna a retificação do julgamento de Denuncia de acordo com 'id' informado.
     *
     * @param integer $idRetificacao
     *
     * @return RetificacaoJulgamentoDenuncia|null
     * @throws \Exception
     */
    public function getRetificacaoPorId($idRetificacao)
    {
        try {
            $query = $this->createQueryBuilder('retificacaoJulgamento');

            $query->innerJoin('retificacaoJulgamento.denuncia', 'denuncia')
                ->addSelect('denuncia');
            $query->innerJoin('retificacaoJulgamento.julgamentoRetificado', 'julgamentoRetificado')
                ->addSelect('julgamentoRetificado');
            $query->leftJoin('retificacaoJulgamento.tipoJulgamento', 'tipoJulgamento')
                ->addSelect('tipoJulgamento');
            $query->leftJoin('retificacaoJulgamento.tipoSentencaJulgamento', 'tipoSentencaJulgamento')
                ->addSelect('tipoSentencaJulgamento');
            $query->leftJoin('retificacaoJulgamento.arquivosJulgamentoDenuncia', 'arquivosJulgamentoDenuncia')
                ->addSelect('arquivosJulgamentoDenuncia');

            $query->where("retificacaoJulgamento.id = :idRetificacao");
            $query->setParameter('idRetificacao', $idRetificacao);

            $retificacao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return RetificacaoJulgamentoDenuncia::newInstance($retificacao);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Retorna lista de Denuncias de acordo com 'filtro' informado.
     *
     * @param integer $idDenuncia
     *
     * @return RetificacaoJulgamentoDenuncia[]
     * @throws \Exception
     */
    public function getRetificacoesPorIdDenuncia($idDenuncia)
    {
        try {
            $query = $this->createQueryBuilder('retificacaoJulgamento');

            $query->innerJoin('retificacaoJulgamento.denuncia', 'denuncia')
                  ->addSelect('denuncia');
            $query->innerJoin('retificacaoJulgamento.julgamentoRetificado', 'julgamentoRetificado')
                  ->addSelect('julgamentoRetificado');
            $query->innerJoin('retificacaoJulgamento.usuario', 'usuario')
                  ->addSelect('usuario');

            $query->where("denuncia.id = :idDenuncia");
            $query->andWhere("julgamentoRetificado IS NOT NULL");

            $query->setParameter('idDenuncia', $idDenuncia);

            $query->orderBy('retificacaoJulgamento.data', 'DESC');

            $retificacoes = $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
            return array_map(static function(array $denuncia) {
                return RetificacaoJulgamentoDenuncia::newInstance($denuncia);
            }, $retificacoes);
        } catch (NoResultException $e) {
            return [];
        }
    }
}
