<?php
namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;

use App\Entities\CorpoEmail;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\EmailAtividadeSecundariaTipo;

/**
 * Entidade que representa 'RespostaDeclaracaoPedidoImpugnacao'.
 * 
 * @ORM\Entity(repositoryClass="App\Repository\RespostaDeclaracaoPedidoImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_RESPOSTA_DECLARACAO_PEDIDO_IMPUGNACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class RespostaDeclaracaoPedidoImpugnacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_RESPOSTA_DECLARACAO_PEDIDO_IMPUGNACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_resposta_declaracao_pedido_impugnacao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\RespostaDeclaracao")
     * @ORM\JoinColumn(name="ID_RESPOSTA_DECLARACAO", referencedColumnName="ID_RESPOSTA_DECLARACAO", nullable=false)
     *
     * @var RespostaDeclaracao
     */
    private $respostaDeclaracao;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\PedidoImpugnacao")
     * @ORM\JoinColumn(name="ID_PEDIDO_IMPUGNACAO", referencedColumnName="ID_PEDIDO_IMPUGNACAO", nullable=false)
     *
     * @var PedidoImpugnacao
     */
    private $pedidoImpugnacao;

    /**
     * Fábrica de instância de RespostaDeclaracaoPedidoImpugnacao
     * @param array $data
     * @return RespostaDeclaracaoPedidoImpugnacao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $respostaDeclaracaoPedidoImpugnacao = new RespostaDeclaracaoPedidoImpugnacao();
        if(!empty($data)){      
            $respostaDeclaracaoPedidoImpugnacao->setId(Utils::getValue('id', $data));

            $respostaDeclaracao = Utils::getValue('respostaDeclaracao', $data);
            if(!empty($respostaDeclaracao)) {
                $respostaDeclaracaoPedidoImpugnacao->setRespostaDeclaracao(
                    RespostaDeclaracao::newInstance($respostaDeclaracao)
                );
            }

            $pedidoImpugnacao = Utils::getValue('pedidoImpugnacao', $data);
            if(!empty($pedidoImpugnacao)) {
                $respostaDeclaracaoPedidoImpugnacao->setPedidoImpugnacao(
                    PedidoImpugnacao::newInstance($pedidoImpugnacao)
                );
            }

        }
        return $respostaDeclaracaoPedidoImpugnacao;
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
     * @return RespostaDeclaracao
     */
    public function getRespostaDeclaracao()
    {
        return $this->respostaDeclaracao;
    }

    /**
     * @param RespostaDeclaracao $respostaDeclaracao
     */
    public function setRespostaDeclaracao($respostaDeclaracao): void
    {
        $this->respostaDeclaracao = $respostaDeclaracao;
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

