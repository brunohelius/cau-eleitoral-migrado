<?php
/*
 * ChapaEleicaoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use Exception;
use App\Business\MembroChapaBO;
use App\Exceptions\NegocioException;
use App\To\MembroChapaSubstituicaoTO;
use Illuminate\Support\Facades\Input;
use App\To\SubstituicaoJulgamentoFinalTO;
use App\Business\SubstituicaoJulgamentoFinalBO;

/**
 * Classe de controle referente a entidade 'SubstituicaoJulgamentoFinal'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class SubstituicaoJulgamentoFinalController extends Controller
{

    /**
     * @var SubstituicaoJulgamentoFinalBO
     */
    private $substituicaoJulgamentoFinalBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->substituicaoJulgamentoFinalBO = app()->make(SubstituicaoJulgamentoFinalBO::class);
        $this->membroChapaBO = app()->make(MembroChapaBO::class);
    }

    /**
     * Salvar dados do julgamento do pedido de substituição da Chapa Eleição
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/substituicaoJulgamentoFinal/salvar",
     *     tags={"Substituição Julgamento Final"},
     *     summary="Salvar dados do pedido de substituição",
     *     description="Salvar dados do pedido de substituição do julgamento final da Chapa Eleição",
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

        $substituicaojulgamentoFinalTO = SubstituicaoJulgamentoFinalTO::newInstance($data);

        $pedido = $this->substituicaoJulgamentoFinalBO->salvar($substituicaojulgamentoFinalTO);

        return $this->toJson($pedido);
    }

    /**
     * Realiza consulta de Substituição Julgamento Final.
     *
     * @param $idChapa
     * @return string
     *
     * @OA\get(
     *     path="/substituicaoJulgamentoFinal/chapa/{idChapa}",
     *     tags={"Substituição Julgamento Final"},
     *     summary="Consultar Substituição Julgamento Final",
     *     description="Busca pedido de substituição(julgamento final) de membro da chapa através do id chapa. ",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapa",
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
    public function getPorChapa($idChapa)
    {
        $substituicaojulgamentoFinal = $this->substituicaoJulgamentoFinalBO->getPorChapa($idChapa);
        return $this->toJson($substituicaojulgamentoFinal);
    }

    /**
     * Realiza download da Substituição Julgamento Final.
     *
     * @param $id
     * @return string
     *
     * @OA\get(
     *     path="/substituicaoJulgamentoFinal/{id}/download",
     *     tags={"Substituição Julgamento Final"},
     *     summary="Download de  arquivo Substituição Julgamento Final",
     *     description="Download de arquivos de substituição.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Substituição",
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
    public function download($id)
    {
        $arquivoTO = $this->substituicaoJulgamentoFinalBO->getArquivoSubstituicaoJulgamentoFinal($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Realiza download de Recurso da Substituição Julgamento Final.
     *
     * @param $id
     * @return string
     *
     * @OA\get(
     *     path="/substituicaoJulgamentoFinal/recurso/{id}/download",
     *     tags={"Substituição Julgamento Final"},
     *     summary="Download de  arquivo Recurso Substituição Julgamento Final",
     *     description="Download de arquivos Recurso de substituição.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Recurso da Substituição",
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
    public function downloadRecurso($id)
    {
        $arquivoTO = $this->substituicaoJulgamentoFinalBO->getArquivoSubstituicaoRecursoJulgamentoFinal($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Realiza consulta de membros Titular para ser substituído
     *
     * @return string
     * @throws NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\get(
     *     path="/substituicaoJulgamentoFinal/membroSubstituto/{idProfissional}",
     *     tags={"Substituição Julgamento Final"},
     *     summary="Realiza consulta de membros Titular para ser substituído",
     *     description="Realiza consulta de membros Titular para ser substituído",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idProfissional",
     *         in="path",
     *         description="Id do Membro Titular",
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
    public function getMembroChapaSubstituto()
    {
        $data = Input::all();

        $membroChapaSubstituicaoTO = MembroChapaSubstituicaoTO::newInstance($data);

        $membro = $this->membroChapaBO->getMembroChapaSubstituto($membroChapaSubstituicaoTO);

        return $this->toJson($membro);
    }

    /**
     * Realiza consulta de Substituição Julgamento Final.
     *
     * @param $id
     * @return string
     *
     *  @OA\get(
     *     path="/substituicaoJulgamentoFinal/{id}",
     *     tags={"Substituição Julgamento Final"},
     *     summary="Consultar Substituição Julgamento Final",
     *     description="Busca pedido de substituição(julgamento final) de membro da chapa através do id. ",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapa",
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
    public function getPorId($id)
    {
        $substituicaojulgamentoFinal = $this->substituicaoJulgamentoFinalBO->getPorId($id);
        return $this->toJson($substituicaojulgamentoFinal);
    }

}
