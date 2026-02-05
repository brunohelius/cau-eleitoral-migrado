<?php
/*
 * PrazoCalendarioBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\Calendario;
use App\Entities\PrazoCalendario;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\CalendarioRepository;
use App\Repository\PrazoCalendarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Classe responsável por encapsular as implementações de negócio referentes à entidade 'PrazoCalendario'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class PrazoCalendarioBO extends AbstractBO
{
    /**
     * @var \App\Repository\PrazoCalendarioRepository
     */
    private $prazoCalendarioRepository;

    /**
     * @var CalendarioRepository
     */
    private $calendarioRepository;

    /**
     * @var \App\Business\HistoricoCalendarioBO
     */
    private $historicoCalendarioBO;

    /**
     * @var \App\Business\AtividadePrincipalBO
     */
    private $atividadePrincipalBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->historicoCalendarioBO = app()->make(HistoricoCalendarioBO::class);
        $this->atividadePrincipalBO = app()->make(AtividadePrincipalBO::class);
        $this->calendarioRepository = $this->getRepository(Calendario::class);
        $this->prazoCalendarioRepository = $this->getRepository(PrazoCalendario::class);
    }

    /**
     * Verifica a existência de algum prazo vinculado ao calendário informado.
     *
     * @param $idCalendario
     * @return boolean
     * @throws NonUniqueResultException
     */
    public function hasPrazosVinculadosAoCalendario($idCalendario)
    {
        $totalPrazosCalendario = $this->prazoCalendarioRepository->getTotalPrazosPorCalendario($idCalendario);
        return !empty($totalPrazosCalendario) && Constants::VALOR_ZERO < $totalPrazosCalendario;
    }  

    /**
     * Salva os dados de Prazo do Calendário de Eleição
     *
     * @param Calendario $calendario
     * @param array $justificativas
     * @param array $prazosExcluidos
     * @throws Exception
     */
    public function salvar(Calendario $calendario, $justificativas = null, $prazosExcluidos = null)
    {
        $condicaoJustificativa = !empty($justificativas);
        $atividadesPrincipais = $calendario->getAtividadesPrincipais();

        $acao = Constants::ACAO_CALENDARIO_INSERIR_PRAZO;
        if ($condicaoJustificativa) {
            $acao = Constants::ACAO_CALENDARIO_ALTERAR_PRAZO;
        }

        try {
            $this->beginTransaction();

            if (!empty($prazosExcluidos)) {
                foreach ($prazosExcluidos as $prazoExcluido) {
                    $this->excluirPrazo($prazoExcluido);
                }
            }

            $atividadePrincipalSalvo = null;
            if (!empty($atividadesPrincipais)) {
                foreach ($atividadesPrincipais as $atividadePrincipal) {
                    $this->atividadePrincipalBO->validarCamposObrigatoriosAtividadePrincipal($atividadePrincipal);
                    if (!empty($atividadePrincipal->getId())) {
                        $atividadePrincipalSalvo = $this->atividadePrincipalBO->find($atividadePrincipal->getId());
                        $this->salvarPrazos($atividadePrincipal->getPrazos(), $atividadePrincipalSalvo, null);
                    }
                }
            }

            if (!empty($atividadePrincipalSalvo)) {

                $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
                $historicoCalendario = $this->historicoCalendarioBO->criarHistorico(
                    $atividadePrincipalSalvo->getCalendario(),
                    $usuarioLogado->id,
                    Constants::DESC_ABA_PRAZO, $acao
                );

                if ($condicaoJustificativa) {
                    foreach ($justificativas as $justificativa) {
                        $justificativa->setHistorico($historicoCalendario);
                    }
                    $historicoCalendario->setJustificativaAlteracao($justificativas);
                }

                $this->historicoCalendarioBO->salvar($historicoCalendario);
            }
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Retorna os Prazos do calendario conforme o id informado.
     *
     * @param $idCalendario
     * @return mixed|null
     * @throws NonUniqueResultException
     */
    public function getPrazosPorCalendario($idCalendario)
    {
        $calendario = $this->calendarioRepository->getPrazosPorCalendario($idCalendario);
        if ($calendario) {
            $calendario->setSituacaoVigente();
            $calendario->filtrarPrazos();
        }
        return $calendario;
    }
    
    /**
     * Retorna prazo calendário vinculados a atividade principal.
     * 
     * @param Integer $idAtividadePrincipal
     * @return array
     */
    public function getPrazosPorAtividadePrincipal($idAtividadePrincipal){
        return $this->prazoCalendarioRepository->getPrazosPorAtividadePrincipal($idAtividadePrincipal);
    }

    /**
     * Excluir prazo de calendário pelo id
     *
     * @param $idPrazo
     * @throws Exception
     */
    public function excluirPrazo($idPrazo)
    {
        $prazo = $this->prazoCalendarioRepository->find($idPrazo);
        
        if(!empty($prazo)){
            $calendario = $this->calendarioRepository->getPorPrazo($idPrazo);

            $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
            $historicoCalendario = $this->historicoCalendarioBO->criarHistorico(
                $calendario,
                $usuarioLogado->id,
                Constants::DESC_ABA_PRAZO,
                Constants::ACAO_CALENDARIO_EXCLUIR_PRAZO
                );
            
            $this->prazoCalendarioRepository->delete($prazo);
            $this->historicoCalendarioBO->salvar($historicoCalendario);
        }
       
    }

    /**
     * Salva a coleção de prazos, realizado a "navegação" na árvore hierárquica dos dados.
     *
     * @param $prazosCalendarios
     * @param $atividadePrincipal
     * @param $prazoPai
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarPrazos($prazosCalendarios, $atividadePrincipal, $prazoPai)
    {
        if (!empty($prazosCalendarios)) {
            foreach ($prazosCalendarios as $prazoCalendario) {
                $this->validarDadosPrazoCalendario($prazoCalendario);
                $prazosFilhos = new ArrayCollection();

                $prazoCalendario->setAtividadePrincipal($atividadePrincipal);

                if (!empty($prazoCalendario->getPrazos())) {
                    $prazosFilhos = $prazoCalendario->getPrazos();
                    $prazoCalendario->setPrazos(null);
                }

                if (!empty($prazoPai)) {
                    $prazoCalendario->setPrazoPai($prazoPai);
                }

                $atividadePrincipal->setPrazos(new ArrayCollection());
                $atividadePrincipal->getPrazos()->add($prazoCalendario);

                $this->prazoCalendarioRepository->persist($prazoCalendario);

                if (!empty($prazosFilhos)) {
                    $this->salvarPrazos($prazosFilhos, $atividadePrincipal, $prazoCalendario);
                }
            }
        }
    }

    /**
     * Método para validar se há campos obrigatórios não preenchidos no prazo
     *
     * @param PrazoCalendario $prazoCalendario
     * @throws NegocioException
     */
    private function validarDadosPrazoCalendario(PrazoCalendario $prazoCalendario)
    {
        $campos = [];

        if (empty($prazoCalendario->getDescricaoAtividade())) {
            $campos[] = 'LABEL_DESCRICAO_PRAZO';
        }
        if(empty($prazoCalendario->getDuracao())){
            $campos[] = 'LABEL_DURACAO_PRAZO';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }
}
