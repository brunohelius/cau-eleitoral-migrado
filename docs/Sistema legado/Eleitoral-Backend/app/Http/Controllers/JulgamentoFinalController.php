<?php
/*
 * ChapaEleicaoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\EleicaoBO;
use App\Business\JulgamentoFinalBO;
use App\Business\JulgamentoRecursoImpugnacaoBO;
use App\Business\JulgamentoRecursoSubstituicaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Business\PedidoSubstituicaoChapaBO;
use App\Entities\JulgamentoSubstituicao;
use App\Exceptions\NegocioException;
use App\To\JulgamentoFinalTO;
use App\To\JulgamentoRecursoImpugnacaoTO;
use App\To\JulgamentoRecursoSubstituicaoTO;
use App\To\JulgamentoSubstituicaoTO;
use App\To\PedidoSubstituicaoChapaTO;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'JulgamentoRecursoSubstituicao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class JulgamentoFinalController extends Controller
{

    /**
     * @var JulgamentoFinalBO
     */
    private $julgamentoFinalBO;
    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salvar dados do julgamento final da Chapa Eleição
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/julgamentosFinais/salvar",
     *     tags={"Julgamentos Finais"},
     *     summary="Salvar dados do julgamento final da Chapa Eleição",
     *     description="Salvar dados do julgamento final da Chapa Eleição",
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
        $julgamento = $this->getJulgamentoFinalBO()->salvar(JulgamentoFinalTO::newInstance(Input::all()));
        return $this->toJson($julgamento);
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
     *     path="/julgamentosFinais/{id}/download",
     *     tags={"Julgamentos Finais"},
     *     summary="Download do Documento do Julgamento Final da Chapa",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
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
    public function download($id)
    {
        $arquivoTO = $this->getJulgamentoFinalBO()->getArquivoJulgamentoFinal($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna o julgamento Final da Chapa da Eleição conforme o id da Chapa da Eleição.
     *
     * @param $idChapaEleicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/julgamentosFinais/julgamento/chapa/{idChapaEleicao}",
     *     tags={"Julgamentos Finais"},
     *     summary="Dados da Julgamento Final da Chapa",
     *     description="Retorna o julgamento Final da Chapa da Eleição conforme o id da Chapa da Eleição.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapaEleicao",
     *         in="path",
     *         description="Id da Chapa da Eleição",
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
    public function getPorChapaEleicao($idChapaEleicao)
    {
        $resp = $this->getJulgamentoFinalBO()->getPorChapaEleicao($idChapaEleicao);
        return $this->toJson($resp);
    }

    /**
     * Retorna o julgamento final conforme o id da chapa da eleição com verificação para membro da comissão.
     *
     * @param $idChapaEleicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/julgamentosFinais/julgamento/membroComissao/chapa/{idChapaEleicao}",
     *     tags={"Julgamentos Finais"},
     *     summary="Dados da Julgamento Final da Chapa",
     *     description="Retorna o julgamento final conforme o id da chapa da eleição com verificação para membro da comissão.",
     *     @OA\Parameter(
     *         name="idChapaEleicao",
     *         in="path",
     *         description="Id da Chapa da Eleição",
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
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição."),
     *     security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getPorChapaEleicaoMembroComissao($idChapaEleicao)
    {
        $resp = $this->getJulgamentoFinalBO()->getPorChapaEleicao($idChapaEleicao, false, true);
        return $this->toJson($resp);
    }

    /**
     * Retorna o julgamento final conforme o id da chapa da eleição com verificação para responsável chapa.
     *
     * @param $idChapaEleicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/julgamentosFinais/julgamento/responsavelChapa/chapa/{idChapaEleicao}",
     *     tags={"Julgamentos Finais"},
     *     summary="Dados da Julgamento Final da Chapa",
     *     description="Retorna o julgamento final conforme o id da chapa da eleição com verificação para responsável chapa.",
     *     @OA\Parameter(
     *         name="idChapaEleicao",
     *         in="path",
     *         description="Id da Chapa da Eleição",
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
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição."),
     *     security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getPorChapaEleicaoResponsavelChapa($idChapaEleicao)
    {
        $resp = $this->getJulgamentoFinalBO()->getPorChapaEleicao($idChapaEleicao, true, false);
        return $this->toJson($resp);
    }

    /**
     * Retorna os membros da chapa organizados para realizar o julgamento final.
     *
     * @param $idChapaEleicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/julgamentosFinais/membrosChapaParaIndicacao/chapa/{idChapaEleicao}",
     *     tags={"Julgamentos Finais"},
     *     summary="Lista de membros separados 'por pendência' e 'sem pendência'",
     *     description="Retorna os membros da chapa organizados para realizar o julgamento final.",
     *     @OA\Parameter(
     *         name="idChapaEleicao",
     *         in="path",
     *         description="Id da Chapa da Eleição",
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
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição."),
     *     security={
     *         {"Authorization": {}}
     *     }
     * )
     */
    public function getMembrosChapaParaJulgamentoFinal($idChapaEleicao)
    {
        $resp = $this->getJulgamentoFinalBO()->getMembrosChapaParaJulgamentoFinal($idChapaEleicao);
        return $this->toJson($resp);
    }

    /**
     * Retorna o julgamento Final de Segunda Instância da Chapa da Eleição conforme o id da Chapa da Eleição.
     *
     * @param $idChapaEleicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/julgamentosFinais/julgamentoSegundaInstancia/chapa/{idChapaEleicao}",
     *     tags={"Julgamentos Finais Segunda Instância"},
     *     summary="Dados da Julgamento Final de Segunda Instância da Chapa",
     *     description="Retorna o julgamento Final de Segunda Instância da Chapa da Eleição conforme o id da Chapa da Eleição.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapaEleicao",
     *         in="path",
     *         description="Id da Chapa da Eleição",
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
    public function getJulgamentoSegundaInstanciaPorChapaEleicao($idChapaEleicao)
    {
        $resp = $this->getJulgamentoFinalBO()->getJulgamentoSegundaInstanciaPorChapaEleicao($idChapaEleicao);
        return $this->toJson($resp);
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalBO'.
     *
     * @return JulgamentoFinalBO
     */
    private function getJulgamentoFinalBO()
    {
        if (empty($this->julgamentoFinalBO)) {
            $this->julgamentoFinalBO = app()->make(JulgamentoFinalBO::class);
        }

        return $this->julgamentoFinalBO;
    }
}
