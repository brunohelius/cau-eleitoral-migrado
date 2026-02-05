<?php

namespace App\To;


use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe de transferência associada aos filtros de "Corpo Email".
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class CorpoEmailFiltroTO
{

    /**
     * @var ArrayCollection
     */
    private $corposEmail;

    /**
     * @var ArrayCollection
     */
    private $atividadeSecundarias;

    /**
     * @var ArrayCollection
     */
    private $ativo;

    /**
     * Retorna uma instância de 'CorpoEmailFiltroTO'.
     *
     * @param null $data
     * @return CorpoEmailFiltroTO
     */
    public static function newInstance($data = null)
    {
        $corpoEmailFiltroTO = new CorpoEmailFiltroTO();

        if ($data != null) {
            $corpoEmailFiltroTO->setAtivo(Utils::getValue('ativo', $data));
            $corpoEmailFiltroTO->setCorposEmail(Utils::getValue('corposEmail', $data));
            $corpoEmailFiltroTO->setAtividadeSecundarias(Utils::getValue('atividadesSecundarias', $data));
        }

        return $corpoEmailFiltroTO;
    }

    /**
     * @return mixed
     */
    public function getCorposEmail()
    {
        return $this->corposEmail;
    }

    /**
     * @param mixed $corposEmail
     */
    public function setCorposEmail($corpoEmail)
    {
        $this->corposEmail = $corpoEmail;
    }

    /**
     * @return mixed
     */
    public function getAtividadeSecundarias()
    {
        return $this->atividadeSecundarias;
    }

    /**
     * @param mixed $atividadeSecundarias
     */
    public function setAtividadeSecundarias($atividadeSecundarias)
    {
        $this->atividadeSecundarias = $atividadeSecundarias;
    }

    /**
     * @return mixed
     */
    public function getAtivo()
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

    /**
     * Recupera a lista de 'ids' do corpo de e-mail vinculados ao corpo de e-mail para o filtro.
     *
     * @return ArrayCollection
     */
    public function getIdsCorposEmail()
    {
        $idsCorpoEmail = new ArrayCollection();

        foreach ($this->getCorposEmail() as $corpoEmail) {
            $idsCorpoEmail->add($corpoEmail['id']);
        }

        return $idsCorpoEmail;
    }

    /**
     * Recupera a lista de 'ids' das atividades secundárias vinculados ao corpo de e-mail para o filtro.
     *
     * @return ArrayCollection
     */
    public function getIdsEmailsAtividadesSecundarias()
    {
        $idsAtividadesSecundarias = new ArrayCollection();

        foreach ($this->getAtividadeSecundarias() as $atividadeSecundaria) {
            $idsAtividadesSecundarias->add($atividadeSecundaria['emailAtividadeSecundaria']);
        }

        return $idsAtividadesSecundarias;
    }

    /**
     * Recupera os status que devem ter o filtro realizado.
     *
     * @return ArrayCollection
     */
    public function getStatusAtivoInativoFiltro()
    {
        $statusAtivoCollection = new ArrayCollection();

        foreach ($this->getAtivo() as $status) {
            $statusAtivo = boolval($status['valor']) ? 't' : 'f';
            $statusAtivoCollection->add($statusAtivo);
        }

        return $statusAtivoCollection;
    }
}
