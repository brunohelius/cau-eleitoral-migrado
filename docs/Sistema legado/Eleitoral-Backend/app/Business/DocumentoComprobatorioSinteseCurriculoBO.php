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
use App\Entities\DocumentoComprobatorioSinteseCurriculo;
use App\Entities\DocumentoEleicao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\DocumentoComprobatorioSinteseCurriculoRepository;
use App\Repository\DocumentoEleicaoRepository;
use App\Service\ArquivoService;
use App\To\ArquivoTO;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'DocumentoComprobatorioSinteseCurriculo'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DocumentoComprobatorioSinteseCurriculoBO extends AbstractBO
{

    /**
     * @var DocumentoComprobatorioSinteseCurriculoRepository
     */
    private $documentoComprobatorioRepository;

    /**
     * @var \App\Service\ArquivoService
     */
    private $arquivoService;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->documentoComprobatorioRepository = $this->getRepository(
            DocumentoComprobatorioSinteseCurriculo::class
        );
    }

    /**
     * Disponibiliza o arquivo 'Documento Comprobatorio Sintese Currisulo' para 'download' conforme o 'id' informado.
     *
     * @param integer $idDocumento
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoDocumentoComprobatorio($idDocumento)
    {
        $documento = $this->documentoComprobatorioRepository->getPorId($idDocumento);

        if (!empty($documento)) {
            $diretorioSinteseCurriculo = sprintf(
                '%s/%s',
                $documento->getMembroChapa()->getChapaEleicao()->getId(),
                $documento->getMembroChapa()->getId()
            );

            $diretorioDocsComprab = Constants::PATH_STORAGE_DOCS_COMPROBATORIOS_SINTESE_CURRICULO;
            $caminho = $this->getArquivoService()->getCaminhoRepositorioSinteseCurriculo(
                $diretorioSinteseCurriculo . DIRECTORY_SEPARATOR . $diretorioDocsComprab
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $documento->getNomeArquivo(),
                $documento->getNomeArquivo()
            );
        }
    }

    /**
     * Método para remover todas os Documentos associado a um Membro da Chapa Eleção
     *
     * @param integer $idMembroChapa
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function excluirDocumentosPorMembroChapa($idMembroChapa)
    {
        $documentos = $this->documentoComprobatorioRepository->findBy(['membroChapa' => $idMembroChapa]);

        if (!empty($documentos)) {
            $this->documentoComprobatorioRepository->deleteEmLote($documentos);
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