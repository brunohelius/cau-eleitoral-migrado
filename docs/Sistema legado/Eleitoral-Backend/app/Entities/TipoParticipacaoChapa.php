<?php
/*
 * TipoParticipacaoChapa.php
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
 * Entidade de representação de 'Tipo de Participação da Chapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoParticipacaoChapaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TP_PARTIC_CHAPA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TipoParticipacaoChapa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_TP_PARTIC_CHAPA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_tipo_partic_chapa_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TP_PARTIC_CHAPA", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'Tipo de Participação da Chapa'.
     *
     * @param array $data
     * @return \App\Entities\TipoParticipacaoChapa
     */
    public static function newInstance($data = null)
    {
        $tipoParticipacaoChapa = new TipoParticipacaoChapa();

        if ($data != null) {
            $tipoParticipacaoChapa->setId(Utils::getValue('id', $data));
            $tipoParticipacaoChapa->setDescricao(Utils::getValue('descricao', $data));
        }
        return $tipoParticipacaoChapa;
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
