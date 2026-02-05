<?php
/*
 * CalendarioSituacao.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Calendário Situação'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\CalendarioSituacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_CALENDARIO_SITUACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class CalendarioSituacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_CALENDARIO_SITUACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_calendario_situacao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Calendario")
     * @ORM\JoinColumn(name="ID_CALENDARIO", referencedColumnName="ID_CALENDARIO", nullable=false)
     *
     * @var \App\Entities\Calendario
     */
    private $calendario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\SituacaoCalendario")
     * @ORM\JoinColumn(name="ID_SITUACAO_CALENDARIO", referencedColumnName="ID_SITUACAO_CALENDARIO", nullable=false)
     *
     * @var \App\Entities\SituacaoCalendario
     */
    private $situacaoCalendario;

    /**
     * @ORM\Column(name="DT_INI", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     * @ORM\OrderBy({"data" = "DESC"})
     * @var DateTime
     */
    private $data;

    /**
     * Fábrica de instância de Calendário Situação.
     *
     * @param array $data
     * @return \App\Entities\CalendarioSituacao
     */
    public static function newInstance($data = null)
    {
        $calendarioSituacao = new CalendarioSituacao();

        if ($data != null) {
            $calendarioSituacao->setId(Utils::getValue('id', $data));
            $situacaoCalendario =  SituacaoCalendario::newInstance(Utils::getValue('situacaoCalendario', $data));
            $calendarioSituacao->setSituacaoCalendario($situacaoCalendario);
            $calendarioSituacao->setCalendario(Utils::getValue('calendario', $data));
            $calendarioSituacao->setData(Utils::getValue('data', $data));
        }
        return $calendarioSituacao;
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
     * @return Calendario
     */
    public function getCalendario()
    {
        return $this->calendario;
    }

    /**
     * @param Calendario $calendario
     */
    public function setCalendario($calendario): void
    {
        $this->calendario = $calendario;
    }

    /**
     * @return SituacaoCalendario
     */
    public function getSituacaoCalendario()
    {
        return $this->situacaoCalendario;
    }

    /**
     * @param SituacaoCalendario $situacaoCalendario
     */
    public function setSituacaoCalendario($situacaoCalendario): void
    {
        $this->situacaoCalendario = $situacaoCalendario;
    }

    /**
     * @return DateTime
     */
    public function getData()
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
}