<?php
/*
 * ParecerFinalController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\ParecerFinalBO;
use App\Config\Constants;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\To\ParecerFinalTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'AlegacaoFinal'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class ParecerFinalController extends Controller
{
    /**
     * @var ParecerFinalBO
     */
    private $parecerFinalBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }

    /**
     * Salva o parecer final da denúncia.
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     *
     * @OA\Post(
     *     path="encaminhamentosDenuncia/parecerFinal/salvar",
     *     tags={"encaminhamento", "parecer final", "salvar"},
     *     summary="Salva o parecer final da denúncia",
     *     description="Salva o parecer final da denúncia",
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
        $parecerFinalTO = ParecerFinalTO::newInstance($data);
        $resp = $this->getParecerFinalBO()->salvar($parecerFinalTO);
        return $this->toJson($resp);
    }

    /**
     * Busca Parecer Final utilizando id de Encaminhamento denúncia.
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     *
     * @OA\Post(
     *     path="encaminhamentosDenuncia/parecerFinal/{iodEncaminhamento}}",
     *     tags={"encaminhamento", "parecer final",},
     *     summary="Buscar o parecer final da denúncia",
     *     description="Busca Parecer Final utilizando id de Encaminhamento denúncia",
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
    public function getParecerFinalPorEncaminhamento($idEncaminhamento)
    {
        $resp =  $this->getParecerFinalBO()->getPorEncaminhamento($idEncaminhamento);
        return $this->toJson($resp);
    }

    /**
     * Disponibiliza o arquivo 'encaminhamento parecer final' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="encaminhamentoDenuncia/parecerFinal/arquivo/{idArquivo}/download",
     *     tags={"denuncia", "encaminhamento", "arquivos"},
     *     summary="Download de Arquivo do Encaminhamento",
     *     description="Disponibiliza o arquivo 'encaminhamento' para 'download' conforme o 'id' informado.",
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
        $arquivoTO = $this->getParecerFinalBO()->getArquivo($idArquivo);
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna a instancia de ParecerFinalBO
     *
     * @return ParecerFinalBO|mixed
     */
    private function getParecerFinalBO()
    {
        if (empty($this->parecerFinalBO)) {
            $this->parecerFinalBO = app()->make(ParecerFinalBO::class);
        }
        return $this->parecerFinalBO;
    }
}
