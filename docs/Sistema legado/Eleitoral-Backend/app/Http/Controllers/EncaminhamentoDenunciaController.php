<?php
/*
 * EncaminhamentoDenunciaController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Business\EncaminhamentoDenunciaBO;
use App\Business\DenunciaAudienciaInstrucaoBO;
use App\Business\ImpedimentoSuspeicaoBO;
use App\Config\Constants;
use App\Entities\EncaminhamentoDenuncia;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * Classe de controle referente a entidade 'EncaminhamentoDenuncia'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 */
class EncaminhamentoDenunciaController extends Controller
{
    /**
     * @var \App\Business\EncaminhamentoDenunciaBO
     */
    private $encaminhamentoDenunciaBO;

    /**
     * @var \App\Business\ImpedimentoSuspeicaoBO
     */
    private $impedimentoSuspeicaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    { }

    /**
     * Retorna um array com todos os tipos de encaminhamento ordenados por Id..
     *
     * @return array|null
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="encaminhamentoDenuncia/tiposEncaminhamento",
     *     tags={"tipo", "encaminhamento", "denuncia"},
     *     summary="Retorna um array com todos os tipos de encaminhamento ordenados por Id.",
     *     description="Retorna um array com todos os tipos de encaminhamento ordenados por Id.",
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
    public function getTiposEncaminhamento()
    {
        $resp = $this->getEncaminhamentoDenunciaBO()->getTiposEncaminhamento();
        return $this->toJson($resp);
    }

    /**
     * Retorna um array com todos os tipos de encaminhamento ordenados por Id de acordo com a denuncia.
     *
     * @param $idDenuncia
     * @return array|null
     *
     * @OA\Get(
     *     path="encaminhamentoDenuncia/tiposEncaminhamento/denuncia/{idDenuncia}",
     *     tags={"tipo", "encaminhamento", "denuncia"},
     *     summary="Retorna um array com todos os tipos de encaminhamento ordenados por Id de acordo com a denuncia.",
     *     description="Retorna um array com todos os tipos de encaminhamento ordenados por Id de acordo com a denuncia.",
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
    public function getTiposEncaminhamentoPorDenuncia($idDenuncia)
    {
        $resp = $this->getEncaminhamentoDenunciaBO()->getTiposEncaminhamentoPorDenuncia($idDenuncia);
        return $this->toJson($resp);
    }

    /**
     * Valida se encaminhamento do tipo "produção de provas" e “audiência de instrução” com status “Pendente” para a denúncia
     *
     * @param $idDenuncia
     * @return string
     * @throws \App\Exceptions\NegocioException
     *
     * @OA\Get(
     *     path="denuncia/{idDenuncia}/validarProducaoProvasAudienciaInstrucaoPendente",
     *     tags={"validar", "encaminhamento", "denuncia", "producao", "provas", "audiencia", "instrucao", "pendente"},
     *     summary="Valida se encaminhamento do tipo produção de provas e audiência de instrução com status Pendente para a denúncia.",
     *     description="Valida se encaminhamento do tipo produção de provas e audiência de instrução com status Pendente para a denúncia.",
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
    public function getEncaminhamentosProducaoProvasAudienciaInstrucaoPendente($idDenuncia)
    {
        $resp = $this->getEncaminhamentoDenunciaBO()->getEncaminhamentosProducaoProvasAudienciaInstrucaoPendente($idDenuncia);
        return $this->toJson($resp);
    }

    /**
     * Retorna 'ImpedimentoSuspeicao' pelo id de Encaminhamhametno de Denuncia.
     *
     * @param $idEmcaminhamento
     * @return string
     * @throws \App\Exceptions\NegocioException
     *
     * @OA\Get(
     *     path="impedimentoSuspeicao/encaminhamentosDenuncia/{idEmcaminhamento}",
     *     tags={ "encaminhamento", "denuncia", "impedimentoSuspeicao"},
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
    public function getImpedimentoSuspeicaoPorEncaminhamentoDenuncia($idEmcaminhamento)
    {
        $impedimentoSuspeicao = $this->getImpedimentoSuspeicaoBO()->getImpedimentoSuspeicaoPorEmcaminhamento($idEmcaminhamento);
        return $this->toJson($impedimentoSuspeicao);
    }

    /**
     * Retorna 'ImpedimentoSuspeicao' pelo id de Encaminhamhamento de Denuncia e Denuncia admitida.
     *
     * @param $idEmcaminhamento
     * @return string
     * @throws \App\Exceptions\NegocioException
     *
     * @OA\POST(
     *     path="impedimentoSuspeicao/buscarPorEncaminhamentoEDenunciaAdmitida",
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
    public function getImpedimentoSuspeicao()
    {
        $data = (object) Input::all();
        $impedimentoSuspeicao = $this->getImpedimentoSuspeicaoBO()->getPorEmcaminhamentoAndDenuncia($data->idEmcaminhamento, $data->idDenuncia);
        return $this->toJson($impedimentoSuspeicao);
    }

    /**
     * Valida se encaminhamento do tipo “Impedimento ou Suspeição” com status “Pendente” para a denúncia
     *
     * @return array|null
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="denuncia/{idDenuncia}/validarImpedimento",
     *     tags={"validar", "encaminhamento", "denuncia", "impedimento", "pendente"},
     *     summary="Valida se encaminhamento do tipo Impedimento ou Suspeição com status Pendente para a denúncia.",
     *     description="Valida se encaminhamento do tipo Impedimento ou Suspeição com status Pendente para a denúncia.",
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
    public function validarImpedimentoPendente($idDenuncia)
    {
        $resp = $this->getEncaminhamentoDenunciaBO()->validarImpedimentoPendente($idDenuncia);
        return $this->toJson($resp);
    }

    /**
     * Valida se encaminhamento do tipo “audiência de instrução” com status “Pendente” para a denúncia
     *
     * @param $idDenuncia
     * @return string
     * @throws \App\Exceptions\NegocioException
     *
     * @OA\Get(
     *     path="denuncia/{idDenuncia}/validarAudienciaInstrucaoPendente",
     *     tags={"validar", "encaminhamento", "denuncia", "audiencia", "instrucao", "pendente"},
     *     summary="Valida se encaminhamento do tipo audiência de instrução com status Pendente para a denúncia.",
     *     description="Valida se encaminhamento do tipo audiência de instrução com status Pendente para a denúncia.",
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
    public function validarAudienciaInstrucaoPendente($idDenuncia)
    {
        $resp = $this->getEncaminhamentoDenunciaBO()->validarAudienciaInstrucaoPendente($idDenuncia);
        return $this->toJson($resp);
    }

    /**
     * Salva o encaminhamento da denuncia.
     *
     * @return string
     * @throws \Exception
     * @throws \App\Exceptions\NegocioException
     * @OA\Post(
     *     path="encaminhamentoDenuncia/salvar",
     *     tags={"encaminhamento", "Denuncia", "salvar"},
     *     summary="Salva o encaminhamento da denuncia",
     *     description="Salva o encaminhamento da denuncia",
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
        $encaminhamento = EncaminhamentoDenuncia::newInstance($data);
        $resp = $this->getEncaminhamentoDenunciaBO()->salvar($encaminhamento);
        return $this->toJson($resp);
    }

    /**
     * Salva o encaminhamento da denuncia.
     *
     * @param $idEncaminhamento
     * @return string
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @OA\Post(
     *     path="encaminhamento/testeEmailEncaminar",
     *     tags={"encaminhamento", "Denuncia", "testeEmailEncaminar"},
     *     summary="Salva o encaminhamento da denuncia",
     *     description="Salva o encaminhamento da denuncia",
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
    public function testeEmailEncaminar($idEncaminhamento)
    {
        $resp = $this->getEncaminhamentoDenunciaBO()->enviarEmailsPorIdEncaminhamento($idEncaminhamento);
        return $this->toJson($resp);
    }

    /**
     * Retorna os encaminhamentos da denuncia.
     *
     * @param $idDenuncia
     * @return string
     * @throws \Exception
     * @OA\Post(
     *     path="encaminhamentosDenuncia/denuncia/{idDenuncia}",
     *     tags={"encaminhamento", "Denuncia", "Parecer"},
     *     summary="Retorna os encaminhamentos da denuncia",
     *     description="Retorna os encaminhamentos da denuncia",
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
    public function listarEncaminhamentos($idDenuncia)
    {
        $encaminhamentos = $this->getEncaminhamentoDenunciaBO()->getParecer($idDenuncia);
        return $this->toJson($encaminhamentos);
    }

    /**
     * Retorna o encaminhamento, pelo id informado, com suas provas relacionadas caso existam
     *
     * @param $idEncaminhamento
     * @return string
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="encaminhamentoDenuncia/visualizarProvas/{idEncaminhamento}",
     *     tags={"visualizar", "encaminhamento", "denuncia", "provas"},
     *     summary="Busca o encaminhamento pelo seu Id, trazendo todas as provas e arquivos inseridos caso existam.",
     *     description="Busca o encaminhamento, pelo seu Id, trazendo todas as provas e arquivos inseridos caso existam.",
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
    public function getProvasEncaminhamento($idEncaminhamento)
    {
        $encaminhamento = $this->getEncaminhamentoDenunciaBO()->getProvasEncaminhamento($idEncaminhamento);
        return $this->toJson($encaminhamento);
    }

    /**
     * Retorna o encaminhamento, pelo id informado, com a audiencia de instrucao relacionadas caso existam
     *
     * @param $idEncaminhamento
     * @return string
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="encaminhamentoDenuncia/audienciaInstrucao/{idEncaminhamento}",
     *     tags={"visualizar", "encaminhamento", "denuncia", "audiencia de instrucao "},
     *     summary="Busca o encaminhamento pelo seu Id, trazendo a audiencias de instrucao e arquivos inseridos caso existam.",
     *     description="Busca o encaminhamento, pelo seu Id, trazendo a audiencia de instrucao e arquivos inseridos caso existam.",
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
    public function getAudienciaInstrucaoEncaminhamento($idEncaminhamento)
    {
        $encaminhamento = $this->getEncaminhamentoDenunciaBO()->getAudienciaInstrucaoEncaminhamento($idEncaminhamento);

        return $this->toJson($encaminhamento);
    }

    /**
     * Retorna o encaminhamento, pelo id informado
     *
     * @param $idEncaminhamento
     * @return string
     *
     * @throws \App\Exceptions\NegocioException
     * @OA\Get(
     *     path="encaminhamentoDenuncia/encaminhamento/{idEncaminhamento}",
     *     tags={"visualizar", "encaminhamento", "denuncia"},
     *     summary="Busca o encaminhamento pelo seu Id.",
     *     description="Busca o encaminhamento, pelo seu Id.",
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
    public function getEncaminhamentoPorId($idEncaminhamento)
    {
        $encaminhamento = $this->getEncaminhamentoDenunciaBO()->getEncaminhamentoPorId($idEncaminhamento);
        return $this->toJson($encaminhamento);
    }
    
    /**
     * Disponibiliza o arquivo 'encaminhamento' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="encaminhamentoDenuncia/arquivo/{idArquivo}/download",
     *     tags={"denuncia", "encaminhamento", "arquivos"},
     *     summary="Download de Arquivo do Encaminhamento",
     *     description="Disponibiliza o arquivo 'encaminhamento' para 'download' conforme o 'id' informado.",
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
        $arquivoTO = $this->getEncaminhamentoDenunciaBO()->getArquivo($idArquivo);        
        return $this->toFile($arquivoTO);
    }

    /**
     * Disponibiliza o arquivo 'denúncia admitida' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @OA\Get(
     *     path="denuncia/admitida/arquivo/{idArquivo}/download",
     *     tags={"denuncia", "admitida", "arquivos"},
     *     summary="Download de Arquivo da Denúncia Admitida",
     *     description="Disponibiliza o arquivo 'denúncia admitida' para 'download' conforme o 'id' informado.",
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
    public function downloadDenunciaAdmitida($idArquivo)
    {
        $arquivoTO = $this->getEncaminhamentoDenunciaBO()->getArquivoDenunciaAdmitida($idArquivo);        
        return $this->toFile($arquivoTO);
    }

    /**
     * Retorna a instancia de EncaminhamentoDenunciaBO
     *
     * @return EncaminhamentoDenunciaBO|mixed
     */
    private function getEncaminhamentoDenunciaBO()
    {
        if (empty($this->encaminhamentoDenunciaBO)) {
            $this->encaminhamentoDenunciaBO = app()->make(EncaminhamentoDenunciaBO::class);
        }
        return $this->encaminhamentoDenunciaBO;
    }

    /**
     * Retorna a instancia de ImpedimentoSuspeicaoBO
     *
     * @return ImpedimentoSuspeicaoBO|mixed
     */
    private function getImpedimentoSuspeicaoBO()
    {
        if(empty($this->impedimentoSuspeicaoBO)) {
            $this->impedimentoSuspeicaoBO = app()->make(ImpedimentoSuspeicaoBO::class);
        }
        return $this->impedimentoSuspeicaoBO;
    }
}
