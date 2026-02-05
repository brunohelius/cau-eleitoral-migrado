<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use DateTime;

/**
 * Entidade de representação de 'ArquivoDefesaImpugnacao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoSubstituicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_JULGAMENTO_SUBSTITUICAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoSubstituicao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_JULGAMENTO_SUBSTITUICAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_julgamento_substituicao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_PARECER", type="string",nullable=false)
     *
     * @var string
     */
    private $parecer;

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
     * @ORM\OneToOne(targetEntity="App\Entities\PedidoSubstituicaoChapa")
     * @ORM\JoinColumn(name="ID_PEDIDO_SUBSTITUICAO_CHAPA", referencedColumnName="ID_PEDIDO_SUBSTITUICAO_CHAPA", nullable=false)
     *
     * @var PedidoSubstituicaoChapa
     */
    private $pedidoSubstituicaoChapa;

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
     * Fábrica de instância de 'JulgamentoSubstituicao'.
     *
     * @param array $data
     * @return JulgamentoSubstituicao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $julgamentoSubstituicao = new JulgamentoSubstituicao();

        if ($data != null) {
            $julgamentoSubstituicao->setId(Utils::getValue('id', $data));
            $julgamentoSubstituicao->setParecer(Utils::getValue('parecer', $data));
            $julgamentoSubstituicao->setTamanho(Utils::getValue('tamanho', $data));
            $julgamentoSubstituicao->setArquivo(Utils::getValue('arquivo', $data));
            $julgamentoSubstituicao->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $julgamentoSubstituicao->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $julgamentoSubstituicao->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $pedidoSubstituicaoChapa = Utils::getValue('pedidoSubstituicaoChapa', $data);
            if(!empty($pedidoSubstituicaoChapa)) {
                $julgamentoSubstituicao->setPedidoSubstituicaoChapa(
                    PedidoSubstituicaoChapa::newInstance($pedidoSubstituicaoChapa)
                );
            }

            $statusJulgamentoSubstituicao = Utils::getValue('statusJulgamentoSubstituicao', $data);
            if(!empty($statusJulgamentoSubstituicao)) {
                $julgamentoSubstituicao->setStatusJulgamentoSubstituicao(
                    StatusJulgamentoSubstituicao::newInstance($statusJulgamentoSubstituicao)
                );
            }

            $usuario = Utils::getValue('usuario', $data);
            if(!empty($usuario)) {
                $julgamentoSubstituicao->setUsuario(
                    Usuario::newInstance($usuario)
                );
            }
        }
        return $julgamentoSubstituicao;
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
    public function getParecer()
    {
        return $this->parecer;
    }

    /**
     * @param string $parecer
     */
    public function setParecer($parecer): void
    {
        $this->parecer = $parecer;
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
     * @return PedidoSubstituicaoChapa
     */
    public function getPedidoSubstituicaoChapa()
    {
        return $this->pedidoSubstituicaoChapa;
    }

    /**
     * @param PedidoSubstituicaoChapa $pedidoSubstituicaoChapa
     */
    public function setPedidoSubstituicaoChapa($pedidoSubstituicaoChapa): void
    {
        $this->pedidoSubstituicaoChapa = $pedidoSubstituicaoChapa;
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
