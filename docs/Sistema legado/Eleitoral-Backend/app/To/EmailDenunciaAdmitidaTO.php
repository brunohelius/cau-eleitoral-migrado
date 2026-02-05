<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\DenunciaAdmissibilidade;
use App\Entities\DenunciaAdmitida;
use App\Entities\SituacaoDenuncia;

/**
 * Class EmailDenunciaAdmitidaTO
 * @package App\To
 */
class EmailDenunciaAdmitidaTO
{
    /**
     * @var string
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
     * @var string
     */
    public $numeroChapa;

    /**
     * @var string
     */
    public $uf;

    /**
     * @var string
     */
    public $statusDenuncia;

    /**
     * @var string
     */
    public $relator;

    /**
     * @param DenunciaAdmissibilidade $denunciaAdmitida
     * @param SituacaoDenuncia $situacaoDenuncia
     * @return static
     */
    public static function newInstanceFromEntity(
        DenunciaAdmissibilidade $denunciaAdmitida,
        SituacaoDenuncia $situacaoDenuncia
    ): self {
        $that = new self;
        $denuncia = $denunciaAdmitida->getDenuncia();

        $that->protocolo = $denuncia->getNumeroSequencial();
        $that->processoEleitoral = '';
        $that->tipoDenuncia = $denuncia->getTipoDenuncia()->getDescricao();
        switch ($denuncia->getTipoDenuncia()->getId()) {
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
        $that->statusDenuncia = $situacaoDenuncia->getDescricao();
        $that->relator = $denunciaAdmitida->getMembroComissao()->getProfissionalEntity()->getNome();
        return $that;
    }
}
