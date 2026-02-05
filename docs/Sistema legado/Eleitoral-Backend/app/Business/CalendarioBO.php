<?php
/*
 * CalendarioBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\AppConfig;
use App\Config\Constants;
use App\Entities\ArquivoCalendario;
use App\Entities\AtividadePrincipalCalendario;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Calendario;
use App\Entities\CalendarioSituacao;
use App\Entities\Entity;
use App\Entities\Filial;
use App\Entities\HistoricoCalendario;
use App\Entities\InformacaoComissaoMembro;
use App\Entities\SituacaoCalendario;
use App\Entities\TipoProcesso;
use App\Entities\UfCalendario;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\PDFFActory;
use App\Repository\ArquivoCalendarioRepository;
use App\Repository\CalendarioRepository;
use App\Repository\CalendarioSituacaoRepository;
use App\Repository\InformacaoComissaoMembroRepository;
use App\Repository\SituacaoCalendarioRepository;
use App\Repository\TipoProcessoRepository;
use App\Repository\UfCalendarioRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoTO;
use App\To\CalendarioFiltroTO;
use App\To\CalendarioPublicacaoComissaoEleitoralFiltroTO;
use App\To\CalendarioTO;
use App\To\NumeroMembroTO;
use App\Util\Utils;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use MpdfException;
use stdClass;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'Calendario'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class CalendarioBO extends AbstractBO
{

    /**
     * @var \App\Business\UfCalendarioBO
     */
    private $ufCalendarioBO;

    /**
     * @var CalendarioRepository
     */
    private $calendarioRepository;

    /**
     * @var ArquivoCalendarioRepository
     */
    private $arquivoCalendarioRepository;

    /**
     * @var SituacaoCalendarioRepository
     */
    private $situacaoCalendarioRepository;

    /**
     * @var \App\Service\ArquivoService
     */
    private $arquivoService;

    /**
     * @var \App\Business\PrazoCalendarioBO
     */
    private $prazoCalendarioBO;

    /**
     * @var \App\Business\AtividadePrincipalBO
     */
    private $atividadePrincipalBO;

    /**
     * @var \App\Business\HistoricoCalendarioBO
     */
    private $historicoCalendarioBO;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var UfCalendarioRepository
     */
    private $ufCalendarioRepository;

    /**
     * @var \App\Service\CorporativoService
     */
    private $corporativoService;

    /**
     * @var InformacaoComissaoMembroRepository
     */
    private $informacaoComissaoMembroRepository;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var CalendarioSituacaoRepository
     */
    private $calendarioSituacaoRepository;

    /**
     * @var PDFFActory
     */
    private $pdfFactory;

    /**
     * @var TipoProcessoRepository
     */
    private $tipoProcessoRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->eleicaoBO = app()->make(EleicaoBO::class);
        $this->ufCalendarioBO = app()->make(UfCalendarioBO::class);
        $this->prazoCalendarioBO = app()->make(PrazoCalendarioBO::class);
        $this->atividadePrincipalBO = app()->make(AtividadePrincipalBO::class);
        $this->historicoCalendarioBO = app()->make(HistoricoCalendarioBO::class);
        $this->calendarioRepository = $this->getRepository(Calendario::class);
        $this->arquivoCalendarioRepository = $this->getRepository(ArquivoCalendario::class);
        $this->situacaoCalendarioRepository = $this->getRepository(SituacaoCalendario::class);
        $this->ufCalendarioRepository = $this->getRepository(UfCalendario::class);
        $this->arquivoService = app()->make(ArquivoService::class);
        $this->corporativoService = app()->make(CorporativoService::class);
        $this->informacaoComissaoMembroRepository = $this->getRepository(InformacaoComissaoMembro::class);
        $this->calendarioSituacaoRepository = $this->getRepository(CalendarioSituacao::class);
        $this->tipoProcessoRepository = $this->getRepository(TipoProcesso::class);
    }

    /**
     * Retorna os anos que houveram calendários eleitorais
     *
     * @return array
     */
    public function getAnos()
    {
        return $this->calendarioRepository->getAnos();
    }

    /**
     * Retorna os anos que houveram calendários eleitorais por filtro
     *
     * @param CalendarioFiltroTO $filtroTO
     * @return array
     */
    public function getAnosPorFiltro(CalendarioFiltroTO $filtroTO)
    {
        return $this->calendarioRepository->getAnosPorFiltro($filtroTO);
    }

    /**
     * Retorna lista de anos com eleições concluidas.
     */
    public function getCalendariosConcluidosAnos()
    {
        return $this->calendarioRepository->getCalendariosConcluidosAnos();
    }

    /**
     * Retorna os tipos de processo
     *
     * @return array
     */
    public function getTipoProcesso()
    {
        return $this->tipoProcessoRepository->findAll();
    }

    /**
     * Retorna as eleicoes para todos os anos
     *
     * @return array
     */
    public function getEleicoes()
    {
        return $this->calendarioRepository->getEleicoes();
    }

    /**
     * Retorna quantidade de calendários pela situação.
     * @param integer $idSituacao
     * @return mixed
     * @throws Exception
     */
    public function getTotalCalendariosPorSituacao($idSituacao)
    {
        return $this->calendarioRepository->getTotalCalendariosPorSituacao($idSituacao);
    }

    /**
     * Retorna o calendario conforme o id informado.
     *
     * @param $id
     * @return Calendario|null
     * @throws NonUniqueResultException
     * @throws NegocioException
     */
    public function getPorId($id)
    {
        $calendario = $this->calendarioRepository->getPorId($id);

        if (!empty($calendario)) {
            $calendario = $this->definirProgressoSituacao($calendario);
            $calendario->filtrarPrazos();
        } else {
            throw new NegocioException(Message::NENHUM_CALENDARIO_ENCONTRADO);
        }

        return $calendario;
    }

    /**
     * Retorna o calendario conforme o id da atividade secundária informado.
     *
     * @param $idAtividadedSecundaria
     * @return Calendario|null
     * @throws NonUniqueResultException
     * @throws NegocioException
     */
    public function getCalendarioPorAtividadeSecundaria($idAtividadedSecundaria)
    {
        return $this->calendarioRepository->getPorAtividadeSecundaria($idAtividadedSecundaria);
    }

    /**
     * Retorna o calendario conforme o id da atividade secundária informado.
     *
     * @param $idAtividadedSecundaria
     * @return Calendario|null
     * @throws NonUniqueResultException
     * @throws NegocioException
     */
    public function getPorAtividadeSecundaria($idAtividadedSecundaria)
    {
        $calendario = $this->calendarioRepository->getPorAtividadeSecundaria($idAtividadedSecundaria);

        if (!empty($calendario)) {
            $calendario = $this->definirProgressoSituacao($calendario);
            $calendario->filtrarPrazos();
        } else {
            throw new NegocioException(Message::NENHUM_CALENDARIO_ENCONTRADO);
        }

        return $calendario;
    }

    /**
     * Retorna o calendario conforme o id da atividade secundária informado.
     *
     * @return CalendarioTO[]|null
     */
    public function getPorMembroComissaoLogado()
    {
        $calendarios = $this->calendarioRepository->getPorProfissionalMembroComissao(
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );

        if (empty($calendarios)) {
            throw new NegocioException(Message::NENHUM_CALENDARIO_ENCONTRADO);
        }

        return $calendarios;
    }

    /**
     * Retorna os Calendários conforme o filtro informado.
     *
     * @param CalendarioFiltroTO $filtroCalendarioTO
     * @return array
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCalendariosPorFiltro(CalendarioFiltroTO $filtroCalendarioTO)
    {
        $calendarios = $this->calendarioRepository->getCalendariosPorFiltro($filtroCalendarioTO);

        if (!empty($calendarios)) {
            foreach ($calendarios as $calendario) {
                $this->definirInformacaoParametrizada($calendario);
            }
        }

        return $calendarios;
    }

    /**
     * Retorna calendários por vigência.
     *
     * @param $data
     * @return Calendario[]
     */
    public function getCalendariosVigentes($data = null)
    {
        $data = !empty($data) ? $data : Utils::getDataHoraZero();
        return $this->calendarioRepository->getCalendariosVigentes($data);

    }

    /**
     * Define parametro para a interface mostrar ou não o campo de cadastro de membro para uma determinada eleição.
     *
     * @param CalendarioTO $calendario
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function definirInformacaoParametrizada(CalendarioTO $calendario)
    {
        $calendario->setIsInformacaoParametrizada(false);
        $informacaoComissao = $this->informacaoComissaoMembroRepository->getPorCalendario($calendario->getId());
        $dataAtual = new DateTime();
        $dataFimVigencia = $calendario->getDataFimVigencia();
        $dataInicioVigencia = $calendario->getDataInicioVigencia();

        $situacaoConcluido = false;
        if (!empty($informacaoComissao)) {
            if (Arr::exists($informacaoComissao, 'situacaoConcluido')) {
                $situacaoConcluido = Arr::get($informacaoComissao, 'situacaoConcluido');
            } else {
                if (Arr::exists($informacaoComissao[0], 'situacaoConcluido')) {
                    $situacaoConcluido = Arr::get($informacaoComissao[0], 'situacaoConcluido');
                }
            }
        }

        if ($calendario->getIdSituacao() == Constants::SITUACAO_CALENDARIO_CONCLUIDO
            and !empty($informacaoComissao) and $situacaoConcluido
            and Utils::getDataHoraZero($dataFimVigencia) >= Utils::getDataHoraZero($dataAtual)
            and Utils::getDataHoraZero($dataInicioVigencia) <= Utils::getDataHoraZero($dataAtual)) {
            $calendario->setIsInformacaoParametrizada(true);
        }
    }

    /**
     * Disponibiliza o arquivo 'Resolução' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return ArquivoTO
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getArquivo($idArquivo)
    {
        $arquivoCalendario = $this->getArquivoResolucao($idArquivo);
        $caminho = $this->arquivoService->getCaminhoRepositorioDocumentos($arquivoCalendario->getCalendario()->getId());

        return $this->arquivoService->getArquivo($caminho, $arquivoCalendario->getNomeFisico(),
            $arquivoCalendario->getNome());
    }

    /**
     * Exclui um arquivo de resolução pelo id
     *
     * @param $idArquivo
     * @throws Exception
     */
    public function excluirArquivo($idArquivo)
    {
        try {
            $this->beginTransaction();
            $arquivoCalendario = $this->arquivoCalendarioRepository->find($idArquivo);
            $caminho = $this->arquivoService->getCaminhoRepositorioDocumentos($idArquivo);

            $this->arquivoCalendarioRepository->delete($arquivoCalendario);
            $this->arquivoService->excluir($caminho, $arquivoCalendario->getNomeFisico());
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Salva a entidade 'Calendario'.
     *
     * @param Calendario $calendario
     * @param stdClass $dadosTO
     * @return Calendario
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function salvar(Calendario $calendario, $dadosTO = null)
    {
        $this->validarCamposObrigatorios($calendario);
        $this->validarCamposDatas($calendario);
        $this->validarIdades($calendario);
        $this->validarQuantidadeArquivos($calendario);
        $this->validarArquivos($calendario);
        $this->validarEleicaoExistente($calendario);
        $calendario = $this->setNomeArquivoFisico($calendario);
        $this->atividadePrincipalBO->validarAtividadePrincipal($calendario);
        $isInclusao = false;
        $situacao = null;

        $acao = Constants::ACAO_CALENDARIO_ALTERAR;

        $calendario->setExcluido(false);
        $calendario->setAtivo(true);

        if (empty($calendario->getId())) {
            $isInclusao = true;
            $calendario->getEleicao()->setId(null);
            $acao = Constants::ACAO_CALENDARIO_INSERIR;
            $situacao = $this->criarSituacaoAtual($calendario);
        }

        try {
            $this->beginTransaction();

            $this->excluirDependenciasCalendario($dadosTO);

            $cauUfs = $calendario->getCauUf();
            $arquivosResolucao = clone $calendario->getArquivos();
            $atividadesPrincipais = $calendario->getAtividadesPrincipais();

            $calendario->setCauUf(new ArrayCollection());
            $calendario->setArquivos(new ArrayCollection());
            $calendario->setAtividadesPrincipais(new ArrayCollection());

            $eleicao = $calendario->getEleicao();
            $calendario->setEleicao(null);

            /** @var Calendario $calendarioSalvo */
            $calendarioSalvo = clone $this->calendarioRepository->persist($calendario);

            if (!empty($situacao)) {
                $this->calendarioSituacaoRepository->persist($situacao);
            }

            $this->excluirUfsCalendario($calendarioSalvo);
            $calendarioSalvo = $this->salvarUfsCalendario($cauUfs, $calendarioSalvo);
            $calendarioSalvo = $this->salvarArquivosCalendario($arquivosResolucao, $calendarioSalvo, $calendario,
                $isInclusao);

            $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
            $this->salvarAtividades($atividadesPrincipais, $calendarioSalvo, $dadosTO->isCalendarioReplicado);
            $historicoCalendario = $this->historicoCalendarioBO->criarHistorico(
                $calendarioSalvo,
                $usuarioLogado->id,
                Constants::DESC_ABA_PERIODO,
                $acao);

            if (!empty($dadosTO->justificativas)) {
                foreach ($dadosTO->justificativas as $justificativa) {
                    $justificativa->setHistorico($historicoCalendario);
                }
                $historicoCalendario->setJustificativaAlteracao($dadosTO->justificativas);
            }

            $this->historicoCalendarioBO->salvar($historicoCalendario);

            $eleicao->setCalendario($calendarioSalvo);
            $this->eleicaoBO->salvar($eleicao);

            $this->commitTransaction();

        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return $this->getPorId($calendarioSalvo->getId());
    }

    /**
     * Exclui logicamente o Calendario pelo Id
     *
     * @param $id
     * @return Entity|array|bool|ObjectManagerAware|object
     * @throws Exception
     */
    public function excluir($id)
    {
        $calendario = $this->calendarioRepository->getPorId($id);
        $calendario->setAtivo(false);
        $calendario->setExcluido(true);

        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        try {
            $this->beginTransaction();
            $calendarioSalvo = $this->calendarioRepository->persist($calendario);
            $historicoCalendario = $this->historicoCalendarioBO->criarHistorico($calendarioSalvo, $usuarioLogado->id,
                null, Constants::ACAO_CALENDARIO_EXCLUIR);
            $this->historicoCalendarioBO->salvar($historicoCalendario);
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        return $calendarioSalvo;
    }

    /**
     * Inativa o 'Calendario' na base de dados.
     *
     * @param CalendarioTO $calendarioTO
     * @param Request $request
     * @return Calendario
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function inativar(CalendarioTO $calendarioTO, Request $request)
    {
        try {
            $this->beginTransaction();

            $calendario = $this->calendarioRepository->getPorId($calendarioTO->getId());
            $calendario->setAtivo(false);
            $calendario->setIdSituacaoVigente(Constants::SITUACAO_CALENDARIO_INATIVADO);
            $situacao = $this->criarSituacaoAtual($calendario);
            $calendario->setSituacoes(null);

            $calendarioSalvo = $this->calendarioRepository->persist($calendario);

            $situacao->setCalendario($calendarioSalvo);
            $situacaoSalva = $this->calendarioSituacaoRepository->persist($situacao);

            $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
            //Salvar no Histórico
            if (!empty($calendarioSalvo->getId())) {
                $historicoCalendario = $this->historicoCalendarioBO->criarHistorico(
                    $calendarioSalvo,
                    $usuarioLogado->id,
                    Constants::DESC_ABA_PERIODO,
                    Constants::ACAO_CALENDARIO_INATIVAR
                );
                $this->historicoCalendarioBO->salvar($historicoCalendario);
            }

            $idEleicao = !empty($calendario->getEleicao()) ? $calendario->getEleicao()->getId() : null;
            $this->eleicaoBO->inativar($idEleicao);

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return $this->getPorId($calendarioSalvo->getId());
    }

    /**
     * Conclui o 'Calendario' na base de dados.
     *
     * @param CalendarioTO $calendarioTO
     * @param Request $request
     * @return Entity|array|bool|ObjectManagerAware|object
     * @throws Exception
     */
    public function concluir(CalendarioTO $calendarioTO, Request $request)
    {
        try {
            $this->beginTransaction();

            $calendario = $this->calendarioRepository->getPorId($calendarioTO->getId());
            $calendario->setIdSituacaoVigente(Constants::SITUACAO_CALENDARIO_CONCLUIDO);
            $situacao = $this->criarSituacaoAtual($calendario);
            $calendario->setSituacoes(null);
            $calendario->setAtivo(true);

            $calendarioSalvo = $this->calendarioRepository->persist($calendario);

            $situacao->setCalendario($calendarioSalvo);
            $situacaoSalva = $this->calendarioSituacaoRepository->persist($situacao);

            // Salvar no Histórico
            if (!empty($calendarioSalvo->getId())) {
                $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
                $historicoCalendario = $this->historicoCalendarioBO->criarHistorico(
                    $calendarioSalvo,
                    $usuarioLogado->id,
                    Constants::DESC_ABA_PERIODO_PRAZO,
                    Constants::ACAO_CALENDARIO_CONCLUIR
                );

                $this->historicoCalendarioBO->salvar($historicoCalendario);
            }

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return $this->getPorId($calendarioSalvo->getId());
    }

    /**
     * Recupera o histórico referente ao 'id' do calendário informado.
     *
     * @param $idCalendario
     * @return HistoricoCalendario[]
     * @throws NegocioException
     */
    public function getHistorico($idCalendario)
    {
        return $this->historicoCalendarioBO->getHistoricoPorCalendario($idCalendario);
    }

    /**
     * Recupera o agrupamento com a quantidade dos membros da UF para o calendário.
     *
     * @param $idCalendario
     * @return ArrayCollection
     * @throws NegocioException
     */
    public function getAgrupamentoNumeroMembros($idCalendario)
    {
        $agrupamentoNumeroMembros = new ArrayCollection();
        $filiais = $this->getFilialBO()->getFiliais();
        $ufsCalendario = $this->ufCalendarioBO->getUfsCalendario($idCalendario);
        $totalMembrosUf = $this->calendarioRepository->getQuantidadeMembrosPorUf($idCalendario);

        foreach ($ufsCalendario as $ufCalendario) {
            array_map(function ($filial) use ($ufCalendario, $agrupamentoNumeroMembros) {
                /** @var Filial $filial */

                if ($ufCalendario->getIdCauUf() == $filial->getId()) {
                    $numeroMembros = $this->getNumeroMembrosTO($filial);
                    $agrupamentoNumeroMembros->add($numeroMembros);
                }

            }, $filiais);
        }

        foreach ($agrupamentoNumeroMembros as $numeroMembro) {
            array_map(function (NumeroMembroTO $totalMembroUf) use ($numeroMembro) {
                if ($totalMembroUf->getIdCauUf() == $numeroMembro->getIdCauUf()) {
                    $quantidade = empty($totalMembroUf->getQuantidade()) ? 0 : $totalMembroUf->getQuantidade();
                    $numeroMembro->setQuantidade($quantidade);
                }
            }, $totalMembrosUf);
        }

        return $agrupamentoNumeroMembros;
    }

    /**
     * Recupera os calendários que estão disponíveis para publicação.
     *
     * @return array
     * @throws NonUniqueResultException
     */
    public function getCalendariosPublicacaoComissaoEleitoral()
    {
        return $this->calendarioRepository->getCalendariosPublicacaoComissaoEleitoral();
    }

    /**
     * Recupera os anos vinculados aos calendários que estão disponíveis para publicação.
     *
     * @return array
     * @throws NonUniqueResultException
     */
    public function getAnosCalendarioPublicacaoComissaoEleitoral()
    {
        return $this->calendarioRepository->getAnosCalendarioPublicacaoComissaoEleitoral();
    }

    /**
     * Retorna os calendários disponíveis para a publicação da comissão membro de acordo com o filtro informado.
     *
     * @param CalendarioPublicacaoComissaoEleitoralFiltroTO $filtro
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getCalendarioPublicacaoComissaoEleitoralPorFiltro(
        CalendarioPublicacaoComissaoEleitoralFiltroTO $filtro
    ) {
        return $this->calendarioRepository->getCalendarioPublicacaoComissaoEleitoralPorFiltro($filtro);
    }

    /**
     * Verifica a quantidade de calendarios com chapas.
     *
     * @return void
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function validarQuantidadeCalendariosComChapa()
    {
        $quantidadeCalendariosChapa = $this->calendarioRepository->getTotalCalendariosChapa();

        if ($quantidadeCalendariosChapa < 1) {
            throw new NegocioException(Message::MSG_ELEICAO_NAO_POSSUI_CHAPAS_CRIADAS);
        }
    }

    /**
     * Recupera um novo objeto de número de 'NumeroMembroTO'.
     *
     * @param Filial $filial
     * @return NumeroMembroTO
     */
    private function getNumeroMembrosTO($filial)
    {
        $prefixoConselho = $filial->getId() == 165 ? Constants::PREFIXO_CONSELHO_ELEITORAL_NACIONAL
            : Constants::PREFIXO_CONSELHO_ELEITORAL;

        $numeroMembros = NumeroMembroTO::newInstance();
        $numeroMembros->setIdCauUf($filial->getId());
        $numeroMembros->setQuantidade(0);
        $numeroMembros->setDescricao($filial->getDescricao());
        $numeroMembros->setPrefixo($prefixoConselho . '-' . $filial->getPrefixo());
        return $numeroMembros;
    }

    /**
     * Recupera a entidade 'ArquivoCalendario' por meio do 'id' informado.
     *
     * @param $id
     *
     * @return \App\Entities\ArquivoCalendario|null
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function getArquivoResolucao($id)
    {
        $arrayArquivo = $this->arquivoCalendarioRepository->getPorId($id);

        if (empty($arrayArquivo)) {
            throw new NegocioException('MSG_ARQUIVO_RESOLUCAO_NAO_ENCONTRADO');
        }

        return $arrayArquivo[0];
    }

    /**
     * Método para salvar atividade principal e secundária no momento de salvar o calendário
     *
     * @param AtividadePrincipalCalendario[] $atividades
     * @param Calendario $calendarioSalvo
     * @param bool $isCalendarioReplicado
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function salvarAtividades($atividades, Calendario $calendarioSalvo, $isCalendarioReplicado = false)
    {
        if (!empty($atividades)) {
            $isInserir = false;
            foreach ($atividades as $atividade) {

                if (empty($atividade->getId())) {
                    $isInserir = true;
                }

                $prazos = $atividade->getPrazos();
                $atividade->setPrazos(new ArrayCollection());
                $atividade->setCalendario($calendarioSalvo);
                $atividadesSecundarias = $atividade->getAtividadesSecundarias();
                $atividade->setAtividadesSecundarias(new ArrayCollection());
                $atividadeSalva = $this->atividadePrincipalBO->salvar($atividade);

                if (!empty($atividadesSecundarias)) {
                    foreach ($atividadesSecundarias as $atividadeSecundaria) {
                        $atividadeSecundaria->setAtividadePrincipalCalendario($atividadeSalva);
                        $this->atividadePrincipalBO->salvarAtividadeSecundaria($atividadeSecundaria);
                    }
                }

                if ($isCalendarioReplicado) {
                    $this->prazoCalendarioBO->salvarPrazos($prazos, $atividadeSalva, null);
                }
            }

            if (!empty($calendarioSalvo->getId()) and $isInserir) {
                $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
                $historicoCalendario = $this->historicoCalendarioBO->criarHistorico(
                    $calendarioSalvo,
                    $usuarioLogado->id,
                    Constants::DESC_ABA_PERIODO,
                    Constants::ACAO_CALENDARIO_INSERIR_ATV_PRINCIPAL
                );
                $this->historicoCalendarioBO->salvar($historicoCalendario);
            }
        }
    }

    /**
     * Verifica se os campos obrigatórios foram preenchidos.
     *
     * @param Calendario $calendario
     * @throws NegocioException
     */
    private function validarCamposObrigatorios(Calendario $calendario)
    {
        $campos = [];

        $eleicao = !empty($calendario) ? $calendario->getEleicao() : null;
        $idTipoProcesso = !empty($eleicao->getTipoProcesso()) ? $eleicao->getTipoProcesso()->getId() : null;

        if (!(Constants::TIPO_PROCESSO_EXTRAORDINARIO == $idTipoProcesso
            && $calendario->isSituacaoIES())) {
            if (empty($calendario->getCauUf())) {
                $campos[] = 'LABEL_CAU_UF';
            }
        }

        if (empty($calendario->getDataInicioVigencia())) {
            $campos[] = 'LABEL_DATA_INI_VIGENCIA';
        }

        if (empty($calendario->getDataFimVigencia())) {
            $campos[] = 'LABEL_DATA_FIM_VIGENCIA';
        }

        if (empty($calendario->getDataInicioMandato())) {
            $campos[] = 'LABEL_DATA_INI_MANDATO';
        }

        if (empty($calendario->getDataFimMandato())) {
            $campos[] = 'LABEL_DATA_FIM_MANDATO';
        }

        if (empty($calendario->getIdadeInicio()) and $calendario->getIdadeInicio() != 0) {
            $campos[] = 'LABEL_IDADE_INI';
        }

        if (empty($calendario->getIdadeFim()) and $calendario->getIdadeFim() != 0) {
            $campos[] = 'LABEL_IDADE_FIM';
        }

        if (!empty($calendario->getId()) and empty($calendario->getAtividadesPrincipais()) and !$calendario->isRascunho()) {
            $campos[] = 'LABEL_ATIVIDADES_PRINCIPAIS';
        }

        if (empty($calendario->getArquivos())) {
            $campos[] = 'LABEL_ARQUIVO';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Valida o ano informado para o Ano do calendario
     *
     * @param $ano
     * @throws NegocioException
     */
    private function validarAno($ano)
    {
        if ($ano < Constants::LIMITE_MIN_ANO or $ano > Constants::LIMITE_MAX_ANO) {
            throw new NegocioException(Message::VALIDACAO_ANO_CALENDARIO);
        }
    }

    /**
     * Valida se uma data de inicio é maior que uma data de fim
     *
     * @param Calendario $calendario
     * @throws NegocioException
     */
    private function validarCamposDatas(Calendario $calendario)
    {
        if (($calendario->getDataInicioVigencia() > $calendario->getDataFimVigencia()) or
            ($calendario->getDataInicioMandato() > $calendario->getDataFimMandato())) {
            throw new NegocioException(Message::VALIDACAO_DATA_INICIAL_FINAL);
        }

        if ($calendario->getDataInicioVigencia()->format('Y') < Constants::LIMITE_MIN_ANO or
            $calendario->getDataFimVigencia()->format('Y') > Constants::LIMITE_MAX_ANO) {
            throw new NegocioException(Message::VALIDACAO_ANO_CALENDARIO);
        }

        if ($calendario->getDataInicioMandato()->format('Y') < Constants::LIMITE_MIN_ANO or
            $calendario->getDataFimMandato()->format('Y') > Constants::LIMITE_MAX_ANO) {
            throw new NegocioException(Message::VALIDACAO_ANO_CALENDARIO);
        }
    }

    /**
     * Valida as idades informadas para o Calendario
     *
     * @param Calendario $calendario
     * @throws NegocioException
     */
    private function validarIdades(Calendario $calendario)
    {
        if ($calendario->getIdadeInicio() < Constants::LIMITE_MIN_IDADE or $calendario->getIdadeFim() < Constants::LIMITE_MIN_IDADE
            or $calendario->getIdadeInicio() > Constants::LIMITE_MAX_IDADE or $calendario->getIdadeFim() > Constants::LIMITE_MAX_IDADE) {
            throw new NegocioException(Message::VALIDACAO_IDADE_CALENDARIO);
        }
    }

    /**
     * Valida a quantidade de arquivos 'Resolução' para o Calendario
     *
     * @param Calendario $calendario
     * @throws NegocioException
     */
    private function validarQuantidadeArquivos(Calendario $calendario)
    {
        $arquivos = $calendario->getArquivos();
        if (!empty($arquivos) and count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO_RESOLUCAO) {
            throw new NegocioException(Message::MSG_QTD_ARQUIVOS_RESOLUCAO);
        }
    }

    /**
     * Valida a extensão e o tamanho do(s) arquivo(s) enviado(s)
     *
     * @param Calendario $calendario
     * @throws NegocioException
     */
    private function validarArquivos(Calendario $calendario)
    {
        if ($this->hasNovoArquivo($calendario)) {
            $arquivos = $calendario->getArquivos();

            if ($arquivos != null) {
                foreach ($arquivos as $arquivo) {
                    $this->arquivoService->validarExtensaoArquivoPDF($arquivo->getNome());
                    if (!$arquivo->getId()) {
                        $this->arquivoService->validarTamanhoArquivo($arquivo->getTamanho(),
                            Constants::TAMANHO_LIMITE_ARQUIVO,
                            Message::MSG_ANEXO_RESOLUCAO_LIMITE_TAMANHO
                        );
                    }
                }
            }
        }
    }

    /**
     * Realiza o(s) upload(s) do(s) arquivo(s) do Calendário
     *
     * @param Calendario $calendario
     */
    private function salvaArquivosDiretorio($arquivo, Calendario $calendario)
    {
        $caminho = $this->arquivoService->getCaminhoRepositorioDocumentos($calendario->getId());

        if ($arquivo != null) {
            if (!empty($arquivo->getArquivo())) {
                $this->arquivoService->salvar($caminho, $arquivo->getNomeFisico(), $arquivo->getArquivo());
            }
        }
    }

    /**
     * Realiza o(s) copia do(s) arquivo(s) do Calendário, quando houver ação de replicação.
     *
     * @param $arquivoOrigem
     * @param $arquivoDestino
     * @throws NegocioException
     */
    private function copiarArquivo($arquivoOrigem, $arquivoDestino)
    {
        $caminhoOrigem = $this->arquivoService->getCaminhoRepositorioDocumentos($arquivoOrigem->getCalendario()->getId());
        $caminhoDestino = $this->arquivoService->getCaminhoRepositorioDocumentos($arquivoDestino->getCalendario()->getId());

        $path = AppConfig::getRepositorio($caminhoDestino);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($arquivoOrigem != null && $arquivoDestino != null) {
            if (!empty($arquivoOrigem->getNomeFisico()) && !empty($arquivoDestino->getNomeFisico())) {
                $this->arquivoService->copiar($caminhoOrigem, $caminhoDestino, $arquivoOrigem->getNomeFisico(),
                    $arquivoDestino->getNomeFisico());
            }
        }
    }

    /**
     * Retorna a sequencia conforme o ano para realizar o cadastro do Calendario
     *
     * @param $ano
     * @return int
     */
    private function getSequenciaAnoCadastro($ano, $recuperaExcluido = false)
    {
        $sequencia = 1;
        $eleicoes = $this->calendarioRepository->getEleicoes($ano, $recuperaExcluido);

        if (!empty($eleicoes)) {
            $sequencia = $eleicoes[0]->getSequenciaAno() + 1;
        }
        return $sequencia;
    }

    /**
     * Verifica se o arquivo anexo deverá ser considerando os seguintes critérios:
     * - Caso o 'Calendario' seja novo.
     * - Caso o 'Calendario' seja a copia do vigênte e possua um novo arquivo.
     *
     * @param Calendario $calendario
     *
     * @return boolean
     */
    private function hasNovoArquivo(Calendario $calendario)
    {
        return empty($calendario->getId()) || (!empty($calendario->getId()) && !empty($calendario->getArquivos()));
    }

    /**
     * Cria os nomes de arquivo
     *
     * @param Calendario $calendario
     * @return Calendario
     */
    private function setNomeArquivoFisico(Calendario $calendario)
    {
        if ($calendario->getArquivos() != null) {
            foreach ($calendario->getArquivos() as $arquivo) {
                if (empty($arquivo->getId())) {
                    $nomeArquivoFisico = $this->arquivoService->getNomeArquivoFormatado(
                        $arquivo->getNome(),
                        Constants::REFIXO_DOC_RESOLUCAO
                    );
                    $arquivo->setNomeFisico($nomeArquivoFisico);
                }
            }
        }
        return $calendario;
    }

    /**
     * Cria a Situação Atual do Calendário para o Cadastro ou Alteração
     *
     * @param Calendario $calendario
     * @return CalendarioSituacao
     * @throws Exception
     */
    private function criarSituacaoAtual(Calendario $calendario)
    {
        $idVigente = Constants::SITUACAO_CALENDARIO_EM_PREENCHIMENTO;
        if (!empty($calendario->getIdSituacaoVigente())) {
            $idVigente = $calendario->getIdSituacaoVigente();
        }
        $situacaoCalendario = $this->situacaoCalendarioRepository->find($idVigente);

        $calendarioSituacao = CalendarioSituacao::newInstance();
        $calendarioSituacao->setData(Utils::getData());
        $calendarioSituacao->setCalendario($calendario);
        $calendarioSituacao->setSituacaoCalendario($situacaoCalendario);

        return $calendarioSituacao;
    }

    /**
     * Método auxiliar para remover as dependencias do calendário, incluindo:
     * Atividades Principais, Secundárias e Arquivos Resolução
     *
     * @param $dadosTO
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    private function excluirDependenciasCalendario($dadosTO)
    {
        if (!empty($dadosTO->subAtividadesPrincipaisExcluidas)) {
            foreach ($dadosTO->subAtividadesPrincipaisExcluidas as $subAtividade) {
                $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance($subAtividade);
                if (!empty($atividadeSecundaria->getId())) {
                    $this->atividadePrincipalBO->excluirAtividadeSecundaria($atividadeSecundaria->getId());
                }
            }
        }

        if (!empty($dadosTO->atividadesPrincipaisExcluidas)) {
            foreach ($dadosTO->atividadesPrincipaisExcluidas as $atividade) {
                $atividadePrincipal = AtividadePrincipalCalendario::newInstance($atividade);
                if (!empty($atividadePrincipal->getId())) {
                    $this->atividadePrincipalBO->excluirPrazosCalendarioPorAtividadePrincipal($atividadePrincipal->getId());
                    $this->atividadePrincipalBO->excluirAtividadePrincipal($atividadePrincipal->getId());
                }
            }
        }

        if (!empty($dadosTO->arquivosExcluidos)) {
            foreach ($dadosTO->arquivosExcluidos as $arquivo) {
                $arquivoTmp = ArquivoCalendario::newInstance($arquivo);
                if (!empty($arquivoTmp->getId())) {
                    $arquivoExcluido = $this->arquivoCalendarioRepository->find($arquivoTmp->getId());
                    $caminho = $this->arquivoService->getCaminhoRepositorioDocumentos($arquivoExcluido->getCalendario()->getId());
                    $this->arquivoCalendarioRepository->delete($arquivoExcluido);
                    $this->arquivoService->excluir($caminho, $arquivoExcluido->getNomeFisico());
                }
            }
        }
    }

    /**
     * Método auxiliar para remover todos os CAU UF associado a um calendário
     *
     * @param Calendario $calendarioSalvo
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function excluirUfsCalendario(Calendario $calendarioSalvo)
    {
        $ufsCalendarios = $this->ufCalendarioRepository->findBy(['calendario' => $calendarioSalvo->getId()]);

        if (!empty($ufsCalendarios)) {
            foreach ($ufsCalendarios as $uf) {
                $this->ufCalendarioRepository->delete($uf);
            }
        }
    }

    /**
     * Método auxiliar para definir o progresso e situação vigente do calendário
     *
     * @param Calendario $calendario
     * @return Calendario
     * @throws NonUniqueResultException
     */
    private function definirProgressoSituacao(Calendario $calendario)
    {
        $calendario->definirProgresso();
        $hasPrazosVinculadosAoCalendario = $this->prazoCalendarioBO->hasPrazosVinculadosAoCalendario($calendario->getId());
        $calendario->setPrazosDefinidos($hasPrazosVinculadosAoCalendario);
        $calendario->setSituacaoVigente();

        return $calendario;
    }

    /**
     * Método auxiliar para salvar os dados de CAU UF para o calendário
     *
     * @param $cauUfs
     * @param Calendario $calendarioSalvo
     * @return Calendario
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function salvarUfsCalendario($cauUfs, Calendario $calendarioSalvo)
    {
        $cauUfsSalvas = new ArrayCollection();

        if (!empty($cauUfs)) {
            foreach ($cauUfs as $cauUf) {
                $cauUf->setId(null);
                $cauUf->setCalendario($calendarioSalvo);
                $cauUfSalvo = clone $this->ufCalendarioRepository->persist($cauUf);
                $cauUfSalvo->setCalendario(null);
                $cauUfsSalvas->add($cauUfSalvo);
            }
        }

        $calendarioSalvo->setCauUf($cauUfsSalvas);
        return $calendarioSalvo;
    }

    /**
     * Método auxiliar para Salvar dados do Arquivo Resolução para o calendário
     *
     * @param $arquivosResolucao
     * @param Calendario $calendarioSalvo
     * @param Calendario $calendario
     * @param $isInclusao
     * @return Calendario
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NegocioException
     */
    private function salvarArquivosCalendario(
        $arquivosResolucao,
        Calendario $calendarioSalvo,
        Calendario $calendario,
        $isInclusao
    ) {
        $arquivosSalvos = new ArrayCollection();

        if (!empty($arquivosResolucao)) {
            foreach ($arquivosResolucao as $arquivoResolucao) {
                if (!empty($arquivoResolucao->getId()) && $isInclusao) { //Caso de replicação
                    $arquivoRecuperado = $this->arquivoCalendarioRepository->getPorId($arquivoResolucao->getId())[0];

                    $arquivoResolucao->setId(null);
                    $arquivoResolucao->setCalendario($calendarioSalvo);
                    $arquivoSalvo = $this->arquivoCalendarioRepository->persist($arquivoResolucao);
                    $arquivosSalvos->add($arquivoSalvo);
                    $this->copiarArquivo($arquivoRecuperado, $arquivoResolucao);
                    $arquivoSalvo->setCalendario(null);
                    $arquivoSalvo->setArquivo(null);
                } else {
                    $arquivoResolucao->setCalendario($calendarioSalvo);
                    $arquivoSalvo = $this->arquivoCalendarioRepository->persist($arquivoResolucao);
                    $arquivoSalvo->setCalendario(null);
                    $arquivosSalvos->add($arquivoSalvo);
                    $this->salvaArquivosDiretorio($arquivoResolucao, $calendarioSalvo);
                    $arquivoSalvo->setArquivo(null);
                }
            }
        }

        $calendarioSalvo->setArquivos($arquivosSalvos);
        $calendarioSalvo->removerFiles();

        return $calendarioSalvo;
    }

    /**
     * Retorna uma nova instância de 'FilialBO'.
     *
     * @return FilialBO|mixed
     */
    private function getFilialBO()
    {
        if (empty($this->filialBO)) {
            $this->filialBO = app()->make(FilialBO::class);
        }

        return $this->filialBO;
    }


    /**
     * Verifica se existe uma eleiçao ja cadastrada para o periodo
     *
     * @param Calendario $calendario
     * @throws NegocioException
     */
    private function validarEleicaoExistente(Calendario $calendario)
    {
        if($calendario->getEleicao()->getTipoProcesso()->getId() == Constants::TIPO_PROCESSO_ORDINARIO){
            $this->validarEleicaoOrdinaria($calendario);
            $this->validarEleicaoExtraordinaria($calendario);
        }
        else {
            $this->validarEleicaoExtraordinaria($calendario);
            $this->validarEleicaoOrdinaria($calendario);
        }
    }

    /**
     * Verifica se existe uma eleiçao ordinariaja cadastrada para o periodo
     *
     * @param Calendario $calendario
     * @throws NegocioException
     */
    private function validarEleicaoOrdinaria(Calendario $calendario)
    {
        $eleicaoOrdinaria = $this->calendarioRepository->getPorTipoProcessoPorSituacaoAndDatasVigencia($calendario);
        if ($eleicaoOrdinaria) {
            throw new NegocioException(Lang::get('messages.calendario.processo_ordinario_ja_cadastrado'));
        }
    }

    /**
     * Verifica se existe uma eleiçao ordinariaja cadastrada para o periodo
     *
     * @param Calendario $calendario
     * @throws NegocioException
     */
    private function validarEleicaoExtraordinaria(Calendario $calendario)
    {
        /** @var  $eleicaoExtraordinaria Calendario */
        $eleicaoExtraordinaria = $this->calendarioRepository->getPorTipoProcessoPorSituacaoAndDatasVigencia($calendario, Constants::TIPO_PROCESSO_EXTRAORDINARIO);

        if ($eleicaoExtraordinaria) {
            foreach ($eleicaoExtraordinaria[0]->getCauUf() as $ufCalendario) {
                foreach ($calendario->getCauUf() as $ufCalendarioNovo) {
                    if ($ufCalendario->getIdCauUf() == $ufCalendarioNovo->getIdCauUf()) {
                        throw new NegocioException(Lang::get('messages.calendario.processo_extraordinario_ja_cadastrado'));
                    }
                }
            }
        }
    }
}
