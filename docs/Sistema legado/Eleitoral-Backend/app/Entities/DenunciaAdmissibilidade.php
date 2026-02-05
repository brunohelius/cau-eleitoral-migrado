<?php
/*
 * DenunciaAdmitida.php
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
 * Entidade de representação de 'DenunciaAdmitida'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DenunciaAdmissibilidadeRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DENUNCIA_ADMITIDA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DenunciaAdmissibilidade extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DENUNCIA_ADMITIDA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_denuncia_admitida_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_DESPACHO_DESIGNACAO", type="string", length=2000, nullable=false)
     *
     * @var string
     */
    private $descricaoDespacho;

    /**
     * @ORM\Column(name="DT_ADMISSAO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $dataAdmissao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\MembroComissao")
     * @ORM\JoinColumn(name="ID_MEMBRO_COMISSAO", referencedColumnName="ID_MEMBRO_COMISSAO", nullable=false)
     *
     * @var \App\Entities\MembroComissao
     */
    private $membroComissao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\Denuncia
     */
    private $denuncia;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="ArquivoDenunciaAdmissibilidade", mappedBy="idDenuncia", cascade={"all"})
     */
    private $arquivos;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\MembroComissao")
     * @ORM\JoinColumn(name="ID_COORDENADOR", referencedColumnName="ID_MEMBRO_COMISSAO", nullable=false)
     *
     * @var \App\Entities\MembroComissao
     */
    private $coordenador;

    /**
     * @var int
     */
    private $idDenuncia;

    /**
     * @var int
     */
    private $idMembroComissao;

    /**
     * DenunciaAdmitida constructor.
     */
    public function __construct()
    {
        $this->arquivos = new ArrayCollection();
    }

    /**
     * Fábrica de instância de 'DenunciaAdmitida'.
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $denunciaAdmitida = new DenunciaAdmitida();

        if ($data != null) {
            $denunciaAdmitida->setId(Utils::getValue('id', $data));
            $denunciaAdmitida->setDataAdmissao(Utils::getValue('dataAdmissao', $data));
            $denunciaAdmitida->setDescricaoDespacho(Utils::getValue('descricaoDespacho', $data));
            $denunciaAdmitida->setIdDenuncia(Utils::getValue('idDenuncia', $data));
            $denunciaAdmitida->setIdMembroComissao(Utils::getValue('idMembroComissao', $data));

            $membroComissao = Utils::getValue('membroComissao', $data);
            if (!empty($membroComissao)) {
                $denunciaAdmitida->setMembroComissao(MembroComissao::newInstance($membroComissao));
            }

            $coordenador = Utils::getValue('coordenador', $data);
            if (!empty($coordenador)) {
                $denunciaAdmitida->setCoordenador(MembroComissao::newInstance($coordenador));
            }

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $denunciaAdmitida->setDenuncia(Denuncia::newInstance($denuncia));
            }
        }
        return $denunciaAdmitida;
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
     * @return string
     */
    public function getDescricaoDespacho()
    {
        return $this->descricaoDespacho;
    }

    /**
     * @param string $descricaoDespacho
     * @return $this
     */
    public function setDescricaoDespacho($descricaoDespacho)
    {
        $this->descricaoDespacho = $descricaoDespacho;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDataAdmissao()
    {
        return $this->dataAdmissao;
    }

    /**
     * @param \DateTime $dataAdmissao
     * @return $this
     */
    public function setDataAdmissao($dataAdmissao)
    {
        $this->dataAdmissao = $dataAdmissao;
        return $this;
    }

    /**
     * @return MembroComissao
     */
    public function getMembroComissao()
    {
        return $this->membroComissao;
    }

    /**
     * @param MembroComissao $membroComissao
     * @return $this
     */
    public function setMembroComissao(MembroComissao $membroComissao)
    {
        $this->membroComissao = $membroComissao;
        return $this;
    }

    /**
     * @return Denuncia
     */
    public function getDenuncia()
    {
        return $this->denuncia;
    }

    /**
     * @param Denuncia $denuncia
     * @return $this
     */
    public function setDenuncia(Denuncia $denuncia)
    {
        $this->denuncia = $denuncia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param mixed $arquivos
     * @return DenunciaAdmitida
     */
    public function setArquivos($arquivos)
    {
        $this->arquivos = $arquivos;
        return $this;
    }

    /**
     * @param ArquivoDenunciaAdmissibilidade $arquivo
     * @return $this
     */
    public function addArquivo(ArquivoDenunciaAdmissibilidade $arquivo)
    {
        $arquivo->setDenunciaAdmitida($this);
        $this->arquivos->add($arquivo);
        return $this;
    }

    /**
     * @param ArquivoDenunciaAdmissibilidade $arquivo
     * @return $this
     */
    public function removeArquivo(ArquivoDenunciaAdmissibilidade $arquivo)
    {
        $this->arquivos->removeElement($arquivo);
        return $this;
    }

    /**
     * @return int
     */
    public function getIdDenuncia()
    {
        return $this->idDenuncia;
    }

    /**
     * @param int $idDenuncia
     * @return $this
     */
    public function setIdDenuncia($idDenuncia)
    {
        $this->idDenuncia = $idDenuncia;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdMembroComissao()
    {
        return $this->idMembroComissao;
    }

    /**
     * @param int $idMembroComissao
     * @return $this
     */
    public function setIdMembroComissao($idMembroComissao)
    {
        $this->idMembroComissao = $idMembroComissao;
        return $this;
    }

    /**
     * @return MembroComissao
     */
    public function getCoordenador(): ?MembroComissao
    {
        return $this->coordenador;
    }

    /**
     * @param MembroComissao $coordenador
     * @return $this
     */
    public function setCoordenador(?MembroComissao $coordenador)
    {
        $this->coordenador = $coordenador;
        return $this;
    }
}
