<?php
/*
 * DenunciaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Config\Constants;
use App\Entities\Denuncia;
use App\Entities\DenunciaAdmitida;
use App\Entities\DenunciaSituacao;
use App\Entities\HistoricoDenuncia;
use App\Entities\JulgamentoRecursoDenuncia;
use App\To\DenunciasFiltroTO;
use App\Util\Utils;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;
use stdClass;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Denuncia'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DenunciaRepository extends AbstractRepository
{

    /**
     * Retorna a Denuncia de acordo com 'id' informado.
     *
     * @param integer $idDenuncia
     *
     * @return Denuncia|null
     * @throws \Exception
     */
    public function getDenunciaPorId($idDenuncia)
    {
        $subQueryDenunciaSituacao = $this->getSubQueryDenunciaSituacao();

        $query = $this->createQueryBuilder('denuncia');
        $query->leftJoin('denuncia.filial', 'filial')
            ->addSelect('filial');
        $query->leftJoin('denuncia.denunciaAdmitida', 'denunciaAdmitida')
            ->addSelect('denunciaAdmitida');
        $query->leftJoin('denuncia.atividadeSecundaria', 'atividadeSecundaria')
            ->addSelect('atividadeSecundaria');
        $query->leftJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal")
            ->addSelect("atividadePrincipal");
        $query->leftJoin("atividadePrincipal.calendario", "calendario")
              ->addSelect("calendario");
        $query->leftJoin("calendario.eleicao", "eleicao")
              ->addSelect("eleicao");
        $query->leftJoin('denuncia.denunciaInadmitida', 'denunciaInadmitida')
            ->addSelect('denunciaInadmitida');
        $query->leftJoin('denunciaInadmitida.arquivoDenunciaInadmitida', 'arquivoDenunciaInadmitida')
            ->addSelect('arquivoDenunciaInadmitida');

        $query->leftJoin('denunciaInadmitida.coordenador', 'coordenadorInadmitido')
            ->addSelect('coordenadorInadmitido');
        $query->leftJoin('coordenadorInadmitido.profissionalEntity', 'profissionalCoordenadorInadmitido')
            ->addSelect('profissionalCoordenadorInadmitido');

        $query->leftJoin('denunciaAdmitida.membroComissao', 'membroComissaoAdmissao')
            ->addSelect('membroComissaoAdmissao');
        $query->leftJoin('membroComissaoAdmissao.profissionalEntity', 'profissionalAdmissao')
            ->addSelect('profissionalAdmissao');
        $query->leftJoin('denunciaAdmitida.coordenador', 'coordenador')
            ->addSelect('coordenador');
        $query->leftJoin('coordenador.profissionalEntity', 'profissionalCoordenadorAdmissao')
            ->addSelect('profissionalCoordenadorAdmissao');
        $query->leftJoin('denuncia.julgamentoDenuncia', 'julgamentoDenuncia')
              ->addSelect('julgamentoDenuncia');
        $query->leftJoin('julgamentoDenuncia.tipoJulgamento', 'tipoJulgamento')
              ->addSelect('tipoJulgamento');
        $query->leftJoin('julgamentoDenuncia.tipoSentencaJulgamento', 'tipoSentencaJulgamento')
              ->addSelect('tipoSentencaJulgamento');
        $query->leftJoin('julgamentoDenuncia.arquivosJulgamentoDenuncia', 'arquivosJulgamentoDenuncia')
              ->addSelect('arquivosJulgamentoDenuncia');
        $query->leftJoin('denuncia.encaminhamentoDenuncia', 'encaminhamentoDenuncia')
            ->addSelect('encaminhamentoDenuncia');
        $query->leftJoin('encaminhamentoDenuncia.tipoEncaminhamento', 'tipoEncaminhamento')
            ->addSelect('tipoEncaminhamento');
        $query->leftJoin('encaminhamentoDenuncia.tipoSituacaoEncaminhamento', 'tipoSituacaoEncaminhamento')
            ->addSelect('tipoSituacaoEncaminhamento');
        $query->leftJoin('encaminhamentoDenuncia.alegacaoFinal', 'alegacaoFinal')
            ->addSelect('alegacaoFinal');
        $query->leftJoin('denuncia.recursoDenuncia', 'recursoDenuncia')
              ->addSelect('recursoDenuncia');
        $query->leftJoin('recursoDenuncia.profissional', 'profissionalRecursoDenuncia')
              ->addSelect('profissionalRecursoDenuncia');
        $query->leftJoin('recursoDenuncia.contrarrazao', 'contrarrazaoRecurso')
              ->addSelect('contrarrazaoRecurso');
        $query->leftJoin('contrarrazaoRecurso.profissional', 'profissionalContrarrazaoRecurso')
              ->addSelect('profissionalContrarrazaoRecurso');
        $query->leftJoin('denuncia.denunciaDefesa', 'denunciaDefesa')
            ->addSelect('denunciaDefesa');
        $query->leftJoin('denunciaDefesa.arquivosDenunciaDefesa', 'arquivosDenunciaDefesa')
            ->addSelect('arquivosDenunciaDefesa');
        $query->leftJoin('denuncia.testemunhas', 'testemunhas')
              ->addSelect('testemunhas');
        $query->leftJoin('denuncia.arquivoDenuncia', 'arquivoDenuncia')
              ->addSelect('arquivoDenuncia');
        $query->leftJoin('denuncia.tipoDenuncia', 'tipoDenuncia')
              ->addSelect('tipoDenuncia');
        $query->leftJoin('denuncia.denunciaSituacao', 'denunciaSituacao')
              ->addSelect('denunciaSituacao');
        $query->leftJoin('denuncia.pessoa', 'pessoa')
              ->addSelect('pessoa');
        $query->leftJoin('pessoa.profissional', 'profissional')
              ->addSelect('profissional');
        $query->leftJoin('denunciaSituacao.situacao', 'situacao')
              ->addSelect('situacao');
        $query->leftJoin('denuncia.denunciaChapa', 'denunciaChapa')
              ->addSelect("denunciaChapa");
        $query->leftJoin("denunciaChapa.chapaEleicao", "chapaEleicao")
              ->addSelect("chapaEleicao");
        $query->leftJoin("chapaEleicao.membrosChapa", "membrosChapaEleicao")
              ->addSelect("membrosChapaEleicao");
        $query->leftJoin("membrosChapaEleicao.profissional", "profissionalChapaEleicao")
              ->addSelect("profissionalChapaEleicao");
        $query->leftJoin("membrosChapaEleicao.statusParticipacaoChapa", "statusParticipacaoChapa")
            ->addSelect("statusParticipacaoChapa");
        $query->leftJoin('denuncia.denunciaMembroChapa', 'denunciaMembroChapa')
              ->addSelect("denunciaMembroChapa");
        $query->leftJoin("denunciaMembroChapa.membroChapa", "membroChapa")
              ->addSelect("membroChapa");
        $query->leftJoin("membroChapa.chapaEleicao", "chapaEleicaoMembroChapa")
            ->addSelect("chapaEleicaoMembroChapa");
        $query->leftJoin("membroChapa.profissional", "profissionalMembroChapa")
              ->addSelect("profissionalMembroChapa");
        $query->leftJoin('denuncia.denunciaMembroComissao', 'denunciaMembroComissao')
              ->addSelect("denunciaMembroComissao");
        $query->leftJoin("denunciaMembroComissao.membroComissao", "membroComissao")
              ->addSelect("membroComissao");
        $query->leftJoin("membroComissao.profissionalEntity", "profissionalMembroComissao")
              ->addSelect("profissionalMembroComissao");
        $query->leftJoin('denuncia.denunciaOutros', 'denunciaOutros')
              ->addSelect('denunciaOutros');
        $query->leftJoin('denuncia.julgamentoAdmissibilidade', 'julgamentoAdmissibilidade')
              ->addSelect('julgamentoAdmissibilidade');
        $query->leftJoin('julgamentoAdmissibilidade.tipoJulgamento', 'tipoJulgamentoAdmissibilidade')
              ->addSelect('tipoJulgamentoAdmissibilidade');
        $query->leftJoin('julgamentoAdmissibilidade.arquivos', 'arquivosTipoJulgamentoAdmissibilidade')
              ->addSelect('arquivosTipoJulgamentoAdmissibilidade');
        $query->leftJoin("julgamentoAdmissibilidade.recursoJulgamento", "recursoJulgamento")
              ->addSelect('recursoJulgamento');
        $query->leftJoin("recursoJulgamento.arquivos", "arquivosRecursoJulgamentoAdmissibilidade")
            ->addSelect('arquivosRecursoJulgamentoAdmissibilidade');
        $query->leftJoin("recursoJulgamento.julgamentoRecursoAdmissibilidade", "julgamentoRecursoAdmissibilidade")
            ->addSelect('julgamentoRecursoAdmissibilidade');
        $query->leftJoin("julgamentoRecursoAdmissibilidade.parecer", "parecerJulgamentoRecursoAdmissibilidade")
            ->addSelect('parecerJulgamentoRecursoAdmissibilidade');
        $query->leftJoin("julgamentoRecursoAdmissibilidade.arquivos", "arquivosJulgamentoRecursoAdmissibilidade")
            ->addSelect('arquivosJulgamentoRecursoAdmissibilidade');

        $query->where("denunciaSituacao.id = ($subQueryDenunciaSituacao)");

        $query->andWhere('denuncia.id = :idDenuncia');
        $query->setParameter(':idDenuncia', $idDenuncia);

        $query->orderBy('denuncia.id');
        $query->addOrderBy('arquivoDenunciaInadmitida.nome');
        $query->addOrderBy('arquivosJulgamentoRecursoAdmissibilidade.nome');
        $query->addOrderBy('arquivosRecursoJulgamentoAdmissibilidade.nome');
        $query->addOrderBy('arquivosJulgamentoDenuncia.nome');

        $denuncia = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
        return $denuncia ? Denuncia::newInstance($denuncia) : null;
    }

    /**
     * Retorna a Denuncia de acordo com 'id' informado.
     *
     * @param $dataLimiteRemovida
     *
     * @return Denuncia|null
     */
    public function getDenunciaAdmitidaPorDataAdmissao($dataLimiteRemovida)
    {
        $subQueryDenunciaSituacao = $this->getSubQueryDenunciaSituacao();

        $query = $this->createQueryBuilder('denuncia');
        $query->leftJoin('denuncia.filial', 'filial')
            ->addSelect('filial');
        $query->innerJoin('denuncia.denunciaAdmitida', 'denunciaAdmitida')
            ->addSelect('denunciaAdmitida');
        $query->leftJoin('denuncia.atividadeSecundaria', 'atividadeSecundaria')
            ->addSelect('atividadeSecundaria');
        $query->leftJoin('denuncia.denunciaInadmitida', 'denunciaInadmitida')
            ->addSelect('denunciaInadmitida');
        $query->leftJoin('denunciaInadmitida.arquivoDenunciaInadmitida', 'arquivoDenunciaInadmitida')
            ->addSelect('arquivoDenunciaInadmitida');
        $query->leftJoin('denunciaAdmitida.membroComissao', 'membroComissaoAdmissao')
            ->addSelect('membroComissaoAdmissao');
        $query->leftJoin('membroComissaoAdmissao.profissionalEntity', 'profissionalAdmissao')
            ->addSelect('profissionalAdmissao');
        $query->leftJoin('denuncia.encaminhamentoDenuncia', 'encaminhamentoDenuncia')
            ->addSelect('encaminhamentoDenuncia');
        $query->leftJoin('denuncia.denunciaDefesa', 'denunciaDefesa')
            ->addSelect('denunciaDefesa');
        $query->leftJoin('denuncia.testemunhas', 'testemunhas')
            ->addSelect('testemunhas');
        $query->leftJoin('denuncia.arquivoDenuncia', 'arquivoDenuncia')
            ->addSelect('arquivoDenuncia');
        $query->leftJoin('denuncia.tipoDenuncia', 'tipoDenuncia')
            ->addSelect('tipoDenuncia');
        $query->leftJoin('denuncia.denunciaSituacao', 'denunciaSituacao')
            ->addSelect('denunciaSituacao');
        $query->leftJoin('denuncia.pessoa', 'pessoa')
            ->addSelect('pessoa');
        $query->leftJoin('pessoa.profissional', 'profissional')
            ->addSelect('profissional');
        $query->leftJoin('denunciaSituacao.situacao', 'situacao')
            ->addSelect('situacao');
        $query->leftJoin('denuncia.denunciaChapa', 'denunciaChapa')
            ->addSelect("denunciaChapa");
        $query->leftJoin("denunciaChapa.chapaEleicao", "chapaEleicao")
            ->addSelect("chapaEleicao");
        $query->leftJoin("chapaEleicao.membrosChapa", "membrosChapaEleicao")
            ->addSelect("membrosChapaEleicao");
        $query->leftJoin("membrosChapaEleicao.profissional", "profissionalChapaEleicao")
            ->addSelect("profissionalChapaEleicao");
        $query->leftJoin('denuncia.denunciaMembroChapa', 'denunciaMembroChapa')
            ->addSelect("denunciaMembroChapa");
        $query->leftJoin("denunciaMembroChapa.membroChapa", "membroChapa")
            ->addSelect("membroChapa");
        $query->leftJoin("membroChapa.chapaEleicao", "chapaEleicaoMembroChapa")
            ->addSelect("chapaEleicaoMembroChapa");
        $query->leftJoin("membroChapa.profissional", "profissionalMembroChapa")
            ->addSelect("profissionalMembroChapa");
        $query->leftJoin('denuncia.denunciaMembroComissao', 'denunciaMembroComissao')
            ->addSelect("denunciaMembroComissao");
        $query->leftJoin("denunciaMembroComissao.membroComissao", "membroComissao")
            ->addSelect("membroComissao");
        $query->leftJoin("membroComissao.profissionalEntity", "profissionalMembroComissao")
            ->addSelect("profissionalMembroComissao");
        $query->leftJoin('denuncia.denunciaOutros', 'denunciaOutros')
            ->addSelect('denunciaOutros');

        $query->where("denunciaSituacao.id = ($subQueryDenunciaSituacao)");

        $query->andWhere('denunciaAdmitida.dataAdmissao >= :date_start')
        ->andWhere('denunciaAdmitida.dataAdmissao <= :date_end')
        ->setParameter('date_start', $dataLimiteRemovida->format('Y-m-d 00:00:00'))
        ->setParameter('date_end',   $dataLimiteRemovida->format('Y-m-d 23:59:59'));

        $query->andWhere('denunciaDefesa.id IS NULL');

        $query->orderBy('denuncia.id');

        return $query->getQuery()->getResult();
    }

    /**
     * Retorna lista de Denuncias de acordo com 'filtro' informado.
     *
     * @param DenunciasFiltroTO $denunciaFiltroTO
     *
     * @return Denuncia[]
     * @throws \Exception
     */
    public function getDenunciasCauUfPorFiltro(DenunciasFiltroTO $denunciaFiltroTO)
    {
        try {
            $subQueryDenunciaSituacao = $this->getSubQueryDenunciaSituacao();

            $query = $this->createQueryBuilder('denuncia');
            $query->innerJoin('denuncia.atividadeSecundaria', 'atividadeSecundaria');
            $query->innerJoin('atividadeSecundaria.atividadePrincipalCalendario', 'atividadePrincipalCalendario');
            $query->innerJoin('atividadePrincipalCalendario.calendario', 'calendario');
            $query->innerJoin('calendario.eleicao', 'eleicao');
            $query->leftJoin('denuncia.filial', 'filial')
                  ->addSelect('filial');
            $query->leftJoin('denuncia.pessoa', 'pessoa')
                  ->addSelect('pessoa');
            $query->leftJoin('denuncia.denunciaSituacao', 'denunciaSituacao')
                ->addSelect('denunciaSituacao');
            $query->leftJoin('denunciaSituacao.situacao', 'situacao')
                  ->addSelect('situacao');

            $query->where("denunciaSituacao.id = ($subQueryDenunciaSituacao)");

            $naoAdmitida = $denunciaFiltroTO->isNaoAdmitida();
            if ($naoAdmitida) {
                $query->andWhere('denunciaSituacao.situacao = :idSituacao');
                $query->setParameter(':idSituacao', Constants::STATUS_EM_ANALISE_ADMISSIBILIDADE);
            }

            $idsCauUf = $denunciaFiltroTO->getIdsCauUf();
            if (!empty($idsCauUf)) {
                if (in_array(Constants::IES_ID, $idsCauUf)) {
                    $query->andWhere($query->expr()->orX(
                        $query->expr()->in('filial.id',':idsCauUf'),
                        $query->expr()->isNull('denuncia.filial')
                    ));
                } else {
                    $query->andWhere('filial.id IN (:idsCauUf)');
                }

                $query->setParameter(':idsCauUf', $idsCauUf);
            }

            $idPessoa = $denunciaFiltroTO->getIdPessoa();
            if (null !== $idPessoa) {
                $query->andWhere('pessoa.id = :idPessoa');
                $query->setParameter(':idPessoa', $idPessoa);
            }

            $idDenuncia = $denunciaFiltroTO->getIdDenuncia();
            if (null !== $idDenuncia) {
                $query->andWhere('denuncia.id = :idDenuncia');
                $query->setParameter(':idDenuncia', $idDenuncia);
            }

            $idEleicao = $denunciaFiltroTO->getIdEleicao();
            if (null !== $idEleicao) {
                $query->andWhere('eleicao.id = :idEleicao');
                $query->setParameter(':idEleicao', $idEleicao);
            }

            $query->orderBy('denuncia.id');

            return $query->getQuery()->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna lista de Denuncias de acordo com 'filtro' informado.
     *
     * @param DenunciasFiltroTO $denunciaFiltroTO
     *
     * @return Denuncia[]
     * @throws \Exception
     */
    public function getDenunciasAdmissibilidadeCauUfPorFiltro(DenunciasFiltroTO $denunciaFiltroTO)
    {
        try {
            $subQueryDenunciaSituacao = $this->getSubQueryDenunciaSituacao();

            $query = $this->createQueryBuilder('denuncia');
            $query->leftJoin('denuncia.filial', 'filial')
                ->addSelect('filial');
            $query->innerJoin('denuncia.denunciaSituacao', 'denunciaSituacao')
                ->addSelect('denunciaSituacao');
            $query->innerJoin('denunciaSituacao.situacao', 'situacao')
                ->addSelect('situacao');

            $query->where("denunciaSituacao.id = ($subQueryDenunciaSituacao)");

            $naoAdmitida = $denunciaFiltroTO->isNaoAdmitida();
            if ($naoAdmitida) {
                $query->andWhere('denunciaSituacao.situacao = :idSituacao');
                $query->setParameter(':idSituacao', Constants::STATUS_EM_ANALISE_ADMISSIBILIDADE);
            }

            $idsCauUf = $denunciaFiltroTO->getIdsCauUf();
            if (!empty($idsCauUf)) {
                if (in_array(Constants::IES_ID, $idsCauUf)) {
                    $query->andWhere($query->expr()->orX(
                        $query->expr()->in('filial.id',':idsCauUf'),
                        $query->expr()->isNull('denuncia.filial')
                    ));
                } else {
                    $query->andWhere('filial.id IN (:idsCauUf)');
                }

                $query->setParameter(':idsCauUf', $idsCauUf);
            }

            $idDenuncia = $denunciaFiltroTO->getIdDenuncia();
            if (null !== $idDenuncia) {
                $query->andWhere('denuncia.id = :idDenuncia');
                $query->setParameter(':idDenuncia', $idDenuncia);
            }

            $query->orderBy('denuncia.id');

            return $query->getQuery()->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna lista de Denuncias de acordo com 'filtro' informado.
     *
     * @param DenunciasFiltroTO $denunciaFiltroTO
     *
     * @return Denuncia[]
     * @throws \Exception
     */
    public function getDenunciasDetalhamentoCauUfPorFiltro(DenunciasFiltroTO $denunciaFiltroTO)
    {
        try {
            $subQueryDenunciaSituacao = $this->getSubQueryDenunciaSituacao();

            $query = $this->createQueryBuilder('denuncia');
            $query->innerJoin('denuncia.atividadeSecundaria', 'atividadeSecundaria');
            $query->innerJoin('atividadeSecundaria.atividadePrincipalCalendario', 'atividadePrincipalCalendario');
            $query->innerJoin('atividadePrincipalCalendario.calendario', 'calendario');
            $query->innerJoin('calendario.eleicao', 'eleicao');
            $query->leftJoin('denuncia.filial', 'filial')
                ->addSelect('filial');
            $query->leftJoin('denuncia.tipoDenuncia', 'tipoDenuncia')
                ->addSelect('tipoDenuncia');
            $query->leftJoin('denuncia.denunciaSituacao', 'denunciaSituacao')
                ->addSelect('denunciaSituacao');
            $query->leftJoin('denuncia.pessoa', 'pessoa')
                ->addSelect('pessoa');
            $query->leftJoin('pessoa.profissional', 'profissional')
                ->addSelect('profissional');
            $query->leftJoin('denunciaSituacao.situacao', 'situacao')
                ->addSelect('situacao');
            $query->leftJoin('denuncia.denunciaChapa', 'denunciaChapa')
                ->addSelect("denunciaChapa");
            $query->leftJoin("denunciaChapa.chapaEleicao", "chapaEleicao")
                ->addSelect("chapaEleicao");
            $query->leftJoin('denuncia.denunciaMembroChapa', 'denunciaMembroChapa')
                ->addSelect("denunciaMembroChapa");
            $query->leftJoin("denunciaMembroChapa.membroChapa", "membroChapa")
                ->addSelect("membroChapa");
            $query->leftJoin('membroChapa.profissional', 'profissionalMembroChapa')
                ->addSelect("profissionalMembroChapa");
            $query->leftJoin('denuncia.denunciaMembroComissao', 'denunciaMembroComissao')
                ->addSelect("denunciaMembroComissao");
            $query->leftJoin("denunciaMembroComissao.membroComissao", "membroComissao")
                ->addSelect("membroComissao");
            $query->leftJoin("membroComissao.profissionalEntity", "profissionalMembroComissao")
                ->addSelect("profissionalMembroComissao");
            $query->leftJoin('denuncia.denunciaOutros', 'denunciaOutros')
                ->addSelect('denunciaOutros');

            $query->where("denunciaSituacao.id = ($subQueryDenunciaSituacao)");

            $idsCauUf = $denunciaFiltroTO->getIdsCauUf();
            if (!empty($idsCauUf)) {
                if (in_array(Constants::IES_ID, $idsCauUf)) {
                    $query->andWhere($query->expr()->orX(
                        $query->expr()->in('filial.id',':idsCauUf'),
                        $query->expr()->isNull('denuncia.filial')
                    ));
                } else {
                    $query->andWhere('filial.id IN (:idsCauUf)');
                }

                $query->setParameter(':idsCauUf', $idsCauUf);
            }

            $idPessoa = $denunciaFiltroTO->getIdPessoa();
            if (null !== $idPessoa) {
                $query->andWhere('pessoa.id = :idPessoa');
                $query->setParameter(':idPessoa', $idPessoa);
            }

            $idDenuncia = $denunciaFiltroTO->getIdDenuncia();
            if (null !== $idDenuncia) {
                $query->andWhere('denuncia.id = :idDenuncia');
                $query->setParameter(':idDenuncia', $idDenuncia);
            }

            $idEleicao = $denunciaFiltroTO->getIdEleicao();
            if (null !== $idEleicao) {
                $query->andWhere('eleicao.id = :idEleicao');
                $query->setParameter(':idEleicao', $idEleicao);
            }

            $query->orderBy('denuncia.id');

            $denuncias = $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
            return array_map(static function(array $denuncia) {
                return Denuncia::newInstance($denuncia);
            }, $denuncias);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Cria o Agrupamento de Denuncias UF por Pessoa UF.
     *
     * @param $idPessoa
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAgrupadaDenunciaPorPessoaUF($idPessoa)
    {
        $query = $this->createQueryBuilder('denuncia')
            ->select('COUNT( DISTINCT denuncia.id) as qtd_pedido',
                'case when cau_uf.id IS null then 1000 else cau_uf.id end as id_cau_uf',
                "case when cau_uf.prefixo IS null then 'IES' else cau_uf.prefixo end as prefixo",
                "case when cau_uf.descricao IS null then 'IES' else cau_uf.descricao end as descricao")
            ->leftJoin('denuncia.denunciaOutros', 'denunciaOutros')
            ->leftJoin('denuncia.denunciaChapa', 'denunciaChapa')
            ->leftJoin('denuncia.denunciaMembroChapa', 'denunciaMembroChapa')
            ->leftJoin('denuncia.denunciaMembroComissao', 'denunciaMembroComissao')
            ->innerJoin('denuncia.tipoDenuncia', 'tipoDenuncia')
            ->innerJoin('denuncia.atividadeSecundaria', 'atividadeSecundaria')
            ->innerJoin('denuncia.pessoa', 'pessoa')
            ->leftJoin('denuncia.filial', 'cau_uf')
            ->where('pessoa.id = :idPessoa')
            ->setParameter(':idPessoa', $idPessoa)
            ->groupBy('id_cau_uf','prefixo','descricao');

        return $query->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);
    }

    function getListaDenunciaPessoaUF($idPessoa, $idUF)
    {
        $sqlDenunciaMembroChapa = "SELECT td.id_denuncia,
                                   td.dt_denuncia,
                                   denunciante.nome as nome_denunciante,
                                   denunciado.nome as nome_denuncido,
                                   tsd.ds_situacao,
                                   tsd.id_situacao_denuncia,
                                   td.id_tipo_denuncia
                            FROM   eleitoral.tb_denuncia td
                                       INNER JOIN PUBLIC.tb_pessoa tp
                                                  ON td.id_pessoa = tp.id
                                       INNER JOIN PUBLIC.tb_profissional denunciante
                                                  ON denunciante.pessoa_id = tp.id
                                       INNER JOIN eleitoral.tb_denuncia_membro_chapa tdmc
                                                  ON td.id_denuncia = tdmc.id_denuncia
                                       INNER JOIN (SELECT Max(tds.id_denuncia_situacao),
                                                          tds.id_denuncia,
                                                          tds.id_situacao_denuncia
                                                   FROM   eleitoral.tb_denuncia_situacao tds
                                                    INNER JOIN eleitoral.tb_denuncia etd ON tds.id_denuncia = etd.id_denuncia
                                                   WHERE etd.id_pessoa =  $idPessoa
                                                   GROUP BY tds.id_denuncia,
                                                       tds.id_situacao_denuncia) sit
                                                  ON sit.id_denuncia = td.id_denuncia
                                       INNER JOIN eleitoral.tb_situacao_denuncia tsd
                                                  ON tsd.id_situacao_denuncia = sit.id_situacao_denuncia
                                       INNER JOIN eleitoral.tb_membro_chapa tmc
                                                  ON tdmc.id_membro_chapa = tmc.id_membro_chapa
                                       INNER JOIN eleitoral.tb_chapa_eleicao tce 
                                                  ON tce.id_chapa_eleicao = tmc.id_chapa_eleicao
                                       INNER JOIN public.tb_profissional denunciado
                                                  ON denunciado.id = tmc.id_profissional
                            WHERE  td.id_pessoa = $idPessoa AND  tce.id_cau_uf = $idUF";

        $sqlDenunciaMembroComissao = " UNION  SELECT td.id_denuncia,
                                   td.dt_denuncia,
                                   denunciante.nome as nome_denunciante,
                                   denunciado.nome as nome_denunciante,
                                   tsd.ds_situacao,
                                   tsd.id_situacao_denuncia,
                                   td.id_tipo_denuncia                                   
                            FROM   eleitoral.tb_denuncia td
                                       INNER JOIN PUBLIC.tb_pessoa tp
                                                  ON td.id_pessoa = tp.id
                                       INNER JOIN PUBLIC.tb_profissional denunciante
                                                  ON denunciante.pessoa_id = tp.id
                                       INNER JOIN eleitoral.tb_denuncia_membro_comissao tdmc
                                                  ON td.id_denuncia = tdmc.id_denuncia
                                       INNER JOIN (SELECT Max(tds.id_denuncia_situacao),
                                                          tds.id_denuncia,
                                                          tds.id_situacao_denuncia
                                                   FROM   eleitoral.tb_denuncia_situacao tds
                                                              INNER JOIN eleitoral.tb_denuncia etd ON tds.id_denuncia = etd.id_denuncia
                                                   WHERE etd.id_pessoa =  $idPessoa
                                                   GROUP BY tds.id_denuncia,
                                                       tds.id_situacao_denuncia) sit
                                                  ON sit.id_denuncia = td.id_denuncia
                                       INNER JOIN eleitoral.tb_situacao_denuncia tsd
                                                  ON tsd.id_situacao_denuncia = sit.id_situacao_denuncia
                                       INNER JOIN eleitoral.tb_membro_comissao tmc
                                                  ON tdmc.id_membro_comissao = tmc.id_membro_comissao
                                       INNER JOIN public.tb_pessoa pe_denunciado ON tmc.id_pessoa = pe_denunciado.id
                                       INNER JOIN public.tb_profissional denunciado
                                                  ON denunciado.pessoa_id = pe_denunciado.id
                            WHERE  td.id_pessoa = $idPessoa AND  tmc.id_cau_uf = $idUF";

        $sqlChapa = " UNION SELECT td.id_denuncia,
                               td.dt_denuncia,
                               denunciante.nome,
                               tdc.id_chapa_eleicao::text,
                               tsd.ds_situacao,
                               tsd.id_situacao_denuncia,
                               td.id_tipo_denuncia
                        FROM   eleitoral.tb_denuncia td
                                   INNER JOIN PUBLIC.tb_pessoa tp
                                              ON td.id_pessoa = tp.id
                                   INNER JOIN PUBLIC.tb_profissional denunciante
                                              ON denunciante.pessoa_id = tp.id
                                   INNER JOIN eleitoral.tb_denuncia_chapa tdc
                                              ON td.id_denuncia = tdc.id_denuncia
                                   INNER JOIN eleitoral.tb_chapa_eleicao tce ON tce.id_chapa_eleicao = tdc.id_chapa_eleicao
                                   INNER JOIN (SELECT Max(tds.id_denuncia_situacao),
                                                      tds.id_denuncia,
                                                      tds.id_situacao_denuncia
                                               FROM   eleitoral.tb_denuncia_situacao tds
                                                          INNER JOIN eleitoral.tb_denuncia etd ON tds.id_denuncia = etd.id_denuncia
                                               WHERE etd.id_pessoa =  $idPessoa
                                               GROUP BY tds.id_denuncia,
                                                   tds.id_situacao_denuncia) sit
                                              ON sit.id_denuncia = td.id_denuncia
                                   INNER JOIN eleitoral.tb_situacao_denuncia tsd
                                              ON tsd.id_situacao_denuncia = sit.id_situacao_denuncia
                        WHERE  td.id_pessoa = $idPessoa AND  tce.id_cau_uf = $idUF
                        ORDER  BY id_denuncia";

        $sql = $sqlDenunciaMembroChapa . $sqlDenunciaMembroComissao  . $sqlChapa;
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Retorna as denuncias dado um determinado Filtro
     *
     * @param stdClass $filtroTO
     * @return array|null
     */
    public function getDenunciasPorFiltro($filtroTO)
    {
        try {
            $query = $this->createQueryBuilder("denuncia");

            $query->innerJoin("denuncia.atividadeSecundaria", "atividadeSecundaria")->addSelect("atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal")->addSelect("atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario")->addSelect("calendario");
            $query->innerJoin("denuncia.tipoDenuncia", "tipoDenuncia")->addSelect("tipoDenuncia");
            $query->innerJoin("denuncia.pessoa", "pessoa")->addSelect("pessoa");
            $query->leftJoin("denuncia.filial", "filial")->addSelect("filial");
            $query->leftJoin("denuncia.testemunhas", "testemunhas")->addSelect("testemunhas");

            $query->leftJoin("denuncia.denunciaOutros", "denunciaOutros")->addSelect("denunciaOutros");

            $query->leftJoin("denuncia.denunciaChapa", "denunciaChapa")->addSelect("denunciaChapa");
            $query->leftJoin("denuncia.denunciaMembroChapa", "denunciaMembroChapa")->addSelect("denunciaMembroChapa");
            $query->leftJoin("denunciaMembroChapa.membroChapa", "membroChapaDenuncia")->addSelect("membroChapaDenuncia");
            $query->leftJoin("membroChapaDenuncia.profissional", "profissionalMembroChapa")->addSelect("profissionalMembroChapa");

            $query->leftJoin("denuncia.denunciaMembroComissao", "denunciaMembroComissao")->addSelect("denunciaMembroComissao");
            $query->leftJoin("denunciaMembroComissao.membroComissao", "membroComissaoDenuncia")->addSelect("membroComissaoDenuncia");
            $query->leftJoin("membroComissaoDenuncia.profissionalEntity", "profissionalMembroComissao");

            $query->leftJoin("denunciaChapa.chapaEleicao", "chapaEleicao")->addSelect("chapaEleicao");
            $query->leftJoin("denunciaMembroChapa.membroChapa", "membroChapa")->addSelect("membroChapa");
            $query->leftJoin("membroChapa.chapaEleicao", "chapaEleicao2")->addSelect("chapaEleicao2");
            $query->leftJoin("denunciaMembroComissao.membroComissao", "membroComissao")->addSelect("membroComissao");

            $query->leftJoin("denuncia.denunciaAdmitida","denunciaAdmitida")->addSelect("denunciaAdmitida");
            $query->leftJoin("denunciaAdmitida.membroComissao","membroComissao2")->addSelect("membroComissao2");
            $query->leftJoin("denuncia.denunciaDefesa", "denunciaDefesa")->addSelect('denunciaDefesa');

            $query->leftJoin('denuncia.denunciaSituacao', 'denunciaSituacao')->addSelect('denunciaSituacao');
            $query->leftJoin('denunciaSituacao.situacao', 'situacao')->addSelect('situacao');


            $query->where('1 = 1');

            if (!empty($filtroTO->idPessoa)) {
                $query->andWhere('denuncia.pessoa = :idPessoa');
                $query->setParameter('idPessoa', $filtroTO->idPessoa);
            }
            if (!empty($filtroTO->idAtividadeSecundaria)) {
                $query->andWhere('atividadeSecundaria.id = :idAtvSec');
                $query->setParameter('idAtvSec', $filtroTO->idAtividadeSecundaria);

            }
            if (!empty($filtroTO->idDenuncia)) {
                $query->andWhere('denuncia.id = :idDenuncia');
                $query->setParameter('idDenuncia', $filtroTO->idDenuncia);
            }
            if (!empty($filtroTO->idSituacao)) {
                $subQueryDenunciaSituacao = $this->getSubQueryDenunciaSituacao();
                $query->andWhere("denunciaSituacao.id = ($subQueryDenunciaSituacao)");
                $query->andWhere('denunciaSituacao.situacao = :idSituacao');
                $query->setParameter(':idSituacao', $filtroTO->idSituacao);
            }

            $query->orderBy('denuncia.id', "DESC");

            $arrayDenuncia = $query->getQuery()->getArrayResult();

            return array_map(function ($dados) {
                return Denuncia::newInstance($dados);
            }, $arrayDenuncia);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna as denuncias para um determinado profissional
     *
     * @param int $idProfissional
     * @param bool $returnDenunciante
     * @param bool $isReturnRelatoria
     * @return array|null
     */
    public function getPorProfissional($idProfissional, $returnDenunciante, $isReturnRelatoria)
    {
        try {
            $dql = " SELECT denuncia.stSigilo is_sigiloso, denuncia.id id_denuncia, denuncia.numeroSequencial numero_denuncia, profissional.nome nome_denunciante, ";
            $dql .= " tipoDenuncia.id id_tipo_denuncia, historico.dataHistorico dt_denuncia, situacao.id id_situacao_denuncia, ";
            $dql .= " situacao.descricao ds_situacao, profissionalEntity.nome nome_denunciado_chapa, profissionalMembro.nome nome_denunciado_comissao, chapaEleicao.numeroChapa as nome_denunciado ";
            $dql .= " FROM App\Entities\Denuncia denuncia ";
            $dql .= " LEFT JOIN denuncia.denunciaMembroChapa denunciaMembroChapa ";
            $dql .= " LEFT JOIN denuncia.denunciaMembroComissao denunciaMembroComissao ";
            $dql .= " LEFT JOIN denuncia.denunciaChapa denunciaChapa ";
            $dql .= " LEFT JOIN denuncia.pessoa pessoa ";
            $dql .= " LEFT JOIN pessoa.profissional denunciante ";
            $dql .= " LEFT JOIN denuncia.tipoDenuncia tipoDenuncia ";
            $dql .= " LEFT JOIN denuncia.denunciaSituacao denunciaSituacao ";
            $dql .= " LEFT JOIN denuncia.historico historico ";
            $dql .= " LEFT JOIN denunciaSituacao.situacao situacao ";
            $dql .= " LEFT JOIN pessoa.profissional profissional ";

            $dql .= " LEFT JOIN denunciaChapa.chapaEleicao chapaEleicao ";
            $dql .= " LEFT JOIN chapaEleicao.membrosChapa membrosChapaTipoChapa ";
            $dql .= " LEFT JOIN membrosChapaTipoChapa.statusParticipacaoChapa statusParticipacaoChapa ";
            $dql .= " LEFT JOIN denunciaMembroChapa.membroChapa membroChapa ";
            $dql .= " LEFT JOIN denunciaMembroComissao.membroComissao membroComissao ";
            $dql .= " LEFT JOIN membroChapa.profissional profissionalEntity ";
            $dql .= " LEFT JOIN membroComissao.pessoaEntity pessoaEntity ";
            $dql .= " LEFT JOIN membroComissao.profissionalEntity profissionalMembro ";

            $dql .= " WHERE historico.id = (SELECT MAX(historicoDenuncia.id) FROM App\Entities\HistoricoDenuncia historicoDenuncia WHERE historicoDenuncia.denuncia = denuncia.id) ";
            $dql .= " AND denunciaSituacao.id = (SELECT MAX(denunciaSituacao2.id) FROM App\Entities\DenunciaSituacao denunciaSituacao2 WHERE denunciaSituacao2.denuncia = denuncia.id) ";

            if($isReturnRelatoria){
                $dql .= " AND (SELECT tbsd.id FROM App\Entities\DenunciaSituacao tds ";
                $dql .= " INNER JOIN tds.denuncia tbd ";
                $dql .= " INNER JOIN tds.situacao tbsd ";
                $dql .= " WHERE tbd.id = denuncia.id AND tds.id = ";
                $dql .= " (select max(tdsSub.id) FROM App\Entities\DenunciaSituacao tdsSub ";
                $dql .= " INNER JOIN tdsSub.denuncia tbdSub ";
                $dql .= " WHERE tbdSub.id = tbd.id) GROUP BY tbd.id, tbsd.id) = :idSituacaoAdmitida ";

                $parametros['idSituacaoAdmitida'] = Constants::SITUACAO_DENUNCIA_EM_RELATORIA;
            }

            $dql .= " AND denunciaSituacao.situacao IN (:idStatus)";

            $parametros['idStatus'] = [
                Constants::STATUS_DENUNCIA_ADMITIDA,
                Constants::STATUS_DENUNCIA_EM_RELATORIA,
                Constants::STATUS_DENUNCIA_JULG_PRIMEIRA_INSTANCIA,
                Constants::STATUS_DENUNCIA_EM_JULGAMENTO_PRIMEIRA_INSTANCIA,
                Constants::STATUS_DENUNCIA_AGUARDANDO_CONTRARRAZAO,
                Constants::STATUS_DENUNCIA_TRANSITADO_EM_JULGADO,
                Constants::STATUS_DENUNCIA_JULG_SEGUNDA_INSTANCIA,
                Constants::STATUS_DENUNCIA_AGUARDANDO_DEFESA
            ];

            if (!empty($idProfissional)) {

                if($returnDenunciante){
                    $parametros['idStatus'][] = Constants::STATUS_PENDENTE_ANALISE;
                    $parametros['idStatus'][] = Constants::STATUS_DENUNCIA_EM_JULGAMENTO;
                    $parametros['idStatus'][] = Constants::SITUACAO_DENUNCIA_AGUARDANDO_RELATOR;
                    $parametros['idStatus'][] = Constants::SITUACAO_DENUNCIA_EM_RECURSO;
                    $parametros['idStatus'][] = Constants::SITUACAO_JULGAMENTEO_RECURSO_ADMISSIBILIDADE;
                    $dql .= " AND ((membrosChapaTipoChapa.profissional = :idProfissional AND membrosChapaTipoChapa.situacaoResponsavel = true) OR profissionalEntity.id = :idProfissional OR membroComissao.pessoa = :idProfissional OR denunciante.id = :idProfissional) ";
                } else {
                    $dql .= " AND denuncia.status = :status";
                    $parametros['status'] = Constants::STATUS_DENUNCIA_ADMITIDA;
                    $dql .= " AND ((membrosChapaTipoChapa.profissional = :idProfissional AND membrosChapaTipoChapa.situacaoResponsavel = true) OR profissionalEntity.id = :idProfissional OR membroComissao.pessoa = :idProfissional) ";
                }

                $parametros['idProfissional'] = $idProfissional;
            }

            $dql .= " ORDER BY denuncia.id ";

            $query = $this->getEntityManager()->createQuery($dql);
            $query->setParameters($parametros);

            return $query->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Cria o Agrupamento de Denuncias UF por Atividade Secundaria.
     *
     * @param $idAtividadeSecundaria
     * @param $idCauUfUsuario
     * @param $isAcessorCEN
     * @return mixed
     */
    public function getAgrupamentoDenunciaUfPorAtividadeSecundaria(
        $idAtividadeSecundaria,
        $idCauUfUsuario,
        $isAcessorCEN
    ) {

        $query = $this->createQueryBuilder('denuncia')
            ->select('COUNT( DISTINCT denuncia.id) as qtd_pedido',
                'case when cau_uf.id IS null then 1000 else cau_uf.id end as id_cau_uf',
                "case when cau_uf.prefixo IS null then 'IES' else cau_uf.prefixo end as prefixo",
                "case when cau_uf.descricao IS null then 'IES' else cau_uf.descricao end as descricao")
            ->leftJoin('denuncia.denunciaOutros', 'denunciaOutros')
            ->leftJoin('denuncia.denunciaChapa', 'denunciaChapa')
            ->leftJoin('denuncia.denunciaMembroChapa', 'denunciaMembroChapa')
            ->leftJoin('denuncia.denunciaMembroComissao', 'denunciaMembroComissao')
            ->innerJoin('denuncia.tipoDenuncia', 'tipoDenuncia')
            ->innerJoin('denuncia.atividadeSecundaria', 'atividadeSecundaria')
            ->innerJoin('denuncia.pessoa', 'pessoa')
            ->leftJoin('denuncia.filial', 'cau_uf')
            ->where('atividadeSecundaria.id = :atividadeSec')
            ->setParameter(':atividadeSec', $idAtividadeSecundaria)
            ->groupBy('id_cau_uf','prefixo','descricao');

        if(!$isAcessorCEN) {
            $query->andWhere('cau_uf.id IN (:idCauUf) ');
            $query->setParameter('idCauUf', $idCauUfUsuario, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY );

            if (in_array(Constants::IES_ID, $idCauUfUsuario)) {
                $query->orWhere('denuncia.filial IS NULL');
            }
        }

        return $query->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);
    }

    /**
     * Cria o Agrupamento de Denuncias em relatoria de acordo com o profissional.
     *
     * @param $idProfissional
     * @return mixed
     */
    public function getDenunciasRelatoriaPorProfissional($idProfissional)
    {
        try {
            $subQueryDenunciaSituacao = $this->getSubQueryDenunciaSituacao();

            $query = $this->createQueryBuilder('denuncia');
            $query->innerJoin('denuncia.denunciaAdmitida', 'denunciaAdmitida');
            $query->innerJoin('denunciaAdmitida.membroComissao', 'membroComissao');

            $query->leftJoin('denuncia.denunciaSituacao', 'denunciaSituacao');
            $query->leftJoin('denunciaSituacao.situacao', 'situacao');

            $query->where("denunciaSituacao.id = ($subQueryDenunciaSituacao)");

            $query->andWhere('denunciaSituacao.situacao = :idDenunciaSituacao');
            $query->setParameter(':idDenunciaSituacao', Constants::STATUS_DENUNCIA_EM_RELATORIA);

            $query->andWhere('membroComissao.profissionalEntity = :idProfissional');
            $query->setParameter(':idProfissional', $idProfissional);

            $query->orderBy('denuncia.id');

            return $query->getQuery()->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    /**
     * Retorna as Denuncias que possuem encaminhamento de acordo com um tipo de encaminhamento.
     *
     * @param integer $idTipoEncaminhamento
     * @param integer $idEleicao
     *
     * @return array|null
     * @throws \Exception
     */
    public function getDenunciaEmRelatoriaPorTipoEncaminhamento($idTipoEncaminhamento, $idEleicao)
    {
        $subQueryDenunciaSituacao = $this->getSubQueryDenunciaSituacao();

        $query = $this->createQueryBuilder('denuncia');
        $query->innerJoin('denuncia.encaminhamentoDenuncia', 'encaminhamentoDenuncia')
            ->addSelect('encaminhamentoDenuncia');
        $query->innerJoin('encaminhamentoDenuncia.tipoEncaminhamento', 'tipoEncaminhamento')
            ->addSelect('tipoEncaminhamento');
        $query->leftJoin('encaminhamentoDenuncia.tipoSituacaoEncaminhamento', 'tipoSituacaoEncaminhamento')
            ->addSelect('tipoSituacaoEncaminhamento');
        $query->leftJoin('encaminhamentoDenuncia.alegacaoFinal', 'alegacaoFinal')
            ->addSelect('alegacaoFinal');

        $query->innerJoin('denuncia.atividadeSecundaria', 'atividadeSecundaria');
        $query->innerJoin('atividadeSecundaria.atividadePrincipalCalendario', 'atividadePrincipalCalendario');
        $query->innerJoin('atividadePrincipalCalendario.calendario', 'calendario');
        $query->innerJoin('calendario.eleicao', 'eleicao');

        $query->leftJoin('denuncia.tipoDenuncia', 'tipoDenuncia')
            ->addSelect('tipoDenuncia');
        $query->leftJoin('denuncia.denunciaSituacao', 'denunciaSituacao')
            ->addSelect('denunciaSituacao');
        $query->leftJoin('denunciaSituacao.situacao', 'situacao')
            ->addSelect('situacao');


        $query->where("denunciaSituacao.id = ($subQueryDenunciaSituacao)");

        $query->andWhere('denunciaSituacao.situacao = :idSituacao');
        $query->setParameter(':idSituacao', Constants::SITUACAO_DENUNCIA_EM_RELATORIA);

        $query->andWhere('tipoEncaminhamento.id = :idTipo');
        $query->setParameter(':idTipo', $idTipoEncaminhamento);

        $query->andWhere('eleicao.id = :idEleicao');
        $query->setParameter(':idEleicao', $idEleicao);

        $query->orderBy('denuncia.id');

        $denuncias = $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        return array_map(function ($dados) {
            return Denuncia::newInstance($dados);
        }, $denuncias);
    }

    /**
     * Retorna as Denúncias para rotina de recurso e contrarrazão de acordo com o filtro
     *
     * @param stdClass $filtroTO
     *
     * @return array|null
     * @throws \Exception
     */
    public function getDenunciaEmJulgamentoParaRotinaRecursoContrarrazao($filtroTO)
    {
        $subQueryDenunciaSituacao = $this->getSubQueryDenunciaSituacao();

        $query = $this->createQueryBuilder('denuncia');
        $query->innerJoin('denuncia.julgamentoDenuncia', 'julgamentoDenuncia')
              ->addSelect('julgamentoDenuncia');
        $query->innerJoin('denuncia.atividadeSecundaria', 'atividadeSecundaria');
        $query->innerJoin('atividadeSecundaria.atividadePrincipalCalendario', 'atividadePrincipalCalendario');
        $query->innerJoin('atividadePrincipalCalendario.calendario', 'calendario');
        $query->innerJoin('calendario.eleicao', 'eleicao');
        $query->leftJoin('denuncia.tipoDenuncia', 'tipoDenuncia')
              ->addSelect('tipoDenuncia');
        $query->leftJoin('denuncia.denunciaSituacao', 'denunciaSituacao')
              ->addSelect('denunciaSituacao');
        $query->leftJoin('denunciaSituacao.situacao', 'situacao')
              ->addSelect('situacao');
        $query->leftJoin('denuncia.recursoDenuncia', 'recursoDenuncia')
              ->addSelect('recursoDenuncia');
        $query->leftJoin('recursoDenuncia.contrarrazao', 'contrarrazao')
              ->addSelect('contrarrazao');

        $query->where("denunciaSituacao.id = ($subQueryDenunciaSituacao)");

        if (!empty($filtroTO->idEleicao)) {
            $query->andWhere('eleicao.id = :idEleicao');
            $query->setParameter(':idEleicao', $filtroTO->idEleicao);
        }

        if (!empty($filtroTO->idSituacao)) {
            if(is_array($filtroTO->idSituacao)) {
                $query->andWhere('denunciaSituacao.situacao in (:idSituacao)');
            } else {
                $query->andWhere('denunciaSituacao.situacao = :idSituacao');
            }
            $query->setParameter(':idSituacao', $filtroTO->idSituacao);
        }

        if (!empty($filtroTO->hasContrarrazao)) {
            $filtroTO->hasContrarrazao
                ? $query->andWhere('recursoDenuncia.contrarrazao IS NOT NULL')
                : $query->andWhere('recursoDenuncia.contrarrazao IS NULL');
        }

        $query->orderBy('denuncia.id');

        $denuncias = $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        return array_map(static function ($dados) {
            return Denuncia::newInstance($dados);
        }, $denuncias);
    }

    /**
     * Retorna as Denúncias para rotina de recurso e contrarrazão de acordo com o filtro
     *
     * @param stdClass $filtroTO
     *
     * @return array|null
     * @throws \Exception
     */
    public function getDenunciaAguardandoDefesaParaRotinaPrazoDefesaEncerrado($filtroTO)
    {
        $subQueryDenunciaSituacao = $this->getSubQueryDenunciaSituacao();

        $query = $this->createQueryBuilder('denuncia');
        $query->innerJoin('denuncia.atividadeSecundaria', 'atividadeSecundaria');
        $query->innerJoin('atividadeSecundaria.atividadePrincipalCalendario', 'atividadePrincipalCalendario');
        $query->innerJoin('atividadePrincipalCalendario.calendario', 'calendario');
        $query->innerJoin('calendario.eleicao', 'eleicao');
        $query->leftJoin('denuncia.tipoDenuncia', 'tipoDenuncia')
              ->addSelect('tipoDenuncia');
        $query->leftJoin('denuncia.denunciaAdmitida', 'denunciaAdmitida')
              ->addSelect('denunciaAdmitida');
        $query->leftJoin('denuncia.denunciaDefesa', 'denunciaDefesa')
              ->addSelect('denunciaDefesa');
        $query->leftJoin('denuncia.denunciaSituacao', 'denunciaSituacao')
              ->addSelect('denunciaSituacao');
        $query->leftJoin('denunciaSituacao.situacao', 'situacao')
              ->addSelect('situacao');

        $query->where("denunciaSituacao.id = ($subQueryDenunciaSituacao)");

        if (!empty($filtroTO->idEleicao)) {
            $query->andWhere('eleicao.id = :idEleicao');
            $query->setParameter(':idEleicao', $filtroTO->idEleicao);
        }

        if (!empty($filtroTO->idSituacao)) {
            $query->andWhere('denunciaSituacao.situacao = :idSituacao');
            $query->setParameter(':idSituacao', $filtroTO->idSituacao);
        }

        $query->orderBy('denuncia.id');

        $denuncias = $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        return array_map(static function ($dados) {
            return Denuncia::newInstance($dados);
        }, $denuncias);
    }

    /**
     * Retorna a sub consulta de historico de denuncia.
     *
     * @return mixed
     */
    public function getSubQueryHistoricoDenuncia()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->from(HistoricoDenuncia::class, 'historicoDenunciaSub');
        $query->select('MAX(historicoDenunciaSub.id)');
        $query->where('historicoDenunciaSub.denuncia = denuncia.id');
        return $query;
    }

    /**
     * Retorna a sub consulta de situacao da denuncia.
     *
     * @return mixed
     */
    public function getSubQueryDenunciaSituacao()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->from(DenunciaSituacao::class, 'denunciaSituacaoSub');
        $query->select('MAX(denunciaSituacaoSub.id)');
        $query->where('denunciaSituacaoSub.denuncia = denuncia.id');
        return $query;
    }

    /**
     * @param $idSituacaoDenuncia
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSituacaoAtualDenuncia($idSituacaoDenuncia)
    {
        $query = $this->createQueryBuilder('denuncia');
        $query->select('denunciaSituacao.id','situacaoDenuncia.descricao');
        $query->innerJoin('denuncia.denunciaSituacao','denunciaSituacao');
        $query->innerJoin('denunciaSituacao.situacao','situacaoDenuncia');
        $query->where('denunciaSituacao.id = :idSituacaoDenuncia');
        $query->setParameter(':idSituacaoDenuncia', $idSituacaoDenuncia);
        return $query->getQuery()->getSingleResult();
    }

    /**
     * Retorna a sub consulta de situacao da denuncia.
     *
     * @param $idDenuncia
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMaxSituacaoDenuncia($idDenuncia)
    {
        $query = $this->createQueryBuilder('denuncia');
        $query->select('MAX(denunciaSituacao.id) as id');
        $query->innerJoin('denuncia.denunciaSituacao','denunciaSituacao');
        $query->where('denuncia.id = :idDenuncia');
        $query->setParameter(':idDenuncia', $idDenuncia);
        return $query->getQuery()->getSingleResult();
    }

    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult|Denuncia[][]
     */
    public function iteratorDenunciasAguardandoRelatores()
    {
        $data = Utils::subtrairDiasData(Utils::getData(), 1);

        $sq = $this->_em->createQueryBuilder()
            ->from(DenunciaSituacao::class, 'ds1')
            ->select('max(ds1.id)')
            ->andWhere('ds1.denuncia = d.id');

        return $this->createQueryBuilder('d')
            ->join('d.denunciaSituacao', 'ds')
            ->join('ds.situacao', 's')
            ->andWhere('s.id = :situacao')
            ->andWhere("ds.id = ({$sq})")
            ->andWhere("ds.data <= :data")
            ->setParameter('situacao', Constants::SITUACAO_DENUNCIA_AGUARDANDO_RELATOR)
            ->setParameter('data', $data)
            ->distinct()
            ->getQuery()
            ->iterate();
    }

    /**
     *
     * @param $idChapaEleicao
     */
    public function getPedidosDenunciaPorChapa($idChapaEleicao)
    {
        try {
            $subqueryStatusDenunciado = $this->getSubQuerySansaoJulgamentoRecurso("Sansao");
            $subQueryDenunciaSituacao = $this->getSubQueryDenunciaSituacao();

            $query = $this->createQueryBuilder('denuncia');
            $query->select([
                'denuncia.id',
                'denuncia.numeroSequencial numeroProtocolo',
                'tipoJulgamento.id idStatusJulgamento',
                'situacao.id idSituacaoAtual',
                "({$subqueryStatusDenunciado}) sancao",
                "tipoDenuncia.id idTipoDenuncia"
            ]);
            $query->innerJoin('denuncia.tipoDenuncia', 'tipoDenuncia');
            $query->leftJoin('denuncia.denunciaChapa', 'denunciaChapa');
            $query->leftJoin('denunciaChapa.chapaEleicao', 'chapaEleicao');
            $query->leftJoin('denuncia.denunciaMembroChapa', 'denunciaMembroChapa');
            $query->leftJoin('denunciaMembroChapa.membroChapa', 'membroChapa');
            $query->leftJoin('membroChapa.chapaEleicao', 'chapaEleicaoMembro');
            $query->leftJoin('denuncia.denunciaSituacao', 'denunciaSituacao');
            $query->leftJoin('denunciaSituacao.situacao', 'situacao');
            $query->leftJoin('denuncia.julgamentoDenuncia', 'julgamentoDenuncia');
            $query->leftJoin('julgamentoDenuncia.tipoJulgamento', 'tipoJulgamento');

            $query->where("denunciaSituacao.id = ($subQueryDenunciaSituacao)");
            $query->andWhere('chapaEleicao.id = :idChapa OR chapaEleicaoMembro.id = :idChapa');
            $query->setParameter('idChapa', $idChapaEleicao);

            return $query->getQuery()->getArrayResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $select
     * @param $alias
     */
    public function getSubQuerySansaoJulgamentoRecurso($alias)
    {
        $subQuery2 = $this->getEntityManager()->createQueryBuilder();
        $subQuery2->select("MAX(julgamentoRecursoDenuncia{$alias}2.id)");
        $subQuery2->from(JulgamentoRecursoDenuncia::class, "julgamentoRecursoDenuncia{$alias}2");
        $subQuery2->leftJoin("julgamentoRecursoDenuncia{$alias}2.recursoDenunciado", "recursoDenunciado{$alias}");
        $subQuery2->leftJoin("julgamentoRecursoDenuncia{$alias}2.recursoDenunciante", "recursoDenunciante{$alias}");
        $subQuery2->where("recursoDenunciado{$alias}.denuncia = denuncia.id OR recursoDenunciante{$alias}.denuncia = denuncia.id");

        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select("julgamentoRecursoDenuncia{$alias}.sancao");
        $subQuery->from(JulgamentoRecursoDenuncia::class, "julgamentoRecursoDenuncia{$alias}");
        $subQuery->where("julgamentoRecursoDenuncia{$alias}.id in ($subQuery2)");

        return $subQuery;
    }

}
