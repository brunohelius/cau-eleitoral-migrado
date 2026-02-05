<?php

namespace App\Business;

use App\Jobs\EnviarEmailJulgamentoRecursoAdmissibilidadeJob;
use App\To\JulgamentoRecursoAdmissibilidadeTO;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

use App\Exceptions\Message;
use App\Exceptions\NegocioException;

use App\Util\Utils;
use App\Config\Constants;

use App\Entities\Denuncia;
use App\Entities\RecursoJulgamentoAdmissibilidade;
use App\Entities\JulgamentoAdmissibilidade;
use App\Entities\ArquivoJulgamentoRecursoAdmissibilidade;
use App\Entities\Usuario;
use App\Entities\MembroComissao;
use App\Entities\JulgamentoRecursoAdmissibilidade;
use App\Entities\ParecerJulgamentoRecursoAdmissibilidade;

use App\Repository\DenunciaRepository;
use App\Repository\JulgamentoAdmissibilidadeRepository;
use App\Repository\RecursoJulgamentoAdmissibilidadeRepository;
use App\Repository\MembroComissaoRepository;
use App\Repository\JulgamentoRecursoAdmissibilidadeRepository;
use App\Repository\ParecerJulgamentoRecursoAdmissibilidadeRepository;
use App\Repository\ArquivoJulgamentoRecursoAdmissibilidadeRepository;
use App\Repository\UsuarioRepository;

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
class JulgamentoRecursoAdmissibilidadeBO extends AbstractBO
{

    public function salvar($request)
    {
        $julgamentoRecurso = $this->montarRequest($request);

        $this->beginTransaction();
        try {
            $julgamentoRecurso = $this->getJulgamentoRecursoAdmissibilidadeRepository()->persist($julgamentoRecurso, true);
            $this->addArquivos($julgamentoRecurso);

            $denuncia = $julgamentoRecurso->getRecursoAdmissibilidade()->getJulgamentoAdmissibilidade()->getDenuncia();
            $this->gerarHistorico($denuncia);

            $situacaoDenuncia = $julgamentoRecurso->getParecer()->getId() == ParecerJulgamentoRecursoAdmissibilidade::PROVIMENTO ? Constants::SITUACAO_DENUNCIA_AGUARDANDO_RELATOR : Constants::SITUACAO_DENUNCIA_TRANSITO_JULGADO;

            $this->getDenunciaBO()->salvarSituacaoDenuncia($denuncia, $situacaoDenuncia);
            $this->commitTransaction();

            if (!empty($julgamentoRecurso)) {
                Utils::executarJOB(new EnviarEmailJulgamentoRecursoAdmissibilidadeJob($julgamentoRecurso->getId()));
            }

            return $julgamentoRecurso;
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw new \Exception($e->getMessage());
        }

    }

