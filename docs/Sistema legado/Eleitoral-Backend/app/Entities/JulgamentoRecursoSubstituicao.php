<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'JulgamentoRecursoSubstituicao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoRecursoSubstituicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_JULGAMENTO_RECURSO_SUBSTITUICAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoSubstituicao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_JULGAMENTO_RECURSO_SUBSTITUICAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_julgamento_recurso_substituicao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_JULGAMENTO_RECURSO_SUBSTITUICAO", type="string",nullable=false)
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
     * @ORM\OneToOne(targetEntity="App\Entities\RecursoSubstituicao")
     * @ORM\JoinColumn(name="ID_RECURSO_SUBSTITUICAO", referencedColumnName="ID_RECURSO_SUBSTITUICAO", nullable=false)
     *
     * @var RecursoSubstituicao
     */
    private $recursoSubstituicao;

    /**
     * @ORM\ManyToOne(targetEntity="StatusJulgamentoSubstituicao")
     * @ORM\JoinColumn(name="ID_STATUS_JULGAMENTO_SUBSTITUICAO", referencedColumnName="ID_STATUS_JULGAMENTO_SUBSTITUICAO", nullable=false)
     *
     * @var StatusJulgamentoSubstituicao
     */
    private $statusJulgamentoSubstituicao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Usuario")
     * @ORM\JoinColumn(name="ID_USUARIO_INCLUSAO", referencedColumnName="id", nullable=false)
     * @var Usuario
     */
    private $usuario;

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
     * Fábrica de instância de 'JulgamentoRecursoSubstituicao'.
     *
     * @param array $data
     * @return JulgamentoRecursoSubstituicao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoRecursoSubstituicao = new JulgamentoRecursoSubstituicao();

        if ($data != null) {
            $julgamentoRecursoSubstituicao->setId(Utils::getValue('id', $data));
            $julgamentoRecursoSubstituicao->setArquivo(Utils::getValue('arquivo', $data));
            $julgamentoRecursoSubstituicao->setTamanho(Utils::getValue('tamanho', $data));
            $julgamentoRecursoSubstituicao->setDescricao(Utils::getValue('descricao', $data));
            $julgamentoRecursoSubstituicao->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $julgamentoRecursoSubstituicao->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $julgamentoRecursoSubstituicao->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $recursoSubstituicao = Utils::getValue('recursoSubstituicao', $data);
            if(!empty($recursoSubstituicao)) {
                $julgamentoRecursoSubstituicao->setRecursoSubstituicao(
                    RecursoSubstituicao::newInstance($recursoSubstituicao)
                );
            }

            $statusJulgamentoSubstituicao = Utils::getValue('statusJulgamentoSubstituicao', $data);
            if(!empty($statusJulgamentoSubstituicao)) {
                $julgamentoRecursoSubstituicao->setStatusJulgamentoSubstituicao(
                    StatusJulgamentoSubstituicao::newInstance($statusJulgamentoSubstituicao)
                );
            }

            $usuario = Utils::getValue('usuario', $data);
            if(!empty($usuario)) {
                $julgamentoRecursoSubstituicao->setUsuario(
                    Usuario::newInstance($usuario)
                );
            }
        }
        return $julgamentoRecursoSubstituicao;
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
     * @return RecursoSubstituicao
     */
    public function getRecursoSubstituicao()
    {
        return $this->recursoSubstituicao;
    }

    /**
     * @param RecursoSubstituicao $recursoSubstituicao
     */
    public function setRecursoSubstituicao($recursoSubstituicao): void
    {
        $this->recursoSubstituicao = $recursoSubstituicao;
    }

    /**
     * @return StatusJulgamentoSubstituicao
     */
    public function getStatusJulgamentoSubstituicao()
    {
        return $this->statusJulgamentoSubstituicao;
    }

    /**
     * @param StatusJulgamentoSubstituicao $statusJulgamentoSubstituicao
     */
    public function setStatusJulgamentoSubstituicao($statusJulgamentoSubstituicao): void
    {
        $this->statusJulgamentoSubstituicao = $statusJulgamentoSubstituicao;
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
     * @return Usuario
     */
    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    /**
     * @param Usuario $usuario
     */
    public function setUsuario(?Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }
}
