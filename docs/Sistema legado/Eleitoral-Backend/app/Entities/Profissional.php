<?php
/*
 * Profissional.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Profissional'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProfissionalRepository")
 * @ORM\Table(schema="public", name="tb_profissional")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class Profissional extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="nome", type="string", length=250, nullable=false)
     * @var string
     */
    private $nome;

    /**
     * @ORM\Column(name="nome_social", type="string", nullable=true)
     * @var string
     */
    private $nomeSocial;

    /**
     * @ORM\Column(name="registro_nacional", type="string", nullable=true)
     * @var string
     */
    private $registroNacional;

    /**
     * @ORM\Column(name="data_nascimento", type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     * @var DateTime
     */
    private $dataNascimento;

    /**
     * @ORM\Column(name="cpf", type="string", length=11, nullable=false)
     * @var string
     */
    private $cpf;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Pessoa")
     * @ORM\JoinColumn(name="pessoa_id", referencedColumnName="id", nullable=false)
     *
     * @var \App\Entities\Pessoa
     */
    private $pessoa;

    /**
     * @ORM\Column(name="identidade", type="string", nullable=true)
     * @var string
     */
    private $rg;

    /**
     * @ORM\Column(name="identidade_data_expedicao", type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     * @var DateTime
     */
    private $dataExpedicao;

    /**
     * @ORM\Column(name="rne", type="string", nullable=true)
     * @var string
     */
    private $rne;

    /**
     * @ORM\Column(name="rne_data_validade", type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     * @var DateTime
     */
    private $dataValidadeRne;

    /**
     * @ORM\Column(name="sexo", type="string", length=1, nullable=true)
     * @var string
     */
    private $sexo;

    /**
     * @ORM\Column(name="naturalidade", type="string", nullable=true)
     * @var string
     */
    private $naturalidade;

    /**
     * @ORM\Column(name="uf_naturalidade", type="string", nullable=true)
     * @var string
     */
    private $ufNaturalidade;

    /**
     * @ORM\Column(name="nome_pai", type="string", nullable=true)
     * @var string
     */
    private $nomePai;

    /**
     * @ORM\Column(name="nome_mae", type="string", nullable=true)
     * @var string
     */
    private $nomeMae;

    /**
     * @ORM\Column(name="observacao", type="string", nullable=true)
     * @var string
     */
    private $observacao;

    /**
     * @ORM\Column(name="tipo_sanguineo", type="string", length=2, nullable=true)
     * @var string
     */
    private $tipoSanguineo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ProfissionalRegistro", mappedBy="profissional")
     * @var ArrayCollection
     */
    private $profissionalRegistro;

    /**
     * @var boolean
     */
    private $conselheiro;

    /**
     * @var \stdClass|null
     */
    private $conselheiroSubsequente;

    /**
     * @var \stdClass|null
     */
    private $perdaMandatoConselheiro;

    /**
     * @var boolean
     */
    private $adimplente;

    /**
     * @var \stdClass|null
     */
    private $situacaoRegistro;

    /**
     * @var boolean
     */
    private $registroProvisorio;

    /**
     * @var string
     */
    private $dataFimRegistro;

    /**
     * @var integer
     */
    private $tempoRegistroAtivo;

    /**
     * @var boolean
     */
    private $infracaoEtica;

    /**
     * @var \stdClass|null
     */
    private $sancionadoInfracaoEticaDisciplinar;

    /**
     * @var boolean
     */
    private $multaEtica;


    /**
     * @var boolean
     */
    private $infracaoRelacionadaExercicioProfissao;

    /**
     * @var boolean
     */
    private $multaProcessoEleitoral;

    /**
     * Transient
     *
     * @var string
     */
    private $avatar;

    /**
     * Transient
     *
     * @var string
     */
    private $nomeCompleto;

    /**
     * Transient
     *
     * @var bool
     */
    private $possuiFoto;

    /**
     * Transient
     *
     * @var integer|null
     */
    private $idCauUf;

    /**
     * Fábrica de instância de 'Profissional'.
     *
     * @param array $data
     * @return Profissional
     */
    public static function newInstance($data = null)
    {
        $profissional = new Profissional();

        if ($data != null) {
            $profissional->setId(Utils::getValue('id', $data));
            $profissional->setRg(Utils::getValue('rg', $data));
            $profissional->setRne(Utils::getValue('rne', $data));
            $profissional->setNome(Utils::getValue('nome', $data));
            $profissional->setSexo(Utils::getValue('sexo', $data));
            $profissional->setIdCauUf(Utils::getValue('idCauUf', $data));
            $profissional->setNomePai(Utils::getValue('nomePai', $data));
            $profissional->setNomeMae(Utils::getValue('nomeMae', $data));
            $profissional->setNomeSocial(Utils::getValue('nomeSocial', $data));
            $profissional->setObservacao(Utils::getValue('observacao', $data));
            $profissional->setNaturalidade(Utils::getValue('naturalidade', $data));
            $profissional->setTipoSanguineo(Utils::getValue('tipoSanguineo', $data));
            $profissional->setUfNaturalidade(Utils::getValue('ufNaturalidade', $data));
            $profissional->setRegistroNacional(Utils::getValue('registroNacional', $data));
            $profissional->setSexo(Utils::getValue('sexo', $data));

            $cpf = Utils::getValue('cpf', $data);
            $cpf = Utils::getOnlyNumbers($cpf);
            $profissional->setCpf($cpf);

            $dataExpedicao = Utils::getValue('dataExpedicao', $data);
            $profissional->setDataExpedicao($dataExpedicao);

            $dataNascimento = Utils::getValue('dataNascimento', $data);
            $profissional->setDataNascimento($dataNascimento);

            $dataValidadeRne = Utils::getValue('dataValidadeRne', $data);
            if ($dataValidadeRne != null) {
                $profissional->setDataValidadeRne($dataValidadeRne);
            }

            $pessoa = Utils::getValue('pessoa', $data);
            if (!empty($pessoa)) {
                $profissional->setPessoa(Pessoa::newInstance($pessoa));
            }
        }

        return $profissional;
    }

    /**
     * Configura o nome do usuário para apresentar somente o primeiro e o último nome
     * e define o valor do atributo nomeCompleto.
     */
    public function definirNomes()
    {
        $this->setNomeCompleto($this->getNome());
        $this->nome = Utils::getPrimeiraEUltimaPalavra($this->getNome());
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
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return string
     */
    public function getNomeSocial()
    {
        return $this->nomeSocial;
    }

    /**
     * @param string $nomeSocial
     */
    public function setNomeSocial($nomeSocial)
    {
        $this->nomeSocial = $nomeSocial;
    }

    /**
     * @return string
     */
    public function getRegistroNacional()
    {
        return $this->registroNacional;
    }

    /**
     * @param string $registroNacional
     */
    public function setRegistroNacional($registroNacional)
    {
        $this->registroNacional = $registroNacional;
    }

    /**
     * @return \DateTime
     */
    public function getDataNascimento()
    {
        return $this->dataNascimento;
    }

    /**
     * @param \DateTime $dataNascimento
     */
    public function setDataNascimento($dataNascimento)
    {
        $this->dataNascimento = $dataNascimento;
    }

    /**
     * @return string
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * @param string $cpf
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
    }

    /**
     * @return Pessoa
     */
    public function getPessoa()
    {
        return $this->pessoa;
    }

    /**
     * @param Pessoa $pessoa
     */
    public function setPessoa($pessoa)
    {
        $this->pessoa = $pessoa;
    }

    /**
     * @return string
     */
    public function getRg()
    {
        return $this->rg;
    }

    /**
     * @param string $rg
     */
    public function setRg($rg)
    {
        $this->rg = $rg;
    }

    /**
     * @return \DateTime
     */
    public function getDataExpedicao()
    {
        return $this->dataExpedicao;
    }

    /**
     * @param \DateTime $dataExpedicao
     */
    public function setDataExpedicao($dataExpedicao)
    {
        $this->dataExpedicao = $dataExpedicao;
    }

    /**
     * @return string
     */
    public function getRne()
    {
        return $this->rne;
    }

    /**
     * @param string $rne
     */
    public function setRne($rne)
    {
        $this->rne = $rne;
    }

    /**
     * @return \DateTime
     */
    public function getDataValidadeRne()
    {
        return $this->dataValidadeRne;
    }

    /**
     * @param \DateTime $dataValidadeRne
     */
    public function setDataValidadeRne($dataValidadeRne)
    {
        $this->dataValidadeRne = $dataValidadeRne;
    }

    /**
     * @return string
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * @param string $sexo
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }

    /**
     * @return string
     */
    public function getNaturalidade()
    {
        return $this->naturalidade;
    }

    /**
     * @param string $naturalidade
     */
    public function setNaturalidade($naturalidade)
    {
        $this->naturalidade = $naturalidade;
    }

    /**
     * @return string
     */
    public function getUfNaturalidade()
    {
        return $this->ufNaturalidade;
    }

    /**
     * @param string $ufNaturalidade
     */
    public function setUfNaturalidade($ufNaturalidade)
    {
        $this->ufNaturalidade = $ufNaturalidade;
    }

    /**
     * @return string
     */
    public function getNomePai()
    {
        return $this->nomePai;
    }

    /**
     * @param string $nomePai
     */
    public function setNomePai($nomePai)
    {
        $this->nomePai = $nomePai;
    }

    /**
     * @return string
     */
    public function getNomeMae()
    {
        return $this->nomeMae;
    }

    /**
     * @param string $nomeMae
     */
    public function setNomeMae($nomeMae)
    {
        $this->nomeMae = $nomeMae;
    }

    /**
     * @return string
     */
    public function getObservacao()
    {
        return $this->observacao;
    }

    /**
     * @param string $observacao
     */
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;
    }

    /**
     * @return string
     */
    public function getTipoSanguineo()
    {
        return $this->tipoSanguineo;
    }

    /**
     * @param string $tipoSanguineo
     */
    public function setTipoSanguineo($tipoSanguineo)
    {
        $this->tipoSanguineo = $tipoSanguineo;
    }

    /**
     * @return bool
     */
    public function isConselheiro()
    {
        return $this->conselheiro;
    }

    /**
     * @param bool $conselheiro
     */
    public function setConselheiro($conselheiro): void
    {
        $this->conselheiro = $conselheiro;
    }

    /**
     * @return \stdClass|null
     */
    public function getConselheiroSubsequente()
    {
        return $this->conselheiroSubsequente;
    }

    /**
     * @param \stdClass|null $conselheiroSubsequente
     */
    public function setConselheiroSubsequente($conselheiroSubsequente): void
    {
        $this->conselheiroSubsequente = $conselheiroSubsequente;
    }

    /**
     * @return \stdClass|null
     */
    public function getPerdaMandatoConselheiro()
    {
        return $this->perdaMandatoConselheiro;
    }

    /**
     * @param \stdClass|null $perdaMandatoConselheiro
     */
    public function setPerdaMandatoConselheiro($perdaMandatoConselheiro): void
    {
        $this->perdaMandatoConselheiro = $perdaMandatoConselheiro;
    }

    /**
     * @return bool
     */
    public function isAdimplente()
    {
        return $this->adimplente;
    }

    /**
     * @param bool $adimplente
     */
    public function setAdimplente($adimplente): void
    {
        $this->adimplente = $adimplente;
    }

    /**
     * @return \stdClass|null
     */
    public function getSituacaoRegistro()
    {
        return $this->situacaoRegistro;
    }

    /**
     * @param \stdClass|null $situacaoRegistro
     */
    public function setSituacaoRegistro($situacaoRegistro): void
    {
        $this->situacaoRegistro = $situacaoRegistro;
    }

    /**
     * @return boolean|null
     */
    public function isRegistroProvisorio()
    {
        return $this->registroProvisorio;
    }

    /**
     * @param boolean $registroProvisorio
     */
    public function setRegistroProvisorio($registroProvisorio): void
    {
        $this->registroProvisorio = $registroProvisorio;
    }

    /**
     * @return string
     */
    public function getDataFimRegistro()
    {
        return $this->dataFimRegistro;
    }

    /**
     * @param string $dataFimRegistro
     */
    public function setDataFimRegistro($dataFimRegistro): void
    {
        $this->dataFimRegistro = $dataFimRegistro;
    }

    /**
     * @return int
     */
    public function getTempoRegistroAtivo()
    {
        return $this->tempoRegistroAtivo;
    }

    /**
     * @param integer $tempoRegistroAtivo
     */
    public function setTempoRegistroAtivo($tempoRegistroAtivo): void
    {
        $this->tempoRegistroAtivo = $tempoRegistroAtivo;
    }

    /**
     * @return bool
     */
    public function isInfracaoEtica()
    {
        return $this->infracaoEtica;
    }

    /**
     * @param bool $infracaoEtica
     */
    public function setInfracaoEtica($infracaoEtica): void
    {
        $this->infracaoEtica = $infracaoEtica;
    }

    /**
     * @return \stdClass|null
     */
    public function getSancionadoInfracaoEticaDisciplinar()
    {
        return $this->sancionadoInfracaoEticaDisciplinar;
    }

    /**
     * @param \stdClass|null $sancionadoInfracaoEticaDisciplinar
     */
    public function setSancionadoInfracaoEticaDisciplinar($sancionadoInfracaoEticaDisciplinar): void
    {
        $this->sancionadoInfracaoEticaDisciplinar = $sancionadoInfracaoEticaDisciplinar;
    }

    /**
     * @return bool
     */
    public function isMultaEtica()
    {
        return $this->multaEtica;
    }

    /**
     * @param bool $multaEtica
     */
    public function setMultaEtica($multaEtica): void
    {
        $this->multaEtica = $multaEtica;
    }

    /**
     * @return bool
     */
    public function isInfracaoRelacionadaExercicioProfissao()
    {
        return $this->infracaoRelacionadaExercicioProfissao;
    }

    /**
     * @param bool $infracaoRelacionadaExercicioProfissao
     */
    public function setInfracaoRelacionadaExercicioProfissao($infracaoRelacionadaExercicioProfissao): void
    {
        $this->infracaoRelacionadaExercicioProfissao = $infracaoRelacionadaExercicioProfissao;
    }

    /**
     * @return bool
     */
    public function isMultaProcessoEleitoral()
    {
        return $this->multaProcessoEleitoral;
    }

    /**
     * @param bool $multaProcessoEleitoral
     */
    public function setMultaProcessoEleitoral($multaProcessoEleitoral): void
    {
        $this->multaProcessoEleitoral = $multaProcessoEleitoral;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getNomeCompleto()
    {
        return $this->nomeCompleto;
    }

    /**
     * @param string $nomeCompleto
     */
    public function setNomeCompleto($nomeCompleto)
    {
        $this->nomeCompleto = $nomeCompleto;
    }

    /**
     * @return bool
     */
    public function isPossuiFoto()
    {
        return $this->possuiFoto;
    }

    /**
     * @param bool $possuiFoto
     */
    public function setPossuiFoto($possuiFoto): void
    {
        $this->possuiFoto = $possuiFoto;
    }

    /**
     * @return int|null
     */
    public function getIdCauUf()
    {
        return $this->idCauUf;
    }

    /**
     * @param int|null $idCauUf
     */
    public function setIdCauUf($idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }
}
