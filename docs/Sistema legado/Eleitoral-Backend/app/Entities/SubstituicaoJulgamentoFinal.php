<?php

namespace App\Entities;

use DateTime;
use Exception;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'SubstituicaoJulgamentoFinal'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\SubstituicaoJulgamentoFinalRepository")
 * @ORM\Table(schema="eleitoral", name="TB_SUBSTITUICAO_JULGAMENTO_FINAL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class SubstituicaoJulgamentoFinal extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_SUBSTITUICAO_JULGAMENTO_FINAL_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="JUSTIFICATIVA", type="text", nullable=false)
     *
     * @var string
     */
    private $justificativa;

    /**
     * @ORM\Column(name="DT_CADASTRO", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $dataCadastro;

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
     * @ORM\OneToMany(targetEntity="App\Entities\MembroSubstituicaoJulgamentoFinal", mappedBy="substituicaoJulgamentoFinal", cascade={"persist"}, fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $membrosSubstituicaoJulgamentoFinal;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoSegundaInstanciaSubstituicao", mappedBy="substituicaoJulgamentoFinal", fetch="EXTRA_LAZY")
     *
     * @var JulgamentoSegundaInstanciaSubstituicao
     */
    private $julgamentoSegundaInstanciaSubstituicao;

    /**
     * Fábrica de instância de 'SubstituicaoJulgamentoFinal'.
     *
     * @param array $data
     *
     * @return SubstituicaoJulgamentoFinal
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $instance->setJustificativa(Utils::getValue('justificativa', $data));
            $instance->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $instance->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $profissional = Utils::getValue('profissional', $data);
            if (!empty($profissional)) {
                $instance->setProfissional(Profissional::newInstance($profissional));
            }

            $julgamentoFinal = Utils::getValue('julgamentoFinal', $data);
            if(!empty($julgamentoFinal)) {
                $instance->setJulgamentoFinal(JulgamentoFinal::newInstance($julgamentoFinal));
            }

            $membrosSubstituicaoJulgamentoFinal = Utils::getValue('membrosSubstituicaoJulgamentoFinal', $data);
            if (!empty($membrosSubstituicaoJulgamentoFinal)) {
                $membrosSubstituicaoJulgamentoFinalEntity = [];
                foreach ($membrosSubstituicaoJulgamentoFinal as $membroSubstituicaoJulgamentoFinal) {
                    array_push(
                        $membrosSubstituicaoJulgamentoFinalEntity,
                        MembroSubstituicaoJulgamentoFinal::newInstance($membroSubstituicaoJulgamentoFinal)
                    );
                }
                $instance->setMembrosSubstituicaoJulgamentoFinal($membrosSubstituicaoJulgamentoFinalEntity);
            }
        }
        return $instance;
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
     * @return string
     */
    public function getJustificativa()
    {
        return $this->justificativa;
    }

    /**
     * @param string $justificativa
     */
    public function setJustificativa($justificativa): void
    {
        $this->justificativa = $justificativa;
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
     * @return array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getMembrosSubstituicaoJulgamentoFinal()
    {
        return $this->membrosSubstituicaoJulgamentoFinal;
    }

    /**
     * @param array|\Doctrine\Common\Collections\ArrayCollection $membrosSubstituicaoJulgamentoFinal
     */
    public function setMembrosSubstituicaoJulgamentoFinal($membrosSubstituicaoJulgamentoFinal): void
    {
        $this->membrosSubstituicaoJulgamentoFinal = $membrosSubstituicaoJulgamentoFinal;
    }

    /**
     * @return JulgamentoSegundaInstanciaSubstituicao
     */
    public function getJulgamentoSegundaInstanciaSubstituicao()
    {
        return $this->julgamentoSegundaInstanciaSubstituicao;
    }

    /**
     * @param JulgamentoSegundaInstanciaSubstituicao $julgamentoSegundaInstanciaSubstituicao
     */
    public function setJulgamentoSegundaInstanciaSubstituicao($julgamentoSegundaInstanciaSubstituicao): void {
        $this->julgamentoSegundaInstanciaSubstituicao = $julgamentoSegundaInstanciaSubstituicao;
    }
}
