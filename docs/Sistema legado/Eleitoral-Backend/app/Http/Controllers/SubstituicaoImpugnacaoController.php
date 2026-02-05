<?php
/*
 * ChapaEleicaoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\RecursoImpugnacaoBO;
use App\Business\SubstituicaoImpugnacaoBO;
use App\Exceptions\NegocioException;
use App\To\RecursoImpugnacaoTO;
use App\To\SubstituicaoImpugnacaoFiltroTO;
use App\To\SubstituicaoImpugnacaoTO;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'SubstituicaoImpugnacao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class SubstituicaoImpugnacaoController extends Controller
{

    /**
     * @var SubstituicaoImpugnacaoBO
     */
    private $substituicaoImpugnacaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }

    /**
     * Retorna um substituto para membro chapa impugnado.
     *
     *
     * @return string
     *
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Post(
     *     path="/substituicaoImpugnacao/buscaSubstituto",
     *     tags={"Substituição Impugnação, Membro Chapa"},
     *     summary="Retorna um substituto para membro chapa impugnado.",
     *     description="Retorna um substituto para membro chapa impugnado.",
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
    public function buscaSubstituto()
    {
        $data = Input::all();

        $resp = $this->getSubstituicaoImpugnacaoBO()->consultarMembroParaSubstituto(
            SubstituicaoImpugnacaoFiltroTO::newInstance($data)
        );

        return $this->toJson($resp);
    }

    /**
     * Retorna a substituição de impugnação conforme o id do pedido impugnação informado.
     *
     * @param $idPedidoImpugnacao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/substituicaoImpugnacao/pedidoImpugnacao/{idPedidoImpugnacao}",
     *     tags={"Substituição Impugnação"},
     *     summary="Retorna a substituição de impugnação conforme o id do pedido impugnação informado.",
     *     description="Retorna a substituição de impugnação conforme o id do pedido impugnação informado.",
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
    public function getPorPedidoImpugnacao($idPedidoImpugnacao)
    {
        $resp = $this->getSubstituicaoImpugnacaoBO()->getPorPedidoImpugnacao($idPedidoImpugnacao);
        return $this->toJson($resp);
    }

    /**
     * Salvar dados da solicitação de substituikção do pedido de impugnação
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/substituicaoImpugnacao/salvar",
     *     tags={"Substituição Impugnação"},
     *     summary="Salvar dados da solicitação de substituikção do pedido de impugnação",
     *     description="Salvar dados da solicitação de substituikção do pedido de impugnação",
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

        $substituicaoImpugnacao = SubstituicaoImpugnacaoTO::newInstance($data);

        $substituicaoImpugnacao = $this->getSubstituicaoImpugnacaoBO()->salvar($substituicaoImpugnacao);
        return $this->toJson($substituicaoImpugnacao);
    }

    /**
     * Retorna uma nova instância de 'SubstituicaoImpugnacaoBO'.
     *
     * @return SubstituicaoImpugnacaoBO
     */
    private function getSubstituicaoImpugnacaoBO()
    {
        if (empty($this->substituicaoImpugnacaoBO)) {
            $this->substituicaoImpugnacaoBO = app()->make(SubstituicaoImpugnacaoBO::class);
        }

        return $this->substituicaoImpugnacaoBO;
    }
}
