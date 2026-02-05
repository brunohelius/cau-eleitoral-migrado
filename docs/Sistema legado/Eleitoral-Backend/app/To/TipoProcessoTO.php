<?php

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada a tabela de número de membros da 'ComissaoMembro'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class TipoProcessoTO
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $descricao;

    /**
     * Retorna uma nova instância de 'TipoProcessoTO'.
     *
     * @param null $data
     * @return TipoProcessoTO
     */
    public static function newInstance($data = null)
    {
        $tipoProcessoTO = new TipoProcessoTO();

        if ($data != null) {
            $tipoProcessoTO->setId(Utils::getValue('id', $data));
            $tipoProcessoTO->setDescricao(Utils::getValue('descricao', $data));
        }

        return $tipoProcessoTO;
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
}
