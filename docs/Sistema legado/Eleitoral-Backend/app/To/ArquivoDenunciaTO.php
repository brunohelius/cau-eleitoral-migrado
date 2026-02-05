<?php

namespace App\To;

use App\Entities\ArquivoDenuncia;
use App\Util\Utils;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'ArquivoDenuncia'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="ArquivoDenuncia")
 */class ArquivoDenunciaTO
{
    /**
     * ID do Arquivo do Calendário
     * @var integer
     * @OA\Property()
     */
    private $id;

    /**
     * Nome do Arquivo
     * @var string
     * @OA\Property()
     */
    private $nome;

    /**
     * Nome Físico do Arquivo
     * @var string
     * @OA\Property()
     */
    private $nomeFisico;

    /**
     * Tamanho do Arquivo
     * @var string
     * @OA\Property()
     */
    private $tamanho;

    /**
     * Calendário do Arquivo
     * @var CalendarioTO
     * @OA\Property()
     */
    private $calendario;

    /**
     * Fabricação estática de 'ArquivoDenunciaTO'.
     *
     * @param array|null $data
     * @return self
     */
    public static function newInstance($data = null)
    {
        $arquivoTO = new self();

        if ($data != null) {
            $arquivoTO->setId(Utils::getValue("id", $data));
            $arquivoTO->setNome(Utils::getValue("nome", $data));
            $arquivoTO->setTamanho(Utils::getValue("tamanho", $data));
            $arquivoTO->setNomeFisico(Utils::getValue("nomeFisico", $data));
            $arquivoTO->setCalendario(Utils::getValue("calendario", $data));
        }

        return $arquivoTO;
    }

    /**
     * Retorna uma nova instância de 'ArquivoDenunciaTO'.
     *
     * @param ArquivoDenuncia $arquivoDenuncia
     * @return self
     */
    public static function newInstanceFromEntity($arquivoDenuncia = null)
    {
        $instance = new self;

        if ($arquivoDenuncia != null) {
            $instance->setId($arquivoDenuncia->getId());
            $instance->setNome($arquivoDenuncia->getNome());
            $instance->setNomeFisico($arquivoDenuncia->getNomeFisico());
        }

        return $instance;
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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome($nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return string
     */
    public function getNomeFisico()
    {
        return $this->nomeFisico;
    }

    /**
     * @param string $nomeFisico
     */
    public function setNomeFisico($nomeFisico): void
    {
        $this->nomeFisico = $nomeFisico;
    }

    /**
     * @return CalendarioTO
     */
    public function getCalendario()
    {
        return $this->calendario;
    }

    /**
     * @param CalendarioTO $calendario
     */
    public function setCalendario($calendario): void
    {
        $this->calendario = $calendario;
    }

    /**
     * @return string
     */
    public function getTamanho()
    {
        return $this->tamanho;
    }

    /**
     * @param string $tamanho
     */
    public function setTamanho($tamanho): void
    {
        $this->tamanho = $tamanho;
    }
}