<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'RecursoSubstituicao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\RecursoSubstituicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_RECURSO_SUBSTITUICAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class RecursoSubstituicao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_RECURSO_SUBSTITUICAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_recurso_substituicao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_RECURSO_SUBSTITUICAO", type="string",nullable=false)
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
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoSubstituicao")
     * @ORM\JoinColumn(name="ID_JULGAMENTO_SUBSTITUICAO", referencedColumnName="ID_JULGAMENTO_SUBSTITUICAO", nullable=false)
     *
     * @var JulgamentoSubstituicao
     */
    private $julgamentoSubstituicao;

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
     * Fábrica de instância de 'RecursoSubstituicao'.
     *
     * @param array $data
     * @return RecursoSubstituicao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $recursoSubstituicao = new RecursoSubstituicao();

        if ($data != null) {
            $recursoSubstituicao->setId(Utils::getValue('id', $data));
            $recursoSubstituicao->setArquivo(Utils::getValue('arquivo', $data));
            $recursoSubstituicao->setTamanho(Utils::getValue('tamanho', $data));
            $recursoSubstituicao->setDescricao(Utils::getValue('descricao', $data));
            $recursoSubstituicao->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $recursoSubstituicao->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $recursoSubstituicao->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $julgamentoSubstituicao = Utils::getValue('julgamentoSubstituicao', $data);
            if(!empty($julgamentoSubstituicao)) {
                $recursoSubstituicao->setJulgamentoSubstituicao(
                    JulgamentoSubstituicao::newInstance($julgamentoSubstituicao)
                );
            }

            $profissional = Utils::getValue('profissional', $data);
            if(!empty($profissional)) {
                $recursoSubstituicao->setProfissional(
                    Profissional::newInstance($profissional)
                );
            }
        }
        return $recursoSubstituicao;
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
     * @return JulgamentoSubstituicao
     */
    public function getJulgamentoSubstituicao()
    {
        return $this->julgamentoSubstituicao;
    }

    /**
     * @param JulgamentoSubstituicao $julgamentoSubstituicao
     */
    public function setJulgamentoSubstituicao($julgamentoSubstituicao): void
    {
        $this->julgamentoSubstituicao = $julgamentoSubstituicao;
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
