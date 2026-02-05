<?php

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada a 'CauUf'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class CauUfTO
{
    /**
     * @var string
     */
    private $descricao;

    /**
     * @var integer
     */
    private $idCauUf;

    /**
     * Fabricação estática de 'StatusChapaEleicaoTO'.
     *
     * @param array|null $data
     *
     * @return CauUfTO
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setIdCauUf(Utils::getValue("idCauUf", $data));
            $instance->setDescricao(Utils::getValue("descricao", $data));
        }

        return $instance;
    }

    /**
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return int
     */
    public function getIdCauUf(): int
    {
        return $this->idCauUf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf(int $idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }
}
