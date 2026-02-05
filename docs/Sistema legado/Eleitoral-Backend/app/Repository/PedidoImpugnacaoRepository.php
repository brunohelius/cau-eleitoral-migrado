<?php
/*
 * PedidoImpugnacaoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Config\Constants;
use App\Entities\PedidoImpugnacao;
use App\To\PedidoImpugnacaoUfTO;
use App\To\PedidoSolicitadoTO;
use App\To\QuantidadePedidoImpugnacaoPorUfTO;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'PedidoImpugnacao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class PedidoImpugnacaoRepository extends AbstractRepository
{

    /**
     * Retorna a quantidade de Pedidos de Impugnaçao agrupados para cada UF
     *
     * @param int $idCalendario
     * @param bool $isEmAnalise
     * @param int $tipoCandidatura
     * @return QuantidadePedidoImpugnacaoPorUfTO|QuantidadePedidoImpugnacaoPorUfTO[]
     */
    public function getQuantidadePedidosParaCadaUf(
        int $idCalendario,
        bool $isEmAnalise,
        int $tipoCandidatura,
        int $idProfissionalSolicitante = null
    ) {
        try {
            $query = $this->createQueryBuilder('pedidoImpugnacao')
                ->select('COUNT(pedidoImpugnacao.id) as quantidadePedidosEmAnalise')
                ->innerJoin('pedidoImpugnacao.statusPedidoImpugnacao', 'statusPedidoImpugnacao')
                ->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa')
                ->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao')
                ->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario')
                ->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario',
                    'atividadePrincipalCalendario')
                ->innerJoin('atividadePrincipalCalendario.calendario', 'calendario')
                ->where('calendario.id = :idCalendario')
                ->andWhere('chapaEleicao.tipoCandidatura = :tipoCandidatura')->setParameter('tipoCandidatura',
                    $tipoCandidatura);

            $this->adicionaEmAnalise($isEmAnalise, $query);

            $query->setParameter('idCalendario', $idCalendario);

            if (!empty($idProfissionalSolicitante)) {
                $query->andWhere("pedidoImpugnacao.profissional = :idProfissional")
                    ->setParameter('idProfissional', $idProfissionalSolicitante);
            }

            if ($tipoCandidatura == Constants::TIPO_CANDIDATURA_IES) {
                $query->addSelect('0 as idCauUf');
                $query->groupBy('idCauUf');
                $dados = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_OBJECT);

                return QuantidadePedidoImpugnacaoPorUfTO::newInstance($dados, $tipoCandidatura);

            } else {
                $query->addSelect('chapaEleicao.idCauUf');
                $query->groupBy('chapaEleicao.idCauUf');

                $dados = $query->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);

                if (count($dados) == 1) {
                    return QuantidadePedidoImpugnacaoPorUfTO::newInstance($dados[0], $tipoCandidatura);
                } elseif (count($dados) > 1) {
                    return array_map(function ($item) use ($tipoCandidatura) {
                        return QuantidadePedidoImpugnacaoPorUfTO::newInstance($item, $tipoCandidatura);
                    }, $dados);
                }
            }
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Pedidos de Impugnaçao agrupados para cada UF
     *
     * @param int $idCalendario
     * @param bool $isEmAnalise
     * @param null|int $idCauUf
     * @return QuantidadePedidoImpugnacaoPorUfTO|QuantidadePedidoImpugnacaoPorUfTO[]
     */
    public function getQuantidadePedidosParaUf(int $idCalendario, bool $isEmAnalise, int $idCauUf)
    {
        try {
            $query = $this->createQueryBuilder('pedidoImpugnacao')
                ->select('COUNT(pedidoImpugnacao.id) as quantidadePedidosEmAnalise')
                ->innerJoin('pedidoImpugnacao.statusPedidoImpugnacao', 'statusPedidoImpugnacao')
                ->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa')
                ->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao')
                ->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario')
                ->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario',
                    'atividadePrincipalCalendario')
                ->innerJoin('atividadePrincipalCalendario.calendario', 'calendario')
                ->where('calendario.id = :idCalendario')->setParameter('idCalendario', $idCalendario)
                ->andWhere('chapaEleicao.tipoCandidatura = :tipoCandidatura')->setParameter('tipoCandidatura',
                    Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR)
                ->andWhere('chapaEleicao.idCauUf = :idCauUf')->setParameter('idCauUf', $idCauUf);

            $this->adicionaEmAnalise($isEmAnalise, $query);

            $query->addSelect('chapaEleicao.idCauUf');
            $query->groupBy('chapaEleicao.idCauUf');

            $dados = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return QuantidadePedidoImpugnacaoPorUfTO::newInstance($dados);

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Pedidos de Impugnacao de um UF
     *
     * @param int $idCauUf
     * @param int $idCalendario
     * @return PedidoImpugnacaoUfTO[]
     */
    public function getPedidosPorUf(int $idCauUf, int $idCalendario)
    {
        try {
            $query = $this->consultaPedidoImpugnacaoComJoins();
            $this->whereMembrosResponsaveisAndCalendario($idCalendario, $query);
            $query->andWhere('chapaEleicao.idCauUf = :idCauUf')->setParameter('idCauUf', $idCauUf);

            $query->andWhere('chapaEleicao.tipoCandidatura = :idTipoCandidatura');
            $query->setParameter('idTipoCandidatura', Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR);

            $this->addCondicaoMembroChapaAtual($query);

            $pedidosImpugnacao = $query->getQuery()->getArrayResult();

            return array_map(function ($pedidoImpugnacao) {
                return PedidoImpugnacaoUfTO::newInstance($pedidoImpugnacao);
            }, $pedidosImpugnacao);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Pedidos de Impugnacao de um UF
     *
     * @param int $idCalendario
     * @param int|null $idProfissional
     * @return PedidoImpugnacaoUfTO[]
     */
    public function getPedidosPorResponsavelChapa(int $idCalendario, $idProfissional = null)
    {
        try {
            $query = $this->consultaPedidoImpugnacaoComJoins();
            $this->whereMembrosResponsaveisAndCalendario($idCalendario, $query);

            $query->andWhere("membrosChapa.profissional = :idProfissional")
                ->setParameter('idProfissional', $idProfissional);

            $this->addCondicaoMembroChapaAtual($query);

            $pedidosImpugnacao = $query->getQuery()->getArrayResult();

            return array_map(function ($pedidoImpugnacao) {
                return PedidoImpugnacaoUfTO::newInstance($pedidoImpugnacao);
            }, $pedidosImpugnacao);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Pedidos de Impugnacao Solicitadas por profissional responsável
     *
     * @param int $idCalendario
     * @param int|null $idProfissional
     * @return PedidoImpugnacaoUfTO[]
     */
    public function getPedidosPorProfissionalSolicitante(
        int $idCalendario,
        $idProfissional,
        $idTipoCandidatura,
        $idCauUf = null
    ) {
        try {
            $query = $this->consultaPedidoImpugnacaoComJoins();
            $this->whereMembrosResponsaveisAndCalendario($idCalendario, $query);

            $query->andWhere("pedidoImpugnacao.profissional = :idProfissional")
                ->setParameter('idProfissional', $idProfissional);

            if (!empty($idCauUf)) {
                $query->andWhere('chapaEleicao.idCauUf = :idCauUf')->setParameter('idCauUf', $idCauUf);
            }

            $query->andWhere('chapaEleicao.tipoCandidatura = :idTipoCandidatura');
            $query->setParameter('idTipoCandidatura', $idTipoCandidatura);

            $this->addCondicaoMembroChapaAtual($query);

            $pedidosImpugnacao = $query->getQuery()->getArrayResult();

            return array_map(function ($pedidoImpugnacao) {
                return PedidoImpugnacaoUfTO::newInstance($pedidoImpugnacao);
            }, $pedidosImpugnacao);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Pedidos de Impugnacao da IES
     *
     * @param int $idCalendario
     * @return PedidoImpugnacaoUfTO[]|null
     */
    public function getPedidosPorIes(int $idCalendario)
    {
        try {
            $query = $this->consultaPedidoImpugnacaoComJoins();
            $this->whereMembrosResponsaveisAndCalendario($idCalendario, $query);
            $query->andWhere('chapaEleicao.tipoCandidatura = :tipoCandidatura')->setParameter('tipoCandidatura',
                Constants::TIPO_CANDIDATURA_IES);

            $this->addCondicaoMembroChapaAtual($query);

            $pedidosImpugnacao = $query->getQuery()->getArrayResult();

            return array_map(function ($pedidoImpugnacao) {
                return PedidoImpugnacaoUfTO::newInstance($pedidoImpugnacao);
            }, $pedidosImpugnacao);

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna um Pedido de Impugnaçao
     *
     * @param int $id
     * @return array
     */
    public function getPorId(int $id)
    {
        try {
            $query = $this->createQueryBuilder('pedidoImpugnacao')
                ->innerJoin('pedidoImpugnacao.statusPedidoImpugnacao',
                    'statusPedidoImpugnacao')->addSelect("statusPedidoImpugnacao")
                ->innerJoin('pedidoImpugnacao.arquivosPedidoImpugnacao', 'arquivos')->addSelect('arquivos')
                ->innerJoin('pedidoImpugnacao.profissional', 'profissional')->addSelect('profissional')
                ->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa')->addSelect('membroChapa')
                ->innerJoin('membroChapa.profissional', 'profissionalMembro')->addSelect('profissionalMembro')
                ->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao')->addSelect('chapaEleicao')
                ->innerJoin('chapaEleicao.tipoCandidatura', 'tipoCandidatura')->addSelect('tipoCandidatura')
                ->innerJoin('membroChapa.tipoParticipacaoChapa',
                    'tipoParticipacaoChapa')->addSelect('tipoParticipacaoChapa')
                ->innerJoin('chapaEleicao.atividadeSecundariaCalendario',
                    'atividadeSecundariaCalendario')->addSelect("atividadeSecundariaCalendario")
                ->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario',
                    'atividadePrincipalCalendario')
                ->innerJoin('atividadePrincipalCalendario.calendario', 'calendario')
                ->leftJoin('pedidoImpugnacao.respostasDeclaracaoPedidoImpugnacao',
                    'declaracoes')->addSelect('declaracoes')
                ->leftJoin('declaracoes.respostaDeclaracao', 'respostaDeclaracao')->addSelect('respostaDeclaracao')
                ->leftJoin('respostaDeclaracao.itensResposta', 'itensResposta')->addSelect('itensResposta')
                ->where('chapaEleicao.excluido = false')
                ->andWhere('pedidoImpugnacao.id = :id');

            $query->setParameter(':id', $id);

            return $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna todos os pedidos cadastrados para um calendário
     *
     * @param int $idCalendario
     * @param $idStatusPedidoImpugnacao
     * @return PedidoImpugnacao[]|null
     */
    public function getPorCalendario(int $idCalendario, $idStatusPedidoImpugnacao = null)
    {
        try {
            $query = $this->createQueryBuilder('pedidoImpugnacao');
            $query->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa')->addSelect('membroChapa');
            $query->innerJoin("membroChapa.profissional", 'profissional')->addSelect('profissional');
            $query->innerJoin('profissional.pessoa', 'pessoa')->addSelect('pessoa');
            $query->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao')->addSelect('chapaEleicao');
            $query->innerJoin('chapaEleicao.tipoCandidatura', 'tipoCandidatura')->addSelect('tipoCandidatura');
            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario')
                ->addSelect('atividadeSecundariaCalendario');
            $query->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario','atividadePrincipalCalendario')
                ->addSelect('atividadePrincipalCalendario');
            $query->innerJoin('atividadePrincipalCalendario.calendario', 'calendario')
                ->addSelect('calendario');
            $query->innerJoin('calendario.eleicao', 'eleicao')->addSelect('eleicao');
            $query->where('chapaEleicao.excluido = false');

            $query->andWhere('atividadePrincipalCalendario.calendario = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            if (!empty($idStatusPedidoImpugnacao)) {
                $query->andWhere("pedidoImpugnacao.statusPedidoImpugnacao = :idStatus");
                $query->setParameter('idStatus', $idStatusPedidoImpugnacao);
            }

            $pedidosImpugnacao = $query->getQuery()->getArrayResult();

            return array_map(function ($pedidoImpugnacao) {
                return PedidoImpugnacao::newInstance($pedidoImpugnacao);
            }, $pedidosImpugnacao);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna todos os pedidos cadastrados para um calendário
     *
     * @param int $idCalendario
     * @param int $idMembroChapa
     * @param $idProfissional
     * @return PedidoImpugnacao[]|null
     */
    public function getPorCalendarioAndMembroChapa(int $idCalendario, $idMembroChapa, $idProfissional)
    {
        try {
            $query = $this->createQueryBuilder('pedidoImpugnacao');
            $query->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa')->addSelect('membroChapa');
            $query->innerJoin('pedidoImpugnacao.profissional', 'profissional')->addSelect('profissional');
            $query->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao')->addSelect('chapaEleicao');
            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario');
            $query->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario',
                'atividadePrincipalCalendario');
            $query->where('chapaEleicao.excluido = false');

            $query->andWhere('atividadePrincipalCalendario.calendario = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            $query->andWhere('membroChapa.id = :idMembroChapa');
            $query->setParameter('idMembroChapa', $idMembroChapa);

            $query->andWhere('profissional.id = :idProfissional');
            $query->setParameter('idProfissional', $idProfissional);

            $pedidosImpugnacao = $query->getQuery()->getArrayResult();

            return array_map(function ($pedidoImpugnacao) {
                return PedidoImpugnacao::newInstance($pedidoImpugnacao);
            }, $pedidosImpugnacao);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Método auxiliar que adiciona condição para buscar apenas membros atuais
     * que esteja na situação de cadastrodo ou subsituido deferido
     *
     * @param QueryBuilder $query
     * @return void
     */
    private function addCondicaoMembroChapaAtual(QueryBuilder &$query): void
    {
        $situacoes = [Constants::ST_MEMBRO_CHAPA_CADASTRADO, Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO];
        $query->andWhere('membrosChapa.situacaoMembroChapa IN (:situacoes)');
        $query->setParameter('situacoes', $situacoes, Connection::PARAM_INT_ARRAY);
    }

    /**
     * @return QueryBuilder
     */
    private function consultaPedidoImpugnacaoComJoins(): QueryBuilder
    {
        $query = $this->createQueryBuilder('pedidoImpugnacao')
            ->innerJoin('pedidoImpugnacao.statusPedidoImpugnacao',
                'statusPedidoImpugnacao')->addSelect('statusPedidoImpugnacao')
            ->innerJoin('pedidoImpugnacao.profissional', 'profissional')->addSelect('profissional')
            ->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa')->addSelect('membroChapa')
            ->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao')->addSelect('chapaEleicao')
            ->innerJoin('chapaEleicao.tipoCandidatura', 'tipoCandidatura')
            ->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario')
            ->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario', 'atividadePrincipalCalendario')
            ->innerJoin('atividadePrincipalCalendario.calendario', 'calendario')
            ->innerJoin('chapaEleicao.membrosChapa', 'membrosChapa')->addSelect('membrosChapa')
            ->innerJoin('membrosChapa.profissional', 'profissionalMembro')->addSelect('profissionalMembro');
        return $query;
    }

    /**
     * @param int $idCalendario
     * @param QueryBuilder $query
     * @return void
     */
    private function whereMembrosResponsaveisAndCalendario(int $idCalendario, QueryBuilder &$query): void
    {
        $query->where('membrosChapa.situacaoResponsavel = true')
            ->andWhere('calendario.id = :idCalendario')->setParameter('idCalendario', $idCalendario);
        $query->andWhere("membrosChapa.statusParticipacaoChapa = :statusParticipacaoChapa")
            ->setParameter('statusParticipacaoChapa', Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO);
    }

    /**
     * @param bool $isEmAnalise
     * @param QueryBuilder $query
     */
    private function adicionaEmAnalise(bool $isEmAnalise, QueryBuilder &$query): void
    {
        if ($isEmAnalise) {
            $query->andWhere('statusPedidoImpugnacao.id = :idStatusImpugnacao')->setParameter('idStatusImpugnacao',
                Constants::STATUS_IMPUGNACAO_EM_ANALISE);
        }
    }

    /**
     * Método que retorna o maior número de protocolo do pedido de impugna
     *
     * @param $idCalendario
     *
     * @return mixed|null
     */
    public function getUltimoProtocoloPorCalendario($idCalendario)
    {
        try {
            $query = $this->createQueryBuilder('pedidoImpugnacao');
            $query->select('MAX(pedidoImpugnacao.numeroProtocolo)');
            $query->join("pedidoImpugnacao.membroChapa", "membroChapa");
            $query->join("membroChapa.chapaEleicao", "chapaEleicao");
            $query->join("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
            $query->join("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->join("atividadePrincipal.calendario", "calendario");

            $query->where('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * Retorna a od ID da atividade secundário 3.1 de acordo com o pedido de impugnação
     *
     * @param int $id
     * @return integer|null
     */
    public function getIdAtividadeSecundariaPedidoImpugnacao(int $id)
    {
        try {
            $query = $this->createQueryBuilder('pedidoImpugnacao');
            $query->select("atividadesSecundariasPedido.id");
            $query->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa');
            $query->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao');
            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundaria');
            $query->innerJoin('atividadeSecundaria.atividadePrincipalCalendario', 'atividadePrincipal');
            $query->innerJoin('atividadePrincipal.calendario', 'calendario');
            $query->innerJoin('calendario.atividadesPrincipais', 'atividadesPrincipaisPedido');
            $query->innerJoin("atividadesPrincipaisPedido.atividadesSecundarias", "atividadesSecundariasPedido");

            $query->where('pedidoImpugnacao.id = :id');
            $query->setParameter('id', $id);

            $query->andWhere("atividadesPrincipaisPedido.nivel = :nivelPrincipal");
            $query->setParameter("nivelPrincipal", 3);

            $query->andWhere("atividadesSecundariasPedido.nivel = :nivelSecundaria");
            $query->setParameter("nivelSecundaria", 1);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Pedidos de Substituiçao por chapa do Usuario Logado
     *
     * @param int $idChapa
     * @return array|null
     */
    public function getPedidosSolicitadosPorChapa(int $idChapa)
    {
        try {
            $query = $this->createQueryBuilder('pedidoImpugnacao');
            $query->select([
                'pedidoImpugnacao.id',
                'pedidoImpugnacao.numeroProtocolo numeroProtocolo',
                'statusPedidoImpugnacao.id idStatus',
                'statusPedidoImpugnacao.descricao descricaoStatus'
            ]);
            $query->innerJoin('pedidoImpugnacao.statusPedidoImpugnacao', 'statusPedidoImpugnacao');
            $query->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa');
            $query->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao');

            $query->where('chapaEleicao.id = :idChapa');
            $query->setParameter('idChapa', $idChapa);

            $pedidos = $query->getQuery()->getArrayResult();

            return array_map(function($pedidos){
                return PedidoSolicitadoTO::newInstance($pedidos);
            }, $pedidos);
        } catch (Exception $e) {
            return null;
        }
    }
}
