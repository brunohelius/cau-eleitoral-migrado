<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'Arquivo Recurso e Reconsideração'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoRecursoContrarrazaoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_RECURSO_CONTRARRAZAO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoRecursoContrarrazaoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_RECURSO_CONTRARRAZAO_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_recurso_contrarrazao_denuncia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\RecursoDenuncia")
     * @ORM\JoinColumn(name="ID_RECURSO_CONTRARRAZAO_DENUNCIA", referencedColumnName="ID_RECURSO_CONTRARRAZAO_DENUNCIA", nullable=false)
     *
     * @var RecursoDenuncia|null
     */
    private $recurso;

    /**
     * @ORM\Column(name="NM_ARQUIVO", type="string", length=200, nullable=false)
     * @var string
     */
    private $nome;

    /**
     * @ORM\Column(name="NM_FIS_ARQUIVO", type="string", length=20, nullable=false)
     *
     * @var string
     */
    private $nomeFisico;

    /**
     * Transient
     *
     * @var mixed
     */
    private $arquivo;

    /**
     * Transient
     *
     * @var mixed
     */
    private $tamanho;

    /**
     * Fábrica de instância de 'Arquivo Denúncia'.
     *
     * @param array $data
     *
     * @return ArquivoRecursoContrarrazaoDenuncia
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();
        if ($data !== null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setNome(Utils::getValue('nome', $data));
            $instance->setNomeFisico(Utils::getValue('nomeFisico', $data));
            $instance->setTamanho(Utils::getValue('tamanho', $data));
            $instance->setArquivo(Utils::getValue('arquivo', $data));

            $recurso = Utils::getValue('recurso', $data);
            if (!empty($recurso)) {
                $instance->setRecurso(RecursoDenuncia::newInstance($recurso));
            }
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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return RecursoDenuncia|null
     */
    public function getRecurso()
    {
        return $this->recurso;
    }

    /**
     * @param $recurso
     */
    public function setRecurso($recurso)
    {
        $this->recurso = $recurso;
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
     */
    public function setNome(string $nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getNomeFisico()
    {
        return $this->nomeFisico;
    }

    /**
     * @param mixed $nomeFisico
     */
    public function setNomeFisico($nomeFisico)
    {
        $this->nomeFisico = $nomeFisico;
    }

    /**
     * @return mixed
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * @param mixed $arquivo
     */
    public function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;
    }

    /**
     * @return mixed
     */
    public function getTamanho()
    {
        return $this->tamanho;
    }

    /**
     * @param mixed $tamanho
     */
    public function setTamanho($tamanho)
    {
        $this->tamanho = $tamanho;
    }
}