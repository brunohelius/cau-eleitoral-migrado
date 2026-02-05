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
use App\Entities\JulgamentoSegundaInstanciaRecurso;
use App\Repository\IndicacaoJulgamentoFinalRepository;
use App\Repository\IndicacaoJulgamentoSegundaInstanciaRecursoRepository;
use App\To\IndicacaoJulgamentoFinalTO;
use App\To\IndicacaoJulgamentoSegundaInstanciaRecursoTO;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'IndicacaoJulgamentoSegundaInstanciaRecurso'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class IndicacaoJulgamentoSegundaInstanciaRecursoBO extends AbstractBO
{

    /**
     * @var IndicacaoJulgamentoSegundaInstanciaRecursoRepository
     */
    private $indicacaoJulgamentoSegundaInstanciaRecursoRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salva o julgamento final da chapa da eleição
     *
     * @param IndicacaoJulgamentoSegundaInstanciaRecursoTO[] $indicacoesTO
     * @param JulgamentoSegundaInstanciaRecurso $julgamentoSegundaInstanciaRecurso
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarIndicacoes($indicacoesTO, JulgamentoSegundaInstanciaRecurso $julgamentoSegundaInstanciaRecurso)
    {
        $indicacoes = [];
        foreach ($indicacoesTO as $indicacaoTO) {

            $membro = !empty($indicacaoTO->getIdMembroChapa()) ? ['id' => $indicacaoTO->getIdMembroChapa()] : null;

            array_push($indicacoes, IndicacaoJulgamentoSegundaInstanciaRecurso::newInstance([
                'numeroOrdem' => $indicacaoTO->getNumeroOrdem(),
                'tipoParticipacaoChapa' => ['id' => $indicacaoTO->getIdTipoParicipacaoChapa()],
                'membroChapa' => $membro,
                'julgamentoSegundaInstanciaRecurso' => ['id' => $julgamentoSegundaInstanciaRecurso->getId()]
            ]));
        }

        $indicacoesSalvas = $this->getIndicacaoJulgamentoSegundaInstanciaRecursoRepository()->persistEmLote($indicacoes);

        $julgamentoSegundaInstanciaRecurso->setIndicacoes($indicacoesSalvas);
    }

    /**
     * Retorna uma nova instância de 'IndicacaoJulgamentoSegundaInstanciaRecurso'.
     *
     * @return IndicacaoJulgamentoFinalRepository|\Doctrine\ORM\EntityRepository
     */
    private function getIndicacaoJulgamentoSegundaInstanciaRecursoRepository()
    {
        if (empty($this->indicacaoJulgamentoSegundaInstanciaRecursoRepository)) {
            $this->indicacaoJulgamentoSegundaInstanciaRecursoRepository = $this->getRepository(IndicacaoJulgamentoSegundaInstanciaRecurso::class);
        }

        return $this->indicacaoJulgamentoSegundaInstanciaRecursoRepository;
    }
}




