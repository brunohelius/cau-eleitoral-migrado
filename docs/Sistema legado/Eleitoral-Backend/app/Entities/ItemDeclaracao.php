<?php
/*
 * ItemDeclaracao.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * Entidade de representação do 'ItemDeclaracao' no portal SICCAU.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ItemDeclaracaoRepository")
 * @ORM\Table(schema="portal", name="TB_ITEM_DECLARACAO")
 *
 * @OA\Schema(schema="ItemDeclaracao")
 *
 * @package App\Entities
 */
class ItemDeclaracao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="ID_ITEM_DECLARACAO", type="integer")
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="portal.TB_ITEM_DECLARACAO_ID_SEQ", initialValue=1, allocationSize=1)
     *
     * @var integer
     * @OA\Property()
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Declaracao")
     * @ORM\JoinColumn(name="ID_DECLARACAO", referencedColumnName="ID_DECLARACAO", nullable=false)
     *
     * @OA\Property()
     * @var \App\Entities\Declaracao
     */
    private $declaracao;

    /**
     * @ORM\Column(name="DS_ITEM_DECLARACAO", type="string", nullable=false)
     *
     * @OA\Property()
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="NR_SEQ", type="integer", nullable=false)
     *
     * @OA\Property()
     * @var integer
     */
    private $sequencial;

    /**
     * Fábrica de instância de 'ItemDeclaracao'.
     *
     * @param array $data
     * @return \App\Entities\ItemDeclaracao
     */
    public static function newInstance($data = null)
    {
        $itemDeclaracao = new ItemDeclaracao();
        if ($data != null) {
            $itemDeclaracao->setId(Utils::getValue('id', $data));
            $itemDeclaracao->setDescricao(Utils::getValue('descricao', $data));
            $itemDeclaracao->setSequencial(Utils::getValue('sequencial', $data));
            $declaracao = Declaracao::newInstance(Utils::getValue('declaracao', $data));
            $itemDeclaracao->setDeclaracao($declaracao);
        }

        return $itemDeclaracao;
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
     * @return Declaracao
     */
    public function getDeclaracao()
    {
        return $this->declaracao;
    }

    /**
     * @param Declaracao $declaracao
     */
    public function setDeclaracao($declaracao): void
    {
        $this->declaracao = $declaracao;
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

    /**
     * @return int
     */
    public function getSequencial()
    {
        return $this->sequencial;
    }

    /**
     * @param int $sequencial
     */
    public function setSequencial($sequencial): void
    {
        $this->sequencial = $sequencial;
    }
}