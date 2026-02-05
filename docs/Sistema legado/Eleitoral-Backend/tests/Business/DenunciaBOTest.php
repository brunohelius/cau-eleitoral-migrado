<?php

use App\Business\AtividadeSecundariaCalendarioBO;
use App\Business\DenunciaBO;
use App\Business\HistoricoDenunciaBO;
use App\Business\MembroComissaoBO;
use App\Config\AppConfig;
use App\Config\Constants;
use App\Entities\AtividadePrincipalCalendario;
use App\Entities\Calendario;
use App\Entities\CalendarioSituacao;
use App\Entities\ChapaEleicao;
use App\Entities\Eleicao;
use App\Entities\EleicaoSituacao;
use App\Entities\Filial;
use App\Entities\HistoricoDenuncia;
use App\Entities\SituacaoCalendario;
use App\Entities\SituacaoEleicao;
use App\Entities\TipoProcesso;
use App\Entities\UfCalendario;
use App\Entities\ArquivoCalendario;
use App\Repository\ArquivoDenunciaRepository;
use App\Repository\AtividadeSecundariaCalendarioRepository;
use App\Repository\CalendarioRepository;
use App\Repository\ChapaEleicaoRepository;
use App\Repository\DenunciaRepository;
use App\Repository\DenunciaSituacaoRepository;
use App\Repository\PessoaRepository;
use App\Repository\SituacaoDenunciaRepository;
use App\Repository\TestemunhaDenunciaRepository;
use App\Repository\TipoDenunciaRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\AcompanhamentoDenunciaTO;
use App\To\DenunciaViewTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Http\Request;
use App\To\AtividadePrincipalCalendarioTO;
use app\To\AtividadePrincipalFiltroTO;
use Illuminate\Support\Arr;
use App\Entities\Denuncia;
use App\Entities\TestemunhaDenuncia;
use App\Entities\ArquivoDenuncia;
use App\Entities\DenunciaSituacao;
use App\Entities\SituacaoDenuncia;
use App\Entities\DenunciaChapa;
use App\Entities\DenunciaOutro;
use App\Entities\DenunciaMembroChapa;
use App\Entities\DenunciaMembroComissao;
use App\Entities\MembroChapa;
use App\Entities\MembroComissao;
use App\Entities\Profissional;
use App\Entities\Pessoa;
use App\Entities\TipoDenuncia;
use App\Entities\AtividadeSecundariaCalendario;
use App\Exceptions\NegocioException;
use App\Repository\DenunciaChapaRepository;
use App\Repository\MembroChapaRepository;
use App\Repository\DenunciaMembroChapaRepository;
use App\Repository\MembroComissaoRepository;
use App\Repository\DenunciaMembroComissaoRepository;
use App\Repository\DenunciaOutroRepository;
use App\Entities\DenunciaAdmitida;
use App\Exceptions\Message;
use App\Factory\UsuarioFactory;
use App\Repository\DenunciaAdmitidaRepository;
use PHPUnit\Framework\MockObject\MockObject;
use App\Entities\DenunciaInadmitida;
use App\Entities\ArquivoDenunciaInadmitida;
use App\Repository\DenunciaInadmitidaRepository;
use App\Repository\ArquivoDenunciaInadmitidaRepository;

