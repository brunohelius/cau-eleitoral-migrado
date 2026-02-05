<?php

namespace App\To;

use App\Config\Constants;
use App\Util\Utils;
use DateTime;
use Illuminate\Support\Arr;

/**
 * Classe de transferência associada a 'HistoricoChapaEleicao'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class HistoricoChapaEleicaoTO
{
    /**
     * @var DateTime
     */
    private $data;

    /**
     * @var integer
     */
    private $idUsuario;

    /**
     * @var ChapaEleicaoTO
     */
    private $chapaEleicao;

    /**
     * @var string
     */
    private $nomeUsuario;

    /**
     * @var string
     */
    private $descricaoAcao;

    /**
     * @var string
     */
    private $descricaoOrigem;

    /**
     * @var string
     */
    private $descricaoJustificativa;

    /**
     * Fabricação estática de 'HistoricoChapaEleicaoTO'.
     *
     * @param array|null $data
     *
     * @return HistoricoChapaEleicaoTO
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setData(Utils::getValue("data", $data));
            $instance->setIdUsuario(Utils::getValue("idUsuario", $data));
            $instance->setDescricaoAcao(Utils::getValue("descricaoAcao", $data));
            $instance->setDescricaoOrigem(Utils::getValue("descricaoOrigem", $data));
            $instance->setDescricaoJustificativa(Utils::getValue("descricaoJustificativa", $data));

            $chapaEleicao = Utils::getValue("chapaEleicao", $data);
            $instance->setChapaEleicao(ChapaEleicaoTO::newInstance($chapaEleicao));

            if($instance->getDescricaoOrigem() == Constants::ORIGEM_PROFISSIONAL) {
                $profissional = Utils::getValue('profissional', $data);
                if (!empty($profissional)) {
                    $instance->setNomeUsuario(Arr::get($profissional, 'nome'));
                }
            }
            else if($instance->getDescricaoOrigem() == Constants::ORIGEM_CORPORATIVO) {
                $usuario = Utils::getValue('usuario', $data);
                if (!empty($usuario)) {
                    $instance->setNomeUsuario(Arr::get($usuario, 'nome'));
                }
            }

            $nomeUsuario = Utils::getValue("nomeUsuario", $data);
            if (!empty($nomeUsuario)) {
                $instance->setNomeUsuario($nomeUsuario);
            }
        }

        return $instance;
    }

    /**
     * @return DateTime
     */
    public function getData(): ?DateTime
    {
        return $this->data;
    }

    /**
     * @param DateTime $data
     */
    public function setData(?DateTime $data): void
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getIdUsuario(): ?int
    {
        return $this->idUsuario;
    }

    /**
     * @param int $idUsuario
     */
    public function setIdUsuario(?int $idUsuario): void
    {
        $this->idUsuario = $idUsuario;
    }

    /**
     * @return ChapaEleicaoTO
     */
    public function getChapaEleicao(): ?ChapaEleicaoTO
    {
        return $this->chapaEleicao;
    }

    /**
     * @param ChapaEleicaoTO $chapaEleicao
     */
    public function setChapaEleicao(?ChapaEleicaoTO $chapaEleicao): void
    {
        $this->chapaEleicao = $chapaEleicao;
    }

    /**
     * @return string
     */
    public function getNomeUsuario(): ?string
    {
        return $this->nomeUsuario;
    }

    /**
     * @param string $nomeUsuario
     */
    public function setNomeUsuario(?string $nomeUsuario): void
    {
        $this->nomeUsuario = $nomeUsuario;
    }

    /**
     * @return string
     */
    public function getDescricaoJustificativa(): ?string
    {
        return $this->descricaoJustificativa;
    }

    /**
     * @param string $descricaoJustificativa
     */
    public function setDescricaoJustificativa(?string $descricaoJustificativa): void
    {
        $this->descricaoJustificativa = $descricaoJustificativa;
    }

    /**
     * @return string
     */
    public function getDescricaoAcao(): ?string
    {
        return $this->descricaoAcao;
    }

    /**
     * @param string $descricaoAcao
     */
    public function setDescricaoAcao(?string $descricaoAcao): void
    {
        $this->descricaoAcao = $descricaoAcao;
    }

    /**
     * @return string
     */
    public function getDescricaoOrigem(): ?string
    {
        return $this->descricaoOrigem;
    }

    /**
     * @param string $descricaoOrigem
     */
    public function setDescricaoOrigem(?string $descricaoOrigem): void
    {
        $this->descricaoOrigem = $descricaoOrigem;
    }
}
