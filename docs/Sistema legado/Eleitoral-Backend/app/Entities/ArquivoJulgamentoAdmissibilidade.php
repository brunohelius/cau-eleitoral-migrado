<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ArquivoJulgamentoAdmissibilidade
 * @package App\Entities
 *
 * @ORM\Table(name="tb_arquivo_julgamento_admissibilidade", schema="eleitoral")
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoAdmissibilidadeRepository")
 */
class ArquivoJulgamentoAdmissibilidade extends Entity
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_julgamento_admissibilidade_seq", )
     * @ORM\Column(name="id_arquivo_julg_admissibilidade", type="integer")
     */
    private $id;

    /**
     * @var JulgamentoAdmissibilidade
     *
     * @ORM\ManyToOne(targetEntity="JulgamentoAdmissibilidade", inversedBy="arquivos")
     * @ORM\JoinColumn(name="id_julg_admissibilidade", referencedColumnName="id_julg_admissibilidade")
     */
    private $julgamento;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_arquivo")
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_fis_arquivo")
     */
    private $nomeFisico;

    public static function newInstance($data = null)
    {
        $self = new self;
        if ($data) {
            $self->setId(Utils::getValue('id', $data));
            $julgamento = Utils::getValue('julgamento', $data);
            if ($julgamento) {
                $self->setJulgamento(JulgamentoAdmissibilidade::newInstance($julgamento));
            }
            $self->setNome(Utils::getValue('nome', $data));
            $self->setNomeFisico(Utils::getValue('nomeFisico', $data));
        }
        return $self;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ArquivoJulgamentoAdmissibilidade
     */
    public function setId(int $id): ArquivoJulgamentoAdmissibilidade
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return JulgamentoAdmissibilidade
     */
    public function getJulgamento(): JulgamentoAdmissibilidade
    {
        return $this->julgamento;
    }

    /**
     * @param JulgamentoAdmissibilidade $julgamento
     * @return ArquivoJulgamentoAdmissibilidade
     */
    public function setJulgamento(JulgamentoAdmissibilidade $julgamento): ArquivoJulgamentoAdmissibilidade
    {
        $this->julgamento = $julgamento;
        return $this;
    }

    /**
     * @return string
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     * @return ArquivoJulgamentoAdmissibilidade
     */
    public function setNome(string $nome): ArquivoJulgamentoAdmissibilidade
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeFisico(): string
    {
        return $this->nomeFisico;
    }

    /**
     * @param string $nomeFisico
     * @return ArquivoJulgamentoAdmissibilidade
     */
    public function setNomeFisico(string $nomeFisico): ArquivoJulgamentoAdmissibilidade
    {
        $this->nomeFisico = $nomeFisico;
        return $this;
    }
}