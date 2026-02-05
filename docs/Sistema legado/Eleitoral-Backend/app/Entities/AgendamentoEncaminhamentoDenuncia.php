<?php
/*
 * AgendamentoEncaminhamentoDenunciaRepository.php
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
 * Entidade de representação de 'AgendamentoEncaminhamentoDenuncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\AgendamentoEncaminhamentoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_AGENDAMENTO_ENCAMINHAMENTO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class AgendamentoEncaminhamentoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_AGENDAMENTO_ENCAMINHAMENTO_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_agendamento_encaminhamento_denuncia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DT_AGENDAMENTO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\EncaminhamentoDenuncia")
     * @ORM\JoinColumn(name="ID_ENCAMINHAMENTO_DENUNCIA", referencedColumnName="ID_ENCAMINHAMENTO_DENUNCIA", nullable=false)
     * @var \App\Entities\EncaminhamentoDenuncia
     */
    private $encaminhamentoDenuncia;

    /**
     * Fábrica de instância de 'AgendamentoEncaminhamentoDenuncia'.
     *
     * @param null $data
     * @return AgendamentoEncaminhamentoDenuncia
     */
    public static function newInstance($data = null)
    {
        $agendamento = new AgendamentoEncaminhamentoDenuncia();

        if ($data != null) {
            $agendamento->setId(Utils::getValue('id', $data));
            $agendamento->setData(Utils::getValue('data', $data));

            $encaminhamentoDenuncia = Utils::getValue('encaminhamentoDenuncia', $data);
            if (!empty($encaminhamentoDenuncia)) {
                $agendamento->setEncaminhamentoDenuncia(EncaminhamentoDenuncia::newInstance($encaminhamentoDenuncia));
            }
        }
        return $agendamento;
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
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \DateTime $data
     */
    public function setData($data): void
    {
        if (is_string($data)) {
            $data = new \DateTime($data);
        }
        $this->data = $data;
    }

    /**
     * @return EncaminhamentoDenuncia
     */
    public function getEncaminhamentoDenuncia(): EncaminhamentoDenuncia
    {
        return $this->encaminhamentoDenuncia;
    }

    /**
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     */
    public function setEncaminhamentoDenuncia(EncaminhamentoDenuncia $encaminhamentoDenuncia): void
    {
        $this->encaminhamentoDenuncia = $encaminhamentoDenuncia;
    }
}