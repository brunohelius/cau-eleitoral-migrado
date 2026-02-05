<?php

namespace App\Entities;

use App\Util\Utils;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class JulgamentoRecursoAdmissibilidade
 * @package App\Entities
 *
 * @ORM\Table(name="tb_julgamento_recursojulgamentoadmissibilidade", schema="eleitoral")
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoRecursoAdmissibilidadeRepository")
 */
class JulgamentoRecursoAdmissibilidade extends Entity
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_julgamento_recursojulgamen_id_julgamento_recursojulgamen_seq", )
     * @ORM\Column(name="id_julgamento_recursojulgamento", type="integer")
     */
    private $id;

    /**
     * @var RecursoJulgamentoAdmissibilidade
     *
     * @ORM\OneToOne(targetEntity="RecursoJulgamentoAdmissibilidade")
     * @ORM\JoinColumn(name="recurso_julgadmissibilidade_id", referencedColumnName="id_recurso_julgamento")
     */
    private $recursoAdmissibilidade;

    /**
     * @var ParecerJulgamentoRecursoAdmissibilidade
     *
     * @ORM\OneToOne(targetEntity="ParecerJulgamentoRecursoAdmissibilidade")
     * @ORM\JoinColumn(name="parecer_id", referencedColumnName="id")
     */
    private $parecer;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_julgamento", type="string")
     */
    private $descricao;

    /**
     * @ORM\Column(name="data", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $data;

    /**
     * @ORM\Column(name="usuario_id", type="integer")
     *
     * @var int
     */
    private $usuario;

    /**
     * @var Collection|ArquivoJulgamentoRecursoAdmissibilidade[]
     *
     * @ORM\OneToMany(targetEntity="ArquivoJulgamentoRecursoAdmissibilidade", mappedBy="julgamentoRecursoAdmissibilidade", cascade={"all"})
     */
    private $arquivos;


    /**
     * JulgamentoRecursoAdmissibilidade constructor.
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

        $parecer =  Utils::getValue('parecer', $data);
        if ($parecer) {
            $self->setParecer(ParecerJulgamentoRecursoAdmissibilidade::newInstance($parecer));
        }


        $arquivos = Utils::getValue('arquivos', $data);

        if ($arquivos) {
            $files = [];
            foreach ($arquivos as $arquivo) {
                $files[] = ArquivoJulgamentoRecursoAdmissibilidade::newInstance($arquivo);
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
     * @return JulgamentoRecursoAdmissibilidade
     */
    public function setId($id): JulgamentoRecursoAdmissibilidade
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return RecursoJulgamentoAdmissibilidade
     */
    public function getRecursoAdmissibilidade(): ?RecursoJulgamentoAdmissibilidade
    {
        return $this->recursoAdmissibilidade;
    }

    /**
     * @param RecursoJulgamentoAdmissibilidade $recursoAdmissibilidade
     * @return JulgamentoRecursoAdmissibilidade
     */
    public function setRecursoAdmissibilidade(RecursoJulgamentoAdmissibilidade $recursoAdmissibilidade): JulgamentoRecursoAdmissibilidade
    {
        $this->recursoAdmissibilidade = $recursoAdmissibilidade;
        return $this;
    }

    /**
     * @return ParecerJulgamentoRecursoAdmissibilidade
     */
    public function getParecer(): ParecerJulgamentoRecursoAdmissibilidade
    {
        return $this->parecer;
    }

    /**
     * @param ParecerJulgamentoRecursoAdmissibilidade $parecer
     * @return JulgamentoRecursoAdmissibilidade
     */
    public function setParecer(ParecerJulgamentoRecursoAdmissibilidade $parecer): JulgamentoRecursoAdmissibilidade
    {
        $this->parecer = $parecer;
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
     * @return JulgamentoRecursoAdmissibilidade
     */
    public function setDescricao($descricao): JulgamentoRecursoAdmissibilidade
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return ArquivoJulgamentoRecursoAdmissibilidade[]|Collection
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param ArquivoJulgamentoRecursoAdmissibilidade[]|Collection $arquivos
     * @return JulgamentoRecursoAdmissibilidade
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
     * @return JulgamentoRecursoAdmissibilidade
     */
    public function setData($data): JulgamentoRecursoAdmissibilidade
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return int
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param int $usuario
     * @return JulgamentoRecursoAdmissibilidade
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
        return $this;
    }


}
