<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\Denuncia;
use App\Entities\EncaminhamentoDenuncia;
use App\Entities\Filial;
use App\Entities\RecursoDenuncia;
use App\Entities\JulgamentoAdmissibilidade;
use App\Entities\Recurso;
use App\Factory\UsuarioFactory;
use App\Util\Utils;
use Psy\Util\Str;

/**
 * Classe de transferência associada a visualização de 'Denuncia'.
 *
 * @package App\To
 * @author  Squadra Tecnologia S/A.
 **/
class DenunciaViewTO
{

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $prefixo;

    /**
     * @var boolean
     */
    private $sigiloso;

    /**
     * @var string
     */
    private $ds_fatos;

    /**
     * @var integer
     */
    private $id_cau_uf;

    /**
     * @var integer
     */
    private $id_denuncia;

    /**
     * @var \DateTime
     */
    private $dt_denuncia;

    /**
     * @var int|null
     */
    private $id_denunciado;

    /**
     * @var string
     */
    private $nome_denunciado;

    /**
     * @var DenunciaDefesaTO
     */
    private $denuncia_defesa;

    /**
     * @var integer
     */
    private $id_tipo_denuncia;

    /**
     * @var string
     */
    private $nome_denunciante;

    /**
     * @var DenunciaAdmitidaTO
     */
    private $denuncia_admitida;

    /**
     * @var JulgamentoAdmissibilidadeTO
     */
    private $julgamento_admissibilidade;

    /**
     * @var int
     */
    private $numero_sequencial;

    /**
     * @var string
     */
    private $registro_nacional;

    /**
     * @var DenunciaInadmitidaTO
     */
    private $denuncia_inadmitida;

    /**
     * @var JulgamentoDenunciaTO
     */
    private $julgamento_denuncia;

    /**
     * @var integer
     */
    private $id_situacao_denuncia;

    /**
     * @var EncaminhamentoDenunciaTO[]
     */
    private $encaminhamentos_denuncia;

    /**
     * @var RecursoDenunciaTO[]
     */
    private $recursos_denuncia;

    /**
     * @var bool
     */
    private $is_relator_atual;

    /**
     * @var bool|null
     */
    private $is_coordenador_ce;

    /**
     * @var bool|null
     */
    private $is_coordenador_cen;

    /**
     * @var bool|null
     */
    private $is_assessor_ce;

    /**
     * @var bool|null
     */
    private $is_assessor_cen;

    /**
     * @var bool
     */
    private $has_defesa_prazo_encerrado;

    /**
     * @var bool
     */
    private $has_alegacao_final_concluido;

    /**
     * @var bool
     */
    private $has_audiencia_instrucao_pendente;

    /**
     * @var bool
     */
    private $has_alegacao_final_prazo_encerrado;

    /**
     * @var bool
     */
    private $has_impedimento_suspeicao_pendente;

    /**
     * @var bool
     */
    private $has_parecer_final_inserido;

    /**
     * @var bool
     */
    private $has_prazo_recurso_denuncia;

    /**
     * @var bool
     */
    private $has_eleicao_vigente;

    /**
     * @var integer
     */
    private $id_denunciante;

    /**
     * @var bool
     */
    private $has_contrarrazao_denunciante_dentro_prazo;

    /**
     * @var bool
     */
    private $has_contrarrazao_denunciado_dentro_prazo;

    /**
     * @var bool
     */
    private $has_encaminhamento_alegacao_final;

    /**
     * @var array|null
     */
    private $coordenadorComissao;

    /**
     * @var array|null
     */
    private $impedimentoSuspeicao;

