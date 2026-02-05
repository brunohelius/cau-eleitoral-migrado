<?php

namespace App\To;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use OpenApi\Annotations as OA;
use PharIo\Manifest\Email;

/**
 * Classe de transferência associada ao envio de Emails.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 */
class EmailTO
{
    /**
     * @var bool
     */
    private $isCabecalhoAtivo;

    /**
     * @var bool
     */
    private $isRodapeAtivo;

    /**
     * @var bool
     */
    private $isCorpoAtivo;

    /**
     * @var String
     */
    private $caminhoImagemCabecalho;

    /**
     * @var String
     */
    private $caminhoImagemRodape;

    /**
     * @var String
     */
    private $textoCorpo;

    /**
     * @var String
     */
    private $textoCabecalho;

    /**
     * @var String
     */
    private $textoRodape;

    /**
     * @var String[]
     */
    private $destinatarios;

    /**
     *
     * @var string
     */
    private $assunto;

    /**
     * Retorna uma nova instância de 'EmailTO'.
     *
     * @param null $data
     * @return EmailTO
     */
    public static function newInstance($data = null)
    {
        $emailTO = new EmailTO();
        $emailTO->setIsRodapeAtivo(false);
        $emailTO->setIsCabecalhoAtivo(false);

        if ($data != null) {

        }

        return $emailTO;
    }

    /**
     * @return bool
     */
    public function isCabecalhoAtivo(): ?bool
    {
        return $this->isCabecalhoAtivo;
    }

    /**
     * @param bool $isCabecalhoAtivo
     */
    public function setIsCabecalhoAtivo(?bool $isCabecalhoAtivo): void
    {
        $this->isCabecalhoAtivo = $isCabecalhoAtivo;
    }

    /**
     * @return bool
     */
    public function isRodapeAtivo(): ?bool
    {
        return $this->isRodapeAtivo;
    }

    /**
     * @param bool $isRodapeAtivo
     */
    public function setIsRodapeAtivo(?bool $isRodapeAtivo): void
    {
        $this->isRodapeAtivo = $isRodapeAtivo;
    }

    /**
     * @return String
     */
    public function getCaminhoImagemCabecalho(): ?string
    {
        return $this->caminhoImagemCabecalho;
    }

    /**
     * @param String $caminhoImagemCabecalho
     */
    public function setCaminhoImagemCabecalho(?string $caminhoImagemCabecalho): void
    {
        $this->caminhoImagemCabecalho = $caminhoImagemCabecalho;
    }

    /**
     * @return String
     */
    public function getCaminhoImagemRodape(): ?string
    {
        return $this->caminhoImagemRodape;
    }

    /**
     * @param String $caminhoImagemRodape
     */
    public function setCaminhoImagemRodape(?string $caminhoImagemRodape): void
    {
        $this->caminhoImagemRodape = $caminhoImagemRodape;
    }

    /**
     * @return String
     */
    public function getTextoCorpo(): ?string
    {
        return $this->textoCorpo;
    }

    /**
     * @param String $textoCorpo
     */
    public function setTextoCorpo(?string $textoCorpo): void
    {
        $this->textoCorpo = $textoCorpo;
    }

    /**
     * @return String
     */
    public function getTextoCabecalho(): ?string
    {
        return $this->textoCabecalho;
    }

    /**
     * @param String $textoCabecalho
     */
    public function setTextoCabecalho(?string $textoCabecalho): void
    {
        $this->textoCabecalho = $textoCabecalho;
    }

    /**
     * @return String
     */
    public function getTextoRodape(): ?string
    {
        return $this->textoRodape;
    }

    /**
     * @param String $textoRodape
     */
    public function setTextoRodape(?string $textoRodape): void
    {
        $this->textoRodape = $textoRodape;
    }

    /**
     * @return String[]
     */
    public function getDestinatarios(): ?array
    {
        return $this->destinatarios;
    }

    /**
     * @param String[] $destinatarios
     */
    public function setDestinatarios(?array $destinatarios): void
    {
        $this->destinatarios = $destinatarios;
    }

    /**
     * @return string
     */
    public function getAssunto(): ?string
    {
        return $this->assunto;
    }

    /**
     * @param string $assunto
     */
    public function setAssunto(?string $assunto): void
    {
        $this->assunto = $assunto;
    }

    /**
     * @return bool
     */
    public function isCorpoAtivo(): ?bool
    {
        return $this->isCorpoAtivo;
    }

    /**
     * @param bool $isCorpoAtivo
     */
    public function setIsCorpoAtivo(?bool $isCorpoAtivo): void
    {
        $this->isCorpoAtivo = $isCorpoAtivo;
    }


}
