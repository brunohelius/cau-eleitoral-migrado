<?php
/*
 * DefesaImpugnacaoTO.php Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Config\Constants;
use App\Entities\ChapaEleicao;
use App\Util\Utils;
use Illuminate\Support\Arr;

/**
 * Classe de transferência associada a 'ChapaEleicao'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ChapaEleicaoTO
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $idCauUf;

    /**
     * @var integer|null
     */
    private $numeroChapa;

    /**
     * @var integer
     */
    private $idProfissionalInclusao;

    /**
     * @var integer
     */
    private $idEtapa;

    /**
     * @var string
     */
    private $descricaoPlataforma;

    /**
     * @var string|null
     */
    private $uf;

    /**
     * @var AtividadeSecundariaCalendarioTO|null
     */
    private $atividadeSecundariaCalendario;

    /**
     * @var TipoCandidaturaTO|null
     */
    private $tipoCandidatura;

    /**
     * @var ProfissionalTO[]|null $responsaveis
     */
    private $responsaveis;

    /**
     * @var StatusGenericoTO|null
     */
    private $statusChapaVigente;

    /**
     * @var StatusGenericoTO|null
     */
    private $statusChapaJulgamentoFinal;

    /**
     * @var int|null
     */
    private $quantidadeMembrosComPendencia;

    /**
     * @var MembroChapaTO[]|null
     */
    private $membrosChapa;

    /**
     * @var integer|null
     */
    private $numeroProporcaoConselheiros;

    /**
     * @var boolean|null
     */
    private $isCadastradoJulgamentoFinal;

    /**
     * @var boolean|null
     */
    private $isJulgamentoFinalIndeferido;

    /**
     * @var boolean|null
     */
    private $isCadastradoRecursoJulgamentoFinal;

    /**
     * @var boolean|null
     */
    private $isCadastradoSubstituicaoJulgamentoFinal;

    /**
     * @var boolean|null
     */
    private $isIniciadoAtivRecursoJulgamentoFinal;

    /**
     * @var boolean|null
     */
    private $isFinalizadoAtivRecursoJulgamentoFinal;

    /**
     * @var boolean|null
     */
    private $isIniciadoAtivSubstituicaoJulgFinal;

    /**
     * @var boolean|null
     */
    private $isFinalizadoAtivSubstituicaoJulgFinal;

    /**
     * @var boolean|null
     */
    private $isIniciadoAtivJulgFinal;

    /**
     * @var boolean|null
     */
    private $isFinalizadoAtivJulgFinal;

    /**
     * @var boolean|null
     */
    private $isIniciadoAtivJulgSegundaInstancia;

    /**
     * @var boolean|null
     */
    private $isFinalizadoAtivJulgSegundaInstancia;

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
     * @return int
     */
    public function getIdCauUf(): ?int
    {
        return $this->idCauUf;
    }

    /**
     * @param int|null $idCauUf
     */
    public function setIdCauUf(?int $idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return int
     */
    public function getIdProfissionalInclusao(): ?int
    {
        return $this->idProfissionalInclusao;
    }

    /**
     * @param int $idProfissionalInclusao
     */
    public function setIdProfissionalInclusao(?int $idProfissionalInclusao): void
    {
        $this->idProfissionalInclusao = $idProfissionalInclusao;
    }

    /**
     * @return int|null
     */
    public function getNumeroChapa(): ?int
    {
        return $this->numeroChapa;
    }

    /**
     * @param int|null $numeroChapa
     */
    public function setNumeroChapa(?int $numeroChapa = null): void
    {
        $this->numeroChapa = $numeroChapa;
    }

    /**
     * @return int
     */
    public function getIdEtapa(): ?int
    {
        return $this->idEtapa;
    }

    /**
     * @param int $idEtapa
     */
    public function setIdEtapa(?int $idEtapa): void
    {
        $this->idEtapa = $idEtapa;
    }

    /**
     * @return string
     */
    public function getDescricaoPlataforma(): ?string
    {
        return $this->descricaoPlataforma;
    }

    /**
     * @param string $descricaoPlataforma
     */
    public function setDescricaoPlataforma(?string $descricaoPlataforma): void
    {
        $this->descricaoPlataforma = $descricaoPlataforma;
    }

    /**
     * @return string|null
     */
    public function getUf(): ?string
    {
        return $this->uf;
    }

    /**
     * @param string|null $uf
     */
    public function setUf(?string $uf): void
    {
        $this->uf = $uf;
    }

    /**
     * @return AtividadeSecundariaCalendarioTO|null
     */
    public function getAtividadeSecundariaCalendario(): ?AtividadeSecundariaCalendarioTO
    {
        return $this->atividadeSecundariaCalendario;
    }

    /**
     * @param AtividadeSecundariaCalendarioTO $atividadeSecundariaCalendario
     */
    public function setAtividadeSecundariaCalendario(?AtividadeSecundariaCalendarioTO $atividadeSecundariaCalendario
    ): void {
        $this->atividadeSecundariaCalendario = $atividadeSecundariaCalendario;
    }

    /**
     * @return TipoCandidaturaTO|null
     */
    public function getTipoCandidatura(): ?TipoCandidaturaTO
    {
        return $this->tipoCandidatura;
    }

    /**
     * @param TipoCandidaturaTO $tipoCandidatura
     */
    public function setTipoCandidatura(?TipoCandidaturaTO $tipoCandidatura): void
    {
        $this->tipoCandidatura = $tipoCandidatura;
    }

    /**
     * @return ProfissionalTO[]|null
     */
    public function getResponsaveis(): ?array
    {
        return $this->responsaveis;
    }

    /**
     * @param ProfissionalTO[]|null $responsaveis
     */
    public function setResponsaveis(?array $responsaveis): void
    {
        $this->responsaveis = $responsaveis;
    }

    /**
     * @return StatusGenericoTO|null
     */
    public function getStatusChapaVigente(): ?StatusGenericoTO
    {
        return $this->statusChapaVigente;
    }

    /**
     * @param StatusGenericoTO|null $statusChapaVigente
     */
    public function setStatusChapaVigente(?StatusGenericoTO $statusChapaVigente): void
    {
        $this->statusChapaVigente = $statusChapaVigente;
    }

    /**
     * @return StatusGenericoTO|null
     */
    public function getStatusChapaJulgamentoFinal(): ?StatusGenericoTO
    {
        return $this->statusChapaJulgamentoFinal;
    }

    /**
     * @param StatusGenericoTO|null $statusChapaJulgamentoFinal
     */
    public function setStatusChapaJulgamentoFinal(?StatusGenericoTO $statusChapaJulgamentoFinal): void
    {
        $this->statusChapaJulgamentoFinal = $statusChapaJulgamentoFinal;
    }

    /**
     * @return int|null
     */
    public function getQuantidadeMembrosComPendencia(): ?int
    {
        return $this->quantidadeMembrosComPendencia;
    }

    /**
     * @param int|null $quantidadeMembrosComPendencia
     */
    public function setQuantidadeMembrosComPendencia(?int $quantidadeMembrosComPendencia): void
    {
        $this->quantidadeMembrosComPendencia = $quantidadeMembrosComPendencia;
    }

    /**
     * @return MembroChapaTO[]|null
     */
    public function getMembrosChapa(): ?array
    {
        return $this->membrosChapa;
    }

    /**
     * @param MembroChapaTO[]|null $membrosChapa
     */
    public function setMembrosChapa(?array $membrosChapa): void
    {
        $this->membrosChapa = $membrosChapa;
    }

    /**
     * @return int|null
     */
    public function getNumeroProporcaoConselheiros(): ?int
    {
        return $this->numeroProporcaoConselheiros;
    }

    /**
     * @param int|null $numeroProporcaoConselheiros
     */
    public function setNumeroProporcaoConselheiros(?int $numeroProporcaoConselheiros): void
    {
        $this->numeroProporcaoConselheiros = $numeroProporcaoConselheiros;
    }

    /**
     * @return bool|null
     */
    public function getIsCadastradoRecursoJulgamentoFinal(): ?bool
    {
        return $this->isCadastradoRecursoJulgamentoFinal;
    }

    /**
     * @param bool|null $isCadastradoRecursoJulgamentoFinal
     */
    public function setIsCadastradoRecursoJulgamentoFinal(?bool $isCadastradoRecursoJulgamentoFinal): void
    {
        $this->isCadastradoRecursoJulgamentoFinal = $isCadastradoRecursoJulgamentoFinal;
    }

    /**
     * @return bool|null
     */
    public function getIsCadastradoSubstituicaoJulgamentoFinal(): ?bool
    {
        return $this->isCadastradoSubstituicaoJulgamentoFinal;
    }

    /**
     * @param bool|null $isCadastradoSubstituicaoJulgamentoFinal
     */
    public function setIsCadastradoSubstituicaoJulgamentoFinal(?bool $isCadastradoSubstituicaoJulgamentoFinal): void
    {
        $this->isCadastradoSubstituicaoJulgamentoFinal = $isCadastradoSubstituicaoJulgamentoFinal;
    }

    /**
     * @return bool|null
     */
    public function getIsIniciadoAtivRecursoJulgamentoFinal(): ?bool
    {
        return $this->isIniciadoAtivRecursoJulgamentoFinal;
    }

    /**
     * @param bool|null $isIniciadoAtivRecursoJulgamentoFinal
     */
    public function setIsIniciadoAtivRecursoJulgamentoFinal(?bool $isIniciadoAtivRecursoJulgamentoFinal): void
    {
        $this->isIniciadoAtivRecursoJulgamentoFinal = $isIniciadoAtivRecursoJulgamentoFinal;
    }

    /**
     * @return bool|null
     */
    public function getIsIniciadoAtivSubstituicaoJulgFinal(): ?bool
    {
        return $this->isIniciadoAtivSubstituicaoJulgFinal;
    }

    /**
     * @param bool|null $isIniciadoAtivSubstituicaoJulgFinal
     */
    public function setIsIniciadoAtivSubstituicaoJulgFinal(?bool $isIniciadoAtivSubstituicaoJulgFinal): void
    {
        $this->isIniciadoAtivSubstituicaoJulgFinal = $isIniciadoAtivSubstituicaoJulgFinal;
    }

    /**
     * @return bool|null
     */
    public function getIsFinalizadoAtivRecursoJulgamentoFinal(): ?bool
    {
        return $this->isFinalizadoAtivRecursoJulgamentoFinal;
    }

    /**
     * @param bool|null $isFinalizadoAtivRecursoJulgamentoFinal
     */
    public function setIsFinalizadoAtivRecursoJulgamentoFinal(?bool $isFinalizadoAtivRecursoJulgamentoFinal): void
    {
        $this->isFinalizadoAtivRecursoJulgamentoFinal = $isFinalizadoAtivRecursoJulgamentoFinal;
    }

    /**
     * @return bool|null
     */
    public function getIsFinalizadoAtivSubstituicaoJulgFinal(): ?bool
    {
        return $this->isFinalizadoAtivSubstituicaoJulgFinal;
    }

    /**
     * @param bool|null $isFinalizadoAtivSubstituicaoJulgFinal
     */
    public function setIsFinalizadoAtivSubstituicaoJulgFinal(?bool $isFinalizadoAtivSubstituicaoJulgFinal): void
    {
        $this->isFinalizadoAtivSubstituicaoJulgFinal = $isFinalizadoAtivSubstituicaoJulgFinal;
    }

    /**
     * @return bool|null
     */
    public function getIsCadastradoJulgamentoFinal(): ?bool
    {
        return $this->isCadastradoJulgamentoFinal;
    }

    /**
     * @param bool|null $isCadastradoJulgamentoFinal
     */
    public function setIsCadastradoJulgamentoFinal(?bool $isCadastradoJulgamentoFinal): void
    {
        $this->isCadastradoJulgamentoFinal = $isCadastradoJulgamentoFinal;
    }

    /**
     * @return bool|null
     */
    public function getIsIniciadoAtivJulgFinal(): ?bool
    {
        return $this->isIniciadoAtivJulgFinal;
    }

    /**
     * @param bool|null $isIniciadoAtivJulgFinal
     */
    public function setIsIniciadoAtivJulgFinal(?bool $isIniciadoAtivJulgFinal): void
    {
        $this->isIniciadoAtivJulgFinal = $isIniciadoAtivJulgFinal;
    }

    /**
     * @return bool|null
     */
    public function getIsFinalizadoAtivJulgFinal(): ?bool
    {
        return $this->isFinalizadoAtivJulgFinal;
    }

    /**
     * @param bool|null $isFinalizadoAtivJulgFinal
     */
    public function setIsFinalizadoAtivJulgFinal(?bool $isFinalizadoAtivJulgFinal): void
    {
        $this->isFinalizadoAtivJulgFinal = $isFinalizadoAtivJulgFinal;
    }

    /**
     * @return bool|null
     */
    public function getIsJulgamentoFinalIndeferido(): ?bool
    {
        return $this->isJulgamentoFinalIndeferido;
    }

    /**
     * @param bool|null $isJulgamentoFinalIndeferido
     */
    public function setIsJulgamentoFinalIndeferido(?bool $isJulgamentoFinalIndeferido): void
    {
        $this->isJulgamentoFinalIndeferido = $isJulgamentoFinalIndeferido;
    }

    /**
     * @return bool|null
     */
    public function getisIniciadoAtivJulgSegundaInstancia(): ?bool
    {
        return $this->isIniciadoAtivJulgSegundaInstancia;
    }

    /**
     * @param bool|null $isIniciadoAtivJulgSegundaInstancia
     */
    public function setIsIniciadoAtivJulgSegundaInstancia(?bool $isIniciadoAtivJulgSegundaInstancia): void
    {
        $this->isIniciadoAtivJulgSegundaInstancia = $isIniciadoAtivJulgSegundaInstancia;
    }

    /**
     * @return bool|null
     */
    public function getisFinalizadoAtivJulgSegundaInstancia()
    {
        return $this->isFinalizadoAtivJulgSegundaInstancia;
    }

    /**
     * @param bool|null $isFinalizadoAtivJulgSegundaInstancia
     */
    public function setIsFinalizadoAtivJulgSegundaInstancia($isFinalizadoAtivJulgSegundaInstancia)
    {
        $this->isFinalizadoAtivJulgSegundaInstancia = $isFinalizadoAtivJulgSegundaInstancia;
    }

    /**
     * Fabricação estática de 'ChapaEleicaoTO'.
     *
     * @param array|null $data
     *
     * @return ChapaEleicaoTO
     */
    public static function newInstance($data = null)
    {
        $chapaEleicaoTO = new ChapaEleicaoTO();

        if ($data != null) {
            $chapaEleicaoTO->setId(Utils::getValue("id", $data));
            $chapaEleicaoTO->setIdCauUf(Utils::getValue("idCauUf", $data));
            $chapaEleicaoTO->setIdEtapa(Utils::getValue("idEtapa", $data));
            $chapaEleicaoTO->setDescricaoPlataforma(Utils::getValue("descricaoPlataforma", $data));
            $chapaEleicaoTO->setIdProfissionalInclusao(Utils::getValue("idProfissionalInclusao", $data));

            $atividadeSecundariaCalendario = Utils::getValue("atividadeSecundariaCalendario", $data);
            if (!empty($atividadeSecundariaCalendario)) {
                $chapaEleicaoTO->setAtividadeSecundariaCalendario(AtividadeSecundariaCalendarioTO::newInstance($atividadeSecundariaCalendario));
            }

            $tipoCandidatura = Utils::getValue("tipoCandidatura", $data);
            if (!empty($tipoCandidatura)) {
                $chapaEleicaoTO->setTipoCandidatura(TipoCandidaturaTO::newInstance($tipoCandidatura));
            }

            $filial = Utils::getValue("filial", $data);
            if (!empty($filial)) {
                $chapaEleicaoTO->setUf(Arr::get($filial, 'prefixo'));
            }

            $numeroChapa = Utils::getValue("numero", $data);
            if (!empty($numeroChapa)) {
                $chapaEleicaoTO->setNumeroChapa($numeroChapa);
            }

            $statusChapaVigente = Arr::get($data, 'statusChapaVigente');
            if (!empty($statusChapaVigente)) {
                $chapaEleicaoTO->setStatusChapaVigente(StatusGenericoTO::newInstance($statusChapaVigente));
            }

            $statusChapaJulgamentoFinal = Arr::get($data, 'statusChapaJulgamentoFinal');
            if (!empty($statusChapaJulgamentoFinal)) {
                $chapaEleicaoTO->setStatusChapaJulgamentoFinal(StatusGenericoTO::newInstance($statusChapaJulgamentoFinal));
            }

            $responsaveis = [];
            $profissionaisResponsaveis = Arr::get($data, 'membrosChapa', []);

            foreach ($profissionaisResponsaveis as $profissionalResponsavel) {
                $situacaoResponsavel = Arr::get($profissionalResponsavel, 'situacaoResponsavel', false);

                if ($situacaoResponsavel) {
                    $profissionalResponsavel = Arr::get($profissionalResponsavel, 'profissional');

                    $responsaveis[] = ProfissionalTO::newInstance(Arr::only($profissionalResponsavel, 'nome'));
                }
            }
            $chapaEleicaoTO->setResponsaveis($responsaveis ?? null);

            $numeroChapa = Utils::getValue("numeroChapa", $data);
            if (!empty($numeroChapa)) {
                $chapaEleicaoTO->setNumeroChapa($numeroChapa);
            }
        }

        return $chapaEleicaoTO;
    }

    /**
     * Fabricação estática de 'ChapaEleicaoTO'.
     *
     * @param ChapaEleicao $chapaEleicao
     * @return ChapaEleicaoTO
     */
    public static function newInstanceFromEntity(ChapaEleicao $chapaEleicao): ChapaEleicaoTO
    {
        $chapaEleicaoTO = new ChapaEleicaoTO();

        if ($chapaEleicao != null) {
            $chapaEleicaoTO->setId($chapaEleicao->getId());
            $chapaEleicaoTO->setIdCauUf($chapaEleicao->getIdCauUf());
            $chapaEleicaoTO->setIdEtapa($chapaEleicao->getIdEtapa());
            $chapaEleicaoTO->setDescricaoPlataforma($chapaEleicao->getDescricaoPlataforma());
            $chapaEleicaoTO->setIdProfissionalInclusao($chapaEleicao->getIdProfissionalInclusao());
            $chapaEleicaoTO->setNumeroChapa($chapaEleicao->getNumeroChapa());

            $atividadeSecundariaCalendario = $chapaEleicao->getAtividadeSecundariaCalendario();
            if (!empty($atividadeSecundariaCalendario)) {
                $chapaEleicaoTO->setAtividadeSecundariaCalendario(AtividadeSecundariaCalendarioTO::newInstanceFromEntity($atividadeSecundariaCalendario));
            }

            $tipoCandidatura = $chapaEleicao->getTipoCandidatura();
            if (!empty($tipoCandidatura)) {
                $chapaEleicaoTO->setTipoCandidatura(TipoCandidaturaTO::newInstanceFromEntity($tipoCandidatura));
            }

            $chapaEleicaoTO->setStatusChapaAndStatusJulgametnoFromEntity($chapaEleicao);
        }

        return $chapaEleicaoTO;
    }

    /**
     * Fabricação estática de 'ChapaEleicaoTO'.
     *
     * @param ChapaEleicao $chapaEleicao
     * @return ChapaEleicaoTO
     */
    public static function newInstanceFromEntityByJulgamentoFinal(ChapaEleicao $chapaEleicao): ChapaEleicaoTO
    {
        $chapaEleicaoTO = new ChapaEleicaoTO();

        if ($chapaEleicao != null) {
            $chapaEleicaoTO->setId($chapaEleicao->getId());
            $chapaEleicaoTO->setNumeroChapa($chapaEleicao->getNumeroChapa());
            $chapaEleicaoTO->setUf($chapaEleicao->getFilial()->getPrefixo());
            $chapaEleicaoTO->setIdCauUf($chapaEleicao->getIdCauUf());

            $tipoCandidatura = $chapaEleicao->getTipoCandidatura();
            if (!empty($tipoCandidatura)) {
                $chapaEleicaoTO->setTipoCandidatura(TipoCandidaturaTO::newInstanceFromEntity($tipoCandidatura));
            }

            $chapaEleicaoTO->setStatusChapaAndStatusJulgametnoFromEntity($chapaEleicao);

            $membrosChapa = $chapaEleicao->getMembrosChapa();
            if (!empty($membrosChapa)) {
                $chapaEleicaoTO->setMembrosChapa(array_map(function ($membroChapa) {
                    return MembroChapaTO::newInstanceFromEntity($membroChapa, false, true);
                }, $membrosChapa));
            }
        }

        return $chapaEleicaoTO;
    }

    /**
     * Fabricação estática de 'ChapaEleicaoTO'.
     *
     * @param ChapaEleicao $chapaEleicao
     * @return ChapaEleicaoTO
     */
    public static function newInstanceFromEntityByListagemJulgamentoFinal(ChapaEleicao $chapaEleicao): ChapaEleicaoTO
    {
        $chapaEleicaoTO = new ChapaEleicaoTO();

        if ($chapaEleicao != null) {
            $chapaEleicaoTO->setId($chapaEleicao->getId());
            $chapaEleicaoTO->setNumeroChapa($chapaEleicao->getNumeroChapa());
            $chapaEleicaoTO->setUf($chapaEleicao->getFilial()->getPrefixo());
            $chapaEleicaoTO->setIdCauUf($chapaEleicao->getIdCauUf());

            $tipoCandidatura = $chapaEleicao->getTipoCandidatura();
            if (!empty($tipoCandidatura)) {
                $chapaEleicaoTO->setTipoCandidatura(TipoCandidaturaTO::newInstanceFromEntity($tipoCandidatura));
            }

            $chapaEleicaoTO->setStatusChapaAndStatusJulgametnoFromEntity($chapaEleicao);

            $responsaveis = [];

            $membrosChapa = $chapaEleicao->getMembrosChapa();
            if (!empty($membrosChapa)) {
                $quantidadeMembrosComPendencia = 0;
                foreach ($membrosChapa as $membroChapa) {
                    $situacaoResponsavel = $membroChapa->isSituacaoResponsavel();
                    if (
                        $membroChapa->getStatusParticipacaoChapa()->getId() != Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
                        || $membroChapa->getStatusValidacaoMembroChapa()->getId() == Constants::STATUS_VALIDACAO_MEMBRO_PENDENTE
                    ) {
                        $quantidadeMembrosComPendencia++;
                    }

                    if ($situacaoResponsavel) {
                        $responsaveis[] = ProfissionalTO::newInstance([
                            'nome' => $membroChapa->getProfissional()->getNome()
                        ]);
                    }
                }
                $chapaEleicaoTO->setQuantidadeMembrosComPendencia($quantidadeMembrosComPendencia);
            }
            $chapaEleicaoTO->setResponsaveis($responsaveis ?? null);
        }

        return $chapaEleicaoTO;
    }

    /**
     * @param ChapaEleicao $chapaEleicao
     */
    public function setStatusChapaAndStatusJulgametnoFromEntity($chapaEleicao)
    {
        $chapaEleicao->definirStatusChapaVigente();
        $statusChapaVigente = $chapaEleicao->getStatusChapaVigente();
        if (!empty($statusChapaVigente)) {
            $this->setStatusChapaVigente(StatusGenericoTO::newInstance([
                'id' => $statusChapaVigente->getId(),
                'descricao' => Constants::$statusChapa[$statusChapaVigente->getId()]
            ]));
        }

        $statusChapaJulgamentoFinal = $chapaEleicao->getStatusChapaJulgamentoFinal();
        if (!empty($statusChapaJulgamentoFinal)) {
            $this->setStatusChapaJulgamentoFinal(StatusGenericoTO::newInstance([
                'id' => $statusChapaJulgamentoFinal->getId(),
                'descricao' => $statusChapaJulgamentoFinal->getDescricao()
            ]));
        }
    }

    public function inicializarFlags()
    {
        $this->setIsIniciadoAtivJulgFinal(false);
        $this->setIsFinalizadoAtivJulgFinal(false);
        $this->setIsJulgamentoFinalIndeferido(true);
        $this->setIsCadastradoJulgamentoFinal(false);
        $this->setIsIniciadoAtivSubstituicaoJulgFinal(false);
        $this->setIsCadastradoRecursoJulgamentoFinal(false);
        $this->setIsIniciadoAtivRecursoJulgamentoFinal(false);
        $this->setIsFinalizadoAtivSubstituicaoJulgFinal(false);
        $this->setIsFinalizadoAtivRecursoJulgamentoFinal(false);
        $this->setIsCadastradoSubstituicaoJulgamentoFinal(false);
    }
}
