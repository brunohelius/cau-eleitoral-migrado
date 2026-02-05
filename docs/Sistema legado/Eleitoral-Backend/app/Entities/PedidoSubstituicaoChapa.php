<?php

namespace App\Entities;

use App\Util\Utils;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'Pedido Substituicao Chapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\PedidoSubstituicaoChapaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_PEDIDO_SUBSTITUICAO_CHAPA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class PedidoSubstituicaoChapa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_PEDIDO_SUBSTITUICAO_CHAPA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_pedido_substituicao_chapa_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DT_CADASTRO", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $dataCadastro;

    /**
     * @ORM\Column(name="NU_PROTOCOLO", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $numeroProtocolo;

    /**
     * @ORM\Column(name="DS_JUSTIFICATIVA", type="text", nullable=false)
     *
     * @var string
     */
    private $justificativa;

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
     * @ORM\ManyToOne(targetEntity="App\Entities\ChapaEleicao")
     * @ORM\JoinColumn(name="ID_CHAPA_ELEICAO", referencedColumnName="ID_CHAPA_ELEICAO", nullable=false)
     *
     * @var ChapaEleicao
     */
    private $chapaEleicao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\StatusSubstituicaoChapa")
     * @ORM\JoinColumn(name="ID_STATUS_SUBSTITUICAO_CHAPA", referencedColumnName="ID_STATUS_SUBSTITUICAO_CHAPA", nullable=false)
     *
     * @var StatusSubstituicaoChapa
     */
    private $statusSubstituicaoChapa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\MembroChapaSubstituicao", mappedBy="pedidoSubstituicaoChapa")
     *
     * @var array|ArrayCollection|null
     */
    private $membrosChapaSubstituicao;

    /**
     * @ORM\Column(name="ID_PROFISSIONAL_INCLUSAO", type="integer")
     *
     * @var integer
     */
    private $idProfissionalInclusao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\JulgamentoSubstituicao", mappedBy="pedidoSubstituicaoChapa", fetch="EXTRA_LAZY")
     *
     * @var JulgamentoSubstituicao
     */
    private $julgamentoSubstituicao;

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
     * Transient
     *
     * @var \stdClass
     */
    private $profissional;

    /**
     * Fábrica de instância de 'PedidoSubstituicaoChapa'.
     *
     * @param array $data
     *
     * @return PedidoSubstituicaoChapa
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $instance->setNumeroProtocolo(Utils::getValue('numeroProtocolo', $data));
            $instance->setJustificativa(Utils::getValue('justificativa', $data));
            $instance->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $instance->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));
            $instance->setIdProfissionalInclusao(Utils::getValue('idProfissionalInclusao', $data));

            $chapaEleicao = Utils::getValue('chapaEleicao', $data);
            if(!empty($chapaEleicao) and is_array($chapaEleicao)){
                $instance->setChapaEleicao(ChapaEleicao::newInstance($chapaEleicao));
            }

            $statusSubstituicaoChapa = Utils::getValue('statusSubstituicaoChapa', $data);
            if(!empty($statusSubstituicaoChapa) and is_array($statusSubstituicaoChapa)){
                $instance->setStatusSubstituicaoChapa(
                    StatusSubstituicaoChapa::newInstance($statusSubstituicaoChapa)
                );
            }

            $membrosChapaSubstituicao = Utils::getValue('membrosChapaSubstituicao', $data);
            if(!empty($membrosChapaSubstituicao)) {
                $instance->setMembrosChapaSubstituicao(
                    array_map(function($membroChapaSubstituicao) {
                        return MembroChapaSubstituicao::newInstance($membroChapaSubstituicao);
                    }, $membrosChapaSubstituicao)
                );
            }

            $julgamentoSubstituicao = Utils::getValue('julgamentoSubstituicao', $data);
            if(!empty($julgamentoSubstituicao) and is_array($julgamentoSubstituicao)){
                $instance->setJulgamentoSubstituicao(
                    JulgamentoSubstituicao::newInstance($julgamentoSubstituicao)
                );
            }
        }
        return $instance;
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
     * @return int
     */
    public function getNumeroProtocolo()
    {
        return $this->numeroProtocolo;
    }

    /**
     * @param int $numeroProtocolo
     */
    public function setNumeroProtocolo($numeroProtocolo): void
    {
        $this->numeroProtocolo = $numeroProtocolo;
    }

    /**
     * @return ChapaEleicao
     */
    public function getChapaEleicao()
    {
        return $this->chapaEleicao;
    }

    /**
     * @param ChapaEleicao $chapaEleicao
     */
    public function setChapaEleicao($chapaEleicao): void
    {
        $this->chapaEleicao = $chapaEleicao;
    }

    /**
     * @return StatusSubstituicaoChapa
     */
    public function getStatusSubstituicaoChapa()
    {
        return $this->statusSubstituicaoChapa;
    }

    /**
     * @param StatusSubstituicaoChapa $statusSubstituicaoChapa
     */
    public function setStatusSubstituicaoChapa($statusSubstituicaoChapa): void
    {
        $this->statusSubstituicaoChapa = $statusSubstituicaoChapa;
    }

    /**
     * @return string
     */
    public function getJustificativa()
    {
        return $this->justificativa;
    }

    /**
     * @param string $justificativa
     */
    public function setJustificativa($justificativa): void
    {
        $this->justificativa = $justificativa;
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
     * @return array|ArrayCollection|null
     */
    public function getMembrosChapaSubstituicao()
    {
        return $this->membrosChapaSubstituicao;
    }

    /**
     * @param array|ArrayCollection|null $membrosChapaSubstituicao
     */
    public function setMembrosChapaSubstituicao($membrosChapaSubstituicao): void
    {
        $this->membrosChapaSubstituicao = $membrosChapaSubstituicao;
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
    public function getIdProfissionalInclusao()
    {
        return $this->idProfissionalInclusao;
    }

    /**
     * @param int $idProfissionalInclusao
     */
    public function setIdProfissionalInclusao($idProfissionalInclusao): void
    {
        $this->idProfissionalInclusao = $idProfissionalInclusao;
    }

    /**
     * @return \stdClass
     */
    public function getProfissional()
    {
        return $this->profissional;
    }

    /**
     * @param \stdClass $profissional
     */
    public function setProfissional($profissional): void
    {
        $this->profissional = $profissional;
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


}
