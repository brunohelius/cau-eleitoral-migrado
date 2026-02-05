<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 08/05/2020
 * Time: 09:07
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AgendamentoEncaminhamentoDenuncia;
use App\Entities\ArquivoDenunciaAudienciaInstrucao;
use App\Entities\ArquivoDenunciaDefesa;
use App\Entities\ArquivoDenunciaProvas;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Denuncia;
use App\Entities\DenunciaAudienciaInstrucao;
use App\Entities\DenunciaDefesa;
use App\Entities\DenunciaProvas;
use App\Entities\EncaminhamentoDenuncia;
use App\Entities\Filial;
use App\Entities\Pessoa;
use App\Entities\Profissional;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Factory\UsuarioFactory;
use App\Jobs\EnviarEmailDenunciaAudienciaInstrucaoJob;
use App\Jobs\EnviarEmailDenunciaAudienciaInstrucaoPendenteJob;
use App\Jobs\EnviarEmailDenunciaDefesaJob;
use App\Jobs\EnviarEmailDenunciaProvasJob;
use App\Repository\FilialRepository;
use App\Repository\ProfissionalRepository;
use App\Service\ArquivoService;
use App\Service\CalendarioApiService;
use App\Service\CorporativoService;
use App\To\DenunciaAudienciaInstrucaoTO;
use App\To\DenunciaDefesaTO;
use App\To\DenunciaInadmitidaTO;
use App\To\DenunciaProvasTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'DenunciaAudienciaInstrucaoBO'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DenunciaAudienciaInstrucaoBO extends AbstractBO
{
    /**
     * @var \App\Service\ArquivoService
     */
    private $arquivoService;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var DenunciaBO
     */
    private $denunciaBO;

    /**
     * @var EncaminhamentoDenunciaBO
     */
    private $encaminhamentoDenunciaBO;

    /**
     * @var HistoricoDenunciaBO
     */
    private $historicoDenunciaBO;

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
     * @var FilialBO
     */
    private $filialBO;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * @var \App\Repository\DenunciaAudienciaInstrucaoRepository
     */
    private $denunciaAudienciaInstrucaoRepository;

    /**
     * @var \App\Repository\ArquivoDenunciaAudienciaInstrucaoRepository
     */
    private $arquivoDenunciaAudienciaInstrucaoRepository;

    /**
     * @var \App\Repository\EncaminhamentoDenunciaRepository
     */
    private $encaminhamentoDenunciaRepository;

    /**
     * @var FilialRepository
     */
    private $filialRepository;

    /**
     * @var ProfissionalRepository
     */
    private $profissionalRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->filialRepository = $this->getRepository(Filial::class);
        $this->profissionalRepository = $this->getRepository(Profissional::class);
        $this->encaminhamentoDenunciaRepository = $this->getRepository(EncaminhamentoDenuncia::class);
        $this->denunciaAudienciaInstrucaoRepository = $this->getRepository(DenunciaAudienciaInstrucao::class);
        $this->arquivoDenunciaAudienciaInstrucaoRepository = $this->getRepository(ArquivoDenunciaAudienciaInstrucao::class);
    }

    /**
     * Salva a Audiencia de Instrução
     *
     * @param DenunciaAudienciaInstrucao $audienciaInstrucao
     * @return mixed
     * @throws NegocioException
     */
    public function salvar(DenunciaAudienciaInstrucao $audienciaInstrucao)
    {
        $this->validarCamposObrigatorios($audienciaInstrucao);
        $this->validarQuantidadeArquivos($audienciaInstrucao);
        $this->validaDataHoraAudienciaIntrucao($audienciaInstrucao);

        $arquivos = (!empty($audienciaInstrucao->getArquivosAudienciaInstrucao()))
            ? clone $audienciaInstrucao->getArquivosAudienciaInstrucao() : null;
        $audienciaInstrucao = $this->setNomeArquivoFisico($audienciaInstrucao);
        $audienciaInstrucao->setArquivosAudienciaInstrucao(null);
        $denuncia = null;

        try {
            $this->beginTransaction();
            $denuncia = $this->getDenunciaBO()->getDenuncia($audienciaInstrucao->getDenuncia()->getId());
            $encaminhamento = $this->getEncaminhamentoDenunciaBO()->getEncaminhamentoDenunciaPorId(
                $audienciaInstrucao->getEncaminhamentoDenuncia()->getId()
            );

            $audienciaInstrucao->setDenuncia($denuncia);
            $audienciaInstrucao->setEncaminhamentoDenuncia($encaminhamento);
            $audienciaInstrucao->setDataCadastro(Utils::getData());

            //TODO - recebe junto com o FROM, somente para testes.
            //$audienciaInstrucao->setDataAudienciaInstrucao(Utils::getData());

            $audienciaInstrucaoSalva = $this->denunciaAudienciaInstrucaoRepository->persist($audienciaInstrucao);

            $tipoSituacao = $this->getEncaminhamentoDenunciaBO()
                ->getTipoSituacaoEncaminhamentoPorId(Constants::TIPO_SITUACAO_ENCAMINHAMENTO_CONCLUIDO);

            $encaminhamento->setTipoSituacaoEncaminhamento($tipoSituacao);
            $this->encaminhamentoDenunciaRepository->persist($encaminhamento);

            $this->alteraStatusEncaminhamentosAudienciaPendentesDenuncia($denuncia, $encaminhamento);

            if (!empty($arquivos)) {
                $this->salvarArquivos($arquivos, $audienciaInstrucaoSalva);
            }

            $historicoDenuncia = $this->getHistoricoDenunciaBO()->criarHistorico($denuncia,
                Constants::ACAO_HISTORICO_DENUNCIA_AUDIENCIA_INSTRUCAO);
            $this->getHistoricoDenunciaBO()->salvar($historicoDenuncia);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        if (!empty($audienciaInstrucaoSalva)) {
            Utils::executarJOB(new EnviarEmailDenunciaAudienciaInstrucaoJob(
                $denuncia->getId(),
                $audienciaInstrucaoSalva->getId()
            ));
        }

        return DenunciaAudienciaInstrucaoTO::newInstanceFromEntity($audienciaInstrucaoSalva);
    }

    /**
     * Evia o Email da defesa de acordo com os parametros informados.
     *
     * @param $idDenuncia
     * @param $idAudienciaInstrucao
     * @return bool
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enviarEmailDenunciaAudienciaInstrucao($idDenuncia, $idAudienciaInstrucao)
    {
        $denuncia = $this->getDenunciaBO()->getDenuncia($idDenuncia);
        $audienciaInstrucao = $this->denunciaAudienciaInstrucaoRepository->find($idAudienciaInstrucao);

        $atvSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getAtividadeSecundariaPorNiveis(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_REGISTRAR_AUDIENCIA_INSTRUCAO,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_REGISTRAR_AUDIENCIA_INSTRUCAO
        );

        $nomeTemplate = '';
        $parametrosEmail = $this->prepararParametrosEmail($denuncia, $atvSecundaria, $audienciaInstrucao);

        if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_CHAPA) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_CHAPA;

            $this->enviarEmailResponsavelChapaDenuncia($atvSecundaria->getId(), $parametrosEmail, $nomeTemplate, $denuncia->getDenunciaChapa()->getChapaEleicao()->getId());
        } else if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_CHAPA) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_MEMBRO_CHAPA;

            $this->enviarEmailResponsavelMembroChapa($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);
        } else if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_COMISSAO) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_MEMBRO_COMISSAO;

            $this->enviarEmailResponsavelMembroComissao($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);
        } else if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_OUTROS) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_OUTROS;
        }

        $this->enviarEmailResponsavelDenunciante($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);
        $this->enviarEmailRelatorDenuncia($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);
        $this->enviarEmailResponsavelAssessor($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);

        return true;
    }

    /**
     * Processa as Audiencias Instrução pendentes de ontem e envia o Email.
     *
     */
    public function enviarEmailDenunciasAudienciaInstrucaoPendentes()
    {

        $encaminhamentosAudiencia =
            $this->encaminhamentoDenunciaRepository->getEncaminhamentoAudienciaInstrucaoPorData();

        foreach ($encaminhamentosAudiencia as $audienciaInstrucao) {
            $denuncia = $this->getDenunciaBO()->getDenunciaPorId($audienciaInstrucao->getDenuncia()->getId());

            if (!empty($audienciaInstrucao)) {
                Utils::executarJOB(new EnviarEmailDenunciaAudienciaInstrucaoPendenteJob(
                    $denuncia->getId(),
                    $audienciaInstrucao->getId()
                ));
            }
        }
    }

    /**
     * Evia o Email da defesa de acordo com os parametros informados.
     *
     * @param $idDenuncia
     * @param $idEncaminhamento
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enviarEmailDenunciaAudienciaInstrucaoPendente($idDenuncia, $idEncaminhamento)
    {
        $denuncia = $this->getDenunciaBO()->getDenuncia($idDenuncia);
        $encaminhamento = $this->getEncaminhamentoDenunciaBO()->getEncaminhamentoDenunciaPorId($idEncaminhamento);

        $atvSecundaria = $this->getAtividadeSecundariaCalendarioBO()->getAtividadeSecundariaPorNiveis(
            Constants::NIVEL_ATIVIDADE_PRINCIPAL_REGISTRAR_AUDIENCIA_INSTRUCAO,
            Constants::NIVEL_ATIVIDADE_SECUNDARIA_REGISTRAR_AUDIENCIA_INSTRUCAO
        );

        $nomeTemplate = '';
        $parametrosEmail = $this->prepararParametrosEmail($denuncia, $atvSecundaria, null, $encaminhamento);

        if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_CHAPA) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_PENDENTE_CHAPA;
        } else if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_CHAPA) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_PENDENTE_MEMBRO_CHAPA;
        } else if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_MEMBRO_COMISSAO) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_PENDENTE_MEMBRO_COMISSAO;
        } else if ($denuncia->getTipoDenuncia()->getId() == Constants::TIPO_OUTROS) {
            $nomeTemplate = Constants::TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_PENDENTE_OUTROS;
        }

        $this->enviarEmailRelatorDenuncia($atvSecundaria->getId(), $parametrosEmail, $denuncia, $nomeTemplate);
        return true;
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
    )
    {
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
            $this->enviarEmail($idAtivSecundaria, $destinatarios, $parametrosEmail, $nomeTemplate);
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
    )
    {
        $responsaveis = $this->getMembroChapaBO()->getMembrosResponsaveisChapa(
            $idChapa,
            Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
        );
        $destinatariosEmail = $this->getMembroChapaBO()->getListEmailsDestinatarios($responsaveis);
        $this->enviarEmail($idAtivSecundaria, $destinatariosEmail, $parametrosEmail, $nomeTemplate);
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
        $this->enviarEmail($idAtivSecundaria, $destinatariosEmail, $parametrosEmail, $nomeTemplate);
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
        $this->enviarEmail($idAtivSecundaria, $destinatariosEmail, $parametrosEmail, $nomeTemplate);
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
    private function enviarEmailResponsavelDenunciante(
        $idAtivSecundaria,
        $parametrosEmail,
        $denunciaSalva,
        $nomeTemplate
    )
    {
        $destinatariosEmail[] = $denunciaSalva->getPessoa()->getEmail();
        $this->enviarEmail($idAtivSecundaria, $destinatariosEmail, $parametrosEmail, $nomeTemplate);
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
        $this->enviarEmail($idAtivSecundaria, $destinatarios, $parametrosEmail, $nomeTemplate);
    }

    /**
     * Método auxiliar que busca o e-mail definido e realiza o envio
     *
     * @param $idAtividadeSecundaria
     * @param array $emailsDestinatarios
     * @param array|null $parametrosExtras
     * @param $nomeTemplate
     */
    private function enviarEmail(
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
     * Retorna a Audiencia de instrução conforme o id informado.
     *
     * @param $id
     * @return DenunciaAudienciaInstrucao|null
     * @throws DenunciaAudienciaInstrucao
     */
    public function getPorId($id)
    {
        return $this->denunciaAudienciaInstrucaoRepository->getPorId($id);
    }

    /**
     * Retorna a Audiencia de instrução conforme o id do encaminhamento.
     *
     * @param $id
     * @return DenunciaAudienciaInstrucao|null
     * @throws DenunciaAudienciaInstrucao
     */
    public function getPorEncaminhamento($id)
    {
        return $this->denunciaAudienciaInstrucaoRepository->getPorEncaminhamento($id);
    }

    /**
     * Método auxiliar para Salvar dados dos Arquivos
     *
     * @param $arquivos
     * @param DenunciaAudienciaInstrucao $audienciaInstrucao
     * @return DenunciaAudienciaInstrucao
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function salvarArquivos($arquivos, DenunciaAudienciaInstrucao $audienciaInstrucao)
    {
        $arquivosSalvos = new ArrayCollection();

        if (!empty($arquivos)) {
            foreach ($arquivos as $arquivo) {
                $arquivo->setDenunciaAudienciaInstrucao($audienciaInstrucao);
                $arquivoSalvo = $this->arquivoDenunciaAudienciaInstrucaoRepository->persist($arquivo);
                $arquivosSalvos->add($arquivoSalvo);
                $this->salvaArquivosDiretorio($arquivo, $audienciaInstrucao);
                $arquivoSalvo->setArquivo(null);
            }
        }
        $audienciaInstrucao->setArquivosAudienciaInstrucao($arquivosSalvos);
        $audienciaInstrucao->removerFiles();

        return $audienciaInstrucao;
    }

    /**
     * Realiza o(s) upload(s) do(s) arquivo(s)
     *
     * @param $arquivo
     * @param DenunciaAudienciaInstrucao $audienciaInstrucao
     */
    private function salvaArquivosDiretorio($arquivo, DenunciaAudienciaInstrucao $audienciaInstrucao)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioDenunciaAudienciaInstrucao(
            $audienciaInstrucao->getId()
        );

        if ($arquivo != null) {
            if (!empty($arquivo->getArquivo())) {
                $this->getArquivoService()->salvar($caminho, $arquivo->getNomeFisicoArquivo(), $arquivo->getArquivo());
            }
        }
    }

    /**
     * Valida se os campos obrigatórios estão preenchidos
     *
     * @param DenunciaAudienciaInstrucao $audienciaInstrucao
     * @throws NegocioException
     */
    private function validarCamposObrigatorios(DenunciaAudienciaInstrucao $audienciaInstrucao)
    {
        $campos = [];

        //TODO - Verificar Label no front
        if (empty($audienciaInstrucao->getDescricaoDenunciaAudienciaInstrucao())) {
            $campos[] = 'LABEL_DESCRICAO_PROVAS_APRESENTADAS';
        }

        if (empty($audienciaInstrucao->getDenuncia()->getId())) {
            $campos[] = 'LABEL_ID_DENUNCIA';
        }

        if (empty($audienciaInstrucao->getEncaminhamentoDenuncia()->getId())) {
            $campos[] = 'LABEL_ID_ENCAMINHAMENTO';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Valida a quantidade de arquivos
     *
     * @param DenunciaAudienciaInstrucao $audienciaInstrucao
     * @throws NegocioException
     */
    private function validarQuantidadeArquivos(DenunciaAudienciaInstrucao $audienciaInstrucao)
    {
        $arquivos = $audienciaInstrucao->getArquivosAudienciaInstrucao();
        if (!empty($arquivos) and count($arquivos) > Constants::QUANTIDADE_MAX_ARQUIVO) {
            throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO);
        }
    }

    /**
     * Cria os nomes dos arquivo.
     *
     * @param DenunciaAudienciaInstrucao $audienciaInstrucao
     * @return DenunciaAudienciaInstrucao
     */
    private function setNomeArquivoFisico(DenunciaAudienciaInstrucao $audienciaInstrucao)
    {
        if (!empty($audienciaInstrucao->getArquivosAudienciaInstrucao())) {
            foreach ($audienciaInstrucao->getArquivosAudienciaInstrucao() as $arquivo) {
                if (empty($arquivo->getId())) {
                    $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                        $arquivo->getNome(),
                        Constants::PREFIXO_DOC_AUDIENCIA_INSTRUCAO
                    );
                    $arquivo->setNomeFisicoArquivo($nomeArquivoFisico);
                }
            }
        }
        return $audienciaInstrucao;
    }

    /**
     * Prepara os parametros para o Envio de email
     *
     * @param DenunciaAudienciaInstrucao $audienciaInstrucao
     * @param Denuncia $denunciaSalva
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     * @param EncaminhamentoDenuncia|null $encaminhamentoDenuncia
     * @return array
     */
    private function prepararParametrosEmail(
        Denuncia $denunciaSalva,
        AtividadeSecundariaCalendario $atividadeSecundaria,
        DenunciaAudienciaInstrucao $audienciaInstrucao = null,
        EncaminhamentoDenuncia $encaminhamentoDenuncia = null
    )
    {
        $denuncia = $this->getDenunciaBO()->findById($denunciaSalva->getId());
        $anoEleicao = $atividadeSecundaria->getAtividadePrincipalCalendario()->getCalendario()->getEleicao()->getSequenciaFormatada();
        $tipoDenuncia = '';
        $descricaoDenunciaAudienciaInstrucao = '';
        $dataHoraAudiencia = '';

        if (!empty($audienciaInstrucao)) {
            $descricaoDenunciaAudienciaInstrucao = $audienciaInstrucao->getDescricaoDenunciaAudienciaInstrucao();
            $encaminhamento = $audienciaInstrucao->getEncaminhamentoDenuncia()->getDescricao();
            $dataHoraAudiencia = $this->getDescricaoAgendamentoAudienciaInstrucaoFormatada($audienciaInstrucao);
        } else {
            $encaminhamento = !empty($encaminhamentoDenuncia) ? $encaminhamentoDenuncia->getDescricao() : null;
        }

        $numeroChapa = null;
        $nomeDenunciado = '';
        $idProfissional = null;

        $protocolo = $denunciaSalva->getNumeroSequencial() ? $denunciaSalva->getNumeroSequencial() : null;

        $idCauUf = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getId() : Constants::IES_ID;
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

        $filialPrefixo = "";

        $filial = $idCauUf != Constants::IES_ID
            ? $this->filialRepository->find($idCauUf)
            : $this->getFilialBO()->getFilialIES();

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
            Constants::PARAMETRO_EMAIL_NM_PROTOCOLO => $protocolo,
            Constants::PARAMETRO_EMAIL_PROCESSO_ELEITORAL => $anoEleicao,
            Constants::PARAMETRO_EMAIL_TIPO_DENUNCIA => $tipoDenuncia,
            Constants::PARAMETRO_EMAIL_NOME_DENUNCIADO => $nomeDenunciado,
            Constants::PARAMETRO_EMAIL_NUMERO_CHAPA => $numeroChapa,
            Constants::PARAMETRO_EMAIL_UF => $filialPrefixo,
            Constants::PARAMETRO_EMAIL_DESCRICAO => $descricaoDenunciaAudienciaInstrucao,
            Constants::PARAMETRO_EMAIL_ENCAMINHAMENTO => $encaminhamento,
            Constants::PARAMETRO_EMAIL_DATA_HORA_AUDIENCIA_INSTRUCAO => $dataHoraAudiencia
        ];
    }

    /**
     * Retorna a descrição formatada do 'AgendamentoEncaminhamentoDenuncia'.
     *
     * @param DenunciaAudienciaInstrucao $audienciaInstrucao
     * @return string
     */
    private function getDescricaoAgendamentoAudienciaInstrucaoFormatada(DenunciaAudienciaInstrucao $audienciaInstrucao)
    {
        return Utils::getStringFromDate($audienciaInstrucao->getDataAudienciaInstrucao(), "d/m/Y")
            . " às " .
            Utils::getStringFromDate($audienciaInstrucao->getDataAudienciaInstrucao(), "H:i");
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
     * Método para retornar a instância de 'FilialBO'
     *
     * @return FilialBO
     */
    private function getFilialBO(): FilialBO
    {
        if (empty($this->filialBO)) {
            $this->filialBO = app()->make(FilialBO::class);
        }
        return $this->filialBO;
    }

    /**
     * Altera o Status do Encaminhamento Audiencia Pendente da Denuncia Iformada.
     *
     * @param Denuncia $denuncia
     * @param EncaminhamentoDenuncia $encaminhamento
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function alteraStatusEncaminhamentosAudienciaPendentesDenuncia(
        Denuncia $denuncia,
        EncaminhamentoDenuncia $encaminhamento
    )
    {
        $encaminhamentosDenuncia = $this->encaminhamentoDenunciaRepository
            ->getEncaminhamentosPendentesPorTipoEDenuncia(
                Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO,
                $denuncia->getId()
            );

        if (!empty($encaminhamentosDenuncia)) {
            $tipoSituacaoEncaminhamento = $this->getEncaminhamentoDenunciaBO()
                ->getTipoSituacaoEncaminhamentoPorId(Constants::TIPO_SITUACAO_ENCAMINHAMENTO_FECHADO);

            foreach ($encaminhamentosDenuncia as $encaminhamentoDenuncia) {
                if ($encaminhamento->getId() != $encaminhamentoDenuncia->getId()) {
                    $encaminhamentoDenuncia->setTipoSituacaoEncaminhamento($tipoSituacaoEncaminhamento);
                    $encaminhamentoDenuncia->setDescricao(Constants::JUSTIFICATIVA_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO_FECHADO);

                    $this->encaminhamentoDenunciaRepository->persist($encaminhamentoDenuncia);
                }
            }
        }
    }

    /**
     * Valida a Data e Hora informadas para a Audiencia de Instrução
     *
     * @param DenunciaAudienciaInstrucao $audienciaInstrucao
     * @throws NegocioException
     */
    private function validaDataHoraAudienciaIntrucao(DenunciaAudienciaInstrucao $audienciaInstrucao)
    {
        if($audienciaInstrucao->getDataAudienciaInstrucao() > Utils::getData()){
            throw new NegocioException(Message::MSG_DATA_HORA_FUTURA);
        }

    }

    /**
     * Disponibiliza o arquivo 'Encaminhamento Audiência de instrução' para 'download' conforme o 'id' informado.
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

        $arquivoDenuncia = $this->getArquivoEncaminhamento($idArquivo);
        $caminho = $this->getarquivoService()->getCaminhoRepositorioDenunciaAudienciaInstrucao($arquivoDenuncia->getDenunciaAudienciaInstrucao()->getId());
        return $this->getarquivoService()->getArquivo($caminho, $arquivoDenuncia->getNomeFisicoArquivo(), $arquivoDenuncia->getNome());
    }

    /**
     * Recupera a entidade 'ArquivoDenuncia' por meio do 'id' informado.
     *
     * @param $id
     *
     * @return \App\Entities\ArquivoDenuncia|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getArquivoEncaminhamento($id)
    {
        echo($id);
        $arrayArquivo = $this->arquivoDenunciaAudienciaInstrucaoRepository->getPorId($id);

        return $arrayArquivo[0];
    }
}
