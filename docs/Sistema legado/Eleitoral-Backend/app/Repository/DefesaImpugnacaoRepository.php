<?php
/*
 * DefesaImpugnacaoRepository.php Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\DefesaImpugnacao;
use App\Entities\PedidoImpugnacao;
use App\To\DefesaImpugnacaoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'DefesaImpugnacao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DefesaImpugnacaoRepository extends AbstractRepository
{
    /**
     * Buscar Defesa de pedido de impugnação por 'id'
     *
     * @param int $id
     * @return DefesaImpugnacao|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorId(int $id)
    {
        try {
            $query = $this->createQueryBuilder('defesa');

            $query->where("defesa.id = :id");
            $query->setParameter("id", $id);

            $defesa = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return DefesaImpugnacao::newInstance($defesa);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Defesa de pedido de impugnação por 'id' do pedido de impugnação.
     *
     * @param int $idPedidoImpugnacao
     * @return DefesaImpugnacao|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorPedidoImpugnacao(int $idPedidoImpugnacao)
    {
        try {
            $query = $this->createQueryBuilder('defesa');

            $query->join('defesa.pedidoImpugnacao', 'pedidoImpugnacao')->addSelect('pedidoImpugnacao');
            $query->join('pedidoImpugnacao.statusPedidoImpugnacao', 'statusPedidoImpugnacao')->addSelect('statusPedidoImpugnacao');

            $query->leftJoin('defesa.arquivos', 'arquivos')->addSelect('arquivos');

            $query->where("pedidoImpugnacao.id = :idPedidoImpugnacao");
            $query->setParameter("idPedidoImpugnacao", $idPedidoImpugnacao);

            $data = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return  DefesaImpugnacao::newInstance($data);

        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a od ID da atividade secundário 3.2 de acordo com o defesa de impugnação
     *
     * @param int $id
     * @return integer|null
     */
    public function getIdAtividadeSecundariaDefesaImpugnacao(int $id)
    {
        try {
            $query = $this->createQueryBuilder('defesaImpugnacao');
            $query->select("atividadesSecundariasDefesa.id");
            $query->innerJoin('defesaImpugnacao.pedidoImpugnacao', 'pedidoImpugnacao');
            $query->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa');
            $query->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao');
            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundaria');
            $query->innerJoin('atividadeSecundaria.atividadePrincipalCalendario', 'atividadePrincipal');
            $query->innerJoin('atividadePrincipal.calendario', 'calendario');
            $query->innerJoin('calendario.atividadesPrincipais', 'atividadesPrincipaisDefesa');
            $query->innerJoin("atividadesPrincipaisDefesa.atividadesSecundarias","atividadesSecundariasDefesa");

            $query->where('defesaImpugnacao.id = :id');
            $query->setParameter('id', $id);

            $query->andWhere("atividadesPrincipaisDefesa.nivel = :nivelPrincipal");
            $query->setParameter("nivelPrincipal", 3);

            $query->andWhere("atividadesSecundariasDefesa.nivel = :nivelSecundaria");
            $query->setParameter("nivelSecundaria", 2);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna todos as defesas cadastradas de acordo com o id do calendário
     *
     * @param int $idCalendario
     * @return DefesaImpugnacao[]|null
     */
    public function getPorCalendario(int $idCalendario)
    {
        try {
            $query = $this->createQueryBuilder('defesaImpugnacao');
            $query->innerJoin('defesaImpugnacao.pedidoImpugnacao', 'pedidoImpugnacao')->addSelect('pedidoImpugnacao');
            $query->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa')->addSelect('membroChapa');
            $query->innerJoin('membroChapa.profissional', 'profissionalMembro')->addSelect('profissionalMembro');
            $query->innerJoin('pedidoImpugnacao.profissional', 'profissional')->addSelect('profissional');
            $query->innerJoin('profissional.pessoa', 'pessoa')->addSelect('pessoa');
            $query->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao')->addSelect('chapaEleicao');
            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario');
            $query->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario', 'atividadePrincipal');
            $query->where('chapaEleicao.excluido = false');

            $query->andWhere('atividadePrincipal.calendario = :idCalendario');

            $query->setParameter('idCalendario', $idCalendario);

            $defesasImpugnacao = $query->getQuery()->getArrayResult();

            return array_map(function($defesaImpugnacao){
                return DefesaImpugnacao::newInstance($defesaImpugnacao);
            }, $defesasImpugnacao);
        } catch (\Exception $e) {
            return null;
        }
    }
}
