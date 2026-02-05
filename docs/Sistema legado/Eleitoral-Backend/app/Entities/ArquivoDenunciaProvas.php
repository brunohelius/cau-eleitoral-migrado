<?php
/*
 * ArquivoDenunciaProvas.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Arquivo Denuncia Provas'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoDenunciaProvasRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_DENUNCIA_PROVAS")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDenunciaProvas extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_DENUNCIA_PROVAS", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_denuncia_provas_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\DenunciaProvas")
     * @ORM\JoinColumn(name="ID_DENUNCIA_PROVAS", referencedColumnName="ID_DENUNCIA_PROVAS", nullable=false)
     * @var DenunciaProvas
     */
    private $denunciaProvas;

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
    private $nomeFisicoArquivo;

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
     * Fábrica de instância de 'Arquivo Denuncia Defesa'.
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $arquivoDenunciaProvas = new ArquivoDenunciaProvas();

        if ($data != null) {
            $arquivoDenunciaProvas->setId(Utils::getValue('id', $data));

            $arquivoDenunciaProvas->setArquivo(Utils::getValue('arquivo', $data));
            $arquivoDenunciaProvas->setTamanho(Utils::getValue('tamanho', $data));

            $arquivoDenunciaProvas->setNome(Utils::getValue('nome', $data));
            $arquivoDenunciaProvas->setNomeFisicoArquivo(Utils::getValue('nomeFisicoArquivo', $data));

            $denunciaProvas = Utils::getValue('denunciaProvas', $data);
            if (!empty($denunciaProvas)) {
                $arquivoDenunciaProvas->setDenunciaProvas(DenunciaProvas::newInstance($denunciaProvas));
            }
        }

        return $arquivoDenunciaProvas;
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
     * @return DenunciaProvas
     */
    public function getDenunciaProvas()
    {
        return $this->denunciaProvas;
    }

    /**
     * @param DenunciaProvas $denunciaProvas
     */
    public function setDenunciaProvas(DenunciaProvas $denunciaProvas)
    {
        $this->denunciaProvas = $denunciaProvas;
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
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return string
     */
    public function getNomeFisicoArquivo()
    {
        return $this->nomeFisicoArquivo;
    }

    /**
     * @param $nomeFisicoArquivo
     */
    public function setNomeFisicoArquivo($nomeFisicoArquivo)
    {
        $this->nomeFisicoArquivo = $nomeFisicoArquivo;
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

    /**
     * Metodo adptado para pegar o nome fisico do arquivo
     * @return string
     */
    public function getNomeFisico() {
        return $this->getNomeFisicoArquivo();
    }
}
