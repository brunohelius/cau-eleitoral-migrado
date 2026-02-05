<?php
/*
 * ExportarImpedimentoSuspeicaoTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Entities\EncaminhamentoDenuncia;
use App\Entities\ImpedimentoSuspeicao;
use App\Util\Utils;

/**
 * Classe de transferência associada a exportar encaminhamento impedimento suspeição.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ExportarImpedimentoSuspeicaoTO
{

    /** @var string|null */
    private $usuario;

    /** @var string|null */
    private $relator;

    /** @var \DateTime|null */
    private $dataHora;

    /** @var string|null */
    private $despacho;

    /** @var ArquivoDescricaoTO[]|null */
    private $documentos;

    /**
     * Retorna uma nova instância de 'ExportarImpedimentoSuspeicaoTO'.
     *
     * @param ImpedimentoSuspeicao $impedimentoSuspeicao
     *
     * @return ExportarImpedimentoSuspeicaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity(ImpedimentoSuspeicao $impedimentoSuspeicao)
    {
        $instance = new self();

        if ($impedimentoSuspeicao !== null) {
            $instance->setDataHora($impedimentoSuspeicao->getDenunciaAdmitida()->getDataAdmissao());
            $instance->setUsuario($impedimentoSuspeicao->getDenunciaAdmitida()->getCoordenador()->getProfissionalEntity()->getNome());
            $instance->setRelator($impedimentoSuspeicao->getDenunciaAdmitida()->getMembroComissao()->getProfissionalEntity()->getNome());
            $instance->setDespacho($impedimentoSuspeicao->getDenunciaAdmitida()->getDescricaoDespacho());
        }

        return $instance;
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
     * @return string|null
     */
    public function getRelator(): ?string
    {
        return $this->relator;
    }

    /**
     * @param string|null $relator
     */
    public function setRelator(?string $relator): void
    {
        $this->relator = $relator;
    }

    /**
     * @return \DateTime|null
     */
    public function getDataHora(): ?\DateTime
    {
        return $this->dataHora;
    }

    /**
     * @param \DateTime|null $dataHora
     */
    public function setDataHora(?\DateTime $dataHora): void
    {
        $this->dataHora = $dataHora;
    }

    /**
     * @return string|null
     */
    public function getDespacho(): ?string
    {
        return $this->despacho;
    }

    /**
     * @param string|null $despacho
     */
    public function setDespacho(?string $despacho): void
    {
        $this->despacho = $despacho;
    }

    /**
     * @return ArquivoDescricaoTO[]|null
     */
    public function getDocumentos(): ?array
    {
        return $this->documentos;
    }

    /**
     * @param ArquivoDescricaoTO[]|null $documentos
     */
    public function setDocumentos(?array $documentos): void
    {
        $this->documentos = $documentos;
    }

}
