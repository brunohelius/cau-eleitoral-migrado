<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 28/11/2019
 * Time: 10:44
 */

namespace App\Http\Controllers;

use App\Business\HistoricoExtratoConselheiroBO;
use App\Entities\HistoricoExtratoConselheiro;
use App\Exceptions\NegocioException;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Mpdf\MpdfException;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe de controle referente a entidade 'istoricoExtratoConselheiro'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class HistoricoExtratoConselheiroController extends Controller
{
    /**
     * @var \App\Business\HistoricoExtratoConselheiroBO
     */
    private $historicoExtratoConselheiroBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }

    /**
     * Método que retorna Historico Extrato Conselheiro por atividade secundaria
     *
     * @param $idAtividadeSecundaria
     * @return string
     */
    public function getPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        $resp = $this->getHistoricoExtratoConselheiroBO()->getPorAtividadeSecundaria($idAtividadeSecundaria);
        return $this->toJson($resp);
    }

    /**
     * Gera o documento PDF com a lista de Conselheiros por UF
     *
     * @param $idHistoricoExtrato
     * @return Response
     * @throws NegocioException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function gerarDocumentoListaConselheiros($idHistoricoExtrato)
    {
        $documento = $this->getHistoricoExtratoConselheiroBO()->gerarDocumentoListaConselheiros($idHistoricoExtrato);
        return $this->toFile($documento);
    }

    /**
     * Gera o documento ZIP com a lista de profissionais
     *
     * @param $idHistoricoExtrato
     * @return Response
     * @throws NegocioException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function gerarDocumentoZIPListaConselheiros($idHistoricoExtrato)
    {
        $documento = $this->getHistoricoExtratoConselheiroBO()->gerarDocumentoZIPListaConselheiros($idHistoricoExtrato);
        return $this->toFile($documento);
    }

    /**
     *
     * @param $idHistoricoExtrato
     * @return Response
     * @throws NegocioException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws Exception
     */
    public function gerarDocumentoXSLListaConselheiros($idHistoricoExtrato)
    {
        $documento = $this->getHistoricoExtratoConselheiroBO()->gerarDocumentoXSLListaConselheiros($idHistoricoExtrato);
        return $this->toFile($documento);
    }

    /**
     * Retorna a instancia de HistoricoExtratoConselheiroBO
     *
     * @return HistoricoExtratoConselheiroBO|mixed
     */
    private function getHistoricoExtratoConselheiroBO()
    {
        if (empty($this->historicoExtratoConselheiroBO)) {
            $this->historicoExtratoConselheiroBO = app()->make(HistoricoExtratoConselheiroBO::class);
        }
        return $this->historicoExtratoConselheiroBO;
    }
}