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
 * Entidade de representação de 'Tipo de Processo do Calendário'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoProcessoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TIPO_PROCESSO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TipoProcesso extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_TIPO_PROCESSO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_tipo_processo_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TIPO_PROCESSO", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de Tipo de Processo do Calendário'.
     *
     * @param array $data
     * @return \App\Entities\TipoProcesso
     */
    public static function newInstance($data = null)
    {
        $tipoProcesso = new TipoProcesso();

        if ($data != null) {
            $tipoProcesso->setId(Utils::getValue('id', $data));
            $tipoProcesso->setDescricao(Utils::getValue('descricao', $data));
        }
        return $tipoProcesso;
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