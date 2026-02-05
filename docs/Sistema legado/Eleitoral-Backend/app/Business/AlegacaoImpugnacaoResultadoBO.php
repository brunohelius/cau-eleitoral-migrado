<?php
/*
 * ImpugnacaoResultadoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AlegacaoImpugnacaoResultado;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ImpugnacaoResultado;
use App\Entities\MembroChapa;
use App\Entities\StatusImpugnacaoResultado;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\UsuarioFactory;
use App\Jobs\EnviarEmailAlegacaoImpugacaoResultadoJob;
use App\Jobs\EnviarEmailAlegacaoImpugnacaoResultadoJob;
use App\Jobs\EnviarEmailAlegacaoImpungacaoResultadoJob;
use App\Mail\AlegacaoImpugnacaoResultadoMail;
use App\Repository\AlegacaoImpugnacaoResultadoRepository;
use App\Repository\ImpugnacaoResultadoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\AlegacaoImpugnacaoFiltroTO;
use App\To\AlegacaoImpugnacaoResultadoTO;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\EleicaoTO;
use App\To\ImpugnacaoResultadoTO;
use App\To\ValidacaoCadastroAlegacaoImpugResultadoTO;
use App\Util\Email;
use App\Util\Utils;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'AlegacaoImpugnacaoResultadoBO'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class AlegacaoImpugnacaoResultadoBO extends AbstractBO
{
    /**
     * @var ImpugnacaoResultadoBO
     */
    private $impugnacaoResultadoBO;

    /**
     * @var AlegacaoImpugnacaoResultadoRepository
     */
    private $alegacaoImpugnacaoREsultadoRepository;

    /**
     * @var UsuarioFactory
     */
    private $usuarioFactory;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var  AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     * @var ImpugnacaoResultadoRepository
     */
    private $impugnacaoResultadoRepository;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var JulgamentoAlegacaoImpugResultadoBO
     */
    private $julgamentoAlegacaoBO;


    /**
     * Método responsável por salvar a alegação de impugnação de resultado
     * @param AlegacaoImpugnacaoResultadoTO $alegacaoImpugnacaoResultadoTO
     * @return AlegacaoImpugnacaoResultadoTO
     * @throws \Exception
     */
    public function salvar( AlegacaoImpugnacaoResultadoTO $alegacaoImpugnacaoResultadoTO )
    {

        /** @var ImpugnacaoResultadoTO $impugnacaoResultado*/
        $impugnacaoResultado = $this->getImpugnacaoResultadoBO()
            ->getImpugnacaoPorId($alegacaoImpugnacaoResultadoTO->getIdPedidoImpugnacaoResultado());

        if(!empty($impugnacaoResultado->getId())) {

            /** @var UsuarioFactory $usuario*/
            $usuario = $this->getUsuarioFactory()->getUsuarioLogado();

            /** @var ArquivoGenericoTO|null $arquivo */
            $arquivo = $this->getArquivosAlegacao($alegacaoImpugnacaoResultadoTO);

            $this->validaSePossuiJulgamentoAlegacao($impugnacaoResultado);
            $this->validarPeriodoVigencia($impugnacaoResultado);
            $this->validaPermissaoResponsavelChapa($impugnacaoResultado, $usuario);
            $this->validarCamposObrigatorios($alegacaoImpugnacaoResultadoTO, $arquivo);

            try {

                $this->beginTransaction();
                $alegacaoImpugnacaoResultado = $this->getAlegacaoImpugnacaoResultado(
                    $alegacaoImpugnacaoResultadoTO,
                    $impugnacaoResultado,
                    $usuario->idProfissional,
                    $arquivo
                );

                $this->getAlegacaoImpugnacaoResultadoRepository()->persist($alegacaoImpugnacaoResultado);

                if (!empty($arquivo)) {
                    $this->salvarArquivo(
                        $alegacaoImpugnacaoResultado->getId(),
                        $arquivo->getArquivo(),
                        $alegacaoImpugnacaoResultado->getNomeArquivoFisico()
                    );
                }

                /** @var ImpugnacaoResultado $impugnacaoResultado */
                $impugnacaoResultado = $this->getImpugnacaoResultadoBO()->getPorId($impugnacaoResultado->getId());
                    $impugnacaoResultado->setStatus(StatusImpugnacaoResultado::newInstance([
                        'id'=> Constants::STATUS_IMPUG_RESULTADO_ALEGACAO_INSERIDA
                    ]));

                $this->getImpugnacaoResultadoRepository()->persist($impugnacaoResultado);

                $this->salvarHistoricoAlegacaoImpugnacao($alegacaoImpugnacaoResultado);

                $this->enviarEmailCadastro($alegacaoImpugnacaoResultado->getId(), $impugnacaoResultado->getCalendario
                ()->getId());

                $this->commitTransaction();

                return AlegacaoImpugnacaoResultadoTO::newInstanceFromEntity($alegacaoImpugnacaoResultado);

            } catch (\Exception $e) {
                $this->rollbackTransaction();
                throw $e;
            }

        } else {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

    }

    /**
     * Retorna To para validação de botão de cadastro de alegação.
     *
     * @param $idImpugnacao
     * @return ValidacaoCadastroAlegacaoImpugResultadoTO
     */
    public function getValidacaoCadastroAlegacaoTO($idImpugnacao) : ValidacaoCadastroAlegacaoImpugResultadoTO
    {
        $impugnacaoResultado = $this->getImpugnacaoResultadoBO()->find($idImpugnacao);

        if(empty($impugnacaoResultado)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $validacaoTO = new ValidacaoCadastroAlegacaoImpugResultadoTO();

        $profissional = $this->getUsuarioFactory()->getUsuarioLogado();
        $membroChapa = $this->getMembroChapaBO()->findBy(["profissional" => $profissional->idProfissional]);
        $chapa = null;

        if(!empty($membroChapa) && $membroChapa[0]->isSituacaoResponsavel()){
            $chapa = $this->getChapaEleicaoBO()->getPorId($membroChapa[0]->getChapaEleicao()->getId(), false);

            if (!empty($chapa)) {
                $idCauUFChapa = $chapa->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES ? 0 : $chapa->getCauUf()->getId();
                $idCauUFImpugnacaoResultado = !empty($impugnacaoResultado->getCauBR()) ? $impugnacaoResultado->getCauBR()->getId() : Constants::ID_IES_ELEITORAL;
                $validacaoTO->setIsResponsavel(
                    $idCauUFChapa == $idCauUFImpugnacaoResultado
                );
            }
        }

        $isVigente = $this->getAtividadeSecundariaCalendarioBO()->isAtividadeVigente(
            $impugnacaoResultado->getCalendario()->getId() ,
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_ALEGACAO_INPUGNACAO_RESULTADO,
            Constants::NIVEL_ATIVIDADE_INCLUSAO_ALEGACAO_INPUGNACAO_RESULTADO
        );
        $validacaoTO->setIsVigenteAtivCadastroAlegacaoImpugResultado($isVigente);


        $idMembrosResponsaveisChapa = [];
        if(!empty($chapa)) {
            $membrosResponsaveisChapa = $this->getMembroChapaBO()->getMembrosResponsaveisChapa(
                $chapa->getId(),
                Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
            );
            $idMembrosResponsaveisChapa = array_map(function ($membroChapa) {
                /** @var MembroChapa $membroChapa */
                return $membroChapa->getProfissional()->getId();
            }, $membrosResponsaveisChapa);
        }

        $filtroAlegacao = AlegacaoImpugnacaoFiltroTO::newInstance([
            'idCalendarios' =>  [ $impugnacaoResultado->getCalendario()->getId() ],
            'idProfissionais' => $idMembrosResponsaveisChapa,
            'idImpugnacoes' => [$idImpugnacao],
        ]);
        $alegacoes = $this->getAlegacaoImpugnacaoResultadoRepository()->getPorFiltro($filtroAlegacao);

        $validacaoTO->setHasAlegacao(!empty($alegacoes));

        return $validacaoTO;
    }


    /**
     * Retorna a Alegaçãao a partir do id da Impugnacao Resultado.
     * @param $idImpugnacao
     * @return mixed
     */
    public function getAlegacaoPorIdImpugnacao($idImpugnacao)
    {
        $alegacao = $this->getAlegacaoImpugnacaoResultadoRepository()->getPorIdImpugnacao($idImpugnacao);
        return $alegacao;
    }

    /**
     * Método para download do arquivo da Alegação de Impugnação de Resultado
     */
    public function downloadDocumento($idAlegacao)
    {

        /** @var AlegacaoImpugnacaoResultado $impugnacao */
        $alegacao = $this->getAlegacaoImpugnacaoResultadoRepository()->find($idAlegacao);
        if (!empty($alegacao->getNomeArquivo())) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioAlegacaoImpugnacaoResultado(
                $alegacao->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $alegacao->getNomeArquivoFisico(),
                $alegacao->getNomeArquivo()
            );
        }

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
     * Retorna uma instancia de AlegacaoImpugnacaoResultadoRepository
     *
     * @return \App\Repository\AlegacaoImpugnacaoResultadoRepository
     */
    private function getAlegacaoImpugnacaoResultadoRepository()
    {
        if(empty($this->alegacaoImpugnacaoREsultadoRepository)) {
            $this->alegacaoImpugnacaoREsultadoRepository = $this->getRepository(AlegacaoImpugnacaoResultado::class);
        }

        return $this->alegacaoImpugnacaoREsultadoRepository;
    }

    /**
     * Prepara todos os dados necessários para retornar uma instancia da alegação
     * com o número de sequencia e arquivos anexados.
     *
     * @param AlegacaoImpugnacaoResultadoTO $alegacaoImpugnacaoResultadoTO
     * @param ImpugnacaoResultadoTO $pedidoImpugnacaoResultado
     * @param $profissional
     * @param $arquivo
     * @return AlegacaoImpugnacaoResultado
     * @throws \Exception
     */
    private function getAlegacaoImpugnacaoResultado(
        AlegacaoImpugnacaoResultadoTO $alegacaoImpugnacaoResultadoTO,
        ImpugnacaoResultadoTO $pedidoImpugnacaoResultado,
        $profissional,
        $arquivo
    ): AlegacaoImpugnacaoResultado
    {
        $nomeArquivoFisico = $this->getNomeArquivoFisico($arquivo);
        $numero = $this->getNumeroSequencial($pedidoImpugnacaoResultado);
        $nomeArquivo = null;

        if(!empty($alegacaoImpugnacaoResultadoTO->getArquivos())) {
            $nomeArquivo = $alegacaoImpugnacaoResultadoTO->getArquivos()[0]->getNome();
        }

        $alegacaoImpugnacaoResultado = AlegacaoImpugnacaoResultado::newInstance([
            'numero' => $numero,
            'profissional' =>  ['id' => $profissional],
            'narracao' => $alegacaoImpugnacaoResultadoTO->getNarracao(),
            'dataCadastro' => $alegacaoImpugnacaoResultadoTO->getDataCadastro(),
            'nomeArquivo' => $nomeArquivo,
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'impugnacaoResultado' => ['id' => $alegacaoImpugnacaoResultadoTO->getIdPedidoImpugnacaoResultado()]
        ]);

        return $alegacaoImpugnacaoResultado;
    }


    /**
     * Retorna o usuário conforme o padrão lazy Inicialization.
     *
     * @return UsuarioFactory | null
     */
    protected function getUsuarioFactory()
    {
        if ($this->usuarioFactory == null) {
            $this->usuarioFactory = app()->make(UsuarioFactory::class);
        }
        return $this->usuarioFactory;
    }

    /**
     * Retorna o profissional conforme o padrão lazy Inicialization.
     *
     * @return ProfissionalBO | null
     */
    protected function getProfissional()
    {
        if ($this->profissionalBO == null) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }
        return $this->profissionalBO;
    }

    /**
     * @param AlegacaoImpugnacaoResultadoTO $alegacaoImpugnacaoResultadoTO
     * @return ArquivoGenericoTO[]|array|null
     */
    private function getArquivosAlegacao(AlegacaoImpugnacaoResultadoTO $alegacaoImpugnacaoResultadoTO)
    {
        $arquivos = $alegacaoImpugnacaoResultadoTO->getArquivos();

        /** @var ArquivoGenericoTO|null $arquivo */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;
        return $arquivo;
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
     * Valida se os dados da alegação foram preenchidos e se o tamanho do arquivo
     * não ultrapassa o limite especificado na regra.
     *
     * @param $alegacaoImpugnacaoResultadoTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     */
    private function validarCamposObrigatorios($alegacaoImpugnacaoResultadoTO, $arquivo)
    {
        if (empty($alegacaoImpugnacaoResultadoTO->getNarracao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (!empty($arquivo)) {
            $this->getArquivoService()->validarArquivoGenrico(
                $arquivo, Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_15MB
            );
        }
    }

    /**
     * Salva os arquivos anexados no banco de dados e no diretório
     *
     * @param $idAlegacaoImpugnacao
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idAlegacaoImpugnacao, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioAlegacaoImpugnacaoResultado($idAlegacaoImpugnacao),
            $nomeArquivoFisico,
            $arquivo
        );
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
     * Valida se o período da atividade 6.2 está vigente para cadastro de Alegação
     * @param ImpugnacaoResultadoTO $impugnacaoResultado
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function validarPeriodoVigencia(ImpugnacaoResultadoTO $impugnacaoResultado)
    {
        $isVigente = false;

        if($impugnacaoResultado->getCalendario()->getId()) {
            $isVigente = $this->getAtividadeSecundariaCalendarioBO()->isAtividadeVigente(
                $impugnacaoResultado->getCalendario()->getId() , 6, 2
            );
        } else {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if (!$isVigente) {
            throw new NegocioException(Message::MSG_PERIODO_FORA_DE_VIGENCIA_GENERICO);
        }

    }

    /**
     * Busca a chapa no qual o usuário logado é responsável e verifica se a uf da chapa é igual a uf do pedido
     * de impugnação cadastrado.
     *
     * @param ImpugnacaoResultadoTO $impugnacaoResultado
     * @param $usuario
     * @throws NegocioException
     */
    private function validaPermissaoResponsavelChapa(ImpugnacaoResultadoTO $impugnacaoResultado, $usuario): void
    {
        $chapaEleicao = $this->getChapaEleicaoBO();
        $dadosChapa = $chapaEleicao->getChapaEleicaoPorCalendarioEResponsavel(
            $impugnacaoResultado->getCalendario()->getId(),
            $usuario->idProfissional
        );

        if ($dadosChapa == null) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }


        if(empty($impugnacaoResultado->getCauBR())) {
            if($dadosChapa->getTipoCandidatura()->getId() != Constants::TIPO_CANDIDATURA_IES) {
                throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
            }

        } else {

            $ufChapa = $dadosChapa->getIdCauUf();
            $ufDoPedidoImpugnacao = $impugnacaoResultado->getCauBR()->getId();

            if ($dadosChapa != null && $ufChapa != $ufDoPedidoImpugnacao) {
                throw new NegocioException(Message::MSG_SEM_MERMISSAO_VISUALIZACAO_ATIV_SELECIONADA);
            }
        }


    }

    /**
     * Retorna o número de sequencia para a Alegação do pedido de impugnação
     * @param ImpugnacaoResultadoTO $pedidoImpugnacaoResultado
     * @return int|mixed
     */
    private function getNumeroSequencial(ImpugnacaoResultadoTO $pedidoImpugnacaoResultado)
    {
        $total = $this->getAlegacaoImpugnacaoResultadoRepository()->getTotalAlegacaoImpugnacaoPorImpugnacao(
            $pedidoImpugnacaoResultado->getId()
        );

        $numero = !empty($total) ? $total + 1 : 1;
        return $numero;
    }

    /**
     * Retorna a descrição do arquivo físico que será gravado no banco
     * @param $arquivo
     * @return string
     */
    private function getNomeArquivoFisico($arquivo): string
    {
        $nomeArquivoFisico = empty($arquivo) ? '' : $this->getArquivoService()->getNomeArquivoFormatado(
            $arquivo->getNome(), Constants::PREFIXO_ARQ_ALEGACAO_IMPUGN_RESULTADO
        );
        return $nomeArquivoFisico;
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
     * Método auxiliar para salvar o histórico  de Alegação de
     *
     * @param AlegacaoImpugnacaoResultado $alegacaoImpugnacaoResultado
     * @throws \Exception
     */
    private function salvarHistoricoAlegacaoImpugnacao(AlegacaoImpugnacaoResultado $alegacaoImpugnacaoResultado): void
    {
        $historico = $this->getHistoricoBO()->criarHistorico(
            $alegacaoImpugnacaoResultado,
            Constants::HISTORICO_ALEGACAO_IMPUGNACAO_RESULTADO,
            Constants::HISTORICO_ACAO_INSERIR,
            Constants::HISTORICO_INCLUSAO_ALEGACAO_IMPUGNACAO_RESULTADO
        );
        $this->getHistoricoBO()->salvar($historico);
    }

    /**
     * Retorna uma nova instância de 'PublicacaoDocumentoRepository'.
     *
     * @return \App\Repository\ImpugnacaoResultadoRepository
     */
    private function getImpugnacaoResultadoRepository()
    {
        if (empty($this->impugnacaoResultadoRepository)) {
            $this->impugnacaoResultadoRepository = $this->getRepository(ImpugnacaoResultado::class);
        }

        return $this->impugnacaoResultadoRepository;
    }

    /**
     * @param AlegacaoImpugnacaoResultadoTO $alegacaoImpugnacaoResultado
     * @param $idCalendario
     * @param $idCauUF
     * @param $anoEleitoral
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enviarEmailCadastro($idAlegacao, $idCalendario)
    {

        /** @var AlegacaoImpugnacaoResultado $alegacaoImpugnacaoResultado */
        $alegacaoImpugnacaoResultado = $this->getAlegacaoImpugnacaoResultadoRepository()->find($idAlegacao);
        $alegacaoImpugnacaoResultadoTO = AlegacaoImpugnacaoResultadoTO::newInstanceFromEntity($alegacaoImpugnacaoResultado);
        $impugnacaoResultado = $alegacaoImpugnacaoResultado->getImpugnacaoResultado();
        $impugnacaoResultadoTO = ImpugnacaoResultadoTO::newInstance(['numero'=> $impugnacaoResultado->getNumero()]);

        $anoEleitoral = $this->getAnoEleitoral($impugnacaoResultado);

        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $idCalendario, 6,2
        );

        $tiposEmail = Constants::$tiposEmailAtividadeSecundaria[6][2];

        $descricaoUf =  $impugnacaoResultado->getCauBR()
            ? $impugnacaoResultado->getCauBR()->getPrefixo()
            : Constants::PREFIXO_IES;

        foreach ($tiposEmail as $tipo) {

            $destinatarios = $this->getDestinatariosEmail($alegacaoImpugnacaoResultadoTO, $tipo, $atividadeSecundaria, $impugnacaoResultado);

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividadeSecundaria->getId(), $tipo
            );

            if (!empty($emailAtividadeSecundaria) && !empty($destinatarios)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                Email::enviarMail(new AlegacaoImpugnacaoResultadoMail(
                    $emailTO,
                    $alegacaoImpugnacaoResultadoTO,
                    $descricaoUf,
                    $anoEleitoral,
                    $impugnacaoResultadoTO->getNumero()
                ));
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
     * Retornar lista de array de destinatários dos emails.
     *
     * @param AlegacaoImpugnacaoResultadoTO $alegacaoImpugnacaoResultado
     * @param $tipo
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @return array
     * @throws NegocioException
     */
    private function getDestinatariosEmail(
        AlegacaoImpugnacaoResultadoTO $alegacaoImpugnacaoResultado ,
        $tipo, AtividadeSecundariaCalendario $atividadeSecundaria,
        ImpugnacaoResultado $impugnacaoResultado
    ): array
    {
        /**
         * @var array $destinatarios
         * @var ImpugnacaoResultado $impugnacaoResultado
         * @var int $idCauUf
         */
        $destinatarios = [];


        $idCauUf = empty($impugnacaoResultado->getCauBR()) ? null : $impugnacaoResultado->getCauBR()->getId();

        /** Registro 01 – Envia e-mail aos responsáveis Chapa/IES.  */
        if($tipo == Constants::EMAIL_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNADO) {

            $isIES =  empty($impugnacaoResultado->getCauBR());
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
        if($tipo == Constants::EMAIL_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNANTE) {
            if (!empty($impugnacaoResultado->getProfissional()->getPessoa())) {
                array_push($destinatarios, $impugnacaoResultado->getProfissional()->getPessoa()->getEmail());
            }
        }

        /** Envia e-mail à todos os Coordenadores  */
        /*if($tipo == Constants::EMAIL_ALEGACAO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN) {
            $destinatarios = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
                $atividadeSecundaria->getId(), $idCauUf
            );
        }*/

        /** Envia e-mail aos assessores */
        if($tipo == Constants::EMAIL_ALEGACAO_IMPUGNACAO_RESULTADO_ASSESSORES)
        {
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
                empty($impugnacaoResultado->getCauBR()) ? null : [$idCauUf]
            );
        }

        return $destinatarios;
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
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @return string|null
     */
    private function getAnoEleitoral(ImpugnacaoResultado $impugnacaoResultado)
    {
        $impugnacaoResultado->getCalendario()->getEleicao()->getAno();
        $impugnacaoResultado->getCalendario()->getEleicao()->getSequenciaAno();

        $eleicaoTO = EleicaoTO::newInstance([
            'ano' => $impugnacaoResultado->getCalendario()->getEleicao()->getAno(),
            'sequenciaAno' => $impugnacaoResultado->getCalendario()->getEleicao()->getSequenciaAno()
        ]);

        $anoEleitoral = $eleicaoTO->getSequenciaFormatada();
        return $anoEleitoral;
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
     * Retorna uma nova instância de 'JulgamentoAlegacaoImpugBO'.
     *
     * @return JulgamentoAlegacaoImpugResultadoBO
     */
    private function getJulgamentoAlegacaoImpugnacaoResultado()
    {
        if (empty($this->julgamentoAlegacaoBO)) {
            $this->julgamentoAlegacaoBO = app()->make(JulgamentoAlegacaoImpugResultadoBO::class);
        }
        return $this->julgamentoAlegacaoBO;
    }

    /**
     * Verifica se possui julgamento cadastrado para o pedido de impugnação no qual o usuário deseja
     * cadastrar uma alegação
     *
     * @param ImpugnacaoResultadoTO $impugnacaoResultado
     * @throws NegocioException
     */
    private function validaSePossuiJulgamentoAlegacao(ImpugnacaoResultadoTO $impugnacaoResultado): void
    {
        $julgamentoAlegacaoBO = $this->getJulgamentoAlegacaoImpugnacaoResultado();
        $julgamento = $julgamentoAlegacaoBO->getJulgamentoAlegacaoPorImpugnacaoResultado($impugnacaoResultado->getId());
        if (!empty($julgamento)) {
            throw new NegocioException(Message::MSG_POSSUI_JULGAMENTO_ALEGACAO_CADASTRADO);
        }
    }

}
