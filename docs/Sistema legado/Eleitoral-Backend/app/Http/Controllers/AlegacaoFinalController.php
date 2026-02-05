<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 24/02/2019
 * Time: 11:25
 */

namespace App\Http\Controllers;

use App\Business\AlegacaoFinalBO;
use App\Config\Constants;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\To\AlegacaoFinalTO;
use App\To\ArquivoGenericoTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'AlegacaoFinal'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class AlegacaoFinalController extends Controller
{
    /**
     * @var AlegacaoFinalBO
     */
    private $alegacaoFinalBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }

    /**
     * Salva a alegação final do encaminhamento.
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @OA\Post(
     *     path="encaminhamentosDenuncia/alegacaoFinal/salvar",
     *     tags={"encaminhamento", "alegação final", "salvar"},
     *     summary="Salva a alegação final do encaminhamento",
     *     description="Salva a alegação final do encaminhamento",
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
        $alegacaoFinal = AlegacaoFinalTO::newInstance($data);
        $resp = $this->getAlegacaoFinalBO()->salvar($alegacaoFinal);
        return $this->toJson($resp);
    }

    /**
     * Realiza a validação de arquivo de alegação final.
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     *
     * @OA\Post(
     *     path="encaminhamentosDenuncia/alegacaoFinal/validarArquivo",
     *     tags={"encaminhamento", "alegação final", "arquivo"},
     *     summary="Realiza a validação de arquivo de alegação final",
     *     description="Realiza a validação de arquivo de alegação final",
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
    public function validarArquivo()
    {
        $data = Input::all();
        $arquivoAlegacaoFinal = ArquivoGenericoTO::newInstance($data);
        $this->getAlegacaoFinalBO()->validarArquivo($arquivoAlegacaoFinal);

        return response()->make('',200);
    }
    
    /**
     * Disponibiliza o arquivo 'Alegação final' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="encaminhamentosDenuncia/alegacaoFinal/arquivo/{idArquivo}/download",
     *     tags={"denuncia", "arquivos"},
     *     summary="Download de Arquivo da alegação final",
     *     description="Disponibiliza o arquivo 'Alegação final' para 'download' conforme o 'id' informado.",
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
        $arquivoTO = $this->getAlegacaoFinalBO()->getArquivo($idArquivo);        
        return $this->toFile($arquivoTO);
    }

    
    /**
     * Retorna a alegação final conforme o id informado.
     *
     * @param $id
     * @return string
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="encaminhamentosDenuncia/alegacaoFinal/{id}",
     *     tags={"Alegação Final"},
     *     summary="Dados da Alegação Final",
     *     description="Retorna a Alegação Final conforme o id informado.",
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
    public function getPorEncaminhamento($id)
    {
        $resp = $this->getAlegacaoFinalBO()->getPorEncaminhamento($id);
        return $this->toJson($resp);
    }
    
    /**
     * Retorna a instancia de AlegacaoFinalBO
     *
     * @return AlegacaoFinalBO|mixed
     */
    private function getAlegacaoFinalBO()
    {
        if (empty($this->alegacaoFinalBO)) {
            $this->alegacaoFinalBO = app()->make(AlegacaoFinalBO::class);
        }
        return $this->alegacaoFinalBO;
    }
}
