<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 15/08/2019
 * Time: 14:39
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
 * Entidade de representação de 'Atividade Principal do Calendario'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\AtividadePrincipalCalendarioRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ATIV_PRINCIPAL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class AtividadePrincipalCalendario extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ATIV_PRINCIPAL", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_ativ_principal_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DT_INI", type="date", nullable=true)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataInicio;

    /**
     * @ORM\Column(name="DT_FIM", type="date", nullable=true)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataFim;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Calendario")
     * @ORM\JoinColumn(name="ID_CALENDARIO", referencedColumnName="ID_CALENDARIO", nullable=false)
     *
     * @var \App\Entities\Calendario
     */
    private $calendario;

    /**
     * @ORM\Column(name="DS_ATIV_PRINCIPAL", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="ST_OBEDECE_VIGENCIA", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $obedeceVigencia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\AtividadeSecundariaCalendario", mappedBy="atividadePrincipalCalendario", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $atividadesSecundarias;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\PrazoCalendario", mappedBy="atividadePrincipal")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $prazos;

    /**
     * @ORM\Column(name="NR_NIVEL", type="integer", nullable=false)
     *
     * @var integer
     */
    private $nivel;

    /**
     * Fábrica de instância de Atividade Principal do Calendario.
     *
     * @param array $data
     * @return AtividadePrincipalCalendario
     */
    public static function newInstance($data = null)
    {
        $atividadePrincipal = new AtividadePrincipalCalendario();

        if ($data != null) {
            $atividadePrincipal->setId(Utils::getValue('id', $data));
            $atividadePrincipal->setDataInicio(Utils::getValue('dataInicio', $data));
            $atividadePrincipal->setDataFim(Utils::getValue('dataFim', $data));
            $atividadePrincipal->setDescricao(Utils::getValue('descricao', $data));
            $atividadePrincipal->setObedeceVigencia(Utils::getBooleanValue('obedeceVigencia', $data));
            $atividadePrincipal->setNivel(Utils::getValue('nivel', $data));

            $calendario = Calendario::newInstance(Utils::getValue('calendario', $data));
            $atividadePrincipal->setCalendario($calendario);

            $atividadesSecundarias = Utils::getValue('atividadesSecundarias', $data);
            if (!empty($atividadesSecundarias)) {
                foreach ($atividadesSecundarias as $dataAtividadeSecundaria) {
                    $atividadePrincipal->adicionarAtividadeSecundaria(
                        AtividadeSecundariaCalendario::newInstance($dataAtividadeSecundaria)
                    );
                }
            }

            $prazos = Utils::getValue('prazos', $data);
            if (!empty($prazos)) {
                foreach ($prazos as $prazo) {
                    $atividadePrincipal->adicionarPrazo(PrazoCalendario::newInstance($prazo));
                }
            }
        }


        return $atividadePrincipal;
    }

    /**
     * Adiciona a 'AtividadeSecundariaCalendario' à sua respectiva coleção.
     *
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     */
    private function adicionarAtividadeSecundaria($atividadeSecundaria)
    {
        if ($this->getAtividadesSecundarias() == null) {
            $this->setAtividadesSecundarias(new ArrayCollection());
        }

        if (!empty($atividadeSecundaria)) {
            $atividadeSecundaria->setAtividadePrincipalCalendario($this);
            $this->getAtividadesSecundarias()->add($atividadeSecundaria);
        }
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
     * @return DateTime
     */
    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    /**
     * @param DateTime $dataInicio
     * @throws Exception
     */
    public function setDataInicio($dataInicio): void
    {
        if (is_string($dataInicio)) {
            $dataInicio = new DateTime($dataInicio);
        }
        $this->dataInicio = $dataInicio;
    }

    /**
     * @return DateTime
     */
    public function getDataFim()
    {
        return $this->dataFim;
    }

    /**
     * @param DateTime $dataFim
     * @throws Exception
     */
    public function setDataFim($dataFim): void
    {
        if (is_string($dataFim)) {
            $dataFim = new DateTime($dataFim);
        }
        $this->dataFim = $dataFim;
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
     * @return bool
     */
    public function isObedeceVigencia()
    {
        return $this->obedeceVigencia;
    }

    /**
     * @param bool $obedeceVigencia
     */
    public function setObedeceVigencia($obedeceVigencia): void
    {
        $this->obedeceVigencia = $obedeceVigencia;
    }

    /**
     * @return array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getAtividadesSecundarias()
    {
        return $this->atividadesSecundarias;
    }

    /**
     * @param array|\Doctrine\Common\Collections\ArrayCollection $atividadesSecundarias
     */
    public function setAtividadesSecundarias($atividadesSecundarias): void
    {
        $this->atividadesSecundarias = $atividadesSecundarias;
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
     * @return array|ArrayCollection
     */
    public function getPrazos()
    {
        return $this->prazos;
    }

    /**
     * @param array|ArrayCollection $prazos
     */
    public function setPrazos($prazos)
    {
        $this->prazos = $prazos;
    }
}
