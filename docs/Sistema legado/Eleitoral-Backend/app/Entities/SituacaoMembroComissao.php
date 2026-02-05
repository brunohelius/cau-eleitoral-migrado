<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 07/10/2019
 * Time: 14:41
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Situação do Membro da Comissão'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\SituacaoMembroComissaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_STATUS_MEMBRO_COMISSAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class SituacaoMembroComissao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_STATUS_MEMBRO_COMISSAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_status_membro_comissao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_STATUS_MEMBRO_COMISSAO", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de Situação do Membro da Comissão'.
     *
     * @param array $data
     * @return \App\Entities\SituacaoMembroComissao
     */
    public static function newInstance($data = null)
    {
        $situacaoMembroComissao = new SituacaoMembroComissao();

        if ($data != null) {
            $situacaoMembroComissao->setId(Utils::getValue('id', $data));
            $situacaoMembroComissao->setDescricao(Utils::getValue('descricao', $data));
        }
        return $situacaoMembroComissao;
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