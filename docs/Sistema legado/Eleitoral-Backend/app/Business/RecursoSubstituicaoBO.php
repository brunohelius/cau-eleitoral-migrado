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
use App\Entities\JulgamentoSubstituicao;
use App\Entities\RecursoSubstituicao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailRecursoJulgamentoSubstituicaoJob;
use App\Repository\RecursoSubstituicaoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\AtividadeSecundariaCalendarioTO;
use App\To\PedidoSubstituicaoChapaTO;
use App\To\RecursoSubstituicaoTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'RecursoSubstituicao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class RecursoSubstituicaoBO extends AbstractBO
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
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var HistoricoProfissionalBO
     */
    private $historicoProfissionalBO;

    /**
     * @var PedidoSubstituicaoChapaBO
     */
    private $pedidoSubstituicaoChapaBO;

    /**
     * @var JulgamentoSubstituicaoBO
     */
    private $julgamentoSubstituicaoBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var RecursoSubstituicaoRepository
     */
    private $recursoSubstituicaoRepository;

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
     * Retorna o julgamento de substituição chapa conforme o id informado.
     *
     * @param $id
     *
     * @return RecursoSubstituicao|null
     */
    public function findById($id)
    {
        /** @var RecursoSubstituicao $recursoSubstituicao */
        $recursoSubstituicao = $this->getRecursoSubstituicaoRepository()->find($id);

        return $recursoSubstituicao;
    }

    /**
     * Retorna o Recurso do Julgamento do Pedido de Substituição conforme o id do pedido informado.
     *
     * @param $idPedidoSubstituicao
     *
     * @return RecursoSubstituicaoTO
     */
    public function getPorPedidoSubstituicao($idPedidoSubstituicao)
    {
        $recursoSubstituicaoTO = $this->getRecursoSubstituicaoRepository()->getPorPedidosSubstituicao(
            $idPedidoSubstituicao
        );

        return  $recursoSubstituicaoTO;
    }

    /**
     * Retorna a atividade de secundária do Recurso julgamento de substituição por pedido de substituição
     *
     * @return AtividadeSecundariaCalendarioTO
     * @throws Exception
     */
    public function getAtividadeSecundariaRecursoPorPedidoSubstituicao($idPedidoSubstituicao)
    {
        $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoSubstituicao($idPedidoSubstituicao);

        $atividadeSecundariaTO = null;
        if (!empty($eleicaoTO)) {
            $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                $eleicaoTO->getCalendario()->getId(), 2, 5
            );

            if(!empty($atividadeSecundaria)) {
                $atividadeSecundariaTO = AtividadeSecundariaCalendarioTO::newInstanceFromEntity($atividadeSecundaria);
            }
        }

        return $atividadeSecundariaTO;
    }

    /**
     * Salva o pedido de subsdtituição chapa
     *
     * @param RecursoSubstituicaoTO $recursoSubstituicaoTO
     * @return RecursoSubstituicaoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function salvar(RecursoSubstituicaoTO $recursoSubstituicaoTO)
    {
        $arquivos = $recursoSubstituicaoTO->getArquivos();

        /** @var ArquivoGenericoTO|null $arquivo */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validacaoInicialSalvarRercurso($recursoSubstituicaoTO, $arquivo);

        $julgamentoSubstituicao = $this->getJulgamentoSubstituicaoBO()->findById(
            $recursoSubstituicaoTO->getIdJulgamentoSubstituicao()
        );

        $this->validacaoComplementarSalvarRercurso($julgamentoSubstituicao);

        try {
            $this->beginTransaction();

            $recursoSubstituicao = $this->prepararRecursoSalvar(
                $recursoSubstituicaoTO, $julgamentoSubstituicao, $arquivo
            );

            $this->getRecursoSubstituicaoRepository()->persist($recursoSubstituicao);

            $this->salvarHistoricoRecursoSubstituicao($recursoSubstituicao);

            if (!empty($arquivo)) {
                $this->salvarArquivo(
                    $recursoSubstituicao->getId(),
                    $arquivo->getArquivo(),
                    $recursoSubstituicao->getNomeArquivoFisico()
                );
            }

            $this->getPedidoSubstituicaoChapaBO()->atualizarStatusPedidoSubstituicao(
                $julgamentoSubstituicao->getPedidoSubstituicaoChapa(),
                Constants::STATUS_SUBSTITUICAO_CHAPA_RECURSO_EM_ANDAMENTO
            );

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailRecursoJulgamentoSubstituicaoJob($recursoSubstituicao->getId()));

        return RecursoSubstituicaoTO::newInstanceFromEntity($recursoSubstituicao);
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoRecursoSubstituicao($id)
    {
        /** @var RecursoSubstituicao $recursoSubstituicao */
        $recursoSubstituicao = $this->getRecursoSubstituicaoRepository()->find($id);

        if (!empty($recursoSubstituicao)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioRecursoSubstituicao(
                $recursoSubstituicao->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $recursoSubstituicao->getNomeArquivoFisico(),
                $recursoSubstituicao->getNomeArquivo()
            );
        }
    }

    /**
     * Realiza o envio de e-mails após o cadastro do recurso do julgamento
     *
     * @param $idRecursoSubstituicao
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function enviarEmailCadastroRecursoSubstituicao($idRecursoSubstituicao)
    {
        /** @var RecursoSubstituicao $recursoSubstituicao */
        $recursoSubstituicao = $this->getRecursoSubstituicaoRepository()->find($idRecursoSubstituicao);

        $pedidoSubstituicaoChapaTO = $this->getPedidoSubstituicaoChapaBO()->getPorId(
            $recursoSubstituicao->getJulgamentoSubstituicao()->getPedidoSubstituicaoChapa()->getId(),
            false
        );

        $idCalendario = $this->getRecursoSubstituicaoRepository()->getIdCalendarioRecursoJulgamento(
            $idRecursoSubstituicao
        );

        $atividadeRecurso = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $idCalendario, 2, 5
        );

        $idTipoCandidatura = $pedidoSubstituicaoChapaTO->getChapaEleicao()->getTipoCandidatura()->getId();
        $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
        $idCauUf = $isIES ? null : $pedidoSubstituicaoChapaTO->getChapaEleicao()->getIdCauUf();

        $paramsEmail = $this->prepararParametrosEmailRecurso($recursoSubstituicao, $pedidoSubstituicaoChapaTO, $isIES);

        $destinariosComissao = [];
        /*$destinariosComissao = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $atividadeRecurso->getId(), $idCauUf
        );*/

        $destinariosAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        $emailsResponsaveisAndMembros = $this->getJulgamentoSubstituicaoBO()->getEmailsResponsaveisChapaEMembrosSubstituicao(
            $pedidoSubstituicaoChapaTO
        );

        $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
            $atividadeRecurso->getId(),
            array_merge($destinariosComissao, $destinariosAssessores, $emailsResponsaveisAndMembros),
            Constants::EMAIL_RECURSO_SUBSTITUICAO,
            Constants::TEMPLATE_EMAIL_RECURSO_JULGAMENTO_SUBST,
            $paramsEmail
        );
    }

    /**
     * Método faz validação inicial do cadastrado do recurso
     *
     * @param RecursoSubstituicaoTO $recursoSubstituicaoTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function validacaoInicialSalvarRercurso($recursoSubstituicaoTO, $arquivo)
    {
        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Message::MSG_VISUALIZACAO_ATIV_APENAS_MEMBROS_RESPONSAVEIS_CHAPA);
        }

        if (empty($recursoSubstituicaoTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($recursoSubstituicaoTO->getIdJulgamentoSubstituicao())) {
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
     * @param JulgamentoSubstituicao $julgamentoSubstituicao
     */
    private function validacaoComplementarSalvarRercurso($julgamentoSubstituicao)
    {
        if (empty($julgamentoSubstituicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoSubstituicao(
            $julgamentoSubstituicao->getPedidoSubstituicaoChapa()->getId()
        );

        if (empty($eleicaoTO)) {
            throw new NegocioException(Message::MSG_PERIODO_VIGENTE_ELEICAO_FECHADO);
        }

        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $eleicaoTO->getCalendario()->getId(), 2, 5
        );

        $inicioVigente = Utils::getDataHoraZero() >= Utils::getDataHoraZero($atividadeSecundaria->getDataInicio());
        $fimVigente = Utils::getDataHoraZero() <= Utils::getDataHoraZero($atividadeSecundaria->getDataFim());
        if (!($inicioVigente && $fimVigente)) {
            throw new NegocioException(Message::MSG_PERIODO_VIGENCIA_FECHADO_PARA_RESPONSAVEL);
        }

        $statusSubstituicao = $julgamentoSubstituicao->getPedidoSubstituicaoChapa()->getStatusSubstituicaoChapa();
        if ($statusSubstituicao->getId() != Constants::STATUS_SUBSTITUICAO_CHAPA_INDEFERIDO) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $chapaEleicao = $julgamentoSubstituicao->getPedidoSubstituicaoChapa()->getChapaEleicao();

        $isResponsavelChapa = $this->getMembroChapaBO()->isMembroResponsavelChapa(
            $chapaEleicao->getId(),
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );
        if (!$isResponsavelChapa) {
            throw new NegocioException(Message::MSG_VISUALIZACAO_ATIV_APENAS_MEMBROS_RESPONSAVEIS_CHAPA);
        }
    }

    /**
     * Método auxiliar para preparar entidade RecursoSubstituicao para cadastro
     *
     * @param RecursoSubstituicaoTO $recursoSubstituicaoTO
     * @param JulgamentoSubstituicao|null $julgamentoSubstituicao
     * @param ArquivoGenericoTO|null $arquivo
     * @return RecursoSubstituicao
     * @throws Exception
     */
    private function prepararRecursoSalvar($recursoSubstituicaoTO, $julgamentoSubstituicao, $arquivo)
    {
        $nomeArquivo = null;
        $nomeArquivoFisico = null;
        if (!empty($arquivo)) {
            $nomeArquivo = $arquivo->getNome();
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getNome(), Constants::PREFIXO_ARQ_RECURSO_JULGAMENTO_SUBST
            );
        }

        $recursoSubstituicao = RecursoSubstituicao::newInstance([
            'nomeArquivo' => $nomeArquivo,
            'dataCadastro' => Utils::getData(),
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'descricao' => $recursoSubstituicaoTO->getDescricao(),
            'profissional' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional]
        ]);
        $recursoSubstituicao->setJulgamentoSubstituicao($julgamentoSubstituicao);

        return $recursoSubstituicao;
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
            $this->getArquivoService()->getCaminhoRepositorioRecursoSubstituicao($idRecursoSubstituicao),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do recurso de substituição
     *
     * @param RecursoSubstituicao $recursoSubstituicao
     * @param bool $isAlteracao
     * @throws Exception
     */
    private function salvarHistoricoRecursoSubstituicao(
        RecursoSubstituicao $recursoSubstituicao
    ): void {
        $historico = $this->getHistoricoProfissionalBO()->criarHistorico(
            $recursoSubstituicao->getId(),
            Constants::HISTORICO_PROF_TIPO_RECURSO_JULGAMENTO_SUBSTITUICAO,
            Constants::HISTORICO_PROF_ACAO_INSERIR,
            Constants::HISTORICO_PROF_DESCRICAO_ACAO_INSERIR
        );
        $this->getHistoricoProfissionalBO()->salvar($historico);
    }

    /**
     * Método auxiliar que prepara os parâmetros para o envio de e-mails
     *
     * @param RecursoSubstituicao $recursoSubstituicao
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO
     * @param bool $isIES
     * @return array
     */
    private function prepararParametrosEmailRecurso(
        RecursoSubstituicao $recursoSubstituicao,
        PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapaTO,
        bool $isIES
    ) {
        $parametrosEmailPedido = $this->getPedidoSubstituicaoChapaBO()->prepararParametrosEmailPedidoSubstituicao(
            $pedidoSubstituicaoChapaTO
        );

        $label_recurso = $isIES ? Constants::LABEL_INTERPOR_RECONSIDERACAO : Constants::LABEL_INTERPOR_RECURSO;

        $parametrosEmailRecurso = [
            Constants::PARAMETRO_EMAIL_LABEL_INTERPOR_RECURSO => $label_recurso,
            Constants::PARAMETRO_EMAIL_DS_INTERPOR_RECURSO => $recursoSubstituicao->getDescricao()
        ];

        return array_merge($parametrosEmailPedido, $parametrosEmailRecurso);
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
     * Retorna uma nova instância de 'RecursoSubstituicaoRepository'.
     *
     * @return RecursoSubstituicaoRepository
     */
    private function getRecursoSubstituicaoRepository()
    {
        if (empty($this->recursoSubstituicaoRepository)) {
            $this->recursoSubstituicaoRepository = $this->getRepository(RecursoSubstituicao::class);
        }

        return $this->recursoSubstituicaoRepository;
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




