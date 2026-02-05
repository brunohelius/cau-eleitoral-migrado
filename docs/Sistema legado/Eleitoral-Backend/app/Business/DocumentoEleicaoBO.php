<?php
/*
 * DocumentoEleicaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\DocumentoEleicao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\DocumentoEleicaoRepository;
use App\Service\ArquivoService;
use App\To\ArquivoTO;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'DocumentoEleicao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DocumentoEleicaoBO extends AbstractBO
{

    /**
     * @var DocumentoEleicaoRepository
     */
    private $documentoEleicaoRepository;

    /**
     * @var \App\Service\ArquivoService
     */
    private $arquivoService;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->documentoEleicaoRepository = $this->getRepository(DocumentoEleicao::class);
    }

    /**
     * Retorna uma lista de instâncias de 'DocumentoEleicao' conforme o id do calendário informado.
     *
     * @param integer $idCalendario
     * @return array|null
     * @throws NegocioException
     */
    public function getDocumentosEleicaoPorCalendario($idCalendario)
    {
        if ($idCalendario == null) {
            throw new NegocioException(Message::VALIDACAO_FILTRO_OBRIGATORIO);
        }
        return $this->documentoEleicaoRepository->getDocumentosEleicaoPorCalendario($idCalendario);
    }

    /**
     * Salva os dados do 'DocumentoEleicao' na base de dados.
     *
     * @param DocumentoEleicao $documentoEleicao
     * @return \App\Entities\Entity|array|bool|\Doctrine\Common\Persistence\ObjectManagerAware|object
     * @throws Exception
     * @throws NegocioException
     */
    public function salvar(DocumentoEleicao $documentoEleicao)
    {
        try {
            $this->beginTransaction();
            $this->validarCamposObrigatorios($documentoEleicao);
            $extensoesValidas = [Constants::EXTENCAO_PDF, Constants::EXTENCAO_DOC, Constants::EXTENCAO_DOCX];
            $this->getArquivoService()->validarExtensaoArquivo($documentoEleicao->getNomeArquivo(), $extensoesValidas);
            $this->getArquivoService()->validarTamanhoArquivo($documentoEleicao->getTamanho(),
                Constants::TAMANHO_LIMITE_ARQUIVO, Message::MSG_DOCUMENTO_ELEICAO_LIMITE_TAMANHO);

            if ($documentoEleicao->getSequencial() == null) {
                $documentoEleicao->setSequencial(
                    $this->documentoEleicaoRepository->getUltimaSequenciaPorEleicao($documentoEleicao->getEleicao()->getId()) + 1
                );
            }

            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $documentoEleicao->getNomeArquivo(),
                Constants::REFIXO_DOC_ELEICAO
            );
            $documentoEleicao->setNomeArquivoFisico($nomeArquivoFisico);
            $documentoEleicao->setDataPublicacao(Utils::getData());

            $documentoEleicao = $this->documentoEleicaoRepository->persist($documentoEleicao);
            $this->salvarArquivo($documentoEleicao);
            $this->commitTransaction();

            return $documentoEleicao;
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Disponibiliza o arquivo 'Resolução' para 'download' conforme o 'id' informado.
     *
     * @param integer $idDocumentoEleicao
     * @return ArquivoTO
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getArquivo($idDocumentoEleicao)
    {
        $documentoEleicao = $this->getPorId($idDocumentoEleicao);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioEleicao($documentoEleicao->getEleicao()->getId());

        return $this->getArquivoService()->getArquivo($caminho, $documentoEleicao->getNomeArquivoFisico(), $documentoEleicao->getNomeArquivo());
    }

    /**
     * Retorna a eleição conforme o id informado.
     *
     * @param integer $id
     * @return array
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getPorId($id)
    {
        return $this->documentoEleicaoRepository->getPorId($id);
    }

    /**
     * Salva o(s) arquivo(s) da resposta da declaração.
     *
     * @param DocumentoEleicao $documentoEleicao
     */
    private function salvarArquivo(DocumentoEleicao $documentoEleicao)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioEleicao($documentoEleicao->getEleicao()->getId());

        if (!empty($documentoEleicao->getArquivo())) {
            $this->getArquivoService()->salvar($caminho, $documentoEleicao->getNomeArquivoFisico(), $documentoEleicao->getArquivo());
        }
    }

    /**
     * Verifica se os campos obrigatórios do 'DocumentoEleicao' foram preechidos.
     *
     * @param DocumentoEleicao $documentoEleicao
     * @throws NegocioException
     */
    private function validarCamposObrigatorios(DocumentoEleicao $documentoEleicao)
    {
        if ($documentoEleicao->getEleicao() == null || $documentoEleicao->getEleicao()->getId() == null) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS);
        }

        if ($documentoEleicao->isCorporativo() == null && $documentoEleicao->isProfissional() == null &&
            $documentoEleicao->isPublico() == null) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS);
        }

        if (empty($documentoEleicao->getNomeArquivo())) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS);
        }

        if ($documentoEleicao->getIdUsuario() == null || $documentoEleicao->getIdUsuario() == 0) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS);
        }

        if (empty($documentoEleicao->getNomeUsuario())) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS);
        }

        if (empty($documentoEleicao->getArquivo())) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS);
        }

        if ($documentoEleicao->getTamanho() == null || $documentoEleicao->getTamanho() == 0) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Retorna a instância do serviço de gravação de arquivo.
     *
     * @return ArquivoService
     */
    private function getArquivoService()
    {
        if ($this->arquivoService == null) {
            $this->arquivoService = app()->make(ArquivoService::class);
        }
        return $this->arquivoService;
    }
}