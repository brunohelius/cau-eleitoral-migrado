<?php

namespace App\Entities;

use App\Util\Utils;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Publicação de Documento'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\PublicacaoDocumentoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_PUBLICACAO_DOC")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class PublicacaoDocumento extends Entity
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_PUBLICACAO_DOC", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_hist_comissao_membro_id_seq", initialValue=1, allocationSize=1)
     *
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="NM_ARQUIVO", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $nomeArquivo;

    /**
     * @ORM\Column(name="DT_PUBLICACAO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataPublicacao;

    /**
     * @ORM\Column(name="ID_USUARIO_PUBLICACAO", type="integer")
     *
     * @var integer
     */
    private $responsavel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\DocumentoComissaoMembro")
     * @ORM\JoinColumn(name="ID_DOC_COMISSAO_MEMBRO", referencedColumnName="ID_DOC_COMISSAO_MEMBRO", nullable=false)
     *
     * @var \App\Entities\DocumentoComissaoMembro
     */
    private $documentoComissaoMembro;

    /**
     * Retorna uma nova instância de 'PublicacaoDocumento' .
     *
     * @param null $data
     * @return PublicacaoDocumento
     */
    public static function newInstance($data = null)
    {
        $publicacaoDocumento = new PublicacaoDocumento();

        if ($data != null) {
            $publicacaoDocumento->setId(Utils::getValue('id', $data));
            $publicacaoDocumento->setResponsavel(Utils::getValue('responsavel', $data));
            $publicacaoDocumento->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $publicacaoDocumento->setDataPublicacao(Utils::getValue('dataPublicacao', $data));

            $documentoComissaoMembro = DocumentoComissaoMembro::newInstance(
                Utils::getValue('documentoComissaoMembro', $data)
            );

            $publicacaoDocumento->setDocumentoComissaoMembro($documentoComissaoMembro);
        }

        return $publicacaoDocumento;
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
     * @return int
     */
    public function getResponsavel()
    {
        return $this->responsavel;
    }

    /**
     * @param int $responsavel
     */
    public function setResponsavel($responsavel)
    {
        $this->responsavel = $responsavel;
    }

    /**
     * @return mixed
     */
    public function getDocumentoComissaoMembro()
    {
        return $this->documentoComissaoMembro;
    }

    /**
     * @param mixed $documentoComissaoMembro
     */
    public function setDocumentoComissaoMembro($documentoComissaoMembro)
    {
        $this->documentoComissaoMembro = $documentoComissaoMembro;
    }

}
