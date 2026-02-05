<?php
/*
 * PedidoImpugnacao.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;


use App\Util\Utils;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Pedidos de Impugnação.'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\PedidoImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_PEDIDO_IMPUGNACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class PedidoImpugnacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_PEDIDO_IMPUGNACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_pedido_impugnacao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_PEDIDO_IMPUGNACAO", type="string", nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="NM_PROTOCOLO", type="integer", nullable=false)
     *
     * @var integer
     */
    private $numeroProtocolo;

    /**
     * @ORM\Column(name="DT_CADASTRO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataCadastro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_PROFISSIONAL_INCLUSAO", referencedColumnName="id", nullable=false)
     * @var Profissional
     */
    private $profissional;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\StatusPedidoImpugnacao")
     * @ORM\JoinColumn(name="ID_STATUS_PEDIDO_IMPUGNACAO", referencedColumnName="ID_STATUS_PEDIDO_IMPUGNACAO", nullable=false)
     *
     * @var StatusPedidoImpugnacao
     */
    private $statusPedidoImpugnacao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\MembroChapa")
     * @ORM\JoinColumn(name="ID_MEMBRO_CHAPA", referencedColumnName="ID_MEMBRO_CHAPA", nullable=false)
     *
     * @var MembroChapa
     */
    private $membroChapa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoPedidoImpugnacao", mappedBy="pedidoImpugnacao", fetch="EXTRA_LAZY", cascade={"remove"})
     *
     * @var array|ArrayCollection
     */
    private $arquivosPedidoImpugnacao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\DefesaImpugnacao", mappedBy="pedidoImpugnacao")
     *
     * @var DefesaImpugnacao
     */
    private $defesaImpugnacao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\SubstituicaoImpugnacao", mappedBy="pedidoImpugnacao", fetch="EXTRA_LAZY")
     *
     * @var SubstituicaoImpugnacao
     */
    private $substituicaoImpugnacao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoImpugnacao", mappedBy="pedidoImpugnacao")
     *
     * @var JulgamentoImpugnacao
     */
    private $julgamentoImpugnacao;

    /**
     * Fábrica de instância de 'Status de Participação da Chapa'.
     *
     * @param array $data
     * @return PedidoImpugnacao
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $pedidoImpugnacao = new PedidoImpugnacao();
        if ($data != null) {
            $pedidoImpugnacao->setId(Utils::getValue('id', $data));
            $pedidoImpugnacao->setDescricao(Utils::getValue('descricao', $data));
            $pedidoImpugnacao->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $pedidoImpugnacao->setNumeroProtocolo(Utils::getValue('numeroProtocolo', $data));

            $profissional = Utils::getValue('profissional', $data);
            if (!empty($profissional)) {
                $pedidoImpugnacao->setProfissional(Profissional::newInstance($profissional));
            }

            $statusPedidoImpugnacao = Utils::getValue('statusPedidoImpugnacao', $data);
            if (!empty($statusPedidoImpugnacao)) {
                $pedidoImpugnacao->setStatusPedidoImpugnacao(StatusPedidoImpugnacao::newInstance($statusPedidoImpugnacao));
            }

            $membroChapa = Utils::getValue('membroChapa', $data);
            if (!empty($membroChapa)) {
                $pedidoImpugnacao->setMembroChapa(MembroChapa::newInstance($membroChapa));
            }

            $arquivosPedidoImpugnacao = Utils::getValue('arquivosPedidoImpugnacao', $data, []);
            if (!empty($arquivosPedidoImpugnacao)) {
                $pedidoImpugnacao->setArquivosPedidoImpugnacao(array_map(function ($arquivo) {
                    return ArquivoPedidoImpugnacao::newInstance($arquivo);
                }, $arquivosPedidoImpugnacao));
            }

            $respostasDecPedidoImpugnacao = Utils::getValue('respostasDeclaracaoPedidoImpugnacao', $data, []);
            if (!empty($respostasDecPedidoImpugnacao)) {
                $pedidoImpugnacao->setRespostasDeclaracaoPedidoImpugnacao(array_map(function ($respostaDecPedido) {
                    return RespostaDeclaracaoPedidoImpugnacao::newInstance($respostaDecPedido);
                }, $respostasDecPedidoImpugnacao));
            }

            $substituicaoImpugnacao = Utils::getValue('substituicaoImpugnacao', $data);
            if (!empty($substituicaoImpugnacao)) {
                $pedidoImpugnacao->setSubstituicaoImpugnacao(SubstituicaoImpugnacao::newInstance($substituicaoImpugnacao));
            }

            $julgamentoImpugnacao = Utils::getValue('julgamentoImpugnacao', $data);
            if (!empty($julgamentoImpugnacao)) {
                $pedidoImpugnacao->setJulgamentoImpugnacao(JulgamentoImpugnacao::newInstance($julgamentoImpugnacao));
            }
        }
        return $pedidoImpugnacao;
    }

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\RespostaDeclaracaoPedidoImpugnacao", mappedBy="pedidoImpugnacao", fetch="EXTRA_LAZY", cascade={"remove"})
     *
     * @var RespostaDeclaracaoPedidoImpugnacao[]|array|ArrayCollection
     */
    private $respostasDeclaracaoPedidoImpugnacao;

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
     * @return int
     */
    public function getNumeroProtocolo()
    {
        return $this->numeroProtocolo;
    }

    /**
     * @param int $numeroProtocolo
     */
    public function setNumeroProtocolo($numeroProtocolo): void
    {
        $this->numeroProtocolo = $numeroProtocolo;
    }

    /**
     * @return DateTime
     */
    public function getDataCadastro(): ?DateTime
    {
        return $this->dataCadastro;
    }

    /**
     * @param DateTime $dataCadastro
     */
    public function setDataCadastro(?DateTime $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return StatusPedidoImpugnacao
     */
    public function getStatusPedidoImpugnacao()
    {
        return $this->statusPedidoImpugnacao;
    }

    /**
     * @param StatusPedidoImpugnacao $statusPedidoImpugnacao
     */
    public function setStatusPedidoImpugnacao($statusPedidoImpugnacao): void
    {
        $this->statusPedidoImpugnacao = $statusPedidoImpugnacao;
    }

    /**
     * @return MembroChapa
     */
    public function getMembroChapa()
    {
        return $this->membroChapa;
    }

    /**
     * @param MembroChapa $membroChapa
     */
    public function setMembroChapa($membroChapa): void
    {
        $this->membroChapa = $membroChapa;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivosPedidoImpugnacao()
    {
        return $this->arquivosPedidoImpugnacao;
    }

    /**
     * @param array|ArrayCollection $arquivosPedidoImpugnacao
     */
    public function setArquivosPedidoImpugnacao($arquivosPedidoImpugnacao): void
    {
        $this->arquivosPedidoImpugnacao = $arquivosPedidoImpugnacao;
    }

    /**
     * @return RespostaDeclaracaoPedidoImpugnacao[]|array|ArrayCollection
     */
    public function getRespostasDeclaracaoPedidoImpugnacao()
    {
        return $this->respostasDeclaracaoPedidoImpugnacao;
    }

    /**
     * @param RespostaDeclaracaoPedidoImpugnacao[]|array|ArrayCollection $respostasDeclaracaoPedidoImpugnacao
     */
    public function setRespostasDeclaracaoPedidoImpugnacao($respostasDeclaracaoPedidoImpugnacao): void
    {
        $this->respostasDeclaracaoPedidoImpugnacao = $respostasDeclaracaoPedidoImpugnacao;
    }

    /**
     * @return Profissional
     */
    public function getProfissional(): ?Profissional
    {
        return $this->profissional;
    }

    /**
     * @param Profissional $profissional
     */
    public function setProfissional(?Profissional $profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return DefesaImpugnacao
     */
    public function getDefesaImpugnacao(): ?DefesaImpugnacao
    {
        return $this->defesaImpugnacao;
    }

    /**
     * @param DefesaImpugnacao $defesaImpugnacao
     */
    public function setDefesaImpugnacao(?DefesaImpugnacao $defesaImpugnacao): void
    {
        $this->defesaImpugnacao = $defesaImpugnacao;
    }

    /**
     * @return SubstituicaoImpugnacao
     */
    public function getSubstituicaoImpugnacao()
    {
        return $this->substituicaoImpugnacao;
    }

    /**
     * @param SubstituicaoImpugnacao $substituicaoImpugnacao
     */
    public function setSubstituicaoImpugnacao(SubstituicaoImpugnacao $substituicaoImpugnacao): void
    {
        $this->substituicaoImpugnacao = $substituicaoImpugnacao;
    }

    /**
     * @return JulgamentoImpugnacao
     */
    public function getJulgamentoImpugnacao()
    {
        return $this->julgamentoImpugnacao;
    }

    /**
     * @param JulgamentoImpugnacao $julgamentoImpugnacao
     */
    public function setJulgamentoImpugnacao($julgamentoImpugnacao): void
    {
        $this->julgamentoImpugnacao = $julgamentoImpugnacao;
    }

}
