<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\Filial;
use App\Util\Utils;

/**
 * Classe de transferência TO para quantidade de pedidos de substiuiçao
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class QuantidadePedidoSubstituicaoPorUfTO
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
    private $quantidadePedidos;

    /**
     * @var integer
     */
    private $quantidadePedidosJulgados;

    /**
     * @return int
     */
    public function getIdCauUf(): ?int
    {
        return $this->idCauUf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf(?int $idCauUf): void
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
    public function getQuantidadePedidos(): ?int
    {
        return $this->quantidadePedidos;
    }

    /**
     * @param int $quantidadePedidos
     */
    public function setQuantidadePedidos(?int $quantidadePedidos): void
    {
        $this->quantidadePedidos = $quantidadePedidos;
    }


    /**
     * @return int
     */
    public function getQuantidadePedidosJulgados(): ?int
    {
        return $this->quantidadePedidosJulgados;
    }

    /**
     * @param int $quantidadePedidosJulgados
     */
    public function setQuantidadePedidosJulgados(?int $quantidadePedidosJulgados): void
    {
        $this->quantidadePedidosJulgados = $quantidadePedidosJulgados;
    }

    /**
     * Fabricação estática de 'QuantidadePedidoSubstituicaoPorUfTO'.
     *
     * @param array|null $data
     *
     * @param int $tipoCandidatura
     * @return QuantidadePedidoSubstituicaoPorUfTO
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
            $instance->setQuantidadePedidos(Utils::getValue("quantidadePedidos", $data));
            $instance->setQuantidadePedidosJulgados(Utils::getValue("quantidadePedidosJulgados", $data));
        }

        return $instance;
    }

    /**
     * Fabricação estática de 'QuantidadePedidoSubstituicaoPorUfTO' com as filiais
     *
     * @param array|null $data
     *
     * @return QuantidadePedidoSubstituicaoPorUfTO
     */
    public static function newInstanceFromFilial($data = null)
    {
        $instance = new self();

        if ($data != null) {
            if($data instanceof Filial){
                $instance->setIdCauUf($data->getId());
                $descricao = sprintf('%s/%s', Constants::PREFIXO_CONSELHO_ELEITORAL,  $data->getPrefixo());
                $instance->setSiglaUf($descricao);
            } else {
                $descricao = sprintf('%s/%s', Constants::PREFIXO_CONSELHO_ELEITORAL,  $data->prefixo);
                $instance->setIdCauUf($data->id);
                $instance->setSiglaUf($descricao);
            }
            $instance->setQuantidadePedidos(0);
            $instance->setQuantidadePedidosJulgados(0);
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

    /**
     * @return QuantidadePedidoSubstituicaoPorUfTO
     */
    public static function criaQuantidadePedidoIES(): QuantidadePedidoSubstituicaoPorUfTO
    {
        $pedidoIES = new self();
        $pedidoIES->setIdCauUf(0);
        $pedidoIES->setSiglaUf(Constants::PREFIXO_IES);
        $pedidoIES->setQuantidadePedidos(0);
        $pedidoIES->setQuantidadePedidosJulgados(0);
        return $pedidoIES;
    }

}
