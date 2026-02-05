<?php

use App\Business\ChapaEleicaoBO;
use App\Business\HistoricoChapaEleicaoBO;
use App\Config\Constants;
use App\Entities\ChapaEleicao;
use App\Entities\ChapaEleicaoStatus;
use App\Entities\HistoricoChapaEleicao;
use App\Entities\StatusChapa;
use App\Exceptions\NegocioException;
use App\Repository\ChapaEleicaoRepository;
use App\Repository\MembroChapaRepository;
use App\Repository\StatusChapaRepository;
use App\Security\Token\TokenContext;
use App\To\StatusChapaEleicaoTO;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Teste de Unidade referente à classe ChapaEleicaoBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class ChapaEleicaoBOTest extends TestCase
{

    /** @var ChapaEleicaoBO */
    private $chapaEleicaoBO;

    /** @var MockObject */
    private $statusChapaEleicaoTO;

    /** @var MockObject */
    private $statusChapaRepository;

    /** @var MockObject */
    private $membroChapaRepository;

    /** @var MockObject */
    private $chapaEleicaoRepository;

    /** Construtor de ChapaEleicaoBOTest */
    public function setUp(): void
    {
        parent::setUp();

        $this->chapaEleicaoBO = app()->make(ChapaEleicaoBO::class);

        $this->statusChapaEleicaoTO = $this->getMockBuilder(StatusChapaEleicaoTO::class)->disableOriginalConstructor()->getMock();
        $this->statusChapaRepository = $this->getMockBuilder(StatusChapaRepository::class)->disableOriginalConstructor()->getMock();
        $this->membroChapaRepository = $this->getMockBuilder(MembroChapaRepository::class)->disableOriginalConstructor()->getMock();
        $this->chapaEleicaoRepository = $this->getMockBuilder(ChapaEleicaoRepository::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * Realiza o teste de busca de ChapaEleicao por ID.
     *
     * @test
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws Exception
     */
    public function getPorIdComSucesso()
    {
        $id = random_int(1, 5);
        $chapaEleicaoMock = $this->createMock(ChapaEleicao::class);

        $this->chapaEleicaoRepository
            ->method('getPorId')
            ->with($id)
            ->willReturn($chapaEleicaoMock);

        $this->setPrivateProperty($this->chapaEleicaoBO, 'chapaEleicaoRepository', $this->chapaEleicaoRepository);

        $chapaEleicao = $this->chapaEleicaoBO->getPorId($id);
        $this->assertNotNull($chapaEleicao);
    }

    /**
     * Realiza o teste de busca de ChapaEleicao por ID quando não encontrado.
     *
     * @test
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws Exception
     */
    public function getPorIdNaoExistente()
    {
        $id = random_int(1, 5);

        $this->chapaEleicaoRepository
            ->method('getPorId')
            ->with($id)
            ->willReturn(null);

        $this->setPrivateProperty($this->chapaEleicaoBO, 'chapaEleicaoRepository', $this->chapaEleicaoRepository);

        $chapaEleicao = $this->chapaEleicaoBO->getPorId($id);
        $this->assertNull($chapaEleicao);
    }

    /**
     * Realiza o teste de alteração de status de ChapaEleicao por StatusChapaEleicaoTO e Id do Usuário.
     *
     * @test
     * @throws Exception
     */
    public function alterarStatusComSucesso()
    {
        $idUsuario = random_int(1, 5);

        $this->setMockAlterarStatus();

        $this->chapaEleicaoBO->alterarStatus($this->statusChapaEleicaoTO, $idUsuario);
        $this->assertTrue(true);
    }

    /**
     * Realiza o teste sem sucesso de alteração de status de ChapaEleicao por StatusChapaEleicaoTO e Id do Usuário.
     *
     * @test
     * @throws Exception
     */
    public function alterarStatusSemSucesso()
    {
        $this->expectException(\Exception::class);
        $idUsuario = random_int(1, 5);

        $this->chapaEleicaoRepository
            ->method('getPorId')
            ->willThrowException(new \Exception);

        $this->setPrivateProperty($this->chapaEleicaoBO, 'chapaEleicaoRepository', $this->chapaEleicaoRepository);
        $this->chapaEleicaoBO->alterarStatus($this->statusChapaEleicaoTO, $idUsuario);
    }

    /**
     * Realiza o teste de inativação com sucesso de ChapaEleicao por IdChapaEleicao.
     *
     * @test
     * @throws Exception
     */
    public function inativarComSucesso()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->id = random_int(100000, 300000);
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $dados = [
            'justificativa' => 'Justificativa',
            'idChapaEleicao' => random_int(1, 5)
        ];

        $this->setMockInativar($dados);

        $this->chapaEleicaoBO->inativar($dados['idChapaEleicao'], $dados, $usuarioLogado);
        $this->assertTrue(true);
    }

    /**
     * Realiza o teste de inativação sem sucesso de ChapaEleicao por IdChapaEleicao.
     *
     * @test
     * @throws Exception
     */
    public function inativarSemSucesso()
    {
        $this->expectException(\Exception::class);

        $idChapaEleicao = random_int(1, 5);

        $dados = [
            'justificativa' => 'Justificativa',
            'idChapaEleicao' => $idChapaEleicao
        ];

        $this->chapaEleicaoRepository
            ->method('find')
            ->willThrowException(new \Exception);

        $this->setPrivateProperty($this->chapaEleicaoBO, 'chapaEleicaoRepository', $this->chapaEleicaoRepository);
        $this->chapaEleicaoBO->inativar($idChapaEleicao, $dados, new \stdClass());
    }

    /**
     * Realiza o teste de inativação sem sucesso de ChapaEleicao por IdChapaEleicao e sem justificativa.
     *
     * @test
     * @throws Exception
     */
    public function inativarSemSucessoSemJustificativa()
    {
        $this->expectException(NegocioException::class);
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->id = random_int(100000, 300000);
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $idChapaEleicao = random_int(1, 5);
        $this->chapaEleicaoBO->inativar($idChapaEleicao, [], $usuarioLogado);
    }

    /**
     * Realiza o teste de busca de histórico de ChapaEleicao por Id de calendário e Usuário logado.
     *
     * @test
     * @throws Exception
     */
    public function getHistoricoComSucesso()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $idCalendario = random_int(1, 5);

        $historicoChapaEleicaoBO = $this->getMockBuilder(HistoricoChapaEleicaoBO::class)->disableOriginalConstructor()->getMock();
        $historicoChapaEleicaoBO
            ->expects($this->once())
            ->method('getHistoricoPorCalendario')
            ->with($idCalendario)
            ->willReturn([HistoricoChapaEleicao::newInstance()]);

        $this->setPrivateProperty($this->chapaEleicaoBO, 'historicoChapaEleicaoBO', $historicoChapaEleicaoBO);

        $this->chapaEleicaoBO->getHistorico($idCalendario, $usuarioLogado);
        $this->assertTrue(true);
    }

    /**
     * Realiza o teste de busca de ChapaEleicao por Id de calendário.
     *
     * @test
     * @throws ReflectionException
     * @throws Exception
     */
    public function getChapasPorCalendarioComSucesso()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $idCalendario = 23;

        $this->chapaEleicaoRepository
            ->expects($this->once())
            ->method('getChapasCalendario')
            ->willReturn([
                [
                    'idCauUf' => Constants::COMISSAO_MEMBRO_CAU_BR_ID,
                    'idTipoCandidatura' => Constants::TIPO_CANDIDATURA_IES,
                    'quantidadeTotalChapas' => random_int(0, 20),
                    'quantidadeChapasPendentes' => random_int(0, 20),
                    'quantidadeChapasConcluidas' => random_int(0, 20)
                ],
                [
                    'idCauUf' => random_int(140, 165),
                    'idTipoCandidatura' => Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR,
                    'quantidadeTotalChapas' => random_int(0, 20),
                    'quantidadeChapasPendentes' => random_int(0, 20),
                    'quantidadeChapasConcluidas' => random_int(0, 20)
                ]
            ]);

        $this->setPrivateProperty($this->chapaEleicaoBO, 'chapaEleicaoRepository', $this->chapaEleicaoRepository);
        $response = $this->chapaEleicaoBO->getChapasPorCalendario($idCalendario, $usuarioLogado);

        $this->assertNotNull($response);
    }

    /**
     * Realiza o teste de busca de ChapaEleicao por Id de calendário e IdCauUf.
     *
     * @test
     * @throws ReflectionException
     * @throws Exception
     */
    public function getChapasPorCalendarioCauUfComSucesso()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $idCalendario = 23;
        $idCauUf = random_int(140, 165);
        $idChapaEleicao = random_int(1, 100);

        $this->membroChapaRepository
            ->expects($this->once())
            ->method('getMembrosResponsaveisChapasCalendarioCauUf')
            ->willReturn([
                [
                    'idCauUf' => $idCauUf,
                    'idChapaEleicao' => $idChapaEleicao,
                    'idProfissional' => random_int(1000, 5000),
                ]
            ]);
        $this->membroChapaRepository
            ->expects($this->once())
            ->method('getQuantidadeMembrosChapasCalendarioCauUf')
            ->willReturn([
                [
                    'idCauUf' => $idCauUf,
                    'idChapaEleicao' => $idChapaEleicao,
                    'quantidadeTotalMembrosChapa' => random_int(0, 20),
                    'quantidadeMembrosConfirmados' => random_int(0, 20),
                    'idStatusChapa' => Constants::SITUACAO_CHAPA_PENDENTE,
                ]
            ]);

        $this->setPrivateProperty($this->chapaEleicaoBO, 'membroChapaRepository', $this->membroChapaRepository);
        $this->setPrivateProperty($this->chapaEleicaoBO, 'chapaEleicaoRepository', $this->chapaEleicaoRepository);
        $response = $this->chapaEleicaoBO->getChapasQuantidadeMembrosPorCalendarioCauUf($idCalendario, $idCauUf, $usuarioLogado);

        $this->assertNotNull($response);
    }

    /**
     * Realiza o teste de validação com sucesso de Uf do Profissional Convidado.
     *
     * @test
     * @throws ReflectionException
     * @throws Exception
     */
    public function validarUfProfissionalConvidadoChapaComSucesso()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = 1359;
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $idChapaEleicao = random_int(1, 100);

        $chapaEleicao = $this->getMockBuilder(ChapaEleicao::class)->disableOriginalConstructor()->getMock();
        $chapaEleicao
            ->method('getIdCauUf')
            ->willReturn(149);

        $this->chapaEleicaoRepository
            ->method('getPorId')
            ->with($idChapaEleicao)
            ->willReturn($chapaEleicao);

        $this->setPrivateProperty($this->chapaEleicaoBO, 'chapaEleicaoRepository', $this->chapaEleicaoRepository);
        $this->chapaEleicaoBO->validarUfProfissionalConvidadoChapa($idChapaEleicao, $usuarioLogado);

        $this->assertTrue(true);
    }

    /**
     * Realiza o teste de validação sem sucesso de Uf do Profissional Convidado.
     *
     * @test
     * @throws ReflectionException
     * @throws Exception
     */
    public function validarUfProfissionalConvidadoChapaSemSucesso()
    {
        $this->expectException(NegocioException::class);

        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = 1359;
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $idChapaEleicao = random_int(1, 100);

        $chapaEleicao = $this->getMockBuilder(ChapaEleicao::class)->disableOriginalConstructor()->getMock();
        $chapaEleicao
            ->method('getIdCauUf')
            ->willReturn(150);

        $this->chapaEleicaoRepository
            ->method('getPorId')
            ->with($idChapaEleicao)
            ->willReturn($chapaEleicao);

        $this->setPrivateProperty($this->chapaEleicaoBO, 'chapaEleicaoRepository', $this->chapaEleicaoRepository);
        $this->chapaEleicaoBO->validarUfProfissionalConvidadoChapa($idChapaEleicao, $usuarioLogado);
    }

    /**
     * Realiza o teste de busca de ChapaEleicao por Id de calendário e IdCauUf sendo zero.
     *
     * @test
     * @throws ReflectionException
     * @throws Exception
     */
    public function getChapasPorCalendarioCauUfComSucessoQuandoIdCauUfForZero()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $idCauUf = 0;
        $idCalendario = 23;
        $idChapaEleicao = random_int(1, 100);

        $this->membroChapaRepository
            ->expects($this->once())
            ->method('getMembrosResponsaveisChapasCalendarioCauUf')
            ->willReturn([
                [
                    'idCauUf' => $idCauUf,
                    'idChapaEleicao' => $idChapaEleicao,
                    'idProfissional' => random_int(1000, 5000),
                ]
            ]);
        $this->membroChapaRepository
            ->expects($this->once())
            ->method('getQuantidadeMembrosChapasCalendarioCauUf')
            ->willReturn([
                [
                    'idCauUf' => random_int(140, 165),
                    'idChapaEleicao' => $idChapaEleicao,
                    'quantidadeTotalMembrosChapa' => random_int(0, 20),
                    'quantidadeMembrosConfirmados' => random_int(0, 20),
                    'idStatusChapa' => Constants::SITUACAO_CHAPA_PENDENTE,
                ]
            ]);

        $this->setPrivateProperty($this->chapaEleicaoBO, 'membroChapaRepository', $this->membroChapaRepository);
        $this->setPrivateProperty($this->chapaEleicaoBO, 'chapaEleicaoRepository', $this->chapaEleicaoRepository);
        $response = $this->chapaEleicaoBO->getChapasQuantidadeMembrosPorCalendarioCauUf($idCalendario, $idCauUf, $usuarioLogado);

        $this->assertNotNull($response);
    }

    /**
     * @throws Exception
     */
    private function setMockAlterarStatus()
    {
        $idChapaEleicao = random_int(1, 5);

        $this->statusChapaEleicaoTO
            ->method('getJustificativa')
            ->willReturn('justificativa');
        $this->statusChapaEleicaoTO
            ->expects($this->once())
            ->method('getIdChapaEleicao')
            ->willReturn($idChapaEleicao);
        $this->statusChapaEleicaoTO
            ->method('getIdStatusChapa')
            ->willReturn(Constants::SITUACAO_CHAPA_CONCLUIDA);

        $statusChapaAnterior = $this->getMockBuilder(StatusChapa::class)->disableOriginalConstructor()->getMock();
        $statusChapaAnterior
            ->expects($this->once())
            ->method('getDescricao')
            ->willReturn('Descrição');

        $chapaEleicaoStatus = $this->getMockBuilder(ChapaEleicaoStatus::class)->disableOriginalConstructor()->getMock();
        $chapaEleicaoStatus
            ->expects($this->once())
            ->method('getStatusChapa')
            ->willReturn($statusChapaAnterior);

        $chapaEleicao = $this->getMockBuilder(ChapaEleicao::class)->disableOriginalConstructor()->getMock();
        $chapaEleicao
            ->expects($this->once())
            ->method('getUltimoChapaEleicaoStatus')
            ->willReturn($chapaEleicaoStatus);

        $this->chapaEleicaoRepository
            ->method('getPorId')
            ->with($idChapaEleicao)
            ->willReturn($chapaEleicao);

        $statusChapaAtualizado = $this->getMockBuilder(StatusChapa::class)->disableOriginalConstructor()->getMock();
        $statusChapaAtualizado
            ->expects($this->once())
            ->method('getDescricao')
            ->willReturn('Descrição 2');

        $this->statusChapaRepository
            ->method('persist')
            ->willReturnSelf();
        $this->statusChapaRepository
            ->method('find')
            ->with(Constants::SITUACAO_CHAPA_CONCLUIDA)
            ->willReturn($statusChapaAtualizado);

        $historicoChapaEleicaoBO = $this->getMockBuilder(HistoricoChapaEleicaoBO::class)->disableOriginalConstructor()->getMock();
        $historicoChapaEleicaoBO
            ->expects($this->once())
            ->method('criarHistorico')
            ->willReturn(HistoricoChapaEleicao::newInstance());
        $historicoChapaEleicaoBO
            ->expects($this->once())
            ->method('salvar');

        $this->setPrivateProperty($this->chapaEleicaoBO, 'historicoChapaEleicaoBO', $historicoChapaEleicaoBO);
        $this->setPrivateProperty($this->chapaEleicaoBO, 'statusChapaRepository', $this->statusChapaRepository);
        $this->setPrivateProperty($this->chapaEleicaoBO, 'chapaEleicaoRepository', $this->chapaEleicaoRepository);
    }

    /**
     * @param array $dados
     *
     * @throws ReflectionException
     * @throws Exception
     */
    private function setMockInativar(array $dados)
    {
        $chapaEleicao = $this->getMockBuilder(ChapaEleicao::class)->disableOriginalConstructor()->getMock();
        $chapaEleicao
            ->expects($this->once())
            ->method('getIdEtapa')
            ->willReturn(Constants::ETAPA_MEMBROS_CHAPA_INCLUIDA);
        $chapaEleicao
            ->expects($this->once())
            ->method('getIdProfissionalInclusao')
            ->willReturn(random_int(1000, 5000));

        $this->chapaEleicaoRepository
            ->method('find')
            ->with($dados['idChapaEleicao'])
            ->willReturn($chapaEleicao);
        $this->chapaEleicaoRepository
            ->method('persist')
            ->with($chapaEleicao)
            ->willReturnSelf();

        $historicoChapaEleicaoBO = $this->getMockBuilder(HistoricoChapaEleicaoBO::class)->disableOriginalConstructor()->getMock();
        $historicoChapaEleicaoBO
            ->expects($this->once())
            ->method('criarHistorico')
            ->willReturn(HistoricoChapaEleicao::newInstance());
        $historicoChapaEleicaoBO
            ->expects($this->once())
            ->method('salvar');

        $this->setPrivateProperty($this->chapaEleicaoBO, 'historicoChapaEleicaoBO', $historicoChapaEleicaoBO);
        $this->setPrivateProperty($this->chapaEleicaoBO, 'chapaEleicaoRepository', $this->chapaEleicaoRepository);
    }
}
