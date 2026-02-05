<?php
/*
 * ConviteStatusFiltroTO.php
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
 * Classe de transferência associada a 'ConviteStatusFiltroTO'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="ConviteStatusFiltro")
 */
class ConviteStatusFiltroTO
{

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
    private $declaracoes;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idChapaEleicao;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idMembroChapa;

    /**
     * @var string
     * @OA\Property()
     */
    private $sinteseCurriculo;

    /**
     * @var string
     * @OA\Property()
     */
    private $fotoSinteseCurriculo;

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
    private $cartasIndicacaoInstituicao;

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
    private $comprovantesVinculoDocenteIes;

    /**
     * Fabricação estática de 'ConviteChapaTO'.
     *
     * @param array|null $data
     *
     * @return ConviteStatusFiltroTO
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setIdMembroChapa(Utils::getValue("idMembroChapa", $data));
            $instance->setIdChapaEleicao(Utils::getValue("idChapaEleicao", $data));

            $declaracoes = Utils::getValue("declaracoes", $data, []);
            if (!empty($declaracoes)) {
                $instance->setDeclaracoes($declaracoes);
            }

            $sinteseCurriculo = Utils::getValue("sinteseCurriculo", $data);
            if (!empty($sinteseCurriculo)) {
                $instance->setSinteseCurriculo($sinteseCurriculo);
            }

            $fotoSinteseCurriculo = Utils::getValue("fotoSinteseCurriculo", $data);
            if (!empty($fotoSinteseCurriculo)) {
                $instance->setFotoSinteseCurriculo($fotoSinteseCurriculo);
            }

            $cartaIndicacaoInstituicao = Utils::getValue("cartasIndicacaoInstituicao", $data);
            if (!empty($cartaIndicacaoInstituicao)) {
                $instance->setCartasIndicacaoInstituicao($cartaIndicacaoInstituicao);
            }

            $comprovanteVinculoDocenteIes = Utils::getValue("comprovantesVinculoDocenteIes", $data);
            if (!empty($comprovanteVinculoDocenteIes)) {
                $instance->setComprovantesVinculoDocenteIes($comprovanteVinculoDocenteIes);
            }
        }

        return $instance;
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
     * @return string
     */
    public function getSinteseCurriculo(): ?string
    {
        return $this->sinteseCurriculo;
    }

    /**
     * @param string $sinteseCurriculo
     */
    public function setSinteseCurriculo(?string $sinteseCurriculo): void
    {
        $this->sinteseCurriculo = $sinteseCurriculo;
    }

    /**
     * @return string
     */
    public function getFotoSinteseCurriculo()
    {
        return $this->fotoSinteseCurriculo;
    }

    /**
     * @param string $fotoSinteseCurriculo
     */
    public function setFotoSinteseCurriculo($fotoSinteseCurriculo): void
    {
        $this->fotoSinteseCurriculo = $fotoSinteseCurriculo;
    }

    /**
     * @return array
     */
    public function getDeclaracoes(): ?array
    {
        return $this->declaracoes;
    }

    /**
     * @param array $declaracoes
     */
    public function setDeclaracoes(?array $declaracoes): void
    {
        $this->declaracoes = $declaracoes;
    }

    /**
     * @return array
     */
    public function getCartasIndicacaoInstituicao(): ?array
    {
        return $this->cartasIndicacaoInstituicao;
    }

    /**
     * @param array $cartasIndicacaoInstituicao
     */
    public function setCartasIndicacaoInstituicao(?array $cartasIndicacaoInstituicao): void
    {
        $this->cartasIndicacaoInstituicao = $cartasIndicacaoInstituicao;
    }

    /**
     * @return array
     */
    public function getComprovantesVinculoDocenteIes(): ?array
    {
        return $this->comprovantesVinculoDocenteIes;
    }

    /**
     * @param array $comprovantesVinculoDocenteIes
     */
    public function setComprovantesVinculoDocenteIes(?array $comprovantesVinculoDocenteIes): void
    {
        $this->comprovantesVinculoDocenteIes = $comprovantesVinculoDocenteIes;
    }
}
