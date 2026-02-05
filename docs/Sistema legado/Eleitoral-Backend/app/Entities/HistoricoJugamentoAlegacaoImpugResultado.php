<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 27/02/2020
 * Time: 14:30
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Histórico de Julgamento de alegação de impugnação de resultado'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\HistoricoJulgamentoAlegacaoImpugResultadoRepository")
 * @ORM\Table(schema="eleitoral", name="tb_hist_julgamento_alegacao_resultado")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class HistoricoJulgamentoAlegacaoImpugResultado extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_HIST_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_hist_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\JulgamentoAlegacaoImpugResultado
     */
    private $julgamentoAlegacaoImpugResultado;

    /**
     * @ORM\Column(name="ID_USUARIO", type="integer", nullable=false)
     *
     * @var integer
     */
    private $responsavel;

    /**
     * @ORM\Column(name="DS_ACAO", type="string", length=200, nullable=true)
     *
     * @var string
     */
    private $descricaoAcao;

    /**
     * @ORM\Column(name="DT_HISTORICO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $dataHistorico;

     /**
     * @ORM\Column(name="DS_ORIGEM", type="string", length=80, nullable=false)
     *
     * @var string
     */
    private $origem;

    /**
     * Fábrica de instância de Histórico Denuncia'.
     *
     * @param array $data
     * @return \App\Entities\HistoricoDenuncia
     */
    public static function newInstance($data = null)
    {
        $historico = new HistoricoJulgamentoAlegacaoImpugResultado();

        if ($data != null) {
            $historico->setId(Utils::getValue('id', $data));
            $historico->setResponsavel(Utils::getValue('responsavel', $data));
            $historico->setDescricaoAcao(Utils::getValue('descricaoAcao', $data));
            $historico->setDataHistorico(Utils::getValue('dataHistorico', $data));
            $historico->setOrigem(Utils::getValue('origem', $data));

            $julgamentoAlegacaoImpugResultado = Utils::getValue('julgamentoAlegacaoImpugResultado', $data);
            if (!empty($julgamentoAlegacaoImpugResultado)) {
                $historico->setDenuncia(JulgamentoAlegacaoImpugResultado::newInstance($julgamentoAlegacaoImpugResultado));
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
     * @return Denuncia
     */
    public function getDenuncia()
    {
        return $this->denuncia;
    }

    /**
     * @param Denuncia $denuncia
     */
    public function setDenuncia($denuncia): void
    {
        $this->denuncia = $denuncia;
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
     * @return \DateTime
     */
    public function getDataHistorico()
    {
        return $this->dataHistorico;
    }

    /**
     * @param \DateTime $dataHistorico
     */
    public function setDataHistorico($dataHistorico): void
    {
        $this->dataHistorico = $dataHistorico;
    }

    /**
     * @return int
     */
    public function getOrigem()
    {
        return $this->origem;
    }

    /**
     * @param int $origem
     */
    public function setOrigem($origem): void
    {
        $this->origem = $origem;
    }

    /**
     * Get the value of descricaoAcao
     */ 
    public function getDescricaoAcao()
    {
        return $this->descricaoAcao;
    }

    /**
     * @param  string  $descricaoAcao
     */ 
    public function setDescricaoAcao($descricaoAcao)
    {
        $this->descricaoAcao = $descricaoAcao;
    }
}
