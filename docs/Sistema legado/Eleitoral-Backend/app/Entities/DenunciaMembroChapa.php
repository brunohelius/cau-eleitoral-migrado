<?php
/*
 * DenunciaMembroChapa.php
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
 * Entidade de representação de 'Denuncia Membro Chapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DenunciaMembroChapaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DENUNCIA_MEMBRO_CHAPA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DenunciaMembroChapa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DENUNCIA_MEMBRO_CHAPA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_denuncia_membro_chapa_id_seq", initialValue=1, allocationSize=1)
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
     * @ORM\ManyToOne(targetEntity="App\Entities\MembroChapa")
     * @ORM\JoinColumn(name="ID_MEMBRO_CHAPA", referencedColumnName="ID_MEMBRO_CHAPA", nullable=false)
     *
     * @var \App\Entities\MembroChapa
     */
    private $membroChapa;

    /**
     * Fábrica de instância de Denuncia Membro Chapa'.
     *
     * @param array $data
     * @return \App\Entities\DenunciaMembroChapa
     */
    public static function newInstance($data = null)
    {
        $denunciaMembroChapa = new DenunciaMembroChapa();

        if ($data != null) {
            $denunciaMembroChapa->setId(Utils::getValue('id', $data));

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $denunciaMembroChapa->setDenuncia(Denuncia::newInstance($denuncia));
            }

            $membroChapa = Utils::getValue('membroChapa', $data);
            if (!empty($membroChapa)) {
                $denunciaMembroChapa->setMembroChapa(MembroChapa::newInstance($membroChapa));
            }
        }
        return $denunciaMembroChapa;
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
     * @return  MembroChapa
     */ 
    public function getMembroChapa()
    {
        return $this->membroChapa;
    }

    /**
     * @param MembroChapa  $membroChapa
     */ 
    public function setMembroChapa(MembroChapa $membroChapa)
    {
        $this->membroChapa = $membroChapa;
    }
}
?>