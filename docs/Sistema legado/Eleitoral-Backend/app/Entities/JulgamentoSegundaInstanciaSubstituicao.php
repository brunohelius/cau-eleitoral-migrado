<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'JulgamentoSegundaInstanciaSubstituicao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoSegundaInstanciaSubstituicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_JULGAMENTO_SEGUNDA_INSTANCIA_SUBSTITUICAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoSegundaInstanciaSubstituicao extends Entity
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_JULGAMENTO_SEGUNDA_INSTANCIA_SUBSTITUICAO_ID_SEQ", initialValue=1, allocationSize=1)
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
     * @ORM\Column(name="DT_CADASTRO", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $dataCadastro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\SubstituicaoJulgamentoFinal")
     * @ORM\JoinColumn(name="ID_SUBSTITUICAO_JULGAMENTO_FINAL", referencedColumnName="ID", nullable=false)
     *
     * @var SubstituicaoJulgamentoFinal
     */
    private $substituicaoJulgamentoFinal;

    /**
     * @ORM\ManyToOne(targetEntity="StatusJulgamentoFinal")
     * @ORM\JoinColumn(name="ID_STATUS_JULGAMENTO_FINAL", referencedColumnName="ID", nullable=false)
     *
     * @var StatusJulgamentoFinal
     */
    private $statusJulgamentoFinal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Usuario")
     * @ORM\JoinColumn(name="ID_USUARIO_INCLUSAO", referencedColumnName="id", nullable=false)
     * @var Usuario
     */
    private $usuario;


    /**
     * @ORM\OneToMany(targetEntity="App\Entities\RecursoSegundoJulgamentoSubstituicao", mappedBy="julgamentoSegundaInstanciaSubstituicao", fetch="EXTRA_LAZY")
     *
     * @var array|ArrayCollection|null
     */
    private $recursoSegundoJulgamentoSubstituicao;


    /**
     * @ORM\OneToMany(targetEntity="App\Entities\IndicacaoJulgamentoSegundaInstanciaSubstituicao", mappedBy="julgamentoSegundaInstanciaSubstituicao", cascade={"persist"}, fetch="EXTRA_LAZY")
     *
     * @var IndicacaoJulgamentoSegundaInstanciaSubstituicao[]|array|ArrayCollection|null
     */
    private $indicacoes;

    /**
     * @ORM\Column(name="retificacao_justificativa", type="string", length=1000, nullable=true)
     *
     * @var string
     */
    private $retificacaoJustificativa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\JulgamentoSegundaInstanciaSubstituicao")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_SEGUNDA_INSTANCIA_SUBSTITUICAO_PAI", referencedColumnName="ID")
     *
     * @var JulgamentoSegundaInstanciaSubstituicao
     */
    private $julgamentoSegundaInstanciaSubstituicaoPai;

    /**
     * Fábrica de instância de 'JulgamentoSegundaInstanciaSubstituicao'.
     *
     * @param array $data
     * @return JulgamentoSegundaInstanciaSubstituicao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoFinal = new JulgamentoSegundaInstanciaSubstituicao();

        if ($data != null) {
            $julgamentoFinal->setId(Utils::getValue('id', $data));
            $julgamentoFinal->setDescricao(Utils::getValue('descricao', $data));
            $julgamentoFinal->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $julgamentoFinal->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $julgamentoFinal->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));
            $julgamentoFinal->setRetificacaoJustificativa(Utils::getValue('retificacaoJustificativa', $data));

            $statusJulgamentoFinal = Utils::getValue('statusJulgamentoFinal', $data);
            if (!empty($statusJulgamentoFinal)) {
                $julgamentoFinal->setStatusJulgamentoFinal(StatusJulgamentoFinal::newInstance($statusJulgamentoFinal));
            }

            $substituicaoJulgamentoFinal = Utils::getValue('substituicaoJulgamentoFinal', $data);
            if (!empty($substituicaoJulgamentoFinal)) {
                $julgamentoFinal->setSubstituicaoJulgamentoFinal(SubstituicaoJulgamentoFinal::newInstance(
                    $substituicaoJulgamentoFinal
                ));
            }

            $recursoSegundoJulgamentoSubstituicao = Utils::getValue('recursoSegundoJulgamentoSubstituicao', $data);
            if (!empty($recursoSegundoJulgamentoSubstituicao)) {
                $julgamentoFinal->setRecursoSegundoJulgamentoSubstituicao(RecursoSegundoJulgamentoSubstituicao::newInstance(
                    $recursoSegundoJulgamentoSubstituicao
                ));
            }

            $usuario = Utils::getValue('usuario', $data);
            if (!empty($usuario)) {
                $julgamentoFinal->setUsuario(Usuario::newInstance($usuario));
            }

            $julgamentoSegundaInstanciaSubstituicaoPai = Utils::getValue(
                'julgamentoSegundaInstanciaSubstituicaoPai', $data
            );
            if (!empty($julgamentoSegundaInstanciaSubstituicaoPai)) {
                $julgamentoFinal->setJulgamentoSegundaInstanciaSubstituicaoPai(
                    JulgamentoSegundaInstanciaSubstituicao::newInstance($julgamentoSegundaInstanciaSubstituicaoPai)
                );
            }

            $indicacoesArray = Utils::getValue('indicacoes', $data);
            if (!empty($indicacoesArray)) {
                $indicacoes = [];
                foreach ($indicacoesArray as $indicacao) {
                    array_push($indicacoes, IndicacaoJulgamentoSegundaInstanciaSubstituicao::newInstance($indicacao));
                }
                $julgamentoFinal->setIndicacoes($indicacoes);
            }
        }

        return $julgamentoFinal;
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
    public function getNomeArquivo()
    {
        return $this->nomeArquivo;
    }

    /**
     * @param string $nomeArquivo
     */
    public function setNomeArquivo($nomeArquivo)
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
    public function setNomeArquivoFisico($nomeArquivoFisico)
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
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }


    /**
     * @return StatusJulgamentoFinal
     */
    public function getStatusJulgamentoFinal()
    {
        return $this->statusJulgamentoFinal;
    }

    /**
     * @param StatusJulgamentoFinal $statusJulgamentoFinal
     */
    public function setStatusJulgamentoFinal($statusJulgamentoFinal)
    {
        $this->statusJulgamentoFinal = $statusJulgamentoFinal;
    }

    /**
     * @return Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param Usuario $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * @return SubstituicaoJulgamentoFinal
     */
    public function getSubstituicaoJulgamentoFinal()
    {
        return $this->substituicaoJulgamentoFinal;
    }

    /**
     * @param SubstituicaoJulgamentoFinal $substituicaoJulgamentoFinal
     */
    public function setSubstituicaoJulgamentoFinal($substituicaoJulgamentoFinal)
    {
        $this->substituicaoJulgamentoFinal = $substituicaoJulgamentoFinal;
    }

    /**
     * @return RecursoSegundoJulgamentoSubstituicao
     */
    public function getRecursoSegundoJulgamentoSubstituicao()
    {
        return $this->recursoSegundoJulgamentoSubstituicao;
    }

    /**
     * @param RecursoSegundoJulgamentoSubstituicao $recursoSegundoJulgamentoSubstituicao
     */
    public function setRecursoSegundoJulgamentoSubstituicao($recursoSegundoJulgamentoSubstituicao)
    {
        $this->recursoSegundoJulgamentoSubstituicao = $recursoSegundoJulgamentoSubstituicao;
    }

    /**
     * @return IndicacaoJulgamentoSegundaInstanciaSubstituicao[]|array|ArrayCollection|null
     */
    public function getIndicacoes()
    {
        return $this->indicacoes;
    }

    /**
     * @param IndicacaoJulgamentoSegundaInstanciaSubstituicao[]|array|ArrayCollection|null $indicacoes
     */
    public function setIndicacoes($indicacoes): void
    {
        $this->indicacoes = $indicacoes;
    }

    /**
     * @return string
     */
    public function getRetificacaoJustificativa()
    {
        return $this->retificacaoJustificativa;
    }

    /**
     * @param string $retificacaoJustificativa
     */
    public function setRetificacaoJustificativa($retificacaoJustificativa): void
    {
        $this->retificacaoJustificativa = $retificacaoJustificativa;
    }

    /**
     * @return JulgamentoSegundaInstanciaSubstituicao
     */
    public function getJulgamentoSegundaInstanciaSubstituicaoPai()
    {
        return $this->julgamentoSegundaInstanciaSubstituicaoPai;
    }

    /**
     * @param JulgamentoSegundaInstanciaSubstituicao $julgamentoSegundaInstanciaSubstituicaoPai
     */
    public function setJulgamentoSegundaInstanciaSubstituicaoPai($julgamentoSegundaInstanciaSubstituicaoPai): void
    {
        $this->julgamentoSegundaInstanciaSubstituicaoPai = $julgamentoSegundaInstanciaSubstituicaoPai;
    }


}
