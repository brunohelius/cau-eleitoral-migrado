<?php

namespace App\Entities;

use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Chapa Eleição Status'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ChapaEleicaoStatusRepository")
 * @ORM\Table(schema="eleitoral", name="TB_CHAPA_ELEICAO_STATUS")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ChapaEleicaoStatus extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_CHAPA_ELEICAO_STATUS", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_chapa_eleicao_status_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DT_CHAPA_ELEICAO_STATUS", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\StatusChapa")
     * @ORM\JoinColumn(name="ID_STATUS_CHAPA", referencedColumnName="ID_STATUS_CHAPA")
     *
     * @var \App\Entities\StatusChapa
     */
    private $statusChapa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoAlteracao")
     * @ORM\JoinColumn(name="ID_TP_ALTERACAO", referencedColumnName="ID_TP_ALTERACAO")
     *
     * @var TipoAlteracao
     */
    private $tipoAlteracao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\ChapaEleicao")
     * @ORM\JoinColumn(name="ID_CHAPA_ELEICAO", referencedColumnName="ID_CHAPA_ELEICAO")
     *
     * @var \App\Entities\ChapaEleicao
     */
    private $chapaEleicao;

    /**
     * Fábrica de instância de 'Chapa Eleição Status'.
     *
     * @param array $data
     * @return \App\Entities\ChapaEleicaoStatus
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setData(Utils::getValue('data', $data));

            $statusChapa = Utils::getValue('statusChapa', $data);
            if (! empty($statusChapa)) {
                $instance->setStatusChapa(StatusChapa::newInstance($statusChapa));
            }

            $tipoAlteracao = Utils::getValue('tipoAlteracao', $data);
            if (! empty($tipoAlteracao)) {
                $instance->setTipoAlteracao(TipoAlteracao::newInstance($tipoAlteracao));
            }

            $chapaEleicao = Utils::getValue('chapaEleicao', $data);
            if (! empty($statusChapa)) {
                $instance->setChapaEleicao(ChapaEleicao::newInstance($chapaEleicao));
            }
        }

        return $instance;
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
     * @return DateTime
     */
    public function getData(): DateTime
    {
        return $this->data;
    }

    /**
     * @param DateTime $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return StatusChapa
     */
    public function getStatusChapa(): StatusChapa
    {
        return $this->statusChapa;
    }

    /**
     * @param StatusChapa $statusChapa
     */
    public function setStatusChapa(StatusChapa $statusChapa): void
    {
        $this->statusChapa = $statusChapa;
    }

    /**
     * @return TipoAlteracao
     */
    public function getTipoAlteracao()
    {
        return $this->tipoAlteracao;
    }

    /**
     * @param TipoAlteracao $tipoAlteracao
     */
    public function setTipoAlteracao($tipoAlteracao): void
    {
        $this->tipoAlteracao = $tipoAlteracao;
    }

    /**
     * @return ChapaEleicao
     */
    public function getChapaEleicao(): ChapaEleicao
    {
        return $this->chapaEleicao;
    }

    /**
     * @param ChapaEleicao $chapaEleicao
     */
    public function setChapaEleicao(ChapaEleicao $chapaEleicao): void
    {
        $this->chapaEleicao = $chapaEleicao;
    }
}
