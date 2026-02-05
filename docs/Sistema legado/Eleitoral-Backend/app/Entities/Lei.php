<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 07/11/2019
 * Time: 12:01
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Lei'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\LeiRepository")
 * @ORM\Table(schema="eleitoral", name="TB_LEI")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class Lei extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_LEI", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_LEI_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_LEI", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'Lei'.
     *
     * @param array $data
     * @return \App\Entities\Lei
     */
    public static function newInstance($data = null)
    {
        $lei = new Lei();

        if ($data != null) {
            $lei->setId(Utils::getValue('id', $data));
            $lei->setDescricao(Utils::getValue('descricao', $data));
        }
        return $lei;
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