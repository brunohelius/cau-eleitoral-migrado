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
 * Entidade de representação de 'HistoricoExtratoConselheiro'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\HistoricoExtratoConselheiroRepository")
 * @ORM\Table(schema="eleitoral", name="TB_HIST_EXTRATO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class HistoricoExtratoConselheiro extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="id_hist_extrato", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_hist_extrato_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="nu_hist_extrato", type="integer", nullable=false)
     *
     * @var integer
     */
    private $numero;

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
     * @ORM\Column(name="JS_DADOS_EXTRATO", type="text", nullable=false)
     *
     * @var string
     */
    private $jsonDados;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\AtividadeSecundariaCalendario")
     * @ORM\JoinColumn(name="ID_ATIV_SECUNDARIA", referencedColumnName="ID_ATIV_SECUNDARIA", nullable=false)
     *
     * @var \App\Entities\AtividadeSecundariaCalendario
     */
    private $atividadeSecundaria;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ProporcaoConselheiroExtrato", mappedBy="historicoExtratoConselheiro", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $proporcoesConselheiroExtrato;

    /**
     * Retorna uma nova instância de 'HistoricoExtratoConselheiro'.
     *
     * @param null $data
     * @param bool $setJson
     * @return HistoricoExtratoConselheiro
     * @throws Exception
     */
    public static function newInstance($data = null, $setJson = true)
    {
        $historico = new HistoricoExtratoConselheiro();

        if ($data != null) {
            $historico->setId(Utils::getValue('id', $data));
            $historico->setDataHistorico(Utils::getValue('dataHistorico', $data));
            $historico->setDescricao(Utils::getValue('descricao', $data));
            $historico->setAcao(Utils::getValue('acao', $data));
            $historico->setResponsavel(Utils::getValue('responsavel', $data));
            $historico->setNumero(Utils::getValue('numero', $data));
            if ($setJson) {
                $historico->setJsonDados(Utils::getValue('jsonDados', $data));
            }
            $atividadeSecundariaCalendario = AtividadeSecundariaCalendario::newInstance(Utils::getValue('atividadeSecundaria', $data));
            if (!empty($atividadeSecundariaCalendario)) {
                $historico->setAtividadeSecundaria($atividadeSecundariaCalendario);
            }

            $proporcoesConselheiroExtrato = Utils::getValue('proporcoesConselheiroExtrato', $data);
            if (!empty($proporcoesConselheiroExtrato)) {
                foreach ($proporcoesConselheiroExtrato as $proporcaoConselheiroExtrato) {
                    $historico->adicionarProporcaoConselheiroExtrato(
                        ProporcaoConselheiroExtrato::newInstance($proporcaoConselheiroExtrato)
                    );
                }
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
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param int $numero
     */
    public function setNumero($numero): void
    {
        $this->numero = $numero;
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
     * @return string
     */
    public function getJsonDados()
    {
        return $this->jsonDados;
    }

    /**
     * @param string $jsonDados
     */
    public function setJsonDados($jsonDados): void
    {
        $this->jsonDados = $jsonDados;
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
     * @return array|ArrayCollection
     */
    public function getProporcoesConselheiroExtrato()
    {
        return $this->proporcoesConselheiroExtrato;
    }

    /**
     * @param array|ArrayCollection $proporcoesConselheiroExtrato
     */
    public function setProporcoesConselheiroExtrato($proporcoesConselheiroExtrato): void
    {
        $this->proporcoesConselheiroExtrato = $proporcoesConselheiroExtrato;
    }

    /**
     * Adiciona a 'AtividadePrincipalCalendario' à sua respectiva coleção.
     *
     * @param ProporcaoConselheiroExtrato $proporcaoConselheiroExtrato
     */
    private function adicionarProporcaoConselheiroExtrato(ProporcaoConselheiroExtrato $proporcaoConselheiroExtrato)
    {
        if ($this->getProporcoesConselheiroExtrato() == null) {
            $this->setProporcoesConselheiroExtrato(new ArrayCollection());
        }

        if (!empty($proporcaoConselheiroExtrato)) {
            $proporcaoConselheiroExtrato->setHistoricoExtratoConselheiro($this);
            $this->getProporcoesConselheiroExtrato()->add($proporcaoConselheiroExtrato);
        }
    }
}
