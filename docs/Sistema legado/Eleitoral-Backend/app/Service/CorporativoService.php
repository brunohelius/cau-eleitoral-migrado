<?php
/*
 * CorporativoService.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Service;

use App\Config\AppConfig;
use App\Config\Constants;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\UsuarioFactory;
use App\To\DeclaracaoTO;
use App\To\ItemDeclaracaoTO;
use App\To\UsuarioTO;
use App\Util\RestClient;
use App\Util\Utils;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use stdClass;

/**
 * Classe de serviço de integração com o WS do SICCAU - Corporativo.
 *
 * @package App\Service'
 * @author Squadra Tecnologia S/A.
 */
class CorporativoService
{

    private $usuarioFactory;

    /**
     * Recupera uma lista com as filiais.
     *
     * @return mixed
     * @throws NegocioException
     */
    public function getFiliais()
    {
        $filiais = $this->getRestClient(true)->sendGet(AppConfig::getUrlPlataforma('filiais'));
        $filiais = json_decode($filiais);

        if (!empty($filiais->error)) {
            throw new NegocioException($filiais->error);
        }

        return $filiais;
    }

    /**
     * Recupera uma lista com as filiais.
     *
     * @return mixed
     * @throws NegocioException
     */
    public function getFiliaisComBandeiras()
    {
        $filiais = $this->getRestClient(true)->sendGet(AppConfig::getUrlPlataforma('filiais/bandeira'));
        $filiais = json_decode($filiais);

        if (!empty($filiais->error)) {
            throw new NegocioException($filiais->error);
        }

        return $filiais;
    }

    /**
     * Recupera uma lista com as filiais.
     *
     * @return mixed
     * @throws NegocioException
     */
    public function getListaFiliaisFormata()
    {
        $filiais = $this->getFiliais();

        $filiaisFormatadas = [];
        if(!empty($filiais) && is_array($filiais)) {
            foreach ($filiais as $filial) {
                $filiaisFormatadas[$filial->id] = $filial;
            }
        }
        return Arr::sort($filiaisFormatadas);
    }

    /**
     * Recupera uma filial por id
     *
     * @return mixed
     * @throws NegocioException
     */
    public function getFilialPorId($id)
    {
        $response = $this->getRestClient(true)->sendGet(AppConfig::getUrlPlataforma('filiais/' . $id));
        $response = json_decode($response);

        if (!empty($response->error)) {
            throw new NegocioException($response->error);
        } elseif (!empty($response->code)) {
            throw new NegocioException($response->description);
        }

        return $this->getFilialTO($response);
    }

    /**
     * Recupera os dados do usuário, conforme o 'id' informado.
     *
     * @param integer $id
     * @return UsuarioTO
     * @throws NegocioException
     */
    public function getUsuarioPorId($id)
    {
        if (empty($id)) {
            throw new NegocioException(Message::MSG_FAVOR_INFORMAR_CAMPOS_OBRIGATORIOS);
        }

        $usuario = $this->getRestClient(true)->sendGet(
            AppConfig::getUrlPlataforma('usuarios/' . $id)
        );

        $usuario = json_decode($usuario);

        if (!empty($usuario->error)) {
            throw new NegocioException($usuario->error);
        }

        return $this->getUsuarioTO($usuario);
    }

    /**
     * Recupera lista usuários conforme os 'ids' informado.
     *
     * @param array $ids
     * @return array|null
     * @throws NegocioException
     */
    public function getUsuariosPorIds($ids)
    {
        $filtro = ['ids' => $ids];

        return $this->getUsuariosFiltro($filtro);
    }

    /**
     * Recupera o Email do profissional/pessoa, conforme o 'id' informado.
     *
     * @return array|null
     * @throws NegocioException
     */
    public function getUsuariosAssessoresCEN()
    {
        $filtro = ['numeroPermissao' => Constants::PERMISSAO_ACESSOR_CEN];

        return $this->getUsuariosFiltro($filtro);
    }

    /**
     * Recupera o Email do profissional/pessoa, conforme o 'id' informado.
     *
     * @param array $idsCauUf
     * @return array|null
     * @throws NegocioException
     */
    public function getUsuariosAssessoresCE($idsCauUf)
    {
        if (empty($idsCauUf)) {
            throw new NegocioException(Message::MSG_FAVOR_INFORMAR_CAMPOS_OBRIGATORIOS);
        }

        $filtro = [
            'numeroPermissao' => Constants::PERMISSAO_ACESSOR_CE_UF,
            'idsFilial' => $idsCauUf
        ];

        return $this->getUsuariosFiltro($filtro);
    }

