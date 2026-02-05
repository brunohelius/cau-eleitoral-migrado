<?php

namespace App\To;

use App\Entities\MembroSubstituicaoJulgamentoFinal;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Membro Substituição no Julgamento Final
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class MembroSubstituicaoJulgamentoFinalTO
{

    /**
     * @var integer | null $id
     */
    private $id;

    /**
     * @var IndicacaoJulgamentoFinalTO | null
     */
    private $indicacaoJulgamentoFinal;

    /**
     * @var MembroChapaTO | null
     */
    private $membroChapa;

    /**
     * @var integer | null $idIndicacaoJulgamento
     */
    private $idIndicacaoJulgamento;

    /**
     * @var integer | null $idProfissional
     */
    private $idProfissional;

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
     * @return MembroChapaTO|null
     */
    public function getMembroChapa(): ?MembroChapaTO
    {
        return $this->membroChapa;
    }

    /**
     * @param MembroChapaTO|null $membroChapa
     */
    public function setMembroChapa(?MembroChapaTO $membroChapa): void
    {
        $this->membroChapa = $membroChapa;
    }

    /**
     * @return int|null
     */
    public function getIdIndicacaoJulgamento(): ?int
    {
        return $this->idIndicacaoJulgamento;
    }

    /**
     * @param int|null $idIndicacaoJulgamento
     */
    public function setIdIndicacaoJulgamento(?int $idIndicacaoJulgamento): void
    {
        $this->idIndicacaoJulgamento = $idIndicacaoJulgamento;
    }

    /**
     * @return int|null
     */
    public function getIdProfissional(): ?int
    {
        return $this->idProfissional;
    }

    /**
     * @param int|null $idProfissional
     */
    public function setIdProfissional(?int $idProfissional): void
    {
        $this->idProfissional = $idProfissional;
    }

    /**
     * Retorna uma nova instância de 'MembroSubstituicaoJulgamentoFinalTO'.
     *
     * @param null $data
     * @return MembroSubstituicaoJulgamentoFinalTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $membroSubstituicaoJulgamentoFinalTO = new MembroSubstituicaoJulgamentoFinalTO();

        if ($data != null) {
            $membroSubstituicaoJulgamentoFinalTO->setId(Arr::get($data, 'id'));

            $indicacao = Arr::get($data, 'indicacaoJulgamentoFinal');
            if (!empty($indicacao)) {
                $membroSubstituicaoJulgamentoFinalTO->setIndicacaoJulgamentoFinal(IndicacaoJulgamentoFinalTO::newInstance($indicacao));
            }

            $membroSubstituicaoJulgamentoFinalTO->setIdIndicacaoJulgamento(Arr::get($data, 'idIndicacaoJulgamento'));
            $membroSubstituicaoJulgamentoFinalTO->setIdProfissional(Arr::get($data, 'idProfissional'));
        }

        return $membroSubstituicaoJulgamentoFinalTO;
    }

    /**
     * Retorna uma nova instância de 'MembroSubstituicaoJulgamentoFinalTO'.
     *
     * @param MembroSubstituicaoJulgamentoFinal $membroSubstituicaoJulgamentoFinal
     * @return MembroSubstituicaoJulgamentoFinalTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($membroSubstituicaoJulgamentoFinal)
    {
        $membroSubstituicaoJulgamentoFinalTO = new MembroSubstituicaoJulgamentoFinalTO();

        if (!empty($membroSubstituicaoJulgamentoFinal)) {
            $membroSubstituicaoJulgamentoFinalTO->setId($membroSubstituicaoJulgamentoFinal->getId());

            $indicacao = $membroSubstituicaoJulgamentoFinal->getIndicacaoJulgamentoFinal();
            if (!empty($indicacao)) {
                $membroSubstituicaoJulgamentoFinalTO->setIndicacaoJulgamentoFinal(IndicacaoJulgamentoFinalTO::newInstanceFromEntity(
                    $indicacao, false
                ));
            }

            $indicacaoJulgamentoRecursoSubst = $membroSubstituicaoJulgamentoFinal->getIndicacaoJulgamentoRecursoPedidoSubstituicao();
            if (!empty($indicacaoJulgamentoRecursoSubst)) {
                $membroSubstituicaoJulgamentoFinalTO->setIndicacaoJulgamentoFinal(IndicacaoJulgamentoFinalTO::newInstanceFromEntity(
                    $indicacaoJulgamentoRecursoSubst, false
                ));
            }

            $indicacaoJulgamentoRecurso = $membroSubstituicaoJulgamentoFinal->getIndicacaoJulgamentoSegundaInstanciaRecurso();
            if (!empty($indicacaoJulgamentoRecurso)) {
                $membroSubstituicaoJulgamentoFinalTO->setIndicacaoJulgamentoFinal(IndicacaoJulgamentoFinalTO::newInstanceFromEntity(
                    $indicacaoJulgamentoRecurso, false
                ));
            }

            $indicacaoJulgamentoSubst = $membroSubstituicaoJulgamentoFinal->getIndicacaoJulgamentoSegundaInstanciaSubstituicao();
            if (!empty($indicacaoJulgamentoSubst)) {
                $membroSubstituicaoJulgamentoFinalTO->setIndicacaoJulgamentoFinal(IndicacaoJulgamentoFinalTO::newInstanceFromEntity(
                    $indicacaoJulgamentoSubst, false
                ));
            }

            $membroChapa = $membroSubstituicaoJulgamentoFinal->getMembroChapa();
            if (!empty($membroChapa)) {
                $membroSubstituicaoJulgamentoFinalTO->setMembroChapa(MembroChapaTO::newInstanceFromEntity(
                    $membroChapa, false, true
                ));
            }
        }

        return $membroSubstituicaoJulgamentoFinalTO;
    }

}
