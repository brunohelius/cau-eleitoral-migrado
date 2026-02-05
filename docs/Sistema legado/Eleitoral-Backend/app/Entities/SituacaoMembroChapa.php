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
use phpDocumentor\Reflection\Types\Self_;

/**
 * Entidade de representação de 'SituacaoMembroChapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\SituacaoMembroChapaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_SITUACAO_MEMBRO_CHAPA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class SituacaoMembroChapa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="ID_SITUACAO_MEMBRO_CHAPA", type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_SITUACAO_MEMBRO_CHAPA", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'SituacaoMembroChapa'.
     *
     * @param array $data
     * @return \App\Entities\SituacaoMembroChapa
     */
    public static function newInstance($data = null)
    {
        $situacaoMembroChapa = new SituacaoMembroChapa();

        if ($data != null) {
            $situacaoMembroChapa->setId(Utils::getValue('id', $data));
            $situacaoMembroChapa->setDescricao(Utils::getValue('descricao', $data));
        }
        return $situacaoMembroChapa;
    }

    /**
     * Fábrica de instância de 'SituacaoMembroChapa'.
     *
     * @param $id
     * @return SituacaoMembroChapa
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
