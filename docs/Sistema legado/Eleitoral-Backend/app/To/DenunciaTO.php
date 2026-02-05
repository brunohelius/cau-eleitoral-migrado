<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\Denuncia;
use App\Entities\Filial;
use App\Entities\Pessoa;
use App\Entities\TestemunhaDenuncia;
use App\Entities\TipoDenuncia;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe de transferência associada a tabela de da 'DenunciaTO'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class DenunciaTO
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $dataHora;

    /**
     * @var string|null
     */
    private $narracaoDosFatos;

    /**
     * @var string
     */
    private $numeroSequencial;

    /**
     * @var Pessoa|null
     */
    private $denunciante;

    /**
     * @var
     */
    private $denunciado;

    /**
     * @var Filial|null
     */
    private $filial;

    /**
     * @var TipoDenuncia|null
     */
    private $tipoDenuncia;

    /**
     * @var array|ArrayCollection|null
     */
    private $testemunhas;

    /**
     * @var ArquivoDescricaoTO[]|null
     */
    private $documentos;

    /**
     * @var boolean
     */
    private $isSigilosa;

    /**
     * @var AnaliseAdmissibilidadeTO|null
     */
    private $analiseAdmissibilidade;

    /**
     * @var JulgamentoAdmissibilidadeTO|null
     */
    private $julgamentoAdmissibilidade;

    /**
     * Retorna uma nova instância de 'DenunciaTO'.
     *
     * @param null $data
     * @return DenunciaTO
     */
    public static function newInstance($data = null)
    {
        $denunciaTO = new DenunciaTO();

        if ($data != null) {
            $denunciaTO->setIdDenuncia(Utils::getValue('id', $data));
            $denunciaTO->setNumeroSequencial(Utils::getValue('numeroSequencial', $data));
            $denunciaTO->setDataHora(Utils::getValue('data', $data));
        }

        return $denunciaTO;
    }

    /**
     * Retorna uma nova instância de 'DenunciaTO'.
     *
     * @param Denuncia $denuncia
     * @return DenunciaTO
     */
    public static function newInstanceFromEntity($denuncia = null)
    {
        $denunciaTO = new DenunciaTO();

        if ($denuncia != null) {
            $denunciaTO->setIdDenuncia($denuncia->getId());
            $denunciaTO->setNumeroSequencial($denuncia->getNumeroSequencial());
            $denunciaTO->setDataHora($denuncia->getDataHora());
            $denunciaTO->setDenunciante($denuncia->getPessoa());
            $denunciaTO->setFilial($denuncia->getFilial());
            $denunciaTO->setTipoDenuncia($denuncia->getTipoDenuncia());
            $denunciaTO->setNarracaoDosFatos($denuncia->getDescricaoFatos());
            $denunciaTO->setTestemunhas($denuncia->getTestemunhas());
            $denunciaTO->setIsSigilosa($denuncia->isSigiloso());
            $denunciaTO->setAnaliseAdmissibilidade(AnaliseAdmissibilidadeTO::newInstance([
                "denuncia_admitida" => $denuncia->getUltimaDenunciaAdmitida(),
                "denuncia_inadmitida" => DenunciaInadmitidaTO::newInstanceFromEntity($denuncia->getDenunciaInadmitida()),
                "historico_admissao" => $denunciaTO->getHistDenunciaAdmitida($denuncia->getDenunciasAdmitidas()),
                "coordenadores" => [$denuncia->getCoordenadorComissao()]
            ]));
            $denunciaTO->setJulgamentoAdmissibilidade(JulgamentoAdmissibilidadeTO::newInstanceFromEntity($denuncia->getJulgamentoAdmissibilidade()));
            $denunciaTO->setDenunciado($denunciaTO->getNomeDenunciadoPorTipoDenuncia($denuncia));
        }

        return $denunciaTO;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setIdDenuncia(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getDataHora(): ?\DateTime
    {
        return $this->dataHora;
    }

    /**
     * @param \DateTime $dataHora
     */
    public function setDataHora(?\DateTime $dataHora): void
    {
        $this->dataHora = $dataHora;
    }

    /**
     * @return string
     */
    public function getNumeroSequencial(): ?string
    {
        return $this->numeroSequencial;
    }

    /**
     * @param string $numeroSequencial
     */
    public function setNumeroSequencial(?string $numeroSequencial): void
    {
        $this->numeroSequencial = $numeroSequencial;
    }

    /**
     * @return Pessoa|null
     */
    public function getDenunciante(): ?Pessoa
    {
        return $this->denunciante;
    }

    /**
     * @param Pessoa|null $denunciante
     */
    public function setDenunciante(?Pessoa $denunciante): void
    {
        $this->denunciante = $denunciante;
    }

    /**
     * @return Filial|null
     */
    public function getFilial(): ?Filial
    {
        return $this->filial;
    }

    /**
     * @param Filial|null $filial
     */
    public function setFilial(?Filial $filial): void
    {
        $this->filial = $filial;
    }

    /**
     * @return TipoDenuncia|null
     */
    public function getTipoDenuncia(): ?TipoDenuncia
    {
        return $this->tipoDenuncia;
    }

    /**
     * @param TipoDenuncia|null $tipoDenuncia
     */
    public function setTipoDenuncia(?TipoDenuncia $tipoDenuncia): void
    {
        $this->tipoDenuncia = $tipoDenuncia;
    }

    /**
     * @return string|null
     */
    public function getNarracaoDosFatos(): ?string
    {
        return $this->narracaoDosFatos;
    }

    /**
     * @param string|null $narracaoDosFatos
     */
    public function setNarracaoDosFatos(?string $narracaoDosFatos): void
    {
        $this->narracaoDosFatos = $narracaoDosFatos;
    }

    /**
     * @return array|ArrayCollection|null
     */
    public function getTestemunhas()
    {
        return $this->testemunhas;
    }

    /**
     * @param array|ArrayCollection|null $testemunhas
     */
    public function setTestemunhas($testemunhas): void
    {
        $this->testemunhas = $testemunhas;
    }

    /**
     * @return ArquivoDescricaoTO[]|null
     */
    public function getDocumentos(): ?array
    {
        return $this->documentos;
    }

    /**
     * @param ArquivoDescricaoTO[]|null $documentos
     */
    public function setDocumentos(?array $documentos): void
    {
        $this->documentos = $documentos;
    }

    /**
     * @return bool
     */
    public function isSigilosa(): bool
    {
        return $this->isSigilosa;
    }

    /**
     * @param bool $isSigilosa
     */
    public function setIsSigilosa(bool $isSigilosa): void
    {
        $this->isSigilosa = $isSigilosa;
    }

    /**
     * @return AnaliseAdmissibilidadeTO|null
     */
    public function getAnaliseAdmissibilidade(): ?AnaliseAdmissibilidadeTO
    {
        return $this->analiseAdmissibilidade;
    }

    /**
     * @param AnaliseAdmissibilidadeTO|null $analiseAdmissibilidade
     */
    public function setAnaliseAdmissibilidade(?AnaliseAdmissibilidadeTO $analiseAdmissibilidade): void
    {
        $this->analiseAdmissibilidade = $analiseAdmissibilidade;
    }

    /**
     * Retorna o historico de denuncias admitidas sem a atual
     * @param $denunciasAdmitidas
     * @return mixed|array|null
     */
    public function getHistDenunciaAdmitida($denunciasAdmitidas) {
        $historico = null;
        if(!empty($denunciasAdmitidas)) {
            $count = 0;
            foreach($denunciasAdmitidas as $denunciaAdmitida){
                if($count < sizeof($denunciasAdmitidas) - 1) {
                    $historico[] = $denunciaAdmitida;
                }
                $count ++;
            }
        }
        return $historico;
    }

    /**
     * @return JulgamentoAdmissibilidadeTO|null
     */
    public function getJulgamentoAdmissibilidade(): ?JulgamentoAdmissibilidadeTO
    {
        return $this->julgamentoAdmissibilidade;
    }

    /**
     * @param JulgamentoAdmissibilidadeTO|null $julgamentoAdmissibilidade
     */
    public function setJulgamentoAdmissibilidade(?JulgamentoAdmissibilidadeTO $julgamentoAdmissibilidade): void
    {
        $this->julgamentoAdmissibilidade = $julgamentoAdmissibilidade;
    }

    /**
     * @return mixed
     */
    public function getDenunciado()
    {
        return $this->denunciado;
    }

    /**
     * @param mixed $denunciado
     */
    public function setDenunciado($denunciado): void
    {
        $this->denunciado = $denunciado;
    }

    /**
     * @param \App\Entities\Denuncia $denuncia
     *
     * @return string
     */
    private function getNomeDenunciadoPorTipoDenuncia(Denuncia $denuncia): string
    {
        $denunciado = '-';
        $tipoDenuncia = $denuncia->getTipoDenuncia()->getId();

        if(Constants::TIPO_CHAPA === $tipoDenuncia) {
            $denunciado = $denuncia->getDenunciaChapa()->getChapaEleicao()
                ->getNumeroChapa();
        }

        if(Constants::TIPO_MEMBRO_CHAPA === $tipoDenuncia) {
            $profissional = $denuncia->getDenunciaMembroChapa()->getMembroChapa()
                ->getProfissional();
            $denunciado = $profissional ? $profissional->getNome() : '-';
        }

        if(Constants::TIPO_MEMBRO_COMISSAO === $tipoDenuncia) {
            $profissional = $denuncia->getDenunciaMembroComissao()->getMembroComissao()
                ->getProfissionalEntity();
            $denunciado = $profissional ? $profissional->getNome() : '-';
        }

        return $denunciado;
    }

}
