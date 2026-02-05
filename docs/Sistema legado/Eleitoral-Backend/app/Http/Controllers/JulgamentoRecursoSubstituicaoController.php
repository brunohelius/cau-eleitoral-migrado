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
use App\Business\JulgamentoRecursoSubstituicaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Business\PedidoSubstituicaoChapaBO;
use App\Entities\JulgamentoSubstituicao;
use App\Exceptions\NegocioException;
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
class JulgamentoRecursoSubstituicaoController extends Controller
{

    /**
     * @var JulgamentoRecursoSubstituicaoBO
     */
    private $julgamentoRecursoSubstituicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salvar dados do julgamento do recurso do pedido de substituição da Chapa Eleição
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/julgamentosRecursoSubstituicao/salvar",
     *     tags={"Julgamento 2ª instância do Pedido de Substituição"},
     *     summary="Salvar dados do julgamento de substituição 2ª instância",
     *     description="Salvar dados do julgamento do recurso do pedido de substituição da Chapa Eleição",
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

        $julgamentoRecursoSubstituicaoTO = JulgamentoRecursoSubstituicaoTO::newInstance($data);

        $pedidoImpugnacao = $this->getJulgamentoRecursoSubstituicaoBO()->salvar($julgamentoRecursoSubstituicaoTO);
        return $this->toJson($pedidoImpugnacao);
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
     *     path="/julgamentosRecursoSubstituicao/{id}/download",
     *     tags={"Julgamento 2ª instância do Pedido de Substituição"},
     *     summary="Download do Documento do Julgamento 2ª instância do Pedido de Substituição",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Pedido de Substituição",
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
        $arquivoTO = $this->getJulgamentoRecursoSubstituicaoBO()->getArquivoJulgamentoRecursoSubstituicao($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna a atividade secundária do julgamento 2ª instância do Pedido de Substituição,
     *
     * @param $idPedidoSubstituicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/julgamentosRecursoSubstituicao/{idPedidoSubstituicao}/atividadeSecundariaJulgamento",
     *     tags={"Julgamento 2ª instância do Pedido de Substituição"},
     *     summary="Retorna a atividade secundária do julgamento 2ª instância do Pedido de Substituição,",
     *     description="Retorna a atividade secundária do julgamento 2ª instância do Pedido de Substituição,",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idPedidoSubstituicao",
     *         in="path",
     *         description="Id do Pedido de Substituição",
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
    public function getAtividadeSecundariaJulgamentoRecurso($idPedidoSubstituicao)
    {
        $atividadeSecundario = $this->getJulgamentoRecursoSubstituicaoBO()->getAtividadeSecundariaJulgamentoRecurso(
            $idPedidoSubstituicao
        );
        return $this->toJson($atividadeSecundario);
    }

    /**
     * Retorna o julgamento do recurso de substituição conforme o id do pedido substituição informado.
     *
     * @param $idPedidoSubstituicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/julgamentosRecursoSubstituicao/pedidoSubstituicao/{idPedidoSubstituicao}",
     *     tags={"Julgamento 2ª instância do Pedido de Substituição"},
     *     summary="Dados da Julgamento do Recurso de Substituição",
     *     description="Retorna o julgamento do recurso de substituição conforme o id do pedido substituição informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idPedidoSubstituicao",
     *         in="path",
     *         description="Id do Pedido de Substituição",
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
    public function getPorPedidoSubstituicao($idPedidoSubstituicao)
    {
        $resp = $this->getJulgamentoRecursoSubstituicaoBO()->getPorPedidoSubstituicao($idPedidoSubstituicao);
        return $this->toJson($resp);
    }

    /**
     * Retorna o julgamento do recurso de substituição conforme o id do pedido substituição informado
     * com verificação para membro da comissão.
     *
     * @param $idPedidoSubstituicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="julgamentosRecursoSubstituicao/membroComissao/pedidoSubstituicao/{idPedidoSubstituicao}",
     *     tags={"Julgamento 2ª instância do Pedido de Substituição"},
     *     summary="Dados da Julgamento do Recurso de Substituição",
     *     description="Retorna o julgamento de substituição conforme com verificação para membro da comissão.",
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
    public function getPorPedidoSubstituicaoMembroComissao($idPedidoSubstituicao)
    {
        $resp = $this->getJulgamentoRecursoSubstituicaoBO()->getPorPedidoSubstituicao(
            $idPedidoSubstituicao, false, true
        );
        return $this->toJson($resp);
    }

    /**
     * Retorna o julgamento do recurso de substituição conforme o id do pedido substituição informado
     * com verificação para responsável chapa.
     *
     * @param $idPedidoSubstituicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="julgamentosRecursoSubstituicao/responsavelChapa/pedidoSubstituicao/{idPedidoSubstituicao}",
     *     tags={"Dados da Julgamento do Recurso de Substituição"},
     *     summary="Dados da Julgamento de Substituição",
     *     description="Retorna o julgamento de substituição conforme com verificação para responsável chapa.",
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
    public function getPorPedidoSubstituicaoResponsavelChapa($idPedidoSubstituicao)
    {
        $resp = $this->getJulgamentoRecursoSubstituicaoBO()->getPorPedidoSubstituicao(
            $idPedidoSubstituicao, true, false
        );
        return $this->toJson($resp);
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoSubstituicaoBO'.
     *
     * @return JulgamentoRecursoSubstituicaoBO
     */
    private function getJulgamentoRecursoSubstituicaoBO()
    {
        if (empty($this->julgamentoRecursoSubstituicaoBO)) {
            $this->julgamentoRecursoSubstituicaoBO = app()->make(JulgamentoRecursoSubstituicaoBO::class);
        }

        return $this->julgamentoRecursoSubstituicaoBO;
    }
}