    /**
     * Recupera o Email do profissional/pessoa, conforme o 'id' informado.
     *
     * @param integer $id
     * @param bool $incluirAvatarRetorno
     * @return stdClass
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getProfissionalPorId($id, $incluirAvatarRetorno = false)
    {
        if (empty($id)) {
            throw new NegocioException(Message::MSG_FAVOR_INFORMAR_CAMPOS_OBRIGATORIOS);
        }

        $profissional = $this->getRestClient(true)->sendGet(
            AppConfig::getUrlPlataforma('profissionais/' . $id)
        );

        return $this->processaRetornoGetProfissional($profissional, $incluirAvatarRetorno);
    }

    /**
     * Recupera o Email do profissional/pessoa, conforme o 'id' informado.
     *
     * @param $cpf
     * @param bool $incluirAvatarRetorno
     *
     * @return stdClass
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getProfissionalPorCPF($cpf, $incluirAvatarRetorno = false)
    {
        if (empty($cpf)) {
            throw new NegocioException(Message::MSG_FAVOR_INFORMAR_CAMPOS_OBRIGATORIOS);
        }

        $profissional = $this->getRestClient(true)->sendGet(
            AppConfig::getUrlPlataforma('profissionais/cpf/' . $cpf)
        );

        return $this->processaRetornoGetProfissional($profissional, $incluirAvatarRetorno);
    }

    /**
     * Processa e formata o retorno da requisição api de retornar um profissional
     *
     * @param string $profissional
     * @param bool $incluirAvatarRetorno
     *
     * @return stdClass
     * @throws NegocioException
     */
    private function processaRetornoGetProfissional($profissional, $incluirAvatarRetorno = false)
    {
        $profissional = json_decode($profissional);

        if (!empty($profissional->error)) {
            throw new NegocioException($profissional->error);
        } elseif (!empty($profissional->code)) {
            throw new NegocioException($profissional->description);
        }

        if (!is_null($profissional)) {
            $profissional = $this->getProfissionalTO($profissional, $incluirAvatarRetorno);
        }

        return $profissional;
    }

    /**
     * Recupera o Email do profissional/pessoa, conforme o 'id' informado.
     *
     * @param $cpfNome
     *
     * @return array|null
     * @throws NegocioException
     */
    public function getProfissionaisPorCpfNome($cpfNome)
    {
        if (empty($cpfNome)) {
            throw new NegocioException(Message::MSG_FAVOR_INFORMAR_CAMPOS_OBRIGATORIOS);
        }

        return $this->getProfissionaisPorFiltro(compact('cpfNome'));
    }

    /**
     * Recupera o Email do profissional/pessoa, conforme o 'id' informado.
     *
     * @param array $ids
     *
     * @param bool $addDadosComplementaresRetorno
     * @return array
     * @throws NegocioException
     */
    public function getProfissionaisPorIds($ids, $addDadosComplementaresRetorno = false)
    {
        if (empty($ids)) {
            throw new NegocioException(Message::MSG_FAVOR_INFORMAR_CAMPOS_OBRIGATORIOS);
        }

        return $this->getProfissionaisPorFiltro(compact('ids'), $addDadosComplementaresRetorno);
    }

    /**
     * Retorna uma lista de profissionais formatada como indice o id do profissional
     *
     * @param $idsProfissionais
     * @return array
     * @throws NegocioException
     */
    public function getListaProfissionaisFormatadaPorIds($idsProfissionais, $addDadosComplementaresRetorno = false)
    {
        $profissionaisRetorno = $this->getProfissionaisPorIds(
            $idsProfissionais,
            $addDadosComplementaresRetorno
        );

        $profissionais = [];

        if (!empty($profissionaisRetorno)) {
            foreach ($profissionaisRetorno as $index => $profissional) {
                $profissionais[$profissional->id] = $profissional;
            }
        }

        return $profissionais;
    }

