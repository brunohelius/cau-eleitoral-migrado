<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'ArquivoPedidoImpugnacao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoPedidoImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_PEDIDO_IMPUGNACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoPedidoImpugnacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_PEDIDO_IMPUGNACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_pedido_impugnacao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="NM_ARQUIVO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $nome;

    /**
     * @ORM\Column(name="NM_FIS_ARQUIVO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $nomeFisico;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\PedidoImpugnacao")
     * @ORM\JoinColumn(name="ID_PEDIDO_IMPUGNACAO", referencedColumnName="ID_PEDIDO_IMPUGNACAO", nullable=false)
     *
     * @var PedidoImpugnacao
     */
    private $pedidoImpugnacao;

    /**
     * Transient.
     *
     * @var mixed
     */
    private $arquivo;

    /**
     * Transient.
     *
     * @var mixed
     */
    private $tamanho;

    /**
     * Fábrica de instância de 'ArquivoPedidoImpugnacao'.
     *
     * @param array $data
     * @return ArquivoPedidoImpugnacao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $arquivoPedidoImpugnacao = new ArquivoPedidoImpugnacao();

        if ($data != null) {
            $arquivoPedidoImpugnacao->setId(Utils::getValue('id', $data));
            $arquivoPedidoImpugnacao->setNome(Utils::getValue('nome', $data));
            $arquivoPedidoImpugnacao->setArquivo(Utils::getValue('arquivo', $data));
            $arquivoPedidoImpugnacao->setTamanho(Utils::getValue('tamanho', $data));
            $arquivoPedidoImpugnacao->setNomeFisico(Utils::getValue('nomeFisico', $data));

            $pedidoImpugnacao = Utils::getValue('pedidoImpugnacao', $data);
            if(!empty($pedidoImpugnacao)) {
                $arquivoPedidoImpugnacao->setPedidoImpugnacao(PedidoImpugnacao::newInstance($pedidoImpugnacao));
            }
        }
        return $arquivoPedidoImpugnacao;
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
}
