<?php
/*
 * ContrarrazaoDenunciaBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoRecursoContrarrazaoDenuncia;
use App\Entities\ContrarrazaoRecursoDenuncia;
use App\Entities\Denuncia;
use App\Entities\Profissional;
use App\Entities\RecursoDenuncia;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailContrarrazaoRecursoDenunciaCadastroJob;
use App\Jobs\EnviarEmailContrarrazaoRecursoDenunciaEncerrarJob;
use App\Mail\ContrarrazaoRecursoDenunciaMail;
use App\Repository\ArquivoRecursoContrarrazaoDenunciaRepository;
use App\Repository\ContrarrazaoRecursoDenunciaRepository;
use App\Repository\RecursoDenunciaRepository;
use App\Service\ArquivoService;
use App\Service\CalendarioApiService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ContrarrazaoRecursoDenunciaTO;
use App\To\EmailDenunciaTO;
use App\To\RecursoDenunciaTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a
 * entidade 'ContrarrazaoRecursoDenuncia'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class ContrarrazaoRecursoDenunciaBO extends AbstractBO
{

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

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
     * @var RecursoContrarrazaoBO
     */
    private $recursoContrarrazaoBO;

    /**
     * @var CalendarioApiService
     */
    private $calendarioApiService;

    /**
     * @var EncaminhamentoDenunciaBO
     */
    private $encaminhamentoDenunciaBO;

    /**
     * @var RecursoDenunciaRepository
     */
    private $recursoDenunciaRepository;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var ContrarrazaoRecursoDenunciaRepository
     */
    private $contrarrazaoRecursoDenunciaRepository;

    /**
     * @var ArquivoRecursoContrarrazaoDenunciaRepository
     */
    private $arquivoRecursoContrarrazaoDenunciaRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salva o julgamento da denuncia.
     *
     * @param ContrarrazaoRecursoDenunciaTO $contrarrazaoRecursoDenunciaTO
     *
     * @return \stdClass
     * @throws \App\Exceptions\NegocioException
     * @throws \Exception
     */
    public function salvar(ContrarrazaoRecursoDenunciaTO $contrarrazaoRecursoDenunciaTO)
    {
        $this->validarCamposObrigatorios($contrarrazaoRecursoDenunciaTO);
        $this->validarQuantidadeArquivosJulgamento($contrarrazaoRecursoDenunciaTO);

        try {
            $this->beginTransaction();

            $usuarioFactory = $this->getUsuarioFactory();
            $idProfissionalLogado = $usuarioFactory->getUsuarioLogado()->idProfissional;

            $idDenuncia = $contrarrazaoRecursoDenunciaTO->getIdDenuncia();
            $recursoContrarrazao = $this->validaExistenciaPedidoRecurso($idDenuncia, $idProfissionalLogado);

            $this->validarDiasUteisPrazo($recursoContrarrazao);

            $idDenuncia = $contrarrazaoRecursoDenunciaTO->getIdDenuncia();
            $denuncia = $this->getDenunciaBO()->getDenuncia($idDenuncia);

            $contrarrazao = $this->prepararContrarrazaoRecursoSalvar($recursoContrarrazao, $contrarrazaoRecursoDenunciaTO);
            $contrarrazao->setDenuncia($denuncia);
            $contrarrazao->setProfissional(
                Profissional::newInstance(['id' => $idProfissionalLogado])
            );

            $contrarrazao->setRecurso($recursoContrarrazao);
            $contrarrazaoSalvo = $this->getContrarrazaoRecursoDenunciaRepository()->persist($contrarrazao);

            $arquivos = $contrarrazaoRecursoDenunciaTO->getArquivosContrarrazao();
            if (!empty($arquivos)) {
                $this->salvarArquivosContrarrazaoRecurso($arquivos, $contrarrazaoSalvo);
            }

            $this->alteraStatusDenunciaPorPrazoRecursoContrarrazao($denuncia, $contrarrazaoSalvo);

            $historicoDenunciaJulgamento = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
                Constants::ACAO_HISTORICO_CONTRARRAZAO_RECURSO);
            $this->getHistoricoDenunciaBO()->salvar($historicoDenunciaJulgamento);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailContrarrazaoRecursoDenunciaCadastroJob($contrarrazaoSalvo->getId()));

        return $this->getRetornoSalvar($contrarrazaoSalvo);
    }

    /**
     * Disponibiliza o arquivo 'Recurso e Contrarrazão' para 'download'
     * conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return \App\To\ArquivoTO
     * @throws \App\Exceptions\NegocioException
     */
    public function getArquivo($idArquivo)
    {
        $arquivoRecurso = $this->getArquivoDenuncia($idArquivo);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioRecursoDenuncia($arquivoRecurso->getRecurso()->getDenuncia()->getId());
        return $this->getArquivoService()->getArquivo($caminho, $arquivoRecurso->getNomeFisico(), $arquivoRecurso->getNome());
    }

    /**
     * Altera o status de denuncia de acordo com o prazo e a existencia de
     * recursos para denuncia.
     *
     * @param \App\Entities\Denuncia        $denuncia
     * @param \App\Entities\ContrarrazaoRecursoDenuncia $contrarrazaoSalvo
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function alteraStatusDenunciaPorPrazoRecursoContrarrazao(
        Denuncia $denuncia,
        ContrarrazaoRecursoDenuncia $contrarrazaoSalvo
    ) {
        $filtro = new \stdClass();
        $filtro->idDenuncia = $denuncia->getId();
        $filtro->idRecursosOcultos = [
            $contrarrazaoSalvo->getId(),
            $contrarrazaoSalvo->getRecurso()->getId()
        ];

        $recursosDenuncia = $this->getRecursoDenunciaRepository()
            ->getRecursosPorFiltro($filtro);

        $dadosVerificacaoRecurso = $this->getDadosVerificacaoRecursos($recursosDenuncia);

        /** @var \stdClass $dadosRecurso */
        foreach ($dadosVerificacaoRecurso as $dadosRecurso) {
            $fluxoSim = (!$dadosRecurso->hasContrarrazao && $dadosRecurso->isPrazoContrarrazaoEncerrado)
                || $dadosRecurso->hasContrarrazao;

            if ($fluxoSim || $dadosRecurso->isPrazoRecursoEncerrado) {
                $this->getDenunciaBO()->alterarStatusSituacaoDenuncia(
                    $denuncia,
                    Constants::STATUS_DENUNCIA_JULG_SEGUNDA_INSTANCIA
                );
                return;
            }
        }
    }

    /**
     * Executa rotina que altera o status da denuncia por prazo da contrarrazão e de recurso.
     *
     * @throws \Exception
     */
    public function alteraStatusDenunciaPorPrazoContrarrazao()
    {
        $eleicao = $this->getEleicaoBO()->getEleicaoVigentePorNivelAtividade(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_RECURSO_CONTRARRAZAO_DENUNCIA,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_CONTRARRAZAO_DENUNCIA
        );

        if ($eleicao) {
            $filtroTO = new \stdClass();
            $filtroTO->idEleicao = $eleicao->getId();
            $filtroTO->idSituacao = [
                Constants::STATUS_DENUNCIA_JULG_PRIMEIRA_INSTANCIA,
                Constants::STATUS_DENUNCIA_EM_JULGAMENTO_PRIMEIRA_INSTANCIA,
                Constants::STATUS_DENUNCIA_AGUARDANDO_CONTRARRAZAO
            ];

            $denuncias = $this->getDenunciaBO()
                ->getDenunciasEmJulgamentoParaRotinaRecursoContrarrazao($filtroTO);

            /** @var Denuncia $denuncia */
            foreach ($denuncias as $denuncia) {
                $recursosDenuncia = $this->getDenunciaBO()
                    ->getEstruturaRecursosContrarrazao($denuncia->getRecursoDenuncia());

                $dadosVerificacaoRecurso = $this->getDadosVerificacaoRecursos($recursosDenuncia);

                $qtdRecursos = count($dadosVerificacaoRecurso);
                $noHasContrarrazaoIsPrazoContrarrazaoEncerradoRecursoUm = !empty($dadosVerificacaoRecurso[0])
                    ? (!$dadosVerificacaoRecurso[0]->hasContrarrazao && $dadosVerificacaoRecurso[0]->isPrazoContrarrazaoEncerrado) : false;
                $noHasContrarrazaoIsPrazoContrarrazaoEncerradoRecursoDois = !empty($dadosVerificacaoRecurso[1])
                    ? (!$dadosVerificacaoRecurso[1]->hasContrarrazao && $dadosVerificacaoRecurso[1]->isPrazoContrarrazaoEncerrado) : false;

                if ($qtdRecursos === 1) {
                    $isRecursoUm = ($noHasContrarrazaoIsPrazoContrarrazaoEncerradoRecursoUm && $dadosVerificacaoRecurso[0]->isPrazoRecursoEncerrado)
                        || $dadosVerificacaoRecurso[0]->hasContrarrazao;
                }

                if ($qtdRecursos === 2) {
                    $isRecursoUm = $noHasContrarrazaoIsPrazoContrarrazaoEncerradoRecursoUm || $dadosVerificacaoRecurso[0]->hasContrarrazao;
                    $isRecursoDois = $noHasContrarrazaoIsPrazoContrarrazaoEncerradoRecursoDois || $dadosVerificacaoRecurso[1]->hasContrarrazao;
                }

                if (($qtdRecursos === 1 && $isRecursoUm) || ($qtdRecursos === 2 && $isRecursoUm && $isRecursoDois)) {

                    $nDenuncia = $this->getDenunciaBO()->findById($denuncia->getId());
                    $this->getDenunciaBO()->alterarStatusSituacaoDenuncia(
                        $nDenuncia,
                        Constants::STATUS_DENUNCIA_JULG_SEGUNDA_INSTANCIA
                    );

                    if ($noHasContrarrazaoIsPrazoContrarrazaoEncerradoRecursoUm) {
                        Utils::executarJOB(new EnviarEmailContrarrazaoRecursoDenunciaEncerrarJob($denuncia->getId(), $recursosDenuncia[0]->getId()));
                    }

                    if ($noHasContrarrazaoIsPrazoContrarrazaoEncerradoRecursoDois) {
                        Utils::executarJOB(new EnviarEmailContrarrazaoRecursoDenunciaEncerrarJob($denuncia->getId(), $recursosDenuncia[1]->getId()));
                    }
                }
            }
        }
    }

    /**
     * Prepara e retorna a entidade de julgamento de denuncia para ser
     * persistida.
     *
     * @param \App\Entities\RecursoDenuncia $recursoContrarrazao
     * @param ContrarrazaoRecursoDenunciaTO $contrarrazaoRecursoDenunciaTO
     *
     * @return \App\Entities\ContrarrazaoRecursoDenuncia
     * @throws \Exception
     */
    private function prepararContrarrazaoRecursoSalvar(
        RecursoDenuncia $recursoContrarrazao,
        ContrarrazaoRecursoDenunciaTO $contrarrazaoRecursoDenunciaTO
    ) {
        return ContrarrazaoRecursoDenuncia::newInstance([
            'dtRecurso' => Utils::getData(),
            'dsRecurso' => $contrarrazaoRecursoDenunciaTO->getDescricaoRecurso(),
            'tipoRecursoContrarrazaoDenuncia' => $recursoContrarrazao->getTipoRecursoContrarrazaoDenuncia(),
        ]);
    }

    /**
     * Método auxiliar para Salvar dados dos Arquivos para a Julgamento Denuncia
     *
     * @param                    $arquivosContrarrazao
     * @param ContrarrazaoRecursoDenuncia    $contrarrazaoRecursoDenuncia
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    private function salvarArquivosContrarrazaoRecurso($arquivosContrarrazao, ContrarrazaoRecursoDenuncia $contrarrazaoRecursoDenuncia)
    {
        $arquivosSalvos = new ArrayCollection();

        /** @var ArquivoGenericoTO $arquivoContrarrazao */
        foreach ($arquivosContrarrazao as $arquivoContrarrazao) {
            $nomeFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivoContrarrazao->getNome(), Constants::PREFIXO_ARQ_CONTRARRAZAO_RECURSO
            );

            $arquivoContrarrazao->setNomeFisico($nomeFisico);
            $arquivoRecursoContrarrazaoDenuncia = ArquivoRecursoContrarrazaoDenuncia::newInstance([
                'nome' => $arquivoContrarrazao->getNome(), 'nomeFisico' => $arquivoContrarrazao->getNomeFisico(),
            ]);

            $arquivoRecursoContrarrazaoDenuncia->setRecurso($contrarrazaoRecursoDenuncia);
            $arquivoSalvo = $this->getArquivoRecursoContrarrazaoRepository()->persist($arquivoRecursoContrarrazaoDenuncia);

            $arquivosSalvos->add($arquivoSalvo);
            $this->salvaArquivosDiretorio($arquivoContrarrazao, $contrarrazaoRecursoDenuncia);
            $arquivoSalvo->setArquivo(null);
        }

        $contrarrazaoRecursoDenuncia->setArquivos($arquivosSalvos);
        $contrarrazaoRecursoDenuncia->removerFiles();
    }

    /**
     * Retorna os dias uteis baseado no recurso.
     *
     * @param \App\Entities\RecursoDenuncia $recursoDenuncia
     *
     * @return null
     * @throws \Exception
     */
    private function getDiasUteisRecursoDenuncia(RecursoDenuncia $recursoDenuncia)
    {
        $ano = Utils::getAnoData($recursoDenuncia->getDtRecurso());
        $feriados = $this->getCalendarioApiService()->getFeriadosNacionais($ano);

        return Utils::adicionarDiasUteisData(
            $recursoDenuncia->getDtRecurso(),
            Constants::PRAZO_DEFESA_CONTRARRAZAO_RECURSO_DENUNCIA_DIAS,
            $feriados
        );
    }

    /**
     * Verifica se o prazo de contrarrazão do recurso não encerrou.
     *
     * @param RecursoDenuncia $recursoDenuncia
     *
     * @return bool
     * @throws \Exception
     */
    public function isPrazoContrarrazaoRecursoDenuncia($recursoDenuncia)
    {
        $isDataValida = false;

        if ($recursoDenuncia !== null) {
            $dataLimit = $this->getDiasUteisRecursoDenuncia($recursoDenuncia);

            if (Utils::getDataHoraZero() <= Utils::getDataHoraZero($dataLimit)) {
                $isDataValida = true;
            }
        }

        return $isDataValida;
    }

    /**
     * Recupera a entidade 'ArquivoRecursoContrarrazao' por meio do 'id'
     * informado.
     *
     * @param $id
     *
     * @return \App\Entities\ArquivoRecursoContrarrazaoDenuncia|null
     */
    private function getArquivoDenuncia($id)
    {
        return current($this->getArquivoRecursoContrarrazaoRepository()->getPorId($id));
    }

    /**
     * Realiza o(s) upload(s) do(s) arquivo(s) da contrarrazao do recurso da
     * denuncia
     *
     * @param ArquivoGenericoTO $arquivo
     * @param \App\Entities\ContrarrazaoRecursoDenuncia        $contrarrazaoRecursoSalvo
     */
    private function salvaArquivosDiretorio(ArquivoGenericoTO $arquivo, ContrarrazaoRecursoDenuncia $contrarrazaoRecursoSalvo)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioRecursoDenuncia($contrarrazaoRecursoSalvo->getDenuncia()->getId());

        if ($arquivo !== null) {
            if (!empty($arquivo->getArquivo())) {
                $this->getArquivoService()->salvar($caminho, $arquivo->getNomeFisico(), $arquivo->getArquivo());
            }
        }
    }

    /**
     * Verifica se os campos obrigatórios foram preenchidos.
     *
     * @param ContrarrazaoRecursoDenunciaTO $contrarrazaoRecursoDenunciaTO
     * @throws \App\Exceptions\NegocioException
     */
    private function validarCamposObrigatorios(ContrarrazaoRecursoDenunciaTO $contrarrazaoRecursoDenunciaTO)
    {
        if ($contrarrazaoRecursoDenunciaTO->getDescricaoRecurso() === null) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }
    }

    /**
     * Valida a quantidade de arquivos para a Contrarrazão do recurso da
     * Denuncia.
     *
     * @param ContrarrazaoRecursoDenunciaTO $contrarrazaoRecursoDenunciaTO
     * @throws NegocioException
     */
    private function validarQuantidadeArquivosJulgamento(ContrarrazaoRecursoDenunciaTO $contrarrazaoRecursoDenunciaTO)
    {
        $arquivos = $contrarrazaoRecursoDenunciaTO->getArquivosContrarrazao();
        if (!empty($arquivos)
            && count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO_JULG_DENUNCIA
        ) {
            throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO);
        }
    }

    /**
     * Valida os dias uteis referente ao prazo do recurso.
     *
     * @param RecursoDenuncia $recursoContrarrazao
     * @throws \Exception
     */
    private function validarDiasUteisPrazo(RecursoDenuncia $recursoContrarrazao)
    {
        $dataLimite = $this->getDiasUteisRecursoDenuncia($recursoContrarrazao);

        if (Utils::getData() > $dataLimite) {
            throw new NegocioException(Lang::get('messages.denuncia.julgamento.recurso.prazo_solicitacao_recurso_encerrou'));
        }
    }

    /**
     * Valida se já existe recurso para contrarrazão para a denuncia informada.
     *
     * @param $idDenuncia
     * @param $idProfissionalLogado
     *
     * @return \App\Entities\RecursoDenuncia|null
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function validaExistenciaPedidoRecurso(
        $idDenuncia,
        $idProfissionalLogado
    ) {
        $recursoContrarrazao = $this->getRecursoDenunciaRepository()
            ->getRecursoPorDenunciaAndProfissionalLogado($idDenuncia, $idProfissionalLogado);

        if ($recursoContrarrazao === null) {
            throw new NegocioException(Lang::get('messages.denuncia.julgamento.recurso.contrarrazao.nao_existe_recurso_contrarrazao'));
        }

        return $recursoContrarrazao;
    }

    /**
     * Retorna os dados dos recursos da denuncia formatados para validações da
     * contrarrazão
     * @param $recursosDenuncia
     *
     * @return \stdClass[]
     * @throws \Exception
     */
    private function getDadosVerificacaoRecursos($recursosDenuncia)
    {
        $recursosDenunciaFormatados = $this->getDenunciaBO()->getEstruturaRecursosContrarrazao(
            $recursosDenuncia
        );

        $dadosRecursosDenuncia = [];

        /** @var RecursoDenuncia $recursoDenuncia */
        foreach ($recursosDenunciaFormatados as $recursoDenuncia) {
            $dadosRecurso = new \stdClass();
            $dadosRecurso->hasContrarrazao = null !== $recursoDenuncia->getContrarrazao();
            $dadosRecurso->isPrazoContrarrazaoEncerrado = !$this->isPrazoContrarrazaoRecursoDenuncia($recursoDenuncia);
            $dadosRecurso->isPrazoRecursoEncerrado = !$this->getRecursoDenunciaBO()->isPrazoRecursoDenuncia(
                $recursoDenuncia->getDenuncia()->getPrimeiroJulgamentoDenuncia()
            );
            $dadosRecurso->isDenunciado = $recursoDenuncia->getTipoRecursoContrarrazaoDenuncia()
                === Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE;
            $dadosRecurso->isDenunciante = $recursoDenuncia->getTipoRecursoContrarrazaoDenuncia()
                === Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO;

            $dadosRecursosDenuncia[] = $dadosRecurso;
        }

        return $dadosRecursosDenuncia;
    }

    /**
     * Cria um objeto para organizar o retorno de sucesso do método Salvar
     *
     * @param ContrarrazaoRecursoDenuncia $contrarrazaoRecursoDenuncia
     * @return \stdClass
     */
    private function getRetornoSalvar(ContrarrazaoRecursoDenuncia $contrarrazaoRecursoDenuncia)
    {
        $retorno = new \stdClass();
        $retorno->numeroSequencial = $contrarrazaoRecursoDenuncia->getDenuncia()->getNumeroSequencial();

        return $retorno;
    }

    /**
     * Retorna os prazos para contrarrazao do recurso da denuncia do denunciante e denunciado.
     *
     * @param Denuncia $denuncia
     *
     * @return \stdClass
     * @throws \Exception
     */
    public function getPrazoContrarrazaoRecursos($denuncia)
    {
        $prazoContrarrazao = new \stdClass();
        $prazoContrarrazao->hasContrarrazaoRecursoDenuncianteDentroPrazo = false;
        $prazoContrarrazao->hasContrarrazaoRecursoDenunciadoDentroPrazo = false;

        if (!empty($denuncia->getRecursoDenuncia())) {
            foreach ($denuncia->getRecursoDenuncia() as $recurso) {
                $prazo = $this->isPrazoContrarrazaoRecursoDenuncia($recurso);

                if ($recurso->getTipoRecursoContrarrazaoDenuncia()
                    === Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE && $prazo) {
                    $prazoContrarrazao->hasContrarrazaoRecursoDenunciadoDentroPrazo = true;
                }

                if ($recurso->getTipoRecursoContrarrazaoDenuncia()
                    === Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO  && $prazo) {
                    $prazoContrarrazao->hasContrarrazaoRecursoDenuncianteDentroPrazo = true;
                }
            }
        }

        return $prazoContrarrazao;
    }

    /**
     * Enviar e-mail ao cadastrar contrarrazão do recurso da denúncia
     *
     * @param int $idContrarrazaoRecurso
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enviarEmailCadastroContrarrazao(int $idContrarrazaoRecurso)
    {
        $contrarrazaoRecurso = $this->getContrarrazaoRecursoDenunciaRepository()->find($idContrarrazaoRecurso);

        $this->enviarEmailPorTipo($contrarrazaoRecurso->getDenuncia()->getId(), [
            Constants::EMAIL_INFORMATIVO_DENUNCIANTE_CADASTRO_CONTRARAZAO,
            Constants::EMAIL_INFORMATIVO_DENUNCIADO_CADASTRO_CONTRARRAZAO,
            Constants::EMAIL_INFORMATIVO_COORDENADOR_CEN_ADJUNTO_CEN_CADASTRO_CONTRARRAZAO,
            Constants::EMAIL_INFORMATIVO_ASSESSOR_CEN_CADASTRO_CONTRARRAZAO,
        ], true, null, $contrarrazaoRecurso);
    }

    /**
     * Enviar e-mail ao encerrar contrarrazão do recurso da denúncia
     *
     * @param int $idDenuncia
     * @param int $idRecurso
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enviarEmailEncerrarContrarrazao(int $idDenuncia, $idRecurso)
    {
        $recursoDenuncia = $this->getRecursoDenunciaBO()->findById($idRecurso);

        $this->enviarEmailPorTipo($idDenuncia, [
            Constants::EMAIL_INFORMATIVO_DENUNCIANTE_ENCERRAMENTO_PRAZO_CONTRARRAZAO,
            Constants::EMAIL_INFORMATIVO_DENUNCIADO_ENCERRAMENTO_PRAZO_CONTRARRAZAO,
            Constants::EMAIL_INFORMATIVO_COORDENADOR_CE_CEN_ADJUNTO_ENCERRAMENTO_PRAZO_CONTRARRAZAO,
            Constants::EMAIL_INFORMATIVO_ACESSOR_CE_CEN_ENCERRAMENTO_PRAZO_CONTRARRAZAO,
        ], false, $recursoDenuncia, null);
    }

    /**
     * Enviar e-mail de acordo com os tipos passado
     *
     * @param int $idDenuncia
     * @param array $tipos
     * @param bool $isCadastrar
     * @param $contrarrazaoRecurso
     * @param $recursoDenuncia
     *
     * @throws NonUniqueResultException
     * @throws \Exception
     * @throws \Doctrine\ORM\NoResultException
     */
    public function enviarEmailPorTipo(
        int $idDenuncia, array $tipos, $isCadastrar = true, $recursoDenuncia = null, $contrarrazaoRecurso = null
    ) {
        $denuncia = $this->getDenunciaBO()->findById($idDenuncia);

        $atividade = $this->getAtividadeSecundariaBO()->getPorAtividadeSecundaria(
            $denuncia->getAtividadeSecundaria()->getId(),
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_RECURSO_CONTRARRAZAO_DENUNCIA,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_CONTRARRAZAO_DENUNCIA
        );

        $emailContrarrazaoTO = EmailDenunciaTO::newInstanceFromEntity($denuncia);
        $emailContrarrazaoTO->setProcessoEleitoral(
            $atividade->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada()
        );
        $emailContrarrazaoTO->setStatusDenuncia(
            $this->getDenunciaBO()->getNomeSituacaoAtualDenuncia($denuncia)
        );
        if (!is_null($contrarrazaoRecurso)) {
            $emailContrarrazaoTO->setContrarrazaoRecursoDenuncia(
                ContrarrazaoRecursoDenunciaTO::newInstanceFromEntity($contrarrazaoRecurso)
            );
        }
        if (!is_null($recursoDenuncia)) {
            $emailContrarrazaoTO->setRecursoDenuncia(
                RecursoDenunciaTO::newInstanceFromEntity($recursoDenuncia)
            );
        }

        foreach ($tipos as $tipo) {
            $destinarios = $this->getDestinatariosEmail($denuncia, $tipo);

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(), $tipo
            );

            $isExibirResponsavelSigiloso = !empty($contrarrazaoRecurso) ?
                $this->isResponsavelCadastroExibirDenunciaSigilosa($tipo, $contrarrazaoRecurso) : false;

            if (!empty($emailAtividadeSecundaria) && !empty($destinarios)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios(array_unique($destinarios));
                Email::enviarMail(new ContrarrazaoRecursoDenunciaMail($emailTO, $emailContrarrazaoTO, $isCadastrar, $isExibirResponsavelSigiloso));
            }
        }
    }

    /**
     * Verifica se no campo responsável pelo cadastro deve ser exibido denúncia sigilosa
     *
     * @param int $registro
     * @param ContrarrazaoRecursoDenuncia $contrarrazaoRecursoDenuncia
     * @return bool
     */
    public function isResponsavelCadastroExibirDenunciaSigilosa(
        int $registro, ContrarrazaoRecursoDenuncia $contrarrazaoRecursoDenuncia
    ) {
        $isExibirResponsavelSigiloso = false;
        $registros = [
            Constants::EMAIL_INFORMATIVO_DENUNCIANTE_CADASTRO_CONTRARAZAO,
            Constants::EMAIL_INFORMATIVO_DENUNCIADO_CADASTRO_CONTRARRAZAO,
            Constants::EMAIL_INFORMATIVO_COORDENADOR_CEN_ADJUNTO_CEN_CADASTRO_CONTRARRAZAO
        ];

        if ($contrarrazaoRecursoDenuncia->getDenuncia()->isSigiloso() &&
            in_array($registro, $registros) &&
            $contrarrazaoRecursoDenuncia->getTipoRecursoContrarrazaoDenuncia() ==
            Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO
        ) {
            $isExibirResponsavelSigiloso = true;
        }

        return $isExibirResponsavelSigiloso;
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
        $destinatarios = [];
        $idCauUf = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::COMISSAO_MEMBRO_CAU_BR_ID;

        if (in_array($tipo, [
                Constants::EMAIL_INFORMATIVO_DENUNCIANTE_CADASTRO_CONTRARAZAO,
                Constants::EMAIL_INFORMATIVO_DENUNCIANTE_ENCERRAMENTO_PRAZO_CONTRARRAZAO,
            ], true)
        ) {
            $destinatarios[] = $denuncia->getPessoa()->getEmail();
        }

        if (in_array($tipo, [
                Constants::EMAIL_INFORMATIVO_DENUNCIADO_CADASTRO_CONTRARRAZAO,
                Constants::EMAIL_INFORMATIVO_DENUNCIADO_ENCERRAMENTO_PRAZO_CONTRARRAZAO,
            ], true)
        ) {
            $destinatarios = $this->getEncaminhamentoDenunciaBO()->getEmailsDenunciadoPorTipoDenuncia($denuncia);
        }

        if (in_array($tipo, [
                Constants::EMAIL_INFORMATIVO_COORDENADOR_CEN_ADJUNTO_CEN_CADASTRO_CONTRARRAZAO,
                Constants::EMAIL_INFORMATIVO_COORDENADOR_CE_CEN_ADJUNTO_ENCERRAMENTO_PRAZO_CONTRARRAZAO,
            ], true)
        ) {
            $coordenadoresCEN = $this->getMembroComissaoBO()->getEmailsCoordenadoresPorIdAtividaSecundaria(
                $denuncia->getAtividadeSecundaria()->getId(), Constants::ID_CAU_BR
            );

            if($idCauUf != Constants::ID_CAU_BR) {
                $coordenadoresCE = $this->getMembroComissaoBO()->getEmailsCoordenadoresPorIdAtividaSecundaria(
                    $denuncia->getAtividadeSecundaria()->getId(), $idCauUf
                );
            }
            $destinatarios = array_merge($coordenadoresCE ?? [], $coordenadoresCEN ?? []);
        }

        if (in_array($tipo, [
                Constants::EMAIL_INFORMATIVO_ASSESSOR_CEN_CADASTRO_CONTRARRAZAO,
                Constants::EMAIL_INFORMATIVO_ACESSOR_CE_CEN_ENCERRAMENTO_PRAZO_CONTRARRAZAO,
            ], true)
        ) {
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
                $idCauUf
            );
        }

        return $destinatarios;
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
     * Método para retornar a instancia de 'MembroComissaoBO'.
     *
     * @return MembroComissaoBO
     */
    private function getMembroComissaoBO(): MembroComissaoBO
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
        if (empty($this->atividadeSecundariaBO)) {
            $this->atividadeSecundariaCalendarioBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }
        return $this->atividadeSecundariaCalendarioBO;
    }

    /**
     * Retorna uma nova instância de 'EmailAtividadeSecundariaBO'.
     *
     * @return EmailAtividadeSecundariaBO|mixed
     */
    private function getEmailAtividadeSecundariaBO(): EmailAtividadeSecundariaBO
    {
        if (empty($this->emailAtividadeSecundariaBO)) {
            $this->emailAtividadeSecundariaBO = app()->make(EmailAtividadeSecundariaBO::class);
        }

        return $this->emailAtividadeSecundariaBO;
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
     * Retorna uma nova instância de 'RecursoDenunciaBO'.
     *
     * @return RecursoContrarrazaoBO
     */
    private function getRecursoDenunciaBO()
    {
        if (empty($this->recursoContrarrazaoBO)) {
            $this->recursoContrarrazaoBO = app()->make(RecursoContrarrazaoBO::class);
        }

        return $this->recursoContrarrazaoBO;
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
     * Retorna uma nova instância de 'RecursoDenunciaRepository'.
     *
     * @return RecursoDenunciaRepository
     */
    private function getRecursoDenunciaRepository()
    {
        if (empty($this->recursoDenunciaRepository)) {
            $this->recursoDenunciaRepository = $this->getRepository(RecursoDenuncia::class);
        }

        return $this->recursoDenunciaRepository;
    }

    /**
     * Retorna uma nova instância de 'ContrarrazaoRecursoDenunciaRepository'.
     *
     * @return ContrarrazaoRecursoDenunciaRepository
     */
    private function getContrarrazaoRecursoDenunciaRepository()
    {
        if (empty($this->contrarrazaoRecursoDenunciaRepository)) {
            $this->contrarrazaoRecursoDenunciaRepository = $this->getRepository(ContrarrazaoRecursoDenuncia::class);
        }

        return $this->contrarrazaoRecursoDenunciaRepository;
    }

    /**
     * Retorna uma nova instância de
     * 'ArquivoRecursoContrarrazaoDenunciaRepository'.
     *
     * @return ArquivoRecursoContrarrazaoDenunciaRepository
     */
    private function getArquivoRecursoContrarrazaoRepository()
    {
        if (empty($this->arquivoRecursoContrarrazaoDenunciaRepository)) {
            $this->arquivoRecursoContrarrazaoDenunciaRepository = $this->getRepository(ArquivoRecursoContrarrazaoDenuncia::class);
        }

        return $this->arquivoRecursoContrarrazaoDenunciaRepository;
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
