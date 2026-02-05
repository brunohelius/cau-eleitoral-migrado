<?php

namespace App\To;

use App\Entities\DenunciaDefesa;
use App\Entities\EncaminhamentoDenuncia;
use App\Util\Utils;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada a listagem de encaminhamentos de denúncia
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 */
class ListagemEncaminhamentoDenunciaTO
{
    /**
     * @var integer
     */
    private $idEncaminhamento;

    /**
     * @var integer
     */
    private $numero;

    /**
     * @var string
     */
    private $tipoEncaminhamento;

    /**
     * @var integer
     */
    private $idTipoEncaminhamento;

    /**
     * @var string
     */
    private $relator;

    /**
     * @var \DateTime
     */
    private $dataEncaminhamento;

    /**
     * @var \DateTime
     */
    private $prazoEnvio;

    /**
     * @var array[]
     */
    private $destinatarios;

    /**
     * @var string
     */
    private $status;

    /**
     * @var integer
     */
    private $idStatus;

    /**
     * @var boolean
     */
    private $isAcaoAlegacoesFinais;

    /**
     * @var boolean
     */
    private $isDestinatarioDenunciado;

    /**
     * @var boolean
     */
    private $isDestinatarioDenunciante;

    /**
     * @var boolean
     */
    private $isDestinatarioEncaminhamento = false;

    /**
     * @var boolean
     */
    private $hasEmcaminhamentoSuspeicaoPendente = false;

    /**
     * @var boolean
     */
    private $isRelatorAtual = false;

    /**
     * @var boolean
     */
    private $isAcaoInserirNovoRelator;


    /**
     * @var boolean
     */
    private $isPrazoVencido = false;


    /**
     * Retorna uma nova instância de 'DenunciaParecerTO'.
     *
     * @param $data
     * @return self
     */
    public static function newInstance($data = null)
    {
        $denunciaParecerTO = new self;

        if ($data != null) {
            $denunciaParecerTO->setIdEncaminhamento(Utils::getValue("id", $data));
            $denunciaParecerTO->setNumero(Utils::getValue("sequencia", $data));
            $denunciaParecerTO->setTipoEncaminhamento(Utils::getValue("tipoEncaminhamento.descricao", $data));
            $denunciaParecerTO->setIdTipoEncaminhamento(Utils::getValue("tipoEncaminhamento.id", $data));
            $denunciaParecerTO->setRelator(Utils::getValue("membroComissao.profissionalEntity.nome", $data));
            $denunciaParecerTO->setDataEncaminhamento(Utils::getValue("data", $data));
            $denunciaParecerTO->setPrazoEnvio(Utils::getValue("data", $data));
            $denunciaParecerTO->setDestinatarios(Utils::getValue("destinatarios", $data));
            $denunciaParecerTO->setStatus(Utils::getValue("tipoSituacaoEncaminhamento.descricao", $data));
            $denunciaParecerTO->setIdStatus(Utils::getValue("tipoSituacaoEncaminhamento.id", $data));
            $denunciaParecerTO->setIsAcaoAlegacoesFinais(Utils::getValue("isAcaoAlegacoesFinais", $data));
            $denunciaParecerTO->setIsAcaoInserirNovoRelator(Utils::getValue("isAcaoInserirNovoRelator", $data));
            $denunciaParecerTO->setIsDestinatarioDenunciado(Utils::getValue("destinoDenunciado", $data));
            $denunciaParecerTO->setIsDestinatarioDenunciante(Utils::getValue("destinoDenunciante", $data));

        }

        return $denunciaParecerTO;
    }

    /**
     * Retorna uma nova instância de 'DenunciaParecerTO'.
     *
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     * @return self
     */
    public static function newInstanceFromEntity(EncaminhamentoDenuncia $encaminhamentoDenuncia)
    {
        $denunciaParecerTO = new self;

        $denunciaParecerTO->setIdEncaminhamento($encaminhamentoDenuncia->getId());
        $denunciaParecerTO->setNumero($encaminhamentoDenuncia->getSequencia());
        $denunciaParecerTO->setTipoEncaminhamento($encaminhamentoDenuncia->getTipoEncaminhamento()->getDescricao());
        $denunciaParecerTO->setIdTipoEncaminhamento($encaminhamentoDenuncia->getTipoEncaminhamento()->getId());
        $denunciaParecerTO->setRelator($encaminhamentoDenuncia->getMembroComissao()->getProfissionalEntity()->getNome());
        $denunciaParecerTO->setDataEncaminhamento($encaminhamentoDenuncia->getData());
        $denunciaParecerTO->setStatus($encaminhamentoDenuncia->getTipoSituacaoEncaminhamento()->getDescricao());

        $denunciaParecerTO->setIsDestinatarioDenunciado($encaminhamentoDenuncia->isDestinoDenunciado());
        $denunciaParecerTO->setIsDestinatarioDenunciante($encaminhamentoDenuncia->isDestinoDenunciante());

        return $denunciaParecerTO;
    }

    /**
     * @return int
     */
    public function getIdEncaminhamento(): ?int
    {
        return $this->idEncaminhamento;
    }

    /**
     * @param int $idEncaminhamento
     */
    public function setIdEncaminhamento(?int $idEncaminhamento): void
    {
        $this->idEncaminhamento = $idEncaminhamento;
    }

    /**
     * @return int
     */
    public function getNumero(): ?int
    {
        return $this->numero;
    }

