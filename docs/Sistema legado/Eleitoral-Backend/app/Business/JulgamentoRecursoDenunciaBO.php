<?php
/*
 * JulgamentoRecursoDenunciaBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoJulgamentoRecursoDenuncia;
use App\Entities\Denuncia;
use App\Entities\JulgamentoRecursoDenuncia;
use App\Entities\RecursoDenuncia;
use App\Entities\RetificacaoJulgamentoRecursoDenuncia;
use App\Entities\StatusRecursoDenuncia;
use App\Entities\TipoJulgamento;
use App\Entities\TipoSentencaJulgamento;
use App\Entities\Usuario;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailJulgamentoRecursoDenunciaJob;
use App\Jobs\EnviarEmailJulgamentoRecursoDenunciaPrazoEncerradoJob;
use App\Mail\JulgamentoRecursoDenunciaMail;
use App\Repository\ArquivoJulgamentoRecursoDenunciaRepository;
use App\Repository\DenunciaRepository;
use App\Repository\JulgamentoRecursoDenunciaRepository;
use App\Repository\RecursoDenunciaRepository;
use App\Repository\RetificacaoJulgamentoRecursoDenunciaRepository;
use App\Service\ArquivoService;
use App\Service\CalendarioApiService;
use App\To\ArquivoTO;
use App\To\JulgamentoRecursoDenunciaTO;
use App\To\RetificacaoJulgamentoRecursoDenunciaTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use Exception;
use App\Service\CorporativoService;
use App\To\EmailDenunciaTO;
use App\Util\Email;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a
 * entidade 'Julgar Recurso 2 instancia'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */

class JulgamentoRecursoDenunciaBO extends AbstractBO
{
    /**
     * @var HistoricoDenunciaBO
     */
    private $historicoDenunciaBO;

    /**
     * @var StatusRecursoDenunciaBO
     */
    private $statusRecursoDenunciaBO;

    /**
     * @var DenunciaRepository
     */
    private $denunciaRepository;

    /**
     * @var RecursoDenunciaRepository
     */
    private $recursoDenunciaRepository;

    /**
     * @var JulgamentoRecursoDenunciaRepository
     */
    private $julgamentoRecursoRepository;

    /**
     * @var RetificacaoJulgamentoRecursoDenunciaRepository
     */
    private $retificacaoJulgamentoRecursoRepository;

    /**
     * @var DenunciaBO
     */
    private $denunciaBO;

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var CalendarioApiService
     */
    private $calendarioApiService;

    /**
     * @var ArquivoJulgamentoRecursoDenunciaRepository
     */
    private $arquivoJulgamentoRecursoRepository;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var EncaminhamentoDenunciaBO
     */
    private $encaminhamentoDenunciaBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    public function __construct()
    {
        $this->recursoDenunciaRepository          = $this->getRepository(RecursoDenuncia::class);
        $this->denunciaRepository                 = $this->getRepository(Denuncia::class);
        $this->julgamentoRecursoRepository        = $this->getRepository(JulgamentoRecursoDenuncia::class);
        $this->arquivoJulgamentoRecursoRepository = $this->getRepository(ArquivoJulgamentoRecursoDenuncia::class);
        $this->retificacaoJulgamentoRecursoRepository = $this->getRepository(RetificacaoJulgamentoRecursoDenuncia::class);
    }

