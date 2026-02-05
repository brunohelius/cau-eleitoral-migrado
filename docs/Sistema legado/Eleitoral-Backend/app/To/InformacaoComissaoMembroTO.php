<?php

namespace App\To;

use App\Entities\InformacaoComissaoMembro;
use App\Util\Utils;

/**
 * Classe de transferência associada ao 'Informação Comissao Membro'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class InformacaoComissaoMembroTO
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var AtividadeSecundariaTO
     */
    private $atividadeSecundaria;

    /**
     * Retorna uma nova instância de 'InformacaoComissaoMembroTO'.
     *
     * @param null $data
     * @return InformacaoComissaoMembroTO
     */
    public static function newInstance($data = null)
    {
        $informacaoComissaoMembroTO = new InformacaoComissaoMembroTO();

        if ($data != null) {
            $informacaoComissaoMembroTO->setId(Utils::getValue('id', $data));

            if (!empty($data['atividadeSecundaria'])) {
                $atividadeSecundariaTO = AtividadeSecundariaTO::newInstance($data['atividadeSecundaria']);
                $informacaoComissaoMembroTO->setAtividadeSecundaria($atividadeSecundariaTO);
            }
        }

        return $informacaoComissaoMembroTO;
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
     * @return AtividadeSecundariaTO
     */
    public function getAtividadeSecundaria()
    {
        return $this->atividadeSecundaria;
    }

    /**
     * @param AtividadeSecundariaTO $atividadeSecundaria
     */
    public function setAtividadeSecundaria($atividadeSecundaria)
    {
        $this->atividadeSecundaria = $atividadeSecundaria;
    }
}
