<?php


namespace App\To;


use Illuminate\Support\Arr;

class RecursoImpugnacaoResultadoTO
{

    /**
     * @var integer | null
     */
    private $id;

    /**
     * @var string | null
     */
    private $descricao;

    /**
     * @var string | null
     */
    private $nomeArquivo;

    /**
     * @var string | null
     */
    private $nomeArquivoFisico;

    /**
     * @var \DateTime | null
     */
    private $dataCadastro;

    /**
     * @var integer | null
     */
    private $numero;

    /**
     * @var string | null
     */
    private $numeroChapa;

    /**
     * @var ProfissionalTO | null
     */
    private $profissional;

    /**
     * @var JulgamentoAlegacaoImpugResultadoTO | null
     */
    private $julgamentoAlegacaoImpugResultado;

    /**
     * @var integer | null
     */
    private $idJulgamentoAlegacaoImpugResultado;


    /**
     * @var integer | null
     */
    private $idTipoRecursoImpugnacaoResultado;

    /**
     * @var ContrarrazaoRecursoImpugnacaoResultadoTO[] | null
     */
    private $contrarrazoesRecursoImpugnacaoResultado;

    /**
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivo;

    /**
     * @var integer | null
     */
    private $tamanho;

    /**
     * @var boolean|null
     */
    private $hasCadastroChapaContrarrazao;


    /**
     * Retorna uma nova instância de 'AlegacaoImpugnacaoResultadoTO'.
     *
     * @param null $data
     * @return RecursoImpugnacaoResultadoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $recursoImpugnacaoResultadoTO = new RecursoImpugnacaoResultadoTO();

        if ($data != null) {

            $recursoImpugnacaoResultadoTO->setId(Arr::get($data,'id'));
            $recursoImpugnacaoResultadoTO->setDescricao(Arr::get($data,'descricao'));
            $recursoImpugnacaoResultadoTO->setNomeArquivo(Arr::get($data, 'nomeArquivo'));
            $recursoImpugnacaoResultadoTO->setNomeArquivoFisico(Arr::get($data, 'nomeArquivoFisico'));
            $recursoImpugnacaoResultadoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $recursoImpugnacaoResultadoTO->setNumero(Arr::get($data, 'numero'));
            $recursoImpugnacaoResultadoTO->setTamanho(Arr::get($data, 'tamanho'));
            $recursoImpugnacaoResultadoTO->setIdTipoRecursoImpugnacaoResultado(Arr::get($data, 'idTipoRecursoImpugnacaoResultado'));

            $profissional = Arr::get($data, 'profissional');
            if(!empty($profissional)) {
                $recursoImpugnacaoResultadoTO->setProfissional(ProfissionalTO::newInstance($profissional));
            }

            $julgamentoAlegacaoImpugResultado = Arr::get($data, 'julgamentoAlegacaoImpugResultado');
            if(!empty($julgamentoAlegacaoImpugResultado)) {
                $recursoImpugnacaoResultadoTO->setJulgamentoAlegacaoImpugResultado(
                    JulgamentoAlegacaoImpugResultadoTO::newInstance($julgamentoAlegacaoImpugResultado)
                );
            }

            $contrarrazoesRecursoImpugnacaoResultado = Arr::get($data, 'contrarrazoesRecursoImpugnacaoResultado');
            if(!empty($contrarrazoesRecursoImpugnacaoResultado)) {

                $recursoImpugnacaoResultadoTO->setContrarrazoesRecursoImpugnacaoResultado(
                    array_map(function ($contrarrazaoRecursoImpugnacaoResultado) {
                        return ContrarrazaoRecursoImpugnacaoResultadoTO::newInstance($contrarrazaoRecursoImpugnacaoResultado);
                    },$contrarrazoesRecursoImpugnacaoResultado )
                );
            }

            $arquivos = Arr::get($data, 'arquivos', null);
            if (!empty($arquivos)) {
                $recursoImpugnacaoResultadoTO->setArquivo(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else {
                if (!empty(Arr::get($data, 'nomeArquivo'))) {
                    $arquivo = ArquivoGenericoTO::newInstance([
                        'nome' => Arr::get($data, 'nomeArquivo'),
                        'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                    ]);
                    $recursoImpugnacaoResultadoTO->setArquivo([$arquivo]);
                }
            }
        }

        return $recursoImpugnacaoResultadoTO;
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
     * @return string|null
     */
    public function getNomeArquivo(): ?string
    {
        return $this->nomeArquivo;
    }

