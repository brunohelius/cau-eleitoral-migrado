<?php
/*
 * DefesaImpugnacaoTO.php Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;


use App\Entities\ChapaEleicao;
use App\Entities\DefesaImpugnacao;
use App\Util\Utils;

/**
 * Classe de transferência associada a 'DefesaImpugnacaoT'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class DefesaImpugnacaoTO
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
    private $idPedidoImpugnacao;

    /**
     * @var array
     */
    private $arquivos;

    /**
     * @var int[]
     */
    private $idArquivosRemover;

    /**
     * @var int
     */
    private $idStatusPedidoImpugnacao;

    /**
     * @var string
     */
    private $descricaoStatusPedidoImpugnacao;

    /**
     * Fabricação estática de 'DefesaImpugnacaoTO'.
     *
     * @param array|null $data
     *
     * @return DefesaImpugnacaoTO
     */
    public static function newInstance($data = null)
    {
        $defesaTO = new DefesaImpugnacaoTO();

        if(!empty($data)) {
            $defesaTO->setId(Utils::getValue('id', $data));
            $defesaTO->setDescricao(Utils::getValue('descricao', $data));
            $defesaTO->setIdPedidoImpugnacao(Utils::getValue('idPedidoImpugnacao', $data));
            $defesaTO->setArquivos(Utils::getValue('arquivos', $data, []));
            $defesaTO->setIdArquivosRemover(Utils::getValue('idArquivosRemover', $data, []));
            $defesaTO->setIdStatusPedidoImpugnacao(Utils::getValue('statusId', $data));
            $defesaTO->setDescricaoStatusPedidoImpugnacao(Utils::getValue('statusDescricao', $data));
        }
        return $defesaTO;
    }


    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return int
     */
    public function getIdPedidoImpugnacao()
    {
        return $this->idPedidoImpugnacao;
    }

    /**
     * @param int $idPedidoImpugnacao
     */
    public function setIdPedidoImpugnacao($idPedidoImpugnacao): void
    {
        $this->idPedidoImpugnacao = $idPedidoImpugnacao;
    }

    /**
     * @return array
     */
    public function getArquivos(): ?array
    {
        return $this->arquivos;
    }

    /**
     * @param array $arquivos
     */
    public function setArquivos(?array $arquivos): void
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return int[]
     */
    public function getIdArquivosRemover(): ?array
    {
        return $this->idArquivosRemover;
    }

    /**
     * @param int[] $idArquivosRemover
     */
    public function setIdArquivosRemover(?array $idArquivosRemover): void
    {
        $this->idArquivosRemover = $idArquivosRemover;
    }

    /**
     * @return int
     */
    public function getIdStatusPedidoImpugnacao(): ?int
    {
        return $this->idStatusPedidoImpugnacao;
    }

    /**
     * @param int $idStatusPedidoImpugnacao
     */
    public function setIdStatusPedidoImpugnacao(?int $idStatusPedidoImpugnacao): void
    {
        $this->idStatusPedidoImpugnacao = $idStatusPedidoImpugnacao;
    }

    /**
     * @return string
     */
    public function getDescricaoStatusPedidoImpugnacao(): ?string
    {
        return $this->descricaoStatusPedidoImpugnacao;
    }

    /**
     * @param string $descricaoStatusPedidoImpugnacao
     */
    public function setDescricaoStatusPedidoImpugnacao(?string $descricaoStatusPedidoImpugnacao): void
    {
        $this->descricaoStatusPedidoImpugnacao = $descricaoStatusPedidoImpugnacao;
    }
}