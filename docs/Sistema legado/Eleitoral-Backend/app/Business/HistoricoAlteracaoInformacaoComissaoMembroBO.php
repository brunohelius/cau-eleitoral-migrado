<?php
/*
 * HistoricoAlteracaoInformacaoComissaoMembroBO.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\Entity;
use App\Entities\HistoricoAlteracaoInformacaoComissaoMembro;
use App\Repository\HistoricoAlteracaoInformacaoComissaoMembroRepository;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade
 * 'HistoricoAlteracaoInformacaoComissaoMembro'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class HistoricoAlteracaoInformacaoComissaoMembroBO extends AbstractBO
{

    /**
     * @var HistoricoAlteracaoInformacaoComissaoMembroRepository
     */
    private $historicoAlteracaoInformacaoComissaoRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->historicoAlteracaoInformacaoComissaoRepository = $this->getRepository(
            HistoricoAlteracaoInformacaoComissaoMembro::class
        );
    }

    /**
     * Salva um novo registro dos dados que foram atualizados para o histórico.
     *
     * @param HistoricoAlteracaoInformacaoComissaoMembro $historicoAlteracaoInformacaoComissaoMembro
     * @return Entity|array|bool|ObjectManagerAware|object
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvar(HistoricoAlteracaoInformacaoComissaoMembro $historicoAlteracaoInformacaoComissaoMembro)
    {
        return $this->historicoAlteracaoInformacaoComissaoRepository->persist(
            $historicoAlteracaoInformacaoComissaoMembro
        );
    }

}
