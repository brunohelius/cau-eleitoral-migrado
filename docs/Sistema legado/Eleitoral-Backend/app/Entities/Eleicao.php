<?php
/*
 * Eleicao.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Entities;

use App\To\TipoProcessoTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Eleicao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\EleicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ELEICAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class Eleicao extends Entity
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ELEICAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_ELEICAO_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="NU_ANO", type="integer", length=4, nullable=false)
     *
     * @var integer
     */
    private $ano;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Calendario")
     * @ORM\JoinColumn(name="ID_CALENDARIO", referencedColumnName="ID_CALENDARIO", nullable=false)
     *
     * @var \App\Entities\Calendario
     */
    private $calendario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoProcesso")
     * @ORM\JoinColumn(name="ID_TIPO_PROCESSO", referencedColumnName="ID_TIPO_PROCESSO", nullable=false)
     *
     * @var TipoProcesso
     */
    private $tipoProcesso;

    /**
     * @ORM\Column(name="SQ_ANO", type="integer", length=3, nullable=false)
     *
     * @var integer
     */
    private $sequenciaAno;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\EleicaoSituacao", mappedBy="eleicao", cascade={"persist"})
     *
     * @var array|ArrayCollection
     */
    private $situacoes;

    /**
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'Eleicao'.
     *
     * @param array $data
     * @return Eleicao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $eleicao = new Eleicao();

        if ($data != null) {
            $eleicao->setId(Utils::getValue('id', $data));
            $eleicao->setAno(Utils::getValue('ano', $data));
            $eleicao->setSequenciaAno(Utils::getValue('sequenciaAno', $data));
            $eleicao->setDescricao($eleicao->getSequenciaFormatada());

            $situacoes = Utils::getValue('situacoes', $data);
            if (!empty($situacoes)) {
                foreach ($situacoes as $dataSituacao) {
                    $eleicao->adicionarSituacao(EleicaoSituacao::newInstance($dataSituacao));
                }
            }

            $tipoProcesso = Utils::getValue('tipoProcesso', $data);
            if (!empty($tipoProcesso)) {
                $eleicao->setTipoProcesso(TipoProcesso::newInstance($tipoProcesso));
            }
        }

        return $eleicao;
    }

    /**
     * Adiciona o 'CalendarioSituacao' à sua respectiva coleção.
     *
     * @param EleicaoSituacao $eleicaoSituacao
     */
    private function adicionarSituacao(EleicaoSituacao $eleicaoSituacao)
    {
        if ($this->getSituacoes() == null) {
            $this->setSituacoes(new ArrayCollection());
        }

        if (!empty($eleicaoSituacao)) {
            $eleicaoSituacao->setEleicao($this);
            $this->getSituacoes()->add($eleicaoSituacao);
        }
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
     * @return int
     */
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * @param int $ano
     */
    public function setAno($ano): void
    {
        $this->ano = $ano;
    }

    /**
     * @return Calendario
     */
    public function getCalendario()
    {
        return $this->calendario;
    }

    /**
     * @param Calendario $calendario
     */
    public function setCalendario($calendario)
    {
        $this->calendario = $calendario;
    }

    /**
     * @return TipoProcesso
     */
    public function getTipoProcesso()
    {
        return $this->tipoProcesso;
    }

    /**
     * @param TipoProcesso $tipoProcesso
     */
    public function setTipoProcesso($tipoProcesso)
    {
        $this->tipoProcesso = $tipoProcesso;
    }

    /**
     * @return int
     */
    public function getSequenciaAno()
    {
        return $this->sequenciaAno;
    }

    /**
     * @param int $sequenciaAno
     */
    public function setSequenciaAno($sequenciaAno)
    {
        $this->sequenciaAno = $sequenciaAno;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getSituacoes()
    {
        return $this->situacoes;
    }

    /**
     * @param array|ArrayCollection $situacoes
     */
    public function setSituacoes($situacoes)
    {
        $this->situacoes = $situacoes;
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
    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * Retorna o sequencial da eleição formatado.
     *
     * @param $sequencial
     * @return string|null
     */
    public function getSequenciaFormatada()
    {
        $sequenciaFormatada = null;

        if (!empty($this->sequenciaAno) and !empty($this->ano)) {
            $sequenciaFormatada =
                $this->ano . '/' . str_pad($this->sequenciaAno, 3, "0", STR_PAD_LEFT);
        }

        return $sequenciaFormatada;
    }
}
