<?php

/*
 * RecursoSegundoJulgamentoSubstituicaoController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Input;
use App\To\RecursoSegundoJulgamentoSubstituicaoTO;
use App\Business\RecursoSegundoJulgamentoSubstituicaoBO;

/**
 * Classe de controle referente a entidade 'RecursoSegundoJulgamentoSubstituicao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class RecursoSegundoJulgamentoSubstituicaoController extends Controller
{

    /**
     * @var RecursoSegundoJulgamentoSubstituicaoBO
     */
    private $recursoSegundoJulgamentoSubstituicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salvar dados do julgamento final da Chapa Eleição
     *
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/recursoSegundoJulgamentoSubstituicao/salvar",
     *     tags={"Recurso Julgamento Final"},
     *     summary="Salvar dados do recurso julgamento final",
     *     description="Salvar dados do recurso julgamento final",
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
        $julgamento = $this->getRecursoSegundoJulgamentoSubstituicaoBO()->salvar(RecursoSegundoJulgamentoSubstituicaoTO::newInstance(Input::all()));
        return $this->toJson($julgamento);
    }

    /**
     * Retorna uma nova instância de 'RecursoJulgamentoFinalBO'.
     *
     * @return RecursoSegundoJulgamentoSubstituicaoBO
     */
    private function getRecursoSegundoJulgamentoSubstituicaoBO()
    {
        if (empty($this->recursoSegundoJulgamentoSubstituicaoBO)) {
            $this->recursoSegundoJulgamentoSubstituicaoBO = app()->make(RecursoSegundoJulgamentoSubstituicaoBO::class);
        }

        return $this->recursoSegundoJulgamentoSubstituicaoBO;
    }

    /**
     * Retorna arquivo para download buscando pelo ID
     * @param integer $id
     * */
    public function download($id)
    {
        $arquivoTO = $this->getRecursoSegundoJulgamentoSubstituicaoBO()->getArquivo($id);
        return $this->toFile($arquivoTO);
    }
}
