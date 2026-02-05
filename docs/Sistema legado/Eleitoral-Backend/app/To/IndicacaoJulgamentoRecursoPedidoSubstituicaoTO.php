<?php


namespace App\To;

use Illuminate\Support\Arr;
use App\Entities\IndicacaoJulgamentoFinal;

/**
 * Classe de transferência para a Indicação do Julgamento Segunda Instancia do Recurso de Pedido de Substituição.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class IndicacaoJulgamentoRecursoPedidoSubstituicaoTO
{

    /**
     * @var integer|null $id
     */
    private $id;

    /**
     * @var integer|null $numeroOrdem
     */
    private $numeroOrdem;

    /**
     * @var TipoGenericoTO
     */
    private $tipoParticipacaoChapa;

    /**
     * @var MembroChapaTO|null
     */
    private $membroChapa;

    /**
     * @var integer|null
     */
    private $idTipoParicipacaoChapa;

    /**
     * @var integer|null
     */
    private $idMembroChapa;

    /**
     * @var integer|null
     */
    private $idJulgamentoSegundaInstanciaSubstituicao;

    /**
     * Retorna uma nova instância de 'IndicacaoJulgamentoRecursoPedidoSubstituicaoTO'.
     *
     * @param null $data
     * @return IndicacaoJulgamentoRecursoPedidoSubstituicaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $indicacaoJulgamentoFinalTO = new IndicacaoJulgamentoRecursoPedidoSubstituicaoTO();

        if ($data != null) {
            $indicacaoJulgamentoFinalTO->setId(Arr::get($data, 'id'));
            $indicacaoJulgamentoFinalTO->setNumeroOrdem(Arr::get($data, 'numeroOrdem'));
            $indicacaoJulgamentoFinalTO->setIdMembroChapa(Arr::get($data, 'idMembroChapa'));
            $indicacaoJulgamentoFinalTO->setIdTipoParicipacaoChapa(Arr::get($data, 'idTipoParicipacaoChapa'));
            $indicacaoJulgamentoFinalTO->setIdJulgamentoSegundaInstanciaSubstituicao(Arr::get($data, 'idJulgamentoSegundaInstanciaSubstituicao'));

            $tipoParticipacaoChapa = Arr::get($data, 'tipoParticipacaoChapa');
            if (!empty($tipoParticipacaoChapa)) {
                $indicacaoJulgamentoFinalTO->setTipoParticipacaoChapa(TipoGenericoTO::newInstance($tipoParticipacaoChapa));
            }

            $membroChapa = Arr::get($data, 'membroChapa');
            if (!empty($membroChapa)) {
                $indicacaoJulgamentoFinalTO->setMembroChapa(MembroChapaTO::newInstance($membroChapa));
            }
        }

        return $indicacaoJulgamentoFinalTO;
    }

    /**
     * Retorna uma nova instância de 'IndicacaoJulgamentoRecursoPedidoSubstituicaoTO'.
     *
     * @param IndicacaoJulgamentoSegundaInstanciaRecursoTO $indicacaoJulgamentoFinal
     * @return IndicacaoJulgamentoRecursoPedidoSubstituicaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($indicacaoJulgamentoFinal)
    {
        $indicacaoJulgamentoFinalTO = new IndicacaoJulgamentoRecursoPedidoSubstituicaoTO();

        if (!empty($indicacaoJulgamentoFinal)) {
            $indicacaoJulgamentoFinalTO->setId($indicacaoJulgamentoFinal->getId());
            $indicacaoJulgamentoFinalTO->setNumeroOrdem($indicacaoJulgamentoFinal->getNumeroOrdem());

            $tipoParticipacao = $indicacaoJulgamentoFinal->getTipoParticipacaoChapa();
            if (!empty($tipoParticipacao)) {
                $indicacaoJulgamentoFinalTO->setTipoParticipacaoChapa(TipoGenericoTO::newInstance([
                    'id' => $tipoParticipacao->getId(),
                    'descricao' => $tipoParticipacao->getDescricao(),
                ]));
            }

            $membroChapa = $indicacaoJulgamentoFinal->getMembroChapa();
            if (!empty($membroChapa)) {
                $indicacaoJulgamentoFinalTO->setMembroChapa(MembroChapaTO::newInstanceFromEntity(
                    $membroChapa, false, true
                ));
            }
        }

        return $indicacaoJulgamentoFinalTO;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getNumeroOrdem()
    {
        return $this->numeroOrdem;
    }

    /**
     * @param int|null $numeroOrdem
     */
    public function setNumeroOrdem($numeroOrdem)
    {
        $this->numeroOrdem = $numeroOrdem;
    }

    /**
     * @return TipoGenericoTO
     */
    public function getTipoParticipacaoChapa()
    {
        return $this->tipoParticipacaoChapa;
    }

    /**
     * @param TipoGenericoTO $tipoParticipacaoChapa
     */
    public function setTipoParticipacaoChapa($tipoParticipacaoChapa)
    {
        $this->tipoParticipacaoChapa = $tipoParticipacaoChapa;
    }

    /**
     * @return MembroChapaTO|null
     */
    public function getMembroChapa()
    {
        return $this->membroChapa;
    }

    /**
     * @param MembroChapaTO|null $membroChapa
     */
    public function setMembroChapa($membroChapa)
    {
        $this->membroChapa = $membroChapa;
    }

    /**
     * @return int|null
     */
    public function getIdTipoParicipacaoChapa()
    {
        return $this->idTipoParicipacaoChapa;
    }

    /**
     * @param int|null $idTipoParicipacaoChapa
     */
    public function setIdTipoParicipacaoChapa($idTipoParicipacaoChapa)
    {
        $this->idTipoParicipacaoChapa = $idTipoParicipacaoChapa;
    }

    /**
     * @return int|null
     */
    public function getIdMembroChapa()
    {
        return $this->idMembroChapa;
    }

    /**
     * @param int|null $idMembroChapa
     */
    public function setIdMembroChapa($idMembroChapa)
    {
        $this->idMembroChapa = $idMembroChapa;
    }

    /**
     * @return int|null
     */
    public function getIdJulgamentoSegundaInstanciaSubstituicao()
    {
        return $this->idJulgamentoSegundaInstanciaSubstituicao;
    }

    /**
     * @param int|null $idJulgamentoSegundaInstanciaSubstituicao
     */
    public function setIdJulgamentoSegundaInstanciaSubstituicao($idJulgamentoSegundaInstanciaSubstituicao)
    {
        $this->idJulgamentoSegundaInstanciaSubstituicao = $idJulgamentoSegundaInstanciaSubstituicao;
    }

}
