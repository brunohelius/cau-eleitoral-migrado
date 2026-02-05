<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'Indicacao para o Julgamento Final'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\IndicacaoJulgamentoFinalRepository")
 * @ORM\Table(schema="eleitoral", name="TB_INDICACAO_JULGAMENTO_FINAL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class IndicacaoJulgamentoFinal extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_INDICACAO_JULGAMENTO_FINAL_ID_SEQ", initialValue=1, allocationSize=1)
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
     * @ORM\ManyToOne(targetEntity="App\Entities\JulgamentoFinal")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_FINAL", referencedColumnName="ID")
     *
     * @var JulgamentoFinal
     */
    private $julgamentoFinal;

    /**
     * Fábrica de instância de 'IndicacaoJulgamentoFinal'.
     *
     * @param array $data
     *
     * @return IndicacaoJulgamentoFinal
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

            $julgamentoFinal = Utils::getValue('julgamentoFinal', $data);
            if(!empty($julgamentoFinal) and is_array($julgamentoFinal)){
                $instance->setJulgamentoFinal(JulgamentoFinal::newInstance($julgamentoFinal));
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
     * @return TipoParticipacaoChapa
     */
    public function getTipoParticipacaoChapa()
    {
        return $this->tipoParticipacaoChapa;
    }

    /**
     * @param TipoParticipacaoChapa $tipoParticipacaoChapa
     */
    public function setTipoParticipacaoChapa(TipoParticipacaoChapa $tipoParticipacaoChapa): void
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
    public function setNumeroOrdem($numeroOrdem): void
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
    public function setMembroChapa($membroChapa): void
    {
        $this->membroChapa = $membroChapa;
    }

    /**
     * @return JulgamentoFinal
     */
    public function getJulgamentoFinal()
    {
        return $this->julgamentoFinal;
    }

    /**
     * @param JulgamentoFinal $julgamentoFinal
     */
    public function setJulgamentoFinal($julgamentoFinal): void
    {
        $this->julgamentoFinal = $julgamentoFinal;
    }
}
