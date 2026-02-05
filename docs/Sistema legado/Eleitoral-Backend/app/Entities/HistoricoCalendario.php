<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 15/08/2019
 * Time: 09:59
 */

namespace App\Entities;

use App\Config\Constants;
use App\To\UsuarioTO;
use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Arr;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Histórico Calendario'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\HistoricoCalendarioRepository")
 * @ORM\Table(schema="eleitoral", name="TB_HIST_CALENDARIO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class HistoricoCalendario extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_HIST_CALENDARIO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_hist_calendario_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_ABA", type="string", length=100, nullable=true)
     *
     * @var string
     */
    private $descricaoAba;

    /**
     * @ORM\Column(name="ID_RESP", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $responsavel;

    /**
     * Trasiente.
     *
     * @var UsuarioTO
     */
    private $dadosResponsavel;

    /**
     * @ORM\Column(name="ID_ACAO", type="integer", length=1, nullable=false)
     *
     * @var integer
     */
    private $acao;

    /**
     * Trasiente.
     *
     * @var ArrayCollection
     */
    private $descricoesAcao;

    /**
     * @ORM\Column(name="DT_HISTORICO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Calendario")
     * @ORM\JoinColumn(name="ID_CALENDARIO", referencedColumnName="ID_CALENDARIO", nullable=false)
     *
     * @var \App\Entities\Calendario
     */
    private $calendario;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\JustificativaAlteracaoCalendario", mappedBy="historico", cascade={"persist", "remove"})
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $justificativaAlteracao;

    /**
     * Fábrica de instância de Histórico Calendario'.
     *
     * @param array $data
     * @return \App\Entities\HistoricoCalendario
     */
    public static function newInstance($data = null)
    {
        $historicoCalendario = new HistoricoCalendario();

        if ($data != null) {
            $historicoCalendario->setId(Utils::getValue('id', $data));
            $historicoCalendario->setCalendario(Utils::getValue('calendario', $data));
            $historicoCalendario->setDescricaoAba(Utils::getValue('descricaoAba', $data));
            $historicoCalendario->setAcao(Utils::getValue('acao', $data));
            $historicoCalendario->setData(Utils::getValue('data', $data));
            $historicoCalendario->setJustificativaAlteracao(Utils::getValue('justificativaAlteracao', $data));
        }
        return $historicoCalendario;
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
    public function getDescricaoAba()
    {
        return $this->descricaoAba;
    }

    /**
     * @param string $descricaoAba
     */
    public function setDescricaoAba($descricaoAba): void
    {
        $this->descricaoAba = $descricaoAba;
    }

    /**
     * @return int
     */
    public function getAcao()
    {
        return $this->acao;
    }

    /**
     * @param int $acao
     */
    public function setAcao($acao): void
    {
        $this->acao = $acao;
    }

    /**
     * @return ArrayCollection
     */
    public function getDescricoesAcao()
    {
        return $this->descricoesAcao;
    }

    /**
     * @param ArrayCollection $descricoesAcao
     */
    public function setDescricoesAcao(ArrayCollection $descricoesAcao)
    {
        $this->descricoesAcao = $descricoesAcao;
    }

    /**
     * @return DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param DateTime $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return Calendario
     */
    public function getCalendario()
    {
        return $this->calendario;
    }

    /**
     * @param Calendario $calendario
     */
    public function setCalendario($calendario): void
    {
        $this->calendario = $calendario;
    }

    /**
     * @return array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getJustificativaAlteracao()
    {
        return $this->justificativaAlteracao;
    }

    /**
     * @param array|\Doctrine\Common\Collections\ArrayCollection $justificativaAlteracao
     */
    public function setJustificativaAlteracao($justificativaAlteracao): void
    {
        $this->justificativaAlteracao = $justificativaAlteracao;
    }

    /**
     * @return int
     */
    public function getResponsavel()
    {
        return $this->responsavel;
    }

    /**
     * @param int $responsavel
     */
    public function setResponsavel($responsavel)
    {
        $this->responsavel = $responsavel;
    }

    /**
     * @return UsuarioTO
     */
    public function getDadosResponsavel()
    {
        return $this->dadosResponsavel;
    }

    /**
     * @param UsuarioTO $dadosResponsavel
     */
    public function setDadosResponsavel($dadosResponsavel)
    {
        $this->dadosResponsavel = $dadosResponsavel;
    }
}
