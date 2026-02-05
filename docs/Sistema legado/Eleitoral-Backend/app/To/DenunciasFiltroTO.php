<?php

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada ao filtro de 'Denuncia'.
 *
 * @package App\To
 * @author  Squadra Tecnologia S/A.
 **/
class DenunciasFiltroTO
{

    /**
     * @var null|array
     */
    private $idsCauUf;

    /**
     * @var null|integer
     */
    private $idPessoa;

    /**
     * @var null|integer
     */
    private $idDenuncia;

    /**
     * @var boolean
     */
    private $naoAdmitida;

    /**
     * @var null|integer
     */
    private $idEleicao;

    /**
     * Retorna uma nova instância de 'DenunciaFiltroTO'.
     *
     * @param array $params
     * @return self
     */
    public static function newInstance($params = null)
    {
        $instance = new self;

        if (!empty($params)) {
            $instance->setNaoAdmitida(Utils::getValue('naoAdmitida', $params,
                false));

            $idsCauUf = Utils::getValue('idsCauUf', $params, []);
            if (null !== $idsCauUf) {
                $instance->setIdsCauUf($idsCauUf);
            }

            $idPessoa = Utils::getValue('idPessoa', $params);
            if (null !== $idPessoa) {
                $instance->setIdPessoa($idPessoa);
            }

            $idDenuncia = Utils::getValue('idDenuncia', $params);
            if (null !== $idDenuncia) {
                $instance->setIdDenuncia($idDenuncia);
            }

            $idEleicao = Utils::getValue('idEleicao', $params);
            if (null !== $idEleicao) {
                $instance->setIdEleicao($idEleicao);
            }
        }

        return $instance;
    }

    /**
     * @return null|int
     */
    public function getIdDenuncia(): ?int
    {
        return $this->idDenuncia;
    }

    /**
     * @param int $id
     */
    public function setIdDenuncia(int $id): void
    {
        $this->idDenuncia = $id;
    }

    /**
     * @return null|array
     */
    public function getIdsCauUf(): ?array
    {
        return $this->idsCauUf;
    }

    /**
     * @param array $idsCauUf
     */
    public function setIdsCauUf(array $idsCauUf): void
    {
        $this->idsCauUf = $idsCauUf;
    }

    /**
     * @return null|int
     */
    public function getIdPessoa(): ?int
    {
        return $this->idPessoa;
    }

    /**
     * @param int $idPessoa
     */
    public function setIdPessoa(int $idPessoa): void
    {
        $this->idPessoa = $idPessoa;
    }

    /**
     * @return bool
     */
    public function isNaoAdmitida(): bool
    {
        return $this->naoAdmitida;
    }

    /**
     * @param bool $naoAdmitida
     */
    public function setNaoAdmitida(bool $naoAdmitida): void
    {
        $this->naoAdmitida = $naoAdmitida;
    }

    /**
     * @return null|int
     */
    public function getIdEleicao(): ?int
    {
        return $this->idEleicao;
    }

    /**
     * @param int $id
     */
    public function setIdEleicao(int $id): void
    {
        $this->idEleicao = $id;
    }
}
