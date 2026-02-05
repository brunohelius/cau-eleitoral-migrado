<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'JulgamentoImpugnacao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_JULGAMENTO_IMPUGNACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoImpugnacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_JULGAMENTO_IMPUGNACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_julgamento_impugnacao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_JULGAMENTO_IMPUGNACAO", type="string",nullable=false)
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
     * @ORM\OneToMany(targetEntity="App\Entities\RecursoImpugnacao", mappedBy="julgamentoImpugnacao", fetch="EXTRA_LAZY")
     *
     * @var array|ArrayCollection
     */
    private $recursosImpugnacao;

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
     * Fábrica de instância de 'JulgamentoSubstituicao'.
     *
     * @param array $data
     * @return JulgamentoImpugnacao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoImpugnacao = new JulgamentoImpugnacao();

        if ($data != null) {
            $julgamentoImpugnacao->setId(Utils::getValue('id', $data));
            $julgamentoImpugnacao->setDescricao(Utils::getValue('descricao', $data));
            $julgamentoImpugnacao->setTamanho(Utils::getValue('tamanho', $data));
            $julgamentoImpugnacao->setArquivo(Utils::getValue('arquivo', $data));
            $julgamentoImpugnacao->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $julgamentoImpugnacao->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $julgamentoImpugnacao->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $pedidoImpugnacao = Utils::getValue('pedidoImpugnacao', $data);
            if(!empty($pedidoImpugnacao)) {
                $julgamentoImpugnacao->setPedidoImpugnacao(
                    PedidoImpugnacao::newInstance($pedidoImpugnacao)
                );
            }

            $statusJulgamentoImpugnacao = Utils::getValue('statusJulgamentoImpugnacao', $data);
            if(!empty($statusJulgamentoImpugnacao)) {
                $julgamentoImpugnacao->setStatusJulgamentoImpugnacao(
                    StatusJulgamentoImpugnacao::newInstance($statusJulgamentoImpugnacao)
                );
            }

            $usuario = Utils::getValue('usuario', $data);
            if(!empty($usuario)) {
                $julgamentoImpugnacao->setUsuario(
                    Usuario::newInstance($usuario)
                );
            }
        }
        return $julgamentoImpugnacao;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return string
     */
    public function getNomeArquivo(): ?string
    {
        return $this->nomeArquivo;
    }

    /**
     * @param string $nomeArquivo
     */
    public function setNomeArquivo(?string $nomeArquivo): void
    {
        $this->nomeArquivo = $nomeArquivo;
    }

    /**
     * @return string
     */
    public function getNomeArquivoFisico(): ?string
    {
        return $this->nomeArquivoFisico;
    }

    /**
     * @param string $nomeArquivoFisico
     */
    public function setNomeArquivoFisico(?string $nomeArquivoFisico): void
    {
        $this->nomeArquivoFisico = $nomeArquivoFisico;
    }

    /**
     * @return DateTime
     */
    public function getDataCadastro(): ?DateTime
    {
        return $this->dataCadastro;
    }

    /**
     * @param DateTime $dataCadastro
     */
    public function setDataCadastro(?DateTime $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return PedidoImpugnacao
     */
    public function getPedidoImpugnacao(): ?PedidoImpugnacao
    {
        return $this->pedidoImpugnacao;
    }

    /**
     * @param PedidoImpugnacao $pedidoImpugnacao
     */
    public function setPedidoImpugnacao(?PedidoImpugnacao $pedidoImpugnacao): void
    {
        $this->pedidoImpugnacao = $pedidoImpugnacao;
    }

    /**
     * @return StatusJulgamentoImpugnacao
     */
    public function getStatusJulgamentoImpugnacao(): ?StatusJulgamentoImpugnacao
    {
        return $this->statusJulgamentoImpugnacao;
    }

    /**
     * @param StatusJulgamentoImpugnacao $statusJulgamentoImpugnacao
     */
    public function setStatusJulgamentoImpugnacao(?StatusJulgamentoImpugnacao $statusJulgamentoImpugnacao): void
    {
        $this->statusJulgamentoImpugnacao = $statusJulgamentoImpugnacao;
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
     * @return array|ArrayCollection
     */
    public function getRecursosImpugnacao()
    {
        return $this->recursosImpugnacao;
    }

    /**
     * @param array|ArrayCollection $recursosImpugnacao
     */
    public function setRecursosImpugnacao($recursosImpugnacao): void
    {
        $this->recursosImpugnacao = $recursosImpugnacao;
    }
}
