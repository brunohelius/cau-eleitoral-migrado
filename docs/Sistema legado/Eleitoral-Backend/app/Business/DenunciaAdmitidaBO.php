<?php

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoDenunciaAdmissibilidade;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Denuncia;
use App\Entities\DenunciaAdmissibilidade;
use App\Entities\DenunciaSituacao;
use App\Entities\EncaminhamentoDenuncia;
use App\Entities\MembroComissao;
use App\Entities\SituacaoDenuncia;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\UsuarioFactory;
use App\Jobs\EnviarEmailDenunciaAdmitidaInserirRelatorJob;
use App\Jobs\EnviarEmailJulgamentoAdmissibilidadeJob;
use App\Mail\DenunciaAdmitidaMail;
use App\Repository\DenunciaAdmissibilidadeRepository;
use App\Repository\DenunciaRepository;
use App\Repository\DenunciaSituacaoRepository;
use App\Repository\EncaminhamentoDenunciaRepository;
use App\Repository\MembroComissaoRepository;
use App\Repository\SituacaoDenunciaRepository;
use App\Service\ArquivoService;
use App\To\EmailDenunciaAdmitidaTO;
use App\To\EmailJulgamentoAdmissibilidadeTO;
use App\Util\Email;
use App\Util\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Class DenunciaAdmitidaBO
 * @package App\Business
 */
class DenunciaAdmitidaBO extends AbstractBO
{

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var DenunciaBO
     */
    private $denunciaBO;

    /**
     * @var SituacaoDenuncia
     */
    private $situacaoAtual;

    /**
     * @var UsuarioFactory
     */
    private $usuarioFactory;

    /**
     * @var MembroComissaoRepository
     */
    private $membroComissaoRepository;

    /**
     * @var DenunciaAdmissibilidadeRepository
     */
    private $denunciaAdmissibilidadeRepository;

    /**
     * @var EncaminhamentoDenunciaRepository
     */
    private $encaminhamentoDenunciaRepository;

    /**
     * @param $denuncia
     * @return bool
     */
    public function possoInserir($denuncia)
    {
        /**
         * @var Denuncia|null $denuncia
         */
        if (!$denuncia instanceof Denuncia) {
            $denuncia = $this->getDenunciaRepository()->find($denuncia);
        }
        $situacao = $this->getSituacaoRepository()->getSituacaoAtualDenuncia($denuncia);
        $filial = $this->getDenunciaBO()->verificaDenunciaIdFilialIES($denuncia);
        return $denuncia &&
            $denuncia->getStatus() === Constants::STATUS_DENUNCIA_INADMITIDA &&
            $situacao->getId() === Constants::SITUACAO_DENUNCIA_AGUARDANDO_RELATOR &&
            $this->isCoordenador($this->getUsuarioFactory()->getUsuarioLogado()->idProfissional, $filial) &&
            !$this->existeAdmissao($denuncia);
    }


    /**
     * @param $denuncia
     * @return bool
     */
    public function existeAdmissao($denuncia)
    {
        return $this->getDenunciaAdmissibilidadeRepository()->existeAdmissao($denuncia);
    }

    /**
     * @param $idDenuncia
     * @throws NegocioException
     */
    public function inserirRelator($idDenuncia)
    {
        $request = $this->getRequest();
        /**
         * @var Denuncia $denuncia
         */
        $denuncia = $this->getDenunciaRepository()->find($idDenuncia);
        if (!$this->possoInserir($denuncia)) {
            throw new NegocioException(Message::MSG_NAO_POSSO_INSERIR_RELATOR);
        }
        /**
         * @var MembroComissao $membroComissao
         */
        $membroComissao = $this->getMembroComissaoRepository()->find($request->get('relator'));
        if (!$this->isMembroComissao($denuncia, $membroComissao)) {
            throw new NegocioException(Message::MSG_MEMBRO_COMISSAO_INVALIDO);
        }

        $this->beginTransaction();
        try {
            $denunciaAdmitida = $this->createDenunciaAdmitida($denuncia, $membroComissao);
            $this->addArquivos($denunciaAdmitida);
            $this->updateDenuncia($denuncia);
            $this->gerarHistorico($denuncia);
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        $this->commitTransaction();

        if (!empty($denunciaAdmitida)) {
            Utils::executarJOB(new EnviarEmailDenunciaAdmitidaInserirRelatorJob($denunciaAdmitida->getId()));
        }
    }

    /**
     * Retorna o membro da comissão coordenador
     *
     * @return MembroComissao
     * @throws \Exception
     */
    public function getCoordenador()
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();

        return $this->getMembroComissaoRepository()->getPorCalendarioAndProfissional(
            $eleicaoVigente->getCalendario()->getId(),
            $usuario->idProfissional
        );
    }

