<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 13/10/2019
 * Time: 14:14
 */
namespace App\Business;

use App\Config\Constants;
use App\Entities\CorpoEmail;
use App\Repository\CorpoEmailRepository;
use App\To\CorpoEmailFiltroTO;
use App\Util\ImageUtils;
use App\To\CorpoEmailTO;
use App\Entities\EmailAtividadeSecundaria;
use App\Entities\Historico;
use App\Exceptions\NegocioException;
use App\Exceptions\Message;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Support\Facades\Lang;

class CorpoEmailBO extends AbstractBO
{

    /**
     *
     * @var CorpoEmailRepository
     */
    private $corpoEmailRepository;

    /**
     *
     * @var \App\Business\EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     *
     * @var HistoricoBO
     */
    private $historicoBO;

    /**
     *
     * @var CabecalhoEmailBO
     */
    private $cabecalhoEmailBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->corpoEmailRepository = $this->getRepository(CorpoEmail::class);
        $this->cabecalhoEmailBO = app()->make(CabecalhoEmailBO::class);
    }

    /**
     * Recupera os corpos do e-mail.
     *
     * @return mixed
     */
    public function getCorposEmail()
    {
        return $this->corpoEmailRepository->getCorposEmail();
    }

    /**
     * Recupera o corpo do e-mail de acordo com o 'id' informado.
     *
     * @param
     *            $id
     * @return \App\To\CorpoEmailTO|null
     * @throws NonUniqueResultException
     */
    public function getPorId($id)
    {
        $corpoEmail = $this->corpoEmailRepository->getPorId($id);

        if (! empty($corpoEmail) && ! empty($corpoEmail->getCabecalhoEmail())) {
            $corpoEmail->getCabecalhoEmail()->carregarImagemRodape();
            $corpoEmail->getCabecalhoEmail()->carregarImagemCabecalho();
        }

        return $corpoEmail;
    }

    /**
     * Salvar Corpo de E-mail.
     *
     * @param CorpoEmailTO $corpoEmailTO
     * @return CorpoEmail
     * @throws Exception
     */
    public function salvar(CorpoEmail $corpoEmail)
    {
        try {
            $this->beginTransaction();

            $this->validarCamposObrigatorios($corpoEmail);

            $emailsAtividadeSecundaria = $corpoEmail->getEmailsAtividadeSecundaria();
            $corpoEmail->setEmailsAtividadeSecundaria(null);
            $emailsAtividadeSecundariaSalvo = [];

            $acao = empty($corpoEmail->getId()) ? Constants::HISTORICO_ACAO_INSERIR : Constants::HISTORICO_ACAO_ALTERAR;
            $descricao = empty($corpoEmail->getId()) ? Constants::HISTORICO_DESCRICAO_ACAO_INSERIR : Constants::HISTORICO_DESCRICAO_ACAO_ALTERAR;

            $this->corpoEmailRepository->persist($corpoEmail);

            foreach ($emailsAtividadeSecundaria as $emailAtividadeSecundaria) {
                $idAtivSec = $emailAtividadeSecundaria->getAtividadeSecundaria()->getId();
                $emailAtividadeSecundariaAnterior = $this->getEmailAtividadeSecundariaBO()
                    ->getEmailAtividadeSecundariaPorCorpoEmail($corpoEmail->getId(), $idAtivSec);
                if (empty($emailAtividadeSecundariaAnterior)) {
                    $emailAtividadeSecundaria->setCorpoEmail($corpoEmail);
                } else {
                    $emailAtividadeSecundariaAnterior->setCorpoEmail($corpoEmail);
                    $emailAtividadeSecundaria = $emailAtividadeSecundariaAnterior;
                }
                $this->getEmailAtividadeSecundariaBO()->salvar($emailAtividadeSecundaria);
                array_push($emailsAtividadeSecundariaSalvo, $emailAtividadeSecundaria);
            }
            $this->removerEmailsAtividadeSecundaria($emailsAtividadeSecundariaSalvo, $corpoEmail);

            $historico = $this->getHistoricoBO()->criarHistorico(
                $corpoEmail, Constants::HISTORICO_ID_TIPO_CORPO_EMAIL, $acao, $descricao
            );
            $this->getHistoricoBO()->salvar($historico);

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        return $corpoEmail;
    }

    /**
     * Recupera os corpos de e-mail de acordo com o filtro informado.
     *
     * @param CorpoEmailFiltroTO $corpoEmailFiltroTO
     * @return mixed
     */
    public function getCorposEmailPorFiltro(CorpoEmailFiltroTO $corpoEmailFiltroTO)
    {
        return $this->corpoEmailRepository->getCorposEmailPorFiltro($corpoEmailFiltroTO);
    }

    /**
     * Recupera a lista de e-mails vinculados a atividade secundária informada.
     *
     * @param
     *            $id
     * @return mixed
     * @throws Exception
     */
    public function getEmailsPorAtividadeSecundaria($id)
    {
        $emails = $this->corpoEmailRepository->getEmailsPorAtividadeSecundaria($id);
        /** @var  $email CorpoEmail */
        foreach ($emails as $email) {
            $email->setCabecalhoEmail($this->getCabecalhoEmailBO()->getPorId($email->getCabecalhoEmail()->getId()));
        }

        return $emails;
    }

    /**
     * Valida campos obrigatórios de Corpo de E-mail.
     *
     * @param CorpoEmail $corpoEmail
     * @throws NegocioException
     */
    private function validarCamposObrigatorios(CorpoEmail $corpoEmail)
    {
        $campos = [];
        if ($corpoEmail->isAtivo()) {
            if(empty($corpoEmail->getAssunto())){
                array_push($campos, 'LABEL_ASSUNTO');
            }
            
            if(empty($corpoEmail->getEmailsAtividadeSecundaria())){
                array_push($campos, 'LABEL_ATIVIDADE_SECUNDARIA');
            }
        }              
        
        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }
    
    /**
     * Retorna BO de E-mail Atividade Secundária.
     *
     * @return \App\Business\EmailAtividadeSecundariaBO
     */
    private function getEmailAtividadeSecundariaBO()
    {
        if (empty($this->emailAtividadeSecundariaBO)) {
            $this->emailAtividadeSecundariaBO = app()->make(EmailAtividadeSecundariaBO::class);
        }
        return $this->emailAtividadeSecundariaBO;
    }

    /**
     * Retorna BO de Cabeçaho de E-mail.
     *
     * @return \App\Business\CabecalhoEmailBO
     */
    private function getCabecalhoEmailBO()
    {
        if (empty($this->cabecalhoEmailBO)) {
            $this->cabecalhoEmailBO = app()->make(CabecalhoEmailBO::class);
        }
        return $this->cabecalhoEmailBO;
    }

    /**
     * Remover relacionamento de E-mail Atividade Secundária com Corpo de Email.
     *
     * @param array $emailsAtividadeSecundaria
     * @param CorpoEmail $corpoEmail
     */
    private function removerEmailsAtividadeSecundaria($emailsAtividadeSecundaria, $corpoEmail)
    {
        $emailsAtividadeSecundaria2 = $this->getEmailAtividadeSecundariaBO()->getEmailsAtividadeSecundariaPorCorpoEmail($corpoEmail->getId());
        foreach ($emailsAtividadeSecundaria2 as $emailAtividadeSecundaria2) {
            $encontrado = false;
            foreach ($emailsAtividadeSecundaria as $emailAtividadeSecundaria) {
                if ($emailAtividadeSecundaria->getId() == $emailAtividadeSecundaria2->getId()) {
                    $encontrado = true;
                }
            }
            if (! $encontrado) {
                if (!empty($emailAtividadeSecundaria2->getEmailsTipos())) {
                    throw new NegocioException(Lang::get('messages.corpo_email.has_definicao_email', [
                        $emailAtividadeSecundaria2->getAtividadeSecundaria()->getAtividadePrincipalCalendario()->getDescricao(),
                        $emailAtividadeSecundaria2->getAtividadeSecundaria()->getDescricao()
                    ]));
                }
                try {
                    $this->getEmailAtividadeSecundariaBO()->deletar($emailAtividadeSecundaria2);
                } catch (OptimisticLockException $e) {
                } catch (ORMException $e) {
                }
            }
        }
    }

    /**
     * Retorna objeto responsável por encapsular implementações de BO de Histórico
     *
     * @return \App\Business\HistoricoBO
     */
    private function getHistoricoBO()
    {
        if (empty($this->historicoBO)) {
            $this->historicoBO = app()->make(HistoricoBO::class);
        }
        return $this->historicoBO;
    }
}
