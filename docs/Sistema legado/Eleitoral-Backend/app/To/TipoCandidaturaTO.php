<?php

namespace App\To;

use App\Entities\AtividadePrincipalCalendario;
use App\Entities\TipoCandidatura;
use App\Util\Utils;

/**
 * Classe de transferência associada a entidade 'TipoCandidatura'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class TipoCandidaturaTO
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $descricao;

    /**
     * Retorna uma nova instância de 'TipoCandidaturaTO'.
     *
     * @param null $data
     * @return TipoCandidaturaTO
     */
    public static function newInstance($data = null)
    {
        $tipoCandidaturaTO = new TipoCandidaturaTO();

        if ($data != null) {
            $tipoCandidaturaTO->setId(Utils::getValue('id', $data));
            $tipoCandidaturaTO->setDescricao(Utils::getValue('descricao', $data));
        }

        return $tipoCandidaturaTO;
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
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * Fabricação estática de 'TipoCandidaturaTO'.
     *
     * @param TipoCandidatura $tipoCandidatura
     * @return TipoCandidaturaTO
     */
    public static function newInstanceFromEntity(TipoCandidatura $tipoCandidatura): TipoCandidaturaTO
    {
        $tipoCandidaturaTO = new TipoCandidaturaTO();

        if (!empty($tipoCandidatura)) {
            $tipoCandidaturaTO->setId($tipoCandidatura->getId());
            $tipoCandidaturaTO->setDescricao($tipoCandidatura->getDescricao());
        }

        return $tipoCandidaturaTO;
    }
}
