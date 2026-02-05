<?php
/*
 * DenunciaBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoDenuncia;
use App\Entities\ArquivoDenunciaAdmitida;
use App\Entities\ArquivoDenunciaDefesa;
use App\Entities\ArquivoDenunciaInadmitida;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Calendario;
use App\Entities\ChapaEleicao;
use App\Entities\Denuncia;
use App\Entities\DenunciaAdmitida;
use App\Entities\DenunciaChapa;
use App\Entities\DenunciaDefesa;
use App\Entities\DenunciaInadmitida;
use App\Entities\DenunciaMembroChapa;
use App\Entities\DenunciaMembroComissao;
use App\Entities\DenunciaOutro;
use App\Entities\DenunciaSituacao;
use App\Entities\EncaminhamentoDenuncia;
use App\Entities\Filial;
use App\Entities\ImpedimentoSuspeicao;
use App\Entities\MembroChapa;
use App\Entities\MembroComissao;
use App\Entities\Pessoa;
use App\Entities\Profissional;
use App\Entities\RecursoDenuncia;
use App\Entities\SituacaoDenuncia;
use App\Entities\TestemunhaDenuncia;
use App\Entities\TipoDenuncia;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\PDFFActory;
use App\Factory\UsuarioFactory;
use App\Jobs\EnviarEmailDenunciaAdmitirInadmitirJob;
use App\Jobs\EnviarEmailDenunciaJob;
use App\Jobs\EnviarEmailInserirNovoRelatorJob;
use App\Mail\InserirNovoRelatorMail;
use App\Repository\CalendarioRepository;
use App\Repository\FilialRepository;
use App\Repository\MembroChapaRepository;
use App\Repository\MembroComissaoRepository;
use App\Repository\PessoaRepository;
use App\Repository\ProfissionalRepository;
use App\Service\ArquivoService;
use App\Service\CalendarioApiService;
use App\Service\CorporativoService;
use App\To\AbaDenunciaTO;
use App\To\AcompanhamentoDenunciaTO;
use App\To\DenunciaAdmitidaTO;
use App\To\DenunciaEmRelatoriaTO;
use App\To\DenunciaInadmitidaTO;
use App\To\DenunciasCauUfTO;
use App\To\DenunciasFiltroTO;
use App\To\DenunciaTO;
use App\To\DocumentoDenunciaTO;
use App\To\EmailEncaminhamentoAlegacaoFinalTO;
use App\To\FilialTO;
use App\To\MembroComissaoTO;
use App\To\PedidoSolicitadoTO;
use App\To\RecursoDenunciaTO;
use App\To\UsuarioTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;

/**
 * Classe responsável por encapsular as implementações de negócio referente a
 * entidade 'Denuncia'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DenunciaBO extends AbstractBO
{
    /**
     * @var \App\Repository\TipoDenunciaRepository
     */
    private $tipoDenunciaRepository;

    /**
     * @var \App\Repository\RecursoDenunciaRepository
     */
    private $recursoDenunciaRepository;

    /**
     * @var \App\Repository\DenunciaRepository
     */
    private $denunciaRepository;

    /**
     * @var \App\Repository\AtividadeSecundariaCalendario
     */
    private $atividadeSecundariaRepository;

    /**
     * @var \App\Repository\DenunciaOutroRepository
     */
    private $denunciaOutroRepository;

    /**
     * @var \App\Repository\DenunciaChapaRepository
     */
    private $denunciaChapaRepository;

    /**
     * @var \App\Repository\DenunciaMembroChapaRepository
     */
    private $denunciaMembroChapaRepository;

    /**
     * @var \App\Repository\DenunciaMembroComissaoRepository
     */
    private $denunciaMembroComissaoRepository;

    /**
     * @var \App\Repository\TestemunhaDenunciaRepository
     */
    private $testemunhaDenunciaRepository;

    /**
     * @var \App\Repository\SituacaoDenunciaRepository
     */
    private $situacaoDenunciaRepository;

    /**
     * @var \App\Repository\DenunciaSituacaoRepository
     */
    private $denunciaSituacaoRepository;

    /**
     * @var HistoricoDenunciaBO
     */
    private $historicoDenunciaBO;

    /**
     * @var ImpedimentoSuspeicaoBO
     */
    private $impedimentoSuspeicaoBO;


    /**
     * @var DenunciaDefesaBO
     */
    private $denunciaDefesaBO;

    /**
     * @var RecursoContrarrazaoBO
     */
    private $recursoContrarrazaoBO;

    /**
     * @var JulgamentoDenunciaBO
     */
    private $julgamentoDenunciaBO;

    /**
     * @var JulgamentoRecursoDenunciaBO
     */
    private $julgamentoRecursoDenunciaBO;

    /**
     * @var ContrarrazaoRecursoDenunciaBO
     */
    private $contrarrazaoRecursoDenunciaBO;

    /**
     * @var \App\Repository\ArquivoDenunciaRepository
     */
    private $arquivoDenunciaRepository;

    /**
     * @var \App\Repository\ArquivoDenunciaAdmitidaRepository
     */
    private $arquivoDenunciaAdmitidaRepository;

    /**
     * @var \App\Repository\ImpedimentoSuspeicaoRepository
     */
    private $impedimentoSuspeicaoRepository;

    /**
     * @var \App\Repository\ArquivoDenunciaDefesaRepository
     */
    private $arquivoDenunciaDefesaRepository;

    /**
     * @var \App\Service\ArquivoService
     */
    private $arquivoService;

    /**
     * @var \App\Repository\ChapaEleicaoRespository
     */
    private $chapaEleicaoRepository;

    /**
     * @var MembroChapaRepository
     */
    private $membroChapaRepository;

    /**
     * @var MembroComissaoRepository
     */
    private $membroComissaoRepository;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var PessoaRepository
     */
    private $pessoaRepository;

    /**
     * @var CalendarioRepository
     */
    private $calendarioRepository;

    /**
     * @var FilialRepository
     */
    private $filialRepository;

    /**
     * @var ProfissionalRepository
     */
    private $profissionalRepository;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var UsuarioFactory
     */
    private $usuarioFactory;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaBO;

    /**
     * @var ParecerFinalBO
     */
    private $parecerFinalBO;

    /**
     * @var ParecerFinalBO
     */
    private $alegacaoFinalBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var \App\Repository\DenunciaAdmitidaRepository
     */
    private $denunciaAdmitidaRepository;

    /**
     * @var \App\Repository\DenunciaInadmitidaRepository
     */
    private $denunciaInadmitidaRepository;

    /**
     * @var \App\Repository\ArquivoDenunciaInadmitidaRepository
     */
    private $arquivoDenunciaInadmitidaRepository;

    /**
     * @var CalendarioApiService
     */
    private $calendarioApiService;

    /**
     * @var EncaminhamentoDenunciaBO
     */
    private $encaminhamentoDenunciaBO;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var RecursoJulgamentoAdmissibilidadeBO
     */
    private $recursoJulgamentoAdmissibilidadeBO;

    /**
     * @var JulgamentoRecursoAdmissibilidadeBO
     */
    private $julgamentoRecursoAdmissibilidadeBO;

    /**
     * @var PDFFActory
     */
    private $pdfFactory;

    /**
     * @var PessoaBO
     */
    private $pessoaBO;


    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->tipoDenunciaRepository = $this->getRepository(TipoDenuncia::class);
        $this->recursoDenunciaRepository = $this->getRepository(RecursoDenuncia::class);
        $this->denunciaRepository = $this->getRepository(Denuncia::class);
        $this->atividadeSecundariaRepository = $this->getRepository(AtividadeSecundariaCalendario::class);
        $this->denunciaOutroRepository = $this->getRepository(DenunciaOutro::class);
        $this->denunciaChapaRepository = $this->getRepository(DenunciaChapa::class);
        $this->denunciaMembroChapaRepository = $this->getRepository(DenunciaMembroChapa::class);
        $this->denunciaMembroComissaoRepository = $this->getRepository(DenunciaMembroComissao::class);
        $this->testemunhaDenunciaRepository = $this->getRepository(TestemunhaDenuncia::class);
        $this->situacaoDenunciaRepository = $this->getRepository(SituacaoDenuncia::class);
        $this->denunciaSituacaoRepository = $this->getRepository(DenunciaSituacao::class);
        $this->arquivoDenunciaRepository = $this->getRepository(ArquivoDenuncia::class);
        $this->chapaEleicaoRepository = $this->getRepository(ChapaEleicao::class);
        $this->membroChapaRepository = $this->getRepository(MembroChapa::class);
        $this->membroComissaoRepository = $this->getRepository(MembroComissao::class);
        $this->pessoaRepository = $this->getRepository(Pessoa::class);
        $this->calendarioRepository = $this->getRepository(Calendario::class);
        $this->filialRepository = $this->getRepository(Filial::class);
        $this->profissionalRepository = $this->getRepository(Profissional::class);
        $this->corporativoService = app()->make(CorporativoService::class);
        $this->denunciaAdmitidaRepository = $this->getRepository(DenunciaAdmitida::class);
        $this->denunciaInadmitidaRepository = $this->getRepository(DenunciaInadmitida::class);
        $this->arquivoDenunciaInadmitidaRepository = $this->getRepository(ArquivoDenunciaInadmitida::class);
        $this->arquivoDenunciaDefesaRepository = $this->getRepository(ArquivoDenunciaDefesa::class);
        $this->arquivoDenunciaAdmitidaRepository = $this->getRepository(ArquivoDenunciaAdmitida::class);
        $this->impedimentoSuspeicaoRepository = $this->getRepository(ImpedimentoSuspeicao::class);
    }

    /**
     * Retorna um array com todos os tipos de denuncia ordenados por Id.
     *
     * @return array
     */
    public function getTiposDenuncia()
    {
        return $this->tipoDenunciaRepository->findBy([], ['id' => 'ASC']);
    }

    /**
     * Salva os dados de uma denuncia.
     *
     * @param Denuncia $denuncia
     * @return DenunciaTO
     * @throws NegocioException
     */
    public function salvar(Denuncia $denuncia)
    {
        if (empty($denuncia)) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        $this->validarQuantidadeArquivos($denuncia);

        $chapa = $denuncia->getDenunciaChapa();
        $outros = $denuncia->getDenunciaOutros();
        $testemunhas = $denuncia->getTestemunhas();
        $membroChapa = $denuncia->getDenunciaMembroChapa();
        $membroComissao = $denuncia->getDenunciaMembroComissao();
        $denuncia = $this->setNomeArquivoFisico($denuncia);
        $arquivos = (!empty($denuncia->getArquivoDenuncia())) ? clone $denuncia->getArquivoDenuncia() : null;
        $idCauUf = 0;
        $isInclusao = true;
        $denunciaSalva = null;

        $denuncia = $this->limparFilhosDenuncia($denuncia);
        $denuncia = $this->getSequencia($denuncia);

        if (!empty($denuncia->getId())) {
            $isInclusao = false;
        }

        try {
            $this->beginTransaction();

            $denuncia->setPessoa(Pessoa::newInstance([
                "id" => $denuncia->getIdPessoa(),
            ]));

            $atividadeSecundaria = $this->atividadeSecundariaRepository->find($denuncia->getAtividadeSecundaria()->getId());
            $tipoDenuncia = $this->tipoDenunciaRepository->find($denuncia->getTipoDenuncia()->getId());
            $denuncia->setAtividadeSecundaria($atividadeSecundaria);
            $denuncia->setTipoDenuncia($tipoDenuncia);
            $denuncia->setDataHora(Utils::getData());
            $denuncia->setStatus(Constants::STATUS_PENDENTE_ANALISE);

            $this->verificaVigenciaCalendario($atividadeSecundaria->getId());
            /** @var Denuncia $denunciaSalva */

            $denuncia->setFilial(null);
            $denunciaSalva = $this->denunciaRepository->persist($denuncia);

            if (!empty($denunciaSalva->getId())) {

                if (!empty($chapa)) {
                    /** @var ChapaEleicao $chapaEleicao */
                    $chapaEleicao = $this->chapaEleicaoRepository->find($chapa->getChapaEleicao()->getId());
                    $chapa->setDenuncia($denunciaSalva);
                    $chapa->setChapaEleicao($chapaEleicao);
                    $this->denunciaChapaRepository->persist($chapa);

                    if ($chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES) {
                        $idCauUf = Constants::IES_ID;
                    } else {
                        $idCauUf = $chapaEleicao->getIdCauUf();
                    }
                    $denunciaSalva->setDenunciaChapa($chapa);
                }
                if (!empty($outros)) {
                    $outros->setDenuncia($denunciaSalva);
                    if ($outros->getIdCauUf() == Constants::IES_ID) {
                        $outros->setIdCauUf(null);
                    }

                    $this->denunciaOutroRepository->persist($outros);

                    $idCauUf = $outros->getIdCauUf();
                    $denunciaSalva->setDenunciaOutros($outros);
                }
                if (!empty($membroChapa)) {
                    /** @var MembroChapa $membro */
                    $membro = $this->membroChapaRepository->find($membroChapa->getMembroChapa()->getId());
                    $membroChapa->setDenuncia($denunciaSalva);
                    $membroChapa->setMembroChapa($membro);
                    $this->denunciaMembroChapaRepository->persist($membroChapa);

                    $chapaEleicao = $membro->getChapaEleicao();
                    if ($chapaEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES) {
                        $idCauUf = Constants::IES_ID;
                    } else {
                        $idCauUf = $membro->getChapaEleicao()->getIdCauUf();
                    }
                    $denunciaSalva->setDenunciaMembroChapa($membroChapa);
                }
                if (!empty($membroComissao)) {
                    /** @var MembroComissao $membro */
                    $membro = $this->membroComissaoRepository->find($membroComissao->getMembroComissao()->getId());
                    $membroComissao->setDenuncia($denunciaSalva);
                    $membroComissao->setMembroComissao($membro);
                    $this->denunciaMembroComissaoRepository->persist($membroComissao);

                    $idCauUf = $membro->getIdCauUf();
                    $denunciaSalva->setDenunciaMembroComissao($membroComissao);
                }

                $filial = null;
                if ($idCauUf != Constants::IES_ID && !empty($idCauUf)) {
                    $filial = new Filial();
                    $filial->setId((int)$idCauUf);
                }
                $denunciaSalva->setFilial($filial);


                $this->denunciaRepository->persist($denunciaSalva);

                if (!empty($testemunhas)) {
                    foreach ($testemunhas as $testemunha) {
                        $testemunha->setDenuncia($denunciaSalva);
                        $this->testemunhaDenunciaRepository->persist($testemunha);
                    }
                    $denunciaSalva->setTestemunhas($testemunhas);
                }

                if (!empty($arquivos)) {
                    $this->salvarArquivosDenuncia($arquivos, $denunciaSalva, $isInclusao);
                }

                $this->salvarSituacaoDenuncia($denunciaSalva);

                $historicoDenuncia = $this->getHistoricoDenunciaBO()->criarHistorico($denunciaSalva,
                    'Cadastro da denúncia');

                $this->getHistoricoDenunciaBO()->salvar($historicoDenuncia);
            }

            $this->commitTransaction();

        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if (!empty($denunciaSalva)) {
            Utils::executarJOB(new EnviarEmailDenunciaJob($denunciaSalva->getId()));
        }

        return DenunciaTO::newInstance([
            "id" => $denunciaSalva->getId(),
            "data" => $denunciaSalva->getDataHora(),
            "numeroSequencial" => $denunciaSalva->getNumeroSequencial()
        ]);
    }

    /**
     * Busca os destinatários dos emails e chava o envio
     */
    public function enviarEmailResponsaveis($idDenuncia)
    {
        $filtroTO = new \stdClass();
        $filtroTO->idDenuncia = $idDenuncia;
        $denuncias = $this->denunciaRepository->getDenunciasPorFiltro($filtroTO->idDenuncia);

        $denunciaSalva = null;
        if (is_array($denuncias) and !empty($denuncias)) {
            $denunciaSalva = $denuncias[0];
        } else {
            throw new NegocioException(Message::NENHUM_CALENDARIO_ENCONTRADO, null, true);
        }

        $atvSecundaria = $this->atividadeSecundariaRepository->getPorId($denunciaSalva->getAtividadeSecundaria()->getId());
        $nomeTemplate = '';

        $idCauUf = !empty($denunciaSalva->getFilial()) ? $denunciaSalva->getFilial()->getId() : Constants::ID_CAU_BR;
        if ($denunciaSalva->getTipoDenuncia()->getId() === Constants::TIPO_CHAPA) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_CHAPA;
        } else if ($denunciaSalva->getTipoDenuncia()->getId() === Constants::TIPO_MEMBRO_CHAPA) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_MEMBRO_CHAPA;
        } else if ($denunciaSalva->getTipoDenuncia()->getId() === Constants::TIPO_MEMBRO_COMISSAO) {
            $idCauUf = $denunciaSalva->getDenunciaMembroComissao()->getMembroComissao()->getIdCauUf();
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_MEMBRO_COMISSAO;
        } else if ($denunciaSalva->getTipoDenuncia()->getId() === Constants::TIPO_OUTROS) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_OUTROS;
        }

        $this->enviarEmailsDenuncia($denunciaSalva, $atvSecundaria, $nomeTemplate, $idCauUf);
    }

    /**
     * Retorna a lista de denúncias agrupadas e o total
     *
     * @param int $idPessoa
     * @return mixed
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getDenunciaAgrupada($idPessoa)
    {
        if ($idPessoa === null) {
            throw new NegocioException(Message::VALIDACAO_FILTRO_OBRIGATORIO);
        }

        $filiaisComBandeira = $this->getCorporativoService()->getFiliaisComBandeiras();
        $agrupamentoUF = $this->denunciaRepository->getAgrupadaDenunciaPorPessoaUF($idPessoa);

        return $this->incluiBandeirasAgrupamentoUF($agrupamentoUF, $filiaisComBandeira);
    }

    /**
     * Retorna a Denuncia de acordo com o ID informado.
     *
     * @param $idDenuncia
     * @return Denuncia
     * @throws \Exception
     */
    public function getDenuncia($idDenuncia)
    {
        return $this->denunciaRepository->getDenunciaPorId($idDenuncia);
    }

    /**
     * Retorna a denúncia conforme o id informado.
     *
     * @param $id
     * @return Denuncia|null
     */
    public function findById($id)
    {
        return $this->denunciaRepository->find($id);
    }

    /**
     * Retorna o Profissional da Denuncia de acordo com o ID informado.
     *
     * @param $idDenuncia
     *
     * @return array
     * @throws \Exception
     */
    public function validaProfissionalLogadoPorIdDenuncia($idDenuncia)
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $denuncia = $this->denunciaRepository->find($idDenuncia);

        if (null !== $denuncia) {
            $profissionalDenunciado = $this->getProfissionalDenunciadoPorTipoDenuncia($denuncia);

            if (null !== $profissionalDenunciado) {
                $isDenunciado = $profissionalDenunciado->getId() === $usuario->idProfissional;
            }

            if ($denuncia->getTipoDenuncia()->getId() === Constants::TIPO_CHAPA) {
                $membrosChapaEleicao = $denuncia->getDenunciaChapa()->getChapaEleicao()
                    ->getMembrosChapa()->getValues();

                $responsavelChapaEleicao = current(
                    array_filter($membrosChapaEleicao, static function (MembroChapa $membroChapaEleicao) use ($usuario) {
                        return $membroChapaEleicao->getProfissional()->getId() === $usuario->idProfissional
                            && $membroChapaEleicao->isSituacaoResponsavel() === true
                            && $membroChapaEleicao->getStatusParticipacaoChapa()->getId() == Constants::SITUACAO_MEMBRO_CONFIRMADO;
                    })
                );

                $isResponsavelChapa = $responsavelChapaEleicao !== false;
            }

            $isDenunciante = $this->isUsuarioDenunciante($denuncia);
        }

        return [
            'isDenunciado' => $isDenunciado ?? false,
            'isResponsavelChapa' => $isResponsavelChapa ?? false,
            'isDenunciante' => $isDenunciante ?? false,
        ];
    }

    /**
     * Retorna o Membro comissao de acordo com o Usuario Logado.
     *
     * @return mixed
     * @throws NegocioException
     */
    public function getMembroComissaoDenunciaPorUsuario()
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getAtividadeSecundariaPorNiveis(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_MEMBRO_COMISSAO,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_MEMBRO_COMISSAO
        );

        if (empty($atividadeSecundaria)) {
            throw new NegocioException(Message::MSG_NAO_EXISTE_ELEICAO_VIGENTE);
        }

        $membrosComissao = $this->getMembroComissaoBO()->getMembroComissaoPorProfissionalEAtividadeSecundaria(
            $usuario->idProfissional,
            $atividadeSecundaria->getId()
        );

        $hasCoordenadorCEN = false;
        $hasMembroComissaoCE = false;
        $idsCauUfCE = [];

        if (!empty($membrosComissao)) {

            $membroComissaoCEN = array_filter($membrosComissao, function ($membroComissao) {
                if ($membroComissao['idCauUf'] == Constants::COMISSAO_MEMBRO_CAU_BR_ID
                    && $membroComissao['idTipoParticipacao'] != Constants::TIPO_PARTICIPACAO_MEMBRO
                    && $membroComissao['idTipoParticipacao'] != Constants::TIPO_PARTICIPACAO_SUBSTITUTO
                ) {
                    return true;
                }
            });

            $membroComissaoCE = array_filter($membrosComissao, function ($membroComissao) {
                if (($membroComissao['idTipoParticipacao'] == Constants::TIPO_PARTICIPACAO_COORDENADOR ||
                        $membroComissao['idTipoParticipacao'] == Constants::TIPO_PARTICIPACAO_COORDENADOR_SUBSTITUTO ||
                        $membroComissao['idTipoParticipacao'] == Constants::TIPO_PARTICIPACAO_COORDENADOR_ADJUNTO) &&
                    $membroComissao['idCauUf'] != Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                    return true;
                }
            });

            foreach ($membrosComissao as $membroComissao) {
                if ($membroComissao['idTipoParticipacao'] != Constants::TIPO_PARTICIPACAO_MEMBRO &&
                    $membroComissao['idCauUf'] != Constants::COMISSAO_MEMBRO_CAU_BR_ID &&
                    $membroComissao['idCauUf'] != Constants::IES_ID) {

                    $idsCauUfCE[] = $membroComissao['idCauUf'];
                }
            }

            $hasCoordenadorCEN = !empty($membroComissaoCEN) ? true : false;
            $hasMembroComissaoCE = !empty($membroComissaoCE) ? true : false;
        }

        $tipoMembro = new \stdClass();
        $tipoMembro->isCoordenadorCE = $hasMembroComissaoCE;
        $tipoMembro->isCoordenadorCEN = $hasCoordenadorCEN;
        $tipoMembro->isMembroComissaoComum = !empty($membrosComissao) && (!$hasMembroComissaoCE && !$hasCoordenadorCEN);
        $tipoMembro->idsCauUfCE = $idsCauUfCE;

        return $tipoMembro;
    }

    /**
     * Retorna a lista de denúncias agrupadas e o total
     *
     * @return mixed
     * @throws NegocioException
     */
    public function getDenunciaComissaoAgrupada()
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $idsCauUf = [];
        $isMembroComissaoCEN = false;

        $atividadeSecundariaMC = $this->getAtividadeSecundariaCalendarioBO()->getAtividadeSecundariaPorNiveis(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_MEMBRO_COMISSAO,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_MEMBRO_COMISSAO,
            true
        );

        $atividadeSecundariaDenuncia = $this->getAtividadeSecundariaCalendarioBO()->getAtividadeSecundariaPorNiveis(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_DENUNCIA,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_DENUNCIA,
            true
        );

        if (empty($atividadeSecundariaMC) || empty($atividadeSecundariaDenuncia)) {
            throw new NegocioException(Message::MSG_NAO_EXISTE_ELEICAO_VIGENTE);
        }

        $membrosComissao = $this->getMembroComissaoBO()->getMembroComissaoPorProfissionalEAtividadeSecundaria(
            $usuario->idProfissional,
            $atividadeSecundariaMC->getId()
        );

        if (!empty($membrosComissao)) {
            $membroComissaoCEN = array_filter($membrosComissao, function ($membroComissao) {
                if ($membroComissao['idCauUf'] == Constants::COMISSAO_MEMBRO_CAU_BR_ID
                    && $membroComissao['idTipoParticipacao'] != Constants::TIPO_PARTICIPACAO_MEMBRO
                    && $membroComissao['idTipoParticipacao'] != Constants::TIPO_PARTICIPACAO_SUBSTITUTO
                ) {
                    return true;
                }
            });
            $hasCoordenadorCEN = !empty($membroComissaoCEN) ? true : false;

            $membroComissaoCE = array_filter($membrosComissao, function ($membroComissao) {
                if (($membroComissao['idTipoParticipacao'] == Constants::TIPO_PARTICIPACAO_COORDENADOR ||
                        $membroComissao['idTipoParticipacao'] == Constants::TIPO_PARTICIPACAO_COORDENADOR_SUBSTITUTO ||
                        $membroComissao['idTipoParticipacao'] == Constants::TIPO_PARTICIPACAO_COORDENADOR_ADJUNTO) &&
                    $membroComissao['idCauUf'] != Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                    return true;
                }
            });
            $hasMembroComissaoCE = !empty($membroComissaoCE) ? true : false;

            $isMembroComissaoCEN = array_search(Constants::COMISSAO_MEMBRO_CAU_BR_ID, array_column($membrosComissao, 'idCauUf')) === false? false : true;
            if (!$isMembroComissaoCEN) {
                foreach ($membrosComissao as $membroComissao) {
                    $idsCauUf[] = $membroComissao['idCauUf'];
                }

                $idsCauUf = array_unique($idsCauUf);
            }
        }

        $filiaisComBandeira = $this->getCorporativoService()->getFiliaisComBandeiras();
        $agrupamentoUF =
            $this->denunciaRepository->getAgrupamentoDenunciaUfPorAtividadeSecundaria(
                $atividadeSecundariaDenuncia->getId(),
                $idsCauUf,
                $isMembroComissaoCEN
            );

        if (empty($agrupamentoUF)) {
            throw new NegocioException(Message::MSG_NAO_EXISTE_DENUNCIA_PARA_ELEICAO_VIGENTE);
        }

        return $this->incluiBandeirasAgrupamentoUF(
            $agrupamentoUF,
            $filiaisComBandeira,
            $hasCoordenadorCEN,
            $hasMembroComissaoCE
        );
    }

    /**
     * @param $idPessoa
     * @param $idUF
     * @return string
     * @throws NegocioException
     */
    public function getListaDenunciaPessoaUF($idPessoa, $idUF)
    {
        if ($idPessoa === null && $idUF === null) {
            throw new NegocioException(Message::VALIDACAO_FILTRO_OBRIGATORIO);
        }
        $retorno = $this->denunciaRepository->getListaDenunciaPessoaUF($idPessoa, $idUF);
        return $retorno;
    }

    /**
     * Valida se o Usuario Logado tem Acesso a Acompanhamento da Denuncia do Modulo Profissional.
     *
     * @param $idDenuncia
     * @throws NegocioException
     */
    public function validarAcessoDenunciaPorDenuncia($idDenuncia)
    {
        $acessoLiberado = false;
        $denunciasIds = $this->getIdsDenunciasProfissionalAndRelatoria();
        if (!empty($denunciasIds)) {
            foreach ($denunciasIds as $id) {
                if($idDenuncia == $id) {
                    $acessoLiberado = true;
                    break;
                }
            }
        }

        if (!$acessoLiberado) {
            throw new NegocioException(Lang::get('messages.denuncia.permissao_somente_membro_comissao'));
        }
    }

    /**
     * Organiza em um array, os ids de denuncias de um usuario logado, seja denunciante, relator ou denunciado
     * @return array
     * @throws NegocioException
     */
    public function getIdsDenunciasProfissionalAndRelatoria() {
        $denunciasUsuarioLogado = $this->getPorProfissional(true, false, true);
        $denunciasRelatoria = $this->getDenunciasRelatoriaPorProfissional();
        $denunciasIds = null;
        foreach ($denunciasUsuarioLogado as $denuncia) {
            $denunciasIds[] = $denuncia['id_denuncia'];
        }
        foreach ($denunciasRelatoria as $denuncia) {
            $denunciasIds[] = $denuncia->getIdDenuncia();
        }
        return $denunciasIds;
    }

    /**
     * Valida se o Usuario Logado tem Acesso a Acompanhamento da Denuncia do modulo Corporativo.
     *
     * @param $idDenuncia
     * @throws NegocioException
     */
    public function validarAcessoDenunciaCorporativoPorDenuncia($idDenuncia)
    {
        $denuncia = $this->getDenunciaPorId($idDenuncia);
        $idsCauUf = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::ID_CAU_BR;

        $isAssessor = $this->getUsuarioFactory()->isCorporativoAssessorCEN();
        if (!$isAssessor) {
            $isAssessor = $this->getUsuarioFactory()->isCorporativoAssessorCeUfPorCauUf($idsCauUf);
        }

        if (!$isAssessor) {
            throw new NegocioException(Lang::get('messages.denuncia.sem_permissao_de_acesso'));
        }
    }

    /**
     * Retorna a 'Denúncia' conforme o 'id' informado.
     *
     * @param $idDenuncia
     *
     * @return \App\To\AcompanhamentoDenunciaTO
     * @throws \App\Exceptions\NegocioException
     * @throws \Exception
     */
    public function getAcompanhamentoDenunciaPorIdDenuncia($idDenuncia)
    {
        if (null === $idDenuncia) {
            throw new NegocioException(Message::VALIDACAO_FILTRO_OBRIGATORIO);
        }

        $denuncia = $this->denunciaRepository->getDenunciaPorId($idDenuncia);
        $this->verificaDenunciaFilialIES($denuncia);
        if (null === $denuncia) {
            throw new NegocioException(Message::MSG_DENUNCIA_NAO_ENOONTRADA_PARA_ELEICAO);
        }

        $filtro = new \stdClass();
        $filtro->idDenuncia = $idDenuncia;
        $recursosDenuncia = $this->recursoDenunciaRepository->getRecursosPorFiltro($filtro);

        if (!empty($recursosDenuncia) && !is_null($recursosDenuncia)) {
            $recursosContrarrazaoDenuncia = $this->getEstruturaRecursosContrarrazao($recursosDenuncia);
            $denuncia->setRecursoDenuncia($recursosContrarrazaoDenuncia);
        }

        $arquivos = $this->arquivoDenunciaRepository->getArquivoPorDenuncia($denuncia->getId());
        $denuncia->setArquivoDenuncia($arquivos);

        if (!empty($denuncia->getDenunciaDefesa()) && !is_null($denuncia->getDenunciaDefesa())) {
            $arquivosDefesa = $this->arquivoDenunciaDefesaRepository->getArquivoPorDenuncia($denuncia->getId());
            $denuncia->getDenunciaDefesa()->setArquivosDenunciaDefesa($arquivosDefesa);
        }

        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();
        $denuncia->setIsEleicaoVigente(
            $eleicaoVigente && $eleicaoVigente->getId() === $denuncia->getAtividadeSecundaria()
                ->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getId()
        );

        $isRelatorAtual = $this->validarRelatorAtual($denuncia);
        $denuncia->setIsRelatorAtual($isRelatorAtual);

        $usuarioFactory = $this->getUsuarioFactory();
        $isAssessorCEN = $usuarioFactory->isCorporativoAssessorCEN();
        if ($isAssessorCEN) {
            $denuncia->setIsAssessorCEN($isAssessorCEN);
        }

        if ($usuarioFactory->isCorporativoAssessorCEUF()) {
            $filial = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::ID_CAU_BR;
            $isAssessorCEUf = $usuarioFactory->isCorporativoAssessorCeUfPorCauUf($filial);
            $denuncia->setIsAssessorCEUf($isAssessorCEUf);
        }

        if (!$this->podeVerJulgamento($denuncia)) {
            $denuncia->setJulgamentoAdmissibilidade(null);
        }

        $hasDefesaPrazoEncerrado = $this->validarDefesaPrazoEncerradoPorDenuncia($denuncia);
        $denuncia->setHasDefesaPrazoEncerrado($hasDefesaPrazoEncerrado);

        $condicionaisView = $this->getCondicionaisView($denuncia);

        $denuncia->setHasPrazoRecursoDenuncia($condicionaisView->hasPrazoRecursoDenuncia);
        $denuncia->setHasAlegacaoFinalConcluido($condicionaisView->hasAlegacoesFinaisConcluidas);
        $denuncia->setHasAudienciaInstrucaoPendente($condicionaisView->hasAudienciasInstrucaoPendentes);
        $denuncia->setHasImpedimentoSuspeicaoPendente($condicionaisView->hasImpedimentosSuspeicaoPendentes);
        $denuncia->setHasParecerFinalInseridoParaDenuncia($condicionaisView->hasParecerFinalInseridoParaDenuncia);
        $denuncia->setHasAlegacaoFinalPendentePrazoEncerrado($condicionaisView->hasAlegacaoFinalPendentePrazoEncerrado);
        $denuncia->setHasContrarrazaoDenuncianteDentroPrazo($condicionaisView->hasContrarrazaoDenuncianteDentroPrazo);
        $denuncia->setHasContrarrazaoDenunciadoDentroPrazo($condicionaisView->hasContrarrazaoDenunciadoDentroPrazo);
        $denuncia->setHasEncaminhamentoAlegacaoFinal($condicionaisView->hasExisteEncaminhamentoAlegacaoFinal);

        if ($denuncia->getUltimaDenunciaAdmitida() !== null
            && $denuncia->getUltimaDenunciaAdmitida()->getCoordenador() !== null
        ) {
            $denuncia->setCoordenadorComissao(
                MembroComissaoTO::newInstanceFromEntity($denuncia->getUltimaDenunciaAdmitida()->getCoordenador())
            );
        } elseif ($denuncia->getDenunciaInadmitida() !== null
            && $denuncia->getDenunciaInadmitida()->getCoordenador() !== null
        ) {
            $denuncia->setCoordenadorComissao(
                MembroComissaoTO::newInstanceFromEntity($denuncia->getDenunciaInadmitida()->getCoordenador())
            );
        } else {
            $denuncia->setCoordenadorComissao($this->getMembroComissaoBO()->getCoordenadorComissaoPorUf($denuncia->getFilial()->getId()));
        }

        $denuncia->setImpedimentoSuspeicao($this->getImpedimentoSuspeicaoBO()->getPorDenuncia($denuncia->getId()));
        $this->getMembroComissaoBO()->getCoordenadorComissaoPorUf($denuncia->getFilial()->getId());
        $acompanharDenunciaTO = AcompanhamentoDenunciaTO::newInstanceFromEntity($denuncia);

        if(empty($acompanharDenunciaTO->getDenuncia()->getCoordenadorComissao())){
            $acompanharDenunciaTO->getDenuncia()->setCoordenadorComissao($this->getMembroComissaoBO()->getCoordenadorComissaoPorUf($denuncia->getFilial()->getId())[0]);
        }
        return $acompanharDenunciaTO;
    }

    /**
     * Retorna as abas disponíveis para a denuncia de acordo com o 'id' informado.
     *
     * @param $idDenuncia
     *
     * @return \App\To\AbaDenunciaTO
     * @throws \App\Exceptions\NegocioException
     * @throws \Exception
     */
    public function getAbasDisponiveisPorIdDenuncia($idDenuncia)
    {
        if (null === $idDenuncia) {
            throw new NegocioException(Message::VALIDACAO_FILTRO_OBRIGATORIO);
        }

        $acompanhamentoDenunciaTO = $this->getAcompanhamentoDenunciaPorIdDenuncia($idDenuncia);
        $denuncia = $acompanhamentoDenunciaTO->getDenuncia();

        $abasDenuncia = AbaDenunciaTO::newInstance();

        if ($denuncia !== null && ($denuncia->isAssessorCE() || $denuncia->isAssessorCen())) {
            $abasDenuncia->setHasAcompanharDenuncia(true);
            $abasDenuncia->setHasAnaliseAdmissibilidade($denuncia->getDenunciaAdmitida() || $denuncia->getDenunciaInadmitida());

            $hasJulgamentoAdmissibilidade = null !== $denuncia->getJulgamentoAdmissibilidade();
            $abasDenuncia->setHasJulgamentoAdmissibilidade($hasJulgamentoAdmissibilidade);

            $verificacaoRecursoAdmissibilidade = $this->getRecursoJulgamentoAdmissibilidadeBO()->verificaRecursoAdmissibilidade($idDenuncia);

            $recursoJulgamentoAdmissibilidade = $hasJulgamentoAdmissibilidade
                ? $denuncia->getJulgamentoAdmissibilidade()->getRecursoJulgamentoAdmissibilidade()
                : null;
            $hasRecursoJulgamentoAdmissibilidade = $hasJulgamentoAdmissibilidade && $recursoJulgamentoAdmissibilidade;

            $hasPrazoRecursoAdmissibilidade = $verificacaoRecursoAdmissibilidade['parazoRecurso'] ?? false;
            $hasRecursoAdmissibilidade = $hasJulgamentoAdmissibilidade && $hasRecursoJulgamentoAdmissibilidade;

            $mostrarRecursoAdmissibilidade = $hasRecursoAdmissibilidade
                || (($hasJulgamentoAdmissibilidade
                        && $denuncia->getJulgamentoAdmissibilidade()->getIdTipoJulgamento() == Constants::TIPO_JULGAMENTO_ADMISSIBILIDADE_IMPROVIMENTO)
                        && !$hasRecursoAdmissibilidade
                        && !$hasPrazoRecursoAdmissibilidade
                );

            $abasDenuncia->setHasRecursoAdmissibilidade($mostrarRecursoAdmissibilidade);
            $abasDenuncia->setHasJulgamentoRecursoAdmissibilidade(
                $mostrarRecursoAdmissibilidade
                && $hasRecursoJulgamentoAdmissibilidade
                && $recursoJulgamentoAdmissibilidade->getJulgamentoRecurso()
            );

            $hasDenunciaDefesa = $denuncia->hasDefesaPrazoEncerrado() || (!empty($denuncia->getDenunciaDefesa()));

            $abasDenuncia->setHasDefesa($abasDenuncia->hasAnaliseAdmissibilidade() && $hasDenunciaDefesa);
            $abasDenuncia->setHasParecer($abasDenuncia->hasDefesa() && $denuncia->getEncaminhamentosDenuncia());
            $abasDenuncia->setHasJulgamentoPrimeiraInstancia($abasDenuncia->hasParecer() && $denuncia->getJulgamentoDenuncia());

            $recursoDenunciado = current($this->getRecursoContrarrazaoPorTipo($denuncia->getRecursosDenuncia(), Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO));
            $recursoDenunciante = current($this->getRecursoContrarrazaoPorTipo($denuncia->getRecursosDenuncia(), Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE));

            $abasDenuncia->setHasRecursoDenunciado($abasDenuncia->hasJulgamentoPrimeiraInstancia() && (
                    $recursoDenunciado || (!$recursoDenunciado && !$denuncia->isHasPrazoRecursoDenuncia())
                )
            );
            $abasDenuncia->setHasRecursoDenunciante($abasDenuncia->hasJulgamentoPrimeiraInstancia() && (
                    $recursoDenunciante || (!$recursoDenunciante && !$denuncia->isHasPrazoRecursoDenuncia())
                )
            );

            $abasDenuncia->setHasJulgamentoSegundaInstancia(
                ($abasDenuncia->hasRecursoDenunciado() && $recursoDenunciado && $recursoDenunciado->getJulgamentoRecurso())
                || ($abasDenuncia->hasRecursoDenunciante() && $recursoDenunciante && $recursoDenunciante->getJulgamentoRecurso())
            );
        }

        return $abasDenuncia;
    }

    private function podeVerJulgamento(Denuncia $denuncia)
    {
        $denunciaAdmitida = $denuncia->getUltimaDenunciaAdmitida();
        if($denunciaAdmitida) {
            return true;
        }

        $filial = $this->verificaDenunciaIdFilialIES($denuncia);
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        return $this->getUsuarioFactory()->isCorporativoAssessorCEN() ||
            $this->getUsuarioFactory()->isCorporativoAssessorCeUfPorCauUf($filial) ||
            $this->isUsuarioDenunciante($denuncia) ||
            $this->membroComissaoRepository->isCoordenador($usuario->idProfissional, Constants::COMISSAO_MEMBRO_CAU_BR_ID) ||
            $this->membroComissaoRepository->isCoordenador($usuario->idProfissional, $filial);
    }

    /**
     * Verifica se a filial é IES
     * @param Denuncia $denuncia
     */
    public function verificaDenunciaFilialIES(Denuncia $denuncia) {
        if(is_null($denuncia->getFilial())){
            $denuncia->setFilial($this->getFilialBO()->getFilialIES());
        }
    }

    /**
     * Verifica se a filial é IES e caso for de IES, substitui para 165: CAU BR
     *
     * @param Denuncia $denuncia
     * @return int
     */
    public function verificaDenunciaIdFilialIES(Denuncia $denuncia) {

        $filial = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::ID_CAU_BR;

        if ($filial == Constants::IES_ID) {
            $filial = Constants::ID_CAU_BR;
        }

        return $filial;
    }

    /**
     * Disponibiliza o arquivo 'Denúncia' para 'download' conforme o 'id'
     * informado.
     *
     * @param $idArquivo
     *
     * @return \App\To\ArquivoTO
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getArquivo($idArquivo)
    {
        $arquivoDenuncia = $this->getArquivoDenuncia($idArquivo);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenuncia($arquivoDenuncia->getDenuncia()->getId());
        return $this->getArquivoService()->getArquivo($caminho, $arquivoDenuncia->getNomeFisico(), $arquivoDenuncia->getNome());
    }

    /**
     * Disponibiliza o arquivo 'Denúncia Inadmitida' para 'download' conforme o
     * 'id' informado.
     *
     * @param $idArquivo
     *
     * @return \App\To\ArquivoTO
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getArquivoInadmitida($idArquivo)
    {
        $arquivoDenuncia = $this->getArquivoDenunciaInadmitida($idArquivo);
        $denunciaInadmitida = $arquivoDenuncia->getDenunciaInadmitida();

        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenuncia($denunciaInadmitida->getDenuncia()->getId());
        return $this->getArquivoService()->getArquivo($caminho, $arquivoDenuncia->getNomeFisico(), $arquivoDenuncia->getNome());
    }

    /**
     * Recupera as Denuncias de acordo com o Profissional Informado.
     *
     * @param bool $returnDenunciante
     * @param bool $isReturnRelatoria
     * @return array|null
     * @throws NegocioException
     */
    public function getPorProfissional($returnDenunciante = false, $isReturnRelatoria = true, $validaAcesso = false)
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $denunciasCauUfTO = [];

        if (!empty($usuario)) {
            $denuncias = $this->denunciaRepository->getPorProfissional(
                $usuario->idProfissional,
                $returnDenunciante,
                $isReturnRelatoria
            );

            $denunciasCauUfTO = $denuncias;

            if (!empty($denuncias) && !$validaAcesso) {
                $denunciasCauUfTO = [];
                foreach ($denuncias as $denuncia) {
                    $qntEncaminhamento =
                        $this->getEncaminhamentoDenunciaBO()->getTotalEncaminhamentosPendentesPorDenunciaEUsuario(
                            $denuncia['id_denuncia']
                        );

                    $denunciaTO = DenunciasCauUfTO::newInstance($denuncia);
                    $this->setNomeDenunciado($denuncia, $denunciaTO);
                    $denunciaTO->setQuantidadeEncaminhamentosPendentes($qntEncaminhamento->quantidade);
                    $denunciaTO->setDescricaoEncaminhamentosPendentes($qntEncaminhamento->tiposEncaminhamento);

                    $denunciasCauUfTO[] = $denunciaTO;
                }
            }
        }

        return $denunciasCauUfTO;
    }

    public function setNomeDenunciado($denuncia, DenunciasCauUfTO $denunciaTO) {
        switch ($denuncia['id_tipo_denuncia']) {
            case Constants::TIPO_CHAPA :
                $denunciaTO->setNomeDenunciado($denuncia['nome_denunciado']);
                break;
            case Constants::TIPO_MEMBRO_CHAPA :
                $denunciaTO->setNomeDenunciado($denuncia['nome_denunciado_chapa']);
                break;
            case Constants::TIPO_MEMBRO_COMISSAO :
                $denunciaTO->setNomeDenunciado($denuncia['nome_denunciado_comissao']);
                break;
            case Constants::TIPO_OUTROS :
                $denunciaTO->setNomeDenunciado('-');
                break;
        }
    }

    /**
     * Recupera as Denuncias de acordo com o Profissional Informado.
     *
     * @return array|null
     * @throws NegocioException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function getDenunciaComissaoAdmissibilidade()
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $idsCauUf = [];

        $atividadeSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getAtividadeSecundariaPorNiveis(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_MEMBRO_COMISSAO,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_MEMBRO_COMISSAO
        );

        if (null === $atividadeSecundaria) {
            throw new NegocioException(Message::MSG_NAO_EXISTE_ELEICAO_VIGENTE);
        }

        $membrosComissao = $this->getMembroComissaoBO()->getMembroComissaoPorProfissionalEAtividadeSecundaria(
            $usuario->idProfissional,
            $atividadeSecundaria->getId()
        );

        if (!empty($membrosComissao)) {
            $membroComissaoCEN = array_filter($membrosComissao, function ($membroComissao) {
                if ($membroComissao['idCauUf'] == Constants::COMISSAO_MEMBRO_CAU_BR_ID
                    && $membroComissao['idTipoParticipacao'] != Constants::TIPO_PARTICIPACAO_MEMBRO) {
                    return true;
                }
            });
            $hasCoordenadorCEN = !empty($membroComissaoCEN) ? true : false;

            if (!$hasCoordenadorCEN) {
                foreach ($membrosComissao as $membroComissao) {
                    $idsCauUf[] = $membroComissao['idCauUf'];
                }
            } else {
                $idsCauUf[] = Constants::COMISSAO_MEMBRO_CAU_BR_ID;
                $idsCauUf[] = Constants::IES_ID;
            }
        } else {
            throw new NegocioException(Message::MSG_NAO_EXISTE_ELEICAO_VIGENTE);
        }

        $denunciasFiltroTO = DenunciasFiltroTO::newInstance([
            'naoAdmitida' => true,
            'idsCauUf' => $idsCauUf,
        ]);

        $denuncias = $this->denunciaRepository->getDenunciasAdmissibilidadeCauUfPorFiltro($denunciasFiltroTO);
        return array_map(static function (Denuncia $denuncia) {
            return DenunciasCauUfTO::newInstanceFromEntity($denuncia);
        }, $denuncias);
    }

    /**
     * Agrupamento de Denuncias de UF por Atividades Secundárias.
     *
     * @param $idCalendario
     * @return mixed
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getAgrupamentoDenunciaUfPorCalendario($idCalendario)
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $idsCauUf = [];
        $isAcessorCEN = $this->getUsuarioFactory()->hasPermissao(Constants::PERMISSAO_ACESSOR_CEN);

        if ($this->getUsuarioFactory()->hasPermissao(Constants::PERMISSAO_ACESSOR_CE_UF)) {
            $idsCauUf[] = $usuario->idCauUf;
        }

        $atividadeSecundaria =
            $this->getAtividadeSecundariaCalendarioBO()->getAtividadeSecundariaPorCalendario($idCalendario);

        if (empty($atividadeSecundaria)) {
            throw new NegocioException(Message::MSG_DENUNCIA_NAO_ENOONTRADA_PARA_ELEICAO);
        }

        $filiaisComBandeira = $this->getCorporativoService()->getFiliaisComBandeiras();
        $agrupamentoUF =
            $this->denunciaRepository->getAgrupamentoDenunciaUfPorAtividadeSecundaria(
                $atividadeSecundaria->getId(),
                $idsCauUf,
                $isAcessorCEN
            );

        if (empty($agrupamentoUF)) {
            throw new NegocioException(Message::MSG_DENUNCIA_NAO_ENOONTRADA_PARA_ELEICAO);
        }

        return $this->incluiBandeirasAgrupamentoUF($agrupamentoUF, $filiaisComBandeira);
    }

    /**
     * Recupera as Denuncias de acordo com o id_cau_uf informado.
     *
     * @param $idCauUf
     * @param $idCalendario
     *
     * @return mixed
     * @throws \Exception
     */
    public function getDenunciasPorCauUf($idCauUf, $idCalendario = null)
    {
        $denunciaFiltroTO = DenunciasFiltroTO::newInstance([
            'idsCauUf' => [$idCauUf],
            'idEleicao' => $idCalendario,
        ]);

        $denuncias = $this->denunciaRepository->getDenunciasDetalhamentoCauUfPorFiltro($denunciaFiltroTO);
        return array_map(static function (Denuncia $denuncia) {
            return DenunciasCauUfTO::newInstanceFromEntity($denuncia);
        }, $denuncias);
    }

    /**
     * Recupera as Denuncias de acordo com o id_cau_uf informado.
     *
     * @param $idCauUf
     *
     * @return mixed
     * @throws \Exception
     */
    public function getDenunciasPorCauUfPessoa($idCauUf)
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $denunciasCauUfTO = [];

        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();

        $denunciaFiltroTO = DenunciasFiltroTO::newInstance([
            'idsCauUf' => [$idCauUf],
            'idPessoa' => $usuario->id,
            'idEleicao' => $eleicaoVigente->getId(),
        ]);

        $denuncias = $this->denunciaRepository->getDenunciasCauUfPorFiltro($denunciaFiltroTO);

        foreach ($denuncias as $denuncia) {
            $qntEncaminhamento =
                $this->getEncaminhamentoDenunciaBO()->getTotalEncaminhamentosPendentesPorDenunciaEUsuario(
                    $denuncia->getId()
                );

            $denunciaTO = DenunciasCauUfTO::newInstanceFromEntity($denuncia);
            $denunciaTO->setQuantidadeEncaminhamentosPendentes($qntEncaminhamento->quantidade);
            $denunciaTO->setDescricaoEncaminhamentosPendentes($qntEncaminhamento->tiposEncaminhamento);

            $denunciasCauUfTO[] = $denunciaTO;
        }

        return $denunciasCauUfTO;
    }

    /**
     * Recupera as Denuncias de acordo com o id_cau_uf informado.
     *
     * @param $idCauUf
     *
     * @return mixed
     * @throws \Exception
     */
    public function getDenunciasComissaoPorCauUfPessoa($idCauUf)
    {
        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();

        $denunciaFiltroTO = DenunciasFiltroTO::newInstance([
            'idsCauUf' => [$idCauUf],
            'idEleicao' => $eleicaoVigente->getId(),
        ]);

        $denuncias = $this->denunciaRepository->getDenunciasCauUfPorFiltro($denunciaFiltroTO);
        return array_map(static function (Denuncia $denuncia) {
            return DenunciasCauUfTO::newInstanceFromEntity($denuncia);
        }, $denuncias);
    }

    /**
     * Recupera as Denuncias em relatoria de acordo com o profissional.
     *
     * @return mixed
     */
    public function getDenunciasRelatoriaPorProfissional()
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $denuncias = $this->denunciaRepository->getDenunciasRelatoriaPorProfissional($usuario->idProfissional);

        return array_map(static function (Denuncia $denuncia) {
            return DenunciaEmRelatoriaTO::newInstanceFromEntity($denuncia);
        }, $denuncias);
    }

    /**
     * @return Denuncia[][]|\Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function iteratorDenunciasAguardandoRelatores()
    {
        return $this->denunciaRepository->iteratorDenunciasAguardandoRelatores();
    }

    /**
     * Recupera a Instancia de Atividade Secundaria Calenadrio BO.
     *
     * @return AtividadeSecundariaCalendarioBO
     */
    public function getAtividadeSecundariaCalendarioBO()
    {
        if (empty($this->atividadeSecundariaCalendarioBO)) {
            $this->atividadeSecundariaCalendarioBO = new AtividadeSecundariaCalendarioBO();
        }

        return $this->atividadeSecundariaCalendarioBO;
    }

    /**
     * Altera a situação da denuncia e cria um registro de situação para
     * histórico de alteração.
     *
     * @param \App\Entities\Denuncia $denuncia
     * @param $statusAtual
     * @param $situacaoAtual
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function alterarStatusSituacaoDenuncia(Denuncia $denuncia, $statusAtual, $situacaoAtual = null)
    {
        $situacoesCorrespondenteStatus = [
            Constants::SITUACAO_DENUNCIA_EM_RELATORIA => Constants::STATUS_DENUNCIA_ADMITIDA,
            Constants::SITUACAO_DENUNCIA_EM_JULGAMENTO => Constants::STATUS_DENUNCIA_INADMITIDA
        ];

        if (key_exists($statusAtual, $situacoesCorrespondenteStatus)) {
            $denuncia->setStatus($situacoesCorrespondenteStatus[$statusAtual]);
            $denuncia = $this->denunciaRepository->persist($denuncia);
        }

        $this->salvarSituacaoDenuncia($denuncia, $situacaoAtual ?? $statusAtual);
    }

    /**
     * Gerar Extrato das informações de uma denúncia.
     *
     * @param AbaDenunciaTO $abaDenunciaTO
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function gerarDocumentoExtratoDenuncia(AbaDenunciaTO $abaDenunciaTO)
    {
        $documentoDenunciaTO = DocumentoDenunciaTO::newInstance([
            'ip' => Request::ip(),
            'usuario' => $this->getUsuarioFactory()->getUsuarioLogado()->nome,
            'data' => Utils::getData()
        ]);

        $denuncia = $this->findById($abaDenunciaTO->getIdDenuncia());

        $filial = $this->getFilialBO()->getPorId($this->getUsuarioFactory()->getUsuarioLogado()->idCauUf);
        $documentoDenunciaTO->setFilialTO(FilialTO::newInstanceFromEntity($filial));

        $testemunhas = $denuncia->getTestemunhas() ?? [];
        if (!is_array($testemunhas)) {
            $testemunhas = $testemunhas->toArray();
        }
        $denuncia->setTestemunhas($testemunhas);

        $documentoDenunciaTO->setDenuncia($this->getDenunciaTOByDenuncia($denuncia));

        if ($abaDenunciaTO->hasAnaliseAdmissibilidade()) {
            $this->setPropsDocumentoDenunciaAnaliseAdmissibilidade($documentoDenunciaTO);
        }

        if($abaDenunciaTO->hasJulgamentoAdmissibilidade()) {
            $arquivos = $documentoDenunciaTO->getDenuncia()->getJulgamentoAdmissibilidade()->getArquivos();
            if(!empty($arquivos)) {
                $documentoDenunciaTO->getDenuncia()->getJulgamentoAdmissibilidade()->setArquivos($this->getDescricaoArquivoExportar($arquivos,
                    $this->getArquivoService()->getCaminhoRepositorioJulgamentoAdmissibilidade($documentoDenunciaTO->getDenuncia()->getJulgamentoAdmissibilidade()->getId())));
            }
        }

        if($abaDenunciaTO->hasRecursoAdmissibilidade()) {
            $this->setPropsDocumentoDenunciaRecursoAdmissibilidade($documentoDenunciaTO);
        }

        $prazoRecurso = $this->getRecursoContrarrazaoBO()->isPrazoRecursoDenuncia(
            $denuncia->getPrimeiroJulgamentoDenuncia());

        $prazoContrarrazao = $this->getContrarrazaoRecursoDenunciaBO()->getPrazoContrarrazaoRecursos($denuncia);

        if ($abaDenunciaTO->hasJulgamentoRecursoAdmissibilidade()) {
            $documentoDenunciaTO->setJulgamentoRecursoAdmissibilidade(
                $this->getJulgamentoRecursoAdmissibilidadeBO()->getExportarInformacoesJulgamentoRecursoAdmissibilidade(
                    $denuncia
                )
            );
        }

        if ($abaDenunciaTO->hasDefesa()) {
            $documentoDenunciaTO->setDefesa(
                $this->getDenunciaDefesaBO()->getExportarInformacoesDefesa($abaDenunciaTO->getIdDenuncia())
            );
        }

        if ($abaDenunciaTO->hasParecer()) {
            $documentoDenunciaTO->setEncaminhamentos(
                $this->getEncaminhamentoDenunciaBO()->getExportarInformacoesParecer($abaDenunciaTO->getIdDenuncia())
            );
        }

        if ($abaDenunciaTO->hasJulgamentoPrimeiraInstancia()) {
            $documentoDenunciaTO->setJulgamentoPrimeiraInstancia(
                $this->getJulgamentoDenunciaBO()->getExportarInformacoesJulgamento($denuncia->getUltimoJulgamentoDenuncia())
            );
        }

        if ($abaDenunciaTO->hasRecursoDenunciante()) {
            $recursoDenunciante = $this->getRecursoContrarrazaoBO()->getExportarRecursoDenunciaPorTipoRecurso(
                $abaDenunciaTO->getIdDenuncia(),
                Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE
            );
            $recursoDenunciante->setIsPrazoRecurso($prazoRecurso ?? false);
            $recursoDenunciante->setIsPrazoContrarrazao($prazoContrarrazao->hasContrarrazaoRecursoDenunciadoDentroPrazo);

            if (!$recursoDenunciante->getId()) {
                $recursoDenunciante->setTipoRecurso(Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE);
            }

            $documentoDenunciaTO->setRecursoDenunciante($recursoDenunciante);
        }

        if ($abaDenunciaTO->hasRecursoDenunciado()) {
            $recursoDenunciado = $this->getRecursoContrarrazaoBO()->getExportarRecursoDenunciaPorTipoRecurso(
                $abaDenunciaTO->getIdDenuncia(),
                Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO
            );
            $recursoDenunciado->setIsPrazoRecurso($prazoRecurso ?? false);
            $recursoDenunciado->setIsPrazoContrarrazao($prazoContrarrazao->hasContrarrazaoRecursoDenuncianteDentroPrazo);

            if (!$recursoDenunciado->getId()) {
                $recursoDenunciado->setTipoRecurso(Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO);
            }

            $documentoDenunciaTO->setRecursoDenunciado($recursoDenunciado);
        }

        if ($abaDenunciaTO->hasJulgamentoSegundaInstancia()) {
            $julgamentoRecursoDenunciado = $this->getJulgamentoRecursoDenunciaBO()->getExportarJulgamentoRecursosPorTipoRecurso(
                $abaDenunciaTO->getIdDenuncia()
            );

            $documentoDenunciaTO->setJulgamentoSegundaInstancia($julgamentoRecursoDenunciado);
        }

        return $this->getPdfFactory()->gerarDocumentoExtratoDenuncia([
            'documentoDenuncia' => $documentoDenunciaTO,
            'abas' => $abaDenunciaTO,
            'quebraPagina' => 0
        ]);
    }

    /**
     * Recupera um objeto DenunciaTO apartir de uma denuncia
     * @param Denuncia $denuncia
     * @return DenunciaTO
     * @throws \Exception
     */
    public function getDenunciaTOByDenuncia(Denuncia $denuncia)
    {
        $filial = $denuncia->getFilial();

        $filialDenuncia = null !== $filial
            ? $this->getFilialBO()->getPorId($filial->getId())
            : Filial::newInstance(['id' => Constants::ID_CAUBR_IES, 'prefixo' => Constants::PREFIXO_IES]);
        $denuncia->setFilial($filialDenuncia);

        $denunciaTO = DenunciaTO::newInstanceFromEntity($denuncia);
        $denunciaTO->setDocumentos($this->getDescricaoArquivoExportar(
            $denuncia->getArquivoDenuncia(), $this->getArquivoService()->getCaminhoRepositorioDenuncia($denuncia->getId())
        ));
        return $denunciaTO;
    }

    /**
     * Define as propriedades de arquivos e coordenador para analise de admissibilidade
     * @param DocumentoDenunciaTO $documentoDenunciaTO
     * @throws NegocioException
     * @throws \Exception
     */
    public function setPropsDocumentoDenunciaAnaliseAdmissibilidade(DocumentoDenunciaTO $documentoDenunciaTO)
    {
        $denuncia = $documentoDenunciaTO->getDenuncia();
        $denunciaAdmitida = $denuncia->getAnaliseAdmissibilidade()->getDenunciaAdmitida();

        if ($denunciaAdmitida && !empty($denunciaAdmitida->getId())) {
            $arquivos = $this->getDescricaoArquivoExportar($denunciaAdmitida->getArquivoDenunciaAdmitida(),
                $this->getArquivoService()->getCaminhoRepositorioDenunciaAdmitida($denunciaAdmitida->getId()));
            $denunciaAdmitida->setArquivoDenunciaAdmitida($arquivos);
        } else {
            $coordenadores = $this->getCoordenadorEAdjuntoComissao($denuncia->getFilial()->getId());
            $denuncia->getAnaliseAdmissibilidade()->setCoordenadores($coordenadores);

            $arquivosInadmitida = $denuncia->getAnaliseAdmissibilidade()->getDenunciaInadmitida()->getArquivos();
            $denuncia->getAnaliseAdmissibilidade()->getDenunciaInadmitida()->setArquivos(
                $this->getDescricaoArquivoExportar($arquivosInadmitida,
                    $this->getArquivoService()->getCaminhoRepositorioDenuncia($denuncia->getId())
            ));
        }
    }

    /**
     * Define as informações para recurso do julgamento de admissibilidade
     * @param DocumentoDenunciaTO $documentoDenunciaTO
     * @throws \Exception
     */
    public function setPropsDocumentoDenunciaRecursoAdmissibilidade(DocumentoDenunciaTO $documentoDenunciaTO)
    {
        $julgamentoAdmissibilidade = $documentoDenunciaTO->getDenuncia()->getJulgamentoAdmissibilidade();

        if($julgamentoAdmissibilidade) {
            $hasPrazoRecursoAdmissibilidade = $this->getRecursoJulgamentoAdmissibilidadeBO()->validaPrazoRecursoInformado($documentoDenunciaTO->getDenuncia()->getJulgamentoAdmissibilidade());
            $julgamentoAdmissibilidade->setHasPrazoRecursoJulgamentoAdmissibilidade($hasPrazoRecursoAdmissibilidade);

            $recursoJulgamentoAdmissibilidade = $julgamentoAdmissibilidade->getRecursoJulgamentoAdmissibilidade();

            if ($recursoJulgamentoAdmissibilidade) {
                $arquivos = $recursoJulgamentoAdmissibilidade->getArquivos();
                if(!empty($arquivos)) {
                    $recursoId = $recursoJulgamentoAdmissibilidade->getId();
                    $recursoJulgamentoAdmissibilidade->setArquivos($this->getDescricaoArquivoExportar($arquivos,
                        $this->getArquivoService()->getCaminhoRepositorioRecursoJulgamentoAdmissibilidade($recursoId)));
                }

                $historico = $this->getHistoricoDenunciaBO()->getHistoricoDenunciaPorDenunciaEAcao(
                    $documentoDenunciaTO->getDenuncia()->getId(), Constants::ACAO_RECURSO_JULGAMENTO_ADMISSIBILIDADE
                );
                $usuarioRecurso = $this->getPessoaBO()->getPessoaPorId($historico->getResponsavel());
                $recursoJulgamentoAdmissibilidade->setSolicitante($usuarioRecurso->getProfissional());
            }
        }
    }

    /**
     * Retorna a descricao dos arquivos para exportação PDF
     *
     * @param $arquivos
     * @param $caminho
     * @return null|array
     * @throws \Exception
     */
    public function getDescricaoArquivoExportar($arquivos, $caminho)
    {
        $documentos = null;

        if (!empty($arquivos)) {
            foreach ($arquivos as $arquivo) {
                $documentos[] = $this->getArquivoService()->getDescricaoArquivo(
                    $caminho, $arquivo->getNomeFisico(), $arquivo->getNome()
                );
            }
        }

        return $documentos;
    }

    /**
     * Salva os dados de uma denuncia admitida.
     *
     * @param DenunciaAdmitida $denunciaAdmitida
     *
     * @return DenunciaAdmitidaTO
     * @throws \Exception
     */
    public function admitir(DenunciaAdmitida $denunciaAdmitida)
    {
        $denunciaAdmitidaSalva = null;
        try {
            $this->beginTransaction();

            $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
            $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();

            $denuncia = $this->denunciaRepository->find($denunciaAdmitida->getIdDenuncia());
            $membroComissao = $this->membroComissaoRepository->find($denunciaAdmitida->getIdMembroComissao());

            $coordenador = $this->membroComissaoRepository->getPorCalendarioAndProfissional(
                $eleicaoVigente->getCalendario()->getId(),
                $usuario->idProfissional
            );

            $this->validarAdmissaoDenuncia($denuncia);

            $denuncia->setStatus(Constants::STATUS_DENUNCIA_ADMITIDA);
            $denuncia = $this->denunciaRepository->persist($denuncia);

            $idSituacaoDenuncia = Constants::STATUS_DENUNCIA_AGUARDANDO_DEFESA;
            if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_OUTROS) {
                $idSituacaoDenuncia = Constants::STATUS_DENUNCIA_EM_RELATORIA;
            }
            $this->salvarSituacaoDenuncia($denuncia, $idSituacaoDenuncia);

            $denunciaAdmitida->setDenuncia($denuncia);
            $denunciaAdmitida->setMembroComissao($membroComissao);
            $denunciaAdmitida->setCoordenador($coordenador);
            $denunciaAdmitida->setDataAdmissao(Utils::getData());

            $denunciaAdmitidaSalva = $this->denunciaAdmitidaRepository->persist($denunciaAdmitida);

            $historicoDenuncia = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
                'Admitir');

            $this->getHistoricoDenunciaBO()->salvar($historicoDenuncia);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if (!empty($denuncia)) {
            Utils::executarJOB(new EnviarEmailDenunciaAdmitirInadmitirJob($denuncia->getId(), $denuncia->getTipoDenuncia()));
        }

        return DenunciaAdmitidaTO::newInstanceFromEntity($denunciaAdmitidaSalva);
    }

    /**
     * Salva os dados de uma relator na denuncia.
     *
     * @param DenunciaAdmitida $denunciaAdmitida
     * @param $idEncaminhamento
     * @return DenunciaAdmitida
     * @throws NegocioException
     */
    public function relator(DenunciaAdmitida $denunciaAdmitida, $idEncaminhamento)
    {
        $denunciaAdmitidaSalva = null;

        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();

        $coordenador = $this->membroComissaoRepository->getPorCalendarioAndProfissional(
            $eleicaoVigente->getCalendario()->getId(),
            $usuario->idProfissional
        );

        $this->validarCamposObrigatoriosAdmitirRelator($denunciaAdmitida);
        $this->validarQuantidadeArquivosAdmitida($denunciaAdmitida);
        $denunciaAdmitida = $this->setNomeArquivoFisicoAdmitido($denunciaAdmitida);
        $arquivos = (!empty($denunciaAdmitida->getArquivoDenunciaAdmitida())) ? clone $denunciaAdmitida->getArquivoDenunciaAdmitida() : null;
        $denuncia = null;
        $denunciaAdmitida->setArquivoDenunciaAdmitida(null);

        try {
            $this->beginTransaction();
            $denuncia = $this->denunciaRepository->find($denunciaAdmitida->getIdDenuncia());
            $membroComissao = $this->membroComissaoRepository->find($denunciaAdmitida->getIdMembroComissao());

            $this->salvarSituacaoDenuncia($denuncia, Constants::STATUS_DENUNCIA_EM_RELATORIA);

            $denunciaAdmitida->setDenuncia($denuncia);
            $denunciaAdmitida->setMembroComissao($membroComissao);
            $denunciaAdmitida->setCoordenador($coordenador);
            $denunciaAdmitida->setDataAdmissao(Utils::getData());

            $denunciaAdmitidaSalva = $this->denunciaAdmitidaRepository->persist($denunciaAdmitida);
            if (!empty($denunciaAdmitidaSalva)) {
                $this->salvarArquivosDenunciaAdmitida($arquivos, $denunciaAdmitidaSalva, $denuncia);
            }

            $historicoDenuncia = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
                'Substituição de relator');

            $this->getHistoricoDenunciaBO()->salvar($historicoDenuncia);

            $encaminhamento = $this->getEncaminhamentoDenunciaBO()->findById($idEncaminhamento);

            $this->getEncaminhamentoDenunciaBO()->alterarTipoSituacaoEncaminhamento($encaminhamento, Constants::TIPO_SITUACAO_ENCAMINHAMENTO_CONCLUIDO);

            $impSuspeicao = ImpedimentoSuspeicao::newInstance([]);
            $impSuspeicao->setDenunciaAdmitida($denunciaAdmitidaSalva);
            $impSuspeicao->setEncaminhamentoDenuncia($encaminhamento);

            $this->impedimentoSuspeicaoRepository->persist($impSuspeicao);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailInserirNovoRelatorJob($idEncaminhamento));

        return DenunciaAdmitidaTO::newInstanceFromEntity($denunciaAdmitidaSalva);
    }

    /**
     * Verifica se a ação de inserir novo relator está disponível
     *
     * @param int $idEncaminhamento
     * @return bool
     */
    public function isAcaoDisponivelInseirNovoRelator(int $idEncaminhamento)
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $encaminhamento = $this->getEncaminhamentoDenunciaBO()->findById($idEncaminhamento);
        $filial = !empty($encaminhamento->getDenuncia()->getFilial())
            ? $encaminhamento->getDenuncia()->getFilial()->getId()
            : Constants::ID_CAU_BR;

        $isPermissao = $this->getMembroComissaoBO()->isCoordenadorPorAtividadeSecundariaAndCauUf(
            $encaminhamento->getDenuncia()->getAtividadeSecundaria()->getId(),
            $filial, $usuarioLogado->idProfissional
        );

        if ($isPermissao && !$this->isVerificarSituacaoEncaminhamentoInserirNovoRelator($encaminhamento)) {
            $isPermissao = false;
        }

        return $isPermissao;
    }

    /**
     * Verifica se a ação de inserir novo relator está disponível
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     * @return bool
     */
    public function isVerificarSituacaoEncaminhamentoInserirNovoRelator(EncaminhamentoDenuncia $encaminhamento)
    {
        $isSituacao = false;

        if($encaminhamento->getTipoEncaminhamento()->getId() === Constants::TIPO_ENCAMINHAMENTO_IMPEDIMENTO_SUSPEICAO
            && $encaminhamento->getTipoSituacaoEncaminhamento()->getId() === Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE){
            $isSituacao = true;
        }

        return $isSituacao;
    }

    /**
     * Enviar e-emails ao inserir novo relator
     *
     * @param int $idEncaminhamento
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function enviarEmailInserirNovoRelator($idEncaminhamento)
    {
        $encaminhamento = $this->getEncaminhamentoDenunciaBO()->findById($idEncaminhamento);

        $this->enviarEmailPorTipos($encaminhamento, [
            Constants::EMAIL_INFORMATIVO_NOVO_RELATOR_DENUNCIA,
            Constants::EMAIL_INFORMATIVO_NOVO_RELATOR_COORDENADORES_CE_CEN,
            Constants::EMAIL_INFORMATIVO_NOVO_RELATOR_ASSESSORES_CEN_CE,
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
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_INSERIR_NOVO_RELATOR,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_NOVO_RELATOR
        );

        $emailAlegacaoFinalTO = EmailEncaminhamentoAlegacaoFinalTO::newInstanceFromEntity($encaminhamento);
        $emailAlegacaoFinalTO->setProcessoEleitoral(
            $atividade->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada()
        );
        $emailAlegacaoFinalTO->setStatusDenuncia(
            $this->getNomeSituacaoAtualDenuncia($encaminhamento->getDenuncia()->getId())
        );

        foreach ($tipos as $tipo) {
            $destinarios = $this->getDestinatariosEmail($encaminhamento->getDenuncia(), $tipo);

            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(), $tipo
            );

            if (!empty($emailAtividadeSecundaria) && !empty($destinarios)) {
                $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                $emailTO->setDestinatarios(array_unique($destinarios));

                Email::enviarMail(new InserirNovoRelatorMail($emailTO, $emailAlegacaoFinalTO));
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

        if ($tipo == Constants::EMAIL_INFORMATIVO_NOVO_RELATOR_DENUNCIA) {
            $destinatarios[] = $denuncia->getUltimaDenunciaAdmitida()->getMembroComissao()->
            getProfissionalEntity()->getPessoa()->getEmail();
        }

        $filial = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::ID_CAU_BR;
        if ($tipo == Constants::EMAIL_INFORMATIVO_NOVO_RELATOR_COORDENADORES_CE_CEN) {
            //Coordenador CE
            $destinatarios[] = $this->getMembroComissaoBO()->getEmailsCoordenadoresPorIdAtividaSecundaria(
                $denuncia->getAtividadeSecundaria()->getId(), $filial
            );

            //Coordenador CEN
            if ($this->isEnviarEmailInserirRelatorCoordenadorCEN($denuncia)) {
                $destinatarios[] = $this->getMembroComissaoBO()->getEmailsCoordenadoresPorIdAtividaSecundaria(
                    $denuncia->getAtividadeSecundaria()->getId(), Constants::COMISSAO_MEMBRO_CAU_BR_ID
                );
            }
        }

        if ($tipo == Constants::EMAIL_INFORMATIVO_NOVO_RELATOR_ASSESSORES_CEN_CE) {
            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE($filial);
        }

        return $destinatarios;
    }

    /**
     * Verifica se é necessário envir e-mail ao coordenador CEN ao inserir
     * relator
     *
     * @param Denuncia $denuncia
     * @return bool
     * @throws \Exception
     */
    public function isEnviarEmailInserirRelatorCoordenadorCEN(Denuncia $denuncia)
    {
        $isEnviar = false;
        if ($denuncia->getTipoDenuncia()->getId() === Constants::TIPO_CHAPA){
            $isEnviar = $denuncia->getDenunciaChapa()->getChapaEleicao()->getTipoCandidatura()
                    ->getId() === Constants::TIPO_CANDIDATURA_IES;
        }

        if ($denuncia->getTipoDenuncia()->getId() === Constants::TIPO_MEMBRO_CHAPA){
            $isEnviar = $denuncia->getDenunciaMembroChapa()->getMembroChapa()->getChapaEleicao()
                    ->getTipoCandidatura()->getId() === Constants::TIPO_CANDIDATURA_IES;
        }

        if ($denuncia->getTipoDenuncia()->getId() === Constants::TIPO_MEMBRO_COMISSAO){
            $isEnviar = $denuncia->getDenunciaMembroComissao()->getMembroComissao()->getFilial()
                    ->getId() === Constants::COMISSAO_MEMBRO_CAU_BR_ID;
        }

        return $isEnviar;
    }

    /**
     * Salva os dados de uma denuncia inadmitida
     *
     * @param DenunciaInadmitida $denunciaInadmitida
     *
     * @return \App\To\DenunciaInadmitidaTO |null
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function inadmitir(DenunciaInadmitida $denunciaInadmitida)
    {
        $denunciaInadmitidaSalva = null;
        $this->validarCamposObrigatoriosInadmitir($denunciaInadmitida);
        $this->validarQuantidadeArquivosInadmitida($denunciaInadmitida);
        $denunciaInadmitida = $this->setNomeArquivoFisicoInadmitido($denunciaInadmitida);
        $arquivos = (!empty($denunciaInadmitida->getArquivoDenunciaInadmitida())) ? clone $denunciaInadmitida->getArquivoDenunciaInadmitida() : null;
        $denunciaInadmitida->setArquivoDenunciaInadmitida(null);
        $denuncia = null;

        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();
        $coordenador = $this->membroComissaoRepository->getPorCalendarioAndProfissional(
            $eleicaoVigente->getCalendario()->getId(),
            $usuario->idProfissional
        );

        try {
            $this->beginTransaction();

            $denuncia = $this->denunciaRepository->find($denunciaInadmitida->getIdDenuncia());

            $this->validarAdmissaoDenuncia($denuncia);

            $denuncia->setStatus(Constants::STATUS_DENUNCIA_INADMITIDA);
            $denuncia = $this->denunciaRepository->persist($denuncia);

            $this->salvarSituacaoDenuncia($denuncia, Constants::STATUS_DENUNCIA_EM_JULGAMENTO);

            $denunciaInadmitida->setDenuncia($denuncia);
            $denunciaInadmitida->setDataInadmissao(Utils::getData());
            $denunciaInadmitida->setCoordenador($coordenador);

            $denunciaInadmitidaSalva = $this->denunciaInadmitidaRepository->persist($denunciaInadmitida);

            if (!empty($arquivos)) {
                $this->salvarArquivosDenunciaInadmitida($arquivos, $denunciaInadmitidaSalva, $denuncia);
            }

            $historicoDenuncia = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
                'Inadmitir');

            $this->getHistoricoDenunciaBO()->salvar($historicoDenuncia);

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if (!empty($denuncia)) {
            Utils::executarJOB(new EnviarEmailDenunciaAdmitirInadmitirJob($denuncia->getId(), $denuncia->getTipoDenuncia(), Constants::STATUS_DENUNCIA_INADMITIDA));
        }
        return DenunciaInadmitidaTO::newInstanceFromEntity($denunciaInadmitidaSalva);
    }

    /**
     * @param $idDenuncia
     * @param $idTipoDenuncia
     * @return bool
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailAdmitirEInadmitir($idDenuncia, $idTipoDenuncia, $tipoDenuncia = Constants::STATUS_DENUNCIA_ADMITIDA)
    {

        if ($idTipoDenuncia == null) {
            throw new NegocioException(Message::VALIDACAO_FILTRO_OBRIGATORIO);
        }

        $filtroTO = new \stdClass();
        $filtroTO->idDenuncia = $idDenuncia;
        $denuncias = $this->denunciaRepository->getDenunciasPorFiltro($filtroTO);

        $denunciaSalva = null;

        if (is_array($denuncias) and !empty($denuncias)) {
            $denunciaSalva = $denuncias[0];
        } else {
            throw new NegocioException(Message::NENHUM_CALENDARIO_ENCONTRADO, null, true);
        }

        if (!empty($denunciaSalva)) {
            $atvSecundaria = $this->getAtividadeSecundariaBO()->getAtividadeSecundariaPorNiveis(
                Constants::NIVEL_ATIVIDADE_PRINCIPAL_DENUNCIA,
                Constants::NIVEL_ATIVIDADE_SECUNDARIA_ANALISE_ADMISSIBILIDADE
            );

            $nomeTemplate = '';
            /** @var  Denuncia $denunciaSalva */
            $idCauUf = !empty($denunciaSalva->getFilial()) ? $denunciaSalva->getFilial()->getId() : Constants::ID_CAU_BR;
            $parametrosEmail = $this->prepararParametrosEmail($denunciaSalva, $atvSecundaria);

            if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_CHAPA) {
                $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_CHAPA;
            } else {
                if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_CHAPA) {
                    $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_MEMBRO_CHAPA;
                }
                if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_COMISSAO) {
                    $idCauUf = $denunciaSalva->getDenunciaMembroComissao()->getMembroComissao()->getIdCauUf();
                    $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_MEMBRO_COMISSAO;
                }
                if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_OUTROS) {
                    $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_OUTROS;
                }
            }

            if ($tipoDenuncia == Constants::STATUS_DENUNCIA_ADMITIDA) {

                if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_CHAPA) {
                    $this->enviarEmailResponsavelChapaDenuncia(
                        $atvSecundaria->getId(),
                        $parametrosEmail,
                        $denunciaSalva,
                        $nomeTemplate,
                        $denunciaSalva->getDenunciaChapa()->getChapaEleicao()->getId()
                    );
                } else {
                    if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_CHAPA) {
                        $this->enviarEmailResponsavelMembroChapa(
                            $atvSecundaria->getId(),
                            $parametrosEmail,
                            $denunciaSalva,
                            $nomeTemplate,
                            $denunciaSalva->getDenunciaMembroChapa()->getMembroChapa()->getId()
                        );
                    }
                    if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_COMISSAO) {
                        $this->enviarEmailResponsavelMembroComissao(
                            $atvSecundaria->getId(),
                            $parametrosEmail,
                            $denunciaSalva,
                            $nomeTemplate,
                            $denunciaSalva->getDenunciaMembroComissao()->getMembroComissao()->getId()
                        );
                    }
                }

                $this->enviarEmailsAdmitir($atvSecundaria, $parametrosEmail, $denunciaSalva, $nomeTemplate, $idCauUf);
            } else if ($tipoDenuncia == Constants::STATUS_DENUNCIA_INADMITIDA) {
                $this->enviarEmailsInadmitir($atvSecundaria, $parametrosEmail, $denunciaSalva, $nomeTemplate, $idCauUf);
            }
        }
        return true;
    }

    /**
     * Retorna o usuário conforme o padrão lazy Inicialization.
     *
     * @return UsuarioFactory | null
     */
    public function getUsuarioFactory()
    {
        if ($this->usuarioFactory == null) {
            $this->usuarioFactory = app()->make(UsuarioFactory::class);
        }

        return $this->usuarioFactory;
    }

    /**
     * Salva os dados de uma denuncia inadmitida
     *
     * @param DenunciaDefesa $denunciaDefesa
     * @return void |null
     * @throws NegocioException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function defenderDenuncia(DenunciaDefesa $denunciaDefesa)
    {
        return $this->getDenunciaDefesaBO()->salvar($denunciaDefesa);
    }

    /**
     * Retorna a 'Denúncia' de acordo com o ID informado.
     *
     * @param int $idDenuncia
     * @return Denuncia|null
     */
    public function getDenunciaPorId(int $idDenuncia)
    {
        $filtroTO = new \stdClass();
        $filtroTO->idDenuncia = $idDenuncia;
        $denuncias = $this->denunciaRepository->getDenunciasPorFiltro($filtroTO);

        return !empty($denuncias) ? $denuncias[0] : null;
    }

    /**
     * Recupera as Denuncias Admitidas pela data de Admissão
     *
     * @param $dataLimiteRemovida
     * @return Denuncia|null
     * @throws \Exception
     */
    public function getDenunciaAdmitidaPorDataAdmissao($dataLimiteRemovida)
    {
        return $this->denunciaRepository->getDenunciaAdmitidaPorDataAdmissao($dataLimiteRemovida);
    }

    /**
     * Valida se o usuário logado é o relator da denuncia
     *
     * @param Denuncia $denuncia
     * @return bool
     */
    public function validarRelatorAtual($denuncia)
    {
        $denunciaAdmitida = $denuncia->getUltimaDenunciaAdmitida();

        if (null !== $denunciaAdmitida) {
            $profissionalMembroComissao = $denunciaAdmitida
                ->getMembroComissao()->getProfissionalEntity();

            $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
            $isRelatorAtual = $usuarioLogado->idProfissional === $profissionalMembroComissao->getId();
        }

        return $isRelatorAtual ?? false;
    }

    /**
     * Retorna a estrutura de recursos com contrarrazão, quando existente.
     *
     * @param $recursosDenuncia
     * @return array
     */
    public function getEstruturaRecursosContrarrazao($recursosDenuncia)
    {
        $recursoDenunciado = $this->getRecursoContrarrazaoPorTipo($recursosDenuncia, Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO);
        $recursoDenunciante = $this->getRecursoContrarrazaoPorTipo($recursosDenuncia, Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE);

        $recursosDenuncia = [];

        if (!empty($recursoDenunciante)) {
            $recursosDenuncia[] = current($recursoDenunciante);
        }

        if (!empty($recursoDenunciado)) {
            $recursosDenuncia[] = current($recursoDenunciado);
        }

        return $recursosDenuncia;
    }


    /**
     * Retorna a estrutura de recursos com contrarrazão, quando existente, de acordo com o tipo do recurso.
     *
     * @param $recursosDenuncia
     * @param $tipoRecurso
     *
     * @return array|RecursoDenuncia[]|RecursoDenunciaTO[]
     */
    public function getRecursoContrarrazaoPorTipo($recursosDenuncia, int $tipoRecurso)
    {
        $recursosDenuncia = $recursosDenuncia ?? [];
        if (!is_array($recursosDenuncia)) {
            $recursosDenuncia = $recursosDenuncia->toArray();
        }

        return array_filter($recursosDenuncia, static function($recursoDenuncia) use($tipoRecurso) {
            if ($recursoDenuncia instanceof RecursoDenuncia) {
                return $recursoDenuncia->getTipoRecursoContrarrazaoDenuncia() === $tipoRecurso;
            }

            if ($recursoDenuncia instanceof RecursoDenunciaTO) {
                return $recursoDenuncia->getTipoRecurso() === $tipoRecurso;
            }
        });
    }

    /**
     * Valida se a denuncia possui defesa ou o prazo de defesa tenha encerrado.
     *
     * @param Denuncia $denuncia
     * @return bool
     * @throws \Exception
     */
    public function validarDefesaPrazoEncerradoPorDenuncia($denuncia): bool
    {
        $dataLimite = null;
        $denunciaAdmitida = $denuncia->getPrimeiraDenunciaAdmitida();

        if (null !== $denunciaAdmitida) {
            $ano = Utils::getAnoData($denunciaAdmitida->getDataAdmissao());
            $feriados = $this->getCalendarioApiService()
                ->getFeriadosNacionais($ano);

            $dataLimite = Utils::adicionarDiasUteisData(
                $denunciaAdmitida->getDataAdmissao(),
                Constants::PRAZO_DEFESA_DENUNCIA_DIAS,
                $feriados
            );
        }

        return null === $denuncia->getDenunciaDefesa()
            && (null !== $dataLimite && Utils::getDataHoraZero() > Utils::getDataHoraZero($dataLimite));
    }

    /**
     * Valida os encaminhamentos para denúncia
     *
     * @param Denuncia $denuncia
     *
     * @return \stdClass
     * @throws \Exception
     */
    private function getCondicionaisView($denuncia): \stdClass
    {
        $encaminhamentos = $denuncia->getEncaminhamentoDenuncia() ?? [];
        if (!is_array($encaminhamentos)) {
            $encaminhamentos = $encaminhamentos->toArray();
        }

        $alegacoesFinaisConcluidas = $this->getAlegacaoFinalBO()->isAcaoRelatorParecerFinalDisponivel($denuncia);
        $audienciasInstrucaoPendentes = $this->getAudienciasInstrucaoPendente($encaminhamentos);
        $impedimentosSuspeicaoPendentes = $this->getImpedimentosSuspeicaoPendente($encaminhamentos);
        $alegacaoFinalPendentePrazoEncerrado = !$this->getEncaminhamentoDenunciaBO()
            ->isAlegacaoFinalPendenteDentroPrazo($encaminhamentos);

        $parecerFinal = $this->getParecerFinalBO()->isParecerFinalInseridoParaDenuncia($denuncia);

        if ($denuncia->getPrimeiroJulgamentoDenuncia() !== null) {
            $recursoContrarrazao = $this->getRecursoContrarrazaoBO()->isPrazoRecursoDenuncia(
                $denuncia->getPrimeiroJulgamentoDenuncia());
        }

        $prazoContrarrazao = $this->getContrarrazaoRecursoDenunciaBO()->getPrazoContrarrazaoRecursos($denuncia);
        $encaminhamentoAlegacao = $this->getEncaminhamentoDenunciaBO()->isExisteEncaminhamentoAlegacaoFinal($denuncia);

        $encaminhamentosExistentes = new \stdClass();
        $encaminhamentosExistentes->hasParecerFinalInseridoParaDenuncia = $parecerFinal;
        $encaminhamentosExistentes->hasPrazoRecursoDenuncia = $recursoContrarrazao ?? false;
        $encaminhamentosExistentes->hasAlegacoesFinaisConcluidas = $alegacoesFinaisConcluidas;
        $encaminhamentosExistentes->hasAudienciasInstrucaoPendentes = !empty($audienciasInstrucaoPendentes);
        $encaminhamentosExistentes->hasImpedimentosSuspeicaoPendentes = !empty($impedimentosSuspeicaoPendentes);
        $encaminhamentosExistentes->hasAlegacaoFinalPendentePrazoEncerrado = $alegacaoFinalPendentePrazoEncerrado;
        $encaminhamentosExistentes->hasContrarrazaoDenuncianteDentroPrazo = $prazoContrarrazao->hasContrarrazaoRecursoDenuncianteDentroPrazo;
        $encaminhamentosExistentes->hasContrarrazaoDenunciadoDentroPrazo = $prazoContrarrazao->hasContrarrazaoRecursoDenunciadoDentroPrazo;
        $encaminhamentosExistentes->hasExisteEncaminhamentoAlegacaoFinal = $encaminhamentoAlegacao;

        return $encaminhamentosExistentes;
    }

    /**
     * Retorna os encaminhamentos da denuncia com status de impedimento ou
     * suspeicao pendente.
     *
     * @param EncaminhamentoDenuncia[]|ArrayCollection $encaminhamentos
     * @return array
     */
    private function getImpedimentosSuspeicaoPendente($encaminhamentos)
    {
        return array_filter($encaminhamentos, static function (EncaminhamentoDenuncia $encaminhamento) {
            $isSituacaoPendente = $encaminhamento->getTipoSituacaoEncaminhamento()
                    ->getId() === Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE;
            $isImpedimentoSuspeicao = $encaminhamento->getTipoEncaminhamento()
                    ->getId() === Constants::TIPO_ENCAMINHAMENTO_IMPEDIMENTO_SUSPEICAO;

            return $isImpedimentoSuspeicao && $isSituacaoPendente;
        });
    }

    /**
     * Retorna os encaminhamentos da denuncia com status de audiencia de
     * instrução pendentes.
     *
     * @param EncaminhamentoDenuncia[]|ArrayCollection $encaminhamentos
     * @return array
     */
    private function getAudienciasInstrucaoPendente($encaminhamentos)
    {
        return array_filter($encaminhamentos, static function (EncaminhamentoDenuncia $encaminhamento) {
            $isSituacaoPendente = $encaminhamento->getTipoSituacaoEncaminhamento()
                    ->getId() === Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE;
            $isAudienciaInstrucao = $encaminhamento->getTipoEncaminhamento()
                    ->getId() === Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO;

            return $isAudienciaInstrucao && $isSituacaoPendente;
        });
    }

    /**
     * Retorna os encaminhamentos da denuncia com status de alegação final
     * concluída.
     *
     * @param Denuncia $denuncia
     * @return bool
     */
    private function getAlegacoesFinaisConcluidas($denuncia)
    {
        $encaminhamentos = $denuncia->getEncaminhamentoDenuncia() ?? [];
        if (!is_array($encaminhamentos)) {
            $encaminhamentos = $encaminhamentos->toArray();
        }

        $alegacoesFinaisConcluidas = array_filter($encaminhamentos, static function (EncaminhamentoDenuncia $encaminhamento) {
            $isSituacaoConcluido = $encaminhamento->getTipoSituacaoEncaminhamento()
                    ->getId() === Constants::TIPO_SITUACAO_ENCAMINHAMENTO_CONCLUIDO;
            $isAlegacoesFinais = $encaminhamento->getTipoEncaminhamento()
                    ->getId() === Constants::TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS;

            return $isAlegacoesFinais && $isSituacaoConcluido;
        });

        $hasAlegacoesFinaisConcluidas = false;
        if (!empty($alegacoesFinaisConcluidas)) {
            $alegacoesFinaisDenunciado = array_filter($alegacoesFinaisConcluidas, static function (EncaminhamentoDenuncia $encaminhamento) {
                return $encaminhamento->isDestinoDenunciado();
            });
            $alegacoesFinaisDenunciante = array_filter($alegacoesFinaisConcluidas, static function (EncaminhamentoDenuncia $encaminhamento) {
                return $encaminhamento->isDestinoDenunciante();
            });

            $hasAlegacoesFinaisConcluidas = ($denuncia->getTipoDenuncia()->getId() === Constants::TIPO_OUTROS && !empty($alegacoesFinaisDenunciante))
                || (!empty($alegacoesFinaisDenunciado) && !empty($alegacoesFinaisDenunciante));
        }

        return $hasAlegacoesFinaisConcluidas;
    }

    /**
     * Envia o e-mail para o responsável da chapa do cadastro da denúncia
     *
     * @param int $idAtivSecundaria
     * @param array $parametrosEmail ,
     * @param Denuncia $denunciaSalva ,
     * @param string $nomeTemplate
     * @throws NonUniqueResultException
     */
    private function enviarEmailResponsavelChapaDenuncia(
        $idAtivSecundaria,
        $parametrosEmail,
        $denunciaSalva,
        $nomeTemplate,
        $chapa
    )
    {
        $responsaveis = $this->getMembroChapaBO()
            ->getMembrosResponsaveisChapa(
                $chapa,
                null,
                Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
            );
        $destinatariosEmail = $this->getMembroChapaBO()->getListEmailsDestinatarios($responsaveis);
        $idTipoEmail = Constants::EMAIL_ADMISSAO_DENUNCIA_RESP_CHAPA_MEMBRO_DENUNCIADOS;
        $this->enviarEmailDenuncia($idAtivSecundaria, $destinatariosEmail, $idTipoEmail, $parametrosEmail, $nomeTemplate);
    }

    /**
     * Envia o e-mail para o responsável da chapa do cadastro da denúncia
     *
     * @param int $idAtivSecundaria
     * @param array $parametrosEmail ,
     * @param Denuncia $denunciaSalva ,
     * @param string $nomeTemplate
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailResponsavelMembroChapa(
        $idAtivSecundaria,
        $parametrosEmail,
        $denunciaSalva,
        $nomeTemplate
    ) {
        $destinatariosEmail = $this->getDenunciaMembroChapaBO()->getDadosDenunciante($denunciaSalva->getId());
        $destinatariosEmail = $destinatariosEmail[0];
        $idTipoEmail = Constants::EMAIL_ADMISSAO_DENUNCIA_RESP_CHAPA_MEMBRO_DENUNCIADOS;
        $this->enviarEmailDenuncia($idAtivSecundaria, $destinatariosEmail, $idTipoEmail, $parametrosEmail, $nomeTemplate);
    }

    /**
     * Envia o e-mail para o responsável da chapa do cadastro da denúncia
     *
     * @param int $idAtivSecundaria
     * @param array $parametrosEmail ,
     * @param Denuncia $denunciaSalva ,
     * @param string $nomeTemplate
     * @throws NonUniqueResultException
     */
    private function enviarEmailResponsavelMembroComissao(
        $idAtivSecundaria,
        $parametrosEmail,
        $denunciaSalva,
        $nomeTemplate
    ) {
        $destinatariosEmail = $this->getDenunciaMembroComissaoBO()->getDadosDenunciante($denunciaSalva->getId());
        $destinatariosEmail = $destinatariosEmail[0];
        $idTipoEmail = Constants::EMAIL_ADMISSAO_DENUNCIA_RESP_CHAPA_MEMBRO_DENUNCIADOS;
        $this->enviarEmailDenuncia($idAtivSecundaria, $destinatariosEmail, $idTipoEmail, $parametrosEmail, $nomeTemplate);
    }

    /**
     * Envia o e-mail para o responsável da chapa do cadastro da denúncia
     *
     * @param int $idAtivSecundaria
     * @param array $parametrosEmail ,
     * @param Denuncia $denunciaSalva ,
     * @param string $nomeTemplate
     * @param $idCauUf
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailsAdmitir(
        $idAtivSecundaria,
        $parametrosEmail,
        $denunciaSalva,
        $nomeTemplate,
        $idCauUf
    ) {
        $this->enviarEmailResponsavelDenuncia(
            $idAtivSecundaria->getId(), $parametrosEmail, $denunciaSalva,
            $nomeTemplate, Constants::EMAIL_ADMISSAO_DENUNCIA_DENUNCIANTE);

        $this->enviarEmailRelatorDenuncia($idAtivSecundaria->getId(), $parametrosEmail, $denunciaSalva, $nomeTemplate);

        $this->enviarEmailResponsavelCoordenadorAdjunto($idAtivSecundaria->getId(), $parametrosEmail, $idCauUf,
            $nomeTemplate, Constants::EMAIL_ADMISSAO_DENUNCIA_COORD_ADJUNTOS_CE_CEN);

        $this->enviarEmailResponsavelAssessor($idAtivSecundaria->getId(), $parametrosEmail, $idCauUf,
            $nomeTemplate, Constants::EMAIL_ADMISSAO_DENUNCIA_ASSESSORES_CE_CEN);
    }

    /**
     * Envia o e-mail para os responsáveis no fluxo de inadmitir
     *
     * @param $ativSecundaria
     * @param $parametrosEmail
     * @param $denunciaSalva
     * @param $nomeTemplate
     * @param $idCauUf
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailsInadmitir(
        $ativSecundaria,
        $parametrosEmail,
        $denunciaSalva,
        $nomeTemplate,
        $idCauUf
    ) {
        $this->enviarEmailResponsavelCoordenadorAdjunto($ativSecundaria->getId(), $parametrosEmail, $idCauUf,
            $nomeTemplate, Constants::EMAIL_INADMISSAO_DENUNCIA_COORD_ADJUNTOS_CE_CEN);

        $this->enviarEmailResponsavelAssessor($ativSecundaria->getId(), $parametrosEmail, $idCauUf,
            $nomeTemplate, Constants::EMAIL_INADMISSAO_DENUNCIA_ASSESSORES_CE_CEN);
    }

    /**
     * Envia o e-mail para o responsável pelo cadastro da denúncia
     *
     * @param int $idAtivSecundaria
     * @param array $parametrosEmail ,
     * @param Denuncia $denunciaSalva ,
     * @param string $nomeTemplate
     * @throws NonUniqueResultException
     */
    private function enviarEmailRelatorDenuncia(
        $idAtivSecundaria,
        $parametrosEmail,
        $denunciaSalva,
        $nomeTemplate
    )
    {
        $profissional = $this->getProfissionalBO()->getPorId($denunciaSalva->getUltimaDenunciaAdmitida()->getMembroComissao()->getPessoa(), false, false);

        $destinatarios[] = $profissional->getPessoa()->getEmail();
        $idTipoEmail = Constants::EMAIL_RELATOR_INDICADOR_DENUNCIA;
        $this->enviarEmailDenuncia($idAtivSecundaria, $destinatarios, $idTipoEmail, $parametrosEmail, $nomeTemplate);
    }


    /**
     * Valida se a Denuncia está com status de Em análise
     *
     * @param Denuncia $denuncia
     * @throws \App\Exceptions\NegocioException
     */
    private function validarAdmissaoDenuncia(Denuncia $denuncia)
    {
        if ($denuncia->getStatus() !== Constants::STATUS_PENDENTE_ANALISE) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }
    }

    /**
     * Valida se os campos obrigatórios estão preenchidos
     *
     * @param DenunciaAdmitida $denunciaAdmitida
     * @throws NegocioException
     */
    private function validarCamposObrigatoriosAdmitir(DenunciaAdmitida $denunciaAdmitida)
    {
        $campos = [];

        if (empty($denunciaAdmitida->getDescricaoDespacho())) {
            $campos[] = 'LABEL_DS_DESPACHO';
        }

        if (empty($denunciaAdmitida->getIdDenuncia())) {
            $campos[] = 'LABEL_ID_DENUNCIA';
        }

        if (empty($denunciaAdmitida->getIdMembroComissao())) {
            $campos[] = 'LABEL_ID_MEMBRO_COMISSAO';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Valida se os campos obrigatórios estão preenchidos
     *
     * @param DenunciaInadmitida $denunciaInadmitida
     * @throws NegocioException
     */
    private function validarCamposObrigatoriosInadmitir(DenunciaInadmitida $denunciaInadmitida)
    {
        $campos = [];

        if (empty($denunciaInadmitida->getDescricao())) {
            $campos[] = 'LABEL_DS_DESPACHO';
        }

        if (empty($denunciaInadmitida->getIdDenuncia())) {
            $campos[] = 'LABEL_ID_DENUNCIA';
        }

        // Arquivo ???

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Valida se os campos obrigatórios estão preenchidos
     *
     * @param DenunciaAdmitida $denunciaAdmitida
     * @throws NegocioException
     */
    private function validarCamposObrigatoriosAdmitirRelator(DenunciaAdmitida $denunciaAdmitida)
    {
        $campos = [];

        if (empty($denunciaAdmitida->getDescricaoDespacho())) {
            $campos[] = 'LABEL_DS_DESPACHO';
        }
        if (empty($denunciaAdmitida->getIdDenuncia())) {
            $campos[] = 'LABEL_ID_DENUNCIA';
        }

        if (empty($denunciaAdmitida->getIdMembroComissao())) {
            $campos[] = 'LABEL_ID_MEMBRO_COMISSAO';
        }

        // Arquivo ???

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Inclui as Bandeiras nas UFs do Agrupamento.
     *
     * @param $agrupamentoUF
     * @param $filiaisComBandeira
     * @return \stdClass
     */
    private function incluiBandeirasAgrupamentoUF(
        $agrupamentoUF,
        $filiaisComBandeira,
        $isCoordenadorCEN = null,
        $isMembroComissaoCE = null
    ) {
        $totalPedidos = 0;

        foreach ($agrupamentoUF as $index => $agrupamento) {
            if (!empty($filiaisComBandeira)) {
                $bandeira = array_filter($filiaisComBandeira, function ($filialBandeira) use ($agrupamento) {
                    $idCau = $agrupamento['id_cau_uf'];

                    if ($agrupamento['id_cau_uf'] == Constants::IES_ID) {
                        $idCau = Constants::COMISSAO_MEMBRO_CAU_BR_ID;
                    }

                    return $filialBandeira->id == $idCau;
                });

                $bandeira = reset($bandeira);
                $agrupamentoUF[$index]['imagemBandeira'] = $bandeira->imagemBandeira ?? null;
            } else {
                $agrupamentoUF[$index]['imagemBandeira'] = null;
            }

            if ($agrupamentoUF[$index]['id_cau_uf'] == Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                $agrupamentoUF[$index]['descricao'] = $agrupamentoUF[$index]['prefixo'] =
                    Constants::PREFIXO_CONSELHO_ELEITORAL_NACIONAL;
            }

            $totalPedidos += $agrupamento['qtd_pedido'];
        }

        $denIES = array_filter($agrupamentoUF, function ($denuncia) {
            return $denuncia['id_cau_uf'] == Constants::IES_ID;
        });

        $denCauBr = array_filter($agrupamentoUF, function ($denuncia) {
            return $denuncia['id_cau_uf'] == Constants::COMISSAO_MEMBRO_CAU_BR_ID;
        });

        $denUf = array_filter($agrupamentoUF, function ($denuncia) {
            return $denuncia['id_cau_uf'] != Constants::COMISSAO_MEMBRO_CAU_BR_ID
                && $denuncia['id_cau_uf'] != Constants::IES_ID;
        });

        $agrupamentoUF = array_merge($denUf, $denIES, $denCauBr);

        $agrupamentoDenuncias = new \stdClass();
        $agrupamentoDenuncias->totalPedidos = $totalPedidos;
        $agrupamentoDenuncias->agrupamentoUF = $agrupamentoUF;
        $agrupamentoDenuncias->isCoordenadorCEN = $isCoordenadorCEN;
        $agrupamentoDenuncias->isCoordenadorCE = $isMembroComissaoCE;
        $agrupamentoDenuncias->isMembroComissaoComum = !$isMembroComissaoCE && !$isCoordenadorCEN;

        return $agrupamentoDenuncias;
    }

    /**
     * @param $denuncia
     *
     * @return Profissional|null
     */
    private function getProfissionalDenunciadoPorTipoDenuncia(Denuncia $denuncia): ?Profissional
    {
        $tipoDenuncia = $denuncia->getTipoDenuncia()->getId();

        if (Constants::TIPO_MEMBRO_CHAPA === $tipoDenuncia) {

            $denunciado = !empty($denuncia->getDenunciaMembroChapa())
                ? $denuncia->getDenunciaMembroChapa()->getMembroChapa()->getProfissional()
                : null;
        }

        if (Constants::TIPO_MEMBRO_COMISSAO === $tipoDenuncia) {
            
            $denunciado = !empty($denuncia->getDenunciaMembroComissao())
                ? $denuncia->getDenunciaMembroComissao()->getMembroComissao()->getProfissionalEntity()
                : null;
        }

        return $denunciado ?? null;
    }

    /**
     * Retorna a situação atual da denúncia (última situação)
     *
     * @param $idDenuncia
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMaxSituacaoDenuncia($idDenuncia)
    {
        return $this->denunciaRepository->getMaxSituacaoDenuncia($idDenuncia);
    }

    /**
     * Retorna a situação atual da denúncia (última situação)
     *
     * @param $idSituacaoDenuncia
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSituacaoAtualDenuncia($idSituacaoDenuncia)
    {
        return $this->denunciaRepository->getSituacaoAtualDenuncia($idSituacaoDenuncia);
    }

    /**
     * Retorna o nome da situação atual da denúncia
     *
     * @param $idDenuncia
     * @return string
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getNomeSituacaoAtualDenuncia($idDenuncia)
    {
        $situacaoDenuncia = $this->denunciaRepository->getMaxSituacaoDenuncia($idDenuncia);
        $situacao = $this->denunciaRepository->getSituacaoAtualDenuncia($situacaoDenuncia['id']);

        return $situacao ? $situacao['descricao'] : null;
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
     * Retorna uma nova instância de 'ProfissionalBO'.
     *
     * @return ProfissionalBO
     */
    private function getProfissionalBO()
    {
        if (empty($this->pessoaBO)) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }

        return $this->profissionalBO;
    }

    /**
     * Recupera a entidade 'ArquivoDenuncia' por meio do 'id' informado.
     *
     * @param $id
     *
     * @return \App\Entities\ArquivoDenuncia|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getArquivoDenuncia($id)
    {
        $arrayArquivo = $this->arquivoDenunciaRepository->getPorId($id);

        return $arrayArquivo[0];
    }

    /**
     * Recupera um array da entidade 'ArquivoDenunciaInadmitida' por meio do
     * 'id' informado.
     *
     * @param $id
     *
     * @return \App\Entities\ArquivoDenunciaInadmitida|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getArquivoDenunciaInadmitida($id)
    {
        return $this->arquivoDenunciaInadmitidaRepository->getPorId($id);
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
     * Verifica se a atividade secundaria ainda pertence a um calendário vigente
     * caso não, retorna a mensagem de excessão
     *
     * @param int $idAtividadeSecundaria
     *
     * @throws \App\Exceptions\NegocioException
     */
    private function verificaVigenciaCalendario($idAtividadeSecundaria)
    {
        $calendario = $this->calendarioRepository->getPorAtividadeSecundaria($idAtividadeSecundaria);

        if (!($calendario->getDataInicioVigencia() <= Utils::getData() and $calendario->getDataFimVigencia() >= Utils::getData())
            or (!$calendario->isAtivo()) or ($calendario->isExcluido())) {
            throw new NegocioException(Message::MSG_DENUNCIA_PRAZO_EXPIRADO);
        }
    }

    /**
     * Cria um número sequencial para a denuncia
     *
     * @param Denuncia $denuncia
     * @return Denuncia
     */
    private function getSequencia(Denuncia $denuncia)
    {
        $filtroTO = new \stdClass();
        $filtroTO->idAtividadeSecundaria = $denuncia->getAtividadeSecundaria()->getId();
        $denunciasBD = $this->denunciaRepository->getDenunciasPorFiltro($filtroTO);

        $denuncia->setNumeroSequencial(1);
        if (!empty($denunciasBD)) {
            $denunciaBD = $denunciasBD[0];
            $denuncia->setNumeroSequencial($denunciaBD->getNumeroSequencial() + 1);
        }
        return $denuncia;
    }

    /**
     * Cria uma instancia de Situacao Denuncia e Salva para a Denuncia
     *
     * @param Denuncia @denunciaSalva
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function salvarSituacaoDenuncia($denunciaSalva, $idSituacao = Constants::STATUS_EM_ANALISE_ADMISSIBILIDADE)
    {
        $situacaoSalva = $this->situacaoDenunciaRepository->find($idSituacao);

        $denunciaSituacao = new DenunciaSituacao();
        $denunciaSituacao->setDenuncia($denunciaSalva);
        $denunciaSituacao->setSituacaoDenuncia($situacaoSalva);
        $denunciaSituacao->setData(Utils::getData());

        $this->denunciaSituacaoRepository->persist($denunciaSituacao);
    }

    /**
     * Método auxiliar para Salvar dados dos Arquivos para a denuncia
     *
     * @param          $arquivosDenuncia
     * @param Denuncia $denunciaSalva
     * @param          $isInclusao
     *
     * @return Denuncia
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function salvarArquivosDenuncia($arquivosDenuncia, Denuncia $denunciaSalva, $isInclusao)
    {
        $arquivosSalvos = new ArrayCollection();

        if (!empty($arquivosDenuncia)) {
            foreach ($arquivosDenuncia as $arquivoDenuncia) {
                if (!empty($arquivoDenuncia->getId()) && $isInclusao) { //Caso de replicação
                    $arquivoRecuperado = $this->arquivoDenunciaRepository->find($arquivoDenuncia->getId())[0];

                    $arquivoDenuncia->setId(null);
                    $arquivoDenuncia->setDenuncia($denunciaSalva);
                    $arquivoSalvo = $this->arquivoDenunciaRepository->persist($arquivoDenuncia);
                    $arquivosSalvos->add($arquivoSalvo);
                    $this->getArquivoService()->copiarArquivoDenuncia($arquivoRecuperado, $arquivoDenuncia);
                    $arquivoSalvo->setDenuncia(null);
                    $arquivoSalvo->setArquivo(null);
                } else {
                    $arquivoDenuncia->setDenuncia($denunciaSalva);
                    $arquivoSalvo = $this->arquivoDenunciaRepository->persist($arquivoDenuncia);
                    $arquivoSalvo->setDenuncia(null);
                    $arquivosSalvos->add($arquivoSalvo);
                    $this->salvaArquivosDiretorio($arquivoDenuncia, $denunciaSalva);
                    $arquivoSalvo->setArquivo(null);
                }
            }
        }

        $denunciaSalva->setArquivoDenuncia($arquivosSalvos);
        $denunciaSalva->removerFiles();

        return $denunciaSalva;
    }

    /**
     * Método auxiliar para Salvar dados dos Arquivos para a denuncia inadmitida
     *
     * @param $arquivosDenuncia
     * @param DenunciaInadmitida $denunciaInadmitida
     * @param Denuncia $denuncia
     * @return DenunciaInadmitida
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function salvarArquivosDenunciaInadmitida($arquivosDenuncia, DenunciaInadmitida $denunciaInadmitida, Denuncia $denuncia)
    {
        $arquivosSalvos = new ArrayCollection();

        if (!empty($arquivosDenuncia)) {
            foreach ($arquivosDenuncia as $arquivoDenuncia) {
                $arquivoDenuncia->setDenunciaInadmitida($denunciaInadmitida);
                $arquivoSalvo = $this->arquivoDenunciaInadmitidaRepository->persist($arquivoDenuncia);
                $arquivosSalvos->add($arquivoSalvo);
                $this->salvaArquivosDiretorio($arquivoDenuncia, $denuncia);
                $arquivoSalvo->setArquivo(null);
            }
        }
        $denunciaInadmitida->setArquivoDenunciaInadmitida($arquivosSalvos);
        $denunciaInadmitida->removerFiles();

        return $denunciaInadmitida;
    }

    /**
     * Método auxiliar para Salvar dados dos Arquivos para a denuncia admitida
     *
     * @param $arquivosDenuncia
     * @param DenunciaAdmitida $denunciaAdmitida
     * @param Denuncia $denuncia
     * @return DenunciaAdmitida
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function salvarArquivosDenunciaAdmitida($arquivosDenuncia, DenunciaAdmitida $denunciaAdmitida, Denuncia $denuncia)
    {
        $arquivosSalvos = new ArrayCollection();
        if (!empty($arquivosDenuncia)) {
            foreach ($arquivosDenuncia as $arquivoDenuncia) {
                $arquivoDenuncia->setDenunciaAdmitida($denunciaAdmitida);
                $arquivoSalvo = $this->arquivoDenunciaAdmitidaRepository->persist($arquivoDenuncia);
                $arquivosSalvos->add($arquivoSalvo);
                $this->salvaArquivosDiretorio($arquivoDenuncia, $denuncia);
                $arquivoSalvo->setArquivo(null);
            }
        }
        $denunciaAdmitida->setArquivoDenunciaAdmitida($arquivosSalvos);

        return $denunciaAdmitida;
    }

    /**
     * Realiza o(s) upload(s) do(s) arquivo(s) da Denuncia
     *
     * @param Denuncia $denunciaSalva
     */
    private function salvaArquivosDiretorio($arquivo, Denuncia $denunciaSalva)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenuncia($denunciaSalva->getId());

        if ($arquivo !== null) {
            if (!empty($arquivo->getArquivo())) {
                $this->getArquivoService()->salvar($caminho, $arquivo->getNomeFisico(), $arquivo->getArquivo());
            }
        }
    }

    /**
     * Cria os nomes de arquivo
     *
     * @param Denuncia $denuncia
     * @return Denuncia
     */
    private function setNomeArquivoFisico(Denuncia $denuncia)
    {
        if ($denuncia->getArquivoDenuncia() !== null) {
            foreach ($denuncia->getArquivoDenuncia() as $arquivo) {
                if (empty($arquivo->getId())) {
                    $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                        $arquivo->getNome(),
                        Constants::REFIXO_DOC_DENUNCIA
                    );
                    $arquivo->setNomeFisico($nomeArquivoFisico);
                }
            }
        }
        return $denuncia;
    }

    /**
     * Cria os nomes de arquivo para Denuncia Inadmitida
     *
     * @param DenunciaInadmitida $denunciaInadmitida
     * @return DenunciaInadmitida
     */
    private function setNomeArquivoFisicoInadmitido(DenunciaInadmitida $denunciaInadmitida)
    {
        if ($denunciaInadmitida->getArquivoDenunciaInadmitida() !== null) {
            foreach ($denunciaInadmitida->getArquivoDenunciaInadmitida() as $arquivo) {
                if (empty($arquivo->getId())) {
                    $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                        $arquivo->getNome(),
                        Constants::REFIXO_DOC_DENUNCIA_INADMITIDA
                    );
                    $arquivo->setNomeFisico($nomeArquivoFisico);
                }
            }
        }
        return $denunciaInadmitida;
    }

    /**
     * Cria os nomes de arquivo para Denuncia Admitida
     *
     * @param DenunciaAdmitida $denunciaAdmitida
     * @return DenunciaAdmitida
     */
    private function setNomeArquivoFisicoAdmitido(DenunciaAdmitida $denunciaAdmitida)
    {
        if ($denunciaAdmitida->getArquivoDenunciaAdmitida() !== null) {
            foreach ($denunciaAdmitida->getArquivoDenunciaAdmitida() as $arquivo) {
                if (empty($arquivo->getId())) {
                    $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                        $arquivo->getNome(),
                        Constants::PREFIXO_DOC_DENUNCIA_ADMITIDA
                    );
                    $arquivo->setNomeFisico($nomeArquivoFisico);
                }
            }
        }
        return $denunciaAdmitida;
    }

    /**
     * Retorna os dados de profissional do responsável pelo cadastro da denuncia
     *
     * @param $idPessoa
     * @return array
     * @throws NegocioException
     */
    private function getDadosResponsavelCadastro($idPessoa)
    {
        return $this->getCorporativoService()->getProfissionaisPorIds([$idPessoa]);
    }

    /**
     * Retorna os dados de Coordenadores e Adjuntos de Comissão
     *
     * @param int $idCauUf
     * @return array|null
     * @throws NegocioException
     */
    private function getCoordenadorEAdjuntoComissao($idCauUf)
    {
        $filtroTO = new \stdClass();
        $filtroTO->idCauUf = $idCauUf;
        $filtroTO->tipoParticipacao = Constants::TIPO_PARTICIPACAO_COORDENADOR;

        $coordenadores = $this->getMembroComissaoBO()->getPorFiltro($filtroTO);

        $filtroTO->tipoParticipacao = Constants::TIPO_PARTICIPACAO_COORDENADOR_ADJUNTO;
        $adjuntos = $this->getMembroComissaoBO()->getPorFiltro($filtroTO);

        return array_merge($coordenadores['membros'], $adjuntos['membros']);
    }

    /**
     * Valida a quantidade de arquivos para a Denuncia
     *
     * @param Denuncia $denuncia
     * @throws NegocioException
     */
    private function validarQuantidadeArquivos(Denuncia $denuncia)
    {
        $arquivos = $denuncia->getArquivoDenuncia();
        if (!empty($arquivos) and count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO_DENUNCIA) {
            throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO);
        }
    }

    /**
     * Valida a quantidade de arquivos para a Denuncia Inadmitida
     *
     * @param DenunciaInadmitida $denunciaInadmitida
     * @throws NegocioException
     */
    private function validarQuantidadeArquivosInadmitida(DenunciaInadmitida $denunciaInadmitida)
    {
        $arquivos = $denunciaInadmitida->getArquivoDenunciaInadmitida();
        if (!empty($arquivos) and count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO_DENUNCIA) {
            throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO);
        }
    }

    /**
     * Valida a quantidade de arquivos para a Denuncia Inadmitida
     *
     * @param DenunciaAdmitida $denunciaAdmitida
     * @throws NegocioException
     */
    private function validarQuantidadeArquivosAdmitida(DenunciaAdmitida $denunciaAdmitida)
    {
        $arquivos = $denunciaAdmitida->getArquivoDenunciaAdmitida();
        if (!empty($arquivos) and count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO_DENUNCIA) {
            throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO);
        }
    }

    /**
     * Retorna a instancia da denuncia sem os 'filhos'
     *
     * @param Denuncia $denuncia
     * @return Denuncia
     */
    private function limparFilhosDenuncia(Denuncia $denuncia)
    {
        $denuncia->setTestemunhas(null);
        $denuncia->setDenunciaChapa(null);
        $denuncia->setDenunciaOutros(null);
        $denuncia->setArquivoDenuncia(null);
        $denuncia->setDenunciaMembroChapa(null);
        $denuncia->setDenunciaMembroComissao(null);
        $denuncia->setArquivoDenuncia(new ArrayCollection());

        return $denuncia;
    }

    /**
     * Responsável por enviar emails após cadastrar pedido substituição chapa
     *
     * @param Denuncia                      $denunciaSalva
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param                               $nomeTemplate
     * @param                               $idCauUf
     *
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function enviarEmailsDenuncia(
        Denuncia $denunciaSalva,
        AtividadeSecundariaCalendario $atividadeSecundaria,
        $nomeTemplate,
        $idCauUf
    ) {
        $parametrosEmail = $this->prepararParametrosEmail($denunciaSalva, $atividadeSecundaria);

        $this->enviarEmailResponsavelDenuncia(
            $atividadeSecundaria->getId(), $parametrosEmail, $denunciaSalva,
            $nomeTemplate, Constants::EMAIL_RESPONSAVEL_CADASTRO_DENUNCIA
        );

        $this->enviarEmailResponsavelCoordenadorAdjunto($atividadeSecundaria->getId(), $parametrosEmail,
            $idCauUf, $nomeTemplate, Constants::EMAIL_COMISSAO_ELEITORAL_CADASTRO_DENUNCIA);

        $this->enviarEmailResponsavelAssessor($atividadeSecundaria->getId(), $parametrosEmail, $idCauUf,
            $nomeTemplate, Constants::EMAIL_ASSESSOR_ELEITORAL_CADASTRO_DENUNCIA);
    }

    /**
     * Prepara os parametros para o Envio de email
     *
     * @param Denuncia $denunciaSalva
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @return array
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function prepararParametrosEmail(Denuncia $denunciaSalva, AtividadeSecundariaCalendario $atividadeSecundaria)
    {
        $denuncia = $this->findById($denunciaSalva->getId());
        $numeroSequencial = $denunciaSalva->getNumeroSequencial() ? $denunciaSalva->getNumeroSequencial() : null;
        $anoEleicao = $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada();
        $testemunhas = $denunciaSalva->getTestemunhas();
        $situacaoDenuncia = $this->getMaxSituacaoDenuncia($denunciaSalva->getId());
        $situacao = $this->getSituacaoAtualDenuncia($situacaoDenuncia['id']);
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
        ];
        return $parametrosEmail;
    }

    /**
     * Envia o e-mail para o responsável pelo cadastro da denúncia
     *
     * @param int $idAtivSecundaria
     * @param array $parametrosEmail ,
     * @param Denuncia $denunciaSalva ,
     * @param string $nomeTemplate
     */
    private function enviarEmailResponsavelDenuncia(
        $idAtivSecundaria,
        $parametrosEmail,
        $denunciaSalva,
        $nomeTemplate,
        $tipo
    ) {
        $destinatarios[] = $denunciaSalva->getPessoa()->getEmail();
        $idTipoEmail = $tipo;
        $this->enviarEmailDenuncia($idAtivSecundaria, $destinatarios, $idTipoEmail, $parametrosEmail, $nomeTemplate);
    }

    /**
     * Envia o e-mail para o coordenador e o adjunto da Comissão Eleitoral da
     * UF do denunciado
     *
     * @param int    $idAtivSecundaria
     * @param array  $parametrosEmail ,
     * @param int    $idCauUf         ,
     * @param string $nomeTemplate
     *
     * @throws \App\Exceptions\NegocioException
     */
    private function enviarEmailResponsavelCoordenadorAdjunto(
        int $idAtivSecundaria,
        array $parametrosEmail,
        int $idCauUf,
        $nomeTemplate,
        $idTipoEmail
    ) {
        $membros = $this->getCoordenadorEAdjuntoComissao($idCauUf);

        $listaDestinatarios = [];

        if (!empty($membros)) {
            /** @var MembroComissao $membro */
            foreach ($membros as $membro) {
                $listaDestinatarios[] = $membro->getProfissionalEntity()->getPessoa()->getEmail();
            }
        }

        $destinatarios = array_unique($listaDestinatarios);
        if (!empty($destinatarios)) {
            $this->enviarEmailDenuncia($idAtivSecundaria, $destinatarios, $idTipoEmail, $parametrosEmail, $nomeTemplate);
        }
    }

    /**
     * Envia o e-mail para o Assessor da Comissão Eleitoral da UF do denunciado
     *
     * @param int    $idAtivSecundaria
     * @param array  $parametrosEmail
     * @param        $idCauUf
     * @param string $nomeTemplate
     *
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function enviarEmailResponsavelAssessor(
        $idAtivSecundaria,
        $parametrosEmail,
        $idCauUf,
        $nomeTemplate,
        $idTipoEmail
    ) {
        if (empty($idCauUf)) {
            $ids[] = Constants::COMISSAO_MEMBRO_CAU_BR_ID;
            $ids[] = Constants::IES_ID;
        } else {
            $ids[] = $idCauUf;
        }

        $assessores = $this->getCorporativoService()->getUsuariosAssessoresCE($ids);
        if (!empty($assessores)) {
            foreach ($assessores as $destinatario) {
                $destinatarios[] = $destinatario->getEmail();
            }
            $this->enviarEmailDenuncia($idAtivSecundaria, $destinatarios, $idTipoEmail, $parametrosEmail, $nomeTemplate);
        }
    }

    /**
     * Método auxiliar que busca o e-mail definido e realiza o envio
     *
     * @param            $idAtividadeSecundaria
     * @param array      $emailsDestinatarios
     * @param            $idTipoEmail
     * @param array|null $parametrosExtras
     * @param            $nomeTemplate
     */
    private function enviarEmailDenuncia(
        $idAtividadeSecundaria,
        $emailsDestinatarios,
        $idTipoEmail,
        $parametrosExtras = [],
        $nomeTemplate
    ) {
        $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
            $idAtividadeSecundaria, $idTipoEmail
        );

        if (is_array($emailAtividadeSecundaria)) {
            $emailAtividadeSecundaria = $emailAtividadeSecundaria[0];
        }

        $this->getEmailAtividadeSecundariaBO()->enviarEmailAtividadeSecundaria(
            $emailAtividadeSecundaria,
            $emailsDestinatarios,
            $nomeTemplate,
            $parametrosExtras
        );
    }

    /**
     * Retornar as denuncias com encaminhamentos de acordo com o tipo especifico
     *
     * @param int $idTipoEncaminhamento
     * @param int $idEleicao
     * @return array
     * @throws \Exception
     */
    public function getDenunciasEmRelatoriaEncaminhamentoPorTipo(int $idTipoEncaminhamento, int $idEleicao)
    {
        return $this->denunciaRepository->getDenunciaEmRelatoriaPorTipoEncaminhamento(
            $idTipoEncaminhamento, $idEleicao
        );
    }

    /**
     * Retorna as denúncias para rotina de recurso e contrarrazão de acordo com
     * o filtro
     *
     * @param \stdClass $filtroTO
     * @return array
     * @throws \Exception
     */
    public function getDenunciasEmJulgamentoParaRotinaRecursoContrarrazao($filtroTO)
    {
        return $this->denunciaRepository->getDenunciaEmJulgamentoParaRotinaRecursoContrarrazao($filtroTO);
    }

    /**
     * Retorna as denúncias para rotina de prazo de defesa encerrado de acordo com
     * o filtro
     *
     * @param \stdClass $filtroTO
     * @return array
     * @throws \Exception
     */
    public function getDenunciaAguardandoDefesaParaRotinaPrazoDefesaEncerrado($filtroTO)
    {
        return $this->denunciaRepository->getDenunciaAguardandoDefesaParaRotinaPrazoDefesaEncerrado($filtroTO);
    }

    /**
     * @param Denuncia $denuncia
     * @return array
     * @throws NegocioException
     */
    public function getEmailCoordenadores(Denuncia $denuncia)
    {
        $emails = [];

        $filial = $this->verificaDenunciaIdFilialIES($denuncia);
        $coordenadores = $this->getMembroComissaoBO()->getPorFiltro((object) [
            'idCauUf' => $filial,
            'tipoParticipacao' => Constants::TIPO_PARTICIPACAO_COORDENADOR
        ]);

        foreach ($coordenadores['membros'] as $membro) {
            /**
             * @var MembroComissao $membro
             */
            $emails[] = $membro->getProfissionalEntity()->getPessoa()->getEmail();
        }
        return array_unique($emails);
    }

    /**
     * @param Denuncia $denuncia
     * @return array
     */
    public function getEmailAssessores(Denuncia $denuncia)
    {
        $emails = [];

        $filial = $this->verificaDenunciaIdFilialIES($denuncia);
        $assessores = $this->getCorporativoService()->getUsuariosAssessoresCE([
            Constants::IES_ID,
            Constants::COMISSAO_MEMBRO_CAU_BR_ID,
            $filial,
        ]);

        if (!empty($assessores)) {
            /** @var UsuarioTO $assessor */
            foreach ($assessores as $assessor) {
                $emails[] = $assessor->getEmail();
            }
        }
        return $emails;
    }

    public function getEmailDenunciante(Denuncia $denuncia)
    {
        return $denuncia->getPessoa()->getEmail();
    }

    /**
     * @param Denuncia $denuncia
     * @return array
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getEmailDenunciados(Denuncia $denuncia)
    {
        switch ($denuncia->getTipoDenuncia()->getId())
        {
            case  Constants::TIPO_CHAPA:
                $idChapa = $denuncia->getDenunciaChapa()->getChapaEleicao()->getId();
                $responsaveis = $this->getMembroChapaBO()->getMembrosResponsaveisChapa($idChapa);
                return $this->getMembroChapaBO()->getListEmailsDestinatarios($responsaveis);
            case Constants::TIPO_MEMBRO_CHAPA;
                $emailsDenunciado = $this->getDenunciaMembroChapaBO()->getDadosDenunciante($denuncia->getId());
                return [$emailsDenunciado[0]['email']];
            case Constants::TIPO_MEMBRO_COMISSAO;
                $emailsDenunciado = $this->getDenunciaMembroComissaoBO()->getDadosDenunciante($denuncia->getId());
                return [$emailsDenunciado[0]['email']];
            default:
                return [];
        }
    }

    /**
     * Valida se o usuario loga é o denunciante de uma denuncia
     * @param Denuncia $denuncia
     * @return bool
     */
    public function isUsuarioDenunciante(Denuncia $denuncia) {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        return $usuario->idProfissional == $denuncia->getPessoa()->getProfissional()->getId();
    }

    /**
     * @param $idChapaEleicao
     */
    public function getPedidosSolicitadosPorChapa($idChapaEleicao)
    {
        $pedidos = $this->denunciaRepository->getPedidosDenunciaPorChapa($idChapaEleicao);

        $pedidosSolicitado = [];
        if (!empty($pedidos)) {
            $pedidosSolicitado = array_map(function($pedido) {
                $pedidoSolicitadoTO = PedidoSolicitadoTO::newInstance($pedido);

                $sancao = Arr::get($pedido, 'sancao');
                $idStatusJulgamento = Arr::get($pedido, 'idStatusJulgamento');
                $idSituacaoAtual = Arr::get($pedido, 'idSituacaoAtual');

                if ($idSituacaoAtual != Constants::STATUS_DENUNCIA_TRANSITADO_EM_JULGADO) {
                    $pedidoSolicitadoTO->setStatusEmAnalise();
                } else {
                    $pedidoSolicitadoTO->setStatusImprocedente();
                    if (
                        (is_null($sancao) && $idStatusJulgamento == Constants::TIPO_JULGAMENTO_PROCEDENTE) ||
                        (!is_null($sancao) && $sancao)
                    ) {
                        $pedidoSolicitadoTO->setStatusProcedente();
                    }
                }
                return $pedidoSolicitadoTO;
            }, $pedidos);
        }
        return $pedidosSolicitado;
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
     * Método para retornar a instancia de Impedimento Suspeição
     *
     * @return ImpedimentoSuspeicaoBO
     */
    private function getImpedimentoSuspeicaoBO()
    {
        if (empty($this->impedimentoSuspeicaoBO)) {
            $this->impedimentoSuspeicaoBO = new ImpedimentoSuspeicaoBO();
        }
        return $this->impedimentoSuspeicaoBO;
    }

    /**
     * Método para retornar a instancia de DenunciaDefesaBO
     *
     * @return DenunciaDefesaBO
     */
    private function getDenunciaDefesaBO()
    {
        if (empty($this->denunciaDefesaBO)) {
            $this->denunciaDefesaBO = new DenunciaDefesaBO();
        }
        return $this->denunciaDefesaBO;
    }

    /**
     * Método para retornar a instancia de Corporativo Service
     *
     * @return CorporativoService
     */
    private function getCorporativoService()
    {
        if (empty($this->corporativoService)) {
            $this->corporativoService = new CorporativoService();
        }
        return $this->corporativoService;
    }

    /**
     * Método para retornar a instancia de Calendario Api Service
     *
     * @return CalendarioApiService
     */
    private function getCalendarioApiService()
    {
        if (empty($this->calendarioApiService)) {
            $this->calendarioApiService = new CalendarioApiService();
        }
        return $this->calendarioApiService;
    }

    /**
     * Método para retornar a instancia de EncaminhamentoBo
     */
    public function getEncaminhamentoDenunciaBO(): EncaminhamentoDenunciaBO
    {
        if (empty($this->encaminhamentoDenunciaBO)) {
            $this->encaminhamentoDenunciaBO = new EncaminhamentoDenunciaBO();
        }
        return $this->encaminhamentoDenunciaBO;
    }

    /**
     * Método para retornar a instancia de Membro de Comissao BO
     *
     * @return MembroComissaoBO
     */
    private function getMembroComissaoBO()
    {
        if (empty($this->membroComissaoBO)) {
            $this->membroComissaoBO = new MembroComissaoBO();
        }
        return $this->membroComissaoBO;
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
     * Método para retornar a instância de 'ParecerFinalBO'
     *
     * @return ParecerFinalBO
     */
    private function getParecerFinalBO(): ParecerFinalBO
    {
        if (empty($this->parecerFinalBO)) {
            $this->parecerFinalBO = app()->make(ParecerFinalBO::class);
        }
        return $this->parecerFinalBO;
    }

    /**
     * Método para retornar a instância de 'AlegacaoFinalBO'
     *
     * @return AlegacaoFinalBO
     */
    private function getAlegacaoFinalBO(): AlegacaoFinalBO
    {
        if (empty($this->alegacaoFinalBO)) {
            $this->alegacaoFinalBO = app()->make(AlegacaoFinalBO::class);
        }
        return $this->alegacaoFinalBO;
    }

    /**
     * Método para retornar a instância de 'RecursoContrarrazaoBO'
     *
     * @return RecursoContrarrazaoBO
     */
    private function getRecursoContrarrazaoBO(): RecursoContrarrazaoBO
    {
        if (empty($this->recursoContrarrazaoBO)) {
            $this->recursoContrarrazaoBO = app()->make(RecursoContrarrazaoBO::class);
        }
        return $this->recursoContrarrazaoBO;
    }

    /**
     * Método para retornar a instância de 'JulgamentoDenunciaBO'
     *
     * @return JulgamentoDenunciaBO
     */
    private function getJulgamentoDenunciaBO(): JulgamentoDenunciaBO
    {
        if (empty($this->julgamentoDenunciaBO)) {
            $this->julgamentoDenunciaBO = app()->make(JulgamentoDenunciaBO::class);
        }
        return $this->julgamentoDenunciaBO;
    }

    /**
     * Método para retornar a instância de 'JulgamentoRecursoDenunciaBO'
     *
     * @return JulgamentoRecursoDenunciaBO
     */
    private function getJulgamentoRecursoDenunciaBO(): JulgamentoRecursoDenunciaBO
    {
        if (empty($this->julgamentoRecursoDenunciaBO)) {
            $this->julgamentoRecursoDenunciaBO = app()->make(JulgamentoRecursoDenunciaBO::class);
        }
        return $this->julgamentoRecursoDenunciaBO;
    }

    /**
     * Método para retornar a instância de 'ContrarrazaoRecursoDenunciaBO'
     *
     * @return ContrarrazaoRecursoDenunciaBO
     */
    private function getContrarrazaoRecursoDenunciaBO(): ContrarrazaoRecursoDenunciaBO
    {
        if (empty($this->contrarrazaoRecursoDenunciaBO)) {
            $this->contrarrazaoRecursoDenunciaBO = app()->make(ContrarrazaoRecursoDenunciaBO::class);
        }
        return $this->contrarrazaoRecursoDenunciaBO;
    }

    /**
     * Método para retornar a instância de 'FilialBO'
     *
     * @return FilialBO
     */
    private function getFilialBO(): FilialBO
    {
        if (empty($this->filialBO)) {
            $this->filialBO = app()->make(FilialBO::class);
        }
        return $this->filialBO;
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
     * Retorna uma instância de RecursoJulgamentoAdmissibilidadeBO
     *
     * @return RecursoJulgamentoAdmissibilidadeBO
     */
    private function getRecursoJulgamentoAdmissibilidadeBO()
    {
        if (empty($this->recursoJulgamentoAdmissibilidadeBO)) {
            $this->recursoJulgamentoAdmissibilidadeBO = app()->make(RecursoJulgamentoAdmissibilidadeBO::class);
        }

        return $this->recursoJulgamentoAdmissibilidadeBO;
    }

    /**
     * Retorna uma instância de JulgamentoRecursoAdmissibilidadeBO
     *
     * @return JulgamentoRecursoAdmissibilidadeBO
     */
    private function getJulgamentoRecursoAdmissibilidadeBO()
    {
        if (empty($this->julgamentoRecursoAdmissibilidadeBO)) {
            $this->julgamentoRecursoAdmissibilidadeBO = app()->make(JulgamentoRecursoAdmissibilidadeBO::class);
        }

        return $this->julgamentoRecursoAdmissibilidadeBO;
    }

    /**
     * Retorna a instância de PDFFactory conforme o padrão Lazy Initialization.
     *
     * @return PDFFActory
     */
    private function getPdfFactory()
    {
        if (empty($this->pdfFactory)) {
            $this->pdfFactory = app()->make(PDFFActory::class);
        }

        return $this->pdfFactory;
    }

    /**
     * @return PessoaBO
     */
    public function getPessoaBO(): PessoaBO
    {
        if (empty($this->pessoaBO)) {
            $this->setPessoaBO(new PessoaBO());
        }
        return $this->pessoaBO;
    }

    /**
     * @param PessoaBO $pessoaBO
     */
    public function setPessoaBO(PessoaBO $pessoaBO): void
    {
        $this->pessoaBO = $pessoaBO;
    }
}
