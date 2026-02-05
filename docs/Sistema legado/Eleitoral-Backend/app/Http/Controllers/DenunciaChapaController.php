<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 24/02/2019
 * Time: 11:25
 */

namespace App\Http\Controllers;

use App\Business\DenunciaBO;
use App\Business\DenunciaChapaBO;
use App\Business\DenunciaMembroChapaBO;
use App\Entities\Denuncia;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

/**
 * Classe de controle referente a entidade 'DenunciaChapaController'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class DenunciaChapaController extends Controller
{
    /**
     * @var \App\Business\DenunciaChapaBO
     */
    private $denunciaChapaBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }


    /**
     * Retorna o total de denúncias das chapa agrupadas em UF pela 'id' da pessoa.
     *
     * @param $idPessoa
     * @return string
     *
     * @OA\Get(
     *     path="/denunciaChapa/totalDenunciaAgrupadoUf/{idPessoa}",
     *     tags={"Total de Denúncias por Chapa Agrupadas em UF por Pessoa"},
     *     summary="Recupera as denúncias da chapa agrupadas em UF de acordo com o usuário logado.",
     *     description="Recupera as denúncias da chapa agrupadas em UF de acordo com o usuário logado.",
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
    public function getTotalDenunciaChapaPorUF($idPessoa)
    {
        $resp = $this->getDenunciaChapaBO()->getTotalChapaPorUf($idPessoa);
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
     *     path="/denunciaChapa/listaDenunciaAgrupadoUf/{idPessoa}/cauUf/{idCauUf}",
     *     tags={"Lista as Denúncias por Chapa por UF e Pessoa"},
     *     summary="Recupera as denúncias da chapa de acordo com a UF e o usuário logado.",
     *     description="Recupera as denúncias da chapa de acordo com a UF e o usuário logado.",
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
    public function getListaDenunciaChapaPorUF($idPessoa,$idUF)
    {
        $resp = $this->getDenunciaChapaBO()->getListaDenunciaChapaPorUF($idPessoa,$idUF);
        return $this->toJson($resp);
    }

    /**
     * Retorna a instancia de DenunciaChapaBO
     *
     * @return DenunciaChapaBO|mixed
     */
    private function getDenunciaChapaBO()
    {
        if (empty($this->denunciaMembroChapaBO)) {
            $this->denunciaChapaBO = app()->make(DenunciaChapaBO::class);
        }
        return $this->denunciaChapaBO;
    }

}
?>
