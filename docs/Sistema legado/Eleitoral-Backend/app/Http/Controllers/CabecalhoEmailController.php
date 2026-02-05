<?php
/*
 * CabecalhoEmailController.php Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Support\Facades\Input;
use App\Business\CabecalhoEmailBO;
use OpenApi\Annotations as OA;
use App\Entities\CabecalhoEmail;
use App\To\CabecalhoEmailFiltroTO;
use Illuminate\Http\Request;

/**
 * * Classe de controle referente a entidade 'Cabeçalho de E-mail'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class CabecalhoEmailController extends Controller
{
    /**
     * @var CabecalhoEmailBO
     */
    private $cabecalhoEmailBO;

    /**
     * Construtor da Classe.
     */
    public function __construct()
    {
        $this->cabecalhoEmailBO = app()->make(CabecalhoEmailBO::class);
    }

    /**
     * Retorna Cabeçalho de E-mail por ID.
     *
     * @OA\Get(
     *     path="/cabecalhoEmail/{id}",
     *     tags={"cabecalho de e-mail"},
     *     summary="Buscar Cabeçalho de E-mail por ID.",
     *     description="Retorna Cabeçalho de E-mail.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Cabeçalho de E-mail",
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
     * @param integer $id
     * @return string
     * @throws Exception
     */
    public function getPorId($id)
    {
        $cabecalhoEmail = $this->cabecalhoEmailBO->getPorId($id);
        return $this->toJson($cabecalhoEmail);
    }

    /**
     * Salva o cabaçalho de e-mail informado.
     *
     * @OA\Post(
     *     path="/cabecalhoEmail",
     *     tags={"cabecalho de e-mail"},
     *     summary="Salvar cabeçalho de e-mail.",
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
     * @throws NegocioException
     */
    public function salvar()
    {
        $data = Input::all();
        $cabecalhoEmail = $this->cabecalhoEmailBO->salvar($this->getCabecalhoEmail($data));
        return $this->toJson($cabecalhoEmail);
    }

    /**
     * Retorna lista de UFs.
     * @OA\Get(
     *     path="/cabecalhoEmail/ufs",
     *     tags={"cabecalho de e-mail", "UF"},
     *     summary="Retorna lista de UFs.",
     *     description="Lista UFs",
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
    public function getUfs()
    {
        $ufs = $this->cabecalhoEmailBO->getUfs();
        return $this->toJson($ufs);
    }

    /**
     * Método responsável por buscar dados de cabeçalho de e-mail.
     *
     * @OA\Post(
     *     path="/cabecalhoEmail/filtro",
     *     tags={"cabecalho de e-mail", "filtro"},
     *     summary="Buscar cabeçalho de e-mail.",
     *     description="buscar",
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
    public function getCabecalhoEmailPorFiltro()
    {
        $data = Input::all();
        $cabecalhoEmailFiltroTO = CabecalhoEmailFiltroTO::newInstance($data);
        $cabecalhos = $this->cabecalhoEmailBO->getPorFiltro($cabecalhoEmailFiltroTO);
        return $this->toJson($cabecalhos);
    }

    /**
     * Retorna quantidade total de E-mais vinculados ao cabeçalho.
     * @param integer $idCabecalhoEmail
     *
     * @OA\Get(
     *     path="/cabecalhoEmail/{idCabecalhoEmail}/emails/total",
     *     tags={"cabecalho de e-mail", "EmailAtividadeSecundaria"},
     *     summary="Retorna quantidade total de E-mais vinculados ao cabeçalho.",
     *     description="Total de cabecalhos vinculados a e-mail. ",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCabecalhoEmail",
     *         in="path",
     *         description="Id do Cabeçalho de E-mail",
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
     * @return string
     * @throws NonUniqueResultException
     */
    public function getTotalCorpoEmailVinculado($idCabecalhoEmail)
    {
        $total = $this->cabecalhoEmailBO->getTotalCorpoEmailVinculado($idCabecalhoEmail);
        return $this->toJson($total);
    }

    /**
     * Instancia entidade de Cabeçalho E-mail através de array.
     *
     * @param array $data
     * @return CabecalhoEmail
     */
    private function getCabecalhoEmail($data): CabecalhoEmail
    {
        return CabecalhoEmail::newInstance($data);
    }

}
