<?php
/*
 * FilialController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\FilialBO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

/**
 * Classe de controle referente a entidade 'Filial'.
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use OpenApi\Annotations as OA;
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class FilialController extends Controller
{
    /**
     * @var FilialBO
     */
    private $filialBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->filialBO = app()->make(FilialBO::class);
    }

    /**
     * Método que retorna Historico Extrato Conselheiro por atividade secundaria
     *
     * @return string
     *
     * @OA\Get(
     *     path="/filiais/ies",
     *     tags={"filiais", "ies"},
     *     summary="Método que retorna Historico Extrato Conselheiro por atividade secundaria",
     *     description="Método que retorna Historico Extrato Conselheiro por atividade secundaria",
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
    public function getFiliaisIES()
    {
        $resp = $this->getFilialBO()->getFiliaisIES();
        return $this->toJson($resp);
    }

    /**
     * Retorna a instancia de Filial BO
     *
     * @return FilialBO
     */
    private function getFilialBO()
    {
        if (empty($this->filialBO)) {
            $this->filialBO = new FilialBO();
        }
        return $this->filialBO;
    }

    /**
     * Retorna todas as filiais.
     *
     * @return string
     *
     * @OA\Get(
     *     path="/filiais",
     *     tags={"Filiais"},
     *     summary="Filiais",
     *     description="Retorna todas as filiais.",
     *     security={{ "Authorization": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Filial")
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getFiliais()
    {
        $filiais = $this->filialBO->getFiliais();
        return $this->toJson($filiais);
    }

    /**
     * Retorna a filial conforme o identificador informado.
     *
     * @param $id
     * @return string
     *
     * @OA\Get(
     *     path="/filiais/{id}",
     *     tags={"Filiais"},
     *     summary="Filiais",
     *     description="Retorna a filial conforme o identificador informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Filial",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Filial")
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getPorId($id)
    {
        $filial = $this->filialBO->getPorId($id);
        return $this->toJson($filial);
    }

    /**
     * Retorna todas as filiais com suas respectivas imagens referentes á bandeira.
     *
     * @return string
     *
     * @OA\Get(
     *     path="/filiais/bandeira",
     *     tags={"filial"},
     *     summary="Filiais",
     *     description="Retorna todas as filiais com suas respectivas imagens referentes á bandeira.",
     *     security={{ "Authorization": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Filial")
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     * @throws NegocioException
     */
    public function getFiliaisComBandeiras()
    {
        $filiais = $this->filialBO->getFiliaisComBandeiras();
        return $this->toJson($filiais);
    }

    /**
     * Retorna filial de acordo com id informado com a respectiva bandeira.
     *
     * @param $id
     * @return string
     *
     * @throws NegocioException
     * @OA\Get(
     *     path="/filiais/{id}/bandeira",
     *     tags={"filial"},
     *     summary="Filial com Bandeira",
     *     description="Retorna filial de acordo com id informado com a respectiva bandeira.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Filial",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A requisição foi bem sucedida.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Filial")
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getFilialComBandeira($id)
    {
        $filiais = $this->filialBO->getFilialComBandeira($id);
        return $this->toJson($filiais);
    }

    /**
     * Retorna as filiais (com bandeiras) associadas ao calendário informado.
     *
     * @param int $idCalendario
     * @return string
     * @throws NegocioException
     *
     * @OA\Get(
     *     path="/filiais/bandeira/calendario/{idCalendario}",
     *     tags={"filial"},
     *     summary="Filiais com Bandeira, por Calendário",
     *     description="Retorna as filiais associadas ao calendário informado.",
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
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Filial")
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getFiliaisComBandeirasPorCalendario($idCalendario)
    {
        $filiais = $this->filialBO->getFiliaisComBandeirasPorCalendario($idCalendario);
        return $this->toJson($filiais);
    }

    /**
     * Retorna a lista de filiais, associadas às UF's que ainda não tiveram Membros de Comissão cadastradas, de um
     * determinado calendário.
     *
     * @param int $idCalendario
     * @return string
     * @throws NegocioException
     * @throws NonUniqueResultException
     *
     * @OA\Get(
     *     path="/filiais/calendario/{idCalendario}/comissao",
     *     tags={"filial"},
     *     summary="Filiais de Comissões de Membros não cadastrados, por Calendário",
     *     description="Retorna a lista de filiais, associadas às UF's que ainda não tiveram Membros de Comissão cadastradas, de um determinado calendário.",
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
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Filial")
     *         )
     *     ),
     *     @OA\Response(response=400, description="O servidor não pode ou não processará a requisição, devido a algo que parece ser um erro do cliente."),
     *     @OA\Response(response=403, description="O servidor entendeu a requisição, mas se recusou a autorizá-la."),
     *     @OA\Response(response=500, description="O servidor encontrou uma condição inesperada que impediu execução da requisição.")
     * )
     */
    public function getFiliaisMembrosNaoCadastradosPorCalendario($idCalendario)
    {
        $filiais = $this->filialBO->getFiliaisMembrosNaoCadastradosPorCalendario($idCalendario);
        return $this->toJson($filiais);
    }
}
