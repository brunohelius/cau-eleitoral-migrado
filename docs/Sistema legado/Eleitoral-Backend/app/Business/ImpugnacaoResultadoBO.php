<?php
/*
 * ImpugnacaoResultadoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ImpugnacaoResultado;
use App\Entities\MembroChapa;
use App\Entities\StatusImpugnacaoResultado;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmaiCadastrolmpugnacaoResultadoJob;
use App\Jobs\EnviarEmailCadastrolmpugnacaoResultadoJob;
use App\Mail\ImpugnacaoResultadoMail;
use App\Repository\ImpugnacaoResultadoRepository;
use App\Service\ArquivoService;
use App\Service\CorporativoService;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\AtividadePrincipalCalendarioTO;
use App\To\AtividadeSecundariaCalendarioTO;
use App\To\EleicaoTO;
use App\To\ImpugnacaoResultadoTO;
use App\To\QuantidadePedidoImpugnacaoResultadoPorUfTO;
use App\To\UfCalendarioTO;
use App\Util\Email;
use App\Util\Utils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'ImpugnacaoResultado'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class ImpugnacaoResultadoBO extends AbstractBO
{
    /**
     * @var ImpugnacaoResultadoRepository
     */
    private $impugnacaoResultadoRepository;

    /**
     * @var MembroComissaoBO
     */
    private $membroComissaoBO;

    /**
     * @var AtividadeSecundariaCalendarioBO
     */
    private $atividadeSecundariaCalendarioBO;

    /**
     * @var RecursoImpugnacaoResultadoBO
     */
    private  $recursoImpugnacaoResultadoBO;

    /**
     * @var CorporativoService
     */
    private $corporativoService;

    /**
     * @var EmailAtividadeSecundariaBO
     */
    private $emailAtividadeSecundariaBO;

    /**
     * Retorna as Cau Uf's conforme o identificador informado.
     *
     * @return mixed|null
     * @throws NegocioException
     * @throws \Exception
     */
    public function getCauUf()
    {
        $eleicaoVigente = $this->getEleicaoBO()->getEleicaoVigenteComCalendario();
        $ufCalendario = $this->getUfCalendarioBO()->getUfsCalendario($eleicaoVigente->getCalendario()->getId());
        foreach ($ufCalendario as $key => $uf) {
            $cauBR = $this->getFilialBO()->getPorId($uf->getIdCauUf());
            if ($cauBR->getId() != Constants::UF_CAU_BR) {
                $ufFinal[$key] = UfCalendarioTO::newInstance($uf);
                $ufFinal[$key]->setCalendario($uf->getCalendario());
                $ufFinal[$key]->setUf($cauBR);
                $calendario = $uf->getCalendario();
            }
        }

        //Retorna os dados ordenados pela UF
        $ufFinal = array_values(Arr::sort($ufFinal, function ($value) {
            /** @var UfCalendarioTO $value */
            return $value->getUf()->getPrefixo();
        }));
        if ($eleicaoVigente->getTipoProcesso()->getId() == Constants::TIPO_PROCESSO_ORDINARIO) {
            $ufIes = UfCalendarioTO::newInstance([
                'uf' => ['id' => 0, 'prefixo' => 'IES'],
                'calendario' => ['id' => $calendario->getId()]
            ]);
            array_push($ufFinal, $ufIes);
        }
        return $ufFinal;
    }

    /**
     * Retorna a impugnação com a verificação de duplicidade
     * @param ImpugnacaoResultadoTO $impugnacaoResultadoTO
     * @return ImpugnacaoResultadoTO
     * @throws NegocioException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getVerificacaoDuplicidadePedido(ImpugnacaoResultadoTO $impugnacaoResultadoTO)
    {
        $idCauBR = !empty($impugnacaoResultadoTO->getIdCauBR()) ? $impugnacaoResultadoTO->getIdCauBR() : Constants::ID_IES_ELEITORAL;
        $totalPorArquitetoAndUf = $this->getImpugnacaoResultadoRepository()->getTotalImpugnacaoResultadoPorCalendario(
            $impugnacaoResultadoTO->getIdCalendario(),
            $idCauBR,
            [$this->getUsuarioFactory()->getUsuarioLogado()->idProfissional]
        );
        if (!empty($totalPorArquitetoAndUf)) {
            $impugnacaoResultadoTO->setTipoValidacao(Constants::TIPO_VALIDACAO_USARIO_CADASTRO);
        } else {
            $totalChapaAndUf = null;
            $membroChapa = $this->getMembroChapaBO()->getMembroChapaAtualPorCalendarioProfissioal(
                $impugnacaoResultadoTO->getIdCalendario(),
                $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
            );
            if (!empty($membroChapa)) {
                $chapa = $this->getChapaEleicaoBO()->getPorId($membroChapa->getChapaEleicao()->getId(), true);
                $membrosChapa = $chapa->getMembrosChapa();

                $profissionais = [];
                foreach ($membrosChapa as $membro) {
                    array_push($profissionais, $membro->getProfissional()->getId());
                }

                $totalChapaAndUf = $this->getImpugnacaoResultadoRepository()->getTotalImpugnacaoResultadoPorCalendario(
                    $impugnacaoResultadoTO->getIdCalendario(),
                    $idCauBR,
                    $profissionais
                );
            }
            if (!empty($totalChapaAndUf)) {
                $impugnacaoResultadoTO->setTipoValidacao(Constants::TIPO_VALIDACAO_CHAPA_CADASTRO);
            } else {
                $totalUf = $this->getImpugnacaoResultadoRepository()->getTotalImpugnacaoResultadoPorCalendario(
                    $impugnacaoResultadoTO->getIdCalendario(),
                    $idCauBR
                );
                if (!empty($totalUf)) {
                    $impugnacaoResultadoTO->setTipoValidacao(Constants::TIPO_VALIDACAO_CADASTRO);
                }
            }
        }

        return $impugnacaoResultadoTO;
    }

    /**
     * Retorna o pedido de Impugnação a partir da uf.
     *
     * @param $uf
     * @return mixed|null
     * @throws NegocioException
     * @throws NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorUf($uf)
    {
        $profissional = $this->getUsuarioFactory()->getUsuarioLogado();
        $impugnacoes = $this->getImpugnacaoResultadoRepository()->getPorUfeProfissional(
            $uf,
            [$profissional->idProfissional]
        );

        if (!empty($impugnacoes)) {
            foreach ($impugnacoes as $impugnacao) {
                $impugnacao->setTipoValidacao(Constants::TIPO_VALIDACAO_USARIO_CADASTRO);
            }
        } else {
            $membroChapa = $this->getMembroChapaBO()->findBy(["profissional" => $profissional->idProfissional]);
            if (!empty($membroChapa)) {
                $chapa = $this->getChapaEleicaoBO()->getPorId($membroChapa[0]->getChapaEleicao()->getId(), true);
                $membrosChapa = $chapa->getMembrosChapa();

                foreach ($membrosChapa as $membro) {
                    $profissionais[] = $membro->getProfissional()->getId();
                }
                $impugnacoes = $this->getImpugnacaoResultadoRepository()->getPorUfeProfissional($uf, $profissionais);
            }

            if (!empty($impugnacoes)) {
                foreach ($impugnacoes as $impugnacao) {
                    $impugnacao->setTipoValidacao(Constants::TIPO_VALIDACAO_CHAPA_CADASTRO);
                }
            } else {
                $impugnacoes = $this->getImpugnacaoResultadoRepository()->getPorUfeProfissional($uf);
                if (!empty($impugnacoes)) {
                    foreach ($impugnacoes as $impugnacao) {
                        $impugnacao->setTipoValidacao(Constants::TIPO_VALIDACAO_CADASTRO);
                    }
                }
            }
        }

        return array_map(function($impugnacao){
            return ImpugnacaoResultadoTO::newInstanceFromEntity($impugnacao);
        }, $impugnacoes);
    }

    /**
     * Retorna o pedido de Impugnação a partir do profissional.
     * @param $idUf
     * @return array
     */
    public function acompanharParaProfissional($idUf)
    {
        $profissional = $this->getUsuarioFactory()->getUsuarioLogado();
        $impugnacoes =  $this->getImpugnacaoResultadoRepository()->getPorUfeProfissional(
            $idUf,
            [$profissional->idProfissional]
        );

        if(empty($impugnacoes)) {
            throw new NegocioException(Lang::get('messages.impugnacao_resultado.sem_pedidos_cadastrados'));
        }

        return array_map(function($impugnacao){
            return ImpugnacaoResultadoTO::newInstanceFromEntity($impugnacao);
        }, $impugnacoes);
    }

    /**
     * Retorna o pedido de Impugnação a partir do profissional.
     *
     * @return array
     * @throws NegocioException
     * @throws NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function acompanharParaChapa()
    {
        $membroChapa = null;

        $profissional = $this->getUsuarioFactory()->getUsuarioLogado();
        $eleicoes = $this->getEleicaoBO()->getEleicoesVigenteComCalendario();

        foreach ($eleicoes as $eleicao) {
            $membro = $this->getMembroChapaBO()->getMembroChapaAtualPorCalendarioProfissioal(
                $eleicao->getCalendario()->getId(), $profissional->idProfissional
            );
            if(!empty($membro)){
                $membroChapa = $membro;
            }
        }

        if(empty($membroChapa)) {
            throw new NegocioException(Lang::get('messages.impugnacao_resultado.sem_permitido_apenas_responsaveis_chapa'));
        }

        $idCauUF = $membroChapa->getChapaEleicao()->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES
            ? 0 : $membroChapa->getChapaEleicao()->getFilial()->getId();

        $impugnacoes = $this->getImpugnacaoResultadoRepository()->getPorUfeProfissional($idCauUF, null);

        if(empty($impugnacoes)) {
            throw new NegocioException(Lang::get('messages.impugnacao_resultado.sem_pedidos_cadastrados'));
        }

        return array_map(function($impugnacao){
            return ImpugnacaoResultadoTO::newInstanceFromEntity($impugnacao);
        }, $impugnacoes);
    }

    /**
     * Retorna o pedido de Impugnação a partir do UF Para membros da comissão.
     *
     * @param $idUf
     * @return array
     * @throws NegocioException
     */
    public function acompanharParaMembroComissao($idUf)
    {
        $eleicoes = $this->getEleicaoBO()->getEleicoesVigenteComCalendario();
        $calendarios = [];
        foreach ($eleicoes as $eleicao) {
            $isMembroComissao = $this->getMembroComissaoBO()->verificarMembroComissaoPorCauUf($eleicao->getCalendario()->getId(), $idUf);
            if($isMembroComissao){
                $calendarios[] = $eleicao->getCalendario()->getId();
            }
        }

        if(empty($calendarios)){
            throw new NegocioException(Lang::get('messages.permissao.permissao_somente_membro_comissao'));
        }

        $impugnacoes = $this->getImpugnacaoResultadoRepository()->getPorUfeCalendario($idUf, $calendarios);
        if(empty($impugnacoes)) {
            throw new NegocioException(Lang::get('messages.impugnacao_resultado.sem_pedidos_cadastrados'));
        }

        return array_map(function($impugnacao){
            return ImpugnacaoResultadoTO::newInstanceFromEntity($impugnacao);
        }, $impugnacoes);
    }

    /**
     * Acompanhar Pedidos de impugnação de Resultado por caledario e UF.
     * Validação de CEN_BR ou CE_UF.
     *
     * @param $idUf
     * @param $idCalendario
     * @return array
     * @throws NegocioException
     */
    public function acompanharParaCorporativo($idUf, $idCalendario){

        $isAssessorCEN = $this->getUsuarioFactory()->isCorporativoAssessorCEN();
        $isAssessorCE = $this->getUsuarioFactory()->isCorporativoAssessorCEUF() &&
            $idUf == $this->getUsuarioFactory()->getUsuarioLogado()->idCauUf;

        if(!$this->getUsuarioFactory()->isCorporativo() || !($isAssessorCEN || $isAssessorCE )) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }

        $impugnacoes = $this->getImpugnacaoResultadoRepository()->getPorUfeCalendario($idUf, [$idCalendario]);

        if(empty($impugnacoes)) {
            throw new NegocioException(Lang::get('messages.impugnacao_resultado.sem_pedidos_cadastrados'));
        }

        return array_map(function($impugnacao){
            return ImpugnacaoResultadoTO::newInstanceFromEntity($impugnacao);
        }, $impugnacoes);
    }

    /**
     * Retorna a impugnação de resultado a partir do id.
     *
     * @param $id
     * @param bool $isVerificarMembroComissao
     * @param bool $isVerificarMembroChapa
     * @param bool $isVerificarImpugnante
     * @param bool $isVerificarAssessor
     * @return mixed
     * @throws NegocioException
     */
    public function getImpugnacaoPorId(
        $id,
        $isVerificarMembroComissao = false,
        $isVerificarMembroChapa = false,
        $isVerificarImpugnante = false,
        $isVerificarAssessor = false
    ) {
        $impugnacaoTO = $this->getImpugnacaoResultadoRepository()->getImpugnacaoPorId($id);

        if (!empty($impugnacaoTO)) {
            $eleicao = $this->getEleicaoBO()->getEleicaoPorCalendario($impugnacaoTO->getCalendario()->getId(), true);

            $this->verificarPermisaoVisualizarImpugnacao(
                $isVerificarAssessor,
                $isVerificarImpugnante,
                $isVerificarMembroChapa,
                $isVerificarMembroComissao,
                $impugnacaoTO
            );

            /** @var AtividadePrincipalCalendarioTO $atividadePrincipalTO */
            foreach ($eleicao->getCalendario()->getAtividadesPrincipais() as $atividadePrincipalTO) {

                /** @var AtividadeSecundariaCalendarioTO $atividadeSecundariaTO */
                foreach ($atividadePrincipalTO->getAtividadesSecundarias() as $atividadeSecundariaTO) {
                    $dataInicio = Utils::getDataHoraZero($atividadeSecundariaTO->getDataInicio());
                    $isIniciadoAtividade = Utils::getDataHoraZero() >= $dataInicio;

                    $dataFim = Utils::getDataHoraZero($atividadeSecundariaTO->getDataFim());
                    $isFinalizadoAtividade = Utils::getDataHoraZero() > $dataFim;

                    if ($atividadePrincipalTO->getNivel() == 6 && $atividadeSecundariaTO->getNivel() == 1) {
                        $impugnacaoTO->setIsIniciadoAtividadeCadastro($isIniciadoAtividade);
                        $impugnacaoTO->setIsFinalizadoAtividadeCadastro($isFinalizadoAtividade);
                    }

                    if ($atividadePrincipalTO->getNivel() == 6 && $atividadeSecundariaTO->getNivel() == 2) {
                        $impugnacaoTO->setIsIniciadoAtividadeAlegacao($isIniciadoAtividade);
                        $impugnacaoTO->setIsFinalizadoAtividadeAlegacao($isFinalizadoAtividade);
                    }

                    if ($atividadePrincipalTO->getNivel() == 6 && $atividadeSecundariaTO->getNivel() == 3) {
                        $impugnacaoTO->setIsIniciadoAtividadeJulgamento($isIniciadoAtividade);
                        $impugnacaoTO->setIsFinalizadoAtividadeJulgamento($isFinalizadoAtividade);
                    }

                    if ($atividadePrincipalTO->getNivel() == 6 && $atividadeSecundariaTO->getNivel() == 4) {
                        $impugnacaoTO->setIsIniciadoAtividadeRecursoJulgamento($isIniciadoAtividade);
                        $impugnacaoTO->setIsFinalizadoAtividadeRecursoJulgamento($isFinalizadoAtividade);
                    }

                    if ($atividadePrincipalTO->getNivel() == 6 && $atividadeSecundariaTO->getNivel() == 5) {
                        $impugnacaoTO->setIsIniciadoAtividadeContrarrazao($isIniciadoAtividade);
                        $impugnacaoTO->setIsFinalizadoAtividadeContrarrazao($isFinalizadoAtividade);
                    }

                    if ($atividadePrincipalTO->getNivel() == Constants::NIVEL_ATIVIDADE_PRINCIPAL_CADASTRAR_JULG_RECURSO_IMPUG_RESULTADO &&
                        $atividadeSecundariaTO->getNivel() == Constants::NIVEL_ATIVIDADE_SECUNDARIA_CADASTRAR_JULG_RECURSO_IMPUG_RESULTADO) {
                        $impugnacaoTO->setIsIniciadoAtividadeJulgamentoRecurso($isIniciadoAtividade);
                        $impugnacaoTO->setIsFinalizadoAtividadeJulgamentoRecurso($isFinalizadoAtividade);
                    }
                }
            }

            $totalRecursoImpugnante = $this->getRecursoImpugnacaoResultadoBO()->getTotalPorPedidoImpugnacaoAndTipoRecurso($id, 2);
            $totalRecursoImpugnado = $this->getRecursoImpugnacaoResultadoBO()->getTotalPorPedidoImpugnacaoAndTipoRecurso($id, 1);
            $impugnacaoTO->setHasRecursoJulgamentoImpugnante(!empty($totalRecursoImpugnante) && $totalRecursoImpugnante > 0);
            $impugnacaoTO->setHasRecursoJulgamentoImpugnado(!empty($totalRecursoImpugnado) && $totalRecursoImpugnado > 0);
            $impugnacaoTO->setHasJulgamento(!empty($impugnacaoTO->getJulgamentoAlegacao()));
            $impugnacaoTO->setHasJulgamentoRecurso(!empty($impugnacaoTO->getJulgamentoRecurso()));

            $impugnacaoTO->setJulgamentoRecurso(null);
            $impugnacaoTO->setAlegacoes(null);
            $impugnacaoTO->setJulgamentoAlegacao(null);
        }

        return $impugnacaoTO;
    }

    /**
     * Reponsavel por execultar o salvamento da impugnação de resultado.
     *
     * @param ImpugnacaoResultadoTO $impugnacaoResultadoTO
     * @return ImpugnacaoResultadoTO
     * @throws \Exception
     */
    public function salvar($impugnacaoResultadoTO)
    {
        $arquivos = $impugnacaoResultadoTO->getArquivos();

        /** @var ArquivoGenericoTO|null $arquivo */
        $arquivo = (!empty($arquivos) && is_array($arquivos)) ? array_shift($arquivos) : null;

        $this->validarCamposObrigatorios($impugnacaoResultadoTO, $arquivo);

        try {
            $this->beginTransaction();

            $impugnacaoResultado = $this->prepararRecursoSalvar($impugnacaoResultadoTO, $arquivo);
            $this->getImpugnacaoResultadoRepository()->persist($impugnacaoResultado);

            if (!empty($arquivo)) {
                $this->salvarArquivo(
                    $impugnacaoResultado->getId(),
                    $arquivo->getArquivo(),
                    $impugnacaoResultado->getNomeArquivoFisico()
                );
            }

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        Utils::executarJOB(new EnviarEmailCadastrolmpugnacaoResultadoJob($impugnacaoResultado->getId(), EnviarEmailCadastrolmpugnacaoResultadoJob::TIPO_IMPUGNANTE));
        Utils::executarJOB(new EnviarEmailCadastrolmpugnacaoResultadoJob($impugnacaoResultado->getId(), EnviarEmailCadastrolmpugnacaoResultadoJob::TIPO_IMPUGNADO));
        Utils::executarJOB(new EnviarEmailCadastrolmpugnacaoResultadoJob($impugnacaoResultado->getId(), EnviarEmailCadastrolmpugnacaoResultadoJob::TIPO_COMISSAO));
        Utils::executarJOB(new EnviarEmailCadastrolmpugnacaoResultadoJob($impugnacaoResultado->getId(), EnviarEmailCadastrolmpugnacaoResultadoJob::TIPO_ASSESSORES));

        return  ImpugnacaoResultadoTO::newInstanceFromEntity($impugnacaoResultado);
    }

    /**
     * Realiza o envio de e-mail após o cadastro de impugnacao de resultado
     * @param $idImpugnacaoResultado
     */
    public function enviarEmailCadastroImpugnacao($idImpugnacaoResultado, $tipo)
    {
        /** @var ImpugnacaoResultado $impugnacaoResultado */
        $impugnacaoResultado = $this->getImpugnacaoResultadoRepository()->find($idImpugnacaoResultado);

        $atividade = $this->getAtividadeSecundariaCalendarioBO()->getPorCalendario(
            $impugnacaoResultado->getCalendario()->getId(), 6, 1
        );

        Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Buscou dados');

        if (EnviarEmailCadastrolmpugnacaoResultadoJob::TIPO_IMPUGNANTE == $tipo) {
            $this->enviarEmailImpugnante($impugnacaoResultado, $atividade);
        }

        if (EnviarEmailCadastrolmpugnacaoResultadoJob::TIPO_IMPUGNADO == $tipo) {
            $this->enviarEmailImpugnanado($impugnacaoResultado, $atividade);
        }

        if (EnviarEmailCadastrolmpugnacaoResultadoJob::TIPO_COMISSAO == $tipo) {
            //$this->enviarEmailCoordenadoresComissao($impugnacaoResultado, $atividade);
        }

        if (EnviarEmailCadastrolmpugnacaoResultadoJob::TIPO_ASSESSORES == $tipo) {
            $this->enviarEmailAcessoresCenAndAcessoresCE($impugnacaoResultado, $atividade);
        }
    }

    /**
     * Responsável por enviar e-mail informativo para os acessores CEN/BR e CE
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @param AtividadeSecundariaCalendario $atividade
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailImpugnante($impugnacaoResultado, $atividade)
    {
        Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Iniciou envio para impugnante');
        $destinatarios = [];

        if (!empty($impugnacaoResultado->getProfissional()->getPessoa())) {
            array_push($destinatarios, $impugnacaoResultado->getProfissional()->getPessoa()->getEmail());
        }

        Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Buscou destinatário impugnante');
        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(), Constants::EMAIL_PEDIDO_IMPUGNACAO_RESULTADO_IMPUGNANTE
            );
            Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Buscou email parametrizado impugnante');

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $impugnacaoResultado);
        }
    }

    /**
     * Responsável por enviar e-mail informativo para os acessores CEN/BR e CE
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @param AtividadeSecundariaCalendario $atividade
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailImpugnanado($impugnacaoResultado, $atividade)
    {
        Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Iniciou envio para impugnanado');
        $isIES = empty($impugnacaoResultado->getCauBR());
        $membrosChapas = $this->getMembroChapaBO()->getMembrosResponsaveisPorCalendarioAndTipoCandidaturaAndCauUF(
            $impugnacaoResultado->getCalendario()->getId(),
            $isIES ? Constants::TIPO_CANDIDATURA_IES : Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR,
            $isIES ? null : $impugnacaoResultado->getCauBR()->getId()
        );

        $destinatarios = [];
        if (!empty($membrosChapas)) {
            /** @var MembroChapa $membroChapa */
            foreach ($membrosChapas as $membroChapa) {
                if (!empty($membroChapa->getProfissional()->getPessoa())) {
                    array_push($destinatarios, $membroChapa->getProfissional()->getPessoa()->getEmail());
                }
            }
        }
        Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Buscou destinatário impugnanado');

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(), Constants::EMAIL_PEDIDO_IMPUGNACAO_RESULTADO_IMPUGNADO
            );
            Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Buscou email parametrizado impugnanado');

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $impugnacaoResultado);
        }
    }

    /**
     * Responsável por enviar e-mail informativo para os acessores CEN/BR e CE
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @param AtividadeSecundariaCalendario $atividade
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailCoordenadoresComissao($impugnacaoResultado, $atividade)
    {
        Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Iniciou envio para comissão');
        $idCauUf = empty($impugnacaoResultado->getCauBR()) ? null : $impugnacaoResultado->getCauBR()->getId();

        $destinatarios = $this->getEmailAtividadeSecundariaBO()->getDestinatariosEmailConselheirosCoordenadoresComissao(
            $atividade->getId(), $idCauUf
        );
        Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Buscou destinatário comissão');

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(), Constants::EMAIL_PEDIDO_IMPUGNACAO_RESULTADO_COORD_ADJUNTOS_CE_CEN
            );
            Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Buscou email parametrizado comissão');

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $impugnacaoResultado);
        }
    }

    /**
     * Responsável por enviar e-mail informativo para os acessores CEN/BR e CE
     * @param ImpugnacaoResultado $impugnacaoResultado
     * @param AtividadeSecundariaCalendario $atividade
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    private function enviarEmailAcessoresCenAndAcessoresCE($impugnacaoResultado, $atividade)
    {
        Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Iniciou envio para assessores');
        $idCauUf = empty($impugnacaoResultado->getCauBR()) ? null : $impugnacaoResultado->getCauBR()->getId();

        $destinatarios = $this->getCorporativoService()->getListaEmailsAssessoresCenAndAssessoresCE(
            empty($impugnacaoResultado->getCauBR()) ? null : [$idCauUf]
        );
        Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Buscou destinatários assessores');

        if (!empty($destinatarios)) {
            $emailAtividadeSecundaria = $this->getEmailAtividadeSecundariaBO()->getEmailPorAtividadeSecundariaAndTipo(
                $atividade->getId(), Constants::EMAIL_PEDIDO_IMPUGNACAO_RESULTADO_ASSESSORES
            );
            Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Buscou email parametrizado assessores');

            $this->enviarEmail($emailAtividadeSecundaria, $destinatarios, $impugnacaoResultado);
        }
    }

    /**
     * @param $emailAtividadeSecundaria
     * @param $destinatarios
     * @param ImpugnacaoResultado $impugnacaoResultado
     */
    private function enviarEmail($emailAtividadeSecundaria, $destinatarios, $impugnacaoResultado)
    {
        $impugnacaoResultadoTO = ImpugnacaoResultadoTO::newInstanceFromEntity($impugnacaoResultado, true);

        if (!empty($emailAtividadeSecundaria)) {
            $emailTO = $this->getEmailAtividadeSecundariaBO()->recuperaInformacoesEmailPadrao($emailAtividadeSecundaria);
            $emailTO->setDestinatarios($destinatarios);

            $eleicaoTO = EleicaoTO::newInstance([
                'ano' => $impugnacaoResultado->getCalendario()->getEleicao()->getAno(),
                'sequenciaAno' => $impugnacaoResultado->getCalendario()->getEleicao()->getSequenciaAno()
            ]);

            Email::enviarMail(new ImpugnacaoResultadoMail($emailTO, $impugnacaoResultadoTO, $eleicaoTO->getSequenciaFormatada()));
        }
    }

    /**
     * Responsável por salvar a arquivo no diretório
     *
     * @param $idImpugnacao
     * @param $arquivo
     * @param $nomeArquivoFisico
     */
    private function salvarArquivo($idImpugnacao, $arquivo, $nomeArquivoFisico)
    {
        $this->getArquivoService()->salvar(
            $this->getArquivoService()->getCaminhoRepositorioImpugnacaoResultado($idImpugnacao),
            $nomeArquivoFisico,
            $arquivo
        );
    }

    /**
     * Método auxiliar para preparar entidade RecursoJulgamentoRecursoImpugnacao para cadastro
     *
     * @param ImpugnacaoResultadoTO $impugnacaoResultadoTO
     * @param ArquivoGenericoTO|null $arquivo
     * @return ImpugnacaoResultado
     * @throws \Exception
     */
    private function prepararRecursoSalvar($impugnacaoResultadoTO, $arquivo)
    {
        $nomeArquivoFisico = empty($arquivo) ? '' : $this->getArquivoService()->getNomeArquivoFormatado(
            $arquivo->getNome(), Constants::PREFIXO_ARQ_IMPUGN_RESULTADO
        );
        $nomeArquivo = empty($arquivo) ? '' : $arquivo->getNome();

        $total = $this->getImpugnacaoResultadoRepository()->getTotalImpugnacaoResultadoPorCalendario(
            $impugnacaoResultadoTO->getIdCalendario()
        );

        $cauBR = $impugnacaoResultadoTO->getIdCauBR() == Constants::ID_IES_ELEITORAL ? null : ["id" => $impugnacaoResultadoTO->getIdCauBR()];
        $numero = !empty($total) ? $total + 1 : 1;

        return ImpugnacaoResultado::newInstance([
            'narracao' => $impugnacaoResultadoTO->getNarracao(),
            'numero' => $numero,
            'nomeArquivo' => $nomeArquivo,
            'nomeArquivoFisico' => $nomeArquivoFisico,
            'dataCadastro' => Utils::getData(),
            'cauBR' => $cauBR,
            'profissional' => ["id" => $impugnacaoResultadoTO->getIdProfissional()],
            'status' => ["id" =>  Constants::STATUS_IMPUG_RESULTADO_AGUARDANDO_ALEGACOES],
            'calendario' => ["id" => $impugnacaoResultadoTO->getIdCalendario()],
        ]);
    }

    /**
     * Buscar Impugnação de resultado por id.
     *
     * @param $id
     * @return ImpugnacaoResultado|mixed|null
     */
    public function find($id): ImpugnacaoResultado {
        return $this->getImpugnacaoResultadoRepository()->find($id);
    }

    /**
     * Método faz validação se pode ser cadastrado o julgamento
     *
     * @param ImpugnacaoResultadoTO $impugnacaoResultadoTO
     * @param ArquivoGenericoTO|null $arquivo
     * @throws NegocioException
     */
    private function validarCamposObrigatorios($impugnacaoResultadoTO, $arquivo)
    {
        if (empty($impugnacaoResultadoTO->getNarracao())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if ($impugnacaoResultadoTO->getIdCauBR() == null) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (!empty($arquivo)) {
            $this->getArquivoService()->validarArquivoGenrico(
                $arquivo, Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_15MB
            );
        }
    }


    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $id
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoImpugnacao($id)
    {
        /** @var ImpugnacaoResultado $impugnacao */
        $impugnacao = $this->getImpugnacaoResultadoRepository()->find($id);

        if (!empty($impugnacao->getNomeArquivo())) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioImpugnacaoResultado(
                $impugnacao->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $impugnacao->getNomeArquivoFisico(),
                $impugnacao->getNomeArquivo()
            );
        }
    }

    /**
     * Retorna a quantidade de pedidos de impugnaçao de resultado para cada UF de acordo com o Calendario
     *
     * @param int $idCalendario
     * @return QuantidadePedidoImpugnacaoResultadoPorUfTO[]|null
     */
    public function getQuantidadeImpugnacaoResultadoParaCadaUf(int $idCalendario = null)
    {
        $impugnacoes = $this->getImpugnacaoResultadoRepository()->getQuantidadeImpugnacaoResultadoParaCadaUf($idCalendario);
        return $this->getImpugnacoesOrdenada($impugnacoes);
    }

    /**
     * Retorna a quantidade de pedidos de impugnaçao de resultado para cada UF de acordo com o Calendario
     *
     * @param int $idCalendario
     * @return QuantidadePedidoImpugnacaoResultadoPorUfTO[]|null
     */
    public function getQtdImpugnacaoResultadoParaCadaUfPorComissao()
    {
        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Lang::get('messages.permissao.permissao_somente_membro_comissao'));
        }

        $eleicoes = $this->getEleicaoBO()->getEleicoesVigenteComCalendario();

        $eleicaoAtual = null;
        if (empty($eleicoes)) {
            throw new NegocioException(Lang::get('messages.eleicao.periodo_fechado'));
        }

        $membroComissao = null;
        foreach ($eleicoes as $eleicao) {
            $membroComissao = $this->getMembroComissaoBO()->getMembroComissaoParaVerificacaoPorCalendario(
                $eleicao->getCalendario()->getId()
            );

            if (!empty($membroComissao)) {
                $eleicaoAtual = $eleicao;
                break;
            }
        }

        if (empty($membroComissao)) {
            throw new NegocioException(Lang::get('messages.permissao.permissao_somente_membro_comissao'));
        }

        $impugnacoes = null;

        $idsCauBr = [];
        if ($membroComissao->getFilial()->getId() != Constants::ID_CAU_BR) {
            array_push($idsCauBr, $membroComissao->getFilial()->getId());
        }

        $impugnacoes = $this->getImpugnacaoResultadoRepository()->getQuantidadeImpugnacaoResultadoParaCadaUf(
            $eleicaoAtual->getCalendario()->getId(), $idsCauBr
        );

        if (empty($impugnacoes)) {
            throw new NegocioException(Lang::get('messages.impugnacao_resultado.sem_pedidos_cadastrados'));
        }

        return $this->getImpugnacoesOrdenada($impugnacoes);
    }


    /**
     * Retorna a impugnação de resultado
     *
     * @param $id
     * @return ImpugnacaoResultado|mixed|null
     */
    public function getPorId($id)
    {
        return $this->getImpugnacaoResultadoRepository()->find($id);
    }

    /**
     * Retorna a quantidade de pedidos de impugnaçao de resultado para cada UF de acordo com o Calendario
     *
     * @param int $idCalendario
     * @return QuantidadePedidoImpugnacaoResultadoPorUfTO[]|null
     * @throws NegocioException
     */
    public function getQtdImpugnacaoResultadoParaCadaUfPorImpugnante()
    {
        $profissional = $this->getUsuarioFactory()->getUsuarioLogado();
        if (!$this->getUsuarioFactory()->isProfissional()) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_menu_profissional'));
        }

        $impugnacoes = null;
        $impugnacoes = $this->getImpugnacaoResultadoRepository()->getQuantidadeImpugnacaoResultadoParaCadaUf(
            null,
            null,
            $profissional->idProfissional
        );

        if (empty($impugnacoes)) {
            throw new NegocioException(Lang::get('messages.impugnacao_resultado.sem_pedidos_cadastrados'));
        }

        return $this->getImpugnacoesOrdenada($impugnacoes);
    }

    /**
     * Atualiza o status dos pedidos inicio atividade recurso para os que possuem julgamento alegações
     * @throws \Exception
     */
    public function atualizarStatusImpugnacaoInicioAtivRecurso()
    {
        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            Utils::getDataHoraZero(), null, 6, 4
        );

        foreach ($atividades as $atividadeSecundariaCalendario) {
            $impugnacoes = $this->getImpugnacaoResultadoRepository()->getImpugnacoesComJulgamentoAlegacaoPorCalendario(
                $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId(), null
            );

            $this->atualizarStatusImpugnacoes($impugnacoes, Constants::STATUS_IMPUG_RESULTADO_EM_RECURSO);
        }
    }

    /**
     * Atualiza o status dos pedidos que possuirem recurso para o status em contrarrazão
     * @throws \Exception
     */
    public function atualizarStatusImpugnacaoInicioAtivContrarrazao()
    {
        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            Utils::getDataHoraZero(), null, 6, 5
        );

        foreach ($atividades as $atividadeSecundariaCalendario) {
            $impugnacoes = $this->getImpugnacaoResultadoRepository()->getImpugnacoesComJulgamentoAlegacaoPorCalendario(
                $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId(), true
            );

            $this->atualizarStatusImpugnacoes($impugnacoes, Constants::STATUS_IMPUG_RESULTADO_EM_CONTRARRAZAO);
        }
    }

    /**
     * Atualiza status dos pedidos que estava em contrarrazão no ínicio da atividade do julgamento para
     * em julgamento 2ª instância
     * @throws \Exception
     */
    public function atualizarStatusImpugnacaoInicioJulgSegundaInstancia()
    {
        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            Utils::getDataHoraZero(), null, 6, 6
        );

        foreach ($atividades as $atividadeSecundariaCalendario) {
            $this->atualizarStatusImpugInicioJulgSegundaInstanciaJulgamentoSegundaInstancia(
                $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId()
            );
        }
    }

    /**
     * Mètodo auxiliar para mudar status dos pedidos para julgamento 2ª instância todos os que possuem recurso
     * @param $idCalendario
     * @throws \Exception
     */
    public function atualizarStatusImpugInicioJulgSegundaInstanciaJulgamentoSegundaInstancia($idCalendario)
    {
        $impugnacoesEmJulgamento = $this->getImpugnacaoResultadoRepository()->getImpugnacoesComJulgamentoAlegacaoPorCalendario(
            $idCalendario,
            true
        );
        $this->atualizarStatusImpugnacoes(
            $impugnacoesEmJulgamento,
            Constants::STATUS_IMPUG_RESULTADO_EM_JULGAMENTO_2_INSTANCIA
        );
    }

    /**
     * Ao finalizar o período da atividade 6.4, se o recurso não for cadastrado e o julgamento em 1ª instancia tiver sido
     * “Improcedente” o pedido de impugnação de resultado é finalizado e o status do pedido de impugnação de resultado é
     * alterado para “Transitado em Julgado”.
     *
     * E se o pedido de impugnação for para IES, verifica se o julgamento em 1ª instancia tiver sido "Procedente”
     * também atualiza o status para “Transitado em Julgado”.
     *
     * @throws \Exception
     */
    public function atualizarStatusImpugnacaoFimAtivRecurso()
    {
        /** @var AtividadeSecundariaCalendario[] $atividades */
        $atividades = $this->getAtividadeSecundariaCalendarioBO()->getAtividadesSecundariasPorVigencia(
            null,  Utils::subtrairDiasData(Utils::getDataHoraZero(), 1), 6, 4
        );

        foreach ($atividades as $atividadeSecundariaCalendario) {
            $impugnacoesJulgamentoImprocedente = $this->getImpugnacaoResultadoRepository()
                ->getImpugnacoesComJulgamentoAlegacaoPorCalendario(
                $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId(), false,
                Constants::STATUS_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_IMPUGNACAO_RESULTADO_IMPROCEDENTE
            );
            $this->atualizarStatusImpugnacoes($impugnacoesJulgamentoImprocedente, Constants::STATUS_IMPUG_RESULTADO_TRANSITADO_JULGADO);


            $impugnacoesJulgamentoProcedenteIES = $this->getImpugnacaoResultadoRepository()
                ->getImpugnacoesComJulgamentoAlegacaoPorCalendario(
                    $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId(), false,
                    Constants::STATUS_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_IMPUGNACAO_RESULTADO_PROCEDENTE,
                    true
                );
            $this->atualizarStatusImpugnacoes($impugnacoesJulgamentoProcedenteIES, Constants::STATUS_IMPUG_RESULTADO_TRANSITADO_JULGADO);

            $impugnacoesJulgamentoProcedenteUF = $this->getImpugnacaoResultadoRepository()
                ->getImpugnacoesComJulgamentoAlegacaoPorCalendario(
                    $atividadeSecundariaCalendario->getAtividadePrincipalCalendario()->getCalendario()->getId(), false,
                    Constants::STATUS_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_IMPUGNACAO_RESULTADO_PROCEDENTE,
                    false
                );
            $this->atualizarStatusImpugnacoes($impugnacoesJulgamentoProcedenteUF, Constants::STATUS_IMPUG_RESULTADO_AGUARDANDO_HOMOLOGACAO);
        }
    }

    /**
     * Método auxiliar que atualiza o status das impugnações passada como parâmetro
     * @param $impugnacoes
     * @param $idStatus
     */
    private function atualizarStatusImpugnacoes($impugnacoes, $idStatus)
    {
        if (!empty($impugnacoes)) {
            try {
                $this->beginTransaction();

                /** @var ImpugnacaoResultado $impugnacao */
                foreach ($impugnacoes as $impugnacao) {
                    $this->salvarStatusImpugnacaoResultado($impugnacao, $idStatus);
                }

                $this->commitTransaction();
            } catch (\Exception $e) {
                $this->rollbackTransaction();
                throw $e;
            }
        }
    }

    /**
     * Método que salva o status da impugnação de resultado
     * @param ImpugnacaoResultado $impugnacao
     * @param $idStatus
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function salvarStatusImpugnacaoResultado(ImpugnacaoResultado $impugnacao, $idStatus)
    {
        $impugnacao->setStatus(StatusImpugnacaoResultado::newInstanceById($idStatus));

        $this->getImpugnacaoResultadoRepository()->persist($impugnacao);
    }

    /**
     * Retorna uma nova instância de 'PublicacaoDocumentoRepository'.
     *
     * @return \App\Repository\ImpugnacaoResultadoRepository
     */
    private function getImpugnacaoResultadoRepository()
    {
        if (empty($this->impugnacaoResultadoRepository)) {
            $this->impugnacaoResultadoRepository = $this->getRepository(ImpugnacaoResultado::class);
        }

        return $this->impugnacaoResultadoRepository;
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
     * Retorna uma instancia de AlegacaoImpugnacaoResultadoBO
     * @return AlegacaoImpugnacaoResultadoBO|mixed
     */
    private function getAlegacaoImpugnacaoResultadoBO()
    {
        if(empty($this->alegacaoImpugnacaoResultadoBO)) {
            $this->alegacaoImpugnacaoResultadoBO = app()->make(AlegacaoImpugnacaoResultadoBO::class);
        }

        return $this->alegacaoImpugnacaoResultadoBO;
    }

    /**
     * Retorna uma instancia de JulgamentoAlegacaoImpugResultadoBO
     * @return JulgamentoAlegacaoImpugResultadoBO|mixed
     */
    private function getJulgamentoAlegacaoImpugResultadoBO()
    {
        if(empty($this->julgamentoAlegacaoImpugResultadoBO)) {
            $this->julgamentoAlegacaoImpugResultadoBO = app()->make(JulgamentoAlegacaoImpugResultadoBO::class);
        }

        return $this->julgamentoAlegacaoImpugResultadoBO;
    }

    /**
     * Retorna uma nova instância de 'MembroComissaoBO'.
     *
     * @return MembroComissaoBO|mixed
     */
    private function getMembroComissaoBO()
    {
        if (empty($this->membroComissaoBO)) {
            $this->membroComissaoBO = app()->make(MembroComissaoBO::class);
        }

        return $this->membroComissaoBO;
    }

    /**
     * Retorna uma nova instância de 'EleicaoBO'.
     *
     * @return ChapaEleicaoBO|mixed
     */
    private function getChapaEleicaoBO()
    {
        if (empty($this->chapaEleicaoBO)) {
            $this->chapaEleicaoBO = app()->make(ChapaEleicaoBO::class);
        }

        return $this->chapaEleicaoBO;
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
     * Retorna uma nova instância de 'UfCalendarioBO'.
     *
     * @return UfCalendarioBO|mixed
     */
    private function getUfCalendarioBO()
    {
        if (empty($this->ufCalendarioBO)) {
            $this->ufCalendarioBO = app()->make(UfCalendarioBO::class);
        }

        return $this->ufCalendarioBO;
    }

    /**
     * Retorna uma nova instância de 'ArquivoService'.
     *
     * @return ArquivoService|mixed
     */
    private function getArquivoService()
    {
        if (empty($this->arquivoService)) {
            $this->arquivoService = app()->make(ArquivoService::class);
        }

        return $this->arquivoService;
    }

    /**
     * Retorna uma nova instância de 'FilialBO'.
     *
     * @return FilialBO|mixed
     */
    private function getFilialBO()
    {
        if (empty($this->filialBO)) {
            $this->filialBO = app()->make(FilialBO::class);
        }

        return $this->filialBO;
    }

    /**
     * Retorna uma nova instância de 'RecursoImpugnacaoResultadoBO'.
     *
     * @return RecursoImpugnacaoResultadoBO|mixed
     */
    private function getRecursoImpugnacaoResultadoBO()
    {
        if (empty($this->recursoImpugnacaoResultadoBO)) {
            $this->recursoImpugnacaoResultadoBO = app()->make(RecursoImpugnacaoResultadoBO::class);
        }

        return $this->recursoImpugnacaoResultadoBO;
    }

    /**
     * Retorna uma nova instância de 'AtividadeSecundariaCalendarioBO'.
     *
     * @return AtividadeSecundariaCalendarioBO|mixed
     */
    private function getAtividadeSecundariaCalendarioBO()
    {
        if (empty($this->atividadeSecundariaCalendarioBO)) {
            $this->atividadeSecundariaCalendarioBO = app()->make(AtividadeSecundariaCalendarioBO::class);
        }

        return $this->atividadeSecundariaCalendarioBO;
    }

    /**
     * Retorna uma nova instância de 'EmailAtividadeSecundariaBO'.
     *
     * @return EmailAtividadeSecundariaBO|mixed
     */
    private function getEmailAtividadeSecundariaBO()
    {
        if (empty($this->emailAtividadeSecundariaBO)) {
            $this->emailAtividadeSecundariaBO = app()->make(EmailAtividadeSecundariaBO::class);
        }

        return $this->emailAtividadeSecundariaBO;
    }

    /**
     * Retorna uma nova instância de 'CorporativoService'.
     *
     * @return CorporativoService|mixed
     */
    private function getCorporativoService()
    {
        if (empty($this->corporativoService)) {
            $this->corporativoService = app()->make(CorporativoService::class);
        }

        return $this->corporativoService;
    }

    /**
     * @param array $impugnacoes
     * @return array
     */
    private function getImpugnacoesOrdenada(array $impugnacoes): array
    {
        $impugnacoesIES = array_filter($impugnacoes, function ($impugnacao) {
            return $impugnacao->getIdCauUF() == null;
        });

        $impugnacoesUf = array_filter($impugnacoes, function ($impugnacao) {
            return $impugnacao->getIdCauUF() != null;
        });

        $impugnacoes = array_merge($impugnacoesUf, $impugnacoesIES);
        return $impugnacoes;
    }

    /**
     * Verifica érmissão de visualizar impugnacao de acordo com os parâmetros
     * @param $isVerificarAssessor
     * @param $isVerificarImpugnante
     * @param $isVerificarMembroChapa
     * @param $isVerificarMembroComissao
     * @param ImpugnacaoResultado|ImpugnacaoResultadoTO $impugnacaoResultado
     * @throws NegocioException
     */
    private function verificarPermisaoVisualizarImpugnacao(
        $isVerificarAssessor,
        $isVerificarImpugnante,
        $isVerificarMembroChapa,
        $isVerificarMembroComissao,
        $impugnacaoResultado
    ): void {

        $idCauUfImpugnacao = !empty($impugnacaoResultado->getCauBR()) ? $impugnacaoResultado->getCauBR()->getId() : null;
        $idProfissionalImpugnante = $impugnacaoResultado->getProfissional()->getId();
        $idCalendario = $impugnacaoResultado->getCalendario()->getId();

        if ($isVerificarAssessor ) {
            if (!$this->getUsuarioFactory()->isCorporativoAssessorCEN() && !$this->getUsuarioFactory()->isCorporativoAssessorCEUF()){
                throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
            }

            if (
                $this->getUsuarioFactory()->isCorporativo() &&
                !$this->getUsuarioFactory()->isCorporativoAssessorCEN() &&
                (empty($idCauUfImpugnacao) || !$this->getUsuarioFactory()->isCorporativoAssessorCeUfPorCauUf($idCauUfImpugnacao))
            ) {
                throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
            }
        }

        if (
            $isVerificarImpugnante &&
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional != $idProfissionalImpugnante
        ) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }

        if ($isVerificarMembroComissao) {
            $this->getMembroComissaoBO()->getMembroComissaoParaVerificacaoPorCalendario($idCalendario);
        }

        if ($isVerificarMembroChapa) {
            $this->verificarPermissaoVisualizarMembroChapa($idCalendario, $idCauUfImpugnacao);
        }
    }

    /**
     * Verifica se é um membro chapa e pertesce a uf do pedido ou
     * se for pedido IES verifica se pertence a chapa IES
     * @param int $idCalendario
     * @param int|null $idCauUfImpugnacao
     * @throws NegocioException
     */
    private function verificarPermissaoVisualizarMembroChapa($idCalendario, $idCauUfImpugnacao )
    {
        $membroChapa = $this->getMembroChapaBO()->getMembroChapaAtualPorCalendarioProfissioal(
            $idCalendario,
            $this->getUsuarioFactory()->getUsuarioLogado()->idProfissional
        );
        if (empty($membroChapa)) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }

        $idCauBrChapa = $membroChapa->getChapaEleicao()->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES
            ? null : $membroChapa->getChapaEleicao()->getFilial()->getId();

        if ($idCauUfImpugnacao != $idCauBrChapa) {
            throw new NegocioException(Lang::get('messages.permissao.sem_acesso_visualizar'));
        }
    }
}
