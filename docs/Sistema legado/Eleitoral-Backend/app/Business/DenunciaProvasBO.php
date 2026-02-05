<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 02/09/2019
 * Time: 09:07
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoDenunciaDefesa;
use App\Entities\ArquivoDenunciaProvas;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Denuncia;
use App\Entities\DenunciaDefesa;
use App\Entities\DenunciaProvas;
use App\Entities\EncaminhamentoDenuncia;
use App\Entities\Filial;
use App\Entities\Pessoa;
use App\Entities\Profissional;
use App\Entities\TipoSituacaoEncaminhamentoDenuncia;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\UsuarioFactory;
use App\Jobs\EnviarEmailDenunciaDefesaJob;
use App\Jobs\EnviarEmailDenunciaProvasJob;
use App\Repository\EncaminhamentoDenunciaRepository;
use App\Repository\FilialRepository;
use App\Repository\ProfissionalRepository;
use App\Repository\TipoSituacaoEncaminhamentoDenunciaRepository;
use App\Service\ArquivoService;
use App\Service\CalendarioApiService;
use App\Service\CorporativoService;
use App\To\DenunciaDefesaTO;
use App\To\DenunciaInadmitidaTO;
use App\To\DenunciaProvasTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'Denuncia Provas'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DenunciaProvasBO extends AbstractBO
{

    /**
     * @var \App\Service\ArquivoService
     */
    private $arquivoService;

    /**
     * @var HistoricoDenunciaBO
     */
    private $historicoDenunciaBO;

    /**
     * @var DenunciaBO
     */
    private $denunciaBO;

    /**
     * @var EncaminhamentoDenunciaBO
     */
    private $encaminhamentoDenunciaBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var DenunciaMembroComissaoBO
     */
    private $denunciaMembroComissaoBO;

    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var DenunciaMembroChapaBO
     */
    private $denunciaMembroChapaBO;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var PessoaBO
     */
    private $pessoaBO;

    /**
     * @var \App\Repository\DenunciaProvasRepository
     */
    private $denunciaProvasRepository;

    /**
     * @var \App\Repository\ArquivoDenunciaProvasRepository
     */
    private $arquivoDenunciaProvasRepository;

    /**
     * @var FilialRepository
     */
    private $filialRepository;

    /**
     * @var ProfissionalRepository
     */
    private $profissionalRepository;

    /**
     * @var TipoSituacaoEncaminhamentoDenunciaRepository
     */
    private $tipoSituacaoEncaminhamentoDenunciaRepository;

    /**
     * @var EncaminhamentoDenunciaRepository
     */
    private $encaminhamentoDenunciaRepository;

    /**
     * @var \App\Service\CalendarioApiService
     */
    private $calendarioApiService;


    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->filialRepository = $this->getRepository(Filial::class);
        $this->profissionalRepository = $this->getRepository(Profissional::class);
        $this->denunciaProvasRepository = $this->getRepository(DenunciaProvas::class);
        $this->arquivoDenunciaProvasRepository = $this->getRepository(ArquivoDenunciaProvas::class);
        $this->encaminhamentoDenunciaRepository = $this->getRepository(EncaminhamentoDenuncia::class);
        $this->tipoSituacaoEncaminhamentoDenunciaRepository = $this->getRepository(TipoSituacaoEncaminhamentoDenuncia::class);
    }

    /**
     * Salva as Provas da denuncia.
     *
     * @param DenunciaProvas $denunciaProvas
     * @return mixed
     * @throws NegocioException
     */
    public function salvar(DenunciaProvas $denunciaProvas)
    {
        $this->validarCamposObrigatoriosDefesa($denunciaProvas);
        $this->validarQuantidadeArquivosDefesa($denunciaProvas);
        $arquivos = (!empty($denunciaProvas->getArquivosDenunciaProvas())) ? clone $denunciaProvas->getArquivosDenunciaProvas() : null;
        $denunciaProvas = $this->setNomeArquivoFisicoProvas($denunciaProvas);
        $denunciaProvas->setArquivosDenunciaProvas(null);
        $denuncia = null;

        try {
            $this->beginTransaction();
            $denuncia = $this->getDenunciaBO()->getDenuncia($denunciaProvas->getDenuncia()->getId());
            $encaminhamento = $this->getEncaminhamentoDenunciaBO()->getEncaminhamentoDenunciaPorId(
                $denunciaProvas->getEncaminhamentoDenuncia()->getId()
            );

            $this->validarPrazoProvaDenuncia($encaminhamento);

            $denunciaProvas->setDenuncia($denuncia);
            $denunciaProvas->setEncaminhamentoDenuncia($encaminhamento);
            $denunciaProvas->setDataProva(Utils::getData());

            $denunciaProvasSalva = $this->denunciaProvasRepository->persist($denunciaProvas);

            $tipoSituacao = $this->getEncaminhamentoDenunciaBO()
                ->getTipoSituacaoEncaminhamentoPorId(Constants::TIPO_SITUACAO_ENCAMINHAMENTO_CONCLUIDO);

            $encaminhamento->setTipoSituacaoEncaminhamento($tipoSituacao);
            $this->encaminhamentoDenunciaRepository->persist($encaminhamento);

            if (!empty($arquivos)) {
                $this->salvarArquivos($arquivos, $denunciaProvasSalva);
            }

            $historicoDenunciaProvas = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
                Constants::ACAO_HISTORICO_DENUNCIA_PROVAS);
            $this->getHistoricoDenunciaBO()->salvar($historicoDenunciaProvas);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if (!empty($denunciaProvasSalva)) {
            Utils::executarJOB(new EnviarEmailDenunciaProvasJob($denuncia->getId(), $denunciaProvasSalva->getId()));
        }

        return DenunciaProvasTO::newInstanceFromEntity($denunciaProvasSalva);
    }

    /**
     * Verifica o prazo de Provas da Denuncia.
     *
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     * @throws NegocioException
     */
    public function validarPrazoProvaDenuncia(EncaminhamentoDenuncia $encaminhamentoDenuncia)
    {
        if (!empty($encaminhamentoDenuncia)) {
            $feriados = $this->getCalendarioApiService()
                ->getFeriadosNacionais(Utils::getAnoData());
            $data = Utils::adicionarDiasUteisData($encaminhamentoDenuncia->getData(), 1, $feriados );

            $dataLimite = Utils::adicionarDiasData(
                $data,
                ($encaminhamentoDenuncia->getPrazoProducaoProvas() - 1)
            );

            if (Utils::getDataHoraZero() > Utils::getDataHoraZero($dataLimite)) {
                throw new NegocioException(Message::MSG_PRAZO_PRODUCAO_PROVAS_ENCERRADO);
            }
        }
    }

    /**
     * Recupera as Denuncias Admitidas sem Defesa ate a data de expiração.
     *
     * @throws \Exception
     */
    public function alteraStatusProvaPorPrazo()
    {
        $encaminhamentosEncerradosHoje = $this->getEncaminhamentoDenunciaBO()->getEncaminhamentosProvaPrazoEncerrado();

        foreach ($encaminhamentosEncerradosHoje as $idEncaminhamento) {
            try {
                $this->beginTransaction();

                $encaminhamento = $this->getEncaminhamentoDenunciaBO()->getEncaminhamentoDenunciaPorId($idEncaminhamento);
                $tipoSituacao = $this->getEncaminhamentoDenunciaBO()
                    ->getTipoSituacaoEncaminhamentoPorId(Constants::TIPO_SITUACAO_ENCAMINHAMENTO_TRANSCORRIDO);

                $encaminhamento->setTipoSituacaoEncaminhamento($tipoSituacao);

                $this->encaminhamentoDenunciaRepository->persist($encaminhamento);

                $this->commitTransaction();
            } catch (\Exception $e) {
                $this->rollbackTransaction();
                throw $e;
            }
        }
    }

    /**
     * Método auxiliar para Salvar dados dos Arquivos para a Denuncia Defesa
     *
     * @param $arquivoProvas
     * @param DenunciaProvas $denunciaProvas
     * @return DenunciaProvas
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function salvarArquivos($arquivoProvas, DenunciaProvas $denunciaProvas)
    {
        $arquivosSalvos = new ArrayCollection();

        if (!empty($arquivoProvas)) {
            foreach ($arquivoProvas as $arquivoProva) {
                $arquivoProva->setDenunciaProvas($denunciaProvas);
                $arquivoSalvo = $this->arquivoDenunciaProvasRepository->persist($arquivoProva);
                $arquivosSalvos->add($arquivoSalvo);
                $this->salvaArquivosDiretorio($arquivoProva, $denunciaProvas);
                $arquivoSalvo->setArquivo(null);
            }
        }
        $denunciaProvas->setArquivosDenunciaProvas($arquivosSalvos);
        $denunciaProvas->removerFiles();

        return $denunciaProvas;
    }

    /**
     * Realiza o(s) upload(s) do(s) arquivo(s) da Denuncia
     *
     * @param $arquivo
     * @param DenunciaProvas $denunciaProvas
     */
    private function salvaArquivosDiretorio($arquivo, DenunciaProvas $denunciaProvas)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenunciaProvas($denunciaProvas->getId());

        if ($arquivo != null) {
            if (!empty($arquivo->getArquivo())) {
                $this->getArquivoService()->salvar($caminho, $arquivo->getNomeFisicoArquivo(), $arquivo->getArquivo());
            }
        }
    }

    /**
     * Cria os nomes de arquivo para Denuncia Inadmitida
     *
     * @param DenunciaProvas $denunciaProvas
     * @return DenunciaProvas
     */
    private function setNomeArquivoFisicoProvas(DenunciaProvas $denunciaProvas)
    {
        if (!empty($denunciaProvas->getArquivosDenunciaProvas())) {
            foreach ($denunciaProvas->getArquivosDenunciaProvas() as $arquivo) {
                if (empty($arquivo->getId())) {
                    $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                        $arquivo->getNome(),
                        Constants::PREFIXO_DOC_DENUNCIA_PROVAS
                    );
                    $arquivo->setNomeFisicoArquivo($nomeArquivoFisico);
                }
            }
        }
        return $denunciaProvas;
    }

    /**
     * Valida se os campos obrigatórios estão preenchidos
     *
     * @param DenunciaProvas $denunciaProvas
     * @throws NegocioException
     */
    private function validarCamposObrigatoriosDefesa(DenunciaProvas $denunciaProvas)
    {
        $campos = [];

        if (empty($denunciaProvas->getDescricaoProvasApresentadas())) {
            $campos[] = 'LABEL_DESCRICAO_PROVAS_APRESENTADAS';
        }

        if (empty($denunciaProvas->getDenuncia()->getId())) {
            $campos[] = 'LABEL_ID_DENUNCIA';
        }

        if (empty($denunciaProvas->getEncaminhamentoDenuncia()->getId())) {
            $campos[] = 'LABEL_ID_ENCAMINHAMENTO';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Valida a quantidade de arquivos para a Denuncia Provas
     *
     * @param DenunciaProvas $denunciaProvas
     * @throws NegocioException
     */
    private function validarQuantidadeArquivosDefesa(DenunciaProvas $denunciaProvas)
    {
        $arquivos = $denunciaProvas->getArquivosDenunciaProvas();
        if (!empty($arquivos) and count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO) {
            throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO);
        }
    }

    /**
     * Evia o Email da defesa de acordo com os parametros informados.
     *
     * @param $idDenuncia
     * @return bool
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enviarEmailDenunciaProvas($idDenuncia, $idDenunciaProvas)
    {
        $denuncia = $this->getDenunciaBO()->getDenuncia($idDenuncia);
        $denunciaProvas = $this->denunciaProvasRepository->find($idDenunciaProvas);

        $atvSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getAtividadeSecundariaPorNiveis(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_INSERIR_PROVAS,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_PROVAS
        );

        $nomeTemplate = '';
        $parametrosEmail = $this->prepararParametrosEmail($denunciaProvas, $denuncia, $atvSecundaria);

        if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_CHAPA) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_PROVAS_CHAPA;

            $this->enviarEmailResponsavelChapaDenuncia($atvSecundaria->getId(), $parametrosEmail, $nomeTemplate, $denuncia->getDenunciaChapa()->getChapaEleicao()->getId());
        } else if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_CHAPA) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_PROVAS_MEMBRO_CHAPA;

            $this->enviarEmailResponsavelMembroChapa($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);
        } else if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_COMISSAO) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_PROVAS_MEMBRO_COMISSAO;

            $this->enviarEmailResponsavelMembroComissao($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);
        } else if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_OUTROS) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_PROVAS_OUTROS;

            $this->enviarEmailResponsavelOutros($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);
        }

        $this->enviarEmailRelatorDenuncia($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);

        $emailsDenunciante[] = $denuncia->getPessoa()->getEmail();
        if(!empty($emailsDenunciante)) {
            $this->enviarEmailDefesaDenuncia($atvSecundaria->getId(), $emailsDenunciante, $parametrosEmail, $nomeTemplate);
        }

        return true;
    }

    /**
     * Envia o e-mail para o 'Denunciante'.
     *
     * @param Denuncia $denuncia
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param $parametrosEmails
     * @param $templateEmail
     */
    private function enviarEmailDenunciante(
        Denuncia $denuncia,
        AtividadeSecundariaCalendario $atividadeSecundaria,
        $parametrosEmails,
        $templateEmail
    )
    {
        $emails[] = $denuncia->getPessoa()->getEmail();

        $this->enviarEmailDefesaDenuncia($atividadeSecundaria->getId(), $emails, $parametrosEmails, $templateEmail);
    }

    /**
     * Envia o e-mail para o responsável da chapa do cadastro da denúncia
     *
     * @param int $idAtivSecundaria
     * @param array $parametrosEmail ,
     * @param string $nomeTemplate
     * @param $idChapa
     */
    private function enviarEmailResponsavelChapaDenuncia(
        $idAtivSecundaria,
        $parametrosEmail,
        $nomeTemplate,
        $idChapa
    )
    {
        $responsaveis = $this->getMembroChapaBO()->getMembrosResponsaveisChapa(
            $idChapa,
            Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
        );
        $destinatariosEmail = $this->getMembroChapaBO()->getListEmailsDestinatarios($responsaveis);
        $this->enviarEmailDefesaDenuncia($idAtivSecundaria, $destinatariosEmail, $parametrosEmail, $nomeTemplate);
    }

    /**
     * Envia o e-mail para o responsável da chapa do cadastro da denúncia
     *
     * @param int $idAtivSecundaria
     * @param array $parametrosEmail ,
     * @param Denuncia $denunciaSalva ,
     * @param string $nomeTemplate
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function enviarEmailResponsavelMembroChapa(
        $idAtivSecundaria,
        $parametrosEmail,
        $denunciaSalva,
        $nomeTemplate
    )
    {
        $destinatariosEmail = $this->getDenunciaMembroChapaBO()->getDadosDenunciante($denunciaSalva->getId());
        $destinatariosEmail = $destinatariosEmail[0];
        $this->enviarEmailDefesaDenuncia($idAtivSecundaria, $destinatariosEmail, $parametrosEmail, $nomeTemplate);
    }

    /**
     * Envia o e-mail para o responsável da chapa do cadastro da denúncia
     *
     * @param int $idAtivSecundaria
     * @param array $parametrosEmail ,
     * @param Denuncia $denunciaSalva ,
     * @param string $nomeTemplate
     * @throws NonUniqueResultException
     */
    private function enviarEmailResponsavelMembroComissao(
        $idAtivSecundaria,
        $parametrosEmail,
        $denunciaSalva,
        $nomeTemplate
    )
    {
        $destinatariosEmail = $this->getDenunciaMembroComissaoBO()->getDadosDenunciante($denunciaSalva->getId());
        $destinatariosEmail = $destinatariosEmail[0];
        $this->enviarEmailDefesaDenuncia($idAtivSecundaria, $destinatariosEmail, $parametrosEmail, $nomeTemplate);
    }

    /**
     * Envia o e-mail para o responsável da Denuncia de Tipo Outros
     *
     * @param int $idAtivSecundaria
     * @param array $parametrosEmail ,
     * @param Denuncia $denunciaSalva ,
     * @param string $nomeTemplate
     * @throws NonUniqueResultException
     */
    private function enviarEmailResponsavelOutros(
        $idAtivSecundaria,
        $parametrosEmail,
        $denunciaSalva,
        $nomeTemplate
    )
    {
        $destinatariosEmail[] = $denunciaSalva->getPessoa()->getProfissional()->getPessoa()->getEmail();
        $this->enviarEmailDefesaDenuncia($idAtivSecundaria, $destinatariosEmail, $parametrosEmail, $nomeTemplate);
    }

    /**
     * Envia o e-mail para o responsável pelo cadastro da denúncia
     *
     * @param int $idAtivSecundaria
     * @param array $parametrosEmail
     * @param Denuncia $denunciaSalva
     * @param string $nomeTemplate
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function enviarEmailRelatorDenuncia(
        $idAtivSecundaria,
        $parametrosEmail,
        $denunciaSalva,
        $nomeTemplate
    )
    {

        $pessoaId = $denunciaSalva->getUltimaDenunciaAdmitida()->getMembroComissao()->getPessoa();
        $profissional = $this->getProfissionalBO()->getPorId($pessoaId);
        $pessoaEmail = $this->getPessoaBO()->getPessoaPorId($profissional->getPessoaId())->getEmail();

        $destinatarios[] = $pessoaEmail;
        $this->enviarEmailDefesaDenuncia($idAtivSecundaria, $destinatarios, $parametrosEmail, $nomeTemplate);
    }

    /**
     * Prepara os parametros para o Envio de email
     *
     * @param DenunciaProvas $denunciaProvas
     * @param Denuncia $denunciaSalva
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @return array
     */
    private function prepararParametrosEmail(
        DenunciaProvas $denunciaProvas,
        Denuncia $denunciaSalva,
        AtividadeSecundariaCalendario $atividadeSecundaria
    )
    {
        $anoEleicao = $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada();
        $tipoDenuncia = '';
        $idCauUf = 0;
        $descricaoDenunciaProvas = $denunciaProvas->getDescricaoProvasApresentadas();
        $encaminhamento = $denunciaProvas->getEncaminhamentoDenuncia()->getDescricao();

        $numeroChapa = null;
        $nomeDenunciado = '';
        $idProfissional = null;

        $protocolo = $denunciaSalva->getNumeroSequencial() ? $denunciaSalva->getNumeroSequencial() : null;

        if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_CHAPA) {

            $tipoDenuncia = Constants::TIPO_DENUNCIA_CHAPA;
            $idCauUf = $denunciaSalva->getDenunciaChapa()->getChapaEleicao()->getIdCauUf();
            $numeroChapa = $denunciaSalva->getDenunciaChapa()->getChapaEleicao()->getNumeroChapa();

        } else if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_CHAPA) {

            $tipoDenuncia = Constants::TIPO_DENUNCIA_MEMBRO_CHAPA;
            $idCauUf = $denunciaSalva->getDenunciaMembroChapa()->getMembroChapa()->getChapaEleicao()->getIdCauUf();
            $idProfissional = $denunciaSalva->getDenunciaMembroChapa()->getMembroChapa()->getProfissional()->getId();
            $numeroChapa = $denunciaSalva->getDenunciaMembroChapa()->getMembroChapa()->getChapaEleicao()->getNumeroChapa();

        } else if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_COMISSAO) {

            $tipoDenuncia = Constants::TIPO_DENUNCIA_MEMBRO_COMISSAO;
            $idCauUf = $denunciaSalva->getDenunciaMembroComissao()->getMembroComissao()->getIdCauUf();
            $idProfissional = $denunciaSalva->getDenunciaMembroComissao()->getMembroComissao()->getPessoa();

        }

        $filialPrefixo = "";

        $filial = $this->filialRepository->find($idCauUf);
        if (!empty($filial)) {
            $filialPrefixo = $filial->getPrefixo();
        }

        if ($idCauUf == Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
            $filialPrefixo = Constants::PREFIXO_CONSELHO_ELEITORAL_NACIONAL;
        }

        if (!empty($idProfissional)) {
            $profissional = $this->profissionalRepository->find($idProfissional);
            $nomeDenunciado = $profissional->getNome();
        }

        return [
            Constants::PARAMETRO_EMAIL_PROCESSO_ELEITORAL => $anoEleicao,
            Constants::PARAMETRO_EMAIL_TIPO_DENUNCIA => $tipoDenuncia,
            Constants::PARAMETRO_EMAIL_NOME_DENUNCIADO => $nomeDenunciado,
            Constants::PARAMETRO_EMAIL_NUMERO_CHAPA => $numeroChapa,
            Constants::PARAMETRO_EMAIL_NM_PROTOCOLO => $protocolo,
            Constants::PARAMETRO_EMAIL_UF => $filialPrefixo,
            Constants::PARAMETRO_EMAIL_DS_PROVAS => $descricaoDenunciaProvas,
            Constants::PARAMETRO_EMAIL_ENCAMINHAMENTO => $encaminhamento
        ];
    }

    /**
     * Método auxiliar que busca o e-mail definido e realiza o envio
     *
     * @param $idAtividadeSecundaria
     * @param array $emailsDestinatarios
     * @param array|null $parametrosExtras
     * @param $nomeTemplate
     */
    private function enviarEmailDefesaDenuncia(
        $idAtividadeSecundaria,
        $emailsDestinatarios,
        $parametrosExtras = [],
        $nomeTemplate
    )
    {
        $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailAtividadeSecundariaPorAtividadeSecundaria(
            $idAtividadeSecundaria
        );

        if (is_array($emailAtividadeSecundaria)) {
            $emailAtividadeSecundaria = $emailAtividadeSecundaria[0];
        }

        $this->getEmailAtividadeSecundariaBO()->enviarEmailAtividadeSecundaria(
            $emailAtividadeSecundaria,
            $emailsDestinatarios,
            $nomeTemplate,
            $parametrosExtras
        );
    }

    /**
     * Método para retornar a instancia de Email Atividade Secundaria BO
     *
     * @return EmailAtividadeSecundariaBO
     */
    private function getEmailAtividadeSecundariaBO()
    {
        if (empty($this->emailAtividadeSecundariaBO)) {
            $this->emailAtividadeSecundariaBO = new EmailAtividadeSecundariaBO();
        }
        return $this->emailAtividadeSecundariaBO;
    }

    /**
     * Recupera a Instancia de Atividade Secundaria Calenadrio BO.
     *
     * @return AtividadeSecundariaCalendarioBO
     */
    public function getAtividadeSecundariaCalendarioBO()
    {
        if (empty($this->atividadeSecundariaCalendarioBO)) {
            $this->atividadeSecundariaCalendarioBO = new AtividadeSecundariaCalendarioBO();
        }

        return $this->atividadeSecundariaCalendarioBO;
    }

    /**
     * Retorna uma nova instância de 'DenunciaBO'.
     *
     * @return DenunciaBO
     */
    private function getDenunciaBO()
    {
        if (empty($this->denunciaBO)) {
            $this->denunciaBO = app()->make(DenunciaBO::class);
        }

        return $this->denunciaBO;
    }

    /**
     * Retorna uma nova instância de 'EncaminhamentoDenunciaBO'.
     *
     * @return EncaminhamentoDenunciaBO
     */
    private function getEncaminhamentoDenunciaBO()
    {
        if (empty($this->encaminhamentoDenunciaBO)) {
            $this->encaminhamentoDenunciaBO = app()->make(EncaminhamentoDenunciaBO::class);
        }

        return $this->encaminhamentoDenunciaBO;
    }


    /**
     * Retorna uma nova instância de 'HistoricoDenunciaBO'.
     *
     * @return HistoricoDenunciaBO
     */
    private function getHistoricoDenunciaBO()
    {
        if (empty($this->historicoDenunciaBO)) {
            $this->historicoDenunciaBO = app()->make(HistoricoDenunciaBO::class);
        }

        return $this->historicoDenunciaBO;
    }

    /**
     * Retorna uma nova instância de 'MembroChapaBO'.
     *
     * @return MembroChapaBO
     */
    private function getMembroChapaBO()
    {
        if (empty($this->membroChapaBO)) {
            $this->membroChapaBO = app()->make(MembroChapaBO::class);
        }

        return $this->membroChapaBO;
    }

    /**
     * Retorna uma nova instância de 'DenunciaMembroChapaBO'.
     *
     * @return DenunciaMembroChapaBO
     */
    private function getDenunciaMembroChapaBO()
    {
        if (empty($this->denunciaMembroChapaBO)) {
            $this->denunciaMembroChapaBO = app()->make(DenunciaMembroChapaBO::class);
        }

        return $this->denunciaMembroChapaBO;
    }

    /**
     * Retorna uma nova instância de 'DenunciaMembroComissaoBO'.
     *
     * @return DenunciaMembroComissaoBO
     */
    private function getDenunciaMembroComissaoBO()
    {
        if (empty($this->denunciaMembroComissaoBO)) {
            $this->denunciaMembroComissaoBO = app()->make(DenunciaMembroComissaoBO::class);
        }

        return $this->denunciaMembroComissaoBO;
    }

    /**
     * Retorna uma nova instância de 'ProfissionalBO'.
     *
     * @return ProfissionalBO
     */
    private function getProfissionalBO()
    {
        if (empty($this->profissionalBO)) {
            $this->profissionalBO = app()->make(ProfissionalBO::class);
        }

        return $this->profissionalBO;
    }

    /**
     * Retorna uma nova instância de 'PessoaBO'.
     *
     * @return PessoaBO
     */
    private function getPessoaBO()
    {
        if (empty($this->pessoaBO)) {
            $this->pessoaBO = app()->make(PessoaBO::class);
        }

        return $this->pessoaBO;
    }

    /**
     * Retorna uma nova instância de 'ArquivoService'.
     *
     * @return ArquivoService
     */
    private function getarquivoService()
    {
        if (empty($this->arquivoService)) {
            $this->arquivoService = app()->make(ArquivoService::class);
        }

        return $this->arquivoService;
    }

    /**
     * Disponibiliza o arquivo 'Denúncia Provas' para 'download' conforme o 'id' informado.
     *
     * @param $idArquivo
     *
     * @return \App\To\ArquivoTO
     * @throws \App\Exceptions\NegocioException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getArquivo($idArquivo)
    {
        $arquivoDenunciaProvas = $this->getArquivoDenunciaProvas($idArquivo);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenunciaProvas($arquivoDenunciaProvas->getDenunciaProvas()->getId());

        return $this->getArquivoService()->getArquivo($caminho, $arquivoDenunciaProvas->getNomeFisicoArquivo(), $arquivoDenunciaProvas->getNome());
    }

    /**
     * Recupera a entidade 'ArquivoDenunciaProvas' por meio do 'id' informado.
     *
     * @param $id
     *
     * @return \App\Entities\ArquivoDenunciaProvas|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getArquivoDenunciaProvas($id)
    {
        $arrayArquivo = $this->arquivoDenunciaProvasRepository->getPorId($id);

        return $arrayArquivo[0];
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
