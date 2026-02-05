<?php
/*
 * DenunciaChapa.php
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
 * Entidade de representação de 'Denuncia Chapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DenunciaChapaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DENUNCIA_CHAPA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DenunciaChapa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DENUNCIA_CHAPA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_denuncia_chapa_id_seq", initialValue=1, allocationSize=1)
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
     * @ORM\ManyToOne(targetEntity="App\Entities\ChapaEleicao")
     * @ORM\JoinColumn(name="ID_CHAPA_ELEICAO", referencedColumnName="ID_CHAPA_ELEICAO", nullable=false)
     *
     * @var \App\Entities\ChapaEleicao
     */
    private $chapaEleicao;

    /**
     * Fábrica de instância de Denuncia Chapa.
     *
     * @param array $data
     * @return \App\Entities\DenunciaChapa
     */
    public static function newInstance($data = null)
    {
        $denunciaChapa = new DenunciaChapa();

        if ($data != null) {
            $denunciaChapa->setId(Utils::getValue('id', $data));

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $denunciaChapa->setDenuncia(Denuncia::newInstance($denuncia));
            }

            $chapaEleicao = Utils::getValue('chapaEleicao', $data);
            if (!empty($chapaEleicao)) {
                $denunciaChapa->setChapaEleicao(ChapaEleicao::newInstance($chapaEleicao));
            }
        }
        return $denunciaChapa;
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
     * @return  ChapaEleicao
     */
    public function getChapaEleicao()
    {
        return $this->chapaEleicao;
    }

    /**
     * @param ChapaEleicao  $chapaEleicao
     */
    public function setChapaEleicao(ChapaEleicao $chapaEleicao)
    {
        $this->chapaEleicao = $chapaEleicao;
    }
}
