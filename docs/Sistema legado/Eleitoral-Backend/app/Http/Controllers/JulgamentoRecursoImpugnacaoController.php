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
use App\Business\JulgamentoRecursoImpugnacaoBO;
use App\Business\JulgamentoRecursoSubstituicaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Business\PedidoSubstituicaoChapaBO;
use App\Entities\JulgamentoSubstituicao;
use App\Exceptions\NegocioException;
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
 * Classe de controle referente a entidade 'JulgamentoRecursoImpugnacao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class JulgamentoRecursoImpugnacaoController extends Controller
{

    /**
     * @var JulgamentoRecursoImpugnacaoBO
     */
    private $julgamentoRecursoImpugnacaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salvar dados do julgamento do recurso do pedido de impugnação
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/julgamentosRecursoImpugnacao/salvar",
     *     tags={"Julgamento 2ª instância do Pedido de Impugnação"},
     *     summary="Salvar dados do julgamento do recurso do pedido de impugnação",
     *     description="Salvar dados do julgamento do recurso do pedido de impugnação",
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

        $julgamentoRecursoImpugnacaoTO = JulgamentoRecursoImpugnacaoTO::newInstance($data);

        $julgamento = $this->getJulgamentoRecursoImpugnacaoBO()->salvar($julgamentoRecursoImpugnacaoTO);
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
     *     path="/julgamentosRecursoImpugnacao/{id}/download",
     *     tags={"Julgamento 2ª instância do Pedido de Impugnação"},
     *     summary="Download do Documento do Julgamento 2ª instância do Pedido de Impugnação",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Julgamento Impugnação",
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
        $arquivoTO = $this->getJulgamentoRecursoImpugnacaoBO()->getArquivoJulgamentoRecursoImpugnacao($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna o julgamento do recurso de impugnação conforme o id do pedido impugnação informado.
     *
     * @param $idPedidoImpugnacao
     * @return string
     * @throws Exception
     *
     * @OA\Get(
     *     path="/julgamentosRecursoImpugnacao/pedidoImpugnacao/{idPedidoImpugnacao}",
     *     tags={"Julgamento 2ª instância do Pedido de Impugnação"},
     *     summary="Dados da Julgamento do Recurso de Impugnação",
     *     description="Retorna o julgamento do recurso de impugnação conforme o id do pedido impugnação informado",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idPedidoImpugnacao",
     *         in="path",
     *         description="Id do Pedido Julgamento Impugnação",
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
    public function getPorPedidoImpugnacao($idPedidoImpugnacao)
    {
        $resp = $this->getJulgamentoRecursoImpugnacaoBO()->getPorPedidoImpugnacao($idPedidoImpugnacao);
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
     *     path="julgamentosRecursoImpugnacao/membroComissao/pedidoImpugnacao/{idPedidoImpugnacao}",
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
    public function getPorPedidoImpugnacaoMembroComissao($idPedidoSubstituicao)
    {
        $resp = $this->getJulgamentoRecursoImpugnacaoBO()->getPorPedidoImpugnacao(
            $idPedidoSubstituicao, false, true
        );
        return $this->toJson($resp);
    }

    /**
     * Retorna o julgamento do recurso de impugnação conforme o id do pedido impugnação informado
     * com verificação para responsável chapa ou impugnante.
     *
     * @param $idPedidoSubstituicao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="julgamentosRecursoImpugnacao/responsavel/pedidoImpugnacao/{idPedidoImpugnacao}",
     *     tags={"Dados da Julgamento do Recurso de Impugnação"},
     *     summary="Dados da Julgamento de Rec6ruso de Impugnação",
     *     description="Retorna o julgamento de recurso de impugnação com verificação para responsável (chapa pu impugnante).",
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
    public function getPorPedidoImpugnacaoResponsavel($idPedidoSubstituicao)
    {
        $resp = $this->getJulgamentoRecursoImpugnacaoBO()->getPorPedidoImpugnacao(
            $idPedidoSubstituicao, true, false
        );
        return $this->toJson($resp);
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoImpugnacaoBO'.
     *
     * @return JulgamentoRecursoImpugnacaoBO
     */
    private function getJulgamentoRecursoImpugnacaoBO()
    {
        if (empty($this->julgamentoRecursoImpugnacaoBO)) {
            $this->julgamentoRecursoImpugnacaoBO = app()->make(JulgamentoRecursoImpugnacaoBO::class);
        }

        return $this->julgamentoRecursoImpugnacaoBO;
    }
}
