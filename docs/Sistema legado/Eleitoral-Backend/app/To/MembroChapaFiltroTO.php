<?php


namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada a filtros de busca associado a  'MembroChapa'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class MembroChapaFiltroTO
{

    /**
     * @var $idChapaEleicao
     */
    private $idChapaEleicao;

    /**
     * @var $idTipoMembro
     */
    private $idTipoMembro;

    /**
     * @var $idTipoParticipacaoChapa
     */
    private $idTipoParticipacaoChapa;

    /**
     * @var $numeroOrdem
     */
    private $numeroOrdem;

    /**
     * @var $situacaoResponsavel
     */
    private $situacaoResponsavel;

    /**
     * @var $idStatusParticipacaoChapa
     */
    private $idStatusParticipacaoChapa;

    /**
     * @var bool $idStatusParticipacaoChapa
     */
    private $incluirSuplenteConsulta;

    /**
     * @var integer|null
     */
    private $idCauUf;

    /**
     * @var integer|null $idCalendario
     */
    private $idCalendario;

    /**
     * Retorna uma nova instância de 'MembroChapaFiltroTO'.
     *
     * @param null $data
     * @return MembroChapaFiltroTO
     */
    public static function newInstance($data = null)
    {
        $membroChapaFiltroTO = new MembroChapaFiltroTO();

        if ($data != null) {
            $membroChapaFiltroTO->setIdCauUf(Utils::getValue('idCauUf', $data));
            $membroChapaFiltroTO->setNumeroOrdem(Utils::getValue('numeroOrdem', $data));
            $membroChapaFiltroTO->setIdTipoMembro(Utils::getValue('idTipoMembro', $data));
            $membroChapaFiltroTO->setIdCalendario(Utils::getValue('idCalendario', $data));
            $membroChapaFiltroTO->setIdChapaEleicao(Utils::getValue('idChapaEleicao', $data));
            $membroChapaFiltroTO->setSituacaoResponsavel(Utils::getValue('situacaoResponsavel', $data));
            $membroChapaFiltroTO->setIdTipoParticipacaoChapa(Utils::getValue('idTipoParticipacaoChapa', $data));
            $membroChapaFiltroTO->setIncluirSuplenteConsulta(Utils::getValue('incluirSuplenteConsulta', $data));
            $membroChapaFiltroTO->setIdStatusParticipacaoChapa(Utils::getValue('idStatusParticipacaoChapa', $data));
        }

        return $membroChapaFiltroTO;
    }

    /**
     * @return mixed
     */
    public function getIdChapaEleicao()
    {
        return $this->idChapaEleicao;
    }

    /**
     * @param mixed $idChapaEleicao
     */
    public function setIdChapaEleicao($idChapaEleicao): void
    {
        $this->idChapaEleicao = $idChapaEleicao;
    }

    /**
     * @return mixed
     */
    public function getIdTipoMembro()
    {
        return $this->idTipoMembro;
    }

    /**
     * @param mixed $idTipoMembro
     */
    public function setIdTipoMembro($idTipoMembro)
    {
        $this->idTipoMembro = $idTipoMembro;
    }

    /**
     * @return mixed
     */
    public function getIdTipoParticipacaoChapa()
    {
        return $this->idTipoParticipacaoChapa;
    }

    /**
     * @param mixed $idTipoParticipacaoChapa
     */
    public function setIdTipoParticipacaoChapa($idTipoParticipacaoChapa)
    {
        $this->idTipoParticipacaoChapa = $idTipoParticipacaoChapa;
    }

    /**
     * @return mixed
     */
    public function getNumeroOrdem()
    {
        return $this->numeroOrdem;
    }

    /**
     * @param mixed $numeroOrdem
     */
    public function setNumeroOrdem($numeroOrdem)
    {
        $this->numeroOrdem = $numeroOrdem;
    }

    /**
     * @return mixed
     */
    public function getSituacaoResponsavel()
    {
        return $this->situacaoResponsavel;
    }

    /**
     * @param mixed $situacaoResponsavel
     */
    public function setSituacaoResponsavel($situacaoResponsavel): void
    {
        $this->situacaoResponsavel = $situacaoResponsavel;
    }

    /**
     * @return mixed
     */
    public function getIdStatusParticipacaoChapa()
    {
        return $this->idStatusParticipacaoChapa;
    }

    /**
     * @param mixed $idStatusParticipacaoChapa
     */
    public function setIdStatusParticipacaoChapa($idStatusParticipacaoChapa): void
    {
        $this->idStatusParticipacaoChapa = $idStatusParticipacaoChapa;
    }

    /**
     * @return bool
     */
    public function isIncluirSuplenteConsulta(): ?bool
    {
        return $this->incluirSuplenteConsulta;
    }

    /**
     * @param bool $incluirSuplenteConsulta
     */
    public function setIncluirSuplenteConsulta(?bool $incluirSuplenteConsulta): void
    {
        $this->incluirSuplenteConsulta = $incluirSuplenteConsulta;
    }

    /**
     * @return int|null
     */
    public function getIdCauUf(): ?int
    {
        return $this->idCauUf;
    }

    /**
     * @param int|null $idCauUf
     */
    public function setIdCauUf(?int $idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return int|null
     */
    public function getIdCalendario(): ?int
    {
        return $this->idCalendario;
    }

    /**
     * @param int|null $idCalendario
     */
    public function setIdCalendario(?int $idCalendario): void
    {
        $this->idCalendario = $idCalendario;
    }
}