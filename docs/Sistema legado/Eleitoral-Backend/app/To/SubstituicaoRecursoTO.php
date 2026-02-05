<?php


namespace App\To;


use App\Entities\JulgamentoRecursoPedidoSubstituicao;
use App\Entities\JulgamentoSegundaInstanciaRecurso;
use App\Entities\JulgamentoSegundaInstanciaSubstituicao;
use App\Entities\RecursoJulgamentoFinal;
use App\Util\Utils;

class SubstituicaoRecursoTO
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string | null $descricao
     */
    private $descricao;

    /**
     * @var string | null $retificacaoJustificativa
     */
    private $retificacaoJustificativa;

    /**
     * @var string | null $nomeArquivo
     */
    private $nomeArquivo;

    /**
     * @var string | null $nomeArquivoFisico
     */
    private $nomeArquivoFisico;

    /**
     * @var string | null $dataCadastro
     */
    private $dataCadastro;

    /**
     * @var null | UsuarioTO $profissional
     */
    private $profissional;

    /**
     * @var string | JulgamentoFinalTO $julgamentoFinal
     */
    private $julgamentoFinal;

    /**
     * @var string | Array $membrosSubstituicaoJulgamentoFinal
     */
    private $membrosSubstituicaoJulgamentoFinal;

    /**
     * @var string | Array $julgamentoSegundaInstanciaSubstituicao
     */
    private $julgamentoSegundaInstanciaSubstituicao;

    /**
     * @var string | SubstituicaoJulgamentoFinalTO $substituicaoJulgamentoFinal
     */
    private $substituicaoJulgamentoFinal;

    /**
     * @var string | StatusGenericoTO $statusJulgamentoFinal
     */
    private $statusJulgamentoFinal;

    /**
     * @var string | Array $recursoSegundoJulgamentoSubstituicao
     */
    private $recursoSegundoJulgamentoSubstituicao;

    /**
     * @var string | IndicacaoJulgamentoFinalTO[] $indicacoes
     */
    private $indicacoes;

    /**
     * @var string|null
     */
    private $sequencia;

    /**
     * @var string|null
     */
    private $tipo;

    /**
     * @var int|null
     */
    private $idRecursoSegundoJulgamentoSubstituicao;

    /**
     * @var RecursoJulgamentoFinalTO
     */
    private $recursoJulgamentoFinal;

    /**
     * Retorna uma nova instância de 'SubstituicaoRecursoTO'.
     *
     * @param JulgamentoSegundaInstanciaSubstituicao $julgamento
     * @return SubstituicaoRecursoTO
     * @throws \Exception
     */
    public static function newInstanceFromJulgamentoSegundaInstanciaSubstituicao($julgamento = null)
    {
        $substituicaoRecursoTO = new SubstituicaoRecursoTO();
        if(!empty($julgamento)) {
            $substituicaoRecursoTO->setId($julgamento->getId());
            $substituicaoRecursoTO->setDescricao($julgamento->getDescricao());
            $substituicaoRecursoTO->setRetificacaoJustificativa($julgamento->getRetificacaoJustificativa());
            $substituicaoRecursoTO->setNomeArquivo($julgamento->getNomeArquivo());
            $substituicaoRecursoTO->setNomeArquivoFisico($julgamento->getNomeArquivoFisico());
            $substituicaoRecursoTO->setDataCadastro($julgamento->getDataCadastro());
            $substituicaoRecursoTO->setTipo('substituicao');

            $indicacoes = $julgamento->getIndicacoes();
            if (!empty($indicacoes)) {
                $indicacoesTO = [];
                foreach ($indicacoes as $indicacao) {
                    $indicacoesTO[] = IndicacaoJulgamentoFinalTO::newInstanceFromEntity($indicacao);
                }
                $substituicaoRecursoTO->setIndicacoes($indicacoesTO);
            }

            $substituicaoRecursoTO->setProfissional(UsuarioTO::newInstanceFromEntity($julgamento->getUsuario()));

            $substituicaoRecursoTO->setSubstituicaoJulgamentoFinal(
                SubstituicaoJulgamentoFinalTO::newInstanceFromEntity($julgamento->getSubstituicaoJulgamentoFinal())
            );

            $substituicaoRecursoTO->setStatusJulgamentoFinal(
                StatusGenericoTO::newInstance([
                    "id" => $julgamento->getStatusJulgamentoFinal()->getId(),
                    "descricao" => $julgamento->getStatusJulgamentoFinal()->getDescricao()
                ])
            );
        }
        return $substituicaoRecursoTO;
    }

    /**
     * Retorna uma nova instância de 'SubstituicaoRecursoTO'.
     *
     * @param JulgamentoSegundaInstanciaRecurso $julgamento
     * @return SubstituicaoRecursoTO
     * @throws \Exception
     */
    public static function newInstanceFromJulgamentoSegundaInstanciaRecurso($julgamento = null)
    {
        $substituicaoRecursoTO = new SubstituicaoRecursoTO();
        if(!empty($julgamento)) {
            $substituicaoRecursoTO->setId($julgamento->getId());
            $substituicaoRecursoTO->setDescricao($julgamento->getDescricao());
            $substituicaoRecursoTO->setRetificacaoJustificativa($julgamento->getRetificacaoJustificativa());
            $substituicaoRecursoTO->setNomeArquivo($julgamento->getNomeArquivo());
            $substituicaoRecursoTO->setNomeArquivoFisico($julgamento->getNomeArquivoFisico());
            $substituicaoRecursoTO->setDataCadastro($julgamento->getDataCadastro());

            $indicacoes = $julgamento->getIndicacoes();
            if (!empty($indicacoes)) {
                $indicacoesTO = [];
                foreach ($indicacoes as $indicacao) {
                    $indicacoesTO[] = IndicacaoJulgamentoFinalTO::newInstanceFromEntity($indicacao);
                }
                $substituicaoRecursoTO->setIndicacoes($indicacoesTO);
            }

            $substituicaoRecursoTO->setProfissional(UsuarioTO::newInstanceFromEntity($julgamento->getUsuario()));

            $recurso = $julgamento->getRecursoJulgamentoFinal();
            if (!empty($recurso)) {
                $substituicaoRecursoTO->setRecursoJulgamentoFinal(
                    RecursoJulgamentoFinalTO::newInstanceFromEntityByJulgamentoRecurso($julgamento->getRecursoJulgamentoFinal())
                );
            }

            $substituicaoRecursoTO->setStatusJulgamentoFinal(
                StatusGenericoTO::newInstance([
                    "id" => $julgamento->getStatusJulgamentoFinal()->getId(),
                    "descricao" => $julgamento->getStatusJulgamentoFinal()->getDescricao()
                ])
            );
        }
        return $substituicaoRecursoTO;
    }

    /**
     * Retorna uma nova instância de 'SubstituicaoRecursoTO'.
     *
     * @param JulgamentoRecursoPedidoSubstituicao $julgamento
     * @return SubstituicaoRecursoTO
     * @throws \Exception
     */
    public static function newInstanceFromJulgamentoRecursoPedidoSubstituicao($julgamento = null)
    {
        $substituicaoRecursoTO = new SubstituicaoRecursoTO();
        if(!empty($julgamento)) {
            $substituicaoRecursoTO->setId($julgamento->getId());
            $substituicaoRecursoTO->setDescricao($julgamento->getDescricao());
            $substituicaoRecursoTO->setRetificacaoJustificativa($julgamento->getRetificacaoJustificativa());
            $substituicaoRecursoTO->setNomeArquivo($julgamento->getNomeArquivo());
            $substituicaoRecursoTO->setNomeArquivoFisico($julgamento->getNomeArquivoFisico());
            $substituicaoRecursoTO->setDataCadastro($julgamento->getDataCadastro());
            $substituicaoRecursoTO->setTipo('recurso');

            $indicacoes = $julgamento->getIndicacoes();
            if (!empty($indicacoes)) {
                $indicacoesTO = [];
                foreach ($indicacoes as $indicacao) {
                    $indicacoesTO[] = IndicacaoJulgamentoFinalTO::newInstanceFromEntity($indicacao);
                }
                $substituicaoRecursoTO->setIndicacoes($indicacoesTO);
            }

            $substituicaoRecursoTO->setProfissional(UsuarioTO::newInstanceFromEntity($julgamento->getUsuario()));

            $substituicao = null;
            $recurso = $julgamento->getRecursoSegundoJulgamentoSubstituicao();
            if (!empty($recurso)) {
                $substituicaoRecursoTO->setIdRecursoSegundoJulgamentoSubstituicao($recurso->getId());
                $julgamentoSubstituicao = $recurso->getJulgamentoSegundaInstanciaSubstituicao();

                if (!empty($julgamentoSubstituicao)) {
                    $substituicao = $julgamentoSubstituicao->getSubstituicaoJulgamentoFinal();
                }
            }

            if (!empty($substituicao)) {
                $substituicaoRecursoTO->setSubstituicaoJulgamentoFinal(
                    SubstituicaoJulgamentoFinalTO::newInstanceFromEntity($substituicao)
                );
            }

            $substituicaoRecursoTO->setStatusJulgamentoFinal(
                StatusGenericoTO::newInstance([
                    "id" => $julgamento->getStatusJulgamentoFinal()->getId(),
                    "descricao" => $julgamento->getStatusJulgamentoFinal()->getDescricao()
                ])
            );
        }
        return $substituicaoRecursoTO;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
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
     * @return mixed
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @param mixed $dataCadastro
     */
    public function setDataCadastro($dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return mixed
     */
    public function getProfissional()
    {
        return $this->profissional;
    }

    /**
     * @param mixed $profissional
     */
    public function setProfissional($profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return mixed
     */
    public function getJulgamentoFinal()
    {
        return $this->julgamentoFinal;
    }

    /**
     * @param mixed $julgamentoFinal
     */
    public function setJulgamentoFinal($julgamentoFinal): void
    {
        $this->julgamentoFinal = $julgamentoFinal;
    }

    /**
     * @return mixed
     */
    public function getMembrosSubstituicaoJulgamentoFinal()
    {
        return $this->membrosSubstituicaoJulgamentoFinal;
    }

    /**
     * @param mixed $membrosSubstituicaoJulgamentoFinal
     */
    public function setMembrosSubstituicaoJulgamentoFinal($membrosSubstituicaoJulgamentoFinal): void
    {
        $this->membrosSubstituicaoJulgamentoFinal = $membrosSubstituicaoJulgamentoFinal;
    }

    /**
     * @return mixed
     */
    public function getJulgamentoSegundaInstanciaSubstituicao()
    {
        return $this->julgamentoSegundaInstanciaSubstituicao;
    }

    /**
     * @param mixed $julgamentoSegundaInstanciaSubstituicao
     */
    public function setJulgamentoSegundaInstanciaSubstituicao($julgamentoSegundaInstanciaSubstituicao): void
    {
        $this->julgamentoSegundaInstanciaSubstituicao = $julgamentoSegundaInstanciaSubstituicao;
    }

    /**
     * @return mixed
     */
    public function getSubstituicaoJulgamentoFinal()
    {
        return $this->substituicaoJulgamentoFinal;
    }

    /**
     * @param mixed $substituicaoJulgamentoFinal
     */
    public function setSubstituicaoJulgamentoFinal($substituicaoJulgamentoFinal): void
    {
        $this->substituicaoJulgamentoFinal = $substituicaoJulgamentoFinal;
    }

    /**
     * @return mixed
     */
    public function getStatusJulgamentoFinal()
    {
        return $this->statusJulgamentoFinal;
    }

    /**
     * @param mixed $statusJulgamentoFinal
     */
    public function setStatusJulgamentoFinal($statusJulgamentoFinal): void
    {
        $this->statusJulgamentoFinal = $statusJulgamentoFinal;
    }

    /**
     * @return mixed
     */
    public function getRecursoSegundoJulgamentoSubstituicao()
    {
        return $this->recursoSegundoJulgamentoSubstituicao;
    }

    /**
     * @param mixed $recursoSegundoJulgamentoSubstituicao
     */
    public function setRecursoSegundoJulgamentoSubstituicao($recursoSegundoJulgamentoSubstituicao): void
    {
        $this->recursoSegundoJulgamentoSubstituicao = $recursoSegundoJulgamentoSubstituicao;
    }

    /**
     * @return mixed
     */
    public function getIndicacoes()
    {
        return $this->indicacoes;
    }

    /**
     * @param mixed $indicacoes
     */
    public function setIndicacoes($indicacoes): void
    {
        $this->indicacoes = $indicacoes;
    }

    /**
     * @return string|null
     */
    public function getSequencia()
    {
        return $this->sequencia;
    }

    /**
     * @param string|null $sequencia
     */
    public function setSequencia($sequencia): void
    {
        $this->sequencia = null;

        if (!empty($sequencia)) {
            $this->sequencia = str_pad($sequencia, 2, '0', STR_PAD_LEFT);
        }
    }

    /**
     * @return string|null
     */
    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    /**
     * @param string|null $tipo
     */
    public function setTipo(?string $tipo): void
    {
        $this->tipo = $tipo;
    }

    /**
     * @return string|null
     */
    public function getRetificacaoJustificativa(): ?string
    {
        return $this->retificacaoJustificativa;
    }

    /**
     * @param string|null $retificacaoJustificativa
     */
    public function setRetificacaoJustificativa(?string $retificacaoJustificativa): void
    {
        $this->retificacaoJustificativa = $retificacaoJustificativa;
    }

    /**
     * @return int|null
     */
    public function getIdRecursoSegundoJulgamentoSubstituicao(): ?int
    {
        return $this->idRecursoSegundoJulgamentoSubstituicao;
    }

    /**
     * @param int|null $idRecursoSegundoJulgamentoSubstituicao
     */
    public function setIdRecursoSegundoJulgamentoSubstituicao(?int $idRecursoSegundoJulgamentoSubstituicao): void
    {
        $this->idRecursoSegundoJulgamentoSubstituicao = $idRecursoSegundoJulgamentoSubstituicao;
    }

    /**
     * @return RecursoJulgamentoFinalTO
     */
    public function getRecursoJulgamentoFinal()
    {
        return $this->recursoJulgamentoFinal;
    }

    /**
     * @param RecursoJulgamentoFinalTO $recursoJulgamentoFinal
     */
    public function setRecursoJulgamentoFinal($recursoJulgamentoFinal): void
    {
        $this->recursoJulgamentoFinal = $recursoJulgamentoFinal;
    }


}
