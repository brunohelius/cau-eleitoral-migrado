<?php


namespace App\To;

use App\Config\Constants;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para Pedidos Solicitados
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class PedidoSolicitadoTO
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $protocolo;

    /**
     * @var StatusGenericoTO|null
     */
    private $status;

    /**
     * @var int|null
     */
    private $idTipoDenuncia;

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
     * @return string|null
     */
    public function getProtocolo(): ?string
    {
        return $this->protocolo;
    }

    /**
     * @param string|null $protocolo
     */
    public function setProtocolo(?string $protocolo): void
    {
        $this->protocolo = '';
        if (!empty($protocolo)) {
            $this->protocolo = str_pad($protocolo, 5, '0', STR_PAD_LEFT);
        }
    }

    /**
     * @return StatusGenericoTO|null
     */
    public function getStatus(): ?StatusGenericoTO
    {
        return $this->status;
    }

    /**
     * @return int|null
     */
    public function getIdTipoDenuncia(): ?int
    {
        return $this->idTipoDenuncia;
    }

    /**
     * @param int|null $idTipoDenuncia
     */
    public function setIdTipoDenuncia(?int $idTipoDenuncia): void
    {
        $this->idTipoDenuncia = $idTipoDenuncia;
    }

    /**
     * Retorna uma nova instância de 'PedidoSolicitadoTO'.
     *
     * @param null $data
     * @return PedidoSolicitadoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $pedidoSolicitadoTO = new PedidoSolicitadoTO();

        if ($data != null) {
            $pedidoSolicitadoTO->setId(Arr::get($data, 'id'));
            $pedidoSolicitadoTO->setProtocolo(Arr::get($data, 'protocolo'));
            $pedidoSolicitadoTO->setProtocolo(Arr::get($data, 'numeroProtocolo'));
            $pedidoSolicitadoTO->setIdTipoDenuncia(Arr::get($data, 'idTipoDenuncia'));

            $idStatus = Arr::get($data, 'idStatus');
            $descricaoStatus = Arr::get($data, 'descricaoStatus');
            if (!empty($idStatus)) {
                $pedidoSolicitadoTO->setStatus($idStatus, $descricaoStatus);
            }
        }

        return $pedidoSolicitadoTO;
    }

    /**
     *
     */
    public function setStatusEmAnalise()
    {
        $this->setStatus(
            Constants::ID_STATUS_PEDIDO_CHAPA_EN_ANALISE,
            Constants::DS_STATUS_PEDIDO_CHAPA_EN_ANALISE
        );
    }

    /**
     *
     */
    public function setStatusProcedente()
    {
        $this->setStatus(
            Constants::ID_STATUS_PEDIDO_CHAPA_PROCEDENTE,
            Constants::DS_STATUS_PEDIDO_CHAPA_PROCEDENTE
        );
    }

    /**
     *
     */
    public function setStatusImprocedente()
    {
        $this->setStatus(
            Constants::ID_STATUS_PEDIDO_CHAPA_IMPROCEDENTE,
            Constants::DS_STATUS_PEDIDO_CHAPA_IMPROCEDENTE
        );
    }

    /**
     *
     */
    public function setStatusDeferido()
    {
        $this->setStatus(
            Constants::ID_STATUS_PEDIDO_CHAPA_DEFERIDO,
            Constants::DS_STATUS_PEDIDO_CHAPA_DEFERIDO
        );
    }

    /**
     *
     */
    public function setStatusIndeferido()
    {
        $this->setStatus(
            Constants::ID_STATUS_PEDIDO_CHAPA_INDEFERIDO,
            Constants::DS_STATUS_PEDIDO_CHAPA_INDEFERIDO
        );
    }

    /**
     * @param $id
     * @param $descricao
     */
    public function setStatus($id, $descricao): void
    {
        $this->status = StatusGenericoTO::newInstance(compact("id", "descricao"));
    }

}
