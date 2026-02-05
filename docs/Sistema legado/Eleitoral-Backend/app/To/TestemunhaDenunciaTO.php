<?php

namespace App\To;

use App\Entities\TestemunhaDenuncia;

/**
 * Classe de transferência associada a visualização de 'Testemunhas' da
 * denúncia.
 *
 * @package App\To
 * @author  Squadra Tecnologia S/A.
 **/
class TestemunhaDenunciaTO
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nome;

    /**
     * @var null|string
     */
    private $telefone;

    /**
     * @var null|string
     */
    private $email;

    /**
     * Retorna uma nova instância de 'TestemunhaDenunciaTO'.
     *
     * @param TestemunhaDenuncia $testemunha
     * @return self
     */
    public static function newInstanceFromEntity($testemunha = null)
    {
        $instance = new self;

        if ($testemunha != null) {
            $instance->setId($testemunha->getId());
            $instance->setNome($testemunha->getNome());

            $email = $testemunha->getEmail();
            if(null !== $email) {
                $instance->setEmail($email);
            }

            $telefone = $testemunha->getTelefone();
            if(null !== $telefone) {
                $instance->setTelefone($telefone);
            }
        }

        return $instance;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome(string $nome): void
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
     * @param string $telefone
     */
    public function setTelefone(string $telefone): void
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
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
