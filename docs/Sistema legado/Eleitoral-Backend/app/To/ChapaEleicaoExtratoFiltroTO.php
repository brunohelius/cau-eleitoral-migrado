<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 16/08/2019
 * Time: 15:39
 */

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada ao 'Chapa Eleicao Extrato'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ChapaEleicaoExtratoFiltroTO
{
    /**
     * @var integer|null
     */
    private $idCalendario;

    /**
     * @var integer|null
     */
    private $idChapaEleicao;

    /**
     * @var integer|null
     */
    private $idCauUf;

    /**
     * @var integer|null
     */
    private $idStatus;

    /**
     * @var integer|null
     */
    private $idTipoCandidatura;

    /**
     * Fabricação estática de 'ChapaEleicaoExtratoFiltroTO'.
     *
     * @param array|null $data
     *
     * @return ChapaEleicaoExtratoFiltroTO
     */
    public static function newInstance($data = null)
    {
        $filtroTO = new ChapaEleicaoExtratoFiltroTO();

        if ($data != null) {
            $filtroTO->setIdCauUf(Utils::getValue("idCauUf", $data));
            $filtroTO->setIdCalendario(Utils::getValue("idCalendario", $data));
            $filtroTO->setIdStatus(Utils::getValue("idStatus", $data));
            $filtroTO->setIdChapaEleicao(Utils::getValue("idChapaEleicao", $data));
            $filtroTO->setIdTipoCandidatura(Utils::getValue("idTipoCandidatura", $data));
        }

        return $filtroTO;
    }

    /**
     * @return int|null
     */
    public function getIdCalendario(): ?int
    {
        return $this->idCalendario;
    }

    /**
     * @param int|null $idCalendario
     */
    public function setIdCalendario(?int $idCalendario): void
    {
        $this->idCalendario = $idCalendario;
    }

    /**
     * @return int|null
     */
    public function getIdChapaEleicao(): ?int
    {
        return $this->idChapaEleicao;
    }

    /**
     * @param int|null $idChapaEleicao
     */
    public function setIdChapaEleicao(?int $idChapaEleicao): void
    {
        $this->idChapaEleicao = $idChapaEleicao;
    }

    /**
     * @return int|null
     */
    public function getIdCauUf(): ?int
    {
        return $this->idCauUf;
    }

    /**
     * @param int|null $idCauUf
     */
    public function setIdCauUf(?int $idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return int|null
     */
    public function getIdStatus(): ?int
    {
        return $this->idStatus;
    }

    /**
     * @param int|null $idStatus
     */
    public function setIdStatus(?int $idStatus): void
    {
        $this->idStatus = $idStatus;
    }

    /**
     * @return int|null
     */
    public function getIdTipoCandidatura(): ?int
    {
        return $this->idTipoCandidatura;
    }

    /**
     * @param int|null $idTipoCandidatura
     */
    public function setIdTipoCandidatura(?int $idTipoCandidatura): void
    {
        $this->idTipoCandidatura = $idTipoCandidatura;
    }
}