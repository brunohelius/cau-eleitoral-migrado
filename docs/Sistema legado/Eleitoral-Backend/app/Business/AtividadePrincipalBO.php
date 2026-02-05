<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 02/09/2019
 * Time: 09:07
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AtividadePrincipalCalendario;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Calendario;
use App\Entities\DeclaracaoAtividade;
use App\Entities\EmailAtividadeSecundaria;
use App\Entities\EmailAtividadeSecundariaTipo;
use App\Entities\Entity;
use App\Entities\HistoricoExtratoConselheiro;
use App\Entities\TipoEmailAtividadeSecundaria;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\AtividadePrincipalCalendarioRepository;
use App\Repository\AtividadeSecundariaCalendarioRepository;
use App\Repository\CalendarioRepository;
use App\Repository\HistoricoExtratoConselheiroRepository;
use app\To\AtividadePrincipalFiltroTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Integer;
use App\Entities\PrazoCalendario;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'AtividadePrincipalCalendario'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class AtividadePrincipalBO extends AbstractBO
{
    /**
     * @var AtividadePrincipalCalendarioRepository
     */
    private $atividadePrincipalCalendarioRepository;

    /**
     * @var AtividadeSecundariaCalendarioRepository
     */
    private $atividadeSecundariaCalendarioRepository;

    /**
     * @var \App\Business\HistoricoCalendarioBO
     */
    private $historicoCalendarioBO;

    /**
     * @var CalendarioRepository
     */
    private $calendarioRepository;

    /**
     * @var \App\Repository\HistoricoExtratoConselheiroRepository
     */
    private $histExtratoConselheiroRepository;

    /**
     *
     * @var \App\Business\PrazoCalendarioBO
     */
    private $prazoCalendarioBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var \App\Repository\HistoricoExtratoConselheiroRepository
     */
    private $historicoExtratoConselheiroRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->calendarioRepository = $this->getRepository(Calendario::class);
        $this->historicoCalendarioBO = app()->make(HistoricoCalendarioBO::class);
        $this->atividadeSecundariaCalendarioBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        $this->atividadePrincipalCalendarioRepository = $this->getRepository(AtividadePrincipalCalendario::class);
        $this->atividadeSecundariaCalendarioRepository = $this->getRepository(AtividadeSecundariaCalendario::class);
        $this->histExtratoConselheiroRepository = $this->getRepository(HistoricoExtratoConselheiro::class);
    }

    /**
     * Método para excluir atividade principal pelo id
     *
     * @param $idAtividade
     * @throws Exception
     */
    public function excluirAtividadePrincipal($idAtividade)
    {
        $atividadePrincipal = $this->atividadePrincipalCalendarioRepository->find($idAtividade);

        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $historicoCalendario = $this->historicoCalendarioBO->criarHistorico(
            $atividadePrincipal->getCalendario(),
            $usuarioLogado->id,
            Constants::DESC_ABA_PERIODO,
            Constants::ACAO_CALENDARIO_EXCLUIR_ATV_PRINCIPAL
        );

        $this->atividadePrincipalCalendarioRepository->delete($atividadePrincipal);
        $this->historicoCalendarioBO->salvar($historicoCalendario);
    }

    /**
     * Excluir Prazo calendário vinculado a Atividade Principal.
     *
     * @param Integer $idAtividadePrincipal
     * @throws Exception
     */
    public function excluirPrazosCalendarioPorAtividadePrincipal($idAtividadePrincipal)
    {
        $prazos = $this->getPrazoCalendarioBO()->getPrazosPorAtividadePrincipal($idAtividadePrincipal);
        if (!empty($prazos)) {
            foreach ($prazos as $prazo) {
                if (!empty($prazo->getId())) {
                    $this->getPrazoCalendarioBO()->excluirPrazo($prazo->getId());
                }
            }
        }
    }

    /**
     * Método para excluir atividade secundaria pelo id
     *
     * @param $idAtividade
     * @throws Exception
     */
    public function excluirAtividadeSecundaria($idAtividade)
    {
        $atividadeSecundaria = $this->atividadeSecundariaCalendarioRepository->find($idAtividade);

        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $historicoCalendario = $this->historicoCalendarioBO->criarHistorico(
            $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario(),
            $usuarioLogado->id,
            Constants::DESC_ABA_PERIODO,
            Constants::ACAO_CALENDARIO_EXCLUIR_ATV_SECUNDARIA
        );

        $this->atividadeSecundariaCalendarioRepository->delete($atividadeSecundaria);
        $this->historicoCalendarioBO->salvar($historicoCalendario);
    }

    /**
     * Retorna lista de Atividades Principais de calendários concluídos.
     */
    public function getAtividadePrincipal()
    {
        return $this->atividadePrincipalCalendarioRepository->getAtividadePrincipal();
    }

    /**
     * Retorna as atividades principais do calendário conforme o id de calendário.
     *
     * @param integer $idCalendario
     * @return array|null
     */
    public function getAtividadePrincipalPorCalendario($idCalendario)
    {
        return $this->atividadePrincipalCalendarioRepository->getPorCalendario($idCalendario);
    }

    /**
     * Retorna as atividades principais do calendário conforme o id de calendário e filtro informados.
     *
     * @param $idCalendario
     * @param AtividadePrincipalFiltroTO $filtroTO
     * @return array|null
     */
    public function getAtividadePrincipalPorCalendarioComFiltro($idCalendario, $filtroTO)
    {
        $atividadesPrincipais = $this->atividadePrincipalCalendarioRepository->getPorCalendarioComFiltro(
            $idCalendario,
            $filtroTO
        );

        if (!empty($atividadesPrincipais)) {
            $atividadesPrincipais = array_map(function ($atividadePrincipal) {
                /** @var AtividadePrincipalCalendario $atividadePrincipal */
                $atividadesSecundarias = $atividadePrincipal->getAtividadesSecundarias();

                /** @var AtividadeSecundariaCalendario $atividadeSecundaria */
                foreach ($atividadesSecundarias as $indice => $atividadeSecundaria) {
                    $atividadeNaoDefinida = true;
                    $statusAtividade = Constants::STATUS_ATIVIDADE_PARAMETRIZACAO_CONCLUIDA;
                    $nivelPrincipal = $atividadePrincipal->getNivel();
                    $nivelSecundaria = $atividadeSecundaria->getNivel();

                    if ($nivelPrincipal == 1 && $nivelSecundaria == 1) {
                        $atividadeNaoDefinida = false;
                        $informacaoComissaoMembro = $atividadeSecundaria->getInformacaoComissaoMembro();
                        if (empty($informacaoComissaoMembro)) {
                            $statusAtividade = Constants::STATUS_ATIVIDADE_AGUARDANDO_PARAMETRIZACAO;
                        } elseif (
                            empty($informacaoComissaoMembro->getDocumentoComissaoMembro())
                            || !$informacaoComissaoMembro->getSituacaoConcluido()
                        ) {
                            $statusAtividade = Constants::STATUS_ATIVIDADE_AGUARDANDO_DOCUMENTO;
                        }
                    }

                    // Verifica status da atividade de definição de e-mail/declaração
                    if (
                        ($nivelPrincipal == 1 && in_array($nivelSecundaria, [2, 4]))
                        || ($nivelPrincipal == 2 && in_array($nivelSecundaria, [1, 2, 3, 4, 5, 6]))
                        || ($nivelPrincipal == 3 && in_array($nivelSecundaria, [1, 2, 3, 4, 5, 6]))
                        || ($nivelPrincipal == 4 && in_array($nivelSecundaria, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]))
                        || ($nivelPrincipal == 5 && in_array($nivelSecundaria, [1, 2, 3, 4, 5]))
                        || ($nivelPrincipal == 6 && in_array($nivelSecundaria, [1, 2, 3, 4, 5, 6]))
                    ) {
                        $atividadeNaoDefinida = false;
                        $hasParametrizacaoEmail = $this->atividadeSecundariaCalendarioBO->hasParametrizacaoEmail(
                            $atividadeSecundaria
                        );

                        if (!$hasParametrizacaoEmail) {
                            $statusAtividade = Constants::STATUS_ATIVIDADE_AGUARDANDO_PARAMETRIZACAO;
                        }
                    }

                    if ($nivelPrincipal == 1 && $nivelSecundaria == 6) {
                        $atividadeNaoDefinida = false;
                        $statusAtividade = Constants::STATUS_ATIVIDADE_ATUALIZACAO_CONCLUIDA;

                        $totalExtrato = $this->histExtratoConselheiroRepository->getTotalExtratoPorAtividadeSecundaria(
                            $atividadeSecundaria->getId()
                        );
                        if ($totalExtrato == 0) {
                            $statusAtividade = Constants::STATUS_ATIVIDADE_AGUARDANDO_ATUALIZACAO;
                        }
                    }

                    if ($nivelPrincipal == 1 && in_array($nivelSecundaria, [3, 5, 7, 8])) {
                        $atividadeNaoDefinida = false;
                        $statusAtividade = Constants::STATUS_ATIVIDADE_REGRAS_NAO_DEFINIDAS;
                    }

                    $atividadeSecundaria->setEmailsAtividadeSecundaria(null);
                    $atividadeSecundaria->setInformacaoComissaoMembro(null);
                    $atividadeSecundaria->setHistoricosExtratoConselheiro(null);
                    $atividadeSecundaria->setDeclaracoesAtividadeSecundaria(null);

                    $atividadeSecundaria->setIsPrazoVigente();
                    $atividadeSecundaria->setStatusAtividade(
                        !$atividadeNaoDefinida ? $statusAtividade : Constants::STATUS_ATIVIDADE_REGRAS_NAO_DEFINIDAS
                    );
                }

                $atividadePrincipal->setAtividadesSecundarias($atividadesSecundarias);
                return $atividadePrincipal;
            }, $atividadesPrincipais);
        }

        return ["atividadePrincipal" => $atividadesPrincipais];
    }

    /**
     * Método para validar se há campos obrigatórios não preenchidos na atividade principal
     *
     * @param AtividadePrincipalCalendario $atividadePrincipal
     * @throws NegocioException
     */
    public function validarCamposObrigatoriosAtividadePrincipal(AtividadePrincipalCalendario $atividadePrincipal)
    {
        $campos = [];
        if (empty($atividadePrincipal->getDataInicio())) {
            $campos[] = 'LABEL_DATA_INI_ATV_PRINCIPAL';
        }
        if (empty($atividadePrincipal->getDataFim())) {
            $campos[] = 'LABEL_DATA_FIM_ATV_PRINCIPAL';
        }
        if (empty($atividadePrincipal->getDescricao())) {
            $campos[] = 'LABEL_DATA_DESC_ATV_PRINCIPAL';
        }
        if (empty($atividadePrincipal->getNivel())) {
            $campos[] = 'LABEL_DATA_NIVEL_ATV_PRINCIPAL';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }


    /**
     * Método para validar se há campos obrigatórios não preenchidos na atividade secundária
     *
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @throws NegocioException
     */
    private function validarCamposObrigatoriosAtividadeSecundaria(AtividadeSecundariaCalendario $atividadeSecundaria)
    {
        $campos = [];
        if (empty($atividadeSecundaria->getDataInicio())) {
            $campos[] = 'LABEL_DATA_INI_ATV_SECUNDARIA';
        }
        if (empty($atividadeSecundaria->getDataFim())) {
            $campos[] = 'LABEL_DATA_FIM_ATV_SECUNDARIA';
        }
        if (empty($atividadeSecundaria->getDescricao())) {
            $campos[] = 'LABEL_DATA_DESC_ATV_SECUNDARIA';
        }
        if (empty($atividadeSecundaria->getNivel())) {
            $campos[] = 'LABEL_DATA_NIVEL_ATV_SECUNDARIA';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }


    /**
     *  Valida se uma data de inicio é maior que uma data de fim para as atividades princiais
     *  Valida se as datas das atividades principais obedecem vigencia do calendário
     *
     * @param Calendario $calendario
     * @throws NegocioException
     * @throws Exception
     */
    public function validarAtividadePrincipal(Calendario $calendario)
    {
        $atividadesPrincipais = $calendario->getAtividadesPrincipais();
        if (!empty($atividadesPrincipais)) {
            foreach ($atividadesPrincipais as $atividadePrincipal) {

                if (!$calendario->isRascunho()) {
                    $this->validarCamposObrigatoriosAtividadePrincipal($atividadePrincipal);
                }

                //Set a hora para evitar os problemas que haviam ao comparar datas do mesmo dia,
                //devido a como essas datas vinham do Front, as vezes uma data final recebia um horário maior
                //que o da data final do calendário
                if (Utils::getDataHoraZero($atividadePrincipal->getDataInicio()) > Utils::getDataHoraZero($atividadePrincipal->getDataFim())) {
                    throw new NegocioException(Message::VALIDACAO_DATA_INICIAL_FINAL);
                }

                if ($atividadePrincipal->isObedeceVigencia() and
                    ((Utils::getDataHoraZero($atividadePrincipal->getDataInicio()) < Utils::getDataHoraZero($calendario->getDataInicioVigencia())) or
                        (Utils::getDataHoraZero($atividadePrincipal->getDataInicio()) > Utils::getDataHoraZero($calendario->getDataFimVigencia())))
                    and !$calendario->isRascunho()) {
                    throw new NegocioException(Message::MSG_DATA_INI_DIVERGENTE_VIGENCIA);
                }

                if ($atividadePrincipal->isObedeceVigencia() and
                    ((Utils::getDataHoraZero($atividadePrincipal->getDataFim()) < Utils::getDataHoraZero($calendario->getDataInicioVigencia())) or
                        (Utils::getDataHoraZero($atividadePrincipal->getDataFim()) > Utils::getDataHoraZero($calendario->getDataFimVigencia())))
                and !$calendario->isRascunho()) {
                    throw new NegocioException(Message::MSG_DATA_FIM_DIVERGENTE_VIGENCIA);
                }

                if (!$calendario->isRascunho()) {
                    $this->validarDatasAtividadeSecundaria($atividadePrincipal);
                }
            }
        }
    }

    /**
     *  Valida se uma data de inicio é maior que uma data de fim para as atividades secundarias
     *  Valida se as datas das atividades secundarias obedecem vigencia da atividade principal
     *
     * @param AtividadePrincipalCalendario $atividadePrincipal
     * @throws NegocioException
     * @throws Exception
     */
    private function validarDatasAtividadeSecundaria(AtividadePrincipalCalendario $atividadePrincipal)
    {
        $atividadesSecundarias = $atividadePrincipal->getAtividadesSecundarias();

        if (!empty($atividadesSecundarias)) {

            foreach ($atividadesSecundarias as $atividadeSecundaria) {

                $this->validarCamposObrigatoriosAtividadeSecundaria($atividadeSecundaria);

                if (Utils::getDataHoraZero($atividadeSecundaria->getDataInicio()) > Utils::getDataHoraZero($atividadeSecundaria->getDataFim())) {
                    throw new NegocioException(Message::VALIDACAO_DATA_INICIAL_FINAL);
                }

                if ((Utils::getDataHoraZero($atividadeSecundaria->getDataInicio()) < Utils::getDataHoraZero($atividadePrincipal->getDataInicio())) or
                    (Utils::getDataHoraZero($atividadeSecundaria->getDataInicio()) > Utils::getDataHoraZero($atividadePrincipal->getDataFim()))) {
                    throw new NegocioException(Message::MSG_DATA_INI_DIVERGENTE_VIGENCIA_ATV_SEC);
                }

                if ((Utils::getDataHoraZero($atividadeSecundaria->getDataFim()) < Utils::getDataHoraZero($atividadePrincipal->getDataInicio())) or
                    (Utils::getDataHoraZero($atividadeSecundaria->getDataFim()) > Utils::getDataHoraZero($atividadePrincipal->getDataFim()))) {
                    throw new NegocioException(Message::MSG_DATA_FIM_DIVERGENTE_VIGENCIA_ATV_SEC);
                }
            }
        }
    }

    /**
     * Salvar a atividade principal
     *
     * @param AtividadePrincipalCalendario $atividadePrincipal
     * @return Entity|array|bool|ObjectManagerAware|object
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvar(AtividadePrincipalCalendario $atividadePrincipal)
    {
        return $this->atividadePrincipalCalendarioRepository->persist($atividadePrincipal);
    }

    /**
     * Busca a atividade principal pelo id
     *
     * @param $id
     * @return null|object
     */
    public function find($id)
    {
        return $this->atividadePrincipalCalendarioRepository->find($id);
    }

    /**
     * Salvar a atividade secundária
     *
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @return Entity|array|bool|ObjectManagerAware|object
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarAtividadeSecundaria(AtividadeSecundariaCalendario $atividadeSecundaria)
    {
        return $this->atividadeSecundariaCalendarioRepository->persist($atividadeSecundaria);
    }

    /**
     * @return \App\Business\PrazoCalendarioBO
     */
    private function getPrazoCalendarioBO()
    {
        if (empty($this->prazoCalendarioBO)) {
            $this->prazoCalendarioBO = app()->make(PrazoCalendarioBO::class);
        }
        return $this->prazoCalendarioBO;
    }

}
