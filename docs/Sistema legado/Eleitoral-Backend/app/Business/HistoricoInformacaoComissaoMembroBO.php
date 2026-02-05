<?php
/*
 * HistoricoInformacaoComissaoMembroBO.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\Entity;
use App\Entities\HistoricoInformacaoComissaoMembro;
use App\Entities\InformacaoComissaoMembro;
use App\Exceptions\NegocioException;
use App\Repository\HistoricoInformacaoComissaoMembroRepository;
use App\Service\CorporativoService;
use App\Util\Utils;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade
 * 'HistoricoInformacaoComissaoMembro'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class HistoricoInformacaoComissaoMembroBO extends AbstractBO
{
    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var HistoricoInformacaoComissaoMembroRepository
     */
    private $histInformacaoComissaoMembroRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->corporativoService = app()->make(CorporativoService::class);
        $this->histInformacaoComissaoMembroRepository = $this->getRepository(
            HistoricoInformacaoComissaoMembro::class
        );
    }

    /**
     * Método responsável por salvar um novo histórico de comissão membro.
     *
     * @param HistoricoInformacaoComissaoMembro $historicoInformacaoComissaoMembro
     * @return Entity|array|bool|ObjectManagerAware|object
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvar(HistoricoInformacaoComissaoMembro $historicoInformacaoComissaoMembro)
    {
        return $this->histInformacaoComissaoMembroRepository->persist($historicoInformacaoComissaoMembro);
    }

    /**
     * Retorna o objeto HistoricoCalendario construído para salvar o histórico
     *
     * @param InformacaoComissaoMembro $informacaoComissaoMembro
     * @param $idUsuario
     * @param int $acao
     * @return HistoricoInformacaoComissaoMembro
     * @throws Exception
     */
    public function criarHistorico(InformacaoComissaoMembro $informacaoComissaoMembro, $idUsuario, $acao = Constants::ACAO_HISTORICO_INFORMACAO_COMISSAO_INSERIR_MEMBROS, $justificativa = null)
    {
        $historico = HistoricoInformacaoComissaoMembro::newInstance();
        $historico->setResponsavel($idUsuario);
        $historico->setAcao($acao);
        $historico->setInformacaoComissaoMembro($informacaoComissaoMembro);
        $historico->setDataHistorico(Utils::getData());
        $historico->setJustificativa($justificativa);
        $historico->setHistComissao(false);

        return $historico;
    }

    /**
     * Retorna os dados de Histórico de Informação de Comissão de Membros pelo id da Informação
     *
     * @param $idInformacaoComissao
     * @return array|null
     * @throws NegocioException
     */
    public function getPorInformacaoComissaoMembro($idInformacaoComissao)
    {
        $historicos = $this->histInformacaoComissaoMembroRepository->getPorInformacaoComissaoMembro($idInformacaoComissao);

        foreach ($historicos as $historico) {
            $historico->setDescricaoAcao(Constants::$acoesHistoricoInformacaoComissaoMembro[$historico->getAcao()]);
            $historico->setDadosResponsavel(
                $this->corporativoService->getUsuarioPorId($historico->getResponsavel())
            );
        }

        return $historicos;
    }
}
