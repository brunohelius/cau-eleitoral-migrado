<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 16/08/2019
 * Time: 09:16
 */

namespace App\To;

use App\Util\Utils;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada a validação de arquivo
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 */class ArquivoValidarTO
{

    /**
     * @var string
     */
    private $nomeArquivo;

    /**
     * @var string
     */
    private $tamanhoArquivo;

    /**
     * @var integer
     */
    private $tamanhoPermitido;

    /**
     * @var string
     */
    private $codigoMsgTamanhoArquivo;

    /**
     * Fabricação estática de 'ArquivoValidarTO'.
     *
     * @param array|null $data
     *
     * @return ArquivoValidarTO
     */
    public static function newInstance($data = null)
    {
        $arquivoTO = new ArquivoValidarTO();

        if ($data != null) {
            $arquivoTO->setNomeArquivo(Utils::getValue("nomeArquivo", $data));
            $arquivoTO->setTamanhoArquivo(Utils::getValue("tamanhoArquivo", $data));
            $arquivoTO->setTamanhoPermitido(Utils::getValue("tamanhoPermitido", $data));
            $arquivoTO->setCodigoMsgTamanhoArquivo(Utils::getValue("codigoMsgTamanhoArquivo", $data));
        }
        return $arquivoTO;
    }

    /**
     * @return string
     */
    public function getNomeArquivo(): ?string
    {
        return $this->nomeArquivo;
    }

    /**
     * @param string $nomeArquivo
     */
    public function setNomeArquivo(?string $nomeArquivo): void
    {
        $this->nomeArquivo = $nomeArquivo;
    }

    /**
     * @return string
     */
    public function getTamanhoArquivo(): ?string
    {
        return $this->tamanhoArquivo;
    }

    /**
     * @param string $tamanhoArquivo
     */
    public function setTamanhoArquivo(?string $tamanhoArquivo): void
    {
        $this->tamanhoArquivo = $tamanhoArquivo;
    }

    /**
     * @return int
     */
    public function getTamanhoPermitido(): ?int
    {
        return $this->tamanhoPermitido;
    }

    /**
     * @param int $tamanhoPermitido
     */
    public function setTamanhoPermitido(?int $tamanhoPermitido): void
    {
        $this->tamanhoPermitido = $tamanhoPermitido;
    }

    /**
     * @return string
     */
    public function getCodigoMsgTamanhoArquivo(): ?string
    {
        return $this->codigoMsgTamanhoArquivo;
    }

    /**
     * @param string $codigoMsgTamanhoArquivo
     */
    public function setCodigoMsgTamanhoArquivo(?string $codigoMsgTamanhoArquivo): void
    {
        $this->codigoMsgTamanhoArquivo = $codigoMsgTamanhoArquivo;
    }
}