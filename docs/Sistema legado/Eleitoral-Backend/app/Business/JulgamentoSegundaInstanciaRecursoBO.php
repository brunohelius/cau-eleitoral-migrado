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
use App\Entities\JulgamentoSegundaInstanciaRecurso;
use App\Entities\JulgamentoSegundaInstanciaSubstituicao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailJulgamentoSegundaIntanciaRecursoJob;
use App\Mail\JulgamentoSegundaInstanciaRecursoDecisaoMail;
use App\Mail\JulgamentoSegundaInstanciaSubstituicaoDecisaoMail;
use App\Repository\JulgamentoSegundaInstanciaRecursoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\IndicacaoJulgamentoFinalTO;
use App\To\IndicacaoJulgamentoSegundaInstanciaRecursoTO;
use App\To\JulgamentoFinalTO;
use App\To\JulgamentoSegundaInstanciaRecursoTO;
use App\To\SubstituicaoRecursoTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'JulgamentoSegundaInstanciaRecurso'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoSegundaInstanciaRecursoBO extends AbstractBO
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
     * @var IndicacaoJulgamentoSegundaInstanciaRecursoBO
     */
    private $indicacaoJulgamentoSegundaInstanciaRecursoBO;

    /**
     * @var JulgamentoSegundaInstanciaSubstituicaoBO
     */
    private $julgamentoSegundaInstanciaSubstituicaoBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var RecursoJulgamentoFinalBO
     */
    private $recursoJulgamentoFinalBO;

    /**
     * @var JulgamentoSegundaInstanciaRecursoRepository
     */
    private $julgamentoSegundaInstanciaRecursoRepository;


    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salva o julgamento final da chapa da eleição
     *
     * @param JulgamentoSegundaInstanciaRecursoTO $julgSegundaInstanciaRecursoTO
     * @return JulgamentoSegundaInstanciaRecursoTO
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function salvar(JulgamentoSegundaInstanciaRecursoTO $julgSegundaInstanciaRecursoTO)
    {
        $arquivos = $julgSegundaInstanciaRecursoTO->getArquivos();
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validarCamposObrigatorios($julgSegundaInstanciaRecursoTO, $arquivo);

        $recursoJulgamentoFinal = $this->getRecursoJulgamentoFinalBO()->getRecursoJulgamentoFinalPorId(
            $julgSegundaInstanciaRecursoTO->getIdRecursoJulgamentoFinal()
        );

        $julgamentoSegundaInstanciaRecursoAnterior = $this->getJulgamentoPaiParaSalvar($julgSegundaInstanciaRecursoTO);

        if (
            !empty($recursoJulgamentoFinal)
            && !empty($recursoJulgamentoFinal->getJulgamentoFinal())
            && !empty($recursoJulgamentoFinal->getJulgamentoFinal()->getChapaEleicao())
        ) {
            $idChapaEleicao = $recursoJulgamentoFinal->getJulgamentoFinal()->getChapaEleicao()->getId();

            $chapaEleicao = $this->getChapaEleicaoBO()->getPorId($idChapaEleicao, true);
            $eleicao = $this->getEleicaoBO()->getEleicaoPorChapaEleicao($idChapaEleicao);
        } else {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $this->validacaoComplementarSalvarJulgamentoFinal($julgSegundaInstanciaRecursoTO, $chapaEleicao, $eleicao);

        try {
            $this->beginTransaction();

            $julgamentoSegundaInstancia = $this->prepararJulgamentoSalvar(
                $julgSegundaInstanciaRecursoTO,
                $recursoJulgamentoFinal,
                $arquivo,
                $julgamentoSegundaInstanciaRecursoAnterior
            );

            $this->getJulgamentoSegundaInstanciaRecursoRepository()->persist($julgamentoSegundaInstancia);

            $this->salvarIndicacoes($julgSegundaInstanciaRecursoTO->getIndicacoes(), $julgamentoSegundaInstancia);

            $this->salvarHistoricoJulgamentoFinalSegundaInstancia($julgamentoSegundaInstancia);

            $this->atualizarStatusChapaPosJulgamento($chapaEleicao, $julgamentoSegundaInstancia);

            $this->salvarArquivo($julgamentoSegundaInstancia, $arquivo, $julgamentoSegundaInstanciaRecursoAnterior);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if (empty($julgSegundaInstanciaRecursoTO->getIdJulgamentoSegundaInstanciaRecursoPai())) {
            Utils::executarJOB(new EnviarEmailJulgamentoSegundaIntanciaRecursoJob($julgamentoSegundaInstancia->getId()));
        }

        return JulgamentoSegundaInstanciaRecursoTO::newInstanceFromEntity($julgamentoSegundaInstancia);
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
        /** @var JulgamentoSegundaInstanciaRecurso $julgamentoFinal */
        $julgamentoSegundaInstanciaRecurso = $this->getJulgamentoSegundaInstanciaRecursoRepository()->find($id);

        if (!empty($julgamentoSegundaInstanciaRecurso)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoRecursoSegundaInstancia(
                $julgamentoSegundaInstanciaRecurso->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $julgamentoSegundaInstanciaRecurso->getNomeArquivoFisico(),
                $julgamentoSegundaInstanciaRecurso->getNomeArquivo()
            );
        }
    }


    /**
     * Retorna o Julgamento de Segunda Instancia Recurso por ID.
     *
     * @param $id
     * @return JulgamentoFinal | mixed
     */
    public function finById($id)
    {
        return $this->getJulgamentoSegundaInstanciaRecursoRepository()->find($id);
    }

    /**
     * Responsável por realizar envio de e-mail após o cadastro do julgamento
     *
     * @param $idJulgamentoRecurso
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailCadastroJulgamento($idJulgamentoRecurso)
    {
        /** @var JulgamentoSegundaInstanciaRecurso $julgamento */
        $julgamento = $this->getJulgamentoSegundaInstanciaRecursoRepository()
                                                    ->getJulgamentoSegundaInstanciaRecusoPorId($idJulgamentoRecurso);
        $julgamentoFinalTO = JulgamentoSegundaInstanciaRecursoTO::newInstanceFromEntity($julgamento);

        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao(
            $julgamento->getRecursoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao()->getId(),
            Constants::ATIVIDADE_PRIMARIA_JULGAMENTO_FINAL_SEGUNDA_INSTANCIA,
            Constants::ATIVIDADE_SECUNDARIA_JULGAMENTO_FINAL_SEGUNDA_INSTANCIA
        );

        $destinatarios = $this->getDestinatariosEnvioEmailCadastro($julgamento, $atividade);

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(),
                $this->getTipoEmailCadastroJulgamento($julgamento->getStatusJulgamentoFinal()->getId())
            );

            if (!empty($emailAtividadeSecundaria)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios($destinatarios);

                Email::enviarMail(new JulgamentoSegundaInstanciaRecursoDecisaoMail($emailTO, $julgamentoFinalTO));
            }
        }
    }

    /**
     * Retorna o Julgamento Segunda Instancia Recurso pelo ID do Julgamento Final.
     *
     * @param $id
     * @return JulgamentoSegundaInstanciaRecurso | mixed
     */
    public function getPorJulgamentoFinal($idJulgamentoFinal)
    {
        return $this->getJulgamentoSegundaInstanciaRecursoRepository()->getPorJulgamentoFinal($idJulgamentoFinal);
    }

    /**
     * Retorna um Julgamento Final conforme o id informado da chapa.
     *
     * @param $idChapaEleicao
     * @return JulgamentoSegundaInstanciaRecurso | mixed
     */
    public function getPorChapaEleicao($idChapaEleicao)
    {
        $julgamento= $this->getJulgamentoSegundaInstanciaRecursoRepository()->getPorChapa($idChapaEleicao);

        $julgamentoRetorno = [];
        if (!empty($julgamento)) {
            $julgamentoRetorno = [JulgamentoSegundaInstanciaRecursoTO::newInstanceFromEntity($julgamento, false, true)];
        }

        return $julgamentoRetorno;
    }

    /**
     * Retorna o Julgamento Segunda Instancia Substituição pelo ID do Julgamento Final.
     *
     * @param $idJulgamentoFinal
     * @return JulgamentoSegundaInstanciaSubstituicao | mixed
     * @throws NonUniqueResultException
     */
    public function getUltimoPorChapa($idChapaEleicao)
    {
        return $this->getJulgamentoSegundaInstanciaRecursoRepository()->getPorChapa($idChapaEleicao);
    }

    /**
     * Retorna todos os julgamento do pedido de substituição de uma chapa
     * @param $idRecurso
     * @return array
     */
    public function getRetificacoes($idRecurso)
    {
        $julgamentos = $this->getJulgamentoSegundaInstanciaRecursoRepository()->getRetificacoes(
            $idRecurso
        );

        $julgamentosTO = [];
        if (!empty($julgamentos)) {
            array_pop($julgamentos);

            $sequencia = 1;
            foreach ($julgamentos as $julgamento) {
                $julgamentoTO = SubstituicaoRecursoTO::newInstanceFromJulgamentoSegundaInstanciaRecurso($julgamento);
                $julgamentoTO->setSequencia($sequencia);
                $julgamentosTO[] = $julgamentoTO;
                $sequencia++;
            }
        }

        return $julgamentosTO;
    }

    /**
     * Retorna o ID do Julgamento de segunda Instancia pelo ID do Recurso Final
     *
     * @param $idRecursoFinal
     * @return mixed|null
     */
    public function getIdJulgamentoSegundaInstanciaRecursoPorRecursoFinal($idRecursoFinal)
    {
        return $this->getJulgamentoSegundaInstanciaRecursoRepository()
            ->getIdJulgamentoSegundaInstanciaRecursoPorRecursoFinal($idRecursoFinal);
    }

    /**
     * Método auxiliar para buscar os e-mails dos destinatários
     * @param JulgamentoSegundaInstanciaRecurso $julgamento
     * @param AtividadeSecundariaCalendario $atividade
     * @return array
     * @throws NegocioException
     */
    private function getDestinatariosEnvioEmailCadastro($julgamento, $atividade): array
    {
        $idTipoCandidatura = $julgamento->getRecursoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao()->getTipoCandidatura()->getId();

        $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
        $idCauUf = $isIES ? null : $julgamento->getRecursoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao()->getIdCauUf();

        $emailsComissao = [];
        /*$emailsComissao = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $atividade->getId(), $idCauUf
        );*/

        $emailsAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        $emailsResponsaveis = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa(
            $julgamento->getRecursoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao()->getId()
        );

        $destinatarios = array_merge($emailsComissao, $emailsAssessores, $emailsResponsaveis);
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
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param JulgamentoSegundaInstanciaRecursoTO $julgamentoFinalTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     */
    private function validarCamposObrigatorios($julgamentoFinalTO, $arquivo)
    {
        if (empty($julgamentoFinalTO->getIdRecursoJulgamentoFinal())) {
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
            empty($julgamentoFinalTO->getIdJulgamentoSegundaInstanciaRecursoPai())
            || (!empty($julgamentoFinalTO->getIdJulgamentoSegundaInstanciaRecursoPai())
                && !empty($arquivo) && !empty($arquivo->getArquivo()))
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

        $emptyJulstificativa = empty($julgamentoFinalTO->getRetificacaoJustificativa());
        if (!empty($julgamentoFinalTO->getIdJulgamentoSegundaInstanciaRecursoPai()) && $emptyJulstificativa) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }
    }

    /**
     * Método faz validação complementar do cadastrado do julgamento do recurso
     *
     * @param JulgamentoSegundaInstanciaRecursoTO $julgSegundaInstanciaRecursoTO
     * @param ChapaEleicao $chapaEleicao
     * @param $eleicao
     * @throws NegocioException
     */
    private function validacaoComplementarSalvarJulgamentoFinal($julgSegundaInstanciaRecursoTO, $chapaEleicao, $eleicao)
    {
        if (empty($chapaEleicao) || empty($eleicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $this->verificaPermissaoRealizarJulgamento($chapaEleicao, $julgSegundaInstanciaRecursoTO);

        $idJulgamentoPai = $julgSegundaInstanciaRecursoTO->getIdJulgamentoSegundaInstanciaRecursoPai();
        if (empty($idJulgamentoPai)) {
            $idJulgamento = $this->getJulgamentoSegundaInstanciaRecursoRepository()
                ->getIdJulgamentoSegundaInstanciaRecursoPorChapaEleicao(
                $chapaEleicao->getId()
            );
            if (!empty($idJulgamento)) {
                throw new NegocioException(Lang::get('messages.julgamento_segunda_instancia_recurso.ja_realizado'));
            }
        }else {
            $julgamentoPai = $this->getJulgamentoSegundaInstanciaRecursoRepository()->findBy(
                ['julgamentoSegundaInstanciaRecursoPai' => $idJulgamentoPai]
            );
            if (!empty($julgamentoPai)) {
                throw new NegocioException(Lang::get('messages.julgamento_final_segunda_instancia.ja_realizado_alteracao'));
            }
        }

        $this->validaIndicacoes($julgSegundaInstanciaRecursoTO, $chapaEleicao);
    }

    /**
     * Método auxiliar para verificar a permissão do usuário autenticado de realizar julgamento
     * @param ChapaEleicao $chapaEleicao
     * @param JulgamentoSegundaInstanciaRecursoTO $julgSegundaInstanciaRecursoTO
     * @throws NegocioException
     */
    private function verificaPermissaoRealizarJulgamento(ChapaEleicao $chapaEleicao, $julgSegundaInstanciaRecursoTO)
    {
        $isAlteracao = !empty($julgSegundaInstanciaRecursoTO->getIdJulgamentoSegundaInstanciaRecursoPai());

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
                foreach ($chapaEleicao->getMembrosChapa() as $membroChapa) {
                    if (
                        $membroChapa->getTipoParticipacaoChapa()->getId() == $indicacao->getIdTipoParicipacaoChapa()
                        && $membroChapa->getNumeroOrdem() == $indicacao->getNumeroOrdem()
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
     * @param IndicacaoJulgamentoSegundaInstanciaRecursoTO[] $indicacoesTO
     * @param JulgamentoSegundaInstanciaRecurso|Object $julgamentoSegundaInstanciaRecurso
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function salvarIndicacoes($indicacoesTO, $julgamentoSegundaInstanciaRecurso)
    {
        if (
            $julgamentoSegundaInstanciaRecurso->getStatusJulgamentoFinal()->getId() == Constants::STATUS_JULG_FINAL_INDEFERIDO
            && !empty($indicacoesTO)
        ) {
            $this->getIndicacaoJulgamentoSegundaInstanciaRecursoBO()->salvarIndicacoes(
                $indicacoesTO,
                $julgamentoSegundaInstanciaRecurso
            );
        }
    }

    /**
     * Método auxiliar para preparar entidade JulgamentoRecursoImpugnacao para cadastro
     *
     * @param JulgamentoSegundaInstanciaRecursoTO $julgamentoFinalTO
     * @param $recursoJulgamentoFinal
     * @param ArquivoGenericoTO|null $arquivo
     * @param JulgamentoSegundaInstanciaRecurso $julgamentoSegundaInstanciaRecursoAnterior
     * @return JulgamentoSegundaInstanciaRecurso
     * @throws Exception
     */
    private function prepararJulgamentoSalvar(
        $julgamentoFinalTO,
        $recursoJulgamentoFinal,
        $arquivo = null,
        $julgamentoSegundaInstanciaRecursoAnterior = null
    ) {
        if (empty($arquivo) || empty($arquivo->getArquivo())) {
            $nomeArquivo = $julgamentoSegundaInstanciaRecursoAnterior->getNomeArquivo();
            $nomeArquivoFisico = $julgamentoSegundaInstanciaRecursoAnterior->getNomeArquivoFisico();
        } else {
            $nomeArquivo = $arquivo->getNome();
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getNome(), Constants::PREFIXO_ARQ_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA
            );
        }

        $julgSegundaInstanciaRecurso = JulgamentoSegundaInstanciaRecurso::newInstance([
            'dataCadastro' => Utils::getData(),
            'nomeArquivo' => $nomeArquivo,
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'statusJulgamentoFinal' => ['id' => $julgamentoFinalTO->getIdStatusJulgamentoFinal()],
            'descricao' => $julgamentoFinalTO->getDescricao(),
            'usuario' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->id],
            'recursoJulgamentoFinal' => ['id' => $recursoJulgamentoFinal->getId()]
        ]);

        if (!empty($julgamentoFinalTO->getIdJulgamentoSegundaInstanciaRecursoPai())) {
            $idJulgamentoPai = $julgamentoFinalTO->getIdJulgamentoSegundaInstanciaRecursoPai();
            $julgSegundaInstanciaRecurso->setRetificacaoJustificativa($julgamentoFinalTO->getRetificacaoJustificativa());
            $julgSegundaInstanciaRecurso->setJulgamentoSegundaInstanciaRecursoPai(
                JulgamentoSegundaInstanciaRecurso::newInstance(['id' => $idJulgamentoPai])
            );
        }

        return $julgSegundaInstanciaRecurso;
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param JulgamentoSegundaInstanciaRecurso $julgamentoSegundaInstancia
     * @param ArquivoGenericoTO $arquivo
     * @param JulgamentoSegundaInstanciaRecurso $julgamentoSegundaInstanciaRecursoAnterior
     */
    private function salvarArquivo($julgamentoSegundaInstancia, $arquivo, $julgamentoSegundaInstanciaRecursoAnterior)
    {
        $caminhoDestino = $this->getArquivoService()->getCaminhoRepositorioJulgamentoSegundaInstanciaRecurso(
            $julgamentoSegundaInstancia->getId()
        );

        if (!empty($arquivo)) {
            $this->getArquivoService()->salvar(
                $caminhoDestino, $julgamentoSegundaInstancia->getNomeArquivoFisico(), $arquivo->getArquivo()
            );
        } else {
            $caminhoOrigem = $this->getArquivoService()->getCaminhoRepositorioJulgamentoSegundaInstanciaRecurso(
                $julgamentoSegundaInstanciaRecursoAnterior->getId()
            );

            $hasArquivo = $this->getArquivoService()->hasArquivo(
                $caminhoOrigem, $julgamentoSegundaInstanciaRecursoAnterior->getNomeArquivoFisico()
            );

            if ($hasArquivo) {
                $this->getArquivoService()->copiar(
                    $caminhoOrigem,
                    $caminhoDestino,
                    $julgamentoSegundaInstanciaRecursoAnterior->getNomeArquivoFisico(),
                    $julgamentoSegundaInstancia->getNomeArquivoFisico()
                );
            }
        }
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param JulgamentoSegundaInstanciaRecurso|Object $julgamentoFinal
     * @throws Exception
     */
    private function salvarHistoricoJulgamentoFinalSegundaInstancia($julgamentoFinal)
    {
        $isInclusao = empty($julgamentoFinal->getJulgamentoSegundaInstanciaRecursoPai());

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
     * Retorna uma nova instância de 'JulgamentoSegundaInstanciaRecurso'.
     *
     * @return JulgamentoSegundaInstanciaRecursoRepository|\Doctrine\ORM\EntityRepository
     */
    private function getJulgamentoSegundaInstanciaRecursoRepository()
    {
        if (empty($this->julgamentoSegundaInstanciaRecursoRepository)) {
            $this->julgamentoSegundaInstanciaRecursoRepository = $this->getRepository(JulgamentoSegundaInstanciaRecurso::class);
        }

        return $this->julgamentoSegundaInstanciaRecursoRepository;
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
     * Retorna uma nova instância de 'IndicacaoJulgamentoSegundaInstanciaRecursoBO'.
     *
     * @return IndicacaoJulgamentoSegundaInstanciaRecursoBO|mixed
     */
    private function getIndicacaoJulgamentoSegundaInstanciaRecursoBO()
    {
        if (empty($this->indicacaoJulgamentoSegundaInstanciaRecursoBO)) {
            $this->indicacaoJulgamentoSegundaInstanciaRecursoBO = app()->make(IndicacaoJulgamentoSegundaInstanciaRecursoBO::class);
        }

        return $this->indicacaoJulgamentoSegundaInstanciaRecursoBO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoSegundaInstanciaSubstituicaoBO'.
     *
     * @return JulgamentoSegundaInstanciaSubstituicaoBO|mixed
     */
    private function getJulgamentoSegundaInstanciaSubstituicaoBO()
    {
        if (empty($this->julgamentoSegundaInstanciaSubstituicaoBO)) {
            $this->julgamentoSegundaInstanciaSubstituicaoBO = app()->make(JulgamentoSegundaInstanciaSubstituicaoBO::class);
        }

        return $this->julgamentoSegundaInstanciaSubstituicaoBO;
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
     * Retorna uma nova instância de 'RecursoJulgamentoFinalBO'.
     *
     * @return RecursoJulgamentoFinalBO
     */
    private function getRecursoJulgamentoFinalBO()
    {
        if (empty($this->recursoJulgamentoFinalBO)) {
            $this->recursoJulgamentoFinalBO = app()->make(RecursoJulgamentoFinalBO::class);
        }

        return $this->recursoJulgamentoFinalBO;
    }

    /**
     * Método auxiliar para buscar o julgamento pai para salvar quando for alteração
     * @param JulgamentoSegundaInstanciaRecursoTO $julgamentoSegundaInstanciaRecursoTO
     * @return JulgamentoSegundaInstanciaRecurso|null
     * @throws NegocioException
     */
    private function getJulgamentoPaiParaSalvar($julgamentoSegundaInstanciaRecursoTO)
    {
        /** @var JulgamentoSegundaInstanciaRecurso $julgamentoSegundaInstanciaRecursoAnterior */
        $julgamentoSegundaInstanciaRecursoAnterior = null;
        if (!empty($julgamentoSegundaInstanciaRecursoTO->getIdJulgamentoSegundaInstanciaRecursoPai())) {
            $julgamentoSegundaInstanciaRecursoAnterior = $this->getJulgamentoSegundaInstanciaRecursoRepository()->find(
                $julgamentoSegundaInstanciaRecursoTO->getIdJulgamentoSegundaInstanciaRecursoPai()
            );

            if (empty($julgamentoSegundaInstanciaRecursoAnterior)) {
                throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
            }
        }
        return $julgamentoSegundaInstanciaRecursoAnterior;
    }

    /**
     * Verifica for inclusão é realizado a alteração do status julgamneto da chapa e se for
     * alteração ele verifica se é o último julgamento realizado para alterar o status
     * @param ChapaEleicao|null $chapaEleicao
     * @param JulgamentoSegundaInstanciaRecurso $julgSegundaInstanciaRecurso
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function atualizarStatusChapaPosJulgamento($chapaEleicao, $julgSegundaInstanciaRecurso): void
    {
        $isAlterarStatusChapa = true;
        if (!empty($julgSegundaInstanciaRecurso->getRetificacaoJustificativa())) {
            $julgamentoSubstituicao = $this->getJulgamentoSegundaInstanciaSubstituicaoBO()->getUltimoPorChapa(
                $julgSegundaInstanciaRecurso->getRecursoJulgamentoFinal()->getJulgamentoFinal()->getChapaEleicao()->getId()
            );

            $isAlterarStatusChapa = empty($julgamentoSubstituicao);
        }

        if ($isAlterarStatusChapa) {
            $this->getChapaEleicaoBO()->atualizarChapaEleicaoPosJulgamentoFinal(
                $chapaEleicao, $julgSegundaInstanciaRecurso->getStatusJulgamentoFinal()->getId()
            );
        }
    }
}






