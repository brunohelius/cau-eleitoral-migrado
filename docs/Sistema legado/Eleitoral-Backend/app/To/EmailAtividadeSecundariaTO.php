<?php
namespace App\To;

use App\Util\Utils;
use App\Entities\EmailAtividadeSecundaria;

/**
 * Classe de transferência associada a “E-mail Atividade Secundária”, utilizada na parametrização de e-mail.
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class EmailAtividadeSecundariaTO
{
    /**
     * @var integer
     */
    private $idAtividadeSecundaria;

    /**
     * @var integer
     */
    private $idEmailAtividadeSecundaria;
    
    /**
     * @var integer
     */
    private $idTipoEmailAtividadeSecundaria;

    /**
     * @var string
     */
    private $descricaoTipoEmailAtividadeSecundaria;
    
    public static function newInstance($data = null){
        $emailAtividadeSecundariaTO = new EmailAtividadeSecundariaTO();
        if(!empty($data)){
            $emailAtividadeSecundariaTO->setIdAtividadeSecundaria(Utils::getValue('idAtividadeSecundaria', $data));
            $emailAtividadeSecundariaTO->setIdEmailAtividadeSecundaria(Utils::getValue(
                'idEmailAtividadeSecundaria',
                $data
            ));
            $emailAtividadeSecundariaTO->setIdTipoEmailAtividadeSecundaria(Utils::getValue(
                'idTipoEmailAtividadeSecundaria',
                $data
            ));
            $emailAtividadeSecundariaTO->setDescricaoTipoEmailAtividadeSecundaria(Utils::getValue(
                'descricaoTipoEmailAtividadeSecundaria',
                $data
            ));
        }
        return $emailAtividadeSecundariaTO;
    }
    
    /**
     * @return mixed
     */
    public function getIdAtividadeSecundaria()
    {
        return $this->idAtividadeSecundaria;
    }

    /**
     * @param mixed $idAtividadeSecundaria
     */
    public function setIdAtividadeSecundaria($idAtividadeSecundaria)
    {
        $this->idAtividadeSecundaria = $idAtividadeSecundaria;
    }

    /**
     * @return mixed
     */
    public function getIdEmailAtividadeSecundaria()
    {
        return $this->idEmailAtividadeSecundaria;
    }

    /**
     * @param mixed $idEmailAtividadeSecundaria
     */
    public function setIdEmailAtividadeSecundaria($idEmailAtividadeSecundaria)
    {
        $this->idEmailAtividadeSecundaria = $idEmailAtividadeSecundaria;
    }

    /**
     * @return mixed
     */
    public function getIdTipoEmailAtividadeSecundaria()
    {
        return $this->idTipoEmailAtividadeSecundaria;
    }

    /**
     * @param mixed $idTipoEmailAtividadeSecundaria
     */
    public function setIdTipoEmailAtividadeSecundaria($idTipoEmailAtividadeSecundaria)
    {
        $this->idTipoEmailAtividadeSecundaria = $idTipoEmailAtividadeSecundaria;
    }

    /**
     * @return string
     */
    public function getDescricaoTipoEmailAtividadeSecundaria(): ?string
    {
        return $this->descricaoTipoEmailAtividadeSecundaria;
    }

    /**
     * @param string $descricaoTipoEmailAtividadeSecundaria
     */
    public function setDescricaoTipoEmailAtividadeSecundaria(?string $descricaoTipoEmailAtividadeSecundaria): void
    {
        $this->descricaoTipoEmailAtividadeSecundaria = $descricaoTipoEmailAtividadeSecundaria;
    }
}

