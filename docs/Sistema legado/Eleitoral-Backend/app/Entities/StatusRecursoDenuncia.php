<?php
/*
 * StatusRecursoDenuncia.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'StatusRecursoDenuncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\StatusRecursoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_STATUS_RECURSO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class StatusRecursoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_STATUS_RECURSO_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_status_recurso_denuncia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\RecursoDenuncia")
     * @ORM\JoinColumn(name="ID_RECURSO_DENUNCIA", referencedColumnName="ID_RECURSO_CONTRARRAZAO_DENUNCIA", nullable=false)
     *
     * @var RecursoDenuncia
     */
    private $recursoDenuncia;

    /**
     * @ORM\Column(name="DT_SITUACAO", type="datetime", nullable=false)
     *
     * @var string
     */
    private $data;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\TipoJulgamento")
     * @ORM\JoinColumn(name="ID_TIPO_JULGAMENTO", referencedColumnName="ID_TIPO_JULGAMENTO", nullable=false)
     *
     * @var TipoJulgamento
     */
    private $situacao;

    /**
     * Fábrica de instância de Situação do Eleição'.
     *
     * @param array $data
     *
     * @return self
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data !== null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setData(Utils::getValue('data', $data));

            $situacao = Utils::getValue('situacao', $data);
            if (!empty($situacao)) {
                $instance->setSituacaoDenuncia(TipoJulgamento::newInstance($situacao));
            }

            $recursoDenuncia = Utils::getValue('recursoDenuncia', $data);
            if(!empty($recursoDenuncia)) {
                $instance->setRecursoDenuncia(RecursoDenuncia::newInstance($recursoDenuncia));
            }
        }
        return $instance;
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
     * @return RecursoDenuncia
     */
    public function getRecursoDenuncia()
    {
        return $this->recursoDenuncia;
    }

    /**
     * @param RecursoDenuncia $recursoDenuncia
     */
    public function setRecursoDenuncia(RecursoDenuncia $recursoDenuncia)
    {
        $this->recursoDenuncia = $recursoDenuncia;
    }

    /**
     * @return TipoJulgamento
     */
    public function getSituacaoDenuncia()
    {
        return $this->situacao;
    }

    /**
     * @param TipoJulgamento  $situacao
     */
    public function setSituacaoDenuncia(TipoJulgamento $situacao)
    {
        $this->situacao = $situacao;
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
    }
}
