<?php

use App\Util\Utils;
use App\Config\Constants;
use App\Exceptions\Message;
use App\Entities\CorpoEmail;
use App\Exceptions\NegocioException;
use App\Entities\DocumentoComissaoMembro;
use App\Entities\InformacaoComissaoMembro;
use App\Business\DocumentoComissaoMembroBO;
use App\Business\InformacaoComissaoMembroBO;
use App\Entities\AtividadeSecundariaCalendario;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entities\HistoricoInformacaoComissaoMembro;
use App\Business\HistoricoInformacaoComissaoMembroBO;
use App\Repository\DocumentoComissaoMembroRepository;
use App\Repository\InformacaoComissaoMembroRepository;
use App\Entities\HistoricoAlteracaoInformacaoComissaoMembro;
use App\Business\HistoricoAlteracaoInformacaoComissaoMembroBO;

/**
 * Teste de Unidade referente á classe InformacaoComissaoMembroBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class InformacaoComissaoMembroBOTest extends TestCase
{

    const ID_EMAIL = 1;
    const ID_ATIVIDADE_SECUNDARIA = 1;
    const ID_DOCUMENTO_COMISSAO_MEMBRO = 1;
    const ID_INFORMACAO_COMISSAO_MEMBRO = 1;
    const ID_HISTORICO_INFORMACAO_COMISSAO = 1;
    const ID_ALTERACAO_HISTORICO_INFORMACAO_COMISSAO = 1;

    /**
     * Teste responsável por válidar se os dados estão sendo cadastrados com sucesso.
     *
     * @throws ReflectionException
     * @throws \App\Exceptions\NegocioException
     */
    public function testSalvarComSucesso()
    {
        $informacaoComissaoMembro = $this->criarInformacaoComissaoMembro(false);
        $informacaoComissaoMembroSalvo = $this->criarInformacaoComissaoMembro(false);
        $informacaoComissaoMembroSalvo->setId(self::ID_INFORMACAO_COMISSAO_MEMBRO);

        $historicosInformacaoComissao = $this->criarHistoricosInformacaoComissao();
        $historicosAlteracaoInformacaoComissao = $this->getHistoricosAlteracaoInformacaoComissao();

        $informacaoComissaoRepositoryMock = $this->createMock(InformacaoComissaoMembroRepository::class);

        $historicoInformacaoComissaoBOMock = $this->createMock(
            HistoricoInformacaoComissaoMembroBO::class
        );

        $historicoAlteracaoInformacaoComissaoBOMock = $this->createMock(
            HistoricoAlteracaoInformacaoComissaoMembroBO::class
        );

        $informacaoComissaoRepositoryMock->method('persist')->willReturn($informacaoComissaoMembroSalvo);
        $historicoInformacaoComissaoBOMock->method('salvar')->willReturn($historicosInformacaoComissao);
        $historicoAlteracaoInformacaoComissaoBOMock->method('salvar')->willReturn(
            $historicosAlteracaoInformacaoComissao
        );

        $informacaoComissaoMembroBO = new InformacaoComissaoMembroBO();

        $this->setPrivateProperty(
            $informacaoComissaoMembroBO,
            'informacaoComissaoMembroRepository',
            $informacaoComissaoRepositoryMock
        );

        $this->setPrivateProperty(
            $informacaoComissaoMembroBO,
            'historicoInformacaoComissaoMembroBO',
            $historicoInformacaoComissaoBOMock
        );

        $this->setPrivateProperty(
            $informacaoComissaoMembroBO,
            'historicoAlterecaoInformacaoComissaoMembroBO',
            $historicoAlteracaoInformacaoComissaoBOMock
        );

        $this->assertNotEmpty($informacaoComissaoMembroBO->salvar($informacaoComissaoMembro));
    }

    /**
     * Testa se os dados de informação comissão membro estão sendo salvos sem o e-mail.
     *
     * @throws ReflectionException
     * @throws \App\Exceptions\NegocioException
     */
    public function testSalvarSemEmailComSucesso()
    {
        $informacaoComissaoMembro = $this->criarInformacaoComissaoMembro();
        $informacaoComissaoMembroSalvo = $this->criarInformacaoComissaoMembro();
        $informacaoComissaoMembroSalvo->setId(self::ID_INFORMACAO_COMISSAO_MEMBRO);

        $historicosInformacaoComissao = $this->criarHistoricosInformacaoComissao();
        $historicosAlteracaoInformacaoComissao = $this->getHistoricosAlteracaoInformacaoComissao();

        $informacaoComissaoRepositoryMock = $this->createMock(InformacaoComissaoMembroRepository::class);

        $historicoInformacaoComissaoBOMock = $this->createMock(HistoricoInformacaoComissaoMembroBO::class);

        $historicoAlteracaoInformacaoComissaoBOMock = $this->createMock(HistoricoAlteracaoInformacaoComissaoMembroBO::class
        );

        $informacaoComissaoRepositoryMock->method('persist')->willReturn($informacaoComissaoMembroSalvo);
        $historicoInformacaoComissaoBOMock->method('salvar')->willReturn($historicosInformacaoComissao);
        $historicoAlteracaoInformacaoComissaoBOMock->method('salvar')->willReturn(
            $historicosAlteracaoInformacaoComissao
        );

        $informacaoComissaoMembroBO = new InformacaoComissaoMembroBO();

        $this->setPrivateProperty(
            $informacaoComissaoMembroBO,
            'informacaoComissaoMembroRepository',
            $informacaoComissaoRepositoryMock
        );

        $this->setPrivateProperty(
            $informacaoComissaoMembroBO,
            'historicoInformacaoComissaoMembroBO',
            $historicoInformacaoComissaoBOMock
        );

        $this->setPrivateProperty(
            $informacaoComissaoMembroBO,
            'historicoAlterecaoInformacaoComissaoMembroBO',
            $historicoAlteracaoInformacaoComissaoBOMock
        );

        $this->assertNotEmpty($informacaoComissaoMembroBO->salvar($informacaoComissaoMembro));
    }

    /**
     * Válida se os dados estão sendo salvos de forma correta sem o parâmetro de e-mail e sem o parâmetro de documento.
     *
     * @throws ReflectionException
     * @throws \App\Exceptions\NegocioException
     */
    public function testSalvarComDocumentoSemEmailComSucesso()
    {
        $informacaoComissaoMembro = $this->criarInformacaoComissaoMembro(false, false);
        $informacaoComissaoMembroSalvo = $this->criarInformacaoComissaoMembro(false, false);
        $informacaoComissaoMembroSalvo->setId(self::ID_INFORMACAO_COMISSAO_MEMBRO);

        $historicosInformacaoComissao = $this->criarHistoricosInformacaoComissao();
        $historicosAlteracaoInformacaoComissao = $this->getHistoricosAlteracaoInformacaoComissao();

        $informacaoComissaoRepositoryMock = $this->createMock(InformacaoComissaoMembroRepository::class);

        $historicoInformacaoComissaoBOMock = $this->createMock(
            HistoricoInformacaoComissaoMembroBO::class
        );

        $historicoAlteracaoInformacaoComissaoBOMock = $this->createMock(
            HistoricoAlteracaoInformacaoComissaoMembroBO::class
        );

        $informacaoComissaoRepositoryMock->method('persist')->willReturn($informacaoComissaoMembroSalvo);
        $historicoInformacaoComissaoBOMock->method('salvar')->willReturn($historicosInformacaoComissao);
        $historicoAlteracaoInformacaoComissaoBOMock->method('salvar')->willReturn(
            $historicosAlteracaoInformacaoComissao
        );

        $informacaoComissaoMembroBO = new InformacaoComissaoMembroBO();

        $this->setPrivateProperty(
            $informacaoComissaoMembroBO,
            'informacaoComissaoMembroRepository',
            $informacaoComissaoRepositoryMock
        );

        $this->setPrivateProperty(
            $informacaoComissaoMembroBO,
            'historicoInformacaoComissaoMembroBO',
            $historicoInformacaoComissaoBOMock
        );

        $this->setPrivateProperty(
            $informacaoComissaoMembroBO,
            'historicoAlterecaoInformacaoComissaoMembroBO',
            $historicoAlteracaoInformacaoComissaoBOMock
        );

        $this->assertNotEmpty($informacaoComissaoMembroBO->salvar($informacaoComissaoMembro));
    }

    /**
     * Válida se a mensagem apresentada está correta para quando não existir atividade secundária.
     */
    public function testSalvarCampoObrigatorioAtividadeSecundariaNaoInformado()
    {
        $informacaoComissaoMembro = $this->criarInformacaoComissaoMembro();
        $informacaoComissaoMembro->getAtividadeSecundaria()->setId(null);
        $informacaoComissaoMembroBO = new InformacaoComissaoMembroBO();

        try {
            $this->assertNotEmpty($informacaoComissaoMembroBO->salvar($informacaoComissaoMembro));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Válida se a mensagem apresentada está correta para quando não existir tipo de opção.
     */
    public function testSalvarCampoObrigatorioTipoOpcaoNaoInformado()
    {
        $informacaoComissaoMembro = $this->criarInformacaoComissaoMembro();
        $informacaoComissaoMembro->setTipoOpcao(null);
        $informacaoComissaoMembroBO = new InformacaoComissaoMembroBO();

        try {
            $this->assertNotEmpty($informacaoComissaoMembroBO->salvar($informacaoComissaoMembro));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Válida se a mensagem apresentada está correta para quando não existir quantidade mínima de membros da comissão.
     */
    public function testSalvarCampoObrigatorioQuantidadeMinimaNaoInformado()
    {
        $informacaoComissaoMembro = $this->criarInformacaoComissaoMembro();
        $informacaoComissaoMembro->setQuantidadeMinima(null);
        $informacaoComissaoMembroBO = new InformacaoComissaoMembroBO();

        try {
            $this->assertNotEmpty($informacaoComissaoMembroBO->salvar($informacaoComissaoMembro));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Válida se a mensagem apresentada está correta para quando não existir quantidade máxima de membros da comissão.
     */
    public function testSalvarCampoObrigatorioQuantidadeMaximaNaoInformado()
    {
        $informacaoComissaoMembro = $this->criarInformacaoComissaoMembro();
        $informacaoComissaoMembro->setQuantidadeMaxima(null);
        $informacaoComissaoMembroBO = new InformacaoComissaoMembroBO();

        try {
            $this->assertNotEmpty($informacaoComissaoMembroBO->salvar($informacaoComissaoMembro));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Válida se as informações da comissão eleitoral estão sendo concluídas.
     *
     * @throws ReflectionException
     */
    public function testConcluirComSucesso()
    {
        $documentoComissaoMembro = $this->criarDocumentoComissaoMembro();
        $informacaoComissaoMembroSalvo = $this->criarInformacaoComissaoMembro();
        $informacaoComissaoMembroSalvo->setId(self::ID_INFORMACAO_COMISSAO_MEMBRO);

        $historicosInformacaoComissao = $this->criarHistoricosInformacaoComissao();
        $historicosAlteracaoInformacaoComissao = $this->getHistoricosAlteracaoInformacaoComissao();

        $documentoComissaoMembroBOMock = $this->createMock(DocumentoComissaoMembroBO::class);
        $informacaoComissaoRepositoryMock = $this->createMock(InformacaoComissaoMembroRepository::class);
        $documentoComissaoMembroRepository = $this->createMock(DocumentoComissaoMembroRepository::class);
        $historicoInformacaoComissaoBOMock = $this->createMock(HistoricoInformacaoComissaoMembroBO::class);
        $historicoAlteracaoInformacaoComissaoBOMock = $this->createMock(HistoricoAlteracaoInformacaoComissaoMembroBO::class);

        $documentoComissaoMembroBOMock->method('getPorId')->willReturn($documentoComissaoMembro);
        $documentoComissaoMembroRepository->method('persist')->willReturn($documentoComissaoMembro);
        $informacaoComissaoRepositoryMock->method('persist')->willReturn($informacaoComissaoMembroSalvo);
        $historicoInformacaoComissaoBOMock->method('salvar')->willReturn($historicosInformacaoComissao);
        $historicoAlteracaoInformacaoComissaoBOMock->method('salvar')->willReturn($historicosAlteracaoInformacaoComissao);

        $informacaoComissaoMembroBO = new InformacaoComissaoMembroBO();

        $this->setPrivateProperty(
            $informacaoComissaoMembroBO,
            'informacaoComissaoMembroRepository',
            $informacaoComissaoRepositoryMock
        );

        $this->setPrivateProperty(
            $informacaoComissaoMembroBO,
            'historicoInformacaoComissaoMembroBO',
            $historicoInformacaoComissaoBOMock
        );

        $this->setPrivateProperty(
            $informacaoComissaoMembroBO,
            'historicoAlterecaoInformacaoComissaoMembroBO',
            $historicoAlteracaoInformacaoComissaoBOMock
        );

        $documentoComissaoBO = new DocumentoComissaoMembroBO();

        $this->setPrivateProperty(
            $documentoComissaoBO,
            'documentoComissaoMembroRepository',
            $documentoComissaoMembroRepository
        );

        $this->setPrivateProperty(
            $documentoComissaoBO,
            'historicoInformacaoComissaoMembroBO',
            $historicoInformacaoComissaoBOMock
        );

        $this->setPrivateProperty(
            $documentoComissaoBO,
            'historicoAlteracaoInformacaoComissaoMembroBO',
            $historicoAlteracaoInformacaoComissaoBOMock
        );

        $this->assertNotEmpty($informacaoComissaoMembroBO->concluir($documentoComissaoMembro));
    }

    /**
     * Retorna a informação da comissão membro.
     *
     * @param bool $semEmail
     * @param bool $semDocumento
     * @return InformacaoComissaoMembro
     */
    private function criarInformacaoComissaoMembro($semEmail = true, $semDocumento = true)
    {
        $informacaoComissaoMembro = InformacaoComissaoMembro::newInstance();

        $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance();
        $atividadeSecundaria->setId(self::ID_ATIVIDADE_SECUNDARIA);

        $email = CorpoEmail::newInstance();
        if (!$semEmail) {
            $email->setId(self::ID_EMAIL);
        }

        $documentosArray = new ArrayCollection();
        if (!$semDocumento) {
            $documento = DocumentoComissaoMembro::newInstance();
            $documento->setId(self::ID_DOCUMENTO_COMISSAO_MEMBRO);
            $documentosArray->add($documento);
        }

        $informacaoComissaoMembro->setEmail($email);
        $informacaoComissaoMembro->setTipoOpcao(1);
        $informacaoComissaoMembro->setDocumentoComissaoMembro($documentosArray);
        $informacaoComissaoMembro->setQuantidadeMaxima(5);
        $informacaoComissaoMembro->setQuantidadeMinima(10);
        $informacaoComissaoMembro->setAtividadeSecundaria($atividadeSecundaria);
        $informacaoComissaoMembro->setSituacaoConselheiro(true);
        $informacaoComissaoMembro->setSituacaoMajoritario(true);

        return $informacaoComissaoMembro;
    }

    /**
     * Retorna uma instância de 'DocumentoComissaoMembro' populada.
     *
     * @return DocumentoComissaoMembro
     */
    private function criarDocumentoComissaoMembro()
    {
        $documentoComissaoMembro = DocumentoComissaoMembro::newInstance();
        $documentoComissaoMembro->setId(self::ID_DOCUMENTO_COMISSAO_MEMBRO);

        $documentoComissaoMembro->setDescricaoCabecalho('<p>Teste</p>');
        $documentoComissaoMembro->setSituacaoCabecalhoAtivo(true);

        $documentoComissaoMembro->setDescricaoTextoInicial('<p>Teste</p>');
        $documentoComissaoMembro->setSituacaoTextoInicial(true);

        $documentoComissaoMembro->setDescricaoTextoFinal('<p>Teste</p>');
        $documentoComissaoMembro->setSituacaoTextoFinal(true);

        $documentoComissaoMembro->setDescricaoTextoRodape('<p>Teste</p>');
        $documentoComissaoMembro->setSituacaoTextoRodape(true);

        $informacaoComissaoMembro = $this->criarInformacaoComissaoMembro();
        $informacaoComissaoMembro->setId(self::ID_INFORMACAO_COMISSAO_MEMBRO);
        $documentoComissaoMembro->setInformacaoComissaoMembro($informacaoComissaoMembro);

        return $documentoComissaoMembro;
    }

    /**
     * Retorna uma coleção de Históricos de Informação da Comissão.
     *
     * @return HistoricoInformacaoComissaoMembro
     * @throws Exception
     */
    private function criarHistoricosInformacaoComissao()
    {
        $historicoInformacaoComissaoMembro = HistoricoInformacaoComissaoMembro::newInstance();
        $historicoInformacaoComissaoMembro->setId(self::ID_HISTORICO_INFORMACAO_COMISSAO);
        $historicoInformacaoComissaoMembro->setDataHistorico(Utils::getData());
        $historicoInformacaoComissaoMembro->setResponsavel(Constants::ID_USUARIO_MOCK);
        $historicoInformacaoComissaoMembro->setAcao(Constants::ACAO_HISTORICO_INFORMACAO_COMISSAO_INSERIR);
        return $historicoInformacaoComissaoMembro;
    }

    /**
     * Retorna uma coleção de Históricos de Alteração de Informação da Comissão.
     *
     * @return ArrayCollection
     */
    private function getHistoricosAlteracaoInformacaoComissao()
    {
        $historicos = new ArrayCollection();

        for ($contador = 0; $contador <= 1; $contador++) {
            $historicoAlteracaoInformacaoComissaoMembro = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $historicoAlteracaoInformacaoComissaoMembro->setId(self::ID_ALTERACAO_HISTORICO_INFORMACAO_COMISSAO);
            $historicos->add($historicoAlteracaoInformacaoComissaoMembro);
        }

        return $historicos;
    }

}
