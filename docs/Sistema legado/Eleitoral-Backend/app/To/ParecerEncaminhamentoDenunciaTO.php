<?php

namespace App\To;

use App\Entities\DenunciaDefesa;
use App\Entities\EncaminhamentoDenuncia;
use App\Util\Utils;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao parecer da denúncia
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 */
class ParecerEncaminhamentoDenunciaTO
{
    /**
     * @var integer
     */
    private $idDenuncia;

    /**
     * @var integer
     */
    private $numeroDenuncia;

    /**
     * @var ListagemEncaminhamentoDenunciaTO[]
     */
    private $encaminhamentos;

    /**
     * Retorna uma nova instância de 'DenunciaParecerTO'.
     *
     * @param $data
     * @return self
     */
    public static function newInstance($data = null)
    {
        $parecerEncaminhamentoDenunciaTO = new self;

        if ($data != null) {
            $parecerEncaminhamentoDenunciaTO->setIdDenuncia(Utils::getValue("idDenuncia", $data));
            $parecerEncaminhamentoDenunciaTO->setNumeroDenuncia(Utils::getValue("numeroDenuncia", $data));
            $parecerEncaminhamentoDenunciaTO->setEncaminhamentos(Utils::getValue("encaminhamentos", $data));
        }

        return $parecerEncaminhamentoDenunciaTO;
    }

    /**
     * @return int
     */
    public function getIdDenuncia(): int
    {
        return $this->idDenuncia;
    }

    /**
     * @param int $idDenuncia
     */
    public function setIdDenuncia(int $idDenuncia): void
    {
        $this->idDenuncia = $idDenuncia;
    }

    /**
     * @return int
     */
    public function getNumeroDenuncia(): ?int
    {
        return $this->numeroDenuncia;
    }

    /**
     * @param int $numeroDenuncia
     */
    public function setNumeroDenuncia(?int $numeroDenuncia): void
    {
        $this->numeroDenuncia = $numeroDenuncia;
    }

    /**
     * @return ListagemEncaminhamentoDenunciaTO[]
     */
    public function getEncaminhamentos(): ?array
    {
        return $this->encaminhamentos;
    }

    /**
     * @param ListagemEncaminhamentoDenunciaTO[] $encaminhamentos
     */
    public function setEncaminhamentos(?array $encaminhamentos): void
    {
        $this->encaminhamentos = $encaminhamentos;
    }


}
