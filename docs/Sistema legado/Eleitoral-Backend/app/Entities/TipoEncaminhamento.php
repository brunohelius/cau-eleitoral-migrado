<?php
/*
 * TipoEncaminhamento.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'TipoEncaminhamento'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoEncaminhamentoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TIPO_ENCAMINHAMENTO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TipoEncaminhamento extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_TIPO_ENCAMINHAMENTO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_tipo_encaminhamento_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TIPO_ENCAMINHAMENTO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'TipoEncaminhamento'.
     *
     * @param null $data
     * @return TipoEncaminhamento
     */
    public static function newInstance($data = null)
    {
        $tipoEncaminhamento = new TipoEncaminhamento();

        if ($data != null) {
            $tipoEncaminhamento->setId(Utils::getValue('id', $data));
            $tipoEncaminhamento->setDescricao(Utils::getValue('descricao', $data));

        }
        return $tipoEncaminhamento;
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