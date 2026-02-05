<?php

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoJulgamentoAdmissibilidade;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Denuncia;
use App\Entities\DenunciaSituacao;
use App\Entities\JulgamentoAdmissibilidade;
use App\Entities\SituacaoDenuncia;
use App\Entities\TipoJulgamentoAdmissibilidade;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailAlegacaoFinalCadastrarJob;
use App\Jobs\EnviarEmailJulgamentoAdmissibilidadeJob;
use App\Mail\JulgamentoAdmissibilidadeMail;
use App\Repository\ArquivoJulgamentoAdmissibilidadeRepository;
use App\Repository\DenunciaRepository;
use App\Repository\JulgamentoAdmissibilidadeRepository;
use App\Repository\SituacaoDenunciaRepository;
use App\Repository\TipoJulgamentoAdmissibilidadeRepository;
use App\Service\ArquivoService;
use App\To\EmailJulgamentoAdmissibilidadeTO;
use App\Util\Email;
use App\Util\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Class JulgamentoAdmissibilidadeBO
 * @package App\Business
 */
class JulgamentoAdmissibilidadeBO extends AbstractBO
{
    /**
     * @var SituacaoDenuncia
     */
    private $situacaoAtual;

    /**
     * @return bool
     */
    public function possoJulgar($denuncia)
    {
        /**
         * @var Denuncia|null $denuncia
         */
        if (!$denuncia instanceof Denuncia) {
            $denuncia = $this->getDenunciaRepository()->find($denuncia);
        }
        $filial = $this->getDenunciaBO()->verificaDenunciaIdFilialIES($denuncia);
        $situacao = $this->getSituacaoDenunciaRepository()->getSituacaoAtualDenuncia($denuncia);
        return $denuncia &&
            $denuncia->getStatus() === Constants::STATUS_DENUNCIA_INADMITIDA &&
            $situacao->getId() === Constants::SITUACAO_DENUNCIA_EM_JULGAMENTO &&
            (
                $this->getUsuarioFactory()->isCorporativoAssessorCEN() ||
                $this->getUsuarioFactory()->isCorporativoAssessorCeUfPorCauUf($filial)
            ) &&
            !$this->existeJulgamento($denuncia);
    }

    /**
     * @param $idDenuncia
     * @return bool
     */
    public function existeJulgamento($denuncia)
    {
        return $this->getJulgamentoRepository()->existeJulgamento($denuncia);
    }

