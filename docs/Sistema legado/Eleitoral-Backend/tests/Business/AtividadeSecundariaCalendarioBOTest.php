<?php

use App\Business\AtividadeSecundariaCalendarioBO;
use App\Business\DenunciaBO;
use App\Config\Constants;
use App\Entities\AtividadePrincipalCalendario;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\CorpoEmail;
use App\Entities\EmailAtividadeSecundaria;
use App\Entities\EmailAtividadeSecundariaTipo;
use App\Entities\Historico;
use App\Entities\MembroComissao;
use App\Entities\TipoEmailAtividadeSecundaria;
use App\Exceptions\NegocioException;
use App\Factory\UsuarioFactory;
use App\Repository\AtividadeSecundariaCalendarioRepository;
use App\Repository\DenunciaRepository;
use App\Repository\EmailAtividadeSecundariaRepository;
use App\Repository\EmailAtividadeSecundariaTipoRepository;
use App\To\DefinicaoDeclaracoesEmailsAtivSecundariaTO;
use App\To\EmailAtividadeSecundariaTO;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Teste de Unidade referente á classe AtividadeSecundariaCalendarioBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class AtividadeSecundariaCalendarioBOTest extends TestCase
{
    const ID_ATIVIDADE_PRINCIPAL = 15;
    const ID_ATIVIDADE_SECUNDARIA = 15;

    /** @var MockObject */
    private $historicoBOMock;

    /** @var MockObject */
    private $usuarioFactory;

    /** @var MockObject */
    private $emailAtividadeSecundaria;

    /** @var MockObject */
    private $atividadeSecundariaCalendario;

    /** @var MockObject */
    private $emailAtividadeSecundariaTipoBOMock;

    /** @var AtividadeSecundariaCalendarioBO */
    private $atividadeSecundariaCalendarioBO;

    /** @var MockObject */
    private $emailAtividadeSecundariaRepositoryMock;

    /** @var MockObject */
    private $atividadeSecundariaCalendarioRepositoryMock;

    /**
     * Construtor de DenunciaBOTest
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->atividadeSecundariaCalendarioBO = app()->make(AtividadeSecundariaCalendarioBO::class);

        $this->usuarioFactory = $this->getMockBuilder(UsuarioFactory::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();
        $this->atividadeSecundariaCalendario
                              = $this->getMockBuilder(AtividadeSecundariaCalendario::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();
        $this->emailAtividadeSecundaria
                              = $this->getMockBuilder(EmailAtividadeSecundaria::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();
        $this->emailAtividadeSecundariaRepositoryMock
                              = $this->getMockBuilder(EmailAtividadeSecundariaRepository::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();
        $this->atividadeSecundariaCalendarioRepositoryMock
                              = $this->getMockBuilder(AtividadeSecundariaCalendarioRepository::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();
    }

    /**
     * Realiza o teste que válida se o resultado de acordo com o 'id' é recuperado de maneira correta.
     *
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testarGerPorIdComSucesso()
    {
        $atividadePrincipal = AtividadePrincipalCalendario::newInstance();
        $atividadePrincipal->setId(self::ID_ATIVIDADE_PRINCIPAL);
        $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance();
        $atividadeSecundaria->setId(self::ID_ATIVIDADE_SECUNDARIA);
        $atividadeSecundaria->setAtividadePrincipalCalendario($atividadePrincipal);

        $atividadeSecundariaRepositoryMock = $this->createMock(
            AtividadeSecundariaCalendarioRepository::class
        );

        $atividadeSecundariaRepositoryMock->method('getPorId')->willReturn($atividadeSecundaria);

        $atividadeSecundariaBO = new AtividadeSecundariaCalendarioBO();
        $this->setPrivateProperty(
            $atividadeSecundariaBO,
            'atividadeSecundariaCalendarioRepository',
            $atividadeSecundariaRepositoryMock
        );

        $this->assertNotEmpty($atividadeSecundariaBO->getPorId(self::ID_ATIVIDADE_SECUNDARIA));
    }

    /**
     * Testa o cenário de não existir a atividade secundária cadastrada na base de dados.
     *
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testeGetPorIdComIdNaoEncontrado()
    {
        $atividadeSecundariaRepositoryMock = $this->createMock(
            AtividadeSecundariaCalendarioRepository::class
        );

        $atividadeSecundariaRepositoryMock->method('getPorId')->willReturn(null);

        $atividadeSecundariaBO = new AtividadeSecundariaCalendarioBO();
        $this->setPrivateProperty(
            $atividadeSecundariaBO,
            'atividadeSecundariaCalendarioRepository',
            $atividadeSecundariaRepositoryMock
        );

        $this->assertNull($atividadeSecundariaBO->getPorId(self::ID_ATIVIDADE_SECUNDARIA));
    }


    /**
     * Testa o método que define as declaraçoes e/ou emails com sucesso.
     *
     * @test
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function definirDeclaracoesEmailsPorAtividadeSecundariaComSucesso()
    {
        $params = [
            'idAtividadeSecundaria'          => random_int(0, 20),
            'idEmailAtividadeSecundaria'     => random_int(1, 20),
            'idTipoEmailAtividadeSecundaria' => random_int(1, 20),
            'nivelAtividadePrincipal'        => Constants::NIVEL_ATIVIDADE_PRINCIPAL_MEMBRO_COMISSAO,
            'nivelAtividadeSecundaria'       => Constants::NIVEL_ATIVIDADE_SECUNDARIA_MEMBRO_COMISSAO,
        ];

        $atividadePrincipalCalendario = $this
            ->getMockBuilder(AtividadePrincipalCalendario::class)
            ->disableOriginalConstructor()
            ->getMock();
        $atividadePrincipalCalendario
            ->expects($this->exactly(5))
            ->method('getNivel')
            ->willReturn($params['nivelAtividadePrincipal']);

        $this->atividadeSecundariaCalendario
            ->expects($this->exactly(4))
            ->method('getId')
            ->willReturn($params['idAtividadeSecundaria']);
        $this->atividadeSecundariaCalendario
            ->expects($this->exactly(5))
            ->method('getNivel')
            ->willReturn($params['nivelAtividadeSecundaria']);
        $this->atividadeSecundariaCalendario
            ->expects($this->exactly(5))
            ->method('getAtividadePrincipalCalendario')
            ->willReturn($atividadePrincipalCalendario);

        $this->atividadeSecundariaCalendarioRepositoryMock
            ->expects($this->once())
            ->method('getPorId')
            ->with($params['idAtividadeSecundaria'])
            ->willReturn($this->atividadeSecundariaCalendario);

        $emailAtividadeSecundariaTO = $this
            ->getMockBuilder(EmailAtividadeSecundariaTO::class)
            ->disableOriginalConstructor()
            ->getMock();
        $emailAtividadeSecundariaTO
            ->expects($this->exactly(4))
            ->method('getIdEmailAtividadeSecundaria')
            ->willReturn($params['idEmailAtividadeSecundaria']);
        $emailAtividadeSecundariaTO
            ->expects($this->exactly(5))
            ->method('getIdTipoEmailAtividadeSecundaria')
            ->willReturn($params['idTipoEmailAtividadeSecundaria']);

        $this->emailAtividadeSecundaria
            ->expects($this->once())
            ->method('getAtividadeSecundaria')
            ->willReturn($this->atividadeSecundariaCalendario);

        $this->emailAtividadeSecundariaRepositoryMock
            ->expects($this->once())
            ->method('getEmailAtividadeSecundariaPorId')
            ->with($params['idEmailAtividadeSecundaria'])
            ->willReturn($this->emailAtividadeSecundaria);

        $definicaoDeclaracoesEmailsAtivSecundariaTOMock = $this
            ->getMockBuilder(DefinicaoDeclaracoesEmailsAtivSecundariaTO::class)
            ->disableOriginalConstructor()
            ->getMock();
        $definicaoDeclaracoesEmailsAtivSecundariaTOMock
            ->expects($this->exactly(2))
            ->method('getIdAtividadeSecundaria')
            ->willReturn($params['idAtividadeSecundaria']);
        $definicaoDeclaracoesEmailsAtivSecundariaTOMock
            ->expects($this->exactly(4))
            ->method('getEmails')
            ->willReturn([$emailAtividadeSecundariaTO]);

        $emailAtividadeSecundariaTipoRepositoryMock = $this->createMock(EmailAtividadeSecundariaTipoRepository::class);
        $emailAtividadeSecundariaTipoRepositoryMock->method('persist')->willReturn(true);

        $usuarioLogado = new \stdClass();
        $usuarioLogado->administrador = false;
        $usuarioLogado->idProfissional = random_int(100000, 300000);

        $usuarioFactory = app()->make(UsuarioFactory::class);
        $this->setPrivateProperty($usuarioFactory, 'usuarioLogado', $usuarioLogado);

        $this->setPrivateProperty($this->atividadeSecundariaCalendarioBO,
            'historicoBO', $this->historicoBOMock);
        $this->setPrivateProperty($this->atividadeSecundariaCalendarioBO,
            'emailAtividadeSecundariaTipoBO', $this->emailAtividadeSecundariaTipoBOMock);
        $this->setPrivateProperty($this->atividadeSecundariaCalendarioBO,
            'emailAtividadeSecundariaRepository', $this->emailAtividadeSecundariaRepositoryMock);
        $this->setPrivateProperty($this->atividadeSecundariaCalendarioBO,
            'emailAtividadeSecundariaTipoRepository', $emailAtividadeSecundariaTipoRepositoryMock);
        $this->setPrivateProperty($this->atividadeSecundariaCalendarioBO,
            'atividadeSecundariaCalendarioRepository', $this->atividadeSecundariaCalendarioRepositoryMock);

        $response = $this->atividadeSecundariaCalendarioBO->definirDeclaracoesEmailsPorAtividadeSecundaria(
            $definicaoDeclaracoesEmailsAtivSecundariaTOMock
        );
        $this->assertNotNull($response);
    }

    /**
     * Testa o método que busca sem sucesso as denúncias em relatoria de acordo
     * com o profissional.
     *
     * @test
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function getDenunciasRelatoriaPorProfissionalSemSucesso()
    {
        $this->expectException(NegocioException::class);

        $definicaoDeclaracoesEmailsAtivSecundariaTO = $this
            ->getMockBuilder(DefinicaoDeclaracoesEmailsAtivSecundariaTO::class)
            ->disableOriginalConstructor()
            ->getMock();
        $definicaoDeclaracoesEmailsAtivSecundariaTO
            ->expects($this->once())
            ->method('getIdAtividadeSecundaria')
            ->willReturn(null);

        $this->atividadeSecundariaCalendarioBO->definirDeclaracoesEmailsPorAtividadeSecundaria(
            $definicaoDeclaracoesEmailsAtivSecundariaTO
        );

        $params = [
            'idAtividadeSecundaria'    => random_int(0, 20),
            'nivelAtividadePrincipal'  => Constants::NIVEL_ATIVIDADE_PRINCIPAL_DENUNCIA,
            'nivelAtividadeSecundaria' => Constants::NIVEL_ATIVIDADE_SECUNDARIA_DENUNCIA,
        ];

        $atividadePrincipalCalendario = $this
            ->getMockBuilder(AtividadePrincipalCalendario::class)
            ->disableOriginalConstructor()
            ->getMock();
        $atividadePrincipalCalendario
            ->expects($this->exactly(4))
            ->method('getNivel')
            ->willReturn($params['nivelAtividadePrincipal']);

        $this->atividadeSecundariaCalendario
            ->expects($this->exactly(4))
            ->method('getNivel')
            ->willReturn($params['nivelAtividadeSecundaria']);
        $this->atividadeSecundariaCalendario
            ->expects($this->exactly(4))
            ->method('getAtividadePrincipalCalendario')
            ->willReturn($atividadePrincipalCalendario);

        $this->atividadeSecundariaCalendarioRepositoryMock
            ->expects($this->once())
            ->method('getPorId')
            ->with($params['idAtividadeSecundaria'])
            ->willReturn(null);

        $definicaoDeclaracoesEmailsAtivSecundariaTO = $this
            ->getMockBuilder(DefinicaoDeclaracoesEmailsAtivSecundariaTO::class)
            ->disableOriginalConstructor()
            ->getMock();
        $definicaoDeclaracoesEmailsAtivSecundariaTO
            ->expects($this->exactly(2))
            ->method('getIdAtividadeSecundaria')
            ->willReturn($params['idAtividadeSecundaria']);

        $this->atividadeSecundariaCalendarioBO->definirDeclaracoesEmailsPorAtividadeSecundaria(
            $definicaoDeclaracoesEmailsAtivSecundariaTO
        );
    }

    /**
     * Cria uma instância de email atividade secundária.
     *
     * @param bool $todosOsCampos
     * @return EmailAtividadeSecundaria
     * @throws Exception
     */
    private function criarEmailAtividadeSecundaria($todosOsCampos = true)
    {
        $emailAtividadeSecundaria = EmailAtividadeSecundaria::newInstance();

        if ($todosOsCampos) {
            $emailAtividadeSecundaria->setId(1);
            $tipoEmail = new TipoEmailAtividadeSecundaria();
            $tipoEmail->setId(1);
            $emailAtividadeSecundaria->setTipoEmail($tipoEmail);
            $atividadeSecundaria = new AtividadeSecundariaCalendario();
            $atividadeSecundaria->setId(1);
            $emailAtividadeSecundaria->setAtividadeSecundaria($atividadeSecundaria);
            $corpoEmail = new CorpoEmail();
            $corpoEmail->setId(1);
            $emailAtividadeSecundaria->setCorpoEmail($corpoEmail);
        }

        return $emailAtividadeSecundaria;
    }

    /**
     * Retorna uma lista de membros de comissão.
     *
     * @return array
     */
    private function criarListaMembros()
    {
        $membros = [];
        $membro1 = new MembroComissao();
        $membro1->setId(1);
        $membros[] = $membro1;
        $membro2 = new MembroComissao();
        $membro2->setId(2);
        $membros[] = $membro2;

        return $membros;
    }
}
