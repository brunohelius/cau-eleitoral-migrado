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
use App\Entities\DenunciaDefesa;
use App\Entities\DenunciaMembroChapa;
use App\Entities\DenunciaMembroComissao;
use App\Entities\DenunciaSituacao;
use App\Entities\Filial;
use App\Entities\HistoricoDenuncia;
use App\Entities\Pessoa;
use App\Entities\Profissional;
use App\Entities\TipoDenuncia;
use App\To\DenunciaDefesaTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Denuncia Defesa'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DenunciaDefesaRepository extends AbstractRepository
{
    /**
     * Retorna a Defesa de um membro de chapa ou comissão por Denunciado
     *
     * @param $idProfissional
     * @return DenunciaDefesaTO|null
     * @throws NonUniqueResultException
     */
    public function getPorDenunciado($idProfissional)
    {
        try {
            $query = $this->createQueryBuilder('denunciaDefesa');
            $query->innerJoin('denunciaDefesa.denuncia', 'denuncia')->addSelect('denuncia');
            $query->leftJoin('denunciaDefesa.arquivosDenunciaDefesa', 'arquivosDenunciaDefesa')->addSelect('arquivosDenunciaDefesa');

            $query->leftJoin('denuncia.denunciaMembroChapa', 'denunciaMembroChapa')->addSelect('denunciaMembroChapa');
            $query->leftJoin('denunciaMembroChapa.membroChapa', 'membroChapa')->addSelect('membroChapa');
            $query->leftJoin('membroChapa.profissional', 'profissional')->addSelect('profissional');

            $query->leftJoin('denuncia.denunciaMembroComissao', 'denunciaMembroComissao')->addSelect('denunciaMembroComissao');
            $query->leftJoin('denunciaMembroComissao.membroComissao', 'membroComissao')->addSelect('membroComissao');
            $query->leftJoin('membroComissao.profissionalEntity', 'profissionalEntity')->addSelect('profissionalEntity');

            $query->where('profissional.id = :idProfissional OR profissionalEntity.id = :idProfissional');
            $query->setParameter(':idProfissional', $idProfissional);

            $defesa = $query->getQuery()->getSingleResult();

            return DenunciaDefesaTO::newInstanceFromEntity($defesa);
        } catch (NoResultException $e) {
            return null;
        }
    }
}
