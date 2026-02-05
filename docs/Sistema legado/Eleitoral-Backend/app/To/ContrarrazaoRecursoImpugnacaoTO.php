<?php


namespace App\To;

use App\Entities\ContrarrazaoRecursoImpugnacao;
use App\Entities\JulgamentoImpugnacao;
use App\Entities\JulgamentoSubstituicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\RecursoImpugnacao;
use App\Entities\StatusJulgamentoImpugnacao;
use App\Entities\StatusJulgamentoSubstituicao;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Contrarrazao do Recurso de Impugnação
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ContrarrazaoRecursoImpugnacaoTO
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
     * @var JulgamentoImpugnacaoTO
     */
    private $julgamentoImpugnacao;

    /**
     * @var RecursoImpugnacaoTO
     */
    private $recursoImpugnacao;

    /**
     * @var TipoGenericoTO
     */
    private $tipoSolicitacaoRecursoImpugnacao;

    /**
     * @var mixed
     */
    private $arquivo;

    /**
     * @var $tamanho
     */
    private $tamanho;

    /**
     * @var $idJulgamentoImpugnacao
     */
    private $idJulgamentoImpugnacao;

    /**
     * @var $idRecursoImpugnacao
     */
    private $idRecursoImpugnacao;

    /**
     * @var $idTipoSolicitacaoRecursoImpugnacao
     */
    private $idTipoSolicitacaoRecursoImpugnacao;

    /**
     * @var ProfissionalTO $profissional
     */
    private $profissional;

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
     * @return TipoGenericoTO
     */
    public function getTipoSolicitacaoRecursoImpugnacao(): ?TipoGenericoTO
    {
        return $this->tipoSolicitacaoRecursoImpugnacao;
    }

    /**
     * @param TipoGenericoTO $tipoSolicitacaoRecursoImpugnacao
     */
    public function setTipoSolicitacaoRecursoImpugnacao(?TipoGenericoTO $tipoSolicitacaoRecursoImpugnacao): void
    {
        $this->tipoSolicitacaoRecursoImpugnacao = $tipoSolicitacaoRecursoImpugnacao;
    }

    /**
     * @return JulgamentoImpugnacaoTO
     */
    public function getJulgamentoImpugnacao(): ?JulgamentoImpugnacaoTO
    {
        return $this->julgamentoImpugnacao;
    }

    /**
     * @param JulgamentoImpugnacaoTO $julgamentoImpugnacao
     */
    public function setJulgamentoImpugnacao(?JulgamentoImpugnacaoTO $julgamentoImpugnacao): void
    {
        $this->julgamentoImpugnacao = $julgamentoImpugnacao;
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
    public function getIdJulgamentoImpugnacao()
    {
        return $this->idJulgamentoImpugnacao;
    }

    /**
     * @param mixed $idJulgamentoImpugnacao
     */
    public function setIdJulgamentoImpugnacao($idJulgamentoImpugnacao): void
    {
        $this->idJulgamentoImpugnacao = $idJulgamentoImpugnacao;
    }

    /**
     * @return mixed
     */
    public function getIdTipoSolicitacaoRecursoImpugnacao()
    {
        return $this->idTipoSolicitacaoRecursoImpugnacao;
    }

    /**
     * @param mixed $idTipoSolicitacaoRecursoImpugnacao
     */
    public function setIdTipoSolicitacaoRecursoImpugnacao($idTipoSolicitacaoRecursoImpugnacao): void
    {
        $this->idTipoSolicitacaoRecursoImpugnacao = $idTipoSolicitacaoRecursoImpugnacao;
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
     * @return mixed
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param mixed $arquivos
     */
    public function setArquivos($arquivos): void
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return RecursoImpugnacaoTO
     */
    public function getRecursoImpugnacao(): ?RecursoImpugnacaoTO
    {
        return $this->recursoImpugnacao;
    }

    /**
     * @param RecursoImpugnacaoTO $recursoImpugnacao
     */
    public function setRecursoImpugnacao(?RecursoImpugnacaoTO $recursoImpugnacao): void
    {
        $this->recursoImpugnacao = $recursoImpugnacao;
    }

    /**
     * @return mixed
     */
    public function getIdRecursoImpugnacao()
    {
        return $this->idRecursoImpugnacao;
    }

    /**
     * @param mixed $idRecursoImpugnacao
     */
    public function setIdRecursoImpugnacao($idRecursoImpugnacao): void
    {
        $this->idRecursoImpugnacao = $idRecursoImpugnacao;
    }

    /**
     * Retorna uma nova instância de 'ContrarrazaoRecursoImpugnacaoTO'.
     *
     * @param null $data
     * @return ContrarrazaoRecursoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $contrarrazaoRecursoImpugnacaoTO = new ContrarrazaoRecursoImpugnacaoTO();

        if ($data != null) {
            $contrarrazaoRecursoImpugnacaoTO->setId(Arr::get($data, 'id'));
            $contrarrazaoRecursoImpugnacaoTO->setArquivo(Arr::get($data, 'arquivo'));
            $contrarrazaoRecursoImpugnacaoTO->setTamanho(Arr::get($data, 'tamanho'));
            $contrarrazaoRecursoImpugnacaoTO->setDescricao(Arr::get($data, 'descricao'));
            $contrarrazaoRecursoImpugnacaoTO->setNomeArquivo(Arr::get($data, 'nomeArquivo'));
            $contrarrazaoRecursoImpugnacaoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $contrarrazaoRecursoImpugnacaoTO->setNomeArquivoFisico(Arr::get($data, 'nomeArquivoFisico'));
            $contrarrazaoRecursoImpugnacaoTO->setIdRecursoImpugnacao(Arr::get($data, 'idRecursoImpugnacao'));

            $julgamentoImpugnacao = Arr::get($data, 'julgamentoImpugnacao');
            if (!empty($julgamentoImpugnacao)) {
                $contrarrazaoRecursoImpugnacaoTO->setJulgamentoImpugnacao(
                    JulgamentoImpugnacaoTO::newInstance($julgamentoImpugnacao)
                );
            }

            $tipoSolicitacaoRecursoImpugnacao = Arr::get($data, 'tipoSolicitacaoRecursoImpugnacao');
            if (!empty($tipoSolicitacaoRecursoImpugnacao)) {
                $contrarrazaoRecursoImpugnacaoTO->setTipoSolicitacaoRecursoImpugnacao(
                    TipoGenericoTO::newInstance($tipoSolicitacaoRecursoImpugnacao)
                );
            }

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
     * @param ContrarrazaoRecursoImpugnacao $contrarrazaoRecursoImpugnacao
     * @param bool $isResumo
     * @return ContrarrazaoRecursoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($contrarrazaoRecursoImpugnacao, $isResumo = false)
    {
        $contrarrazaoRecursoImpugnacaoTO = new ContrarrazaoRecursoImpugnacaoTO();

        if (!empty($contrarrazaoRecursoImpugnacaoTO)) {
            $contrarrazaoRecursoImpugnacaoTO->setId($contrarrazaoRecursoImpugnacao->getId());
            $contrarrazaoRecursoImpugnacaoTO->setDescricao($contrarrazaoRecursoImpugnacao->getDescricao());

            $contrarrazaoRecursoImpugnacaoTO->setDataCadastro($contrarrazaoRecursoImpugnacao->getDataCadastro());
            $contrarrazaoRecursoImpugnacaoTO->setNomeArquivo($contrarrazaoRecursoImpugnacao->getNomeArquivo());
            $contrarrazaoRecursoImpugnacaoTO->setNomeArquivoFisico($contrarrazaoRecursoImpugnacao->getNomeArquivoFisico());

            $arquivos = [];
            if (!empty($contrarrazaoRecursoImpugnacao->getNomeArquivo())) {
                $arquivos[] = ArquivoGenericoTO::newInstance([
                    'nome' => $contrarrazaoRecursoImpugnacao->getNomeArquivo(),
                    'nomeFisico' => $contrarrazaoRecursoImpugnacao->getNomeArquivoFisico()
                ]);
            }
            $contrarrazaoRecursoImpugnacaoTO->setArquivos($arquivos);

            if (!empty($contrarrazaoRecursoImpugnacao->getProfissional())) {
                $contrarrazaoRecursoImpugnacaoTO->setProfissional(ProfissionalTO::newInstanceFromEntity(
                    $contrarrazaoRecursoImpugnacao->getProfissional()
                ));
            }

            if (!$isResumo) {
                if (!empty($contrarrazaoRecursoImpugnacao->getRecursoImpugnacao())) {
                    $contrarrazaoRecursoImpugnacaoTO->setRecursoImpugnacao(RecursoImpugnacaoTO::newInstanceFromEntity(
                        $contrarrazaoRecursoImpugnacao->getRecursoImpugnacao()
                    ));
                }

                if (!empty($contrarrazaoRecursoImpugnacao->getRecursoImpugnacao()->getTipoSolicitacaoRecursoImpugnacao())) {
                    $contrarrazaoRecursoImpugnacaoTO->setTipoSolicitacaoRecursoImpugnacao(TipoGenericoTO::newInstance([
                        'id' => $contrarrazaoRecursoImpugnacao->getRecursoImpugnacao()->getTipoSolicitacaoRecursoImpugnacao()->getId(),
                        'descricao' => $contrarrazaoRecursoImpugnacao->getRecursoImpugnacao()->getTipoSolicitacaoRecursoImpugnacao()->getDescricao()
                    ]));
                }
            }
        }

        return $contrarrazaoRecursoImpugnacaoTO;
    }

}
