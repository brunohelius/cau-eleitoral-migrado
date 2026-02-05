<?php


namespace App\To;

use App\Entities\JulgamentoAlegacaoImpugResultado;
use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Classe de transferência para a JulgamentoAlegacaoImpugResultado
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoAlegacaoImpugResultadoTO
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
    private $statusJulgamentoAlegacaoResultado;

    /**
     * @var int|null $idStatusJulgamentoAlegacaoResultado
     */
    private $idStatusJulgamentoAlegacaoResultado;

    /**
     * @var int|null $idImpugnacaoResultado
     */
    private $idImpugnacaoResultado;

    /**
     * @var boolean|null $isIES
     */
    private $isIES;

    /**
     * @var boolean | null
     */
    private $hasRecursoPorResponsavelEChapa;

    /**
     * @var UsuarioTO|null $usuario
     */
    private $usuario;

    /**
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivos;

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
    public function getStatusJulgamentoAlegacaoResultado(): ?StatusGenericoTO
    {
        return $this->statusJulgamentoAlegacaoResultado;
    }

    /**
     * @param StatusGenericoTO|null $statusJulgamentoAlegacaoResultado
     */
    public function setStatusJulgamentoAlegacaoResultado(?StatusGenericoTO $statusJulgamentoAlegacaoResultado): void
    {
        $this->statusJulgamentoAlegacaoResultado = $statusJulgamentoAlegacaoResultado;
    }

    /**
     * @return int|null
     */
    public function getIdStatusJulgamentoAlegacaoResultado(): ?int
    {
        return $this->idStatusJulgamentoAlegacaoResultado;
    }

    /**
     * @param int|null $idStatusJulgamentoAlegacaoResultado
     */
    public function setIdStatusJulgamentoAlegacaoResultado(?int $idStatusJulgamentoAlegacaoResultado): void
    {
        $this->idStatusJulgamentoAlegacaoResultado = $idStatusJulgamentoAlegacaoResultado;
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
     * @return bool|null
     */
    public function getIsIES(): ?bool
    {
        return $this->isIES;
    }

    /**
     * @param bool|null $isIES
     */
    public function setIsIES(?bool $isIES): void
    {
        $this->isIES = $isIES;
    }

    /**
     * @return bool|null
     */
    public function getHasRecursoPorResponsavelEChapa(): ?bool
    {
        return $this->hasRecursoPorResponsavelEChapa;
    }

    /**
     * @param bool|null $hasRecursoPorResponsavelEChapa
     */
    public function setHasRecursoPorResponsavelEChapa(?bool $hasRecursoPorResponsavelEChapa): void
    {
        $this->hasRecursoPorResponsavelEChapa = $hasRecursoPorResponsavelEChapa;
    }



    /**
     * Retorna uma nova instância de 'JulgamentoAlegacaoImpugResultadoTO'.
     *
     * @param null $data
     * @return JulgamentoAlegacaoImpugResultadoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoAlegacaoImpugResultadoTO = new JulgamentoAlegacaoImpugResultadoTO();

        if ($data != null) {
            $julgamentoAlegacaoImpugResultadoTO->setId(Arr::get($data, 'id'));
            $julgamentoAlegacaoImpugResultadoTO->setDescricao(Arr::get($data, 'descricao'));
            $julgamentoAlegacaoImpugResultadoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $julgamentoAlegacaoImpugResultadoTO->setIdImpugnacaoResultado(Arr::get($data, 'idImpugnacaoResultado'));
            $julgamentoAlegacaoImpugResultadoTO->setIdStatusJulgamentoAlegacaoResultado(
                Arr::get($data, 'idStatusJulgamentoAlegacaoResultado')
            );

            $impugnacaoResultado = Arr::get($data, 'impugnacaoResultado');
            if (!empty($impugnacaoResultado)) {
                $julgamentoAlegacaoImpugResultadoTO->setImpugnacaoResultado(ImpugnacaoResultadoTO::newInstance(
                    $impugnacaoResultado
                ));
            }

            $status = Arr::get($data, 'statusJulgamentoAlegacaoResultado');
            if (!empty($status)) {
                $julgamentoAlegacaoImpugResultadoTO->setStatusJulgamentoAlegacaoResultado(StatusGenericoTO::newInstance(
                    $status
                ));
            }

            $usuario = Arr::get($data, 'usuario');
            if (!empty($usuario)) {
                $julgamentoAlegacaoImpugResultadoTO->setUsuario(UsuarioTO::newInstance($usuario));
            }

            $arquivos = Arr::get($data, 'arquivos', null);
            if (!empty($arquivos)) {
                $julgamentoAlegacaoImpugResultadoTO->setArquivos(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else {
                if (!empty(Arr::get($data, 'nomeArquivo'))) {
                    $arquivo = ArquivoGenericoTO::newInstance([
                        'nome' => Arr::get($data, 'nomeArquivo'),
                        'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                    ]);
                    $julgamentoAlegacaoImpugResultadoTO->setArquivos([$arquivo]);
                }
            }
        }

        return $julgamentoAlegacaoImpugResultadoTO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalTO'.
     *
     * @param JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado
     * @param bool $isResumo
     * @return JulgamentoAlegacaoImpugResultadoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($julgamentoAlegacaoImpugResultado, $isResumo = false)
    {
        $julgamentoAlegacaoImpugResultadoTO = new JulgamentoAlegacaoImpugResultadoTO();

        if (!empty($julgamentoAlegacaoImpugResultado)) {
            $julgamentoAlegacaoImpugResultadoTO->setId($julgamentoAlegacaoImpugResultado->getId());
            $julgamentoAlegacaoImpugResultadoTO->setDescricao($julgamentoAlegacaoImpugResultado->getDescricao());
            $julgamentoAlegacaoImpugResultadoTO->setDataCadastro($julgamentoAlegacaoImpugResultado->getDataCadastro());

            if (!$isResumo) {

                $status = $julgamentoAlegacaoImpugResultado->getStatusJulgamentoAlegacaoResultado();
                if (!empty($status)) {
                    $julgamentoAlegacaoImpugResultadoTO->setStatusJulgamentoAlegacaoResultado(
                        StatusGenericoTO::newInstance([
                            'id' => $status->getId(),
                            'descricao' => $status->getDescricao()
                        ])
                    );
                }

                $usuario = $julgamentoAlegacaoImpugResultado->getUsuario();
                if (!empty($usuario)) {
                    $julgamentoAlegacaoImpugResultadoTO->setUsuario(UsuarioTO::newInstanceFromEntity($usuario));
                }

                $arquivos = [];
                if (!empty($julgamentoAlegacaoImpugResultado->getNomeArquivo())) {
                    $arquivos[] = ArquivoGenericoTO::newInstance([
                        'nome' => $julgamentoAlegacaoImpugResultado->getNomeArquivo(),
                        'nomeFisico' => $julgamentoAlegacaoImpugResultado->getNomeArquivoFisico()
                    ]);
                }
                $julgamentoAlegacaoImpugResultadoTO->setArquivos($arquivos);
            }
        }
        return $julgamentoAlegacaoImpugResultadoTO;
    }

}
