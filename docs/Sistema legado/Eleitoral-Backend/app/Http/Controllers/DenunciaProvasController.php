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
use App\Business\DenunciaProvasBO;
use App\Entities\DenunciaProvas;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

/**
 * Classe de controle referente a entidade 'DenunciaProvasController'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class DenunciaProvasController extends Controller
{
    /**
     * @var \App\Business\DenunciaProvasBO
     */
    private $denunciaProvasBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {}


    /**
     * Salva a Denuncia Prova
     *
     * @return string
     * @throws \Exception
     * @OA\Post(
     *     path="denuncia/prova",
     *     tags={"Inserir Provas"},
     *     summary="Salva as Provas da Denuncia",
     *     description="Salva as Provas da Denuncia",
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
        $denunciaProvas = DenunciaProvas::newInstance($data);
        $resp = $this->getDenunciaProvasBO()->salvar($denunciaProvas);
        return $this->toJson($resp);
    }

    /**
     * Retorna a instancia de DenunciaProvasBO
     *
     * @return DenunciaProvasBO
     */
    private function getDenunciaProvasBO()
    {
        if (empty($this->denunciaProvasBO)) {
            $this->denunciaProvasBO = app()->make(DenunciaProvasBO::class);
        }
        return $this->denunciaProvasBO;
    }

    /**
     * Disponibiliza o arquivo 'Denúncia Provas' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="denunciaProvas/arquivo/{idArquivo}/download",
     *     tags={"denuncia", "arquivos"},
     *     summary="Download de Arquivo da Denúncia Provas",
     *     description="Disponibiliza o arquivo 'Denúncia Provas' para 'download' conforme o 'id' informado.",
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
        $arquivoTO = $this->getDenunciaProvasBO()->getArquivo($idArquivo);
        return $this->toFile($arquivoTO);
    }
}