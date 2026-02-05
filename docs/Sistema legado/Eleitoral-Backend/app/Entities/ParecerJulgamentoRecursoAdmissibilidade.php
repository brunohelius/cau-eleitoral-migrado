<?php

namespace App\Entities;

use App\Util\Utils;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ParecerJulgamentoRecursoAdmissibilidade
 * @package App\Entities
 *
 * @ORM\Table(name="tb_parecer_julgamentorecursoadmissibilidade", schema="eleitoral")
 * @ORM\Entity(repositoryClass="App\Repository\ParecerJulgamentoRecursoAdmissibilidadeRepository")
 */
class ParecerJulgamentoRecursoAdmissibilidade extends Entity
{
    CONST PROVIMENTO = 1;
    CONST IMPROVIMENTO = 2;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao")
     */
    private $descricao;

    public static function newInstance($data = null )
    {

        $self = new self();
        $id = Utils::getValue('id', $data);
        if ($id) {
            $self->setId($id);
        }

        $descricao = Utils::getValue('descricao', $data);
        if ($descricao) {
            $self->setDescricao($descricao);
        }

        return $self;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ParecerJulgamentoRecursoAdmissibilidade
     */
    public function setId(int $id): ParecerJulgamentoRecursoAdmissibilidade
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
     * @return ParecerJulgamentoRecursoAdmissibilidade
     */
    public function setDescricao(string $descricao): ParecerJulgamentoRecursoAdmissibilidade
    {
        $this->descricao = $descricao;
        return $this;
    }



}