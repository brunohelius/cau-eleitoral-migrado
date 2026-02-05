<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 12/11/2019
 * Time: 16:56
 */

namespace App\Http\Controllers;

use App\Business\ParametroConselheiroBO;
use App\Config\Constants;
use App\Entities\ParametroConselheiro;
use App\Exceptions\NegocioException;
use App\To\CalendarioFiltroTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use stdClass;

/**
 * Classe de controle referente a entidade 'ParametroConselheiro'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class ParametroConselheiroController extends Controller
{
    /**
     * @var ParametroConselheiroBO
     */
    private $parametroConselheiroBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }

    /**
     * Atualiza o Número de Conselheiros
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/conselheiros/total/atualizar",
     *     tags={"Atualiza o Número de Conselheiros"},
     *     summary="Atualiza o Número de Conselheiros",
     *     description="Atualiza o Número de Conselheiros",
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
    public function atualizarNumeroConselheiros()
    {
        $data = Input::all();
        $dadosTO = new stdClass();
        $dadosTO->idAtividadeSecundaria = Utils::getValue('idAtividadeSecundaria', $data);
        $dadosTO->justificativa = Utils::getValue('justificativa', $data, '');
        $resp = $this->getParametroConselheiroBO()->atualizarNumeroConselheiros($dadosTO);
        return $this->toJson($resp);
    }

    /**
     * Retorna os parametros conselheiros conforme o filtro informado
     *
     * @return string
     * @throws NegocioException
     * @throws NonUniqueResultException
     *
     * @OA\Post(
     *     path="/profissionais/total/filtro",
     *     tags={"Busca profissionais Conselheiros Quantidade Filtro"},
     *     summary="Retorna os parametros conselheiros conforme o filtro informado",
     *     description="Retorna os parametros conselheiros conforme o filtro informado",
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
    public function getParametroConselheiroPorFiltro()
    {
        $data = Input::all();
        $filtroTO = $this->getFiltroParametroConselheiro($data);
        $resp = $this->getParametroConselheiroBO()->getParametroConselheiroPorFiltro($filtroTO);
        return $this->toJson($resp);
    }

    /**
     * Salva os dados de Parametro Conselheiro
     *
     * @param Request $request
     *
     * @OA\Post(
     *     path="/conselheiros/salvar",
     *     tags={"salva Conselheiros Quantidade"},
     *     summary="Salva os dados de Parametro Conselheiro",
     *     description="Salva os dados de Parametro Conselheiro",
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
    public function salvar(Request $request)
    {
        $data = Input::all();
        $dadosTO = $this->getDadosSalvar($data);
        $this->getParametroConselheiroBO()->salvar($dadosTO, Utils::getValue('justificativa', $data));
    }

    /**
     * Rotina automática que busca por todas as Atividades Secundarias 1.6 e atualiza os dados de conselheiros no dia vigente
     *
     * @throws Exception
     */
    public function atualizarConselheiroAutomatico()
    {
        $this->getParametroConselheiroBO()->atualizarConselheiroAutomatico();
    }

    /**
     * Retorna o histórico por Tipo e por Filtro
     *
     * @param $id
     * @return string
     *
     * @OA\Get(
     *     path="/conselheiros/atividadeSecundaria/{id}/historico",
     *     tags={"Busca Historico Conselheiro por Tipo"},
     *     summary="Retorna o histórico por Tipo e por Filtro",
     *     description="Retorna o histórico por Tipo e por Filtro",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da Atividade Secundária",
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
     * @throws DBALException
     * @throws NegocioException
     */
    public function getHistorico($id)
    {
        $resp = $this->getParametroConselheiroBO()->getHistorico($id);
        return $this->toJson($resp);
    }


    public function gerarDocumentoXSLListaConselheiros($id)
    {
        $filtroTO = new stdClass();
        $filtroTO->idAtividadeSecundaria = $id;
        $documento = $this->getParametroConselheiroBO()->gerarDocumentoXSLListaTotalConselheiros($filtroTO);
        return $this->toFile($documento);
    }

    public function gerarDocumentoPDFListaConselheiros($id)
    {
        $filtroTO = new stdClass();
        $filtroTO->idAtividadeSecundaria = $id;
        $documento = $this->getParametroConselheiroBO()->gerarDocumentoPDFListaTotalConselheiros($filtroTO);
        return $this->toFile($documento);
    }

    /**
     * Retorna a instancia de ParametroConselheiroBO
     *
     * @return ParametroConselheiroBO|mixed
     */
    private function getParametroConselheiroBO()
    {
        if(empty($this->parametroConselheiroBO)){
            $this->parametroConselheiroBO = app()->make(ParametroConselheiroBO::class);
        }
        return $this->parametroConselheiroBO;
    }

    /**
     * Retorna o filtro de pesquisa conforme os parâmetros informados na requisição.
     *
     * @param array $data
     * @return stdClass
     */
    private function getFiltroParametroConselheiro($data)
    {
        $filtroTO = new stdClass();
        $filtroTO->idAtividadeSecundaria = Utils::getValue('idAtividadeSecundaria', $data);
        $filtroTO->idsCauUf = Utils::getValue('idsCauUf', $data);
        return $filtroTO;
    }

    /**
     * Método Auxiliar do Salvar para organizar os dados para o método
     *
     * @param $data
     * @return ParametroConselheiro
     * @throws Exception
     */
    private function getDadosSalvar($data)
    {
        return ParametroConselheiro::newInstance($data);
    }

}
