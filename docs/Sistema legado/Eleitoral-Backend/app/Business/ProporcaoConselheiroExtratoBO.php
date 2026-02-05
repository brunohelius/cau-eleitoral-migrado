<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 19/11/2019
 * Time: 10:35
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\HistoricoExtratoConselheiro;
use App\Entities\ParametroConselheiro;
use App\Entities\ProporcaoConselheiroExtrato;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\PDFFActory;
use App\Factory\XLSFactory;
use App\Repository\ProporcaoConselheiroExtratoRepository;
use App\Service\CorporativoService;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'ProporcaoConselheiroExtrato'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class ProporcaoConselheiroExtratoBO extends AbstractBO
{
    /**
     * @var ProporcaoConselheiroExtratoRepository
     */
    private $proporcaoConselheiroExtratoRepository;

    /**
     * @var ParametroConselheiroBO
     */
    private $parametroConselheiroBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->proporcaoConselheiroExtratoRepository = $this->getRepository(
            ProporcaoConselheiroExtrato::class
        );
    }

    /**
     * Retorna o número proporção de conselheiros por atividade principal independente do nível e por id CauUf
     *
     * @param $idAtividadePrincipal
     * @param $idCauUf
     * @return int
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getProporcaoConselheirosPorAtividadeEIdCauUf($idAtividadePrincipal, $idCauUf)
    {
        $proporcao = 0;

        /**
        $proporcaoConselheiro = $this->getProporcaoConselheiroExtratoRepository()->getUltimaPorAtividadePrincipalECauUf(
            $idAtividadePrincipal,
            $idCauUf
        );**/

        $paramConselheiro = $this->getParametroConselheiroBO()->getParamConselheiroPorAtividadePrincipalAndCauUf(
            $idAtividadePrincipal, $idCauUf
        );

        if(!empty($paramConselheiro)){
            $proporcao = $paramConselheiro->getNumeroProporcaoConselheiro();
        }else{
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        return $proporcao;
    }

    /**
     * Retorna o número proporção de conselheiros por atividade principal independente do nível e por id CauUf
     *
     * @param $idCalendario
     * @param $idCauUf
     * @return int
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getProporcaoConselheirosPorCalendarioEIdCauUf($idCalendario, $idCauUf)
    {
        $proporcao = 0;

        /**
        $proporcaoConselheiro = $this->getProporcaoConselheiroExtratoRepository()->getUltimaPorCalendarioECauUf(
            $idCalendario,
            $idCauUf
        );**/

        $paramConselheiro = $this->getParametroConselheiroBO()->getParamConselheiroPorCalendarioAndCauUf(
            $idCalendario, $idCauUf
        );

        if(!empty($paramConselheiro)){
            $proporcao = $paramConselheiro->getNumeroProporcaoConselheiro();
        }else{
            //throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO);
        }

        return $proporcao;
    }

    /**
     * Retorna a instância de ProporcaoConselheiroExtratoRepository conforme o padrão Lazy Initialization.
     *
     * @return ProporcaoConselheiroExtratoRepository
     */
    private function getProporcaoConselheiroExtratoRepository()
    {
        if (empty($this->proporcaoConselheiroExtratoRepository)) {
            $this->proporcaoConselheiroExtratoRepository = $this->getRepository(
                ProporcaoConselheiroExtrato::class
            );
        }

        return $this->proporcaoConselheiroExtratoRepository;
    }

    /**
     * Retorna a instância de ProporcaoConselheiroExtratoRepository conforme o padrão Lazy Initialization.
     *
     * @return ParametroConselheiroBO
     */
    private function getParametroConselheiroBO()
    {
        if (empty($this->parametroConselheiroBO)) {
            $this->parametroConselheiroBO = app()->make(ParametroConselheiroBO::class);
        }

        return $this->parametroConselheiroBO;
    }
}
