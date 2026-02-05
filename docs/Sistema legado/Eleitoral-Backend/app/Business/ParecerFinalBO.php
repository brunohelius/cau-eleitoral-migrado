<?php
/*
 * ParecerFinalBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoEncaminhamentoDenuncia;
use App\Entities\Denuncia;
use App\Entities\EncaminhamentoDenuncia;
use App\Entities\HistoricoDenuncia;
use App\Entities\MembroChapa;
use App\Entities\ParecerFinal;
use App\Entities\Profissional;
use App\Entities\TipoSentencaJulgamento;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailParecerFinalJob;
use App\Mail\ParecerFinalMail;
use App\Repository\AlegacaoFinalRepository;
use App\Repository\ArquivoEncaminhamentoDenunciaRepository;
use App\Repository\ParecerFinalRepository;
use App\Service\ArquivoService;
use App\Service\CalendarioApiService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\EmailDenunciaTO;
use App\To\ParecerFinalTO;
use App\Util\Email;
use App\Util\Utils;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'ParecerFinal'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class ParecerFinalBO extends AbstractBO
{
    /**
     * @var DenunciaBO
     */
    private $denunciaBO;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var HistoricoDenunciaBO
     */
    private $historicoDenunciaBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var AlegacaoFinalBO
     */
    private $alegacaoFinalBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var \App\Service\ArquivoService
     */
    private $arquivoService;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var \App\Service\CalendarioApiService
     */
    private $calendarioApiService;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaBO;

    /**
     * @var AlegacaoFinalRepository
     */
    private $parecerFinalRepository;

    /**
     * @var EncaminhamentoDenunciaBO
     */
    private $encaminhamentoDenunciaBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var ArquivoEncaminhamentoDenunciaRepository
     */
    private $arquivoEncaminhamentoDenunciaRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->parecerFinalRepository = $this->getRepository(ParecerFinal::class);
    }

    /**
     * Salva o parecer final
     *
     * @param ParecerFinalTO $parecerFinalTO
     * @return ParecerFinalTO
     * @throws \Exception
     */
    public function salvar(ParecerFinalTO $parecerFinalTO)
    {
        $this->validarCamposObrigatoriosParecerFinal($parecerFinalTO);

        $denuncia = $this->getDenunciaBO()->findById($parecerFinalTO->getIdDenuncia());

        $this->validacaoComplementarParecerFinal($parecerFinalTO, $denuncia);

        try {
            $this->beginTransaction();

            $parecerFinal = $this->prepararParecerFinalSalvar($parecerFinalTO, $denuncia);

            $this->parecerFinalRepository->persist($parecerFinal);

            $this->getDenunciaBO()->alterarStatusSituacaoDenuncia(
                $denuncia,
                Constants::STATUS_DENUNCIA_EM_JULGAMENTO_PRIMEIRA_INSTANCIA
            );

            $historicoParecerFinal = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
                Constants::ACAO_HISTORICO_PARECER_FINAL);
            $this->getHistoricoDenunciaBO()->salvar($historicoParecerFinal);

            if (!empty($parecerFinalTO->getArquivos())) {
                $this->salvarArquivos($parecerFinal, $parecerFinalTO->getArquivos());
            }

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if (!empty($parecerFinal)) {
            Utils::executarJOB(new EnviarEmailParecerFinalJob($parecerFinal->getId()));
        }

        return ParecerFinalTO::newInstanceFromEntity($parecerFinal);
    }

    /**
     * Busca Parecer Final utilizando id de Encaminhamento denúncia.
     *
     * @param $idEncaminhamento
     * @return ParecerFinalTO
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorEncaminhamento($idEncaminhamento)
    {
        $parecerFinal =  $this->parecerFinalRepository->getPorEncaminhamento($idEncaminhamento);
        $historico = $this->getHistoricoDenunciaBO()->getHistoricoDenunciaPorDenunciaEAcao($parecerFinal->getEncaminhamentoDenuncia()->getDenuncia()->getId() , Constants::ACAO_HISTORICO_PARECER_FINAL);
        $parecerFinalTO = ParecerFinalTO::newInstanceFromEntity($parecerFinal);
        if($historico) {
            $profissional = $this->getProfissionalBO()->getPorPessoa($historico->getResponsavel());
            $parecerFinalTO->setDataCadastro($historico->getDataHistorico());
            $parecerFinalTO->setUsuarioCadastro($this->getProfissionalBO()->getPorId($profissional->getId()));
        }
        return $parecerFinalTO;
    }

    /**
     * Método auxiliar para preparar entidade ParecerFinal para cadastro
     *
     * @param ParecerFinalTO $parecerFinalTO
     * @param Denuncia $denuncia
     * @return ParecerFinal
     * @throws \Exception
     */
    private function prepararParecerFinalSalvar(ParecerFinalTO $parecerFinalTO, Denuncia $denuncia)
    {
        $encaminhamentoDenuncia = [
            'descricao' => $parecerFinalTO->getDescricao(),
            'data' => Utils::getData(),
            'tipoEncaminhamento' => ["id" => Constants::TIPO_ENCAMINHAMENTO_PARECER_FINAL],
            'tipoSituacaoEncaminhamento' => ['id' => Constants::TIPO_SITUACAO_ENCAMINHAMENTO_CONCLUIDO],
            'idDenuncia' => $denuncia->getId()
        ];

        $parecerFinal = ParecerFinal::newInstance([
            'tipoJulgamento' => ["id" => $parecerFinalTO->getIdTipoJulgamento()],
            'encaminhamentoDenuncia' => $encaminhamentoDenuncia
        ]);

        $parecerFinal->getEncaminhamentoDenuncia()->setDenuncia($denuncia);

        $sequencia = $this->getEncaminhamentoDenunciaBO()->getSequencia($parecerFinal->getEncaminhamentoDenuncia());
        $parecerFinal->getEncaminhamentoDenuncia()->setSequencia($sequencia->getSequencia());

        $parecerFinal->getEncaminhamentoDenuncia()->setMembroComissao(
            $denuncia->getUltimaDenunciaAdmitida()->getMembroComissao()
        );

        if (Constants::TIPO_JULGAMENTO_PROCEDENTE == $parecerFinalTO->getIdTipoJulgamento()) {

            $parecerFinal->setTipoSentencaJulgamento(TipoSentencaJulgamento::newInstance(
                ["id" => $parecerFinalTO->getIdTipoSentencaJulgamento()]
            ));

            $idTipoSentencaJulgamento = $parecerFinalTO->getIdTipoSentencaJulgamento();
            if (Constants::TIPO_SENTENCA_JULGAMENTO_SUSP_PROPAGANDA === $idTipoSentencaJulgamento) {
                $parecerFinal->setQuantidadeDiasSuspensaoPropaganda(
                    $parecerFinalTO->getQuantidadeDias()
                );
            }

            if (Constants::TIPO_SENTENCA_JULGAMENTO_MULTA !== $idTipoSentencaJulgamento) {
                $parecerFinal->setMulta($parecerFinalTO->getMulta());
            }

            if ($parecerFinalTO->getMulta()
                || Constants::TIPO_SENTENCA_JULGAMENTO_MULTA === $idTipoSentencaJulgamento
            ) {
                $parecerFinal->setValorPercentualMulta(
                    $parecerFinalTO->getValorPercentual()
                );
            }
        }

        return $parecerFinal;
    }

    /**
     * Método para validar se há campos obrigatórios não preenchidos no parecer
     *
     * @param ParecerFinalTO $parecerFinalTO
     * @throws NegocioException
     */
    public function validarCamposObrigatoriosParecerFinal(ParecerFinalTO $parecerFinalTO)
    {
        $camposObrigatorios = [
            $parecerFinalTO->getDescricao()
        ];

        if (Constants::TIPO_JULGAMENTO_PROCEDENTE === $parecerFinalTO->getIdTipoJulgamento()) {
            $idTipoSentencaJulgamento = $parecerFinalTO->getIdTipoSentencaJulgamento();

            $camposObrigatorios[] = $idTipoSentencaJulgamento;
            if ($idTipoSentencaJulgamento !== null) {
                if (Constants::TIPO_SENTENCA_JULGAMENTO_SUSP_PROPAGANDA === $idTipoSentencaJulgamento) {
                    $camposObrigatorios[] = $parecerFinalTO->getQuantidadeDias();
                }

                if (Constants::TIPO_SENTENCA_JULGAMENTO_ADVERTENCIA === $idTipoSentencaJulgamento) {
                    $camposObrigatorios[] = $parecerFinalTO->getMulta();
                }

                if ($parecerFinalTO->getMulta()
                    || Constants::TIPO_SENTENCA_JULGAMENTO_MULTA === $idTipoSentencaJulgamento
                ) {
                    $camposObrigatorios[] = $parecerFinalTO->getValorPercentual();
                }
            }
        }

        array_walk($camposObrigatorios, static function ($campoObrigatorio) {
            if ($campoObrigatorio === null) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
            }
        });
    }

    /**
     * Método para realizar as validações complementares para salvar a alegação final
     *
     * @param ParecerFinalTO $parecerFinalTO
     * @param Denuncia $denuncia
     * @throws NegocioException
     */
    public function validacaoComplementarParecerFinal(ParecerFinalTO $parecerFinalTO, Denuncia $denuncia) {

        if (!$this->isUsuarioLogadoPermissaoInserirParecerFinal($denuncia) ||
            $this->isParecerFinalInseridoParaDenuncia($denuncia)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if (!$this->getAlegacaoFinalBO()->isAcaoRelatorParecerFinalDisponivel($denuncia)) {
            $encaminhamentos = $denuncia->getEncaminhamentoDenuncia();

            if ($this->getEncaminhamentoDenunciaBO()->isAlegacaoFinalPendenteDentroPrazo($encaminhamentos)) {
                throw new NegocioException(Lang::get('messages.denuncia.parecer.alegacao_final_nao_respondida'));
            }

            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $arquivos = $parecerFinalTO->getArquivos();
        if (!empty($arquivos)) {
            if (count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO_DENUNCIA) {
                throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO);
            }

            foreach ($arquivos as $arquivo) {
                $this->validarArquivo($arquivo);
            }
        }
    }

    /**
     * Método para realizar a validação se o usuário logado tem permissão de inserir parecer final
     *
     * @param Denuncia $denuncia
     * @return bool
     * @throws \Exception
     */
    public function isUsuarioLogadoPermissaoInserirParecerFinal(Denuncia $denuncia)
    {
        return $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional ==
            $denuncia->getUltimaDenunciaAdmitida()->getMembroComissao()->getProfissionalEntity()->getId();
    }

    /**
     * Método para realizar a validação se já existe parecer final para denúncia
     *
     * @param Denuncia $denuncia
     * @return bool
     * @throws \Exception
     */
    public function isParecerFinalInseridoParaDenuncia(Denuncia $denuncia)
    {
        $isParecer = false;
        $parecer = $this->parecerFinalRepository->getPorIdDenuncia($denuncia->getId());

        if (!empty($parecer)) {
            $isParecer = true;
        }

        return $isParecer;
    }

    /**
     * Método para realizar a validação do arquivo: tamanho e formato
     *
     * @param ArquivoGenericoTO $arquivoGenericoTO
     * @throws NegocioException
     */
    public function validarArquivo(ArquivoGenericoTO $arquivoGenericoTO)
    {
        $this->getArquivoService()->validarArquivoGenrico(
            $arquivoGenericoTO,
            Constants::TP_VALIDACAO_ARQUIVO_FORMATOS_DIVERSOS_MAIXIMO_25MB
        );
    }

    /**
     * Responsável por salvar os arquivos
     *
     * @param ParecerFinal $parecerFinal
     * @param ArquivoGenericoTO[] $arquivos
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function salvarArquivos(ParecerFinal $parecerFinal, $arquivos)
    {
        $arquivosSalvos = [];

        /** @var ArquivoGenericoTO $arquivo */
        foreach ($arquivos as $arquivo) {
            $nomeFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getNome(), Constants::PREFIXO_ARQ_PARECER_FINAL_ENCAMINHAMENTO
            );

            $arquivoEncaminhamento = ArquivoEncaminhamentoDenuncia::newInstance([
                "nome" => $arquivo->getNome(), "nomeFisico" => $nomeFisico
            ]);
            $arquivo->setNomeFisico($nomeFisico);

            $arquivoEncaminhamento->setEncaminhamentoDenuncia($parecerFinal->getEncaminhamentoDenuncia());

            $this->getArquivoEncaminhamentoDenunciaRepository()->persist($arquivoEncaminhamento);
            array_push($arquivosSalvos, $arquivoEncaminhamento);

            $this->salvarArquivoDiretorio($parecerFinal->getId(), $arquivo);
        }
        $parecerFinal->getEncaminhamentoDenuncia()->setArquivoEncaminhamento($arquivosSalvos);
    }

    /**
     * Responsável por salvar os arquivos no diretório
     *
     * @param int $idParecerFinal
     * @param ArquivoGenericoTO $arquivo
     */
    private function salvarArquivoDiretorio(int $idParecerFinal, ArquivoGenericoTO $arquivo)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioParecerFinal($idParecerFinal),
            $arquivo->getNomeFisico(),
            $arquivo->getArquivo()
        );
    }

    /**
     * Disponibiliza o arquivo de 'encaminhamento parecer final' para 'download' conforme o 'id' informado
     *
     * @param $idArquivo
     * @return \App\To\ArquivoTO
     * @throws NegocioException
     */
    public function getArquivo($idArquivo)
    {
        /** @var ArquivoEncaminhamentoDenuncia $arquivoEncaminhamento */
        $arquivoEncaminhamento = $this->getArquivoEncaminhamentoParecerfinalDenuncia($idArquivo);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioParecerFinal($arquivoEncaminhamento->getEncaminhamentoDenuncia()->getParecerFinal()->getId());

        return $this->getArquivoService()->getArquivo($caminho, $arquivoEncaminhamento->getNomeFisico(), $arquivoEncaminhamento->getNome());
    }

    /**
     * Recupera a entidade 'ArquivoEncaminhamentoDenuncia' por meio do 'id' informado.
     *
     * @param $id
     *
     * @return \App\Entities\ArquivoEncaminhamentoDenuncia|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getArquivoEncaminhamentoParecerfinalDenuncia($id)
    {
        $arrayArquivo = $this->getArquivoEncaminhamentoDenunciaRepository()->getPorId($id);
        return $arrayArquivo;
    }

    /**
     * Enviar email ao realizar o cadastro de parecer final
     *
     * @param int $idParecerFinal
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enviarEmailCadastroParecerFinal(int $idParecerFinal)
    {
        $parecerFinal = $this->parecerFinalRepository->find($idParecerFinal);
        $denuncia = $parecerFinal->getEncaminhamentoDenuncia()->getDenuncia();

        $atividade = $this->getAtividadeSecundariaBO()->getPorAtividadeSecundaria(
            $denuncia->getAtividadeSecundaria()->getId(),
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_INSERIR_PARECER_FINAL,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_PARECER_FINAL
        );

        $emailParecerFinalTO = EmailDenunciaTO::newInstanceFromEntity($denuncia);
        $emailParecerFinalTO->setProcessoEleitoral(
            $atividade->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada()
        );
        $emailParecerFinalTO->setStatusDenuncia(
            $this->getDenunciaBO()->getNomeSituacaoAtualDenuncia($denuncia->getId())
        );
        $emailParecerFinalTO->setParecerFinal(ParecerFinalTO::newInstanceFromEntity($parecerFinal));

        $tipos = $this->getTiposEmailCadastroParecerFinal();
        foreach ($tipos as $tipo) {
            $destinarios = $this->getDestinatariosEmail($denuncia, $tipo);

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(), $tipo
            );

            if (!empty($emailAtividadeSecundaria) && !empty($destinarios)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios(array_unique($destinarios));
                Email::enviarMail(new ParecerFinalMail($emailTO, $emailParecerFinalTO));
            }
        }
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

        if ($tipo == Constants::EMAIL_CADASTRO_PARECER_FINAL_DENUNCIADO) {
            $destinatarios = $this->getEncaminhamentoDenunciaBO()->getEmailsDenunciadoPorTipoDenuncia($denuncia);
        }

        if ($tipo == Constants::EMAIL_CADASTRO_PARECER_FINAL_DENUNCIANTE) {
            $destinatarios[] = $denuncia->getPessoa()->getEmail();
        }

        if ($tipo == Constants::EMAIL_CADASTRO_PARECER_FINAL_RELATOR_ATUAL) {
            $destinatarios[] = $denuncia->getUltimaDenunciaAdmitida()->getMembroComissao()->
                getProfissionalEntity()->getPessoa()->getEmail();
        }

        $filial = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::IES_ID;
        if($filial == Constants::IES_ID) {
            $filial = Constants::ID_CAU_BR;
        }
        if ($tipo == Constants::EMAIL_CADASTRO_PARECER_FINAL_COORDENADOR_CE_CEN) {
            $destinatarios = $this->getMembroComissaoBO()->getEmailsCoordenadoresPorIdAtividaSecundaria(
                $denuncia->getAtividadeSecundaria()->getId(), $filial
            );
        }

        if ($tipo == Constants::EMAIL_CADASTRO_PARECER_FINAL_ASSESSORES_CEN_CE) {
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE($filial);
        }

        return $destinatarios;
    }

    /**
     * Retorna os tipos de emails que devem ser enviados ao inserir parecer final
     *
     * @return array
     * @throws \Exception
     */
    public function getTiposEmailCadastroParecerFinal()
    {
        return [
            Constants::EMAIL_CADASTRO_PARECER_FINAL_RELATOR_ATUAL,
            Constants::EMAIL_CADASTRO_PARECER_FINAL_DENUNCIANTE,
            Constants::EMAIL_CADASTRO_PARECER_FINAL_DENUNCIADO,
            Constants::EMAIL_CADASTRO_PARECER_FINAL_COORDENADOR_CE_CEN,
            Constants::EMAIL_CADASTRO_PARECER_FINAL_ASSESSORES_CEN_CE
        ];
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
     * Método para retornar a instância de 'DenunciaBO'
     *
     * @return DenunciaBO
     */
    private function getDenunciaBO(): DenunciaBO
    {
        if (empty($this->denunciaBO)) {
            $this->denunciaBO = app()->make(DenunciaBO::class);
        }
        return $this->denunciaBO;
    }

    /**
     * Método para retornar a instância de 'AtividadeSecundariaCalendarioBO'
     *
     * @return AtividadeSecundariaCalendarioBO
     */
    private function getAtividadeSecundariaBO(): AtividadeSecundariaCalendarioBO
    {
        if (empty($this->atividadeSecundariaBO)) {
            $this->atividadeSecundariaBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }
        return $this->atividadeSecundariaBO;
    }

    /**
     * Retorna uma nova instância de 'ProfissionalBO'.
     *
     * @return ProfissionalBO
     */
    private function getProfissionalBO()
    {
        if (empty($this->profissionalBO)) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }
        return $this->profissionalBO;
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
     * Método para retornar a instancia de 'FilialBO'.
     *
     * @return FilialBO
     */
    private function getFilialBO()
    {
        if (empty($this->filialBO)) {
            $this->filialBO = app()->make(FilialBO::class);
        }
        return $this->filialBO;
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
     * Método para retornar a instancia de Alegação Final BO
     *
     * @return AlegacaoFinalBO
     */
    private function getAlegacaoFinalBO()
    {
        if (empty($this->alegacaoFinalBO)) {
            $this->alegacaoFinalBO = new AlegacaoFinalBO();
        }
        return $this->alegacaoFinalBO;
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
     * Retorna uma instancia de Arquivo Service
     *
     * @return ArquivoService
     * @var \App\Service\ArquivoService
     */
    private function getArquivoService()
    {
        if (empty($this->arquivoService)) {
            $this->arquivoService = new ArquivoService();
        }
        return $this->arquivoService;
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
     * Retorna uma nova instância de 'ArquivoEncaminhamentoDenunciaRepository'.
     *
     * @return ArquivoEncaminhamentoDenunciaRepository
     */
    private function getArquivoEncaminhamentoDenunciaRepository()
    {
        if (empty($this->arquivoEncaminhamentoDenunciaRepository)) {
            $this->arquivoEncaminhamentoDenunciaRepository = $this->getRepository(ArquivoEncaminhamentoDenuncia::class);
        }

        return $this->arquivoEncaminhamentoDenunciaRepository;
    }
}
