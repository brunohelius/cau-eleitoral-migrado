<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'ContrarrazaoRecursoImpugnacaoResultado'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ContrarrazaoRecursoImpugnacaoResultadoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_CONTRARRAZAO_RECURSO_IMPUGNACAO_RESULTADO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ContrarrazaoRecursoImpugnacaoResultado extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_CONTRARRAZAO_RECURSO_IMPUGNACAO_RESULTADO_ID_SEQ", initialValue=1, allocationSize=1)
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
     * @ORM\Column(name="NUMERO", type="integer", nullable=false)
     *
     * @var integer
     */
    private $numero;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_PROFISSIONAL_INCLUSAO", referencedColumnName="id", nullable=false)
     * @var Profissional
     */
    private $profissional;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\RecursoImpugnacaoResultado")
     * @ORM\JoinColumn(name="ID_RECURSO_IMPUGNACAO_RESULTADO", referencedColumnName="ID", nullable=false)
     * @var RecursoImpugnacaoResultado
     */
    private $recursoImpugnacaoResultado;

    /**
     * Fábrica de instância de 'ContrarrazaoRecursoImpugnacaoResultado'.
     *
     * @param array $data
     * @return ContrarrazaoRecursoImpugnacaoResultado
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $recursoImpugnacao = new ContrarrazaoRecursoImpugnacaoResultado();

        if ($data != null) {
            $recursoImpugnacao->setId(Utils::getValue('id', $data));
            $recursoImpugnacao->setNumero(Utils::getValue('numero', $data));
            $recursoImpugnacao->setDescricao(Utils::getValue('descricao', $data));
            $recursoImpugnacao->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $recursoImpugnacao->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $recursoImpugnacao->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $profissional = Utils::getValue('profissional', $data);
            if(!empty($profissional)) {
                $recursoImpugnacao->setProfissional(
                    Profissional::newInstance($profissional)
                );
            }

            $recursoImpugnacaoResultado = Utils::getValue('recursoImpugnacaoResultado', $data);
            if(!empty($profissional)) {
                $recursoImpugnacao->setRecursoImpugnacaoResultado(
                    RecursoImpugnacaoResultado::newInstance($recursoImpugnacaoResultado)
                );
            }
        }
        return $recursoImpugnacao;
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
     * @return int
     */
    public function getNumero(): int
    {
        return $this->numero;
    }

    /**
     * @param int $numero
     */
    public function setNumero(int $numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return RecursoImpugnacaoResultado
     */
    public function getRecursoImpugnacaoResultado(): RecursoImpugnacaoResultado
    {
        return $this->recursoImpugnacaoResultado;
    }

    /**
     * @param RecursoImpugnacaoResultado $recursoImpugnacaoResultado
     */
    public function setRecursoImpugnacaoResultado(RecursoImpugnacaoResultado $recursoImpugnacaoResultado): void
    {
        $this->recursoImpugnacaoResultado = $recursoImpugnacaoResultado;
    }
}
