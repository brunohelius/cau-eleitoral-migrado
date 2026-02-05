<?php

namespace App\To;

use App\Util\Utils;
use Carbon\Traits\Date;
use DateTime;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as JMS;

/**
 * Classe de transferência associada ao 'AtividadePrincipalCalendario'.
 *
 * @OA\Schema(schema="AtividadePrincipalCalendarioTO")
 */
class AtividadePrincipalCalendarioTO
{
    /**
     * ID do Atividade Principal
     * @var integer
     * @OA\Property()
     */
    private $id;

    /**
     * Data de início Atividade Principal.
     * @var DateTime
     * @OA\Property()
     */
    private $dataInicio;

    /**
     * Data Fim Atividade Principal
     * @var DateTime
     * @OA\Property()
     */
    private $dataFim;

    /**
     * Classe de trasferência associada ao 'Calendário'
     * @var \App\To\CalendarioTO
     * @OA\Property()
     */
    private $calendario;
    
    /**
     * Descrição de Atividade Principal
     * @var string
     * @OA\Property()
     */
    private $descricao;

    /**
     *  Informa se a atividade principal obedece vigência
     * @var boolean
     * @OA\Property()
     */
    private $obedeceVigencia;
    
    /**
     * Classe de trasferência associada a 'Atividade Secundária'
     * @var \App\To\AtividadeSecundariaTO
     * @OA\Property()
     */
    private $atividadesSecundarias;

    /**
     * Nível de Atividade Principal.
     * @var integer
     * @OA\Property()
     */
    private $nivel;

    /**
     * Fabricação estática de 'AtividadePrincipalTO'.
     *
     * @param array|null $data
     *
     * @return AtividadePrincipalCalendarioTO
     */
    public static function newInstance($data = null)
    {
        $atividadePrincipalTO = new AtividadePrincipalCalendarioTO();
        
        if ($data != null) {
            $atividadePrincipalTO->setId(Utils::getValue("id", $data));
            $atividadePrincipalTO->setDataInicio(Utils::getValue("dataInicio", $data));
            $atividadePrincipalTO->setDataFim(Utils::getValue("dataFim", $data));
            $atividadePrincipalTO->setCalendario(Utils::getValue("calendario",$data));
            $atividadePrincipalTO->setDescricao(Utils::getValue("descricao",$data));
            $atividadePrincipalTO->setObedeceVigencia(Utils::getValue("obedeceVigencia", $data));
            $atividadePrincipalTO->setNivel(Utils::getValue("nivel", $data));

            $atividadesSecundarias = Utils::getValue('atividadesSecundarias', $data);
            if (!empty($atividadesSecundarias)) {
                foreach ($atividadesSecundarias as $dataAtividadeSecundaria) {
                    $atividadePrincipalTO->adicionarAtividadeSecundaria(AtividadeSecundariaTO::newInstance($dataAtividadeSecundaria));
                }
            }

            $calendarioData = Utils::getValue("calendario", $data);
            if(!empty($calendarioData)){
                $atividadePrincipalTO->setCalendario(CalendarioTO::newInstance($calendarioData));
            }

        }
        return $atividadePrincipalTO;
    }

    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
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
     * @param $dataInicio
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
     * @return $dataFim
     */
    public function setDataFim($dataFim)
    {
        $this->dataFim = $dataFim;
    }
    
    /**
     * @return \App\To\CalendarioTO

     */
    public function getCalendario()
    {
        return $this->calendario;
    }

    /**
     * @param CalendarioTO $calendario
     */
    public function setCalendario($calendario)
    {
        $this->calendario = $calendario;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @param boolean $obedeceVigencia
     */
    public function setObedeceVigencia($obedeceVigencia)
    {
        $this->obedeceVigencia = $obedeceVigencia;
    }

    /**
     * @return AtividadeSecundariaTO
     */
    public function getAtividadesSecundarias()
    {
        return $this->atividadesSecundarias;
    }

    /**
     * @param AtividadeSecundariaTO $atividadesSecundarias
     */
    public function setAtividadesSecundarias($atividadesSecundarias): void
    {
        $this->atividadesSecundarias = $atividadesSecundarias;
    }

    /**
     * Adicionar uma Atividade Secundária ao Array de Atividade Secundárias.
     * @param \App\To\AtividadeSecundariaTO $atividadesSecundaria
     */
    public function adicionarAtividadeSecundaria($atividadesSecundaria){
        if(!$this->atividadesSecundarias){
            $this->atividadesSecundarias = [];
        }
        array_push($this->atividadesSecundarias, $atividadesSecundaria);
    }
    
    /**
     * @param number $nivel
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;
    }

    /**
     * @return int
     */
    public function getNivel()
    {
        return $this->nivel;
    }
}

