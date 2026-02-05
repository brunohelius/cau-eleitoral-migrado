<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 24/02/2019
 * Time: 11:25
 */

namespace App\Http\Controllers;

use App\Business\DenunciaAdmitidaBO;
use App\Business\DenunciaBO;
use App\Business\RecursoContrarrazaoBO;
use App\Business\RecursoImpugnacaoBO;
use App\Config\Constants;
use App\Business\JulgamentoAdmissibilidadeBO;
use App\Business\RecursoJulgamentoAdmissibilidadeBO;
use App\Entities\Denuncia;
use App\Entities\DenunciaAdmitida;
use App\Entities\DenunciaDefesa;
use App\Entities\DenunciaInadmitida;
use App\Entities\EncaminhamentoDenuncia;
use App\Entities\RecursoDenuncia;
use App\To\AbaDenunciaTO;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'DenunciaController'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class DenunciaController extends Controller
{
    /**
     * @var \App\Business\DenunciaBO
     */
    private $denunciaBO;

    /**
     * @var RecursoContrarrazaoBO
     */
    private $recursoContrarrazaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        ini_set('max_execution_time', 180);
        set_time_limit(0);
    }

    /**
     * Retorna todos os Tipos de Denuncia
     * @return string
     *
     * @OA\Get(
     *     path="/tiposDenuncia",
     *     tags={"anos"},
     *     summary="Tipos de Denuncia",
     *     description="Retorna todos os Tipos de Denuncia",
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
    public function getTiposDenuncia()
    {
        $resp = $this->getDenunciaBO()->getTiposDenuncia();
        return $this->toJson($resp);
    }

    /**
     * Salva os dados de uma denuncia.
     *
     * @return string
     * @throws \Exception
     * @throws \App\Exceptions\NegocioException
     * @OA\Post(
     *     path="denuncia/salvar",
     *     tags={"Salvar Denuncia"},
     *     summary="Salva os dados de uma denuncia",
     *     description="Salva os dados de uma denuncia",
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
    public function salvar()
    {
        $data = Input::all();
        $denuncia = Denuncia::newInstance($data);
        $resp = $this->getDenunciaBO()->salvar($denuncia);
        return $this->toJson($resp);
    }

    /**
     * Retorna o total de denúncias da chapa agrupadas em UF pela 'id' da pessoa.
     *
     * @param $idPessoa
     * @return string
     *
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="/getTotalDenunciaChapaPorUF/{idPessoa}",
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
    public function getAgrupadaDenunciaPorPessoaUF($idPessoa)
    {
        $resp = $this->getDenunciaBO()->getDenunciaAgrupada($idPessoa);
        return $this->toJson($resp);
    }

    /**
     * Recupera as Denuncias UF por Calendario.
     *
     * @param $idCalendario
     * @return string
     *
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="/denuncias/calendario/{idCalendario}/agrupamentoUf",
     *     tags={"Recupera as Denuncias de UF por Calendario"},
     *     summary="Recupera as Denuncias de UF por Calendario.",
     *     description="Recupera as Denuncias de UF por Calendario.",
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
    public function getAgrupamentoDenunciaUfPorCalendario($idCalendario)
    {
        $denunciasUf = $this->getDenunciaBO()->getAgrupamentoDenunciaUfPorCalendario($idCalendario);
        return $this->toJson($denunciasUf);
    }

    /**
     * Lista as Denuncias a UF informada.
     *
     * @param $idCauUf
     * @return string
     *
     * @throws \Doctrine\DBAL\DBALException
     * @OA\Get(
     *     path="/denuncias/cauUf/{idCauUf}/detalhamentoDenunciaUF",
     *     tags={"Lista as Denuncias a UF informada."},
     *     summary="Lista as Denuncias a UF informada.",
     *     description="Lista as Denuncias a UF informada.",
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
    public function getDenunciasPorCauUf($idCauUf)
    {
        $denunciasUf = $this->getDenunciaBO()->getDenunciasPorCauUf($idCauUf);
        return $this->toJson($denunciasUf);
    }

    /**
     * Lista as Denúncias por Calendário e UF informados.
     *
     * @param $idCalendario
     * @param $idCauUf
     * @return string
     *
     * @throws \Doctrine\DBAL\DBALException
     * @OA\Get(
     *     path="/denuncias/calendario/{idCalendario}/cauUf/{idCauUf}/detalhamentoDenunciaUF",
     *     tags={"Lista as Denúncias por Calendário e UF informados."},
     *     summary="Lista as Denúncias por Calendário e UF informados.",
     *     description="Lista as Denuncias a UF informada.",
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
    public function getDenunciasPorCalendarioCauUf($idCalendario, $idCauUf)
    {
        $denunciasUf = $this->getDenunciaBO()->getDenunciasPorCauUf($idCauUf, $idCalendario);
        return $this->toJson($denunciasUf);
    }

    /**
     * Lista as Denuncias a UF informada.
     *
     * @param $idCauUf
     *
     * @return string
     *
     * @throws \Exception
     * @OA\Get(
     *     path="/denuncias/cauUf/{idCauUf}/detalhamentoDenunciaUfPessoa",
     *     tags={"Lista as Denuncias a UF informada."},
     *     summary="Lista as Denuncias a UF informada.",
     *     description="Lista as Denuncias a UF informada.",
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
    public function getDenunciasPorCauUfPessoa($idCauUf)
    {
        $denunciasUf = $this->getDenunciaBO()->getDenunciasPorCauUfPessoa($idCauUf);
        return $this->toJson($denunciasUf);
    }

    /**
     * Lista as Denuncias em relatoria de acordo com o profissional.
     *
     * @return string
     * @OA\Get(
     *     path="/denuncias/detalhamentoDenunciaRelatoriaProfissional",
     *     tags={"Lista as Denuncias em relatoria de acordo com o profissional."},
     *     summary="Lista as Denuncias em relatoria de acordo com o profissional.",
     *     description="Lista as Denuncias em relatoria de acordo com o profissional.",
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
    public function getDenunciasRelatoriaPorProfissional()
    {
        $denunciasRelatoria = $this->getDenunciaBO()->getDenunciasRelatoriaPorProfissional();
        return $this->toJson($denunciasRelatoria);
    }

    /**
     * Retorna o total de denúncias pelo id' da pessoa agrupadas por UF.
     *
     * @param $idPessoa
     * @return integer
     *
     * @OA\Get(
     *     path="denuncia/getTotalDenunciaPorPessoaUF/{idPessoa}",
     *     tags={"Retorna o total de denúncias agrupadas por UF pelo id da pessoa."},
     *     summary="Recupera o total de denúncias agrupadas por UF do usuário logado.",
     *     description="Recupera o total de denúncias agrupadas por UF  do usuário logado.",
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
    public function getTotalDenunciaPorPessoaUF($idPessoa)
    {
        $resp = $this->getDenunciaBO()->getTotalDenunciaPorPessoaUF($idPessoa);
        return $this->toJson($resp);
    }
    /**
     * Retorna o total de denúncias pelo id' da pessoa.
     *
     * @param $idPessoa
     * @return integer
     *
     * @OA\Get(
     *     path="denuncia/getTotalDenunciaPorPessoa/{idPessoa}",
     *     tags={"Retorna o total de denúncias pelo id da pessoa."},
     *     summary="Recupera o total de denúncias do usuário logado.",
     *     description="Recupera o total de denúncias do usuário logado.",
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
    public function getTotalDenunciaPorPessoa($idPessoa)
    {
        $resp = $this->getDenunciaBO()->getTotalDenunciaPorPessoa($idPessoa);
        return $this->toJson($resp);
    }

    /**
     * Retorna a lista de denúncias pelo id' da pessoa e a UF.
     *
     * @param $idPessoa
     * @return string
     *
     * @OA\Get(
     *     path="denuncia/getListaDenunciaPessoaUF/{idPessoa}",
     *     tags={"Retorna a lista de denúncias pelo id da pessoa e a UF."},
     *     summary="Retorna a lista de denúncias pelo id da pessoa e a UF.",
     *     description="Retorna a lista de denúncias pelo id da pessoa e a UF.",
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
     * @throws \App\Exceptions\NegocioException
     */
    public function getDenunciaLista($idPessoa,$idUF)
    {
        $resp = $this->getDenunciaBO()->getListaDenunciaPessoaUF($idPessoa,$idUF);
        return $this->toJson($resp);
    }

    /**
     * Retorna a visualização detalhada da Denúncia
     *
     * @param $idDenuncia
     *
     * @return string
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncia/{idDenuncia}",
     *     tags={"Retorna a lista de denúncias pelo id da pessoa e a UF."},
     *     summary="Retorna a lista de denúncias pelo id da pessoa e a UF.",
     *     description="Retorna a lista de denúncias pelo id da pessoa e a UF.",
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
    public function getVisualizarDenuncia($idDenuncia)
    {
        $denuncia = $this->getDenunciaBO()->getAcompanhamentoDenunciaPorIdDenuncia($idDenuncia);
        return $this->toJson($denuncia);
    }

    /**
     * Retorna as abas disponíveis para a denuncia de acordo com o 'id' informado.
     *
     * @param $idDenuncia
     * @return string
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncia/{idDenuncia}/abasDisponiveis",
     *     tags={"Retorna as abas disponíveis para a denuncia de acordo com o 'id' informado."},
     *     summary="Retorna as abas disponíveis para a denuncia de acordo com o 'id' informado.",
     *     description="Retorna as abas disponíveis para a denuncia de acordo com o 'id' informado.",
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
    public function getAbasDisponiveisByIdDenuncia($idDenuncia)
    {
        $abasDisponiveis = $this->getDenunciaBO()->getAbasDisponiveisPorIdDenuncia($idDenuncia);
        return $this->toJson($abasDisponiveis);
    }

    /**
     * @return JsonResponse
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncia/{idDenuncia}/condicao",
     *     tags={"denuncia", "profissional"},
     *     summary="Verificar Julgamento",
     *     description="Retorna informações de julgamento",
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
    public function getCondicaoDenuncia(
        $idDenuncia,
        JulgamentoAdmissibilidadeBO $julgamentoDenunciaBO,
        DenunciaAdmitidaBO $denunciaAdmitidaBO,
        RecursoJulgamentoAdmissibilidadeBO $recursoJulgamentoAdmissibilidadeBO
    ) {
        return response()->json([
            'posso_julgar' => $julgamentoDenunciaBO->possoJulgar($idDenuncia),
            'existe_julgamento' => $julgamentoDenunciaBO->existeJulgamento($idDenuncia),
            'posso_inserir_relator' => $denunciaAdmitidaBO->possoInserir($idDenuncia),
            'existe_relator' => $denunciaAdmitidaBO->existeAdmissao($idDenuncia),
            'recurso_admissibilidade' => $recursoJulgamentoAdmissibilidadeBO->verificaRecursoAdmissibilidade($idDenuncia)
        ]);
    }

    /**
     * @param $idArquivo
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @OA\Get(
     *     path="denuncia/arquivo/{idArquivo}/download",
     *     tags={"denuncia", "arquivo", "download"},
     *     summary="Disponibiliza o arquivo Denúncia para download conforme o id informado.",
     *     description="Disponibiliza o arquivo Denúncia para download conforme o id informado.",
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
    public function download($idArquivo)
    {
        $arquivoTO = $this->getDenunciaBO()->getArquivo($idArquivo);
        return $this->toFile($arquivoTO);
    }

    /**
     * Disponibiliza o arquivo 'Denúncia' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="denuncia/inadmitida/arquivo/{idArquivo}/download",
     *     tags={"denuncia", "arquivos"},
     *     summary="Download de Arquivo da Denúncia",
     *     description="Disponibiliza o arquivo 'Denúncia' para 'download' conforme o 'id' informado.",
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
    public function downloadInadmitida($idArquivo)
    {
        $arquivoTO = $this->getDenunciaBO()->getArquivoInadmitida($idArquivo);
        return $this->toFile($arquivoTO);
    }

    /**
     * Recupera as Denuncias de acordo com o Profissional Informado.
     *
     * @return array|null
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncia/profissional",
     *     tags={"denuncia", "profissional"},
     *     summary="Recupera as Denuncias de acordo com o Profissional Informado",
     *     description="Recupera as Denuncias de acordo com o Profissional Informado.",
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
    public function getPorProfissional()
    {
        $resp = $this->getDenunciaBO()->getPorProfissional(false, false);
        return $this->toJson($resp);
    }

    /**
     * Recupera as Denuncias de acordo com o Profissional Informado.
     *
     * @param $idDenuncia
     * @return array|null
     *
     * @throws \Exception
     * @OA\Get(
     *     path="denuncia/{idDenuncia}/validaProfissionalLogado",
     *     tags={"denuncia", "profissional"},
     *     summary="Recupera as Denuncias de acordo com o Profissional Informado",
     *     description="Recupera as Denuncias de acordo com o Profissional Informado.",
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
    public function validaProfissionalLogadoPorIdDenuncia($idDenuncia)
    {
        $resp = $this->getDenunciaBO()->validaProfissionalLogadoPorIdDenuncia($idDenuncia);
        return $this->toJson($resp);
    }

    public function enviarEmailResponsaveis($idDenuncia)
    {
        $resp = $this->getDenunciaBO()->enviarEmailResponsaveis($idDenuncia);
        return $this->toJson($resp);
    }

    /**
     * Salva a admissão da denúncia
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @OA\Post(
     *     path="denuncia/admitir",
     *     tags={"Admitir Denuncia"},
     *     summary="Admite a denuncia",
     *     description="Salva a operação de admitir a denuncia",
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
    public function admitir()
    {
        $data = Input::all();
        $denunciaAdmitida = DenunciaAdmitida::newInstance($data);
        $resp = $this->getDenunciaBO()->admitir($denunciaAdmitida);
        return $this->toJson($resp);
    }
    /**
     * Salva o relator da denúncia
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @OA\Post(
     *     path="denuncia/relator",
     *     tags={"Relator Denuncia"},
     *     summary="Relator da denuncia",
     *     description="Salva a operação relator da denuncia",
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
    public function relator()
    {
        $data = Input::all();
        $denunciaAdmRelator = DenunciaAdmitida::newInstance($data);
        $resp = $this->getDenunciaBO()->relator($denunciaAdmRelator, $data['idEncaminhamento']);
        return $this->toJson($resp);
    }

    /**
     * Salva a recurso da denúncia
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @OA\Post(
     *     path="denuncia/recurso",
     *     tags={"Recurso Denuncia"},
     *     summary="Recurso da denuncia",
     *     description="Salva a operação recurso da denuncia",
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
    public function recurso()
    {
        $data = Input::all();
        $recurso = RecursoDenuncia::newInstance($data);
        $resp = $this->getRecursoContrarrazaoBO()->recurso($recurso);
        return $this->toJson($resp);
    }

    public function emailSend($idDenuncia, $idTipoDenuncia)
    {
        $this->getDenunciaBO()->enviarEmailAdmitirEInadmitir($idDenuncia, $idTipoDenuncia);
    }

    /**
     * Salva a Inadmissão da Denuncia
     *
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @OA\Post(
     *     path="denuncia/inadmitir",
     *     tags={"Inadmitir Denuncia"},
     *     summary="Salva a Inadmissão da Denuncia",
     *     description="Salva a Inadmissão da Denuncia",
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
    public function inadmitir()
    {
        $data = Input::all();
        $denunciaInadmitida = DenunciaInadmitida::newInstance($data);
        $resp = $this->getDenunciaBO()->inadmitir($denunciaInadmitida);
        return $this->toJson($resp);
    }

    /**
     * Salva a Defesa da Denuncia
     *
     * @return string
     * @throws \Exception
     * @OA\Post(
     *     path="denuncia/defender",
     *     tags={"Defender Denuncia"},
     *     summary="Salva a Defesa da Denuncia",
     *     description="Salva a Defesa da Denuncia",
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
    public function defesaDenuncia()
    {
        $data = Input::all();
        $denunciaDefesa = DenunciaDefesa::newInstance($data);
        $resp = $this->getDenunciaBO()->defenderDenuncia($denunciaDefesa);
        return $this->toJson($resp);
    }

    /**
     * Retorna o total de denúncias da chapa agrupadas em UF pela 'id' da pessoa.
     *
     * @return string
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncias/comissao/agrupadoUf",
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
    public function getAgrupadaDenunciaComissaoUF()
    {
        $resp = $this->getDenunciaBO()->getDenunciaComissaoAgrupada();
        return $this->toJson($resp);
    }

    /**
     * Lista as Denuncias a UF informada.
     *
     * @param $idCauUf
     *
     * @return string
     *
     * @throws \Exception
     * @OA\Get(
     *     path="denuncias/comissao/cauUf/{idCauUf}/detalhamentoDenunciaUfPessoa",
     *     tags={"Lista as Denuncias a UF informada."},
     *     summary="Lista as Denuncias a UF informada.",
     *     description="Lista as Denuncias a UF informada.",
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
    public function getDenunciasComissaoPorCauUfPessoa($idCauUf)
    {
        $denunciasUf = $this->getDenunciaBO()->getDenunciasComissaoPorCauUfPessoa($idCauUf);
        return $this->toJson($denunciasUf);
    }

    /**
     * Recupera as Denuncias de acordo com o Profissional Informado.
     *
     * @return array|null
     *
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\DBAL\DBALException
     * @OA\Get(
     *     path="denuncias/comissao/admissibilidade",
     *     tags={"denuncia", "profissional"},
     *     summary="Recupera as Denuncias de acordo com o Profissional Informado",
     *     description="Recupera as Denuncias de acordo com o Profissional Informado.",
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
    public function getDenunciaComissaoAdmissibilidade()
    {
        $resp = $this->getDenunciaBO()->getDenunciaComissaoAdmissibilidade();
        return $this->toJson($resp);
    }

    /**
     * Recupera o Membro comissão de Denuncia de acordo com o Usuario Logado.
     *
     * @return array|null
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncia/comissao/membroComissao",
     *     tags={"denuncia", "profissional"},
     *     summary="Recupera as Denuncias de acordo com o Profissional Informado",
     *     description="Recupera as Denuncias de acordo com o Profissional Informado.",
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
    public function getMembroComissaoDenunciaPorUsuario()
    {
        $resp = $this->getDenunciaBO()->getMembroComissaoDenunciaPorUsuario();
        return $this->toJson($resp);
    }

    /**
     * Valida se o Usuario Logado tem Acesso a Acompanhamento da Denuncia do respectivo UF.
     *
     * @return array|null
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncias/denuncia/{idDenuncia}/validarAcessoAcompanhar",
     *     tags={"denuncia", "profissional"},
     *     summary="Valida se o Usuario Logado tem Acesso a Acompanhamento da Denuncia do respectivo UF.",
     *     description="Valida se o Usuario Logado tem Acesso a Acompanhamento da Denuncia do respectivo UF.",
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
    public function validarAcessoDenunciaPorDenuncia($idDenuncia)
    {
        $this->getDenunciaBO()->validarAcessoDenunciaPorDenuncia($idDenuncia);
        return response()->make('',200);
    }

    /**
     * Gerar documento das denúncia exportando as informações em formato PDF.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     * @OA\Post(
     *     path="denuncia/gerarDocumento",
     *     tags={"denuncia", "gerar Documento Denúncia"},
     *     summary="Gerar documento das denúncia exportando as informações em formato PDF.",
     *     description="Gerar documento das denúncia exportando as informações em formato PDF.",
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
    public function gerarDocumento()
    {
        $data = Input::all();
        return $this->getDenunciaBO()->gerarDocumentoExtratoDenuncia(AbaDenunciaTO::newInstance($data));
    }

    /**
     * TODO
     *
     * @param $idDenuncia
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\NegocioException
     */
    public function validarAcessoDenunciaCorporativoPorDenuncia($idDenuncia){
        $this->getDenunciaBO()->validarAcessoDenunciaCorporativoPorDenuncia($idDenuncia);
        return response()->make('',200);
    }

    /**
     * Retorna a instancia de DenunciaBO
     *
     * @return DenunciaBO|mixed
     */
    private function getDenunciaBO()
    {
        if (empty($this->denunciaBO)) {
            $this->denunciaBO = app()->make(DenunciaBO::class);
        }
        return $this->denunciaBO;
    }

    /**
     * Retorna a instancia de RecursoContrarazao
     *
     * @return RecursoContrarrazaoBO|mixed
     */
    private function getRecursoContrarrazaoBO()
    {
        if (empty($this->recursoContrarrazaoBO)) {
            $this->recursoContrarrazaoBO = app()->make(RecursoContrarrazaoBO::class);
        }
        return $this->recursoContrarrazaoBO;
    }
}
