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

use App\Entities\SubstituicaoJulgamentoFinal;
use App\Jobs\EnviarEmailAlteradoJulgamentoFinalJob;
use App\Mail\JulgamentoFinalAlteradoMail;
use App\Repository\SubstituicaoJulgamentoFinalRepository;
use App\To\EmailTO;
use App\To\SubstituicaoRecursoTO;
use Exception;
use App\Util\Email;
use App\Util\Utils;
use App\To\ArquivoTO;
use App\Config\Constants;
use App\To\MembroChapaTO;
use App\Exceptions\Message;
use App\Entities\MembroChapa;
use App\To\ArquivoGenericoTO;
use App\To\JulgamentoFinalTO;
use App\Entities\ChapaEleicao;
use App\To\ListMembrosChapaTO;
use App\Service\ArquivoService;
use App\Entities\JulgamentoFinal;
use App\Service\CorporativoService;
use Doctrine\ORM\NoResultException;
use App\Exceptions\NegocioException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use App\Mail\AtividadeSecundariaMail;
use App\To\IndicacaoJulgamentoFinalTO;
use Doctrine\ORM\NonUniqueResultException;
use App\Jobs\EnviarEmailJulgamentoFinalJob;
use App\Mail\JulgamentoFinalCadastradoMail;
use App\Repository\JulgamentoFinalRepository;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\JulgamentoSegundaInstanciaRecurso;
use App\Entities\JulgamentoSegundaInstanciaSubstituicao;
use App\Repository\JulgamentoSegundaInstanciaRecursoRepository;
use App\Repository\JulgamentoSegundaInstanciaSubstituicaoRepository;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'JulgamentoFinal'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoFinalBO extends AbstractBO
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
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * @var JulgamentoFinalRepository
     */
    private $julgamentoFinalRepository;

    /**
     * @var JulgamentoSegundaInstanciaRecursoRepository
     */
    private $julgamentoSegundaInstanciaRecursoRepository;

    /**
     * @var JulgamentoRecursoPedidoSubstituicaoBO
     */
    private $julgamentoRecursoPedidoSubstituicaoBO;

    /**
     * @var JulgamentoSegundaInstanciaSubstituicaoBO
     */
    private $julgamentoSegundaInstanciaSubstituicaoBO;

    /**
     * @var JulgamentoSegundaInstanciaRecursoBO
     */
    private $julgamentoSegundaInstanciaRecursoBO;

    /**
     * @var JulgamentoSegundaInstanciaSubstituicaoRepository
     */
    private $julgamentoSegundaInstanciaSubstituicaoRepository;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var UfCalendarioBO
     */
    private $ufCalendarioBO;

    /**
     * @var IndicacaoJulgamentoFinalBO
     */
    private $indicacaoJulgamentoFinalBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var ProporcaoConselheiroExtratoBO
     */
    private $proporcaoConselheiroExtratoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Retorna o julgamento de acordo com id da chapa
     * @param $idChapa
     * @return JulgamentoFinal
     */
    public function findByIdChapa($idChapa) {
        $julgamento =  $this->getJulgamentoFinalRepository()->findBy([
            'chapaEleicao' => $idChapa
        ]);
        if (!empty($julgamento)) {
            $julgamento = array_pop($julgamento);
        }

        return $julgamento;
    }

    /**
     * Retorna o julgamento de acordo com id da chapa
     * @param $idChapa
     * @return JulgamentoFinalTO
     */
    public function getUltimoJulgamentoPorChapa($idChapa) {
        return $this->getJulgamentoFinalRepository()->getPorChapaEleicao($idChapa);
    }

    /**
     * Retorna um Julgamento Final conforme o id informado da chapa.
     *
     * @param $idChapaEleicao
     * @param bool $verificarUsuarioResponsavel
     * @param bool $verificarUsuarioMembroComissao
     * @return JulgamentoFinalTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getPorChapaEleicao(
        $idChapaEleicao,
        $verificarUsuarioResponsavel = false,
        $verificarUsuarioMembroComissao = false
    ) {
        $julgamentoFinalTO = null;

        $isProfissional = $this->getUsuarioFactory()->isProfissional();
        if ($isProfissional && ($verificarUsuarioResponsavel || $verificarUsuarioMembroComissao)) {
            $julgamentoFinalTO = $this->getPorChapaEleicaoComVerificacaoUsuario(
                $idChapaEleicao,
                $verificarUsuarioResponsavel,
                $verificarUsuarioMembroComissao
            );
        } else {
            $julgamentoFinalTO = $this->getJulgamentoFinalRepository()->getPorChapaEleicao($idChapaEleicao);
        }

        if (!empty($julgamentoFinalTO)) {
            $retificacoes = $this->getJulgamentoFinalRepository()->getRetificacoesPorChapaEleicao($idChapaEleicao);
            if (!empty($retificacoes)) {
                array_pop($retificacoes);
            }

            $julgamentoFinalTO->setRetificacoes($retificacoes);
        }

        return $julgamentoFinalTO;
    }

    /**
     * Salva o julgamento final da chapa da eleição
     *
     * @param JulgamentoFinalTO $julgamentoFinalTO
     * @return JulgamentoFinalTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function salvar(JulgamentoFinalTO $julgamentoFinalTO)
    {
        $arquivos = $julgamentoFinalTO->getArquivos();

        /** @var ArquivoGenericoTO|null $arquivo */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validacaoIncialSalvarJulgamentoFinal($julgamentoFinalTO, $arquivo);

        /** @var ChapaEleicao $chapaEleicao */
        $chapaEleicao = $this->getChapaEleicaoBO()->getPorId($julgamentoFinalTO->getIdChapaEleicao(), true);

        $eleicao = $this->getEleicaoBO()->getEleicaoPorChapaEleicao($julgamentoFinalTO->getIdChapaEleicao());

        $this->validacaoComplementarSalvarJulgamentoFinal($julgamentoFinalTO, $chapaEleicao, $eleicao);

        try {
            $this->beginTransaction();

            $julgamentoFinal = $this->prepararJulgamentoSalvar($julgamentoFinalTO, $chapaEleicao, $arquivo);

            $this->getJulgamentoFinalRepository()->persist($julgamentoFinal);

            $this->salvarIndicacoes($julgamentoFinalTO->getIndicacoes(), $julgamentoFinal);

            $this->salvarHistoricoJulgamentoFinal($julgamentoFinal);

            $this->getChapaEleicaoBO()->atualizarChapaEleicaoPosJulgamentoFinal(
                $chapaEleicao, $julgamentoFinalTO->getIdStatusJulgamentoFinal()
            );

            if(empty($arquivo->getNomeFisico())) {
                $this->salvarArquivo(
                    $julgamentoFinal->getId(), $arquivo->getArquivo(), $julgamentoFinal->getNomeArquivoFisico()
                );
            } else {
                $this->copiarArquivoJulgamentoPai(
                    $julgamentoFinal
                );
            }

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if(!empty($julgamentoFinal->getJulgamentoFinalPai())) {
            //Utils::executarJOB(new EnviarEmailAlteradoJulgamentoFinalJob($julgamentoFinal->getId()));
        } else {
            Utils::executarJOB(new EnviarEmailJulgamentoFinalJob($julgamentoFinal->getId()));
        }
        Utils::executarJOB(new EnviarEmailAlteradoJulgamentoFinalJob($julgamentoFinal->getId()));
        return JulgamentoFinalTO::newInstanceFromEntity($julgamentoFinal);
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoJulgamentoFinal($id)
    {
        /** @var JulgamentoFinal $julgamentoFinal */
        $julgamentoFinal = $this->getJulgamentoFinalRepository()->find($id);

        if (!empty($julgamentoFinal)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoFinal($julgamentoFinal->getId());

            return $this->getArquivoService()->getArquivo(
                $caminho, $julgamentoFinal->getNomeArquivoFisico(), $julgamentoFinal->getNomeArquivo()
            );
        }
    }

    /**
     * Método que busca membros chapa organizados para realizar a indicação no julgamento
     * @param $idChapaEleicao
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getMembrosChapaParaJulgamentoFinal($idChapaEleicao)
    {
        /** @var ChapaEleicao $chapaEleicao */
        $chapaEleicao = $this->getChapaEleicaoBO()->getPorId($idChapaEleicao, true);

        if (empty($chapaEleicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $this->verificaPermissaoRealizarJulgamento($chapaEleicao);

        $numeroProporcao = 0;
        if ($chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR) {
            $numeroProporcao = $this->getProporcaoConselheiroExtratoBO()->getProporcaoConselheirosPorAtividadeEIdCauUf(
                $chapaEleicao->getAtividadeSecundariaCalendario()->getAtividadePrincipalCalendario()->getId(),
                $chapaEleicao->getIdCauUf()
            );
        }

        $listaMembros = ListMembrosChapaTO::newInstance();
        $listaMembros->setMembrosComPendencia([]);
        $listaMembros->setMembrosSemPendencia([]);

        $posicao = 0;
        while ($posicao <= $numeroProporcao) {

            $membroTitular = $this->getMembroChapaByList(
                $posicao, Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR, $chapaEleicao->getMembrosChapa()
            );

            $membroSuplente = $this->getMembroChapaByList(
                $posicao, Constants::TIPO_PARTICIPACAO_CHAPA_SUPLENTE, $chapaEleicao->getMembrosChapa()
            );

            $this->addMembroChapaToListMembroChapaTO($membroTitular, $listaMembros);
            $this->addMembroChapaToListMembroChapaTO($membroSuplente, $listaMembros);

            $posicao++;
        }

        return $listaMembros;
    }

    /**
     * Retorna o julgamento de segunda instância de acordo com id da chapa
     * @param $idChapaEleicao
     * @return mixed|array
     */
    public function getJulgamentoSegundaInstanciaPorChapaEleicao($idChapaEleicao)
    {
        $julgamentosSubstituicao = $this->getJulgamentoSegundaInstanciaSubstituicaoBO()->getPorChapaEleicao(
            $idChapaEleicao
        );

        $julgamentosRecursoSubstituicao = $this->getJulgamentoRecursoPedidoSubstituicaoBO()->getPorChapaEleicao(
            $idChapaEleicao
        );

        $julgamentosTO = array_merge($julgamentosSubstituicao, $julgamentosRecursoSubstituicao);

        $substituicaoSegundaInstancia = array_values(Arr::sort($julgamentosTO, function ($value) {
            /** @var SubstituicaoRecursoTO $value */
            return $value->getDataCadastro();
        }));

        $recursoSegundaInstancia = $this->getJulgamentoSegundaInstanciaRecursoBO()->getPorChapaEleicao($idChapaEleicao);

        return compact('substituicaoSegundaInstancia', 'recursoSegundaInstancia');
    }

    /**
     * @param MembroChapaTO $membroChapa
     * @param ListMembrosChapaTO $listaMembros
     */
    private function addMembroChapaToListMembroChapaTO($membroChapa, &$listaMembros)
    {
        if (
            empty($membroChapa->getId())
            || $membroChapa->getStatusParticipacaoChapa()->getId() != Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
            || $membroChapa->getStatusValidacaoMembroChapa()->getId() == Constants::STATUS_VALIDACAO_MEMBRO_PENDENTE
        ) {
            $listaMembros->addMembrosComPendencia($membroChapa);
        } else {
            $listaMembros->addMembrosSemPendencia($membroChapa);
        }
    }

    /**
     *
     * @param int $posicao
     * @param int $idTipoParticipacao
     * @param array $membrosChapa
     */
    private function getMembroChapaByList($posicao, $idTipoParticipacao, $membrosChapa)
    {
        $descricaoTipoParticipacao = Constants::$descricaoTipoParicipacao[$idTipoParticipacao] ?? '';
        $membroChapaTO = MembroChapaTO::newInstance([
            'numeroOrdem' => $posicao,
            'tipoParticipacaoChapa' => [
                'id' => $idTipoParticipacao,
                'descricao' => $descricaoTipoParticipacao
            ]
        ]);

        if (!empty($membrosChapa)) {
            /** @var MembroChapa $membroChapa */
            foreach ($membrosChapa as $membroChapa) {
                if (
                    $membroChapa->getNumeroOrdem() == $posicao
                    && $membroChapa->getTipoParticipacaoChapa()->getId() == $idTipoParticipacao
                ) {
                    $membroChapaTO = MembroChapaTO::newInstanceFromEntity($membroChapa);
                    break;
                }
            }
        }
        return $membroChapaTO;
    }

    /**
     * Retorna o Julgamento Final por ID.
     *
     * @param $id
     * @return JulgamentoFinal | mixed
     */
    public function getPorId($id)
    {
        return $this->getJulgamentoFinalRepository()->find($id);
    }

    /**
     * Responsável por realizar envio de e-mail após o cadastro do julgamento
     *
     * @param $idJulgamentoFinal
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailCadastroJulgamento($idJulgamentoFinal)
    {
        /** @var JulgamentoFinal $julgamento */
        $julgamento = $this->getJulgamentoFinalRepository()->find($idJulgamentoFinal);
        $julgamentoFinalTO = JulgamentoFinalTO::newInstanceFromEntity($julgamento);

        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao(
            $julgamento->getChapaEleicao()->getId(), 5, 1
        );

        $isAddEmailsResponsaveis = Utils::getDataHoraZero() > Utils::getDataHoraZero($atividade->getDataFim());

        $destinatarios = $this->getDestinatariosEnvioEmailCadastro($julgamento, $atividade, $isAddEmailsResponsaveis);

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(),
                $this->getTipoEmailCadastroJulgamento($julgamento->getStatusJulgamentoFinal()->getId())
            );

            if (!empty($emailAtividadeSecundaria)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios($destinatarios);

                Email::enviarMail(new JulgamentoFinalCadastradoMail($emailTO, $julgamentoFinalTO));
            }
        }
    }

    /**
     * Responsável por realizar envio de e-mail após o alteracao do julgamento
     *
     * @param $idJulgamentoFinal
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailAlteracaoJulgamento($idJulgamentoFinal)
    {
        /** @var JulgamentoFinal $julgamento */
        $julgamento = $this->getJulgamentoFinalRepository()->find($idJulgamentoFinal);
        $julgamentoFinalTO = JulgamentoFinalTO::newInstanceFromEntity($julgamento);

        $idTipoCandidatura = $julgamento->getChapaEleicao()->getTipoCandidatura()->getId();
        $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
        $idCauUf = $isIES ? null : $julgamento->getChapaEleicao()->getIdCauUf();
        $emailsAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        if(!empty($emailsAssessores) || true) {
            $emailTO = EmailTO::newInstance([]);
            $emailTO->setIsRodapeAtivo(true);
            $emailTO->setIsCabecalhoAtivo(true);
            $emailTO->getDestinatarios($emailsAssessores);
            Email::enviarMail(new JulgamentoFinalAlteradoMail($emailTO, $julgamentoFinalTO));
        }
    }


    /**
     * Método realiza o envio de e-mail um dia antes do ínicio do julgamento final
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailInicioJulgamento()
    {
        $idTipo = Constants::EMAIL_JULGAMENTO_FINAL_IMPUGNACAO_ASSESSORES_PERIODO_SERA_ABERTO;
        $this->getEmailAtividadeSecundariaBO()->enviarEmailIncioPeriodoJulgamentoChapa(5, 1, $idTipo);
    }

    /**
     * Método realiza o envio de e-mail um dia antes do ínicio do julgamento final
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailInicioJulgamentoSegundaInstancia()
    {
        $idTipo = Constants::EMAIL_JULGAMENTO_FINAL_SEG_INSTANCIA_ASSESSORES_PERIODO_SERA_ABERTO;
        $this->getEmailAtividadeSecundariaBO()->enviarEmailIncioPeriodoJulgamentoChapa(5, 4, $idTipo);
    }

    /**
     * Método realiza o envio de e-mail no fim das atividades de julgamento
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailFimPeriodoJulgamentoFinal()
    {
        $dataFim = Utils::adicionarDiasData(Utils::getDataHoraZero(), 1);

        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            null, $dataFim, 5, 1
        );

        foreach ($atividades as $atividadeSecundariaCalendario) {

            $idsCauUf = $this->getChapaEleicaoBO()->getIdsCauUfChapasPorCalendarioSemJulgamento(
                $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId()
            );

            if (!empty($idsCauUf)) {
                if(array_key_exists(Constants::ID_CAU_BR, $idsCauUf)) {
                    unset($idsCauUf[Constants::ID_CAU_BR]);
                }

                $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE($idsCauUf);

                if (!empty($destinatarios)) {
                    $idTipoEmail = Constants::EMAIL_JULGAMENTO_FINAL_ASSESSORES_PERIODO_SERA_FECHADO;
                    $emailAtivSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                        $atividadeSecundariaCalendario->getId(), $idTipoEmail
                    );

                    if (!empty($emailAtivSecundaria)) {
                        $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao(
                            $emailAtivSecundaria
                        );
                        $emailTO->setDestinatarios($destinatarios);

                        Email::enviarMail(new AtividadeSecundariaMail($emailTO));
                    }
                }
            }
        }
    }

    /**
     * Método realiza o envio de e-mail no fim das atividades de julgamento
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailFimPeriodoJulgamentoFinalSegundaInstancia()
    {
        $dataFim = Utils::adicionarDiasData(Utils::getDataHoraZero(), 1);

        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            null, $dataFim, 5, 4
        );

        foreach ($atividades as $atividadeSecundariaCalendario) {

            $idsCauUf = $this->getUfCalendarioBO()->getIdsCauUfCalendario(
                $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId()
            );

            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE($idsCauUf);

            if (!empty($destinatarios)) {
                $emailAtivSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                    $atividadeSecundariaCalendario->getId(),
                    Constants::EMAIL_JULGAMENTO_FINAL_SEG_INSTANCIA_ASSESSORES_PERIODO_SERA_FECHADO
                );

                if (!empty($emailAtivSecundaria)) {
                    $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao(
                        $emailAtivSecundaria
                    );
                    $emailTO->setDestinatarios($destinatarios);

                    Email::enviarMail(new AtividadeSecundariaMail($emailTO));
                }
            }
        }
    }

    /**
     * Envia e-mail para o cadastro do julgamento no fim da atividade
     *
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function enviarEmailCadastroJulgamentoFimPeriodo()
    {
        $dataFim = Utils::subtrairDiasData(Utils::getDataHoraZero(), 1);

        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            null, $dataFim, 5, 1
        );

        foreach ($atividades as $atividadeSecundaria) {

            $julgamentosFinal = $this->getJulgamentoFinalRepository()->getPorCalendario(
                $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getId()
            );

            foreach ($julgamentosFinal as $julgamentoFinal) {
                $destinatarios = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa(
                    $julgamentoFinal->getChapaEleicao()->getId()
                );

                if (!empty($destinatarios)) {
                    $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                        $atividadeSecundaria->getId(),
                        $this->getTipoEmailCadastroJulgamento($julgamentoFinal->getStatusJulgamentoFinal()->getId())
                    );

                    if (!empty($emailAtividadeSecundaria)) {
                        $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao(
                            $emailAtividadeSecundaria
                        );
                        $emailTO->setDestinatarios($destinatarios);

                        Email::enviarMail(new JulgamentoFinalCadastradoMail(
                            $emailTO, JulgamentoFinalTO::newInstanceFromEntity($julgamentoFinal)
                        ));
                    }
                }
            }
        }
    }

    /**
     * Método auxiliar para buscar os e-mails dos destinatários
     * @param JulgamentoFinal $julgamento
     * @param AtividadeSecundariaCalendario $atividade
     * @param bool $isAdicionarEmailsResponsaveis
     * @return array
     * @throws NegocioException
     */
    private function getDestinatariosEnvioEmailCadastro($julgamento, $atividade, $isAdicionarEmailsResponsaveis): array
    {
        $idTipoCandidatura = $julgamento->getChapaEleicao()->getTipoCandidatura()->getId();

        $isIES = $idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES;
        $idCauUf = $isIES ? null : $julgamento->getChapaEleicao()->getIdCauUf();

        $emailsComissao = [];
        /*$emailsComissao = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $atividade->getId(), $idCauUf
        );*/

        $emailsAssessores = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        $emailsResponsaveis = [];
        if ($isAdicionarEmailsResponsaveis) {
            $emailsResponsaveis = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa(
                $julgamento->getChapaEleicao()->getId()
            );
        }
        $destinatarios = array_merge($emailsComissao, $emailsAssessores, $emailsResponsaveis);
        return $destinatarios;
    }

    /**
     * Método auxiliar para retornar o tipo de e-mail a ser enviado
     * @param $idStatusJulgamento
     * @return int
     */
    private function getTipoEmailCadastroJulgamento($idStatusJulgamento): int
    {
        $idTipoEmail = Constants::EMAIL_JULGAMENTO_FINAL_DEFERIDO;
        if ($idStatusJulgamento == Constants::STATUS_JULG_FINAL_INDEFERIDO) {
            $idTipoEmail = Constants::EMAIL_JULGAMENTO_FINAL_INDEFERIDO;
        }
        return $idTipoEmail;
    }

    /**
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param JulgamentoFinalTO $julgamentoFinalTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     */
    private function validacaoIncialSalvarJulgamentoFinal($julgamentoFinalTO, $arquivo)
    {
        //print_R($julgamentoFinalTO);
        if (empty($julgamentoFinalTO->getIdChapaEleicao())) {
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

        if (empty($arquivo)) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($arquivo->getArquivo()) && empty($arquivo->getNomeFisico())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if(!empty(empty($arquivo->getNomeFisico()))) {
            $this->getArquivoService()->validarArquivoGenrico(
                $arquivo, Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_10MB
            );
        }
    }

    /**
     * Método faz validação complementar do cadastrado do julgamento do recurso
     *
     * @param JulgamentoFinalTO $julgamentoFinalTO
     * @param ChapaEleicao $chapaEleicao
     * @param $eleicao
     * @throws NegocioException
     */
    private function validacaoComplementarSalvarJulgamentoFinal($julgamentoFinalTO, $chapaEleicao, $eleicao)
    {
        if (empty($chapaEleicao) || empty($eleicao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $this->verificaPermissaoRealizarJulgamento($chapaEleicao);

        if(empty($julgamentoFinalTO->getIdJulgamentoFinalPai())){
            $idJulgamento = $this->getJulgamentoFinalRepository()->getidJulgamentoPorChapaEleicao($chapaEleicao->getId());
            if (!empty($idJulgamento)) {
                throw new NegocioException(Lang::get('messages.julgamento_final.ja_realizado'));
            }
        }

        $this->validaIndicacoes($julgamentoFinalTO, $chapaEleicao);
    }

    /**
     * Método auxiliar para verificar a permissão do usuário autenticado de realizar julgamento
     * @param ChapaEleicao $chapaEleicao
     * @throws NegocioException
     */
    private function verificaPermissaoRealizarJulgamento(ChapaEleicao $chapaEleicao): void
    {
        $isCorporativoAssessorCEUF = $this->getUsuarioFactory()->isCorporativoAssessorCEUF();
        if (!$this->getUsuarioFactory()->isCorporativoAssessorCEN() && !$isCorporativoAssessorCEUF) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }

        $isIES = $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;

        if (
            !$this->getUsuarioFactory()->isCorporativoAssessorCEN()
            && !$isCorporativoAssessorCEUF
            && ($isIES || $this->getUsuarioFactory()->getUsuarioLogado()->idCauUf != $chapaEleicao->getIdCauUf())
        ) {
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
     * @param IndicacaoJulgamentoFinalTO[] $indicacoesTO
     * @param JulgamentoFinal $julgamentoFinal
     */
    private function salvarIndicacoes($indicacoesTO, $julgamentoFinal)
    {
        if (
            $julgamentoFinal->getStatusJulgamentoFinal()->getId() == Constants::STATUS_JULG_FINAL_INDEFERIDO
            && !empty($indicacoesTO)
        ) {
            $this->getIndicacaoJulgamentoFinalBO()->salvarIndicacoes($indicacoesTO, $julgamentoFinal);
        }
    }

    /**
     * Método auxiliar para preparar entidade JulgamentoRecursoImpugnacao para cadastro
     *
     * @param JulgamentoFinalTO $julgamentoFinalTO
     * @param ChapaEleicao|null $chapaEleicao
     * @param ArquivoGenericoTO|null $arquivo
     * @return JulgamentoFinal
     * @throws Exception
     */
    private function prepararJulgamentoSalvar($julgamentoFinalTO, $chapaEleicao, $arquivo)
    {
        $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
            $arquivo->getNome(), Constants::PREFIXO_ARQ_JULGAMENTO_FINAL
        );

        $julgamentoFinal = JulgamentoFinal::newInstance([
            'dataCadastro' => Utils::getData(),
            'nomeArquivo' => $arquivo->getNome(),
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'statusJulgamentoFinal' => ['id' => $julgamentoFinalTO->getIdStatusJulgamentoFinal()],
            'descricao' => $julgamentoFinalTO->getDescricao(),
            'usuario' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->id],
            'chapaEleicao' => ['id' => $chapaEleicao->getId()],
            'retificacaoJustificativa' => $julgamentoFinalTO->getRetificacaoJustificativa()
        ]);

        if (!empty($julgamentoFinalTO->getIdJulgamentoFinalPai())) {
            $julgamentoFinal->setJulgamentoFinalPai(JulgamentoFinal::newInstance([
                'id' => $julgamentoFinalTO->getIdJulgamentoFinalPai()
            ]));
        }

        return $julgamentoFinal;
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $idJulgamentoFinal
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idJulgamentoFinal, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioJulgamentoFinal($idJulgamentoFinal),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Copiar arquivo de Julgamento Pai.
     *
     * @param JulgamentoFinal $julgamentoFinal
     * @throws NegocioException
     */
    private function copiarArquivoJulgamentoPai(JulgamentoFinal $julgamentoFinal)
    {
        $caminhoOrigem = $this->getArquivoService()->getCaminhoRepositorioJulgamentoFinal($julgamentoFinal->getJulgamentoFinalPai()->getId());
        $caminhoDestino = $this->getArquivoService()->getCaminhoRepositorioJulgamentoFinal($julgamentoFinal->getId());
        $this->getArquivoService()->copiar(
            $caminhoOrigem, $caminhoDestino, $julgamentoFinal->getJulgamentoFinalPai()->getNomeArquivoFisico(), $julgamentoFinal->getNomeArquivoFisico()
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param JulgamentoFinal $julgamentoFinal
     * @throws Exception
     */
    private function salvarHistoricoJulgamentoFinal(JulgamentoFinal $julgamentoFinal): void
    {
        $historico = $this->getHistoricoBO()->criarHistorico(
            $julgamentoFinal,
            Constants::HISTORICO_ID_TIPO_JUGAMENTO_FINAL,
            Constants::HISTORICO_ACAO_INSERIR,
            Constants::HISTORICO_INCLUSAO_JULG_FINAL
        );
        $this->getHistoricoBO()->salvar($historico);
    }

    /**
     * Verifica se usuário pode visualizar julgamento de acordo com as seguinte regras:
     * - Deve ser um membro da comissão CEN ou Ce da Cau UF da chapa vinculada ao julgamento final
     *   se parâmetro para verificar estiver habilitado
     * - Deve ser um responsável da chapa ou impugnante vinculada ao julgamento final
     *   se parâmetro para verificar estiver habilitado e a atividade de julgamento estiver finalizada
     *
     * @param $idChapa
     * @param bool $verificarUsuarioResponsavel
     * @param bool $verificarUsuarioMembroComissao
     * @return JulgamentoFinalTO|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function getPorChapaEleicaoComVerificacaoUsuario(
        $idChapa,
        $verificarUsuarioResponsavel,
        $verificarUsuarioMembroComissao
    ) {
        $isPermitidoVisualizar = false;

        /** O Retificação do julgamento deve ser exibida apenas apos a data fim da atividade 5.1. */
        $order = 'ASC';
        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao( $idChapa, Constants::NIVEL_ATIVIDADE_PRINCIPAL_JULGAMENTO_FINAL, Constants::NIVEL_ATIVIDADE_SECUNDARIA_JULGAMENTO_FINAL );
        $isAtividadeSecundariaFinalizada = Utils::getDataHoraZero() > Utils::getDataHoraZero($atividadeSecundaria->getDataFim());
        if ($isAtividadeSecundariaFinalizada) {
            $order = 'DESC';
        }

        /** @var JulgamentoFinal $julgamentoFinal */
        $julgamentoFinal = $this->getJulgamentoFinalRepository()->findOneBy(['chapaEleicao' => $idChapa], ['id' => $order]);
        $julgamentoFinalTO = null;

        if (!empty($julgamentoFinal)) {
            $chapaEleicao = $julgamentoFinal->getChapaEleicao();
            $isIES = $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;

            $eleicao = $this->getEleicaoBO()->getEleicaoPorChapaEleicao($chapaEleicao->getId());

            if ($verificarUsuarioMembroComissao) {
                $isMembroComissao = $this->getMembroComissaoBO()->verificarMembroComissaoPorCauUf(
                    $eleicao->getCalendario()->getId(), $chapaEleicao->getIdCauUf(), $isIES
                );
                if (!$isMembroComissao) {
                    throw new NegocioException(Lang::get('messages.permissao.visualizacao_membro_comissao'));
                }
                $isPermitidoVisualizar = true;
            }

            if ($verificarUsuarioResponsavel) {
                $ativSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                    $eleicao->getCalendario()->getId(), 5, 1
                );
                $isFinalizadoAtiv = Utils::getDataHoraZero() > Utils::getDataHoraZero($ativSecundaria->getDataFim());

                if ($isFinalizadoAtiv) {
                    $idProfissionalLogado = $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional;

                    $idChapaEleicao = $this->getChapaEleicaoBO()->getIdChapaEleicaoPorCalendarioEResponsavel(
                        $eleicao->getCalendario()->getId(), $idProfissionalLogado
                    );
                    if (!empty($idChapaEleicao) && $idChapaEleicao == $chapaEleicao->getId()) {
                        $isPermitidoVisualizar = true;
                    }
                }
            }

            $julgamentoFinalTO = JulgamentoFinalTO::newInstanceFromEntity($julgamentoFinal);
        }

        return $isPermitidoVisualizar ? $julgamentoFinalTO : null;
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
     * Retorna uma nova instância de 'JulgamentoFinalRepository'.
     *
     * @return JulgamentoFinalRepository
     */
    private function getJulgamentoFinalRepository()
    {
        if (empty($this->julgamentoFinalRepository)) {
            $this->julgamentoFinalRepository = $this->getRepository(JulgamentoFinal::class);
        }

        return $this->julgamentoFinalRepository;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoSegundaInstanciaRecursoRepository'.
     *
     * @return JulgamentoSegundaInstanciaRecursoRepository
     */
    private function getJulgamentoSegundaInstanciaRecursoRepository()
    {
        if (empty($this->julgamentoSegundaInstanciaRecursoRepository)) {
            $this->julgamentoSegundaInstanciaRecursoRepository = $this->getRepository(JulgamentoSegundaInstanciaRecurso::class);
        }

        return $this->julgamentoSegundaInstanciaRecursoRepository;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoSegundaInstanciaSubstituicaoRepository'.
     *
     * @return JulgamentoSegundaInstanciaSubstituicaoRepository
     */
    private function getJulgamentoSegundaInstanciaSubstituicaoRepository()
    {
        if (empty($this->julgamentoSegundaInstanciaSubstituicaoRepository)) {
            $this->julgamentoSegundaInstanciaSubstituicaoRepository = $this->getRepository(JulgamentoSegundaInstanciaSubstituicao::class);
        }

        return $this->julgamentoSegundaInstanciaSubstituicaoRepository;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoPedidoSubstituicaoBO'.
     *
     * @return JulgamentoRecursoPedidoSubstituicaoBO|mixed
     */
    private function getJulgamentoRecursoPedidoSubstituicaoBO()
    {
        if (empty($this->julgamentoRecursoPedidoSubstituicaoBO)) {
            $this->julgamentoRecursoPedidoSubstituicaoBO = app()->make(JulgamentoRecursoPedidoSubstituicaoBO::class);
        }

        return $this->julgamentoRecursoPedidoSubstituicaoBO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoPedidoSubstituicaoBO'.
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
     * Retorna uma nova instância de 'JulgamentoSegundaInstanciaRecursoBO'.
     *
     * @return JulgamentoSegundaInstanciaRecursoBO|mixed
     */
    private function getJulgamentoSegundaInstanciaRecursoBO()
    {
        if (empty($this->julgamentoSegundaInstanciaRecursoBO)) {
            $this->julgamentoSegundaInstanciaRecursoBO = app()->make(JulgamentoSegundaInstanciaRecursoBO::class);
        }

        return $this->julgamentoSegundaInstanciaRecursoBO;
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
     * Retorna a intancia de 'UfCalendarioBO'.
     *
     * @return UfCalendarioBO
     */
    private function getUfCalendarioBO()
    {
        if ($this->ufCalendarioBO == null) {
            $this->ufCalendarioBO = app()->make(UfCalendarioBO::class);
        }
        return $this->ufCalendarioBO;
    }

    /**
     * Retorna uma nova instância de 'IndicacaoJulgamentoFinalBO'.
     *
     * @return IndicacaoJulgamentoFinalBO|mixed
     */
    private function getIndicacaoJulgamentoFinalBO()
    {
        if (empty($this->indicacaoJulgamentoFinalBO)) {
            $this->indicacaoJulgamentoFinalBO = app()->make(IndicacaoJulgamentoFinalBO::class);
        }

        return $this->indicacaoJulgamentoFinalBO;
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
     * Retorna uma nova instância de 'ProporcaoConselheiroExtratoBO'.
     *
     * @return ProporcaoConselheiroExtratoBO
     */
    private function getProporcaoConselheiroExtratoBO()
    {
        if (empty($this->proporcaoConselheiroExtratoBO)) {
            $this->proporcaoConselheiroExtratoBO = app()->make(ProporcaoConselheiroExtratoBO::class);
        }

        return $this->proporcaoConselheiroExtratoBO;
    }
}




