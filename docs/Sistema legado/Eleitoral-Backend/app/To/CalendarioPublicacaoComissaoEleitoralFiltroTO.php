<?php

namespace App\To;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe de transferência associada ao filtro de 'CalendarioPublicacaoComissaoMembro'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class CalendarioPublicacaoComissaoEleitoralFiltroTO
{

    /**
     * @var ArrayCollection
     */
    private $anosEleicao;

    /**
     * @var ArrayCollection
     */
    private $eleicoes;

    /**
     * @var ArrayCollection
     */
    private $tiposProcesso;

    /**
     * @var ArrayCollection
     */
    private $publicadas;

    /**
     * Retorna uma nova instância de 'CalendarioPublicacaoComissaoEleitoralFiltroTO'.
     *
     * @param null $data
     * @return CalendarioPublicacaoComissaoEleitoralFiltroTO
     */
    public static function newInstance($data = null)
    {
        $calendarioPublicacaoFiltro = new CalendarioPublicacaoComissaoEleitoralFiltroTO();

        if ($data != null) {
            $calendarioPublicacaoFiltro->setEleicoes(Utils::getValue('eleicoesFiltro', $data));
            $calendarioPublicacaoFiltro->setPublicadas(Utils::getValue('publicadoFiltro', $data));
            $calendarioPublicacaoFiltro->setAnosEleicao(Utils::getValue('anosEleicaoFiltro', $data));
            $calendarioPublicacaoFiltro->setTiposProcesso(Utils::getValue('tipoProcessoFiltro', $data));
        }

        return $calendarioPublicacaoFiltro;
    }

    /**
     * @return ArrayCollection
     */
    public function getAnosEleicao()
    {
        return $this->anosEleicao;
    }

    /**
     * @param ArrayCollection $anosEleicao
     */
    public function setAnosEleicao($anosEleicao)
    {
        $this->anosEleicao = $anosEleicao;
    }

    /**
     * @return ArrayCollection
     */
    public function getEleicoes()
    {
        return $this->eleicoes;
    }

    /**
     * @param ArrayCollection $eleicoes
     */
    public function setEleicoes($eleicoes)
    {
        $this->eleicoes = $eleicoes;
    }

    /**
     * @return ArrayCollection
     */
    public function getTiposProcesso()
    {
        return $this->tiposProcesso;
    }

    /**
     * @param ArrayCollection $tiposProcesso
     */
    public function setTiposProcesso($tiposProcesso)
    {
        $this->tiposProcesso = $tiposProcesso;
    }

    /**
     * @return ArrayCollection
     */
    public function getPublicadas()
    {
        return $this->publicadas;
    }

    /**
     * @param ArrayCollection $publicadas
     */
    public function setPublicadas($publicadas)
    {
        $this->publicadas = $publicadas;
    }

    /**
     * Recupera a lista com os 'ids' das eleições.
     *
     * @return ArrayCollection
     */
    public function getIdsEleicoes()
    {
        $idsEleicoes = new ArrayCollection();

        foreach ($this->getEleicoes() as $eleicao) {
            $idsEleicoes->add($eleicao['id']);
        }

        return $idsEleicoes;
    }

    /**
     * Recupera a lista com os 'ids' dos tipos de processo.
     *
     * @return ArrayCollection
     */
    public function getIdsProcessos()
    {
        $idsProcessos = new ArrayCollection();

        foreach ($this->getTiposProcesso() as $tipoProcesso) {
            $idsProcessos->add($tipoProcesso['id']);
        }

        return $idsProcessos;
    }

    /**
     * Recupera a lista com os status de publicado para o filtro.
     *
     * @return ArrayCollection
     */
    public function getStatusPublicado()
    {
        $statusPublicado = new ArrayCollection();

        foreach ($this->getPublicadas() as $publicado) {
            $status = boolval($publicado['valor']) ? 't' : 'f';
            $statusPublicado->add($status);
        }

        return $statusPublicado;
    }

}
