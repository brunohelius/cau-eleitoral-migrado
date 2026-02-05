<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 07/10/2019
 * Time: 14:53
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Membro Comissão Situação'.
 *
 * @ORM\Entity(repositoryClass="MembroComissaoSituacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_MEMBRO_COMISSAO_STATUS")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class MembroComissaoSituacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_MEMBRO_COMISSAO_STATUS", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_membro_comissao_situcao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\SituacaoMembroComissao")
     * @ORM\JoinColumn(name="ID_STATUS_MEMBRO_COMISSAO", referencedColumnName="ID_STATUS_MEMBRO_COMISSAO", nullable=false)
     *
     * @var \App\Entities\SituacaoMembroComissao
     */
    private $situacaoMembroComissao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\MembroComissao")
     * @ORM\JoinColumn(name="ID_MEMBRO_COMISSAO", referencedColumnName="ID_MEMBRO_COMISSAO", nullable=false)
     *
     * @var \App\Entities\MembroComissao
     */
    private $membroComissao;

    /**
     * @ORM\Column(name="DT_STATUS", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     * @ORM\OrderBy({"data" = "DESC"})
     * @var DateTime
     */
    private $data;

    /**
     * Fábrica de instância de Membro Comissão Situação.
     *
     * @param array $data
     * @return \App\Entities\MembroComissaoSituacao
     */
    public static function newInstance($data = null)
    {
        $membroComissaoSituacao = new MembroComissaoSituacao();

        if ($data != null) {
            $membroComissaoSituacao->setId(Utils::getValue('id', $data));
            $membroComissaoSituacao->setMembroComissao(Utils::getValue('membroComissao', $data));
            $membroComissaoSituacao->setData(Utils::getValue('data', $data));

            $situacaoMembroComissao = Utils::getValue('situacaoMembroComissao', $data);
            if(!empty($situacaoMembroComissao)){
                $membroComissaoSituacao->setSituacaoMembroComissao(SituacaoMembroComissao::newInstance($situacaoMembroComissao));
            }
        }
        return $membroComissaoSituacao;
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
     * @return SituacaoMembroComissao
     */
    public function getSituacaoMembroComissao()
    {
        return $this->situacaoMembroComissao;
    }

    /**
     * @param SituacaoMembroComissao $situacaoMembroComissao
     */
    public function setSituacaoMembroComissao($situacaoMembroComissao): void
    {
        $this->situacaoMembroComissao = $situacaoMembroComissao;
    }

    /**
     * @return MembroComissao
     */
    public function getMembroComissao()
    {
        return $this->membroComissao;
    }

    /**
     * @param MembroComissao $membroComissao
     */
    public function setMembroComissao($membroComissao): void
    {
        $this->membroComissao = $membroComissao;
    }

    /**
     * @return DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param DateTime $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }
}