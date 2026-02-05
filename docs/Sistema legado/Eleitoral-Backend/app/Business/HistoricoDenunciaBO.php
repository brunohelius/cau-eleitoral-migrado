<?php
/*
 * HistoricoDenunciaBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\Denuncia;
use App\Util\Utils;
use App\Entities\HistoricoDenuncia;
use App\Repository\HistoricoDenunciaRepository;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'HistoricoDenuncia'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class HistoricoDenunciaBO extends AbstractBO
{
    /**
     * @var HistoricoDenunciaRepository
     */
    private $historicoDenunciaRepository;

    /**
     * Construtor da Classe.
     */
    public function __construct()
    {
        $this->historicoDenunciaRepository = $this->getRepository(HistoricoDenuncia::class);
    }

    /**
     * Salva o histórico.
     *
     * @param HistoricoDenuncia $historico
     * @throws \Exception
     */
    public function salvar(HistoricoDenuncia $historico)
    {
        try{
            $historico->setDataHistorico(Utils::getData());
            $this->historicoDenunciaRepository->persist($historico);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Retorna o objeto HistoricoDenuncia construído para salvar o histórico
     *
     * @param Denuncia $denuncia
     * @param $acao
     *
     * @return HistoricoDenuncia
     * @throws \Exception
     */
    public function criarHistorico($denuncia, $acao)
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();

        $historicoDenuncia = HistoricoDenuncia::newInstance();
        $historicoDenuncia->setDenuncia($denuncia);
        $historicoDenuncia->setResponsavel($usuarioLogado->id ?? null);
        $historicoDenuncia->setDescricaoAcao($acao);
        $historicoDenuncia->setDataHistorico(Utils::getData());

        $origem = Constants::ORIGEM_CORPORATIVO;
        if (!empty($usuarioLogado->idProfissional)) {
            $origem = Constants::ORIGEM_PROFISSIONAL;
        }
        $historicoDenuncia->setOrigem($origem);

        return $historicoDenuncia;
    }

    /**
     * Buscar Usuário que admitiu a denuncia.
     * @param $idDenuncia
     * @return \App\Entities\Profissional|\App\To\ProfissionalTO|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getProfissionalHistoricoDenuncia($idDenuncia, $acao)
    {
        $historicoAdmissao = $this->historicoDenunciaRepository->findOneBy(['denuncia' => $idDenuncia, 'descricaoAcao' => $acao]);
        if($historicoAdmissao) {
            $profissional = $this->getProfissionalBO()->getPorPessoa($historicoAdmissao->getResponsavel());
            return $this->getProfissionalBO()->getPorId($profissional->getId());
        }
        return null;
    }

    /**
     * Buscar Historico de denúncia por id e ação.
     *
     * @param $idDenuncia
     * @param $acao
     * @return object|null
     */
    public function getHistoricoDenunciaPorDenunciaEAcao($idDenuncia, $acao)
    {
        return  $this->historicoDenunciaRepository->findOneBy(['denuncia' => $idDenuncia, 'descricaoAcao' => $acao]);
    }

    /**
     * Buscar todos os Historicos de denúncia por id e ação.
     *
     * @param $idDenuncia
     * @param $acao
     * @return array|null
     */
    public function getTodosHistoricosDenunciaPorDenunciaEAcao($idDenuncia, $acao)
    {
        return  $this->historicoDenunciaRepository->findBy(['denuncia' => $idDenuncia, 'descricaoAcao' => $acao]);
    }

    /**
     * Retorna uma nova instância de 'ProfissionalBO'.
     *
     * @return ProfissionalBO
     */
    private function getProfissionalBO()
    {
        if (empty($this->pessoaBO)) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }

        return $this->profissionalBO;
    }
}
