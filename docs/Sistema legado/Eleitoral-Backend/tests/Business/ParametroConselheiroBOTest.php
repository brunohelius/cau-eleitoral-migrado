<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 25/11/2019
 * Time: 10:36
 */

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Http\Request;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ParametroConselheiro;
use App\Service\CorporativoService;
use App\Entities\HistoricoExtratoConselheiro;
use App\Repository\ParametroConselheiroRepository;
use App\Repository\AtividadeSecundariaCalendarioRepository;
use App\Entities\Lei;
use App\Business\ParametroConselheiroBO;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Entities\AtividadePrincipalCalendario;
use App\Business\HistoricoBO;
use App\Entities\Historico;
use App\Business\HistoricoExtratoConselheiroBO;
use App\Business\AtividadeSecundariaCalendarioBO;
use App\Repository\CalendarioRepository;
use App\Entities\Eleicao;
use App\Entities\Calendario;

/**
 * Teste de Unidade referente à classe ParametroConselheiroBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class ParametroConselheiroBOTest extends TestCase
{
    const ID_PARAM_CONSELHEIRO = 1;
    const ID_ATV_PRINCIPAL = 1;
    const ID_ATV_SECUNDARIA = 1;
    const idsCAUUF = array(12, 213, 134, 22, 66);

    /**
     * Testa a execução do método 'getParametroConselheiroPorFiltro', com sucesso, retornando o parametroConselheiro
     *
     * @throws ReflectionException
     * @throws \App\Exceptions\NegocioException
     */
    public function testarGetParametroConselheiroPorFiltroComSucesso()
    {
        $filtroTO = new \stdClass();
        $filtroTO->idAtividadeSecundaria = self::ID_ATV_SECUNDARIA;
        $filtroTO->idsCauUf = self::idsCAUUF;

        $paramConselheiro = $this->criarParametroConselheiro();

        $parametroConselheiroRepositoryMock = $this->createMock(ParametroConselheiroRepository::class);
        $parametroConselheiroRepositoryMock->method('getParametroConselheiroPorFiltro')->willReturn($paramConselheiro);

        $paramConselheiroBO = new ParametroConselheiroBO();
        $this->setPrivateProperty($paramConselheiroBO, 'parametroConselheiroRepository', $parametroConselheiroRepositoryMock);

        $this->assertNotEmpty($paramConselheiroBO->getParametroConselheiroPorFiltro($filtroTO));
    }

    /**
     * Testa a execução do método 'getParametroConselheiroPorFiltro', sem sucesso, retornando NegocioException
     *
     * @throws ReflectionException
     */
    public function testarGetParametroConselheiroPorFiltroSemSucesso()
    {
        $filtroTO = new \stdClass();
        $filtroTO->idAtividadeSecundaria = self::ID_ATV_SECUNDARIA;
        $filtroTO->idsCauUf = self::idsCAUUF;

        $parametroConselheiroRepositoryMock = $this->createMock(ParametroConselheiroRepository::class);
        $parametroConselheiroRepositoryMock->method('getParametroConselheiroPorFiltro')->willReturn(null);

        $paramConselheiroBO = new ParametroConselheiroBO();
        $this->setPrivateProperty($paramConselheiroBO, 'parametroConselheiroRepository', $parametroConselheiroRepositoryMock);
        try {
            $this->assertNotEmpty($paramConselheiroBO->getParametroConselheiroPorFiltro($filtroTO));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::NENHUM_MEMBRO_ENCONTRADO]);
        }
    }

    /**
     * Testar o método Salvar, com sucesso
     *
     * @throws NegocioException
     * @throws ReflectionException
     */
    public function testarSalvarComSucesso()
    {
        $dados = $this->criarListaParaConselheiro();
        $request = new Request();
        $justificativa = "Texto justificativa teste";

        $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance();
        $atividadeSecundaria->setId(self::ID_ATV_SECUNDARIA);
        $atividadeSecundaria->setNivel(1);

        $paramConselheiroBD = $this->criarParametroConselheiro();
        $atividadeSecundaria = $this->criarAtividadeSecundaria($atividadeSecundaria, 6);

        $parametroConselheiroRepositoryMock = $this->createMock(ParametroConselheiroRepository::class);
        $parametroConselheiroRepositoryMock->method('find')->willReturn($paramConselheiroBD);
        $parametroConselheiroRepositoryMock->method('persist')->willReturn(true);

        $atividadeSecundariaCalendarioRepositoryMock = $this->createMock(AtividadeSecundariaCalendarioRepository::class);
        $atividadeSecundariaCalendarioRepositoryMock->method('find')->willReturn($atividadeSecundaria);

        $historicoBOMock = $this->createMock(HistoricoBO::class);
        $historicoBOMock->method('criarHistorico')->willReturn($this->criarHistorico());
        $historicoBOMock->method('salvar')->willReturn(true);

        $paramConselheiroBO = new ParametroConselheiroBO();
        $this->setPrivateProperty($paramConselheiroBO, 'parametroConselheiroRepository', $parametroConselheiroRepositoryMock);
        $this->setPrivateProperty($paramConselheiroBO, 'atividadeSecundariaCalendarioRepository', $atividadeSecundariaCalendarioRepositoryMock);
        $this->setPrivateProperty($paramConselheiroBO, 'historicoBO', $historicoBOMock);

        $this->assertEmpty($paramConselheiroBO->salvar($dados, $request, $justificativa));
    }

    /**
     * Testar o método Salvar, sem sucesso com Exception no salvar
     *
     * @throws ReflectionException
     */
    public function testarSalvarSemSucesso()
    {
        $dados = $this->criarListaParaConselheiro();
        $request = new Request();
        $justificativa = "Texto justificativa teste";
        $e = new \Exception();

        $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance();
        $atividadeSecundaria->setId(self::ID_ATV_SECUNDARIA);
        $atividadeSecundaria->setNivel(1);

        $paramConselheiroBD = $this->criarParametroConselheiro();
        $atividadeSecundaria = $this->criarAtividadeSecundaria($atividadeSecundaria, 6);

        $parametroConselheiroRepositoryMock = $this->createMock(ParametroConselheiroRepository::class);
        $parametroConselheiroRepositoryMock->method('find')->willReturn($paramConselheiroBD);
        $parametroConselheiroRepositoryMock->method('persist')->willThrowException($e);

        $paramConselheiroBO = new ParametroConselheiroBO();
        $this->setPrivateProperty($paramConselheiroBO, 'parametroConselheiroRepository', $parametroConselheiroRepositoryMock);

        try {
            $this->assertNotEmpty($paramConselheiroBO->salvar($dados, $request, $justificativa));
        }  catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testar o método Salvar, sem sucesso com os dados vazios e mensagem de campos obrigatórios
     */
    public function testarSalvarSemSucessoCampoObrigatorio()
    {
        $dados = null;
        $request = new Request();
        $justificativa = "Texto justificativa teste";

        $paramConselheiroBO = new ParametroConselheiroBO();

        try {
            $this->assertNotEmpty($paramConselheiroBO->salvar($dados, $request, $justificativa));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Testar o método getParametroConselheiroService, com sucesso
     *
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testarGetParametroConselheiroServiceComSucesso()
    {
        $dadosTO = new \stdClass();
        $dadosTO->idAtividadeSecundaria = self::ID_ATV_PRINCIPAL;

        $eleicao = Eleicao::newInstance();
        $eleicao->setId(1);
        $eleicao->setAno(2019);
        $eleicao->setSequenciaAno(3);

        $calendario = Calendario::newInstance();
        $calendario->setId(1);
        $calendario->setEleicao($eleicao);

        $paramConselheiroBD = $this->criarParametroConselheiro();

        $atividadePrincipal = AtividadePrincipalCalendario::newInstance();
        $atividadePrincipal->setId(self::ID_ATV_PRINCIPAL);
        $atividadeSecundaria = $this->criarAtividadeSecundaria($atividadePrincipal,6);

        $corporativoServiceMock = $this->createMock(CorporativoService::class);
        $corporativoServiceMock->method('getQtdProfissionaisPorUf')->willReturn($this->criarListaProfissionalCorporativo());
        $corporativoServiceMock->method('getFiliais')->willReturn($this->criarFiliais());

        $parametroConselheiroRepositoryMock = $this->createMock(ParametroConselheiroRepository::class);
        $parametroConselheiroRepositoryMock->method('getParametroConselheiroPorFiltro')->willReturn(null);
        $parametroConselheiroRepositoryMock->method('find')->willReturn($paramConselheiroBD);
        $parametroConselheiroRepositoryMock->method('persist')->willReturn(true);

        $historicoExtratoConselheiroBOMock = $this->createMock(HistoricoExtratoConselheiroBO::class);
        $historicoExtratoConselheiroBOMock->method('getNumeroHistoricoExtratoPorAtvSec')->willReturn(3);
        $historicoExtratoConselheiroBOMock->method('criarHistorico')->willReturn($this->criarHistoricoExtrato($atividadeSecundaria));
        $historicoExtratoConselheiroBOMock->method('salvar')->willReturn(true);

        $atividadeSecundariaBOMock = $this->createMock(AtividadeSecundariaCalendarioBO::class);
        $atividadeSecundariaBOMock->method('getPorId')->willReturn($atividadeSecundaria);

        $atividadeSecundariaCalendarioRepositoryMock = $this->createMock(AtividadeSecundariaCalendarioRepository::class);
        $atividadeSecundariaCalendarioRepositoryMock->method('find')->willReturn($atividadeSecundaria);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getPorAtividadeSecundaria')->willReturn($calendario);

        $historicoBOMock = $this->createMock(HistoricoBO::class);
        $historicoBOMock->method('criarHistorico')->willReturn($this->criarHistorico());
        $historicoBOMock->method('salvar')->willReturn(true);

        $paramConselheiroBO = new ParametroConselheiroBO();
        $this->setPrivateProperty($paramConselheiroBO, 'corporativoService', $corporativoServiceMock);
        $this->setPrivateProperty($paramConselheiroBO, 'parametroConselheiroRepository', $parametroConselheiroRepositoryMock);
        $this->setPrivateProperty($paramConselheiroBO, 'historicoExtratoConselheiroBO', $historicoExtratoConselheiroBOMock);
        $this->setPrivateProperty($paramConselheiroBO, 'atividadeSecundariaBO', $atividadeSecundariaBOMock);
        $this->setPrivateProperty($paramConselheiroBO, 'atividadeSecundariaCalendarioRepository', $atividadeSecundariaCalendarioRepositoryMock);
        $this->setPrivateProperty($paramConselheiroBO, 'historicoBO', $historicoBOMock);
        $this->setPrivateProperty($paramConselheiroBO, 'calendarioRepository', $calendarioRepositoryMock);

        $this->assertNotEmpty($paramConselheiroBO->atualizarNumeroConselheiros($dadosTO));
    }

    /**
     * Testar o método getParametroConselheiroService, sem sucesso lançando uma Exception
     *
     * @throws ReflectionException
     */
    public function testarGetParametroConselheiroServiceSemSucesso()
    {
        $dadosTO = new \stdClass();
        $dadosTO->idAtividadeSecundaria = self::ID_ATV_PRINCIPAL;

        $e = new Exception();

        $paramConselheiroBD = $this->criarParametroConselheiro();

        $atividadePrincipal = AtividadePrincipalCalendario::newInstance();
        $atividadePrincipal->setId(self::ID_ATV_PRINCIPAL);
        $atividadeSecundaria = $this->criarAtividadeSecundaria($atividadePrincipal,6);

        $corporativoServiceMock = $this->createMock(CorporativoService::class);
        $corporativoServiceMock->method('getQtdProfissionaisPorUf')->willReturn($this->criarListaProfissionalCorporativo());
        $corporativoServiceMock->method('getFiliais')->willReturn($this->criarFiliais());

        $parametroConselheiroRepositoryMock = $this->createMock(ParametroConselheiroRepository::class);
        $parametroConselheiroRepositoryMock->method('getParametroConselheiroPorFiltro')->willReturn(null);
        $parametroConselheiroRepositoryMock->method('find')->willReturn($paramConselheiroBD);
        $parametroConselheiroRepositoryMock->method('persist')->willThrowException($e);

        $historicoExtratoConselheiroBOMock = $this->createMock(HistoricoExtratoConselheiroBO::class);
        $historicoExtratoConselheiroBOMock->method('getNumeroHistoricoExtratoPorAtvSec')->willReturn(3);

        $atividadeSecundariaBOMock = $this->createMock(AtividadeSecundariaCalendarioBO::class);
        $atividadeSecundariaBOMock->method('getPorId')->willReturn($atividadeSecundaria);

        $atividadeSecundariaCalendarioRepositoryMock = $this->createMock(AtividadeSecundariaCalendarioRepository::class);
        $atividadeSecundariaCalendarioRepositoryMock->method('find')->willReturn($atividadeSecundaria);

        $paramConselheiroBO = new ParametroConselheiroBO();
        $this->setPrivateProperty($paramConselheiroBO, 'corporativoService', $corporativoServiceMock);
        $this->setPrivateProperty($paramConselheiroBO, 'parametroConselheiroRepository', $parametroConselheiroRepositoryMock);
        $this->setPrivateProperty($paramConselheiroBO, 'historicoExtratoConselheiroBO', $historicoExtratoConselheiroBOMock);
        $this->setPrivateProperty($paramConselheiroBO, 'atividadeSecundariaBO', $atividadeSecundariaBOMock);
        $this->setPrivateProperty($paramConselheiroBO, 'atividadeSecundariaCalendarioRepository', $atividadeSecundariaCalendarioRepositoryMock);

        try {
            $this->assertNotEmpty($paramConselheiroBO->atualizarNumeroConselheiros($dadosTO));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Cria uma instancia de ParametroConselheiro
     *
     * @return ParametroConselheiro
     */
    private function criarParametroConselheiro()
    {
        $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance();
        $atividadeSecundaria->setId(self::ID_ATV_SECUNDARIA);

        $lei = Lei::newInstance();
        $lei->setId(Constants::INCISO_I);
        $lei->setDescricao(Constants::$incisosLei[Constants::INCISO_I]);

        $paramConselheiro = ParametroConselheiro::newInstance();
        $paramConselheiro->setId(self::ID_PARAM_CONSELHEIRO);
        $paramConselheiro->setAtividadeSecundaria($atividadeSecundaria);
        $paramConselheiro->setQtdProfissional(123);
        $paramConselheiro->setNumeroProporcaoConselheiro(5);
        $paramConselheiro->setSituacaoEditado(false);
        $paramConselheiro->setLei($lei);
        $paramConselheiro->setIdCauUf(self::idsCAUUF);

        return $paramConselheiro;
    }

    /**
     * Retorna uma lista de ParametroConselheiro
     *
     * @return ArrayCollection
     */
    private function criarListaParaConselheiro()
    {
        $listaParamConselheiro = new ArrayCollection();

        for($i=0; $i<6; $i++){
            $paramConselheiro = $this->criarParametroConselheiro();
            $listaParamConselheiro->add($paramConselheiro);
        }
        return $listaParamConselheiro;
    }

    /**
     * Retorna uma lista de Atividades Secundarias
     *
     * @return ArrayCollection
     * @throws Exception
     */
    private function criarListaAtividadeSecundaria()
    {
        $atividadePrincipal = AtividadePrincipalCalendario::newInstance();
        $atividadePrincipal->setId(self::ID_ATV_PRINCIPAL);
        $atividadePrincipal->setNivel(1);

        $listaAtividades = new ArrayCollection();
        for($i=0; $i<8; $i++){
            $listaAtividades->add($this->criarAtividadeSecundaria($atividadePrincipal, $i+1));
        }

        return $listaAtividades;
    }

    /**
     * Retorna uma instancia de Atividade Secundaria
     *
     * @param $atividadePrincipal
     * @param int $nivel
     * @return AtividadeSecundariaCalendario
     * @throws Exception
     */
    private function criarAtividadeSecundaria($atividadePrincipal, $nivel = 1)
    {
        $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance();
        $atividadeSecundaria->setId(self::ID_ATV_SECUNDARIA);
        $atividadeSecundaria->setNivel($nivel);
        $atividadeSecundaria->setDataInicio(Utils::getData());
        $atividadeSecundaria->setAtividadePrincipalCalendario($atividadePrincipal);

        return $atividadeSecundaria;
    }

    /**
     * Retorna uma instancia de Historico
     *
     * @return Historico
     * @throws Exception
     */
    private function criarHistorico()
    {
        $historico = Historico::newInstance();
        $historico->setDataHistorico(Utils::getData());
        $historico->setAcao(Constants::HISTORICO_ACAO_INSERIR);
        $historico->setDescricao(Constants::PARAM_CONSELHEIRO_DESC_ALT_REALIZADA);
        $historico->setResponsavel(Constants::ID_USUARIO_MOCK);
        $historico->setTipoHistorico(Constants::HISTORICO_TIPO_REFERENCIA_PARAMETRO_CONSELHEIRO);
        $historico->setIdReferencia(self::ID_ATV_SECUNDARIA);
        $historico->setJustificativa('Texto justificativa teste');

        return $historico;
    }

    /**
     * Retorna uma lista de profissionais
     *
     * @return ArrayCollection
     */
    private function criarListaProfissionalCorporativo()
    {
        $listaProfissional = new ArrayCollection();

        for ($i = 0; $i < 7; $i++) {
            $profissionalTO = array();
            $profissionalTO['qtdProfissional'] = rand(100, 50000);
            $profissionalTO['idCauUf'] = array('GO', 'SP', 'AC');
            $listaProfissional->add($profissionalTO);
        }

        return $listaProfissional;
    }

    /**
     * Retorna a lista de filiais
     *
     * @return array
     */
    private function criarFiliais()
    {
        $lista = array();

        $cauUf1 = new stdClass();
        $cauUf2 = new stdClass();
        $cauUf3 = new stdClass();

        $cauUf1->prefixo = 'GO';
        $cauUf2->prefixo = 'AC';
        $cauUf3->prefixo = 'SP';

        $cauUf1->id = 213;
        $cauUf2->id = 134;
        $cauUf3->id = 22;

        $lista[] = $cauUf1;
        $lista[] = $cauUf2;
        $lista[] = $cauUf3;

        return $lista;
    }

    /**
     * Retorna uma instancia de HistoricoExtratoConselheiro
     *
     * @param $atividadeSecundaria
     * @return HistoricoExtratoConselheiro
     * @throws Exception
     */
    private function criarHistoricoExtrato($atividadeSecundaria)
    {
        $historico = HistoricoExtratoConselheiro::newInstance();
        $historico->setDataHistorico(Utils::getData());
        $historico->setDescricao(str_pad(3, 3, "0", STR_PAD_LEFT) . '_Extrato Conselheiros');
        $historico->setAcao(Constants::HISTORICO_ACAO_INSERIR);
        $historico->setResponsavel(Constants::ID_USUARIO_MOCK);
        $historico->setJsonDados('dadosJson');
        $historico->setAtividadeSecundaria($atividadeSecundaria);

        return $historico;
    }
}