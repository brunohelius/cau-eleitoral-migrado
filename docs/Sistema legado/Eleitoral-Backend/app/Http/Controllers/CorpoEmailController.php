<?php
/*
 * CorpoEmailController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\CorpoEmailBO;
use App\To\CorpoEmailFiltroTO;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Support\Facades\Input;
use App\To\CorpoEmailTO;
use App\Entities\CorpoEmail;
use Illuminate\Http\Request;

/**
 * Classe de controle referente a entidade 'CorpoEmail'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class CorpoEmailController extends Controller
{

    /**
     * @var \App\Business\CorpoEmailBO
     */
    private $corpoEmailBO;

    /**
     * Construtor da Classe.
     */
    public function __construct()
    {
        $this->corpoEmailBO = app()->make(CorpoEmailBO::class);
    }

    /**
     * Retorna a lista de corpo de emails.
     *
     * @return string
     * @throws NonUniqueResultException
     *
     * @OA\Get(
     *     path="/corpoEmail",
     *     tags={"Corpo do Email"},
     *     summary="Corpos de E-mail ",
     *     description="Retorna os corpos de emails cadastrados.",
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
    public function getCorposEmail()
    {
        $emails = $this->corpoEmailBO->getCorposEmail();
        return $this->toJson($emails);
    }

    /**
     * Retorna a lista de emails de acordo com o 'id' informado.
     *
     * @param $id
     * @return string
     * @throws NonUniqueResultException
     *
     * @OA\Get(
     *     path="/corpoEmail/{id}",
     *     tags={"Corpo do Email", "Emails"},
     *     summary="Emails por ID",
     *     description="Retorna os emails de acordo com o id informado",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Corpo de E-mail",
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
        $email = $this->corpoEmailBO->getPorId($id);
        return $this->toJson($email);
    }

    /**
     * Retorna a lista de emails vinculados a atividade secundária informada.
     *
     * @param $id
     * @return string
     * @throws Exception
     *
     * @OA\Get(
     *     path="/atividadeSecundaria/{id}/emails",
     *     tags={"Atividade Secundária", "Emails"},
     *     summary="Emails de Atividade Secundária por ID",
     *     description="Retorna os emails vinculados a atividade secundárias de acordo com o id informado",
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
    public function getEmailsPorAtividadeSecundaria($id)
    {
        $emails = $this->corpoEmailBO->getEmailsPorAtividadeSecundaria($id);
        return $this->toJson($emails);
    }

    /**
     * Retorna a lista de emails de acordo com o filtro informado.
     *
     * @return string
     *
     * @OA\Get(
     *     path="/corpoEmail/filtro",
     *     tags={"Corpo do Email", "Emails"},
     *     summary="Corpos dos Emails de acordo com o filtro informado.",
     *     description="Retorna os emails de acordo com o filtro informado.",
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
    public function getCorposEmailPorFiltro()
    {
        $data = Input::all();
        $corpoEmailFiltroTO = CorpoEmailFiltroTO::newInstance($data);
        $emails = $this->corpoEmailBO->getCorposEmailPorFiltro($corpoEmailFiltroTO);
        return $this->toJson($emails);
    }

    /**
     * Salva Corpo de E-mail.
     *
     * @OA\Post(
     *     path="/corpoEmail",
     *     tags={"Corpo do Emaill", "salvar"},
     *     summary="Salvar Corpo de E-mail.",
     *     description="Salvar",
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
     * @return string
     * @throws Exception
     */
    public function salvar(){
        $data = Input::all();
        $corpoEmail = CorpoEmail::newInstance($data);
        $this->corpoEmailBO->salvar($corpoEmail);
        return $this->toJson($corpoEmail);
    }

}
