<?php
/*
 * DocumentoEleicaoController.php Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\DocumentoEleicaoBO;
use App\Entities\DocumentoEleicao;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use OpenApi\Annotations as OA;

/**
 * Classe de controle referente a entidade 'DocumentoEleicao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class DocumentoEleicaoController extends Controller
{
    /**
     * @var DocumentoEleicaoBO
     */
    private $documentoEleicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->documentoEleicaoBO = app()->make(DocumentoEleicaoBO::class);
    }

    /**
     * Recupera os documentos de uma eleição informada.
     *
     * @param int $idEleicao
     * @return string
     *
     * @throws NegocioException
     * @OA\Get(
     *     path="/documentosEleicoes/{idEleicao}",
     *     tags={"Documentos de Eleições"},
     *     summary="Recupera os documentos de uma eleição informada.",
     *     description="Recupera os documentos de uma eleição informada.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idEleicao",
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
    public function getDocumentosPorEleicao($idEleicao)
    {
        $documentos = $this->documentoEleicaoBO->getDocumentosEleicaoPorCalendario($idEleicao);
        return $this->toJson($documentos);
    }

    /**
     * Salva o dados do documento da eleição.
     *
     * @return string
     * @throws NegocioException
     * @OA\Post(
     *     path="/documentosEleicoes",
     *     tags={"Documentos de Eleições"},
     *     summary="Salva o dados do documento da eleição",
     *     description="Salva o dados do documento da eleição",
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
        $documentoEleicao = DocumentoEleicao::newInstance($data);
        $documentoEleicaoSalva = $this->documentoEleicaoBO->salvar($documentoEleicao);
        $documentoEleicaoSalva->setArquivo(null);
        return $this->toJson($documentoEleicaoSalva);
    }

    /**
     * Disponibiliza o arquivo 'Resolução' para 'download' conforme o 'id' informado.
     *
     * @param $idDocumentoEleicao
     * @return Response
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/documentosEleicoes/{idDocumentoEleicao}/download",
     *     tags={"Calendários", "arquivos"},
     *     summary="Download de Resolução",
     *     description="Disponibiliza o arquivo 'Resolução' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idDocumentoEleicao",
     *         in="path",
     *         description="Id do Documento da Eleição",
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
    public function download($idDocumentoEleicao)
    {
        $arquivoTO = $this->documentoEleicaoBO->getArquivo($idDocumentoEleicao);
        return $this->toFile($arquivoTO);
    }


}
