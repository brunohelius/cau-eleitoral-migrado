<?php
/*
 * RedeSocialHistoricoPlataformaBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\RedeSocialChapa;
use App\Entities\RedeSocialHistoricoPlataforma;
use App\Repository\RedeSocialHistoricoPlataformaRepository;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'RedeSocialHistoricoPlataforma'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class RedeSocialHistoricoPlataformaBO extends AbstractBO
{
    /**
     * @var RedeSocialHistoricoPlataformaRepository
     */
    private $redeSocialHistoricoPlataformaRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }

    /**
     * @param $plataformaChapaHistorico
     * @param $redesSociais
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function salvar($plataformaChapaHistorico, $redesSociais)
    {
        if(!empty($redesSociais)) {

            $redesSociaisParaSalvar = [];

            foreach ($redesSociais as $redeSocial) {
                array_push($redesSociaisParaSalvar, $this->prepararRedeSocialHistoricoPlataformaSalvar($redeSocial, $plataformaChapaHistorico));
            }

            $this->getRedeSocialHistoricoPlataformaRepository()->persistEmLote($redesSociaisParaSalvar);
        }
    }

    /**
     * Prepara RedeSocialHistorico para salvar
     * @param RedeSocialChapa $redeSocial
     * @return RedeSocialHistoricoPlataforma
     */
    private function prepararRedeSocialHistoricoPlataformaSalvar($redeSocial, $plataformaChapaHistorico)
    {
        $redeSocialHistoricoPlataforma = RedeSocialHistoricoPlataforma::newInstance([
            'descricao' => $redeSocial->getDescricao(),
            'isAtivo'=> $redeSocial->isAtivo()
        ]);

        $redeSocialHistoricoPlataforma->setTipoRedeSocial($redeSocial->getTipoRedeSocial());
        $redeSocialHistoricoPlataforma->setPlataformaChapaHistorico($plataformaChapaHistorico);

        return $redeSocialHistoricoPlataforma;
    }

    /**
     * Retorna RedeSocialHistoricoPlataorma
     * @return RedeSocialHistoricoPlataformaRepository
     */
    private function getRedeSocialHistoricoPlataformaRepository()
    {
        if(empty($this->redeSocialHistoricoPlataformaRepository)){
            $this->redeSocialHistoricoPlataformaRepository = $this->getRepository(RedeSocialHistoricoPlataforma::class);
        }
        return $this->redeSocialHistoricoPlataformaRepository;
    }

}
