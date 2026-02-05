<?php
/*
 * ImpugnacaoResultado.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use DateTime;
use Exception;
use Illuminate\Support\Arr;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação da ImpugnacaoResultado
 *
 * @ORM\Entity(repositoryClass="App\Repository\ImpugnacaoResultadoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_PEDIDO_IMPUGNACAO_RESULTADO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ImpugnacaoResultado extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_PEDIDO_IMPUGNACAO_RESULTADO_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="NARRACAO_FATOS", type="string",nullable=false)
     *
     * @var string
     */
    private $narracao;

    /**
     * @ORM\Column(name="NUMERO", type="integer", nullable=false)
     *
     * @var integer
     */
    private $numero;

    /**
     * @ORM\Column(name="DT_CADASTRO", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $dataCadastro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Filial")
     * @ORM\JoinColumn(name="ID_CAU_BR", referencedColumnName="id", nullable=true)
     *
     * @var Filial
     */
    private $cauBR;

    /**
     * @ORM\Column(name="NM_ARQUIVO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $nomeArquivo;

    /**
     * @ORM\Column(name="NM_FIS_ARQUIVO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $nomeArquivoFisico;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_PROFISSIONAL_INCLUSAO", referencedColumnName="id", nullable=false)
     *
     * @var Profissional
     */
    private $profissional;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\StatusImpugnacaoResultado")
     * @ORM\JoinColumn(name="ID_STATUS_IMPUGNACAO_RESULTADO", referencedColumnName="id", nullable=false)
     *
     * @var StatusImpugnacaoResultado
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Calendario")
     * @ORM\JoinColumn(name="ID_CALENDARIO", referencedColumnName="ID_CALENDARIO", nullable=false)
     *
     * @var Calendario
     */
    private $calendario;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoAlegacaoImpugResultado", mappedBy="impugnacaoResultado", fetch="EXTRA_LAZY")
     *
     * @var JulgamentoAlegacaoImpugResultado
     */
    private $julgamentoAlegacao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoRecursoImpugResultado", mappedBy="impugnacaoResultado", fetch="EXTRA_LAZY")
     * @var JulgamentoRecursoImpugResultado
     */
    private $julgamentoRecurso;

    /**
     *  @ORM\OneToMany(targetEntity="App\Entities\AlegacaoImpugnacaoResultado", mappedBy="impugnacaoResultado", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $alegacoes;

    /**
     * @var int|null $tipoValidacao
     */
    private $tipoValidacao;

    /**
     * Fábrica de instância de 'ImpugnacaoResultado'.
     *
     * @param array $data
     * @return ImpugnacaoResultado
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $impugnacaoResultado = new ImpugnacaoResultado();

        if ($data != null) {
            $impugnacaoResultado->setId(Utils::getValue('id', $data));
            $impugnacaoResultado->setNumero(Utils::getValue('numero', $data));
            $impugnacaoResultado->setNarracao(Utils::getValue('narracao', $data));
            $impugnacaoResultado->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $impugnacaoResultado->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $impugnacaoResultado->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $profissional = Utils::getValue('profissional', $data);
            if(!empty($profissional)) {
                $impugnacaoResultado->setProfissional(Profissional::newInstance($profissional));
            }

            $filial = Utils::getValue('cauBR', $data);
            if (!empty($filial)) {
                $impugnacaoResultado->setCauBR(Filial::newInstance($filial));
            }

            $status = Arr::get($data, 'status');
            if(!empty($status)) {
                $impugnacaoResultado->setStatus(StatusImpugnacaoResultado::newInstance($status));
            }

            $calendario = Arr::get($data, 'calendario');
            if(!empty($calendario)) {
                $impugnacaoResultado->setCalendario(Calendario::newInstance($calendario));
            }

            $julgamentoAlegacao = JulgamentoAlegacaoImpugResultado::newInstance(Arr::get($data, 'julgamentoAlegacao'));
            if(!empty(Arr::get($data, 'julgamentoAlegacao'))) {
                $impugnacaoResultado->setJulgamentoAlegacao($julgamentoAlegacao);
            }

            $alegacoes = !empty(Arr::get($data, 'alegacao')) ? array_map(
                function ($data) {
                    return AlegacaoImpugnacaoResultado::newInstance($data);
                },
                Arr::get($data, 'alegacao')
            ) : [];
            $impugnacaoResultado->setAlegacoes($alegacoes);

            $julgamentoRecurso = JulgamentoRecursoImpugResultado::newInstance(Arr::get($data, 'julgamentoRecurso'));
            if(!empty(Arr::get($data, 'julgamentoRecurso'))) {
                $impugnacaoResultado->setJulgamentoRecurso($julgamentoRecurso);
            }
        }
        return $impugnacaoResultado;
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
     * @return string
     */
    public function getNarracao()
    {
        return $this->narracao;
    }

    /**
     * @param string $narracao
     */
    public function setNarracao($narracao): void
    {
        $this->narracao = $narracao;
    }

    /**
     * @return DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @param DateTime $dataCadastro
     */
    public function setDataCadastro($dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return Filial
     */
    public function getCauBR()
    {
        return $this->cauBR;
    }

    /**
     * @param Filial $cauBR
     */
    public function setCauBR($cauBR): void
    {
        $this->cauBR = $cauBR;
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
    public function setNomeArquivo($nomeArquivo): void
    {
        $this->nomeArquivo = $nomeArquivo;
    }

    /**
     * @return string
     */
    public function getNomeArquivoFisico()
    {
        return $this->nomeArquivoFisico;
    }

    /**
     * @param string $nomeArquivoFisico
     */
    public function setNomeArquivoFisico($nomeArquivoFisico): void
    {
        $this->nomeArquivoFisico = $nomeArquivoFisico;
    }

    /**
     * @return Profissional
     */
    public function getProfissional(): ? Profissional
    {
        return $this->profissional;
    }

    /**
     * @param Profissional $profissional
     */
    public function setProfissional($profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return StatusImpugnacaoResultado
     */
    public function getStatus(): ? StatusImpugnacaoResultado
    {
        return $this->status;
    }

    /**
     * @param StatusImpugnacaoResultado $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return Calendario
     */
    public function getCalendario(): ? Calendario
    {
        return $this->calendario;
    }

    /**
     * @param Calendario $calendario
     */
    public function setCalendario($calendario): void
    {
        $this->calendario = $calendario;
    }

    /**
     * @return int|null
     */
    public function getTipoValidacao()
    {
        return $this->tipoValidacao;
    }

    /**
     * @param int|null $tipoValidacao
     */
    public function setTipoValidacao($tipoValidacao): void
    {
        $this->tipoValidacao = $tipoValidacao;
    }

    /**
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param int $numero
     */
    public function setNumero($numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return JulgamentoAlegacaoImpugResultado
     */
    public function getJulgamentoAlegacao(): ? JulgamentoAlegacaoImpugResultado
    {
        return $this->julgamentoAlegacao;
    }

    /**
     * @param JulgamentoAlegacaoImpugResultado $julgamentoAlegacao
     */
    public function setJulgamentoAlegacao($julgamentoAlegacao): void
    {
        $this->julgamentoAlegacao = $julgamentoAlegacao;
    }

    /**
     * @return AlegacaoImpugnacaoResultado
     */
    public function getAlegacoes()
    {
        return $this->alegacoes;
    }

    /**
     * @param AlegacaoImpugnacaoResultado $alegacoes
     */
    public function setAlegacoes($alegacoes): void
    {
        $this->alegacoes = $alegacoes;
    }

    /**
     * @return JulgamentoRecursoImpugResultado
     */
    public function getJulgamentoRecurso(): ?JulgamentoRecursoImpugResultado
    {
        return $this->julgamentoRecurso;
    }

    /**
     * @param JulgamentoRecursoImpugResultado $julgamentoRecurso
     */
    public function setJulgamentoRecurso(?JulgamentoRecursoImpugResultado $julgamentoRecurso): void
    {
        $this->julgamentoRecurso = $julgamentoRecurso;
    }

}
