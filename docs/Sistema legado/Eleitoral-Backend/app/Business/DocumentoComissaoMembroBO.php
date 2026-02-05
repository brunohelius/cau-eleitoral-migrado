<?php
/*
 * DocumentoComissaoMembroBO.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\Entity;
use App\Repository\DocumentoComissaoMembroRepository;
use App\To\DocumentoComissaoMembroTO;
use App\Util\Utils;
use App\Config\Constants;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Entities\DocumentoComissaoMembro;
use App\Entities\InformacaoComissaoMembro;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entities\HistoricoInformacaoComissaoMembro;
use App\Entities\HistoricoAlteracaoInformacaoComissaoMembro;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'DocumentoComissaoMembro'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DocumentoComissaoMembroBO extends AbstractBO
{

    /**
     * @var DocumentoComissaoMembroRepository
     */
    private $documentoComissaoMembroRepository;

    /**
     * @var \App\Business\HistoricoInformacaoComissaoMembroBO
     */
    private $historicoInformacaoComissaoMembroBO;

    /**
     * @var \App\Business\HistoricoAlteracaoInformacaoComissaoMembroBO
     */
    private $historicoAlteracaoInformacaoComissaoMembroBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->documentoComissaoMembroRepository = $this->getRepository(DocumentoComissaoMembro::class);
        $this->historicoInformacaoComissaoMembroBO = app()->make(HistoricoInformacaoComissaoMembroBO::class);
        $this->historicoAlteracaoInformacaoComissaoMembroBO = app()->make(
            HistoricoAlteracaoInformacaoComissaoMembroBO::class
        );
    }

    /**
     * Recupera o documento da comissão de acordo com o 'id' informado.
     *
     * @param $id
     * @return mixed|null
     * @throws NonUniqueResultException
     * @throws NegocioException
     */
    public function getPorId($id): DocumentoComissaoMembroTO
    {
        return $this->documentoComissaoMembroRepository->getPorId($id);
    }

    /**
     * Salva um novo documento da comissão do membro.
     *
     * @param DocumentoComissaoMembro $documentoComissaoMembro
     * @return Entity|array|bool|ObjectManagerAware|object
     * @throws Exception
     */
    public function salvar(DocumentoComissaoMembro $documentoComissaoMembro)
    {
        $this->validarCamposObrigatorios($documentoComissaoMembro);
        $coAcaoHistorico = empty($documentoComissaoMembro->getId()) ?
            Constants::ACAO_HISTORICO_INFORMACAO_COMISSAO_INSERIR
            : Constants::ACAO_HISTORICO_INFORMACAO_COMISSAO_ALTERAR;

        $documentoComissaoMembro->substituiLinkImagem();

        $documentoComissaoMembroPreAtualizacao = DocumentoComissaoMembroTO::newInstance();
        if (!empty($documentoComissaoMembro->getId())) {
            $documentoComissaoMembroPreAtualizacao = clone $this->getPorId($documentoComissaoMembro->getId());
        }

        try {

            $this->beginTransaction();

            $documentoComissaoMembroSalvo = $this->documentoComissaoMembroRepository->persist($documentoComissaoMembro);

            $histInformacaoComissaoMembro = $this->getHistInformacaoComissaoMembro(
                $documentoComissaoMembro->getInformacaoComissaoMembro(),
                $coAcaoHistorico
            );

            $histInformacaoComissaoMembroSalvo = $this->historicoInformacaoComissaoMembroBO->salvar(
                $histInformacaoComissaoMembro
            );

            $historicoAlteracoes = $this->getDadosHistoricoAlteracaoComissaoMembro(
                $documentoComissaoMembro,
                $documentoComissaoMembroPreAtualizacao,
                $histInformacaoComissaoMembroSalvo
            );

            foreach ($historicoAlteracoes as $historicoAltecao) {
                $this->historicoAlteracaoInformacaoComissaoMembroBO->salvar($historicoAltecao);
            }

            $this->commitTransaction();

        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        $documentoComissaoMembroSalvo->setInformacaoComissaoMembro(null);

        return $documentoComissaoMembroSalvo;
    }

    /**
     * Método responsável por concluir a informação da comissão membro.
     *
     * @param $id
     * @return Entity|array|bool|ObjectManagerAware|object
     * @throws Exception
     */
    public function concluir(DocumentoComissaoMembro $documentoComissaoMembro)
    {
        $this->validarCamposConcluir($documentoComissaoMembro);

        try {

            $this->beginTransaction();

            $documentoComissaoSalvo = $this->salvar($documentoComissaoMembro);

            $informacaoComissaoMembro = $this->getPorId($documentoComissaoSalvo->getInformacaoComissaoMembro()->getId());
            $informacaoComissaoMembro->setSituacaoConcluido(true);
            $informacaoComissaoMembroSalva = $this->informacaoComissaoMembroRepository->persist(
                $informacaoComissaoMembro
            );

            $historicoInformacaoComissaoMembro = $this->getHistInformacaoComissaoMembro(
                $informacaoComissaoMembroSalva,
                Constants::ACAO_HISTORICO_INFORMACAO_COMISSAO_CONCLUIR
            );

            $this->historicoInformacaoComissaoMembroBO->salvar($historicoInformacaoComissaoMembro);

            $this->commitTransaction();

        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return $informacaoComissaoMembroSalva;
    }

    /**
     * Válida o preenchimento dos campos obrigatórios.
     *
     * @param DocumentoComissaoMembro $documentoComissaoMembro
     * @throws NegocioException
     */
    private function validarCamposObrigatorios(DocumentoComissaoMembro $documentoComissaoMembro)
    {
        $campos = [];

        if (empty($documentoComissaoMembro->getDescricaoCabecalho())
            && boolval($documentoComissaoMembro->isSituacaoCabecalhoAtivo())) {
            $campos[] = 'LABEL_CABECALHO';
        }

        if (empty($documentoComissaoMembro->getDescricaoTextoInicial())
            && boolval($documentoComissaoMembro->isSituacaoTextoInicial())) {
            $campos[] = 'LABEL_TEXTO_INICIAL';
        }

        if (empty($documentoComissaoMembro->getDescricaoTextoFinal())
            && boolval($documentoComissaoMembro->isSituacaoTextoFinal())) {
            $campos[] = 'LABEL_TEXTO_FINAL';
        }

        if (empty($documentoComissaoMembro->getDescricaoTextoRodape())
            && boolval($documentoComissaoMembro->isSituacaoTextoRodape())) {
            $campos[] = 'LABEL_RODAPE';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Recupera a entidade 'HistoricoInformacaoComissaoMembro' populada.
     *
     * @param InformacaoComissaoMembro $informacaoComissaoMembro
     * @param $coAcao
     * @return HistoricoInformacaoComissaoMembro
     * @throws Exception
     */
    private function getHistInformacaoComissaoMembro(InformacaoComissaoMembro $informacaoComissaoMembro, $coAcao)
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $historicoInformacaoComissaoMembro = HistoricoInformacaoComissaoMembro::newInstance();
        $historicoInformacaoComissaoMembro->setAcao($coAcao);
        $historicoInformacaoComissaoMembro->setDataHistorico(Utils::getData());
        $historicoInformacaoComissaoMembro->setResponsavel($usuarioLogado->id);
        $historicoInformacaoComissaoMembro->setInformacaoComissaoMembro($informacaoComissaoMembro);
        return $historicoInformacaoComissaoMembro;
    }

    /**
     * Recupera a lista de alterações realizadas na funcionalidade de salvar comissão membro.
     *
     * @param DocumentoComissaoMembro $documentoComissaoMembro
     * @param HistoricoInformacaoComissaoMembro $historicoInformacaoComissaoMembro
     * @return ArrayCollection
     */
    private function getDadosHistoricoAlteracaoComissaoMembro(
        DocumentoComissaoMembro $documentoComissaoMembro,
        DocumentoComissaoMembroTO $documentoComissaoMembroPreAtualizacao,
        HistoricoInformacaoComissaoMembro $historicoInformacaoComissaoMembro
    )
    {
        if ($historicoInformacaoComissaoMembro->getAcao() == Constants::ACAO_HISTORICO_INFORMACAO_COMISSAO_INSERIR) {
            $historicoAlteracoes = $this->getListaHistoricoAlteracoesIncluir($historicoInformacaoComissaoMembro);
        } else {
            $historicoAlteracoes = $this->getListaHistoricoAlteracoesAlterar(
                $documentoComissaoMembro,
                $documentoComissaoMembroPreAtualizacao,
                $historicoInformacaoComissaoMembro
            );
        }

        return $historicoAlteracoes;
    }

    /**
     * Recupera a lista de inclusões para o histórico de alterações.
     *
     * @param HistoricoInformacaoComissaoMembro $historicoInformacaoComissaoMembro
     * @return ArrayCollection
     */
    private function getListaHistoricoAlteracoesIncluir(
        HistoricoInformacaoComissaoMembro $historicoInformacaoComissaoMembro
    ) {
        $historicoAlteracoes = new ArrayCollection();

        $histInserirCampoCabecalho = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
        $histInserirCampoCabecalho->setDescricao(Constants::INCLUSAO_CAMPO_PARAMETRIZAR_CABECALHO);
        $histInserirCampoCabecalho->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);

        $histInserirCampoTextoInicial = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
        $histInserirCampoTextoInicial->setDescricao(Constants::INCLUSAO_CAMPO_PARAMETRIZAR_TEXTO_INICIAL);
        $histInserirCampoTextoInicial->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);

        $histInserirCampoTextoFinal = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
        $histInserirCampoTextoFinal->setDescricao(Constants::INCLUSAO_CAMPO_PARAMETRIZAR_TEXTO_FINAL);
        $histInserirCampoTextoFinal->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);

        $histInserirCampoRodape = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
        $histInserirCampoRodape->setDescricao(Constants::INCLUSAO_CAMPO_PARAMETRIZAR_TEXTO_RODAPE);
        $histInserirCampoRodape->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);

        $histInserirCampoAtivarCabecalho = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
        $histInserirCampoAtivarCabecalho->setDescricao(Constants::INCLUSAO_CAMPO_ATIVAR_CABECALHO);
        $histInserirCampoAtivarCabecalho->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);

        $histInserirCampoAtivarTextoInicial = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
        $histInserirCampoAtivarTextoInicial->setDescricao(Constants::INCLUSAO_CAMPO_ATIVAR_TEXTO_INICIAL);
        $histInserirCampoAtivarTextoInicial->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);

        $histInserirCampoAtivarTextoFinal = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
        $histInserirCampoAtivarTextoFinal->setDescricao(Constants::INCLUSAO_CAMPO_ATIVAR_TEXTO_FINAL);
        $histInserirCampoAtivarTextoFinal->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);

        $histInserirCampoAtivarRodape = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
        $histInserirCampoAtivarRodape->setDescricao(Constants::INCLUSAO_CAMPO_ATIVAR_TEXTO_RODAPE);
        $histInserirCampoAtivarRodape->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);

        $historicoAlteracoes->add($histInserirCampoCabecalho);
        $historicoAlteracoes->add($histInserirCampoTextoInicial);
        $historicoAlteracoes->add($histInserirCampoTextoFinal);
        $historicoAlteracoes->add($histInserirCampoRodape);
        $historicoAlteracoes->add($histInserirCampoAtivarCabecalho);
        $historicoAlteracoes->add($histInserirCampoAtivarTextoInicial);
        $historicoAlteracoes->add($histInserirCampoAtivarTextoFinal);
        $historicoAlteracoes->add($histInserirCampoAtivarRodape);

        return $historicoAlteracoes;
    }

    /**
     * Recupera a lista de alterações para o histórico de alterações.
     *
     * @param DocumentoComissaoMembro $documentoComissaoMembro
     * @param DocumentoComissaoMembro $documentoComissaoMembroPreAtualizacao
     * @param HistoricoInformacaoComissaoMembro $historicoInformacaoComissaoMembro
     * @return ArrayCollection
     */
    private function getListaHistoricoAlteracoesAlterar(
        DocumentoComissaoMembro $documentoComissaoMembro,
        DocumentoComissaoMembroTO $documentoComissaoMembroPreAtualizacao,
        HistoricoInformacaoComissaoMembro $historicoInformacaoComissaoMembro
    ) {
        $historicoAlteracoes = new ArrayCollection();

        if ($documentoComissaoMembro->getDescricaoCabecalho()
            != $documentoComissaoMembroPreAtualizacao->getDescricaoCabecalho()) {
            $histAlterarCampoCabecalho = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $histAlterarCampoCabecalho->setDescricao(Constants::ALTERACAO_CAMPO_PARAMETRIZAR_CABECALHO);
            $histAlterarCampoCabecalho->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);
            $historicoAlteracoes->add($histAlterarCampoCabecalho);
        }

        if ($documentoComissaoMembro->getDescricaoTextoInicial()
            != $documentoComissaoMembroPreAtualizacao->getDescricaoTextoInicial()) {
            $histAlterarCampoTextoInicial = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $histAlterarCampoTextoInicial->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);
            $histAlterarCampoTextoInicial->setDescricao(
                Constants::ALTERACAO_CAMPO_PARAMETRIZAR_TEXTO_INICIAL
            );
            $historicoAlteracoes->add($histAlterarCampoTextoInicial);
        }

        if ($documentoComissaoMembro->getDescricaoTextoFinal()
            != $documentoComissaoMembroPreAtualizacao->getDescricaoTextoFinal()) {
            $histAlterarCampoTextoFinal = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $histAlterarCampoTextoFinal->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);
            $histAlterarCampoTextoFinal->setDescricao(Constants::ALTERACAO_CAMPO_PARAMETRIZAR_TEXTO_FINAL);
            $historicoAlteracoes->add($histAlterarCampoTextoFinal);
        }

        if ($documentoComissaoMembro->getDescricaoTextoRodape()
            != $documentoComissaoMembroPreAtualizacao->getDescricaoTextoRodape()) {
            $histAlterarCampoRodape = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $histAlterarCampoRodape->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);
            $histAlterarCampoRodape->setDescricao(Constants::ALTERACAO_CAMPO_PARAMETRIZAR_TEXTO_RODAPE);
            $historicoAlteracoes->add($histAlterarCampoRodape);
        }

        if ($documentoComissaoMembro->isSituacaoCabecalhoAtivo()
            != $documentoComissaoMembroPreAtualizacao->isSituacaoCabecalhoAtivo()) {

            $histAlterarCampoAtivoCabecalho = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $histAlterarCampoAtivoCabecalho->setDescricao(Constants::ALTERACAO_CAMPO_ATIVAR_CABECALHO);
            $histAlterarCampoAtivoCabecalho->setHistoricoInformacaoComissaoMembro(
                $historicoInformacaoComissaoMembro
            );

            $historicoAlteracoes->add($histAlterarCampoAtivoCabecalho);
        }

        if ($documentoComissaoMembro->isSituacaoTextoInicial()
            != $documentoComissaoMembroPreAtualizacao->isSituacaoCabecalhoAtivo()) {

            $histAlterarCampoAtivoTextoInicial = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $histAlterarCampoAtivoTextoInicial->setHistoricoInformacaoComissaoMembro(
                $historicoInformacaoComissaoMembro
            );

            $histAlterarCampoAtivoTextoInicial->setDescricao(
                Constants::ALTERACAO_CAMPO_ATIVAR_TEXTO_INICIAL
            );

            $historicoAlteracoes->add($histAlterarCampoAtivoTextoInicial);
        }

        if ($documentoComissaoMembro->isSituacaoTextoFinal()
            != $documentoComissaoMembroPreAtualizacao->isSituacaoTextoFinal()) {

            $histAlterarCampoAtivoTextoFinal = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $histAlterarCampoAtivoTextoFinal->setDescricao(Constants::ALTERACAO_CAMPO_ATIVAR_TEXTO_FINAL);
            $histAlterarCampoAtivoTextoFinal->setHistoricoInformacaoComissaoMembro(
                $historicoInformacaoComissaoMembro
            );

            $historicoAlteracoes->add($histAlterarCampoAtivoTextoFinal);
        }

        if ($documentoComissaoMembro->isSituacaoTextoRodape()
            != $documentoComissaoMembroPreAtualizacao->isSituacaoTextoRodape()) {
            $histAlterarCampoAtivoRodape = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $histAlterarCampoAtivoRodape->setDescricao(Constants::ALTERACAO_CAMPO_ATIVAR_TEXTO_RODAPE);
            $histAlterarCampoAtivoRodape->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);
            $historicoAlteracoes->add($histAlterarCampoAtivoRodape);
        }

        return $historicoAlteracoes;
    }

}
