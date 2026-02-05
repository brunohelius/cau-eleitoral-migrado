<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 15/08/2019
 * Time: 10:26
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Justificativa de Alteração do Calendario'.
 *
 * @ORM\Entity(repositoryClass="JustificativaAlteracaoCalendarioRepository")
 * @ORM\Table(schema="eleitoral", name="TB_JUSTIFIC_ALTERACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class JustificativaAlteracaoCalendario extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_JUSTIFIC_ALTERACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_justific_alteracao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_ALTERACAO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="DS_JUSTIFICATIVA", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $justificativa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\HistoricoCalendario")
     * @ORM\JoinColumn(name="ID_HIST_CALENDARIO", referencedColumnName="ID_HIST_CALENDARIO", nullable=false)
     *
     * @var \App\Entities\HistoricoCalendario
     */
    private $historico;

    /**
     * Fábrica de instância de Justificativa de Alteração do Calendario'.
     *
     * @param array $data
     * @return \App\Entities\JustificativaAlteracaoCalendario
     */
    public static function newInstance($data = null)
    {
        $justificativaAlteracao = new JustificativaAlteracaoCalendario();

        if ($data != null) {
            $justificativaAlteracao->setId(Utils::getValue('id', $data));
            $justificativaAlteracao->setDescricao(Utils::getValue('descricao', $data));
            $justificativaAlteracao->setJustificativa(Utils::getValue('justificativa', $data));
            $justificativaAlteracao->setHistorico(Utils::getValue('historico', $data));
        }
        return $justificativaAlteracao;
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
     * @return HistoricoCalendario
     */
    public function getHistorico()
    {
        return $this->historico;
    }

    /**
     * @param HistoricoCalendario $historico
     */
    public function setHistorico($historico): void
    {
        $this->historico = $historico;
    }
}