<?php


namespace App\To;

use App\Entities\JulgamentoFinal;
use App\Entities\JulgamentoRecursoImpugResultado;
use App\Entities\JulgamentoSubstituicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\StatusJulgamentoSubstituicao;
use App\Util\Utils;
use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Self_;

/**
 * Classe de transferência para a JulgamentoRecursoImpugResultadoTO
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoImpugResultadoTO
{

    /**
     * @var int|null $id
     */
    private $id;

    /**
     * @var string|null $descricao
     */
    private $descricao;

    /**
     * @var \DateTime|null
     */
    private $dataCadastro;

    /**
     * @var ImpugnacaoResultadoTO|null
     */
    private $impugnacaoResultado;

    /**
     * @var StatusGenericoTO|null
     */
    private $statusJulgamentoRecursoImpugResultado;

    /**
     * @var int|null $idStatusJulgamentoRecursoImpugResultado
     */
    private $idStatusJulgamentoRecursoImpugResultado;

    /**
     * @var int|null $idImpugnacaoResultado
     */
    private $idImpugnacaoResultado;

    /**
     * @var UsuarioTO|null $usuario
     */
    private $usuario;

    /**
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivos;

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoImpugResultadoTO'.
     *
     * @param null $data
     * @return JulgamentoRecursoImpugResultadoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoRecursoImpugResultadoTO = new JulgamentoRecursoImpugResultadoTO();

        if ($data != null) {
            $julgamentoRecursoImpugResultadoTO->setId(Arr::get($data, 'id'));
            $julgamentoRecursoImpugResultadoTO->setDescricao(Arr::get($data, 'descricao'));
            $julgamentoRecursoImpugResultadoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $julgamentoRecursoImpugResultadoTO->setIdImpugnacaoResultado(Arr::get($data, 'idImpugnacaoResultado'));
            $julgamentoRecursoImpugResultadoTO->setIdStatusJulgamentoRecursoImpugResultado(
                Arr::get($data, 'idStatusJulgamentoRecursoImpugResultado')
            );

            $impugnacaoResultado = Arr::get($data, 'impugnacaoResultado');
            if (!empty($impugnacaoResultado)) {
                $julgamentoRecursoImpugResultadoTO->setImpugnacaoResultado(ImpugnacaoResultadoTO::newInstance($impugnacaoResultado));
            }

            $status = Arr::get($data, 'statusJulgamentoRecursoImpugResultado');
            if (!empty($status)) {
                $julgamentoRecursoImpugResultadoTO->setStatusJulgamentoRecursoImpugResultado(StatusGenericoTO::newInstance($status));
            }

            $usuario = Arr::get($data, 'usuario');
            if (!empty($usuario)) {
                $julgamentoRecursoImpugResultadoTO->setUsuario(UsuarioTO::newInstance($usuario));
            }

            $arquivos = Arr::get($data, 'arquivos', null);
            if (!empty($arquivos)) {
                $julgamentoRecursoImpugResultadoTO->setArquivos(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else {
                if (!empty(Arr::get($data, 'nomeArquivo'))) {
                    $arquivo = ArquivoGenericoTO::newInstance([
                        'nome' => Arr::get($data, 'nomeArquivo'),
                        'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                    ]);
                    $julgamentoRecursoImpugResultadoTO->setArquivos([$arquivo]);
                }
            }
        }

        return $julgamentoRecursoImpugResultadoTO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoImpugResultadoTO'.
     *
     * @param JulgamentoRecursoImpugResultado $julgamentoRecursoImpugResultado
     * @param bool $isResumo
     * @return JulgamentoRecursoImpugResultadoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($julgamentoRecursoImpugResultado, $isResumo = false)
    {
        $julgamentoRecursoImpugResultadoTO = new JulgamentoRecursoImpugResultadoTO();

        if (!empty($julgamentoRecursoImpugResultado)) {
            $julgamentoRecursoImpugResultadoTO->setId($julgamentoRecursoImpugResultado->getId());
            $julgamentoRecursoImpugResultadoTO->setDescricao($julgamentoRecursoImpugResultado->getDescricao());
            $julgamentoRecursoImpugResultadoTO->setDataCadastro($julgamentoRecursoImpugResultado->getDataCadastro());

            if (!$isResumo) {

                if (!empty($julgamentoRecursoImpugResultado->getUsuario())) {
                    $julgamentoRecursoImpugResultadoTO->setUsuario(UsuarioTO::newInstanceFromEntity(
                        $julgamentoRecursoImpugResultado->getUsuario()
                    ));
                }

                $status = $julgamentoRecursoImpugResultado->getStatusJulgamentoRecursoImpugResultado();
                if (!empty($status)) {
                    $julgamentoRecursoImpugResultadoTO->setStatusJulgamentoRecursoImpugResultado(StatusGenericoTO::newInstance([
                        'id' => $status->getId(),
                        'descricao' => $status->getDescricao()
                    ]));
                }

                $arquivos = [];
                if (!empty($julgamentoRecursoImpugResultado->getNomeArquivo())) {
                    $arquivos[] = ArquivoGenericoTO::newInstance([
                        'nome' => $julgamentoRecursoImpugResultado->getNomeArquivo(),
                        'nomeFisico' => $julgamentoRecursoImpugResultado->getNomeArquivoFisico()
                    ]);
                }
                $julgamentoRecursoImpugResultadoTO->setArquivos($arquivos);
            }
        }
        return $julgamentoRecursoImpugResultadoTO;
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
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param string|null $descricao
     */
    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
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
    public function getImpugnacaoResultado(): ?ImpugnacaoResultadoTO
    {
        return $this->impugnacaoResultado;
    }

    /**
     * @param ImpugnacaoResultadoTO|null $impugnacaoResultado
     */
    public function setImpugnacaoResultado(?ImpugnacaoResultadoTO $impugnacaoResultado): void
    {
        $this->impugnacaoResultado = $impugnacaoResultado;
    }

    /**
     * @return StatusGenericoTO|null
     */
    public function getStatusJulgamentoRecursoImpugResultado(): ?StatusGenericoTO
    {
        return $this->statusJulgamentoRecursoImpugResultado;
    }

    /**
     * @param StatusGenericoTO|null $statusJulgamentoRecursoImpugResultado
     */
    public function setStatusJulgamentoRecursoImpugResultado(?StatusGenericoTO $statusJulgamentoRecursoImpugResultado
    ): void {
        $this->statusJulgamentoRecursoImpugResultado = $statusJulgamentoRecursoImpugResultado;
    }

    /**
     * @return int|null
     */
    public function getIdStatusJulgamentoRecursoImpugResultado(): ?int
    {
        return $this->idStatusJulgamentoRecursoImpugResultado;
    }

    /**
     * @param int|null $idStatusJulgamentoRecursoImpugResultado
     */
    public function setIdStatusJulgamentoRecursoImpugResultado(?int $idStatusJulgamentoRecursoImpugResultado): void
    {
        $this->idStatusJulgamentoRecursoImpugResultado = $idStatusJulgamentoRecursoImpugResultado;
    }

    /**
     * @return int|null
     */
    public function getIdImpugnacaoResultado(): ?int
    {
        return $this->idImpugnacaoResultado;
    }

    /**
     * @param int|null $idImpugnacaoResultado
     */
    public function setIdImpugnacaoResultado(?int $idImpugnacaoResultado): void
    {
        $this->idImpugnacaoResultado = $idImpugnacaoResultado;
    }

    /**
     * @return UsuarioTO|null
     */
    public function getUsuario(): ?UsuarioTO
    {
        return $this->usuario;
    }

    /**
     * @param UsuarioTO|null $usuario
     */
    public function setUsuario(?UsuarioTO $usuario): void
    {
        $this->usuario = $usuario;
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
     * Retorna o arquivo se existir
     * @return ArquivoGenericoTO|mixed|null
     */
    public function getArquivo()
    {
        return (!empty($this->arquivos) && is_array($this->arquivos)) ? $this->arquivos[0] : null;
    }
}
