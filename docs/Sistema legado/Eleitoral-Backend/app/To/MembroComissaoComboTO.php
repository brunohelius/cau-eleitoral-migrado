<?php


namespace App\To;

use App\Entities\MembroComissao;
use App\Entities\Pessoa;
use App\Util\Utils;

/**
 * Classe que estrutura os dados para combos e autocomplites
 * Class MembroComissaoComboTO
 * @package App\To
 */
class MembroComissaoComboTO
{
    private $id;
    private $pessoa;
    private $profissional;

    /**
     * Instancia a classe por um array
     * @param $data
     * @return MembroComissaoComboTO
     */
    public static function newInstance($data) {
        $membroComissaoComboTO = new MembroComissaoComboTO();
        if(!empty($data)) {
            $membroComissaoComboTO->setId(Utils::getValue('id', $data));
            if(!empty($data['profissional'])) {
                $membroComissaoComboTO->setProfissional(ProfissionalTO::newInstance($data['profissional']));
            }
            if(!empty($data['pessoa'])) {
                $membroComissaoComboTO->setPessoa(Pessoa::newInstance($data['pessoa']));
            }
        }
        return $membroComissaoComboTO;
    }

    /**
     * Instancia a classe pela entidade MembroComissao
     * @param MembroComissao $membroComissao
     * @return MembroComissaoComboTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($membroComissao) {
        $membroComissaoComboTO = new MembroComissaoComboTO();
        if(!empty($membroComissao)) {
            $membroComissaoComboTO->setId($membroComissao->getId());
            if(!empty($membroComissao->getProfissionalEntity())) {
                $membroComissaoComboTO->setProfissional(ProfissionalTO::newInstanceFromEntity($membroComissao->getProfissionalEntity()));
            }
            if(!empty($membroComissao->getPessoaEntity())) {
                $membroComissaoComboTO->setPessoa(Pessoa::newInstance($membroComissao->getPessoaEntity()));
            }
        }
        return $membroComissaoComboTO;
    }
    /**
     * @return mixed
     */
    public function getProfissional()
    {
        return $this->profissional;
    }

    /**
     * @param mixed $profissional
     */
    public function setProfissional($profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPessoa()
    {
        return $this->pessoa;
    }

    /**
     * @param mixed $pessoa
     */
    public function setPessoa($pessoa): void
    {
        $this->pessoa = $pessoa;
    }
}
