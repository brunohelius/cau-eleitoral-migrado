<?php


namespace App\To;

use Illuminate\Support\Arr;
use App\Entities\RecursoIndicacao;

/**
 * Classe de transferência para a Julgamento Final
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class RecursoIndicacaoTO
{

    /**
     * @var integer|null $id
     */
    private $id;

    /**
     * @var IndicacaoJulgamentoFinalTO|null $indicacaoJulgamentoFinal
     */
    private $indicacaoJulgamentoFinal;

    /**
     * @var RecursoJulgamentoFinalTO|null $recursoJulgamentoFinal
     */
    private $recursoJulgamentoFinal;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return IndicacaoJulgamentoFinalTO|null
     */
    public function getIndicacaoJulgamentoFinal(): ?IndicacaoJulgamentoFinalTO
    {
        return $this->indicacaoJulgamentoFinal;
    }

    /**
     * @param IndicacaoJulgamentoFinalTO|null $indicacaoJulgamentoFinal
     */
    public function setIndicacaoJulgamentoFinal(?IndicacaoJulgamentoFinalTO $indicacaoJulgamentoFinal): void
    {
        $this->indicacaoJulgamentoFinal = $indicacaoJulgamentoFinal;
    }

    /**
     * @return RecursoJulgamentoFinalTO|null
     */
    public function getRecursoJulgamentoFinal(): ?RecursoJulgamentoFinalTO
    {
        return $this->recursoJulgamentoFinal;
    }

    /**
     * @param RecursoJulgamentoFinalTO|null $recursoJulgamentoFinal
     */
    public function setRecursoJulgamentoFinal(?RecursoJulgamentoFinalTO $recursoJulgamentoFinal): void
    {
        $this->recursoJulgamentoFinal = $recursoJulgamentoFinal;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalTO'.
     *
     * @param null $data
     * @return RecursoIndicacaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $recursoIndicacaoTO = new RecursoIndicacaoTO();

        if ($data != null) {
            $recursoIndicacaoTO->setId(Arr::get($data, 'id'));

            $indicacaoJulgamentoFinal = Arr::get($data, 'indicacaoJulgamentoFinal');
            if (!empty($indicacaoJulgamentoFinal)) {
                $recursoIndicacaoTO
                    ->setIndicacaoJulgamentoFinal(IndicacaoJulgamentoFinalTO::newInstance($indicacaoJulgamentoFinal));
            }

            $recursoJulgamentoFinal = Arr::get($data, 'recursoJulgamentoFinal');
            if (!empty($recursoJulgamentoFinal)) {
                $recursoIndicacaoTO
                    ->setRecursoJulgamentoFinal(RecursoJulgamentoFinalTO::newInstance($recursoJulgamentoFinal));
            }
        }

        return $recursoIndicacaoTO;
    }

    /**
     * Retorna uma nova instância de 'RecursoJulgamentoFinalTO'.
     *
     * @param RecursoIndicacao $recursoIndicacao
     * @param bool $isResumo
     * @return RecursoIndicacaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($recursoIndicacao)
    {
        $recursoIndicacaoTO = new RecursoIndicacaoTO();

        if (!empty($recursoIndicacaoTO)) {
            $recursoIndicacaoTO->setId($recursoIndicacao->getId());

            $profissional = $recursoIndicacao->getIndicacaoJulgamentoFinal();
            if (!empty($profissional)) {
                $recursoIndicacaoTO->setIndicacaoJulgamentoFinal(IndicacaoJulgamentoFinalTO::newInstanceFromEntity(
                    $profissional
                ));
            }
        }

        return $recursoIndicacaoTO;
    }

}
