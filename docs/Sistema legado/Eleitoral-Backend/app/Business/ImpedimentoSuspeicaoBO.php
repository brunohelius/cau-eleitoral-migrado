<?php
/*
 * ImpedimentoSuspeicaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\EncaminhamentoDenuncia;
use App\Entities\ImpedimentoSuspeicao;
use App\Repository\EncaminhamentoDenunciaRepository;
use App\Repository\ImpedimentoSuspeicaoRepository;

/**
 * Classe responsável por encapsular as implementações de negócio referente a
 * entidade 'ImpedimentoSuspeicaoBO'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class ImpedimentoSuspeicaoBO extends AbstractBO
{
    /**
     * @var ImpedimentoSuspeicaoRepository
     */
    private $impedimentoSuspeicaoRepository;

    /**
     * @var EncaminhamentoDenunciaRepository
     */
    private $encaminhamentoDenunciaRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->impedimentoSuspeicaoRepository = $this->getRepository(ImpedimentoSuspeicao::class);
        $this->encaminhamentoDenunciaRepository = $this->getRepository(EncaminhamentoDenuncia::class);
    }

    /**
     * Retorna 'ImpedimentoSuspeicao' relacionada a um pedido de emcaminhamento.
     *
     * @param $idEmcaminhamento
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getImpedimentoSuspeicaoPorEmcaminhamento($idEmcaminhamento)
    {

        $impedimentoSuspeicao =  $this->encaminhamentoDenunciaRepository->getEmcaminhamentoImpedimentoSuspeicao($idEmcaminhamento);
        if(!empty($impedimentoSuspeicao['impedimentoSuspeicao']['denunciaAdmitida'])) {
            $profissionalAdimitiuDenuncia = $this->getHistoricoDenunciaBO()->getProfissionalHistoricoDenuncia($impedimentoSuspeicao['impedimentoSuspeicao']['denunciaAdmitida']['denuncia']['id'], 'Admitir');
            $impedimentoSuspeicao['impedimentoSuspeicao']['denunciaAdmitida']['profissionalAdimitiuDenuncia'] = $profissionalAdimitiuDenuncia;
        }

        return $impedimentoSuspeicao;
    }

    /**
     * Retorna 'ImpedimentoSuspeicao' relacionada ao Encaminhamento e e DenunciaAdmitida.
     *
     * @param $idEmcaminhamento
     * @param $idDenunciaAdmitida
     * @return mixed|null
     */
    public function getPorEmcaminhamentoAndDenuncia($idEmcaminhamento, $idDenuncia)
    {
        return $this->impedimentoSuspeicaoRepository->getPorEmcaminhamentoAndDenuncia($idEmcaminhamento, $idDenuncia);
    }

    /**
     * Retorna 'ImpedimentoSuspeicao' relacionada a Denuncia.
     *
     * @param $idEmcaminhamento
     * @param $idDenuncia
     * @return mixed|null
     */
    public function getPorDenuncia($idDenuncia)
    {
        return $this->impedimentoSuspeicaoRepository->getPorDenuncia($idDenuncia);
    }

    /**
     * Salva o histórico.
     *
     * @param ImpedimentoSuspeicao $impSuspeicao
     * @throws \Exception
     */
    public function salvar($impSuspeicao, $denuncia, $tipo)
    {
        try{
            $impSuspeicao->setEncaminhamentoDenuncia($impSuspeicao);
            $impSuspeicao->setDenunciaAdmitida($denuncia);
            $this->impedimentoSuspeicaoRepository->persist($impSuspeicao);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Método para retornar a instancia de Historico Denuncia BO
     *
     * @return HistoricoDenunciaBO
     */
    private function getHistoricoDenunciaBO()
    {
        if (empty($this->historicoDenunciaBO)) {
            $this->historicoDenunciaBO = new HistoricoDenunciaBO();
        }
        return $this->historicoDenunciaBO;
    }
}
