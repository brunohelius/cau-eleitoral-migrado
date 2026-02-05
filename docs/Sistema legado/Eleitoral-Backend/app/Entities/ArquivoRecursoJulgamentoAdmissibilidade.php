<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ArquivoRecursoJulgamentoAdmissibilidade
 * @package App\Entities
 *
 * @ORM\Table(name="tb_arquivo_recursojulgamentoadmissibilidade", schema="eleitoral")
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoRecursoJulgamentoAdmissibilidadeRepository")
 */
class ArquivoRecursoJulgamentoAdmissibilidade extends Entity
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_recursojulgamentoadmissibilidade_id_arquivo_seq", )
     * @ORM\Column(name="id_arquivo", type="integer")
     */
    private $id;

    /**
     * @var RecursoJulgamentoAdmissibilidade
     *
     * @ORM\ManyToOne(targetEntity="RecursoJulgamentoAdmissibilidade", inversedBy="arquivos")
     * @ORM\JoinColumn(name="recursojulgamento_id", referencedColumnName="id_recurso_julgamento")
     */
    private $recurso;

    /**
     * @var string
     *
     * @ORM\Column(name="no_arquivo")
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="no_fis_arquivo")
     */
    private $nomeFisico;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ArquivoRecursoJulgamentoAdmissibilidade
     */
    public function setId( $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return RecursoJulgamentoAdmissibilidade
     */
    public function getRecurso()
    {
        return $this->recurso;
    }

    /**
     * @param RecursoJulgamentoAdmissibilidade $recurso
     * @return ArquivoRecursoJulgamentoAdmissibilidade
     */
    public function setRecurso(RecursoJulgamentoAdmissibilidade $recurso)
    {
        $this->recurso = $recurso;
        return $this;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     * @return ArquivoRecursoJulgamentoAdmissibilidade
     */
    public function setNome(string $nome)
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeFisico()
    {
        return $this->nomeFisico;
    }

    /**
     * @param string $nomeFisico
     * @return ArquivoRecursoJulgamentoAdmissibilidade
     */
    public function setNomeFisico(string $nomeFisico)
    {
        $this->nomeFisico = $nomeFisico;
        return $this;
    }

    public static function newInstance($data = null)
    {
        $self = new self;
        if ($data) {
            $self->setId(Utils::getValue('id', $data));
            $self->setNome(Utils::getValue('nome', $data));
            $self->setNomeFisico(Utils::getValue('nomeFisico', $data));
        }
        return $self;
    }
}