<?php
/*
 * ImagemArqutetoTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada ao 'ImagemArqutetoTO'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ImagemArqutetoTO
{

    /**
     * @var string
     */
    private $nome;

    /**
     * @var string
     */
    private $nomeGerado;


    /**
     * @var string
     */
    private $tipo;

    /**
     * @var string
     */
    private $extensao;


    /**
     * Fabricação estática de 'ImagemArqutetoTO'.
     *
     * @param array $data
     *
     * @return ImagemArqutetoTO
     */
    public static function newInstance($data = null)
    {
        $imagemArquitetoTO = new ImagemArqutetoTO();

        if ($data != null) {
            $imagemArquitetoTO->setNome(Utils::getValue("nome", $data));
            $imagemArquitetoTO->setNomeGerado(Utils::getValue("nomeGerado", $data));
            $imagemArquitetoTO->setTipo(Utils::getValue("tipo", $data));
            $imagemArquitetoTO->setExtensao(Utils::getValue("extensao", $data));
        }

        return $imagemArquitetoTO;
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
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return string
     */
    public function getNomeGerado()
    {
        return $this->nomeGerado;
    }

    /**
     * @param string $nomeGerado
     */
    public function setNomeGerado($nomeGerado)
    {
        $this->nomeGerado = $nomeGerado;
    }

    /**
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param string $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * @return string
     */
    public function getExtensao()
    {
        return $this->extensao;
    }

    /**
     * @param string $extensao
     */
    public function setExtensao($extensao)
    {
        $this->extensao = $extensao;
    }
}

