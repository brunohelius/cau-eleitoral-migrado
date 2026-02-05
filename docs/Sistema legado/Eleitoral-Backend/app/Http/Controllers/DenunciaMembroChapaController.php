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
use App\Entities\Denuncia;
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
class DenunciaMembroChapaController extends Controller
{
    /**
     * @var \App\Business\DenunciaMembroChapaBO
     */
    private $denunciaMembroChapaBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }


    /**
     * Retorna o total de denúncias da chapa agrupadas em UF pela 'id' da pessoa.
     *
     * @param $idPessoa
     * @return string
     *
     * @OA\Get(
     *     path="/denunciaMembroChapa/totalDenunciaAgrupadoUf/{idPessoa}",
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
    public function getTotalDenunciaMembroChapaPorUF($idPessoa)
    {
        $resp = $this->getDenunciaMembroChapaBO()->getTotalDenunciaMembroChapaPorUF($idPessoa);
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
     *     path="/denunciaMembroChapa/listaDenunciaAgrupadoUf/{idPessoa}/cauUf/{idCauUf}",
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
    public function getListaDenunciaMembroChapaPorUF($idPessoa,$idUF)
    {
        $resp = $this->getDenunciaMembroChapaBO()->getListaDenunciaMembroChapaPorUF($idPessoa,$idUF);
        return $this->toJson($resp);
    }

    /**
     * Retorna a lista de denúncias por UF e 'id' da pessoa.
     *
     * @param $idPessoa
     * @param $idTipoDenuncia
     * @return string
     *
     * @OA\Get(
     *     path="denuncia/listaDenunciaPessoaUF/{idPessoa}/cauUF/{idCauUF}",
     *     tags={"Retorna os dados das Denúncias do tipo Membro de Chapa"},
     *     summary="Recupera os dados das Denúncias do tipo Membro de Chapa.",
     *     description="Recupera os dados das Denúncias do tipo Membro de Chapa.",
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
    public function getDadosDenunciaMembroChapa($idDenuncia)
    {
        $resp = $this->getDenunciaMembroChapaBO()->getDadosDenunciaMembroChapa($idDenuncia);
        return $this->toJson($resp);
    }

    /**
     * Retorna a instancia de DenunciaBO
     *
     * @return DenunciaBO|mixed
     */
    private function getDenunciaMembroChapaBO()
    {
        if (empty($this->denunciaMembroChapaBO)) {
            $this->denunciaMembroChapaBO = app()->make(DenunciaMembroChapaBO::class);
        }
        return $this->denunciaMembroChapaBO;
    }


}
?>
