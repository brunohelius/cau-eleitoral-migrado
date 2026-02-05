<?php

use App\Util\Utils;
use App\To\EleicaoTO;
use App\To\CalendarioTO;
use App\Config\Constants;
use App\Entities\Eleicao;
use App\Exceptions\Message;
use App\Business\EleicaoBO;
use App\Entities\Calendario;
use Illuminate\Http\Request;
use App\Entities\UfCalendario;
use App\Entities\TipoProcesso;
use App\Business\CalendarioBO;
use App\To\CalendarioFiltroTO;
use App\Service\ArquivoService;
use App\To\AtividadeSecundariaTO;
use App\Entities\SituacaoEleicao;
use App\Entities\EleicaoSituacao;
use App\Entities\ArquivoCalendario;
use App\Entities\CalendarioSituacao;
use App\Exceptions\NegocioException;
use App\Entities\SituacaoCalendario;
use App\Entities\HistoricoCalendario;
use App\Repository\EleicaoRepository;
use App\To\DocumentoComissaoMembroTO;
use App\To\InformacaoComissaoMembroTO;
use App\Business\AtividadePrincipalBO;
use App\Business\HistoricoCalendarioBO;
use App\Entities\TipoProcessoCalendario;
use App\Repository\CalendarioRepository;
use App\To\AtividadePrincipalCalendarioTO;
use Doctrine\ORM\NonUniqueResultException;
use App\Repository\SituacaoEleicaoRepository;
use App\Entities\AtividadePrincipalCalendario;
use App\Entities\AtividadeSecundariaCalendario;
use App\Repository\ArquivoCalendarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\CalendarioSituacaoRepository;
use App\To\CalendarioPublicacaoComissaoEleitoralFiltroTO;

