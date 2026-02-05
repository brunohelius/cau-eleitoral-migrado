<?php


namespace App\To;

use App\Entities\RecursoIndicacao;
use App\Entities\RecursoJulgamentoFinal;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Julgamento Final
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class RecursoJulgamentoFinalTO
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
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivos;

    /**
     * @var \DateTime|string|null
     */
    private $dataCadastro;

    /**
     * @var integer|null $idProfissional
     */
    private $idProfissional;

    /**
     * @var IndicacaoJulgamentoFinalTO[]|null
     */
    private $indicacoes;

    /**
     * @var RecursoIndicacaoTO[]|null
     */
    private $recursosIndicacao;

    /**
     * @var integer|null $idJulgamentoFinal
     */
    private $idJulgamentoFinal;

    /**
     * @var ProfissionalTO|null $profissional
     */
    private $profissional;

    /**
     * @var JulgamentoFinalTO|null $julgamentoFinal
     */
    private $julgamentoFinal;

    /**
     * @var boolean|null
     */
    private $hasJulgamentoSegundaInstancia = false;


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
     * @return \DateTime|string|null
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @param \DateTime|string|null $dataCadastro
     */
    public function setDataCadastro($dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return IndicacaoJulgamentoFinalTO[]|null
     */
    public function getIndicacoes(): ?array
    {
        return $this->indicacoes;
    }

    /**
     * @param IndicacaoJulgamentoFinalTO[]|null $indicacoes
     */
    public function setIndicacoes(?array $indicacoes): void
    {
        $this->indicacoes = $indicacoes;
    }

    /**
     * @return int|null
     */
    public function getIdJulgamentoFinal(): ?int
    {
        return $this->idJulgamentoFinal;
    }

    /**
     * @param int|null $idJulgamentoFinal
     */
    public function setIdJulgamentoFinal(?int $idJulgamentoFinal): void
    {
        $this->idJulgamentoFinal = $idJulgamentoFinal;
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
     * @return JulgamentoFinalTO|null
     */
    public function getJulgamentoFinal(): ?JulgamentoFinalTO
    {
        return $this->julgamentoFinal;
    }

    /**
     * @param JulgamentoFinalTO|null $julgamentoFinal
     */
    public function setJulgamentoFinal(?JulgamentoFinalTO $julgamentoFinal): void
    {
        $this->julgamentoFinal = $julgamentoFinal;
    }

    /**
     * @return bool|null
     */
    public function hasJulgamentoSegundaInstancia(): ?bool
    {
        return $this->hasJulgamentoSegundaInstancia;
    }

    /**
     * @param bool|null $hasJulgamentoSegundaInstancia
     */
    public function setHasJulgamentoSegundaInstancia(?bool $hasJulgamentoSegundaInstancia): void
    {
        $this->hasJulgamentoSegundaInstancia = $hasJulgamentoSegundaInstancia;
    }

    /**
     * @return RecursoIndicacaoTO[]|null
     */
    public function getRecursosIndicacao(): ?array
    {
        return $this->recursosIndicacao;
    }

    /**
     * @param RecursoIndicacaoTO[]|null $recursosIndicacao
     */
    public function setRecursosIndicacao(?array $recursosIndicacao): void
    {
        $this->recursosIndicacao = $recursosIndicacao;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalTO'.
     *
     * @param null $data
     * @return RecursoJulgamentoFinalTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $recursoJulgamentoFinalTO = new RecursoJulgamentoFinalTO();

        if ($data != null) {
            $recursoJulgamentoFinalTO->setId(Arr::get($data, 'id'));
            $recursoJulgamentoFinalTO->setDescricao(Arr::get($data, 'descricao'));
            $recursoJulgamentoFinalTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $recursoJulgamentoFinalTO->setIdProfissional(Arr::get($data, 'idProfissional'));
            $recursoJulgamentoFinalTO->setIdJulgamentoFinal(Arr::get($data, 'idJulgamentoFinal'));

            $arquivos = Arr::get($data, 'arquivos', null);
            if (!empty($arquivos)) {
                $recursoJulgamentoFinalTO->setArquivos(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else {
                if (!empty(Arr::get($data, 'nomeArquivo'))) {
                    $arquivo = ArquivoGenericoTO::newInstance([
                        'nome' => Arr::get($data, 'nomeArquivo'),
                        'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                    ]);
                    $recursoJulgamentoFinalTO->setArquivos([$arquivo]);
                }
            }

            $profissional = Arr::get($data, 'profissional');
            if (!empty($profissional)) {
                $recursoJulgamentoFinalTO->setProfissional(ProfissionalTO::newInstance($profissional));
            }

            $julgamentoFinal = Arr::get($data, 'julgamentoFinal');
            if (!empty($julgamentoFinal)) {
                $recursoJulgamentoFinalTO->
                setJulgamentoFinal(JulgamentoFinalTO::newInstance($julgamentoFinal));
            }

            $indicacoesArray = Arr::get($data, 'indicacoes');
            $indicacoesSecundaria = Arr::get($data, 'recursosIndicacao');
            if (!empty($indicacoesArray)) {
                $indicacoes = [];
                foreach ($indicacoesArray as $indicacao) {
                    array_push($indicacoes, IndicacaoJulgamentoFinalTO::newInstance($indicacao));
                }
                $recursoJulgamentoFinalTO->setIndicacoes($indicacoes);

            } else if (!empty($indicacoesSecundaria)) {
                $indicacoes = [];
                foreach ($indicacoesSecundaria as $indicacao) {
                    $recursoIndicacao = RecursoIndicacao::newInstance($indicacao);
                    $indicacaoJulgamento = $recursoIndicacao->getIndicacaoJulgamentoFinal();
                    array_push($indicacoes, $indicacaoJulgamento);
                }
                $recursoJulgamentoFinalTO->setIndicacoes($indicacoes);
            }
        }

        return $recursoJulgamentoFinalTO;
    }

    /**
     * Retorna uma nova instância de 'RecursoJulgamentoFinalTO'.
     *
     * @param RecursoJulgamentoFinal $recursoJulgamentoFinal
     * @param bool $isResumo
     * @return RecursoJulgamentoFinalTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($recursoJulgamentoFinal, $isResumo = false)
    {
        $recursoJulgamentoFinalTO = new RecursoJulgamentoFinalTO();

        if (!empty($recursoJulgamentoFinalTO)) {
            $recursoJulgamentoFinalTO->setId($recursoJulgamentoFinal->getId());
            $recursoJulgamentoFinalTO->setDescricao($recursoJulgamentoFinal->getDescricao());
            $recursoJulgamentoFinalTO->setDataCadastro($recursoJulgamentoFinal->getDataCadastro());

            $profissional = $recursoJulgamentoFinal->getProfissional();
            if (!empty($profissional)) {
                $recursoJulgamentoFinalTO->setProfissional(ProfissionalTO::newInstanceFromEntity($profissional));
            }

            $julgamentoFinal = $recursoJulgamentoFinal->getJulgamentoFinal();
            if (!empty($julgamentoFinal)) {
                $recursoJulgamentoFinalTO->
                setJulgamentoFinal(JulgamentoFinalTO::newInstanceFromEntity($julgamentoFinal));
            }

            $indicacoesArray = $recursoJulgamentoFinal->getRecursosIndicacao();
            if (!empty($indicacoesArray)) {
                $indicacoes = [];
                /** @var RecursoIndicacao $indicacao */
                foreach ($indicacoesArray as $indicacao) {
                    array_push($indicacoes, IndicacaoJulgamentoFinalTO::newInstanceFromEntity(
                        $indicacao->getIndicacaoJulgamentoFinal()
                    ));
                }
                $recursoJulgamentoFinalTO->setIndicacoes($indicacoes);
            }

            if (!$isResumo) {
                $arquivos = [];
                if (!empty($recursoJulgamentoFinal->getNomeArquivo())) {
                    $arquivos[] = ArquivoGenericoTO::newInstance([
                        'nome' => $recursoJulgamentoFinal->getNomeArquivo(),
                        'nomeFisico' => $recursoJulgamentoFinal->getNomeArquivoFisico()
                    ]);
                }
                $recursoJulgamentoFinalTO->setArquivos($arquivos);
            }
        }

        return $recursoJulgamentoFinalTO;
    }

    /**
     * Retorna uma nova instância de 'RecursoJulgamentoFinalTO'.
     *
     * @param RecursoJulgamentoFinal $recursoJulgamentoFinal
     * @param bool $isResumo
     * @return RecursoJulgamentoFinalTO
     * @throws \Exception
     */
    public static function newInstanceFromEntityByJulgamentoRecurso($recursoJulgamentoFinal, $isResumo = false)
    {
        $recursoJulgamentoFinalTO = self::newInstanceFromEntity($recursoJulgamentoFinal, $isResumo);

        $recursoJulgamentoFinalTO->setIndicacoes(null);

        $indicacoesArray = $recursoJulgamentoFinal->getRecursosIndicacao();
        if (!empty($indicacoesArray)) {
            $indicacoes = [];
            /** @var RecursoIndicacao $indicacao */
            foreach ($indicacoesArray as $indicacao) {
                array_push($indicacoes, RecursoIndicacaoTO::newInstanceFromEntity($indicacao));
            }
            $recursoJulgamentoFinalTO->setRecursosIndicacao($indicacoes);
        }

        return $recursoJulgamentoFinalTO;
    }

}
