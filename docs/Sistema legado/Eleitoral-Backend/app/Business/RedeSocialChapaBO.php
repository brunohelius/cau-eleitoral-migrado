<?php
/*
 * ChapaEleicaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ChapaEleicao;
use App\Entities\RedeSocialChapa;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\RedeSocialChapaRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'RedeSocialChapa'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class RedeSocialChapaBO extends AbstractBO
{

    /**
     * @var RedeSocialChapaRepository
     */
    private $redeSocialChapaRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->redeSocialChapaRepository = $this->getRepository(RedeSocialChapa::class);
    }

    /**
     * Método que salva as redes sociais de acordo com a etapa de criação da ChapaEleicao
     *
     * @param ChapaEleicao $chapaEleicao
     * @param $redesSociais
     * @param bool $isAcessorCEN
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarRedesSociais(
        ChapaEleicao $chapaEleicao,
        $redesSociais,
        $isAcessorCEN,
        $isValidarEtapa = true,
        $isApenasTipoOutros = false
    ) {
        $filtros = ['chapaEleicao' => $chapaEleicao->getId()];
        if ($isApenasTipoOutros) {
            $filtros['tipoRedeSocial'] = Constants::TIPO_REDE_SOCIAL_OUTROS;
        }

        $redesSociaisChapa = $this->redeSocialChapaRepository->findBy($filtros);

        $redesSociaisSalvar = array_filter($redesSociais, function ($redeSocial) use ($isApenasTipoOutros) {
            /** @var RedeSocialChapa $redeSocial */
            return (
                !empty($redeSocial->getDescricao()) &&
                (
                    !$isApenasTipoOutros ||
                    ($isApenasTipoOutros && $redeSocial->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_OUTROS)
                )
            );
        });

        $idsRedeSocialSalvar = array_map(function ($redeSocial) {
            /** @var RedeSocialChapa $redeSocial */
            return $redeSocial->getId();
        }, $redesSociaisSalvar);

        $redesSociaisExcluir = array_filter($redesSociaisChapa, function ($redeSocial) use ($idsRedeSocialSalvar) {
            $isRedeSocialIncluida = in_array($redeSocial->getId(), $idsRedeSocialSalvar);
            return !$isRedeSocialIncluida || $isRedeSocialIncluida && $redeSocial->getId();
        });

        if (
            !empty($redesSociaisExcluir)
            and (!$isValidarEtapa or $isAcessorCEN or $chapaEleicao->getIdEtapa() != Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA)
        ) {
            $this->redeSocialChapaRepository->deleteEmLote($redesSociaisExcluir);
        }

        if (!empty($redesSociaisSalvar)) {
            /** @var RedeSocialChapa $redeSocial */
            foreach ($redesSociaisSalvar as $redeSocial) {

                $podeSalvar = true;

                if($isValidarEtapa) {
                    $podeSalvar = $this->validarRedeSocialChapaSalvarPorEtapa(
                        $redeSocial,
                        $chapaEleicao->getIdEtapa(),
                        $isAcessorCEN
                    );
                }

                if ($podeSalvar) {
                    $this->validarCamposObrigatoriosRedeSocialChapa($redeSocial);
                    if (is_null($redeSocial->isAtivo()) && empty($redeSocial->getId())) {
                        $redeSocial->setIsAtivo(true);
                    }

                    $redeSocial->setChapaEleicao($chapaEleicao);
                    $this->redeSocialChapaRepository->persist($redeSocial);
                }
            }
        }
    }

    /**
     * Método que salva as redes sociais de acordo com a etapa de criação da ChapaEleicao
     *
     * @param ChapaEleicao $chapaEleicao
     * @param $redesSociais
     * @param bool $isAcessorCEN
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarRedesSociaisOutros(ChapaEleicao $chapaEleicao, $redesSociais)
    {
        $redesSociaisChapa = $this->redeSocialChapaRepository->findBy([
            'chapaEleicao' => $chapaEleicao->getId(),
            'tipoRedeSocial' => Constants::TIPO_REDE_SOCIAL_OUTROS
        ]);

        $redesSociaisSalvar = array_filter($redesSociais, function ($redeSocial) {
            /** @var RedeSocialChapa $redeSocial */
            return (
                !empty($redeSocial->getDescricao()) &&
                $redeSocial->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_OUTROS
            );
        });

        $idsRedeSocialSalvar = array_map(function ($redeSocial) {
            /** @var RedeSocialChapa $redeSocial */
            return $redeSocial->getId();
        }, $redesSociaisSalvar);

        $redesSociaisExcluir = array_filter($redesSociaisChapa, function ($redeSocial) use ($idsRedeSocialSalvar) {
            $isRedeSocialIncluida = in_array($redeSocial->getId(), $idsRedeSocialSalvar);
            return !$isRedeSocialIncluida || $isRedeSocialIncluida && $redeSocial->getId();
        });

        if (!empty($redesSociaisExcluir)) {
            $this->redeSocialChapaRepository->deleteEmLote($redesSociaisExcluir);
        }

        if (!empty($redesSociaisSalvar)) {
            /** @var RedeSocialChapa $redeSocial */
            foreach ($redesSociaisSalvar as $redeSocial) {
                $this->validarCamposObrigatoriosRedeSocialChapa($redeSocial);
                if (is_null($redeSocial->isAtivo()) && empty($redeSocial->getId())) {
                    $redeSocial->setIsAtivo(true);
                }

                $redeSocial->setChapaEleicao($chapaEleicao);
                $this->redeSocialChapaRepository->persist($redeSocial);
            }
        }
    }

    /**
     * Método para remover todas as Redes Sociais associado a uma Chapa Eleção
     *
     * @param integer $idChapaEleicao
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function excluirRedesSociaisChapa($idChapaEleicao)
    {
        $redesSociaisChapa = $this->redeSocialChapaRepository->findBy(['chapaEleicao' => $idChapaEleicao]);

        if (!empty($redesSociaisChapa)) {
            $this->redeSocialChapaRepository->deleteEmLote($redesSociaisChapa);
        }
    }

    /**
     * Verifica se os campos obrigatórios foram preenchidos da Rede Social da Chapa.
     *
     * @param RedeSocialChapa $redeSocialChapa
     *
     * @throws NegocioException
     */
    private function validarCamposObrigatoriosRedeSocialChapa(RedeSocialChapa $redeSocialChapa)
    {
        if (empty($redeSocialChapa->getTipoRedeSocial())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
        }

        if (empty($redeSocialChapa->getDescricao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
        }
    }

    /**
     * Método auxiliar que verifica se uma rede social pode ser salva de acordo com a etapa da Chapa da Eleição
     *
     * @param RedeSocialChapa $redeSocialChapa
     * @param integer $idEtapa
     * @param bool $isAcessorCEN
     *
     * @return bool
     */
    private function validarRedeSocialChapaSalvarPorEtapa(RedeSocialChapa $redeSocialChapa, $idEtapa, $isAcessorCEN)
    {
        $podeSalvar = false;

        $isEtapaConfirmada = $idEtapa == Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA;
        $isTipoRedeSocialOutros = $redeSocialChapa->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_OUTROS;
        if (
            $isAcessorCEN
            or !$isEtapaConfirmada
            or ($isEtapaConfirmada and empty($redeSocialChapa->getId()) and $isTipoRedeSocialOutros)
        ) {
            $podeSalvar = true;
        }

        return $podeSalvar;
    }
}
