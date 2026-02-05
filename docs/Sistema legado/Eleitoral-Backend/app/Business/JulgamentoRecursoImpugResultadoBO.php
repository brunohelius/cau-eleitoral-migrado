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
use App\Entities\ChapaEleicao;
use App\Entities\ContrarrazaoRecursoImpugnacaoResultado;
use App\Entities\ImpugnacaoResultado;
use App\Entities\JulgamentoRecursoImpugResultado;
use App\Entities\MembroChapa;
use App\Entities\RecursoImpugnacaoResultado;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Mail\ContrarrazaoImpugnacaoResultadoMail;
use App\Mail\JulgamentoSegundaInstanciaImpugnacaoResultadoMail;
use App\Repository\ContrarrazaoRecursoImpugnacaoResultadoRepository;
use App\Repository\JulgamentoRecursoImpugResultadoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\ContrarrazaoRecursoImpugnacaoResultadoTO;
use App\To\EleicaoTO;
use App\To\ImpugnacaoResultadoTO;
use App\To\JulgamentoFinalTO;
use App\To\JulgamentoRecursoImpugResultadoTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'JulgamentoRecursoImpugResultado'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoImpugResultadoBO extends AbstractBO
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
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var JulgamentoRecursoImpugResultadoRepository
     */
    private $julgamentoRecursoImpugResultadoRepository;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var ImpugnacaoResultadoBO
     */
    private $impugnacaoResultadoBO;

    /**
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Buscar julgamento/Homologação por id Pedido de impugnação de Resultado.
     *
     * @param $idImpugResultado
     */
    public function getPorImpugnacaoResultado($idImpugResultado) {
        $julgamentos =  $this->getJulgamentoRecursoImpugResultadoRepository()->findBy([
            'impugnacaoResultado' => $idImpugResultado
        ]);
        return !empty($julgamentos) ? JulgamentoRecursoImpugResultadoTO::newInstanceFromEntity(array_shift($julgamentos)) : null;
    }

    /**
     * Salva o julgamento 2ª instância do pedido de impugnação de resultado
     *
     * @param JulgamentoRecursoImpugResultadoTO $julgamentoRecursoImpugResultadoTO
     * @return JulgamentoRecursoImpugResultadoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function salvar(JulgamentoRecursoImpugResultadoTO $julgamentoRecursoImpugResultadoTO)
    {
        $this->validacaoIncialSalvarJulgamento($julgamentoRecursoImpugResultadoTO);

        $impugnacaoResultado = $this->getImpugnacaoResultadoBO()->getPorId(
            $julgamentoRecursoImpugResultadoTO->getIdImpugnacaoResultado()
        );

        $atividade = null;
        $this->validacaoComplementarSalvarJulgamentoFinal($julgamentoRecursoImpugResultadoTO, $impugnacaoResultado, $atividade);

        try {
            $this->beginTransaction();

            $julgamentoRecursoImpugResultado = $this->prepararJulgamentoSalvar(
                $julgamentoRecursoImpugResultadoTO, $impugnacaoResultado
            );

            $this->getJulgamentoRecursoImpugResultadoRepository()->persist($julgamentoRecursoImpugResultado);

            $this->salvarHistoricoJulgamentoFinal($julgamentoRecursoImpugResultado);

            $this->getImpugnacaoResultadoBO()->salvarStatusImpugnacaoResultado(
                $impugnacaoResultado, Constants::STATUS_IMPUG_RESULTADO_TRANSITADO_JULGADO
            );

            $this->salvarArquivo(
                $julgamentoRecursoImpugResultado->getId(),
                $julgamentoRecursoImpugResultadoTO->getArquivo()->getArquivo(),
                $julgamentoRecursoImpugResultado->getNomeArquivoFisico()
            );

            $this->enviarEmailCadastroJulgamentoRecurso($julgamentoRecursoImpugResultado, $impugnacaoResultado ,$atividade);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return JulgamentoRecursoImpugResultadoTO::newInstanceFromEntity($julgamentoRecursoImpugResultado);
    }

    /**
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param JulgamentoRecursoImpugResultadoTO $julgamentoRecursoImpugResultadoTO
     * @throws NegocioException
     */
    private function validacaoIncialSalvarJulgamento($julgamentoRecursoImpugResultadoTO)
    {
        if (empty($julgamentoRecursoImpugResultadoTO->getIdImpugnacaoResultado())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($julgamentoRecursoImpugResultadoTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $idStatus = $julgamentoRecursoImpugResultadoTO->getIdStatusJulgamentoRecursoImpugResultado();
        if (empty($idStatus) || !in_array($idStatus, Constants::$statusJulgRecursoImpugResultado)) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $arquivo = $julgamentoRecursoImpugResultadoTO->getArquivo();
        if (empty($arquivo)) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($arquivo->getArquivo()) && empty($arquivo->getNomeFisico())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if(!empty(empty($arquivo->getNomeFisico()))) {
            $this->getArquivoService()->validarArquivoGenrico(
                $arquivo, Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_15MB
            );
        }
    }

    /**
     * Método faz validação complementar do cadastrado do julgamento do recurso
     *
     * @param JulgamentoRecursoImpugResultadoTO $julgamentoRecursoImpugResultadoTO
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @param $atividade
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function validacaoComplementarSalvarJulgamentoFinal(
        $julgamentoRecursoImpugResultadoTO,
        $impugnacaoResultado,
        &$atividade
    ) {
        if (empty($impugnacaoResultado)) {
            throw new NegocioException(Lang::get('messages.erro_inesperado'));
        }

        $this->verificaPermissaoRealizarJulgamento();

        $this->validarPeriodoVigenteCadastro($impugnacaoResultado->getCalendario()->getId(), $atividade);

        if (!empty($impugnacaoResultado->getJulgamentoRecurso())) {
            throw new NegocioException(Lang::get('messages.julg_recurso_impug_resultado.ja_realizado'));
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
            $idCalendario, 6, 6
        );

        if ( empty($atividade) || !(Utils::getDataHoraZero($atividade->getDataInicio()) <= Utils::getDataHoraZero())) {
            throw new NegocioException(Lang::get('messages.julg_recurso_impug_resultado.periodo_fora_vigencia'));
        }
    }

    /**
     * Método auxiliar para verificar a permissão do usuário autenticado de realizar julgamento
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @throws NegocioException
     */
    private function verificaPermissaoRealizarJulgamento(): void
    {
        if (!$this->getUsuarioFactory()->isCorporativoAssessorCEN()) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }
    }

    /**
     * Método auxiliar para preparar entidade JulgamentoRecursoImpugResultado para cadastro
     *
     * @param JulgamentoRecursoImpugResultadoTO $julgamentoRecursoImpugResultadoTO
     * @param ImpugnacaoResultado|null $impugnacaoResultado
     * @return JulgamentoRecursoImpugResultado
     * @throws Exception
     */
    private function prepararJulgamentoSalvar($julgamentoRecursoImpugResultadoTO, $impugnacaoResultado)
    {
        $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
            $julgamentoRecursoImpugResultadoTO->getArquivo()->getNome(),
            Constants::PREFIXO_ARQ_JULG_RECURSO_IMPUG_RESULT
        );

        return JulgamentoRecursoImpugResultado::newInstance([
            'dataCadastro' => Utils::getData(),
            'nomeArquivo' => $julgamentoRecursoImpugResultadoTO->getArquivo()->getNome(),
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'statusJulgamentoRecursoImpugResultado' => ['id' => $julgamentoRecursoImpugResultadoTO->getIdStatusJulgamentoRecursoImpugResultado()],
            'descricao' => $julgamentoRecursoImpugResultadoTO->getDescricao(),
            'usuario' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->id],
            'impugnacaoResultado' => ['id' => $impugnacaoResultado->getId()],
        ]);
    }

    /**
     * Método auxiliar para salvar o histórico  de inclusão do pedido
     *
     * @param JulgamentoRecursoImpugResultado $julgamentoRecursoImpugResultado
     * @throws Exception
     */
    private function salvarHistoricoJulgamentoFinal(JulgamentoRecursoImpugResultado $julgamentoRecursoImpugResultado): void
    {
        $historico = $this->getHistoricoBO()->criarHistorico(
            $julgamentoRecursoImpugResultado,
            Constants::HISTORICO_ID_TIPO_JULG_RECURSOS_IMPUGNACAO_RESULTADO,
            Constants::HISTORICO_ACAO_INSERIR,
            Constants::HISTORICO_INCLUSAO_JULG_RECURSO_IMPUG_RESULTADO
        );
        $this->getHistoricoBO()->salvar($historico);
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $idJulgamento
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idJulgamento, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioJulgRecursoImpugResultado($idJulgamento),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Disponibiliza o arquivo(JulgamentoRecursoImpugResultado) conforme o 'id' informado.
     *
     * @param $idArquivo
     * @return ArquivoTO
     */
    public function getArquivoJulgamentoRecursoPorId($idArquivo) {
        /** @var JulgamentoRecursoImpugResultado $julgamento */
        $julgamento = $this->getJulgamentoRecursoImpugResultadoRepository()->find($idArquivo);

        if (!empty($julgamento)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgRecursoImpugResultado($julgamento->getId());

            return $this->getArquivoService()->getArquivo(
                $caminho, $julgamento->getNomeArquivoFisico(), $julgamento->getNomeArquivo()
            );
        }
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
     * Retorna uma nova instância de 'JulgamentoRecursoImpugResultadoRepository'.
     *
     * @return JulgamentoRecursoImpugResultadoRepository
     */
    private function getJulgamentoRecursoImpugResultadoRepository()
    {
        if (empty($this->julgamentoRecursoImpugResultadoRepository)) {
            $this->julgamentoRecursoImpugResultadoRepository = $this->getRepository(
                JulgamentoRecursoImpugResultado::class
            );
        }

        return $this->julgamentoRecursoImpugResultadoRepository;
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
     * Retorna uma nova instância de 'ImpugnacaoResultadoBO'.
     *
     * @return ImpugnacaoResultadoBO|mixed
     */
    private function getImpugnacaoResultadoBO()
    {
        if (empty($this->impugnacaoResultadoBO)) {
            $this->impugnacaoResultadoBO = app()->make(ImpugnacaoResultadoBO::class);
        }

        return $this->impugnacaoResultadoBO;
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
     * Método responsável por enviar o email ao realizar o cadastro do julgamento
     * de segunda instância do pedido de impugnação de resultado.
     * @throws NonUniqueResultException
     */
    private function enviarEmailCadastroJulgamentoRecurso(
        JulgamentoRecursoImpugResultado $julgamentoRecursoImpugResultado,
        ImpugnacaoResultado $impugnacaoResultado ,
        AtividadeSecundariaCalendario $atividade
    ) {
        $tipos = Constants::$tiposEmailAtividadeSecundaria[6][6];

        foreach($tipos as $tipo) {

            $destinatarios = $this->getDestinatariosEmailJulgamentoRecurso(
                $julgamentoRecursoImpugResultado,
                $impugnacaoResultado,
                $atividade,
                $tipo
            );

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()
                ->getEmailPorAtividadeSecundariaAndTipo($atividade->getId(), $tipo);

            $this->enviarEmail(
                $julgamentoRecursoImpugResultado,
                $impugnacaoResultado,
                $emailAtividadeSecundaria,
                $destinatarios
            );
        }
    }

    /**
     * Retorna os destinatarios envolvidos no julgamento de segunda instância
     */
    private function getDestinatariosEmailJulgamentoRecurso(
        JulgamentoRecursoImpugResultado $julgamentoRecursoImpugResultado,
        ImpugnacaoResultado $impugnacaoResultado ,
        AtividadeSecundariaCalendario $atividade,
        $tipo
    ) {
        $destinatarios = [];
        $idCauUf = empty($impugnacaoResultado->getCauBR()) ? null : $impugnacaoResultado->getCauBR()->getId();

        /** Envia e-mail ao usuário  impugnante que cadastrou o recurso */
        if (
            $tipo == Constants::EMAIL_JULG_RECURSO_IMPUG_RESULT_IMPUGNANTE &&
            !empty($impugnacaoResultado->getProfissional()->getPessoa())
        ) {
            array_push($destinatarios, $impugnacaoResultado->getProfissional()->getPessoa()->getEmail());
        }

        /** envia email aos impugnados que cadastraram o recurso/reconsideração */
        if($tipo == Constants::EMAIL_JULG_RECURSO_IMPUG_RESULT_CHAPA) {
            $isIES = empty($impugnacaoResultado->getCauBR());
            $membrosChapas = $this->getMembroChapaBO()->getMembrosResponsaveisPorCalendarioAndTipoCandidaturaAndCauUF(
                $impugnacaoResultado->getCalendario()->getId(),
                $isIES ? Constants::TIPO_CANDIDATURA_IES : Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR,
                $isIES ? null : $impugnacaoResultado->getCauBR()->getId()
            );

            if (!empty($membrosChapas)) {
                /** @var MembroChapa $membroChapa */
                foreach ($membrosChapas as $membroChapa) {
                    if (!empty($membroChapa->getProfissional()->getPessoa())) {
                        array_push($destinatarios, $membroChapa->getProfissional()->getPessoa()->getEmail());
                    }
                }
            }
        }

        /** Envia e-mail à todos os Coordenadores  */
        /*if ($tipo == Constants::EMAIL_JULG_RECURSO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN) {
            $destinatarios = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
                $atividade->getId(), $idCauUf
            );
        }*/

        /** Envia e-mail aos assessores */
        if ($tipo == Constants::EMAIL_JULG_RECURSO_IMPUGNACAO_RESULTADO_ASSESSORES) {
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
                empty($impugnacaoResultado->getCauBR()) ? null : [$idCauUf]
            );
        }
        return $destinatarios;
    }

    /**
     * Retorna uma nova instância de 'MembroChapaBO'.
     *
     * @return MembroChapaBO
     */
    private function getMembroChapaBO()
    {
        if (empty($this->membroChapaBO)) {
            $this->membroChapaBO = app()->make(MembroChapaBO::class);
        }
        return $this->membroChapaBO;
    }

    /**
     * @param JulgamentoRecursoImpugResultado $julgamentoRecursoImpugResultado
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @param $emailAtividadeSecundaria
     * @param array $destinatarios
     */
    private function enviarEmail(JulgamentoRecursoImpugResultado $julgamentoRecursoImpugResultado, ImpugnacaoResultado $impugnacaoResultado, $emailAtividadeSecundaria, array $destinatarios): void
    {
        if (!empty($emailAtividadeSecundaria) && !empty($destinatarios)) {

            $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
            $emailTO->setDestinatarios($destinatarios);

            $eleicaoTO = EleicaoTO::newInstance([
                'ano' => $impugnacaoResultado->getCalendario()->getEleicao()->getAno(),
                'sequenciaAno' => $impugnacaoResultado->getCalendario()->getEleicao()->getSequenciaAno()
            ]);

            $isIES = empty($impugnacaoResultado->getCauBR());

            Email::enviarMail(new JulgamentoSegundaInstanciaImpugnacaoResultadoMail(
                $isIES,
                $emailTO,
                $julgamentoRecursoImpugResultado,
                $impugnacaoResultado->getNumero(),
                $eleicaoTO->getSequenciaFormatada(),
                $isIES ? Constants::PREFIXO_IES : $impugnacaoResultado->getCauBR()->getPrefixo()
            ));
        }
    }
}




