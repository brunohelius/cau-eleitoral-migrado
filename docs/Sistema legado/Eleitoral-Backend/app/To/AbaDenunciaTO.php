<?php
/*
 * AbaDenunciaTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada as abas da denúncia.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class AbaDenunciaTO
{
    /** @var int|null */
    private $idDenuncia;

    /** @var bool|null */
    private $hasAcompanharDenuncia;

    /** @var bool|null */
    private $hasAnaliseAdmissibilidade;

    /** @var bool|null */
    private $hasJulgamentoAdmissibilidade;

    /** @var bool|null */
    private $hasRecursoAdmissibilidade;

    /** @var bool|null */
    private $hasJulgamentoRecursoAdmissibilidade;

    /** @var bool|null */
    private $hasDefesa;

    /** @var bool|null */
    private $hasParecer;

    /** @var bool|null */
    private $hasJulgamentoPrimeiraInstancia;

    /** @var bool|null */
    private $hasRecursoDenunciante;

    /** @var bool|null */
    private $hasRecursoDenunciado;

    /** @var bool|null */
    private $hasJulgamentoSegundaInstancia;

    /**
     * Retorna uma nova instância de 'AbaDenunciaTO'.
     *
     * @param null $data
     *
     * @return \App\To\AbaDenunciaTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data !== null) {
            $instance->setIdDenuncia(Utils::getValue('idDenuncia', $data));
            $instance->setHasAcompanharDenuncia(Utils::getBooleanValue('hasAcompanharDenuncia', $data));
            $instance->setHasAnaliseAdmissibilidade(Utils::getBooleanValue('hasAnaliseAdmissibilidade', $data));
            $instance->setHasJulgamentoAdmissibilidade(Utils::getBooleanValue('hasJulgamentoAdmissibilidade', $data));
            $instance->setHasRecursoAdmissibilidade(Utils::getBooleanValue('hasRecursoAdmissibilidade', $data));
            $instance->setHasJulgamentoRecursoAdmissibilidade(Utils::getBooleanValue('hasJulgamentoRecursoAdmissibilidade', $data));
            $instance->setHasDefesa(Utils::getBooleanValue('hasDefesa', $data));
            $instance->setHasParecer(Utils::getBooleanValue('hasParecer', $data));
            $instance->setHasJulgamentoPrimeiraInstancia(Utils::getBooleanValue('hasJulgamentoPrimeiraInstancia', $data));
            $instance->setHasRecursoDenunciante(Utils::getBooleanValue('hasRecursoDenunciante', $data));
            $instance->setHasRecursoDenunciado(Utils::getBooleanValue('hasRecursoDenunciado', $data));
            $instance->setHasJulgamentoSegundaInstancia(Utils::getBooleanValue('hasJulgamentoSegundaInstancia', $data));
        }

        return $instance;
    }

    /**
     * @return int|null
     */
    public function getIdDenuncia(): ?int
    {
        return $this->idDenuncia;
    }

    /**
     * @param int|null $idDenuncia
     */
    public function setIdDenuncia(?int $idDenuncia): void
    {
        $this->idDenuncia = $idDenuncia;
    }

    /**
     * @return bool|null
     */
    public function hasAcompanharDenuncia(): ?bool
    {
        return $this->hasAcompanharDenuncia;
    }

    /**
     * @param bool|null $hasAcompanharDenuncia
     */
    public function setHasAcompanharDenuncia(?bool $hasAcompanharDenuncia): void
    {
        $this->hasAcompanharDenuncia = $hasAcompanharDenuncia;
    }

    /**
     * @return bool|null
     */
    public function hasAnaliseAdmissibilidade(): ?bool
    {
        return $this->hasAnaliseAdmissibilidade;
    }

    /**
     * @param bool|null $hasAnaliseAdmissibilidade
     */
    public function setHasAnaliseAdmissibilidade(?bool $hasAnaliseAdmissibilidade): void
    {
        $this->hasAnaliseAdmissibilidade = $hasAnaliseAdmissibilidade;
    }

    /**
     * @return bool|null
     */
    public function hasJulgamentoAdmissibilidade(): ?bool
    {
        return $this->hasJulgamentoAdmissibilidade;
    }

    /**
     * @param bool|null $hasJulgamentoAdmissibilidade
     */
    public function setHasJulgamentoAdmissibilidade(?bool $hasJulgamentoAdmissibilidade): void
    {
        $this->hasJulgamentoAdmissibilidade = $hasJulgamentoAdmissibilidade;
    }

    /**
     * @return bool|null
     */
    public function hasRecursoAdmissibilidade(): ?bool
    {
        return $this->hasRecursoAdmissibilidade;
    }

    /**
     * @param bool|null $hasRecursoAdmissibilidade
     */
    public function setHasRecursoAdmissibilidade(?bool $hasRecursoAdmissibilidade): void
    {
        $this->hasRecursoAdmissibilidade = $hasRecursoAdmissibilidade;
    }

    /**
     * @return bool|null
     */
    public function hasJulgamentoRecursoAdmissibilidade(): ?bool
    {
        return $this->hasJulgamentoRecursoAdmissibilidade;
    }

    /**
     * @param bool|null $hasJulgamentoRecursoAdmissibilidade
     */
    public function setHasJulgamentoRecursoAdmissibilidade(?bool $hasJulgamentoRecursoAdmissibilidade): void
    {
        $this->hasJulgamentoRecursoAdmissibilidade = $hasJulgamentoRecursoAdmissibilidade;
    }

    /**
     * @return bool|null
     */
    public function hasDefesa(): ?bool
    {
        return $this->hasDefesa;
    }

    /**
     * @param bool|null $hasDefesa
     */
    public function setHasDefesa(?bool $hasDefesa): void
    {
        $this->hasDefesa = $hasDefesa;
    }

    /**
     * @return bool|null
     */
    public function hasParecer(): ?bool
    {
        return $this->hasParecer;
    }

    /**
     * @param bool|null $hasParecer
     */
    public function setHasParecer(?bool $hasParecer): void
    {
        $this->hasParecer = $hasParecer;
    }

    /**
     * @return bool|null
     */
    public function hasJulgamentoPrimeiraInstancia(): ?bool
    {
        return $this->hasJulgamentoPrimeiraInstancia;
    }

    /**
     * @param bool|null $hasJulgamentoPrimeiraInstancia
     */
    public function setHasJulgamentoPrimeiraInstancia(?bool $hasJulgamentoPrimeiraInstancia): void
    {
        $this->hasJulgamentoPrimeiraInstancia = $hasJulgamentoPrimeiraInstancia;
    }

    /**
     * @return bool|null
     */
    public function hasRecursoDenunciante(): ?bool
    {
        return $this->hasRecursoDenunciante;
    }

    /**
     * @param bool|null $hasRecursoDenunciante
     */
    public function setHasRecursoDenunciante(?bool $hasRecursoDenunciante): void
    {
        $this->hasRecursoDenunciante = $hasRecursoDenunciante;
    }

    /**
     * @return bool|null
     */
    public function hasRecursoDenunciado(): ?bool
    {
        return $this->hasRecursoDenunciado;
    }

    /**
     * @param bool|null $hasRecursoDenunciado
     */
    public function setHasRecursoDenunciado(?bool $hasRecursoDenunciado): void
    {
        $this->hasRecursoDenunciado = $hasRecursoDenunciado;
    }

    /**
     * @return bool|null
     */
    public function hasJulgamentoSegundaInstancia(): ?bool
    {
        return $this->hasJulgamentoSegundaInstancia;
    }

    /**
     * @param bool|null $hasJulgamentoSegundaInstancia
     */
    public function setHasJulgamentoSegundaInstancia(?bool $hasJulgamentoSegundaInstancia): void
    {
        $this->hasJulgamentoSegundaInstancia = $hasJulgamentoSegundaInstancia;
    }

}
