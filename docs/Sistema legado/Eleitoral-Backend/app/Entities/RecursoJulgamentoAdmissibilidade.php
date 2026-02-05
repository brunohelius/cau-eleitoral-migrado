<?php

namespace App\Entities;

use App\Util\Utils;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class RecursoJulgamentoAdmissibilidade
 * @package App\Entities
 *
 * @ORM\Table(name="tb_recurso_julgamentoadmissibilidade", schema="eleitoral")
 * @ORM\Entity(repositoryClass="App\Repository\RecursoJulgamentoAdmissibilidadeRepository")
 */
class RecursoJulgamentoAdmissibilidade extends Entity
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_recurso_julgamentoadmissibilidade_id_recurso_julgamento_seq", )
     * @ORM\Column(name="id_recurso_julgamento", type="integer")
     */
    private $id;

    /**
     * @var JulgamentoAdmissibilidade
     *
     * @ORM\OneToOne(targetEntity="JulgamentoAdmissibilidade")
     * @ORM\JoinColumn(name="julg_admissibilidade_id", referencedColumnName="id_julg_admissibilidade")
     */
    private $julgamentoAdmissibilidade;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_recurso", type="string")
     */
    private $descricao;

    /**
     * @var Collection|ArquivoRecursoJulgamentoAdmissibilidade[]
     *
     * @ORM\OneToMany(targetEntity="ArquivoRecursoJulgamentoAdmissibilidade", mappedBy="recurso", cascade={"all"})
     */
    private $arquivos;

    /**
     * @ORM\Column(name="data", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $data;

    /**
     * @var JulgamentoRecursoAdmissibilidade
     * @ORM\OneToOne(targetEntity="JulgamentoRecursoAdmissibilidade", mappedBy="recursoAdmissibilidade")
     */
    private $julgamentoRecursoAdmissibilidade;

    /**
     * JulgamentoDenuncia constructor.
     */
    public function __construct()
    {
        $this->arquivos = new ArrayCollection();
        $this->data = new \DateTime();
    }

    public static function newInstance($data = null )
    {

        $self = new self();
        $id = Utils::getValue('id', $data);
        if ($id) {
            $self->setId($id);
        }

        $descricao = Utils::getValue('descricao', $data);
        if ($descricao) {
            $self->setDescricao($descricao);
        }

        $dataCriacao = Utils::getValue('data', $data);
        if ($dataCriacao) {
            $self->setData($dataCriacao);
        }

        $julgamentoRecursoAdmissibilidade = Utils::getValue("julgamentoRecursoAdmissibilidade", $data);
        if($julgamentoRecursoAdmissibilidade) {
            $self->setJulgamentoRecursoAdmissibilidade(JulgamentoRecursoAdmissibilidade::newInstance($julgamentoRecursoAdmissibilidade));
        }

        $arquivos = Utils::getValue('arquivos', $data);

        if ($arquivos) {
            $files = [];
            foreach ($arquivos as $arquivo) {
                $files[] = ArquivoRecursoJulgamentoAdmissibilidade::newInstance($arquivo);
            }
            $self->setArquivos($files);
        }

        return $self;
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
     * @return RecursoJulgamentoAdmissibilidade
     */
    public function setId($id): RecursoJulgamentoAdmissibilidade
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return JulgamentoAdmissibilidade
     */
    public function getJulgamentoAdmissibilidade(): ?JulgamentoAdmissibilidade
    {
        return $this->julgamentoAdmissibilidade;
    }

    /**
     * @param JulgamentoAdmissibilidade $julgamentoAdmissibilidade
     * @return RecursoJulgamentoAdmissibilidade
     */
    public function setJulgamentoAdmissibilidade(JulgamentoAdmissibilidade $julgamentoAdmissibilidade): RecursoJulgamentoAdmissibilidade
    {
        $this->julgamentoAdmissibilidade = $julgamentoAdmissibilidade;
        return $this;
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
     * @return RecursoJulgamentoAdmissibilidade
     */
    public function setDescricao($descricao): RecursoJulgamentoAdmissibilidade
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return ArquivoRecursoJulgamentoAdmissibilidade[]|Collection
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param ArquivoRecursoJulgamentoAdmissibilidade[]|Collection $arquivos
     * @return RecursoJulgamentoAdmissibilidade
     */
    public function setArquivos($arquivos)
    {
        $this->arquivos = $arquivos;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @param DateTime $data
     * @return RecursoJulgamentoAdmissibilidade
     */
    public function setData($data): RecursoJulgamentoAdmissibilidade
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return JulgamentoRecursoAdmissibilidade
     */
    public function getJulgamentoRecursoAdmissibilidade()
    {
        return $this->julgamentoRecursoAdmissibilidade;
    }

    /**
     * @param JulgamentoRecursoAdmissibilidade $julgamentoRecursoAdmissibilidade
     * @return RecursoJulgamentoAdmissibilidade
     */
    public function setJulgamentoRecursoAdmissibilidade($julgamentoRecursoAdmissibilidade)
    {
        $this->julgamentoRecursoAdmissibilidade = $julgamentoRecursoAdmissibilidade;
        return $this;
    }


}
