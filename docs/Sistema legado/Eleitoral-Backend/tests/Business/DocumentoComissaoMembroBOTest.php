<?php

use App\Util\Utils;
use App\Config\Constants;
use App\Exceptions\Message;
use App\Entities\CorpoEmail;
use App\Exceptions\NegocioException;
use App\Entities\DocumentoComissaoMembro;
use App\Entities\InformacaoComissaoMembro;
use App\Business\DocumentoComissaoMembroBO;
use App\Entities\AtividadeSecundariaCalendario;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entities\HistoricoInformacaoComissaoMembro;
use App\Business\HistoricoInformacaoComissaoMembroBO;
use App\Repository\DocumentoComissaoMembroRepository;
use App\Entities\HistoricoAlteracaoInformacaoComissaoMembro;
use App\Business\HistoricoAlteracaoInformacaoComissaoMembroBO;

/**
 * Teste de Unidade referente á classe DocumentoComissaoMembroBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class DocumentoComissaoMembroBOTest extends TestCase
{

    const ID_EMAIL = 1;
    const ID_ATIVIDADE_SECUNDARIA = 1;
    const ID_DOCUMENTO_COMISSAO_MEMBRO = 1;
    const ID_INFORMACAO_COMISSAO_MEMBRO = 1;
    const ID_HISTORICO_INFORMACAO_COMISSAO = 1;
    const ID_ALTERACAO_HISTORICO_INFORMACAO_COMISSAO = 1;

    /**
     * Válida se os dados de documento comissão membro estão sendo salvos com sucesso.
     * 
     * @throws ReflectionException
     */
    public function testSalvarComSucesso()
    {
        $documentoComissaoBO = new DocumentoComissaoMembroBO();
        $documentoComissaoMembro = $this->criarDocumentoComissaoMembro();
        $documentoComissaoMembroSalvo = $this->criarDocumentoComissaoMembro();
        $documentoComissaoMembroSalvo->setId(self::ID_DOCUMENTO_COMISSAO_MEMBRO);
        $historicosInformacaoComissao = $this->criarHistoricosInformacaoComissao();
        $historicosAlteracaoInformacaoComissao = $this->getHistoricosAlteracaoInformacaoComissao();

        $documentoComissaoMembroRepository = $this->createMock(DocumentoComissaoMembroRepository::class);
        $historicoInformacaoComissaoBOMock = $this->createMock(HistoricoInformacaoComissaoMembroBO::class);
        $historicoAlteracaoInformacaoComissaoBOMock = $this->createMock(HistoricoAlteracaoInformacaoComissaoMembroBO::class);

        $documentoComissaoMembroRepository->method('persist')->willReturn($documentoComissaoMembroSalvo);
        $historicoInformacaoComissaoBOMock->method('salvar')->willReturn($historicosInformacaoComissao);
        $historicoAlteracaoInformacaoComissaoBOMock->method('salvar')->willReturn(
            $historicosAlteracaoInformacaoComissao
        );

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

        $this->assertNotEmpty($documentoComissaoBO->salvar($documentoComissaoMembro));
    }

    /**
     * Válida se o campo de cabeçalho foi informado.
     *
     * @throws Exception
     */
    public function testSalvarCampoObrigatorioCabecalhoNaoInformado()
    {
        $oducmentoComissaoMembro = $this->criarDocumentoComissaoMembro();
        $oducmentoComissaoMembro->setDescricaoCabecalho(null);
        $documentoComissaoMembroBO = new DocumentoComissaoMembroBO();

        try {
            $this->assertNotEmpty($documentoComissaoMembroBO->salvar($oducmentoComissaoMembro));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Válida se o campo de cabeçalho foi informado.
     *
     * @throws Exception
     */
    public function testSalvarCampoObrigatorioTextoIncialNaoInformado()
    {
        $oducmentoComissaoMembro = $this->criarDocumentoComissaoMembro();
        $oducmentoComissaoMembro->setDescricaoTextoInicial(null);
        $documentoComissaoMembroBO = new DocumentoComissaoMembroBO();

        try {
            $this->assertNotEmpty($documentoComissaoMembroBO->salvar($oducmentoComissaoMembro));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Válida se o campo de cabeçalho foi informado.
     *
     * @throws Exception
     */
    public function testSalvarCampoObrigatorioTextoFinalNaoInformado()
    {
        $oducmentoComissaoMembro = $this->criarDocumentoComissaoMembro();
        $oducmentoComissaoMembro->setDescricaoTextoFinal(null);
        $documentoComissaoMembroBO = new DocumentoComissaoMembroBO();

        try {
            $this->assertNotEmpty($documentoComissaoMembroBO->salvar($oducmentoComissaoMembro));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Válida se o campo de cabeçalho foi informado.
     *
     * @throws Exception
     */
    public function testSalvarCampoObrigatorioRodapeNaoInformado()
    {
        $oducmentoComissaoMembro = $this->criarDocumentoComissaoMembro();
        $oducmentoComissaoMembro->setDescricaoTextoRodape(null);
        $documentoComissaoMembroBO = new DocumentoComissaoMembroBO();

        try {
            $this->assertNotEmpty($documentoComissaoMembroBO->salvar($oducmentoComissaoMembro));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Retorna uma instância de 'DocumentoComissaoMembro' populada.
     *
     * @return DocumentoComissaoMembro
     */
    private function criarDocumentoComissaoMembro()
    {
        $documentoComissaoMembro = DocumentoComissaoMembro::newInstance();

        $documentoComissaoMembro->setDescricaoCabecalho('<p>Teste Cabeçalho</p>');
        $documentoComissaoMembro->setSituacaoCabecalhoAtivo(true);

        $documentoComissaoMembro->setDescricaoTextoInicial('<p>Teste Texto Inicial</p>');
        $documentoComissaoMembro->setSituacaoTextoInicial(true);

        $documentoComissaoMembro->setDescricaoTextoFinal('<p>Teste Texto Final</p>');
        $documentoComissaoMembro->setSituacaoTextoFinal(true);

        $documentoComissaoMembro->setDescricaoTextoRodape('<p>Teste Rodapé</p>');
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
        $historicoInformacaoComissaoMembro = \App\Entities\HistoricoInformacaoComissaoMembro::newInstance();
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

}
