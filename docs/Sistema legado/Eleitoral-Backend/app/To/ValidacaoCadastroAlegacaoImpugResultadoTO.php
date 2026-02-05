<?php


namespace App\To;


/**
 * Classe de transferência para validação de cadastro alegação de pedido de impugnação resultado
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ValidacaoCadastroAlegacaoImpugResultadoTO
{
    /**
     * @var boolean
     */
    private $isResponsavel;

    /**
     * @var boolean
     */
    private $isVigenteAtivCadastroAlegacaoImpugResultado;

    /**
     * @var boolean
     */
    private $hasAlegacao;

    /**
     * ValidacaoCadastroAlegacaoImpugResultadoTO constructor.
     * @param bool $isResponsavel
     */
    public function __construct()
    {
        $this->iniciarFlags();
    }

    /**
     * @return bool
     */
    public function isResponsavel(): bool
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
    public function isVigenteAtivCadastroAlegacaoImpugResultado(): bool
    {
        return $this->isVigenteAtivCadastroAlegacaoImpugResultado;
    }

    /**
     * @param bool $isVigenteAtivCadastroAlegacaoImpugResultado
     */
    public function setIsVigenteAtivCadastroAlegacaoImpugResultado(bool $isVigenteAtivCadastroAlegacaoImpugResultado): void
    {
        $this->isVigenteAtivCadastroAlegacaoImpugResultado = $isVigenteAtivCadastroAlegacaoImpugResultado;
    }

    /**
     * @return bool
     */
    public function isHasAlegacao(): bool
    {
        return $this->hasAlegacao;
    }

    /**
     * @param bool $hasAlegacao
     */
    public function setHasAlegacao(bool $hasAlegacao): void
    {
        $this->hasAlegacao = $hasAlegacao;
    }

    public function iniciarFlags(): void
    {
        $this->setHasAlegacao(false);
        $this->setIsVigenteAtivCadastroAlegacaoImpugResultado(false);
        $this->setIsResponsavel(false);
    }


}