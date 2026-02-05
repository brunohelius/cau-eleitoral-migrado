<?php

namespace App\Http\Controllers;

use App\Business\JulgamentoRecursoAdmissibilidadeBO;
use Illuminate\Http\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Illuminate\Support\Facades\Input;

/**
 * Class JulgamentoRecursoAdmissibilidadeController
 * @package App\Http\Controllers
 */
class JulgamentoRecursoAdmissibilidadeController extends Controller
{
    /**
     * @return JsonResponse
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncia/julgamento-recurso-admissibilidade",
     *     tags={"denuncia", "profissional"},
     *     summary="Julgar Recurso Admissibilidade",
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
    public function salvar(JulgamentoRecursoAdmissibilidadeBO $julgamentoRecursoAdmissibilidadeBO)
    {
        $data = $julgamentoRecursoAdmissibilidadeBO->salvar(Input::all());
        return response()->json(["id" => $data->getId()]);
    }

    /**
     * Disponibiliza o arquivo 'Resucrso' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return BinaryFileResponse
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="denuncia/julgamento-recurso-admissibilidade/arquivo/{idArquivo}",
     *     tags={"Julgamento recurso admissibilidade", "arquivos"},
     *     summary="Download de Arquivo do julgamento recurso  de admissibilidade",
     *     description="Disponibiliza o arquivo 'Julgamento Recurso de admissibilidade' para 'download' conforme o 'id' informado.",
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
    public function download(JulgamentoRecursoAdmissibilidadeBO $julgamentoRecursoAdmissibilidadeBO, $idArquivo)
    {
        $arquivoTO = $julgamentoRecursoAdmissibilidadeBO->getArquivo($idArquivo);
        $response = response($arquivoTO->file, 200);
        $response->header('Content-Type', $arquivoTO->type);
        $response->header('Content-disposition', 'attachment; filename="' . $arquivoTO->name . '"');
        return $response;
    }
}