<?php
/*
 * DenunciaAudienciaInstrucaoController* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\DenunciaAudienciaInstrucaoBO;

use App\Business\DenunciaProvasBO;
use App\Entities\DenunciaAudienciaInstrucao;

use App\Exceptions\NegocioException;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Mpdf\MpdfException;
use stdClass;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe de controle referente a entidade 'DenunciaAudienciaInstrucao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class DenunciaAudienciaInstrucaoController extends Controller
{
    /**
     * @var \App\Business\DenunciaAudienciaInstrucaoBO
     */
    private $denunciaAudienciaInstrucaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }

    /**
     * Salva a Denuncia Audiencia Instrução
     *
     * @return string
     * @throws \Exception
     * @OA\Post(
     *     path="denuncia/audienciaInstrucao/salvar",
     *     tags={"Inserir Audiencia Instrução"},
     *     summary="Inserir Audiencia Instrução",
     *     description="Inserir Audiencia Instrução",
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
        $data = Input::all();
        $denunciaAudiencia = DenunciaAudienciaInstrucao::newInstance($data);
        $resp = $this->getDenunciaAudienciaInstrucaoBO()->salvar($denunciaAudiencia);
        return $this->toJson($resp);
    }

    /**
     * Retorna a audiencia de instrucao conforme o id informado.
     *
     * @param $id
     * @return string
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="audienciainstrucao/{id}",
     *     tags={"Audiencia de Instrucao"},
     *     summary="Dados da audiencia de instrucao",
     *     description="Retorna a audiencia de instrucao conforme o id informado.",
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
    public function getPorId($id)
    {
        $resp = $this->getDenunciaAudienciaInstrucaoBO()->getPorId($id);
        return $this->toJson($resp);
    }

    /**
     * Retorna a instancia de DenunciaAudienciaInstrucaoBO
     *
     * @return DenunciaAudienciaInstrucaoBO
     */
    private function getDenunciaAudienciaInstrucaoBO()
    {
        if (empty($this->denunciaAudienciaInstrucaoBO)) {
            $this->denunciaAudienciaInstrucaoBO = app()->make(DenunciaAudienciaInstrucaoBO::class);
        }
        return $this->denunciaAudienciaInstrucaoBO;
    }

    /**
     * Disponibiliza o arquivo 'Encaminhamento Audiência de Instrução' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="audienciaInstrucao/documento/{idArquivoAudienciaInstrucao}/download",
     *     tags={"audiencia", "instrucao", "arquivos"},
     *     summary="Disponibiliza o arquivo Encaminhamento Audiência de Instrução para download conforme o id informado",
     *     description="Disponibiliza o arquivo Encaminhamento Audiência de Instrução para download conforme o id informado.",
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
    public function download($idArquivo)
    {
        $arquivoTO = $this->getDenunciaAudienciaInstrucaoBO()->getArquivo($idArquivo);
        return $this->toFile($arquivoTO);
    }
}
