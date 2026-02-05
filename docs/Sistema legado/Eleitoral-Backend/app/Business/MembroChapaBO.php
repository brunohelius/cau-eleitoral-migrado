<?php
/*
 * MembroChapaBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\AppConfig;
use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ChapaEleicao;
use App\Entities\DocumentoComprobatorioSinteseCurriculo;
use App\Entities\MembroChapa;
use App\Entities\MembroChapaPendencia;
use App\Entities\MembroSubstituicaoJulgamentoFinal;
use App\Entities\PedidoImpugnacao;
use App\Entities\PedidoSubstituicaoChapa;
use App\Entities\Profissional;
use App\Entities\SituacaoMembroChapa;
use App\Entities\StatusParticipacaoChapa;
use App\Entities\StatusValidacaoMembroChapa;
use App\Entities\TipoMembroChapa;
use App\Entities\TipoParticipacaoChapa;
use App\Entities\TipoPendencia;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailConviteMembroChapaJob;
use App\Jobs\EnviarEmailMembroIncluidoChapaJob;
use App\Jobs\EnviarEmailPendenciasMembroChapaJob;
use App\Mail\AtividadeSecundariaMail;
use App\Models\ArquivoRepresentatividade;
use App\Models\Conselheiro;
use App\Models\HistoricoChapaEleicao;
use App\Models\MembrosChapa;
use App\Models\RespostaDeclaracaoRepresentatividade;
use App\Repository\AtividadeSecundariaCalendarioRepository;
use App\Repository\ChapaEleicaoRepository;
use App\Repository\DocumentoComprobatorioSinteseCurriculoRepository;
use App\Repository\MembroChapaPendenciaRepository;
use App\Repository\MembroChapaRepository;
use App\Repository\PedidoSubstituicaoChapaRepository;
use App\Repository\StatusParticipacaoChapaRepository;
use App\Repository\StatusValidacaoMembroChapaRepository;
use App\Repository\TipoMembroChapaRepository;
use App\Repository\TipoParticipacaoChapaRepository;
use App\Repository\TipoPendenciaRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoTO;
use App\To\AtividadePrincipalCalendarioTO;
use App\To\AtividadeSecundariaCalendarioTO;
use App\To\ConviteMembroChapaTO;
use App\To\ConviteStatusFiltroTO;
use App\To\DeclaracaoTO;
use App\To\EleicaoTO;
use App\To\EnvioEmailMembroIncuidoChapaTO;
use App\To\MembroChapaFiltroTO;
use App\To\MembroChapaSubstituicaoTO;
use App\To\MembroChapaTO;
use App\To\ProfissionalTO;
use App\To\StatusMembroChapaTO;
use App\Util\Email;
use App\Util\ImageUtils;
use App\Util\RestClient;
use App\Util\Utils;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use finfo;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenApi\Util;
use stdClass;
use function trim;
use Mpdf\Mpdf;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'MembroChapa'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class MembroChapaBO extends AbstractBO
{
    private const NOME_ARQUIVO_FOTO = 'foto';

    private const NOME_ARQUIVO_CARTA_INDICACAO = 'carta_indicacao';

    private const NOME_ARQUIVO_COMPROVANTE_VINCULO = 'comprovante_vinculo';

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var AtividadeSecundariaCalendarioRepository
     */
    private $atividadeSecundariaCalendarioRepository;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaBO;

    /**
     * @var DeclaracaoAtividadeBO
     */
    private $declaracaoAtividadeBO;

    /**
     * @var MembroChapaPendenciaBO
     */
    private $membroChapaPendenciaBO;

    /**
     * @var HistoricoChapaEleicaoBO
     */
    private $historicoChapaEleicaoBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var DocumentoComprobatorioSinteseCurriculoBO
     */
    private $documentoComprobatorioSinteseCurriculoBO;

    /**
     * @var PedidoImpugnacaoBO
     */
    private $pedidoImpugnacaoBO;

    /**
     * @var MembroChapaRepository
     */
    private $membroChapaRepository;

    /**
     * @var MembroChapaPendenciaRepository
     */
    private $membroChapaPendenciaRepository;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var ChapaEleicaoRepository
     */
    private $chapaEleicaoRepository;

    /**
     * @var DocumentoComprobatorioSinteseCurriculoRepository
     */
    private $documentoComprobatorioSinteseCurriculoRepository;

    /**
     * @var TipoMembroChapaRepository
     */
    private $tipoMembroChapaRepository;

    /**
     * @var TipoPendenciaRepository
     */
    private $tipoPendenciaRepository;

    /**
     * @var TipoParticipacaoChapaRepository
     */
    private $tipoParticipacaoChapaRepository;

    /**
     * @var StatusParticipacaoChapaRepository
     */
    private $statusParticipacaoChapaRepository;

    /**
     * @var StatusValidacaoMembroChapaRepository
     */
    private $statusValidacaoMembroChapaRepository;

    /**
     * @var PedidoSubstituicaoChapaRepository
     */
    private $pedidoSubstituicaoChapaRepository;

    /**
     * @var SubstituicaoJulgamentoFinalBO
     */
    private $substituicaoJulgamentoFinalBO;

    /**
     * @var RespostaDeclaracaoRepresentatividadeBO
     */
    private $respostaDeclaracaoRepresentatividadeBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->membroChapaRepository = $this->getRepository(MembroChapa::class);
    }

    /**
     * Retorna os membros responsáveis de uma chapa conforme o id da chapa informada.
     *
     * @param $idChapaEleicao
     * @param int|null $idStatusParticipacaoChapa
     *
     * @return array|null
     */
    public function getMembrosResponsaveisPorCalendario($idCalendario, int $idStatusParticipacaoChapa = null)
    {
        $membroChapaFiltroTO = MembroChapaFiltroTO::newInstance([
            'idCalendario' => $idCalendario,
            'situacaoResponsavel' => true,
            'idStatusParticipacaoChapa' => $idStatusParticipacaoChapa
        ]);

        return $this->membroChapaRepository->getMembrosPorFiltro($membroChapaFiltroTO);
    }

    /**
     * @param $idCalendario
     * @param $idTipoCandidatura
     * @param null $idCauUf
     * @return array|null
     */
    public function getMembrosResponsaveisPorCalendarioAndTipoCandidaturaAndCauUF($idCalendario, $idTipoCandidatura, $idCauUf = null)
    {
        return $this->membroChapaRepository->getMembrosResponsaveisPorCalendarioAndTipoCandidaturaAndCauUF($idCalendario, $idTipoCandidatura, $idCauUf);
    }

    /**
     * Retorna todos os convites para chapa que o usuário informado recebeu.
     *
     * @return array
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getConvitesUsuario(): array
    {
        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_menu_profissional'));
        }

        $eleicoes = $this->getEleicaoBO()->getEleicoesVigenteComCalendario(true);
        if (empty($eleicoes)) {
            throw new NegocioException(Message::MSG_PERIODO_CONVITE_CHAPA_NOT_VIGENTE);
        }

        $isPeriodoVigente = false;
        $atividadesChapa = [];
        $convitesUsuario = $this->getTodosConvitesMembroChapa($eleicoes, $atividadesChapa, $isPeriodoVigente);

        if (!$isPeriodoVigente) {
            throw new NegocioException(Message::MSG_PERIODO_CONVITE_CHAPA_NOT_VIGENTE);
        }

        if (!empty($convitesUsuario)) {
            $convitesUsuario = array_map(function ($conviteUsuario) {

                $nomeResponsavelChapa = $this->membroChapaRepository->getPrimeiroResponsavelPorChapa($conviteUsuario['idChapaEleicao']);

                $conviteUsuario['nomeResponsavelChapa'] = trim($nomeResponsavelChapa);

                $conviteUsuario['idAtividadeSecundariaConvite'] = $this->getAtividadeSecundariaCalendarioRepository()
                    ->getIdAtividadeSecundariaPorAtividadePrincipalENivel($conviteUsuario['idAtividadePrincipal'], 2);

                return $conviteUsuario;
            }, $convitesUsuario);
        } else {
            $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
            if (!empty($atividadesChapa)) {
                foreach ($atividadesChapa as $atividadeChapa) {
                    $this->verificarSeJaConfirmouParticipacaoChapa($usuarioLogado->idProfissional, $atividadeChapa->getId());
                }
            }
        }

        return $this->getListaConviteChapasTO($convitesUsuario);
    }

    /**
     * Retorna uma nova instância de 'EleicaoBO'.
     *
     * @return EleicaoBO|mixed
     */
    private function getEleicaoBO()
    {
        if (empty($this->eleicaoBO)) {
            $this->eleicaoBO = app()->make(EleicaoBO::class);
        }

        return $this->eleicaoBO;
    }

    /**
     * @param $eleicoes
     * @param $atividadesChapa
     * @param $isPeriodoVigente
     * @return array
     */
    private function getTodosConvitesMembroChapa(
        $eleicoes,
        &$atividadesChapa,
        &$isPeriodoVigente
    ): array
    {
        $convitesUsuario = [];

        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        foreach ($eleicoes as $eleicao) {

            $isPossuiConviteSubstituicaoJulgFinal = false;

            /** @var AtividadePrincipalCalendarioTO $ativPrincipalTO */
            foreach ($eleicao->getCalendario()->getAtividadesPrincipais() as $ativPrincipalTO) {

                /** @var AtividadeSecundariaCalendarioTO $ativSecundariaTO */
                foreach ($ativPrincipalTO->getAtividadesSecundarias() as $ativSecundariaTO) {
                    $convites = null;
                    $dataFim = Utils::getDataHoraZero($ativSecundariaTO->getDataFim());
                    $dataInicio = Utils::getDataHoraZero($ativSecundariaTO->getDataInicio());
                    $isAtividadeVigente = Utils::getDataHoraZero() >= $dataInicio && Utils::getDataHoraZero() <= $dataFim;

                    if ($ativPrincipalTO->getNivel() == 2 && $ativSecundariaTO->getNivel() == 1 && $isAtividadeVigente) {
                        $atividadesChapa[] = $ativSecundariaTO;
                    }

                    if ($ativPrincipalTO->getNivel() == 2 && $ativSecundariaTO->getNivel() == 2 && $isAtividadeVigente) {
                        $isPeriodoVigente = true;
                        $convites = $this->membroChapaRepository->getConvitesUsuario(
                            $usuarioLogado->idProfissional, $eleicao->getCalendario()->getId()
                        );
                    }

                    if ($ativPrincipalTO->getNivel() == 2 && $ativSecundariaTO->getNivel() == 7 && $isAtividadeVigente) {
                        $isPeriodoVigente = true;
                        $convites = $this->membroChapaRepository->getConvitesUsuarioPedidoSubstituicao(
                            $usuarioLogado->idProfissional,
                            $eleicao->getCalendario()->getId(),
                            Constants::STATUS_SUBSTITUICAO_CHAPA_DEFERIDO
                        );
                    }

                    if ($ativPrincipalTO->getNivel() == 2 && $ativSecundariaTO->getNivel() == 8 && $isAtividadeVigente) {
                        $isPeriodoVigente = true;
                        $convites = $this->membroChapaRepository->getConvitesUsuarioPedidoSubstituicao(
                            $usuarioLogado->idProfissional,
                            $eleicao->getCalendario()->getId(),
                            Constants::STATUS_SUBSTITUICAO_CHAPA_RECURSO_DEFERIDO
                        );
                    }

                    if ($ativPrincipalTO->getNivel() == 3 && $ativSecundariaTO->getNivel() == 8 && $isAtividadeVigente) {
                        $isPeriodoVigente = true;
                        $convites = $this->membroChapaRepository->getConvitesUsuarioSubstituicaoImpugnacao(
                            $usuarioLogado->idProfissional, $eleicao->getCalendario()->getId()
                        );
                    }

                    if (!$isPossuiConviteSubstituicaoJulgFinal &&
                        $ativPrincipalTO->getNivel() == 5 && in_array
                    ($ativSecundariaTO->getNivel(), [3, 6]) && $isAtividadeVigente) {
                        $isPossuiConviteSubstituicaoJulgFinal = true;
                        $isPeriodoVigente = true;
                        $convites = $this->membroChapaRepository->getConvitesUsuarioSubstituicaoJulgFinal(
                            $usuarioLogado->idProfissional, $eleicao->getCalendario()->getId()
                        );
                    }

                    if (!empty($convites)) {
                        $convitesUsuario = array_merge($convitesUsuario, $convites ?? []);
                    }
                }
            }
        }

        return $convitesUsuario;
    }

    /**
     * Retorna uma nova instância de 'AtividadeSecundariaCalendarioRepository'.
     *
     * @return AtividadeSecundariaCalendarioRepository
     */
    private function getAtividadeSecundariaCalendarioRepository()
    {
        if (empty($this->atividadeSecundariaCalendarioRepository)) {
            $this->atividadeSecundariaCalendarioRepository = $this->getRepository(
                AtividadeSecundariaCalendario::class
            );
        }

        return $this->atividadeSecundariaCalendarioRepository;
    }

    /**
     * @param int $idProfissional
     * @param int $idAtividadeSecundaria
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function verificarSeJaConfirmouParticipacaoChapa($idProfissional, $idAtividadeSecundaria): void
    {
        $totalConvitesConfirmados = $this->totalConvitePorStatusParticipacao(
            $idProfissional,
            $idAtividadeSecundaria,
            Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
        );

        if (!empty($totalConvitesConfirmados) && $totalConvitesConfirmados > 0) {
            throw new NegocioException(Lang::get('messages.membro_chapa.ja_aceitou_convite'));
        }
    }

    /**
     * Retorna total de convite para participar de uma chapa pelo status de participacao
     *
     * @param integer $idProfissional
     * @param integer $idAtividadeSecundaria
     * @param integer $idStatusParticipacao
     *
     * @return integer
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function totalConvitePorStatusParticipacao($idProfissional, $idAtividadeSecundaria, $idStatusParticipacao)
    {
        return $this->membroChapaRepository->totalConvitePorStatusParticipacao(
            $idProfissional,
            $idAtividadeSecundaria,
            $idStatusParticipacao
        );
    }

    /**
     * Retorna os convites para chapas em formato de ConviteMembroChapaTO.
     *
     * @param array $convitesUsuario
     *
     * @return array
     */
    private function getListaConviteChapasTO(array $convitesUsuario): array
    {
        return array_map(static function ($conviteUsuario) {
            return ConviteMembroChapaTO::newInstance($conviteUsuario);
        }, $convitesUsuario);
    }

    /**
     * Aceita o convite para se tornar membro de uma chapa e rejeita os outros convites existentes
     * através do id chapa eleicao e id do usuario logado.
     *
     * @param ConviteStatusFiltroTO $conviteStatusFiltroTO
     *
     * @return void
     * @throws NegocioException
     * @throws Exception
     */
    public function aceitarConvite(ConviteStatusFiltroTO $conviteStatusFiltroTO, $representatividades)
    {
        $membrosChapa = $this->getMembrosChapaParaAceiteConvite();

        $chapaEleicao = $this->getChapaEleicaoRepository()->getPorId($conviteStatusFiltroTO->getIdChapaEleicao());

        if (!empty($membrosChapa)) {
            try {
                $this->beginTransaction();

                $membrosConvites = $this->preAceiteConvite($membrosChapa, $conviteStatusFiltroTO);

                $this->membroChapaRepository->persistEmLote($membrosConvites);

                if (!empty($representatividades)) {
                    $this->salvarRespostaDeclaracaoRepresentatividade($representatividades, $membrosChapa[0]->getId());
                }

                /*if (count($arquivos)) {
                    $this->uparArquivos($arquivos, $membrosChapa[0]->getId());
                }*/

                $this->getChapaEleicaoBO()->concluirChapa($chapaEleicao, Constants::TP_ALTERACAO_AUTOMATICO);

                $this->commitTransaction();

            } catch (Exception $e) {
                $this->rollbackTransaction();
                throw $e;
            }
            Utils::executarJOB(new EnviarEmailConviteMembroChapaJob(
                $chapaEleicao->getId(), Constants::EMAIL_MEMBRO_CHAPA_ACEITOU_CONVITE
            ));
        }
    }

    /**
     * Salva resposta de Declaração de Representatividade
     * 
     */
    public function salvarRespostaDeclaracaoRepresentatividade(Array $representatividades, $idMembro)
    {
        foreach ($representatividades as $representatividade) {
            RespostaDeclaracaoRepresentatividade::create([
                'id_membro_chapa' => $idMembro,
                'id_item_declaracao' => $representatividade
            ]);
        }
    }

     /**
     * Upar documentos declaração de representatividade
     * 
     */
    public function uparArquivos(Array $arquivos, $idMembro)
    {
        $ArrayArquivos = [];
        foreach ($arquivos as $arquivo) {
            $dadosArquivos = [
                'nome' => 'arquivo.pdf',
                'diretorio' =>  Constants::PATH_STORAGE_ARQUIVO_REPRESENTATIVIDADE,
                'arquivo' => $arquivo,
                'nmFisico' => ''
            ];
            array_push($ArrayArquivos, $dadosArquivos);
        }
       
        $params = [
            'arquivos' => $ArrayArquivos
        ];
      
        $arquivos = json_decode($this->getRestClient(true)->sendPost(AppConfig::getUrlAcesso('api/file/upload'), json_encode($params), true));
        foreach ($arquivos as $arquivo) {
            ArquivoRepresentatividade::create([
                'id_membro_chapa' => $idMembro,
                'nm_fis_arquivo' => $arquivo->nmFisico
            ]);
        }
    }

    /**
     * Retorna uma instância de 'RestClient'.
     *
     * @param null $authorization
     * @return RestClient
     */
    protected function getRestClient($authorization = null): RestClient
    {
        $headers = ["Content-Type: application/json"];

        if ($authorization) {
            $headers[] = sprintf("Authorization: %s %s", Constants::PARAM_BEARER, Input::bearerToken());
        }

        return RestClient::newInstance()->addHeaders($headers);
    }

    /**
     * Altera dados do membro/usuario logado.
     *
     * @param ConviteStatusFiltroTO $conviteStatusFiltroTO
     *
     * @return void
     * @throws NegocioException
     * @throws Exception
     */
    public function alterarDadosCurriculo(ConviteStatusFiltroTO $conviteStatusFiltroTO)
    {
        if (empty($conviteStatusFiltroTO->getIdMembroChapa())) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        /** @var MembroChapa $membro */
        $membro = $this->membroChapaRepository->find($conviteStatusFiltroTO->getIdMembroChapa());

        if (empty($membro)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $idProfissionalLogado = $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional;
        if($idProfissionalLogado != $membro->getProfissional()->getId()) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if (empty($conviteStatusFiltroTO->getSinteseCurriculo())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $this->validarPeriodoCadastroPorIdChapa($membro->getChapaEleicao()->getId());

        try {
            $this->beginTransaction();

            $membro->setSinteseCurriculo($conviteStatusFiltroTO->getSinteseCurriculo());

            if (!empty($conviteStatusFiltroTO->getFotoSinteseCurriculo())) {
                $this->getArquivoService()->validarFotoSinteseCurriculo(
                    $conviteStatusFiltroTO->getFotoSinteseCurriculo()
                );

                $arquivosSinteseCurriculo = $this->salvarArquivosSinteseCurriculo($membro,
                    $conviteStatusFiltroTO->getFotoSinteseCurriculo());

                $membro->setNomeArquivoFoto($arquivosSinteseCurriculo['nomeFoto']);
            }

            $this->membroChapaRepository->persist($membro);

            $this->commitTransaction();

        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param $idChapa
     */
    private function validarPeriodoCadastroPorIdChapa($idChapa)
    {
        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao($idChapa, 2, 1);

        $dataFim = Utils::getDataHoraZero($atividade->getDataFim());
        $dataInicio = Utils::getDataHoraZero($atividade->getDataInicio());
        if (!(Utils::getDataHoraZero() >= $dataInicio && Utils::getDataHoraZero() <= $dataFim)) {
            throw new NegocioException(Message::MSG_SEM_PERIODO_VIGENTE_INCLUIR_CHAPA);
        }
    }

    /**
     * @return array|null
     * @throws NegocioException
     */
    private function getMembrosChapaParaAceiteConvite()
    {
        /** @var EleicaoTO $eleicao */
        $eleicoes = $this->getEleicaoBO()->getEleicoesVigenteComCalendario(true);
        $isPeriodoVigente = false;
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $membrosConvite = [];

        foreach ($eleicoes as $eleicao) {
            $isEleicaoVigente = false;
            /** @var AtividadePrincipalCalendarioTO $ativPrincipalTO */
            foreach ($eleicao->getCalendario()->getAtividadesPrincipais() as $ativPrincipalTO) {

                /** @var AtividadeSecundariaCalendarioTO $ativSecundariaTO */
                foreach ($ativPrincipalTO->getAtividadesSecundarias() as $ativSecundariaTO) {
                    $dataFim = Utils::getDataHoraZero($ativSecundariaTO->getDataFim());
                    $dataInicio = Utils::getDataHoraZero($ativSecundariaTO->getDataInicio());
                    $isAtividadeVigente = Utils::getDataHoraZero() >= $dataInicio && Utils::getDataHoraZero() <= $dataFim;

                    if (
                        $isAtividadeVigente &&
                        (
                            ($ativPrincipalTO->getNivel() == 2 && in_array($ativSecundariaTO->getNivel(), [2,7,8])) ||
                            ($ativPrincipalTO->getNivel() == 3 && $ativSecundariaTO->getNivel() == 8) ||
                            ($ativPrincipalTO->getNivel() == 5 && in_array($ativSecundariaTO->getNivel(), [3, 6]))
                        )
                    ) {
                        $isEleicaoVigente = true;
                        break;
                    }
                }

                if ($isEleicaoVigente) {
                    break;
                }
            }
            $isPeriodoVigente = ($isPeriodoVigente) ? $isPeriodoVigente : $isEleicaoVigente;

            if ($isEleicaoVigente) {
                $membrosChapa = $this->membroChapaRepository->getMembrosChapaAConfirmarPorProfissional(
                    $eleicao->getCalendario()->getId(),
                    $usuarioLogado->idProfissional,
                    [Constants::ST_MEMBRO_CHAPA_CADASTRADO, Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO]
                );
                $membrosConvite = array_merge($membrosConvite, $membrosChapa ?? []);
            }
        }

        if (!$isPeriodoVigente) {
            throw new NegocioException(Message::MSG_PERIODO_CONVITE_CHAPA_NOT_VIGENTE);
        }

        return $membrosConvite;
    }

    /**
     * Retorna a instância do 'ChapaEleicaoRepository'.
     *
     * @return ChapaEleicaoRepository
     */
    private function getChapaEleicaoRepository()
    {
        if ($this->chapaEleicaoRepository == null) {
            $this->chapaEleicaoRepository = $this->getRepository(ChapaEleicao::class);
        }
        return $this->chapaEleicaoRepository;
    }

    /**
     * Método auxiliar para salvar as informações adicionais do aceite do convite e preparar a atualização
     * nas membros chapa
     *
     * @param array $membrosChapa
     * @param ConviteStatusFiltroTO $conviteStatusFiltroTO
     * @return array
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function preAceiteConvite(array $membrosChapa, ConviteStatusFiltroTO $conviteStatusFiltroTO)
    {
        $membrosParaAlteracao = [];

        /** @var MembroChapa $membroChapa */
        foreach ($membrosChapa as $membroChapa) {

            $idStatusParticChapa = Constants::SITUACAO_MEMBRO_REJEITADO;

            $idSituacaoAtualMembro = $membroChapa->getSituacaoMembroChapa()->getId();

            if ($membroChapa->getId() === $conviteStatusFiltroTO->getIdMembroChapa()) {
                $idStatusParticChapa = Constants::SITUACAO_MEMBRO_CONFIRMADO;

                $this->salvarInformacoesAceiteConvite($membroChapa, $conviteStatusFiltroTO);

                if ($idSituacaoAtualMembro == Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO) {

                    //HST37 e 45
                    //Verificar se veio de um pedido de substituiçao
                    $idPedido = $this->getPedidoSubstituicaoChapaRepository()
                        ->getPedidoPorMembroSubstituto($conviteStatusFiltroTO->getIdMembroChapa());
                    if (!empty($idPedido)) {
                        /** @var PedidoSubstituicaoChapa $pedido */
                        $pedido = $this->getPedidoSubstituicaoChapaRepository()->find($idPedido);
                        if (!empty($pedido)) {
                            $this->aceitaConviteSubstituicao($pedido, $membrosParaAlteracao, $conviteStatusFiltroTO->getIdMembroChapa());
                            continue;
                        }
                    }

                    //HST100
                    //Verificar se veio de pedido de substituição de julgamento final 1ª ou 2ª instância
                    $membroSubstituicaoJulgamentoFinal = $membroChapa->getMembroSubstituicaoJulgamentoFinal();
                    if (!empty($membroSubstituicaoJulgamentoFinal)) {
                        $this->aceitaConviteSubstituicaoFinal($membroSubstituicaoJulgamentoFinal, $membrosParaAlteracao);
                        continue;
                    }

                    //HST48 e 55
                    //Verificar se veio de pedido de impugnacao 1ª ou 2ª instância
                    $substituicaoImpugnacao = $membroChapa->getSubstituicaoImpugnacao();
                    if (!empty($substituicaoImpugnacao)) {
                        $pedidoImpugnacao = $substituicaoImpugnacao->getPedidoImpugnacao();
                        $this->aceitaConviteSubstituicaoImpugnacao($membroChapa, $pedidoImpugnacao, $membrosParaAlteracao);
                        continue;
                    }
                }
            }

            if ($idSituacaoAtualMembro == Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO) {
                $membroChapa->setSituacaoMembroChapa(SituacaoMembroChapa::newInstanceById(Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_INDEFERIDO));
            }

            $membroChapa->setStatusParticipacaoChapa(StatusParticipacaoChapa::newInstanceById($idStatusParticChapa));
            $membrosParaAlteracao[] = $membroChapa;
        }
        return $membrosParaAlteracao;
    }

    /**
     * Método auxiliar para salvar as informações do aceite do convite
     *
     * @param $membroChapa
     * @param $conviteStatusFiltroTO
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function salvarInformacoesAceiteConvite($membroChapa, $conviteStatusFiltroTO)
    {
        $this->getArquivoService()->validarFotoSinteseCurriculo($conviteStatusFiltroTO->getFotoSinteseCurriculo());

        $documentos = Constants::TIPO_CANDIDATURA_IES === $membroChapa->getChapaEleicao()->getTipoCandidatura()->getId()
            ? $this->getDocumentosComprobatoriosSinteseCurriculoCollection($membroChapa, $conviteStatusFiltroTO)
            : null;

        $arquivosSinteseCurriculo = $this->salvarArquivosSinteseCurriculo(
            $membroChapa, $conviteStatusFiltroTO->getFotoSinteseCurriculo(), $documentos
        );

        if (null !== $documentos) {
            $membroChapa->setDocumentosComprobatoriosSinteseCurriculo($arquivosSinteseCurriculo['documentos']);

            $this->getDocumentoComprobatorioSinteseCurriculoRepository()->persistEmLote(
                $arquivosSinteseCurriculo['documentos']
            );
        }

        $membroChapa->setNomeArquivoFoto($arquivosSinteseCurriculo['nomeFoto']);
        $membroChapa->setSinteseCurriculo($conviteStatusFiltroTO->getSinteseCurriculo());

        $this->salvarRespostaDeclaracaoMembro($membroChapa, $conviteStatusFiltroTO->getDeclaracoes());

        $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
            $membroChapa->getChapaEleicao(),
            Constants::ORIGEM_PROFISSIONAL,
            Constants::HISTORICO_CONFIRMACAO_PARTICIPACAO_CHAPA
        );
        $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);
    }

    /**
     * Retorna a instância do 'ArquivoService'.
     *
     * @return ArquivoService
     */
    private function getArquivoService()
    {
        if ($this->arquivoService == null) {
            $this->arquivoService = app()->make(ArquivoService::class);
        }
        return $this->arquivoService;
    }

    /**
     * Retorna os documentos comprobatórios em formato de entidade 'DocumentoComprobatorioSinteseCurriculo'.
     *
     * @param MembroChapa $membroChapa
     * @param ConviteStatusFiltroTO $conviteStatusFiltroTO
     *
     * @return array
     */
    private function getDocumentosComprobatoriosSinteseCurriculoCollection(
        MembroChapa $membroChapa,
        ConviteStatusFiltroTO $conviteStatusFiltroTO
    ): array
    {
        $comprovantesIndicacaoInstituicao = array_map(static function ($comprovanteIndicacaoInstituicao) use (
            $membroChapa,
            $conviteStatusFiltroTO
        ) {
            $documentoComprobatorio = DocumentoComprobatorioSinteseCurriculo::newInstance([
                'nomeArquivo' => $comprovanteIndicacaoInstituicao,
                'tipoDocumentoComprobatorioSinteseCurriculo' => Constants::TIPO_DOCUMENTO_COMPROB_COMPROVANTE
            ]);
            $documentoComprobatorio->setMembroChapa($membroChapa);
            return $documentoComprobatorio;
        }, $conviteStatusFiltroTO->getComprovantesVinculoDocenteIes());

        $cartasIndicacaoInstituicao = array_map(static function ($cartaIndicacaoInstituicao) use (
            $membroChapa,
            $conviteStatusFiltroTO
        ) {
            $documentoComprobatorio = DocumentoComprobatorioSinteseCurriculo::newInstance([
                'nomeArquivo' => $cartaIndicacaoInstituicao,
                'tipoDocumentoComprobatorioSinteseCurriculo' => Constants::TIPO_DOCUMENTO_COMPROB_CARTA_INDICACAO
            ]);
            $documentoComprobatorio->setMembroChapa($membroChapa);
            return $documentoComprobatorio;
        }, $conviteStatusFiltroTO->getCartasIndicacaoInstituicao());

        return array_merge($comprovantesIndicacaoInstituicao, $cartasIndicacaoInstituicao);
    }

    /**
     * Salva os arquivos de sintese do currículo.
     *
     * @param MembroChapa $membroChapa
     * @param $fotoSinteseCurriculo
     * @param array $documentosComprobatoriosIES
     *
     * @return array
     * @throws Exception
     */
    private function salvarArquivosSinteseCurriculo(
        MembroChapa $membroChapa,
        $fotoSinteseCurriculo,
        array $documentosComprobatoriosIES = null
    ): array
    {
        $nomeArquivoFoto = "";
        $diretorioSinteseCurriculo = $this->getArquivoService()->getCaminhoRepositorioSinteseCurriculo(sprintf(
            '%s/%s', $membroChapa->getChapaEleicao()->getId(), $membroChapa->getId()));

        if (!empty($fotoSinteseCurriculo)) {
            $nomeArquivoFoto = sprintf('%s.%s', self::NOME_ARQUIVO_FOTO, $fotoSinteseCurriculo->extension());

            $this->getArquivoService()->salvar($diretorioSinteseCurriculo, $nomeArquivoFoto, $fotoSinteseCurriculo);
        }


        if (null !== $documentosComprobatoriosIES) {
            /** @var DocumentoComprobatorioSinteseCurriculo $documentoComprobatorioIES */
            foreach ($documentosComprobatoriosIES as $k => $documentoComprobatorioIES) {
                $this->getArquivoService()->validarDocsComprobatoriosSinteseCurriculo($documentoComprobatorioIES->getNomeArquivo());

                $diretorioDocumentoComprobatorio = Constants::PATH_STORAGE_DOCS_COMPROBATORIOS_SINTESE_CURRICULO . DIRECTORY_SEPARATOR;
                $diretorioSinteseCurriculoDocumento = $diretorioSinteseCurriculo . DIRECTORY_SEPARATOR . $diretorioDocumentoComprobatorio;

                $nomeArquivoDocumento = self::NOME_ARQUIVO_CARTA_INDICACAO;
                if (Constants::TIPO_DOCUMENTO_COMPROB_COMPROVANTE === $documentoComprobatorioIES->getTipoDocumentoComprobatorioSinteseCurriculo()) {
                    $nomeArquivoDocumento = self::NOME_ARQUIVO_COMPROVANTE_VINCULO;
                }

                $digitoNaoRewrite = Utils::getData()->getTimestamp() + $k;

                $arquivoDocComprobatorio = $documentoComprobatorioIES->getNomeArquivo();
                $nomeArquivoDocComprobatorio = sprintf('%s_%d.%s', $nomeArquivoDocumento, $digitoNaoRewrite,
                    $arquivoDocComprobatorio->extension());

                $documentoComprobatorioIES->setNomeArquivo($nomeArquivoDocComprobatorio);

                $this->getArquivoService()->salvar($diretorioSinteseCurriculoDocumento, $nomeArquivoDocComprobatorio,
                    $arquivoDocComprobatorio);
            }
        }

        return [
            'nomeFoto' => $nomeArquivoFoto,
            'documentos' => $documentosComprobatoriosIES,
        ];
    }

    /**
     * Retorna a instância do 'DocumentoComprobatorioSinteseCurriculoRepository'.
     *
     * @return DocumentoComprobatorioSinteseCurriculoRepository
     */
    private function getDocumentoComprobatorioSinteseCurriculoRepository()
    {
        if ($this->documentoComprobatorioSinteseCurriculoRepository == null) {
            $this->documentoComprobatorioSinteseCurriculoRepository = $this->getRepository(
                DocumentoComprobatorioSinteseCurriculo::class
            );
        }
        return $this->documentoComprobatorioSinteseCurriculoRepository;
    }

    /**
     * Salva a resposta da declaração do candidato.
     *
     * @param MembroChapa $membroChapa
     * @param array $declaracoes
     *
     * @throws NegocioException
     * @throws Exception
     */
    private function salvarRespostaDeclaracaoMembro(MembroChapa $membroChapa, array $declaracoes): void
    {
        $idAtividadePrincipal = $membroChapa->getChapaEleicao()->getAtividadeSecundariaCalendario()
            ->getAtividadePrincipalCalendario()->getId();

        $idAtividadeSecundaria = $this->getAtividadeSecundariaCalendarioRepository()
            ->getIdAtividadeSecundariaPorAtividadePrincipalENivel(
                $idAtividadePrincipal, 2
            );

        $declaracaoAtividade = $this->getDeclaracaoAtividadeBO()->getDeclaracaoPorAtividadeSecundariaTipo(
            $idAtividadeSecundaria,
            Constants::TIPO_DECLARACAO_MEMBRO_PARA_PARTICIPAR_CHAPA
        );

        /** @var DeclaracaoTO $declaracao */
        $declaracao = $declaracaoAtividade->getDeclaracao();
        if (count($declaracoes) < 1
            || Constants::TIPO_RESPOSTA_DECLARACAO_UNICA === $declaracao->getTipoResposta() && count($declaracoes) > 1
            || Constants::TIPO_RESPOSTA_DECLARACAO_MULTIPLA === $declaracao->getTipoResposta() && count($declaracoes) !== count($declaracao->getItensDeclaracao())
        ) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $membroChapa->setSituacaoRespostaDeclaracao(true);
    }

    /**
     * Retorna uma nova instância de 'DeclaracaoAtividadeBO'.
     *
     * @return DeclaracaoAtividadeBO
     */
    private function getDeclaracaoAtividadeBO()
    {
        if (empty($this->declaracaoAtividadeBO)) {
            $this->declaracaoAtividadeBO = app()->make(DeclaracaoAtividadeBO::class);
        }

        return $this->declaracaoAtividadeBO;
    }

    /**
     * Retorna uma nova instância de 'HistoricoChapaEleicaoBO'.
     *
     * @return HistoricoChapaEleicaoBO
     */
    private function getHistoricoChapaEleicaoBO()
    {
        if (empty($this->historicoChapaEleicaoBO)) {
            $this->historicoChapaEleicaoBO = app()->make(HistoricoChapaEleicaoBO::class);
        }

        return $this->historicoChapaEleicaoBO;
    }

    /**
     * Retorna uma nova instância de 'PedidoSubstituicaoChapaRepository'.
     *
     * @return PedidoSubstituicaoChapaRepository
     */
    private function getPedidoSubstituicaoChapaRepository()
    {
        if (empty($this->pedidoSubstituicaoChapaRepository)) {
            $this->pedidoSubstituicaoChapaRepository = $this->getRepository(
                PedidoSubstituicaoChapa::class
            );
        }

        return $this->pedidoSubstituicaoChapaRepository;
    }

    /**
     * @param PedidoSubstituicaoChapa $pedido
     * @param $membrosSerAlterados
     * @param $idMembroChapa
     */
    private function aceitaConviteSubstituicao(PedidoSubstituicaoChapa $pedido, &$membrosSerAlterados, $idMembroChapa): void
    {
        foreach ($pedido->getMembrosChapaSubstituicao() as $membroChapaSubstituicao) {

            $membroSubstituido = $membroChapaSubstituicao->getMembroChapaSubstituido();
            $membroSubstituto = $membroChapaSubstituicao->getMembroChapaSubstituto();

            if(!empty($membroSubstituto) && $membroSubstituto->getId() == $idMembroChapa){
                $this->processarAceiteConviteSubstituicao($membroSubstituido, $membroSubstituto, $membrosSerAlterados);
            }
        }
    }

    /**
     * @param MembroSubstituicaoJulgamentoFinal $membroSubstituicaoJulgamentoFinal
     * @param $membrosSerAlterados
     */
    private function aceitaConviteSubstituicaoFinal(MembroSubstituicaoJulgamentoFinal $membroSubstituicaoJulgamentoFinal, &$membrosSerAlterados): void
    {
        $membroSubstituido = $this->getSubstituicaoJulgamentoFinalBO()->recuperaMembroChapaIndicacao(
                $membroSubstituicaoJulgamentoFinal
            );
        $membroSubstituto = $membroSubstituicaoJulgamentoFinal->getMembroChapa();

        $this->processarAceiteConviteSubstituicao($membroSubstituido, $membroSubstituto, $membrosSerAlterados);
    }

    /**
     * @param MembroChapa $membroChapa
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @param $membrosSerAlterados
     */
    private function aceitaConviteSubstituicaoImpugnacao($membroChapa, $pedidoImpugnacao, &$membrosSerAlterados): void
    {
        $membroSubstituido = $pedidoImpugnacao->getMembroChapa();
        $membroSubstituto = $membroChapa;

        $this->processarAceiteConviteSubstituicao($membroSubstituido, $membroSubstituto, $membrosSerAlterados);
    }

    /**
     * Retorna uma nova instância de 'ChapaEleicaoBO'.
     *
     * @return ChapaEleicaoBO
     */
    private function getChapaEleicaoBO()
    {
        if (empty($this->chapaEleicaoBO)) {
            $this->chapaEleicaoBO = app()->make(ChapaEleicaoBO::class);
        }

        return $this->chapaEleicaoBO;
    }

    /**
     * @param $idChapaEleicao
     * @param $idTipoEmail
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailResponsaveisChapaConvite($idChapaEleicao, $idTipoEmail)
    {
        $responsaveis = $this->getListaEmailsMembrosResponsaveisChapa($idChapaEleicao);

        if (!empty($responsaveis)) {
            $atividadeSecundariaCalendario = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao(
                $idChapaEleicao, 2, 2
            );

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividadeSecundariaCalendario->getId(), $idTipoEmail
            );

            if (!empty($emailAtividadeSecundaria)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao(
                    $emailAtividadeSecundaria
                );
                $emailTO->setDestinatarios($responsaveis);

                Email::enviarMail(new AtividadeSecundariaMail($emailTO));
            }
        }
    }

    /**
     * Retorna os membros responsáveis de uma chapa conforme o id da chapa informada.
     *
     * @param $idChapaEleicao
     * @return array|null
     * @throws NegocioException
     */
    public function getListaEmailsMembrosResponsaveisChapa($idChapaEleicao)
    {
        $membrosResponsaveis = $this->getMembrosResponsaveisChapa(
            $idChapaEleicao,
            Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
        );

        return $this->getListEmailsDestinatarios($membrosResponsaveis);
    }

    /**
     * Retorna os membros responsáveis de uma chapa conforme o id da chapa informada.
     *
     * @param $idChapaEleicao
     * @param int|null $idStatusParticipacaoChapa
     *
     * @return array|null
     */
    public function getMembrosResponsaveisChapa($idChapaEleicao, int $idStatusParticipacaoChapa = null)
    {
        $membroChapaFiltroTO = MembroChapaFiltroTO::newInstance([
            'idChapaEleicao' => $idChapaEleicao,
            'situacaoResponsavel' => true,
            'idStatusParticipacaoChapa' => $idStatusParticipacaoChapa
        ]);

        return $this->membroChapaRepository->getMembrosPorFiltro($membroChapaFiltroTO);
    }

    /**
     * Retorna uma lista de e-mails de destinatários de acordo com a lista de membros informado
     *
     * @param MembroChapa[] $membrosChapa
     *
     * @return array
     */
    public function getListEmailsDestinatarios($membrosChapa)
    {
        $destinatariosEmail = [];

        if (!empty($membrosChapa)) {
            foreach ($membrosChapa as $membroChapa) {
                $destinatariosEmail[$membroChapa->getId()] = $membroChapa->getProfissional()->getPessoa()->getEmail();
            }
        }
        return $destinatariosEmail;
    }

    /**
     * @param MembroChapa[] $membrosChapa
     * @return array
     */
    public function getListEmailsByMembros($membrosChapa) {
        $destinatariosEmail = null;

        if (!empty($membrosChapa)) {
            foreach ($membrosChapa as $membroChapa) {
                $destinatariosEmail[] = $membroChapa->getProfissional()->getPessoa()->getEmail();
            }
        }
        return $destinatariosEmail;
    }

    /**
     * Retorna uma nova instância de 'AtividadeSecundariaCalendarioBO'.
     *
     * @return AtividadeSecundariaCalendarioBO
     */
    private function getAtividadeSecundariaCalendarioBO()
    {
        if (empty($this->atividadeSecundariaBO)) {
            $this->atividadeSecundariaBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }

        return $this->atividadeSecundariaBO;
    }

    /**
     * Retorna uma nova instância de 'EmailAtividadeSecundariaBO'.
     *
     * @return EmailAtividadeSecundariaBO
     */
    private function getEmailAtividadeSecundariaBO()
    {
        if (empty($this->emailAtividadeSecundariaBO)) {
            $this->emailAtividadeSecundariaBO = app()->make(EmailAtividadeSecundariaBO::class);
        }

        return $this->emailAtividadeSecundariaBO;
    }

    /**
     * Rejeita o convite para se tornar membro de uma chapa através do id membro chapa e id do usuario logado.
     *
     * @param ConviteStatusFiltroTO $conviteStatusFiltroTO
     *
     * @return void
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function rejeitarConvite(ConviteStatusFiltroTO $conviteStatusFiltroTO)
    {
        /** @var MembroChapa $membroChapa */
        $membroChapa = $this->membroChapaRepository->find($conviteStatusFiltroTO->getIdMembroChapa());

        try {
            $this->beginTransaction();

            $membroChapa->setStatusParticipacaoChapa(StatusParticipacaoChapa::newInstance([
                'id' => Constants::SITUACAO_MEMBRO_REJEITADO
            ]));

            if ($membroChapa->getSituacaoMembroChapa()->getId() == Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO) {
                $membroChapa->setSituacaoMembroChapa(SituacaoMembroChapa::newInstance([
                    'id' => Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_INDEFERIDO
                ]));
            }

            $this->membroChapaRepository->persist($membroChapa);

            $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
                $membroChapa->getChapaEleicao(),
                Constants::ORIGEM_PROFISSIONAL,
                Constants::HISTORICO_REJEICAO_PARTICIPACAO_CHAPA
            );
            $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        Utils::executarJOB(new EnviarEmailConviteMembroChapaJob(
            $membroChapa->getChapaEleicao()->getId(), Constants::EMAIL_MEMBRO_CHAPA_REJEITOU_CONVITE
        ));
    }

    /**
     * Rejeita o convite para se tornar membro de uma chapa através do id membro chapa e id do usuario logado.
     *
     * @param ConviteStatusFiltroTO $conviteStatusFiltroTO
     *
     * @return void
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function rejeitarConvitesAposCadastroJulgFinalSegundaInstancia($idChapaEleicao)
    {
        $membrosChapa = $this->membroChapaRepository->getMembrosChapaAConfirmarDeSubstituicaoJulgFinalPorChapa(
            $idChapaEleicao, [Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO]
        );

        if (!empty($membrosChapa)) {
            $this->rejeitarConviteMembrosChapa($membrosChapa);
        }
    }

    /**
     * Rejeita todos os convites para membro de uma chapa após o fim do prazo do calendário.
     *
     * @throws Exception
     */
    public function rejeitarConvitesForaVigenciaPeriodoCadastro()
    {
        $dataFim = Utils::getDataHoraZero(Utils::subtrairDiasData(Utils::getDataHoraZero(), 1));
        $membrosChapa = $this->membroChapaRepository->getMembrosChapaAConfirmarPrazoExpiradoPorNivelAtividade(
            2, 2, Constants::ST_MEMBRO_CHAPA_CADASTRADO, $dataFim
        );

        if (!empty($membrosChapa)) {
            $this->rejeitarConviteMembrosChapa($membrosChapa);
        }
    }

    /**
     * @param array|null $membrosChapa
     * @throws Exception
     */
    public function rejeitarConviteMembrosChapa(?array $membrosChapa): void
    {
        $idsChapas = [];
        try {
            $this->beginTransaction();

            /** @var MembroChapa $membroChapa */
            foreach ($membrosChapa as $membroChapa) {
                $idsChapas[] = $membroChapa->getChapaEleicao()->getId();

                $idStatusParticipacao = Constants::SITUACAO_MEMBRO_REJEITADO;
                $membroChapa->setStatusParticipacaoChapa(StatusParticipacaoChapa::newInstanceById($idStatusParticipacao));

                if ($membroChapa->getSituacaoMembroChapa()->getId() == Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO) {
                    $idSituacao = Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_INDEFERIDO;
                    $membroChapa->setSituacaoMembroChapa(SituacaoMembroChapa::newInstanceById($idSituacao));
                }
            }

            $this->membroChapaRepository->persistEmLote($membrosChapa);
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if (!empty($idsChapas)) {
            foreach ($idsChapas as $idChapa) {
                Utils::executarJOB(new EnviarEmailConviteMembroChapaJob(
                    $idChapa, Constants::EMAIL_MEMBRO_CHAPA_REJEITOU_CONVITE
                ));
            }
        }
    }

    /**
     * Rejeita todos os convites para membro de uma chapa após o fim do prazo do calendário.
     *
     * @throws Exception
     */
    public function rejeitarConvitesForaVigenciaPeriodoSubstituicao()
    {
        $dataFim = Utils::getDataHoraZero(Utils::subtrairDiasData(Utils::getDataHoraZero(), 1));
        $membrosChapaImpugnacao = $this->membroChapaRepository->getMembrosChapaAConfirmarPrazoExpiradoPorImpugnacao(
            3, 8, Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO, $dataFim
        );

        $membrosChapaSubst = $this->membroChapaRepository->getMembrosChapaAConfirmarPrazoExpiradoPorSubstituicao(
            2, [7, 8], Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO, $dataFim
        );

        $membrosChapaSubstFinal = $this->membroChapaRepository->getMembrosChapaAConfirmarPrazoExpiradoPorSubstituicaoFinal(
            5, [3, 6], Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO, $dataFim
        );

        $membrosChapa = array_merge(
            $membrosChapaImpugnacao ?? [],
            $membrosChapaSubst ?? [],
            $membrosChapaSubstFinal ?? []
        );

        if (!empty($membrosChapa)) {
            $this->rejeitarConviteMembrosChapa($membrosChapa);
        }
    }

    /**
     * Reenvia e-mail para o membro, informando que foi cadastrado para participar da Chapa Eleitoral.
     *
     * @param $id
     */
    public function reenviarConvite($id)
    {
        /** @var MembroChapa $membroChapa */
        $membroChapa = $this->membroChapaRepository->find($id);

        if (empty($membroChapa)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if ($membroChapa->getStatusParticipacaoChapa()->getId() == Constants::STATUS_PARTICIPACAO_MEMBRO_REJEITADO) {
            $dataInicio = Utils::getDataHoraZero($membroChapa->getChapaEleicao()->getAtividadeSecundariaCalendario()->getDataInicio());
            $datafim = Utils::getDataHoraZero($membroChapa->getChapaEleicao()->getAtividadeSecundariaCalendario()->getDataFim());
            if (
                !$this->getUsuarioFactory()->isCorporativoAssessorCEN()
                && (($datafim < Utils::getDataHoraZero()) or ($dataInicio > Utils::getDataHoraZero()))
            ) {
                throw new NegocioException(Message::MSG_SEM_PERIODO_VIGENTE_INCLUIR_CHAPA);
            }

            $this->verificarSeJaConfirmouParticipacaoChapa(
                $membroChapa->getProfissional()->getId(),
                $membroChapa->getChapaEleicao()->getAtividadeSecundariaCalendario()->getId()
            );

            $membroChapa->setStatusParticipacaoChapa(StatusParticipacaoChapa::newInstanceById(
                Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR
            ));

            $this->membroChapaRepository->persist($membroChapa);
        }

        Utils::executarJOB(new EnviarEmailMembroIncluidoChapaJob($id));
    }

    /**
     * Retorna um membro chapa da eleição vigente de acordo com o id e ud do profissional
     * Se não passar os ids situações ele considera as situações cadastrado ou substituto deferido
     *
     * @param integer $idCalendario
     * @param integer $idProfissional
     * @param integer $idCauUf
     * @param null $idsSituacoes
     * @return array|null
     * @throws Exception
     */
    public function getMembrosChapaPorCalendarioProfissioal(
        $idCalendario,
        $idProfissional,
        $idCauUf,
        $idsSituacoes = null
    )
    {
        $membroChapa = $this->membroChapaRepository->getMembrosChapaPorCalendarioProfissioal(
            $idCalendario,
            $idProfissional,
            $idCauUf,
            $idsSituacoes
        );
        return $membroChapa;
    }

    /**
     * Reotorna o membro chapa atual (confirmado e cm situação atual) por calendário
     * @param $idCalendario
     * @param $idProfissioanl
     */
    public function getMembroChapaAtualPorCalendarioProfissioal($idCalendario, $idProfissioanl)
    {
        return $this->membroChapaRepository->getMembroChapaAtualPorCalendarioProfissioal($idCalendario, $idProfissioanl);
    }

    /**
     * Rejeita todos os convites para membro de uma chapa após o fim do prazo do calendário.
     *
     * @throws Exception
     */
    public function alertarConvitesASeremAceitos()
    {
        $membrosChapa = $this->membroChapaRepository->getMembrosChapaAConfirmarDiasLimitesConvite();

        try {
            $this->beginTransaction();

            $idsAtividadeSecundaria = array_unique(array_map(static function (MembroChapa $membroChapa) {
                return $membroChapa->getChapaEleicao()->getAtividadeSecundariaCalendario()->getId();
            }, $membrosChapa));

            /** @var int $idAtividadeSecundaria */
            foreach ($idsAtividadeSecundaria as $idAtividadeSecundaria) {
                /** @var MembroChapa[] $membrosChapaAtividadeSecundaria */
                $membrosChapaAtividadeSecundaria = array_filter(
                    $membrosChapa, static function (MembroChapa $membroChapa) use ($idAtividadeSecundaria) {
                    return $idAtividadeSecundaria === $membroChapa->getChapaEleicao()->getAtividadeSecundariaCalendario()->getId();
                });

                $idAtividadePrincipal = $membrosChapaAtividadeSecundaria[0]->getChapaEleicao()
                    ->getAtividadeSecundariaCalendario()->getAtividadePrincipalCalendario()->getId();

                $idAtividadeSecundariaConvite = $this->getAtividadeSecundariaCalendarioRepository()
                    ->getIdAtividadeSecundariaPorAtividadePrincipalENivel(
                        $idAtividadePrincipal, 2
                    );

                $emailsDestinatario = $this->getListEmailsDestinatarios($membrosChapaAtividadeSecundaria);

                $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                    $idAtividadeSecundariaConvite,
                    Constants::EMAIL_MEMBRO_CHAPA_CONVITE_A_CONFIRMAR);

                if (null !== $emailAtividadeSecundaria) {
                    $this->getEmailAtividadeSecundariaBO()->enviarEmailAtividadeSecundaria(
                        $emailAtividadeSecundaria,
                        $emailsDestinatario
                    );
                }
            }

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Altera o status do Convite dado um id membro chapa.
     *
     * @param StatusMembroChapaTO $statusMembroChapaTO
     *
     * @return MembroChapaTO|null
     * @throws Exception
     */
    public function alterarStatusParticipacao(StatusMembroChapaTO $statusMembroChapaTO)
    {
        $this->validarAlteracaoStatusMembroChapa($statusMembroChapaTO);

        $idStatusParticipacao = $statusMembroChapaTO->getIdStatusParticipacaoChapa();

        try {
            $this->beginTransaction();

            $membroChapa = $this->membroChapaRepository->find($statusMembroChapaTO->getIdMembroChapa());
            if (!empty($membroChapa)) {
                $membroChapa->setStatusParticipacaoChapa(StatusParticipacaoChapa::newInstance([
                    'id' => $idStatusParticipacao
                ]));
                $this->membroChapaRepository->persist($membroChapa);

                $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
                    $membroChapa->getChapaEleicao(),
                    Constants::ORIGEM_CORPORATIVO,
                    sprintf(Constants::HISTORICO_ALTERACAO_STATUS_CONFIRMACAO_CPF,
                        $membroChapa->getProfissional()->getCpf()),
                    $statusMembroChapaTO->getJustificativa()
                );
                $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);

                $idSituacaoChapa = $idStatusParticipacao != Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
                    ? Constants::SITUACAO_CHAPA_PENDENTE
                    : Constants::SITUACAO_CHAPA_CONCLUIDA;

                $this->alterarStatusChapaPosAlteracaoMembro($membroChapa->getChapaEleicao()->getId(), $idSituacaoChapa);
            }

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        return MembroChapaTO::newInstanceFromEntity($membroChapa);
    }

    /**
     * Método auxiliar para os métodos de alterar status de participação e alterar status de validação.
     *
     * @param StatusMembroChapaTO $statusMembroChapaTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function validarAlteracaoStatusMembroChapa(StatusMembroChapaTO $statusMembroChapaTO): void
    {
        if (!$this->getUsuarioFactory()->hasPermissao(Constants::PERMISSAO_ACESSOR_CEN)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if (empty($statusMembroChapaTO->getJustificativa())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }
    }

    /**
     * @param int $idChapaEleicao
     * @param $idSituacaoChapa
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function alterarStatusChapaPosAlteracaoMembro(int $idChapaEleicao, $idSituacaoChapa): void
    {
        $chapaEleicao = $this->getChapaEleicaoRepository()->getPorId($idChapaEleicao);
        $chapaEleicao->definirStatusChapaVigente();

        if (
            $chapaEleicao->getIdTipoAlteracaoVigente() == Constants::TP_ALTERACAO_AUTOMATICO
            && $chapaEleicao->getStatusChapaVigente()->getId() != $idSituacaoChapa
        ) {
            if ($idSituacaoChapa == Constants::SITUACAO_CHAPA_CONCLUIDA) {
                $this->getChapaEleicaoBO()->concluirChapa($chapaEleicao, Constants::TP_ALTERACAO_AUTOMATICO);
            } else {
                $this->getChapaEleicaoBO()->salvarChapaEleicaoStatus(
                    $chapaEleicao->getId(),
                    $idSituacaoChapa,
                    Constants::TP_ALTERACAO_AUTOMATICO
                );
            }
        }
    }

    /**
     * Altera o status do Validação dado um id membro chapa.
     *
     * @param StatusMembroChapaTO $statusMembroChapaTO
     *
     * @return MembroChapaTO|null
     * @throws Exception
     */
    public function alterarStatusValidacao(StatusMembroChapaTO $statusMembroChapaTO)
    {
        $this->validarAlteracaoStatusMembroChapa($statusMembroChapaTO);

        try {
            $this->beginTransaction();

            /** @var MembroChapa $membroChapa */
            $membroChapa = $this->membroChapaRepository->find($statusMembroChapaTO->getIdMembroChapa());
            if (!empty($membroChapa)) {
                $this->salvarStatusValidacaoMembroChapa($membroChapa, $statusMembroChapaTO->getIdStatusValidacao());

                if ($statusMembroChapaTO->getIdStatusValidacao() == Constants::STATUS_VALIDACAO_MEMBRO_SEM_PENDENCIA) {
                    $pendencias = [];
                } else {
                    $pendencias = $membroChapa->getPendencias() ?? [];
                    if (!$this->hasTipoPendencia($membroChapa, Constants::PENDENCIA_MEMBRO_CHAPA_DECORRER_PROCESSO_ELEITORAL)) {
                        $membroChapaPendencia = MembroChapaPendencia::newInstance();
                        $membroChapaPendencia->setTipoPendencia(TipoPendencia::newInstance([
                            "id" => Constants::PENDENCIA_MEMBRO_CHAPA_DECORRER_PROCESSO_ELEITORAL
                        ]));
                        $membroChapaPendencia->setMembroChapa($membroChapa);
                        $pendencias[] = $membroChapaPendencia;
                    }
                }
                $pendenciasSalvo = $this->getMembroChapaPendenciaBO()->salvarMembroChapaPendencias($membroChapa, $pendencias);
                $membroChapa->setPendencias($pendenciasSalvo);


                $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
                    $membroChapa->getChapaEleicao(),
                    Constants::ORIGEM_CORPORATIVO,
                    sprintf(
                        Constants::HISTORICO_ALTERACAO_STATUS_VALIDACAO_CPF,
                        $membroChapa->getProfissional()->getCpf()
                    ),
                    $statusMembroChapaTO->getJustificativa()
                );
                $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);
            }

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        return MembroChapaTO::newInstanceFromEntity($membroChapa);
    }

    /**
     * Salvar status de validação do membro da chapa da eleição
     *
     * @param MembroChapa $membroChapa
     * @param $idStatusValidacaoMembroChapa
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function salvarStatusValidacaoMembroChapa(MembroChapa $membroChapa, $idStatusValidacaoMembroChapa)
    {
        $membroChapa->setStatusValidacaoMembroChapa(StatusValidacaoMembroChapa::newInstance([
            'id' => $idStatusValidacaoMembroChapa
        ]));

        $this->membroChapaRepository->persist($membroChapa);
    }

    private function hasTipoPendencia(MembroChapa $membroChapa, $idTipoPendencia)
    {
        $hasPendencia = false;
        if (!empty($membroChapa->getPendencias())) {

            /** @var MembroChapaPendencia $pendencia */
            foreach ($membroChapa->getPendencias() as $pendencia) {
                if ($pendencia->getTipoPendencia()->getId() == $idTipoPendencia) {
                    $hasPendencia = true;
                    break;
                }
            }
        }
        return $hasPendencia;
    }

    /**
     * Retorna uma nova instância de 'MembroChapaPendenciaBO'.
     *
     * @return MembroChapaPendenciaBO
     */
    private function getMembroChapaPendenciaBO()
    {
        if (empty($this->membroChapaPendenciaBO)) {
            $this->membroChapaPendenciaBO = app()->make(MembroChapaPendenciaBO::class);
        }

        return $this->membroChapaPendenciaBO;
    }

    /**
     * @param array $criterios
     * @return array
     */
    public function findBy($criterios)
    {
        return $this->membroChapaRepository->findBy($criterios);
    }

    /**
     * Altera a situação de responsável dos membros de uma chapa
     *
     * @param ChapaEleicao $chapaEleicao
     * @param array $listaMembrosChapaTO
     *
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NegocioException
     */
    public function alterarSituacaoResponsavelMembrosChapa(ChapaEleicao $chapaEleicao, $listaMembrosChapaTO)
    {
        foreach ($listaMembrosChapaTO as $membroChapaTO) {
            $membroChapa = $this->membroChapaRepository->getPorId($membroChapaTO->getId());

            if (empty($membroChapa) || $membroChapa->getChapaEleicao()->getId() != $chapaEleicao->getId()) {
                throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
            }

            if ($membroChapa->getProfissional()->getId() != $chapaEleicao->getIdProfissionalInclusao()) {
                $membroChapa->setSituacaoResponsavel($membroChapaTO->isSituacaoResponsavel());
                $this->membroChapaRepository->persist($membroChapa);
            }
        }
    }

    /**
     * Inclui um membro na chapa a partir do CPF do profissional
     *
     * @param integer $idChapaEleicao
     * @param stdClass $dadosTO
     *
     * @return MembroChapa|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function incluirMembroChapa($idChapaEleicao, $dadosTO)
    {
        $chapaEleicao = $this->getChapaEleicaoRepository()->getPorId($idChapaEleicao);

        $isAcessorCEN = $this->getUsuarioFactory()->hasPermissao(Constants::PERMISSAO_ACESSOR_CEN);
        $isProfissional = $this->getUsuarioFactory()->hasPermissao(Constants::ROLE_PROFISSIONAL);

        if (!$isAcessorCEN and !$isProfissional) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $this->validarCamposIncluirMembroChapa($chapaEleicao, $dadosTO, $isAcessorCEN);

        $profissionalTO = $this->getProfissionalBO()->getPorId($dadosTO->idProfissional, true);

        $this->validarMembroPodeSerIncuidoChapa($chapaEleicao, $profissionalTO, $isAcessorCEN);
        $this->validarImpedimentosIncluirMembro($chapaEleicao, $profissionalTO, $dadosTO->idTipoMembroChapa);

        $membroChapaRetorno = null;
        try {
            $this->beginTransaction();

            $membroAnterior = $this->getMembroChapaPorTipoNumeroOrdem(
                $chapaEleicao->getId(),
                $dadosTO->idTipoMembroChapa,
                $dadosTO->idTipoParticipacao,
                $dadosTO->numeroOrdem
            );

            $isAlteracao = false;
            if (!empty($membroAnterior)) {
                $totalResponsaveis = $this->membroChapaRepository->totalMembrosResponsaveisChapa(
                    $idChapaEleicao
                );

                if ($membroAnterior->isSituacaoResponsavel() && $totalResponsaveis == 1) {
                    throw new NegocioException(Lang::get('messages.membro_chapa.exclusao_unico_responsavel'));
                }

                $isAlteracao = true;
                RespostaDeclaracaoRepresentatividade::where('id_membro_chapa', $membroAnterior->getId())->delete();
            }

            $cpfAnterior = !empty($membroAnterior) ? $membroAnterior->getProfissional()->getCpf() : null;
            $membroChapa = $this->prepararMembroChapaParaIncluir(
                $membroAnterior, $chapaEleicao, $dadosTO, $profissionalTO, $isAcessorCEN
            );

            /** @var MembroChapa $membroSalvo */
            $membroSalvo = $this->membroChapaRepository->persist($membroChapa);

            if ($membroChapa->getTipoParticipacaoChapa()->getId() == Constants::TIPO_PARTICIPACAO_CHAPA_SUPLENTE) {
                $titular = $this->getMembroChapaPorTipoNumeroOrdem(
                    $chapaEleicao->getId(),
                    $membroChapa->getTipoMembroChapa()->getId(),
                    Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR,
                    $membroChapa->getNumeroOrdem()
                );

                if (!empty($titular)) {
                    $titular->setSuplente($membroSalvo);
                    $this->membroChapaRepository->persist($titular);
                }
            }

            $this->salvarPendenciasMembro($membroSalvo, $profissionalTO);

            $this->salvarHistoricoIncluirMembroChapa(
                $chapaEleicao,
                $membroSalvo,
                $isAcessorCEN ? Constants::ORIGEM_CORPORATIVO : Constants::ORIGEM_PROFISSIONAL,
                $cpfAnterior,
                $isAcessorCEN ? $dadosTO->justificativa : null
            );

            if ($chapaEleicao->getIdEtapa() == Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA) {
                $this->getChapaEleicaoBO()->concluirChapa(
                    $chapaEleicao, Constants::TP_ALTERACAO_AUTOMATICO
                );

                Utils::executarJOB(new EnviarEmailMembroIncluidoChapaJob($membroSalvo->getId()));
            }

            $membroChapaRetorno = $this->membroChapaRepository->getPorId($membroSalvo->getId());

            if ($isAlteracao) {
                $this->excluirFotoAlteracaoMembro($membroAnterior);
            }

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        return $membroChapaRetorno;
    }

    /**
     * Verifica se os campos obrigatórios foram preenchidos inclusão de membros.
     *
     * @param ChapaEleicao $chapaEleicao
     * @param stdClass $dadosTO
     * @param bool $isAcessorCEN
     *
     * @throws NegocioException
     */
    private function validarCamposIncluirMembroChapa(?ChapaEleicao $chapaEleicao, $dadosTO, $isAcessorCEN)
    {
        if (empty($chapaEleicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        if (empty($dadosTO->idProfissional)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        if (empty($dadosTO->idTipoParticipacao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        if (empty($dadosTO->idTipoMembroChapa)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        if ($dadosTO->idTipoMembroChapa == Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL &&
            empty($dadosTO->numeroOrdem)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        if ($isAcessorCEN and empty($dadosTO->justificativa)) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
        }
    }

    /**
     * Verifica se o profissional já confirmou participação em alguma chapa.
     *
     * @param ChapaEleicao $chapaEleicao
     * @param ProfissionalTO $profissionalTO
     * @param bool $isAcessorCEN
     *
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function validarMembroPodeSerIncuidoChapa(ChapaEleicao $chapaEleicao, $profissionalTO, $isAcessorCEN)
    {
        $dataInicio = Utils::getDataHoraZero($chapaEleicao->getAtividadeSecundariaCalendario()->getDataInicio());
        $datafim = Utils::getDataHoraZero($chapaEleicao->getAtividadeSecundariaCalendario()->getDataFim());
        if (!$isAcessorCEN and (($datafim < Utils::getDataHoraZero()) or ($dataInicio > Utils::getDataHoraZero()))) {
            throw new NegocioException(Message::MSG_SEM_PERIODO_VIGENTE_INCLUIR_CHAPA);
        }

        if (empty($profissionalTO)) {
            throw new NegocioException(Message::MSG_CPF_NAO_ENCONTRADO_SICCAU, [], true);
        }

        $totalConvitesConfirmados = $this->totalConvitePorStatusParticipacao(
            $profissionalTO->getId(),
            $chapaEleicao->getAtividadeSecundariaCalendario()->getId(),
            Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
        );

        if (!empty($totalConvitesConfirmados) && $totalConvitesConfirmados > 0) {
            throw new NegocioException(Message::MSG_CPF_ACEITOU_PARTIC_OUTRA_CHAPA, [], true);
        }
    }

    /**
     * Validações das pendências de impedimento para inclusão do membro na chapa
     *
     * @param ChapaEleicao $chapaEleicao
     * @param ProfissionalTO|Profissional $profissional
     *
     * @param $idTipoMembro
     * @param bool $isSubstituicao
     * @throws NegocioException
     * @throws Exception
     */
    public function validarImpedimentosIncluirMembro(
        ChapaEleicao $chapaEleicao,
        $profissional,
        $idTipoMembro,
        $isSubstituicao = false
    )
    {
        $pendencias = [];

        if (empty($profissional->getSituacaoRegistro()) || empty($profissional->getSituacaoRegistro()->id)) {
            $pendencias[] = Message::$descriptions[Message::MSG_CPF_SEM_REGISTRO_CAU_COMPLETO];
        }

        if ($chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR &&
            $profissional->getIdCauUf() != $chapaEleicao->getIdCauUf()) {
            $pendencias[] = Message::$descriptions[Message::MSG_UF_CPF_DIFERENTE_UF_CHAPA];
        }

        if (!$isSubstituicao) {
            $membros = $this->membroChapaRepository->getMembroChapaPorProfissional(
                $chapaEleicao->getId(),
                $profissional->getId(),
                [
                    Constants::ST_MEMBRO_CHAPA_CADASTRADO,
                    Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO,
                    Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO,
                ]
            );

            if (!empty($membros)) {
                $pendencias[] = Message::$descriptions[Message::MSG_CPF_JA_INCLUIDO_CHAPA];
            }
        }

        /*$isConselheiroSubsequente = $profissional->getConselheiroSubsequente()->situacao ?? false;
        $cargo = $profissional->getConselheiroSubsequente()->cargo ?? null;

        if (!empty($cargo)) {
            if ($cargo == Constants::DS_REPRESENTACAO_FEDERAL) {
                $cargo = Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL;
            } else if ($cargo == Constants::DS_REPRESENTACAO_ESTADUAL) {
                $cargo = Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL;
            }
        }

        if ($isConselheiroSubsequente and $idTipoMembro == $cargo) {
            $pendencias[] = Message::$descriptions[Message::MSG_CPF_FOI_ELEITO_MANDATO_SUBSEQUENTE];
        }*/

        /*$isPerdaMandato = $profissional->getPerdaMandatoConselheiro()->situacao ?? false;
        $dataPerdaMandato = $profissional->getPerdaMandatoConselheiro()->dataPerdaMandato ?? null;
        if ($isPerdaMandato) {
        /*
            /** @var DateTime $dataAcrescentadoCincoAnos */
            /*$dataAcrescentadoCincoAnos = Utils::adicionarAnosToData(
                Utils::getDataToString($dataPerdaMandato, 'Y-m-d'), 5
            );

            $anoEleicao = $this->getChapaEleicaoRepository()->getAnoEleicaoChapa($chapaEleicao->getId());
            if ($dataAcrescentadoCincoAnos->format('Y') >= $anoEleicao) {
                $pendencias[] = Message::$descriptions[Message::MSG_CPF_PERDEU_MANDATO_ULTIMOS_CINCO_ANOS];
            }
        }*/

        /*if ($profissional->isInfracaoEtica()) {
            $pendencias[] = Message::$descriptions[Message::MSG_CPF_POSSUI_SANCAO_ETICO_DISCIPLINAR];
        }*/

        /*$isSancionado = $profissional->getSancionadoInfracaoEticaDisciplinar()->situacao ?? false;
        if ($isSancionado) {
            $dataReabilitacao = $profissional->getSancionadoInfracaoEticaDisciplinar()->dataReabilitacao;
            if (!empty($dataReabilitacao)) {
                $transcursoReabilitacao = Utils::adicionarAnosToData(
                    Utils::getDataToString($dataReabilitacao, 'Y-m-d'), 3
                );
                if ($transcursoReabilitacao >= Utils::getDataHoraZero()) {
                    $pendencias[] = Message::$descriptions[Message::MSG_CPF_SANCIONADO_INFRACAO_ETICO_DISCIPLINAR];
                }
            }
        }*/

        /*$isDevedorMultaProcessoEleitoral = $profissional->isMultaProcessoEleitoral() ?? false;
        if ($isDevedorMultaProcessoEleitoral) {
            $pendencias[] = Message::$descriptions[Message::MSG_CPF_DEVEDOR_MULTA_PROCESSO_ELEITORAL];
        }*/

        if (!empty($pendencias)) {
            throw new NegocioException(Message::MSG_PENDENCIA_INCLUIR_MEMBRO_CHAPA, $pendencias, true);
        }
    }

    /**
     * Retorna um membro chapa em uma posição específica na chapa
     *
     * @param integer $idChapaEleicao
     * @param integer $idTipoMembro
     * @param integer $idTipoParticipacaoChapa
     * @param integer $numeroOrdem
     *
     * @return MembroChapa
     */
    public function getMembroChapaPorTipoNumeroOrdem(
        $idChapaEleicao,
        $idTipoMembro,
        $idTipoParticipacaoChapa,
        $numeroOrdem
    )
    {
        $membroFiltroTo = MembroChapaFiltroTO::newInstance(compact(
            'idChapaEleicao',
            "idTipoMembro",
            "idTipoParticipacaoChapa",
            "numeroOrdem"
        ));

        $membros = $this->membroChapaRepository->getMembrosPorFiltro($membroFiltroTo);
        return !empty($membros) ? array_shift($membros) : null;
    }

    /**
     * Método auxiliar do método incluirMembroChapa que instância e seta os valores do membro chapa para incluir
     *
     * @param MembroChapa|null $membroChapa
     * @param ChapaEleicao $chapaEleicao
     * @param $dadosTO
     * @param ProfissionalTO $profissionalTO
     * @param bool $isAcessorCEN
     *
     * @return MembroChapa
     * @throws Exception
     */
    private function prepararMembroChapaParaIncluir(
        ?MembroChapa $membroChapa,
        ChapaEleicao $chapaEleicao,
        $dadosTO,
        $profissionalTO,
        $isAcessorCEN
    )
    {
        if (empty($membroChapa)) {
            $membroChapa = MembroChapa::newInstance();
            $membroChapa->setChapaEleicao($chapaEleicao);

            $idTipoMembro = ($chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES)
                ? Constants::TIPO_MEMBRO_CHAPA_REPRESENTANTE_IES
                : $dadosTO->idTipoMembroChapa;

            $membroChapa->setTipoMembroChapa(TipoMembroChapa::newInstance([
                "id" => $idTipoMembro
            ]));

            $membroChapa->setTipoParticipacaoChapa(TipoParticipacaoChapa::newInstance([
                "id" => $dadosTO->idTipoParticipacao
            ]));

            $membroChapa->setStatusValidacaoMembroChapa(StatusValidacaoMembroChapa::newInstance([
                "id" => Constants::STATUS_VALIDACAO_MEMBRO_PENDENTE
            ]));

            if ($dadosTO->idTipoMembroChapa == Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL) {
                $membroChapa->setNumeroOrdem($dadosTO->numeroOrdem);
            } else {
                $membroChapa->setNumeroOrdem(0);
            }

            if ($dadosTO->idTipoParticipacao == Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR) {
                $membroFiltroTO = MembroChapaFiltroTO::newInstance([
                    "idChapaEleicao" => $chapaEleicao->getId(),
                    "numeroOrdem" => $dadosTO->numeroOrdem,
                    "idTipoMembro" => $dadosTO->idTipoMembroChapa,
                    "idTipoParticipacaoChapa" => Constants::TIPO_PARTICIPACAO_CHAPA_SUPLENTE
                ]);

                $suplente = $this->membroChapaRepository->getMembrosPorFiltro($membroFiltroTO);
                if (!empty($suplente)) {
                    $suplente = reset($suplente);
                    $membroChapa->setSuplente($suplente);
                }
            }
        } else {
            /** @var MembroChapa $membroChapa */
            $membroChapa = $this->membroChapaRepository->find($membroChapa->getId());
            $membroChapa->setNomeArquivoFoto(null);
            $membroChapa->setSituacaoRespostaDeclaracao(null);
            $membroChapa->setSinteseCurriculo(null);
        }

        $isResponsavel = $chapaEleicao->getIdProfissionalInclusao() == $profissionalTO->getId();

        $membroChapa->setProfissional(Profissional::newInstance([
            'id' => $profissionalTO->getId()
        ]));
        $membroChapa->setSituacaoResponsavel($isResponsavel);

        $idStatusParticipacaoChapa = ($isAcessorCEN)
            ? Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
            : Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR;

        $membroChapa->setStatusParticipacaoChapa(StatusParticipacaoChapa::newInstance([
            "id" => $idStatusParticipacaoChapa
        ]));

        $membroChapa->setSituacaoMembroChapa(SituacaoMembroChapa::newInstance([
            "id" => Constants::ST_MEMBRO_CHAPA_CADASTRADO
        ]));

        return $membroChapa;
    }

    /**
     * Responsável por salvar as pendências de um membro da chapa
     *
     * @param MembroChapa $membroChapa
     * @param ProfissionalTO $profissionalTO
     *
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarPendenciasMembro(MembroChapa $membroChapa, $profissionalTO = null)
    {
        $pendencias = $this->getMembroChapaPendenciasAtualizadas($membroChapa, $profissionalTO);

        $membroChapaPendenciasSalvo = $this->getMembroChapaPendenciaBO()->salvarMembroChapaPendencias(
            $membroChapa, $pendencias
        );

        $idStatusValidacaoMembroChapa = (empty($pendencias))
            ? Constants::STATUS_VALIDACAO_MEMBRO_SEM_PENDENCIA
            : Constants::STATUS_VALIDACAO_MEMBRO_PENDENTE;

        $this->salvarStatusValidacaoMembroChapa($membroChapa, $idStatusValidacaoMembroChapa);

        $membroChapa->setPendencias($membroChapaPendenciasSalvo);
    }

    /**
     * Retorna uma lista de MembroChapaPendencia atualizada
     *
     * @param MembroChapa $membroChapa
     * @param ProfissionalTO|Profissional|null $profissional
     * @return array
     * @throws NegocioException
     * @throws Exception
     */
    private function getMembroChapaPendenciasAtualizadas(MembroChapa $membroChapa, $profissional = null): array
    {
        $pendencias = [];

        if (empty($profissional)) {
            $profissional = $this->getProfissionalBO()->getPorId($membroChapa->getProfissional()->getId(), true);
        }

        /* ID_TP_PENDENCIA = 11 */
        $isConselheiroSubsequente = $profissional->getConselheiroSubsequente()->situacao ?? false;
        $cargo = $profissional->getConselheiroSubsequente()->cargo ?? null;

        if (!empty($cargo)) {
            if ($cargo == Constants::DS_REPRESENTACAO_FEDERAL) {
                $cargo = Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL;
            } else if ($cargo == Constants::DS_REPRESENTACAO_ESTADUAL) {
                $cargo = Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL;
            }
        }

        if ($isConselheiroSubsequente and $membroChapa->getTipoMembroChapa()->getId() == $cargo) {
            $pendencias[] = TipoPendencia::newInstance([
                "id" => Constants::PENDENCIA_CONSELHEIRO_MANDATO_SUBSEQUENTE
            ]);
        }

        /* ID_TP_PENDENCIA = 9 */
        $isSancionado = $profissional->getSancionadoInfracaoEticaDisciplinar()->situacao ?? false;
        if ($isSancionado) {
            $dataReabilitacao = $profissional->getSancionadoInfracaoEticaDisciplinar()->dataReabilitacao;
            if (!empty($dataReabilitacao)) {
                $transcursoReabilitacao = Utils::adicionarAnosToData(
                    Utils::getDataToString($dataReabilitacao, 'Y-m-d'), 3
                );
                if ($transcursoReabilitacao >= Utils::getDataHoraZero()) {
                    $pendencias[] = TipoPendencia::newInstance([
                        "id" => Constants::PENDENCIA_INFRACAO_ETICO_DISCIPLINAR
                    ]);
                }
            }
        }

        /* ID_TP_PENDENCIA = 8 */
        if ($profissional->isInfracaoEtica()) {
            $pendencias[] = TipoPendencia::newInstance([
                "id" => Constants::PENDENCIA_SANCAO_ETICO_DISCIPLINAR
            ]);
        }

        /* ID_TP_PENDENCIA = 5 */
        $isPendenciaDecorrerProcesso = $this->hasTipoPendencia(
            $membroChapa, Constants::PENDENCIA_MEMBRO_CHAPA_DECORRER_PROCESSO_ELEITORAL
        );

        if ($isPendenciaDecorrerProcesso) {
            $pendencias[] = TipoPendencia::newInstance([
                "id" => Constants::PENDENCIA_MEMBRO_CHAPA_DECORRER_PROCESSO_ELEITORAL
            ]);
        }

        /* ID_TP_PENDENCIA = 10 */
        $isDevedorMultaProcessoEleitoral = $profissional->isMultaProcessoEleitoral() ?? false;
        if ($isDevedorMultaProcessoEleitoral) {
            $pendencias[] = TipoPendencia::newInstance([
                "id" => Constants::PENDENCIA_MULTA_ELEITORAL
            ]);
        }

        /* ID_TP_PENDENCIA = 4 */
        if (!$profissional->isAdimplente()) {
            $pendencias[] = TipoPendencia::newInstance([
                "id" => Constants::PENDENCIA_REGISTRO_INADIPLENTE
            ]);
        }

        /* ID_TP_PENDENCIA = 6 */
        if ($profissional->isInfracaoRelacionadaExercicioProfissao()) {
            $pendencias[] = TipoPendencia::newInstance([
                "id" => Constants::PENDENCIA_MULTA_FISCALIZACAO
            ]);
        }

        /* ID_TP_PENDENCIA = 7 */
        if ($profissional->isMultaEtica()) {
            $pendencias[] = TipoPendencia::newInstance([
                "id" => Constants::PENDENCIA_MULTA_ETICA
            ]);
        }

        /* ID_TP_PENDENCIA = 1 */
        if ($profissional->getSituacaoRegistro()->id != Constants::SITUACAO_REGISTRO_PROFISSIONAL_ATIVO) {
            $pendencias[] = TipoPendencia::newInstance(["id" => Constants::PENDENCIA_REGISTRO_NAO_ATIVO]);
        }

        /* ID_TP_PENDENCIA = 3 */
        if ($profissional->isRegistroProvisorio()) {
            $pendencias[] = TipoPendencia::newInstance([
                "id" => Constants::PENDENCIA_REGISTRO_PROVISORIO
            ]);
        }

        /* ID_TP_PENDENCIA = 2 */
        $stringDataFimRegistro = $profissional->getDataFimRegistro();
        $dataFimRegistro = (!empty($stringDataFimRegistro)) ?
            Utils::getDataToString($stringDataFimRegistro, 'Y-m-d')
            : null;

        if (!empty($dataFimRegistro) && Utils::getDataHoraZero($dataFimRegistro) < Utils::getDataHoraZero()) {
            $pendencias[] = TipoPendencia::newInstance([
                "id" => Constants::PENDENCIA_REGISTRO_DATA_VALIDADE_EXPIRADA
            ]);
        }

        /* ID_TP_PENDENCIA = 12 */
        $isPerdaMandato = $profissional->getPerdaMandatoConselheiro()->situacao ?? false;
        $dataPerdaMandato = $profissional->getPerdaMandatoConselheiro()->dataPerdaMandato ?? null;
        if ($isPerdaMandato) {
            /** @var DateTime $dataAcrescentadoCincoAnos */
            $dataAcrescentadoCincoAnos = Utils::adicionarAnosToData(
                Utils::getDataToString($dataPerdaMandato, 'Y-m-d'), 5
            );

            $anoEleicao = $this->getChapaEleicaoRepository()->getAnoEleicaoChapa($membroChapa->getChapaEleicao()->getId());
            if ($dataAcrescentadoCincoAnos->format('Y') >= $anoEleicao) {
                $pendencias[] = TipoPendencia::newInstance([
                    "id" => Constants::PENDENCIA_PERDEU_MANDATO_ULTIMOS_CINCO_ANOS
                ]);
            }
        }

        $membroChapaPendencias = [];
        if (!empty($pendencias)) {
            $membroChapaPendencias = array_map(static function ($tipoPendencia) use ($membroChapa) {
                $membroChapaPendencia = MembroChapaPendencia::newInstance();
                $membroChapaPendencia->setTipoPendencia($tipoPendencia);
                $membroChapaPendencia->setMembroChapa($membroChapa);

                return $membroChapaPendencia;
            }, $pendencias);
        }

        return $membroChapaPendencias;
    }

    /**
     * Método auxiliar do método incluirMembroChapa para salvar o histórico da chapa
     *
     * @param ChapaEleicao $chapaEleicao
     * @param MembroChapa $membroSalvo
     * @param string $origem
     * @param string|null $cpfAnterior
     * @param string|null $justificativa
     *
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    private function salvarHistoricoIncluirMembroChapa(
        ChapaEleicao $chapaEleicao,
        MembroChapa $membroSalvo,
        $origem,
        $cpfAnterior = null,
        $justificativa = null
    )
    {
        if ($chapaEleicao->getIdEtapa() == Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA) {
            if (empty($cpfAnterior)) {
                $descricaoAcao = sprintf(
                    Constants::HISTORICO_INCLUSAO_CPF_CHAPA,
                    $membroSalvo->getProfissional()->getCpf()
                );
            } else {

                $descricaoAcao = sprintf(
                    Constants::HISTORICO_SUBSTITUICAO_CPF_CHAPA,
                    $cpfAnterior,
                    $membroSalvo->getProfissional()->getCpf()
                );
            }

            $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
                $membroSalvo->getChapaEleicao(),
                $origem,
                $descricaoAcao,
                $justificativa
            );
            $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);
        }
    }

    /**
     * Consulta membro para ser substituto
     *
     * @param integer $idChapaEleicao
     * @param MembroChapaSubstituicaoTO $membroChapaSubstituicaoTO
     *
     * @return MembroChapa|null
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function consultarMembroParaSubstituto($idChapaEleicao, MembroChapaSubstituicaoTO $membroChapaSubstituicaoTO)
    {
        $chapaEleicao = $this->getChapaEleicaoRepository()->getPorId($idChapaEleicao);

        return $this->prepararSubstituto($chapaEleicao, $membroChapaSubstituicaoTO);
    }

    /**
     * Consulta membro para ser substituto
     *
     * @param ChapaEleicao $chapaEleicao
     * @param MembroChapaSubstituicaoTO $membroChapaSubstituicaoTO
     *
     * @return MembroChapa|null
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function prepararSubstituto($chapaEleicao, MembroChapaSubstituicaoTO $membroChapaSubstituicaoTO)
    {
        $membroChapa = $this->verificarProfissionalPodeSerSubstitutoChapa(
            $chapaEleicao->getId(),
            $membroChapaSubstituicaoTO,
            $chapaEleicao->getAtividadeSecundariaCalendario()->getId()
        );

        $profissional = $this->getProfissionalBO()->getPorId(
            $membroChapaSubstituicaoTO->getIdProfissional(), true, false
        );

        if (empty($membroChapa)) {
            $idTipoMembroTO = $membroChapaSubstituicaoTO->getIdTipoMembro();
            $this->validarImpedimentosIncluirMembro($chapaEleicao, $profissional, $idTipoMembroTO, true);

            $membroChapa = MembroChapa::newInstance([
                'idProfissional' => $membroChapaSubstituicaoTO->getIdProfissional(),
                'numeroOrdem' => $membroChapaSubstituicaoTO->getNumeroOrdem(),
                'situacaoResponsavel' => false,
            ]);

            $tipoMembro = $this->getTipoMembroChapaRepository()->getPorId($membroChapaSubstituicaoTO->getIdTipoMembro());
            $tipoParticipacao = $this->getTipoParticipacaoChapaRepository()->getPorId(
                $membroChapaSubstituicaoTO->getIdTipoParticipacaoChapa()
            );
            $statusParticipacao = $this->getStatusParticipacaoChapaRepository()->getPorId(
                Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR
            );

            $pendencias = $this->getMembroChapaPendenciasAtualizadas($membroChapa, $profissional);

            $pendencias = array_map(function ($pendencia) {
                /**
                 * @var MembroChapaPendencia $pendencia
                 * @var TipoPendencia $tipoPendencia
                 */
                $tipoPendencia = $this->getTipoPendenciaRepository()->find($pendencia->getTipoPendencia()->getId());
                $pendencia->setTipoPendencia($tipoPendencia);
                return $pendencia;
            }, $pendencias);

            $idStatusValidacao = empty($pendencias)
                ? Constants::STATUS_VALIDACAO_MEMBRO_SEM_PENDENCIA
                : Constants::STATUS_VALIDACAO_MEMBRO_PENDENTE;
            $statusValidacao = $this->getStatusValidacaoMembroChapaRepository()->getPorId($idStatusValidacao);

            $membroChapa->setPendencias($pendencias);
            $membroChapa->setTipoMembroChapa($tipoMembro);
            $membroChapa->setTipoParticipacaoChapa($tipoParticipacao);
            $membroChapa->setStatusParticipacaoChapa($statusParticipacao);
            $membroChapa->setStatusValidacaoMembroChapa($statusValidacao);
        }
        $membroChapa->setProfissional($profissional);

        return $membroChapa;
    }

    /**
     * Método que verifica se membro pode ser substituto chapa, se for o mesmo membro da posição atual a ser substituido
     * o mesmo é retornado, caso contrário retorna null
     *
     * @param $idChapaEleicao
     * @param MembroChapaSubstituicaoTO $membroChapaSubstituicaoTO
     * @param $idAtividadeSecundariaChapa
     * @return MembroChapa|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function verificarProfissionalPodeSerSubstitutoChapa(
        $idChapaEleicao,
        MembroChapaSubstituicaoTO $membroChapaSubstituicaoTO,
        $idAtividadeSecundariaChapa
    )
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        if (!$this->isMembroResponsavelChapa($idChapaEleicao, $usuarioLogado->idProfissional)) {
            throw new NegocioException(Message::MSG_VISUALIZACAO_ATIV_APENAS_MEMBROS_RESPONSAVEIS_CHAPA);
        }

        if (empty($membroChapaSubstituicaoTO->getIdProfissional())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($membroChapaSubstituicaoTO->getIdTipoMembro())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($membroChapaSubstituicaoTO->getIdTipoParticipacaoChapa())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (is_null($membroChapaSubstituicaoTO->getNumeroOrdem())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $membroChapa = $this->membroChapaRepository->getMembroChapaPorProfissional(
            $idChapaEleicao,
            $membroChapaSubstituicaoTO->getIdProfissional(),
            [
                Constants::ST_MEMBRO_CHAPA_CADASTRADO,
                Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO,
                Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO
            ]
        );
        $idTipoParticipacaoTO = $membroChapaSubstituicaoTO->getIdTipoParticipacaoChapa();

        $isMembroAtual = (
            !empty($membroChapa)
            && $membroChapa->getTipoMembroChapa()->getId() == $membroChapaSubstituicaoTO->getIdTipoMembro()
            && $membroChapa->getTipoParticipacaoChapa()->getId() == $idTipoParticipacaoTO
            && $membroChapa->getNumeroOrdem() == $membroChapaSubstituicaoTO->getNumeroOrdem()
        );

        if (!empty($membroChapa) && !$isMembroAtual) {
            throw new NegocioException(Message::MSG_CPF_JA_INCLUIDO_CHAPA);
        }

        if (!$isMembroAtual) {
            $totalConvitesConfirmadados = $this->membroChapaRepository->totalConvitePorStatusParticipacao(
                $membroChapaSubstituicaoTO->getIdProfissional(),
                $idAtividadeSecundariaChapa,
                Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
            );
            if ($totalConvitesConfirmadados > 0) {
                throw new NegocioException(Message::MSG_CPF_ACEITOU_PARTIC_OUTRA_CHAPA);
            }
            return $membroChapa;
        }
    }

    /**
     * Retorna se um membro é responsável (Deve ter aceito o convite, se não for o profissional que criou a chapa)
     *
     * @param $idChapaEleicao
     * @param $idProfissional
     * @return bool
     * @throws Exception
     */
    public function isMembroResponsavelChapa($idChapaEleicao, $idProfissional)
    {
        $membroChapa = $this->membroChapaRepository->getMembroChapaPorProfissional(
            $idChapaEleicao,
            $idProfissional,
            [Constants::ST_MEMBRO_CHAPA_CADASTRADO, Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO]
        );

        $isResponsavel = false;

        if (
            !empty($membroChapa) &&
            $membroChapa->isSituacaoResponsavel() &&
            $membroChapa->getStatusParticipacaoChapa()->getId() == Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
        ) {
            $isResponsavel = true;
        }
        return $isResponsavel;
    }

    /**
     * Retorna uma nova instância de 'TipoMembroChapaRepository'.
     *
     * @return TipoMembroChapaRepository
     */
    private function getTipoMembroChapaRepository()
    {
        if (empty($this->tipoMembroChapaRepository)) {
            $this->tipoMembroChapaRepository = $this->getRepository(TipoMembroChapa::class);
        }

        return $this->tipoMembroChapaRepository;
    }

    /**
     * Retorna uma nova instância de 'TipoParticipacaoChapaRepository'.
     *
     * @return TipoParticipacaoChapaRepository
     */
    private function getTipoParticipacaoChapaRepository()
    {
        if (empty($this->tipoParticipacaoChapaRepository)) {
            $this->tipoParticipacaoChapaRepository = $this->getRepository(TipoParticipacaoChapa::class);
        }

        return $this->tipoParticipacaoChapaRepository;
    }

    /**
     * Retorna uma nova instância de 'StatusParticipacaoChapaRepository'.
     *
     * @return StatusParticipacaoChapaRepository
     */
    private function getStatusParticipacaoChapaRepository()
    {
        if (empty($this->statusParticipacaoChapaRepository)) {
            $this->statusParticipacaoChapaRepository = $this->getRepository(StatusParticipacaoChapa::class);
        }

        return $this->statusParticipacaoChapaRepository;
    }

    /**
     * Retorna uma nova instância de 'TipoPendenciaRepository'.
     *
     * @return TipoPendenciaRepository
     */
    private function getTipoPendenciaRepository()
    {
        if (empty($this->tipoPendenciaRepository)) {
            $this->tipoPendenciaRepository = $this->getRepository(TipoPendencia::class);
        }

        return $this->tipoPendenciaRepository;
    }

    /**
     * Retorna uma nova instância de 'StatusValidacaoMembroChapaRepository'.
     *
     * @return StatusValidacaoMembroChapaRepository
     */
    private function getStatusValidacaoMembroChapaRepository()
    {
        if (empty($this->statusValidacaoMembroChapaRepository)) {
            $this->statusValidacaoMembroChapaRepository = $this->getRepository(
                StatusValidacaoMembroChapa::class
            );
        }

        return $this->statusValidacaoMembroChapaRepository;
    }

    /**
     *  Altera situação de responsável do membro pelo id informado.
     *
     * @param integer $id
     * @param stdClass $dadosTO
     *
     * @return MembroChapa|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function alterarSituacaoResponsavel($id, $dadosTO)
    {
        $membroChapa = $this->membroChapaRepository->getPorId($id);

        $isAcessorCEN = $this->getUsuarioFactory()->hasPermissao(Constants::PERMISSAO_ACESSOR_CEN);

        $this->validarAlterarSituacaoResponsavel($membroChapa, $dadosTO, $isAcessorCEN);

        try {
            $this->beginTransaction();

            $membroChapa->setSituacaoResponsavel($dadosTO->situacaoResponsavel);
            $membroSalvo = $this->membroChapaRepository->persist($membroChapa);

            $descricaoHistorico = Constants::HISTORICO_INCLUSAO_RESPONSAVEL_CHAPA;
            if (!$dadosTO->situacaoResponsavel) {
                $descricaoHistorico = Constants::HISTORICO_EXCLUSAO_RESPONSAVEL_CHAPA;
            }

            $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
                $membroChapa->getChapaEleicao(),
                $isAcessorCEN ? Constants::ORIGEM_CORPORATIVO : Constants::ORIGEM_PROFISSIONAL,
                sprintf($descricaoHistorico, $membroChapa->getProfissional()->getCpf()),
                $dadosTO->justificativa
            );
            $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        return $this->getPorId($membroSalvo->getId());
    }

    /**
     * Validações os dados para alterar a situação de responsável de um membro da chapa
     *
     * @param MembroChapa $membroChapa
     * @param stdClass $dadosTO
     * @param bool $isAcessorCEN
     *
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function validarAlterarSituacaoResponsavel(MembroChapa $membroChapa, $dadosTO, $isAcessorCEN)
    {

        if (
            empty($membroChapa)
            or ($membroChapa->getProfissional()->getId() == $membroChapa->getChapaEleicao()->getIdProfissionalInclusao()
                and !$isAcessorCEN)
        ) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        if (
            !isset($dadosTO->situacaoResponsavel) ||
            $membroChapa->isSituacaoResponsavel() == $dadosTO->situacaoResponsavel
        ) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

        $totalResponsaveis = $this->membroChapaRepository->totalMembrosResponsaveisChapa(
            $membroChapa->getChapaEleicao()->getId()
        );

        if ($dadosTO->situacaoResponsavel && $totalResponsaveis >= 3) {
            throw new NegocioException(Message::MSG_PERMITIDO_ATE_TRES_RESPONSAVEIS_CHAPA, [], true);
        }

        if (!$dadosTO->situacaoResponsavel && $totalResponsaveis == 1) {
            throw new NegocioException(Message::MSG_CHAPA_DEVE_TER_MINIMO_UM_RESPONSAVEL, [], true);
        }

        if (empty($dadosTO->justificativa)) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
        }
    }

    /**
     * Retorna membro chapa conforme o id informado.
     *
     * @param $id
     *
     * @return MembroChapa|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getPorId($id)
    {
        return $this->membroChapaRepository->getPorId($id);
    }

    /**
     *  Envia e-mail de pendências para o membro de id informado.
     *
     * @param integer $id
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function enviarEmailPendencias($id)
    {
        $membroChapa = $this->membroChapaRepository->getPorId($id);

        $isAcessorCEN = $this->getUsuarioFactory()->hasPermissao(Constants::PERMISSAO_ACESSOR_CEN);
        $isAcessorCauUf = $this->getUsuarioFactory()->hasPermissao(Constants::PERMISSAO_ACESSOR_CE_UF);
        $isAcessorCauUfChapa = $this->isAcessorCauUfChapa($membroChapa, $isAcessorCauUf);

        if (!$isAcessorCEN && $isAcessorCauUf && !$isAcessorCauUfChapa) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
            $membroChapa->getChapaEleicao(),
            $this->getUsuarioFactory()->isProfissional() ? Constants::ORIGEM_PROFISSIONAL : Constants::ORIGEM_CORPORATIVO,
            sprintf(
                Constants::HISTORICO_ENVIO_EMAIL_PENDENCIA_MEMBRO_CHAPA,
                $membroChapa->getProfissional()->getCpf()
            )
        );

        $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);

        Utils::executarJOB(new EnviarEmailPendenciasMembroChapaJob($id));
    }

    /**
     * @param MembroChapa|null $membroChapa
     * @return bool
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function isAcessorCauUfChapa(?MembroChapa $membroChapa, $isAcessorCauUf): bool
    {
        $idCauUfChapa = $membroChapa->getChapaEleicao()->getIdCauUf();
        $idCauUfUsuario = $this->getUsuarioFactory()->getUsuarioLogado()->idCauUf ?? null;
        $isAcessorCauUfChapa = ($isAcessorCauUf && $idCauUfChapa == $idCauUfUsuario);
        return $isAcessorCauUfChapa;
    }

    /**
     * Método auxiliar que prepara os parâmetros do e-mail de pendências e efetiva o envio
     *
     * @param $id
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function prepararParamsEmailPendenciasEfetivarEnvio($id)
    {
        $membroChapa = $this->membroChapaRepository->getPorId($id);

        $parametrosEmail = [
            Constants::PARAMETRO_EMAIL_ACEITE_CONVITE => '',
            Constants::$paramsEmailPendenciasMembroChapa[Constants::PENDENCIA_REGISTRO_NAO_ATIVO] => '',
            Constants::$paramsEmailPendenciasMembroChapa[Constants::PENDENCIA_REGISTRO_PROVISORIO] => '',
            Constants::$paramsEmailPendenciasMembroChapa[Constants::PENDENCIA_REGISTRO_INADIPLENTE] => '',
            Constants::$paramsEmailPendenciasMembroChapa[Constants::PENDENCIA_REGISTRO_DATA_VALIDADE_EXPIRADA] => '',
            Constants::$paramsEmailPendenciasMembroChapa[Constants::PENDENCIA_MEMBRO_CHAPA_DECORRER_PROCESSO_ELEITORAL] => '',
            Constants::$paramsEmailPendenciasMembroChapa[Constants::PENDENCIA_MULTA_FISCALIZACAO] => '',
            Constants::$paramsEmailPendenciasMembroChapa[Constants::PENDENCIA_MULTA_ETICA] => '',
            Constants::$paramsEmailPendenciasMembroChapa[Constants::PENDENCIA_SANCAO_ETICO_DISCIPLINAR] => '',
            Constants::$paramsEmailPendenciasMembroChapa[Constants::PENDENCIA_INFRACAO_ETICO_DISCIPLINAR] => '',
            Constants::$paramsEmailPendenciasMembroChapa[Constants::PENDENCIA_MULTA_ELEITORAL] => '',
            Constants::$paramsEmailPendenciasMembroChapa[Constants::PENDENCIA_CONSELHEIRO_MANDATO_SUBSEQUENTE] => '',
            Constants::$paramsEmailPendenciasMembroChapa[Constants::PENDENCIA_PERDEU_MANDATO_ULTIMOS_CINCO_ANOS] => '',
        ];

        if ($membroChapa->getStatusParticipacaoChapa()->getId() == Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR) {
            $parametrosEmail[Constants::PARAMETRO_EMAIL_ACEITE_CONVITE] = Constants::DS_PENDENCIA_ACEITE_CONVITE;
            $parametrosEmail[Constants::PARAMETRO_EMAIL_ACEITE_CONVITE] .= Constants::QUEBRA_LINHA_TEMPLATE_EMAIL;
        }

        if ($membroChapa->getStatusValidacaoMembroChapa()->getId() == Constants::STATUS_VALIDACAO_MEMBRO_PENDENTE) {
            $pendencias = $this->getMembroChapaPendenciaRepository()->getPorMembroChapa($membroChapa->getId());

            if (!empty($pendencias)) {
                foreach ($pendencias as $pendencia) {
                    $idPendencia = $pendencia->getTipoPendencia()->getId();
                    $indiceParam = Constants::$paramsEmailPendenciasMembroChapa[$idPendencia];
                    $parametrosEmail[$indiceParam] = $pendencia->getTipoPendencia()->getDescricao();
                    $parametrosEmail[$indiceParam] .= Constants::QUEBRA_LINHA_TEMPLATE_EMAIL;
                }
            }
        }

        if (!empty($parametrosEmail)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $membroChapa->getChapaEleicao()->getAtividadeSecundariaCalendario()->getId(),
                Constants::EMAIL_MEMBRO_CHAPA_COM_PENDENCIA
            );

            if (!is_null($emailAtividadeSecundaria)) {
                $destinatarioEmail = $membroChapa->getProfissional()->getPessoa()->getEmail();

                $this->getEmailAtividadeSecundariaBO()->enviarEmailAtividadeSecundaria(
                    $emailAtividadeSecundaria,
                    [$destinatarioEmail],
                    Constants::TEMPLATE_EMAIL_PENDENCIAS_MEMBRO_CHAPA,
                    $parametrosEmail
                );
            }
        }
    }

    /**
     * Retorna uma nova instância de 'MembroChapaPendenciaRepository'.
     *
     * @return MembroChapaPendenciaRepository
     */
    private function getMembroChapaPendenciaRepository()
    {
        if (empty($this->membroChapaPendenciaRepository)) {
            $this->membroChapaPendenciaRepository = $this->getRepository(MembroChapaPendencia::class);
        }

        return $this->membroChapaPendenciaRepository;
    }

    /**
     *  Responsável por atualizar as pendências dos membros.
     *
     * @param $idChapaEleicao
     *
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function atualizarPendenciasMembro($idChapaEleicao)
    {
        $membros = $this->membroChapaRepository->findBy([
            'chapaEleicao' => $idChapaEleicao
        ]);

        if (!empty($membros)) {
            $idsProfissionais = $this->getIdsProfissionais($membros);

            $profissionaisTO = $this->getProfissionalBO()->getListaProfissionaisFormatadaPorIds(
                $idsProfissionais,
                true
            );

            /** @var MembroChapa $membro */
            foreach ($membros as $membro) {
                $situacoesValidasAtuais = [
                    Constants::ST_MEMBRO_CHAPA_CADASTRADO,
                    Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO
                ];

                if (in_array($membro->getSituacaoMembroChapa()->getId(), $situacoesValidasAtuais)) {
                    $profissionalTO = Utils::getValue($membro->getProfissional()->getId(), $profissionaisTO);

                    $this->salvarPendenciasMembro($membro, $profissionalTO);
                }
            }
        }
    }

    /**
     * Retorna uma lista de profissionais a partir de uma lista de membros chapa
     *
     * @param $membrosChapa
     * @return array
     */
    public function getIdsProfissionais($membrosChapa): array
    {
        $idsProfissionais = array_map(function ($membroChapa) {
            /** @var MembroChapa $membroChapa */
            return $membroChapa->getProfissional()->getId();
        }, $membrosChapa);
        return $idsProfissionais;
    }

    /**
     * Método auxiliar para remover um membro chapa pelo ID informado
     *
     * @param $idMembroChapa
     * @param $justificativa
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function excluirMembroChapa($idMembroChapa, $justificativa)
    {
        $isAcessorCEN = $this->getUsuarioFactory()->hasPermissao(Constants::PERMISSAO_ACESSOR_CEN);

        if (!$isAcessorCEN) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if (empty($justificativa)) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        try {
            $this->beginTransaction();

            /** @var MembroChapa $membroChapa */
            $membroChapa = $this->membroChapaRepository->find($idMembroChapa);

            $totalResponsaveis = $this->membroChapaRepository->totalMembrosResponsaveisChapa(
                $membroChapa->getChapaEleicao()->getId()
            );

            if ($membroChapa->isSituacaoResponsavel() && $totalResponsaveis == 1) {
                throw new NegocioException(Lang::get('messages.membro_chapa.exclusao_unico_responsavel'));
            }

            $idChapaEleicao = $membroChapa->getChapaEleicao()->getId();

            if (!empty($membroChapa)) {
                $this->getMembroChapaPendenciaBO()->excluirMembroChapaPendencias($membroChapa);
                $this->getDocumentoComprobatorioSinteseCurriculoBO()->excluirDocumentosPorMembroChapa(
                    $membroChapa->getId()
                );
            }

            $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
                $membroChapa->getChapaEleicao(),
                Constants::ORIGEM_CORPORATIVO,
                sprintf(Constants::HISTORICO_EXCLUSAO_MEMBRO_CHAPA, $membroChapa->getProfissional()->getCpf()),
                $justificativa
            );
            $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);

            if (!empty($membroChapa->getRespostaDeclaracaoRepresentatividade())) {
                $lista = $this->getRespostaDeclaracaoRepresentatividadeBO()->excluirPorMembro($membroChapa->getId());
            } 

            $this->membroChapaRepository->delete($membroChapa);

            $this->alterarStatusChapaPosAlteracaoMembro($idChapaEleicao, Constants::SITUACAO_CHAPA_PENDENTE);

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Método auxiliar para remover um membro chapa pelo ID informado
     *
     * @param $idMembroChapa
     * @param $justificativa
     * @throws NegocioException
     * @throws Exception
     */
    public function excluirMembroChapaByResponsavelChapa($idMembroChapa)
    {
        /** @var MembroChapa $membroChapa */
        $membroChapa = $this->membroChapaRepository->find($idMembroChapa);

        if (!$this->getUsuarioFactory()->isProfissional() || empty($membroChapa)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $isResponsavel = $this->isMembroResponsavelChapa(
            $membroChapa->getChapaEleicao()->getId(),
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );

        if (!$isResponsavel) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        try {
            $this->beginTransaction();

            $totalResponsaveis = $this->membroChapaRepository->totalMembrosResponsaveisChapa(
                $membroChapa->getChapaEleicao()->getId()
            );

            if ($membroChapa->isSituacaoResponsavel() && $totalResponsaveis == 1) {
                throw new NegocioException(Lang::get('messages.membro_chapa.exclusao_unico_responsavel'));
            }

            $idChapaEleicao = $membroChapa->getChapaEleicao()->getId();

            if (!empty($membroChapa)) {
                $this->getMembroChapaPendenciaBO()->excluirMembroChapaPendencias($membroChapa);
                $this->getDocumentoComprobatorioSinteseCurriculoBO()->excluirDocumentosPorMembroChapa(
                    $membroChapa->getId()
                );
            }

            $historicoChapaEleicao = $this->getHistoricoChapaEleicaoBO()->criarHistorico(
                $membroChapa->getChapaEleicao(),
                Constants::ORIGEM_PROFISSIONAL,
                sprintf(Constants::HISTORICO_EXCLUSAO_MEMBRO_CHAPA, $membroChapa->getProfissional()->getCpf())
            );
            $this->getHistoricoChapaEleicaoBO()->salvar($historicoChapaEleicao);

            $this->getRespostaDeclaracaoRepresentatividadeBO()->excluirPorMembro($membroChapa->getId());

            $this->membroChapaRepository->delete($membroChapa);

            $this->alterarStatusChapaPosAlteracaoMembro($idChapaEleicao, Constants::SITUACAO_CHAPA_PENDENTE);

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Retorna uma nova instância de 'DocumentoComprobatorioSinteseCurriculoBO'.
     *
     * @return DocumentoComprobatorioSinteseCurriculoBO
     */
    private function getDocumentoComprobatorioSinteseCurriculoBO()
    {
        if (empty($this->documentoComprobatorioSinteseCurriculoBO)) {
            $this->documentoComprobatorioSinteseCurriculoBO = app()->make(
                DocumentoComprobatorioSinteseCurriculoBO::class
            );
        }

        return $this->documentoComprobatorioSinteseCurriculoBO;
    }

    /**
     * Retorna o destinatário do e-mail
     *
     * @param $idProfissional
     *
     * @return string|null
     * @throws NegocioException
     */
    public function getDestinatarioEmail($idProfissional)
    {
        $emailProfissional = null;
        if (!empty($idProfissional)) {
            $profissionais = $this->getProfissionalBO()->getListaProfissionaisFormatadaPorIds([$idProfissional]);

            if (!empty($profissionais)) {
                /** @var Profissional $profissional */
                $profissional = array_shift($profissionais);
                $emailProfissional = $profissional->getEmail();
            }
        }
        return $emailProfissional;
    }

    /**
     * Retorna a síntese do currículo e arquivos comprobatórios do membro chapa
     *
     * @param $idMembroChapa
     *
     * @return mixed
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function detalhar($idMembroChapa)
    {
        $membroChapa = $this->membroChapaRepository->getPorId($idMembroChapa);

        $documentosComprobatorios = $this->getDocumentoComprobatorioSinteseCurriculoRepository()->getPorMembro(
            $idMembroChapa
        );
        $fotoMembroChapa = null;

        if (empty($membroChapa->getNomeArquivoFoto())) {
            $diretorioSinteseCurriculo = $this->getArquivoService()->getCaminhoRepositorioSinteseCurriculo(sprintf(
                '%s/%s',
                $membroChapa->getChapaEleicao()->getId(),
                $membroChapa->getId()
            ));

            $nomeFoto = 'foto.jpg';

            $path = AppConfig::getRepositorio($diretorioSinteseCurriculo, $nomeFoto);
            $fotoMembroChapa = ImageUtils::getImageBase64($path);

            if (empty($fotoMembroChapa)){
                $nomeFoto = 'foto.png';
                $path = AppConfig::getRepositorio($diretorioSinteseCurriculo, $nomeFoto);
                $fotoMembroChapa = ImageUtils::getImageBase64($path);
            }

            if (empty($fotoMembroChapa)){
                $nomeFoto = 'foto.jpeg';
                $path = AppConfig::getRepositorio($diretorioSinteseCurriculo, $nomeFoto);
                $fotoMembroChapa = ImageUtils::getImageBase64($path);
            }

            if(!empty($fotoMembroChapa)){
                $membroChapa->setNomeArquivoFoto($nomeFoto);
            }
        }

        if (!empty($membroChapa->getNomeArquivoFoto())) {
            $diretorioSinteseCurriculo = $this->getArquivoService()->getCaminhoRepositorioSinteseCurriculo(sprintf(
                '%s/%s',
                $membroChapa->getChapaEleicao()->getId(),
                $membroChapa->getId()
            ));

            $path = AppConfig::getRepositorio($diretorioSinteseCurriculo, $membroChapa->getNomeArquivoFoto());
            $fotoMembroChapa = ImageUtils::getImageBase64($path);

        }
        if (empty($membroChapa->getNomeArquivoFoto()) || empty($fotoMembroChapa)) {
            $profissional = $this->getCorporativoService()->getProfissionalPorId(
                $membroChapa->getProfissional()->getId(), true
            );

            $fotoMembroChapa = "";
            if (!empty($profissional->possuiFoto) && $profissional->possuiFoto) {
                $fotoMembroChapa = $profissional->avatar;
            }
        }
        $membroChapaRetorno = MembroChapa::newInstance([
            'fotoMembroChapa' => $fotoMembroChapa,
            'sinteseCurriculo' => $membroChapa->getSinteseCurriculo() ?? ""
        ]);
        $membroChapaRetorno->setDocumentosComprobatoriosSinteseCurriculo($documentosComprobatorios);

        return $membroChapaRetorno;
    }

    /**
     * Retorna a instância do 'CorporativoService'.
     *
     * @return CorporativoService
     */
    private function getCorporativoService()
    {
        if ($this->corporativoService == null) {
            $this->corporativoService = app()->make(CorporativoService::class);
        }
        return $this->corporativoService;
    }

    /**
     * Retorna os membros de chapas por id cau Uf
     *
     * @param int $idCauUf
     */
    public function getMembrosChapasPorUf($idCauUf)
    {
        $membroChapaFiltroTO = MembroChapaFiltroTO::newInstance(['idCauUf' => $idCauUf]);
        $lista = $this->membroChapaRepository->getMembrosPorFiltro($membroChapaFiltroTO);

        if (!empty($lista)) {
            $lista = $this->organizeListaMembrosChapa($lista);
        }

        return $lista;
    }

    /**
     * Retorna os membros de chapas por id cau Uf
     *
     * @param stdClass $filtroTO
     */
    public function getMembrosChapasPorFiltro($filtroTO)
    {
        $membroChapaFiltroTO = MembroChapaFiltroTO::newInstance(['idCauUf' => $filtroTO->idCauUf]);
        $lista = $this->membroChapaRepository->getMembrosPorFiltro($membroChapaFiltroTO);

        if (!empty($lista)) {
            if (!empty($filtroTO->nomeRegistro)) {
                $novaListaMembros = array();
                foreach ($lista as $membro) {
                    $teste[] = $membro->getProfissional()->getNome();
                    if ((strpos(\strtolower(Utils::tirarAcentos($membro->getProfissional()->getNome())), \strtolower(Utils::tirarAcentos($filtroTO->nomeRegistro))) !== false)
                        or (strpos(\strtolower($membro->getProfissional()->getRegistroNacional()), \strtolower($filtroTO->nomeRegistro)) !== false)) {
                        $novaListaMembros[] = $membro;
                    }
                }
                $lista = $novaListaMembros;
            }
        }

        return $lista;
    }

    /**
     * Realiza a busca pelos membros para substituição pelo id do profissional
     * @param $idProfissional
     *
     * @return array|null
     * @throws NegocioException
     */
    public function getMembrosParaSubstituicao($idProfissional)
    {
        $chapaSubstituicao = $this->getChapaEleicaoBO()->getChapaParaSubstituicao();

        /** @var MembroChapa[] $membros */
        $membros = $this->membroChapaRepository->getMembrosTitularESuplentePorChapaIdProfissional(
            $chapaSubstituicao->getId(),
            $idProfissional
        );

        if (empty($membros)) {
            throw new NegocioException(Message::MSG_SUBSTITUICAO_APENAS_PROFISSIONAL_DA_CHAPA_DO_RESPONSAVEL);
        }

        $idPedido = $this->getPedidoSubstituicaoChapaRepository()->getIdPedidoSubstituicaoPorTipoMembroChapa(
            $chapaSubstituicao->getId(),
            $membros[0]->getTipoMembroChapa()->getId(),
            $membros[0]->getNumeroOrdem()
        );
        if (!empty($idPedido)) {
            throw new NegocioException(Message::MSG_MEMBRO_CHAPA_JA_TEM_PEDIDO_SUBSTITUICAO);
        }

        $membrosRetorno = ['titular' => '', 'suplente' => ''];

        /** @var MembroChapa $membro */
        foreach ($membros as $membro) {
            if ($membro->getTipoParticipacaoChapa()->getId() == Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR) {
                $membrosRetorno['titular'] = $membro;
            } else {
                $membrosRetorno['suplente'] = $membro;
            }
        }

        return $membrosRetorno;
    }

    /**
     * Realiza a busca pelo substituto pelo id do profissional para substituição no julgamento final
     * @param MembroChapaSubstituicaoTO $membroChapaSubstituicaoTO
     * @return MembroChapaTO|null
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function getMembroChapaSubstituto(MembroChapaSubstituicaoTO $membroChapaSubstituicaoTO)
    {
        $chapaSubstituicao = $this->getChapaEleicaoBO()->getPorId($membroChapaSubstituicaoTO->getIdChapaEleicao());
        if (empty($chapaSubstituicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $profissional = $this->getProfissionalBO()->getPorId(
            $membroChapaSubstituicaoTO->getIdProfissional(), true, false
        );

        if (empty($profissional)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $idTipoMembro = Constants::TIPO_MEMBRO_CHAPA_REPRESENTANTE_IES;
        if ($chapaSubstituicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR) {
            $idTipoMembro = $membroChapaSubstituicaoTO->getNumeroOrdem() == 0
                ? Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL
                : Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL;
        }

        $this->validarImpedimentosIncluirMembro(
            $chapaSubstituicao, $profissional, $idTipoMembro
        );

        $membroChapa = MembroChapa::newInstance([
            'idProfissional' => $membroChapaSubstituicaoTO->getIdProfissional()
        ]);

        $pendencias = $this->getMembroChapaPendenciasAtualizadas($membroChapa, $profissional);

        $pendencias = array_map(function ($pendencia) {
            /**
             * @var MembroChapaPendencia $pendencia
             * @var TipoPendencia $tipoPendencia
             */
            $tipoPendencia = $this->getTipoPendenciaRepository()->find($pendencia->getTipoPendencia()->getId());
            $pendencia->setTipoPendencia($tipoPendencia);
            return $pendencia;
        }, $pendencias);

        $idStatusValidacao = empty($pendencias)
            ? Constants::STATUS_VALIDACAO_MEMBRO_SEM_PENDENCIA
            : Constants::STATUS_VALIDACAO_MEMBRO_PENDENTE;
        $statusValidacao = $this->getStatusValidacaoMembroChapaRepository()->getPorId($idStatusValidacao);

        $membroChapa->setPendencias($pendencias);
        $membroChapa->setStatusValidacaoMembroChapa($statusValidacao);
        $membroChapa->setProfissional($profissional);

        return MembroChapaTO::newInstanceFromEntity($membroChapa);
    }

    /**
     * Realiza a busca pelos membros para substituição pelo id do profissional
     * @param $idProfissional
     *
     * @return MembroChapa|null
     * @throws NegocioException
     * @throws Exception
     */
    public function getMembroParaImpugnacao($idProfissional)
    {
        $eleicaoVigenteImpugnacao = $this->getChapaEleicaoBO()->getEleicaoVigenteCadastroImpugnacaoChapa();

        if (empty($eleicaoVigenteImpugnacao)) {
            throw new NegocioException(Message::MSG_PERIODO_CADASTRO_IMPUGNACAO_NAO_VIGENTE_PROCESSO_ELEITORAL);
        }

        /** @var MembroChapa $membroChapa */
        $membroChapa = $this->membroChapaRepository->getMembroConfirmadoPorCalendarioProfissioal(
            $eleicaoVigenteImpugnacao->getCalendario()->getId(),
            $idProfissional
        );

        if (empty($membroChapa)) {
            $profissional = $this->getProfissionalBO()->getPorId($idProfissional, false);
            throw new NegocioException(
                Message::MSG_PROFISSIONAL_NAO_PARTICIPA_NH_CHAPA__PROCESSO_ELEITORAL,
                [$profissional->getNome(), $eleicaoVigenteImpugnacao->getDescricao()],
                false
            );
        }

        $this->getPedidoImpugnacaoBO()->verificarDuplicacaoPedidoImpugnacao(
            $eleicaoVigenteImpugnacao->getCalendario()->getId(),
            $membroChapa->getId()
        );

        $membroChapa->getChapaEleicao()->definirStatusChapaVigente();
        $membroChapa->getChapaEleicao()->setChapaEleicaoStatus(null);

        $this->getChapaEleicaoBO()->definirFilialChapa($membroChapa->getChapaEleicao());

        return $membroChapa;
    }

    /**
     * Retorna uma nova instância de 'PedidoImpugnacaoBO'.
     *
     * @return PedidoImpugnacaoBO
     */
    private function getPedidoImpugnacaoBO()
    {
        if (empty($this->pedidoImpugnacaoBO)) {
            $this->pedidoImpugnacaoBO = app()->make(PedidoImpugnacaoBO::class);
        }

        return $this->pedidoImpugnacaoBO;
    }

    /**
     * @param MembroChapa $membroChapa
     * @return string|null
     */
    public function setArquivoFotoMembroChapa(MembroChapa $membroChapa, $idChapaEleicao)
    {
        if (!empty($membroChapa->getNomeArquivoFoto())) {
            $diretorioSinteseCurriculo = $this->getArquivoService()->getCaminhoRepositorioSinteseCurriculo(sprintf(
                '%s/%s',
                $idChapaEleicao,
                $membroChapa->getId()
            ));

            $path = AppConfig::getRepositorio($diretorioSinteseCurriculo, $membroChapa->getNomeArquivoFoto());

            $membroChapa->setFotoMembroChapa(ImageUtils::getImageBase64($path));
        }
    }

    /**
     * Método faz a persistência em lote de membros chapa
     *
     * @param $membroChapa
     * @param bool $isTransacion
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function persist($membroChapa, $isTransacion = true)
    {
        try {
            if ($isTransacion) {
                $this->beginTransaction();
            }

            $this->membroChapaRepository->persist($membroChapa);

            if ($isTransacion) {
                $this->commitTransaction();
            }
        } catch (OptimisticLockException|ORMException|\Exception $e) {
            if ($isTransacion) {
                $this->rollbackTransaction();
            }
            throw $e;
        }
    }

    /**
     * Método faz a persistência em lote de membros chapa
     *
     * @param $membrosChapa
     * @param bool $isTransacion
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function persistEmLote($membrosChapa, $isTransacion = true)
    {
        try {
            if ($isTransacion) {
                $this->beginTransaction();
            }

            $this->membroChapaRepository->persistEmLote($membrosChapa);

            if ($isTransacion) {
                $this->commitTransaction();
            }
        } catch (OptimisticLockException|ORMException|\Exception $e) {
            if ($isTransacion) {
                $this->rollbackTransaction();
            }
            throw $e;
        }
    }

    /**
     * Método auxiliar que prepara e envio e-mail alertando o membro incluido na chapa
     *
     * @param MembroChapa|null $membroChapa
     * @param stdClass $profissional
     * @param $idMembroChapa
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function enviarEmailMembroChapaIncluido($idMembroChapa): void
    {
        $membroChapa = $this->membroChapaRepository->getPorId($idMembroChapa);

        $isTitular = $membroChapa->getTipoParticipacaoChapa()->getId() == Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR;

        $tpParticipacaoMembroPar = $isTitular
            ? Constants::TIPO_PARTICIPACAO_CHAPA_SUPLENTE
            : Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR;

        $membroParPosicaoRegistro = $this->getMembroChapaPorTipoNumeroOrdem(
            $membroChapa->getChapaEleicao()->getId(),
            $membroChapa->getTipoMembroChapa()->getId(),
            $tpParticipacaoMembroPar,
            $membroChapa->getNumeroOrdem()
        );

        $this->getChapaEleicaoBO()->atribuirProfissionalChapa($membroChapa->getChapaEleicao());

        $responsavelInclusaoTO = $membroChapa->getChapaEleicao()->getProfissional();

        $profissional = $membroChapa->getProfissional();

        $profissionalPar = (!empty($membroParPosicaoRegistro)) ? $membroParPosicaoRegistro->getProfissional() : null;

        $descricoesTiposMembros = Constants::$descricoesTiposMembrosChapa[$membroChapa->getTipoMembroChapa()->getId()];
        $anoEleicao = $this->getChapaEleicaoRepository()->getAnoEleicaoChapa($membroChapa->getChapaEleicao()->getId());

        $nomeProfissionalPar = !empty($profissionalPar) && $profissionalPar->getNome() ? $profissionalPar->getNome() : '';

        $envioEmailMembroIncuidoChapaTO = EnvioEmailMembroIncuidoChapaTO::newInstance([
            'anoEleicao' => $anoEleicao,
            'nomeMembro' => $profissional->getNome(),
            'nomeResponsavel' => $responsavelInclusaoTO->getNome(),
            'posicao' => $membroChapa->getNumeroOrdem(),
            'emailDestinatario' => $profissional->getPessoa()->getEmail(),
            'nomeTitular' => ($isTitular) ? $profissional->getNome() : $nomeProfissionalPar,
            'nomeSuplente' => !($isTitular) ? $profissional->getNome() : $nomeProfissionalPar,
            'descricaoTitular' => $descricoesTiposMembros[Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR],
            'descricaoSuplente' => $descricoesTiposMembros[Constants::TIPO_PARTICIPACAO_CHAPA_SUPLENTE],
            'idAtividadeSecundaria' => $membroChapa->getChapaEleicao()->getAtividadeSecundariaCalendario()->getId()
        ]);

        $this->getChapaEleicaoBO()->enviarEmailMembroIncluidoChapa($envioEmailMembroIncuidoChapaTO);
    }

    /**
     * Método que verifica se membro pode ser substituto chapa, se for o mesmo membro da posição atual a ser substituido
     * o mesmo é retornado, caso contrário retorna null
     *
     * @param $idChapaEleicao
     * @param MembroChapa $membroChapaSubstituto
     * @param $idAtividadeSecundariaChapa
     * @return MembroChapa|null
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function verificarProfissionalPodeSerSubstitutoPorMembro(
        $idChapaEleicao,
        $idProfissional,
        MembroChapa $membroChapaSubstituto,
        $idAtividadeSecundariaChapa
    )
    {
        $membroChapaSubstituicaoValidarTO = MembroChapaSubstituicaoTO::newInstance([
            "idProfissional" => $idProfissional,
            "numeroOrdem" => $membroChapaSubstituto->getNumeroOrdem(),
            "idTipoMembro" => $membroChapaSubstituto->getTipoMembroChapa()->getId(),
            "idTipoParticipacaoChapa" => $membroChapaSubstituto->getTipoParticipacaoChapa()->getId()
        ]);
        return $this->verificarProfissionalPodeSerSubstitutoChapa(
            $idChapaEleicao,
            $membroChapaSubstituicaoValidarTO,
            $idAtividadeSecundariaChapa
        );
    }

    /**
     * Retorna responsáveis pela chapa conforme o id informado.
     *
     * @param $idChapa
     *
     * @return MembroChapa|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getResponsaveisChapaPorIdChapa($idChapa)
    {
        return $this->membroChapaRepository->getResponsaveisChapaPorIdChapa($idChapa);
    }

    /**
     * Retorna os nomes dos membros da chapa.
     *
     * @return array
     * @throws NegocioException
     */
    private function getNomesMembrosChapa($membrosChapas): array
    {
        $chapasMembros = [];

        $idsProfissionais = array_map(function ($membro) {
            return $membro['idProfissional'];
        }, $membrosChapas);

        $profissionais = $this->getProfissionalBO()->getListaProfissionaisFormatadaPorIds($idsProfissionais);

        foreach ($membrosChapas as $membroChapa) {
            /** @var Profissional $profissional */
            $profissional = Utils::getValue($membroChapa['idProfissional'], $profissionais);

            $chapasMembros[$membroChapa['idChapaEleicao']] = $profissional ? $profissional->getNome() : null;
        }

        return $chapasMembros;
    }

    /**
     * Retorna uma nova instância de 'ProfissionalBO'.
     *
     * @return ProfissionalBO|mixed
     */
    private function getProfissionalBO()
    {
        if (empty($this->profissionalBO)) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }

        return $this->profissionalBO;
    }

    /**
     * @param $membroSubstituido
     * @param $membrosSerAlterados
     * @param $membroSubstituto
     * @return mixed
     */
    private function processarAceiteConviteSubstituicao($membroSubstituido, $membroSubstituto, &$membrosSerAlterados)
    {
        if (!empty($membroSubstituido)) {
            $membroSubstituido->setSituacaoMembroChapa(SituacaoMembroChapa::newInstance([
                'id' => Constants::ST_MEMBRO_CHAPA_SUBSTITUIDO
            ]));
            array_push($membrosSerAlterados, $membroSubstituido);
        }

        if (!empty($membroSubstituto)) {

            $idSituacaoMembroChapa = Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO;

            $membroSubstituto->setStatusParticipacaoChapa(StatusParticipacaoChapa::newInstance([
                "id" => Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
            ]));

            $membroSubstituto->setSituacaoMembroChapa(SituacaoMembroChapa::newInstance([
                'id' => $idSituacaoMembroChapa
            ]));

            array_push($membrosSerAlterados, $membroSubstituto);
        }
    }


    /**
     * Retorna uma nova instância de 'SubstituicaoJulgamentoFinalBO'.
     *
     * @return SubstituicaoJulgamentoFinalBO|mixed
     */
    private function getSubstituicaoJulgamentoFinalBO()
    {
        if (empty($this->substituicaoJulgamentoFinalBO)) {
            $this->substituicaoJulgamentoFinalBO = app()->make(SubstituicaoJulgamentoFinalBO::class);
        }

        return $this->substituicaoJulgamentoFinalBO;
    }

    /**
     * Método provisório para setar e salvar a foto para membros que alteraram a foto
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function atualizarNomeFotoBancoDados()
    {
        $membrosChapa = $this->membroChapaRepository->getMembrosSemFoto();

        $nomeFotoJpg = 'foto.jpg';
        $nomeFotoJpeg = 'foto.jpeg';
        $nomeFotoPng = 'foto.png';

        /** @var MembroChapa $membroChapa */
        foreach ($membrosChapa as $membroChapa) {

            $diretorioSinteseCurriculo = $this->getArquivoService()->getCaminhoRepositorioSinteseCurriculo(sprintf(
                '%s/%s',
                $membroChapa->getChapaEleicao()->getId(),
                $membroChapa->getId()
            ));

            $pathJpg = AppConfig::getRepositorio($diretorioSinteseCurriculo, $nomeFotoJpg);
            $pathJpeg = AppConfig::getRepositorio($diretorioSinteseCurriculo, $nomeFotoJpeg);
            $pathPng = AppConfig::getRepositorio($diretorioSinteseCurriculo, $nomeFotoPng);

            if ($this->getArquivoService()->fileExiste($pathJpg)) {
                $membroChapa->setNomeArquivoFoto($nomeFotoJpg);
                $this->membroChapaRepository->persist($membroChapa);

                echo "Membro id {$membroChapa->getId()} da chapa id {$membroChapa->getChapaEleicao()->getId()} alterado!<br>";

            } else if ($this->getArquivoService()->fileExiste($pathJpeg)) {
                $membroChapa->setNomeArquivoFoto($nomeFotoJpeg);
                $this->membroChapaRepository->persist($membroChapa);

                echo "Membro id {$membroChapa->getId()} da chapa id {$membroChapa->getChapaEleicao()->getId()} alterado!<br>";

            } else if ($this->getArquivoService()->fileExiste($pathPng)) {
                $membroChapa->setNomeArquivoFoto($nomeFotoPng);
                $this->membroChapaRepository->persist($membroChapa);

                echo "Membro id {$membroChapa->getId()} da chapa id {$membroChapa->getChapaEleicao()->getId()} alterado!<br>";

            }
            else {
                $profissional = $this->getCorporativoService()->getProfissionalPorId(
                    $membroChapa->getProfissional()->getId(), true
                );

                $fotoMembroChapa = "";
                if (!empty($profissional->possuiFoto) && $profissional->possuiFoto) {
                    $fotoMembroChapa = $profissional->avatar;
                }

                $nomeArquivoFoto = "";
                $diretorioSinteseCurriculo = $this->getArquivoService()->getCaminhoRepositorioSinteseCurriculo(sprintf(
                    '%s/%s', $membroChapa->getChapaEleicao()->getId(), $membroChapa->getId()));

                if (!empty($fotoMembroChapa)) {
                    $extension = explode('/', mime_content_type($fotoMembroChapa))[1];
                    $nomeArquivoFoto = sprintf('%s.%s', self::NOME_ARQUIVO_FOTO, $extension);

                    $membroChapa->setNomeArquivoFoto($nomeArquivoFoto);
                    $this->membroChapaRepository->persist($membroChapa);
                    $this->getArquivoService()->salvarBase64ToArquivo($fotoMembroChapa, $diretorioSinteseCurriculo,
                        $nomeArquivoFoto, $extension);

                    echo "Membro id {$membroChapa->getId()} da chapa id {$membroChapa->getChapaEleicao()->getId()} alterado!<br>";
                }
            }
        }
    }

    /**
     * Método provisório para setar e salvar a foto para membros que alteraram a foto
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function atualizarSinteseCurriculo()
    {
        $membrosChapa = $this->membroChapaRepository->getMembrosConfirmados();

        /** @var MembroChapa $membroChapa */
        foreach ($membrosChapa as $membroChapa) {
            if (Str::contains($membroChapa->getSinteseCurriculo(), '<img')) {
                $curriculo = $membroChapa->getSinteseCurriculo();
                $curriculo = preg_replace("/<img[^>]+\>/i", "", $curriculo);

                /** @var MembroChapa $membro */
                $membro = $this->membroChapaRepository->find($membroChapa->getId());
                $membro->setSinteseCurriculo($curriculo);
                $this->membroChapaRepository->persist($membro);
            }
        }
    }

    /**
     * Método executado par acorrigir status participação em produção de rotina qye rejeitou convites errado
     */
    public function corrigirStatusParticipacaoMembros($id)
    {
        /** @var MembroChapa $membro */
        $membro = $this->membroChapaRepository->find($id);

        if (!empty($membro)) {
            $membro->setStatusParticipacaoChapa(StatusParticipacaoChapa::newInstanceById(
                Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR
            ));
            $membro->setSituacaoMembroChapa(SituacaoMembroChapa::newInstanceById(
                Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO
            ));
            $this->membroChapaRepository->persist($membro);
            echo "Membro #id {$id} alterado <br>";
        }
    }

    /**
     * Método auxiliar que exluir a foto do membro anterior na alteração de membro
     * @param MembroChapa|null $membroAnterior
     */
    private function excluirFotoAlteracaoMembro(?MembroChapa $membroAnterior): void
    {
        if(!empty($membroAnterior->getNomeArquivoFoto())) {
            $diretorioSinteseCurriculo = $this->getArquivoService()->getCaminhoRepositorioSinteseCurriculo(sprintf(
                '%s/%s',
                $membroAnterior->getChapaEleicao()->getId(),
                $membroAnterior->getId()
            ));
            $this->getArquivoService()->excluir($diretorioSinteseCurriculo, $membroAnterior->getNomeArquivoFoto());
        }
    }

    /**
     * Método para criar pdf de Declaração de Representatividade
     * @param integer $idMembro
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function documentoRepresentatividade($idMembro)
    {   
        $profissional = $this->membroChapaRepository->getPorId($idMembro)->getProfissional();
        $nome = $profissional->getNome();
        $cpf = $profissional->getCpf();

        $representatividades = RespostaDeclaracaoRepresentatividade::with('item')
            ->where('id_membro_chapa', $idMembro)
            ->get();

        $mPdf = new Mpdf(['setAutoTopMargin' => 'stretch']);
        $mPdf->setAutoBottomMargin = 'stretch';
        $html = "
        <!DOCTYPE html>
        <html lang='{{ str_replace('_', '-', app('translator')->getLocale()) }}'>
        <head>
        <link href='http://fonts.cdnfonts.com/css/dax' rel='stylesheet'>
        </head>
        <body>
            <img src=' http://siccau.caubr.org.br/public/img/banners/caubr/bannermailsiccau.jpg'>
            <p>
                <strong>
                    Declaração de representatividade
                </strong>
            </p>
            <p>
                Eu, $nome, CPF: $cpf, <strong>DECLARO</strong> que componho o Grupo Representativo conforme art. 46-A da Reolução 179/2022::
            </p>";
        
        $html_representatividade = '';
        foreach ($representatividades as $representatividade) {
            $html_representatividade = $html_representatividade.'<p> - '.$representatividade->item[0]->ds_item_declaracao.'</p>';
        }

        $html = $html. $html_representatividade."</body></html>";

        $mPdf->WriteHTML($html);

        $arquivos = ArquivoRepresentatividade::where('id_membro_chapa', $idMembro)->get();
        $filesTotal = sizeof($arquivos);
        $fileNumber = 1;
        $mPdf->SetImportUse();
        $mPdf->AddPage(); 

        foreach ($arquivos as $arquivo) {
            $path = getenv('SICCAU_STORAGE_PATH'). DIRECTORY_SEPARATOR . Constants::PATH_STORAGE_ARQUIVO_REPRESENTATIVIDADE . DIRECTORY_SEPARATOR . $arquivo->nm_fis_arquivo;
            if (file_exists($path)) {
                $pagesInFile = $mPdf->SetSourceFile($path);
                for ($i = 1; $i <= $pagesInFile; $i++) {
                    $tplId = $mPdf->ImportPage($i); 
                    $mPdf->UseTemplate($tplId);
                    if (($fileNumber < $filesTotal) || ($i != $pagesInFile)) {
                        $mPdf->WriteHTML('<pagebreak />');
                    }
                }
            }
            $fileNumber++;
        }

        $nomeDocumento = "Eleitoral" . time() . ".pdf";
        $caminho = sys_get_temp_dir() .'/'. $nomeDocumento;
        $mPdf->Output($caminho);

        $arquivoTO = new ArquivoTO();
        $arquivoTO->name = 'Declaracao_Representatividade';

        $info = new finfo(FILEINFO_MIME_TYPE);
        $arquivoTO->type = $info->file($caminho);;
        $arquivoTO->file = file_get_contents($caminho);

        return $arquivoTO;

    }

    /**
     * Retorna uma nova instância de 'RespostaDeclaracaoRepresentatividadeBO'.
     *
     * @return RespostaDeclaracaoRepresentatividadeBO
     */
    private function getRespostaDeclaracaoRepresentatividadeBO()
    {
        if (empty($this->respostaDeclaracaoRepresentatividadeBO)) {
            $this->respostaDeclaracaoRepresentatividadeBO = app()->make(RespostaDeclaracaoRepresentatividadeBO::class);
        }

        return $this->respostaDeclaracaoRepresentatividadeBO;
    }

    /**
     * Atualizar statusEleição dos membros da chapa
     *
     * @return bool
     */
    public function setStatusEleito(Request $request)
    {
        $membros = $request['membros'];

        if (empty($membros)) {
            throw new NegocioException(Message::NENHUM_REGISTRO_ENCONTRADO);
        }

        foreach ($membros as $membro) {
            MembrosChapa::where('id_membro_chapa', $membro['titular']['idMembro'])
                ->update(['status_eleito' => $membro['titular']['statusEleito']]);
            MembrosChapa::where('id_membro_chapa', $membro['suplente']['idMembro'])
                ->update(['status_eleito' => $membro['suplente']['statusEleito']]);
        }

        $this->saveHistorico($request['idChapa'], $request['idUsuario']);

        return true;
    }

    /**
     * Salva registro em histórico
     *
     * @return array
     */
    public function saveHistorico($idChapa, $idUsuario)
    {
        $ultimo = HistoricoChapaEleicao::orderBy('id_hist_chapa_eleicao', 'DESC')->first();
        
        HistoricoChapaEleicao::create([
            'id_hist_chapa_eleicao' => $ultimo->id_hist_chapa_eleicao+1,
            'id_usuario' => $idUsuario,
            'id_chapa_eleicao' => $idChapa,
            'dt_historico' => date('Y-m-d H:i:s'),
            'ds_acao' => 'Seleção de membro(s) eleito(s)',
            'ds_origem' => 'Corporativo',
        ]);
    }

    /**
     * Busca membros da chapa que foram eleitos utilizando filtros
     *
     * @return array
     */
    public function getEleitoByFilter(Request $request)
    {
        if (empty($request['ano'])) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS);
        }
        $ano = $request['ano'];
        
        $membros = MembrosChapa::where('status_eleito', true)
            ->whereHas('chapaEleicao', function ($query) use ($ano) {
                $query->whereHas('atividadeSecundaria', function ($query) use ($ano) {
                    $query->whereHas('atividadePrincipal', function ($query) use ($ano) {
                        $query->whereHas('calendario', function ($query) use ($ano) {
                            $query->whereHas('eleicao', function ($query) use ($ano) {
                                $query->where('nu_ano', $ano);
                            });
                        });
                    });
                });
            })
            ->when($request['idFilial'], function ($query, $idFilial) {
                $query->whereHas('chapaEleicao', function ($query) use ($idFilial) {
                    $query->where('id_cau_uf', $idFilial);
                });
            })
            ->when($request['cpf'], function ($query, $cpf) {
                $query->whereHas('profissional', function ($query) use ($cpf) {
                    $query->where('cpf', $cpf);
                });
            })
            ->when($request['nome'], function ($query, $nome) {
                $query->whereHas('profissional', function ($query) use ($nome) {
                    $query->where('nome', 'ILIKE', '%'.$nome.'%');
                });
            })
            ->when($request['representacao'], function ($query, $representacao) {
                if ($representacao == 1) {
                    $query->whereIn('id_tp_membro_chapa', ['1','3']);
                } else {
                    $query->where('id_tp_membro_chapa', $representacao);
                }
            })
            ->when($request['tipoConselheiro'], function ($query, $tipo) {
                $query->where('id_tp_partic_chapa', $tipo);
            })
            ->when($request['ordenar'] == 2, function ($query, $tipo) {
                $query->orderBy('id_tp_membro_chapa', 'ASC');
            })
            ->when($request['ordenar'] == 3, function ($query, $tipo) {
                $query->orderBy('id_tp_partic_chapa', 'ASC');
            })
            ->with('chapaEleicao.filial')
            ->with('profissional.pessoa')
            ->get();
        
        

        $arrayMembros = [];
        foreach ($membros as $membro) {
            $diploma = Conselheiro::where(['pessoa_id' => $membro['profissional']['pessoa_id'], 'ano_eleicao' => $ano])
                ->with(['diploma' => function ($query) {
                    $query->where('tipo_diploma_termo', Constants::TIPO_DIPLOMA);
                }])
                ->orderBY('id', 'desc')
                ->first();

            $termoDePosse = Conselheiro::where(['pessoa_id' => $membro['profissional']['pessoa_id'], 'ano_eleicao' => $ano])
                ->with(['diploma' => function ($query) {
                    $query->where('tipo_diploma_termo', Constants::TIPO_TERMO_POSSE);
                }])
                ->orderBY('id', 'desc')
                ->first();

            $arrayMembros[] = [
                'id' => $membro['id_membro_chapa'],
                'nome' => $membro['profissional']['nome'],
                'cpf' => $membro['profissional']['cpf'],
                'ano' => $ano,
                'uf' => $membro['chapaEleicao']['filial']['prefixo'],
                'idFilial' => $membro['chapaEleicao']['filial']['id'],
                'representacao' => $this->getRepresentacao($membro['id_tp_membro_chapa']),
                'idRepresentacao' => $membro['id_tp_membro_chapa'],
                'tipo' => $membro['id_tp_partic_chapa'] == Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR ? 'Titular' : 'Suplente',
                'idTipo' => $membro['id_tp_partic_chapa'],
                'diploma' => empty($diploma['diploma']['0']) ? false : $diploma['diploma'][0]['id'],
                'termo' => empty($termoDePosse['diploma']['0']) ? false : $termoDePosse['diploma'][0]['id'],
                'pessoa_id' => $membro['profissional']['pessoa_id'],
                'email' => $membro['profissional']['pessoa']['email'],
                'idConselheiro' => !empty($diploma['diploma']['0']) ? $diploma['id'] : 
                    (!empty($termoDePosse['diploma']['0']) ? $termoDePosse['id'] : false)
            ];
        }
        
        if (empty($arrayMembros)) {
            throw new NegocioException(Message::NENHUM_REGISTRO_ENCONTRADO);
        }

        return $arrayMembros;
    }

     /**
     * Retorna a representação do Membro da chapa
     *
     * @return array
     */
    public function getRepresentacao(int $idTipoMembro)
    {
        if ($idTipoMembro== Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL) {
            return 'Federal';
        }

        if ($idTipoMembro== Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL) {
            return 'Estadual';
        }

        if ($idTipoMembro== Constants::TIPO_MEMBRO_CHAPA_REPRESENTANTE_IES) {
            return 'IES';
        }
    }
    /**
     * Retorna o Presidente da UF
     *
     * @return array
     */
    public function getPresidenteUf(Request $request)
    {
        $ano = $request['ano'];
        $filial = $request['filial'];

        $presidente = MembrosChapa::where('status_eleito', true)
            ->whereHas('chapaEleicao', function ($query) use ($ano) {
                $query->whereHas('atividadeSecundaria', function ($query) use ($ano) {
                    $query->whereHas('atividadePrincipal', function ($query) use ($ano) {
                        $query->whereHas('calendario', function ($query) use ($ano) {
                            $query->whereHas('eleicao', function ($query) use ($ano) {
                                $query->where('nu_ano', $ano);
                            });
                        });
                    });
                });
            })
            ->whereHas('chapaEleicao', function ($query) use ($filial) {
                $query->whereHas('filial', function ($query) use ($filial) {
                    $query->where('prefixo', $filial);
                });
            })
            ->where('id_tp_membro_chapa', Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL)
            ->where('id_tp_partic_chapa', Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR)
            ->with('profissional')
            ->first();
           
        return [
            'nome' => $presidente->profissional->nome,
            'cpf' => $presidente->profissional->cpf,
            'uf' => $filial
        ];
    }
}
