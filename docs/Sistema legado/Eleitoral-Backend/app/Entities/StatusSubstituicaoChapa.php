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
 * Entidade de representação de 'Status de Substituição da Chapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\StatusSubstituicaoChapaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_STATUS_SUBSTITUICAO_CHAPA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class StatusSubstituicaoChapa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="ID_STATUS_SUBSTITUICAO_CHAPA", type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_STATUS_SUBSTITUICAO_CHAPA", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'Status de Substituição da Chapa'.
     *
     * @param array $data
     * @return \App\Entities\StatusSubstituicaoChapa
     */
    public static function newInstance($data = null)
    {
        $statusSubstituicaoChapa = new StatusSubstituicaoChapa();

        if ($data != null) {
            $statusSubstituicaoChapa->setId(Utils::getValue('id', $data));
            $statusSubstituicaoChapa->setDescricao(Utils::getValue('descricao', $data));
        }
        return $statusSubstituicaoChapa;
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