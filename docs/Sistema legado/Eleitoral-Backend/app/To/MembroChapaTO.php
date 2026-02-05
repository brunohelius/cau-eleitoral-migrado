<?php


namespace App\To;

use App\Entities\JulgamentoImpugnacao;
use App\Entities\JulgamentoSubstituicao;
use App\Entities\MembroChapa;
use App\Entities\MembroChapaPendencia;
use App\Entities\PedidoImpugnacao;
use App\Entities\RecursoImpugnacao;
use App\Entities\StatusJulgamentoImpugnacao;
use App\Entities\StatusJulgamentoSubstituicao;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Recurso de Impugnação
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class MembroChapaTO
{

    /**
     * @var integer|null $id
     */
    private $id;

    /**
     * @var integer|null
     */
    private $numeroOrdem;

    /**
     * @var boolean|null
     */
    private $situacaoResponsavel;

    /**
     * @var ProfissionalTO|null $profissional
     */
    private $profissional;

    /**
     * @var StatusGenericoTO|null
     */
    private $statusValidacaoMembroChapa;

    /**
     * @var StatusGenericoTO|null
     */
    private $statusParticipacaoChapa;

    /**
 * @var TipoGenericoTO|null
 */
    private $tipoParticipacaoChapa;

    /**
     * @var TipoGenericoTO|null
     */
    private $tipoMembroChapa;

    /**
     * @var MembroChapaPendenciaTO[]|null
     */
    private $pendencias;

    private $cpf;

    private $sexo;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return ProfissionalTO|null
     */
    public function getProfissional(): ?ProfissionalTO
    {
        return $this->profissional;
    }

    /**
     * @param ProfissionalTO|null $profissional
     */
    public function setProfissional(?ProfissionalTO $profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return StatusGenericoTO|null
     */
    public function getStatusValidacaoMembroChapa(): ?StatusGenericoTO
    {
        return $this->statusValidacaoMembroChapa;
    }

    /**
     * @param StatusGenericoTO|null $statusValidacaoMembroChapa
     */
    public function setStatusValidacaoMembroChapa(?StatusGenericoTO $statusValidacaoMembroChapa): void
    {
        $this->statusValidacaoMembroChapa = $statusValidacaoMembroChapa;
    }

    /**
     * @return StatusGenericoTO|null
     */
    public function getStatusParticipacaoChapa(): ?StatusGenericoTO
    {
        return $this->statusParticipacaoChapa;
    }

    /**
     * @param StatusGenericoTO|null $statusParticipacaoChapa
     */
    public function setStatusParticipacaoChapa(?StatusGenericoTO $statusParticipacaoChapa): void
    {
        $this->statusParticipacaoChapa = $statusParticipacaoChapa;
    }

    /**
     * @return TipoGenericoTO|null
     */
    public function getTipoParticipacaoChapa(): ?TipoGenericoTO
    {
        return $this->tipoParticipacaoChapa;
    }

    /**
     * @param TipoGenericoTO|null $tipoParticipacaoChapa
     */
    public function setTipoParticipacaoChapa(?TipoGenericoTO $tipoParticipacaoChapa): void
    {
        $this->tipoParticipacaoChapa = $tipoParticipacaoChapa;
    }

    /**
     * @return MembroChapaPendenciaTO[]|null
     */
    public function getPendencias(): ?array
    {
        return $this->pendencias;
    }

    /**
     * @param MembroChapaPendenciaTO[]|null $pendencias
     */
    public function setPendencias(?array $pendencias): void
    {
        $this->pendencias = $pendencias;
    }

    /**
     * @return TipoGenericoTO|null
     */
    public function getTipoMembroChapa(): ?TipoGenericoTO
    {
        return $this->tipoMembroChapa;
    }

    /**
     * @param TipoGenericoTO|null $tipoMembroChapa
     */
    public function setTipoMembroChapa(?TipoGenericoTO $tipoMembroChapa): void
    {
        $this->tipoMembroChapa = $tipoMembroChapa;
    }

    /**
     * @return int|null
     */
    public function getNumeroOrdem(): ?int
    {
        return $this->numeroOrdem;
    }

    /**
     * @param int|null $numeroOrdem
     */
    public function setNumeroOrdem(?int $numeroOrdem): void
    {
        $this->numeroOrdem = $numeroOrdem;
    }

    /**
     * @return bool|null
     */
    public function getSituacaoResponsavel(): ?bool
    {
        return $this->situacaoResponsavel;
    }


    /**
     * @param bool|null $situacaoResponsavel
     */
    public function setSituacaoResponsavel(?bool $situacaoResponsavel): void
    {
        $this->situacaoResponsavel = $situacaoResponsavel;
    }

    /**
     * Retorna uma nova instância de 'MembroChapaTO'.
     *
     * @param null $data
     * @return MembroChapaTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $membroChapaTO = new MembroChapaTO();

        if ($data != null) {
            $membroChapaTO->setId(Arr::get($data, 'id'));
            $membroChapaTO->setNumeroOrdem(Arr::get($data, 'numeroOrdem'));

            $profissional = Arr::get($data, 'profissional');
            if (!empty($profissional)) {
                $membroChapaTO->setProfissional(ProfissionalTO::newInstance($profissional));
            }

            $statusValidacaoMembroChapa = Arr::get($data, 'statusValidacaoMembroChapa');
            if (!empty($statusValidacaoMembroChapa)) {
                $membroChapaTO->setStatusValidacaoMembroChapa(StatusGenericoTO::newInstance($statusValidacaoMembroChapa));
            }

            $statusParticipacaoChapa = Arr::get($data, 'statusParticipacaoChapa');
            if (!empty($statusParticipacaoChapa)) {
                $membroChapaTO->setStatusParticipacaoChapa(
                    StatusGenericoTO::newInstance($statusParticipacaoChapa)
                );
            }

            $tipoParticipacaoChapa = Arr::get($data, 'tipoParticipacaoChapa');
            if (!empty($tipoParticipacaoChapa)) {
                $membroChapaTO->setTipoParticipacaoChapa(
                    TipoGenericoTO::newInstance($tipoParticipacaoChapa)
                );
            }

            $pendencias = Utils::getValue('pendencias', $data);
            if (!empty($pendencias)) {
                $membroChapaTO->setPendencias(array_map(function ($pendencia) {
                    return MembroChapaPendenciaTO::newInstance($pendencia);
                }, $pendencias));
            }
        }

        return $membroChapaTO;
    }

    /**
     * Retorna uma nova instância de 'MembroChapaTO'.
     *
     * @param MembroChapa $membroChapa
     * @param bool $isResumo
     * @param bool $isResumoProfissional
     * @return MembroChapaTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($membroChapa, $isResumo = false, $isResumoProfissional = false)
    {
        $membroChapaTO = new MembroChapaTO();

        if (!empty($membroChapa)) {
            $membroChapaTO->setId($membroChapa->getId());
            $membroChapaTO->setNumeroOrdem($membroChapa->getNumeroOrdem());
            $membroChapaTO->setSituacaoResponsavel($membroChapa->isSituacaoResponsavel());
            $membroChapaTO->setCpf($membroChapa->getProfissional()->getCpf());
            $membroChapaTO->setSexo($membroChapa->getProfissional()->getSexo());
            
            $profissional = $membroChapa->getProfissional();
            if (!empty($profissional)) {
                $membroChapaTO->setProfissional(ProfissionalTO::newInstanceFromEntity($profissional,
                    $isResumoProfissional));
            }

            $statusValidacaoMembroChapa = $membroChapa->getStatusValidacaoMembroChapa();
            if (!empty($statusValidacaoMembroChapa)) {
                $membroChapaTO->setStatusValidacaoMembroChapa(
                    StatusGenericoTO::newInstance([
                        "id" => $statusValidacaoMembroChapa->getId(),
                        "descricao" => $statusValidacaoMembroChapa->getDescricao()
                    ])
                );
            }

            $statusParticipacaoChapa = $membroChapa->getStatusParticipacaoChapa();
            if (!empty($statusParticipacaoChapa)) {
                $membroChapaTO->setStatusParticipacaoChapa(
                    StatusGenericoTO::newInstance([
                        "id" => $statusParticipacaoChapa->getId(),
                        "descricao" => $statusParticipacaoChapa->getDescricao()
                    ])
                );
            }

            $tipoParticipacaoChapa = $membroChapa->getTipoParticipacaoChapa();
            if (!empty($tipoParticipacaoChapa)) {
                $membroChapaTO->setTipoParticipacaoChapa(TipoGenericoTO::newInstance([
                    "id" => $tipoParticipacaoChapa->getId(),
                    "descricao" => $tipoParticipacaoChapa->getDescricao()
                ]));
            }

            $tipoMembroChapa = $membroChapa->getTipoMembroChapa();
            if (!empty($tipoMembroChapa)) {
                $membroChapaTO->setTipoMembroChapa(TipoGenericoTO::newInstance([
                    "id" => $tipoMembroChapa->getId(),
                    "descricao" => $tipoMembroChapa->getDescricao()
                ]));
            }

            if (!$isResumo) {
                $pendencias = $membroChapa->getPendencias();
                if (!empty($pendencias)) {
                    $pendenciasTO = [];
                    foreach ($pendencias as $pendencia) {
                        $pendenciasTO[] = MembroChapaPendenciaTO::newInstanceFromEntity($pendencia);
                    }
                    $membroChapaTO->setPendencias($pendenciasTO);
                }
            }
        }

        return $membroChapaTO;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(?string $cpf): void
    {
        $this->cpf = $cpf;
    }    

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(?string $sexo): void
    {
        $this->sexo = $sexo;
    }    

}
