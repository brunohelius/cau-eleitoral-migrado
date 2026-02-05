<?php

namespace App\To;

use Illuminate\Support\Arr;

/**
 * Classe de transferência para arquivos
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDescricaoTO
{
    /**
     * @var string
     */
    private $nome;

    /**
     * @var string
     */
    private $extensao;

    /**
     * @var string
     */
    private $tamanho;

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
    public function getExtensao(): string
    {
        return $this->extensao;
    }

    /**
     * @param string $extensao
     */
    public function setExtensao(string $extensao): void
    {
        $this->extensao = $extensao;
    }

    /**
     * @return string
     */
    public function getTamanho(): string
    {
        return $this->tamanho;
    }

    /**
     * @param string $tamanho
     */
    public function setTamanho(string $tamanho): void
    {
        $this->tamanho = $tamanho;
    }

    /**
     * Retorna uma nova instância de 'ArquivoDescricaoTO'.
     *
     * @param null $data
     * @return ArquivoDescricaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $arquivo = new ArquivoDescricaoTO();

        if ($data != null) {
            $arquivo->setNome(Arr::get($data, 'nome'));
            $arquivo->setExtensao(Arr::get($data, 'extensao'));
            $arquivo->setTamanho(Arr::get($data, 'tamanho'));
        }

        return $arquivo;
    }

}
