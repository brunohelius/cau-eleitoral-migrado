<?php

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada a 'StatusMembroChapa'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class StatusMembroChapaTO
{
    /**
     * @var integer
     */
    private $idMembroChapa;

    /**
     * @var integer
     */
    private $idStatusConvite;

    /**
     * @var integer
     */
    private $idStatusValidacao;

    /**
     * @var string
     */
    private $justificativa;

    /**
     * Fabricação estática de 'StatusMembroChapaTO'.
     *
     * @param array|null $data
     *
     * @return StatusMembroChapaTO
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setJustificativa(Utils::getValue("justificativa", $data));
            $instance->setIdMembroChapa(Utils::getValue("idMembroChapa", $data));
            $instance->setIdStatusConvite(Utils::getValue("idStatusConvite", $data));
            $instance->setIdStatusValidacao(Utils::getValue("idStatusValidacao", $data));
        }

        return $instance;
    }

    /**
     * @return int
     */
    public function getIdMembroChapa(): ?int
    {
        return $this->idMembroChapa;
    }

    /**
     * @param int $idMembroChapa
     */
    public function setIdMembroChapa(?int $idMembroChapa): void
    {
        $this->idMembroChapa = $idMembroChapa;
    }

    /**
     * @return int
     */
    public function getIdStatusParticipacaoChapa(): ?int
    {
        return $this->idStatusConvite;
    }

    /**
     * @param int $idStatusConvite
     */
    public function setIdStatusConvite(?int $idStatusConvite): void
    {
        $this->idStatusConvite = $idStatusConvite;
    }

    /**
     * @return int
     */
    public function getIdStatusValidacao(): ?int
    {
        return $this->idStatusValidacao;
    }

    /**
     * @param int $idStatusValidacao
     */
    public function setIdStatusValidacao(?int $idStatusValidacao): void
    {
        $this->idStatusValidacao = $idStatusValidacao;
    }

    /**
     * @return string
     */
    public function getJustificativa(): ?string
    {
        return $this->justificativa;
    }

    /**
     * @param string $justificativa
     */
    public function setJustificativa(?string $justificativa): void
    {
        $this->justificativa = $justificativa;
    }
}
