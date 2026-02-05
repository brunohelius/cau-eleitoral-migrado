<?php

namespace App\To;

use App\Config\AppConfig;
use App\Config\Constants;
use App\Service\ArquivoService;
use App\Util\ImageUtils;
use App\Util\Utils;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'CabecalhoEmail'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="CabecalhoEmail")
 */
class CabecalhoEmailTO
{

    /**
     * @var integer
     */
    private $id;

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
    private $nomeImagemFisicaCabecalho;

    /**
     * @var string
     */
    private $imagemCabecalho;

    /**
     * @var string
     */
    private $textoCabecalho;

    /**
     * @var boolean
     */
    private $isCabecalhoAtivo;

    /**
     * @var string
     */
    private $nomeImagemRodape;

    /**
     * @var string
     */
    private $nomeImagemFisicaRodape;

    /**
     * @var string
     */
    private $imagemRodape;

    /**
     * @var string
     */
    private $textoRodape;

    /**
     * @var boolean
     */
    private $isRodapeAtivo;

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * Retorna uma nova instância de 'CabecalhoEmailTO'.
     *
     * @param null $data
     * @return CabecalhoEmailTO
     */
    public static function newInstance($data = null)
    {
        $cabecalhoEmailTO = new CabecalhoEmailTO();

        if ($data != null) {
            $cabecalhoEmailTO->setId(Utils::getValue('id', $data));
            $cabecalhoEmailTO->setTitulo(Utils::getValue('titulo', $data));
            $cabecalhoEmailTO->setTextoRodape(Utils::getValue('textoRodape', $data));
            $cabecalhoEmailTO->setTextoCabecalho(Utils::getValue('textoCabecalho', $data));
            $cabecalhoEmailTO->setIsRodapeAtivo(Utils::getBooleanValue('isRodapeAtivo', $data));
            $cabecalhoEmailTO->setIsCabecalhoAtivo(Utils::getBooleanValue('isCabecalhoAtivo', $data));
            $cabecalhoEmailTO->setNomeImagemFisicaRodape(Utils::getValue('nomeImagemFisicaRodape', $data));
            $cabecalhoEmailTO->setNomeImagemFisicaCabecalho(Utils::getValue('nomeImagemFisicaCabecalho', $data));
        }

        return $cabecalhoEmailTO;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getNomeImagemFisicaCabecalho()
    {
        return $this->nomeImagemFisicaCabecalho;
    }

    /**
     * @param string $nomeImagemFisicaCabecalho
     */
    public function setNomeImagemFisicaCabecalho($nomeImagemFisicaCabecalho)
    {
        $this->nomeImagemFisicaCabecalho = $nomeImagemFisicaCabecalho;
    }

    /**
     * @return string
     */
    public function getImagemCabecalho()
    {
        return $this->imagemCabecalho;
    }

    /**
     * @param string $imagemCabecalho
     */
    public function setImagemCabecalho($imagemCabecalho)
    {
        $this->imagemCabecalho = $imagemCabecalho;
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
     * @return bool
     */
    public function isCabecalhoAtivo()
    {
        return $this->isCabecalhoAtivo;
    }

    /**
     * @param bool $isCabecalhoAtivo
     */
    public function setIsCabecalhoAtivo($isCabecalhoAtivo)
    {
        $this->isCabecalhoAtivo = $isCabecalhoAtivo;
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
     * @return string
     */
    public function getNomeImagemFisicaRodape()
    {
        return $this->nomeImagemFisicaRodape;
    }

    /**
     * @param string $nomeImagemFisicaRodape
     */
    public function setNomeImagemFisicaRodape($nomeImagemFisicaRodape)
    {
        $this->nomeImagemFisicaRodape = $nomeImagemFisicaRodape;
    }

    /**
     * @return string
     */
    public function getImagemRodape()
    {
        return $this->imagemRodape;
    }

    /**
     * @param string $imagemRodape
     */
    public function setImagemRodape($imagemRodape)
    {
        $this->imagemRodape = $imagemRodape;
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
     * @return bool
     */
    public function isRodapeAtivo()
    {
        return $this->isRodapeAtivo;
    }

    /**
     * @param bool $isRodapeAtivo
     */
    public function setIsRodapeAtivo($isRodapeAtivo)
    {
        $this->isRodapeAtivo = $isRodapeAtivo;
    }

    /**
     * Carrega a imagem do ródape do cabeçalho de e-mail.
     */
    public function carregarImagemRodape()
    {
        if (!empty($this->getNomeImagemFisicaRodape())) {
            $path = $this->getArquivoService()->getCaminhoRepositorioCabecalhoEmail($this->getId());
            $arquivoRodape = AppConfig::getRepositorio($path, $this->getNomeImagemFisicaRodape());
            $this->setImagemRodape(ImageUtils::getImageBase64($arquivoRodape));
        } else if($this->getId() == Constants::EMAIL_INFORMACAO_MEMBRO) {
            $arquivoRodape = $this->getArquivoService()->getCaminhoDefaultRodape();
            $this->setImagemRodape(ImageUtils::getImageBase64($arquivoRodape));
        }
    }

    /**
     * Carrega a imagem do cabeçalho do cabeçalho de e-mail.
     */
    public function carregarImagemCabecalho()
    {
        if (!empty($this->getNomeImagemFisicaCabecalho())) {
            $path = $this->getArquivoService()->getCaminhoRepositorioCabecalhoEmail($this->getId());
            $arquivoCabecalho = AppConfig::getRepositorio($path, $this->getNomeImagemFisicaCabecalho());
            $this->setImagemCabecalho(ImageUtils::getImageBase64($arquivoCabecalho));
        } else if($this->getId() == Constants::EMAIL_INFORMACAO_MEMBRO) {
            $arquivoCabecalho = $this->getArquivoService()->getCaminhoDefaultCabecalho();
            $this->setImagemCabecalho(ImageUtils::getImageBase64($arquivoCabecalho));
        }
        
    }

    /**
     * Recupera uma instância de 'ArquivoService'.
     *
     * @return ArquivoService|mixed
     */
    private function getArquivoService()
    {
        if(empty($this->arquivoService)){
            $this->arquivoService = app()->make(ArquivoService::class);
        }

        return $this->arquivoService;
    }
}
