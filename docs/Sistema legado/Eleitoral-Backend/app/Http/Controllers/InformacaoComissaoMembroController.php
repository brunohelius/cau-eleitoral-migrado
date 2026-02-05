<?php
/*
 * InformacaoComissaoMembroController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\HistoricoInformacaoComissaoMembroBO;
use App\Business\InformacaoComissaoMembroBO;
use App\Entities\DocumentoComissaoMembro;
use App\Entities\InformacaoComissaoMembro;
use App\Exceptions\NegocioException;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Support\Facades\Input;
use stdClass;

/**
 * Classe de controle referente a entidade 'InformacaoComissaoMembro'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class InformacaoComissaoMembroController extends Controller
{

    /**
     * @var InformacaoComissaoMembroBO
     */
    private $informacaoComissaoMembroBO;

    /**
     * @var HistoricoInformacaoComissaoMembroBO
     */
    private $historicoInformacaoComissaoMembroBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->informacaoComissaoMembroBO = app()->make(InformacaoComissaoMembroBO::class);
        $this->historicoInformacaoComissaoMembroBO =  app()->make(HistoricoInformacaoComissaoMembroBO::class);
    }

    /**
     * Salva um novo registro de informação comissão membro.
     *
     * @return string
     * @throws NegocioException
     * @throws Exception
     *
     * @OA\Post(
     *     path="/informacaoComissaoMembro",
     *     tags={"Comissão Membros"},
     *     summary="Salvar dados de Informação da Comissão dos Membros",
     *     description="Salva dados de Informação da Comissão dos Membros.",
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
        $informacaoComissaoMembro = InformacaoComissaoMembro::newInstance($data);
        $dadosTO = $this->getDadosTOSalvar($data);

        $informacaoComissaoMembroSalvo = $this->informacaoComissaoMembroBO->salvar($informacaoComissaoMembro, $dadosTO);
        return $this->toJson($informacaoComissaoMembroSalvo);
    }

    /**
     * Concluí o cadastro da Informação de Comissão Membro informada.
     *
     * @param $id
     * @return string
     * @throws Exception
     *
     * @OA\Post(
     *     path="/informacaoComissaoMembro/concluir",
     *     tags={"Concluir Comissão Membros"},
     *     summary="Conclui o cadastro de Informação da Comissão dos Membros",
     *     description="Conclui o cadastro de Informação da Comissão dos Membros.",
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
    public function concluir()
    {
        $data = Input::all();
        $documentoComissaoMembro = DocumentoComissaoMembro::newInstance($data);
        $informacaoComissaoMembro = $this->informacaoComissaoMembroBO->concluir($documentoComissaoMembro);
        return $this->toJson($informacaoComissaoMembro);
    }

    /**
     * Retorna os dados de Informação de Comissão de Membros pelo id do Calendário
     *
     * @param $idCalendario
     * @return string
     * @throws NonUniqueResultException
     * @throws NegocioException
     *
     * @OA\Get(
     *     path="/informacaoComissaoMembro/{idCalendario}",
     *     tags={"Buscar Comissão Membros id Calendario"},
     *     summary="Retorna os dados de Informação de Comissão de Membros pelo id do Calendário",
     *     description="Retorna os dados de Informação de Comissão de Membros pelo id do Calendário",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCalendario",
     *         in="path",
     *         description="Id do Calendário",
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
    public function getPorCalendario(int $idCalendario)
    {
        $resp = $this->informacaoComissaoMembroBO->getPorCalendario($idCalendario);
        return $this->toJson($resp);
    }

    /**
     * Retorna os dados de Histórico de Informação de Comissão de Membros pelo id da Informação
     *
     * @param $idInformacaoComissao
     * @return string
     *
     * @throws NegocioException
     *
     * @OA\Get(
     *     path="/informacaoComissaoMembro/{idInformacaoComissao}/historico",
     *     tags={"Buscar Histórico Comissão Membros id Informação"},
     *     summary="Retorna os dados de Histórico de Informação de Comissão de Membros pelo id da Informação",
     *     description="Retorna os dados de Histórico de Informação de Comissão de Membros pelo id da Informação",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idInformacaoComissao",
     *         in="path",
     *         description="Id da Comissão Membros",
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
    public function getHistoricoPorInformacaoComissao($idInformacaoComissao)
    {
        $resp = $this->historicoInformacaoComissaoMembroBO->getPorInformacaoComissaoMembro($idInformacaoComissao);
        return $this->toJson($resp);
    }

    /**
     *
     * @param $data
     * @return stdClass
     */
    private function getDadosTOSalvar($data) {
        $dadosTO = new stdClass();
        $dadosTO->email = Utils::getValue('email', $data);

        return $dadosTO;
    }
}