    /**
     * @param int $idJulgamentoRecursoAdmissibilidade
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function notificacaoJulgamentoRecurso(int $idJulgamentoRecursoAdmissibilidade)
    {
        $julgamentoRecurso = $this->getJulgamentoRecursoAdmissibilidadeRepository()->find($idJulgamentoRecursoAdmissibilidade);

        $denuncia = $julgamentoRecurso->getRecursoAdmissibilidade()->getJulgamentoAdmissibilidade()->getDenuncia();
        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorAtividadeSecundaria(
            $denuncia->getAtividadeSecundaria()->getId(),4,15
        );

        $arrCampos = $this->montarCamposEmails($julgamentoRecurso);

        if($julgamentoRecurso->getParecer()->getId() == ParecerJulgamentoRecursoAdmissibilidade::PROVIMENTO){
            $this->notificarProvimento($julgamentoRecurso, $arrCampos, $atividade);
        }else{
            $this->notificarImprovimento($julgamentoRecurso, $arrCampos, $atividade);
        }
    }

    private function notificarImprovimento(JulgamentoRecursoAdmissibilidade $julgamentoRecurso, $arrCampos, $atividade)
    {
        $denuncia = $julgamentoRecurso->getRecursoAdmissibilidade()->getJulgamentoAdmissibilidade()->getDenuncia();

        $templateEmail = 'emailJulgamentoRecursoAdmissibilidade.html';

        //Coordenadores CEN
        $coodenadoresCEN = $this->getMembroComissaoRepository()->getCoordenadoresPorFiltro([]);
        if(!empty($coodenadoresCEN)){
            $listaEmails = $this->getMembroComissaoBO()->getListEmailsDestinatarios($coodenadoresCEN);
            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_COORDENADOR_CEN_DENUNCIA_JULGADA, $templateEmail, $arrCampos);
        }

        //Responsavel pelo  julgamento recurso
        if($julgamentoRecurso->getUsuario()){
            $usuario = $this->getUsuarioRepository()->find($julgamentoRecurso->getUsuario());
            if(!empty($usuario->getEmail())){
                $listaEmails = [$usuario->getEmail()];
                $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_ASSESSOR_CEN_DENUNCIA_JULGADA, $templateEmail, $arrCampos);
            }
        }

        //Denuciante
        $email = $denuncia->getPessoa()->getEmail();
        if (!empty($email)) {
            $listaEmails = [$email];
            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_DENUNCIA_CADATRADA_IMPROVIMENTO, $templateEmail, $arrCampos);
        }

    }
    private function notificarProvimento(JulgamentoRecursoAdmissibilidade $julgamentoRecurso, $arrCampos, $atividade)
    {
        $denuncia = $julgamentoRecurso->getRecursoAdmissibilidade()->getJulgamentoAdmissibilidade()->getDenuncia();
        $filial = $this->getDenunciaBO()->verificaDenunciaIdFilialIES($denuncia);
        $templateEmail = 'emailJulgamentoRecursoAdmissibilidade.html';

        //Coordenadores CEN
        $coodenadoresCEN = $this->getMembroComissaoRepository()->getCoordenadoresPorFiltro([]);
        $listaEmailsCoordenadoresCen = [];
        if(!empty($coodenadoresCEN)){
            $listaEmailsCoordenadoresCen = $this->getMembroComissaoBO()->getListEmailsDestinatarios($coodenadoresCEN);
        }

        //Coordenadores CE
        $coodenadoresCE = $this->getMembroComissaoRepository()->getCoordenadoresPorFiltro(['idCauUf' => $filial]);
        $listaEmailsCoordenadoresCe = [];
        if(!empty($coodenadoresCE)) {
            $listaEmailsCoordenadoresCe = $this->getMembroComissaoBO()->getListEmailsDestinatarios($coodenadoresCE);
        }

        $listaEmails = array_unique(array_merge($listaEmailsCoordenadoresCe, $listaEmailsCoordenadoresCen));

        if(!empty($listaEmails)){
            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_COORDENADOR_CE_CEN, $templateEmail, $arrCampos);
        }

        //Responsavel pelo  julgamento recurso
        if($julgamentoRecurso->getUsuario()){
            $usuario = $this->getUsuarioRepository()->find($julgamentoRecurso->getUsuario());
            if(!empty($usuario->getEmail())){
                $listaEmails = [$usuario->getEmail()];
                $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_ASSCESSOR_CEN, $templateEmail, $arrCampos);
            }
        }

        //Denuciante
        $email = $denuncia->getPessoa()->getEmail();
        if (!empty($email)) {
            $listaEmails = [$email];
            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_DENUNCIANTE_DENUNCIA_ADMITIDA, $templateEmail, $arrCampos);
        }

        //Denunciados
        $denunciados = $this->getDenunciaBO()->getEmailDenunciados($denuncia);

        if(!empty($denunciados)){
            $listaEmails = $denunciados;

            $this->getEmailAtividadeSecundariaBO()->enviarEmailPorIdAtividadeSecundaria($atividade->getId(), $listaEmails, Constants::EMAIL_DENUNCIADO_DENUNCIA_ADMITIDA, $templateEmail, $arrCampos);
        }
    }

    private function montarCamposEmails(JulgamentoRecursoAdmissibilidade $julgamentoRecurso)
    {
        $denuncia = $julgamentoRecurso->getRecursoAdmissibilidade()->getJulgamentoAdmissibilidade()->getDenuncia();

        $arrCampos = [];
        $arrCampos['{{parecer}}'] = 'Julgamento: ' . $julgamentoRecurso->getParecer()->getDescricao();
        $arrCampos['{{protocolo}}'] = 'Protocolo: ' . $denuncia->getNumeroSequencial();
        $arrCampos['{{processoEleitoral}}'] = 'Processo Eleitoral: ' . $denuncia->getAtividadeSecundaria()->getAtividadePrincipalCalendario()->getCalendario()->getDataInicioVigencia()->format('Y');
        $arrCampos['{{tipoDenuncia}}'] = 'Tipo de denúncia: ' . $denuncia->getTipoDenuncia()->getDescricao();
        $arrCampos['{{narracaoFatos}}'] = 'Narração dos Fatos: ' . $denuncia->getDescricaoFatos();

        if($denuncia->getTestemunhas()->count()){

            $arrTestemunhas = [];
            foreach ($denuncia->getTestemunhas() as $testemunha){
                $arrTestemunhas[] = $testemunha->getNome();
            }
            $arrCampos['{{testemunhas}}'] = 'Testemunhas: ' .implode(', ', $arrTestemunhas);
        }else{
            $arrCampos['{{testemunhas}}'] = 'Testemunhas: Não há testemunhas';
        }

        $filial = !empty($denuncia->getFilial())
            ? $this->getFilialBO()->getPorId($denuncia->getFilial()->getId())
            : $this->getFilialBO()->getFilialIES();

        switch ( $denuncia->getTipoDenuncia()->getId()){

            case Constants::TIPO_MEMBRO_CHAPA :

                $listaEmails = $this->getDenunciaMembroChapaBO()->getDadosDenunciante($denuncia->getId());
                $nome = $listaEmails ? $listaEmails[0]['nome'] : '';
                $arrCampos['{{nomeDenunciado}}'] = 'Nome do Denunciado: ' . $nome;

                $denunciaChapa = $denuncia->getDenunciaChapa();
                $numeroChapa = '';
                $uf = '';
                if($denunciaChapa != null) {
                    $numeroChapa =  $denunciaChapa->getChapaEleicao()->getNumeroChapa();
                    $uf = $filial->getPrefixo();
                }
                $arrCampos['{{nChapa}}'] ='Nº Chapa: ' . $numeroChapa;
                $arrCampos['{{uf}}'] ='Nº UF: ' . $uf;
                break;

            case Constants::TIPO_CHAPA :

                $arrCampos['{{nomeDenunciado}}'] = '';

                $denunciaChapa = $denuncia->getDenunciaChapa();
                $numeroChapa = '';
                $uf = '';
                if($denunciaChapa != null) {
                    $numeroChapa =  $denunciaChapa->getChapaEleicao()->getNumeroChapa();
                    $uf = $filial->getPrefixo();
                }
                $arrCampos['{{nChapa}}'] ='Nº Chapa: ' . $numeroChapa;
                $arrCampos['{{uf}}'] ='UF: ' . $uf;
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
                $arrCampos['{{nChapa}}'] = '';
                $arrCampos['{{uf}}'] = 'UF: ' . $filial->getPrefixo();
                break;

            default:
                $arrCampos['{{parecer}}'] = '';
                $arrCampos['{{protocolo}}'] = '';
                $arrCampos['{{processoEleitoral}}'] = '';
                $arrCampos['{{tipoDenuncia}}'] = '';
                $arrCampos['{{narracaoFatos}}'] = '';
                $arrCampos['{{nomeDenunciado}}'] = '';
                $arrCampos['{{testemunhas}}'] = '';
                $arrCampos['{{nomeDenunciado}}'] = '';
                $arrCampos['{{nChapa}}'] = '';
                $arrCampos['{{uf}}'] = '';
        }

        return $arrCampos;
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

        if (empty($request['recursoAdmissibilidade']['id'])) {
            throw new \Exception('Julgamento sem recurso');
        }

        if (empty($request['parecer']['id'])) {
            throw new \Exception('Julgamento sem parecer');
        }

        $recurso = $this->getRecursoJulgamentoRepository()->find($request['recursoAdmissibilidade']['id']);
        if (empty($recurso)) {
            throw new \Exception('Julgamento sem recurso');
        }

        $parecer = $this->getParecerRepository()->find($request['parecer']['id']);
        if (empty($parecer)) {
            throw new \Exception('Julgamento sem parecer');
        }

        $julgamentoRecurso = new JulgamentoRecursoAdmissibilidade();
        $julgamentoRecurso->setId(Utils::getValue('id', $request))
            ->setDescricao(Utils::getValue('descricao', $request))
            ->setRecursoAdmissibilidade($recurso)
            ->setParecer($parecer)
            ->setUsuario($this->getUsuarioFactory()->getUsuarioLogado()->id);

        return $julgamentoRecurso;
    }

    /**
     * Retorna as informações do julgamento recurso admissibilidade para exportação em pdf
     *
     * @param Denuncia $denuncia
     * @return JulgamentoRecursoAdmissibilidadeTO|null
     * @throws \Exception
     */
    public function getExportarInformacoesJulgamentoRecursoAdmissibilidade(Denuncia $denuncia)
    {
        $julgamento = $denuncia->getJulgamentoAdmissibilidade()->getRecursoJulgamento()
            ->getJulgamentoRecursoAdmissibilidade();

        $julgamentoTO = JulgamentoRecursoAdmissibilidadeTO::newInstanceFromEntity($julgamento);

        $documentos = $this->getDenunciaBO()->getDescricaoArquivoExportar(
            $julgamento->getArquivos(),
            $this->getArquivoService()->getCaminhoRepositorioJulgamentoRecursoAdmissibilidade($julgamento->getId())
        );
        $julgamentoTO->setDescricaoArquivo($documentos);

        return $julgamentoTO;
    }

