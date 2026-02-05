<?php
/*
 * AtividadeSecundariaTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Entities\AtividadePrincipalCalendario;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ChapaEleicao;
use App\Util\Utils;
use Carbon\Traits\Date;
use DateTime;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as JMS;

/**
 * Classe de transferência associada a 'Atividades Secundária'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="AtividadeSecundaria")
 */
class AtividadeSecundariaCalendarioTO
{
    /**
     * ID do Atividade Secundária
     * @var integer
     * @OA\Property()
     */
    private $id;

    /**
     * Data de início Atividade Secundária.
     * @var DateTime
     * @OA\Property()
     */
    private $dataInicio;

    /**
     * Data Fim Atividade Secundária
     * @var DateTime
     * @OA\Property()
     */
    private $dataFim;

    /**
     * Descrição de Atividade Secundária
     * @var string
     * @OA\Property()
     */
    private $descricao;

    /**
     * Nível de Atividade Secundária.
     * @var integer
     * @OA\Property()
     */
    private $nivel;

    /**
     * Fabricação estática de 'AtividadeSecundariaTO'.
     *
     * @param array|null $data
     *
     * @return AtividadeSecundariaCalendarioTO
     */
    public static function newInstance($data = null): AtividadeSecundariaCalendarioTO
    {
        $atividadeSecundariaTO = new AtividadeSecundariaCalendarioTO();

        if (!empty($data)) {
            $atividadeSecundariaTO->setId(Utils::getValue("id", $data));
            $atividadeSecundariaTO->setDataInicio(Utils::getValue("dataInicio", $data));
            $atividadeSecundariaTO->setDataFim(Utils::getValue("dataFim", $data));
            $atividadeSecundariaTO->setDescricao(Utils::getValue("descricao", $data));
            $atividadeSecundariaTO->setNivel(Utils::getValue("nivel", $data));
        }

        return $atividadeSecundariaTO;
    }

    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return DateTime
     */
    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    /**
     * @param DateTime $dataInicio
     */
    public function setDataInicio($dataInicio)
    {
        $this->dataInicio = $dataInicio;
    }

    /**
     * @return DateTime
     */
    public function getDataFim()
    {
        return $this->dataFim;
    }

    /**
     * @param DateTime $dataFim
     */
    public function setDataFim($dataFim)
    {
        $this->dataFim = $dataFim;
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
     * @return number
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * @param number $nivel
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;
    }

    /**
     * Fabricação estática de 'ChapaEleicaoTO'.
     *
     * @param AtividadeSecundariaCalendario $atividadeSecundariaCalendario
     * @return AtividadeSecundariaCalendarioTO
     */
    public static function newInstanceFromEntity(AtividadeSecundariaCalendario $atividadeSecundariaCalendario): AtividadeSecundariaCalendarioTO
    {
        $atividadePrincipalCalendarioTO = new AtividadeSecundariaCalendarioTO();

        if (!empty($atividadeSecundariaCalendario)) {
            $atividadePrincipalCalendarioTO->setId($atividadeSecundariaCalendario->getId());
            $atividadePrincipalCalendarioTO->setDataInicio($atividadeSecundariaCalendario->getDataInicio());
            $atividadePrincipalCalendarioTO->setDataFim($atividadeSecundariaCalendario->getDataFim());
            $atividadePrincipalCalendarioTO->setDescricao($atividadeSecundariaCalendario->getDescricao());
            $atividadePrincipalCalendarioTO->setNivel($atividadeSecundariaCalendario->getNivel());
        }

        return $atividadePrincipalCalendarioTO;
    }
}

