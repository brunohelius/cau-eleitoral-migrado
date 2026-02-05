<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 18/11/2019
 * Time: 11:54
 */

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada ao 'Histórico Extrato Conselheiros'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class HistoricoExtratoConselheiroTO
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $numero;

    /**
     * @var integer
     */
    private $acao;

    /**
     * @var \DateTime
     */
    private $dataHistorico;

    /**
     * @var string
     */
    private $descricao;

    /**
     * @var integer
     */
    private $responsavel;

    /**
     * @var AtividadeSecundariaTO
     */
    private $atividadeSecundaria;

    /**
     * @var integer
     */
    private $anoEleicao;

    /**
     * @var integer
     */
    private $sequenciaAnoEleicao;

    /**
     * @var integer
     */
    private $descricaoEleicao;

    /**
     * Retorna uma nova instância de 'HistoricoExtratoConselheiroTO'.
     *
     * @param null $data
     * @return HistoricoExtratoConselheiroTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $historico = new HistoricoExtratoConselheiroTO();

        if ($data != null) {
            $historico->setId(Utils::getValue('id', $data));
            $historico->setAcao(Utils::getValue('acao', $data));
            $historico->setNumero(Utils::getValue('numero', $data));
            $historico->setNumero(Utils::getValue('numero', $data));
            $historico->setDescricao(Utils::getValue('descricao', $data));
            $historico->setAnoEleicao(Utils::getValue('anoEleicao', $data));
            $historico->setResponsavel(Utils::getValue('responsavel', $data));
            $historico->setDataHistorico(Utils::getValue('dataHistorico', $data));
            $historico->setSequenciaAnoEleicao(Utils::getValue('sequenciaAnoEleicao', $data));

            $historico->setDescricaoEleicao($historico->getSequenciaFormatadaEleicao());

            $atividadeSecundariaCalendario = Utils::getValue('atividadeSecundaria', $data);
            if (!empty($atividadeSecundariaCalendario)) {
                $historico->setAtividadeSecundaria(
                    AtividadeSecundariaTO::newInstance($atividadeSecundariaCalendario)
                );
            }
        }

        return $historico;
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
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param int $numero
     */
    public function setNumero($numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return int
     */
    public function getAcao()
    {
        return $this->acao;
    }

    /**
     * @param int $acao
     */
    public function setAcao($acao): void
    {
        $this->acao = $acao;
    }

    /**
     * @return \DateTime
     */
    public function getDataHistorico()
    {
        return $this->dataHistorico;
    }

    /**
     * @param \DateTime $dataHistorico
     */
    public function setDataHistorico($dataHistorico): void
    {
        $this->dataHistorico = $dataHistorico;
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
    public function getResponsavel()
    {
        return $this->responsavel;
    }

    /**
     * @param int $responsavel
     */
    public function setResponsavel($responsavel): void
    {
        $this->responsavel = $responsavel;
    }

    /**
     * @return AtividadeSecundariaTO
     */
    public function getAtividadeSecundaria()
    {
        return $this->atividadeSecundaria;
    }

    /**
     * @param AtividadeSecundariaTO $atividadeSecundaria
     */
    public function setAtividadeSecundaria($atividadeSecundaria): void
    {
        $this->atividadeSecundaria = $atividadeSecundaria;
    }

    /**
     * @return int|null
     */
    public function getAnoEleicao(): ?int
    {
        return $this->anoEleicao;
    }

    /**
     * @param int|null $anoEleicao
     */
    public function setAnoEleicao(?int $anoEleicao): void
    {
        $this->anoEleicao = $anoEleicao;
    }

    /**
     * @return int|null
     */
    public function getSequenciaAnoEleicao(): ?int
    {
        return $this->sequenciaAnoEleicao;
    }

    /**
     * @param int|null $sequenciaAnoEleicao
     */
    public function setSequenciaAnoEleicao(?int $sequenciaAnoEleicao): void
    {
        $this->sequenciaAnoEleicao = $sequenciaAnoEleicao;
    }

    /**
     * @return string
     */
    public function getDescricaoEleicao()
    {
        return $this->descricaoEleicao;
    }

    /**
     * @param string $descricaoEleicao
     */
    public function setDescricaoEleicao($descricaoEleicao): void
    {
        $this->descricaoEleicao = $descricaoEleicao;
    }

    /**
     * Retorna o sequencial da eleição formatado.
     *
     * @param $sequencial
     * @return string|null
     */
    public function getSequenciaFormatadaEleicao()
    {
        $sequenciaFormatada = null;

        if (!empty($this->sequenciaAnoEleicao) and !empty($this->anoEleicao)) {
            $sequenciaFormatada =
                $this->anoEleicao . '/' . str_pad($this->sequenciaAnoEleicao, 3, "0", STR_PAD_LEFT);
        }

        return $sequenciaFormatada;
    }
}
