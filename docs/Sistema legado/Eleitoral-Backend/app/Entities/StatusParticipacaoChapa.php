<?php
/*
 * StatusParticipacaoChapa.php
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
 * Entidade de representação de 'Status de Participação da Chapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\StatusParticipacaoChapaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_STATUS_PARTIC_CHAPA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class StatusParticipacaoChapa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_STATUS_PARTIC_CHAPA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_status_partic_chapa_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_STATUS_PARTIC_CHAPA", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'Status de Participação da Chapa'.
     *
     * @param array $data
     * @return \App\Entities\StatusParticipacaoChapa
     */
    public static function newInstance($data = null)
    {
        $statusParticipacaoChapa = new StatusParticipacaoChapa();

        if ($data != null) {
            $statusParticipacaoChapa->setId(Utils::getValue('id', $data));
            $statusParticipacaoChapa->setDescricao(Utils::getValue('descricao', $data));
        }
        return $statusParticipacaoChapa;
    }

    /**
     * Fábrica de instância de 'StatusParticipacaoChapa'.
     *
     * @param $id
     * @return StatusParticipacaoChapa
     */
    public static function newInstanceById($id)
    {
        return self::newInstance(['id' => $id]);
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
