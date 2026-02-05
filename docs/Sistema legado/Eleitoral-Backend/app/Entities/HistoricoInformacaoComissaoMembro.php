<?php
/*
 * HistoricoInformacaoComissaoMembro.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;


use App\Util\Utils;
use App\To\UsuarioTO;
use App\Entities\Entity;
use Datetime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Histórico de Informação de Comissão Membro'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\HistoricoInformacaoComissaoMembroRepository")
 * @ORM\Table(schema="eleitoral", name="TB_HIST_COMISSAO_MEMBRO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class HistoricoInformacaoComissaoMembro extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_HIST_COMISSAO_MEMBRO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_hist_comissao_membro_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="ID_ACAO", type="integer")
     *
     * @var integer
     */
    private $acao;

    /**
     * @ORM\Column(name="ID_RESP", type="integer")
     *
     * @var integer
     */
    private $responsavel;

    /**
     * @ORM\Column(name="DT_HISTORICO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var Datetime
     */
    private $dataHistorico;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\InformacaoComissaoMembro")
     * @ORM\JoinColumn(name="ID_INF_COMISSAO_MEMBRO", referencedColumnName="ID_INF_COMISSAO_MEMBRO", nullable=false)
     *
     * @var \App\Entities\InformacaoComissaoMembro
     */
    private $informacaoComissaoMembro;

    /**
     * @ORM\Column(name="DS_JUSTIFICATIVA", type="string", length=100, nullable=true)
     *
     * @var string
     */
    private $justificativa;

    /**
     * @ORM\Column(name="HIST_COMISSAO", type="boolean")
     *
     * @var boolean
     */
    private $histComissao;

    /**
     * Trasiente.
     *
     * @var string
     */
    private $descricaoAcao;

    /**
     * Trasiente.
     *
     * @var UsuarioTO
     */
    private $dadosResponsavel;

    /**
     * Retorna uma nova instância de 'HistoricoInformacaoComissaoMembro'.
     *
     * @param null $data
     * @return HistoricoInformacaoComissaoMembro
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $histInformacaoComissaoMembro = new HistoricoInformacaoComissaoMembro();

        if ($data != null) {
            $informacaoComissaoMembro = InformacaoComissaoMembro::newInstance(
                Utils::getValue('informacaoComissaoMembro', $data)
            );

            $histInformacaoComissaoMembro->setId(Utils::getValue('id', $data));
            $histInformacaoComissaoMembro->setAcao(Utils::getValue('acao', $data));
            $histInformacaoComissaoMembro->setInformacaoComissaoMembro($informacaoComissaoMembro);
            $histInformacaoComissaoMembro->setResponsavel(Utils::getValue('responsavel', $data));
            $histInformacaoComissaoMembro->setDataHistorico(Utils::getValue('dataHistorico', $data));
            $histInformacaoComissaoMembro->setJustificativa(Utils::getValue('justificativa', $data));
            $histInformacaoComissaoMembro->setHistComissao(Utils::getBooleanValue('histComissao', $data));
        }

        return $histInformacaoComissaoMembro;
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
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getAcao(): int
    {
        return $this->acao;
    }

    /**
     * @param int $acao
     */
    public function setAcao(int $acao): void
    {
        $this->acao = $acao;
    }

    /**
     * @return int
     */
    public function getResponsavel(): int
    {
        return $this->responsavel;
    }

    /**
     * @param int $responsavel
     */
    public function setResponsavel(int $responsavel): void
    {
        $this->responsavel = $responsavel;
    }

    /**
     * @return Datetime
     */
    public function getDataHistorico()
    {
        return $this->dataHistorico;
    }

    /**
     * @param Datetime $dataHistorico
     */
    public function setDataHistorico($dataHistorico): void
    {
        $this->dataHistorico = $dataHistorico;
    }

    /**
     * @return \App\Entities\InformacaoComissaoMembro
     */
    public function getInformacaoComissaoMembro()
    {
        return $this->informacaoComissaoMembro;
    }

    /**
     * @param \App\Entities\InformacaoComissaoMembro $informacaoComissaoMembro
     */
    public function setInformacaoComissaoMembro($informacaoComissaoMembro): void
    {
        $this->informacaoComissaoMembro = $informacaoComissaoMembro;
    }

    /**
     * @return string
     */
    public function getJustificativa()
    {
        return $this->justificativa;
    }

    /**
     * @param string $justificativa
     */
    public function setJustificativa($justificativa): void
    {
        $this->justificativa = $justificativa;
    }

    /**
     * @return string
     */
    public function getDescricaoAcao()
    {
        return $this->descricaoAcao;
    }

    /**
     * @param string $descricaoAcao
     */
    public function setDescricaoAcao($descricaoAcao): void
    {
        $this->descricaoAcao = $descricaoAcao;
    }

    /**
     * @return UsuarioTO
     */
    public function getDadosResponsavel()
    {
        return $this->dadosResponsavel;
    }

    /**
     * @param UsuarioTO $dadosResponsavel
     */
    public function setDadosResponsavel($dadosResponsavel): void
    {
        $this->dadosResponsavel = $dadosResponsavel;
    }

    /**
     * @return bool
     */
    public function isHistComissao()
    {
        return $this->histComissao;
    }

    /**
     * @param bool $histComissao
     */
    public function setHistComissao($histComissao): void
    {
        $this->histComissao = $histComissao;
    }
}
