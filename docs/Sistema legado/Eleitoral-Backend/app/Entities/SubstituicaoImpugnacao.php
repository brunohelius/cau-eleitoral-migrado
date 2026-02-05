<?php

namespace App\Entities;

use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'SubstituicaoImpugnacao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\SubstituicaoImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_SUBSTITUICAO_IMPUGNACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class SubstituicaoImpugnacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_SUBSTITUICAO_IMPUGNACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_SUBSTITUICAO_IMPUGNACAO_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DT_CADASTRO", type="datetime", nullable=false)
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
     * @ORM\OneToOne(targetEntity="App\Entities\MembroChapa")
     * @ORM\JoinColumn(name="ID_MEMBRO_CHAPA_SUBSTITUTO", referencedColumnName="ID_MEMBRO_CHAPA")
     *
     * @var MembroChapa
     */
    private $membroChapaSubstituto;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\PedidoImpugnacao")
     * @ORM\JoinColumn(name="ID_PEDIDO_IMPUGNACAO", referencedColumnName="ID_PEDIDO_IMPUGNACAO", nullable=false)
     *
     * @var PedidoImpugnacao
     */
    private $pedidoImpugnacao;

    /**
     * Fábrica de instância de 'SubstituicaoImpugnacao'.
     *
     * @param array $data
     *
     * @return SubstituicaoImpugnacao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setDataCadastro(Utils::getValue('dataCadastro', $data));

            $profissional = Utils::getValue('profissional', $data);
            if(!empty($profissional)){
                $instance->setProfissional(Profissional::newInstance($profissional));
            }

            $membroChapaSubstituto = Utils::getValue('membroChapaSubstituto', $data);
            if (!empty($membroChapaSubstituto) and is_array($membroChapaSubstituto)) {
                $instance->setMembroChapaSubstituto(MembroChapa::newInstance($membroChapaSubstituto));
            }

            $pedidoImpugnacao = Utils::getValue('pedidoImpugnacao', $data);
            if(!empty($pedidoImpugnacao) and is_array($pedidoImpugnacao)){
                $instance->setPedidoImpugnacao(PedidoImpugnacao::newInstance($pedidoImpugnacao));
            }
        }
        return $instance;
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
     * @return Profissional
     */
    public function getProfissional()
    {
        return $this->profissional;
    }

    /**
     * @param Profissional $profissional
     */
    public function setProfissional($profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return MembroChapa
     */
    public function getMembroChapaSubstituto()
    {
        return $this->membroChapaSubstituto;
    }

    /**
     * @param MembroChapa $membroChapaSubstituto
     */
    public function setMembroChapaSubstituto($membroChapaSubstituto): void
    {
        $this->membroChapaSubstituto = $membroChapaSubstituto;
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
}
