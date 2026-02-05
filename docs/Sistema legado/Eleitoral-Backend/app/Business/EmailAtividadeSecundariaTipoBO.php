<?php
namespace App\Business;

use App\Entities\EmailAtividadeSecundariaTipo;
use App\Repository\EmailAtividadeSecundariaTipoRepository;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'EmailAtividadeSecundariaTipo'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class EmailAtividadeSecundariaTipoBO extends AbstractBO
{

    /**
     *
     * @var EmailAtividadeSecundariaTipoRepository
     */
    private $emailAtividadeSecundariaTipoRepository;

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
        $this->emailAtividadeSecundariaTipoRepository = $this->getRepository(EmailAtividadeSecundariaTipo::class);
    }

    /**
     * Retorna um e-mail tipo definido para uma determinada atividade secundária
     *
     * @param $idAtividadeSecundaria
     * @param $idTipoCorpoEmail
     * @return EmailAtividadeSecundariaTipo
     * @throws NonUniqueResultException
     */
    public function getEmailAtividadeSecundariaTipoDefinido($idAtividadeSecundaria, $idTipoCorpoEmail)
    {
        return $this->emailAtividadeSecundariaTipoRepository->getPorAtividadeSecundariaAndTipoEmail(
            $idAtividadeSecundaria,
            $idTipoCorpoEmail
        );
    }

    /**
     * Retorna um e-mail tipo definido para uma determinada atividade secundária
     *
     * @param $idAtividadeSecundaria
     * @return array|null
     */
    public function getPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        $emailsTipos = $this->emailAtividadeSecundariaTipoRepository->getPorAtividadeSecundaria(
            $idAtividadeSecundaria
        );

        return $emailsTipos;
    }

    /**
     * Salva EmailAtividadeSecundariaTipo.
     *
     * @param EmailAtividadeSecundariaTipo $emailAtividadeSecundariaTipo
     * @throws Exception
     */
    public function salvar(\App\Entities\EmailAtividadeSecundariaTipo $emailAtividadeSecundariaTipo)
    {
        if (!$this->hasEmailTipoDefinido($emailAtividadeSecundariaTipo)) {
            try {
                $this->beginTransaction();
                $this->emailAtividadeSecundariaTipoRepository->persist($emailAtividadeSecundariaTipo);
                $this->commitTransaction();
            } catch (Exception $e) {
                $this->rollbackTransaction();
                throw $e;
            }
        }
    }

    /**
     *
     * @param EmailAtividadeSecundariaTipo $emailAtividadeSecundariaTipo
     * @throws Exception
     */
    public function delete(EmailAtividadeSecundariaTipo $emailAtividadeSecundariaTipo)
    {
        $emailTipo = $this->emailAtividadeSecundariaTipoRepository->find($emailAtividadeSecundariaTipo->getId());
        $this->emailAtividadeSecundariaTipoRepository->delete($emailTipo);
    }

    /**
     * Valida se já existe EmailAtividadeSecundariaTipo definido.
     *
     * @param EmailAtividadeSecundariaTipo $emailAtividadeSecundariaTipo
     * @return bool
     * @throws NonUniqueResultException
     */
    private function hasEmailTipoDefinido(EmailAtividadeSecundariaTipo $emailAtividadeSecundariaTipo)
    {
        $emailTipoAnterior = $this->emailAtividadeSecundariaTipoRepository->getPorEmailAtividadeSecundariaAndTipoEmail($emailAtividadeSecundariaTipo->getEmailAtividadeSecundaria()
            ->getId(), $emailAtividadeSecundariaTipo->getTipoEmail()
            ->getId());
        return ! empty($emailTipoAnterior);
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
}

