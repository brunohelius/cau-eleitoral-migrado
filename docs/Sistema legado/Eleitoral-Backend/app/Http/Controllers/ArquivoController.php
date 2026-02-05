<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 22/08/2019
 * Time: 16:17
 */

namespace App\Http\Controllers;

use App\Business\ChapaEleicaoBO;
use App\Entities\ArquivoDecMembroComissao;
use App\Entities\ArquivoRespostaDeclaracaoChapa;
use App\Exceptions\NegocioException;
use App\Service\ArquivoService;
use App\To\ArquivoCalendarioTO;
use Doctrine\ORM\NonUniqueResultException;
use http\Env\Request;
use Illuminate\Support\Facades\Input;
use App\Util\Utils;
use stdClass;

/**
 * Classe de controle referente a entidade 'Arquivo'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class ArquivoController extends Controller
{
    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->arquivoService = app()->make(ArquivoService::class);
    }

    /**
     * Verifica se o 'Arquivo' está em conformidade com os seguintes critérios:
     * O arquivo deve possuir o formato: pdf.
     * O arquivo não pode ser maior que 10Mb.
     * @throws NegocioException
     */
    public function validarResolucaoPDF()
    {
        $data = Input::all();

        $arquivoTO = ArquivoCalendarioTO::newInstance($data);
        $this->arquivoService->validarResolucaoPDF($arquivoTO);

        $response = response()->make('');
        $response->setStatusCode(200);

        return $response;
    }

    /**
     * Verificar se arquivos de Cabeçalho de E-mail estão em conformidade com os seguintes critérios.
     * O arquivo de possuir o formato: jpg, jpeg ou png
     * com largura 610 px e altura 600 px.
     * @throws NegocioException
     */
    public function validarAriquivosCabecalhoEmail(){
        $data = Input::all();
        $this->arquivoService->validarImagemCabecalhoEmail($data['file']);
    }

    /**
     * Verifica se o 'Arquivo' está em conformidade com os seguintes critérios:
     * O arquivo deve possuir o formato: pdf ou doc ou docx
     * O arquivo não pode ser maior que 40Mb.
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function validarArquivoViaDeclaracao()
    {
        $data = Input::all();
        $arquivoTO = ArquivoDecMembroComissao::newInstance($data);
        $id = Utils::getValue('idDeclaracao', $data);

        $this->arquivoService->validarArquivoViaDeclaracao($arquivoTO, $id);

        $response = response()->make('');
        $response->setStatusCode(200);

        return $response;
    }

    /**
     * Verifica se o 'Arquivo' está em conformidade com os seguintes critérios:
     * O arquivo deve possuir o formato: pdf ou doc ou docx
     * O arquivo não pode ser maior que 10Mb.
     * @throws NegocioException
     */
    public function validarArquivoEleicao()
    {
        $data = Input::all();
        $this->arquivoService->validarArquivoEleicao($data);
        $response = response()->make('');

        return $response->setStatusCode(200);
    }

    /**
     * Verificar se a foto de Sintese do Currículo estão em conformidade com os seguintes critérios.
     * O arquivo de possuir o formato: jpg, jpeg ou png, e se é maior de 2mb.
     *
     * @throws \App\Exceptions\NegocioException
     */
    public function validarFotoSinteseCurriculo()
    {
        $data = Input::all();
        $this->arquivoService->validarFotoSinteseCurriculo($data['file']);
    }

    /**
     * Valida previamente arquivo se possui tamnaho e extensão válida.
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     *
     * @OA\Post(
     *     path="/arquivo/validarArquivoRespostaDeclaracaoChapa",
     *     tags={"Arquivo Resposta Declaração Chapa"},
     *     summary="Valida previamente arquivo se possui tamnaho e extensão válida.",
     *     description="Valida previamente arquivo se possui tamnaho e extensão válida.",
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
    public function validarArquivoDeclaracaoChapa()
    {
        $data = Input::all();
        $arquivoRespostaDeclaracaoChapa = ArquivoRespostaDeclaracaoChapa::newInstance($data);
        $idDeclaracao = Utils::getValue('idDeclaracao', $data);

        $this->getChapaEleicaoBO()->validarPreviamenteArquivoRespostaDeclaracao(
            $arquivoRespostaDeclaracaoChapa,
            $idDeclaracao
        );
        return response()->make('',200);
    }

    /**
     * Validação genérica para arquivo previamente.
     *
     * @throws NegocioException
     *
     * @OA\Post(
     *     path="/arquivo/validarArquivo",
     *     tags={"Validação genérica para arquivo"},
     *     summary="Validação genérica para arquivo previamente",
     *     description="Validação genérica para arquivo previamente",
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
    public function validarArquivo()
    {
        $data = Input::all();

        $this->arquivoService->validarArquivo($this->getDadosTOValidarArquivo($data));

        return response()->make('',200);
    }

    /**
     * Valida previamente arquivo se possui tamnaho e extensão válida.
     *
     * @throws NegocioException
     *
     * @OA\Post(
     *     path="arquivo/validarArquivoDenuncia",
     *     tags={"Arquivo Resposta Denuncia"},
     *     summary="Valida previamente arquivo se possui tamnaho e extensão válida.",
     *     description="Valida previamente arquivo se possui tamnaho e extensão válida.",
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
    public function validarArquivoDenuncia()
    {
        $data = Input::all();
        $arquivoTO = ArquivoCalendarioTO::newInstance($data);
        $this->arquivoService->validarArquivoDenuncia($arquivoTO);

        $response = response()->make('');
        $response->setStatusCode(200);

        return $response;
    }

    /**
     * Retorna uma nova instância de 'ChapaEleicaoBO'.
     *
     * @return ChapaEleicaoBO
     */
    private function getChapaEleicaoBO()
    {
        if (empty($this->chapaEleicaoBO)) {
            $this->chapaEleicaoBO = app()->make(ChapaEleicaoBO::class);
        }

        return $this->chapaEleicaoBO;
    }

    private function getDadosTOValidarArquivo($data)
    {
        $dadosTO = new stdClass();
        $dadosTO->nome = Utils::getValue('nome', $data);
        $dadosTO->tamanho = Utils::getValue('tamanho', $data);
        $dadosTO->tipoValidacao = Utils::getValue('tipoValidacao', $data);

        return $dadosTO;
    }
}
