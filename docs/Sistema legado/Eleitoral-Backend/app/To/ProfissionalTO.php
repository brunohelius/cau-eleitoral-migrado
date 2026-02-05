<?php


namespace App\To;

use App\Entities\Profissional;
use App\Entities\RecursoImpugnacao;
use App\Util\Utils;

/**
 * Classe de transferência para o Profissional
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ProfissionalTO
{
    /**
     * @var int $id
     */
    public $id;

    /**
     * @var string $nome
     */
    public $nome;

    /**
     * @var string $cpf
     */
    public $cpf;

    /**
     * @var string $numeroRegistro
     */
    public $numeroRegistro;

    /**
     * @var string $dataFimRegistro
     */
    public $dataFimRegistro;

    /**
     * @var \stdClass|string|null $situacaoRegistro
     */
    public $situacaoRegistro;

    /**
     * @var string $uf
     */
    public $uf;

    /**
     * @var string $registroNacional
     */
    public $registroNacional;

    /**
     * @var string $email
     */
    public $email;

    /**
     * @var integer $idCauUf
     */
    public $idCauUf;

    /**
     * @var boolean
     */
    public $conselheiro;

    /**
     * @var \stdClass|null
     */
    public $conselheiroSubsequente;

    /**
     * @var \stdClass|null
     */
    public $perdaMandatoConselheiro;

    /**
     * @var boolean
     */
    public $adimplente;

    /**
     * @var boolean
     */
    public $registroProvisorio;

    /**
     * @var integer
     */
    public $tempoRegistroAtivo;

    /**
     * @var boolean
     */
    public $infracaoEtica;

    /**
     * @var \stdClass|null
     */
    public $sancionadoInfracaoEticaDisciplinar;

    /**
     * @var boolean
     */
    public $multaEtica;

    /**
     * @var boolean
     */
    public $infracaoRelacionadaExercicioProfissao;

    /**
     * @var boolean
     */
    public $multaProcessoEleitoral;

    /**
     * @var integer|null $pessoaId
     */
    public $pessoaId;

    /**
     * @var string $sexo
     */
    public $sexo;

    /**
     * Retorna uma nova instância de 'ProfissionalTO'.
     *
     * @param null $data
     * @return ProfissionalTO
     */
    public static function newInstance($data = null)
    {
        $profissionalTO = new ProfissionalTO();

        if ($data != null) {
            $profissionalTO->setId(Utils::getValue('id', $data));
            $profissionalTO->setUf(Utils::getValue('uf', $data));
            $profissionalTO->setNome(Utils::getValue('nome', $data));
            $profissionalTO->setDataFimRegistro(Utils::getValue('dataFimRegistro', $data));
            $profissionalTO->setSituacaoRegistro(Utils::getValue('situacao', $data));
            $profissionalTO->setRegistroNacional(Utils::getValue('registroNacional', $data));
            $profissionalTO->setNumeroRegistro(Utils::getValue('numeroRegistro', $data));

            if(empty($profissionalTO->getNumeroRegistro())) {
                $profissionalTO->setNumeroRegistro(Utils::getValue('registro', $data));
            }

            if(empty($profissionalTO->getDataFimRegistro())) {
                $profissionalTO->setDataFimRegistro(Utils::getValue('datafimregistro', $data));
            }

            $cpf = Utils::getValue('cpf', $data);
            $cpf = Utils::getOnlyNumbers($cpf);
            $profissionalTO->setCpf($cpf);

            $pessoa = Utils::getValue('pessoa', $data);
            if (!empty($pessoa)) {
                $endereco = Utils::getValue('endereco', $pessoa, []);
                $profissionalTO->setUf(Utils::getValue("uf", $endereco));
                $profissionalTO->setPessoaId(Utils::getValue('id', $pessoa));
                $profissionalTO->setEmail(Utils::getValue('email', $pessoa));
            }

        }

        return $profissionalTO;
    }

    /**
     * Retorna uma nova instância de 'ProfissionalTO'.
     *
     * @param Profissional $profissional
     * @param bool $isResumo
     * @return ProfissionalTO
     */
    public static function newInstanceFromEntity($profissional, $isResumo = false)
    {
        $profissionalTO = new ProfissionalTO();

        if(!empty($profissional)) {
            $profissionalTO->setId($profissional->getId());
            $profissionalTO->setNome($profissional->getNome());
            $profissionalTO->setRegistroNacional($profissional->getRegistroNacional());

            if (!$isResumo) {
                $profissionalTO->setDataFimRegistro($profissional->getDataFimRegistro());
                $profissionalTO->setSituacaoRegistro($profissional->getSituacaoRegistro());
                $profissionalTO->setNumeroRegistro($profissional->getRegistroNacional());

                $cpf = $profissional->getCpf();
                $cpf = Utils::getOnlyNumbers($cpf);
                $profissionalTO->setCpf($cpf);
            }

            if (!empty($profissional->getPessoa())) {
                $profissionalTO->setEmail($profissional->getPessoa()->getEmail());

                if (!$isResumo) {
                    $profissionalTO->setPessoaId($profissional->getPessoa()->getId());
                    $endereco = $profissional->getPessoa()->getEndereco();
                    if(!empty($endereco)) {
                        $profissionalTO->setUf($endereco->getUf());
                    }
                }
            }
        }
        return  $profissionalTO;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome(?string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return string
     */
    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    /**
     * @param string $cpf
     */
    public function setCpf(?string $cpf): void
    {
        $this->cpf = $cpf;
    }

    /**
     * @return string
     */
    public function getNumeroRegistro(): ?string
    {
        return $this->numeroRegistro;
    }

    /**
     * @param string $numeroRegistro
     */
    public function setNumeroRegistro(?string $numeroRegistro): void
    {
        $this->numeroRegistro = $numeroRegistro;
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
     * @return \stdClass|string|null
     */
    public function getSituacaoRegistro()
    {
        return $this->situacaoRegistro;
    }

    /**
     * @param \stdClass|string|null $situacaoRegistro
     */
    public function setSituacaoRegistro( $situacaoRegistro): void
    {
        $this->situacaoRegistro = $situacaoRegistro;
    }

    /**
     * @return string
     */
    public function getUf(): ?string
    {
        return $this->uf;
    }

    /**
     * @param string $uf
     */
    public function setUf(?string $uf): void
    {
        $this->uf = $uf;
    }

    /**
     * @return string
     */
    public function getRegistroNacional(): ?string
    {
        return $this->registroNacional;
    }

    /**
     * @param string $registroNacional
     */
    public function setRegistroNacional(?string $registroNacional): void
    {
        $this->registroNacional = $registroNacional;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getIdCauUf(): ?int
    {
        return $this->idCauUf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf(?int $idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return bool
     */
    public function isConselheiro(): ?bool
    {
        return $this->conselheiro;
    }

    /**
     * @param bool $conselheiro
     */
    public function setConselheiro(?bool $conselheiro): void
    {
        $this->conselheiro = $conselheiro;
    }

    /**
     * @return \stdClass|null
     */
    public function getConselheiroSubsequente(): ?\stdClass
    {
        return $this->conselheiroSubsequente;
    }

    /**
     * @param \stdClass|null $conselheiroSubsequente
     */
    public function setConselheiroSubsequente(?\stdClass $conselheiroSubsequente): void
    {
        $this->conselheiroSubsequente = $conselheiroSubsequente;
    }

    /**
     * @return \stdClass|null
     */
    public function getPerdaMandatoConselheiro(): ?\stdClass
    {
        return $this->perdaMandatoConselheiro;
    }

    /**
     * @param \stdClass|null $perdaMandatoConselheiro
     */
    public function setPerdaMandatoConselheiro(?\stdClass $perdaMandatoConselheiro): void
    {
        $this->perdaMandatoConselheiro = $perdaMandatoConselheiro;
    }

    /**
     * @return bool
     */
    public function isAdimplente(): ?bool
    {
        return $this->adimplente;
    }

    /**
     * @param bool $adimplente
     */
    public function setAdimplente(?bool $adimplente): void
    {
        $this->adimplente = $adimplente;
    }

    /**
     * @return bool
     */
    public function isRegistroProvisorio(): ?bool
    {
        return $this->registroProvisorio;
    }

    /**
     * @param bool $registroProvisorio
     */
    public function setRegistroProvisorio(?bool $registroProvisorio): void
    {
        $this->registroProvisorio = $registroProvisorio;
    }

    /**
     * @return int
     */
    public function getTempoRegistroAtivo(): ?int
    {
        return $this->tempoRegistroAtivo;
    }

    /**
     * @param int $tempoRegistroAtivo
     */
    public function setTempoRegistroAtivo(?int $tempoRegistroAtivo): void
    {
        $this->tempoRegistroAtivo = $tempoRegistroAtivo;
    }

    /**
     * @return bool
     */
    public function isInfracaoEtica(): ?bool
    {
        return $this->infracaoEtica;
    }

    /**
     * @param bool $infracaoEtica
     */
    public function setInfracaoEtica(?bool $infracaoEtica): void
    {
        $this->infracaoEtica = $infracaoEtica;
    }

    /**
     * @return \stdClass|null
     */
    public function getSancionadoInfracaoEticaDisciplinar(): ?\stdClass
    {
        return $this->sancionadoInfracaoEticaDisciplinar;
    }

    /**
     * @param \stdClass|null $sancionadoInfracaoEticaDisciplinar
     */
    public function setSancionadoInfracaoEticaDisciplinar(?\stdClass $sancionadoInfracaoEticaDisciplinar): void
    {
        $this->sancionadoInfracaoEticaDisciplinar = $sancionadoInfracaoEticaDisciplinar;
    }

    /**
     * @return bool
     */
    public function isMultaEtica(): ?bool
    {
        return $this->multaEtica;
    }

    /**
     * @param bool $multaEtica
     */
    public function setMultaEtica(?bool $multaEtica): void
    {
        $this->multaEtica = $multaEtica;
    }

    /**
     * @return bool
     */
    public function isInfracaoRelacionadaExercicioProfissao(): ?bool
    {
        return $this->infracaoRelacionadaExercicioProfissao;
    }

    /**
     * @param bool $infracaoRelacionadaExercicioProfissao
     */
    public function setInfracaoRelacionadaExercicioProfissao(?bool $infracaoRelacionadaExercicioProfissao): void
    {
        $this->infracaoRelacionadaExercicioProfissao = $infracaoRelacionadaExercicioProfissao;
    }

    /**
     * @return bool
     */
    public function isMultaProcessoEleitoral(): ?bool
    {
        return $this->multaProcessoEleitoral;
    }

    /**
     * @param bool $multaProcessoEleitoral
     */
    public function setMultaProcessoEleitoral(?bool $multaProcessoEleitoral): void
    {
        $this->multaProcessoEleitoral = $multaProcessoEleitoral;
    }

    /**
     * @return int|null
     */
    public function getPessoaId(): ?int
    {
        return $this->pessoaId;
    }

    /**
     * @param int|null $pessoaId
     */
    public function setPessoaId(?int $pessoaId): void
    {
        $this->pessoaId = $pessoaId;
    }

    /**
     * @return string
     */
    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    /**
     * @param string $sexo
     */
    public function setSexo(?string $sexo): void
    {
        $this->sexo = $sexo;
    }

}
