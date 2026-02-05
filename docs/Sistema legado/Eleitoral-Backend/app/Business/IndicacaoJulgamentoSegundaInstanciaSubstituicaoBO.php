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

use App\Entities\IndicacaoJulgamentoSegundaInstanciaRecurso;
use App\Entities\IndicacaoJulgamentoSegundaInstanciaSubstituicao;
use App\Entities\JulgamentoSegundaInstanciaRecurso;
use App\Entities\JulgamentoSegundaInstanciaSubstituicao;
use App\Repository\IndicacaoJulgamentoFinalRepository;
use App\Repository\IndicacaoJulgamentoSegundaInstanciaRecursoRepository;
use App\Repository\IndicacaoJulgamentoSegundaInstanciaSubstituicaoRepository;
use App\To\IndicacaoJulgamentoFinalTO;
use App\To\IndicacaoJulgamentoSegundaInstanciaRecursoTO;
use App\To\IndicacaoJulgamentoSegundaInstanciaSubstituicaoTO;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'IndicacaoJulgamentoSegundaInstanciaSubstituicaoBO'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class IndicacaoJulgamentoSegundaInstanciaSubstituicaoBO extends AbstractBO
{

    /**
     * @var IndicacaoJulgamentoSegundaInstanciaSubstituicaoRepository
     */
    private $indicacaoJulgamentoSegundaInstanciaSubstituicaoRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {}

    /**
     * Salva o julgamento final da chapa da eleição
     *
     * @param IndicacaoJulgamentoSegundaInstanciaSubstituicaoTO[] $indicacoesTO
     * @param JulgamentoSegundaInstanciaSubstituicao $julgamentoSegundaInstanciaSubstituicao
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarIndicacoes($indicacoesTO, JulgamentoSegundaInstanciaSubstituicao $julgamentoSegundaInstanciaSubstituicao)
    {
        $indicacoes = [];
        foreach ($indicacoesTO as $indicacaoTO) {

            $membro = !empty($indicacaoTO->getIdMembroChapa()) ? ['id' => $indicacaoTO->getIdMembroChapa()] : null;

            array_push($indicacoes, IndicacaoJulgamentoSegundaInstanciaSubstituicao::newInstance([
                'numeroOrdem' => $indicacaoTO->getNumeroOrdem(),
                'tipoParticipacaoChapa' => ['id' => $indicacaoTO->getIdTipoParicipacaoChapa()],
                'membroChapa' => $membro,
                'julgamentoSegundaInstanciaSubstituicao' => ['id' => $julgamentoSegundaInstanciaSubstituicao->getId()]
            ]));
        }

        $indicacoesSalvas = $this->getIndicacaoJulgamentoSegundaInstanciaSubstituicaoRepository()->persistEmLote($indicacoes);

        $julgamentoSegundaInstanciaSubstituicao->setIndicacoes($indicacoesSalvas);
    }

    /**
     * Retorna uma nova instância de 'IndicacaoJulgamentoSegundaInstanciaSubstituicao'.
     *
     * @return IndicacaoJulgamentoFinalRepository|\Doctrine\ORM\EntityRepository
     */
    private function getIndicacaoJulgamentoSegundaInstanciaSubstituicaoRepository()
    {
        if (empty($this->indicacaoJulgamentoSegundaInstanciaSubstituicaoRepository)) {
            $this->indicacaoJulgamentoSegundaInstanciaSubstituicaoRepository = $this->getRepository(IndicacaoJulgamentoSegundaInstanciaSubstituicao::class);
        }

        return $this->indicacaoJulgamentoSegundaInstanciaSubstituicaoRepository;
    }
}




