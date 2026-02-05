<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 24/02/2019
 * Time: 11:25
 */

namespace App\Http\Controllers;

use App\Business\DenunciaBO;
use App\Business\DenunciaDefesaBO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

/**
 * Classe de controle referente a entidade 'DenunciaDefesaController'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class DenunciaDefesaController extends Controller
{
    /**
     * @var \App\Business\DenunciaDefesaBO
     */
    private $denunciaDefesaBO;

    /**
     * @var \App\Business\DenunciaBO
     */
    private $denunciaBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }

    /**
     * Retorna a Defesa de um membro de chapa ou comissão por Denunciado
     *
     * @return string
     *
     * @OA\Get(
     *     path="denunciaDefesa/denunciado",
     *     tags={"defesa", "membro", "denunciado"},
     *     summary="Retorna a Defesa de um membro de chapa ou comissão por Denunciado",
     *     description="Retorna a Defesa de um membro de chapa ou comissão por Denunciado.",
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
    public function getPorDenunciado()
    {
        $resp = $this->getDenunciaDefesaBO()->getPorDenunciado();
        return $this->toJson($resp);
    }

    /**
     * Disponibiliza o arquivo de 'Denúncia Defesa' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="denunciaDefesa/arquivo/{idArquivo}/download",
     *     tags={"denuncia", "arquivos", "download"},
     *     summary="Download de Arquivo da Denúncia",
     *     description="Disponibiliza o arquivo de 'Denúncia Defesa' para 'download' conforme o 'id' informado.",
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
    public function download($idArquivo)
    {
        $arquivoTO = $this->getDenunciaDefesaBO()->getArquivo($idArquivo);
        return $this->toFile($arquivoTO);
    }


    /**
     * Valida o prazo de Defesa da Denuncia Admitida
     *
     * @param $idDenuncia
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denunciaDefesa/validaPrazoDefesaDenuncia",
     *     tags={"denuncia"},
     *     summary="Valida o prazo de Defesa da Denuncia Admitida",
     *     description="Valida o prazo de Defesa da Denuncia Admitida.",
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
    public function validaPrazoDefesaDenuncia($idDenuncia)
    {
        $denuncia = $this->getDenunciaBO()->getDenunciaPorId($idDenuncia);
        return $this->getDenunciaDefesaBO()->validaPrazoDefesaDenuncia($denuncia);
    }
    
    /**
     * Retorna a instancia de DenunciaBO
     *
     * @return DenunciaDefesaBO|mixed
     */
    private function getDenunciaDefesaBO()
    {
        if (empty($this->denunciaDefesaBO)) {
            $this->denunciaDefesaBO = app()->make(DenunciaDefesaBO::class);
        }
        return $this->denunciaDefesaBO;
    }

    /**
     * Retorna a instancia de DenunciaBO
     *
     * @return DenunciaBO
     */
    private function getDenunciaBO()
    {
        if (empty($this->denunciaBO)) {
            $this->denunciaBO = app()->make(DenunciaBO::class);
        }
        return $this->denunciaBO;
    }
}