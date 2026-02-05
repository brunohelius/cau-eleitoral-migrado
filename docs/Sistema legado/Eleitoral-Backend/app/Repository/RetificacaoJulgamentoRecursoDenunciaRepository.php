<?php
namespace App\Repository;

use App\Entities\RetificacaoJulgamentoRecursoDenuncia;
use Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Retificação Julgamento'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class RetificacaoJulgamentoRecursoDenunciaRepository extends AbstractRepository
{
    /**
     * Função que consulta dados os recurso Julgamentos retificados, pelo IdDenuncia
     * @param $idDenuncia
     * @return RetificacaoJulgamentoRecursoDenuncia[]|array
     */
    public function getRecRetificacaoJulgamento($idDenuncia)
    {
        try{
            $query = $this->createQueryBuilder('recursoJulgamentoRetificado');
            $query->innerJoin('recursoJulgamentoRetificado.julgamentoRetificado', 'julgamentoRetificado')
                  ->addSelect('julgamentoRetificado');
            $query->innerJoin('recursoJulgamentoRetificado.usuario', 'usuario')
                  ->addSelect('usuario');
            $query->leftJoin('recursoJulgamentoRetificado.recursoDenunciado', 'recursoDenunciado')
                  ->addSelect('recursoDenunciado');
            $query->leftJoin('recursoJulgamentoRetificado.recursoDenunciante', 'recursoDenunciante')
                  ->addSelect('recursoDenunciante');
            $query->leftJoin('recursoDenunciado.denuncia','denunciaDenunciado')
                  ->addSelect('denunciaDenunciado');
            $query->leftJoin('recursoDenunciante.denuncia','denunciaDenunciante')
                  ->addSelect('denunciaDenunciante');

            $query->where("julgamentoRetificado IS NOT NULL");

            $query->andWhere($query->expr()->orX(
                $query->expr()->eq('denunciaDenunciado.id',':idDenuncia'),
                $query->expr()->eq('denunciaDenunciante.id', ':idDenuncia')
            ));

            $query->setParameter('idDenuncia', $idDenuncia);

            $query->orderBy('julgamentoRetificado.data', 'DESC');

            $retificacoes = $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

            return array_map(static function(array $denuncia){
               return RetificacaoJulgamentoRecursoDenuncia::newInstance($denuncia);
            }, $retificacoes);

        }catch(NoResultException $e){
            return [];
        }
    }

    /**
     * Função que retorna um recurso para o visualizar do histórico 2 instancia
     * @param $idRetificacao
     * @return RetificacaoJulgamentoRecursoDenuncia|null
     * @throws NonUniqueResultException
     */
    public function getRetificacaoPorId($idRetificacao)
    {
        try {
            $query = $this->createQueryBuilder('retificacaoRecursoJulgamentoRetificado');

            $query->leftJoin('retificacaoRecursoJulgamentoRetificado.tipoJulgamentoDenunciado', 'tipoJulgamentoDenunciadoRecurso')
                ->addSelect('tipoJulgamentoDenunciadoRecurso');
            $query->leftJoin('retificacaoRecursoJulgamentoRetificado.tipoJulgamentoDenunciante', 'tipoJulgamentoDenuncianteRecursoDenunciado')
                ->addSelect('tipoJulgamentoDenuncianteRecursoDenunciado');
            $query->leftJoin('retificacaoRecursoJulgamentoRetificado.arquivosJulgamentoRecursoDenuncia', 'arquivosJulgamentoRecursoDenuncia')
                ->addSelect('arquivosJulgamentoRecursoDenuncia');
            $query->leftJoin('retificacaoRecursoJulgamentoRetificado.tipoSentencaJulgamento', 'tipoSentencaJulgamentoRecurso')
                ->addSelect('tipoSentencaJulgamentoRecurso');

            $query->where('retificacaoRecursoJulgamentoRetificado.id =:idRetificacao');
            $query->setParameter('idRetificacao', $idRetificacao);

            $recRetificado = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return RetificacaoJulgamentoRecursoDenuncia::newInstance($recRetificado);
        }catch (NoResultException $e){
            return null;
        }catch (NonUniqueFieldNameException $e){
            return null;
        }
    }
}
