<?php
/*
 * DocumentoDenunciaTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada ao documento denúncia.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class DocumentoDenunciaTO
{
    /** @var string|null */
    private $ip;

    /** @var \DateTime|null */
    private $data;

    /** @var string|null */
    private $usuario;

    /** @var DenunciaTO|null */
    private $denuncia;

    /** @var JulgamentoRecursoAdmissibilidadeTO|null */
    private $julgamentoRecursoAdmissibilidade;

    /** @var \stdClass|null */
    private $defesa;

    /** @var \stdClass|null */
    private $encaminhamentos;

    /** @var \stdClass|null */
    private $julgamentoPrimeiraInstancia;

    /** @var RecursoDenunciaTO|null */
    private $recursoDenunciante;

    /** @var RecursoDenunciaTO|null */
    private $recursoDenunciado;

    /** @var \stdClass|null */
    private $julgamentoSegundaInstancia;

    /**
     * @var FilialTO|null
     */
    private $filialTO;

    /**
     * Retorna uma nova instância de 'DocumentoDenunciaTO'.
     *
     * @param null $data
     *
     * @return \App\To\DocumentoDenunciaTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data !== null) {
            $instance->setIp(Utils::getValue('ip', $data));
            $instance->setData(Utils::getValue('data', $data));
            $instance->setUsuario(Utils::getValue('usuario', $data));
            $filialTO = Utils::getValue('filial', $data);
            if (!empty($filialTO)) {
                $instance->setFilialTO(
                    FilialTO::newInstanceFromEntity($filialTO)
                );
            }
        }

        return $instance;
    }

    /**
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @param string|null $ip
     */
    public function setIp(?string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return \DateTime|null
     */
    public function getData(): ?\DateTime
    {
        return $this->data;
    }

    /**
     * @param \DateTime|null $data
     */
    public function setData(?\DateTime $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    /**
     * @param string|null $usuario
     */
    public function setUsuario(?string $usuario): void
    {
        $this->usuario = $usuario;
    }

    /**
     * @return DenunciaTO|null
     */
    public function getDenuncia(): ?DenunciaTO
    {
        return $this->denuncia;
    }

    /**
     * @param DenunciaTO|null $denuncia
     */
    public function setDenuncia(?DenunciaTO $denuncia): void
    {
        $this->denuncia = $denuncia;
    }

    /**
     * @return JulgamentoRecursoAdmissibilidadeTO|null
     */
    public function getJulgamentoRecursoAdmissibilidade(): ?JulgamentoRecursoAdmissibilidadeTO
    {
        return $this->julgamentoRecursoAdmissibilidade;
    }

    /**
     * @param JulgamentoRecursoAdmissibilidadeTO|null $julgamentoRecursoAdmissibilidade
     */
    public function setJulgamentoRecursoAdmissibilidade(
        ?JulgamentoRecursoAdmissibilidadeTO $julgamentoRecursoAdmissibilidade
    ): void {
        $this->julgamentoRecursoAdmissibilidade = $julgamentoRecursoAdmissibilidade;
    }

    /**
     * @return \stdClass|null
     */
    public function getDefesa(): ?\stdClass
    {
        return $this->defesa;
    }

    /**
     * @param \stdClass|null $defesa
     */
    public function setDefesa(?\stdClass $defesa): void
    {
        $this->defesa = $defesa;
    }

    /**
     * @return \stdClass|null
     */
    public function getEncaminhamentos(): ?\stdClass
    {
        return $this->encaminhamentos;
    }

    /**
     * @param \stdClass|null $encaminhamentos
     */
    public function setEncaminhamentos(?\stdClass $encaminhamentos): void
    {
        $this->encaminhamentos = $encaminhamentos;
    }

    /**
     * @return \stdClass|null
     */
    public function getJulgamentoPrimeiraInstancia(): ?\stdClass
    {
        return $this->julgamentoPrimeiraInstancia;
    }

    /**
     * @param \stdClass|null $julgamentoPrimeiraInstancia
     */
    public function setJulgamentoPrimeiraInstancia(?\stdClass $julgamentoPrimeiraInstancia): void
    {
        $this->julgamentoPrimeiraInstancia = $julgamentoPrimeiraInstancia;
    }

    /**
     * @return FilialTO|null
     */
    public function getFilialTO(): ?FilialTO
    {
        return $this->filialTO;
    }

    /**
     * @param FilialTO|null $filialTO
     */
    public function setFilialTO(?FilialTO $filialTO): void
    {
        $this->filialTO = $filialTO;
    }

    /**
     * @return \App\To\RecursoDenunciaTO|null
     */
    public function getRecursoDenunciante(): ?RecursoDenunciaTO
    {
        return $this->recursoDenunciante;
    }

    /**
     * @param \App\To\RecursoDenunciaTO|null $recursoDenunciante
     */
    public function setRecursoDenunciante(RecursoDenunciaTO $recursoDenunciante): void
    {
        $this->recursoDenunciante = $recursoDenunciante;
    }

    /**
     * @return \App\To\RecursoDenunciaTO|null
     */
    public function getRecursoDenunciado(): ?RecursoDenunciaTO
    {
        return $this->recursoDenunciado;
    }

    /**
     * @param \App\To\RecursoDenunciaTO|null $recursoDenunciado
     */
    public function setRecursoDenunciado(RecursoDenunciaTO $recursoDenunciado): void
    {
        $this->recursoDenunciado = $recursoDenunciado;
    }

    /**
     * @return \stdClass|null
     */
    public function getJulgamentoSegundaInstancia()
    {
        return $this->julgamentoSegundaInstancia;
    }

    /**
     * @param \stdClass|null $julgamentoSegundaInstancia
     */
    public function setJulgamentoSegundaInstancia($julgamentoSegundaInstancia): void
    {
        $this->julgamentoSegundaInstancia = $julgamentoSegundaInstancia;
    }
}
