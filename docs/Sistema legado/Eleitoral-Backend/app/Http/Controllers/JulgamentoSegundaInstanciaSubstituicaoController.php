<?php
/*
 * JulgamentoSegundaInstanciaSubstituicaoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\JulgamentoSegundaInstanciaSubstituicaoBO;
use App\Exceptions\NegocioException;
use App\To\JulgamentoSegundaInstanciaSubstituicaoTO;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'JulgamentoSegundaInstanciaSubstituicao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class JulgamentoSegundaInstanciaSubstituicaoController extends Controller
{

    /**
     * @var JulgamentoSegundaInstanciaSubstituicaoBO
     */
    private $julgamentoSegundaInstanciaSubstituicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salva o Julgamento de Segunda Instancia de Substituição.
     *
     * @return string
     * @throws NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Post(
     *     path="/julgamentoSubstituicaoSegundaInstancia/salvar",
     *     tags={"Julgamento 2ª instância de Substituição"},
     *     summary="Salva o Julgamento de Segunda Instancia de Substituição.",
     *     description="Salva o Julgamento de Segunda Instancia de Substituição.",
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
        $resp = $this->getJulgamentoSegundaInstanciaSubstituicaoBO()->salvar(
            JulgamentoSegundaInstanciaSubstituicaoTO::newInstance(Input::all())
        );
        return $this->toJson($resp);
    }

    /**
     * Retorna o julgamento do substituicao de segunda instância conforme o id do julgamento final informado.
     *
     * @param $idJulgamentoFinal
     * @return string
     * @throws Exception
     *
     * @OA\Get(
     *     path="/julgamentosSubstituicaoSegundaInstancia/julgamentoFinal/{idJulgamentoFinal}",
     *     tags={"Julgamento 2ª instância da Substituicao"},
     *     summary="Dados da Julgamento da Substituicao de Segunda Instância",
     *     description="Retorna o julgamento da substituicao de segunda instância conforme o id do julgamento final informado",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idJulgamentoFinal",
     *         in="path",
     *         description="Id do Julgamento Final",
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
    public function getPorJulgamentoFinal($idJulgamentoFinal)
    {
        $resp = $this->getJulgamentoSegundaInstanciaSubstituicaoBO()->getPorJulgamentoFinal($idJulgamentoFinal);
        return $this->toJson($resp);
    }

    /**
     * Retorna o julgamento do substituicao de segunda instância conforme o id do julgamento final informado.
     *
     * @param $idSubstituicaoJulgamentoFinal
     * @return string
     * @throws Exception
     *
     * @OA\Get(
     *     path="/julgamentoSubstituicaoSegundaInstancia/retificacoes/{idSubstituicaoJulgamentoFinal}",
     *     tags={"Julgamento 2ª instância da Substituicao"},
     *     summary="Dados da Julgamento da Substituicao de Segunda Instância",
     *     description="Retorna as retificações do julgamento da substituicao de segunda instância conforme o id da substituição",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idSubstituicaoJulgamentoFinal",
     *         in="path",
     *         description="Id da Substituição do Julgamento Final",
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
    public function getRetificacoes($idSubstituicaoJulgamentoFinal)
    {
        $resp = $this->getJulgamentoSegundaInstanciaSubstituicaoBO()->getRetificacoes($idSubstituicaoJulgamentoFinal);
        return $this->toJson($resp);
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
     *     path="/julgamentosSubstituicaoSegundaInstancia/{id}/download",
     *     tags={"Julgamento 2ª instância da substituicao"},
     *     summary="Download do documento do julgamento da substituicao de segunda instância da Chapa",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Julgamento da substituicao de Segunda Instância",
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
    public function download($id)
    {
        $arquivoTO = $this->getJulgamentoSegundaInstanciaSubstituicaoBO()->getArquivo($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna uma nova instância de 'JulgamentoSegundaInstanciaSubstituicaoBO'.
     *
     * @return JulgamentoSegundaInstanciaSubstituicaoBO
     */
    private function getJulgamentoSegundaInstanciaSubstituicaoBO()
    {
        if (empty($this->julgamentoSegundaInstanciaSubstituicaoBO)) {
            $this->julgamentoSegundaInstanciaSubstituicaoBO = app()->make(JulgamentoSegundaInstanciaSubstituicaoBO::class);
        }

        return $this->julgamentoSegundaInstanciaSubstituicaoBO;
    }
}
