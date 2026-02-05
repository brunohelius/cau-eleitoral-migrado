<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 09/01/2020
 * Time: 09:01
 */

use App\Config\Constants;
use App\Util\Utils;
use App\Entities\HistoricoExtratoConselheiro;
use App\Entities\ParametroConselheiro;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\HistoricoExtratoConselheiroRepository;
use App\Service\ArquivoService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Http\Request;
use App\Business\HistoricoExtratoConselheiroBO;
use App\Factory\PDFFActory;
use App\Factory\XLSFactory;

/**
 * Teste de Unidade referente à classe HistoricoExtratoConselheiroBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class HistoricoExtratoConselheiroBOTest extends TestCase
{

    public function testarSalvarComSucesso()
    {
        $historicoExtratoConselheiro = $this->criarHistorico();

        $historicoExtratoConselheiroRepositoryMock = $this->createMock(HistoricoExtratoConselheiroRepository::class);
        $historicoExtratoConselheiroRepositoryMock->method('persist')->willReturn(true);

        $historicoExtratoConselheiroBO = HistoricoExtratoConselheiroBO::newInstance();
        $this->setPrivateProperty($historicoExtratoConselheiroBO, 'historicoExtratoConselheiroRepository', $historicoExtratoConselheiroRepositoryMock);

        $this->assertNotEmpty($historicoExtratoConselheiroBO->salvar($historicoExtratoConselheiro));
    }


    public function testarSalvarSemSucesso()
    {
        $historicoExtratoConselheiro = $this->criarHistorico();

        $historicoExtratoConselheiroRepositoryMock = $this->createMock(HistoricoExtratoConselheiroRepository::class);
        $historicoExtratoConselheiroRepositoryMock->method('persist')->willReturn(false);

        $historicoExtratoConselheiroBO = HistoricoExtratoConselheiroBO::newInstance();
        $this->setPrivateProperty($historicoExtratoConselheiroBO, 'historicoExtratoConselheiroRepository', $historicoExtratoConselheiroRepositoryMock);

        $this->assertEmpty($historicoExtratoConselheiroBO->salvar($historicoExtratoConselheiro));
    }

    public function testarGerarDocumentoListaConselheirosComSucesso()
    {
        $pdfFactoryMock = $this->createMock(PDFFActory::class);
        $pdfFactoryMock->method('gerarDocumentoListaConselheiros')->willReturn(true);

        $historicoExtratoConselheiroRepositoryMock = $this->createMock(HistoricoExtratoConselheiroRepository::class);
        $historicoExtratoConselheiroRepositoryMock->method('find')->willReturn($this->criarHistorico());

        $historicoExtratoConselheiroBO = HistoricoExtratoConselheiroBO::newInstance();
        $this->setPrivateProperty($historicoExtratoConselheiroBO, 'historicoExtratoConselheiroRepository', $historicoExtratoConselheiroRepositoryMock);
        $this->setPrivateProperty($historicoExtratoConselheiroBO, 'pdfFactory', $pdfFactoryMock);

        $this->assertNotEmpty($historicoExtratoConselheiroBO->gerarDocumentoListaConselheiros(1));
    }

    public function testarGerarDocumentoListaConselheirosSemSucesso()
    {
        $pdfFactoryMock = $this->createMock(PDFFActory::class);
        $pdfFactoryMock->method('gerarDocumentoListaConselheiros')->willReturn(null);

        $historicoExtratoConselheiroRepositoryMock = $this->createMock(HistoricoExtratoConselheiroRepository::class);
        $historicoExtratoConselheiroRepositoryMock->method('find')->willReturn($this->criarHistorico());

        $historicoExtratoConselheiroBO = HistoricoExtratoConselheiroBO::newInstance();
        $this->setPrivateProperty($historicoExtratoConselheiroBO, 'historicoExtratoConselheiroRepository', $historicoExtratoConselheiroRepositoryMock);
        $this->setPrivateProperty($historicoExtratoConselheiroBO, 'pdfFactory', $pdfFactoryMock);

        $this->assertEmpty($historicoExtratoConselheiroBO->gerarDocumentoListaConselheiros(1));
    }


    public function testarGerarDocumentoXSLListaConselheirosComSucesso()
    {
        $xlsFactoryMock = $this->createMock(XLSFactory::class);
        $xlsFactoryMock->method('gerarDocumentoListaConselheiros')->willReturn(true);

        $historicoExtratoConselheiroRepositoryMock = $this->createMock(HistoricoExtratoConselheiroRepository::class);
        $historicoExtratoConselheiroRepositoryMock->method('find')->willReturn($this->criarHistorico());

        $historicoExtratoConselheiroBO = HistoricoExtratoConselheiroBO::newInstance();
        $this->setPrivateProperty($historicoExtratoConselheiroBO, 'historicoExtratoConselheiroRepository', $historicoExtratoConselheiroRepositoryMock);
        $this->setPrivateProperty($historicoExtratoConselheiroBO, 'xlsFactory', $xlsFactoryMock);

        $this->assertNotEmpty($historicoExtratoConselheiroBO->gerarDocumentoXSLListaConselheiros(1));
    }

    public function testarGerarDocumentoXSLListaConselheirosSemSucesso()
    {
        $xlsFactoryMock = $this->createMock(XLSFactory::class);
        $xlsFactoryMock->method('gerarDocumentoListaConselheiros')->willReturn(true);

        $historicoExtratoConselheiroRepositoryMock = $this->createMock(HistoricoExtratoConselheiroRepository::class);
        $historicoExtratoConselheiroRepositoryMock->method('find')->willReturn($this->criarHistorico());

        $historicoExtratoConselheiroBO = HistoricoExtratoConselheiroBO::newInstance();
        $this->setPrivateProperty($historicoExtratoConselheiroBO, 'historicoExtratoConselheiroRepository', $historicoExtratoConselheiroRepositoryMock);
        $this->setPrivateProperty($historicoExtratoConselheiroBO, 'xlsFactory', $xlsFactoryMock);

        $this->assertEmpty($historicoExtratoConselheiroBO->gerarDocumentoXSLListaConselheiros(1));
    }

    
    private function criarHistorico()
    {
        $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance();
        $atividadeSecundaria->setId(1);

        $jsonDados = '{"detalhada":[{"nome":"teste1","cpf":"23423423443","registroRegional":"3343","dataFim":"2020-10-02","descricao":"teste","cauUf":213},{"nome":"teste2","cpf":"32132132112","registroRegional":"5555","dataFim":"","descricao":"teste2","cauUf":12},{"nome":"teste3","cpf":"12312312323","registroRegional":"5554","dataFim":"2021-01-01","descricao":"teste3","cauUf":312}],"totalProfAtivos":"321453","totalProf":"500431"}';

        $historico = HistoricoExtratoConselheiro::newInstance();
        $historico->setDataHistorico(Utils::getData());
        $historico->setDescricao('teste1');
        $historico->setAcao(Constants::HISTORICO_ACAO_INSERIR);
        $historico->setResponsavel(Constants::ID_USUARIO_MOCK);
        $historico->setJsonDados($jsonDados);
        $historico->setAtividadeSecundaria($atividadeSecundaria);

        return $historico;
    }
}