<?php
/*
 * TipoProcesso.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */


namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Entidade de representação de 'DefesaImpugnacao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DefesaImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DEFESA_IMPUGNACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DefesaImpugnacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DEFESA_IMPUGNACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_defesa_impugnacao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_DEFESA_IMPUGNACAO", type="string", nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="DT_CADASTRO", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $dataCadastro;

    /**
     * @ORM\Column(name="ID_PROFISSIONAL_INCLUSAO", type="integer")
     *
     * @var integer
     */
    private $idProfissionalInclusao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\PedidoImpugnacao")
     * @ORM\JoinColumn(name="ID_PEDIDO_IMPUGNACAO", referencedColumnName="ID_PEDIDO_IMPUGNACAO",unique=true, nullable=false)
     *
     * @var PedidoImpugnacao
     */
    private $pedidoImpugnacao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoDefesaImpugnacao", mappedBy="defesaImpugnacao")
     *
     * @var array|ArrayCollection
     */
    private $arquivos;

    /**
     * Fábrica de instância de RespostaDeclaracao.
     *
     * @param array $data
     * @return DefesaImpugnacao
     */
    public static function newInstance($data = null)
    {
        $defesaImpugnacao = new DefesaImpugnacao();

        if ($data != null) {
            $defesaImpugnacao->setId(Utils::getValue('id', $data));
            $defesaImpugnacao->setDescricao(Utils::getValue('descricao', $data));
            $defesaImpugnacao->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $defesaImpugnacao->setIdProfissionalInclusao(Utils::getValue('idProfissionalInclusao', $data));

            $pedidoImpugnacao = Utils::getValue('pedidoImpugnacao', $data);
            if (!empty($pedidoImpugnacao)) {
                $defesaImpugnacao->setPedidoImpugnacao(PedidoImpugnacao::newInstance($pedidoImpugnacao));
            }

            $arquivosDefesaImpugnacao = Utils::getValue('arquivos', $data);
            if (!empty($arquivosDefesaImpugnacao)) {
                $defesaImpugnacao->setArquivos(array_map(function ($arquivo) {
                    return ArquivoDefesaImpugnacao::newInstance($arquivo);
                }, $arquivosDefesaImpugnacao));
            }
        }
        return $defesaImpugnacao;
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
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @param DateTime $dataCadastro
     */
    public function setDataCadastro($dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return int
     */
    public function getIdProfissionalInclusao()
    {
        return $this->idProfissionalInclusao;
    }

    /**
     * @param int $idProfissionalInclusao
     */
    public function setIdProfissionalInclusao($idProfissionalInclusao): void
    {
        $this->idProfissionalInclusao = $idProfissionalInclusao;
    }

    /**
     * @return PedidoImpugnacao
     */
    public function getPedidoImpugnacao()
    {
        return $this->pedidoImpugnacao;
    }

    /**
     * @param PedidoImpugnacao $pedidoImpugnacao
     */
    public function setPedidoImpugnacao($pedidoImpugnacao): void
    {
        $this->pedidoImpugnacao = $pedidoImpugnacao;
    }


    /**
     * @return array|ArrayCollection
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param $arquivos
     */
    public function setArquivos($arquivos)
    {
        $this->arquivos = $arquivos;
    }
}