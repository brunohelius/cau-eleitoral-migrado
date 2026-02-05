<?php
/*
 * StatusPedidoImpugnacaoRepository.php
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
 * Entidade de representação de 'StatusChapaJulgamentoFinal'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\StatusChapaJulgamentoFinalRepository")
 * @ORM\Table(schema="eleitoral", name="TB_STATUS_CHAPA_JULGAMENTO_FINAL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class StatusChapaJulgamentoFinal extends Entity
{

    /**
     * @ORM\Id
     * @ORM\Column(name="ID", type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DESCRICAO", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="PARECER", type="string", length=100, nullable=true)
     *
     * @var string
     */
    private $parecer;

    /**
     * Fábrica de instância de 'Status Chapa do Julgamento Final'.
     *
     * @param array $data
     * @return StatusChapaJulgamentoFinal
     */
    public static function newInstance($data = null)
    {
        $statusJulgamento = new StatusChapaJulgamentoFinal();

        if ($data != null) {
            $statusJulgamento->setId(Utils::getValue('id', $data));
            $statusJulgamento->setDescricao(Utils::getValue('descricao', $data));
            $statusJulgamento->setParecer(Utils::getValue('parecer', $data));
        }
        return $statusJulgamento;
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

    /**
     * @return string
     */
    public function getParecer()
    {
        return $this->parecer;
    }

    /**
     * @param string $parecer
     */
    public function setParecer($parecer): void
    {
        $this->parecer = $parecer;
    }

}
