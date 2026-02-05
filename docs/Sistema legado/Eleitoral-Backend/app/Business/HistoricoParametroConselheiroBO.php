<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 19/11/2019
 * Time: 10:35
 */

namespace App\Business;

use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Entity;
use App\Entities\HistoricoParametroConselheiro;
use App\Entities\ParametroConselheiro;
use App\Util\Utils;
use App\Config\Constants;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Http\Request;
use App\Repository\HistoricoParametroConselheiroRepository;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'HistoricoParametroConselheiro'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class HistoricoParametroConselheiroBO extends AbstractBO
{
    /**
     * @var \App\Repository\HistoricoParametroConselheiroRepository
     */
    private $historicoParametroConselheiroRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->historicoParametroConselheiroRepository = $this->getRepository(
            HistoricoParametroConselheiro::class
        );
    }

    /**
     * Retorna o objeto HistoricoParametroConselheiro construído para salvar o histórico
     *
     * @param ParametroConselheiro $parametroConselheiro
     * @param $idUsuario
     * @param null $descricao
     * @param int $acao
     * @return HistoricoParametroConselheiro
     * @throws Exception
     */
    public function criarHistorico(ParametroConselheiro $parametroConselheiro, $idUsuario, $descricao = null, $acao = Constants::HISTORICO_ACAO_ALTERAR, $justificativa = '')
    {
        $historico = HistoricoParametroConselheiro::newInstance();
        $historico->setDataHistorico(Utils::getData());
        $historico->setDescricao($descricao);
        $historico->setAcao($acao);
        $historico->setResponsavel($idUsuario);
        $historico->setJustificativa($justificativa);
        $historico->setIdCauUf($parametroConselheiro->getIdCauUf());
        $historico->setAtividadeSecundaria($parametroConselheiro->getAtividadeSecundaria());

        return $historico;
    }

    /**
     * Método responsável por salvar a instancia de Histórico Parametro Conselheiro
     *
     * @param HistoricoParametroConselheiro $historico
     * @return Entity|array|bool|ObjectManagerAware|object
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvar(HistoricoParametroConselheiro $historico)
    {
        return $this->historicoParametroConselheiroRepository->persist($historico);
    }

    /**
     * Método que retorna Historico Completo de Parametro Conselheiro por Filtro
     *
     * @param $filtroTO
     * @return array|null
     * @throws DBALException
     */
    public function getHistoricoCompleto($filtroTO)
    {
        return $this->historicoParametroConselheiroRepository->getHistoricoCompleto($filtroTO);
    }

}