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
 * Entidade de representação de 'Status do Pedido de Substituição'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\StatusJulgamentoSubstituicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_STATUS_JULGAMENTO_SUBSTITUICAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class StatusJulgamentoSubstituicao extends Entity
{

    /**
     * @ORM\Id
     * @ORM\Column(name="ID_STATUS_JULGAMENTO_SUBSTITUICAO", type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_STATUS_JULGAMENTO_SUBSTITUICAO", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'Status de Pedido de Impugnação'.
     *
     * @param array $data
     * @return StatusJulgamentoSubstituicao
     */
    public static function newInstance($data = null)
    {
        $statusJulgamento = new StatusJulgamentoSubstituicao();

        if ($data != null) {
            $statusJulgamento->setId(Utils::getValue('id', $data));
            $statusJulgamento->setDescricao(Utils::getValue('descricao', $data));
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

}