/**
 * Teste de Unidade referente à classe DenunciaBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class DenunciaBOTest extends TestCase
{
    const ID_PESSOA = 104313;
    const ID_TIPO_DENUNCIA_CHAPA = 1;
    const ID_TIPO_DENUNCIA_MEMBRO_CHAPA = 2;
    const ID_TIPO_DENUNCIA_MEMBRO_COMISSAO = 3;
    const ID_TIPO_DENUNCIA_OUTROS = 4;
    const ID_ATV_SECUNDARIA = 36;
    const ID_CAU_UF = 165;
    const ID_CHAPA_ELEICAO = 1;
    const ID_MEMBRO_COMISSAO = 200;
    const ID_MEMBRO_CHAPA = 300;
    const ID_PROFISSIONAL = 299;
    const ID_DENUNCIA = 1;
    const ID_ARQUIVO = 1;

    /** @var MockObject */
    private $denunciaMock;

    /** @var DenunciaBO */
    private $denunciaBO;

    /** @var MockObject */
    private $usuarioFactory;

    /** @var MockObject */
    private $denunciaRepositoryMock;

    /**
     * Construtor de DenunciaBOTest
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->denunciaBO = app()->make(DenunciaBO::class);

        $this->denunciaMock           = $this->getMockBuilder(Denuncia::class)->disableOriginalConstructor()->getMock();
        $this->usuarioFactory         = $this->getMockBuilder(UsuarioFactory::class)->disableOriginalConstructor()->getMock();
        $this->denunciaRepositoryMock = $this->getMockBuilder(DenunciaRepository::class)->disableOriginalConstructor()->getMock();
    }


    /**
     * Testa o método salvar para o tipo de denuncia Chapa com sucesso
     *
     * @throws NegocioException
     * @throws \ReflectionException
     */
    public function testarSalvarChapaComSucesso()
    {
        $tipo = self::ID_TIPO_DENUNCIA_CHAPA;
        $denuncia = $this->getDenuncia($tipo);
        $denunciaBO = $this->criaMocksDenunciaBOSucesso($tipo);

        $chapaEleicaoRepositoryMock = $this->createMock(ChapaEleicaoRepository::class);
        $chapaEleicaoRepositoryMock->method('find')->willReturn($this->getChapaEleicao());

        $denunciaChapaRepositoryMock = $this->createMock(DenunciaChapaRepository::class);
        $denunciaChapaRepositoryMock->method('persist')->willReturn(true);

        $this->setPrivateProperty($denunciaBO, 'chapaEleicaoRepository', $chapaEleicaoRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'denunciaChapaRepository', $denunciaChapaRepositoryMock);

        $this->assertNotEmpty($denunciaBO->salvar($denuncia));
    }

    /**
     * Testa o método salvar para o tipo de denuncia Chapa sem sucesso
     *
     * @throws ReflectionException
     */
    public function testarSalvarChapaSemSucesso()
    {
        $e = new Exception();
        $tipo = self::ID_TIPO_DENUNCIA_CHAPA;
        $denuncia = $this->getDenuncia($tipo);
        $denunciaBO = $this->criaMocksDenunciaBOSucesso($tipo);

        $chapaEleicaoRepositoryMock = $this->createMock(ChapaEleicaoRepository::class);
        $chapaEleicaoRepositoryMock->method('find')->willReturn($this->getChapaEleicao());

        $denunciaChapaRepositoryMock = $this->createMock(DenunciaChapaRepository::class);
        $denunciaChapaRepositoryMock->method('persist')->willThrowException($e);

        $this->setPrivateProperty($denunciaBO, 'chapaEleicaoRepository', $chapaEleicaoRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'denunciaChapaRepository', $denunciaChapaRepositoryMock);

        try {
            $this->assertNotEmpty($denunciaBO->salvar($denuncia));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testa o método salvar para o tipo de denuncia Membro de Chapa com sucesso
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarSalvarMembroChapaComSucesso()
    {
        $tipo = self::ID_TIPO_DENUNCIA_MEMBRO_CHAPA;
        $denuncia = $this->getDenuncia($tipo);
        $denunciaBO = $this->criaMocksDenunciaBOSucesso($tipo);

        $membroChapaRepositoryMock = $this->createMock(MembroChapaRepository::class);
        $membroChapaRepositoryMock->method('find')->willReturn($this->getMembroChapa());

        $denunciaMembroChapaRepositoryMock = $this->createMock(DenunciaMembroChapaRepository::class);
        $denunciaMembroChapaRepositoryMock->method('persist')->willReturn(true);

        $this->setPrivateProperty($denunciaBO, 'membroChapaRepository', $membroChapaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'denunciaMembroChapaRepository', $denunciaMembroChapaRepositoryMock);

        $this->assertNotEmpty($denunciaBO->salvar($denuncia));
    }

    /**
     * Testa o método salvar para o tipo de denuncia Membro de Chapa sem sucesso
     *
     * @throws ReflectionException
     */
    public function testarSalvarMembroChapaSemSucesso()
    {
        $e = new Exception();
        $tipo = self::ID_TIPO_DENUNCIA_MEMBRO_CHAPA;
        $denuncia = $this->getDenuncia($tipo);
        $denunciaBO = $this->criaMocksDenunciaBOSucesso($tipo);

        $membroChapaRepositoryMock = $this->createMock(MembroChapaRepository::class);
        $membroChapaRepositoryMock->method('find')->willReturn($this->getMembroChapa());

        $denunciaMembroChapaRepositoryMock = $this->createMock(DenunciaMembroChapaRepository::class);
        $denunciaMembroChapaRepositoryMock->method('persist')->willThrowException($e);

        $this->setPrivateProperty($denunciaBO, 'membroChapaRepository', $membroChapaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'denunciaMembroChapaRepository', $denunciaMembroChapaRepositoryMock);

        try {
            $this->assertNotEmpty($denunciaBO->salvar($denuncia));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testa o método salvar para o tipo de denuncia Membro de Comissão com sucesso
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarSalvarMembroComissaoComSucesso()
    {
        $tipo = self::ID_TIPO_DENUNCIA_MEMBRO_COMISSAO;
        $denuncia = $this->getDenuncia($tipo);
        $denunciaBO = $this->criaMocksDenunciaBOSucesso($tipo);

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('find')->willReturn($this->getMembroComissao());

        $denunciaMembroComissaoRepositoryMock = $this->createMock(DenunciaMembroComissaoRepository::class);
        $denunciaMembroComissaoRepositoryMock->method('persist')->willReturn(true);

        $this->setPrivateProperty($denunciaBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'denunciaMembroComissaoRepository', $denunciaMembroComissaoRepositoryMock);

        $this->assertNotEmpty($denunciaBO->salvar($denuncia));
    }

    /**
     * Testa o método salvar para o tipo de denuncia Membro de Comissão sem sucesso
     *
     * @throws ReflectionException
     */
    public function testarSalvarMembroComissaoSemSucesso()
    {
        $e = new Exception();
        $tipo = self::ID_TIPO_DENUNCIA_MEMBRO_COMISSAO;
        $denuncia = $this->getDenuncia($tipo);
        $denunciaBO = $this->criaMocksDenunciaBOSucesso($tipo);

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('find')->willReturn($this->getMembroComissao());

        $denunciaMembroComissaoRepositoryMock = $this->createMock(DenunciaMembroComissaoRepository::class);
        $denunciaMembroComissaoRepositoryMock->method('persist')->willThrowException($e);

        $this->setPrivateProperty($denunciaBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'denunciaMembroComissaoRepository', $denunciaMembroComissaoRepositoryMock);

        try {
            $this->assertNotEmpty($denunciaBO->salvar($denuncia));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testa o método salvar para o tipo de denuncia Outro com sucesso
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarSalvarOutrosComSucesso()
    {
        $tipo = self::ID_TIPO_DENUNCIA_OUTROS;
        $denuncia = $this->getDenuncia($tipo);
        $denunciaBO = $this->criaMocksDenunciaBOSucesso($tipo);

        $denunciaOutroRepositoryMock = $this->createMock(DenunciaOutroRepository::class);
        $denunciaOutroRepositoryMock->method('persist')->willReturn(true);

        $this->setPrivateProperty($denunciaBO, 'denunciaOutroRepository', $denunciaOutroRepositoryMock);

        $this->assertNotEmpty($denunciaBO->salvar($denuncia));
    }

    /**
     * Testa o método salvar para o tipo de denuncia Outro sem sucesso
     *
     * @throws ReflectionException
     */
    public function testarSalvarOutrosSemSucesso()
    {
        $e = new Exception();
        $tipo = self::ID_TIPO_DENUNCIA_OUTROS;
        $denuncia = $this->getDenuncia($tipo);
        $denunciaBO = $this->criaMocksDenunciaBOSucesso($tipo);

        $denunciaOutroRepositoryMock = $this->createMock(DenunciaOutroRepository::class);
        $denunciaOutroRepositoryMock->method('persist')->willThrowException($e);

        $this->setPrivateProperty($denunciaBO, 'denunciaOutroRepository', $denunciaOutroRepositoryMock);

        try {
            $this->assertNotEmpty($denunciaBO->salvar($denuncia));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testa o método para recuperar a lista de denúncias agrupadas por UF
     *
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \ReflectionException
     */
    public function testarGetDenunciaLista()
    {
        $data['pessoa'] = self::ID_PESSOA;
        $denunciaRepositoryMock = $this->createMock(DenunciaRepository::class);
        $denunciaRepositoryMock->method('getAgrupadaDenunciaPorPessoaUF')->willReturn($data);

        $denunciaBO = new DenunciaBO();
        $this->setPrivateProperty($denunciaBO, 'denunciaRepository', $denunciaRepositoryMock);

        $this->assertNotEmpty($denunciaBO->getDenunciaAgrupada(self::ID_PESSOA));
    }

    /**
     * Testa o método para visualizar a denúncias com sucesso.
     *
     * @test
     * @throws \App\Exceptions\NegocioException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getDenunciaPorIdComSucesso(): void
    {
        $idDenuncia = random_int(1, 10);

        $this->setMockDenunciaViewTO($idDenuncia);

        $this->denunciaRepositoryMock
            ->expects($this->once())
            ->method('getDenunciaPorId')
            ->with($idDenuncia)
            ->willReturn($this->denunciaMock);

        $this->setPrivateProperty($this->denunciaBO, 'denunciaRepository', $this->denunciaRepositoryMock);

        $denuncia = $this->denunciaBO->getAcompanhamentoDenunciaPorIdDenuncia($idDenuncia);
        $this->assertInstanceOf(AcompanhamentoDenunciaTO::class, $denuncia);
    }

    /**
     * Testa o método para visualizar a denúncias sem sucesso.
     *
     * @test
     * @throws \App\Exceptions\NegocioException
     */
    public function getDenunciaPorIdSemSucesso(): void
    {
        $this->expectException(NegocioException::class);

        $this->denunciaBO->getAcompanhamentoDenunciaPorIdDenuncia(null);
        $this->assertTrue(true);
    }

    /**
     * Testa a execução do método 'getArquivo', com sucesso.
     */
    public function testarGetArquivoComSucesso()
    {
        $denunciaBOMock = $this->createMock(DenunciaBO::class);
        $denunciaBOMock->method('getArquivo')->willReturn($this->criarStdClassArquivoTO());

        $this->assertNotEmpty($denunciaBOMock->getArquivo(self::ID_ARQUIVO));
    }

    /**
     * Testa a execução do método 'admitir' com sucesso.
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarAdmitirComSucesso()
    {
        $denunciaAdmitida = new DenunciaAdmitida();
        $denunciaAdmitida->setDescricaoDespacho("teste teste teste");
        $denunciaAdmitida->setIdDenuncia(348);
        $denunciaAdmitida->setIdMembroComissao(206);

        $denuncia = $this->getDenuncia();

        $denunciaRepositoryMock = $this->createMock(DenunciaRepository::class);
        $denunciaRepositoryMock->method('find')->willReturn($denuncia);
        $denunciaRepositoryMock->method('persist')->willReturn($denuncia);

        $membroComissaoRepositoryMock = $this->createMock(MembroComissaoRepository::class);
        $membroComissaoRepositoryMock->method('find')->willReturn($this->getMembroComissao());

        $situacaoDenunciaRepositoryMock = $this->createMock(SituacaoDenunciaRepository::class);
        $situacaoDenunciaRepositoryMock->method('find')->willReturn($this->getSituacaoDenuncia());

        $denunciaSituacaoRepositoryMock = $this->createMock(DenunciaSituacaoRepository::class);
        $denunciaSituacaoRepositoryMock->method('persist')->willReturn(true);

        $denunciaAdmitidaRepositoryMock = $this->createMock(DenunciaAdmitidaRepository::class);
        $denunciaAdmitidaRepositoryMock->method('persist')->willReturn($denunciaAdmitida);

        $historicoDenunciaBOMock = $this->createMock(HistoricoDenunciaBO::class);
        $historicoDenunciaBOMock->method('criarHistorico')->willReturn($this->getHistoricoDenuncia(self::ID_TIPO_DENUNCIA_CHAPA));
        $historicoDenunciaBOMock->method('salvar')->willReturn(true);

        $denunciaBO = new DenunciaBO();
        $this->setPrivateProperty($denunciaBO, 'denunciaRepository', $denunciaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'membroComissaoRepository', $membroComissaoRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'situacaoDenunciaRepository', $situacaoDenunciaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'denunciaSituacaoRepository', $denunciaSituacaoRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'denunciaAdmitidaRepository', $denunciaAdmitidaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'historicoDenunciaBO', $historicoDenunciaBOMock);

        $this->assertNotEmpty($denunciaBO->admitir($denunciaAdmitida));
    }

    /**
     * Testar a execução do método 'admitir' sem sucesso, com mensagem de campo obrigatório
     */
    public function testarAdmitirSemSucessoCampoObrigatorio()
    {
        $denunciaAdmitida = new DenunciaAdmitida();
        $denunciaAdmitida->setDescricaoDespacho(null);
        $denunciaAdmitida->setIdDenuncia(348);
        $denunciaAdmitida->setIdMembroComissao(206);

        $denunciaBO = new DenunciaBO();

        try {
            $this->assertNotEmpty($denunciaBO->admitir($denunciaAdmitida));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Testar a execução do método 'admitir' sem sucesso, com exception no persist
     *
     * @throws ReflectionException
     */
    public function testarAdmitirSemSucesso()
    {
        $e = new Exception();
        $denunciaAdmitida = new DenunciaAdmitida();
        $denunciaAdmitida->setDescricaoDespacho("teste teste teste");
        $denunciaAdmitida->setIdDenuncia(348);
        $denunciaAdmitida->setIdMembroComissao(206);

        $denuncia = $this->getDenuncia();

        $denunciaRepositoryMock = $this->createMock(DenunciaRepository::class);
        $denunciaRepositoryMock->method('find')->willReturn($denuncia);
        $denunciaRepositoryMock->method('persist')->willThrowException($e);

        $denunciaBO = new DenunciaBO();
        $this->setPrivateProperty($denunciaBO, 'denunciaRepository', $denunciaRepositoryMock);

        try {
            $this->assertNotEmpty($denunciaBO->admitir($denunciaAdmitida));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testa a execução do método 'Inadmitir' com sucesso.
     *
     * @throws NegocioException
     * @throws ReflectionException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testarInadmitirComSucesso()
    {
        $denunciaInadmitida = new DenunciaInadmitida();
        $denunciaInadmitida->setDescricao('teste teste teste');
        $denunciaInadmitida->setIdDenuncia(348);
        $denunciaInadmitida->setArquivoDenunciaInadmitida(new ArrayCollection());

        $denunciaArquivo = new ArquivoDenunciaInadmitida();
        $denunciaArquivo->setNome('teste.pdf');
        $denunciaArquivo->setTamanho(321233);

        $denunciaInadmitida->getArquivoDenunciaInadmitida()->add($denunciaArquivo);

        $denuncia = $this->getDenuncia();

        $denunciaRepositoryMock = $this->createMock(DenunciaRepository::class);
        $denunciaRepositoryMock->method('find')->willReturn($denuncia);
        $denunciaRepositoryMock->method('persist')->willReturn($denuncia);

        $situacaoDenunciaRepositoryMock = $this->createMock(SituacaoDenunciaRepository::class);
        $situacaoDenunciaRepositoryMock->method('find')->willReturn($this->getSituacaoDenuncia());

        $denunciaSituacaoRepositoryMock = $this->createMock(DenunciaSituacaoRepository::class);
        $denunciaSituacaoRepositoryMock->method('persist')->willReturn(true);

        $denunciaInadmitidaRepositoryMock = $this->createMock(DenunciaInadmitidaRepository::class);
        $denunciaInadmitidaRepositoryMock->method('persist')->willReturn($denunciaInadmitida);

        $historicoDenunciaBOMock = $this->createMock(HistoricoDenunciaBO::class);
        $historicoDenunciaBOMock->method('criarHistorico')->willReturn($this->getHistoricoDenuncia(self::ID_TIPO_DENUNCIA_CHAPA));
        $historicoDenunciaBOMock->method('salvar')->willReturn(true);

        $arquivoDenunciaInadmitidaRepositoryMock = $this->createMock(ArquivoDenunciaInadmitidaRepository::class);
        $arquivoDenunciaInadmitidaRepositoryMock->method('persist')->willReturn($denunciaArquivo);

        $arquivoServiceMock = $this->createMock(ArquivoService::class);
        $arquivoServiceMock->method('getCaminhoRepositorioDenuncia')->willReturn('/');
        $arquivoServiceMock->method('salvar')->willReturn(true);

        $denunciaBO = new DenunciaBO();
        $this->setPrivateProperty($denunciaBO, 'denunciaRepository', $denunciaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'situacaoDenunciaRepository', $situacaoDenunciaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'denunciaSituacaoRepository', $denunciaSituacaoRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'denunciaInadmitidaRepository', $denunciaInadmitidaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'historicoDenunciaBO', $historicoDenunciaBOMock);
        $this->setPrivateProperty($denunciaBO, 'arquivoDenunciaInadmitidaRepository', $arquivoDenunciaInadmitidaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'arquivoService', $arquivoServiceMock);

        $this->assertNotEmpty($denunciaBO->inadmitir($denunciaInadmitida));
    }

    /**
     * Testar a execução do método 'admitir' sem sucesso, com mensagem de campo obrigatório
     */
    public function testarInadmitirSemSucessoCampoObrigatorio()
    {
        $denunciaInadmitida = new DenunciaInadmitida();
        $denunciaInadmitida->setDescricao(null);
        $denunciaInadmitida->setIdDenuncia(348);
        $denunciaInadmitida->setArquivoDenunciaInadmitida(new ArrayCollection());

        $denunciaBO = new DenunciaBO();

        try {
            $this->assertNotEmpty($denunciaBO->inadmitir($denunciaInadmitida));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Testar a execução do método 'admitir' sem sucesso, com exception no persist
     *
     * @throws ReflectionException
     */
    public function testarInadmitirSemSucesso()
    {
        $e = new Exception();
        $denunciaInadmitida = new DenunciaInadmitida();
        $denunciaInadmitida->setDescricao('teste teste teste');
        $denunciaInadmitida->setIdDenuncia(348);
        $denunciaInadmitida->setArquivoDenunciaInadmitida(new ArrayCollection());

        $denuncia = $this->getDenuncia();

        $denunciaRepositoryMock = $this->createMock(DenunciaRepository::class);
        $denunciaRepositoryMock->method('find')->willReturn($denuncia);
        $denunciaRepositoryMock->method('persist')->willThrowException($e);

        $denunciaBO = new DenunciaBO();
        $this->setPrivateProperty($denunciaBO, 'denunciaRepository', $denunciaRepositoryMock);

        try {
            $this->assertNotEmpty($denunciaBO->inadmitir($denunciaInadmitida));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testa o método que busca com sucesso as denúncias em relatoria de acordo com o
     * profissional.
     *
     * @test
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getDenunciasRelatoriaPorProfissionalComSucesso()
    {
        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = random_int(100000, 300000);

        $this->usuarioFactory
            ->expects($this->once())
            ->method('getUsuarioLogado')
            ->willReturn($usuarioLogado);

        $this->denunciaRepositoryMock
            ->expects($this->once())
            ->method('getDenunciasRelatoriaPorProfissional')
            ->with($usuarioLogado->idProfissional)
            ->willReturn([
                [
                    "ds_situacao" => 'Em relatoria',
                    "numero_denuncia" => random_int(1, 100),
                    "id_denuncia" => random_int(1, 100),
                    "id_cau_uf" => random_int(140, 165),
                    "nome_denunciado" => 'SILENIO MARTINS CAMARGO',
                    "id_tipo_denuncia" => Constants::TIPO_MEMBRO_COMISSAO,
                    "nome_denunciante" => 'GUILHERME CARPINTERO DE CARVALHO',
                    "id_situacao_denuncia" => Constants::STATUS_DENUNCIA_EM_RELATORIA,
                    "dt_denuncia" => Utils::getData()->format('Y-m-d H:i:s'),
                ],
            ]);

        $this->setPrivateProperty($this->denunciaBO, 'usuarioFactory', $this->usuarioFactory);
        $this->setPrivateProperty($this->denunciaBO, 'denunciaRepository', $this->denunciaRepositoryMock);

        $response = $this->denunciaBO->getDenunciasRelatoriaPorProfissional();
        $this->assertNotNull($response);
    }

    /**
     * Testa o método que busca sem sucesso as denúncias em relatoria de acordo com o
     * profissional.
     *
     * @test
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getDenunciasRelatoriaPorProfissionalSemSucesso()
    {
        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = random_int(100000, 300000);

        $this->usuarioFactory
            ->expects($this->once())
            ->method('getUsuarioLogado')
            ->willReturn($usuarioLogado);

        $this->denunciaRepositoryMock
            ->expects($this->once())
            ->method('getDenunciasRelatoriaPorProfissional')
            ->with($usuarioLogado->idProfissional)
            ->willReturn(null);

        $this->setPrivateProperty($this->denunciaBO, 'usuarioFactory', $this->usuarioFactory);
        $this->setPrivateProperty($this->denunciaBO, 'denunciaRepository', $this->denunciaRepositoryMock);

        $response = $this->denunciaBO->getDenunciasRelatoriaPorProfissional();
        $this->assertNull($response);
    }

    /**
     * Testa com sucesso o metodo getDenunciaComissaoAgrupada
     *
     * @test
     * @throws Exception
     */
    public function getDenunciaComissaoAgrupadaComSucesso()
    {
        $idAtividadeSecundaria = random_int(100000, 300000);
        $idsCauUf = [];
        $hasCoordenadorCEN = true;

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = random_int(100000, 300000);

        $this->usuarioFactory
            ->expects($this->once())
            ->method('getUsuarioLogado')
            ->willReturn($usuarioLogado);

        $atividadeSecundariaCalendario = $this->getMockBuilder(AtividadeSecundariaCalendario::class)->disableOriginalConstructor()->getMock();
        $atividadeSecundariaCalendario
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($idAtividadeSecundaria);

        $atividadeSecundariaCalendarioBOMock = $this->getMockBuilder(AtividadeSecundariaCalendarioBO::class)->disableOriginalConstructor()->getMock();
        $atividadeSecundariaCalendarioBOMock
            ->expects($this->once())
            ->method('getAtividadeSecundariaPorNiveis')
            ->with(
                Constants::NIVEL_ATIVIDADE_PRINCIPAL_DENUNCIA,
                Constants::NIVEL_ATIVIDADE_SECUNDARIA_DENUNCIA)
            ->willReturn($atividadeSecundariaCalendario);

        $membroComissaoBOMock = $this->getMockBuilder(MembroComissaoBO::class)->disableOriginalConstructor()->getMock();
        $membroComissaoBOMock
            ->expects($this->once())
            ->method('getMembroComissaoPorProfissionalEAtividadeSecundaria')
            ->with(
                $usuarioLogado->idProfissional,
                $idAtividadeSecundaria
            )
            ->willReturn([
                [
                    "idCauUf" => 165,
                    "idTipoParticipacao" => 3,
                    "descricao" => "Coordenador(a) Adjunto"
                ]
            ]);

        $bandeira = new stdClass();
        $bandeira->id = 165;
        $bandeira->prefixo = "SE";
        $bandeira->descricao = "CAU/SE";
        $bandeira->cnpj = '14817219000192';
        $bandeira->imagemBandeira = "imagem";

        $corporativoServiceMock = $this->createMock(CorporativoService::class);
        $corporativoServiceMock->method('getFiliaisComBandeiras')->willReturn([$bandeira]);

        $this->denunciaRepositoryMock
            ->expects($this->once())
            ->method('getAgrupamentoDenunciaUfPorAtividadeSecundaria')
            ->with(
                $idAtividadeSecundaria,
                $idsCauUf,
                $hasCoordenadorCEN
            )
            ->willReturn([
                [
                    "qtd_pedido" => 8,
                    "id_cau_uf" => 165,
                    "prefixo" => "CAU/BR",
                    "descricao" => "CONSELHO DE ARQUITETURA E URBANISMO DO BRASIL"
                ]

            ]);

        $this->setPrivateProperty($this->denunciaBO, 'corporativoService', $corporativoServiceMock);
        $this->setPrivateProperty($this->denunciaBO, 'membroComissaoBO', $membroComissaoBOMock);
        $this->setPrivateProperty($this->denunciaBO, 'atividadeSecundariaCalendarioBO', $atividadeSecundariaCalendarioBOMock);
        $this->setPrivateProperty($this->denunciaBO, 'usuarioFactory', $this->usuarioFactory);
        $this->setPrivateProperty($this->denunciaBO, 'denunciaRepository', $this->denunciaRepositoryMock);


        $response = $this->denunciaBO->getDenunciaComissaoAgrupada();
        $this->assertObjectHasAttribute("totalPedidos", $response);
        $this->assertObjectHasAttribute("agrupamentoUF", $response);
        $this->assertObjectHasAttribute("isCoordenadorCEN", $response);
        $this->assertObjectHasAttribute("isCoordenadorCE", $response);
        $this->assertObjectHasAttribute("isMembroComissaoComum", $response);
    }

    /**
     * Testa sem sucesso o metodo getDenunciaComissaoAgrupada
     *
     * @test
     * @throws Exception
     */
    public function getDenunciaComissaoAgrupadaSemSucesso1()
    {
        $e = new Exception();
        $idAtividadeSecundaria = random_int(100000, 300000);

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = random_int(100000, 300000);

        $this->usuarioFactory
            ->expects($this->once())
            ->method('getUsuarioLogado')
            ->willReturn($usuarioLogado);

        $atividadeSecundariaCalendarioBOMock = $this->getMockBuilder(AtividadeSecundariaCalendarioBO::class)->disableOriginalConstructor()->getMock();
        $atividadeSecundariaCalendarioBOMock
            ->expects($this->once())
            ->method('getAtividadeSecundariaPorNiveis')
            ->with(Constants::NIVEL_ATIVIDADE_PRINCIPAL_DENUNCIA,
                Constants::NIVEL_ATIVIDADE_SECUNDARIA_DENUNCIA)
            ->willReturn(null)->willThrowException($e);

        $this->setPrivateProperty($this->denunciaBO, 'atividadeSecundariaCalendarioBO', $atividadeSecundariaCalendarioBOMock);
        $this->setPrivateProperty($this->denunciaBO, 'usuarioFactory', $this->usuarioFactory);

        try {
            $this->assertNotEmpty($this->denunciaBO->getDenunciaComissaoAgrupada());
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testa sem sucesso o metodo getDenunciaComissaoAgrupada
     *
     * @test
     * @throws Exception
     */
    public function getDenunciaComissaoAgrupadaSemSucesso2()
    {
        $e = new Exception();
        $idAtividadeSecundaria = random_int(100000, 300000);

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = random_int(100000, 300000);

        $this->usuarioFactory
            ->expects($this->once())
            ->method('getUsuarioLogado')
            ->willReturn($usuarioLogado);

        $atividadeSecundariaCalendario = $this->getMockBuilder(AtividadeSecundariaCalendario::class)->disableOriginalConstructor()->getMock();
        $atividadeSecundariaCalendario
            ->expects($this->exactly(1))
            ->method('getId')
            ->willReturn($idAtividadeSecundaria);

        $atividadeSecundariaCalendarioBOMock = $this->getMockBuilder(AtividadeSecundariaCalendarioBO::class)->disableOriginalConstructor()->getMock();
        $atividadeSecundariaCalendarioBOMock
            ->expects($this->once())
            ->method('getAtividadeSecundariaPorNiveis')
            ->with(Constants::NIVEL_ATIVIDADE_PRINCIPAL_DENUNCIA,
                Constants::NIVEL_ATIVIDADE_SECUNDARIA_DENUNCIA)
            ->willReturn($atividadeSecundariaCalendario);

        $membroComissaoBOMock = $this->getMockBuilder(MembroComissaoBO::class)->disableOriginalConstructor()->getMock();
        $membroComissaoBOMock
            ->expects($this->once())
            ->method('getMembroComissaoPorProfissionalEAtividadeSecundaria')
            ->with(
                $usuarioLogado->idProfissional,
                $idAtividadeSecundaria
            )
            ->willReturn([])->willThrowException($e);

        $this->setPrivateProperty($this->denunciaBO, 'atividadeSecundariaCalendarioBO', $atividadeSecundariaCalendarioBOMock);
        $this->setPrivateProperty($this->denunciaBO, 'usuarioFactory', $this->usuarioFactory);
        $this->setPrivateProperty($this->denunciaBO, 'membroComissaoBO', $membroComissaoBOMock);

        try {
            $this->assertNotEmpty($this->denunciaBO->getDenunciaComissaoAgrupada());
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testa sem sucesso o metodo getDenunciaComissaoAgrupada
     *
     * @test
     * @throws Exception
     */
    public function getDenunciaComissaoAgrupadaSemSucesso3()
    {
        $e = new Exception();
        $idAtividadeSecundaria = random_int(100000, 300000);
        $idsCauUf = [];
        $hasCoordenadorCEN = true;

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = random_int(100000, 300000);

        $this->usuarioFactory
            ->expects($this->once())
            ->method('getUsuarioLogado')
            ->willReturn($usuarioLogado);

        $atividadeSecundariaCalendario = $this->getMockBuilder(AtividadeSecundariaCalendario::class)->disableOriginalConstructor()->getMock();
        $atividadeSecundariaCalendario
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($idAtividadeSecundaria);

        $atividadeSecundariaCalendarioBOMock = $this->getMockBuilder(AtividadeSecundariaCalendarioBO::class)->disableOriginalConstructor()->getMock();
        $atividadeSecundariaCalendarioBOMock
            ->expects($this->once())
            ->method('getAtividadeSecundariaPorNiveis')
            ->with(Constants::NIVEL_ATIVIDADE_PRINCIPAL_DENUNCIA,
                Constants::NIVEL_ATIVIDADE_SECUNDARIA_DENUNCIA)
            ->willReturn($atividadeSecundariaCalendario);

        $membroComissaoBOMock = $this->getMockBuilder(MembroComissaoBO::class)->disableOriginalConstructor()->getMock();
        $membroComissaoBOMock
            ->expects($this->once())
            ->method('getMembroComissaoPorProfissionalEAtividadeSecundaria')
            ->with(
                $usuarioLogado->idProfissional,
                $idAtividadeSecundaria
            )
            ->willReturn([
                [
                    "idCauUf" => 165,
                    "idTipoParticipacao" => 3,
                    "descricao" => "Coordenador(a) Adjunto"
                ]
            ]);

        $bandeira = new stdClass();
        $bandeira->id = 165;
        $bandeira->prefixo = "SE";
        $bandeira->descricao = "CAU/SE";
        $bandeira->cnpj = '14817219000192';
        $bandeira->imagemBandeira = "imagem";

        $corporativoServiceMock = $this->createMock(CorporativoService::class);
        $corporativoServiceMock->method('getFiliaisComBandeiras')->willReturn([$bandeira]);

        $this->denunciaRepository
            ->expects($this->once())
            ->method('getAgrupamentoDenunciaUfPorAtividadeSecundaria')
            ->with(
                $idAtividadeSecundaria,
                $idsCauUf,
                $hasCoordenadorCEN
            )
            ->willReturn([])->willThrowException($e);

        $this->setPrivateProperty($this->denunciaBO, 'corporativoService', $corporativoServiceMock);
        $this->setPrivateProperty($this->denunciaBO, 'membroComissaoBO', $membroComissaoBOMock);
        $this->setPrivateProperty($this->denunciaBO, 'atividadeSecundariaCalendarioBO', $atividadeSecundariaCalendarioBOMock);
        $this->setPrivateProperty($this->denunciaBO, 'usuarioFactory', $this->usuarioFactory);
        $this->setPrivateProperty($this->denunciaBO, 'denunciaRepository', $this->denunciaRepository);

        try {
            $this->assertNotEmpty($this->denunciaBO->getDenunciaComissaoAgrupada());
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Cria os Mocks de DenunciaBO e retorna uma instancia de DenunciaBO
     *
     * @param int $tipo
     * @return DenunciaBO
     * @throws ReflectionException
     */
    private function criaMocksDenunciaBOSucesso($tipo = self::ID_TIPO_DENUNCIA_CHAPA)
    {
        $denunciaRepositoryMock = $this->createMock(DenunciaRepository::class);
        $denunciaRepositoryMock->method('getPorFiltro')->willReturn([]);
        $denunciaSalva = $this->getDenuncia($tipo, self::ID_DENUNCIA);
        $denunciaRepositoryMock->method('persist')->willReturn($denunciaSalva);

        $pessoaRepositoryMock = $this->createMock(PessoaRepository::class);
        $pessoaRepositoryMock->method('find')->willReturn($this->getPessoa());

        $atividadeSecundariaRepositoryMock = $this->createMock(AtividadeSecundariaCalendarioRepository::class);
        $atividadeSecundariaRepositoryMock->method('find')->willReturn($this->getAtividadeSecundaria());

        $tipoDenunciaRepositoryMock = $this->createMock(TipoDenunciaRepository::class);
        $tipoDenunciaRepositoryMock->method('find')->willReturn($this->getTipoDenuncia());

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getPorAtividadeSecundaria')->willReturn($this->getCalendario());

        $testemunhaDenunciaRepositoryMock = $this->createMock(TestemunhaDenunciaRepository::class);
        $testemunhaDenunciaRepositoryMock->method('persist')->willReturn(true);

        $arquivoDenunciaRepositoryMock = $this->createMock(ArquivoDenunciaRepository::class);
        $arquivoDenunciaRepositoryMock->method('find')->willReturn($this->getArquivos());
        $arquivoDenunciaRepositoryMock->method('persist')->willReturn($this->getArquivo());

        $arquivoServiceMock = $this->createMock(ArquivoService::class);
        $arquivoServiceMock->method('copiarArquivoDenuncia')->willReturn(true);
        $arquivoServiceMock->method('getCaminhoRepositorioDenuncia')->willReturn('/files/teste/');
        $arquivoServiceMock->method('salvar')->willReturn(true);

        $situacaoDenunciaRepositoryMock = $this->createMock(SituacaoDenunciaRepository::class);
        $situacaoDenunciaRepositoryMock->method('find')->willReturn($this->getSituacaoDenuncia());

        $denunciaSituacaoRepositoryMock = $this->createMock(DenunciaSituacaoRepository::class);
        $denunciaSituacaoRepositoryMock->method('persist')->willReturn(true);

        $historicoDenunciaBOMock = $this->createMock(HistoricoDenunciaBO::class);
        $historicoDenunciaBOMock->method('criarHistorico')->willReturn($this->getHistoricoDenuncia($tipo));
        $historicoDenunciaBOMock->method('salvar')->willReturn(true);

        $denunciaBO = new DenunciaBO();
        $this->setPrivateProperty($denunciaBO, 'denunciaRepository', $denunciaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'pessoaRepository', $pessoaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'atividadeSecundariaRepository', $atividadeSecundariaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'tipoDenunciaRepository', $tipoDenunciaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'calendarioRepository', $calendarioRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'testemunhaDenunciaRepository', $testemunhaDenunciaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'arquivoDenunciaRepository', $arquivoDenunciaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'arquivoService', $arquivoServiceMock);
        $this->setPrivateProperty($denunciaBO, 'situacaoDenunciaRepository', $situacaoDenunciaRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'denunciaSituacaoRepository', $denunciaSituacaoRepositoryMock);
        $this->setPrivateProperty($denunciaBO, 'historicoDenunciaBO', $historicoDenunciaBOMock);

        return $denunciaBO;
    }

    /**
     * Retorna a instancia de Denuncia.
     *
     * @param int $tipo
     * @return Denuncia
     */
    private function getDenuncia($tipo = self::ID_TIPO_DENUNCIA_CHAPA, $id = null)
    {
        $atvSecundaria = new AtividadeSecundariaCalendario();
        $atvSecundaria->setId(self::ID_ATV_SECUNDARIA);

        $testemunha1 = new TestemunhaDenuncia();
        $testemunha1->getNome('Estrojocilda');
        $testemunha1->setTelefone('66999996666');

        $testemunha2 = new TestemunhaDenuncia();
        $testemunha2->setNome('Asdrubal');
        $testemunha2->setEmail('asdrubal@teste.com');

        $testemunhas = new ArrayCollection();
        $testemunhas->add($testemunha1);
        $testemunhas->add($testemunha2);

        $denuncia = new Denuncia();
        $denuncia->setIdPessoa(self::ID_PESSOA);
        $denuncia->setTipoDenuncia($this->getTipoDenuncia($tipo));
        $denuncia->setAtividadeSecundaria($atvSecundaria);
        $denuncia->setDescricaoFatos("teste outrosssssss");
        $denuncia->setTestemunhas($testemunhas);
        $denuncia->setArquivoDenuncia($this->getArquivos());

        if (!empty($id)) {
            $denuncia->setId($id);
        }

        if ($tipo == self::ID_TIPO_DENUNCIA_CHAPA) {
            $denuncia->setDenunciaChapa($this->getDenunciaChapaEleicao());
        } else if ($tipo == self::ID_TIPO_DENUNCIA_MEMBRO_CHAPA) {
            $denuncia->setDenunciaMembroChapa($this->getDenunciaMembroChapa());
        } else if ($tipo == self::ID_TIPO_DENUNCIA_MEMBRO_COMISSAO) {
            $denuncia->setDenunciaMembroComissao($this->getDenunciaMembroComissao());
        } else {
            $outro = new DenunciaOutro();
            $outro->setIdCauUf(self::ID_CAU_UF);
            $denuncia->setDenunciaOutros($outro);
        }

        return $denuncia;
    }

    /**
     * Retorna uma instancia de Pessoa
     *
     * @return Pessoa
     */
    private function getPessoa()
    {
        $profissional = new Profissional();
        $profissional->setId(self::ID_PROFISSIONAL);
        $profissional->setNome('João das Neves');
        $profissional->setCpf('03388844466');

        $pessoa = new Pessoa();
        $pessoa->setId(self::ID_PESSOA);
        $pessoa->setEmail('teste@teste.com');
        $pessoa->setProfissional($profissional);

        return $pessoa;
    }

    /**
     * retorna uma instancia de calendario
     *
     * @param bool $criaSituacao
     * @return mixed
     */
    private function getCalendario($criaSituacao = false)
    {
        $tipoProcesso = TipoProcesso::newInstance();
        $tipoProcesso->setId(Constants::TIPO_PROCESSO_ORDINARIO);
        $arquivo = $this->criarArquivoCalendario();

        $cauUf = UfCalendario::newInstance();
        $cauUf->setIdCauUf(23);

        $eleicao = Eleicao::newInstance();
        $eleicao->setAno(2020);
        $eleicao->setTipoProcesso($tipoProcesso);

        $calendario = Calendario::newInstance();
        $calendario->setEleicao($eleicao);
        $calendario->setIdSituacaoVigente(Constants::SITUACAO_CALENDARIO_EM_PREENCHIMENTO);
        $calendario->setDataInicioVigencia(new DateTime('2020-01-02'));
        $calendario->setDataFimVigencia(new DateTime('2024-06-29'));
        $calendario->setDataInicioMandato(new DateTime('2020-04-01'));
        $calendario->setDataFimMandato(new DateTime('2025-01-01'));
        $calendario->setIdadeInicio(25);
        $calendario->setIdadeFim(65);
        $calendario->setArquivos(new ArrayCollection());
        $calendario->getArquivos()->add($arquivo);
        $calendario->setSituacaoIES(false);
        $calendario->setCauUf(new ArrayCollection());
        $calendario->getCauUf()->add($cauUf);
        $calendario->setExcluido(false);
        $calendario->setAtivo(true);

        if ($criaSituacao) {
            $situacao = SituacaoCalendario::newInstance();
            $situacao->setId(Constants::SITUACAO_CALENDARIO_CONCLUIDO);

            $calendarioSituacao = CalendarioSituacao::newInstance();
            $calendarioSituacao->setId(122);
            $calendarioSituacao->setData(new DateTime('now'));
            $calendarioSituacao->setSituacaoCalendario($situacao);

            $calendario->setSituacoes(new ArrayCollection());
            $calendario->getSituacoes()->add($calendarioSituacao);

            $situacaoEleicao = SituacaoEleicao::newInstance();
            $situacaoEleicao->setId(Constants::SITUACAO_CALENDARIO_CONCLUIDO);

            $eleicaoSituacao = EleicaoSituacao::newInstance();
            $eleicaoSituacao->setId(self::ID_ATV_SECUNDARIA);
            $eleicaoSituacao->setData(new DateTime('now'));
            $eleicaoSituacao->setSituacaoEleicao($situacaoEleicao);

            $calendario->getEleicao()->setSituacoes(new ArrayCollection());
            $calendario->getEleicao()->getSituacoes()->add($situacaoEleicao);
        }

        return $calendario;
    }

    /**
     * Retorna uma instancia de atividade secundaria
     *
     * @param bool $criaIds
     * @return AtividadePrincipalCalendario
     */
    private function getAtividadeSecundaria($criaErro = false)
    {
        $atividadePrincipal = AtividadePrincipalCalendario::newInstance();
        $atividadePrincipal->setDescricao('desc 1');
        $atividadePrincipal->setDataInicio(new DateTime('2020-02-01'));
        $atividadePrincipal->setDataFim(new DateTime('2024-02-25'));
        if (!$criaErro) {
            $atividadePrincipal->setDataFim(new DateTime('2020-04-25'));
        }
        $atividadePrincipal->setNivel(4);
        $atividadePrincipal->setObedeceVigencia(true);
        $atividadePrincipal->setCalendario($this->getCalendario());

        $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance();
        $atividadeSecundaria->setDescricao('sub desc 1.1');
        $atividadeSecundaria->setDataInicio(new DateTime('2020-02-01'));
        $atividadeSecundaria->setDataFim(new DateTime('2024-02-15'));
        $atividadeSecundaria->setNivel(1);
        $atividadeSecundaria->setAtividadePrincipalCalendario($atividadePrincipal);

        return $atividadeSecundaria;
    }

    /**
     * Retorna uma instancia de Tipo de Denuncia
     *
     * @param int $tipo
     * @return TipoDenuncia
     */
    private function getTipoDenuncia($tipo = self::ID_TIPO_DENUNCIA_CHAPA)
    {
        $tipoDenuncia = new TipoDenuncia();
        $tipoDenuncia->getId($tipo);

        return $tipoDenuncia;
    }

    /**
     * Retorna uma instancia de Denuncia Chapa Eleição
     *
     * @return DenunciaChapa
     */
    private function getDenunciaChapaEleicao()
    {
        $denunciaChapa = new DenunciaChapa();
        $denunciaChapa->setChapaEleicao($this->getChapaEleicao());

        return $denunciaChapa;
    }

    /**
     * retorna uma instancia de ChapaEleicao
     *
     * @return ChapaEleicao
     */
    private function getChapaEleicao()
    {
        $chapaEleicao = new ChapaEleicao();
        $chapaEleicao->setId(self::ID_CHAPA_ELEICAO);
        $chapaEleicao->setIdCauUf(self::ID_CAU_UF);

        return $chapaEleicao;
    }

    /**
     * retorna uma instancia de Denuncia de Membro Chapa
     *
     * @return DenunciaMembroChapa
     */
    private function getDenunciaMembroChapa()
    {
        $denunciaMembroChapa = new DenunciaMembroChapa();
        $denunciaMembroChapa->setMembroChapa($this->getMembroChapa());

        return $denunciaMembroChapa;
    }

    /**
     * retorna uma instancia de Membro de chama
     *
     * @return MembroChapa
     */
    private function getMembroChapa()
    {
        $membroChapa = new MembroChapa();
        $membroChapa->setId(self::ID_MEMBRO_CHAPA);
        $membroChapa->setChapaEleicao($this->getChapaEleicao());

        return $membroChapa;
    }

    /**
     * retorna uma instancia de membro de comissão
     *
     * @return MembroComissao
     */
    private function getMembroComissao()
    {
        $membroComissao = new MembroComissao();
        $membroComissao->setId(self::ID_MEMBRO_COMISSAO);
        $membroComissao->setIdCauUf(self::ID_CAU_UF);

        return $membroComissao;
    }

    /**
     * retorna uma instancia de denuncia de membro de comissao
     *
     * @return DenunciaMembroComissao
     */
    private function getDenunciaMembroComissao()
    {
        $denunciaMembroComissao = new DenunciaMembroComissao();
        $denunciaMembroComissao->setMembroComissao($this->getMembroComissao());

        return $denunciaMembroComissao;
    }

    /**
     * Retorna um array de arquivos
     *
     * @return ArrayCollection
     */
    private function getArquivos()
    {
        $arquivo2 = new ArquivoDenuncia();
        $arquivo2->setNome('arquivo2.pdf');
        $arquivo2->setTamanho(1222244);

        $arquivos = new ArrayCollection();
        $arquivos->add($this->getArquivo());
        $arquivos->add($arquivo2);

        return $arquivos;
    }

    /**
     * Retorna uma instancia de Arquivo
     *
     * @return ArquivoDenuncia
     */
    private function getArquivo()
    {
        $arquivo1 = new ArquivoDenuncia();
        $arquivo1->setNome('arquivo1.pdf');
        $arquivo1->setTamanho(1222233);

        return $arquivo1;
    }

    /**
     * Retorna uma instancia Situacao Denuncia
     *
     * @return SituacaoDenuncia
     */
    private function getSituacaoDenuncia()
    {
        $situacaoDenuncia = new SituacaoDenuncia();
        $situacaoDenuncia->setId(Constants::STATUS_EM_ANALISE_ADMISSIBILIDADE);

        return $situacaoDenuncia;
    }

    /**
     * Retorna uma instancia de Historico da Denuncia
     *
     * @param $tipo
     * @return HistoricoDenuncia
     * @throws \Exception
     */
    private function getHistoricoDenuncia($tipo)
    {
        $historicoDenuncia = HistoricoDenuncia::newInstance();
        $historicoDenuncia->setDenuncia($this->getDenuncia($tipo));
        $historicoDenuncia->setResponsavel(self::ID_PESSOA);
        $historicoDenuncia->setDescricaoAcao('Cadastro da denúncia');
        $historicoDenuncia->setDataHistorico(Utils::getData());
        $historicoDenuncia->setOrigem(Constants::ORIGEM_CORPORATIVO);

        return $historicoDenuncia;
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
     * Testa o método para recuperar a lista de denúncias agrupadas por UF
     *
     * @throws ReflectionException
     */
    public function testGetDenunciaLista()
    {
        $data['pessoa'] = self::ID_PESSOA;
        $denunciaRepositoryMock = $this->createMock(DenunciaRepository::class);
        $denunciaRepositoryMock->method('getAgrupadaDenunciaPorPessoaUF')->willReturn($data);

        $denunciaBO = new DenunciaBO();
        $this->setPrivateProperty($denunciaBO, 'denunciaRepository', $denunciaRepositoryMock);

        $this->assertNotEmpty($denunciaBO->getDenunciaAgrupada(self::ID_PESSOA));
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
     * @param $idDenuncia
     *
     * @throws \Exception
     */
    private function setMockDenunciaViewTO($idDenuncia)
    {
        $filialMock = $this->getMockBuilder(Filial::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filialMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn(random_int(144, 165));
        $filialMock
            ->expects($this->once())
            ->method('getPrefixo')
            ->willReturn('prefixo');

        $tipoDenunciaMock = $this->getMockBuilder(TipoDenuncia::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tipoDenunciaMock
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(
                random_int(Constants::TIPO_CHAPA, Constants::TIPO_OUTROS)
            );

        $situacaoDenunciaMock = $this->getMockBuilder(SituacaoDenuncia::class)
            ->disableOriginalConstructor()
            ->getMock();
        $situacaoDenunciaMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn(
                random_int(Constants::STATUS_DENUNCIA_ADMITIDA, Constants::STATUS_DENUNCIA_EM_RELATORIA)
            );

        $profissionalMock = $this->getMockBuilder(Profissional::class)
            ->disableOriginalConstructor()
            ->getMock();
        $profissionalMock
            ->expects($this->once())
            ->method('getNome')
            ->willReturn('Nome profissional');
        $profissionalMock
            ->expects($this->once())
            ->method('getRegistroNacional')
            ->willReturn('123456ABC');

        $denunciaSituacaoMock = $this->getMockBuilder(DenunciaSituacao::class)
            ->disableOriginalConstructor()
            ->getMock();
        $denunciaSituacaoMock
            ->expects($this->once())
            ->method('getSituacaoDenuncia')
            ->willReturn($situacaoDenunciaMock);

        $chapaEleicaoMock = $this->getMockBuilder(ChapaEleicao::class)
            ->disableOriginalConstructor()
            ->getMock();
        $chapaEleicaoMock
            ->expects($this->once())
            ->method('getNumeroChapa')
            ->willReturn(random_int(1, 10));

        $denunciaChapaMock = $this->getMockBuilder(DenunciaChapa::class)
            ->disableOriginalConstructor()
            ->getMock();
        $denunciaChapaMock
            ->expects($this->once())
            ->method('getChapaEleicao')
            ->willReturn($chapaEleicaoMock);

        $pessoaMock = $this->getMockBuilder(Pessoa::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pessoaMock
            ->expects($this->once())
            ->method('getEmail')
            ->willReturn('email@test.com');
        $pessoaMock
            ->expects($this->once())
            ->method('getProfissional')
            ->willReturn($profissionalMock);

        $this->denunciaMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($idDenuncia);
        $this->denunciaMock
            ->expects($this->once())
            ->method('getFilial')
            ->willReturn($filialMock);
        $this->denunciaMock
            ->expects($this->once())
            ->method('getPessoa')
            ->willReturn($pessoaMock);
        $this->denunciaMock
            ->expects($this->exactly(2))
            ->method('getTipoDenuncia')
            ->willReturn($tipoDenunciaMock);
        $this->denunciaMock
            ->expects($this->once())
            ->method('getDenunciaChapa')
            ->willReturn($denunciaChapaMock);
        $this->denunciaMock
            ->expects($this->once())
            ->method('getDescricaoFatos')
            ->willReturn('descrição');
        $this->denunciaMock
            ->expects($this->once())
            ->method('getNumeroSequencial')
            ->willReturn(random_int(1, 5));
        $this->denunciaMock
            ->expects($this->once())
            ->method('getDenunciaSituacao')
            ->willReturn(new ArrayCollection([$denunciaSituacaoMock]));
    }
}
