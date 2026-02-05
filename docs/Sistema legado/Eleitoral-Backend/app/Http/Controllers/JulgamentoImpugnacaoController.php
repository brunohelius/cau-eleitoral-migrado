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
use App\Business\JulgamentoImpugnacaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Business\PedidoSubstituicaoChapaBO;
use App\Entities\JulgamentoImpugnacao;
use App\Entities\JulgamentoSubstituicao;
use App\Exceptions\NegocioException;
use App\To\JulgamentoImpugnacaoTO;
use App\To\JulgamentoSubstituicaoTO;
use App\To\PedidoSubstituicaoChapaTO;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Mpdf\MpdfException;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe de controle referente a entidade 'JulgamentoSubstituicao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class JulgamentoImpugnacaoController extends Controller
{

    /**
     * @var JulgamentoImpugnacaoBO
     */
    private $julgamentoImpugnacaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->julgamentoImpugnacaoBO = app()->make(JulgamentoImpugnacaoBO::class);
    }

    /**
     * Retorna a atividade secundária do julgamento do pedido de substituição.
     *
     * @param $idPedidoImpugnacao
     * @return string
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/julgamentosImpugnacao/atividadeSecundariaCadastro",
     *     tags={"Atividade Secundária do Julgamento Substituição"},
     *     summary="Retorna a atividade secundária do julgamento do pedido de substituição.",
     *     description="Retorna a atividade secundária do julgamento do pedido de substituição.",
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
    public function getAtividadeSecundariaCadastroJulgamento($idPedidoImpugnacao)
    {
        $atividadeSecundario = $this->julgamentoImpugnacaoBO->getAtividadeSecundariaCadastroJulgamento(
            $idPedidoImpugnacao
        );
        return $this->toJson($atividadeSecundario);
    }

    /**
     * Retorna o julgamento de impugnação conforme o id do pedido impugnação informado.
     *
     * @param $idPedidoImpugnacao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/julgamentosImpugnacao/pedidoImpugnacao/{idPedidoImpugnacao}",
     *     tags={"Julgamento de impugnação"},
     *     summary="Dados da Julgamento de impugnação",
     *     description="Retorna o julgamento de impugnação conforme o id do pedido impugnação informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idPedidoImpugnacao",
     *         in="path",
     *         description="Id do Pedido Impugnação",
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
        $resp = $this->julgamentoImpugnacaoBO->getPorPedidoImpugnacao($idPedidoImpugnacao);
        return $this->toJson($resp);
    }

    /**
     * Retorna o julgamento de impugnação conforme o id do pedido impugnação informado com verificação para membro da comissão.
     *
     * @param $idPedidoImpugnacao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/julgamentosImpugnacao/membroComissao/pedidoImpugnacao/{idPedidoImpugnacao}",
     *     tags={"Julgamento de impugnação"},
     *     summary="Dados da Julgamento de impugnação",
     *     description="Retorna o julgamento de impugnação conforme o id do pedido impugnação informado com verificação para membro da comissão.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idPedidoImpugnacao",
     *         in="path",
     *         description="Id do Pedido Impugnação",
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
    public function getPorPedidoImpugnacaoMembroComissao($idPedidoImpugnacao)
    {
        $resp = $this->julgamentoImpugnacaoBO->getPorPedidoImpugnacao(
            $idPedidoImpugnacao, false, false, true
        );
        return $this->toJson($resp);
    }

    /**
     * Retorna o julgamento de impugnação conforme o id do pedido impugnação informado com verificação para responsável chapa ou impugnante.
     *
     * @param $idPedidoImpugnacao
     * @return string
     * @throws Exception
     * @OA\Get(
     *     path="/julgamentosImpugnacao/responsavel/pedidoImpugnacao/{idPedidoImpugnacao}",
     *     tags={"Julgamento de Substituição"},
     *     summary="Dados da Julgamento de Substituição",
     *     description="Retorna o julgamento de substituição conforme o id do pedido substituição informado com verificação para responsável chapa.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idPedidoImpugnacao",
     *         in="path",
     *         description="Id do Pedido Impugnação",
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
    public function getPorPedidoImpugnacaoResponsavel($idPedidoImpugnacao)
    {
        $resp = $this->julgamentoImpugnacaoBO->getPorPedidoImpugnacao(
            $idPedidoImpugnacao, false, true, false
        );
        return $this->toJson($resp);
    }

    /**
     * Salvar dados do julgamento do pedido de impugnação
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/julgamentosImpugnacao/salvar",
     *     tags={"Julgamento do Pedido de Impugnação"},
     *     summary="Salvar dados do julgamento do pedido de impugnação",
     *     description="Salvar dados do julgamento do pedido de impugnação",
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

        $julgamentoImpugnacaoTO = JulgamentoImpugnacaoTO::newInstance($data);

        $julgamentoImpugnacaoSalvo = $this->julgamentoImpugnacaoBO->salvar($julgamentoImpugnacaoTO);
        return $this->toJson($julgamentoImpugnacaoSalvo);
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
     *     path="/julgamentosImpugnacao/{id}/download",
     *     tags={"Julgamento do Pedido de Impugnação"},
     *     summary="Download de Documento do Julgamento de Impugnação",
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
        $arquivoTO = $this->julgamentoImpugnacaoBO->getArquivoJulgamentoImpugnacao($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Gera e retorna documento no formato PDF do julgamento de impugnação.
     *
     * @param $id
     * @return Response
     * @throws NegocioException
     * @throws MpdfException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/julgamentosImpugnacao/{id}/pdf",
     *     tags={"Julgamento Impugnação"},
     *     summary="Gera e retorna documento no formato PDF do julgamento de impugnação.",
     *     description="Gera e retorna documento no formato PDF do julgamento de impugnação.",
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
    public function getDocumentoPDFJulgamentoImpugnacao($id)
    {
        $documentoPDF = $this->julgamentoImpugnacaoBO->gerarDocumentoPDFJulgamentoImpugnacao($id);
        return $this->toFile($documentoPDF);
    }
}
