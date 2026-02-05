<?php
/*
 * ImpedimentoSuspeicao.php
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
 * Entidade de representação de 'ImpedimentoSuspeicao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ImpedimentoSuspeicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_IMPEDIMENTO_SUSPEICAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ImpedimentoSuspeicao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_IMPEDIMENTO_SUSPEICAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_impedimento_suspeicao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\DenunciaAdmitida")
     * @ORM\JoinColumn(name="ID_DENUNCIA_ADMITIDA", referencedColumnName="ID_DENUNCIA_ADMITIDA")
     * @var DenunciaAdmitida
     */
    private $denunciaAdmitida;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\EncaminhamentoDenuncia")
     * @ORM\JoinColumn(name="ID_ENCAMINHAMENTO_DENUNCIA", referencedColumnName="ID_ENCAMINHAMENTO_DENUNCIA")
     * @var EncaminhamentoDenuncia
     */
    private $encaminhamentoDenuncia;

    /**
     * Fábrica de instância de 'ImpedimentoSuspeicao'.
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $impedimento = new ImpedimentoSuspeicao();

        if ($data != null) {
            $impedimento->setId(Utils::getValue('id', $data));

            $denunciaAdmitida = Utils::getValue('denunciaAdmitida', $data);
            if (!empty($denunciaAdmitida)) {
                $impedimento->setDenunciaAdmitida(DenunciaAdmitida::newInstance($denunciaAdmitida));
            }

            $encaminhamentoDenuncia = Utils::getValue('encaminhamentoDenuncia', $data);
            if (!empty($encaminhamentoDenuncia)) {
                $impedimento->setEncaminhamentoDenuncia(EncaminhamentoDenuncia::newInstance($encaminhamentoDenuncia));
            }
        }
        return $impedimento;
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
     * @return DenunciaAdmitida
     */
    public function getDenunciaAdmitida(): DenunciaAdmitida
    {
        return $this->denunciaAdmitida;
    }

    /**
     * @param DenunciaAdmitida $denunciaAdmitida
     */
    public function setDenunciaAdmitida(DenunciaAdmitida $denunciaAdmitida)
    {
        $this->denunciaAdmitida = $denunciaAdmitida;
    }

    /**
     * @return EncaminhamentoDenuncia
     */
    public function getIdEncaminhamentoDenuncia(): EncaminhamentoDenuncia
    {
        return $this->encaminhamentoDenuncia;
    }

    /**
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     * @return void
     */
    public function setEncaminhamentoDenuncia(EncaminhamentoDenuncia $encaminhamentoDenuncia)
    {
        $this->encaminhamentoDenuncia = $encaminhamentoDenuncia;
    }
}
