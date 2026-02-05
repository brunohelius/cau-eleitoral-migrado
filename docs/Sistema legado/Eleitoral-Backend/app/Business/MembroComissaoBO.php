<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 17/10/2019
 * Time: 11:13
 */

namespace App\Business;


use App\Config\Constants;
use App\Entities\ArquivoDecMembroComissao;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\DeclaracaoAtividade;
use App\Entities\DenunciaAdmitida;
use App\Entities\EleicaoSituacao;
use App\Entities\Entity;
use App\Entities\Filial;
use App\Entities\InformacaoComissaoMembro;
use App\Entities\MembroComissao;
use App\Entities\MembroComissaoSituacao;
use App\Entities\Profissional;
use App\Entities\SituacaoMembroComissao;
use App\Entities\TipoParticipacaoMembro;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\DocFactory;
use App\Factory\PDFFActory;
use App\Models\MembroComissaoStatus;
use App\Models\MembroComissao as ModelMembroComissao;
use App\Repository\ArquivoDecMembroComissaoRepository;
use App\Repository\DenunciaAdmitidaRepository;
use App\Repository\EleicaoSituacaoRepository;
use App\Repository\InformacaoComissaoMembroRepository;
use App\Repository\MembroComissaoRepository;
use App\Repository\SituacaoMembroComissaoRepository;
use App\Repository\TipoParticipacaoMembroRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoTO;
use App\To\ConviteStatusComissaoFiltroTO;
use App\To\MembroComissaoComboTO;
use App\To\MembroComissaoParticipacaoFiltroTO;
use App\To\MembroComissaoTO;
use App\To\ProfissionalTO;
use App\To\TipoConselheiroMembroComissaoTO;
use App\To\UsuarioTO;
use App\Util\Utils;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use PhpOffice\PhpWord\Exception\Exception;
use stdClass;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'MembroComissao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class MembroComissaoBO extends AbstractBO
{
    /**
     *
     * @var MembroComissaoRepository
     */
    private $membroComissaoRepository;

    /**
     *
     * @var DenunciaAdmitidaRepository
     */
    private $denunciaAdmitidaRepository;

    /**
     *
     * @var TipoParticipacaoMembroRepository
     */
    private $tipoParticipacaoRepository;

    /**
     *
     * @var SituacaoMembroComissaoRepository
     */
    private $situacaoMembroComissaoRepository;

    /**
     *
     * @var HistoricoInformacaoComissaoMembroBO
     */
    private $historicoInformacaoComissaoMembroBO;

    /**
     *
     * @var InformacaoComissaoMembroRepository
     */
    private $informacaoComissaoMembroRepository;

    /**
     *
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     *
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     *
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaBO;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var DeclaracaoAtividadeBO
     */
    private $declaracaoAtividadeBO;

    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;


    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var DenunciaBO
     */
    private $denunciaBO;

    /**
     * @var PDFFActory
     */
    private $pdfFactory;

    /**
     *
     * @var EleicaoSituacaoRepository
     */
    private $eleicaoSituacaoRepository;

    /**
     * @var DocFactory
     */
    private $docFactory;

    /**
     * @var ArquivoDecMembroComissaoRepository
     */
    private $arquivoDecMembroComissaoRepository;

    /**
     * @var InformacaoComissaoMembroBO
     */
    private $informacaoComissaoMembroBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->historicoInformacaoComissaoMembroBO = app()->make(HistoricoInformacaoComissaoMembroBO::class);
        $this->membroComissaoRepository = $this->getRepository(MembroComissao::class);
        $this->tipoParticipacaoRepository = $this->getRepository(TipoParticipacaoMembro::class);
        $this->situacaoMembroComissaoRepository = $this->getRepository(SituacaoMembroComissao::class);
        $this->informacaoComissaoMembroRepository = $this->getRepository(InformacaoComissaoMembro::class);
        $this->eleicaoSituacaoRepository = $this->getRepository(EleicaoSituacao::class);
        $this->denunciaAdmitidaRepository = $this->getRepository(DenunciaAdmitida::class);
        $this->corporativoService = app()->make(CorporativoService::class);
        $this->arquivoService = app()->make(ArquivoService::class);
        $this->emailAtividadeSecundariaBO = app()->make(EmailAtividadeSecundariaBO::class);
        $this->arquivoDecMembroComissaoRepository = $this->getRepository(ArquivoDecMembroComissao::class);
        $this->atividadeSecundariaBO = app()->make(AtividadeSecundariaCalendarioBO::class);
    }

    /**
     * Retorna todos os tipos de participação
     *
     * @return array
     */
    public function getTipoParticipacao()
    {
        return $this->tipoParticipacaoRepository->findAll();
    }

    /**
     * Salva os dados de Membro de Comissão
     *
     * @param $dadosTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function salvar($dadosTO)
    {
        $this->validarCamposObrigatorios($dadosTO);

        $acao = Constants::ACAO_HISTORICO_INFORMACAO_COMISSAO_INSERIR_MEMBROS;
        $idTipoAtividadeSecundaria = Constants::EMAIL_INCLUIR_MEMBRO;
        $idMembroSubstituto = null;
        try {
            $this->beginTransaction();

            $informacaoComissaoMembro = $this->informacaoComissaoMembroRepository->find($dadosTO->informacaoComissaoMembro->getId());

            //Pro alterar, caso a quantidade de membros tenha sido modificada
            $this->deleteMembrosPorInformacaoCauUf($informacaoComissaoMembro->getId(),
                $dadosTO->membros->first()->getIdCauUf(), count($dadosTO->membros));

            $idCalendario = $informacaoComissaoMembro->getAtividadeSecundaria()->getAtividadePrincipalCalendario()->getCalendario()->getId();
            $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getPorCalendario($idCalendario, 1, 2);

            foreach ($dadosTO->membros as $membroComissao) {
                if ($membroComissao->getId()) {
                    $membroAnterior = $this->membroComissaoRepository->getPorId($membroComissao->getId());
                    if ($membroAnterior->getPessoa() != $membroComissao->getPessoa()) {
                        MembroComissaoStatus::where('id_membro_comissao', $membroComissao->getId())
                            ->whereIn('id_status_membro_comissao', [Constants::SITUACAO_MEMBRO_REJEITADO, Constants::SITUACAO_MEMBRO_CONFIRMADO])
                            ->delete();
                    }
                    $membro = ModelMembroComissao::where('id_membro_comissao', $membroComissao->getId())->first();
                    $idMembroSubstituto = $membro->id_membro_substituto;
                } else {
                    $membroComissao->setIdSituacao(Constants::SITUACAO_MEMBRO_ACONFIRMAR);
                }

                $membroComissao->setExcluido(false);
                if (!empty($membroComissao->getId())) {
                    $acao = Constants::ACAO_HISTORICO_INFORMACAO_COMISSAO_ALTERAR_MEMBROS;
                    $idTipoAtividadeSecundaria = Constants::EMAIL_ALTERAR_MEMBRO;
                }
                
                $membroComissao = $this->criarSituacao($membroComissao);
                $membroComissao->setInformacaoComissaoMembro($informacaoComissaoMembro);
                if (!empty($membroComissao->getMembroSubstituto())) {
                    $membroSubstituto = $membroComissao->getMembroSubstituto();

                    if (!empty($idMembroSubstituto)) {
                        $membroAnterior = ModelMembroComissao::where('id_membro_comissao', $idMembroSubstituto)->first();
                        if ($membroAnterior->id_pessoa != $membroComissao->getMembroSubstituto()->getPessoa()) {
                            $membroSubstituto->setIdSituacao(Constants::SITUACAO_MEMBRO_ACONFIRMAR);
                        }
                    } else {
                        $membroSubstituto->setIdSituacao(Constants::SITUACAO_MEMBRO_ACONFIRMAR);
                    }
                    
                    $membroSubstituto->setInformacaoComissaoMembro($informacaoComissaoMembro);
                    $membroSubstituto = $this->criarSituacao($membroSubstituto);
                    $membroSubstitutoSalvo = $this->salvarMembroIndividual($membroSubstituto);
                    $membroComissao->setMembroSubstituto($membroSubstitutoSalvo);
                }

                if($membroComissao->getTipoParticipacao()->getId() != Constants::TIPO_PARTICIPACAO_SUBSTITUTO &&
                    $membroComissao->getTipoParticipacao()->getId() != Constants::TIPO_PARTICIPACAO_COORDENADOR_SUBSTITUTO) {
                    $this->salvarMembroIndividual($membroComissao);
                }

                if (!empty($atividadeSecundaria) && $membroComissao->isInserido()) {
                    $this->enviarEmailMembroComissao($membroComissao, $atividadeSecundaria->getId(), $idTipoAtividadeSecundaria);
                }
            }

            $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
            $historicoMembro = $this->historicoInformacaoComissaoMembroBO->criarHistorico(
                $informacaoComissaoMembro,
                $usuarioLogado->id,
                $acao,
                $dadosTO->justificativa);
            $historicoMembro->setHistComissao(true);

            $this->historicoInformacaoComissaoMembroBO->salvar($historicoMembro);

            $this->commitTransaction();

        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Verifica se os campos obrigatórios foram preenchidos.
     *
     * @param $dadosTO
     * @throws NegocioException
     */
    private function validarCamposObrigatorios($dadosTO)
    {
        $campos = [];

        if (empty($dadosTO->informacaoComissaoMembro)) {
            $campos[] = 'LABEL_INF_COMISSAO_MEMBRO';
        }

        foreach ($dadosTO->membros as $membro) {

            if (empty($membro->getTipoParticipacao())) {
                $campos[] = 'LABEL_TIPO_PARTICIPACAO';
            }

            if (in_array($membro->getTipoParticipacao()->getId(), [Constants::TIPO_PARTICIPACAO_COORDENADOR, Constants::TIPO_PARTICIPACAO_COORDENADOR_ADJUNTO]) && empty($membro->getPessoa())) {
                $campos[] = 'LABEL_CPF_PROFISSIONAL';
            }
            if (empty($membro->getIdCauUf())) {
                $campos[] = 'LABEL_CAU_UF';
            }
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Exclui logicamente todos os membros de uma comissão por id da informação e cau uf.
     *
     * @param $idInformacaoComissao
     * @param $idCauUf
     */
    private function deleteMembrosPorInformacaoCauUf($idInformacaoComissao, $idCauUf, $qtdAtual)
    {
        $totalMembros = $this->getTotalMembrosPorCauUf($idCauUf, $idInformacaoComissao);
        if (!empty($totalMembros)) {
            $primeiroTotal = array_pop($totalMembros);
            if ($qtdAtual != $primeiroTotal['quantidade']) {
                $this->membroComissaoRepository->deleteMembrosPorInformacaoCauUf($idInformacaoComissao, $idCauUf);
            }
        }
    }

    /**
     * Retorna a quantidade de membros para cada CAU UF.
     *
     * @param null $idCauUf
     * @param null $idInformacaoComissao
     * @return mixed
     */
    public function getTotalMembrosPorCauUf($idCauUf = null, $idInformacaoComissao = null)
    {
        return $this->membroComissaoRepository->getTotalMembrosPorCauUf($idCauUf, $idInformacaoComissao);
    }

    /**
     * Cria a Situação do Membro da Comissão para o Cadastro ou Alteração
     *
     * @param MembroComissao $membroComissao
     * @return MembroComissao
     * @throws \Exception
     */
    private function criarSituacao(MembroComissao $membroComissao)
    {
        $idVigente = Constants::SITUACAO_MEMBRO_ACONFIRMAR;
        if (!empty($membroComissao->getIdSituacao())) {
            $idVigente = $membroComissao->getIdSituacao();
        }
        $situacaoCalendario = $this->situacaoMembroComissaoRepository->find($idVigente);

        $membroComissaoSituacao = MembroComissaoSituacao::newInstance();
        $membroComissaoSituacao->setMembroComissao($membroComissao);
        $membroComissaoSituacao->setSituacaoMembroComissao($situacaoCalendario);
        $membroComissaoSituacao->setData(new DateTime());

        $membroComissao->setMembroComissaoSituacao(new ArrayCollection());
        $membroComissao->getMembroComissaoSituacao()->add($membroComissaoSituacao);

        return $membroComissao;
    }

    /**
     * Salva um membro da comissão individualmente
     *
     * @param MembroComissao $membroComissao
     * @return Entity|array|bool|ObjectManagerAware|object
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function salvarMembroIndividual(MembroComissao $membroComissao)
    {
        return $this->membroComissaoRepository->persist($membroComissao);
    }

    /**
     * Retorna BO de Atividade Secundário.
     * @return AtividadeSecundariaCalendarioBO
     */
    private function getAtividadeSecundariaBO()
    {
        if (empty($this->atividadeSecundariaBO)) {
            $this->atividadeSecundariaBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }
        return $this->atividadeSecundariaBO;
    }

    /**
     * Envia e-mail notificando os membros informando que o cadastro foi realizado.
     *
     * @param MembroComissao $membroComissao
     * @param integer $idAtividadeSecundaria
     * @param integer $idTipoAtividadeSecundaria
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailMembroComissao($membroComissao, $idAtividadeSecundaria, $idTipoAtividadeSecundaria)
    {
        $emailParametrizadoMembro = $this->emailAtividadeSecundariaBO->getEmailPorAtividadeSecundariaAndTipo($idAtividadeSecundaria,
            $idTipoAtividadeSecundaria);

        $destinatariosEmails = [];

        $endEmailResponsavel = $this->getDestinatarioEmail($membroComissao->getPessoa());
        array_push($destinatariosEmails, $endEmailResponsavel);

        if (!empty($emailParametrizadoMembro) && !empty($destinatariosEmails)) {
            $this->emailAtividadeSecundariaBO->enviarEmailAtividadeSecundaria(
                $emailParametrizadoMembro,
                $destinatariosEmails
            );
        }
    }

    /**
     * Retorna o destinatário do e-mail
     *
     * @param $idResponsavel
     * @return string|null
     * @throws NegocioException
     */
    private function getDestinatarioEmail($idResponsavel)
    {
        $emailResponsavel = null;
        if (!empty($idResponsavel)) {
            $responsavel = $this->getProfissionalBO()->getPorId($idResponsavel);

            if (!empty($responsavel)) {
                $emailResponsavel = $responsavel->getEmail();
            }
        }
        return $emailResponsavel;
    }

    /**
     * Retorna uma nova instância de 'ProfissionalBO'.
     *
     * @return ProfissionalBO|mixed
     */
    private function getProfissionalBO()
    {
        if (empty($this->profissionalBO)) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }

        return $this->profissionalBO;
    }

    /**
     * Atualiza o membro da comissão com o id da declaração.
     *
     * @param MembroComissao $membroComissao
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarDeclaracao(MembroComissao $membroComissao)
    {
        if ($membroComissao->getIdDeclaracao() == null) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, [], true);
        }

        $this->membroComissaoRepository->persist($membroComissao);
    }

    /**
     * Retorna os membros da comissão dado um id de informação de comissão e id cau uf.
     *
     * @param $idInformacaoComissao
     * @param $idCauUf
     *
     * @return mixed
     * @throws NegocioException
     */
    public function getPorInformacaoComissaoCauUf($idInformacaoComissao, $idCauUf)
    {
        $membros = $this->membroComissaoRepository->getPorInformacaoComissao($idInformacaoComissao, $idCauUf);
        $membrosComissao["membros"] = $membros;

        if (!empty($membros)) {
            $membrosComissao["membros"] = $this->organizeListaMembrosComissao($membros);
            $membrosComissao['qtdMembros'] = count($membrosComissao["membros"]);
        } else {
            throw new NegocioException(Message::MSG_CADASTRO_COMISSAO_UF_INFORMADA_NAO_FOI_REALIZADO);
        }

        return $membrosComissao;
    }

    /**
     * Retorna o tipo de conselheiro para membro da comissao do usuario logado
     *
     * @return mixed
     * @throws NegocioException
     * @throws \Exception
     */
    public function getTipoConselheiroProfissionalLogado()
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();

        $membro = $this->membroComissaoRepository->getPorCalendarioAndProfissional(
            $eleicaoVigente->getCalendario()->getId(),
            $usuario->idProfissional);

        if (empty($membro)) {
            throw new NegocioException(Message::MSG_VISUALIZACAO_PEDIDOS_APENAS_MEMBROS_COMISSAO);
        }

        return TipoConselheiroMembroComissaoTO::newInstanceFromMembroComissao($membro);
    }

    /**
     * Retorna os membros da comissão dado um id de informação de comissão organizado por id cau uf.
     *
     * @param $idInformacaoComissao
     * @return array
     * @throws NegocioException
     */
    public function getPorInformacaoComissao($idInformacaoComissao)
    {
        $membrosComissao = $this->membroComissaoRepository->getPorInformacaoComissao($idInformacaoComissao);

        if (!empty($membrosComissao)) {
            $membrosComissao = $this->organizeListaMembrosComissao($membrosComissao, true);
        }

        return $membrosComissao;
    }

    /**
     * Gera o documenmto .docx conforme o identificador da informação da comissão de membros.
     *
     * @param $idInformacaoComissao
     * @return ArquivoTO
     * @throws Exception
     * @throws NegocioException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function gerarDocumentoRelacaoMembros($idInformacaoComissao)
    {
        $cauUfs = $this->getFilialBO()->getFiliais();
        $membrosComissao = $this->getPorInformacaoComissao($idInformacaoComissao);
        $formatedMembrosComissao = [];

        foreach ($membrosComissao as $cauUf => $comissao) {

            $cauUf = (array_filter($cauUfs, function ($cauUfObj) use ($cauUf) {
                /** @var Filial $cauUfObj */
                return ($cauUfObj->getId() == $cauUf);
            }));

            /** @var Filial $cauUf */
            $cauUf = reset($cauUf);

            $filial = new stdClass();
            $filial->id = $cauUf->getId();
            $filial->prefixo = $cauUf->getPrefixo();
            $filial->descricao = $cauUf->getDescricao();

            $formatedMembrosComissao[] = [
                "membros" => $comissao,
                "cauUf" => $filial
            ];
        }
        return $this->getDocFactory()->gerarDocumentoRelacaoMembros($formatedMembrosComissao);
    }

    /**
     * Retorna uma nova instância de 'FilialBO'.
     *
     * @return FilialBO|mixed
     */
    private function getFilialBO()
    {
        if (empty($this->filialBO)) {
            $this->filialBO = app()->make(FilialBO::class);
        }

        return $this->filialBO;
    }

    /**
     * Retorna uma nova instância de 'DocFactory'.
     *
     * @return DocFactory
     */
    private function getDocFactory()
    {
        if ($this->docFactory == null) {
            $this->docFactory = app()->make(DocFactory::class);
        }

        return $this->docFactory;
    }

    /**
     * Retorna uma nova instância de 'ArquivoService'.
     *
     * @return ArquivoService
     */
    private function getArquivoService()
    {
        if (empty($this->arquivoService)) {
            $this->arquivoService = new ArquivoService();
        }
        return $this->arquivoService;
    }

    /**
     * Método de aceite de convite para ser membro da comissão da eleição
     *
     * @param ConviteStatusComissaoFiltroTO $convitesStatusComissaoFiltroTO
     * @return Entity[]
     * @throws \Exception
     */
    public function aceitarConvite(ConviteStatusComissaoFiltroTO $convitesStatusComissaoFiltroTO)
    {
        $acaoEmail = null;
        $acoesExecucao = [];
        $idAtividadeSecundaria = null;
        $idDeclaracao = $convitesStatusComissaoFiltroTO->getIdDeclaracao();
        $isConviteAceito = $convitesStatusComissaoFiltroTO->getIsConviteAceito();
        $idMembroComissao = $convitesStatusComissaoFiltroTO->getIdMembroComissao();
        $membroComissao = $this->membroComissaoRepository->getPorId($idMembroComissao);
        $arquivosMembroComissao = $convitesStatusComissaoFiltroTO->getArquivosDecMembroComissao();

        try {
            if (!empty($membroComissao)) {
                $this->beginTransaction();

                /** @var MembroComissao $membroComissao */
                $membroComissao->setArquivos($arquivosMembroComissao);
                $this->validarQuantidadeArquivos($membroComissao);
                $this->validarArquivos($membroComissao, $idDeclaracao);
                $membroComissao = $this->setNomeArquivoFisico($membroComissao);

                $arquivosDeclaracao = $membroComissao->getArquivos();

                $isInclusao = false;
                if (empty($membroComissao->getId())) {
                    $isInclusao = true;
                }

                $membroComissao->setArquivos(new ArrayCollection());

                $acaoEmail = Constants::EMAIL_RECUSAR_COMISSAO;
                $aceiteConviteSituacao = Constants::SITUACAO_MEMBRO_REJEITADO;

                /**
                 * Verifica se a ação disparada pelo membro foi aceitar convite e se possui alguma situação válida
                 * no grupo de atividades secundárias
                 */
//
//                Log::info('Aceitar Convite Membro Comissao', ['$membroComissao' => print_r($membroComissao,
//                    true), '$convitesStatusComissaoFiltroTO' => print_r($convitesStatusComissaoFiltroTO,
//                    true)]);

                if ($isConviteAceito !== false) {
                    if ($membroComissao->getId() === $convitesStatusComissaoFiltroTO->getIdMembroComissao()) {
                        $acaoEmail = Constants::EMAIL_ACEITAR_COMISSAO;
                        $membroComissao->setSitRespostaDeclaracao($isConviteAceito);
                        $aceiteConviteSituacao = Constants::SITUACAO_MEMBRO_CONFIRMADO;
                    }
                }

                $membroComissao->setIdSituacao($aceiteConviteSituacao);
                $membroComissaoAtualizado = $this->criarSituacao($membroComissao);

                $persiste = $this->membroComissaoRepository->persist($membroComissaoAtualizado);
                array_push($acoesExecucao, $persiste);

                $idAtividadeSecundaria = $this->atividadeSecundariaBO
                    ->getIdAtividadeSecundariaPorNivelMembroComissao($idMembroComissao, 4);

                $this->salvarArquivos($arquivosDeclaracao, $persiste, $isInclusao);

                $this->commitTransaction();
                return $acoesExecucao;
            }
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if (!empty($acaoEmail) && !empty($idAtividadeSecundaria)) {
            $this->enviarEmailsNotificacaoAceiteConviteGestores($idAtividadeSecundaria, $acaoEmail);
        }

    }

    /**
     *Método responsável por retornar a lista de email de membros e gestores
     * quando estiver 24 horas antes da data fim da atividade secundária de
     * aceite de convite para membros de comissão
     *
     * @throws \Exception
     */
    public function enviarEmailsconvitesPendentes()
    {
        $atividadesVigente = $this->getAtividadesSecundariasPorVigenciaMembro();

        /** @var AtividadeSecundariaCalendario $atividadeVigente */
        foreach ($atividadesVigente as $atividadeVigente) {

            $membros = $this->membroComissaoRepository->getMembrosComissaoConvitePendente(
                $atividadeVigente->getAtividadePrincipalCalendario()->getId()
            );

            $idsProfissional = array_map(function ($membro) {
                return $membro['pessoa'];
            }, $membros);

            $emailsProfissionais = $this->getProfissionalBO()->getListaProfissionaisFormatadaPorIds($idsProfissional);

            $emailProf = [];
            if (!empty($emailsProfissionais)) {
                /** @var Profissional $emailProfissional */
                foreach ($emailsProfissionais as $emailProfissional) {
                    array_push($emailProf, $emailProfissional->getEmail());
                }
            }

            $idsCauUf = array_map(function ($idCauUf) {
                return $idCauUf['idCauUf'];
            }, $membros);

            $idsCaufResult = array_unique($idsCauUf);
            $emailsProfissionaisGestoresCE = $this->corporativoService->getUsuariosAssessoresCE($idsCaufResult);

            $emailsGestoresCE = [];
            /** @var UsuarioTO $emailGestor */
            foreach ($emailsProfissionaisGestoresCE as $emailGestor) {
                array_push($emailsGestoresCE, $emailGestor->getEmail());
            }

            $destinatarios = array_merge($emailProf ?? [], $emailsGestoresCE ?? []);

            if (!empty($destinatarios)) {

                $emailParametrizadoAceitarConvite = $this->emailAtividadeSecundariaBO->getEmailPorAtividadeSecundariaAndTipo(
                    $atividadeVigente->getId(),
                    Constants::EMAIL_24H_ENCERRAMENTO
                );

                if (!empty($emailParametrizadoAceitarConvite)) {
                    $this->emailAtividadeSecundariaBO->enviarEmailAtividadeSecundaria(
                        $emailParametrizadoAceitarConvite,
                        $destinatarios
                    );
                }
            }

        }
    }

    /**
     * Retorna os membros da comissão dado um Filtro.
     *
     * @param $filtroTO
     *
     * @return mixed
     * @throws NegocioException
     */
    public function getPorFiltro($filtroTO)
    {
        $membros = $this->membroComissaoRepository->getPorFiltro($filtroTO);
        $membrosComissao["membros"] = $membros;

        if (!empty($membros)) {
            $membrosComissao["membros"] = $this->organizeListaMembrosComissao($membros);
            $membrosComissao['qtdMembros'] = count($membros);
        }

        return $membrosComissao;
    }

    /**
     * Recupera as lista de membros de uma comissão pela eleição informada.
     *
     * @param $idEleicao
     * @return array
     */
    public function getTotalRespostaDeclaracaoPorAtividadePrincipalUm($idAtividadePrincipal)
    {
        return $this->membroComissaoRepository->getQtdRespostaDeclaracaoPorAtividadePrincipal($idAtividadePrincipal);
    }

    /**
     * Retorna os membros e os substitutos de uma determinada comissão por uf
     *
     * @param $idCauUf
     * @return array
     * @throws NegocioException
     */
    public function getMembrosComissaoPorUf($idCauUf)
    {
        $filtroTO = new \stdClass();
        $filtroTO->idCauUf = $idCauUf;

        if($idCauUf == Constants::IES_ID){
            $idCauUf = Constants::ID_CAU_BR;
        }

        $filtroTO->tipoParticipacao = Constants::TIPO_PARTICIPACAO_MEMBRO;

        $membrosLista = $this->getPorFiltro($filtroTO);

        $filtroTO->tipoParticipacao = Constants::TIPO_PARTICIPACAO_SUBSTITUTO;
        $substitutos = $this->getPorFiltro($filtroTO);

        $filtroTO->tipoParticipacao = Constants::TIPO_PARTICIPACAO_COORDENADOR;
        $coordenador = $this->getPorFiltro($filtroTO);

        $filtroTO->tipoParticipacao = Constants::TIPO_PARTICIPACAO_COORDENADOR_SUBSTITUTO;
        $coorSubs = $this->getPorFiltro($filtroTO);

        $membros = array_merge($membrosLista['membros'], $substitutos['membros'], $coordenador['membros'], $coorSubs['membros']);

        $membrosComissaoTO = null;
        foreach ($membros as $membro) {
            $membrosComissaoTO[] = MembroComissaoTO::newInstanceFromEntity($membro);
        }
        return $membrosComissaoTO;
    }

    public function getPossiveisRelatores($idCauUf, $idDenuncia)
    {
        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();

        $filtroTO = new \stdClass();
        $filtroTO->idCauUf = $idCauUf;
        $filtroTO->idDenuncia = $idDenuncia;
        $filtroTO->ano = $eleicaoVigente->getAno();

        if($idCauUf == Constants::IES_ID){
            $idCauUf = Constants::ID_CAU_BR;
        }


        //Lista de Relatores
        $resRelatores = $this->denunciaAdmitidaRepository->findBy(array('denuncia'=>$filtroTO->idDenuncia));

        $membrosLista = $this->getPorFiltro($filtroTO);

        $membrosComissaoTO = null;
        foreach ($membrosLista['membros'] as $membro) {
            $isRelator = true;
            foreach ($resRelatores as $relator){
                if($relator->getMembroComissao()->getId() == $membro->getId()){
                    $isRelator = false;
                }
            }
            if($isRelator) {
                $membrosComissaoTO[] = MembroComissaoTO::newInstanceFromEntity($membro);
            }
        }
        return $membrosComissaoTO;
    }

    /**
     * Retorna o coordenador de uma determinada comissão por uf
     *
     * @param $idCauUf
     * @return array
     */
    public function getCoordenadorComissaoPorUf($idCauUf)
    {
        $filtroTO = new \stdClass();

        if($idCauUf == Constants::IES_ID){
            $idCauUf = Constants::ID_CAU_BR;
        }

        $filtroTO->idCauUf = $idCauUf;
        $filtroTO->tipoParticipacao = [
            Constants::TIPO_PARTICIPACAO_COORDENADOR,
            Constants::TIPO_PARTICIPACAO_COORDENADOR_SUBSTITUTO,
            Constants::TIPO_PARTICIPACAO_COORDENADOR_ADJUNTO
        ];

        $membrosLista = $this->getPorFiltro($filtroTO);

        $membros = array_merge($membrosLista['membros']);

        $membrosComissaoTO = null;
        foreach ($membros as $membro) {
            $membrosComissaoTO = MembroComissaoTO::newInstanceFromEntity($membro);
        }
        return $membrosComissaoTO;
    }

    /**
     * Valida se o Usuario logado tem acesso para acessar a Respectiva Denuncia
     *
     * @param $idDenuncia
     * @return void
     * @throws NegocioException
     */
    public function validarMembroComissaoPorDenuncia($idDenuncia)
    {
        $acessoLiberado = false;
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $denuncia = $this->getDenunciaBO()->getDenunciaPorId($idDenuncia);

        $this->getDenunciaBO()->verificaDenunciaFilialIES($denuncia);
        $idCauUf = $denuncia->getFilial()->getId();


        $atividadeSecundariaMC = $this->getAtividadeSecundariaCalendarioBO()->getAtividadeSecundariaPorNiveis(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_MEMBRO_COMISSAO,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_MEMBRO_COMISSAO
        );

        if (empty($atividadeSecundariaMC)) {
            throw new NegocioException(Message::MSG_NAO_EXISTE_ELEICAO_VIGENTE);
        }

        $membrosComissao = $this->getMembroComissaoPorProfissionalEAtividadeSecundaria(
            $usuarioLogado->idProfissional,
            $atividadeSecundariaMC->getId()
        );

        if (!empty($membrosComissao)) {
            $membroComissaoCEN = array_filter($membrosComissao, function ($membroComissao) {
                if ($membroComissao['idCauUf'] == Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                    return true;
                }
            });
            $isCoordenadorCEN = !empty($membroComissaoCEN) ? true : false;

            $membroComissaoCE = array_filter($membrosComissao, function ($membroComissao) use ($idCauUf) {
                if ($membroComissao['idCauUf'] == $idCauUf) {
                    return true;
                }
            });
            $isMembroComissaoCE = !empty($membroComissaoCE) ? true : false;

            $acessoLiberado = $isCoordenadorCEN || $isMembroComissaoCE;
        }

        if(!$acessoLiberado){
            throw new NegocioException(Lang::get('messages.denuncia.sem_permissao_de_acesso'));
        }
    }

    /**
     * Valida a quantidade de arquivos 'Declaração' para o MembroComissao
     *
     * @param MembroComissao $membroComissao
     * @throws NegocioException
     */
    private function validarQuantidadeArquivos(MembroComissao $membroComissao)
    {
        $arquivos = $membroComissao->getArquivos();
        if (!empty($arquivos) and count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO_RESOLUCAO_DECLARACAO) {
            throw new NegocioException(Message::MSG_QTD_ARQUIVOS_RESOLUCAO_DECLARACAO);
        }
    }

    /**
     * Valida a extensão e o tamanho do(s) arquivo(s) enviado(s)
     *
     * @param MembroComissao $membroComissao
     * @param $idDeclaracao
     * @throws NegocioException
     */
    private function validarArquivos(MembroComissao $membroComissao, $idDeclaracao)
    {
        if ($this->hasNovoArquivo($membroComissao)) {
            $arquivos = $membroComissao->getArquivos();

            if ($arquivos != null) {
                foreach ($arquivos as $arquivo) {
                    if (!$arquivo->getId()) {
                        $this->getArquivoService()->validarArquivoViaDeclaracao($arquivo, $idDeclaracao);
                    }
                }
            }
        }
    }

    /**
     * Verifica se o arquivo anexo deverá ser considerando os seguintes critérios:
     * - Caso o 'MembroComissao' seja novo.
     * - Caso o 'MembroComissao' seja a copia do vigênte e possua um novo arquivo.
     *
     * @param MembroComissao $membroComissao
     *
     * @return boolean
     */
    private function hasNovoArquivo(MembroComissao $membroComissao)
    {
        return empty($membroComissao->getId()) || (!empty($membroComissao->getId()) && !empty($membroComissao->getArquivos()));
    }

    /**
     * Cria os nomes de arquivo
     *
     * @param MembroComissao $membroComissao
     * @return MembroComissao
     */
    private function setNomeArquivoFisico(MembroComissao $membroComissao)
    {
        if ($membroComissao->getArquivos() != null) {
            foreach ($membroComissao->getArquivos() as $arquivo) {
                if (empty($arquivo->getId())) {
                    $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                        $arquivo->getNome(),
                        Constants::REFIXO_DOC_RESOLUCAO
                    );
                    $arquivo->setNomeFisico($nomeArquivoFisico);
                }
            }
        }
        return $membroComissao;
    }

    /**
     * Método auxiliar para Salvar dados do Arquivo Resolução para o calendário
     *
     * @param $arquivosDeclaracao
     * @param MembroComissao $membroComissaoSalvo
     * @param $isInclusao
     * @return MembroComissao
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function salvarArquivos($arquivosDeclaracao, MembroComissao $membroComissaoSalvo, $isInclusao)
    {
        $arquivosSalvos = new ArrayCollection();

        if (!empty($arquivosDeclaracao)) {
            foreach ($arquivosDeclaracao as $arquivoDeclaracao) {
                if (!empty($arquivoDeclaracao->getId()) && $isInclusao) { //Caso de replicação
                    $arquivoRecuperado = $this->arquivoDecMembroComissaoRepository->find($arquivoDeclaracao->getId())[0];

                    $arquivoDeclaracao->setId(null);
                    $arquivoDeclaracao->setMembroComissao($membroComissaoSalvo);
                    $arquivoSalvo = $this->arquivoDecMembroComissaoRepository->persist($arquivoDeclaracao);
                    $arquivosSalvos->add($arquivoSalvo);
                    $this->getArquivoService()->copiarArquivo($arquivoRecuperado, $arquivoDeclaracao);
                    $arquivoSalvo->setMembroComissao(null);
                    $arquivoSalvo->setArquivo(null);
                } else {
                    $arquivoDeclaracao->setMembroComissao($membroComissaoSalvo);
                    $arquivoSalvo = $this->arquivoDecMembroComissaoRepository->persist($arquivoDeclaracao);
                    $arquivoSalvo->setMembroComissao(null);
                    $arquivosSalvos->add($arquivoSalvo);
                    $this->salvaArquivosDiretorio($arquivoDeclaracao, $membroComissaoSalvo);
                    $arquivoSalvo->setArquivo(null);
                }
            }
        }

        $membroComissaoSalvo->setArquivos($arquivosSalvos);
        $membroComissaoSalvo->removerFiles();

        return $membroComissaoSalvo;
    }


    /**
     * Realiza o(s) upload(s) do(s) arquivo(s) do Calendário
     *
     * @param $arquivo
     * @param MembroComissao $membroComissao
     */
    private function salvaArquivosDiretorio($arquivo, MembroComissao $membroComissao)
    {
        $caminho = $this->arquivoService->getCaminhoRepositorioDocumentos($membroComissao->getId());

        if ($arquivo != null) {
            if (!empty($arquivo->getArquivo())) {
                $this->arquivoService->salvar($caminho, $arquivo->getNomeFisico(), $arquivo->getArquivo());
            }
        }
    }

    /**
     * Envia e-mail notificando os gestores que o convite enviado para o membro foi aceito.
     *
     * @param $idAtividadeSecundaria
     * @param $acaoEmail
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailsNotificacaoAceiteConviteGestores(
        $idAtividadeSecundaria,
        $acaoEmail
    )
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $idsCauUf = $usuarioLogado->idCauUf;
        $acessoresCEN = $this->corporativoService->getUsuariosAssessoresCEN();
        $acessoresCEUF = $this->corporativoService->getUsuariosAssessoresCE([$idsCauUf]);

        $destinatarios = array_merge($acessoresCEN ?? [], $acessoresCEUF ?? []);

        $destinatariosEmails = array_map(static function ($usuarioTO) {
            return $usuarioTO->getEmail();
        }, $destinatarios);


        if (!empty($destinatariosEmails)) {

            $emailParametrizadoAceitarConvite = $this->emailAtividadeSecundariaBO->getEmailPorAtividadeSecundariaAndTipo(
                $idAtividadeSecundaria,
                $acaoEmail
            );

            if (!empty($emailParametrizadoAceitarConvite)) {
                $this->emailAtividadeSecundariaBO->enviarEmailAtividadeSecundaria(
                    $emailParametrizadoAceitarConvite,
                    $destinatariosEmails
                );
            }
        }
    }

    /**
     * Método que retorna todas as atividades Secundárias que estão com períodos
     * vigente cadastradas na base de dados.
     *
     * @return array|Statement|mixed|NULL
     * @throws \Exception
     */
    public function getAtividadesSecundariasPorVigenciaMembro()
    {
        $dataFinal = Utils::adicionarDiasData(Utils::getDataHoraZero(), 1);

        $dados = $this->atividadeSecundariaBO->getAtividadesSecundariasPorVigencia(
            null,
            $dataFinal,
            1,
            4
        );

        return $dados;
    }

    /**
     * Retorna o membro da comissão dado um determinado id.
     *
     * @param $idMembroComissao
     * @return MembroComissao|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getPorId($idMembroComissao)
    {
        $membroComissao = $this->membroComissaoRepository->getPorId($idMembroComissao);

        if (!empty($membroComissao)) {
            $profissional = $this->corporativoService->getProfissionalPorId($membroComissao->getPessoa(), true);
            $membroComissao->setProfissional($profissional);
            $membroComissao->setSituacaoVigente();

            $this->verificaConviteAceitoMembroComissao($membroComissao);
        } else {
            throw new NegocioException(Message::NENHUM_MEMBRO_ENCONTRADO);
        }

        return $membroComissao;
    }


    /**
     * Retorna a lista de Membros dado um determinado membro da comissão
     *
     * @param $id
     * @param $idCauUf
     * @return array|null
     * @throws NegocioException
     */
    public function getListaMembrosPorMembro($id, $idCauUf = null)
    {
        $membrosComissao = $this->membroComissaoRepository->getListaMembrosPorMembro($id);

        if (!empty($membrosComissao)) {
            $membrosOrganizados = $this->organizeListaMembrosComissao($membrosComissao, true, false);

            $membrosRetorno = array();
            if (!empty($idCauUf)) {
                if (array_key_exists($idCauUf, $membrosOrganizados)) {
                    $membrosRetorno[$idCauUf] = $membrosOrganizados[$idCauUf];
                }
                return $membrosRetorno;
            } else {
                $filial = null;
                /** @var MembroComissao $membroComissao */
                foreach ($membrosComissao as $membroComissao) {
                    if ($membroComissao->getPessoa() == $id
                        || (!empty($membroComissao->getMembroSubstituto()) && $membroComissao->getMembroSubstituto()
                        ->getPessoa() == $id)) {
                        $filial = $this->membroComissaoRepository->getFilialMembroComissaoAtual($id);
                        break;
                    }
                }

                if (!empty($filial)) {
                    $membrosRetorno[$filial] = $membrosOrganizados[$filial];
                    return $membrosRetorno;
                }
            }
        } else {
            throw new NegocioException(Message::NENHUM_MEMBRO_ENCONTRADO);
        }
    }

    /**
     * Verificar
     * @param $idCalendario
     * @param $idCauUf
     * @param bool $isApenasPermissaoCEN
     * @return bool
     * @throws NegocioException
     */
    public function verificarMembroComissaoPorCauUf($idCalendario, $idCauUf, $isApenasPermissaoCEN = false)
    {
        $isMembro = false;

        $membroComissao = $this->getMembroComissaoParaVerificacaoPorCalendario(
            $idCalendario, '', false
        );

        $isMembroCEN = !empty($membroComissao) && $membroComissao->getIdCauUf() == Constants::ID_CAU_BR;
        $isMembroCE = !empty($membroComissao) && $membroComissao->getIdCauUf() != Constants::ID_CAU_BR;

        if ($isMembroCEN || ($isMembroCE && !$isApenasPermissaoCEN && $membroComissao->getIdCauUf() == $idCauUf)) {
            $isMembro = true;
        }

        return $isMembro;
    }

    /**
     * Método auxiliar que busca o membro da comissão pelo id do calendário informado para verificação
     *
     * @param $idCalendario
     * @param string $mensagem
     * @param bool $hasExcecao
     * @return MembroComissao
     * @throws NegocioException
     */
    public function getMembroComissaoParaVerificacaoPorCalendario(
        $idCalendario,
        $mensagem = Message::MSG_VISUALIZACAO_PEDIDOS_APENAS_MEMBROS_COMISSAO,
        $hasExcecao = true
    ): ?MembroComissao
    {
        /** @var MembroComissao $membroComissao */
        $membroComissao = $this->membroComissaoRepository->getPorCalendarioAndProfissional(
            $idCalendario,
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );

        if(empty($membroComissao)){
            $this->verificaConviteAceitoMembroComissao($membroComissao);
        }

        if (empty($membroComissao) && $hasExcecao) {
            //Caro(a) sr. (a), a visualização da atividade selecionada só está disponível para membros da Comissão Eleitoral!
            throw new NegocioException($mensagem);
        }

        return $membroComissao;
    }
    /**
     * Verificar
     *
     * @param $idCalendario
     * @param $idCauUf
     * @param bool $isApenasPermissaoCEN
     * @return bool
     * @throws NegocioException
     */
    public function verificarMembroComissaoPorCauUfAndChapa($idChapa, $idCauUf, $isApenasPermissaoCEN = false)
    {
        $isMembro = false;

        $membroComissao = $this->getMembroComissaoParaVerificacaoPorChapa(
            $idChapa
        );

        $isMembroCEN = !empty($membroComissao) && $membroComissao->getIdCauUf() == Constants::ID_CAU_BR;
        $isMembroCE = !empty($membroComissao) && $membroComissao->getIdCauUf() != Constants::ID_CAU_BR;

        if ($isMembroCEN || ($isMembroCE && !$isApenasPermissaoCEN && $membroComissao->getIdCauUf() == $idCauUf)) {
            $isMembro = true;
        }

        return $isMembro;
    }

    /**
     * Retorna os dados da informacao da comissao por um membro (idPessoa)
     *
     * @param $idPessoa
     * @return array|null
     * @throws NegocioException
     */
    public function getInformacaoPorMembro($idPessoa)
    {
        $informacaoComissao = $this->membroComissaoRepository->getInformacaoPorMembro($idPessoa);

        if (empty($informacaoComissao)) {
            throw new NegocioException(Message::NENHUM_MEMBRO_ENCONTRADO);
        }

        return $informacaoComissao;
    }

    /**
     * Valida se o Usuario logado é membro da comissão referente a eleição vigente.
     *
     * @return string
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validaMembroComissaoEleicaoVigente()
    {
        $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getAtividadeSecundariaAtivaPorNivel(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_MEMBRO_COMISSAO,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_MEMBRO_COMISSAO,
            true
        );

        if ($atividadeSecundaria == null) {
            throw new NegocioException(Message::MSG_NAO_EXISTE_ELEICAO_VIGENTE);
        }

        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $membroComissao = $this->getMembroComissaoPorProfissionalEAtividadeSecundaria(
            $usuarioLogado->idProfissional,
            $atividadeSecundaria->getId()
        );

        if (empty($membroComissao)) {
            throw new NegocioException(Lang::get('messages.permissao.permissao_somente_membro_comissao'));
        }

        return 'OK';
    }

    /**
     * Retorna os dados da informacao por comissão vigente
     *
     * @return array|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getInformacaoComissaoVigente()
    {
        $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getAtividadeSecundariaAtivaPorNivel(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_MEMBRO_COMISSAO,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_MEMBRO_COMISSAO
        );

        if(!empty($atividadeSecundaria)) {
            $informacaoComissao = $this->informacaoComissaoMembroRepository->getPorCalendario(
                $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getId()
            );
        }


        if (empty($informacaoComissao)) {
            throw new NegocioException(Message::NENHUM_MEMBRO_ENCONTRADO);
        }

        return $informacaoComissao;
    }

    /**
     * Retorna as UFs que possuem comissão
     */
    public function getUfsComissao()
    {
        $listaUfs = $this->membroComissaoRepository->getUfsComissao();

        if (!empty($listaUfs)) {
            $cauUfs = $this->corporativoService->getFiliais();
            $listaUfs = Utils::organizeIdCauUfParaLista($cauUfs, $listaUfs);
        }
        return $listaUfs;
    }

    /**
     * Retorna o destinatário do e-mail da da comissão eleitoral (coordenadores)
     *
     * @param $idAtividadeSecundaria
     * @param $idCauUF
     * @return array
     * @throws NegocioException
     */
    public function getEmailsCoordenadoresPorIdAtividaSecundaria($idAtividadeSecundaria, $idCauUF)
    {
        $comissaoCEN = $this->membroComissaoRepository->getCoordenadoresPorAtividadeSecundaria(
            $idAtividadeSecundaria, $idCauUF
        );

        return $this->getListEmailsDestinatarios($comissaoCEN);
    }

    /**
     * Retorna e-mails de membros comissão(membro e substituto) por atividade secundário(independente do nível) e uf
     *
     * @param $idAtividadeSecundaria
     * @param $idCauUF
     * @return array
     * @throws NegocioException
     */
    public function getEmailsMembrosComissaoPorIdAtividaSecundariaCauUf($idAtividadeSecundaria, $idCauUF)
    {
        $comissao = $this->membroComissaoRepository->getMembrosComissaoPorAtividadeSecundaria(
            $idAtividadeSecundaria, $idCauUF
        );

        return $this->getListEmailsDestinatarios($comissao);
    }

    /**
     * Retorna os nomes dos coordenadores profissionais
     *
     * @param $idAtividadeSecundaria
     * @param $idCauUF
     * @return null|array
     * @throws NegocioException
     */
    public function getNomesCoordenadoresProfissionaisPorIdAtividaSecundaria($idAtividadeSecundaria, $idCauUF)
    {
        $coordenadores = $this->membroComissaoRepository->getCoordenadoresPorAtividadeSecundaria(
            $idAtividadeSecundaria, $idCauUF
        );

        if($coordenadores){
            return array_map(function ($coord){
                /** @var MembroComissao $coord */
                return $coord->getProfissionalEntity()->getNome();
            }, $coordenadores);
        }

        return null;
    }

    /**
     * Retorna o destinatário do e-mail da lista de membros da comissão
     *
     * @param array $membrosComissao
     * @return array
     * @throws NegocioException
     */
    public function getListEmailsDestinatarios($membrosComissao)
    {
        $destinatariosEmail = [];

        if (!empty($membrosComissao)) {
            $idsProfissionais = array_map(function ($membroComissao) {
                /** @var MembroComissao $membroComissao */
                return $membroComissao->getPessoa();
            }, $membrosComissao);

            $profissionais = $this->getProfissionalBO()->getListaProfissionaisFormatadaPorIds($idsProfissionais);

            if (!empty($profissionais)) {
                /** @var ProfissionalTO $profissionalTO */
                foreach ($profissionais as $profissionalTO) {
                    $destinatariosEmail[$profissionalTO->getId()] = $profissionalTO->getEmail();
                }
            }
        }

        return $destinatariosEmail;
    }

    /**
     * Envia e-mail notificando os membros informando que o cadastro foi realizado.
     *
     * @param MembroComissao[] $membros
     * @param integer $idAtividadeSecundaria
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailsMembroComissao($membros, $idAtividadeSecundaria)
    {
        $emailParametrizadoIncluirMembro = $this->emailAtividadeSecundariaBO->getEmailPorAtividadeSecundariaAndTipo($idAtividadeSecundaria,
            Constants::EMAIL_INCLUIR_MEMBRO);
        $emailParametrizadoAlterarMembro = $this->emailAtividadeSecundariaBO->getEmailPorAtividadeSecundariaAndTipo($idAtividadeSecundaria,
            Constants::EMAIL_ALTERAR_MEMBRO);
        $destinatariosEmailsAlterarMembro = [];
        $destinatariosEmailsIncluirMembro = [];

        foreach ($membros as $membro) {
            $endEmailResponsavel = $this->getDestinatarioEmail($membro->getPessoa());
            if (!empty($membro->getId())) {
                array_push($destinatariosEmailsIncluirMembro, $endEmailResponsavel);
            } else {
                array_push($destinatariosEmailsAlterarMembro, $endEmailResponsavel);
            }
        }

        if (!empty($emailParametrizadoIncluirMembro) && !empty($destinatariosEmailsIncluirMembro)) {
            $this->emailAtividadeSecundariaBO->enviarEmailAtividadeSecundaria(
                $emailParametrizadoIncluirMembro,
                $destinatariosEmailsIncluirMembro
            );
        }

        if (!empty($emailParametrizadoAlterarMembro) && !empty($destinatariosEmailsAlterarMembro)) {
            $this->emailAtividadeSecundariaBO->enviarEmailAtividadeSecundaria(
                $emailParametrizadoAlterarMembro,
                $destinatariosEmailsAlterarMembro
            );
        }
    }

    /**
     * Método que organiza os dados da lista de Membros
     *
     * @param $membrosComissao
     * @param bool $organizarPorCauUf
     * @param bool $addDadosComplementaresProfissional
     * @return array
     */
    private function organizeListaMembrosComissao(
        $membrosComissao,
        $organizarPorCauUf = false,
        $addDadosComplementaresProfissional = true
    )
    {
        if ($addDadosComplementaresProfissional) {
            $idsProfissionais = [];
            /** @var  $membro MembroComissao */
            foreach ($membrosComissao as $membro) {

                $idsProfissionais[] = $membro->getPessoa();

                if (!empty($membro->getMembroSubstituto())) {
                    $membroSubstituto = $membro->getMembroSubstituto();
                    $idsProfissionais[] = $membroSubstituto->getPessoa();
                }
            }
            $profissionaisRetorno = $this->getProfissionalBO()->getListaProfissionaisFormatadaPorIds(
                $idsProfissionais, true
            );
        }

        $listaMembros = array();
        if (!empty($membrosComissao)) {
            /** @var  $membroComissao MembroComissao */
            foreach ($membrosComissao as $membroComissao) {

                if ($membroComissao->getTipoParticipacao()->getId() != Constants::TIPO_PARTICIPACAO_COORDENADOR_SUBSTITUTO &&
                    $membroComissao->getTipoParticipacao()->getId() != Constants::TIPO_PARTICIPACAO_SUBSTITUTO) {

                    if ($addDadosComplementaresProfissional) {
                        $profissional = !empty($profissionaisRetorno[$membroComissao->getPessoa()])
                            ? $profissionaisRetorno[$membroComissao->getPessoa()]
                            : null;
                        $membroComissao->setProfissional($profissional);
                    }

                    $membroComissao->setSituacaoVigente();
                    if (!empty($membroComissao->getMembroSubstituto())) {
                        $membroSubstituto = $membroComissao->getMembroSubstituto();

                        if ($addDadosComplementaresProfissional) {
                            $profissionalSubstituto = !empty($profissionaisRetorno[$membroSubstituto->getPessoa()])
                                ? $profissionaisRetorno[$membroSubstituto->getPessoa()]
                                : null;
                            $membroComissao->getMembroSubstituto()->setProfissional($profissionalSubstituto);
                        }
                        $membroSubstituto->setSituacaoVigente();

                        $this->verificaConviteAceitoMembroComissao($membroSubstituto);
                    }
                    if ($organizarPorCauUf) {
                        $listaMembros[$membroComissao->getIdCauUf()][] = $membroComissao;
                    } else {
                        $listaMembros[] = $membroComissao;
                    }
                }

            }
        }

        return $listaMembros;
    }

    /**
     * @param $membrosComissao
     * @return MembroComissaoComboTO[]
     * @throws \Exception
     */
    public function getProfissionaisMembroComissao($membrosComissao) {
        $membrosComissaoComboTO = null;
        foreach ($membrosComissao as $membro) {
            $membrosComissaoComboTO[] = MembroComissaoComboTO::newInstanceFromEntity($membro);
        }
        return $membrosComissaoComboTO;
    }

    /**
     * @param $idAtividadeSecundaria
     * @param $idCauUf
     * @param $idProfissional
     * @return bool
     */
    public function isCoordenadorPorAtividadeSecundariaAndCauUf($idAtividadeSecundaria, $idCauUf, $idProfissional)
    {
        $coordenadores = $this->membroComissaoRepository->getCoordenadoresPorAtividadeSecundaria(
            $idAtividadeSecundaria, $idCauUf
        );
        $isCoordenador = false;
        if(!empty($coordenadores)) {
            /** @var MembroComissao $coordenador */
            foreach ($coordenadores as $coordenador) {
                if($coordenador->getProfissionalEntity()->getId() == $idProfissional) {
                    $isCoordenador = true;
                }
            }
        }

        return $isCoordenador;
    }


    /**
     * Retorna a quantidade de membros que tem relação com a declaração informada.
     *
     * @param integer $idModulo
     *
     * @return integer
     */
    public function getCountMembrosPorDeclaracao($idDeclaracao)
    {
        $count = $this->membroComissaoRepository->getCountMembrosPorDeclaracao($idDeclaracao);
        return $count > 0;
    }

    /**
     * Recupera as lista de membros de uma comissão pela eleição informada.
     *
     * @param $idEleicao
     * @return array
     */
    public function getMembrosPorEleicao($idEleicao)
    {
        return $this->membroComissaoRepository->getMembrosPorEleicao($idEleicao);
    }


    /**
     * retorna os dados da declaração caso o profissional esteja cadastrado
     * como membro de comissão eleitoral e esteja dentro do prazo vigente da atividade secundária
     * e as parametrizações de e-mail e declarações estejam configuradas.
     *
     * @param $idProfissional
     * @return MembroComissaoParticipacaoFiltroTO
     * @throws NegocioException
     * @throws \Exception
     */
    public function isParticipanteComissao($idProfissional)
    {
        $dados = false;
        $situacoes = $this->eleicaoSituacaoRepository->getSituacoesPorIdPessoa($idProfissional);
        $situacao = null;
        if (!empty($situacoes)) {

            $listaIdsMembroComissao = [];
            foreach ($situacoes as $situacao) {
                array_push($listaIdsMembroComissao, $situacao['idMembroComissao']);
            }

            $idMembroVigente = max($listaIdsMembroComissao);

            foreach ($situacoes as $situacaoTO) {
                if ($situacaoTO['idMembroComissao'] == $idMembroVigente) {
                    $situacao = $situacaoTO;
                }
            }

            /** @var DeclaracaoAtividade $declaracaoAtividade */
            $declaracaoAtividade = $this->getDeclaracaoAtividadeBO()->getDeclaracaoPorAtividadeSecundariaTipo(
                $situacao['idAtividadeSecundaria'],
                Constants::TIPO_DECLARACAO_MEMBRO_PARA_PARTICIPAR_COMISSAO
            );

            if ($situacao['idCauUf'] == Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                $situacao['prefixo'] = Constants::PREFIXO_CONSELHO_ELEITORAL_NACIONAL;
            }

            $dados = MembroComissaoParticipacaoFiltroTO::newInstance(
                $declaracaoAtividade->getDeclaracao(),
                $situacao
            );
        }

        return $dados;
    }

    /**
     * Retorna uma nova instância de 'DeclaracaoAtividadeBO'.
     *
     * @return DeclaracaoAtividadeBO|mixed
     */
    private function getDeclaracaoAtividadeBO()
    {
        if (empty($this->declaracaoAtividadeBO)) {
            $this->declaracaoAtividadeBO = app()->make(DeclaracaoAtividadeBO::class);
        }

        return $this->declaracaoAtividadeBO;
    }

    /**
     * Retorna o status do membro da comissão pelo id do profissional
     * @param $idProfissional
     * @return array
     * @throws \Exception
     */
    public function getStatusMembroComissaoPorIdProfissional($idProfissional)
    {
        $situacoes = $this->eleicaoSituacaoRepository->getSituacoesPorIdPessoa($idProfissional);
        $isConviteAceito = false;
        $situacaoMembro = null;
        $respostaTO = [];
        $listaIdsMembroComissao = [];
        foreach ($situacoes as $situacao) {
            array_push($listaIdsMembroComissao, $situacao['idMembroComissao']);
        }

        $situacaoCalendario = false;
        if (!empty($situacoes)) {
            $idMembroVigente = max($listaIdsMembroComissao);
            foreach ($situacoes as $situacao) {
                if ($situacao['idMembroComissao'] == $idMembroVigente) {
                    $situacaoMembro = $situacao['situacaoMembro'];
                    $situacaoCalendario = $situacao['sitCalendario'];
                }
            }
        }

        if ($situacaoMembro != 2) {
            $isConviteAceito = false;
        } else {
            $isConviteAceito = true;
        }

        if ($situacaoCalendario == false) {
            $isConviteAceito = false;
        }

        $respostaTO = [
            "isConnviteAceito" => $isConviteAceito,
            "situacaoCalendario" => $situacaoCalendario
        ];

        return $respostaTO;
    }

    /**
     * Verifica se já existe Membros de Comissão cadastrado para o 'Calendário' e UF do usuário logado.
     *
     * @param int $idCalendario
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function validarMembrosComissaoExistentePorCalendarioUsuario($idCalendario)
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $informacaoComissao = $this->getInformacaoComissaoMembroBO()->getPorCalendario($idCalendario);

        if (!empty($informacaoComissao)) {
            if ($usuario->idCauUf == Constants::ID_CAU_BR) {
                $filiais = $this->getFilialBO()->getFiliaisMembrosNaoCadastradosPorCalendario(
                    $idCalendario, $informacaoComissao->getId());
                // Caso existam UF's de Comissão de Membros que ainda não foram cadastradas.
                if (empty($filiais)) {
                    throw new NegocioException(Message::MSG_CADASTRO_COMISSAO_UFS_CEN_JA_FORAM_CRIADOS);
                }
            } else {
                $membrosComissao = $this->membroComissaoRepository
                    ->getPorInformacaoComissao($informacaoComissao->getId(), $usuario->idCauUf);
                // Caso a Comissão de Membros da UF não tenha sido cadastrado anteriormente.
                if (!empty($membrosComissao)) {
                    throw new NegocioException(Message::MSG_CADASTRO_COMISSAO_UF_INFORMADA_JA_FOI_CRIADO);
                }
            }
        }
    }

    /**
     * Retorna uma nova instância de 'InformacaoComissaoMembroBO'.
     *
     * @return InformacaoComissaoMembroBO|mixed
     */
    private function getInformacaoComissaoMembroBO()
    {
        if (empty($this->informacaoComissaoMembroBO)) {
            $this->informacaoComissaoMembroBO = app()->make(InformacaoComissaoMembroBO::class);
        }

        return $this->informacaoComissaoMembroBO;
    }

    /**
     * Retorna os membros de comissão dado um id cau uf.
     *
     * @param $filtroTO
     *
     * @return mixed
     * @throws NegocioException
     */
    public function getComissaoPorFiltro($filtroTO)
    {
        $informacaoComissao = $this->getInformacaoComissaoVigente();

        $membros = $this->membroComissaoRepository->getPorInformacaoComissao(
            $informacaoComissao['id'], $filtroTO->idCauUf, 10
        );

        $membrosComissao = array();
        if (!empty($membros)) {
            $membrosProfissionaisTO = $this->getProfissionaisMembroComissao($membros);

            if (!empty($filtroTO->nomeRegistro)) {
                $novaListaMembros = array();
                foreach ($membrosProfissionaisTO as $membroProfissionalTO) {
                    if ((strpos(\strtolower(Utils::tirarAcentos($membroProfissionalTO->getProfissional()->getNome())), \strtolower(Utils::tirarAcentos($filtroTO->nomeRegistro))) !== false)
                        or (strpos(\strtolower($membroProfissionalTO->getProfissional()->getRegistroNacional()), \strtolower($filtroTO->nomeRegistro)) !== false)) {
                        $novaListaMembros[] = $membroProfissionalTO;
                    }
                }
                $membros = $novaListaMembros;
            }

            $membrosComissao = $membros;
        }

        return $membrosComissao;
    }

    /**
     * Recupera o membro comissao de acordo com o id Profissional e a AtividadeSecundaria
     *
     * @param $idProfissional
     * @param $idAtividadeSecundaria
     * @return mixed
     */
    public function getMembroComissaoPorProfissionalEAtividadeSecundaria(
        $idProfissional,
        $idAtividadeSecundaria
    ) {
        return $this->membroComissaoRepository->getMembroComissaoPorProfissionalEAtividadeSecundaria(
            $idProfissional,
            $idAtividadeSecundaria
        );
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
     * Retorna a instância de PDFFactory conforme o padrão Lazy Initialization.
     *
     * @return PDFFActory
     */
    private function getPdfFactory()
    {
        if ($this->pdfFactory == null) {
            $this->pdfFactory = app()->make(PDFFActory::class);
        }

        return $this->pdfFactory;
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
     * Recupera a Instancia de Denuncia BO.
     *
     * @return DenunciaBO
     */
    public function getDenunciaBO()
    {
        if (empty($this->denunciaBO)) {
            $this->denunciaBO = new DenunciaBO();
        }

        return $this->denunciaBO;
    }

    /**
     * Método auxiliar que busca o membro da comissão pelo id do calendário informado para verificação
     *
     * @param $idCalendario
     * @param string $mensagem
     * @param bool $hasExcecao
     * @return MembroComissao[]|null
     * @throws NegocioException
     */
    public function getMembroComissaoParaVerificacaoPorCalendarios(
        $idsCalendario,
        $mensagem = Message::MSG_VISUALIZACAO_PEDIDOS_APENAS_MEMBROS_COMISSAO,
        $hasExcecao = true
    ): ?MembroComissao {
        /** @var MembroComissao[] $membrosComissao */
        $membrosComissao = $this->membroComissaoRepository->getPorCalendariosAndProfissional(
            $idsCalendario,
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );

        if (empty($membrosComissao) && $hasExcecao) {
            //Caro(a) sr. (a), a visualização da atividade selecionada só está disponível para membros da Comissão Eleitoral!
            throw new NegocioException($mensagem);
        }

        return $membrosComissao;
    }

    /**
     * Buscar membros da comissão por id chapa.
     *
     * @param $idChapa
     * @return MembroComissao
     */
    public function getMembroComissaoParaVerificacaoPorChapa($idChapa)
    {
        /** @var MembroComissao $membroComissao */
        $membroComissao = $this->membroComissaoRepository->getPorChapaAndProfissional(
            $idChapa,
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );

        return $membroComissao;
    }

    /**
     * Retorna uma nova instância de 'ChapaEleicaoBO'.
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
     * Retorna a lista de Coordenadores dada uma determinada chapa
     *
     * @param $idChapa
     * @return array|null
     * @throws NegocioException
     */
    public function getCoordenadoresPorChapaId($idChapa)
    {
        $chapaEleicao = $this->getChapaEleicaoBO()->getPorId($idChapa);

        if (!empty($chapaEleicao)) {
            return $this->membroComissaoRepository->getCoordenadoresPorCauUfId($chapaEleicao->getIdCauUf());
        }
    }

    /**
     * Retorna a lista de Assessores dada uma determinada chapa
     *
     * @param $idChapa
     * @return array|null
     * @throws NegocioException
     */
    public function getAssessoresPorChapaId($idChapa)
    {
        $assessores = [];
        $chapaEleicao = $this->getChapaEleicaoBO()->getPorId($idChapa);

        if (!empty($chapaEleicao)) {
            $assessoresCE = $this->corporativoService->getUsuariosAssessoresCE($chapaEleicao->getIdCauUf());
            $assessoresCEN = $this->corporativoService->getUsuariosAssessoresCEN();

            if (!empty($assessoresCE)) {
                $assessores['assessoresCE'] = $assessoresCE;
            }

            if (!empty($assessoresCEN)) {
                $assessores['assessoresCEN'] = $assessoresCEN;
            }
            return $assessores;
        }
    }

    /**
     * @param MembroComissao $membroComissao
     */
    private function verificaConviteAceitoMembroComissao(MembroComissao &$membroComissao = null): void
    {
        $idProfissional = empty($membroComissao) ? $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional :
            $membroComissao->getPessoa();

        /** @var MembroComissao[] $membroConfirmado */
        $membroConfirmado = $this->membroComissaoRepository->getMembroComissaoPorProfissionalAndIdInformacaoComissaoMembroAndIdCauUf(
            $idProfissional
        );

        if (!empty($membroConfirmado) && Arr::exists($membroConfirmado, 0)) {
            if (Arr::has($membroConfirmado[0]->getMembroComissaoSituacao(), 0))

                if(empty($membroComissao)){
                    $membroComissao = $membroConfirmado[0];
                }
                $membroComissao->setSituacao($membroConfirmado[0]->getMembroComissaoSituacao()->get(0)
                    ->getSituacaoMembroComissao());
        }
    }
}
