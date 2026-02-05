<?php


namespace App\To;

use App\Entities\JulgamentoSubstituicao;
use App\Entities\PedidoImpugnacao;
use App\Entities\StatusJulgamentoSubstituicao;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Julgamento de Substituição
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoSubstituicaoTO
{

    /**
     * @var integer|null $id
     */
    private $id;

    /**
     * @var string|null $parecer
     */
    private $parecer;

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
     * @var PedidoSubstituicaoChapaTO
     */
    private $pedidoSubstituicaoChapa;

    /**
     * @var StatusJulgamentoSubstituicao
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
     * @var $idPedidoSubstituicaoChapa
     */
    private $idPedidoSubstituicaoChapa;

    /**
     * @var UsuarioTO $usuario
     */
    private $usuario;

    /**
     * @var bool
     */
    private $isPermitidoAlterarParecer;

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
    public function getParecer(): ?string
    {
        return $this->parecer;
    }

    /**
     * @param string|null $parecer
     */
    public function setParecer(?string $parecer): void
    {
        $this->parecer = $parecer;
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
     * @return PedidoSubstituicaoChapaTO
     */
    public function getPedidoSubstituicaoChapa(): ?PedidoSubstituicaoChapaTO
    {
        return $this->pedidoSubstituicaoChapa;
    }

    /**
     * @param PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapa
     */
    public function setPedidoSubstituicaoChapa(?PedidoSubstituicaoChapaTO $pedidoSubstituicaoChapa): void
    {
        $this->pedidoSubstituicaoChapa = $pedidoSubstituicaoChapa;
    }

    /**
     * @return StatusJulgamentoSubstituicao
     */
    public function getStatusJulgamentoSubstituicao(): ?StatusJulgamentoSubstituicao
    {
        return $this->statusJulgamentoSubstituicao;
    }

    /**
     * @param StatusJulgamentoSubstituicao $statusJulgamentoSubstituicao
     */
    public function setStatusJulgamentoSubstituicao(?StatusJulgamentoSubstituicao $statusJulgamentoSubstituicao): void
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
    public function getIdPedidoSubstituicaoChapa()
    {
        return $this->idPedidoSubstituicaoChapa;
    }

    /**
     * @param mixed $idPedidoSubstituicaoChapa
     */
    public function setIdPedidoSubstituicaoChapa($idPedidoSubstituicaoChapa): void
    {
        $this->idPedidoSubstituicaoChapa = $idPedidoSubstituicaoChapa;
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
     * @return bool
     */
    public function isPermitidoAlterarParecer(): ?bool
    {
        return $this->isPermitidoAlterarParecer;
    }

    /**
     * @param bool $isPermitidoAlterarParecer
     */
    public function setIsPermitidoAlterarParecer(?bool $isPermitidoAlterarParecer): void
    {
        $this->isPermitidoAlterarParecer = $isPermitidoAlterarParecer;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoSubstituicaoTO'.
     *
     * @param null $data
     * @return JulgamentoSubstituicaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoSubstituicaoTO = new JulgamentoSubstituicaoTO();

        if ($data != null) {
            $julgamentoSubstituicaoTO->setId(Arr::get($data, 'id'));
            $julgamentoSubstituicaoTO->setParecer(Arr::get($data, 'parecer'));
            $julgamentoSubstituicaoTO->setArquivo(Arr::get($data, 'arquivo'));
            $julgamentoSubstituicaoTO->setTamanho(Arr::get($data, 'tamanho'));
            $julgamentoSubstituicaoTO->setNomeArquivo(Arr::get($data, 'nomeArquivo'));
            $julgamentoSubstituicaoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $julgamentoSubstituicaoTO->setNomeArquivoFisico(Arr::get($data, 'nomeArquivoFisico'));
            $julgamentoSubstituicaoTO->setIdStatusJulgamentoSubstituicao(Arr::get($data,
                'idStatusJulgamentoSubstituicao'));
            $julgamentoSubstituicaoTO->setIdPedidoSubstituicaoChapa(Arr::get($data, 'idPedidoSubstituicaoChapa'));
            $julgamentoSubstituicaoTO->setIsPermitidoAlterarParecer(Arr::get($data, 'isPermitidoAlterarParecer'));

            $pedidoSubstituicao = Arr::get($data, 'pedidoSubstituicaoChapa');
            if (!empty($pedidoSubstituicao)) {
                $julgamentoSubstituicaoTO->setPedidoSubstituicaoChapa(
                    PedidoSubstituicaoChapaTO::newInstance($pedidoSubstituicao)
                );
            }

            $status = Arr::get($data, 'statusJulgamentoSubstituicao');
            if (!empty($status)) {
                $julgamentoSubstituicaoTO->setStatusJulgamentoSubstituicao(StatusJulgamentoSubstituicao::newInstance($status));
            }

            $usuario = Arr::get($data, 'usuario');
            if (!empty($usuario)) {
                $julgamentoSubstituicaoTO->setUsuario(UsuarioTO::newInstance($usuario));
            }
        }

        return $julgamentoSubstituicaoTO;
    }

    /**
     * Retorna uma nova instância de 'PedidoImpugnacaoTO'.
     *
     * @param JulgamentoSubstituicao $julgamentoSubstituicao
     * @return JulgamentoSubstituicaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($julgamentoSubstituicao, $isResumo = false)
    {
        $julgamentoSubstituicaoTO = new JulgamentoSubstituicaoTO();

        if (!empty($julgamentoSubstituicao)) {
            $julgamentoSubstituicaoTO->setId($julgamentoSubstituicao->getId());
            $julgamentoSubstituicaoTO->setParecer($julgamentoSubstituicao->getParecer());
            $julgamentoSubstituicaoTO->setDataCadastro($julgamentoSubstituicao->getDataCadastro());

            if (!$isResumo) {
                $julgamentoSubstituicaoTO->setNomeArquivo($julgamentoSubstituicao->getNomeArquivo());
                $julgamentoSubstituicaoTO->setNomeArquivoFisico($julgamentoSubstituicao->getNomeArquivoFisico());

                $julgamentoSubstituicaoTO->setUsuario(UsuarioTO::newInstanceFromEntity(
                    $julgamentoSubstituicao->getUsuario()
                ));

                $julgamentoSubstituicaoTO->setStatusJulgamentoSubstituicao(StatusJulgamentoSubstituicao::newInstance([
                    'id' => $julgamentoSubstituicao->getStatusJulgamentoSubstituicao()->getId(),
                    'descricao' => $julgamentoSubstituicao->getStatusJulgamentoSubstituicao()->getDescricao()
                ]));
            }
        }

        return $julgamentoSubstituicaoTO;
    }

}
