<?php

namespace App\Business;


use App\Jobs\EnviarEmailRecursoJulgamentoAdmissibilidadeJob;
use App\Service\CalendarioApiService;
use App\To\JulgamentoAdmissibilidadeTO;
use App\To\RecursoJulgamentoAdmissibilidadeTO;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

use App\Exceptions\Message;
use App\Exceptions\NegocioException as NegocioExceptionAlias;

use App\Util\Utils;
use App\Config\Constants;

use App\Entities\Denuncia;
use App\Entities\RecursoJulgamentoAdmissibilidade;
use App\Entities\JulgamentoAdmissibilidade;
use App\Entities\ArquivoRecursoJulgamentoAdmissibilidade;
use App\Entities\MembroComissao;

use App\Repository\DenunciaRepository;
use App\Repository\JulgamentoAdmissibilidadeRepository;
use App\Repository\RecursoJulgamentoAdmissibilidadeRepository;
use App\Repository\MembroComissaoRepository;

use App\Service\CorporativoService;
use App\Service\ArquivoService;


use App\Business\AtividadeSecundariaCalendarioBO;
use App\Business\EmailAtividadeSecundariaBO;
use App\Business\FilialBO;
use App\Business\MembroComissaoBO;
use App\Business\DenunciaMembroChapaBO;


/**
 * Class RecursoJulgamentoAdmissibilidadeBO
 * @package App\Business
 */
class RecursoJulgamentoAdmissibilidadeBO extends AbstractBO
{

    /**
     * @var \App\Service\CalendarioApiService
     */
    private $calendarioApiService;

    public function salvar($request)
    {
        $recurso = $this->montarRequest($request);

        if (!$this->validarPrazoRecurso($recurso)) {
            throw new \Exception('Não foi possível realizar o cadastro porque o prazo de apresentação de recurso encerrou.');
        }

        $this->beginTransaction();
        try {
            $recurso = $this->getRecursoJulgamentoRepository()->persist($recurso, true);
            $this->addArquivos($recurso);
            $this->gerarHistorico($recurso->getJulgamentoAdmissibilidade()->getDenuncia());
            $this->getDenunciaBO()->salvarSituacaoDenuncia($recurso->getJulgamentoAdmissibilidade()->getDenuncia(), Constants::SITUACAO_JULGAMENTEO_RECURSO_ADMISSIBILIDADE);
            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw new \Exception($e->getMessage());
        }

        if (!empty($recurso)) {
            Utils::executarJOB(new EnviarEmailRecursoJulgamentoAdmissibilidadeJob($recurso->getId()));
        }

        return $recurso;
    }

