<?php
/*
 * CabecalhoEmailUf.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Arquivo Calendario'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\CabecalhoEmailUfRepository")
 * @ORM\Table(schema="eleitoral", name="TB_CABECALHO_EMAIL_UF")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class CabecalhoEmailUf extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_CABECALHO_EMAIL_UF", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_cabecalho_email_uf_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\CabecalhoEmail")
     * @ORM\JoinColumn(name="ID_CABECALHO_EMAIL", referencedColumnName="ID_CABECALHO_EMAIL", nullable=false)
     *
     * @var \App\Entities\CabecalhoEmail
     */
    private $cabecalhoEmail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Uf")
     * @ORM\JoinColumn(name="ID_UF", referencedColumnName="ID_UF", nullable=false)
     *
     * @var \App\Entities\Uf
     */
    private $uf;


    /**
     * Fábrica de instância de 'Arquivo Calendario'.
     *
     * @param array $data
     * @return CabecalhoEmailUf
     */
    public static function newInstance($data = null)
    {
        $cabecalhoEmailUf = new CabecalhoEmailUf();

        if ($data != null) {
            $cabecalhoEmailUf->setId(Utils::getValue('id', $data));
            $cabecalhoEmail = CabecalhoEmail::newInstance(Utils::getValue('cabecalhoEmail', $data));
            $cabecalhoEmailUf->setCabecalhoEmail($cabecalhoEmail);
            $uf = Uf::newInstance(Utils::getValue('uf', $data));
            $cabecalhoEmailUf->setUf($uf);
        }

        return $cabecalhoEmailUf;
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
     * @return CabecalhoEmail
     */
    public function getCabecalhoEmail()
    {
        return $this->cabecalhoEmail;
    }

    /**
     * @param CabecalhoEmail $cabecalhoEmail
     */
    public function setCabecalhoEmail($cabecalhoEmail)
    {
        $this->cabecalhoEmail = $cabecalhoEmail;
    }

    /**
     * @return Uf
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * @param Uf $uf
     */
    public function setUf($uf)
    {
        $this->uf = $uf;
    }
}
