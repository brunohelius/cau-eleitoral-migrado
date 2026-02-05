<?php


namespace App\To;

use App\Entities\JulgamentoImpugnacao;
use App\Entities\JulgamentoSubstituicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\StatusJulgamentoImpugnacao;
use App\Entities\StatusJulgamentoSubstituicao;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Julgamento de Impugnação
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoImpugnacaoTO
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
     * @var PedidoImpugnacaoTO
     */
    private $pedidoImpugnacao;

    /**
     * @var StatusJulgamentoImpugnacao
     */
    private $statusJulgamentoImpugnacao;

    /**
     * @var mixed
     */
    private $arquivo;

    /**
     * @var $tamanho
     */
    private $tamanho;

    /**
     * @var $idStatusJulgamentoImpugnacao
     */
    private $idStatusJulgamentoImpugnacao;

    /**
     * @var $idPedidoImpugnacao
     */
    private $idPedidoImpugnacao;

    /**
     * @var UsuarioTO $usuario
     */
    private $usuario;

    /**
     * @var $isFinalizadoAtividadeRecurso
     */
    private $isFinalizadoAtividadeRecurso;

    /**
     * @var $isConcluidoRecurso
     */
    private $isConcluidoRecurso;

    /**
     * @var $isConcluidoRecursoResponsavel
     */
    private $isConcluidoRecursoResponsavel;

    /**
     * @var $isConcluidoRecursoImpugnante
     */
    private $isConcluidoRecursoImpugnante;

    /**
     * @var $isConcluidoReconsideracao
     */
    private $isConcluidoReconsideracao;

    /**
     * @var $isConcluidoSubstituicao
     */
    private $isConcluidoSubstituicao;

    /**
     * @var $arquivos
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
     * @return PedidoImpugnacaoTO
     */
    public function getPedidoImpugnacao(): ?PedidoImpugnacaoTO
    {
        return $this->pedidoImpugnacao;
    }

    /**
     * @param PedidoImpugnacaoTO $pedidoImpugnacao
     */
    public function setPedidoImpugnacao(?PedidoImpugnacaoTO $pedidoImpugnacao): void
    {
        $this->pedidoImpugnacao = $pedidoImpugnacao;
    }

    /**
     * @return StatusJulgamentoImpugnacao
     */
    public function getStatusJulgamentoImpugnacao(): ?StatusJulgamentoImpugnacao
    {
        return $this->statusJulgamentoImpugnacao;
    }

    /**
     * @param StatusJulgamentoImpugnacao $statusJulgamentoImpugnacao
     */
    public function setStatusJulgamentoImpugnacao(?StatusJulgamentoImpugnacao $statusJulgamentoImpugnacao): void
    {
        $this->statusJulgamentoImpugnacao = $statusJulgamentoImpugnacao;
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
    public function getIdStatusJulgamentoImpugnacao()
    {
        return $this->idStatusJulgamentoImpugnacao;
    }

    /**
     * @param mixed $idStatusJulgamentoImpugnacao
     */
    public function setIdStatusJulgamentoImpugnacao($idStatusJulgamentoImpugnacao): void
    {
        $this->idStatusJulgamentoImpugnacao = $idStatusJulgamentoImpugnacao;
    }

    /**
     * @return mixed
     */
    public function getIdPedidoImpugnacao()
    {
        return $this->idPedidoImpugnacao;
    }

    /**
     * @param mixed $idPedidoImpugnacao
     */
    public function setIdPedidoImpugnacao($idPedidoImpugnacao): void
    {
        $this->idPedidoImpugnacao = $idPedidoImpugnacao;
    }

    /**
     * @return UsuarioTO
     */
    public function getUsuario(): ?UsuarioTO
    {
        return $this->usuario;
    }

    /**
     * @param UsuarioTO $usuario
     */
    public function setUsuario(?UsuarioTO $usuario): void
    {
        $this->usuario = $usuario;
    }

    /**
     * @return mixed
     */
    public function getIsFinalizadoAtividadeRecurso()
    {
        return $this->isFinalizadoAtividadeRecurso;
    }

    /**
     * @param mixed $isFinalizadoAtividadeRecurso
     */
    public function setIsFinalizadoAtividadeRecurso($isFinalizadoAtividadeRecurso): void
    {
        $this->isFinalizadoAtividadeRecurso = $isFinalizadoAtividadeRecurso;
    }

    /**
     * @return mixed
     */
    public function getIsConcluidoRecurso()
    {
        return $this->isConcluidoRecurso;
    }

    /**
     * @param mixed $isConcluidoRecurso
     */
    public function setIsConcluidoRecurso($isConcluidoRecurso): void
    {
        $this->isConcluidoRecurso = $isConcluidoRecurso;
    }

    /**
     * @return mixed
     */
    public function getIsConcluidoRecursoResponsavel()
    {
        return $this->isConcluidoRecursoResponsavel;
    }

    /**
     * @param mixed $isConcluidoRecursoResponsavel
     */
    public function setIsConcluidoRecursoResponsavel($isConcluidoRecursoResponsavel): void
    {
        $this->isConcluidoRecursoResponsavel = $isConcluidoRecursoResponsavel;
    }

    /**
     * @return mixed
     */
    public function getIsConcluidoRecursoImpugnante()
    {
        return $this->isConcluidoRecursoImpugnante;
    }

    /**
     * @param mixed $isConcluidoRecursoImpugnante
     */
    public function setIsConcluidoRecursoImpugnante($isConcluidoRecursoImpugnante): void
    {
        $this->isConcluidoRecursoImpugnante = $isConcluidoRecursoImpugnante;
    }

    /**
     * @return mixed
     */
    public function getIsConcluidoReconsideracao()
    {
        return $this->isConcluidoReconsideracao;
    }

    /**
     * @param mixed $isConcluidoReconsideracao
     */
    public function setIsConcluidoReconsideracao($isConcluidoReconsideracao): void
    {
        $this->isConcluidoReconsideracao = $isConcluidoReconsideracao;
    }

    /**
     * @return mixed
     */
    public function getIsConcluidoSubstituicao()
    {
        return $this->isConcluidoSubstituicao;
    }

    /**
     * @param mixed $isConcluidoSubstituicao
     */
    public function setIsConcluidoSubstituicao($isConcluidoSubstituicao): void
    {
        $this->isConcluidoSubstituicao = $isConcluidoSubstituicao;
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
     * Retorna uma nova instância de 'JulgamentoImpugnacaoTO'.
     *
     * @param null $data
     * @return JulgamentoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoImpugnacaoTO = new JulgamentoImpugnacaoTO();

        if ($data != null) {
            $julgamentoImpugnacaoTO->setId(Arr::get($data, 'id'));
            $julgamentoImpugnacaoTO->setDescricao(Arr::get($data, 'descricao'));
            $julgamentoImpugnacaoTO->setArquivo(Arr::get($data, 'arquivo'));
            $julgamentoImpugnacaoTO->setTamanho(Arr::get($data, 'tamanho'));
            $julgamentoImpugnacaoTO->setNomeArquivo(Arr::get($data, 'nomeArquivo'));
            $julgamentoImpugnacaoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $julgamentoImpugnacaoTO->setNomeArquivoFisico(Arr::get($data, 'nomeArquivoFisico'));
            $julgamentoImpugnacaoTO->setIdStatusJulgamentoImpugnacao(Arr::get($data,
                'idStatusJulgamentoImpugnacao'));
            $julgamentoImpugnacaoTO->setIdPedidoImpugnacao(Arr::get($data, 'idPedidoImpugnacao'));
            $pedidoImpugnacao = Arr::get($data, 'pedidoImpugnacao');
            if (!empty($pedidoImpugnacao)) {
                $julgamentoImpugnacaoTO->setPedidoImpugnacao(
                    PedidoImpugnacaoTO::newInstance($pedidoImpugnacao)
                );
            }

            $status = Arr::get($data, 'statusJulgamentoImpugnacao');
            if (!empty($status)) {
                $julgamentoImpugnacaoTO->setStatusJulgamentoImpugnacao(StatusJulgamentoImpugnacao::newInstance($status));
            }

            $usuario = Arr::get($data, 'usuario');
            if (!empty($usuario)) {
                $julgamentoImpugnacaoTO->setUsuario(UsuarioTO::newInstance($usuario));
            }

            $arquivos = Arr::get($data, 'arquivos');
            if (!empty($arquivos)) {
                $julgamentoImpugnacaoTO->setArquivos(array_map(function($arquivo){
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else {
                $arquivo = ArquivoGenericoTO::newInstance([
                    'nome' => Arr::get($data, 'nomeArquivo'),
                    'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                ]);
                $julgamentoImpugnacaoTO->setArquivos([$arquivo]);
            }
        }

        return $julgamentoImpugnacaoTO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoImpugnacaoTO'.
     *
     * @param JulgamentoImpugnacao $julgamentoImpugnacao
     * @param bool $isResumo
     * @return JulgamentoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity(JulgamentoImpugnacao $julgamentoImpugnacao, bool $isResumo = false)
    {
        $julgamentoImpugnacaoTO = new JulgamentoImpugnacaoTO();

        if (!empty($julgamentoImpugnacao)) {
            $julgamentoImpugnacaoTO->setId($julgamentoImpugnacao->getId());
            $julgamentoImpugnacaoTO->setDescricao($julgamentoImpugnacao->getDescricao());

            if (!$isResumo) {
                $julgamentoImpugnacaoTO->setDataCadastro($julgamentoImpugnacao->getDataCadastro());
                $julgamentoImpugnacaoTO->setNomeArquivo($julgamentoImpugnacao->getNomeArquivo());
                $julgamentoImpugnacaoTO->setNomeArquivoFisico($julgamentoImpugnacao->getNomeArquivoFisico());

                $julgamentoImpugnacaoTO->setUsuario(UsuarioTO::newInstanceFromEntity(
                    $julgamentoImpugnacao->getUsuario()
                ));

                $julgamentoImpugnacaoTO->setStatusJulgamentoImpugnacao(StatusJulgamentoImpugnacao::newInstance([
                    'id' => $julgamentoImpugnacao->getStatusJulgamentoImpugnacao()->getId(),
                    'descricao' => $julgamentoImpugnacao->getStatusJulgamentoImpugnacao()->getDescricao()
                ]));

                if(!empty($julgamentoImpugnacao->getPedidoImpugnacao())) {
                    $julgamentoImpugnacaoTO->setPedidoImpugnacao(PedidoImpugnacaoTO::newInstanceFromEntity(
                        $julgamentoImpugnacao->getPedidoImpugnacao()
                    ));
                }

                $arquivo = ArquivoGenericoTO::newInstance([
                    'nome' => $julgamentoImpugnacao->getNomeArquivo(),
                    'nomeFisico' => $julgamentoImpugnacao->getNomeArquivoFisico()
                ]);
                $julgamentoImpugnacaoTO->setArquivos([$arquivo]);

            }
        }

        return $julgamentoImpugnacaoTO;
    }

}