    /**
     * Recupera a quantidade de profissionais.
     *
     * @return integer
     * @throws NegocioException
     */
    public function getQtdProfissionais()
    {
        $qtdProfissionais = $this->getRestClient(true)->sendGet(
            AppConfig::getUrlPlataforma('profissionais/quantidade')
        );

        $qtdProfissionais = json_decode($qtdProfissionais);

        if (!empty($qtdProfissionais->error)) {
            throw new NegocioException($qtdProfissionais->error);
        } elseif (!empty($qtdProfissionais->code) && !empty($qtdProfissionais->description)) {
            throw new NegocioException($qtdProfissionais->description);
        }

        return $qtdProfissionais;
    }

    /**
     * Retorna todos os conselheiros em ordem de CAU/UF
     *
     * @return array
     * @throws NegocioException
     */
    public function getConselheiros()
    {
        $conselheiros = $this->getRestClient(true)->sendGet(
            AppConfig::getUrlPlataforma('profissionais/conselheiros/uf')
        );

        $conselheiros = json_decode($conselheiros);

        if (!empty($conselheiros->error)) {
            throw new NegocioException($conselheiros->error);
        }

        return array_map(function ($dados) {
            return $this->getConselheiroTO($dados);
        }, $conselheiros);
    }

    /**
     * Recupera as declarações definidas para o módulo eleitoral.
     *
     * @return array
     * @throws NegocioException
     */
    public function getDeclaracoes()
    {
        $declaracoes = $this->getRestClient(true)->sendPost(
            AppConfig::getUrlPlataforma('declaracoes/filtro?idModulo=' . Constants::MODULO_ELEITORAL)
        );

        $declaracoes = json_decode($declaracoes);

        if (!empty($declaracoes->error)) {
            throw new NegocioException($declaracoes->error);
        }

        return array_map(function ($dados) {
            return $this->getDeclaracaoTO($dados);
        }, $declaracoes);
    }

    /**
     * Recupera uma declaracão por ID
     *
     * @param $id
     *
     * @return mixed
     * @throws NegocioException
     */
    public function getDeclaracaoPorId($id)
    {
        $declaracao = $this->getRestClient(true)->sendGet(
            AppConfig::getUrlPlataforma('declaracoes/' . $id)
        );

        $declaracao = json_decode($declaracao);

        if (!empty($declaracao->error)) {
            throw new NegocioException($declaracao->error);
        } elseif (!empty($declaracao->code) && !empty($declaracao->description)) {
            throw new NegocioException($declaracao->description);
        }
        return $this->getDeclaracaoTO($declaracao);
    }

    /**
     * Recupera os profissionais de acordo com os filtros informados.
     *
     * @param array $filtro
     *
     * @param bool $addDadosComplementaresRetorno
     * @return array
     * @throws NegocioException
     */
    public function getProfissionaisPorFiltro($filtro, $addDadosComplementaresRetorno = false)
    {
        if($addDadosComplementaresRetorno){
            $filtro['addDadosComplementaresRetorno'] = $addDadosComplementaresRetorno;
        }

        $profissionais = $this->getRestClient(true)->sendPost(
            AppConfig::getUrlPlataforma('profissionais/filtro'),
            json_encode($filtro)
        );

        $profissionais = json_decode($profissionais);

        if (!empty($profissionais->error)) {
            throw new NegocioException($profissionais->error);
        } elseif (!empty($profissionais->code) && !empty($profissionais->description)) {
            throw new NegocioException($profissionais->description);
        }

        if(is_array($profissionais)) {
            $profissionais = array_map(function ($profissional) use($addDadosComplementaresRetorno) {
                $profissionalTO = $this->getProfissionalTO(
                    $profissional,
                    false,
                    false,
                    true,
                    $addDadosComplementaresRetorno);

                return $profissionalTO;
            }, $profissionais);
        }
        return $profissionais;
    }

    /**
     * Método para buscar lista de e-mails dos assessores CEN e CE/UF
     *
     * @param array|null $idsCauUf
     * @return array
     * @throws NegocioException
     */
    public function getListaEmailsAssessoresCenAndAssessoresCE($idsCauUf = null)
    {
        $acessoresCEN = $this->getUsuariosAssessoresCEN();

        if(!empty($idsCauUf)){
            $acessoresCEUF = $this->getUsuariosAssessoresCE($idsCauUf);
        }

        $destinatarios = array_merge($acessoresCEN ?? [], $acessoresCEUF ?? []);

        return array_map(static function ($usuarioTO) {
            return $usuarioTO->getEmail();
        }, $destinatarios);
    }

