<?php

namespace App\To;

use App\Entities\ArquivoDenuncia;
use App\Entities\ArquivoRecursoJulgamentoAdmissibilidade;
use App\Util\Utils;
use OpenApi\Annotations as OA;

/**
 * Class ArquivoRecursoJulgamentoAdmissibilidade
 * @package App\To
 *
 * @OA\Schema(schema="ArquivoRecursoJulgamentoAdmissibilidade")
 */
class ArquivoRecursoJulgamentoAdmissibilidadeTO
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
     * Retorna uma nova instância de 'ArquivoDenunciaTO'.
     *
     * @param ArquivoDenuncia $arquivoDenuncia
     * @return self
     */
    public static function newInstanceFromEntity(ArquivoRecursoJulgamentoAdmissibilidade $arquivoJulgamento = null)
    {
        $instance = new self;

        if ($arquivoJulgamento != null) {
            $instance->setId($arquivoJulgamento->getId());
            $instance->setNome($arquivoJulgamento->getNome());
            $instance->setNomeFisico($arquivoJulgamento->getNomeFisico());
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