<?php


namespace App\To;

use App\Entities\MembroChapa;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a visualizaçao das informaçoes do Candidato no pedido de impugnaçao
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class CandidadoPedidoImpugnacaoTO
{
    /**
     * @var string
     */
    private $nome;

    /**
     * @var string
     */
    private $registro;

    /**
     * @var string
     */
    private $tipoParticipacao;

    /**
     * @var int
     */
    private $posicaoChapa;

    /**
     * @var string
     */
    private $numeroChapa;

    /**
     * @return string
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome(?string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return string
     */
    public function getRegistro(): ?string
    {
        return $this->registro;
    }

    /**
     * @param string $registro
     */
    public function setRegistro(?string $registro): void
    {
        $this->registro = $registro;
    }

    /**
     * @return string
     */
    public function getTipoParticipacao(): ?string
    {
        return $this->tipoParticipacao;
    }

    /**
     * @param string $tipoParticipacao
     */
    public function setTipoParticipacao(?string $tipoParticipacao): void
    {
        $this->tipoParticipacao = $tipoParticipacao;
    }

    /**
     * @return int
     */
    public function getPosicaoChapa(): ?int
    {
        return $this->posicaoChapa;
    }

    /**
     * @param int $posicaoChapa
     */
    public function setPosicaoChapa(?int $posicaoChapa): void
    {
        $this->posicaoChapa = $posicaoChapa;
    }

    /**
     * @return string
     */
    public function getNumeroChapa(): ?string
    {
        return $this->numeroChapa;
    }

    /**
     * @param string $numeroChapa
     */
    public function setNumeroChapa(?string $numeroChapa): void
    {
        $this->numeroChapa = $numeroChapa;
    }

    /**
     * Retorna uma nova instância de 'CandidadoPedidoImpugnacaoTO'.
     *
     * @param null $data
     * @return CandidadoPedidoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $candidadoPedidoImpugnacaoTO = new CandidadoPedidoImpugnacaoTO();

        if ($data != null) {
            $candidadoPedidoImpugnacaoTO->setNome(Arr::get($data, 'membroChapa.profissional.nome'));
            $candidadoPedidoImpugnacaoTO->setRegistro(Arr::get($data, 'membroChapa.profissional.registroNacional'));
            $candidadoPedidoImpugnacaoTO->setTipoParticipacao(Arr::get($data, 'membroChapa.tipoParticipacaoChapa.descricao'));
            $candidadoPedidoImpugnacaoTO->setPosicaoChapa(Arr::get($data, 'membroChapa.numeroOrdem'));
            $candidadoPedidoImpugnacaoTO->setNumeroChapa(Arr::get($data, 'membroChapa.chapaEleicao.numeroChapa', 'N/A'));
        }

        return $candidadoPedidoImpugnacaoTO;
    }

    /**
     * Retorna uma nova instância de 'CandidadoPedidoImpugnacaoTO'.
     *
     * @param MembroChapa $membroChapa
     * @return CandidadoPedidoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($membroChapa = null)
    {
        $candidadoPedidoImpugnacaoTO = new CandidadoPedidoImpugnacaoTO();

        if ($membroChapa != null) {
            $candidadoPedidoImpugnacaoTO->setNome($membroChapa->getProfissional()->getNome());
            $candidadoPedidoImpugnacaoTO->setRegistro($membroChapa->getProfissional()->getRegistroNacional());
            $candidadoPedidoImpugnacaoTO->setTipoParticipacao($membroChapa->getTipoParticipacaoChapa()->getDescricao());
            $candidadoPedidoImpugnacaoTO->setPosicaoChapa($membroChapa->getNumeroOrdem());
            $candidadoPedidoImpugnacaoTO->setNumeroChapa(
                $membroChapa->getChapaEleicao()->getNumeroChapa() ?? 'N/A'
            );
        }

        return $candidadoPedidoImpugnacaoTO;
    }

}
