<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ArquivoJulgamentoRecursoAdmissibilidade
 * @package App\Entities
 *
 * @ORM\Table(name="tb_arquivo_julgamentorecursojulgamentoadmissibilidade", schema="eleitoral")
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoJulgamentoRecursoAdmissibilidadeRepository")
 */
class ArquivoJulgamentoRecursoAdmissibilidade extends Entity
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
     * @var JulgamentoRecursoAdmissibilidade
     *
     * @ORM\ManyToOne(targetEntity="JulgamentoRecursoAdmissibilidade", inversedBy="arquivos")
     * @ORM\JoinColumn(name="julgamentorecursojulgamento_id", referencedColumnName="id_julgamento_recursojulgamento")
     */
    private $julgamentoRecursoAdmissibilidade;

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
     * @return ArquivoJulgamentoRecursoAdmissibilidade
     */
    public function setId( $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return JulgamentoRecursoAdmissibilidade
     */
    public function getJulgamentoRecursoAdmissibilidade()
    {
        return $this->julgamentoRecursoAdmissibilidade;
    }

    /**
     * @param JulgamentoRecursoAdmissibilidade $julgamentoRecursoAdmissibilidade
     * @return ArquivoJulgamentoRecursoAdmissibilidade
     */
    public function setJulgamentoRecursoAdmissibilidade(JulgamentoRecursoAdmissibilidade $julgamentoRecursoAdmissibilidade)
    {
        $this->julgamentoRecursoAdmissibilidade = $julgamentoRecursoAdmissibilidade;
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
     * @return ArquivoJulgamentoRecursoAdmissibilidade
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
     * @return ArquivoJulgamentoRecursoAdmissibilidade
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