<?php
/*
 * DocumentoComissaoMembroController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;


use App\Business\DocumentoComissaoMembroBO;
use App\Entities\DocumentoComissaoMembro;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'DocumentoComissaoMembro'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class DocumentoComissaoMembroController extends Controller
{

    /**
     * @var DocumentoComissaoMembroBO
     */
    private $documentoComissaoMembroBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->documentoComissaoMembroBO = app()->make(DocumentoComissaoMembroBO::class);
    }

    /**
     * Salva o registro de documento comissão membro.
     *
     * @return string
     * @throws Exception
     *
     * @OA\Post(
     *     path="/documentoComissaoMembro",
     *     tags={"Documento Comissão Membro", "Comissão Membro"},
     *     summary="Salvar dados de Documento da Comissão dos Membros",
     *     description="Salva dados de Documento da Comissão dos Membros.",
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
        $documentoComissaoMembro = DocumentoComissaoMembro::newInstance($data);
        $documentoComissaoMembroSalvo = $this->documentoComissaoMembroBO->salvar($documentoComissaoMembro);
        return $this->toJson($documentoComissaoMembroSalvo);
    }

    /**
     * Recupera um documento de comissão membro de acordo com o 'id' informado.
     *
     * @param $id
     * @return string
     * @throws NonUniqueResultException
     * @throws NegocioException
     *
     * @OA\Get(
     *     path="/documentoComissaoMembro/{id}",
     *     tags={"Documento Comissão Membro", "Comissão Membro"},
     *     summary="Recupera os dados de Documento da Comissão dos Membros de acordo com o 'id' informado.",
     *     description="Recupera os dados de Documento da Comissão dos Membros de acordo com o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Comissão do Membro",
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
        $resp = $this->documentoComissaoMembroBO->getPorId($id);
        return $this->toJson($resp);
    }

}
