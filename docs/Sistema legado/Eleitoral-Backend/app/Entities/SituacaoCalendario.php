<?php
/*
 * SituacaoCalendario.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Situação do Calendario'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\SituacaoCalendarioRepository")
 * @ORM\Table(schema="eleitoral", name="TB_SITUACAO_CALENDARIO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class SituacaoCalendario extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_SITUACAO_CALENDARIO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_situacao_calendario_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_SITUACAO_CALENDARIO", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de Situação do Calendario'.
     *
     * @param array $data
     * @return \App\Entities\SituacaoCalendario
     */
    public static function newInstance($data = null)
    {
        $situacaoCalendario = new SituacaoCalendario();

        if ($data != null) {
            $situacaoCalendario->setId(Utils::getValue('id', $data));
            $situacaoCalendario->setDescricao(Utils::getValue('descricao', $data));
        }
        return $situacaoCalendario;
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
}