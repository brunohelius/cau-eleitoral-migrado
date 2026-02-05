<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 29/08/2019
 * Time: 15:39
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de UFs de Calendario.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UfCalendarioRepository")
 * @ORM\Table(schema="eleitoral", name="TB_CAU_UF_CALENDARIO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class UfCalendario extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_CAU_UF_CALENDARIO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_CAU_UF_CALENDARIO_ID_SEQ", initialValue=1, allocationSize=1)
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
     * @ORM\Column(name="ID_CAU_UF", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $idCauUf;

    /**
     * Fábrica de instância de 'Calendario'.
     *
     * @param array $data
     * @return UfCalendario
     */
    public static function newInstance($data = null)
    {
        $ufCalendario = new UfCalendario();

        if ($data != null) {
            $ufCalendario->setId(Utils::getValue('id', $data));
            $ufCalendario->setIdCauUf(Utils::getValue('idCauUf', $data));
            $ufCalendario->setCalendario(Utils::getValue('calendario', $data));
        }
        return $ufCalendario;
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
     * @return int
     */
    public function getIdCauUf()
    {
        return $this->idCauUf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf($idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }
}