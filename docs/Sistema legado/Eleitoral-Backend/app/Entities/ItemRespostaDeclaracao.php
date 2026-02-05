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
 * Entidade de representação de 'Item Resposta Declaração'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ItemRespostaDeclaracaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ITEM_RESPOSTA_DECLARACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ItemRespostaDeclaracao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ITEM_RESPOSTA_DECLARACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_item_resposta_declaracao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_ITEM_RESPOSTA_DECLARACAO", type="string", length=2000, nullable=true)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="ST_RESPOSTA", type="boolean", nullable=false, options={"default":false})
     *
     * @var boolean
     */
    private $situacaoResposta;

    /**
     * @ORM\Column(name="NR_SEQ", type="integer", nullable=false)
     *
     * @var integer
     */
    private $sequencial;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\RespostaDeclaracao")
     * @ORM\JoinColumn(name="ID_RESPOSTA_DECLARACAO", referencedColumnName="ID_RESPOSTA_DECLARACAO", nullable=false)
     *
     * @var RespostaDeclaracao
     */
    private $respostaDeclaracao;

    /**
     * Fábrica de instância de ItemRespostaDeclaracao.
     *
     * @param array $data
     * @return ItemRespostaDeclaracao
     */
    public static function newInstance($data = null)
    {
        $itemRespostaDeclaracao = new ItemRespostaDeclaracao();

        if ($data != null) {
            $itemRespostaDeclaracao->setId(Utils::getValue('id', $data));
            $itemRespostaDeclaracao->setDescricao(Utils::getValue('descricao', $data));
            $itemRespostaDeclaracao->setSituacaoResposta(Utils::getValue('situacaoResposta', $data));
            $itemRespostaDeclaracao->setSequencial(Utils::getValue('sequencial', $data));

            $respostaDeclaracao = Utils::getValue('respostaDeclaracao', $data);
            if (!empty($respostaDeclaracao)) {
                $itemRespostaDeclaracao->setRespostaDeclaracao(RespostaDeclaracao::newInstance($respostaDeclaracao));
            }

        }
        return $itemRespostaDeclaracao;
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

    /**
     * @return bool
     */
    public function isSituacaoResposta()
    {
        return $this->situacaoResposta;
    }

    /**
     * @param bool $situacaoResposta
     */
    public function setSituacaoResposta($situacaoResposta): void
    {
        $this->situacaoResposta = $situacaoResposta;
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

    /**
     * @return RespostaDeclaracao
     */
    public function getRespostaDeclaracao()
    {
        return $this->respostaDeclaracao;
    }

    /**
     * @param RespostaDeclaracao $respostaDeclaracao
     */
    public function setRespostaDeclaracao($respostaDeclaracao): void
    {
        $this->respostaDeclaracao = $respostaDeclaracao;
    }
}