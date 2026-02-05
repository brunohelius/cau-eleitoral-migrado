<?php
/*
 * DocumentoEleicaoBOTest.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

use App\Entities\DocumentoEleicao;
use App\Business\DocumentoEleicaoBO;
use App\Entities\Eleicao;
use App\Repository\DocumentoEleicaoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use \App\Exceptions\NegocioException;
use App\Exceptions\Message;
use App\Service\ArquivoService;

/**
 * Teste de Unidade referente á classe DocumentoEleicaoBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class DocumentoEleicaoBOTest extends TestCase
{
    const ID_CALENDARIO = 1;
    const ID_DOCUMENTO = 1;

    /**
     * Testa a execução do método 'getDocumentosPorEleicao', com sucesso, retornando os documentos
     * da eleição informada.
     */
    public function testarGetDocumentosEleicaoPorCalendarioComSucesso()
    {
        $documentos = $this->criarListaDocumentos();

        $documentoEleicaoRepositoryMock = $this->createMock(\App\Repository\DocumentoEleicaoRepository::class);
        $documentoEleicaoRepositoryMock->method('getDocumentosPorEleicao')->willReturn($documentos);

        $documentoEleicaoBO = new DocumentoEleicaoBO();
        $this->setPrivateProperty($documentoEleicaoBO, 'documentoEleicaoRepository', $documentoEleicaoRepositoryMock);

        $this->assertNotEmpty($documentoEleicaoBO->getDocumentosEleicaoPorCalendario(self::ID_CALENDARIO));
    }

    /**
     * Testa a execução do método 'getDocumentosPorEleicao', sem sucesso.
     */
    public function testarGetDocumentosEleicaoPorCalendarioSemSucesso()
    {
        $documentos = $this->criarListaDocumentos();

        $documentoEleicaoRepositoryMock = $this->createMock(\App\Repository\DocumentoEleicaoRepository::class);
        $documentoEleicaoRepositoryMock->method('getDocumentosPorEleicao')->willReturn($documentos);

        $documentoEleicaoBO = new DocumentoEleicaoBO();
        $this->setPrivateProperty($documentoEleicaoBO, 'documentoEleicaoRepository', $documentoEleicaoRepositoryMock);

        try {
            $documentoEleicaoBO->getDocumentosEleicaoPorCalendario(null);
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_FILTRO_OBRIGATORIO]);
        }
    }

    /**
     * Testar o método Salvar.
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarSalvarDocumentoComSucesso()
    {
        $documentoEleicao = $this->criarDocumento(false);
        $documentoEleicao->setId(self::ID_DOCUMENTO);
        $documentoEleicaoSalvo = $this->criarDocumento(false);
        $documentoEleicaoSalvo->setId(self::ID_DOCUMENTO);

        $documentoEleicaoRepositoryMock = $this->createMock(DocumentoEleicaoRepository::class);
        $documentoEleicaoRepositoryMock->method('persist')->willReturn($documentoEleicaoSalvo);
        $documentoEleicaoBO = new DocumentoEleicaoBO();
        $this->setPrivateProperty($documentoEleicaoBO, 'documentoEleicaoRepository', $documentoEleicaoRepositoryMock);

        $arquivoServiceMock = $this->createMock(ArquivoService::class);
        $arquivoServiceMock->method('salvar')->willReturn(null);
        $this->setPrivateProperty($documentoEleicaoBO, 'arquivoService', $arquivoServiceMock);

        try {
            $documentoEleicaoBO->salvar($documentoEleicao);
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Testar o método Salvar.
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarSalvarDocumentoSemSucesso()
    {
        $documentoEleicao = $this->criarDocumento();
        $documentoEleicao->setId(self::ID_DOCUMENTO);
        $documentoEleicaoSalvo = $this->criarDocumento();
        $documentoEleicaoSalvo->setId(self::ID_DOCUMENTO);

        $documentoEleicaoRepositoryMock = $this->createMock(DocumentoEleicaoRepository::class);
        $documentoEleicaoRepositoryMock->method('persist')->willReturn($documentoEleicaoSalvo);
        $documentoEleicaoBO = new DocumentoEleicaoBO();
        $this->setPrivateProperty($documentoEleicaoBO, 'documentoEleicaoRepository', $documentoEleicaoRepositoryMock);

        $arquivoServiceMock = $this->createMock(ArquivoService::class);
        $arquivoServiceMock->method('salvar')->willReturn(null);
        $this->setPrivateProperty($documentoEleicaoBO, 'arquivoService', $arquivoServiceMock);

        $this->assertNotEmpty($documentoEleicaoBO->salvar($documentoEleicao));
    }

    /**
     * Cria uma instância de declaração.
     *
     * @param bool $todosOsCampos
     * @return Declaracao
     * @throws Exception
     */
    private function criarDocumento($todosOsCampos = true)
    {
        $documentoEleicao = DocumentoEleicao::newInstance();

        if ($todosOsCampos) {
            $eleicao = Eleicao::newInstance();
            $eleicao->setId(self::ID_CALENDARIO);
            $documentoEleicao->setEleicao($eleicao);
            $documentoEleicao->setCorporativo(true);
            $documentoEleicao->setProfissional(false);
            $documentoEleicao->setPublico(false);
            $documentoEleicao->setNomeArquivo("Arquivo 1.pdf");
            $documentoEleicao->setNomeArquivoFisico("Arquivo 1.pdf");
            $documentoEleicao->setSequencial(1);
            $documentoEleicao->setIdUsuario(1);
            $documentoEleicao->setNomeUsuario("Usuário 1");
            $documentoEleicao->setArquivo("xxxx");
            $documentoEleicao->setTamanho(1);
        }

        return $documentoEleicao;
    }

    /**
     * @return ArrayCollection
     */
    private function criarListaDocumentos()
    {
        $documentos = new ArrayCollection();

        for ($i=0; $i<5; $i++) {
            $documentoEleicao = DocumentoEleicao::newInstance();
            $documentoEleicao->setId($i+1);
            $documentos->add($documentoEleicao);
        }

        return $documentos;
    }
}