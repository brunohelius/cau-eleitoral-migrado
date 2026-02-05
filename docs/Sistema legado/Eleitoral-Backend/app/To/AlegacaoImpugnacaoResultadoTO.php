<?php


namespace App\To;

use App\Config\Constants;

use App\Entities\AlegacaoImpugnacaoResultado;
use App\Entities\Profissional;
use App\Util\Utils;
use DoctrineProxies\__CG__\App\Entities\PedidoImpugnacaoResultado;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Impugnacao de Resultado.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class AlegacaoImpugnacaoResultadoTO
{
    /**
     * @var int|null $id
     */
    private $id;

    /**
     * @var string|null $descricao
     */
    private $narracao;

    /**
     * @var \DateTime|null
     */
    private $dataCadastro;

    /**
     * @var ImpugnacaoResultadoTO|null
     */
    private $pedidoImpugnacaoResultado;

    /**
     * @var int | null
     */
    private $idPedidoImpugnacaoResultado;

    /**
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivos;

    /**
     * @var int|null $idProfissional
     */
    private $idProfissional;


    /**
     * @var Profissional|null $profissional
     */
    private $profissional;

    /**
     * @var  int | null
     */
    private $numero;

    /**
     * @var  string | null
     */
    private $nomeProfissional;

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
    public function getNarracao(): ?string
    {
        return $this->narracao;
    }

    /**
     * @param string|null $narracao
     */
    public function setNarracao(?string $narracao): void
    {
        $this->narracao = $narracao;
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
     * @return ImpugnacaoResultadoTO|null
     */
    public function getPedidoImpugnacaoResultado(): ?ImpugnacaoResultadoTO
    {
        return $this->pedidoImpugnacaoResultado;
    }

    /**
     * @param ImpugnacaoResultadoTO|null $pedidoImpugnacaoResultado
     */
    public function setPedidoImpugnacaoResultado(?ImpugnacaoResultadoTO $pedidoImpugnacaoResultado): void
    {
        $this->pedidoImpugnacaoResultado = $pedidoImpugnacaoResultado;
    }

    /**
     * @return ArquivoGenericoTO[]|null
     */
    public function getArquivos(): ?array
    {
        return $this->arquivos;
    }

    /**
     * @param ArquivoGenericoTO[]|null $arquivos
     */
    public function setArquivos(?array $arquivos): void
    {
        $this->arquivos = $arquivos;
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
     * @return Profissional|null
     */
    public function getProfissional(): ?Profissional
    {
        return $this->profissional;
    }

    /**
     * @param Profissional|null $profissional
     */
    public function setProfissional(?Profissional $profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return int|null
     */
    public function getIdPedidoImpugnacaoResultado()
    {
        return $this->idPedidoImpugnacaoResultado;
    }

    /**
     * @param int|null $idPedidoImpugnacaoResultado
     */
    public function setIdPedidoImpugnacaoResultado(?int $idPedidoImpugnacaoResultado)
    {
        $this->idPedidoImpugnacaoResultado = $idPedidoImpugnacaoResultado;
    }

    /**
     * @return int|null
     */
    public function getNumero(): ?int
    {
        return $this->numero;
    }

    /**
     * @param int|null $numero
     */
    public function setNumero(?int $numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return string|null
     */
    public function getNomeProfissional(): ?string
    {
        return $this->nomeProfissional;
    }

    /**
     * @param string|null $nomeProfissional
     */
    public function setNomeProfissional(?string $nomeProfissional): void
    {
        $this->nomeProfissional = $nomeProfissional;
    }

    /**
     * Retorna uma nova instância de 'AlegacaoImpugnacaoResultadoTO'.
     *
     * @param null $data
     * @return AlegacaoImpugnacaoResultadoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $alegacaoImpugnacaoResultadoTO = new AlegacaoImpugnacaoResultadoTO();

        if ($data != null) {

            $alegacaoImpugnacaoResultadoTO->setId(Arr::get($data,'id'));
            $alegacaoImpugnacaoResultadoTO->setDataCadastro(Utils::getData());
            $alegacaoImpugnacaoResultadoTO->setNumero(Arr::get($data,'numero'));
            $alegacaoImpugnacaoResultadoTO->setNarracao(Arr::get($data,'narracao'));
            $alegacaoImpugnacaoResultadoTO->setProfissional(Arr::get($data, 'profissional'));
            $alegacaoImpugnacaoResultadoTO->setIdProfissional(Arr::get($data,'idProfissional'));
            $alegacaoImpugnacaoResultadoTO->setIdPedidoImpugnacaoResultado(Arr::get($data,'idPedidoImpugnacaoResultado'));

            $pedidoImpugnacaoResultado = Arr::get($data, 'pedidoImpugnacaoResultado');
            if(!empty($pedidoImpugnacaoResultado)) {
                $alegacaoImpugnacaoResultadoTO->setPedidoImpugnacaoResultado(ImpugnacaoResultadoTO::newInstance($pedidoImpugnacaoResultado));
            }

            $arquivos = Arr::get($data, 'arquivos', null);
            if (!empty($arquivos)) {
                $alegacaoImpugnacaoResultadoTO->setArquivos(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else {
                if (!empty(Arr::get($data, 'nomeArquivo'))) {
                    $arquivo = ArquivoGenericoTO::newInstance([
                        'nome' => Arr::get($data, 'nomeArquivo'),
                        'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                    ]);
                    $alegacaoImpugnacaoResultadoTO->setArquivos([$arquivo]);
                }
            }
        }

        return $alegacaoImpugnacaoResultadoTO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalTO'.
     *
     * @param AlegacaoImpugnacaoResultado $entity
     * @param bool $isAddDadosCauBrRetorno
     * @return AlegacaoImpugnacaoResultadoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity(AlegacaoImpugnacaoResultado $entity)
    {
        $alegacaoImpugnacaoResultadoTO = new AlegacaoImpugnacaoResultadoTO();

        if ($entity != null) {
            $alegacaoImpugnacaoResultadoTO->setId($entity->getId());
            $alegacaoImpugnacaoResultadoTO->setDataCadastro($entity->getDataCadastro());
            $alegacaoImpugnacaoResultadoTO->setNarracao($entity->getNarracao());
            $alegacaoImpugnacaoResultadoTO->setNumero($entity->getNumero());

            if (!empty($entity->getImpugnacaoResultado())) {
                $alegacaoImpugnacaoResultadoTO->setIdPedidoImpugnacaoResultado($entity->getImpugnacaoResultado()->getId());
            }
            if (!empty($entity->getProfissional())) {
                $alegacaoImpugnacaoResultadoTO->setIdProfissional($entity->getProfissional()->getId());
                $alegacaoImpugnacaoResultadoTO->setNomeProfissional($entity->getProfissional()->getNome());
            }
            if (!empty($entity->getNomeArquivo())) {
                $arquivo = ArquivoGenericoTO::newInstance([
                    'nome' => $entity->getNomeArquivo(),
                    'nomeFisico' => $entity->getNomeArquivoFisico()
                ]);
                $alegacaoImpugnacaoResultadoTO->setArquivos([$arquivo]);
            }
        }

        return $alegacaoImpugnacaoResultadoTO;
    }
}
