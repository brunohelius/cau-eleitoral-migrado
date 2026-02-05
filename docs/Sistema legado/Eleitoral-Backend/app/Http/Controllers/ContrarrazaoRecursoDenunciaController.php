<?php
/*
 * ContrarrazaoRecursoDenunciaController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\ContrarrazaoRecursoDenunciaBO;
use App\To\ContrarrazaoRecursoDenunciaTO;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'ContrarrazaoRecursoDenuncia'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class ContrarrazaoRecursoDenunciaController extends Controller
{

    /**
     * @var ContrarrazaoRecursoDenunciaBO
     */
    private $contrarrazaoDenunciaBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salvar dados da contrarrazão do recurso da denuncia
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="contrarrazaoRecursoDenuncia/salvar",
     *     tags={"contrarrazao", "denuncia", "salvar"},
     *     summary="Salvar dados da contrarrazao do recurso da denúncia",
     *     description="Salvar dados da contrarrazao do recurso da denúncia",
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
        $contrarrazaoRecursoDenunciaTO = ContrarrazaoRecursoDenunciaTO::newInstance($data);

        $contrarrazaoRecursoSalvo = $this->getContrarrazaoRecursoDenunciaBO()->salvar($contrarrazaoRecursoDenunciaTO);
        return $this->toJson($contrarrazaoRecursoSalvo);
    }

    /**
     * Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.
     *
     * @param $id
     *
     * @return Response
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="recursoContrarrazaoDenuncia/{id}/download",
     *     tags={"contrarrazao", "denuncia", "download"},
     *     summary="Download de arquivo da Contrarrazão do recurso da Denuncia",
     *     description="Disponibiliza o arquivo da Contrarrazão da denuncia para 'download' conforme o 'id' informado.",
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
        $arquivoTO = $this->getContrarrazaoRecursoDenunciaBO()->getArquivo($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna a instancia de ContrarrazaoRecursoDenunciaBO.
     *
     * @return ContrarrazaoRecursoDenunciaBO
     */
    private function getContrarrazaoRecursoDenunciaBO()
    {
        if (empty($this->contrarrazaoDenunciaBO)) {
            $this->contrarrazaoDenunciaBO = app()->make(ContrarrazaoRecursoDenunciaBO::class);
        }
        return $this->contrarrazaoDenunciaBO;
    }
}
