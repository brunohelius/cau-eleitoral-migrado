<?php
/*
 * EncaminhamentoDenuncia.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'EncaminhamentoDenuncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\EncaminhamentoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ENCAMINHAMENTO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class EncaminhamentoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ENCAMINHAMENTO_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_encaminhamento_denuncia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_ENCAMINHAMENTO", type="string", length=2000, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="DS_JUSTIFICATIVA", type="string", length=2000, nullable=true)
     *
     * @var string
     */
    private $justificativa;

    /**
     * @ORM\Column(name="DT_ENCAMINHAMENTO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $data;

    /**
     * @ORM\Column(name="DT_FECHAMENTO", type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $dataFechamento;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\TipoEncaminhamento")
     * @ORM\JoinColumn(name="ID_TIPO_ENCAMINHAMENTO", referencedColumnName="ID_TIPO_ENCAMINHAMENTO", nullable=false)
     *
     * @var \App\Entities\TipoEncaminhamento
     */
    private $tipoEncaminhamento;

    /**
     * @ORM\Column(name="DESTINO_DENUNCIADO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $destinoDenunciado;

    /**
     * @ORM\Column(name="DESTINO_DENUNCIANTE", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $destinoDenunciante;

    /**
     * @ORM\Column(name="PRAZO_PRODUCAO_PROVAS", type="integer", length=11, nullable=true)
     *
     * @var integer
     */
    private $prazoProducaoProvas;

    /**
     * @ORM\Column(name="SQ_ENCAMINHAMENTO", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $sequencia;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\TipoSituacaoEncaminhamentoDenuncia")
     * @ORM\JoinColumn(name="ID_TP_SITUACAO_ENCAMINHAMENTO", referencedColumnName="ID_TP_SITUACAO_ENCAMINHAMENTO", nullable=false)
     *
     * @var \App\Entities\TipoSituacaoEncaminhamentoDenuncia
     */
    private $tipoSituacaoEncaminhamento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\Denuncia
     */
    private $denuncia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\AgendamentoEncaminhamentoDenuncia", mappedBy="encaminhamentoDenuncia")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $agendamentoEncaminhamento;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoEncaminhamentoDenuncia", mappedBy="encaminhamentoDenuncia")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $arquivoEncaminhamento;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\MembroComissao")
     * @ORM\JoinColumn(name="ID_MEMBRO_COMISSAO", referencedColumnName="ID_MEMBRO_COMISSAO", nullable=false)
     *
     * @var \App\Entities\MembroComissao
     */
    private $membroComissao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\AlegacaoFinal", mappedBy="encaminhamentoDenuncia")
     *
     * @var AlegacaoFinal
     */
    private $alegacaoFinal;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\ParecerFinal", mappedBy="encaminhamentoDenuncia", cascade={"persist"})
     *
     * @var ParecerFinal
     */
    private $parecerFinal;

    /**
     * Transient
     *
     * @var int
     */
    private $idDenuncia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\DenunciaProvas", mappedBy="encaminhamentoDenuncia", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $denunciaProvas;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\ImpedimentoSuspeicao", mappedBy="encaminhamentoDenuncia")
     *
     * @var \App\Entities\ImpedimentoSuspeicao
     */
    private $impedimentoSuspeicao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\DenunciaAudienciaInstrucao", mappedBy="encaminhamentoDenuncia", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $audienciaInstrucao;

    /**
     * Fábrica de instância de 'EncaminhamentoDenuncia'.
     *
     * @param null $data
     *
     * @return self
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data !== null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setData(Utils::getValue('data', $data));
            $instance->setDescricao(Utils::getValue('descricao', $data));
            $instance->setSequencia(Utils::getValue('sequencia', $data));
            $instance->setIdDenuncia(Utils::getValue('idDenuncia', $data));
            $instance->setPrazoProducaoProvas(Utils::getValue('prazoProducaoProvas', $data));
            $instance->setDestinoDenunciado(Utils::getBooleanValue('destinoDenunciado', $data));
            $instance->setDestinoDenunciante(Utils::getBooleanValue('destinoDenunciante', $data));

            $justificativa = Utils::getValue('justificativa', $data);
            if (!empty($justificativa)) {
                $instance->setJustificativa($justificativa);
            }

            $dataFechamento = Utils::getValue('dataFechamento', $data);
            if (!empty($dataFechamento)) {
                $instance->setDataFechamento($dataFechamento);
            }

            $tipoEncaminhamento = Utils::getValue('tipoEncaminhamento', $data);
            if (!empty($tipoEncaminhamento)) {
                $instance->setTipoEncaminhamento(TipoEncaminhamento::newInstance($tipoEncaminhamento));
            }

            $tipoSituacaoEncaminhamento = Utils::getValue('tipoSituacaoEncaminhamento', $data);
            if (!empty($tipoSituacaoEncaminhamento)) {
                $instance->setTipoSituacaoEncaminhamento(TipoSituacaoEncaminhamentoDenuncia::newInstance($tipoSituacaoEncaminhamento));
            }

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $instance->setDenuncia(Denuncia::newInstance($denuncia));
            }

            $agendamentos = Utils::getValue('agendamentoEncaminhamento', $data);
            if (!empty($agendamentos)) {
                if (count($agendamentos) == 1) {
                    $instance->adicionarAgendamentoEncaminhamento(
                        AgendamentoEncaminhamentoDenuncia::newInstance($agendamentos)
                    );
                }
                else {
                    foreach ($agendamentos as $agendamento) {
                        $instance->adicionarAgendamentoEncaminhamento(
                            AgendamentoEncaminhamentoDenuncia::newInstance($agendamento)
                        );
                    }
                }
            }

            $arquivos = Utils::getValue('arquivoEncaminhamento', $data);
            if (!empty($arquivos)) {
                foreach ($arquivos as $arquivo) {
                    $instance->adicionarArquivoEncaminhamento(
                        ArquivoEncaminhamentoDenuncia::newInstance($arquivo)
                    );
                }
            }

            $membroComissao = Utils::getValue('membroComissao', $data);
            if (!empty($membroComissao)) {
                $instance->setMembroComissao(MembroComissao::newInstance($membroComissao));
            }

            $impedimentoSuspeicao = Utils::getValue('impedimentoSuspeicao', $data);
            if (!empty($impedimentoSuspeicao)) {
                $instance->setImpedimentoSuspeicao(ImpedimentoSuspeicao::newInstance($impedimentoSuspeicao));
            }

            $alegacaoFinal = Utils::getValue('alegacaoFinal', $data);
            if (!empty($alegacaoFinal)) {
                $instance->setAlegacaoFinal(AlegacaoFinal::newInstance($alegacaoFinal));
            }

            $parecerFinal = Utils::getValue('parecerFinal', $data);
            if (!empty($parecerFinal)) {
                $instance->setParecerFinal(ParecerFinal::newInstance($parecerFinal));
            }
        }

        return $instance;
    }

    /**
     * Adiciona uma agendamento no array de agendamentos
     *
     * @param AgendamentoEncaminhamentoDenuncia $agendamentoEncaminhamento
     */
    private function adicionarAgendamentoEncaminhamento(AgendamentoEncaminhamentoDenuncia $agendamentoEncaminhamento)
    {
        if ($this->getAgendamentoEncaminhamento() === null) {
            $this->setAgendamentoEncaminhamento(new ArrayCollection());
        }

        if (!empty($agendamentoEncaminhamento)) {
            $agendamentoEncaminhamento->setEncaminhamentoDenuncia($this);
            $this->getAgendamentoEncaminhamento()->add($agendamentoEncaminhamento);
        }
    }

    /**
     * Adiciona um Arquivo no array de Arquivos
     *
     * @param ArquivoEncaminhamentoDenuncia $arquivo
     */
    private function adicionarArquivoEncaminhamento(ArquivoEncaminhamentoDenuncia $arquivo)
    {
        if ($this->getArquivoEncaminhamento() == null) {
            $this->setArquivoEncaminhamento(new ArrayCollection());
        }

        if (!empty($arquivo)) {
            $arquivo->setEncaminhamentoDenuncia($this);
            $this->getArquivoEncaminhamento()->add($arquivo);
        }
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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return string
     */
    public function getJustificativa()
    {
        return $this->justificativa;
    }

    /**
     * @param string $justificativa
     */
    public function setJustificativa(?string $justificativa)
    {
        $this->justificativa = $justificativa;
    }

    /**
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \DateTime|string $data
     *
     * @throws \Exception
     */
    public function setData($data)
    {
        if (is_string($data)) {
            $data = new \DateTime($data);
        }
        $this->data = $data;
    }

    /**
     * @return \DateTime
     */
    public function getDataFechamento()
    {
        return $this->dataFechamento;
    }

    /**
     * @param \DateTime|string $dataFechamento
     *
     * @throws \Exception
     */
    public function setDataFechamento($dataFechamento)
    {
        if (is_string($dataFechamento)) {
            $data = new \DateTime($dataFechamento);
        }

        $this->dataFechamento = $dataFechamento;
    }

    /**
     * @return TipoEncaminhamento
     */
    public function getTipoEncaminhamento()
    {
        return $this->tipoEncaminhamento;
    }

    /**
     * @param TipoEncaminhamento $tipoEncaminhamento
     */
    public function setTipoEncaminhamento(TipoEncaminhamento $tipoEncaminhamento)
    {
        $this->tipoEncaminhamento = $tipoEncaminhamento;
    }

    /**
     * @return bool
     */
    public function isDestinoDenunciado()
    {
        return $this->destinoDenunciado;
    }

    /**
     * @param bool $destinoDenunciado
     */
    public function setDestinoDenunciado($destinoDenunciado)
    {
        $this->destinoDenunciado = $destinoDenunciado;
    }

    /**
     * @return bool
     */
    public function isDestinoDenunciante()
    {
        return $this->destinoDenunciante;
    }

    /**
     * @param bool $destinoDenunciante
     */
    public function setDestinoDenunciante($destinoDenunciante)
    {
        $this->destinoDenunciante = $destinoDenunciante;
    }

    /**
     * @return int
     */
    public function getPrazoProducaoProvas()
    {
        return $this->prazoProducaoProvas;
    }

    /**
     * @param int $prazoProducaoProvas
     */
    public function setPrazoProducaoProvas($prazoProducaoProvas)
    {
        $this->prazoProducaoProvas = $prazoProducaoProvas;
    }

    /**
     * @return int
     */
    public function getSequencia()
    {
        return $this->sequencia;
    }

    /**
     * @param int $sequencia
     */
    public function setSequencia($sequencia)
    {
        $this->sequencia = $sequencia;
    }

    /**
     * @return TipoSituacaoEncaminhamentoDenuncia
     */
    public function getTipoSituacaoEncaminhamento()
    {
        return $this->tipoSituacaoEncaminhamento;
    }

    /**
     * @param TipoSituacaoEncaminhamentoDenuncia $tipoSituacaoEncaminhamento
     */
    public function setTipoSituacaoEncaminhamento(TipoSituacaoEncaminhamentoDenuncia $tipoSituacaoEncaminhamento)
    {
        $this->tipoSituacaoEncaminhamento = $tipoSituacaoEncaminhamento;
    }

    /**
     * @return Denuncia
     */
    public function getDenuncia()
    {
        return $this->denuncia;
    }

    /**
     * @param Denuncia $denuncia
     */
    public function setDenuncia(Denuncia $denuncia)
    {
        $this->denuncia = $denuncia;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getAgendamentoEncaminhamento()
    {
        return $this->agendamentoEncaminhamento;
    }

    /**
     * @param array|ArrayCollection $agendamentoEncaminhamento
     */
    public function setAgendamentoEncaminhamento($agendamentoEncaminhamento)
    {
        $this->agendamentoEncaminhamento = $agendamentoEncaminhamento;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivoEncaminhamento()
    {
        return $this->arquivoEncaminhamento;
    }

    /**
     * @param array|ArrayCollection $arquivoEncaminhamento
     */
    public function setArquivoEncaminhamento($arquivoEncaminhamento)
    {
        $this->arquivoEncaminhamento = $arquivoEncaminhamento;
    }

    /**
     * @return int
     */
    public function getIdDenuncia()
    {
        return $this->idDenuncia;
    }

    /**
     * @param int $idDenuncia
     */
    public function setIdDenuncia($idDenuncia)
    {
        $this->idDenuncia = $idDenuncia;
    }

    /**
     * @return MembroComissao
     */
    public function getMembroComissao()
    {
        return $this->membroComissao;
    }

    /**
     * @param MembroComissao $membroComissao
     */
    public function setMembroComissao(MembroComissao $membroComissao)
    {
        $this->membroComissao = $membroComissao;
    }

    /**
     * @return ImpedimentoSuspeicao
     */
    public function getImpedimentoSuspeicao()
    {
        return $this->impedimentoSuspeicao;
    }

    /**
     * @param ImpedimentoSuspeicao $impedimentoSuspeicao
     */
    public function setImpedimentoSuspeicao(ImpedimentoSuspeicao $impedimentoSuspeicao)
    {
        $this->impedimentoSuspeicao = $impedimentoSuspeicao;
    }

    /**
     * @return AlegacaoFinal
     */
    public function getAlegacaoFinal(): ?AlegacaoFinal
    {
        return $this->alegacaoFinal;
    }

    /**
     * @param AlegacaoFinal $alegacaoFinal
     */
    public function setAlegacaoFinal($alegacaoFinal): void
    {
        $this->alegacaoFinal = $alegacaoFinal;
    }

    /**
     * @return ParecerFinal
     */
    public function getParecerFinal(): ?ParecerFinal
    {
        return $this->parecerFinal;
    }

    /**
     * @param ParecerFinal $parecerFinal
     */
    public function setParecerFinal(?ParecerFinal $parecerFinal): void
    {
        $this->parecerFinal = $parecerFinal;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getDenunciaProvas()
    {
        return $this->denunciaProvas;
    }

    /**
     * @param array|ArrayCollection $denunciaProvas
     */
    public function setDenunciaProvas($denunciaProvas): void
    {
        $this->denunciaProvas = $denunciaProvas;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getAudienciaInstrucao()
    {
        return $this->audienciaInstrucao;
    }

    /**
     * @param array|ArrayCollection $audienciaInstrucao
     */
    public function setAudienciaInstrucao($audienciaInstrucao): void
    {
        $this->audienciaInstrucao = $audienciaInstrucao;
    }

    /**
     * Método para retirar os binários de arquivos
     */
    public function removerFiles()
    {
        if (!empty($this->arquivoEncaminhamento)) {
            foreach ($this->arquivoEncaminhamento as $arquivo) {
                $arquivo->setArquivo(null);
            }
        }
    }
}
