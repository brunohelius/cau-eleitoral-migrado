<?php
namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use App\Entities\Entity;
use App\Entities\TipoEmailAtividadeSecundaria;
use App\Entities\EmailAtividadeSecundaria;

/**
 * Entidade que representa a parametrização de um e-mails de atividades secundárias por tipo.
 *
 * @ORM\Entity(repositoryClass="App\Repository\EmailAtividadeSecundariaTipoRepository")
 * @ORM\Table(schema="eleitoral", name="tb_email_tp_corpo_email")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class EmailAtividadeSecundariaTipo extends Entity
{
    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_EMAIL_TP_CORPO_EMAIL", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_email_tp_corpo_email_id_email_tp_corpo_email_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entities\EmailAtividadeSecundaria")
     * @ORM\JoinColumn(name="ID_EMAIL_ATIV_SECUNDARIA", referencedColumnName="ID_EMAIL_ATIV_SECUNDARIA", nullable=true)
     *
     * @var \App\Entities\EmailAtividadeSecundaria
     */
    private $emailAtividadeSecundaria;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoEmailAtividadeSecundaria")
     * @ORM\JoinColumn(name="ID_TP_CORPO_EMAIL", referencedColumnName="ID_TP_CORPO_EMAIL", nullable=true)
     *
     * @var \App\Entities\TipoEmailAtividadeSecundaria
     */
    private $tipoEmail;

    /**
     * Fábrica de instância de e-mails de atividades secundárias parametrizados por tipo.
     *
     * @param array $data
     * @return EmailAtividadeSecundariaTipo
     */
    public static function newInstance($data = null)
    {
        $emailAtividadeSecundariaTipo = new EmailAtividadeSecundariaTipo();
        if (! empty($data)) {
            $emailAtividadeSecundariaTipo->setId(Utils::getValue('id', $data));
            $tipoEmail = TipoEmailAtividadeSecundaria::newInstance(Utils::getValue('tipoEmail', $data));
            $emailAtividadeSecundariaTipo->setTipoEmail($tipoEmail);
            $emailAtividadeSecundaria = EmailAtividadeSecundaria::newInstance(Utils::getValue('emailAtividadeSecundaria', $data));
            $emailAtividadeSecundariaTipo->setEmailAtividadeSecundaria($emailAtividadeSecundaria);
        }
        return $emailAtividadeSecundariaTipo;
    }

    /**
     *
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return \App\Entities\EmailAtividadeSecundaria
     */
    public function getEmailAtividadeSecundaria()
    {
        return $this->emailAtividadeSecundaria;
    }

    /**
     *
     * @param \App\Entities\EmailAtividadeSecundaria $emailAtividadeSecundaria
     */
    public function setEmailAtividadeSecundaria($emailAtividadeSecundaria)
    {
        $this->emailAtividadeSecundaria = $emailAtividadeSecundaria;
    }

    /**
     *
     * @return \App\Entities\TipoEmailAtividadeSecundaria
     */
    public function getTipoEmail()
    {
        return $this->tipoEmail;
    }

    /**
     *
     * @param \App\Entities\TipoEmailAtividadeSecundaria $tipoEmail
     */
    public function setTipoEmail($tipoEmail)
    {
        $this->tipoEmail = $tipoEmail;
    }
}

