<?php
/*
 * StatusImpugnacaoResultado.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação da 'Filial' / 'CAU/UF'
 *
 * @ORM\Entity(repositoryClass="App\Repository\StatusImpugnacaoResultadoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_STATUS_PEDIDO_IMPUGNACAO_RESULTADO")
 *
 * @package App\Entities
 */
class StatusImpugnacaoResultado extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     *
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DESCRICAO", type="string",nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'ImpugnacaoResultado'.
     *
     * @param array $data
     * @return StatusImpugnacaoResultado
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $statusImpugnacaoResultado = new StatusImpugnacaoResultado();

        if ($data != null) {
            $statusImpugnacaoResultado->setId(Utils::getValue('id', $data));
            $statusImpugnacaoResultado->setDescricao(Utils::getValue('descricao', $data));
        }
        return $statusImpugnacaoResultado;
    }

    /**
     * Fábrica de instância de 'ImpugnacaoResultado'.
     *
     * @param $id
     * @return StatusImpugnacaoResultado
     */
    public static function newInstanceById($id)
    {
        return StatusImpugnacaoResultado::newInstance(['id' => $id]);
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
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }
}
