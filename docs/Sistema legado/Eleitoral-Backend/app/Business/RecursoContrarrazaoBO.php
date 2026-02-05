<?php
/*
 * RecursoContrarrazaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoRecursoContrarrazaoDenuncia;
use App\Entities\Denuncia;
use App\Entities\JulgamentoDenuncia;
use App\Entities\Profissional;
use App\Entities\RecursoDenuncia;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailRecursoDenunciaCadastroJob;
use App\Jobs\EnviarEmailRecursoDenunciaEncerradoJob;
use App\Mail\RecursoDenunciaMail;
use App\Repository\ArquivoRecursoContrarrazaoDenunciaRepository;
use App\Repository\RecursoDenunciaRepository;
use App\Service\ArquivoService;
use App\Service\CalendarioApiService;
use App\Service\CorporativoService;
use App\To\ContrarrazaoRecursoDenunciaTO;
use App\To\EmailDenunciaTO;
use App\To\RecursoDenunciaTO;
use App\Util\Email;
use App\Util\Utils;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Facades\Lang;
use function Matrix\trace;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'RecursoDenuncia'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class RecursoContrarrazaoBO extends AbstractBO
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
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var RecursoDenunciaRepository
     */
    private $recursoRepository;

    /**
     * @var JulgamentoDenuncia
     */
    private $julgamentoRepository;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var HistoricoDenunciaBO
     */
    private $historicoDenunciaBO;

    /**
     * @var CalendarioApiService
     */
    private $calendarioApiService;

    /**
     * @var EncaminhamentoDenunciaBO
     */
    private $encaminhamentoDenunciaBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var ArquivoRecursoContrarrazaoDenunciaRepository
     */
    private $arquivoRecursoRepository;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    public function __construct()
    {
        $this->recursoRepository = $this->getRepository(RecursoDenuncia::class);
        $this->arquivoRecursoRepository = $this->getRepository(ArquivoRecursoContrarrazaoDenuncia::class);
        $this->julgamentoRepository = $this->getRepository(JulgamentoDenuncia::class);
    }

    /**
     * Retorna o Recurso da Denúncia conforme o id informado.
     *
     * @param $id
     *
     * @return object|RecursoDenuncia|null
     */
    public function findById($id)
    {
        return $this->recursoRepository->find($id);
    }

    /**
     * Salva os dados de um recurso na denuncia.
     *
     * @param RecursoDenuncia $recurso
     * @return null
     * @throws NegocioException
     */
    public function recurso(RecursoDenuncia $recurso)
    {
        $tpRecConDen = [
            Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE,
            Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO
        ];
        $idDenuncia = $recurso->getIdDenuncia();
        $dtJulgamento = $this->julgamentoRepository->findOneBy(
            ['denuncia' => $idDenuncia],
            ['data' => 'ASC']
        );
        $isRecurso = $this->recursoRepository->hasRecurso($tpRecConDen,$idDenuncia);
        $this->validarCamposObrigatoriosRecurso($recurso);
        $dtAtual = Utils::getData();
        $denuncia = $this->getDenunciaBO()->getDenuncia($idDenuncia);
        $recurso = $this->setNomeArquivoFisicoRecurso($recurso);
        $arquivos = (!empty($recurso->getArquivos())) ? clone $recurso->getArquivos() : null;
        $recurso->setArquivos(null);

        //Valida o tipo de denuncia
        $tipoDenuncia = $this->validTipoDenuncia($denuncia);
        $recurso->setDenuncia($denuncia);
        $dtLimite = $this->diasUteis($denuncia->getPrimeiroJulgamentoDenuncia());
        $valDtRecLimite = $this->hasRecursovalidacaoData($dtLimite, $dtAtual);

        $qtd = $this->countRecurso($isRecurso);
        if(!$valDtRecLimite){
            throw new NegocioException(Lang::get('messages.denuncia.julgamento.recurso.prazo_solicitacao_recurso_encerrou'));
        }
        $tipoRecurso = Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE;
        //Verifica se usuário logado é diferente do Profissional da denuncia
        if($denuncia->getPessoa()->getProfissional()->getId() != $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional)
        {
            $tipoRecurso = Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO;
        }

        $profissional = Profissional::newInstance(
            ['id' => $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional]
        );

        $denunciaAdmitidaSalva = null;

        try {
            $this->beginTransaction();
            //Montando Objeto Recurso
            $recurso->setDtRecurso($dtAtual);
            $recurso->setProfissional($profissional);
            $recurso->setTipoRecursoContrarrazaoDenuncia($tipoRecurso);
            $recursoSalvo = $this->recursoRepository->persist($recurso);

            if (!empty($recursoSalvo) && !empty($arquivos)) {
                $this->salvarArquivosDenunciaRecurso($arquivos, $recursoSalvo, $denuncia);
            }

            //Historico
            $historicoRecurso = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
                'Solicitação de recurso ou reconsideração');
             $this->getHistoricoDenunciaBO()->salvar($historicoRecurso);

             //Alterar status da denuncia
            if($qtd > 0)
            {
                $this->getDenunciaBO()->alterarStatusSituacaoDenuncia(
                    $denuncia,
                    Constants::STATUS_DENUNCIA_AGUARDANDO_CONTRARRAZAO
                );
            }
            if(!$tipoDenuncia){
                $this->getDenunciaBO()->alterarStatusSituacaoDenuncia(
                    $denuncia,
                    Constants::STATUS_DENUNCIA_JULG_SEGUNDA_INSTANCIA
                );
            }

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailRecursoDenunciaCadastroJob($recurso->getId()));

        return ["id" => $recurso->getId()];
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
     * Valida se os campos obrigatórios estão preenchidos
     *
     * @param RecursoDenuncia $recurso
     *
     * @throws NegocioException
     */
    private function validarCamposObrigatoriosRecurso(RecursoDenuncia $recurso)
    {
        $campos = [];

        if (empty($recurso->getDsRecurso())) {
            $campos[] = 'LABEL_DS_RECURSO';
        }

        // Arquivo ???

        if (!empty($campos)) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, $campos, true);
        }
    }

    private function diasUteis($julgamento)
    {
        $ano      = Utils::getAnoData($julgamento->getData());
        $feriados = $this->getCalendarioApiService()
            ->getDatasFeriadosNacionais($ano);

        $dataLimite = Utils::adicionarDiasUteisData(
            $julgamento->getData(),
            Constants::PRAZO_DEFESA_RECURSO_DENUNCIA_DIAS,
            $feriados
        );
        return $dataLimite;
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

    /**
     * Validação da data limite do Julgamento
     * @param $dtRecursoLimite
     * @param $dtAtual
     * @return bool
     * @throws \Exception
     */
    private function hasRecursovalidacaoData($dtRecursoLimite, $dtAtual)
    {
        $status = false;
        $dataLimteRecurso = $dtRecursoLimite;
        $dtRecurso    = new Carbon($dataLimteRecurso);
        $dtNow        = new Carbon($dtAtual);
        if($dtNow->lessThanOrEqualTo($dtRecurso)){
            $status = true;
        }
        return $status;
    }

    /**
     * @param $idDenuncia
     * @return bool
     * @throws \Exception
     */
    private function isValDtRecursoContrarrazaoDenuncia($idDenuncia){
        if(is_int($idDenuncia)){
            $denuncia = $this->getRepository(Denuncia::class)->find($idDenuncia);
            $dtLimite = $this->diasUteis($denuncia->getPrimeiroJulgamentoDenuncia());
            $dtAtual  = Utils::getData();
            $isDtVal = $this->hasRecursovalidacaoData($dtLimite, $dtAtual);
            return $isDtVal;
        }
    }

    /**
     * Enviar e-mail ao cadastrar o recurso da denúncia
     *
     * @param int $idRecursoDenuncia
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enviarEmailCadastroRecurso(int $idRecursoDenuncia)
    {
        $recursoDenuncia = $this->findById($idRecursoDenuncia);

        $this->enviarEmailPorTipo($recursoDenuncia->getDenuncia()->getId(), [
            Constants::EMAIL_INFORMATIVO_DENUNCIANTE_CADASTRO_RECURSO_RECONSIDERACAO,
            Constants::EMAIL_INFORMATIVO_DENUNCIADO_CADASTRO_RECURSO_RECONSIDERACAO,
            Constants::EMAIL_INFORMATIVO_COORDENADOR_CEN_ADJUNTO_CEN_RECURSO_RECONSIDERACAO,
            Constants::EMAIL_INFORMATIVO_ASSESSOR_CEN_RECURSO_RECONSIDERACAO
        ], true, $recursoDenuncia);
    }

    /**
     * Enviar e-mail ao encerrar o recurso da denúncia
     *
     * @param int $idDenuncia
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enviarEmailEncerrarRecurso(int $idDenuncia)
    {
        $this->enviarEmailPorTipo($idDenuncia, [
            Constants::EMAIL_INFORMATIVO_DENUNCIANTE_ENCERRAMENTO_PRAZO_RECURSO_RECONSIDERACAO,
            Constants::EMAIL_INFORMATIVO_DENUNCIADO_ENCERRAMENTO_PRAZO_RECURSO_RECONSIDERACAO,
            Constants::EMAIL_INFORMATIVO_COORDENADOR_CE_CEN_ADJUNTO_ENCERRAMENTO_PRAZO_RECURSO_RECONSIDERACAO,
            Constants::EMAIL_INFORMATIVO_ACESSOR_CE_CEN_ENCERRAMENTO_RECURSO_RECONSIDERACAO
        ], false, null);
    }

    /**
     * Enviar e-mail de acordo com os tipos passado
     *
     * @param int $idDenuncia
     * @param array $tipos
     * @param bool $isCadastrar
     * @param $recursoDenuncia
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enviarEmailPorTipo(int $idDenuncia, array $tipos, $isCadastrar = true, $recursoDenuncia = null)
    {
        $denuncia = $this->getDenunciaBO()->findById($idDenuncia);

        $atividade = $this->getAtividadeSecundariaBO()->getPorAtividadeSecundaria(
            $denuncia->getAtividadeSecundaria()->getId(),
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_RECURSO_CONTRARRAZAO_DENUNCIA,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_RECURSO_DENUNCIA
        );

        $emailRecursoDenunciaTO = $this->getDadosEmailRecursoDenuncia($denuncia, $recursoDenuncia, $atividade);

        foreach ($tipos as $tipo) {
            $destinarios = $this->getDestinatariosEmail($denuncia, $tipo);

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(), $tipo
            );

            $isExibirResponsavelSigiloso = !empty($recursoDenuncia) ?
                $this->isResponsavelCadastroExibirDenunciaSigilosa($tipo, $recursoDenuncia) : false;

            if (!empty($emailAtividadeSecundaria) && !empty($destinarios)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios($destinarios);
                Email::enviarMail(new RecursoDenunciaMail(
                    $emailTO, $emailRecursoDenunciaTO, $isCadastrar, $isExibirResponsavelSigiloso
                ));
            }
        }
    }

    /**
     * Verifica se no campo responsável pelo cadastro deve ser exibido denúncia sigilosa
     *
     * @param RecursoDenuncia $recursoDenuncia
     * @param int $registro
     * @return bool
     */
    public function isResponsavelCadastroExibirDenunciaSigilosa(int $registro, RecursoDenuncia $recursoDenuncia)
    {
        $isExibirResponsavelSigiloso = false;
        $registros = [
            Constants::EMAIL_INFORMATIVO_DENUNCIANTE_CADASTRO_RECURSO_RECONSIDERACAO,
            Constants::EMAIL_INFORMATIVO_DENUNCIADO_CADASTRO_RECURSO_RECONSIDERACAO,
            Constants::EMAIL_INFORMATIVO_COORDENADOR_CEN_ADJUNTO_CEN_RECURSO_RECONSIDERACAO
        ];

        if (
            $recursoDenuncia->getDenuncia()->isSigiloso() &&
            in_array($registro, $registros) &&
            $recursoDenuncia->getTipoRecursoContrarrazaoDenuncia() == Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE
        ) {
            $isExibirResponsavelSigiloso = true;
        }

        return $isExibirResponsavelSigiloso;
    }

    /**
     * Retorna os dados do corpo do e-mail
     *
     * @param Denuncia $denuncia
     * @param $recursoDenuncia
     * @param $atividade
     * @return EmailDenunciaTO
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDadosEmailRecursoDenuncia(Denuncia $denuncia, $recursoDenuncia, $atividade)
    {
        $emailRecursoDenunciaTO = EmailDenunciaTO::newInstanceFromEntity($denuncia);
        $emailRecursoDenunciaTO->setProcessoEleitoral(
            $atividade->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada()
        );
        $emailRecursoDenunciaTO->setStatusDenuncia(
            $this->getDenunciaBO()->getNomeSituacaoAtualDenuncia($denuncia)
        );
        if (!is_null($recursoDenuncia)) {
            $emailRecursoDenunciaTO->setRecursoDenuncia(
                RecursoDenunciaTO::newInstanceFromEntity($recursoDenuncia)
            );
        }

        return $emailRecursoDenunciaTO;
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

        if ($tipo == Constants::EMAIL_INFORMATIVO_DENUNCIANTE_CADASTRO_RECURSO_RECONSIDERACAO ||
            $tipo == Constants::EMAIL_INFORMATIVO_DENUNCIANTE_ENCERRAMENTO_PRAZO_RECURSO_RECONSIDERACAO) {
            $destinatarios[] = $denuncia->getPessoa()->getEmail();
        }

        if ($tipo == Constants::EMAIL_INFORMATIVO_DENUNCIADO_CADASTRO_RECURSO_RECONSIDERACAO ||
            $tipo == Constants::EMAIL_INFORMATIVO_DENUNCIADO_ENCERRAMENTO_PRAZO_RECURSO_RECONSIDERACAO) {
            $destinatarios = $this->getEncaminhamentoDenunciaBO()->getEmailsDenunciadoPorTipoDenuncia($denuncia);
        }

        $filial = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::ID_CAU_BR;

        if ($tipo == Constants::EMAIL_INFORMATIVO_COORDENADOR_CEN_ADJUNTO_CEN_RECURSO_RECONSIDERACAO) {
            $coordenadoresCEN = $this->getMembroComissaoBO()->getEmailsCoordenadoresPorIdAtividaSecundaria(
                $denuncia->getAtividadeSecundaria()->getId(), Constants::ID_CAU_BR
            );
            $coordenadoresCE = $this->getMembroComissaoBO()->getEmailsCoordenadoresPorIdAtividaSecundaria(
                $denuncia->getAtividadeSecundaria()->getId(), $filial
            );

            $destinatarios = array_merge($coordenadoresCEN ?? [], $coordenadoresCE ?? []);
        }

        if ($tipo == Constants::EMAIL_INFORMATIVO_COORDENADOR_CE_CEN_ADJUNTO_ENCERRAMENTO_PRAZO_RECURSO_RECONSIDERACAO) {
            $destinatarios = $this->getMembroComissaoBO()->getEmailsCoordenadoresPorIdAtividaSecundaria(
                $denuncia->getAtividadeSecundaria()->getId(), $filial
            );
        }

        if ($tipo == Constants::EMAIL_INFORMATIVO_ASSESSOR_CEN_RECURSO_RECONSIDERACAO) {
            $assessorCEN = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
                Constants::ID_CAU_BR
            );
            $assessorCE = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
                $filial
            );

            $destinatarios = array_merge($assessorCEN ?? [], $assessorCE ?? []);
        }

        if ($tipo == Constants::EMAIL_INFORMATIVO_ACESSOR_CE_CEN_ENCERRAMENTO_RECURSO_RECONSIDERACAO) {
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
                $filial
            );
        }

        return $destinatarios;
    }

    /**
     * Rotina de mudança de situação da denúncia caso o prazo de recurso encerrar
     *
     * @return void
     * @throws \Exception
     */
    public function rotinaPrazoEncerradoRecurso()
    {
        $eleicao = $this->getEleicaoBO()->getEleicaoVigentePorNivelAtividade(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_RECURSO_CONTRARRAZAO_DENUNCIA,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_RECURSO_DENUNCIA
        );

        $filtroTO = new \stdClass();
        $filtroTO->idEleicao = $eleicao->getId();
        $filtroTO->idSituacao = [Constants::STATUS_DENUNCIA_EM_JULGAMENTO_PRIMEIRA_INSTANCIA, Constants::STATUS_DENUNCIA_JULG_PRIMEIRA_INSTANCIA];
        $filtroTO->hasContrarrazao = false;

        $denuncias = $this->getDenunciaBO()->getDenunciasEmJulgamentoParaRotinaRecursoContrarrazao($filtroTO);

        /** @var Denuncia $denuncia */
        foreach ($denuncias as $denuncia) {

            if (!$this->isPrazoRecursoDenuncia($denuncia->getPrimeiroJulgamentoDenuncia())) {

                $objDenuncia = $this->getDenunciaBO()->findById($denuncia->getId());

                if (empty($denuncia->getRecursoDenuncia())) {
                    $this->getDenunciaBO()->alterarStatusSituacaoDenuncia(
                        $objDenuncia, Constants::STATUS_DENUNCIA_TRANSITADO_EM_JULGADO
                    );

                    $historicoRotinaRecurso = $this->getHistoricoDenunciaBO()->criarHistorico($objDenuncia,
                        Constants::ACAO_HISTORICO_PRAZO_ENCERRADO_RECURSO_DENUNCIA);
                    $this->getHistoricoDenunciaBO()->salvar($historicoRotinaRecurso);

                    Utils::executarJOB(new EnviarEmailRecursoDenunciaEncerradoJob($denuncia->getId()));
                }

                if (!empty($denuncia->getRecursoDenuncia()) && $denuncia->getRecursoDenuncia()->count() === 1) {
                    $this->getDenunciaBO()->alterarStatusSituacaoDenuncia(
                        $objDenuncia, Constants::STATUS_DENUNCIA_AGUARDANDO_CONTRARRAZAO
                    );
                }
            }
        }
    }

    /**
     * Verifica se o prazo de recurso não encerrou
     *
     * @param JulgamentoDenuncia $julgamentoDenuncia
     * @return bool
     */
    public function isPrazoRecursoDenuncia($julgamentoDenuncia)
    {
        $isDataValida = false;

        if (!empty($julgamentoDenuncia)) {
            $dataLimit = $this->diasUteis($julgamentoDenuncia);

            if (Utils::getDataHoraZero() <= Utils::getDataHoraZero($dataLimit)) {
                $isDataValida = true;
            }
        }

        return $isDataValida;
    }

    /**
     * Retorna as informações de recurso para exportar para PDF
     *
     * @param $idDenuncia
     * @return RecursoDenunciaTO
     * @throws \Exception
     */
    public function getExportarRecursoDenunciaPorTipoRecurso(
        $idDenuncia,
        $tipoRecurso
    ) {
        $recursosDenuncia = $this->recursoRepository->findBy([
            'denuncia' => $idDenuncia,
            'tipoRecursoContrarrazaoDenuncia' => $tipoRecurso
        ]);

        $recursosContrarrazao = $this->getDenunciaBO()->getEstruturaRecursosContrarrazao($recursosDenuncia);

        /** @var RecursoDenuncia $recursoDenuncia */
        $recursoDenuncia = !empty($recursosContrarrazao) ? current($recursosContrarrazao) : null;

        if ($recursoDenuncia) {
            $arquivosRecurso = $recursoDenuncia->getArquivos();
            if (!empty($arquivosRecurso)) {
                $recursoDenuncia->setArquivos($this->getDenunciaBO()->getDescricaoArquivoExportar(
                    $arquivosRecurso,
                    $this->getArquivoService()->getCaminhoRepositorioRecursoDenuncia($recursoDenuncia->getDenuncia()->getId())
                ));
            }

            $contrarrazao = $recursoDenuncia->getContrarrazao();
            if (null !== $contrarrazao) {
                $arquivosContrarrazao = $contrarrazao->getArquivos();

                if (!empty($arquivosContrarrazao)) {
                    $contrarrazao->setArquivos($this->getDenunciaBO()->getDescricaoArquivoExportar(
                        $arquivosContrarrazao,
                        $this->getArquivoService()->getCaminhoRepositorioRecursoDenuncia($recursoDenuncia->getDenuncia()->getId())
                    ));
                    $recursoDenuncia->setContrarrazao($contrarrazao);
                }
            }
        }

        return RecursoDenunciaTO::newInstanceFromEntity($recursoDenuncia);
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
     * Valida o tipo da Denuncia
     * @param $denuncia
     */
    private function validTipoDenuncia(Denuncia $denuncia)
    {
        $tipoDenuncia = false;
        if(
            $denuncia->getTipoDenuncia()->getId() == Constants::TIPO_CHAPA ||
            $denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_CHAPA ||
            $denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_COMISSAO
        )
        {
            $tipoDenuncia = true;
        }
        return $tipoDenuncia;
    }

    /**
     * Valida a quantidade de recurso
     */
    private function countRecurso($isRecurso)
    {
        $quantidade = count($isRecurso);
        if($quantidade == Constants::QUANTIDADE_RECURSO_RECONSIDERACAO)
        {
            throw new NegocioException(Lang::get('messages.denuncia.julgamento.recurso.prazo_solicitacao_recurso_encerrou'));
        }
        return $quantidade;
    }

    /**
     * Cria os nomes de arquivo para Recurso
     *
     * @param RecursoDenuncia $recurso
     * @return RecursoDenuncia
     */
    private function setNomeArquivoFisicoRecurso(RecursoDenuncia $recurso)
    {
        if ($recurso->getArquivos() !== null) {
            foreach ($recurso->getArquivos() as $arquivo) {
                if (empty($arquivo->getId())) {
                    $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                        $arquivo->getNome(),
                        Constants::PREFIXO_DOC_DENUNCIA_RECURSO
                    );
                    $arquivo->setNomeFisico($nomeArquivoFisico);
                }
            }
        }
        return $recurso;
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
     * Método auxiliar para Salvar dados dos Arquivos para a denuncia admitida
     *
     * @param $arquivosRecurso
     * @param RecursoDenuncia $recurso
     * @param Denuncia $denuncia
     * @return RecursoDenuncia
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function salvarArquivosDenunciaRecurso($arquivosRecurso, RecursoDenuncia $recurso, Denuncia $denuncia)
    {
        $arquivosSalvos = new ArrayCollection();
        if (!empty($arquivosRecurso)) {
            /** @var ArquivoRecursoContrarrazaoDenuncia $arquivoDenuncia */
            foreach ($arquivosRecurso as $arquivoDenuncia) {
                $arquivoDenuncia->setRecurso($recurso);
                $arquivoSalvo = $this->arquivoRecursoRepository->persist($arquivoDenuncia);
                $arquivosSalvos->add($arquivoSalvo);
                $this->salvaArquivosDiretorio($arquivoDenuncia, $denuncia);
                $arquivoSalvo->setArquivo(null);
            }
        }
        $recurso->setArquivos($arquivosSalvos);

        return $recurso;
    }

    /**
     * Realiza o(s) upload(s) do(s) arquivo(s) da Denuncia
     *
     * @param $arquivo
     * @param Denuncia $denuncia
     */
    private function salvaArquivosDiretorio($arquivo, Denuncia $denuncia)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioRecursoDenuncia($denuncia->getId());

        if ($arquivo !== null) {
            if (!empty($arquivo->getArquivo())) {
                $this->getArquivoService()->salvar($caminho, $arquivo->getNomeFisico(), $arquivo->getArquivo());
            }
        }
    }
}
