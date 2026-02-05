<?php


namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada a pesquisa de membro chapa a ser substituto
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class MembroChapaSubstituicaoTO
{

    /**
     * @var $idProfissional
     */
    private $idProfissional;

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
     * @var integer|null
     */
    private $idChapaEleicao;

    /**
     * Retorna uma nova instância de 'MembroChapaSubstituicaoTO'.
     *
     * @param null $data
     * @return MembroChapaSubstituicaoTO
     */
    public static function newInstance($data = null)
    {
        $membroChapaSubstituicaoTO = new MembroChapaSubstituicaoTO();

        if ($data != null) {
            $membroChapaSubstituicaoTO->setNumeroOrdem(Utils::getValue('numeroOrdem', $data));
            $membroChapaSubstituicaoTO->setIdTipoMembro(Utils::getValue('idTipoMembro', $data));
            $membroChapaSubstituicaoTO->setIdProfissional(Utils::getValue('idProfissional', $data));
            $membroChapaSubstituicaoTO->setSituacaoResponsavel(Utils::getValue('situacaoResponsavel', $data));
            $membroChapaSubstituicaoTO->setIdTipoParticipacaoChapa(
                Utils::getValue('idTipoParticipacaoChapa', $data)
            );
            $membroChapaSubstituicaoTO->setIdChapaEleicao(Utils::getValue('idChapaEleicao', $data));
        }

        return $membroChapaSubstituicaoTO;
    }

    /**
     * @return mixed
     */
    public function getIdProfissional()
    {
        return $this->idProfissional;
    }

    /**
     * @param mixed $idProfissional
     */
    public function setIdProfissional($idProfissional): void
    {
        $this->idProfissional = $idProfissional;
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
    public function setIdTipoMembro($idTipoMembro): void
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
    public function setIdTipoParticipacaoChapa($idTipoParticipacaoChapa): void
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
    public function setNumeroOrdem($numeroOrdem): void
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
     * @return int|null
     */
    public function getIdChapaEleicao(): ?int
    {
        return $this->idChapaEleicao;
    }

    /**
     * @param int|null $idChapaEleicao
     */
    public function setIdChapaEleicao(?int $idChapaEleicao): void
    {
        $this->idChapaEleicao = $idChapaEleicao;
    }
}
