<?php

namespace App\To;


use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe de transferência associada a tabela de número de membros da 'ComissaoMembro'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class NumeroMembroTO
{

    /**
     * @var integer
     */
    private $idCauUf;

    /**
     * @var string
     */
    private $prefixo;

    /**
     * @var string
     */
    private $descricao;

    /**
     * @var integer
     */
    private $quantidade;

    /**
     * Retorna uma nova instância de 'NumeroMembroTO.
     *
     * @param null $data
     * @return NumeroMembroTO
     */
    public static function newInstance($data = null)
    {
        $numeroMembroTO = new NumeroMembroTO();

        if ($data != null) {
            $numeroMembroTO->setIdCauUf(Utils::getValue('idCauUf', $data));
            $numeroMembroTO->setPrefixo(Utils::getValue('prefixo', $data));
            $numeroMembroTO->setDescricao(Utils::getValue('descricao', $data));
            $numeroMembroTO->setQuantidade(Utils::getValue('quantidade', $data));
        }

        return $numeroMembroTO;
    }

    /**
     * @return int
     */
    public function getIdCauUf()
    {
        return $this->idCauUf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf($idCauUf)
    {
        $this->idCauUf = $idCauUf;
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
     * @return int
     */
    public function getQuantidade()
    {
        return $this->quantidade;
    }

    /**
     * @param int $quantidade
     */
    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;
    }

}
