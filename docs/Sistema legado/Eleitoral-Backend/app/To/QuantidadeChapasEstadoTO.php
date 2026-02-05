<?php
/*
 * QuantidadeChapasEstadoTO.php
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
 * Classe de transferência associada a 'QuantidadeChapasEstado'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="QuantidadeChapasEstado")
 */
class QuantidadeChapasEstadoTO
{
    /**
     * @var integer|null
     * @OA\Property()
     */
    private $idCauUf;

    /**
     * @var string
     * @OA\Property()
     */
    private $uf;

    /**
     * @var string
     * @OA\Property()
     */
    private $prefixoUf;

	/**
     * @var integer
     * @OA\Property()
     */
	private $quantidadeTotalChapas;

	/**
     * @var integer
     * @OA\Property()
     */
	private $quantidadeChapasPendentes;

    /**
     * @var integer
     * @OA\Property()
     */
    private $quantidadeChapasSemPendentes;

	/**
     * @var integer
     * @OA\Property()
     */
	private $quantidadeChapasConcluidas;

	/**
	 * @return int|null
	 */
	public function getIdCauUf(): ?int
	{
		return $this->idCauUf;
	}

	/**
	 * @param int|null $idCauUf
	 */
	public function setIdCauUf(?int $idCauUf): void
	{
		$this->idCauUf = $idCauUf;
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
     * @return string
     */
    public function getPrefixoUf(): string
    {
        return $this->prefixoUf;
    }

    /**
     * @param string $prefixoUf
     */
    public function setPrefixoUf(string $prefixoUf): void
    {
        $this->prefixoUf = $prefixoUf;
    }

	/**
	 * @return int
	 */
	public function getQuantidadeTotalChapas(): ?int
	{
		return $this->quantidadeTotalChapas;
	}

	/**
	 * @param int $quantidadeTotalChapas
	 */
	public function setQuantidadeTotalChapas(?int $quantidadeTotalChapas): void
	{
		$this->quantidadeTotalChapas = $quantidadeTotalChapas;
	}

	/**
	 * @return int
	 */
	public function getQuantidadeChapasPendentes(): ?int
	{
		return $this->quantidadeChapasPendentes;
	}

	/**
	 * @param int $quantidadeChapasPendentes
	 */
	public function setQuantidadeChapasPendentes(?int $quantidadeChapasPendentes): void
	{
		$this->quantidadeChapasPendentes = $quantidadeChapasPendentes;
	}

    /**
     * @return int
     */
    public function getQuantidadeChapasSemPendentes(): ?int
    {
        return $this->quantidadeChapasSemPendentes;
    }

    /**
     * @param int $quantidadeChapasSemPendentes
     */
    public function setQuantidadeChapasSemPendentes(?int $quantidadeChapasSemPendentes): void
    {
        $this->quantidadeChapasSemPendentes = $quantidadeChapasSemPendentes;
    }

	/**
	 * @return int
	 */
	public function getQuantidadeChapasConcluidas(): ?int
	{
		return $this->quantidadeChapasConcluidas;
	}

	/**
	 * @param int $quantidadeChapasConcluidas
	 */
	public function setQuantidadeChapasConcluidas(?int $quantidadeChapasConcluidas): void
	{
		$this->quantidadeChapasConcluidas = $quantidadeChapasConcluidas;
	}

	/**
	 * Fabricação estática de 'QuantidadeChapasEstadoTO'.
	 *
	 * @param array|null $data
	 *
	 * @return QuantidadeChapasEstadoTO
	 */
	public static function newInstance($data = null)
	{
		$instance = new self();

		if ($data != null) {
            $instance->setUf(Utils::getValue("uf", $data));

            $instance->setPrefixoUf(Utils::getValue("prefixoUf", $data));

            $quantidadeTotalChapas = Utils::getValue("quantidadeTotalChapas", $data) ?? 0;
			$instance->setQuantidadeTotalChapas($quantidadeTotalChapas);

            $quantidadeChapasPendentes = Utils::getValue("quantidadeChapasPendentes", $data) ?? 0;
			$instance->setQuantidadeChapasPendentes($quantidadeChapasPendentes);

			$instance->setQuantidadeChapasSemPendentes($quantidadeTotalChapas - $quantidadeChapasPendentes);

            $quantidadeChapasConcluidas = Utils::getValue("quantidadeChapasConcluidas", $data);
			$instance->setQuantidadeChapasConcluidas($quantidadeChapasConcluidas);

            $idCauUf = Utils::getValue("idCauUf", $data);
            if(! empty($idCauUf)) {
                $instance->setIdCauUf($idCauUf);
            }
		}

		return $instance;
	}
}
