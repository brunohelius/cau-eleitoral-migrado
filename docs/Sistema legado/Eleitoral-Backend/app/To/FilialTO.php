<?php


namespace App\To;

use App\Util\Utils;
use Illuminate\Support\Arr;
use App\Entities\Filial;
use App\Entities\Calendario;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Classe de transferência para a Filial
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class FilialTO
{
    private $nome;
    private $cauUf;
    private $imagemBandeira;
    private $endereco;

    /**
     * @var int|null $id
     */
    private $id;

    /**
     * @var string|null
     */
    private $descricao;

    /**
     * @var string|null
     */
    private $prefixo;

    /**
     * @return Integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param Integer|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param string|null $descricao
     */
    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return string|null
     */
    public function getPrefixo(): ?string
    {
        return $this->prefixo;
    }

    /**
     * @param string|null $prefixo
     */
    public function setPrefixo(?string $prefixo): void
    {
        $this->prefixo = $prefixo;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getCauUf()
    {
        return $this->cauUf;
    }

    /**
     * @param mixed $cauUf
     */
    public function setCauUf($cauUf): void
    {
        $this->cauUf = $cauUf;
    }

    /**
     * @return mixed
     */
    public function getImagemBandeira()
    {
        return $this->imagemBandeira;
    }

    /**
     * @param mixed $imagemBandeira
     */
    public function setImagemBandeira($imagemBandeira): void
    {
        $this->imagemBandeira = $imagemBandeira;
    }

    /**
     * @return mixed
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * @param mixed $endereco
     */
    public function setEndereco($endereco): void
    {
        $this->endereco = $endereco;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalTO'.
     *
     * @param null $data
     * @return FilialTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $filialTO = new FilialTO();

        if ($data != null) {
            $filialTO->setId(Arr::get($data,'id'));
            $filialTO->setPrefixo(Arr::get($data,'prefixo'));
            $filialTO->setDescricao(Arr::get($data,'descricao'));

        }
        return $filialTO;
    }

    /**
     * Retorna uma nova instância de 'FilialTO'.
     *
     * @param Filial $filial
     * @return FilialTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($filial = null)
    {
        $filialTO = new FilialTO();

        if ($filial != null) {
            $filialTO->setId($filial->getId());
            $filialTO->setPrefixo($filial->getPrefixo());
            $filialTO->setDescricao($filial->getDescricao());
            $filialTO->setEndereco($filialTO->getEnderecoFromEntity($filial));
        }
        return $filialTO;
    }

    /**
     * Recupera o endereçod e uma filial concatenado a todos atributos de endereço
     * @param Filial $filial
     * @return string|null
     */
    public function  getEnderecoFromEntity(Filial $filial) {
        $endereco = $filial->getLogradouro();
        if(!empty($filial->getNumeroEndereco())) {
            $endereco .= " Nº ".$filial->getNumeroEndereco();
        }
        $endereco .= " ".$filial->getComplemento();
        $endereco .= " ".$filial->getBairro();
        $endereco .= " ".$filial->getCidade();
        $endereco .= "-".$filial->getUf();
        if(!empty($filial->getCep())) {
            $endereco .= " ".Utils::getCepFormatado($filial->getCep());
        }

        return $endereco;
    }
}
