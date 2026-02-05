<?php

use App\Business\DeclaracaoAtividadeBO;
use App\Business\HistoricoChapaEleicaoBO;
use App\Business\MembroChapaBO;
use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ChapaEleicao;
use App\Entities\DeclaracaoAtividade;
use App\Entities\HistoricoChapaEleicao;
use App\Entities\MembroChapa;
use App\Exceptions\NegocioException;
use App\Factory\UsuarioFactory;
use App\Repository\DocumentoComprobatorioSinteseCurriculoRepository;
use App\Repository\MembroChapaRepository;
use App\Security\Token\TokenContext;
use App\To\ConviteStatusFiltroTO;
use App\To\StatusMembroChapaTO;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Teste de Unidade referente à classe MembroChapaBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class MembroChapaBOTest extends TestCase
{

    /** @var MembroChapaBO */
    private $membroChapaBO;

    /** @var MockObject */
    private $statusMembroChapaTO;

    /** @var MockObject */
    private $conviteStatusFiltroTO;

    /** @var MockObject */
    private $membroChapaRepository;

    /** @var MockObject */
    private $documentoComprobatorioSinteseCurriculoRepository;

    /** Construtor de ChapaEleicaoBOTest */
    public function setUp(): void
    {
        parent::setUp();

        $this->membroChapaBO = app()->make(MembroChapaBO::class);

        $this->statusMembroChapaTO = $this->getMockBuilder(StatusMembroChapaTO::class)->disableOriginalConstructor()->getMock();
        $this->conviteStatusFiltroTO = $this->getMockBuilder(ConviteStatusFiltroTO::class)->disableOriginalConstructor()->getMock();
        $this->membroChapaRepository = $this->getMockBuilder(MembroChapaRepository::class)->disableOriginalConstructor()->getMock();
        $this->documentoComprobatorioSinteseCurriculoRepository = $this->getMockBuilder(DocumentoComprobatorioSinteseCurriculoRepository::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * Realiza o teste de busca convites do usuario.
     *
     * @test
     * @throws Exception
     */
    public function getConvitesUsuarioComSucesso()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = 1359;
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $idChapaEleicao = random_int(1, 100);

        $this->membroChapaRepository
            ->method('getConvitesUsuario')
            ->with($usuarioLogado->idProfissional)
            ->willReturn([
                [
                    'numeroOrdem' => random_int(0, 2),
                    'idChapaEleicao' => $idChapaEleicao,
                    'descricaoPlataforma' => 'Descrição',
                    'idMembroChapa' => random_int(1, 500),
                    'nomeResponsavelChapa' => 'Responsável Chapa',
                    'idProfissional' => $usuarioLogado->idProfissional,
                    'idTipoCandidatura' => Constants::TIPO_CANDIDATURA_IES,
                    'tipoParticChapa' => Constants::TIPO_PARTICIPACAO_CHAPA_TITULAR,
                ]
            ]);

        $usuarioFactory = $this->getMockBuilder(UsuarioFactory::class)->disableOriginalConstructor()->getMock();
        $usuarioFactory
            ->expects($this->once())
            ->method('getUsuarioLogado')
            ->willReturn($usuarioLogado);

        $this->setPrivateProperty($this->membroChapaBO, 'usuarioFactory', $usuarioFactory);
        $this->setPrivateProperty($this->membroChapaBO, 'membroChapaRepository', $this->membroChapaRepository);

        $convitesMembroChapa = $this->membroChapaBO->getConvitesUsuario();
        $this->assertNotNull($convitesMembroChapa);
    }

    /**
     * Realiza o teste de aceitação de convites do usuario.
     *
     * @test
     * @throws Exception
     */
    public function aceitarConviteComSucesso()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = 1359;
        $usuarioLogado->id = random_int(1, 100);
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $this->setMockAceitarConvite($usuarioLogado);

        $this->membroChapaBO->aceitarConvite($this->conviteStatusFiltroTO);
        $this->assertTrue(true);
    }


    /**
     * Realiza o teste de aceitação de convites do usuario.
     *
     * @test
     * @throws Exception
     */
    public function aceitarConviteComSucessoQuandoExistirDocumentos()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = 1359;
        $usuarioLogado->id = random_int(1, 100);
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $this->setMockAceitarConvite($usuarioLogado, true);

        $this->membroChapaBO->aceitarConvite($this->conviteStatusFiltroTO);
        $this->assertTrue(true);
    }

    /**
     * Realiza o teste sem sucesso de aceitação de convites do usuario.
     *
     * @test
     * @throws Exception
     */
    public function aceitarConviteSemSucesso()
    {
        $this->expectException(\Exception::class);

        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = 1359;
        $usuarioLogado->id = random_int(1, 100);
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $this->setMockAceitarConvite($usuarioLogado);

        $this->membroChapaRepository
            ->method('persistEmLote')
            ->willThrowException(new \Exception);

        $this->setPrivateProperty($this->membroChapaBO, 'membroChapaRepository', $this->membroChapaRepository);
        $this->membroChapaBO->aceitarConvite($this->conviteStatusFiltroTO);
    }

    /**
     * Realiza o teste de aceitação de convites do usuario.
     *
     * @test
     * @throws Exception
     */
    public function rejeitarConviteComSucesso()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = 1359;
        $usuarioLogado->id = random_int(1, 100);
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $this->setMockRejeitarConvite($usuarioLogado, true);

        $this->setPrivateProperty($this->membroChapaBO, 'membroChapaRepository', $this->membroChapaRepository);

        $this->membroChapaBO->rejeitarConvite($this->conviteStatusFiltroTO);
        $this->assertTrue(true);
    }

    /**
     * Realiza o teste sem sucesso de aceitação de convites do usuario.
     *
     * @test
     * @throws Exception
     */
    public function rejeitarConviteSemSucesso()
    {
        $this->expectException(\Exception::class);

        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = 1359;
        $usuarioLogado->id = random_int(1, 100);
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $this->setMockRejeitarConvite($usuarioLogado);

        $this->membroChapaRepository
            ->method('persist')
            ->willThrowException(new \Exception);

        $this->setPrivateProperty($this->membroChapaBO, 'membroChapaRepository', $this->membroChapaRepository);
        $this->membroChapaBO->rejeitarConvite($this->conviteStatusFiltroTO);
    }

    /**
     * Realiza o teste sem sucesso de aceitação de convites do usuario quando membro chapa não existe.
     *
     * @test
     * @throws Exception
     */
    public function rejeitarConviteSemSucessoQuandoMembroChapaNaoExiste()
    {
        $this->expectException(NegocioException::class);

        $idMembroChapa = random_int(1, 100);

        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = 1359;

        $this->conviteStatusFiltroTO
            ->expects($this->once())
            ->method('getIdMembroChapa')
            ->willReturn($idMembroChapa);

        $this->membroChapaRepository
            ->method('getMembroChapaAConfirmarPorProfissional')
            ->willReturn(null);

        $usuarioFactory = $this->getMockBuilder(UsuarioFactory::class)->disableOriginalConstructor()->getMock();
        $usuarioFactory
            ->expects($this->once())
            ->method('getUsuarioLogado')
            ->willReturn($usuarioLogado);

        $this->setPrivateProperty($this->membroChapaBO, 'usuarioFactory', $usuarioFactory);
        $this->setPrivateProperty($this->membroChapaBO, 'membroChapaRepository', $this->membroChapaRepository);

        $this->membroChapaBO->rejeitarConvite($this->conviteStatusFiltroTO);
    }

    /**
     * Realiza o teste com sucesso de alteração de status de ChapaEleicao por StatusMembroChapaTO e Usuário Logado.
     *
     * @test
     * @throws Exception
     */
    public function alterarStatusComSucesso()
    {
        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = 1359;

        $this->setMockAlterarStatus($usuarioLogado);

        $this->membroChapaBO->alterarStatus($this->statusMembroChapaTO);
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

        $this->statusMembroChapaTO
            ->expects($this->once())
            ->method('getJustificativa')
            ->willReturn('Justificativa');

        $this->membroChapaRepository
            ->method('getPorId')
            ->willThrowException(new \Exception);

        $this->setPrivateProperty($this->membroChapaBO, 'membroChapaRepository', $this->membroChapaRepository);
        $this->membroChapaBO->alterarStatus($this->statusMembroChapaTO);
    }

    /**
     * @param $usuarioLogado
     * @param bool $comDocumentos
     *
     * @throws ReflectionException
     * @throws Exception
     */
    private function setMockAceitarConvite(\stdClass $usuarioLogado, bool $comDocumentos = false)
    {
        $idMembroChapa = random_int(1, 100);
        $idChapaEleicao = random_int(1, 100);
        $idAtividadeSecundaria = random_int(1, 100);

        $tipoCandidatura = Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR;
        if (true === $comDocumentos) {
            $tipoCandidatura = Constants::TIPO_CANDIDATURA_IES;

            $uploadFilePdf = $this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()->getMock();
            $uploadFilePdf
                ->expects($this->exactly(2))
                ->method('extension')
                ->willReturn('pdf');
            $uploadFilePdf
                ->expects($this->exactly(2))
                ->method('getClientOriginalName')
                ->willReturn('MOCK_TESTE.pdf');
            $uploadFilePdf
                ->expects($this->exactly(2))
                ->method('getSize')
                ->willReturn(9481060);

            $this->conviteStatusFiltroTO
                ->expects($this->once())
                ->method('getCartasIndicacaoInstituicao')
                ->willReturn([$uploadFilePdf]);
            $this->conviteStatusFiltroTO
                ->expects($this->once())
                ->method('getComprovantesVinculoDocenteIes')
                ->willReturn([$uploadFilePdf]);

            $this->documentoComprobatorioSinteseCurriculoRepository
                ->expects($this->once())
                ->method('persistEmLote');

            $this->setPrivateProperty($this->membroChapaBO, 'documentoComprobatorioSinteseCurriculoRepository',
                $this->documentoComprobatorioSinteseCurriculoRepository);
        }

        $atividadeSecundariaCalendario = $this->getMockBuilder(AtividadeSecundariaCalendario::class)->disableOriginalConstructor()->getMock();
        $atividadeSecundariaCalendario
            ->expects($this->once())
            ->method('getId')
            ->willReturn($idAtividadeSecundaria);

        $chapaEleicao = $this->getMockBuilder(ChapaEleicao::class)->disableOriginalConstructor()->getMock();
        $chapaEleicao
            ->expects($this->once())
            ->method('getId')
            ->willReturn($idChapaEleicao);
        $chapaEleicao
            ->expects($this->once())
            ->method('getTipoCandidatura')
            ->willReturn($tipoCandidatura);
        $chapaEleicao
            ->expects($this->once())
            ->method('getAtividadeSecundariaCalendario')
            ->willReturn($atividadeSecundariaCalendario);

        $membroChapa = $this->getMockBuilder(MembroChapa::class)->disableOriginalConstructor()->getMock();
        $membroChapa
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($idMembroChapa);
        $membroChapa
            ->expects($this->exactly(4))
            ->method('getChapaEleicao')
            ->willReturn($chapaEleicao);

        $declaracao = new \stdClass();
        $declaracao->tipoResposta = Constants::TIPO_RESPOSTA_DECLARACAO_UNICA;

        $declaracaoAtividade = $this->getMockBuilder(DeclaracaoAtividade::class)->disableOriginalConstructor()->getMock();
        $declaracaoAtividade
            ->expects($this->once())
            ->method('getDeclaracao')
            ->willReturn($declaracao);

        $uploadFileJpg = $this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()->getMock();
        $uploadFileJpg
            ->expects($this->once())
            ->method('path')
            ->willReturn(resource_path('./mocks_teste/MOCK_TESTE.jpg'));
        $uploadFileJpg
            ->expects($this->exactly(2))
            ->method('extension')
            ->willReturn('jpg');

        $this->conviteStatusFiltroTO
            ->expects($this->once())
            ->method('getDeclaracoes')
            ->willReturn([$declaracao->tipoResposta]);
        $this->conviteStatusFiltroTO
            ->expects($this->once())
            ->method('getIdMembroChapa')
            ->willReturn($idMembroChapa);
        $this->conviteStatusFiltroTO
            ->expects($this->exactly(2))
            ->method('getFotoSinteseCurriculo')
            ->willReturn($uploadFileJpg);

        $this->membroChapaRepository
            ->method('getMembrosChapaAConfirmarPorProfissional')
            ->with($usuarioLogado->idProfissional)
            ->willReturn([$membroChapa]);

        $declaracaoAtividadeBO = $this->getMockBuilder(DeclaracaoAtividadeBO::class)->disableOriginalConstructor()->getMock();
        $declaracaoAtividadeBO
            ->expects($this->once())
            ->method('getDeclaracaoPorAtividadeSecundariaTipo')
            ->willReturn($declaracaoAtividade);

        $historicoChapaEleicaoBO = $this->getMockBuilder(HistoricoChapaEleicaoBO::class)->disableOriginalConstructor()->getMock();
        $historicoChapaEleicaoBO
            ->expects($this->once())
            ->method('criarHistorico')
            ->willReturn(HistoricoChapaEleicao::newInstance());
        $historicoChapaEleicaoBO
            ->expects($this->once())
            ->method('salvar');

        $usuarioFactory = $this->getMockBuilder(UsuarioFactory::class)->disableOriginalConstructor()->getMock();
        $usuarioFactory
            ->expects($this->once())
            ->method('getUsuarioLogado')
            ->willReturn($usuarioLogado);

        $this->setPrivateProperty($this->membroChapaBO, 'usuarioFactory', $usuarioFactory);
        $this->setPrivateProperty($this->membroChapaBO, 'historicoChapaEleicaoBO', $historicoChapaEleicaoBO);
        $this->setPrivateProperty($this->membroChapaBO, 'declaracaoAtividadeBO', $declaracaoAtividadeBO);
        $this->setPrivateProperty($this->membroChapaBO, 'membroChapaRepository', $this->membroChapaRepository);
    }

    /**
     * @param stdClass $usuarioLogado
     * @param bool $comSucesso
     *
     * @throws ReflectionException
     * @throws Exception
     */
    private function setMockRejeitarConvite(\stdClass $usuarioLogado, bool $comSucesso = false)
    {
        $idMembroChapa = random_int(1, 100);

        $membroChapa = $this->getMockBuilder(MembroChapa::class)->disableOriginalConstructor()->getMock();
        $chapaEleicao = $this->getMockBuilder(ChapaEleicao::class)->disableOriginalConstructor()->getMock();

        if ($comSucesso) {
            $membroChapa
                ->expects($this->once())
                ->method('getChapaEleicao')
                ->willReturn($chapaEleicao);

            $historicoChapaEleicaoBO = $this->getMockBuilder(HistoricoChapaEleicaoBO::class)->disableOriginalConstructor()->getMock();
            $historicoChapaEleicaoBO
                ->expects($this->once())
                ->method('criarHistorico')
                ->willReturn(HistoricoChapaEleicao::newInstance());
            $historicoChapaEleicaoBO
                ->expects($this->once())
                ->method('salvar');

            $this->setPrivateProperty($this->membroChapaBO, 'historicoChapaEleicaoBO', $historicoChapaEleicaoBO);
        }

        $this->conviteStatusFiltroTO
            ->expects($this->once())
            ->method('getIdMembroChapa')
            ->willReturn($idMembroChapa);

        $this->membroChapaRepository
            ->method('getMembroChapaAConfirmarPorProfissional')
            ->willReturn($membroChapa);

        $usuarioFactory = $this->getMockBuilder(UsuarioFactory::class)->disableOriginalConstructor()->getMock();
        $usuarioFactory
            ->expects($this->once())
            ->method('getUsuarioLogado')
            ->willReturn($usuarioLogado);

        $this->setPrivateProperty($this->membroChapaBO, 'usuarioFactory', $usuarioFactory);
    }

    /**
     * @param stdClass $usuarioLogado
     *
     * @throws ReflectionException
     * @throws Exception
     */
    private function setMockAlterarStatus(\stdClass $usuarioLogado)
    {
        $idMembroChapa = random_int(1, 100);

        $profissional = new \stdClass();
        $profissional->cpf = 12345678900;

        $chapaEleicao = $this->getMockBuilder(ChapaEleicao::class)->disableOriginalConstructor()->getMock();

        $membroChapa = $this->getMockBuilder(MembroChapa::class)->disableOriginalConstructor()->getMock();
        $membroChapa
            ->expects($this->once())
            ->method('getChapaEleicao')
            ->willReturn($chapaEleicao);
        $membroChapa
            ->expects($this->once())
            ->method('getIdProfissional')
            ->willReturn($usuarioLogado->idProfissional);
        $membroChapa
            ->expects($this->once())
            ->method('getProfissional')
            ->willReturn($profissional);

        $this->statusMembroChapaTO
            ->expects($this->once())
            ->method('getIdMembroChapa')
            ->willReturn($idMembroChapa);
        $this->statusMembroChapaTO
            ->expects($this->exactly(2))
            ->method('getJustificativa')
            ->willReturn('Justificativa');
        $this->statusMembroChapaTO
            ->expects($this->once())
            ->method('getIdStatusParticipacaoChapa')
            ->willReturn($idMembroChapa);

        $this->membroChapaRepository
            ->method('getPorId')
            ->with($idMembroChapa)
            ->willReturn($membroChapa);

        $historicoChapaEleicaoBO = $this->getMockBuilder(HistoricoChapaEleicaoBO::class)->disableOriginalConstructor()->getMock();
        $historicoChapaEleicaoBO
            ->expects($this->once())
            ->method('criarHistorico')
            ->willReturn(HistoricoChapaEleicao::newInstance());
        $historicoChapaEleicaoBO
            ->expects($this->once())
            ->method('salvar');

        $this->setPrivateProperty($this->membroChapaBO, 'historicoChapaEleicaoBO', $historicoChapaEleicaoBO);
        $this->setPrivateProperty($this->membroChapaBO, 'membroChapaRepository', $this->membroChapaRepository);
    }
}
