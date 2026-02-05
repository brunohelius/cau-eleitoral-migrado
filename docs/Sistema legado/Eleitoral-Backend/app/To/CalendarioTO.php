<?php
/*
 * CalendarioTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Entities\AtividadePrincipalCalendario;
use App\Entities\Eleicao;
use App\Util\Utils;
use Carbon\Traits\Date;
use DateTime;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as JMS;

/**
 * Classe de transferência associada ao 'Calendario'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="Calendario")
 */
class CalendarioTO
{
    /**
     * ID do Calendário
     * @var integer
     * @OA\Property()
     */
    private $id;

    /**
     * Ano do Calendário
     * @var integer
     * @OA\Property()
     */
    private $ano;

    /**
     * Descrição do Calendário da Eleição
     * @var string
     * @OA\Property()
     */
    private $eleicao;

    /**
     * Tipo de Proceso do Calendário
     * @var integer
     * @OA\Property()
     */
    private $idTipoProcesso;

    /**
     * Descrição do Tipo de Proceso do Calendário
     * @var string
     * @OA\Property()
     */
    private $descricaoTipoProcesso;

    /**
     * Resolução do Calendário
     * @var string
     * @OA\Property()
     */
    private $resolucao;

    /**
     * Resolução do Calendário
     * @var integer
     * @OA\Property()
     */
    private $idResolucao;

    /**
     * Status Atual do Eleiçao
     * @var integer
     * @OA\Property()
     */
    private $idSituacaoEleicao;

    /**
     * Descrição do Status Atual da Eleiçao
     * @var string
     * @OA\Property()
     */
    private $descricaoSituacaoEleicao;

    /**
     * Status Atual do Calendário
     * @var integer
     * @OA\Property()
     */
    private $idSituacao;

    /**
     * Descrição do Status Atual do Calendário
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
     * Sequencia do calendário da eleição
     * @var integer
     * @OA\Property()
     */
    private $sequenciaAno;

    /**
     * Data do inicio da vigencia do calendário da eleição
     * @var DateTime
     * @OA\Property()
     */
    private $dataInicioVigencia;

    /**
     * Data do fim da vigencia do calendário da eleição
     * @var DateTime
     * @OA\Property()
     */
    private $dataFimVigencia;

    /**
     * Idade de inicio
     * @var integer
     * @OA\Property()
     */
    private $idadeInicio;

    /**
     * Idade de fim
     * @var integer
     * @OA\Property()
     */
    private $idadeFim;

    /**
     * Data do inicio do mandato
     * @var DateTime
     * @OA\Property()
     */
    private $dataInicioMandato;

    /**
     * Data do fim do mandato
     * @var DateTime
     * @OA\Property()
     */
    private $dataFimMandato;

    /**
     * @var boolean
     */
    private $isInformacaoParametrizada;

    /**
     * @var AtividadePrincipalCalendario
     */
    private $atividadePrincipal;

    /**
     * @var array
     */
    private $atividadesPrincipais;

    /**
     * @var integer
     */
    private $idDocumentoComissaoMembro;

    /**
     * @var array
     */
    private $arquivos;

    /**
     * @var boolean
     */
    private $situacaoIES;

