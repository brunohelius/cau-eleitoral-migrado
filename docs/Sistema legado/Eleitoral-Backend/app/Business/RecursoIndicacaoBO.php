<?php
/*
 * RecursoIndicacaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\RecursoIndicacao;
use App\Entities\RecursoJulgamentoFinal;
use App\Repository\RecursoIndicacaoRepository;
use App\To\RecursoIndicacaoTO;
use App\To\RecursoJulgamentoFinalTO;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'IndicacaoJulgamentoFinal'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class RecursoIndicacaoBO extends AbstractBO
{

    /**
     * @var RecursoIndicacaoRepository
     */
    private $recursoIndicacaoRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salva o julgamento final da chapa da eleição
     *
     * @param RecursoJulgamentoFinal $recursoJulgamentoFinal
     * @param RecursoJulgamentoFinalTO $recursoJulgamentoFinalTO
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarIndicacoes($recursoJulgamentoFinal, $recursoJulgamentoFinalTO)
    {
        if (!empty($recursoJulgamentoFinalTO->getIndicacoes())) {
            $indicacoes = [];
            foreach ($recursoJulgamentoFinalTO->getIndicacoes() as $indicacaoTO) {

                array_push($indicacoes, RecursoIndicacao::newInstance([
                    'indicacaoJulgamentoFinal' => ['id' => $indicacaoTO->getId()],
                    'recursoJulgamentoFinal' => ['id' => $recursoJulgamentoFinal->getId()]
                ]));
            }

            $indicacoes = $this->getRecursoIndicacaoRepository()->persistEmLote($indicacoes);
            $recursoJulgamentoFinal->setRecursosIndicacao($indicacoes);
        }
    }

    /**
     * Retorna um Recurso Indicacao conforme o id informado da chapa.
     *
     * @param $idChapaEleicao
     * @return RecursoIndicacaoTO
     * @throws \Exception
     */
    public function getRecursoIndicacaoPorChapaEleicao($idChapaEleicao) {
        return $this->getRecursoIndicacaoRepository()->getPorChapaEleicao($idChapaEleicao);
    }

    /**
     * Retorna uma nova instância de 'RecursoIndicacaoRepository'.
     *
     * @return RecursoIndicacaoRepository
     */
    private function getRecursoIndicacaoRepository()
    {
        if (empty($this->recursoIndicacaoRepository)) {
            $this->recursoIndicacaoRepository = $this->getRepository(RecursoIndicacao::class);
        }

        return $this->recursoIndicacaoRepository;
    }
}
