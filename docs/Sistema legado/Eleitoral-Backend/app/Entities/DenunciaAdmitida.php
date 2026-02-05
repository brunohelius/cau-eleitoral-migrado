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
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'DenunciaAdmitida'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DenunciaAdmitidaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DENUNCIA_ADMITIDA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DenunciaAdmitida extends Entity
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
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoDenunciaAdmitida", mappedBy="denunciaAdmitida")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $arquivoDenunciaAdmitida;

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

            $arquivosDenunciaAdmitida = Utils::getValue('arquivosDenunciaAdmitida', $data);
            if (!empty($arquivosDenunciaAdmitida)) {
                foreach ($arquivosDenunciaAdmitida as $arquivoDenuncia) {
                    $denunciaAdmitida->adicionarArquivoDenunciaAdmitida(ArquivoDenunciaAdmitida::newInstance($arquivoDenuncia));
                }
            }
        }
        return $denunciaAdmitida;
    }

    /**
     * Adiciona o 'ArquivoDenunciaAdmitida' à sua respectiva coleção.
     *
     * @param ArquivoDenunciaAdmitida $arquivoDenunciaAdmitida
     */
    private function adicionarArquivoDenunciaAdmitida(ArquivoDenunciaAdmitida $arquivoDenunciaAdmitida)
    {
        if ($this->getArquivoDenunciaAdmitida() == null) {
            $this->setArquivoDenunciaAdmitida(new ArrayCollection());
        }

        if (!empty($arquivoDenunciaAdmitida)) {
            $arquivoDenunciaAdmitida->setDenunciaAdmitida($this);
            $this->getArquivoDenunciaAdmitida()->add($arquivoDenunciaAdmitida);
        }
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
     */
    public function setDescricaoDespacho($descricaoDespacho): void
    {
        $this->descricaoDespacho = $descricaoDespacho;
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
     */
    public function setDataAdmissao($dataAdmissao): void
    {
        $this->dataAdmissao = $dataAdmissao;
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
     */
    public function setMembroComissao(MembroComissao $membroComissao): void
    {
        $this->membroComissao = $membroComissao;
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
     */
    public function setDenuncia(Denuncia $denuncia): void
    {
        $this->denuncia = $denuncia;
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
     */
    public function setIdDenuncia($idDenuncia): void
    {
        $this->idDenuncia = $idDenuncia;
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
     */
    public function setIdMembroComissao($idMembroComissao): void
    {
        $this->idMembroComissao = $idMembroComissao;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivoDenunciaAdmitida()
    {
        return $this->arquivoDenunciaAdmitida;
    }

    /**
     * @param array|ArrayCollection $arquivoDenunciaAdmitida
     */
    public function setArquivoDenunciaAdmitida($arquivoDenunciaAdmitida): void
    {
        $this->arquivoDenunciaAdmitida = $arquivoDenunciaAdmitida;
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
     */
    public function setCoordenador(?MembroComissao $coordenador): void
    {
        $this->coordenador = $coordenador;
    }
}
