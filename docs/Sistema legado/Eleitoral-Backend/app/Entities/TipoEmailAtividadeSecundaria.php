<?php
namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade que representa tipos e-mails de atividades secundárias.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoEmailAtividadeSecundariaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TP_CORPO_EMAIL",indexes={@ORM\Index(name="tb_tp_corpo_email_id_tp_corpo_email_idx", columns={"id"})}  )
 */
class TipoEmailAtividadeSecundaria extends Entity
{
    /**
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Id
     * @ORM\Column(name="ID_TP_CORPO_EMAIL", type="integer")
     * @var integer
     */
    private $id;
    
    /**
     * @ORM\Column(name="DS_CORPO_EMAIL", type="string", length=200, nullable=false) 
     * @var string
     */
    private $descricao;     
    
    /**
     *  Fábrica de instância de tipo e-mails de atividades secundárias.
     * @param array $data
     * @return \app\Entities\TipoEmailAtividadeSecundaria
     */
    public static function newInstance($data = null)
    {
        $tipoEmailAtividadeSecundaria = new TipoEmailAtividadeSecundaria();
        if(!empty($data)){
            $tipoEmailAtividadeSecundaria->setId(Utils::getValue('id', $data));
            $tipoEmailAtividadeSecundaria->setDescricao(Utils::getValue('descricao', $data));                   
        }
        return $tipoEmailAtividadeSecundaria;
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

}

