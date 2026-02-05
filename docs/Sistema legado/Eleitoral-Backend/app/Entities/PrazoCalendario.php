<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 15/08/2019
 * Time: 16:38
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Prazo do Calendário'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\PrazoCalendarioRepository")
 * @ORM\Table(schema="eleitoral", name="TB_PRAZO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class PrazoCalendario extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_PRAZO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_prazo_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\PrazoCalendario", mappedBy="prazoPai", cascade={"remove"})
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $prazos;

    /**
     * @ORM\ManyToOne(targetEntity="PrazoCalendario", inversedBy="prazos")
     * @ORM\JoinColumn(name="ID_PRAZO_SUP", referencedColumnName="ID_PRAZO")
     */
    private $prazoPai;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\AtividadePrincipalCalendario")
     * @ORM\JoinColumn(name="ID_ATIV_PRINCIPAL", referencedColumnName="ID_ATIV_PRINCIPAL")
     *
     * @var \App\Entities\AtividadePrincipalCalendario
     */
    private $atividadePrincipal;

    /**
     * @ORM\Column(name="DS_ATIVIDADE", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricaoAtividade;

    /**
     * @ORM\Column(name="DURACAO", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $duracao;

    /**
     * @ORM\Column(name="ST_DIA_UTIL", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $situacaoDiaUtil;

    /**
     * @ORM\Column(name="NR_NIVEL", type="integer", nullable=false)
     *
     * @var integer
     */
    private $nivel;

    /**
     * @ORM\Column(name="DS_NIVEL", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricaoNivel;

    /**
     * Fábrica de instância de Prazo do Calendário.
     *
     * @param array $data
     * @return \App\Entities\PrazoCalendario
     */
    public static function newInstance($data = null)
    {
        $prazoCalendario = new PrazoCalendario();

        if ($data != null) {
            $prazoCalendario->setId(Utils::getValue('id', $data));
            $prazoCalendario->setNivel(Utils::getValue('nivel', $data));
            $prazoCalendario->setAtividadePrincipal(Utils::getValue('atividadePrincipal', $data));
            $prazoCalendario->setDescricaoAtividade(Utils::getValue('descricaoAtividade', $data));
            $prazoCalendario->setDuracao(Utils::getValue('duracao', $data));
            $prazoCalendario->setSituacaoDiaUtil(Utils::getBooleanValue('situacaoDiaUtil', $data));
            $prazoCalendario->setDescricaoNivel(Utils::getBooleanValue('descricaoNivel', $data));

            $prazos = Utils::getValue('prazos', $data);
            if (!empty($prazos)) {
                foreach ($prazos as $prazo) {
                    $prazoCalendario->adicionarPrazo(PrazoCalendario::newInstance($prazo));
                }
            }
        }
        return $prazoCalendario;
    }

    /**
     * Adiciona a 'PrazoCalendario' à sua respectiva coleção.
     *
     * @param PrazoCalendario $prazo
     */
    private function adicionarPrazo($prazo)
    {
        if ($this->getPrazos() == null) {
            $this->setPrazos(new ArrayCollection());
        }

        if (!empty($prazo)) {
            $prazo->setAtividadePrincipal($this);
            $this->getPrazos()->add($prazo);
        }
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
     * @return array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getPrazos()
    {
        return $this->prazos;
    }

    /**
     * @param array|\Doctrine\Common\Collections\ArrayCollection $prazo
     */
    public function setPrazos($prazos): void
    {
        $this->prazos = $prazos;
    }

    /**
     * @return PrazoCalendario
     */
    public function getPrazoPai()
    {
        return $this->prazoPai;
    }

    /**
     * @param PrazoCalendario $prazoPai
     */
    public function setPrazoPai($prazoPai): void
    {
        $this->prazoPai = $prazoPai;
    }

    /**
     * @return AtividadePrincipalCalendario
     */
    public function getAtividadePrincipal()
    {
        return $this->atividadePrincipal;
    }

    /**
     * @param AtividadePrincipalCalendario $atividadePrincipal
     */
    public function setAtividadePrincipal($atividadePrincipal)
    {
        $this->atividadePrincipal = $atividadePrincipal;
    }

    /**
     * @return string
     */
    public function getDescricaoAtividade()
    {
        return $this->descricaoAtividade;
    }

    /**
     * @param string $descricaoAtividade
     */
    public function setDescricaoAtividade($descricaoAtividade): void
    {
        $this->descricaoAtividade = $descricaoAtividade;
    }

    /**
     * @return int
     */
    public function getDuracao()
    {
        return $this->duracao;
    }

    /**
     * @param int $duracao
     */
    public function setDuracao($duracao): void
    {
        $this->duracao = $duracao;
    }

    /**
     * @return bool
     */
    public function isSituacaoDiaUtil()
    {
        return $this->situacaoDiaUtil;
    }

    /**
     * @param bool $situacaoDiaUtil
     */
    public function setSituacaoDiaUtil($situacaoDiaUtil): void
    {
        $this->situacaoDiaUtil = $situacaoDiaUtil;
    }

    /**
     * @return int
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * @param int $nivel
     */
    public function setNivel($nivel): void
    {
        $this->nivel = $nivel;
    }

    /**
     * @return string
     */
    public function getDescricaoNivel()
    {
        return $this->descricaoNivel;
    }

    /**
     * @param string $descricaoNivel
     */
    public function setDescricaoNivel($descricaoNivel): void
    {
        $this->descricaoNivel = $descricaoNivel;
    }
}