<?php
/*
 * DenunciaDefesaBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoDenunciaDefesa;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Denuncia;
use App\Entities\DenunciaDefesa;
use App\Entities\Filial;
use App\Entities\Historico;
use App\Entities\HistoricoDenuncia;
use App\Entities\Pessoa;
use App\Entities\Profissional;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\UsuarioFactory;
use App\Jobs\EnviarEmailDenunciaDefesaJob;
use App\Repository\FilialRepository;
use App\Repository\ProfissionalRepository;
use App\Service\ArquivoService;
use App\Service\CalendarioApiService;
use App\Service\CorporativoService;
use App\To\DenunciaDefesaTO;
use App\To\DenunciaInadmitidaTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'Denuncia Defesa'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DenunciaDefesaBO extends AbstractBO
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
     * @var \App\Repository\DenunciaDefesaRepository
     */
    private $denunciaDefesaRepository;

    /**
     * @var \App\Repository\ArquivoDenunciaDefesaRepository
     */
    private $arquivoDenunciaDefesaRepository;

    /**
     * @var FilialRepository
     */
    private $filialRepository;

    /**
     * @var ProfissionalRepository
     */
    private $profissionalRepository;

    /**
     * @var \App\Repository\AtividadeSecundariaCalendarioRepository
     */
    private $atividadeSecundariaRepository;

    /**
     * @var UsuarioFactory
     */
    private $usuarioFactory;

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
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     * @var PessoaBO
     */
    private $pessoaBO;

    /**
     * @var EleicaoBO
     */
    private $eleicaoBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var CalendarioApiService
     */
    private $calendarioApiService;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->filialRepository = $this->getRepository(Filial::class);
        $this->profissionalRepository = $this->getRepository(Profissional::class);
        $this->denunciaDefesaRepository = $this->getRepository(DenunciaDefesa::class);
        $this->arquivoDenunciaDefesaRepository = $this->getRepository(ArquivoDenunciaDefesa::class);
        $this->atividadeSecundariaRepository = $this->getRepository(AtividadeSecundariaCalendario::class);
    }

    /**
     * Salva a Defesa da denuncia.
     *
     * @param DenunciaDefesa $denunciaDefesa
     * @return mixed
     * @throws NegocioException
     * @throws \Exception
     */
    public function salvar(DenunciaDefesa $denunciaDefesa)
    {
        $denunciaInadmitidaSalva = null;
        $this->validarCamposObrigatoriosDefesa($denunciaDefesa);
        $this->validarQuantidadeArquivosDefesa($denunciaDefesa);
        $arquivos = (!empty($denunciaDefesa->getArquivosDenunciaDefesa())) ? clone $denunciaDefesa->getArquivosDenunciaDefesa() : null;
        $denunciaDefesa = $this->setNomeArquivoFisicoDefesa($denunciaDefesa);
        $denunciaDefesa->setArquivosDenunciaDefesa(null);
        $denuncia = null;

        try {
            $this->beginTransaction();

            $denuncia = $this->getDenunciaBO()->getDenuncia($denunciaDefesa->getDenuncia()->getId());

            $this->validaPrazoDefesaDenuncia($denuncia);

            $denunciaDefesa->setDenuncia($denuncia);
            $denunciaDefesa->setDataDefesa(Utils::getData());

            $denunciaDefesaSalva = $this->denunciaDefesaRepository->persist($denunciaDefesa);

            if (!empty($arquivos)) {
                $this->salvarArquivosDenunciaDefesa($arquivos, $denunciaDefesaSalva, $denuncia);
            }

            $this->getDenunciaBO()->salvarSituacaoDenuncia($denuncia, Constants::STATUS_DENUNCIA_EM_RELATORIA);

            $historicoDenunciaDefesa = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
                Constants::ACAO_HISTORICO_DENUNCIA_DEFESA);
            $this->getHistoricoDenunciaBO()->salvar($historicoDenunciaDefesa);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if (!empty($denunciaDefesaSalva)) {
            Utils::executarJOB(new EnviarEmailDenunciaDefesaJob($denuncia->getId()));
        }

        return DenunciaDefesaTO::newInstanceFromEntity($denunciaDefesaSalva);
    }

    /**
     * Retorna a Defesa de um membro de chapa ou comissão por Denunciado
     *
     * @return DenunciaDefesaTO|null
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorDenunciado()
    {
        $usuarioLogado = $this->getUsuarioFactory()->getUsuarioLogado();
        $defesa = $this->denunciaDefesaRepository->getPorDenunciado($usuarioLogado->idProfissional);

        if (empty($defesa)) {
            throw new NegocioException(Message::MSG_NAO_EXISTE_DEFESA_DENUNCIA);
        }
        return $defesa;
    }

    /**
     * Disponibiliza o arquivo de 'Denúncia Defesa' para 'download' conforme o 'id' informado
     *
     * @param $idArquivo
     * @return \App\To\ArquivoTO
     * @throws NegocioException
     */
    public function getArquivo($idArquivo)
    {
        $arquivoDenuncia = $this->getArquivoDenunciaDefesa($idArquivo);
        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenunciaDefesa($arquivoDenuncia->getDenunciaDefesa()->getDenuncia()->getId());
        return $this->getArquivoService()->getArquivo($caminho, $arquivoDenuncia->getNomeFisicoArquivo(), $arquivoDenuncia->getNome());
    }

    /**
     * Evia o Email da defesa de acordo com os parametros informados.
     *
     * @param $idDenuncia
     * @param bool $isJobPrazo
     * @return bool
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enviarEmailDenunciaDefesa($idDenuncia, $isJobPrazo = false)
    {
        $denuncia = $this->getDenunciaBO()->getDenuncia($idDenuncia);

        if ($denuncia->getTipoDenuncia()->getId() != Constants::TIPO_OUTROS) {

            $atvSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getAtividadeSecundariaPorNiveis(
                Constants::NIVEL_ATIVIDADE_PRINCIPAL_APRESENTAR_DEFESA,
                Constants::NIVEL_ATIVIDADE_SECUNDARIA_APRESENTAR_DEFESA
            );

            $nomeTemplate = '';
            $idCauUf = 0;
            $parametrosEmail = $this->prepararParametrosEmail($denuncia, $atvSecundaria);

            if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_CHAPA) {

                $idCauUf = $denuncia->getDenunciaChapa()->getChapaEleicao()->getIdCauUf();
                $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_DEFESA_CHAPA;

                if(!$isJobPrazo){
                    $this->enviarEmailResponsavelChapaDenuncia($atvSecundaria->getId(), $parametrosEmail, $nomeTemplate, $denuncia->getDenunciaChapa()->getChapaEleicao()->getId());
                }
            } else if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_CHAPA) {

                $idCauUf = $denuncia->getDenunciaMembroChapa()->getMembroChapa()->getChapaEleicao()->getIdCauUf();
                $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_DEFESA_MEMBRO_CHAPA;

                if(!$isJobPrazo) {
                    $this->enviarEmailResponsavelMembroChapa($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);
                }
            } else if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_COMISSAO) {

                $idCauUf = $denuncia->getDenunciaMembroComissao()->getMembroComissao()->getIdCauUf();
                $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_DEFESA_MEMBRO_COMISSAO;

                if(!$isJobPrazo) {
                    $this->enviarEmailResponsavelMembroComissao($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);
                }
            }

            $this->enviarEmailRelatorDenuncia($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);

            if(!$isJobPrazo) {
                $this->enviarEmailResponsavelAssessor($atvSecundaria->getId(), $parametrosEmail, $idCauUf, $nomeTemplate);
            }

        }
        return true;
    }

    /**
     * Rotina de mudança de situação da denúncia caso o prazo de recurso encerrar
     *
     * @return void
     * @throws \Exception
     */
    public function rotinaPrazoEncerradoApresentacaoDefesa()
    {
        $eleicao = $this->getEleicaoBO()->getEleicaoVigentePorNivelAtividade(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_APRESENTAR_DEFESA,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_APRESENTAR_DEFESA
        );

        $filtroTO = new \stdClass();
        $filtroTO->idEleicao = $eleicao->getId();
        $filtroTO->idSituacao = Constants::STATUS_DENUNCIA_AGUARDANDO_DEFESA;

        $denuncias = $this->getDenunciaBO()->getDenunciaAguardandoDefesaParaRotinaPrazoDefesaEncerrado($filtroTO);

        /** @var Denuncia $denuncia */
        foreach ($denuncias as $denuncia) {
            if (null === $denuncia->getDenunciaDefesa() && $this->validaPrazoDefesaEncerradoPorDenuncia($denuncia)) {
                $objDenuncia = $this->getDenunciaBO()->findById($denuncia->getId());

                if (empty($denuncia->getRecursoDenuncia())) {
                    $this->getDenunciaBO()->alterarStatusSituacaoDenuncia(
                        $objDenuncia, Constants::STATUS_DENUNCIA_EM_RELATORIA
                    );

                    $historicoRotinaRecurso = $this->getHistoricoDenunciaBO()->criarHistorico($objDenuncia,
                        Constants::ACAO_HISTORICO_PRAZO_ENCERRADO_DEFESA);
                    $this->getHistoricoDenunciaBO()->salvar($historicoRotinaRecurso);
                }
            }
        }
    }

    /**
     * Envia o e-mail para o Assessor da Comissão Eleitoral da UF do denunciado
     *
     * @param int $idAtivSecundaria
     * @param array $parametrosEmail
     * @param $idCauUf
     * @param string $nomeTemplate
     * @throws NegocioException
     */
    private function enviarEmailResponsavelAssessor(
        $idAtivSecundaria,
        $parametrosEmail,
        $idCauUf,
        $nomeTemplate
    ) {
        if (empty($idCauUf)) {
            $ids[] = Constants::COMISSAO_MEMBRO_CAU_BR_ID;
            $ids[] = Constants::IES_ID;
        } else {
            $ids[] = $idCauUf;
        }

        $assessores = $this->getCorporativoService()->getUsuariosAssessoresCE($ids);

        if (!empty($assessores)) {
            foreach ($assessores as $destinatario) {
                $destinatarios[] = $destinatario->getEmail();
            }
            $this->enviarEmailDefesaDenuncia($idAtivSecundaria, $destinatarios, $parametrosEmail,
                $nomeTemplate, Constants::EMAIL_APRESENTACAO_DEFESA_ASSESSORES);
        }
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
    ) {
        $responsaveis = $this->getMembroChapaBO()->getMembrosResponsaveisChapa(
            $idChapa,
            Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
        );
        $destinatariosEmail = $this->getMembroChapaBO()->getListEmailsDestinatarios($responsaveis);
        $this->enviarEmailDefesaDenuncia($idAtivSecundaria, $destinatariosEmail, $parametrosEmail,
            $nomeTemplate, Constants::EMAIL_APRESENTACAO_DEFESA_DENUNCIADO);
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
    ) {
        $destinatariosEmail = $this->getDenunciaMembroChapaBO()->getDadosDenunciante($denunciaSalva->getId());
        $destinatariosEmail = $destinatariosEmail[0];
        $this->enviarEmailDefesaDenuncia($idAtivSecundaria, $destinatariosEmail, $parametrosEmail,
            $nomeTemplate, Constants::EMAIL_APRESENTACAO_DEFESA_DENUNCIADO);
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
    ) {
        $destinatariosEmail = $this->getDenunciaMembroComissaoBO()->getDadosDenunciante($denunciaSalva->getId());
        $destinatariosEmail = $destinatariosEmail[0];
        $this->enviarEmailDefesaDenuncia($idAtivSecundaria, $destinatariosEmail, $parametrosEmail,
            $nomeTemplate, Constants::EMAIL_APRESENTACAO_DEFESA_DENUNCIADO);
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
    ) {
        $pessoaId = $denunciaSalva->getUltimaDenunciaAdmitida()->getMembroComissao()->getPessoa();
        $profissional = $this->getProfissionalBO()->getPorId($pessoaId);
        $pessoaEmail = $this->getPessoaBO()->getPessoaPorId($profissional->getPessoaId())->getEmail();

        $destinatarios[] = $pessoaEmail;
        $this->enviarEmailDefesaDenuncia($idAtivSecundaria, $destinatarios, $parametrosEmail,
            $nomeTemplate, Constants::EMAIL_APRESENTACAO_DEFESA_RELATOR_ATUAL);
    }

    /**
     * Prepara os parametros para o Envio de email
     *
     * @param Denuncia $denunciaSalva
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @return array
     */
    private function prepararParametrosEmail(Denuncia $denunciaSalva, AtividadeSecundariaCalendario $atividadeSecundaria)
    {
        $anoEleicao = $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada();
        $tipoDenuncia = '';
        $idCauUf = !empty($denunciaSalva->getFilial()) ? $denunciaSalva->getFilial()->getId() : Constants::IES_ID;
        $denunciaDefesa = Constants::NAO_HOUVE_APRESENTACAO_DEFESA_DENUNCIA;

        if(!empty($denunciaSalva->getDenunciaDefesa())){
            $denunciaDefesa = $denunciaSalva->getDenunciaDefesa()->getDescricaoDefesa();
        }

        $numeroChapa = null;
        $nomeDenunciado = '';
        $idProfissional = null;

        $protocolo = $denunciaSalva->getNumeroSequencial() ? $denunciaSalva->getNumeroSequencial() : null;

        if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_CHAPA) {

            $tipoDenuncia = Constants::TIPO_DENUNCIA_CHAPA;
            $numeroChapa = $denunciaSalva->getDenunciaChapa()->getChapaEleicao()->getNumeroChapa();

        } else if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_CHAPA) {

            $tipoDenuncia = Constants::TIPO_DENUNCIA_MEMBRO_CHAPA;
            $idProfissional = $denunciaSalva->getDenunciaMembroChapa()->getMembroChapa()->getProfissional()->getId();
            $numeroChapa = $denunciaSalva->getDenunciaMembroChapa()->getMembroChapa()->getChapaEleicao()->getNumeroChapa();

        } else if ($denunciaSalva->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_COMISSAO) {

            $tipoDenuncia = Constants::TIPO_DENUNCIA_MEMBRO_COMISSAO;
            $idCauUf = $denunciaSalva->getDenunciaMembroComissao()->getMembroComissao()->getIdCauUf();
            $idProfissional = $denunciaSalva->getDenunciaMembroComissao()->getMembroComissao()->getPessoa();

        }

        $filialPrefixo = Constants::PREFIXO_IES;
        if ($idCauUf != Constants::IES_ID) {
            $filial = $this->filialRepository->find($idCauUf);
            $filialPrefixo = $filial->getPrefixo();
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
            Constants::PARAMETRO_EMAIL_DS_DEFESA => $denunciaDefesa
        ];
    }

    /**
     * Método auxiliar que busca o e-mail definido e realiza o envio
     *
     * @param $idAtividadeSecundaria
     * @param array $emailsDestinatarios
     * @param array|null $parametrosExtras
     * @param $nomeTemplate
     * @param $tipo
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function enviarEmailDefesaDenuncia(
        $idAtividadeSecundaria,
        $emailsDestinatarios,
        $parametrosExtras = [],
        $nomeTemplate,
        $tipo
    )
    {
        $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
            $idAtividadeSecundaria, $tipo
        );

        $this->getEmailAtividadeSecundariaBO()->enviarEmailAtividadeSecundaria(
            $emailAtividadeSecundaria,
            $emailsDestinatarios,
            $nomeTemplate,
            $parametrosExtras
        );
    }

    /**
     * Método auxiliar para Salvar dados dos Arquivos para a Denuncia Defesa
     *
     * @param $arquivosDefesa
     * @param DenunciaDefesa $denunciaDefesa
     * @param Denuncia $denuncia
     * @return DenunciaDefesa
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function salvarArquivosDenunciaDefesa($arquivosDefesa, DenunciaDefesa $denunciaDefesa, Denuncia $denuncia)
    {
        $arquivosSalvos = new ArrayCollection();

        if (!empty($arquivosDefesa)) {
            foreach ($arquivosDefesa as $arquivoDefesa) {
                $arquivoDefesa->setDenunciaDefesa($denunciaDefesa);
                $arquivoSalvo = $this->arquivoDenunciaDefesaRepository->persist($arquivoDefesa);
                $arquivosSalvos->add($arquivoSalvo);
                $this->salvaArquivosDiretorio($arquivoDefesa, $denuncia);
                $arquivoSalvo->setArquivo(null);
            }
        }
        $denunciaDefesa->setArquivosDenunciaDefesa($arquivosSalvos);
        $denunciaDefesa->removerFiles();

        return $denunciaDefesa;
    }

    /**
     * Realiza o(s) upload(s) do(s) arquivo(s) da Denuncia
     *
     * @param Denuncia $denunciaSalva
     */
    private function salvaArquivosDiretorio($arquivo, Denuncia $denunciaSalva)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenunciaDefesa($denunciaSalva->getId());

        if ($arquivo != null) {
            if (!empty($arquivo->getArquivo())) {
                $this->getArquivoService()->salvar($caminho, $arquivo->getNomeFisicoArquivo(), $arquivo->getArquivo());
            }
        }
    }

    /**
     * Cria os nomes de arquivo para Denuncia Inadmitida
     *
     * @param DenunciaDefesa $denunciaDefesa
     * @return DenunciaDefesa
     */
    private function setNomeArquivoFisicoDefesa(DenunciaDefesa $denunciaDefesa)
    {
        if (!empty($denunciaDefesa->getArquivosDenunciaDefesa())) {
            foreach ($denunciaDefesa->getArquivosDenunciaDefesa() as $arquivo) {
                if (empty($arquivo->getId())) {
                    $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                        $arquivo->getNome(),
                        Constants::PREFIXO_DOC_DENUNCIA_DEFESA
                    );
                    $arquivo->setNomeFisicoArquivo($nomeArquivoFisico);
                }
            }
        }
        return $denunciaDefesa;
    }

    /**
     * Valida se os campos obrigatórios estão preenchidos
     *
     * @param DenunciaDefesa $denunciaDefesa
     * @throws NegocioException
     */
    private function validarCamposObrigatoriosDefesa(DenunciaDefesa $denunciaDefesa)
    {
        $campos = [];

        if (empty($denunciaDefesa->getDescricaoDefesa())) {
            $campos[] = 'LABEL_APRESENTACAO_DEFESA';
        }

        if (empty($denunciaDefesa->getDenuncia()->getId())) {
            $campos[] = 'LABEL_ID_DENUNCIA';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Valida a quantidade de arquivos para a Denuncia Defesa
     *
     * @param DenunciaDefesa $denunciaDefesa
     * @throws NegocioException
     */
    private function validarQuantidadeArquivosDefesa(DenunciaDefesa $denunciaDefesa)
    {
        $arquivos = $denunciaDefesa->getArquivosDenunciaDefesa();
        if (!empty($arquivos) and count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO_DENUNCIA_DEFESA) {
            throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO);
        }
    }

    /**
     * Retorna as informações da defesa para exportação em pdf.
     *
     * @param $idDenuncia
     * @return \stdClass|null
     * @throws \Exception
     */
    public function getExportarInformacoesDefesa($idDenuncia)
    {
        $denuncia = $this->getDenunciaBO()->findById($idDenuncia);

        $defesa = new \stdClass();
        $defesa->defesa = null;
        $defesa->isPrazoEncerrado = $this->getDenunciaBO()->validarDefesaPrazoEncerradoPorDenuncia($denuncia);

        if (!empty($denuncia->getDenunciaDefesa())) {
            $defesa->defesa = DenunciaDefesaTO::newInstanceFromEntity($denuncia->getDenunciaDefesa());

            $historico = $this->getHistoricoDenunciaBO()->getHistoricoDenunciaPorDenunciaEAcao(
                $idDenuncia, Constants::ACAO_HISTORICO_DENUNCIA_DEFESA
            );
            $pessoa = $this->getPessoaBO()->getPessoaPorId($historico->getResponsavel());
            $defesa->defesa->setUsuario($pessoa->getProfissional()->getNome());


            $arquivos = $denuncia->getDenunciaDefesa()->getArquivosDenunciaDefesa();
            if (!empty($arquivos)) {
                $documentos = null;
                foreach ($arquivos as $arquivo){
                    $documentos[] = $this->getArquivoService()->getDescricaoArquivo(
                        $this->getArquivoService()->getCaminhoRepositorioDenunciaDefesa($idDenuncia),
                        $arquivo->getNomeFisicoArquivo(), $arquivo->getNome()
                    );
                }
                $defesa->defesa->setDescricaoArquivo($documentos);
            }
        }

        return $defesa;
    }

    /**
     * Verifica o prazo de defesa da Denuncia.
     *
     * @param Denuncia $denuncia
     *
     * @return bool
     * @throws \Exception
     */
    private function validaPrazoDefesaEncerradoPorDenuncia(Denuncia $denuncia)
    {
        $dataLimite = null;
        $denunciaAdmitida = $denuncia->getUltimaDenunciaAdmitida();

        if (null !== $denunciaAdmitida) {
            $ano      = Utils::getAnoData($denunciaAdmitida->getDataAdmissao());
            $feriados = $this->getCalendarioApiService()
                             ->getFeriadosNacionais($ano);

            $dataLimite = Utils::adicionarDiasUteisData(
                $denunciaAdmitida->getDataAdmissao(),
                Constants::PRAZO_DEFESA_DENUNCIA_DIAS,
                $feriados
            );
        }

        return null !== $dataLimite && Utils::getDataHoraZero() > Utils::getDataHoraZero($dataLimite);
    }

    /**
     * Método para retornar a instancia de Historico Denuncia BO
     *
     * @return HistoricoDenunciaBO
     */
    private function getHistoricoDenunciaBO()
    {
        if (empty($this->historicoDenunciaBO)) {
            $this->historicoDenunciaBO = new HistoricoDenunciaBO();
        }
        return $this->historicoDenunciaBO;
    }

    /**
     * Método para retornar a instancia de DenunciaBO
     *
     * @return DenunciaBO
     */
    private function getDenunciaBO()
    {
        if (empty($this->denunciaBO)) {
            $this->denunciaBO = new DenunciaBO();
        }
        return $this->denunciaBO;
    }

    /**
     * Retorna uma instancia de Arquivo Service
     *
     * @return ArquivoService
     * @var \App\Service\ArquivoService
     */
    private function getArquivoService()
    {
        if (empty($this->arquivoService)) {
            $this->arquivoService = new ArquivoService();
        }
        return $this->arquivoService;
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
     * Recupera a entidade 'ArquivoDenunciaDefesa' por meio do 'id' informado.
     *
     * @param $id
     * @return |null
     */
    private function getArquivoDenunciaDefesa($id)
    {
        return $this->arquivoDenunciaDefesaRepository->find($id);
    }

    /**
     * Método para retornar a instancia de Corporativo Service
     *
     * @return CorporativoService
     */
    private function getCorporativoService()
    {
        if (empty($this->corporativoService)) {
            $this->corporativoService = new CorporativoService();
        }
        return $this->corporativoService;
    }

    /**
     * Método para retornar a instancia de Calendario Api Service
     *
     * @return CalendarioApiService
     */
    private function getCalendarioApiService()
    {
        if (empty($this->calendarioApiService)) {
            $this->calendarioApiService = new CalendarioApiService();
        }
        return $this->calendarioApiService;
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

    /**
     * Verifica o prazo de defesa da Denuncia.
     *
     * @param Denuncia $denuncia
     *
     * @throws NegocioException
     * @throws \Exception
     */
    public function validaPrazoDefesaDenuncia(Denuncia $denuncia)
    {
        $isPrazoEncerrado = $this->validaPrazoDefesaEncerradoPorDenuncia($denuncia);

        if (null !== $denuncia->getDenunciaDefesa() || $isPrazoEncerrado) {
            throw new NegocioException(Message::MSG_PRAZO_DEFESA_ENCERRADO);
        }
    }

    /**
     * Recupera as Denuncias Admitidas sem Defesa ate a data de expiração.
     *
     * @throws \Exception
     */
    public function enviarEmailDenunciasDefesaExpira()
    {
        $dataHoje = Utils::getData();
        $ano = Utils::getAnoData($dataHoje);
        $feriados = $this->getCalendarioApiService()->getFeriadosNacionais($ano);

        $dataLimiteRemovida = Utils::removeDiasUteisData(
            $dataHoje,
            Constants::PRAZO_DEFESA_DENUNCIA_DIAS,
            $feriados
        );

        $denuncias = $this->getDenunciaBO()->getDenunciaAdmitidaPorDataAdmissao($dataLimiteRemovida);

        foreach ($denuncias as $denuncia) {
            Utils::executarJOB(new EnviarEmailDenunciaDefesaJob($denuncia->getId(), true));
        }
    }
}
