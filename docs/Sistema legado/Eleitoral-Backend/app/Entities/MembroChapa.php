<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'Membro da Chapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\MembroChapaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_MEMBRO_CHAPA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class MembroChapa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_MEMBRO_CHAPA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_membro_chapa_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\ChapaEleicao")
     * @ORM\JoinColumn(name="ID_CHAPA_ELEICAO", referencedColumnName="ID_CHAPA_ELEICAO", nullable=false)
     *
     * @var \App\Entities\ChapaEleicao
     */
    private $chapaEleicao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoParticipacaoChapa")
     * @ORM\JoinColumn(name="ID_TP_PARTIC_CHAPA", referencedColumnName="ID_TP_PARTIC_CHAPA", nullable=false)
     *
     * @var \App\Entities\TipoParticipacaoChapa
     */
    private $tipoParticipacaoChapa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoMembroChapa")
     * @ORM\JoinColumn(name="ID_TP_MEMBRO_CHAPA", referencedColumnName="ID_TP_MEMBRO_CHAPA", nullable=false)
     *
     * @var \App\Entities\TipoMembroChapa
     */
    private $tipoMembroChapa;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\MembroChapa")
     * @ORM\JoinColumn(name="ID_SUPLENTE", referencedColumnName="ID_MEMBRO_CHAPA")
     *
     * @var \App\Entities\MembroChapa
     */
    private $suplente;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_PROFISSIONAL", referencedColumnName="id", nullable=false)
     * @var Profissional
     */
    private $profissional;

    /**
     * @ORM\Column(name="NR_ORDEM", type="integer", length=11)
     *
     * @var integer
     */
    private $numeroOrdem;

    /**
     * @ORM\Column(name="ST_RESPONSAVEL", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $situacaoResponsavel;

    /**
     * @ORM\Column(name="ST_RESPOSTA_DECLARACAO", type="boolean", nullable=false)
     *
     * @var bool
     */
    private $situacaoRespostaDeclaracao;

    /**
     * @ORM\Column(name="DS_SINTESE_CURRICULO", type="text")
     *
     * @var string
     */
    private $sinteseCurriculo;

    /**
     * @ORM\Column(name="NM_ARQUIVO_FOTO", type="string", length=200)
     *
     * @var string
     */
    private $nomeArquivoFoto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\StatusParticipacaoChapa")
     * @ORM\JoinColumn(name="ID_STATUS_PARTIC_CHAPA", referencedColumnName="ID_STATUS_PARTIC_CHAPA", nullable=false)
     *
     * @var \App\Entities\StatusParticipacaoChapa
     */
    private $statusParticipacaoChapa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\StatusValidacaoMembroChapa")
     * @ORM\JoinColumn(name="ID_STATUS_VALIDACAO_MEMBRO_CHAPA", referencedColumnName="ID_STATUS_VALIDACAO_MEMBRO_CHAPA", nullable=false)
     *
     * @var \App\Entities\StatusValidacaoMembroChapa
     */
    private $statusValidacaoMembroChapa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\SituacaoMembroChapa")
     * @ORM\JoinColumn(name="ID_SITUACAO_MEMBRO_CHAPA", referencedColumnName="ID_SITUACAO_MEMBRO_CHAPA", nullable=false)
     *
     * @var \App\Entities\SituacaoMembroChapa
     */
    private $situacaoMembroChapa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\DocumentoComprobatorioSinteseCurriculo", mappedBy="membroChapa")
     *
     * @var array|ArrayCollection
     */
    private $documentosComprobatoriosSinteseCurriculo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\MembroChapaPendencia", mappedBy="membroChapa", cascade={"remove"})
     *
     * @var array|ArrayCollection
     */
    private $pendencias;

    /**
     * Transient
     *
     * @var mixed
     */
    private $fotoMembroChapa;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\PedidoImpugnacao", mappedBy="membroChapa", fetch="EXTRA_LAZY")
     *
     * @var PedidoImpugnacao
     */
    private $pedidoImpugnacao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\SubstituicaoImpugnacao", mappedBy="membroChapaSubstituto", fetch="EXTRA_LAZY")
     *
     * @var SubstituicaoImpugnacao
     */
    private $substituicaoImpugnacao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\MembroChapaSubstituicao", mappedBy="membroChapaSubstituto", fetch="EXTRA_LAZY")
     *
     * @var MembroChapaSubstituicao
     */
    private $membroChapaSubstituicao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\MembroSubstituicaoJulgamentoFinal", mappedBy="membroChapa", fetch="EXTRA_LAZY")
     *
     * @var MembroSubstituicaoJulgamentoFinal
     */
    private $membroSubstituicaoJulgamentoFinal;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\RespostaDeclaracaoRepresentatividade", mappedBy="membroChapa")
     *
     * @var array|ArrayCollection
     */
    private $respostaDeclaracaoRepresentatividade;

    /**
     * @ORM\Column(name="STATUS_ELEITO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $statusEleito;

    /**
     * Fábrica de instância de 'Membro da Chapa'.
     *
     * @param array $data
     *
     * @return MembroChapa
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setNumeroOrdem(Utils::getValue('numeroOrdem', $data));
            $instance->setSinteseCurriculo(Utils::getValue('sinteseCurriculo', $data));
            $instance->setSituacaoResponsavel(Utils::getValue('situacaoResponsavel', $data));
            $instance->setSituacaoRespostaDeclaracao(Utils::getValue('situacaoRespostaDeclaracao', $data));
            $instance->setNomeArquivoFoto(Utils::getValue('nomeArquivoFoto', $data));
            $instance->setFotoMembroChapa(Utils::getValue('fotoMembroChapa', $data));
            $instance->setStatusEleito(Utils::getValue('statusEleito', $data));

            $profissional = Utils::getValue('profissional', $data);
            if(!empty($profissional) and is_array($profissional)){
                $instance->setProfissional(Profissional::newInstance($profissional));
            }

            $chapaEleicao = Utils::getValue('chapaEleicao', $data);
            if(!empty($chapaEleicao) and is_array($chapaEleicao)){
                $instance->setChapaEleicao(ChapaEleicao::newInstance($chapaEleicao));
            }

            $tipoMembroChapa = Utils::getValue('tipoMembroChapa', $data);
            if(!empty($tipoMembroChapa) and is_array($tipoMembroChapa)){
                $instance->setTipoMembroChapa(TipoMembroChapa::newInstance($tipoMembroChapa));
            }

            $statusValidacaoMembroChapa = Utils::getValue('statusValidacaoMembroChapa', $data);
            if(!empty($statusValidacaoMembroChapa) and is_array($statusValidacaoMembroChapa)){
                $instance->setStatusValidacaoMembroChapa(
                    StatusValidacaoMembroChapa::newInstance($statusValidacaoMembroChapa)
                );
            }

            $tipoParticipacaoChapa = Utils::getValue('tipoParticipacaoChapa', $data);
            if(!empty($tipoParticipacaoChapa) and is_array($tipoParticipacaoChapa)){
                $instance->setTipoParticipacaoChapa(TipoParticipacaoChapa::newInstance($tipoParticipacaoChapa));
            }

            $statusParticipacaoChapa = Utils::getValue('statusParticipacaoChapa', $data);
            if(!empty($statusParticipacaoChapa) and is_array($statusParticipacaoChapa)){
                $instance->setStatusParticipacaoChapa(StatusParticipacaoChapa::newInstance($statusParticipacaoChapa));
            }

            $situacaoMembroChapa = Utils::getValue('situacaoMembroChapa', $data);
            if(!empty($situacaoMembroChapa) and is_array($situacaoMembroChapa)){
                $instance->setSituacaoMembroChapa(SituacaoMembroChapa::newInstance($situacaoMembroChapa));
            }

            $suplente = Utils::getValue('suplente', $data);
            if (!empty($suplente) and is_array($suplente)) {
                $instance->setSuplente(MembroChapa::newInstance($suplente));
            }

            $docsComprobatoriosSinteseCurriculo = Utils::getValue('docsComprobatoriosSinteseCurriculo', $data);
            if (!empty($docsComprobatoriosSinteseCurriculo)) {
                $instance->setDocumentosComprobatoriosSinteseCurriculo(array_map(function ($documentos) {
                    return DocumentoComprobatorioSinteseCurriculo::newInstance($documentos);
                }, $docsComprobatoriosSinteseCurriculo));
            }

            $pendencias = Utils::getValue('pendencias', $data);
            if (!empty($pendencias)) {
                $instance->setPendencias(array_map(function ($pendencia) {
                    return MembroChapaPendencia::newInstance($pendencia);
                }, $pendencias));
            }

            $substituicaoImpugnacao = Utils::getValue('substituicaoImpugnacao', $data);
            if(!empty($substituicaoImpugnacao)){
                $instance->setSubstituicaoImpugnacao(SubstituicaoImpugnacao::newInstance($substituicaoImpugnacao));
            }

            $membroChapaSubstituicao = Utils::getValue('membroChapaSubstituicao', $data);
            if(!empty($membroChapaSubstituicao)){
                $instance->setMembroChapaSubstituicao(MembroChapaSubstituicao::newInstance($membroChapaSubstituicao));
            }

            $membroSubstituicaoJulgamentoFinal = Utils::getValue('membroSubstituicaoJulgamentoFinal', $data);
            if(!empty($membroSubstituicaoJulgamentoFinal)){
                $instance->setMembroSubstituicaoJulgamentoFinal(MembroSubstituicaoJulgamentoFinal::newInstance(
                    $membroSubstituicaoJulgamentoFinal
                ));
            }

            $respostaDeclaracaoRepresentatividade = Utils::getValue('respostaDeclaracaoRepresentatividade', $data);
            if(!empty($respostaDeclaracaoRepresentatividade)){
                $instance->setRespostaDeclaracaoRepresentatividade(RespostaDeclaracaoRepresentatividade::newInstance(
                    $respostaDeclaracaoRepresentatividade
                ));
            }

            $respostasDeclaracaoRepresentatividade = Utils::getValue('respostaDeclaracaoRepresentatividade', $data);
            if (!empty($respostasDeclaracaoRepresentatividade)) {
                $instance->setRespostaDeclaracaoRepresentatividade(array_map(function ($respostaDeclaracaoRepresentatividade) {
                    return RespostaDeclaracaoRepresentatividade::newInstance($respostaDeclaracaoRepresentatividade);
                }, $respostasDeclaracaoRepresentatividade));
            }
        }
        return $instance;
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
     * @return ChapaEleicao
     */
    public function getChapaEleicao()
    {
        return $this->chapaEleicao;
    }

    /**
     * @param ChapaEleicao $chapaEleicao
     */
    public function setChapaEleicao(ChapaEleicao $chapaEleicao): void
    {
        $this->chapaEleicao = $chapaEleicao;
    }

    /**
     * @return TipoParticipacaoChapa
     */
    public function getTipoParticipacaoChapa()
    {
        return $this->tipoParticipacaoChapa;
    }

    /**
     * @param TipoParticipacaoChapa $tipoParticipacaoChapa
     */
    public function setTipoParticipacaoChapa(TipoParticipacaoChapa $tipoParticipacaoChapa): void
    {
        $this->tipoParticipacaoChapa = $tipoParticipacaoChapa;
    }

    /**
     * @return TipoMembroChapa
     */
    public function getTipoMembroChapa()
    {
        return $this->tipoMembroChapa;
    }

    /**
     * @param TipoMembroChapa $tipoMembroChapa
     */
    public function setTipoMembroChapa(TipoMembroChapa $tipoMembroChapa): void
    {
        $this->tipoMembroChapa = $tipoMembroChapa;
    }

    /**
     * @return int
     */
    public function getNumeroOrdem()
    {
        return $this->numeroOrdem;
    }

    /**
     * @param int $numeroOrdem
     */
    public function setNumeroOrdem($numeroOrdem): void
    {
        $this->numeroOrdem = $numeroOrdem;
    }

    /**
     * @return MembroChapa
     */
    public function getSuplente()
    {
        return $this->suplente;
    }

    /**
     * @param MembroChapa $suplente
     */
    public function setSuplente(?MembroChapa $suplente): void
    {
        $this->suplente = $suplente;
    }

    /**
     * @return bool|null
     */
    public function isSituacaoResponsavel()
    {
        return $this->situacaoResponsavel;
    }

    /**
     * @param bool $situacaoResponsavel
     */
    public function setSituacaoResponsavel($situacaoResponsavel): void
    {
        $this->situacaoResponsavel = $situacaoResponsavel;
    }

    /**
     * @return StatusParticipacaoChapa
     */
    public function getStatusParticipacaoChapa()
    {
        return $this->statusParticipacaoChapa;
    }

    /**
     * @param StatusParticipacaoChapa $statusParticipacaoChapa
     */
    public function setStatusParticipacaoChapa(StatusParticipacaoChapa $statusParticipacaoChapa): void
    {
        $this->statusParticipacaoChapa = $statusParticipacaoChapa;
    }

    /**
     * @return SituacaoMembroChapa
     */
    public function getSituacaoMembroChapa()
    {
        return $this->situacaoMembroChapa;
    }

    /**
     * @param SituacaoMembroChapa $situacaoMembroChapa
     */
    public function setSituacaoMembroChapa($situacaoMembroChapa): void
    {
        $this->situacaoMembroChapa = $situacaoMembroChapa;
    }

    /**
     * @return StatusValidacaoMembroChapa
     */
    public function getStatusValidacaoMembroChapa()
    {
        return $this->statusValidacaoMembroChapa;
    }

    /**
     * @param StatusValidacaoMembroChapa $statusValidacaoMembroChapa
     */
    public function setStatusValidacaoMembroChapa(StatusValidacaoMembroChapa $statusValidacaoMembroChapa): void
    {
        $this->statusValidacaoMembroChapa = $statusValidacaoMembroChapa;
    }

    /**
     * @return string
     */
    public function getSinteseCurriculo()
    {
        return $this->sinteseCurriculo;
    }

    /**
     * @param string $sinteseCurriculo
     */
    public function setSinteseCurriculo($sinteseCurriculo): void
    {
        $this->sinteseCurriculo = $sinteseCurriculo;
    }

    /**
     * @return string
     */
    public function getNomeArquivoFoto()
    {
        return $this->nomeArquivoFoto;
    }

    /**
     * @param string $nomeArquivoFoto
     */
    public function setNomeArquivoFoto($nomeArquivoFoto): void
    {
        $this->nomeArquivoFoto = $nomeArquivoFoto;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getDocumentosComprobatoriosSinteseCurriculo()
    {
        return $this->documentosComprobatoriosSinteseCurriculo;
    }

    /**
     * @param array|ArrayCollection $documentosComprobatoriosSinteseCurriculo
     */
    public function setDocumentosComprobatoriosSinteseCurriculo($documentosComprobatoriosSinteseCurriculo): void
    {
        $this->documentosComprobatoriosSinteseCurriculo = $documentosComprobatoriosSinteseCurriculo;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getPendencias()
    {
        return $this->pendencias;
    }

    /**
     * @param array|ArrayCollection $pendencias
     */
    public function setPendencias($pendencias): void
    {
        $this->pendencias = $pendencias;
    }

    /**
     * @return bool
     */
    public function getSituacaoRespostaDeclaracao()
    {
        return $this->situacaoRespostaDeclaracao;
    }

    /**
     * @param bool $situacaoRespostaDeclaracao
     */
    public function setSituacaoRespostaDeclaracao(?bool $situacaoRespostaDeclaracao): void
    {
        $this->situacaoRespostaDeclaracao = $situacaoRespostaDeclaracao;
    }

    /**
     * @return mixed
     */
    public function getFotoMembroChapa()
    {
        return $this->fotoMembroChapa;
    }

    /**
     * @param mixed $fotoMembroChapa
     */
    public function setFotoMembroChapa($fotoMembroChapa): void
    {
        $this->fotoMembroChapa = $fotoMembroChapa;
    }


    /**
     * @return Profissional
     */
    public function getProfissional(): ?Profissional
    {
        return $this->profissional;
    }

    /**
     * @param Profissional $profissional
     */
    public function setProfissional(?Profissional $profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return PedidoImpugnacao
     */
    public function getPedidoImpugnacao(): ?PedidoImpugnacao
    {
        return $this->pedidoImpugnacao;
    }

    /**
     * @param PedidoImpugnacao $pedidoImpugnacao
     */
    public function setPedidoImpugnacao(?PedidoImpugnacao $pedidoImpugnacao): void
    {
        $this->pedidoImpugnacao = $pedidoImpugnacao;
    }

    /**
     * @return SubstituicaoImpugnacao
     */
    public function getSubstituicaoImpugnacao()
    {
        return $this->substituicaoImpugnacao;
    }

    /**
     * @param SubstituicaoImpugnacao $substituicaoImpugnacao
     */
    public function setSubstituicaoImpugnacao($substituicaoImpugnacao): void
    {
        $this->substituicaoImpugnacao = $substituicaoImpugnacao;
    }

    /**
     * @return MembroChapaSubstituicao
     */
    public function getMembroChapaSubstituicao()
    {
        return $this->membroChapaSubstituicao;
    }

    /**
     * @param MembroChapaSubstituicao $membroChapaSubstituicao
     */
    public function setMembroChapaSubstituicao($membroChapaSubstituicao): void
    {
        $this->membroChapaSubstituicao = $membroChapaSubstituicao;
    }

    /**
     * @return MembroSubstituicaoJulgamentoFinal
     */
    public function getMembroSubstituicaoJulgamentoFinal()
    {
        return $this->membroSubstituicaoJulgamentoFinal;
    }

    /**
     * @param MembroSubstituicaoJulgamentoFinal $membroSubstituicaoJulgamentoFinal
     */
    public function setMembroSubstituicaoJulgamentoFinal($membroSubstituicaoJulgamentoFinal): void
    {
        $this->membroSubstituicaoJulgamentoFinal = $membroSubstituicaoJulgamentoFinal;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getRespostaDeclaracaoRepresentatividade()
    {
        return $this->respostaDeclaracaoRepresentatividade;
    }

    /**
     * @param array|ArrayCollection $respostaDeclaracaoRepresentatividade
     */
    public function setRespostaDeclaracaoRepresentatividade($respostaDeclaracaoRepresentatividade): void
    {
        $this->respostaDeclaracaoRepresentatividade = $respostaDeclaracaoRepresentatividade;
    }

     /**
     * @param bool $statusEleito
     */
    public function setStatusEleito($statusEleito): void
    {
        $this->statusEleito = $statusEleito;
    }

    /**
     * @return bool|null
     */
    public function isStatusEleito()
    {
        return $this->statusEleito;
    }
}
