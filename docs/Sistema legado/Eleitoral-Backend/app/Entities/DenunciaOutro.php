<?php
/*
 * DenunciaOutro.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Denuncia Outro'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DenunciaOutroRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DENUNCIA_OUTRO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DenunciaOutro extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DENUNCIA_OUTRO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_denuncia_outro_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\Denuncia
     */
    private $denuncia;

    /**
     * @ORM\Column(name="ID_CAU_UF", type="integer", length=11, nullable=true)
     *
     * @var integer
     */
    private $idCauUf;

    /**
     * Fábrica de instância de Denuncia Outro'.
     *
     * @param array $data
     * @return \App\Entities\DenunciaOutro
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $denunciaOutro = new DenunciaOutro();

        if ($data != null) {
            $denunciaOutro->setId(Utils::getValue('id', $data));
            $denunciaOutro->setIdCauUf(Utils::getValue('idCauUf', $data));

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $denunciaOutro->setDenuncia(Denuncia::newInstance($denuncia));
            }
        }
        return $denunciaOutro;
    }

    /**
     * @return  integer
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  integer  $id
     */ 
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Denuncia
     */ 
    public function getDenuncia()
    {
        return $this->denuncia;
    }

    /**
     * @param Denuncia  $denuncia
     */ 
    public function setDenuncia(Denuncia $denuncia)
    {
        $this->denuncia = $denuncia;
    }

    /**
     * @return  integer
     */ 
    public function getIdCauUf()
    {
        return $this->idCauUf;
    }

    /**
     * @param  integer  $idCauUf
     */ 
    public function setIdCauUf($idCauUf)
    {
        $this->idCauUf = $idCauUf;
    }
}
