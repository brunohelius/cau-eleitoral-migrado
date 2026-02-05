<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'MembroSubstituicaoJulgamentoFinal'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\MembroSubstituicaoJulgamentoFinalRepository")
 * @ORM\Table(schema="eleitoral", name="TB_MEMBRO_SUBSTITUICAO_JULGAMENTO_FINAL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class MembroSubstituicaoJulgamentoFinal extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_MEMBRO_SUBSTITUICAO_JULGAMENTO_FINAL_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\IndicacaoJulgamentoFinal")
     * @ORM\JoinColumn(name="ID_INDICACAO_JULGAMENTO_FINAL", referencedColumnName="ID", nullable=true)
     * @var IndicacaoJulgamentoFinal
     */
    private $indicacaoJulgamentoFinal;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\IndicacaoJulgamentoRecursoPedidoSubstituicao")
     * @ORM\JoinColumn(name="ID_INDICACAO_JULGAMENTO_RECURSO_PEDIDO_SUBSTITUICAO", referencedColumnName="ID", nullable=true)
     * @var IndicacaoJulgamentoRecursoPedidoSubstituicao
     */
    private $indicacaoJulgamentoRecursoPedidoSubstituicao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\IndicacaoJulgamentoSegundaInstanciaRecurso")
     * @ORM\JoinColumn(name="ID_INDICACAO_JULGAMENTO_SEGUNDA_INSTANCIA_RECURSO", referencedColumnName="ID", nullable=true)
     * @var IndicacaoJulgamentoSegundaInstanciaRecurso
     */
    private $indicacaoJulgamentoSegundaInstanciaRecurso;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\IndicacaoJulgamentoSegundaInstanciaSubstituicao")
     * @ORM\JoinColumn(name="ID_INDICACAO_JULGAMENTO_SEGUNDA_INSTANCIA_SUBSTITUICAO", referencedColumnName="ID", nullable=true)
     * @var IndicacaoJulgamentoSegundaInstanciaSubstituicao
     */
    private $indicacaoJulgamentoSegundaInstanciaSubstituicao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\MembroChapa")
     * @ORM\JoinColumn(name="ID_MEMBRO_CHAPA", referencedColumnName="ID_MEMBRO_CHAPA", nullable=true)
     *
     * @var MembroChapa
     */
    private $membroChapa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\SubstituicaoJulgamentoFinal")
     * @ORM\JoinColumn(name="ID_SUBSTITUICAO_JULGAMENTO_FINAL", referencedColumnName="ID", nullable=false)
     *
     * @var SubstituicaoJulgamentoFinal
     */
    private $substituicaoJulgamentoFinal;

    /**
     * Fábrica de instância de 'MembroSubstituicaoJulgamentoFinal'.
     *
     * @param array $data
     * @return MembroSubstituicaoJulgamentoFinal
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $membroSubstituicaoJulgamentoFinal = new MembroSubstituicaoJulgamentoFinal();

        if ($data != null) {
            $membroSubstituicaoJulgamentoFinal->setId(Utils::getValue('id', $data));

            $substituicaoJulgamentoFinal = Utils::getValue('substituicaoJulgamentoFinal', $data);
            if (!empty($substituicaoJulgamentoFinal)) {
                $membroSubstituicaoJulgamentoFinal->setSubstituicaoJulgamentoFinal(
                    SubstituicaoJulgamentoFinal::newInstance($substituicaoJulgamentoFinal)
                );
            }

            $membroChapa = Utils::getValue('membroChapa', $data);
            if(!empty($membroChapa) and is_array($membroChapa)){
                $membroSubstituicaoJulgamentoFinal->setMembroChapa(MembroChapa::newInstance($membroChapa));
            }

            $indicacaoJulgamentoFinal = Utils::getValue('indicacaoJulgamentoFinal', $data);
            if (!empty($indicacaoJulgamentoFinal)) {
                $membroSubstituicaoJulgamentoFinal->setIndicacaoJulgamentoFinal(
                    IndicacaoJulgamentoFinal::newInstance($indicacaoJulgamentoFinal)
                );
            }

            $indicacaoJulgamentoRecursoPedidoSubstituicao = Utils::getValue('indicacaoJulgamentoRecursoPedidoSubstituicao', $data);
            if (!empty($indicacaoJulgamentoRecursoPedidoSubstituicao)) {
                $membroSubstituicaoJulgamentoFinal->setIndicacaoJulgamentoRecursoPedidoSubstituicao(
                    IndicacaoJulgamentoRecursoPedidoSubstituicao::newInstance($indicacaoJulgamentoRecursoPedidoSubstituicao)
                );
            }

            $indicacaoJulgamentoSegundaInstanciaRecurso = Utils::getValue('indicacaoJulgamentoSegundaInstanciaRecurso', $data);
            if (!empty($indicacaoJulgamentoSegundaInstanciaRecurso)) {
                $membroSubstituicaoJulgamentoFinal->setIndicacaoJulgamentoSegundaInstanciaRecurso(
                    IndicacaoJulgamentoSegundaInstanciaRecurso::newInstance($indicacaoJulgamentoSegundaInstanciaRecurso)
                );
            }

            $indicacaoJulgamentoSegundaInstanciaSubstituicao = Utils::getValue('indicacaoJulgamentoSegundaInstanciaSubstituicao', $data);
            if (!empty($indicacaoJulgamentoSegundaInstanciaSubstituicao)) {
                $membroSubstituicaoJulgamentoFinal->setIndicacaoJulgamentoSegundaInstanciaSubstituicao(
                    IndicacaoJulgamentoSegundaInstanciaSubstituicao::newInstance($indicacaoJulgamentoSegundaInstanciaSubstituicao)
                );
            }
        }
        return $membroSubstituicaoJulgamentoFinal;
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
     * @return IndicacaoJulgamentoFinal
     */
    public function getIndicacaoJulgamentoFinal()
    {
        return $this->indicacaoJulgamentoFinal;
    }

    /**
     * @param IndicacaoJulgamentoFinal $indicacaoJulgamentoFinal
     */
    public function setIndicacaoJulgamentoFinal($indicacaoJulgamentoFinal): void
    {
        $this->indicacaoJulgamentoFinal = $indicacaoJulgamentoFinal;
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
     * @return SubstituicaoJulgamentoFinal
     */
    public function getSubstituicaoJulgamentoFinal()
    {
        return $this->substituicaoJulgamentoFinal;
    }

    /**
     * @param SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
     */
    public function setSubstituicaoJulgamentoFinal($substituicaoJulgamentoFinal): void
    {
        $this->substituicaoJulgamentoFinal = $substituicaoJulgamentoFinal;
    }

    /**
     * @return IndicacaoJulgamentoRecursoPedidoSubstituicao
     */
    public function getIndicacaoJulgamentoRecursoPedidoSubstituicao()
    {
        return $this->indicacaoJulgamentoRecursoPedidoSubstituicao;
    }

    /**
     * @param IndicacaoJulgamentoRecursoPedidoSubstituicao $indicacaoJulgamentoRecursoPedidoSubstituicao
     */
    public function setIndicacaoJulgamentoRecursoPedidoSubstituicao($indicacaoJulgamentoRecursoPedidoSubstituicao): void {
        $this->indicacaoJulgamentoRecursoPedidoSubstituicao = $indicacaoJulgamentoRecursoPedidoSubstituicao;
    }

    /**
     * @return IndicacaoJulgamentoSegundaInstanciaRecurso
     */
    public function getIndicacaoJulgamentoSegundaInstanciaRecurso()
    {
        return $this->indicacaoJulgamentoSegundaInstanciaRecurso;
    }

    /**
     * @param IndicacaoJulgamentoSegundaInstanciaRecurso $indicacaoJulgamentoSegundaInstanciaRecurso
     */
    public function setIndicacaoJulgamentoSegundaInstanciaRecurso($indicacaoJulgamentoSegundaInstanciaRecurso): void {
        $this->indicacaoJulgamentoSegundaInstanciaRecurso = $indicacaoJulgamentoSegundaInstanciaRecurso;
    }

    /**
     * @return IndicacaoJulgamentoSegundaInstanciaSubstituicao
     */
    public function getIndicacaoJulgamentoSegundaInstanciaSubstituicao()
    {
        return $this->indicacaoJulgamentoSegundaInstanciaSubstituicao;
    }

    /**
     * @param IndicacaoJulgamentoSegundaInstanciaSubstituicao $indicacaoJulgamentoSegundaInstanciaSubstituicao
     */
    public function setIndicacaoJulgamentoSegundaInstanciaSubstituicao($indicacaoJulgamentoSegundaInstanciaSubstituicao): void {
        $this->indicacaoJulgamentoSegundaInstanciaSubstituicao = $indicacaoJulgamentoSegundaInstanciaSubstituicao;
    }
}
