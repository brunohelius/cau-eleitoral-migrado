<?php

namespace App\Business;

use App\Config\AppConfig;
use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\CorpoEmail;
use App\Entities\EmailAtividadeSecundaria;
use App\Entities\Entity;
use App\Entities\TipoEmailAtividadeSecundaria;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Mail\AtividadeSecundariaMail;
use App\Mail\JulgamentoRecursoImpugnacaoCadastradoMail;
use App\Repository\CorpoEmailRepository;
use App\Repository\EmailAtividadeSecundariaRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\EmailTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'E-mail Atividade Secundária'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class EmailAtividadeSecundariaBO extends AbstractBO
{

    /**
     *
     * @var EmailAtividadeSecundariaRepository
     */
    private $emailAtividadeSecundariaRepository;

    /**
     * @var CorpoEmailRepository
     */
    private $corpoEmailRepository;

    /**
     *
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaBO;

    /**
     *
     * @var EmailAtividadeSecundariaTipoBO
     */
    private $emailAtividadeSecundariaTipoBO;

    /**
     *
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     *
     * @var CabecalhoEmailBO
     */
    private $cabecalhoEmailBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var UfCalendarioBO
     */
    private $ufCalendarioBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->arquivoService = app()->make(ArquivoService::class);
        $this->emailAtividadeSecundariaRepository = $this->getRepository(EmailAtividadeSecundaria::class);
        $this->atividadeSecundariaBO = app()->make(AtividadeSecundariaCalendarioBO::class);
    }

    /**
     * Retorna E-mail Atividade Secundária por id.
     *
     * @param integer $id
     * @return object|NULL
     */
    public function getPorId($id)
    {
        return $this->emailAtividadeSecundariaRepository->find($id);
    }

    /**
     * Retorna lista de CorpoEmail relacionados a uma determinada AtividadeSecundaria.
     *
     * @param integer $idAtividadeSecundaria
     * @return array|Statement|mixed|NULL
     */
    public function getEmailAtividadeSecundariaPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        $emails = $this->emailAtividadeSecundariaRepository->getEmailAtividadeSecundariaPorAtividadeSecundaria(
            $idAtividadeSecundaria
        );

        if (!empty($emails)) {
            /** @var EmailAtividadeSecundaria $email */
            foreach ($emails as $index => $email) {
                $cabecalho = $email->getCorpoEmail()->getCabecalhoEmail();
                $this->getCabecalhoEmailBO()->definirImagensBase64CabecalhoRodape($cabecalho);
                $email->getCorpoEmail()->setCabecalhoEmail($cabecalho);
            }
        }
        return $emails;
    }

    /**
     * Retorna lista de CorpoEmail relacionados a uma determinada AtividadeSecundaria.
     *
     * @param integer $idEmailAtividadeSecundaria
     * @return array|Statement|mixed|NULL
     */
    public function hasDefinicaoEmail($idEmailAtividadeSecundaria)
    {
        $ids = $this->emailAtividadeSecundariaRepository->getIdsTipoEmailPorEmilAtividadeSecundaria(
            $idEmailAtividadeSecundaria
        );

        $hasDefinicao = (!empty($ids));

        return compact('hasDefinicao');
    }

    /**
     * Retorna e-mail defenido por atividade secundária e tipo.
     *
     * @param integer $idAtividadeSecundaria
     * @param integer $idTipoAtividadeSecundaria
     * @return mixed|Statement|array|NULL
     * @throws NonUniqueResultException
     */
    public function getEmailPorAtividadeSecundariaAndTipo($idAtividadeSecundaria, $idTipoAtividadeSecundaria)
    {
        return $this->emailAtividadeSecundariaRepository->getEmailAtividadeSecundariaPorTipo($idAtividadeSecundaria,
            $idTipoAtividadeSecundaria);
    }

    /**
     * Retorna E-mail de Atividade secundária por Corpo de E-mail e Atividade Secundária.
     *
     * @param integer $idCorpoEmail
     * @return EmailAtividadeSecundaria|mixed|Statement|array|NULL
     * @throws NonUniqueResultException
     */
    public function getEmailAtividadeSecundariaPorCorpoEmail($idCorpoEmail, $idAtividadeSecundaria)
    {
        return $this->emailAtividadeSecundariaRepository->getEmailAtividadeSecundariaPorCorpoEmail($idCorpoEmail,
            $idAtividadeSecundaria);
    }

    /**
     * Retorna lista de E-mail de Atividade secundária por Corpo de E-mail.
     *
     * @param integer $idCorpoEmail
     * @return mixed|Statement|array|NULL
     */
    public function getEmailsAtividadeSecundariaPorCorpoEmail($idCorpoEmail)
    {
        return $this->emailAtividadeSecundariaRepository->getEmailsAtividadeSecundariaPorCorpoEmail($idCorpoEmail);
    }

    /**
     * Remove EmailAtividadeSecundaria por id.
     *
     * @param EmailAtividadeSecundaria $emailAtividadeSecundaria
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deletar(EmailAtividadeSecundaria $emailAtividadeSecundaria)
    {
        $this->emailAtividadeSecundariaRepository->delete($emailAtividadeSecundaria);
    }

    /**
     * Método faz o envio de e-mails para os acessores CEN/BR e CE/UF após o cadatro do pedido de substituição
     *
     * @param $idAtividadeSecundaria
     * @param $idTipoEmailAtividadeSecundaria
     * @param $nomeTemplate
     * @param $idsCauUf
     * @param array $parametrosExtras
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailAcessoresCenAndAcessoresCE(
        $idAtividadeSecundaria,
        $idTipoEmailAtividadeSecundaria,
        $nomeTemplate,
        $idsCauUf,
        $parametrosExtras = []
    ) {
        $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            $idsCauUf
        );

        $this->enviarEmailPorIdAtividadeSecundaria(
            $idAtividadeSecundaria,
            array_unique($destinatarios),
            $idTipoEmailAtividadeSecundaria,
            $nomeTemplate,
            $parametrosExtras
        );
    }

    /**
     * Método faz o envio de e-mails para os membros comissão CEN/BR e CE/UF após o cadatro do pedido de substituição
     *
     * @param int $idAtivSecundaria
     * @param $idTipoEmailAtividadeSecundaria
     * @param $nomeTemplate
     * @param $idCauUf
     * @param array $parametrosExtras
     * @throws NonUniqueResultException
     * @throws NegocioException
     */
    public function enviarEmailConselheirosCoordenadoresComissao(
        int $idAtivSecundaria,
        $idTipoEmailAtividadeSecundaria,
        $nomeTemplate,
        $idCauUf = null,
        $parametrosExtras = []
    ) {
        $destinatarios = $this->getDestinatariosEmailConselheirosCoordenadoresComissao($idAtivSecundaria, $idCauUf);

        $this->enviarEmailPorIdAtividadeSecundaria(
            $idAtivSecundaria,
            array_unique($destinatarios),
            $idTipoEmailAtividadeSecundaria,
            $nomeTemplate,
            $parametrosExtras
        );
    }

    /**
     * Método realiza o envio de e-mail no ínicio dos julgamentos
     * - Julgamento 1ª Instância do Pedido de substituição
     * - Julgamento 1ª Instância do Pedido de Impugnação
     * - Julgamento 2ª Instância do Pedido de substituição
     *
     * @param $nivelAtivPrincipal
     * @param $nivelAtivSecundaria
     * @param $idTipoEmail
     * @param $nomeTemplate
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function enviarEmailIncioPeriodoJulgamentoChapa($nivelAtivPrincipal, $nivelAtivSecundaria, $idTipoEmail)
    {
        $dataInicio = Utils::adicionarDiasData(Utils::getDataHoraZero(), 1);
        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            $dataInicio, null, $nivelAtivPrincipal, $nivelAtivSecundaria
        );

        foreach ($atividades as $atividadeSecundariaCalendario) {

            $idsCauUf = $this->getUfCalendarioBO()->getIdsCauUfCalendario(
                $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId()
            );

            $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE($idsCauUf);

            if (!empty($destinatarios)) {
                $emailAtividadeSecundaria = $this->getEmailPorAtividadeSecundariaAndTipo(
                    $atividadeSecundariaCalendario->getId(), $idTipoEmail
                );

                if (!empty($emailAtividadeSecundaria)) {
                    $emailTO = $this->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
                    $emailTO->setDestinatarios($destinatarios);

                    Email::enviarMail(new AtividadeSecundariaMail($emailTO));
                }
            }
        }
    }

    /**
     * @param int $idAtivSecundaria
     * @param $idCauUf
     * @return array
     * @throws NegocioException
     */
    public function getDestinatariosEmailConselheirosCoordenadoresComissao(int $idAtivSecundaria, $idCauUf): array
    {
        $destinatariosCEN = $this->getMembroComissaoBO()->getEmailsCoordenadoresPorIdAtividaSecundaria(
            $idAtivSecundaria, Constants::COMISSAO_MEMBRO_CAU_BR_ID
        );

        $destinatariosCE = [];
        if (!empty($idCauUf)) {
            $destinatariosCE = $this->getMembroComissaoBO()->getEmailsCoordenadoresPorIdAtividaSecundaria(
                $idAtivSecundaria, $idCauUf
            );
        }

        return array_merge($destinatariosCEN, $destinatariosCE);
    }

    /**
     * Método que busca e-mail definido para aticidade secundária e realiza o envio
     *
     * @param $idAtividadeSecundaria
     * @param array $emailsDestinatarios
     * @param $idTipoEmailAtividadeSecundaria
     * @param $nomeTemplate
     * @param array|null $parametrosExtras
     * @throws NonUniqueResultException
     */
    public function enviarEmailPorIdAtividadeSecundaria(
        $idAtividadeSecundaria,
        array $emailsDestinatarios,
        $idTipoEmailAtividadeSecundaria,
        $nomeTemplate,
        $parametrosExtras = []
    ) {
        $this->logs('##### enviarEmailPorIdAtividadeSecundaria');
        $this->logs($nomeTemplate);
        $this->logs('Tipo email: ' .$idTipoEmailAtividadeSecundaria);
        $this->logs(' ');

        $emailAtividadeSecundaria = $this->getEmailPorAtividadeSecundariaAndTipo(
            $idAtividadeSecundaria,
            $idTipoEmailAtividadeSecundaria
        );

        if (!empty($emailAtividadeSecundaria)) {
            $this->enviarEmailAtividadeSecundaria(
                $emailAtividadeSecundaria,
                array_unique($emailsDestinatarios),
                $nomeTemplate,
                $parametrosExtras
            );
        }
    }

    /**
     * Enviar e-mail definido para atividade secundária.
     *
     * @param EmailAtividadeSecundaria $emailAtividadeSecundaria
     * @param $enderecoEmailDestinatarios
     * @param $nomeTemplate
     * @param array $parametrosExtras
     * @return string|string[]
     */
    public function enviarEmailAtividadeSecundaria(
        EmailAtividadeSecundaria $emailAtividadeSecundaria,
        $enderecoEmailDestinatarios,
        $nomeTemplate = Constants::TEMPLATE_EMAIL_PADRAO,
        $parametrosExtras = []
    ) {
        $arquivoCabecalho = null;
        $arquivoRodape = null;

        $possuiCabecalho = !empty($emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail());

        if ($possuiCabecalho) {
            $path = $this->arquivoService->getCaminhoRepositorioCabecalhoEmail(
                $emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->getId()
            );

            $arquivoCabecalho = AppConfig::getRepositorio(
                $path,
                $emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->getNomeImagemFisicaCabecalho()
            );

            $arquivoRodape = AppConfig::getRepositorio(
                $path,
                $emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->getNomeImagemFisicaRodape()
            );
        }

        $email = Email::newInstance();
        $email->setAssunto($emailAtividadeSecundaria->getCorpoEmail()->getAssunto());
        $email->setDestinatarios($enderecoEmailDestinatarios);

        $html = AppConfig::getTemplateEmail($nomeTemplate);

        $arquivoCabecalhoInline = is_file($arquivoCabecalho)
            ? $email->incluirImagemInline($arquivoCabecalho)
            : null;

        $arquivoRodapeInline = is_file($arquivoRodape)
            ? $email->incluirImagemInline($arquivoRodape)
            : null;

        if (!empty($enderecoEmailDestinatarios) && $emailAtividadeSecundaria->getCorpoEmail()->isAtivo()) {
            if ($possuiCabecalho && $emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->isCabecalhoAtivo()) {
                $html = str_replace(Constants::PARAMETRO_EMAIL_IMG_CABECALHO, $arquivoCabecalhoInline, $html);

                $html = str_replace(
                    Constants::PARAMETRO_EMAIL_CABECALHO,
                    $emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->getTextoCabecalho(),
                    $html
                );
            } else {
                $html = str_replace(Constants::PARAMETRO_EMAIL_CABECALHO, ' ', $html);
            }

            if ($possuiCabecalho && $emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->isRodapeAtivo()) {
                $html = str_replace(Constants::PARAMETRO_EMAIL_IMG_RODAPE, $arquivoRodapeInline, $html);
                $html = str_replace(
                    Constants::PARAMETRO_EMAIL_RODAPE,
                    $emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->getTextoRodape(),
                    $html
                );
            } else {
                $html = str_replace(Constants::PARAMETRO_EMAIL_RODAPE, ' ', $html);
            }

            $html = str_replace(
                Constants::PARAMETRO_EMAIL_CORPO,
                $emailAtividadeSecundaria->getCorpoEmail()->getDescricao(),
                $html
            );

            if (!empty($parametrosExtras)) {
                foreach ($parametrosExtras as $descricaoParamentro => $valorParametro) {
                    $html = str_replace($descricaoParamentro, $valorParametro, $html);
                }
            }

            $email->setCorpoEmail($html);
            $email->enviar();
            foreach ($enderecoEmailDestinatarios as $descricaoParamentro => $valorParametro) {
                $this->logs($valorParametro);
            }
            $this->logs(' ');
        }
        return $html;
    }

    /**
     * Recupera as informaçoes do email (cabeçalho, corpo e rodape) de acordo com a atividade secundaria
     *
     * @param EmailAtividadeSecundaria $emailAtividadeSecundaria
     * @param $enderecoEmailDestinatarios
     * @return EmailTO
     */
    public function recuperaInformacoesEmailPadrao(EmailAtividadeSecundaria $emailAtividadeSecundaria)
    {
        $emailTO = EmailTO::newInstance();
        $emailTO->setAssunto($emailAtividadeSecundaria->getCorpoEmail()->getAssunto());
        $possuiCabecalho = !empty($emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail());

        if ($emailAtividadeSecundaria->getCorpoEmail()->isAtivo()) {

            $emailTO->setTextoCorpo($emailAtividadeSecundaria->getCorpoEmail()->getDescricao());

            if ($possuiCabecalho) {
                $this->recuperaCabecalhoRodape($emailAtividadeSecundaria, $emailTO);
            }
        }

        return $emailTO;
    }

    /**
     * Envio de E-mails agendados da atividade Informar membros.
     * @throws Exception
     */
    public function enviarEmailIncioPeriodoInformarMembros()
    {
        $dataInicio = Utils::getDataHoraZero();
        $emails = $this->emailAtividadeSecundariaRepository->getEmailPorData(Constants::EMAIL_INFORMACAO_MEMBRO,
            $dataInicio->format('Y-m-d'), null);

        if (!empty($emails)) {
            foreach ($emails as $email) {
                $this->enviarEmailAtividadeSecundaria($email, []);
            }
        }
    }

    /**
     * Método responsável por persistir entidade de EmailAtividadeSecundaria.
     *
     * @param EmailAtividadeSecundaria $emailAtividadeSecundaria
     * @return Entity|object|array|ObjectManagerAware|boolean
     * @throws Exception
     */
    public function salvar(EmailAtividadeSecundaria $emailAtividadeSecundaria, $isTransacaoInterna = true)
    {
        $this->validarCamposObrigatorios($emailAtividadeSecundaria);
        $emailAtividadeSecundariaSalva = null;

        try {
            if ($isTransacaoInterna) {
                $this->beginTransaction();
            }

            $emailAtividadeSecundariaSalva = $this->emailAtividadeSecundariaRepository->persist($emailAtividadeSecundaria);

            if ($isTransacaoInterna) {
                $this->commitTransaction();
            }
        } catch (Exception $e) {
            if ($isTransacaoInterna) {
                $this->rollbackTransaction();
            }
            throw $e;
        }
        return $emailAtividadeSecundariaSalva;
    }

    /**
     * Retorna o e-mail padrão 001
     * @return EmailAtividadeSecundaria
     * @throws NonUniqueResultException
     */
    public function getEmailAtividadeSecundariaPadrao()
    {
        $corpoEmailPadrao = $this->getCorpoEmailRepository()->getPorId(
            Constants::ID_EMAIL_PADRAO_DEFINIR_NUMERO_MEMBROS
        );

        $emailAtividadeSecundariaPadrao = EmailAtividadeSecundaria::newInstance();
        $emailAtividadeSecundariaPadrao->setCorpoEmail($corpoEmailPadrao);

        return $emailAtividadeSecundariaPadrao;
    }

    /**
     * Validação de campos obrigatórios da entidade de EmailAtividadeSecundaria.
     *
     * @param EmailAtividadeSecundaria $emailAtividadeSecundaria
     * @throws NegocioException
     */
    private function validarCamposObrigatorios(EmailAtividadeSecundaria $emailAtividadeSecundaria)
    {
        $campos = [];
        if (empty($emailAtividadeSecundaria)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, [], true);
        }

        if (empty($emailAtividadeSecundaria->getAtividadeSecundaria()) || empty($emailAtividadeSecundaria->getAtividadeSecundaria()->getId())) {
            $campos[] = 'LABEL_ATIVIDADE_SECUNDARIA';
        }

        if (empty($emailAtividadeSecundaria->getCorpoEmail()) || empty($emailAtividadeSecundaria->getCorpoEmail()->getId())) {
            $campos[] = 'LABEL_CORPO_EMAIL';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, [], true);
        }
    }

    /**
     * Método responsável por validar duplicação do par atividadeSecundaria e tipoEmail.
     *
     * @param EmailAtividadeSecundaria $emailAtividadeSecundaria
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function validarTipoEmailDefinido(EmailAtividadeSecundaria $emailAtividadeSecundaria)
    {
        $emailPorTipo = $this->emailAtividadeSecundariaRepository->getEmailAtividadeSecundariaPorTipo($emailAtividadeSecundaria->getAtividadeSecundaria()
            ->getId(), $emailAtividadeSecundaria->getTipoEmail()
            ->getId());

        if (!empty($emailPorTipo) && $emailPorTipo->getTipoEmail()->getId() != $emailAtividadeSecundaria->getTipoEmail()->getId()) {
            throw new NegocioException(Message::MSG_JA_EXISTE_EMAIL_PARAMETRIZADO_PARA_ESSE_TIPO, [], true);
        }
    }

    /**
     * Retorna uma instância de 'CorpoEmailRepository'
     *
     * @return CorpoEmailRepository
     */
    private function getCorpoEmailRepository(): CorpoEmailRepository
    {
        if (empty($this->corpoEmailRepository)) {
            $this->corpoEmailRepository = $this->getRepository(CorpoEmail::class);
        }

        return $this->corpoEmailRepository;
    }

    /**
     * Retorna BO de E-mail Atividade Secundária Tipo.
     *
     * @return EmailAtividadeSecundariaTipoBO
     */
    private function getEmailAtividadeSecundariaTipoBO()
    {
        if ($this->emailAtividadeSecundariaTipoBO == null) {
            $this->emailAtividadeSecundariaTipoBO = app()->make(EmailAtividadeSecundariaTipoBO::class);
        }

        return $this->emailAtividadeSecundariaTipoBO;
    }

    /**
     * Verifica se o E-mail Atividade Secundária é de um determinado tipo.
     *
     * @param EmailAtividadeSecundaria $emailAtividadeSecundaria
     * @param TipoEmailAtividadeSecundaria $tipoEmail
     * @return boolean
     */
    private function hasTipoEmailAtividadeSecundaria($emailAtividadeSecundaria, $tipoEmail)
    {
        if (!empty($emailAtividadeSecundaria) && !empty($emailAtividadeSecundaria->getEmailsTipos())) {
            foreach ($emailAtividadeSecundaria->getEmailsTipos() as $emailTipo) {
                if ($emailTipo->getTipoEmail()->getId() == $tipoEmail->getId()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Retorna BO de Cabeçaho de E-mail.
     *
     * @return CabecalhoEmailBO
     */
    private function getCabecalhoEmailBO()
    {
        if (empty($this->cabecalhoEmailBO)) {
            $this->cabecalhoEmailBO = app()->make(CabecalhoEmailBO::class);
        }
        return $this->cabecalhoEmailBO;
    }

    /**
     * Retorna uma nova instância de 'MembroComissaoBO'.
     *
     * @return MembroComissaoBO|mixed
     */
    private function getMembroComissaoBO()
    {
        if (empty($this->membroComissaoBO)) {
            $this->membroComissaoBO = app()->make(MembroComissaoBO::class);
        }

        return $this->membroComissaoBO;
    }

    /**
     * Retorna uma nova instância de 'CorporativoService'.
     *
     * @return CorporativoService|mixed
     */
    private function getCorporativoService()
    {
        if (empty($this->corporativoService)) {
            $this->corporativoService = app()->make(CorporativoService::class);
        }

        return $this->corporativoService;
    }

    /**
     * Retorna uma nova instância de 'AtividadeSecundariaCalendarioBO'.
     *
     * @return AtividadeSecundariaCalendarioBO|mixed
     */
    private function getAtividadeSecundariaCalendarioBO()
    {
        if (empty($this->atividadeSecundariaCalendarioBO)) {
            $this->atividadeSecundariaCalendarioBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }

        return $this->atividadeSecundariaCalendarioBO;
    }

    /**
     * Retorna a intancia de 'UfCalendarioBO'.
     *
     * @return UfCalendarioBO
     */
    private function getUfCalendarioBO()
    {
        if ($this->ufCalendarioBO == null) {
            $this->ufCalendarioBO = app()->make(UfCalendarioBO::class);
        }
        return $this->ufCalendarioBO;
    }

    /**
     * @param EmailAtividadeSecundaria $emailAtividadeSecundaria
     * @param $nomeArquivo
     * @return string
     */
    private function recuperaCaminhoArquivoFisico(EmailAtividadeSecundaria $emailAtividadeSecundaria, $nomeArquivo):
    string {
        $path = $this->arquivoService->getCaminhoRepositorioCabecalhoEmail(
            $emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->getId()
        );

        return AppConfig::getRepositorio(
            $path,
            $nomeArquivo
        );
    }

    /**
     * Recupera as informaçoes do cabeçalho e rodape
     *
     * @param EmailAtividadeSecundaria $emailAtividadeSecundaria
     * @param EmailTO $emailTO
     */
    private function recuperaCabecalhoRodape(
        EmailAtividadeSecundaria $emailAtividadeSecundaria,
        EmailTO &$emailTO
    ): void {
        if ($emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->isCabecalhoAtivo()) {
            $arquivoCabecalho = $this->recuperaCaminhoArquivoFisico($emailAtividadeSecundaria,
                $emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->getNomeImagemFisicaCabecalho());

            $emailTO->setIsCabecalhoAtivo(true);
            $emailTO->setCaminhoImagemCabecalho($this->arquivoService->fileExiste($arquivoCabecalho) ?
                $arquivoCabecalho : null);
            $emailTO->setTextoCabecalho($emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->getTextoCabecalho());
        }

        if ($emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->isRodapeAtivo()) {
            $arquivoRodape = $this->recuperaCaminhoArquivoFisico($emailAtividadeSecundaria,
                $emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->getNomeImagemFisicaRodape());

            $emailTO->setIsRodapeAtivo(true);
            $emailTO->setCaminhoImagemRodape($this->arquivoService->fileExiste($arquivoRodape) ?
                $arquivoRodape : null);
            $emailTO->setTextoRodape($emailAtividadeSecundaria->getCorpoEmail()->getCabecalhoEmail()->getTextoRodape());
        }
    }

    protected function logs($texto){
        $fp = fopen('/var/www/storage/logs/lumen.log', 'a');
        fwrite($fp, $texto."\n");
        fclose($fp);
    }

}

