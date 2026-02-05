<?php
namespace App\To;

use App\Util\Utils;

class CabecalhoEmailFiltroTO
{
    private $ufs;
    
    private $idsCabecalhosEmail;
    
    private $ativo;
    
    public static function newInstance($data = null){
        $cabecalhoEmailFiltroTO = new CabecalhoEmailFiltroTO();
        
        if(!empty($data)){
            $cabecalhoEmailFiltroTO->setUfs(Utils::getValue('ufs', $data, []));
            $cabecalhoEmailFiltroTO->setIdsCabecalhosEmail(Utils::getValue('idsCabecalhosEmail', $data, []));
            $cabecalhoEmailFiltroTO->setAtivo(Utils::getBooleanValue('ativo', $data));
        }
        
        return $cabecalhoEmailFiltroTO;
    }
    /**
     * @return mixed
     */
    public function getUfs()
    {
        return $this->ufs;
    }

    /**
     * @param mixed $ufs
     */
    public function setUfs($ufs)
    {
        $this->ufs = $ufs;
    }

    /**
     * @return mixed
     */
    public function getIdsCabecalhosEmail()
    {
        return $this->idsCabecalhosEmail;
    }

    /**
     * @param mixed $idsCabecalhosEmail
     */
    public function setIdsCabecalhosEmail($idsCabecalhosEmail)
    {
        $this->idsCabecalhosEmail = $idsCabecalhosEmail;
    }

    /**
     * @return mixed
     */
    public function isAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param mixed $ativo
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

}