    /**
     * Fabricação estática de 'CalendarioTO'.
     *
     * @param array|null $data
     * @return CalendarioTO
     */
    public static function newInstance($data = null)
    {
        $calendarioTO = new CalendarioTO();

        if ($data != null) {
            $calendarioTO->setId(Utils::getValue("id", $data));
            $calendarioTO->setAtivo(Utils::getValue("ativo", $data));
            $calendarioTO->setIdadeFim(Utils::getValue("idadeFim", $data));
            $calendarioTO->setResolucao(Utils::getValue("resolucao", $data));
            $calendarioTO->setIdSituacao(Utils::getValue("idSituacao", $data));
            $calendarioTO->setIdadeInicio(Utils::getValue("idadeInicio", $data));
            $calendarioTO->setIdResolucao(Utils::getValue("idResolucao", $data));
            $calendarioTO->setSituacaoIES(Utils::getValue("situacaoIES", $data));
            $calendarioTO->setSequenciaAno(Utils::getValue("sequenciaAno", $data));
            $calendarioTO->setDataFimMandato(Utils::getValue("dataFimMandato", $data));
            $calendarioTO->setDataFimVigencia(Utils::getValue("dataFimVigencia", $data));
            $calendarioTO->setDataInicioMandato(Utils::getValue("dataInicioMandato", $data));
            $calendarioTO->setDescricaoSituacao(Utils::getValue("descricaoSituacao", $data));
            $calendarioTO->setDataInicioVigencia(Utils::getValue("dataInicioVigencia", $data));
            $calendarioTO->setIdDocumentoComissaoMembro(Utils::getValue("idDocumentoComissaoMembro", $data));

            $calendarioTO->setIdSituacaoEleicao(Utils::getValue("idSituacaoEleicao", $data));
            $calendarioTO->setDescricaoSituacaoEleicao(Utils::getValue("descricaoSituacaoEleicao", $data));

            $eleicao = !empty($data['eleicao']) ? $data['eleicao'] : null;
            $eleicaoTO = EleicaoTO::newInstance($eleicao);

            if (!empty($data['idEleicao'])) {
                $eleicaoTO->setId(Utils::getValue("idEleicao", $data));
            }

            if (!empty($data['ano'])) {
                $eleicaoTO->setAno(Utils::getValue("ano", $data));
            }

            if (!empty($data['sequenciaAno'])) {
                $eleicaoTO->setSequenciaAno(Utils::getValue("sequenciaAno", $data));
            }

            $eleicaoTO->setDescricao($eleicaoTO->getSequenciaFormatada());
            $calendarioTO->setEleicao($eleicaoTO);

            if (!empty($calendarioTO->getEleicao())) {
                $tipoProcessoTO = TipoProcessoTO::newInstance();
                $tipoProcessoTO->setId(Utils::getValue("idTipoProcesso", $data));
                $tipoProcessoTO->setDescricao(Utils::getValue("descricaoTipoProcesso", $data));
                $calendarioTO->getEleicao()->setTipoProcesso($tipoProcessoTO);
            }

            $atividadesPrincipais = Utils::getValue("atividadesPrincipais", $data);
            if(!empty($atividadesPrincipais)){
                $atividadesPrincipaisTO = array_map(function ($atividadePricipal){
                    return AtividadePrincipalCalendarioTO::newInstance($atividadePricipal);
                }, $atividadesPrincipais);
                $calendarioTO->setAtividadesPrincipais($atividadesPrincipaisTO);
            }

            $arquivos = Utils::getValue("arquivos", $data);
            if(!empty($arquivos)){
                $arquivosTO = array_map(function ($arquivo){
                    return ArquivoCalendarioTO::newInstance($arquivo);
                }, $arquivos);
                $calendarioTO->setArquivos($arquivosTO);
            }
        }

        return $calendarioTO;
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
     * @return string
     */
    public function getEleicao()
    {
        return $this->eleicao;
    }

    /**
     * @param EleicaoTO $eleicao
     */
    public function setEleicao($eleicao)
    {
        $this->eleicao = $eleicao;
    }

    /**
     * @return string
     */
    public function getResolucao()
    {
        return $this->resolucao;
    }

    /**
     * @param string $resolucao
     */
    public function setResolucao($resolucao)
    {
        $this->resolucao = $resolucao;
    }

    /**
     * @return int
     */
    public function getIdResolucao()
    {
        return $this->idResolucao;
    }

    /**
     * @param int $idResolucao
     */
    public function setIdResolucao($idResolucao)
    {
        $this->idResolucao = $idResolucao;
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
     * @return DateTime
     */
    public function getDataInicioVigencia()
    {
        return $this->dataInicioVigencia;
    }

    /**
     * @param DateTime $dataInicioVigencia
     */
    public function setDataInicioVigencia($dataInicioVigencia)
    {
        $this->dataInicioVigencia = $dataInicioVigencia;
    }

    /**
     * @return DateTime
     */
    public function getDataFimVigencia()
    {
        return $this->dataFimVigencia;
    }

    /**
     * @param DateTime $dataFimVigencia
     */
    public function setDataFimVigencia($dataFimVigencia)
    {
        $this->dataFimVigencia = $dataFimVigencia;
    }

    /**
     * @return int
     */
    public function getIdadeInicio()
    {
        return $this->idadeInicio;
    }

    /**
     * @param int $idadeInicio
     */
    public function setIdadeInicio($idadeInicio)
    {
        $this->idadeInicio = $idadeInicio;
    }

    /**
     * @return int
     */
    public function getIdadeFim()
    {
        return $this->idadeFim;
    }

    /**
     * @param int $idadeFim
     */
    public function setIdadeFim($idadeFim)
    {
        $this->idadeFim = $idadeFim;
    }

    /**
     * @return DateTime
     */
    public function getDataInicioMandato()
    {
        return $this->dataInicioMandato;
    }

    /**
     * @param DateTime $dataInicioMandato
     */
    public function setDataInicioMandato($dataInicioMandato)
    {
        $this->dataInicioMandato = $dataInicioMandato;
    }

    /**
     * @return DateTime
     */
    public function getDataFimMandato()
    {
        return $this->dataFimMandato;
    }

    /**
     * @param DateTime $dataFimMandato
     */
    public function setDataFimMandato($dataFimMandato)
    {
        $this->dataFimMandato = $dataFimMandato;
    }

    /**
     * @return bool
     */
    public function isInformacaoParametrizada()
    {
        return $this->isInformacaoParametrizada;
    }

    /**
     * @param bool $isInformacaoParametrizada
     */
    public function setIsInformacaoParametrizada($isInformacaoParametrizada): void
    {
        $this->isInformacaoParametrizada = $isInformacaoParametrizada;
    }

    /**
     * @return AtividadePrincipalCalendario
     */
    public function getAtividadePrincipal()
    {
        return $this->atividadePrincipal;
    }

    /**
     * @param AtividadePrincipalCalendarioTO $atividadePrincipal
     */
    public function setAtividadePrincipal($atividadePrincipal)
    {
        $this->atividadePrincipal = $atividadePrincipal;
    }

    /**
     * @return array
     */
    public function getAtividadesPrincipais()
    {
        return $this->atividadesPrincipais;
    }

    /**
     * @param array $atividadesPrincipais
     */
    public function setAtividadesPrincipais(array $atividadesPrincipais): void
    {
        $this->atividadesPrincipais = $atividadesPrincipais;
    }


    /**
     * @return int
     */
    public function getIdDocumentoComissaoMembro()
    {
        return $this->idDocumentoComissaoMembro;
    }

    /**
     * @param int $idDocumentoComissaoMembro
     */
    public function setIdDocumentoComissaoMembro($idDocumentoComissaoMembro)
    {
        $this->idDocumentoComissaoMembro = $idDocumentoComissaoMembro;
    }

    /**
     * @return array
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param array $arquivos
     */
    public function setArquivos(array $arquivos): void
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return bool
     */
    public function isSituacaoIES()
    {
        return $this->situacaoIES;
    }

    /**
     * @param bool $situacaoIES
     */
    public function setSituacaoIES($situacaoIES)
    {
        $this->situacaoIES = $situacaoIES;
    }

    /**
     * @return int
     */
    public function getIdSituacaoEleicao(): ?int
    {
        return $this->idSituacaoEleicao;
    }

    /**
     * @param int $idSituacaoEleicao
     */
    public function setIdSituacaoEleicao(?int $idSituacaoEleicao): void
    {
        $this->idSituacaoEleicao = $idSituacaoEleicao;
    }

    /**
     * @return string
     */
    public function getDescricaoSituacaoEleicao(): ?string
    {
        return $this->descricaoSituacaoEleicao;
    }

    /**
     * @param string $descricaoSituacaoEleicao
     */
    public function setDescricaoSituacaoEleicao(?string $descricaoSituacaoEleicao): void
    {
        $this->descricaoSituacaoEleicao = $descricaoSituacaoEleicao;
    }
}
