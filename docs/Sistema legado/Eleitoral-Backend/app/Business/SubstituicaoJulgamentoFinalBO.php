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

use App\Entities\IndicacaoJulgamentoRecursoPedidoSubstituicao;
use App\Entities\IndicacaoJulgamentoSegundaInstanciaRecurso;
use App\Entities\IndicacaoJulgamentoSegundaInstanciaSubstituicao;
use App\Entities\RecursoSegundoJulgamentoSubstituicao;
use App\Entities\SubstituicaoImpugnacao;
use App\Jobs\EnviarEmailSubstituicaoJulgamentoFinalJob;
use App\Mail\AtividadeSecundariaMail;
use App\Mail\SubstituicaoImpugnacaoCadastradoMail;
use App\Repository\RecursoSegundoJulgamentoSubstituicaoRepository;
use App\To\JulgamentoSegundaInstanciaRecursoTO;
use App\To\SubstituicaoImpugnacaoTO;
use App\Util\Email;
use Doctrine\Common\Collections\Collection;
use Exception;
use App\Util\Utils;
use App\To\ArquivoTO;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Mpdf\Tag\Sub;
use Twig_Error_Loader;
use Twig_Error_Syntax;
use Mpdf\MpdfException;
use Twig_Error_Runtime;
use App\Config\Constants;
use App\Exceptions\Message;
use App\Factory\PDFFActory;
use App\Entities\MembroChapa;
use App\To\ArquivoGenericoTO;
use App\Service\ArquivoService;
use App\Entities\JulgamentoFinal;
use App\Service\CorporativoService;
use App\Exceptions\NegocioException;
use App\To\PedidoSubstituicaoChapaTO;
use App\To\SubstituicaoJulgamentoFinalTO;
use App\Entities\IndicacaoJulgamentoFinal;
use Doctrine\ORM\NonUniqueResultException;
use App\To\AtividadeSecundariaCalendarioTO;
use App\Entities\SubstituicaoJulgamentoFinal;
use App\Entities\AtividadeSecundariaCalendario;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entities\MembroSubstituicaoJulgamentoFinal;
use App\Repository\SubstituicaoJulgamentoFinalRepository;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'SubstituicaoJulgamentoFinal'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class SubstituicaoJulgamentoFinalBO extends AbstractBO
{

    /**
     * @var JulgamentoFinalBO
     */
    private $julgamentoFinalBO;

    /**
     * @var MembroSubstituicaoJulgamentoFinalBO
     */
    private $membroSubstituicaoJulgamentoFinalBO;

    /**
     * @var JulgamentoSegundaInstanciaSubstituicaoBO
     */
    private $julgamentoSegundaInstanciaSubstituicaoBO;

    /**
     * @var JulgamentoSegundaInstanciaRecursoBO
     */
    private $julgamentoSegundaInstanciaRecursoBO;

    /**
     * @var JulgamentoRecursoPedidoSubstituicaoBO
     */
    private $julgamentoRecursoPedidoSubstituicaoBO;

    /**
     * @var HistoricoProfissionalBO
     */
    private $historicoProfissionalBO;

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
     * @var RecursoSegundoJulgamentoSubstituicaoBO
     */
    private $recursoSegundoJulgamentoSubstituicaoBO;

    /**
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * @var PedidoSubstituicaoChapaBO
     */
    private $pedidoSubstituicaoChapaBO;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var SubstituicaoJulgamentoFinalRepository
     */
    private $substituicaoJulgamentoFinalRepository;

    /**
     * @var RecursoSegundoJulgamentoSubstituicaoRepository
     */
    private $recursoSegundoJulgamentoSubstituicaoRepository;

    /**
     * @var PDFFActory
     */
    private $pdfFactory;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Retorna um Julgamento de Substituição conforme o id informado.
     *
     * @param $id
     *
     * @param bool $addPedidoSubstituicao
     * @return SubstituicaoJulgamentoFinalTO
     * @throws Exception
     */
    public function getPorId($id)
    {
        /** @var SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal */
        $substituicaoJulgamentoFinal = $this->getSubstituicaoJulgamentoFinalRepository()->find($id);

        $substituicaoJulgamentoFinalTO = null;
        if (!empty($substituicaoJulgamentoFinal)) {

            $substituicaoJulgamentoFinalTO = SubstituicaoJulgamentoFinalTO::newInstanceFromEntity(
                $substituicaoJulgamentoFinal
            );
        }

        return $substituicaoJulgamentoFinalTO;
    }

    /**
     * Retorna a quantidade de Julgamento Impugnação
     *
     * @param $id
     * @return SubstituicaoJulgamentoFinalTO|null
     * @throws Exception
     */
    public function getRecursoJulgamentoFinalPorId($id)
    {
        return $this->getSubstituicaoJulgamentoFinalRepository()->getRecursoJulgamentoFinalPorId($id);
    }


    /**
     * Retorna o julgamento de substituição chapa conforme o id informado.
     *
     * @param $id
     *
     * @return SubstituicaoJulgamentoFinal|null
     */
    public function findById($id)
    {
        /** @var SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal */
        $substituicaoJulgamentoFinal = $this->getSubstituicaoJulgamentoFinalRepository()->find($id);

        return $substituicaoJulgamentoFinal;
    }

    /**
     * Retorna pedido substituição julgamento.
     *
     * @param $idChapa
     * @return mixed|null
     * @throws Exception
     */
    public function getPorChapa($idChapa)
    {
        $substituicoes = $this->getSubstituicaoJulgamentoFinalRepository()->getPorChapa($idChapa);

        $substituicoesTO = [];
        $sequencia = 0;

        if (!empty($substituicoes)) {
            /** @var SubstituicaoJulgamentoFinal $substituicao */
            foreach ($substituicoes as $i => $substituicao) {
                $substituicaoJulgamentoFinalTO = SubstituicaoJulgamentoFinalTO::newInstanceFromEntity($substituicao);
                $sequencia++;
                $substituicaoJulgamentoFinalTO->setSequencia($sequencia);

                if (!empty($substituicao->getJulgamentoSegundaInstanciaSubstituicao())) {
                    $substituicaoJulgamentoFinalTO->setHasJulgamentoSegundaInstancia(true);
                }

                array_push($substituicoesTO, $substituicaoJulgamentoFinalTO);
            }
        }

        $recursosTO = $this->getRecursoSegundoJulgamentoSubstituicaoBO()->getPorChapa($idChapa);

        $substituicoesTO = array_merge($substituicoesTO ?? [], $recursosTO ?? []);

        return array_values(Arr::sort($substituicoesTO, function ($value) {
            /** @var SubstituicaoJulgamentoFinalTO $value */
            return $value->getDataCadastro();
        }));
    }

    /**
     * Retorna o total de pedido substituição julgamento.
     *
     * @param $idChapa
     * @return mixed|null
     * @throws Exception
     */
    public function hasSubstituicaoPorChapa($idChapa)
    {
        $total = $this->getSubstituicaoJulgamentoFinalRepository()->getTotalPorChapa($idChapa);

        return $total > 0;
    }

    /**
     * Retorna a atividade de secundária do julgamento de substituição
     *
     * @return AtividadeSecundariaCalendarioTO
     * @throws Exception
     */
    public function getAtividadeSecundariaCadastroJulgamento($idPedidoSubstituicao)
    {
        $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoSubstituicao($idPedidoSubstituicao);

        $atividadeSecundariaTO = null;
        if (!empty($eleicaoTO)) {
            $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                $eleicaoTO->getCalendario()->getId(), 2, 4
            );

            if (!empty($atividadeSecundaria)) {
                $atividadeSecundariaTO = AtividadeSecundariaCalendarioTO::newInstanceFromEntity($atividadeSecundaria);
            }
        }

        return $atividadeSecundariaTO;
    }

    /**
     * Verifica se o pedido de substituição pode ser julgado
     *
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapa
     * @return bool
     * @throws Exception
     */
    public function verificarPedidoSubstituicaoPodeSerJulgado(PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapa)
    {
        $podeSerJulgado = false;

        $isPermissaoJulgar = $this->isUsuarioComPermissaoJulgar(
            $pedidoSubstituicaoChapa->getChapaEleicao()->getIdCauUf()
        );

        $idStatusPedido = $pedidoSubstituicaoChapa->getStatusSubstituicaoChapa()->getId();
        $isStatusEmAndanmento = $idStatusPedido == Constants::STATUS_SUBSTITUICAO_CHAPA_EM_ANDAMENTO;

        if ($isPermissaoJulgar && $isStatusEmAndanmento) {
            $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoSubstituicao($pedidoSubstituicaoChapa->getId());

            $dataInicioVigencia = Utils::getDataHoraZero($eleicaoTO->getCalendario()->getDataInicioVigencia());
            $dataFimVigencia = Utils::getDataHoraZero($eleicaoTO->getCalendario()->getDataFimVigencia());

            if (
                !empty($eleicaoTO)
                || (Utils::getDataHoraZero() >= $dataInicioVigencia && Utils::getDataHoraZero() <= $dataFimVigencia)
            ) {
                $podeSerJulgado = true;
            }
        }

        return $podeSerJulgado;
    }

    /**
     * Salva o pedido de subsdtituição chapa
     *
     * @param SubstituicaoJulgamentoFinalTO $substituicaoJulgamentoFinalTO
     * @return SubstituicaoJulgamentoFinalTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function salvar(SubstituicaoJulgamentoFinalTO $substituicaoJulgamentoFinalTO)
    {
        $arquivos = $substituicaoJulgamentoFinalTO->getArquivos();

        /** @var ArquivoGenericoTO | null $arquivo */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validarSalvarJulgamentoFinal($substituicaoJulgamentoFinalTO, $arquivo);

        /** @var JulgamentoFinal $julgamentoFinal */
        $julgamentoFinal = $this->getJulgamentoFinalBO()->getPorId($substituicaoJulgamentoFinalTO->getIdJulgamentoFinal());


        $this->validacaoComplementarSalvarJulgamento($substituicaoJulgamentoFinalTO, $julgamentoFinal);

        $indicacoes = $julgamentoFinal->getIndicacoes();
        if (!$substituicaoJulgamentoFinalTO->getIsPrimeiraInstancia()) {
            $this->validacaoExtrasSalvarJulgamentoSegundaIntancia($substituicaoJulgamentoFinalTO, $julgamentoFinal, $indicacoes);
        }

        try {
            $this->beginTransaction();

            $substituicaoJulgamentoFinal = $this->prepararJulgamentoSalvar(
                $substituicaoJulgamentoFinalTO,
                $julgamentoFinal,
                $arquivo
            );

            $this->getSubstituicaoJulgamentoFinalRepository()->persist($substituicaoJulgamentoFinal);

            $this->salvarHistoricoSubstituicaoJulgamentoFinal($substituicaoJulgamentoFinal);

            $this->getChapaEleicaoBO()->atualizarStatusChapaJulgamentoFinal(
                $julgamentoFinal->getChapaEleicao()->getId(), Constants::STATUS_CHAPA_JULG_FINAL_AGUARDANDO
            );

            $membrosSubstituicaoJulgamentoFinal = $this->salvarMembrosSubstituicaoJulgamentoFinal(
                $substituicaoJulgamentoFinal,
                $julgamentoFinal,
                $substituicaoJulgamentoFinalTO,
                $indicacoes
            );
            $substituicaoJulgamentoFinal->setMembrosSubstituicaoJulgamentoFinal($membrosSubstituicaoJulgamentoFinal);

            if (!empty($arquivo)) {
                $this->salvarArquivo(
                    $substituicaoJulgamentoFinal->getId(),
                    $arquivo->getArquivo(),
                    $substituicaoJulgamentoFinal->getNomeArquivoFisico()
                );
            }

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new  EnviarEmailSubstituicaoJulgamentoFinalJob($substituicaoJulgamentoFinal->getId()));

        return SubstituicaoJulgamentoFinalTO::newInstanceFromEntity($substituicaoJulgamentoFinal);
    }

    public function enviarEmailAposCadastroSubstituicao($idSubstuicao)
    {
        /** @var SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal */
        $substituicaoJulgamentoFinal = $this->getSubstituicaoJulgamentoFinalRepository()->find($idSubstuicao);

        $chapaEleicao = $substituicaoJulgamentoFinal->getJulgamentoFinal()->getChapaEleicao();

        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorAtividadeSecundaria(
            $chapaEleicao->getAtividadeSecundariaCalendario()->getId(), 5, 3
        );

        $isIES = $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;

        // enviar e-mail informativo para responsável chapa uf ou IES
        $this->enviarEmailResponsavelChapa($atividade->getId(), $substituicaoJulgamentoFinal);

        // enviar e-mail informativo para membro substituido uf ou IES
        $this->enviarEmailMembroSubstituido($atividade->getId(), $substituicaoJulgamentoFinal);

        // enviar e-mail informativo para membro substituto uf ou IES
        $this->enviarEmailMembroSubstituto($atividade->getId(), $substituicaoJulgamentoFinal);

        // enviar e-mail informativo para conselheiros CEN e a comissão UF
        // Constants::EMAIL_SUBST_MEMBRO_CHAPA_PARA_CONSELHEIROS_CEN_E_CEUF
        //$this->enviarEmailConselheirosCoordenadoresComissao($atividade->getId(), $substituicaoJulgamentoFinal, $isIES);

        // enviar e-mail informativo para os acessores CEN/BR e CE
        $this->enviarEmailAcessoresCenAndAcessoresCE($atividade->getId(), $substituicaoJulgamentoFinal, $isIES);
    }

    /**
     * Responsável por enviar e-mail informativo para responsável chapa uf ou IES
     * @param int $idAtivSecundaria
     * @param SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
     * @param bool $isIES
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailResponsavelChapa(
        int $idAtivSecundaria,
        SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
    )
    {
        $chapaEleicao = $substituicaoJulgamentoFinal->getJulgamentoFinal()->getChapaEleicao();
        $destinatarios = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa($chapaEleicao->getId());

        if (!empty($destinatarios)) {

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $idAtivSecundaria, Constants::EMAIL_SUBST_JULGAMENTO_FINAL_RESPONSAVEIS_CHAPA
            );

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $substituicaoJulgamentoFinal);
        }
    }

    /**
     * Responsável por enviar e-mail informativo para membro substituido uf ou IES
     * @param int $idAtivSecundaria
     * @param SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
     * @param bool $isIES
     */
    private function enviarEmailMembroSubstituido(
        int $idAtivSecundaria,
        SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
    )
    {
        $membrosChapaSubstituicao = $substituicaoJulgamentoFinal->getMembrosSubstituicaoJulgamentoFinal();

        $destinatarios = [];
        /** @var MembroSubstituicaoJulgamentoFinal $membroSubstituicaoJulgamento */
        foreach ($membrosChapaSubstituicao as $membroSubstituicaoJulgamento) {
            $membroChapa = $this->recuperaMembroChapaIndicacao($membroSubstituicaoJulgamento);
            if (!empty($membroChapa) && !empty($membroChapa->getProfissional()->getPessoa()->getEmail())) {
                array_push(
                    $destinatarios,
                    $membroSubstituicaoJulgamento->getMembroChapa()->getProfissional()->getPessoa()->getEmail()
                );
            }
        }

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $idAtivSecundaria, Constants::EMAIL_SUBST_JULGAMENTO_FINAL_SUBSTITUIDO
            );

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $substituicaoJulgamentoFinal);
        }
    }

    /**
     * Responsável por enviar e-mail informativo para membro substituto uf ou IES
     * @param int $idAtivSecundaria
     * @param SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
     * @param bool $isIES
     */
    private function enviarEmailMembroSubstituto(
        int $idAtivSecundaria,
        SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
    )
    {
        $membrosChapaSubstituicao = $substituicaoJulgamentoFinal->getMembrosSubstituicaoJulgamentoFinal();

        $destinatarios = [];
        /** @var MembroSubstituicaoJulgamentoFinal $membroSubstituicaoJulgamento */
        foreach ($membrosChapaSubstituicao as $membroSubstituicaoJulgamento) {
            if (!empty($membroSubstituicaoJulgamento->getMembroChapa()->getProfissional()->getPessoa()->getEmail())) {
                array_push(
                    $destinatarios,
                    $membroSubstituicaoJulgamento->getMembroChapa()->getProfissional()->getPessoa()->getEmail()
                );
            }
        }

        if (!empty($destinatarios)) {

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $idAtivSecundaria, Constants::EMAIL_SUBST_JULGAMENTO_FINAL_SUBSTITUTO
            );

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $substituicaoJulgamentoFinal);
        }
    }

    /**
     * Responsável por enviar e-mail informativo para conselheiros CEN e a comissão UF
     * @param int $idAtivSecundaria
     * @param SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
     * @param bool $isIES
     */
    private function enviarEmailConselheirosCoordenadoresComissao(
        int $idAtivSecundaria,
        SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal,
        bool $isIES
    )
    {
        $idCauUf = $isIES ? null : $substituicaoJulgamentoFinal->getJulgamentoFinal()->getChapaEleicao()->getIdCauUf();

        $destinatarios = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $idAtivSecundaria, $idCauUf
        );

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $idAtivSecundaria,
                Constants::EMAIL_SUBST_JULGAMENTO_FINAL_PARA_CONSELHEIROS_CEN_E_CEUF
            );

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $substituicaoJulgamentoFinal);
        }
    }

    /**
     * Responsável por enviar e-mail informativo para os acessores CEN/BR e CE
     * @param int $idAtivSecundaria
     * @param SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
     * @param bool $isIES
     */
    private function enviarEmailAcessoresCenAndAcessoresCE(
        int $idAtivSecundaria,
        SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal,
        bool $isIES
    )
    {
        $idCauUf = $isIES ? null : $substituicaoJulgamentoFinal->getJulgamentoFinal()->getChapaEleicao()->getIdCauUf();

        $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $isIES ? null : [$idCauUf]
        );

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $idAtivSecundaria, Constants::EMAIL_SUBST_JULGAMENTO_FINAL_PARA_ASSESSOR_CEN_E_CEUF
            );

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $substituicaoJulgamentoFinal);
        }
    }

    /**
     * @param $emailAtividadeSecundaria
     * @param $destinatarios
     * @param SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
     * @throws Exception
     */
    private function enviarEmail(
        $emailAtividadeSecundaria,
        $destinatarios,
        SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
    ): void
    {
        if (!empty($emailAtividadeSecundaria)) {
            $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
            $emailTO->setDestinatarios($destinatarios);

            Email::enviarMail(new AtividadeSecundariaMail($emailTO));
        }
    }

    /**
     * Salvar os membros de Substituição do Julgamnto Final
     * @param SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
     * @param JulgamentoFinal $julgamentoFinal
     * @param SubstituicaoJulgamentoFinalTO $substituicaoJulgamentoFinalTO
     * @param $indicacoes
     * @return \App\Entities\Entity[]
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function salvarMembrosSubstituicaoJulgamentoFinal(
        $substituicaoJulgamentoFinal,
        $julgamentoFinal,
        $substituicaoJulgamentoFinalTO,
        $indicacoes
    ) {
        $membrosSubstituicaoParaSalvar = [];

        foreach ($substituicaoJulgamentoFinalTO->getMembrosSubstituicaoJulgamentoFinal() as $membroSubstituicaoJulgamentoFinalTO) {
            $indicacao = $this->GetIndicacaoDaLista($membroSubstituicaoJulgamentoFinalTO->getIdIndicacaoJulgamento(), $indicacoes);

            if (empty($indicacao)) {
                throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
            }

            $profissionalTO = $this->getProfissionalBO()->getPorId($membroSubstituicaoJulgamentoFinalTO->getIdProfissional(), true);

            $idTipoMembro = Constants::TIPO_MEMBRO_CHAPA_REPRESENTANTE_IES;
            if ($julgamentoFinal->getChapaEleicao()->getTipoCandidatura()->getId() != Constants::TIPO_CANDIDATURA_IES) {
                $idTipoMembro = $indicacao->getNumeroOrdem() == 0
                    ? Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL
                    : Constants::TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL;
            }

            $this->getMembroChapaBO()->validarImpedimentosIncluirMembro(
                $julgamentoFinal->getChapaEleicao(),
                $profissionalTO,
                $idTipoMembro,
                false
            );

            $membroChapa = MembroChapa::newInstance([
                'numeroOrdem' => $indicacao->getNumeroOrdem(),
                'situacaoResponsavel' => false,
                'profissional' => ['id' => $membroSubstituicaoJulgamentoFinalTO->getIdProfissional()],
                'chapaEleicao' => ['id' => $julgamentoFinal->getChapaEleicao()->getId()],
                'tipoMembroChapa' => ['id' => $idTipoMembro],
                'statusValidacaoMembroChapa' => ['id' => Constants::STATUS_VALIDACAO_MEMBRO_PENDENTE],
                'tipoParticipacaoChapa' => ['id' => $indicacao->getTipoParticipacaoChapa()->getId()],
                'statusParticipacaoChapa' => ['id' => Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR],
                'situacaoMembroChapa' => ['id' => Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO]
            ]);

            $this->getMembroChapaBO()->persist($membroChapa);

            $this->getMembroChapaBO()->salvarPendenciasMembro($membroChapa, $profissionalTO);

            $membroFinal = MembroSubstituicaoJulgamentoFinal::newInstance([
                'substituicaoJulgamentoFinal' => ['id' => $substituicaoJulgamentoFinal->getId()],
                'membroChapa' => ['id' => $membroChapa->getId()],
                'indicacaoJulgamentoFinal' => $indicacao instanceof IndicacaoJulgamentoFinal ? ['id' => $indicacao->getId()] : null,
                'indicacaoJulgamentoRecursoPedidoSubstituicao' => $indicacao instanceof IndicacaoJulgamentoRecursoPedidoSubstituicao ? ['id' => $indicacao->getId()] : null,
                'indicacaoJulgamentoSegundaInstanciaSubstituicao' => $indicacao instanceof IndicacaoJulgamentoSegundaInstanciaSubstituicao ? ['id' => $indicacao->getId()] : null,
                'indicacaoJulgamentoSegundaInstanciaRecurso' => $indicacao instanceof IndicacaoJulgamentoSegundaInstanciaRecurso ? ['id' => $indicacao->getId()] : null,
            ]);

            if (array_key_exists($indicacao->getId(), $membrosSubstituicaoParaSalvar)) {
                throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
            }
            $membrosSubstituicaoParaSalvar[$indicacao->getId()] = $membroFinal;
        }

        return $this->getMembroSubstituicaoJulgamentoFinalBO()->salvarMembrosSubstituicaoJulgamentoFinal(
            $membrosSubstituicaoParaSalvar
        );
    }

    /**
     * @param $idIndicacao
     * @param $indicacoes
     * @return IndicacaoJulgamentoFinal|mixed|null
     */
    public function GetIndicacaoDaLista($idIndicacao, $indicacoes)
    {
        $indicacaoRetorno = NULL;
        foreach ($indicacoes as $indicacao) {
            if ($indicacao->getId() === $idIndicacao) {
                $indicacaoRetorno = $indicacao;
                break;
            }
        }

        return $indicacaoRetorno;
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoSubstituicaoJulgamentoFinal($id)
    {
        /** @var SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal */
        $substituicaoJulgamentoFinal = $this->getSubstituicaoJulgamentoFinalRepository()->find($id);

        if (!empty($substituicaoJulgamentoFinal)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioSubstituicaoJulgamentoFinal(
                $substituicaoJulgamentoFinal->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $substituicaoJulgamentoFinal->getNomeArquivoFisico(),
                $substituicaoJulgamentoFinal->getNomeArquivo()
            );
        }
    }

    /**
     * Disponibiliza o arquivo de Recurso de substituição conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoSubstituicaoRecursoJulgamentoFinal($id)
    {
        /** @var $recursoSubstituicaoJulgamentoFinal */
        $recursoSubstituicaoJulgamentoFinal = $this->getRecursoSegundoJulgamentoSubstituicaoRepository()->find($id);
        if(!empty($recursoSubstituicaoJulgamentoFinal)) {

            $caminho = $this->getArquivoService()->getCaminhoRepositorioRecursoSubstituicao(
                $recursoSubstituicaoJulgamentoFinal->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $recursoSubstituicaoJulgamentoFinal->getNomeArquivoFisico(),
                $recursoSubstituicaoJulgamentoFinal->getNomeArquivo()
            );
        }
    }

    /**
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param SubstituicaoJulgamentoFinalTO $substituicaoJulgamentoFinalTO
     * @param ArquivoGenericoTO $arquivo
     * @throws NegocioException
     */
    private function validarSalvarJulgamentoFinal($substituicaoJulgamentoFinalTO, $arquivo)
    {
        if (empty($substituicaoJulgamentoFinalTO->getMembrosSubstituicaoJulgamentoFinal())) {
            throw new NegocioException(Lang::get('messages.substituicao_julgamento_final.error_msg.membros_obrigatorios'));
        }

        if (empty($substituicaoJulgamentoFinalTO->getJustificativa())) {
            throw new NegocioException(Lang::get('messages.substituicao_julgamento_final.error_msg.justificativa_obrigatorio'));
        }

        if (empty($substituicaoJulgamentoFinalTO->getIdJulgamentoFinal())) {
            throw new NegocioException(Lang::get('messages.substituicao_julgamento_final.error_msg.julgamento_final_nao_encontrado'));
        }

        if (!empty($arquivo)) {
            if (empty($arquivo->getArquivo())) {
                throw new NegocioException(Lang::get('messages.substituicao_julgamento_final.error_msg.arquivo_nao_encontrado'));
            }

            $this->getArquivoService()->validarArquivoGenrico(
                $arquivo, Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_10MB
            );
        }
    }

    /**
     * Método faz validação complementar do cadastrado do julgamento de substituição
     *
     * @param SubstituicaoJulgamentoFinalTO $substituicaoJulgamentoFinalTO
     * @param JulgamentoFinal $julgamentoFinal
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function validacaoComplementarSalvarJulgamento($substituicaoJulgamentoFinalTO, $julgamentoFinal)
    {
        if (empty($julgamentoFinal)) {
            throw new NegocioException(Lang::get('messages.substituicao_julgamento_final.error_msg.julgamento_final_nao_encontrado'));
        }

        if ($julgamentoFinal->getStatusJulgamentoFinal()->getId() == Constants::STATUS_JULG_FINAL_DEFERIDO) {
            throw new NegocioException(Lang::get('messages.substituicao_julgamento_final.error_msg.julgamento_final_deferido'));
        }

        if ($substituicaoJulgamentoFinalTO->getIsPrimeiraInstancia()) {
            $substituicoes = $julgamentoFinal->getSubstituicoesJulgamentoFinal();
            if ((is_array($substituicoes) && !empty($substituicoes)) || ($substituicoes instanceof Collection && $substituicoes->count() > 0)) {
                throw new NegocioException(Lang::get('messages.substituicao_julgamento_final.error_msg.substituicoes_existentes'));
            }

            $indicacoes = $julgamentoFinal->getIndicacoes();
            if ((is_array($indicacoes) && empty($indicacoes)) || ($indicacoes instanceof Collection && $indicacoes->count() == 0)) {
                throw new NegocioException(Lang::get('messages.substituicao_julgamento_final.error_msg.indicacoes_nao_encontradas'));
            }

            $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao(
                $julgamentoFinal->getChapaEleicao()->getId(), 5, 3
            );
        } else {
            $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorChapaEleicao(
                $julgamentoFinal->getChapaEleicao()->getId(), 5, 6
            );
        }

        $inicioVigente = Utils::getDataHoraZero() >= Utils::getDataHoraZero($atividadeSecundaria->getDataInicio());
        $fimVigente = Utils::getDataHoraZero() <= Utils::getDataHoraZero($atividadeSecundaria->getDataFim());
        if (!($inicioVigente && $fimVigente)) {
            throw new NegocioException(Lang::get('messages.substituicao_julgamento_final.error_msg.vigencia_fechada'));
        }

        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Lang::get('messages.substituicao_julgamento_final.error_msg.nao_eh_responsavel'));
        }

        $isResponsavel = $this->getMembroChapaBO()->isMembroResponsavelChapa(
            $julgamentoFinal->getChapaEleicao()->getId(),
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );

        if (!$isResponsavel) {
            throw new NegocioException(Lang::get('messages.substituicao_julgamento_final.error_msg.nao_eh_responsavel'));
        }
    }

    /**
     * @param SubstituicaoJulgamentoFinalTO $substituicaoJulgamentoFinalTO
     * @param JulgamentoFinal $julgamentoFinal
     * @param $indicacoes
     */
    private function validacaoExtrasSalvarJulgamentoSegundaIntancia($substituicaoJulgamentoFinalTO, $julgamentoFinal, &$indicacoes)
    {
        $ultimoJulgamento = null;
        $indicacoes = null;

        $julgamentoRecurso = $this->getJulgamentoSegundaInstanciaRecursoBO()->getUltimoPorChapa(
            $julgamentoFinal->getChapaEleicao()->getId()
        );
        if (!empty($julgamentoRecurso)) {
            $ultimoJulgamento = $julgamentoRecurso;
        }

        $ultimoJulgamentoSubst = $this->getJulgamentoSegundaInstanciaSubstituicaoBO()->getUltimoPorChapa(
            $julgamentoFinal->getChapaEleicao()->getId()
        );
        if (!empty($ultimoJulgamentoSubst)) {
            $ultimoJulgamento = (empty($ultimoJulgamento) || $ultimoJulgamentoSubst->getDataCadastro() > $ultimoJulgamento->getDataCadastro())
                ? $ultimoJulgamentoSubst
                : $ultimoJulgamento;
        }

        $ultimoJulgamentoRecursoSubst = $this->getJulgamentoRecursoPedidoSubstituicaoBO()->getUltimoPorJulgamentoFinal(
            $julgamentoFinal->getId()
        );
        if (!empty($ultimoJulgamentoRecursoSubst)) {
            $ultimoJulgamento = (empty($ultimoJulgamento) || $ultimoJulgamentoRecursoSubst->getDataCadastro() > $ultimoJulgamento->getDataCadastro())
                ? $ultimoJulgamentoRecursoSubst
                : $ultimoJulgamento;
        }

        if (empty($ultimoJulgamento)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $indicacoes = $ultimoJulgamento->getIndicacoes();
        if ($ultimoJulgamento->getStatusJulgamentoFinal()->getId() == Constants::STATUS_JULG_FINAL_DEFERIDO || empty($indicacoes)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }
    }

    /**
     * Método auxiliar que verifica se usuário tem permissão de julgar
     *
     * @param $idCauUf
     * @return bool
     */
    private function isUsuarioComPermissaoJulgar($idCauUf)
    {
        $isAssessorCE = $this->getUsuarioFactory()->isCorporativoAssessorCeUfPorCauUf($idCauUf);

        return ($this->getUsuarioFactory()->isCorporativoAssessorCEN() || $isAssessorCE);
    }

    /**
     * Método auxiliar para preparar entidade SubstituicaoJulgamentoFinal para cadastro
     *
     * @param SubstituicaoJulgamentoFinalTO $substituicaoJulgamentoFinalTO
     * @param JulgamentoFinal $julgamentoFinal
     * @param ArquivoGenericoTO $arquivo
     * @return SubstituicaoJulgamentoFinal
     * @throws Exception
     */
    private function prepararJulgamentoSalvar($substituicaoJulgamentoFinalTO, $julgamentoFinal, $arquivo)
    {

        $nomeArquivo = null;
        $nomeArquivoFisico = null;
        if (!empty($arquivo)) {
            $nomeArquivo = $arquivo->getNome();
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getNome(), Constants::PREFIXO_ARQ_SUBSTITUICOES_JULGAMENTOS_FINAIS
            );
        }

        $substituicaoJulgamentoFinal = SubstituicaoJulgamentoFinal::newInstance([
            'dataCadastro' => Utils::getData(),
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'justificativa' => $substituicaoJulgamentoFinalTO->getJustificativa(),
            'nomeArquivo' => $nomeArquivo,
            'profissional' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional]
        ]);
        $substituicaoJulgamentoFinal->setJulgamentoFinal($julgamentoFinal);

        return $substituicaoJulgamentoFinal;
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $idSubstituicaoJulgamentoFinal
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idSubstituicaoJulgamentoFinal, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioSubstituicaoJulgamentoFinal($idSubstituicaoJulgamentoFinal),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
     * @throws Exception
     */
    private function salvarHistoricoSubstituicaoJulgamentoFinal(
        SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
    ): void
    {

        $historico = $this->getHistoricoProfissionalBO()->criarHistorico(
            $substituicaoJulgamentoFinal->getId(),
            Constants::HISTORICO_PROF_TIPO_SUBSTITUICAO_JUGAMENTO_FINAL,
            Constants::HISTORICO_PROF_ACAO_INSERIR,
            Constants::HISTORICO_PROF_DESCRICAO_ACAO_INSERIR,
            Constants::HISTORICO_PROF_DS_ACAO_INSERIR_SUBSTITUICAO_JULG_FINAL
        );
        $this->getHistoricoProfissionalBO()->salvar($historico);
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
     * Retorna uma nova instância de 'SubstituicaoJulgamentoFinalRepository'.
     *
     * @return SubstituicaoJulgamentoFinalRepository
     */
    private function getSubstituicaoJulgamentoFinalRepository()
    {
        if (empty($this->substituicaoJulgamentoFinalRepository)) {
            $this->substituicaoJulgamentoFinalRepository = $this->getRepository(SubstituicaoJulgamentoFinal::class);
        }

        return $this->substituicaoJulgamentoFinalRepository;
    }

    /**
     * Retorna uma nova instância de 'RecursoSegundoJulgamentoSubstituicaoRepositor'.
     *
     * @return RecursoSegundoJulgamentoSubstituicaoRepository
     */
    private function getRecursoSegundoJulgamentoSubstituicaoRepository()
    {
        if(empty($this->recursoSegundoJulgamentoSubstituicaoRepository)) {
            $this->recursoSegundoJulgamentoSubstituicaoRepository = $this->getRepository(RecursoSegundoJulgamentoSubstituicao::class);
        }
        return $this->recursoSegundoJulgamentoSubstituicaoRepository;
    }

    /**
     * Retorna a instância de PDFFactory conforme o padrão Lazy Initialization.
     *
     * @return PDFFActory
     */
    private function getPdfFactory(): PDFFActory
    {
        if (empty($this->pdfFactory)) {
            $this->pdfFactory = app()->make(PDFFActory::class);
        }

        return $this->pdfFactory;
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
     * Retorna a intancia de 'ProfissionalBO'.
     *
     * @return ProfissionalBO
     */
    private function getProfissionalBO()
    {
        if ($this->profissionalBO == null) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }
        return $this->profissionalBO;
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
     * @return SubstituicaoJulgamentoFinalTO|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function getPorPedidoSubstituicaoComVerificacaoUsuario(
        $idPedidoSubstituicao,
        $verificarUsuarioResponsavelChapa,
        $verificarUsuarioMembroComissao
    )
    {
        $isPermitidoVisualizar = false;

        /** @var SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal */
        $substituicaoJulgamentoFinal = $this->getSubstituicaoJulgamentoFinalRepository()->findOneBy([
            'pedidoSubstituicaoChapa' => $idPedidoSubstituicao
        ]);

        if (!empty($substituicaoJulgamentoFinal)) {
            $idCalendario = $this->getSubstituicaoJulgamentoFinalRepository()->getIdCalendarioJulgamento(
                $substituicaoJulgamentoFinal->getId()
            );

            $atividadeSecundariaRecurso = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
                $idCalendario, 2, 5
            );

            if (
                !empty($atividadeSecundariaRecurso)
                && Utils::getDataHoraZero() >= Utils::getDataHoraZero($atividadeSecundariaRecurso->getDataInicio())
            ) {
                $chapaEleicao = $substituicaoJulgamentoFinal->getPedidoSubstituicaoChapa()->getChapaEleicao();
                $isIES = $chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;

                if ($verificarUsuarioMembroComissao) {
                    $isMembroComissao = $this->getMembroComissaoBO()->verificarMembroComissaoPorCauUf(
                        $idCalendario, $chapaEleicao->getIdCauUf(), $isIES
                    );

                    if ($isMembroComissao) {
                        $isPermitidoVisualizar = true;
                    }
                }

                if ($verificarUsuarioResponsavelChapa) {
                    $idChapaEleicao = $this->getChapaEleicaoBO()->getIdChapaEleicaoPorCalendarioEResponsavel(
                        $idCalendario,
                        $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
                    );

                    if (!empty($idChapaEleicao) && $idChapaEleicao == $chapaEleicao->getId()) {
                        $isPermitidoVisualizar = true;
                    }
                }
            }
        }

        return $isPermitidoVisualizar ? SubstituicaoJulgamentoFinalTO::newInstanceFromEntity($substituicaoJulgamentoFinal) : null;
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
     * Retorna uma nova instância de 'JulgamentoFinalBO'.
     *
     * @return JulgamentoFinalBO
     */
    private function getJulgamentoFinalBO()
    {
        if (empty($this->julgamentoFinalBO)) {
            $this->julgamentoFinalBO = app()->make(JulgamentoFinalBO::class);
        }

        return $this->julgamentoFinalBO;
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
     * Retorna uma nova instância de 'MembroSubstituicaoJulgamentoFinalBO'.
     *
     * @return MembroSubstituicaoJulgamentoFinalBO
     */
    private function getMembroSubstituicaoJulgamentoFinalBO()
    {
        if (empty($this->membroSubstituicaoJulgamentoFinalBO)) {
            $this->membroSubstituicaoJulgamentoFinalBO = app()->make(MembroSubstituicaoJulgamentoFinalBO::class);
        }

        return $this->membroSubstituicaoJulgamentoFinalBO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoSegundaInstanciaSubstituicaoBO'.
     *
     * @return JulgamentoSegundaInstanciaSubstituicaoBO
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
     * @return JulgamentoSegundaInstanciaRecursoBO
     */
    private function getJulgamentoSegundaInstanciaRecursoBO()
    {
        if (empty($this->julgamentoSegundaInstanciaRecursoBO)) {
            $this->julgamentoSegundaInstanciaRecursoBO = app()->make(JulgamentoSegundaInstanciaRecursoBO::class);
        }

        return $this->julgamentoSegundaInstanciaRecursoBO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoPedidoSubstituicaoBO'.
     *
     * @return JulgamentoRecursoPedidoSubstituicaoBO
     */
    private function getJulgamentoRecursoPedidoSubstituicaoBO()
    {
        if (empty($this->julgamentoRecursoPedidoSubstituicaoBO)) {
            $this->julgamentoRecursoPedidoSubstituicaoBO = app()->make(JulgamentoRecursoPedidoSubstituicaoBO::class);
        }

        return $this->julgamentoRecursoPedidoSubstituicaoBO;
    }

    /**
     * Recupera a indicaçao de membro chapa a partir de um julgamento
     *
     * @param MembroSubstituicaoJulgamentoFinal $membroSubstituicaoJulgamento
     * @return MembroChapa|null
     */
    public function recuperaMembroChapaIndicacao(MembroSubstituicaoJulgamentoFinal $membroSubstituicaoJulgamento):
    ?MembroChapa
    {
        if (!empty($membroSubstituicaoJulgamento->getIndicacaoJulgamentoFinal())) {
            return $membroSubstituicaoJulgamento->getIndicacaoJulgamentoFinal()->getMembroChapa();
        } else if (!empty($membroSubstituicaoJulgamento->getIndicacaoJulgamentoRecursoPedidoSubstituicao())) {
            return $membroSubstituicaoJulgamento->getIndicacaoJulgamentoRecursoPedidoSubstituicao()->getMembroChapa();
        } else if (!empty($membroSubstituicaoJulgamento->getIndicacaoJulgamentoSegundaInstanciaRecurso())) {
            return $membroSubstituicaoJulgamento->getIndicacaoJulgamentoSegundaInstanciaRecurso()->getMembroChapa();
        } else if (!empty($membroSubstituicaoJulgamento->getIndicacaoJulgamentoSegundaInstanciaSubstituicao())) {
            return $membroSubstituicaoJulgamento->getIndicacaoJulgamentoSegundaInstanciaSubstituicao()->getMembroChapa();
        }
    }

    /**
     * Retorna uma nova instância de 'RecursoSegundoJulgamentoSubstituicaoBO'.
     *
     * @return RecursoSegundoJulgamentoSubstituicaoBO |mixed
     */
    private function getRecursoSegundoJulgamentoSubstituicaoBO()
    {
        if (empty($this->recursoSegundoJulgamentoSubstituicaoBO)) {
            $this->recursoSegundoJulgamentoSubstituicaoBO = app()->make(RecursoSegundoJulgamentoSubstituicaoBO::class);
        }

        return $this->recursoSegundoJulgamentoSubstituicaoBO;
    }
}




