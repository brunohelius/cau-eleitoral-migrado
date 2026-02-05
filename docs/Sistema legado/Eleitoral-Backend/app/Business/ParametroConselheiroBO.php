<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 12/11/2019
 * Time: 16:52
 */

namespace App\Business;

use App\Config\AppConfig;
use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Calendario;
use App\Entities\Entity;
use App\Entities\Filial;
use App\Entities\HistoricoExtratoConselheiro;
use App\Entities\Lei;
use App\Entities\ParametroConselheiro;
use App\Entities\ProporcaoConselheiroExtrato;
use App\Entities\UfCalendario;
use App\Entities\Usuario;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\PDFFActory;
use App\Factory\XLSFactory;
use App\Jobs\GerarExtratoTodosProfissionaisJob;
use App\Repository\AtividadeSecundariaCalendarioRepository;
use App\Repository\CalendarioRepository;
use App\Repository\ParametroConselheiroRepository;
use App\Repository\ProporcaoConselheiroExtratoRepository;
use App\Repository\UsuarioRepository;
use App\Service\CorporativoService;
use App\To\ArquivoTO;
use App\To\ExtratoProfissionaisTO;
use App\To\UsuarioTO;
use App\Util\Utils;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Mpdf\MpdfException;
use stdClass;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'ParametroConselheiro'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class ParametroConselheiroBO extends AbstractBO
{
    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var ParametroConselheiroRepository
     */
    private $parametroConselheiroRepository;

    /**
     * @var AtividadeSecundariaCalendarioRepository
     */
    private $atividadeSecundariaCalendarioRepository;

    /**
     * @var UsuarioRepository
     */
    private $usuarioRepository;

    /**
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * @var HistoricoExtratoConselheiroBO
     */
    private $historicoExtratoConselheiroBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaBO;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var CalendarioRepository
     */
    private $calendarioRepository;

    /**
     * @var HistoricoParametroConselheiroBO
     */
    private $historicoParametroConselheiroBO;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var PDFFActory
     */
    private $pdfFactory;

    /**
     * @var XLSFactory
     */
    private $xlsFactory;

    /**
     * @var ProporcaoConselheiroExtratoRepository
     */
    private $proporcaoConselheiroExtratoRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->parametroConselheiroRepository = $this->getRepository(ParametroConselheiro::class);
        $this->atividadeSecundariaCalendarioRepository = $this->getRepository(AtividadeSecundariaCalendario::class);
        $this->calendarioRepository = $this->getRepository(Calendario::class);
        $this->proporcaoConselheiroExtratoRepository = $this->getRepository(ProporcaoConselheiroExtrato::class);
        $this->usuarioRepository = $this->getRepository(Usuario::class);
    }

    /**
     * @param $idAtividadePrincipal
     * @param $idCauUf
     * @return ParametroConselheiro|null
     */
    public function getParamConselheiroPorAtividadePrincipalAndCauUf($idAtividadePrincipal, $idCauUf)
    {
        $parametroConselheiro = $this->parametroConselheiroRepository->getUltimaPorAtividadePrincipalECauUf(
            $idAtividadePrincipal, $idCauUf
        );

        return $parametroConselheiro;
    }

    /**
     * @param $idCalendario
     * @param $idCauUf
     * @return ParametroConselheiro|null
     */
    public function getParamConselheiroPorCalendarioAndCauUf($idCalendario, $idCauUf)
    {
        $parametroConselheiro = $this->parametroConselheiroRepository->getUltimaPorCalendarioECauUf(
            $idCalendario, $idCauUf
        );

        return $parametroConselheiro;
    }

    /**
     * Retorna os parametros conselheiros conforme o filtro informado
     *
     * @param $filtroTO
     * @return mixed
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function getParametroConselheiroPorFiltro($filtroTO)
    {
        $retorno = null;
        $parametroConselheiro = $this->parametroConselheiroRepository->getParametroConselheiroPorFiltro($filtroTO);

        if (empty($parametroConselheiro)) {
            $retorno['listaProfissionais'] = $this->getDadosIniciais($filtroTO);
            $retorno['totalProfAtivos'] = 0;
        } else {
            $cauUfs = $this->getFilialBO()->getFiliais();
            $retorno['listaProfissionais'] = $this->organizeIdCauUf($cauUfs, $parametroConselheiro, 'getId');
            $retorno['totalProfAtivos'] = $this->somarProfissionaisAtivos($parametroConselheiro);
        }
        $retorno['totalProf'] = $this->getProfissionalBO()->quantidadeTodosProfissionais();
        $this->addParamHasJustificativaEHasIniciadaAtiv21($retorno, $filtroTO->idAtividadeSecundaria);

        return $retorno;
    }

    /**
     * Gera o extrato com todos os profissionais - HST 19
     *
     * @param int $idHistoricoExtratoConselheiro
     * @param string $sigla
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws Exception
     */
    public function gerarExtratoTodosProfissionais(int $idHistoricoExtratoConselheiro, string $sigla)
    {
        $historico = $this->getHistoricoExtratoConselheiroBO()->getPorId($idHistoricoExtratoConselheiro);

        /** @var $calendario Calendario */
        $calendario = $this->calendarioRepository->getPorAtividadeSecundaria($historico->getAtividadeSecundaria()->getId());

        //Retorna a quantidade de todos profissionais
        $quantidadeTodosProfissionais = $this->getProfissionalBO()->quantidadeTodosProfissionaisPorUf($sigla);

        //Retorna a quantidade de profissionais Ativos
        $quantidadeProfissionaisAtivos = $this->getProfissionalBO()->quantidadeTodosProfissionaisPorUf($sigla, true);

        //Busca todos os profissionais
        $profissionais = $this->getProfissionalBO()->getTodosProfissionaisPorUf($sigla);

        $extratoProfissionaisTO = new ExtratoProfissionaisTO();
        $extratoProfissionaisTO->setTotalProfissionaisAtivos($quantidadeProfissionaisAtivos);
        $extratoProfissionaisTO->setTotalProfissionais($quantidadeTodosProfissionais);
        $extratoProfissionaisTO->setProfissionais($profissionais);

        $nomeArquivo = $this->nomeArquivoExtrato($historico, $calendario, $sigla);

        $this->getXlsFactory()->gerarDocumentoXSLXTotalProfissionais($extratoProfissionaisTO, $nomeArquivo, $idHistoricoExtratoConselheiro, $sigla);

        $caminho = ($this->getCaminhoExtratoProfissionais($idHistoricoExtratoConselheiro));
        $arquivos = glob($caminho . '*.xlsx');

        $ufsCalendario = $this->getFilialBO()->getFiliaisComBandeirasPorCalendario($calendario->getId());

        if (count($arquivos) == count($ufsCalendario)) {
            $this->salvarDescricaoHistoricoExtratoConselheiro($historico, $calendario);
        }
    }

    /**
     * Método que verifica status da geração do extrato e salva a descrição
     *
     * @param HistoricoExtratoConselheiro $historicoExtratoConselheiro
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function verificarGeracaoExtratoTodosProfissionais(HistoricoExtratoConselheiro $historicoExtratoConselheiro)
    {
        $caminho = ($this->getCaminhoExtratoProfissionais($historicoExtratoConselheiro->getId()));
        $arquivos = glob($caminho . '*.xlsx');

        $calendario = $historicoExtratoConselheiro->getAtividadeSecundaria()
            ->getAtividadePrincipalCalendario()->getCalendario();
        $ufsCalendario = $this->getFilialBO()->getFiliaisComBandeirasPorCalendario($calendario->getId());

        if (count($arquivos) == 27 && empty($historicoExtratoConselheiro->getDescricao())){
            $this->salvarDescricaoHistoricoExtratoConselheiro($historicoExtratoConselheiro);
        }

        if (empty($historicoExtratoConselheiro->getDescricao()) && count($arquivos) != 27){
            throw new NegocioException(Message::MSG_HISTORICO_EXTRATO_CONSELHEIROS_EM_GERACAO);
        }
    }

    /**
     * Gera o extrato zip com todos os profissionais - HST 19
     *
     * @param HistoricoExtratoConselheiro $historico
     * @return ArquivoTO
     */
    public function getExtratoProfissionaisZip(HistoricoExtratoConselheiro $historico)
    {
        $nome = $historico->getDescricao() . '.zip';

        $zip_file = $nome;

        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $caminhoCompleto = $this->getCaminhoExtratoProfissionais($historico->getId());
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($caminhoCompleto));

        foreach ($files as $name => $file)
        {
            // We're skipping all subfolders
            if (!$file->isDir()) {
                $filePath     = $file->getRealPath();

                // extracting filename with substr/strlen
                $relativePath = 'extratos/' . substr($filePath, strlen($caminhoCompleto));

                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();

        $arquivoTO = new ArquivoTO();
        $arquivoTO->name = $zip_file;
        $info = new \finfo(FILEINFO_MIME_TYPE);
        $arquivoTO->type = $info->file($zip_file);
        $arquivoTO->file = file_get_contents($zip_file);

        return $arquivoTO;
    }

    /**
     * Retorna a lista de profissionais e conselheiros por UF.
     *
     * @param stdClass $dadosTO
     * @param string $descHist
     * @return mixed
     * @throws Exception
     */
    public function atualizarNumeroConselheiros($dadosTO, $descHist = Constants::PARAM_CONSELHEIRO_DESC_EXTRATO_GERADO)
    {
        try {
            $this->beginTransaction();

            $listaProfissionais = $this->getProfissionalBO()->quantidadeTodosProfissionaisAgrupadoPorUf(true);

            if (!empty($listaProfissionais)) {
                foreach ($listaProfissionais as $i => $item) {
                    if (!empty($item)) {
                        $item['situacaoEditado'] = false;
                        $listaProfissionais[$i] = $this->defineQuantidadeConselheirosLei($item);
                    }
                }
            }

            $this->excluir($dadosTO);
            $numero = $this->getHistoricoExtratoConselheiroBO()->getNumeroHistoricoExtratoPorAtvSec($dadosTO->idAtividadeSecundaria);

            $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getPorId($dadosTO->idAtividadeSecundaria);

            if (!empty($dadosTO->idsCauUf)) {
                $ufsCalendario = [];
                foreach ($dadosTO->idsCauUf as $idCauUf) {
                    $filial = Filial::newInstance();
                    $filial->setId($idCauUf);
                    $ufsCalendario[] = $filial;
                }
            }
            else {
                $calendario = $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario();
                $ufsCalendario = $this->getFilialBO()->getFiliaisComBandeirasPorCalendario($calendario->getId());
            }

            /** @var Filial[] $cauUfs */
            $cauUfs = $this->getFilialBO()->getFiliais();
            $listaProfissionais = $this->organizeIdCauUf($cauUfs, $listaProfissionais);
            $listaProfissionais = $this->selecionarCauUfCalendario($ufsCalendario, $listaProfissionais);

            if (empty($dadosTO->idsCauUf) && $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->isSituacaoIES()) {
                $listaProfissionais[] = $this->getDadosIniciaisIES();
            }

            $dados = array();
            $dadosProporcao = array();

            foreach ($listaProfissionais as $paramConselheiro) {
                $param = $paramConselheiro;
                if (!empty($paramConselheiro) && is_array($paramConselheiro)) {
                    $param = ParametroConselheiro::newInstance($paramConselheiro);
                }

                if($param instanceof ParametroConselheiro){
                    $proporcao = ProporcaoConselheiroExtrato::newInstance([
                        'idCauUf' => $param->getIdCauUf(),
                        'numeroProporcaoConselheiro' => $param->getNumeroProporcaoConselheiro()
                    ]);
                    $param->setAtividadeSecundaria($atividadeSecundaria);

                    $dados[] = $param;
                    $dadosProporcao[] = $proporcao;
                }
            }

            /** @var ParametroConselheiro $conselheiro */
            foreach ($dados as $conselheiro) {
                $this->salvarDados($conselheiro);
            }

            $totalProf = $this->getProfissionalBO()->quantidadeTodosProfissionais();

            $jsonDados['totalProfAtivos'] = $this->somarProfissionaisAtivos($listaProfissionais);

            $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

            $historicoExtrato = $this->getHistoricoExtratoConselheiroBO()->criarHistorico(
                $atividadeSecundaria,
                $usuarioLogado->id ?? null,
                '',
                ''
            );

            $historicoExtrato->setNumero($numero + 1);
            $historicoExtratoSalvo = $this->getHistoricoExtratoConselheiroBO()->salvar($historicoExtrato);

            foreach ($ufsCalendario as $filial) {
                if ($filial->getId() != Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                    Queue::push(new GerarExtratoTodosProfissionaisJob($historicoExtratoSalvo->getId(), $filial->getPrefixo()));
                }
            }

            if (!empty($dadosProporcao)) {
                foreach ($dadosProporcao as $proporcao) {
                    $proporcao->setHistoricoExtratoConselheiro($historicoExtratoSalvo);
                    $this->proporcaoConselheiroExtratoRepository->persist($proporcao);
                }
            }

            $historico = $this->getHistoricoBO()->criarHistorico(
                $atividadeSecundaria,
                Constants::HISTORICO_TIPO_REFERENCIA_PARAMETRO_CONSELHEIRO,
                Constants::HISTORICO_ACAO_ALTERAR,
                    $descHist,
                   !empty($dadosTO->justificativa) ? $dadosTO->justificativa : '');

            $this->getHistoricoBO()->salvar($historico);

            $atividadesSecundarias = $this->atividadeSecundariaCalendarioRepository->getAtividadesSecundariasPorVigencia($dataAtual, null, 1, 6);

            if (!empty($atividadesSecundarias)) {
                foreach ($atividadesSecundarias as $atividadeSecundaria) {
                    $dadosTO = new stdClass();
                    $dadosTO->idAtividadeSecundaria = $atividadeSecundaria->getId();
                    $this->atualizarNumeroConselheiros($dadosTO, Constants::PARAM_CONSELHEIRO_DESC_ATUALIZACAO_REALIZADA);
                }
            }

            $filtroTO = new stdClass();
            $filtroTO->idAtividadeSecundaria = $atividadeSecundaria->getId();
            $parametroConselheiro = $this->parametroConselheiroRepository->getParametroConselheiroPorFiltro($filtroTO);

            $retorno['listaProfissionais'] = $this->organizeIdCauUf($cauUfs, $parametroConselheiro, 'getId');
            $retorno['totalProfAtivos'] = $jsonDados['totalProfAtivos'];
            $retorno['totalProf'] = $totalProf;

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        return $retorno;
    }


    /**
     * Salva os dados de Parametro Conselheiro
     *
     * @param $paramConselheiro
     * @param null $justificativa
     * @param string $descHistorico
     * @throws NegocioException
     * @throws Exception
     */
    public function salvar($paramConselheiro, $justificativa = null, $descHistorico = Constants::PARAM_CONSELHEIRO_DESC_ATUALIZACAO_REALIZADA)
    {
        if (empty($paramConselheiro)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS);
        }

        $acao = Constants::HISTORICO_ACAO_INSERIR;

        try {
            $this->beginTransaction();

            if (!empty($paramConselheiro->getId())) {
                $acao = Constants::HISTORICO_ACAO_ALTERAR;
            }

            if (!empty($paramConselheiro->getQtdAtual())) {
                $descHistorico = 'Alteração na proporção de conselheiro '.$paramConselheiro->getPrefixo();
                $descHistorico .= ': de '.$paramConselheiro->getQtdAtual(). ' para '.$paramConselheiro->getNumeroProporcaoConselheiro();
                $paramConselheiro->setSituacaoEditado(true);
            }

            $parametroConselheiroSalvo = $this->salvarDados($paramConselheiro);

            $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
            $historico = $this->getHistoricoParametroConselheiroBO()->criarHistorico(
                $parametroConselheiroSalvo,
                $usuarioLogado->id,
                $descHistorico,
                $acao,
                $justificativa);

            $this->getHistoricoParametroConselheiroBO()->salvar($historico);

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Rotina automática que busca por todas as Atividades Secundarias 1.6 e atualiza os dados de conselheiros no dia vigente
     *
     * @throws Exception
     */
    public function atualizarConselheiroAutomatico()
    {
        $dataAtual = Utils::getData();
        $atividadesSecundarias = $this->atividadeSecundariaCalendarioRepository->getAtividadesSecundariasPorVigencia($dataAtual, null, 1, 6);

        if (!empty($atividadesSecundarias)) {
            foreach ($atividadesSecundarias as $atividadeSecundaria) {
                $dadosTO = new stdClass();
                $dadosTO->idAtividadeSecundaria = $atividadeSecundaria->getId();
                $this->atualizarNumeroConselheiros($dadosTO, Constants::PARAM_CONSELHEIRO_DESC_ATUALIZACAO_REALIZADA);
            }
        }
    }

    /**
     *
     * @param $filtroTO
     * @return ArquivoTO
     * @throws NegocioException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function gerarDocumentoXSLListaTotalConselheiros($filtroTO)
    {
        $listaConselheiros = $this->getListaConselheirosExportacao($filtroTO);
        return $this->getXlsFactory()->gerarDocumentoXSLListaTotalConselheiros($listaConselheiros);
    }

    /**
     *
     * @param $filtroTO
     * @return ArquivoTO
     * @throws NegocioException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function gerarDocumentoPDFListaTotalConselheiros($filtroTO)
    {
        $listaConselheiros = $this->getListaConselheirosExportacao($filtroTO);
        return $this->getPdfFactory()->gerarDocumentoPDFListaTotalConselheiros($listaConselheiros);
    }

    /**
     * @param ParametroConselheiro $paramConselheiro
     *
     * Salva um ParamConselheiro por vez
     * @return Entity|array|bool|ObjectManagerAware|object
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function salvarDados($paramConselheiro)
    {
        $atividadeSecundariaSalva = null;

        if($paramConselheiro->getIdCauUf() == Constants::ID_IES_LISTA_CONSELHEIROS){
            $paramConselheiro->setLei(null);
            $paramConselheiro->setQtdProfissional(0);
        }

        if (!empty($paramConselheiro->getId())) {
            $paramAtualBD = $this->parametroConselheiroRepository->find($paramConselheiro->getId());

            if ($paramConselheiro->getQtdProfissional() != $paramAtualBD->getQtdProfissional()) {
                $paramConselheiro->setSituacaoEditado(true);
            }
        }

        $atividadeSecundariaSalva = $this->atividadeSecundariaCalendarioRepository->find($paramConselheiro->getAtividadeSecundaria()->getId());
        $paramConselheiro->setAtividadeSecundaria($atividadeSecundariaSalva);

        return $this->parametroConselheiroRepository->persist($paramConselheiro);
    }

    /**
     * Retorna os dados iniciais para quando não existem dados no BD para a atividade buscada.
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    private function getDadosIniciais($dadosTO)
    {
        $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getPorId($dadosTO->idAtividadeSecundaria);
        $ufsCalendario = $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getCauUf();
        $isIES = $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->isSituacaoIES();

        //Caso seja buscado por UF
        if (!empty($dadosTO->idsCauUf)) {
            $ufsCalendario = $ufsCalendario->filter(
                function($entry) use ($dadosTO) {
                    return in_array($entry->getIdCauUf(), $dadosTO->idsCauUf);
                }
            );
        }

        $cauUfs = $this->getFilialBO()->getFiliais();

        $listaProfissionais = Array();
        foreach ($ufsCalendario as $ufCalendario) {
            $parametroConselheiro = ParametroConselheiro::newInstance();
            $parametroConselheiro->setIdCauUf($ufCalendario->getIdCauUf());
            $parametroConselheiro->setQtdProfissional(0);
            $parametroConselheiro->setNumeroProporcaoConselheiro(0);
            $parametroConselheiro->setSituacaoEditado(false);
            $parametroConselheiro->setLei(Lei::newInstance());

            /** @var Filial $cauUf */
            foreach ($cauUfs as $cauUf) {

                if ($cauUf->getId() == $ufCalendario->getIdCauUf()) {
                    $parametroConselheiro->setPrefixo($cauUf->getPrefixo());
                    $parametroConselheiro->setDescricao($cauUf->getDescricao());
                }

            }

            $listaProfissionais[] = $parametroConselheiro;
        }

        if ($isIES) {
            $listaProfissionais[] = $this->getDadosIniciaisIES();
        }

        return $listaProfissionais;
    }

    /**
     * Retorna os dados do histórico como lista
     * @param $idAtividadeSecundaria
     * @return array|null
     * @throws DBALException
     * @throws NegocioException
     */
    public function getHistorico($idAtividadeSecundaria)
    {
        $filtroTO = new stdClass();
        $filtroTO->idAtividadeSecundaria = $idAtividadeSecundaria;

        $listaHistorico = $this->getHistoricoParametroConselheiroBO()->getHistoricoCompleto($filtroTO);

        if (!empty($listaHistorico)) {
            $usuariosHistorico = $this->getUsuariosHistorico($listaHistorico);
            foreach ($listaHistorico as $i => $historico) {
                $usuario = !empty($usuariosHistorico[$historico['responsavel']])
                    ? $usuariosHistorico[$historico['responsavel']]
                    : null;

                $listaHistorico[$i]['nomeResponsavel'] = Constants::PARAM_CONSELHEIRO_NOME_RESP_SISTEMA;
                if($historico['descricao'] != Constants::PARAM_CONSELHEIRO_DESC_ATUALIZACAO_REALIZADA){
                    $listaHistorico[$i]['nomeResponsavel'] = !empty($usuario) ? $usuario->getNome() : "";
                }
                $listaHistorico[$i]['datahistorico'] = Utils::getDataToString($historico['datahistorico'], "Y-m-d H:i:s");
            }
        }

        return $listaHistorico;
    }

    /**
     * @param array $historicosParametroConselheiroTO
     * @return array
     * @throws NegocioException
     */
    private function getUsuariosHistorico(array $historicosParametroConselheiroTO): array
    {
        $usuariosHistorico = [];

        if(!empty($historicosParametroConselheiroTO)){
            $idsUsuarios = [];

            foreach ($historicosParametroConselheiroTO as $historicoParametroConselheiroTO) {
                if(!empty($historicoParametroConselheiroTO['responsavel'])) {
                    $idsUsuarios[] = $historicoParametroConselheiroTO['responsavel'];
                }
            }

            if(!empty($idsUsuarios)) {
                $idsUsuarios = array_unique($idsUsuarios, SORT_NUMERIC);
                $usuarios = $this->usuarioRepository->getUsuariosPorIds($idsUsuarios);

                if(!empty($usuarios)) {
                    /** @var UsuarioTO $usuario */
                    foreach ($usuarios as $usuario) {
                        $usuariosHistorico[$usuario->getId()] = $usuario;
                    }
                }
            }
        }

        return $usuariosHistorico;
    }

    /**
     * Define a quantidade de conselheiros por quantidade de profissionais ativos, segundo a Lei 12378, art. 32, §1º
     *
     * @param $quantidade
     * @return int
     */
    private function defineQuantidadeConselheirosLei($item)
    {
        if ($item['qtdProfissional'] <= 499) {
            $item['numeroProporcaoConselheiro'] = 5;
            $item['lei']['id'] = Constants::INCISO_I;
            $item['lei']['descricao'] = Constants::$incisosLei[Constants::INCISO_I];
        }
        if ($item['qtdProfissional'] >= 500 and $item['qtdProfissional'] <= 1000) {
            $item['numeroProporcaoConselheiro'] = 7;
            $item['lei']['id'] = Constants::INCISO_II;
            $item['lei']['descricao'] = Constants::$incisosLei[Constants::INCISO_II];
        }
        if ($item['qtdProfissional'] >= 1001 and $item['qtdProfissional'] <= 3000) {
            $item['numeroProporcaoConselheiro'] = 9;
            $item['lei']['id'] = Constants::INCISO_III;
            $item['lei']['descricao'] = Constants::$incisosLei[Constants::INCISO_III];
        }
        if ($item['qtdProfissional'] > 3000) {
            $item['numeroProporcaoConselheiro'] = (int)(9 + (ceil(($item['qtdProfissional'] - 3000) / 1000)));
            $item['lei']['id'] = Constants::INCISO_IV;
            $item['lei']['descricao'] = Constants::$incisosLei[Constants::INCISO_IV];
        }

        return $item;
    }

    /**
     * Retorna a instancia de CorporativoService
     *
     * @return CorporativoService
     */
    private function getCorporativoService()
    {
        if (empty($this->corporativoService)) {
            $this->corporativoService = app()->make(CorporativoService::class);
        }
        return $this->corporativoService;
    }

    /**
     * Retorna a instancia de HistoricoBO
     *
     * @return HistoricoBO
     */
    private function getHistoricoBO()
    {
        if (empty($this->historicoBO)) {
            $this->historicoBO = app()->make(HistoricoBO::class);
        }
        return $this->historicoBO;
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

    /**
     * Retorna a instancia de AtividadeSecundariaCalendarioBO
     *
     * @return AtividadeSecundariaCalendarioBO|mixed
     */
    private function getAtividadeSecundariaBO()
    {
        if (empty($this->atividadeSecundariaBO)) {
            $this->atividadeSecundariaBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }
        return $this->atividadeSecundariaBO;
    }

    /**
     * Retorna a instancia de FilialBO
     *
     * @return FilialBO
     */
    private function getFilialBO()
    {
        if (empty($this->filialBO)) {
            $this->filialBO = app()->make(FilialBO::class);
        }
        return $this->filialBO;
    }

    /**
     * Retorna a instancia de ProfissionalBO
     *
     * @return ProfissionalBO
     */
    private function getProfissionalBO()
    {
        if (empty($this->profissionalBO)) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }
        return $this->profissionalBO;
    }

    /**
     * Método para organizar a lista de profissionais setando o id de cau uf
     *
     * @param $filiaisCauUf
     * @param $listaProfissionais
     * @return mixed
     */
    private function organizeIdCauUf($filiaisCauUf, $listaProfissionais, $busca = 'getPrefixo')
    {
        foreach ($listaProfissionais as $i => $profissional) {
            /** @var Filial $filialCauUf */
            foreach ($filiaisCauUf as $filialCauUf) {
                if ($filialCauUf->$busca() == $profissional['idCauUf']) {
                    $listaProfissionais[$i]['idCauUf'] = $filialCauUf->getId();
                    $listaProfissionais[$i]['prefixo'] = $filialCauUf->getPrefixo();
                    $listaProfissionais[$i]['descricao'] = $filialCauUf->getDescricao();
                }
                else if ($profissional['idCauUf'] == Constants::ID_IES_LISTA_CONSELHEIROS) {
                    $listaProfissionais[$i]['idCauUf'] = Constants::ID_IES_LISTA_CONSELHEIROS;
                    $listaProfissionais[$i]['prefixo'] = Constants::DESC_IES_LISTA_CONSELHEIROS;
                    $listaProfissionais[$i]['descricao'] = Constants::DESC_IES_LISTA_CONSELHEIROS;
                }
            }
        }
        return $listaProfissionais;
    }

    /**
     * Método para organizar a lista de profissionais setando o id de cau uf
     *
     * @param $filiaisCauUf
     * @param $listaProfissionais
     * @return mixed
     */
    private function organizeIdCauUfTotal($filiaisCauUf, $listaProfissionais)
    {
        foreach ($listaProfissionais as $i => $profissional) {
            /** @var Filial $filialCauUf */
            foreach ($filiaisCauUf as $filialCauUf) {
                if ($filialCauUf->getId() == $profissional->getIdCauUf()) {
                    $listaProfissionais[$i]->setIdCauUf($filialCauUf->getId());
                    $listaProfissionais[$i]->setPrefixo($filialCauUf->getPrefixo());
                    $listaProfissionais[$i]->setDescricao($filialCauUf->getDescricao());
                }
            }
        }
        return $listaProfissionais;
    }

    /**
     * Filtra a lista de profissionais deixando apenas as UFs cadastradas no calendário.
     *
     * @param Filial[] $ufsCalendario
     * @param array $listaProfissionais
     * @return array
     */
    private function selecionarCauUfCalendario($ufsCalendario, $listaProfissionais)
    {
        $novaLista = array();
        foreach ($ufsCalendario as $ufCalendario) {
            foreach ($listaProfissionais as $profissional) {
                if ($profissional['idCauUf'] == $ufCalendario->getId()) {
                    $novaLista[] = $profissional;
                }
            }
        }

        return $novaLista;
    }

    /**
     * Soma a quantidade de profissionais ativos e retorna o inteiro
     */
    private function somarProfissionaisAtivos($listaProfissionais)
    {
        $qtdProfissional = 0;
        foreach ($listaProfissionais as $profissional) {
            if(!empty($profissional) && is_array($profissional)){
                $qtdProfissional += $profissional['qtdProfissional'];
            }
        }
        return $qtdProfissional;
    }

    /**
     * Exclui os parametros conselheiros dado um filtro
     *
     * @param $filtroTO
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function excluir($filtroTO)
    {
        $paramConselheiros = $this->parametroConselheiroRepository->getParametroConselheiroPorFiltro($filtroTO);

        if (!empty($paramConselheiros)) {
            foreach ($paramConselheiros as $paramConselheiro) {
                $paramConselheiro = $this->parametroConselheiroRepository->find($paramConselheiro['id']);
                $this->parametroConselheiroRepository->delete($paramConselheiro);
            }
        }
    }

    /**
     * Verifica se um determinado Parametro de Conselheiro pode ser alterado com Justificativa
     *
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function addParamHasJustificativaEHasIniciadaAtiv21(&$retorno, $idAtividadeSecundaria)
    {
        $atividadeSecundaria = $this->atividadeSecundariaCalendarioRepository->getPorId($idAtividadeSecundaria);
        $calendario = $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario();

        $atividade16 = $this->atividadeSecundariaCalendarioRepository->getPorCalendario($calendario->getId(), 1, 6);
        $atividade21 = $this->atividadeSecundariaCalendarioRepository->getPorCalendario($calendario->getId(), 2, 1);

        $dataAtual = new DateTime();
        $hasJustificativa = false;
        $hasIniciadaAtividadeChapa = false;
        if (Utils::getDataHoraZero($dataAtual) < Utils::getDataHoraZero($atividade16->getDataInicio())){
            $hasJustificativa = true;
        }
        else if (!empty($atividade21)) {
            if (Utils::getDataHoraZero($dataAtual) >= Utils::getDataHoraZero($atividade21->getDataInicio())) {
                $hasJustificativa = true;
                $hasIniciadaAtividadeChapa = true;
            }
        }

        $retorno['hasJustificativa'] = $hasJustificativa;
        $retorno['hasIniciadaAtividadeChapa'] = $hasIniciadaAtividadeChapa;
    }

    /**
     * Retorna a instancia de HistoricoParametroConselheiroBO
     *
     * @return HistoricoParametroConselheiroBO|mixed
     */
    private function getHistoricoParametroConselheiroBO()
    {
        if (empty($this->historicoParametroConselheiroBO)) {
            $this->historicoParametroConselheiroBO = app()->make(HistoricoParametroConselheiroBO::class);
        }
        return $this->historicoParametroConselheiroBO;
    }

    /**
     * Retorna a lista dos dados de conselheiros para a exportação de XLS e PDF
     * @throws NegocioException
     */
    private function getListaConselheirosExportacao($filtroTO)
    {
        $listaConselheiros = $this->parametroConselheiroRepository->getParametroConselheiroComHistorico($filtroTO);
        $totalProfAtivo = 0;
        $atividadeSecundaria = null;
        if (!empty($listaConselheiros)) {
            foreach ($listaConselheiros as $paramConselheiro) {
                if(empty($atividadeSecundaria)){
                    $atividadeSecundaria = $paramConselheiro->getAtividadeSecundaria();
                }
                $historicoRecente = $atividadeSecundaria->selecionarHistoricoParametroRecente($paramConselheiro->getIdCauUf());

                $paramConselheiro->setHistoricoParametroRecente($historicoRecente);
                $totalProfAtivo += $paramConselheiro->getQtdProfissional();
                if (!empty($historicoRecente)) {
                    $usuario = $this->getCorporativoService()->getUsuarioPorId($historicoRecente->getResponsavel());
                    $historicoRecente->setNomeResponsavel($usuario->getNome());
                }
            }

            $cauUfs = $this->getFilialBO()->getFiliais();
            $listaConselheiros = $this->organizeIdCauUfTotal($cauUfs, $listaConselheiros);

            usort($listaConselheiros, function($a, $b) {
                return $a->getPrefixo() <=> $b->getPrefixo();
            });
        }

        $retorno['listaProfissionais'] = $listaConselheiros;
        $retorno['totalProfAtivo'] = $totalProfAtivo;
        $retorno['totalProf'] = $this->getCorporativoService()->getQtdProfissionais();
        return $retorno;
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
     * Cria e retorna uma instancia de Parametro Conselheiro para IES
     *
     * @return ParametroConselheiro
     * @throws Exception
     */
    private function getDadosIniciaisIES()
    {
        $parametroConselheiro = ParametroConselheiro::newInstance();
        $parametroConselheiro->setIdCauUf(Constants::ID_IES_LISTA_CONSELHEIROS);
        $parametroConselheiro->setQtdProfissional(0);
        $parametroConselheiro->setNumeroProporcaoConselheiro(Constants::NU_PROP_IES_LISTA_CONSELHEIROS);
        $parametroConselheiro->setSituacaoEditado(false);
        $parametroConselheiro->setPrefixo(Constants::DESC_IES_LISTA_CONSELHEIROS);
        $parametroConselheiro->setDescricao(Constants::DESC_IES_LISTA_CONSELHEIROS);

        return $parametroConselheiro;
    }

    /**
     * @param $historico
     * @param Calendario $calendario
     * @param string $sigla
     * @return string
     */
    private function nomeArquivoExtrato(HistoricoExtratoConselheiro $historico, Calendario $calendario, string $sigla = null): string
    {
        $nomeArquivo = '';
        if(!empty($sigla)){
            $nomeArquivo .= $sigla . ' - ';
        }

        $nomeArquivo .= str_pad($historico->getNumero(), 3, "0", STR_PAD_LEFT)
            . '_Extrato Conselheiros ' . str_replace('/', '-', $calendario->getEleicao()->getDescricao());
        return $nomeArquivo;
    }

    /**
     * @param int $idHistoricoExtratoConselheiro
     * @return \RecursiveIteratorIterator
     */
    private function verificaQuantidadeArquivos(int $idHistoricoExtratoConselheiro): \RecursiveIteratorIterator
    {
        $caminhoCompleto = AppConfig::getRepositorio(
            Constants::PATH_STORAGE_ARQUIVO_EXTRATO_PROFISSIONAIS
            . DIRECTORY_SEPARATOR . $idHistoricoExtratoConselheiro);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($caminhoCompleto));
        return $files;
    }

    /**
     * @param int $idHistoricoExtratoConselheiro
     * @return string
     */
    private function getCaminhoExtratoProfissionais(int $idHistoricoExtratoConselheiro): string
    {
        return AppConfig::getRepositorio(
            Constants::PATH_STORAGE_ARQUIVO_EXTRATO_PROFISSIONAIS
            . DIRECTORY_SEPARATOR . $idHistoricoExtratoConselheiro);
    }

    /**
     * Método auxiliar que salva a descrição do histórico extrato conselheir
     *
     * @param HistoricoExtratoConselheiro $historico
     * @param Calendario $calendario
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function salvarDescricaoHistoricoExtratoConselheiro(
        HistoricoExtratoConselheiro $historico,
        ?Calendario $calendario = null
    ): void {

        if(empty($calendario)) {
            $calendario = $this->calendarioRepository->getPorAtividadeSecundaria(
                $historico->getAtividadeSecundaria()->getId()
            );
        }

        $historico->setDescricao($this->nomeArquivoExtrato($historico, $calendario));
        $this->parametroConselheiroRepository->persist($historico);
    }
}
