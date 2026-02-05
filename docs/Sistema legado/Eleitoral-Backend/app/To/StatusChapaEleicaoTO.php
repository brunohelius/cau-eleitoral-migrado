<?php

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada a 'StatusChapaEleicao'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class StatusChapaEleicaoTO
{
    /**
     * @var integer
     */
    private $idChapaEleicao;

    /**
     * @var integer
     */
    private $idStatusChapa;

    /**
     * @var string
     */
    private $justificativa;

    /**
     * Fabricação estática de 'StatusChapaEleicaoTO'.
     *
     * @param array|null $data
     *
     * @return StatusChapaEleicaoTO
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setJustificativa(Utils::getValue("justificativa", $data));
            $instance->setIdStatusChapa(Utils::getValue("idStatusChapa", $data));
            $instance->setIdChapaEleicao(Utils::getValue("idChapaEleicao", $data));
        }

        return $instance;
    }

    /**
     * @return int
     */
    public function getIdChapaEleicao(): int
    {
        return $this->idChapaEleicao;
    }

    /**
     * @param int $idChapaEleicao
     */
    public function setIdChapaEleicao(int $idChapaEleicao): void
    {
        $this->idChapaEleicao = $idChapaEleicao;
    }

    /**
     * @return int
     */
    public function getIdStatusChapa(): int
    {
        return $this->idStatusChapa;
    }

    /**
     * @param int $idStatusChapa
     */
    public function setIdStatusChapa(int $idStatusChapa): void
    {
        $this->idStatusChapa = $idStatusChapa;
    }

    /**
     * @return string
     */
    public function getJustificativa(): string
    {
        return $this->justificativa;
    }

    /**
     * @param string $justificativa
     */
    public function setJustificativa(string $justificativa): void
    {
        $this->justificativa = $justificativa;
    }
}
