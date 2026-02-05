<?php

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada aos e-mails das subatividades pendentes de cadastro das informações iniciais
 * da definição da commisão de membros.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class EmailAtividadeSemParametrizacaoTO
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $assunto;

    /**
     * @var string
     */
    private $descricaoCorpoEmail;

    /**
     * @var string
     */
    private $titulo;

    /**
     * @var string
     */
    private $nomeImagemCabecalho;

    /**
     * @var string
     */
    private $textoCabecalho;

    /**
     * @var string
     */
    private $textoRodape;

    /**
     * @var string
     */
    private $nomeImagemRodape;

    /**
     * @var integer
     */
    private $idResponsavel;

    /**
     * @var integer
     */
    private $idCabecalhoEmail;
    
    /**
     * @var string
     */
    private $ativo;
    
    /**
     * @var string
     */
    private $isCabecalhoAtivo;
    
    /**
     * @var string
     */
    private $isRodapeAtivo;


    /**
     * Retorna uma nova instância de 'EmailAusenciaParametrizacaoComissaoTO'.
     *
     * @param null $data
     * @return EmailAtividadeSemParametrizacaoTO
     */
    public static function newInstance($data = null)
    {
        $emailAusenciaParametrizacaoComissaoTO = new EmailAtividadeSemParametrizacaoTO();

        if ($data != null) {
            $emailAusenciaParametrizacaoComissaoTO->setId(Utils::getValue('id', $data));
            $emailAusenciaParametrizacaoComissaoTO->setTitulo(Utils::getValue('titulo', $data));
            $emailAusenciaParametrizacaoComissaoTO->setAssunto(Utils::getValue('assunto', $data));
            $emailAusenciaParametrizacaoComissaoTO->setTextoRodape(Utils::getValue('textoRodape', $data));
            $emailAusenciaParametrizacaoComissaoTO->setIdResponsavel(Utils::getValue('idResponsavel', $data));
            $emailAusenciaParametrizacaoComissaoTO->setTextoCabecalho(Utils::getValue('textoCabecalho', $data));
            $emailAusenciaParametrizacaoComissaoTO->setIdCabecalhoEmail(Utils::getValue('idCabecalhoEmail', $data));
            $emailAusenciaParametrizacaoComissaoTO->setNomeImagemRodape(Utils::getValue('nomeImagemRodape', $data));
            $emailAusenciaParametrizacaoComissaoTO->setDescricaoCorpoEmail(Utils::getValue('descricaoCorpoEmail', $data));
            $emailAusenciaParametrizacaoComissaoTO->setNomeImagemCabecalho(Utils::getValue('nomeImagemCabecalho', $data));
            $emailAusenciaParametrizacaoComissaoTO->setAtivo(Utils::getValue('ativo', $data));
            $emailAusenciaParametrizacaoComissaoTO->setIsCabecalhoAtivo(Utils::getValue('isCabecalhoAtivo', $data));
            $emailAusenciaParametrizacaoComissaoTO->setIsRodapeAtivo(Utils::getValue('isRodapeAtivo', $data));
        }

        return $emailAusenciaParametrizacaoComissaoTO;
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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAssunto()
    {
        return $this->assunto;
    }

    /**
     * @param string $assunto
     */
    public function setAssunto($assunto)
    {
        $this->assunto = $assunto;
    }

    /**
     * @return string
     */
    public function getDescricaoCorpoEmail()
    {
        return $this->descricaoCorpoEmail;
    }

    /**
     * @param string $descricaoCorpoEmail
     */
    public function setDescricaoCorpoEmail($descricaoCorpoEmail)
    {
        $this->descricaoCorpoEmail = $descricaoCorpoEmail;
    }

    /**
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * @param string $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    /**
     * @return string
     */
    public function getNomeImagemCabecalho()
    {
        return $this->nomeImagemCabecalho;
    }

    /**
     * @param string $nomeImagemCabecalho
     */
    public function setNomeImagemCabecalho($nomeImagemCabecalho)
    {
        $this->nomeImagemCabecalho = $nomeImagemCabecalho;
    }

    /**
     * @return string
     */
    public function getTextoCabecalho()
    {
        return $this->textoCabecalho;
    }

    /**
     * @param string $textoCabecalho
     */
    public function setTextoCabecalho($textoCabecalho)
    {
        $this->textoCabecalho = $textoCabecalho;
    }

    /**
     * @return string
     */
    public function getTextoRodape()
    {
        return $this->textoRodape;
    }

    /**
     * @param string $textoRodape
     */
    public function setTextoRodape($textoRodape)
    {
        $this->textoRodape = $textoRodape;
    }

    /**
     * @return string
     */
    public function getNomeImagemRodape()
    {
        return $this->nomeImagemRodape;
    }

    /**
     * @param string $nomeImagemRodape
     */
    public function setNomeImagemRodape($nomeImagemRodape)
    {
        $this->nomeImagemRodape = $nomeImagemRodape;
    }

    /**
     * @return int
     */
    public function getIdResponsavel()
    {
        return $this->idResponsavel;
    }

    /**
     * @param int $idResponsavel
     */
    public function setIdResponsavel($idResponsavel)
    {
        $this->idResponsavel = $idResponsavel;
    }

    /**
     * @return int
     */
    public function getIdCabecalhoEmail()
    {
        return $this->idCabecalhoEmail;
    }

    /**
     * @param int $idCabecalhoEmail
     */
    public function setIdCabecalhoEmail($idCabecalhoEmail)
    {
        $this->idCabecalhoEmail = $idCabecalhoEmail;
    }
    /**
     * @return string
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param string $ativo
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    /**
     * @return string
     */
    public function getIsCabecalhoAtivo()
    {
        return $this->isCabecalhoAtivo;
    }

    /**
     * @param string $isCabecalhoAtivo
     */
    public function setIsCabecalhoAtivo($isCabecalhoAtivo)
    {
        $this->isCabecalhoAtivo = $isCabecalhoAtivo;
    }

    /**
     * @return string
     */
    public function getIsRodapeAtivo()
    {
        return $this->isRodapeAtivo;
    }

    /**
     * @param string $isRodapeAtivo
     */
    public function setIsRodapeAtivo($isRodapeAtivo)
    {
        $this->isRodapeAtivo = $isRodapeAtivo;
    }

}
