<?php
/*
 * JulgamentoSegundaInstanciaRecursoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\JulgamentoSegundaInstanciaRecursoBO;
use App\Exceptions\NegocioException;
use App\To\JulgamentoSegundaInstanciaRecursoTO;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'JulgamentoSegundaInstanciaRecurso'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class JulgamentoSegundaInstanciaRecursoController extends Controller
{

    /**
     * @var JulgamentoSegundaInstanciaRecursoBO
     */
    private $julgamentoSegundaInstanciaRecursoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salva o Julgamento de Segunda Instancia do Recurso.
     *
     * @return string
     * @throws NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Post(
     *     path="/julgamentoRecursoSegundaInstancia/salvar",
     *     tags={"Julgamento 2ª instância do Recurso"},
     *     summary="Salva o Julgamento de Segunda Instancia do Recurso.",
     *     description="Salva o Julgamento de Segunda Instancia do Recurso.",
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
        $resp = $this->getJulgamentoSegundaInstanciaRecursoBO()->salvar(
            JulgamentoSegundaInstanciaRecursoTO::newInstance(Input::all())
        );
        return $this->toJson($resp);
    }

    /**
     * Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.
     *
     * @param $id
     *
     * @return Response
     * @throws NegocioException
     *
     * @OA\Get(
     *     path="/julgamentoRecursoSegundaInstancia/{id}/download",
     *     tags={"Julgamento 2ª instância do Recurso"},
     *     summary="Download do documento do julgamento do recurso de segunda instância da Chapa",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Julgamento do Recurso de Segunda Instância",
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
        $arquivoTO = $this->getJulgamentoSegundaInstanciaRecursoBO()->getArquivo($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna o julgamento Final da Chapa da Eleição conforme o id da Chapa da Eleição.
     *
     * @param $idChapaEleicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/julgamentoRecursoSegundaInstancia/chapa/{idChapaEleicao}",
     *     tags={"Julgamento 2ª instância do Recurso"},
     *     summary="Dados da Julgamento 2ª instância do Recurso",
     *     description="Retorna o julgamento 2ª instância do Recurso conforme o id da Chapa da Eleição.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapaEleicao",
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
    public function getPorChapaEleicao($idChapaEleicao)
    {
        $resp = $this->getJulgamentoSegundaInstanciaRecursoBO()->getPorChapaEleicao($idChapaEleicao);
        return $this->toJson($resp);
    }

    /**
     * Retorna as retificações do julgamento do recurso de segunda instância conforme o id do julgamento final informado.
     *
     * @param $idRecursoJulgamentoFinal
     * @return string
     * @throws Exception
     *
     * @OA\Get(
     *     path="/julgamentoRecursoSegundaInstancia/retificacoes/{idRecursoJulgamentoFinal}",
     *     tags={"Julgamento 2ª instância da Substituicao"},
     *     summary="Dados da Julgamento da Substituicao de Segunda Instância",
     *     description="Retorna as retificações do julgamento do recurso conforme o id da substituição",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idRecursoJulgamentoFinal",
     *         in="path",
     *         description="Id do Recurso do Julgamento Final",
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
    public function getRetificacoes($idRecursoJulgamentoFinal)
    {
        $resp = $this->getJulgamentoSegundaInstanciaRecursoBO()->getRetificacoes($idRecursoJulgamentoFinal);
        return $this->toJson($resp);
    }

    /**
     * Retorna uma nova instância de 'JulgamentoSegundaInstanciaRecursoBO'.
     *
     * @return JulgamentoSegundaInstanciaRecursoBO
     */
    private function getJulgamentoSegundaInstanciaRecursoBO()
    {
        if (empty($this->julgamentoSegundaInstanciaRecursoBO)) {
            $this->julgamentoSegundaInstanciaRecursoBO = app()->make(JulgamentoSegundaInstanciaRecursoBO::class);
        }

        return $this->julgamentoSegundaInstanciaRecursoBO;
    }
}
