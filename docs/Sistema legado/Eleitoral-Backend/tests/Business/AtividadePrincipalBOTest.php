<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 12/09/2019
 * Time: 17:14
 */

use App\Business\AtividadePrincipalBO;
use App\Config\Constants;
use App\Entities\AtividadePrincipalCalendario;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Calendario;
use App\Entities\CalendarioSituacao;
use App\Entities\SituacaoCalendario;
use App\Entities\TipoProcesso;
use App\Entities\UfCalendario;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Service\AuthService;
use App\To\AuthTO;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\AtividadePrincipalCalendarioRepository;
use App\To\AtividadePrincipalFiltroTO;
use App\Business\PrazoCalendarioBO;
use App\Entities\PrazoCalendario;
use App\Entities\Eleicao;
use App\Entities\SituacaoEleicao;
use App\Entities\EleicaoSituacao;
use App\Entities\ArquivoCalendario;

/**
 * Teste de Unidade referente á classe AtividadePrincipalBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class AtividadePrincipalBOTest extends TestCase
{

    const ID_CALENDARIO = 99;

    const ID_ATIVIDADE_PRINCIPAL = 23;

    const ID_ATIVIDADE_SECUNDARIA = 63;

    const ID_PRAZO_CALENDARIO = 99;

    /**
     * Teste Unitário do metódo GetAtividadePrincipalPorCalendarioComFiltro sobre cenário de sucesso.
     *
     * @throws ReflectionException
     */
    public function testarGetAtividadePrincipalPorCalendarioComFiltro_ComSucesso()
    {
        $calendario = $this->criarCalendario();
        $atividade = $this->criarAtividadesPrimariasSecundarias();
        $filtroTO = AtividadePrincipalFiltroTO::newInstance();
        $calendario->setId(self::ID_CALENDARIO);
        $atividade->setId(self::ID_ATIVIDADE_PRINCIPAL);
        $atividade->getAtividadesSecundarias()[0]->setId(self::ID_ATIVIDADE_SECUNDARIA);

        $atividadePrincipalRepositoryMock = $this->createMock(AtividadePrincipalCalendarioRepository::class);
        $atividadePrincipalRepositoryMock->method('getPorCalendarioComFiltro')->willReturn([
            'calendario' => $calendario,
            'atividadePrincipal' => $atividade
        ]);

        $atividadePrincipalBO = new AtividadePrincipalBO();
        $this->setPrivateProperty($atividadePrincipalBO, 'atividadePrincipalCalendarioRepository', $atividadePrincipalRepositoryMock);

        $this->assertNotEmpty($atividadePrincipalBO->getAtividadePrincipalPorCalendarioComFiltro(self::ID_CALENDARIO, $filtroTO)['atividadePrincipal']);
    }

    /**
     * Teste Unitário do método GetAtividadePrincipalPorCalendarioComFiltro sobre cenário de busca onde o calendário não contem atividades cadastradas.
     */
    public function testarGetAtividadePrincipalPorCalendarioComFiltro_SemAtividade()
    {
        $calendario = $this->criarCalendario();
        $filtroTO = AtividadePrincipalFiltroTO::newInstance();
        $calendario->setId(self::ID_CALENDARIO);

        $atividadePrincipalRepositoryMock = $this->createMock(AtividadePrincipalCalendarioRepository::class);
        $atividadePrincipalRepositoryMock->method('getPorCalendarioComFiltro')->willReturn([
            'calendario' => $calendario,
            'atividadePrincipal' => []
        ]);

        $atividadePrincipalBO = new AtividadePrincipalBO();
        $this->setPrivateProperty($atividadePrincipalBO, 'atividadePrincipalCalendarioRepository', $atividadePrincipalRepositoryMock);

        $this->assertEmpty($atividadePrincipalBO->getAtividadePrincipalPorCalendarioComFiltro(self::ID_CALENDARIO, $filtroTO)['atividadePrincipal'], "Atividade Principal deveria estar vazia.");
        $this->assertNotEmpty($atividadePrincipalBO->getAtividadePrincipalPorCalendarioComFiltro(self::ID_CALENDARIO, $filtroTO)['calendario'], "Calendário não deveria estar vazio.");
    }

    /**
     * Teste Unitário do método GetAtividadePrincipalPorCalendarioComFiltro sobre cenário de busca onde o id de calendário não exista.
     */
    public function testarGetAtividadePrincipalPorCalendarioComFiltro_SemCalendario()
    {
        $filtroTO = AtividadePrincipalFiltroTO::newInstance();
        $atividadePrincipalRepositoryMock = $this->createMock(AtividadePrincipalCalendarioRepository::class);
        $atividadePrincipalRepositoryMock->method('getPorCalendarioComFiltro')->willReturn([
            'calendario' => [],
            'atividadePrincipal' => []
        ]);

        $atividadePrincipalBO = new AtividadePrincipalBO();
        $this->setPrivateProperty($atividadePrincipalBO, 'atividadePrincipalCalendarioRepository', $atividadePrincipalRepositoryMock);

        $this->assertEmpty($atividadePrincipalBO->getAtividadePrincipalPorCalendarioComFiltro(self::ID_CALENDARIO, $filtroTO)['atividadePrincipal'], "Atividade Principal deveria estar vazia.");
        $this->assertEmpty($atividadePrincipalBO->getAtividadePrincipalPorCalendarioComFiltro(self::ID_CALENDARIO, $filtroTO)['calendario'], "Calendário deveria estar vazio.");
    }

    /**
     * Teste Unitário do método excluirPrazosCalendarioPorAtividadePrincipal sobre cenário onde todos os prazos são removidos com sucesso.
     */
    public function testarExcluirPrazosCalendarioPorAtividadePrincipal_ComSucesso()
    {
        $atividadePrincipalBO = new AtividadePrincipalBO();

        $prazo = $this->criarPrazosCalendario(self::ID_ATIVIDADE_PRINCIPAL);
        $prazoBOMock = $this->createMock(PrazoCalendarioBO::class);
        $prazoBOMock->method('getPrazosPorAtividadePrincipal')->willReturn([
            $prazo
        ]);
        $prazoBOMock->method('excluirPrazo')->willReturn(null);

        $this->setPrivateProperty($atividadePrincipalBO, 'prazoCalendarioBO', $prazoBOMock);
        $this->assertNull($atividadePrincipalBO->excluirPrazosCalendarioPorAtividadePrincipal(self::ID_ATIVIDADE_PRINCIPAL));
    }

    /**
     * Teste Unitário do método excluirPrazosCalendarioPorAtividadePrincipal sobre cenário onde não existem prazos vinculados a atividade principal.
     */
    public function testarExcluirPrazosCalendarioPorAtividadePrincipal_SemPrazos()
    {
        $atividadePrincipalBO = new AtividadePrincipalBO();

        $prazoBOMock = $this->createMock(PrazoCalendarioBO::class);
        $prazoBOMock->method('getPrazosPorAtividadePrincipal')->willReturn([]);

        $this->setPrivateProperty($atividadePrincipalBO, 'prazoCalendarioBO', $prazoBOMock);
        $this->assertNull($atividadePrincipalBO->excluirPrazosCalendarioPorAtividadePrincipal(self::ID_ATIVIDADE_PRINCIPAL));
    }

    /**
     * Teste Unitário do método excluirPrazosCalendarioPorAtividadePrincipal sobre cenário onde não existe atividade principal com o id informado.
     */
    public function testarExcluirPrazosCalendarioPorAtividadePrincipal_SemAtividadePrincipal()
    {
        $atividadePrincipalBO = new AtividadePrincipalBO();

        $prazoBOMock = $this->createMock(PrazoCalendarioBO::class);
        $prazoBOMock->method('getPrazosPorAtividadePrincipal')->willReturn(null);

        $this->setPrivateProperty($atividadePrincipalBO, 'prazoCalendarioBO', $prazoBOMock);
        $this->assertNull($atividadePrincipalBO->excluirPrazosCalendarioPorAtividadePrincipal(self::ID_ATIVIDADE_PRINCIPAL));
    }

    /**
     * Instanciação de um objeto de Calendário.
     *
     * @param boolean $criaAtividades
     * @param boolean $criaSituacao
     * @return \App\Entities\Calendario
     */
    private function criarCalendario($criaAtividades = false, $criaSituacao = false)
    {
        $tipoProcesso = TipoProcesso::newInstance();
        $tipoProcesso->setId(Constants::TIPO_PROCESSO_ORDINARIO);
        $arquivo = $this->criarArquivoCalendario();

        $cauUf = UfCalendario::newInstance();
        $cauUf->setIdCauUf(23);

        $eleicao = Eleicao::newInstance();
        $eleicao->setAno(2013);
        $eleicao->setTipoProcesso($tipoProcesso);

        $calendario = Calendario::newInstance();
        $calendario->setEleicao($eleicao);
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

        if ($criaSituacao) {
            $situacao = SituacaoCalendario::newInstance();
            $situacao->setId(Constants::SITUACAO_CALENDARIO_CONCLUIDO);

            $calendarioSituacao = CalendarioSituacao::newInstance();
            $calendarioSituacao->setId(self::ID_ATV_PRM);
            $calendarioSituacao->setData(new DateTime('now'));
            $calendarioSituacao->setSituacaoCalendario($situacao);

            $calendario->setSituacoes(new ArrayCollection());
            $calendario->getSituacoes()->add($calendarioSituacao);

            $situacaoEleicao = SituacaoEleicao::newInstance();
            $situacaoEleicao->setId(Constants::SITUACAO_CALENDARIO_CONCLUIDO);

            $eleicaoSituacao = EleicaoSituacao::newInstance();
            $eleicaoSituacao->setId(self::ID_ATV_SEC);
            $eleicaoSituacao->setData(new DateTime('now'));
            $eleicaoSituacao->setSituacaoEleicao($situacaoEleicao);

            $calendario->getEleicao()->setSituacoes(new ArrayCollection());
            $calendario->getEleicao()->getSituacoes()->add($situacaoEleicao);
        }

        if ($criaAtividades) {
            $calendario->setAtividadesPrincipais(new ArrayCollection());
            $calendario->getAtividadesPrincipais()->add($this->criarAtividadesPrimariasSecundarias($criaAtividades));
        }

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
     * Instanciação de um objeto de Atividade Primaria com usa respectivas atividades secundárias.
     *
     * @param boolean $criaErro
     * @return \App\Entities\AtividadePrincipalCalendario
     */
    private function criarAtividadesPrimariasSecundarias($criaErro = false)
    {
        $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance();
        $atividadeSecundaria->setDescricao('sub desc 1.1');
        $atividadeSecundaria->setDataInicio(new DateTime('2013-02-01'));
        $atividadeSecundaria->setDataFim(new DateTime('2013-02-15'));
        $atividadeSecundaria->setNivel(1);

        $atividadePrincipal = AtividadePrincipalCalendario::newInstance();
        $atividadePrincipal->setDescricao('desc 1');
        $atividadePrincipal->setDataInicio(new DateTime('2013-02-01'));
        $atividadePrincipal->setDataFim(new DateTime('2013-02-25'));
        if (! $criaErro) {
            $atividadePrincipal->setDataFim(new DateTime('2013-04-25'));
        }
        $atividadePrincipal->setNivel(1);
        $atividadePrincipal->setObedeceVigencia(true);

        $atividadePrincipal->setAtividadesSecundarias(new ArrayCollection());
        $atividadePrincipal->getAtividadesSecundarias()->add($atividadeSecundaria);

        return $atividadePrincipal;
    }

    /**
     * Instanciação de objeto de prazo calendário.
     *
     * @param integer $idAtividadePrincipal
     * @return \App\Entities\PrazoCalendario
     */
    private function criarPrazosCalendario($idAtividadePrincipal)
    {
        $data = [
            'id' => $idAtividadePrincipal,
            'nivel' => 2,
            'atividadePrincipal' => self::ID_ATIVIDADE_PRINCIPAL,
            'descricaoAtividade' => 'Atividade 1.1',
            'duracao' => 1,
            'situacaoDiaUtil' => 'true'
        ];
        return PrazoCalendario::newInstance($data);
    }
}