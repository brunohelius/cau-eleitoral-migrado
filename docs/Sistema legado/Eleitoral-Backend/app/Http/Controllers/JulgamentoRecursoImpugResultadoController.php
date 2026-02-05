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
use App\Business\JulgamentoRecursoImpugResultadoBO;
use App\Business\JulgamentoRecursoSubstituicaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Business\PedidoSubstituicaoChapaBO;
use App\Entities\JulgamentoSubstituicao;
use App\Exceptions\NegocioException;
use App\To\JulgamentoFinalTO;
use App\To\JulgamentoRecursoImpugnacaoTO;
use App\To\JulgamentoRecursoImpugResultadoTO;
use App\To\JulgamentoRecursoSubstituicaoTO;
use App\To\JulgamentoSubstituicaoTO;
use App\To\PedidoSubstituicaoChapaTO;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'JulgamentoRecursoImpugResultado'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class JulgamentoRecursoImpugResultadoController extends Controller
{

    /**
     * @var JulgamentoRecursoImpugResultadoBO
     */
    private $julgamentoRecursoImpugResultadoBO;
    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salvar dados do julgamento 2ª instância do pedido de impugnação de resultado
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/julgamentosRecursosImpugResultado/salvar",
     *     tags={"Julgamentos Finais"},
     *     summary="Salvar dados do julgamento 2ª instância do pedido de impugnação de resultado",
     *     description="Salvar dados do julgamento 2ª instância do pedido de impugnação de resultado",
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
        $julgamento = $this->getJulgamentoRecursoImpugResultadoBO()->salvar(
            JulgamentoRecursoImpugResultadoTO::newInstance(Input::all())
        );
        return $this->toJson($julgamento);
    }

    /**
     * Buscar julgamento/Homologação por id Pedido de impugnação de Resultado.
     * @param $idImpugResultado
     * @return string
     *
     * @OA\Get(
     *     path="/julgamentosRecursosImpugResultado/impugnacaoResultado/{idImpugResultado}",
     *     tags={"Julgamento" , "Impugnaçao de resultado"},
     *     summary="Julgamento 2ª instância do Recurso de Impugnaçao de resultadoo",
     *     description="Buscar Julgamento 2ª instância do Recurso de Impugnaçao de resultado por id Pedido de impugnaçao.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idImpugResultado",
     *         in="path",
     *         description="Id do pedido de impugnaçao",
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
    public function getPorImpugnacaoResultado($idImpugResultado) {
        $resp = $this->getJulgamentoRecursoImpugResultadoBO()->getPorImpugnacaoResultado($idImpugResultado);
        return $this->toJson($resp);
    }

    /**
     * Donwload de arquivo de julgamento de recurso de pedido de impugnaçao de resultado.
     * @param $idArquivo
     * @return Response
     *
     * @OA\Get(
     *     path="/julgamentosRecursosImpugResultado/{idArquivo}/donwload/",
     *     tags={"Julgamento" , "Impugnaçao de resultado", "Donwload", "Arquivo"},
     *     summary="Donwload arquivo de Julgamento 2ª instância do Recurso de Impugnaçao de resultadoo",
     *     description="Buscar arquivo Julgamento 2ª instância do Recurso de Impugnaçao de resultado por id Pedido de impugnaçao.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idArquivo",
     *         in="path",
     *         description="Id do pedido de impugnaçao",
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
    public function getArquivoJulgamentoRecursoPorId($idArquivo) {
        $resp = $this->getJulgamentoRecursoImpugResultadoBO()->getArquivoJulgamentoRecursoPorId($idArquivo);
        return $this->toFile($resp);
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoImpugResultadoBO'.
     *
     * @return JulgamentoRecursoImpugResultadoBO
     */
    private function getJulgamentoRecursoImpugResultadoBO()
    {
        if (empty($this->julgamentoRecursoImpugResultadoBO)) {
            $this->julgamentoRecursoImpugResultadoBO = app()->make(JulgamentoRecursoImpugResultadoBO::class);
        }

        return $this->julgamentoRecursoImpugResultadoBO;
    }
}
