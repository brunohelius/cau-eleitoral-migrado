<?php

namespace App\To;

use App\Exceptions\NegocioException;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe de transferência associada ao 'DocumentoComissaoMembro'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class DocumentoComissaoMembroTO
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var boolean
     */
    private $situacaoCabecalhoAtivo;

    /**
     * @var string
     */
    private $descricaoCabecalho;

    /**
     * @var boolean
     */
    private $situacaoTextoInicial;

    /**
     * @var string
     */
    private $descricaoTextoInicial;

    /**
     * @var boolean
     */
    private $situacaoTextoFinal;

    /**
     * @var string
     */
    private $descricaoTextoFinal;

    /**
     * @var boolean
     */
    private $situacaoTextoRodape;

    /**
     * @var string
     */
    private $descricaoTextoRodape;

    /**
     * @var integer
     */
    private $totalPublicacoes;

    /**
     * @var CalendarioTO
     */
    private $calendario;

    /**
     * @var InformacaoComissaoMembroTO
     */
    private $informacaoComissaoMembro;

    /**
     * @var ArrayCollection
     */
    private $publicacoes;

    /**
     * Retorna uma nova instância de 'DocumentoComissaoMembroTO'.
     *
     * @param null $data
     * @return DocumentoComissaoMembroTO
     * @throws NegocioException
     */
    public static function newInstance($data = null)
    {
        $documentoComissaoMembroTO = new DocumentoComissaoMembroTO();

        if ($data != null) {
            $documentoComissaoMembroTO->setId(Utils::getValue('id', $data));
            $documentoComissaoMembroTO->setTotalPublicacoes(Utils::getValue('totalPublicacoes', $data));
            $documentoComissaoMembroTO->setDescricaoCabecalho(Utils::getValue('descricaoCabecalho', $data));
            $documentoComissaoMembroTO->setSituacaoTextoFinal(Utils::getValue('situacaoTextoFinal', $data));
            $documentoComissaoMembroTO->setDescricaoTextoFinal(Utils::getValue('descricaoTextoFinal', $data));
            $documentoComissaoMembroTO->setSituacaoTextoRodape(Utils::getValue('situacaoTextoRodape', $data));
            $documentoComissaoMembroTO->setDescricaoTextoRodape(Utils::getValue('descricaoTextoRodape', $data));
            $documentoComissaoMembroTO->setSituacaoTextoInicial(Utils::getValue('situacaoTextoInicial', $data));
            $documentoComissaoMembroTO->setDescricaoTextoInicial(Utils::getValue('descricaoTextoInicial', $data));
            $documentoComissaoMembroTO->setSituacaoCabecalhoAtivo(
                Utils::getValue('situacaoCabecalhoAtivo', $data)
            );

            $calendarioTO = CalendarioTO::newInstance($data);
            $documentoComissaoMembroTO->setCalendario($calendarioTO);

            if (!empty($data['informacaoComissaoMembro'])) {
                $informacaoComissaoMembroTO = InformacaoComissaoMembroTO::newInstance($data['informacaoComissaoMembro']);
                $documentoComissaoMembroTO->setInformacaoComissaoMembro($informacaoComissaoMembroTO);
            }

            $publicacoes = Utils::getValue('publicacoesDocumento', $data);
            if (!empty($publicacoes)) {
                foreach ($publicacoes as $publicacao) {
                    $publicacaoTO = PublicacaoDocumentoTO::newInstance($publicacao);
                    $documentoComissaoMembroTO->adicionarPublicacao($publicacaoTO);
                }
            }
        }

        return $documentoComissaoMembroTO;
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
     * @return bool
     */
    public function isSituacaoCabecalhoAtivo()
    {
        return $this->situacaoCabecalhoAtivo;
    }

    /**
     * @param bool $situacaoCabecalhoAtivo
     */
    public function setSituacaoCabecalhoAtivo($situacaoCabecalhoAtivo)
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
    public function setDescricaoCabecalho($descricaoCabecalho)
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
    public function setSituacaoTextoInicial($situacaoTextoInicial)
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
    public function setDescricaoTextoInicial($descricaoTextoInicial)
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
    public function setSituacaoTextoFinal($situacaoTextoFinal)
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
    public function setDescricaoTextoFinal($descricaoTextoFinal)
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
    public function setSituacaoTextoRodape($situacaoTextoRodape)
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
    public function setDescricaoTextoRodape($descricaoTextoRodape)
    {
        $this->descricaoTextoRodape = $descricaoTextoRodape;
    }

    /**
     * @return int
     */
    public function getTotalPublicacoes()
    {
        return $this->totalPublicacoes;
    }

    /**
     * @param int $totalPublicacoes
     */
    public function setTotalPublicacoes($totalPublicacoes)
    {
        $this->totalPublicacoes = $totalPublicacoes;
    }

    /**
     * @return CalendarioTO
     */
    public function getCalendario()
    {
        return $this->calendario;
    }

    /**
     * @param CalendarioTO $calendario
     */
    public function setCalendario($calendario)
    {
        $this->calendario = $calendario;
    }

    /**
     * @return InformacaoComissaoMembroTO
     */
    public function getInformacaoComissaoMembro()
    {
        return $this->informacaoComissaoMembro;
    }

    /**
     * @param InformacaoComissaoMembroTO $informacaoComissaoMembro
     */
    public function setInformacaoComissaoMembro($informacaoComissaoMembro)
    {
        $this->informacaoComissaoMembro = $informacaoComissaoMembro;
    }

    /**
     * @return ArrayCollection
     */
    public function getPublicacoes()
    {
        return $this->publicacoes;
    }

    /**
     * @param ArrayCollection $publicacoes
     */
    public function setPublicacoes($publicacoes)
    {
        $this->publicacoes = $publicacoes;
    }

    private function adicionarPublicacao($publicacao)
    {
        if (empty($this->getPublicacoes())) {
            $this->setPublicacoes(new ArrayCollection());
        }

        $this->getPublicacoes()->add($publicacao);
    }
}
