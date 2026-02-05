<?php
/*
 * AtividadeSecundariaCalendarioBO.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ChapaEleicao;
use App\Entities\DeclaracaoAtividade;
use App\Entities\TipoDeclaracaoAtividade;
use App\Exceptions\NegocioException;
use App\Repository\DeclaracaoAtividadeRepository;
use App\Repository\TipoDeclaracaoAtividadeRepository;
use App\Service\CorporativoService;
use App\To\DeclaracaoFiltroTO;
use App\Util\Utils;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'DeclaracaoAtividade'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DeclaracaoAtividadeBO extends AbstractBO
{
    /**
     * @var \App\Service\CorporativoService
     */
    private $corporativoService;

    /**
     *
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaBO;

    /**
     * @var DeclaracaoAtividadeRepository
     */
    private $declaracaoAtividadeRepository;

    /**
     * @var TipoDeclaracaoAtividadeRepository
     */
    private $tipoDeclaracaoAtividadeRepository;

    /**
     * @var DeclaracaoBO
     */
    private $declaracaoBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->declaracaoAtividadeRepository = $this->getRepository(DeclaracaoAtividade::class);
    }

    /**
     * Recupera a declaração de atividade definida pelo 'id' de uma atividade do calendário e pelo 'id' do tipo.
     *
     * @param int $idAtividadeSecundaria
     * @param int $idTipoDeclaracao
     *
     * @return mixed
     * @throws Exception
     */
    public function getDeclaracaoPorAtividadeSecundariaTipo($idAtividadeSecundaria, $idTipoDeclaracao)
    {
        $declaracaoAtividadeSecundaria = $this->declaracaoAtividadeRepository
            ->getDeclaracaoAtividadePorTipoDeclaracao($idTipoDeclaracao, $idAtividadeSecundaria);

        if (!empty($declaracaoAtividadeSecundaria)) {
            $declaracao = $this->getDeclaracaoBO()->getDeclaracao(
                $declaracaoAtividadeSecundaria->getIdDeclaracao()
            );

            if (!empty($declaracao)) {
                $declaracaoAtividadeSecundaria->setDeclaracao($declaracao);
            }
        }

        return $declaracaoAtividadeSecundaria;
    }

    /**
     * Recupera a declaração de atividade definida pelo 'id' de uma atividade do calendário e pelo 'id' do tipo.
     *
     * @param int $idAtividadeSecundaria
     * @param bool $atribuirDeclaracao
     * @return DeclaracaoAtividade[]
     * @throws NegocioException
     */
    public function getDeclaracoesAtividadePorAtividadeSecundaria($idAtividadeSecundaria, $atribuirDeclaracao = false)
    {
        $declaracoesAtividades = $this->declaracaoAtividadeRepository->getDeclaracoesPorAtividadeSecundaria(
            $idAtividadeSecundaria
        );

        if (!empty($declaracoesAtividades) && $atribuirDeclaracao) {
            $idsDeclaracoes = array_map(function ($declaracaoAtividade) {
                /** @var DeclaracaoAtividade $declaracaoAtividade */
                return $declaracaoAtividade->getIdDeclaracao();
            }, $declaracoesAtividades);

            $declaracoes = $this->getDeclaracaoBO()->getListaDeclaracoesFormatadaPorIds($idsDeclaracoes);

            /** @var DeclaracaoAtividade $declaracaoAtividade */
            foreach ($declaracoesAtividades as $declaracaoAtividade) {
                $declaracaoAtividade->setDeclaracao(
                    Utils::getValue($declaracaoAtividade->getIdDeclaracao(), $declaracoes)
                );
            }
        }

        return $declaracoesAtividades;
    }

    /**
     * Recupera a declaração de atividade definida através de uma determinada chapa
     *
     * @param ChapaEleicao $chapaEleicao
     * @return mixed
     * @throws Exception
     */
    public function getDeclaracaoPorChapa(ChapaEleicao $chapaEleicao)
    {
        $atividadeSecundaria = $chapaEleicao->getAtividadeSecundariaCalendario();
        $idTipoCandidatura = $chapaEleicao->getTipoCandidatura()->getId();

        $idTipoDeclaracao = ($idTipoCandidatura == Constants::TIPO_CANDIDATURA_IES)
            ? Constants::TIPO_DECLARACAO_CADASTRO_CHAPA_IES
            : Constants::TIPO_DECLARACAO_CADASTRO_CHAPA_UF;

        $declaracaoAtividade = $this->getDeclaracaoPorAtividadeSecundariaTipo(
            $atividadeSecundaria->getId(),
            $idTipoDeclaracao
        );

        return $declaracaoAtividade;
    }

    /**
     * Recupera a total de declarações definidas por idDeclaracao.
     *
     * @param int $idDeclaracao
     * @return mixed
     */
    public function getQtdDeclaracoesDefinidasPorDeclaracao($idDeclaracao)
    {
        return $this->declaracaoAtividadeRepository->getQtdDeclaracoesPorDeclaracao($idDeclaracao);
    }

    /**
     * Retorna uma nova instância de 'CorporativoService'.
     *
     * @return CorporativoService|mixed
     */
    private function getCorporativoService()
    {
        if (empty($this->corporativoService)) {
            $this->corporativoService = app()->make(CorporativoService::class);
        }

        return $this->corporativoService;
    }

    /**
     * Retorna uma nova instância de 'AtividadeSecundariaCalendarioBO'.
     *
     * @return AtividadeSecundariaCalendarioBO|mixed
     */
    private function getAtividadeSecundariaBO()
    {
        if (empty($this->atividadeSecundariaBO)) {
            $this->atividadeSecundariaBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }

        return $this->atividadeSecundariaBO;
    }

    /**
     * Retorna uma nova instância de 'TipoDeclaracaoAtividadeRepository'.
     *
     * @return TipoDeclaracaoAtividadeRepository|mixed
     */
    private function getTipoDeclaracaoAtividadeRepository()
    {
        if (empty($this->tipoDeclaracaoAtividadeRepository)) {
            $this->tipoDeclaracaoAtividadeRepository = $this->getRepository(TipoDeclaracaoAtividade::class);
        }

        return $this->tipoDeclaracaoAtividadeRepository;
    }

    /**
     * Retorna uma nova instância de 'DeclaracaoBO'.
     *
     * @return DeclaracaoBO|mixed
     */
    private function getDeclaracaoBO()
    {
        if (empty($this->declaracaoBO)) {
            $this->declaracaoBO = app()->make(DeclaracaoBO::class);
        }

        return $this->declaracaoBO;
    }
}
