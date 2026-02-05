<?php


namespace App\To;


use App\Util\Utils;

class DefesaImpugnacaoValidacaoAcessoProfissionalTO
{
    /**
     * @var boolean
     */
    private $isProfissional;

    /**
     * @var boolean
     */
    private $isResponsavel;

    /**
     * @var boolean
     */
    private $hasPedidoImpugnacao;

    /**
     * @var boolean
     */
    private $hasPedidoImpugnacaoEmAnalise;

    /**
     * @var boolean
     */
    private $isAtividadeSecundariaVigente;

    /**
     * @var boolean
     */
    private $hasDefesaImpugnacao;

    /**
     * Fabricação estática de 'DefesaImpugnacaoValidacaoAcessoProfissionalTO'.
     *
     * @param array|null $data
     *
     * @return DefesaImpugnacaoValidacaoAcessoProfissionalTO
     */
    public static function newInstance($data = null)
    {
        $defesaImpugnacaoValidacao = new DefesaImpugnacaoValidacaoAcessoProfissionalTO();

        if($data !== null) {
            $defesaImpugnacaoValidacao->setIsProfissional(
                Utils::getValue('isProfissional', $data, false)
            );

            $defesaImpugnacaoValidacao->setIsResponsavel(
                Utils::getValue('isResponsavel', $data, false)
            );

            $defesaImpugnacaoValidacao->setHasPedidoImpugnacao(
                Utils::getValue('hasPedidoIMpugnacao', $data, false)
            );

            $defesaImpugnacaoValidacao->setHasPedidoImpugnacaoEmAnalise(
                Utils::getValue('hasPedidoImpugnacaoEmAnalise', $data, false)
            );

            $defesaImpugnacaoValidacao->setIsAtividadeSecundariaVigente(
                Utils::getValue('isAtividadeSecundariaVigente', $data, false)
            );

            $defesaImpugnacaoValidacao->setHasDefesaImpugnacao(
                Utils::getValue('hasDefesaImpugnacao', $data, false)
            );
        }

        return $defesaImpugnacaoValidacao;

    }

    /**
     * @return bool
     */
    public function getIsProfissional(): bool
    {
        return $this->isProfissional;
    }

    /**
     * @param bool $isProfissional
     */
    public function setIsProfissional(bool $isProfissional): void
    {
        $this->isProfissional = $isProfissional;
    }

    /**
     * @return bool
     */
    public function getIsResponsavel(): bool
    {
        return $this->isResponsavel;
    }

    /**
     * @param bool $isResponsavel
     */
    public function setIsResponsavel(bool $isResponsavel): void
    {
        $this->isResponsavel = $isResponsavel;
    }

    /**
     * @return bool
     */
    public function getHasPedidoImpugnacao(): bool
    {
        return $this->hasPedidoImpugnacao;
    }

    /**
     * @param bool $hasPedidoImpugnacao
     */
    public function setHasPedidoImpugnacao(bool $hasPedidoImpugnacao): void
    {
        $this->hasPedidoImpugnacao = $hasPedidoImpugnacao;
    }

    /**
     * @return bool
     */
    public function getHasPedidoImpugnacaoEmAnalise(): bool
    {
        return $this->hasPedidoImpugnacaoEmAnalise;
    }

    /**
     * @param bool $hasPedidoImpugnacaoEmAnalise
     */
    public function setHasPedidoImpugnacaoEmAnalise(bool $hasPedidoImpugnacaoEmAnalise): void
    {
        $this->hasPedidoImpugnacaoEmAnalise = $hasPedidoImpugnacaoEmAnalise;
    }

    /**
     * @return bool
     */
    public function getIsAtividadeSecundariaVigente(): bool
    {
        return $this->isAtividadeSecundariaVigente;
    }

    /**
     * @param bool $isAtividadeSecundariaVigente
     */
    public function setIsAtividadeSecundariaVigente(bool $isAtividadeSecundariaVigente): void
    {
        $this->isAtividadeSecundariaVigente = $isAtividadeSecundariaVigente;
    }

    /**
     * @return bool
     */
    public function getHasDefesaImpugnacao(): bool
    {
        return $this->hasDefesaImpugnacao;
    }

    /**
     * @param bool $hasDefesaImpugnacao
     */
    public function setHasDefesaImpugnacao(bool $hasDefesaImpugnacao): void
    {
        $this->hasDefesaImpugnacao = $hasDefesaImpugnacao;
    }
}