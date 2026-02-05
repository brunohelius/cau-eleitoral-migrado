<?php
/*
 * JulgamentoFinalBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\IndicacaoJulgamentoRecursoPedidoSubstituicao;
use App\Entities\JulgamentoRecursoPedidoSubstituicao;
use App\Repository\IndicacaoJulgamentoRecursoPedidoSubstituicaoRepository;
use App\To\IndicacaoJulgamentoRecursoPedidoSubstituicaoTO;
use App\To\IndicacaoJulgamentoSegundaInstanciaSubstituicaoTO;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'IndicacaoJulgamentoRecursoPedidoSubstituicaoBO'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class IndicacaoJulgamentoRecursoPedidoSubstituicaoBO extends AbstractBO
{

    /**
     * @var IndicacaoJulgamentoRecursoPedidoSubstituicaoRepository
     */
    private $indicacaoJulgamentoRecursoPedidoSubstituicaoRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {}

    /**
     * Salva o julgamento final da chapa da eleição
     *
     * @param IndicacaoJulgamentoRecursoPedidoSubstituicaoTO[] $indicacoesTO
     * @param JulgamentoRecursoPedidoSubstituicao $julgamentoRecursoPedidoSubstituicao
     * @return void
     * @throws \Exception
     */
    public function salvarIndicacoes($indicacoesTO, JulgamentoRecursoPedidoSubstituicao $julgamentoRecursoPedidoSubstituicao)
    {
        $indicacoes = [];
        foreach ($indicacoesTO as $indicacaoTO) {

            $membro = !empty($indicacaoTO->getIdMembroChapa()) ? ['id' => $indicacaoTO->getIdMembroChapa()] : null;

            array_push($indicacoes, IndicacaoJulgamentoRecursoPedidoSubstituicao::newInstance([
                'numeroOrdem' => $indicacaoTO->getNumeroOrdem(),
                'tipoParticipacaoChapa' => ['id' => $indicacaoTO->getIdTipoParicipacaoChapa()],
                'membroChapa' => $membro,
                'julgamentoRecursoPedidoSubstituicao' => ['id' => $julgamentoRecursoPedidoSubstituicao->getId()]
            ]));
        }

        $indicacoesSalvas = $this->getIndicacaoJulgamentoRecursoPedidoSubstituicaoRepository()->persistEmLote($indicacoes);

        $julgamentoRecursoPedidoSubstituicao->setIndicacoes($indicacoesSalvas);
    }

    /**
     * Retorna uma nova instância de 'IndicacaoJulgamentoRecursoPedidoSubstituicao'.
     *
     * @return IndicacaoJulgamentoRecursoPedidoSubstituicaoRepository|\Doctrine\ORM\EntityRepository
     */
    private function getIndicacaoJulgamentoRecursoPedidoSubstituicaoRepository()
    {
        if (empty($this->indicacaoJulgamentoRecursoPedidoSubstituicaoRepository)) {
            $this->indicacaoJulgamentoRecursoPedidoSubstituicaoRepository = $this->getRepository(IndicacaoJulgamentoRecursoPedidoSubstituicao::class);
        }

        return $this->indicacaoJulgamentoRecursoPedidoSubstituicaoRepository;
    }
}




