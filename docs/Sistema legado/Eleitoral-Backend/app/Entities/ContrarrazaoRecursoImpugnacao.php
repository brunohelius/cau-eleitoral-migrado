<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'JulgamentoRecursoSubstituicao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ContrarrazaoRecursoImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_CONTRARRAZAO_RECURSO_IMPUGNACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ContrarrazaoRecursoImpugnacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_CONTRARRAZAO_RECURSO_IMPUGNACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_CONTRARRAZAO_RECURSO_IMPUGNACAO_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_CONTRARRAZAO_RECURSO_IMPUGNACAO", type="string",nullable=false)
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
     * @ORM\OneToOne(targetEntity="App\Entities\RecursoImpugnacao")
     * @ORM\JoinColumn(name="ID_RECURSO_IMPUGNACAO", referencedColumnName="ID_RECURSO_IMPUGNACAO", nullable=false)
     *
     * @var RecursoImpugnacao
     */
    private $recursoImpugnacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_PROFISSIONAL_INCLUSAO", referencedColumnName="id", nullable=false)
     * @var Profissional
     */
    private $profissional;

    /**
     * Transient.
     *
     * @var mixed
     */
    private $arquivo;

    /**
     * Transient.
     *
     * @var mixed
     */
    private $tamanho;

    /**
     * Fábrica de instância de 'ContrarrazaoRecursoImpugnacao'.
     *
     * @param array $data
     * @return ContrarrazaoRecursoImpugnacao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $contrarrazaoRecursoImpugnacao = new ContrarrazaoRecursoImpugnacao();

        if ($data != null) {
            $contrarrazaoRecursoImpugnacao->setAtributtes($contrarrazaoRecursoImpugnacao, $data);

            $recursoImpugnacao = Utils::getValue('recursoImpugnacao', $data);
            if(!empty($recursoImpugnacao)) {
                $contrarrazaoRecursoImpugnacao->setRecursoImpugnacao(
                    RecursoImpugnacao::newInstance($recursoImpugnacao)
                );
            }

            $profissional = Utils::getValue('profissional', $data);
            if(!empty($profissional)) {
                $contrarrazaoRecursoImpugnacao->setProfissional(
                    Profissional::newInstance($profissional)
                );
            }
        }
        return $contrarrazaoRecursoImpugnacao;
    }

    /**
     * @param ContrarrazaoRecursoImpugnacao $contrarrazaoRecursoImpugnacao
     * @param array $data
     */
    public function setAtributtes(ContrarrazaoRecursoImpugnacao $contrarrazaoRecursoImpugnacao, array $data): void
    {
        $contrarrazaoRecursoImpugnacao->setId(Utils::getValue('id', $data));
        $contrarrazaoRecursoImpugnacao->setArquivo(Utils::getValue('arquivo', $data));
        $contrarrazaoRecursoImpugnacao->setTamanho(Utils::getValue('tamanho', $data));
        $contrarrazaoRecursoImpugnacao->setDescricao(Utils::getValue('descricao', $data));
        $contrarrazaoRecursoImpugnacao->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
        $contrarrazaoRecursoImpugnacao->setDataCadastro(Utils::getValue('dataCadastro', $data));
        $contrarrazaoRecursoImpugnacao->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));
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
     * @return RecursoImpugnacao
     */
    public function getRecursoImpugnacao(): RecursoImpugnacao
    {
        return $this->recursoImpugnacao;
    }

    /**
     * @param RecursoImpugnacao $recursoImpugnacao
     */
    public function setRecursoImpugnacao(RecursoImpugnacao $recursoImpugnacao): void
    {
        $this->recursoImpugnacao = $recursoImpugnacao;
    }

    /**
     * @return mixed
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * @param mixed $arquivo
     */
    public function setArquivo($arquivo): void
    {
        $this->arquivo = $arquivo;
    }

    /**
     * @return mixed
     */
    public function getTamanho()
    {
        return $this->tamanho;
    }

    /**
     * @param mixed $tamanho
     */
    public function setTamanho($tamanho): void
    {
        $this->tamanho = $tamanho;
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
}
