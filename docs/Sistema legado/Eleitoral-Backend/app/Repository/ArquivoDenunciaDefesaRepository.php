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
use App\Entities\ArquivoDenunciaDefesa;
use App\Entities\Denuncia;
use App\Entities\DenunciaMembroChapa;
use App\Entities\DenunciaMembroComissao;
use App\Entities\DenunciaSituacao;
use App\Entities\Filial;
use App\Entities\HistoricoDenuncia;
use App\Entities\Pessoa;
use App\Entities\Profissional;
use App\Entities\TipoDenuncia;
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
class ArquivoDenunciaDefesaRepository extends AbstractRepository
{
    /**
     * Retorna o arquivo a denuncia Defesa conforme o id informado.
     *
     * @param $id
     * @return array|null
     */
    public function getArquivoPorDenuncia($id)
    {
        try {
            $query = $this->createQueryBuilder('arquivoDenunciaDefesa');
            $query->innerJoin("arquivoDenunciaDefesa.denunciaDefesa", "denunciaDefesa")->addSelect('denunciaDefesa');
            $query->innerJoin("denunciaDefesa.denuncia", "denuncia")->addSelect('denuncia');

            $query->where("denuncia.id = :id");
            $query->setParameter("id", $id);

            $arquivo = $query->getQuery()->getArrayResult();

            return array_map(function ($dadosArquivo) {
                return ArquivoDenunciaDefesa::newInstance($dadosArquivo);
            }, $arquivo);
        } catch (NoResultException $e) {
            return null;
        }
    }

}
