<?php

namespace App\To;

use App\Config\Constants;
use App\Util\Utils;

/**
 * Classe de transferência TO para quantidade de pedidos de impugnaçao
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class QuantidadePedidoImpugnacaoPorUfTO
{
    /**
     * @var integer
     */
    private $idCauUf;

    /**
     * @var string
     */
    private $siglaUf;

    /**
     * @var integer
     */
    private $quantidadePedidosEmAnalise;

    /**
     * @return int
     */
    public function getIdCauUf(): int
    {
        return $this->idCauUf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf(int $idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return string
     */
    public function getSiglaUf(): ?string
    {
        return $this->siglaUf;
    }

    /**
     * @param string $siglaUf
     */
    public function setSiglaUf(?string $siglaUf): void
    {
        $this->siglaUf = $siglaUf;
    }

    /**
     * @return int
     */
    public function getQuantidadePedidosEmAnalise(): ?int
    {
        return $this->quantidadePedidosEmAnalise;
    }

    /**
     * @param int $quantidadePedidosEmAnalise
     */
    public function setQuantidadePedidosEmAnalise(?int $quantidadePedidosEmAnalise): void
    {
        $this->quantidadePedidosEmAnalise = $quantidadePedidosEmAnalise;
    }

    /**
     * Fabricação estática de 'QuantidadePedidoImpugnacaoPorUfTO'.
     *
     * @param array|null $data
     *
     * @param int $tipoCandidatura
     * @return QuantidadePedidoImpugnacaoPorUfTO
     */
    public static function newInstance($data = null, int $tipoCandidatura = null)
    {
        $instance = new self();

        if ($data != null) {

            if ($tipoCandidatura == 2) {
                $instance->setIdCauUf(0);
                $instance->setSiglaUf(Constants::PREFIXO_IES);
            } else {
                $instance->setIdCauUf(Utils::getValue("idCauUf", $data));
                $instance->setSiglaUf(Utils::getValue("siglaUf", $data));
            }
            $instance->setQuantidadePedidosEmAnalise(Utils::getValue("quantidadePedidosEmAnalise", $data));
        }

        return $instance;
    }

    /**
     * Comparaçao dos objetos
     *
     * @param $other QuantidadePedidoSubstituicaoPorUfTO
     * @return bool
     */
    public function compareTo($other)
    {
        return $this->getIdCauUf() === $other->getIdCauUf();
    }
}
