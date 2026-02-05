<?php

namespace App\Repository;

use App\Config\Constants;
use App\Entities\PedidoSubstituicaoChapa;
use App\To\PedidoSolicitadoTO;
use App\To\PedidoSubstituicaoChapaTO;
use App\To\QuantidadePedidoSubstituicaoPorUfTO;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'PedidoSubstituicaoChapa'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class PedidoSubstituicaoChapaRepository extends AbstractRepository
{

    /**
     * Retorna a quantidade de Pedidos de Substituiçao agrupados por UF
     *
     * @param int $id
     * @return PedidoSubstituicaoChapaTO
     * @throws Exception
     */
    public function getPorId(int $id)
    {
        try {
            $query = $this->createQueryBuilder('pedidoSubstituicaoChapa');
            $query->innerJoin('pedidoSubstituicaoChapa.chapaEleicao', 'chapaEleicao')->addSelect('chapaEleicao');
            $query->innerJoin("chapaEleicao.tipoCandidatura", "tipoCandidatura")->addSelect("tipoCandidatura");
            $query->innerJoin('pedidoSubstituicaoChapa.statusSubstituicaoChapa', 'statusSubstituicaoChapa')->addSelect('statusSubstituicaoChapa');
            $query->innerJoin('pedidoSubstituicaoChapa.membrosChapaSubstituicao', 'membrosChapaSubstituicao')->addSelect('membrosChapaSubstituicao');

            $query->leftJoin("chapaEleicao.chapaEleicaoStatus", "chapaEleicaoStatus")->addSelect("chapaEleicaoStatus");
            $query->leftJoin("chapaEleicaoStatus.statusChapa", "statusChapa")->addSelect("statusChapa");

            $query->leftJoin('membrosChapaSubstituicao.membroChapaSubstituido', 'membroChapaSubstituido')->addSelect('membroChapaSubstituido');
            $query->leftJoin("membroChapaSubstituido.tipoMembroChapa", "tipoMembroChapaSubstituido")->addSelect('tipoMembroChapaSubstituido');
            $query->leftJoin("membroChapaSubstituido.tipoParticipacaoChapa", "tipoParticipacaoChapaSubstituido")->addSelect("tipoParticipacaoChapaSubstituido");
            $query->leftJoin("membroChapaSubstituido.statusParticipacaoChapa", "statusParticipacaoChapaSubstituido")->addSelect("statusParticipacaoChapaSubstituido");
            $query->leftJoin("membroChapaSubstituido.statusValidacaoMembroChapa", "statusValidacaoMembroChapaSubstituido")->addSelect('statusValidacaoMembroChapaSubstituido');
            $query->leftJoin("membroChapaSubstituido.pendencias", "pendenciasSubstituido")->addSelect('pendenciasSubstituido');
            $query->leftJoin("pendenciasSubstituido.tipoPendencia", "tipoPendenciaSubstituido")->addSelect('tipoPendenciaSubstituido');
            $query->leftJoin("membroChapaSubstituido.profissional", "profissionalSubstituido")->addSelect('profissionalSubstituido');
            $query->leftJoin("profissionalSubstituido.pessoa", "pessoaSubstituido")->addSelect('pessoaSubstituido');

            $query->leftJoin('membrosChapaSubstituicao.membroChapaSubstituto', 'membroChapaSubstituto')->addSelect('membroChapaSubstituto');
            $query->leftJoin("membroChapaSubstituto.tipoMembroChapa", "tipoMembroChapaSubstituto")->addSelect('tipoMembroChapaSubstituto');
            $query->leftJoin("membroChapaSubstituto.tipoParticipacaoChapa", "tipoParticipacaoChapaSubstituto")->addSelect('tipoParticipacaoChapaSubstituto');
            $query->leftJoin("membroChapaSubstituto.statusParticipacaoChapa", "statusParticipacaoChapaSubstituto")->addSelect('statusParticipacaoChapaSubstituto');
            $query->leftJoin("membroChapaSubstituto.statusValidacaoMembroChapa", "statusValidacaoMembroChapaSubstituto")->addSelect('statusValidacaoMembroChapaSubstituto');
            $query->leftJoin("membroChapaSubstituto.pendencias", "pendenciasSubstituto")->addSelect('pendenciasSubstituto');
            $query->leftJoin("pendenciasSubstituto.tipoPendencia", "tipoPendenciaSubstituto")->addSelect('tipoPendenciaSubstituto');
            $query->leftJoin("membroChapaSubstituto.profissional", "profissionalSubstituto")->addSelect('profissionalSubstituto');
            $query->leftJoin("profissionalSubstituto.pessoa", "pessoaSubstituto")->addSelect('pessoaSubstituto');

            $query->where('chapaEleicao.excluido = false');

            $query->andWhere('pedidoSubstituicaoChapa.id = :id');
            $query->setParameter('id', $id);

            $pedidoSubstituicaoChapa = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return PedidoSubstituicaoChapaTO::newInstance($pedidoSubstituicaoChapa);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a od ID da atividade secundário 2.3 de acordo com o pedido de substiuição
     *
     * @param int $id
     * @return integer|null
     */
    public function getIdAtividadeSecundariPedidoSubstituicao(int $id)
    {
        try {
            $query = $this->createQueryBuilder('pedidoSubstituicaoChapa');
            $query->select("atividadeSecundariaPedido.id");
            $query->innerJoin('pedidoSubstituicaoChapa.chapaEleicao', 'chapaEleicao');
            $query->innerJoin('pedidoSubstituicaoChapa.statusSubstituicaoChapa', 'statusSubstituicaoChapa');
            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario');
            $query->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario', 'atividadePrincipalCalendario');
            $query->innerJoin("atividadePrincipalCalendario.atividadesSecundarias","atividadeSecundariaPedido");

            $query->where('pedidoSubstituicaoChapa.id = :id');
            $query->setParameter('id', $id);

            $query->andWhere("atividadeSecundariaPedido.nivel = :nivel");
            $query->setParameter("nivel", 3);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Pedidos de Substituiçao agrupados para cada UF
     *
     * @param int $idCalendario
     * @param bool $isJulgados
     * @param int $tipoCandidatura
     * @return QuantidadePedidoSubstituicaoPorUfTO|QuantidadePedidoSubstituicaoPorUfTO[]
     */
    public function getQuantidadePedidosParaCadaUf(int $idCalendario, bool $isJulgados, int $tipoCandidatura)
    {
        try {
            $query = $this->createQueryBuilder('pedidoSubstituicaoChapa')
                ->innerJoin('pedidoSubstituicaoChapa.chapaEleicao', 'chapaEleicao')
                ->innerJoin('pedidoSubstituicaoChapa.statusSubstituicaoChapa', 'statusSubstituicaoChapa')
                ->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario')
                ->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario', 'atividadePrincipalCalendario')
                ->innerJoin('atividadePrincipalCalendario.calendario', 'calendario')
                ->where('calendario.id = :idCalendario')
                ->andWhere('chapaEleicao.tipoCandidatura = :tipoCandidatura');

            if ($isJulgados) {
                $query->select('COUNT(pedidoSubstituicaoChapa.id) as quantidadePedidosJulgados');
                $query->andWhere('statusSubstituicaoChapa.id > 1');
            } else {
                $query->select('COUNT(pedidoSubstituicaoChapa.id) as quantidadePedidos');
            }

            $query->setParameter('tipoCandidatura', $tipoCandidatura);
            $query->setParameter('idCalendario', $idCalendario);

            if ($tipoCandidatura == Constants::TIPO_CANDIDATURA_IES) {
                $query->addSelect('0 as idCauUf');
                $dados = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_OBJECT);

                return QuantidadePedidoSubstituicaoPorUfTO::newInstance($dados, $tipoCandidatura);
            } else {
                $query->addSelect('chapaEleicao.idCauUf');
                $query->groupBy('chapaEleicao.idCauUf');
                $dados = $query->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);

                if(count($dados) == 1) {
                    return QuantidadePedidoSubstituicaoPorUfTO::newInstance($dados[0], $tipoCandidatura);
                }
                elseif(count($dados) > 1) {
                    return array_map(function ($item) use ($tipoCandidatura) {
                        return QuantidadePedidoSubstituicaoPorUfTO::newInstance($item, $tipoCandidatura);
                    }, $dados);
                }
            }
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Pedidos de Substituiçao agrupados por UF
     *
     * @param int $idCauUf
     * @param int $idCalendario
     * @return PedidoSubstituicaoChapaTO[]
     */
    public function getPedidosPorUf(int $idCauUf, int $idCalendario)
    {
        try {
            $query = $this->createQueryBuilder('pedidoSubstituicaoChapa')
                ->innerJoin('pedidoSubstituicaoChapa.statusSubstituicaoChapa', 'statusSubstituicaoChapa')->addSelect('statusSubstituicaoChapa')
                ->innerJoin('pedidoSubstituicaoChapa.chapaEleicao', 'chapaEleicao')->addSelect('chapaEleicao')
                ->innerJoin('chapaEleicao.tipoCandidatura', 'tipoCandidatura')->addSelect('tipoCandidatura')
                ->innerJoin('chapaEleicao.membrosChapa', 'membrosChapa')->addSelect('membrosChapa')
                ->innerJoin('membrosChapa.profissional', 'profissional')->addSelect('profissional')
                ->innerJoin('profissional.pessoa', 'pessoa')->addSelect('pessoa')
                ->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario')
                ->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario', 'atividadePrincipalCalendario')
                ->innerJoin('atividadePrincipalCalendario.calendario', 'calendario')
                ->where('chapaEleicao.excluido = false')
                ->andWhere('membrosChapa.situacaoResponsavel = true')
                ->andWhere('calendario.id = :idCalendario')
                ->andWhere('chapaEleicao.idCauUf = :idCauUf')
                ->andWhere('chapaEleicao.tipoCandidatura = :idTipoCandidatura');

            $query->setParameter('idCauUf', $idCauUf);
            $query->setParameter('idCalendario', $idCalendario);
            $query->setParameter('idTipoCandidatura', Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR);

            $this->addCondicaoMembroChapaAtual($query);

            $pedidosSubstituicaoChapa = $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

            return array_map(function($pedidoSubstituicaoChapa){
                return PedidoSubstituicaoChapaTO::newInstance($pedidoSubstituicaoChapa);
            },$pedidosSubstituicaoChapa);
        } catch (Exception $e) {
            Log::info($e);
            return null;
        }
    }

    /**
     * Retorna a quantidade de Pedidos de Substituiçao da IES
     *
     * @param int $idCauUf
     * @param int $idCalendario
     * @return PedidoSubstituicaoChapaTO[]
     */
    public function getPedidosPorIes(int $idCalendario)
    {
        try {
            $query = $this->createQueryBuilder('pedidoSubstituicaoChapa')
                ->innerJoin('pedidoSubstituicaoChapa.statusSubstituicaoChapa', 'statusSubstituicaoChapa')->addSelect('statusSubstituicaoChapa')
                ->innerJoin('pedidoSubstituicaoChapa.chapaEleicao', 'chapaEleicao')->addSelect('chapaEleicao')
                ->innerJoin('chapaEleicao.tipoCandidatura', 'tipoCandidatura')->addSelect('tipoCandidatura')
                ->innerJoin('chapaEleicao.membrosChapa', 'membrosChapa')->addSelect('membrosChapa')
                ->innerJoin('membrosChapa.profissional', 'profissional')->addSelect('profissional')
                ->innerJoin('profissional.pessoa', 'pessoa')->addSelect('pessoa')
                ->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario')
                ->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario', 'atividadePrincipalCalendario')
                ->innerJoin('atividadePrincipalCalendario.calendario', 'calendario')
                ->where('chapaEleicao.excluido = false')
                ->andWhere('calendario.id = :idCalendario')
                ->setParameter('idCalendario', $idCalendario)
                ->andWhere('membrosChapa.situacaoResponsavel = true')
                ->andWhere('chapaEleicao.tipoCandidatura = :tipoCandidatura')
                ->setParameter('tipoCandidatura', Constants::TIPO_CANDIDATURA_IES);

            $this->addCondicaoMembroChapaAtual($query);

            $pedidosSubstituicaoChapa = $query->getQuery()->getArrayResult();

            return array_map(function($pedidoSubstituicaoChapa){
                return PedidoSubstituicaoChapaTO::newInstance($pedidoSubstituicaoChapa);
            },$pedidosSubstituicaoChapa);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Pedidos de Substituiçao por chapa do Usuario Logado
     *
     * @param int $idProfissional
     * @param int $idCalendario
     * @return array|null
     */
    public function getPedidosChapaPorResponsavelChapa(int $idProfissional, int $idCalendario)
    {
        try {
            $query = $this->createQueryBuilder('pedidoSubstituicaoChapa');
            $query->innerJoin('pedidoSubstituicaoChapa.chapaEleicao', 'chapaEleicao');
            $query->innerJoin('chapaEleicao.membrosChapa', 'membrosChapa');
            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario');
            $query->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario', 'atividadePrincipalCalendario');
            $query->innerJoin('atividadePrincipalCalendario.calendario', 'calendario');
            $query->innerJoin('pedidoSubstituicaoChapa.statusSubstituicaoChapa', 'statusSubstituicaoChapa')->addSelect('statusSubstituicaoChapa');
            $query->innerJoin('pedidoSubstituicaoChapa.membrosChapaSubstituicao', 'membrosChapaSubstituicao')->addSelect('membrosChapaSubstituicao');

            $query->leftJoin('membrosChapaSubstituicao.membroChapaSubstituido', 'membroChapaSubstituido')->addSelect('membroChapaSubstituido');
            $query->leftJoin("membroChapaSubstituido.tipoMembroChapa", "tipoMembroChapaSubstituido")->addSelect('tipoMembroChapaSubstituido');
            $query->leftJoin("membroChapaSubstituido.tipoParticipacaoChapa", "tipoParticipacaoChapaSubstituido")->addSelect("tipoParticipacaoChapaSubstituido");
            $query->leftJoin("membroChapaSubstituido.profissional", "profissionalSubstituido")->addSelect('profissionalSubstituido');
            $query->leftJoin("profissionalSubstituido.pessoa", "pessoaSubstituido")->addSelect('pessoaSubstituido');

            $query->leftJoin('membrosChapaSubstituicao.membroChapaSubstituto', 'membroChapaSubstituto')->addSelect('membroChapaSubstituto');
            $query->leftJoin("membroChapaSubstituto.tipoMembroChapa", "tipoMembroChapaSubstituto")->addSelect('tipoMembroChapaSubstituto');
            $query->leftJoin("membroChapaSubstituto.tipoParticipacaoChapa", "tipoParticipacaoChapaSubstituto")->addSelect('tipoParticipacaoChapaSubstituto');
            $query->leftJoin("membroChapaSubstituto.profissional", "profissionalSubstituto")->addSelect('profissionalSubstituto');
            $query->leftJoin("profissionalSubstituto.pessoa", "pessoaSubstituto")->addSelect('pessoaSubstituto');

            $query->where('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            $query->andWhere('membrosChapa.situacaoResponsavel = true');

            $query->andWhere('membrosChapa.profissional = :idProfissional');
            $query->setParameter('idProfissional', $idProfissional);

            $this->addCondicaoMembroChapaAtual($query);

            $query->orderBy("pedidoSubstituicaoChapa.numeroProtocolo", "ASC");

            $pedidosSubstituicaoChapa = $query->getQuery()->getArrayResult();

            return array_map(function($pedidoSubstituicaoChapa){
                return PedidoSubstituicaoChapaTO::newInstance($pedidoSubstituicaoChapa);
            }, $pedidosSubstituicaoChapa);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna os pedidos conforme o id do calendário
     *
     * @param int $idCalendario
     * @param int|null $idStatusPedidoSubstituicao
     * @return array|null
     */
    public function getPorCalendario(int $idCalendario, int $idStatusPedidoSubstituicao = null)
    {
        try {
            $query = $this->createQueryBuilder('pedidoSubstituicaoChapa');
            $query->innerJoin('pedidoSubstituicaoChapa.chapaEleicao', 'chapaEleicao');
            $query->innerJoin('chapaEleicao.membrosChapa', 'membrosChapa');
            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundaria');
            $query->innerJoin('atividadeSecundaria.atividadePrincipalCalendario', 'atividadePrincipal');

            $query->where('atividadePrincipal.calendario = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            if(!empty($idStatusPedidoSubstituicao)) {
                $query->andWhere('pedidoSubstituicaoChapa.statusSubstituicaoChapa = :idStatus');
                $query->setParameter('idStatus', $idStatusPedidoSubstituicao);
            }

            return $query->getQuery()->getResult();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Método que retorna o maior numero de um Histórico de Extrato de Conselheiros
     *
     * @param $idCalendario
     *
     * @return mixed|null
     */
    public function getUltimoProtocoloPorCalendario($idCalendario)
    {
        try {
            $query = $this->createQueryBuilder('pedidoSubstituicaoChapa');
            $query->select('MAX(pedidoSubstituicaoChapa.numeroProtocolo)');
            $query->join("pedidoSubstituicaoChapa.chapaEleicao", "chapaEleicao");
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
     * Método para buscar id do pedido de substituição a partir do tipo do membro e ordem
     *
     * @param $idChapaEleicao
     * @param $idTipoMembro
     * @param $numeroOrdem
     * @return mixed|null
     */
    public function getIdPedidoSubstituicaoPorTipoMembroChapa(
        $idChapaEleicao,
        $idTipoMembro,
        $numeroOrdem
    ) {
        try {
            $query = $this->createQueryBuilder('pedidoSubstituicaoChapa');
            $query->select('DISTINCT (pedidoSubstituicaoChapa.id)');
            $query->innerJoin('pedidoSubstituicaoChapa.membrosChapaSubstituicao', 'membrosChapaSubstituicao');
            $query->leftJoin('membrosChapaSubstituicao.membroChapaSubstituido', 'membroChapaSubstituido');
            $query->leftJoin('membrosChapaSubstituicao.membroChapaSubstituto', 'membroChapaSubstituto');
            $query->where('pedidoSubstituicaoChapa.chapaEleicao = :idChapa');

            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->andX(
                        $query->expr()->eq("membroChapaSubstituto.tipoMembroChapa", ":idTipoMembro"),
                        $query->expr()->eq("membroChapaSubstituto.numeroOrdem", ":numeroOrdem")
                    ),
                    $query->expr()->andX(
                        $query->expr()->eq("membroChapaSubstituido.tipoMembroChapa", ":idTipoMembro"),
                        $query->expr()->eq("membroChapaSubstituido.numeroOrdem", ":numeroOrdem")
                    )
                )
            );

            $query->setParameter('idChapa', $idChapaEleicao);
            $query->setParameter("numeroOrdem", $numeroOrdem);
            $query->setParameter("idTipoMembro", $idTipoMembro);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException $e) {
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
            $query = $this->createQueryBuilder('pedidoSubstituicaoChapa');
            $query->select([
                'pedidoSubstituicaoChapa.id',
                'pedidoSubstituicaoChapa.numeroProtocolo numeroProtocolo',
                'statusSubstituicaoChapa.id idStatus',
                'statusSubstituicaoChapa.descricao descricaoStatus'
            ]);
            $query->innerJoin('pedidoSubstituicaoChapa.chapaEleicao', 'chapaEleicao');
            $query->innerJoin('pedidoSubstituicaoChapa.statusSubstituicaoChapa', 'statusSubstituicaoChapa');

            $query->where('chapaEleicao.id = :idChapa');
            $query->setParameter('idChapa', $idChapa);

            $pedidosSubstituicaoChapa = $query->getQuery()->getArrayResult();

            return array_map(function($pedidoSubstituicaoChapa){
                return PedidoSolicitadoTO::newInstance($pedidoSubstituicaoChapa);
            }, $pedidosSubstituicaoChapa);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Método auxiliar que adiciona condição para buscar apenas membros atuais
     * que esteja na situação de cadastrodo ou subsituido deferido
     *
     * @param QueryBuilder $query
     */
    private function addCondicaoMembroChapaAtual(QueryBuilder $query): void
    {
        $situacaoes = [Constants::ST_MEMBRO_CHAPA_CADASTRADO, Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO];
        $query->andWhere('membrosChapa.situacaoMembroChapa IN (:situacoes)');
        $query->setParameter('situacoes', $situacaoes, Connection::PARAM_INT_ARRAY);
    }

    /**
     * Retorna a quantidade de Pedidos de Substituiçao por chapa do Usuario Logado
     *
     * @param int $idMembroChapa
     * @return int
     */
    public function getPedidoPorMembroSubstituto(int $idMembroChapa)
    {
        try {
            $query = $this->createQueryBuilder('pedidoSubstituicaoChapa');
            $query->select('pedidoSubstituicaoChapa.id');
            $query->innerJoin('pedidoSubstituicaoChapa.membrosChapaSubstituicao', 'membrosChapaSubstituicao');
            $query->innerJoin('membrosChapaSubstituicao.membroChapaSubstituto', 'membroChapaSubstituto');

            $query->andWhere('membroChapaSubstituto.id = :idMembroChapa');
            $query->setParameter('idMembroChapa', $idMembroChapa);

            $situacoesDeferido =
                [Constants::STATUS_SUBSTITUICAO_CHAPA_DEFERIDO,
                Constants::STATUS_SUBSTITUICAO_CHAPA_RECURSO_DEFERIDO];
            $query->andWhere('pedidoSubstituicaoChapa.statusSubstituicaoChapa IN (:situacoes)');
            $query->setParameter('situacoes', $situacoesDeferido, Connection::PARAM_INT_ARRAY);

            return $query->getQuery()->getSingleScalarResult();
        } catch (Exception $e) {
            return null;
        }
    }
}
