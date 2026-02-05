<?php
/*
 * Uf.php* Copyright (c) CAU/BR.
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
 * Entidade de representação de 'UF'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UfRepository")
 * @ORM\Table(schema="eleitoral", name="TB_UF")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class Uf extends Entity
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_UF", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_cabecalho_email_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="SG_UF", type="string", length=50)
     *
     * @var string
     */
    private $sgUf;

    /**
     * Retorna uma nova instância de 'UF'.
     *
     * @param null $data
     * @return Uf
     */
    public static function newInstance($data = null)
    {
        $uf = new Uf();

        if ($data != null) {
            $uf->setId(Utils::getValue('id', $data));
            $uf->setSgUf(Utils::getValue('sgUf', $data));
        }

        return $uf;
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
    public function getSgUf()
    {
        return $this->sgUf;
    }

    /**
     * @param string $sgUf
     */
    public function setSgUf($sgUf): void
    {
        $this->sgUf = $sgUf;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getCabecalhosEmail()
    {
        return $this->cabecalhosEmail;
    }

    /**
     * @param array|ArrayCollection $cabecalhosEmail
     */
    public function setCabecalhosEmail($cabecalhosEmail): void
    {
        $this->cabecalhosEmail = $cabecalhosEmail;
    }
}
