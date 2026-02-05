<?php
/*
 * MembroChapaController.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\DocumentoComprobatorioSinteseCurriculoBO;
use App\Business\MembroChapaBO;
use App\Exceptions\NegocioException;
use App\To\ConviteStatusFiltroTO;
use App\To\MembroChapaTO;
use App\To\StatusMembroChapaTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use stdClass;

/**
 * Classe de controle referente a entidade 'MembroChapa'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class MembroChapaController extends Controller
{
    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var DocumentoComprobatorioSinteseCurriculoBO
     */
    private $documentoComprobatorioBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->membroChapaBO = app()->make(MembroChapaBO::class);
    }

    /**
     * Retorna os convites para chapas através do id do usuario logado.
     *
     * @return string
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @OA\Get(
     *     path="/membrosChapa/convitesRecebidos",
     *     tags={"Busca Convites Membros Chapa"},
     *     summary="Retorna os convites para chapas através do id do usuario logado.",
     *     description="Retorna os convites para chapas através do id do usuario logado.",
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
    public function getConvitesPorUsuarioLogado()
    {
        $resp = $this->membroChapaBO->getConvitesUsuario();
        return $this->toJson($resp);
    }

    /**
     * Aceita o convite para se tornar membro de uma chapa através do id chapa eleicao e id do usuario logado.
     *
     *
     * @return void
     * @throws Exception
     * @OA\Post(
     *     path="/membrosChapa/aceitarConvite",
     *     tags={"Aceita Convite Membro Chapa"},
     *     summary="Aceita o convite para se tornar membro de uma chapa através do id chapa eleicao e id do usuario
     *     logado.",
     *     description="Aceita o convite para se tornar membro de uma chapa através do id chapa eleicao e id do usuario
     *     logado.",
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

        $conviteStatusFiltroTO = ConviteStatusFiltroTO::newInstance($data);
        $this->membroChapaBO->aceitarConvite($conviteStatusFiltroTO, $data['representatividade'] ?? null);
    }

    /**
     * Altera dados (curriculo e/ou foto) do membro/usuario logado.
     *
     *
     * @return void
     * @throws Exception
     * @OA\Post(
     *     path="/membrosChapa/alterarDadosCurriculo",
     *     tags={"Membros Chapa"},
     *     summary="Altera dados (curriculo e/ou foto) do membro/usuario logado.",
     *     description="Altera dados (curriculo e/ou foto) do membro/usuario logado.",
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
    public function alterarDadosCurriculo()
    {
        $data = Input::all();

        $conviteStatusFiltroTO = ConviteStatusFiltroTO::newInstance($data);
        $this->membroChapaBO->alterarDadosCurriculo($conviteStatusFiltroTO);
    }

    /**
     * Rejeita o convite para se tornar membro de uma chapa através do id chapa eleicao e id do usuario logado.
     *
     * @return void
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @OA\Post(
     *     path="/membrosChapa/rejeitarConvite",
     *     tags={"Rejeita Convite Membro Chapa"},
     *     summary="Rejeita o convite para se tornar membro de uma chapa através do id chapa eleicao e id do usuario
     *     logado.",
     *     description="Rejeita o convite para se tornar membro de uma chapa através do id chapa eleicao e id do usuario
     *     logado.",
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
    public function rejeitarConvite()
    {
        $data = Input::all();

        $conviteStatusFiltroTO = ConviteStatusFiltroTO::newInstance($data);
        $this->membroChapaBO->rejeitarConvite($conviteStatusFiltroTO);
    }

    /**
     * Reenvia e-mail para o membro, informando que foi cadastrado para participar da Chapa Eleitoral.
     *
     * @param $id
     *
     * @return void
     * @throws Exception
     * @OA\Post(
     *     path="/membrosChapa/{id}/reenviarConvite",
     *     tags={"Membro Chapa"},
     *     summary="Reenvia e-mail para o membro, informando que foi cadastrado para participar da Chapa Eleitoral.",
     *     description="Reenvia e-mail para o membro, informando que foi cadastrado para participar da Chapa Eleitoral.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Membro da Chapa",
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
    public function reenviarConvite($id)
    {
        $this->membroChapaBO->reenviarConvite($id);
    }

    /**
     * Altera situação de responsável do membro pelo id informado.
     *
     * @param $id
     * @return string
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @OA\Post(
     *     path="/membrosChapa/{id}/alterarSituacaoResponsavel",
     *     tags={"Membro Chapa"},
     *     summary="Altera situação de responsável do membro pelo id informado.",
     *     description="Altera situação de responsável do membro pelo id informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Membro da Chapa",
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
    public function alterarSituacaoResponsavel($id)
    {
        $data = Input::all();

        $dadosTO = $this->getDadosTOAlterarSituacaoResponsavel($data);

        $membroChapa = $this->membroChapaBO->alterarSituacaoResponsavel($id, $dadosTO);
        return $this->toJson($membroChapa);
    }

    /**
     * Altera o status do Convite dado um id membro chapa.
     *
     * @param int $idMembroChapa
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/membrosChapa/{idMembroChapa}/alterarStatusConvite",
     *     tags={"Altera Status Membro Chapa"},
     *     summary="Altera o status do Convite dado um id membro chapa.",
     *     description="Altera o status do Convite dado um id membro chapa.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idMembroChapa",
     *         in="path",
     *         description="Id do Membro da Chapa",
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
    public function alterarStatusConvite(int $idMembroChapa)
    {
        $data = Input::all();
        $data['idMembroChapa'] = $idMembroChapa;

        $statusMembroChapaTO = StatusMembroChapaTO::newInstance($data);
        $membroChapa = $this->membroChapaBO->alterarStatusParticipacao($statusMembroChapaTO);

        return $this->toJson($membroChapa);
    }

    /**
     * Altera o status de Validação dado um id membro chapa.
     *
     * @param int $idMembroChapa
     *
     * @return string
     * @throws Exception
     * @OA\Post(
     *     path="/membrosChapa/{idMembroChapa}/alterarStatusValidacao",
     *     tags={"Altera Status de Validação Membro Chapa"},
     *     summary="Altera o status de Validação dado um id membro chapa.",
     *     description="Altera o status de Validação dado um id membro chapa.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idMembroChapa",
     *         in="path",
     *         description="Id do Membro da Chapa",
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
    public function alterarStatusValidacao(int $idMembroChapa)
    {
        $data = Input::all();
        $data['idMembroChapa'] = $idMembroChapa;

        $statusMembroChapaTO = StatusMembroChapaTO::newInstance($data);
        $membroChapa = $this->membroChapaBO->alterarStatusValidacao($statusMembroChapaTO);

        return $this->toJson($membroChapa);
    }

    /**
     * Envia um e-mail informativo de pendências para o membro com o id informado
     *
     * @param $id
     *
     * @return void
     * @throws Exception
     * @OA\Post(
     *     path="/membrosChapa/{id}/enviarEmailPendencias",
     *     tags={"Membro Chapa"},
     *     summary="Envia um e-mail informativo de pendências para o membro com o id informado.",
     *     description="Envia um e-mail informativo de pendências para o membro com o id informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Membro da Chapa",
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
    public function enviarEmailPendencias($id)
    {
        $this->membroChapaBO->enviarEmailPendencias($id);
    }

    /**
     * Retorna a síntese do currículo e arquivos comprobatórios do membro chapa
     *
     * @param $idMembroChapa
     *
     * @return string
     * @throws NegocioException
     * @throws NonUniqueResultException
     *
     * @OA\Get(
     *     path="/membrosChapa/{id}/detalhar",
     *     tags={"Membros Chapa"},
     *     summary="Detalhes Membro Chapa",
     *     description="Retorna a síntese do currículo e arquivos comprobatórios do membro chapa",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Membro da Chapa",
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
    public function detalhar($idMembroChapa)
    {
        $detalhesSinteseCurriculo = $this->membroChapaBO->detalhar($idMembroChapa);
        return $this->toJson($detalhesSinteseCurriculo);
    }

    /**
     * Disponibiliza o arquivo 'Documento Comprobatório' para 'download' conforme o 'id' informado.
     *
     * @param $idDocumentoComprobatorio
     *
     * @return Response
     * @throws NegocioException
     *
     * @OA\Get(
     *     path="/membrosChapa/documentoComprobatorio/{idDocumento}/download",
     *     tags={"Membros Chapa", "Documento Comprobatório"},
     *     summary="Download de Documento Comprobatório",
     *     description="Disponibiliza o arquivo 'Documento Comprobatório' para 'download' conforme o 'id' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idDocumento",
     *         in="path",
     *         description="Id do Documento",
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
    public function downloadDocumentoComprobatorio($idDocumentoComprobatorio)
    {
        $arquivoTO = $this->getDocumentoComprobatorioBO()->getArquivoDocumentoComprobatorio($idDocumentoComprobatorio);
        return $this->toFile($arquivoTO);
    }

    /**
     * Disponibiliza o arquivo 'Declaração de Representatividde' para 'download' conforme o 'idMembro' informado.
     *
     * @param $idMembro
     *
     * @return Response
     * @throws NegocioException
     *
     * @OA\Get(
     *     path="/membrosChapa/documentoRepresentatividade/{idMembro}/download",
     *     tags={"Membros Chapa", "Documento Representatividade"},
     *     summary="Download de Documento Representatividade",
     *     description="Disponibiliza o arquivo 'Documento Representatividade' para 'download' conforme o 'idMembro' informado.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idMembro",
     *         in="path",
     *         description="Id do Membro da Chapa",
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
    public function downloadDocumentoRepresentatividade($idMembro)
    {
        $arquivoTO = $this->membroChapaBO->documentoRepresentatividade($idMembro);
        return $this->toFile($arquivoTO);
    }

    /**
     * Exclui um Membro pelo Id enviando Justificativa.
     *
     * @param int $id
     *
     * @throws Exception
     * @OA\Post(
     *     path="/membrosChapa/{id}/excluir",
     *     tags={"Excluir Membro Chapa"},
     *     summary="Exclui um Membro pelo Id enviando Justificativa.",
     *     description="Exclui um Membro pelo Id enviando Justificativa.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Membro da Chapa",
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
    public function excluir(int $id)
    {
        $data = Input::all();
        $this->membroChapaBO->excluirMembroChapa($id, $this->getJustificativa($data));
    }

    /**
     * Exclui um Membro pelo Id enviando Justificativa.
     *
     * @param int $id
     *
     * @throws Exception
     * @OA\Post(
     *     path="/membrosChapa/responsavel/excluirMembro/{id}",
     *     tags={"Excluir Membro Chapa"},
     *     summary="Exclui um Membro pelo Id enviando Justificativa.",
     *     description="Exclui um Membro pelo Id enviando Justificativa.",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do Membro da Chapa",
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
    public function excluirByResponsavelChapa(int $id)
    {
        $this->membroChapaBO->excluirMembroChapaByResponsavelChapa($id);
    }

    /**
     * Retorna os membros de chapas por Filtro
     *
     * @OA\Post(
     *     path="membrosChapa/filtro",
     *     tags={"Membros Chapa", "Filtro"},
     *     summary="Retorna os membros de chapas por Filtro.",
     *     description="Retorna os membros de chapas por Filtro.",
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
    public function getMembrosChapasPorFiltro()
    {
        $data = Input::all();
        $filtroTO = new \stdClass();
        $filtroTO->idCauUf = Utils::getValue('idCauUf', $data);
        $filtroTO->nomeRegistro = Utils::getValue('nomeRegistro', $data);
        $resp = $this->membroChapaBO->getMembrosChapasPorFiltro($filtroTO);
        return $this->toJson($resp);
    }

    /**
     * Realiza consulta de membros Titular/Suplente para ser substituído
     *
     * @param int $idProfissional
     *
     * @return string
     * @throws NegocioException
     * @OA\get(
     *     path="/membrosChapa/busca-substituicao/{idProfissional}",
     *     tags={"Busca Membro Chapa para substituição"},
     *     summary="Realiza consulta de membros Titular/Suplente para ser substituído",
     *     description="Realiza consulta de membros Titular/Suplente para ser substituído",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idProfissional",
     *         in="path",
     *         description="Id do Membro Titular/Suplente",
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
    public function getMembrosParaSubstituicao($idProfissional)
    {
        $membros = $this->membroChapaBO->getMembrosParaSubstituicao($idProfissional);

        return $this->toJson($membros);
    }

    /**
     * Realiza consulta de membro chapa a ser impugnado
     *
     * @param int $idProfissional
     *
     * @return string
     * @throws NegocioException
     * @OA\get(
     *     path="/pedidosImpugnacao/consultarMembroChapa/{idProfissional}",
     *     tags={"Busca Membro Chapa para Impugnação"},
     *     summary="Realiza consulta de membro chapa a ser impugnado",
     *     description="Realiza consulta de membro chapa a ser impugnado",
     *     security={{ "Authorization": {} }},
     *     @OA\Parameter(
     *         name="idProfissional",
     *         in="path",
     *         description="Id do Membro da Chapa",
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
    public function getMembroParaImpugnacao($idProfissional)
    {
        $membro = $this->membroChapaBO->getMembroParaImpugnacao($idProfissional);

        return $this->toJson($membro);
    }

    /**
     * Realiza consulta de responsáveis da chapa pelo id da chapa
     *
     * @param int $idChapa
     *
     * @return array
     * @throws NegocioException
     * @OA\get(
     *     path="/responsaveisChapa/{idChapa}",
     *     tags={"Busca Responsáveis Chapa"},
     *     summary="Realiza consulta de responsáveis da chapa pelo id da chapa",
     *     description="Realiza consulta de responsáveis da chapa pelo id da chapa",
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
    public function getResponsaveisChapaPorIdChapa($idChapa)
    {
        $membros = $this->membroChapaBO->getResponsaveisChapaPorIdChapa($idChapa);

        return $this->toJson($membros);
    }

    /**
     * Método provisório para setar e salvar a foto para membros que alteraram a foto
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function atualizarFoto()
    {
        $this->membroChapaBO->atualizarNomeFotoBancoDados();
    }

    /**
     * Método provisório para remover as tags img da sintese de curriculo
     *
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function atualizarSintese()
    {
        $this->membroChapaBO->atualizarSinteseCurriculo();
    }

    /**
     * Método Auxiliar do alterarSituacaoResponsavel para organizar os dados para o método
     *
     * @param $data
     * @return stdClass
     */
    private function getDadosTOAlterarSituacaoResponsavel($data)
    {
        $dadosTO = new stdClass();
        $dadosTO->situacaoResponsavel = Utils::getValue('situacaoResponsavel', $data);
        $dadosTO->justificativa = Utils::getValue('justificativa', $data);

        return $dadosTO;
    }

    /**
     * Retorna uma instância de 'DocumentoComprobatorioSinteseCurriculoBO'
     *
     * @return DocumentoComprobatorioSinteseCurriculoBO
     */
    private function getDocumentoComprobatorioBO()
    {
        if (empty($this->documentoComprobatorioBO)) {
            $this->documentoComprobatorioBO = app()->make(DocumentoComprobatorioSinteseCurriculoBO::class);
        }
        return $this->documentoComprobatorioBO;
    }

    /**
     * Retorna a justificativa de alteração para quando for o Acessor CEN
     *
     * @param $data
     * @return string|null
     */
    private function getJustificativa($data)
    {
        return Utils::getValue('justificativa', $data);
    }

     /**
     * Atualiza o status eleito dos Membros da chapa
     *
     * @param Request $request
     * @return bool
     * @throws NegocioException
     * @OA\post(
     *     path="/setStatusEleito",
     *     tags={"Atualiza o status eleito dos Membros da chapa"},
     *     summary="Atualiza o status eleito dos Membros da chapa",
     *     description="Atualiza o status eleito dos Membros da chapa",
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
    public function setStatusEleito(Request $request)
    {
        $response = $this->membroChapaBO->setStatusEleito($request);
        return $this->toJson($response);
    }

    /**
     * Retorna os membros de chapas Eleitos
     *
     * @OA\Post(
     *     path="membrosChapa/getEleitoByFilter",
     *     tags={"Membros Chapa Eleitos", "Filtro"},
     *     summary="Retorna os membros de chapas Eleitos.",
     *     description="Retorna os membros de chapas Eleitos.",
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
    public function getEleitoByFilter(Request $request)
    {
        $response = $this->membroChapaBO->getEleitoByFilter($request);
        return $this->toJson($response);
    }

    /**
     * Retorna o Presidente da UF
     *
     * @OA\Post(
     *     path="membrosChapa/getPresidenteUf",
     *     tags={"Presidente UF", "Filtro"},
     *     summary="Retorna o Presidente da UF.",
     *     description="Retorna o Presidente da UF.",
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
    public function getPresidenteUf(Request $request)
    {
        $response = $this->membroChapaBO->getPresidenteUf($request);
        return $this->toJson($response);
    }
}
