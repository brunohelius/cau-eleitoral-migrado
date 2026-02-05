<?php
/*
 * TipoCandidatura.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'Tipo de Candidatura'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoCandidaturaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TP_CANDIDATURA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TipoCandidatura extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_TP_CANDIDATURA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_tipo_candidatura_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TP_CANDIDATURA", type="string", length=80, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'Tipo de Candidatura'.
     *
     * @param array $data
     * @return \App\Entities\TipoCandidatura
     */
    public static function newInstance($data = null)
    {
        $tipoCandidatura = new TipoCandidatura();

        if ($data != null) {
            $tipoCandidatura->setId(Utils::getValue('id', $data));
            $tipoCandidatura->setDescricao(Utils::getValue('descricao', $data));
        }
        return $tipoCandidatura;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }
}