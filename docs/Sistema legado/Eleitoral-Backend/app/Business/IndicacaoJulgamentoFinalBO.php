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

use App\Config\Constants;
use App\Entities\ChapaEleicao;
use App\Entities\IndicacaoJulgamentoFinal;
use App\Entities\JulgamentoFinal;
use App\Entities\JulgamentoRecursoImpugnacao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\IndicacaoJulgamentoFinalRepository;
use App\Repository\JulgamentoFinalRepository;
use App\Service\ArquivoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\IndicacaoJulgamentoFinalTO;
use App\To\JulgamentoFinalTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Support\Facades\Lang;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'IndicacaoJulgamentoFinal'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class IndicacaoJulgamentoFinalBO extends AbstractBO
{

    /**
     * @var IndicacaoJulgamentoFinalRepository
     */
    private $indicacaoJulgamentoFinalRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salva o julgamento final da chapa da eleição
     *
     * @param IndicacaoJulgamentoFinalTO[] $indicacoesTO
     * @param JulgamentoFinal $julgamentoFinal
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarIndicacoes($indicacoesTO, $julgamentoFinal)
    {
        $indicacoes = [];
        foreach ($indicacoesTO as $indicacaoTO) {

            $membro = !empty($indicacaoTO->getIdMembroChapa()) ? ['id' => $indicacaoTO->getIdMembroChapa()] : null;

            array_push($indicacoes, IndicacaoJulgamentoFinal::newInstance([
                'numeroOrdem' => $indicacaoTO->getNumeroOrdem(),
                'tipoParticipacaoChapa' => ['id' => $indicacaoTO->getIdTipoParicipacaoChapa()],
                'membroChapa' => $membro,
                'julgamentoFinal' => ['id' => $julgamentoFinal->getId()]
            ]));
        }

        /** @var IndicacaoJulgamentoFinal[] $indicacoesSalvas */
        $indicacoesSalvas = $this->getIndicacaoJulgamentoFinalRepository()->persistEmLote($indicacoes);

        $julgamentoFinal->setIndicacoes($indicacoesSalvas);
    }

    /**
     * Retorna uma nova instância de 'IndicacaoJulgamentoFinalRepository'.
     *
     * @return IndicacaoJulgamentoFinalRepository
     */
    private function getIndicacaoJulgamentoFinalRepository()
    {
        if (empty($this->indicacaoJulgamentoFinalRepository)) {
            $this->indicacaoJulgamentoFinalRepository = $this->getRepository(IndicacaoJulgamentoFinal::class);
        }

        return $this->indicacaoJulgamentoFinalRepository;
    }
}




