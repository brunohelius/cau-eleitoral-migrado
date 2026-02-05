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
 * Classe de transferência associada ao 'Calendario'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class CalendarioFiltroTO
{
    /**
     * ID do tipo de processo
     * @var integer
     */
    private $idTipoProcesso;

    /**
     * Anos do Calendário
     * @var array
     */
    private $anos;

    /**
     * Sequências do Calendário
     * @var array
     */
    private $idsCalendariosEleicao;
    
    /**
     * Situação do calendário
     * @var array of integer
     */
    private $situacoes;
    
    /**
     * Data de início de atividade secundaria
     * @var String
     */
    private $dataInicioAtividadeSecundaria;
    
    /**
     * Data de fim de atividade secundaria
     * @var String
     */
    private $dataFimAtividadeSecundaria;

    /**
     * Condicional para listagem de chapas.
     * @var int
     */
    private $listaChapas;

    /**
     * Condicional para listagem de pedidos substituiões chapa.
     * @var boolean
     */
    private $listaPedidosSubstituicaoChapa;

    /**
     * Condicional para listagem de pedidos substituiões chapa.
     * @var boolean
     */
    private $listaPedidosImpugnacao;

    /**
     * Verifica se é um filtro de acompanhar denuncias
     * @var boolean|null
     */
    private $listaDenuncias;

    /**
     * Condicional para listagem de pedidos impugnacao resultado
     * @var boolean
     */
    private $listaPedidosImpugnacaoResultado;

    /**
     * Fabricação estática de 'CalendarioFiltroTO'.
     *
     * @param array|null $data
     *
     * @return CalendarioFiltroTO
     */
    public static function newInstance($data = null)
    {
        $filtroTO = new CalendarioFiltroTO();

        if ($data != null) {
            $filtroTO->setAnos(Utils::getValue("anos", $data));
            $filtroTO->setSituacoes(Utils::getValue("situacoes", $data));
            $filtroTO->setIdTipoProcesso(Utils::getValue("idTipoProcesso", $data));
            $filtroTO->setIdsCalendariosEleicao(Utils::getValue("eleicoes", $data));
            $filtroTO->setListaPedidosImpugnacao(Utils::getValue('listaPedidosImpugnacao', $data));
            $filtroTO->setDataFimAtividadeSecundaria(Utils::getValue("dataFimAtividadeSecundaria", $data));
            $filtroTO->setDataInicioAtividadeSecundaria(Utils::getValue("dataInicioAtividadeSecundaria", $data));
            $filtroTO->setListaPedidosSubstituicaoChapa(Utils::getValue('listaPedidosSubstituicaoChapa', $data));
            $filtroTO->setListaPedidosImpugnacaoResultado(Utils::getValue('listaPedidosImpugnacaoResultado', $data));
            $filtroTO->setListaDenuncias(Utils::getValue('listaDenuncias', $data));
        }

        return $filtroTO;
    }

    /**
     * @return int
     */
    public function getIdTipoProcesso()
    {
        return $this->idTipoProcesso;
    }

    /**
     * @param int $idTipoProcesso
     */
    public function setIdTipoProcesso($idTipoProcesso)
    {
        $this->idTipoProcesso = $idTipoProcesso;
    }

    /**
     * @return array
     */
    public function getAnos()
    {
        return $this->anos;
    }

    /**
     * @param array $anos
     */
    public function setAnos($anos)
    {
        $this->anos = $anos;
    }

    /**
     * @return array
     */
    public function getIdsCalendariosEleicao()
    {
        return $this->idsCalendariosEleicao;
    }

    /**
     * @param array $idsCalendariosEleicao
     */
    public function setIdsCalendariosEleicao($idsCalendariosEleicao)
    {
        $this->idsCalendariosEleicao = $idsCalendariosEleicao;
    }
    
    /**
     * @return array
     */
    public function getSituacoes(){
        return $this->situacoes;
    }
    
    /**
     * @param array $status
     */
    public function setSituacoes($situacao){
        $this->situacoes = $situacao;
    }
    
    /**
     * @return string
     */
    public function getDataInicioAtividadeSecundaria()
    {
        return $this->dataInicioAtividadeSecundaria;
    }
    
    /**
     * @return string
     */
    public function getDataFimAtividadeSecundaria()
    {
        return $this->dataFimAtividadeSecundaria;
    }
    
    /**
     * @param string $dataInicioAtividadeSecundaria
     */
    public function setDataInicioAtividadeSecundaria($dataInicioAtividadeSecundaria)
    {
        $this->dataInicioAtividadeSecundaria = $dataInicioAtividadeSecundaria;
    }
    
    /**
     * @param string $dataFimAtividadeSecundaria
     */
    public function setDataFimAtividadeSecundaria($dataFimAtividadeSecundaria)
    {
        $this->dataFimAtividadeSecundaria = $dataFimAtividadeSecundaria;
    }

    /**
     * @return bool
     */
    public function isListaChapas(): bool
    {
        return (bool)$this->listaChapas;
    }

    /**
     * @param int $listaChapas
     */
    public function setListaChapas(int $listaChapas): void
    {
        $this->listaChapas = $listaChapas;
    }

    /**
     * @return bool
     */
    public function isListaPedidosSubstituicaoChapa()
    {
        return $this->listaPedidosSubstituicaoChapa;
    }

    /**
     * @param bool $listaPedidosSubstituicaoChapa
     */
    public function setListaPedidosSubstituicaoChapa($listaPedidosSubstituicaoChapa): void
    {
        $this->listaPedidosSubstituicaoChapa = $listaPedidosSubstituicaoChapa;
    }

    /**
     * @return bool
     */
    public function isListaPedidosImpugnacao(): ?bool
    {
        return $this->listaPedidosImpugnacao;
    }

    /**
     * @param bool $listaPedidosImpugnacao
     */
    public function setListaPedidosImpugnacao(?bool $listaPedidosImpugnacao): void
    {
        $this->listaPedidosImpugnacao = $listaPedidosImpugnacao;
    }

    /**
     * @return bool|null
     */
    public function isListaDenuncias(): ?bool
    {
        return $this->listaDenuncias;
    }

    /**
     * @param bool|null $listaDenuncias
     */
    public function setListaDenuncias(?bool $listaDenuncias): void
    {
        $this->listaDenuncias = $listaDenuncias;
    }


    /**
     * @return bool
     */
    public function isListaPedidosImpugnacaoResultado()
    {
        return $this->listaPedidosImpugnacaoResultado;
    }

    /**
     * @param bool $listaPedidosImpugnacaoResultado
     */
    public function setListaPedidosImpugnacaoResultado($listaPedidosImpugnacaoResultado): void
    {
        $this->listaPedidosImpugnacaoResultado = $listaPedidosImpugnacaoResultado;
    }
}