<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'Contrarrazão do Recurso'
 *
 * @ORM\Entity(repositoryClass="App\Repository\ContrarrazaoRecursoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_RECURSO_CONTRARRAZAO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ContrarrazaoRecursoDenuncia extends Entity
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
     * @ORM\ManyToOne(targetEntity="App\Entities\RecursoDenuncia", inversedBy="contrarrazao")
     * @ORM\JoinColumn(name="ID_RECURSO_DENUNCIA", referencedColumnName="ID_RECURSO_CONTRARRAZAO_DENUNCIA", nullable=false)
     *
     * @var RecursoDenuncia
     */
    private $recurso;

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

            $recurso = Utils::getValue('recurso', $data);
            if (!empty($recurso)) {
                $instance->setRecurso(RecursoDenuncia::newInstance($recurso));
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
    public function getDsRecurso(): string
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
     * @return RecursoDenuncia|null
     */
    public function getRecurso()
    {
        return $this->recurso;
    }

    /**
     * @param $recurso
     */
    public function setRecurso($recurso)
    {
        $this->recurso = $recurso;
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
     * Adiciona o 'ArquivoRecurso' à sua respectiva coleção.
     *
     * @param ArquivoRecursoContrarrazaoDenuncia $arquivoRecurso
     */
    private function addArquivosRecurso(ArquivoRecursoContrarrazaoDenuncia $arquivoRecurso)
    {
        if ($this->getArquivos() === null) {
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
