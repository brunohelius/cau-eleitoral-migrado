<?php
/*
 * JulgamentoDenunciaController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\JulgamentoDenunciaBO;
use App\Exceptions\NegocioException;
use App\To\JulgamentoDenunciaTO;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'JulgamentoDenuncia'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class JulgamentoDenunciaController extends Controller
{

    /**
     * @var JulgamentoDenunciaBO
     */
    private $julgamentoDenunciaBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Retorna um array com todos os tipos de julgamento ordenados por Id..
     *
     * @return string
     *
     * @OA\Get(
     *     path="julgamentoDenuncia/tiposJulgamento",
     *     tags={"tipo", "julgamento", "denuncia"},
     *     summary="Retorna um array com todos os tipos de julgamento ordenados por Id.",
     *     description="Retorna um array com todos os tipos de julgamento ordenados por Id.",
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
    public function getTiposJulgamento()
    {
        $resp = $this->getJulgamentoDenunciaBO()->getTiposJulgamento();
        return $this->toJson($resp);
    }

    /**
     * Retorna um array com todos os tipos de sentença do julgamento ordenados por Id..
     *
     * @return string
     *
     * @OA\Get(
     *     path="julgamentoDenuncia/tiposSentencaJulgamento",
     *     tags={"tipo", "julgamento", "denuncia"},
     *     summary="Retorna um array com todos os tipos de sentença do julgamento ordenados por Id.",
     *     description="Retorna um array com todos os tipos de sentença do julgamento ordenados por Id.",
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
    public function getTiposSentencaJulgamento()
    {
        $resp = $this->getJulgamentoDenunciaBO()->getTiposSentencaJulgamento();
        return $this->toJson($resp);
    }

    /**
     * Salvar dados do julgamento da denuncia
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="julgamentoDenuncia/salvar",
     *     tags={"julgamento", "denuncia", "salvar"},
     *     summary="Salvar dados do julgamento da denúncia",
     *     description="Salvar dados do julgamento da denúncia",
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
        $julgamentoDenunciaTO = JulgamentoDenunciaTO::newInstance($data);

        $julgamentoDenunciaSalvo = $this->getJulgamentoDenunciaBO()->salvar($julgamentoDenunciaTO);
        return $this->toJson($julgamentoDenunciaSalvo);
    }

    /**
     * Retorna as retificações de julgamento da denuncia.
     *
     * @param $idDenuncia
     * @return string
     * @throws \Exception
     * @OA\Post(
     *     path="retificacoesJulgamentoDenuncia/denuncia/{idDenuncia}",
     *     tags={"retificacao", "Denuncia", "Julgamento", "Primeira", "Instância"},
     *     summary="Retorna as retificações de julgamento da denuncia",
     *     description="Retorna as retificações de julgamento da denuncia",
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
    public function listarRetificacoes($idDenuncia)
    {
        $retificacoes = $this->getJulgamentoDenunciaBO()->getRetificacoesJulgamento($idDenuncia);
        return $this->toJson($retificacoes);
    }

    /**
     * Retorna a retificação de julgamento da denuncia de acordo com 'idRetificacao'.
     *
     * @param $idRetificacao
     * @return string
     * @throws \Exception
     * @OA\Post(
     *     path="retificacaoJulgamentoDenuncia/{idRetificacao}",
     *     tags={"retificacao", "Denuncia", "Julgamento", "Primeira", "Instância"},
     *     summary="Retorna a retificação de julgamento da denuncia de acordo com 'idRetificacao'.",
     *     description="Retorna a retificação de julgamento da denuncia de acordo com 'idRetificacao'.",
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
    public function getRetificacaoPorId($idRetificacao)
    {
        $retificacao = $this->getJulgamentoDenunciaBO()->getRetificacaoPorId($idRetificacao);
        return $this->toJson($retificacao);
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
     *     path="julgamentoDenuncia/{id}/download",
     *     tags={"julgamento", "denuncia", "download"},
     *     summary="Download de arquivo do Julgamento de Denuncia",
     *     description="Disponibiliza o arquivo de julgamento da denuncia para 'download' conforme o 'id' informado.",
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
        $arquivoTO = $this->getJulgamentoDenunciaBO()->getArquivoJulgamentoDenuncia($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna a instancia de JulgamentoDenunciaBO.
     *
     * @return JulgamentoDenunciaBO
     */
    private function getJulgamentoDenunciaBO()
    {
        if (empty($this->julgamentoDenunciaBO)) {
            $this->julgamentoDenunciaBO = app()->make(JulgamentoDenunciaBO::class);
        }
        return $this->julgamentoDenunciaBO;
    }
}
