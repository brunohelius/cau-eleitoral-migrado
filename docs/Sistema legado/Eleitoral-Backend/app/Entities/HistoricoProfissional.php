<?php
/*
 * Historico.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'HistoricoProfissional'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\HistoricoProfissionalRepository")
 * @ORM\Table(schema="eleitoral", name="TB_HISTORICO_PROFISSIONAL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class HistoricoProfissional extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_HISTORICO_PROFISSIONAL", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_HISTORICO_PROFISSIONAL_ID_SEQ", initialValue=1, allocationSize=1)
     *
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="TP_ACAO", type="integer", nullable=false)
     *
     * @var integer
     */
    private $acao;

    /**
     * @ORM\Column(name="DT_HISTORICO_PROFISSIONAL", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataHistorico;

    /**
     * @ORM\Column(name="DS_HISTORICO_PROFISSIONAL", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="TP_HISTORICO_PROFISSIONAL", type="integer", nullable=false)
     *
     * @var integer
     */
    private $tipoHistorico;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_PROFISSIONAL", referencedColumnName="id", nullable=false)
     * @var Profissional
     */
    private $profissional;

    /**
     * @ORM\Column(name="ID_REFERENCIA", type="integer", nullable=false)
     *
     * @var integer
     */
    private $idReferencia;

    /**
     * @ORM\Column(name="DS_JUSTIFICATIVA", type="string", length=250)
     *
     * @var string
     */
    private $justificativa;

    /**
     * Retorna uma nova instância de 'Historico'.
     *
     * @param null $data
     * @return HistoricoProfissional
     */
    public static function newInstance($data = null)
    {
        $historico = new HistoricoProfissional();

        if ($data != null) {
            $historico->setId(Utils::getValue('id', $data));
            $historico->setAcao(Utils::getValue('acao', $data));
            $historico->setDescricao(Utils::getValue('descricao', $data));
            $historico->setDataHistorico(Utils::getValue('dataHistorico', $data));
            $historico->setTipoHistorico(Utils::getValue('tipoHistorico', $data));
            $historico->setIdReferencia(Utils::getValue('idReferencia', $data));
            $historico->setJustificativa(Utils::getValue('justificativa', $data));

            $profissional = Utils::getValue('profissional', $data);
            if(!empty($profissional) and is_array($profissional)){
                $historico->setProfissional(Profissional::newInstance($profissional));
            }
        }

        return $historico;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getAcao()
    {
        return $this->acao;
    }

    /**
     * @param mixed $acao
     */
    public function setAcao($acao)
    {
        $this->acao = $acao;
    }

    /**
     * @return mixed
     */
    public function getDataHistorico()
    {
        return $this->dataHistorico;
    }

    /**
     * @param mixed $dataHistorico
     */
    public function setDataHistorico($dataHistorico)
    {
        $this->dataHistorico = $dataHistorico;
    }

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param mixed $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return mixed
     */
    public function getTipoHistorico()
    {
        return $this->tipoHistorico;
    }

    /**
     * @param mixed $tipoHistorico
     */
    public function setTipoHistorico($tipoHistorico)
    {
        $this->tipoHistorico = $tipoHistorico;
    }

    /**
     * @return Profissional
     */
    public function getProfissional()
    {
        return $this->profissional;
    }

    /**
     * @param Profissional $profissional
     */
    public function setProfissional($profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return int
     */
    public function getIdReferencia()
    {
        return $this->idReferencia;
    }

    /**
     * @param int $idReferencia
     */
    public function setIdReferencia($idReferencia): void
    {
        $this->idReferencia = $idReferencia;
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
}
