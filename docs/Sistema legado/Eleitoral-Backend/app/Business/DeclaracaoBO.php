<?php
/*
 * DeclaracaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\Declaracao;
use App\Entities\Entity;
use App\Entities\ItemDeclaracao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\DeclaracaoRepository;
use App\Repository\ItemDeclaracaoRepository;
use App\To\DeclaracaoFiltroTO;
use App\Util\Utils;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'Declaracao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DeclaracaoBO extends AbstractBO
{
    /**
     * @var DeclaracaoRepository
     */
    private $declaracaoRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->declaracaoRepository = $this->getRepository(Declaracao::class);
    }

    /**
     * Retorna uma declaração conforme id informado.
     *
     * @param integer $id
     *
     * @return Declaracao|null
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getDeclaracao($id)
    {
        if ($id == null) {
            throw new NegocioException(Message::VALIDACAO_FILTRO_OBRIGATORIO);
        }

        return $this->declaracaoRepository->getDeclaracao($id);
    }

    /**
     * Retorna um lista de nomes de declarações conforme id do módulo informado.
     *
     * @param integer $idModulo
     *
     * @return array|null
     * @throws NegocioException
     */
    public function getDeclaracoesPorModulo($idModulo)
    {
        if ($idModulo == null) {
            throw new NegocioException(Message::VALIDACAO_FILTRO_OBRIGATORIO);
        }

        return $this->declaracaoRepository->getDeclaracoesPorModulo($idModulo);
    }

    /**
     * Retorna uma lista de instâncias de 'Declaracao' conforme o id do módulo informado ou nome da declaração
     * informada.
     *
     * @param DeclaracaoFiltroTO $filtroTO
     *
     * @return array|null
     * @throws NegocioException
     */
    public function getDeclaracoesPorFiltro(DeclaracaoFiltroTO $filtroTO)
    {
        return $this->declaracaoRepository->getDeclaracoesPorFiltro($filtroTO);
    }

    /**
     * Retorna uma lista de instâncias de 'Declaracao' conforme o id do módulo informado ou nome da declaração
     * informada.
     *
     * @param array $ids
     *
     * @return Declaracao[]|null
     * @throws NegocioException
     */
    public function getListaDeclaracoesFormatadaPorIds($ids)
    {
        $declaracoesFormatada = [];

        $filtroTO = DeclaracaoFiltroTO::newInstance(compact('ids'));
        $declaracoes = $this->declaracaoRepository->getDeclaracoesPorFiltro($filtroTO);

        if (!empty($declaracoes)) {
            /** @var Declaracao $declaracao */
            foreach ($declaracoes as $declaracao) {
                $declaracoesFormatada[$declaracao->getId()] = $declaracao;
            }
        }
        return $declaracoesFormatada;
    }
}
