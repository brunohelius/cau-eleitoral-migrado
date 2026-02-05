<?php
/*
 * PedidoImpugnacaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoPedidoImpugnacao;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ChapaEleicao;
use App\Entities\Declaracao;
use App\Entities\DeclaracaoAtividade;
use App\Entities\ItemDeclaracao;
use App\Entities\ItemRespostaDeclaracao;
use App\Entities\MembroChapa;
use App\Entities\PedidoImpugnacao;
use App\Entities\PedidoSubstituicaoChapa;
use App\Entities\RespostaDeclaracao;
use App\Entities\RespostaDeclaracaoPedidoImpugnacao;
use App\Entities\StatusPedidoImpugnacao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailPedidoImpugnacaoJob;
use App\Jobs\EnviarPedidoSubstituicaoChapaCadastradaJob;
use App\Repository\ArquivoPedidoImpugnacaoRepository;
use App\Repository\AtividadeSecundariaCalendarioRepository;
use App\Repository\ItemRespostaDeclaracaoRepository;
use App\Repository\MembroChapaRepository;
use App\Repository\PedidoImpugnacaoRepository;
use App\Repository\RespostaDeclaracaoPedidoImpugnacaoRepository;
use App\Repository\RespostaDeclaracaoRepository;
use App\Service\ArquivoService;
use App\To\EleicaoTO;
use App\To\ItemRespostaDeclaracaoTO;
use App\To\PedidoSubstituicaoChapaTO;
use App\To\RespostaDeclaracaoTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Predis\Response\Status;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'RespostaDeclaracao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class RespostaDeclaracaoBO extends AbstractBO
{

    /**
     * @var RespostaDeclaracaoRepository
     */
    private $respostaDeclaracaoRepository;

    /**
     * @var ItemRespostaDeclaracaoRepository
     */
    private $itemRespostaDeclaracaoRepository;

    /**
     * @var RespostaDeclaracaoPedidoImpugnacaoRepository
     */
    private $respostaDeclaracaoPedidoImpugnacaoRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salva os arquivos do pedido de impugnação
     *
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @param RespostaDeclaracaoTO[] $respostasDeclaracaoTO
     * @param DeclaracaoAtividade[] $declaracoesAtividade
     * @return mixed
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarParaPedidoImpugnacao($pedidoImpugnacao, $respostasDeclaracaoTO, $declaracoesAtividade)
    {
        if (!empty($pedidoImpugnacao)) {
            /** @var RespostaDeclaracaoTO $respostaDeclaracaoTO */
            foreach ($respostasDeclaracaoTO as $respostaDeclaracaoTO) {
                $declaracao = $this->recuperarDeclaracaoDeDeclaracoesAtividades(
                    $declaracoesAtividade, $respostaDeclaracaoTO->getIdDeclaracao()
                );

                $respostaDeclaracao = $this->salvarRespostaDeclaracao($respostaDeclaracaoTO, $declaracao);

                $respostaDeclaracaoPedidoImpugnacao = RespostaDeclaracaoPedidoImpugnacao::newInstance();
                $respostaDeclaracaoPedidoImpugnacao->setRespostaDeclaracao($respostaDeclaracao);
                $respostaDeclaracaoPedidoImpugnacao->setPedidoImpugnacao($pedidoImpugnacao);
                $this->getRespostaDeclaracaoPedidoImpugnacaoRepository()->persist(
                    $respostaDeclaracaoPedidoImpugnacao
                );
            }
        }
    }

    /**
     * Método que salva uma resposta de declaração e os itens de resposta da declaração
     *
     * @param RespostaDeclaracaoTO $respostaDeclaracaoTO
     * @param Declaracao $declaracao
     * @return RespostaDeclaracao
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarRespostaDeclaracao($respostaDeclaracaoTO, $declaracao)
    {
        $respostaDeclaracao = RespostaDeclaracao::newInstance([
            'titulo' => $declaracao->getTitulo(),
            'textoInicial' => $declaracao->getTextoInicial(),
            'tipoResposta' => $declaracao->getTipoResposta()
        ]);

        /** @var RespostaDeclaracao $respostaDeclaracao */
        $respostaDeclaracao = $this->getRespostaDeclaracaoRepository()->persist($respostaDeclaracao);

        /** @var ItemRespostaDeclaracaoTO $itemRespostaDeclaracaoTO */
        foreach ($respostaDeclaracaoTO->getItensRespostaDeclaracao() as $itemRespostaDeclaracaoTO) {
            $itemDeclaracao = $this->recuperarItemDeclaracao(
                $declaracao, $itemRespostaDeclaracaoTO->getIdItemDeclaracao()
            );

            $itemRespostaDeclaracao = ItemRespostaDeclaracao::newInstance([
                'descricao' => $itemDeclaracao->getDescricao(),
                'sequencial' => $itemDeclaracao->getSequencial(),
                'situacaoResposta' => $itemRespostaDeclaracaoTO->getSituacaoResposta()
            ]);
            $itemRespostaDeclaracao->setRespostaDeclaracao($respostaDeclaracao);
            $this->getItemRespostaDeclaracaoRepository()->persist($itemRespostaDeclaracao);
        }

        return $respostaDeclaracao;
    }

    /**
     * Método auxiliar que valida as respostas declaração seção 'Fundamentação'
     *
     * @param RespostaDeclaracaoTO[] $respostasDeclaracaoTO
     * @param DeclaracaoAtividade[] $declaracoesAtividade
     * @throws NegocioException
     */
    public function validarRespostasDeclaracao($respostasDeclaracaoTO, $declaracoesAtividade)
    {
        $quantidadeItensSelecionado = 0;
        if (!empty($respostasDeclaracaoTO) && count($respostasDeclaracaoTO) == 3) {

            /**  @var RespostaDeclaracaoTO $respostaDeclaracaoTO ; */
            foreach ($respostasDeclaracaoTO as $respostaDeclaracaoTO) {
                $this->validarRespostaDeclaracao(
                    $declaracoesAtividade,
                    $respostaDeclaracaoTO,
                    $quantidadeItensSelecionado
                );
            }
        }

        if ($quantidadeItensSelecionado == 0) {
            throw new NegocioException(Message::MSG_SELECIONE_NO_MINIMO_UM_ITEM_DECLARACOES);
        }
    }

    /**
     * Método auxiliar que valida uma resposta declaração e inclementa o valor de $quantidadeItensSelecionado
     * para cada item selecionado
     *
     * @param DeclaracaoAtividade[] $declaracoesAtiv
     * @param RespostaDeclaracaoTO $respostaDeclaracaoTO
     * @param int $quantidadeItensSelecionado
     * @throws NegocioException
     */
    private function validarRespostaDeclaracao(
        $declaracoesAtiv,
        $respostaDeclaracaoTO,
        &$quantidadeItensSelecionado
    ) {
        $declaracao = $this->recuperarDeclaracaoDeDeclaracoesAtividades(
            $declaracoesAtiv,
            $respostaDeclaracaoTO->getIdDeclaracao()
        );

        $idsItensDeclaracao = [];
        /** @var ItemDeclaracao $item */
        foreach ($declaracao->getItensDeclaracao() as $item) {
            $idsItensDeclaracao[] = $item->getId();
        }

        $idsItensRespostaDeclaracao = [];

        /** @var ItemRespostaDeclaracaoTO $itemRespostaDeclaracao */
        foreach ($respostaDeclaracaoTO->getItensRespostaDeclaracao() as $itemRespostaDeclaracao) {
            if (json_decode($itemRespostaDeclaracao->getSituacaoResposta())) {
                $quantidadeItensSelecionado++;
            }

            $idsItensRespostaDeclaracao[] = $itemRespostaDeclaracao->getIdItemDeclaracao();
        }

        $isQtdItensIgual = count($idsItensDeclaracao) == count($idsItensRespostaDeclaracao);
        $isTodosItensDeclaracaoEnviados = empty(array_diff($idsItensDeclaracao, $idsItensRespostaDeclaracao));

        if (!$isQtdItensIgual || !$isTodosItensDeclaracaoEnviados) {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }
    }

    /**
     * Método auxiliar para recuperar uma declaração de uma lista de Declarações Atividade
     * @param DeclaracaoAtividade[] $declaracoesAtiv
     * @param $idDeclaracao
     * @return Declaracao
     */
    private function recuperarDeclaracaoDeDeclaracoesAtividades($declaracoesAtiv, $idDeclaracao)
    {
        $declaracao = null;

        foreach ($declaracoesAtiv as $declaracaoAtiv) {
            if ($declaracaoAtiv->getDeclaracao()->getId() == $idDeclaracao) {
                $declaracao = $declaracaoAtiv->getDeclaracao();
            }
        }

        return $declaracao;
    }

    /**
     * Método auxiliar para recuperar um item da declaração a partir de um ID
     *
     * @param Declaracao $declaracao
     * @param int $idItemDeclaracao
     * @return ItemDeclaracao|null
     */
    private function recuperarItemDeclaracao($declaracao, $idItemDeclaracao)
    {
        $itemDeclaracao = null;

        /** @var ItemDeclaracao $item */
        foreach ($declaracao->getItensDeclaracao() as $item) {
            if ($item->getId() == $idItemDeclaracao) {
                $itemDeclaracao = $item;
            }
        }

        return $itemDeclaracao;
    }

    /**
     * Retorna uma nova instância de 'RespostaDeclaracaoRepository'.
     *
     * @return RespostaDeclaracaoRepository|mixed
     */
    private function getRespostaDeclaracaoRepository()
    {
        if (empty($this->respostaDeclaracaoRepository)) {
            $this->respostaDeclaracaoRepository = $this->getRepository(RespostaDeclaracao::class);
        }

        return $this->respostaDeclaracaoRepository;
    }

    /**
     * Retorna uma nova instância de 'ItemRespostaDeclaracaoRepository'.
     *
     * @return ItemRespostaDeclaracaoRepository|mixed
     */
    private function getItemRespostaDeclaracaoRepository()
    {
        if (empty($this->itemRespostaDeclaracaoRepository)) {
            $this->itemRespostaDeclaracaoRepository = $this->getRepository(ItemRespostaDeclaracao::class);
        }

        return $this->itemRespostaDeclaracaoRepository;
    }

    /**
     * Retorna uma nova instância de 'RespostaDeclaracaoPedidoImpugnacaoRepository'.
     *
     * @return RespostaDeclaracaoPedidoImpugnacaoRepository|mixed
     */
    private function getRespostaDeclaracaoPedidoImpugnacaoRepository()
    {
        if (empty($this->respostaDeclaracaoPedidoImpugnacaoRepository)) {
            $this->respostaDeclaracaoPedidoImpugnacaoRepository = $this->getRepository(
                RespostaDeclaracaoPedidoImpugnacao::class
            );
        }

        return $this->respostaDeclaracaoPedidoImpugnacaoRepository;
    }
}




