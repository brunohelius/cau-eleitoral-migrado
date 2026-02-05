<?php
/*
 * ContrarrazaoRecursoImpugnacaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ContrarrazaoRecursoImpugnacao;
use App\Entities\PedidoImpugnacao;
use App\Entities\RecursoImpugnacao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailContrarrazaoJob;
use App\Mail\ContrarrazaoCadastradaMail;
use App\Repository\ContrarrazaoRecursoImpugnacaoRepository;
use App\Repository\RecursoImpugnacaoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\ContrarrazaoRecursoImpugnacaoTO;
use App\To\PedidoImpugnacaoTO;
use App\To\RecursoImpugnacaoTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'ContrarrazaoRecursoImpugnacao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class ContrarrazaoRecursoImpugnacaoBO extends AbstractBO
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
     * @var RecursoImpugnacaoBO
     */
    private $recursoImpugnacaoBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

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
     * @var ContrarrazaoRecursoImpugnacaoRepository
     */
    private $contrarrazaoRecursoImpugnacaoRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Retorna o Recurso do Julgamento do Pedido de Impugnação conforme o id do pedido informado.
     *
     * @param $idPedidoImpugnacao
     *
     * @return RecursoImpugnacaoTO
     */
    public function getPorPedidoImpugnacaoAndTipoSolicitacao($idPedidoImpugnacao, $idTipoSolicitacao)
    {
        $recursoImpugnacaoTO = $this->getContrarrazaoRecursoImpugnacaoRepository()->getPorPedidoImpugnacaoAndTipoSolicitacao(
            $idPedidoImpugnacao, $idTipoSolicitacao
        );

        return $recursoImpugnacaoTO;
    }

    /**
     * Salva o recurso de julgamento de pedido de impugnação
     *
     * @param ContrarrazaoRecursoImpugnacaoTO $contrarrazaoRecursoImpugnacaoTO
     * @return ContrarrazaoRecursoImpugnacaoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function salvar(ContrarrazaoRecursoImpugnacaoTO $contrarrazaoRecursoImpugnacaoTO)
    {
        $arquivos = $contrarrazaoRecursoImpugnacaoTO->getArquivos();

        /** @var ArquivoGenericoTO|null $arquivo */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validacaoCamposObrigatorios($contrarrazaoRecursoImpugnacaoTO, $arquivo);

        $recursoImpugnacao = $this->getRecursoImpugnacaoBO()->findById(
            $contrarrazaoRecursoImpugnacaoTO->getIdRecursoImpugnacao()
        );

        $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoImpugnacao(
            $recursoImpugnacao->getJulgamentoImpugnacao()->getPedidoImpugnacao()->getId()
        );

        $this->validacaoComplementarSalvar($eleicaoTO, $contrarrazaoRecursoImpugnacaoTO, $recursoImpugnacao);

        try {
            $this->beginTransaction();

            $contrarrazaoRecursoImpugnacao = $this->prepararSalvar(
                $contrarrazaoRecursoImpugnacaoTO, $recursoImpugnacao, $arquivo
            );

            $this->getContrarrazaoRecursoImpugnacaoRepository()->persist(
                $contrarrazaoRecursoImpugnacao
            );

            if (!empty($arquivo)) {
                $this->salvarArquivo(
                    $contrarrazaoRecursoImpugnacao->getId(),
                    $arquivo->getArquivo(),
                    $contrarrazaoRecursoImpugnacao->getNomeArquivoFisico()
                );
            }

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailContrarrazaoJob($contrarrazaoRecursoImpugnacao->getId(), $eleicaoTO->getCalendario()->getId()));

        return ContrarrazaoRecursoImpugnacaoTO::newInstanceFromEntity($contrarrazaoRecursoImpugnacao, true);
    }

    /**
     * Realiza o envio de e-mails após o cadastro do recurso do julgamento
     *
     * @param $idContrarrazaoRecursoImpugnacao
     * @param $idCalendario
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailCadastro($idContrarrazaoRecursoImpugnacao, $idCalendario)
    {
        /** @var ContrarrazaoRecursoImpugnacao $contrarrazaoRecursoImpugnacao */
        $contrarrazaoRecursoImpugnacao = $this->getContrarrazaoRecursoImpugnacaoRepository()->find
        ($idContrarrazaoRecursoImpugnacao);

        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $idCalendario, 3, 5
        );

        $destinarios = $this->recuperaDestinatarios($contrarrazaoRecursoImpugnacao->getRecursoImpugnacao()->getJulgamentoImpugnacao()->getPedidoImpugnacao(),
            $atividadeSecundaria);

        $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
            $atividadeSecundaria->getId(),
            $contrarrazaoRecursoImpugnacao->getRecursoImpugnacao()->getTipoSolicitacaoRecursoImpugnacao()->getId() == 1 ?
                Constants::EMAIL_CONTRARRAZAO_IMPUGNANTE :
                Constants::EMAIL_CONTRARRAZAO_RESPONSAVEL_CHAPA
        );

        if (!empty($emailAtividadeSecundaria) && !empty($destinarios)) {

            $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
            $emailTO->setDestinatarios(array_unique($destinarios));

            $contrarrazaoRecursoImpugnacaoTO = ContrarrazaoRecursoImpugnacaoTO::newInstanceFromEntity
            ($contrarrazaoRecursoImpugnacao);

            Email::enviarMail(new ContrarrazaoCadastradaMail($emailTO,
                $contrarrazaoRecursoImpugnacaoTO));
        }
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivo($id)
    {
        /** @var ContrarrazaoRecursoImpugnacao $contrarrazaoRecursoImpugnacao */
        $contrarrazaoRecursoImpugnacao = $this->getContrarrazaoRecursoImpugnacaoRepository()->find($id);

        if (!empty($contrarrazaoRecursoImpugnacao)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioContrarrazaoRecursoImpugnacao(
                $contrarrazaoRecursoImpugnacao->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $contrarrazaoRecursoImpugnacao->getNomeArquivoFisico(),
                $contrarrazaoRecursoImpugnacao->getNomeArquivo()
            );
        }
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoContrarrazaoRecursoImpugnacao($id)
    {
        /** @var ContrarrazaoRecursoImpugnacao $contrarrazao */
        $contrarrazao = $this->getContrarrazaoRecursoImpugnacaoRepository()->find($id);

        if (!empty($contrarrazao)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioContrarrazaoRecursoImpugnacao(
                $contrarrazao->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $contrarrazao->getNomeArquivoFisico(),
                $contrarrazao->getNomeArquivo()
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
        $recursoImpugnacao = $this->getContrarrazaoRecursoImpugnacaoRepository()->find($idRecursoImpugnacao);

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
     * Método faz validação dos campos obrigatorios da contrarrazao do recurso
     *
     * @param ContrarrazaoRecursoImpugnacaoTO $contrarrazaoRecursoImpugnacaoTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     */
    private function validacaoCamposObrigatorios($contrarrazaoRecursoImpugnacaoTO, $arquivo)
    {
        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Message::MSG_VISUALIZACAO_NAO_PERMITIDA_ATIV_SELECIONADA);
        }

        if (empty($contrarrazaoRecursoImpugnacaoTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($contrarrazaoRecursoImpugnacaoTO->getIdRecursoImpugnacao())) {
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
     * @param ContrarrazaoRecursoImpugnacaoTO $contrarrazaoRecursoImpugnacaoTO
     * @param RecursoImpugnacao $recursoImpugnacao
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function validacaoComplementarSalvar($eleicaoTO, $contrarrazaoRecursoImpugnacaoTO, $recursoImpugnacao)
    {
        if (empty($recursoImpugnacao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $contrarrazaoAnterior = $this->getContrarrazaoRecursoImpugnacaoRepository()->findBy([
            'recursoImpugnacao' => $recursoImpugnacao->getId()
        ]);

        if (!empty($contrarrazaoAnterior)) {
            throw new NegocioException(Lang::get('messages.contrarrazao.ja_cadastrada_para_recurso'));
        }

        if (empty($eleicaoTO)) {
            throw new NegocioException(Message::MSG_PERIODO_VIGENCIA_FECHADO_ELEICAO_SELECIONADA);
        }

        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $eleicaoTO->getCalendario()->getId(), 3, 5
        );

        $inicioVigente = Utils::getDataHoraZero() >= Utils::getDataHoraZero($atividadeSecundaria->getDataInicio());
        $fimVigente = Utils::getDataHoraZero() <= Utils::getDataHoraZero($atividadeSecundaria->getDataFim());
        if (!($inicioVigente && $fimVigente)) {
            throw new NegocioException(Message::MSG_PERIODO_VIGENCIA_FECHADO_ELEICAO_SELECIONADA);
        }
    }

    /**
     * Método auxiliar para preparar entidade RecursoImpugnacao para cadastro
     *
     * @param ContrarrazaoRecursoImpugnacaoTO $contrarrazaoRecursoImpugnacaoTO
     * @param RecursoImpugnacao|null $recursoImpugnacao
     * @param ArquivoGenericoTO|null $arquivo
     * @return ContrarrazaoRecursoImpugnacao
     * @throws Exception
     */
    private function prepararSalvar($contrarrazaoRecursoImpugnacaoTO, $recursoImpugnacao, $arquivo)
    {
        $nomeArquivo = null;
        $nomeArquivoFisico = null;
        if (!empty($arquivo)) {
            $nomeArquivo = $arquivo->getNome();
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getNome(), Constants::PREFIXO_ARQ_CONTRARRAZAO_RECURSO_IMPUGN
            );
        }

        $contrarrazaoRecursoImpugnacao = ContrarrazaoRecursoImpugnacao::newInstance([
            'nomeArquivo' => $nomeArquivo,
            'dataCadastro' => Utils::getData(),
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'descricao' => $contrarrazaoRecursoImpugnacaoTO->getDescricao(),
            'profissional' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional],
        ]);
        $contrarrazaoRecursoImpugnacao->setRecursoImpugnacao($recursoImpugnacao);

        return $contrarrazaoRecursoImpugnacao;
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
            $this->getArquivoService()->getCaminhoRepositorioContrarrazaoRecursoImpugnacao($idRecursoSubstituicao),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do recurso de impugnação
     *
     * @param RecursoImpugnacao $recursoImpugnacao
     * @throws Exception
     */
    private function salvarHistoricoRecursoImpugnacao(
        RecursoImpugnacao $recursoImpugnacao
    ): void
    {
        $historico = $this->getHistoricoProfissionalBO()->criarHistorico(
            $recursoImpugnacao->getId(),
            Constants::HISTORICO_PROF_TIPO_RECURSO_JULGAMENTO_IMPUGNACAO,
            Constants::HISTORICO_PROF_ACAO_INSERIR,
            Constants::HISTORICO_PROF_DESCRICAO_ACAO_INSERIR
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
     * Retorna uma nova instância de 'ContrarrazaoRecursoImpugnacaoRepository'.
     *
     * @return ContrarrazaoRecursoImpugnacaoRepository
     */
    private function getContrarrazaoRecursoImpugnacaoRepository()
    {
        if (empty($this->contrarrazaoRecursoImpugnacaoRepository)) {
            $this->contrarrazaoRecursoImpugnacaoRepository = $this->getRepository(ContrarrazaoRecursoImpugnacao::class);
        }

        return $this->contrarrazaoRecursoImpugnacaoRepository;
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
     * Recupera os destinatarios do email de acordo com o pedido de impugnaçao e atividade secundaria
     *
     * @param PedidoImpugnacao|null $pedidoImpugnacao
     * @param AtividadeSecundariaCalendario $atividadeSecundariaRecurso
     * @return array
     * @throws NegocioException
     */
    private function recuperaDestinatarios(PedidoImpugnacao $pedidoImpugnacao, AtividadeSecundariaCalendario $atividadeSecundariaRecurso): array
    {
        $idTipoCandidatura = $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getTipoCandidatura()->getId();

        $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
        $idCauUf = $isIES ? null : $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getIdCauUf();

        $emailsResponsaveis = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa(
            $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getId()
        );

        $destinariosComissao = [];
        /*$destinariosComissao = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $atividadeSecundariaRecurso->getId(), $idCauUf
        );*/

        $destinariosAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        return array_unique(array_merge(
            $destinariosComissao,
            $destinariosAssessores,
            $emailsResponsaveis,
            [$pedidoImpugnacao->getProfissional()->getPessoa()->getEmail()])
        );
    }
}




