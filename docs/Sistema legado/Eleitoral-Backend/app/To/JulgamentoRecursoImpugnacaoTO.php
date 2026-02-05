<?php


namespace App\To;

use App\Entities\JulgamentoRecursoImpugnacao;
use App\Entities\JulgamentoRecursoSubstituicao;
use App\Entities\JulgamentoSubstituicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\StatusJulgamentoSubstituicao;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Julgamento do Recurso de Impugnação
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoImpugnacaoTO
{

    /**
     * @var integer|null $id
     */
    private $id;

    /**
     * @var string|null $parecer
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
     * @var StatusGenericoTO
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
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivos;

    /**
     * @var bool
     */
    private $podeCadastrarContrarrazao;

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
     * @return StatusGenericoTO
     */
    public function getStatusJulgamentoImpugnacao(): ?StatusGenericoTO
    {
        return $this->statusJulgamentoImpugnacao;
    }

    /**
     * @param StatusGenericoTO $statusJulgamentoImpugnacao
     */
    public function setStatusJulgamentoImpugnacao(?StatusGenericoTO $statusJulgamentoImpugnacao): void
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
     * @return bool
     */
    public function isPodeCadastrarContrarrazao(): ?bool
    {
        return $this->podeCadastrarContrarrazao;
    }

    /**
     * @param bool $podeCadastrarContrarrazao
     */
    public function setPodeCadastrarContrarrazao(?bool $podeCadastrarContrarrazao): void
    {
        $this->podeCadastrarContrarrazao = $podeCadastrarContrarrazao;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoImpugnacaoTO'.
     *
     * @param null $data
     * @return JulgamentoRecursoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoRecursoImpugnacaoTO = new JulgamentoRecursoImpugnacaoTO();

        if ($data != null) {
            $julgamentoRecursoImpugnacaoTO->setId(Arr::get($data, 'id'));
            $julgamentoRecursoImpugnacaoTO->setArquivo(Arr::get($data, 'arquivo'));
            $julgamentoRecursoImpugnacaoTO->setTamanho(Arr::get($data, 'tamanho'));
            $julgamentoRecursoImpugnacaoTO->setDescricao(Arr::get($data, 'descricao'));
            $julgamentoRecursoImpugnacaoTO->setNomeArquivo(Arr::get($data, 'nomeArquivo'));
            $julgamentoRecursoImpugnacaoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $julgamentoRecursoImpugnacaoTO->setNomeArquivoFisico(Arr::get($data, 'nomeArquivoFisico'));
            $julgamentoRecursoImpugnacaoTO->setIdPedidoImpugnacao(Arr::get($data, 'idPedidoImpugnacao'));

            $julgamentoRecursoImpugnacaoTO->setIdStatusJulgamentoImpugnacao(
                Arr::get($data,'idStatusJulgamentoImpugnacao')
            );

            $pedidoImpugnacao = Arr::get($data, 'pedidoImpugnacao');
            if (!empty($pedidoImpugnacao)) {
                $julgamentoRecursoImpugnacaoTO->setPedidoImpugnacao(
                    PedidoImpugnacaoTO::newInstance($pedidoImpugnacao)
                );
            }

            $status = Arr::get($data, 'statusJulgamentoImpugnacao');
            if (!empty($status)) {
                $julgamentoRecursoImpugnacaoTO->setStatusJulgamentoImpugnacao(
                    StatusGenericoTO::newInstance($status)
                );
            }

            $usuario = Arr::get($data, 'usuario');
            if (!empty($usuario)) {
                $julgamentoRecursoImpugnacaoTO->setUsuario(UsuarioTO::newInstance($usuario));
            }

            $arquivos = Arr::get($data, 'arquivos');
            if (!empty($arquivos)) {
                $julgamentoRecursoImpugnacaoTO->setArquivos(array_map(function($arquivo){
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else if(!empty(Arr::get($data, 'nomeArquivo'))){
                $arquivo = ArquivoGenericoTO::newInstance([
                    'nome' => Arr::get($data, 'nomeArquivo'),
                    'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                ]);
                $julgamentoRecursoImpugnacaoTO->setArquivos([$arquivo]);
            }
        }

        return $julgamentoRecursoImpugnacaoTO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoImpugnacaoTO'.
     *
     * @param JulgamentoRecursoImpugnacao $julgamentoRecursoImpugnacao
     * @param bool $isResumo
     * @return JulgamentoRecursoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($julgamentoRecursoImpugnacao, $isResumo = false)
    {
        $julgamentoRecursoImpugnacaoTO = new JulgamentoRecursoImpugnacaoTO();

        if (!empty($julgamentoRecursoImpugnacao)) {
            $julgamentoRecursoImpugnacaoTO->setId($julgamentoRecursoImpugnacao->getId());
            $julgamentoRecursoImpugnacaoTO->setDescricao($julgamentoRecursoImpugnacao->getDescricao());
            $julgamentoRecursoImpugnacaoTO->setDataCadastro($julgamentoRecursoImpugnacao->getDataCadastro());

            if (!$isResumo) {
                $julgamentoRecursoImpugnacaoTO->setNomeArquivo($julgamentoRecursoImpugnacao->getNomeArquivo());
                $julgamentoRecursoImpugnacaoTO->setNomeArquivoFisico($julgamentoRecursoImpugnacao->getNomeArquivoFisico());

                $julgamentoRecursoImpugnacaoTO->setUsuario(UsuarioTO::newInstanceFromEntity(
                    $julgamentoRecursoImpugnacao->getUsuario()
                ));

                $julgamentoRecursoImpugnacaoTO->setStatusJulgamentoImpugnacao(StatusGenericoTO::newInstance([
                    'id' => $julgamentoRecursoImpugnacao->getStatusJulgamentoImpugnacao()->getId(),
                    'descricao' => $julgamentoRecursoImpugnacao->getStatusJulgamentoImpugnacao()->getDescricao()
                ]));

                $arquivos = [];
                if(!empty($julgamentoRecursoImpugnacaoTO->getNomeArquivo())) {
                    $arquivos[] = ArquivoGenericoTO::newInstance([
                        'nome' => $julgamentoRecursoImpugnacaoTO->getNomeArquivo(),
                        'nomeFisico' => $julgamentoRecursoImpugnacaoTO->getNomeArquivoFisico()
                    ]);
                }
                $julgamentoRecursoImpugnacaoTO->setArquivos($arquivos);

                $pedidoImpugnacao = $julgamentoRecursoImpugnacao->getPedidoImpugnacao();
                if (!empty($pedidoImpugnacao)) {
                    $julgamentoRecursoImpugnacaoTO->setPedidoImpugnacao(
                        PedidoImpugnacaoTO::newInstanceFromEntity($pedidoImpugnacao)
                    );
                }
            }
        }

        return $julgamentoRecursoImpugnacaoTO;
    }

}