    /**
     * @param int $numero
     */
    public function setNumero(?int $numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return string
     */
    public function getTipoEncaminhamento(): ?string
    {
        return $this->tipoEncaminhamento;
    }

    /**
     * @param string $tipoEncaminhamento
     */
    public function setTipoEncaminhamento(?string $tipoEncaminhamento): void
    {
        $this->tipoEncaminhamento = $tipoEncaminhamento;
    }

    /**
     * @return int
     */
    public function getIdTipoEncaminhamento(): ?int
    {
        return $this->idTipoEncaminhamento;
    }

    /**
     * @param int $idTipoEncaminhamento
     */
    public function setIdTipoEncaminhamento(?int $idTipoEncaminhamento): void
    {
        $this->idTipoEncaminhamento = $idTipoEncaminhamento;
    }

    /**
     * @return string
     */
    public function getRelator(): ?string
    {
        return $this->relator;
    }

    /**
     * @param string $relator
     */
    public function setRelator(?string $relator): void
    {
        $this->relator = $relator;
    }

    /**
     * @return \DateTime
     */
    public function getDataEncaminhamento(): ?\DateTime
    {
        return $this->dataEncaminhamento;
    }

    /**
     * @param \DateTime $dataEncaminhamento
     */
    public function setDataEncaminhamento(?\DateTime $dataEncaminhamento): void
    {
        $this->dataEncaminhamento = $dataEncaminhamento;
    }


    /**
     * @return \DateTime
     */
    public function getPrazoEnvio(): ?\DateTime
    {
        return $this->prazoEnvio;
    }

    /**
     * @param \DateTime $prazoEnvio
     */
    public function setPrazoEnvio(?\DateTime $prazoEnvio): void
    {
        $this->prazoEnvio = $prazoEnvio;
    }

    /**
     * @return array[]
     */
    public function getDestinatarios(): ?array
    {
        return $this->destinatarios;
    }

    /**
     * @param array[] $destinatario
     */
    public function setDestinatarios(?array $destinatarios): void
    {
        $this->destinatarios = $destinatarios;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getIdStatus(): ?int
    {
        return $this->idStatus;
    }

    /**
     * @param int $idStatus
     */
    public function setIdStatus(?int $idStatus): void
    {
        $this->idStatus = $idStatus;
    }

    /**
     * @return bool
     */
    public function isAcaoAlegacoesFinais(): ?bool
    {
        return $this->isAcaoAlegacoesFinais;
    }

    /**
     * @param bool $isAcaoAlegacoesFinais
     */
    public function setIsAcaoAlegacoesFinais(?bool $isAcaoAlegacoesFinais): void
    {
        $this->isAcaoAlegacoesFinais = $isAcaoAlegacoesFinais;
    }

    /**
     * @return bool
     */
    public function isDestinatarioDenunciado()
    {
        return $this->isDestinatarioDenunciado;
    }

    /**
     * @param bool $isDestinatarioDenunciado
     */
    public function setIsDestinatarioDenunciado($isDestinatarioDenunciado)
    {
        $this->isDestinatarioDenunciado = $isDestinatarioDenunciado;
    }

    /**
     * @return bool
     */
    public function isDestinatarioDenunciante()
    {
        return $this->isDestinatarioDenunciante;
    }

    /**
     * @return bool
     */
    public function isDestinatarioEncaminhamento()
    {
        return $this->isDestinatarioEncaminhamento;
    }

    /**
     * @param bool $isDestinatarioEncaminhamento
     */
    public function setIsDestinatarioEncaminhamento($isDestinatarioEncaminhamento)
    {
        $this->isDestinatarioEncaminhamento = $isDestinatarioEncaminhamento;
    }

    /**
     * @param bool $isDestinatarioDenunciante
     */
    public function setIsDestinatarioDenunciante($isDestinatarioDenunciante)
    {
        $this->isDestinatarioDenunciante = $isDestinatarioDenunciante;
    }

    /**
     * @return bool
     */
    public function hasEmcaminhamentoSuspeicaoPendente()
    {
        return $this->hasEmcaminhamentoSuspeicaoPendente;
    }

    /**
     * @param bool $hasEmcaminhamentoSuspeicaoPendente
     */
    public function setHasEmcaminhamentoSuspeicaoPendente($hasEmcaminhamentoSuspeicaoPendente)
    {
        $this->hasEmcaminhamentoSuspeicaoPendente = $hasEmcaminhamentoSuspeicaoPendente;
    }

    /**
     * @return bool
     */
    public function isRelatorAtual()
    {
        return $this->isRelatorAtual;
    }

    /**
     * @param bool $isRelatorAtual
     */
    public function setIsRelatorAtual($isRelatorAtual)
    {
        $this->isRelatorAtual = $isRelatorAtual;
    }

    /**
     * @return bool
     */
    public function isAcaoInserirNovoRelator(): ?bool
    {
        return $this->isAcaoInserirNovoRelator;
    }

    /**
     * @param bool $isAcaoInserirNovoRelator
     */
    public function setIsAcaoInserirNovoRelator(?bool $isAcaoInserirNovoRelator): void
    {
        $this->isAcaoInserirNovoRelator = $isAcaoInserirNovoRelator;
    }

    /**
     * @return bool
     */
    public function isPrazoVencido()
    {
        return $this->isPrazoVencido;
    }

    /**
     * @param bool $isPrazoVencido
     */
    public function setIsPrazoVencido($isPrazoVencido)
    {
        $this->isPrazoVencido = $isPrazoVencido;
    }


}
