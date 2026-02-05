<?php
/*
 * DefesaImpugnacaoBO.php Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;


use App\Config\Constants;
use App\Entities\ArquivoDefesaImpugnacao;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Calendario;
use App\Entities\ChapaEleicao;
use App\Entities\DefesaImpugnacao;
use App\Entities\PedidoImpugnacao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailDefesaImpugnacaoJob;
use App\Repository\DefesaImpugnacaoRepository;
use App\To\DefesaImpugnacaoTO;
use App\To\DefesaImpugnacaoValidacaoAcessoProfissionalTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'DefesaImpugnacao'.
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DefesaImpugnacaoBO extends AbstractBO
{

    /**
     * @var DefesaImpugnacaoRepository
     */
    private $defesaImpugnacaoRepository;

    /**
     * @var PedidoImpugnacaoBO
     */
    private $pedidoImpugnacaoBO;

    /**
     * @var ArquivoPedidoImpugnacaoBO
     */
    private $arquivoDefesaImpugnacaoBO;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var CalendarioBO
     */
    private $calendarioBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->defesaImpugnacaoRepository = $this->getRepository(DefesaImpugnacao::class);
    }

    /**
     * Buscar Defesa de pedido de impugnação por id.
     *
     * @param int $id
     * @return DefesaImpugnacao
     * @throws NonUniqueResultException
     */
    public function getPorId(int $id): DefesaImpugnacao
    {
        return $this->defesaImpugnacaoRepository->getPorId($id);
    }

    /**
     * Buscar Defesa de pedido de impugnação por 'id' do pedido de impugnação.
     *
     * @param int $idPedidoImpugnacao
     * @return DefesaImpugnacaoTO
     * @throws NonUniqueResultException
     */
    public function getPorPedidoImpugnacao(int $idPedidoImpugnacao)
    {
        /** @var DefesaImpugnacao $defesaImpugnacao */
        $defesaImpugnacao = $this->defesaImpugnacaoRepository->getPorPedidoImpugnacao($idPedidoImpugnacao);
        /** @var DefesaImpugnacaoTO $defesaImpugnacaoTO */
        $defesaImpugnacaoTO = null;
        if($defesaImpugnacao) {
            $defesaImpugnacaoTO = $this->defesaImpugnacaoTOFromEntity($defesaImpugnacao);
        }
        //$this->validarAcessoProfissional($defesaImpugnacaoTO);
        return $defesaImpugnacaoTO;
    }

    /**
     * Salvar defesa para pedido de impugnação.
     *
     * @param DefesaImpugnacaoTO $data
     * @return true
     * @throws Exception
     */
    public function salvar(DefesaImpugnacaoTO $data)
    {
        try {
            $this->beginTransaction();
            $defesaImpugnacao = $this->entityFromDefesaImpugnacaoTO($data);

            $arquivosDefesaImpugnacao = $defesaImpugnacao->getArquivos();
            $defesaImpugnacao->setArquivos(null);

            $this->validarAcessoProfissional($data);

            $defesaImpugnacao->setDataCadastro(Utils::getData());

            $responsavel = $this->getUsuarioFactory()->getUsuarioLogado();
            $defesaImpugnacao->setIdProfissionalInclusao($responsavel->id);

            $this->validarCamposObrigatorios($defesaImpugnacao);

            /** @var DefesaImpugnacao $pedidoImpugnacaoSalvo */
            $defesaImpugnacaoSalvo = $this->defesaImpugnacaoRepository->persist($defesaImpugnacao);

            $this->getArquivoDefesaImpugnacaoBO()->salvar($defesaImpugnacaoSalvo, $arquivosDefesaImpugnacao);
            $this->getArquivoDefesaImpugnacaoBO()->removerPorIds($data->getIdArquivosRemover());

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if($defesaImpugnacao->getArquivos()){
            $defesaImpugnacao->setArquivos(
                array_map( function(ArquivoDefesaImpugnacao $arquivo) {
                    $arquivo->setArquivo(null);
                    return $arquivo;
                }, $defesaImpugnacao->getArquivos() )
            );
        }

        Utils::executarJOB(new EnviarEmailDefesaImpugnacaoJob($defesaImpugnacao->getId()));

        return true;
    }

    /**
     * Responsável por enviar emails após cadastrar defesa do pedido substituição chapa
     *
     * @param $idDefesaImpugnacao
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailsDefesaImpugnacaoIncluida($idDefesaImpugnacao)
    {
        /** @var DefesaImpugnacao $defesaImpugnacao */
        $defesaImpugnacao = $this->defesaImpugnacaoRepository->find($idDefesaImpugnacao);

        $idAtivSecundaria = $this->defesaImpugnacaoRepository->getIdAtividadeSecundariaDefesaImpugnacao(
            $idDefesaImpugnacao
        );

        if (!empty($idAtivSecundaria)) {
            $calendario = $this->getCalendarioBO()->getCalendarioPorAtividadeSecundaria($idAtivSecundaria);
            $parametrosEmail = $this->prepararParametrosEmailDefesaImpugnacao(
                $defesaImpugnacao, $calendario
            );

            $chapaEleicao = $defesaImpugnacao->getPedidoImpugnacao()->getMembroChapa()->getChapaEleicao();

            $this->efetivarEnvioEmailDefesaImpugnacaoIncluida($chapaEleicao, $idAtivSecundaria, $parametrosEmail);
        }
    }

    /**
     * Método realiza o envio de e-mail no ínicio da atividade 3.2 de de cadastro de defesa de impúgnação
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailIncioPeriodoCadastro()
    {
        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            Utils::getDataHoraZero(), null, 3, 2
        );

        foreach ($atividades as $atividadeSecundariaCalendario) {
            $pedidosImpugnacao = $this->getPedidoImpugnacaoBO()->getPorCalendario(
                $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId()
            );

            foreach ($pedidosImpugnacao as $pedidoImpugnacao) {
                $parametrosEmail = $this->getPedidoImpugnacaoBO()->prepararParametrosEmailCadastroPedidoImpugnacao(
                    $pedidoImpugnacao,
                    $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getAtividadeSecundariaCalendario()
                        ->getAtividadePrincipalCalendario()->getCalendario()
                );

                $destinatarios = $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa(
                    $pedidoImpugnacao->getMembroChapa()->getChapaEleicao()->getId()
                );

                $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
                    $atividadeSecundariaCalendario->getId(),
                    $destinatarios,
                    Constants::EMAIL_DEFESA_RESPONSAVEIS_CHAPA_ALERTA,
                    Constants::TEMPLATE_EMAIL_PEDIDO_IMPUGNACAO,
                    $parametrosEmail
                );
            }
        }
    }

    /**
     * Método realiza o envio de e-mail no ínicio da atividade 3.2 de de cadastro de defesa de impúgnação
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @throws Exception
     */
    public function enviarEmailFimPeriodoCadastro()
    {
        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            null,
            Utils::subtrairDiasData(Utils::getDataHoraZero(), 1),
            3,
            2
        );

        foreach ($atividades as $atividadeSecundariaCalendario) {
            $defesasImpugnacao = $this->defesaImpugnacaoRepository->getPorCalendario(
                $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId()
            );

            foreach ($defesasImpugnacao as $defesaImpugnacao) {
                $parametrosEmail = $this->prepararParametrosEmailDefesaImpugnacao(
                    $defesaImpugnacao,
                    $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()
                );

                $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
                    $atividadeSecundariaCalendario->getId(),
                    [$defesaImpugnacao->getPedidoImpugnacao()->getProfissional()->getPessoa()->getEmail()],
                    Constants::EMAIL_DEFESA_IMPUGNANTE_NOTIFICACAO_CADASTRO_DEFESA,
                    Constants::TEMPLATE_EMAIL_DEFESA_IMPUGNACAO,
                    $parametrosEmail
                );
            }
        }
    }

    /**
     * Valida campos obrigatórios de Defesa de pedido de impugnaçao.
     *
     * @param DefesaImpugnacao $defesa
     * @throws NegocioException
     */
    private function validarCamposObrigatorios(DefesaImpugnacao $defesaImpugnacao)
    {
        $campos = [];

        if (empty($defesaImpugnacao->getDescricao())) {
            array_push($campos, 'LABEL_DESCRICAO');
        }

        if (empty($defesaImpugnacao->getDataCadastro())) {
            array_push($campos, 'LABEL_DATA_CADASTRO');
        }

        if (empty($defesaImpugnacao->getPedidoImpugnacao())) {
            array_push($campos, 'LABEL_PEDIDO_IMPUGNACAO');
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Validar de acesso a defesa de impugnaçao, HST24(1.1, 1.2, 1.3, 1.4, 1.5)
     *
     * @param DefesaImpugnacaoTO $defesaImpugnacao
     * @throws NegocioException
     */
    public function validarAcessoProfissional(DefesaImpugnacaoTO $defesaImpugnacao): void
    {
        /** @var  DefesaImpugnacaoValidacaoAcessoProfissionalTO */
        $validacaoTO = $this->getDefesaImpugnacaoValidacaoAcessoProfissionalTO($defesaImpugnacao->getIdPedidoImpugnacao() ,$defesaImpugnacao);

        if (!$validacaoTO->getIsResponsavel()) {
            throw new NegocioException(Message::MSG_DEFESA_IMPUGNACAO_APENAS_RESPONSAVEL_CHAPA);
        }
        if(!$validacaoTO->getIsProfissional()) {
            if(!$validacaoTO->getHasPedidoImpugnacao()) {
                throw new NegocioException(Message::MSG_DEFESA_IMPUGNACAO_NECESSARIO_PEDIDO_IMPUGNACAO);
            }
            if(!$validacaoTO->getHasPedidoImpugnacaoEmAnalise()) {
                throw new NegocioException(Message::MSG_DEFESA_IMPUGNACAO_NECESSARIO_PEDIDO_IMPUGNACAO_EM_ANALISE);
            }
            if(!$validacaoTO->getIsAtividadeSecundariaVigente()) {
                throw new NegocioException(Message::MSG_DEFESA_IMPUGNACAO_PERIODO_CADASTRO_NAO_ESTA_VIGENTE);
            }
        }
    }

    /**
     * Retorna resultado validaçao do profissional.
     *
     * @param $idPedidoImpugnacao
     * @param DefesaImpugnacaoTO $defesaImpugnacaoTO
     * @return DefesaImpugnacaoValidacaoAcessoProfissionalTO
     * @throws NonUniqueResultException
     */
    public function getDefesaImpugnacaoValidacaoAcessoProfissionalTO(
        $idPedidoImpugnacao,
        ?DefesaImpugnacaoTO $defesaImpugnacaoTO
    ): DefesaImpugnacaoValidacaoAcessoProfissionalTO {
        $pedido = $this->getPedidoImpugnacaoBO()->findById($idPedidoImpugnacao);
        if(empty($defesaImpugnacaoTO)) {
            $defesaImpugnacao = $pedido->getDefesaImpugnacao();
            $defesaImpugnacaoTO = $defesaImpugnacao ?  $this->defesaImpugnacaoTOFromEntity(
                $this->defesaImpugnacaoRepository->getPorPedidoImpugnacao($idPedidoImpugnacao)
            ) : null;
        }

        $validacaoTO = DefesaImpugnacaoValidacaoAcessoProfissionalTO::newInstance([]);

        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();

        $eleicaoTO = $this->getEleicaoBO()->getEleicaoPorPedidoImpugnacao($idPedidoImpugnacao);

        $isProfissional = $this->getUsuarioFactory()->isProfissional();
        $validacaoTO->setIsProfissional($isProfissional);

        /** HST34(1.1) Valida se o usuario e responsavel da chapa. */
        $IdChapaEleicao = $this->getChapaEleicaoBO()->getIdChapaEleicaoPorCalendarioEResponsavel(
            $eleicaoTO->getCalendario()->getId(), $usuario->idProfissional
        );
        if (!empty($pedido) && $IdChapaEleicao == $pedido->getMembroChapa()->getChapaEleicao()->getId()) {
            $validacaoTO->setIsResponsavel(true);
        }

        if($isProfissional) {
            if($defesaImpugnacaoTO)  {
                /** HST34(1.4) Valida existe Pedido de Impugnaçao cadastrado. */
                $validacaoTO->setHasPedidoImpugnacao(true);

                /** HST34(2.1.2) Valida existe Defesa de Impugnaçao cadastrado. */
                $validacaoTO->setHasDefesaImpugnacao(true);

                /** HST34(1.3) Valida status do Pedido de Impugnaçao e igual a 'em analise'. */
                if($defesaImpugnacaoTO->getDescricaoStatusPedidoImpugnacao() == Constants::STATUS_IMPUGNACAO_EM_ANALISE ) {
                    $validacaoTO->setHasPedidoImpugnacaoEmAnalise(true);
                }
            }

            /** HST34(1.2) Valida periodo de vigencia da atividade secundaria. */
            if($this->getEleicaoBO()->getEleicaoVigentePorNivelAtividade(3, 2)) {
                $validacaoTO->setIsAtividadeSecundariaVigente(true);
            }
        }
        return $validacaoTO;
    }

    /**
     * Método auxiliar que prepara os parâmetros para o envio de e-mails
     *
     * @param DefesaImpugnacao $defesaImpugnacao
     * @param Calendario $calendario
     * @return array
     * @throws NegocioException
     */
    public function prepararParametrosEmailDefesaImpugnacao(
        DefesaImpugnacao $defesaImpugnacao,
        Calendario $calendario
    ): array {

        $parametrosEmail = $this->getPedidoImpugnacaoBO()->prepararParametrosEmailCadastroPedidoImpugnacao(
            $defesaImpugnacao->getPedidoImpugnacao(), $calendario
        );

        $parametrosEmail[Constants::PARAMETRO_EMAIL_JUSTIFICATIVA] = $defesaImpugnacao->getDescricao();
        return $parametrosEmail;
    }

    /**
     * Método que efetiva o envio dos e-mail após inclusão da defesa do pedido de impugnação
     *
     * @param ChapaEleicao $chapaEleicao
     * @param int|null $idAtivSecundaria
     * @param array $parametrosEmail
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function efetivarEnvioEmailDefesaImpugnacaoIncluida($chapaEleicao, $idAtivSecundaria, $parametrosEmail)
    {
        $idTipoCndidatura = $chapaEleicao->getTipoCandidatura()->getId();

        // enviar e-mail informativo para responsável chapa
        $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria(
            $idAtivSecundaria,
            $this->getMembroChapaBO()->getListaEmailsMembrosResponsaveisChapa($chapaEleicao->getId()),
            Constants::EMAIL_DEFESA_RESPONSAVEIS_CHAPA_NOTIFICACAO_CADASTRO,
            Constants::TEMPLATE_EMAIL_DEFESA_IMPUGNACAO,
            $parametrosEmail
        );

        // enviar e-mail informativo para conselheiros CEN e a comissão UF
        /*if ($idTipoCndidatura == Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR) {
            $this->getEmailAtividadeSecundariaBO()->enviarEmailConselheirosCoordenadoresComissao(
                $idAtivSecundaria,
                Constants::EMAIL_DEFESA_COMISSAO_ELEITORAL_NOTIFICACAO_CADASTRO,
                Constants::TEMPLATE_EMAIL_DEFESA_IMPUGNACAO,
                $chapaEleicao->getIdCauUf(),
                $parametrosEmail
            );
        }*/

        // enviar e-mail informativo para os acessores CEN/BR e CE
        $this->getEmailAtividadeSecundariaBO()->enviarEmailAcessoresCenAndAcessoresCE(
            $idAtivSecundaria,
            Constants::EMAIL_DEFESA_ASSESSOR_CEN_E_CE_NOTIFICACAO_CADASTRO,
            Constants::TEMPLATE_EMAIL_PEDIDO_IMPUGNACAO,
            $idTipoCndidatura != Constants::TIPO_CANDIDATURA_IES ? [$chapaEleicao->getCauUf()] : null,
            $parametrosEmail
        );
    }

    /**
     * Retorna 'BO' de Pedido de impugnação.
     *
     * @return PedidoImpugnacaoBO
     */
    private function getPedidoImpugnacaoBO(): PedidoImpugnacaoBO
    {
        if (empty($this->pedidoImpugnacaoBO)) {
            $this->pedidoImpugnacaoBO = app()->make(PedidoImpugnacaoBO::class);
        }
        return $this->pedidoImpugnacaoBO;
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
     * Retorna uma nova instância de 'MembroChapaBO'.
     *
     * @return MembroChapaBO|mixed
     */
    private function getMembroChapaBO()
    {
        if (empty($this->membroChapaBO)) {
            $this->membroChapaBO = app()->make(MembroChapaBO::class);
        }

        return $this->membroChapaBO;
    }

    /**
     * Retorna uma nova instância de 'CalendarioBO'.
     *
     * @return CalendarioBO|mixed
     */
    private function getCalendarioBO()
    {
        if (empty($this->calendarioBO)) {
            $this->calendarioBO = app()->make(CalendarioBO::class);
        }

        return $this->calendarioBO;
    }

    /**
     * Retorna uma nova instância de 'ArquivoDefesaImpugnacaoBO'.
     *
     * @return ArquivoDefesaImpugnacaoBO
     */
    private function getArquivoDefesaImpugnacaoBO()
    {
        if(empty($this->arquivoDefesaImpugnacaoBO)) {
            $this->arquivoDefesaImpugnacaoBO = app()->make(ArquivoDefesaImpugnacaoBO::class);
        }
        return $this->arquivoDefesaImpugnacaoBO;
    }

    /**
     * Retorna uma nova instância de 'ChapaEleicaoBO'.
     *
     * @retun ChapaEleicaoBO
     */
    private function getChapaEleicaoBO(): ChapaEleicaoBO
    {
        if(empty($this->chapaEleicaoBO)) {
            $this->chapaEleicaoBO = app()->make(ChapaEleicaoBO::class);
        }
        return $this->chapaEleicaoBO;
    }

    /**
     * Retorna uma nova instância de 'EleicaoBO'.
     *
     * @return EleicaoBO
     */
    private function getEleicaoBO(): EleicaoBO
    {
        if(empty($this->eleicaoBO)) {
            $this->eleicaoBO = app()->make(EleicaoBO::class);
        }
        return $this->eleicaoBO;
    }

    /**
     * Criar Entidade Defesa impugnação a partir do objeto 'DefesaImpugnacaoTO'.
     *
     * @param DefesaImpugnacaoTO $defesaTo
     * @return DefesaImpugnacao
     */
    private function entityFromDefesaImpugnacaoTO(DefesaImpugnacaoTO $defesaTo): DefesaImpugnacao
    {
        return DefesaImpugnacao::newInstance(
            [
                'id' => $defesaTo->getId(),
                'descricao' => $defesaTo->getDescricao(),
                'pedidoImpugnacao' => ['id' => $defesaTo->getIdPedidoImpugnacao()],
                'arquivos' => $defesaTo->getArquivos()
            ]
        );
    }

    /**
     * Criar 'DefesaImpugnaçaoTO' a partir do objeto 'DefesaImpugnacao'.
     *
     * @param DefesaImpugnacao $defesaImpugnacao
     * @return DefesaImpugnacaoTO
     */
    private function defesaImpugnacaoTOFromEntity(DefesaImpugnacao $defesaImpugnacao): DefesaImpugnacaoTO
    {
        return DefesaImpugnacaoTO::newInstance(
            [
                'id' => $defesaImpugnacao->getId(),
                'descricao' => $defesaImpugnacao->getDescricao(),
                'idPedidoImpugnacao' => $defesaImpugnacao->getPedidoImpugnacao()->getId(),
                'arquivos' => $defesaImpugnacao->getArquivos(),
                'idStatusPedidoImpugnacao' => $defesaImpugnacao->getPedidoImpugnacao()->getStatusPedidoImpugnacao()->getId(),
                'descricaoStatusPedidoImpugnacao' => $defesaImpugnacao->getPedidoImpugnacao()->getStatusPedidoImpugnacao()->getDescricao()
            ]
        );
    }
}
