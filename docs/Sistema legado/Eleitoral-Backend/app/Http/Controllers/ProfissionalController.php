<?php
/*
 * ProfissionalController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\ParametroConselheiroBO;
use App\Business\PedidoImpugnacaoBO;
use App\Business\ProfissionalBO;
use App\Exceptions\NegocioException;
use App\Util\Utils;
use Exception;
use Illuminate\Support\Facades\Input;
use stdClass;

/**
 * Classe de controle referente a entidade 'Profissional'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class ProfissionalController extends Controller
{
    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var ParametroConselheiroBO
     */
    private $parametroConselheiroBO;

    /**
     * Retorna Profissionais conforme o filtro informado.
     *
     *
     * @return string
     *
     * @OA\Post(
     *     path="/profissionais/filtro",
     *     tags={"Profissionais"},
     *     summary="Lista de Profissionais",
     *     description="Retorna Profissionais conforme o filtro informado.",
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
    public function getProfissionaisPorFiltro()
    {
        $data = Input::all();
        $profissionalTO = $this->getFiltroProfissional($data);
        $profissionais = $this->getProfissionalBO()->getProfissionaisPorFiltro($profissionalTO, 50);

        return $this->toJson($profissionais);
    }

    /**
     * Retorna o filtro de pesquisa conforme os parâmetros informados na requisição.
     *
     * @param array $data
     * @return stdClass
     */
    private function getFiltroProfissional($data)
    {
        $profissionalTO = new stdClass();
        $profissionalTO->cpfNome = Utils::getValue('cpfNome', $data);
        $profissionalTO->registroNome = Utils::getValue('registroNome', $data);

        return $profissionalTO;
    }

    /**
     * Retorna a intancia de 'ParametroConselheiroBO'.
     *
     * @return ParametroConselheiroBO
     */
    private function getParametroConselheiroBO()
    {
        if ($this->parametroConselheiroBO == null) {
            $this->parametroConselheiroBO = app()->make(ParametroConselheiroBO::class);
        }
        return $this->parametroConselheiroBO;
    }

    /**
     * Retorna a intancia de 'ProfissionalBO'.
     *
     * @return ProfissionalBO
     */
    private function getProfissionalBO()
    {
        if ($this->profissionalBO == null) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }
        return $this->profissionalBO;
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function getProfissionais()
    {
        /** @var PedidoImpugnacaoBO $pedidoImpugnacaoBO */
        $pedidoImpugnacaoBO = app()->make(PedidoImpugnacaoBO::class);
    //    $pedidos = $pedidoImpugnacaoBO->getQuantidadePedidosParaCadaUf();
        $pedidos = $pedidoImpugnacaoBO->getPedidosPorUf(0);
        return $this->toJson($pedidos);
    }

}
