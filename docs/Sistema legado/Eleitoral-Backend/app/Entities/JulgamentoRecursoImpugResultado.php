<?php

namespace App\Entities;

use App\To\ArquivoTO;
use App\To\JulgamentoAlegacaoImpugResultadoTO;
use App\Util\Utils;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'JulgamentoAlegacaoImpugResultado'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoRecursoImpugResultadoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_JULGAMENTO_RECURSO_RESULTADO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoImpugResultado extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_JULGAMENTO_RECURSO_RESULTADO_ID_SEQ", initialValue=1, allocationSize=1)
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
     * @ORM\OneToOne(targetEntity="App\Entities\ImpugnacaoResultado")
     * @ORM\JoinColumn(name="ID_PEDIDO_IMPUGNACAO_RESULTADO", referencedColumnName="ID", nullable=false)
     *
     * @var ImpugnacaoResultado
     */
    private $impugnacaoResultado;

    /**
     * @ORM\ManyToOne(targetEntity="StatusJulgamentoRecursoImpugResultado")
     * @ORM\JoinColumn(name="ID_STATUS_JULGAMENTO_RECURSO_RESULTADO", referencedColumnName="ID", nullable=false)
     *
     * @var StatusJulgamentoRecursoImpugResultado
     */
    private $statusJulgamentoRecursoImpugResultado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Usuario")
     * @ORM\JoinColumn(name="ID_USUARIO_INCLUSAO", referencedColumnName="id", nullable=false)
     * @var Usuario
     */
    private $usuario;

    /**
     * Fábrica de instância de 'JulgamentoRecursoImpugResultado'.
     *
     * @param array $data
     * @return JulgamentoRecursoImpugResultado
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoRecursoImpugResultado = new JulgamentoRecursoImpugResultado();

        if ($data != null) {
            $julgamentoRecursoImpugResultado->setId(Utils::getValue('id', $data));
            $julgamentoRecursoImpugResultado->setDescricao(Utils::getValue('descricao', $data));
            $julgamentoRecursoImpugResultado->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $julgamentoRecursoImpugResultado->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $julgamentoRecursoImpugResultado->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $impugnacaoResultado = Utils::getValue('impugnacaoResultado', $data);
            if(!empty($impugnacaoResultado)) {
                $julgamentoRecursoImpugResultado->setImpugnacaoResultado(ImpugnacaoResultado::newInstance(
                    $impugnacaoResultado
                ));
            }

            $statusJulgamentoRecursoResultado = Utils::getValue('statusJulgamentoRecursoImpugResultado', $data);
            if(!empty($statusJulgamentoRecursoResultado)) {
                $julgamentoRecursoImpugResultado->setStatusJulgamentoRecursoImpugResultado(
                    StatusJulgamentoRecursoImpugResultado::newInstance($statusJulgamentoRecursoResultado)
                );
            }

            $usuario = Utils::getValue('usuario', $data);
            if(!empty($usuario)) {
                $julgamentoRecursoImpugResultado->setUsuario(Usuario::newInstance($usuario));
            }
        }
        return $julgamentoRecursoImpugResultado;
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
     * @return ImpugnacaoResultado
     */
    public function getImpugnacaoResultado(): ?ImpugnacaoResultado
    {
        return $this->impugnacaoResultado;
    }

    /**
     * @param ImpugnacaoResultado $impugnacaoResultado
     */
    public function setImpugnacaoResultado(?ImpugnacaoResultado $impugnacaoResultado): void
    {
        $this->impugnacaoResultado = $impugnacaoResultado;
    }

    /**
     * @return StatusJulgamentoRecursoImpugResultado
     */
    public function getStatusJulgamentoRecursoImpugResultado()
    {
        return $this->statusJulgamentoRecursoImpugResultado;
    }

    /**
     * @param StatusJulgamentoRecursoImpugResultado $statusJulgamentoRecursoImpugResultado
     */
    public function setStatusJulgamentoRecursoImpugResultado($statusJulgamentoRecursoImpugResultado): void
    {
        $this->statusJulgamentoRecursoImpugResultado = $statusJulgamentoRecursoImpugResultado;
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
