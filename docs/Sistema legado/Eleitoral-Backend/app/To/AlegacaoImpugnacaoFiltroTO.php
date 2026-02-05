<?php

namespace App\To;

use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Alegação de impugnação de Resultado.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class AlegacaoImpugnacaoFiltroTO
{
    /**
     * @var array
     */
    private $idCalendarios;

    /**
     * @var array
     */
    private $idProfissionais;

    /**
     * @var array
     */
    private $idImpugnacoes;

    /**
     * @return array
     */
    public function getIdCalendarios(): ? array
    {
        return $this->idCalendarios;
    }

    /**
     * @param array $idCalendarios
     */
    public function setIdCalendarios( $idCalendarios): void
    {
        $this->idCalendarios = $idCalendarios;
    }

    /**
     * @return array
     */
    public function getIdProfissionais(): ? array
    {
        return $this->idProfissionais;
    }

    /**
     * @param array $idProfissionais
     */
    public function setIdProfissionais($idProfissionais): void
    {
        $this->idProfissionais = $idProfissionais;
    }

    /**
     * @return array
     */
    public function getIdImpugnacoes()
    {
        return $this->idImpugnacoes;
    }

    /**
     * @param array $idImpugnacoes
     */
    public function setIdImpugnacoes($idImpugnacoes): void
    {
        $this->idImpugnacoes = $idImpugnacoes;
    }



    /**
     * Retorna uma nova instância de 'AlegacaoImpugnacaoFiltroTO'.
     *
     * @param null $data
     * @return AlegacaoImpugnacaoFiltroTO
     */
    public static function newInstance($data = null)
    {
        $alegacaoImpugnacaoFiltroTO = new AlegacaoImpugnacaoFiltroTO();

        if ($data != null) {
            $alegacaoImpugnacaoFiltroTO->setIdCalendarios(Arr::get($data, 'idCalendarios'));
            $alegacaoImpugnacaoFiltroTO->setIdProfissionais(Arr::get($data, 'idProfissionais'));
            $alegacaoImpugnacaoFiltroTO->setIdImpugnacoes(Arr::get($data, 'idImpugnacoes'));
        }

        return $alegacaoImpugnacaoFiltroTO;
    }

}