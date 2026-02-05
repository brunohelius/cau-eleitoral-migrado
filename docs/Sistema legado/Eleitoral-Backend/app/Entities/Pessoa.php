<?php
/*
 * ProfissionalRegistro.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
    
namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Profissional'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\PessoaRepository")
 * @ORM\Table(schema="public", name="tb_pessoa")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class Pessoa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="id", type="integer")
     * @ORM\SequenceGenerator(sequenceName="tb_pessoa_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Profissional", mappedBy="pessoa" , fetch="EXTRA_LAZY")
     *
     * @var \App\Entities\Profissional
     */
    private $profissional;

    /**
     * @ORM\Column(name="email", type="string", nullable=false)
     * @var string
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Endereco")
     * @ORM\JoinColumn(name="enderecocorrespondencia", referencedColumnName="id", nullable=false)
     * @var \App\Entities\Endereco
     */
    private $endereco;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\Conselheiro", mappedBy="pessoa")
     *
     * @var array|ArrayCollection
     */
    private $conselheiro;

    /**
     * Fábrica de instância de 'Pessoa'.
     *
     * @param array $data
     * @return \App\Entities\Pessoa
     */
    public static function newInstance($data = null)
    {
        $pessoa = new Pessoa();
        
        if ($data != null) {
            $pessoa->setId(Utils::getValue('id', $data));
            $pessoa->setEmail(Utils::getValue('email', $data));

            $profissional = Utils::getValue('profissional', $data);
            if (!empty($profissional)) {
                $pessoa->setProfissional(Profissional::newInstance($profissional));
            }

            $endereco = Utils::getValue('endereco', $data);
            if (!empty($endereco)) {
                $pessoa->setEndereco(Endereco::newInstance($endereco));
            }
        }

        return $pessoa;
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
     * @return Profissional
     */
    public function getProfissional()
    {
        return $this->profissional;
    }

    /**
     * @param Profissional $profissional
     */
    public function setProfissional($profissional)
    {
        $this->profissional = $profissional;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return Endereco
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * @param Endereco $endereco
     */
    public function setEndereco($endereco): void
    {
        $this->endereco = $endereco;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getConselheiro()
    {
        return $this->conselheiro;
    }

    /**
     * @param array|ArrayCollection $conselheiro
     */
    public function setConselheiro($conselheiro): void
    {
        $this->conselheiro = $conselheiro;
    }
}