/**
 * Teste de Unidade referente à classe CalendarioBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class CalendarioBOTest extends TestCase
{
    const ID_CALENDARIO = 99;

    const ID_ARQUIVO = 5;

    const ID_ATV_SEC = 200;

    const ID_ATV_PRM = 42;

    const ID_ELEICAO = 1;

    const ID_RESPONSAVEL = 1;

    const ANO_ELEICAO = 2019;

    const SEQUENCIA_ANO_ELEICAO = 1;

    const ID_ATIVIDADE_PRINCIPAL = 1;

    const ID_ATIVIDADE_SECUNDARIA = 1;

    const ID_PUBLICACAO_DOCUMENTO = 1;

    const ID_DOCUMENTO_COMISSAO_MEMBRO = 1;

    const ID_INFORMACAO_COMISSAO_MEMBRO = 1;

    /**
     * Testa a execução do método 'getPorId', com sucesso, retornando o calendário
     * segundo o 'id' informado.
     *
     * @throws NonUniqueResultException
     */
    public function testarGetPorIdComSucesso()
    {
        $calendario = $this->criarCalendario(true, true);
        $calendario->setId(self::ID_CALENDARIO);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getPorId')->willReturn($calendario);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        $this->assertNotEmpty($calendarioBO->getPorId(self::ID_CALENDARIO));
    }

    /**
     * Testa a execução do método 'getPorId', não havendo o calendário
     * segundo o 'id' informado.
     *
     * @throws NonUniqueResultException
     */
    public function testarGetPorIdComIdNaoEncontrado()
    {
        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getPorId')->willReturn(null);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        try {
            $this->assertNotEmpty($calendarioBO->getPorId(self::ID_CALENDARIO));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::NENHUM_CALENDARIO_ENCONTRADO]);
        }
    }

    /**
     * Testa a execução do método 'getCalendariosPorFiltro', com sucesso, retornando a lista de calendarios
     *
     * @throws \App\Exceptions\NegocioException
     */
    public function testarGetCalendariosPorFiltroComSucesso()
    {
        $filtroTO = $this->criarFiltroCalendarioTO();
        $calendarios = $this->criarListaCalendariosTO();

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getCalendariosPorFiltro')->willReturn($calendarios);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        $this->assertNotEmpty($calendarioBO->getCalendariosPorFiltro($filtroTO));
    }

    /**
     * Testa a execução do método 'getCalendariosPorFiltro', sem sucesso, retornando vazio
     *
     * @throws \App\Exceptions\NegocioException
     */
    public function testarGetCalendariosPorFiltroSemResultados()
    {
        $filtroTO = $this->criarFiltroCalendarioTO();

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getCalendariosPorFiltro')->willReturn(null);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        $this->assertNull($calendarioBO->getCalendariosPorFiltro($filtroTO));
    }


    /**
     * Testa a execução do método 'getArquivo', com sucesso.
     *
     * @throws NonUniqueResultException
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function testarGetArquivoComSucesso()
    {
        $calendarioBOMock = $this->createMock(CalendarioBO::class);
        $calendarioBOMock->method('getArquivo')->willReturn($this->criarStdClassArquivoTO());

        $this->assertNotEmpty($calendarioBOMock->getArquivo(self::ID_ARQUIVO));
    }

    /**
     * Testa a execução do método 'getArquivo', sem sucesso.
     *
     * @throws NonUniqueResultException
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function testarGetArquivoSemSucesso()
    {
        $calendarioBOMock = $this->createMock(CalendarioBO::class);
        $calendarioBOMock->method('getArquivo')->willReturn(null);

        $this->assertNull($calendarioBOMock->getArquivo(self::ID_ARQUIVO));
    }

    /**
     * Testa a execução do método 'excluirArquivo', com sucesso.
     *
     * @throws ReflectionException
     */
    public function testarExcluirArquivoComSucesso()
    {
        $arquivo = $this->criarArquivoCalendario();
        $arquivo->setId(self::ID_ARQUIVO);

        $arquivoCalendarioRepositoryMock = $this->createMock(ArquivoCalendarioRepository::class);
        $arquivoCalendarioRepositoryMock->method('find')->willReturn($arquivo);

        $arquivoServiceMock = $this->createMock(ArquivoService::class);
        $arquivoServiceMock->method('excluir')->willReturn(null);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'arquivoCalendarioRepository', $arquivoCalendarioRepositoryMock);
        $this->setPrivateProperty($calendarioBO, 'arquivoService', $arquivoServiceMock);

        $this->assertNull($calendarioBO->excluirArquivo(self::ID_ARQUIVO));
    }

    /**
     * Testa a execução do método 'excluirArquivo', sem sucesso.
     *
     * @throws ReflectionException
     */
    public function testarExcluirArquivoSemSucesso()
    {
        $e = new Exception();
        $arquivo = ArquivoCalendario::newInstance();
        $arquivo->setId(self::ID_ARQUIVO);
        $arquivoCalendarioRepositoryMock = $this->createMock(ArquivoCalendarioRepository::class);
        $arquivoCalendarioRepositoryMock->method('find')->willThrowException($e);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'arquivoCalendarioRepository', $arquivoCalendarioRepositoryMock);

        try {
            $this->assertNull($calendarioBO->excluirArquivo(self::ID_ARQUIVO));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testar o método Salvar
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarSalvarSemAtividadeComSucesso()
    {
        $calendario = $this->criarCalendario();
        $calendarioSalvo = $this->criarCalendario(false, true);
        $calendarioSalvo->setId(self::ID_CALENDARIO);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('persist')->willReturn($calendarioSalvo);
        $calendarioRepositoryMock->method('getPorId')->willReturn($calendarioSalvo);

        $calendarioSituacaoRepositoryMock = $this->createMock(CalendarioSituacaoRepository::class);
        $calendarioSituacaoRepositoryMock->method('persist')->withAnyParameters()->willReturn(true);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);
        $this->setPrivateProperty($calendarioBO, 'calendarioSituacaoRepository', $calendarioSituacaoRepositoryMock);

        $request = new Request();

        $this->assertNotEmpty($calendarioBO->salvar($calendario, $request));
    }

    /**
     * Testar o método Salvar sem sucesso, com mensagem de campo obrigatório
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarSalvarSemAtividadeSemSucesso()
    {
        $calendario = $this->criarCalendario();
        $calendarioSalvo = $this->criarCalendario(false, true);
        $calendarioSalvo->setId(self::ID_CALENDARIO);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('persist')->willReturn($calendarioSalvo);
        $calendarioRepositoryMock->method('getPorId')->willReturn($calendarioSalvo);

        $calendarioSituacaoRepositoryMock = $this->createMock(CalendarioSituacaoRepository::class);
        $calendarioSituacaoRepositoryMock->method('persist')->withAnyParameters()->willReturn(true);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);
        $this->setPrivateProperty($calendarioBO, 'calendarioSituacaoRepository', $calendarioSituacaoRepositoryMock);

        $request = new Request();

        try {
            $this->assertNotEmpty($calendarioBO->salvar($calendario, $request));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Testar método Salvar com as atividades definidas, com sucesso
     *
     * @throws ReflectionException
     */
    public function testarSalvarComAtividadeComSucesso()
    {
        $calendarioSalvo = $this->criarCalendario(true, true);
        $calendarioSalvo->setId(self::ID_CALENDARIO);
        $calendarioSalvo->getEleicao()->setId(self::ID_CALENDARIO);

        $atividadePrincipal = $this->criarAtividadesPrimariasSecundarias();
        $atividadePrincipal->setId(1);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('persist')->willReturn($calendarioSalvo);
        $calendarioRepositoryMock->method('getPorId')->willReturn($calendarioSalvo);

        $atividadePrincipalBOMock = $this->createMock(AtividadePrincipalBO::class);
        $atividadePrincipalBOMock->method('salvar')->willReturn($atividadePrincipal);

        $calendarioSituacaoRepositoryMock = $this->createMock(CalendarioSituacaoRepository::class);
        $calendarioSituacaoRepositoryMock->method('persist')->withAnyParameters()->willReturn(true);

        $eleicaoBOMock = $this->createMock(EleicaoBO::class);
        $eleicaoBOMock->method('salvar')->withAnyParameters()->willReturn(true);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);
        $this->setPrivateProperty($calendarioBO, 'atividadePrincipalBO', $atividadePrincipalBOMock);
        $this->setPrivateProperty($calendarioBO, 'calendarioSituacaoRepository', $calendarioSituacaoRepositoryMock);
        $this->setPrivateProperty($calendarioBO, 'eleicaoBO', $eleicaoBOMock);

        $request = new Request();
        $this->assertNotEmpty($calendarioBO->salvar($calendarioSalvo, $request));
    }

    /**
     * Testar método de Salvar calendário com atividades sem sucesso
     *
     * @throws ReflectionException
     */
    public function testarSalvarComAtividadeSemSucesso()
    {
        $calendario = $this->criarCalendario(true, true);
        $calendarioSalvo = $this->criarCalendario(true, true);
        $calendarioSalvo->setId(self::ID_CALENDARIO);
        $calendarioSalvo->getEleicao()->setId(self::ID_CALENDARIO);

        $atividadePrincipal = $this->criarAtividadesPrimariasSecundarias();
        $atividadePrincipal->setId(1);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('persist')->willReturn($calendarioSalvo);
        $calendarioRepositoryMock->method('getPorId')->willReturn($calendarioSalvo);

        $atividadePrincipalBOMock = $this->createMock(AtividadePrincipalBO::class);
        $atividadePrincipalBOMock->method('salvar')->willReturn($atividadePrincipal);

        $calendarioSituacaoRepositoryMock = $this->createMock(CalendarioSituacaoRepository::class);
        $calendarioSituacaoRepositoryMock->method('persist')->withAnyParameters()->willReturn(true);

        $eleicaoBOMock = $this->createMock(EleicaoBO::class);
        $eleicaoBOMock->method('salvar')->withAnyParameters()->willReturn(true);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);
        $this->setPrivateProperty($calendarioBO, 'atividadePrincipalBO', $atividadePrincipalBOMock);
        $this->setPrivateProperty($calendarioBO, 'calendarioSituacaoRepository', $calendarioSituacaoRepositoryMock);
        $this->setPrivateProperty($calendarioBO, 'eleicaoBO', $eleicaoBOMock);

        $request = new Request();

        try {
            $this->assertNotEmpty($calendarioBO->salvar($calendario, $request));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::MSG_DATA_FIM_DIVERGENTE_VIGENCIA]);
        }
    }

    /**
     * Testar método de exclusão lógica do calendário, com sucesso
     *
     * @throws ReflectionException
     */
    public function testarExcluirCalendarioComSucesso()
    {
        $calendario = $this->criarCalendario();
        $calendario->setId(self::ID_CALENDARIO);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getPorId')->willReturn($calendario);
        $calendarioRepositoryMock->method('persist')->willReturn($calendario);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        $this->assertNotNull($calendarioBO->excluir(self::ID_CALENDARIO));
    }

    /**
     * Testar método de exclusão lógica do calendário, sem sucesso
     *
     * @throws ReflectionException
     */
    public function testarExcluirCalendarioSemSucesso()
    {
        $calendario = $this->criarCalendario();
        $calendario->setId(self::ID_CALENDARIO);

        $historico = $this->criarHistorico($calendario);
        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getPorId')->willReturn($calendario);
        $calendarioRepositoryMock->method('persist')->willReturn($calendario);

        $historicoBOMock = $this->createMock(HistoricoCalendarioBO::class);
        $historicoBOMock->method('criarHistorico')->willReturn($historico);
        $historicoBOMock->method('salvar')->willReturn(null);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);
        $this->setPrivateProperty($calendarioBO, 'historicoCalendarioBO', $historicoBOMock);

        try {
            $this->assertNotNull($calendarioBO->excluir(self::ID_CALENDARIO));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage());
        }
    }

    /**
     * Testar método Inativar calendário com sucesso
     *
     * @throws ReflectionException
     */
    public function testarInativarComSucesso()
    {
        $calendarioTO = CalendarioTO::newInstance();
        $calendarioTO->setId(self::ID_CALENDARIO);

        $calendario = $this->criarCalendario(false, true);
        $calendario->setId(self::ID_CALENDARIO);
        $eleicao = Eleicao::newInstance();
        $eleicao->setId(self::ID_CALENDARIO);
        $calendario->setEleicao($eleicao);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getPorId')->willReturn($calendario);
        $calendarioRepositoryMock->method('persist')->willReturn($calendario);

        $eleicaoBOMock = $this->createMock(EleicaoBO::class);
        $eleicaoBOMock->method('inativar')->willReturn(true);

        $calendarioSituacaoRepositoryMock = $this->createMock(CalendarioSituacaoRepository::class);
        $calendarioSituacaoRepositoryMock->method('persist')->withAnyParameters()->willReturn(true);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);
        $this->setPrivateProperty($calendarioBO, 'eleicaoBO', $eleicaoBOMock);
        $this->setPrivateProperty($calendarioBO, 'calendarioSituacaoRepository', $calendarioSituacaoRepositoryMock);

        $request = new Request();
        $this->assertNotNull($calendarioBO->inativar($calendarioTO, $request));
    }

    /**
     * Testar método Inativar calendário sem sucesso
     *
     * @throws ReflectionException
     */
    public function testarInativarSemSucesso()
    {
        $calendarioTO = CalendarioTO::newInstance();
        $calendarioTO->setId(self::ID_CALENDARIO);

        $calendario = $this->criarCalendario();
        $calendario->setId(self::ID_CALENDARIO);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getPorId')->willReturn($calendario);
        $calendarioRepositoryMock->method('persist')->willReturn($calendario);

        $calendarioSituacaoRepositoryMock = $this->createMock(CalendarioSituacaoRepository::class);
        $calendarioSituacaoRepositoryMock->method('persist')->withAnyParameters()->willReturn(true);

        $historico = $this->criarHistorico($calendario);
        $e = new Exception();

        $historicoBO = $this->createMock(HistoricoCalendarioBO::class);
        $historicoBO->method('salvar')->willThrowException($e);
        $historicoBO->method('criarHistorico')->willReturn($historico);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);
        $this->setPrivateProperty($calendarioBO, 'historicoCalendarioBO', $historicoBO);
        $this->setPrivateProperty($calendarioBO, 'calendarioSituacaoRepository', $calendarioSituacaoRepositoryMock);

        $request = new Request();

        try {
            $this->assertNotNull($calendarioBO->inativar($calendarioTO, $request));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testar método Concluir calendário com sucesso
     *
     * @throws ReflectionException
     */
    public function testarConcluirComSucesso()
    {
        $calendarioTO = CalendarioTO::newInstance();
        $calendarioTO->setId(self::ID_CALENDARIO);

        $calendario = $this->criarCalendario(false, true);
        $calendario->setId(self::ID_CALENDARIO);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getPorId')->willReturn($calendario);
        $calendarioRepositoryMock->method('persist')->willReturn($calendario);

        $calendarioSituacaoRepositoryMock = $this->createMock(CalendarioSituacaoRepository::class);
        $calendarioSituacaoRepositoryMock->method('persist')->withAnyParameters()->willReturn(true);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);
        $this->setPrivateProperty($calendarioBO, 'calendarioSituacaoRepository', $calendarioSituacaoRepositoryMock);

        $request = new Request();

        $this->assertNotNull($calendarioBO->concluir($calendarioTO, $request));
    }

    /**
     * Testar método Inativar calendário sem sucesso
     *
     * @throws ReflectionException
     */
    public function testarConcluirSemSucesso()
    {
        $calendarioTO = CalendarioTO::newInstance();
        $calendarioTO->setId(self::ID_CALENDARIO);

        $calendario = $this->criarCalendario();
        $calendario->setId(self::ID_CALENDARIO);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getPorId')->willReturn($calendario);
        $calendarioRepositoryMock->method('persist')->willReturn($calendario);

        $calendarioSituacaoRepositoryMock = $this->createMock(CalendarioSituacaoRepository::class);
        $calendarioSituacaoRepositoryMock->method('persist')->withAnyParameters()->willReturn(true);

        $historico = $this->criarHistorico($calendario);
        $e = new Exception();

        $historicoBO = $this->createMock(HistoricoCalendarioBO::class);
        $historicoBO->method('salvar')->willThrowException($e);
        $historicoBO->method('criarHistorico')->willReturn($historico);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);
        $this->setPrivateProperty($calendarioBO, 'historicoCalendarioBO', $historicoBO);
        $this->setPrivateProperty($calendarioBO, 'calendarioSituacaoRepository', $calendarioSituacaoRepositoryMock);

        $request = new Request();

        try {
            $this->assertNotNull($calendarioBO->concluir($calendarioTO, $request));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Teste Unitário do método getAtividadePrincipalPorCalendarioComFiltro sobre cenário de busca com sucesso.
     */
    public function testeGetTotalCalendariosPorSituacao_ComTotal3()
    {
        $situacao = Constants::ACAO_CALENDARIO_CONCLUIR;
        $calendarioBO = new CalendarioBO();
        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getTotalCalendariosPorSituacao')->willReturn(3);

        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        $this->assertSame($calendarioBO->getTotalCalendariosPorSituacao($situacao), 3);
    }

    /**
     * Teste Unitário do método getAtividadePrincipalPorCalendarioComFiltro sobre cenário de busca com total igual a zero.
     */
    public function testarGetAtividadePrincipalPorCalendarioComFiltro_SemCalendario()
    {
        $situacao = Constants::ACAO_CALENDARIO_CONCLUIR;
        $calendarioBO = new CalendarioBO();
        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getTotalCalendariosPorSituacao')->willReturn(0);

        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        $this->assertSame($calendarioBO->getTotalCalendariosPorSituacao($situacao), 0);
    }

    /**
     * Realiza o teste de recuperação dos dados de calendário para publicação da comissão eleitoral.
     *
     * @throws NonUniqueResultException
     * @throws ReflectionException
     */
    public function testarGetCalendariosPublicacaoComissaoEleitoralComSucesso()
    {
        $documentosComissao = $this->criarListaDocumentoComissaoCalendariosTO();

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getCalendariosPublicacaoComissaoEleitoral')
            ->willReturn($documentosComissao);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        $this->assertNotEmpty($calendarioBO->getCalendariosPublicacaoComissaoEleitoral());
    }

    /**
     * Realiza o teste de recuperação dos dados de anos de calendário para publicação da comissão eleitoral.
     *
     * @throws NonUniqueResultException
     * @throws ReflectionException
     */
    public function testarGetAnosCalendarioPublicacaoComissaoEleitoralComSucesso()
    {
        $documentosComissao = $this->criarListaAnosDocumentoComissaoCalendariosTO();

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getCalendariosPublicacaoComissaoEleitoral')
            ->willReturn($documentosComissao);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        $this->assertNotEmpty($calendarioBO->getAnosCalendarioPublicacaoComissaoEleitoral());
    }

    /**
     * Realiza o teste de recuperação dos dados de calendário para publicação da comissão eleitoral por filtro.
     *
     * @throws NonUniqueResultException
     * @throws ReflectionException
     */
    public function testarGetCalendarioPublicacaoComissaoEleitoralPorFiltroComSucesso()
    {
        $filtroTO = $this->getCalendarioPublicacaoComissaoEleitoralFiltroTO();
        $documentosComissao = $this->criarListaAnosDocumentoComissaoCalendariosTO();

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getCalendarioPublicacaoComissaoEleitoralPorFiltro')
            ->willReturn($documentosComissao);

        $calendarioBO = new CalendarioBO();
        $this->setPrivateProperty($calendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        $this->assertNotEmpty($calendarioBO->getCalendarioPublicacaoComissaoEleitoralPorFiltro($filtroTO));
    }

    /**
     * @return CalendarioFiltroTO
     */
    private function criarFiltroCalendarioTO($idsCalendarios = array())
    {
        $filtroTO = new CalendarioFiltroTO();
        $filtroTO->setAnos(array(2017, 2018, 2019));
        $filtroTO->setIdTipoProcesso(Constants::TIPO_PROCESSO_ORDINARIO);
        $filtroTO->setIdsCalendariosEleicao($idsCalendarios);

        return $filtroTO;
    }

    /**
     * @return ArrayCollection
     */
    private function criarListaCalendarios()
    {
        $calendarios = new ArrayCollection();

        for ($i = 0; $i < 10; $i++) {
            $calendario = Calendario::newInstance();
            $calendario->setId($i + 1);
            $calendarios->add($calendario);
        }
        return $calendarios;
    }

    /**
     * @return ArrayCollection
     */
    private function criarListaCalendariosTO()
    {
        $calendarios = new ArrayCollection();

        for ($i = 0; $i < 10; $i++) {
            $calendario = CalendarioTO::newInstance();
            $calendario->setId($i + 1);
            $calendarios->add($calendario);
            $calendario->setDataInicioVigencia(new DateTime('2015-01-01'));
            $calendario->setDataFimVigencia(new DateTime('now'));
        }
        return $calendarios;
    }

    /**
     * @return stdClass
     */
    private function criarStdClassArquivoTO()
    {
        $arquivo = new stdClass();
        $arquivo->name = "nomeTest.pdf";
        $arquivo->size = 12333;
        $arquivo->type = 'pdf';
        return $arquivo;
    }

    /**
     * @param bool $criaIds
     * @return Calendario
     */
    private function criarCalendario($criaAtividades = false, $criaSituacao = false)
    {
        $tipoProcesso = TipoProcesso::newInstance();
        $tipoProcesso->setId(Constants::TIPO_PROCESSO_ORDINARIO);
        $arquivo = $this->criarArquivoCalendario();

        $cauUf = UfCalendario::newInstance();
        $cauUf->setIdCauUf(23);

        $eleicao = Eleicao::newInstance();
        $eleicao->setAno(2013);
        $eleicao->setTipoProcesso($tipoProcesso);

        $calendario = Calendario::newInstance();
        $calendario->setEleicao($eleicao);
        $calendario->setIdSituacaoVigente(Constants::SITUACAO_CALENDARIO_EM_PREENCHIMENTO);
        $calendario->setDataInicioVigencia(new DateTime('2013-01-02'));
        $calendario->setDataFimVigencia(new DateTime('2013-03-29'));
        $calendario->setDataInicioMandato(new DateTime('2013-04-01'));
        $calendario->setDataFimMandato(new DateTime('2014-01-01'));
        $calendario->setIdadeInicio(25);
        $calendario->setIdadeFim(65);
        $calendario->setArquivos(new ArrayCollection());
        $calendario->getArquivos()->add($arquivo);
        $calendario->setSituacaoIES(false);
        $calendario->setCauUf(new ArrayCollection());
        $calendario->getCauUf()->add($cauUf);

        if ($criaSituacao) {
            $situacao = SituacaoCalendario::newInstance();
            $situacao->setId(Constants::SITUACAO_CALENDARIO_CONCLUIDO);

            $calendarioSituacao = CalendarioSituacao::newInstance();
            $calendarioSituacao->setId(self::ID_ATV_PRM);
            $calendarioSituacao->setData(new DateTime('now'));
            $calendarioSituacao->setSituacaoCalendario($situacao);

            $calendario->setSituacoes(new ArrayCollection());
            $calendario->getSituacoes()->add($calendarioSituacao);

            $situacaoEleicao = SituacaoEleicao::newInstance();
            $situacaoEleicao->setId(Constants::SITUACAO_CALENDARIO_CONCLUIDO);

            $eleicaoSituacao = EleicaoSituacao::newInstance();
            $eleicaoSituacao->setId(self::ID_ATV_SEC);
            $eleicaoSituacao->setData(new DateTime('now'));
            $eleicaoSituacao->setSituacaoEleicao($situacaoEleicao);

            $calendario->getEleicao()->setSituacoes(new ArrayCollection());
            $calendario->getEleicao()->getSituacoes()->add($situacaoEleicao);
        }

        if ($criaAtividades) {
            $calendario->setAtividadesPrincipais(new ArrayCollection());
            $calendario->getAtividadesPrincipais()->add($this->criarAtividadesPrimariasSecundarias($criaAtividades));
        }

        return $calendario;
    }

    /**
     * @return ArquivoCalendario
     */
    private function criarArquivoCalendario()
    {
        $arquivo = ArquivoCalendario::newInstance();
        $arquivo->setTamanho(23212);
        $arquivo->setNomeFisico('euiehriu3234.pdf');
        $arquivo->setNome('teste.pdf');

        return $arquivo;
    }

    /**
     * @param bool $criaIds
     * @return AtividadePrincipalCalendario
     */
    private function criarAtividadesPrimariasSecundarias($criaErro = false)
    {
        $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance();
        $atividadeSecundaria->setDescricao('sub desc 1.1');
        $atividadeSecundaria->setDataInicio(new DateTime('2013-02-01'));
        $atividadeSecundaria->setDataFim(new DateTime('2013-02-15'));
        $atividadeSecundaria->setNivel(1);

        $atividadePrincipal = AtividadePrincipalCalendario::newInstance();
        $atividadePrincipal->setDescricao('desc 1');
        $atividadePrincipal->setDataInicio(new DateTime('2013-02-01'));
        $atividadePrincipal->setDataFim(new DateTime('2013-02-25'));
        if (!$criaErro) {
            $atividadePrincipal->setDataFim(new DateTime('2013-04-25'));
        }
        $atividadePrincipal->setNivel(1);
        $atividadePrincipal->setObedeceVigencia(true);

        $atividadePrincipal->setAtividadesSecundarias(new ArrayCollection());
        $atividadePrincipal->getAtividadesSecundarias()->add($atividadeSecundaria);

        return $atividadePrincipal;
    }

    /**
     * @param $calendario
     * @return HistoricoCalendario
     */
    private function criarHistorico($calendario)
    {
        $historico = HistoricoCalendario::newInstance();
        $historico->setData(Utils::getData());
        $historico->setCalendario($calendario);
        $historico->setAcao(Constants::ACAO_CALENDARIO_INSERIR);
        $historico->setResponsavel(1);
        $historico->setDescricaoAba(null);

        return $historico;
    }

    /**
     * @param Eleicao $eleicao
     * @param $idSituacao
     * @return Eleicao
     */
    private function criarSituacaoEleicao(Eleicao $eleicao, $idSituacao)
    {
        $situacaoEleicao = SituacaoEleicao::newInstance();
        $situacaoEleicao->setId(1);

        $eleicaoSituacao = EleicaoSituacao::newInstance();
        $eleicaoSituacao->setData(Utils::getData());
        $eleicaoSituacao->setEleicao($eleicao);
        $eleicaoSituacao->setSituacaoEleicao($situacaoEleicao);
        $eleicao->setSituacoes(new ArrayCollection());
        $eleicao->getSituacoes()->add($eleicaoSituacao);

        return $eleicao;
    }

    /**
     * Cria uma lista de documento de comissão de calendário.
     *
     * @return DocumentoComissaoMembroTO|ArrayCollection
     */
    private function criarListaDocumentoComissaoCalendariosTO()
    {
        $documentosComissaoCollection = new ArrayCollection();

        for ($i = 0; $i < 10; $i++) {
            $eleicao = EleicaoTO::newInstance();
            $eleicao->setId(self::ID_ELEICAO);
            $eleicao->setAno(self::ANO_ELEICAO);
            $eleicao->setSequenciaAno(self::SEQUENCIA_ANO_ELEICAO);

            $calendario = CalendarioTO::newInstance();
            $calendario->setId(self::ID_CALENDARIO);
            $calendario->setEleicao($eleicao);

            $atividadePrincipal = AtividadePrincipalCalendarioTO::newInstance();
            $atividadePrincipal->setId(self::ID_ATIVIDADE_PRINCIPAL);
            $atividadePrincipal->setCalendario($calendario);

            $atividadeSecundaria = AtividadeSecundariaTO::newInstance();
            $atividadeSecundaria->setId(self::ID_ATIVIDADE_SECUNDARIA);
            $atividadeSecundaria->setAtividadePrincipal($atividadePrincipal);

            $informacacaoComissaoMembro = InformacaoComissaoMembroTO::newInstance();
            $informacacaoComissaoMembro->setId(self::ID_INFORMACAO_COMISSAO_MEMBRO);
            $informacacaoComissaoMembro->setAtividadeSecundaria($atividadeSecundaria);

            $documentoComissaoMembro = DocumentoComissaoMembroTO::newInstance();
            $documentoComissaoMembro->setId($i + 1);
            $documentoComissaoMembro->setDescricaoCabecalho('');
            $documentoComissaoMembro->setDescricaoTextoFinal('');
            $documentoComissaoMembro->setSituacaoTextoFinal(true);
            $documentoComissaoMembro->setDescricaoTextoInicial('');
            $documentoComissaoMembro->setDescricaoTextoRodape('');
            $documentoComissaoMembro->setSituacaoTextoRodape(true);
            $documentoComissaoMembro->setSituacaoTextoInicial(true);
            $documentoComissaoMembro->setSituacaoCabecalhoAtivo(true);
            $documentoComissaoMembro->setInformacaoComissaoMembro($informacacaoComissaoMembro);

            $documentosComissaoCollection->add($documentoComissaoMembro);
        }

        return $documentosComissaoCollection;
    }

    /**
     * Retorna uma lista com os objetos de anos das eleições dos documentos de comissão membro para publicação.
     *
     * @return ArrayCollection
     */
    private function criarListaAnosDocumentoComissaoCalendariosTO()
    {
        $eleicoesCollection = new ArrayCollection();

        for ($i = 0; $i < 10; $i++) {
            $eleicao = EleicaoTO::newInstance();
            $eleicao->setId($i + 1);
            $eleicao->setAno(2019);
            $eleicao->setSequenciaAno($i + 1);
            $eleicoesCollection->add($eleicao);
        }

        return $eleicoesCollection;
    }

    /**
     * Retorna um objeto de filtro de 'CalendarioPublicacaoComissaoEleitoralFiltroTO'.
     *
     * @return CalendarioPublicacaoComissaoEleitoralFiltroTO
     */
    private function getCalendarioPublicacaoComissaoEleitoralFiltroTO()
    {
        $calendarioPublicacaoFiltroTO = CalendarioPublicacaoComissaoEleitoralFiltroTO::newInstance();
        $calendarioPublicacaoFiltroTO->setEleicoes(new ArrayCollection());
        $calendarioPublicacaoFiltroTO->setPublicadas(new ArrayCollection());
        $calendarioPublicacaoFiltroTO->setAnosEleicao(new ArrayCollection());
        $calendarioPublicacaoFiltroTO->setTiposProcesso(new ArrayCollection());
        return $calendarioPublicacaoFiltroTO;
    }

}
