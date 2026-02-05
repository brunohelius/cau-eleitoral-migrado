<?php
/*
 * EleicaoSituacao.php
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
use Exception;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação das situações da eleição.
 *
 * @ORM\Entity(repositoryClass="App\Repository\EleicaoSituacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ELEICAO_SITUACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class EleicaoSituacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ELEICAO_SITUACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_eleicao_situacao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Eleicao")
     * @ORM\JoinColumn(name="ID_ELEICAO", referencedColumnName="ID_ELEICAO", nullable=false)
     *
     * @var \App\Entities\Eleicao
     */
    private $eleicao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\SituacaoEleicao")
     * @ORM\JoinColumn(name="ID_SITUACAO_ELEICAO", referencedColumnName="ID_SITUACAO_ELEICAO", nullable=false)
     *
     * @var \App\Entities\SituacaoEleicao
     */
    private $situacaoEleicao;

    /**
     * @ORM\Column(name="DT_INI", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     * @ORM\OrderBy({"data" = "DESC"})
     * @var DateTime
     */
    private $data;

    /**
     * Fábrica de instância de Eleição Situação.
     *
     * @param array $data
     * @return EleicaoSituacao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $eleicaoSituacao = new EleicaoSituacao();

        if ($data != null) {
            $eleicaoSituacao->setId(Utils::getValue('id', $data));
            $situacao = SituacaoEleicao::newInstance(Utils::getValue('situacaoEleicao', $data));
            $eleicaoSituacao->setSituacaoEleicao($situacao);
            $eleicaoSituacao->setEleicao(Utils::getValue('eleicao', $data));
            $eleicaoSituacao->setData(Utils::getValue('data', $data));
        }
        return $eleicaoSituacao;
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
     * @return Eleicao
     */
    public function getEleicao()
    {
        return $this->eleicao;
    }

    /**
     * @param Eleicao $eleicao
     */
    public function setEleicao($eleicao): void
    {
        $this->eleicao = $eleicao;
    }

    /**
     * @return SituacaoEleicao
     */
    public function getSituacaoEleicao()
    {
        return $this->situacaoEleicao;
    }

    /**
     * @param SituacaoEleicao $situacaoEleicao
     */
    public function setSituacaoEleicao($situacaoEleicao): void
    {
        $this->situacaoEleicao = $situacaoEleicao;
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
     * @throws Exception
     */
    public function setData($data): void
    {
        if (is_string($data)) {
            $data = new DateTime($data);
        }
        $this->data = $data;
    }

}
