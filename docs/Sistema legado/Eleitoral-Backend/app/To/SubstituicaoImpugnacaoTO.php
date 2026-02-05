<?php


namespace App\To;

use App\Config\Constants;
use App\Entities\ChapaEleicao;
use App\Entities\MembroChapa;
use App\Entities\PedidoSubstituicaoChapa;
use App\Entities\StatusSubstituicaoChapa;
use App\Entities\SubstituicaoImpugnacao;
use App\Util\Utils;
use Illuminate\Support\Facades\Log;

/**
 * Classe de transferência para os dados do substituição impugnação
 *
 *
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class SubstituicaoImpugnacaoTO
{
    /**
     *
     * @var integer|null
     */
    private $id;

    /**
     *
     * @var \DateTime|null
     */
    private $dataCadastro;

    /**
     * @var ProfissionalTO|null
     */
    private $profissional;

    /**
     * @var MembroChapaTO|null
     */
    private $membroChapaSubstituto;

    /**
     * @var PedidoImpugnacaoTO|null
     */
    private $pedidoImpugnacao;

    /**
     * @var integer|null
     */
    private $idProfissional;

    /**
     * @var integer|null
     */
    private $idPedidoImpugnacao;

    /**
     * Retorna uma nova instância de 'SubstituicaoImpugnacaoTO'.
     *
     * @param null $data
     * @return SubstituicaoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $substituicaoImpugnacaoTO = new SubstituicaoImpugnacaoTO();

        if ($data != null) {
            $substituicaoImpugnacaoTO->setId(Utils::getValue('id', $data));
            $substituicaoImpugnacaoTO->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $substituicaoImpugnacaoTO->setIdProfissional(Utils::getValue('idProfissional', $data));
            $substituicaoImpugnacaoTO->setIdPedidoImpugnacao(Utils::getValue('idPedidoImpugnacao', $data));

            $membroChapaSubstituto = Utils::getValue('membroChapaSubstituto', $data);
            if (!empty($membroChapaSubstituto)) {
                $substituicaoImpugnacaoTO->setMembroChapaSubstituto(MembroChapaTO::newInstance($membroChapaSubstituto));
            }

            $pedidoImpugnacao = Utils::getValue('pedidoImpugnacao', $data);
            if (!empty($pedidoImpugnacao)) {
                $substituicaoImpugnacaoTO->setPedidoImpugnacao(PedidoImpugnacaoTO::newInstance($pedidoImpugnacao));
            }

            $profissional = Utils::getValue('profissional', $data);
            if (!empty($profissional)) {
                $substituicaoImpugnacaoTO->setProfissional(ProfissionalTO::newInstance($profissional));
            }
        }

        return $substituicaoImpugnacaoTO;
    }

    /**
     * Retorna uma nova instância de 'SubstituicaoImpugnacaoTO'.
     *
     * @param SubstituicaoImpugnacao $substituicaoImpugnacao
     * @return SubstituicaoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($substituicaoImpugnacao, $isResumo = true)
    {
        $substituicaoImpugnacaoTO = new SubstituicaoImpugnacaoTO();

        if (!empty($substituicaoImpugnacao)) {
            $substituicaoImpugnacaoTO->setId($substituicaoImpugnacao->getId());
            $substituicaoImpugnacaoTO->setDataCadastro($substituicaoImpugnacao->getDataCadastro());

            $membroChapaSubstituto = $substituicaoImpugnacao->getMembroChapaSubstituto();
            if (!empty($membroChapaSubstituto)) {
                $substituicaoImpugnacaoTO->setMembroChapaSubstituto(
                    MembroChapaTO::newInstanceFromEntity($membroChapaSubstituto)
                );
            }

            if (!$isResumo) {
                if (!empty($substituicaoImpugnacao->getPedidoImpugnacao())) {
                    $substituicaoImpugnacaoTO->setPedidoImpugnacao(PedidoImpugnacaoTO::newInstanceFromEntity(
                        $substituicaoImpugnacao->getPedidoImpugnacao(), false
                    ));
                }

                if (!empty($substituicaoImpugnacao->getProfissional())) {
                    $substituicaoImpugnacaoTO->setProfissional(ProfissionalTO::newInstanceFromEntity(
                        $substituicaoImpugnacao->getProfissional()
                    ));
                }
            }
        }

        return $substituicaoImpugnacaoTO;
    }

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
     * @return \DateTime|null
     */
    public function getDataCadastro(): ?\DateTime
    {
        return $this->dataCadastro;
    }

    /**
     * @param \DateTime|null $dataCadastro
     */
    public function setDataCadastro(?\DateTime $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
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
     * @return MembroChapaTO|null
     */
    public function getMembroChapaSubstituto(): ?MembroChapaTO
    {
        return $this->membroChapaSubstituto;
    }

    /**
     * @param MembroChapaTO|null $membroChapaSubstituto
     */
    public function setMembroChapaSubstituto(?MembroChapaTO $membroChapaSubstituto): void
    {
        $this->membroChapaSubstituto = $membroChapaSubstituto;
    }

    /**
     * @return PedidoImpugnacaoTO|null
     */
    public function getPedidoImpugnacao(): ?PedidoImpugnacaoTO
    {
        return $this->pedidoImpugnacao;
    }

    /**
     * @param PedidoImpugnacaoTO|null $pedidoImpugnacao
     */
    public function setPedidoImpugnacao(?PedidoImpugnacaoTO $pedidoImpugnacao): void
    {
        $this->pedidoImpugnacao = $pedidoImpugnacao;
    }

    /**
     * @return int|null
     */
    public function getIdProfissional(): ?int
    {
        return $this->idProfissional;
    }

    /**
     * @param int|null $idProfissional
     */
    public function setIdProfissional(?int $idProfissional): void
    {
        $this->idProfissional = $idProfissional;
    }

    /**
     * @return int|null
     */
    public function getIdPedidoImpugnacao(): ?int
    {
        return $this->idPedidoImpugnacao;
    }

    /**
     * @param int|null $idPedidoImpugnacao
     */
    public function setIdPedidoImpugnacao(?int $idPedidoImpugnacao): void
    {
        $this->idPedidoImpugnacao = $idPedidoImpugnacao;
    }
}
