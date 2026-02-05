<?php
/*
 * AlegacaoFinalBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AlegacaoFinal;
use App\Entities\Denuncia;
use App\Entities\EncaminhamentoDenuncia;
use App\Entities\Profissional;
use App\Entities\TipoSituacaoEncaminhamentoDenuncia;
use App\Entities\ArquivoAlegacaoFinal;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailAlegacaoFinalCadastrarJob;
use App\Jobs\EnviarEmailAlegacaoFinalPrazoEncerradoJob;
use App\Mail\AlegacoesFinaisCadastradaMail;
use App\Mail\AlegacoesFinaisPrazoEncerradoMail;
use App\Repository\AlegacaoFinalRepository;
use App\Repository\ArquivoAlegacaoFinalRepository;
use App\Repository\EncaminhamentoDenunciaRepository;
use App\Service\ArquivoService;
use App\Service\CalendarioApiService;
use App\Service\CorporativoService;
use App\To\AlegacaoFinalTO;
use App\To\ArquivoGenericoTO;
use App\To\EleicaoTO;
use App\To\EmailEncaminhamentoAlegacaoFinalTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use stdClass;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'EncaminhamentoDenuncia'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class AlegacaoFinalBO extends AbstractBO
{
    /**
     * @var DenunciaBO
     */
    private $denunciaBO;

    /**
     * @var EncaminhamentoDenunciaBO
     */
    private $encaminhamentoDenunciaBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

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
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var DenunciaMembroChapaBO
     */
    private $denunciaMembroChapaBO;

    /**
     * @var DenunciaMembroComissaoBO
     */
    private $denunciaMembroComissaoBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaBO;

    /**
     * @var AlegacaoFinalRepository
     */
    private $alegacaoFinalRepository;
    
    /**
     * @var ArquivoAlegacaoFinalRepository
     */
    private $arquivoAlegacaoFinalRepository;
    
    
    /**
     * @var EncaminhamentoDenunciaRepository
     */
    private $encaminhamentoDenunciaRepository;
    
    
    /**
     * @var \App\Service\ArquivoService
     */
    private $arquivoService;

    /**
     * @var \App\Service\CalendarioApiService
     */
    private $calendarioApiService;

    /**
     * @var HistoricoDenunciaBO
     */
    private $historicoDenunciaBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->alegacaoFinalRepository          = $this->getRepository(AlegacaoFinal::class);
        $this->arquivoAlegacaoFinalRepository   = $this->getRepository(ArquivoAlegacaoFinal::class);
        $this->encaminhamentoDenunciaRepository = $this->getRepository(EncaminhamentoDenuncia::class);
    }

    /**
     * Salva a alegação final do encaminhamento
     *
     * @param AlegacaoFinalTO $alegacaoFinalTO
     * @return AlegacaoFinalTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function salvar(AlegacaoFinalTO $alegacaoFinalTO)
    {
        $this->validarCamposObrigatoriosAlegacaoFinal($alegacaoFinalTO);

        $encaminhamento = $this->getEncaminhamentoDenunciaBO()->findById(
            $alegacaoFinalTO->getIdEncaminhamentoDenuncia()
        );

        $this->validacaoComplementarAlegacaoFinal($alegacaoFinalTO, $encaminhamento);

        try {
            $this->beginTransaction();

            $alegacaoFinal = $this->prepararAlegacaoFinalSalvar($alegacaoFinalTO);

            $this->alegacaoFinalRepository->persist(
                $alegacaoFinal
            );

            //Modifica a situação do encaminhamento para concluído
            $this->getEncaminhamentoDenunciaBO()->alterarTipoSituacaoEncaminhamento(
                $encaminhamento, Constants::TIPO_SITUACAO_ENCAMINHAMENTO_CONCLUIDO
            );

            //Salvar o histórico para denuncia de inserção da alegação final
            $historicoDenuncia = $this->getHistoricoDenunciaBO()->criarHistorico(
                $encaminhamento->getDenuncia(), Constants::ACAO_HISTORICO_ALEGACAO_FINAL
            );
            $this->getHistoricoDenunciaBO()->salvar($historicoDenuncia);

            if (!empty($alegacaoFinalTO->getArquivos())) {
                $this->salvarArquivoDiretorio($alegacaoFinal->getId(), $alegacaoFinalTO->getArquivos());
            }

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailAlegacaoFinalCadastrarJob($encaminhamento->getId()));

        return AlegacaoFinalTO::newInstanceFromEntity($alegacaoFinal);
    }

    /**
     * Método auxiliar para preparar entidade AlegacaoFinal para cadastro
     *
     * @param AlegacaoFinalTO $alegacaoFinalTO
     * @return AlegacaoFinal
     * @throws \Exception
     */
    private function prepararAlegacaoFinalSalvar($alegacaoFinalTO)
    {
        $arquivos = $alegacaoFinalTO->getArquivos();
        $arquivosAlegacaoFinal = null;

        if (!empty($arquivos)) {
            $arquivosAlegacaoFinal = array_map(function ($arquivo) {
                /** @var ArquivoGenericoTO $arquivo */

                $nomeFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                    $arquivo->getNome(), Constants::PREFIXO_ARQ_ALEGACAO_FINAL_ENCAMINHAMENTO
                );

                $arquivo->setNomeFisico($nomeFisico);

                return ["nome" => $arquivo->getNome(), "nomeFisicoArquivo" => $nomeFisico];
            }, $arquivos);
        }

        $alegacaoFinal = AlegacaoFinal::newInstance([
            'descricaoAlegacaoFinal' => $alegacaoFinalTO->getDescricao(),
            'encaminhamentoDenuncia' => ["id" => $alegacaoFinalTO->getIdEncaminhamentoDenuncia()],
            'arquivosAlegacaoFinal' => $arquivosAlegacaoFinal,
            'dataHora' => Utils::getData()
        ]);

        return $alegacaoFinal;
    }

    /**
     * Método para validar se há campos obrigatórios não preenchidos na alegação final
     *
     * @param AlegacaoFinalTO $alegacaoFinalTO
     * @throws NegocioException
     */
    public function validarCamposObrigatoriosAlegacaoFinal(AlegacaoFinalTO $alegacaoFinalTO)
    {
        $campos = [];
        if (empty($alegacaoFinalTO->getIdEncaminhamentoDenuncia())) {
            $campos[] = 'LABEL_ID_ENCAMINHAMENTO_DENUNCIA';
        }
        if (empty($alegacaoFinalTO->getDescricao())) {
            $campos[] = 'LABEL_DESCRICAO';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Método para realizar as validações complementares para salvar a alegação final
     *
     * @param AlegacaoFinalTO $alegacaoFinalTO
     * @param EncaminhamentoDenuncia $encaminhamento
     * @throws NegocioException
     */
    public function validacaoComplementarAlegacaoFinal(
        AlegacaoFinalTO $alegacaoFinalTO,
        EncaminhamentoDenuncia $encaminhamento
    ) {

        if (!$this->isEncaminhamentoAlegacaoFinalPendente($encaminhamento)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if (!$this->isUsuarioLogadoPermissaoInserirAlegacaoFinal($encaminhamento)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        if (!$this->isDataPrazoEnvioAlegacaoFinal($encaminhamento)) {
            throw new NegocioException(Message::MSG_PRAZO_ENVIO_ALEGACOES_FINAIS_ENCERRADO);
        }

        $arquivos = $alegacaoFinalTO->getArquivos();
        if (!empty($arquivos)) {
            if (count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO_DENUNCIA) {
                throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO);
            }

            foreach ($arquivos as $arquivo) {
                /** @var ArquivoGenericoTO $arquivo */
                $this->validarArquivo($arquivo);
            }
        }
    }

    /**
     * Método para realizar a validação se o usuário logado tem permissão de inserir alegação
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     * @return bool
     * @throws \Exception
     */
    public function isUsuarioLogadoPermissaoInserirAlegacaoFinal(EncaminhamentoDenuncia $encaminhamento)
    {
        $isPermissao = false;
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional;
        $denuncia = $encaminhamento->getDenuncia();

        if ($encaminhamento->isDestinoDenunciante() &&
            $denuncia->getPessoa()->getProfissional()->getId() == $usuarioLogado) {
            $isPermissao = true;
        }

        if ($encaminhamento->isDestinoDenunciado()) {

            if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_CHAPA) {
                $isPermissao = $this->getMembroChapaBO()->isMembroResponsavelChapa(
                    $denuncia->getDenunciaChapa()->getChapaEleicao()->getId(), $usuarioLogado
                );
            }

            if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_CHAPA) {
                $isPermissao = $denuncia->getDenunciaMembroChapa()->getMembroChapa()->
                    getProfissional()->getId() == $usuarioLogado;
            }

            if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_COMISSAO) {
                $isPermissao = $denuncia->getDenunciaMembroComissao()->getMembroComissao()->
                    getProfissionalEntity()->getId() == $usuarioLogado;
            }
        }

        return $isPermissao;
    }

    /**
     * Método para realizar a validação se a data está dentro do prazo da alegação final
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     * @return bool
     * @throws \Exception
     */
    public function isDataPrazoEnvioAlegacaoFinal(EncaminhamentoDenuncia $encaminhamento)
    {
        $dataHoje = Utils::getData();
        $ano = Utils::getAnoData($dataHoje);
        $feriados = $this->getCalendarioApiService()
            ->getFeriadosNacionais($ano);
        $data = Utils::adicionarDiasUteisData($encaminhamento->getData(), 1, $feriados );
        $dataLimite = Utils::adicionarDiasData($data, 1);
        if (Utils::getDataHoraZero() > Utils::getDataHoraZero($dataLimite)) {
            return false;
        }

        return true;
    }

    /**
     * Método para realizar a validação se o encaminhamento é do tipo alegação final e situação pendente
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     * @return bool
     * @throws \Exception
     */
    public function isEncaminhamentoAlegacaoFinalPendente(EncaminhamentoDenuncia $encaminhamento)
    {
        if ($encaminhamento->getTipoEncaminhamento()->getId() != Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS) {
            return false;
        }
        if ($encaminhamento->getTipoSituacaoEncaminhamento()->getId() != Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE) {
            return false;
        }

        return true;
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
     * Responsável por salvar os arquivos no diretório
     *
     * @param $idAlegacaoFinal
     * @param ArquivoGenericoTO[] $arquivos
     */
    private function salvarArquivoDiretorio($idAlegacaoFinal, $arquivos)
    {
        foreach ($arquivos as $arquivo) {
            /** @var ArquivoGenericoTO $arquivo */

            $this->getArquivoService()->salvar(
                $this->getArquivoService()->getCaminhoRepositorioAlegacaoFinal($idAlegacaoFinal),
                $arquivo->getNomeFisico(),
                $arquivo->getArquivo()
            );
        }
    }

    /**
     * Verifica se ação de inserir alegações finais do Encaminhamento da Denúncia está disponível
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function isAcaoInserirAlegacoesFinaisEncaminhamento(int $id)
    {
        $encaminhamento = $this->getEncaminhamentoDenunciaBO()->findById($id);

        if (!$this->isEncaminhamentoAlegacaoFinalPendente($encaminhamento) ||
            !$this->isUsuarioLogadoPermissaoInserirAlegacaoFinal($encaminhamento) ||
            !$this->isDataPrazoEnvioAlegacaoFinal($encaminhamento)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Enviar o email após o cadastro da alegações finais
     *
     * @param int $idEncaminhamento
     * @throws \Exception
     */
    public function enviarEmailCadastroAlegacaoFinal(int $idEncaminhamento)
    {
        $encaminhamento = $this->getEncaminhamentoDenunciaBO()->findById($idEncaminhamento);
        $tipos = [
            Constants::EMAIL_CADASTRO_ALEGACOES_FINAIS_ASSESSORES_CEN_CE,
            Constants::EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_ATUAL
        ];

        if ($this->isAcaoRelatorParecerFinalDisponivel($encaminhamento->getDenuncia())) {
            $tipos[] = Constants::EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_SAIBA_PRAZO_CONDICOES_PARECER_FINAL;
        }
        $this->enviarEmailPorTipos($encaminhamento, $tipos);
    }

    /**
     * Enviar o email após o prazo encerrado da alegações finais
     *
     * @param int $idEncaminhamento
     * @throws \Exception
     */
    public function enviarEmailPrazoEncerradoAlegacaoFinal(int $idEncaminhamento)
    {
        $encaminhamento = $this->getEncaminhamentoDenunciaBO()->findById($idEncaminhamento);

        $this->enviarEmailPorTipos($encaminhamento, [
            Constants::EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_DENUNCIA_APOS_ENCERRADO_PRAZO,
            Constants::EMAIL_CADASTRO_ALEGACOES_FINAIS_DESTINATARIO_APOS_ENCERRADO_PRAZO
        ]);
    }

    /**
     * Enviar o email de acordo com o tipo passado
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     * @param array $tipos
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function enviarEmailPorTipos(EncaminhamentoDenuncia $encaminhamento, array $tipos)
    {
        $atividade = $this->getAtividadeSecundariaBO()->getPorAtividadeSecundaria(
            $encaminhamento->getDenuncia()->getAtividadeSecundaria()->getId(),
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_ALEGACOES_FINAIS,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_ALEGACOES_FINAIS
        );

        $emailAlegacaoFinalTO = EmailEncaminhamentoAlegacaoFinalTO::newInstanceFromEntity($encaminhamento);
        $emailAlegacaoFinalTO->setProcessoEleitoral(
            $atividade->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada()
        );
        $emailAlegacaoFinalTO->setStatusDenuncia(
            $this->getDenunciaBO()->getNomeSituacaoAtualDenuncia($encaminhamento->getDenuncia()->getId())
        );

        foreach ($tipos as $tipo) {
            $destinarios = $this->getDestinatariosEmail($encaminhamento, $tipo);

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(), $tipo
            );

            if (!empty($emailAtividadeSecundaria) && !empty($destinarios)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios(array_unique($destinarios));

                if ($tipo == Constants::EMAIL_CADASTRO_ALEGACOES_FINAIS_ASSESSORES_CEN_CE ||
                    $tipo == Constants::EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_ATUAL) {
                    Email::enviarMail(new AlegacoesFinaisCadastradaMail($emailTO, $emailAlegacaoFinalTO));
                } else {
                    Email::enviarMail(new AlegacoesFinaisPrazoEncerradoMail($emailTO, $emailAlegacaoFinalTO));
                }

            }
        }

    }

    /**
     * Retorna os emails dos destinatarios de acordo com o tipo de envio
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     * @param int $tipo
     * @return array
     * @throws \Exception
     */
    public function getDestinatariosEmail(EncaminhamentoDenuncia $encaminhamento, int $tipo)
    {
        if ($tipo == Constants::EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_ATUAL ||
            $tipo == Constants::EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_SAIBA_PRAZO_CONDICOES_PARECER_FINAL ||
            $tipo == Constants::EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_DENUNCIA_APOS_ENCERRADO_PRAZO) {
            $destinatarios[] = $encaminhamento->getDenuncia()->getUltimaDenunciaAdmitida()->getMembroComissao()->
            getProfissionalEntity()->getPessoa()->getEmail();
        }

        if ($tipo == Constants::EMAIL_CADASTRO_ALEGACOES_FINAIS_ASSESSORES_CEN_CE) {
            $filial = !empty($encaminhamento->getDenuncia()->getFilial())
                        ? $encaminhamento->getDenuncia()->getFilial()->getId() : null;
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE($filial);
        }

        if ($tipo == Constants::EMAIL_CADASTRO_ALEGACOES_FINAIS_DESTINATARIO_APOS_ENCERRADO_PRAZO) {
            $encaminhamento->setDestinoDenunciante(true);
            $encaminhamento->setDestinoDenunciado(true);
            $profDestinatarios = $this->getEncaminhamentoDenunciaBO()->getProfissionaisDestinariosEncaminhamento(
                $encaminhamento
            );

            $destinatarios = array_map(function ($profissional) {
                /** @var Profissional $profissional */
                return $profissional->getPessoa()->getEmail();
            }, $profDestinatarios);
        }

        return $destinatarios;
    }

    /**
     * Rotina que verifica os prazos do encaminhamentos e se o parecer final já está disponível ao relator
     *
     * @return void
     * @throws \Exception
     */
    public function rotinaVerificaoEncaminhamentosDenuncia()
    {
        $eleicao = $this->getEleicaoBO()->getEleicaoVigentePorNivelAtividade(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_ALEGACOES_FINAIS,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_ALEGACOES_FINAIS
        );

        if(!empty($eleicao)){
            $denuncias = $this->getDenunciaBO()->getDenunciasEmRelatoriaEncaminhamentoPorTipo(
                Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS, $eleicao->getId()
            );

            if (!empty($denuncias)) {
                foreach ($denuncias as $denuncia) {

                    /** @var Denuncia $denuncia */
                    $encaminhamentos = $denuncia->getEncaminhamentoDenuncia();
                    if (!empty($encaminhamentos)) {

                        foreach ($encaminhamentos as $encaminhamento) {
                            /** @var EncaminhamentoDenuncia $encaminhamento */

                            $idTipoSituacao = $encaminhamento->getTipoSituacaoEncaminhamento()->getId();

                            if ($idTipoSituacao == Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE
                                && !$this->isDataPrazoEnvioAlegacaoFinal($encaminhamento)) {

                                //Modifica a situação do encaminhamento para transcorrido
                                $edEncaminhamento = $this->getEncaminhamentoDenunciaBO()->findById($encaminhamento->getId());
                                $this->getEncaminhamentoDenunciaBO()->alterarTipoSituacaoEncaminhamento(
                                    $edEncaminhamento, Constants::TIPO_SITUACAO_ENCAMINHAMENTO_TRANSCORRIDO
                                );

                                Utils::executarJOB(new EnviarEmailAlegacaoFinalPrazoEncerradoJob($encaminhamento->getId()));
                            }
                        }
                    }

                }
            }
        }

    }

    /**
     * Verifica se a ação parecer final está disponível ao relator
     *
     * @return bool
     * @throws \Exception
     */
    public function isAcaoRelatorParecerFinalDisponivel(Denuncia $denuncia)
    {
        $encaminhamentos = $denuncia->getEncaminhamentoDenuncia();
        $concluidoDenunciante = false;
        $concluidoDenunciado = false;

        if (!empty($encaminhamentos)) {
            foreach ($encaminhamentos as $encaminhamento) {
                /** @var EncaminhamentoDenuncia $encaminhamento */
                $idTipoSituacao = $encaminhamento->getTipoSituacaoEncaminhamento()->getId();

                //Verifica se o encaminhamento para denunciado está concluído com alegação final
                if ((($idTipoSituacao == Constants::TIPO_SITUACAO_ENCAMINHAMENTO_CONCLUIDO && !empty($encaminhamento->getAlegacaoFinal())) ||
                        $idTipoSituacao == Constants::TIPO_SITUACAO_ENCAMINHAMENTO_TRANSCORRIDO) &&
                        $encaminhamento->getTipoEncaminhamento()->getId() == Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS
                ) {

                    if ($encaminhamento->isDestinoDenunciado()) {
                        $concluidoDenunciado = true;
                    }

                    if ($encaminhamento->isDestinoDenunciante()) {
                        $concluidoDenunciante = true;
                    }
                }
            }
        }

        $tipoDenuncia = $denuncia->getTipoDenuncia()->getId();
        if ($concluidoDenunciante && (($tipoDenuncia != Constants::TIPO_OUTROS && $concluidoDenunciado)
                || $tipoDenuncia == Constants::TIPO_OUTROS)) {
            return true;
        }

        return false;
    }
    
    
    /**
     * Disponibiliza o arquivo de 'Alegação final' para 'download' conforme o 'id' informado
     *
     * @param $idArquivo
     * @return \App\To\ArquivoTO
     * @throws NegocioException
     */
    public function getArquivo($idArquivo)
    {
        $arquivoAlegacaoFinal   = $this->getArquivoAlegacaoFinal($idArquivo);
        $caminho                = $this->getArquivoService()->getCaminhoRepositorioAlegacaoFinal($arquivoAlegacaoFinal->getAlegacaoFinal()->getId());
        
        return $this->getArquivoService()->getArquivo($caminho, $arquivoAlegacaoFinal->getNomeFisicoArquivo(), $arquivoAlegacaoFinal->getNome());
    }
    
    
    /**
     * Retorna o Alegação Final dado um determinado id.
     *
     * @param $idEncaminhamento
     * @return AlegacaoFinal|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getPorEncaminhamento($idEncaminhamento)
    {
       $encaminhamento['encaminhamentoDenuncia']    = $this->encaminhamentoDenunciaRepository->getEncaminhamentoPorId($idEncaminhamento);
       $alegacao                                    = $this->alegacaoFinalRepository->getPorEncaminhamento($idEncaminhamento);
       $result                                      = (!empty($alegacao)) ? array_merge($encaminhamento, $alegacao) : $encaminhamento;
       return $result;
    }
    
    
     /**
     * Recupera a entidade 'ArquivoAlegacaoFinal' por meio do 'id' informado.
     *
     * @param $id
     *
     * @return \App\Entities\ArquivoAlegacaoFinal|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getArquivoAlegacaoFinal($id)
    {
        $arrayArquivo = $this->arquivoAlegacaoFinalRepository->getPorId($id);
        return $arrayArquivo;
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
     * Retorna uma nova instância de 'DenunciaMembroChapaBO'.
     *
     * @return DenunciaMembroChapaBO
     */
    private function getDenunciaMembroChapaBO()
    {
        if (empty($this->denunciaMembroChapaBO)) {
            $this->denunciaMembroChapaBO = app()->make(DenunciaMembroChapaBO::class);
        }
        return $this->denunciaMembroChapaBO;
    }

    /**
     * Retorna uma nova instância de 'DenunciaMembroComissaoBO'.
     *
     * @return DenunciaMembroComissaoBO
     */
    private function getDenunciaMembroComissaoBO()
    {
        if (empty($this->denunciaMembroChapaBO)) {
            $this->denunciaMembroComissaoBO = app()->make(DenunciaMembroComissaoBO::class);
        }
        return $this->denunciaMembroComissaoBO;
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
}
