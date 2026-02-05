<?php
/*
 * DenunciaInadmitida.php
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
 * Entidade de representação de 'DenunciaInadmitida'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DenunciaInadmitidaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DENUNCIA_INADMITIDA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DenunciaInadmitida extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DENUNCIA_INADMITIDA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_denuncia_inadmitida_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_INADMISSAO", type="string", length=2000, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="DT_INADMISSAO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $dataInadmissao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\Denuncia
     */
    private $denuncia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoDenunciaInadmitida", mappedBy="denunciaInadmitida", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $arquivoDenunciaInadmitida;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\MembroComissao")
     * @ORM\JoinColumn(name="ID_COORDENADOR", referencedColumnName="ID_MEMBRO_COMISSAO", nullable=false)
     *
     * @var \App\Entities\MembroComissao
     */
    private $coordenador;

    /**
     * Transient
     *
     * @var int
     */
    private $idDenuncia;

    /**
     * Fábrica de instância de 'DenunciaInadmitida'.
     *
     * @param array $data
     * @return mixed
     */
    public static function newInstance($data = null)
    {
        $denunciaInadmitida = new DenunciaInadmitida();

        if ($data != null) {
            $denunciaInadmitida->setId(Utils::getValue('id', $data));
            $denunciaInadmitida->setDataInadmissao(Utils::getValue('dataInadmissao', $data));
            $denunciaInadmitida->setDescricao(Utils::getValue('descricao', $data));
            $denunciaInadmitida->setIdDenuncia(Utils::getValue('idDenuncia', $data));

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $denunciaInadmitida->setDenuncia(Denuncia::newInstance($denuncia));
            }

            $coordenador = Utils::getValue('coordenador', $data);
            if (!empty($coordenador)) {
                $denunciaInadmitida->setCoordenador(MembroComissao::newInstance($coordenador));
            }

            $arquivosDenuncia = Utils::getValue('arquivoDenunciaInadmitida', $data);
            if (!empty($arquivosDenuncia)) {
                foreach ($arquivosDenuncia as $arquivoDenunciaInadmitida) {
                    $denunciaInadmitida->adicionarArquivoDenunciaInadmitida(ArquivoDenunciaInadmitida::newInstance($arquivoDenunciaInadmitida));
                }
            }
        }
        return $denunciaInadmitida;
    }

    /**
     * Adiciona o 'ArquivoDenunciaInadmitida' à sua respectiva coleção.
     *
     * @param ArquivoDenunciaInadmitida $arquivoDenunciaInadmitida
     */
    private function adicionarArquivoDenunciaInadmitida(ArquivoDenunciaInadmitida $arquivoDenunciaInadmitida)
    {
        if (empty($this->getArquivoDenunciaInadmitida())) {
            $this->setArquivoDenunciaInadmitida(new ArrayCollection());
        }

        if (!empty($arquivoDenunciaInadmitida)) {
            $arquivoDenunciaInadmitida->setDenunciaInadmitida($this);
            $this->getArquivoDenunciaInadmitida()->add($arquivoDenunciaInadmitida);
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
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return \DateTime
     */
    public function getDataInadmissao()
    {
        return $this->dataInadmissao;
    }

    /**
     * @param \DateTime $dataInadmissao
     */
    public function setDataInadmissao($dataInadmissao): void
    {
        $this->dataInadmissao = $dataInadmissao;
    }

    /**
     * @return Denuncia
     */
    public function getDenuncia(): ?Denuncia
    {
        return $this->denuncia;
    }

    /**
     * @param Denuncia $denuncia
     */
    public function setDenuncia(?Denuncia $denuncia): void
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
     * @return array|ArrayCollection
     */
    public function getArquivoDenunciaInadmitida()
    {
        return $this->arquivoDenunciaInadmitida;
    }

    /**
     * @param array|ArrayCollection $arquivoDenunciaInadmitida
     */
    public function setArquivoDenunciaInadmitida($arquivoDenunciaInadmitida): void
    {
        $this->arquivoDenunciaInadmitida = $arquivoDenunciaInadmitida;
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

    /**
     * Método para retirar os binários de arquivos
     */
    public function removerFiles()
    {
        if (!empty($this->arquivoDenunciaInadmitida)) {
            foreach ($this->arquivoDenunciaInadmitida as $arquivo) {
                $arquivo->setArquivo(null);
            }
        }
    }
}