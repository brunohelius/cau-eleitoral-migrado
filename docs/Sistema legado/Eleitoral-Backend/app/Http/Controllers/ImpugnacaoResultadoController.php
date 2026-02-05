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

use App\Business\EleicaoBO;
use App\Entities\ImpugnacaoResultado;
use App\Exceptions\NegocioException;
use App\To\ImpugnacaoResultadoTO;
use Illuminate\Http\Request;
use App\Business\ImpugnacaoResultadoBO;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'ImpugnacaoResultado'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class ImpugnacaoResultadoController  extends Controller
{


    /**
     * @var ImpugnacaoResultadoBO
     */
    private $impugnacaoResultadoBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->impugnacaoResultadoBO = app()->make(ImpugnacaoResultadoBO::class);
        $this->eleicaoBO = app()->make(EleicaoBO::class);
    }

    /**
     * Retorna as Cau Uf's de acordo com as regras da impugnação de resultado.
     *
     * @param $idProfissional
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="/impugnacaoResultado/{idProfissional}/getCauUf",
     *     tags={"Impugnacao de Resultado", "Get CAU UF"},
     *     summary="Retorna as Cau Uf's de acordo com as regras da impugnação de resultado.",
     *     description="Retorna as Cau Uf's de acordo com as regras da impugnação de resultado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idProfissional",
     *         in="path",
     *         description="Id do Profissional",
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
    public function getCauUf()
    {
        $resp = $this->impugnacaoResultadoBO->getCauUf();
        return $this->toJson($resp);
    }

    /**
     * Retorna a impugnação de resultado a partir do id.
     *
     * @param $idProfissional
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="/impugnacaoResultado/{id}/getImpugnacaoPorId",
     *     tags={"Impugnacao de Resultado"},
     *     summary="Retorna a impugnação de resultado a partir do id.",
     *     description="Retorna a impugnação de resultado a partir do id",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id",
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
    public function getImpugnacaoPorId($id)
    {
        $resp = $this->impugnacaoResultadoBO->getImpugnacaoPorId(
            $id, false, false, false, true
        );
        return $this->toJson($resp);
    }

    /**
     * Retorna a impugnação de resultado a partir do id com verificação de comissão.
     *
     * @param $idProfissional
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="/impugnacaoResultado/comissao/{idImpugnacao}/getImpugnacaoPorId",
     *     tags={"Impugnacao de Resultado"},
     *     summary="Retorna a impugnação de resultado a partir do id com verificação de comissão.",
     *     description="Retorna a impugnação de resultado a partir do id com verificação de comissão.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idImpugnacao",
     *         in="path",
     *         description="Id",
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
    public function getImpugnacaoComVerificacaoComissaoPorId($idImpugnacao)
    {
        $resp = $this->impugnacaoResultadoBO->getImpugnacaoPorId($idImpugnacao, true);
        return $this->toJson($resp);
    }

    /**
     * Retorna a impugnação de resultado a partir do id com verificação de chapa.
     *
     * @param $idProfissional
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="/impugnacaoResultado/chapa/{idImpugnacao}/getImpugnacaoPorId",
     *     tags={"Impugnacao de Resultado"},
     *     summary="Retorna a impugnação de resultado a partir do id com verificação de chapa.",
     *     description="Retorna a impugnação de resultado a partir do id com verificação de chapa.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idImpugnacao",
     *         in="path",
     *         description="Id",
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
    public function getImpugnacaoComVerificacaoChapaPorId($idImpugnacao)
    {
        $resp = $this->impugnacaoResultadoBO->getImpugnacaoPorId($idImpugnacao, false, true);
        return $this->toJson($resp);
    }

    /**
     * Retorna a impugnação de resultado a partir do id com verificação de impugnante.
     *
     * @param $idProfissional
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="/impugnacaoResultado/impugnante/{idImpugnacao}/getImpugnacaoPorId",
     *     tags={"Impugnacao de Resultado"},
     *     summary="Retorna a impugnação de resultado a partir do id com verificação de impugnante.",
     *     description="Retorna a impugnação de resultado a partir do id com verificação de impugnante.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idImpugnacao",
     *         in="path",
     *         description="Id",
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
    public function getImpugnacaoComVerificacaoImpugnantePorId($idImpugnacao)
    {
        $resp = $this->impugnacaoResultadoBO->getImpugnacaoPorId($idImpugnacao, false, false, true);
        return $this->toJson($resp);
    }

    /**
     * Salvar dados da Impugnacao do Resultado
     *
     *
     * @return string
     * @throws Exception
     * @throws \Exception
     * @OA\Post(
     *     path="/impugnacaoResultado/salvar",
     *     tags={"Impugnacao  Resultado"},
     *     summary="Salvar dados da Impugnacao do Resultado",
     *     description="Salvar dados da Impugnacao do Resultado",
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
        $impugnacaoTO = ImpugnacaoResultadoTO::newInstance($data);

        $impugnacao = $this->impugnacaoResultadoBO->salvar($impugnacaoTO);
        return $this->toJson($impugnacao);
    }

    /**
     * Retorna o pedido de Impugnação a partir da uf.
     *
     * @param $uf
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="/impugnacaoResultado/{uf}",
     *     tags={"Impugnacao de Resultado", "Get CAU UF"},
     *     summary="Retorna o pedido de Impugnação a partir da uf.",
     *     description="Retorna o pedido de Impugnação a partir da uf.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idProfissional",
     *         in="path",
     *         description="Id do Profissional",
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
    public function getPorUf($uf)
    {
        $resp = $this->impugnacaoResultadoBO->getPorUf($uf);
        return $this->toJson($resp);
    }

    /**
     * Retorna a verificação de duplicidade do pedido de impugnação
     *
     * @param $uf
     * @return string
     * @OA\Post(
     *     path="/impugnacaoResultado/verificacaoDuplicidade",
     *     tags={"Impugnacao de Resultado"},
     *     summary="Retorna a verificação de duplicidade do pedido de impugnação",
     *     description="Retorna a verificação de duplicidade do pedido de impugnação",
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
    public function getVerificacaoDuplicidadePedido()
    {
        $data = Input::all();

        $resp = $this->impugnacaoResultadoBO->getVerificacaoDuplicidadePedido(ImpugnacaoResultadoTO::newInstance($data));
        return $this->toJson($resp);
    }

    /**
     *  Retorna o pedido de Impugnação a partir do profissional.
     *
     * @return string
     * @OA\Get(
     *     path="/impugnacaoResultado/acompanharMeusPedidos",
     *     tags={"Impugnacao de Resultado", "Get CAU UF"},
     *     summary="Retorna o pedido de Impugnação a partir do profissional Logado.",
     *     description="Retorna o pedido de Impugnação a partir do profissional Logad.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idUf",
     *         in="path",
     *         description="Id do UF",
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
    public function acompanharParaProfissional($idUf)
    {
        $resp = $this->impugnacaoResultadoBO->acompanharParaProfissional($idUf);
        return $this->toJson($resp);
    }

    /**
     * Retorna o pedido de Impugnação relacionados ao UF da Chapa do profissional logado.
     *
     * @return string
     * @throws NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="/impugnacaoResultado/acompanhar",
     *     tags={"Impugnacao de Resultado", "Get CAU UF"},
     *     summary=" Retorna o pedido de Impugnação relacionados ao UF da Chapa do profissional logado.",
     *     description="Retorna o pedido de Impugnação relacionados ao UF da Chapa do profissional logado.",
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
    public function acompanharParaChapa()
    {
        $resp = $this->impugnacaoResultadoBO->acompanharParaChapa();
        return $this->toJson($resp);
    }

    /**
     * Retorna o pedido de Impugnação relacionados ao UF da Chapa do profissional Membro da comissão logado.
     *
     * @param $idUf
     * @return string
     * @throws NegocioException
     * @OA\Get(
     *     path="/impugnacaoResultado/acompanhar/membroComissao/{idUf}",
     *     tags={"Impugnacao de Resultado", "Get CAU UF"},
     *     summary="Acompanhar impugnação de Resultado para Membros da comissão.",
     *     description="Retorna o pedido de Impugnação relacionados ao UF da Chapa do profissional Membro da comissão logado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idUf",
     *         in="path",
     *         description="Id do UF",
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
     *  )
     */
    public function acompanharParaMembroComissao($idUf)
    {
        $resp = $this->impugnacaoResultadoBO->acompanharParaMembroComissao($idUf);
        return $this->toJson($resp);
    }

    /**
     * Retorna o pedido de Impugnação relacionados por UF e Calendário.
     *
     * @param $idUf
     * @param $idCalendario
     * @return string
     * @throws NegocioException
     * @OA\Get(
     *     path="/impugnacaoResultado/acompanhar/corporativo/calendario/{idCalendario}/uf/{idUf}",
     *     tags={"Impugnacao de Resultado", "Get CAU UF"},
     *     summary="Acompanhar impugnação de Resultado Corporátivo.",
     *     description="Retorna o pedido de Impugnação relacionados por UF e Calendário..",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idUf",
     *         in="path",
     *         description="Id do UF",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idCalendario",
     *         in="path",
     *         description="Id do Celendário",
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
     *  )
     */
    public function acompanharParaCorporativo($idUf, $idCalendario) {
        $resp = $this->impugnacaoResultadoBO->acompanharParaCorporativo($idUf, $idCalendario);
        return $this->toJson($resp);
    }

    /**
     * Recupera as eleicoes que possuem pedidos
     *
     * @return string
     * @throws \Exception
     *
     * @OA\Get(
     *     path="/impugnacaoResultado/eleicoes",
     *     tags={"Pedido de impugnaçao de resultado"},
     *     summary="Recupera as eleicoes que possuem pedidos de impugnaçao de resultado",
     *     description="Recupera as eleicoes que possuem pedidos de impugnaçao de resultado",
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
    public function getEleicoesComPedidoImpugnacaoResultado()
    {
        $eleicoes = $this->eleicaoBO->getEleicoesComPedidoImpugnacaoResultado();
        return $this->toJson($eleicoes);
    }

    /**
     * Recupera a quantidade de pedidos de impugnaçao de resultado para cada UF
     *
     * @param null $idCalendario
     * @return string
     * @OA\Get(
     *     path="/impugnacaoResultado/quantidadeParaCadaUf/{idCalendario}",
     *     tags={"Pedido de impugnaçao de resultado"},
     *     summary="Recupera a quantidade de pedidos de impugnaçao de resultado para cada UF",
     *     description="Recupera a quantidade de pedidos de impugnaçao de resultado para cada UF",
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
    public function getQuantidadeImpugnacaoResultadoParaCadaUf($idCalendario = null)
    {
        $eleicoes = $this->impugnacaoResultadoBO->getQuantidadeImpugnacaoResultadoParaCadaUf($idCalendario);
        return $this->toJson($eleicoes);
    }

    /**
     * Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' do documento informado.
     *
     * @param $id
     *
     * @return Response
     * @throws NegocioException
     * @OA\Get(
     *     path="/impugnacaoResultado/documento/{idDocumento}/download",
     *     tags={"Arquivo Pedido de Impugnação"},
     *     summary="Download de Documento do Pedido de Impugnação Chapa",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' do documento informado.",
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
    public function downloadDocumento($id)
    {
        $arquivoTO = $this->impugnacaoResultadoBO->getArquivoImpugnacao($id);
        return $this->toFile($arquivoTO);
    }

    /**
     * Recupera a quantidade de pedidos de impugnaçao de resultado para cada UF
     *
     * @param null $idCalendario
     * @return string
     * @OA\Get(
     *     path="/impugnacaoResultado/comissao/quantidadeParaCadaUf",
     *     tags={"Pedido de impugnaçao de resultado"},
     *     summary="Recupera a quantidade de pedidos de impugnaçao de resultado para cada UF",
     *     description="Recupera a quantidade de pedidos de impugnaçao de resultado para cada UF",
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
     * @throws NegocioException
     */
    public function getQuantidadeImpugnacaoResultParaCadaUfPorMembroComissao()
    {
        $eleicoes = $this->impugnacaoResultadoBO->getQtdImpugnacaoResultadoParaCadaUfPorComissao();
        return $this->toJson($eleicoes);
    }

    /**
     * Recupera a quantidade de pedidos de impugnaçao de resultado para cada UF
     *
     * @param null $idCalendario
     * @return string
     * @OA\Get(
     *     path="/impugnacaoResultado/comissao/quantidadeParaCadaUf/impugnante",
     *     tags={"Pedido de impugnaçao de resultado"},
     *     summary="Recupera a quantidade de pedidos de impugnaçao de resultado para cada UF",
     *     description="Recupera a quantidade de pedidos de impugnaçao de resultado para cada UF",
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
     * @throws NegocioException
     */
    public function getQtdImpugnacaoResultadoParaCadaUfPorImpugnante()
    {
        $eleicoes = $this->impugnacaoResultadoBO->getQtdImpugnacaoResultadoParaCadaUfPorImpugnante();
        return $this->toJson($eleicoes);
    }
}
