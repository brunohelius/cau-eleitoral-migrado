<?php
/*
 * CorpoEmail.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Corpo de E-mail'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\CorpoEmailRepository")
 * @ORM\Table(schema="eleitoral", name="TB_CORPO_EMAIL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class CorpoEmail extends Entity
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_CORPO_EMAIL", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_corpo_email_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_ASSUNTO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $assunto;

    /**
     * @ORM\Column(name="DS_CORPO_EMAIL", type="text")
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="ST_ATIVO", type="boolean")
     *
     * @var boolean
     */
    private $ativo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\EmailAtividadeSecundaria", mappedBy="corpoEmail", fetch="EXTRA_LAZY")
     *
     * @var array|ArrayCollection
     */
    private $emailsAtividadeSecundaria;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\CabecalhoEmail")
     * @ORM\JoinColumn(name="ID_CABECALHO_EMAIL", referencedColumnName="ID_CABECALHO_EMAIL", nullable=false)
     *
     * @var \App\Entities\CabecalhoEmail
     */
    private $cabecalhoEmail;

    /**
     * Retorna uma nova instância de 'CorpoEmail'.
     *
     * @param null $data
     * @return CorpoEmail
     */
    public static function newInstance($data = null)
    {
        $corpoEmail = new CorpoEmail();

        if ($corpoEmail != null) {
            $cabecalhoEmail = Utils::getValue('cabecalhoEmail', $data);         
            $array = Utils::getValue('emailsAtividadeSecundaria', $data, []);  
            $emailsAtividadeSecundaria = array_map(function($emailAtividadeSecundaria){
                return EmailAtividadeSecundaria::newInstance($emailAtividadeSecundaria);
            }, $array);
            $corpoEmail->setEmailsAtividadeSecundaria($emailsAtividadeSecundaria);
            
            $corpoEmail->setCabecalhoEmail(empty($cabecalhoEmail) ? null : CabecalhoEmail::newInstance($cabecalhoEmail));
            $corpoEmail->setId(Utils::getValue('id', $data));
            $corpoEmail->setAtivo(Utils::getValue('ativo', $data));
            $corpoEmail->setAssunto(Utils::getValue('assunto', $data));
            $corpoEmail->setDescricao(Utils::getValue('descricao', $data));
        }

        return $corpoEmail;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAssunto()
    {
        return $this->assunto;
    }

    /**
     * @param string $assunto
     */
    public function setAssunto($assunto)
    {
        $this->assunto = $assunto;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return bool
     */
    public function isAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param bool $ativo
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }


    /**
     * @return mixed
     */
    public function getCabecalhoEmail()
    {
        return $this->cabecalhoEmail;
    }

    /**
     * @param mixed $cabecalhoEmail
     */
    public function setCabecalhoEmail($cabecalhoEmail)
    {
        $this->cabecalhoEmail = $cabecalhoEmail;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getEmailsAtividadeSecundaria()
    {
        return $this->emailsAtividadeSecundaria;
    }

    /**
     * @param  <array, \Doctrine\Common\Collections\ArrayCollection> $emailsAtividadeSecundaria
     */
    public function setEmailsAtividadeSecundaria($emailsAtividadeSecundaria)
    {
        $this->emailsAtividadeSecundaria = $emailsAtividadeSecundaria;
    }

}