    /**
     * @param JulgamentoRecursoAdmissibilidade $julgamentoRecurso
     * @throws \Doctrine\ORM\ORMException
     */
    private function addArquivos(JulgamentoRecursoAdmissibilidade $julgamentoRecurso)
    {
        $request = $this->getRequest();
        $upload = $request->allFiles();

        $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoRecursoAdmissibilidade($julgamentoRecurso->getId());

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

            $arquivoJulgamento = (new ArquivoJulgamentoRecursoAdmissibilidade())
                ->setNome($arquivo->getClientOriginalName())
                ->setNomeFisico($nomeArquivoFisico)
                ->setJulgamentoRecursoAdmissibilidade($julgamentoRecurso);

            $julgamentoRecurso->getArquivos()->add($arquivoJulgamento);
        }

        $this->getJulgamentoRecursoAdmissibilidadeRepository()->persist($julgamentoRecurso, true);
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
        $arquivo = $this->getArquivoJulgamentoRecursoAdmissibilidadeRepository()->find($idArquivo);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioJulgamentoRecursoAdmissibilidade($arquivo->getJulgamentoRecursoAdmissibilidade()->getId());
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
     * @return ParecerJulgamentoRecursoAdmissibilidadeRepository|mixed
     */
    private function getParecerRepository()
    {
        if (empty($this->parecerJulgamentoRecursoAdmissibilidadeRepository)) {
            $this->parecerJulgamentoRecursoAdmissibilidadeRepository = $this->getRepository(ParecerJulgamentoRecursoAdmissibilidade::class);
        }
        return $this->parecerJulgamentoRecursoAdmissibilidadeRepository;
    }

