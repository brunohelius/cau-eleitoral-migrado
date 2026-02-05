<?php

namespace App\To;

use App\Entities\MembroComissao;

/**
 * Classe de transferência associada ao 'Membro de Comissao'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class MembroComissaoTO
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var ProfissionalTO
     */
    private $profissional;

    /**
     * @var MembroComissaoTO
     */
    private $membroSubstituto;

    /**
     * Retorna uma nova instância de 'MembroComissaoTO'.
     *
     * @param MembroComissao $membroComissao
     * @return self
     */
    public static function newInstanceFromEntity($membroComissao = null)
    {
        $instance = new self;

        if (null !== $membroComissao) {
            $instance->setId($membroComissao->getId());
            $instance->setProfissional(
                ProfissionalTO::newInstanceFromEntity($membroComissao->getProfissionalEntity())
            );

            if(!empty($membroComissao->getMembroSubstituto()) && !is_null($membroComissao->getMembroSubstituto())) {
                $instance->setMembroSubstituto(
                    MembroComissaoTO::newInstanceFromEntity($membroComissao->getMembroSubstituto())
                );
            }
        }

        return $instance;
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
     * @return ProfissionalTO
     */
    public function getProfissional(): ProfissionalTO
    {
        return $this->profissional;
    }

    /**
     * @param ProfissionalTO $profissional
     */
    public function setProfissional($profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return MembroComissaoTO
     */
    public function getMembroSubstituto(): ?MembroComissaoTO
    {
        return $this->membroSubstituto;
    }

    /**
     * @param MembroComissaoTO $membroSubstituto
     */
    public function setMembroSubstituto(MembroComissaoTO $membroSubstituto): void
    {
        $this->membroSubstituto = $membroSubstituto;
    }


}