    public function julgarAdmissibilidade($idDenuncia)
    {
        $request = $this->getRequest();
        /**
         * @var Denuncia $denuncia
         */
        $denuncia = $this->getDenunciaRepository()->find($idDenuncia);
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        if (!$this->possoJulgar($denuncia)) {
            throw new NegocioException(Message::MSG_NAO_POSSO_JULGAR);
        }
        /**
         * @var TipoJulgamentoAdmissibilidade|null $tipo
         */
        $tipo = $this->getTipoJulgamentoRepository()->find($request->get('julgamento'));
        if (!$tipo) {
            throw new NegocioException(Message::MSG_TIPO_JULGAMENTO_ADMISSIBILIDADE_NAO_EXISTE);
        }

        $this->beginTransaction();
        try {
            $julgamento = $this->createJulgamento($denuncia, $tipo, $request->get('descricao'), $usuario);
            $this->addArquivos($julgamento, $request->allFiles());
            $this->updateDenuncia($denuncia, $julgamento);
            $this->gerarHistorico($denuncia);
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        $this->commitTransaction();

        if (!empty($julgamento)) {
            Utils::executarJOB(new EnviarEmailJulgamentoAdmissibilidadeJob($julgamento->getId()));
        }
    }

    /**
     * @param $denuncia
     * @return SituacaoDenuncia
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getSituacaoAtual($denuncia)
    {
        return $this->getDenunciaRepository()->getSituacaoAtualDenuncia(
            $this->getDenunciaRepository()->getMaxSituacaoDenuncia($denuncia->getId())['id']
        );
    }

    /**
     * @param Denuncia $denuncia
     * @param TipoJulgamentoAdmissibilidade $tipo
     * @param $descricao
     * @return JulgamentoAdmissibilidade
     */
    private function createJulgamento(Denuncia $denuncia, TipoJulgamentoAdmissibilidade $tipo, $descricao, $usuario)
    {
        $julgamento = (new JulgamentoAdmissibilidade())
            ->setDenuncia($denuncia)
            ->setDescricao($descricao)
            ->setTipoJulgamento($tipo)
            ->setCriadoPor($this->getUsuarioFactory()->getUsuarioLogado()->id)
            ->setDataCriacao(Carbon::now());

        $this->getJulgamentoRepository()->persist($julgamento, true);
        return $julgamento;
    }

    /**
     * @param JulgamentoAdmissibilidade $julgamento
     * @param $upload
     * @throws \Doctrine\ORM\ORMException
     */
    private function addArquivos(JulgamentoAdmissibilidade $julgamento, $upload)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoAdmissibilidade($julgamento->getId());
        foreach ($upload['arquivos'] ?? [] as $arquivo) {
            /**
             * @var UploadedFile $arquivo
             */
            $arquivo = $arquivo['arquivo'];
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getClientOriginalName(),
                Constants::PREFIX_ARQ_JULGAMENTO_ADMISSIBILIDADE
            );
            $this->getArquivoService()->salvar($caminho, $nomeArquivoFisico, $arquivo);
            $arquivoJulgamento = (new ArquivoJulgamentoAdmissibilidade())
                ->setNome($arquivo->getClientOriginalName())
                ->setNomeFisico($nomeArquivoFisico);
            $julgamento->addArquivo($arquivoJulgamento);
        }
        $this->getArquivoJulgamentoRepository()->persist($julgamento, true);
    }

    /**
     * @param $idArquivo
     * @return \App\To\ArquivoTO
     * @throws NegocioException
     */
    public function getArquivo($idArquivo)
    {
        /**
         * @var ArquivoJulgamentoAdmissibilidade|null $arquivo
         */
        $arquivo = $this->getArquivoJulgamentoRepository()->find($idArquivo);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoAdmissibilidade($arquivo->getJulgamento()->getId());
        return $this->getArquivoService()->getArquivo($caminho, $arquivo->getNomeFisico(), $arquivo->getNome());
    }

    /**
     * @param Denuncia $denuncia
     * @param JulgamentoAdmissibilidade $julgamento
     * @throws \Doctrine\ORM\ORMException
     */
    private function updateDenuncia(Denuncia $denuncia, JulgamentoAdmissibilidade $julgamento)
    {
        switch ($julgamento->getTipoJulgamento()->getId()) {
            case Constants::TIPO_JULGAMENTO_ADMISSIBILIDADE_PROVIMENTO:
                $situacao = Constants::SITUACAO_DENUNCIA_AGUARDANDO_RELATOR;
                break;
            case Constants::TIPO_JULGAMENTO_ADMISSIBILIDADE_IMPROVIMENTO:
                $situacao = Constants::SITUACAO_DENUNCIA_EM_RECURSO;
                break;
        }
        $this->createSituacao($denuncia, $situacao ?? 0);
    }

    /**
     * @param Denuncia $denuncia
     * @param int $idSituacao
     * @throws \Doctrine\ORM\ORMException
     */
    private function createSituacao(Denuncia $denuncia, $idSituacao)
    {
        /**
         * @var SituacaoDenuncia $situacaoEmRelatoria
         */
        $this->situacaoAtual = $this->getSituacaoDenunciaRepository()->find($idSituacao);

        $situacao = (new DenunciaSituacao())
            ->setDenuncia($denuncia)
            ->setSituacaoDenuncia($this->situacaoAtual)
            ->setData(Carbon::now());

        $this->getSituacaoDenunciaRepository()->persist($situacao, true);
    }

    /**
     * @param Denuncia $denuncia
     * @throws \Exception
     */
    private function gerarHistorico(Denuncia $denuncia)
    {
        $historicoDenuncia = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
            'Julgamento de admissibilidade');

        $this->getHistoricoDenunciaBO()->salvar($historicoDenuncia);
    }

    /**
     * @param JulgamentoAdmissibilidade $julgamento
     * @throws NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function gerarEEnviarEmail(int $idJulgamentoAdmissibilidade)
    {
        $params = [];
        $julgamento = $this->getJulgamentoRepository()->find($idJulgamentoAdmissibilidade);
        $denuncia = $julgamento->getDenuncia();
        switch ($julgamento->getTipoJulgamento()->getId()) {
            case Constants::TIPO_JULGAMENTO_ADMISSIBILIDADE_PROVIMENTO:
                $params = [
                    [
                        Constants::EMAIL_INFORMATIVO_COORDENADOR_CE_DENUNCIA_JULGADA,
                        $this->getDenunciaBO()->getEmailCoordenadores($denuncia)
                    ],
                    [
                        Constants::EMAIL_INFORMATIVO_ASSESSOR_ADMISSIBILIDADE_DENUNCIA_JULGADA,
                        $this->getDenunciaBO()->getEmailAssessores($denuncia)
                    ],
                    [
                        Constants::EMAIL_INFORMATIVO_DENUNCIANTE_DENUNCIA_JULGADA_PROVIDA,
                        [$this->getDenunciaBO()->getEmailDenunciante($denuncia)]
                    ],
                    [
                        Constants::EMAIL_INFORMATIVO_DENUNCIADO_DENUNCIA_JULGADA_PROVIDA,
                        $this->getDenunciaBO()->getEmailDenunciados($denuncia)
                    ],
                ];
                break;
            case Constants::TIPO_JULGAMENTO_ADMISSIBILIDADE_IMPROVIMENTO:
                $params = [
                    [
                        Constants::EMAIL_INFORMATIVO_COORDENADOR_DENUNCIA_JULGADA_IMPROVIDA,
                        $this->getDenunciaBO()->getEmailCoordenadores($denuncia)
                    ],
                    [
                        Constants::EMAIL_INFORMATIVO_ASSESSOR_DENUNCIA_JULGADA_IMPROVIDA,
                        $this->getDenunciaBO()->getEmailAssessores($denuncia)
                    ],
                    [
                        Constants::EMAIL_INFORMATIVO_QUEM_CADASTROU_DENUNCIA_JULGADA_IMPROVIDA,
                        [$this->getDenunciaBO()->getEmailDenunciante($denuncia)]
                    ],
                ];
                break;
        }
        $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getAtividadeSecundariaPorNiveis(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_DENUNCIA,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_JULGAMENTO_ADMISSIBILIDADE,
            true
        );

        $situacaoAtual = $this->getSituacaoDenunciaRepository()->getSituacaoAtualDenuncia($denuncia);
        $julgamentoTo = EmailJulgamentoAdmissibilidadeTO::newInstanceFromEntity(
            $julgamento, $situacaoAtual
        );

        foreach ($params as $param) {
            [$idTipoAtividadeSecundaria, $destinatarios] = $param;
            $this->enviarEmail($idTipoAtividadeSecundaria, $destinatarios, $julgamentoTo, $atividadeSecundaria);
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
        EmailJulgamentoAdmissibilidadeTO $julgamentoTo,
        AtividadeSecundariaCalendario $atividadeSecundaria
    ) {
        $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
            $atividadeSecundaria->getId(),
            $idTipoAtividadeSecundaria
        );
        if (!empty($emailAtividadeSecundaria) && !empty($destinatarios)) {
            $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
            $emailTO->setDestinatarios(array_unique($destinatarios));
            Email::enviarMail(new JulgamentoAdmissibilidadeMail($emailTO, $julgamentoTo));
        }
    }

    /**
     * Rotina de notificação de denúncia sem relator
     *
     * @return void
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function alertaDenunciaSemRelator()
    {
        $denuncias = $this->getDenunciaBO()->iteratorDenunciasAguardandoRelatores();

        $atividadeSecundaria = $this->getAtividadeSecundariaBO()->getAtividadeSecundariaPorNiveis(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_DENUNCIA,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_JULGAMENTO_ADMISSIBILIDADE
        );

        $situacao = $this->getSituacaoDenunciaRepository()->find(Constants::SITUACAO_DENUNCIA_AGUARDANDO_RELATOR);

        $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
            $atividadeSecundaria->getId(),
            Constants::EMAIL_INFORMATIVO_ASSESSOR_COORDENADOR_24H_SEM_RELATOR
        );

        if ($emailAtividadeSecundaria) {
            $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
            foreach ($denuncias as $items) {
                $denuncia = $items[0];
                $julgamento = $denuncia->getJulgamentoAdmissibilidade();
                $julgamentoTo = EmailJulgamentoAdmissibilidadeTO::newInstanceFromEntity(
                    $julgamento,
                    $situacao
                );

                $destinatarios = $this->getDenunciaBO()->getEmailAssessores($denuncia);
                if ($destinatarios) {
                    $emailTO->setDestinatarios(array_unique($destinatarios));
                    Email::enviarMail(new JulgamentoAdmissibilidadeMail($emailTO, $julgamentoTo));
                }
            }
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
     * @return DenunciaBO
     */
    private function getDenunciaBO() {
        if (empty($this->denunciaBO)) {
            $this->denunciaBO = app(DenunciaBO::class);
        }
        return $this->denunciaBO;
    }

    /**
     * @return HistoricoDenunciaBO
     */
    private function getHistoricoDenunciaBO() {
        if (empty($this->historicoDenunciaBO)) {
            $this->historicoDenunciaBO = app(HistoricoDenunciaBO::class);
        }
        return $this->historicoDenunciaBO;
    }

    /**
     * @return Request
     */
    private function getRequest() {
        if (empty($this->request)) {
            $this->request = app(Request::class);
        }
        return $this->request;
    }

    /**
     * @return ArquivoService
     */
    private function getArquivoService() {
        if (empty($this->arquivoService)) {
            $this->arquivoService = app(ArquivoService::class);
        }
        return $this->arquivoService;
    }

    /**
     * @return DenunciaRepository|mixed
     */
    private function getDenunciaRepository() {
        if (empty($this->denunciaRepository)) {
            $this->denunciaRepository = $this->getRepository(Denuncia::class);
        }
        return $this->denunciaRepository;
    }

    /**
     * @return JulgamentoAdmissibilidadeRepository|mixed
     */
    private function getJulgamentoRepository() {
        if (empty($this->julgamentoRepository)) {
            $this->julgamentoRepository = $this->getRepository(JulgamentoAdmissibilidade::class);
        }
        return $this->julgamentoRepository;
    }

    /**
     * @return TipoJulgamentoAdmissibilidadeRepository|mixed
     */
    private function getTipoJulgamentoRepository() {
        if (empty($this->tipoJulgamentoRepository)) {
            $this->tipoJulgamentoRepository = $this->getRepository(TipoJulgamentoAdmissibilidade::class);
        }
        return $this->tipoJulgamentoRepository;
    }

    /**
     * @return SituacaoDenunciaRepository|mixed
     */
    private function getSituacaoDenunciaRepository() {
        if (empty($this->situacaoDenunciaRepository)) {
            $this->situacaoDenunciaRepository = $this->getRepository(SituacaoDenuncia::class);
        }
        return $this->situacaoDenunciaRepository;
    }

    /**
     * @return ArquivoJulgamentoAdmissibilidadeRepository|mixed
     */
    private function getArquivoJulgamentoRepository() {
        if (empty($this->arquivoJulgamentoRepository)) {
            $this->arquivoJulgamentoRepository = $this->getRepository(ArquivoJulgamentoAdmissibilidade::class);
        }
        return $this->arquivoJulgamentoRepository;
    }
}
