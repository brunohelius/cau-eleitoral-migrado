<?php


namespace App\Business;

use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ChapaEleicao;
use App\Entities\ImpugnacaoResultado;
use App\Entities\JulgamentoAlegacaoImpugResultado;
use App\Entities\RecursoImpugnacaoResultado;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\UsuarioFactory;
use App\Mail\RecursoImpugnacaoResultadoMail;
use App\Repository\RecursoImpugnacaoResultadoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\EleicaoTO;
use App\To\ImpugnacaoResultadoTO;
use App\To\JulgamentoAlegacaoImpugResultadoTO;
use App\To\RecursoImpugnacaoResultadoTO;
use App\Util\Email;
use App\Util\Utils;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'RecursoImpugnacaoResultado'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class RecursoImpugnacaoResultadoBO extends AbstractBO
{

    /**
     * @var RecursoImpugnacaoResultadoRepository
     */
    private $recursoImpugnacaoResultadoRepository;

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var JulgamentoAlegacaoImpugResultadoBO
     */
    private $julgamentoAlegacaoImpugResultadoBO;

    /**
     * @var HistoricoProfissionalBO
     */
    private $historicoProfissionalBO;

    /**
     * @var ImpugnacaoResultadoBO
     */
    private $impugnacaoResultadoBO;

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
     * Retorna o recurso de acordo com id informado
     * @param $idRecursoImpugnacaoResultado
     * @return RecursoImpugnacaoResultado|mixed|null
     */
    public function findPorId($idRecursoImpugnacaoResultado)
    {
        return $this->getRecursoImpugnacaoResultadoRepository()->find($idRecursoImpugnacaoResultado);
    }

    /**
     * @param $idImpugnacao
     * @param $idTipoRecurso
     * @return mixed
     */
    public function getTotalPorPedidoImpugnacaoAndTipoRecurso($idImpugnacao, $idTipoRecurso)
    {
        return $this->getRecursoImpugnacaoResultadoRepository()->getTotalPorPedidoImpugnacaoAndTipoRecurso($idImpugnacao, $idTipoRecurso);
    }

    /**
     * Retorna uma nova instância de 'RecursoImpugnacaoResultadoRepository'.
     *
     * @return RecursoImpugnacaoResultadoRepository
     */
    private function getRecursoImpugnacaoResultadoRepository()
    {
        if (empty($this->recursoImpugnacaoResultadoRepository)) {
            $this->recursoImpugnacaoResultadoRepository = $this->getRepository(
                RecursoImpugnacaoResultado::class
            );
        }

        return $this->recursoImpugnacaoResultadoRepository;
    }

    /**
     * Salva o recurso de julgamento de impugnacao de resultado
     *
     * @param $recursoImpugnacaoResultadoTO RecursoImpugnacaoResultadoTO
     * @return RecursoImpugnacaoResultadoTO
     * @throws NegocioException
     * @throws \Exception
     */
    public function salvar(RecursoImpugnacaoResultadoTO $recursoImpugnacaoResultadoTO)
    {

        $arquivo = $this->getArquivoRecursoImpugnacaoResultado($recursoImpugnacaoResultadoTO);

        //Obtem o julgamento de alegaçao atraves do Id
        /** @var JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado */
        $julgamentoAlegacaoImpugResultado = $this->getJulgamentoAlegacaoImpugResultadoBO()->findPorId(
            $recursoImpugnacaoResultadoTO->getJulgamentoAlegacaoImpugResultado()->getId()
        );

        $recursoImpugnacaoResultadoTO->setJulgamentoAlegacaoImpugResultado(
            JulgamentoAlegacaoImpugResultadoTO::newInstanceFromEntity($julgamentoAlegacaoImpugResultado)
        );

        $atividade = null; // Busca/carrega a atividade quando for realizar a verificação/validação

        $this->validarPeriodoVigencia($julgamentoAlegacaoImpugResultado, $atividade);
        $this->validacaoCamposObrigatorios($recursoImpugnacaoResultadoTO, $arquivo);
        $this->validacaoPorTipoRecursoImpugnacao($recursoImpugnacaoResultadoTO, $julgamentoAlegacaoImpugResultado);

        try {
            $this->beginTransaction();

            $recursoImpugnacaoResultado = $this->prepararSalvar(
                $recursoImpugnacaoResultadoTO, $julgamentoAlegacaoImpugResultado, $arquivo
            );

            $this->getRecursoImpugnacaoResultadoRepository()->persist($recursoImpugnacaoResultado);

            $this->salvarHistorico($recursoImpugnacaoResultado);

            if(!empty($arquivo)) {
                $this->salvarArquivo(
                    $recursoImpugnacaoResultado,
                    $arquivo->getArquivo()
                );
            }

            $this->enviarEmailCadastroRecurso($julgamentoAlegacaoImpugResultado, $recursoImpugnacaoResultado ,$atividade);

            $this->commitTransaction();

        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

    }

    /**
     * Realiza validaçao de campos obrigatorios
     *
     * @param RecursoImpugnacaoResultadoTO $recursoImpugnacaoResultadoTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     */
    private function validacaoCamposObrigatorios($recursoImpugnacaoResultadoTO, $arquivo)
    {
        if (empty($recursoImpugnacaoResultadoTO->getJulgamentoAlegacaoImpugResultado()->getId())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($recursoImpugnacaoResultadoTO->getIdTipoRecursoImpugnacaoResultado())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (empty($recursoImpugnacaoResultadoTO->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (!empty($arquivo)) {
            $this->getArquivoService()->validarArquivoGenrico(
                $arquivo, Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_15MB
            );
        }
    }

    /**
     * Método auxiliar para preparar entidade JulgamentoRecursoImpugnacao para cadastro
     *
     * @param RecursoImpugnacaoResultadoTO $recursoImpugnacaoResultadoTO
     * @param JulgamentoAlegacaoImpugResultado|null $julgamentoAlegacaoImpugResultado
     * @param ArquivoGenericoTO|null $arquivo
     * @return RecursoImpugnacaoResultado
     * @throws \Exception
     */
    private function prepararSalvar($recursoImpugnacaoResultadoTO, $julgamentoAlegacaoImpugResultado, $arquivo)
    {
        $nomeArquivo = null;
        $nomeArquivoFisico = null;

        if (!empty($arquivo)) {
            $nomeArquivo = $arquivo->getNome();
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getNome(), Constants::PREFIXO_ARQ_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO
            );
        }

        $numero = $this->retornarProximoNumeroPorJulgamentoAndTipoRecurso (
            $recursoImpugnacaoResultadoTO->getJulgamentoAlegacaoImpugResultado()->getId(),
            $recursoImpugnacaoResultadoTO->getIdTipoRecursoImpugnacaoResultado()
        );

        return RecursoImpugnacaoResultado::newInstance([
            'dataCadastro' => Utils::getData(),
            'nomeArquivo' => $nomeArquivo,
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'descricao' => $recursoImpugnacaoResultadoTO->getDescricao(),
            'tipoRecursoImpugnacaoResultado' => ['id' =>
                $recursoImpugnacaoResultadoTO->getIdTipoRecursoImpugnacaoResultado()],
            'julgamentoAlegacaoImpugResultado' => ['id' => $julgamentoAlegacaoImpugResultado->getId()],
            'numero' => $numero,
            'profissional' => ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional],
        ]);
    }

    /**
     * Retorna o número de sequencia para a Alegação do pedido de impugnação
     * @param int $idjulgamentoAlegacaoImpugResultado
     * @return int|mixed
     */
    public function retornarProximoNumeroPorJulgamentoAndTipoRecurso(int $idjulgamentoAlegacaoImpugResultado, $idTipoRecursoImpugnacaoResultado)
    {
        $ultimoNumero = $this->getRecursoImpugnacaoResultadoRepository()
            ->getUltimoNumeroPorJulgamentoAlegacaoImpugnacaoResultadoAndTipoRecurso($idjulgamentoAlegacaoImpugResultado, $idTipoRecursoImpugnacaoResultado);

        return !empty($ultimoNumero) ? $ultimoNumero + 1 : 1;
    }

    /**
     * Salva o historico para o Recurso
     *
     * @param RecursoImpugnacaoResultado $recursoImpugnacaoResultado
     * @throws \Exception
     */
    private function salvarHistorico($recursoImpugnacaoResultado): void
    {

        $historico = $this->getHistoricoProfissionalBO()->criarHistorico(
            $recursoImpugnacaoResultado->getId(),
            Constants::HISTORICO_PROF_TIPO_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO,
            Constants::HISTORICO_PROF_ACAO_INSERIR,
            Constants::HISTORICO_PROF_DESCRICAO_ACAO_INSERIR,
            Constants::HISTORICO_PROF_DS_ACAO_INSERIR_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO
        );
        $this->getHistoricoProfissionalBO()->salvar($historico);
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $recursoImpugnacaoResultado RecursoImpugnacaoResultado
     * @param $arquivo
     */
    private function salvarArquivo($recursoImpugnacaoResultado, $arquivo)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorio
            (Constants::PATH_STORAGE_ARQ_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO, $recursoImpugnacaoResultado->getId()),
            $recursoImpugnacaoResultado->getNomeArquivoFisico(),
            $arquivo
        );
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
     * Retorna uma nova instância de 'RecursoImpugnacaoResultadoBO'.
     *
     * @return JulgamentoAlegacaoImpugResultadoBO
     */
    private function getJulgamentoAlegacaoImpugResultadoBO()
    {
        if (empty($this->julgamentoAlegacaoImpugResultadoBO)) {
            $this->julgamentoAlegacaoImpugResultadoBO = app()->make(JulgamentoAlegacaoImpugResultadoBO::class);
        }

        return $this->julgamentoAlegacaoImpugResultadoBO;
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
     * Retorna o arquivo do recurso de impugnação de resultado
     * @param RecursoImpugnacaoResultadoTO $recursoImpugnacaoResultadoTO
     * @return mixed|null
     */
    private function getArquivoRecursoImpugnacaoResultado(RecursoImpugnacaoResultadoTO $recursoImpugnacaoResultadoTO)
    {
        $arquivos = $recursoImpugnacaoResultadoTO->getArquivo();

        /** @var ArquivoGenericoTO $arquivos */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        return $arquivo;
    }

    /**
     * Realiza a validação do tipo de recurso de impugnação
     * @param RecursoImpugnacaoResultadoTO $recursoImpugnacaoResultadoTO
     * @throws NegocioException
     */
    private function validacaoPorTipoRecursoImpugnacao(
        RecursoImpugnacaoResultadoTO $recursoImpugnacaoResultadoTO,
        JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado
    ) {

        /** @var UsuarioFactory $usuario*/
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $tipoRecurso = $recursoImpugnacaoResultadoTO->getIdTipoRecursoImpugnacaoResultado();

        if($tipoRecurso == Constants::TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNANTE) {
            $this->validaPermissaoImpugnante($julgamentoAlegacaoImpugResultado, $usuarioLogado);
        }
        else if ($tipoRecurso == Constants::TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNADO) {
            $this->validaPermissaoImpugnado($julgamentoAlegacaoImpugResultado, $usuarioLogado);
        }
        else {

            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }
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
     * Retorna uma instancia de AlegacaoImpugnacaoBO
     * @return ImpugnacaoResultadoBO|mixed
     */
    private function getImpugnacaoResultadoBO()
    {
        if(empty($this->impugnacaoResultadoBO)) {
            $this->impugnacaoResultadoBO = app()->make(ImpugnacaoResultadoBO::class);
        }

        return $this->impugnacaoResultadoBO;
    }

    /**
     * Método responsável por validar a permissão do usuário impugnante
     * @param JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado
     * @param \stdClass|null $usuarioLogado
     * @throws NegocioException
     */
    private function validaPermissaoImpugnante(JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado, ?\stdClass $usuarioLogado): void
    {
        $idUsuarioLogado = $usuarioLogado->idProfissional;
        $impugnante = $julgamentoAlegacaoImpugResultado->getImpugnacaoResultado()->getProfissional()->getId();

        if ($impugnante != $idUsuarioLogado) {
            throw new NegocioException(
                Lang::get('messages.recurso_impugnacao_resultado.permissao_impugnante')
            );
        }
    }

    /**
     * Responsável por validar a permissão caso tipo de recurso seja impugnado
     * @param JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado
     * @param \stdClass|null $usuarioLogado
     * @throws NegocioException
     * @throws \Exception
     */
    private function validaPermissaoImpugnado(
        JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado,
        ?\stdClass $usuarioLogado): void
    {
        $idProfissional = $usuarioLogado->idProfissional;
        $idImpugnacaoResultado = $julgamentoAlegacaoImpugResultado->getImpugnacaoResultado()->getId();

        /** @var ImpugnacaoResultado $impugnacaoResultado */
        $impugnacaoResultado = $this->getImpugnacaoResultadoBO()->find($idImpugnacaoResultado);
        $idFilialImpugnacao = $impugnacaoResultado->getCauBR();

        if (!empty($idFilialImpugnacao)) {
            $idFilialImpugnacao = $impugnacaoResultado->getCauBR()->getId();
        }

        /** @var ChapaEleicao $chapa */
        $chapa = $this->getChapaEleicaoBO()
            ->getChapaEleicaoPorCalendarioEResponsavel($impugnacaoResultado->getCalendario()->getId(), $idProfissional);


        // verifica se o usuário logado é responsável de chapa
        if (empty($chapa)) {
            throw new NegocioException(
                Lang::get('messages.recurso_impugnacao_resultado.permissao_impugnado')
            );

        }

        // verifica se a chapa do usuário logado possui algum recurso cadastrado
        $this->validarPossuiRecursoCadastrado(
            $julgamentoAlegacaoImpugResultado,
            $chapa->getId(),
            Constants::TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNADO
        );

        $idFilialChapa = $chapa->getFilial()->getId();
        $isChapaIES = $chapa->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES;

        // verifica se o Pedido de impugnação e o responsável são de IES
        if ($isChapaIES && !is_null($idFilialImpugnacao)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        // verifica se a uf da chapa do responsável é equivalente a uf do pedido de Impugnação
        if (!$isChapaIES && $idFilialChapa != $idFilialImpugnacao) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }
    }

    /**
     * Valida se o período da atividade 6.2 está vigente para cadastro de Alegação
     * @param JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado
     * @throws NegocioException
     */
    private function validarPeriodoVigencia(JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado, &$atividade)
    {
        $isVigente = false;
        $idImpugnacaoResultado = $julgamentoAlegacaoImpugResultado->getImpugnacaoResultado()->getId();

        /** @var ImpugnacaoResultado $impugnacaoResultado */
        $impugnacaoResultado = $this->getImpugnacaoResultadoBO()->find($idImpugnacaoResultado);

        if($impugnacaoResultado->getCalendario()->getId()) {
            $atividadeSecundariaBO = $this->getAtividadeSecundariaCalendarioBO();

            $isVigente = $atividadeSecundariaBO->isAtividadeVigente(
                $impugnacaoResultado->getCalendario()->getId() , 6, 4
            );

            $atividade = $atividadeSecundariaBO->getPorCalendario(
                $impugnacaoResultado->getCalendario()->getId(),
                6,
                4
            );

        } else {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if (!$isVigente) {
            throw new NegocioException(Lang::get('messages.recurso_impugnacao_resultado.periodo_fora_vigencia'));
        }
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
     * Retorna o Recurso do Julgamento a partir do id da Impugnacao Resultado e Tipo de Recurso.
     * @param $idImpugnacao
     * @param $idTipoRecurso
     * @return int|mixed|string|null
     */
    public function getRecursoJulgamentoPorIdImpugnacao($idImpugnacao, $idTipoRecurso)
    {
        $recursosJulgamento = $this->getRecursoImpugnacaoResultadoRepository()->getPorPedidoImpugnacao($idImpugnacao, $idTipoRecurso);
        if (!empty($recursosJulgamento)) {

            $impugnacaoResultado = $this->getImpugnacaoResultadoBO()->find($idImpugnacao);

            foreach ($recursosJulgamento as $recursoJulgamento) {

                if ($idTipoRecurso == Constants::TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNANTE) {
                    $this->atribuirInformacoesRecursoImpugnante($recursoJulgamento, $impugnacaoResultado);
                } else {
                    $this->atribuirInformacaoRecursoImpugnado($recursoJulgamento, $impugnacaoResultado);
                }
            }
        }
        return $recursosJulgamento;
    }

    /**
     * Método auxiliar para setar as informações necessárias para o tipo recurso impugnante
     * @param RecursoImpugnacaoResultadoTO $recursoJulgamento
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @throws \Exception
     */
    private function atribuirInformacoesRecursoImpugnante(
        RecursoImpugnacaoResultadoTO $recursoJulgamento,
        ImpugnacaoResultado $impugnacaoResultado
    ) {
        if (!empty($recursoJulgamento->getContrarrazoesRecursoImpugnacaoResultado())) {

            $idsChapasComContrarrazao = [];

            foreach ($recursoJulgamento->getContrarrazoesRecursoImpugnacaoResultado() as $contrarrazaoRecursoImpugnacaoResultadoTO) {

                $chapaEleicao = $this->getChapaEleicaoBO()->getChapaEleicaoPorCalendarioEResponsavel(
                    $impugnacaoResultado->getCalendario()->getId(),
                    $contrarrazaoRecursoImpugnacaoResultadoTO->getProfissional()->getId()
                );
                if (!empty($chapaEleicao)) {
                    array_push($idsChapasComContrarrazao, $chapaEleicao->getId());
                    $contrarrazaoRecursoImpugnacaoResultadoTO->setNumeroChapa($chapaEleicao->getNumeroChapa());
                }
            }
            $idChapaEleicao = $this->getChapaEleicaoBO()->getIdChapaEleicaoPorCalendarioEResponsavel(
                $impugnacaoResultado->getCalendario()->getId(),
                $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
            );
            $recursoJulgamento->setHasCadastroChapaContrarrazao(in_array($idChapaEleicao, $idsChapasComContrarrazao));
        } else {
            $recursoJulgamento->setHasCadastroChapaContrarrazao(false);
        }
    }

    /**
     * Método auxiliar para setar as informações necessárias para o tipo recurso impugnado
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @param RecursoImpugnacaoResultadoTO $recursoJulgamento
     * @throws \Exception
     */
    private function atribuirInformacaoRecursoImpugnado(
        RecursoImpugnacaoResultadoTO $recursoJulgamento,
        ImpugnacaoResultado $impugnacaoResultado
    ): void {
        $chapaEleicao = $this->getChapaEleicaoBO()->getChapaEleicaoPorCalendarioEResponsavel(
            $impugnacaoResultado->getCalendario()->getId(),
            $recursoJulgamento->getProfissional()->getId()
        );

        if (!empty($chapaEleicao)) {
            $recursoJulgamento->setNumeroChapa($chapaEleicao->getNumeroChapa());
        }
    }

    /**
     * Método para download do arquivo do Recurso de Impugnação de Resultado
     */
    public function downloadDocumento($idRecursoJulgamento)
    {
        /** @var RecursoImpugnacaoResultado $impugnacao */
        $recursoJulgamento = $this->getRecursoImpugnacaoResultadoRepository()->find($idRecursoJulgamento);
        if (!empty($recursoJulgamento->getNomeArquivo())) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorio(
                Constants::PATH_STORAGE_ARQ_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO, $recursoJulgamento->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $recursoJulgamento->getNomeArquivoFisico(),
                $recursoJulgamento->getNomeArquivo()
            );
        }
    }

    /**
     * Verifica se possui algum recurso cadastrado por algum responsável da chapa
     * @param JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado
     * @param $idChapa
     * @param null $idTipoRecuro
     * @throws NegocioException
     */
    public function validarPossuiRecursoCadastrado(
        JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado,
        $idChapa,
        $idTipoRecuro = null
    ) {
        $idImpugnacaoResultado = $julgamentoAlegacaoImpugResultado->getImpugnacaoResultado()->getId();
        $recurso = $this->getRecursoImpugnacaoResultadoRepository()->getRecursoPorImpugnacaoEChapa(
            $idImpugnacaoResultado,
            $idChapa,
            $idTipoRecuro
        );

        if(!empty($recurso)) {
            throw new NegocioException(
                Lang::get('messages.recurso_impugnacao_resultado.possui_recurso_cadastrado')
            );
        }
    }

    /**
     * @param JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado
     * @param RecursoImpugnacaoResultado $recursoImpugnacaoResultado
     * @param AtividadeSecundariaCalendario $atividade
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    private function enviarEmailCadastroRecurso(
        JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado,
        RecursoImpugnacaoResultado $recursoImpugnacaoResultado,
        AtividadeSecundariaCalendario $atividade
    ) {

        $idImpugnacaoResultado = $julgamentoAlegacaoImpugResultado->getImpugnacaoResultado()->getId();
        /** @var ImpugnacaoResultado $impugnacaoResultado */
        $impugnacaoResultado = $this->getImpugnacaoResultadoBO()->find($idImpugnacaoResultado);

        $tipos = Constants::$tiposEmailAtividadeSecundaria[6][4];

        foreach ($tipos as $tipo) {

            $destinatarios = $this->getDestinatariosEmailRecurso(
                $recursoImpugnacaoResultado,
                $atividade,
                $impugnacaoResultado,
                $tipo
            );

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(), $tipo
            );

            if (!empty($emailAtividadeSecundaria) && !empty($destinatarios)) {
                $this->enviarEmail(
                    $recursoImpugnacaoResultado,
                    $emailAtividadeSecundaria,
                    $impugnacaoResultado,
                    $destinatarios
                );
            }
        }

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
     * @param RecursoImpugnacaoResultado $recursoImpugnacaoResultado
     * @param AtividadeSecundariaCalendario $atividade
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @param $tipo
     * @return array
     * @throws NegocioException
     */
    private function getDestinatariosEmailRecurso(
        RecursoImpugnacaoResultado $recursoImpugnacaoResultado,
        AtividadeSecundariaCalendario $atividade,
        ImpugnacaoResultado $impugnacaoResultado,
        $tipo
    ) {

        $idCauUf = empty($impugnacaoResultado->getCauBR()) ? null : $impugnacaoResultado->getCauBR()->getId();
        $tipoRecurso = $recursoImpugnacaoResultado->getTipoRecursoImpugnacaoResultado()->getId();
        $destinatarios = [];

        /** Envia e-mail ao usuário impugnado que cadastrou o recurso */
        if (
            $tipo == Constants::EMAIL_RECURSO_JULG_ALEGACAO_IMPUG_RESULT_IMPUGNADO_CADASTROU &&
            !empty($recursoImpugnacaoResultado->getProfissional()->getPessoa()) &&
            $tipoRecurso == Constants::TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNADO
        ) {
            array_push($destinatarios, $recursoImpugnacaoResultado->getProfissional()->getPessoa()->getEmail());
        }

        /** Envia e-mail ao usuário  impugnante que cadastrou o recurso */
        if (
            $tipo == Constants::EMAIL_RECURSO_JULG_ALEGACAO_IMPUG_RESULT_IMPUGNANTE_CADASTROU &&
            !empty($recursoImpugnacaoResultado->getProfissional()->getPessoa()) &&
            $tipoRecurso == Constants::TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNANTE
        ) {
            array_push($destinatarios, $recursoImpugnacaoResultado->getProfissional()->getPessoa()->getEmail());
        }

        /** Envia e-mail à todos os Coordenadores  */
        /*if ($tipo == Constants::EMAIL_RECURSO_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN) {
            $destinatarios = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
                $atividade->getId(), $idCauUf
            );
        }*/

        /** Envia e-mail aos assessores */
        if ($tipo == Constants::EMAIL_RECURSO_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_ASSESSORES) {
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
                empty($impugnacaoResultado->getCauBR()) ? null : [$idCauUf]
            );
        }
        return $destinatarios;
    }

    /**
     * @param RecursoImpugnacaoResultado $recursoImpugnacaoResultado
     * @param $emailAtividadeSecundaria
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @param array $destinatarios
     * @throws \Exception
     */
    private function enviarEmail(
        RecursoImpugnacaoResultado $recursoImpugnacaoResultado,
        $emailAtividadeSecundaria,
        ImpugnacaoResultado $impugnacaoResultado,
        array $destinatarios
    ) {

        if (!empty($emailAtividadeSecundaria)) {
            $impugnacaoResultadoTO = ImpugnacaoResultadoTO::newInstanceFromEntity($impugnacaoResultado);

            $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
            $emailTO->setDestinatarios($destinatarios);

            $eleicaoTO = EleicaoTO::newInstance([
                'ano' => $impugnacaoResultado->getCalendario()->getEleicao()->getAno(),
                'sequenciaAno' => $impugnacaoResultado->getCalendario()->getEleicao()->getSequenciaAno()
            ]);

            $isIES = empty($impugnacaoResultado->getCauBR());

            Email::enviarMail(new RecursoImpugnacaoResultadoMail(
                $isIES,
                $emailTO,
                $recursoImpugnacaoResultado,
                $impugnacaoResultadoTO->getNumero(),
                $eleicaoTO->getSequenciaFormatada(),
                $isIES ? Constants::PREFIXO_IES : $impugnacaoResultado->getCauBR()->getPrefixo()
            ));
        }
    }

    /**
     * retorna o pedido de Impugnação cadastrado pela chapa do responsável
     * @param $idImpugnacaoResultado
     * @param $idChapa
     * @return RecursoImpugnacaoResultadoTO[]|array|null
     * @throws NegocioException
     */
    public function getRecursoPorImpugnacaoEChapa(int $idImpugnacaoResultado, int $idChapa, $idTipoRecuro = null)
    {
        $recurso = $this->getRecursoImpugnacaoResultadoRepository()->getRecursoPorImpugnacaoEChapa(
            $idImpugnacaoResultado,
            $idChapa,
            $idTipoRecuro
        );

        return $recurso;
    }

}
