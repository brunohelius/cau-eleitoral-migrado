<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\MembroComissao;

/**
 * Classe de transferência TO para o tipo de conselheiro de membro comissao
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class TipoConselheiroMembroComissaoTO
{
    /**
     * @var integer
     */
    private $tipo;

    /**
     * @var string
     */
    private $descricao;

    /**
     * @var string
     */
    private $idCauUf;

    /**
     * Fabricação estática de 'TipoConselheiroMembroComissaoTO' com o MembroComissao
     *
     * @param MembroComissao $membro
     *
     * @return TipoConselheiroMembroComissaoTO
     */
    public static function newInstanceFromMembroComissao($membro = null)
    {
        $instance = new self();

        if ($membro != null) {
            $instance->setIdCauUf($membro->getIdCauUf());

            if ($membro->getIdCauUf() == Constants::COMISSAO_MEMBRO_CAU_BR_ID){
                $instance->setTipo(Constants::TIPO_MEMBRO_COMISSAO_CONSELHEIRO_CEN_BR);
                $instance->setDescricao(Constants::DESCRICAO_MEMBRO_COMISSAO_CONSELHEIRO_CEN_BR);
            }
            else {
                $instance->setTipo(Constants::TIPO_MEMBRO_COMISSAO_CONSELHEIRO_CE_UF);
                $instance->setDescricao(Constants::DESCRICAO_MEMBRO_COMISSAO_CONSELHEIRO_CE_UF);
            }
        }

        return $instance;
    }


    /**
     * @return int
     */
    public function getTipo(): int
    {
        return $this->tipo;
    }

    /**
     * @param int $tipo
     */
    public function setTipo(int $tipo): void
    {
        $this->tipo = $tipo;
    }

    /**
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return string
     */
    public function getIdCauUf(): string
    {
        return $this->idCauUf;
    }

    /**
     * @param string $idCauUf
     */
    public function setIdCauUf(string $idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }
}
