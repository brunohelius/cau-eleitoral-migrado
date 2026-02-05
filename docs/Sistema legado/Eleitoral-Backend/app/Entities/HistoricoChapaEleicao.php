<?php

namespace App\Entities;

use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Histórico Chapa Eleição'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\HistoricoChapaEleicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_HIST_CHAPA_ELEICAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class HistoricoChapaEleicao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_HIST_CHAPA_ELEICAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_hist_chapa_eleicao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entities\Usuario")
     * @ORM\JoinColumn(name="ID_USUARIO", referencedColumnName="id", nullable=false)
     *
     * @var \App\Entities\Usuario
     */
    private $usuario;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_USUARIO", referencedColumnName="id", nullable=false)
     *
     * @var \App\Entities\Profissional
     */
    private $profissional;

    /**
     * @ORM\Column(name="ID_USUARIO", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $idUsuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\ChapaEleicao")
     * @ORM\JoinColumn(name="ID_CHAPA_ELEICAO", referencedColumnName="ID_CHAPA_ELEICAO", nullable=false)
     *
     * @var \App\Entities\ChapaEleicao
     */
    private $chapaEleicao;

    /**
     * @ORM\Column(name="DT_HISTORICO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $data;

    /**
     * @ORM\Column(name="DS_ACAO", type="string")
     *
     * @var string|null
     */
    private $descricaoAcao;

    /**
     * @ORM\Column(name="DS_JUSTIFICATIVA", type="string")
     *
     * @var string|null
     */
    private $descricaoJustificativa;

    /**
     * @ORM\Column(name="DS_ORIGEM", type="string", nullable=false)
     *
     * @var string
     */
    private $descricaoOrigem;

    /**
     * Fábrica de instância de 'Histórico Chapa Eleição'.
     *
     * @param array $data
     *
     * @return HistoricoChapaEleicao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setData(Utils::getValue('data', $data));
            $instance->setDescricaoOrigem(Utils::getValue('origem', $data));
            $instance->setIdUsuario(Utils::getValue('idUsuario', $data));
            $instance->setDescricaoAcao(Utils::getValue('descricaoAcao', $data));

            $chapaEleicao = Utils::getValue('chapaEleicao', $data);
            if (!empty($chapaEleicao)) {
                $instance->setChapaEleicao(ChapaEleicao::newInstance($chapaEleicao));
            }

            $justificativa = Utils::getValue('justificativa', $data);
            if (!empty($justificativa)) {
                $instance->setDescricaoJustificativa($justificativa);
            }
            $usuario = Utils::getValue('usuario', $data);
            if (!empty($usuario)) {
                $instance->setUsuario(Usuario::newInstance($usuario));
            }
            else{
                $instance->setUsuario(Usuario::newInstance(['id' => $instance->getIdUsuario()]));
            }

            $profissional = Utils::getValue('profissional', $data);
            if (!empty($profissional)) {
                $instance->setProfissional(Profissional::newInstance($profissional));
            }
            else{
                $instance->setProfissional(Profissional::newInstance(['id' => $instance->getIdUsuario()]));
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
     * @return int
     */
    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    /**
     * @param int $idUsuario
     */
    public function setIdUsuario(int $idUsuario): void
    {
        $this->idUsuario = $idUsuario;
    }

    /**
     * @return ChapaEleicao
     */
    public function getChapaEleicao(): ChapaEleicao
    {
        return $this->chapaEleicao;
    }

    /**
     * @param ChapaEleicao $chapaEleicao
     */
    public function setChapaEleicao(ChapaEleicao $chapaEleicao): void
    {
        $this->chapaEleicao = $chapaEleicao;
    }

    /**
     * @return DateTime
     */
    public function getData(): DateTime
    {
        return $this->data;
    }

    /**
     * @param DateTime $data
     */
    public function setData(DateTime $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getDescricaoAcao(): ?string
    {
        return $this->descricaoAcao;
    }

    /**
     * @param string $descricaoAcao
     */
    public function setDescricaoAcao(string $descricaoAcao): void
    {
        $this->descricaoAcao = $descricaoAcao;
    }

    /**
     * @return string|null
     */
    public function getDescricaoJustificativa(): ?string
    {
        return $this->descricaoJustificativa;
    }

    /**
     * @param string $descricaoJustificativa
     */
    public function setDescricaoJustificativa($descricaoJustificativa): void
    {
        $this->descricaoJustificativa = $descricaoJustificativa;
    }

    /**
     * @return string
     */
    public function getDescricaoOrigem(): string
    {
        return $this->descricaoOrigem;
    }

    /**
     * @param string $descricaoOrigem
     */
    public function setDescricaoOrigem(string $descricaoOrigem): void
    {
        $this->descricaoOrigem = $descricaoOrigem;
    }

    /**
     * @return Usuario
     */
    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    /**
     * @param Usuario $usuario
     */
    public function setUsuario(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    /**
     * @return Profissional
     */
    public function getProfissional(): Profissional
    {
        return $this->profissional;
    }

    /**
     * @param Profissional $profissional
     */
    public function setProfissional(Profissional $profissional): void
    {
        $this->profissional = $profissional;
    }
}
