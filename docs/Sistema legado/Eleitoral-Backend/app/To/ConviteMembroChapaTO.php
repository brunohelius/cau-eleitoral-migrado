<?php
/*
 * ConviteMembroChapaTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Util\Utils;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada a 'ConviteMembroChapaTO'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="ConviteMembroChapa")
 */
class ConviteMembroChapaTO
{

    /**
     * @var string
     * @OA\Property()
     */
    private $nomeResponsavelChapa;

    /**
     * @var string
     * @OA\Property()
     */
    private $descricaoPlataforma;

    /**
     * @var string
     * @OA\Property()
     */
    private $tipoParticChapa;

    /**
     * @var integer
     * @OA\Property()
     */
    private $tipoCandidatura;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idChapaEleicao;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idAtividadeSecundaria;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idAtividadeSecundariaConvite;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idMembroChapa;

    /**
     * @var integer
     * @OA\Property()
     */
    private $numeroOrdem;

    /**
     * Fabricação estática de 'ConviteChapaTO'.
     *
     * @param array|null $data
     *
     * @return ConviteMembroChapaTO
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setNumeroOrdem(Utils::getValue("numeroOrdem", $data));
            $instance->setIdMembroChapa(Utils::getValue("idMembroChapa", $data));
            $instance->setIdChapaEleicao(Utils::getValue("idChapaEleicao", $data));
            $instance->setTipoParticChapa(Utils::getValue("tipoParticChapa", $data));
            $instance->setTipoCandidatura(Utils::getValue("idTipoCandidatura", $data));
            $instance->setDescricaoPlataforma(Utils::getValue("descricaoPlataforma", $data));
            $instance->setNomeResponsavelChapa(Utils::getValue("nomeResponsavelChapa", $data));
            $instance->setIdAtividadeSecundaria(Utils::getValue("idAtividadeSecundaria", $data));
            $instance->setIdAtividadeSecundariaConvite(Utils::getValue("idAtividadeSecundariaConvite", $data));
        }

        return $instance;
    }

    /**
     * @return string
     */
    public function getNomeResponsavelChapa(): string
    {
        return $this->nomeResponsavelChapa;
    }

    /**
     * @param string $nomeResponsavelChapa
     */
    public function setNomeResponsavelChapa(string $nomeResponsavelChapa): void
    {
        $this->nomeResponsavelChapa = $nomeResponsavelChapa;
    }

    /**
     * @return string
     */
    public function getDescricaoPlataforma(): string
    {
        return $this->descricaoPlataforma;
    }

    /**
     * @param string $descricaoPlataforma
     */
    public function setDescricaoPlataforma(string $descricaoPlataforma): void
    {
        $this->descricaoPlataforma = $descricaoPlataforma;
    }

    /**
     * @return string
     */
    public function getTipoParticChapa(): string
    {
        return $this->tipoParticChapa;
    }

    /**
     * @param string $tipoParticChapa
     */
    public function setTipoParticChapa(string $tipoParticChapa): void
    {
        $this->tipoParticChapa = $tipoParticChapa;
    }

    /**
     * @return int
     */
    public function getTipoCandidatura(): int
    {
        return $this->tipoCandidatura;
    }

    /**
     * @param int $tipoCandidatura
     */
    public function setTipoCandidatura(int $tipoCandidatura): void
    {
        $this->tipoCandidatura = $tipoCandidatura;
    }

    /**
     * @return integer
     */
    public function getIdChapaEleicao(): int
    {
        return $this->idChapaEleicao;
    }

    /**
     * @param integer $idChapaEleicao
     */
    public function setIdChapaEleicao(int $idChapaEleicao): void
    {
        $this->idChapaEleicao = $idChapaEleicao;
    }

    /**
     * @return int
     */
    public function getIdMembroChapa(): int
    {
        return $this->idMembroChapa;
    }

    /**
     * @return int
     */
    public function getIdAtividadeSecundaria(): ?int
    {
        return $this->idAtividadeSecundaria;
    }

    /**
     * @param int $idAtividadeSecundaria
     */
    public function setIdAtividadeSecundaria(?int $idAtividadeSecundaria): void
    {
        $this->idAtividadeSecundaria = $idAtividadeSecundaria;
    }

    /**
     * @param int $idMembroChapa
     */
    public function setIdMembroChapa(int $idMembroChapa): void
    {
        $this->idMembroChapa = $idMembroChapa;
    }

    /**
     * @return int
     */
    public function getNumeroOrdem(): int
    {
        return $this->numeroOrdem;
    }

    /**
     * @param int $numeroOrdem
     */
    public function setNumeroOrdem(int $numeroOrdem): void
    {
        $this->numeroOrdem = $numeroOrdem;
    }

    /**
     * @return int
     */
    public function getIdAtividadeSecundariaConvite()
    {
        return $this->idAtividadeSecundariaConvite;
    }

    /**
     * @param int $idAtividadeSecundariaConvite
     */
    public function setIdAtividadeSecundariaConvite($idAtividadeSecundariaConvite): void
    {
        $this->idAtividadeSecundariaConvite = $idAtividadeSecundariaConvite;
    }
}
