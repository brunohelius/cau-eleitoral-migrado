<?php

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada aos filtro para consulta de membro substituto relacionado a entidade 'SubstituicaoImpugnacao'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class SubstituicaoImpugnacaoFiltroTO
{

    /**
     * @var integer|null
     */
    private $idPedidoImpugnacao;

    /**
     * @var integer|null
     */
    private $idProfissional;

    /**
     * Retorna uma nova instância de 'TipoProcessoTO'.
     *
     * @param null $data
     * @return SubstituicaoImpugnacaoFiltroTO
     */
    public static function newInstance($data = null)
    {
        $substituicaoImpugnacaoFiltroTO = new SubstituicaoImpugnacaoFiltroTO();

        if ($data != null) {
            $substituicaoImpugnacaoFiltroTO->setIdProfissional(Utils::getValue('idProfissional', $data));
            $substituicaoImpugnacaoFiltroTO->setIdPedidoImpugnacao(Utils::getValue('idPedidoImpugnacao', $data));
        }

        return $substituicaoImpugnacaoFiltroTO;
    }

    /**
     * @return int|null
     */
    public function getIdPedidoImpugnacao(): ?int
    {
        return $this->idPedidoImpugnacao;
    }

    /**
     * @param int|null $idPedidoImpugnacao
     */
    public function setIdPedidoImpugnacao(?int $idPedidoImpugnacao): void
    {
        $this->idPedidoImpugnacao = $idPedidoImpugnacao;
    }

    /**
     * @return int|null
     */
    public function getIdProfissional(): ?int
    {
        return $this->idProfissional;
    }

    /**
     * @param int|null $idProfissional
     */
    public function setIdProfissional(?int $idProfissional): void
    {
        $this->idProfissional = $idProfissional;
    }
}
