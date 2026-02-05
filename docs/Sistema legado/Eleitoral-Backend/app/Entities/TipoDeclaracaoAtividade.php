<?php
namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade que representa tipo declaração de atividades secundárias.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoDeclaracaoAtividadeRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TP_DECLARACAO_ATIVIDADE")
 */
class TipoDeclaracaoAtividade extends Entity
{
    /**
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Id
     * @ORM\Column(name="ID_TP_DECLARACAO_ATIVIDADE", type="integer")
     * @var integer
     */
    private $id;
    
    /**
     * @ORM\Column(name="DS_TP_DECLARACAO_ATIVIDADE", type="string", length=200, nullable=false)
     * @var string
     */
    private $descricao;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entities\DeclaracaoAtividade", mappedBy="tipoDeclaracaoAtividade")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $declaracoesAtividade;
    
    /**
     *  Fábrica de instância de tipo declaração de atividades secundárias.
     * @param array $data
     * @return \app\Entities\TipoDeclaracaoAtividade
     */
    public static function newInstance($data = null)
    {
        $tipoDeclaracaoAtividadeSecundaria = new TipoDeclaracaoAtividade();
        if(!empty($data)){
            $tipoDeclaracaoAtividadeSecundaria->setId(Utils::getValue('id', $data));
            $tipoDeclaracaoAtividadeSecundaria->setDescricao(Utils::getValue('descricao', $data));
            $declaracoes = Utils::getValue('declaracoesAtividade', $data);
            if(!empty($declaracoes)){
                $tipoDeclaracaoAtividadeSecundaria->setDeclaracoesAtividade( array_map(function($declaracao){
                    return DeclaracaoAtividade::newInstance($declaracao);
                }, $declaracoes));
            }          
        }
        return $tipoDeclaracaoAtividadeSecundaria;
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

    /**
     * @return array|ArrayCollection
     */
    public function getDeclaracoesAtividade()
    {
        return $this->declaracoesAtividade;
    }

    /**
     * @param array|ArrayCollection $declaracoesAtividade
     */
    public function setDeclaracoesAtividade($declaracoesAtividade)
    {
        $this->declaracoesAtividade = $declaracoesAtividade;
    }
}

