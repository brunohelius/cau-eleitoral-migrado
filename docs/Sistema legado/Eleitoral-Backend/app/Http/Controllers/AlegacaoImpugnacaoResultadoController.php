<?php
/*
 * ImpugnacaoResultadoController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\AlegacaoImpugnacaoResultadoBO;
use App\Exceptions\NegocioException;
use App\To\AlegacaoImpugnacaoResultadoTO;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'AlegacaoImpugnacaoResultado'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class AlegacaoImpugnacaoResultadoController  extends Controller
{

    /**
     * @var AlegacaoImpugnacaoResultadoBO
     */
    private $alegacaoImpugnacaoResultadoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->alegacaoImpugnacaoResultadoBO = app()->make(AlegacaoImpugnacaoResultadoBO::class);
    }

    /**
     * Salvar dados da Impugnacao do Resultado
     *
     *
     * @return string
     * @throws Exception
     * @throws \Exception
     * @OA\Post(
     *     path="/alegacaoImpugnacaoResultado/salvar",
     *     tags={"Impugnacao  Resultado"},
     *     summary="Salvar dados da Alegação de Impugnacao do Resultado",
     *     description="Salvar dados da Alegação do Resultado",
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
        $alegacaoImpugnacaoResultadoTO = AlegacaoImpugnacaoResultadoTO::newInstance($data);
        $alegacao = $this->alegacaoImpugnacaoResultadoBO->salvar($alegacaoImpugnacaoResultadoTO);

        return $this->toJson($alegacao);
    }


    /**
     * Retorna a Alegaçãao a partir do id da Impugnacao Resultado.
     *
     * @param $uf
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="/alegacaoImpugnacaoResultado/{idImpugnacao}/alegacao",
     *     tags={"Impugnacao de Resultado", "Alegacao"},
     *     summary="Retorna a Alegaçãao a partir do id da Impugnacao Resultado.",
     *     description="Retorna a Alegaçãao a partir do id da Impugnacao Resultado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idImpugnacao",
     *         in="path",
     *         description="Id da Impugnacao Resultado",
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
    public function getAlegacaoPorIdImpugnacao($idImpugnacao)
    {
        $resp = $this->alegacaoImpugnacaoResultadoBO->getAlegacaoPorIdImpugnacao($idImpugnacao);
        return $this->toJson($resp);
    }

    /**
     * Retorna To para validação de botão de cadastro de alegação.
     *
     * @param $idImpugnacao
     * @return string
     * @throws NegocioException
     */
    public function getValidacaoCadastroAlegacaoTO($idImpugnacao)
    {
        $resp = $this->alegacaoImpugnacaoResultadoBO->getValidacaoCadastroAlegacaoTO($idImpugnacao);
        return $this->toJson($resp);
    }

    /**
     * Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' do documento informado.
     *
     * @param $id
     *
     * @return Response
     * @throws NegocioException
     * @OA\Get(
     *     path="/alegacaoImpugnacaoResultado/documento/{idAlegacao}/download",
     *     tags={"Arquivo da Alegação de Impugnacao de Resultado"},
     *     summary="Download de Documento do Pedido de Impugnação Chapa",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' da alegação informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Pedido de Impugnação Chapa",
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
    public function downloadDocumento($idAlegacao)
    {
        $arquivoTO = $this->alegacaoImpugnacaoResultadoBO->downloadDocumento($idAlegacao);
        return $this->toFile($arquivoTO);
    }
}
