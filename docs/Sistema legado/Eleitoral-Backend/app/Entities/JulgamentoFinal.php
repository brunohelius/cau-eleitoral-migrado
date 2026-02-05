<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'JulgamentoFinal'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoFinalRepository")
 * @ORM\Table(schema="eleitoral", name="TB_JULGAMENTO_FINAL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoFinal extends Entity
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_JULGAMENTO_FINAL_ID_SEQ", initialValue=1, allocationSize=1)
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
     * @ORM\Column(name="retificacao_justificativa", type="string", length=200, nullable=true)
     *
     * @var string
     */
    private $retificacaoJustificativa;

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
     * @ORM\ManyToOne(targetEntity="App\Entities\ChapaEleicao")
     * @ORM\JoinColumn(name="ID_CHAPA_ELEICAO", referencedColumnName="ID_CHAPA_ELEICAO", nullable=false)
     *
     * @var ChapaEleicao
     */
    private $chapaEleicao;

    /**
     * @ORM\ManyToOne(targetEntity="StatusJulgamentoFinal")
     * @ORM\JoinColumn(name="ID_STATUS_JULGAMENTO_FINAL", referencedColumnName="ID", nullable=false)
     *
     * @var StatusJulgamentoFinal
     */
    private $statusJulgamentoFinal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\JulgamentoFinal")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_FINAL_PAI", referencedColumnName="ID")
     *
     * @var \App\Entities\JulgamentoFinal
     */
    private $julgamentoFinalPai;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Usuario")
     * @ORM\JoinColumn(name="ID_USUARIO_INCLUSAO", referencedColumnName="id", nullable=false)
     * @var Usuario
     */
    private $usuario;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\IndicacaoJulgamentoFinal", mappedBy="julgamentoFinal", cascade={"persist"}, fetch="EXTRA_LAZY")
     *
     * @var IndicacaoJulgamentoFinal[]|array|ArrayCollection|null
     */
    private $indicacoes;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\RecursoJulgamentoFinal", mappedBy="julgamentoFinal", fetch="EXTRA_LAZY")
     *
     * @var RecursoJulgamentoFinal
     */
    private $recursoJulgamentoFinal;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\SubstituicaoJulgamentoFinal", mappedBy="julgamentoFinal", fetch="EXTRA_LAZY")
     *
     * @var array|ArrayCollection|null
     */
    private $substituicoesJulgamentoFinal;

    /**
     * Fábrica de instância de 'JulgamentoFinal'.
     *
     * @param array $data
     * @return JulgamentoFinal
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoFinal = new JulgamentoFinal();

        if ($data != null) {
            $julgamentoFinal->setId(Utils::getValue('id', $data));
            $julgamentoFinal->setDescricao(Utils::getValue('descricao', $data));
            $julgamentoFinal->setRetificacaoJustificativa(Utils::getValue('retificacaoJustificativa', $data));
            $julgamentoFinal->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $julgamentoFinal->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $julgamentoFinal->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $chapaEleicao = Utils::getValue('chapaEleicao', $data);
            if(!empty($chapaEleicao)) {
                $julgamentoFinal->setChapaEleicao(ChapaEleicao::newInstance($chapaEleicao));
            }

            $statusJulgamentoFinal = Utils::getValue('statusJulgamentoFinal', $data);
            if(!empty($statusJulgamentoFinal)) {
                $julgamentoFinal->setStatusJulgamentoFinal(StatusJulgamentoFinal::newInstance($statusJulgamentoFinal));
            }

            $recursoJulgamentoFinal = Utils::getValue('recursoJulgamentoFinal', $data);
            if(!empty($recursoJulgamentoFinal)) {
                $julgamentoFinal->setRecursoJulgamentoFinal(RecursoJulgamentoFinal::newInstance(
                    $recursoJulgamentoFinal
                ));
            }

            $usuario = Utils::getValue('usuario', $data);
            if(!empty($usuario)) {
                $julgamentoFinal->setUsuario(Usuario::newInstance($usuario));
            }

            $indicacoesArray = Utils::getValue('indicacoes', $data);
            if (!empty($indicacoesArray)) {
                $indicacoes = [];
                foreach ($indicacoesArray as $indicacao) {
                    array_push($indicacoes, IndicacaoJulgamentoFinal::newInstance($indicacao));
                }
                $julgamentoFinal->setIndicacoes($indicacoes);
            }

            $substituicoesArray = Utils::getValue('substituicoesJulgamentoFinal', $data);
            if (!empty($substituicoesArray)) {
                $substituicoes = [];
                foreach ($substituicoesArray as $substituicao) {
                    array_push($substituicoes, SubstituicaoJulgamentoFinal::newInstance($substituicao));
                }
                $julgamentoFinal->setSubstituicoesJulgamentoFinal($substituicoes);
            }

            $julgamentoFinalPai = Utils::getValue('julgamentoFinalPai', $data);
            if(!empty($julgamentoFinalPai)) {
                $julgamentoFinal->setJulgamentoFinalPai(JulgamentoFinal::newInstance($julgamentoFinalPai));
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
     * @return ChapaEleicao
     */
    public function getChapaEleicao()
    {
        return $this->chapaEleicao;
    }

    /**
     * @param ChapaEleicao $chapaEleicao
     */
    public function setChapaEleicao($chapaEleicao): void
    {
        $this->chapaEleicao = $chapaEleicao;
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
    public function setStatusJulgamentoFinal($statusJulgamentoFinal): void
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
    public function setUsuario($usuario): void
    {
        $this->usuario = $usuario;
    }

    /**
     * @return IndicacaoJulgamentoFinal[]|array|ArrayCollection|null
     */
    public function getIndicacoes()
    {
        return $this->indicacoes;
    }

    /**
     * @param IndicacaoJulgamentoFinal[]|array|ArrayCollection|null $indicacoes
     */
    public function setIndicacoes($indicacoes): void
    {
        $this->indicacoes = $indicacoes;
    }

    /**
     * @return RecursoJulgamentoFinal
     */
    public function getRecursoJulgamentoFinal()
    {
        return $this->recursoJulgamentoFinal;
    }


    /**
     * @param RecursoJulgamentoFinal $recursoJulgamentoFinal
     */
    public function setRecursoJulgamentoFinal($recursoJulgamentoFinal): void
    {
        $this->recursoJulgamentoFinal = $recursoJulgamentoFinal;
    }

    /**
     * @return array|ArrayCollection|null
     */
    public function getSubstituicoesJulgamentoFinal()
    {
        return $this->substituicoesJulgamentoFinal;
    }

    /**
     * @param array|ArrayCollection|null $substituicoesJulgamentoFinal
     */
    public function setSubstituicoesJulgamentoFinal($substituicoesJulgamentoFinal): void
    {
        $this->substituicoesJulgamentoFinal = $substituicoesJulgamentoFinal;
    }

    /**
     * @return JulgamentoFinal
     */
    public function getJulgamentoFinalPai(): ?JulgamentoFinal
    {
        return $this->julgamentoFinalPai;
    }

    /**
     * @param JulgamentoFinal $julgamentoFinalPai
     */
    public function setJulgamentoFinalPai(?JulgamentoFinal $julgamentoFinalPai): void
    {
        $this->julgamentoFinalPai = $julgamentoFinalPai;
    }
}
