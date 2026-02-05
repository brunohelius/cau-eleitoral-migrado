<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'RecursoSegundoJulgamentoSubstituicao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\RecursoSegundoJulgamentoSubstituicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_RECURSO_JULGAMENTO_SEGUNDA_INSTANCIA_SUBSTITUICAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class RecursoSegundoJulgamentoSubstituicao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_RECURSO_JULGAMENTO_SEGUNDA_INSTANCIA_SUBSTITUICAO_ID_SEQ", initialValue=1, allocationSize=1)
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
     * @ORM\JoinColumn(name="ID_USUARIO_INCLUSAO", referencedColumnName="id", nullable=false)
     * @var Profissional
     */
    private $profissional;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoSegundaInstanciaSubstituicao")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_SEGUNDA_INSTANCIA_SUBSTITUICAO", referencedColumnName="ID")
     *
     * @var JulgamentoSegundaInstanciaSubstituicao
     */
    private $julgamentoSegundaInstanciaSubstituicao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoRecursoPedidoSubstituicao", mappedBy="recursoSegundoJulgamentoSubstituicao", fetch="EXTRA_LAZY")
     *
     * @var JulgamentoRecursoPedidoSubstituicao
     */
    private $julgamentoRecursoPedidoSubstituicao;

    /**
     * Fábrica de instância de 'RecursoSegundoJulgamentoSubstituicao'.
     *
     * @param array $data
     * @return RecursoSegundoJulgamentoSubstituicao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $recursoSegundoJulgamentoSubstituicao = new RecursoSegundoJulgamentoSubstituicao();

        if ($data != null) {
            $recursoSegundoJulgamentoSubstituicao->setId(Utils::getValue('id', $data));
            $recursoSegundoJulgamentoSubstituicao->setDescricao(Utils::getValue('descricao', $data));
            $recursoSegundoJulgamentoSubstituicao->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $recursoSegundoJulgamentoSubstituicao->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $recursoSegundoJulgamentoSubstituicao->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $julgamentoSubstituicao = Utils::getValue('julgamentoSegundaInstanciaSubstituicao', $data);
            if(!empty($julgamentoSubstituicao)) {
                $recursoSegundoJulgamentoSubstituicao->setJulgamentoSegundaInstanciaSubstituicao(
                    JulgamentoSegundaInstanciaSubstituicao::newInstance($julgamentoSubstituicao)
                );
            }

            $julgamentoRecursoPedidoSubstituicao = Utils::getValue('julgamentoRecursoPedidoSubstituicao', $data);
            if(!empty($julgamentoRecursoPedidoSubstituicao)) {
                $recursoSegundoJulgamentoSubstituicao->setJulgamentoRecursoPedidoSubstituicao(
                    JulgamentoRecursoPedidoSubstituicao::newInstance($julgamentoRecursoPedidoSubstituicao)
                );
            }

            $profissional = Utils::getValue('profissional', $data);
            if(!empty($profissional)) {
                $recursoSegundoJulgamentoSubstituicao->setProfissional(Profissional::newInstance($profissional));
            }
        }
        return $recursoSegundoJulgamentoSubstituicao;
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
     * @return JulgamentoSegundaInstanciaSubstituicao
     */
    public function getJulgamentoSegundaInstanciaSubstituicao()
    {
        return $this->julgamentoSegundaInstanciaSubstituicao;
    }

    /**
     * @param JulgamentoSegundaInstanciaSubstituicao $julgamentoSegundaInstanciaSubstituicao
     */
    public function setJulgamentoSegundaInstanciaSubstituicao($julgamentoSegundaInstanciaSubstituicao): void
    {
        $this->julgamentoSegundaInstanciaSubstituicao = $julgamentoSegundaInstanciaSubstituicao;
    }

    /**
     * @return JulgamentoRecursoPedidoSubstituicao
     */
    public function getJulgamentoRecursoPedidoSubstituicao()
    {
        return $this->julgamentoRecursoPedidoSubstituicao;
    }

    /**
     * @param JulgamentoRecursoPedidoSubstituicao $julgamentoRecursoPedidoSubstituicao
     */
    public function setJulgamentoRecursoPedidoSubstituicao($julgamentoRecursoPedidoSubstituicao): void
    {
        $this->julgamentoRecursoPedidoSubstituicao = $julgamentoRecursoPedidoSubstituicao;
    }
}
