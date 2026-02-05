<?php

namespace App\To;

use App\Entities\RedeSocialHistoricoPlataforma;
use App\Util\Utils;
use OpenApi\Util;

/**
 * Classe de transferência para a RedeSocialHistoricoPlataformaTO
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class RedeSocialHistoricoPlataformaTO {

    /**
     * @var int $id
     */
    private $id;

    /**
     * @var string $descricao
     */
    private $descricao;

    /**
     * @var int $tipoRedeSocial
     */
    private $tipoRedeSocial;

    /**
     * @var PlataformaChapaHistoricoTO $plataformaChapaHistorico
     */
    private $plataformaChapaHistorico;

    /**
     * @var boolean $isAtivo
     */
    private $isAtivo;


    /**
     * Retorna uma nova instância de '$redeSocialHistoricoPlataformaTO'.
     * @param null $data
     * @return RedeSocialHistoricoPlataformaTO
     */
    public static function newInstance($data = null)
    {
        $redeSocialHistoricoPlataformaTO = new RedeSocialHistoricoPlataformaTO();

        if($data !=null) {

            $redeSocialHistoricoPlataformaTO->setId(Utils::getValue('id', $data));
            $redeSocialHistoricoPlataformaTO->setDescricao(Utils::getValue('descricao', $data));
            $redeSocialHistoricoPlataformaTO->setTipoRedeSocial(Utils::getValue('tipoRedeSocial', $data));
            $redeSocialHistoricoPlataformaTO->setIsAtivo(Utils::getValue('isAtivo', $data));

        }

        return $redeSocialHistoricoPlataformaTO;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return int
     */
    public function getTipoRedeSocial()
    {
        return $this->tipoRedeSocial;
    }

    /**
     * @param int $tipoRedeSocial
     */
    public function setTipoRedeSocial($tipoRedeSocial)
    {
        $this->tipoRedeSocial = $tipoRedeSocial;
    }

    /**
     * @return PlataformaChapaHistoricoTO
     */
    public function getPlataformaChapaHistorico()
    {
        return $this->plataformaChapaHistorico;
    }

    /**
     * @param PlataformaChapaHistoricoTO $plataformaChapaHistorico
     */
    public function setPlataformaChapaHistorico(i$plataformaChapaHistorico)
    {
        $this->plataformaChapaHistorico = $plataformaChapaHistorico;
    }

    /**
     * @return bool
     */
    public function isAtivo()
    {
        return $this->isAtivo;
    }

    /**
     * @param bool $isAtivo
     */
    public function setIsAtivo($isAtivo)
    {
        $this->isAtivo = $isAtivo;
    }

}