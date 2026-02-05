<?php
/*
 * JulgamentoDenunciaBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoJulgamentoDenuncia;
use App\Entities\Denuncia;
use App\Entities\JulgamentoDenuncia;
use App\Entities\JulgamentoImpugnacao;
use App\Entities\Pessoa;
use App\Entities\RetificacaoJulgamentoDenuncia;
use App\Entities\TipoJulgamento;
use App\Entities\TipoSentencaJulgamento;
use App\Entities\Usuario;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailJulgamentoDenunciaJob;
use App\Mail\JulgamentoDenunciaMail;
use App\Repository\ArquivoJulgamentoDenunciaRepository;
use App\Repository\JulgamentoDenunciaRepository;
use App\Repository\RetificacaoJulgamentoDenunciaRepository;
use App\Repository\TipoJulgamentoRepository;
use App\Repository\TipoSentencaJulgamentoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\EmailDenunciaTO;
use App\To\JulgamentoDenunciaTO;
use App\To\RetificacaoJulgamentoDenunciaTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Facades\Lang;
use function foo\func;

/**
 * Classe responsável por encapsular as implementações de negócio referente a
 * entidade 'JulgamentoDenuncia'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoDenunciaBO extends AbstractBO
{

    /**
     * @var DenunciaBO
     */
    private $denunciaBO;

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var HistoricoDenunciaBO
     */
    private $historicoDenunciaBO;

    /**
     * @var TipoJulgamentoRepository
     */
    private $tipoJulgamentoRepository;

    /**
     * @var EncaminhamentoDenunciaBO
     */
    private $encaminhamentoDenunciaBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var JulgamentoDenunciaRepository
     */
    private $julgamentoDenunciaRepository;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var TipoSentencaJulgamentoRepository
     */
    private $tipoSentencaJulgamentoRepository;

    /**
     * @var ArquivoJulgamentoDenunciaRepository
     */
    private $arquivoJulgamentoDenunciaRepository;

    /**
     * @var RetificacaoJulgamentoDenunciaRepository
     */
    private $retificacaoJulgamentoDenunciaRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Retorna um array com todos os tipos de julgamento ordenados por Id.
     *
     * @param array $filtro
     * @return array
     */
    public function getTiposJulgamento($filtro = [])
    {
        return $this->getTipoJulgamentoRepository()->findBy($filtro, ['id' => 'ASC']);
    }

    /**
     * Retorna um array com todos os tipos de sentença do julgamento ordenados
     * por Id.
     *
     * @param array $filtro
     * @return array
     */
    public function getTiposSentencaJulgamento($filtro = [])
    {
        return $this->getTipoSentencaJulgamentoRepository()->findBy($filtro, ['id' => 'ASC']);
    }

    /**
     * Retorna os dados da retificação do julgamento de denuncia de acordo com 'id'.
     *
     * @param $idRetificacao
     *
     * @return RetificacaoJulgamentoDenunciaTO|null
     * @throws \Exception
     */
    public function getRetificacaoPorId($idRetificacao)
    {
        $retificacaoJulgamento = $this->getRetificacaoJulgamentoDenunciaRepository()
            ->getRetificacaoPorId($idRetificacao);

        return null !== $retificacaoJulgamento
            ? RetificacaoJulgamentoDenunciaTO::newInstanceFromEntity($retificacaoJulgamento)
            : null;
    }

    /**
     * Retorna um array com todas as retificações do julgamento de denuncia ordenados
     * por data.
     *
     * @param $idDenuncia
     *
     * @return RetificacaoJulgamentoDenunciaTO[]|array
     * @throws \Exception
     */
    public function getRetificacoesJulgamento($idDenuncia)
    {
        $retificacoesJulgamento = $this->getRetificacaoJulgamentoDenunciaRepository()
            ->getRetificacoesPorIdDenuncia($idDenuncia);

        if (!empty($retificacoesJulgamento)) {
            $retificacoesJulgamento = array_map(static function(RetificacaoJulgamentoDenuncia $retificacaoJulgamento) {
                return RetificacaoJulgamentoDenunciaTO::newInstanceFromEntity($retificacaoJulgamento);
            }, $retificacoesJulgamento);
        }

        return $retificacoesJulgamento;
    }

    /**
     * Salva o julgamento da denuncia.
     *
     * @param JulgamentoDenunciaTO $julgamentoDenunciaTO
     *
     * @return \stdClass
     * @throws \App\Exceptions\NegocioException
     */
    public function salvar(JulgamentoDenunciaTO $julgamentoDenunciaTO)
    {
        $this->validarCamposObrigatorios($julgamentoDenunciaTO);
        $this->validarQuantidadeArquivosJulgamento($julgamentoDenunciaTO);

        $julgamentoDenunciaExistente = $this->validaExistenciaUnicaJulgamentoPorIdDenuncia($julgamentoDenunciaTO);

        try {
            $this->beginTransaction();

            $denuncia = $this->getDenunciaBO()->getDenuncia($julgamentoDenunciaTO->getIdDenuncia());

            $usuarioFactory = $this->getUsuarioFactory();
            $filial = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::ID_CAU_BR;
            if ($usuarioFactory && !$usuarioFactory->isCorporativoAssessorCEN()
                && (!$usuarioFactory->isCorporativoAssessorCeUfPorCauUf($filial) || $julgamentoDenunciaTO->isRetificacao())
            ) {
                throw new NegocioException(Lang::get('messages.denuncia.julgamento.assessor_ce_outra_uf'));
            }

            $acaoHistoricoJulgamento = Constants::ACAO_HISTORICO_RETIFICACAO_JULGAMENTO_DENUNCIA;
            if ($julgamentoDenunciaTO->isRetificacao()) {
                if ($julgamentoDenunciaExistente === null) {
                    throw new NegocioException(Lang::get('messages.denuncia.julgamento.nao_existe_julgamento_para_retificar'));
                }

                if ($denuncia->getDenunciaSituacao()->last()->getSituacaoDenuncia()->getId() === Constants::STATUS_DENUNCIA_JULG_SEGUNDA_INSTANCIA) {
                    throw new NegocioException(Lang::get('messages.denuncia.julgamento.nao_possivel_retificar_julgamento'));
                }

                $julgamentoDenuncia = $this->prepararRetificacaoJulgamentoDenunciaSalvar($julgamentoDenunciaTO);
                $julgamentoDenuncia->setDenuncia($denuncia);
                $julgamentoDenuncia->setJulgamentoRetificado($julgamentoDenunciaExistente);
                $julgamentoDenuncia->setJustificativa($julgamentoDenunciaTO->getJustificativa());
                $julgamentoDenuncia->setUsuario(Usuario::newInstance(['id' => $usuarioFactory->getUsuarioLogado()->id]));

                $julgamentoDenunciaSalvo = $this->getRetificacaoJulgamentoDenunciaRepository()->persist($julgamentoDenuncia);

                $arquivos = $julgamentoDenunciaTO->getArquivosJulgamentoDenuncia();
                if (!empty($arquivos)) {
                    $this->salvarArquivosRetificacaoJulgamentoDenuncia(
                        $arquivos,
                        $julgamentoDenunciaSalvo,
                        $julgamentoDenunciaTO->getIdsArquivosExcluidos()
                    );
                }
            }

            if (!$julgamentoDenunciaTO->isRetificacao()) {
                $acaoHistoricoJulgamento = Constants::ACAO_HISTORICO_JULGAMENTO_DENUNCIA;

                $julgamentoDenuncia = $this->prepararJulgamentoDenunciaSalvar($julgamentoDenunciaTO);
                $julgamentoDenuncia->setDenuncia($denuncia);
                $julgamentoDenuncia->setUsuario(Usuario::newInstance(['id' => $usuarioFactory->getUsuarioLogado()->id]));

                $julgamentoDenunciaSalvo = $this->getJulgamentoDenunciaRepository()->persist($julgamentoDenuncia);

                $denuncia->setJulgamentoDenuncia($julgamentoDenunciaSalvo);

                $this->getDenunciaBO()->alterarStatusSituacaoDenuncia(
                    $denuncia,
                    Constants::STATUS_DENUNCIA_JULG_PRIMEIRA_INSTANCIA
                );

                $arquivos = $julgamentoDenunciaTO->getArquivosJulgamentoDenuncia();
                if (!empty($arquivos)) {
                    $this->salvarArquivosJulgamentoDenuncia($arquivos, $julgamentoDenunciaSalvo);
                }
            }

            $historicoDenunciaJulgamento = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
                $acaoHistoricoJulgamento);
            $this->getHistoricoDenunciaBO()->salvar($historicoDenunciaJulgamento);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if (!empty($julgamentoDenunciaSalvo)) {
            Utils::executarJOB(new EnviarEmailJulgamentoDenunciaJob($julgamentoDenunciaSalvo->getId()));
        }

        return $this->getRetornoSalvar($julgamentoDenunciaSalvo);
    }

    /**
     * Prepara e retorna a entidade de julgamento de denuncia para ser
     * persistida.
     *
     * @param \App\To\JulgamentoDenunciaTO $julgamentoDenunciaTO
     *
     * @return \App\Entities\JulgamentoDenuncia
     * @throws \Exception
     */
    private function prepararJulgamentoDenunciaSalvar(JulgamentoDenunciaTO $julgamentoDenunciaTO)
    {
        $idTipoJulgamento = $julgamentoDenunciaTO->getIdTipoJulgamento();
        $idTipoSentencaJulgamento = $julgamentoDenunciaTO->getIdTipoSentencaJulgamento();

        $julgamentoDenuncia = JulgamentoDenuncia::newInstance([
            'data'      => Utils::getData(),
            'descricao' => $julgamentoDenunciaTO->getDescricaoJulgamento(),
            'nivel'     => Constants::NIVEL_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA,
        ]);

        $julgamentoDenuncia->setTipoJulgamento(TipoJulgamento::newInstance(
            ['id' => $idTipoJulgamento]
        ));

        if (Constants::TIPO_JULGAMENTO_PROCEDENTE == $idTipoJulgamento) {
            $julgamentoDenuncia->setTipoSentencaJulgamento(TipoSentencaJulgamento::newInstance(
                ['id' => $idTipoSentencaJulgamento]
            ));
        }

        if (Constants::TIPO_SENTENCA_JULGAMENTO_SUSP_PROPAGANDA === $idTipoSentencaJulgamento) {
            $julgamentoDenuncia->setQuantidadeDiasSuspensaoPropaganda(
                $julgamentoDenunciaTO->getQuantidadeDiasSuspensaoPropaganda()
            );
        }

        if (Constants::TIPO_SENTENCA_JULGAMENTO_MULTA !== $idTipoSentencaJulgamento) {
            $julgamentoDenuncia->setMulta($julgamentoDenunciaTO->isMulta());
        }

        if ($julgamentoDenunciaTO->isMulta()
            || Constants::TIPO_SENTENCA_JULGAMENTO_MULTA === $idTipoSentencaJulgamento
        ) {
            $julgamentoDenuncia->setMulta(true);
            $julgamentoDenuncia->setValorPercentualMulta(
                $julgamentoDenunciaTO->getValorPercentualMulta()
            );
        }

        return $julgamentoDenuncia;
    }

    /**
     * Prepara e retorna a entidade de retificação do julgamento de denuncia
     * para ser persistida.
     *
     * @param \App\To\JulgamentoDenunciaTO $julgamentoDenunciaTO
     *
     * @return \App\Entities\RetificacaoJulgamentoDenuncia
     * @throws \Exception
     */
    private function prepararRetificacaoJulgamentoDenunciaSalvar(JulgamentoDenunciaTO $julgamentoDenunciaTO)
    {
        $idTipoJulgamento = $julgamentoDenunciaTO->getIdTipoJulgamento();
        $idTipoSentencaJulgamento = $julgamentoDenunciaTO->getIdTipoSentencaJulgamento();

        $retificacaoJulgamentoDenuncia = RetificacaoJulgamentoDenuncia::newInstance([
            'data'      => Utils::getData(),
            'descricao' => $julgamentoDenunciaTO->getDescricaoJulgamento(),
            'nivel'     => Constants::NIVEL_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA,
        ]);

        $retificacaoJulgamentoDenuncia->setTipoJulgamento(TipoJulgamento::newInstance(
            ['id' => $idTipoJulgamento]
        ));

        if (Constants::TIPO_JULGAMENTO_PROCEDENTE == $idTipoJulgamento) {
            $retificacaoJulgamentoDenuncia->setTipoSentencaJulgamento(TipoSentencaJulgamento::newInstance(
                ['id' => $idTipoSentencaJulgamento]
            ));
        }

        if (Constants::TIPO_SENTENCA_JULGAMENTO_SUSP_PROPAGANDA === $idTipoSentencaJulgamento) {
            $retificacaoJulgamentoDenuncia->setQuantidadeDiasSuspensaoPropaganda(
                $julgamentoDenunciaTO->getQuantidadeDiasSuspensaoPropaganda()
            );
        }

        if (Constants::TIPO_SENTENCA_JULGAMENTO_MULTA !== $idTipoSentencaJulgamento) {
            $retificacaoJulgamentoDenuncia->setMulta($julgamentoDenunciaTO->isMulta());
        }

        if ($julgamentoDenunciaTO->isMulta()
            || Constants::TIPO_SENTENCA_JULGAMENTO_MULTA === $idTipoSentencaJulgamento
        ) {
            $retificacaoJulgamentoDenuncia->setMulta(true);
            $retificacaoJulgamentoDenuncia->setValorPercentualMulta(
                $julgamentoDenunciaTO->getValorPercentualMulta()
            );
        }

        return $retificacaoJulgamentoDenuncia;
    }

    /**
     * Método auxiliar para Salvar dados dos Arquivos para a Julgamento
     * Denuncia
     *
     * @param                    $arquivosJulgamento
     * @param JulgamentoDenuncia|RetificacaoJulgamentoDenuncia $julgamentoDenuncia
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    private function salvarArquivosJulgamentoDenuncia($arquivosJulgamento, $julgamentoDenuncia)
    {
        $arquivosSalvos = new ArrayCollection();

        /** @var ArquivoGenericoTO $arquivoJulgamento */
        foreach ($arquivosJulgamento as $arquivoJulgamento) {
            if (!$arquivoJulgamento->getId()) {
                $nomeFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                    $arquivoJulgamento->getNome(), Constants::PREFIXO_ARQ_JULGAMENTO_DENUNCIA
                );

                $arquivoJulgamentoDenuncia = ArquivoJulgamentoDenuncia::newInstance([
                    'nome' => $arquivoJulgamento->getNome(), 'nomeFisico' => $nomeFisico,
                ]);

                $arquivoJulgamentoDenuncia->setJulgamentoDenuncia($julgamentoDenuncia);
                $arquivoSalvo = $this->getArquivoJulgamentoDenunciaRepository()->persist($arquivoJulgamentoDenuncia);

                $arquivosSalvos->add($arquivoSalvo);
                $arquivoJulgamentoDenuncia->setArquivo($arquivoJulgamento->getArquivo());
                $this->salvaArquivosDiretorio($arquivoJulgamentoDenuncia, $julgamentoDenuncia);
                $arquivoSalvo->setArquivo(null);
            }
        }

        if ($arquivosSalvos->count() > 0) {
            $julgamentoDenuncia->setArquivosJulgamentoDenuncia($arquivosSalvos);
        }

        $julgamentoDenuncia->removerFiles();
    }


    /**
     * Método auxiliar para Salvar dados dos Arquivos para a Julgamento
     * Denuncia
     *
     * @param                                                  $arquivosJulgamento
     * @param JulgamentoDenuncia|RetificacaoJulgamentoDenuncia $julgamentoDenuncia
     * @param array                                            $idsArquivosExcluidos
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    private function salvarArquivosRetificacaoJulgamentoDenuncia(
        $arquivosJulgamento,
        $julgamentoDenuncia,
        $idsArquivosExcluidos = []
    ) {
        $arquivosJulgamentoAnterior = $this->getArquivoJulgamentoDenunciaRepository()->findBy([
            'julgamentoDenuncia' => $julgamentoDenuncia->getJulgamentoRetificado()->getId(),
        ]);

        if (!empty($arquivosJulgamentoAnterior)) {
            /** @var ArquivoJulgamentoDenuncia $arquivoJulgamentoAnterior */
            foreach ($arquivosJulgamentoAnterior as $arquivoJulgamentoAnterior) {
                $idArquivoExistente = $arquivoJulgamentoAnterior->getId();

                $arquivoExcluido = array_filter($idsArquivosExcluidos,
                    static function(int $idArquivoExcluido) use ($idArquivoExistente) {
                        return $idArquivoExcluido === $idArquivoExistente;
                    });

                if (empty($arquivoExcluido)) {
                    $arquivoJulgamentoDenuncia = ArquivoJulgamentoDenuncia::newInstance([
                        'nome' => $arquivoJulgamentoAnterior->getNome(),
                        'nomeFisico' => $arquivoJulgamentoAnterior->getNomeFisico(),
                    ]);

                    $arquivoJulgamentoDenuncia->setJulgamentoDenuncia($julgamentoDenuncia);
                    $this->getArquivoJulgamentoDenunciaRepository()->persist($arquivoJulgamentoDenuncia);

                    $caminhoOrigem = $this->getArquivoService()->getCaminhoRepositorioJulgamentoDenuncia(
                        $arquivoJulgamentoAnterior->getJulgamentoDenuncia()->getId()
                    );

                    $caminhoDestino = $this->getArquivoService()->getCaminhoRepositorioJulgamentoDenuncia(
                        $julgamentoDenuncia->getId()
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

        $this->salvarArquivosJulgamentoDenuncia($arquivosJulgamento, $julgamentoDenuncia);
    }

    /**
     * Realiza o(s) upload(s) do(s) arquivo(s) da Julgamento Denuncia
     *
     * @param \App\Entities\ArquivoJulgamentoDenuncia $arquivo
     * @param JulgamentoDenuncia|RetificacaoJulgamentoDenuncia $julgamentoDenunciaSalvo
     */
    private function salvaArquivosDiretorio(ArquivoJulgamentoDenuncia $arquivo, $julgamentoDenunciaSalvo)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoDenuncia($julgamentoDenunciaSalvo->getId());

        if ($arquivo !== null) {
            if (!empty($arquivo->getArquivo())) {
                $this->getArquivoService()->salvar($caminho, $arquivo->getNomeFisico(), $arquivo->getArquivo());
            }
        }
    }

    /**
     * Verifica se os campos obrigatórios foram preenchidos.
     *
     * @param \App\To\JulgamentoDenunciaTO $julgamentoDenunciaTO
     */
    private function validarCamposObrigatorios(JulgamentoDenunciaTO $julgamentoDenunciaTO)
    {
        $camposObrigatorios = [
            $julgamentoDenunciaTO->getDescricaoJulgamento(),
        ];

        if (Constants::TIPO_JULGAMENTO_PROCEDENTE === $julgamentoDenunciaTO->getIdTipoJulgamento()) {
            $idTipoSentencaJulgamento = $julgamentoDenunciaTO->getIdTipoSentencaJulgamento();

            $camposObrigatorios[] = $idTipoSentencaJulgamento;
            if ($idTipoSentencaJulgamento !== null) {
                if (Constants::TIPO_SENTENCA_JULGAMENTO_SUSP_PROPAGANDA === $idTipoSentencaJulgamento) {
                    $camposObrigatorios[] = $julgamentoDenunciaTO->getQuantidadeDiasSuspensaoPropaganda();
                }

                if (Constants::TIPO_SENTENCA_JULGAMENTO_ADVERTENCIA === $idTipoSentencaJulgamento) {
                    $camposObrigatorios[] = $julgamentoDenunciaTO->isMulta();
                }

                if ($julgamentoDenunciaTO->isMulta()
                    || Constants::TIPO_SENTENCA_JULGAMENTO_MULTA === $idTipoSentencaJulgamento
                ) {
                    $camposObrigatorios[] = $julgamentoDenunciaTO->getValorPercentualMulta();
                }
            }
        }

        if ($julgamentoDenunciaTO->isRetificacao()) {
            $camposObrigatorios[] = $julgamentoDenunciaTO->getJustificativa();
        }

        array_walk($camposObrigatorios, static function ($campoObrigatorio) {
            if ($campoObrigatorio === null) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
            }
        });
    }

    /**
     * Valida a quantidade de arquivos para o Julgamento Denuncia
     *
     * @param JulgamentoDenunciaTO $julgamentoDenunciaTO
     * @throws NegocioException
     */
    private function validarQuantidadeArquivosJulgamento(JulgamentoDenunciaTO $julgamentoDenunciaTO)
    {
        $arquivos = $julgamentoDenunciaTO->getArquivosJulgamentoDenuncia();
        if (!empty($arquivos)
            && count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO_JULG_DENUNCIA
        ) {
            throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO);
        }
    }

    /**
     * Valida se já existe julgamento em primeira instância para a denuncia
     * informada.
     *
     * @param \App\To\JulgamentoDenunciaTO $julgamentoDenunciaTO
     *
     * @return JulgamentoDenuncia|object|null
     * @throws \App\Exceptions\NegocioException
     */
    private function validaExistenciaUnicaJulgamentoPorIdDenuncia(JulgamentoDenunciaTO $julgamentoDenunciaTO)
    {
        $idDenuncia = $julgamentoDenunciaTO->getIdDenuncia();
        $julgamentoDenuncia = $this->getJulgamentoDenunciaRepository()
            ->findOneBy(['denuncia' => $idDenuncia], ['id' => 'DESC']);

        if ($julgamentoDenuncia !== null && !$julgamentoDenunciaTO->isRetificacao()) {
            throw new NegocioException(Lang::get('messages.denuncia.julgamento.ja_inserido_primeira_instancia'));
        }

        return $julgamentoDenuncia;
    }

    /**
     * Cria um objeto para organizar o retorno de sucesso do método Salvar
     *
     * @param JulgamentoDenuncia|RetificacaoJulgamentoDenuncia $julgamentoDenuncia
     * @return \stdClass
     */
    private function getRetornoSalvar($julgamentoDenuncia)
    {
        $retorno = new \stdClass();
        $retorno->numeroSequencial = $julgamentoDenuncia->getDenuncia()->getNumeroSequencial();

        return $retorno;
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoJulgamentoDenuncia($id)
    {
        $arquivoJulgamento = $this->getArquivoJulgamento($id);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoDenuncia($arquivoJulgamento
            ->getJulgamentoDenuncia()->getId());
        return $this->getArquivoService()->getArquivo($caminho, $arquivoJulgamento->getNomeFisico(), $arquivoJulgamento->getNome());
    }

    /**
     * Enviar email ao realizar o cadastro do julgamento da denúncia
     *
     * @param int $idJulgamentoDenuncia
     * @throws \Exception
     */
    public function enviarEmailCadastroJulgamentoDenuncia(int $idJulgamentoDenuncia)
    {
        /** @var JulgamentoDenuncia $julgamentoDenuncia */
        $julgamentoDenuncia = $this->getJulgamentoDenunciaRepository()->find($idJulgamentoDenuncia);

        $atividade = $this->getAtividadeSecundariaBO()->getPorAtividadeSecundaria(
            $julgamentoDenuncia->getDenuncia()->getAtividadeSecundaria()->getId(),
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_JULGAMENTO_DENUNCIA,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_JULGAMENTO_DENUNCIA
        );

        $emailJulgamentoDenunciaTO = $this->getDadosEmailJulgamentoDenuncia($julgamentoDenuncia, $atividade);

        $tipos = Constants::$tiposEmailAtividadeSecundaria[Constants::NIVEL_ATIVIDADE_PRINCIPAL_JULGAMENTO_DENUNCIA]
        [Constants::NIVEL_ATIVIDADE_SECUNDARIA_JULGAMENTO_DENUNCIA];

        foreach ($tipos as $tipo) {
            $destinarios = $this->getDestinatariosEmail($julgamentoDenuncia->getDenuncia(), $tipo);

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(), $tipo
            );

            if (!empty($emailAtividadeSecundaria) && !empty($destinarios)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios($destinarios);
                Email::enviarMail(new JulgamentoDenunciaMail($emailTO, $emailJulgamentoDenunciaTO));
            }
        }
    }

    /**
     * Retorna os emails dos destinatarios de acordo com o tipo de envio
     *
     * @param JulgamentoDenuncia $julgamentoDenuncia
     * @param $atividade
     * @return EmailDenunciaTO
     * @throws \Exception
     */
    public function getDadosEmailJulgamentoDenuncia(JulgamentoDenuncia $julgamentoDenuncia, $atividade)
    {
        $emailParecerFinalTO = EmailDenunciaTO::newInstanceFromEntity($julgamentoDenuncia->getDenuncia());
        $emailParecerFinalTO->setProcessoEleitoral(
            $atividade->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada()
        );
        $emailParecerFinalTO->setStatusDenuncia(
            $this->getDenunciaBO()->getNomeSituacaoAtualDenuncia($julgamentoDenuncia->getDenuncia()->getId())
        );
        $emailParecerFinalTO->setJulgamentoDenuncia(JulgamentoDenunciaTO::newInstanceFromEntity($julgamentoDenuncia));

        return $emailParecerFinalTO;
    }

    /**
     * Retorna os emails dos destinatarios de acordo com o tipo de envio
     *
     * @param Denuncia $denuncia
     * @param int $tipo
     * @return array
     * @throws \Exception
     */
    public function getDestinatariosEmail(Denuncia $denuncia, int $tipo)
    {
        $destinatarios = null;

        if ($tipo == Constants::EMAIL_INFORMATIVO_DENUNCIANTE_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA) {
            $destinatarios = [$denuncia->getPessoa()->getEmail()];
        }

        if ($tipo == Constants::EMAIL_INFORMATIVO_DENUNCIADO_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA) {
            $destinatarios = $this->getEncaminhamentoDenunciaBO()->getEmailsDenunciadoPorTipoDenuncia($denuncia);
        }

        $filial = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::ID_CAU_BR;

        if ($tipo == Constants::EMAIL_INFORMATIVO_MEMBROS_COMISSAO_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA) {
            $destinatarios = $this->getMembroComissaoBO()->getEmailsMembrosComissaoPorIdAtividaSecundariaCauUf(
                $denuncia->getAtividadeSecundaria()->getId(), $filial
            );
        }

        if ($tipo == Constants::EMAIL_INFORMATIVO_COORDENADORES_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA) {
            $destinatarios = $this->getMembroComissaoBO()->getEmailsCoordenadoresPorIdAtividaSecundaria(
                $denuncia->getAtividadeSecundaria()->getId(), $filial
            );
        }

        if ($tipo == Constants::EMAIL_INFORMATIVO_ASSESSORES_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA) {
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE($filial);
        }

        return $destinatarios;
    }

    /**
     * Retorna as informações do julgamento para exportação em pdf
     *
     * @param JulgamentoDenuncia $julgamentoDenuncia
     * @return \stdClass
     * @throws \Exception
     */
    public function getExportarInformacoesJulgamento(JulgamentoDenuncia $julgamentoDenuncia)
    {
        $julgamentoDenunciaTO = JulgamentoDenunciaTO::newInstanceFromEntity($julgamentoDenuncia);

        $documentos = $this->getDenunciaBO()->getDescricaoArquivoExportar(
            $julgamentoDenuncia->getArquivosJulgamentoDenuncia(),
            $this->getArquivoService()->getCaminhoRepositorioJulgamentoDenuncia($julgamentoDenuncia->getId())
        );
        $julgamentoDenunciaTO->setDescricaoArquivo($documentos);

        $julgamento = new \stdClass();
        $julgamento->julgamento = $julgamentoDenunciaTO;
        $julgamento->retificacoes = $this->getExportarHistoricoRetificacaoJulgamento(
            $julgamentoDenuncia->getDenuncia()->getId()
        );

        return $julgamento;
    }

    /**
     * Retorna as informações de retificações do julgamento para exportação em pdf
     *
     * @param int $idDenuncia
     * @return \stdClass[]|null
     * @throws \Exception
     */
    public function getExportarHistoricoRetificacaoJulgamento(int $idDenuncia)
    {
        $listaHistoricos = null;

        $historicos = $this->getHistoricoDenunciaBO()->getTodosHistoricosDenunciaPorDenunciaEAcao(
            $idDenuncia,Constants::ACAO_HISTORICO_RETIFICACAO_JULGAMENTO_DENUNCIA
        );

        if (!empty($historicos)) {
            foreach ($historicos as $historico){
                $usuario = $this->getCorporativoService()->getUsuarioPorId($historico->getResponsavel());

                $listaHistoricos[] =
                $historicoRetificacao = new \stdClass();
                $historicoRetificacao->dataHora = $historico->getDataHistorico();
                $historicoRetificacao->usuario = $usuario->getNome();
                $listaHistoricos[] = $historicoRetificacao;
            }
        }

        return $listaHistoricos;
    }

    /**
     * Recupera a entidade 'ArquivoJulgamentoDenuncia' por meio do 'id'
     * informado.
     *
     * @param $id
     * @return \App\Entities\ArquivoJulgamentoDenuncia|null
     */
    private function getArquivoJulgamento($id)
    {
        return current($this->getArquivoJulgamentoDenunciaRepository()->getPorId($id));
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
     * Retorna uma nova instância de 'JulgamentoDenunciaRepository'.
     *
     * @return JulgamentoDenunciaRepository
     */
    private function getJulgamentoDenunciaRepository()
    {
        if (empty($this->julgamentoDenunciaRepository)) {
            $this->julgamentoDenunciaRepository = $this->getRepository(JulgamentoDenuncia::class);
        }

        return $this->julgamentoDenunciaRepository;
    }

    /**
     * Retorna uma nova instância de 'RetificacaoJulgamentoDenunciaRepository'.
     *
     * @return RetificacaoJulgamentoDenunciaRepository
     */
    private function getRetificacaoJulgamentoDenunciaRepository()
    {
        if (empty($this->retificacaoJulgamentoDenunciaRepository)) {
            $this->retificacaoJulgamentoDenunciaRepository = $this->getRepository(RetificacaoJulgamentoDenuncia::class);
        }

        return $this->retificacaoJulgamentoDenunciaRepository;
    }

    /**
     * Retorna uma nova instância de 'TipoJulgamentoRepository'.
     *
     * @return TipoJulgamentoRepository
     */
    private function getTipoJulgamentoRepository()
    {
        if (empty($this->tipoJulgamentoRepository)) {
            $this->tipoJulgamentoRepository = $this->getRepository(TipoJulgamento::class);
        }

        return $this->tipoJulgamentoRepository;
    }

    /**
     * Retorna uma nova instância de 'TipoSentencaJulgamentoRepository'.
     *
     * @return TipoSentencaJulgamentoRepository
     */
    private function getTipoSentencaJulgamentoRepository()
    {
        if (empty($this->tipoSentencaJulgamentoRepository)) {
            $this->tipoSentencaJulgamentoRepository = $this->getRepository(TipoSentencaJulgamento::class);
        }

        return $this->tipoSentencaJulgamentoRepository;
    }

    /**
     * Retorna uma nova instância de 'ArquivoJulgamentoDenunciaRepository'.
     *
     * @return ArquivoJulgamentoDenunciaRepository
     */
    private function getArquivoJulgamentoDenunciaRepository()
    {
        if (empty($this->arquivoJulgamentoDenunciaRepository)) {
            $this->arquivoJulgamentoDenunciaRepository = $this->getRepository(ArquivoJulgamentoDenuncia::class);
        }

        return $this->arquivoJulgamentoDenunciaRepository;
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

}
