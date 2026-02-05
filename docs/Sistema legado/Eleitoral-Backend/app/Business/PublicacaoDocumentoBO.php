<?php
/*
 * PublicacaoDocumentoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Exceptions\NegocioException;
use App\To\ArquivoTO;
use App\Util\Utils;
use App\Config\Constants;
use App\Factory\DocFactory;
use App\Factory\PDFFActory;
use App\Service\ArquivoService;
use App\To\PublicacaoDocumentoTO;
use App\Entities\PublicacaoDocumento;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\PublicacaoDocumentoRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Mpdf\MpdfException;
use PhpOffice\PhpWord\Exception\Exception;
use stdClass;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'PublicacaoDocumento'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class PublicacaoDocumentoBO extends AbstractBO
{

    /**
     * @var PDFFActory
     */
    private $pdfFactory;

    /**
     * @var DocFactory
     */
    private $docFactory;

    /**
     * @var CalendarioBO
     */
    private $calendarioBO;

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var DocumentoComissaoMembroBO
     */
    private $documentoComissaoMembroBO;

    /**
     * @var PublicacaoDocumentoRepository
     */
    private $publicacaoDocumentoRepository;

    /**
     * Recupera a publicação de documento de acordo com o id informado.
     *
     * @param $id
     * @return PublicacaoDocumentoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getPorId($id)
    {
        return $this->getPublicacaoDocumentoRepository()->getPorId($id);
    }

    /**
     * Salva uma nova publicação de documento de comissão membro.
     *
     * @param PublicacaoDocumento $publicacaoDocumento
     *
     * @return mixed
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws Exception
     * @throws \Exception
     */
    public function salvar(PublicacaoDocumento $publicacaoDocumento)
    {
        $resposavel = $this->getUsuarioFactory()->getUsuarioLogado();
        $publicacaoDocumento->setResponsavel($resposavel->id);
        $publicacaoDocumento->setDataPublicacao(Utils::getData());
        $publicacaoDocumento->setNomeArquivo($this->getNomeArquivo($publicacaoDocumento));
        $publicacaoDocumentoSalvo = $this->getPublicacaoDocumentoRepository()->persist($publicacaoDocumento);

        $this->salvarDocumentoPdf($publicacaoDocumento);
        $this->salvarDocumentoDocx($publicacaoDocumento);

        return $this->getPorId($publicacaoDocumentoSalvo->getId());
    }

    /**
     * Gera o documento PDF de acordo com o documento da comissão informado.
     *
     * @param $idDocumentoComissao
     * @param $caminho
     * @param $nomeDocumento
     *
     * @return ArquivoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws MpdfException
     */
    public function gerarPdf($idDocumentoComissao, $caminho = null, $nomeDocumento = null)
    {
        $documentoComissaoMembro = $this->getDocumentoComissaoMembroBO()->getPorId($idDocumentoComissao);
        $numeroMembrosComissao = $this->getCalendarioBO()->getAgrupamentoNumeroMembros(
            $documentoComissaoMembro->getInformacaoComissaoMembro()
                ->getAtividadeSecundaria()
                ->getAtividadePrincipal()
                ->getCalendario()
                ->getId()
        );

        $numeroMembrosComissaoDocumento = new ArrayCollection();
        $numeroMembrosComissaoDocumento->add($this->getNumeroMembrosComissaoTabela($numeroMembrosComissao, 1));
        $numeroMembrosComissaoDocumento->add($this->getNumeroMembrosComissaoTabela($numeroMembrosComissao, 2));
        $numeroMembrosComissaoDocumento->add($this->getNumeroMembrosComissaoTabela($numeroMembrosComissao, 3));
        $numeroMembrosComissaoDocumento->add($this->getNumeroMembrosComissaoTabela($numeroMembrosComissao, 4));

        return $this->getPdfFactory()->gerarDocumentoPublicacaoComissaoMembro(
            $documentoComissaoMembro,
            $numeroMembrosComissaoDocumento,
            $caminho,
            $nomeDocumento
        );
    }

    /**
     * Gera o documento do documento da comissão.
     *
     * @param $idDocumentoComissao
     * @param $caminho
     * @param $nomeArquivo
     * @return ArquivoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function gerarDocumento($idDocumentoComissao, $caminho = null, $nomeArquivo = null)
    {
        $documentoComissaoMembro = $this->getDocumentoComissaoMembroBO()->getPorId($idDocumentoComissao);

        $numeroMembrosComissao = $this->getCalendarioBO()->getAgrupamentoNumeroMembros(
            $documentoComissaoMembro->getInformacaoComissaoMembro()
                ->getAtividadeSecundaria()
                ->getAtividadePrincipal()
                ->getCalendario()
                ->getId()
        );

        $numeroMembrosComissaoDocumento = new ArrayCollection();
        $numeroMembrosComissaoDocumento->add($this->getNumeroMembrosComissaoTabela($numeroMembrosComissao, 1));
        $numeroMembrosComissaoDocumento->add($this->getNumeroMembrosComissaoTabela($numeroMembrosComissao, 2));
        $numeroMembrosComissaoDocumento->add($this->getNumeroMembrosComissaoTabela($numeroMembrosComissao, 3));
        $numeroMembrosComissaoDocumento->add($this->getNumeroMembrosComissaoTabela($numeroMembrosComissao, 4));

        return $this->getDocumentoFactory()->gerarDocumentoPublicacaoComissaoEleitoral(
            $documentoComissaoMembro,
            $numeroMembrosComissaoDocumento,
            $caminho,
            $nomeArquivo
        );
    }

    /**
     * Realiza o download do pdf vinculado ao 'id' de publicação informado.
     *
     * @param $id
     * @return ArquivoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function downloadPdf($id)
    {
        $publicacaoDocumento = $this->getPorId($id);

        $caminho = $this->getArquivoService()->getCaminhoRepositorioPublicacaoComissaoMembro(
            $publicacaoDocumento->getDocumentoComissaoMembro()->getId()
        );

        return $this->arquivoService->getArquivo(
            $caminho,
            $publicacaoDocumento->getNomeArquivo() . '.pdf',
            $publicacaoDocumento->getNomeArquivo() . '.pdf'
        );
    }

    /**
     * Salva o documento fisicamente.
     *
     * @param PublicacaoDocumento $publicacaoDocumento
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws MpdfException
     */
    private function salvarDocumentoPdf(PublicacaoDocumento $publicacaoDocumento)
    {
        $caminho = getenv('SICCAU_STORAGE_PATH') . DIRECTORY_SEPARATOR;
        $caminho .= Constants::PATH_STORAGE_DOCUMENTO_PUBLICACAO_COMISSAO_MEMBRO . DIRECTORY_SEPARATOR;
        $caminho .= $publicacaoDocumento->getDocumentoComissaoMembro()->getId() . DIRECTORY_SEPARATOR;

        if (!file_exists($caminho)) {
            mkdir($caminho, 0777, true);
        }

        $caminho .= $publicacaoDocumento->getNomeArquivo() . '.pdf';

        $this->gerarPdf(
            $publicacaoDocumento->getDocumentoComissaoMembro()->getId(),
            $caminho,
            $publicacaoDocumento->getNomeArquivo()
        );
    }

    /**
     * Salva o documento no formato de '.DOCX'.
     *
     * @param PublicacaoDocumento $publicacaoDocumento
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws Exception
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    private function salvarDocumentoDocx(PublicacaoDocumento $publicacaoDocumento)
    {
        $caminho = getenv('SICCAU_STORAGE_PATH') . DIRECTORY_SEPARATOR;
        $caminho .= Constants::PATH_STORAGE_DOCUMENTO_PUBLICACAO_COMISSAO_MEMBRO . DIRECTORY_SEPARATOR;
        $caminho .= $publicacaoDocumento->getDocumentoComissaoMembro()->getId() . DIRECTORY_SEPARATOR;

        if (!file_exists($caminho)) {
            mkdir($caminho, 0777, true);
        }

        $caminho .= $publicacaoDocumento->getNomeArquivo() . '.docx';

        $this->gerarDocumento(
            $publicacaoDocumento->getDocumentoComissaoMembro()->getId(),
            $caminho,
            $publicacaoDocumento->getNomeArquivo()
        );
    }

    /**
     * Recupera o nome do arquivo.
     *
     * @param PublicacaoDocumento $publicacaoDocumento
     * @return string
     * @throws NonUniqueResultException
     */
    private function getNomeArquivo(PublicacaoDocumento $publicacaoDocumento)
    {
        $documentoComissao = $this->getDocumentoComissaoMembroBO()->getPorId(
            $publicacaoDocumento->getDocumentoComissaoMembro()->getId()
        );

        $totalPublicacoesComissao = $this->getPublicacaoDocumentoRepository()->getTotalPublicacoesComissaoMembro(
            $publicacaoDocumento->getDocumentoComissaoMembro()->getId()
        );

        $eleicao = $documentoComissao->getInformacaoComissaoMembro()
                ->getAtividadeSecundaria()
                ->getAtividadePrincipal()
                ->getCalendario()
                ->getEleicao()
                ->getAno();

        $eleicao .= '_';

        $eleicao .= str_pad($documentoComissao->getInformacaoComissaoMembro()
                ->getAtividadeSecundaria()
                ->getAtividadePrincipal()
                ->getCalendario()
                ->getEleicao()
                ->getSequenciaAno(), 2, 0, STR_PAD_LEFT);

        $eleicao .= '_';

        $sequencial = str_pad($totalPublicacoesComissao + 1, 3, 0, STR_PAD_LEFT);
        return $eleicao . Constants::PREFIXO_DOCUMENTO_PUBLICACAO_COMISSAO_MEMBRO . $sequencial;
    }

    /**
     * Recupera a quantidade de membros de comissão membros para cada tabela da exportação.
     *
     * @param $numeroMembrosComissao
     * @param $nuTabela
     * @param int $totalTabelas
     * @return ArrayCollection
     */
    private function getNumeroMembrosComissaoTabela($numeroMembrosComissao, $nuTabela, $totalTabelas = 4)
    {
        $numeroMembrosComissaoArr = new ArrayCollection();
        $totalNumeroMembro = count($numeroMembrosComissao);

        $limiteInferior = ceil($totalNumeroMembro / $totalTabelas) * ($nuTabela - 1);
        $limiteSuperior = ceil($totalNumeroMembro / $totalTabelas) * ($nuTabela);

        if ($limiteInferior == 0 && $nuTabela != 1) {
            $limiteInferior = $limiteInferior == 0 ? (1 * ($nuTabela - 1)) : $limiteInferior;
        }

        $limiteSuperior = $limiteSuperior == 0 ? (1 * $nuTabela) : $limiteSuperior;

        foreach ($numeroMembrosComissao as $index => $numeroMembroComissao) {
            if ($index >= $limiteInferior && $index < $limiteSuperior) {
                $numeroMembrosComissaoArr->add($numeroMembroComissao);
            }
        }

        return $numeroMembrosComissaoArr;
    }

    /**
     * Retorna uma nova instância de 'PublicacaoDocumentoRepository'.
     *
     * @return \App\Repository\PublicacaoDocumentoRepository
     */
    private function getPublicacaoDocumentoRepository()
    {
        if (empty($this->publicacaoDocumentoRepository)) {
            $this->publicacaoDocumentoRepository = $this->getRepository(PublicacaoDocumento::class);
        }

        return $this->publicacaoDocumentoRepository;
    }

    /**
     * Retorna uma nova instância de 'DocumentoComissaoMembroBO'.
     *
     * @return DocumentoComissaoMembroBO|EntityRepository
     */
    private function getDocumentoComissaoMembroBO()
    {
        if (empty($this->documentoComissaoMembroBO)) {
            $this->documentoComissaoMembroBO = app()->make(DocumentoComissaoMembroBO::class);
        }

        return $this->documentoComissaoMembroBO;
    }

    /**
     * Retorna uma instância de 'CalendárioBO'.
     *
     * @return CalendarioBO|mixed
     */
    private function getCalendarioBO()
    {
        if (empty($this->calendarioBO)) {
            $this->calendarioBO = app()->make(CalendarioBO::class);
        }

        return $this->calendarioBO;
    }

    /**
     * Retorna a instância de PDFFactory conforme o padrão Lazy Initialization.
     *
     * @return PDFFActory
     */
    private function getPdfFactory()
    {
        if ($this->pdfFactory == null) {
            $this->pdfFactory = app()->make(PDFFActory::class);
        }

        return $this->pdfFactory;
    }

    /**
     * Retorna uma nova instância de 'DocFactory'.
     *
     * @return DocFactory|mixed
     */
    private function getDocumentoFactory()
    {
        if ($this->docFactory == null) {
            $this->docFactory = app()->make(DocFactory::class);
        }

        return $this->docFactory;
    }

    /**
     * Retorna uma nova instância de 'ArquivoService'.
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
