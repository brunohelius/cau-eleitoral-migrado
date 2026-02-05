<?php


namespace App\To;
use App\Util\Utils;
use App\To\DeclaracaoTO;

class MembroComissaoParticipacaoFiltroTO
{
    /**
     * Texto inicial da declaração
     * @var DeclaracaoTO
     */
    private $declaracao;

    /**
     * id do Membro da Comissão Eleitoral
     * @var integer
     */
    private $idMembroComissao;

    /**
     * id do Membro da Comissão Eleitoral
     * @var integer|null
     */
    private $idCauUf;

    /**
     * id do Membro da Comissão Eleitoral
     * @var string|null
     */
    private $prefixo;

    /**
     * situacao do Membro da Comissão Eleitoral
     * @var integer
     */
    private $situacaoMembroComissao;


    public static function newInstance($declaracao , $data)
    {
        $membroComissaoParticipacaoFiltroTO = new MembroComissaoParticipacaoFiltroTO();

        if($declaracao != null) {
            $membroComissaoParticipacaoFiltroTO->setDeclaracao($declaracao);
        }

        $idMembroComissao = Utils::getValue('idMembroComissao', $data);
        if(!empty($idMembroComissao)) {
            $membroComissaoParticipacaoFiltroTO->setIdMembroComissao($idMembroComissao);
        }

        $situacaoMembroComissao = Utils::getValue('situacaoMembro', $data);
        if(!empty($situacaoMembroComissao)) {
            $membroComissaoParticipacaoFiltroTO->setSituacaoMembroComissao($situacaoMembroComissao);
        }

        $idCauUf = Utils::getValue('idCauUf', $data);
        if(!empty($idCauUf)) {
            $membroComissaoParticipacaoFiltroTO->setIdCauUf($idCauUf);
        }

        $prefixo = Utils::getValue('prefixo', $data);
        if(!empty($prefixo)) {
            $membroComissaoParticipacaoFiltroTO->setPrefixo($prefixo);
        }

        return $membroComissaoParticipacaoFiltroTO;
    }

    /**
     * @return DeclaracaoTO
     */
    public function getDeclaracao()
    {
        return $this->declaracao;
    }

    /**
     * @param DeclaracaoTO $declaracao
     */
    public function setDeclaracao($declaracao)
    {
        $this->declaracao = $declaracao;
    }

    /**
     * @return int
     */
    public function getIdMembroComissao()
    {
        return $this->idMembroComissao;
    }

    /**
     * @param $idMembroComissao
     */
    public function setIdMembroComissao($idMembroComissao)
    {
        $this->idMembroComissao = $idMembroComissao;
    }

    /**
     * @param $situacaoMembroComissao
     */
    public function setSituacaoMembroComissao($situacaoMembroComissao)
    {
        $this->situacaoMembroComissao = $situacaoMembroComissao;
    }

    /**
     * @return int
     */
    public function getSituacaoMembroComissao()
    {
        return $this->situacaoMembroComissao;
    }

    /**
     * @return int|null
     */
    public function getIdCauUf(): ?int
    {
        return $this->idCauUf;
    }

    /**
     * @param int|null $idCauUf
     */
    public function setIdCauUf(?int $idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return string|null
     */
    public function getPrefixo(): ?string
    {
        return $this->prefixo;
    }

    /**
     * @param string|null $prefixo
     */
    public function setPrefixo(?string $prefixo): void
    {
        $this->prefixo = $prefixo;
    }


}