    /**
     * @param int $idRecursoJulgamentoAdmissibilidade
     * @throws NegocioExceptionAlias
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function notificacaoRecurso(int $idRecursoJulgamentoAdmissibilidade)
    {
        $recurso = $this->getRecursoJulgamentoRepository()->find($idRecursoJulgamentoAdmissibilidade);
        
        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getAtividadeSecundariaAtivaPorNivel(4, 14);

        $templateEmail = 'emailRecursoJulgamentoAdmissibilidade.html';

        $arrCampos = $this->montarCamposEmails($recurso);

        $denuncia = $recurso->getJulgamentoAdmissibilidade()->getDenuncia();

        //Responsave cadastro denuncia
        $email = $denuncia->getPessoa()->getEmail();
        if (!empty($email)) {
            $listaEmails = [$email];
            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_USUARIO_CADASTRO_DENUNCIA, $templateEmail, $arrCampos);
        }


        // Acessor CEN
        $listaAcessorCEN = $this->getCorporativoService()->getUsuariosAssessoresCEN();
        if(!empty($listaAcessorCEN)){
            $listaEmails = $this->montaEmailUsuarioTO($listaAcessorCEN);
            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_ACESSOR_CEN, $templateEmail, $arrCampos);
        }

        //Coordenadores CEN
        $coodenadoresCEN = $this->getMembroComissaoRepository()->getCoordenadoresPorFiltro(['idCauUf' => $denuncia->getFilial()->getId()]);
        if(!empty($coodenadoresCEN)){

            $listaEmails = $this->getMembroComissaoBO()->getListEmailsDestinatarios($coodenadoresCEN);
            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_COORDENADORES_CEN, $templateEmail, $arrCampos);
        }

    }

    private function montarCamposEmails(RecursoJulgamentoAdmissibilidade $recurso)
    {
        $denuncia = $recurso->getJulgamentoAdmissibilidade()->getDenuncia();

        $arrCampos = [];
        $arrCampos['{{protocolo}}'] = 'Protocolo: ' . $denuncia->getNumeroSequencial();
        $arrCampos['{{processoEleitoral}}'] = 'Processo Eleitoral: ' . $denuncia->getAtividadeSecundaria()->getAtividadePrincipalCalendario()->getCalendario()->getDataInicioVigencia()->format('Y');
        $arrCampos['{{tipoDenuncia}}'] = 'Tipo de denúncia: ' . $denuncia->getTipoDenuncia()->getDescricao();
        $arrCampos['{{recurso}}'] = $recurso->getDescricao();

        if(empty($recurso->getDescricao())){
            $arrCampos['{{recurso}}'] = 'Não foi apresentada recurso para esta denúncia.';
        }

        $filial = !empty($denuncia->getFilial())
            ? $this->getFilialBO()->getPorId($denuncia->getFilial()->getId())
            : $this->getFilialBO()->getFilialIES();

        switch ( $recurso->getJulgamentoAdmissibilidade()->getDenuncia()->getTipoDenuncia()->getId()){

            case Constants::TIPO_MEMBRO_CHAPA:
                $listaEmails = $this->getDenunciaMembroChapaBO()->getDadosDenunciante($denuncia->getId());
                $nome = $listaEmails ? $listaEmails[0]['nome'] : '';
                $arrCampos['{{nomeDenunciado}}'] = 'Nome do Denunciado: ' . $nome;

                $denunciaChapa = $denuncia->getDenunciaChapa();
                $numeroChapa = '';
                $uf = '';
                if($denunciaChapa != null) {
                    $numeroChapa = $denunciaChapa->getChapaEleicao()->getNumeroChapa();
                    $uf = $filial->getPrefixo();
                }
                $arrCampos['{{nChapa}}'] = 'Nº Chapa: ' . $numeroChapa;
                $arrCampos['{{uf}}']  = 'UF: ' .$uf;


                break;
            case Constants::TIPO_CHAPA :

                $arrCampos['{{nomeDenunciado}}'] = '';

                $denunciaChapa = $denuncia->getDenunciaChapa();
                $numeroChapa = '';
                $uf = '';
                if($denunciaChapa != null) {
                    $numeroChapa = $denunciaChapa->getChapaEleicao()->getNumeroChapa();
                    $uf = $filial->getPrefixo();
                }

                $arrCampos['{{nChapa}}'] = 'Nº Chapa: ' . $numeroChapa;
                $arrCampos['{{uf}}']  = 'UF: ' .$uf;
                break;
            case Constants::TIPO_MEMBRO_COMISSAO :

                $listaEmails = $this->getDenunciaMembroChapaBO()->getDadosDenunciante($denuncia->getId());
                $nome = $listaEmails ? $listaEmails[0]['nome'] : '';
                $arrCampos['{{nomeDenunciado}}'] = 'Nome do Denunciado: ' . $nome;
                $arrCampos['{{nChapa}}'] = '';


                $arrCampos['{{uf}}'] = 'UF: ' . $filial->getPrefixo();
                break;
            case Constants::TIPO_OUTROS :
                $arrCampos['{{nomeDenunciado}}'] = '';
                $arrCampos['{{nChapa}}'] = '' ;
                $arrCampos['{{uf}}'] = 'UF: ' . $filial->getPrefixo();
                break;
        }

        return $arrCampos;
    }

    private function validarPrazoRecurso(RecursoJulgamentoAdmissibilidade $recurso)
    {
        $dataJulgamento = $recurso->getJulgamentoAdmissibilidade()->getDataCriacao();
        $dataAtual = new \DateTime('midnight');

        //ADD 3 DIAS DE PRAZO
        $feriados = $this->getCalendarioApiService()
            ->getFeriadosNacionais(Utils::getAnoData());
        $dataJulgamento = Utils::adicionarDiasUteisData($dataJulgamento, 1, $feriados );
        $dataJulgamento->add(new \DateInterval('P2D'));
        return $dataAtual < $dataJulgamento;
    }

    public function verificarPrazoRecurso($request)
    {
        $recurso = $this->montarRequest($request);
        $recursoCadastrado = $this->getRecursoJulgamentoRepository()->findOneBy(['julgamentoAdmissibilidade' => $recurso->getJulgamentoAdmissibilidade()]);

        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();
        $denunciaUsuarioLogado = $usuario->idProfissional == $recurso->getJulgamentoAdmissibilidade()
                ->getDenuncia()
                ->getPessoa()
                ->getProfissional()
                ->getId();

        return [
            'parazoRecurso' =>  $this->validarPrazoRecurso($recurso),
            'recursoId' => empty($recursoCadastrado) ? 0 : $recursoCadastrado->getId(),
            'denunciaUsuarioLogado' => $denunciaUsuarioLogado
        ];
    }

    /**
     * Valida o prazo de um recurso de julgamento de admissibilidade atraves de um Julgamento de Admissibilidade
     * @param JulgamentoAdmissibilidadeTO $julgamentoAdmissibilidadeTO
     * @return bool
     * @throws \Exception
     */
    public function validaPrazoRecursoInformado(JulgamentoAdmissibilidadeTO $julgamentoAdmissibilidadeTO) {
        $dataJulgamento = $julgamentoAdmissibilidadeTO->getDataCriacao();
        $dataAtual = new \DateTime('midnight');

        //ADD 3 DIAS DE PRAZO
        $dataJulgamento->add(new \DateInterval('P3D'));
        return $dataAtual < $dataJulgamento;
    }


