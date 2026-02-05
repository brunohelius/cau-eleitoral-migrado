<?php

namespace App\Entities;

use App\To\ProfissionalTO;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'RecursoIndicacao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\RecursoIndicacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_RECURSO_INDICACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class RecursoIndicacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\Column(name="ID", type="integer")
* @ORM\SequenceGenerator(sequenceName="eleitoral.TB_RECURSO_INDICACAO_ID_SEQ", initialValue=1, allocationSize=1)
* @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\IndicacaoJulgamentoFinal")
     * @ORM\JoinColumn(name="ID_INDICACAO_JULGAMENTO_FINAL", referencedColumnName="ID", nullable=false)
     * @var IndicacaoJulgamentoFinal
     */
    private $indicacaoJulgamentoFinal;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\RecursoJulgamentoFinal")
     * @ORM\JoinColumn(name="ID_RECURSO_JULGAMENTO_FINAL", referencedColumnName="ID", nullable=false)
     *
     * @var RecursoJulgamentoFinal
     */
    private $recursoJulgamentoFinal;

    /**
     * Fábrica de instância de 'RecursoIndicacao'.
     *
     * @param array $data
     * @return RecursoIndicacao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $recursoIndicacao = new RecursoIndicacao();

        if ($data != null) {
            $recursoIndicacao->setId(Utils::getValue('id', $data));

            $recursoJulgamentoFinal = Utils::getValue('recursoJulgamentoFinal', $data);
            if (!empty($recursoJulgamentoFinal)) {
                $recursoIndicacao->setRecursoJulgamentoFinal(RecursoJulgamentoFinal::newInstance($recursoJulgamentoFinal));
            }

            $indicacaoJulgamentoFinal = Utils::getValue('indicacaoJulgamentoFinal', $data);
            if (!empty($indicacaoJulgamentoFinal)) {
                $recursoIndicacao->setIndicacaoJulgamentoFinal(IndicacaoJulgamentoFinal::newInstance($indicacaoJulgamentoFinal));
            }
        }
        return $recursoIndicacao;
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
    public function getIndicacaoJulgamentoFinal(): IndicacaoJulgamentoFinal
    {
        return $this->indicacaoJulgamentoFinal;
    }

    /**
     * @param IndicacaoJulgamentoFinal $indicacaoJulgamentoFinal
     */
    public function setIndicacaoJulgamentoFinal(IndicacaoJulgamentoFinal $indicacaoJulgamentoFinal): void
    {
        $this->indicacaoJulgamentoFinal = $indicacaoJulgamentoFinal;
    }

    /**
     * @return RecursoJulgamentoFinal
     */
    public function getRecursoJulgamentoFinal(): RecursoJulgamentoFinal
    {
        return $this->recursoJulgamentoFinal;
    }

    /**
     * @param RecursoJulgamentoFinal $recursoJulgamentoFinal
     */
    public function setRecursoJulgamentoFinal(RecursoJulgamentoFinal $recursoJulgamentoFinal): void
    {
        $this->recursoJulgamentoFinal = $recursoJulgamentoFinal;
    }

}
