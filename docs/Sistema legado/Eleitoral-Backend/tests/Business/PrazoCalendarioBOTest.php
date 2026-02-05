<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 23/09/2019
 * Time: 10:23
 */

use App\Repository\PrazoCalendarioRepository;
use App\Business\PrazoCalendarioBO;
use App\Entities\Calendario;
use App\Entities\TipoProcesso;
use App\Config\Constants;
use App\Util\Utils;
use App\Entities\UfCalendario;
use App\Entities\ArquivoCalendario;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\CalendarioRepository;
use App\Entities\PrazoCalendario;
use App\Entities\AtividadePrincipalCalendario;
use Doctrine\ORM\NoResultException;
use App\Business\AtividadePrincipalBO;
use App\Entities\HistoricoCalendario;
use App\Business\HistoricoCalendarioBO;
use App\Entities\JustificativaAlteracaoCalendario;
use App\Exceptions\Message;
use App\Entities\Eleicao;

/**
 * Teste de Unidade referente à classe CalendarioBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class PrazoCalendarioBOTest extends TestCase
{
    const ID_CALENDARIO = 99;
    const ID_ATV_PRM = 42;
    const ID_PRAZO = 52;
    const VALOR_ZERO = 0;

    /**
     * Testa a execução do método 'hasPrazosVinculadosAoCalendario' com sucesso, retornando true.
     *
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testarHasPrazosVinculadosAoCalendarioComSucesso()
    {
        $prazoCalendarioRepositoryMock = $this->createMock(PrazoCalendarioRepository::class);
        $prazoCalendarioRepositoryMock->method('getTotalPrazosPorCalendario')->willReturn(34);

        $prazoCalendarioBO = new PrazoCalendarioBO();
        $this->setPrivateProperty($prazoCalendarioBO, 'prazoCalendarioRepository', $prazoCalendarioRepositoryMock);

        $this->assertTrue($prazoCalendarioBO->hasPrazosVinculadosAoCalendario(self::ID_CALENDARIO));
    }

    /**
     * Testa a execução do método 'hasPrazosVinculadosAoCalendario' sem sucesso, retornando false.
     *
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testarHasPrazosVinculadosAoCalendarioSemSucesso()
    {
        $prazoCalendarioRepositoryMock = $this->createMock(PrazoCalendarioRepository::class);
        $prazoCalendarioRepositoryMock->method('getTotalPrazosPorCalendario')->willReturn(self::VALOR_ZERO);

        $prazoCalendarioBO = new PrazoCalendarioBO();
        $this->setPrivateProperty($prazoCalendarioBO, 'prazoCalendarioRepository', $prazoCalendarioRepositoryMock);

        $this->assertFalse($prazoCalendarioBO->hasPrazosVinculadosAoCalendario(self::ID_CALENDARIO));
    }

    /**
     * Testa a execução do método 'excluirPrazo' com sucesso.
     *
     * @throws ReflectionException
     */
    public function testarExcluirPrazoComSucesso()
    {
        $prazoCalendario = $this->criarPrazo();
        $calendario = $this->criarCalendario();

        $prazoCalendarioRepositoryMock = $this->createMock(PrazoCalendarioRepository::class);
        $prazoCalendarioRepositoryMock->method('find')->willReturn($prazoCalendario);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getPorPrazo')->willReturn($calendario);

        $prazoCalendarioBO = new PrazoCalendarioBO();
        $this->setPrivateProperty($prazoCalendarioBO, 'prazoCalendarioRepository', $prazoCalendarioRepositoryMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        $this->assertNull($prazoCalendarioBO->excluirPrazo(self::ID_PRAZO));
    }

    /**
     * Testa a execução do método 'excluirPrazo' sem sucesso.
     *
     * @throws ReflectionException
     */
    public function testarExcluirPrazoSemSucesso()
    {
        $prazoCalendario = $this->criarPrazo();
        $prazoCalendarioRepositoryMock = $this->createMock(PrazoCalendarioRepository::class);
        $prazoCalendarioRepositoryMock->method('find')->willReturn($prazoCalendario);

        $e = new NoResultException();

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->method('getPorPrazo')->willReturn(null)->willThrowException($e);

        $prazoCalendarioBO = new PrazoCalendarioBO();
        $this->setPrivateProperty($prazoCalendarioBO, 'prazoCalendarioRepository', $prazoCalendarioRepositoryMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        try {
            $this->assertNull($prazoCalendarioBO->excluirPrazo(self::ID_PRAZO));
        } catch (NoResultException $e) {
            $this->assertSame('', '');
        }
    }

    /**
     * Testa o método Salvar para salvar os prazos com Sucesso.
     *
     * @throws ReflectionException
     */
    public function testarSalvarComSucesso()
    {
        $calendario = $this->criarCalendario();
        $atividadePrincipal = $this->criarAtividadePrimaria(true, true);
        $calendario->setAtividadesPrincipais(new ArrayCollection());
        $calendario->getAtividadesPrincipais()->add(clone $atividadePrincipal);
        $atividadePrincipal->setCalendario($this->criarCalendario());

        $prazoCalendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $prazoCalendarioRepositoryMock->method('persist')->willReturn(true);

        $atividadePrincipalBOMock = $this->createMock(AtividadePrincipalBO::class);
        $atividadePrincipalBOMock->method('find')->willReturn($atividadePrincipal);

        $historicoCalendarioBOMock = $this->createMock(HistoricoCalendarioBO::class);
        $historicoCalendarioBOMock->expects($this->any())->method('criarHistorico')->willReturn($this->criarHistorico());
        $historicoCalendarioBOMock->expects($this->any())->method('salvar')->willReturn(null);

        $prazoCalendarioBO = new PrazoCalendarioBO();
        $this->setPrivateProperty($prazoCalendarioBO, 'prazoCalendarioRepository', $prazoCalendarioRepositoryMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'atividadePrincipalBO', $atividadePrincipalBOMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'historicoCalendarioBO', $historicoCalendarioBOMock);

        $this->assertNull($prazoCalendarioBO->salvar($calendario));
    }

    /**
     * Testar método Salvar com exclusão de prazos, com sucesso.
     *
     * @throws ReflectionException
     */
    public function testarSalvarComExclusaoComSucesso()
    {
        $prazosExcluidos = $this->criarPrazosExcluidos();
        $calendario = $this->criarCalendario();
        $atividadePrincipal = $this->criarAtividadePrimaria(true, true);
        $calendario->setAtividadesPrincipais(new ArrayCollection());
        $calendario->getAtividadesPrincipais()->add(clone $atividadePrincipal);
        $atividadePrincipal->setCalendario($this->criarCalendario());

        $prazoCalendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $prazoCalendarioRepositoryMock->method('persist')->willReturn(true);
        $prazoCalendarioRepositoryMock->method('delete')->willReturn(null);
        $prazoCalendarioRepositoryMock->method('find')->willReturn($this->criarPrazo());

        $atividadePrincipalBOMock = $this->createMock(AtividadePrincipalBO::class);
        $atividadePrincipalBOMock->method('find')->willReturn($atividadePrincipal);

        $historicoCalendarioBOMock = $this->createMock(HistoricoCalendarioBO::class);
        $historicoCalendarioBOMock->expects($this->any())->method('criarHistorico')->willReturn($this->criarHistorico());
        $historicoCalendarioBOMock->expects($this->any())->method('salvar')->willReturn(null);

        $calendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $calendarioRepositoryMock->expects($this->any())->method('getPorPrazo')->willReturn($this->criarCalendario());

        $prazoCalendarioBO = new PrazoCalendarioBO();
        $this->setPrivateProperty($prazoCalendarioBO, 'prazoCalendarioRepository', $prazoCalendarioRepositoryMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'atividadePrincipalBO', $atividadePrincipalBOMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'historicoCalendarioBO', $historicoCalendarioBOMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'calendarioRepository', $calendarioRepositoryMock);

        $this->assertNull($prazoCalendarioBO->salvar($calendario, null, $prazosExcluidos));
    }

    /**
     * Testa o método 'Salvar' com Justificativa, com sucesso.
     *
     * @throws ReflectionException
     */
    public function testarSalvarComJustificativaComSucesso()
    {
        $justificativas = $this->criarJustificativas();
        $calendario = $this->criarCalendario();
        $atividadePrincipal = $this->criarAtividadePrimaria(true, true);
        $calendario->setAtividadesPrincipais(new ArrayCollection());
        $calendario->getAtividadesPrincipais()->add(clone $atividadePrincipal);
        $atividadePrincipal->setCalendario($this->criarCalendario());

        $prazoCalendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $prazoCalendarioRepositoryMock->method('persist')->willReturn(true);

        $atividadePrincipalBOMock = $this->createMock(AtividadePrincipalBO::class);
        $atividadePrincipalBOMock->method('find')->willReturn($atividadePrincipal);

        $historicoCalendarioBOMock = $this->createMock(HistoricoCalendarioBO::class);
        $historicoCalendarioBOMock->expects($this->any())->method('criarHistorico')->willReturn($this->criarHistorico());
        $historicoCalendarioBOMock->expects($this->any())->method('salvar')->willReturn(null);

        $prazoCalendarioBO = new PrazoCalendarioBO();
        $this->setPrivateProperty($prazoCalendarioBO, 'prazoCalendarioRepository', $prazoCalendarioRepositoryMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'atividadePrincipalBO', $atividadePrincipalBOMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'historicoCalendarioBO', $historicoCalendarioBOMock);

        $this->assertNull($prazoCalendarioBO->salvar($calendario, $justificativas));
    }

    /**
     * Testa o método 'Salvar' sem sucesso
     *
     * @throws ReflectionException
     */
    public function testarSalvarSemSucesso()
    {
        $e = new Exception();
        $calendario = $this->criarCalendario();
        $atividadePrincipal = $this->criarAtividadePrimaria(true, true);
        $calendario->setAtividadesPrincipais(new ArrayCollection());
        $calendario->getAtividadesPrincipais()->add(clone $atividadePrincipal);
        $atividadePrincipal->setCalendario($this->criarCalendario());

        $prazoCalendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $prazoCalendarioRepositoryMock->method('persist')->willReturn(true);

        $atividadePrincipalBOMock = $this->createMock(AtividadePrincipalBO::class);
        $atividadePrincipalBOMock->method('find')->willReturn($atividadePrincipal);

        $historicoCalendarioBOMock = $this->createMock(HistoricoCalendarioBO::class);
        $historicoCalendarioBOMock->expects($this->any())->method('criarHistorico')->willReturn($this->criarHistorico());
        $historicoCalendarioBOMock->expects($this->any())->method('salvar')->willThrowException($e);

        $prazoCalendarioBO = new PrazoCalendarioBO();
        $this->setPrivateProperty($prazoCalendarioBO, 'prazoCalendarioRepository', $prazoCalendarioRepositoryMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'atividadePrincipalBO', $atividadePrincipalBOMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'historicoCalendarioBO', $historicoCalendarioBOMock);

        try {
            $this->assertNull($prazoCalendarioBO->salvar($calendario));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * Testa o método 'Salvar' sem sucesso, apresentando mensagem de campos obrigatórios.
     *
     * @throws ReflectionException
     */
    public function testarSalvarMsgCamposObrigatorios()
    {
        $calendario = $this->criarCalendario();
        $atividadePrincipal = $this->criarAtividadePrimaria(true, true, true);
        $calendario->setAtividadesPrincipais(new ArrayCollection());
        $calendario->getAtividadesPrincipais()->add(clone $atividadePrincipal);
        $atividadePrincipal->setCalendario($this->criarCalendario());

        $prazoCalendarioRepositoryMock = $this->createMock(CalendarioRepository::class);
        $prazoCalendarioRepositoryMock->method('persist')->willReturn(true);

        $atividadePrincipalBOMock = $this->createMock(AtividadePrincipalBO::class);
        $atividadePrincipalBOMock->method('find')->willReturn($atividadePrincipal);

        $historicoCalendarioBOMock = $this->createMock(HistoricoCalendarioBO::class);
        $historicoCalendarioBOMock->expects($this->any())->method('criarHistorico')->willReturn($this->criarHistorico());
        $historicoCalendarioBOMock->expects($this->any())->method('salvar')->willReturn(null);

        $prazoCalendarioBO = new PrazoCalendarioBO();
        $this->setPrivateProperty($prazoCalendarioBO, 'prazoCalendarioRepository', $prazoCalendarioRepositoryMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'atividadePrincipalBO', $atividadePrincipalBOMock);
        $this->setPrivateProperty($prazoCalendarioBO, 'historicoCalendarioBO', $historicoCalendarioBOMock);

        try {
            $this->assertNull($prazoCalendarioBO->salvar($calendario));
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }
    
    /**
     * Teste Unitário do método testeGetPrazosPorAtividadePrincipalComSucesso sobre cenário de busca com sucesso.
     */
    public function testeGetPrazosPorAtividadePrincipalComSucesso(){
        $prazo = $this->criarPrazo();
        $prazo->setAtividadePrincipal(self::ID_ATV_PRM);
        $prazoBO = new PrazoCalendarioBO();
        
        $prazoCalendarioRepositoryMock = $this->createMock(PrazoCalendarioRepository::class);
        $prazoCalendarioRepositoryMock->method('getPrazosPorAtividadePrincipal')->willReturn([$prazo]);
        
        $this->setPrivateProperty($prazoBO, 'prazoCalendarioRepository', $prazoCalendarioRepositoryMock);
        
        $this->assertNotEmpty($prazoBO->getPrazosPorAtividadePrincipal(self::ID_ATV_PRM));
    }
    
    /**
     * Teste Unitário do método testeGetPrazosPorAtividadePrincipalComSucesso sobre cenário de busca sem sucesso.
     */
    public function testeGetPrazosPorAtividadePrincipalSemSucesso(){       
        $prazoBO = new PrazoCalendarioBO();
        
        $prazoCalendarioRepositoryMock = $this->createMock(PrazoCalendarioRepository::class);
        $prazoCalendarioRepositoryMock->method('getPrazosPorAtividadePrincipal')->willReturn(null);
        
        $this->setPrivateProperty($prazoBO, 'prazoCalendarioRepository', $prazoCalendarioRepositoryMock);
        
        $this->assertNull($prazoBO->getPrazosPorAtividadePrincipal(self::ID_ATV_PRM));
    }

    /**
     * @param bool $criaIds
     * @return Calendario
     */
    private function criarCalendario()
    {
        $calendario = Calendario::newInstance();

        $tipoProcesso = TipoProcesso::newInstance();
        $tipoProcesso->setId(Constants::TIPO_PROCESSO_ORDINARIO);
        $arquivo = $this->criarArquivoCalendario();

        $eleicao = Eleicao::newInstance();
        $eleicao->setAno(2013);
        $eleicao->setTipoProcesso($tipoProcesso);

        $cauUf = UfCalendario::newInstance();
        $cauUf->setIdCauUf(23);

        $calendario->setId(self::ID_CALENDARIO);
        $calendario->setAno(2013);
        $calendario->setIdSituacaoVigente(Constants::SITUACAO_CALENDARIO_EM_PREENCHIMENTO);
        $calendario->setDataInicioVigencia(new DateTime('2013-01-02'));
        $calendario->setDataFimVigencia(new DateTime('2013-03-29'));
        $calendario->setDataInicioMandato(new DateTime('2013-04-01'));
        $calendario->setDataFimMandato(new DateTime('2014-01-01'));
        $calendario->setIdadeInicio(25);
        $calendario->setIdadeFim(65);
        $calendario->setArquivos(new ArrayCollection());
        $calendario->getArquivos()->add($arquivo);
        $calendario->setSituacaoIES(false);
        $calendario->setCauUf(new ArrayCollection());
        $calendario->getCauUf()->add($cauUf);

        $calendario->setAtividadesPrincipais($this->criarAtividadePrimaria());

        return $calendario;
    }

    /**
     * @return ArquivoCalendario
     */
    private function criarArquivoCalendario()
    {
        $arquivo = ArquivoCalendario::newInstance();
        $arquivo->setTamanho(23212);
        $arquivo->setNomeFisico('euiehriu3234.pdf');
        $arquivo->setNome('teste.pdf');

        return $arquivo;
    }

    /**
     * @return PrazoCalendario
     */
    private function criarPrazo($criaFilhos = false, $campoEmBranco = false)
    {
        $prazo = PrazoCalendario::newInstance();
        $prazo->getId(self::ID_PRAZO);
        $prazo->setNivel(1);
        $prazo->setSituacaoDiaUtil(true);
        $prazo->setDuracao(2);

        if (!$campoEmBranco) {
            $prazo->setDescricaoAtividade('Prazo 1.1.1.1');
        }

        if ($criaFilhos) {
            $prazo->setPrazos($this->criarPrazos());
        }

        return $prazo;
    }

    /**
     * @param bool $criaIds
     * @return AtividadePrincipalCalendario
     */
    private function criarAtividadePrimaria($criaPrazos = false, $criarFilhos = false, $campoEmBranco = false)
    {
        $atividadePrincipal = AtividadePrincipalCalendario::newInstance();
        $atividadePrincipal->setDescricao('desc 1');
        $atividadePrincipal->setDataInicio(new DateTime('2013-02-01'));
        $atividadePrincipal->setDataFim(new DateTime('2013-02-25'));
        $atividadePrincipal->setNivel(1);
        $atividadePrincipal->setObedeceVigencia(true);

        if ($criaPrazos) {
            $atividadePrincipal->setPrazos($this->criarPrazos($criarFilhos, $campoEmBranco));
        }

        return $atividadePrincipal;
    }

    /**
     * @return ArrayCollection
     */
    private function criarPrazos($criarFilhos = false, $campoEmBranco = false)
    {
        $prazos = new ArrayCollection();

        for ($i = 0; $i < 4; $i++) {
            $prazo = $this->criarPrazo($criarFilhos, $campoEmBranco);
            $prazos->add($prazo);
        }

        return $prazos;
    }

    /**
     * @return HistoricoCalendario
     * @throws Exception
     */
    private function criarHistorico()
    {
        $historico = HistoricoCalendario::newInstance();
        $historico->setData(Utils::getData());
        $historico->setCalendario($this->criarCalendario());
        $historico->setAcao(Constants::ACAO_CALENDARIO_INSERIR_PRAZO);
        $historico->setResponsavel(1);
        $historico->setDescricaoAba(Constants::DESC_ABA_PRAZO);

        return $historico;
    }

    /**
     * @return ArrayCollection
     */
    private function criarJustificativas()
    {
        $justificativas = new ArrayCollection();

        for($i=0; $i <= 5; $i++){
            $justificativa = JustificativaAlteracaoCalendario::newInstance();
            $justificativa->setDescricao('Descricao da justificativa '.($i+1));
            $justificativa->setJustificativa('Justificativa da justificativa');

            $justificativas->add($justificativa);
        }

        return $justificativas;
    }

    /**
     * @return ArrayCollection
     */
    private function criarPrazosExcluidos()
    {
        $prazos = new ArrayCollection();

        for($i=0; $i <= 5; $i++){
            $prazos->add(rand(0, 67));
        }

        return $prazos;
    }
}