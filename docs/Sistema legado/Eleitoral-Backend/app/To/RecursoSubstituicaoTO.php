<?php


namespace App\To;

use App\Entities\JulgamentoImpugnacao;
use App\Entities\JulgamentoSubstituicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\RecursoImpugnacao;
use App\Entities\RecursoSubstituicao;
use App\Entities\StatusJulgamentoImpugnacao;
use App\Entities\StatusJulgamentoSubstituicao;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Recurso de Substituição
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class RecursoSubstituicaoTO
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
     * @var JulgamentoSubstituicaoTO
     */
    private $julgamentoSubstituicao;

    /**
     * @var mixed
     */
    private $arquivo;

    /**
     * @var $tamanho
     */
    private $tamanho;

    /**
     * @var $idJulgamentoSubstituicao
     */
    private $idJulgamentoSubstituicao;

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
     * @return JulgamentoSubstituicaoTO
     */
    public function getJulgamentoSubstituicao(): ?JulgamentoSubstituicaoTO
    {
        return $this->julgamentoSubstituicao;
    }

    /**
     * @param JulgamentoSubstituicaoTO $julgamentoSubstituicao
     */
    public function setJulgamentoSubstituicao(?JulgamentoSubstituicaoTO $julgamentoSubstituicao): void
    {
        $this->julgamentoSubstituicao = $julgamentoSubstituicao;
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
    public function getIdJulgamentoSubstituicao()
    {
        return $this->idJulgamentoSubstituicao;
    }

    /**
     * @param mixed $idJulgamentoSubstituicao
     */
    public function setIdJulgamentoSubstituicao($idJulgamentoSubstituicao): void
    {
        $this->idJulgamentoSubstituicao = $idJulgamentoSubstituicao;
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
     * Retorna uma nova instância de 'RecursoSubstituicaoTO'.
     *
     * @param null $data
     * @return RecursoSubstituicaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $recursoSubstituicaoTO = new RecursoSubstituicaoTO();

        if ($data != null) {
            $recursoSubstituicaoTO->setId(Arr::get($data, 'id'));
            $recursoSubstituicaoTO->setTamanho(Arr::get($data, 'tamanho'));
            $recursoSubstituicaoTO->setArquivo(Arr::get($data, 'arquivo'));
            $recursoSubstituicaoTO->setDescricao(Arr::get($data, 'descricao'));
            $recursoSubstituicaoTO->setNomeArquivo(Arr::get($data, 'nomeArquivo'));
            $recursoSubstituicaoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $recursoSubstituicaoTO->setNomeArquivoFisico(Arr::get($data, 'nomeArquivoFisico'));
            $recursoSubstituicaoTO->setIdJulgamentoSubstituicao(Arr::get($data,'idJulgamentoSubstituicao'));

            $julgamentoSubstituicao = Arr::get($data, 'julgamentoSubstituicao');
            if (!empty($julgamentoSubstituicao)) {
                $recursoSubstituicaoTO->setJulgamentoSubstituicao(
                    JulgamentoSubstituicaoTO::newInstance($julgamentoSubstituicao)
                );
            }

            $profissional = Arr::get($data, 'profissional');
            if (!empty($profissional)) {
                $recursoSubstituicaoTO->setProfissional(ProfissionalTO::newInstance($profissional));
            }

            $arquivos = Arr::get($data, 'arquivos');
            if (!empty($arquivos)) {
                $recursoSubstituicaoTO->setArquivos(array_map(function($arquivo){
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else if(!empty(Arr::get($data, 'nomeArquivo'))){
                $arquivo = ArquivoGenericoTO::newInstance([
                    'nome' => Arr::get($data, 'nomeArquivo'),
                    'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                ]);
                $recursoSubstituicaoTO->setArquivos([$arquivo]);
            }
        }

        return $recursoSubstituicaoTO;
    }

    /**
     * Retorna uma nova instância de 'RecursoSubstituicaoTO'.
     *
     * @param RecursoSubstituicao $recursoSubstituicao
     * @param bool $isResumo
     * @return RecursoSubstituicaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($recursoSubstituicao, $isResumo = false)
    {
        $recursoSubstituicaoTO = new RecursoSubstituicaoTO();

        if (!empty($recursoSubstituicao)) {
            $recursoSubstituicaoTO->setId($recursoSubstituicao->getId());
            $recursoSubstituicaoTO->setDescricao($recursoSubstituicao->getDescricao());

            if (!$isResumo) {
                $recursoSubstituicaoTO->setDataCadastro($recursoSubstituicao->getDataCadastro());
                $recursoSubstituicaoTO->setNomeArquivo($recursoSubstituicao->getNomeArquivo());
                $recursoSubstituicaoTO->setNomeArquivoFisico($recursoSubstituicao->getNomeArquivoFisico());

                if(!empty($recursoSubstituicao->getJulgamentoSubstituicao())) {
                    $recursoSubstituicaoTO->setJulgamentoSubstituicao(JulgamentoSubstituicaoTO::newInstanceFromEntity(
                        $recursoSubstituicao->getJulgamentoSubstituicao()
                    ));
                }

                if(!empty($recursoSubstituicao->getProfissional())) {
                    $recursoSubstituicaoTO->setProfissional(ProfissionalTO::newInstanceFromEntity(
                        $recursoSubstituicao->getProfissional()
                    ));
                }

                $arquivos = [];
                if(!empty($recursoSubstituicao->getNomeArquivo())) {
                    $arquivos[] = ArquivoGenericoTO::newInstance([
                        'nome' => $recursoSubstituicao->getNomeArquivo(),
                        'nomeFisico' => $recursoSubstituicao->getNomeArquivoFisico()
                    ]);
                }
                $recursoSubstituicaoTO->setArquivos($arquivos);
            }
        }

        return $recursoSubstituicaoTO;
    }

}
