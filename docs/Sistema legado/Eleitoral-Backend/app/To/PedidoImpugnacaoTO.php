<?php


namespace App\To;

use App\Config\Constants;
use App\Entities\PedidoImpugnacao;
use App\Entities\StatusPedidoImpugnacao;
use App\Util\Utils;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a visualizaçao do pedido de impugnaçao
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class PedidoImpugnacaoTO
{

    /**
     * @var integer|null $id
     */
    private $id;

    /**
     * @var string $descricao
     */
    private $descricao;

    /**
     * @var int $idCauUf
     */
    private $idCauUf;

    /**
     * @var string $numeroProtocolo
     */
    private $numeroProtocolo;

    /**
     * @var CandidadoPedidoImpugnacaoTO $informacoesCandidato
     */
    private $informacoesCandidato;

    /**
     * @var ImpugnantePedidoImpugnacaoTO $informacoesImpugnante
     */
    private $informacoesImpugnante;

    /**
     * @var ArquivoPedidoImpugnacaoTO[] $arquivos
     */
    private $arquivos;

    /**
     * @var DeclaracaoTO[] $declaracoes
     */
    private $declaracoes;

    /**
     * @var bool
     */
    private $podeCadastrarDefesa;

    /**
     * @var bool
     */
    private $isIES;

    private $isIniciadoAtividadeRecurso;

    /**
     * @var bool
     */
    private $isFinalizadoAtividadeRecurso;

    /**
     * @var bool
     */
    private $isIniciadoAtividadeJulgamentoRecurso;

    /**
     * @var bool
     */
    private $isFinalizadoAtividadeJulgamentoRecurso;

    /**
     * @var bool
     */
    private $isIniciadoAtividadeSubstituicao;

    /**
     * @var bool
     */
    private $isFinalizadoAtividadeSubstituicao;

    /**
     * @var bool
     */
    private $isIniciadoAtividadeContrarrazao;

    /**
     * @var bool
     */
    private $isFinalizadoAtividadeContrarrazao;

    /**
     * @var bool|null
     */
    private $isIniciadoAtividadeDefesa;

    /**
     * @var bool|null
     */
    private $isFinalizadoAtividadeDefesa;

    /**
     * @var bool
     */
    private $isIniciadoAtividadeRecursoJulgamentoFinal;

    /**
     * @var bool
     */
    private $isFinalizadoAtividadeRecursoJulgamentoFinal;

    /**
     * @var StatusPedidoImpugnacao|null
     */
    private $statusPedidoImpugnacao;

    /**
     * @var bool
     */
    private $isPermissaoJulgamento;

    /**
     * @return CandidadoPedidoImpugnacaoTO
     */
    public function getInformacoesCandidato(): ?CandidadoPedidoImpugnacaoTO
    {
        return $this->informacoesCandidato;
    }

    /**
     * @param CandidadoPedidoImpugnacaoTO $informacoesCandidato
     */
    public function setInformacoesCandidato(?CandidadoPedidoImpugnacaoTO $informacoesCandidato): void
    {
        $this->informacoesCandidato = $informacoesCandidato;
    }

    /**
     * @return ImpugnantePedidoImpugnacaoTO
     */
    public function getInformacoesImpugnante(): ?ImpugnantePedidoImpugnacaoTO
    {
        return $this->informacoesImpugnante;
    }

    /**
     * @param ImpugnantePedidoImpugnacaoTO $informacoesImpugnante
     */
    public function setInformacoesImpugnante(?ImpugnantePedidoImpugnacaoTO $informacoesImpugnante): void
    {
        $this->informacoesImpugnante = $informacoesImpugnante;
    }

    /**
     * @return string
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return string
     */
    public function getNumeroProtocolo(): ?string
    {
        return $this->numeroProtocolo;
    }

    /**
     * @param string $numeroProtocolo
     */
    public function setNumeroProtocolo(?string $numeroProtocolo): void
    {
        if (!empty($numeroProtocolo)) {
            $this->numeroProtocolo = str_pad($numeroProtocolo, 5, '0', STR_PAD_LEFT);
        }
    }

    /**
     * @return ArquivoPedidoImpugnacaoTO[]
     */
    public function getArquivos(): ?array
    {
        return $this->arquivos;
    }

    /**
     * @param ArquivoPedidoImpugnacaoTO[] $arquivos
     */
    public function setArquivos(?array $arquivos): void
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return DeclaracaoTO[]
     */
    public function getDeclaracoes(): ?array
    {
        return $this->declaracoes;
    }

    /**
     * @param DeclaracaoTO[] $declaracoes
     */
    public function setDeclaracoes(?array $declaracoes): void
    {
        $this->declaracoes = $declaracoes;
    }

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
     * @return int
     */
    public function getIdCauUf(): ?int
    {
        return $this->idCauUf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf(?int $idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return bool
     */
    public function isPodeCadastrarDefesa(): ?bool
    {
        return $this->podeCadastrarDefesa;
    }

    /**
     * @param bool $podeCadastrarDefesa
     */
    public function setPodeCadastrarDefesa(?bool $podeCadastrarDefesa): void
    {
        $this->podeCadastrarDefesa = $podeCadastrarDefesa;
    }

    /**
     * @return bool
     */
    public function isIES(): ?bool
    {
        return $this->isIES;
    }

    /**
     * @param bool $isIES
     */
    public function setIsIES(?bool $isIES): void
    {
        $this->isIES = $isIES;
    }

    /**
     * @return mixed
     */
    public function getIsIniciadoAtividadeRecurso()
    {
        return $this->isIniciadoAtividadeRecurso;
    }

    /**
     * @param mixed $isIniciadoAtividadeRecurso
     */
    public function setIsIniciadoAtividadeRecurso($isIniciadoAtividadeRecurso): void
    {
        $this->isIniciadoAtividadeRecurso = $isIniciadoAtividadeRecurso;
    }

    /**
     * @return bool
     */
    public function isFinalizadoAtividadeRecurso()
    {
        return $this->isFinalizadoAtividadeRecurso;
    }

    /**
     * @param bool $isFinalizadoAtividadeRecurso
     */
    public function setIsFinalizadoAtividadeRecurso($isFinalizadoAtividadeRecurso): void
    {
        $this->isFinalizadoAtividadeRecurso = $isFinalizadoAtividadeRecurso;
    }

    /**
     * @return bool
     */
    public function isIniciadoAtividadeJulgamentoRecurso()
    {
        return $this->isIniciadoAtividadeJulgamentoRecurso;
    }

    /**
     * @param bool $isIniciadoAtividadeJulgamentoRecurso
     */
    public function setIsIniciadoAtividadeJulgamentoRecurso($isIniciadoAtividadeJulgamentoRecurso): void
    {
        $this->isIniciadoAtividadeJulgamentoRecurso = $isIniciadoAtividadeJulgamentoRecurso;
    }

    /**
     * @return StatusPedidoImpugnacao|null
     */
    public function getStatusPedidoImpugnacao(): ?StatusPedidoImpugnacao
    {
        return $this->statusPedidoImpugnacao;
    }

    /**
     * @return bool
     */
    public function isFinalizadoAtividadeJulgamentoRecurso()
    {
        return $this->isFinalizadoAtividadeJulgamentoRecurso;
    }

    /**
     * @param bool $isFinalizadoAtividadeJulgamentoRecurso
     */
    public function setIsFinalizadoAtividadeJulgamentoRecurso($isFinalizadoAtividadeJulgamentoRecurso): void
    {
        $this->isFinalizadoAtividadeJulgamentoRecurso = $isFinalizadoAtividadeJulgamentoRecurso;
    }

    /**
     * @return bool
     */
    public function isIniciadoAtividadeSubstituicao(): ?bool
    {
        return $this->isIniciadoAtividadeSubstituicao;
    }

    /**
     * @param bool $isIniciadoAtividadeSubstituicao
     */
    public function setIsIniciadoAtividadeSubstituicao(?bool $isIniciadoAtividadeSubstituicao): void
    {
        $this->isIniciadoAtividadeSubstituicao = $isIniciadoAtividadeSubstituicao;
    }

    /**
     * @return bool
     */
    public function isFinalizadoAtividadeSubstituicao(): ?bool
    {
        return $this->isFinalizadoAtividadeSubstituicao;
    }

    /**
     * @param bool $isFinalizadoAtividadeSubstituicao
     */
    public function setIsFinalizadoAtividadeSubstituicao(?bool $isFinalizadoAtividadeSubstituicao): void
    {
        $this->isFinalizadoAtividadeSubstituicao = $isFinalizadoAtividadeSubstituicao;
    }

    /**
     * @param StatusPedidoImpugnacao|null $statusPedidoImpugnacao
     */
    public function setStatusPedidoImpugnacao(?StatusPedidoImpugnacao $statusPedidoImpugnacao): void
    {
        $this->statusPedidoImpugnacao = $statusPedidoImpugnacao;
    }

    /**
     * @return bool
     */
    public function isPermissaoJulgamento(): ?bool
    {
        return $this->isPermissaoJulgamento;
    }

    /**
     * @param bool $isPermissaoJulgamento
     */
    public function setIsPermissaoJulgamento(?bool $isPermissaoJulgamento): void
    {
        $this->isPermissaoJulgamento = $isPermissaoJulgamento;
    }

    /**
     * @return bool
     */
    public function isIniciadoAtividadeContrarrazao(): bool
    {
        return $this->isIniciadoAtividadeContrarrazao;
    }

    /**
     * @param bool $isIniciadoAtividadeContrarrazao
     */
    public function setIsIniciadoAtividadeContrarrazao(bool $isIniciadoAtividadeContrarrazao): void
    {
        $this->isIniciadoAtividadeContrarrazao = $isIniciadoAtividadeContrarrazao;
    }

    /**
     * @return bool
     */
    public function isFinalizadoAtividadeContrarrazao(): bool
    {
        return $this->isFinalizadoAtividadeContrarrazao;
    }

    /**
     * @param bool $isFinalizadoAtividadeContrarrazao
     */
    public function setIsFinalizadoAtividadeContrarrazao(bool $isFinalizadoAtividadeContrarrazao): void
    {
        $this->isFinalizadoAtividadeContrarrazao = $isFinalizadoAtividadeContrarrazao;
    }

    /**
     * @return bool|null
     */
    public function getIsIniciadoAtividadeDefesa(): ?bool
    {
        return $this->isIniciadoAtividadeDefesa;
    }

    /**
     * @param bool|null $isIniciadoAtividadeDefesa
     */
    public function setIsIniciadoAtividadeDefesa(?bool $isIniciadoAtividadeDefesa): void
    {
        $this->isIniciadoAtividadeDefesa = $isIniciadoAtividadeDefesa;
    }

    /**
     * @return bool|null
     */
    public function getIsFinalizadoAtividadeDefesa(): ?bool
    {
        return $this->isFinalizadoAtividadeDefesa;
    }

    /**
     * @param bool|null $isFinalizadoAtividadeDefesa
     */
    public function setIsFinalizadoAtividadeDefesa(?bool $isFinalizadoAtividadeDefesa): void
    {
        $this->isFinalizadoAtividadeDefesa = $isFinalizadoAtividadeDefesa;
    }

    /**
     * Retorna uma nova instância de 'PedidoImpugnacaoTO'.
     *
     * @param null $data
     * @return PedidoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $pedidoImpugnacaoTO = new PedidoImpugnacaoTO();

        if ($data != null) {
            $pedidoImpugnacaoTO->setId(Arr::get($data, 'id'));
            $pedidoImpugnacaoTO->setDescricao(Arr::get($data, 'descricao'));
            $pedidoImpugnacaoTO->setNumeroProtocolo(Arr::get($data, 'numeroProtocolo'));
            $pedidoImpugnacaoTO->setInformacoesCandidato(CandidadoPedidoImpugnacaoTO::newInstance($data));
            $pedidoImpugnacaoTO->setInformacoesImpugnante(ImpugnantePedidoImpugnacaoTO::newInstance($data));
            $pedidoImpugnacaoTO->setIdCauUf(Arr::get($data, 'membroChapa.chapaEleicao.idCauUf'));
            $pedidoImpugnacaoTO->setPodeCadastrarDefesa(Arr::get($data, 'podeCadastrarDefesa'));

            $idIES = Arr::get($data, 'membroChapa.chapaEleicao.tipoCandidatura.id');
            $pedidoImpugnacaoTO->setIsIES($idIES == Constants::TIPO_CANDIDATURA_IES);

            //Setando os arquivos enviados pela declaraçao
            /** @var ArquivoPedidoImpugnacaoTO[] $arquivosTO */
            $arquivosTO = [];
            $arquivosPedido = Arr::get($data, 'arquivosPedidoImpugnacao');
            if (!empty($arquivosPedido)) {
                foreach ($arquivosPedido as $arquivoPedido) {
                    $arquivosTO[] = ArquivoPedidoImpugnacaoTO::newInstance($arquivoPedido);
                }
            }
            $pedidoImpugnacaoTO->setArquivos($arquivosTO);

            //Setando as declaraçoes respondidas no pedido
            $declaracaoesTO = [];
            $declaracoesPedido = Arr::get($data, 'respostasDeclaracaoPedidoImpugnacao');
            if (!empty($declaracoesPedido)) {
                foreach ($declaracoesPedido as $declaracaoPedido) {
                    $declaracaoesTO[] = DeclaracaoTO::newInstance(Arr::get($declaracaoPedido, 'respostaDeclaracao'));
                }
            }
            $pedidoImpugnacaoTO->setDeclaracoes($declaracaoesTO);

            $statusPedidoImpugnacao = Arr::get($data, 'statusPedidoImpugnacao');
            if (!empty($statusPedidoImpugnacao)) {
                $pedidoImpugnacaoTO->setStatusPedidoImpugnacao(StatusPedidoImpugnacao::newInstance($statusPedidoImpugnacao));
            }

        }

        return $pedidoImpugnacaoTO;
    }

    /**
     * Retorna uma nova instância de 'PedidoImpugnacaoTO'.
     *
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @return PedidoImpugnacaoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($pedidoImpugnacao, $isResumo = true)
    {
        $pedidoImpugnacaoTO = new PedidoImpugnacaoTO();

        if (!empty($pedidoImpugnacao)) {
            $pedidoImpugnacaoTO->setId($pedidoImpugnacao->getId());
            $pedidoImpugnacaoTO->setNumeroProtocolo($pedidoImpugnacao->getNumeroProtocolo());

            if (!$isResumo) {
                $pedidoImpugnacaoTO->setInformacoesCandidato(CandidadoPedidoImpugnacaoTO::newInstanceFromEntity(
                    $pedidoImpugnacao->getMembroChapa()
                ));
                $pedidoImpugnacaoTO->setInformacoesImpugnante(ImpugnantePedidoImpugnacaoTO::newInstance([
                    'profissional' => [
                        'nome' => $pedidoImpugnacao->getProfissional()->getNome(),
                        'registroNacional' => $pedidoImpugnacao->getProfissional()->getRegistroNacional()
                    ]
                ]));
            }
        }

        return $pedidoImpugnacaoTO;
    }

    public function iniciarFlags()
    {
        $this->setIsIniciadoAtividadeRecurso(false);
        $this->setIsFinalizadoAtividadeRecurso(false);
        $this->setIsIniciadoAtividadeSubstituicao(false);
        $this->setIsFinalizadoAtividadeSubstituicao(false);
        $this->setIsIniciadoAtividadeJulgamentoRecurso(false);
        $this->setIsFinalizadoAtividadeJulgamentoRecurso(false);
        $this->setIsIniciadoAtividadeContrarrazao(false);
        $this->setIsFinalizadoAtividadeContrarrazao(false);
    }

}
