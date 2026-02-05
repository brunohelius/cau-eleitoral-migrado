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
 * Entidade de representação de 'TipoAlteracao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoAlteracaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TP_ALTERACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TipoAlteracao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_TP_ALTERACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_tp_alteracao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TP_ALTERACAO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de TipoAlteracao'.
     *
     * @param array $data
     * @return TipoAlteracao
     */
    public static function newInstance($data = null)
    {
        $tipoAlteracao = new TipoAlteracao();

        if ($data != null) {
            $tipoAlteracao->setId(Utils::getValue('id', $data));
            $tipoAlteracao->setDescricao(Utils::getValue('descricao', $data));
        }
        return $tipoAlteracao;
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