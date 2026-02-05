<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\NamedQuery;
use Doctrine\ORM\Mapping\NamedQueries;
use Exception;

/**
 * Entidade de representação de 'MembroChapaSubstituicao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\MembroChapaSubstituicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_MEMBRO_CHAPA_SUBSTITUICAO")
 **@NamedQueries({
 *     @NamedQuery(name="activated", query="SELECT u FROM __CLASS__ u WHERE u.status = 1")
 * })
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class MembroChapaSubstituicao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_MEMBRO_CHAPA_SUBSTITUICAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_membro_chapa_substituicao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\MembroChapa")
     * @ORM\JoinColumn(name="ID_MEMBRO_CHAPA_SUBSTITUIDO", referencedColumnName="ID_MEMBRO_CHAPA")
     *
     * @var MembroChapa
     */
    private $membroChapaSubstituido;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\MembroChapa")
     * @ORM\JoinColumn(name="ID_MEMBRO_CHAPA_SUBSTITUTO", referencedColumnName="ID_MEMBRO_CHAPA")
     *
     * @var MembroChapa
     */
    private $membroChapaSubstituto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\PedidoSubstituicaoChapa")
     * @ORM\JoinColumn(name="ID_PEDIDO_SUBSTITUICAO_CHAPA", referencedColumnName="ID_PEDIDO_SUBSTITUICAO_CHAPA", nullable=false)
     *
     * @var PedidoSubstituicaoChapa
     */
    private $pedidoSubstituicaoChapa;

    /**
     * Fábrica de instância de 'Membro da Chapa'.
     *
     * @param array $data
     *
     * @return MembroChapaSubstituicao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setId(Utils::getValue('id', $data));

            $membroChapaSubstituido = Utils::getValue('membroChapaSubstituido', $data);
            if (!empty($membroChapaSubstituido) and is_array($membroChapaSubstituido)) {
                $instance->setMembroChapaSubstituido(MembroChapa::newInstance($membroChapaSubstituido));
            }

            $membroChapaSubstituto = Utils::getValue('membroChapaSubstituto', $data);
            if (!empty($membroChapaSubstituto) and is_array($membroChapaSubstituto)) {
                $instance->setMembroChapaSubstituto(MembroChapa::newInstance($membroChapaSubstituto));
            }

            $oedidoSubstituicaoChapa = Utils::getValue('pedidoSubstituicaoChapa', $data);
            if(!empty($oedidoSubstituicaoChapa) and is_array($oedidoSubstituicaoChapa)){
                $instance->setPedidoSubstituicaoChapa(PedidoSubstituicaoChapa::newInstance($oedidoSubstituicaoChapa));
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
     * @return MembroChapa
     */
    public function getMembroChapaSubstituido()
    {
        return $this->membroChapaSubstituido;
    }

    /**
     * @param MembroChapa $membroChapaSubstituido
     */
    public function setMembroChapaSubstituido($membroChapaSubstituido): void
    {
        $this->membroChapaSubstituido = $membroChapaSubstituido;
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
     * @return PedidoSubstituicaoChapa
     */
    public function getPedidoSubstituicaoChapa()
    {
        return $this->pedidoSubstituicaoChapa;
    }

    /**
     * @param PedidoSubstituicaoChapa $pedidoSubstituicaoChapa
     */
    public function setPedidoSubstituicaoChapa($pedidoSubstituicaoChapa): void
    {
        $this->pedidoSubstituicaoChapa = $pedidoSubstituicaoChapa;
    }
}
