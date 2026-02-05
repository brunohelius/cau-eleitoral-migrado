<?php

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Documento de Comissão Membro'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DocumentoComissaoMembroRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DOC_COMISSAO_MEMBRO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DocumentoComissaoMembro extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DOC_COMISSAO_MEMBRO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_doc_comissao_membro_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="ST_CABECALHO_ATIVO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $situacaoCabecalhoAtivo;

    /**
     * @ORM\Column(name="DS_CABECALHO", type="text")
     *
     * @var string
     */
    private $descricaoCabecalho;

    /**
     * @ORM\Column(name="ST_TEXTO_INICIAL", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $situacaoTextoInicial;

    /**
     * @ORM\Column(name="DS_TEXTO_INICIAL", type="text")
     *
     * @var string
     */
    private $descricaoTextoInicial;

    /**
     * @ORM\Column(name="ST_TEXTO_FINAL", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $situacaoTextoFinal;

    /**
     * @ORM\Column(name="DS_TEXTO_FINAL", type="text")
     *
     * @var string
     */
    private $descricaoTextoFinal;

    /**
     * @ORM\Column(name="ST_TEXTO_RODAPE", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $situacaoTextoRodape;

    /**
     * @ORM\Column(name="DS_TEXTO_RODAPE", type="text")
     *
     * @var string
     */
    private $descricaoTextoRodape;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\InformacaoComissaoMembro")
     * @ORM\JoinColumn(name="ID_INF_COMISSAO_MEMBRO", referencedColumnName="ID_INF_COMISSAO_MEMBRO", nullable=false)
     *
     * @var \App\Entities\InformacaoComissaoMembro
     */
    private $informacaoComissaoMembro;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\PublicacaoDocumento", mappedBy="documentoComissaoMembro")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $publicacoesDocumento;

    /**
     * Fábrica de instância de Documento Comissão Membro'.
     *
     * @param array $data
     * @return DocumentoComissaoMembro
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $documentoComissaoMembro = new DocumentoComissaoMembro();

        if ($data != null) {

            $informacaoComissaoMembro = InformacaoComissaoMembro::newInstance(
                Utils::getValue('informacaoComissaoMembro', $data)
            );

            $documentoComissaoMembro->setId(Utils::getValue('id', $data));
            $documentoComissaoMembro->setInformacaoComissaoMembro($informacaoComissaoMembro);
            $documentoComissaoMembro->setDescricaoCabecalho(Utils::getValue('descricaoCabecalho', $data));
            $documentoComissaoMembro->setDescricaoTextoFinal(Utils::getValue('descricaoTextoFinal', $data));
            $documentoComissaoMembro->setDescricaoTextoRodape(Utils::getValue('descricaoTextoRodape', $data));
            $documentoComissaoMembro->setDescricaoTextoInicial(Utils::getValue('descricaoTextoInicial', $data));
            $documentoComissaoMembro->setSituacaoTextoFinal(Utils::getBooleanValue('situacaoTextoFinal', $data));

            $documentoComissaoMembro->setSituacaoTextoRodape(
                Utils::getBooleanValue('situacaoTextoRodape', $data)
            );

            $documentoComissaoMembro->setSituacaoTextoInicial(
                Utils::getBooleanValue('situacaoTextoInicial', $data)
            );

            $documentoComissaoMembro->setSituacaoCabecalhoAtivo(
                Utils::getBooleanValue('situacaoCabecalhoAtivo', $data)
            );
        }

        return $documentoComissaoMembro;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isSituacaoCabecalhoAtivo()
    {
        return $this->situacaoCabecalhoAtivo;
    }

    /**
     * @param bool $situacaoCabecalhoAtivo
     */
    public function setSituacaoCabecalhoAtivo($situacaoCabecalhoAtivo): void
    {
        $this->situacaoCabecalhoAtivo = $situacaoCabecalhoAtivo;
    }

    /**
     * @return string
     */
    public function getDescricaoCabecalho()
    {
        return $this->descricaoCabecalho;
    }

    /**
     * @param string $descricaoCabecalho
     */
    public function setDescricaoCabecalho($descricaoCabecalho): void
    {
        $this->descricaoCabecalho = $descricaoCabecalho;
    }

    /**
     * @return bool
     */
    public function isSituacaoTextoInicial()
    {
        return $this->situacaoTextoInicial;
    }

    /**
     * @param bool $situacaoTextoInicial
     */
    public function setSituacaoTextoInicial($situacaoTextoInicial): void
    {
        $this->situacaoTextoInicial = $situacaoTextoInicial;
    }

    /**
     * @return string
     */
    public function getDescricaoTextoInicial()
    {
        return $this->descricaoTextoInicial;
    }

    /**
     * @param string $descricaoTextoInicial
     */
    public function setDescricaoTextoInicial($descricaoTextoInicial): void
    {
        $this->descricaoTextoInicial = $descricaoTextoInicial;
    }

    /**
     * @return bool
     */
    public function isSituacaoTextoFinal()
    {
        return $this->situacaoTextoFinal;
    }

    /**
     * @param bool $situacaoTextoFinal
     */
    public function setSituacaoTextoFinal($situacaoTextoFinal): void
    {
        $this->situacaoTextoFinal = $situacaoTextoFinal;
    }

    /**
     * @return string
     */
    public function getDescricaoTextoFinal()
    {
        return $this->descricaoTextoFinal;
    }

    /**
     * @param string $descricaoTextoFinal
     */
    public function setDescricaoTextoFinal($descricaoTextoFinal): void
    {
        $this->descricaoTextoFinal = $descricaoTextoFinal;
    }

    /**
     * @return bool
     */
    public function isSituacaoTextoRodape()
    {
        return $this->situacaoTextoRodape;
    }

    /**
     * @param bool $situacaoTextoRodape
     */
    public function setSituacaoTextoRodape($situacaoTextoRodape): void
    {
        $this->situacaoTextoRodape = $situacaoTextoRodape;
    }

    /**
     * @return string
     */
    public function getDescricaoTextoRodape()
    {
        return $this->descricaoTextoRodape;
    }

    /**
     * @param string $descricaoTextoRodape
     */
    public function setDescricaoTextoRodape($descricaoTextoRodape): void
    {
        $this->descricaoTextoRodape = $descricaoTextoRodape;
    }

    /**
     * @return InformacaoComissaoMembro
     */
    public function getInformacaoComissaoMembro()
    {
        return $this->informacaoComissaoMembro;
    }

    /**
     * @param InformacaoComissaoMembro $informacaoComissaoMembro
     */
    public function setInformacaoComissaoMembro($informacaoComissaoMembro): void
    {
        $this->informacaoComissaoMembro = $informacaoComissaoMembro;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getPublicacoesDocumento()
    {
        return $this->publicacoesDocumento;
    }

    /**
     * @param array|ArrayCollection $publicacoesDocumento
     */
    public function setPublicacoesDocumento($publicacoesDocumento)
    {
        $this->publicacoesDocumento = $publicacoesDocumento;
    }

    /**
     * Substitui os links criados para imagens pelo CKEditor e coloca em formato de imagem para as exportações
     */
    public function substituiLinkImagem()
    {
        $tags[] = 'a'; 
        $this->descricaoCabecalho = Utils::removerHtmlTags($this->descricaoCabecalho, $tags);
        $this->descricaoTextoInicial = Utils::removerHtmlTags($this->descricaoTextoInicial, $tags);
        $this->descricaoTextoFinal =  Utils::removerHtmlTags($this->descricaoTextoFinal, $tags);
        $this->descricaoTextoRodape = Utils::removerHtmlTags($this->descricaoTextoRodape, $tags);
    }
}
