<?php
/*
 * TipoProcesso.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */


namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'Tipo de Pendência'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoPendenciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TP_PENDENCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TipoPendencia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_TP_PENDENCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_tp_pendencia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TP_PENDENCIA", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de Tipo de Pendência'.
     *
     * @param array $data
     * @return \App\Entities\TipoPendencia
     */
    public static function newInstance($data = null)
    {
        $tipoPendencia = new TipoPendencia();

        if ($data != null) {
            $tipoPendencia->setId(Utils::getValue('id', $data));
            $tipoPendencia->setDescricao(Utils::getValue('descricao', $data));
        }
        return $tipoPendencia;
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