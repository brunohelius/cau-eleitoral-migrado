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
use App\Entities\JulgamentoRecursoSubstituicao;
use App\Entities\RecursoSubstituicao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailJulgamentoRecursoSubstituicaoJob;
use App\Repository\JulgamentoRecursoSubstituicaoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\AtividadeSecundariaCalendarioTO;
use App\To\JulgamentoRecursoSubstituicaoTO;
use App\To\PedidoSubstituicaoChapaTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'JulgamentoRecursoSubstituicao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoSubstituicaoBO extends AbstractBO
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
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * @var PedidoSubstituicaoChapaBO
     */
    private $pedidoSubstituicaoChapaBO;

    /**
     * @var RecursoSubstituicaoBO
     */
    private $recursoSubstituicaoBO;

    /**
     * @var JulgamentoSubstituicaoBO
     */
    private $julgamentoSubstituicaoBO;

    /**
     * @var JulgamentoRecursoSubstituicaoRepository
     */
    private $julgamentoRecursoSubstituicaoRepository;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Retorna um Julgamento de Substituição conforme o id informado.
     *
     * @param $idPedidoSubstituicao
     *
     * @return JulgamentoRecursoSubstituicaoTO
     */
    public function getPorPedidoSubstituicao(
        $idPedidoSubstituicao,
        $verificarUsuarioResponsavelChapa = false,
        $verificarUsuarioMembroComissao = false
    ) {

        $julgamentoSubstituicaoTO = null;

        $isProfissional = $this->getUsuarioFactory()->isProfissional();
        if ($isProfissional && ($verificarUsuarioResponsavelChapa || $verificarUsuarioMembroComissao)) {
            $julgamentoSubstituicaoTO = $this->getPorPedidoSubstituicaoComVerificacaoUsuario(
                $idPedidoSubstituicao,
                $verificarUsuarioResponsavelChapa,
                $verificarUsuarioMembroComissao
            );
        } else {
            $julgamentoSubstituicaoTO = $this->getJulgamentoRecursoSubstituicaoRepository()->getPorPedidosSubstituicao(
                $idPedidoSubstituicao
            );
        }


        return $julgamentoSubstituicaoTO;
    }

    /**
     * Retorna a atividade de secundária do julgamento do recurso do pedido de substituição
     *
     * @return AtividadeSecundariaCalendarioTO
     * @throws Exception
     */
    public function getAtividadeSecundariaJulgamentoRecurso($idPedidoSubstituicao)
    {
        $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoSubstituicao($idPedidoSubstituicao);

        $atividadeSecundariaTO = null;
        if (!empty($eleicaoTO)) {
            $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                $eleicaoTO->getCalendario()->getId(), 2, 6
            );

            if (!empty($atividadeSecundaria)) {
                $atividadeSecundariaTO = AtividadeSecundariaCalendarioTO::newInstanceFromEntity($atividadeSecundaria);
            }
        }

        return $atividadeSecundariaTO;
    }

    /**
     * Salva o julgamento do recurso de substituição (2ª instância)
     *
     * @param JulgamentoRecursoSubstituicaoTO $julgamentoRecursoSubstituicaoTO
     * @return JulgamentoRecursoSubstituicaoTO
     * @throws NegocioException
     */
    public function salvar(JulgamentoRecursoSubstituicaoTO $julgamentoRecursoSubstituicaoTO)
    {
        $arquivos = $julgamentoRecursoSubstituicaoTO->getArquivos();

        /** @var ArquivoGenericoTO|null $arquivo */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validacaoIncialSalvarJulgamentoRecurso($julgamentoRecursoSubstituicaoTO, $arquivo);

        /** @var RecursoSubstituicao $recursoSubstituicao */
        $recursoSubstituicao = $this->getRecursoSubstituicaoBO()->findById(
            $julgamentoRecursoSubstituicaoTO->getIdRecursoSubstituicao()
        );

        $this->validacaoComplementarSalvarJulgamentoRercurso($recursoSubstituicao);

        $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoSubstituicao(
            $recursoSubstituicao->getJulgamentoSubstituicao()->getPedidoSubstituicaoChapa()->getId()
        );
        if (empty($eleicaoTO)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        try {
            $this->beginTransaction();

            $julgamentoRecursoSubstituicao = $this->prepararJulgamentoSalvar(
                $julgamentoRecursoSubstituicaoTO, $recursoSubstituicao, $arquivo
            );

            $this->getJulgamentoRecursoSubstituicaoRepository()->persist(
                $julgamentoRecursoSubstituicao
            );

            $this->salvarHistoricoJulgamentoSubstituicao($julgamentoRecursoSubstituicao);

            $this->salvarArquivo(
                $julgamentoRecursoSubstituicao->getId(),
                $arquivo->getArquivo(),
                $julgamentoRecursoSubstituicao->getNomeArquivoFisico()
            );

            $this->getPedidoSubstituicaoChapaBO()->atualizarPedidoSubstituicaoPosJulgamentoRecurso(
                $eleicaoTO->getCalendario()->getId(),
                $recursoSubstituicao->getJulgamentoSubstituicao()->getPedidoSubstituicaoChapa(),
                $julgamentoRecursoSubstituicaoTO->getIdStatusJulgamentoSubstituicao()
            );

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new  EnviarEmailJulgamentoRecursoSubstituicaoJob($julgamentoRecursoSubstituicao->getId()));

        return JulgamentoRecursoSubstituicaoTO::newInstanceFromEntity($julgamentoRecursoSubstituicao);
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoJulgamentoRecursoSubstituicao($id)
    {
        /** @var JulgamentoRecursoSubstituicao $julgamentoRecursoSubstituicao */
        $julgamentoRecursoSubstituicao = $this->getJulgamentoRecursoSubstituicaoRepository()->find($id);

        if (!empty($julgamentoRecursoSubstituicao)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoRecursoSubstituicao(
                $julgamentoRecursoSubstituicao->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $julgamentoRecursoSubstituicao->getNomeArquivoFisico(),
                $julgamentoRecursoSubstituicao->getNomeArquivo()
            );
        }
    }

    /**
     * Método realiza o envio de e-mail no ínicio da atividade 2.6 de cadastro de julgamento do recurso de substituição
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailIncioPeriodoJulgamento()
    {
        $idTipo = Constants::EMAIL_JULGAMENTO_RECURSO_SUBST_ASSESSORES_PERIODO_SERA_ABERTO;
        $this->getEmailAtividadeSecundariaBO()->enviarEmailIncioPeriodoJulgamentoChapa(2, 6, $idTipo);
    }

    /**
     * Método realiza o envio de e-mail no ínicio da atividade 2.6 de cadastro de julgamento do recurso de substituição
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailFimPeriodoJulgamento()
    {
        $idStatus = Constants::STATUS_SUBSTITUICAO_CHAPA_RECURSO_EM_ANDAMENTO;
        $idTipo = Constants::EMAIL_JULGAMENTO_RECURSO_SUBST_ASSESSORES_PERIODO_SERA_FECHADO;
        $this->getPedidoSubstituicaoChapaBO()->enviarEmailFimPeriodoJulgamento(2, 6, $idStatus, $idTipo);
    }

    /**
     * Realiza o envio de e-mails após o cadastro do julgamento
     *
     * @param $idJulgamentoRecursoSubstituicao
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function enviarEmailCadastroJulgamento($idJulgamentoRecursoSubstituicao)
    {
        /** @var JulgamentoRecursoSubstituicao $julgamentoRecursoSubstituicao */
        $julgamentoRecursoSubstituicao = $this->getJulgamentoRecursoSubstituicaoRepository()->find(
            $idJulgamentoRecursoSubstituicao
        );
        $julgamentoSubstituicao = $julgamentoRecursoSubstituicao->getRecursoSubstituicao()->getJulgamentoSubstituicao();

        $pedidoSubstituicaoChapaTO = $this->getPedidoSubstituicaoChapaBO()->getPorId(
            $julgamentoSubstituicao->getPedidoSubstituicaoChapa()->getId(), false
        );

        $idCalendario = $this->getJulgamentoSubstituicaoBO()->getIdCalendarioJulgamentoSubstituicao(
            $julgamentoSubstituicao->getId()
        );

        $atividadeJulgamento = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $idCalendario, 2, 6
        );

        $paramsEmail = $this->prepararParametrosEmailJulgamentoRecursoSubs(
            $julgamentoRecursoSubstituicao, $pedidoSubstituicaoChapaTO
        );

        $idStatus = $julgamentoRecursoSubstituicao->getStatusJulgamentoSubstituicao()->getId();

        $idTipoEmail = $idStatus == Constants::STATUS_JULGAMENTO_DEFERIDO
            ? Constants::EMAIL_JULGAMENTO_RECURSO_SUBST_DEFERIDO
            : Constants::EMAIL_JULGAMENTO_RECURSO_SUBST_INDEFERIDO;

        $destinarios = $this->getDestinatariosEmailCadastroJulgamento($pedidoSubstituicaoChapaTO, $atividadeJulgamento);

        $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
            $atividadeJulgamento->getId(),
            $destinarios,
            $idTipoEmail,
            Constants::TEMPLATE_EMAIL_JULGAMENTO_SUBSTITUICAO,
            $paramsEmail
        );
    }

    /**
     * Envia e-mail para o julgamento no ínicio da atividade de recurso
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function enviarEmailCadastroJulgamentoFimPeriodo()
    {
        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            null, Utils::getDataHoraZero(), 2, 6
        );

        foreach ($atividades as $atividadeSecundaria) {

            $julgamentosRecursoSubstituicaoTO = $this->getJulgamentoRecursoSubstituicaoRepository()->getPorCalendario(
                $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getId()
            );

            foreach ($julgamentosRecursoSubstituicaoTO as $julgamentoRecursoSubstituicaoTO) {
                $parametrosEmail = $this->prepararParametrosEmailJulgamentoRecursoSubs(
                    $julgamentoRecursoSubstituicaoTO,
                    $julgamentoRecursoSubstituicaoTO->getPedidoSubstituicaoChapa()
                );

                $idTipoEmail = $julgamentoRecursoSubstituicaoTO->getStatusJulgamentoSubstituicao()->getId() == Constants::STATUS_JULGAMENTO_DEFERIDO
                    ? Constants::EMAIL_JULGAMENTO_RECURSO_SUBST_DEFERIDO
                    : Constants::EMAIL_JULGAMENTO_RECURSO_SUBST_INDEFERIDO;

                $destinarios = $this->getJulgamentoSubstituicaoBO()->getEmailsResponsaveisChapaEMembrosSubstituicao(
                    $julgamentoRecursoSubstituicaoTO->getPedidoSubstituicaoChapa()
                );

                $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
                    $atividadeSecundaria->getId(),
                    $destinarios,
                    $idTipoEmail,
                    Constants::TEMPLATE_EMAIL_JULGAMENTO_SUBSTITUICAO,
                    $parametrosEmail
                );
            }
        }
    }

    /**
     * Método auxiliar que prepara os parâmetros para o envio de e-mails
     *
     * @param JulgamentoRecursoSubstituicao|JulgamentoRecursoSubstituicaoTO $julgamentoRecursoSubstituicao
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @return array
     */
    private function prepararParametrosEmailJulgamentoRecursoSubs(
        JulgamentoRecursoSubstituicao $julgamentoRecursoSubstituicao,
        PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
    ) {
        $parametrosEmailPedido = $this->getPedidoSubstituicaoChapaBO()->prepararParametrosEmailPedidoSubstituicao(
            $pedidoSubstituicaoChapaTO
        );

        $desicao = $julgamentoRecursoSubstituicao->getStatusJulgamentoSubstituicao()->getDescricao();

        $parametrosEmailJulgamento = [
            Constants::PARAMETRO_EMAIL_JULGAMENTO_PARECER => $julgamentoRecursoSubstituicao->getDescricao(),
            Constants::PARAMETRO_EMAIL_JULGAMENTO_DESICAO => $desicao
        ];

        return array_merge($parametrosEmailPedido, $parametrosEmailJulgamento);
    }

    /**
     * Método auxiliar que prepara os destinatários do envio de e-mail
     *
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @param AtividadeSecundariaCalendario $atividadeJulgamento
     * @return array
     * @throws NegocioException
     */
    public function getDestinatariosEmailCadastroJulgamento($pedidoSubstituicaoChapaTO, $atividadeJulgamento)
    {
        $idTipoCandidatura = $pedidoSubstituicaoChapaTO->getChapaEleicao()->getTipoCandidatura()->getId();
        $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
        $idCauUf = $isIES ? null : $pedidoSubstituicaoChapaTO->getChapaEleicao()->getIdCauUf();

        $destinariosComissao = [];
        /*$destinariosComissao = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $atividadeJulgamento->getId(), $idCauUf
        );*/

        $destinariosAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        $destinariosResponsaveisEMembros = [];
        if (Utils::getDataHoraZero() > Utils::getDataHoraZero($atividadeJulgamento->getDataFim())) {
            $destinariosResponsaveisEMembros = $this->getJulgamentoSubstituicaoBO()->getEmailsResponsaveisChapaEMembrosSubstituicao(
                $pedidoSubstituicaoChapaTO
            );
        }

        return array_merge($destinariosComissao, $destinariosAssessores, $destinariosResponsaveisEMembros);
    }

    /**
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param JulgamentoRecursoSubstituicaoTO $julgamentoRecursoSubstituicaoTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     */
    private function validacaoIncialSalvarJulgamentoRecurso($julgamentoRecursoSubstituicaoTO, $arquivo)
    {
        if (!$this->getUsuarioFactory()->isCorporativoAssessorCEN()) {
            throw new NegocioException(Message::MSG_SEM_ACESSO_ATIV_SELECIONADA);
        }

        if (empty($julgamentoRecursoSubstituicaoTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $statusValidos = [Constants::STATUS_JULGAMENTO_DEFERIDO, Constants::STATUS_JULGAMENTO_INDEFERIDO];
        if (
            empty($julgamentoRecursoSubstituicaoTO->getIdStatusJulgamentoSubstituicao())
            || !in_array($julgamentoRecursoSubstituicaoTO->getIdStatusJulgamentoSubstituicao(), $statusValidos)
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
     * @param RecursoSubstituicao $recursoSubstituicao
     */
    private function validacaoComplementarSalvarJulgamentoRercurso($recursoSubstituicao)
    {
        if (empty($recursoSubstituicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $pedidoSubstituicao = $recursoSubstituicao->getJulgamentoSubstituicao()->getPedidoSubstituicaoChapa();
        $isStatusAtual = $pedidoSubstituicao->getStatusSubstituicaoChapa()->getId();

        if ($isStatusAtual != Constants::STATUS_SUBSTITUICAO_CHAPA_RECURSO_EM_ANDAMENTO) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }
    }

    /**
     * Método auxiliar para preparar entidade JulgamentoSubstituicao para cadastro
     *
     * @param JulgamentoRecursoSubstituicaoTO $julgamentoRecursoSubstituicaoTO
     * @param RecursoSubstituicao|null $recursoSubstituicao
     * @param ArquivoGenericoTO|null $arquivo
     * @return JulgamentoRecursoSubstituicao
     * @throws Exception
     */
    private function prepararJulgamentoSalvar($julgamentoRecursoSubstituicaoTO, $recursoSubstituicao, $arquivo)
    {
        $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
            $arquivo->getNome(), Constants::PREFIXO_ARQ_JULGAMENTO_RECURSO_SUBST
        );

        $idStatus = $julgamentoRecursoSubstituicaoTO->getIdStatusJulgamentoSubstituicao();

        $julgamentoRecursoSubstituicao = JulgamentoRecursoSubstituicao::newInstance([
            'dataCadastro' => Utils::getData(),
            'nomeArquivo' => $arquivo->getNome(),
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'statusJulgamentoSubstituicao' => ['id' => $idStatus],
            'descricao' => $julgamentoRecursoSubstituicaoTO->getDescricao(),
            'usuario' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->id],
        ]);
        $julgamentoRecursoSubstituicao->setRecursoSubstituicao($recursoSubstituicao);

        return $julgamentoRecursoSubstituicao;
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $idJulgamentoSubstituicao
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idJulgamentoSubstituicao, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioJulgamentoRecursoSubstituicao($idJulgamentoSubstituicao),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param JulgamentoRecursoSubstituicao $julgamentoRecursoSubstituicao
     * @throws Exception
     */
    private function salvarHistoricoJulgamentoSubstituicao(
        JulgamentoRecursoSubstituicao $julgamentoRecursoSubstituicao
    ): void {
        $historico = $this->getHistoricoBO()->criarHistorico(
            $julgamentoRecursoSubstituicao,
            Constants::HISTORICO_ID_TIPO_JUGAMENTO_RECURSO_SUBST,
            Constants::HISTORICO_ACAO_INSERIR,
            Constants::HISTORICO_DESCRICAO_ACAO_INSERIR
        );
        $this->getHistoricoBO()->salvar($historico);
    }

    /**
     * Verifica se usuário pode visualizar julgamento de acordo com as seguinte regras:
     * - A atividade de recurso dete estar iniciada
     * - Deve ser um membro da comissão CEN ou Ce da Cau UF da chapa vinculada ao julgamento
     *   se parâmetro para verificar estiver habilitado
     * - Deve ser um responsável da chapa vinculada ao julgamento
     *   se parâmetro para verificar estiver habilitado
     *
     * @param $idPedidoSubstituicao
     * @param bool $verificarUsuarioResponsavelChapa
     * @param bool $verificarUsuarioMembroComissao
     * @return JulgamentoRecursoSubstituicaoTO|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function getPorPedidoSubstituicaoComVerificacaoUsuario(
        $idPedidoSubstituicao,
        $verificarUsuarioResponsavelChapa,
        $verificarUsuarioMembroComissao
    ) {
        $isPermitidoVisualizar = false;

        /** @var JulgamentoRecursoSubstituicao $julgamentoRecursoSubst */
        $julgamentoRecursoSubst = $this->getJulgamentoRecursoSubstituicaoRepository()->findPorPedidoSubstituicao(
            $idPedidoSubstituicao
        );

        if (!empty($julgamentoRecursoSubst)) {
            $julgamentoSubstituicao = $julgamentoRecursoSubst->getRecursoSubstituicao()->getJulgamentoSubstituicao();
            $chapaEleicao = $julgamentoSubstituicao->getPedidoSubstituicaoChapa()->getChapaEleicao();
            $isIES = $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;

            $eleicap = $this->getEleicaoBO()->getEleicaoPorPedidoSubstituicao($idPedidoSubstituicao);

            if ($verificarUsuarioMembroComissao) {
                $isMembroComissao = $this->getMembroComissaoBO()->verificarMembroComissaoPorCauUf(
                    $eleicap->getCalendario()->getId(), $chapaEleicao->getIdCauUf(), $isIES
                );

                if ($isMembroComissao) {
                    $isPermitidoVisualizar = true;
                }
            }

            if ($verificarUsuarioResponsavelChapa) {
                $ativSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                    $eleicap->getCalendario()->getId(), 2, 6
                );

                $isFinalizadoAtiv = Utils::getDataHoraZero() > Utils::getDataHoraZero($ativSecundaria->getDataFim());

                if ($isFinalizadoAtiv) {
                    $idChapaEleicao = $this->getChapaEleicaoBO()->getIdChapaEleicaoPorCalendarioEResponsavel(
                        $eleicap->getCalendario()->getId(),
                        $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
                    );

                    if (!empty($idChapaEleicao) && $idChapaEleicao == $chapaEleicao->getId()) {
                        $isPermitidoVisualizar = true;
                    }
                }
            }
        }

        return $isPermitidoVisualizar ? JulgamentoRecursoSubstituicaoTO::newInstanceFromEntity($julgamentoRecursoSubst) : null;
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
     * Retorna uma nova instância de 'JulgamentoRecursoSubstituicaoRepository'.
     *
     * @return JulgamentoRecursoSubstituicaoRepository
     */
    private function getJulgamentoRecursoSubstituicaoRepository()
    {
        if (empty($this->julgamentoRecursoSubstituicaoRepository)) {
            $this->julgamentoRecursoSubstituicaoRepository = $this->getRepository(
                JulgamentoRecursoSubstituicao::class
            );
        }

        return $this->julgamentoRecursoSubstituicaoRepository;
    }

    /**
     * Retorna uma nova instância de 'PedidoSubstituicaoChapaBO'.
     *
     * @return PedidoSubstituicaoChapaBO
     */
    private function getPedidoSubstituicaoChapaBO()
    {
        if (empty($this->pedidoSubstituicaoChapaBO)) {
            $this->pedidoSubstituicaoChapaBO = app()->make(PedidoSubstituicaoChapaBO::class);
        }

        return $this->pedidoSubstituicaoChapaBO;
    }

    /**
     * Retorna uma nova instância de 'RecursoSubstituicaoBO'.
     *
     * @return RecursoSubstituicaoBO
     */
    private function getRecursoSubstituicaoBO()
    {
        if (empty($this->recursoSubstituicaoBO)) {
            $this->recursoSubstituicaoBO = app()->make(RecursoSubstituicaoBO::class);
        }

        return $this->recursoSubstituicaoBO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoSubstituicaoBO'.
     *
     * @return JulgamentoSubstituicaoBO
     */
    private function getJulgamentoSubstituicaoBO()
    {
        if (empty($this->julgamentoSubstituicaoBO)) {
            $this->julgamentoSubstituicaoBO = app()->make(JulgamentoSubstituicaoBO::class);
        }

        return $this->julgamentoSubstituicaoBO;
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
}