    /**
     * Retorna a lista de usuários de acordo com o filtro informado
     *
     * @param array $filtro
     * @return array|null
     * @throws NegocioException
     */
    private function getUsuariosFiltro($filtro)
    {
        $usuarios = $this->getRestClient(true)->sendPost(
            AppConfig::getUrlPlataforma("usuarios/filtro"),
            http_build_query($filtro)
        );

        $usuarios = json_decode($usuarios);

        if (!empty($usuarios->error)) {
            throw new NegocioException($usuarios->error);
        } elseif (!empty($usuarios->code) && !empty($usuarios->description)) {
            throw new NegocioException($usuarios->description);
        }

        if (is_array($usuarios)) {
            $usuarios = array_map(function ($usuario) {
                return $this->getUsuarioTO($usuario);
            }, $usuarios);
        }

        return $usuarios;
    }

    /**
     * Recupera as declarações definidas para o módulo eleitoral.
     *
     * @param string $uf
     *
     * @return array
     * @throws NegocioException
     */
    private function getIdCauUfPorUf(?string $uf)
    {
        $filiais = $this->getFiliais();

        $ids = array_filter($filiais, function ($dados) use ($uf) {
            return $uf === $dados->prefixo;
        });

        return array_first(array_column($ids, 'id'));
    }

    /**
     * Retorna a instância de 'UsuarioTO' conforme os parâmetros informados.
     *
     * @param stdClass $usuario
     * @return UsuarioTO
     */
    private function getUsuarioTO($usuario)
    {
        $usuarioTO = new UsuarioTO();

        $usuarioTO->setId(Utils::getValorAtributo($usuario, 'id'));
        $usuarioTO->setNome(Utils::getValorAtributo($usuario, 'nome'));
        $usuarioTO->setEmail(Utils::getValorAtributo($usuario, 'email'));

        return $usuarioTO;
    }

    /**
     * Retorna a instancia stdClass para ConselheiroTO conforme os parâmetros informados.
     *
     * @param $conselheiro
     * @return stdClass
     * @throws Exception
     */
    private function getConselheiroTO($conselheiro)
    {
        $conselheiroTO = new stdClass();
        $conselheiroTO->nome = strtoupper(Utils::getValorAtributo($conselheiro, 'nome'));
        $conselheiroTO->cpf = Utils::getValorAtributo($conselheiro, 'cpf');
        $conselheiroTO->registroRegional = Utils::getValorAtributo($conselheiro, 'registroRegional', '');
        $conselheiroTO->descricao = Utils::getValorAtributo($conselheiro, 'descricao');
        $conselheiroTO->cauUf = Utils::getValorAtributo($conselheiro, 'cauUf');

        $valorDataFim = 'Sem Limite';
        if (!empty($conselheiro->dataFim)) {
            $dataFim = new DateTime($conselheiro->dataFim);
            $valorDataFim = Utils::getStringFromDate($dataFim, "d/m/Y");
        }
        $conselheiroTO->dataFim = $valorDataFim;

        return $conselheiroTO;
    }

    /**
     * Retorna a instancia stdClass para ProfissionalTO conforme os parâmetros informados.
     *
     * @param $profissional
     * @param bool $incluirAvatarRetorno
     * @param bool $incluirIdCauUf
     * @param bool $incluirDadosPessoa
     * @param bool $incluirDadosComplementares
     *
     * @return stdClass
     * @throws NegocioException
     */
    private function getProfissionalTO($profissional,
        bool $incluirAvatarRetorno,
        bool $incluirIdCauUf = true,
        bool $incluirDadosPessoa = true,
        bool $incluirDadosComplementares = true
    ) {
        $profissionalTO = new stdClass();

        $profissionalTO->id = Utils::getValorAtributo($profissional, 'id');
        $profissionalTO->cpf = Utils::getValorAtributo($profissional, 'cpf');
        $profissionalTO->nome = Utils::getValorAtributo($profissional, 'nome');

        $profissionalTO->registroNacional = ltrim(
            Utils::getValorAtributo($profissional, 'registroNacional',''),'0'
        );

        if($incluirDadosPessoa){
            $pessoa = Utils::getValorAtributo($profissional, 'pessoa');
            $profissionalTO->email = Utils::getValorAtributo($pessoa, 'email');

            if ($incluirIdCauUf) {
                $endereco = Utils::getValorAtributo($pessoa, 'endereco');
                $profissionalTO->uf = Utils::getValorAtributo($endereco, 'uf');

                if(!empty($profissionalTO->uf)){
                    $profissionalTO->idCauUf = $this->getIdCauUfPorUf($profissionalTO->uf);
                }
            }
        }

        if ($incluirAvatarRetorno) {
            $profissionalTO->avatar = Utils::getValorAtributo($profissional, 'avatar');
            $profissionalTO->possuiFoto = Utils::getValorAtributo($profissional, 'possuiFoto', false);
        }

        if($incluirDadosComplementares){
            $this->setDadosComplementaresProfissionalTO($profissionalTO, $profissional);
        }

        return $profissionalTO;
    }

