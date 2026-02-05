<?php

namespace App\Entities;

use App\Util\Utils;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'RecursoImpugnacaoResultado'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\RecursoImpugnacaoResultadoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_RECURSO_IMPUGNACAO_RESULTADO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class RecursoImpugnacaoResultado extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_RECURSO_IMPUGNACAO_RESULTADO_ID_SEQ", initialValue=1, allocationSize=1)
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
     * @ORM\ManyToOne(targetEntity="App\Entities\JulgamentoAlegacaoImpugResultado")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_ALEGACAO_RESULTADO", referencedColumnName="ID", nullable=false)
     * @var JulgamentoAlegacaoImpugResultado
     */
    private $julgamentoAlegacaoImpugResultado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoRecursoImpugnacaoResultado")
     * @ORM\JoinColumn(name="ID_TP_RECURSO_IMPUGNACAO_RESULTADO", referencedColumnName="ID", nullable=false)
     * @var TipoRecursoImpugnacaoResultado
     */
    private $tipoRecursoImpugnacaoResultado;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ContrarrazaoRecursoImpugnacaoResultado", mappedBy="recursoImpugnacaoResultado", cascade={"persist"}, fetch="EXTRA_LAZY")
     *
     * @var ContrarrazaoRecursoImpugnacaoResultado[]|array|ArrayCollection|null
     */
    private $contrarrazoesRecursoImpugnacaoResultado;

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
     * Fábrica de instância de 'RecursoImpugnacaoResultado'.
     *
     * @param array $data
     * @return RecursoImpugnacaoResultado
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $recursoImpugnacaoResultado = new RecursoImpugnacaoResultado();

        if ($data != null) {
            $recursoImpugnacaoResultado->setId(Utils::getValue('id', $data));
            $recursoImpugnacaoResultado->setNumero(Utils::getValue('numero', $data));
            $recursoImpugnacaoResultado->setTamanho(Utils::getValue('tamanho', $data));
            $recursoImpugnacaoResultado->setArquivo(Utils::getValue('arquivo', $data));
            $recursoImpugnacaoResultado->setDescricao(Utils::getValue('descricao', $data));
            $recursoImpugnacaoResultado->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $recursoImpugnacaoResultado->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $recursoImpugnacaoResultado->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $profissional = Utils::getValue('profissional', $data);
            if(!empty($profissional)) {
                $recursoImpugnacaoResultado->setProfissional(
                    Profissional::newInstance($profissional)
                );
            }

            $julgamentoAlegacaoImpugResultado = Utils::getValue('julgamentoAlegacaoImpugResultado', $data);
            if(!empty($julgamentoAlegacaoImpugResultado)) {
                $recursoImpugnacaoResultado->setJulgamentoAlegacaoImpugResultado(
                    JulgamentoAlegacaoImpugResultado::newInstance($julgamentoAlegacaoImpugResultado)
                );
            }

            $tipoRecursoImpugnacaoResultado = Utils::getValue('tipoRecursoImpugnacaoResultado', $data);
            if(!empty($tipoRecursoImpugnacaoResultado)) {
                $recursoImpugnacaoResultado->setTipoRecursoImpugnacaoResultado(
                    TipoRecursoImpugnacaoResultado::newInstance($tipoRecursoImpugnacaoResultado)
                );
            }

            $contrarrazoesRecursoImpugnacaoResultado = Utils::getValue('contrarrazoesRecursoImpugnacaoResultado', $data);
            if(!empty($contrarrazoesRecursoImpugnacaoResultado)) {
                foreach ($contrarrazoesRecursoImpugnacaoResultado as $contrarrazaoRecursoImpugnacaoResultado) {
                    $recursoImpugnacaoResultado->adicionaContrarracaoRecursoImpugnacaoResultado(
                        ContrarrazaoRecursoImpugnacaoResultado::newInstance($contrarrazaoRecursoImpugnacaoResultado));
                }
            }
        }
        return $recursoImpugnacaoResultado;
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
     * @return int
     */
    public function getNumero(): ?int
    {
        return $this->numero;
    }

    /**
     * @param int $numero
     */
    public function setNumero(?int $numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return JulgamentoAlegacaoImpugResultado
     */
    public function getJulgamentoAlegacaoImpugResultado(): JulgamentoAlegacaoImpugResultado
    {
        return $this->julgamentoAlegacaoImpugResultado;
    }

    /**
     * @param JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado
     */
    public function setJulgamentoAlegacaoImpugResultado(JulgamentoAlegacaoImpugResultado $julgamentoAlegacaoImpugResultado): void
    {
        $this->julgamentoAlegacaoImpugResultado = $julgamentoAlegacaoImpugResultado;
    }

    /**
     * @return TipoRecursoImpugnacaoResultado
     */
    public function getTipoRecursoImpugnacaoResultado(): TipoRecursoImpugnacaoResultado
    {
        return $this->tipoRecursoImpugnacaoResultado;
    }

    /**
     * @param TipoRecursoImpugnacaoResultado $tipoRecursoImpugnacaoResultado
     */
    public function setTipoRecursoImpugnacaoResultado(TipoRecursoImpugnacaoResultado $tipoRecursoImpugnacaoResultado): void
    {
        $this->tipoRecursoImpugnacaoResultado = $tipoRecursoImpugnacaoResultado;
    }

    /**
     * @return ContrarrazaoRecursoImpugnacaoResultado[]|array|ArrayCollection|null
     */
    public function getContrarrazoesRecursoImpugnacaoResultado()
    {
        return $this->contrarrazoesRecursoImpugnacaoResultado;
    }

    /**
     * @param ContrarrazaoRecursoImpugnacaoResultado[]|array|ArrayCollection|null $contrarrazoesRecursoImpugnacaoResultado
     */
    public function setContrarrazoesRecursoImpugnacaoResultado($contrarrazoesRecursoImpugnacaoResultado): void
    {
        $this->contrarrazoesRecursoImpugnacaoResultado = $contrarrazoesRecursoImpugnacaoResultado;
    }

    /**
     * Adiciona um Arquivo no array de Arquivos
     *
     * @param ContrarrazaoRecursoImpugnacaoResultado contrarrazoesRecursoImpugnacaoResultado
     */
    private function adicionaContrarracaoRecursoImpugnacaoResultado(ContrarrazaoRecursoImpugnacaoResultado
                                          $contrarrazaoRecursoImpugnacaoResultado)
    {
        if ($this->getContrarrazoesRecursoImpugnacaoResultado() === null) {
            $this->setContrarrazoesRecursoImpugnacaoResultado(new ArrayCollection());
        }

        if ($contrarrazaoRecursoImpugnacaoResultado !== null) {
            $contrarrazaoRecursoImpugnacaoResultado->setRecursoImpugnacaoResultado($this);
            $this->getContrarrazoesRecursoImpugnacaoResultado()->add($contrarrazaoRecursoImpugnacaoResultado);
        }
    }
}
