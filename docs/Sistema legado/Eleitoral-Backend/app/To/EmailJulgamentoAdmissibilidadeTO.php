<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\EncaminhamentoDenuncia;
use App\Entities\JulgamentoAdmissibilidade;
use App\Entities\SituacaoDenuncia;
use App\Entities\TestemunhaDenuncia;

/**
 * Class EmailJulgamentoAdmissibilidadeTO
 * @package App\To
 */
class EmailJulgamentoAdmissibilidadeTO
{
    public $julgamento;

    /**
     * @var int
     */
    public $protocolo;

    /**
     * @var string
     */
    public $processoEleitoral;

    /**
     * @var string
     */
    public $tipoDenuncia;

    /**
     * @var string
     */
    public $nomeDenunciado;

    /**
     * @var integer
     */
    public $numeroChapa;

    /**
     * @var string
     */
    public $uf;

    /**
     * @var
     */
    public $narracaoFatos;

    /**
     * @var
     */
    public $testemunhas = [];

    /**
     * @var string
     */
    public $statusDenuncia;

    /**
     * Retorna uma nova instância de 'EmailEncaminhamentoAlegacaoFinalTO'.
     *
     * @param JulgamentoAdmissibilidade $julgamento
     * @param SituacaoDenuncia|null $situacaoDenuncia
     * @return self
     */
    public static function newInstanceFromEntity(
        JulgamentoAdmissibilidade $julgamento,
        SituacaoDenuncia $situacaoDenuncia = null
    ): self {
        $that = new self;
        $denuncia = $julgamento->getDenuncia();

        $that->julgamento = $julgamento->getTipoJulgamento()->getDescricao();
        $that->protocolo = $denuncia->getNumeroSequencial();
        $that->processoEleitoral = $denuncia->getAtividadeSecundaria()->getAtividadePrincipalCalendario()
            ->getCalendario()->getEleicao()->getSequenciaFormatada();
        $that->tipoDenuncia = $denuncia->getTipoDenuncia()->getDescricao();
        switch ($julgamento->getDenuncia()->getTipoDenuncia()->getId()) {
            case Constants::TIPO_CHAPA:
                $that->numeroChapa = $denuncia->getDenunciaChapa()->getChapaEleicao()->getNumeroChapa();
                break;
            case Constants::TIPO_MEMBRO_CHAPA:
                $membro = $denuncia->getDenunciaMembroChapa()->getMembroChapa();
                $that->nomeDenunciado = $membro->getProfissional()->getNome();
                $that->numeroChapa = $membro->getChapaEleicao()->getNumeroChapa();
                break;
            case Constants::TIPO_MEMBRO_COMISSAO:
                $that->nomeDenunciado = $denuncia->getDenunciaMembroComissao()
                    ->getMembroComissao()
                    ->getProfissionalEntity()
                    ->getNome();
                break;
        }
        $that->uf = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getPrefixo() : Constants::PREFIXO_IES;
        $that->narracaoFatos = $denuncia->getDescricaoFatos();
        $denuncia->getTestemunhas()->map(function(TestemunhaDenuncia $testemunha) use($that) {
            $that->testemunhas[] = $testemunha->getNome();
        })->toArray();
        $that->statusDenuncia = $situacaoDenuncia->getDescricao();
        return $that;
    }
}
