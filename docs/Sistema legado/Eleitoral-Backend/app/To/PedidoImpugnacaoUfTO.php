<?php


namespace App\To;

use DateTime;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para os dados do pedido de impugnaçao
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class PedidoImpugnacaoUfTO
{
    /**
     * @var $id
     */
    private $id;

    /**
     * @var string $protocolo
     */
    private $protocolo;

    /**
     * @var DateTime $dataCadastro
     */
    private $dataCadastro;

    /**
     * @var int $numeroChapa
     */
    private $numeroChapa;

    /**
     * @var ProfissionalTO[] $responsaveis
     */
    private $responsaveis;

    /**
     * @var int $idStatus
     */
    private $idStatusImpugnacao;

    /**
     * @var string $status
     */
    private $statusImpugnacao;

    /**
     * @var string $nomeProfissionalInclusao
     */
    private $impugnante;

    /**
     * @var int $idCauUf
     */
    private $idCauUf;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getProtocolo(): ?string
    {
        return $this->protocolo;
    }

    /**
     * @param string $protocolo
     */
    public function setProtocolo(?string $protocolo): void
    {
        if(!empty($protocolo)) {
            $this->protocolo = str_pad($protocolo, 5, '0', STR_PAD_LEFT);
        }
    }

    /**
     * @return DateTime
     */
    public function getDataCadastro(): DateTime
    {
        return $this->dataCadastro;
    }

    /**
     * @param DateTime $dataCadastro
     */
    public function setDataCadastro(DateTime $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
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
     * @return ProfissionalTO[]
     */
    public function getResponsaveis(): array
    {
        return $this->responsaveis;
    }

    /**
     * @param ProfissionalTO[] $responsaveis
     */
    public function setResponsaveis(array $responsaveis): void
    {
        $this->responsaveis = $responsaveis;
    }

    /**
     * @return string
     */
    public function getImpugnante(): string
    {
        return $this->impugnante;
    }

    /**
     * @param string $impugnante
     */
    public function setImpugnante(string $impugnante): void
    {
        $this->impugnante = $impugnante;
    }

    /**
     * @return int
     */
    public function getIdCauUf(): int
    {
        return $this->idCauUf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf(int $idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return int
     */
    public function getIdStatusImpugnacao(): int
    {
        return $this->idStatusImpugnacao;
    }

    /**
     * @param int $idStatusImpugnacao
     */
    public function setIdStatusImpugnacao(int $idStatusImpugnacao): void
    {
        $this->idStatusImpugnacao = $idStatusImpugnacao;
    }

    /**
     * @return string
     */
    public function getStatusImpugnacao(): string
    {
        return $this->statusImpugnacao;
    }

    /**
     * @param string $statusImpugnacao
     */
    public function setStatusImpugnacao(string $statusImpugnacao): void
    {
        $this->statusImpugnacao = $statusImpugnacao;
    }

    /**
     * Retorna uma nova instância de 'PedidoImpugnacaoUfTO'.
     *
     * @param null $data
     * @return PedidoImpugnacaoUfTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $pedidoImpugnacaoUfTO = new PedidoImpugnacaoUfTO();

        if ($data != null) {
            $pedidoImpugnacaoUfTO->setId(Arr::get($data, 'id'));
            $pedidoImpugnacaoUfTO->setProtocolo(Arr::get($data, 'numeroProtocolo'));
            $pedidoImpugnacaoUfTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $pedidoImpugnacaoUfTO->setNumeroChapa(Arr::get($data, 'membroChapa.chapaEleicao.numeroChapa') ?? 'N/A');
            $pedidoImpugnacaoUfTO->setStatusImpugnacao(Arr::get($data, 'statusPedidoImpugnacao.descricao'));
            $pedidoImpugnacaoUfTO->setIdStatusImpugnacao(Arr::get($data, 'statusPedidoImpugnacao.id'));
            $pedidoImpugnacaoUfTO->setImpugnante(Arr::get($data, 'profissional.nome'));
            $pedidoImpugnacaoUfTO->setIdCauUf(Arr::get($data, 'membroChapa.chapaEleicao.idCauUf'));

            $responsaveis = [];
            $profissionaisResponsaveis = Arr::get($data, 'membroChapa.chapaEleicao.membrosChapa');
            foreach ($profissionaisResponsaveis as $profissionalResponsavel) {
                $profissionalResponsavel = Arr::get($profissionalResponsavel, 'profissional');

                $responsaveis[] = ProfissionalTO::newInstance(Arr::only($profissionalResponsavel, 'nome'));
            }
            $pedidoImpugnacaoUfTO->setResponsaveis($responsaveis);
        }

        return $pedidoImpugnacaoUfTO;
    }

}
