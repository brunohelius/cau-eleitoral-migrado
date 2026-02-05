<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 29/10/2019
 * Time: 14:55
 */

use App\Business\MembroComissaoBO;
use App\Config\Constants;
use App\Security\Token\TokenContext;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use App\Entities\TipoParticipacaoMembro;
use App\Repository\TipoParticipacaoMembroRepository;
use App\Repository\MembroComissaoRepository;
use App\Entities\MembroComissao;
use App\Service\CorporativoService;
use App\Entities\SituacaoCalendario;
use App\Entities\MembroComissaoSituacao;
use Illuminate\Http\Request;
use App\Repository\InformacaoComissaoMembroRepository;
use App\Entities\InformacaoComissaoMembro;
use App\Repository\SituacaoMembroComissaoRepository;
use App\Business\HistoricoInformacaoComissaoMembroBO;
use App\Entities\HistoricoInformacaoComissaoMembro;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use Doctrine\ORM\ORMException;
use App\Entities\AtividadePrincipalCalendario;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Calendario;
use App\Business\AtividadeSecundariaCalendarioBO;
use App\Business\ProfissionalBO;

/**
 * Teste de Unidade referente à classe MembroComissaoBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class MembroComissaoBOTest extends TestCase
{
    const ID_TIPO_PARTICIPACAO = 1;
    const ID_INF_COMISSAO = 52;
    const ID_CAU_UF = 200;
    const ID_MEMBRO_COMISSAO = 42;
    const ID_PESSOA = 66;
    const ID_SITUACAO_MEMBRO = 2;

    /**
     * Testa a execução do método 'getTipoParticipacao', com sucesso, retornando uma lista
     * de todos tipos de participação.
     *
     * @throws ReflectionException
     */
    public function testarGetTipoParticipacaoComSucesso()
    {
        $tipoParticipacao = TipoParticipacaoMembro::newInstance();
        $tipoParticipacao->setId(self::ID_TIPO_PARTICIPACAO);
        $listaTipoParticipacao = new ArrayCollection();
        $listaTipoParticipacao->add($tipoParticipacao);

        $tipoParticipacaoRepositoryMock = $this->createMock(TipoParticipacaoMembroRepository::class);
        $tipoParticipacaoRepositoryMock->method('findAll')->willReturn($listaTipoParticipacao);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'tipoParticipacaoRepository', $tipoParticipacaoRepositoryMock);

        $this->assertNotEmpty($membroComissaoBO->getTipoParticipacao());
    }

    /**
     * Testa a execução do método 'getTipoParticipacao', não havendo nenhum tipo de participação.
     *
     * @throws ReflectionException
     */
    public function testarGetTipoParticipacaoVazio()
    {
        $tipoParticipacaoRepositoryMock = $this->createMock(TipoParticipacaoMembroRepository::class);
        $tipoParticipacaoRepositoryMock->method('findAll')->willReturn(null);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'tipoParticipacaoRepository', $tipoParticipacaoRepositoryMock);

        $this->assertNull($membroComissaoBO->getTipoParticipacao());
    }

    /**
     * Testa a execução do método 'getPorInformacaoComissaoCauUf' com sucesso, retornando uma lista de membros
     * dado um determinado id de informacaoComissaoMembro e cauUf
     *
     * @throws ReflectionException
     */
    public function testarGetPorInformacaoComissaoCauUfComSucesso()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $profissional = new stdClass();
        $profissional->id = self::ID_PESSOA;

        $listaMembros = new ArrayCollection();
        $listaMembros->add($this->criarMembroComissao());

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getPorInformacaoComissao')->withAnyParameters()->willReturn($listaMembros);

        $corporativoServiceMock = $this->createMock(CorporativoService::class);
        $corporativoServiceMock->method('getProfissionalPorId')->withAnyParameters()->willReturn($profissional);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);
        $this->setPrivateProperty($membroComissaoBO, 'corporativoService', $corporativoServiceMock);

        $this->assertNotEmpty($membroComissaoBO->getPorInformacaoComissaoCauUf(self::ID_INF_COMISSAO, self::ID_CAU_UF,
            $usuarioLogado));
    }

    /**
     * Testa a execução do método 'getPorInformacaoComissaoCauUf', retornando vazia a lista de membros
     * dado um determinado id de informacaoComissaoMembro e cauUf
     *
     * @throws ReflectionException
     */
    public function testarGetPorInformacaoComissaoCauUfVazio()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getPorInformacaoComissao')->withAnyParameters()->willReturn(null);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);

        $listaMembros = $membroComissaoBO->getPorInformacaoComissaoCauUf(self::ID_INF_COMISSAO, self::ID_CAU_UF, $usuarioLogado);
        $this->assertEmpty($listaMembros["membros"]);
    }

    /**
     * Testa a execução do método 'getPorInformacaoComissao' com sucesso, retornando uma lista de membros
     * dado um determinado id de informacaoComissaoMembro
     *
     * @throws ReflectionException
     */
    public function testarGetPorInformacaoComissaoComSucesso()
    {
        $profissional = new stdClass();
        $profissional->id = self::ID_PESSOA;

        $listaMembros = new ArrayCollection();
        $listaMembros->add($this->criarMembroComissao());

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getPorInformacaoComissao')->withAnyParameters()->willReturn($listaMembros);

        $corporativoServiceMock = $this->createMock(CorporativoService::class);
        $corporativoServiceMock->method('getProfissionalPorId')->withAnyParameters()->willReturn($profissional);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);
        $this->setPrivateProperty($membroComissaoBO, 'corporativoService', $corporativoServiceMock);

        $this->assertNotEmpty($membroComissaoBO->getPorInformacaoComissao(self::ID_INF_COMISSAO));
    }

    /**
     * Testa a execução do método 'getPorInformacaoComissao', retornando vazia a lista de membros
     * dado um determinado id de informacaoComissaoMembro
     *
     * @throws ReflectionException
     */
    public function testarGetPorInformacaoComissaoVazio()
    {
        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getPorInformacaoComissao')->withAnyParameters()->willReturn(null);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);

        $this->assertEmpty($membroComissaoBO->getPorInformacaoComissao(self::ID_INF_COMISSAO));
    }

    /**
     * Testa a execução do método 'getTotalMembrosPorCauUf' com sucesso, retornando uma lista de cau uf e a
     * quantidade dado um determinado id de cau uf
     *
     * @throws ReflectionException
     */
    public function testarGetTotalMembrosPorCauUfComSucesso()
    {
        $retornoUf = new stdClass();
        $retornoUf->idCauUf = self::ID_CAU_UF;
        $retornoUf->quantidade = 10;
        $arrayResult = new ArrayCollection();
        $arrayResult->add($retornoUf);

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getTotalMembrosPorCauUf')->withAnyParameters()->willReturn($arrayResult);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);

        $this->assertNotEmpty($membroComissaoBO->getTotalMembrosPorCauUf(self::ID_CAU_UF));
    }

    /**
     * Testa a execução do método 'getTotalMembrosPorCauUf', retornando vazio para ma lista de cau uf e a
     * quantidade dado um determinado id de cau uf
     *
     * @throws ReflectionException
     */
    public function testarGetTotalMembrosPorCauUfVazio()
    {
        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getTotalMembrosPorCauUf')->withAnyParameters()->willReturn(new ArrayCollection());

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);

        $this->assertEmpty($membroComissaoBO->getTotalMembrosPorCauUf(self::ID_CAU_UF));
    }

    /**
     * Testa a execução do método 'salvar' com sucesso
     *
     * @throws ReflectionException
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function testarSalvarComSucesso()
    {
        $membrosArray = new ArrayCollection();
        $membrosArray->add($this->criarMembroComissao(false, true));
        $membrosArray->add($this->criarMembroComissao(false));

        $calendario = Calendario::newInstance();
        $calendario->setId(1);
        $atvPrincipal = AtividadePrincipalCalendario::newInstance();
        $atvPrincipal->setId(1);
        $atvPrincipal->setCalendario($calendario);
        $atvSecundaria = AtividadeSecundariaCalendario::newInstance();
        $atvSecundaria->setId(1);
        $atvSecundaria->setAtividadePrincipalCalendario($atvPrincipal);

        $informacaoComissaoMembro = InformacaoComissaoMembro::newInstance();
        $informacaoComissaoMembro->setId(self::ID_INF_COMISSAO);
        $informacaoComissaoMembro->setAtividadeSecundaria($atvSecundaria);

        $dadosTO = new stdClass();
        $dadosTO->membros = $membrosArray;
        $dadosTO->informacaoComissaoMembro = $informacaoComissaoMembro;
        $dadosTO->justificativa = "Descrição da Justificativa";

        $retornoUf = array();
        $retornoUf['idCauUf'] = self::ID_CAU_UF;
        $retornoUf['quantidade'] = 10;
        $arrayResult = array();
        $arrayResult[] = $retornoUf;

        $informacaoComissaoMembroRepositoryMock = $this->createMock(InformacaoComissaoMembroRepository::class);
        $informacaoComissaoMembroRepositoryMock->method('find')->withAnyParameters()->willReturn($informacaoComissaoMembro);

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getTotalMembrosPorCauUf')->withAnyParameters()->willReturn($arrayResult);
        $membroComissaoRepositoryMock->method('deleteMembrosPorInformacaoCauUf')->withAnyParameters()->willReturn(true);
        $membroComissaoRepositoryMock->method('persist')->withAnyParameters()->willReturn($this->criarMembroComissao(false));

        $situacaoMembroComissaoRepositoryMock = $this->createMock(SituacaoMembroComissaoRepository::class);
        $situacaoMembroComissaoRepositoryMock->method('find')->withAnyParameters()->willReturn($this->criarMembroSituacao());

        $historicoInformacaoComissaoMembroBOMock = $this->createMock(HistoricoInformacaoComissaoMembroBO::class);
        $historicoInformacaoComissaoMembroBOMock->method('salvar')->withAnyParameters()->willReturn(true);
        $historicoInformacaoComissaoMembroBOMock->method('criarHistorico')->withAnyParameters()->willReturn($this->criarHistorico($informacaoComissaoMembro, $dadosTO->justificativa));

        $atividadeSecundariaBOMock = $this->createMock(AtividadeSecundariaCalendarioBO::class);
        $atividadeSecundariaBOMock->method('getPorCalendario')->willReturn(null);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'informacaoComissaoMembroRepository', $informacaoComissaoMembroRepositoryMock);
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);
        $this->setPrivateProperty($membroComissaoBO, 'situacaoMembroComissaoRepository', $situacaoMembroComissaoRepositoryMock);
        $this->setPrivateProperty($membroComissaoBO, 'historicoInformacaoComissaoMembroBO', $historicoInformacaoComissaoMembroBOMock);
        $this->setPrivateProperty($membroComissaoBO, 'atividadeSecundariaBO', $atividadeSecundariaBOMock);

        $this->assertEmpty($membroComissaoBO->salvar($dadosTO));
    }

    /**
     * Testa a execução do método 'salvar' sem sucesso, com mensagem de campos obrigatórios
     *
     * @throws Exception
     */
    public function testarSalvarSemSucessoCamposObrigatorios()
    {
        $membrosArray = new ArrayCollection();
        $membrosArray->add($this->criarMembroComissao(false, true, true));
        $membrosArray->add($this->criarMembroComissao(false));

        $informacaoComissaoMembro = InformacaoComissaoMembro::newInstance();
        $informacaoComissaoMembro->setId(self::ID_INF_COMISSAO);

        $dadosTO = new stdClass();
        $dadosTO->membros = $membrosArray;
        $dadosTO->informacaoComissaoMembro = $informacaoComissaoMembro;
        $dadosTO->justificativa = "Descrição da Justificativa";

        $membroComissaoBO = new MembroComissaoBO();
        $request = new Request();

        try {
            $this->assertEmpty($membroComissaoBO->salvar($dadosTO, $request));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Testa a execução do método 'salvar' sem sucesso, com exception
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarSalvarSemSucessoException()
    {
        $membrosArray = new ArrayCollection();
        $membrosArray->add($this->criarMembroComissao(false, true));
        $membrosArray->add($this->criarMembroComissao(false));

        $informacaoComissaoMembro = InformacaoComissaoMembro::newInstance();
        $informacaoComissaoMembro->setId(self::ID_INF_COMISSAO);

        $dadosTO = new stdClass();
        $dadosTO->membros = $membrosArray;
        $dadosTO->informacaoComissaoMembro = $informacaoComissaoMembro;
        $dadosTO->justificativa = "Descrição da Justificativa";

        $e = new ORMException();

        $informacaoComissaoMembroRepositoryMock = $this->createMock(InformacaoComissaoMembroRepository::class);
        $informacaoComissaoMembroRepositoryMock->method('find')->withAnyParameters()->willReturn($informacaoComissaoMembro);

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('persist')->withAnyParameters()->willThrowException($e);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'informacaoComissaoMembroRepository', $informacaoComissaoMembroRepositoryMock);
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);

        $request = new Request();

        try {
            $this->assertEmpty($membroComissaoBO->salvar($dadosTO, $request));
        } catch (ORMException $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testa a execução do método 'getPorId' com sucesso
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarGetPorIdComSucesso()
    {
        $membroComissao = $this->criarMembroComissao(false, true);

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getPorId')->willReturn($membroComissao);

        $corporativoServiceMock = $this->createMock(CorporativoService::class);
        $corporativoServiceMock->method('getProfissionalPorId')->willReturn($this->criarProfissionalTO());

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);
        $this->setPrivateProperty($membroComissaoBO, 'corporativoService', $corporativoServiceMock);

        $this->assertNotEmpty($membroComissaoBO->getPorId(self::ID_MEMBRO_COMISSAO));
    }

    /**
     * Testa a execução do método 'getPorId' sem sucesso
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarGetPorIdSemSucesso()
    {
        $membroComissao = $this->criarMembroComissao(false, true);

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getPorId')->willReturn($membroComissao);

        $corporativoServiceMock = $this->createMock(CorporativoService::class);
        $corporativoServiceMock->method('getProfissionalPorId')->willReturn(null);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);
        $this->setPrivateProperty($membroComissaoBO, 'corporativoService', $corporativoServiceMock);

        try {
            $this->assertNotEmpty($membroComissaoBO->getPorId(self::ID_MEMBRO_COMISSAO));
        } catch (ORMException $e) {
            $this->assertSame($e->getMessage(), Message::NENHUM_MEMBRO_ENCONTRADO);
        }
    }

    /**
     * Testa a execução do método 'getListaMembrosPorMembro' com sucesso
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarGetListaMembrosPorMembroComSucesso()
    {
        $profissional = new stdClass();
        $profissional->id = self::ID_PESSOA;

        $listaMembros = new ArrayCollection();
        $listaMembros->add($this->criarMembroComissao());

        $corporativoServiceMock = $this->createMock(CorporativoService::class);
        $corporativoServiceMock->method('getProfissionalPorId')->withAnyParameters()->willReturn($profissional);

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getListaMembrosPorMembro')->willReturn($listaMembros);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);
        $this->setPrivateProperty($membroComissaoBO, 'corporativoService', $corporativoServiceMock);

        $this->assertNotEmpty($membroComissaoBO->getListaMembrosPorMembro(self::ID_MEMBRO_COMISSAO, 165));
    }

    /**
     * Testa a execução do método 'getListaMembrosPorMembro' sem sucesso
     *
     * @throws ReflectionException
     */
    public function testarGetListaMembrosPorMembroSemSucesso()
    {
        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getListaMembrosPorMembro')->willReturn(null);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);

        try {
            $this->assertNotEmpty($membroComissaoBO->getListaMembrosPorMembro(self::ID_MEMBRO_COMISSAO, 165));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::NENHUM_MEMBRO_ENCONTRADO]);
        }
    }

    /**
     * Testa a execução do método getMembrosComissaoPorUf com sucesso
     *
     * @throws ReflectionException
     */
    public function testarGetMembrosComissaoPorUfComSucesso()
    {
        $idCauUf = 165;
        $membros[] = $this->criarMembroComissao();
        $membros[] = $this->criarMembroComissao();

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getPorFiltro')->willReturn($membros);

        $profissionalBOMock = $this->createMock(ProfissionalBO::class);
        $profissionalBOMock->method('getListaProfissionaisFormatadaPorIds')->willReturn([]);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);
        $this->setPrivateProperty($membroComissaoBO, 'profissionalBO', $profissionalBOMock);

        $this->assertNotEmpty($membroComissaoBO->getMembrosComissaoPorUf($idCauUf));
    }

    /**
     * Testa a execução do método 'getMembrosComissaoPorUf' retornando um array de dados vazio
     *
     * @throws ReflectionException
     */
    public function testarGetMembrosComissaoPorUfVazio()
    {
        $idCauUf = 165;

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('getPorFiltro')->willReturn([]);

        $profissionalBOMock = $this->createMock(ProfissionalBO::class);
        $profissionalBOMock->method('getListaProfissionaisFormatadaPorIds')->willReturn([]);

        $membroComissaoBO = new MembroComissaoBO();
        $this->setPrivateProperty($membroComissaoBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);
        $this->setPrivateProperty($membroComissaoBO, 'profissionalBO', $profissionalBOMock);

        $this->assertEmpty($membroComissaoBO->getMembrosComissaoPorUf($idCauUf));
    }

    /**
     * @return \App\Entities\Calendario
     * @throws Exception
     */
    private function criarMembroComissao($criaSituacao = true, $criaSubstituto = false, $naoCriaCauUf = false)
    {
        $tipoParticipacao = TipoParticipacaoMembro::newInstance();
        $tipoParticipacao->setId(self::ID_TIPO_PARTICIPACAO);

        $membroComissao = MembroComissao::newInstance();
        $membroComissao->setId(self::ID_MEMBRO_COMISSAO);
        $membroComissao->setPessoa(self::ID_PESSOA);
        if(!$naoCriaCauUf) {
            $membroComissao->setIdCauUf(self::ID_CAU_UF);
        }
        $membroComissao->setTipoParticipacao($tipoParticipacao);

        if ($criaSubstituto) {
            $membroComissao->setMembroSubstituto($this->criarMembroComissao(false));
        }

        if ($criaSituacao) {
            $membroComissao->setMembroComissaoSituacao(new ArrayCollection());
            $membroComissao->getMembroComissaoSituacao()->add($this->criarMembroSituacao());
        }

        return $membroComissao;
    }

    /**
     * @return MembroComissaoSituacao
     * @throws Exception
     */
    private function criarMembroSituacao()
    {
        $situacaoMembro = SituacaoCalendario::newInstance();
        $situacaoMembro->setId(self::ID_SITUACAO_MEMBRO);

        $membroSituacao = MembroComissaoSituacao::newInstance();
        $membroSituacao->setId(self::ID_TIPO_PARTICIPACAO);
        $membroSituacao->setData(Utils::getData());
        $membroSituacao->setSituacaoMembroComissao($situacaoMembro);

        return $membroSituacao;
    }

    /**
     * @param $informacaoComissaoMembro
     * @param string $justificativa
     * @return HistoricoInformacaoComissaoMembro
     * @throws Exception
     */
    private function criarHistorico($informacaoComissaoMembro, $justificativa = '')
    {
        $historico = HistoricoInformacaoComissaoMembro::newInstance();
        $historico->setResponsavel(self::ID_INF_COMISSAO);
        $historico->setAcao(Constants::ACAO_HISTORICO_INFORMACAO_COMISSAO_INSERIR);
        $historico->setInformacaoComissaoMembro($informacaoComissaoMembro);
        $historico->setDataHistorico(Utils::getData());
        $historico->setJustificativa($justificativa);

        return $historico;
    }

    /**
     * @return stdClass
     */
    private function criarProfissionalTO()
    {
        $profissionalTO = new stdClass();

        $profissionalTO->id = self::ID_MEMBRO_COMISSAO;
        $profissionalTO->nome = 'Nome Teste';
        $profissionalTO->cpf = '12332112332';
        $profissionalTO->email = 'teste@teste.com';

        return $profissionalTO;
    }
}