    /**
     * @param $idCauUf
     * @param $idProfissional
     * @return bool
     */
    private function isCoordenador($idProfissional, $idCauUf)
    {
        $isCoordenadorCE = $this->getMembroComissaoRepository()->isCoordenador($idProfissional, $idCauUf);
        $isCoordenadorCEN = $this->getMembroComissaoRepository()->isCoordenador($idProfissional, Constants::COMISSAO_MEMBRO_CAU_BR_ID);
        return $isCoordenadorCE || $isCoordenadorCEN;
    }

    /**
     * @param $idDenuncia
     * @return MembroComissao[]|array
     * @throws NegocioException
     */
    public function getMembrosComissao($idDenuncia)
    {
        /**
         * @var Denuncia $denuncia
         */
        $denuncia = $this->getDenunciaRepository()->find($idDenuncia);

        /**
         * @var EncaminhamentoDenuncia $impedimento
         */
        $impedimento = $this->getEncaminhaDenuciaRepository()->findBy(
            array(
                'tipoEncaminhamento'=> Constants::TIPO_ENCAMINHAMENTO_IMPEDIMENTO_SUSPEICAO,
                'denuncia' => $idDenuncia
            ));

        if (!$denuncia) {
            throw new NegocioException(Message::MSG_NAO_POSSO_INSERIR_RELATOR);
        }


        $filial = $this->getDenunciaBO()->verificaDenunciaIdFilialIES($denuncia);

        if ($denuncia->getTipoDenuncia()->getId() === Constants::TIPO_DENUNCIA_MEMBRO_COMISSAO) {
            return $this->getMembroComissaoRepository()->getMembrosAtivosPorIdCauUf(
                $filial,
                [$denuncia->getDenunciaMembroComissao()->getMembroComissao()->getPessoa()]
            );
        }

        $rsRelator = $this->getMembroComissaoRepository()->getMembrosAtivosPorIdCauUf($filial);

        $relator = [];
        //Montando arrays de relatores
        foreach ($rsRelator as $relatores)
        {
            $relator[$relatores->getId()]['nome']     = $relatores->getProfissionalEntity()->getNome();
            $relator[$relatores->getId()]['registro'] = $relatores->getProfissionalEntity()->getRegistroNacional();
            $relator[$relatores->getId()]['email']    = $relatores->getProfissionalEntity()->getPessoa()->getEmail();
        }

        $memRelator = array_unique($relator, SORT_REGULAR);

        //Removendo relatores com impedimento ou suspeição

        $relMembros = [];

        if(!empty($impedimento))
        {
            foreach ($impedimento as $imp)
            {
                if (isset($memRelator[$imp->getMembroComissao()->getId()])) {
                    unset($memRelator[$imp->getMembroComissao()->getId()]);
                }
            }
        }

        $i = 0;
        foreach ($memRelator as $key => $rel)
        {
            $relMembros[$i]['id']       = $key;
            $relMembros[$i]['nome']     = $rel['nome'];
            $relMembros[$i]['registro'] = $rel['registro'];
            $relMembros[$i]['email']    = $rel['email'];
            $i++;
        }
        return $relMembros;
    }

    /**
     * @param Denuncia $denuncia
     * @param MembroComissao $membroComissao
     * @return bool
     */
    private function isMembroComissao(Denuncia $denuncia, MembroComissao $membroComissao)
    {
        $filial = $this->getDenunciaBO()->verificaDenunciaIdFilialIES($denuncia);
        if ($denuncia->getTipoDenuncia()->getDescricao() === Constants::TIPO_DENUNCIA_MEMBRO_COMISSAO) {
            return $this->getMembroComissaoRepository()->isMembroAtivoIdCauUf(
                $membroComissao,
                $filial,
                [$denuncia->getDenunciaMembroComissao()->getMembroComissao()->getPessoa()]
            );
        }
        return $this->getMembroComissaoRepository()->isMembroAtivoIdCauUf(
            $membroComissao,
            $filial
        );
    }

