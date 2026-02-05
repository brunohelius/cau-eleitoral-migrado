<?php
/*
 * TipoSituacaoEncaminhamentoDenuncia.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'TipoSituacaoEncaminhamentoDenuncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TipoSituacaoEncaminhamentoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_TP_SITUACAO_ENCAMINHAMENTO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class TipoSituacaoEncaminhamentoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_TP_SITUACAO_ENCAMINHAMENTO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_tp_situacao_encaminhamento_denuncia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TP_SITUACAO_ENCAMINHAMENTO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'TipoSituacaoEncaminhamentoDenuncia'.
     *
     * @param null $data
     * @return TipoSituacaoEncaminhamentoDenuncia
     */
    public static function newInstance($data = null)
    {
        $tipoSituacao = new TipoSituacaoEncaminhamentoDenuncia();

        if ($data != null) {
            $tipoSituacao->setId(Utils::getValue('id', $data));
            $tipoSituacao->setDescricao(Utils::getValue('descricao', $data));

        }
        return $tipoSituacao;
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