    /**
     * Retorna uma nova instância de 'DenunciaTO'.
     *
     * @param Denuncia $denuncia
     * @return self
     */
    public static function newInstanceFromEntity($denuncia = null)
    {
        $instance = new self;

        if (null !== $denuncia) {
            $instance->setIdDenuncia($denuncia->getId());
            $instance->setSigiloso($denuncia->isSigiloso());
            $instance->setDtDenuncia($denuncia->getDataHora());
            $instance->setDsFatos($denuncia->getDescricaoFatos());
            $instance->setIsRelatorAtual($denuncia->isRelatorAtual());
            $instance->setHasEleicaoVigente($denuncia->isEleicaoVigente());
            $instance->setNumeroSequencial($denuncia->getNumeroSequencial());
            $instance->setIdTipoDenuncia($denuncia->getTipoDenuncia()->getId());
            $instance->setHasDefesaPrazoEncerrado($denuncia->hasDefesaPrazoEncerrado());
            $instance->setHasPrazoRecursoDenuncia($denuncia->getHasPrazoRecursoDenuncia());
            $instance->setHasAlegacaoFinalConcluido($denuncia->hasAlegacaoFinalConcluido());
            $instance->setHasAudienciaInstrucaoPendente($denuncia->hasAudienciaInstrucaoPendente());
            $instance->setHasImpedimentoSuspeicaoPendente($denuncia->hasImpedimentoSuspeicaoPendente());
            $instance->setHasAlegacaoFinalPrazoEncerrado($denuncia->hasAlegacaoFinalPendentePrazoEncerrado());
            $instance->setHasParecerFinalInserido($denuncia->hasParecerFinalInseridoParaDenuncia());
            $instance->setIdSituacaoDenuncia($denuncia
                ->getDenunciaSituacao()->last()->getSituacaoDenuncia()->getId());
            $instance->setHasContrarrazaoDenuncianteDentroPrazo($denuncia
                ->getHasContrarrazaoDenuncianteDentroPrazo());
            $instance->setHasContrarrazaoDenunciadoDentroPrazo($denuncia
                ->getHasContrarrazaoDenunciadoDentroPrazo());
            $instance->setHasEncaminhamentoAlegacaoFinal($denuncia
                ->getHasEncaminhamentoAlegacaoFinal());

            $filial = $denuncia->getFilial();
            if (empty($filial)) {
                $filial = Filial::newInstance(
                    ['id' => Constants::IES_ID, 'prefixo' => Constants::PREFIXO_IES]
                );
            }
            $instance->setIdCauUf($filial->getId());
            $instance->setPrefixo($filial->getPrefixo());

            $usuarioFactory = app()->make(UsuarioFactory::class);

            $pessoa = $denuncia->getPessoa();
            $instance->setEmail($usuarioFactory->isCorporativo() || !$denuncia->isSigiloso()
                ? $pessoa->getEmail()
                : Utils::ofuscarCampo($pessoa->getEmail())
            );

            $profissional = $pessoa->getProfissional();
            $instance->setIdDenunciante($profissional->getId());
            $instance->setNomeDenunciado($instance
                ->getNomeDenunciadoPorTipoDenuncia($denuncia));
            $instance->setIdDenunciado($instance
                ->getIdProfissionalDenunciadoPorTipoDenuncia($denuncia));

            $usuarioFactory = app()->make(UsuarioFactory::class);

            $instance->setNomeDenunciante($usuarioFactory->isCorporativo() || !$denuncia->isSigiloso()
                ? $profissional->getNome()
                : Utils::ofuscarCampo($profissional->getNome())
            );
            $instance->setRegistroNacional($usuarioFactory->isCorporativo() || !$denuncia->isSigiloso()
                ? $profissional->getRegistroNacional()
                : Utils::ofuscarCampo($profissional->getRegistroNacional())
            );

            $isAssessorCEUf = $denuncia->isAssessorCEUf();
            if (null !== $isAssessorCEUf) {
                $instance->setIsAssessorCEUf($isAssessorCEUf);
            }

            $isAssessorCEN = $denuncia->isAssessorCEN();
            if (null !== $isAssessorCEN) {
                $instance->setIsAssessorCEN($isAssessorCEN);
            }

            $denunciaAdmitida = $denuncia->getUltimaDenunciaAdmitida();
            if (null !== $denunciaAdmitida) {
                $instance->setDenunciaAdmitida(
                    DenunciaAdmitidaTO::newInstanceFromEntity($denunciaAdmitida)
                );
            }

            $julgamentoAdmissibilidade = $denuncia->getJulgamentoAdmissibilidade();
            if (null !== $julgamentoAdmissibilidade) {
                $instance->setJulgamentoAdmissibilidade(
                    JulgamentoAdmissibilidadeTO::newInstanceFromEntity($julgamentoAdmissibilidade)
                );
            }

            $denunciaInadmitida = $denuncia->getDenunciaInadmitida();
            if (null !== $denunciaInadmitida) {
                $instance->setDenunciaInadmitida(
                    DenunciaInadmitidaTO::newInstanceFromEntity($denunciaInadmitida)
                );
            }

            $denunciaDefesa = $denuncia->getDenunciaDefesa();
            if (null !== $denunciaDefesa) {
                $instance->setDenunciaDefesa(
                    DenunciaDefesaTO::newInstanceFromEntity($denunciaDefesa)
                );
            }

            $julgamentoDenuncia = $denuncia->getUltimoJulgamentoDenuncia();
            if (null !== $julgamentoDenuncia) {
                $julgamentoDenunciaTO = JulgamentoDenunciaTO::newInstanceFromEntity(
                    $julgamentoDenuncia);
                $julgamentoDenunciaTO->setRetificacao(
                    count($denuncia->getJulgamentoDenuncia()) > 1
                );

                $instance->setJulgamentoDenuncia($julgamentoDenunciaTO);
            }

            $encaminhamentosDenuncia = $denuncia->getEncaminhamentoDenuncia() ?? [];
            if (!is_array($encaminhamentosDenuncia)) {
                $encaminhamentosDenuncia = $encaminhamentosDenuncia->toArray();
            }

            $instance->setEncaminhamentosDenuncia(array_map(static function(EncaminhamentoDenuncia $encaminhamentoDenuncia) {
                return EncaminhamentoDenunciaTO::newInstanceFromEntity($encaminhamentoDenuncia);
            }, $encaminhamentosDenuncia));

            $recursoDenuncia = $denuncia->getRecursoDenuncia() ?? [];
            if (!is_array($recursoDenuncia)) {
                $recursoDenuncia = $recursoDenuncia->toArray();
            }

            $instance->setRecursosDenuncia(array_map(static function(RecursoDenuncia $recursoDenuncia) {
                return RecursoDenunciaTO::newInstanceFromEntity($recursoDenuncia);
            }, $recursoDenuncia));

            foreach($instance->getRecursosDenuncia() as $recursoDenunciaTO) {
                if($recursoDenunciaTO->isRecursoDenunciante() && !$usuarioFactory->isCorporativo() && $denuncia->isSigiloso()) {
                    $recursoDenunciaTO->setResponsavel( Utils::ofuscarCampo($recursoDenunciaTO->getResponsavel()));
                }else if(!is_null($recursoDenunciaTO->getContrarrazao()) && $recursoDenunciaTO->getContrarrazao()->isContrarrazaoDenunciante() && !$usuarioFactory->isCorporativo() && $denuncia->isSigiloso()) {
                    $recursoDenunciaTO->getContrarrazao()->setResponsavel(Utils::ofuscarCampo($recursoDenunciaTO->getContrarrazao()->getResponsavel()));
                }
            }

            $instance->setCoordenadorComissao($denuncia->getCoordenadorComissao());
            $instance->setImpedimentoSuspeicao($denuncia->getImpedimentoSuspeicao());
        }

        return $instance;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getDsFatos(): string
    {
        return $this->ds_fatos;
    }

    /**
     * @param string $dsFatos
     */
    public function setDsFatos(string $dsFatos): void
    {
        $this->ds_fatos = $dsFatos;
    }

    /**
     * @return int
     */
    public function getIdDenuncia(): ?int
    {
        return $this->id_denuncia;
    }

    /**
     * @param int $id
     */
    public function setIdDenuncia(int $id): void
    {
        $this->id_denuncia = $id;
    }

    /**
     * @return int
     */
    public function getIdTipoDenuncia(): int
    {
        return $this->id_tipo_denuncia;
    }

    /**
     * @param int $idTipoDenuncia
     */
    public function setIdTipoDenuncia(int $idTipoDenuncia): void
    {
        $this->id_tipo_denuncia = $idTipoDenuncia;
    }

    /**
     * @return JulgamentoAdmissibilidadeTO
     */
    public function getJulgamentoAdmissibilidade()
    {
        return $this->julgamento_admissibilidade;
    }

    /**
     * @param JulgamentoAdmissibilidadeTO $julgamentoAdmissibilidadeTO
     */
    public function setJulgamentoAdmissibilidade($julgamentoAdmissibilidadeTO)
    {
        $this->julgamento_admissibilidade = $julgamentoAdmissibilidadeTO;
    }

    /**
     * @return DenunciaAdmitidaTO
     */
    public function getDenunciaAdmitida(): ?DenunciaAdmitidaTO
    {
        return $this->denuncia_admitida;
    }

    /**
     * @param $denunciaAdmitida
     */
    public function setDenunciaAdmitida($denunciaAdmitida): void
    {
        $this->denuncia_admitida = $denunciaAdmitida;
    }

    /**
     * @return DenunciaInadmitidaTO
     */
    public function getDenunciaInadmitida(): ?DenunciaInadmitidaTO
    {
        return $this->denuncia_inadmitida;
    }

    /**
     * @param $denunciaInadmitida
     */
    public function setDenunciaInadmitida($denunciaInadmitida): void
    {
        $this->denuncia_inadmitida = $denunciaInadmitida;
    }

    /**
     * @return JulgamentoDenunciaTO
     */
    public function getJulgamentoDenuncia(): ?JulgamentoDenunciaTO
    {
        return $this->julgamento_denuncia;
    }

    /**
     * @param $julgamentoDenuncia
     */
    public function setJulgamentoDenuncia($julgamentoDenuncia): void
    {
        $this->julgamento_denuncia = $julgamentoDenuncia;
    }

    /**
     * @return DenunciaDefesaTO
     */
    public function getDenunciaDefesa(): ?DenunciaDefesaTO
    {
        return $this->denuncia_defesa;
    }

    /**
     * @param DenunciaDefesaTO $denunciaDefesa
     */
    public function setDenunciaDefesa($denunciaDefesa): void {
        $this->denuncia_defesa = $denunciaDefesa;
    }

    /**
     * @return string
     */
    public function getNomeDenunciado(): string
    {
        return $this->nome_denunciado;
    }

    /**
     * @param string $nomeDenunciado
     */
    public function setNomeDenunciado(string $nomeDenunciado): void
    {
        $this->nome_denunciado = $nomeDenunciado;
    }

    /**
     * @return int|null
     */
    public function getIdDenunciado(): ?int
    {
        return $this->id_denunciado;
    }

    /**
     * @param int|null $id_denunciado
     */
    public function setIdDenunciado(?int $id_denunciado): void
    {
        $this->id_denunciado = $id_denunciado;
    }

    /**
     * @return string
     */
    public function getNomeDenunciante(): string
    {
        return $this->nome_denunciante;
    }

    /**
     * @param string $nomeDenunciante
     */
    public function setNomeDenunciante(string $nomeDenunciante): void
    {
        $this->nome_denunciante = $nomeDenunciante;
    }

    /**
     * @return string
     */
    public function getPrefixo(): string
    {
        return $this->prefixo;
    }

    /**
     * @param string $prefixo
     */
    public function setPrefixo(string $prefixo): void
    {
        $this->prefixo = $prefixo;
    }

    /**
     * @return bool
     */
    public function isSigiloso(): bool
    {
        return $this->sigiloso;
    }

    /**
     * @param bool $sigiloso
     */
    public function setSigiloso(bool $sigiloso): void
    {
        $this->sigiloso = $sigiloso;
    }

    /**
     * @return int
     */
    public function getIdCauUf(): int
    {
        return $this->id_cau_uf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf(int $idCauUf): void
    {
        $this->id_cau_uf = $idCauUf;
    }

    /**
     * @return \DateTime
     */
    public function getDtDenuncia(): \DateTime
    {
        return $this->dt_denuncia;
    }

    /**
     * @param $dtDenuncia
     */
    public function setDtDenuncia($dtDenuncia): void
    {
        $this->dt_denuncia = $dtDenuncia;
    }

    /**
     * @return int
     */
    public function getNumeroSequencial(): int
    {
        return $this->numero_sequencial;
    }

    /**
     * @param int $numeroSequencial
     */
    public function setNumeroSequencial(int $numeroSequencial): void
    {
        $this->numero_sequencial = $numeroSequencial;
    }

    /**
     * @return string
     */
    public function getRegistroNacional(): string
    {
        return $this->registro_nacional;
    }

    /**
     * @param string $registroNacional
     */
    public function setRegistroNacional(string $registroNacional): void
    {
        $this->registro_nacional = $registroNacional;
    }

    /**
     * @return int
     */
    public function getIdSituacaoDenuncia(): int
    {
        return $this->id_situacao_denuncia;
    }

    /**
     * @param int $idSituacaoDenuncia
     */
    public function setIdSituacaoDenuncia(int $idSituacaoDenuncia): void
    {
        $this->id_situacao_denuncia = $idSituacaoDenuncia;
    }

    /**
     * @return EncaminhamentoDenunciaTO[]
     */
    public function getEncaminhamentosDenuncia(): array
    {
        return $this->encaminhamentos_denuncia;
    }

    /**
     * @param EncaminhamentoDenunciaTO[] $encaminhamentoDenuncia
     */
    public function setEncaminhamentosDenuncia(array $encaminhamentoDenuncia): void
    {
        $this->encaminhamentos_denuncia = $encaminhamentoDenuncia;
    }

    /**
     * @return RecursoDenunciaTO[]
     */
    public function getRecursosDenuncia(): array
    {
        return $this->recursos_denuncia;
    }

    /**
     * @param RecursoDenunciaTO[] $recursosDenuncia
     */
    public function setRecursosDenuncia(array $recursosDenuncia): void
    {
        $this->recursos_denuncia = $recursosDenuncia;
    }

    /**
     * @return bool
     */
    public function isRelatorAtual(): bool
    {
        return $this->is_relator_atual;
    }

    /**
     * @param bool $isRelatorAtual
     */
    public function setIsRelatorAtual(?bool $isRelatorAtual): void
    {
        $this->is_relator_atual = $isRelatorAtual;
    }

    /**
     * @return bool|null
     */
    public function isAssessorCE(): ?bool
    {
        return $this->is_assessor_ce;
    }

    /**
     * @param bool|null $isAssessorCE
     */
    public function setIsAssessorCEUf(?bool $isAssessorCE): void
    {
        $this->is_assessor_ce = $isAssessorCE;
    }

    /**
     * @return bool|null
     */
    public function isAssessorCen(): ?bool
    {
        return $this->is_assessor_cen;
    }

    /**
     * @param bool|null $isAssessorCEN
     */
    public function setIsAssessorCen(?bool $isAssessorCEN): void
    {
        $this->is_assessor_cen = $isAssessorCEN;
    }

    /**
     * @return bool
     */
    public function hasDefesaPrazoEncerrado(): bool
    {
        return $this->has_defesa_prazo_encerrado;
    }

    /**
     * @param bool $hasDefesaPrazoEncerrado
     */
    public function setHasDefesaPrazoEncerrado(bool $hasDefesaPrazoEncerrado): void
    {
        $this->has_defesa_prazo_encerrado = $hasDefesaPrazoEncerrado;
    }

    /**
     * @return bool
     */
    public function hasAlegacaoFinalConcluido(): bool
    {
        return $this->has_alegacao_final_concluido;
    }

    /**
     * @param bool $hasAlegacaoFinalConcluido
     */
    public function setHasAlegacaoFinalConcluido(bool $hasAlegacaoFinalConcluido): void
    {
        $this->has_alegacao_final_concluido = $hasAlegacaoFinalConcluido;
    }

    /**
     * @return bool
     */
    public function hasImpedimentoSuspeicaoPendente(): bool
    {
        return $this->has_impedimento_suspeicao_pendente;
    }

    /**
     * @param bool $hasImpedimentoSuspeicaoPendente
     */
    public function setHasImpedimentoSuspeicaoPendente(bool $hasImpedimentoSuspeicaoPendente): void
    {
        $this->has_impedimento_suspeicao_pendente = $hasImpedimentoSuspeicaoPendente;
    }

    /**
     * @return bool
     */
    public function hasAlegacaoFinalPrazoEncerrado(): bool
    {
        return $this->has_alegacao_final_prazo_encerrado;
    }

    /**
     * @param bool $hasAlegacaoFinalPrazoEncerrado
     */
    public function setHasAlegacaoFinalPrazoEncerrado(bool $hasAlegacaoFinalPrazoEncerrado): void
    {
        $this->has_alegacao_final_prazo_encerrado = $hasAlegacaoFinalPrazoEncerrado;
    }

    /**
     * @return bool
     */
    public function hasAudienciaInstrucaoPendente(): bool
    {
        return $this->has_audiencia_instrucao_pendente;
    }

    /**
     * @param bool $hasAudienciaInstrucaoPendente
     */
    public function setHasAudienciaInstrucaoPendente(bool $hasAudienciaInstrucaoPendente): void
    {
        $this->has_audiencia_instrucao_pendente = $hasAudienciaInstrucaoPendente;
    }

    /**
     * @return bool
     */
    public function isHasParecerFinalInserido(): bool
    {
        return $this->has_parecer_final_inserido;
    }

    /**
     * @param bool $has_parecer_final_inserido
     */
    public function setHasParecerFinalInserido(bool $has_parecer_final_inserido): void
    {
        $this->has_parecer_final_inserido = $has_parecer_final_inserido;
    }

    /**
     * @return bool
     */
    public function isHasPrazoRecursoDenuncia(): bool
    {
        return $this->has_prazo_recurso_denuncia;
    }

    /**
     * @param bool $has_prazo_recurso_denuncia
     */
    public function setHasPrazoRecursoDenuncia(bool $has_prazo_recurso_denuncia): void
    {
        $this->has_prazo_recurso_denuncia = $has_prazo_recurso_denuncia;
    }

    /**
     * @return bool
     */
    public function isHasEleicaoVigente(): bool
    {
        return $this->has_eleicao_vigente;
    }

    /**
     * @param bool $has_eleicao_vigente
     */
    public function setHasEleicaoVigente(bool $has_eleicao_vigente): void
    {
        $this->has_eleicao_vigente = $has_eleicao_vigente;
    }

    /**
     * @param \App\Entities\Denuncia $denuncia
     *
     * @return string
     */
    private function getNomeDenunciadoPorTipoDenuncia(Denuncia $denuncia): string
    {
        $denunciado = '-';
        $tipoDenuncia = $denuncia->getTipoDenuncia()->getId();

        if(Constants::TIPO_CHAPA === $tipoDenuncia) {
            $denunciado = $denuncia->getDenunciaChapa()->getChapaEleicao()
                ->getNumeroChapa();
        }

        if(Constants::TIPO_MEMBRO_CHAPA === $tipoDenuncia) {

            $profissional = !empty($denuncia->getDenunciaMembroChapa())
                ? $denuncia->getDenunciaMembroChapa()->getMembroChapa()
                    ->getProfissional()
                : null;

            // $profissional = $denuncia->getDenunciaMembroChapa()->getMembroChapa()
            //     ->getProfissional();
            
            $denunciado = $profissional ? $profissional->getNome() : '-';
        }

        if(Constants::TIPO_MEMBRO_COMISSAO === $tipoDenuncia) {
            $profissional = !empty($denuncia->getDenunciaMembroComissao()) ? $denuncia->getDenunciaMembroComissao()->getMembroComissao()->getProfissionalEntity() : null;
            $denunciado = $profissional ? $profissional->getNome() : '-';
        }

        return $denunciado;
    }

    /**
     * @param \App\Entities\Denuncia $denuncia
     *
     * @return null|int
     */
    private function getIdProfissionalDenunciadoPorTipoDenuncia(Denuncia $denuncia): ?int
    {
        $denunciado = null;
        $tipoDenuncia = $denuncia->getTipoDenuncia()->getId();

        if(Constants::TIPO_MEMBRO_CHAPA === $tipoDenuncia) {

            $profissional = !empty($denuncia->getDenunciaMembroChapa())
                ? $denuncia->getDenunciaMembroChapa()->getMembroChapa()->getProfissional()
                : null;

            $denunciado = $profissional ? $profissional->getId() : null;
        }

        if(Constants::TIPO_MEMBRO_COMISSAO === $tipoDenuncia) {
            
            $profissional = !empty($denuncia->getDenunciaMembroComissao())
                ? $denuncia->getDenunciaMembroComissao()->getMembroComissao()->getProfissionalEntity()
                : null;

            $denunciado = $profissional ? $profissional->getId() : null;
        }

        return $denunciado;
    }

    /**
     * @return int
     */
    public function getIdDenunciante(): int
    {
        return $this->id_denunciante;
    }

    /**
     * @param int $id_denunciante
     */
    public function setIdDenunciante(int $id_denunciante): void
    {
        $this->id_denunciante = $id_denunciante;
    }

    /**
     * @return bool
     */
    public function isHasContrarrazaoDenuncianteDentroPrazo(): ?bool
    {
        return $this->has_contrarrazao_denunciante_dentro_prazo;
    }

    /**
     * @param bool $has_contrarrazao_denunciante_dentro_prazo
     */
    public function setHasContrarrazaoDenuncianteDentroPrazo(?bool $has_contrarrazao_denunciante_dentro_prazo): void
    {
        $this->has_contrarrazao_denunciante_dentro_prazo = $has_contrarrazao_denunciante_dentro_prazo;
    }

    /**
     * @return bool
     */
    public function isHasContrarrazaoDenunciadoDentroPrazo(): ?bool
    {
        return $this->has_contrarrazao_denunciado_dentro_prazo;
    }

    /**
     * @param bool $has_contrarrazao_denunciado_dentro_prazo
     */
    public function setHasContrarrazaoDenunciadoDentroPrazo(?bool $has_contrarrazao_denunciado_dentro_prazo): void
    {
        $this->has_contrarrazao_denunciado_dentro_prazo = $has_contrarrazao_denunciado_dentro_prazo;
    }

    /**
     * @return bool
     */
    public function isHasEncaminhamentoAlegacaoFinal(): ?bool
    {
        return $this->has_encaminhamento_alegacao_final;
    }

    /**
     * @param bool $has_encaminhamento_alegacao_final
     */
    public function setHasEncaminhamentoAlegacaoFinal(?bool $has_encaminhamento_alegacao_final): void
    {
        $this->has_encaminhamento_alegacao_final = $has_encaminhamento_alegacao_final;
    }

    public function getCoordenadorComissao()
    {
        return $this->coordenadorComissao;
    }

    public function setCoordenadorComissao($coordenadorComissao): void
    {
        $this->coordenadorComissao = $coordenadorComissao;
    }

    public function getImpedimentoSuspeicao()
    {
        return $this->impedimentoSuspeicao;
    }

    public function setImpedimentoSuspeicao($impedimentoSuspeicao): void
    {
        $this->impedimentoSuspeicao = $impedimentoSuspeicao;
    }
}