    /**
     * @param string|null $nomeArquivo
     */
    public function setNomeArquivo(?string $nomeArquivo): void
    {
        $this->nomeArquivo = $nomeArquivo;
    }

    /**
     * @return string|null
     */
    public function getNomeArquivoFisico(): ?string
    {
        return $this->nomeArquivoFisico;
    }

    /**
     * @param string|null $nomeArquivoFisico
     */
    public function setNomeArquivoFisico(?string $nomeArquivoFisico): void
    {
        $this->nomeArquivoFisico = $nomeArquivoFisico;
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
    public function getNumeroChapa(): ?string
    {
        return $this->numeroChapa;
    }

    /**
     * @param string|null $numeroChapa
     */
    public function setNumeroChapa(?string $numeroChapa): void
    {
        $this->numeroChapa = $numeroChapa ?? '';
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
     * @return JulgamentoAlegacaoImpugResultadoTO|null
     */
    public function getJulgamentoAlegacaoImpugResultado(): ?JulgamentoAlegacaoImpugResultadoTO
    {
        return $this->julgamentoAlegacaoImpugResultado;
    }

    /**
     * @param JulgamentoAlegacaoImpugResultadoTO|null $julgamentoAlegacaoImpugResultado
     */
    public function setJulgamentoAlegacaoImpugResultado(?JulgamentoAlegacaoImpugResultadoTO $julgamentoAlegacaoImpugResultado): void
    {
        $this->julgamentoAlegacaoImpugResultado = $julgamentoAlegacaoImpugResultado;
    }

    /**
     * @return ContrarrazaoRecursoImpugnacaoResultadoTO[]|null
     */
    public function getContrarrazoesRecursoImpugnacaoResultado(): ?array
    {
        return $this->contrarrazoesRecursoImpugnacaoResultado;
    }

    /**
     * @param ContrarrazaoRecursoImpugnacaoResultadoTO[]|null $contrarrazoesRecursoImpugnacaoResultado
     */
    public function setContrarrazoesRecursoImpugnacaoResultado(?array $contrarrazoesRecursoImpugnacaoResultado): void
    {
        $this->contrarrazoesRecursoImpugnacaoResultado = $contrarrazoesRecursoImpugnacaoResultado;
    }

    /**
     * @return ArquivoGenericoTO[]|null
     */
    public function getArquivo(): ?array
    {
        return $this->arquivo;
    }

    /**
     * @param ArquivoGenericoTO[]|null $arquivo
     */
    public function setArquivo(?array $arquivo): void
    {
        $this->arquivo = $arquivo;
    }

    /**
     * @return int|null
     */
    public function getTamanho(): ?int
    {
        return $this->tamanho;
    }

    /**
     * @param int|null $tamanho
     */
    public function setTamanho(?int $tamanho): void
    {
        $this->tamanho = $tamanho;
    }

    /**
     * @return int|null
     */
    public function getIdjulgamentoAlegacaoImpugResultado(): ?int
    {
        return $this->idJulgamentoAlegacaoImpugResultado;
    }

    /**
     * @param int|null $idjulgamentoAlegacaoImpugResultado
     */
    public function setIdjulgamentoAlegacaoImpugResultado(?int $idJulgamentoAlegacaoImpugResultado): void
    {
        $this->idJulgamentoAlegacaoImpugResultado = $idJulgamentoAlegacaoImpugResultado;
    }

    /**
     * @return int|null
     */
    public function getIdTipoRecursoImpugnacaoResultado(): ?int
    {
        return $this->idTipoRecursoImpugnacaoResultado;
    }

    /**
     * @param int|null $idTipoRecursoImpugnacaoResultado
     */
    public function setIdTipoRecursoImpugnacaoResultado(?int $idTipoRecursoImpugnacaoResultado): void
    {
        $this->idTipoRecursoImpugnacaoResultado = $idTipoRecursoImpugnacaoResultado;
    }

    /**
     * @return bool|null
     */
    public function getHasCadastroChapaContrarrazao(): ?bool
    {
        return $this->hasCadastroChapaContrarrazao;
    }

    /**
     * @param bool|null $hasCadastroChapaContrarrazao
     */
    public function setHasCadastroChapaContrarrazao(?bool $hasCadastroChapaContrarrazao): void
    {
        $this->hasCadastroChapaContrarrazao = $hasCadastroChapaContrarrazao;
    }
}
