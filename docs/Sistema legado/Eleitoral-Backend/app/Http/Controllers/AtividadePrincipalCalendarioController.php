<?php
/*
 * AtividadePrincipalCalendarioController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\AtividadePrincipalBO;
use Illuminate\Support\Facades\Input;
use App\To\AtividadePrincipalCalendarioTO;
use App\To\AtividadePrincipalFiltroTO;
use App\Util\Utils;

/**
 * Classe de controle referente a entidade 'AtividadePrincipalCalendario'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class AtividadePrincipalCalendarioController extends Controller
{
    /**
     *
     * @var AtividadePrincipalBO
     */
    private $atividadePrincipalBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->atividadePrincipalBO = app()->make(AtividadePrincipalBO::class);
    }

    /**
     * Retorna as atividades principais do calendario conforme o id informado.
     *
     * @param $idCalendario
     * @return string
     *
     * @OA\Get(
     *     path="/calendarios/atividadesPrincipais/{idCalendario}",
     *     tags={"Calendários", "Atividade Principal"},
     *     summary="Busca de Atividades Principais",
     *     description="Retorna as atividades principais do calendario conforme o id informado.",
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
    public function getAtividadePrincipalPorCalendario($idCalendario)
    {
        $resp = $this->atividadePrincipalBO->getAtividadePrincipalPorCalendario($idCalendario);
        return $this->toJson($resp);
    }

    /**
     * Retorna as atividades principais do calendario conforme o id do calendario e o filtro informado.
     *
     * @param integer $idCalendario
     * @return string
     *
     * @OA\Get(
     *     path="/calendarios/{idCalendario}/atividadesPrincipais/filtro",
     *     tags={"Calendários", "Atividade Principal"},
     *     summary="Busca de Atividades Principais",
     *     description="Retorna as atividades principais do calendario conforme o id do calendário e filtro informados.",
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
    public function getAtividadePrincipalPorCalendarioComFiltro($idCalendario)
    {
        $data = Input::all();
        $filtroTO = $this->getAtividadePrincipalFiltro($data);
        $atividades = $this->atividadePrincipalBO->getAtividadePrincipalPorCalendarioComFiltro($idCalendario, $filtroTO);
        return $this->toJson($atividades);
    }

    public function getAtividadePrincipal(){
        $atividadesPrincipais = $this->atividadePrincipalBO->getAtividadePrincipal();
        return $this->toJson($atividadesPrincipais);
    }


    /**
     * Cria o objeto de filtro de Atividade Principal
     *
     * @param $data
     * @return AtividadePrincipalCalendarioTO
     */
    private function getFiltro($data)
    {
        $filtroTO = AtividadePrincipalCalendarioTO::newInstance($data);
        return $filtroTO;
    }

    /**
     * Método utilizado para criar o objeto de filtro de Atividade Principal
     *
     * @param array $data
     * @return \App\To\AtividadePrincipalFiltroTO
     */
    private function getAtividadePrincipalFiltro($data = null)
    {
        $filtroTO = new AtividadePrincipalFiltroTO();
        $filtroTO->setAtividadeSecundariaDataInicio(Utils::getValue('atividadeSecundariaDataInicio', $data));
        $filtroTO->setAtividadeSecundariaDataFim(Utils::getValue('atividadeSecundariaDataFim', $data));
        return $filtroTO;
    }
}
