<?php
/*
 * TipoRecursoImpugnacaoResultado.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */


namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'TipoRecursoImpugnacao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoRecursoImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TP_RECURSO_IMPUGNACAO_RESULTADO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TipoRecursoImpugnacaoResultado extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="ID", type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DESCRICAO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de Tipo de Recurso de Impugnaçao de resultado'.
     *
     * @param array $data
     * @return TipoRecursoImpugnacaoResultado
     */
    public static function newInstance($data = null)
    {
        $tipoPendencia = new TipoRecursoImpugnacaoResultado();

        if ($data != null) {
            $tipoPendencia->setId(Utils::getValue('id', $data));
            $tipoPendencia->setDescricao(Utils::getValue('descricao', $data));
        }
        return $tipoPendencia;
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