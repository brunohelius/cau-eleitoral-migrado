<?php


namespace App\To;

use App\Entities\RecursoIndicacao;
use App\Entities\RecursoJulgamentoFinal;
use App\Entities\RecursoSegundoJulgamentoSubstituicao;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Julgamento Final
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class RecursoSegundoJulgamentoSubstituicaoTO
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
     * @var \DateTime|null
     */
    private $dataCadastro;

    /**
     * @var integer|null $idProfissional
     */
    private $idProfissional;

    /**
     * @var integer|null $idJulgamentoSegundaInstanciaSubstituicao
     */
    private $idJulgamentoSegundaInstanciaSubstituicao;

    /**
     * @var ProfissionalTO|null $profissional
     */
    private $profissional;

    /**
     * @var JulgamentoSegundaInstanciaSubstituicaoTO|null $julgamentoSegundaInstanciaSubstituicao
     */
    private $julgamentoSegundaInstanciaSubstituicao;

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
     * @return int|null
     */
    public function getIdJulgamentoSegundaInstanciaSubstituicao(): ?int
    {
        return $this->idJulgamentoSegundaInstanciaSubstituicao;
    }

    /**
     * @param int|null $idJulgamentoSegundaInstanciaSubstituicao
     */
    public function setIdJulgamentoSegundaInstanciaSubstituicao(?int $idJulgamentoSegundaInstanciaSubstituicao): void
    {
        $this->idJulgamentoSegundaInstanciaSubstituicao = $idJulgamentoSegundaInstanciaSubstituicao;
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
     * @return JulgamentoSegundaInstanciaSubstituicaoTO|null
     */
    public function getJulgamentoSegundaInstanciaSubstituicao(): ?JulgamentoSegundaInstanciaSubstituicaoTO
    {
        return $this->julgamentoSegundaInstanciaSubstituicao;
    }

    /**
     * @param JulgamentoSegundaInstanciaSubstituicaoTO|null $julgamentoSegundaInstanciaSubstituicao
     */
    public function setJulgamentoSegundaInstanciaSubstituicao(?JulgamentoSegundaInstanciaSubstituicaoTO $julgamentoSegundaInstanciaSubstituicao): void
    {
        $this->julgamentoSegundaInstanciaSubstituicao = $julgamentoSegundaInstanciaSubstituicao;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalTO'.
     *
     * @param null $data
     * @return RecursoSegundoJulgamentoSubstituicaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $recursoSegundoJulgamentoSubstituicaoTO = new RecursoSegundoJulgamentoSubstituicaoTO();

        if ($data != null) {
            $recursoSegundoJulgamentoSubstituicaoTO->setId(Arr::get($data, 'id'));
            $recursoSegundoJulgamentoSubstituicaoTO->setDescricao(Arr::get($data, 'descricao'));
            $recursoSegundoJulgamentoSubstituicaoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $recursoSegundoJulgamentoSubstituicaoTO->setIdProfissional(Arr::get($data, 'idProfissional'));
            $recursoSegundoJulgamentoSubstituicaoTO->setIdJulgamentoSegundaInstanciaSubstituicao(
                Arr::get($data, 'idJulgamentoSegundaInstanciaSubstituicao')
            );

            $arquivos = Arr::get($data, 'arquivos', null);
            if (!empty($arquivos)) {
                $recursoSegundoJulgamentoSubstituicaoTO->setArquivos(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else {
                if (!empty(Arr::get($data, 'nomeArquivo'))) {
                    $arquivo = ArquivoGenericoTO::newInstance([
                        'nome' => Arr::get($data, 'nomeArquivo'),
                        'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                    ]);
                    $recursoSegundoJulgamentoSubstituicaoTO->setArquivos([$arquivo]);
                }
            }

            $profissional = Arr::get($data, 'profissional');
            if (!empty($profissional)) {
                $recursoSegundoJulgamentoSubstituicaoTO->setProfissional(ProfissionalTO::newInstance($profissional));
            }

            $julgamentoSegundaInstanciaSubstituicao = Arr::get($data, 'julgamentoSegundaInstanciaSubstituicao');
            if (!empty($julgamentoSegundaInstanciaSubstituicao)) {
                $recursoSegundoJulgamentoSubstituicaoTO->setJulgamentoSegundaInstanciaSubstituicao(
                    JulgamentoSegundaInstanciaSubstituicaoTO::newInstance($julgamentoSegundaInstanciaSubstituicao)
                );
            }
        }

        return $recursoSegundoJulgamentoSubstituicaoTO;
    }

    /**
     * Retorna uma nova instância de 'RecursoJulgamentoFinalTO'.
     *
     * @param RecursoSegundoJulgamentoSubstituicao $recursoJulgamentoFinal
     * @param $isResumo
     * @return RecursoSegundoJulgamentoSubstituicaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($recursoJulgamentoFinal, $isResumo = false)
    {
        $recursoSegundoJulgamentoSubstituicaoTO = new RecursoSegundoJulgamentoSubstituicaoTO();

        if (!empty($recursoJulgamentoFinalTO)) {
            $recursoSegundoJulgamentoSubstituicaoTO->setId($recursoJulgamentoFinal->getId());
            $recursoSegundoJulgamentoSubstituicaoTO->setDescricao($recursoJulgamentoFinal->getDescricao());
            $recursoSegundoJulgamentoSubstituicaoTO->setDataCadastro($recursoJulgamentoFinal->getDataCadastro());

            $profissional = $recursoJulgamentoFinal->getProfissional();
            if (!empty($profissional)) {
                $recursoSegundoJulgamentoSubstituicaoTO->setProfissional(ProfissionalTO::newInstanceFromEntity($profissional));
            }

            $julgamentoSegundaInstanciaSubstituicao = $recursoJulgamentoFinal->getJulgamentoSegundaInstanciaSubstituicao();
            if (!empty($julgamentoSegundaInstanciaSubstituicao)) {
                $recursoSegundoJulgamentoSubstituicaoTO->setJulgamentoSegundaInstanciaSubstituicao(
                    JulgamentoSegundaInstanciaSubstituicaoTO::newInstanceFromEntity($julgamentoSegundaInstanciaSubstituicao));
            }

            if (!$isResumo) {
                $arquivos = [];
                if (!empty($recursoJulgamentoFinal->getNomeArquivo())) {
                    $arquivos[] = ArquivoGenericoTO::newInstance([
                        'nome' => $recursoJulgamentoFinal->getNomeArquivo(),
                        'nomeFisico' => $recursoJulgamentoFinal->getNomeArquivoFisico()
                    ]);
                }
                $recursoSegundoJulgamentoSubstituicaoTO->setArquivos($arquivos);
            }
        }

        return $recursoSegundoJulgamentoSubstituicaoTO;
    }

}
