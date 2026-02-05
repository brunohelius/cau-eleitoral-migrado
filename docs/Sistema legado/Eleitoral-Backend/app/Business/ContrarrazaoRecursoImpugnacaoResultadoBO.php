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
use App\Entities\ContrarrazaoRecursoImpugnacaoResultado;
use App\Entities\ImpugnacaoResultado;
use App\Entities\RecursoImpugnacaoResultado;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Mail\ContrarrazaoImpugnacaoResultadoMail;
use App\Repository\ContrarrazaoRecursoImpugnacaoResultadoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\ContrarrazaoRecursoImpugnacaoResultadoTO;
use App\To\EleicaoTO;
use App\To\ImpugnacaoResultadoTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'ContrarrazaoRecursoImpugnacaoResultado'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class ContrarrazaoRecursoImpugnacaoResultadoBO extends AbstractBO
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
     * @var RecursoImpugnacaoResultadoBO
     */
    private $recursoImpugnacaoResultadoBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var HistoricoProfissionalBO
     */
    private $historicoProfissionalBO;

    /**
     * @var ContrarrazaoRecursoImpugnacaoResultadoRepository
     */
    private $contrarrazaoRecursoImpugnacaoResultadoRepository;

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
     * Salva o julgamento final da chapa da eleição
     *
     * @param ContrarrazaoRecursoImpugnacaoResultadoTO $contrarrazaoRecursoImpugnacaoResultadoTO
     * @return ContrarrazaoRecursoImpugnacaoResultadoTO
     * @throws NegocioException
     */
    public function salvar(ContrarrazaoRecursoImpugnacaoResultadoTO $contrarrazaoRecursoImpugnacaoResultadoTO)
    {
        $arquivos = $contrarrazaoRecursoImpugnacaoResultadoTO->getArquivos();

        /** @var ArquivoGenericoTO|null $arquivo */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validacaoIncialSalvarContrarrazao($contrarrazaoRecursoImpugnacaoResultadoTO, $arquivo);

        /** @var RecursoImpugnacaoResultado $recursoImpugnacaoResultado */
        $recursoImpugnacaoResultado = $this->getRecursoImpugnacaoResultadoBO()->findPorId(
            $contrarrazaoRecursoImpugnacaoResultadoTO->getIdRecursoImpugnacaoResultado()
        );

        $atividade = null; // Busca/carrega a atividade quando for realizar a verificação/validação

        $this->validacaoComplementarSalvarContrarrazao(
            $contrarrazaoRecursoImpugnacaoResultadoTO, $recursoImpugnacaoResultado, $atividade
        );

        try {
            $this->beginTransaction();

            $contrarrazaoRecursoImpugResult = $this->prepararContrarrazaoSalvar(
                $contrarrazaoRecursoImpugnacaoResultadoTO, $recursoImpugnacaoResultado, $arquivo
            );

            $this->getContrarrazaoRecursoImpugnacaoResultadoRepository()->persist($contrarrazaoRecursoImpugResult);

            $this->salvarHistoricoContrarrazao($contrarrazaoRecursoImpugResult);

            if (!empty($arquivo)) {
                $this->salvarArquivo(
                    $contrarrazaoRecursoImpugResult->getId(),
                    $arquivo->getArquivo(),
                    $contrarrazaoRecursoImpugResult->getNomeArquivoFisico()
                );
            }

            $this->enviarEmailCadastroContrarrazao($contrarrazaoRecursoImpugResult, $atividade);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return ContrarrazaoRecursoImpugnacaoResultadoTO::newInstanceFromEntity($contrarrazaoRecursoImpugResult);
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoContrarrazao($id)
    {
        /** @var ContrarrazaoRecursoImpugnacaoResultado $contrarrazao */
        $contrarrazao = $this->getContrarrazaoRecursoImpugnacaoResultadoRepository()->find($id);

        if (!empty($contrarrazao) && !empty($contrarrazao->getNomeArquivo())) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioContrarrazaoImpugnacaoResultado(
                $contrarrazao->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho, $contrarrazao->getNomeArquivoFisico(), $contrarrazao->getNomeArquivo()
            );
        }
    }

    /**
     * Envia e-mail as interessados alertando que o cadastro de contrarrazão foi realizado
     * @param ContrarrazaoRecursoImpugnacaoResultado $contrarrazaoRecursoImpugResult
     */
    private function enviarEmailCadastroContrarrazao($contrarrazaoRecursoImpugResult, $atividade)
    {
        $tipos = Constants::$tiposEmailAtividadeSecundaria[6][5];

        foreach ($tipos as $tipo) {
            $recurso = $contrarrazaoRecursoImpugResult->getRecursoImpugnacaoResultado();
            $impugnacaoResultado = $recurso->getJulgamentoAlegacaoImpugResultado()->getImpugnacaoResultado();

            $destinatarios = $this->getDestinatariosEmail(
                $contrarrazaoRecursoImpugResult, $recurso, $impugnacaoResultado, $tipo, $atividade
            );

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(), $tipo
            );

            if (!empty($emailAtividadeSecundaria) && !empty($destinatarios)) {
                $this->enviarEmail(
                    $emailAtividadeSecundaria,
                    $destinatarios,
                    ContrarrazaoRecursoImpugnacaoResultadoTO::newInstanceFromEntity($contrarrazaoRecursoImpugResult),
                    $impugnacaoResultado
                );
            }
        }
    }

    /**
     * Enviar os E-mails.
     *
     * @param $emailAtivSecundaria
     * @param $destinatarios
     * @param ContrarrazaoRecursoImpugnacaoResultadoTO $contrarrazaoTO
     * @throws Exception
     */
    private function enviarEmail($emailAtivSecundaria, $destinatarios, $contrarrazaoTO, $impugnacaoResultado)
    {
        if (!empty($emailAtivSecundaria)) {
            $impugnacaoResultadoTO = ImpugnacaoResultadoTO::newInstanceFromEntity($impugnacaoResultado);

            $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtivSecundaria);
            $emailTO->setDestinatarios($destinatarios);

            $eleicaoTO = EleicaoTO::newInstance([
                'ano' => $impugnacaoResultado->getCalendario()->getEleicao()->getAno(),
                'sequenciaAno' => $impugnacaoResultado->getCalendario()->getEleicao()->getSequenciaAno()
            ]);

            $isIES = empty($impugnacaoResultado->getCauBR());

            Email::enviarMail(new ContrarrazaoImpugnacaoResultadoMail(
                $emailTO,
                $impugnacaoResultadoTO->getNumero(),
                $contrarrazaoTO,
                $eleicaoTO->getSequenciaFormatada(),
                $isIES ? Constants::PREFIXO_IES : $impugnacaoResultado->getCauBR()->getPrefixo()
            ));
        }
    }

    /**
     * Retornar lista de array de destinatários dos emails.
     *
     * @param ContrarrazaoRecursoImpugnacaoResultado $contrarrazaoRecursoImpugResult
     * @return array
     * @throws NegocioException
     */
    private function getDestinatariosEmail(
        $contrarrazaoRecursoImpugResult,
        $recurso,
        $impugnacaoResultado,
        $tipo,
        $atividade
    ): array {

        $destinatarios = [];
        $idCauUf = empty($impugnacaoResultado->getCauBR()) ? null : $impugnacaoResultado->getCauBR()->getId();

        /** Envia e-mail ao usuário que cadastrou a contrarrazão */
        if (
            $tipo == Constants::EMAIL_CONTRARRAZAO_RECURSO_IMPUG_RESULT_CADASTROU_CONTRARRAZAO &&
            !empty($contrarrazaoRecursoImpugResult->getProfissional()->getPessoa())
        ) {
            array_push($destinatarios, $contrarrazaoRecursoImpugResult->getProfissional()->getPessoa()->getEmail());
        }

        /** Envia e-mail ao usuário que cadastrou o recurso */
        if (
            $tipo == Constants::EMAIL_CONTRARRAZAO_RECURSO_IMPUG_RESULT_CADASTROU_RECURSO &&
            !empty($recurso->getProfissional()->getPessoa())
        ) {
            array_push($destinatarios, $recurso->getProfissional()->getPessoa()->getEmail());
        }

        /** Envia e-mail à todos os Coordenadores  */
        /*if ($tipo == Constants::EMAIL_CONTRARRAZAO_RECURSO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN) {
            $destinatarios = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
                $atividade->getId(), $idCauUf
            );
        }*/

        /** Envia e-mail aos assessores */
        if ($tipo == Constants::EMAIL_CONTRARRAZAO_RECURSO_IMPUGNACAO_RESULTADO_ASSESSORES) {
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
                empty($impugnacaoResultado->getCauBR()) ? null : [$idCauUf]
            );
        }

        return $destinatarios;
    }

    /**
     * Método faz validação se pode ser cadastrado a contrarrazão, verifica os campos obrigatórios
     *
     * @param ContrarrazaoRecursoImpugnacaoResultadoTO $contrarrazaoRecursoImpugnacaoResultadoTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     */
    private function validacaoIncialSalvarContrarrazao($contrarrazaoRecursoImpugnacaoResultadoTO, $arquivo)
    {
        if (empty($contrarrazaoRecursoImpugnacaoResultadoTO->getIdRecursoImpugnacaoResultado())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($contrarrazaoRecursoImpugnacaoResultadoTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (!empty($arquivo)) {
            if (empty($arquivo->getArquivo()) && empty($arquivo->getNomeFisico())) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
            }

            if (!empty(empty($arquivo->getNomeFisico()))) {
                $this->getArquivoService()->validarArquivoGenrico(
                    $arquivo, Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_15MB
                );
            }
        }
    }

    /**
     * Realiza as validações de negócio
     * @param ContrarrazaoRecursoImpugnacaoResultadoTO $contrarrazaoTO
     * @param RecursoImpugnacaoResultado $recursoImpugnacaoResultado
     */
    private function validacaoComplementarSalvarContrarrazao($contrarrazaoTO, $recursoImpugnacaoResultado, &$atividade)
    {
        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Lang::get('messages.erro_inesperado'));
        }

        if (empty($recursoImpugnacaoResultado)) {
            throw new NegocioException(Lang::get('messages.erro_inesperado'));
        }

        $impugnacao = $recursoImpugnacaoResultado->getJulgamentoAlegacaoImpugResultado()->getImpugnacaoResultado();
        $idTipoRecurso = $recursoImpugnacaoResultado->getTipoRecursoImpugnacaoResultado()->getId();

        $this->validacaoPorTipoRecursoImpugnado(
            $impugnacao, $idTipoRecurso, $contrarrazaoTO->getIdRecursoImpugnacaoResultado()
        );

        $this->validarPorTipoRecursoImpugnante($impugnacao, $idTipoRecurso);

        $this->validarPeriodoVigenteCadastro($impugnacao->getCalendario()->getId(), $atividade);
    }

    /**
     * Método auxiliar para validar tipo recurso impugnado
     * @param $idCalendario
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function validacaoPorTipoRecursoImpugnado($impugnacao, $idTipoRecurso, $idRecurso): void
    {
        if ($idTipoRecurso == Constants::TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNADO) {
            if ($impugnacao->getProfissional()->getId() != $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional) { // Validação para tipo recurso impugnado
                throw new NegocioException(Lang::get('messages.erro_inesperado'));
            }

            $this->validarProfissionalLogadoImpugnanteCadastrouContrarrazao($idRecurso);
        }
    }

    /**
     * Método auxiliar para validar tipo recurso impugnado
     * @param ImpugnacaoResultado $impugnacao
     * @param $idTipoRecurso
     * @throws NegocioException
     */
    private function validarPorTipoRecursoImpugnante($impugnacao, $idTipoRecurso): void
    {
        if ($idTipoRecurso == Constants::TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNANTE) { // Validação para tipo recurso impugnante
            $chapa = $this->getChapaEleicaoBO()->getChapaEleicaoPorCalendarioEResponsavel(
                $impugnacao->getCalendario()->getId(),
                $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
            );

            if (empty($chapa)) {
                throw new NegocioException(Lang::get('messages.erro_inesperado'));
            }

            $idCauUfChapa = $chapa->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR
                ? $chapa->getFilial()->getId()
                : null;

            $idCauUfImpugnacao = !empty($impugnacao->getCauBR()) ? $impugnacao->getCauBR()->getId() : null;

            if ($idCauUfChapa != $idCauUfImpugnacao) {
                throw new NegocioException(Lang::get('messages.erro_inesperado'));
            }

            $contrarrazoes = $this->getContrarrazaoRecursoImpugnacaoResultadoRepository()->getContrarrazoesPorImpugnacaoEChapa(
                $impugnacao->getId(), $chapa->getId(), Constants::TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNANTE);

            if (!empty($contrarrazoes)) {
                throw new NegocioException(Lang::get('messages.contrarrazao_impugnacao_resultado.ja_realizado_cadastro_impugnado'));
            }
        }
    }

    /**
     * Método auxiliar para validar período vigente de cadastro de contrarrazão
     * @param $idCalendario
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function validarPeriodoVigenteCadastro($idCalendario, &$atividade): void
    {
        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $idCalendario, 6, 5
        );

        if (
            empty($atividade) ||
            !(Utils::getDataHoraZero($atividade->getDataInicio()) <= Utils::getDataHoraZero()
                && Utils::getDataHoraZero($atividade->getDataFim()) >= Utils::getDataHoraZero())
        ) {
            throw new NegocioException(Lang::get('messages.contrarrazao_impugnacao_resultado.periodo_vigente_cadastro'));
        }
    }

    /**
     * Método auxiliar para validar se profissional já cadastrou contrarrazão para o recurso
     * @param $idRecurso
     * @throws NegocioException
     */
    private function validarProfissionalLogadoImpugnanteCadastrouContrarrazao($idRecurso): void
    {
        $totalPorRecursoAndProfissional = $this->getContrarrazaoRecursoImpugnacaoResultadoRepository()->getTotalPorRecursoAndProfissional(
            $idRecurso,
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );
        if (!empty($totalPorRecursoAndProfissional) && $totalPorRecursoAndProfissional > 0) {
            throw new NegocioException(Lang::get('messages.contrarrazao_impugnacao_resultado.ja_realizado_cadastro_impugnante'));
        }
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $idContrarrazao
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idContrarrazao, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioContrarrazaoImpugnacaoResultado($idContrarrazao),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param ContrarrazaoRecursoImpugnacaoResultado $contrarrazaoRecursoImpugResult
     * @throws Exception
     */
    private function salvarHistoricoContrarrazao($contrarrazaoRecursoImpugResult): void
    {

        $historico = $this->getHistoricoProfissionalBO()->criarHistorico(
            $contrarrazaoRecursoImpugResult->getId(),
            Constants::HISTORICO_PROF_TIPO_CONTRARRAZAO_IMPUGNACAO_RESULTADO,
            Constants::HISTORICO_PROF_ACAO_INSERIR,
            Constants::HISTORICO_PROF_DESCRICAO_ACAO_INSERIR,
            Constants::HISTORICO_PROF_DS_ACAO_INSERIR_CONTRARRAZAO_IMPUG_RESULT
        );
        $this->getHistoricoProfissionalBO()->salvar($historico);
    }

    /**
     * Método auxiliar para preparar entidade JulgamentoRecursoImpugnacao para cadastro
     *
     * @param ContrarrazaoRecursoImpugnacaoResultadoTO $contrarrazaoRecursoImpugnacaoResultadoTO
     * @param RecursoImpugnacaoResultado|null $recursoImpugnacaoResultado
     * @param ArquivoGenericoTO|null $arquivo
     * @return ContrarrazaoRecursoImpugnacaoResultado
     * @throws Exception
     */
    private function prepararContrarrazaoSalvar(
        $contrarrazaoRecursoImpugnacaoResultadoTO,
        $recursoImpugnacaoResultado,
        $arquivo
    ) {
        $nomeArquivo = null;
        $nomeArquivoFisico = null;
        if (!empty($arquivo)) {
            $nomeArquivo = $arquivo->getNome();
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getNome(), Constants::PREFIXO_ARQ_CONTRARRAZAO_IMPUGNACAO_RESULTADO
            );
        }

        $contrarrazaoRecursoImpugnacaoResultado = ContrarrazaoRecursoImpugnacaoResultado::newInstance([
            'dataCadastro' => Utils::getData(),
            'nomeArquivo' => $nomeArquivo,
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'descricao' => $contrarrazaoRecursoImpugnacaoResultadoTO->getDescricao(),
            'recursoImpugnacaoResultado' => ['id' => $recursoImpugnacaoResultado->getId()],
            'numero' => $this->getNumeroSequencial($contrarrazaoRecursoImpugnacaoResultadoTO),
            'profissional' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional],
        ]);

        return $contrarrazaoRecursoImpugnacaoResultado;
    }

    /**
     * Retorna o número de sequencia para a Alegação do pedido de impugnação
     * @param ContrarrazaoRecursoImpugnacaoResultadoTO $contrarrazaoRecursoImpugnacaoResultadoTO
     * @return int|mixed
     */
    private function getNumeroSequencial($contrarrazaoRecursoImpugnacaoResultadoTO)
    {
        $total = $this->getContrarrazaoRecursoImpugnacaoResultadoRepository()->getTotalPorRecursoAndProfissional(
            $contrarrazaoRecursoImpugnacaoResultadoTO->getIdRecursoImpugnacaoResultado()
        );

        $numero = !empty($total) ? $total + 1 : 1;
        return $numero;
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
     * Retorna uma nova instância de 'ContrarrazaoRecursoImpugnacaoResultadoRepository'.
     *
     * @return ContrarrazaoRecursoImpugnacaoResultadoRepository
     */
    private function getContrarrazaoRecursoImpugnacaoResultadoRepository()
    {
        if (empty($this->contrarrazaoRecursoImpugnacaoResultadoRepository)) {
            $this->contrarrazaoRecursoImpugnacaoResultadoRepository = $this->getRepository(
                ContrarrazaoRecursoImpugnacaoResultado::class
            );
        }

        return $this->contrarrazaoRecursoImpugnacaoResultadoRepository;
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
     * Retorna uma nova instância de 'RecursoImpugnacaoResultadoBO'.
     *
     * @return RecursoImpugnacaoResultadoBO
     */
    private function getRecursoImpugnacaoResultadoBO()
    {
        if (empty($this->recursoImpugnacaoResultadoBO)) {
            $this->recursoImpugnacaoResultadoBO = app()->make(RecursoImpugnacaoResultadoBO::class);
        }

        return $this->recursoImpugnacaoResultadoBO;
    }

    /**
     * Retorna uma nova instância de 'EleicaoBO'.
     *
     * @return ChapaEleicaoBO|mixed
     */
    private function getChapaEleicaoBO()
    {
        if (empty($this->chapaEleicaoBO)) {
            $this->chapaEleicaoBO = app()->make(ChapaEleicaoBO::class);
        }

        return $this->chapaEleicaoBO;
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




