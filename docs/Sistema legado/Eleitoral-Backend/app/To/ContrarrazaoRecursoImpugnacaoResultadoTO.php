<?php


namespace App\To;

use App\Entities\ContrarrazaoRecursoImpugnacao;
use App\Entities\ContrarrazaoRecursoImpugnacaoResultado;
use App\Entities\JulgamentoImpugnacao;
use App\Entities\JulgamentoSubstituicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\RecursoImpugnacao;
use App\Entities\StatusJulgamentoImpugnacao;
use App\Entities\StatusJulgamentoSubstituicao;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Contrarrazao do Recurso de Impugnação de Resultado
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ContrarrazaoRecursoImpugnacaoResultadoTO
{

    /**
     * @var integer|null $id
     */
    private $id;

    /**
     * @var string|null $descricao
     */
    private $descricao;

    /**
     * @var string|null $numeroChapa
     */
    private $numeroChapa;

    /**
     * @var string|null
     */
    private $nomeArquivo;

    /**
     * @var string|null
     */
    private $nomeArquivoFisico;

    /**
     * @var \DateTime
     */
    private $dataCadastro;

    /**
     * @var string|null
     */
    private $numero;

    /**
     * @var mixed
     */
    private $arquivo;

    /**
     * @var $tamanho
     */
    private $tamanho;

    /**
     * @var $idRecursoImpugnacaoResultado
     */
    private $idRecursoImpugnacaoResultado;

    /**
     * @var ProfissionalTO $profissional
     */
    private $profissional;

    /**
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivos;

    /**
     * Retorna uma nova instância de 'ContrarrazaoRecursoImpugnacaoResultadoTO'.
     *
     * @param null $data
     * @return ContrarrazaoRecursoImpugnacaoResultadoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $contrarrazaoRecursoImpugnacaoTO = new ContrarrazaoRecursoImpugnacaoResultadoTO();

        if ($data != null) {
            $contrarrazaoRecursoImpugnacaoTO->setId(Arr::get($data, 'id'));
            $contrarrazaoRecursoImpugnacaoTO->setNumero(Arr::get($data, 'numero'));
            $contrarrazaoRecursoImpugnacaoTO->setArquivo(Arr::get($data, 'arquivo'));
            $contrarrazaoRecursoImpugnacaoTO->setTamanho(Arr::get($data, 'tamanho'));
            $contrarrazaoRecursoImpugnacaoTO->setDescricao(Arr::get($data, 'descricao'));
            $contrarrazaoRecursoImpugnacaoTO->setNomeArquivo(Arr::get($data, 'nomeArquivo'));
            $contrarrazaoRecursoImpugnacaoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $contrarrazaoRecursoImpugnacaoTO->setNomeArquivoFisico(Arr::get($data, 'nomeArquivoFisico'));
            $contrarrazaoRecursoImpugnacaoTO->setIdRecursoImpugnacaoResultado(Arr::get($data, 'idRecursoImpugnacaoResultado'));

            $profissional = Arr::get($data, 'profissional');
            if (!empty($profissional)) {
                $contrarrazaoRecursoImpugnacaoTO->setProfissional(ProfissionalTO::newInstance($profissional));
            }

            $arquivos = Arr::get($data, 'arquivos');
            if (!empty($arquivos)) {
                $contrarrazaoRecursoImpugnacaoTO->setArquivos(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else if (!empty(Arr::get($data, 'nomeArquivo'))) {
                $arquivo = ArquivoGenericoTO::newInstance([
                    'nome' => Arr::get($data, 'nomeArquivo'),
                    'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                ]);
                $contrarrazaoRecursoImpugnacaoTO->setArquivos([$arquivo]);
            }
        }

        return $contrarrazaoRecursoImpugnacaoTO;
    }

    /**
     * Retorna uma nova instância de 'RecursoImpugnacaoTO'.
     *
     * @param ContrarrazaoRecursoImpugnacaoResultado $contrarrazaoRecursoImpugnacaoResultado
     * @param bool $isResumo
     * @return ContrarrazaoRecursoImpugnacaoResultadoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($contrarrazaoRecursoImpugnacaoResultado)
    {
        $contrarrazaoRecursoImpugnacaoResultadoTO = new ContrarrazaoRecursoImpugnacaoResultadoTO();

        if (!empty($contrarrazaoRecursoImpugnacaoResultado)) {
            $contrarrazaoRecursoImpugnacaoResultadoTO->setId($contrarrazaoRecursoImpugnacaoResultado->getId());
            $contrarrazaoRecursoImpugnacaoResultadoTO->setDescricao($contrarrazaoRecursoImpugnacaoResultado->getDescricao());
            $contrarrazaoRecursoImpugnacaoResultadoTO->setNumero($contrarrazaoRecursoImpugnacaoResultado->getNumero());
            $contrarrazaoRecursoImpugnacaoResultadoTO->setDataCadastro($contrarrazaoRecursoImpugnacaoResultado->getDataCadastro());

            $arquivos = [];
            if (!empty($contrarrazaoRecursoImpugnacaoResultado->getNomeArquivo())) {
                $arquivos[] = ArquivoGenericoTO::newInstance([
                    'nome' => $contrarrazaoRecursoImpugnacaoResultado->getNomeArquivo(),
                    'nomeFisico' => $contrarrazaoRecursoImpugnacaoResultado->getNomeArquivoFisico()
                ]);
            }
            $contrarrazaoRecursoImpugnacaoResultadoTO->setArquivos($arquivos);

            if (!empty($contrarrazaoRecursoImpugnacaoResultado->getProfissional())) {
                $contrarrazaoRecursoImpugnacaoResultadoTO->setProfissional(ProfissionalTO::newInstanceFromEntity(
                    $contrarrazaoRecursoImpugnacaoResultado->getProfissional(), true
                ));
            }
        }

        return $contrarrazaoRecursoImpugnacaoResultadoTO;
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
     * @return \DateTime
     */
    public function getDataCadastro(): ?\DateTime
    {
        return $this->dataCadastro;
    }

    /**
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro(?\DateTime $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return string|null
     */
    public function getNumero(): ?string
    {
        return $this->numero;
    }

    /**
     * @param string|null $numero
     */
    public function setNumero(?string $numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return mixed
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * @param mixed $arquivo
     */
    public function setArquivo($arquivo): void
    {
        $this->arquivo = $arquivo;
    }

    /**
     * @return mixed
     */
    public function getTamanho()
    {
        return $this->tamanho;
    }

    /**
     * @param mixed $tamanho
     */
    public function setTamanho($tamanho): void
    {
        $this->tamanho = $tamanho;
    }

    /**
     * @return mixed
     */
    public function getIdRecursoImpugnacaoResultado()
    {
        return $this->idRecursoImpugnacaoResultado;
    }

    /**
     * @param mixed $idRecursoImpugnacaoResultado
     */
    public function setIdRecursoImpugnacaoResultado($idRecursoImpugnacaoResultado): void
    {
        $this->idRecursoImpugnacaoResultado = $idRecursoImpugnacaoResultado;
    }

    /**
     * @return ProfissionalTO
     */
    public function getProfissional(): ?ProfissionalTO
    {
        return $this->profissional;
    }

    /**
     * @param ProfissionalTO $profissional
     */
    public function setProfissional(?ProfissionalTO $profissional): void
    {
        $this->profissional = $profissional;
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
        $this->numeroChapa = $numeroChapa;
    }



}