    /**
     * Seta os dados complementares do profissional no TO
     *
     * @param $profissional
     * @param $profissionalTO
     */
    private function setDadosComplementaresProfissionalTO($profissionalTO, $profissional): void
    {
        $profissionalTO->adimplente = Utils::getValorAtributo($profissional, 'adimplente');
        $profissionalTO->conselheiroSubsequente = $this->getConselheiroSubsequenteTO($profissional);
        $profissionalTO->conselheiro = Utils::getValorAtributo($profissional, 'conselheiro');
        $profissionalTO->perdaMandatoConselheiro = $this->getPerdaMandatoConselheiroTO($profissional);
        $profissionalTO->dataFimRegistro = Utils::getValorAtributo($profissional, 'dataFimRegistro');
        $profissionalTO->situacao_registro = Utils::getValorAtributo($profissional, 'situacao_registro');
        $profissionalTO->tempoRegistroAtivo = Utils::getValorAtributo($profissional, 'tempoRegistroAtivo');
        $profissionalTO->infracaoEtica = Utils::getValorAtributo($profissional, 'infracaoEtica', false);

        $profissionalTO->registroProvisorio = Utils::getValorAtributo(
            $profissional, 'registroProvisorio', false
        );

        $profissionalTO->multaProcessoEleitoral = Utils::getValorAtributo(
            $profissional, 'multaProcessoEleitoral', false
        );

        $profissionalTO->infracaoRelacionadaExercicioProfissao = Utils::getValorAtributo(
            $profissional, 'infracaoRelacionadaExercicioProfissao', false
        );

        $profissionalTO->sansionadoInfracaoEticaDisciplinar = $this->getSansionadoInfracaoEticaDisciplinarTO(
            $profissional
        );
    }

    /**
     * Retorna uma instancia de DeclaracaoTO.
     *
     * @param $declaracao
     * @return DeclaracaoTO
     */
    private function getDeclaracaoTO($declaracao)
    {
        $declaracaoTO = new DeclaracaoTO();
        $declaracaoTO->setId(Utils::getValorAtributo($declaracao, 'id'));
        $declaracaoTO->setNome(Utils::getValorAtributo($declaracao, 'nome'));
        $declaracaoTO->setTitulo(Utils::getValorAtributo($declaracao, 'titulo'));
        $declaracaoTO->setObjetivo(Utils::getValorAtributo($declaracao, 'objetivo'));
        $declaracaoTO->setSequencial(Utils::getValorAtributo($declaracao, 'sequencial'));
        $declaracaoTO->setTextoInicial(Utils::getValorAtributo($declaracao, 'textoInicial'));
        $declaracaoTO->setTipoResposta(Utils::getValorAtributo($declaracao, 'tipoResposta'));
        $declaracaoTO->setAtivo(Utils::getValorAtributo($declaracao, 'ativo', false));
        $declaracaoTO->setPermitePDF(Utils::getValorAtributo($declaracao, 'permitePDF', false));
        $declaracaoTO->setPermiteDOC(Utils::getValorAtributo($declaracao, 'permiteDOC', false));
        $declaracaoTO->setPermiteUpload(Utils::getValorAtributo($declaracao, 'permiteUpload', false));
        $declaracaoTO->setUploadObrigatorio(Utils::getValorAtributo($declaracao, 'uploadObrigatorio', false));

        $itens = array_map(function ($dados) {
            return $this->getItemDeclaracaoTO($dados);
        }, Utils::getValorAtributo($declaracao, 'itensDeclaracao', []));

        $declaracaoTO->setItensDeclaracao($itens);

        return $declaracaoTO;
    }

