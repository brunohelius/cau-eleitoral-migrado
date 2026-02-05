<?php


namespace App\To;

use Illuminate\Support\Arr;

/**
 * Classe de transferência para a visualizaçao do pedido de impugnaçao
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ImpugnantePedidoImpugnacaoTO
{
    /**
     * @var string
     */
    private $nome;

    /**
     * @var string
     */
    private $registro;

    /**
     * @return string
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome(?string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return string
     */
    public function getRegistro(): ?string
    {
        return $this->registro;
    }

    /**
     * @param string $registro
     */
    public function setRegistro(?string $registro): void
    {
        $this->registro = $registro;
    }

    /**
     * Retorna uma nova instância de 'ImpugnantePedidoImpugnacaoTO'.
     *
     * @param null $data
     * @return ImpugnantePedidoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $impugnantePedidoImpugnacaoTO = new ImpugnantePedidoImpugnacaoTO();

        if ($data != null) {
            $impugnantePedidoImpugnacaoTO->setNome(Arr::get($data, 'profissional.nome'));
            $impugnantePedidoImpugnacaoTO->setRegistro(Arr::get($data, 'profissional.registroNacional'));
        }

        return $impugnantePedidoImpugnacaoTO;
    }

}
