<?php
/*
 * EncaminhamentoDenuncia.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'ArquivoEncaminhamentoDenuncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoEncaminhamentoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_ENCAMINHAMENTO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoEncaminhamentoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_ENCAMINHAMENTO_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_encaminhamento_denuncia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\EncaminhamentoDenuncia")
     * @ORM\JoinColumn(name="ID_ENCAMINHAMENTO_DENUNCIA", referencedColumnName="ID_ENCAMINHAMENTO_DENUNCIA", nullable=false)
     * @var \App\Entities\EncaminhamentoDenuncia
     */
    private $encaminhamentoDenuncia;

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
     * @return ArquivoEncaminhamentoDenuncia
     */
    public static function newInstance($data = null)
    {
        $arquivo = new ArquivoEncaminhamentoDenuncia();

        if ($data != null) {
            $arquivo->setId(Utils::getValue('id', $data));
            $arquivo->setNome(Utils::getValue('nome', $data));
            $arquivo->setNomeFisico(Utils::getValue('nomeFisico', $data));
            $arquivo->setTamanho(Utils::getValue('tamanho', $data));
            $arquivo->setArquivo(Utils::getValue('arquivo', $data));

            $encaminhamentoDenuncia = Utils::getValue('encaminhamentoDenuncia', $data);
            if (!empty($encaminhamentoDenuncia)) {
                $arquivo->setEncaminhamentoDenuncia(EncaminhamentoDenuncia::newInstance($encaminhamentoDenuncia));
            }
        }
        return $arquivo;
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
     * @return EncaminhamentoDenuncia
     */
    public function getEncaminhamentoDenuncia(): EncaminhamentoDenuncia
    {
        return $this->encaminhamentoDenuncia;
    }

    /**
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     */
    public function setEncaminhamentoDenuncia(EncaminhamentoDenuncia $encaminhamentoDenuncia): void
    {
        $this->encaminhamentoDenuncia = $encaminhamentoDenuncia;
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