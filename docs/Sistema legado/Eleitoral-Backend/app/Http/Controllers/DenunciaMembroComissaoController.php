<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 24/02/2019
 * Time: 11:25
 */

namespace App\Http\Controllers;

use App\Business\DenunciaBO;
use App\Business\DenunciaMembroChapaBO;
use App\Business\DenunciaMembroComissaoBO;
use App\Entities\Denuncia;
use App\Entities\DenunciaMembroComissao;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

/**
 * Classe de controle referente a entidade 'DenunciaController'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class DenunciaMembroComissaoController extends Controller
{
    /**
     * @var \App\Business\DenunciaMembroComissaoBO
     */
    private $denunciaMembroComissao;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }

    /**
     * Retorna o total de denúncias da comissão agrupadas em UF pela 'id' da pessoa.
     *
     * @param $idPessoa
     * @return string
     *
     * @OA\Get(
     *     path="/totalDenunciaMembroComissaoPorUf/{idPessoa}",
     *     tags={"Total de Denúncias da comissão Agrupadas em UF por Pessoa"},
     *     summary="Recupera as denúncias da comissão agrupadas em UF de acordo com o usuário logado.",
     *     description="Recupera as denúncias da comissão agrupadas em UF de acordo com o usuário logado.",
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
    public function getTotalDenunciaComissaoPorUF($idPessoa)
    {
        $resp = $this->getDenunciaMembroComissaoBO()->getTotalMembroComissaoPorUF($idPessoa);
        return $this->toJson($resp);
    }

    /**
     * Retorna a lista de denúncias por UF e 'id' da pessoa.
     *
     * @param $idPessoa
     * @param $idUF
     * @return string
     *
     * @OA\Get(
     *     path="/denunciaMembroComissao/listaDenunciaAgrupadoUf/{idPessoa}/cauUf/{idCauUf}",
     *     tags={"Lista as Denúncias por membro da comissão por UF e Pessoa"},
     *     summary="Recupera as denúncias por membro da comissão de acordo com a UF e o usuário logado.",
     *     description="Recupera as denúncias por membro da comissão de acordo com a UF e o usuário logado.",
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
    public function getListaDenunciaMembroComissaoPorUF($idPessoa,$idUF)
    {
        $resp = $this->getDenunciaMembroComissaoBO()->getListaDenunciaMembroComissaoPorUF($idPessoa,$idUF);
        return $this->toJson($resp);
    }


    /**
     * Retorna a instancia de DenunciaMembroComissaoBO
     *
     * @return denunciaMembroComissaoBO|mixed
     */
    private function getDenunciaMembroComissaoBO()
    {
        if (empty($this->denunciaMembroComissaoBO)) {
            $this->denunciaMembroComissaoBO = app()->make(DenunciaMembroComissaoBO::class);
        }
        return $this->denunciaMembroComissaoBO;
    }
}
?>
