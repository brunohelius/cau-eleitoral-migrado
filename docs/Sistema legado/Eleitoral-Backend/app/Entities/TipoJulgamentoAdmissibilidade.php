<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class TipoJulgamentoAdmissibilidade
 * @package App\Entities
 *
 * @ORM\Table(name="tb_tipo_julgamento_admissibilidade", schema="eleitoral")
 * @ORM\Entity(repositoryClass="App\Repository\TipoJulgamentoAdmissibilidadeRepository")
 */
class TipoJulgamentoAdmissibilidade extends Entity
{

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_tipo_julgamento_admissibilidade_seq")
     * @ORM\Column(name="id_tipo_julg_admissibilidade", type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_tipo_julg_admissibilidade", type="string")
     */
    private $descricao;

    public static function newInstance($data = null)
    {
        $self = new self;
        if($data) {
            $self->setId(Utils::getValue('id', $data));
            $self->setDescricao(Utils::getValue('descricao', $data));
        }
        return $self;
    }

    /**
     * @return string
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return TipoJulgamentoAdmissibilidade
     */
    public function setId(string $id): TipoJulgamentoAdmissibilidade
    {
        $this->id = $id;
        return $this;
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
     * @return TipoJulgamentoAdmissibilidade
     */
    public function setDescricao(string $descricao): TipoJulgamentoAdmissibilidade
    {
        $this->descricao = $descricao;
        return $this;
    }
}