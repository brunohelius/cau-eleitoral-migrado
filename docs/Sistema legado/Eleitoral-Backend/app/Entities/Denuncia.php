<?php
/*
 * Denuncia.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Denuncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class Denuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_denuncia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Pessoa")
     * @ORM\JoinColumn(name="ID_PESSOA", referencedColumnName="id", nullable=false)
     * @var Pessoa
     */
    private $pessoa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoDenuncia")
     * @ORM\JoinColumn(name="ID_TIPO_DENUNCIA", referencedColumnName="ID_TIPO_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\TipoDenuncia
     */
    private $tipoDenuncia;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\AtividadeSecundariaCalendario")
     * @ORM\JoinColumn(name="ID_ATIV_SECUNDARIA", referencedColumnName="ID_ATIV_SECUNDARIA", nullable=false)
     *
     * @var \App\Entities\AtividadeSecundariaCalendario
     */
    private $atividadeSecundaria;

    /**
     * @ORM\Column(name="DS_FATOS", type="string", length=2000, nullable=false)
     *
     * @var string
     */
    private $descricaoFatos;

    /**
     * @ORM\Column(name="SQ_DENUNCIA", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $numeroSequencial;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Filial")
     * @ORM\JoinColumn(name="id_cau_uf", referencedColumnName="id", nullable=true)
     * @var \App\Entities\Filial|null
     */
    private $filial;

    /**
     * @ORM\Column(name="DT_DENUNCIA", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $dataHora;

    /**
     * @ORM\Column(name="ST_DENUNCIA", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $status;

    /**
     * @ORM\Column(name="ST_SIGILO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $stSigilo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\TestemunhaDenuncia", mappedBy="denuncia")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $testemunhas;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\DenunciaOutro", mappedBy="denuncia")
     *
     * @var \App\Entities\DenunciaOutro
     */
    private $denunciaOutros;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\DenunciaChapa", mappedBy="denuncia")
     *
     * @var \App\Entities\DenunciaChapa
     */
    private $denunciaChapa;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\DenunciaMembroChapa", mappedBy="denuncia")
     *
     * @var \App\Entities\DenunciaMembroChapa
     */
    private $denunciaMembroChapa;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\DenunciaMembroComissao", mappedBy="denuncia")
     *
     * @var \App\Entities\DenunciaMembroComissao
     */
    private $denunciaMembroComissao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoDenuncia", mappedBy="denuncia")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $arquivoDenuncia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\DenunciaSituacao", mappedBy="denuncia")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $denunciaSituacao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\HistoricoDenuncia", mappedBy="denuncia")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $historico;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\DenunciaAdmitida", mappedBy="denuncia")
     *
     * @var null|array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $denunciaAdmitida;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\DenunciaInadmitida", mappedBy="denuncia", fetch="EXTRA_LAZY")
     *
     * @var \App\Entities\DenunciaInadmitida|null
     */
    private $denunciaInadmitida;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\DenunciaDefesa", mappedBy="denuncia", fetch="EXTRA_LAZY")
     *
     * @var \App\Entities\DenunciaDefesa|null
     */
    private $denunciaDefesa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\JulgamentoDenuncia", mappedBy="denuncia")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $julgamentoDenuncia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\EncaminhamentoDenuncia", mappedBy="denuncia")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $encaminhamentoDenuncia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\RecursoDenuncia", mappedBy="denuncia")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $recursoDenuncia;

    /**
     * @var JulgamentoAdmissibilidade|null
     *
     * @ORM\OneToOne(targetEntity="JulgamentoAdmissibilidade", mappedBy="denuncia")
     */
    private $julgamentoAdmissibilidade;

    /**
     * Transient
     *
     * @var int
     */
    private $idPessoa;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $isAssessorCE;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $isAssessorCEN;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $isRelatorAtual;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $isEleicaoVigente;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $hasDefesaPrazoEncerrado;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $hasAlegacaoFinalConcluido;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $hasAudienciaInstrucaoPendente;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $hasImpedimentoSuspeicaoPendente;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $hasParecerFinalInseridoParaDenuncia;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $hasAlegacaoFinalPendentePrazoEncerrado;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $hasPrazoRecursoDenuncia;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $hasContrarrazaoDenuncianteDentroPrazo;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $hasContrarrazaoDenunciadoDentroPrazo;

    /**
     * Transient
     *
     * @var bool|null
     */
    private $hasEncaminhamentoAlegacaoFinal;

    /**
     * Transient
     *
     * @var array|null
     */
    private $coordenadorComissao;

    /**
     * Transient
     *
     * @var array|null
     */
    private $impedimentoSuspeicao;

    /**
     * Fábrica de instância de 'Denuncia'.
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $denuncia = new Denuncia();

        if ($data !== null) {
            $denuncia->setId(Utils::getValue('id', $data));
            $denuncia->setStatus(Utils::getValue('status', $data));
            $denuncia->setIdPessoa(Utils::getValue('idPessoa', $data));
            $denuncia->setStSigilo(Utils::getBooleanValue('stSigilo', $data));
            $denuncia->setDescricaoFatos(Utils::getValue('descricaoFatos', $data));
            $denuncia->setNumeroSequencial(Utils::getValue('numeroSequencial', $data));

            $pessoa = Utils::getValue('pessoa', $data);
            if (!empty($pessoa)) {
                $denuncia->setPessoa(Pessoa::newInstance($pessoa));
            }

            $dataHora = Utils::getValue('dataHora', $data);
            if (!empty($dataHora)) {
                $denuncia->setDataHora($dataHora );
            }

            $atividadeSecundaria = Utils::getValue('atividadeSecundaria', $data);
            if (!empty($atividadeSecundaria)) {
                $denuncia->setAtividadeSecundaria(AtividadeSecundariaCalendario::newInstance($atividadeSecundaria));
            }

            $tipoDenuncia = Utils::getValue('tipoDenuncia', $data);
            if (!empty($tipoDenuncia)) {
                $denuncia->setTipoDenuncia(TipoDenuncia::newInstance($tipoDenuncia));
            }

            $testemunhas = Utils::getValue('testemunhas', $data);
            if (!empty($testemunhas)) {

                foreach ($testemunhas as $testemunha) {
                    $denuncia->adicionarTestemunhaDenuncia(
                        TestemunhaDenuncia::newInstance($testemunha)
                    );
                }
            }

            $denunciaOutros = Utils::getValue('denunciaOutros', $data);
            if (!empty($denunciaOutros)) {
                $denuncia->setDenunciaOutros(DenunciaOutro::newInstance($denunciaOutros));
            }

            $denunciaOutro = Utils::getValue('denunciaOutro', $data);
            if (!empty($denunciaOutro)) {
                $denuncia->setDenunciaOutros(DenunciaOutro::newInstance($denunciaOutro));
            }

            $denunciaChapa = Utils::getValue('denunciaChapa', $data);
            if (!empty($denunciaChapa)) {
                $denuncia->setDenunciaChapa(DenunciaChapa::newInstance($denunciaChapa));
            }

            $denunciaMembroChapa = Utils::getValue('denunciaMembroChapa', $data);
            if (!empty($denunciaMembroChapa)) {
                $denuncia->setDenunciaMembroChapa(DenunciaMembroChapa::newInstance($denunciaMembroChapa));
            }

            $denunciaMembroComissao = Utils::getValue('denunciaMembroComissao', $data);
            if (!empty($denunciaMembroComissao)) {
                $denuncia->setDenunciaMembroComissao(DenunciaMembroComissao::newInstance($denunciaMembroComissao));
            }

            $arquivosDenuncia = Utils::getValue('arquivosDenuncia', $data);
            if (!empty($arquivosDenuncia)) {
                foreach ($arquivosDenuncia as $arquivoDenuncia) {
                    $denuncia->adicionarArquivoDenuncia(ArquivoDenuncia::newInstance($arquivoDenuncia));
                }
            }

            $denunciaSituacao = Utils::getValue('denunciaSituacao', $data);
            if (!empty($denunciaSituacao)) {
                foreach ($denunciaSituacao as $situacao) {
                    $denuncia->adicionarDenunciaSituacao(DenunciaSituacao::newInstance($situacao));
                }
            }

            $historicos = Utils::getValue('historico', $data);
            if (!empty($historicos)) {

                foreach ($historicos as $historico) {
                    $denuncia->adicionarHistorico(HistoricoDenuncia::newInstance($historico));
                }
            }

            $filial = Utils::getValue('filial', $data);
            if (!empty($filial)) {
                $denuncia->setFilial(Filial::newInstance($filial));
            }

            $denunciasAdmitida = Utils::getValue('denunciaAdmitida', $data);
            if (!empty($denunciasAdmitida)) {
                foreach ($denunciasAdmitida as $denunciaAdmitida) {
                    $denuncia->adicionarDenunciaAdmitida(DenunciaAdmitida::newInstance($denunciaAdmitida));
                }
            }

            $denunciaInadmitida = Utils::getValue('denunciaInadmitida', $data);
            if (!empty($denunciaInadmitida)) {
                $denuncia->setDenunciaInadmitida(DenunciaInadmitida::newInstance($denunciaInadmitida));
            }

            $denunciaDefesa = Utils::getValue('denunciaDefesa', $data);
            if (!empty($denunciaDefesa)) {
                $denuncia->setDenunciaDefesa(DenunciaDefesa::newInstance($denunciaDefesa));
            }

            $julgamentosDenuncia = Utils::getValue('julgamentoDenuncia', $data);
            if (!empty($julgamentosDenuncia)) {
                foreach ($julgamentosDenuncia as $julgamentoDenuncia) {
                    $denuncia->adicionarJulgamentoDenuncia(
                        JulgamentoDenuncia::newInstance($julgamentoDenuncia)
                    );
                }
            }

            $encaminhamentos = Utils::getValue('encaminhamentoDenuncia', $data);
            if (!empty($encaminhamentos)) {
                foreach ($encaminhamentos as $encaminhamento) {
                    $denuncia->adicionarEncaminhamento(
                        EncaminhamentoDenuncia::newInstance($encaminhamento)
                    );
                }
            }

            $recursosDenuncia = Utils::getValue('recursoDenuncia', $data);
            if (!empty($recursosDenuncia)) {
                foreach ($recursosDenuncia as $recursoDenuncia) {
                    $denuncia->adicionarRecursoDenuncia(RecursoDenuncia::newInstance($recursoDenuncia));
                }
            }

            $julgamentoAdmissibilidade = Utils::getValue('julgamentoAdmissibilidade', $data);
            if (!empty($julgamentoAdmissibilidade)) {
                $denuncia->setJulgamentoAdmissibilidade(
                    JulgamentoAdmissibilidade::newInstance($julgamentoAdmissibilidade)
                );
            }
        }

        return $denuncia;
    }

    /**
     * Adiciona o 'TestemunhaDenuncia' à sua respectiva coleção.
     *
     * @param TestemunhaDenuncia $testemunhaDenuncia
     */
    private function adicionarTestemunhaDenuncia(TestemunhaDenuncia $testemunhaDenuncia)
    {
        if ($this->getTestemunhas() == null) {
            $this->setTestemunhas(new ArrayCollection());
        }

        if (!empty($testemunhaDenuncia)) {
            $testemunhaDenuncia->setDenuncia($this);
            $this->getTestemunhas()->add($testemunhaDenuncia);
        }
    }

    /**
     * Adiciona o 'ArquivoDenuncia' à sua respectiva coleção.
     *
     * @param ArquivoDenuncia $arquivoDenuncia
     */
    private function adicionarArquivoDenuncia(ArquivoDenuncia $arquivoDenuncia)
    {
        if ($this->getArquivoDenuncia() == null) {
            $this->setArquivoDenuncia(new ArrayCollection());
        }

        if (!empty($arquivoDenuncia)) {
            $arquivoDenuncia->setDenuncia($this);
            $this->getArquivoDenuncia()->add($arquivoDenuncia);
        }
    }

    /**
     * Adiciona o 'DenunciaSituacao' à sua respectiva coleção.
     *
     * @param DenunciaSituacao $denunciaSituacao
     */
    private function adicionarDenunciaSituacao(DenunciaSituacao $denunciaSituacao)
    {
        if ($this->getDenunciaSituacao() == null) {
            $this->setDenunciaSituacao(new ArrayCollection());
        }

        if (!empty($denunciaSituacao)) {
            $denunciaSituacao->setDenuncia($this);
            $this->getDenunciaSituacao()->add($denunciaSituacao);
        }
    }

    /**
     * Adiciona o 'DenunciaAdmitida' à sua respectiva coleção.
     *
     * @param DenunciaAdmitida $denunciaAdmitida
     */
    private function adicionarDenunciaAdmitida(DenunciaAdmitida $denunciaAdmitida): void
    {
        if ($this->getDenunciasAdmitidas() === null) {
            $this->setDenunciaAdmitida(new ArrayCollection());
        }

        if (null !== $denunciaAdmitida) {
            $denunciaAdmitida->setDenuncia($this);
            $this->getDenunciasAdmitidas()->add($denunciaAdmitida);
        }
    }

    /**
     * Adiciona o 'HistoricoDenuncia' à sua respectiva coleção.
     *
     * @param HistoricoDenuncia $historico
     */
    private function adicionarHistorico(HistoricoDenuncia $historico)
    {
        if ($this->getHistorico() == null) {
            $this->setHistorico(new ArrayCollection());
        }

        if (!empty($historico)) {
            $historico->setDenuncia($this);
            $this->getHistorico()->add($historico);
        }
    }

    /**
     * Adiciona o Encaminhamento à sua respectiva coleção
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     */
    private function adicionarEncaminhamento(EncaminhamentoDenuncia $encaminhamento)
    {
        if ($this->getEncaminhamentoDenuncia() == null) {
            $this->setEncaminhamentoDenuncia(new ArrayCollection());
        }

        if (!empty($encaminhamento)) {
            $encaminhamento->setDenuncia($this);
            $this->getEncaminhamentoDenuncia()->add($encaminhamento);
        }
    }

    /**
     * Adiciona o RecursoDenuncia à sua respectiva coleção
     *
     * @param RecursoDenuncia $recursoDenuncia
     */
    private function adicionarRecursoDenuncia(RecursoDenuncia $recursoDenuncia)
    {
        if ($this->getRecursoDenuncia() === null) {
            $this->setRecursoDenuncia(new ArrayCollection());
        }

        if ($recursoDenuncia !== null) {
            $recursoDenuncia->setDenuncia($this);
            $this->getRecursoDenuncia()->add($recursoDenuncia);
        }
    }

    /**
     * Adiciona o JulgamentoDenuncia à sua respectiva coleção
     *
     * @param JulgamentoDenuncia $julgamentoDenuncia
     */
    private function adicionarJulgamentoDenuncia(JulgamentoDenuncia $julgamentoDenuncia)
    {
        if ($this->getJulgamentoDenuncia() === null) {
            $this->setJulgamentoDenuncia(new ArrayCollection());
        }

        if ($julgamentoDenuncia !== null) {
            $julgamentoDenuncia->setDenuncia($this);
            $this->getJulgamentoDenuncia()->add($julgamentoDenuncia);
        }
    }

    /**
     * @return  integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  integer  $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return  Pessoa
     */
    public function getPessoa()
    {
        return $this->pessoa;
    }

    /**
     * @param  Pessoa  $pessoa
     */
    public function setPessoa($pessoa)
    {
        $this->pessoa = $pessoa;
    }

    /**
     * @return TipoDenuncia
     */
    public function getTipoDenuncia()
    {
        return $this->tipoDenuncia;
    }

    /**
     * @param  TipoDenuncia  $tipoDenuncia
     */
    public function setTipoDenuncia(TipoDenuncia $tipoDenuncia)
    {
        $this->tipoDenuncia = $tipoDenuncia;
    }

    /**
     * @return  AtividadeSecundariaCalendario
     */
    public function getAtividadeSecundaria()
    {
        return $this->atividadeSecundaria;
    }

    /**
     * @param  AtividadeSecundariaCalendario  $atividadeSecundaria
     */
    public function setAtividadeSecundaria(AtividadeSecundariaCalendario $atividadeSecundaria)
    {
        $this->atividadeSecundaria = $atividadeSecundaria;
    }

    /**
     * @return  string
     */
    public function getDescricaoFatos()
    {
        return $this->descricaoFatos;
    }

    /**
     * @param  $descricaoFatos
     */
    public function setDescricaoFatos($descricaoFatos)
    {
        $this->descricaoFatos = $descricaoFatos;
    }

    /**
     * @return  integer
     */
    public function getNumeroSequencial()
    {
        return $this->numeroSequencial;
    }

    /**
     * @param  integer  $numeroSequencial
     */
    public function setNumeroSequencial($numeroSequencial)
    {
        $this->numeroSequencial = $numeroSequencial;
    }

    /**
     * @return  \DateTime
     */
    public function getDataHora()
    {
        return $this->dataHora;
    }

    /**
     * @param  \DateTime  $dataHora
     */
    public function setDataHora(\DateTime $dataHora)
    {
        $this->dataHora = $dataHora;
    }

    /**
     * @return  integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param  integer  $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isSigiloso()
    {
        return $this->stSigilo ?? false;
    }

    /**
     * @param bool $stSigilo
     */
    public function setStSigilo($stSigilo): void
    {
        $this->stSigilo = $stSigilo;
    }

    /**
     * @return  array|ArrayCollection
     */
    public function getTestemunhas()
    {
        return $this->testemunhas;
    }

    /**
     * @param  array|ArrayCollection  $testemunhas
     */
    public function setTestemunhas($testemunhas)
    {
        $this->testemunhas = $testemunhas;
    }

    /**
     * @return  \App\Entities\DenunciaOutro
     */
    public function getDenunciaOutros()
    {
        return $this->denunciaOutros;
    }

    /**
     * @param DenunciaOutro  $denunciaOutros
     */
    public function setDenunciaOutros($denunciaOutros)
    {
        $this->denunciaOutros = $denunciaOutros;
    }

    /**
     * @return DenunciaChapa
     */
    public function getDenunciaChapa()
    {
        return $this->denunciaChapa;
    }

    /**
     * @param DenunciaChapa  $denunciaChapa
     */
    public function setDenunciaChapa($denunciaChapa)
    {
        $this->denunciaChapa = $denunciaChapa;
    }

    /**
     * @return DenunciaMembroChapa
     */
    public function getDenunciaMembroChapa()
    {
        return $this->denunciaMembroChapa;
    }

    /**
     * @param DenunciaMembroChapa $denunciaMembroChapa
     */
    public function setDenunciaMembroChapa($denunciaMembroChapa)
    {
        $this->denunciaMembroChapa = $denunciaMembroChapa;
    }

    /**
     * @return DenunciaMembroComissao
     */
    public function getDenunciaMembroComissao()
    {
        return $this->denunciaMembroComissao;
    }

    /**
     * @param DenunciaMembroComissao  $denunciaMembroComissao
     */
    public function setDenunciaMembroComissao($denunciaMembroComissao)
    {
        $this->denunciaMembroComissao = $denunciaMembroComissao;
    }

    /**
     * @return  array|ArrayCollection
     */
    public function getArquivoDenuncia()
    {
        return $this->arquivoDenuncia;
    }

    /**
     * @param  array|ArrayCollection  $arquivoDenuncia
     */
    public function setArquivoDenuncia($arquivoDenuncia)
    {
        $this->arquivoDenuncia = $arquivoDenuncia;
    }

    /**
     * @param int $idPessoa
     */
    public function setIdPessoa($idPessoa)
    {
        $this->idPessoa = $idPessoa;
    }

    /**
     * @return int
     */
    public function getIdPessoa()
    {
        return $this->idPessoa;
    }

    /**
     * @return bool
     */
    public function isRelatorAtual(): ?bool
    {
        return $this->isRelatorAtual;
    }

    /**
     * @param bool $isRelatorAtual
     */
    public function setIsRelatorAtual(bool $isRelatorAtual)
    {
        $this->isRelatorAtual = $isRelatorAtual;
    }

    /**
     * @return bool
     */
    public function isEleicaoVigente(): bool
    {
        return $this->isEleicaoVigente;
    }

    /**
     * @param bool $isEleicaoVigente
     */
    public function setIsEleicaoVigente(bool $isEleicaoVigente): void
    {
        $this->isEleicaoVigente = $isEleicaoVigente;
    }

    /**
     * @return bool|null
     */
    public function isAssessorCEUf(): ?bool
    {
        return $this->isAssessorCE;
    }

    /**
     * @param bool|null $isAssessorCE
     */
    public function setIsAssessorCEUf(?bool $isAssessorCE): void
    {
        $this->isAssessorCE = $isAssessorCE;
    }

    /**
     * @return bool|null
     */
    public function isAssessorCEN(): ?bool
    {
        return $this->isAssessorCEN;
    }

    /**
     * @param bool|null $isAssessorCEN
     */
    public function setIsAssessorCEN(?bool $isAssessorCEN): void
    {
        $this->isAssessorCEN = $isAssessorCEN;
    }

    /**
     * @return bool
     */
    public function hasDefesaPrazoEncerrado(): bool
    {
        return $this->hasDefesaPrazoEncerrado;
    }

    /**
     * @param bool $hasDefesaPrazoEncerrado
     */
    public function setHasDefesaPrazoEncerrado(bool $hasDefesaPrazoEncerrado
    ) {
        $this->hasDefesaPrazoEncerrado = $hasDefesaPrazoEncerrado;
    }

    /**
     * @return bool
     */
    public function hasAlegacaoFinalConcluido(): bool
    {
        return $this->hasAlegacaoFinalConcluido;
    }

    /**
     * @param bool $hasAlegacaoFinalConcluido
     */
    public function setHasAlegacaoFinalConcluido(bool $hasAlegacaoFinalConcluido)
    {
        $this->hasAlegacaoFinalConcluido = $hasAlegacaoFinalConcluido;
    }

    /**
     * @return bool
     */
    public function hasAudienciaInstrucaoPendente(): bool
    {
        return $this->hasAudienciaInstrucaoPendente;
    }

    /**
     * @param bool $hasAudienciaInstrucaoPendente
     */
    public function setHasAudienciaInstrucaoPendente(
        bool $hasAudienciaInstrucaoPendente
    ) {
        $this->hasAudienciaInstrucaoPendente = $hasAudienciaInstrucaoPendente;
    }

    /**
     * @return bool
     */
    public function hasImpedimentoSuspeicaoPendente(): bool
    {
        return $this->hasImpedimentoSuspeicaoPendente;
    }

    /**
     * @param bool $hasImpedimentoSuspeicaoPendente
     */
    public function setHasImpedimentoSuspeicaoPendente(
        bool $hasImpedimentoSuspeicaoPendente
    ) {
        $this->hasImpedimentoSuspeicaoPendente = $hasImpedimentoSuspeicaoPendente;
    }

    /**
     * @return bool
     */
    public function hasAlegacaoFinalPendentePrazoEncerrado(): bool
    {
        return $this->hasAlegacaoFinalPendentePrazoEncerrado;
    }

    /**
     * @param bool $hasAlegacaoFinalPendentePrazoEncerrado
     */
    public function setHasAlegacaoFinalPendentePrazoEncerrado(
        bool $hasAlegacaoFinalPendentePrazoEncerrado
    ): void {
        $this->hasAlegacaoFinalPendentePrazoEncerrado = $hasAlegacaoFinalPendentePrazoEncerrado;
    }

    /**
     * @return bool
     */
    public function hasParecerFinalInseridoParaDenuncia(): bool
    {
        return $this->hasParecerFinalInseridoParaDenuncia;
    }

    /**
     * @param bool $hasParecerFinalInseridoParaDenuncia
     */
    public function setHasParecerFinalInseridoParaDenuncia(
        bool $hasParecerFinalInseridoParaDenuncia
    ): void {
        $this->hasParecerFinalInseridoParaDenuncia = $hasParecerFinalInseridoParaDenuncia;
    }

    /**
     * @return bool|null
     */
    public function getHasPrazoRecursoDenuncia(): ?bool
    {
        return $this->hasPrazoRecursoDenuncia;
    }

    /**
     * @param bool|null $hasPrazoRecursoDenuncia
     */
    public function setHasPrazoRecursoDenuncia(?bool $hasPrazoRecursoDenuncia): void
    {
        $this->hasPrazoRecursoDenuncia = $hasPrazoRecursoDenuncia;
    }

    /**
     * @return bool|null
     */
    public function getHasContrarrazaoDenuncianteDentroPrazo(): ?bool
    {
        return $this->hasContrarrazaoDenuncianteDentroPrazo;
    }

    /**
     * @param bool|null $hasContrarrazaoDenuncianteDentroPrazo
     */
    public function setHasContrarrazaoDenuncianteDentroPrazo(?bool $hasContrarrazaoDenuncianteDentroPrazo): void
    {
        $this->hasContrarrazaoDenuncianteDentroPrazo = $hasContrarrazaoDenuncianteDentroPrazo;
    }

    /**
     * @return bool|null
     */
    public function getHasContrarrazaoDenunciadoDentroPrazo(): ?bool
    {
        return $this->hasContrarrazaoDenunciadoDentroPrazo;
    }

    /**
     * @param bool|null $hasContrarrazaoDenunciadoDentroPrazo
     */
    public function setHasContrarrazaoDenunciadoDentroPrazo(?bool $hasContrarrazaoDenunciadoDentroPrazo): void
    {
        $this->hasContrarrazaoDenunciadoDentroPrazo = $hasContrarrazaoDenunciadoDentroPrazo;
    }

    /**
     * Método para retirar os binários de arquivos
     */
    public function removerFiles()
    {
        if (!empty($this->arquivoDenuncia)) {
            foreach ($this->arquivoDenuncia as $arquivo) {
                $arquivo->setArquivo(null);
            }
        }
    }

    /**
     * @return  array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getDenunciaSituacao()
    {
        return $this->denunciaSituacao;
    }

    /**
     * @param  array|\Doctrine\Common\Collections\ArrayCollection  $denunciaSituacao
     */
    public function setDenunciaSituacao($denunciaSituacao)
    {
        $this->denunciaSituacao = $denunciaSituacao;
    }

    /**
     * @return  array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getHistorico()
    {
        return $this->historico;
    }

    /**
     * @param  array|\Doctrine\Common\Collections\ArrayCollection  $historico
     */
    public function setHistorico($historico)
    {
        $this->historico = $historico;
    }

    /**
     * @return  \App\Entities\Filial|null
     */
    public function getFilial()
    {
        return $this->filial;
    }

    /**
     * @param  \App\Entities\Filial  $filial
     */
    public function setFilial($filial)
    {
        $this->filial = $filial;
    }

    /**
     * @return  null|array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getDenunciasAdmitidas()
    {
        return $this->denunciaAdmitida;
    }

    /**
     * @return DenunciaAdmitida|null
     */
    public function getUltimaDenunciaAdmitida(): ?DenunciaAdmitida
    {
        $ultimaAdmissao = null;

        if (null !== $this->denunciaAdmitida && !$this->denunciaAdmitida->isEmpty()) {
            $iterator = $this->denunciaAdmitida->getIterator();

            $iterator->uasort(static function (DenunciaAdmitida $a, DenunciaAdmitida $b) {
                return ($a->getDataAdmissao() > $b->getDataAdmissao()) ? 1 : -1;
            });

            $denunciasAdmitidas = new ArrayCollection(iterator_to_array($iterator));
            $ultimaAdmissao = $denunciasAdmitidas->last();
        }

        return $ultimaAdmissao;
    }

    /**
     * Retorna a primeira admissão da denúncia
     *
     * @return DenunciaAdmitida|null
     * @throws \Exception
     */
    public function getPrimeiraDenunciaAdmitida()
    {
        $primeiraAdmissao = null;

        if (null !== $this->denunciaAdmitida && !$this->denunciaAdmitida->isEmpty()) {
            $iterator = $this->denunciaAdmitida->getIterator();

            $iterator->uasort(static function (DenunciaAdmitida $a, DenunciaAdmitida $b) {
                return ($a->getDataAdmissao() > $b->getDataAdmissao()) ? 1 : -1;
            });

            $denunciasAdmitidas = new ArrayCollection(iterator_to_array($iterator));
            $primeiraAdmissao = $denunciasAdmitidas->first();
        }

        return $primeiraAdmissao;
    }

    /**
     * @param  array|\Doctrine\Common\Collections\ArrayCollection  $denunciaAdmitida
     */
    public function setDenunciaAdmitida($denunciaAdmitida)
    {
        $this->denunciaAdmitida = $denunciaAdmitida;
    }

    /**
     * @return null|DenunciaInadmitida
     */
    public function getDenunciaInadmitida(): ?DenunciaInadmitida
    {
        return $this->denunciaInadmitida;
    }

    /**
     * @param DenunciaInadmitida $denunciaInadmitida
     */
    public function setDenunciaInadmitida(DenunciaInadmitida $denunciaInadmitida)
    {
        $this->denunciaInadmitida = $denunciaInadmitida;
    }

    /**
     * @return DenunciaDefesa|null
     */
    public function getDenunciaDefesa()
    {
        return $this->denunciaDefesa;
    }

    /**
     * @param DenunciaDefesa|null $denunciaDefesa
     */
    public function setDenunciaDefesa($denunciaDefesa)
    {
        $this->denunciaDefesa = $denunciaDefesa;
    }

    /**
     * @return array|ArrayCollection $julgamentoDenuncia
     */
    public function getJulgamentoDenuncia()
    {
        return $this->julgamentoDenuncia;
    }

    /**
     * @return JulgamentoDenuncia|null
     */
    public function getPrimeiroJulgamentoDenuncia()
    {
        $primeiroJulgamento = null;

        if (null !== $this->julgamentoDenuncia && !$this->julgamentoDenuncia->isEmpty()) {
            $iterator = $this->julgamentoDenuncia->getIterator();

            $iterator->uasort(static function (JulgamentoDenuncia $a, JulgamentoDenuncia $b) {
                return ($a->getData() > $b->getData()) ? 1 : -1;
            });

            $julgamentosDenuncia = new ArrayCollection(iterator_to_array($iterator));
            $primeiroJulgamento = $julgamentosDenuncia->first();
        }

        return $primeiroJulgamento;
    }

    /**
     * @return JulgamentoDenuncia|null
     */
    public function getUltimoJulgamentoDenuncia()
    {
        $ultimaJulgamento = null;

        if (null !== $this->julgamentoDenuncia && !$this->julgamentoDenuncia->isEmpty()) {
            $iterator = $this->julgamentoDenuncia->getIterator();

            $iterator->uasort(static function (JulgamentoDenuncia $a, JulgamentoDenuncia $b) {
                return ($a->getData() > $b->getData()) ? 1 : -1;
            });

            $julgamentosDenuncia = new ArrayCollection(iterator_to_array($iterator));
            $ultimaJulgamento = $julgamentosDenuncia->last();
        }

        return $ultimaJulgamento;
    }

    /**
     * @param array|ArrayCollection $julgamentoDenuncia
     */
    public function setJulgamentoDenuncia($julgamentoDenuncia): void
    {
        $this->julgamentoDenuncia = $julgamentoDenuncia;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getEncaminhamentoDenuncia()
    {
        return $this->encaminhamentoDenuncia;
    }

    /**
     * @param array|ArrayCollection $encaminhamentoDenuncia
     */
    public function setEncaminhamentoDenuncia($encaminhamentoDenuncia)
    {
        $this->encaminhamentoDenuncia = $encaminhamentoDenuncia;
    }

    public function getCoordenadorComissao()
    {
        return $this->coordenadorComissao;
    }

    public function setCoordenadorComissao($coordenadorComissao): void
    {
        $this->coordenadorComissao = $coordenadorComissao;
    }

    public function getImpedimentoSuspeicao()
    {
        return $this->impedimentoSuspeicao;
    }

    public function setImpedimentoSuspeicao($impedimentoSuspeicao): void
    {
        $this->impedimentoSuspeicao = $impedimentoSuspeicao;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getRecursoDenuncia()
    {
        return $this->recursoDenuncia;
    }

    /**
     * @param array|ArrayCollection $recursoDenuncia
     */
    public function setRecursoDenuncia($recursoDenuncia): void
    {
        $this->recursoDenuncia = $recursoDenuncia;
    }

    /**
     * @return bool|null
     */
    public function getHasEncaminhamentoAlegacaoFinal(): ?bool
    {
        return $this->hasEncaminhamentoAlegacaoFinal;
    }

    /**
     * @param bool|null $hasEncaminhamentoAlegacaoFinal
     */
    public function setHasEncaminhamentoAlegacaoFinal(?bool $hasEncaminhamentoAlegacaoFinal): void
    {
        $this->hasEncaminhamentoAlegacaoFinal = $hasEncaminhamentoAlegacaoFinal;
    }

    /**
     * @return JulgamentoAdmissibilidade|null
     */
    public function getJulgamentoAdmissibilidade(): ?JulgamentoAdmissibilidade
    {
        return $this->julgamentoAdmissibilidade;
    }

    /**
     * @param JulgamentoAdmissibilidade|null $julgamentoAdmissibilidade
     * @return Denuncia
     */
    public function setJulgamentoAdmissibilidade(?JulgamentoAdmissibilidade $julgamentoAdmissibilidade): Denuncia
    {
        $this->julgamentoAdmissibilidade = $julgamentoAdmissibilidade;
        return $this;
    }
}
