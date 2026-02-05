<?php
/*
 * AtividadeSecundariaCalendarioController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\AtividadeSecundariaCalendarioBO;
use App\Business\DeclaracaoAtividadeBO;
use App\Business\EmailAtividadeSecundariaTipoBO;
use App\Business\ProfissionalBO;
use App\Exceptions\NegocioException;
use App\To\DefinicaoDeclaracoesEmailsAtivSecundariaTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use stdClass;

/**
 * Classe de controle referente a entidade 'AtividadeSecundariaCalendario'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class AtividadeSecundariaCalendarioController extends Controller
{

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var DeclaracaoAtividadeBO
     */
    private $declaracaoAtividadeBO;

    /**
     * @var EmailAtividadeSecundariaTipoBO
     */
    private $emailAtividadeSecundariaTipoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->atividadeSecundariaCalendarioBO = app()->make(AtividadeSecundariaCalendarioBO::class);
    }

    /**
     * Recupera a atividade secundária do calendário pelo 'id' informado.
     *
     * @param $id
     * @return string
     * @throws NonUniqueResultException
     *
     * @OA\Get(
     *     path="/atividadeSecundaria/{id}",
     *     tags={"Atividade Secundária", "Comissão Eleitoral"},
     *     summary="Atividade Secundário por ID",
     *     description="Retorna as atividades secundárias de acordo com o id informado",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Atividade",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getPorId($id)
    {
        $atividadeSecundaria = $this->atividadeSecundariaCalendarioBO->getPorId($id);
        return $this->toJson($atividadeSecundaria);
    }

    /**
     * Salva a definição de e-mails e declarações.
     *
     * @return string
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @OA\Post(
     *     path="/atividadeSecundaria/definir-declaracoes-emails",
     *     tags={"Atividade Secundária"},
     *     summary="Configuração de E-mails e Declarações",
     *     description="Retorna os e-mails e declarações definidos",
     *     security={{ "Authorization": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function definirEmailsDeclaracoesPorAtividadeSecundaria()
    {
        $data = Input::all();

        $definicaoEmailsDeclaracoesTO = DefinicaoDeclaracoesEmailsAtivSecundariaTO::newInstance($data);
        $definicaoTOSalva = $this->atividadeSecundariaCalendarioBO->definirDeclaracoesEmailsPorAtividadeSecundaria(
            $definicaoEmailsDeclaracoesTO
        );

        return $this->toJson($definicaoTOSalva);
    }

    /**
     * Retorna as ufs selecionadas por atividade secundaria.
     *
     * @param $idAtividadeSecundaria
     *
     * @return string
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @OA\Get(
     *     path="/atividadeSecundaria/{idAtividadeSecundaria}/ufs-calendarios",
     *     tags={"ufs"},
     *     summary="Ufs dos calendários",
     *     description="Retorna distintamente todos as ufs dos calendários",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idAtividadeSecundaria",
     *         in="path",
     *         description="Id da Atividade Secundária",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getUfsCalendariosPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        $resp = $this->atividadeSecundariaCalendarioBO->getUfsCalendariosPorAtividadeSecundaria($idAtividadeSecundaria);
        return $this->toJson($resp);
    }

    /**
     * Retorna todos os profissionais conforme o 'CPF ou Nome' informado.
     *
     * @return string
     * @throws Exception
     *
     * @OA\Get(
     *     path="/atividadeSecundaria/profissionaisPorCpfNome",
     *     tags={"Atividade Secundária"},
     *     summary="Lista de profissionais",
     *     description="Retorna todos os profissionais conforme o 'CPF ou Nome' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getProfissionaisPorCpfNome()
    {
        $data = Input::all();
        $profissionalTO = $this->getFiltroProfissional($data);
        $profissionais = $this->getProfissionalBO()->getProfissionaisPorFiltro($profissionalTO, 50);

        return $this->toJson($profissionais);
    }

    /**
     * Recupera total de declarações respondidas na criação de chapas pelo id de uma atividade secundária.
     *
     * @param $id
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/atividadesSecundarias/{id}/total-resposta-declaraoes",
     *     tags={"Atividade Secundária", "Comissão Eleitoral"},
     *     summary="Total de Declarações Respondidas por ID da Atividade Secundário",
     *     description="Recupera total de declarações respondidas na criação de chapas pelo id de uma atividade secundária.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Atividade Secundária",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getQuantidadeRespostaDeclaracaoPorAtividade($id)
    {
        $quantidade = $this->atividadeSecundariaCalendarioBO->getQuantidadeRespostaDeclaracaoPorAtividade($id);
        return $this->toJson($quantidade);
    }

    /**
     * Recupera as declarações de atividade definidas para uma atividade secundária do calendário pelo 'id' informado.
     *
     * @param $id
     * @param $idTipoDeclaracao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/atividadesSecundarias/{id}/declaracao-definida-por-tipo/{idTipoDeclaracao}",
     *     tags={"Atividade Secundária", "Comissão Eleitoral"},
     *     summary="Declarações definidas por ID da Atividade Secundário ",
     *     description="Recupera as declarações de atividade definidas para uma atividade secundária do calendário pelo 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Atividade Secundária",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idTipoDeclaracao",
     *         in="path",
     *         description="Id do Tipo de Declaração",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getDeclaracaoPorAtividadeSecundariaTipo($id, $idTipoDeclaracao)
    {
        $declaracao = $this->getDeclaracaoAtividadeBO()->getDeclaracaoPorAtividadeSecundariaTipo($id, $idTipoDeclaracao);
        return $this->toJson($declaracao);
    }

    /**
     * Recupera as declarações de atividade definidas para uma atividade secundária do calendário pelo 'id' informado.
     *
     * @param $id
     * @return string
     * @throws NegocioException
     * @OA\Get(
     *     path="/atividadesSecundarias/{id}/declaracoes-atividade",
     *     tags={"Atividade Secundária", "Declarações"},
     *     summary="Declarações definidas por ID da Atividade Secundário ",
     *     description="Recupera as declarações de atividade definidas para uma atividade secundária.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Atividade Secundária",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getDeclaracoesPorAtividadeSecundaria($id)
    {
        $declaracoesAtividade = $this->getDeclaracaoAtividadeBO()->getDeclaracoesAtividadePorAtividadeSecundaria(
            $id, true
        );

        return $this->toJson($declaracoesAtividade);
    }

    /**
     * Retorna a quantidade de atividades que tem relação com a declaração informada.
     *
     * @param $idDeclaracao
     *
     * @return string
     *
     * @OA\Get(
     *     path="/membroComissao/membrosDeclaracao/{idDeclaracao}",
     *     tags={"Membros Comissão"},
     *     summary="Retorna a quantidade de atividades que tem relação com a declaração informada.",
     *     description="Retorna a quantidade de atividades que tem relação com a declaração informada.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idDeclaracao",
     *         in="path",
     *         description="Id da Declaração",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getCountAtividadesPorDeclaracao($idDeclaracao)
    {
        $resp = $this->getDeclaracaoAtividadeBO()->getQtdDeclaracoesDefinidasPorDeclaracao($idDeclaracao);
        return $this->toJson($resp);
    }

    /**
     * Retorna as informações para definição/parametrização de e-mails de acordo com o id da atividade secundária
     *
     * @param $id
     * @return string
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @OA\Get(
     *     path="/atividadesSecundarias/{id}/definicao-emails",
     *     tags={"Informação definição e-mails"},
     *     summary="Retorna as informações para definição/parametrização de e-mails de acordo com o id da atividade secundária",
     *     description="Retorna as informações para definição/parametrização de e-mails de acordo com o id da atividade secundária",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Atividade Secundária",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getInformacaoDefinicaoEmails($id)
    {
        $resp = $this->atividadeSecundariaCalendarioBO->getInformacoesDefinicoesEmail($id);
        return $this->toJson($resp);
    }

    /**
     * Retorna as informações para definição/parametrização de declarações de acordo com o id da atividade secundária
     *
     * @param $id
     * @return string
     *
     * @throws NonUniqueResultException
     * @throws NegocioException
     * @OA\Get(
     *     path="/atividadesSecundarias/{id}/definicao-declaracoes",
     *     tags={"Informação definição declarações"},
     *     summary="Retorna as informações para definição/parametrização de declarações de acordo com o id da atividade secundária",
     *     description="Retorna as informações para definição/parametrização de declarações de acordo com o id da atividade secundária",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Atividade Secundária",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getInformacaoDefinicaoDeclaracoes($id)
    {
        $resp = $this->atividadeSecundariaCalendarioBO->getInformacoesDefinicoesDeclaracao($id);
        return $this->toJson($resp);
    }

    /**
     * Retorna uma lista de Atividades Secundárias até calendário e eleição dado 2 níveis de busca
     * e Para eleicoes que estejam vigentes e ativas.
     *
     * @param int $nivelPrincipal
     * @param int $nivelSecundaria
     *
     * @return array|null
     *
     * @throws NonUniqueResultException
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="atividadesSecundarias/nivelPrincipal/{nivelPrincipal}/nivelSecundaria/{nivelSecundaria}",
     *     tags={"Atividade Secundaria", "calendario", "eleicao", "nivel"},
     *     summary="Retorna uma lista de Atividades Secundárias até calendário e eleição dado 2 níveis de busca e Para eleicoes que estejam vigentes e ativas",
     *     description="Retorna uma lista de Atividades Secundárias até calendário e eleição dado 2 níveis de busca e Para eleicoes que estejam vigentes e ativas",
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição."),
     *     security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getAtividadeSecundariaAtivaPorNivel($nivelPrincipal, $nivelSecundaria)
    {
        $resp = $this->atividadeSecundariaCalendarioBO->getAtividadeSecundariaAtivaPorNivel($nivelPrincipal, $nivelSecundaria);
        return $this->toJson($resp);
    }

    /**
     * Retorna o filtro de pesquisa conforme os parâmetros informados na requisição.
     *
     * @param array $data
     * @return stdClass
     */
    private function getFiltroProfissional($data)
    {
        $profissionalTO = new stdClass();
        $profissionalTO->cpfNome = Utils::getValue('cpfNome', $data);
        $profissionalTO->registroNome = Utils::getValue('registroNome', $data);

        return $profissionalTO;
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
     * Retorna uma nova instância de 'EmailAtividadeSecundariaTipoBO'.
     *
     * @return EmailAtividadeSecundariaTipoBO|mixed
     */
    private function getEmailAtividadeSecundariaTipoBO()
    {
        if (empty($this->emailAtividadeSecundariaTipoBO)) {
            $this->emailAtividadeSecundariaTipoBO = app()->make(EmailAtividadeSecundariaTipoBO::class);
        }

        return $this->emailAtividadeSecundariaTipoBO;
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
}
