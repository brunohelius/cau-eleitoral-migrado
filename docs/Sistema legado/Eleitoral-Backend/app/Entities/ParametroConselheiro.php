<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 07/11/2019
 * Time: 16:58
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'ParametroConselheiro'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ParametroConselheiroRepository")
 * @ORM\Table(schema="eleitoral", name="TB_PARAM_CONSELHEIRO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ParametroConselheiro extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_PARAM_CONSELHEIRO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_PARAM_CONSELHEIRO_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="ID_CAU_UF", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $idCauUf;

    /**
     * @ORM\Column(name="ST_EDITADO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $situacaoEditado;

    /**
     * @ORM\Column(name="QTD_PROFISSIONAL", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $qtdProfissional;

    /**
     * @ORM\Column(name="NU_PROPORCAO_CONSELHEIRO", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $numeroProporcaoConselheiro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Lei")
     * @ORM\JoinColumn(name="ID_LEI", referencedColumnName="ID_LEI", nullable=true)
     *
     * @var \App\Entities\Lei
     */
    private $lei;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\AtividadeSecundariaCalendario", fetch="LAZY")
     * @ORM\JoinColumn(name="ID_ATIV_SECUNDARIA", referencedColumnName="ID_ATIV_SECUNDARIA", nullable=false)
     *
     * @var \App\Entities\AtividadeSecundariaCalendario
     */
    private $atividadeSecundaria;

    /**
     * @var string
     */
    private $prefixo;

    /**
     * @var string
     */
    private $descricao;

    /**
     * Quantidade atual de Profissionais, campo para historico do atualizar
     *
     * @var int
     */
    private $qtdAtual;

    /**
     * @var HistoricoParametroConselheiro
     */
    private $historicoParametroRecente;

    /**
     * @ORM\Column(name="qtd_minima_criterio", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $qtdMinimaCriterio;

    /**
     * @ORM\Column(name="qtd_minima_cotista", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $qtdMinimaCotista;

    /**
     * Fábrica de instância de 'ParametroConselheiro'.
     *
     * @param array $data
     * @return ParametroConselheiro
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $parametroConselheiro = new ParametroConselheiro();

        if ($data != null) {
            $parametroConselheiro->setId(Utils::getValue('id', $data));
            $parametroConselheiro->setIdCauUf(Utils::getValue('idCauUf', $data));
            $parametroConselheiro->setSituacaoEditado(Utils::getBooleanValue('situacaoEditado', $data));
            $parametroConselheiro->setQtdProfissional(Utils::getValue('qtdProfissional', $data));
            $parametroConselheiro->setNumeroProporcaoConselheiro(Utils::getValue('numeroProporcaoConselheiro', $data));
            $parametroConselheiro->setQtdAtual(Utils::getValue('qtdAtual', $data));

            $lei = Lei::newInstance(Utils::getValue('lei', $data));
            if (!empty($lei->getId())) {
                $parametroConselheiro->setLei($lei);
            }

            $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance(Utils::getValue('atividadeSecundaria', $data));
            if (!empty($atividadeSecundaria->getId())) {
                $parametroConselheiro->setAtividadeSecundaria($atividadeSecundaria);
            }

            $parametroConselheiro->setPrefixo(Utils::getValue('prefixo', $data));
            $parametroConselheiro->setDescricao(Utils::getValue('descricao', $data));
        }
        return $parametroConselheiro;
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
    public function getIdCauUf()
    {
        return $this->idCauUf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf($idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return bool
     */
    public function isSituacaoEditado()
    {
        return $this->situacaoEditado;
    }

    /**
     * @param bool $situacaoEditado
     */
    public function setSituacaoEditado($situacaoEditado): void
    {
        $this->situacaoEditado = $situacaoEditado;
    }

    /**
     * @return int
     */
    public function getQtdProfissional()
    {
        return $this->qtdProfissional;
    }

    /**
     * @param int $qtdProfissional
     */
    public function setQtdProfissional($qtdProfissional): void
    {
        $this->qtdProfissional = $qtdProfissional;
    }

    /**
     * @return int
     */
    public function getNumeroProporcaoConselheiro()
    {
        return $this->numeroProporcaoConselheiro;
    }

    /**
     * @param int $numeroProporcaoConselheiro
     */
    public function setNumeroProporcaoConselheiro($numeroProporcaoConselheiro): void
    {
        $this->numeroProporcaoConselheiro = $numeroProporcaoConselheiro;
    }

    /**
     * @return Lei
     */
    public function getLei()
    {
        return $this->lei;
    }

    /**
     * @param Lei $lei
     */
    public function setLei($lei): void
    {
        $this->lei = $lei;
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

    public function setPrefixo($prefixo)
    {
        $this->prefixo = $prefixo;
    }

    public function getPrefixo()
    {
        return $this->prefixo;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setQtdAtual($qtdAtual)
    {
        $this->qtdAtual = $qtdAtual;
    }

    public function getQtdAtual()
    {
        return $this->qtdAtual;
    }

    /**
     * @var HistoricoParametroConselheiro $historicoParametroRecente
     */
    public function setHistoricoParametroRecente($historicoParametroRecente)
    {
        $this->historicoParametroRecente = $historicoParametroRecente;
    }

    /**
     * @return HistoricoParametroConselheiro
     */
    public function getHistoricoParametroRecente()
    {
        return $this->historicoParametroRecente;
    }

    /**
     * @return int
     */
    public function getQtdMinimaCriterio()
    {
        return $this->qtdMinimaCriterio;
    }

    /**
     * @param int $qtdMinimaCriterio
     */
    public function setQtdMinimaCriterio($qtdMinimaCriterio): void
    {
        $this->qtdMinimaCriterio = $qtdMinimaCriterio;
    }

    /**
     * @return int
     */
    public function getQtdMinimaCotista()
    {
        return $this->qtdMinimaCotista;
    }

    /**
     * @param int $qtdMinimaCotista
     */
    public function setQtdMinimaCotista($qtdMinimaCotista): void
    {
        $this->qtdMinimaCotista = $qtdMinimaCotista;
    }
}
