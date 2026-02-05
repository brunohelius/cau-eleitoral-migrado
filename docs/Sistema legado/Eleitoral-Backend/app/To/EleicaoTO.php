<?php
/*
 * EleicaoTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Entities\Eleicao;
use App\Util\Utils;
use Carbon\Traits\Date;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as JMS;

/**
 * Classe de transferência associada ao 'Calendario'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="Eleicao")
 */
class EleicaoTO
{
    /**
     * ID da Eleição
     * @var integer
     * @OA\Property()
     */
    private $id;

    /**
     * Ano do Eleição
     * @var integer
     * @OA\Property()
     */
    private $ano;

    /**
     * Descrição da Eleição
     * @var string
     * @OA\Property()
     */
    private $descricao;

    /**
     * Tipo de Proceso da Eleição
     * @var integer
     * @OA\Property()
     */
    private $idTipoProcesso;

    /**
     * Descrição do Tipo de Proceso da Eleição
     * @var string
     * @OA\Property()
     */
    private $descricaoTipoProcesso;

    /**
     * Status Atual da Eleição
     * @var integer
     * @OA\Property()
     */
    private $idSituacao;

    /**
     * Descrição do Status Atual da Eleição
     * @var string
     * @OA\Property()
     */
    private $descricaoSituacao;

    /**
     * Situação de Ativação
     * @var boolean
     * @OA\Property()
     */
    private $ativo;

    /**
     * Sequência da eleição
     * @var integer
     * @OA\Property()
     */
    private $sequenciaAno;

    /**
     * Quantidades de convites do profissional autenticado de particiapacao na chapa eleicao a confirmar
     * @var integer
     * @OA\Property()
     */
    private $totalConvitesProfissionalAConfirmar;

    /**
     * Quantidades de convites do profissional autenticado de particiapacao na chapa eleicao confirmado
     * @var integer
     * @OA\Property()
     */
    private $totalConvitesProfissionalConfirmado;

    /**
     * @var TipoProcessoTO
     */
    private $tipoProcesso;

    /**
     * @var CalendarioTO
     */
    private $calendario;

    /**
     * Fabricação estática de 'EleicaoTO'.
     *
     * @param array|null $data
     *
     * @return EleicaoTO
     */
    public static function newInstance($data = null)
    {
        $eleicaoTO = new EleicaoTO();

        if ($data != null) {

            $eleicaoTO->setSequenciaAno(Utils::getValue("sequenciaAno", $data));

            $eleicaoTO->setId(Utils::getValue("id", $data));
            $eleicaoTO->setAno(Utils::getValue("ano", $data));
            $eleicaoTO->setAtivo(Utils::getValue("ativo", $data));
            $eleicaoTO->setIdSituacao(Utils::getValue("idSituacao", $data));
            $eleicaoTO->setIdTipoProcesso(Utils::getValue("idTipoProcesso", $data));
            $eleicaoTO->setDescricaoSituacao(Utils::getValue("descricaoSituacao", $data));
            $eleicaoTO->setDescricaoTipoProcesso(Utils::getValue("descricaoTipoProcesso", $data));

            $eleicaoTO->setDescricao($eleicaoTO->getSequenciaFormatada());

            if (!empty($data['tipoProcesso'])) {
                $tipoProcessoTO = TipoProcessoTO::newInstance($data['tipoProcesso']);
                $eleicaoTO->setTipoProcesso($tipoProcessoTO);
            }

            if (!empty($data['calendario'])) {
                $calendarioTO = CalendarioTO::newInstance($data['calendario']);
                $eleicaoTO->setCalendario($calendarioTO);
            }
        }

        return $eleicaoTO;
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
    public function setId($id)
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
    public function setAno($ano)
    {
        $this->ano = $ano;
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
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return int
     */
    public function getIdTipoProcesso()
    {
        return $this->idTipoProcesso;
    }

    /**
     * @param int $idTipoProcesso
     */
    public function setIdTipoProcesso($idTipoProcesso)
    {
        $this->idTipoProcesso = $idTipoProcesso;
    }

    /**
     * @return string
     */
    public function getDescricaoTipoProcesso()
    {
        return $this->descricaoTipoProcesso;
    }

    /**
     * @param string $descricaoTipoProcesso
     */
    public function setDescricaoTipoProcesso($descricaoTipoProcesso)
    {
        $this->descricaoTipoProcesso = $descricaoTipoProcesso;
    }

    /**
     * @return int
     */
    public function getIdSituacao()
    {
        return $this->idSituacao;
    }

    /**
     * @param int $idSituacao
     */
    public function setIdSituacao($idSituacao)
    {
        $this->idSituacao = $idSituacao;
    }

    /**
     * @return string
     */
    public function getDescricaoSituacao()
    {
        return $this->descricaoSituacao;
    }

    /**
     * @param string $descricaoSituacao
     */
    public function setDescricaoSituacao($descricaoSituacao)
    {
        $this->descricaoSituacao = $descricaoSituacao;
    }

    /**
     * @return bool
     */
    public function isAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param bool $ativo
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
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
     * @return TipoProcessoTO
     */
    public function getTipoProcesso()
    {
        return $this->tipoProcesso;
    }

    /**
     * @param TipoProcessoTO $tipoProcesso
     */
    public function setTipoProcesso($tipoProcesso)
    {
        $this->tipoProcesso = $tipoProcesso;
    }

    /**
     * @return CalendarioTO
     */
    public function getCalendario()
    {
        return $this->calendario;
    }

    /**
     * @param CalendarioTO $calendario
     */
    public function setCalendario(CalendarioTO $calendario): void
    {
        $this->calendario = $calendario;
    }

    /**
     * @return int
     */
    public function getTotalConvitesProfissionalAConfirmar()
    {
        return $this->totalConvitesProfissionalAConfirmar;
    }

    /**
     * @param int $totalConvitesProfissionalAConfirmar
     */
    public function setTotalConvitesProfissionalAConfirmar(int $totalConvitesProfissionalAConfirmar): void
    {
        $this->totalConvitesProfissionalAConfirmar = $totalConvitesProfissionalAConfirmar;
    }

    /**
     * @return int
     */
    public function getTotalConvitesProfissionalConfirmado()
    {
        return $this->totalConvitesProfissionalConfirmado;
    }

    /**
     * @param int $totalConvitesProfissionalConfirmado
     */
    public function setTotalConvitesProfissionalConfirmado(int $totalConvitesProfissionalConfirmado): void
    {
        $this->totalConvitesProfissionalConfirmado = $totalConvitesProfissionalConfirmado;
    }
}