    /**
     * @param JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia
     * @param $dados
     * @return null
     * @throws Exception
     */
    public function salvar(JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia, $dados)
    {
        $denuncia = $this->denunciaRepository->find($dados['idDenuncia']);

        if($julgamentoRecursoDenuncia->isRetificacao())
        {
            $julgRecurso = $this->julgamentoRecursoRepository->find($dados['idJulgamento']);
        }

        //Buscar denunciante pelo denuncia
        $recursoDenunciado = current(
            $this->recursoDenunciaRepository->getRecursoDenunciaPorTipo(
                Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO,
                $dados['idDenuncia']
            )
        );

        $recursoDenunciante = current(
            $this->recursoDenunciaRepository->getRecursoDenunciaPorTipo(
                Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE,
                $dados['idDenuncia']
            )
        );

        $julgamentoSalvo = null;

        $data = Utils::getData();

        try {
            $this->beginTransaction();

            $arquivos = !empty($julgamentoRecursoDenuncia->getArquivosJulgamentoRecursoDenuncia())
                ? clone $julgamentoRecursoDenuncia->getArquivosJulgamentoRecursoDenuncia()
                : null;

            $usuarioFactory = $this->getUsuarioFactory();

            //Salvando Retificação do Julgamento do Recurso

            if ($julgamentoRecursoDenuncia->isRetificacao()) {
                if ($usuarioFactory && !$usuarioFactory->isCorporativoAssessorCEN()) {
                    throw new NegocioException(Lang::get('messages.denuncia.julgamento.assessor_ce_outra_uf'));
                }
                $dados['julgamentoRecursoDenuncia'] = null;

                $julgamentoRetificacao = RetificacaoJulgamentoRecursoDenuncia::newInstance($dados);
                $julgamentoRetificacao->setData($data);
                $julgamentoRetificacao->setUsuario(Usuario::newInstance(['id' => $usuarioFactory->getUsuarioLogado()->id]));
                if(!empty($dados['tipoJulgamentoDenunciante'])){
                    $julgamentoRetificacao->setTipoJulgamentoDenunciante(TipoJulgamento::newInstance(['id' => $dados['tipoJulgamentoDenunciante']]));
                }
                if(!empty($dados['tipoJulgamentoDenunciado'])){
                    $julgamentoRetificacao->setTipoJulgamentoDenunciado(TipoJulgamento::newInstance(['id' => $dados['tipoJulgamentoDenunciado']]));
                }
                $julgamentoRetificacao->setArquivosJulgamentoRecursoDenuncia(null);
                $julgamentoRetificacao->setJulgamentoRetificado(JulgamentoRecursoDenuncia::newInstance(['id' => $julgRecurso->getId()]));

                if (!empty($dados['tipoSentencaJulgamento'])) {
                    $julgamentoRetificacao->setTipoSentencaJulgamento(TipoSentencaJulgamento::newInstance(['id' => $dados['tipoSentencaJulgamento']]));
                }

                if (!empty($dados['valorPercentualMulta'])) {
                    $julgamentoRetificacao->setValorPercentualMulta($dados['valorPercentualMulta']);
                }

                if (!empty($recursoDenunciado)) {
                    $julgamentoRetificacao->setRecursoDenunciado(RecursoDenuncia::newInstance(['id'=> $recursoDenunciado['id']]));
                }

                if (!empty($recursoDenunciante)) {
                    $julgamentoRetificacao->setRecursoDenunciante(RecursoDenuncia::newInstance(['id' => $recursoDenunciante['id']]));
                }

                $julgamentoSalvo = $this->retificacaoJulgamentoRecursoRepository->persist($julgamentoRetificacao);

                if (!empty($arquivos)) {
                    $idsArquivosExcluidos = !empty($dados['arquivosExcluidos'])
                                                ? Utils::getArrayFromString($dados['arquivosExcluidos'])
                                                : [];

                    $this->salvarArquivosRetificacaoJulgamentoRecursoDenuncia(
                        $arquivos,
                        $julgamentoSalvo,
                        $idsArquivosExcluidos
                    );
                }

            }else{
                //Salvando Julgamento Recurso
                $julgamentoRecursoDenuncia->setData($data);
                $julgamentoRecursoDenuncia->setUsuario(Usuario::newInstance(['id' => $usuarioFactory->getUsuarioLogado()->id]));
                if(!empty($dados['tipoJulgamentoDenunciante'])){
                    $julgamentoRecursoDenuncia->setTipoJulgamentoDenunciante(TipoJulgamento::newInstance(['id' => $dados['tipoJulgamentoDenunciante']]));
                }
                if(!empty($dados['tipoJulgamentoDenunciado'])){
                    $julgamentoRecursoDenuncia->setTipoJulgamentoDenunciado(TipoJulgamento::newInstance(['id' => $dados['tipoJulgamentoDenunciado']]));
                }                
                $julgamentoRecursoDenuncia->setArquivosJulgamentoRecursoDenuncia(null);

                if (!empty($recursoDenunciante)) {
                    $julgamentoRecursoDenuncia->setRecursoDenunciante(RecursoDenuncia::newInstance(['id' => $recursoDenunciante['id']]));
                }
                if (!empty($dados['tipoSentencaJulgamento'])) {
                    $julgamentoRecursoDenuncia->setTipoSentencaJulgamento(TipoSentencaJulgamento::newInstance(['id'=>$dados['tipoSentencaJulgamento']]));
                }
                if (!empty($dados['valorPercentualMulta'])) {
                    $julgamentoRecursoDenuncia->setValorPercentualMulta($dados['valorPercentualMulta']);
                }
                if (!empty($recursoDenunciado)) {
                    $julgamentoRecursoDenuncia->setRecursoDenunciado(RecursoDenuncia::newInstance(['id'=> $recursoDenunciado['id']]));
                }

                $julgamentoSalvo = $this->julgamentoRecursoRepository->persist($julgamentoRecursoDenuncia);

            //Salva arquivo
            if (!empty($arquivos)) {
                $this->salvarArquivosJulgamentoDenuncia($arquivos, $julgamentoSalvo);
            }

            //Salvando o status da denuncia
            $recursoDenuncianteOuDenunciado = !empty($recursoDenunciante) ? $recursoDenunciante : $recursoDenunciado;
            $recursoDenuncia = $this->recursoDenunciaRepository->find($recursoDenuncianteOuDenunciado['id']);

                $stDenuncia = StatusRecursoDenuncia::newInstance();
                $stDenuncia->setRecursoDenuncia($recursoDenuncia);
                $stDenuncia->setData($data);
                if(!empty($dados['tipoJulgamentoDenunciado'])){
                    $stDenuncia->setSituacaoDenuncia(TipoJulgamento::newInstance(['id'=>$dados['tipoJulgamentoDenunciado']]));
                } else {
                    $stDenuncia->setSituacaoDenuncia(TipoJulgamento::newInstance(['id'=>$dados['tipoJulgamentoDenunciante']]));
                }
                $this->getStatusRecursoDenunciaBO()->salvar($stDenuncia);

                //Alterar status da denuncia
                $this->getDenunciaBO()->alterarStatusSituacaoDenuncia($denuncia, Constants::STATUS_DENUNCIA_TRANSITADO_EM_JULGADO);

                //Salva arquivo
                if (!empty($arquivos)) {
                    $this->salvarArquivosJulgamentoDenuncia($arquivos, $julgamentoSalvo);
                }
            }

            //Histórico
            $hisJulgamentoRecurso = $this->getHistoricoDenunciaBO()->criarHistorico(
                $denuncia,
                Constants::ACAO_HISTORICO_JULGAMENTO_RECURSO_DENUNCIA
            );
            $this->getHistoricoDenunciaBO()->salvar($hisJulgamentoRecurso);

            $this->commitTransaction();
        } catch (\Exception $e) {

            $this->rollbackTransaction();
            throw $e;
        }
        if (!empty($julgamentoSalvo)) {
            Utils::executarJOB(new EnviarEmailJulgamentoRecursoDenunciaJob($julgamentoSalvo->getId()));
        }

        return JulgamentoRecursoDenunciaTO::newInstanceFromEntity($julgamentoSalvo);
    }

