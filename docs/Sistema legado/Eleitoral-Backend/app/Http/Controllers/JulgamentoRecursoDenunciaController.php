<?php
/*
 * JulgamentoRecursoDenunciaController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\JulgamentoRecursoDenunciaBO;
use App\Entities\JulgamentoRecursoDenuncia;
use App\Exceptions\NegocioException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

class JulgamentoRecursoDenunciaController extends Controller
{

    /**
     * @var \App\Business\JulgamentoRecursoDenunciaBO
     */
    private $julgamentoRecursoDenunciaBO;

    public function __construct()
    {
    }

    /**
     * Salvar dados do Julgamento do Recurso
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="julgamentoRecurso/salvar",
     *     tags={"julgamento", "recurso", "salvar"},
     *     summary="Salvar dados do julgamento do recurso",
     *     description="Salvar dados do julgamento do recurso",
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
    public function salvar()
    {
        $data = Input::all();
        $dados = JulgamentoRecursoDenuncia::newInstance($data);

        $resp = $this->getJulgamentoRecursoDenunciaBO()->salvar($dados, $data);
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
     *     path="julgamentoRecursoDenuncia/{id}/download",
     *     tags={"julgamento", "recurso", "download"},
     *     summary="Download de arquivo do Recurso da Denuncia",
     *     description="Disponibiliza o arquivo do Recurso do julgamento para 'download' conforme o 'id' informado.",
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
    public function download($id)
    {
        $arquivoTO = $this->getJulgamentoRecursoDenunciaBO()->getArquivoRecursoJulgamento($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoDenunciaBO'.
     *
     * @return JulgamentoRecursoDenunciaBO
     */
    private function getJulgamentoRecursoDenunciaBO()
    {
        if (empty($this->julgamentoRecursoDenunciaBO)) {
            $this->julgamentoRecursoDenunciaBO = app()->make(JulgamentoRecursoDenunciaBO::class);
        }
        return $this->julgamentoRecursoDenunciaBO;
    }

    /**
     * Recupera a entidade 'ArquivoJulgamentoDenuncia' por meio do 'id'
     * informado.
     *
     * @param $id
     * @return \App\Entities\ArquivoJulgamentoRecursoDenuncia|null
     */
    private function getArquivoJulgamento($id)
    {
        return current($this->getArquivoJulgamento()->getPorId($id));
    }
    /**
     * Retorna todas as retificação de julgamento da recurso de acordo com 'idRetificacao'.
     *
     * @param $idRetificacao
     * @return string
     * @throws \Exception
     * @OA\Post(
     *     path="retificacaoJulgamentoRecursoDenuncia/{idRetificacao}",
     *     tags={"retificacao", "Denuncia", "Julgamento", "Segunda", "Instância", "Recurso"},
     *     summary="Retorna a retificação do recurso do julgamento de acordo com 'idRetificacao'.",
     *     description="Retorna a retificação do recurso do julgamento da denuncia de acordo com 'idRetificacao'.",
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

    public function getRecursoRetificadoPorId($idRetificacao)
    {
        $recursoRetificado = $this->getJulgamentoRecursoDenunciaBO()->getAllRecursoRetificadoPorId($idRetificacao);
        return $this->toJson($recursoRetificado);
    }


    /**
     * Retorna uma retificação do recurso do julgamento pelo 'idRetificacao'.
     *
     * @param $idRetificado
     * @return string
     * @OA\Post(
     *     path="retificacaoJulgamentoRecurso/{idRetificacao}",
     *     tags={"retificacao", "Denuncia", "Julgamento", "Segunda", "Instância", "Recurso"},
     *     summary="Retorna a retificação do recurso do julgamento de acordo com 'idRetificacao'.",
     *     description="Retorna a retificação do recurso do julgamento da denuncia de acordo com 'idRetificacao'.",
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
    public function getRetificadoPorId($idRetificado)
    {
        $recursoRetificado = $this->getJulgamentoRecursoDenunciaBO()->getRecursoRetificadoPorId($idRetificado);
        return $this->toJson($recursoRetificado);
    }
}
