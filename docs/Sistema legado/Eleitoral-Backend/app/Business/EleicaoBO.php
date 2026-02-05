<?php
/*
 * EleicaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\Eleicao;
use App\Entities\EleicaoSituacao;
use App\Entities\PedidoSubstituicaoChapa;
use App\Entities\SituacaoEleicao;
use App\Entities\TipoProcesso;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\EleicaoRepository;
use App\Repository\SituacaoEleicaoRepository;
use App\Repository\TipoProcessoRepository;
use App\To\ArquivoTO;
use App\To\EleicaoTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'Eleicao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class EleicaoBO extends AbstractBO
{
    /**
     * @var EleicaoRepository
     */
    private $eleicaoRepository;

    /**
     * @var TipoProcessoRepository
     */
    private $tipoProcessoRepository;

    /**
     * @var SituacaoEleicaoRepository
     */
    private $situacaoEleicaoRepository;


    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->tipoProcessoRepository = $this->getRepository(TipoProcesso::class);
        $this->situacaoEleicaoRepository = $this->getRepository(SituacaoEleicao::class);
        $this->eleicaoRepository = $this->getRepository(Eleicao::class);
    }

    /**
     * Retorna os anos que houveram eleições
     *
     * @return array
     */
    public function getAnos()
    {
        return $this->eleicaoRepository->getAnos();
    }

    /**
     * Retorna os tipos de processo
     *
     * @return array
     */
    public function getTipoProcesso()
    {
        return $this->tipoProcessoRepository->findAll();
    }

    /**
     * Retorna as eleicoes para todos os anos
     *
     * @return array
     */
    public function getEleicoes()
    {
        return $this->eleicaoRepository->getEleicoes();
    }

    /**
     * Salva a eleição informada.
     *
     * @param Eleicao $eleicao
     * @return Eleicao
     * @throws NegocioException
     * @throws Exception
     */
    public function salvar(Eleicao $eleicao)
    {
        $this->validarCamposObrigatorios($eleicao);
        $this->validarAno($eleicao->getAno());

        if (empty($eleicao->getId())) {
            $eleicao->setSequenciaAno($this->getSequenciaAnoCadastro($eleicao->getAno(), true));
            $eleicao = $this->criarSituacao($eleicao, Constants::SITUACAO_ELEICAO_EM_ANDAMENTO);
        }

        try {
            $this->beginTransaction();
            $this->eleicaoRepository->persist($eleicao);
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return $eleicao;
    }

    /**
     * Exclui logicamente a eleição conforme o identificador informado.
     *
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function excluir($id)
    {
        /** @var Eleicao $eleicao */
        $eleicao = $this->eleicaoRepository->find($id);
        $eleicao->setAtivo(false);
        $eleicao->setExcluido(true);

        try {
            $this->beginTransaction();
            $calendarioSalvo = $this->eleicaoRepository->persist($eleicao);
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return $calendarioSalvo;
    }


    /**
     * Inativa a eleição conforme o identificador informado.
     *
     * @param $idEleicao
     * @return Eleicao|null
     * @throws Exception
     */
    public function inativar($idEleicao)
    {
        try {
            /** @var Eleicao $eleicao */
            $eleicao = $this->eleicaoRepository->find($idEleicao);
            $eleicao = $this->criarSituacao($eleicao, Constants::SITUACAO_ELEICAO_INATIVADA);
            $this->eleicaoRepository->persist($eleicao);

        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return $eleicao;
    }

    /**
     * Recupera a eleição vigente por nível de atividade.
     *
     * @return EleicaoTO | null
     * @throws Exception
     */
    public function getEleicaoVigentePorNivelAtividade($nivelPrincipal, $nivelSecundaria)
    {
        return $this->eleicaoRepository->getEleicaoVigentePorNivelAtividade($nivelPrincipal, $nivelSecundaria);
    }

    /**
     * Recupera a eleição ativa por nível de atividade.
     *
     * @return EleicaoTO | null
     * @throws Exception
     */
    public function getEleicaoAtivaPorNivelAtividade($nivelPrincipal, $nivelSecundaria)
    {
        return $this->eleicaoRepository->getEleicaoVigentePorNivelAtividade($nivelPrincipal, $nivelSecundaria, false);
    }

    /**
     * Recupera a eleição da chapa.
     *
     * @param $idChapaEleicao
     * @param bool $isAddAtividades
     * @return EleicaoTO | null
     */
    public function getEleicaoPorChapaEleicao($idChapaEleicao, $isAddAtividades = false)
    {
        return $this->eleicaoRepository->getEleicaoPorChapaEleicao($idChapaEleicao, $isAddAtividades);
    }

    /**
     * Verifica se os campos obrigatórios foram preenchidos.
     *
     * @param Eleicao $eleicao
     * @throws NegocioException
     */
    private function validarCamposObrigatorios(Eleicao $eleicao)
    {
        $campos = [];

        if (empty($eleicao->getAno())) {
            $campos[] = 'LABEL_ANO';
        }

        if (empty($eleicao->getTipoProcesso()->getId())) {
            $campos[] = 'LABEL_TIPO_PROCESSO';
        }

        if (!empty($campos)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS, $campos, true);
        }
    }

    /**
     * Valida o ano informado para o Ano do calendario
     *
     * @param $ano
     * @throws NegocioException
     */
    private function validarAno($ano)
    {
        if ($ano < Constants::LIMITE_MIN_ANO || $ano > Constants::LIMITE_MAX_ANO) {
            throw new NegocioException(Message::VALIDACAO_ANO_CALENDARIO);
        }
    }

    /**
     * Retorna a sequencia conforme o ano para realizar o cadastro do Calendario
     *
     * @param $ano
     * @param $recuperaExcluido
     * @return int
     */
    private function getSequenciaAnoCadastro($ano, $recuperaExcluido = false)
    {
        $sequencia = 1;
        $eleicoes = $this->eleicaoRepository->getEleicoes($ano, $recuperaExcluido);

        if (!empty($eleicoes)) {
            $sequencia = $eleicoes[0]->getSequenciaAno() + 1;
        }

        return $sequencia;
    }

    /**
     * Cria a Situação Atual do Calendário para o Cadastro ou Alteração
     *
     * @param Eleicao $eleicao
     * @param $idSituacao
     * @return Eleicao
     * @throws Exception
     */
    public function criarSituacao(Eleicao $eleicao, $idSituacao)
    {
        /** @var SituacaoEleicao $situacaoEleicao */
        $situacaoEleicao = $this->situacaoEleicaoRepository->find($idSituacao);

        $eleicaoSituacao = EleicaoSituacao::newInstance();
        $eleicaoSituacao->setData(Utils::getData());
        $eleicaoSituacao->setEleicao($eleicao);
        $eleicaoSituacao->setSituacaoEleicao($situacaoEleicao);
        $eleicao->setSituacoes(new ArrayCollection());
        $eleicao->getSituacoes()->add($eleicaoSituacao);

        return $eleicao;
    }

    /**
     * Recupera a eleição vigente
     *
     * @param bool $isAddAtividades
     * @return EleicaoTO | null
     */
    public function getEleicaoVigenteComCalendario($isAddAtividades = false)
    {
        return $this->eleicaoRepository->getEleicaoVigenteComCalendario($isAddAtividades);
    }

    /**
     * Recupera a eleição vigente
     *
     * @param bool $isAddAtividades
     * @return EleicaoTO | null
     */
    public function getEleicaoPorCalendario($idCalendario, $isAddAtividades = false)
    {
        return $this->eleicaoRepository->getEleicaoPorCalendario($idCalendario, $isAddAtividades);
    }

    /**
     * Recupera a eleições vigentes com calendário
     *
     * @param bool $isAddAtividades
     * @return EleicaoTO[] | null
     */
    public function getEleicoesVigenteComCalendario($isAddAtividades = false)
    {
        return $this->eleicaoRepository->getEleicoesVigenteComCalendario($isAddAtividades);
    }

    /**
     * Retorna as eleicoes com pedido de substituicao
     *
     * @throws Exception
     */
    public function getEleicoesComPedidoSubstituicao()
    {
        $usuario = $this->getUsuarioFactory()->getUsuarioLogado();

        if ($this->getUsuarioFactory()->isCorporativo()){
            //Caro(a) sr. (a),  você não tem permissão de acesso para visualização da atividade selecionada!
            throw new NegocioException(Message::MSG_SEM_MERMISSAO_VISUALIZACAO_ATIV_SELECIONADA);
        }
        $eleicoes = $this->eleicaoRepository->getEleicaoVigenteComCalendarioEPedidosSubstituicao();

        if (empty($eleicoes)){
            //O Sistema Eleitoral não possui eleição com Pedido de Substituição!
            throw new NegocioException(Message::MSG_ELEICOES_SEM_PEDIDOS_SUBSTITUICAO);
        }
        return $eleicoes;
    }

    /**
     * Retorna as eleicoes com pedido de impugnacao
     *
     * @throws Exception
     */
    public function getEleicoesComPedidoImpugnacao()
    {
        if ($this->getUsuarioFactory()->isCorporativo()){
            //Caro(a) sr. (a),  você não tem permissão de acesso para visualização da atividade selecionada!
            throw new NegocioException(Message::MSG_SEM_MERMISSAO_VISUALIZACAO_ATIV_SELECIONADA);
        }
        $eleicoes = $this->eleicaoRepository->getEleicaoVigenteComCalendarioEPedidosImpugnacao();

        if (empty($eleicoes)){
            //O Sistema Eleitoral não possui eleição com Pedido de Substituição!
            throw new NegocioException(Message::MSG_ELEICOES_SEM_PEDIDOS_SUBSTITUICAO);
        }

        return array_map(function ($dadosEleicoes) {
            return EleicaoTO::newInstance($dadosEleicoes);
        }, $eleicoes);
    }

    /**
     * Retorna as eleicoes com pedido de substituicao
     *
     * @param $idPedidoSubstituicao
     * @param bool $addAtividades
     * @return EleicaoTO
     */
    public function getEleicaoPorPedidoSubstituicao($idPedidoSubstituicao, $addAtividades = false)
    {
        return $this->eleicaoRepository->getEleicaoPorPedidoSubstituicao($idPedidoSubstituicao, $addAtividades);
    }

    /**
     * Retorna as eleicoes com pedido de substituicao
     *
     * @param $idPedidoImpugnacao
     * @param bool $addAtividades
     * @return EleicaoTO
     */
    public function getEleicaoPorPedidoImpugnacao($idPedidoImpugnacao, $addAtividades = false)
    {
        return $this->eleicaoRepository->getEleicaoPorPedidoImpugnacao($idPedidoImpugnacao, $addAtividades);
    }

    /**
     * Recupera a eleição vigente com calendário por uf, se passar uf = 0 ele retorna eleição vigente para IES
     *
     * @param int $idCauUf
     * @return EleicaoTO | null
     */
    public function getEleicoesVigenteComCalendarioPorUf($idCauUf)
    {
        return $this->eleicaoRepository->getEleicaoVigenteComCalendarioPorIesOuUf(
            $idCauUf != 0 ? $idCauUf : null
        );
    }

    /**
     * Retorna as eleicoes com pedido de impugnaçao de resultado
     *
     * @return EleicaoTO[]|array
     * @throws Exception
     */
    public function getEleicoesComPedidoImpugnacaoResultado()
    {
        $eleicoes = $this->eleicaoRepository->getEleicaoVigenteComCalendarioEPedidosImpugnacaoResultado();

        return array_map(function ($dadosEleicoes) {
            return EleicaoTO::newInstance($dadosEleicoes);
        }, $eleicoes);
    }
}
