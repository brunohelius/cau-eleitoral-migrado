<?php


namespace App\To;

use Illuminate\Support\Arr;

/**
 * Classe de transferência para a visualizaçao das informaçoes do Candidato no pedido de impugnaçao
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ArquivoPedidoImpugnacaoTO
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nome;

    /**
     * @var string
     */
    private $nomeFisico;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return string
     */
    public function getNomeFisico(): string
    {
        return $this->nomeFisico;
    }

    /**
     * @param string $nomeFisico
     */
    public function setNomeFisico(string $nomeFisico): void
    {
        $this->nomeFisico = $nomeFisico;
    }

    /**
     * Retorna uma nova instância de 'CandidadoPedidoImpugnacaoTO'.
     *
     * @param null $data
     * @return ArquivoPedidoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $arquivo = new ArquivoPedidoImpugnacaoTO();

        if ($data != null) {
            $arquivo->setId(Arr::get($data, 'id'));
            $arquivo->setNome(Arr::get($data, 'nome'));
            $arquivo->setNomeFisico(Arr::get($data, 'nomeFisico'));
        }

        return $arquivo;
    }

}