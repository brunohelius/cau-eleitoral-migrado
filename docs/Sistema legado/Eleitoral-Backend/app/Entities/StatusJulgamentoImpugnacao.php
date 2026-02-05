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
 * Entidade de representação de 'Status do Pedido de Impugnção'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\StatusJulgamentoImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_STATUS_JULGAMENTO_IMPUGNACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class StatusJulgamentoImpugnacao extends Entity
{

    /**
     * @ORM\Id
     * @ORM\Column(name="ID_STATUS_JULGAMENTO_IMPUGNACAO", type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_STATUS_JULGAMENTO_IMPUGNACAO", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'Status de Julgamento de Impugnação'.
     *
     * @param array $data
     * @return StatusJulgamentoImpugnacao
     */
    public static function newInstance($data = null)
    {
        $statusJulgamentoImpugnacao = new StatusJulgamentoImpugnacao();

        if ($data != null) {
            $statusJulgamentoImpugnacao->setId(Utils::getValue('id', $data));
            $statusJulgamentoImpugnacao->setDescricao(Utils::getValue('descricao', $data));
        }
        return $statusJulgamentoImpugnacao;
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
