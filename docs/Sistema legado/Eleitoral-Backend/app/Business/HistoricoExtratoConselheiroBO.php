<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 19/11/2019
 * Time: 10:35
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Entity;
use App\Entities\HistoricoExtratoConselheiro;
use App\Exceptions\NegocioException;
use App\Factory\PDFFActory;
use App\Factory\XLSFactory;
use App\Repository\HistoricoExtratoConselheiroRepository;
use App\Service\CorporativoService;
use App\To\ArquivoTO;
use App\Util\Utils;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Mpdf\MpdfException;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'HistoricoExtratoConselheiro'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class HistoricoExtratoConselheiroBO extends AbstractBO
{
    /**
     * @var HistoricoExtratoConselheiroRepository
     */
    private $historicoExtratoConselheiroRepository;

    /**
     * @var PDFFActory
     */
    private $pdfFactory;

    /**
     * @var XLSFactory
     */
    private $xlsFactory;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var ParametroConselheiroBO
     */
    private $parametroConselheiroBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->historicoExtratoConselheiroRepository = $this->getRepository(
            HistoricoExtratoConselheiro::class
        );
    }

    /**
     * Retorna o objeto HistoricoExtratoConselheiro construído para salvar o histórico
     *
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param $idUsuario
     * @param $jsonDados
     * @param null $descricao
     * @param int $acao
     * @return HistoricoExtratoConselheiro
     * @throws \Exception
     */
    public function criarHistorico(
        AtividadeSecundariaCalendario $atividadeSecundaria,
        $idUsuario,
        $jsonDados,
        $descricao = null,
        $acao = Constants::HISTORICO_ACAO_INSERIR
    ) {
        $historico = HistoricoExtratoConselheiro::newInstance();
        $historico->setDataHistorico(Utils::getData());
        $historico->setDescricao($descricao);
        $historico->setAcao($acao);
        $historico->setResponsavel($idUsuario);
        $historico->setJsonDados($jsonDados);
        $historico->setAtividadeSecundaria($atividadeSecundaria);

        return $historico;
    }

    /**
     * Método responsável por salvar a instancia de Histórico Extrato Conselheiro
     * @param HistoricoExtratoConselheiro $historicoExtratoConselheiro
     * @return HistoricoExtratoConselheiro|array
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvar(HistoricoExtratoConselheiro $historicoExtratoConselheiro)
    {
        return $this->historicoExtratoConselheiroRepository->persist($historicoExtratoConselheiro);
    }

    /**
     * Método que retorna o maior numero de um Histórico de Extrato de Conselheiros
     *
     * @param $idAtividadeSecundaria
     * @return mixed|null
     */
    public function getNumeroHistoricoExtratoPorAtvSec($idAtividadeSecundaria)
    {
        $resultado = $this->historicoExtratoConselheiroRepository->getNumeroHistoricoExtratoPorAtvSec($idAtividadeSecundaria);

        if (!empty($resultado)) {
            $resultado = Utils::getValue('numero', $resultado[0]);
        }

        return $resultado;
    }

    /**
     * Método que retorna Historico Extrato Conselheiro por atividade secundaria
     *
     * @param $idAtividadeSecundaria
     * @return array|null
     */
    public function getPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        return $this->historicoExtratoConselheiroRepository->getPorAtividadeSecundaria($idAtividadeSecundaria);
    }

    /**
     * Gera o documento PDF com a lista de Conselheiros por UF
     *
     * @param $idHistoricoExtrato
     * @return ArquivoTO
     * @throws NegocioException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws \Exception
     */
    public function gerarDocumentoListaConselheiros($idHistoricoExtrato)
    {
        $historicoExtrato = $this->historicoExtratoConselheiroRepository->getPorId($idHistoricoExtrato);
        $nomeArquivo = $historicoExtrato->getDescricao();
        $dados = json_decode($historicoExtrato->getJsonDados());
        $dados->totalProf = $this->getCorporativoService()->getQtdProfissionais();
        return $this->getPdfFactory()->gerarDocumentoListaConselheiros($dados, $nomeArquivo);
    }

    /**
     *
     * @param $idHistoricoExtrato
     * @return ArquivoTO
     * @throws NegocioException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \Exception
     */
    public function gerarDocumentoXSLListaConselheiros($idHistoricoExtrato)
    {
        $historicoExtrato = $this->historicoExtratoConselheiroRepository->getPorId($idHistoricoExtrato);
        $nomeArquivo = $historicoExtrato->getDescricao();
        $dados = json_decode($historicoExtrato->getJsonDados());
        $dados->totalProf = $this->getCorporativoService()->getQtdProfissionais();
        return $this->getXlsFactory()->gerarDocumentoXSLListaConselheiros($dados, $nomeArquivo);
    }

    /**
     * Método responsável por gerar arquivo zip com os arquivos de extrato gerados
     *
     * @param $idHistoricoExtrato
     * @return ArquivoTO
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \Exception
     */
    public function gerarDocumentoZIPListaConselheiros($idHistoricoExtrato)
    {
        $historicoExtrato = $this->historicoExtratoConselheiroRepository->getPorId($idHistoricoExtrato);

        $this->getParametroConselheiroBO()->verificarGeracaoExtratoTodosProfissionais($historicoExtrato);

        return $this->getParametroConselheiroBO()->getExtratoProfissionaisZip($historicoExtrato);
    }

    /**
     *
     * @param int $idHistoricoExtrato
     * @return HistoricoExtratoConselheiro
     * @throws \Exception
     */
    public function getPorId($idHistoricoExtrato)
    {
        return $this->historicoExtratoConselheiroRepository->getPorId($idHistoricoExtrato);
    }

    /**
     * Retorna a instância de PDFFactory conforme o padrão Lazy Initialization.
     *
     * @return PDFFActory
     */
    private function getPdfFactory()
    {
        if (empty($this->pdfFactory)) {
            $this->pdfFactory = app()->make(PDFFActory::class);
        }

        return $this->pdfFactory;
    }

    /**
     * Retorna a instância de XLSFactory conforme o padrão Lazy Initialization.
     *
     * @return XLSFactory
     */
    private function getXlsFactory()
    {
        if (empty($this->xlsFactory)) {
            $this->xlsFactory = app()->make(XLSFactory::class);
        }

        return $this->xlsFactory;
    }

    /**
     * Retorna a instancia de CorporativoService
     *
     * @return CorporativoService|mixed
     */
    private function getCorporativoService()
    {
        if (empty($this->corporativoService)) {
            $this->corporativoService = app()->make(CorporativoService::class);
        }
        return $this->corporativoService;
    }

    /**
     * Retorna a instancia de ParametroConselheiroBO
     *
     * @return ParametroConselheiroBO|mixed
     */
    private function getParametroConselheiroBO()
    {
        if (empty($this->parametroConselheiroBO)) {
            $this->parametroConselheiroBO = app()->make(ParametroConselheiroBO::class);
        }
        return $this->parametroConselheiroBO;
    }
}
