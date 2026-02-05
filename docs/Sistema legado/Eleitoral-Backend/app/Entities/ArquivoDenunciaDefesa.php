<?php
/*
 * Denuncia.php
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
 * Entidade de representação de 'Arquivo Denuncia Defesa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoDenunciaDefesaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_DENUNCIA_DEFESA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDenunciaDefesa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_DENUNCIA_DEFESA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_denuncia_defesa_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\DenunciaDefesa")
     * @ORM\JoinColumn(name="ID_DENUNCIA_DEFESA", referencedColumnName="ID_DENUNCIA_DEFESA", nullable=false)
     * @var DenunciaDefesa
     */
    private $denunciaDefesa;

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
        $arquivoDenunciaDefesa = new ArquivoDenunciaDefesa();

        if ($data != null) {
            $arquivoDenunciaDefesa->setId(Utils::getValue('id', $data));

            $arquivoDenunciaDefesa->setArquivo(Utils::getValue('arquivo', $data));
            $arquivoDenunciaDefesa->setTamanho(Utils::getValue('tamanho', $data));

            $arquivoDenunciaDefesa->setNome(Utils::getValue('nome', $data));
            $arquivoDenunciaDefesa->setNomeFisicoArquivo(Utils::getValue('nomeFisicoArquivo', $data));

            $denunciaDefesa = Utils::getValue('denunciaDefesa', $data);
            if (!empty($denunciaDefesa)) {
                $arquivoDenunciaDefesa->setDenunciaDefesa(DenunciaDefesa::newInstance($denunciaDefesa));
            }
        }

        return $arquivoDenunciaDefesa;
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
     * @return DenunciaDefesa
     */
    public function getDenunciaDefesa()
    {
        return $this->denunciaDefesa;
    }

    /**
     * @param DenunciaDefesa $denunciaDefesa
     */
    public function setDenunciaDefesa(DenunciaDefesa $denunciaDefesa)
    {
        $this->denunciaDefesa = $denunciaDefesa;
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
}