    /**
     * Valida se os campos obrigatórios estão preenchidos
     *
     * @param \App\Entities\JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia
     *
     * @throws \App\Exceptions\NegocioException
     */
    private function validarCamposObrigatoriosJulgamentoRecurso(JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia)
    {
        $campos = [];

        if (empty($julgamentoRecursoDenuncia->getDescricaoDespacho())) {
            $campos[] = 'LABEL_DS_DESPACHO';
        }

        if (empty($julgamentoRecursoDenuncia->getIdDenuncia())) {
            $campos[] = 'LABEL_ID_DENUNCIA';
        }

        if (empty($julgamentoRecursoDenuncia->getIdMembroComissao())) {
            $campos[] = 'LABEL_ID_MEMBRO_COMISSAO';
        }

        // Arquivo ???

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Valida a quantidade de arquivos para a Denuncia Inadmitida
     *
     * @param JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia
     * @throws NegocioException
     */
    private function validarQuantidadeArquivosJulgamentoRecurso(JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia)
    {
        $arquivos = $julgamentoRecursoDenuncia->getArquivoDenunciaAdmitida();
        if (!empty($arquivos) and count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO_DENUNCIA) {
            throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO);
        }
    }

    /**
     * Cria os nomes de arquivo para Recurso
     *
     * @param JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia
     * @return RecursoDenuncia
     */
    private function setNomeArquivoFisicoRecurso(JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia)
    {
        if ($julgamentoRecursoDenuncia->getArquivosJulgamentoRecursoDenuncia() !== null) {
            foreach ($julgamentoRecursoDenuncia->getArquivosJulgamentoRecursoDenuncia() as $arquivo) {
                if (empty($arquivo->getId())) {
                    $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                        $arquivo->getNome(),
                        Constants::PREFIXO_DOC_DENUNCIA_RECURSO
                    );
                    $arquivo->setNomeFisico($nomeArquivoFisico);
                }
            }
        }
        return $julgamentoRecursoDenuncia;
    }

    /**
     * Retorna as informações de recurso para exportar para PDF
     *
     * @param $idDenuncia
     * @return \stdClass
     * @throws \Exception
     */
    public function getExportarJulgamentoRecursosPorTipoRecurso($idDenuncia)
    {
        $filtro = new \stdClass();
        $filtro->idDenuncia = $idDenuncia;

        $recursosDenuncia = $this->recursoDenunciaRepository->getRecursosPorFiltro($filtro);

        $recursosContrarrazao = $this->getDenunciaBO()->getEstruturaRecursosContrarrazao($recursosDenuncia);
        $recursoDenuncia = !empty($recursosContrarrazao) ? current($recursosContrarrazao) : null;

        /** @var JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia */
        $julgamentosRecursoDenuncia = $recursoDenuncia->getJulgamentoRecursoDenunciante()
            ?? $recursoDenuncia->getJulgamentoRecursoDenunciado();

        $julgamentosRecursoDenuncia = $julgamentosRecursoDenuncia ?? [];
        if (!is_array($julgamentosRecursoDenuncia)) {
            $julgamentosRecursoDenuncia = $julgamentosRecursoDenuncia->toArray();
        }

        if (!empty($julgamentosRecursoDenuncia)) {
            $historicoRetificacoes = array_map(static function(JulgamentoRecursoDenuncia $julgamento) {
                $retificacao = $julgamento->getRetificacaoJulgamento();
                if (null !== $retificacao) {
                    return RetificacaoJulgamentoRecursoDenunciaTO::newInstanceFromEntity($retificacao);
                }
            }, array_filter($julgamentosRecursoDenuncia, static function (JulgamentoRecursoDenuncia $julgamento) {
                return null !== $julgamento->getRetificacaoJulgamento();
            }));

            uasort( $historicoRetificacoes, static function($retificacaoA, $retificacaoB) {
                return $retificacaoA->getData()->getTimestamp() < $retificacaoB->getData()->getTimestamp();
            });

            /** @var JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia */
            $julgamentoRecursoDenuncia = $recursoDenuncia->getUltimoJulgamentoDenunciante()
                ?? $recursoDenuncia->getUltimoJulgamentoDenunciado();

            if ($julgamentoRecursoDenuncia) {
                $arquivosJulgamentoRecurso = $julgamentoRecursoDenuncia->getArquivosJulgamentoRecursoDenuncia();
                if ( ! empty($arquivosJulgamentoRecurso)) {
                    $julgamentoRecursoDenuncia->setArquivosJulgamentoRecursoDenuncia($this->getDenunciaBO()->getDescricaoArquivoExportar(
                        $arquivosJulgamentoRecurso,
                        $this->getArquivoService()->getCaminhoRepositorioJulgamentoSegundaInstanciaRecurso($julgamentoRecursoDenuncia->getId())
                    ));
                }
            }

            $julgamento = new \stdClass();
            $julgamento->retificacoes = $historicoRetificacoes;
            $julgamento->julgamento = JulgamentoRecursoDenunciaTO::newInstanceFromEntity($julgamentoRecursoDenuncia);
        }

        return $julgamento ?? null;
    }

    /**
     * Realiza o(s) upload(s) do(s) arquivo(s) da Denuncia
     *
     * @param $arquivo
     * @param $julgamentoRecurso
     */
    private function salvaArquivosDiretorio($arquivo, $julgamentoRecurso)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioRecursoDenuncia($julgamentoRecurso->getId());

        if ($arquivo !== null) {
            if (!empty($arquivo->getArquivo())) {
                $this->getArquivoService()->salvar($caminho, $arquivo->getNomeFisico(), $arquivo->getArquivo());
            }
        }
    }

    /**
     * Método auxiliar para Salvar dados dos Arquivos para a Julgamento Recurso Denuncia
     *
     * @param $arquivosJulgamento
     * @param JulgamentoRecursoDenuncia|RetificacaoJulgamentoRecursoDenuncia $julgamentoRecursoDenuncia
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    private function salvarArquivosJulgamentoDenuncia($arquivosJulgamento, $julgamentoRecursoDenuncia)
    {
        $arquivosSalvos = new ArrayCollection();

        /** @var ArquivoJulgamentoRecursoDenuncia $arquivoJulgamento */
        foreach ($arquivosJulgamento as $arquivoJulgamento) {
            if (!$arquivoJulgamento->getId()) {
                $nomeFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                    $arquivoJulgamento->getNome(),
                    Constants::PREFIXO_ARQ_JULGAMENTO_RECURSO
                );

                $arquivoJulgamento->setNomeFisico($nomeFisico);

                $arquivoJulgamento->setJulgamentoRecursoDenuncia($julgamentoRecursoDenuncia);
                $arquivoSalvo = $this->getArquivoJulgamentoRecursoDenunciaRepository()->persist($arquivoJulgamento);

                $arquivosSalvos->add($arquivoSalvo);
                $arquivoJulgamento->setArquivo($arquivoJulgamento->getArquivo());
                $this->salvaArquivosDiretorio($arquivoJulgamento, $julgamentoRecursoDenuncia);
                $arquivoSalvo->setArquivo(null);
            }
        }
    }

    /**
     * Método auxiliar para Salvar dados dos Arquivos para a Julgamento
     * Denuncia
     *
     * @param                                                                $arquivosJulgamento
     * @param JulgamentoRecursoDenuncia|RetificacaoJulgamentoRecursoDenuncia $julgamentoRecursoDenuncia
     * @param array                                                          $idsArquivosExcluidos
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    private function salvarArquivosRetificacaoJulgamentoRecursoDenuncia(
        $arquivosJulgamento,
        $julgamentoRecursoDenuncia,
        $idsArquivosExcluidos = []
    ) {
        $arquivosJulgamentoAnterior = $this->getArquivoJulgamentoRecursoDenunciaRepository()->findBy([
            'julgamentoRecursoDenuncia' => $julgamentoRecursoDenuncia->getJulgamentoRetificado()->getId()
        ]);

        if (!empty($arquivosJulgamentoAnterior)) {
            /** @var ArquivoJulgamentoRecursoDenuncia $arquivoJulgamentoAnterior */
            foreach ($arquivosJulgamentoAnterior as $arquivoJulgamentoAnterior) {
                $idArquivoExistente = $arquivoJulgamentoAnterior->getId();

                $arquivoExcluido = array_filter($idsArquivosExcluidos,
                    static function(int $idArquivoExcluido) use ($idArquivoExistente) {
                        return $idArquivoExcluido === $idArquivoExistente;
                    });

                if (empty($arquivoExcluido)) {
                    $arquivoJulgamentoDenuncia = ArquivoJulgamentoRecursoDenuncia::newInstance([
                        'nome' => $arquivoJulgamentoAnterior->getNome(),
                        'nomeFisico' => $arquivoJulgamentoAnterior->getNomeFisico(),
                    ]);

                    $arquivoJulgamentoDenuncia->setJulgamentoRecursoDenuncia($julgamentoRecursoDenuncia);
                    $this->getArquivoJulgamentoRecursoDenunciaRepository()->persist($arquivoJulgamentoDenuncia);

                    $caminhoOrigem = $this->getArquivoService()->getCaminhoRepositorioRecursoDenuncia(
                        $arquivoJulgamentoAnterior->getJulgamentoRecursoDenuncia()->getId()
                    );

                    $caminhoDestino = $this->getArquivoService()->getCaminhoRepositorioRecursoDenuncia(
                        $julgamentoRecursoDenuncia->getId()
                    );

                    $this->getArquivoService()->copiar(
                        $caminhoOrigem,
                        $caminhoDestino,
                        $arquivoJulgamentoAnterior->getNomeFisico(),
                        $arquivoJulgamentoDenuncia->getNomeFisico()
                    );
                }
            }
        }

        $this->salvarArquivosJulgamentoDenuncia($arquivosJulgamento, $julgamentoRecursoDenuncia);
    }

    /**
     * Enviar email ao realizar o cadastro do julgamento de denúncia em segunda instância.
     *
     * @param int $idJulgamentoDenuncia
     * @throws \Exception
     */
    public function enviarEmailCadastroJulgamentoDenunciaSegundaInstancia(int $idJulgamentoDenuncia)
    {
        /** @var JulgamentoRecursoDenuncia $julgamentoDenuncia */
        $julgamentoDenuncia = $this->julgamentoRecursoRepository->find($idJulgamentoDenuncia);

        return $this->enviarEmailPorTipos([
            Constants::EMAIL_INFORMATIVO_DENUNCIANTE_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA,
            Constants::EMAIL_INFORMATIVO_DENUNCIADO_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA,
            Constants::EMAIL_INFORMATIVO_MEMBROS_COMISSAO_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA,
            Constants::EMAIL_INFORMATIVO_COORDENADORES_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA,
            Constants::EMAIL_INFORMATIVO_ASSESSORES_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA
        ], $julgamentoDenuncia, null);
    }

    /**
     * Enviar email ao finalizar o prazo.
     *
     * @param int $idDenuncia
     * @throws \Exception
     */
    public function enviarEmailPrazoEncerrado(int $idDenuncia)
    {
        $denuncia = $this->denunciaRepository->find($idDenuncia);

        return $this->enviarEmailPorTipos([
            Constants::EMAIL_INFORMATIVO_ASSESSORES_CEN_SOBRE_TEMPO_DENUNCIA_AGUARDANDO_SEGUNDA_INSTANCIA
        ], null, $denuncia);
    }

    /**
     * Enviar email Por tipos de e-mail.
     *
     * @param array $tipos
     * @param $julgamentoRecursoDenuncia
     * @param null $denuncia
     * @throws Exception
     */
    public function enviarEmailPorTipos(array $tipos, $julgamentoRecursoDenuncia, $denuncia = null)
    {
        if (!is_null($julgamentoRecursoDenuncia)) {
            $denuncia = $this->getDenunciaJulgamentoRecurso($julgamentoRecursoDenuncia);
        }

        $atividade = $this->getAtividadeSecundariaBO()->getPorAtividadeSecundaria(
            $denuncia->getAtividadeSecundaria()->getId(),
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_JULGAMENTO_DENUNCIA,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_DENUNCIA_JULGAMENTO_SEGUNDA_INSTANCIA
        );

        $emailJulgamentoDenunciaTO = $this->getDadosEmailJulgamentoDenuncia(
            $denuncia, $julgamentoRecursoDenuncia, $atividade
        );

        foreach ($tipos as $tipo) {
            $destinarios = $this->getDestinatariosEmailJulgamentoSegundaInstancia($denuncia, $tipo);

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(),
                $tipo
            );

            if (!empty($emailAtividadeSecundaria) && !empty($destinarios)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios($destinarios);
                Email::enviarMail(new JulgamentoRecursoDenunciaMail($emailTO, $emailJulgamentoDenunciaTO));
            }
        }
    }

    /**
     * Retorna o objeto denuncia do julgamento.
     *
     * @param JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia
     * @return Denuncia
     * @throws \Exception
     */
    public function getDenunciaJulgamentoRecurso(JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia)
    {
        if (!empty($julgamentoRecursoDenuncia->getRecursoDenunciante())) {
            $denuncia = $julgamentoRecursoDenuncia->getRecursoDenunciante()->getDenuncia();
        }

        if (!empty($julgamentoRecursoDenuncia->getRecursoDenunciado())) {
            $denuncia = $julgamentoRecursoDenuncia->getRecursoDenunciado()->getDenuncia();
        }

        return $denuncia;
    }


    /**
     * Retorna os emails dos destinatarios de acordo com o tipo de envio
     *
     * @param Denuncia $denuncia
     * @param int $tipo
     * @return array
     * @throws \Exception
     */
    public function getDestinatariosEmailJulgamentoSegundaInstancia(Denuncia $denuncia, int $tipo)
    {
        $destinatarios = null;

        if ($tipo == Constants::EMAIL_INFORMATIVO_DENUNCIANTE_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA) {
            $destinatarios = [$denuncia->getPessoa()->getEmail()];
        }

        if ($tipo == Constants::EMAIL_INFORMATIVO_DENUNCIADO_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA) {
            $destinatarios = $this->getEncaminhamentoDenunciaBO()->getEmailsDenunciadoPorTipoDenuncia($denuncia);
        }

        $filial = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::ID_CAU_BR;

        if ($tipo == Constants::EMAIL_INFORMATIVO_MEMBROS_COMISSAO_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA) {
            $destinatarios = $this->getMembroComissaoBO()->getEmailsMembrosComissaoPorIdAtividaSecundariaCauUf(
                $denuncia->getAtividadeSecundaria()->getId(),
                $filial
            );
        }

        if ($tipo == Constants::EMAIL_INFORMATIVO_COORDENADORES_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA) {
            $destinatarios = $this->getMembroComissaoBO()->getEmailsCoordenadoresPorIdAtividaSecundaria(
                $denuncia->getAtividadeSecundaria()->getId(),
                $filial
            );
        }

        if ($tipo == Constants::EMAIL_INFORMATIVO_ASSESSORES_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA) {
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE($filial);
        }

        if ($tipo == Constants::EMAIL_INFORMATIVO_ASSESSORES_CEN_SOBRE_TEMPO_DENUNCIA_AGUARDANDO_SEGUNDA_INSTANCIA) {
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(Constants::ID_CAU_BR);
        }

        return $destinatarios;
    }

    /**
     * Retorna os emails dos destinatarios de acordo com o tipo de envio
     *
     * @param $denuncia
     * @param $julgamentoRecursoDenuncia
     * @param $atividade
     * @return EmailDenunciaTO
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDadosEmailJulgamentoDenuncia($denuncia, $julgamentoRecursoDenuncia, $atividade)
    {
        $emailRecursoJulgamentoTO = EmailDenunciaTO::newInstanceFromEntity($denuncia);
        $emailRecursoJulgamentoTO->setProcessoEleitoral(
            $atividade->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada()
        );
        $emailRecursoJulgamentoTO->setStatusDenuncia(
            $this->getDenunciaBO()->getNomeSituacaoAtualDenuncia($denuncia->getId())
        );

        if (!empty($julgamentoRecursoDenuncia)) {
            $emailRecursoJulgamentoTO->setJulgamentoRecursoDenuncia(JulgamentoRecursoDenunciaTO::newInstanceFromEntity($julgamentoRecursoDenuncia));
        }

        return $emailRecursoJulgamentoTO;
    }

    /**
     * Método para enviar e-mail após 10 dias.
     *
     * @return CorporativoService
     */
    public function envioEmailPrazoEncerrado()
    {
        $filtroTO = new \stdClass();
        $filtroTO->idSituacao = Constants::STATUS_DENUNCIA_JULG_SEGUNDA_INSTANCIA;

        $dununcias = $this->denunciaRepository->getDenunciasPorFiltro($filtroTO);

        if (!empty($dununcias)) {
            /** @var Denuncia $denuncia */
            foreach ($dununcias as $denuncia) {
                $denunciaSituacao = $denuncia->getDenunciaSituacao()->first();

                $ano = Utils::getAnoData($denunciaSituacao->getData());
                $feriados = $this->getCalendarioApiService()
                    ->getDatasFeriadosNacionais($ano);

                $dataLimite = Utils::adicionarDiasUteisData(
                    $denunciaSituacao->getData(),
                    Constants::PRAZO_JULGAMENTO_RECURSO_DENUNCIA_DIAS,
                    $feriados
                );

                if (Utils::getData() > $dataLimite) {
                    Utils::executarJOB(new EnviarEmailJulgamentoRecursoDenunciaPrazoEncerradoJob($denuncia->getId()));
                }
            }
        }
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoRecursoJulgamento($id)
    {
        $arquivoJulgamento = $this->getArquivoRecJulgamento($id);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioRecursoDenuncia($arquivoJulgamento
            ->getJulgamentoRecursoDenuncia()->getId());
        return $this->getArquivoService()->getArquivo($caminho, $arquivoJulgamento->getNomeFisico(), $arquivoJulgamento->getNome());
    }

    /**
     * Recupera a entidade 'ArquivoRecursoJulgamento' por meio do 'id'
     * informado.
     *
     * @param $id
     * @return \App\Entities\ArquivoJulgamentoRecursoDenuncia|null
     */
    private function getArquivoRecJulgamento($id)
    {
        return current($this->getArquivoJulgamentoRecursoDenunciaRepository()->getPorId($id));
    }

    /**
     * Retorna uma nova instância de 'ArquivoJulgamentoRecursoDenunciaRepository'.
     *
     * @return ArquivoJulgamentoRecursoDenunciaRepository
     */
    private function getArquivoJulgamentoRecursoDenunciaRepository()
    {
        if (empty($this->arquivoJulgamentoRecursoRepository)) {
            $this->arquivoJulgamentoRecursoRepository = $this->getRepository(ArquivoJulgamentoRecursoDenuncia::class);
        }
        return $this->arquivoJulgamentoRecursoRepository;
    }

    /**
     * Retorna uma nova instância de 'RetificacaoJulgamentoRecurso'.
     *
     * @return RetificacaoJulgamentoRecursoDenunciaRepository
     */
    private function getRetificacaoJulgamentoRecursoRepository()
    {
        if (empty($this->retificacaoJulgamentoRecursoRepository)) {
            $this->retificacaoJulgamentoRecursoRepository = $this->getRepository(RetificacaoJulgamentoRecursoDenuncia::class);
        }

        return $this->retificacaoJulgamentoRecursoRepository;
    }

    /**
     * Método para retornar a instancia de Historico Denuncia BO
     *
     * @return HistoricoDenunciaBO
     */
    private function getHistoricoDenunciaBO()
    {
        if (empty($this->historicoDenunciaBO)) {
            $this->historicoDenunciaBO = new HistoricoDenunciaBO();
        }
        return $this->historicoDenunciaBO;
    }

    /**
     * Método para retornar a instancia de Status Recurso DenunciaBO
     *
     * @return StatusRecursoDenunciaBO
     */
    private function getStatusRecursoDenunciaBO()
    {
        if (empty($this->statusRecursoDenunciaBO)) {
            $this->statusRecursoDenunciaBO = new StatusRecursoDenunciaBO();
        }
        return $this->statusRecursoDenunciaBO;
    }

    /**
     * Método para retornar a instancia de DenunciaBO
     *
     * @return DenunciaBO
     */
    private function getDenunciaBO()
    {
        if (empty($this->denunciaBO)) {
            $this->denunciaBO = new DenunciaBO();
        }
        return $this->denunciaBO;
    }

    /**
     * Retorna uma instancia de Arquivo Service
     *
     * @return ArquivoService
     * @var ArquivoService
     */
    private function getArquivoService()
    {
        if (empty($this->arquivoService)) {
            $this->arquivoService = new ArquivoService();
        }
        return $this->arquivoService;
    }

    /**
     * Método para retornar a instancia de 'MembroComissaoBO'.
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
     * Método para retornar a instância de 'AtividadeSecundariaCalendarioBO'
     *
     * @return AtividadeSecundariaCalendarioBO
     */
    private function getAtividadeSecundariaBO(): AtividadeSecundariaCalendarioBO
    {
        if (empty($this->atividadeSecundariaCalendarioBO)) {
            $this->atividadeSecundariaCalendarioBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }
        return $this->atividadeSecundariaCalendarioBO;
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
     * Método para retornar a instância de 'EncaminhamentoDenunciaBO'
     *
     * @return EncaminhamentoDenunciaBO
     */
    private function getEncaminhamentoDenunciaBO(): EncaminhamentoDenunciaBO
    {
        if (empty($this->encaminhamentoDenunciaBO)) {
            $this->encaminhamentoDenunciaBO = app()->make(EncaminhamentoDenunciaBO::class);
        }
        return $this->encaminhamentoDenunciaBO;
    }

    /**
     * Método para retornar a instancia de 'CorporativoService'
     *
     * @return CorporativoService
     */
    private function getCorporativoService()
    {
        if (empty($this->corporativoService)) {
            $this->corporativoService = app()->make(CorporativoService::class);
        }
        return $this->corporativoService;
    }

    /**
     * Método que retorna todos os recurso que foram retificados
     *
     * @param $idRetificacao
     * @return \App\Entities\RetificacaoJulgamentoRecursoDenuncia[]|array
     */
    public function getAllRecursoRetificadoPorId($idRetificacao)
    {
        $recJulgamento = $this->getRetificacaoJulgamentoRecursoRepository()->getRecRetificacaoJulgamento($idRetificacao);

        return array_map(static function($rJulgamento){
            return JulgamentoRecursoDenunciaTO::newInstanceFromEntity($rJulgamento);
        }, $recJulgamento);
    }

    /**
     * Método que retorna o recurso que foi retificado
     *
     * @param $idRetificado
     * @return JulgamentoRecursoDenunciaTO
     */
    public function getRecursoRetificadoPorId($idRetificado)
    {
        $recJulgamento = $this->getRetificacaoJulgamentoRecursoRepository()->getRetificacaoPorId($idRetificado);
        return JulgamentoRecursoDenunciaTO::newInstanceFromEntity($recJulgamento);
    }

    /**
     * Retorna uma nova instância de 'ArquivoRecursoJulgamento'.
     *
     * @return ArquivoJulgamentoRecursoDenunciaRepository
     */
    private function getArquivoJulgamentoDenunciaRepository()
    {
        if (empty($this->arquivoJulgamentoRecursoRepository)) {
            $this->arquivoJulgamentoRecursoRepository = $this->getRepository(ArquivoJulgamentoRecursoDenunciaRepository::class);
        }

        return $this->arquivoJulgamentoRecursoRepository;
    }

    /**
     * Retorna uma instancia de Calendario Api Service
     *
     * @return CalendarioApiService
     * @var \App\Service\CalendarioApiService
     */
    private function getCalendarioApiService()
    {
        if (empty($this->calendarioApiService)) {
            $this->calendarioApiService = new CalendarioApiService();
        }
        return $this->calendarioApiService;
    }

}
