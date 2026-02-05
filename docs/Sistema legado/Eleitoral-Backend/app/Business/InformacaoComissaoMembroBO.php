<?php
/*
 * InformacaoComissaoMembroBO.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\DocumentoComissaoMembro;
use App\Entities\Entity;
use App\Entities\HistoricoAlteracaoInformacaoComissaoMembro;
use App\Entities\HistoricoInformacaoComissaoMembro;
use App\Entities\InformacaoComissaoMembro;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\InformacaoComissaoMembroRepository;
use App\To\DefinicaoDeclaracoesEmailsAtivSecundariaTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'InformacaoComissaoMembro'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class InformacaoComissaoMembroBO extends AbstractBO
{
    /**
     * @var InformacaoComissaoMembroRepository
     */
    private $informacaoComissaoMembroRepository;

    /**
     * @var \App\Business\HistoricoInformacaoComissaoMembroBO
     */
    private $historicoInformacaoComissaoMembroBO;

    /**
     * @var \App\Business\HistoricoAlteracaoInformacaoComissaoMembroBO
     */
    private $historicoAlterecaoInformacaoComissaoMembroBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->informacaoComissaoMembroRepository = $this->getRepository(InformacaoComissaoMembro::class);
        $this->historicoInformacaoComissaoMembroBO = app()->make(HistoricoInformacaoComissaoMembroBO::class);
        $this->historicoAlterecaoInformacaoComissaoMembroBO = app()->make(
            HistoricoAlteracaoInformacaoComissaoMembroBO::class
        );
    }

    /**
     * Retorna InformacaoComissaoMembro conforme o id informado.
     *
     * @param $idInformacaoComissaoMembro
     * @return \App\Entities\InformacaoComissaoMembro
     */
    public function getPorId($idInformacaoComissaoMembro)
    {
        return $this->informacaoComissaoMembroRepository->getPorId($idInformacaoComissaoMembro);
    }

    /**
     * Salva uma nova informação de comissão membros no sistema.
     *
     * @param InformacaoComissaoMembro $informacaoComissaoMembro
     * @param
     *
     * @return Entity|array|bool|ObjectManagerAware|object
     * @throws NegocioException
     * @throws Exception
     */
    public function salvar(InformacaoComissaoMembro $informacaoComissaoMembro, $dadosTO)
    {
        $this->validarCamposObrigatorios($informacaoComissaoMembro);

        $coAcaoHistorico = empty($informacaoComissaoMembro->getId()) ?
            Constants::ACAO_HISTORICO_INFORMACAO_COMISSAO_INSERIR
            : Constants::ACAO_HISTORICO_INFORMACAO_COMISSAO_ALTERAR;

        $informacaoComissaoMembroPreAtualizacao = InformacaoComissaoMembro::newInstance();
        if (!empty($informacaoComissaoMembro->getId())) {
            $informacaoComissaoMembroPreAtualizacao = clone $this->getPorId($informacaoComissaoMembro->getId());
            $informacaoComissaoMembro->setSituacaoConcluido(
                $informacaoComissaoMembroPreAtualizacao->getSituacaoConcluido()
            );
        }

        try {

            $this->beginTransaction();

            $informacaoComissaoMembroSalva = $this->informacaoComissaoMembroRepository->persist(
                $informacaoComissaoMembro
            );

            $histInformacaoComissaoMembro = $this->getHistInformacaoComissaoMembro(
                $informacaoComissaoMembro,
                $coAcaoHistorico
            );

            $histInformacaoComissaoMembroSalvo = $this->historicoInformacaoComissaoMembroBO->salvar(
                $histInformacaoComissaoMembro
            );

            $historicoAlteracoes = $this->getDadosHistoricoAlteracaoComissaoMembro(
                $informacaoComissaoMembro,
                $informacaoComissaoMembroPreAtualizacao,
                $histInformacaoComissaoMembroSalvo
            );

            foreach ($historicoAlteracoes as $alteracao) {
                $this->historicoAlterecaoInformacaoComissaoMembroBO->salvar($alteracao);
            }

            $idAtividadeSecundaria = $informacaoComissaoMembro->getAtividadeSecundaria()->getId();

            $this->salvarDefinicaoEmail($dadosTO, $idAtividadeSecundaria);

            $this->commitTransaction();

        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        $informacaoComissaoMembroSalva->setMembrosComissao(null);
        $informacaoComissaoMembroSalva->setAtividadeSecundaria(null);
        return $informacaoComissaoMembroSalva;
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
        $informacaoComissaoMembro = $this->informacaoComissaoMembroRepository->find($documentoComissaoMembro->getInformacaoComissaoMembro()->getId());
        $this->validarCamposConcluir($documentoComissaoMembro);

        try {

            $this->beginTransaction();

            $this->getDocumentoComissaoMembroBO()->salvar($documentoComissaoMembro);

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
        $informacaoComissaoMembroSalva->setDocumentoComissaoMembro(null);
        $informacaoComissaoMembroSalva->setMembrosComissao(null);

        return $this->getPorId($informacaoComissaoMembroSalva->getId());
    }

    /**
     * Recupera a entidade formatada de 'HistoricoInformacaoComissaoMembro'.
     *
     * @param InformacaoComissaoMembro $informacaoComissaoMembro
     * @param $coAcao
     * @return HistoricoInformacaoComissaoMembro
     * @throws Exception
     */
    private function getHistInformacaoComissaoMembro(InformacaoComissaoMembro $informacaoComissaoMembro, $coAcao)
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $histInformacaoComissaoMembro = new HistoricoInformacaoComissaoMembro();
        $histInformacaoComissaoMembro->setAcao($coAcao);
        $histInformacaoComissaoMembro->setDataHistorico(Utils::getData());
        $histInformacaoComissaoMembro->setInformacaoComissaoMembro($informacaoComissaoMembro);
        $histInformacaoComissaoMembro->setResponsavel($usuarioLogado->id);
        return $histInformacaoComissaoMembro;
    }

    /**
     * Recupera a lista de alteração nos campos de informação comissão membro.
     *
     * @param InformacaoComissaoMembro $informacaoComissaoMembro
     * @param InformacaoComissaoMembro $informacaoComissaoMembroPreAtualizacao
     * @param HistoricoInformacaoComissaoMembro $historicoInformacaoComissaoMembro
     * @return ArrayCollection
     */
    private function getDadosHistoricoAlteracaoComissaoMembro(
        InformacaoComissaoMembro $informacaoComissaoMembro,
        InformacaoComissaoMembro $informacaoComissaoMembroPreAtualizacao,
        HistoricoInformacaoComissaoMembro $historicoInformacaoComissaoMembro
    )
    {
        $historicoAlteracoes = new ArrayCollection();

        if ($historicoInformacaoComissaoMembro->getAcao() == Constants::ACAO_HISTORICO_INFORMACAO_COMISSAO_INSERIR) {

            $histInserirCampoMajorietario = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $histInserirCampoMajorietario->setDescricao(Constants::INCLUSAO_CAMPO_MAJORIETARIO);
            $histInserirCampoMajorietario->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);

            $histInserirCampoCoordenadorAdjunto = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $histInserirCampoCoordenadorAdjunto->setDescricao(Constants::INCLUSAO_CAMPO_COORDENADOR_ADJUNTO);
            $histInserirCampoCoordenadorAdjunto->setHistoricoInformacaoComissaoMembro(
                $historicoInformacaoComissaoMembro
            );

            $histInserirCampoQuantidadeMinima = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $histInserirCampoQuantidadeMinima->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);
            $histInserirCampoQuantidadeMinima->setDescricao(
                Constants::INCLUSAO_CAMPO_QUANTIDADE_MINIMA_MEMBROS
            );

            $histInserirCampoQuantidadeMaxima = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
            $histInserirCampoQuantidadeMaxima->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);
            $histInserirCampoQuantidadeMaxima->setDescricao(
                Constants::INCLUSAO_CAMPO_QUANTIDADE_MAXIMA_MEMBROS
            );

            $historicoAlteracoes->add($histInserirCampoMajorietario);
            $historicoAlteracoes->add($histInserirCampoCoordenadorAdjunto);
            $historicoAlteracoes->add($histInserirCampoQuantidadeMinima);
            $historicoAlteracoes->add($histInserirCampoQuantidadeMaxima);

        } else {

            if ($informacaoComissaoMembro->getTipoOpcao() != $informacaoComissaoMembroPreAtualizacao->getTipoOpcao()) {
                $histAlterarCampoTipoAcao = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
                $histAlterarCampoTipoAcao->setDescricao(Constants::ALTERACAO_CAMPO_MAJORIETARIO);
                $histAlterarCampoTipoAcao->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);
                $historicoAlteracoes->add($histAlterarCampoTipoAcao);
            }

            if ($informacaoComissaoMembro->isSituacaoConselheiro() != $informacaoComissaoMembroPreAtualizacao->isSituacaoConselheiro()) {
                $histAlterarCampoConselheiro = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
                $histAlterarCampoConselheiro->setHistoricoInformacaoComissaoMembro($historicoInformacaoComissaoMembro);
                $histAlterarCampoConselheiro->setDescricao(Constants::ALTERACAO_CAMPO_COORDENADOR_ADJUNTO);
                $historicoAlteracoes->add($histAlterarCampoConselheiro);
            }

            if ($informacaoComissaoMembro->getQuantidadeMinima()
                != $informacaoComissaoMembroPreAtualizacao->getQuantidadeMinima()) {

                $histAlterarCampoQuantidadeMinima = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
                $histAlterarCampoQuantidadeMinima->setHistoricoInformacaoComissaoMembro(
                    $historicoInformacaoComissaoMembro
                );

                $histAlterarCampoQuantidadeMinima->setDescricao(
                    Constants::ALTERACAO_CAMPO_QUANTIDADE_MINIMA_MEMBROS
                );

                $historicoAlteracoes->add($histAlterarCampoQuantidadeMinima);
            }

            if ($informacaoComissaoMembro->getQuantidadeMaxima()
                != $informacaoComissaoMembroPreAtualizacao->getQuantidadeMaxima()) {

                $histAlterarCampoQuantidadeMaxima = HistoricoAlteracaoInformacaoComissaoMembro::newInstance();
                $histAlterarCampoQuantidadeMaxima->setHistoricoInformacaoComissaoMembro(
                    $historicoInformacaoComissaoMembro
                );

                $histAlterarCampoQuantidadeMaxima->setDescricao(
                    Constants::ALTERACAO_CAMPO_QUANTIDADE_MAXIMA_MEMBROS
                );

                $historicoAlteracoes->add($histAlterarCampoQuantidadeMaxima);
            }
        }

        return $historicoAlteracoes;
    }

    /**
     * Retorna a Informacao de Comissao conforme o Id do calendário informado.
     *
     * @param $idCalendario
     * @return InformacaoComissaoMembro
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function getPorCalendario($idCalendario)
    {
        $informacao = $this->informacaoComissaoMembroRepository->getPorCalendario($idCalendario);

        if (!empty($informacao)) {
            $informacao = InformacaoComissaoMembro::newInstance($informacao);
            $informacao->getAtividadeSecundaria()->getAtividadePrincipalCalendario()->getCalendario()->definirEleicao();
        } else {
            throw new NegocioException(Message::MSG_NAO_FOI_DENIFIDO_PARAMETRO);
        }

        return $informacao;
    }

    /**
     * Válida o preenchimento dos campos obrigatórios.
     *
     * @param InformacaoComissaoMembro $informacaoComissaoMembro
     * @throws NegocioException
     */
    private function validarCamposObrigatorios(InformacaoComissaoMembro $informacaoComissaoMembro)
    {
        $campos = [];

        if (empty($informacaoComissaoMembro->getAtividadeSecundaria()->getId())) {
            $campos[] = 'LABEL_ATIVIDADE_SECUNDARIA';
        }

        if (empty($informacaoComissaoMembro->getTipoOpcao())) {
            $campos[] = 'LABEL_TIPO_OPCAO';
        }

        if (empty($informacaoComissaoMembro->getQuantidadeMinima())) {
            $campos[] = 'LABEL_QUANTIDADE_MINIMA';
        }

        if (empty($informacaoComissaoMembro->getQuantidadeMaxima())) {
            $campos[] = 'LABEL_QUANTIDADE_MAXIMA';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Válida se os campos obrigatórios para a conclusão foram preenchidos.
     *
     * @param DocumentoComissaoMembro $documentoComissaoMembro
     * @throws NegocioException
     */
    private function validarCamposConcluir(DocumentoComissaoMembro $documentoComissaoMembro)
    {
        $campos = [];

        if (empty($documentoComissaoMembro->getInformacaoComissaoMembro()->getId())) {
            $campos[] = 'LABEL_INFORMACAO_NAO_CADASTRADA';
        }

        if (empty($documentoComissaoMembro->getId())) {
            $campos[] = 'LABEL_DOCUMENTO_NAO_CADASTRADO';
        }

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
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, $campos, true);
        }
    }

    /**
     * Método auxiliar do método de salvar para salvar definição de e-mail
     *
     * @param $dadosTO
     * @param int $idAtividadeSecundaria
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function salvarDefinicaoEmail($dadosTO, int $idAtividadeSecundaria): void
    {
        if (!empty($dadosTO->email)) {
            $email = [
                'idEmailAtividadeSecundaria' => $dadosTO->email['id'],
                'idTipoEmailAtividadeSecundaria' => Constants::EMAIL_DEFINIR_NUMERO_MEMBROS
            ];

            $configuracaoDeclaracoesEmailsTO = DefinicaoDeclaracoesEmailsAtivSecundariaTO::newInstance([
                'idAtividadeSecundaria' => $idAtividadeSecundaria,
                'emails' => [$email]
            ]);

            $this->getAtividadeSecundariaCalendarioBO()->definirDeclaracoesEmailsPorAtividadeSecundaria(
                $configuracaoDeclaracoesEmailsTO
            );
        }
    }

    /**
     * Retorna uma nova instância de 'DocumentoComissaoMembroBO'.
     *
     * @return \App\Business\DocumentoComissaoMembroBO
     */
    private function getDocumentoComissaoMembroBO()
    {
        return app()->make(DocumentoComissaoMembroBO::class);
    }

    /**
     * Retorna uma nova instância de 'AtividadeSecundariaCalendarioBO'.
     *
     * @return AtividadeSecundariaCalendarioBO|mixed
     */
    private function getAtividadeSecundariaCalendarioBO()
    {
        if (empty($this->atividadeSecundariaCalendarioBO)) {
            $this->atividadeSecundariaCalendarioBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }

        return $this->atividadeSecundariaCalendarioBO;
    }}
