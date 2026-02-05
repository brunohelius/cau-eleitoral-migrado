<?php
/*
 * PlataformaChapaHistoricoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\ChapaEleicao;
use App\Entities\PlataformaChapaHistorico;
use App\Entities\Profissional;
use App\Entities\RedeSocialHistoricoPlataforma;
use App\Repository\PlataformaChapaHistoricoRepository;
use App\To\PlataformaChapaHistoricoTO;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'PlataformaChapaHistorico'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class PlataformaChapaHistoricoBO extends AbstractBO
{

    /**
     * @var PlataformaChapaHistoricoRepository
     */
    private $plataformaChapaHistoricoRepository;

    /**
     * @var RedeSocialHistoricoPlataformaBO
     */
    private $redeSocialHistoricoPlataformaBO;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {

    }

    /**
     * Salva os dados da Plataforma da chapa no PlataformaChapaHistorico
     * @param ChapaEleicao $chapaEleicao
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function salvar(ChapaEleicao $chapaEleicao)
    {
        $plataformaChapaHistorico = PlataformaChapaHistorico::newInstance([
            'descricaoPlataforma'=> $chapaEleicao->getDescricaoPlataforma()
        ]);

        $profissionalInclusao = $chapaEleicao->getProfissionalInclusaoPlataforma();
        if(empty($chapaEleicao->getUsuarioInclusaoPlataforma()) && empty($profissionalInclusao)) {
            $profissionalInclusao = Profissional::newInstance([
               'id'=> $chapaEleicao->getIdProfissionalInclusao()
            ]);
        }

        $plataformaChapaHistorico->setChapaEleicao($chapaEleicao);
        $plataformaChapaHistorico->setProfissionalInclusaoPlataforma($profissionalInclusao);
        $plataformaChapaHistorico->setUsuarioInclusaoPlataforma($chapaEleicao->getUsuarioInclusaoPlataforma());
        $this->getPlataformaChapaHistoricoRepository()->persist($plataformaChapaHistorico);

        $this->getRedeSocialHistoricoPlataformaBO()->salvar($plataformaChapaHistorico, $chapaEleicao->getRedesSociaisChapa());
    }

    /**
     * Prepara a Plataforma ChapaHistoricoRepository
     * @return PlataformaChapaHistoricoRepository
     */
    private function getPlataformaChapaHistoricoRepository()
    {
        if(empty($this->plataformaChapaHistoricoRepository)){
            $this->plataformaChapaHistoricoRepository = $this->getRepository(PlataformaChapaHistorico::class);
        }
        return $this->plataformaChapaHistoricoRepository;
    }

    /**
     * Prepara a RedeSociais do Historico PlataformaBO
     * @return RedeSocialHistoricoPlataformaBO
     */
    public function getRedeSocialHistoricoPlataformaBO()
    {
        if(empty($this->redeSocialHistoricoPlataformaBO)){
            $this->redeSocialHistoricoPlataformaBO = app()->make(RedeSocialHistoricoPlataformaBO::class);
        }

        return $this->redeSocialHistoricoPlataformaBO;
    }

    /**
     * @param $idChapa
     * @return mixed
     */
    public function getRetificacoesPlataforma($idChapa)
    {
        $retificacoes =  $this->getPlataformaChapaHistoricoRepository()->getPorChapaEleicao($idChapa);
        foreach ($retificacoes as $sequencia => $plataformaChapaHistoricoTO) {
            $plataformaChapaHistoricoTO->setSequencia($sequencia+1);
        }
        return $retificacoes;
    }



}