    /**
     * @return JulgamentoRecursoAdmissibilidadeRepository|mixed
     */
    private function getJulgamentoRecursoAdmissibilidadeRepository()
    {
        if (empty($this->julgamentoRecursoAdmissibilidadeRepository)) {
            $this->julgamentoRecursoAdmissibilidadeRepository = $this->getRepository(JulgamentoRecursoAdmissibilidade::class);
        }
        return $this->julgamentoRecursoAdmissibilidadeRepository;
    }

    /**
     * @return ArquivoJulgamentoRecursoAdmissibilidadeRepository|mixed
     */
    private function getArquivoJulgamentoRecursoAdmissibilidadeRepository()
    {
        if (empty($this->arquivoJulgamentoRecursoAdmissibilidadeRepository)) {
            $this->arquivoJulgamentoRecursoAdmissibilidadeRepository = $this->getRepository(ArquivoJulgamentoRecursoAdmissibilidade::class);
        }
        return $this->arquivoJulgamentoRecursoAdmissibilidadeRepository;
    }

    /**
     * @return UsuarioRepository|mixed
     */
    private function getUsuarioRepository()
    {
        if (empty($this->usuarioRepository)) {
            $this->usuarioRepository = $this->getRepository(Usuario::class);
        }
        return $this->usuarioRepository;
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
            'Julgamento do recurso de admissibilidade');

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

}
