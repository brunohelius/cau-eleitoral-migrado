<?php
/*
 * SituacaoEleicao.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Situação da Denúncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\SituacaoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_SITUACAO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class SituacaoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="ID_SITUACAO_DENUNCIA", type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_SITUACAO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @var DenunciaSituacao
     *
     * @ORM\OneToMany(targetEntity="DenunciaSituacao", mappedBy="situacao")
     */
    private $denunciaSituacao;

    /**
     * Fábrica de instância de Situação do Eleição'.
     *
     * @param array $data
     * @return \App\Entities\SituacaoDenuncia
     */
    public static function newInstance($data = null)
    {
        $situacaoDenuncia = new SituacaoDenuncia();

        if ($data != null) {
            $situacaoDenuncia->setId(Utils::getValue('id', $data));
            $situacaoDenuncia->setDescricao(Utils::getValue('descricao', $data));
        }
        return $situacaoDenuncia;
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
     * @return DenunciaSituacao
     */
    public function getDenunciaSituacao()
    {
        return $this->denunciaSituacao;
    }

    /**
     * @param DenunciaSituacao $denunciaSituacao
     */
    public function setDenunciaSituacao($denunciaSituacao): void
    {
        $this->denunciaSituacao = $denunciaSituacao;
    }
}
