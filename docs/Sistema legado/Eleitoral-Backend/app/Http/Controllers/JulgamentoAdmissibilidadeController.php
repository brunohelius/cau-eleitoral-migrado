<?php

namespace App\Http\Controllers;

use App\Business\JulgamentoAdmissibilidadeBO;
use Illuminate\Http\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class JulgamentoAdmissibilidadeController
 * @package App\Http\Controllers
 */
class JulgamentoAdmissibilidadeController extends AbstractController
{
    /**
     * @return JsonResponse
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncia/{idDenuncia}/julgar_admissibilidade",
     *     tags={"denuncia", "profissional"},
     *     summary="Julgar Admissibilidade",
     *     description="Provimento ou Improvimento a denúncia em gestão",
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
    public function salvar($idDenuncia, JulgamentoAdmissibilidadeBO $julgamentoDenunciaBO)
    {
        $data = $julgamentoDenunciaBO->julgarAdmissibilidade($idDenuncia);
        return response()->json([

        ]);
    }

    /**
     * Disponibiliza o arquivo 'Denúncia' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return BinaryFileResponse
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="denuncia/julgamento_admissibilidade/arquivo/{idArquivo}/download",
     *     tags={"denuncia", "arquivos"},
     *     summary="Download de Arquivo do julgamento de admissibilidade",
     *     description="Disponibiliza o arquivo 'Julgamento de admissibilidade' para 'download' conforme o 'id' informado.",
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
    public function download($idArquivo, JulgamentoAdmissibilidadeBO $julgamentoDenunciaBO)
    {
        $arquivoTO = $julgamentoDenunciaBO->getArquivo($idArquivo);
        $response = response($arquivoTO->file, 200);
        $response->header('Content-Type', $arquivoTO->type);
        $response->header('Content-disposition', 'attachment; filename="' . $arquivoTO->name . '"');
        return $response;
    }
}