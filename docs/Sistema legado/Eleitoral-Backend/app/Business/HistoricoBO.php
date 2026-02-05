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

use App\Util\Utils;
use App\Entities\Historico;
use App\Repository\HistoricoRepository;
use Doctrine\ORM\EntityRepository;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'Historico'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class HistoricoBO extends AbstractBO
{
    /**
     * @var HistoricoRepository
     */
    private $historicoRepository;

    /**
     * Construtor da Classe.
     */
    public function __construct()
    {
        $this->historicoRepository = $this->getRepository(Historico::class);
    }

    /**
     * Salva o histórico.
     *
     * @param Historico $historico
     * @throws Exception
     */
    public function salvar(Historico $historico)
    {
        try{
            $historico->setDataHistorico(Utils::getData());
            $this->getHistoricoRepository()->persist($historico);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Retorna o objeto Historico construído para salvar o histórico
     *
     * @param $referencia
     * @param $tipoHistorico
     * @param $acao
     * @param $descricao
     * @param null $justificativa
     *
     * @return Historico
     * @throws Exception
     */
    public function criarHistorico($referencia, $tipoHistorico, $acao, $descricao, $justificativa = null)
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        $historico = Historico::newInstance();
        $historico->setDataHistorico(Utils::getData());
        $historico->setAcao($acao);
        $historico->setDescricao($descricao);
        $historico->setResponsavel($usuarioLogado->id ?? null);
        $historico->setTipoHistorico($tipoHistorico);
        $historico->setIdReferencia($referencia->getId());
        $historico->setJustificativa($justificativa);

        return $historico;
    }

    /**
     * Retorna o histórico por Tipo e por Filtro
     *
     * @param $tipo
     * @param $filtroTO
     * @return array|null
     */
    public function getPorTipo($tipo, $filtroTO)
    {
        return $this->getHistoricoRepository()->getPorTipo($tipo, $filtroTO);
    }

    /**
     * Retorna uma nova instância de 'HistoricoRepository'.
     *
     * @return HistoricoRepository
     */
    private function getHistoricoRepository()
    {
        if (empty($this->historicoRepository)) {
            $this->historicoRepository = $this->getRepository(Historico::class);
        }

        return $this->historicoRepository;
    }
}
