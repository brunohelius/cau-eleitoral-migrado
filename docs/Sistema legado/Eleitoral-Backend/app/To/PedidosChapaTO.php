<?php


namespace App\To;

use Illuminate\Support\Arr;

/**
 * Classe de transferência para Pedidos Solicitados Chapa
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class PedidosChapaTO
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var PedidoSolicitadoTO[]|null
     */
    private $pedidosSubstituicao;

    /**
     * @var PedidoSolicitadoTO[]|null
     */
    private $pedidosImpugnacao;

    /**
     * @var PedidoSolicitadoTO[]|null
     */
    private $pedidosDenuncia;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return PedidoSolicitadoTO[]|null
     */
    public function getPedidosSubstituicao(): ?array
    {
        return $this->pedidosSubstituicao;
    }

    /**
     * @param PedidoSolicitadoTO[]|null $pedidosSubstituicao
     */
    public function setPedidosSubstituicao(?array $pedidosSubstituicao): void
    {
        $this->pedidosSubstituicao = $pedidosSubstituicao;
    }

    /**
     * @return PedidoSolicitadoTO[]|null
     */
    public function getPedidosImpugnacao(): ?array
    {
        return $this->pedidosImpugnacao;
    }

    /**
     * @param PedidoSolicitadoTO[]|null $pedidosImpugnacao
     */
    public function setPedidosImpugnacao(?array $pedidosImpugnacao): void
    {
        $this->pedidosImpugnacao = $pedidosImpugnacao;
    }

    /**
     * @return PedidoSolicitadoTO[]|null
     */
    public function getPedidosDenuncia(): ?array
    {
        return $this->pedidosDenuncia;
    }

    /**
     * @param PedidoSolicitadoTO[]|null $pedidosDenuncia
     */
    public function setPedidosDenuncia(?array $pedidosDenuncia): void
    {
        $this->pedidosDenuncia = $pedidosDenuncia;
    }

    /**
     * Retorna uma nova instância de 'PedidosChapaTO'.
     *
     * @param null $data
     * @return PedidosChapaTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $pedidosChapaTO = new PedidosChapaTO();

        if ($data != null) {
            $pedidosChapaTO->setId(Arr::get($data, 'id'));
            $pedidosChapaTO->setPedidosDenuncia([]);
            $pedidosChapaTO->setPedidosImpugnacao([]);
            $pedidosChapaTO->setPedidosSubstituicao([]);
        }

        return $pedidosChapaTO;
    }

}
