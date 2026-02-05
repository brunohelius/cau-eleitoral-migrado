<?php
/*
 * JulgamentoFinalBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ChapaEleicao;
use App\Entities\JulgamentoFinal;
use App\Entities\JulgamentoRecursoPedidoSubstituicao;
use App\Entities\JulgamentoSegundaInstanciaRecurso;
use App\Entities\JulgamentoSegundaInstanciaSubstituicao;
use App\Entities\MembroChapa;
use App\Entities\MembroSubstituicaoJulgamentoFinal;
use App\Entities\RecursoSegundoJulgamentoSubstituicao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailJulgamentoRecursoPedidoSubstituicaoJob;
use App\Mail\AtividadeSecundariaMail;
use App\Mail\JulgamentoFinalCadastradoMail;
use App\Mail\JulgamentoRecursoPedidoSubstituicaoMail;
use App\Mail\JulgamentoSegundaInstanciaSubstituicaoDecisaoMail;
use App\Repository\JulgamentoRecursoPedidoSubstituicaoRepository;
use App\Repository\JulgamentoSegundaInstanciaSubstituicaoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\IndicacaoJulgamentoFinalTO;
use App\To\IndicacaoJulgamentoRecursoPedidoSubstituicaoTO;
use App\To\JulgamentoFinalTO;
use App\To\JulgamentoRecursoPedidoSubstituicaoTO;
use App\To\JulgamentoSegundaInstanciaSubstituicaoTO;
use App\To\SubstituicaoRecursoTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'JulgamentoSegundaInstanciaRecursoPedidoSubstituicao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoPedidoSubstituicaoBO extends AbstractBO
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
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var IndicacaoJulgamentoRecursoPedidoSubstituicaoBO
     */
    private $indicacaoJulgamentoRecursoPedidoSubstituicaoBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var RecursoSegundoJulgamentoSubstituicaoBO
     */
    private $recursoSegundoJulgamentoSubstituicaoBO;

    /**
     * @var JulgamentoRecursoPedidoSubstituicaoRepository
     */
    private $julgamentoRecursoPedidoSubstituicaoRepository;

    /**
     * @var SubstituicaoJulgamentoFinalBO
     */
    private $substituicaoJulgamentoFinalBO;


    /**
     * Construtor da classe.
     */
    public function __construct()
    {}


    /**
     * Salva o julgamento do Recurso de Pedido de Substituição da chapa da eleição
     *
     * @param JulgamentoRecursoPedidoSubstituicaoTO $julgamentoRecursoPedidoSubstTO
     * @return JulgamentoRecursoPedidoSubstituicaoTO
     * @throws NegocioException
     */
    public function salvar(JulgamentoRecursoPedidoSubstituicaoTO $julgamentoRecursoPedidoSubstTO)
    {
        $arquivos = $julgamentoRecursoPedidoSubstTO->getArquivos();
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validarCamposObrigatorios($julgamentoRecursoPedidoSubstTO, $arquivo);

        $recursoSegundoJulgamentoSubstituicao = $this->getRecursoSegundoJulgamentoSubstituicaoBO()->findById(
            $julgamentoRecursoPedidoSubstTO->getIdRecursoSegundoJulgamentoSubstituicao()
        );

        if (empty($recursoSegundoJulgamentoSubstituicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $this->validacaoComplementarSalvarJulgamentoFinal(
            $julgamentoRecursoPedidoSubstTO,
            $recursoSegundoJulgamentoSubstituicao
        );

        $chapaEleicao = $recursoSegundoJulgamentoSubstituicao->getJulgamentoSegundaInstanciaSubstituicao()
            ->getSubstituicaoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao();

        $julgamentoRecursoPedidoSubstAnterior = $this->getJulgamentoPaiParaSalvar($julgamentoRecursoPedidoSubstTO);

        try {
            $this->beginTransaction();

            $julgamentoRecursoPedidoSubst = $this->prepararJulgamentoSalvar(
                $julgamentoRecursoPedidoSubstTO,
                $recursoSegundoJulgamentoSubstituicao,
                $arquivo,
                $julgamentoRecursoPedidoSubstAnterior
            );

            $this->getJulgamentoRecursoPedidoSubstituicaoRepository()->persist($julgamentoRecursoPedidoSubst);

            $this->salvarIndicacoes($julgamentoRecursoPedidoSubstTO->getIndicacoes(), $julgamentoRecursoPedidoSubst);

            $this->salvarHistoricoJulgamentoFinal($julgamentoRecursoPedidoSubst);

            $this->getChapaEleicaoBO()->atualizarChapaEleicaoPosJulgamentoFinal(
                $chapaEleicao, $julgamentoRecursoPedidoSubstTO->getIdStatusJulgamentoFinal()
            );

            $this->salvarArquivo($julgamentoRecursoPedidoSubst, $arquivo, $julgamentoRecursoPedidoSubstAnterior);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailJulgamentoRecursoPedidoSubstituicaoJob(
            $julgamentoRecursoPedidoSubst->getId(),
            $chapaEleicao->getId(),
            $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES,
            $chapaEleicao->getIdCauUf()
        ));

        return JulgamentoRecursoPedidoSubstituicaoTO::newInstanceFromEntity($julgamentoRecursoPedidoSubst);
    }

    /**
     * Responsável por realizar envio de e-mail após o cadastro do julgamento
     *
     * @param $idJulgamento
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailCadastroJulgamento($idJulgamento, $idChapaEleicao, $isIES, $idCauUf)
    {
        /** @var JulgamentoRecursoPedidoSubstituicao $julgamento */
        $julgamento = $this->getJulgamentoRecursoPedidoSubstituicaoRepository()->find($idJulgamento);
        $julgamentoTO = JulgamentoRecursoPedidoSubstituicaoTO::newInstanceFromEntity($julgamento);

        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao(
            $idChapaEleicao,
            Constants::ATIVIDADE_PRIMARIA_JULGAMENTO_FINAL_SEGUNDA_INSTANCIA,
            Constants::ATIVIDADE_SECUNDARIA_JULGAMENTO_FINAL_SEGUNDA_INSTANCIA
        );

        $destinatarios = $this->getDestinatariosEnvioEmailCadastro(
            $julgamento, $atividade->getId(), $idChapaEleicao, $isIES, $idCauUf
        );

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(),
                $this->getTipoEmailCadastroJulgamento($julgamento->getStatusJulgamentoFinal()->getId())
            );

            if (!empty($emailAtividadeSecundaria)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios($destinatarios);

                Email::enviarMail(new JulgamentoRecursoPedidoSubstituicaoMail($emailTO, $julgamentoTO));
            }
        }
    }

    /**
     * Método auxiliar para buscar os e-mails dos destinatários
     * @param JulgamentoRecursoPedidoSubstituicao $julgamento
     * @param boolean $isIES
     * @return array
     * @throws NegocioException
     */
    private function getDestinatariosEnvioEmailCadastro($julgamento, $idAtividade, $idChapaEleicao, $isIES, $idCauUf)
    {
        $idCauUf = $isIES ? null : $idCauUf;

        /*$emailsComissao = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $idAtividade, $idCauUf
        );*/
        $emailsComissao = [];

        $emailsAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        $emailsResponsaveis = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa($idChapaEleicao);

        $emailsMembrosSubstitutosAndSubstituidos = [];

        $membrosChapaSubstituicao = $julgamento->getRecursoSegundoJulgamentoSubstituicao()
            ->getJulgamentoSegundaInstanciaSubstituicao()
            ->getSubstituicaoJulgamentoFinal()
            ->getMembrosSubstituicaoJulgamentoFinal();

        /** @var MembroSubstituicaoJulgamentoFinal $membroSubstituicaoJulgamento */
        foreach ($membrosChapaSubstituicao as $membroSubstituicaoJulgamento) {
            $membroChapa = $this->getSubstituicaoJulgamentoFinalBO()->recuperaMembroChapaIndicacao(
                $membroSubstituicaoJulgamento
            );

            if (!empty($membroChapa) && !empty($membroChapa->getProfissional()->getPessoa()->getEmail())) {
                array_push(
                    $emailsMembrosSubstitutosAndSubstituidos,
                    $membroChapa->getProfissional()->getPessoa()->getEmail()
                );
            }

            if (!empty($membroSubstituicaoJulgamento->getMembroChapa()->getProfissional()->getPessoa()->getEmail())) {
                array_push(
                    $emailsMembrosSubstitutosAndSubstituidos,
                    $membroSubstituicaoJulgamento->getMembroChapa()->getProfissional()->getPessoa()->getEmail()
                );
            }
        }

        $destinatarios = array_merge(
            $emailsComissao, $emailsAssessores, $emailsResponsaveis, $emailsMembrosSubstitutosAndSubstituidos
        );

        return $destinatarios;
    }

    /**
     * Método auxiliar para retornar o tipo de e-mail a ser enviado
     * @param $idStatusJulgamento
     * @return int
     */
    private function getTipoEmailCadastroJulgamento($idStatusJulgamento)
    {
        $idTipoEmail = Constants::EMAIL_JULGAMENTO_FINAL_RECURSO_DEFERIDO;
        if ($idStatusJulgamento == Constants::STATUS_JULG_FINAL_INDEFERIDO) {
            $idTipoEmail = Constants::EMAIL_JULGAMENTO_FINAL_RECURSO_INDEFERIDO;
        }
        return $idTipoEmail;
    }

    /**
     * Retorna o Julgamento Final por ID.
     *
     * @param $id
     * @return JulgamentoRecursoPedidoSubstituicao | mixed | null
     */
    public function findById($id)
    {
        return $this->getJulgamentoRecursoPedidoSubstituicaoRepository()->find($id);
    }

    /**
     * Retorna todos os julgamento do recurso do pedido de substituição de uma chapa
     * @param $idChapa
     * @return array
     * @throws NonUniqueResultException
     */
    public function getPorChapaEleicao($idChapa)
    {
        $julgamentos = $this->getJulgamentoRecursoPedidoSubstituicaoRepository()->getPorChapa($idChapa);


        $julgamentosTO = [];
        if (!empty($julgamentos)) {
            $sequencia = 1;
            /** @var JulgamentoRecursoPedidoSubstituicao $julgamento */
            foreach ($julgamentos as $julgamento) {
                $julgamentoTO = SubstituicaoRecursoTO::newInstanceFromJulgamentoRecursoPedidoSubstituicao($julgamento);
                $julgamentoTO->setSequencia($sequencia);
                $julgamentosTO[] = $julgamentoTO;
                $sequencia++;
            }
        }

        return $julgamentosTO;
    }

    /**
     * Retorna todos os julgamento do pedido de substituição de uma chapa
     * @param $idRecursoPedidoSubst
     * @return array
     */
    public function getRetificacoes($idRecursoPedidoSubst)
    {
        $julgamentos = $this->getJulgamentoRecursoPedidoSubstituicaoRepository()->getPorRecurso($idRecursoPedidoSubst);

        $julgamentosTO = [];
        if (!empty($julgamentos) && count($julgamentos) > 1) {
            array_pop($julgamentos);

            $julgamentosTO = $this->converterJulgamentoSubstituicaoRecursoTO($julgamentos);
        }

        return $julgamentosTO;
    }

    /**
     * @param JulgamentoSegundaInstanciaSubstituicao[] $julgamentos
     */
    private function converterJulgamentoSubstituicaoRecursoTO($julgamentos)
    {
        $julgamentosTO = [];
        if (!empty($julgamentos)) {
            $sequencia = 1;
            /** @var JulgamentoRecursoPedidoSubstituicao $julgamento */
            foreach ($julgamentos as $julgamento) {
                $julgamentoTO = SubstituicaoRecursoTO::newInstanceFromJulgamentoRecursoPedidoSubstituicao($julgamento);
                $julgamentoTO->setSequencia($sequencia);
                $julgamentosTO[] = $julgamentoTO;
                $sequencia++;
            }
        }

        return $julgamentosTO;
    }

    /**
     * Retorna o Julgamento Segunda Instancia Substituição pelo ID do Julgamento Final.
     *
     * @param $idJulgamentoFinal
     * @return JulgamentoSegundaInstanciaSubstituicao | mixed| null
     * @throws NonUniqueResultException
     */
    public function getUltimoPorJulgamentoFinal($idJulgamentoFinal)
    {
        return $this->getJulgamentoRecursoPedidoSubstituicaoRepository()->getUltimoPorJulgamentoFinal($idJulgamentoFinal);
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
        /** @var JulgamentoRecursoPedidoSubstituicao $julgamento */
        $julgamento = $this->getJulgamentoRecursoPedidoSubstituicaoRepository()->find($id);

        if (!empty($julgamento)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoRecursoDaSubstituicao($julgamento->getId());

            return $this->getArquivoService()->getArquivo(
                $caminho, $julgamento->getNomeArquivoFisico(), $julgamento->getNomeArquivo()
            );
        }
    }

    /**
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param JulgamentoRecursoPedidoSubstituicaoTO $julgamentoFinalTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     */
    private function validarCamposObrigatorios($julgamentoFinalTO, $arquivo)
    {
        if (empty($julgamentoFinalTO->getIdRecursoSegundoJulgamentoSubstituicao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($julgamentoFinalTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $statusValidos = [Constants::STATUS_JULG_FINAL_DEFERIDO, Constants::STATUS_JULG_FINAL_INDEFERIDO];
        if (
            empty($julgamentoFinalTO->getIdStatusJulgamentoFinal())
            || !in_array($julgamentoFinalTO->getIdStatusJulgamentoFinal(), $statusValidos)
        ) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (
            empty($julgamentoFinalTO->getIdJulgamentoRecursoPedidoSubstituicaoPai())
            || (!empty($julgamentoFinalTO->getIdJulgamentoRecursoPedidoSubstituicaoPai()) && !empty($arquivo))
        ) {
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
    }

    /**
     * Método faz validação complementar do cadastrado do julgamento do recurso
     *
     * @param JulgamentoRecursoPedidoSubstituicaoTO $julgamentoRecursoPedidoSubstituicaoTO
     * @param RecursoSegundoJulgamentoSubstituicao $recursoSegundoJulgamentoSubstituicao
     * @param JulgamentoFinal $julgamentoFinal
     * @throws NegocioException
     */
    private function validacaoComplementarSalvarJulgamentoFinal(
        $julgamentoRecursoPedidoSubstituicaoTO,
        $recursoSegundoJulgamentoSubstituicao
    ) {
        $chapaEleicao = $recursoSegundoJulgamentoSubstituicao->getJulgamentoSegundaInstanciaSubstituicao()
            ->getSubstituicaoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao();

        $eleicao = $this->getEleicaoBO()->getEleicaoPorChapaEleicao($chapaEleicao->getId());
        if (empty($eleicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $this->verificaPermissaoRealizarJulgamento($chapaEleicao, $julgamentoRecursoPedidoSubstituicaoTO);

        $idJulgamentoPai = $julgamentoRecursoPedidoSubstituicaoTO->getIdJulgamentoRecursoPedidoSubstituicaoPai();
        if (empty($idJulgamentoPai)) {
            $idJulgamento = $this->getJulgamentoRecursoPedidoSubstituicaoRepository()->findBy([
                'recursoSegundoJulgamentoSubstituicao' => $recursoSegundoJulgamentoSubstituicao->getId()
            ]);

            if (!empty($idJulgamento)) {
                throw new NegocioException(Lang::get('messages.julgamento_segunda_instancia_substituicao.ja_realizado'));
            }
        } else {
            $julgamentoPai = $this->getJulgamentoRecursoPedidoSubstituicaoRepository()->findBy(
                ['julgamentoRecursoPedidoSubstituicaoPai' => $idJulgamentoPai]
            );
            if (!empty($julgamentoPai)) {
                throw new NegocioException(Lang::get('messages.julgamento_final_segunda_instancia.ja_realizado_alteracao'));
            }
        }

        $this->validaIndicacoes($julgamentoRecursoPedidoSubstituicaoTO, $chapaEleicao);
    }

    /**
     * Método auxiliar para verificar a permissão do usuário autenticado de realizar julgamento
     * @param ChapaEleicao $chapaEleicao
     * @param JulgamentoRecursoPedidoSubstituicaoTO $julgamentoRecursoPedidoSubstituicaoTO
     * @throws NegocioException
     */
    private function verificaPermissaoRealizarJulgamento($chapaEleicao, $julgamentoRecursoPedidoSubstituicaoTO): void
    {
        $isAlteracao = !empty($julgamentoRecursoPedidoSubstituicaoTO->getIdJulgamentoRecursoPedidoSubstituicaoPai());

        $isAssessorCEN = $this->getUsuarioFactory()->isCorporativoAssessorCEN();
        $isAssessorCEUF = $this->getUsuarioFactory()->isCorporativoAssessorCeUfPorCauUf($chapaEleicao->getIdCauUf());
        $isIES = $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;

        if (($isAlteracao && !$isAssessorCEN) || (!$isAssessorCEN && !$isAssessorCEUF) || ($isIES && !$isAssessorCEN)) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }
    }

    /**
     * Método responsável de validar as indicações do julgamento
     *
     * @param $julgamentoFinalTO
     * @param $chapaEleicao
     * @throws NegocioException
     */
    private function validaIndicacoes($julgamentoFinalTO, $chapaEleicao)
    {
        if (
            $julgamentoFinalTO->getIdStatusJulgamentoFinal() == Constants::STATUS_JULG_FINAL_INDEFERIDO
            && !empty($julgamentoFinalTO->getIndicacoes())
        ) {
            /** @var IndicacaoJulgamentoFinalTO $indicacao */
            foreach ($julgamentoFinalTO->getIndicacoes() as $indicacao) {

                if (is_null($indicacao->getNumeroOrdem())) {
                    throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
                }

                if (empty($indicacao->getIdTipoParicipacaoChapa())) {
                    throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
                }

                $membroSelecionado = null;
                /** @var MembroChapa $membroChapa */
                foreach ($chapaEleicao->getMembrosChapa() as $membroChapa) {
                    if (
                        $membroChapa->getTipoParticipacaoChapa()->getId() == $indicacao->getIdTipoParicipacaoChapa()
                        && $membroChapa->getNumeroOrdem() == $indicacao->getNumeroOrdem()
                        && in_array($membroChapa->getSituacaoMembroChapa()->getId(), Constants::$situacaoMembroAtual)
                    ) {
                        $membroSelecionado = $membroChapa;
                        break;
                    }
                }

                if (
                    (!empty($membroSelecionado) && empty($indicacao->getIdMembroChapa()))
                    || (empty($membroSelecionado) && !empty($indicacao->getIdMembroChapa()))
                    || (!empty($membroSelecionado) && $indicacao->getIdMembroChapa() != $membroSelecionado->getId())
                ) {
                    throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
                }
            }
        }
    }

    /**
     * Método auxiliar para salvar as indicações do julgamento
     *
     * @param IndicacaoJulgamentoRecursoPedidoSubstituicaoTO[] $indicacoesTO
     * @param JulgamentoRecursoPedidoSubstituicao|Object $julgamentoFinal
     * @throws Exception
     */
    private function salvarIndicacoes($indicacoesTO, $julgamentoFinal)
    {
        if (
            $julgamentoFinal->getStatusJulgamentoFinal()->getId() == Constants::STATUS_JULG_FINAL_INDEFERIDO
            && !empty($indicacoesTO)
        ) {
            $this->getIndicacaoJulgamentoRecursoPedidoSubstituicaoBO()->salvarIndicacoes(
                $indicacoesTO,
                $julgamentoFinal
            );
        }
    }

    /**
     * Método auxiliar para preparar entidade JulgamentoSubstituicaoSegundaIntancia para cadastro
     *
     * @param JulgamentoRecursoPedidoSubstituicaoTO $julgamentoFinalTO
     * @param $recursoSegundoJulgamentoSubstituicao
     * @param ArquivoGenericoTO|null $arquivo
     * @param JulgamentoRecursoPedidoSubstituicao $julgamentoRecursoPedidoSubstAnterior
     * @return JulgamentoRecursoPedidoSubstituicao
     * @throws Exception
     */
    private function prepararJulgamentoSalvar(
        $julgamentoFinalTO,
        $recursoSegundoJulgamentoSubstituicao,
        $arquivo,
        $julgamentoRecursoPedidoSubstAnterior
    ) {
        if (empty($arquivo) || empty($arquivo->getArquivo())) {
            $nomeArquivo = $julgamentoRecursoPedidoSubstAnterior->getNomeArquivo();
            $nomeArquivoFisico = $julgamentoRecursoPedidoSubstAnterior->getNomeArquivoFisico();
        } else {
            $nomeArquivo = $arquivo->getNome();
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getNome(),
                Constants::PREFIXO_ARQ_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA
            );
        }

        $julgamentoFinal = JulgamentoRecursoPedidoSubstituicao::newInstance([
            'dataCadastro' => Utils::getData(),
            'nomeArquivo' => $nomeArquivo,
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'statusJulgamentoFinal' => ['id' => $julgamentoFinalTO->getIdStatusJulgamentoFinal()],
            'descricao' => $julgamentoFinalTO->getDescricao(),
            'usuario' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->id],
            'recursoSegundoJulgamentoSubstituicao' => ['id' => $recursoSegundoJulgamentoSubstituicao->getId()]
        ]);

        if (!empty($julgamentoFinalTO->getIdJulgamentoRecursoPedidoSubstituicaoPai())) {
            $idJulgamentoPai = $julgamentoFinalTO->getIdJulgamentoRecursoPedidoSubstituicaoPai();
            $julgamentoFinal->setRetificacaoJustificativa($julgamentoFinalTO->getRetificacaoJustificativa());
            $julgamentoFinal->setJulgamentoRecursoPedidoSubstituicaoPai(
                JulgamentoRecursoPedidoSubstituicao::newInstance(['id' => $idJulgamentoPai])
            );
        }

        return $julgamentoFinal;
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param JulgamentoRecursoPedidoSubstituicao $julgamentoRecursoPedidoSubstituicao
     * @param ArquivoGenericoTO $arquivo
     * @param JulgamentoRecursoPedidoSubstituicao $julgamentoRecursoPedidoSubstAnterior
     */
    private function salvarArquivo($julgamentoRecursoPedidoSubstituicao, $arquivo, $julgamentoRecursoPedidoSubstAnterior)
    {
        $caminhoDestino = $this->getArquivoService()->getCaminhoRepositorioJulgamentoRecursoDaSubstituicao(
            $julgamentoRecursoPedidoSubstituicao->getId()
        );

        if (!empty($arquivo)) {
            $this->getArquivoService()->salvar(
                $caminhoDestino, $julgamentoRecursoPedidoSubstituicao->getNomeArquivoFisico(), $arquivo->getArquivo()
            );
        } else {
            $caminhoOrigem = $this->getArquivoService()->getCaminhoRepositorioJulgamentoRecursoDaSubstituicao(
                $julgamentoRecursoPedidoSubstAnterior->getId()
            );

            $hasArquivo = $this->getArquivoService()->hasArquivo(
                $caminhoOrigem, $julgamentoRecursoPedidoSubstAnterior->getNomeArquivoFisico()
            );

            if ($hasArquivo) {
                $this->getArquivoService()->copiar(
                    $caminhoOrigem,
                    $caminhoDestino,
                    $julgamentoRecursoPedidoSubstAnterior->getNomeArquivoFisico(),
                    $julgamentoRecursoPedidoSubstituicao->getNomeArquivoFisico()
                );
            }
        }
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param JulgamentoRecursoPedidoSubstituicao $julgamentoFinal
     * @throws Exception
     */
    private function salvarHistoricoJulgamentoFinal(JulgamentoRecursoPedidoSubstituicao $julgamentoFinal): void
    {
        $isInclusao = empty($julgamentoFinal->getJulgamentoRecursoPedidoSubstituicaoPai());

        $descricao = $isInclusao
            ? Constants::HISTORICO_INCLUSAO_JULG_SEGUNDA_INSTANCIA
            : Constants::HISTORICO_ALTERACAO_JULG_SEGUNDA_INSTANCIA;

        $historico = $this->getHistoricoBO()->criarHistorico(
            $julgamentoFinal,
            Constants::HISTORICO_ID_TIPO_JUGAMENTO_SEGUNDA_INSTANCIA,
            $isInclusao ? Constants::HISTORICO_ACAO_INSERIR : Constants::HISTORICO_ACAO_ALTERAR,
            $descricao,
            $isInclusao ? null : $julgamentoFinal->getRetificacaoJustificativa()
        );
        $this->getHistoricoBO()->salvar($historico);
    }

    /**
     * Método auxiliar para buscar o julgamento pai para salvar quando for alteração
     * @param JulgamentoRecursoPedidoSubstituicaoTO $julgamentoSegundaInstanciaSubstituicaoTO
     * @return JulgamentoRecursoPedidoSubstituicao|null
     * @throws NegocioException
     */
    private function getJulgamentoPaiParaSalvar($julgamentoSegundaInstanciaSubstituicaoTO)
    {
        /** @var JulgamentoRecursoPedidoSubstituicao $julgamentoRecursoPedidoSubstituicao */
        $julgamentoRecursoPedidoSubstituicao = null;
        if (!empty($julgamentoSegundaInstanciaSubstituicaoTO->getIdJulgamentoRecursoPedidoSubstituicaoPai())) {
            $julgamentoRecursoPedidoSubstituicao = $this->getJulgamentoRecursoPedidoSubstituicaoRepository()->find(
                $julgamentoSegundaInstanciaSubstituicaoTO->getIdJulgamentoRecursoPedidoSubstituicaoPai()
            );

            if (empty($julgamentoRecursoPedidoSubstituicao)) {
                throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
            }
        }
        return $julgamentoRecursoPedidoSubstituicao;
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
     * Retorna uma nova instância de 'JulgamentoSegundaInstanciaSubstituicao'.
     *
     * @return JulgamentoRecursoPedidoSubstituicaoRepository|\Doctrine\ORM\EntityRepository
     */
    private function getJulgamentoRecursoPedidoSubstituicaoRepository()
    {
        if (empty($this->julgamentoRecursoPedidoSubstituicaoRepository)) {
            $this->julgamentoRecursoPedidoSubstituicaoRepository = $this->getRepository(JulgamentoRecursoPedidoSubstituicao::class);
        }

        return $this->julgamentoRecursoPedidoSubstituicaoRepository;
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
     * Retorna uma nova instância de 'IndicacaoJulgamentoRecursoPedidoSubstituicaoBO'.
     *
     * @return IndicacaoJulgamentoRecursoPedidoSubstituicaoBO|mixed
     */
    private function getIndicacaoJulgamentoRecursoPedidoSubstituicaoBO()
    {
        if (empty($this->indicacaoJulgamentoRecursoPedidoSubstituicaoBO)) {
            $this->indicacaoJulgamentoRecursoPedidoSubstituicaoBO = app()->make(IndicacaoJulgamentoRecursoPedidoSubstituicaoBO::class);
        }

        return $this->indicacaoJulgamentoRecursoPedidoSubstituicaoBO;
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
     * Retorna uma nova instância de 'RecursoSegundoJulgamentoSubstituicaoBO'.
     *
     * @return RecursoSegundoJulgamentoSubstituicaoBO
     */
    private function getRecursoSegundoJulgamentoSubstituicaoBO()
    {
        if (empty($this->recursoSegundoJulgamentoSubstituicaoBO)) {
            $this->recursoSegundoJulgamentoSubstituicaoBO = app()->make(RecursoSegundoJulgamentoSubstituicaoBO::class);
        }

        return $this->recursoSegundoJulgamentoSubstituicaoBO;
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
}






