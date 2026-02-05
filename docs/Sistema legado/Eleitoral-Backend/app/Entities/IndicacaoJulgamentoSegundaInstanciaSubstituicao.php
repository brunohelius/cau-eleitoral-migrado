<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'IndicacaoJulgamentoSegundaInstanciaSubstituicao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\IndicacaoJulgamentoSegundaInstanciaSubstituicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_INDICACAO_JULGAMENTO_SEGUNDA_INSTANCIA_SUBSTITUICAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class IndicacaoJulgamentoSegundaInstanciaSubstituicao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_INDICACAO_JULGAMENTO_SEGUNDA_INSTANCIA_SUBSTITUICAO_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="NR_ORDEM", type="integer", length=11)
     *
     * @var integer
     */
    private $numeroOrdem;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoParticipacaoChapa")
     * @ORM\JoinColumn(name="ID_TP_PARTIC_CHAPA", referencedColumnName="ID_TP_PARTIC_CHAPA", nullable=false)
     *
     * @var TipoParticipacaoChapa
     */
    private $tipoParticipacaoChapa;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\MembroChapa")
     * @ORM\JoinColumn(name="ID_MEMBRO_CHAPA", referencedColumnName="ID_MEMBRO_CHAPA", nullable=true)
     *
     * @var MembroChapa
     */
    private $membroChapa;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoSegundaInstanciaSubstituicao")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_SEGUNDA_INSTANCIA_SUBSTITUICAO", referencedColumnName="ID", nullable=true)
     *
     * @var JulgamentoSegundaInstanciaSubstituicao
     */
    private $julgamentoSegundaInstanciaSubstituicao;

    /**
     * Fábrica de instância de 'IndicacaoJulgamentoSegundaInstanciaSubstituicao'.
     *
     * @param array $data
     *
     * @return IndicacaoJulgamentoSegundaInstanciaSubstituicao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setNumeroOrdem(Utils::getValue('numeroOrdem', $data));

            $tipoParticipacaoChapa = Utils::getValue('tipoParticipacaoChapa', $data);
            if(!empty($tipoParticipacaoChapa) and is_array($tipoParticipacaoChapa)){
                $instance->setTipoParticipacaoChapa(TipoParticipacaoChapa::newInstance($tipoParticipacaoChapa));
            }

            $membroChapa = Utils::getValue('membroChapa', $data);
            if(!empty($membroChapa) and is_array($membroChapa)){
                $instance->setMembroChapa(MembroChapa::newInstance($membroChapa));
            }

            $julgamentoSegundaInstanciaSubstituicao = Utils::getValue('julgamentoSegundaInstanciaSubstituicao', $data);
            if(!empty($julgamentoSegundaInstanciaSubstituicao)){
                $instance->setJulgamentoSegundaInstanciaSubstituicao(JulgamentoSegundaInstanciaSubstituicao::newInstance(
                    $julgamentoSegundaInstanciaSubstituicao
                ));
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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return TipoParticipacaoChapa
     */
    public function getTipoParticipacaoChapa()
    {
        return $this->tipoParticipacaoChapa;
    }

    /**
     * @param TipoParticipacaoChapa $tipoParticipacaoChapa
     */
    public function setTipoParticipacaoChapa($tipoParticipacaoChapa)
    {
        $this->tipoParticipacaoChapa = $tipoParticipacaoChapa;
    }

    /**
     * @return int
     */
    public function getNumeroOrdem()
    {
        return $this->numeroOrdem;
    }

    /**
     * @param int $numeroOrdem
     */
    public function setNumeroOrdem($numeroOrdem)
    {
        $this->numeroOrdem = $numeroOrdem;
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
    public function setMembroChapa($membroChapa)
    {
        $this->membroChapa = $membroChapa;
    }

    /**
     * @return JulgamentoSegundaInstanciaSubstituicao
     */
    public function getJulgamentoSegundaInstanciaSubstituicao()
    {
        return $this->julgamentoSegundaInstanciaSubstituicao;
    }

    /**
     * @param JulgamentoSegundaInstanciaSubstituicao $julgamentoSegundaInstanciaSubstituicao
     */
    public function setJulgamentoSegundaInstanciaSubstituicao($julgamentoSegundaInstanciaSubstituicao)
    {
        $this->julgamentoSegundaInstanciaSubstituicao = $julgamentoSegundaInstanciaSubstituicao;
    }

}
