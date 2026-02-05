<?php
namespace app\To;

use App\Util\Utils;
use Carbon\Traits\Date;
use DateTime;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as JMS;

/**
 * Classe de transferência associada ao filtro de Atividades Principal.
 *
 * @OA\Schema(schema="AtividadePrincipalFiltroTO")
 * @author Squadra Tecnologia S/A.
 */
class AtividadePrincipalFiltroTO
{
    /**
     * ID do Calendário.
     * @var integer
     * @OA\Property()
     */
    private $idCalendario;
    
    /**
     * Data do inicio da ativida principal.
     * @var DateTime
     * @OA\Property()
     */
    private $dataInicio;
    
    /**
     * Data do fim da ativida principal.
     * @var DateTime
     * @OA\Property()
     */
    private $dataFim;
    
    /**
     * Data do inicio da ativida secundária.
     * @var DateTime
     * @OA\Property()
     */
    private $atividadeSecundariaDataInicio;
    
    /**
     * Data do fim da ativida secundária.
     * @var DateTime
     * @OA\Property()
     */
    private $atividadeSecundariaDataFim;
    
    /**
     * Fabricação estática de 'AtividadePrincipalFiltroTO'.
     * 
     * @param array $data
     * @return \app\To\AtividadePrincipalFiltroTO
     */
    public static function newInstance($data = null){
        $atividadePrincipalFiltroTO = new AtividadePrincipalFiltroTO();
        
        if(!empty($data)){
            $atividadePrincipalFiltroTO->setIdCalendario(Utils::getValue('idCalendario', $data));
            $atividadePrincipalFiltroTO->setDataInicio(Utils::getValue('dataInicio', $data));
            $atividadePrincipalFiltroTO->setDataFim(Utils::getValue('dataFim', $data));
            $atividadePrincipalFiltroTO->setAtividadeSecundariaDataInicio(Utils::getValue('atividadeSecundariaDataInicio', $data));
            $atividadePrincipalFiltroTO->setAtividadeSecundariaDataFim(Utils::getValue('atividadeSecundariaDataFim', $data));
        }
        
        return $atividadePrincipalFiltroTO;
    }
    
    /**
     * @return mixed
     */
    public function getIdCalendario()
    {
        return $this->idCalendario;
    }

    /**
     * @param mixed $idCalendario
     */
    public function setIdCalendario($idCalendario)
    {
        $this->idCalendario = $idCalendario;
    }

    /**
     * @return mixed
     */
    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    /**
     * @param mixed $dataInicio
     */
    public function setDataInicio($dataInicio)
    {
        $this->dataInicio = $dataInicio;
    }

    /**
     * @return mixed
     */
    public function getDataFim()
    {
        return $this->dataFim;
    }

    /**
     * @param mixed $dataFim
     */
    public function setDataFim($dataFim)
    {
        $this->dataFim = $dataFim;
    }

    /**
     * @return mixed
     */
    public function getAtividadeSecundariaDataInicio()
    {
        return $this->atividadeSecundariaDataInicio;
    }

    /**
     * @param mixed $atividadeSecundariaDataInicio
     */
    public function setAtividadeSecundariaDataInicio($atividadeSecundariaDataInicio)
    {
        $this->atividadeSecundariaDataInicio = $atividadeSecundariaDataInicio;
    }

    /**
     * @return mixed
     */
    public function getAtividadeSecundariaDataFim()
    {
        return $this->atividadeSecundariaDataFim;
    }

    /**
     * @param mixed $atividadeSecundariaDataFim
     */
    public function setAtividadeSecundariaDataFim($atividadeSecundariaDataFim)
    {
        $this->atividadeSecundariaDataFim = $atividadeSecundariaDataFim;
    }

}
