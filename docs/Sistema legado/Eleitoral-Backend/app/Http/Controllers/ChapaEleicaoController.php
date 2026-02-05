<?php
/*
 * ChapaEleicaoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\ChapaEleicaoBO;
use App\Business\EleicaoBO;
use App\Business\MembroChapaBO;
use App\Entities\ChapaEleicao;
use App\Entities\MembroChapa;
use App\Exceptions\NegocioException;
use App\To\ChapaEleicaoExtratoFiltroTO;
use App\To\ChapaEleicaoTO;
use App\To\ConfirmarChapaTO;
use App\To\MembroChapaSubstituicaoTO;
use App\To\StatusChapaEleicaoTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Mpdf\MpdfException;
use stdClass;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe de controle referente a entidade 'ChapaEleicao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class ChapaEleicaoController extends Controller
{

    /**
     * @var \App\Business\MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var \App\Business\ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->chapaEleicaoBO = app()->make(ChapaEleicaoBO::class);
        $this->membroChapaBO = app()->make(MembroChapaBO::class);
        $this->eleicaoBO = app()->make(EleicaoBO::class);

        ini_set('max_execution_time', 180);
        set_time_limit(0);
    }

    /**
     * Retorna a chapa eleição conforme o id informado.
     *
     * @param $id
     * @return string
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/chapas/{id}",
     *     tags={"Chapa Eleição"},
     *     summary="Dados da chapa da eleição",
     *     description="Retorna a chapa eleição conforme o id informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Chapa da Eleição",
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
        $resp = $this->chapaEleicaoBO->getPorId($id, false);
        return $this->toJson($resp);
    }

    /**
     * Retorna a chapa eleição com membros conforme o id informado.
     *
     * @param $id
     * @return string
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/chapas/{id}/comMembros",
     *     tags={"Chapa Eleição Membros"},
     *     summary="Dados da chapa da eleição e Membros",
     *     description="Retorna a chapa eleição com membros conforme o id informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Chapa da Eleição",
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
    public function getPorIdComMembros($id)
    {
        $resp = $this->chapaEleicaoBO->getPorId($id, true);
        return $this->toJson($resp);
    }

    /**
     * Retorna as chapas cadastradas dado um id de calendário organizadas por estado.
     *
     * @param $idCalendario
     * @return string
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/chapas/informacaoChapaEleicao/{idCalendario}",
     *     tags={"Busca Chapas Eleição"},
     *     summary="Retorna as chapas cadastradas dado um id de calendário organizadas por estado",
     *     description="Retorna as chapas cadastradas dado um id de calendário organizadas por estado",
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
    public function getChapasPorCalendario($idCalendario)
    {
        $resp = $this->chapaEleicaoBO->getChapasPorCalendario($idCalendario, true);
        return $this->toJson($resp);
    }

    /**
     * Retorna chapa da eleição que o ator logado por realizar substituição de membros.
     *
     * @return string
     *
     * @throws NegocioException
     * @OA\Get(
     *     path="/chapas/substituicao",
     *     tags={"Busca Chapa Eleição"},
     *     summary="Retorna chapa da eleição que o ator logado por realizar substituição de membros.",
     *     description="Retorna chapa da eleição que o ator logado por realizar substituição de membros.",
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
    public function getChapaParaSubstituicao()
    {
        $chapa = $this->chapaEleicaoBO->getChapaParaSubstituicao();
        return $this->toJson($chapa);
    }

    /**
     * Retorna o histórico referente ao 'id' do calendario informado.
     *
     * @param int $idCalendario
     * @return string
     *
     * @throws NegocioException
     * @OA\Get(
     *     path="/chapas/{idCalendario}/historico",
     *     tags={"Busca Historico Chapas Eleição"},
     *     summary="Retorna o histórico referente ao 'id' do calendario informado.",
     *     description="Retorna o histórico referente ao 'id' do calendario informado.",
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
    public function getHistorico(int $idCalendario)
    {
        $resp = $this->chapaEleicaoBO->getHistorico($idCalendario);
        return $this->toJson($resp);
    }

    /**
     * Recupera a eleição chapa vigente com o 'id' mais antigo.
     *
     * @return string
     * @throws Exception
     *
     * @OA\Get(
     *     path="/chapas/eleicaoVigente",
     *     tags={"Chapa Eleição Vigente"},
     *     summary="Recupera a eleição chapa vigente com o 'id' mais antigo.",
     *     description="Recupera a eleição chapa vigente com o 'id' mais antigo.",
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
    public function getEleicaoChapaVigente()
    {
        $eleicao = $this->chapaEleicaoBO->getEleicaoVigenteCadastroChapa();
        return $this->toJson($eleicao);
    }

    /**
     * Recupera a eleição da chapa
     *
     * @param $id
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/chapas/{id}/eleicao",
     *     tags={"Eleição da Chapa Eleição "},
     *     summary="Recupera a eleição da chapa",
     *     description="Recupera a eleição da chapa",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Chapa",
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
    public function getEleicaoPorChapa($id)
    {
        $eleicao = $this->eleicaoBO->getEleicaoPorChapaEleicao($id);
        return $this->toJson($eleicao);
    }

    /**
     * Recupera a chapa de eleição que o profissional logado seja o responsável por criação.
     *
     * @return string
     * @throws NegocioException
     *
     * @OA\Get(
     *     path="/chapas/cadastro",
     *     tags={"Chapa Eleição por Profissional"},
     *     summary="Recupera a chapa de eleição que o profissional logado seja o responsável por criação.",
     *     description="Recupera a chapa de eleição que o profissional logado seja o responsável por criação.",
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
    public function getPorProfissionalLogadoInclusao()
    {
        $chapaEleicao = $this->chapaEleicaoBO->getPorProfissionalInclusao();
        return $this->toJson($chapaEleicao);
    }

    /**
     * Retorna a chapa de eleição de acordo com o profissional logado, o mesmo deve ser um membro confirmado.
     *
     * @return string
     * @throws NegocioException
     *
     * @OA\Get(
     *     path="/chapas/acompanhar",
     *     tags={"Chapa Eleição por Profissional"},
     *     summary="Retorna a chapa de eleição de acordo com o profissional logado, o mesmo deve ser um membro confirmado.",
     *     description="Retorna a chapa de eleição de acordo com o profissional logado, o mesmo deve ser um membro confirmado.",
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
    public function getPorMembroProfissionalLogado()
    {
        $chapaEleicao = $this->chapaEleicaoBO->getPorProfissionalLogado();
        return $this->toJson($chapaEleicao);
    }

    /**
     * Retorna as chapas cadastradas dado um id de calendário e uf.
     *
     * @param integer $idCalendario
     * @param integer $idCauUf
     *
     * @return string
     *
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/chapas/informacaoChapaEleicao/{idCalendario}/cauUf/{idCauUf}",
     *     tags={"Busca Chapas CauUf"},
     *     summary="Retorna as chapas cadastradas dado um id de calendário e uf",
     *     description="Retorna as chapas cadastradas dado um id de calendário e uf",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCalendario",
     *         in="path",
     *         description="Id do Calendário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idCauUf",
     *         in="path",
     *         description="Id do UF",
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
    public function getChapasQuantidadeMembrosPorCalendarioCauUf(int $idCalendario, int $idCauUf)
    {
        $resp = $this->chapaEleicaoBO->getChapasQuantidadeMembrosPorCalendarioCauUf($idCalendario, $idCauUf);
        return $this->toJson($resp);
    }

    /**
     * Valida se o idCauUf do profissional é o mesmo da chapa eleição dado um id de chapa eleição e o id do usuário
     * logado.
     *
     * @param int $idChapaEleicao
     *
     * @return string
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/chapas/{idChapaEleicao}/validacaoCauUfConvidado",
     *     tags={"Valida CauUf Convidado Chapa"},
     *     summary="Valida se o idCauUf do profissional é o mesmo da chapa eleição dado um id de chapa eleição e o id do
     *     usuário logado.",
     *     description="Valida se o idCauUf do profissional é o mesmo da chapa eleição dado um id de chapa eleição e o
     *     id do usuário logado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapaEleicao",
     *         in="path",
     *         description="Id da Chapa Eleição",
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
    public function validarCauUfProfissionalConvidadoChapa(int $idChapaEleicao)
    {
        $this->chapaEleicaoBO->validarUfProfissionalConvidadoChapa($idChapaEleicao);
    }

    /**
     * Altera o status da Chapa Eleição dado um id chapa eleição.
     *
     * @param int $idChapaEleicao
     *
     * @return void
     * @throws Exception
     * @OA\Post(
     *     path="/chapas/{idChapaEleicao}/alterarStatus",
     *     tags={"Altera Status Chapa Eleição"},
     *     summary="Altera o status da Chapa Eleição dado um id chapa eleição.",
     *     description="Altera o status da Chapa Eleição dado um id chapa eleição.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapaEleicao",
     *         in="path",
     *         description="Id da Chapa Eleição",
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
    public function alterarStatus(int $idChapaEleicao)
    {
        $data = Input::all();
        $data['idChapaEleicao'] = $idChapaEleicao;

        $statusChapaEleicaoTO = StatusChapaEleicaoTO::newInstance($data);
        $this->chapaEleicaoBO->alterarStatus($statusChapaEleicaoTO);
    }

    /**
     * Salvar dados de Chapa Eleição.
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/chapas/salvar",
     *     tags={"Chapa Eleição"},
     *     summary="Salvar dados de Chapa Eleição.",
     *     description="Salvar dados de Chapa Eleição.",
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
    public function salvar()
    {
        $data = Input::all();
        $chapaEleicao = ChapaEleicao::newInstance($data);

        $chapaEleicaoSalvo = $this->chapaEleicaoBO->salvar($chapaEleicao, $this->getJustificativaAlteracao($data));
        return $this->toJson($chapaEleicaoSalvo);
    }

    /**
     * Alterar dados de Chapa Eleição.
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/chapas/alterarPlataforma",
     *     tags={"Chapa Eleição"},
     *     summary="Salvar dados de Chapa Eleição.",
     *     description="Alterar dados de Chapa Eleição.",
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
    public function alterarPlataforma()
    {
        $data = Input::all();
        $chapaEleicao = ChapaEleicao::newInstance($data);
        $alterarAposDataFim = null;
        if(!empty($data['alterarAposDataFim'])) {
            $alterarAposDataFim = $data['alterarAposDataFim'];
        }
        $chapaEleicaoSalvo = $this->chapaEleicaoBO->alterarPlataforma($chapaEleicao, $this->getJustificativaAlteracao($data), $alterarAposDataFim);
        return $this->toJson($chapaEleicaoSalvo);
    }

    /**
     * Salvar a etapa de inclusão de membros na inclusão de Chapa Eleição.
     *
     * @param $id
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/chapas/salvarMembros",
     *     tags={"Chapa Eleição"},
     *     summary="Salvar a etapa de inclusão de membros na inclusão de Chapa Eleição.",
     *     description="Salvar a etapa de inclusão de membros na inclusão de Chapa Eleição.",
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
    public function salvarMembros($id)
    {
        $data = Input::all();

        $listaMembrosChapaTO = $this->getListaMembrosChapaTO($data);

        $chapaEleicaoSalvo = $this->chapaEleicaoBO->salvarMembros($id, $listaMembrosChapaTO);
        return $this->toJson($chapaEleicaoSalvo);
    }

    /**
     * Confirma a criação da Chapa Eleição e salva a respospa da declaração.
     *
     *
     * @param $id
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/chapas/confirmarChapa",
     *     tags={"Chapa Eleição"},
     *     summary="Confirma a criação da Chapa Eleição e salva a respospa da declaração.",
     *     description="Confirma a criação da Chapa Eleição e salva a respospa da declaração.",
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
    public function confirmarChapa($id)
    {
        $data = Input::all();

        $confirmarChapaTO = ConfirmarChapaTO::newInstance($data);

        $chapaConfirmada = $this->chapaEleicaoBO->confirmarChapa($id, $confirmarChapaTO);
        return $this->toJson($chapaConfirmada);
    }

    /**
     * Atualiza Chapa Eleição forçando a validação dos membros e da chapa.
     *
     *
     * @param $id
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/chapas/atualizarChapa",
     *     tags={"Chapa Eleição"},
     *     summary="Atualiza Chapa Eleição forçando a validação dos membros e da chapa.",
     *     description="Atualiza Chapa Eleição forçando a validação dos membros e da chapa.",
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
    public function atualizarChapa($id)
    {
        $chapaAtualizada = $this->chapaEleicaoBO->atualizarChapa($id);
        return $this->toJson($chapaAtualizada);
    }

    /**
     * Atualiza o número da chapa.
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/chapas/salvarNumeroChapa",
     *     tags={"Chapa Eleição"},
     *     summary="Adiciona um número para a chapa.",
     *     description="Adiciona um número para a chapa.",
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
    public function salvarNumeroChapa()
    {
        $chapaEleicaoTO = ChapaEleicaoTO::newInstance(Input::all());
        $chapaEleicao = $this->chapaEleicaoBO->salvarNumeroChapa($chapaEleicaoTO);
        return $this->toJson($chapaEleicao);
    }

    /**
     * Inclui um membro na chapa a partir do ID do profissional
     *
     * @param $id
     *
     * @return string
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @OA\Post(
     *     path="/chapas/incluirMembro",
     *     tags={"Chapa Eleição"},
     *     summary="Inclui um membro na chapa a partir do ID do profissional.",
     *     description="Inclui um membro na chapa a partir do ID do profissional.",
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
    public function incluirMembro($id)
    {
        $data = Input::all();

        $dadosTO = $this->getDadosTOIncluirMembroChapa($data);

        $membroChapaSalvo = $this->membroChapaBO->incluirMembroChapa($id, $dadosTO);
        return $this->toJson($membroChapaSalvo);
    }

    /**
     * Exclui fisicamente uma Chapa da Eleição pelo Id.
     *
     * @param $id
     * @throws Exception
     *
     * @OA\Delete(
     *     path="/chapas/{id}/excluir",
     *     tags={"Chapa Eleição"},
     *     summary="Exclui uma Chapa da Eleição pelo Id.",
     *     description="Exclui uma Chapa da Eleição pelo Id.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Chapa Eleição",
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
        $this->chapaEleicaoBO->excluir($id);
    }

    /**
     * Exclui uma Chapa da Eleição pelo Id enviando Justificativa.
     *
     * @param int $idChapaEleicao
     *
     * @throws Exception
     * @OA\Post(
     *     path="/chapas/{idChapaEleicao}/excluirComJustificativa",
     *     tags={"Chapa Eleição Com Justificativa"},
     *     summary="Exclui uma Chapa da Eleição pelo Id enviando Justificativa.",
     *     description="Exclui uma Chapa da Eleição pelo Id enviando Justificativa.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapaEleicao",
     *         in="path",
     *         description="Id da Chapa Eleição",
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
    public function excluirComJustificativa(int $idChapaEleicao)
    {
        $data = Input::all();
        $this->chapaEleicaoBO->inativar($idChapaEleicao, $data);
    }

    /**
     * Retorna todas as UFs que possuem chapas vigentes
     *
     * @param int $idChapaEleicao
     *
     * @return string
     * @throws \Exception
     * @OA\Post(
     *     path="chapas/ufs",
     *     tags={"UFs de Chapa Eleição"},
     *     summary="Retorna todas as UFs que possuem chapas vigentes.",
     *     description="Retorna todas as UFs que possuem chapas vigentes.",
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
    public function getUfsDeChapas()
    {
        $resp = $this->chapaEleicaoBO->getUfsDeChapas();
        return $this->toJson((array) $resp);
    }

    /**
     * Retornando as chapas ativas por id cau uf
     *
     * @param int $idCauUf
     *
     * @OA\Get(
     *     path="chapas/cauUf/{idCauUf}",
     *     tags={"Chapa Eleição por UF"},
     *     summary="Retorna todas as Chapas vigentes pela Cau UFs.",
     *     description="Retorna todas as Chapas vigentes pela Cau UFs.",
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
    public function getPorCauUf($idCauUf)
    {
        $resp = $this->chapaEleicaoBO->getPorCauUf($idCauUf);
        return $this->toJson($resp);
    }

    /**
     * Gerar documento PDF do extrato de quantidades de chapa da eleição.
     *
     * @param int $idCalendario
     * @return Response
     * @throws NegocioException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @OA\GET(
     *     path="/chapas/{idCalendario}/gerarPDFExtratoQuantidadeChapa",
     *     tags={"Extrato Chapa Eleição"},
     *     summary="Documento de extrato de chapa eleição que indica a quantidade de chapas por estados.",
     *     description="Download de extrato de quantidade chapas por UF da Eleição.",
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
    public function gerarDocumentoPDFExtratoQuantidadeChapa(int $idCalendario) {
        $documento = $this->chapaEleicaoBO->gerarDocumentoPDFExtratoQuantidadeChapa($idCalendario);
        return $this->toFile($documento);
    }

    /**
     * Gerar documento XML das chapas
     * @param int $idCalendario
     * @param int $statusChapaJulgamentoFinal
     * @return Response
     * @throws NegocioException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @OA\GET(
     *     path="/chapas/{idCalendario}/gerarXMLChapas",
     *     tags={"Extrato Chapa Eleição"},
     *     summary="Documento XML das chapas eleição ",
     *     description="Download do XML das chapas .",
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
    public function gerarXMLChapas(int $idCalendario, int $statusChapaJulgamentoFinal) {
        $documento = $this->chapaEleicaoBO->gerarXMLChapas($idCalendario, $statusChapaJulgamentoFinal);
        return $this->toFile($documento);
    }
    /**
     * link fotos membros
     * @param int $idMembro
     * @return Response
     * @throws NegocioException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @OA\GET(
     *     path="/membros/download-foto/{idMembro}",
     *     tags={"download foto membro para exportação da chapa"},
     *     summary="download foto membro para exportação da chapa ",
     *     description="download foto membro para exportação da chapa",
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
    public function downloadFotoMembro(int $idMembro) {
        $documento = $this->chapaEleicaoBO->downloadFotoMembro($idMembro);
        return $this->toFile($documento);
    }



    /**
     * Gerar documento CSV das chapas
     * @param int $idCalendario
     * @param int $statusChapaJulgamentoFinal
     * @return Response
     * @throws NegocioException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @OA\GET(
     *     path="/chapas/{idCalendario}/gerarCSVChapas",
     *     tags={"Exportar CSV Chapa Eleição"},
     *     summary="Documento CSV das chapas eleição ",
     *     description="Download do CSV das chapas .",
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
    public function gerarCSVChapas(int $idCalendario, int $statusChapaJulgamentoFinal) {
        $documento = $this->chapaEleicaoBO->gerarCSVChapas($idCalendario, $statusChapaJulgamentoFinal);
        return $this->toFile($documento);
    }

    /**
     * Gerar documento CSV das chapas Por Uf"
     *
     * @param int $idCalendario
     * @param int $idCauUf
     * @return Response
     * @throws NegocioException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @OA\GET(
     *     path="/chapas/{idCalendario}/gerarCSVChapasPorUf/{idCauUf}",
     *     tags={"Exportar CSV Chapa Eleição Por Uf"},
     *     summary="Documento CSV das chapas eleição Por Uf",
     *     description="Download do CSV das chapas Por Uf",
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
    public function gerarCSVChapasPorUf(int $idCalendario, int $idCauUf) {
        $documento = $this->chapaEleicaoBO->gerarCSVChapasPorUf($idCalendario, $idCauUf);
        return $this->toFile($documento);
    }

    /**
     * @param int $idCalendario
     * @return Response
     *
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @OA\POST(
     *     path="/chapas/{idCalendario}/gerarDocumentoPDFExtratoChapa",
     *     tags={"Extrato Chapa Eleição"},
     *     summary="Documento de extrato de chapa eleição que lista todos os membros da chapa eleitoral.",
     *     description="Download de estrato da chapa eleitoral.",
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
    public function gerarDocumentoPDFExtratoChapa(int $idCalendario) {

        set_time_limit(0);

        $data = Input::all();
        $data['idCalendario'] = $idCalendario;
        $chapaEleicaoExtratoFiltroTO = ChapaEleicaoExtratoFiltroTO::newInstance($data);

        return $this->chapaEleicaoBO->gerarDocumentoPDFExtratoChapa($chapaEleicaoExtratoFiltroTO);
       // return $this->toFile($arquivoTO);
    }

    /**
     * Gera o arquivo Json com os dados do Extrato da Chapa.
     *
     * @return Response
     *
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\POST(
     *     path="/chapas/gerarDadosExtratoChapaJson",
     *     tags={"Extrato Chapa Eleição"},
     *     summary="Gera o arquivo Json com os dados do Extrato da Chapa.",
     *     description="Gera o arquivo Json com os dados do Extrato da Chapa.",
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
    public function gerarDadosExtratoChapaJson() {
        $chapaEleicaoExtratoFiltroTO = ChapaEleicaoExtratoFiltroTO::newInstance(Input::all());
        $this->chapaEleicaoBO->gerarDadosExtratoChapaJson($chapaEleicaoExtratoFiltroTO);
    }

    /**
     * Retorna os dados do json das chapas por UF para o Extrato Navegavel
     *
     * @param int $idCauUf
     * @return Response
     *
     * @throws Exception
     * @OA\GET(
     *     path="/chapas/dadosExtratoChapaJson/{idCauUf}",
     *     tags={"Extrato Chapa Eleição"},
     *     summary="Retorna os dados do json das chapas por UF para o Extrato Navegavel.",
     *     description="Retorna os dados do json das chapas por UF para o Extrato Navegavel.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCauUf",
     *         in="path",
     *         description="Id do UF",
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
    public function getDadosExtratoChapaJson(int $idCauUf) {
        $documento = $this->chapaEleicaoBO->getDadosExtratoChapaJson($idCauUf);

        return $this->jsonContents($documento);
    }

    /**
     * @param int $idCalendario
     * @return string
     *
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\POST(
     *     path="/chapas/{idCalendario}/dadosParaExtratoChapa",
     *     tags={"Extrato Chapa Eleição"},
     *     summary="Retorna os dados para exibição do extrato navegável.",
     *     description="Dados para montagem de extrato navegável.",
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
    public function getDadosParaExtratoChapa(int $idCalendario) {
        $data = Input::all();
        $data['idCalendario'] = $idCalendario;
        $chapaEleicaoExtratoFiltroTO = ChapaEleicaoExtratoFiltroTO::newInstance($data);

        $dadosExtrato = $this->chapaEleicaoBO->getChapasParaExtratoPorCalendarioCauUf($chapaEleicaoExtratoFiltroTO);
        $dadosExtrato['quantidadeChapas'] = $this->chapaEleicaoBO->getChapasQuantidadeMembrosPorCalendarioCauUf($idCalendario, $chapaEleicaoExtratoFiltroTO->getIdCauUf());
        $dadosExtrato['idCauUf'] = $chapaEleicaoExtratoFiltroTO->getIdCauUf();
        $dadosExtrato['idCalendario'] = $idCalendario;
        return $this->toJson($dadosExtrato);
    }

    /**
     * Retorna as chapas cadastradas dado um id de calendário e uf.
     *
     * @param integer $id
     *
     * @return string
     *
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Post(
     *     path="/chapas/{id}/buscaSubstituto",
     *     tags={"Busca Chapas CauUf"},
     *     summary="Retorna as chapas cadastradas dado um id de calendário e uf",
     *     description="Retorna as chapas cadastradas dado um id de calendário e uf",
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
    public function buscaSubstituto(int $id)
    {
        $data = Input::all();

        $membroChapaSubstotuicaoTO = MembroChapaSubstituicaoTO::newInstance($data);

        $resp = $this->membroChapaBO->consultarMembroParaSubstituto($id, $membroChapaSubstotuicaoTO);
        return $this->toJson($resp);
    }

    /**
     * Retorna as quantidades de chapas cadastradas dado um id de calendário organizadas por estado.
     *
     * @param $idCalendario
     * @return string
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/chapas/quantidades/{idCalendario}",
     *     tags={"Chapas Eleição"},
     *     summary="Retorna as quantidades de chapas cadastradas dado um id de calendário organizadas por estado.",
     *     description="Retorna as quantidades de chapas cadastradas dado um id de calendário organizadas por estado.",
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
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo
     *     que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da
     *     requisição."), security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getQuantidadeChapasPorCalendario($idCalendario)
    {
        $resp = $this->chapaEleicaoBO->getChapasPorCalendario($idCalendario, false);
        return $this->toJson($resp);
    }

    /**
     * Retorna as quantidades de chapas cadastradas com verificação do membro comissão logado (CEN)
     *
     * @return string
     *
     * @OA\Get(
     *     path="/chapas/membroComissao/quantidades",
     *     tags={"Chapas Eleição"},
     *     summary="Retorna as quantidades de chapas cadastradas com verificação do membro comissão logado (CEN)",
     *     description="Retorna as quantidades de chapas cadastradas com verificação do membro comissão logado (CEN)",
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo
     *     que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da
     *     requisição."), security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getQuantidadeChapasPorMembroComissao()
    {
        $resp = $this->chapaEleicaoBO->getQuantidadeChapasPorMembroComissao();
        return $this->toJson($resp);
    }

    /**
     * Retorna as chapas cadastradas dado um id de calendário e uf.
     *
     * @param integer $idCalendario
     * @param integer $idCauUf
     *
     * @return string
     *
     * @OA\Get(
     *     path="/julgamentosFinais/chapas/calendario/{idCalendario}/cauUf/{idCauUf}",
     *     tags={"Chapas Eleição"},
     *     summary="Retorna as chapas cadastradas dado um id de calendário e uf",
     *     description="Retorna as chapas cadastradas dado um id de calendário e uf",
     *     @OA\Parameter(
     *         name="idCalendario",
     *         in="path",
     *         description="Id do Calendário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idCauUf",
     *         in="path",
     *         description="Id da CAU Uf",
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
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo
     *     que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da
     *     requisição."), security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getChapasEleicaoJulgamentoFinalPorCalendarioCauUf(int $idCalendario, int $idCauUf)
    {
        $resp = $this->chapaEleicaoBO->getChapasEleicaoJulgamentoFinalPorCalendarioCauUf($idCalendario, $idCauUf);
        return $this->toJson($resp);
    }

    /**
     * Retorna as chapas confirmadas wue possuem julgamento de acordo com o usuário autenticado.
     *
     * @param integer $idCauUf
     *
     * @return string
     *
     * @throws NegocioException
     * @OA\Get(
     *     path="/julgamentosFinais/chapas/membroComissao[/{idCauUf}]",
     *     tags={"Chapas Eleição"},
     *     summary="Retorna as chapas confirmadas de acordo com o usuário autenticado.",
     *     description="Retorna as chapas confirmadas de acordo com o usuário autenticado.",
     *     @OA\Parameter(
     *         name="idCauUf",
     *         in="path",
     *         description="Id da CAU Uf",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo
     *     que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da
     *     requisição."), security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getChapasEleicaoJulgamentoFinalPorMembroComissao(int $idCauUf = null)
    {
        $resp = $this->chapaEleicaoBO->getChapasEleicaoJulgamentoFinalPorMembroComissao($idCauUf);
        return $this->toJson($resp);
    }

    /**
     * Retorna a chapa eleição para julgamento final do responsável da chapa que está autenticado.
     *
     * @return string
     *
     * @OA\Get(
     *     path="/julgamentosFinais/chapaEleicao/responsavelChapa",
     *     tags={"Chapas Eleição"},
     *     summary="Retorna a chapa eleição para julgamento final do responsável da chapa que está autenticado",
     *     description="Retorna a chapa eleição para julgamento final do responsável da chapa que está autenticado",
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo
     *     que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da
     *     requisição."), security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getChapaEleicaoJulgamentoFinalPorResponsavelChapa()
    {
        $resp = $this->chapaEleicaoBO->getChapaEleicaoJulgamentoFinalPorResponsavelChapa();
        return $this->toJson($resp);
    }

    /**
     * Retorna a chapa eleição para julgamento final de acordo com id da chapa da eleição.
     *
     * @return string
     *
     * @OA\Get(
     *     path="/julgamentosFinais/chapaEleicao/{idChapa}",
     *     tags={"Chapas Eleição"},
     *     summary="Retorna a chapa eleição para julgamento final de acordo com id da chapa da eleição",
     *     description="Retorna a chapa eleição para julgamento final de acordo com id da chapa da eleição",
     *     @OA\Parameter(
     *         name="idChapa",
     *         in="path",
     *         description="Id da Chapa",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo
     *     que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da
     *     requisição."), security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getChapaEleicaoJulgamentoFinalPorIdChapa($idChapa)
    {
        $resp = $this->chapaEleicaoBO->getChapaEleicaoJulgamentoFinalPorIdChapa($idChapa);
        return $this->toJson($resp);
    }

    /**
     * Retorna a chapa eleição para julgamento final de acordo com id da chapa da eleição.
     *
     * @return string
     *
     * @OA\Get(
     *     path="/julgamentosFinais/chapaEleicao/membroComissao/{idChapa}",
     *     tags={"Chapas Eleição"},
     *     summary="Retorna a chapa eleição para julgamento final de acordo com id da chapa da eleição",
     *     description="Retorna a chapa eleição para julgamento final de acordo com id da chapa da eleição",
     *     @OA\Parameter(
     *         name="idChapa",
     *         in="path",
     *         description="Id da Chapa",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo
     *     que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da
     *     requisição."), security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getChapaJulgFinalVerificacaoMembroComissaoPorIdChapa($idChapa)
    {
        $resp = $this->chapaEleicaoBO->getChapaJulgFinalVerificacaoMembroComissaoPorIdChapa($idChapa);
        return $this->toJson($resp);
    }

    /**
     * Retorna todos os pedidos (substituição, impugnação e denúncia) de uma chapa eleição
     *
     * @return string
     *
     * @OA\Get(
     *     path="/chapas/{idChapa}/pedidosSolicitados",
     *     tags={"Chapas Eleição"},
     *     summary="Retorna todos os pedidos (substituição, impugnação e denúncia) de uma chapa eleição",
     *     description="Retorna todos os pedidos (substituição, impugnação e denúncia) de uma chapa eleição",
     *     @OA\Parameter(
     *         name="idChapa",
     *         in="path",
     *         description="Id da Chapa",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo
     *     que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da
     *     requisição."), security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getPedidosSolicitadosPorIdChapa($idChapa)
    {
        $resp = $this->chapaEleicaoBO->getPedidosSolicitadosPorChapa($idChapa);
        return $this->toJson($resp);
    }

    /**
     * Retorna a chapa eleição conforme o id informado.
     *
     * @param $id
     * @return string
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/chapas/retificacoesPlataforma/{idChapa}",
     *     tags={"Chapa Eleição"},
     *     summary="Dados da chapa da eleição",
     *     description="Retorna a chapa eleição conforme o id informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapa",
     *         in="path",
     *         description="Id da Chapa da Eleição",
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
    public function getRetificacoesPlataforma($idChapa)
    {
        $resp = $this->chapaEleicaoBO->getRetificacoesPlataforma($idChapa);
        return $this->toJson($resp);
    }

    /**
     * Retorna a chapa eleição conforme o id informado.
     *
     * @param $id
     * @return string
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/chapas/verificaRetificacoesPlataforma/{idChapa}",
     *     tags={"Chapa Eleição"},
     *     summary="Dados da chapa da eleição",
     *     description="Verifica se a chapa em questão possui retificações na Plataforma",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapa",
     *         in="path",
     *         description="Id da Chapa da Eleição",
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
    public function verificaRetificacoesPlataforma($idChapa)
    {
        $resp = $this->chapaEleicaoBO->getRetificacoesPlataforma($idChapa);

        return $this->toJson((object)[
            'possuiRetificacao' => sizeof($resp) > 0
        ]);
    }

    /**
     * Retorna a plataforma e os meios de propraganda da chapa eleição conforme o id informado.
     *
     * @param $id
     * @return string
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/chapas/{idChapa}/infoPlataformaChapa",
     *     tags={"Chapa Eleição"},
     *     summary="Dados da chapa da eleição",
     *     description="Retorna a plataforma e os meios de propraganda da chapa eleição conforme o id informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapa",
     *         in="path",
     *         description="Id da Chapa da Eleição",
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
    public function getPlataformaAndMeiosPropaganda($idChapa)
    {
        $resp = $this->chapaEleicaoBO->getPlataformaAndMeiosPropaganda($idChapa);

        return $this->toJson($resp);
    }

    /**
     * Método Auxiliar do incluirMembroChapaPorCpf para organizar os dados para o método
     *
     * @param $data
     * @return stdClass
     */
    private function getDadosTOIncluirMembroChapa($data)
    {
        $dadosTO = new stdClass();
        $dadosTO->idProfissional = Utils::getValue('idProfissional', $data);
        $dadosTO->idTipoParticipacao = Utils::getValue('idTipoParticipacao', $data);
        $dadosTO->idTipoMembroChapa = Utils::getValue('idTipoMembroChapa', $data);
        $dadosTO->numeroOrdem = Utils::getValue('numeroOrdem', $data);
        $dadosTO->justificativa = Utils::getValue('justificativa', $data);

        return $dadosTO;
    }

    /**
     * Método Auxiliar do Salvar para organizar os dados para o método salvarMembros
     *
     * @param $data
     *
     * @return array
     * @throws Exception
     */
    private function getListaMembrosChapaTO($data)
    {
        $listaMembrosChapaTO = [];

        foreach ($data as $dataMembro) {
            array_push($listaMembrosChapaTO, MembroChapa::newInstance($dataMembro));
        }

        return $listaMembrosChapaTO;
    }

    /**
     * Retorna a justificativa de alteração para quando for o Acessor CEN
     *
     * @param $data
     * @return string|null
     */
    private function getJustificativaAlteracao($data)
    {
        return Utils::getValue('justificativa', $data);
    }

    public function gerarCSVChapasTrePorUf(int $idCalendario, int $idCauUf) {
        $documento = $this->chapaEleicaoBO->gerarCSVChapasTrePorUf($idCalendario, $idCauUf);
        return $this->toFile($documento);
    }

}
