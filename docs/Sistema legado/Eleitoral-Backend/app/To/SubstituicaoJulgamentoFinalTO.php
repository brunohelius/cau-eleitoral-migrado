<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\RecursoSegundoJulgamentoSubstituicao;
use App\Util\Utils;
use Illuminate\Support\Arr;
use App\Entities\SubstituicaoJulgamentoFinal;
use App\Entities\StatusJulgamentoSubstituicao;

/**
 * Classe de transferência para a Substituição no Julgamento Final
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class SubstituicaoJulgamentoFinalTO
{

    /**
     * @var integer|null $id
     */
    private $id;

    /**
     * @var string|null
     */
    private $sequencia;

    /**
     * @var string|null $justificativa
     */
    private $justificativa;

    /**
     * @var \DateTime
     */
    private $dataCadastro;

    /**
     * @var ArquivoGenericoTO[] | null
     */
    private $arquivos;

    /**
     * @var JulgamentoFinalTO
     */
    private $julgamentoFinal;

    /**
     * @var $idJulgamentoFinal
     */
    private $idJulgamentoFinal;

    /**
     * @var ProfissionalTO $profissional
     */
    private $profissional;

    /**
     * @var MembroSubstituicaoJulgamentoFinalTO[] | null $membrosSubstituicaoJulgamentoFinal
     */
    private $membrosSubstituicaoJulgamentoFinal;

    /**
     * @var string|null
     */
    private $nomeArquivo;

    /**
     * @var boolean|null
     */
    private $hasJulgamentoSegundaInstancia = false;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var boolean|null
     */
    private $isPrimeiraInstancia;

    /**
     * @return int | null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int | null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string | null
     */
    public function getJustificativa(): ?string
    {
        return $this->justificativa;
    }

    /**
     * @param string | null $justificativa
     */
    public function setJustificativa(?string $justificativa): void
    {
        $this->justificativa = $justificativa;
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
     * @return ArquivoGenericoTO[] | null
     */
    public function getArquivos(): ?array
    {
        return $this->arquivos;
    }

    /**
     * @param ArquivoGenericoTO[] | null $arquivos
     */
    public function setArquivos(?array $arquivos): void
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return JulgamentoFinalTO
     */
    public function getJulgamentoFinalTo(): ?JulgamentoFinalTO
    {
        return $this->julgamentoFinal;
    }

    /**
     * @param JulgamentoFinalTO $julgamentoFinalTO
     */
    public function setJulgamentoFinalTo(?JulgamentoFinalTO $julgamentoFinalTO): void
    {
        $this->julgamentoFinal = $julgamentoFinalTO;
    }

    /**
     * @return mixed
     */
    public function getIdJulgamentoFinal()
    {
        return $this->idJulgamentoFinal;
    }

    /**
     * @param mixed $idJulgamentoFinal
     */
    public function setIdJulgamentoFinal($idJulgamentoFinal): void
    {
        $this->idJulgamentoFinal = $idJulgamentoFinal;
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
     * @return MembroSubstituicaoJulgamentoFinalTO[]|null
     */
    public function getMembrosSubstituicaoJulgamentoFinal(): ?array
    {
        return $this->membrosSubstituicaoJulgamentoFinal;
    }

    /**
     * @param MembroSubstituicaoJulgamentoFinalTO[]|null $membrosSubstituicaoJulgamentoFinal
     */
    public function setMembrosSubstituicaoJulgamentoFinal(?array $membrosSubstituicaoJulgamentoFinal): void
    {
        $this->membrosSubstituicaoJulgamentoFinal = $membrosSubstituicaoJulgamentoFinal;
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
    public function getSequencia(): ?string
    {
        return $this->sequencia;
    }

    /**
     * @param string|null $sequencia
     */
    public function setSequencia(?string $sequencia): void
    {
        $this->sequencia = null;

        if (!empty($sequencia)) {
            $this->sequencia = str_pad($sequencia, 2, '0', STR_PAD_LEFT);
        }
    }

    /**
     * @return JulgamentoFinalTO
     */
    public function getJulgamentoFinal()
    {
        return $this->julgamentoFinal;
    }

    /**
     * @param JulgamentoFinalTO $julgamentoFinal
     */
    public function setJulgamentoFinal($julgamentoFinal)
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
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param string $tipo
     */
    public function setTipo($tipo): void
    {
        $this->tipo = $tipo;
    }

    /**
     * @return bool|null
     */
    public function getIsPrimeiraInstancia()
    {
        return $this->isPrimeiraInstancia;
    }

    /**
     * @param bool|null $isPrimeiraInstancia
     */
    public function setIsPrimeiraInstancia($isPrimeiraInstancia)
    {
        $this->isPrimeiraInstancia = $isPrimeiraInstancia;
    }

    /**
     * Retorna uma nova instância de 'SubstituicaoJulgamentoFinalTO'.
     *
     * @param null $data
     * @return SubstituicaoJulgamentoFinalTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $substituicaoJulgamentoFinalTO = new SubstituicaoJulgamentoFinalTO();

        if ($data != null) {
            $substituicaoJulgamentoFinalTO->setId(Arr::get($data, 'id'));
            $substituicaoJulgamentoFinalTO->setJustificativa(Arr::get($data, 'justificativa'));
            $substituicaoJulgamentoFinalTO->setNomeArquivo(Arr::get($data, 'nomeArquivo'));
            $substituicaoJulgamentoFinalTO->setSequencia(Arr::get($data, 'sequencia'));
            $substituicaoJulgamentoFinalTO->setTipo(Constants::SUBSTITUICAO_JULGAMENTO_TO_TIPO_SUBSTITUICAO);
            $substituicaoJulgamentoFinalTO->setIsPrimeiraInstancia(
                Utils::getBooleanValue('isPrimeiraInstancia', $data)
            );

            $arquivos = Arr::get($data, 'arquivos', null);
            if (!empty($arquivos)) {
                $substituicaoJulgamentoFinalTO->setArquivos(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else {
                if (!empty(Arr::get($data, 'nomeArquivo'))) {
                    $arquivo = ArquivoGenericoTO::newInstance([
                        'nome' => Arr::get($data, 'nomeArquivo'),
                        'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                    ]);
                    $substituicaoJulgamentoFinalTO->setArquivos([$arquivo]);
                }
            }
            $substituicaoJulgamentoFinalTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $profissional = Arr::get($data, 'profissional');
            if (!empty($profissional)) {
                $substituicaoJulgamentoFinalTO->setProfissional(ProfissionalTO::newInstance($profissional));
            }
            $substituicaoJulgamentoFinalTO->setIdJulgamentoFinal(Arr::get($data, 'idJulgamentoFinal'));

            $julgamentoFinal = Arr::get($data, 'julgamentoFinal');
            if (!empty($julgamentoFinal)) {
                $substituicaoJulgamentoFinalTO->setJulgamentoFinalTo(
                    JulgamentoFinalTO::newInstance($julgamentoFinal)
                );
            }

            $membrosSubstituicaoJulgamentoFinal = Arr::get($data, 'membrosSubstituicaoJulgamentoFinal');
            if (!empty($membrosSubstituicaoJulgamentoFinal)) {
                $membros = [];
                foreach ($membrosSubstituicaoJulgamentoFinal as $membro) {
                    array_push($membros, MembroSubstituicaoJulgamentoFinalTO::newInstance($membro));
                }
                $substituicaoJulgamentoFinalTO->setMembrosSubstituicaoJulgamentoFinal($membros);
            }

        }

        return $substituicaoJulgamentoFinalTO;
    }

    /**
     * Retorna uma nova instância de 'SubstituicaoJulgamentoFinalTO'.
     *
     * @param null $data
     * @return SubstituicaoJulgamentoFinalTO
     * @throws \Exception
     */
    public static function newInstanceRecurso($data = null)
    {
        $substituicaoJulgamentoFinalTO = new SubstituicaoJulgamentoFinalTO();

        if ($data != null) {
            $substituicaoJulgamentoFinalTO->setId(Arr::get($data, 'id'));
            $substituicaoJulgamentoFinalTO->setJustificativa(Arr::get($data, 'descricao'));
            $substituicaoJulgamentoFinalTO->setNomeArquivo(Arr::get($data, 'nomeArquivo'));
            $substituicaoJulgamentoFinalTO->setSequencia(Arr::get($data, 'sequencia'));
            $substituicaoJulgamentoFinalTO->setTipo(Constants::SUBSTITUICAO_JULGAMENTO_TO_TIPO_RECURSO);

            if (!empty(Arr::get($data, 'nomeArquivo'))) {
                $arquivo = ArquivoGenericoTO::newInstance([
                    'nome' => Arr::get($data, 'nomeArquivo'),
                    'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                ]);
                $substituicaoJulgamentoFinalTO->setArquivos([$arquivo]);
            }

            $substituicaoJulgamentoFinalTO->setDataCadastro(Arr::get($data, 'dataCadastro'));

            $profissional = Arr::get($data, 'profissional');
            if (!empty($profissional)) {
                $substituicaoJulgamentoFinalTO->setProfissional(ProfissionalTO::newInstance($profissional));
            }

            $membrosSubstituicaoJulgamentoFinal = Arr::get(
                $data,
                'julgamentoSegundaInstanciaSubstituicao.substituicaoJulgamentoFinal.membrosSubstituicaoJulgamentoFinal'
            );
            if (!empty($membrosSubstituicaoJulgamentoFinal)) {
                $membros = [];
                foreach ($membrosSubstituicaoJulgamentoFinal as $membro) {
                    array_push($membros, MembroSubstituicaoJulgamentoFinalTO::newInstance($membro));
                }
                $substituicaoJulgamentoFinalTO->setMembrosSubstituicaoJulgamentoFinal($membros);
            }

        }

        return $substituicaoJulgamentoFinalTO;
    }

    /**
     * Retorna uma nova instância de 'PedidoImpugnacaoTO'.
     *
     * @param SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
     * @param bool $isResumo
     * @return SubstituicaoJulgamentoFinalTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($substituicaoJulgamentoFinal, $isResumo = false)
    {
        $substituicaoJulgamentoFinalTO = new SubstituicaoJulgamentoFinalTO();

        if (!empty($substituicaoJulgamentoFinal)) {
            $substituicaoJulgamentoFinalTO->setId($substituicaoJulgamentoFinal->getId());
            $substituicaoJulgamentoFinalTO->setJustificativa($substituicaoJulgamentoFinal->getJustificativa());
            $substituicaoJulgamentoFinalTO->setDataCadastro($substituicaoJulgamentoFinal->getDataCadastro());
            $substituicaoJulgamentoFinalTO->setNomeArquivo($substituicaoJulgamentoFinal->getNomeArquivo());
            $substituicaoJulgamentoFinalTO->setTipo(Constants::SUBSTITUICAO_JULGAMENTO_TO_TIPO_SUBSTITUICAO);

            if (!$isResumo) {
                $arquivos = [];
                if (!empty($substituicaoJulgamentoFinal->getNomeArquivo())) {
                    $arquivos[] = ArquivoGenericoTO::newInstance([
                        'nome' => $substituicaoJulgamentoFinal->getNomeArquivo(),
                        'nomeFisico' => $substituicaoJulgamentoFinal->getNomeArquivoFisico()
                    ]);
                }
                $substituicaoJulgamentoFinalTO->setArquivos($arquivos);

                $substituicaoJulgamentoFinalTO->setProfissional(ProfissionalTO::newInstanceFromEntity(
                    $substituicaoJulgamentoFinal->getProfissional()
                ));

                $membrosArray = $substituicaoJulgamentoFinal->getMembrosSubstituicaoJulgamentoFinal();
                if (!empty($membrosArray)) {
                    $membros = [];
                    foreach ($membrosArray as $membro) {
                        array_push($membros, MembroSubstituicaoJulgamentoFinalTO::newInstanceFromEntity($membro));
                    }
                    $substituicaoJulgamentoFinalTO->setMembrosSubstituicaoJulgamentoFinal($membros);
                }
            }
        }

        return $substituicaoJulgamentoFinalTO;
    }

    /**
     * Retorna uma nova instância de 'PedidoImpugnacaoTO'.
     *
     * @param RecursoSegundoJulgamentoSubstituicao $recursoSegundoJulgamentoSubstituicao
     * @param bool $isResumo
     * @return SubstituicaoJulgamentoFinalTO
     * @throws \Exception
     */
    public static function newInstanceFromEntityRecurso($recursoSegundoJulgamentoSubstituicao, $membrosSubstituicao = null)
    {
        $substituicaoJulgamentoFinalTO = new SubstituicaoJulgamentoFinalTO();

        if (!empty($recursoSegundoJulgamentoSubstituicao)) {
            $substituicaoJulgamentoFinalTO->setId($recursoSegundoJulgamentoSubstituicao->getId());
            $substituicaoJulgamentoFinalTO->setJustificativa($recursoSegundoJulgamentoSubstituicao->getDescricao());
            $substituicaoJulgamentoFinalTO->setDataCadastro($recursoSegundoJulgamentoSubstituicao->getDataCadastro());
            $substituicaoJulgamentoFinalTO->setNomeArquivo($recursoSegundoJulgamentoSubstituicao->getNomeArquivo());
            $substituicaoJulgamentoFinalTO->setTipo(Constants::SUBSTITUICAO_JULGAMENTO_TO_TIPO_RECURSO);

            $arquivos = [];
            if (!empty($recursoSegundoJulgamentoSubstituicao->getNomeArquivo())) {
                $arquivos[] = ArquivoGenericoTO::newInstance([
                    'nome' => $recursoSegundoJulgamentoSubstituicao->getNomeArquivo(),
                    'nomeFisico' => $recursoSegundoJulgamentoSubstituicao->getNomeArquivoFisico()
                ]);
            }

            $substituicaoJulgamentoFinalTO->setProfissional(ProfissionalTO::newInstanceFromEntity(
                $recursoSegundoJulgamentoSubstituicao->getProfissional()
            ));

            $membrosSubstituicao = null;
            $julgamentoSubstituicao = $recursoSegundoJulgamentoSubstituicao->getJulgamentoSegundaInstanciaSubstituicao();

            if (!empty($julgamentoSubstituicao)) {
                $substituicao = $julgamentoSubstituicao->getSubstituicaoJulgamentoFinal();
                if (!empty($substituicao)) {
                    $membrosSubstituicao = $substituicao->getMembrosSubstituicaoJulgamentoFinal();
                }
            }

            if (!empty($membrosSubstituicao)) {
                $membros = [];
                foreach ($membrosSubstituicao as $membro) {
                    array_push($membros, MembroSubstituicaoJulgamentoFinalTO::newInstanceFromEntity($membro));
                }
                $substituicaoJulgamentoFinalTO->setMembrosSubstituicaoJulgamentoFinal($membros);
            }

        }

        return $substituicaoJulgamentoFinalTO;
    }

}
