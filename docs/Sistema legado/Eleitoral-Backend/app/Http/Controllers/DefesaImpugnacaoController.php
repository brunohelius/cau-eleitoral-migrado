<?php


namespace App\Http\Controllers;


use App\Business\ArquivoDefesaImpugnacaoBO;
use App\Business\DefesaImpugnacaoBO;
use App\Entities\DefesaImpugnacao;
use App\Exceptions\NegocioException;
use App\To\DefesaImpugnacaoTO;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

class DefesaImpugnacaoController extends Controller
{

    /**
     * @var DefesaImpugnacaoBO
     */
    private $defesaImpugnacaoBO;

    /**
     * @var ArquivoDefesaImpugnacaoBO
     */
    private $arquivoDefesaImpugnacaoBO;

    /**
     * Construtor da Classe.
     */
    public function __construct()
    {
        $this->defesaImpugnacaoBO = app()->make(DefesaImpugnacaoBO::class);

    }

    /**
     * Salvar pedido de impugnação.
     *
     * @return string
     * @throws Exception
     */
    public function salvar()
    {
        $data = Input::all();
        $defesaImpugnacao = DefesaImpugnacaoTO::newInstance($data);
        return $this->toJson($this->defesaImpugnacaoBO->salvar($defesaImpugnacao));
    }

    /**
     * Buscar Defesa de pedido de impugnação por 'id'.
     *
     * @param $id
     * @return DefesaImpugnacao
     * @throws NonUniqueResultException
     */
    public function getPorId($id)
    {
        return $this->defesaImpugnacaoBO->getPorId($id);
    }

    /**
     * Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' do documento informado.
     *
     * @param $idArquivoDefesaImpugnacao
     *
     * @return Response
     * @throws NegocioException
     * @OA\Get(
     *     path="/defesaImpugnacao/documento/{idArquivoDefesaImpugnacao}/download",
     *     tags={"Arquivo Defesa Impugnação"},
     *     summary="Download de Documento da Defesa do Pedido de Impugnação Chapa",
     *     description="Disponibiliza o arquivo 'Documento' para 'download' conforme o 'id' do documento informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idArquivoDefesaImpugnacao",
     *         in="path",
     *         description="Id do Arquivo",
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
    public function downloadDocumento($idArquivoDefesaImpugnacao)
    {
        $arquivoTO = $this->getArquivoDefesaImpugnacaoBO()->getArquivoDefesaImpugnacao($idArquivoDefesaImpugnacao);
        return $this->toFile($arquivoTO);
    }

    /**
     * Buscar Defesa de pedido de impugnação por 'id' do pedido de impugnação.
     *
     * @param $idPedidoImpugnacao
     * @return string
     * @throws NonUniqueResultException
     */
    public function getPorPedidoImpugnacao($idPedidoImpugnacao)
    {
        $defesaImpugnacao = $this->defesaImpugnacaoBO->getPorPedidoImpugnacao($idPedidoImpugnacao);
        if($defesaImpugnacao) {
            return $this->toJson($defesaImpugnacao);
        }
    }

    public function getDefesaImpugnacaoValidacaoAcessoProfissional($idPedidoImpugnacao)
    {
        return $this->toJson($this->defesaImpugnacaoBO->getDefesaImpugnacaoValidacaoAcessoProfissionalTO($idPedidoImpugnacao, null));
    }

    /**
     * Retorna uma nova instância de 'ArquivoDefesaImpugnacaoBO'.
     *
     * @return ArquivoDefesaImpugnacaoBO
     */
    private function getArquivoDefesaImpugnacaoBO()
    {
        if (empty($this->arquivoDefesaImpugnacaoBO)) {
            $this->arquivoDefesaImpugnacaoBO = app()->make(ArquivoDefesaImpugnacaoBO::class);
        }

        return $this->arquivoDefesaImpugnacaoBO;
    }
}