    /**
     * @param Denuncia $denuncia
     * @param MembroComissao $membroComissao
     * @return DenunciaAdmissibilidade
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function createDenunciaAdmitida(Denuncia $denuncia, MembroComissao $membroComissao)
    {
        $coordenador = $this->getCoordenador();

        $denunciaAdmitida = (new DenunciaAdmissibilidade())
            ->setDataAdmissao(Carbon::now())
            ->setDescricaoDespacho($this->getRequest()->get('despacho'))
            ->setDenuncia($denuncia)
            ->setMembroComissao($membroComissao)
            ->setCoordenador($coordenador);

        $this->getDenunciaAdmissibilidadeRepository()->persist($denunciaAdmitida, true);
        return $denunciaAdmitida;
    }


    private function addArquivos(DenunciaAdmissibilidade $denunciaAdmitida)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenunciaAdmitida($denunciaAdmitida->getId());
        foreach ($this->getRequest()->allFiles()['arquivos'] ?? [] as $arquivo) {
            /**
             * @var UploadedFile $arquivo
             */
            $arquivo = $arquivo['arquivo'];
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getClientOriginalName(),
                Constants::PREFIXO_DOC_DENUNCIA_ADMITIDA
            );
            $this->getArquivoService()->salvar($caminho, $nomeArquivoFisico, $arquivo);
            $arquivoDenunciaAdmitida = (new ArquivoDenunciaAdmissibilidade())
                ->setNmArquivo($arquivo->getClientOriginalName())
                ->setNmFisicoArquivo($nomeArquivoFisico);
            $denunciaAdmitida->addArquivo($arquivoDenunciaAdmitida);
        }
        $this->getDenunciaAdmissibilidadeRepository()->persist($denunciaAdmitida, true);
    }

    /**
     * @param Denuncia $denuncia
     * @throws \Doctrine\ORM\ORMException
     */
    private function updateDenuncia(Denuncia $denuncia)
    {
        $denuncia->setStatus(Constants::STATUS_DENUNCIA_ADMITIDA);
        $this->createSituacao($denuncia);
        $this->getDenunciaRepository()->persist($denuncia, true);
    }

    /**
     * @param Denuncia $denuncia
     * @throws \Doctrine\ORM\ORMException
     */
    private function createSituacao(Denuncia $denuncia)
    {
        /**
         * @var SituacaoDenuncia $situacaoEmRelatoria
         */
        $idSituacao = Constants::STATUS_DENUNCIA_AGUARDANDO_DEFESA;
        if($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_OUTROS) {
            $idSituacao = Constants::SITUACAO_DENUNCIA_EM_RELATORIA;
        }
        $this->situacaoAtual = $this->getSituacaoRepository()->find($idSituacao);


        $situacao = (new DenunciaSituacao())
            ->setDenuncia($denuncia)
            ->setSituacaoDenuncia($this->situacaoAtual)
            ->setData(Carbon::now());

        $this->getDenunciaSituacaoRepository()->persist($situacao, true);
    }

    /**
     * @param Denuncia $denuncia
     * @throws \Exception
     */
    private function gerarHistorico(Denuncia $denuncia)
    {
        $historicoDenuncia = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
            'Indicação do Relator');

        $this->getHistoricoDenunciaBO()->salvar($historicoDenuncia);
    }

    public function gerarEEnviarEmail(int $idDenunciaAdmitida)
    {
        $denunciaAdmitida = $this->getDenunciaAdmissibilidadeRepository()->find($idDenunciaAdmitida);
        $denuncia = $denunciaAdmitida->getDenuncia();
        $params = [
            [
                Constants::EMAIL_INFORMATIVO_NOVO_RELATOR_DENUNCIA,
                [$denunciaAdmitida->getMembroComissao()->getProfissionalEntity()->getPessoa()->getEmail()]
            ],
            [
                Constants::EMAIL_INFORMATIVO_NOVO_RELATOR_COORDENADORES_CE_CEN,
                $this->getDenunciaBO()->getEmailCoordenadores($denuncia)
            ],
            [
                Constants::EMAIL_INFORMATIVO_NOVO_RELATOR_ASSESSORES_CEN_CE,
                $this->getDenunciaBO()->getEmailAssessores($denuncia)
            ],
            [
                Constants::EMAIL_INFORMATIVO_DENUNCIANTE_DENUNCIA_ADMITIDA,
                [$this->getDenunciaBO()->getEmailDenunciante($denuncia)]
            ],
            [
                Constants::EMAIL_INFORMATIVO_DENUNCIADO_DENUNCIA_ADMITIDA,
                $this->getDenunciaBO()->getEmailDenunciados($denuncia)
            ],
        ];
        $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getAtividadeSecundariaPorNiveis(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_DENUNCIA,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_RELATOR,
            true
        );

        $situacaoAtual = $this->getSituacaoRepository()->getSituacaoAtualDenuncia($denuncia);
        $denunciaAdmitidaTO = EmailDenunciaAdmitidaTO::newInstanceFromEntity(
            $denunciaAdmitida,
            $situacaoAtual
        );

        foreach ($params as $param) {
            [$idTipoAtividadeSecundaria, $destinatarios] = $param;
            $this->enviarEmail($idTipoAtividadeSecundaria, $destinatarios, $denunciaAdmitidaTO, $atividadeSecundaria);
        }
    }
    /**
     * @param $idTipoAtividadeSecundaria
     * @param $destinatarios
     * @param EmailJulgamentoAdmissibilidadeTO $julgamentoTo
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function enviarEmail(
        $idTipoAtividadeSecundaria,
        $destinatarios,
        EmailDenunciaAdmitidaTO $denunciaAdmitidaTO,
        AtividadeSecundariaCalendario $atividadeSecundaria
    ) {
        $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
            $atividadeSecundaria->getId(),
            $idTipoAtividadeSecundaria
        );
        if (!empty($emailAtividadeSecundaria) && !empty($destinatarios)) {
            $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
            $emailTO->setDestinatarios(array_unique($destinatarios));
            Email::enviarMail(new DenunciaAdmitidaMail($emailTO, $denunciaAdmitidaTO));
        }
    }

    /**
     * @return AtividadeSecundariaCalendarioBO
     */
    private function getAtividadeSecundariaBO(): AtividadeSecundariaCalendarioBO
    {
        if (empty($this->atividadeSecundariaBO)) {
            $this->atividadeSecundariaBO = new AtividadeSecundariaCalendarioBO();
        }
        return $this->atividadeSecundariaBO;
    }

    /**
     * @return EmailAtividadeSecundariaBO
     */
    private function getEmailAtividadeSecundariaBO()
    {
        if (empty($this->emailAtividadeSecundariaBO)) {
            $this->emailAtividadeSecundariaBO = app(EmailAtividadeSecundariaBO::class);
        }
        return $this->emailAtividadeSecundariaBO;
    }

    /**
     * @return Request
     */
    private function getRequest()
    {
        if (empty($this->request)) {
            $this->request = app(Request::class);
        }
        return $this->request;
    }

    /**
     * @return ArquivoService
     */
    private function getArquivoService()
    {
        if (empty($this->arquivoService)) {
            $this->arquivoService = app(ArquivoService::class);
        }
        return $this->arquivoService;
    }

    /**
     * @return DenunciaRepository|mixed
     */
    private function getDenunciaRepository()
    {
        if(empty($this->denunciaRepository)){
            $this->denunciaRepository = $this->getRepository(Denuncia::class);
        }
        return $this->denunciaRepository;
    }

    /**
     * @return EncaminhamentoDenuncia|mixed
     */
    private function getEncaminhaDenuciaRepository()
    {
        if(empty($this->encaminhamentoDenunciaRepository)){
            $this->encaminhamentoDenunciaRepository = $this->getRepository(EncaminhamentoDenuncia::class);
        }
        return $this->encaminhamentoDenunciaRepository;
    }

    /**
     * @return DenunciaAdmissibilidadeRepository|mixed
     */
    private function getDenunciaAdmissibilidadeRepository()
    {
        if(empty($this->denunciaAdmissibilidadeRepository)){
            $this->denunciaAdmissibilidadeRepository = $this->getRepository(DenunciaAdmissibilidade::class);
        }
        return $this->denunciaAdmissibilidadeRepository;
    }

    /**
     * @return MembroComissaoRepository|mixed
     */
    private function getMembroComissaoRepository()
    {
        if(empty($this->membroComissaoRepository)){
            $this->membroComissaoRepository = $this->getRepository(MembroComissao::class);
        }
        return $this->membroComissaoRepository;
    }

    /**
     * @return SituacaoDenunciaRepository|mixed
     */
    private function getSituacaoRepository()
    {
        if(empty($this->situacaoDenunciaRepository)){
            $this->situacaoDenunciaRepository = $this->getRepository(SituacaoDenuncia::class);
        }
        return $this->situacaoDenunciaRepository;
    }

    /**
     * @return DenunciaSituacaoRepository|mixed
     */
    private function getDenunciaSituacaoRepository()
    {
        if(empty($this->denunciaSituacaoRepository)){
            $this->denunciaSituacaoRepository = $this->getRepository(DenunciaSituacao::class);
        }
        return $this->denunciaSituacaoRepository;
    }

    /**
     * @return HistoricoDenunciaBO
     */
    private function getHistoricoDenunciaBO()
    {
        if(empty($this->historicoDenunciaBO)){
            $this->historicoDenunciaBO = new HistoricoDenunciaBO();
        }
        return $this->historicoDenunciaBO;
    }

    /**
     * @return DenunciaBO
     */
    private function getDenunciaBO()
    {
        if(empty($this->denunciaBO)) {
            $this->denunciaBO = new DenunciaBO();
        }
        return $this->denunciaBO;
    }

    /**
     * Retorna o usuário conforme o padrão lazy Inicialization.
     *
     * @return UsuarioFactory | null
     */
    public function getUsuarioFactory()
    {
        if ($this->usuarioFactory == null) {
            $this->usuarioFactory = app()->make(UsuarioFactory::class);
        }

        return $this->usuarioFactory;
    }

    /**
     * Retorna uma nova instância de 'EleicaoBO'.
     *
     * @return EleicaoBO|mixed
     */
    private function getEleicaoBO()
    {
        if (empty($this->eleicaoBO)) {
            $this->eleicaoBO = app()->make(EleicaoBO::class);
        }

        return $this->eleicaoBO;
    }
}
