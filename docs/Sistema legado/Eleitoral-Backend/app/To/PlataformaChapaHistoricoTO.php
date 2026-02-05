<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\PlataformaChapaHistorico;
use App\Util\Utils;
use Illuminate\Support\Arr;
use OpenApi\Util;

/**
 * Classe de transferência para a PlataformaChapaHistoricoTO
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class PlataformaChapaHistoricoTO {

    /**
     * @var int $id
     */
    private $id;

    /**
     * @var string $descricaoPlataforma
     */
    private $descricaoPlataforma;

    /**
     * @var ChapaEleicaoTO $chapaEleicao
     */
    private $chapaEleicao;

    /**
     * @var string |null
     */
    private $nomeUsuarioInclusao;

    /**
     * @var string $modulo
     */
    private $modulo;

    /**
     * @var \DateTime | null $dataCadastro
     */
    private $dataCadastro;

    /**
     * @var integer | nul $sequencia
     */
    private $sequencia;

    /**.
     * @var RedeSocialHistoricoPlataformaTO[] |null
     */
    private $redesSociaisChapa;


    /**
     * Retorna uma nova instância de 'PlataformaChapaTO'.
     * @param null $data
     * @return PlataformaChapaHistoricoTO
     */
    public static function  newInstance($data = null)
    {
        $plataformaChapaHistoricoTO = new PlataformaChapaHistoricoTO();

        if($data !=null) {
            $plataformaChapaHistoricoTO->setId(Utils::getValue('id', $data));
            $plataformaChapaHistoricoTO->setDescricaoPlataforma(Utils::getValue('descricaoPlataforma', $data));
            $plataformaChapaHistoricoTO->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $plataformaChapaHistoricoTO->setSequencia(Utils::getValue('sequencia', $data));

            $chapaEleicao = Utils::getValue("chapaEleicao", $data) ;
            if(!empty($chapaEleicao)) {
                $plataformaChapaHistoricoTO->setChapaEleicao(ChapaEleicaoTO::newInstance($chapaEleicao));
            }

            $profissionalInclusaoPlataforma = Utils::getValue("profissionalInclusaoPlataforma", $data);
            if(!empty($profissionalInclusaoPlataforma)) {
               $plataformaChapaHistoricoTO->setNomeUsuarioInclusao(Utils::getValue('nome', $profissionalInclusaoPlataforma));
            }

            $usuarioInclusaoPlataforma = Utils::getValue("usuarioInclusaoPlataforma", $data);
            if(!empty($usuarioInclusaoPlataforma)) {
                $plataformaChapaHistoricoTO->setNomeUsuarioInclusao(Utils::getValue('nome', $usuarioInclusaoPlataforma));
            }

            $redesSociais = Utils::getValue("redesSociaisHistoricoPlataforma", $data);
            if(!empty($redesSociais)) {
               $plataformaChapaHistoricoTO->setRedesSociaisChapa(array_map(
                   function($redeSocial){
                    return RedeSocialHistoricoPlataformaTO::newInstance($redeSocial);
                    }, $redesSociais));
            }

            $modulo = Utils::getValue('modulo', $data);

            if(!empty($modulo)) {
                $plataformaChapaHistoricoTO->setModulo($modulo);
            } else {
                $plataformaChapaHistoricoTO->setModulo(!empty($profissionalInclusaoPlataforma)
                    ? Constants::ORIGEM_PROFISSIONAL
                    : Constants::ORIGEM_CORPORATIVO
                );

            }

        }
        return $plataformaChapaHistoricoTO;
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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDescricaoPlataforma()
    {
        return $this->descricaoPlataforma;
    }

    /**
     * @param string $descricaoPlataforma
     */
    public function setDescricaoPlataforma($descricaoPlataforma)
    {
        $this->descricaoPlataforma = $descricaoPlataforma;
    }

    /**
     * @return ChapaEleicaoTO
     */
    public function getChapaEleicao()
    {
        return $this->chapaEleicao;
    }

    /**
     * @param ChapaEleicaoTO $chapaEleicao
     */
    public function setChapaEleicao($chapaEleicao)
    {
        $this->chapaEleicao = $chapaEleicao;
    }

    /**
     * @return string|null
     */
    public function getNomeUsuarioInclusao(): ?string
    {
        return $this->nomeUsuarioInclusao;
    }

    /**
     * @param string|null $nomeUsuarioInclusao
     */
    public function setNomeUsuarioInclusao(?string $nomeUsuarioInclusao): void
    {
        $this->nomeUsuarioInclusao = $nomeUsuarioInclusao;
    }



    /**
     * @return string
     */
    public function getModulo()
    {
        return $this->modulo;
    }

    /**
     * @param string $modulo
     */
    public function setModulo($modulo)
    {
        $this->modulo = $modulo;
    }

    /**
     * @return nul|int
     */
    public function getSequencia()
    {
        return $this->sequencia;
    }

    /**
     * @param nul|int $sequencia
     */
    public function setSequencia($sequencia): void
    {
        $this->sequencia = $sequencia;
    }

    /**
     * @return \DateTime|null
     */
    public function getDataCadastro(): ?\DateTime
    {
        return $this->dataCadastro;
    }

    /**
     * @param \DateTime|null $dataCadastro
     */
    public function setDataCadastro(?\DateTime $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return RedeSocialHistoricoPlataformaTO[]|null
     */
    public function getRedesSociaisChapa(): ?array
    {
        return $this->redesSociaisChapa;
    }

    /**
     * @param RedeSocialHistoricoPlataformaTO[]|null $redesSociaisChapa
     */
    public function setRedesSociaisChapa(?array $redesSociaisChapa): void
    {
        $this->redesSociaisChapa = $redesSociaisChapa;
    }



}