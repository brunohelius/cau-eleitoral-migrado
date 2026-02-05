<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 18/11/2019
 * Time: 11:54
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'HistoricoParametroConselheiro'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\HistoricoParametroConselheiroRepository")
 * @ORM\Table(schema="eleitoral", name="tb_hist_param_conselheiro")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class HistoricoParametroConselheiro extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="id_hist_param_conselheiro", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_hist_param_conselheiro_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="ID_ACAO", type="integer", nullable=false)
     *
     * @var integer
     */
    private $acao;

    /**
     * @ORM\Column(name="DT_HISTORICO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataHistorico;

    /**
     * @ORM\Column(name="DS_HISTORICO", type="string", length=100)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="ID_USUARIO", type="integer", nullable=false)
     *
     * @var integer
     */
    private $responsavel;

    /**
     * @ORM\Column(name="DS_JUSTIFICATIVA", type="string", length=100, nullable=true)
     *
     * @var string
     */
    private $justificativa;

    /**
     * @ORM\Column(name="ID_CAU_UF", type="integer", nullable=false)
     *
     * @var integer
     */
    private $idCauUf;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\AtividadeSecundariaCalendario")
     * @ORM\JoinColumn(name="ID_ATIV_SECUNDARIA", referencedColumnName="ID_ATIV_SECUNDARIA", nullable=false)
     *
     * @var \App\Entities\AtividadeSecundariaCalendario
     */
    private $atividadeSecundaria;

    /**
     * @var string
     */
    private $nomeResponsavel;

    /**
     * Retorna uma nova instância de 'HistoricoParametroConselheiro'.
     *
     * @param null $data
     * @return HistoricoParametroConselheiro
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $historico = new HistoricoParametroConselheiro();

        if ($data != null) {
            $historico->setId(Utils::getValue('id', $data));
            $historico->setDataHistorico(Utils::getValue('dataHistorico', $data));
            $historico->setDescricao(Utils::getValue('descricao', $data));
            $historico->setAcao(Utils::getValue('acao', $data));
            $historico->setResponsavel(Utils::getValue('responsavel', $data));
            $historico->setJustificativa(Utils::getValue('justificativa', $data));
            $historico->setIdCauUf(Utils::getValue('idCauUf', $data));

            $atividadeSecundariaCalendario = AtividadeSecundariaCalendario::newInstance(Utils::getValue('atividadeSecundaria', $data));
            if (!empty($atividadeSecundariaCalendario)) {
                $historico->setAtividadeSecundaria($atividadeSecundariaCalendario);
            }
        }

        return $historico;
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
     * @return DateTime
     */
    public function getDataHistorico()
    {
        return $this->dataHistorico;
    }

    /**
     * @param DateTime $dataHistorico
     */
    public function setDataHistorico($dataHistorico): void
    {
        $this->dataHistorico = $dataHistorico;
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
     * @return int
     */
    public function getResponsavel()
    {
        return $this->responsavel;
    }

    /**
     * @param int $responsavel
     */
    public function setResponsavel($responsavel): void
    {
        $this->responsavel = $responsavel;
    }

    /**
     * @param string $justificativa
     */
    public function setJustificativa($justificativa)
    {
        $this->justificativa = $justificativa;
    }

    /**
     * @return string
     */
    public function getJustificativa()
    {
        return $this->justificativa;
    }

    /**
     * @return AtividadeSecundariaCalendario
     */
    public function getAtividadeSecundaria()
    {
        return $this->atividadeSecundaria;
    }

    /**
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     */
    public function setAtividadeSecundaria($atividadeSecundaria): void
    {
        $this->atividadeSecundaria = $atividadeSecundaria;
    }

    /**
     * @return int
     */
    public function getNomeResponsavel()
    {
        return $this->nomeResponsavel;
    }

    /**
     * @param int $nomeResponsavel
     */
    public function setNomeResponsavel($nomeResponsavel): void
    {
        $this->nomeResponsavel = $nomeResponsavel;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf($idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return int
     */
    public function getIdCauUf()
    {
        return $this->idCauUf;
    }
}