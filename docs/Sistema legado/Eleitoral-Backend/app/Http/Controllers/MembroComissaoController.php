<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 17/10/2019
 * Time: 11:09
 */

namespace App\Http\Controllers;

use App\Business\MembroComissaoBO;
use App\Entities\InformacaoComissaoMembro;
use App\Entities\MembroComissao;
use App\Exceptions\NegocioException;
use App\To\ConviteStatusComissaoFiltroTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Mpdf\MpdfException;
use PhpOffice\PhpWord\Exception\Exception;
use stdClass;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe de controle referente a entidade 'MembroComissao'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class MembroComissaoController extends Controller
{
    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->membroComissaoBO = app()->make(MembroComissaoBO::class);
    }

    /**
     * Retorna todos os tipos de participação
     *
     * @return string
     *
     * @OA\Get(
     *     path="/membroComissao/tipoParticipacao",
     *     tags={"Buscar Tipo Participacao Membro Comissao"},
     *     summary="Retorna todos os tipos de participação",
     *     description="Retorna todos os tipos de participação",
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
    public function getTipoParticipacao()
    {
        $resp = $this->membroComissaoBO->getTipoParticipacao();
        return $this->toJson($resp);
    }

    /**
     * Salva os membros de uma comissão.
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @OA\Post(
     *     path="/membroComissao/salvar",
     *     tags={"Salvar Membro Comissao"},
     *     summary="Salva os membros de uma comissão",
     *     description="Salva os membros de uma comissão",
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
        $data = Input::all();
        $dadosTO = $this->getDadosTOSalvar($data);
        $this->membroComissaoBO->salvar($dadosTO);
    }

    /**
     * Retorna os membros da comissão dado um id de informação de comissão e id cau uf.
     *
     * @param $idInformacaoComissao
     * @param $idCauUf
     * @return string
     *
     * @throws NegocioException
     * @OA\Get(
     *     path="/membroComissao/informacaoComissaoMembro/{idInformacaoComissao}/cauUf/{idCauUf}",
     *     tags={"Busca Membro Comissao Informacao CAU UF"},
     *     summary="Retorna os membros da comissão dado um id de informação de comissão e id cau uf",
     *     description="Retorna os membros da comissão dado um id de informação de comissão e id cau uf",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idInformacaoComissao",
     *         in="path",
     *         description="Id de Informação de Comissão",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idCauUf",
     *         in="path",
     *         description="Id do CauUF",
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
     * @throws NegocioException
     */
    public function getPorInformacaoComissaoCauUf($idInformacaoComissao, $idCauUf)
    {
        $resp = $this->membroComissaoBO->getPorInformacaoComissaoCauUf($idInformacaoComissao, $idCauUf);
        return $this->toJson($resp);
    }

    /**
     * Retorna os membros da comissão dado um id de informação de comissão organizado por id cau uf.
     *
     * @param $idInformacaoComissao
     * @return string
     *
     * @throws NegocioException
     * @OA\Get(
     *     path="/membroComissao/informacaoComissaoMembro/{idInformacaoComissao}",
     *     tags={"Busca Membro Informacao Comissao"},
     *     summary="Retorna os membros da comissão dado um id de informação de comissão organizado por id cau uf",
     *     description="Retorna os membros da comissão dado um id de informação de comissão organizado por id cau uf",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idInformacaoComissao",
     *         in="path",
     *         description="Id de Informação de Comissão",
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
    public function getPorInformacaoComissao($idInformacaoComissao)
    {
        $resp = $this->membroComissaoBO->getPorInformacaoComissao($idInformacaoComissao);
        return $this->toJson($resp);
    }

    /**
     * Retorna os membros da comissão dado um id de informação de comissão organizado por id cau uf.
     *
     * @param $idInformacaoComissao
     * @return string
     *
     * @throws NegocioException
     * @OA\Get(
     *     path="/membroComissao/informacaoComissaoMembro/{idInformacaoComissao}/resumo",
     *     tags={"Busca Membro Informacao Comissao"},
     *     summary="Retorna os membros da comissão dado um id de informação de comissão organizado por id cau uf",
     *     description="Retorna os membros da comissão dado um id de informação de comissão organizado por id cau uf",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idInformacaoComissao",
     *         in="path",
     *         description="Id de Informação de Comissão",
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
    public function getPorInformacaoComissaoResumo($idInformacaoComissao)
    {
        $resp = $this->membroComissaoBO->getPorInformacaoComissao($idInformacaoComissao, false);
        return $this->toJson($resp);
    }

    /**
     * Retorna a quantidade de membros para cada CAU UF.
     *
     * @param $idCauUf
     * @param $idInformacaoComissao
     * @return string
     *
     * @OA\Get(
     *     path="/membroComissao/quantidadePorCauUf/{idCauUf}/informacaoComissao/{idInformacaoComissao}",
     *     tags={"Busca Quantidade Membro Comissao"},
     *     summary="Retorna a quantidade de membros para cada CAU UF",
     *     description="Retorna a quantidade de membros para cada CAU UF",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCauUf",
     *         in="path",
     *         description="Id do CauUf",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idInformacaoComissao",
     *         in="path",
     *         description="Id de Informação de Comissão",
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
    public function getTotalMembrosPorCauUf($idCauUf, $idInformacaoComissao)
    {
        $resp = $this->membroComissaoBO->getTotalMembrosPorCauUf($idCauUf, $idInformacaoComissao);
        return $this->toJson($resp);
    }

    /**
     * Retorna o membro da comissão dado um determinado id.
     *
     * @param $idMembroComissao
     * @return string
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/membroComissao/{idMembroComissao}",
     *     tags={"Busca Membro Comissao por Id"},
     *     summary="Retorna o membro da comissão dado um determinado id",
     *     description="Retorna o membro da comissão dado um determinado id",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idMembroComissao",
     *         in="path",
     *         description="Id do Membro da Comissão",
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
    public function getPorId($idMembroComissao)
    {
        $resp = $this->membroComissaoBO->getPorId($idMembroComissao);
        return $this->toJson($resp);
    }

    /**
     * Retorna o arquivo referente á relação de membros da comissão.
     *
     * @param $idInformacaoComissao
     * @return Response
     * @throws NegocioException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws Exception
     */
    public function gerarDocumentoRelacaoMembros($idInformacaoComissao)
    {
        $documento = $this->membroComissaoBO->gerarDocumentoRelacaoMembros($idInformacaoComissao);
        return $this->toFile($documento);
    }


    /**
     * Aceita ou rejeita o convite de membro de uma comissão.
     *
     * @return string
     * @throws \Exception
     *
     * @OA\Post(
     *     path="/membroComissao/aceitarConvite",
     *     tags={"Aceitar Convite"},
     *     summary="Aceita convite para membro de comissão",
     *     description="Aceita ou rejeita o convite para membro de comissão",
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
    public function aceitarConvite()
    {
        $data = Input::all();

        $conviteStatusComissaoFiltroTO = ConviteStatusComissaoFiltroTO::newInstance($data);
        $convite = $this->membroComissaoBO->aceitarConvite($conviteStatusComissaoFiltroTO);
        return $this->toJson($convite);
    }

    /**
     * Retorna a lista de Membros dado um determinado membro da comissão
     *
     * @param $id
     * @return string
     * @throws NegocioException
     * @OA\Get(
     *     path="/membroComissao/{id}/lista",
     *     tags={"Busca Membro Comissao por Membro"},
     *     summary="Retorna a lista de Membros dado um determinado membro da comissão",
     *     description="Retorna a lista de Membros dado um determinado membro da comissão",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Membro da Comissão",
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
    public function getListaMembrosPorMembro($id)
    {
        $resp = $this->membroComissaoBO->getListaMembrosPorMembro($id);
        return $this->toJson($resp);
    }

    /**
     * Valida se o Usuario logado é membro da comissão referente a eleição vigente..
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="/membroComissao/validaMembroComissaoEleicaoVigente",
     *     tags={"Valida Membro Comissao Eleicao Vigente"},
     *     summary="Valida se o Usuario logado é membro da comissão referente a eleição vigente.",
     *     description="Valida se o Usuario logado é membro da comissão referente a eleição vigente.",
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
    public function validaMembroComissaoEleicaoVigente()
    {
        $resp = $this->membroComissaoBO->validaMembroComissaoEleicaoVigente();
        return $this->toJson($resp);
    }

    public function getListaMembrosPorMembroCauUf($id, $idCauUf)
    {
        $resp = $this->membroComissaoBO->getListaMembrosPorMembro($id, $idCauUf);
        return $this->toJson($resp);
    }

    /**
     * Retorna a lista de Coordenadores dada uma determinada chapa
     *
     * @param $idChapa
     * @return array
     * @throws NegocioException
     * @OA\Get(
     *     path="membroComissao/coordenadores/chapa/{idChapa}",
     *     tags={"Busca Coordenadores por Chapa"},
     *     summary="Retorna a lista de Coordenadores dada uma determinada chapa",
     *     description="Retorna a lista de Coordenadores dada uma determinada chapa",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapa",
     *         in="path",
     *         description="Id da Chapa",
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
    public function getCoordenadoresPorChapaId($idChapa)
    {
        $resp = $this->membroComissaoBO->getCoordenadoresPorChapaId($idChapa);
        return $this->toJson($resp);
    }

    /**
     * Retorna a lista de Assessores CE e CEN, dada uma determinada chapa
     *
     * @param $idChapa
     * @return array
     * @throws NegocioException
     * @OA\Get(
     *     path="membroComissao/assessores/chapa/{idChapa}",
     *     tags={"Busca Assessores CE e CEN por Chapa"},
     *     summary="Retorna a lista de Assessores CE e CEN dada uma determinada chapa",
     *     description="Retorna a lista de Assessores CE e CEN dada uma determinada chapa",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idChapa",
     *         in="path",
     *         description="Id da Chapa",
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
    public function getAssessoresPorChapaId($idChapa)
    {
        $resp = $this->membroComissaoBO->getAssessoresPorChapaId($idChapa);
        return $this->toJson($resp);
    }

    public function getPorFiltro()
    {
        $data = Input::all();
        $filtroTO = new \stdClass();
        $filtroTO->idCauUf = Utils::getValue('cauUfId', $data);
        $filtroTO->idEleicao = Utils::getValue('eleicaoId', $data);
        $filtroTO->ano = Utils::getValue('anoEleicao', $data);
        $filtroTO->idInformacaoComissao = Utils::getValue('idInformacaoEleicao', $data);
        $filtroTO->addDadosComplementaresProfissional = Utils::getBooleanValue('addDadosComplementaresProfissional', $data);

        $resp = $this->membroComissaoBO->getPorFiltro($filtroTO);
        return $this->toJson($resp);
    }

    /**
     * Método Auxiliar do Salvar para organizar os dados para o método
     *
     * @param $data
     * @return stdClass
     * @throws \Exception
     */
    private function getDadosTOSalvar($data)
    {
        $dadosTO = new stdClass();
        $coordenadores = Utils::getValue('coordenadores', $data);
        $membros = Utils::getValue('membros', $data);
        $dadosTO->justificativa = Utils::getValue('justificativa', $data);
        $dadosTO->informacaoComissaoMembro = InformacaoComissaoMembro::newInstance(Utils::getValue('informacaoComissaoMembro', $data));

        $membrosArray = new ArrayCollection();
        foreach ($coordenadores as $coordenador) {
            $membrosArray->add(MembroComissao::newInstance($coordenador));
            if (!empty($coordenador['membroSubstituto']) && !empty($membro['membroSubstituto']['pessoa'])) {
                $membrosArray->add(MembroComissao::newInstance($coordenador['membroSubstituto']));
            }
        }

        foreach ($membros as $membro) {
            if (!empty($membro['pessoa'])) {
                $membrosArray->add(MembroComissao::newInstance($membro));
                if (!empty($membro['membroSubstituto']) && !empty($membro['membroSubstituto']['pessoa'])) {
                    $membrosArray->add(MembroComissao::newInstance($membro['membroSubstituto']));
                }
            }
        }
        $dadosTO->membros = $membrosArray;

        return $dadosTO;
    }

    /**
     * Retorna as UFs que possuem comissão
     *
     * @OA\Get(
     *     path="membroComissao/ufs",
     *     tags={"Busca Ufs de Comissao"},
     *     summary="Retorna as UFs que possuem comissão",
     *     description="Retorna as UFs que possuem comissão",
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
    public function getUfsComissao()
    {
        $resp = $this->membroComissaoBO->getUfsComissao();
        return $this->toJson($resp);
    }


    /**
     * Retorna a declaração para participação da comissão eleitoral.
     *
     * @param $idProfissional
     * @return string
     *
     * @throws NegocioException
     * @OA\Get(
     *     path="/membroComissao/declaracao/{idPessoa}",
     *     tags={"Membro Comissão - API"},
     *     summary="Retorna a declaração para participação da comissão eleitoral.",
     *     description="Retorna a declaração para participação da comissão eleitoral.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idPessoa",
     *         in="path",
     *         description="Id do Membro da Comissão",
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
    public function getDeclaracaoPorIdProfissional($idProfissional)
    {
        $resp = $this->membroComissaoBO->isParticipanteComissao($idProfissional);
        return $this->toJson($resp);
    }

    /**
     * @param $idProfissional
     * @return array
     * @throws \Exception
     */
    public function getStatusMembroComissaoPorIdProfissional($idProfissional)
    {
        return $this->membroComissaoBO->getStatusMembroComissaoPorIdProfissional($idProfissional);
    }

    /**
     * Retorna todos os membros de comissão cujo convites não foram aceitos
     * @throws \Exception
     */
    public function enviarEmailsConvitesPendentes()
    {
        return $this->membroComissaoBO->enviarEmailsConvitesPendentes();
    }

    /**
     * Retorna os dados da informacao da comissao por um membro (idPessoa)
     *
     * @param $idPessoa
     * @return string
     * @throws NegocioException
     */
    public function getInformacaoPorMembro($idPessoa)
    {
        $resp = $this->membroComissaoBO->getInformacaoPorMembro($idPessoa);
        return $this->toJson($resp);
    }

    /**
     * Retorna os membros de comissão dado um id cau uf.
     *
     * @return string
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Post(
     *     path="membroComissao/informacaoComissaoMembro/filtro",
     *     tags={"Busca Membro Comissao CAU UF"},
     *     summary="Retorna os membros de comissão dado um Filtro",
     *     description="Retorna os membros de comissão dado um Filtro",
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
     * @throws NegocioException
     */
    public function getComissaoPorFiltro()
    {
        $data = Input::all();
        $filtroTO = new \stdClass();
        $filtroTO->idCauUf = Utils::getValue('idCauUf', $data);
        $filtroTO->nomeRegistro = Utils::getValue('nomeRegistro', $data);

        $resp = $this->membroComissaoBO->getComissaoPorFiltro($filtroTO);
        return $this->toJson($resp);
    }

    /**
     * Retorna o tipo de conselheiro para membro da comissao do usuario logado
     *
     * @return string
     *
     * @throws NegocioException
     * @OA\Get(
     *     path="/membroComissao/getTipoConselheiroProfissionalLogado",
     *     tags={"Busca Membro Comissao Informacao CAU UF"},
     *     summary="Retorna o tipo de conselheiro para membro da comissao do usuario logado",
     *     description="Retorna o tipo de conselheiro para membro da comissao do usuario logado",
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
    public function getTipoConselheiroProfissionalLogado()
    {
        $resp = $this->membroComissaoBO->getTipoConselheiroProfissionalLogado();
        return $this->toJson($resp);
    }

    /**
     * Verifica se já existe Membros de Comissão cadastrado para o 'Calendário' e UF do usuário logado.
     *
     * @param int $idCalendario
     * @return Response
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/membroComissao/validacao/calendario/{idCalendario}/usuario",
     *     tags={"Verifica se já existe Membros de Comissão cadastrado para o 'Calendário' e UF do usuário logado"},
     *     summary="Verifica se já existe Membros de Comissão cadastrado para o 'Calendário' e UF do usuário logado",
     *     description="Verifica se já existe Membros de Comissão cadastrado para o 'Calendário' e UF do usuário logado",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idCalendario",
     *         in="path",
     *         description="Id do Calendário",
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
    public function validarMembrosComissaoExistentePorCalendarioUsuario($idCalendario)
    {
        $this->membroComissaoBO->validarMembrosComissaoExistentePorCalendarioUsuario($idCalendario);
        return response()->make('',200);
    }

    /**
     * Retorna os membros e os substitutos de uma determinada comissão por uf
     *
     * @param $idCauUf
     * @return string
     *
     * @OA\Get(
     *     path="membroComissao/uf/{idCauUf}/denuncia/{denuncia}",
     *     tags={"Busca", "membro", "comissao", "uf"},
     *     summary="Retorna os membros e os substitutos de uma determinada comissão por uf",
     *     description="Retorna os membros e os substitutos de uma determinada comissão por uf",
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
    public function getMembrosComissaoPorUf($idCauUf, $denuncia)
    {
        $resp = $this->membroComissaoBO->getPossiveisRelatores($idCauUf, $denuncia);
        return $this->toJson($resp);
    }

    /**
     * Valida se o Usuario logado tem acesso para acessar a Respectiva Denuncia
     *
     * @param $idDenuncia
     * @return string
     *
     * @throws NegocioException
     * @OA\Get(
     *     path="membroComissao/denuncia/{idDenuncia}/validarAcessoMembro",
     *     tags={"Busca", "membro", "comissao", "uf"},
     *     summary="Valida se o Usuario logado tem acesso para acessar as Denuncias da Respectiva UF",
     *     description="Valida se o Usuario logado tem acesso para acessar as Denuncias da Respectiva UF",
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
    public function validarMembroComissaoPorDenuncia($idDenuncia)
    {
        $resp = $this->membroComissaoBO->validarMembroComissaoPorDenuncia($idDenuncia);
        return response()->make('',200);
    }

    /**
     * Retorna o coordenador e os substitutos de uma determinada comissão por uf
     *
     * @param $idCauUf
     * @return string
     *
     * @OA\Get(
     *     path="coordenadorComissao/uf/{idCauUf}",
     *     tags={"Busca", "coordenador", "comissao", "uf"},
     *     summary="Retorna o coordenador e os substitutos de uma determinada comissão por uf",
     *     description="Retorna o coordenador e os substitutos de uma determinada comissão por uf",
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
    public function getCoordenadorComissaoPorUf($idCauUf)
    {
        $resp = $this->membroComissaoBO->getCoordenadorComissaoPorUf($idCauUf);
        return $this->toJson($resp);
    }
}
