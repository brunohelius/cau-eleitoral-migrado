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
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\JulgamentoRecursoImpugnacao;
use App\Entities\PedidoImpugnacao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailJulgamentoRecursoImpugnacaoJob;
use App\Mail\JulgamentoRecursoImpugnacaoCadastradoMail;
use App\Repository\JulgamentoRecursoImpugnacaoRepository;
use App\Service\ArquivoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\JulgamentoRecursoImpugnacaoTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'JulgamentoRecursoImpugnacao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoImpugnacaoBO extends AbstractBO
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
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var PedidoImpugnacaoBO
     */
    private $pedidoImpugnacaoBO;

    /**
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * @var JulgamentoRecursoImpugnacaoRepository
     */
    private $julgamentoRecursoImpugnacaoRepository;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var RecursoImpugnacaoBO
     */
    private $recursoImpugnacaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Retorna um Julgamento do Recurso de Impugnação  conforme o id informado.
     *
     * @param $idPedidoImpugnacao
     * @param bool $verificarUsuarioResponsavel
     * @param bool $verificarUsuarioMembroComissao
     * @return JulgamentoRecursoImpugnacaoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getPorPedidoImpugnacao(
        $idPedidoImpugnacao,
        $verificarUsuarioResponsavel = false,
        $verificarUsuarioMembroComissao = false
    ) {
        $julgamentoRecursoImpugnacaoTO = null;

        $isProfissional = $this->getUsuarioFactory()->isProfissional();
        if ($isProfissional && ($verificarUsuarioResponsavel || $verificarUsuarioMembroComissao)) {
            $julgamentoRecursoImpugnacaoTO = $this->getPorPedidoImpugnacaoComVerificacaoUsuario(
                $idPedidoImpugnacao,
                $verificarUsuarioResponsavel,
                $verificarUsuarioMembroComissao
            );
        } else {
            $julgamentoRecursoImpugnacaoTO = $this->getJulgamentoRecursoImpugnacaoRepository()->getPorPedidoImpugnacao(
                $idPedidoImpugnacao
            );
        }

        return $julgamentoRecursoImpugnacaoTO;
    }

    /**
     * Salva o julgamento do recurso de substituição (2ª instância)
     *
     * @param JulgamentoRecursoImpugnacaoTO $julgamentoRecursoImpugnacaoTO
     * @return JulgamentoRecursoImpugnacaoTO
     * @throws NegocioException
     */
    public function salvar(JulgamentoRecursoImpugnacaoTO $julgamentoRecursoImpugnacaoTO)
    {
        $arquivos = $julgamentoRecursoImpugnacaoTO->getArquivos();

        /** @var ArquivoGenericoTO|null $arquivo */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validacaoIncialSalvarJulgamentoRecurso($julgamentoRecursoImpugnacaoTO, $arquivo);

        /** @var PedidoImpugnacao $pedidoImpugnacao */
        $pedidoImpugnacao = $this->getPedidoImpugnacaoBO()->findById(
            $julgamentoRecursoImpugnacaoTO->getIdPedidoImpugnacao()
        );

        $eleicao = $this->getEleicaoBO()->getEleicaoPorPedidoImpugnacao(
            $julgamentoRecursoImpugnacaoTO->getIdPedidoImpugnacao()
        );

        $this->validacaoComplementarSalvarJulgamentoRercurso($pedidoImpugnacao, $eleicao);

        try {
            $this->beginTransaction();

            $julgamentoRecursoImpugnacao = $this->prepararJulgamentoSalvar(
                $julgamentoRecursoImpugnacaoTO, $pedidoImpugnacao, $arquivo
            );

            $this->getJulgamentoRecursoImpugnacaoRepository()->persist($julgamentoRecursoImpugnacao);

            $this->salvarHistoricoJulgamentoImpugnacao($julgamentoRecursoImpugnacao);

            $this->getPedidoImpugnacaoBO()->atualizarPedidoImpugnacaoPosJulgamentoRecurso(
                $pedidoImpugnacao, $julgamentoRecursoImpugnacaoTO->getIdStatusJulgamentoImpugnacao()
            );

            $this->salvarArquivo(
                $julgamentoRecursoImpugnacao->getId(),
                $arquivo->getArquivo(),
                $julgamentoRecursoImpugnacao->getNomeArquivoFisico()
            );

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailJulgamentoRecursoImpugnacaoJob(
            $julgamentoRecursoImpugnacao->getId(), $eleicao->getCalendario()->getId()
        ));

        return JulgamentoRecursoImpugnacaoTO::newInstanceFromEntity($julgamentoRecursoImpugnacao);
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoJulgamentoRecursoImpugnacao($id)
    {
        /** @var JulgamentoRecursoImpugnacao $julgamentoRecursoImpugnacao */
        $julgamentoRecursoImpugnacao = $this->getJulgamentoRecursoImpugnacaoRepository()->find($id);

        if (!empty($julgamentoRecursoImpugnacao)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoRecursoImpugnacao(
                $julgamentoRecursoImpugnacao->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $julgamentoRecursoImpugnacao->getNomeArquivoFisico(),
                $julgamentoRecursoImpugnacao->getNomeArquivo()
            );
        }
    }

    /**
     * Método realiza o envio de e-mail no ínicio da atividade 3.6
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailIncioPeriodoJulgamento()
    {
        $idTipo = Constants::EMAIL_JULGAMENTO_RECURSO_IMPUGNACAO_ASSESSORES_PERIODO_SERA_ABERTO;
        $this->getEmailAtividadeSecundariaBO()->enviarEmailIncioPeriodoJulgamentoChapa(3, 6, $idTipo);
    }

    /**
     * Método realiza o envio de e-mail no fim da atividade 3.6
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailFimPeriodoJulgamento()
    {
        $idTipo = Constants::EMAIL_JULGAMENTO_IMPUGNACAO_ASSESSORES_PERIODO_SERA_FECHADO;
        $idStatusPedido = Constants::STATUS_IMPUGNACAO_RECURSO_EM_ANALISE;
        $this->getPedidoImpugnacaoBO()->enviarEmailFimPeriodoJulgamento(3, 6, $idStatusPedido, $idTipo);
    }

    /**
     * Responsável por realizar envio de e-mail após o cadastro do julgamento
     *
     * @param $idJulgamento
     * @param $idCalendario
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailCadastroJulgamento($idJulgamento, $idCalendario)
    {
        /** @var JulgamentoRecursoImpugnacao $julgamento */
        $julgamento = $this->getJulgamentoRecursoImpugnacaoRepository()->find($idJulgamento);
        $julgamentoRecursoImpugnacaoTO = JulgamentoRecursoImpugnacaoTO::newInstanceFromEntity($julgamento);

        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $idCalendario, 3, 6
        );

        $isAdicionarEmailsResponsaveis = Utils::getDataHoraZero() > Utils::getDataHoraZero($atividade->getDataFim());

        $destinatarios = $this->getPedidoImpugnacaoBO()->recuperaDestinatariosPorPedidoImpugnacao(
            $julgamento->getPedidoImpugnacao(), $atividade->getId(), $isAdicionarEmailsResponsaveis
        );

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(),
                $this->getTipoEmailCadastroJulgamento($julgamento->getStatusJulgamentoImpugnacao()->getId())
            );

            if (!empty($emailAtividadeSecundaria)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios($destinatarios);

                Email::enviarMail(new JulgamentoRecursoImpugnacaoCadastradoMail(
                    $emailTO, $julgamentoRecursoImpugnacaoTO
                ));
            }
        }
    }

    /**
     * Envia e-mail para o julgamento no ínicio da atividade de recurso
     *
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function enviarEmailCadastroJulgamentoFimPeriodo()
    {
        $dataFim = Utils::subtrairDiasData(Utils::getDataHoraZero(), 1);

        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            null, $dataFim, 3, 6
        );

        foreach ($atividades as $atividadeSecundaria) {

            $julgamentosRecursoImpugnacaoTO = $this->getJulgamentoRecursoImpugnacaoRepository()->getPorCalendario(
                $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getId(), true
            );

            foreach ($julgamentosRecursoImpugnacaoTO as $julgamentoRecursoImpugnacaoTO) {
                $destinatarios = $this->getPedidoImpugnacaoBO()->getEmailsResponsaveis(
                    $julgamentoRecursoImpugnacaoTO->getPedidoImpugnacao()
                );

                if (!empty($destinatarios)) {
                    $idStatusJulgamento = $julgamentoRecursoImpugnacaoTO->getStatusJulgamentoImpugnacao()->getId();
                    $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                        $atividadeSecundaria->getId(),
                        $this->getTipoEmailCadastroJulgamento($idStatusJulgamento)
                    );

                    if (!empty($emailAtividadeSecundaria)) {
                        $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                        $emailTO->setDestinatarios($destinatarios);

                        Email::enviarMail(new JulgamentoRecursoImpugnacaoCadastradoMail(
                            $emailTO, $julgamentoRecursoImpugnacaoTO
                        ));
                    }
                }
            }
        }
    }

    /**
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param JulgamentoRecursoImpugnacaoTO $julgamentoRecursoImpugnacaoTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     */
    private function validacaoIncialSalvarJulgamentoRecurso($julgamentoRecursoImpugnacaoTO, $arquivo)
    {
        if (!$this->getUsuarioFactory()->isCorporativoAssessorCEN()) {
            throw new NegocioException(Message::MSG_SEM_ACESSO_ATIV_SELECIONADA);
        }

        if (empty($julgamentoRecursoImpugnacaoTO->getIdPedidoImpugnacao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($julgamentoRecursoImpugnacaoTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $statusValidos = [Constants::STATUS_JULG_IMPUGNACAO_PROCEDENTE, Constants::STATUS_JULG_IMPUGNACAO_IMPROCEDENTE];
        if (
            empty($julgamentoRecursoImpugnacaoTO->getIdStatusJulgamentoImpugnacao())
            || !in_array($julgamentoRecursoImpugnacaoTO->getIdStatusJulgamentoImpugnacao(), $statusValidos)
        ) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($arquivo)) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($arquivo->getArquivo())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $this->getArquivoService()->validarArquivoGenrico(
            $arquivo, Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_10MB
        );
    }

    /**
     * Método faz validação complementar do cadastrado do julgamento do recurso
     *
     * @param PedidoImpugnacao $pedidoImpugnacao
     */
    private function validacaoComplementarSalvarJulgamentoRercurso($pedidoImpugnacao, $eleicao)
    {
        if (empty($pedidoImpugnacao) || empty($eleicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $isStatusAtual = $pedidoImpugnacao->getStatusPedidoImpugnacao()->getId();

        if ($isStatusAtual < Constants::STATUS_IMPUGNACAO_RECURSO_EM_ANALISE) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $dataInicioCalendario = Utils::getDataHoraZero($eleicao->getCalendario()->getDataInicioVigencia());
        $dataFimCalendario = Utils::getDataHoraZero($eleicao->getCalendario()->getDataFimVigencia());

        if (Utils::getDataHoraZero() < $dataInicioCalendario || Utils::getDataHoraZero() > $dataFimCalendario) {
            throw new NegocioException(Message::MSG_PERIODO_VIGENTE_ELEICAO_FECHADO);
        }

        $atividadeJulgamento = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $eleicao->getCalendario()->getId(), 3, 6
        );

        if (Utils::getDataHoraZero() < Utils::getDataHoraZero($atividadeJulgamento->getDataInicio())) {
            $recursoImpugnante = $this->getRecursoImpugnacaoBO()->getPorPedidoImpugnacaoAndTipoSolicitacao(
                $pedidoImpugnacao->getId(), Constants::TP_SOLICITACAO_RECURSO_IMPUGNANTE
            );
            if (empty($recursoImpugnante)) {
                throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
            }

            $recursoResponsavelChapa = $this->getRecursoImpugnacaoBO()->getPorPedidoImpugnacaoAndTipoSolicitacao(
                $pedidoImpugnacao->getId(), Constants::TP_SOLICITACAO_RECURSO_RESPONSAVEL_CHAPA
            );
            if (empty($recursoResponsavelChapa)) {
                throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
            }
        }
    }

    /**
     * Método auxiliar para preparar entidade JulgamentoRecursoImpugnacao para cadastro
     *
     * @param JulgamentoRecursoImpugnacaoTO $julgamentoRecursoImpugnacaoTO
     * @param PedidoImpugnacao|null $pedidoImpugnacao
     * @param ArquivoGenericoTO|null $arquivo
     * @return JulgamentoRecursoImpugnacao
     * @throws Exception
     */
    private function prepararJulgamentoSalvar($julgamentoRecursoImpugnacaoTO, $pedidoImpugnacao, $arquivo)
    {
        $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
            $arquivo->getNome(), Constants::PREFIXO_ARQ_JULGAMENTO_RECURSO_IMPUGN
        );

        $idStatus = $julgamentoRecursoImpugnacaoTO->getIdStatusJulgamentoImpugnacao();

        $julgamentoRecursoImpugnacao = JulgamentoRecursoImpugnacao::newInstance([
            'dataCadastro' => Utils::getData(),
            'nomeArquivo' => $arquivo->getNome(),
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'statusJulgamentoImpugnacao' => ['id' => $idStatus],
            'descricao' => $julgamentoRecursoImpugnacaoTO->getDescricao(),
            'usuario' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->id],
        ]);
        $julgamentoRecursoImpugnacao->setPedidoImpugnacao($pedidoImpugnacao);

        return $julgamentoRecursoImpugnacao;
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $idJulgamentoImpugnacao
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idJulgamentoImpugnacao, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioJulgamentoRecursoImpugnacao($idJulgamentoImpugnacao),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param JulgamentoRecursoImpugnacao $julgamentoRecursoImpugnacao
     * @throws Exception
     */
    private function salvarHistoricoJulgamentoImpugnacao(
        JulgamentoRecursoImpugnacao $julgamentoRecursoImpugnacao
    ): void {
        $historico = $this->getHistoricoBO()->criarHistorico(
            $julgamentoRecursoImpugnacao,
            Constants::HISTORICO_ID_TIPO_JUGAMENTO_RECURSO_IMPUGNACAO,
            Constants::HISTORICO_ACAO_INSERIR,
            Constants::HISTORICO_INCLUSAO_JULG_RECURSO_IMPUGNACAO
        );
        $this->getHistoricoBO()->salvar($historico);
    }

    /**
     * Verifica se usuário pode visualizar julgamento de acordo com as seguinte regras:
     * - Deve ser um membro da comissão CEN ou Ce da Cau UF da chapa vinculada ao julgamento
     *   se parâmetro para verificar estiver habilitado
     * - Deve ser um responsável da chapa ou impugnante vinculada ao julgamento
     *   se parâmetro para verificar estiver habilitado e a atividade de recurso dete estar iniciada
     *
     * @param $idPedidoImpugnacao
     * @param bool $verificarUsuarioResponsavel
     * @param bool $verificarUsuarioMembroComissao
     * @return JulgamentoRecursoImpugnacaoTO|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function getPorPedidoImpugnacaoComVerificacaoUsuario(
        $idPedidoImpugnacao,
        $verificarUsuarioResponsavel,
        $verificarUsuarioMembroComissao
    ) {
        $isPermitidoVisualizar = false;

        /** @var JulgamentoRecursoImpugnacao $julgamentoRecursoImpugnacao */
        $julgamentoRecursoImpugnacao = $this->getJulgamentoRecursoImpugnacaoRepository()->findOneBy([
            'pedidoImpugnacao' => $idPedidoImpugnacao
        ]);
        $julgamentoRecursoImpugnacaoTO = null;

        if (!empty($julgamentoRecursoImpugnacao)) {
            $chapaEleicao = $julgamentoRecursoImpugnacao->getPedidoImpugnacao()->getMembroChapa()->getChapaEleicao();
            $isIES = $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;

            $eleicao = $this->getEleicaoBO()->getEleicaoPorPedidoImpugnacao($idPedidoImpugnacao);

            if ($verificarUsuarioMembroComissao) {
                $isMembroComissao = $this->getMembroComissaoBO()->verificarMembroComissaoPorCauUf(
                    $eleicao->getCalendario()->getId(), $chapaEleicao->getIdCauUf(), $isIES
                );
                if ($isMembroComissao) {
                    $isPermitidoVisualizar = true;
                }
            }

            if ($verificarUsuarioResponsavel) {
                $ativSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                    $eleicao->getCalendario()->getId(), 3, 6
                );
                $isFinalizadoAtiv = Utils::getDataHoraZero() > Utils::getDataHoraZero($ativSecundaria->getDataFim());

                if ($isFinalizadoAtiv) {
                    $idProfissionalLogado = $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional;
                    $profissionalPedido = $julgamentoRecursoImpugnacao->getPedidoImpugnacao()->getProfissional();

                    if ($profissionalPedido->getId() == $idProfissionalLogado) {
                        $isPermitidoVisualizar = true;
                    } else {
                        $idChapaEleicao = $this->getChapaEleicaoBO()->getIdChapaEleicaoPorCalendarioEResponsavel(
                            $eleicao->getCalendario()->getId(), $idProfissionalLogado
                        );
                        if (!empty($idChapaEleicao) && $idChapaEleicao == $chapaEleicao->getId()) {
                            $isPermitidoVisualizar = true;
                        }
                    }
                }
            }

            $julgamentoRecursoImpugnacaoTO = JulgamentoRecursoImpugnacaoTO::newInstanceFromEntity
            ($julgamentoRecursoImpugnacao);

            $podeCadastrarContrarrazao = $this->getAtividadeSecundariaCalendarioBO()->isAtividadeVigente
            ($eleicao->getCalendario()->getId(), 3, 5);

            $julgamentoRecursoImpugnacaoTO->setPodeCadastrarContrarrazao($podeCadastrarContrarrazao);
        }

        return $isPermitidoVisualizar ? $julgamentoRecursoImpugnacaoTO : null;
    }

    /**
     * Retorna uma nova instância de 'MembroComissaoBO'.
     *
     * @return MembroComissaoBO
     */
    private function getMembroComissaoBO()
    {
        if (empty($this->membroComissaoBO)) {
            $this->membroComissaoBO = app()->make(MembroComissaoBO::class);
        }

        return $this->membroComissaoBO;
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
     * Retorna uma nova instância de 'HistoricoChapaEleicaoBO'.
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
     * Retorna uma nova instância de 'JulgamentoRecursoImpugnacaoRepository'.
     *
     * @return JulgamentoRecursoImpugnacaoRepository
     */
    private function getJulgamentoRecursoImpugnacaoRepository()
    {
        if (empty($this->julgamentoRecursoImpugnacaoRepository)) {
            $this->julgamentoRecursoImpugnacaoRepository = $this->getRepository(
                JulgamentoRecursoImpugnacao::class
            );
        }

        return $this->julgamentoRecursoImpugnacaoRepository;
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
     * Retorna uma nova instância de 'RecursoImpugnacaoBO'.
     *
     * @return RecursoImpugnacaoBO
     */
    private function getRecursoImpugnacaoBO()
    {
        if (empty($this->recursoImpugnacaoBO)) {
            $this->recursoImpugnacaoBO = app()->make(RecursoImpugnacaoBO::class);
        }

        return $this->recursoImpugnacaoBO;
    }

    /**
     * @param JulgamentoRecursoImpugnacao $julgamento
     * @return int
     */
    public function getTipoEmailCadastroJulgamento($idStatusJulgamento): int
    {
        $idTipoEmail = Constants::EMAIL_JULGAMENTO_RECURSO_IMPUGNACAO_PROCEDENTE;
        if ($idStatusJulgamento == Constants::STATUS_JULG_IMPUGNACAO_IMPROCEDENTE) {
            $idTipoEmail = Constants::EMAIL_JULGAMENTO_RECURSO_IMPUGNACAO_IMPROCEDENTE;
        }
        return $idTipoEmail;
    }
}




