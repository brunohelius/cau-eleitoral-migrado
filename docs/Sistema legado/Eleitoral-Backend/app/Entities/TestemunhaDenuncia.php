<?php
/*
 * TestemunhaDenuncia.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Testemunha de Denuncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TestemunhaDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TESTEMUNHA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TestemunhaDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_TESTEMUNHA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_testemunha_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\Denuncia
     */
    private $denuncia;

    /**
     * @ORM\Column(name="NM_TESTEMUNHA", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $nome;

    /**
     * @ORM\Column(name="TEL_TESTEMUNHA", type="string", length=15, nullable=true)
     *
     * @var null|string
     */
    private $telefone;

    /**
     * @ORM\Column(name="EMAIL_TESTEMUNHO", type="string", length=200, nullable=true)
     *
     * @var null|string
     */
    private $email;

    /**
     * Fábrica de instância de 'TestemunhaDenuncia'.
     *
     * @param array $data
     * @return TestemunhaDenuncia
     */
    public static function newInstance($data = null)
    {
        $testemunha = new TestemunhaDenuncia();

        if ($data != null) {
            $testemunha->setId(Utils::getValue('id', $data));
            $testemunha->setNome(Utils::getValue('nome', $data));
            $testemunha->setTelefone(Utils::getValue('telefone', $data));

            $email = Utils::getValue('email', $data);
            if (!empty($email)) {
                $testemunha->setEmail($email);
            }

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $testemunha->setDenuncia(Denuncia::newInstance($denuncia));
            }
        }

        return $testemunha;
    }

    /**
     * @return  integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  integer  $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Denuncia
     */
    public function getDenuncia()
    {
        return $this->denuncia;
    }

    /**
     * @param Denuncia  $denuncia
     */
    public function setDenuncia(Denuncia $denuncia)
    {
        $this->denuncia = $denuncia;
    }

    /**
     * @return  string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param  string  $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return null|string
     */
    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    /**
     * @param  string  $telefone
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param  string  $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
}
