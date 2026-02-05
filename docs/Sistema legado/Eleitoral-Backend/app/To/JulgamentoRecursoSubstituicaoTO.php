<?php


namespace App\To;

use App\Entities\JulgamentoRecursoSubstituicao;
use App\Entities\JulgamentoSubstituicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\StatusJulgamentoSubstituicao;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Julgamento do Recurso de Substituição
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoSubstituicaoTO
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
     * @var RecursoSubstituicaoTO
     */
    private $recursoSubstituicao;

    /**
     * @var StatusGenericoTO
     */
    private $statusJulgamentoSubstituicao;

    /**
     * @var mixed
     */
    private $arquivo;

    /**
     * @var $tamanho
     */
    private $tamanho;

    /**
     * @var $idStatusJulgamentoSubstituicao
     */
    private $idStatusJulgamentoSubstituicao;

    /**
     * @var $idRecursoSubstituicao
     */
    private $idRecursoSubstituicao;

    /**
     * @var UsuarioTO $usuario
     */
    private $usuario;

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
     * @return RecursoSubstituicaoTO
     */
    public function getRecursoSubstituicao(): ?RecursoSubstituicaoTO
    {
        return $this->recursoSubstituicao;
    }

    /**
     * @param RecursoSubstituicaoTO $recursoSubstituicao
     */
    public function setRecursoSubstituicao(?RecursoSubstituicaoTO $recursoSubstituicao): void
    {
        $this->recursoSubstituicao = $recursoSubstituicao;
    }

    /**
     * @return StatusGenericoTO
     */
    public function getStatusJulgamentoSubstituicao(): ?StatusGenericoTO
    {
        return $this->statusJulgamentoSubstituicao;
    }

    /**
     * @param StatusGenericoTO $statusJulgamentoSubstituicao
     */
    public function setStatusJulgamentoSubstituicao(?StatusGenericoTO $statusJulgamentoSubstituicao): void
    {
        $this->statusJulgamentoSubstituicao = $statusJulgamentoSubstituicao;
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
    public function getIdStatusJulgamentoSubstituicao()
    {
        return $this->idStatusJulgamentoSubstituicao;
    }

    /**
     * @param mixed $idStatusJulgamentoSubstituicao
     */
    public function setIdStatusJulgamentoSubstituicao($idStatusJulgamentoSubstituicao): void
    {
        $this->idStatusJulgamentoSubstituicao = $idStatusJulgamentoSubstituicao;
    }

    /**
     * @return mixed
     */
    public function getIdRecursoSubstituicao()
    {
        return $this->idRecursoSubstituicao;
    }

    /**
     * @param mixed $idRecursoSubstituicao
     */
    public function setIdRecursoSubstituicao($idRecursoSubstituicao): void
    {
        $this->idRecursoSubstituicao = $idRecursoSubstituicao;
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
     * Retorna uma nova instância de 'JulgamentoRecursoSubstituicaoTO'.
     *
     * @param null $data
     * @return JulgamentoRecursoSubstituicaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoRecursoSubstituicaoTO = new JulgamentoRecursoSubstituicaoTO();

        if ($data != null) {
            $julgamentoRecursoSubstituicaoTO->setId(Arr::get($data, 'id'));
            $julgamentoRecursoSubstituicaoTO->setArquivo(Arr::get($data, 'arquivo'));
            $julgamentoRecursoSubstituicaoTO->setTamanho(Arr::get($data, 'tamanho'));
            $julgamentoRecursoSubstituicaoTO->setDescricao(Arr::get($data, 'descricao'));
            $julgamentoRecursoSubstituicaoTO->setNomeArquivo(Arr::get($data, 'nomeArquivo'));
            $julgamentoRecursoSubstituicaoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $julgamentoRecursoSubstituicaoTO->setNomeArquivoFisico(Arr::get($data, 'nomeArquivoFisico'));
            $julgamentoRecursoSubstituicaoTO->setIdRecursoSubstituicao(Arr::get($data, 'idRecursoSubstituicao'));

            $julgamentoRecursoSubstituicaoTO->setIdStatusJulgamentoSubstituicao(
                Arr::get($data,'idStatusJulgamentoSubstituicao')
            );

            $recursoSubstituicao = Arr::get($data, 'recursoSubstituicao');
            if (!empty($recursoSubstituicao)) {
                $julgamentoRecursoSubstituicaoTO->setRecursoSubstituicao(
                    RecursoSubstituicaoTO::newInstance($recursoSubstituicao)
                );
            }

            $status = Arr::get($data, 'statusJulgamentoSubstituicao');
            if (!empty($status)) {
                $julgamentoRecursoSubstituicaoTO->setStatusJulgamentoSubstituicao(
                    StatusGenericoTO::newInstance($status)
                );
            }

            $usuario = Arr::get($data, 'usuario');
            if (!empty($usuario)) {
                $julgamentoRecursoSubstituicaoTO->setUsuario(UsuarioTO::newInstance($usuario));
            }

            $arquivos = Arr::get($data, 'arquivos');
            if (!empty($arquivos)) {
                $julgamentoRecursoSubstituicaoTO->setArquivos(array_map(function($arquivo){
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else if(!empty(Arr::get($data, 'nomeArquivo'))){
                $arquivo = ArquivoGenericoTO::newInstance([
                    'nome' => Arr::get($data, 'nomeArquivo'),
                    'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                ]);
                $julgamentoRecursoSubstituicaoTO->setArquivos([$arquivo]);
            }
        }

        return $julgamentoRecursoSubstituicaoTO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoSubstituicaoTO'.
     *
     * @param JulgamentoRecursoSubstituicao $julgamentoRecursoSubstituicao
     * @param bool $isResumo
     * @return JulgamentoRecursoSubstituicaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($julgamentoRecursoSubstituicao, $isResumo = false)
    {
        $julgamentoRecursoSubstituicaoTO = new JulgamentoRecursoSubstituicaoTO();

        if (!empty($julgamentoRecursoSubstituicao)) {
            $julgamentoRecursoSubstituicaoTO->setId($julgamentoRecursoSubstituicao->getId());
            $julgamentoRecursoSubstituicaoTO->setDescricao($julgamentoRecursoSubstituicao->getDescricao());
            $julgamentoRecursoSubstituicaoTO->setDataCadastro($julgamentoRecursoSubstituicao->getDataCadastro());

            if (!$isResumo) {
                $julgamentoRecursoSubstituicaoTO->setNomeArquivo($julgamentoRecursoSubstituicao->getNomeArquivo());
                $julgamentoRecursoSubstituicaoTO->setNomeArquivoFisico($julgamentoRecursoSubstituicao->getNomeArquivoFisico());

                $julgamentoRecursoSubstituicaoTO->setUsuario(UsuarioTO::newInstanceFromEntity(
                    $julgamentoRecursoSubstituicao->getUsuario()
                ));

                $julgamentoRecursoSubstituicaoTO->setStatusJulgamentoSubstituicao(StatusGenericoTO::newInstance([
                    'id' => $julgamentoRecursoSubstituicao->getStatusJulgamentoSubstituicao()->getId(),
                    'descricao' => $julgamentoRecursoSubstituicao->getStatusJulgamentoSubstituicao()->getDescricao()
                ]));

                $arquivos = [];
                if(!empty($julgamentoRecursoSubstituicaoTO->getNomeArquivo())) {
                    $arquivos[] = ArquivoGenericoTO::newInstance([
                        'nome' => $julgamentoRecursoSubstituicaoTO->getNomeArquivo(),
                        'nomeFisico' => $julgamentoRecursoSubstituicaoTO->getNomeArquivoFisico()
                    ]);
                }
                $julgamentoRecursoSubstituicaoTO->setArquivos($arquivos);
            }
        }

        return $julgamentoRecursoSubstituicaoTO;
    }

}
