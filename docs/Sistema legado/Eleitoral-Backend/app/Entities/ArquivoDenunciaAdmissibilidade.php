<?php

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'ArquivoDenunciaAdmissibilidade'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoDenunciaAdmissibilidadeRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_DENUNCIA_ADMITIDA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */

class ArquivoDenunciaAdmissibilidade extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_DENUNCIA_ADMITIDA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_denuncia_admitida_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="DenunciaAdmissibilidade", cascade={"persist"}, inversedBy="arquivos")
     * @ORM\JoinColumn(name="ID_DENUNCIA_ADMITIDA", referencedColumnName="ID_DENUNCIA_ADMITIDA", nullable=false)
     * @var DenunciaAdmitida
     */
    private $denunciaAdmitida;

    /**
     * @ORM\Column(name="NM_ARQUIVO", type="string", length=200, nullable=false)
     */
    private $nmArquivo;

    /**
     * @ORM\Column(name="NM_FIS_ARQUIVO", type="string", length=200, nullable=false)
     */
    private $nmFisicoArquivo;

    /**
     * Fábrica de instância de 'ArquivoDenunciaAdmissibilidade'.
     *
     * @param array $data
     * @return ArquivoDenunciaAdmissibilidade
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $arquivoDenunciaAdmitida = new ArquivoDenunciaAdmissibilidade();

        if ($data != null) {
            $arquivoDenunciaAdmitida->setId(Utils::getValue('id', $data));
            $arquivoDenunciaAdmitida->setArquivo(Utils::getValue('id_denuncia', $data));
            $arquivoDenunciaAdmitida->setTamanho(Utils::getValue('nm_arquivo', $data));
            $arquivoDenunciaAdmitida->setNomeFisico(Utils::getValue('nm_fis_arquivo', $data));
        }
        return $arquivoDenunciaAdmitida;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ArquivoDenunciaAdmissibilidade
     */
    public function setId(int $id): ArquivoDenunciaAdmissibilidade
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DenunciaAdmitida
     */
    public function getDenunciaAdmitida()
    {
        return $this->denunciaAdmitida;
    }

    /**
     * @param DenunciaAdmissibilidade $denunciaAdmitida
     * @return ArquivoDenunciaAdmissibilidade
     */
    public function setDenunciaAdmitida(DenunciaAdmissibilidade $denunciaAdmitida): ArquivoDenunciaAdmissibilidade
    {
        $this->denunciaAdmitida = $denunciaAdmitida;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNmArquivo()
    {
        return $this->nmArquivo;
    }

    /**
     * @param mixed $nmArquivo
     * @return ArquivoDenunciaAdmissibilidade
     */
    public function setNmArquivo($nmArquivo): ArquivoDenunciaAdmissibilidade
    {
        $this->nmArquivo = $nmArquivo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNmFisicoArquivo()
    {
        return $this->nmFisicoArquivo;
    }

    /**
     * @param mixed $nmFisicoArquivo
     * @return ArquivoDenunciaAdmissibilidade
     */
    public function setNmFisicoArquivo($nmFisicoArquivo): ArquivoDenunciaAdmissibilidade
    {
        $this->nmFisicoArquivo = $nmFisicoArquivo;
        return $this;
    }
}
