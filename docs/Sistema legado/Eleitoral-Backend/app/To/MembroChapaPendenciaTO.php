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
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a MembroChapaPendencia
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class MembroChapaPendenciaTO
{

    /**
     * @var integer|null $id
     */
    private $id;

    /**
     * @var TipoGenericoTO|null $tipoPendencia
     */
    private $tipoPendencia;

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
     * @return TipoGenericoTO|null
     */
    public function getTipoPendencia(): ?TipoGenericoTO
    {
        return $this->tipoPendencia;
    }

    /**
     * @param TipoGenericoTO|null $tipoPendencia
     */
    public function setTipoPendencia(?TipoGenericoTO $tipoPendencia): void
    {
        $this->tipoPendencia = $tipoPendencia;
    }

    /**
     * Retorna uma nova instância de 'MembroChapaPendenciaTO'.
     *
     * @param null $data
     * @return MembroChapaPendenciaTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $membroChapaPendenciaTO = new MembroChapaPendenciaTO();

        if ($data != null) {
            $membroChapaPendenciaTO->setId(Arr::get($data, 'id'));

            $tipoPendencia = Arr::get($data, 'tipoPendencia');
            if (!empty($tipoPendencia)) {
                $membroChapaPendenciaTO->setTipoPendencia(TipoGenericoTO::newInstance($tipoPendencia));
            }
        }

        return $membroChapaPendenciaTO;
    }

    /**
     * Retorna uma nova instância de 'MembroChapaPendenciaTO'.
     *
     * @param MembroChapaPendencia $membroChapa
     * @return MembroChapaPendenciaTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($membroChapa)
    {
        $membroChapaPendenciaTO = new MembroChapaPendenciaTO();

        if (!empty($membroChapa)) {
            $membroChapaPendenciaTO->setId($membroChapa->getId());

            $tipoPendencia = $membroChapa->getTipoPendencia();
            if (!empty($tipoPendencia)) {
                $membroChapaPendenciaTO->setTipoPendencia(
                    TipoGenericoTO::newInstance([
                        "id" => $tipoPendencia->getId(),
                        "descricao" => $tipoPendencia->getDescricao()
                    ])
                );
            }
        }

        return $membroChapaPendenciaTO;
    }

}
