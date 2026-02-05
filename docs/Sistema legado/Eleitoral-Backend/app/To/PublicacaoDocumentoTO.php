<?php

namespace App\To;


use App\Exceptions\NegocioException;
use App\Service\CorporativoService;
use App\Util\Utils;
use DateTime;
use stdClass;

class PublicacaoDocumentoTO
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nomeArquivo;

    /**
     * @var DateTime
     */
    private $dataPublicacao;

    /**
     * @var stdClass
     */
    private $responsavel;

    /**
     * @var DocumentoComissaoMembroTO
     */
    private $documentoComissaoMembro;

    /**
     * Retorna uma nova instância de 'PublicacaoDocumentoTO'.
     *
     * @param null $data
     * @return PublicacaoDocumentoTO
     * @throws NegocioException
     */
    public static function newInstance($data = null)
    {
        $publicacaoDocumentoTO = new PublicacaoDocumentoTO();

        if ($publicacaoDocumentoTO != null) {
            $publicacaoDocumentoTO->setId(Utils::getValue('id', $data));
            $publicacaoDocumentoTO->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $publicacaoDocumentoTO->setDataPublicacao(Utils::getValue('dataPublicacao', $data));

            $documentoComissaoMembro = DocumentoComissaoMembroTO::newInstance(
                Utils::getValue('documentoComissaoMembro', $data)
            );
            $publicacaoDocumentoTO->setDocumentoComissaoMembro($documentoComissaoMembro);

            if (!empty(Utils::getValue('responsavel', $data))) {
                $corporativoService = new CorporativoService();
                $responsavel = $corporativoService->getUsuarioPorId(Utils::getValue('responsavel', $data));
                $publicacaoDocumentoTO->setResponsavel($responsavel);
            }
        }

        return $publicacaoDocumentoTO;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNomeArquivo()
    {
        return $this->nomeArquivo;
    }

    /**
     * @param string $nomeArquivo
     */
    public function setNomeArquivo($nomeArquivo)
    {
        $this->nomeArquivo = $nomeArquivo;
    }

    /**
     * @return mixed
     */
    public function getDataPublicacao()
    {
        return $this->dataPublicacao;
    }

    /**
     * @param mixed $dataPublicacao
     */
    public function setDataPublicacao($dataPublicacao)
    {
        $this->dataPublicacao = $dataPublicacao;
    }

    /**
     * @return mixed
     */
    public function getResponsavel()
    {
        return $this->responsavel;
    }

    /**
     * @param mixed $responsavel
     */
    public function setResponsavel($responsavel)
    {
        $this->responsavel = $responsavel;
    }

    /**
     * @return DocumentoComissaoMembroTO
     */
    public function getDocumentoComissaoMembro()
    {
        return $this->documentoComissaoMembro;
    }

    /**
     * @param DocumentoComissaoMembroTO $documentoComissaoMembro
     */
    public function setDocumentoComissaoMembro($documentoComissaoMembro)
    {
        $this->documentoComissaoMembro = $documentoComissaoMembro;
    }
}
