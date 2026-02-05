<?php
/*
 * ArquivoJulgamentoRecursoDenuncia.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'ArquivoJulgamentoRecursoDenuncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoJulgamentoRecursoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_JULGAMENTO_RECURSO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoJulgamentoRecursoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_JULGAMENTO_RECURSO_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_julgamento_recurso_denuncia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="NM_ARQUIVO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $nome;

    /**
     * @ORM\Column(name="NM_FIS_ARQUIVO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $nomeFisico;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\JulgamentoRecursoDenuncia")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_RECURSO_DENUNCIA", referencedColumnName="ID_JULGAMENTO_RECURSO_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\JulgamentoRecursoDenuncia
     */
    private $julgamentoRecursoDenuncia;

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
     * Fábrica de instância de 'ArquivoJulgamentoRecursoDenuncia'.
     *
     * @param null $data
     *
     * @return self
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data !== null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setNome(Utils::getValue('nome', $data));
            $instance->setTamanho(Utils::getValue('tamanho', $data));
            $instance->setArquivo(Utils::getValue('arquivo', $data));
            $instance->setNomeFisico(Utils::getValue('nomeFisico', $data));

            $julgamentoRecursoDenuncia = Utils::getValue('julgamentoRecursoDenuncia', $data);
            if (!empty($julgamentoRecursoDenuncia)) {
                $instance->setJulgamentoRecursoDenuncia(JulgamentoRecursoDenuncia::newInstance($julgamentoRecursoDenuncia));
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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return JulgamentoRecursoDenuncia
     */
    public function getJulgamentoRecursoDenuncia()
    {
        return $this->julgamentoRecursoDenuncia;
    }

    /**
     * @param JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia
     */
    public function setJulgamentoRecursoDenuncia($julgamentoRecursoDenuncia): void
    {
        $this->julgamentoRecursoDenuncia = $julgamentoRecursoDenuncia;
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
    public function setNome($nome): void
    {
        $this->nome = $nome;
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
     */
    public function setNomeFisico($nomeFisico): void
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
    public function setArquivo($arquivo): void
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
    public function setTamanho($tamanho): void
    {
        $this->tamanho = $tamanho;
    }
}
