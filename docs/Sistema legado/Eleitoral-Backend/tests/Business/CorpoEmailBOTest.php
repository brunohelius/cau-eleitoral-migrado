<?php
use App\Entities\CorpoEmail;
use App\Business\CorpoEmailBO;
use App\Entities\CabecalhoEmail;
use App\Repository\CorpoEmailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use App\To\CorpoEmailTO;
use App\To\CorpoEmailFiltroTO;
use App\Business\EmailAtividadeSecundariaBO;
use App\Repository\EmailAtividadeSecundariaRepository;
use App\Entities\EmailAtividadeSecundaria;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\TipoEmailAtividadeSecundaria;
use App\Repository\HistoricoRepository;
use App\Entities\Historico;
use App\Business\HistoricoBO;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
include 'TestCase12.php';

/**
 * Teste de Unidade referente á classe CorpoEmailBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class CorpoEmailBOTest extends TestCase12
{

    const ID_ATIVIDADE_SECUNDARIA = 15;

    const ID_CORPO_EMAIL = 1;

    const ID_EMAIL_ATIVIDADE_SECUNDARIA = 3;

    const ID_TIPO_EMAIL = 4;

    const ID_RESPONSAVEL = 5;
    
    const CORPO_EMAIL_ASSUNTO = 'Assunto Corpo E-mail 1';

    /**
     * Realiza o teste verificando se os emails vinculados a atividade secundária foram recuperados.
     *
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testeGetEmailsComSucesso()
    {
        $emails = $this->getListaEmails();
        $corpoEmailRespositoryMock = $this->createMock(CorpoEmailRepository::class);
        $corpoEmailRespositoryMock->method('getEmailsPorAtividadeSecundaria')->willReturn($emails);

        $corpoEmailBO = new CorpoEmailBO();
        $this->setPrivateProperty($corpoEmailBO, 'corpoEmailRepository', $corpoEmailRespositoryMock);

        $this->assertNotEmpty($corpoEmailBO->getEmailsPorAtividadeSecundaria(self::ID_ATIVIDADE_SECUNDARIA));
    }

    /**
     * Teste que verifica o retorna caso a atividade secundária não exista.
     *
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testeGetEmailsPorAtividadeSecundariaNaoExistente()
    {
        $corpoEmailRespositoryMock = $this->createMock(CorpoEmailRepository::class);
        $corpoEmailRespositoryMock->method('getEmailsPorAtividadeSecundaria')->willReturn(new ArrayCollection());

        $corpoEmailBO = new CorpoEmailBO();
        $this->setPrivateProperty($corpoEmailBO, 'corpoEmailRepository', $corpoEmailRespositoryMock);

        $this->assertEquals($corpoEmailBO->getEmailsPorAtividadeSecundaria(null), new ArrayCollection());
    }

    /**
     * Teste que verifica o retorno da busca de Corpo de E-mail por id.
     *
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testeGetPorIdComSucesso()
    {
        $corpoEmailRespositoryMock = $this->createMock(CorpoEmailRepository::class);
        $corpoEmail = $this->getCorpoEmail();
        $corpoEmailRespositoryMock->method('getPorId')->willReturn($corpoEmail);

        $corpoEmailBO = new CorpoEmailBO();
        $this->setPrivateProperty($corpoEmailBO, 'corpoEmailRepository', $corpoEmailRespositoryMock);

        $this->assertEquals($corpoEmailBO->getPorId(self::ID_CORPO_EMAIL), $corpoEmail);
    }

    /**
     * Teste que verifica busca de Corpo de E-mail em caso onde o id informado não exista.
     *
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testeGetPorIdSemSucesso()
    {
        $corpoEmailRespositoryMock = $this->createMock(CorpoEmailRepository::class);
        $corpoEmailRespositoryMock->method('getPorId')->willReturn(null);

        $corpoEmailBO = new CorpoEmailBO();
        $this->setPrivateProperty($corpoEmailBO, 'corpoEmailRepository', $corpoEmailRespositoryMock);

        $this->assertEquals($corpoEmailBO->getPorId(self::ID_CORPO_EMAIL), null);
    }

    /**
     * Teste para verificar listagem de Entidades de Corpo de E-mail.
     *
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testeGetCorposEmailComSucesso()
    {
        $corpoEmailRespositoryMock = $this->createMock(CorpoEmailRepository::class);
        $corposEmail = $this->getListaEmails();
        $corpoEmailRespositoryMock->method('getCorposEmail')->willReturn($corposEmail);

        $corpoEmailBO = new CorpoEmailBO();
        $this->setPrivateProperty($corpoEmailBO, 'corpoEmailRepository', $corpoEmailRespositoryMock);

        $this->assertEquals($corpoEmailBO->getCorposEmail(), $corposEmail);
    }

    /**
     * Teste para verificar listagem de Entidades de Corpo de E-mail , em caso de lista vazia.
     *
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testeGetCorposEmailSemSucesso()
    {
        $corpoEmailRespositoryMock = $this->createMock(CorpoEmailRepository::class);
        $corposEmail = [];
        $corpoEmailRespositoryMock->method('getCorposEmail')->willReturn($corposEmail);

        $corpoEmailBO = new CorpoEmailBO();
        $this->setPrivateProperty($corpoEmailBO, 'corpoEmailRepository', $corpoEmailRespositoryMock);

        $this->assertEquals($corpoEmailBO->getCorposEmail(), $corposEmail);
    }

    /**
     * Teste de busca de Corpo de E-mail com sucesso.
     *
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testeGetCorposEmailPorFiltroComSucesso()
    {
        $corpoEmailRespositoryMock = $this->createMock(CorpoEmailRepository::class);
        $corposEmail = $this->getListaEmails();
        $corpoEmailRespositoryMock->method('getCorposEmailPorFiltro')->willReturn($corposEmail);

        $corpoEmailBO = new CorpoEmailBO();
        $this->setPrivateProperty($corpoEmailBO, 'corpoEmailRepository', $corpoEmailRespositoryMock);

        $corpoEmailFiltroTO = new CorpoEmailFiltroTO();

        $this->assertEquals($corpoEmailBO->getCorposEmailPorFiltro($corpoEmailFiltroTO), $corposEmail);
    }

    /**
     * Teste de busca de Corpo de E-mail sem sucesso.
     *
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testeGetCorposEmailPorFiltroSemSucesso()
    {
        $corpoEmailRespositoryMock = $this->createMock(CorpoEmailRepository::class);
        $corposEmail = [];
        $corpoEmailRespositoryMock->method('getCorposEmailPorFiltro')->willReturn($corposEmail);

        $corpoEmailBO = new CorpoEmailBO();
        $this->setPrivateProperty($corpoEmailBO, 'corpoEmailRepository', $corpoEmailRespositoryMock);

        $corpoEmailFiltroTO = new CorpoEmailFiltroTO();

        $this->assertEquals($corpoEmailBO->getCorposEmailPorFiltro($corpoEmailFiltroTO), $corposEmail);
    }

    /**
     * Teste de salvar Corpo de E-mail com sucesso.
     */
    public function testeSalvarComSucesso()
    {
        $corpoEmailRespositoryMock = $this->createMock(CorpoEmailRepository::class);
        $corpoEmail = $this->getCorpoEmail();
        $corpoEmailRespositoryMock->method('persist')->willReturn($corpoEmail);

        $emailAtividadeSecundariaBO = new EmailAtividadeSecundariaBO();
        $emailAtividadeSecundariaRespositoryMock = $this->createMock(EmailAtividadeSecundariaRepository::class);
        $emailAtividadeSecundaria = $this->criarEmailAtividadeSecundaria();
        $emailAtividadeSecundaria->setCorpoEmail($corpoEmail);
        $emailAtividadeSecundariaRespositoryMock->method('getEmailAtividadeSecundariaPorCorpoEmail')->willReturn($emailAtividadeSecundaria);
        $emailAtividadeSecundariaRespositoryMock->method('persist')->willReturn($emailAtividadeSecundaria);
        $emailAtividadeSecundariaRespositoryMock->method('getEmailsAtividadeSecundariaPorCorpoEmail')->willReturn([]);
        $this->setPrivateProperty($emailAtividadeSecundariaBO, 'emailAtividadeSecundariaRepository', $emailAtividadeSecundariaRespositoryMock);

        $historicoBO = new HistoricoBO();
        $historicoRespositoryMock = $this->createMock(HistoricoRepository::class);
        $historico = new Historico();
        $historicoRespositoryMock->method('persist')->willReturn($historico);
        $this->setPrivateProperty($historicoBO, 'historicoRepository', $historicoRespositoryMock);

        $corpoEmailBO = new CorpoEmailBO();
        $this->setPrivateProperty($corpoEmailBO, 'corpoEmailRepository', $corpoEmailRespositoryMock);
        $this->setPrivateProperty($corpoEmailBO, 'historicoBO', $historicoBO);
        $this->setPrivateProperty($corpoEmailBO, 'emailAtividadeSecundariaBO', $emailAtividadeSecundariaBO);

        $responsavel = new stdClass();
        $responsavel->id = self::ID_RESPONSAVEL;
        $corpoEmail->setEmailsAtividadeSecundaria([$emailAtividadeSecundaria]);

        $this->assertEquals($corpoEmailBO->salvar($corpoEmail, $responsavel), $corpoEmail);
    }
    
    /**
     * Testar validação do campo obrigatório assunto no salvar.
     * 
     */
    public function testeSalvarSemCampoObrigatorioAssunto()
    {
        $corpoEmailRespositoryMock = $this->createMock(CorpoEmailRepository::class);
        $corpoEmail = $this->getCorpoEmail();        
        $corpoEmailRespositoryMock->method('persist')->willReturn($corpoEmail);
        
        $emailAtividadeSecundariaBO = new EmailAtividadeSecundariaBO();
        $emailAtividadeSecundariaRespositoryMock = $this->createMock(EmailAtividadeSecundariaRepository::class);
        $emailAtividadeSecundaria = $this->criarEmailAtividadeSecundaria();
        $emailAtividadeSecundaria->setCorpoEmail($corpoEmail);
        $emailAtividadeSecundariaRespositoryMock->method('getEmailAtividadeSecundariaPorCorpoEmail')->willReturn($emailAtividadeSecundaria);
        $emailAtividadeSecundariaRespositoryMock->method('persist')->willReturn($emailAtividadeSecundaria);
        $emailAtividadeSecundariaRespositoryMock->method('getEmailsAtividadeSecundariaPorCorpoEmail')->willReturn([]);
        $this->setPrivateProperty($emailAtividadeSecundariaBO, 'emailAtividadeSecundariaRepository', $emailAtividadeSecundariaRespositoryMock);
        
        $historicoBO = new HistoricoBO();
        $historicoRespositoryMock = $this->createMock(HistoricoRepository::class);
        $historico = new Historico();
        $historicoRespositoryMock->method('persist')->willReturn($historico);
        $this->setPrivateProperty($historicoBO, 'historicoRepository', $historicoRespositoryMock);
        
        $corpoEmailBO = new CorpoEmailBO();
        $this->setPrivateProperty($corpoEmailBO, 'corpoEmailRepository', $corpoEmailRespositoryMock);
        $this->setPrivateProperty($corpoEmailBO, 'historicoBO', $historicoBO);
        $this->setPrivateProperty($corpoEmailBO, 'emailAtividadeSecundariaBO', $emailAtividadeSecundariaBO);
        
        $responsavel = new stdClass();
        $responsavel->id = self::ID_RESPONSAVEL;
        $corpoEmail->setEmailsAtividadeSecundaria([$emailAtividadeSecundaria]);
        $corpoEmail->setAssunto('');
        
        try {
            $this->assertNotEmpty($corpoEmailBO->salvar($corpoEmail, $responsavel));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }
    
    /**
     * Testar validação do campo obrigatório E-mail Atividade Secundária no salvar.
     */
    public function testeSalvarSemCampoObrigatorioEmailAtividadeSecundaria()
    {
        $corpoEmailRespositoryMock = $this->createMock(CorpoEmailRepository::class);
        $corpoEmail = $this->getCorpoEmail();
        $corpoEmailRespositoryMock->method('persist')->willReturn($corpoEmail);
        
        $emailAtividadeSecundariaBO = new EmailAtividadeSecundariaBO();
        $emailAtividadeSecundariaRespositoryMock = $this->createMock(EmailAtividadeSecundariaRepository::class);
        $emailAtividadeSecundaria = $this->criarEmailAtividadeSecundaria();
        $emailAtividadeSecundaria->setCorpoEmail($corpoEmail);
        $emailAtividadeSecundariaRespositoryMock->method('getEmailAtividadeSecundariaPorCorpoEmail')->willReturn($emailAtividadeSecundaria);
        $emailAtividadeSecundariaRespositoryMock->method('persist')->willReturn($emailAtividadeSecundaria);
        $emailAtividadeSecundariaRespositoryMock->method('getEmailsAtividadeSecundariaPorCorpoEmail')->willReturn([]);
        $this->setPrivateProperty($emailAtividadeSecundariaBO, 'emailAtividadeSecundariaRepository', $emailAtividadeSecundariaRespositoryMock);
        
        $historicoBO = new HistoricoBO();
        $historicoRespositoryMock = $this->createMock(HistoricoRepository::class);
        $historico = new Historico();
        $historicoRespositoryMock->method('persist')->willReturn($historico);
        $this->setPrivateProperty($historicoBO, 'historicoRepository', $historicoRespositoryMock);
        
        $corpoEmailBO = new CorpoEmailBO();
        $this->setPrivateProperty($corpoEmailBO, 'corpoEmailRepository', $corpoEmailRespositoryMock);
        $this->setPrivateProperty($corpoEmailBO, 'historicoBO', $historicoBO);
        $this->setPrivateProperty($corpoEmailBO, 'emailAtividadeSecundariaBO', $emailAtividadeSecundariaBO);
        
        $responsavel = new stdClass();
        $responsavel->id = self::ID_RESPONSAVEL;
        $corpoEmail->setEmailsAtividadeSecundaria([$emailAtividadeSecundaria]);
        $corpoEmail->setEmailsAtividadeSecundaria(null);
        
        try {
            $this->assertNotEmpty($corpoEmailBO->salvar($corpoEmail, $responsavel));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Retorna a lista de emails para simulação de cenário.
     *
     * @return ArrayCollection
     */
    private function getListaEmails()
    {
        $emails = new ArrayCollection();

        for ($i = 0; $i < 10; $i ++) {
            $cabecalhoEmail = new CabecalhoEmail();
            $cabecalhoEmail->setId($i + 1);

            $corpoEmail = new CorpoEmail();
            $corpoEmail->setId($i + 1);
            $corpoEmail->setCabecalhoEmail($cabecalhoEmail);
            $emails->add($corpoEmail);
        }

        return $emails;
    }

    /**
     * Retorna entidade de corpo de e-mail.
     *
     * @return \App\Entities\CorpoEmail
     */
    private function getCorpoEmail()
    {
        $corpoEmail = new CorpoEmail();
        $corpoEmail->setId(self::ID_CORPO_EMAIL);
        $corpoEmail->setAssunto(self::CORPO_EMAIL_ASSUNTO);
        return $corpoEmail;
    }

    /**
     * Retorno Entidade de E-mail Atividade Secundária.
     *
     * @param integer $idEmailAtividadeSecundaria
     * @param integer $idAtividadeSecundaria
     * @param integer $idTipoEmail
     * @return EmailAtividadeSecundaria
     */
    private function criarEmailAtividadeSecundaria($idEmailAtividadeSecundaria = null, $idAtividadeSecundaria = null, $idTipoEmail = null)
    {
        $emailAtividadeSecundaria = new EmailAtividadeSecundaria();
        $atividadeSecundaria = null;
        $tipoEmail = null;

        if (empty($idEmailAtividadeSecundaria)) {
            $emailAtividadeSecundaria->setId(self::ID_EMAIL_ATIVIDADE_SECUNDARIA);
        } else {
            $emailAtividadeSecundaria->setId($idEmailAtividadeSecundaria);
        }

        if (empty($idAtividadeSecundaria)) {
            $atividadeSecundaria = $this->criarAtividadeSecundaria();
        } else {
            $atividadeSecundaria = $this->criarAtividadeSecundaria($idAtividadeSecundaria);
        }

        if (empty($idTipoEmail)) {
            $tipoEmail = $this->criarTipoEmail();
        } else {
            $tipoEmail = $this->criarTipoEmail($idTipoEmail);
        }

        $emailAtividadeSecundaria->setAtividadeSecundaria($atividadeSecundaria);
        $emailAtividadeSecundaria->setTipoEmail($tipoEmail);

        return $emailAtividadeSecundaria;
    }

    /**
     * Retorno Entidade de Atividade Secundária.
     *
     * @param integer $idAtividadeSecundaria
     */
    private function criarAtividadeSecundaria($idAtividadeSecundaria = null)
    {
        $atividadeSecundaria = new AtividadeSecundariaCalendario();
        if (empty($idAtividadeSecundaria)) {
            $atividadeSecundaria->setId(self::ID_ATIVIDADE_SECUNDARIA);
        } else {
            $atividadeSecundaria->setId($idAtividadeSecundaria);
        }
        return $atividadeSecundaria;
    }

    /**
     * Retorno Entidade de Tipo E-mail Atividade Secundária.
     *
     * @param integer $idTipoEmail
     * @return \App\Entities\TipoEmailAtividadeSecundaria
     */
    private function criarTipoEmail($idTipoEmail = null)
    {
        $tipoEmail = new TipoEmailAtividadeSecundaria();
        if (empty($idTipoEmail)) {
            $tipoEmail->setId(self::ID_TIPO_EMAIL);
        } else {
            $tipoEmail->setId($idTipoEmail);
        }
        return $tipoEmail;
    }
}
