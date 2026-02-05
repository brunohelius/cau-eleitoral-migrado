<?php
/*
 * StatusChapa.php
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
 * Entidade de representação de 'Status da Chapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\StatusChapaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_STATUS_CHAPA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class StatusChapa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_STATUS_CHAPA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_status_chapa_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_STATUS_CHAPA", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'Status da Chapa'.
     *
     * @param array $data
     * @return \App\Entities\StatusChapa
     */
    public static function newInstance($data = null)
    {
        $statusChapa = new StatusChapa();

        if ($data != null) {
            $statusChapa->setId(Utils::getValue('id', $data));
            $statusChapa->setDescricao(Utils::getValue('descricao', $data));
        }
        return $statusChapa;
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