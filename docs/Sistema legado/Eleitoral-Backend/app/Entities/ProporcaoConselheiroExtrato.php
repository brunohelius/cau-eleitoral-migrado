<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 18/11/2019
 * Time: 11:54
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'ProporcaoConselheiroExtrato'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProporcaoConselheiroExtratoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_PROPORCAO_CONSELHEIRO_EXTRATO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ProporcaoConselheiroExtrato extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_PROPORCAO_CONSELHEIRO_EXTRATO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_hist_extrato_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="ID_CAU_UF", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $idCauUf;

    /**
     * @ORM\Column(name="NU_PROPORCAO_CONSELHEIRO", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $numeroProporcaoConselheiro;

     /**
     * @ORM\ManyToOne(targetEntity="App\Entities\HistoricoExtratoConselheiro")
     * @ORM\JoinColumn(name="id_hist_extrato", referencedColumnName="id_hist_extrato", nullable=false)
     *
     * @var \App\Entities\HistoricoExtratoConselheiro
     */
    private $historicoExtratoConselheiro;

    /**
     * Retorna uma nova instância de 'ProporcaoConselheiroExtrato'.
     *
     * @param null $data
     * @return ProporcaoConselheiroExtrato
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $proporcaoConselheiroExtrato = new ProporcaoConselheiroExtrato();

        if ($data != null) {
            $proporcaoConselheiroExtrato->setId(Utils::getValue('id', $data));
            $proporcaoConselheiroExtrato->setIdCauUf(Utils::getValue('idCauUf', $data));
            $proporcaoConselheiroExtrato->setNumeroProporcaoConselheiro(Utils::getValue('numeroProporcaoConselheiro', $data));

            $historicoExtrato = Utils::getValue('historicoExtratoConselheiro', $data);
            if (!empty($historicoExtrato)) {
                $proporcaoConselheiroExtrato->setHistoricoExtratoConselheiro(HistoricoExtratoConselheiro::newInstance(
                    $historicoExtrato
                ));
            }
        }

        return $proporcaoConselheiroExtrato;
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

    /**
     * @return int
     */
    public function getNumeroProporcaoConselheiro()
    {
        return $this->numeroProporcaoConselheiro;
    }

    /**
     * @param int $numeroProporcaoConselheiro
     */
    public function setNumeroProporcaoConselheiro($numeroProporcaoConselheiro): void
    {
        $this->numeroProporcaoConselheiro = $numeroProporcaoConselheiro;
    }

    /**
     * @return HistoricoExtratoConselheiro
     */
    public function getHistoricoExtratoConselheiro()
    {
        return $this->historicoExtratoConselheiro;
    }

    /**
     * @param HistoricoExtratoConselheiro $historicoExtratoConselheiro
     */
    public function setHistoricoExtratoConselheiro($historicoExtratoConselheiro): void
    {
        $this->historicoExtratoConselheiro = $historicoExtratoConselheiro;
    }
}