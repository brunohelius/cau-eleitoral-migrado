<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 07/10/2019
 * Time: 11:07
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Tipo de Participação do Membro'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoParticipacaoMembroRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TIPO_PARTICIPACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TipoParticipacaoMembro extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_TIPO_PARTICIPACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_tipo_participacao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TIPO_PARTICIPACAO", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de Tipo de Participação do Membro'.
     *
     * @param array $data
     * @return \App\Entities\TipoParticipacaoMembro
     */
    public static function newInstance($data = null)
    {
        $tipoParticipacao = new TipoParticipacaoMembro();

        if ($data != null) {
            $tipoParticipacao->setId(Utils::getValue('id', $data));
            $tipoParticipacao->setDescricao(Utils::getValue('descricao', $data));
        }
        return $tipoParticipacao;
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
    public function setId($id): void
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
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }
}