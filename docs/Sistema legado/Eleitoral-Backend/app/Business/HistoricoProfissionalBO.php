<?php
/*
 * HistoricoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\HistoricoProfissional;
use App\Entities\Profissional;
use App\Repository\HistoricoProfissionalRepository;
use App\Util\Utils;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'HistoricoProfissional'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class HistoricoProfissionalBO extends AbstractBO
{
    /**
     * @var HistoricoProfissionalRepository
     */
    private $historicoProfissionalRepository;

    /**
     * Construtor da Classe.
     */
    public function __construct()
    {
    }

    /**
     * Salva o HistoricoProfissional.
     *
     * @param HistoricoProfissional $historicoProfissional
     * @throws Exception
     */
    public function salvar(HistoricoProfissional $historicoProfissional)
    {
        try{
            $historicoProfissional->setDataHistorico(Utils::getData());
            $this->getHistoricoProfissionalRepository()->persist($historicoProfissional);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Retorna o objeto Historico construído para salvar o histórico
     *
     * @param $idReferencia
     * @param $tipoHistorico
     * @param $acao
     * @param $descricao
     * @param null $justificativa
     * @param null $idProfissional
     *
     * @return HistoricoProfissional
     */
    public function criarHistorico(
        $idReferencia,
        $tipoHistorico,
        $acao,
        $descricao,
        $justificativa = null,
        $idProfissional = null
    ) {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $profissional = ['id' => $idProfissional ?? $usuarioLogado->idProfissional];

        $historicoProfissional = HistoricoProfissional::newInstance(
            compact('acao', 'descricao', 'tipoHistorico', 'idReferencia', 'justificativa', 'profissional')
        );

        return $historicoProfissional;
    }

    /**
     * Retorna uma nova instância de 'HistoricoProfissionalRepository'.
     *
     * @return HistoricoProfissionalRepository
     */
    private function getHistoricoProfissionalRepository()
    {
        if (empty($this->historicoProfissionalRepository)) {
            $this->historicoProfissionalRepository = $this->getRepository(HistoricoProfissional::class);
        }

        return $this->historicoProfissionalRepository;
    }
}
