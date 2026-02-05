<?php

namespace App\Entities;

use App\To\ProfissionalTO;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'RecursoImpugnacao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\RecursoImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_RECURSO_IMPUGNACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class RecursoImpugnacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_RECURSO_IMPUGNACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_recurso_impugnacao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_RECURSO_IMPUGNACAO", type="string",nullable=false)
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
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoImpugnacao")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_IMPUGNACAO", referencedColumnName="ID_JULGAMENTO_IMPUGNACAO", nullable=false)
     *
     * @var JulgamentoImpugnacao
     */
    private $julgamentoImpugnacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_PROFISSIONAL_INCLUSAO", referencedColumnName="id", nullable=false)
     * @var Profissional
     */
    private $profissional;

    /**
     * @ORM\ManyToOne(targetEntity="TipoSolicitacaoRecursoImpugnacao")
     * @ORM\JoinColumn(name="ID_TP_SOLICITACAO_RECURSO_IMPUGNACAO", referencedColumnName="ID_TP_SOLICITACAO_RECURSO_IMPUGNACAO", nullable=false)
     *
     * @var TipoSolicitacaoRecursoImpugnacao
     */
    private $tipoSolicitacaoRecursoImpugnacao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\ContrarrazaoRecursoImpugnacao", mappedBy="recursoImpugnacao", fetch="EXTRA_LAZY")
     *
     * @var ContrarrazaoRecursoImpugnacao
     */
    private $contrarrazaoRecursoImpugnacao;

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
     * Fábrica de instância de 'RecursoImpugnacao'.
     *
     * @param array $data
     * @return RecursoImpugnacao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $recursoImpugnacao = new RecursoImpugnacao();

        if ($data != null) {
            $recursoImpugnacao->setId(Utils::getValue('id', $data));
            $recursoImpugnacao->setTamanho(Utils::getValue('tamanho', $data));
            $recursoImpugnacao->setArquivo(Utils::getValue('arquivo', $data));
            $recursoImpugnacao->setDescricao(Utils::getValue('descricao', $data));
            $recursoImpugnacao->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $recursoImpugnacao->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $recursoImpugnacao->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $julgamentoImpugnacao = Utils::getValue('julgamentoImpugnacao', $data);
            if(!empty($julgamentoImpugnacao)) {
                $recursoImpugnacao->setJulgamentoImpugnacao(
                    JulgamentoImpugnacao::newInstance($julgamentoImpugnacao)
                );
            }

            $tipoSolicitacaoRecursoImpugnacao = Utils::getValue('tipoSolicitacaoRecursoImpugnacao', $data);
            if(!empty($tipoSolicitacaoRecursoImpugnacao)) {
                $recursoImpugnacao->setTipoSolicitacaoRecursoImpugnacao(
                    TipoSolicitacaoRecursoImpugnacao::newInstance($tipoSolicitacaoRecursoImpugnacao)
                );
            }

            $profissional = Utils::getValue('profissional', $data);
            if(!empty($profissional)) {
                $recursoImpugnacao->setProfissional(
                    Profissional::newInstance($profissional)
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
     * @return JulgamentoImpugnacao
     */
    public function getJulgamentoImpugnacao()
    {
        return $this->julgamentoImpugnacao;
    }

    /**
     * @param JulgamentoImpugnacao $julgamentoImpugnacao
     */
    public function setJulgamentoImpugnacao($julgamentoImpugnacao): void
    {
        $this->julgamentoImpugnacao = $julgamentoImpugnacao;
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
     * @return TipoSolicitacaoRecursoImpugnacao
     */
    public function getTipoSolicitacaoRecursoImpugnacao()
    {
        return $this->tipoSolicitacaoRecursoImpugnacao;
    }

    /**
     * @param TipoSolicitacaoRecursoImpugnacao $tipoSolicitacaoRecursoImpugnacao
     */
    public function setTipoSolicitacaoRecursoImpugnacao($tipoSolicitacaoRecursoImpugnacao): void
    {
        $this->tipoSolicitacaoRecursoImpugnacao = $tipoSolicitacaoRecursoImpugnacao;
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
     * @return ContrarrazaoRecursoImpugnacao
     */
    public function getContrarrazaoRecursoImpugnacao(): ContrarrazaoRecursoImpugnacao
    {
        return $this->contrarrazaoRecursoImpugnacao;
    }

    /**
     * @param ContrarrazaoRecursoImpugnacao $contrarrazaoRecursoImpugnacao
     */
    public function setContrarrazaoRecursoImpugnacao(ContrarrazaoRecursoImpugnacao $contrarrazaoRecursoImpugnacao): void
    {
        $this->contrarrazaoRecursoImpugnacao = $contrarrazaoRecursoImpugnacao;
    }


}
