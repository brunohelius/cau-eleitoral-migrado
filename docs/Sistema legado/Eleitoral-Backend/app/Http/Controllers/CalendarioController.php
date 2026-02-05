<?php
/*
 * CalendarioController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\AtividadePrincipalBO;
use App\Business\CalendarioBO;
use App\Business\PrazoCalendarioBO;
use App\Entities\Calendario;
use App\Entities\JustificativaAlteracaoCalendario;
use App\Exceptions\NegocioException;
use App\To\AtividadePrincipalFiltroTO;
use App\To\CalendarioFiltroTO;
use App\To\CalendarioPublicacaoComissaoEleitoralFiltroTO;
use App\To\CalendarioTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use stdClass;

/**
 * Classe de controle referente a entidade 'Calendario'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class CalendarioController extends Controller
{
    /**
     * @var CalendarioBO
     */
    private $calendariolBO;

    /**
     * @var \App\Business\AtividadePrincipalBO
     */
    private $atividadePrincipalBO;

    /**
     * @var \App\Business\PrazoCalendarioBO
     */
    private $prazoCalendarioBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->calendariolBO = app()->make(CalendarioBO::class);
        $this->atividadePrincipalBO = app()->make(AtividadePrincipalBO::class);
        $this->prazoCalendarioBO = app()->make(PrazoCalendarioBO::class);
    }

    /**
     * Retorna os anos que houveram calendários eleitorais
     * @return string
     *
     * @OA\Get(
     *     path="/calendarios/anos",
     *     tags={"anos"},
     *     summary="Anos dos calendários",
     *     description="Retorna distintamente todos os anos dos calendários",
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
    public function getAnos()
    {
        $resp = $this->calendariolBO->getAnos();
        return $this->toJson($resp);
    }

    /**
     * Retorna os anos que houveram calendários eleitorais por filtro
     * @return string
     *
     * @OA\Post(
     *     path="/calendarios/anos/filtro",
     *     tags={"anos"},
     *     summary="Anos dos calendários por Filtro",
     *     description="Retorna distintamente todos os anos dos calendários dado um determinado filtro",
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
    public function getAnosPorFiltro()
    {
        $data = Input::all();
        $filtroTO = $this->getFiltroCalendarios($data);
        $resp = $this->calendariolBO->getAnosPorFiltro($filtroTO);
        return $this->toJson($resp);
    }

    /**
     * Retorna lista de anos com eleições concluidas.
     *
     * @return string
     *
     * * @OA\Get(
     *     path="/calendarios/concluidos/anos",
     *     tags={"anos"},
     *     summary="Anos dos calendários Concluidos",
     *     description="Retorna distintamente todos os anos dos calendários com status igual a concluído",
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
    public function getCalendariosConcluidosAnos(){
        $resp = $this->calendariolBO->getCalendariosConcluidosAnos();
        return $this->toJson($resp);
    }

    /**
     * Retorna as eleicoes para todos os anos
     *
     * @return string
     *
     * @OA\Get(
     *     path="/calendarios/eleicoes",
     *     tags={"eleicoes"},
     *     summary="Eleições dos calendários",
     *     description="Retorna distintamente todas as eleições dos calendários",
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
    public function getEleicoes()
    {
        $resp = $this->calendariolBO->getEleicoes();
        return $this->toJson($resp);
    }

    /**
     * Retorna os tipos de processo
     *
     * @return string
     *
     * @OA\Get(
     *     path="/calendarios/tiposProcessos",
     *     tags={"tipoProcesso"},
     *     summary="Tipos de Processos dos calendários",
     *     description="Retorna todos os Tipos de Processos dos calendários",
     *     security={{ "Authorization": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json")
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getTipoProcesso()
    {
        $resp = $this->calendariolBO->getTipoProcesso();
        return $this->toJson($resp);
    }

    /**
     * Retorna o calendario conforme o id informado.
     *
     * @param $id
     * @return string
     * @throws NonUniqueResultException
     * @throws NegocioException
     *
     * @OA\Get(
     *     path="/calendarios/{id}",
     *     tags={"Calendários"},
     *     summary="Dados do calendário",
     *     description="Retorna o calendario conforme o id informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Calendário",
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
        $resp = $this->calendariolBO->getPorId($id);
        return $this->toJson($resp);
    }

    /**
     * Retorna o calendario conforme o id da atividade secundária informado.
     *
     * @param $idAtividadeSecundaria
     * @return string
     * @throws NonUniqueResultException
     * @throws NegocioException
     *
     * @OA\Get(
     *     path="/calendarios/atividadeSecundaria/{idAtividadeSecundaria}",
     *     tags={"Calendários"},
     *     summary="Dados do calendário",
     *     description="Retorna o calendario conforme o id da atividade secundária informado",
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
    public function getPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        $resp = $this->calendariolBO->getPorAtividadeSecundaria($idAtividadeSecundaria);
        return $this->toJson($resp);
    }

    /**
     * Retorna quantidade de calendários pela situação.
     *
     * @param integer $idSituacao
     * @return string
     * * @OA\Get(
     *     path="/calendarios/{idSituacao}/total",
     *     tags={"Calendários"},
     *     summary="Quantidade de calendários pela situação",
     *     description="Retorna o total de calendario conforme a situação informada.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idSituacao",
     *         in="path",
     *         description="Id da Situação",
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
     *
     * @throws Exception
     */
    public function getTotalCalendariosPorSituacao($idSituacao){
        $resp = $this->calendariolBO->getTotalCalendariosPorSituacao($idSituacao);
        return $this->toJson($resp);
    }

    /**
     * Retorna os Calendários conforme o filtro informado.
     *
     * @return string
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/calendarios/filtro",
     *     tags={"Calendários"},
     *     summary="Lista de calendários",
     *     description="Retorna uma lista de calendarios conforme os filtros informados.",
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
    public function getCalendariosPorFiltro()
    {
        $data = Input::all();
        $calendarioFiltroTO = $this->getFiltroCalendarios($data);
        $calendarios = $this->calendariolBO->getCalendariosPorFiltro($calendarioFiltroTO);
        return $this->toJson($calendarios);
    }

    /**
     * Retorna os Calendários conforme o filtro informado.
     *
     * @return string
     * @throws NegocioException
     * @OA\Get(
     *     path="/calendarios/membroComissao",
     *     tags={"Calendários"},
     *     summary="Lista de calendários",
     *     description="Retorna uma lista de calendarios conforme os filtros informados.",
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
    public function getPorMembroComissaoLogado()
    {
        $calendarios = $this->calendariolBO->getPorMembroComissaoLogado();
        return $this->toJson($calendarios);
    }

    /**
     * Disponibiliza o arquivo 'Resolução' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     * @return Response
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/calendarios/arquivo/{idArquivo}/download",
     *     tags={"Calendários", "arquivos"},
     *     summary="Download de Resolução",
     *     description="Disponibiliza o arquivo 'Resolução' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idArquivo",
     *         in="path",
     *         description="Id do Arquivo",
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
    public function download($idArquivo)
    {
        $arquivoTO = $this->calendariolBO->getArquivo($idArquivo);
        return $this->toFile($arquivoTO);
    }

    /**
     * Salva o dados de Calendario
     *
     * @param Request $request
     * @return string
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws NegocioException
     * @throws Exception
     *
     * @OA\Post(
     *     path="/calendarios/salvar",
     *     tags={"Calendários"},
     *     summary="Salvar dados de Calendário",
     *     description="Salva o dados de Calendario.",
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
    public function salvar(Request $request)
    {
        $data = Input::all();
        $calendario = Calendario::newInstance($data);
        $dadosTO = $this->getDadosTOSalvar($data);
        $calendarioSalvo = $this->calendariolBO->salvar($calendario, $dadosTO);
        return $this->toJson($calendarioSalvo);
    }

    /**
     * Exclui logicamente o Calendário pelo Id
     *
     * @param $id
     * @return string
     * @throws Exception
     *
     * @OA\Delete(
     *     path="/calendarios/{id}/excluir",
     *     tags={"Calendários"},
     *     summary="Exclusão de Calendário",
     *     description="Exclui logicamente o Calendário pelo Id.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Calendário",
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
    public function excluir($id)
    {
        $calendario = $this->calendariolBO->excluir($id);
        return $this->toJson($calendario);
    }

    /**
     * Retorna as atividades principais do calendario conforme o id informado.
     *
     * @param $idCalendario
     * @return string
     *
     * @OA\POST(
     *     path="/calendarios/atividadesPrincipais/{idCalendario}",
     *     tags={"Calendários", "Atividade Principal"},
     *     summary="Busca de Atividades Principais",
     *     description="Retorna as atividades principais do calendario conforme o id informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCalendario",
     *         in="path",
     *         description="Id do Calendário",
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
    public function getAtividadePrincipalPorCalendario($idCalendario)
    {
        $resp = $this->atividadePrincipalBO->getAtividadePrincipalPorCalendario($idCalendario);
        return $this->toJson($resp);
    }

    /**
     * Retorna as atividades principais do calendario conforme o id filtro informado.
     *
     * @param integer $idCalendario
     * @return string
     *
     * @OA\POST(
     *     path="/calendarios/{idCalendario}/atividadesPrincipais/filtro",
     *     tags={"Calendários", "Atividade Principal", "Filtro"},
     *     summary="Busca de Atividades Principais por filtro.",
     *     description="Retorna as atividades principais do calendario conforme o filtro informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCalendario",
     *         in="path",
     *         description="Id do Calendário",
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
    public function getAtividadePrincipalPorCalendarioComFiltro($idCalendario){
        $data = Input::all();
        $filtroTO = $this->getAtividadePrincipalFiltro($data);
        $atividade = $this->atividadePrincipalBO->getAtividadePrincipalPorCalendarioComFiltro($idCalendario, $filtroTO);
        return $this->toJson($atividade);
    }

    /**
     * Retorna Filtro de Atividade Principal
     *
     * @param array $data
     * @return AtividadePrincipalFiltroTO
     */
    public function getAtividadePrincipalFiltro($data = null){
        $filtroTO = new AtividadePrincipalFiltroTO();
        $filtroTO->setAtividadeSecundariaDataInicio(Utils::getValue('atividadeSecundariaDataInicio', $data));
        $filtroTO->setAtividadeSecundariaDataFim(Utils::getValue('atividadeSecundariaDataFim', $data));
        return $filtroTO;
    }

    /**
     * Retorna os Prazos do calendario conforme o id informado.
     *
     * @param $idCalendario
     * @return string
     *
     * @OA\Get(
     *     path="/calendarios/prazos/{idCalendario}",
     *     tags={"Calendários", "Prazos"},
     *     summary="Lista de Prazos de Calendários",
     *     description="Retorna os Prazos do calendario conforme o id do Calendário informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCalendario",
     *         in="path",
     *         description="Id do Calendário",
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
     * @throws NonUniqueResultException
     */
    public function getPrazosPorCalendario($idCalendario)
    {
        $resp = $this->prazoCalendarioBO->getPrazosPorCalendario($idCalendario);
        return $this->toJson($resp);
    }

    /**
     * Inativa o 'Calendario' na base de dados.
     *
     * @param Request $request
     * @return string
     * @throws Exception
     *
     * @OA\Post(
     *     path="/calendarios/inativar",
     *     tags={"Calendários"},
     *     summary="Inativação de Calendário",
     *     description="Inativa o 'Calendario' na base de dados.",
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
    public function inativar(Request $request)
    {
        $data = Input::all();
        $calendarioTO = CalendarioTO::newInstance($data);

        $result = $this->calendariolBO->inativar($calendarioTO, $request);
        return $this->toJson($result);
    }

    /**
     * Conclui o 'Calendario' na base de dados.
     *
     * @param Request $request
     * @return string
     * @throws Exception
     *
     * @OA\Post(
     *     path="/calendarios/concluir",
     *     tags={"Calendários"},
     *     summary="Conclusão de Calendário",
     *     description="Conclui o 'Calendario' na base de dados.",
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
    public function concluir(Request $request)
    {
        $data = Input::all();
        $calendarioTO = CalendarioTO::newInstance($data);

        $result = $this->calendariolBO->concluir($calendarioTO, $request);
        return $this->toJson($result);
    }

    /**
     * Salva os dados de Prazos de Calendário
     *
     * @param Request $request
     * @throws Exception
     *
     * @OA\Post(
     *     path="/calendarios/prazos/salvar",
     *     tags={"Calendários", "prazos"},
     *     summary="Salvar Prazos de Calendário",
     *     description="Salva os dados de Prazos de Calendário.",
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
    public function salvarPrazos(Request $request)
    {
        $data = Input::all();
        $calendario = Calendario::newInstance($data);
        $justificativas = $this->getJustificativasAlteracao($data);
        $prazosExcluidos = Utils::getValue('prazosExcluidos', $data);
        $this->prazoCalendarioBO->salvar($calendario, $justificativas, $prazosExcluidos);
    }

    /**
     * Recupera o histórico do calendário.
     *
     * @param $id
     * @return string
     *
     * @throws NegocioException
     * @OA\Get(
     *     path="/calendarios/{id}/historico",
     *     tags={"Calendários", "historico"},
     *     summary="Lista os registros de histórico de Calendários",
     *     description="Retorna os registros de histórico do calendario conforme o id do Calendário informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Calendário",
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
    public function getHistorico($id)
    {
        $historicos = $this->calendariolBO->getHistorico($id);
        return $this->toJson($historicos);
    }

    /**
     * Retorna a quantidade de membros cadastrados no conselho eleitoral para o calendário informado.
     *
     * @param $id
     * @return string
     * @throws NegocioException
     * @OA\Get(
     *     path="/calendarios/{id}/numeroMembros",
     *     tags={"Calendários", "numeroMembrosComissão"},
     *     summary="Retorna a quantidade de membros cadastrados no conselho eleitoral para o calendário informado.",
     *     description="Retorna a quantidade de membros cadastrados no conselho eleitoral para o calendário informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Calendário",
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
    public function getAgrupamentoNumeroMembros($id)
    {
        $numeroMembrosComissaoEleitoral = $this->calendariolBO->getAgrupamentoNumeroMembros($id);
        return $this->toJson($numeroMembrosComissaoEleitoral);
    }


    public function gerarDocumento()
    {
        $arquivoTO = $this->calendariolBO->gerarDocumento();
        return $this->toFile($arquivoTO);
    }

    /**
     * Recupera a lista de calendários disponíveis para serem publicados na comissão eleitoral.
     *
     * @return string
     * @throws NonUniqueResultException
     *
     * @OA\Get(
     *     path="/calendarios/comissaoEleitoral/publicacao",
     *     tags={"Calendários", "Publicação Comissão Eleitoral"},
     *     summary="Retorna os calendários disponíveis para a publicação da comissão eleitoral.",
     *     description="Retorna os calendários disponíveis para a realização da publicação da comissão eleitoral.",
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
    public function getCalendariosPublicacaoComissaoEleitoral()
    {
        $calendarios = $this->calendariolBO->getCalendariosPublicacaoComissaoEleitoral();
        return $this->toJson($calendarios);
    }

    /**
     * Recupera a lista de anos do calendário de publicação da comissão membro para o filtro.
     *
     * @return string
     * @throws NonUniqueResultException
     *
     * @OA\Get(
     *     path="/calendarios/comissaoEleitoral/publicacao/anos",
     *     tags={"Anos de Eleição de Calendario", "Publicação Comissão Eleitoral"},
     *     summary="Recupera a lista de anos do calendário de publicação da comissão membro para o filtro.",
     *     description="Recupera a lista de anos do calendário de publicação da comissão membro para o filtro.",
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
    public function getAnosCalendarioPublicacaoComissaoEleitoral()
    {
        $calendarios = $this->calendariolBO->getAnosCalendarioPublicacaoComissaoEleitoral();
        return $this->toJson($calendarios);
    }

    /**
     * Recupera a lista de calendário de publicação da comissão membro de acordo com o filtro informado.
     *
     * @return string
     * @throws NonUniqueResultException
     *
     * @OA\Get(
     *     path="/calendarios/comissaoEleitoral/publicacao/filtro",
     *     tags={"Calendários", "Publicação Comissão Eleitoral"},
     *     summary="Recupera a lista de calendário de publicação da comissão membro de acordo com o filtro informado.",
     *     description="Recupera a lista de calendário de publicação da comissão membro de acordo com o filtro informado.",
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
    public function getCalendarioPublicacaoComissaoEleitoralPorFiltro()
    {
        $data = Input::all();
        $calendarioPublicacaoFiltro = CalendarioPublicacaoComissaoEleitoralFiltroTO::newInstance($data);

        $calendarios = $this->calendariolBO->getCalendarioPublicacaoComissaoEleitoralPorFiltro(
            $calendarioPublicacaoFiltro
        );

        return $this->toJson($calendarios);
    }

    /**
     * Retorna o calendário/eleição conforme o id informado.
     *
     * @param integer $id
     * @return string
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/eleicoes/{id}",
     *     tags={"eleicoes"},
     *     summary="Dados do Calendário/Eleição",
     *     description="Retorna o calendário/eleição conforme o id informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Eleição",
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
    public function getCalendarioEleicaoPorId($id)
    {
        $data = ["eleicoes" => array($id)];
        $calendarioFiltroTO = CalendarioFiltroTO::newInstance($data);
        $calendarios = $this->calendariolBO->getCalendariosPorFiltro($calendarioFiltroTO);

        return $this->toJson($calendarios[0]);
    }

    /**
     * Verifica a quantidade de calendarios com chapa.
     *
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/calendario/validacao/chapas",
     *     tags={"Verifica Quantidade Calendários Chapa"},
     *     summary="Verifica a quantidade de calendarios com chapa.",
     *     description="Verifica a quantidade de calendarios com chapa.",
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
    public function validarQuantidadeCalendariosComChapa()
    {
        $this->calendariolBO->validarQuantidadeCalendariosComChapa();
    }

    /**
     * Retorna o filtro de pesquisa conforme os parâmetros informados na requisição.
     *
     * @param array $data
     * @return CalendarioFiltroTO
     */
    private function getFiltroCalendarios($data)
    {
        $filtroTO = new CalendarioFiltroTO();
        $filtroTO->setIdTipoProcesso(Utils::getValue('idTipoProcesso', $data));
        $filtroTO->setAnos(Utils::getValue('anos', $data));
        if (!empty(Utils::getValue('eleicoes', $data))) {
            $filtroTO->setIdsCalendariosEleicao(Utils::getValue('eleicoes', $data));
        } else {
            $filtroTO->setIdsCalendariosEleicao(Utils::getValue('idsCalendariosEleicao', $data));
        }
        $filtroTO->setSituacoes(Utils::getValue('situacoes', $data));

        $listaChapas = Utils::getValue('listaChapas', $data);
        if (!empty($listaChapas)) {
            $filtroTO->setListaChapas($listaChapas);
        }

        $listaPedidosSubstituicaoChapa = Utils::getValue('listaPedidosSubstituicaoChapa', $data);
        if (!empty($listaPedidosSubstituicaoChapa)) {
            $filtroTO->setListaPedidosSubstituicaoChapa($listaPedidosSubstituicaoChapa);
        }

        $listaPedidosImpugnacao = Utils::getValue('listaPedidosImpugnacao', $data);
        if (!empty($listaPedidosImpugnacao)) {
            $filtroTO->setListaPedidosImpugnacao($listaPedidosImpugnacao);
        }

        $listaPedidosImpugnacaoResultado = Utils::getValue('listaPedidosImpugnacaoResultado', $data);
        if (!empty($listaPedidosImpugnacaoResultado)) {
            $filtroTO->setListaPedidosImpugnacaoResultado($listaPedidosImpugnacaoResultado);
        }

        $listaDenuncias = Utils::getValue('listaDenuncias', $data);
        if(!empty($listaDenuncias)) {
            $filtroTO->setListaDenuncias($listaDenuncias);
        }

        return $filtroTO;
    }

    /**
     * Retorna um array de Objetos JustificativaAlteracaoCalendario para salvar no histórico de
     * Alteração do Calendário
     *
     * @param $data
     * @return array
     */
    private function getJustificativasAlteracao($data)
    {
        $justificativas = Utils::getValue('justificativa', $data);

        $justificativaTO = [];
        if(!empty($justificativas)){
            foreach ($justificativas as $justificativa){
                $justificativaTO[] = JustificativaAlteracaoCalendario::newInstance($justificativa);
            }
        }
        return $justificativaTO;
    }

    /**
     * Método Auxiliar do Salvar para organizar os dados para o método
     *
     * @param $data
     * @return stdClass
     */
    private function getDadosTOSalvar($data)
    {
        $dadosTO = new stdClass();
        $dadosTO->justificativas = $this->getJustificativasAlteracao($data);
        $dadosTO->arquivosExcluidos = Utils::getValue('arquivosExcluidos', $data);
        $dadosTO->isCalendarioReplicado = (boolean) Utils::getValue('isCalendarioReplicado', $data);
        $dadosTO->atividadesPrincipaisExcluidas = Utils::getValue('atividadesPrincipaisExcluidas', $data);
        $dadosTO->subAtividadesPrincipaisExcluidas = Utils::getValue('subAtividadesPrincipaisExcluidas', $data);

        return $dadosTO;
    }
}
