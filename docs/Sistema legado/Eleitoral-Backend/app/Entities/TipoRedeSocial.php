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
 * Entidade de representação de 'Tipo de Rede Social'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoRedeSocialRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TP_REDE_SOCIAL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TipoRedeSocial extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_TP_REDE_SOCIAL", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_tipo_rede_social_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TP_REDE_SOCIAL", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'Tipo de Rede Social'.
     *
     * @param array $data
     * @return \App\Entities\TipoRedeSocial
     */
    public static function newInstance($data = null)
    {
        $tipoRedeSocial = new TipoRedeSocial();

        if ($data != null) {
            $tipoRedeSocial->setId(Utils::getValue('id', $data));
            $tipoRedeSocial->setDescricao(Utils::getValue('descricao', $data));
        }

        return $tipoRedeSocial;
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
