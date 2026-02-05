<?php
/*
 * ArquivoJulgamentoDenuncia.php
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
 * Entidade de representação de 'ArquivoJulgamentoDenuncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoJulgamentoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_JULGAMENTO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoJulgamentoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_JULGAMENTO_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_julgamento_denuncia_id_seq", initialValue=1, allocationSize=1)
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
     * @ORM\ManyToOne(targetEntity="App\Entities\JulgamentoDenuncia")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_DENUNCIA", referencedColumnName="ID_JULGAMENTO_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\JulgamentoDenuncia
     */
    private $julgamentoDenuncia;

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
     * Fábrica de instância de 'ArquivoEncaminhamentoDenuncia'.
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

            $julgamentoDenuncia = Utils::getValue('julgamentoDenuncia', $data);
            if (!empty($julgamentoDenuncia)) {
                $instance->setJulgamentoDenuncia(JulgamentoDenuncia::newInstance($julgamentoDenuncia));
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
     * @return JulgamentoDenuncia
     */
    public function getJulgamentoDenuncia()
    {
        return $this->julgamentoDenuncia;
    }

    /**
     * @param $julgamentoDenuncia
     */
    public function setJulgamentoDenuncia($julgamentoDenuncia): void
    {
        $this->julgamentoDenuncia = $julgamentoDenuncia;
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