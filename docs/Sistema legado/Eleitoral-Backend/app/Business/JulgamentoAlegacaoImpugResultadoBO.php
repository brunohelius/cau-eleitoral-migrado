<?php
/*
 * UfCalendarioBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\AppConfig;
use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ChapaEleicao;
use App\Entities\HistoricoJulgamentoAlegacaoImpugResultado;
use App\Entities\ImpugnacaoResultado;
use App\Entities\AlegacaoImpugnacaoResultado;
use App\Entities\JulgamentoAlegacaoImpugResultado;
use App\Entities\JulgamentoFinal;
use App\Entities\MembroChapa;
use App\Entities\StatusImpugnacaoResultado;
use App\Entities\StatusJulgamentoAlegacaoResultado;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\UsuarioFactory;
use App\Jobs\EnviarEmailCadastrarJulgamentoAlegacaoImpugResultadoJob;
use App\Mail\EncaminhamentoAlegacoesFinaisMail;
use App\Mail\ImpugnacaoResultadoMail;
use App\Mail\JulgamentoAlegacaoImpugnacaoResultadoMail;
use App\Repository\HistoricoJulgamentoAlegacaoImpugResultadoRepository;
use App\Repository\ImpugnacaoResultadoRepository;
use App\Repository\JulgamentoAlegacaoImpugResultadoRepository;
use App\Repository\StatusJulgamentoAlegacaoResultadoRepository;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\EleicaoTO;
use App\To\ImpugnacaoResultadoTO;
use App\To\JulgamentoAlegacaoImpugResultadoTO;
use App\Util\Email;
use App\Util\Utils;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use App\Service\ArquivoService;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'JulgamentoAlegacaoImpugResultado'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoAlegacaoImpugResultadoBO extends AbstractBO
{

    /**
     * @var JulgamentoAlegacaoImpugResultadoRepository
     */
    private $julgamentoAlegacaoImpugResultadoRepository;

    /**
     * @var ImpugnacaoResultadoRepository
     */
    private $impugnacaoResultadoRepository;

    /**
     * @var ImpugnacaoResultadoBO
     */
    private $impugnacaoResultadoBO;

    /**
     * @var StatusJulgamentoAlegacaoResultadoRepository
     */
    private $statusJulgamentoAlegacaoResultadoRepository;

    /**
     * @var HistoricoJulgamentoAlegacaoImpugResultadoRepository
     */
    private $historicoJulgamentoAlegacaoImpugResultadoRepository;

    /**
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var RecursoImpugnacaoResultadoBO
     */
    private $recursoImpugnacaoResultadoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salvar Julgamento de Alegação de Impugnação de Resultado.
     *
     * @param $julgamentoAlegacaoImpugResultadoTO
     */
    public function salvar(JulgamentoAlegacaoImpugResultadoTO $julgamentoAlegacaoImpugResultadoTO)
    {
        /** Validar permissão usuário. */
        $impugnacaoResultado = $this->getImpugnacaoResultadoBO()->find(
            $julgamentoAlegacaoImpugResultadoTO->getIdImpugnacaoResultado()
        );

        $idUf = (!empty($impugnacaoResultado) && !empty($impugnacaoResultado->getCauBR()))
            ? $impugnacaoResultado->getCauBR()->getId()
            : null;

        $isAssessorCEN = $this->getUsuarioFactory()->isCorporativoAssessorCEN();
        $isAssessorCE = $this->getUsuarioFactory()->isCorporativoAssessorCEUF() &&
            $idUf == $this->getUsuarioFactory()->getUsuarioLogado()->idCauUf;
        if(!$this->getUsuarioFactory()->isCorporativo() || !($isAssessorCEN || $isAssessorCE )) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }

        /** Validar Campos obrigatórios para salvar julgamento de alegação de impugnação. */
        $this->validarCamposObrigatoriosSalvar($julgamentoAlegacaoImpugResultadoTO);

        if (!empty($impugnacaoResultado->getJulgamentoAlegacao())) {
            throw new NegocioException(Lang::get('messages.julgamento_alegaca_impug_resultado.ja_realizado'));
        }

        try {
            $this->beginTransaction();

            $julgamento = $this->prepararJulgamentoSalvar($julgamentoAlegacaoImpugResultadoTO);
            $this->getJulgamentoAlegacaoImpugResultadoRepository()->persist($julgamento);

            /** Salvar Histórico de Julgamento de Alegação de Impugnação. */
            $this->salvarHistoricoJulgamentoAlegacaoImpugResultado($julgamento);

            /** Atualizar Status de Pedido de Impugnação de Resultado. */
            $status = StatusImpugnacaoResultado::newInstance(['id' => Constants::STATUS_IMPUG_RESULTADO_JUGADO_1_INSTANCIA]);
            $impugnacaoResultado->setStatus($status);
            $this->getImpugnacaoResultadoRepository()->persist($impugnacaoResultado);

            /** Gravar Arquivo. */
            /** @var ArquivoGenericoTO|null $arquivo */
            $arquivo = !empty($julgamentoAlegacaoImpugResultadoTO->getArquivos()) && is_array($julgamentoAlegacaoImpugResultadoTO->getArquivos()) ? $julgamentoAlegacaoImpugResultadoTO->getArquivos()[0] : null;
            if(!empty($arquivo)) {
                $this->salvarArquivo(
                    $julgamento->getId(), $arquivo->getArquivo(), $julgamento->getNomeArquivoFisico()
                );
            }

            $this->enviarEmailCadastrarJulgamentoAlegacaoImpugResultado($impugnacaoResultado);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return JulgamentoAlegacaoImpugResultadoTO::newInstanceFromEntity($julgamento);
    }

    /**
     * Retorna o Julgamento da Alegação Primeira Instância.
     *
     * @param $idImpugnacao
     * @return int|mixed|string|null
     * @throws \Exception
     */
    public function getJulgamentoAlegacaoPorImpugnacaoResultado($idImpugnacao)
    {
        $julgamentoTO = $this->getJulgamentoAlegacaoImpugResultadoRepository()->getJulgamentoAlegacaoPorImpugnacaoResultado(
            $idImpugnacao
        );

        if(!empty($julgamentoTO) && $this->getUsuarioFactory()->isProfissional()) {

            /** @var UsuarioFactory $usuario*/
            $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

            /** @var ChapaEleicao $chapa */
            $chapa = $this->getChapaEleicaoBO()->getChapaEleicaoPorCalendarioEResponsavel(
                $julgamentoTO->getImpugnacaoResultado()->getCalendario()->getId(),
                $usuarioLogado->idProfissional
            );

            if (!empty($chapa)) {
                $recursos = $this->getRecursoImpugnacaoResultadoBO()->getRecursoPorImpugnacaoEChapa(
                    $idImpugnacao,
                    $chapa->getId(),
                    Constants::TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNADO
                );
                $julgamentoTO->setHasRecursoPorResponsavelEChapa(!empty($recursos));
            }
        }

        return $julgamentoTO;
    }

    /**
     * Preparar Entidade de Julgamento de Alegação através de TO.
     *
     * @param JulgamentoAlegacaoImpugResultadoTO $julgamentoAlegacaoImpugResultadoTO
     * @return JulgamentoAlegacaoImpugResultado
     * @throws \Exception
     */
    private function prepararJulgamentoSalvar(JulgamentoAlegacaoImpugResultadoTO $julgamentoAlegacaoImpugResultadoTO): JulgamentoAlegacaoImpugResultado
    {
        /** @var ArquivoGenericoTO $arquivo */
        $arquivo = !empty($julgamentoAlegacaoImpugResultadoTO->getArquivos()) ? $julgamentoAlegacaoImpugResultadoTO->getArquivos()[0] : null;
        $nomeArquivoFisico = empty($arquivo) ? '' : $this->getArquivoService()->getNomeArquivoFormatado(
            $arquivo->getNome(), Constants::PREFIXO_ARQ_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO
        );

        return JulgamentoAlegacaoImpugResultado::newInstance([
            'id' => $julgamentoAlegacaoImpugResultadoTO->getId(),
            'descricao' => $julgamentoAlegacaoImpugResultadoTO->getDescricao(),
            'dataCadastro' => Utils::getData(),
            'nomeArquivo' => !empty($arquivo) ? $arquivo->getNome() : '',
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'impugnacaoResultado' => [ 'id' => $julgamentoAlegacaoImpugResultadoTO->getIdImpugnacaoResultado() ],
            'statusJulgamentoAlegacaoResultado' => [ 'id' => $julgamentoAlegacaoImpugResultadoTO->getIdStatusJulgamentoAlegacaoResultado() ],
            'usuario' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->id],
        ]);
    }

    /**
     * Validar de Campos obrigatórios para salvar julgamento de alegações.
     *
     * @param JulgamentoAlegacaoImpugResultadoTO $julgamentoAlegacaoImpugResultadoTO
     * @throws NegocioException
     */
    private function validarCamposObrigatoriosSalvar(JulgamentoAlegacaoImpugResultadoTO $julgamentoAlegacaoImpugResultadoTO)
    {
        if(empty($julgamentoAlegacaoImpugResultadoTO->getIdStatusJulgamentoAlegacaoResultado())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if(empty($julgamentoAlegacaoImpugResultadoTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        $arquivo = $julgamentoAlegacaoImpugResultadoTO->getArquivos()[0];
        if(empty($arquivo)) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        } else {
            $this->getArquivoService()->validarArquivoGenrico(
                $arquivo, Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_15MB
            );
        }
    }

    /**
     * Método auxiliar para salvar o histórico  de salvar julgamento de alegação.
     *
     * @param JulgamentoFinal $julgamentoFinal
     * @throws Exception
     */
    private function salvarHistoricoJulgamentoAlegacaoImpugResultado(JulgamentoAlegacaoImpugResultado $julgamento): void
    {
        $historico = $this->getHistoricoBO()->criarHistorico(
            $julgamento,
            Constants::HISTORICO_ID_TIPO_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO,
            Constants::HISTORICO_ACAO_INSERIR,
            Constants::HISTORICO_INCLUSAO_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO
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
            $this->getArquivoService()->getCaminhoRepositorioJulgamentoAlegacaoImpugResultado($idJulgamento),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Enviar e-mail de cadastro de julgamento de julgamento de alegação de I.R.
     *
     * @param $idJulgamentoAlegacaoImpugResultado
     */
    public function enviarEmailCadastrarJulgamentoAlegacaoImpugResultado($impugnacaoResultado): void
    {
        $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getPorCalendario(
            $impugnacaoResultado->getCalendario()->getId(),
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO,
            Constants::NIVEL_ATIVIDADE_INCLUSAO_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO
        );

        $tipos = [
            Constants::EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNADO,
            Constants::EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNANTE,
            Constants::EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN,
            Constants::EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_ASSESSORES,
        ] ;

        foreach ($tipos as $tipo) {
            $destinatarios = $this->getDestinatariosEmail($impugnacaoResultado, $tipo, $atividadeSecundaria);

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividadeSecundaria->getId(), $tipo
            );

            if (!empty($emailAtividadeSecundaria) && !empty($destinatarios)) {
                $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $impugnacaoResultado);
            }
        }
    }

    /**
     * Retornar lista de array de destinatários dos emails.
     *
     * @param JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado
     * @param $tipo
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @return array
     */
    private function getDestinatariosEmail($impugnacaoResultado , $tipo, AtividadeSecundariaCalendario $atividadeSecundaria): array
    {
        /**
         * @var array $destinatarios
         * @var ImpugnacaoResultado $impugnacaoResultado
         * @var int $idCauUf
         */
        $destinatarios = [];
        $idCauUf = empty($impugnacaoResultado->getCauBR()) ? null : $impugnacaoResultado->getCauBR()->getId();

        /** Registro 01 – Envia e-mail aos responsáveis Chapa/IES.  */
        if($tipo == Constants::EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNADO) {
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

        /** Envia e-mail ao usuário que cadastrou o pedido de impugnação, impugnante */
        if($tipo == Constants::EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNANTE) {
            if (!empty($impugnacaoResultado->getProfissional()->getPessoa())) {
                array_push($destinatarios, $impugnacaoResultado->getProfissional()->getPessoa()->getEmail());
            }
        }

        /** Envia e-mail à todos os Coordenadores  */
        /*if($tipo == Constants::EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN) {
            $destinatarios = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
                $atividadeSecundaria->getId(), $idCauUf
            );
        }*/

        /** Envia e-mail aos assessores */
        if($tipo == Constants::EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_ASSESSORES)
        {
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
                empty($impugnacaoResultado->getCauBR()) ? null : [$idCauUf]
            );
        }

        return $destinatarios;
    }

    /**
     * Enviar os E-mails.
     *
     * @param $emailAtividadeSecundaria
     * @param $destinatarios
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @throws \Exception
     */
    private function enviarEmail($emailAtividadeSecundaria, $destinatarios, $impugnacaoResultado)
    {
        $impugnacaoResultadoTO = ImpugnacaoResultadoTO::newInstanceFromEntity($impugnacaoResultado, true);

        if (!empty($emailAtividadeSecundaria)) {
            $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
            $emailTO->setDestinatarios($destinatarios);

            $eleicaoTO = EleicaoTO::newInstance([
                'ano' => $impugnacaoResultado->getCalendario()->getEleicao()->getAno(),
                'sequenciaAno' => $impugnacaoResultado->getCalendario()->getEleicao()->getSequenciaAno()
            ]);

            Email::enviarMail(new JulgamentoAlegacaoImpugnacaoResultadoMail($emailTO, $impugnacaoResultadoTO, $eleicaoTO->getSequenciaFormatada()));
        }
    }

    /**
     * Retorna a instância do 'JulgamentoAlegacaoImpugResultadoRepository'.
     *
     * @return JulgamentoAlegacaoImpugResultadoRepository
     */
    private function getJulgamentoAlegacaoImpugResultadoRepository()
    {
        if ($this->julgamentoAlegacaoImpugResultadoRepository == null) {
            $this->julgamentoAlegacaoImpugResultadoRepository = $this->getRepository(JulgamentoAlegacaoImpugResultado::class);
        }
        return $this->julgamentoAlegacaoImpugResultadoRepository;
    }

    /**
     * Retorna a instância do 'StatusJulgamentoAlegacaoResultadoRepository'.
     *
     * @return JulgamentoAlegacaoImpugResultadoRepository
     */
    private function getStatusJulgamentoAlegacaoResultadoRepository()
    {
        if ($this->statusJulgamentoAlegacaoResultadoRepository == null) {
            $this->statusJulgamentoAlegacaoResultadoRepository = $this->getRepository(StatusJulgamentoAlegacaoResultado::class);
        }
        return $this->statusJulgamentoAlegacaoResultadoRepository;
    }

    /**
     * Retorna a instância do 'HistoricoJulgamentoAlegacaoImpugResultadoRepository'.
     *
     * @return HistoricoJulgamentoAlegacaoImpugResultadoRepository
     */
    private function getHistoricoJulgamentoAlegacaoImpugResultadoRepository()
    {
        if ($this->historicoJulgamentoAlegacaoImpugResultadoRepository == null) {
            $this->historicoJulgamentoAlegacaoImpugResultadoRepository = $this->getRepository(HistoricoJulgamentoAlegacaoImpugResultadoRepository::class);
        }
        return $this->historicoJulgamentoAlegacaoImpugResultadoRepository;
    }

    /**
     * Retorna a instância do 'ImpugnacaoResultadoRepository'.
     *
     * @return ImpugnacaoResultadoRepository
     */
    private function getImpugnacaoResultadoRepository(): ImpugnacaoResultadoRepository
    {
        if(empty($this->impugnacaoResultadoRepository)) {
            $this->impugnacaoResultadoRepository = $this->getRepository(ImpugnacaoResultado::class);
        }
        return $this->impugnacaoResultadoRepository;
    }

    /**
     * Retorna a instância do 'ImpugnacaoResultadoBO'.
     *
     * @return ImpugnacaoResultadoBO
     */
    private function getImpugnacaoResultadoBO(): ImpugnacaoResultadoBO
    {
        if(empty($this->impugnacaoResultadoBO)) {
            $this->impugnacaoResultadoBO = app()->make(ImpugnacaoResultadoBO::class);
        }
        return $this->impugnacaoResultadoBO;
    }

    /**
     * Retorna a instância do 'AtividadeSecundariaCalendarioBO'.
     *
     * @return AtividadeSecundariaCalendarioBO
     */
    private function getAtividadeSecundariaBO(): AtividadeSecundariaCalendarioBO
    {
        if(empty($this->atividadeSecundariaBO)) {
            $this->atividadeSecundariaBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }
        return $this->atividadeSecundariaBO;
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
     * Método para download do arquivo do Julgamento da Alegação de Impugnação de Resultado
     */
    public function getArquivoJulgamento($idJulgamento)
    {

        /** @var AlegacaoImpugnacaoResultado $impugnacao */
        $julgamento = $this->getJulgamentoAlegacaoImpugResultadoRepository()->find($idJulgamento);
        if (!empty($julgamento->getNomeArquivo())) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoAlegacaoImpugResultado(
                $julgamento->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $julgamento->getNomeArquivoFisico(),
                $julgamento->getNomeArquivo()
            );
        }

    }

    /**
     * Retorna um julgamento de alegaçao por Id
     *
     * @param $idJulgamentoAlegacaoImpugResultado
     * @return JulgamentoAlegacaoImpugResultado|mixed|null
     */
    public function findPorId($idJulgamentoAlegacaoImpugResultado)
    {
        return $this->getJulgamentoAlegacaoImpugResultadoRepository()->find($idJulgamentoAlegacaoImpugResultado);
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
     * Retorna uma nova instância de 'RecursoImpugnacaoResultadoBO'.
     * @return RecursoImpugnacaoResultadoBO|mixed
     */
    private function getRecursoImpugnacaoResultadoBO()
    {
        if (empty($this->recursoImpugnacaoResultadoBO)) {
            $this->recursoImpugnacaoResultadoBO = app()->make(RecursoImpugnacaoResultadoBO::class);
        }

        return $this->recursoImpugnacaoResultadoBO;
    }
}
