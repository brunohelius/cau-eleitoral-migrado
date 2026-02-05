<?php
namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;

use App\Entities\CorpoEmail;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\EmailAtividadeSecundariaTipo;

/**
 * Entidade que representa e-mails de atividades secundárias.
 * 
 * @ORM\Entity(repositoryClass="App\Repository\EmailAtividadeSecundariaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_EMAIL_ATIV_SECUNDARIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class EmailAtividadeSecundaria extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_EMAIL_ATIV_SECUNDARIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_email_ativ_secundaria_id_email_ativ_secundaria_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\CorpoEmail")
     * @ORM\JoinColumn(name="ID_CORPO_EMAIL", referencedColumnName="ID_CORPO_EMAIL", nullable=false)
     *
     * @var \App\Entities\CorpoEmail
     */
    private $corpoEmail;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\AtividadeSecundariaCalendario")
     * @ORM\JoinColumn(name="ID_ATIV_SECUNDARIA", referencedColumnName="ID_ATIV_SECUNDARIA", nullable=false)
     *
     * @var \App\Entities\AtividadeSecundariaCalendario
     */
    private $atividadeSecundaria;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entities\EmailAtividadeSecundariaTipo", mappedBy="emailAtividadeSecundaria", cascade={"persist"})
     *
     * @var array|ArrayCollection
     */
    private $emailsTipos;

    /**
     * Fábrica de instância de e-mails de atividades secundárias.
     * @param array $data
     * @return \app\Entities\EmailAtividadeSecundaria
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $emailAtividadeSecundaria = new EmailAtividadeSecundaria();
        if(!empty($data)){      
            $emailAtividadeSecundaria->setId(Utils::getValue('id', $data));            
            if(!empty(Utils::getValue('corpoEmail', $data))){
                $emailAtividadeSecundaria->setCorpoEmail(CorpoEmail::newInstance(Utils::getValue('corpoEmail', $data))); 
            }
            if(!empty(Utils::getValue('atividadeSecundaria', $data))){
                $emailAtividadeSecundaria->setAtividadeSecundaria(AtividadeSecundariaCalendario::newInstance(Utils::getValue('atividadeSecundaria', $data)));
            }
           
            $emailsTipos = array_map(function($emailTipo) {
                return EmailAtividadeSecundariaTipo::newInstance($emailTipo);
            } , Utils::getValue('emailsTipos', $data, []));
            $emailAtividadeSecundaria->setEmailsTipos($emailsTipos);               

        }
        return $emailAtividadeSecundaria;
    }
    
    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \App\Entities\CorpoEmail
     */
    public function getCorpoEmail()
    {
        return $this->corpoEmail;
    }

    /**
     * @param \App\Entities\CorpoEmail $corpoEmail
     */
    public function setCorpoEmail($corpoEmail)
    {
        $this->corpoEmail = $corpoEmail;
    }

    /**
     * @return \App\Entities\AtividadeSecundariaCalendario
     */
    public function getAtividadeSecundaria()
    {
        return $this->atividadeSecundaria;
    }

    /**
     * @param \App\Entities\AtividadeSecundariaCalendario $atividadesSecundarias
     */
    public function setAtividadeSecundaria($atividadesSecundarias)
    {
        $this->atividadeSecundaria = $atividadesSecundarias;
    }
    
    /**
     * @return array|ArrayCollection
     */
    public function getEmailsTipos()
    {
        return $this->emailsTipos;
    }

    /**
     * @param ArrayCollection $emailsTipos
     */
    public function setEmailsTipos($emailsTipos)
    {
        $this->emailsTipos = $emailsTipos;
    }
}

