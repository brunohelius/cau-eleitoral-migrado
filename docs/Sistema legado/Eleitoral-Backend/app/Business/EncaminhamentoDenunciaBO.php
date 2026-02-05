<?php
/*
 * EncaminhamentoDenunciaBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AgendamentoEncaminhamentoDenuncia;
use App\Entities\AlegacaoFinal;
use App\Entities\ArquivoDenunciaProvas;
use App\Entities\ArquivoEncaminhamentoDenuncia;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Denuncia;
use App\Entities\DenunciaProvas;
use App\Entities\EncaminhamentoDenuncia;
use App\Entities\Filial;
use App\Entities\HistoricoDenuncia;
use App\Entities\ImpedimentoSuspeicao;
use App\Entities\MembroChapa;
use App\Entities\ParecerFinal;
use App\Entities\Profissional;
use App\Entities\MembroComissao;
use App\Entities\TipoEncaminhamento;
use App\Entities\TipoSituacaoEncaminhamentoDenuncia;
use App\Entities\DenunciaAudienciaInstrucao;
use App\Entities\ArquivoDenunciaAdmitida;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\UsuarioFactory;
use App\Http\Controllers\EncaminhamentoDenunciaController;
use App\Jobs\EnviarEmailEncaminhamentoDenunciaJob;
use App\Mail\EncaminhamentoAlegacoesFinaisMail;
use App\Repository\AgendamentoEncaminhamentoDenunciaRepository;
use App\Repository\AlegacaoFinalRepository;
use App\Repository\ArquivoEncaminhamentoDenunciaRepository;
use App\Repository\DenunciaRepository;
use App\Repository\DenunciaProvasRepository;
use App\Repository\DenunciaAudienciaInstrucaoRepository;
use App\Repository\EncaminhamentoDenunciaRepository;
use App\Repository\MembroComissaoRepository;
use App\Repository\TipoEncaminhamentoRepository;
use App\Repository\TipoSituacaoEncaminhamentoDenunciaRepository;
use App\Repository\ArquivoDenunciaAdmitidaRepository;
use App\Service\ArquivoService;
use App\Service\CalendarioApiService;
use App\Service\CorporativoService;
use App\To\ArquivoDescricaoTO;
use App\To\EmailEncaminhamentoAlegacaoFinalTO;
use App\To\ExportarParecerTO;
use App\To\ListagemEncaminhamentoDenunciaTO;
use App\To\ParecerEncaminhamentoDenunciaTO;
use App\To\UsuarioTO;
use App\To\EncaminhamentoDenunciaProvasTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use stdClass;

/**
 * Classe responsável por encapsular as implementações de negócio referente a
 * entidade 'EncaminhamentoDenuncia'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class EncaminhamentoDenunciaBO extends AbstractBO
{
    /**
     * @var DenunciaBO
     */
    private $denunciaBO;

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
     * @var TipoEncaminhamentoRepository
     */
    private $tipoEncaminhamentoRepository;

    /**
     * @var EncaminhamentoDenunciaRepository
     */
    private $encaminhamentoDenunciaRepository;

    /**
     * @var ArquivoDenunciaAdmitidaRepository
     */
    private $arquivoDenunciaAdmitidaRepository;

    /**
     * @var \App\Service\ArquivoService
     */
    private $arquivoService;

    /**
     * @var \App\Service\CalendarioApiService
     */
    private $calendarioApiService;

    /**
     * @var TipoSituacaoEncaminhamentoDenunciaRepository
     */
    private $tipoSituacaoEncaminhamentoRepository;

    /**
     * @var \App\Repository\ProfissionalRepository
     */
    private $profissionalRepository;

    /**
     * @var \App\Repository\FilialRepository
     */
    private $filialRepository;

    /**
     * @var MembroComissaoRepository
     */
    private $membroComissaoRepository;

    /**
     * @var DenunciaRepository
     */
    private $denunciaRepository;

    /**
     * @var DenunciaProvasRepository
     */
    private $denunciaProvasRepository;

    /**
     * @var DenunciaAudienciaInstrucaoRepository
     */
    private $denunciaAudienciaInstrucaoRepository;

    /**
     * @var HistoricoDenunciaBO
     */
    private $historicoDenunciaBO;

    /**
     * @var ArquivoEncaminhamentoDenunciaRepository
     */
    private $arquivoEncaminhamentoRepository;

    /**
     * @var AgendamentoEncaminhamentoDenunciaRepository
     */
    private $agendamentoEncaminhamentoRepository;

    /**
     * @var AlegacaoFinalRepository
     */
    private $alegacaoFinalRepository;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var AlegacaoFinalBO
     */
    private $alegacaoFinalBO;

    /**
     * @var UsuarioFactory
     */
    private $usuarioFactory;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->filialRepository = $this->getRepository(Filial::class);
        $this->denunciaRepository = $this->getRepository(Denuncia::class);
        $this->profissionalRepository = $this->getRepository(Profissional::class);
        $this->membroComissaoRepository = $this->getRepository(MembroComissao::class);
        $this->tipoEncaminhamentoRepository = $this->getRepository(TipoEncaminhamento::class);
        $this->encaminhamentoDenunciaRepository = $this->getRepository(EncaminhamentoDenuncia::class);
        $this->tipoSituacaoEncaminhamentoRepository = $this->getRepository(TipoSituacaoEncaminhamentoDenuncia::class);
        $this->membroComissaoRepository = $this->getRepository(MembroComissao::class);
        $this->denunciaRepository = $this->getRepository(Denuncia::class);
        $this->denunciaProvasRepository = $this->getRepository(DenunciaProvas::class);
        $this->arquivoEncaminhamentoRepository = $this->getRepository(ArquivoEncaminhamentoDenuncia::class);
        $this->agendamentoEncaminhamentoRepository = $this->getRepository(AgendamentoEncaminhamentoDenuncia::class);
        $this->alegacaoFinalRepository = $this->getRepository(AlegacaoFinal::class);
        $this->denunciaAudienciaInstrucaoRepository = $this->getRepository(DenunciaAudienciaInstrucao::class);
        $this->arquivoDenunciaAdmitidaRepository = $this->getRepository(ArquivoDenunciaAdmitida::class);
    }

    /**
     * Retorna um array com todos os tipos de encaminhamento ordenados por Id.
     *
     * @param array $filtro
     * @return array
     */
    public function getTiposEncaminhamento($filtro = [])
    {
        $tipos = $this->tipoEncaminhamentoRepository->findBy($filtro, ['id' => 'ASC']);

        return array_map(function (TipoEncaminhamento $tipoEncaminhamento){
            if ($tipoEncaminhamento->getId() != Constants::TIPO_ENCAMINHAMENTO_PARECER_FINAL) {
                return $tipoEncaminhamento;
            }
        }, $tipos);
    }

    /**
     * Retorna um array com todos os tipos de encaminhamento ordenados por Id.
     *
     * @return array
     */
    public function getTiposEncaminhamentoPorDenuncia($idDenuncia)
    {
        $encaminhamentos = $this->encaminhamentoDenunciaRepository->getEncaminhamentosPorDenuncia($idDenuncia);

        $alegacoesFinais = array_filter($encaminhamentos, static function ($encaminhamento) {
            $tipoEncaminhamento = Utils::getValue('tipoEncaminhamento.id', $encaminhamento);
            return $tipoEncaminhamento === Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS;
        });

        $filtroTiposEncaminhamento = !empty($alegacoesFinais)
            ? ['id' => Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS]
            : [];

        return $this->getTiposEncaminhamento($filtroTiposEncaminhamento);
    }

    /**
     * Retorna o Encaminhamento de acordo com o ID informado.
     *
     * @param $id
     * @return EncaminhamentoDenuncia|object
     */
    public function getEncaminhamentoDenunciaPorId($id)
    {
        return $this->encaminhamentoDenunciaRepository->find($id);
    }

    /**
     * Retorna o encaminhamento conforme o id informado.
     *
     * @param $id
     * @return EncaminhamentoDenuncia|null
     */
    public function findById($id)
    {
        return $this->encaminhamentoDenunciaRepository->find($id);
    }

    /**
     * Valida se encaminhamento do tipo “Impedimento ou Suspeição” com status
     * “Pendente” para a denúncia
     *
     * @param $idDenuncia
     * @return string
     * @throws NegocioException
     */
    public function validarImpedimentoPendente($idDenuncia)
    {
        $encaminhamentos = $this->encaminhamentoDenunciaRepository->findBy(['denuncia' => $idDenuncia]);

        if (!empty($encaminhamentos)) {
            if (is_array($encaminhamentos)) {
                foreach ($encaminhamentos as $encaminhamento) {
                    if (($encaminhamento->getTipoEncaminhamento()->getId() == Constants::TIPO_ENCAMINHAMENTO_IMPEDIMENTO_SUSPEICAO)
                        and ($encaminhamento->getTipoSituacaoEncaminhamento()->getId() == Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE)) {
                        throw new NegocioException(Message::MSG_ENCAMINHAMENTO_DENUNCIA_PENDENTE);
                    }
                }
            }
        }
        return "OK";
    }

    /**
     * Valida se encaminhamento do tipo “audiência de instrução” com status
     * “Pendente” para a denúncia
     *
     * @param $idDenuncia
     * @return string
     */
    public function validarAudienciaInstrucaoPendente($idDenuncia)
    {
        $encaminhamentosPendentes = [];
        $encaminhamentos = $this->encaminhamentoDenunciaRepository->findBy(['denuncia' => $idDenuncia]);

        if (!empty($encaminhamentos) && is_array($encaminhamentos)) {
            $encaminhamentosPendentes = array_filter($encaminhamentos, static function (EncaminhamentoDenuncia $encaminhamento) {
                $hasTipoEncaminhamento = $encaminhamento
                        ->getTipoEncaminhamento()->getId() == Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO;

                $isEncaminhamentoPendente = $encaminhamento
                        ->getTipoSituacaoEncaminhamento()->getId() === Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE;

                return $hasTipoEncaminhamento && $isEncaminhamentoPendente;
            });
        }

        return $encaminhamentosPendentes;
    }

    /**
     * Retorna os encaminhamentos do "tipo produção de provas" e“audiência de
     * instrução” com status
     * “Pendente” para a denúncia
     *
     * @param $idDenuncia
     * @return mixed
     */
    public function getEncaminhamentosProducaoProvasAudienciaInstrucaoPendente($idDenuncia)
    {
        $sequenciasEncaminhamentos = [];
        $encaminhamentos = $this->encaminhamentoDenunciaRepository->findBy(['denuncia' => $idDenuncia]);

        if (!empty($encaminhamentos) && is_array($encaminhamentos)) {
            $encaminhamentosPendentes = array_filter($encaminhamentos, static function (EncaminhamentoDenuncia $encaminhamento) {
                $hasTipoEncaminhamento = in_array(
                    $encaminhamento->getTipoEncaminhamento()->getId(),
                    [
                        Constants::TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS,
                        Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO,
                    ], true);

                $isEncaminhamentoPendente = $encaminhamento
                        ->getTipoSituacaoEncaminhamento()->getId() === Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE;

                return $hasTipoEncaminhamento && $isEncaminhamentoPendente;
            });

            if (!empty($encaminhamentosPendentes)) {
                /** @var EncaminhamentoDenuncia $encaminhamentoPendente */
                foreach ($encaminhamentosPendentes as $encaminhamentoPendente) {
                    $sequenciasEncaminhamentos[] = $encaminhamentoPendente->getSequencia();
                }

                sort($sequenciasEncaminhamentos);
            }
        }

        return $sequenciasEncaminhamentos;
    }

    /**
     * Salva o encaminhamento da denuncia.
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     *
     * @return stdClass
     * @throws \Exception
     * @throws \App\Exceptions\NegocioException
     */
    public function salvar(EncaminhamentoDenuncia $encaminhamento)
    {
        $encaminhamentoSalvo = null;
        $this->validarCamposObrigatorios($encaminhamento);
        $this->validarQuantidadeArquivos($encaminhamento);
        $this->validarDataInvalida($encaminhamento);
        $this->validarDataAgendamento($encaminhamento);

        $encaminhamento = $this->setNomeArquivoFisico($encaminhamento);
        $arquivos = !empty($encaminhamento->getArquivoEncaminhamento())
            ? clone $encaminhamento->getArquivoEncaminhamento()
            : null;
        $encaminhamento->setArquivoEncaminhamento(null);

        $denuncia = null;
        $agendamento = null;
        $encaminhamentoSalvo = null;
        $encaminhamentoDestinatariosSalvo = null;

        try {
            $this->beginTransaction();

            //Setar o objeto do tipo de encaminhamento
            $tipoEncaminhamento = $this->tipoEncaminhamentoRepository->find($encaminhamento->getTipoEncaminhamento()->getId());
            $encaminhamento->setTipoEncaminhamento($tipoEncaminhamento);

            //Gerar um número sequencial para o encaminhamento (Ex: 01, 02);
            $encaminhamento = $this->getSequencia($encaminhamento);

            //Data e hora do encaminhamento;
            $encaminhamento->setData(Utils::getData());

            //Registra o relator;
            $denuncia = $this->denunciaRepository->find($encaminhamento->getIdDenuncia());
            $encaminhamento->setDenuncia($denuncia);

            $filial = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::IES_ID;
            $membroComissao = $this->getMembroComissaoPorProfissionalUf($filial);
            $encaminhamento->setMembroComissao($membroComissao);

            //Valor “Pendente” para o campo “Status” do encaminhamento do parecer
            $tipoSituacaoEncaminhamento = $this->tipoSituacaoEncaminhamentoRepository->find(Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE);
            $encaminhamento->setTipoSituacaoEncaminhamento($tipoSituacaoEncaminhamento);

            $idTipoEncaminhamento = $encaminhamento->getTipoEncaminhamento()->getId();

            if ($idTipoEncaminhamento === Constants::TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS) {
                if($encaminhamento->isDestinoDenunciado()) {
                    $this->validaMembroComissaoChapaEncaminhamento($denuncia);
                }
                if($encaminhamento->isDestinoDenunciante()) {
                    $this->validarDenuncianteEncaminhamento($denuncia);
                }
            }

            if ($idTipoEncaminhamento === Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO) {
                $agendamento = !empty($encaminhamento->getAgendamentoEncaminhamento())
                    ? $encaminhamento->getAgendamentoEncaminhamento()[0]
                    : null;
                $encaminhamento->setAgendamentoEncaminhamento(null);
            }

            if ($idTipoEncaminhamento === Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS) {
                $tipoDenuncia = $denuncia->getTipoDenuncia()->getId();
                $encaminhamentosAlegacoesFinais = $this->getEncaminhamentosAlegacoesFinais($denuncia);

                $this->validaAlegacaoFinalPendente($tipoDenuncia, $encaminhamentosAlegacoesFinais);

                if (($tipoDenuncia === Constants::TIPO_OUTROS && $encaminhamentosAlegacoesFinais->hasConcluidasToDenunciante)
                    || ($encaminhamentosAlegacoesFinais->hasConcluidasToDenunciado && $encaminhamentosAlegacoesFinais->hasConcluidasToDenunciante)
                ) {
                    throw new NegocioException(Message::MSG_NAO_POSSIVEL_INCLUIR_ENCAMINHAMENTO_ALEGACAO_FINAL_JA_RESPONDIDA_PELAS_PARTES);
                }

                $this->alteraEncaminhamentosProducaoProvasAudienciaInstrucaoPendentesParaFechado($encaminhamento);
                $this->setEncaminhamentoAlegacoesFinaisPendentes($encaminhamento, $encaminhamentosAlegacoesFinais);

                if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_OUTROS) {
                    $encaminhamento->setDestinoDenunciado(false);
                }
            }

            // Se o encaminhamento for para denunciado E denunciante, deve-se duplicar os registros e salvar os 2
            if ($encaminhamento->isDestinoDenunciado() && $encaminhamento->isDestinoDenunciante()) {
                $encaminhamentoDestinatariosSalvo = $this->criarEncaminhamentosDenuncianteDenunciado($encaminhamento);
            }

            $encaminhamentoSalvo = $this->encaminhamentoDenunciaRepository->persist($encaminhamento);

            // Salva o Agendamento caso seja Audiencia de Instrução
            if (!empty($agendamento)) {
                $encaminhamento->setDestinoDenunciante(true);
                if ($encaminhamento->isDestinoDenunciado() && $encaminhamento->isDestinoDenunciante()) {
                    $agendamentoDenunciante = clone $agendamento;
                    $agendamentoDenunciante->setEncaminhamentoDenuncia($encaminhamentoDestinatariosSalvo);

                    $this->agendamentoEncaminhamentoRepository->persist($agendamentoDenunciante);
                }
                $agendamento->setEncaminhamentoDenuncia($encaminhamentoSalvo);
                $this->agendamentoEncaminhamentoRepository->persist($agendamento);
            }

            //Salvar os arquivos do Encaminhamento da Denuncia
            if (!empty($arquivos)) {
                $this->salvarArquivos($arquivos, $encaminhamentoSalvo, $denuncia);

                if (empty($agendamento) && null !== $encaminhamentoDestinatariosSalvo) {
                    $newFiles = $this->cloneArquivos($arquivos);
                    $this->salvarArquivos($newFiles, $encaminhamentoDestinatariosSalvo, $denuncia);
                }
            }

            //Salvar o histórico para denuncia com a descrição do tipo de encaminhamento
            $historicoDenuncia = $this->getHistoricoDenunciaBO()->criarHistorico(
                $denuncia,
                $encaminhamento->getTipoEncaminhamento()->getDescricao()
            );

            $this->getHistoricoDenunciaBO()->salvar($historicoDenuncia);

            if (!empty($encaminhamentoSalvo)) {
                Utils::executarJOB(new EnviarEmailEncaminhamentoDenunciaJob($encaminhamentoSalvo->getId()));
            }

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        return $this->getRetornoSalvar($encaminhamentoSalvo);
    }

    /**
     * Metodo para clonar um array de ArquivoEncaminhamentoDenuncia
     * @param array|ArrayCollection $arquivos
     * @return array
     */
    public function cloneArquivos($arquivos) {
        $cloneFiles = null;
        if(!empty($arquivos)) {
            foreach ($arquivos as $arquivo) {
                $clonedFile = clone $arquivo;
                $clonedFile->setId(null);
                $cloneFiles[] = $clonedFile;
            }
        }
        return $cloneFiles;
    }

    /**
     * Cria os encaminhamentos para denunciante e/ou denunciado e retorna o
     * encaminhamento do denunciante.
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     *
     * @return \App\Entities\Entity|array|bool|\Doctrine\Common\Persistence\ObjectManagerAware|object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function criarEncaminhamentosDenuncianteDenunciado(EncaminhamentoDenuncia $encaminhamento)
    {
        $sequenciaDenunciado = $encaminhamento->getSequencia();

        $encaminhamento->setDestinoDenunciante(false);
        $encaminhamento->setSequencia($sequenciaDenunciado + 1);

        $encaminhamentoDenunciante = clone $encaminhamento;
        $encaminhamentoDenunciante->setDestinoDenunciado(false);
        $encaminhamentoDenunciante->setDestinoDenunciante(true);
        $encaminhamentoDenunciante->setSequencia($sequenciaDenunciado);

        return $this->encaminhamentoDenunciaRepository->persist($encaminhamentoDenunciante);
    }

    /**
     * Recupera o Tipo de Situação de Encaminhamento Por ID.
     *
     * @param $idTipoSituacao
     * @return TipoSituacaoEncaminhamentoDenuncia
     */
    public function getTipoSituacaoEncaminhamentoPorId($idTipoSituacao)
    {
        return $this->tipoSituacaoEncaminhamentoRepository->find($idTipoSituacao);
    }

    /**
     * Seta o encaminhamento para denunciante e/ou denunciado.
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     * @param \stdClass $alegacoesFinais
     */
    private function setEncaminhamentoAlegacoesFinaisPendentes(EncaminhamentoDenuncia $encaminhamento, \stdClass $alegacoesFinais): void
    {
        if (!$alegacoesFinais->hasConcluidasToDenunciado && !$alegacoesFinais->hasConcluidasToDenunciante) {
            $encaminhamento->setDestinoDenunciado(true);
            $encaminhamento->setDestinoDenunciante(true);
        }

        if (!$alegacoesFinais->hasConcluidasToDenunciado && $alegacoesFinais->hasConcluidasToDenunciante) {
            $encaminhamento->setDestinoDenunciado(true);
            $encaminhamento->setDestinoDenunciante(false);
        }

        if ($alegacoesFinais->hasConcluidasToDenunciado && !$alegacoesFinais->hasConcluidasToDenunciante) {
            $encaminhamento->setDestinoDenunciado(false);
            $encaminhamento->setDestinoDenunciante(true);
        }
    }

    /**
     * Valida se os campos obrigatórios estão preenchidos
     *
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     * @throws NegocioException
     */
    private function validarCamposObrigatorios(EncaminhamentoDenuncia $encaminhamentoDenuncia)
    {
        $campos = [];

        if (empty($encaminhamentoDenuncia->getIdDenuncia())) {
            $campos[] = 'LABEL_DENUNCIA';
        }

        if (empty($encaminhamentoDenuncia->getDescricao())) {
            $campos[] = 'LABEL_DS_ENCAMINHAMENTO';
        }

        if (empty($encaminhamentoDenuncia->getTipoEncaminhamento())) {
            $campos[] = 'LABEL_TIPO_ENCAMINHAMENTO';
        }

        // Arquivo ???

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Valida a quantidade de arquivos para o Encaminhamento
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     * @throws NegocioException
     */
    private function validarQuantidadeArquivos(EncaminhamentoDenuncia $encaminhamento)
    {
        $arquivos = $encaminhamento->getArquivoEncaminhamento();
        if (!empty($arquivos) and count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO_DENUNCIA) {
            throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO);
        }
    }

    /**
     * Cria os nomes de arquivo para Encaminhamento
     *
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     * @return EncaminhamentoDenuncia
     */
    private function setNomeArquivoFisico(EncaminhamentoDenuncia $encaminhamentoDenuncia)
    {
        if ($encaminhamentoDenuncia->getArquivoEncaminhamento() != null) {
            foreach ($encaminhamentoDenuncia->getArquivoEncaminhamento() as $arquivo) {
                if (empty($arquivo->getId())) {
                    $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                        $arquivo->getNome(),
                        Constants::PREFIXO_DOC_ENCAMINHAMENTO_DENUNCIA
                    );
                    $arquivo->setNomeFisico($nomeArquivoFisico);
                }
            }
        }
        return $encaminhamentoDenuncia;
    }

    /**
     * Cria um número sequencial para o Encaminhamento
     *
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     * @return EncaminhamentoDenuncia
     */
    public function getSequencia(EncaminhamentoDenuncia $encaminhamentoDenuncia)
    {
        $listaEncaminhamento = $this->encaminhamentoDenunciaRepository->findBy(['denuncia' => $encaminhamentoDenuncia->getIdDenuncia()], ['id' => 'desc']);

        $encaminhamentoDenuncia->setSequencia(1);
        if (!empty($listaEncaminhamento)) {
            $encaminhamentoBD = $listaEncaminhamento[0];
            $encaminhamentoDenuncia->setSequencia($encaminhamentoBD->getSequencia() + 1);
        }
        return $encaminhamentoDenuncia;
    }

    /**
     * Retorna o Relator, que é o membro da comissão. Por Profissional e por ID
     * CAU UF
     *
     * @param $idCauUf
     * @return MembroComissao|null
     */
    private function getMembroComissaoPorProfissionalUf($idCauUf)
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();

        $filtroTO = new \stdClass();
        $filtroTO->idCauUf = $idCauUf;
        $filtroTO->idProfissional = $usuario->idProfissional;

        $membros = $this->membroComissaoRepository->getPorFiltro($filtroTO);

        if (!empty($membros)) {
            return $membros[0];
        }
        return null;
    }

    /**
     * Retorna os encaminhamentos da denuncia com status de alegação final
     * concluída.
     *
     * @param Denuncia $denuncia
     * @return \stdClass
     */
    public function getEncaminhamentosAlegacoesFinais($denuncia)
    {
        $encaminhamentos = $denuncia->getEncaminhamentoDenuncia() ?? [];
        if (!is_array($encaminhamentos)) {
            $encaminhamentos = $encaminhamentos->toArray();
        }

        $alegacoesFinaisPendentes = array_filter($encaminhamentos, static function (EncaminhamentoDenuncia $encaminhamento) {
            $isSituacaoPendente = $encaminhamento->getTipoSituacaoEncaminhamento()
                    ->getId() === Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE;
            $isAlegacoesFinais = $encaminhamento->getTipoEncaminhamento()
                    ->getId() === Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS;

            return $isAlegacoesFinais && $isSituacaoPendente;
        });

        $alegacoesFinaisDenunciadoPendentes = array_filter($alegacoesFinaisPendentes, static function (EncaminhamentoDenuncia $encaminhamento) {
            return $encaminhamento->isDestinoDenunciado();
        });
        $alegacoesFinaisDenunciantePendentes = array_filter($alegacoesFinaisPendentes, static function (EncaminhamentoDenuncia $encaminhamento) {
            return $encaminhamento->isDestinoDenunciante();
        });

        $alegacoesFinaisConcluidas = array_filter($encaminhamentos, static function (EncaminhamentoDenuncia $encaminhamento) {
            $isSituacaoConcluido = $encaminhamento->getTipoSituacaoEncaminhamento()
                    ->getId() === Constants::TIPO_SITUACAO_ENCAMINHAMENTO_CONCLUIDO;
            $isAlegacoesFinais = $encaminhamento->getTipoEncaminhamento()
                    ->getId() === Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS;

            return $isAlegacoesFinais && $isSituacaoConcluido;
        });

        $alegacoesFinaisDenunciado = array_filter($alegacoesFinaisConcluidas, static function (EncaminhamentoDenuncia $encaminhamento) {
            return $encaminhamento->isDestinoDenunciado();
        });
        $alegacoesFinaisDenunciante = array_filter($alegacoesFinaisConcluidas, static function (EncaminhamentoDenuncia $encaminhamento) {
            return $encaminhamento->isDestinoDenunciante();
        });

        $alegacoesFinais = new \stdClass();
        $alegacoesFinais->hasPendentes = !empty($alegacoesFinaisPendentes);
        $alegacoesFinais->hasConcluidas = !empty($alegacoesFinaisConcluidas);
        $alegacoesFinais->hasConcluidasToDenunciado = !empty($alegacoesFinaisDenunciado);
        $alegacoesFinais->hasConcluidasToDenunciante = !empty($alegacoesFinaisDenunciante);
        $alegacoesFinais->hasPendentesToDenunciado = !empty($alegacoesFinaisDenunciadoPendentes);
        $alegacoesFinais->hasPendentesToDenunciante = !empty($alegacoesFinaisDenunciantePendentes);
        return $alegacoesFinais;
    }

    /**
     * @param EncaminhamentoDenuncia $encaminhamento
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    private function alteraEncaminhamentosProducaoProvasAudienciaInstrucaoPendentesParaFechado(EncaminhamentoDenuncia $encaminhamento)
    {
        $encaminhamentosPendentes = $this->encaminhamentoDenunciaRepository->findBy([
            'denuncia' => $encaminhamento->getIdDenuncia(),
            'tipoEncaminhamento' => [
                Constants::TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS,
                Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO,
            ],
            'tipoSituacaoEncaminhamento' => Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE,
        ]);

        $dataFechamento = Utils::getData();
        $tipoSituacaoEncaminhamentoFechado = TipoSituacaoEncaminhamentoDenuncia::newInstance([
            'id' => Constants::TIPO_SITUACAO_ENCAMINHAMENTO_FECHADO,
        ]);

        array_walk($encaminhamentosPendentes, static function (
            EncaminhamentoDenuncia $encaminhamentoJustificado
        ) use ($encaminhamento, $tipoSituacaoEncaminhamentoFechado, $dataFechamento) {
            $encaminhamentoJustificado->setDataFechamento($dataFechamento);
            $encaminhamentoJustificado->setJustificativa($encaminhamento->getJustificativa());
            $encaminhamentoJustificado->setTipoSituacaoEncaminhamento($tipoSituacaoEncaminhamentoFechado);
            return $encaminhamentoJustificado;
        });

        $this->encaminhamentoDenunciaRepository->persistEmLote($encaminhamentosPendentes);
    }

    /**
     * @param $tipoDenuncia
     * @param $alegacoesFinais
     *
     * @throws \App\Exceptions\NegocioException
     */
    private function validaAlegacaoFinalPendente($tipoDenuncia, $alegacoesFinais)
    {
        $alegacoesFinaisTipoOutrosDenunciante = $tipoDenuncia === Constants::TIPO_OUTROS && $alegacoesFinais->hasPendentesToDenunciante;

        if ($alegacoesFinaisTipoOutrosDenunciante || $alegacoesFinais->hasPendentes) {
            throw new NegocioException(Message::MSG_EXISTE_SOLIC_ALEGACOES_FINAIS_PENDENTES_RESP_DESTINATARIOS);
        }
    }

    /**
     * REGRA: Caso o destinatário seja o denunciado e já exista um
     * encaminhamento “Produção de provas” pendente para ele na mesma denúncia,
     * então o sistema impede o cadastro e exibe a mensagem ME04.
     *
     * @param Denuncia $denuncia
     * @throws NegocioException
     */
    private function validaMembroComissaoChapaEncaminhamento(Denuncia $denuncia)
    {
        if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_CHAPA
            or $denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_COMISSAO) {
            $filtroTO = new \stdClass();
            $filtroTO->tipoDenuncia = $denuncia->getTipoDenuncia()->getId();

            if ($filtroTO->tipoDenuncia == Constants::TIPO_MEMBRO_CHAPA) {
                $filtroTO->idMembro = $denuncia->getDenunciaMembroChapa()->getMembroChapa()->getId();
            } else if ($filtroTO->tipoDenuncia == Constants::TIPO_MEMBRO_COMISSAO) {
                $filtroTO->idMembro = $denuncia->getDenunciaMembroComissao()->getMembroComissao()->getId();
            }
            $resp = $this->encaminhamentoDenunciaRepository->validaMembroComissaoChapaEncaminhamento($filtroTO);

            if (!empty($resp)) {
                throw new NegocioException(Message::MSG_EXISTEM_PROVAS_DESTINATARIO_DENUNCIA);
            }
        }
    }

    /**
     * Caso o destinatário seja o denunciante e já exista um encaminhamento
     * “Produção de provas” pendente para ele na mesma denúncia, então o
     * sistema impede o cadastro e exibe a mensagem ME04;
     *
     * @param Denuncia $denuncia
     * @throws NegocioException
     */
    private function validarDenuncianteEncaminhamento(Denuncia $denuncia)
    {
        $filtroTO = new \stdClass();
        $filtroTO->idPessoa = $denuncia->getPessoa()->getId();
        $filtroTO->idDenuncia = $denuncia->getId();
        $resp = $this->encaminhamentoDenunciaRepository->validarDenuncianteEncaminhamento($filtroTO);

        if (!empty($resp)) {
            throw new NegocioException(Message::MSG_EXISTEM_PROVAS_DESTINATARIO_DENUNCIA);
        }
    }

    /**
     * Cria um objeto para organizar o retorno de sucesso do método Salvar
     *
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     * @return stdClass
     */
    private function getRetornoSalvar(EncaminhamentoDenuncia $encaminhamentoDenuncia)
    {
        $retorno = new \stdClass();
        $retorno->numeroSequencial = $encaminhamentoDenuncia->getDenuncia()->getNumeroSequencial();

        return $retorno;
    }

    /**
     * Método auxiliar para Salvar dados dos Arquivos para o encaminhamento da
     * Denuncia
     *
     * @param $arquivosEncaminhamento
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     * @param Denuncia $denuncia
     * @return EncaminhamentoDenuncia
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function salvarArquivos($arquivosEncaminhamento, EncaminhamentoDenuncia $encaminhamentoDenuncia, Denuncia $denuncia)
    {
        $arquivosSalvos = new ArrayCollection();

        if (!empty($arquivosEncaminhamento)) {
            foreach ($arquivosEncaminhamento as $arquivoEncaminhamento) {
                $arquivoEncaminhamento->setEncaminhamentoDenuncia($encaminhamentoDenuncia);
                $arquivoSalvo = $this->arquivoEncaminhamentoRepository->persist($arquivoEncaminhamento);
                $arquivosSalvos->add($arquivoSalvo);
                $this->salvaArquivosDiretorio($arquivoEncaminhamento, $denuncia);
                $arquivoSalvo->setArquivo(null);
            }
        }
        $encaminhamentoDenuncia->setArquivoEncaminhamento($arquivosSalvos);
        $encaminhamentoDenuncia->removerFiles();

        return $encaminhamentoDenuncia;
    }

    /**
     * Realiza o(s) upload(s) do(s) arquivo(s) da Denuncia
     *
     * @param Denuncia $denunciaSalva
     */
    private function salvaArquivosDiretorio($arquivo, Denuncia $denunciaSalva)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenuncia($denunciaSalva->getId());

        if ($arquivo != null) {
            if (!empty($arquivo->getArquivo())) {
                $this->getArquivoService()->salvar($caminho, $arquivo->getNomeFisico(), $arquivo->getArquivo());
            }
        }
    }

    /**
     * Valida se o agendamento possui uma data válida
     *
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     * @throws NegocioException
     */
    public function validarDataInvalida(EncaminhamentoDenuncia $encaminhamentoDenuncia)
    {
        if ($encaminhamentoDenuncia->getTipoEncaminhamento()->getId() == Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO) {
            $agendamento = !empty($encaminhamentoDenuncia->getAgendamentoEncaminhamento()) ? $encaminhamentoDenuncia->getAgendamentoEncaminhamento()[0] : null;

            if (empty($agendamento->getData())) {
                throw new NegocioException(Message::MSG_DATA_INVALIDA_AGENDAMENTO);
            }
        }
    }

    /**
     * Valida se a data do agendamento para audiencia de instrucao é maior que
     * a data atual
     *
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     * @throws NegocioException
     */
    private function validarDataAgendamento(EncaminhamentoDenuncia $encaminhamentoDenuncia)
    {
        if ($encaminhamentoDenuncia->getTipoEncaminhamento()->getId() == Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO) {
            $agendamento = !empty($encaminhamentoDenuncia->getAgendamentoEncaminhamento()) ? $encaminhamentoDenuncia->getAgendamentoEncaminhamento()[0] : null;

            if (is_string($agendamento->getData())) {
                $data = new \DateTime($agendamento->getData());
            } else {
                $data = $agendamento->getData();
            }

            if ($data < Utils::getData()) {
                throw new NegocioException(Message::MSG_DATA_PASSADA_AUDIENCIA_INSTRUCAO);
            }
        }
    }

    /**
     * Envia os e-mails para os destinatários de acordo com o 'Tipo de
     * Encaminhamento'.
     *
     * @param $idEncaminhamento
     *
     * @return bool
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailsPorIdEncaminhamento($idEncaminhamento)
    {
        $encaminhamentoDenuncia = $this->encaminhamentoDenunciaRepository->find($idEncaminhamento);

        $idTipoEncaminhamento = $encaminhamentoDenuncia->getTipoEncaminhamento()->getId();
        $denuncia = $this->getDenunciaBO()->getDenunciaPorId($encaminhamentoDenuncia->getDenuncia()->getId());

        if ($idTipoEncaminhamento === Constants::TIPO_ENCAMINHAMENTO_IMPEDIMENTO_SUSPEICAO) {
            $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getAtividadeSecundariaPorNiveis(
                Constants::NIVEL_ATIVIDADE_PRINCIPAL_IMPEDIMENTO_SUSPENSAO,
                Constants::NIVEL_ATIVIDADE_SECUNDARIA_IMPEDIMENTO_SUSPENSAO
            );

            $templateEmail = $this->getTemplateEmailPorTipoDenuncia($denuncia->getTipoDenuncia()->getId(), $idTipoEncaminhamento);
            $parametrosEmails = $this->prepararParametrosEmail($denuncia, $atividadeSecundaria, $encaminhamentoDenuncia);

            $this->enviarEmailsRelatorAtualDenuncianteDenunciado($denuncia, $atividadeSecundaria, $parametrosEmails, $templateEmail);
            $this->enviarEmailCoordenador($denuncia, $atividadeSecundaria, $parametrosEmails, $templateEmail);
        }

        if ($idTipoEncaminhamento === Constants::TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS) {
            $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getAtividadeSecundariaPorNiveis(
                Constants::NIVEL_ATIVIDADE_PRINCIPAL_PRODUCAO_PROVAS,
                Constants::NIVEL_ATIVIDADE_SECUNDARIA_PRODUCAO_PROVAS
            );

            $templateEmail = $this->getTemplateEmailPorTipoDenuncia($denuncia->getTipoDenuncia()->getId(), $idTipoEncaminhamento);
            $parametrosEmails = $this->prepararParametrosEmail($denuncia, $atividadeSecundaria, $encaminhamentoDenuncia);

            $this->enviarEmailsRelatorAtualDenuncianteDenunciado($denuncia, $atividadeSecundaria, $parametrosEmails, $templateEmail);
        }

        if ($idTipoEncaminhamento === Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO) {
            $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getAtividadeSecundariaPorNiveis(
                Constants::NIVEL_ATIVIDADE_PRINCIPAL_AUDIENCIA_INSTRUCAO,
                Constants::NIVEL_ATIVIDADE_SECUNDARIA_AUDIENCIA_INSTRUCAO
            );

            $templateEmail = $this->getTemplateEmailPorTipoDenuncia($denuncia->getTipoDenuncia()->getId(), $idTipoEncaminhamento);
            $parametrosEmails = $this->prepararParametrosEmail($denuncia, $atividadeSecundaria, $encaminhamentoDenuncia);

            $this->enviarEmailsRelatorAtualDenuncianteDenunciado($denuncia, $atividadeSecundaria, $parametrosEmails, $templateEmail);
            $this->enviarEmailAssessorUfAssessorCen($denuncia, $atividadeSecundaria, $parametrosEmails, $templateEmail);
        }

        if ($idTipoEncaminhamento === Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS) {
            $this->enviarEmailEncaminhamentoAlegacaoFinal($encaminhamentoDenuncia);
        }
        return true;
    }

    /**
     * Envia os emails de encaminhamento de alegação final.
     *
     * @param \App\Entities\EncaminhamentoDenuncia $encaminhamentoDenuncia
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    private function enviarEmailEncaminhamentoAlegacaoFinal(EncaminhamentoDenuncia $encaminhamentoDenuncia): void
    {
        $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getAtividadeSecundariaPorNiveis(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_ALEGACOES_FINAIS,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_ALEGACOES_FINAIS
        );

        $tipos = [
            Constants::EMAIL_SOLICITACAO_ALEGACOES_FINAIS_DESTINATARIO,
            Constants::EMAIL_SOLICITACAO_ALEGACOES_FINAIS_RELATOR_ATUAL,
            Constants::EMAIL_SOLICITACAO_ALEGACOES_FINAIS_ASSESSORES_CE_CEN
        ];

        $emailAlegacaoFinalTO = EmailEncaminhamentoAlegacaoFinalTO::newInstanceFromEntity($encaminhamentoDenuncia);
        $emailAlegacaoFinalTO->setProcessoEleitoral(
            $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada()
        );
        $emailAlegacaoFinalTO->setStatusDenuncia(
            $this->getDenunciaBO()->getNomeSituacaoAtualDenuncia($encaminhamentoDenuncia->getDenuncia()->getId())
        );

        foreach ($tipos as $tipo) {
            $destinarios = $this->getDestinatariosEmail($encaminhamentoDenuncia, $tipo);

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividadeSecundaria->getId(), $tipo
            );

            if (!empty($emailAtividadeSecundaria) && !empty($destinarios)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios(array_unique($destinarios));

                Email::enviarMail(new EncaminhamentoAlegacoesFinaisMail($emailTO, $emailAlegacaoFinalTO));
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
    public function getDestinatariosEmail(EncaminhamentoDenuncia $encaminhamento, int $tipo): array
    {
        if ($tipo === Constants::EMAIL_SOLICITACAO_ALEGACOES_FINAIS_RELATOR_ATUAL) {
            $destinatarios[] = $encaminhamento->getDenuncia()->getUltimaDenunciaAdmitida()->getMembroComissao()
                ->getProfissionalEntity()->getPessoa()->getEmail();
        }

        if ($tipo === Constants::EMAIL_SOLICITACAO_ALEGACOES_FINAIS_ASSESSORES_CE_CEN) {
            $filial = !empty($encaminhamento->getDenuncia()->getFilial())
                        ? $encaminhamento->getDenuncia()->getFilial()->getId() : null;
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE($filial);
        }

        if ($tipo === Constants::EMAIL_SOLICITACAO_ALEGACOES_FINAIS_DESTINATARIO) {
            $encaminhamento->setDestinoDenunciado(true);
            $encaminhamento->setDestinoDenunciante(true);
            $profDestinatarios = $this->getProfissionaisDestinariosEncaminhamento(
                $encaminhamento
            );

            $destinatarios = array_map(static function (Profissional $profissional) {
                return $profissional->getPessoa()->getEmail();
            }, $profDestinatarios);
        }

        return $destinatarios;
    }

    /**
     * Prepara os parametros para o Envio de email
     *
     * @param Denuncia $denunciaSalva
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param EncaminhamentoDenuncia $encaminhamento
     * @return array
     */
    private function prepararParametrosEmail(
        Denuncia $denunciaSalva,
        AtividadeSecundariaCalendario $atividadeSecundaria,
        EncaminhamentoDenuncia $encaminhamento
    ) {
        $denuncia = $this->getDenunciaBO()->findById($denunciaSalva->getId());
        $numeroSequencial = $denunciaSalva->getNumeroSequencial() ? $denunciaSalva->getNumeroSequencial() : null;
        $anoEleicao = $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada();
        $testemunhas = $denunciaSalva->getTestemunhas();
        $situacaoDenuncia = $this->getDenunciaBO()->getMaxSituacaoDenuncia($denunciaSalva->getId());
        $situacao = $this->getDenunciaBO()->getSituacaoAtualDenuncia($situacaoDenuncia['id']);
        $idCauUf = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::IES_ID;
        $tipoDenuncia = '';
        $nomeDenunciado = '';
        $idProfissional = 0;
        $numeroChapa = null;
        $htmlTestemunha = '';


        if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_CHAPA) {
            $tipoDenuncia = Constants::TIPO_DENUNCIA_CHAPA;
            $numeroChapa = $denunciaSalva->getDenunciaChapa()->getChapaEleicao()->getNumeroChapa();
        } else if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_CHAPA) {
            $tipoDenuncia = Constants::TIPO_DENUNCIA_MEMBRO_CHAPA;
            $idProfissional = $denunciaSalva->getDenunciaMembroChapa()->getMembroChapa()->getProfissional()->getId();
            $numeroChapa = $denunciaSalva->getDenunciaMembroChapa()->getMembroChapa()->getChapaEleicao()->getNumeroChapa();
        } else if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_COMISSAO) {
            $tipoDenuncia = Constants::TIPO_DENUNCIA_MEMBRO_COMISSAO;
            $idCauUf = $denunciaSalva->getDenunciaMembroComissao()->getMembroComissao()->getIdCauUf();
            $idProfissional = $denunciaSalva->getDenunciaMembroComissao()->getMembroComissao()->getPessoa();
        } else if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_OUTROS) {
            $tipoDenuncia = Constants::TIPO_DENUNCIA_OUTROS;
        }

        if (!empty($idProfissional)) {
            $profissional = $this->profissionalRepository->find($idProfissional);
            $nomeDenunciado = $profissional->getNome();
        }

        if (!empty($testemunhas)) {
            $htmlTestemunha = '<div>';
            foreach ($testemunhas as $testemunha) {
                $htmlTestemunha .= ' Nome: ' . $testemunha->getNome();
                $htmlTestemunha .= Constants::QUEBRA_LINHA_TEMPLATE_EMAIL;
            }
            $htmlTestemunha .= '</div>';
        }

        $filialPrefixo = Constants::PREFIXO_IES;
        if ($idCauUf != Constants::IES_ID) {
            $filial = $this->filialRepository->find($idCauUf);
            $filialPrefixo = $filial->getPrefixo();
        }

        $descFator = $denunciaSalva->getDescricaoFatos() ? $denunciaSalva->getDescricaoFatos() : null;

        $descricaoTipoEncaminhamento = $encaminhamento->getTipoEncaminhamento()->getDescricao();

        $parametrosEmail = [
            Constants::PARAMETRO_EMAIL_NUMERO_SEQUENCIAL => $numeroSequencial,
            Constants::PARAMETRO_EMAIL_PROCESSO_ELEITORAL => $anoEleicao,
            Constants::PARAMETRO_EMAIL_TIPO_DENUNCIA => $tipoDenuncia,
            Constants::PARAMETRO_EMAIL_NOME_DENUNCIADO => $nomeDenunciado,
            Constants::PARAMETRO_EMAIL_NUMERO_CHAPA => $numeroChapa,
            Constants::PARAMETRO_EMAIL_UF => $filialPrefixo,
            Constants::PARAMETRO_EMAIL_DESC_FATOR => $descFator,
            Constants::PARAMETRO_EMAIL_TESTEMUNHAS => $htmlTestemunha,
            Constants::PARAMETRO_EMAIL_STATUS => $situacao['descricao'],
            Constants::PARAMETRO_EMAIL_TIPO_ENCAMINHAMENTO => $descricaoTipoEncaminhamento,
            Constants::PARAMETRO_EMAIL_DESCRICAO_ENCAMINHAMENTO => $encaminhamento->getDescricao(),
        ];

        if ($encaminhamento->getTipoEncaminhamento()->getId() == Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO) {
            /** @var AgendamentoEncaminhamentoDenuncia $agendamento */
            $agendamento = max($encaminhamento->getAgendamentoEncaminhamento()->toArray());
            $descricaoAgendamentoFormatada = $this->getDescricaoAgendamentoFormatada($agendamento);
            $parametrosEmail[Constants::PARAMETRO_EMAIL_AGENDAMENTO_ENCAMINHAMENTO] = $descricaoAgendamentoFormatada;
        }

        return $parametrosEmail;
    }

    /**
     * Enviar os e-mails para o 'Relator atual da Denúncia', o 'Denunciante' e
     * o 'Denunciado'.
     *
     * @param Denuncia $denuncia
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param $parametrosEmails
     * @param $templateEmail
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailsRelatorAtualDenuncianteDenunciado(
        Denuncia $denuncia,
        AtividadeSecundariaCalendario $atividadeSecundaria,
        $parametrosEmails,
        $templateEmail
    )
    {
        $this->enviarEmailRelatorAtualDenuncia($denuncia, $atividadeSecundaria, $parametrosEmails, $templateEmail);
        $this->enviarEmailDenunciante($denuncia, $atividadeSecundaria, $parametrosEmails, $templateEmail);
        $this->enviarEmailDenunciado($denuncia, $atividadeSecundaria, $parametrosEmails, $templateEmail);
    }

    /**
     * Enviar o e-mail para o 'Relator atual da Denúncia'.
     *
     * @param Denuncia $denuncia
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param $parametrosEmails
     * @param $templateEmail
     * @throws NonUniqueResultException
     */
    private function enviarEmailRelatorAtualDenuncia(
        Denuncia $denuncia,
        AtividadeSecundariaCalendario $atividadeSecundaria,
        $parametrosEmails,
        $templateEmail
    )
    {
        $idRelator = $denuncia->getUltimaDenunciaAdmitida()->getMembroComissao()->getPessoa();
        $emails[] = $this->getProfissionalBO()->getPorId($idRelator, false, false)->getPessoa()->getEmail();

        $this->enviarEmailDefesaDenuncia($atividadeSecundaria->getId(), $emails, $parametrosEmails, $templateEmail);
    }

    /**
     * Envia o e-mail para o 'Denunciante'.
     *
     * @param Denuncia $denuncia
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param $parametrosEmails
     * @param $templateEmail
     */
    private function enviarEmailDenunciante(
        Denuncia $denuncia,
        AtividadeSecundariaCalendario $atividadeSecundaria,
        $parametrosEmails,
        $templateEmail
    )
    {
        $emails[] = $denuncia->getPessoa()->getEmail();

        $this->enviarEmailDefesaDenuncia($atividadeSecundaria->getId(), $emails, $parametrosEmails, $templateEmail);
    }

    /**
     * Envia o e-mail para o 'Denunciado'.
     *
     * @param Denuncia $denuncia
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param $parametrosEmails
     * @param $templateEmail
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailDenunciado(
        Denuncia $denuncia,
        AtividadeSecundariaCalendario $atividadeSecundaria,
        $parametrosEmails,
        $templateEmail
    )
    {
        $emails = $this->getEmailsDenunciadoPorTipoDenuncia($denuncia);

        $this->enviarEmailDefesaDenuncia($atividadeSecundaria->getId(), $emails, $parametrosEmails, $templateEmail);
    }

    /**
     * Envia o -email para o 'Coordenador UF/CEN'.
     *
     * @param Denuncia $denuncia
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param $parametrosEmails
     * @param $templateEmail
     * @throws NegocioException
     */
    private function enviarEmailCoordenador(
        Denuncia $denuncia,
        AtividadeSecundariaCalendario $atividadeSecundaria,
        $parametrosEmails,
        $templateEmail
    )
    {
        $emails = $this->getEmailsCoordenadoresPorIdCauUfDenuncia($denuncia);
        $this->enviarEmailDefesaDenuncia($atividadeSecundaria->getId(), $emails, $parametrosEmails, $templateEmail);
    }

    /**
     * Envia o e-mail para os 'Assessores da UF/CEN'.
     *
     * @param Denuncia $denuncia
     * @throws NegocioException
     */
    private function enviarEmailAssessorUfAssessorCen(
        Denuncia $denuncia,
        AtividadeSecundariaCalendario $atividadeSecundaria,
        $parametrosEmails,
        $templateEmail
    )
    {
        $emails = $this->getEmailsAssessoresUfCen($denuncia);
        $this->enviarEmailDefesaDenuncia($atividadeSecundaria->getId(), $emails, $parametrosEmails, $templateEmail);
    }

    /**
     * Retorna a lista de e-mails dos 'Coordenadores' de acordo com o ID da
     * 'CAU UF'.
     *
     * @param Denuncia $denuncia
     * @return array
     * @throws NegocioException
     */
    private function getEmailsCoordenadoresPorIdCauUfDenuncia(Denuncia $denuncia): array
    {
        $emails = [];
        $filtroTO = new stdClass();
        $filtroTO->idCauUf = $this->getIdCauUfPorTipoDenuncia($denuncia);
        $filtroTO->tipoParticipacao = Constants::TIPO_PARTICIPACAO_COORDENADOR;

        $coordenadores = $this->getMembroComissaoBO()->getPorFiltro($filtroTO);

        foreach ($coordenadores['membros'] as $membro) {
            $emails[] = $membro->getProfissional()->getEmail();
        }
        return array_unique($emails);
    }

    /**
     * Retorna a lista de e-mails dos 'Assessores da UF/CEN'.
     *
     * @param Denuncia $denuncia
     * @return array
     * @throws NegocioException
     */
    private function getEmailsAssessoresUfCen(Denuncia $denuncia): array
    {
        $emails = [];

        $idsCauUf = $this->getIdCauUfPorTipoDenuncia($denuncia);

        $assessores = $this->getCorporativoService()->getUsuariosAssessoresCE([
            Constants::IES_ID, // @TODO: Verificar se o 'Assessor IES' refere-se também ao 'Assessor CEN'
            Constants::COMISSAO_MEMBRO_CAU_BR_ID,
            $idsCauUf,
        ]);

        if (!empty($assessores)) {
            /** @var UsuarioTO $assessor */
            foreach ($assessores as $assessor) {
                $emails[] = $assessor->getEmail();
            }
        }
        return $emails;
    }

    /**
     * Retorna a lista de e-mails do 'denunciado' de acordo com o 'tipo da
     * denúncia'.
     *
     * @param Denuncia $denuncia
     * @return array
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getEmailsDenunciadoPorTipoDenuncia(Denuncia $denuncia): array
    {
        $emailsDenunciado = null;
        $idTipoDenuncia = $denuncia->getTipoDenuncia()->getId();

        if ($idTipoDenuncia === Constants::TIPO_CHAPA) {
            $idChapa = $denuncia->getDenunciaChapa()->getChapaEleicao()->getId();
            $responsaveis = $this->getMembroChapaBO()->getMembrosResponsaveisChapa($idChapa);
            $emailsDenunciado = $this->getMembroChapaBO()->getListEmailsByMembros($responsaveis);
        }
        if ($idTipoDenuncia === Constants::TIPO_MEMBRO_CHAPA) {
            $email = $this->getDenunciaMembroChapaBO()->getDadosDenunciante($denuncia->getId());
            $emailsDenunciado[] = $email[0];
        }
        if ($idTipoDenuncia === Constants::TIPO_MEMBRO_COMISSAO) {
            $email = $this->getDenunciaMembroComissaoBO()->getDadosDenunciante($denuncia->getId());
            $emailsDenunciado[] = $email[0];
        }
        return $emailsDenunciado;
    }

    /**
     * Retorna o nome do 'template de e-mail' de acordo com o 'tipo de
     * denúncia' informado.
     *
     * @param int $idTipoDenuncia
     * @param $idTipoEncaminhamento
     * @return string
     */
    private function getTemplateEmailPorTipoDenuncia($idTipoDenuncia, $idTipoEncaminhamento)
    {
        $templatesEmail = [
            Constants::TIPO_CHAPA => Constants::TEMPLATE_EMAIL_ENCAMINHAMENTO_CHAPA,
            Constants::TIPO_OUTROS => Constants::TEMPLATE_EMAIL_ENCAMINHAMENTO_OUTROS,
            Constants::TIPO_MEMBRO_CHAPA => Constants::TEMPLATE_EMAIL_ENCAMINHAMENTO_MEMBRO_CHAPA,
            Constants::TIPO_MEMBRO_COMISSAO => Constants::TEMPLATE_EMAIL_ENCAMINHAMENTO_MEMBRO_COMISSAO,
        ];

        if ($idTipoEncaminhamento === Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO) {
            $templatesEmail = [
                Constants::TIPO_CHAPA => Constants::TEMPLATE_EMAIL_ENCAMINHAMENTO_CHAPA_AUDIENCIA_INSTRUCAO,
                Constants::TIPO_OUTROS => Constants::TEMPLATE_EMAIL_ENCAMINHAMENTO_OUTROS_AUDIENCIA_INSTRUCAO,
                Constants::TIPO_MEMBRO_CHAPA => Constants::TEMPLATE_EMAIL_ENCAMINHAMENTO_MEMBRO_CHAPA_AUDIENCIA_INSTRUCAO,
                Constants::TIPO_MEMBRO_COMISSAO => Constants::TEMPLATE_EMAIL_ENCAMINHAMENTO_MEMBRO_COMISSAO_AUDIENCIA_INSTRUCAO,
            ];
        }

        return $templatesEmail[$idTipoDenuncia];
    }

    /**
     * Retorna o ID da 'CAU UF' de acordo com o 'tipo de denúncia' informado.
     *
     * @param Denuncia $denuncia
     * @return int
     */
    private function getIdCauUfPorTipoDenuncia(Denuncia $denuncia)
    {
        $tipoDenuncia = $denuncia->getTipoDenuncia()->getId();
        $idCalUf = null;

        if ($tipoDenuncia == Constants::TIPO_CHAPA) {
            $idCalUf = $denuncia->getDenunciaChapa()->getChapaEleicao()->getIdCauUf();
        } else if ($tipoDenuncia == Constants::TIPO_MEMBRO_COMISSAO) {
            $idCalUf = $denuncia->getDenunciaMembroComissao()->getMembroComissao()->getIdCauUf();
        } else if ($tipoDenuncia == Constants::TIPO_MEMBRO_CHAPA) {
            $idCalUf = $denuncia->getDenunciaMembroChapa()->getMembroChapa()->getChapaEleicao()->getIdCauUf();
        }

        return $idCalUf;
    }

    /**
     * Método auxiliar que busca o e-mail definido e realiza o envio
     *
     * @param $idAtividadeSecundaria
     * @param array $emailsDestinatarios
     * @param array|null $parametrosExtras
     * @param $nomeTemplate
     */
    private function enviarEmailDefesaDenuncia(
        $idAtividadeSecundaria,
        $emailsDestinatarios,
        $parametrosExtras = [],
        $nomeTemplate
    )
    {
        $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailAtividadeSecundariaPorAtividadeSecundaria(
            $idAtividadeSecundaria
        );

        if (is_array($emailAtividadeSecundaria)) {
            $emailAtividadeSecundaria = $emailAtividadeSecundaria[0];
        }

        if (!empty($emailsDestinatarios)) {
            $this->getEmailAtividadeSecundariaBO()->enviarEmailAtividadeSecundaria(
                $emailAtividadeSecundaria,
                $emailsDestinatarios,
                $nomeTemplate,
                $parametrosExtras
            );
        }
    }

    /**
     * Retorna a descrição formatada do 'AgendamentoEncaminhamentoDenuncia'.
     *
     * @param AgendamentoEncaminhamentoDenuncia $agendamentoEncaminhamento
     * @return string
     */
    private function getDescricaoAgendamentoFormatada(AgendamentoEncaminhamentoDenuncia $agendamentoEncaminhamento)
    {
        return Utils::getStringFromDate($agendamentoEncaminhamento->getData(), "d/m/Y")
            . " às " .
            Utils::getStringFromDate($agendamentoEncaminhamento->getData(), "H:i");
    }

    /**
     * Método para retornar a instancia de Email Atividade Secundaria BO
     *
     * @return EmailAtividadeSecundariaBO
     */
    private function getEmailAtividadeSecundariaBO()
    {
        if (empty($this->emailAtividadeSecundariaBO)) {
            $this->emailAtividadeSecundariaBO = new EmailAtividadeSecundariaBO();
        }
        return $this->emailAtividadeSecundariaBO;
    }

    /**
     * Retorna a listagem dos encaminhamentos de uma denúncia especifica
     *
     * @param $idDenuncia
     * @return array|ParecerEncaminhamentoDenunciaTO
     * @throws \Exception
     */
    public function getParecer($idDenuncia)
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        $denuncia = $this->getDenunciaBO()->findById($idDenuncia);
        $encaminhamentos = $this->encaminhamentoDenunciaRepository->getEncaminhamentosPorDenuncia($idDenuncia);

        $listaEncaminhamentos = [];
        if (!empty($encaminhamentos) && !empty($denuncia)) {
            foreach ($encaminhamentos as $encaminhamento) {
                $denunciaParecerTO = ListagemEncaminhamentoDenunciaTO::newInstance($encaminhamento);
                $denunciaParecerTO->setIsAcaoAlegacoesFinais(
                    $this->getAlegacaoFinalBO()->isAcaoInserirAlegacoesFinaisEncaminhamento($denunciaParecerTO->getIdEncaminhamento())
                );
                $denunciaParecerTO->setIsAcaoInserirNovoRelator(
                    $this->getDenunciaBO()->isAcaoDisponivelInseirNovoRelator($denunciaParecerTO->getIdEncaminhamento())
                );

                $prazo = null;
                if ($denunciaParecerTO->getIdTipoEncaminhamento() === Constants::TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS) {
                    if ($denunciaParecerTO->getDataEncaminhamento() !== null
                        && !empty($encaminhamento['prazoProducaoProvas'])) {

                        $feriados = $this->getCalendarioApiService()
                            ->getFeriadosNacionais(Utils::getAnoData());
                        $data = Utils::adicionarDiasUteisData($denunciaParecerTO->getDataEncaminhamento(), 1, $feriados );
                        $prazo = Utils::adicionarDiasData(
                            $data,
                            $encaminhamento['prazoProducaoProvas']
                        );

                        if (Utils::getDataHoraZero($prazo) < Utils::getDataHoraZero(Utils::getData())) {
                            $denunciaParecerTO->setIsPrazoVencido(true);
                        }
                    }
                }

                if ($denunciaParecerTO->getIdTipoEncaminhamento() === Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO) {
                    $prazo = !empty($encaminhamento['agendamentoEncaminhamento'])
                        ? $encaminhamento['agendamentoEncaminhamento'][0]['data']
                        : null;

                    $dadosEncaminhamentoIS = array_filter($encaminhamentos, function ($encaminhamento) {
                        $tipo = $encaminhamento['tipoEncaminhamento'];
                        $situacao = $encaminhamento['tipoSituacaoEncaminhamento'];

                        return $tipo['id'] == Constants::TIPO_ENCAMINHAMENTO_IMPEDIMENTO_SUSPEICAO
                            && $situacao['id'] == Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE;
                    });

                    $dadosEncaminhamentoIS = reset($dadosEncaminhamentoIS);
                    $denunciaParecerTO->setHasEmcaminhamentoSuspeicaoPendente(!empty($dadosEncaminhamentoIS));

                    $isRelatorAtual = $this->getDenunciaBO()->validarRelatorAtual($denuncia);
                    $denunciaParecerTO->setIsRelatorAtual($isRelatorAtual);
                }

                if ($denunciaParecerTO->getIdTipoEncaminhamento() === Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS) {
                    $feriados = $this->getCalendarioApiService()
                        ->getFeriadosNacionais(Utils::getAnoData());
                    $data = Utils::adicionarDiasUteisData($denunciaParecerTO->getDataEncaminhamento(), 1, $feriados );
                    $prazo = Utils::adicionarDiasData($data, 1);
                }

                $denunciaParecerTO->setPrazoEnvio($prazo);

                $destinatarios = $this->getDestinatariosEncaminhamentoDenuncia($denuncia, $encaminhamento);
                $denunciaParecerTO->setDestinatarios($destinatarios->destinatarios);

                if (!empty($usuarioLogado)) {
                    $hasDestinatarioEncaminhamento = array_filter($destinatarios->idDestinatarios, function ($idDestinatario) use ($usuarioLogado) {
                        return $usuarioLogado->idProfissional == $idDestinatario;
                    });

                    if (!empty($hasDestinatarioEncaminhamento)) {
                        $denunciaParecerTO->setIsDestinatarioEncaminhamento(true);
                    }
                }

                $denunciaParecerTO->setIsDestinatarioDenunciante($encaminhamento['destinoDenunciante']);
                $denunciaParecerTO->setIsDestinatarioDenunciado($encaminhamento['destinoDenunciado']);

                $listaEncaminhamentos[] = $denunciaParecerTO;
            }
        }

        return ParecerEncaminhamentoDenunciaTO::newInstance([
            'idDenuncia' => !empty($denuncia) ? $denuncia->getId() : null,
            'numeroDenuncia' => !empty($denuncia) ? $denuncia->getNumeroSequencial() : null,
            'encaminhamentos' => $listaEncaminhamentos,
        ]);
    }

    /**
     * Retorna as informações do parecer para exportar para PDF
     *
     * @param $idDenuncia
     * @return stdClass
     * @throws \Exception
     */
    public function getExportarInformacoesParecer($idDenuncia)
    {
        $detalhes = [];
        $encaminhamentos = $this->getParecer($idDenuncia);

        foreach ($encaminhamentos->getEncaminhamentos() as $encaminhamento){
            $parecer = $this->findById($encaminhamento->getIdEncaminhamento());
            $exportar = ExportarParecerTO::newInstanceFromEntity($parecer);

            //Informações dos documentos do encaminhamento ou do parecer final
            $exportar->setDocumentos($this->getDescricaoDocumentosEncaminhamento($parecer));

            //Informações dos documentos do encaminhamento: Impedimento Suspeição
            $impedimentoSuspeicao = $parecer->getImpedimentoSuspeicao();
            if (!empty($impedimentoSuspeicao)) {
                $exportar->getImpedimentoSuspeicao()->setDocumentos($this->getDescricaoDocumentosImpedimentoSuspeicao($impedimentoSuspeicao));
            }

            //Informações dos documentos do encaminhamento: Produção Provas
            $provas = $parecer->getDenunciaProvas();
            if (!empty($provas->current())) {
                $exportar->getProducaoProvas()->setDescricaoArquivo($this->getDescricaoDocumentosProducaoProvas($provas->current()));
                $exportar->getProducaoProvas()->setDestinatario($encaminhamento->getDestinatarios()[0]);
            }

            //Informações dos documentos do encaminhamento: Audiencia de Instrução
            $audiencia = $parecer->getAudienciaInstrucao();
            if (!empty($audiencia->current())) {
                $exportar->getAudienciaInstrucao()->setDescricaoArquivo($this->getDescricaoDocumentosAudienciaInstrucao($audiencia->current()));
            }


            //Informações dos documentos do encaminhamento: Alegação final
            $alegacaoFinal = $parecer->getAlegacaoFinal();
            if (!empty($alegacaoFinal)) {
                $exportar->getAlegacaoFinal()->setDescricaoArquivo($this->getDescricaoDocumentosAlegacaoFinal($alegacaoFinal));
                $exportar->getAlegacaoFinal()->setDestinatario($encaminhamento->getDestinatarios()[0]);

                if (empty($exportar->getAlegacaoFinal()->getDataHora())) {
                    $historico = $this->getHistoricoDenunciaBO()->getHistoricoDenunciaPorDenunciaEAcao(
                        $parecer->getDenuncia()->getId(), Constants::ACAO_HISTORICO_ALEGACAO_FINAL
                    );
                    $exportar->getAlegacaoFinal()->setDataHora($historico->getDataHistorico());
                }
            }

            $detalhes[] = $exportar;
        }
        sort($detalhes);

        $exportarParecer = new stdClass();
        $exportarParecer->encaminhamentos = $encaminhamentos->getEncaminhamentos();
        $exportarParecer->detalhes = $detalhes;
        return $exportarParecer;
    }

    /**
     * Retorna as informações do arquivo de encaminhamentos ou do parecer final PDF
     *
     * @param EncaminhamentoDenuncia $parecer
     * @return array|null
     * @throws \Exception
     */
    public function getDescricaoDocumentosEncaminhamento(EncaminhamentoDenuncia $parecer)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenuncia($parecer->getDenuncia()->getId());

        $parecerFinal = $parecer->getParecerFinal();
        if(!empty($parecerFinal)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioParecerFinal($parecerFinal->getId());
        }

        $documentos = $this->getDenunciaBO()->getDescricaoArquivoExportar(
            $parecer->getArquivoEncaminhamento(), $caminho
        );

        return $documentos;
    }

    /**
     * Retorna as informações do arquivo de encaminhamentos impedimento suspeição PDF
     *
     * @param $impedimentoSuspeicao
     * @return array|null
     * @throws \Exception
     */
    public function getDescricaoDocumentosImpedimentoSuspeicao($impedimentoSuspeicao)
    {
        $documentos = null;

        if (!empty($impedimentoSuspeicao)) {
            $documentos = $this->getDenunciaBO()->getDescricaoArquivoExportar(
                $impedimentoSuspeicao->getDenunciaAdmitida()->getArquivoDenunciaAdmitida(),
                $this->getArquivoService()->getCaminhoRepositorioDenuncia(
                    $impedimentoSuspeicao->getDenunciaAdmitida()->getDenuncia()->getId()
                )
            );
        }

        return $documentos;
    }

    /**
     * Retorna as informações do arquivo de encaminhamentos Produção Provas PDF
     *
     * @param $provas
     * @return array|null
     * @throws \Exception
     */
    public function getDescricaoDocumentosProducaoProvas($provas)
    {
        $documentos = null;

        if (!empty($provas)) {
            $documentos = $this->getExportarArquivoEncaminhamento(
                $provas->getArquivosDenunciaProvas(),
                $this->getArquivoService()->getCaminhoRepositorioDenunciaProvas($provas->getId())
            );
        }

        return $documentos;
    }

    /**
     * Retorna as informações do arquivo de encaminhamentos Audiencia de Instrução PDF
     *
     * @param $audiencia
     * @return array|null
     * @throws \Exception
     */
    public function getDescricaoDocumentosAudienciaInstrucao($audiencia)
    {
        $documentos = null;

        if (!empty($audiencia)) {
            $documentos = $this->getExportarArquivoEncaminhamento(
                $audiencia->getArquivosAudienciaInstrucao(),
                $this->getArquivoService()->getCaminhoRepositorioDenunciaAudienciaInstrucao($audiencia->getId())
            );
        }

        return $documentos;
    }

    /**
     * Retorna as informações do arquivo de encaminhamentos Alegações finais PDF
     *
     * @param $alegacaoFinal
     * @return array|null
     * @throws \Exception
     */
    public function getDescricaoDocumentosAlegacaoFinal($alegacaoFinal)
    {
        $documentos = null;

        if (!empty($alegacaoFinal)) {
            $documentos = $this->getExportarArquivoEncaminhamento(
                $alegacaoFinal->getArquivosAlegacaoFinal(),
                $this->getArquivoService()->getCaminhoRepositorioAlegacaoFinal($alegacaoFinal->getId())
            );
        }

        return $documentos;
    }

    /**
     * Retorna as informações do arquivo de encaminhamentos PDF
     *
     * @param $arquivos
     * @param $caminho
     * @return array|null
     * @throws \Exception
     */
    public function getExportarArquivoEncaminhamento($arquivos, $caminho)
    {
        $documentos = null;

        if (!empty($arquivos)) {
            foreach ($arquivos as $arquivo) {
                $documentos[] = $this->getArquivoService()->getDescricaoArquivo(
                    $caminho, $arquivo->getNomeFisicoArquivo(), $arquivo->getNome()
                );
            }
        }

        return $documentos;
    }

    /**
     * Recupera o Total de Encaminhamentos Pendentes de “Produção de provas” e “Alegações Finais” para a Denuncia e o Usuario Logado.
     *
     * @param $idDenuncia
     * @return stdClass
     * @throws NegocioException
     */
    public function getTotalEncaminhamentosPendentesPorDenunciaEUsuario($idDenuncia)
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $countPendentes = 0;
        $tipos = [];

        $denuncia = $this->getDenunciaBO()->getDenuncia($idDenuncia);
        $encaminhamentos = $this->encaminhamentoDenunciaRepository->getEncaminhamentosPorDenuncia($idDenuncia);

        if (!empty($encaminhamentos) && !empty($denuncia)) {
            foreach ($encaminhamentos as $encaminhamento) {

                $situacao = $encaminhamento['tipoSituacaoEncaminhamento'];
                $tipo = $encaminhamento['tipoEncaminhamento'];
                $destinatarios = $this->getDestinatariosEncaminhamentoDenuncia($denuncia, $encaminhamento);

                if (!empty($usuarioLogado)) {
                    $hasDestinatarioEncaminhamento
                        = array_filter($destinatarios->idDestinatarios, function ($idDestinatario) use ($usuarioLogado, $situacao, $tipo) {
                        return $usuarioLogado->idProfissional == $idDestinatario &&
                            $situacao['id'] == Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE
                            && ($tipo['id'] == Constants::TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS
                                || $tipo['id'] ==  Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS);
                    });

                    if (!empty($hasDestinatarioEncaminhamento)) {
                        $countPendentes++;
                        $tipos[] = $tipo['descricao'];
                    }
                }

            }
        }

        $dadosEncaminhamento = new stdClass();
        $dadosEncaminhamento->quantidade = $countPendentes;
        $dadosEncaminhamento->tiposEncaminhamento = implode(", ", $tipos);

        return $dadosEncaminhamento;
    }

    /**
     * Retorna os destinatarios do encaminhamento da denúncia
     *
     * @param Denuncia $denuncia
     * @param $encaminhamento
     * @return stdClass
     * @throws NegocioException
     */
    private function getDestinatariosEncaminhamentoDenuncia(Denuncia $denuncia, $encaminhamento)
    {
        $destinatario = [];
        $idDestinatarios = [];
        $filial = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::IES_ID;

        if ($encaminhamento['tipoEncaminhamento']['id'] === Constants::TIPO_ENCAMINHAMENTO_IMPEDIMENTO_SUSPEICAO) {
            $destinatario = $this->getMembroComissaoBO()->getNomesCoordenadoresProfissionaisPorIdAtividaSecundaria(
                $denuncia->getAtividadeSecundaria()->getId(), $filial
            );
        } else {
            if ($encaminhamento['destinoDenunciado']) {
                if ($denuncia->getTipoDenuncia()->getId() === Constants::TIPO_CHAPA) {

                    $id_cau_uf = $denuncia->getDenunciaChapa()->getChapaEleicao()->getIdCauUf();
                    $objFilial = $filial != Constants::IES_ID ? $this->getFilialBO()->getPorId($id_cau_uf)
                        : $this->getFilialBO()->getFilialIES();

                    $destinatario[] = "Chapa " . $denuncia->getDenunciaChapa()->getChapaEleicao()->getNumeroChapa()
                        . " " . $objFilial->getPrefixo();

                    $idChapa = $denuncia->getDenunciaChapa()->getChapaEleicao()->getId();
                    $responsaveis = $this->getMembroChapaBO()->getMembrosResponsaveisChapa(
                        $idChapa,
                        Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
                    );

                    if (!empty($responsaveis)) {
                        foreach ($responsaveis as $responsavel) {
                            $idDestinatarios[] = $responsavel->getProfissional()->getId();
                        }
                    }
                }

                if ($denuncia->getTipoDenuncia()->getId() === Constants::TIPO_MEMBRO_CHAPA) {
                    $destinatario[] = $denuncia->getDenunciaMembroChapa()->getMembroChapa()->getProfissional()->getNome();
                    $idDestinatarios[] = $denuncia->getDenunciaMembroChapa()->getMembroChapa()->getProfissional()->getId();
                }

                if ($denuncia->getTipoDenuncia()->getId() === Constants::TIPO_MEMBRO_COMISSAO) {
                    $destinatario[] = $denuncia->getDenunciaMembroComissao()->getMembroComissao()->
                    getProfissionalEntity()->getNome();

                    $idDestinatarios[] = $denuncia->getDenunciaMembroComissao()->getMembroComissao()->
                    getProfissionalEntity()->getId();
                }
            }
            if ($encaminhamento['destinoDenunciante']) {
                $denunciante = $denuncia->getPessoa()->getProfissional()->getNome();
                $destinatario[] = $this->getUsuarioFactory()->isCorporativo() || !$denuncia->isSigiloso() ? $denunciante : Utils::ofuscarCampo($denunciante);
                $idDestinatarios[] = $denuncia->getPessoa()->getProfissional()->getId();
            }
        }

        $destinatariosTO = new stdClass();
        $destinatariosTO->destinatarios = $destinatario;
        $destinatariosTO->idDestinatarios = $idDestinatarios;

        return $destinatariosTO;
    }

    /**
     * Altera o tipo de situação do encaminhamento.
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     * @param int $tipo
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function alterarTipoSituacaoEncaminhamento(EncaminhamentoDenuncia $encaminhamento, int $tipo)
    {
        $encaminhamento->setTipoSituacaoEncaminhamento(TipoSituacaoEncaminhamentoDenuncia::newInstance([
            'id' => $tipo,
        ]));

        $this->encaminhamentoDenunciaRepository->persist($encaminhamento);
    }

    /**
     * Retorna os profissionais destinatarios do encaminhamento, caso for do tipo chapa, retorna
     * os profissionais responsaveis pela chapa.
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     * @return Profissional[]|array
     */
    public function  getProfissionaisDestinariosEncaminhamento(EncaminhamentoDenuncia $encaminhamento)
    {
        $profissional = array();

        if ($encaminhamento->isDestinoDenunciado()) {
            $profissional[] = $encaminhamento->getDenuncia()->getPessoa()->getProfissional();
        }

        if ($encaminhamento->isDestinoDenunciante()) {
            $tipo = $encaminhamento->getDenuncia()->getTipoDenuncia()->getId();

            if ($tipo == Constants::TIPO_CHAPA) {
                $membroChapa = $this->getMembroChapaBO()->getMembrosResponsaveisChapa(
                    $encaminhamento->getDenuncia()->getDenunciaChapa()->getChapaEleicao()->getId()
                );
                if (!empty($membroChapa)) {
                    foreach ($membroChapa as $membro) {
                        /** @var MembroChapa $membro */
                        $profissional[] = $membro->getProfissional();
                    }
                }
            }

            if ($tipo == Constants::TIPO_MEMBRO_CHAPA) {
                $profissional[] = $encaminhamento->getDenuncia()->getDenunciaMembroChapa()->getMembroChapa()->
                getProfissional();
            }

            if ($tipo == Constants::TIPO_MEMBRO_COMISSAO) {
                $profissional[] = $encaminhamento->getDenuncia()->getDenunciaMembroComissao()->getMembroComissao()->
                getProfissionalEntity();
            }
        }

        return $profissional;
    }

    /**
     * Método para realizar a validação se encaminhamento alegação final pendente está dentro do prazo
     *
     * @param EncaminhamentoDenuncia[] $encaminhamentos
     * @return bool
     * @throws \Exception
     */
    public function isAlegacaoFinalPendenteDentroPrazo($encaminhamentos)
    {
        if (!is_array($encaminhamentos)) {
            $encaminhamentos = $encaminhamentos->toArray();
        }

        $isEncaminhamentos = array_filter($encaminhamentos, function (EncaminhamentoDenuncia $encaminhamento) {

            $isSituacaoPendente = $encaminhamento->getTipoSituacaoEncaminhamento()
                    ->getId() === Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE;
            $isAlegacoesFinais = $encaminhamento->getTipoEncaminhamento()
                    ->getId() === Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS;

            $feriados = $this->getCalendarioApiService()
                ->getFeriadosNacionais(Utils::getAnoData());
            $data = Utils::adicionarDiasUteisData($encaminhamento->getData(), 1, $feriados );
            $isPrazo = Utils::getData() <= Utils::adicionarDiasData($data, 1)
                ? true : false;

            return $isPrazo && $isSituacaoPendente && $isAlegacoesFinais;
        });

        if (empty($isEncaminhamentos)) {
            return false;
        }

        return true;
    }

    /**
     *  Recupera os encaminhamentos do Tipo Prova com o prazo encerrado
     *
     * @return array
     */
    public function getEncaminhamentosProvaPrazoEncerrado()
    {
        return $this->encaminhamentoDenunciaRepository->getEncaminhamentosProvaPrazoEncerrado();
    }

    /**
     * Disponibiliza o arquivo de 'encaminhamento' para 'download' conforme o 'id' informado
     *
     * @param $idArquivo
     * @return \App\To\ArquivoTO
     * @throws NegocioException
     */
    public function getArquivo($idArquivo)
    {
        $arquivoEncaminhamento = $this->getArquivoEncaminhamentoDenuncia($idArquivo);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenuncia($arquivoEncaminhamento->getEncaminhamentoDenuncia()->getDenuncia()->getId());

        return $this->getArquivoService()->getArquivo($caminho, $arquivoEncaminhamento->getNomeFisico(), $arquivoEncaminhamento->getNome());
    }

    /**
     * Disponibiliza o arquivo da 'denuncia admitida' para 'download' conforme o 'id' informado
     *
     * @param $idArquivo
     * @return \App\To\ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoDenunciaAdmitida($idArquivo)
    {
        $arquivoDenunciaAdmitida = $this->getArquivoEncaminhamentoDenunciaAdmitida($idArquivo);

        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenunciaAdmitida($arquivoDenunciaAdmitida->getDenunciaAdmitida()->getDenuncia()->getId());

        return $this->getArquivoService()->getArquivo($caminho, $arquivoDenunciaAdmitida->getNomeFisico(), $arquivoDenunciaAdmitida->getNome());
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
    private function getArquivoEncaminhamentoDenunciaAdmitida($id)
    {
        $arrayArquivo = $this->arquivoDenunciaAdmitidaRepository->getPorId($id);

        return $arrayArquivo;
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
    private function getArquivoEncaminhamentoDenuncia($id)
    {
        $arrayArquivo = $this->arquivoEncaminhamentoRepository->getPorId($id);
        return $arrayArquivo;
    }

    /**
     * Verifica se existe encaminhamento do tipo alegação final
     *
     * @param Denuncia $denuncia
     * @return mixed
     */
    public function isExisteEncaminhamentoAlegacaoFinal(Denuncia $denuncia)
    {
        $isExiste = false;

        $encaminhamentos = $this->encaminhamentoDenunciaRepository->findBy(
            ['denuncia' => $denuncia->getId(), 'tipoEncaminhamento' => Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS]
        );

        if (!empty($encaminhamentos)) {
            $isExiste = true;
        }

        return $isExiste;
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
     * Retorna o encaminhamento com suas provas e arquivos caso existam
     */
    public function getProvasEncaminhamento($idEncaminhamento)
    {
        $encaminhamento = $this->encaminhamentoDenunciaRepository->getEncaminhamentoPorId($idEncaminhamento);

        if ($encaminhamento) {
            $denunciaProvas = $this->denunciaProvasRepository->getDenunciaProvasPorEncaminhamento($idEncaminhamento);

            return EncaminhamentoDenunciaProvasTO::newInstance([
                'encaminhamento' => $encaminhamento,
                'denunciaProvas' => $denunciaProvas,
            ]);

        } else {
            return array();
        }
    }

    /**
     * Retorna o encaminhamento com suas provas e arquivos caso existam
     *
     * @param $id
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function getAudienciaInstrucaoEncaminhamento($id)
    {
        $encaminhamento = $this->encaminhamentoDenunciaRepository->getEncaminhamentoPorId($id);
        if ($encaminhamento) {
            $encaminhamento['audienciaInstrucao'] = $this->denunciaAudienciaInstrucaoRepository->getPorEncaminhamento($encaminhamento['id']);
        }
        return $encaminhamento;
    }


    /**
     * Método para retornar a instancia do encaminhamento da denúncia
     *
     * @return EncaminhamentoDenunciaBO
     */
    public function getEncaminhamentoPorId($id)
    {
        $encaminhamento = $this->encaminhamentoDenunciaRepository->getEncaminhamentoPorId($id);
        if ($encaminhamento) {
            $encaminhamento['audienciaInstrucao'] = $this->denunciaAudienciaInstrucaoRepository->getPorEncaminhamento($encaminhamento['id']);
        }
        return $encaminhamento;
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
}
