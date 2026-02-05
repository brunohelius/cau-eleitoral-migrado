<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'Recurso'
 *
 * @ORM\Entity(repositoryClass="App\Repository\RecursoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_RECURSO_CONTRARRAZAO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class RecursoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_RECURSO_CONTRARRAZAO_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_recurso_contrarrazao_denuncia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_RECURSO_CONTRARRAZAO_DENUNCIA", type="text", nullable=false)
     * @var string
     */
    private $dsRecurso;

    /**
     * @ORM\Column(name="DT_RECURSO_CONTRARRAZAO_DENUNCIA", type="datetime", nullable=false)
     * @var \DateTime
     */
    private $dtRecurso;

    /**
     * @ORM\Column(name="ID_RECURSO_DENUNCIA", type="integer", nullable=true)
     * @var integer
     */
    private $recursoDenuncia;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ATOR_RECURSO_CONTRARRAZAO", referencedColumnName="id", nullable=false)
     *
     * @var Profissional
     */
    private $profissional;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\Denuncia
     */
    private $denuncia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\JulgamentoRecursoDenuncia", mappedBy="recursoDenunciado")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $julgamentoRecursoDenunciado;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\JulgamentoRecursoDenuncia", mappedBy="recursoDenunciante")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $julgamentoRecursoDenunciante;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\ContrarrazaoRecursoDenuncia", mappedBy="recurso")
     *
     * @var ContrarrazaoRecursoDenuncia|null
     */
    private $contrarrazao;

    /**
     * @ORM\Column(name="TP_RECURSO_CONTRARRAZAO_DENUNCIA", type="integer", nullable=false)
     * @var integer
     */
    private $tipoRecursoContrarrazaoDenuncia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoRecursoContrarrazaoDenuncia", mappedBy="recurso")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $arquivos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\StatusRecursoDenuncia", mappedBy="recursoDenuncia")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $statusRecursoDenuncia;

    /**
     * Transient
     */
    private $idDenuncia;

    /**
     * Fábrica de instância de 'Recurso'.
     *
     * @param array $data
     *
     * @return self
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data !== null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setDsRecurso(Utils::getValue('dsRecurso', $data));
            $instance->setDtRecurso(Utils::getValue('dtRecurso', $data));
            $instance->setIdDenuncia(Utils::getValue('idDenuncia', $data));
            $instance->setTipoRecursoContrarrazaoDenuncia(
                Utils::getValue('tipoRecursoContrarrazaoDenuncia', $data)
            );

            $profissional = Utils::getValue('profissional', $data);
            if (!empty($profissional)) {
                $instance->setProfissional(Profissional::newInstance($profissional));
            }

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $instance->setDenuncia(Denuncia::newInstance($denuncia));
            }

            $contrarrazao = Utils::getValue('contrarrazao', $data);
            if (!empty($contrarrazao)) {
                $instance->setContrarrazao(ContrarrazaoRecursoDenuncia::newInstance($contrarrazao));
            }

            $julgamentoRecursoDenunciado = Utils::getValue('julgamentoRecursoDenunciado', $data);
            if (!empty($julgamentoRecursoDenunciado)) {
                foreach ($julgamentoRecursoDenunciado as $julgamento) {
                    $instance->addJulgamentoRecursoDenunciado(JulgamentoRecursoDenuncia::newInstance($julgamento));
                }
            }

            $julgamentoRecursoDenunciante = Utils::getValue('julgamentoRecursoDenunciante', $data);
            if (!empty($julgamentoRecursoDenunciante)) {
                foreach ($julgamentoRecursoDenunciante as $julgamento) {
                    $instance->addJulgamentoRecursoDenunciante(JulgamentoRecursoDenuncia::newInstance($julgamento));
                }
            }

            $statusRecursoDenuncia = Utils::getValue('statusRecursoDenuncia', $data);
            if (!empty($statusRecursoDenuncia)) {
                foreach ($statusRecursoDenuncia as $statusRecurso) {
                    $instance->addStatusRecursoDenuncia(StatusRecursoDenuncia::newInstance($statusRecurso));
                }
            }

            $arquivosRecurso = Utils::getValue('arquivos', $data);
            if (!empty($arquivosRecurso)) {
                foreach ($arquivosRecurso as $arquivoRecurso) {
                    $instance->addArquivosRecurso(ArquivoRecursoContrarrazaoDenuncia::newInstance($arquivoRecurso));
                }
            }
        }

        return $instance;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDsRecurso()
    {
        return $this->dsRecurso;
    }

    /**
     * @param $dsRecurso
     */
    public function setDsRecurso($dsRecurso)
    {
        $this->dsRecurso = $dsRecurso;
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
     * @return \DateTime $dtRecurso
     */
    public function getDtRecurso()
    {
        return $this->dtRecurso;
    }

    /**
     * @param \DateTime $dtRecurso
     */
    public function setDtRecurso($dtRecurso)
    {
        $this->dtRecurso = $dtRecurso;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getJulgamentoRecursoDenunciado()
    {
        return $this->julgamentoRecursoDenunciado;
    }

    /**
     * @return JulgamentoRecursoDenuncia|null
     */
    public function getUltimoJulgamentoDenunciado()
    {
        $ultimoJulgamento = null;

        if (null !== $this->julgamentoRecursoDenunciado && !$this->julgamentoRecursoDenunciado->isEmpty()) {
            $iterator = $this->julgamentoRecursoDenunciado->getIterator();

            $iterator->uasort(static function (JulgamentoRecursoDenuncia $a, JulgamentoRecursoDenuncia $b) {
                return ($a->getData() > $b->getData()) ? 1 : -1;
            });

            $julgamentosRecurso = new ArrayCollection(iterator_to_array($iterator));
            $ultimoJulgamento = $julgamentosRecurso->last();
        }

        return $ultimoJulgamento;
    }

    /**
     * @param array|ArrayCollection $julgamentoRecursoDenunciado
     */
    public function setJulgamentoRecursoDenunciado($julgamentoRecursoDenunciado): void
    {
        $this->julgamentoRecursoDenunciado = $julgamentoRecursoDenunciado;
    }

    /**
     * Adiciona o 'JulgamentoRecursoDenuncia' à sua respectiva coleção.
     *
     * @param JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia
     */
    private function addJulgamentoRecursoDenunciado(JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia)
    {
        if (empty($this->getJulgamentoRecursoDenunciado())) {
            $this->setJulgamentoRecursoDenunciado(new ArrayCollection());
        }

        if ($julgamentoRecursoDenuncia !== null) {
            $julgamentoRecursoDenuncia->setRecursoDenunciado($this);
            $this->getJulgamentoRecursoDenunciado()->add($julgamentoRecursoDenuncia);
        }
    }

    /**
     * @return array|ArrayCollection
     */
    public function getJulgamentoRecursoDenunciante()
    {
        return $this->julgamentoRecursoDenunciante;
    }

    /**
     * @return JulgamentoRecursoDenuncia|null
     */
    public function getUltimoJulgamentoDenunciante()
    {
        $ultimoJulgamento = null;

        if (null !== $this->julgamentoRecursoDenunciante && !$this->julgamentoRecursoDenunciante->isEmpty()) {
            $iterator = $this->julgamentoRecursoDenunciante->getIterator();

            $iterator->uasort(static function (JulgamentoRecursoDenuncia $a, JulgamentoRecursoDenuncia $b) {
                return ($a->getData() > $b->getData()) ? 1 : -1;
            });

            $julgamentosRecurso = new ArrayCollection(iterator_to_array($iterator));
            $ultimoJulgamento = $julgamentosRecurso->last();
        }

        return $ultimoJulgamento;
    }

    /**
     * @param array|ArrayCollection $julgamentoRecursoDenunciante
     */
    public function setJulgamentoRecursoDenunciante($julgamentoRecursoDenunciante): void
    {
        $this->julgamentoRecursoDenunciante = $julgamentoRecursoDenunciante;
    }

    /**
     * Adiciona o 'JulgamentoRecursoDenuncia' à sua respectiva coleção.
     *
     * @param JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia
     */
    private function addJulgamentoRecursoDenunciante(JulgamentoRecursoDenuncia $julgamentoRecursoDenuncia)
    {
        if (empty($this->getJulgamentoRecursoDenunciante())) {
            $this->setJulgamentoRecursoDenunciante(new ArrayCollection());
        }

        if ($julgamentoRecursoDenuncia !== null) {
            $julgamentoRecursoDenuncia->setRecursoDenunciante($this);
            $this->getJulgamentoRecursoDenunciante()->add($julgamentoRecursoDenuncia);
        }
    }

    /**
     * @return ContrarrazaoRecursoDenuncia|null
     */
    public function getContrarrazao()
    {
        return $this->contrarrazao;
    }

    /**
     * @param $contrarrazao
     */
    public function setContrarrazao($contrarrazao)
    {
        $this->contrarrazao = $contrarrazao;
    }

    /**
     * @return int
     */
    public function getTipoRecursoContrarrazaoDenuncia()
    {
        return $this->tipoRecursoContrarrazaoDenuncia;
    }

    /**
     * @param int $tipoRecursoContrarrazaoDenuncia
     */
    public function setTipoRecursoContrarrazaoDenuncia($tipoRecursoContrarrazaoDenuncia): void
    {
        $this->tipoRecursoContrarrazaoDenuncia = $tipoRecursoContrarrazaoDenuncia;
    }

    /**
     * @return mixed
     */
    public function getIdDenuncia()
    {
        return $this->idDenuncia;
    }

    /**
     * @param mixed $idDenuncia
     */
    public function setIdDenuncia($idDenuncia)
    {
        $this->idDenuncia = $idDenuncia;
    }

    /**
     * @return int
     */
    public function getRecursoDenuncia()
    {
        return $this->recursoDenuncia;
    }

    /**
     * @param int $recursoDenuncia
     */
    public function setRecursoDenuncia($recursoDenuncia)
    {
        $this->recursoDenuncia = $recursoDenuncia;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param array|ArrayCollection $arquivos
     */
    public function setArquivos($arquivos): void
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return  array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getStatusRecursoDenuncia()
    {
        return $this->statusRecursoDenuncia;
    }

    /**
     * @param  array|\Doctrine\Common\Collections\ArrayCollection $statusRecursoDenuncia
     */
    public function setStatusRecursoDenuncia($statusRecursoDenuncia)
    {
        $this->statusRecursoDenuncia = $statusRecursoDenuncia;
    }

    /**
     * Adiciona o 'StatusRecursoDenuncia' à sua respectiva coleção.
     *
     * @param StatusRecursoDenuncia $statusRecursoDenuncia
     */
    private function addStatusRecursoDenuncia(StatusRecursoDenuncia $statusRecursoDenuncia)
    {
        if (empty($this->getStatusRecursoDenuncia())) {
            $this->setStatusRecursoDenuncia(new ArrayCollection());
        }

        if ($statusRecursoDenuncia !== null) {
            $statusRecursoDenuncia->setRecursoDenuncia($this);
            $this->getStatusRecursoDenuncia()->add($statusRecursoDenuncia);
        }
    }

    /**
     * Adiciona o 'ArquivoRecurso' à sua respectiva coleção.
     *
     * @param ArquivoRecursoContrarrazaoDenuncia $arquivoRecurso
     */
    private function addArquivosRecurso(ArquivoRecursoContrarrazaoDenuncia $arquivoRecurso)
    {
        if (empty($this->getArquivos())) {
            $this->setArquivos(new ArrayCollection());
        }

        if ($arquivoRecurso !== null) {
            $arquivoRecurso->setRecurso($this);
            $this->getArquivos()->add($arquivoRecurso);
        }
    }

    /**
     * Método para retirar os binários de arquivos
     */
    public function removerFiles()
    {
        if (!empty($this->arquivos)) {
            foreach ($this->arquivos as $arquivo) {
                $arquivo->setArquivo(null);
            }
        }
    }

}
