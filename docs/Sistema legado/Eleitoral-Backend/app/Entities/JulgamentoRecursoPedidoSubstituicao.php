<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'JulgamentoRecursoPedidoSubstituicao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoRecursoPedidoSubstituicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_JULGAMENTO_RECURSO_PEDIDO_SUBSTITUICAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoPedidoSubstituicao extends Entity
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_JULGAMENTO_RECURSO_PEDIDO_SUBSTITUICAO_ID_SEQ", initialValue=1, allocationSize=1)
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
     * @ORM\OneToOne(targetEntity="App\Entities\RecursoSegundoJulgamentoSubstituicao")
     * @ORM\JoinColumn(name="ID_RECURSO_SEGUNDO_JULGAMENTO_SUBSTITUICAO", referencedColumnName="ID", nullable=false)
     *
     * @var RecursoSegundoJulgamentoSubstituicao
     */
    private $recursoSegundoJulgamentoSubstituicao;

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
     * @ORM\OneToMany(targetEntity="App\Entities\IndicacaoJulgamentoRecursoPedidoSubstituicao", mappedBy="julgamentoRecursoPedidoSubstituicao", cascade={"persist"}, fetch="EXTRA_LAZY")
     *
     * @var IndicacaoJulgamentoRecursoPedidoSubstituicao[]|array|ArrayCollection|null
     */
    private $indicacoes;

    /**
     * @ORM\Column(name="retificacao_justificativa", type="string", length=1000, nullable=true)
     *
     * @var string
     */
    private $retificacaoJustificativa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\JulgamentoRecursoPedidoSubstituicao")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_RECURSO_PEDIDO_SUBSTITUICAO_PAI", referencedColumnName="ID")
     *
     * @var JulgamentoRecursoPedidoSubstituicao
     */
    private $julgamentoRecursoPedidoSubstituicaoPai;

    /**
     * Fábrica de instância de 'JulgamentoRecursoPedidoSubstituicao'.
     *
     * @param array $data
     * @return JulgamentoRecursoPedidoSubstituicao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoFinal = new JulgamentoRecursoPedidoSubstituicao();

        if ($data != null) {
            $julgamentoFinal->setId(Utils::getValue('id', $data));
            $julgamentoFinal->setDescricao(Utils::getValue('descricao', $data));
            $julgamentoFinal->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $julgamentoFinal->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $julgamentoFinal->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));
            $julgamentoFinal->setRetificacaoJustificativa(Utils::getValue('retificacaoJustificativa', $data));

            $statusJulgamentoFinal = Utils::getValue('statusJulgamentoFinal', $data);
            if(!empty($statusJulgamentoFinal)) {
                $julgamentoFinal->setStatusJulgamentoFinal(StatusJulgamentoFinal::newInstance($statusJulgamentoFinal));
            }

            $recursoSegundoJulgamentoSubstituicao = Utils::getValue('recursoSegundoJulgamentoSubstituicao', $data);
            if(!empty($recursoSegundoJulgamentoSubstituicao)) {
                $julgamentoFinal->setRecursoSegundoJulgamentoSubstituicao(RecursoSegundoJulgamentoSubstituicao::newInstance(
                    $recursoSegundoJulgamentoSubstituicao
                ));
            }

            $usuario = Utils::getValue('usuario', $data);
            if(!empty($usuario)) {
                $julgamentoFinal->setUsuario(Usuario::newInstance($usuario));
            }

            $julgamentoRecursoPedidoSubstituicao = Utils::getValue('julgamentoRecursoPedidoSubstituicao', $data);
            if (!empty($julgamentoRecursoPedidoSubstituicao)) {
                $julgamentoFinal->setJulgamentoRecursoPedidoSubstituicaoPai(
                    JulgamentoRecursoPedidoSubstituicao::newInstance($julgamentoRecursoPedidoSubstituicao)
                );
            }

            $indicacoesArray = Utils::getValue('indicacoes', $data);
            if (!empty($indicacoesArray)) {
                $indicacoes = [];
                foreach ($indicacoesArray as $indicacao) {
                    array_push($indicacoes, IndicacaoJulgamentoRecursoPedidoSubstituicao::newInstance($indicacao));
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
     * @return IndicacaoJulgamentoRecursoPedidoSubstituicao[]|array|ArrayCollection|null
     */
    public function getIndicacoes()
    {
        return $this->indicacoes;
    }

    /**
     * @param IndicacaoJulgamentoRecursoPedidoSubstituicao[]|array|ArrayCollection|null $indicacoes
     */
    public function setIndicacoes($indicacoes)
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
     * @return JulgamentoRecursoPedidoSubstituicao
     */
    public function getJulgamentoRecursoPedidoSubstituicaoPai()
    {
        return $this->julgamentoRecursoPedidoSubstituicaoPai;
    }

    /**
     * @param JulgamentoRecursoPedidoSubstituicao $julgamentoRecursoPedidoSubstituicaoPai
     */
    public function setJulgamentoRecursoPedidoSubstituicaoPai($julgamentoRecursoPedidoSubstituicaoPai): void {
        $this->julgamentoRecursoPedidoSubstituicaoPai = $julgamentoRecursoPedidoSubstituicaoPai;
    }

}
