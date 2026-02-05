<?php

namespace App\Entities;

use App\Util\Utils;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class JulgamentoAdmissibilidade
 * @package App\Entities
 *
 * @ORM\Table(name="tb_julgamento_admissibilidade", schema="eleitoral")
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoAdmissibilidadeRepository")
 */
class JulgamentoAdmissibilidade extends Entity
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_julgamento_admissibilidade_seq", )
     * @ORM\Column(name="id_julg_admissibilidade", type="integer")
     */
    private $id;

    /**
     * @var Denuncia
     *
     * @ORM\ManyToOne(targetEntity="Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA")
     */
    private $denuncia;

    /**
     * @var TipoJulgamentoAdmissibilidade
     *
     * @ORM\ManyToOne(targetEntity="TipoJulgamentoAdmissibilidade")
     * @ORM\JoinColumn(name="id_tipo_julg_admissibilidade", referencedColumnName="id_tipo_julg_admissibilidade")
     */
    private $tipoJulgamento;

    /**
     * @var Collection|ArquivoJulgamentoAdmissibilidade[]
     *
     * @ORM\OneToMany(targetEntity="ArquivoJulgamentoAdmissibilidade", mappedBy="julgamento", cascade={"all"})
     */
    private $arquivos;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_solicitacao", type="string")
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_usuario_criacao", type="integer")
     */
    private $criadoPor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_criacao", type="datetime")
     */
    private $dataCriacao;

    /**
     * @var RecursoJulgamentoAdmissibilidade
     * @ORM\OneToOne(targetEntity="RecursoJulgamentoAdmissibilidade", mappedBy="julgamentoAdmissibilidade")
     */
    private $recursoJulgamento;

    /**
     * JulgamentoDenuncia constructor.
     */
    public function __construct()
    {
        $this->arquivos = new ArrayCollection();
        $this->dataCriacao = new \DateTime();
    }

    public static function newInstance($data = null )
    {
        $self = new self();
        $id = Utils::getValue('id', $data);
        if ($id) {
            $self->setId($id);
        }
        $denuncia = Utils::getValue('denuncia', $data);
        if ($denuncia) {
            $self->setDenuncia($denuncia);
        }
        $tipoJulgamento = Utils::getValue('tipoJulgamento', $data);
        if ($tipoJulgamento) {
            $self->setTipoJulgamento(TipoJulgamentoAdmissibilidade::newInstance($tipoJulgamento));
        }
        $descricao = Utils::getValue('descricao', $data);
        if ($descricao) {
            $self->setDescricao($descricao);
        }
        $criadoPor = Utils::getValue('criadoPor', $data);
        if ($criadoPor) {
            $self->setCriadoPor($criadoPor);
        }
        $dataCriacao = Utils::getValue('dataCriacao', $data);
        if ($dataCriacao) {
            $self->setDataCriacao($dataCriacao);
        }

        $recursoAdmissibilidade = Utils::getValue("recursoJulgamento", $data);
        if($recursoAdmissibilidade) {
            $self->setRecursoJulgamento(RecursoJulgamentoAdmissibilidade::newInstance($recursoAdmissibilidade));
        }

        $arquivos = Utils::getValue('arquivos', $data);
        if ($arquivos) {
            foreach ($arquivos as $arquivo) {
                $self->addArquivo(ArquivoJulgamentoAdmissibilidade::newInstance($arquivo));
            }
        }
        return $self;
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
     * @return JulgamentoAdmissibilidade
     */
    public function setId(int $id): JulgamentoAdmissibilidade
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Denuncia
     */
    public function getDenuncia(): ?Denuncia
    {
        return $this->denuncia;
    }

    /**
     * @param Denuncia $denuncia
     * @return JulgamentoAdmissibilidade
     */
    public function setDenuncia(Denuncia $denuncia): JulgamentoAdmissibilidade
    {
        $this->denuncia = $denuncia;
        return $this;
    }

    /**
     * @return TipoJulgamentoAdmissibilidade
     */
    public function getTipoJulgamento(): TipoJulgamentoAdmissibilidade
    {
        return $this->tipoJulgamento;
    }

    /**
     * @param TipoJulgamentoAdmissibilidade $tipoJulgamento
     * @return JulgamentoAdmissibilidade
     */
    public function setTipoJulgamento(TipoJulgamentoAdmissibilidade $tipoJulgamento): JulgamentoAdmissibilidade
    {
        $this->tipoJulgamento = $tipoJulgamento;
        return $this;
    }

    /**
     * @return ArquivoJulgamentoAdmissibilidade[]|Collection
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param ArquivoJulgamentoAdmissibilidade $arquivo
     * @return $this
     */
    public function addArquivo(ArquivoJulgamentoAdmissibilidade $arquivo)
    {
        $arquivo->setJulgamento($this);
        $this->arquivos->add($arquivo);
        return $this;
    }

    /**
     * @param ArquivoJulgamentoAdmissibilidade $arquivo
     * @return $this
     */
    public function removeArquivo(ArquivoJulgamentoAdmissibilidade $arquivo)
    {
        $this->arquivos->removeElement($arquivo);
        return $this;
    }

    /**
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     * @return JulgamentoAdmissibilidade
     */
    public function setDescricao(string $descricao): JulgamentoAdmissibilidade
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDataCriacao()
    {
        return $this->dataCriacao;
    }

    /**
     * @param \DateTime $dataCriacao
     */
    public function setDataCriacao($dataCriacao)
    {
        $this->dataCriacao = $dataCriacao;
        return $this;
    }

    /**
     * @return RecursoJulgamentoAdmissibilidade
     */
    public function getRecursoJulgamento()
    {
        return $this->recursoJulgamento;
    }

    /**
     * @param RecursoJulgamentoAdmissibilidade $recursoJulgamento
     * @return JulgamentoAdmissibilidade
     */
    public function setRecursoJulgamento($recursoJulgamento)
    {
        $this->recursoJulgamento = $recursoJulgamento;
        return $this;
    }

    /**
     * @return int
     */
    public function getCriadoPor(): int
    {
        return $this->criadoPor;
    }

    /**
     * @param int $criadoPor
     * @return JulgamentoAdmissibilidade
     */
    public function setCriadoPor(int $criadoPor): JulgamentoAdmissibilidade
    {
        $this->criadoPor = $criadoPor;
        return $this;
    }

}
