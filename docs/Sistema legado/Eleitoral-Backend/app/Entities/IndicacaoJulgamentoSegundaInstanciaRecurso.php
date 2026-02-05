<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'Indicacao para o Julgamento Segunda Instancia Recurso'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\IndicacaoJulgamentoSegundaInstanciaRecursoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_INDICACAO_JULGAMENTO_SEGUNDA_INSTANCIA_RECURSO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class IndicacaoJulgamentoSegundaInstanciaRecurso extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_INDICACAO_JULGAMENTO_SEGUNDA_INSTANCIA_RECURSO_ID_SEQ", initialValue=1, allocationSize=1)
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
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoSegundaInstanciaRecurso")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_SEGUNDA_INSTANCIA_RECURSO", referencedColumnName="ID", nullable=true)
     *
     * @var JulgamentoSegundaInstanciaRecurso
     */
    private $julgamentoSegundaInstanciaRecurso;

    /**
     * Fábrica de instância de 'IndicacaoJulgamentoSegundaInstanciaRecurso'.
     *
     * @param array $data
     *
     * @return IndicacaoJulgamentoSegundaInstanciaRecurso
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

            $julgamentoSegundaInstanciaRecurso = Utils::getValue('julgamentoSegundaInstanciaRecurso', $data);
            if(!empty($julgamentoSegundaInstanciaRecurso)){
                $instance->setJulgamentoSegundaInstanciaRecurso(JulgamentoSegundaInstanciaRecurso::newInstance(
                    $julgamentoSegundaInstanciaRecurso
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
     * @return JulgamentoSegundaInstanciaRecurso
     */
    public function getJulgamentoSegundaInstanciaRecurso()
    {
        return $this->julgamentoSegundaInstanciaRecurso;
    }

    /**
     * @param JulgamentoSegundaInstanciaRecurso $julgamentoSegundaInstanciaRecurso
     */
    public function setJulgamentoSegundaInstanciaRecurso($julgamentoSegundaInstanciaRecurso)
    {
        $this->julgamentoSegundaInstanciaRecurso = $julgamentoSegundaInstanciaRecurso;
    }


}
