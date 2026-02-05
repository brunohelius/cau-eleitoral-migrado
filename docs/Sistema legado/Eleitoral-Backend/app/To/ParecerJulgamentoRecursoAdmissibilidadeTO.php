<?php

namespace App\To;

use App\Entities\ParecerJulgamentoRecursoAdmissibilidade;
use App\Util\Utils;
use OpenApi\Annotations as OA;

/**
 * Class ParecerJulgamentoRecursoAdmissibilidade
 * @package App\To
 *
 * @OA\Schema(schema="ParecerJulgamentoRecursoAdmissibilidade")
 */
class ParecerJulgamentoRecursoAdmissibilidadeTO
{
    /**
     * ID
     * @var integer
     * @OA\Property()
     */
    private $id;

    /**
     * Descricao
     * @var string
     * @OA\Property()
     */
    private $descricao;


    /**
     * Retorna uma nova instância de 'ArquivoDenunciaTO'.
     *
     * @param ArquivoDenuncia $arquivoDenuncia
     * @return self
     */
    public static function newInstanceFromEntity(ParecerJulgamentoRecursoAdmissibilidade $parecerJulgamentoRecursoAdmissibilidade = null)
    {
        $instance = new self;

        if ($parecerJulgamentoRecursoAdmissibilidade != null) {
            $instance->setId($parecerJulgamentoRecursoAdmissibilidade->getId());
            $instance->setDescricao($parecerJulgamentoRecursoAdmissibilidade->getDescricao());
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
     * @return ParecerJulgamentoRecursoAdmissibilidadeTO
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * @return ParecerJulgamentoRecursoAdmissibilidadeTO
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }


}