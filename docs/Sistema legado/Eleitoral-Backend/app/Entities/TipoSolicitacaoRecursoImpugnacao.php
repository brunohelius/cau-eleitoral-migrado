<?php
/*
 * TipoProcesso.php
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
 * Entidade de representação de 'TipoSolicitacaoRecursoImpugnacao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoSolicitacaoRecursoImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TP_SOLICITACAO_RECURSO_IMPUGNACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TipoSolicitacaoRecursoImpugnacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="ID_TP_SOLICITACAO_RECURSO_IMPUGNACAO", type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TP_SOLICITACAO_RECURSO_IMPUGNACAO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de Tipo de Pendência'.
     *
     * @param array $data
     * @return TipoSolicitacaoRecursoImpugnacao
     */
    public static function newInstance($data = null)
    {
        $tipoPendencia = new TipoSolicitacaoRecursoImpugnacao();

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