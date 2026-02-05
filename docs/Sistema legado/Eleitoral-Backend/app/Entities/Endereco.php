<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 11/11/2019
 * Time: 15:14
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Endereco'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\EnderecoRepository")
 * @ORM\Table(schema="public", name="tb_enderecos")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class Endereco extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="id", type="integer")
     * @ORM\SequenceGenerator(sequenceName="tb_enderecos_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="uf", type="string", length=2, nullable=false)
     * @var string
     */
    private $uf;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Pessoa")
     * @ORM\JoinColumn(name="pessoa_id", referencedColumnName="id", nullable=false)
     * @var \App\Entities\Pessoa
     */
    private $pessoa;

    /**
     * Fábrica de instância de 'Endereco'.
     *
     * @param array $data
     * @return Endereco
     */
    public static function newInstance($data = null)
    {
        $endereco = new Endereco();

        if (!empty($data)) {
            $endereco->setId(Utils::getValue('id', $data));
            $endereco->setUf(Utils::getValue('uf', $data));

            $pessoa = Utils::getValue('pessoa', $data);
            if (!empty($pessoa)) {
                $endereco->setPessoa(Pessoa::newInstance($pessoa));
            }
        }
        return $endereco;
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
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * @param string $uf
     */
    public function setUf($uf): void
    {
        $this->uf = $uf;
    }

    /**
     * @return Pessoa
     */
    public function getPessoa()
    {
        return $this->pessoa;
    }

    /**
     * @param Pessoa $pessoa
     */
    public function setPessoa($pessoa): void
    {
        $this->pessoa = $pessoa;
    }
}
