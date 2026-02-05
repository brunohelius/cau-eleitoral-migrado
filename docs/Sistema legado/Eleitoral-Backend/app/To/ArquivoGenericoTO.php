<?php


namespace App\To;

use Illuminate\Support\Arr;

/**
 * Classe de transferência para arquivos
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ArquivoGenericoTO
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
     * @var mixed
     */
    private $arquivo;

    /**
     * @var $tamanho
     */
    private $tamanho;

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
     * @return mixed
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * @param mixed $arquivo
     */
    public function setArquivo($arquivo): void
    {
        $this->arquivo = $arquivo;
    }

    /**
     * @return mixed
     */
    public function getTamanho()
    {
        return $this->tamanho;
    }

    /**
     * @param mixed $tamanho
     */
    public function setTamanho($tamanho): void
    {
        $this->tamanho = $tamanho;
    }

    /**
     * Retorna uma nova instância de 'ArquivoGenericoTO'.
     *
     * @param null $data
     * @return ArquivoGenericoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $arquivo = new ArquivoGenericoTO();

        if ($data != null) {
            $arquivo->setId(Arr::get($data, 'id'));
            $arquivo->setNome(Arr::get($data, 'nome'));
            $arquivo->setNomeFisico(Arr::get($data, 'nomeFisico'));
            $arquivo->setArquivo(Arr::get($data, 'arquivo'));
            $arquivo->setTamanho(Arr::get($data, 'tamanho'));
        }

        return $arquivo;
    }

}
