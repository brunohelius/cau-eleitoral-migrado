<?php
/*
 * Filial.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;


use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use OpenApi\Annotations as OA;

/**
 * Entidade de representação da 'Filial' / 'CAU/UF'
 *
 * @ORM\Entity(repositoryClass="App\Repository\FilialRepository")
 * @ORM\Table(schema="public", name="tb_filial")
 *
 * @OA\Schema(schema="Filial")
 *
 * @package App\Entities
 */
class Filial extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     *
     * @OA\Property()
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="prefixo", type="string", nullable=false)
     *
     * @OA\Property()
     * @var string
     */
    private $prefixo;

    /**
     * @ORM\Column(name="descricao", type="string", nullable=false)
     *
     * @OA\Property()
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="cnpj", type="string", nullable=false)
     *
     * @OA\Property()
     * @var string
     */
    private $cnpj;

    /**
     * @OA\Property()A
     * @var string
     */
    private $imagemBandeira;

    /**
     *
     * @ORM\Column(name="filial_id", type="integer")
     *
     * @OA\Property()
     * @var integer
     */
    private $filialId;

    /**
     *
     * @ORM\Column(name="tipofilial_id", type="integer")
     *
     * @OA\Property()
     * @var integer
     */
    private $tipoFilialId;

    /**
     * Id do Calendairo.
     *
     * @var integer
     */
    private $idCalendario;

    /**
     *
     * @ORM\Column(name="enderecologradouro", type="string")
     *
     * @OA\Property()
     * @var string|null
     */
    private $logradouro;

    /**
     *
     * @ORM\Column(name="endereconumero", type="string")
     *
     * @OA\Property()
     * @var string|null
     */
    private $numeroEndereco;

    /**
     *
     * @ORM\Column(name="enderecobairro", type="string")
     *
     * @OA\Property()
     * @var string|null
     */
    private $bairro;

    /**
     *
     * @ORM\Column(name="enderecocomplemento", type="string")
     *
     * @OA\Property()
     * @var string|null
     */
    private $complemento;

    /**
     *
     * @ORM\Column(name="enderecocidade", type="string")
     *
     * @OA\Property()
     * @var string|null
     */
    private $cidade;

    /**
     *
     * @ORM\Column(name="enderecouf", type="string")
     *
     * @OA\Property()
     * @var string|null
     */
    private $uf;

    /**
     *
     * @ORM\Column(name="enderecocep", type="string")
     *
     * @OA\Property()
     * @var string|null
     */
    private $cep;

    /**
     * Fábrica de instância de Filial.
     *
     * @param array $data
     * @return Filial
     */
    public static function newInstance($data = null)
    {
        $filial = new Filial();

        if ($data != null) {
            $filial->setId(Utils::getValue('id', $data));
            $filial->setCnpj(Utils::getValue('cnpj', $data));
            $filial->setPrefixo(Utils::getValue('prefixo', $data));
            $filial->setDescricao(Utils::getValue('descricao', $data));
            $filial->setImagemBandeira(Utils::getValue('imagemBandeira', $data));
            $filial->setBairro(Utils::getValue('enderecobairro', $data));
            $filial->setLogradouro(Utils::getValue('enderecologradouro', $data));
            $filial->setNumeroEndereco(Utils::getValue('endereconumero', $data));
            $filial->setComplemento(Utils::getValue('enderecocomplemento', $data));
            $filial->setCidade(Utils::getValue('enderecocidade', $data));
            $filial->setUf(Utils::getValue('enderecouf', $data));
            $filial->setCep(Utils::getValue('enderecocep', $data));
            $filial->setTipoFilial(Utils::getValue('tipoFilialId', $data));
        }

        return $filial;
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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getPrefixo()
    {
        return $this->prefixo;
    }

    /**
     * @param string $prefixo
     */
    public function setPrefixo($prefixo)
    {
        $this->prefixo = $prefixo;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return string
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * @param string $cnpj
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;
    }

    /**
     * @return string
     */
    public function getImagemBandeira()
    {
        return $this->imagemBandeira;
    }

    /**
     * @param string $imagemBandeira
     */
    public function setImagemBandeira($imagemBandeira)
    {
        $this->imagemBandeira = $imagemBandeira;
    }

    /**
     * @return string|null
     */
    public function getLogradouro(): ?string
    {
        return $this->logradouro;
    }

    /**
     * @param string|null $logradouro
     */
    public function setLogradouro(?string $logradouro): void
    {
        $this->logradouro = $logradouro;
    }

    /**
     * @return string|null
     */
    public function getNumeroEndereco(): ?string
    {
        return $this->numeroEndereco;
    }

    /**
     * @param string|null $numeroEndereco
     */
    public function setNumeroEndereco(?string $numeroEndereco): void
    {
        $this->numeroEndereco = $numeroEndereco;
    }

    /**
     * @return string|null
     */
    public function getBairro(): ?string
    {
        return $this->bairro;
    }

    /**
     * @param string|null $bairro
     */
    public function setBairro(?string $bairro): void
    {
        $this->bairro = $bairro;
    }

    /**
     * @return string|null
     */
    public function getComplemento(): ?string
    {
        return $this->complemento;
    }

    /**
     * @param string|null $complemento
     */
    public function setComplemento(?string $complemento): void
    {
        $this->complemento = $complemento;
    }

    /**
     * @return string|null
     */
    public function getCidade(): ?string
    {
        return $this->cidade;
    }

    /**
     * @param string|null $cidade
     */
    public function setCidade(?string $cidade): void
    {
        $this->cidade = $cidade;
    }

    /**
     * @return string|null
     */
    public function getUf(): ?string
    {
        return $this->uf;
    }

    /**
     * @param string|null $uf
     */
    public function setUf(?string $uf): void
    {
        $this->uf = $uf;
    }

    /**
     * @return string|null
     */
    public function getCep(): ?string
    {
        return $this->cep;
    }

    /**
     * @param string|null $cep
     */
    public function setCep(?string $cep): void
    {
        $this->cep = $cep;
    }

    /**
     * @return int
     */
    public function getIdCalendario()
    {
        return $this->idCalendario;
    }

    /**
     * @param int $idCalendario
     */
    public function setIdCalendario(?int $idCalendario): void
    {
        $this->idCalendario = $idCalendario;
    }

    /**
     * @return int
     */
    public function getTipoFilial()
    {
        return $this->id;
    }

    /**
     * @param int $tipoFiliaId
     */
    public function setTipoFilial($tipoFilialId)
    {
        $this->tipoFilialId = $tipoFilialId;
    }

}
