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
 * Entidade de representação de 'Status do Julgamento de Recursos do Pedido de Impugnção de Resultado'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\StatusJulgamentoRecursoImpugResultadoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_STATUS_JULGAMENTO_RECURSO_RESULTADO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class StatusJulgamentoRecursoImpugResultado extends Entity
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
     * Fábrica de instância de 'StatusJulgamentoRecursoResultado'.
     *
     * @param array $data
     * @return StatusJulgamentoRecursoImpugResultado
     */
    public static function newInstance($data = null)
    {
        $statusJulgamentoRecursoResultado = new StatusJulgamentoRecursoImpugResultado();

        if ($data != null) {
            $statusJulgamentoRecursoResultado->setId(Utils::getValue('id', $data));
            $statusJulgamentoRecursoResultado->setDescricao(Utils::getValue('descricao', $data));
        }
        return $statusJulgamentoRecursoResultado;
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
