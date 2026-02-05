<?php


namespace App\To;

use App\Entities\IndicacaoJulgamentoSegundaInstanciaRecurso;
use App\Entities\JulgamentoRecursoPedidoSubstituicao;
use App\Entities\JulgamentoSegundaInstanciaRecurso;
use App\Entities\RecursoJulgamentoFinal;
use App\Entities\RecursoSegundoJulgamentoSubstituicao;
use App\Entities\SubstituicaoJulgamentoFinal;
use App\Util\Utils;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a 'JulgamentoRecursoPedidoSubstituicaoTO'
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoPedidoSubstituicaoTO
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
     * @var \DateTime|null
     */
    private $dataCadastro;

    /**
     * @var RecursoSegundoJulgamentoSubstituicaoTO|null
     */
    private $recursoSegundoJulgamentoSubstituicao;

    /**
     * @var StatusGenericoTO|null
     */
    private $statusJulgamentoFinal;

    /**
     * @var integer|null $idStatusJulgamentoFinal
     */
    private $idStatusJulgamentoFinal;

    /**
     * @var integer|null $idRecursoSegundoJulgamentoSubstituicao
     */
    private $idRecursoSegundoJulgamentoSubstituicao;

    /**
     * @var UsuarioTO|null $usuario
     */
    private $usuario;

    /**
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivos;

    /**
     * @var IndicacaoJulgamentoRecursoPedidoSubstituicaoTO[]|null
     */
    private $indicacoes;

    /**
     * @var string|null $retificacaoJustificativa
     */
    private $retificacaoJustificativa;

    /**
     * @var int|null $idJulgamentoRecursoPedidoSubstituicaoPai
     */
    private $idJulgamentoRecursoPedidoSubstituicaoPai;

    /**
     * Retorna uma nova instância de 'JulgamentoSegundaInstanciaRecursoTO'.
     *
     * @param null $data
     * @return JulgamentoRecursoPedidoSubstituicaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $entity = new JulgamentoRecursoPedidoSubstituicaoTO();

        if ($data != null) {
            $entity->setId(Arr::get($data, 'id'));
            $entity->setDescricao(Arr::get($data, 'descricao'));
            $entity->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $entity->setIdRecursoSegundoJulgamentoSubstituicao(Arr::get($data, 'idRecursoSegundoJulgamentoSubstituicao'));
            $entity->setIdStatusJulgamentoFinal(Arr::get($data, 'idStatusJulgamentoFinal'));
            $entity->setRetificacaoJustificativa(Utils::getValue('retificacaoJustificativa', $data));
            $entity->setIdJulgamentoRecursoPedidoSubstituicaoPai(
                Utils::getValue('idJulgamentoRecursoPedidoSubstituicaoPai', $data)
            );

            $recursoSegundoJulgamentoSubstituicao = Arr::get($data, 'recursoSegundoJulgamentoSubstituicao');
            if (!empty($recursoSegundoJulgamentoSubstituicao)) {
                $entity->setRecursoSegundoJulgamentoSubstituicao(RecursoSegundoJulgamentoSubstituicaoTO::newInstance(
                    $recursoSegundoJulgamentoSubstituicao
                ));
            }

            $status = Arr::get($data, 'statusJulgamentoFinal');
            if (!empty($status)) {
                $entity->setStatusJulgamentoFinal(StatusGenericoTO::newInstance($status));
            }

            $usuario = Arr::get($data, 'usuario');
            if (!empty($usuario)) {
                $entity->setUsuario(UsuarioTO::newInstance($usuario));
            }

            $indicacoesArray = Utils::getValue('indicacoes', $data);
            if (!empty($indicacoesArray)) {
                $indicacoes = [];
                foreach ($indicacoesArray as $indicacao) {
                    array_push($indicacoes, IndicacaoJulgamentoRecursoPedidoSubstituicaoTO::newInstance($indicacao));
                }
                $entity->setIndicacoes($indicacoes);
            }

            $arquivos = Arr::get($data, 'arquivos', null);
            if (!empty($arquivos)) {
                $entity->setArquivos(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else {
                if (!empty(Arr::get($data, 'nomeArquivo'))) {
                    $arquivo = ArquivoGenericoTO::newInstance([
                        'nome' => Arr::get($data, 'nomeArquivo'),
                        'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                    ]);
                    $entity->setArquivos([$arquivo]);
                }
            }

        }

        return $entity;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalTO'.
     *
     * @param JulgamentoRecursoPedidoSubstituicao $data
     * @param bool $isResumo
     * @return JulgamentoRecursoPedidoSubstituicaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($data, $isResumo = false)
    {
        $entity = new JulgamentoRecursoPedidoSubstituicaoTO();

        if (!empty($data)) {
            $entity->setId($data->getId());
            $entity->setDescricao($data->getDescricao());
            $entity->setDataCadastro($data->getDataCadastro());
            $entity->setRetificacaoJustificativa($data->getRetificacaoJustificativa());

            if (!$isResumo) {

                if (!empty($data->getUsuario())) {
                    $entity->setUsuario(UsuarioTO::newInstanceFromEntity($data->getUsuario()));
                }

                $status = $data->getStatusJulgamentoFinal();
                if (!empty($status)) {
                    $entity->setStatusJulgamentoFinal(StatusGenericoTO::newInstance([
                        'id' => $status->getId(),
                        'descricao' => $status->getDescricao()
                    ]));
                }

                $usuario = $data->getUsuario();
                if (!empty($usuario)) {
                    $entity->setUsuario(UsuarioTO::newInstanceFromEntity($usuario));
                }

                $indicacoesArray = $data->getIndicacoes();
                if (!empty($indicacoesArray)) {
                    $indicacoes = [];
                    foreach ($indicacoesArray as $indicacao) {
                        array_push($indicacoes, IndicacaoJulgamentoRecursoPedidoSubstituicaoTO::newInstanceFromEntity($indicacao));
                    }
                    $entity->setIndicacoes($indicacoes);
                }

                $arquivos = [];
                if (!empty($data->getNomeArquivo())) {
                    $arquivos[] = ArquivoGenericoTO::newInstance([
                        'nome' => $data->getNomeArquivo(),
                        'nomeFisico' => $data->getNomeArquivoFisico()
                    ]);
                }
                $entity->setArquivos($arquivos);
            }
        }

        return $entity;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string|null $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return \DateTime|null
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @param \DateTime|null $dataCadastro
     */
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return RecursoSegundoJulgamentoSubstituicaoTO|null
     */
    public function getRecursoSegundoJulgamentoSubstituicao()
    {
        return $this->recursoSegundoJulgamentoSubstituicao;
    }

    /**
     * @param RecursoSegundoJulgamentoSubstituicaoTO|null $recursoSegundoJulgamentoSubstituicao
     */
    public function setRecursoSegundoJulgamentoSubstituicao($recursoSegundoJulgamentoSubstituicao)
    {
        $this->recursoSegundoJulgamentoSubstituicao = $recursoSegundoJulgamentoSubstituicao;
    }

    /**
     * @return int|null
     */
    public function getIdRecursoSegundoJulgamentoSubstituicao()
    {
        return $this->idRecursoSegundoJulgamentoSubstituicao;
    }

    /**
     * @param int|null $idRecursoSegundoJulgamentoSubstituicao
     */
    public function setIdRecursoSegundoJulgamentoSubstituicao( $idRecursoSegundoJulgamentoSubstituicao)
    {
        $this->idRecursoSegundoJulgamentoSubstituicao = $idRecursoSegundoJulgamentoSubstituicao;
    }

    /**
     * @return StatusGenericoTO|null
     */
    public function getStatusJulgamentoFinal()
    {
        return $this->statusJulgamentoFinal;
    }

    /**
     * @param StatusGenericoTO|null $statusJulgamentoFinal
     */
    public function setStatusJulgamentoFinal($statusJulgamentoFinal)
    {
        $this->statusJulgamentoFinal = $statusJulgamentoFinal;
    }

    /**
     * @return int|null
     */
    public function getIdStatusJulgamentoFinal()
    {
        return $this->idStatusJulgamentoFinal;
    }

    /**
     * @param int|null $idStatusJulgamentoFinal
     */
    public function setIdStatusJulgamentoFinal(?int $idStatusJulgamentoFinal)
    {
        $this->idStatusJulgamentoFinal = $idStatusJulgamentoFinal;
    }

    /**
     * @return UsuarioTO|null
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param UsuarioTO|null $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * @return ArquivoGenericoTO[]|null
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param ArquivoGenericoTO[]|null $arquivos
     */
    public function setArquivos($arquivos)
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return IndicacaoJulgamentoRecursoPedidoSubstituicaoTO[]|null
     */
    public function getIndicacoes()
    {
        return $this->indicacoes;
    }

    /**
     * @param IndicacaoJulgamentoRecursoPedidoSubstituicaoTO[]|null $indicacoes
     */
    public function setIndicacoes($indicacoes)
    {
        $this->indicacoes = $indicacoes;
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
    public function getIdJulgamentoRecursoPedidoSubstituicaoPai(): ?int
    {
        return $this->idJulgamentoRecursoPedidoSubstituicaoPai;
    }

    /**
     * @param int|null $idJulgamentoRecursoPedidoSubstituicaoPai
     */
    public function setIdJulgamentoRecursoPedidoSubstituicaoPai(?int $idJulgamentoRecursoPedidoSubstituicaoPai): void
    {
        $this->idJulgamentoRecursoPedidoSubstituicaoPai = $idJulgamentoRecursoPedidoSubstituicaoPai;
    }
}
