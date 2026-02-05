<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 02/09/2019
 * Time: 10:35
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\Calendario;
use App\Entities\HistoricoCalendario;
use App\Exceptions\NegocioException;
use App\Repository\HistoricoCalendarioRepository;
use App\Service\CorporativoService;
use App\To\AcaoHistoricoCalendarioTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'HistoricoCalendario'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class HistoricoCalendarioBO extends AbstractBO
{
    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * Define as ações de alteração e de exclusão do histórico de calendário.
     *
     * @var array
     */
    private $acoesAlteracaoExclusao = [
        Constants::ACAO_CALENDARIO_ALTERAR,
        Constants::ACAO_CALENDARIO_EXCLUIR,
        Constants::ACAO_CALENDARIO_EXCLUIR_ATV_PRINCIPAL,
        Constants::ACAO_CALENDARIO_EXCLUIR_ATV_SECUNDARIA,
        Constants::ACAO_CALENDARIO_ALTERAR_PRAZO,
        Constants::ACAO_CALENDARIO_EXCLUIR_PRAZO
    ];

    /**
     * @var HistoricoCalendarioRepository
     */
    private $historicoCalendarioRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->corporativoService = app()->make(CorporativoService::class);
        $this->historicoCalendarioRepository = $this->getRepository(HistoricoCalendario::class);
    }

    /**
     * Retorna o objeto HistoricoCalendario construído para salvar o histórico
     *
     * @param Calendario $calendario
     * @param $idUsuario
     * @param string $descricaoAba
     * @param int $acao
     * @return HistoricoCalendario
     * @throws Exception
     */
    public function criarHistorico(Calendario $calendario, $idUsuario, $descricaoAba, $acao = Constants::ACAO_CALENDARIO_INSERIR)
    {
        $historico = HistoricoCalendario::newInstance();
        $historico->setData(Utils::getData());
        $historico->setCalendario($calendario);
        $historico->setAcao($acao);
        $historico->setResponsavel($idUsuario);
        $historico->setDescricaoAba($descricaoAba);

        return $historico;
    }

    /**
     * Salva o histórico do calendário
     *
     * @param HistoricoCalendario $historicoCalendario
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvar(HistoricoCalendario $historicoCalendario)
    {
        $this->historicoCalendarioRepository->persist($historicoCalendario);
    }


    /**
     * Recupera os históricos do calendário de acordo com o 'id' do calendário informado.
     *
     * @param integer $idCalendario
     * @return HistoricoCalendario[]
     * @throws NegocioException
     */
    public function getHistoricoPorCalendario($idCalendario)
    {
        $historicos = $this->historicoCalendarioRepository->getHistoricoPorCalendario($idCalendario);

        foreach ($historicos as $historico) {
            $historico->setDescricoesAcao($this->getDescricoesAcao($historico));
            $historico->setDadosResponsavel(
                $this->corporativoService->getUsuarioPorId($historico->getResponsavel())
            );
        }

        return $historicos;
    }

    /**
     * Recupera as descrições das ações realizadas.
     *
     * @param HistoricoCalendario $historico
     * @return ArrayCollection
     */
    private function getDescricoesAcao(HistoricoCalendario $historico)
    {
        $descricaoAcao = new ArrayCollection();

        if ($historico->getAcao() == Constants::ACAO_CALENDARIO_INSERIR) {
            $acaoHistoricoCalendarioTO = AcaoHistoricoCalendarioTO::newInstance();

            $acaoHistoricoCalendarioTO->setDescricao(
                Constants::$acoesHistoricoCalendario[Constants::ACAO_CALENDARIO_INSERIR]
            );

            $descricaoAcao->add($acaoHistoricoCalendarioTO);
        }

        if ($historico->getAcao() == Constants::ACAO_CALENDARIO_INSERIR_PRAZO) {
            $acaoHistoricoCalendarioTO = AcaoHistoricoCalendarioTO::newInstance();

            $acaoHistoricoCalendarioTO->setDescricao(
                Constants::$acoesHistoricoCalendario[Constants::ACAO_CALENDARIO_INSERIR_PRAZO]
            );

            $descricaoAcao->add($acaoHistoricoCalendarioTO);
        }

        if ($historico->getAcao() == Constants::ACAO_CALENDARIO_CONCLUIR) {
            $acaoHistoricoCalendarioTO = AcaoHistoricoCalendarioTO::newInstance();

            $acaoHistoricoCalendarioTO->setDescricao(
                Constants::$acoesHistoricoCalendario[Constants::ACAO_CALENDARIO_CONCLUIR]
            );

            $descricaoAcao->add($acaoHistoricoCalendarioTO);
        }

        if ($historico->getAcao() == Constants::ACAO_CALENDARIO_INSERIR_ATV_PRINCIPAL) {
            $acaoHistoricoCalendarioTO = AcaoHistoricoCalendarioTO::newInstance();

            $acaoHistoricoCalendarioTO->setDescricao(
                Constants::$acoesHistoricoCalendario[Constants::ACAO_CALENDARIO_INSERIR_ATV_PRINCIPAL]
            );

            $descricaoAcao->add($acaoHistoricoCalendarioTO);
        }

        if (
            $historico->getAcao() == Constants::ACAO_CALENDARIO_ALTERAR
            && count($historico->getJustificativaAlteracao()) == 0
        ) {
            $acaoHistoricoCalendarioTO = AcaoHistoricoCalendarioTO::newInstance();

            $acaoHistoricoCalendarioTO->setDescricao(
                Constants::$acoesHistoricoCalendario[Constants::ACAO_CALENDARIO_ALTERAR]
            );

            $descricaoAcao->add($acaoHistoricoCalendarioTO);
        }

        if (
            in_array($historico->getAcao(), $this->acoesAlteracaoExclusao)
            && count($historico->getJustificativaAlteracao()) > 0
        ) {
            foreach ($historico->getJustificativaAlteracao() as $justificativa) {
                $acaoHistoricoCalendarioTO = AcaoHistoricoCalendarioTO::newInstance();
                $acaoHistoricoCalendarioTO->setDescricao($justificativa->getDescricao());
                $descricaoAcao->add($acaoHistoricoCalendarioTO);
            }
        }

        return $descricaoAcao;
    }
}
