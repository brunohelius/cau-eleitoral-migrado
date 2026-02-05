<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'JulgamentoRecursoSubstituicao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoRecursoImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_JULGAMENTO_RECURSO_IMPUGNACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoImpugnacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_JULGAMENTO_RECURSO_IMPUGNACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_julgamento_recurso_impugnacao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_JULGAMENTO_RECURSO_IMPUGNACAO", type="string",nullable=false)
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
     * @ORM\OneToOne(targetEntity="App\Entities\PedidoImpugnacao")
     * @ORM\JoinColumn(name="ID_PEDIDO_IMPUGNACAO", referencedColumnName="ID_PEDIDO_IMPUGNACAO", nullable=false)
     *
     * @var PedidoImpugnacao
     */
    private $pedidoImpugnacao;

    /**
     * @ORM\ManyToOne(targetEntity="StatusJulgamentoImpugnacao")
     * @ORM\JoinColumn(name="ID_STATUS_JULGAMENTO_IMPUGNACAO", referencedColumnName="ID_STATUS_JULGAMENTO_IMPUGNACAO", nullable=false)
     *
     * @var StatusJulgamentoImpugnacao
     */
    private $statusJulgamentoImpugnacao;

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
     * Fábrica de instância de 'JulgamentoRecursoImpugnacao'.
     *
     * @param array $data
     * @return JulgamentoRecursoImpugnacao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoRecursoImpugnacao = new JulgamentoRecursoImpugnacao();

        if ($data != null) {
            $julgamentoRecursoImpugnacao->setId(Utils::getValue('id', $data));
            $julgamentoRecursoImpugnacao->setArquivo(Utils::getValue('arquivo', $data));
            $julgamentoRecursoImpugnacao->setTamanho(Utils::getValue('tamanho', $data));
            $julgamentoRecursoImpugnacao->setDescricao(Utils::getValue('descricao', $data));
            $julgamentoRecursoImpugnacao->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $julgamentoRecursoImpugnacao->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $julgamentoRecursoImpugnacao->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $pedidoImpugnacao = Utils::getValue('pedidoImpugnacao', $data);
            if(!empty($pedidoImpugnacao)) {
                $julgamentoRecursoImpugnacao->setPedidoImpugnacao(
                    PedidoImpugnacao::newInstance($pedidoImpugnacao)
                );
            }

            $statusJulgamentoImpugnacao = Utils::getValue('statusJulgamentoImpugnacao', $data);
            if(!empty($statusJulgamentoImpugnacao)) {
                $julgamentoRecursoImpugnacao->setStatusJulgamentoImpugnacao(
                    StatusJulgamentoImpugnacao::newInstance($statusJulgamentoImpugnacao)
                );
            }

            $usuario = Utils::getValue('usuario', $data);
            if(!empty($usuario)) {
                $julgamentoRecursoImpugnacao->setUsuario(
                    Usuario::newInstance($usuario)
                );
            }
        }
        return $julgamentoRecursoImpugnacao;
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
     * @return PedidoImpugnacao
     */
    public function getPedidoImpugnacao()
    {
        return $this->pedidoImpugnacao;
    }

    /**
     * @param PedidoImpugnacao $pedidoImpugnacao
     */
    public function setPedidoImpugnacao($pedidoImpugnacao): void
    {
        $this->pedidoImpugnacao = $pedidoImpugnacao;
    }

    /**
     * @return StatusJulgamentoImpugnacao
     */
    public function getStatusJulgamentoImpugnacao()
    {
        return $this->statusJulgamentoImpugnacao;
    }

    /**
     * @param StatusJulgamentoImpugnacao $statusJulgamentoImpugnacao
     */
    public function setStatusJulgamentoImpugnacao($statusJulgamentoImpugnacao): void
    {
        $this->statusJulgamentoImpugnacao = $statusJulgamentoImpugnacao;
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
