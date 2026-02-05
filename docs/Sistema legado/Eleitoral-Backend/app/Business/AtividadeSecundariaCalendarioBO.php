<?php
/*
 * AtividadeSecundariaCalendarioBO.php* Copyright (c) CAU/BR.
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
use App\Entities\DeclaracaoAtividade;
use App\Entities\EmailAtividadeSecundaria;
use App\Entities\EmailAtividadeSecundariaTipo;
use App\Entities\Filial;
use App\Entities\MembroChapa;
use App\Entities\Profissional;
use App\Entities\TipoDeclaracaoAtividade;
use App\Entities\TipoEmailAtividadeSecundaria;
use App\Entities\UfCalendario;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailAtividadeJulgamentoFinalPeriodoAbertoJob;
use App\Jobs\EnviarEmailAtividadeJulgamentoFinalPeriodoFechadoJob;
use App\Mail\InformativoJulgamentoAtividadeSecundariaMail;
use App\Repository\AtividadeSecundariaCalendarioRepository;
use App\Repository\ChapaEleicaoRepository;
use App\Repository\DeclaracaoAtividadeRepository;
use App\Repository\EmailAtividadeSecundariaRepository;
use App\Repository\EmailAtividadeSecundariaTipoRepository;
use App\Repository\MembroChapaRepository;
use App\Repository\TipoDeclaracaoAtividadeRepository;
use App\Repository\TipoEmailAtividadeSecundariaRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\CauUfTO;
use App\To\DeclaracaoFiltroTO;
use App\To\DefinicaoDeclaracoesEmailsAtivSecundariaTO;
use App\To\DeclaracaoAtividadeTO;
use App\To\EmailAtividadeSecundariaTO;
use App\To\EmailAtividadeSemParametrizacaoTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use stdClass;
use Illuminate\Support\Arr;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'AtividadeSecundariaCalendario'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class AtividadeSecundariaCalendarioBO extends AbstractBO
{
    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var AtividadeSecundariaCalendarioRepository
     */
    private $atividadeSecundariaCalendarioRepository;

    /**
     * @var ChapaEleicaoRepository
     */
    private $chapaEleicaoRepository;

    /**
     * @var DeclaracaoAtividadeRepository
     */
    private $declaracaoAtividadeRepository;

    /**
     * @var EmailAtividadeSecundariaRepository
     */
    private $emailAtividadeSecundariaRepository;

    /**
     * @var EmailAtividadeSecundariaTipoRepository
     */
    private $emailAtividadeSecundariaTipoRepository;

    /**
     * @var MembroChapaRepository
     */
    private $membroChapaRepository;

    /**
     * @var TipoDeclaracaoAtividadeRepository
     */
    private $tipoDeclaracaoAtividadeRepository;

    /**
     * @var TipoEmailAtividadeSecundariaRepository
     */
    private $tipoEmailAtividadeSecundariaRepository;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var EmailAtividadeSecundariaTipoBO
     */
    private $emailAtividadeSecundariaTipoBO;

    /**
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * @var DeclaracaoBO
     */
    private $declaracaoBO;

    /**
     * @var UfCalendarioBO
     */
    private $ufCalendarioBO;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var DeclaracaoAtividadeBO
     */
    private $declaracaoAtividadeBO;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->atividadeSecundariaCalendarioRepository = $this->getRepository(AtividadeSecundariaCalendario::class);
        $this->declaracaoAtividadeRepository = $this->getRepository(DeclaracaoAtividade::class);
    }

    /**
     * Recupera a atividade secundária de acordo com o id informado.
     *
     * @param $id
     * @return mixed|null
     * @throws NonUniqueResultException
     */
    public function getPorId($id)
    {
        /** @var  $atividadeSecundaria AtividadeSecundariaCalendario */
        $atividadeSecundaria = $this->atividadeSecundariaCalendarioRepository->getPorId($id);

        if (!empty($atividadeSecundaria)) {
            if (!empty($atividadeSecundaria->getInformacaoComissaoMembro())) {
                $atividadeSecundaria->getInformacaoComissaoMembro()->setMembrosComissao(null);
            }
            $atividadeSecundaria->setEmailsAtividadeSecundaria(null);
            $atividadeSecundaria->getAtividadePrincipalCalendario()->setAtividadesSecundarias(null);
        }

        return $atividadeSecundaria;
    }

    /**
     * Busca Atividade Secundária por data de início e data de fim.
     *
     * @param integer $dataIncio
     * @param integer $dataFim
     * @param null $nivelPrincioal
     * @param null $nivelSecundaria
     * @return array|mixed|Statement|NULL
     */
    public function getAtividadesSecundariasPorVigencia(
        $dataIncio = null,
        $dataFim = null,
        $nivelPrincioal = null,
        $nivelSecundaria = null
    ) {
        return $this->atividadeSecundariaCalendarioRepository->getAtividadesSecundariasPorVigencia(
            $dataIncio,
            $dataFim,
            $nivelPrincioal,
            $nivelSecundaria
        );
    }

    /**
     * Buscar Atividade secundária por calendário.
     *
     * @param integer $idCalendario
     * @param integer $nivelPrincipal
     * @param integer $nivelSecundaria
     * @return AtividadeSecundariaCalendario
     * @throws NonUniqueResultException
     */
    public function getPorCalendario($idCalendario, $nivelPrincipal, $nivelSecundaria)
    {
        return $this->atividadeSecundariaCalendarioRepository->getPorCalendario(
            $idCalendario, $nivelPrincipal, $nivelSecundaria
        );
    }

    /**
     * Buscar Atividades secundárias por calendário.
     * @param integer $idCalendario
     * @param integer $nivelPrincipal
     * @param integer $nivelSecundaria
     * @return AtividadeSecundariaCalendario
     * @throws NonUniqueResultException
     */
    public function getAtividadesPorCalendario($idCalendario, $nivelPrincipal = null, $nivelSecundaria = null)
    {
        return $this->atividadeSecundariaCalendarioRepository->getAtividadesPorCalendario(
            $idCalendario, $nivelPrincipal, $nivelSecundaria
        );
    }

    /**
     * Buscar Atividade secundária por uma atividade secundária.
     * @param integer $idAtividadeSecundaria
     * @param integer $nivelPrincipal
     * @param integer $nivelSecundaria
     * @return AtividadeSecundariaCalendario
     * @throws NonUniqueResultException
     */
    public function getPorAtividadeSecundaria($idAtividadeSecundaria, $nivelPrincipal, $nivelSecundaria)
    {
        return $this->atividadeSecundariaCalendarioRepository->getPorAtividadeSecundaria(
            $idAtividadeSecundaria,
            $nivelPrincipal,
            $nivelSecundaria
        );
    }

    /**
     * Buscar Atividade secundária por uma atividade secundária.
     * @param $idChapaEleicao
     * @param integer $nivelPrincipal
     * @param integer $nivelSecundaria
     * @return AtividadeSecundariaCalendario
     * @throws NonUniqueResultException
     */
    public function getPorChapaEleicao($idChapaEleicao, $nivelPrincipal, $nivelSecundaria)
    {
        return $this->atividadeSecundariaCalendarioRepository->getPorChapaEleicao(
            $idChapaEleicao,
            $nivelPrincipal,
            $nivelSecundaria
        );
    }

    /**
     * Envia e-mail notificando o usuário concluinte do calendário de que a
     * atividade 1.1 (Definição da comissão de membros) não foi concluída e está prester a vencer.
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailsAtividadesSemParametrizacaoInicial()
    {
        $dataFim = Utils::adicionarDiasData(Utils::getDataHoraZero(), 2);

        $atividadesSemParametrizacao = $this->atividadeSecundariaCalendarioRepository
            ->getAtividadesSemParametrizacaoInicialPorDataFim(
                $dataFim
            );

        if (!empty($atividadesSemParametrizacao)) {

            $idsResponsaveis = array_map(function ($atividade) {
                return $atividade['idResponsavel'];
            }, $atividadesSemParametrizacao);

            $profissionais = $this->getProfissionalBO()->getListaProfissionaisFormatadaPorIds($idsResponsaveis);

            $emailAtivSecundariaPadrao = $this->getEmailAtividadeSecundariaBO()->getEmailAtividadeSecundariaPadrao();

            foreach ($atividadesSemParametrizacao as $atividadeSemParametrizacao) {

                if (!empty($profissionais[$atividadeSemParametrizacao['idResponsavel']])) {

                    /** @var Profissional $profissional */
                    $profissional = $profissionais[$atividadeSemParametrizacao['idResponsavel']];

                    $idAtividadeSecundaria = $atividadeSemParametrizacao['id'];

                    $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()
                        ->getEmailPorAtividadeSecundariaAndTipo(
                            $idAtividadeSecundaria,
                            Constants::EMAIL_DEFINIR_NUMERO_MEMBROS
                        );

                    if (!empty($emailAtivSecundariaPadrao) || !empty($emailAtividadeSecundaria)) {

                        $emailAtividadeSecundaria = $emailAtividadeSecundaria ?? $emailAtivSecundariaPadrao;

                        $this->getEmailAtividadeSecundariaBO()->enviarEmailAtividadeSecundaria(
                            $emailAtividadeSecundaria,
                            [$profissional->getEmail()]
                        );
                    }
                }
            }
        }
    }

    /**
     * Retorna as ufs que houveram calendários eleitorais.
     *
     * @param $idAtividadeSecundaria
     *
     * @return array
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getUfsCalendariosPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        $atividadeSecundaria = $this->atividadeSecundariaCalendarioRepository->getPorId($idAtividadeSecundaria);

        $calendario = $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario();

        $ufsCalendario = $this->getUfCalendarioBO()->getUfsCalendario($calendario->getId());
        return $this->getUfsCalendarioChaveValorComPrefixo($ufsCalendario);
    }

    /**
     * Retorna todas os profissionais com o nome informado.
     *
     * @param stdClass $profissionalTO
     * @return array|null
     * @throws NegocioException
     */
    public function getProfissionaisPorCpfNome($profissionalTO)
    {
        $profissionais = $this->getCorporativoService()->getProfissionaisPorCpfNome($profissionalTO->cpfNome);

        if (empty($profissionais)) {
            throw new NegocioException(Message::MSG_CPF_NOME_NAO_ENCONTRADO_SICCAU);
        }

        return $profissionais;
    }

    /**
     * Responsável por fazer definição de e-mails e declarações por atividade secundária
     *
     * @param DefinicaoDeclaracoesEmailsAtivSecundariaTO $configuracaoDeclaracoesEmailsTO
     *
     * @return DefinicaoDeclaracoesEmailsAtivSecundariaTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function definirDeclaracoesEmailsPorAtividadeSecundaria(
        DefinicaoDeclaracoesEmailsAtivSecundariaTO $configuracaoDeclaracoesEmailsTO
    ): DefinicaoDeclaracoesEmailsAtivSecundariaTO {

        if (empty($configuracaoDeclaracoesEmailsTO->getIdAtividadeSecundaria())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $atividadeSecundaria = $this->atividadeSecundariaCalendarioRepository->getPorId(
            $configuracaoDeclaracoesEmailsTO->getIdAtividadeSecundaria()
        );

        if (empty($atividadeSecundaria)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $qtdEmailsDefinir = $this->getQtdEmailsDefinirPorAtividadeSecundaria($atividadeSecundaria);
        $qtdDeclaracoesDefinir = $this->getQtdDeclaracoesDefinirPorAtividadeSecundaria($atividadeSecundaria);
        $permiteAlteracaoTipos = $this->permiteAlteracaoDeTiposEmailsDeclaracoes($atividadeSecundaria);
        $isSalvarHistorico = $this->possuiAuditoriaDefinicaoEmailsDeclaracoes($atividadeSecundaria);

        $this->validarCamposObrigatorioDeclaracoesEEmails(
            $atividadeSecundaria,
            $configuracaoDeclaracoesEmailsTO,
            $qtdEmailsDefinir,
            $qtdDeclaracoesDefinir
        );

        try {
            $this->beginTransaction();

            if ($qtdEmailsDefinir > 0) {
                $this->definirEmailsParaAtividadeSecundaria(
                    $atividadeSecundaria,
                    $configuracaoDeclaracoesEmailsTO->getEmails(),
                    $permiteAlteracaoTipos,
                    $isSalvarHistorico
                );
            }

            if ($qtdDeclaracoesDefinir > 0) {
                $this->definirDeclaracoesParaAtividadeSecundaria(
                    $atividadeSecundaria,
                    $configuracaoDeclaracoesEmailsTO->getDeclaracoes(),
                    $permiteAlteracaoTipos,
                    $isSalvarHistorico
                );
            }

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return $configuracaoDeclaracoesEmailsTO;
    }

    /**
     * Retorna um Atividade Secundária até calendário e eleição dado 2 níveis de busca
     * e Para eleicoes que estejam vigentes e ativas.
     *
     * @param int $nivelPrincipal
     * @param int $nivelSecundaria
     * @param bool $isApenasVigenciaCalendario
     *
     * @return array|null
     */
    public function getAtividadeSecundariaAtivaPorNivel($nivelPrincipal, $nivelSecundaria, $isApenasVigenciaCalendario = false)
    {
        $atividadeSecundaria = $this->atividadeSecundariaCalendarioRepository->getAtividadeSecundariaAtivaPorNivel(
            $nivelPrincipal, $nivelSecundaria, $isApenasVigenciaCalendario
        );
        return $atividadeSecundaria ?? [];
    }

    /**
     *  Realiza a definições de e-mails com histórico
     *
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param array $emails
     * @param bool $permiteAlteracaoTipos
     * @param bool $isSalvarHistorico
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    private function definirEmailsParaAtividadeSecundaria(
        AtividadeSecundariaCalendario $atividadeSecundaria,
        array $emails,
        $permiteAlteracaoTipos = true,
        $isSalvarHistorico = true
    ) {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();

        /** @var EmailAtividadeSecundariaTO $emailAtividadeSecundariaTO */
        foreach ($emails as $emailAtividadeSecundariaTO) {

            $emailAtivSecundaria = $this->getEmailAtividadeSecundariaRepository()->getEmailAtividadeSecundariaPorId(
                $emailAtividadeSecundariaTO->getIdEmailAtividadeSecundaria()
            );
            if (
                empty($emailAtivSecundaria) or
                $emailAtivSecundaria->getAtividadeSecundaria()->getId() != $atividadeSecundaria->getId()
            ) {
                throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
            }

            $indice = array_search(
                $emailAtividadeSecundariaTO->getIdTipoEmailAtividadeSecundaria(),
                $this->getIdsTiposEmailPorAtividadeSecundaria($atividadeSecundaria)
            );

            if ($usuario->administrador and $permiteAlteracaoTipos) {
                $tipoCorpoEmailAnterior = $this->getTipoEmailAtividadeSecundariaRepository()->find(
                    $emailAtividadeSecundariaTO->getIdTipoEmailAtividadeSecundaria()
                );

                $descricaoTipoEmail = $emailAtividadeSecundariaTO->getDescricaoTipoEmailAtividadeSecundaria();

                if (empty($descricaoTipoEmail)) {
                    throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
                }

                if ($tipoCorpoEmailAnterior->getDescricao() != $descricaoTipoEmail) {
                    if (strlen($descricaoTipoEmail) > Constants::TAMANHO_MAXIMO_CARACTERES_REGRA) {
                        throw new NegocioException(
                            Message::MSG_TAMANHO_MAXIMO_N_CARACTERES_EXCEDIDAS,
                            [Constants::TAMANHO_MAXIMO_CARACTERES_REGRA]
                        );
                    }

                    $tipoCorpoEmailAnterior->setDescricao($descricaoTipoEmail);
                    $this->getTipoEmailAtividadeSecundariaRepository()->persist($tipoCorpoEmailAnterior);

                    $this->salvarHistoricosDefinicaoDeclaraoesEmails(
                        $atividadeSecundaria,
                        sprintf(Constants::HISTORICO_DS_ALTERACAO_REGRA_ENVIO, $indice),
                        Constants::HISTORICO_ACAO_ALTERAR,
                        $isSalvarHistorico
                    );
                }
            }

            $emailAtividadeSecundariaTipoAnterior = $this->getEmailAtividadeSecundariaTipoBO()
                ->getEmailAtividadeSecundariaTipoDefinido(
                    $atividadeSecundaria->getId(),
                    $emailAtividadeSecundariaTO->getIdTipoEmailAtividadeSecundaria()
                );


            if (empty($emailAtividadeSecundariaTipoAnterior)) {
                $emailAtividadeSecundariaTipo = EmailAtividadeSecundariaTipo::newInstance([
                    'tipoEmail' => ['id' => $emailAtividadeSecundariaTO->getIdTipoEmailAtividadeSecundaria()],
                    'emailAtividadeSecundaria' => ['id' => $emailAtividadeSecundariaTO->getIdEmailAtividadeSecundaria()]
                ]);

                $this->getEmailAtividadeSecundariaTipoRepository()->persist($emailAtividadeSecundariaTipo);

                $this->salvarHistoricosDefinicaoDeclaraoesEmails(
                    $atividadeSecundaria,
                    sprintf(Constants::HISTORICO_DS_INCLUSAO_EMAIL, $indice),
                    Constants::HISTORICO_ACAO_INSERIR,
                    $isSalvarHistorico
                );
            } elseif (
                $emailAtividadeSecundariaTipoAnterior->getEmailAtividadeSecundaria()->getId() !=
                $emailAtividadeSecundariaTO->getIdEmailAtividadeSecundaria()
            ) {
                $emailAtividadeSecundariaTipoAnterior->setEmailAtividadeSecundaria($emailAtivSecundaria);
                $this->getEmailAtividadeSecundariaTipoRepository()->persist($emailAtividadeSecundariaTipoAnterior);

                $this->salvarHistoricosDefinicaoDeclaraoesEmails(
                    $atividadeSecundaria,
                    sprintf(Constants::HISTORICO_DS_ALTERACAO_EMAIL, $indice),
                    Constants::HISTORICO_ACAO_ALTERAR,
                    $isSalvarHistorico
                );
            }
        }
    }

    /**
     * Realiza a definições de declarações com histórico e
     * salva alteração de tipos de declaração para usuário administrador
     *
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param array $declaracoes
     * @param bool $permiteAlteracaoTipos
     * @param bool $isSalvarHistorico
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    private function definirDeclaracoesParaAtividadeSecundaria(
        AtividadeSecundariaCalendario $atividadeSecundaria,
        array $declaracoes,
        $permiteAlteracaoTipos = true,
        $isSalvarHistorico = true
    ) {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();

        /** @var  DeclaracaoAtividadeTO $declaracaoAtividadeTO */
        foreach ($declaracoes as $declaracaoAtividadeTO) {

            $indice = array_search(
                $declaracaoAtividadeTO->getIdTipoDeclaracaoAtividade(),
                $this->getIdsTiposDeclaracaoPorAtividadeSecundaria($atividadeSecundaria)
            );

            if ($usuario->administrador and $permiteAlteracaoTipos) {
                /** @var TipoDeclaracaoAtividade $tipoDeclaracaoAnterior */
                $tipoDeclaracaoAnterior = $this->getTipoDeclaracaoAtividadeRepository()->find(
                    $declaracaoAtividadeTO->getIdTipoDeclaracaoAtividade()
                );

                $descricaoTipoDeclaracaoAtividade = $declaracaoAtividadeTO->getDescricaoTipoDeclaracaoAtividade();

                if (empty($descricaoTipoDeclaracaoAtividade)) {
                    throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
                }

                if ($tipoDeclaracaoAnterior->getDescricao() != $descricaoTipoDeclaracaoAtividade) {
                    if (strlen($descricaoTipoDeclaracaoAtividade) > Constants::TAMANHO_MAXIMO_CARACTERES_REGRA) {
                        throw new NegocioException(
                            Message::MSG_TAMANHO_MAXIMO_N_CARACTERES_EXCEDIDAS,
                            [Constants::TAMANHO_MAXIMO_CARACTERES_REGRA]
                        );
                    }

                    $tipoDeclaracaoAnterior->setDescricao($descricaoTipoDeclaracaoAtividade);
                    $tipoDeclaracaoAnterior = $this->getTipoDeclaracaoAtividadeRepository()->persist(
                        $tipoDeclaracaoAnterior
                    );

                    $this->salvarHistoricosDefinicaoDeclaraoesEmails(
                        $tipoDeclaracaoAnterior,
                        sprintf(Constants::HISTORICO_DS_ALTERACAO_REGRA_DECLARACAO, $indice),
                        Constants::HISTORICO_ACAO_ALTERAR,
                        $isSalvarHistorico
                    );
                }
            }

            $declaracaoAtivAnterior = $this->declaracaoAtividadeRepository->getDeclaracaoAtividadePorTipoDeclaracao(
                $declaracaoAtividadeTO->getIdTipoDeclaracaoAtividade(),
                $atividadeSecundaria->getId()
            );

            if (empty($declaracaoAtivAnterior)) {
                $declaracaoAtividade = DeclaracaoAtividade::newInstance([
                    'idDeclaracao' => $declaracaoAtividadeTO->getIdDeclaracao(),
                    'tipoDeclaracaoAtividade' => ['id' => $declaracaoAtividadeTO->getIdTipoDeclaracaoAtividade()]
                ]);
                $declaracaoAtividade->setAtividadeSecundaria($atividadeSecundaria);
                $this->declaracaoAtividadeRepository->persist($declaracaoAtividade);

                $this->salvarHistoricosDefinicaoDeclaraoesEmails(
                    $atividadeSecundaria,
                    sprintf(Constants::HISTORICO_DS_INCLUSAO_DECLARACAO, $indice),
                    Constants::HISTORICO_ACAO_INSERIR,
                    $isSalvarHistorico
                );
            } elseif ($declaracaoAtivAnterior->getIdDeclaracao() != $declaracaoAtividadeTO->getIdDeclaracao()) {
                $declaracaoAtivAnterior->setIdDeclaracao($declaracaoAtividadeTO->getIdDeclaracao());
                $this->declaracaoAtividadeRepository->persist($declaracaoAtivAnterior);

                $this->salvarHistoricosDefinicaoDeclaraoesEmails(
                    $atividadeSecundaria,
                    sprintf(Constants::HISTORICO_DS_ALTERACAO_DECLARACAO, $indice),
                    Constants::HISTORICO_ACAO_ALTERAR,
                    $isSalvarHistorico
                );
            }
        }
    }

    /**
     * Salva histórico referente a definição de declarações e e-mails
     *
     * @param $referencia
     * @param $descricao
     * @param $acao
     * @param bool $isSalvarHistorico
     * @throws Exception
     */
    private function salvarHistoricosDefinicaoDeclaraoesEmails($referencia, $descricao, $acao, $isSalvarHistorico)
    {
        if ($isSalvarHistorico) {
            $historico = $this->getHistoricoBO()->criarHistorico(
                $referencia,
                Constants::HISTORICO_ID_TIPO_PARAMET_DECLARACOES_EMAILS,
                $acao,
                $descricao
            );

            $this->getHistoricoBO()->salvar($historico);
        }
    }

    /**
     * Recupera o total de resposta de declaracao da chapa referente a uma atividade secundária
     *
     * @param int $idAtividadeSecundaria
     * @return mixed
     * @throws Exception
     */
    public function getQuantidadeRespostaDeclaracaoPorAtividade(int $idAtividadeSecundaria)
    {
        /** @var AtividadeSecundariaCalendario $atividadeSecundaria */
        $atividadeSecundaria = $this->atividadeSecundariaCalendarioRepository->getPorId($idAtividadeSecundaria);

        $nivelPrincipal = $atividadeSecundaria->getAtividadePrincipalCalendario()->getNivel();
        $nivelSecundaria = $atividadeSecundaria->getNivel();

        $totalRespostas = 0;

        if ($nivelPrincipal == 1 && $nivelSecundaria == 4) {
            $totalRespostas = $this->getMembroComissaoBO()->getTotalRespostaDeclaracaoPorAtividadePrincipalUm(
                $atividadeSecundaria->getAtividadePrincipalCalendario()->getId()
            );
        }

        if ($nivelPrincipal == 2 && $nivelSecundaria == 1) {
            $totalRespostas = $this->getChapaEleicaoRepository()->getQtdRespostaDeclaracaoPorAtividadeSecundaria(
                $idAtividadeSecundaria
            );
        }

        if ($nivelPrincipal == 2 && $nivelSecundaria == 2) {
            $totalRespostas = $this->getMembroChapaRepository()->getQtdRespostaDeclaracaoPorAtividadePrincipal(
                $atividadeSecundaria->getAtividadePrincipalCalendario()->getId()
            );
        }

        return $totalRespostas;
    }

    /**
     * Valida os campos obrigatórios para a definição de emails e declarações.
     *
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param DefinicaoDeclaracoesEmailsAtivSecundariaTO $configuracaoDeclaracoesEmailsTO
     * @throws NegocioException
     */
    private function validarCamposObrigatorioDeclaracoesEEmails(
        $atividadeSecundaria,
        DefinicaoDeclaracoesEmailsAtivSecundariaTO $configuracaoDeclaracoesEmailsTO,
        $qtdEmailsDefinir,
        $qtdDeclaracoesDefinir
    ) {
        if ($qtdEmailsDefinir > 0) {
            if (
                empty($configuracaoDeclaracoesEmailsTO->getEmails())
                or count($configuracaoDeclaracoesEmailsTO->getEmails()) < $qtdEmailsDefinir
            ) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
            }

            $emailsInvalidos = array_filter(
                $configuracaoDeclaracoesEmailsTO->getEmails(),
                function ($emailAtividadeSecundariaTO) {
                    /** @var EmailAtividadeSecundariaTO $emailAtividadeSecundariaTO */
                    return (
                        empty($emailAtividadeSecundariaTO->getIdEmailAtividadeSecundaria()) or
                        empty($emailAtividadeSecundariaTO->getIdTipoEmailAtividadeSecundaria())
                    );
                }
            );

            if (!empty($emailsInvalidos)) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
            }
        } else {
            $configuracaoDeclaracoesEmailsTO->setEmails([]);
        }

        if ($qtdDeclaracoesDefinir > 0) {
            if (
                empty($configuracaoDeclaracoesEmailsTO->getDeclaracoes())
                or count($configuracaoDeclaracoesEmailsTO->getDeclaracoes()) < $qtdDeclaracoesDefinir
            ) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
            }

            $declaracoesInvalidos = array_filter(
                $configuracaoDeclaracoesEmailsTO->getDeclaracoes(),
                function ($declaracaoAtividadeTO) {
                    /** @var  DeclaracaoAtividadeTO $declaracaoAtividadeTO */
                    return (
                        empty($declaracaoAtividadeTO->getIdDeclaracao()) or
                        empty($declaracaoAtividadeTO->getIdTipoDeclaracaoAtividade())
                    );
                }
            );

            if (!empty($declaracoesInvalidos)) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
            }
        } else {
            $configuracaoDeclaracoesEmailsTO->setDeclaracoes([]);
        }
    }

    /**
     * Verifica se uma atividade esta vigente
     *
     * @param int $idCalendario
     * @param int $nivelPrincipal
     * @param int $nivelSecundario
     * @return bool
     */
    public function isAtividadeVigente(int $idCalendario, int $nivelPrincipal, int $nivelSecundario): bool
    {
        $ativSecundaria = $this->atividadeSecundariaCalendarioRepository->getPorCalendario(
            $idCalendario, $nivelPrincipal, $nivelSecundario
        );

        $dataInicio = Utils::getDataHoraZero($ativSecundaria->getDataInicio());
        $dataFim = Utils::getDataHoraZero($ativSecundaria->getDataFim());

        return (Utils::getDataHoraZero() >= $dataInicio)
            && (Utils::getDataHoraZero() <= $dataFim);
    }

    /**
     * Retorna o destinatário do e-mail
     *
     * @param EmailAtividadeSemParametrizacaoTO $emailAtividadeSemParametrizacaoTO
     * @return string|null
     * @throws NegocioException
     */
    private function getDestinatarioEmail(EmailAtividadeSemParametrizacaoTO $emailAtividadeSemParametrizacaoTO)
    {
        $idResponsavel = !empty($emailAtividadeSemParametrizacaoTO)
            ? $emailAtividadeSemParametrizacaoTO->getIdResponsavel()
            : null;

        $emailResponsavel = null;
        if (!empty($idResponsavel)) {
            $usuario = $this->getCorporativoService()->getUsuarioPorId($idResponsavel);

            if (!empty($usuario)) {
                $emailResponsavel = $usuario->getEmail();
            }
        }
        return $emailResponsavel;
    }

    /**
     * Retorna o conteudo do e-mail em HTML.
     *
     * @param string|null $arquivoCabecalhoBase64
     * @param string|null $arquivoRodapeBase64
     * @param EmailAtividadeSemParametrizacaoTO $emailAtividadesSemParametrizacao
     * @return mixed|string
     */
    private function getConteudoEmail(
        $arquivoCabecalhoBase64,
        $arquivoRodapeBase64,
        EmailAtividadeSemParametrizacaoTO $emailAtividadesSemParametrizacao
    ) {
        $html = AppConfig::getTemplateEmail(Constants::TEMPLATE_EMAIL_PADRAO);
        $html = str_replace(Constants::PARAMETRO_EMAIL_IMG_CABECALHO, $arquivoCabecalhoBase64, $html);
        $html = str_replace(Constants::PARAMETRO_EMAIL_IMG_RODAPE, $arquivoRodapeBase64, $html);

        $html = str_replace(
            Constants::PARAMETRO_EMAIL_CABECALHO,
            $emailAtividadesSemParametrizacao->getTextoCabecalho(),
            $html
        );

        $html = str_replace(
            Constants::PARAMETRO_EMAIL_CORPO,
            $emailAtividadesSemParametrizacao->getDescricaoCorpoEmail(),
            $html
        );

        $html = str_replace(
            Constants::PARAMETRO_EMAIL_RODAPE,
            $emailAtividadesSemParametrizacao->getTextoRodape(),
            $html
        );
        return $html;
    }

    /**
     * Retorna a instância de ArquivoService, realizando a inicialização tardia.
     *
     * @return ArquivoService|mixed
     */
    public function getArquivoService()
    {
        if ($this->arquivoService == null) {
            $this->arquivoService = app()->make(ArquivoService::class);
        }

        return $this->arquivoService;
    }

    /**
     * Retorna a instância de CorporativoService, realizando a inicialização tardia.
     *
     * @return CorporativoService|mixed
     */
    public function getCorporativoService()
    {
        if ($this->corporativoService == null) {
            $this->corporativoService = app()->make(CorporativoService::class);
        }

        return $this->corporativoService;
    }

    /**
     * Retorna a instância de EmailAtividadeSecundariaBO, realizando a inicialização tardia.
     *
     * @return EmailAtividadeSecundariaBO|mixed
     */
    public function getEmailAtividadeSecundariaBO()
    {
        if ($this->emailAtividadeSecundariaBO == null) {
            $this->emailAtividadeSecundariaBO = app()->make(EmailAtividadeSecundariaBO::class);
        }

        return $this->emailAtividadeSecundariaBO;
    }

    /**
     * Retorna a instância de MembroComissaoBO, realizando a inicialização tardia.
     *
     * @return MembroComissaoBO|mixed
     */
    public function getMembroComissaoBO()
    {
        if ($this->membroComissaoBO == null) {
            $this->membroComissaoBO = app()->make(MembroComissaoBO::class);
        }

        return $this->membroComissaoBO;
    }

    /**
     * Retorna id da atividade secundária por nível
     * @param $idMembroComissao
     * @param $nivel
     * @return int
     * @throws NonUniqueResultException
     */
    public function getIdAtividadeSecundariaPorNivelMembroComissao($idMembroComissao, $nivel)
    {
        return $this->atividadeSecundariaCalendarioRepository->getIdAtividadeSecundariaPorNivelMembroComissao($idMembroComissao,
            $nivel);
    }

    /**
     * Retorna se a atividade secundária foi parametrizada os e-mails
     *
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @return bool
     */
    public function hasParametrizacaoEmail(AtividadeSecundariaCalendario $atividadeSecundaria)
    {
        $idsTiposEMail = $this->getIdsTiposEmailPorAtividadeSecundaria(
            $atividadeSecundaria
        );

        $emailsAtividadeSecundaria = $atividadeSecundaria->getEmailsAtividadeSecundaria() ?? [];
        $emailsAtividadeSecundaria = (!is_array($emailsAtividadeSecundaria))
            ? $emailsAtividadeSecundaria->toArray()
            : $emailsAtividadeSecundaria;

        $tiposEmailDefinido = [];
        /** @var EmailAtividadeSecundaria $emailAtividadeSecundaria */
        foreach ($emailsAtividadeSecundaria as $emailAtividadeSecundaria) {
            $tiposEmailDefinidoAux = $emailAtividadeSecundaria->getEmailsTipos() ?? [];
            $tiposEmailDefinidoAux = (!is_array($tiposEmailDefinidoAux))
                ? $tiposEmailDefinidoAux->toArray()
                : $tiposEmailDefinidoAux;

            $tiposEmailDefinido = array_merge($tiposEmailDefinido, $tiposEmailDefinidoAux);
        }

        $idsTiposEmailsDefinido = array_map(function ($tipoEmail) {
            /** @var EmailAtividadeSecundariaTipo $tipoEmail */
            return $tipoEmail->getTipoEmail()->getId();
        }, $tiposEmailDefinido);

        return count(array_diff($idsTiposEMail, $idsTiposEmailsDefinido)) == 0;
    }

    /**
     * Retorna as informações para definição/parametrização de e-mails de acordo com o id da atividade secundária
     *
     * @param integer $idAtividadeSecundaria
     * @return array
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getInformacoesDefinicoesEmail($idAtividadeSecundaria)
    {
        $atividadeSecundaria = $this->getPorId($idAtividadeSecundaria);
        $tiposEmails = [];
        $emailsAtividadeSecundaria = [];
        $emailsDefinidos = [];

        $idsTiposEmailsAtivSecundaria = $this->getIdsTiposEmailPorAtividadeSecundaria(
            $atividadeSecundaria
        );

        if (!empty($idsTiposEmailsAtivSecundaria)) {

            $tiposEmails = $this->getEmailAtividadeSecundariaRepository()->getTipoEmailsAtividadeSecundariaPorIds(
                $idsTiposEmailsAtivSecundaria
            );

            $emailsDefinidos = $this->getEmailAtividadeSecundariaTipoBO()->getPorAtividadeSecundaria(
                $idAtividadeSecundaria
            );

            $emailsAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()
                ->getEmailAtividadeSecundariaPorAtividadeSecundaria($idAtividadeSecundaria);
        }

        return [
            'tiposEmails' => $tiposEmails,
            'emailsDefinidos' => $emailsDefinidos,
            'emailsAtividadeSecundaria' => $emailsAtividadeSecundaria,
            'hasAlteracaoTipos' => $this->permiteAlteracaoDeTiposEmailsDeclaracoes($atividadeSecundaria)
        ];
    }

    /**
     * Retorna as informações para definição/parametrização de declarações de acordo com o id da atividade secundária
     *
     * @param integer $idAtividadeSecundaria
     * @return array
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getInformacoesDefinicoesDeclaracao($idAtividadeSecundaria)
    {
        $atividadeSecundaria = $this->getPorId($idAtividadeSecundaria);
        $tiposDeclaracoes = [];
        $declaracoes = [];
        $declaracoesDefinidas = [];

        $idsTiposDeclaracoesAtivSecundaria = $this->getIdsTiposDeclaracaoPorAtividadeSecundaria(
            $atividadeSecundaria
        );

        if (!empty($idsTiposDeclaracoesAtivSecundaria)) {

            $tiposDeclaracoes = $this->getTipoDeclaracaoAtividadeRepository()->getTiposDeclaracaoPorIds(
                $idsTiposDeclaracoesAtivSecundaria
            );

            $filtroTO = DeclaracaoFiltroTO::newInstance();
            $filtroTO->setIdModulo(Constants::MODULO_ELEITORAL);
            $declaracoes = $this->getDeclaracaoBO()->getDeclaracoesPorFiltro($filtroTO);

            $declaracoesDefinidas = $this->getDeclaracaoAtividadeBO()->getDeclaracoesAtividadePorAtividadeSecundaria(
                $idAtividadeSecundaria
            );
        }

        return [
            'declaracoes' => $declaracoes,
            'tiposDeclaracoes' => $tiposDeclaracoes,
            'declaracoesDefinidas' => $declaracoesDefinidas
        ];
    }

    /**
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     *
     * @return array
     */
    public function getIdsTiposEmailPorAtividadeSecundaria(AtividadeSecundariaCalendario $atividadeSecundaria)
    {
        $idsTiposEmailAtivSecundaria = [];

        $nivelPrincipal = $atividadeSecundaria->getAtividadePrincipalCalendario()->getNivel();
        $nivelSecundaria = $atividadeSecundaria->getNivel();

        if (!empty(Constants::$tiposEmailAtividadeSecundaria[$nivelPrincipal][$nivelSecundaria])) {
            $idsTiposEmailAtivSecundaria = Constants::$tiposEmailAtividadeSecundaria[$nivelPrincipal][$nivelSecundaria];
        }

        return $idsTiposEmailAtivSecundaria;
    }

    /**
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     *
     * @return array
     */
    public function getIdsTiposDeclaracaoPorAtividadeSecundaria(AtividadeSecundariaCalendario $atividadeSecundaria)
    {
        $idsTiposDeclaracao = [];

        $nivelPrincipal = $atividadeSecundaria->getAtividadePrincipalCalendario()->getNivel();
        $nivelSecundaria = $atividadeSecundaria->getNivel();

        if (!empty(Constants::$tiposDeclaracoesAtividadeSecundaria[$nivelPrincipal][$nivelSecundaria])) {
            $idsTiposDeclaracao = Constants::$tiposDeclaracoesAtividadeSecundaria[$nivelPrincipal][$nivelSecundaria];
        }

        return $idsTiposDeclaracao;
    }

    /**
     * Recupera a Atividade Secundaria de acordo com o Calendario e os Niveis informados.
     *
     * @param $idCalendario
     * @return AtividadeSecundariaCalendario|null
     * @throws NonUniqueResultException
     */
    public function getAtividadeSecundariaPorCalendario($idCalendario){
        return $this->atividadeSecundariaCalendarioRepository->getAtividadeSecundariaPorCalendario(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_DENUNCIA,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_DENUNCIA,
            $idCalendario
        );
    }

    /**
     * Recupera a Atividade Secundaria de acordo com o Calendario e os Niveis informados.
     *
     * @param $nivelPrincipal
     * @param $nivelSecundario
     * @param $isApenasVigenciaCalendario
     * @return array
     * @throws NonUniqueResultException
     */
    public function getAtividadeSecundariaPorNiveis($nivelPrincipal, $nivelSecundario, $isApenasVigenciaCalendario = false){
        return $this->atividadeSecundariaCalendarioRepository->getAtividadeSecundariaAtivaPorNivel(
            $nivelPrincipal,
            $nivelSecundario,
            $isApenasVigenciaCalendario
        );
    }

    /**
     * @param $atividadeSecundaria
     * @return integer
     */
    private function getQtdEmailsDefinirPorAtividadeSecundaria($atividadeSecundaria)
    {
        $idsTiposEmailsAtividadeSecundaria = $this->getIdsTiposEmailPorAtividadeSecundaria(
            $atividadeSecundaria
        );

        $qtdEmailsDefinir = 0;
        if (!empty($idsTiposEmailsAtividadeSecundaria)) {
            $qtdEmailsDefinir = count($idsTiposEmailsAtividadeSecundaria);
        }
        return $qtdEmailsDefinir;
    }

    /**
     * @param $atividadeSecundaria
     * @return integer
     */
    private function getQtdDeclaracoesDefinirPorAtividadeSecundaria($atividadeSecundaria)
    {
        $idsTiposDeclaracao = $this->getIdsTiposDeclaracaoPorAtividadeSecundaria($atividadeSecundaria);

        $qtdDeclaracoesDefinir = 0;
        if (!empty($idsTiposDeclaracao)) {
            $qtdDeclaracoesDefinir = count($idsTiposDeclaracao);
        }
        return $qtdDeclaracoesDefinir;
    }

    /**
     * @param $atividadeSecundaria
     * @return boolean
     */
    private function permiteAlteracaoDeTiposEmailsDeclaracoes($atividadeSecundaria)
    {
        $nivelPrincipal = $atividadeSecundaria->getAtividadePrincipalCalendario()->getNivel();
        $nivelSecundaria = $atividadeSecundaria->getNivel();

        $configDefinicaoTipos = [];
        if (!empty(Constants::$configuracaoDefinicaoEmails[$nivelPrincipal]['permiteAlteracaoDeTipos'])) {
            $configDefinicaoTipos = Constants::$configuracaoDefinicaoEmails[$nivelPrincipal]['permiteAlteracaoDeTipos'];
        }

        return in_array($nivelSecundaria, $configDefinicaoTipos);
    }

    /**
     * @param $atividadeSecundaria
     * @return boolean
     */
    private function possuiAuditoriaDefinicaoEmailsDeclaracoes($atividadeSecundaria)
    {
        $nivelPrincipal = $atividadeSecundaria->getAtividadePrincipalCalendario()->getNivel();
        $nivelSecundaria = $atividadeSecundaria->getNivel();

        $configDefinicaoTipos = [];
        if (!empty(Constants::$configuracaoDefinicaoEmails[$nivelPrincipal]['possuiAuditoria'])) {
            $configDefinicaoTipos = Constants::$configuracaoDefinicaoEmails[$nivelPrincipal]['possuiAuditoria'];
        }

        return in_array($nivelSecundaria, $configDefinicaoTipos);
    }

    /**
     * Retorna as UFs das chapas em formato Chave/Valor com uf com prefixo.
     *
     * @param array $ufsCalendario
     *
     * @return array
     * @throws NegocioException
     */
    private function getUfsCalendarioChaveValorComPrefixo($ufsCalendario): array
    {
        $ufsChaveValor = $this->getUfsCalendarioChaveValor();

        $filiaisChaveValor = [
            [
                'idCauUf' => Constants::PREFIXO_IES,
                'descricao' => Constants::PREFIXO_IES
            ]
        ];

        /** @var UfCalendario $ufCalendario */
        foreach ($ufsCalendario as $ufCalendario) {
            $idCauUf = $ufCalendario->getIdCauUf();

            if (array_key_exists($idCauUf, $ufsChaveValor)) {
                $cauUfTo = CauUfTO::newInstance([
                    'idCauUf' => $idCauUf,
                    'descricao' => $ufsChaveValor[$idCauUf]
                ]);
                $filiaisChaveValor[] = $cauUfTo;
            }
        }

        return array_sort($filiaisChaveValor);
    }

    /**
     * Retorna as UFs do Calendario em formato Chave/Valor com uf já formatado.
     *
     *
     * @return array
     * @throws NegocioException
     */
    private function getUfsCalendarioChaveValor(): array
    {
        $filiais = $this->getFilialBO()->getFiliais();

        $filiaisChaveValor = $this->getUfsFiliaisChaveValor($filiais);

        return $filiaisChaveValor;
    }

    /**
     * Retorna as UFs das filiais em formato Chave/Valor.
     *
     * @param array $filiais
     *
     * @return array
     */
    private function getUfsFiliaisChaveValor($filiais): array
    {
        $filiaisChaveValor = [];

        /** @var Filial $filial */
        foreach ($filiais as $filial) {
            $filiaisChaveValor[$filial->getId()] = $filial->getPrefixo();
        }

        asort($filiaisChaveValor);

        return $filiaisChaveValor;
    }

    /**
     * Retorna uma instância de 'ChapaEleicaoRepository'
     *
     * @return ChapaEleicaoRepository
     */
    private function getChapaEleicaoRepository(): ChapaEleicaoRepository
    {
        if (empty($this->chapaEleicaoRepository)) {
            $this->chapaEleicaoRepository = $this->getRepository(ChapaEleicao::class);
        }

        return $this->chapaEleicaoRepository;
    }

    /**
     * Retorna uma instância de 'EmailAtividadeSecundariaRepository'
     *
     * @return EmailAtividadeSecundariaRepository
     */
    private function getEmailAtividadeSecundariaRepository()
    {
        if (empty($this->emailAtividadeSecundariaRepository)) {
            $this->emailAtividadeSecundariaRepository = $this->getRepository(
                EmailAtividadeSecundaria::class
            );
        }

        return $this->emailAtividadeSecundariaRepository;
    }

    /**
     * Retorna uma instância de 'EmailAtividadeSecundariaTipoRepository'
     *
     * @return EmailAtividadeSecundariaTipoRepository
     */
    private function getEmailAtividadeSecundariaTipoRepository(): EmailAtividadeSecundariaTipoRepository
    {
        if (empty($this->emailAtividadeSecundariaTipoRepository)) {
            $this->emailAtividadeSecundariaTipoRepository = $this->getRepository(
                EmailAtividadeSecundariaTipo::class
            );
        }

        return $this->emailAtividadeSecundariaTipoRepository;
    }

    /**
     * Retorna uma instância de 'MembroChapaRepository'
     *
     * @return MembroChapaRepository
     */
    private function getMembroChapaRepository(): MembroChapaRepository
    {
        if (empty($this->membroChapaRepository)) {
            $this->membroChapaRepository = $this->getRepository(MembroChapa::class);
        }

        return $this->membroChapaRepository;
    }

    /**
     * Retorna uma instância de 'TipoEmailAtividadeSecundariaRepository'
     *
     * @return TipoEmailAtividadeSecundariaRepository
     */
    private function getTipoEmailAtividadeSecundariaRepository(): TipoEmailAtividadeSecundariaRepository
    {
        if (empty($this->tipoEmailAtividadeSecundariaRepository)) {
            $this->tipoEmailAtividadeSecundariaRepository = $this->getRepository(
                TipoEmailAtividadeSecundaria::class
            );
        }

        return $this->tipoEmailAtividadeSecundariaRepository;
    }

    /**
     * Retorna uma nova instância de 'TipoDeclaracaoAtividadeRepository'.
     *
     * @return TipoDeclaracaoAtividadeRepository|mixed
     */
    private function getTipoDeclaracaoAtividadeRepository()
    {
        if (empty($this->tipoDeclaracaoAtividadeRepository)) {
            $this->tipoDeclaracaoAtividadeRepository = $this->getRepository(TipoDeclaracaoAtividade::class);
        }

        return $this->tipoDeclaracaoAtividadeRepository;
    }

    /**
     * Retorna uma instância de 'EmailAtividadeSecundariaTipoBO'
     *
     * @return EmailAtividadeSecundariaTipoBO
     */
    private function getEmailAtividadeSecundariaTipoBO(): EmailAtividadeSecundariaTipoBO
    {
        if (empty($this->emailAtividadeSecundariaTipoBO)) {
            $this->emailAtividadeSecundariaTipoBO = app()->make(EmailAtividadeSecundariaTipoBO::class);
        }

        return $this->emailAtividadeSecundariaTipoBO;
    }

    /**
     * Retorna uma instância de 'HistoricoBO'
     *
     * @return HistoricoBO
     */
    private function getHistoricoBO(): HistoricoBO
    {
        if (empty($this->historicoBO)) {
            $this->historicoBO = app()->make(HistoricoBO::class);
        }

        return $this->historicoBO;
    }

    /**
     * Retorna uma instância de 'UfCalendarioBO'
     *
     * @return UfCalendarioBO
     */
    private function getUfCalendarioBO(): UfCalendarioBO
    {
        if (empty($this->ufCalendarioBO)) {
            $this->ufCalendarioBO = app()->make(UfCalendarioBO::class);
        }

        return $this->ufCalendarioBO;
    }

    /**
     * Retorna uma nova instância de 'DeclaracaoAtividadeBO'.
     *
     * @return DeclaracaoAtividadeBO|mixed
     */
    private function getDeclaracaoAtividadeBO()
    {
        if (empty($this->declaracaoAtividadeBO)) {
            $this->declaracaoAtividadeBO = app()->make(DeclaracaoAtividadeBO::class);
        }

        return $this->declaracaoAtividadeBO;
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
     * Retorna uma nova instância de 'DeclaracaoBO'.
     *
     * @return DeclaracaoBO|mixed
     */
    private function getDeclaracaoBO()
    {
        if (empty($this->declaracaoBO)) {
            $this->declaracaoBO = app()->make(DeclaracaoBO::class);
        }

        return $this->declaracaoBO;
    }
}
