<?php
/*
 * ChapaQuantidadeMembrosTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Util\Utils;
use DateTime;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada a 'ChapaQuantidadeMembrosTO'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="ChapaQuantidadeMembros")
 */
class ChapaQuantidadeMembrosTO
{

    /**
     * @var integer|null
     * @OA\Property()
     */
    private $numeroChapa;

    /**
     * @var string
     * @OA\Property()
     */
    private $uf;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idCauUf;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idStatusChapa;

    /**
     * @var DateTime
     * @OA\Property()
     */
    private $dataStatusChapa;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idChapaEleicao;

    /**
     * @var integer
     * @OA\Property()
     */
    private $quantidadeTotalMembrosChapa;

    /**
     * @var integer
     * @OA\Property()
     */
    private $quantidadeMembrosConfirmados;

    /**
     * @var array
     * @OA\Property(
     *      type="array",
     *      @OA\Items(
     *          type="array",
     *          @OA\Items()
     *      )
     * )
     */
    private $membrosResponsaveis;

    /**
     * Fabricação estática de 'ChapasQuantidadeMembrosTO'.
     *
     * @param array|null $data
     *
     * @return ChapaQuantidadeMembrosTO
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setUf(Utils::getValue("uf", $data));
            $instance->setIdCauUf(Utils::getValue("idCauUf", $data));
            $instance->setIdStatusChapa(Utils::getValue("idStatusChapa", $data));
            $instance->setDataStatusChapa(Utils::getValue("dataStatusChapa", $data));
            $instance->setIdChapaEleicao(Utils::getValue("idChapaEleicao", $data));
            $instance->setMembrosResponsaveis(Utils::getValue("membrosResponsaveis", $data));

            $quantidadeTotalMembrosChapa = Utils::getValue("quantidadeTotalMembrosChapa", $data) ?? 0;
            $instance->setQuantidadeTotalMembrosChapa($quantidadeTotalMembrosChapa);

            $quantidadeMembrosConfirmados = Utils::getValue("quantidadeMembrosConfirmados", $data) ?? 0;
            $instance->setQuantidadeMembrosConfirmados($quantidadeMembrosConfirmados);

            $numeroChapa = Utils::getValue("numeroChapa", $data, null);
            $instance->setNumeroChapa($numeroChapa);
        }

        return $instance;
    }

    /**
     * @return int|null
     */
    public function getNumeroChapa(): ?int
    {
        return $this->numeroChapa;
    }

    /**
     * @param int|null $numeroChapa
     */
    public function setNumeroChapa(?int $numeroChapa): void
    {
        $this->numeroChapa = $numeroChapa;
    }

    /**
     * @return string
     */
    public function getUf(): ?string
    {
        return $this->uf;
    }

    /**
     * @param string $uf
     */
    public function setUf(?string $uf): void
    {
        $this->uf = $uf;
    }

    /**
     * @return int
     */
    public function getIdCauUf(): ?int
    {
        return $this->idCauUf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf(?int $idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return int
     */
    public function getIdStatusChapa(): ?int
    {
        return $this->idStatusChapa;
    }

    /**
     * @param int $idStatusChapa
     */
    public function setIdStatusChapa(?int $idStatusChapa): void
    {
        $this->idStatusChapa = $idStatusChapa;
    }

    /**
     * @return DateTime
     */
    public function getDataStatusChapa(): ?DateTime
    {
        return $this->dataStatusChapa;
    }

    /**
     * @param DateTime|null $dataStatusChapa
     */
    public function setDataStatusChapa(?DateTime $dataStatusChapa): void
    {
        $this->dataStatusChapa = $dataStatusChapa;
    }

    /**
     * @return integer
     */
    public function getIdChapaEleicao(): ?int
    {
        return $this->idChapaEleicao;
    }

    /**
     * @param integer $idChapaEleicao
     */
    public function setIdChapaEleicao(?int $idChapaEleicao): void
    {
        $this->idChapaEleicao = $idChapaEleicao;
    }

    /**
     * @return int
     */
    public function getQuantidadeTotalMembrosChapa(): ?int
    {
        return $this->quantidadeTotalMembrosChapa;
    }

    /**
     * @param int $quantidadeTotalMembrosChapa
     */
    public function setQuantidadeTotalMembrosChapa(?int $quantidadeTotalMembrosChapa): void
    {
        $this->quantidadeTotalMembrosChapa = $quantidadeTotalMembrosChapa;
    }

    /**
     * @return int
     */
    public function getQuantidadeMembrosConfirmados(): ?int
    {
        return $this->quantidadeMembrosConfirmados;
    }

    /**
     * @param int $quantidadeMembrosConfirmados
     */
    public function setQuantidadeMembrosConfirmados(?int $quantidadeMembrosConfirmados): void
    {
        $this->quantidadeMembrosConfirmados = $quantidadeMembrosConfirmados;
    }

    /**
     * @return array
     */
    public function getMembrosResponsaveis(): ?array
    {
        return $this->membrosResponsaveis;
    }

    /**
     * @param array $membrosResponsaveis
     */
    public function setMembrosResponsaveis(?array $membrosResponsaveis): void
    {
        $this->membrosResponsaveis = $membrosResponsaveis;
    }
}
