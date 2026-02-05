<?php
/*
 * ChapaEleicaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\JulgamentoImpugnacao;
use App\Entities\PedidoImpugnacao;
use App\Entities\RecursoImpugnacao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailRecursoJulgamentoImpugnacaoJob;
use App\Repository\RecursoImpugnacaoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\AtividadeSecundariaCalendarioTO;
use App\To\EleicaoTO;
use App\To\PedidoImpugnacaoTO;
use App\To\RecursoImpugnacaoTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'RecursoImpugnacao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class RecursoImpugnacaoBO extends AbstractBO
{

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var JulgamentoImpugnacaoBO
     */
    private $julgamentoImpugnacaoBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var PedidoImpugnacaoBO
     */
    private $pedidoImpugnacaoBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var RecursoImpugnacaoRepository
     */
    private $recursoImpugnacaoRepository;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var HistoricoProfissionalBO
     */
    private $historicoProfissionalBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Retorna os Recursos do Julgamento do Pedido de Impugnação conforme o id do pedido informado com a contrarrazao
     * cadastrada.
     *
     * @param $idPedidoImpugnacao
     * @param $idTipoSolicitacao
     * @return RecursoImpugnacaoTO[]|array
     */
    public function getTodosPorPedidoImpugnacaoAndTipoSolicitacao($idPedidoImpugnacao, $idTipoSolicitacao)
    {
        $recursoImpugnacaoTO = $this->getRecursoImpugnacaoRepository()->getTodosPorPedidoImpugnacao($idPedidoImpugnacao, $idTipoSolicitacao);
        return $recursoImpugnacaoTO;
    }


    /**
     * Retorna o Recurso do Julgamento do Pedido de Impugnação conforme o id do pedido informado.
     *
     * @param $idPedidoImpugnacao
     *
     * @param $idTipoSolicitacao
     * @param bool $isRetornarContrarrazao
     * @return RecursoImpugnacaoTO|null
     */
    public function getPorPedidoImpugnacaoAndTipoSolicitacao($idPedidoImpugnacao, $idTipoSolicitacao,
                                                             $isRetornarContrarrazao = false)
    {
        $recursoImpugnacaoTO = $this->getRecursoImpugnacaoRepository()->getPorPedidoImpugnacaoAndTipoSolicitacao(
            $idPedidoImpugnacao, $idTipoSolicitacao, $isRetornarContrarrazao
        );
        $eleicao = $this->getEleicaoBO()->getEleicaoPorPedidoImpugnacao($idPedidoImpugnacao, true);

        if(!empty($recursoImpugnacaoTO)){
            $this->atribuirFlagVarificacaoCadastroContrarrazao($eleicao, $recursoImpugnacaoTO);
        }

        return $recursoImpugnacaoTO;
    }

    /**
     * Salva o recurso de julgamento de pedido de impugnação
     *
     * @param RecursoImpugnacaoTO $recursoImpugnacaoTO
     * @return RecursoImpugnacaoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function salvar(RecursoImpugnacaoTO $recursoImpugnacaoTO)
    {
        $arquivos = $recursoImpugnacaoTO->getArquivos();

        /** @var ArquivoGenericoTO|null $arquivo */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validacaoInicialSalvarRercurso($recursoImpugnacaoTO, $arquivo);

        $julgamentoImpugnacao = $this->getJulgamentoImpugnacaoBO()->findById(
            $recursoImpugnacaoTO->getIdJulgamentoImpugnacao()
        );

        $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoImpugnacao(
            $julgamentoImpugnacao->getPedidoImpugnacao()->getId()
        );

        $this->validacaoComplementarSalvarRercurso($recursoImpugnacaoTO, $julgamentoImpugnacao, $eleicaoTO);

        try {
            $this->beginTransaction();

            $recursoImpugnacao = $this->prepararRecursoSalvar(
                $recursoImpugnacaoTO, $julgamentoImpugnacao, $arquivo
            );

            $this->getRecursoImpugnacaoRepository()->persist($recursoImpugnacao);

            $this->salvarHistoricoRecursoImpugnacao($recursoImpugnacao, $julgamentoImpugnacao->getPedidoImpugnacao());

            $idStatusPedido = $julgamentoImpugnacao->getPedidoImpugnacao()->getStatusPedidoImpugnacao()->getId();
            if ($idStatusPedido < Constants::STATUS_IMPUGNACAO_RECURSO_EM_ANALISE) {
                $this->getPedidoImpugnacaoBO()->atualizarStatusPedido(
                    $julgamentoImpugnacao->getPedidoImpugnacao(),
                    Constants::STATUS_IMPUGNACAO_RECURSO_EM_ANALISE
                );
            }

            if (!empty($arquivo)) {
                $this->salvarArquivo(
                    $recursoImpugnacao->getId(), $arquivo->getArquivo(), $recursoImpugnacao->getNomeArquivoFisico()
                );
            }

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailRecursoJulgamentoImpugnacaoJob($recursoImpugnacao->getId()));

        $recursoImpugnacaoTOSalvo = RecursoImpugnacaoTO::newInstanceFromEntity($recursoImpugnacao);
        $this->atribuirFlagVarificacaoCadastroContrarrazao($eleicaoTO, $recursoImpugnacaoTOSalvo);

        return $recursoImpugnacaoTOSalvo;
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoRecursoImpugnacao($id)
    {
        /** @var RecursoImpugnacao $recursoImpugnacao */
        $recursoImpugnacao = $this->getRecursoImpugnacaoRepository()->find($id);

        if (!empty($recursoImpugnacao)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioRecursoImpugnacao(
                $recursoImpugnacao->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $recursoImpugnacao->getNomeArquivoFisico(),
                $recursoImpugnacao->getNomeArquivo()
            );
        }
    }

    /**
     * Realiza o envio de e-mails após o cadastro do recurso do julgamento
     *
     * @param $idRecursoImpugnacao
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailCadastroRecursoImpugnacao($idRecursoImpugnacao)
    {
        /** @var RecursoImpugnacao $recursoImpugnacao */
        $recursoImpugnacao = $this->getRecursoImpugnacaoRepository()->find($idRecursoImpugnacao);

        $idCalendario = $this->getJulgamentoImpugnacaoBO()->getIdCalendarioJulgamentoImpugnacao(
            $recursoImpugnacao->getJulgamentoImpugnacao()->getId()
        );

        $atividadeRecurso = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $idCalendario, 3, 4
        );

        $pedidoImpugnacao = $recursoImpugnacao->getJulgamentoImpugnacao()->getPedidoImpugnacao();
        $idTipoCandidatura = $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getTipoCandidatura()->getId();

        $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
        $idCauUf = $isIES ? null : $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getIdCauUf();

        $paramsEmail = $this->prepararParametrosEmailRecurso($recursoImpugnacao, $isIES);

        $emailsResponsaveis = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa(
            $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getId()
        );

        $destinariosComissao = [];
        /*$destinariosComissao = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $atividadeRecurso->getId(), $idCauUf
        );*/

        $destinariosAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        $destinarios = array_merge(
            $destinariosComissao,
            $destinariosAssessores,
            $emailsResponsaveis,
            [$pedidoImpugnacao->getProfissional()->getPessoa()->getEmail()]
        );

        $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
            $atividadeRecurso->getId(),
            $destinarios,
            $isIES ? Constants::EMAIL_RECONSIDERACAO_IMPUGNACAO : Constants::EMAIL_RECURSO_IMPUGNACAO,
            Constants::TEMPLATE_EMAIL_RECURSO_JULGAMENTO_IMPUGNACAO,
            $paramsEmail
        );
    }

    /**
     * Método faz validação se pode ser cadastrado o recurso
     *
     * @param RecursoImpugnacaoTO $recursoImpugnacaoTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     */
    private function validacaoInicialSalvarRercurso($recursoImpugnacaoTO, $arquivo)
    {
        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Message::MSG_VISUALIZACAO_NAO_PERMITIDA_ATIV_SELECIONADA);
        }

        if (empty($recursoImpugnacaoTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($recursoImpugnacaoTO->getIdJulgamentoImpugnacao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $idsTiposPermitidos = [
            Constants::TP_SOLICITACAO_RECURSO_IMPUGNANTE,
            Constants::TP_SOLICITACAO_RECURSO_RESPONSAVEL_CHAPA,
        ];
        if (
            empty($recursoImpugnacaoTO->getIdTipoSolicitacaoRecursoImpugnacao())
            && in_array($recursoImpugnacaoTO->getIdTipoSolicitacaoRecursoImpugnacao(), $idsTiposPermitidos)
        ) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (!empty($arquivo)) {
            $this->getArquivoService()->validarArquivoGenrico(
                $arquivo, Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_10MB
            );

            if (empty($arquivo->getArquivo())) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
            }
        }
    }

    /**
     * Método faz validação complementar do cadastrado do recurso
     *
     * @param RecursoImpugnacaoTO $recursoImpugnacaoTO
     * @param JulgamentoImpugnacao $julgamentoImpugnacao
     * @param $eleicaoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function validacaoComplementarSalvarRercurso($recursoImpugnacaoTO, $julgamentoImpugnacao, $eleicaoTO)
    {
        if (empty($julgamentoImpugnacao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $recursoAnterior = $this->getRecursoImpugnacaoRepository()->findBy([
            'julgamentoImpugnacao' => $julgamentoImpugnacao->getId(),
            'tipoSolicitacaoRecursoImpugnacao' => $recursoImpugnacaoTO->getIdTipoSolicitacaoRecursoImpugnacao()
        ]);

        if (!empty($recursoAnterior)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if (empty($eleicaoTO)) {
            throw new NegocioException(Message::MSG_PERIODO_VIGENCIA_FECHADO_ELEICAO_SELECIONADA);
        }

        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $eleicaoTO->getCalendario()->getId(), 3, 4
        );

        $inicioVigente = Utils::getDataHoraZero() >= Utils::getDataHoraZero($atividadeSecundaria->getDataInicio());
        $fimVigente = Utils::getDataHoraZero() <= Utils::getDataHoraZero($atividadeSecundaria->getDataFim());
        if (!($inicioVigente && $fimVigente)) {
            throw new NegocioException(Message::MSG_PERIODO_VIGENCIA_FECHADO_ELEICAO_SELECIONADA);
        }

        $idTipoSolicitacao = $recursoImpugnacaoTO->getIdTipoSolicitacaoRecursoImpugnacao();
        $idProfissionalLogado = $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional;

        if ($idTipoSolicitacao == Constants::TP_SOLICITACAO_RECURSO_RESPONSAVEL_CHAPA) {
            $chapaEleicao = $julgamentoImpugnacao->getPedidoImpugnacao()->getMembroChapa()->getChapaEleicao();

            $isResponsavelChapa = $this->getMembroChapaBO()->isMembroResponsavelChapa(
                $chapaEleicao->getId(),
                $idProfissionalLogado
            );
            if (!$isResponsavelChapa) {
                throw new NegocioException(Message::MSG_VISUALIZACAO_NAO_PERMITIDA_ATIV_SELECIONADA);
            }
        } else {
            $idProfissionalImpugnante = $julgamentoImpugnacao->getPedidoImpugnacao()->getProfissional()->getId();

            if ($idProfissionalImpugnante != $idProfissionalLogado) {
                throw new NegocioException(Message::MSG_VISUALIZACAO_NAO_PERMITIDA_ATIV_SELECIONADA);
            }
        }
    }

    /**
     * Método auxiliar para preparar entidade RecursoImpugnacao para cadastro
     *
     * @param RecursoImpugnacaoTO $recursoImpugnacaoTO
     * @param JulgamentoImpugnacao|null $julgamentoImpugnacao
     * @param ArquivoGenericoTO|null $arquivo
     * @return RecursoImpugnacao
     * @throws Exception
     */
    private function prepararRecursoSalvar($recursoImpugnacaoTO, $julgamentoImpugnacao, $arquivo)
    {
        $nomeArquivo = null;
        $nomeArquivoFisico = null;
        if (!empty($arquivo)) {
            $nomeArquivo = $arquivo->getNome();
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getNome(), Constants::PREFIXO_ARQ_RECURSO_JULGAMENTO_IMPUG
            );
        }

        $recursoImpugnacao = RecursoImpugnacao::newInstance([
            'nomeArquivo' => $nomeArquivo,
            'dataCadastro' => Utils::getData(),
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'descricao' => $recursoImpugnacaoTO->getDescricao(),
            'profissional' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional],
            'tipoSolicitacaoRecursoImpugnacao' => ['id' => $recursoImpugnacaoTO->getIdTipoSolicitacaoRecursoImpugnacao()],
        ]);
        $recursoImpugnacao->setJulgamentoImpugnacao($julgamentoImpugnacao);

        return $recursoImpugnacao;
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $idRecursoSubstituicao
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idRecursoSubstituicao, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioRecursoImpugnacao($idRecursoSubstituicao),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do recurso de impugnação
     *
     * @param RecursoImpugnacao $recursoImpugnacao
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @throws Exception
     */
    private function salvarHistoricoRecursoImpugnacao(
        RecursoImpugnacao $recursoImpugnacao,
        PedidoImpugnacao $pedidoImpugnacao
    ): void
    {
        $idTipoCandidatura = $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getTipoCandidatura()->getId();

        $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
        $label = $isIES ? Constants::LABEL_RECONSIDERACAO : Constants::LABEL_RECURSO;

        $historico = $this->getHistoricoProfissionalBO()->criarHistorico(
            $recursoImpugnacao->getId(),
            Constants::HISTORICO_PROF_TIPO_RECURSO_JULGAMENTO_IMPUGNACAO,
            Constants::HISTORICO_PROF_ACAO_INSERIR,
            sprintf(Constants::HISTORICO_INCLUSAO_RECURSO_IMPUGNACAO, $label)
        );
        $this->getHistoricoProfissionalBO()->salvar($historico);
    }

    /**
     * Método auxiliar que prepara os parâmetros para o envio de e-mails
     *
     * @param RecursoImpugnacao $recursoImpugnacao
     * @param bool $isIES
     * @return array
     */
    private function prepararParametrosEmailRecurso(
        RecursoImpugnacao $recursoImpugnacao,
        bool $isIES
    )
    {
        $label_recurso = $isIES ? Constants::LABEL_RECONSIDERACAO : Constants::LABEL_RECURSO;
        $descricao_recurso = $recursoImpugnacao->getTipoSolicitacaoRecursoImpugnacao()->getDescricao();

        $label_interpor_recurso = $isIES ? Constants::LABEL_INTERPOR_RECONSIDERACAO : Constants::LABEL_INTERPOR_RECURSO;

        $pedidoImpugnacaoTO = PedidoImpugnacaoTO::newInstanceFromEntity(
            $recursoImpugnacao->getJulgamentoImpugnacao()->getPedidoImpugnacao()
        );

        return array(
            Constants::PARAMETRO_EMAIL_NM_PROTOCOLO => $pedidoImpugnacaoTO->getNumeroProtocolo(),
            Constants::PARAMETRO_EMAIL_LABEL_RECURSO => $label_recurso,
            Constants::PARAMETRO_EMAIL_DS_RECURSO => $descricao_recurso,
            Constants::PARAMETRO_EMAIL_LABEL_INTERPOR_RECURSO => $label_interpor_recurso,
            Constants::PARAMETRO_EMAIL_DS_INTERPOR_RECURSO => $recursoImpugnacao->getDescricao()
        );
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
     * Retorna uma nova instância de 'ArquivoService'.
     *
     * @return ArquivoService|mixed
     */
    private function getArquivoService()
    {
        if (empty($this->arquivoService)) {
            $this->arquivoService = app()->make(ArquivoService::class);
        }

        return $this->arquivoService;
    }

    /**
     * Retorna uma nova instância de 'RecursoImpugnacaoRepository'.
     *
     * @return RecursoImpugnacaoRepository
     */
    private function getRecursoImpugnacaoRepository()
    {
        if (empty($this->recursoImpugnacaoRepository)) {
            $this->recursoImpugnacaoRepository = $this->getRepository(RecursoImpugnacao::class);
        }

        return $this->recursoImpugnacaoRepository;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoImpugnacaoBO'.
     *
     * @return JulgamentoImpugnacaoBO
     */
    private function getJulgamentoImpugnacaoBO()
    {
        if (empty($this->julgamentoImpugnacaoBO)) {
            $this->julgamentoImpugnacaoBO = app()->make(JulgamentoImpugnacaoBO::class);
        }

        return $this->julgamentoImpugnacaoBO;
    }

    /**
     * Retorna uma nova instância de 'AtividadeSecundariaCalendarioBO'.
     *
     * @return AtividadeSecundariaCalendarioBO|mixed
     */
    private function getAtividadeSecundariaCalendarioBO()
    {
        if (empty($this->atividadeSecundariaCalendarioBO)) {
            $this->atividadeSecundariaCalendarioBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }

        return $this->atividadeSecundariaCalendarioBO;
    }

    /**
     * Retorna uma nova instância de 'MembroChapaBO'.
     *
     * @return MembroChapaBO|mixed
     */
    private function getMembroChapaBO()
    {
        if (empty($this->membroChapaBO)) {
            $this->membroChapaBO = app()->make(MembroChapaBO::class);
        }

        return $this->membroChapaBO;
    }

    /**
     * Retorna uma nova instância de 'PedidoImpugnacaoBO'.
     *
     * @return PedidoImpugnacaoBO|mixed
     */
    private function getPedidoImpugnacaoBO()
    {
        if (empty($this->pedidoImpugnacaoBO)) {
            $this->pedidoImpugnacaoBO = app()->make(PedidoImpugnacaoBO::class);
        }

        return $this->pedidoImpugnacaoBO;
    }

    /**
     * Retorna uma nova instância de 'EmailAtividadeSecundariaBO'.
     *
     * @return EmailAtividadeSecundariaBO|mixed
     */
    private function getEmailAtividadeSecundariaBO()
    {
        if (empty($this->emailAtividadeSecundariaBO)) {
            $this->emailAtividadeSecundariaBO = app()->make(EmailAtividadeSecundariaBO::class);
        }

        return $this->emailAtividadeSecundariaBO;
    }

    /**
     * Retorna uma nova instância de 'CorporativoService'.
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
     * Retorna uma nova instância de 'HistoricoProfissionalBO'.
     *
     * @return HistoricoProfissionalBO
     */
    private function getHistoricoProfissionalBO()
    {
        if (empty($this->historicoProfissionalBO)) {
            $this->historicoProfissionalBO = app()->make(HistoricoProfissionalBO::class);
        }

        return $this->historicoProfissionalBO;
    }

    /**
     * Retorna o recurso do pedido de impugnação chapa conforme o id informado.
     *
     * @param $id
     *
     * @return RecursoImpugnacao|null
     */
    public function findById($id)
    {
        /** @var RecursoImpugnacao $recursoImpugnacao */
        $recursoImpugnacao = $this->getRecursoImpugnacaoRepository()->find($id);

        return $recursoImpugnacao;
    }

    /**
     * Método auxiliar para adicionar flag se pode cadastrar contrarrazão
     * @param EleicaoTO $eleicao
     * @param RecursoImpugnacaoTO $recursoImpugnacaoTO
     */
    public function atribuirFlagVarificacaoCadastroContrarrazao($eleicao, $recursoImpugnacaoTO): void
    {
        $podeCadastrarContrarrazao = $this->getAtividadeSecundariaCalendarioBO()->isAtividadeVigente(
            $eleicao->getCalendario()->getId(), 3, 5
        );

        $recursoImpugnacaoTO->setPodeCadastrarContrarrazao($podeCadastrarContrarrazao);
    }

}




