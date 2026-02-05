<?php

use App\Security\Token\TokenContext;
use \App\To\EleicaoTO;
use \App\To\CalendarioTO;
use \App\To\AtividadeSecundariaTO;
use \App\To\PublicacaoDocumentoTO;
use \App\Entities\PublicacaoDocumento;
use \App\To\DocumentoComissaoMembroTO;
use \App\To\InformacaoComissaoMembroTO;
use \App\Business\PublicacaoDocumentoBO;
use \App\To\AtividadePrincipalCalendarioTO;
use \App\Repository\PublicacaoDocumentoRepository;
use \App\Repository\DocumentoComissaoMembroRepository;

/**
 * Teste de Unidade referente à classe PublicacaoDocumentoBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class PublicacaoDocumentoBOTest extends TestCase
{

    const ID_ELEICAO = 1;

    const ID_CALENDARIO = 1;

    const ID_RESPONSAVEL = 1;

    const ANO_ELEICAO = 2019;

    const SEQUENCIA_ANO_ELEICAO = 1;

    const ID_ATIVIDADE_PRINCIPAL = 1;

    const ID_ATIVIDADE_SECUNDARIA = 1;

    const ID_PUBLICACAO_DOCUMENTO = 1;

    const ID_DOCUMENTO_COMISSAO_MEMBRO = 1;

    const ID_INFORMACAO_COMISSAO_MEMBRO = 1;

    const ID_PUBLICACAO_DOCUMENTO_INEXISTENTE = 9999999999;

    /**
     * Válida se o retorno do 'getPorId' está correto.
     *
     * @throws ReflectionException
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testGetPorIdComSucesso()
    {
        $data = [];
        $data['id'] = self::ID_PUBLICACAO_DOCUMENTO;
        $publicacaoDocumentoRepositoryMock = $this->createMock(PublicacaoDocumentoRepository::class);
        $publicacaoDocumentoRepositoryMock->method('getPorId')->willReturn(PublicacaoDocumentoTO::newInstance($data));

        $publicacaoDocumentoBO = new PublicacaoDocumentoBO();
        $this->setPrivateProperty($publicacaoDocumentoBO, 'publicacaoDocumentoRepository', $publicacaoDocumentoRepositoryMock);

        $publicacaoDocumento = $publicacaoDocumentoBO->getPorId(self::ID_PUBLICACAO_DOCUMENTO);

        $this->assertEquals($publicacaoDocumento, PublicacaoDocumentoTO::newInstance($data));
    }

    /**
     * Válida se o retorno do 'getPorId' para id de publicação inexistente.
     *
     * @throws ReflectionException
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testGetPorIdInexistente()
    {
        $publicacaoDocumentoRepositoryMock = $this->createMock(PublicacaoDocumentoRepository::class);
        $publicacaoDocumentoRepositoryMock->method('getPorId')->willReturn(PublicacaoDocumentoTO::newInstance());

        $publicacaoDocumentoBO = new PublicacaoDocumentoBO();
        $this->setPrivateProperty($publicacaoDocumentoBO, 'publicacaoDocumentoRepository', $publicacaoDocumentoRepositoryMock);

        $publicacaoDocumento = $publicacaoDocumentoBO->getPorId(self::ID_PUBLICACAO_DOCUMENTO_INEXISTENTE);

        $this->assertEquals($publicacaoDocumentoBO->getPorId(self::ID_PUBLICACAO_DOCUMENTO_INEXISTENTE), $publicacaoDocumento);
    }

    /**
     * Válida se a publicação está sendo salva com sucesso.
     *
     * @throws ReflectionException
     */
    public function testSalvarComSucesso()
    {
        $tokenContext = app()->make(TokenContext::class);
        $tokenBuilder = $tokenContext->createTokenBuilder();

        $usuarioLogado = new \stdClass();
        $usuarioLogado->appToken = $tokenBuilder->encode()['accessToken'];

        $responsavel = $this->criarResponsavel();
        $publicacaoDocumentoSemId = $this->criarPublicacaoDocumento();
        $publicacaoDocumentoComId = $this->criarPublicacaoDocumento(true);

        $publicacaoDocumentoRepositoryMock = $this->createMock(PublicacaoDocumentoRepository::class);
        $publicacaoDocumentoRepositoryMock->method('persist')->willReturn($publicacaoDocumentoSemId);
        $publicacaoDocumentoRepositoryMock->method('getPorId')->willReturn($publicacaoDocumentoComId);

        $documentoComissaoMembro = $this->getDocumentoComissaoMembro();
        $documentoComissaoMembroRepositoryMock = $this->createMock(DocumentoComissaoMembroRepository::class);
        $documentoComissaoMembroRepositoryMock->method('getPorId')->willReturn($documentoComissaoMembro);

        $publicacaoDocumentoBO = new PublicacaoDocumentoBO();
        $this->setPrivateProperty($publicacaoDocumentoBO, 'publicacaoDocumentoRepository', $publicacaoDocumentoRepositoryMock);

        $this->assertNotNull($publicacaoDocumentoBO->salvar($publicacaoDocumentoSemId, $responsavel, $usuarioLogado));
    }

    /**
     * Testa se o objeto de arquivo está sendo retornado corretamente.
     */
    public function testGerarPdfComSucesso()
    {
        $publicacaoDocumentoBOMock = $this->createMock(PublicacaoDocumentoBO::class);
        $publicacaoDocumentoBOMock->method('gerarPdf')->willReturn($this->getArquivoTO());
        $this->assertNotEmpty($publicacaoDocumentoBOMock->gerarPdf(self::ID_DOCUMENTO_COMISSAO_MEMBRO));
    }

    /**
     * Testa se o objeto de arquivo está sendo retornado incorretamente.
     */
    public function testGerarPdfSemSucesso()
    {
        $publicacaoDocumentoBOMock = $this->createMock(PublicacaoDocumentoBO::class);
        $publicacaoDocumentoBOMock->method('gerarPdf')->willReturn(null);
        $this->assertNull($publicacaoDocumentoBOMock->gerarPdf(self::ID_PUBLICACAO_DOCUMENTO_INEXISTENTE));
    }

    /**
     * Testa se o objeto de download de pdf está sendo retornado corretamente.
     */
    public function testDownloadPdfComSucesso()
    {
        $publicacaoDocumentoBOMock = $this->createMock(PublicacaoDocumentoBO::class);
        $publicacaoDocumentoBOMock->method('downloadPdf')->willReturn($this->getArquivoTO());
        $this->assertNotEmpty($publicacaoDocumentoBOMock->downloadPdf(self::ID_PUBLICACAO_DOCUMENTO));
    }

    /**
     * Testa se o objeto de download de pdf está sendo retornado incorretamente.
     */
    public function testDownloadPdfSemSucesso()
    {
        $publicacaoDocumentoBOMock = $this->createMock(PublicacaoDocumentoBO::class);
        $publicacaoDocumentoBOMock->method('downloadPdf')->willReturn(null);
        $this->assertNull($publicacaoDocumentoBOMock->downloadPdf(self::ID_DOCUMENTO_COMISSAO_MEMBRO));
    }

    /**
     * Retorna uma nova instância de 'PublicacaoDocumento'.
     *
     * @return PublicacaoDocumento
     * @throws Exception
     */
    private function criarPublicacaoDocumento($comId = false)
    {
        $documentoComissaoMembro = DocumentoComissaoMembroTO::newInstance([
            'id' => self::ID_DOCUMENTO_COMISSAO_MEMBRO
        ]);

        $publicacaoDocumento = PublicacaoDocumento::newInstance();

        if (boolval($comId)) {
            $publicacaoDocumento->setId(self::ID_PUBLICACAO_DOCUMENTO);
        }

        $publicacaoDocumento->setDocumentoComissaoMembro($documentoComissaoMembro);
        return $publicacaoDocumento;
    }

    /**
     * Retorna o objeto referente ao responsável pelo registro do valor.
     *
     * @return stdClass
     */
    private function criarResponsavel()
    {
        $responsavel = new \stdClass();
        $responsavel->id = self::ID_RESPONSAVEL;
        return $responsavel;
    }

    /**
     * Retorna uma nova instância de 'DocumentoComissaoMembroTO'.
     *
     * @return DocumentoComissaoMembroTO
     */
    private function getDocumentoComissaoMembro()
    {
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
        $documentoComissaoMembro->setId(self::ID_DOCUMENTO_COMISSAO_MEMBRO);
        $documentoComissaoMembro->setDescricaoCabecalho('');
        $documentoComissaoMembro->setDescricaoTextoFinal('');
        $documentoComissaoMembro->setSituacaoTextoFinal(true);
        $documentoComissaoMembro->setDescricaoTextoInicial('');
        $documentoComissaoMembro->setDescricaoTextoRodape('');
        $documentoComissaoMembro->setSituacaoTextoRodape(true);
        $documentoComissaoMembro->setSituacaoTextoInicial(true);
        $documentoComissaoMembro->setSituacaoCabecalhoAtivo(true);
        $documentoComissaoMembro->setInformacaoComissaoMembro($informacacaoComissaoMembro);

        return $documentoComissaoMembro;
    }

    /**
     * Recupera um objeto de arquivo.
     *
     * @return stdClass
     */
    private function getArquivoTO()
    {
        $arquivo = new stdClass();
        $arquivo->name = "nomeTest.pdf";
        $arquivo->size = 12333;
        $arquivo->type = 'pdf';
        return $arquivo;
    }

}
