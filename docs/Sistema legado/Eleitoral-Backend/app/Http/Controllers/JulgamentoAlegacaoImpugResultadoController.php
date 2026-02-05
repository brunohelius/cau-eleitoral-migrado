<?php
/*
 * ChapaEleicaoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\JulgamentoAlegacaoImpugResultadoBO;
use App\To\JulgamentoAlegacaoImpugResultadoTO;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'SubstituicaoJulgamentoFinal'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class JulgamentoAlegacaoImpugResultadoController extends Controller
{

    /**
     * @var JulgamentoAlegacaoImpugResultadoBO
     */
    private $julgamentoAlegacaoImpugResultadoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Retorna julgamento da alegação impugnação de resultado em 1ª Instância.
     *
     * @param $idImpugnacao
     * @return string
     *
     *  @OA\Get(
     *     path="/julgamentoAlegacaoImpugnacaoResultados/{idImpugnacao}",
     *     tags={"Julgamento Alegação Impugnação Resultado"},
     *     summary="Julgamento da alegação de pedido de Impugnação Resultado",
     *     description="Disponibiliza e Retorna o julgamento da alegação de impugnação de resultado em 1ª Instância.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id Julgamento Alegação",
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
    public function getJulgamentoAlegacaoPorImpugnacaoResultado($idImpugnacao)
    {
        $resp = $this->getJulgamentoAlegacaoImpugResultadoBO()->getJulgamentoAlegacaoPorImpugnacaoResultado($idImpugnacao);
        return $this->toJson($resp);
    }

    public function downloadDocumento($idJulgamento)
    {
        $resp = $this->getJulgamentoAlegacaoImpugResultadoBO()->getArquivoJulgamento($idJulgamento);
        return $this->toFile($resp);
    }

    /**
     * Salvar julgamento de alegação impugnação de resultado em 1ª Instância.
     *
     *  @OA\Post(
     *     path="julgamentoAlegacaoImpugnacaoResultado/salvar",
     *     tags={"julgamentoAlegacaoImpugnacaoResultado", "Julgamento", "Alegação impugnação de resultado"},
     *     summary="Salvar julgamento de alegação",
     *     description="Salvar julgamento de alegação impugnação de resultado em 1ª Instância.",
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
    public function salvar()
    {
        $julgamentoAlegacaoImpugResultadoTO = JulgamentoAlegacaoImpugResultadoTO::newInstance(Input::all());
        $resp = $this->getJulgamentoAlegacaoImpugResultadoBO()->salvar($julgamentoAlegacaoImpugResultadoTO);
        return $this->toJson($resp);
    }

    /**
     * Retorna uma nova instância de 'JulgamentoAlegacaoImpugResultadoBO'.
     *
     * @return JulgamentoAlegacaoImpugResultadoBO
     */
    private function getJulgamentoAlegacaoImpugResultadoBO()
    {
        if (empty($this->julgamentoAlegacaoImpugResultadoBO)) {
            $this->julgamentoAlegacaoImpugResultadoBO = app()->make(JulgamentoAlegacaoImpugResultadoBO::class);
        }

        return $this->julgamentoAlegacaoImpugResultadoBO;
    }
}
