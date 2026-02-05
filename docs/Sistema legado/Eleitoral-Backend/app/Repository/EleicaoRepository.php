<?php
/*
 * EleicaoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Config\Constants;
use App\Entities\CalendarioSituacao;
use App\Entities\ChapaEleicao;
use App\Entities\Eleicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\PedidoSubstituicaoChapa;
use App\To\EleicaoTO;
use App\Util\Utils;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Eleicao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class EleicaoRepository extends AbstractRepository
{
    /**
     * Retorna todos os anos das eleiçoes
     *
     * @return array
     */
    public function getAnos()
    {
        $dql = " SELECT DISTINCT eleicao.ano ano ";
        $dql .= " FROM App\Entities\Eleicao eleicao ";
        $dql .= " WHERE eleicao.ativo = :ativo  ";
        $dql .= " AND eleicao.excluido = :excluido ";

        $parametros['ativo'] = true;
        $parametros['excluido'] = false;

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $arrayAnos = $query->getArrayResult();

        return array_map(function ($dadosAnos) {
            return EleicaoTO::newInstance($dadosAnos);
        }, $arrayAnos);
    }

    /**
     * Retorna as eleicoes para todos os anos
     *
     * @param null $ano
     * @param bool $recuperaExcluido
     * @return array
     */
    public function getEleicoes($ano = null, $recuperaExcluido = false)
    {
        $dql = " SELECT DISTINCT eleicao.ano ano, eleicao.sequenciaAno sequenciaAno, eleicao.id id ";
        $dql .= " FROM App\Entities\Eleicao eleicao ";
        $dql .= " WHERE 1 = 1 ";
        $parametros = [];

        if (!$recuperaExcluido) {
            $dql .= " AND eleicao.ativo = :ativo  ";
            $dql .= " AND eleicao.excluido = :excluido ";

            $parametros['ativo'] = true;
            $parametros['excluido'] = false;
        }

        //Caso seja informado o ano, filtra e ordena pela maior sequencia.
        if (!empty($ano)) {
            $dql .= " AND eleicao.ano = :ano ";
            $dql .= " ORDER BY eleicao.sequenciaAno DESC ";
            $parametros['ano'] = $ano;
        }

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $arrayEleicoes = $query->getArrayResult();

        return array_map(function ($dadosEleicoes) {
            return EleicaoTO::newInstance($dadosEleicoes);
        }, $arrayEleicoes);
    }

    /**
     * Retorna a eleição conforme o id informado.
     *
     * @param $id
     * @return Eleicao|null
     * @throws NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('eleicao');
            $query->leftJoin("eleicao.situacoes", "situacoes")->addSelect("situacoes");
            $query->leftJoin("situacoes.situacaoEleicao", "situacaoEleicao")
                ->addSelect("situacaoEleicao")
                ->orderBy('situacoes.data', 'ASC');
            $query->innerJoin("eleicao.tipoProcesso", "tipoProcesso")->addSelect("tipoProcesso");

            $query->where("eleicao.id = :id");
            $query->setParameter("id", $id);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a eleição que está vigente por nível da atividade.
     *
     * @param $nivelPrincipal
     * @param $nivelSecundaria
     *
     * @param bool $isVigente
     * @return EleicaoTO
     */
    public function getEleicaoVigentePorNivelAtividade($nivelPrincipal, $nivelSecundaria, $isVigente = true)
    {
        try {
            $query = $this->createQueryBuilder('eleicao');
            $query->innerJoin('eleicao.tipoProcesso', 'tipoProcesso')->addSelect('tipoProcesso');
            $query->innerJoin('eleicao.calendario', 'calendario')->addSelect('calendario');
            $query->innerJoin('calendario.atividadesPrincipais', 'atividadesPrincipais')
                ->addSelect('atividadesPrincipais');
            $query->innerJoin('calendario.arquivos', 'arquivos')
                ->addSelect('arquivos');
            $query->innerJoin('atividadesPrincipais.atividadesSecundarias', 'atividadesSecundarias')
                ->addSelect('atividadesSecundarias');

            $query->where('calendario.excluido = false');

            $query->andWhere('atividadesPrincipais.nivel = :nivelPrincipal');
            $query->setParameter('nivelPrincipal', $nivelPrincipal);

            $query->andWhere('atividadesSecundarias.nivel = :nivelSecundario');
            $query->setParameter('nivelSecundario', $nivelSecundaria);

            if ($isVigente) {
                $query->andWhere('atividadesSecundarias.dataInicio <= :dataAtual');
                $query->andWhere('atividadesSecundarias.dataFim >= :dataAtual');
                $query->setParameter('dataAtual', Utils::getData());

                $this->addCondicaoCalendarioVigente($query);
            }

            $query->orderBy('eleicao.id', 'ASC');
            $query->setMaxResults(1);

            $dadosEleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return EleicaoTO::newInstance($dadosEleicao);
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a eleição da chapa
     *
     * @param $idChapaEleicao
     * @param bool $isAddAtividades
     * @return EleicaoTO|null
     */
    public function getEleicaoPorChapaEleicao($idChapaEleicao, $isAddAtividades = false)
    {
        try {
            $query = $this->getQueryPorChapa();
            $query->innerJoin('atividadesSecundarias.chapasEleicao', 'chapasEleicao');
            $query->leftJoin('calendario.arquivos', 'arquivos')->addSelect('arquivos');

            if ($isAddAtividades) {
                $query->innerJoin('calendario.atividadesPrincipais', 'atividadesPrincipais2');
                $query->innerJoin('atividadesPrincipais2.atividadesSecundarias', 'atividadesSecundarias2');
                $query->addSelect('atividadesPrincipais2')->addSelect('atividadesSecundarias2');
            }

            $query->andWhere('chapasEleicao.id = :idChapaEleicao');
            $query->setParameter('idChapaEleicao', $idChapaEleicao);

            $eleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return EleicaoTO::newInstance($eleicao);
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a eleição que está vigente por calendario ativo.
     *
     * @param bool $isAddAtividades
     * @return EleicaoTO
     */
    public function getEleicaoVigenteComCalendario($isAddAtividades = false)
    {
        try {
            $query = $this->createQueryBuilder('eleicao');
            $query->innerJoin('eleicao.tipoProcesso', 'tipoProcesso')->addSelect('tipoProcesso');
            $query->innerJoin('eleicao.calendario', 'calendario')->addSelect('calendario');

            $query->where('calendario.excluido = false');
            if ($isAddAtividades) {
                $query->innerJoin('calendario.atividadesPrincipais', 'atividadesPrincipais')->addSelect('atividadesPrincipais');
                $query->innerJoin('atividadesPrincipais.atividadesSecundarias', 'atividadesSecundarias')->addSelect('atividadesSecundarias');
            }

            $this->addCondicaoCalendarioVigente($query);

            $query->orderBy('eleicao.id', 'ASC');

            $query->getQuery()->setMaxResults(1);

            $dadosEleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return EleicaoTO::newInstance($dadosEleicao);
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a eleição que está vigente por calendario ativo.
     *
     * @param bool $isAddAtividades
     * @return EleicaoTO
     */
    public function getEleicaoPorCalendario($idCalendario, $isAddAtividades = false)
    {
        try {
            $query = $this->createQueryBuilder('eleicao');
            $query->innerJoin('eleicao.tipoProcesso', 'tipoProcesso')->addSelect('tipoProcesso');
            $query->innerJoin('eleicao.calendario', 'calendario')->addSelect('calendario');

            $query->where('calendario.excluido = false');
            $query->andWhere('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            if ($isAddAtividades) {
                $query->innerJoin('calendario.atividadesPrincipais', 'atividadesPrincipais')->addSelect('atividadesPrincipais');
                $query->innerJoin('atividadesPrincipais.atividadesSecundarias', 'atividadesSecundarias')->addSelect('atividadesSecundarias');
            }

            $dadosEleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return EleicaoTO::newInstance($dadosEleicao);
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a eleição que está vigente por calendario ativo.
     *
     * @param bool $isAddAtividades
     * @return EleicaoTO[]|null
     */
    public function getEleicoesVigenteComCalendario($isAddAtividades = false)
    {
        try {
            $query = $this->createQueryBuilder('eleicao');
            $query->innerJoin('eleicao.tipoProcesso', 'tipoProcesso')->addSelect('tipoProcesso');
            $query->innerJoin('eleicao.calendario', 'calendario')->addSelect('calendario');

            if ($isAddAtividades) {
                $query->innerJoin('calendario.atividadesPrincipais', 'atividadesPrincipais')->addSelect('atividadesPrincipais');
                $query->innerJoin('atividadesPrincipais.atividadesSecundarias', 'atividadesSecundarias')->addSelect('atividadesSecundarias');
            }

            $query->where('calendario.excluido = false');

            $this->addCondicaoCalendarioVigente($query);

            $query->orderBy('eleicao.id', 'ASC');

            $dadosEleicoes = $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

            return array_map(function ($eleicao) {
                return EleicaoTO::newInstance($eleicao);
            }, $dadosEleicoes);
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Recupera as eleiçoes com pedido de substituiçao
     *
     * @return EleicaoTO
     * @throws Exception
     */
    public function getEleicaoVigenteComCalendarioEPedidosSubstituicao()
    {
        try {
            $query = $this->createQueryBuilder('eleicao');
            $query->innerJoin('eleicao.tipoProcesso', 'tipoProcesso')->addSelect('tipoProcesso');
            $query->innerJoin('eleicao.calendario', 'calendario')->addSelect('calendario');
            $query->innerJoin('calendario.arquivos', 'arquivos')->addSelect('arquivos');
            $query->innerJoin('calendario.atividadesPrincipais', 'atividadesPrincipais');
            $query->innerJoin('atividadesPrincipais.atividadesSecundarias', 'atividadesSecundarias');
            $query->innerJoin('atividadesSecundarias.chapasEleicao', 'chapasEleicao');
            $query->innerJoin('chapasEleicao.pedidosSubstituicaoChapa', 'pedidosSubstituicaoChapa');
            $query->where('calendario.ativo = true');
            $query->andWhere('calendario.excluido = false');

            $query->orderBy('eleicao.id', 'ASC');

            $dadosEleicao = $query->getQuery()->getResult();
            return $dadosEleicao;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Recupera as eleiçoes com pedido de impugnacao de resultado
     *
     * @return Eleicao[]
     * @throws Exception
     */
    public function getEleicaoVigenteComCalendarioEPedidosImpugnacaoResultado()
    {
        try {
            $query = $this->createQueryBuilder('eleicao');
            $query->innerJoin('eleicao.tipoProcesso', 'tipoProcesso')->addSelect('tipoProcesso');
            $query->innerJoin('eleicao.calendario', 'calendario')->addSelect('calendario');
            $query->innerJoin('calendario.impugnacoesResultado', 'arquivos');
            $query->where('calendario.ativo = true');
            $query->andWhere('calendario.excluido = false');

            $query->orderBy('eleicao.id', 'ASC');

            $dadosEleicao = $query->getQuery()->getArrayResult();
            return $dadosEleicao;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Recupera as eleiçoes com pedido de impugnacao
     *
     * @return Eleicao[]
     * @throws Exception
     */
    public function getEleicaoVigenteComCalendarioEPedidosImpugnacao()
    {
        try {
            $query = $this->createQueryBuilder('eleicao');
            $query->innerJoin('eleicao.tipoProcesso', 'tipoProcesso')->addSelect('tipoProcesso');
            $query->innerJoin('eleicao.calendario', 'calendario')->addSelect('calendario');
            $query->innerJoin('calendario.arquivos', 'arquivos')->addSelect('arquivos');
            $query->innerJoin('calendario.atividadesPrincipais', 'atividadesPrincipais');
            $query->innerJoin('atividadesPrincipais.atividadesSecundarias', 'atividadesSecundarias');
            $query->innerJoin('atividadesSecundarias.chapasEleicao', 'chapasEleicao');
            $query->innerJoin('chapasEleicao.membrosChapa', 'membrosChapa');
            $query->innerJoin('membrosChapa.pedidoImpugnacao', 'pedidoImpugnacao');
            $query->where('calendario.ativo = true');
            $query->andWhere('calendario.excluido = false');

            $query->orderBy('eleicao.id', 'ASC');

            $dadosEleicao = $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
            return $dadosEleicao;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Recupera a eleição de acordo com id do pedido de substituição
     *
     * @param $idPedidoSubstituicao
     * @param bool $addAtividades
     * @return EleicaoTO|null
     */
    public function getEleicaoPorPedidoSubstituicao($idPedidoSubstituicao, $addAtividades = false)
    {
        try {
            $query = $this->getQueryPorChapa();

            if ($addAtividades) {
                $query->addSelect("atividadesPrincipais");
                $query->addSelect("atividadesSecundarias");
            }

            $query->andWhere("calendario.id in (".$this->getSubQueryIdCalendarioPedidoSubstituicao().")");
            $query->setParameter('idPedidoSubstituicao', $idPedidoSubstituicao);

            $eleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return EleicaoTO::newInstance($eleicao);
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a eleição de acordo com id do pedido de substituição
     *
     * @param $idPedidoImpugnacao
     * @param bool $addAtividades
     * @return EleicaoTO|null
     */
    public function getEleicaoPorPedidoImpugnacao($idPedidoImpugnacao, $addAtividades = false)
    {
        try {
            $query = $this->getQueryPorChapa();

            if ($addAtividades) {
                $query->addSelect("atividadesPrincipais");
                $query->addSelect("atividadesSecundarias");
            }

            $query->andWhere("calendario.id in (".$this->getSubQueryIdCalendarioPedidoImpugnacao().")");
            $query->setParameter('idPedidoImpugnacao', $idPedidoImpugnacao);

            $eleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return EleicaoTO::newInstance($eleicao);
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryPorChapa(): QueryBuilder
    {
        $query = $this->createQueryBuilder('eleicao');
        $query->innerJoin('eleicao.tipoProcesso', 'tipoProcesso')->addSelect('tipoProcesso');
        $query->innerJoin('eleicao.calendario', 'calendario')->addSelect('calendario');
        $query->innerJoin('calendario.atividadesPrincipais', 'atividadesPrincipais');
        $query->innerJoin('atividadesPrincipais.atividadesSecundarias', 'atividadesSecundarias');


        $query->where('calendario.ativo = :ativo');
        $query->setParameter('ativo', true);

        $query->andWhere('calendario.excluido = :excluido');
        $query->setParameter("excluido", false);

        return $query;
    }

    private function getSubQueryIdCalendarioPedidoImpugnacao ()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('calendarioPedidoImpugnacao.id');
        $query->from(PedidoImpugnacao::class, "pedidoImpugnacao");
        $query->innerJoin("pedidoImpugnacao.membroChapa", "membroChapaPedidoImpugnacao");
        $query->innerJoin("membroChapaPedidoImpugnacao.chapaEleicao", "chapaEleicaoPedidoImpugnacao");
        $query->innerJoin("chapaEleicaoPedidoImpugnacao.atividadeSecundariaCalendario", "atividadeSecundariaPedidoImpugnacao");
        $query->innerJoin("atividadeSecundariaPedidoImpugnacao.atividadePrincipalCalendario", "atividadePrincipalPedidoImpugnacao");
        $query->innerJoin("atividadePrincipalPedidoImpugnacao.calendario", "calendarioPedidoImpugnacao");

        $query->where("pedidoImpugnacao.id = :idPedidoImpugnacao");

        return $query;
    }

    private function getSubQueryIdCalendarioPedidoSubstituicao ()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('calendarioPedidoSubst.id');
        $query->from(PedidoSubstituicaoChapa::class, "pedidoSubstituicao");
        $query->innerJoin("pedidoSubstituicao.chapaEleicao", "chapaEleicaoPedidoSubst");
        $query->innerJoin("chapaEleicaoPedidoSubst.atividadeSecundariaCalendario", "atividadeSecundariaPedidoSubst");
        $query->innerJoin("atividadeSecundariaPedidoSubst.atividadePrincipalCalendario", "atividadePrincipalPedidoSubst");
        $query->innerJoin("atividadePrincipalPedidoSubst.calendario", "calendarioPedidoSubst");

        $query->where("pedidoSubstituicao.id = :idPedidoSubstituicao");

        return $query;
    }

    /**
     * Condição para o calendário estar ativi, concluído e dentro do período de vigência
     * @param QueryBuilder $query
     */
    public function addCondicaoCalendarioVigente(QueryBuilder $query): void
    {
        $query->leftJoin("calendario.situacoes", "situacoes");
        $query->leftJoin("situacoes.situacaoCalendario", "situacaoCalendario");

        $query->andWhere('calendario.ativo = :ativo');
        $query->setParameter('ativo', true);

        $query->andWhere('calendario.dataInicioVigencia <= :dataAtual');
        $query->andWhere('calendario.dataFimVigencia >= :dataAtual');
        $query->setParameter('dataAtual', Utils::getData());

        $query->andWhere("situacoes.id in {$this->getSubQueryCalendarioSituacaoAtual()}");
        $query->andWhere("situacaoCalendario.id = :idSituacaoConcluido");
        $query->setParameter("idSituacaoConcluido", Constants::SITUACAO_CALENDARIO_CONCLUIDO);
    }

    /**
     * Método para retornar trecho DQL que busca id do calendário situação.
     *
     * @return string
     */
    private function getSubQueryCalendarioSituacaoAtual()
    {
        $dql = " (SELECT calendarioSituacao5.id";
        $dql .= " FROM App\Entities\CalendarioSituacao calendarioSituacao5 ";
        $dql .= " INNER JOIN calendarioSituacao5.calendario calendario5 ";
        $dql .= " WHERE calendario5.id = calendario.id ";
        $dql .= " AND calendarioSituacao5.data = (select MAX(calendarioSituacao6.data) FROM App\Entities\CalendarioSituacao calendarioSituacao6  ";
        $dql .= " WHERE calendarioSituacao6.calendario = calendario5.id ))  ";
        return $dql;
    }

    /**
     * Recupera a eleição que está vigente por calendario ativo.
     *
     * @param null $idCauUf
     * @return EleicaoTO|null
     */
    public function getEleicaoVigenteComCalendarioPorIesOuUf($idCauUf = null)
    {
        try {
            $query = $this->createQueryBuilder('eleicao');
            $query->innerJoin('eleicao.tipoProcesso', 'tipoProcesso')->addSelect('tipoProcesso');
            $query->innerJoin('eleicao.calendario', 'calendario')->addSelect('calendario');

            $query->where('calendario.excluido = false');

            if (!empty($idCauUf)) {
                $query->innerJoin('calendario.cauUf', 'cauUf');
                $query->andWhere('cauUf.idCauUf = :idCauUf');
                $query->setParameter('idCauUf', $idCauUf);
            } else {
                $query->andWhere('calendario.situacaoIES = true');
            }

            $this->addCondicaoCalendarioVigente($query);

            $query->orderBy('eleicao.id', 'ASC');

            $query->getQuery()->setMaxResults(1);

            $dadosEleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return EleicaoTO::newInstance($dadosEleicao);
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }
}
