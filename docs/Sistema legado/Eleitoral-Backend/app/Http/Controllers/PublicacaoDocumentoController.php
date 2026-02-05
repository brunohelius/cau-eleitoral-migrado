<?php
/*
 * PublicacaoDocumentoController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use App\Entities\PublicacaoDocumento;
use App\Business\PublicacaoDocumentoBO;
use Mpdf\MpdfException;
use PhpOffice\PhpWord\Exception\Exception;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe de controle referente a entidade 'PublicacaoDocumento'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class PublicacaoDocumentoController extends Controller
{

    /**
     * @var PublicacaoDocumentoBO
     */
    private $publicacaoDocumentoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->publicacaoDocumentoBO = app()->make(PublicacaoDocumentoBO::class);
    }

    /**
     * Método responsável por salvar uma publicação de documento.
     *
     * @return string
     * @throws Exception
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws ORMException
     * @throws OptimisticLockException
     * @OA\Post(
     *     path="/publicacao/comissaoMembro",
     *     tags={"Publicação Comissão Membro", "Salvar Publicação Comissão Membro"},
     *     summary="Salvar dados de Publicação de Comissão Membro.",
     *     description="Salva o dados de Publicação de Comissão Membro.",
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
        $publicacaoDocumento = PublicacaoDocumento::newInstance($data);
        $publicacaoDocumentoSalvo = $this->publicacaoDocumentoBO->salvar($publicacaoDocumento);

        return $this->toJson($publicacaoDocumentoSalvo);
    }

    /**
     * Gera o pdf do documento de comissão membro.
     *
     * @param $idDocumentoComissao
     * @param Request $request
     *
     * @return Response
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws MpdfException
     * @OA\Get(
     *     path="/publicacao/comissaoMembro/{idDocumentoComissao}/pdf",
     *     tags={"Publicação de Comissão Membro", "Gerar PDF"},
     *     summary="Realiza a geração do PDF de acordo com o id da publicação do documento da comissão membro.",
     *     description="Realiza a geração do PDF de acordo com o id da publicação do documento da comissão membro.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idDocumentoComissao",
     *         in="path",
     *         description="Id do Publicação do Documento",
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
    public function gerarPDF($idDocumentoComissao, Request $request)
    {
        $pdfDocumentoComissaoMembro = $this->publicacaoDocumentoBO->gerarPdf($idDocumentoComissao);
        return $this->toFile($pdfDocumentoComissaoMembro);
    }

    /**
     * Gera o documento no formato '.DOCX'.
     *
     * @param $idDocumentoComissao
     * @return Response
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     *
     * @OA\Get(
     *     path="/publicacao/comissaoMembro/{idDocumentoComissao}/doc",
     *     tags={"Publicação de Comissão Membro", "Gerar Documento"},
     *     summary="Realiza a geração do documento de acordo com o id da publicação do documento da comissão membro.",
     *     description="Realiza a geração do documento de acordo com o id da publicação do documento da comissão membro.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idDocumentoComissao",
     *         in="path",
     *         description="Id da Publicação do Documento",
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
    public function gerarDocumento($idDocumentoComissao)
    {
        $documentoPublicacaoComissaoMembro = $this->publicacaoDocumentoBO->gerarDocumento($idDocumentoComissao);
        return $this->toFile($documentoPublicacaoComissaoMembro);
    }

    /**
     * Realiza o download do documento pdf.
     *
     * @param $id
     * @return Response
     * @throws NegocioException
     * @throws NonUniqueResultException
     *
     * @OA\Get(
     *     path="/publicacao/{id}/download",
     *     tags={"Publicação de Comissão Membro", "Download PDF"},
     *     summary="Realiza o download do PDF de acordo com o id da publicação do documento da comissão membro.",
     *     description="Realiza o download do PDF de acordo com o id da publicação do documento da comissão membro.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Publicação do Documento",
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
    public function downloadPdf($id)
    {
        $pdfDocumentoComissaoMembro = $this->publicacaoDocumentoBO->downloadPdf($id);
        return $this->toFile($pdfDocumentoComissaoMembro);
    }

}
