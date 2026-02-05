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
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoAlegacaoImpugResultadoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_JULGAMENTO_ALEGACAO_RESULTADO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoAlegacaoImpugResultado extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_JULGAMENTO_ALEGACAO_RESULTADO_ID_SEQ", initialValue=1, allocationSize=1)
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
     * @ORM\ManyToOne(targetEntity="StatusJulgamentoAlegacaoResultado")
     * @ORM\JoinColumn(name="ID_STATUS_JULGAMENTO_ALEGACAO_RESULTADO", referencedColumnName="ID", nullable=false)
     *
     * @var StatusJulgamentoAlegacaoResultado
     */
    private $statusJulgamentoAlegacaoResultado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Usuario")
     * @ORM\JoinColumn(name="ID_USUARIO_INCLUSAO", referencedColumnName="id", nullable=false)
     * @var Usuario
     */
    private $usuario;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\RecursoImpugnacaoResultado", mappedBy="julgamentoAlegacaoImpugResultado", cascade={"persist"}, fetch="EXTRA_LAZY")
     *
     * @var RecursoImpugnacaoResultado[]|array|ArrayCollection|null
     */
    private $recursosJulgamentoAlegacaoImpugResultado;

    /**
     * Fábrica de instância de 'JulgamentoAlegacaoImpugResultado'.
     *
     * @param array $data
     * @return JulgamentoAlegacaoImpugResultado
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoAlegacaoImpugResultado = new JulgamentoAlegacaoImpugResultado();

        if ($data != null) {
            $julgamentoAlegacaoImpugResultado->setId(Utils::getValue('id', $data));
            $julgamentoAlegacaoImpugResultado->setDescricao(Utils::getValue('descricao', $data));
            $julgamentoAlegacaoImpugResultado->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $julgamentoAlegacaoImpugResultado->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $julgamentoAlegacaoImpugResultado->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $impugnacaoResultado = Utils::getValue('impugnacaoResultado', $data);
            if(!empty($impugnacaoResultado)) {
                $julgamentoAlegacaoImpugResultado->setImpugnacaoResultado(ImpugnacaoResultado::newInstance(
                    $impugnacaoResultado
                ));
            }

            $statusJulgamentoAlegacaoResultado = Utils::getValue('statusJulgamentoAlegacaoResultado', $data);
            if(!empty($statusJulgamentoAlegacaoResultado)) {
                $julgamentoAlegacaoImpugResultado->setStatusJulgamentoAlegacaoResultado(
                    StatusJulgamentoAlegacaoResultado::newInstance($statusJulgamentoAlegacaoResultado)
                );
            }

            $usuario = Utils::getValue('usuario', $data);
            if(!empty($usuario)) {
                $julgamentoAlegacaoImpugResultado->setUsuario(Usuario::newInstance($usuario));
            }
        }
        return $julgamentoAlegacaoImpugResultado;
    }

    /**
     * Fábrica de instância de 'JulgamentoAlegacaoImpugResultado'.
     *
     * @param JulgamentoAlegacaoImpugResultadoTO|null $to
     * @return JulgamentoAlegacaoImpugResultado
     */
    public static function newInstanceFromTo(JulgamentoAlegacaoImpugResultadoTO $to = null)
    {
        $julgamentoAlegacaoImpugResultado = new JulgamentoAlegacaoImpugResultado();
        if ($to != null) {
            $julgamentoAlegacaoImpugResultado->setId($to->getId());
            $julgamentoAlegacaoImpugResultado->setDataCadastro($to->getDataCadastro());
            $julgamentoAlegacaoImpugResultado->setDescricao($to->getDescricao());
            $impugnacaoResultado = !empty($to->getImpugnacaoResultado()) ? $to->getImpugnacaoResultado() : null;
            $julgamentoAlegacaoImpugResultado->setImpugnacaoResultado($impugnacaoResultado);
            $arquivo = !empty($to->getArquivos()) ? $to->getArquivos()[0] : null;
            if(!empty($arquivo)) {
                $julgamentoAlegacaoImpugResultado->setNomeArquivo($arquivo->getNome());
                $julgamentoAlegacaoImpugResultado->setNomeArquivoFisico($arquivo->getNomeFisico());
            }
            $usuario = !empty($to->getUsuario()) ? $to->getUsuario() : null;
            $julgamentoAlegacaoImpugResultado->setUsuario($usuario);
        }
        return $julgamentoAlegacaoImpugResultado;
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
     * @return StatusJulgamentoAlegacaoResultado
     */
    public function getStatusJulgamentoAlegacaoResultado()
    {
        return $this->statusJulgamentoAlegacaoResultado;
    }

    /**
     * @param StatusJulgamentoAlegacaoResultado $statusJulgamentoAlegacaoResultado
     */
    public function setStatusJulgamentoAlegacaoResultado($statusJulgamentoAlegacaoResultado): void
    {
        $this->statusJulgamentoAlegacaoResultado = $statusJulgamentoAlegacaoResultado;
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
     * @return RecursoImpugnacaoResultado[]|array|ArrayCollection|null
     */
    public function getRecursosJulgamentoAlegacaoImpugResultado()
    {
        return $this->recursosJulgamentoAlegacaoImpugResultado;
    }

    /**
     * @param RecursoImpugnacaoResultado[]|array|ArrayCollection|null $recursosJulgamentoAlegacaoImpugResultado
     */
    public function setRecursosJulgamentoAlegacaoImpugResultado($recursosJulgamentoAlegacaoImpugResultado): void
    {
        $this->recursosJulgamentoAlegacaoImpugResultado = $recursosJulgamentoAlegacaoImpugResultado;
    }
}
