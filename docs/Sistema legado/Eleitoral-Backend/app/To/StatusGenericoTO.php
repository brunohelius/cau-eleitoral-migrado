<?php

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada a tabelas de status
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class StatusGenericoTO
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
     * Retorna uma nova instância de 'StatusGenericoTO'.
     *
     * @param null $data
     * @return StatusGenericoTO
     */
    public static function newInstance($data = null)
    {
        $statusGenericoTO = new StatusGenericoTO();

        if ($data != null) {
            $statusGenericoTO->setId(Utils::getValue('id', $data));
            $statusGenericoTO->setDescricao(Utils::getValue('descricao', $data));
        }

        return $statusGenericoTO;
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
