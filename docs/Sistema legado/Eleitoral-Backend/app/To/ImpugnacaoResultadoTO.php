<?php


namespace App\To;

use App\Config\Constants;
use App\Entities\Calendario;
use App\Entities\ImpugnacaoResultado;
use App\Entities\JulgamentoRecursoImpugResultado;
use App\Entities\Profissional;
use App\Entities\StatusImpugnacaoResultado;
use App\Util\Utils;
use Illuminate\Support\Arr;

/**
 * Classe de transferência para a Impugnacao de Resultado.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ImpugnacaoResultadoTO
{
    /**
     * @var int|null $id
     */
    private $id;

    /**
     * @var string|null $descricao
     */
    private $narracao;

    /**
     * @var string|null
     */
    private $numero;

    /**
     * @var \DateTime|null
     */
    private $dataCadastro;

    /**
     * @var int|null $idCauBR
     */
    private $idCauBR;

    /**
     * @var ArquivoGenericoTO[]|null $arquivos
     */
    private $arquivos;

    /**
     * @var AlegacaoImpugnacaoResultadoTO[]|null $alegacoes
     */
    private $alegacoes;

    /**
     * @var int|null $idProfissional
     */
    private $idProfissional;

    /**
     * @var int|null $idStatus
     */
    private $idStatus;

    /**
     * @var int|null $idCalendario
     */
    private $idCalendario;

    /**
     * @var int|null $tipoValidacao
     */
    private $tipoValidacao;

    /**
     * @var FilialTO|null $cauBR
     */
    private $cauBR;

    /**
     * @var ProfissionalTO|null $profissional
     */
    private $profissional;

    /**
     * @var CalendarioTO|null $calendario
     */
    private $calendario;

    /**
     * @var StatusGenericoTO|null $status
     */
    private $status;

    /**
     * @var string|null $nomeProfissional
     */
    private $nomeProfissional;

    /**
     * @var string|null $nomeProfissional
     */
    private $emailProfissional;

    /**
     * @var string|null $nomeProfissional
     */
    private $registroProfissional;

    /**
     * @var bool
     */
    private $hasAlegacao;

    /**
     * @var bool
     */
    private $hasJulgamento;

    /**
     * @var bool
     */
    private $hasRecursoJulgamentoImpugnante;
    /**
     * @var bool
     */
    private $hasRecursoJulgamentoImpugnado;

    /**
     * @var bool
     */
    private $isIniciadoAtividadeCadastro;

    /**
     * @var bool
     */
    private $isFinalizadoAtividadeCadastro;

    /**
     * @var bool
     */
    private $isIniciadoAtividadeAlegacao;

    /**
     * @var bool
     */
    private $isFinalizadoAtividadeAlegacao;

    /**
     * @var JulgamentoAlegacaoImpugResultadoTO|null julgamentoAlegacao
     */
    private $julgamentoAlegacao;

    /**
     * @var bool
     */
    private $isIniciadoAtividadeJulgamento;

    /**
     * @var bool
     */
    private $isFinalizadoAtividadeJulgamento;

    /**
     * @var bool
     */
    private $isIniciadoAtividadeRecursoJulgamento;

    /**
     * @var bool
     */
    private $isFinalizadoAtividadeRecursoJulgamento;

    /**
     * @var bool
     */
    private $isIniciadoAtividadeContrarrazao;

    /**
     * @var bool
     */
    private $isFinalizadoAtividadeContrarrazao;

    /**
     * @var bool
     */
    private $isIniciadoAtividadeJulgamentoRecurso;

    /**
     * @var bool
     */
    private $isFinalizadoAtividadeJulgamentoRecurso;

    /**
     * @var bool
     */
    private $hasRecursoJulgamento;

    /**
     * @var JulgamentoRecursoImpugResultado
     */
    private $julgamentoRecurso;

    /**
     * @var ?bool
     */
    private $hasJulgamentoRecurso;

    /**
     * @var int|null
     */
    private $idStatusJulgamentoAlegacao;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getNarracao(): ?string
    {
        return $this->narracao;
    }

    /**
     * @param string|null $narracao
     */
    public function setNarracao(?string $narracao): void
    {
        $this->narracao = $narracao;
    }

    /**
     * @return \DateTime|null
     */
    public function getDataCadastro(): ?\DateTime
    {
        return $this->dataCadastro;
    }

    /**
     * @param \DateTime|null $dataCadastro
     */
    public function setDataCadastro(?\DateTime $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return ArquivoGenericoTO[]|null
     */
    public function getArquivos(): ?array
    {
        return $this->arquivos;
    }

    /**
     * @param ArquivoGenericoTO[]|null $arquivos
     */
    public function setArquivos(?array $arquivos): void
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return int|null
     */
    public function getIdCauBR()
    {
        return $this->idCauBR;
    }

    /**
     * @param int|null $idCauBR
     */
    public function setIdCauBR( $idCauBR): void
    {
        $this->idCauBR = $idCauBR;
    }

    /**
     * @return int|null
     */
    public function getIdProfissional()
    {
        return $this->idProfissional;
    }

    /**
     * @param int|null $idProfissional
     */
    public function setIdProfissional($idProfissional): void
    {
        $this->idProfissional = $idProfissional;
    }

    /**
     * @return int|null
     */
    public function getIdStatus()
    {
        return $this->idStatus;
    }

    /**
     * @param int|null $idStatus
     */
    public function setIdStatus($idStatus): void
    {
        $this->idStatus = $idStatus;
    }

    /**
     * @return int|null
     */
    public function getIdCalendario()
    {
        return $this->idCalendario;
    }

    /**
     * @param int|null $idCalendario
     */
    public function setIdCalendario($idCalendario): void
    {
        $this->idCalendario = $idCalendario;
    }

    /**
     * @return int|null
     */
    public function getTipoValidacao()
    {
        return $this->tipoValidacao;
    }

    /**
     * @param int|null $tipoValidacao
     */
    public function setTipoValidacao($tipoValidacao): void
    {
        $this->tipoValidacao = $tipoValidacao;
    }

    /**
     * @return string|null
     */
    public function getNomeProfissional(): ?string
    {
        return $this->nomeProfissional;
    }

    /**
     * @param string|null $nomeProfissional
     */
    public function setNomeProfissional(?string $nomeProfissional): void
    {
        $this->nomeProfissional = $nomeProfissional;
    }

    /**
     * @return FilialTO|null
     */
    public function getCauBR()
    {
        return $this->cauBR;
    }

    /**
     * @param FilialTO|null $cauBR
     */
    public function setCauBR($cauBR): void
    {
        $this->cauBR = $cauBR;
    }

    /**
     * @return ProfissionalTO|null
     */
    public function getProfissional(): ?ProfissionalTO
    {
        return $this->profissional;
    }

    /**
     * @param ProfissionalTO|null $profissional
     */
    public function setProfissional(?ProfissionalTO $profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return CalendarioTO|null
     */
    public function getCalendario(): ?CalendarioTO
    {
        return $this->calendario;
    }

    /**
     * @param CalendarioTO|null $calendario
     */
    public function setCalendario(?CalendarioTO $calendario): void
    {
        $this->calendario = $calendario;
    }

    /**
     * @return StatusGenericoTO|null
     */
    public function getStatus(): ?StatusGenericoTO
    {
        return $this->status;
    }

    /**
     * @param StatusGenericoTO|null $status
     */
    public function setStatus(?StatusGenericoTO $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getNumero(): ?string
    {
        return $this->numero;
    }

    /**
     * @param string|null $numero
     */
    public function setNumero(?string $numero): void
    {
        if (!empty($numero)) {
            $this->numero = str_pad($numero, 2, '0', STR_PAD_LEFT);
        }
    }

    /**
     * @return string|null
     */
    public function getEmailProfissional(): ?string
    {
        return $this->emailProfissional;
    }

    /**
     * @param string|null $emailProfissional
     */
    public function setEmailProfissional(?string $emailProfissional): void
    {
        $this->emailProfissional = $emailProfissional;
    }

    /**
     * @return string|null
     */
    public function getRegistroProfissional(): ?string
    {
        return $this->registroProfissional;
    }

    /**
     * @param string|null $registroProfissional
     */
    public function setRegistroProfissional(?string $registroProfissional): void
    {
        $this->registroProfissional = $registroProfissional;
    }

    /**
     * @return bool
     */
    public function isHasAlegacao()
    {
        return $this->hasAlegacao;
    }

    /**
     * @param bool $hasAlegacao
     */
    public function setHasAlegacao($hasAlegacao): void
    {
        $this->hasAlegacao = $hasAlegacao;
    }
    /**
     * @param bool $hasJulgamento
     */
    public function setHasJulgamento($hasJulgamento): void
    {
        $this->hasJulgamento = $hasJulgamento;
    }

    /**
     * @param $hasRecursoJulgamentoImpugnante
     */
    public function setHasRecursoJulgamentoImpugnante($hasRecursoJulgamentoImpugnante): void
    {
        $this->hasRecursoJulgamentoImpugnante = $hasRecursoJulgamentoImpugnante;
    }

    public function setHasRecursoJulgamentoImpugnado($hasRecursoJulgamentoImpugnado): void
    {
        $this->hasRecursoJulgamentoImpugnado = $hasRecursoJulgamentoImpugnado;
    }

    /**
     * @return bool
     */
    public function isHasJulgamento(): ? bool
    {
        return $this->hasJulgamento;
    }

    /**
     * @return bool
     */
    public function isIniciadoAtividadeCadastro(): bool
    {
        return $this->isIniciadoAtividadeCadastro;
    }

    /**
     * @param bool $isIniciadoAtividadeCadastro
     */
    public function setIsIniciadoAtividadeCadastro(bool $isIniciadoAtividadeCadastro): void
    {
        $this->isIniciadoAtividadeCadastro = $isIniciadoAtividadeCadastro;
    }

    /**
     * @return bool
     */
    public function isFinalizadoAtividadeCadastro(): bool
    {
        return $this->isFinalizadoAtividadeCadastro;
    }

    /**
     * @param bool $isFinalizadoAtividadeCadastro
     */
    public function setIsFinalizadoAtividadeCadastro(bool $isFinalizadoAtividadeCadastro): void
    {
        $this->isFinalizadoAtividadeCadastro = $isFinalizadoAtividadeCadastro;
    }

    /**
     * @return bool
     */
    public function isIniciadoAtividadeAlegacao(): bool
    {
        return $this->isIniciadoAtividadeAlegacao;
    }

    /**
     * @param bool $isIniciadoAtividadeAlegacao
     */
    public function setIsIniciadoAtividadeAlegacao(bool $isIniciadoAtividadeAlegacao): void
    {
        $this->isIniciadoAtividadeAlegacao = $isIniciadoAtividadeAlegacao;
    }

    /**
     * @return bool
     */
    public function isFinalizadoAtividadeAlegacao(): bool
    {
        return $this->isFinalizadoAtividadeAlegacao;
    }

    /**
     * @param bool $isFinalizadoAtividadeAlegacao
     */
    public function setIsFinalizadoAtividadeAlegacao(bool $isFinalizadoAtividadeAlegacao): void
    {
        $this->isFinalizadoAtividadeAlegacao = $isFinalizadoAtividadeAlegacao;
    }

    /**
     * @return AlegacaoImpugnacaoResultadoTO[]|null
     */
    public function getAlegacoes(): ?array
    {
        return $this->alegacoes;
    }

    /**
     * @param AlegacaoImpugnacaoResultadoTO[]|null $alegacoes
     */
    public function setAlegacoes(?array $alegacoes): void
    {
        $this->alegacoes = $alegacoes;
    }

    /**
     * @return JulgamentoAlegacaoImpugResultadoTO|null
     */
    public function getJulgamentoAlegacao(): ?JulgamentoAlegacaoImpugResultadoTO
    {
        return $this->julgamentoAlegacao;
    }

    /**
     * @param JulgamentoAlegacaoImpugResultadoTO|null $julgamentoAlegacao
     */
    public function setJulgamentoAlegacao(?JulgamentoAlegacaoImpugResultadoTO $julgamentoAlegacao): void
    {
        $this->julgamentoAlegacao = $julgamentoAlegacao;
    }

    /**
     * @return bool
     */
    public function isIniciadoAtividadeJulgamento(): bool
    {
        return $this->isIniciadoAtividadeJulgamento;
    }

    /**
     * @param bool $isIniciadoAtividadeJulgamento
     */
    public function setIsIniciadoAtividadeJulgamento(bool $isIniciadoAtividadeJulgamento): void
    {
        $this->isIniciadoAtividadeJulgamento = $isIniciadoAtividadeJulgamento;
    }

    /**
     * @return bool
     */
    public function isFinalizadoAtividadeJulgamento(): bool
    {
        return $this->isFinalizadoAtividadeJulgamento;
    }

    /**
     * @param bool $isFinalizadoAtividadeJulgamento
     */
    public function setIsFinalizadoAtividadeJulgamento(bool $isFinalizadoAtividadeJulgamento): void
    {
        $this->isFinalizadoAtividadeJulgamento = $isFinalizadoAtividadeJulgamento;
    }

    /**
     * @return bool
     */
    public function isIniciadoAtividadeRecursoJulgamento(): bool
    {
        return $this->isIniciadoAtividadeRecursoJulgamento;
    }

    /**
     * @param bool $isIniciadoAtividadeRecursoJulgamento
     */
    public function setIsIniciadoAtividadeRecursoJulgamento(bool $isIniciadoAtividadeRecursoJulgamento): void
    {
        $this->isIniciadoAtividadeRecursoJulgamento = $isIniciadoAtividadeRecursoJulgamento;
    }

    /**
     * @return bool
     */
    public function isFinalizadoAtividadeRecursoJulgamento(): bool
    {
        return $this->isFinalizadoAtividadeRecursoJulgamento;
    }

    /**
     * @param bool $isFinalizadoAtividadeJulgamento
     */
    public function setIsFinalizadoAtividadeRecursoJulgamento(bool $isFinalizadoAtividadeRecursoJulgamento): void
    {
        $this->isFinalizadoAtividadeRecursoJulgamento = $isFinalizadoAtividadeRecursoJulgamento;
    }

    /**
     * @return bool
     */
    public function isIniciadoAtividadeContrarrazao(): ?bool
    {
        return $this->isIniciadoAtividadeContrarrazao;
    }

    /**
     * @param bool $isIniciadoAtividadeContrarrazao
     */
    public function setIsIniciadoAtividadeContrarrazao(?bool $isIniciadoAtividadeContrarrazao): void
    {
        $this->isIniciadoAtividadeContrarrazao = $isIniciadoAtividadeContrarrazao;
    }

    /**
     * @return bool
     */
    public function isFinalizadoAtividadeContrarrazao(): ?bool
    {
        return $this->isFinalizadoAtividadeContrarrazao;
    }

    /**
     * @param bool $isFinalizadoAtividadeContrarrazao
     */
    public function setIsFinalizadoAtividadeContrarrazao(?bool $isFinalizadoAtividadeContrarrazao): void
    {
        $this->isFinalizadoAtividadeContrarrazao = $isFinalizadoAtividadeContrarrazao;
    }

    /**
     * @return bool
     */
    public function isIniciadoAtividadeJulgamentoRecurso(): ?bool
    {
        return $this->isIniciadoAtividadeJulgamentoRecurso;
    }

    /**
     * @param bool $isIniciadoAtividadeJulgamentoRecurso
     */
    public function setIsIniciadoAtividadeJulgamentoRecurso(?bool $isIniciadoAtividadeJulgamentoRecurso): void
    {
        $this->isIniciadoAtividadeJulgamentoRecurso = $isIniciadoAtividadeJulgamentoRecurso;
    }

    /**
     * @return bool
     */
    public function getIsFinalizadoAtividadeJulgamentoRecurso(): ?bool
    {
        return $this->isFinalizadoAtividadeJulgamentoRecurso;
    }

    /**
     * @param bool $isFinalizadoAtividadeJulgamentoRecurso
     */
    public function setIsFinalizadoAtividadeJulgamentoRecurso(?bool $isFinalizadoAtividadeJulgamentoRecurso): void
    {
        $this->isFinalizadoAtividadeJulgamentoRecurso = $isFinalizadoAtividadeJulgamentoRecurso;
    }

    /**
     * @return bool
     */
    public function isHasRecursoJulgamento(): ?bool
    {
        return $this->hasRecursoJulgamento;
    }

    /**
     * @param bool $hasRecursoJulgamento
     */
    public function setHasRecursoJulgamento(?bool $hasRecursoJulgamento): void
    {
        $this->hasRecursoJulgamento = $hasRecursoJulgamento;
    }

    /**
     * @return JulgamentoRecursoImpugResultado
     */
    public function getJulgamentoRecurso(): ?JulgamentoRecursoImpugResultado
    {
        return $this->julgamentoRecurso;
    }

    /**
     * @param JulgamentoRecursoImpugResultado|null $julgamentoRecurso
     */
    public function setJulgamentoRecurso(?JulgamentoRecursoImpugResultado $julgamentoRecurso): void
    {
        $this->julgamentoRecurso = $julgamentoRecurso;
    }

    /**
     * @return bool|null
     */
    public function getHasJulgamentoRecurso(): ?bool
    {
        return $this->hasJulgamentoRecurso;
    }

    /**
     * @param bool|null $hasJulgamentoRecurso
     */
    public function setHasJulgamentoRecurso(?bool $hasJulgamentoRecurso): void
    {
        $this->hasJulgamentoRecurso = $hasJulgamentoRecurso;
    }

    /**
     * @return int|null
     */
    public function getIdStatusJulgamentoAlegacao(): ?int
    {
        return $this->idStatusJulgamentoAlegacao;
    }

    /**
     * @param int|null $idStatusJulgamentoAlegacao
     */
    public function setIdStatusJulgamentoAlegacao(?int $idStatusJulgamentoAlegacao): void
    {
        $this->idStatusJulgamentoAlegacao = $idStatusJulgamentoAlegacao;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalTO'.
     *
     * @param null $data
     * @return ImpugnacaoResultadoTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $impugnacaoResultadoTO = new ImpugnacaoResultadoTO();

        if ($data != null) {
            $impugnacaoResultadoTO->iniciarFlags();

            $impugnacaoResultadoTO->setId(Arr::get($data,'id'));
            $impugnacaoResultadoTO->setDataCadastro(Arr::get($data, 'dataCadastro'));
            $impugnacaoResultadoTO->setIdCauBR(Arr::get($data,'idCauBR'));
            $impugnacaoResultadoTO->setIdStatus(Arr::get($data,'idStatus'));
            $impugnacaoResultadoTO->setNarracao(Arr::get($data,'narracao'));
            $impugnacaoResultadoTO->setNumero(Arr::get($data,'numero'));
            $impugnacaoResultadoTO->setIdCalendario(Arr::get($data,'idCalendario'));
            $impugnacaoResultadoTO->setIdProfissional(Arr::get($data,'idProfissional'));

            $profissional = Arr::get($data, 'profissional');
            if (!empty($profissional)) {
                $impugnacaoResultadoTO->setProfissional(ProfissionalTO::newInstance($profissional));
            }

            $arquivos = Arr::get($data, 'arquivos', null);
            if (!empty($arquivos)) {
                $impugnacaoResultadoTO->setArquivos(array_map(function ($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            } else {
                if (!empty(Arr::get($data, 'nomeArquivo'))) {
                    $arquivo = ArquivoGenericoTO::newInstance([
                        'nome' => Arr::get($data, 'nomeArquivo'),
                        'nomeFisico' => Arr::get($data, 'nomeArquivoFisico')
                    ]);
                    $impugnacaoResultadoTO->setArquivos([$arquivo]);
                }
            }

            $calendario = Arr::get($data, 'calendario', null);
            if (!empty($calendario)) {
                $impugnacaoResultadoTO->setCalendario(CalendarioTO::newInstance($calendario));
            }

            $filial = Arr::get($data, 'cauBR', null);
            if (!empty($filial)) {
                $impugnacaoResultadoTO->setCauBR(FilialTO::newInstance($filial));
            }

            $alegacoes = Arr::get($data, 'alegacoes', null);
            if (!empty($alegacoes)) {
                $impugnacaoResultadoTO->setAlegacoes(array_map(function ($alegacao) {
                    return AlegacaoImpugnacaoResultadoTO::newInstance($alegacao);
                }, $alegacoes));
            }
            $impugnacaoResultadoTO->setHasAlegacao(!empty($alegacoes));

            $julgamentoAlegacao = Arr::get($data,'julgamentoAlegacao');
            if (!empty($julgamentoAlegacao)) {
                $impugnacaoResultadoTO->setJulgamentoAlegacao(JulgamentoAlegacaoImpugResultadoTO::newInstance($julgamentoAlegacao));

                if (!empty($impugnacaoResultadoTO->getJulgamentoAlegacao()->getStatusJulgamentoAlegacaoResultado())) {
                    $impugnacaoResultadoTO->setIdStatusJulgamentoAlegacao(
                        $impugnacaoResultadoTO->getJulgamentoAlegacao()->getStatusJulgamentoAlegacaoResultado()->getId()
                    );
                }
            }

            $julgamentoRecurso = JulgamentoRecursoImpugResultado::newInstance(Arr::get($data, 'julgamentoRecurso'));
            if(Arr::get($data, 'julgamentoRecurso')) {
                $impugnacaoResultadoTO->setJulgamentoRecurso($julgamentoRecurso);
            }

        }

        return $impugnacaoResultadoTO;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalTO'.
     *
     * @param ImpugnacaoResultado $entity
     * @param bool $isAddDadosCauBrRetorno
     * @return ImpugnacaoResultadoTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity(ImpugnacaoResultado $entity, $isAddDadosCauBrRetorno = false)
    {
        $impugnacaoResultadoTO = new ImpugnacaoResultadoTO();

        if ($entity != null) {
            $impugnacaoResultadoTO->setId($entity->getId());
            $impugnacaoResultadoTO->setDataCadastro($entity->getDataCadastro());
            $impugnacaoResultadoTO->setNarracao($entity->getNarracao());
            $impugnacaoResultadoTO->setNumero($entity->getNumero());
            $impugnacaoResultadoTO->setTipoValidacao($entity->getTipoValidacao());

            $status = $entity->getStatus();

            if (!empty($status)){
               $impugnacaoResultadoTO->setStatus(StatusGenericoTO::newInstance([
                   'id'=> $status->getId(),
                   'descricao'=> $status->getDescricao()
               ]));
            }

            if (!empty($entity->getCauBR())) {
                $impugnacaoResultadoTO->setIdCauBR($entity->getCauBR()->getId());

                $cauBr = FilialTO::newInstanceFromEntity($entity->getCauBR());
            } else {
                $impugnacaoResultadoTO->setIdCauBR(Constants::ID_IES_ELEITORAL);
                $cauBr = FilialTO::newInstance([
                    'id' => 0,
                    'descricao' => Constants::PREFIXO_IES,
                    'prefixo' => Constants::PREFIXO_IES
                ]);
            }

            if ($isAddDadosCauBrRetorno) {
                $impugnacaoResultadoTO->setCauBR($cauBr);
            }

            if (!empty($entity->getStatus())) {
                $impugnacaoResultadoTO->setIdStatus($entity->getStatus()->getId());
            }
            if (!empty($entity->getCalendario())) {
                $impugnacaoResultadoTO->setIdCalendario($entity->getCalendario()->getId());
            }
            if (!empty($entity->getProfissional())) {
                $impugnacaoResultadoTO->setIdProfissional($entity->getProfissional()->getId());
                $impugnacaoResultadoTO->setNomeProfissional($entity->getProfissional()->getNome());
                $impugnacaoResultadoTO->setRegistroProfissional($entity->getProfissional()->getRegistroNacional());
                $impugnacaoResultadoTO->setEmailProfissional(
                    !empty($entity->getProfissional()->getPessoa()) ? $entity->getProfissional()->getPessoa()->getEmail() : null
                );
            }
            if (!empty($entity->getNomeArquivo())) {
                $arquivo = ArquivoGenericoTO::newInstance([
                    'nome' => $entity->getNomeArquivo(),
                    'nomeFisico' => $entity->getNomeArquivoFisico()
                ]);
                $impugnacaoResultadoTO->setArquivos([$arquivo]);
            }
        }

        return $impugnacaoResultadoTO;
    }

    public function iniciarFlags()
    {
        $this->setIsIniciadoAtividadeCadastro(false);
        $this->setIsFinalizadoAtividadeCadastro(false);
        $this->setIsIniciadoAtividadeAlegacao(false);
        $this->setIsFinalizadoAtividadeAlegacao(false);
        $this->setIsIniciadoAtividadeJulgamento(false);
        $this->setIsFinalizadoAtividadeJulgamento(false);
        $this->setIsIniciadoAtividadeRecursoJulgamento(false);
        $this->setIsFinalizadoAtividadeRecursoJulgamento(false);
        $this->setHasAlegacao(false);
        $this->setHasJulgamento(false);
        $this->setHasRecursoJulgamentoImpugnante(false);
        $this->setHasRecursoJulgamentoImpugnado(false);
        $this->setIsIniciadoAtividadeContrarrazao(false);
        $this->setIsFinalizadoAtividadeContrarrazao(false);
        $this->setIsIniciadoAtividadeJulgamentoRecurso(false);
        $this->setIsFinalizadoAtividadeJulgamentoRecurso(false);
        $this->setHasJulgamentoRecurso(false);
    }
}
