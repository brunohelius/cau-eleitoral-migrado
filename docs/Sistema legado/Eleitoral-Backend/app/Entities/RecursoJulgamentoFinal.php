<?php

namespace App\Entities;

use App\To\IndicacaoJulgamentoFinalTO;
use App\To\ProfissionalTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'RecursoJulgamentoFinal'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\RecursoJulgamentoFinalRepository")
 * @ORM\Table(schema="eleitoral", name="TB_RECURSO_JULGAMENTO_FINAL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class RecursoJulgamentoFinal extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_RECURSO_JULGAMENTO_FINAL_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DESCRICAO", type="string",nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="NM_ARQUIVO", type="string", length=200, nullable=true)
     *
     * @var string
     */
    private $nomeArquivo;

    /**
     * @ORM\Column(name="NM_FIS_ARQUIVO", type="string", length=200, nullable=true)
     *
     * @var string
     */
    private $nomeArquivoFisico;

    /**
     * @ORM\Column(name="DT_CADASTRO", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $dataCadastro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_PROFISSIONAL_INCLUSAO", referencedColumnName="id", nullable=false)
     * @var Profissional
     */
    private $profissional;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoFinal")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_FINAL", referencedColumnName="ID")
     *
     * @var JulgamentoFinal
     */
    private $julgamentoFinal;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\RecursoIndicacao", mappedBy="recursoJulgamentoFinal")
     *
     * @var array|ArrayCollection|null
     */
    private $recursosIndicacao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoSegundaInstanciaRecurso", mappedBy="recursoJulgamentoFinal", fetch="EXTRA_LAZY")
     *
     * @var JulgamentoSegundaInstanciaRecurso
     */
    private $julgamentoSegundaInstanciaRecurso;

    /**
     * Fábrica de instância de 'RecursoJulgamentoFinal'.
     *
     * @param array $data
     * @return RecursoJulgamentoFinal
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $recursoJulgamentoFinal = new RecursoJulgamentoFinal();

        if ($data != null) {
            $recursoJulgamentoFinal->setId(Utils::getValue('id', $data));
            $recursoJulgamentoFinal->setDescricao(Utils::getValue('descricao', $data));
            $recursoJulgamentoFinal->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $recursoJulgamentoFinal->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $recursoJulgamentoFinal->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $julgamentoFinal = Utils::getValue('julgamentoFinal', $data);
            if(!empty($julgamentoFinal)) {
                $recursoJulgamentoFinal->setJulgamentoFinal(JulgamentoFinal::newInstance($julgamentoFinal));
            }

            $profissional = Utils::getValue('profissional', $data);
            if(!empty($profissional)) {
                $recursoJulgamentoFinal->setProfissional(Profissional::newInstance($profissional));
            }

            $recursosIndicacao = Utils::getValue('recursosIndicacao', $data);
            if (!empty($recursosIndicacao)) {
                $recursos = [];
                foreach ($recursosIndicacao as $recursoIndicacao) {
                    array_push($recursos, RecursoIndicacao::newInstance($recursoIndicacao));
                }
                $recursoJulgamentoFinal->setRecursosIndicacao($recursos);
            }
        }
        return $recursoJulgamentoFinal;
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
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
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
     * @return Profissional
     */
    public function getProfissional()
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
     * @return JulgamentoFinal
     */
    public function getJulgamentoFinal(): JulgamentoFinal
    {
        return $this->julgamentoFinal;
    }

    /**
     * @param JulgamentoFinal $julgamentoFinal
     */
    public function setJulgamentoFinal(JulgamentoFinal $julgamentoFinal): void
    {
        $this->julgamentoFinal = $julgamentoFinal;
    }

    /**
     * @return array|ArrayCollection|null
     */
    public function getRecursosIndicacao()
    {
        return $this->recursosIndicacao;
    }

    /**
     * @param array|ArrayCollection|null $recursosIndicacao
     */
    public function setRecursosIndicacao($recursosIndicacao): void
    {
        $this->recursosIndicacao = $recursosIndicacao;
    }

    /**
     * @return JulgamentoSegundaInstanciaRecurso
     */
    public function getJulgamentoSegundaInstanciaRecurso()
    {
        return $this->julgamentoSegundaInstanciaRecurso;
    }

    /**
     * @param JulgamentoSegundaInstanciaRecurso $julgamentoSegundaInstanciaRecurso
     */
    public function setJulgamentoSegundaInstanciaRecurso($julgamentoSegundaInstanciaRecurso)
    {
        $this->julgamentoSegundaInstanciaRecurso = $julgamentoSegundaInstanciaRecurso;
    }
}
