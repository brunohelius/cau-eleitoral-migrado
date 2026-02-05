<?php


namespace App\To;


use App\Entities\DenunciaAdmitida;
use App\Entities\MembroComissao;
use Doctrine\Common\Collections\ArrayCollection;

class AnaliseAdmissibilidadeTO
{
    /**
     * @var DenunciaAdmitida|null
     */
    private $denunciaAdmitida;
    /**
     * @var DenunciaInadmitidaTO|null
     */
    private $denunciaInadmitida;
    /**
     * @var array|ArrayCollection|null
     */
    private $historicoAdmissao;

    /**
     * @var MembroComissao[]|null
     */
    private $coordenadores;

    /**
     * Fabrica de instancias de AnaliseAdmissibilidadeTO
     * @param $data
     * @return AnaliseAdmissibilidadeTO
     */
    public static function newInstance($data) {
        $analiseAdmissibilidadeTO = new AnaliseAdmissibilidadeTO();
        if(!empty($data)){
            $analiseAdmissibilidadeTO->setDenunciaAdmitida($data['denuncia_admitida']);
            $analiseAdmissibilidadeTO->setDenunciaInadmitida($data['denuncia_inadmitida']);
            $analiseAdmissibilidadeTO->setHistoricoAdmissao($data['historico_admissao']);
            $analiseAdmissibilidadeTO->setCoordenadores($data['coordenadores']);
        }
        return $analiseAdmissibilidadeTO;
    }

    /**
     * @return DenunciaAdmitida|null
     */
    public function getDenunciaAdmitida(): ?DenunciaAdmitida
    {
        return $this->denunciaAdmitida;
    }

    /**
     * @param DenunciaAdmitida|null $denunciaAdmitida
     */
    public function setDenunciaAdmitida(?DenunciaAdmitida $denunciaAdmitida): void
    {
        $this->denunciaAdmitida = $denunciaAdmitida;
    }

    /**
     * @return DenunciaInadmitidaTO|null
     */
    public function getDenunciaInadmitida(): ?DenunciaInadmitidaTO
    {
        return $this->denunciaInadmitida;
    }

    /**
     * @param DenunciaInadmitidaTO|null $denunciaInadmitida
     */
    public function setDenunciaInadmitida(?DenunciaInadmitidaTO $denunciaInadmitida): void
    {
        $this->denunciaInadmitida = $denunciaInadmitida;
    }

    /**
     * @return array|ArrayCollection|null
     */
    public function getHistoricoAdmissao()
    {
        return $this->historicoAdmissao;
    }

    /**
     * @param array|ArrayCollection|null $historicoAdmissao
     */
    public function setHistoricoAdmissao($historicoAdmissao): void
    {
        $this->historicoAdmissao = $historicoAdmissao;
    }

    /**
     * @return MembroComissao[]|null
     */
    public function getCoordenadores(): ?array
    {
        return $this->coordenadores;
    }

    /**
     * @param MembroComissao[]|null $coordenadores
     */
    public function setCoordenadores(?array $coordenadores): void
    {
        $this->coordenadores = $coordenadores;
    }

}
