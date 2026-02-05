<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\Filial;
use App\Util\Utils;
use Illuminate\Support\Arr;

/**
 * Classe de transferência TO para quantidade de pedidos de impugnacao de resultado
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class QuantidadePedidoImpugnacaoResultadoPorUfTO
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
    private $idCalendario;

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
    public function getIdCalendario(): int
    {
        return $this->idCalendario;
    }

    /**
     * @param int $idCalendario
     */
    public function setIdCalendario(int $idCalendario): void
    {
        $this->idCalendario = $idCalendario;
    }

    /**
     * Fabricação estática de 'QuantidadePedidoSubstituicaoPorUfTO'.
     *
     * @param array|null $data
     *
     * @return QuantidadePedidoImpugnacaoResultadoPorUfTO
     */
    public static function newInstance($data)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setIdCauUf(Arr::get($data,"idCauUf"));
            $descricao = sprintf('%s/%s', Constants::PREFIXO_CONSELHO_ELEITORAL,  Utils::getValue("siglaUf", $data));
            $instance->setSiglaUf($descricao);
            $instance->setQuantidadePedidos(Arr::get($data,"quantidadePedidos"));
            $instance->setIdCalendario(Arr::get($data,"idCalendario"));
        }

        return $instance;
    }

    /**
     * Fabricação estática de 'QuantidadePedidoSubstituicaoPorUfTO' com as filiais
     *
     * @param array|null $data
     *
     * @return QuantidadePedidoImpugnacaoResultadoPorUfTO
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
        }

        return $instance;
    }

}
