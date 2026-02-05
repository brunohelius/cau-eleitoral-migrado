<?php

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use ArrayObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use stdClass;

/**
 * Entidade de representação de 'Chapa Eleição'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ChapaEleicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_CHAPA_ELEICAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ChapaEleicao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_CHAPA_ELEICAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_chapa_eleicao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Filial")
     * @ORM\JoinColumn(name="ID_CAU_UF", referencedColumnName="id", nullable=false)
     * @var \App\Entities\Filial
     */
    private $filial;

    /**
     * @ORM\Column(name="ID_CAU_UF", type="integer", nullable=false)
     *
     * @var integer
     */
    private $idCauUf;

    /**
     * @ORM\Column(name="ID_PROFISSIONAL_INCLUSAO", type="integer")
     *
     * @var integer
     */
    private $idProfissionalInclusao;

    /**
     * @ORM\Column(name="ID_ETAPA", type="integer", nullable=false)
     *
     * @var integer
     */
    private $idEtapa;

    /**
     * @ORM\Column(name="NU_CHAPA", type="integer")
     *
     * @var integer|null
     */
    private $numeroChapa;

    /**
     * @ORM\Column(name="DS_PLATAFORMA", type="text", nullable=false)
     *
     * @var string
     */
    private $descricaoPlataforma;

    /**
     * @ORM\Column(name="ST_RESPOSTA_DECLARACAO", type="boolean", nullable=false)
     *
     * @var bool
     */
    private $situacaoRespostaDeclaracao;

    /**
     * @ORM\Column(name="ST_EXCLUIDO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $excluido;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\AtividadeSecundariaCalendario", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="ID_ATIV_SECUNDARIA", referencedColumnName="ID_ATIV_SECUNDARIA", nullable=false)
     *
     * @var \App\Entities\AtividadeSecundariaCalendario
     */
    private $atividadeSecundariaCalendario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoCandidatura")
     * @ORM\JoinColumn(name="ID_TP_CANDIDATURA", referencedColumnName="ID_TP_CANDIDATURA", nullable=false)
     *
     * @var TipoCandidatura
     */
    private $tipoCandidatura;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ChapaEleicaoStatus", mappedBy="chapaEleicao", fetch="EXTRA_LAZY")
     *
     * @var array|ArrayCollection
     */
    private $chapaEleicaoStatus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\MembroChapa", mappedBy="chapaEleicao", cascade={"remove"})
     *
     * @var MembroChapa[]
     */
    private $membrosChapa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\RedeSocialChapa", mappedBy="chapaEleicao", fetch="EXTRA_LAZY", cascade={"remove"})
     *
     * @var array|ArrayCollection
     */
    private $redesSociaisChapa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\HistoricoChapaEleicao", mappedBy="chapaEleicao", fetch="EXTRA_LAZY", cascade={"remove"})
     *
     * @var HistoricoChapaEleicao[]|ArrayCollection
     */
    private $historicoChapaEleicao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoRespostaDeclaracaoChapa", mappedBy="chapaEleicao", fetch="EXTRA_LAZY")
     *
     * @var array|ArrayCollection
     */
    private $arquivosRespostaDeclaracaoChapa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\PedidoSubstituicaoChapa", mappedBy="chapaEleicao", fetch="EXTRA_LAZY")
     *
     * @var array|ArrayCollection
     */
    private $pedidosSubstituicaoChapa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\StatusChapaJulgamentoFinal")
     * @ORM\JoinColumn(name="ID_STATUS_CHAPA_JULGAMENTO_FINAL", referencedColumnName="ID", nullable=false)
     *
     * @var StatusChapaJulgamentoFinal
     */
    private $statusChapaJulgamentoFinal;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\JulgamentoFinal", mappedBy="chapaEleicao", fetch="EXTRA_LAZY")
     *
     * @var JulgamentoFinal
     */
    private $julgamentoFinal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_PROFISSIONAL_INCLUSAO_PLATAFORMA", referencedColumnName="id", nullable=true)
     * @var Profissional
     */
    private $profissionalInclusaoPlataforma;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Usuario")
     * @ORM\JoinColumn(name="ID_USUARIO_INCLUSAO_PLATAFORMA", referencedColumnName="id", nullable=true)
     * @var Usuario
     */
    private $usuarioInclusaoPlataforma;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\PlataformaChapaHistorico", mappedBy="chapaEleicao", fetch="EXTRA_LAZY")
     *
     * @var array|ArrayCollection|PlataformaChapaHistorico[]
     */
    private $plataformaChapaHistoricos;

    /**
     * Transient
     *
     * @var Profissional
     */
    private $profissional;

    /**
     * @var StatusChapa
     */
    private $statusChapaVigente;

    /**
     * @var integer
     */
    private $idTipoAlteracaoVigente;

    /**
     * @var integer|null
     */
    private $numeroProporcaoConselheiros;

    /**
     * Transient
     *
     * @var Filial
     */
    private $cauUf;

    /**
     * Transient
     *
     * @var array
     */
    private $membrosResponsaveis;

    /**
     * Transient
     *
     * @var string
     */
    private $descricaoPosicoesSemMembros;

    /**
     * Transient
     *
     * @var boolean
     */
    private $atendeCriteriosRepresentatividade;

    /**
     * Transient
     *
     * @var boolean
     */
    private $atendeCotistasRepresentatividade;

    /**
     * Fábrica de instância de 'Chapa Eleição'.
     *
     * @param array $data
     * @return ChapaEleicao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $chapaEleicao = new self();

        if ($data != null) {
            $chapaEleicao->setId(Utils::getValue('id', $data));
            $chapaEleicao->setIdEtapa(Utils::getValue('idEtapa', $data));
            $chapaEleicao->setProfissional(Utils::getValue('profissional', $data));
            $chapaEleicao->setSituacaoRespostaDeclaracao(Utils::getValue('situacaoRespostaDeclaracao', $data));
            $chapaEleicao->setExcluido(Utils::getBooleanValue('excluido', $data));

            $tipoCandidatura = Utils::getValue('tipoCandidatura', $data, []);
            if (!empty($tipoCandidatura)) {
                $chapaEleicao->setTipoCandidatura(TipoCandidatura::newInstance(Utils::getValue('tipoCandidatura', $data)));
            }

            $membrosChapa = Utils::getValue('membrosChapa', $data, []);
            if (!empty($membrosChapa)) {
                $chapaEleicao->setMembrosChapa(array_map(function ($membroChapa) {
                    return MembroChapa::newInstance($membroChapa);
                }, $membrosChapa));
            }

            $atividadeSecundariaCalendario = Utils::getValue('atividadeSecundariaCalendario', $data);
            if (!empty($atividadeSecundariaCalendario)) {
                $chapaEleicao->setAtividadeSecundariaCalendario(AtividadeSecundariaCalendario::newInstance(
                    Utils::getValue('atividadeSecundariaCalendario', $data))
                );
            }

            $redesSociaisChapa = Utils::getValue('redesSociaisChapa', $data, []);
            $chapaEleicao->setRedesSociaisChapa(array_map(function ($redeSocialChapa) {
                return RedeSocialChapa::newInstance($redeSocialChapa);
            }, $redesSociaisChapa));

            $chapaEleicaoStatus = Utils::getValue('chapaEleicaoStatus', $data, []);
            if (!empty($chapaEleicaoStatus)) {
                $chapaEleicao->setChapaEleicaoStatus(array_map(function ($status) {
                    return ChapaEleicaoStatus::newInstance($status);
                }, $chapaEleicaoStatus));
            }

            $historicosChapaEleicao = Utils::getValue('historicosChapaEleicao', $data, []);
            $chapaEleicao->setHistoricoChapaEleicao(array_map(function ($historicoChapaEleicao) {
                return HistoricoChapaEleicao::newInstance($historicoChapaEleicao);
            }, $historicosChapaEleicao));

            $descricao = Utils::getValue('descricaoPlataforma', $data);
            if (!empty($descricao)) {
                $chapaEleicao->setDescricaoPlataforma($descricao);
            }

            $numeroChapa = Utils::getValue("numeroChapa", $data);
            if (!empty($numeroChapa)) {
                $chapaEleicao->setNumeroChapa($numeroChapa);
            }

            $idProfissionalInclusao = Utils::getValue('idProfissionalInclusao', $data);
            if (!empty($idProfissionalInclusao)) {
                $chapaEleicao->setIdProfissionalInclusao($idProfissionalInclusao);
            }

            $idCauUf = Utils::getValue('idCauUf', $data);
            $chapaEleicao->setIdCauUf($idCauUf);

            $filial = Utils::getValue('filial', $data, (!empty($idCauUf) ? ['id' => $idCauUf] : null));
            if (!empty($filial)) {
                $chapaEleicao->setFilial(Filial::newInstance($filial));
            }

            $arquivosRespostaDeclaracaoChapa = Utils::getValue('arquivosRespostaDeclaracaoChapa', $data, []);
            if (!empty($arquivosRespostaDeclaracaoChapa)) {
                $chapaEleicao->setArquivosRespostaDeclaracaoChapa(array_map(function ($arquivo) {
                    return ChapaEleicaoStatus::newInstance($arquivo);
                }, $arquivosRespostaDeclaracaoChapa));
            }

            $statusChapaJulgamentoFinal = Utils::getValue('statusChapaJulgamentoFinal', $data, []);
            if (!empty($statusChapaJulgamentoFinal)) {
                $chapaEleicao->setStatusChapaJulgamentoFinal(StatusChapaJulgamentoFinal::newInstance(
                    $statusChapaJulgamentoFinal
                ));
            }

            $profissionalInclusaoPlataforma = Utils::getValue('profissionalInclusaoPlataforma', $data, []);
            if (!empty($profissionalInclusaoPlataforma)) {
                $chapaEleicao->setProfissionalInclusaoPlataforma(Profissional::newInstance(
                    $profissionalInclusaoPlataforma
                ));
            }

            $usuarioInclusaoPlataforma = Utils::getValue('usuarioInclusaoPlataforma', $data, []);
            if (!empty($usuarioInclusaoPlataforma)) {
                $chapaEleicao->setUsuarioInclusaoPlataforma(Usuario::newInstance($usuarioInclusaoPlataforma));
            }

            $plataformaChapaHistoricos = Utils::getValue('plataformaChapaHistoricos', $data, []);
            if (!empty($plataformaChapaHistoricos)) {
                $chapaEleicao->setPlataformaChapaHistoricos(array_map(function ($plataformaHistorico) {
                    return PlataformaChapaHistorico::newInstance($plataformaHistorico);
                }, $plataformaChapaHistoricos));
            }
        }

        return $chapaEleicao;
    }

    /**
     * Define o status vigente da chapa da eleicao.
     *
     */
    public function definirStatusChapaVigente()
    {
        $statusChapa = null;

        $chapasEleicoesStatus = $this->getChapaEleicaoStatusOrdenadasPorData($this->getChapaEleicaoStatus());

        if (!empty($chapasEleicoesStatus) && count($chapasEleicoesStatus) > 0) {
            /** @var ChapaEleicaoStatus $chapaEleicaoStatus */
            $chapaEleicaoStatus = $chapasEleicoesStatus->last();
            $statusChapa = $chapaEleicaoStatus->getStatusChapa();

            if(!empty($chapaEleicaoStatus->getTipoAlteracao())){
                $this->idTipoAlteracaoVigente = $chapaEleicaoStatus->getTipoAlteracao()->getId();
            }
        }

        $this->statusChapaVigente = $statusChapa;
    }

    /**
     * Define o status vigente da chapa da eleicao.
     *
     */
    public function definirChapaEleicaoStatusVigente()
    {
        $chapaEleicaoStatus = null;
        $chapasEleicoesStatus = $this->getChapaEleicaoStatusOrdenadasPorData($this->getChapaEleicaoStatus());

        if (!empty($chapasEleicoesStatus) && count($chapasEleicoesStatus) > 0) {
            /** @var ChapaEleicaoStatus $chapaEleicaoStatus */
            $chapaEleicaoStatus = $chapasEleicoesStatus->last();

            if(!empty($chapaEleicaoStatus->getTipoAlteracao())){
                $this->idTipoAlteracaoVigente = $chapaEleicaoStatus->getTipoAlteracao()->getId();
            }
        }

        $this->statusChapaVigente = $chapaEleicaoStatus;
    }

    /**
     * Retorna as ChapaEleicaoStatus ordenadas por data.
     *
     * @param $chapaEleicaoStatus
     * @return mixed
     */
    private function getChapaEleicaoStatusOrdenadasPorData($chapaEleicaoStatus)
    {
        $chapaEleicaoStatusOrdenadas = new ArrayCollection();

        if (!empty($chapaEleicaoStatus)) {
            if (is_array($chapaEleicaoStatus)) {
                $chapaEleicaoStatus = new ArrayObject($chapaEleicaoStatus);
            }

            $iterator = $chapaEleicaoStatus->getIterator();

            $iterator->uasort(function ($a, $b) {
                return ($a->getData() < $b->getData()) ? -1 : 1;
            });

            $chapaEleicaoStatusOrdenadas = new ArrayCollection(iterator_to_array($iterator));
        }

        return $chapaEleicaoStatusOrdenadas;
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
     * @return int
     */
    public function getIdCauUf()
    {
        return $this->idCauUf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf($idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return Filial
     */
    public function getFilial()
    {
        return $this->filial;
    }

    /**
     * @param Filial $filial
     */
    public function setFilial($filial): void
    {
        $this->filial = $filial;
    }

    /**
     * @return int
     */
    public function getIdProfissionalInclusao()
    {
        return $this->idProfissionalInclusao;
    }

    /**
     * @param int $idProfissionalInclusao
     */
    public function setIdProfissionalInclusao($idProfissionalInclusao): void
    {
        $this->idProfissionalInclusao = $idProfissionalInclusao;
    }

    /**
     * @return int
     */
    public function getIdEtapa()
    {
        return $this->idEtapa;
    }

    /**
     * @param int $idEtapa
     */
    public function setIdEtapa($idEtapa): void
    {
        $this->idEtapa = $idEtapa;
    }

    /**
     * @return int|null
     */
    public function getNumeroChapa(): ?int
    {
        return $this->numeroChapa;
    }

    /**
     * @param int|null $numeroChapa
     */
    public function setNumeroChapa(?int $numeroChapa): void
    {
        $this->numeroChapa = $numeroChapa;
    }

    /**
     * @return string
     */
    public function getDescricaoPlataforma()
    {
        return $this->descricaoPlataforma;
    }

    /**
     * @param string $descricaoPlataforma
     */
    public function setDescricaoPlataforma($descricaoPlataforma): void
    {
        $this->descricaoPlataforma = $descricaoPlataforma;
    }

    /**
     * @return bool
     */
    public function isSituacaoRespostaDeclaracao(): ?bool
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
     * @return bool|null
     */
    public function isExcluido()
    {
        return $this->excluido;
    }

    /**
     * @param bool $excluido
     */
    public function setExcluido($excluido): void
    {
        $this->excluido = $excluido;
    }

    /**
     * @return AtividadeSecundariaCalendario
     */
    public function getAtividadeSecundariaCalendario()
    {
        return $this->atividadeSecundariaCalendario;
    }

    /**
     * @param AtividadeSecundariaCalendario $atividadeSecundariaCalendario
     */
    public function setAtividadeSecundariaCalendario(?AtividadeSecundariaCalendario $atividadeSecundariaCalendario): void
    {
        $this->atividadeSecundariaCalendario = $atividadeSecundariaCalendario;
    }

    /**
     * @return TipoCandidatura
     */
    public function getTipoCandidatura()
    {
        return $this->tipoCandidatura;
    }

    /**
     * @param TipoCandidatura $tipoCandidatura
     */
    public function setTipoCandidatura(TipoCandidatura $tipoCandidatura): void
    {
        $this->tipoCandidatura = $tipoCandidatura;
    }

    /**
     * @return ChapaEleicaoStatus[]|ArrayCollection
     */
    public function getChapaEleicaoStatus()
    {
        return $this->chapaEleicaoStatus;
    }

    /**
     * @return ChapaEleicaoStatus
     */
    public function getUltimoChapaEleicaoStatus()
    {
        return end($this->chapaEleicaoStatus);
    }

    /**
     * @param ChapaEleicaoStatus[]|ArrayCollection $chapaEleicaoStatus
     */
    public function setChapaEleicaoStatus($chapaEleicaoStatus): void
    {
        $this->chapaEleicaoStatus = $chapaEleicaoStatus;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getMembrosChapa()
    {
        return $this->membrosChapa;
    }

    /**
     * @param $membrosChapa
     */
    public function setMembrosChapa($membrosChapa): void
    {
        $this->membrosChapa = $membrosChapa;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getRedesSociaisChapa()
    {
        return $this->redesSociaisChapa;
    }

    /**
     * @param array|ArrayCollection $redesSociaisChapa
     */
    public function setRedesSociaisChapa($redesSociaisChapa): void
    {
        $this->redesSociaisChapa = $redesSociaisChapa;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivosRespostaDeclaracaoChapa()
    {
        return $this->arquivosRespostaDeclaracaoChapa;
    }

    /**
     * @param array|ArrayCollection $arquivosRespostaDeclaracaoChapa
     */
    public function setArquivosRespostaDeclaracaoChapa($arquivosRespostaDeclaracaoChapa): void
    {
        $this->arquivosRespostaDeclaracaoChapa = $arquivosRespostaDeclaracaoChapa;
    }

    /**
     * @return Profissional
     */
    public function getProfissional()
    {
        return $this->profissional;
    }

    /**
     * @param Profissional $profissional
     */
    public function setProfissional($profissional)
    {
        $this->profissional = $profissional;
    }

    /**
     * @return HistoricoChapaEleicao[]|ArrayCollection
     */
    public function getHistoricoChapaEleicao()
    {
        return $this->historicoChapaEleicao;
    }

    /**
     * @param HistoricoChapaEleicao[]|ArrayCollection $historicoChapaEleicao
     */
    public function setHistoricoChapaEleicao(array $historicoChapaEleicao): void
    {
        $this->historicoChapaEleicao = $historicoChapaEleicao;
    }

    /**
     * @return StatusChapa
     */
    public function getStatusChapaVigente()
    {
        return $this->statusChapaVigente;
    }

    /**
     * @return int
     */
    public function getIdTipoAlteracaoVigente()
    {
        return $this->idTipoAlteracaoVigente;
    }

    /**
     * @return int|null
     */
    public function getNumeroProporcaoConselheiros(): ?int
    {
        return $this->numeroProporcaoConselheiros;
    }

    /**
     * @param int|null $numeroProporcaoConselheiros
     */
    public function setNumeroProporcaoConselheiros(?int $numeroProporcaoConselheiros): void
    {
        $this->numeroProporcaoConselheiros = $numeroProporcaoConselheiros;
    }

    /**
     * @return Filial
     */
    public function getCauUf()
    {
        return $this->cauUf;
    }

    /**
     * @param Filial $cauUf
     */
    public function setCauUf($cauUf): void
    {
        $this->cauUf = $cauUf;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getPedidosSubstituicaoChapa()
    {
        return $this->pedidosSubstituicaoChapa;
    }

    /**
     * @param array|ArrayCollection $pedidosSubstituicaoChapa
     */
    public function setPedidosSubstituicaoChapa($pedidosSubstituicaoChapa): void
    {
        $this->pedidosSubstituicaoChapa = $pedidosSubstituicaoChapa;
    }

    /**
     * @return array
     */
    public function getMembrosResponsaveis()
    {
        return $this->membrosResponsaveis;
    }

    /**
     * @param array $membrosResponsaveis
     */
    public function setMembrosResponsaveis(array $membrosResponsaveis): void
    {
        $this->membrosResponsaveis = $membrosResponsaveis;
    }

    /**
     * @return string
     */
    public function getDescricaoPosicoesSemMembros(): ?string
    {
        return $this->descricaoPosicoesSemMembros;
    }

    /**
     * @param string $descricaoPosicoesSemMembros
     */
    public function setDescricaoPosicoesSemMembros(?string $descricaoPosicoesSemMembros): void
    {
        $this->descricaoPosicoesSemMembros = $descricaoPosicoesSemMembros;
    }

    /**
     * @return StatusChapaJulgamentoFinal
     */
    public function getStatusChapaJulgamentoFinal()
    {
        return $this->statusChapaJulgamentoFinal;
    }

    /**
     * @param StatusChapaJulgamentoFinal $statusChapaJulgamentoFinal
     */
    public function setStatusChapaJulgamentoFinal($statusChapaJulgamentoFinal): void
    {
        $this->statusChapaJulgamentoFinal = $statusChapaJulgamentoFinal;
    }

    /**
     * @return JulgamentoFinal
     */
    public function getJulgamentoFinal()
    {
        return $this->julgamentoFinal;
    }

    /**
     * @param JulgamentoFinal $julgamentoFinal
     */
    public function setJulgamentoFinal($julgamentoFinal): void
    {
        $this->julgamentoFinal = $julgamentoFinal;
    }

    /**
     * @return Profissional
     */
    public function getProfissionalInclusaoPlataforma()
    {
        return $this->profissionalInclusaoPlataforma;
    }

    /**
     * @param Profissional $profissionalInclusaoPlataforma
     */
    public function setProfissionalInclusaoPlataforma($profissionalInclusaoPlataforma): void
    {
        $this->profissionalInclusaoPlataforma = $profissionalInclusaoPlataforma;
    }

    /**
     * @return Usuario
     */
    public function getUsuarioInclusaoPlataforma()
    {
        return $this->usuarioInclusaoPlataforma;
    }

    /**
     * @param Usuario $usuarioInclusaoPlataforma
     */
    public function setUsuarioInclusaoPlataforma($usuarioInclusaoPlataforma): void
    {
        $this->usuarioInclusaoPlataforma = $usuarioInclusaoPlataforma;
    }

    /**
     * @return PlataformaChapaHistorico[]|array|ArrayCollection
     */
    public function getPlataformaChapaHistoricos()
    {
        return $this->plataformaChapaHistoricos;
    }

    /**
     * @param PlataformaChapaHistorico[]|array|ArrayCollection $plataformaChapaHistoricos
     */
    public function setPlataformaChapaHistoricos($plataformaChapaHistoricos): void
    {
        $this->plataformaChapaHistoricos = $plataformaChapaHistoricos;
    }

    /**
     * @return bool
     */
    public function isAtendeCriteriosRepresentatividade()
    {
        return $this->atendeCriteriosRepresentatividade;
    }

    /**
     * @param bool $atendeCriteriosRepresentatividade
     */
    public function setAtendeCriteriosRepresentatividade($atendeCriteriosRepresentatividade): void
    {
        $this->atendeCriteriosRepresentatividade = $atendeCriteriosRepresentatividade;
    }

    /**
     * @return bool
     */
    public function isAtendeCotistasRepresentatividade()
    {
        return $this->atendeCotistasRepresentatividade;
    }

    /**
     * @param bool $atendeCotistasRepresentatividade
     */
    public function setAtendeCotistasRepresentatividade($atendeCotistasRepresentatividade): void
    {
        $this->atendeCotistasRepresentatividade = $atendeCotistasRepresentatividade;
    }
}
