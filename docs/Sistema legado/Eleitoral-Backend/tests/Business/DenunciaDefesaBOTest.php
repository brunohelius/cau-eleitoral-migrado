<?php

use App\Factory\UsuarioFactory;
use App\Repository\DenunciaDefesaRepository;
use App\To\DenunciaDefesaTO;
use App\Util\Utils;
use App\Business\DenunciaDefesaBO;
use App\Exceptions\Message;
use App\Entities\ArquivoDenunciaDefesa;
use App\Entities\DenunciaDefesa;
use App\Entities\Denuncia;
use App\To\ArquivoTO;
use App\Repository\ArquivoDenunciaDefesaRepository;
use App\Service\ArquivoService;
use App\Exceptions\NegocioException;

/**
 * Teste de Unidade referente à classe DenunciaBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class DenunciaDefesaBOTest extends TestCase
{
    /**
     * Testa o método GetPorDenunciado com sucesso
     *
     * @throws ReflectionException
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testarGetPorDenunciadoComSucesso()
    {
        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = 123;

        $defesa = new DenunciaDefesaTO();
        $defesa->setIdDenuncia(1);
        $defesa->setId(1);
        $defesa->setDataDefesa(Utils::getData());
        $defesa->setDescricaoDefesa('teste teste');

        $usuarioFactoryMock = $this->createMock(UsuarioFactory::class);
        $usuarioFactoryMock->method('getUsuarioLogado')->willReturn($usuarioLogado);

        $denunciaDefesaRepositoryMock = $this->createMock(DenunciaDefesaRepository::class);
        $denunciaDefesaRepositoryMock->method('getPorDenunciado')->withAnyParameters()->willReturn($defesa);

        $denunciaDefesaBO = new DenunciaDefesaBO();
        $this->setPrivateProperty($denunciaDefesaBO, 'usuarioFactory', $usuarioFactoryMock);
        $this->setPrivateProperty($denunciaDefesaBO, 'denunciaDefesaRepository', $denunciaDefesaRepositoryMock);

        $this->assertNotEmpty($denunciaDefesaBO->getPorDenunciado());
    }

    /**
     * Testa o método GetPorDenunciado sem sucesso
     *
     * @throws ReflectionException
     */
    public function testarGetPorDenunciadoSemSucesso()
    {
        $usuarioLogado = new \stdClass();
        $usuarioLogado->idProfissional = 123;

        $usuarioFactoryMock = $this->createMock(UsuarioFactory::class);
        $usuarioFactoryMock->method('getUsuarioLogado')->willReturn($usuarioLogado);

        $denunciaDefesaRepositoryMock = $this->createMock(DenunciaDefesaRepository::class);
        $denunciaDefesaRepositoryMock->method('getPorDenunciado')->withAnyParameters()->willReturn(null);

        $denunciaDefesaBO = new DenunciaDefesaBO();
        $this->setPrivateProperty($denunciaDefesaBO, 'usuarioFactory', $usuarioFactoryMock);
        $this->setPrivateProperty($denunciaDefesaBO, 'denunciaDefesaRepository', $denunciaDefesaRepositoryMock);

        try {
            $this->assertNotEmpty($denunciaDefesaBO->getPorDenunciado());
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), Message::MSG_NAO_EXISTE_DEFESA_DENUNCIA);
        }
    }

    /**
     * testar método getArquivo com sucesso
     *
     * @throws ReflectionException
     * @throws \App\Exceptions\NegocioException
     */
    public function testarGetArquivoComSucesso()
    {
        $arquivo = new ArquivoDenunciaDefesa();
        $arquivo->setId(1);
        $arquivo->setTamanho(123333);
        $arquivo->setNomeFisicoArquivo('nome_123323.pdf');
        $arquivo->setNome('nome.pdf');

        $denunciaDefesa = new DenunciaDefesa();
        $denunciaDefesa->setId(1);
        $denunciaDefesa->setDescricaoDefesa('test test');
        $denunciaDefesa->setDataDefesa(Utils::getData());

        $denuncia = new Denuncia();
        $denuncia->setId(1);

        $denunciaDefesa->setDenuncia($denuncia);

        $arquivo->setDenunciaDefesa($denunciaDefesa);

        $arquivoTO = new ArquivoTO();
        $arquivoTO->name = 'nome.pdf';
        $arquivoTO->type = 'pdf';

        $arquivoDenunciaDefesaRepositoryMock = $this->createMock(ArquivoDenunciaDefesaRepository::class);
        $arquivoDenunciaDefesaRepositoryMock->method('find')->willReturn($arquivo);

        $arquivoServiceMock = $this->createMock(ArquivoService::class);
        $arquivoServiceMock->method('getCaminhoRepositorioDenuncia')->willReturn('/');
        $arquivoServiceMock->method('getArquivo')->willReturn($arquivoTO);

        $denunciaDefesaBO = new DenunciaDefesaBO();
        $this->setPrivateProperty($denunciaDefesaBO, 'arquivoDenunciaDefesaRepository', $arquivoDenunciaDefesaRepositoryMock);
        $this->setPrivateProperty($denunciaDefesaBO, 'arquivoService', $arquivoServiceMock);

        $this->assertNotEmpty($denunciaDefesaBO->getArquivo(1));
    }

    /**
     * testar método getArquivo sem sucesso
     *
     * @throws ReflectionException
     */
    public function testarGetArquivoSemSucesso()
    {
        $arquivo = new ArquivoDenunciaDefesa();
        $arquivo->setId(1);
        $arquivo->setTamanho(123333);
        $arquivo->setNomeFisicoArquivo('nome_123323.pdf');
        $arquivo->setNome('nome.pdf');

        $denunciaDefesa = new DenunciaDefesa();
        $denunciaDefesa->setId(1);
        $denunciaDefesa->setDescricaoDefesa('test test');
        $denunciaDefesa->setDataDefesa(Utils::getData());

        $denuncia = new Denuncia();
        $denuncia->setId(1);

        $denunciaDefesa->setDenuncia($denuncia);
        $arquivo->setDenunciaDefesa($denunciaDefesa);

        $e = new NegocioException();

        $arquivoDenunciaDefesaRepositoryMock = $this->createMock(ArquivoDenunciaDefesaRepository::class);
        $arquivoDenunciaDefesaRepositoryMock->method('find')->willReturn($arquivo);

        $arquivoServiceMock = $this->createMock(ArquivoService::class);
        $arquivoServiceMock->method('getCaminhoRepositorioDenuncia')->willReturn('/');
        $arquivoServiceMock->method('getArquivo')->willThrowException($e);

        $denunciaDefesaBO = new DenunciaDefesaBO();
        $this->setPrivateProperty($denunciaDefesaBO, 'arquivoDenunciaDefesaRepository', $arquivoDenunciaDefesaRepositoryMock);
        $this->setPrivateProperty($denunciaDefesaBO, 'arquivoService', $arquivoServiceMock);

        try {
            $this->assertNotEmpty($denunciaDefesaBO->getArquivo(1));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }
}