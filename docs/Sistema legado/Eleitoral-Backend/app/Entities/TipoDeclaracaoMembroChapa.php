<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'Tipo de Declaração de Membro Chapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoDeclaracaoMembroChapaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TP_DECLARACAO_MEMBRO_CHAPA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TipoDeclaracaoMembroChapa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_TP_DECLARACAO_MEMBRO_CHAPA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_tp_dec_membro_chapa_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TP_DECLARACAO_MEMBRO_CHAPA", type="string", nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'Tipo Declaração Membro Chapa'.
     *
     * @param array $data
     * @return self
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setDescricao(Utils::getValue('descricao', $data));
        }

        return $instance;
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
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }
}
