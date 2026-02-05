<?php
/*
 * UsuarioTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use Illuminate\Support\Arr;

/**
 * Classe de transferência associada ao 'Item da declaração'
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ItemDeclaracaoTO
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $descricao;

    /**
     * @var integer
     */
    private $sequencial;

    /**
     * @var boolean
     */
    private $resposta;

    public static function newInstance($data = null)
    {
        $itemDeclaracaoTO = new ItemDeclaracaoTO();

        if ($data != null) {
            $itemDeclaracaoTO->setId(Arr::get($data, 'id'));
            $itemDeclaracaoTO->setDescricao(Arr::get($data, 'descricao'));
            $itemDeclaracaoTO->setSequencial(Arr::get($data, 'sequencial'));
            $itemDeclaracaoTO->setResposta(Arr::get($data, 'situacaoResposta'));
        }

        return $itemDeclaracaoTO;
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
    public function setId(?int $id)
    {
        $this->id = $id;
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
    public function setDescricao(?string $descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return int
     */
    public function getSequencial()
    {
        return $this->sequencial;
    }

    /**
     * @param int $sequencial
     */
    public function setSequencial(?int $sequencial)
    {
        $this->sequencial = $sequencial;
    }

    /**
     * @return bool
     */
    public function isResposta()
    {
        return $this->resposta;
    }

    /**
     * @param bool $resposta
     */
    public function setResposta(?bool $resposta): void
    {
        $this->resposta = $resposta;
    }
}
