<?php
/*
 * UfCalendarioBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\UfCalendario;
use App\Repository\UfCalendarioRepository;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'UfCalendario'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class UfCalendarioBO extends AbstractBO
{

    /**
     * @var UfCalendarioRepository
     */
    private $ufCalendarioRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->ufCalendarioRepository = $this->getRepository(UfCalendario::class);
    }

    /**
     * Recupera as Ufs de acordo com o calendário informado.
     *
     * @param $idCalendario
     * @return array
     */
    public function getUfsCalendario($idCalendario)
    {
        return $this->ufCalendarioRepository->getPorCalendario($idCalendario);
    }

    /**
     * Método retorna os ids das cau ufs selecionadas no calendário
     *
     * @param $idCalendario
     * @return array
     */
    public function getIdsCauUfCalendario($idCalendario)
    {
        $ufsCalendario = $this->getUfsCalendario(
            $idCalendario
        );

        $idsCauUf = [];
        if(!empty($ufsCalendario)) {
            $idsCauUf = array_map(function($ufCalendario){
                /** @var UfCalendario $ufCalendario */
                return $ufCalendario->getIdCauUf();
            }, $ufsCalendario);
        }
        return $idsCauUf;
    }

    /**
     * Recupera as Ufs de acordo com o calendário informado.
     *
     * @param $idCalendario
     * @param $idCauUf
     * @return bool
     */
    public function isCauUfIncluidaCalendario($idCalendario, $idCauUf)
    {
        $ufCalendario = $this->ufCalendarioRepository->getPorCalendarioCauUf($idCalendario, $idCauUf);

        return (!empty($ufCalendario));
    }

}
