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
use App\Entities\ArquivoRespostaDeclaracaoChapa;
use App\Entities\ChapaEleicao;
use App\Entities\ChapaEleicaoStatus;
use App\Entities\HistoricoChapaEleicao;
use App\Entities\MembroChapa;
use App\Entities\StatusChapa;
use App\Entities\UfCalendario;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\PDFFActory;
use App\Jobs\EnviarEmailChapaConfirmadaJob;
use App\Repository\ArquivoRespostaDeclaracaoChapaRepository;
use App\Repository\ChapaEleicaoRepository;
use App\Repository\ChapaEleicaoStatusRepository;
use App\Repository\MembroChapaRepository;
use App\Repository\StatusChapaRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoValidarTO;
use App\To\ChapaQuantidadeMembrosTO;
use App\To\ConfirmarChapaTO;
use App\To\DeclaracaoTO;
use App\To\EleicaoTO;
use App\To\EnvioEmailMembroIncuidoChapaTO;
use App\To\MembroChapaFiltroTO;
use App\To\QuantidadeChapasEstadoTO;
use App\To\StatusChapaEleicaoTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'PedidoSubstituicaoChapa'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class SubstituicaoChapaEleicaoBO extends AbstractBO
{


    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var DeclaracaoAtividadeBO
     */
    private $declaracaoAtividadeBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var UfCalendarioBO
     */
    private $ufCalendarioBO;

    /**
     * @var RedeSocialChapaBO
     */
    private $redeSocialChapaBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var HistoricoChapaEleicaoBO
     */
    private $historicoChapaEleicaoBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var ArquivoRespostaDeclaracaoChapaRepository
     */
    private $arquivoRespostaDeclaracaoChapaRepository;

    /**
     * @var ChapaEleicaoRepository
     */
    private $chapaEleicaoRepository;

    /**
     * @var ChapaEleicaoStatusRepository
     */
    private $chapaEleicaoStatusRepository;

    /**
     * @var ProporcaoConselheiroExtratoBO
     */
    private $proporcaoConselheiroExtratoBO;

    /**
     * @var MembroChapaRepository
     */
    private $membroChapaRepository;

    /**
     * @var StatusChapaRepository
     */
    private $statusChapaRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->chapaEleicaoRepository = $this->getRepository(ChapaEleicao::class);
    }



}