    public function rotinaValidarPrazoRecursoJulgamento()
    {
        $julgamentos = $this->getRecursoJulgamentoRepository()->getJulgamentoSemRecursoPrazoVencido();

        if ($julgamentos) {
            foreach ($julgamentos as $julgamento) {

                $denuncia = $this->getDenunciaRepository()->find($julgamento['id_denuncia']);

                $historicoDenuncia = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
                    'Prazo de apresentação de recurso transcorrido');

                $this->getHistoricoDenunciaBO()->salvar($historicoDenuncia);

                $this->getDenunciaBO()->salvarSituacaoDenuncia($denuncia, Constants::SITUACAO_DENUNCIA_TRANSITO_JULGADO);
                $this->notificarJulgamentoSemRecurso($julgamento['id_julg_admissibilidade']);
            }

        }
    }

    private function notificarJulgamentoSemRecurso($id_julg_admissibilidade)
    {
        $julgamento = $this->getJulgamentoRepository()->find($id_julg_admissibilidade);
        $recurso = new RecursoJulgamentoAdmissibilidade();
        $recurso->setJulgamentoAdmissibilidade($julgamento);

        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getAtividadeSecundariaAtivaPorNivel(4, 14);

        $templateEmail = 'emailRecursoJulgamentoAdmissibilidade.html';

        $arrCampos = $this->montarCamposEmails($recurso);
        $denuncia = $recurso->getJulgamentoAdmissibilidade()->getDenuncia();
        $filial = $this->getDenunciaBO()->verificaDenunciaIdFilialIES($denuncia);

        //Coordenadores CE
        $coodenadoresCE = $this->getMembroComissaoRepository()->getCoordenadoresPorFiltro(['idCauUf' => $filial]);

        if(!empty($coodenadoresCE)){
            $listaEmails = $this->getMembroComissaoBO()->getListEmailsDestinatarios($coodenadoresCE);
            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_COORDENAROR_CE_ATUA_DENUNCIA, $templateEmail,$arrCampos);
        }


        //Acessor CE
        $listaAcessorCE = $this->getCorporativoService()->getUsuariosAssessoresCE($filial);
        $listaEmailsCE = [];
        if(!empty($listaAcessorCE)){
            $listaEmailsCE = $this->montaEmailUsuarioTO($listaAcessorCE);
        }

        // Acessor CEN
        $listaAcessorCEN = $this->getCorporativoService()->getUsuariosAssessoresCEN();
        $listaEmailsCEN = [];
        if(!empty($listaAcessorCEN)){
            $listaEmailsCEN = $this->montaEmailUsuarioTO($listaAcessorCEN);
        }

        $listaEmails = array_unique(array_merge($listaEmailsCE, $listaEmailsCEN));

        if(!empty($listaEmails)){
            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_ACESSOR_CE_UF_DENUNCIADO, $templateEmail,$arrCampos);
        }

        //Responsave cadastro denuncia
        $email = $denuncia->getPessoa()->getEmail();
        if (!empty($email)) {
            $listaEmails = [$email];
            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_DENUNCIANTE, $templateEmail, $arrCampos);
        }
    }

    private function montaEmailUsuarioTO($arrUsuario)
    {
        $retorno = [];
        foreach ($arrUsuario as $usuarioTO) {
            $retorno[] = $usuarioTO->getEmail();
        }
        return array_unique($retorno);
    }

    /**
     * @param $request
     * @return RecursoJulgamentoAdmissibilidade
     * @throws \Exception
     */
    private function montarRequest($request)
    {

        if (empty($request['julgamentoAdmissibilidade']['id'])) {
            throw new \Exception('Recurso sem julgamento');
        }

        $julgamento = $this->getJulgamentoRepository()->find($request['julgamentoAdmissibilidade']['id']);

        if (empty($julgamento)) {
            throw new \Exception('Recurso sem julgamento');
        }


        $recurso = new RecursoJulgamentoAdmissibilidade();
        $recurso->setId(Utils::getValue('id', $request))
            ->setDescricao(Utils::getValue('descricao', $request))
            ->setJulgamentoAdmissibilidade($julgamento);

        return $recurso;

    }

    /**
     * @param RecursoJulgamentoAdmissibilidade $julgamento
     * @throws \Doctrine\ORM\ORMException
     */
    private function addArquivos($recurso)
    {
        $request = $this->getRequest();
        $upload = $request->allFiles();

        $caminho = $this->getArquivoService()->getCaminhoRepositorioRecursoJulgamentoAdmissibilidade($recurso->getId());

        foreach ($upload['arquivos'] ?? [] as $arquivo) {
            /**
             * @var UploadedFile $arquivo
             */
            $arquivo = $arquivo['arquivo'];
            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivo->getClientOriginalName(),
                Constants::PREFIX_ARQ_RECURSO_JULGAMENTO_ADMISSIBILIDADE
            );
            $this->getArquivoService()->salvar($caminho, $nomeArquivoFisico, $arquivo);

            $arquivoJulgamento = (new ArquivoRecursoJulgamentoAdmissibilidade())
                ->setNome($arquivo->getClientOriginalName())
                ->setNomeFisico($nomeArquivoFisico)
                ->setRecurso($recurso);
            $recurso->getArquivos()->add($arquivoJulgamento);
        }

        $this->getRecursoJulgamentoRepository()->persist($recurso, true);
    }

    /**
     * @param $id
     * @return RecursoJulgamentoAdmissibilidade
     */
    public function getRecurso($id)
    {
        $recurso = $this->getRecursoJulgamentoRepository()->find($id);
        return $recurso;
    }

    public function getArquivo($idArquivo)
    {
        $arquivo = $this->getArquivoRecursoJulgamentoRepository()->find($idArquivo);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioRecursoJulgamentoAdmissibilidade($arquivo->getRecurso()->getId());
        return $this->getArquivoService()->getArquivo($caminho, $arquivo->getNomeFisico(), $arquivo->getNome());
    }


    /**
     * @return JulgamentoAdmissibilidadeRepository|mixed
     */
    private function getJulgamentoRepository()
    {
        if (empty($this->julgamentoRepository)) {
            $this->julgamentoRepository = $this->getRepository(JulgamentoAdmissibilidade::class);
        }
        return $this->julgamentoRepository;
    }

    /**
     * @return RecursoJulgamentoAdmissibilidadeRepository|mixed
     */
    private function getRecursoJulgamentoRepository()
    {
        if (empty($this->recursoJulgamentoRepository)) {
            $this->recursoJulgamentoRepository = $this->getRepository(RecursoJulgamentoAdmissibilidade::class);
        }
        return $this->recursoJulgamentoRepository;
    }

    /**
     * @return RecursoJulgamentoAdmissibilidadeRepository|mixed
     */
    private function getArquivoRecursoJulgamentoRepository()
    {
        if (empty($this->arquivoRecursoJulgamentoRepository)) {
            $this->arquivoRecursoJulgamentoRepository = $this->getRepository(ArquivoRecursoJulgamentoAdmissibilidade::class);
        }
        return $this->arquivoRecursoJulgamentoRepository;
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
        if (empty($this->denunciaRepository)) {
            $this->denunciaRepository = $this->getRepository(Denuncia::class);
        }
        return $this->denunciaRepository;
    }

    /**
     * @return MembroComissaoRepository|mixed
     */
    private function getMembroComissaoRepository()
    {
        if (empty($this->membroComissaoRepository)) {
            $this->membroComissaoRepository = $this->getRepository(MembroComissao::class);
        }
        return $this->membroComissaoRepository;
    }

    /**
     * @param Denuncia $denuncia
     * @throws \Exception
     */
    private function gerarHistorico(Denuncia $denuncia)
    {
        $historicoDenuncia = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
            Constants::ACAO_RECURSO_JULGAMENTO_ADMISSIBILIDADE);

        $this->getHistoricoDenunciaBO()->salvar($historicoDenuncia);
    }

    /**
     * @return DenunciaBO
     */
    private function getDenunciaBO()
    {
        if (empty($this->denunciaBO)) {
            $this->denunciaBO = app(DenunciaBO::class);
        }
        return $this->denunciaBO;
    }

    /**
     * @return DenunciaMembroChapaBO
     */
    private function getDenunciaMembroChapaBO()
    {
        if (empty($this->denunciaMembroChapaBO)) {
            $this->denunciaMembroChapaBO = app(DenunciaMembroChapaBO::class);
        }
        return $this->denunciaMembroChapaBO;
    }

    /**
     * @return HistoricoDenunciaBO
     */
    private function getHistoricoDenunciaBO()
    {
        if (empty($this->historicoDenunciaBO)) {
            $this->historicoDenunciaBO = app(HistoricoDenunciaBO::class);
        }
        return $this->historicoDenunciaBO;
    }

    /**
     * @return AtividadeSecundariaCalendarioBO
     */
    private function getAtividadeSecundariaCalendarioBO()
    {
        if (empty($this->atividadeSecundariaCalendarioBO)) {
            $this->atividadeSecundariaCalendarioBO = app(AtividadeSecundariaCalendarioBO::class);
        }
        return $this->atividadeSecundariaCalendarioBO;
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
     * @return CorporativoService
     */
    private function getCorporativoService()
    {
        if (empty($this->corporativoService)) {
            $this->corporativoService = app()->make(CorporativoService::class);
        }

        return $this->corporativoService;
    }

    /**
     * @return FilialBO
     */
    private function getFilialBO()
    {
        if (empty($this->filialBO)) {
            $this->filialBO = app()->make(FilialBO::class);
        }

        return $this->filialBO;
    }

    /**
     * Método para retornar a instancia de Membro de Comissao BO
     *
     * @return MembroComissaoBO
     */
    private function getMembroComissaoBO()
    {
        if (empty($this->membroComissaoBO)) {
            $this->membroComissaoBO = new MembroComissaoBO();
        }
        return $this->membroComissaoBO;
    }

    public function verificaRecursoAdmissibilidade($idDenuncia) {
        /** @var Denuncia $denuncia */
        $denuncia = $this->getDenunciaRepository()->find($idDenuncia);
        if($julgamento =$denuncia->getJulgamentoAdmissibilidade()) {
            return $this->verificarPrazoRecurso([
                "julgamentoAdmissibilidade" => [
                    "id" => $julgamento->getId()
                ]
            ]);
        }
        return [];
    }

    /**
     * Retorna uma instancia de Calendario Api Service
     *
     * @return CalendarioApiService
     * @var \App\Service\CalendarioApiService
     */
    private function getCalendarioApiService()
    {
        if (empty($this->calendarioApiService)) {
            $this->calendarioApiService = new CalendarioApiService();
        }
        return $this->calendarioApiService;
    }

}
