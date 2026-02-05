<?php
/*
 * SituacaoEleicao.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Denúncia Situação'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DenunciaSituacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DENUNCIA_SITUACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DenunciaSituacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DENUNCIA_SITUACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_denuncia_situacao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\Denuncia
     */
    private $denuncia;

    /**
     * @ORM\Column(name="DT_SITUACAO", type="datetime", nullable=false)
     *
     * @var string
     */
    private $data;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\SituacaoDenuncia", inversedBy="denunciaSituacao")
     * @ORM\JoinColumn(name="ID_SITUACAO_DENUNCIA", referencedColumnName="ID_SITUACAO_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\SituacaoDenuncia
     */
    private $situacao;

    /**
     * Fábrica de instância de Situação do Eleição'.
     *
     * @param array $data
     * @return \App\Entities\DenunciaSituacao
     */
    public static function newInstance($data = null)
    {
        $denunciaSituacao = new DenunciaSituacao();

        if ($data != null) {
            $denunciaSituacao->setId(Utils::getValue('id', $data));
            $denunciaSituacao->setData(Utils::getValue('data', $data));

            $situacao = Utils::getValue('situacao', $data);
            if (!empty($situacao)) {
                $denunciaSituacao->setSituacaoDenuncia(SituacaoDenuncia::newInstance($situacao));
            }

            $denuncia = Utils::getValue('denuncia', $data);
            if(!empty($denuncia)) {
                $denunciaSituacao->setDenuncia(Denuncia::newInstance($denuncia));
            }
        }
        return $denunciaSituacao;
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
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    /**
     * @return Denuncia
     */
    public function getDenuncia()
    {
        return $this->denuncia;
    }

    /**
     * @param Denuncia  $denuncia
     */
    public function setDenuncia(Denuncia $denuncia)
    {
        $this->denuncia = $denuncia;
        return $this;
    }

    /**
     * @return Situacao
     */
    public function getSituacaoDenuncia()
    {
        return $this->situacao;
    }

    /**
     * @param SituacaoDenuncia  $situacao
     */
    public function setSituacaoDenuncia(SituacaoDenuncia $situacao)
    {
        $this->situacao = $situacao;
        return $this;
    }

    /**
     * @return  string
     */ 
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param  string  $data
     */ 
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}
