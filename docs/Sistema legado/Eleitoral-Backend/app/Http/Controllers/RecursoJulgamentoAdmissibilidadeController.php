<?php

namespace App\Http\Controllers;

use App\Business\RecursoJulgamentoAdmissibilidadeBO;
use Illuminate\Http\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Illuminate\Support\Facades\Input;

/**
 * Class RecursoJulgamentoAdmissibilidadeController
 * @package App\Http\Controllers
 */
class RecursoJulgamentoAdmissibilidadeController extends Controller
{
    /**
     * @return JsonResponse
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncia/recurso-julgamento-admissibilidade",
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
    public function salvar(RecursoJulgamentoAdmissibilidadeBO $recursoJulgamentoAdmissibilidadeBO)
    {
        $data = $recursoJulgamentoAdmissibilidadeBO->salvar(Input::all());
        return response()->json(["id" => $data->getId()]);
    }

    /**
     * @return JsonResponse
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncia/recurso/recurso-julgamento-admissibilidade/{idJulgamentoAdmissibilidade}",
     *     tags={"denuncia", "profissional"},
     *     summary="Validar Prazo Recurso",
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
    public function validarPrazo(RecursoJulgamentoAdmissibilidadeBO $recursoJulgamentoAdmissibilidadeBO, $idJulgamentoAdmissibilidade)
    {
        $data = $recursoJulgamentoAdmissibilidadeBO->verificarPrazoRecurso([
            "julgamentoAdmissibilidade" => [
                "id" => $idJulgamentoAdmissibilidade
            ]
        ]);
        return response()->json($data);
    }

    /**
     * @return JsonResponse
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncia/recurso/visualizar/{idRecurso}",
     *     tags={"denuncia", "profissional"},
     *     summary="Valdiar Prazo Recurso",
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
    public function visualizar(RecursoJulgamentoAdmissibilidadeBO $recursoJulgamentoAdmissibilidadeBO, $idRecurso)
    {
        $data = $recursoJulgamentoAdmissibilidadeBO->getRecurso($idRecurso);
        return $this->toJson($data);
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
     *     path="denuncia/recurso/arquivo/{idArquivo}",
     *     tags={"denuncia", "arquivos"},
     *     summary="Download de Arquivo do recurso julgamento de admissibilidade",
     *     description="Disponibiliza o arquivo 'Recurso Julgamento de admissibilidade' para 'download' conforme o 'id' informado.",
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
    public function download(RecursoJulgamentoAdmissibilidadeBO $recursoJulgamentoAdmissibilidadeBO, $idArquivo)
    {
        $arquivoTO = $recursoJulgamentoAdmissibilidadeBO->getArquivo($idArquivo);
        $response = response($arquivoTO->file, 200);
        $response->header('Content-Type', $arquivoTO->type);
        $response->header('Content-disposition', 'attachment; filename="' . $arquivoTO->name . '"');
        return $response;
    }
}