    /**
     * Retorna uma instancia de ItemDeclaracaoTO.
     *
     * @param $itemDeclaracao
     * @return ItemDeclaracaoTO
     */
    private function getItemDeclaracaoTO($itemDeclaracao)
    {
        $itemDeclaracaoTO = new ItemDeclaracaoTO();
        $itemDeclaracaoTO->setId(Utils::getValorAtributo($itemDeclaracao, 'id'));
        $itemDeclaracaoTO->setDescricao(Utils::getValorAtributo($itemDeclaracao, 'descricao'));
        $itemDeclaracaoTO->setSequencial(Utils::getValorAtributo($itemDeclaracao, 'sequencial'));
        return $itemDeclaracaoTO;
    }

    /**
     * Método auxiliar para retornar uma instância do objeto ConselheiroSubsequenteTO
     *
     * @param stdClass $profissional
     * @return stdClass
     */
    private function getConselheiroSubsequenteTO(?stdClass $profissional)
    {
        $conselheiroSubsequenteTO = new stdClass();

        $conselheiroSubsequente = Utils::getValorAtributo($profissional, 'conselheiroSubsequente');
        $conselheiroSubsequenteTO->cargo = Utils::getValorAtributo($conselheiroSubsequente, 'cargo');
        $conselheiroSubsequenteTO->situacao = Utils::getValorAtributo(
            $conselheiroSubsequente, 'situacao', false
        );

        return $conselheiroSubsequenteTO;
    }

    /**
     * Método auxiliar para retornar uma instância do objeto PerdaMandatoConselheiroTO
     *
     * @param stdClass $profissional
     * @return stdClass
     */
    private function getPerdaMandatoConselheiroTO(?stdClass $profissional)
    {
        $perdaMandatoConselheiroTO = new stdClass();

        $perdaMandatoConselheiro = Utils::getValorAtributo($profissional, 'perdaMandatoConselheiro');

        $perdaMandatoConselheiroTO->situacao = Utils::getValorAtributo(
            $perdaMandatoConselheiro, 'situacao', false
        );
        $perdaMandatoConselheiroTO->dataPerdaMandato = Utils::getValorAtributo(
            $perdaMandatoConselheiro, 'dataPerdaMandato'
        );

        return $perdaMandatoConselheiroTO;
    }

    /**
     * Método auxiliar para retornar uma instância do objeto SansionadoInfracaoEticaDisciplinarTO
     *
     * @param stdClass $profissional
     * @return stdClass
     */
    private function getSansionadoInfracaoEticaDisciplinarTO(?stdClass $profissional)
    {
        $sansionadoInfracaoEticaDisciplinarTO = new stdClass();

        $sansionadoInfracaoEticaDisciplinar = Utils::getValorAtributo(
            $profissional,
            'sansionadoInfracaoEticaDisciplinar'
        );
        $sansionadoInfracaoEticaDisciplinarTO->situacao = Utils::getValorAtributo(
            $sansionadoInfracaoEticaDisciplinar, 'situacao', false
        );
        $sansionadoInfracaoEticaDisciplinarTO->dataReabilitacao = Utils::getValorAtributo(
            $sansionadoInfracaoEticaDisciplinar, 'dataReabilitacao'
        );

        return $sansionadoInfracaoEticaDisciplinarTO;
    }


    /**
     * Retorna uma instância de 'RestClient'.
     *
     * @param null $authorization
     * @return RestClient
     */
    protected function getRestClient($authorization = null): RestClient
    {
        $headers = ["Content-Type: application/json"];

        if ($authorization) {
            $headers[] = sprintf("Authorization: %s %s", Constants::PARAM_BEARER, Input::bearerToken());
        }

        return RestClient::newInstance()->addHeaders($headers);
    }

    /**
     * Retorna o usuário conforme o padrão lazy Inicialization.
     *
     * @return UsuarioFactory | null
     */
    public function getUsuarioFactory()
    {
        if ($this->usuarioFactory == null) {
            $this->usuarioFactory = app()->make(UsuarioFactory::class);
        }

        return $this->usuarioFactory;
    }

    /**
     * @param $response
     * @return stdClass
     */
    private function getFilialTO($response): stdClass
    {
        $filial = new stdClass();
        $filial->id = Utils::getValorAtributo($response, "id");
        $filial->prefixo = Utils::getValorAtributo($response, "prefixo");
        $filial->descricao = Utils::getValorAtributo($response, "descricao");
        return $filial;
    }
}
