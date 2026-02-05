<?php
/*
 * PedidoImpugnacaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Config\Constants;
use App\Entities\ArquivoPedidoImpugnacao;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\ChapaEleicao;
use App\Entities\Declaracao;
use App\Entities\DeclaracaoAtividade;
use App\Entities\ItemDeclaracao;
use App\Entities\ItemRespostaDeclaracao;
use App\Entities\MembroChapa;
use App\Entities\PedidoImpugnacao;
use App\Entities\PedidoSubstituicaoChapa;
use App\Entities\RespostaDeclaracao;
use App\Entities\StatusPedidoImpugnacao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Jobs\EnviarEmailPedidoImpugnacaoJob;
use App\Jobs\EnviarPedidoSubstituicaoChapaCadastradaJob;
use App\Repository\ArquivoPedidoImpugnacaoRepository;
use App\Repository\AtividadeSecundariaCalendarioRepository;
use App\Repository\ItemRespostaDeclaracaoRepository;
use App\Repository\MembroChapaRepository;
use App\Repository\PedidoImpugnacaoRepository;
use App\Service\ArquivoService;
use App\To\ArquivoTO;
use App\To\EleicaoTO;
use App\To\ItemRespostaDeclaracaoTO;
use App\To\PedidoSubstituicaoChapaTO;
use App\To\RespostaDeclaracaoTO;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Predis\Response\Status;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'ArquivoPedidoImpugnacao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class ArquivoPedidoImpugnacaoBO extends AbstractBO
{

    /**
     * @var ArquivoService
     */
    private $arquivoService;

    /**
     * @var ArquivoPedidoImpugnacaoRepository
     */
    private $arquivoPedidoImpugnacaoRepository;


    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salva os arquivos do pedido de impugnação
     *
     * @param PedidoImpugnacao $pedidoImpugnacao
     * @param ArquivoPedidoImpugnacao[] $arquivosPedidoImpugnacao
     * @return mixed
     * @throws NegocioException
     * @throws \Exception
     */
    public function salvar($pedidoImpugnacao, $arquivosPedidoImpugnacao)
    {
        $caminho = $this->getArquivoService()->getCaminhoRepositorioPedidoImpugnacao($pedidoImpugnacao->getId());

        /** @var ArquivoPedidoImpugnacao $arquivoPedidoImpugnacao */
        foreach ($arquivosPedidoImpugnacao as $arquivoPedidoImpugnacao) {

            $nomeArquivoFisico = $this->getArquivoService()->getNomeArquivoFormatado(
                $arquivoPedidoImpugnacao->getNome(),
                Constants::PREFIXO_ARQ_PEDIDO_IMPUGNACAO
            );

            $this->getArquivoService()->salvar($caminho, $nomeArquivoFisico, $arquivoPedidoImpugnacao->getArquivo());

            $arquivoPedidoImpugnacao->setNomeFisico($nomeArquivoFisico);
            $arquivoPedidoImpugnacao->setPedidoImpugnacao($pedidoImpugnacao);
            $arquivoPedidoImpugnacao->setArquivo(null);

            $this->getArquivoPedidoImpugnacaoRepository()->persist($arquivoPedidoImpugnacao);
        }
    }

    /**
     * Método auxiliar para validar arquivo pedido impugnação
     *
     * @param ArquivoPedidoImpugnacao[] $arquivosPedidoImpugnacao
     * @throws NegocioException
     */
    public function validarArquivosDocumentoComprobatorio($arquivosPedidoImpugnacao)
    {
        if (count($arquivosPedidoImpugnacao) == 0) {
            throw new NegocioException(Message::MSG_MINIMO_UM_DOC_COMPROBATORIO);
        }

        if (count($arquivosPedidoImpugnacao) > 5) {
            throw new NegocioException(
                Message::MSG_PERMISSAO_UPLOAD_MAXIMO,
                [Constants::QTD_MAXIMA_ARQUIVOS_PEDIDO_IMPUGNACAO]
            );
        }

        /** @var ArquivoPedidoImpugnacao $arquivoPedidoImpugnacao */
        foreach ($arquivosPedidoImpugnacao as $arquivoPedidoImpugnacao) {

            $dadosTOValidarArquivo = new \stdClass();
            $dadosTOValidarArquivo->nome = $arquivoPedidoImpugnacao->getNome();
            $dadosTOValidarArquivo->tamanho = $arquivoPedidoImpugnacao->getTamanho();
            $dadosTOValidarArquivo->tipoValidacao = Constants::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_10MB;
            $this->getArquivoService()->validarArquivo($dadosTOValidarArquivo);

            if (empty($arquivoPedidoImpugnacao->getArquivo())) {
                throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
            }
        }
    }

    /**
     * Disponibiliza o arquivo conforme o 'id' informado.
     *
     * @param integer $idArquivoPedidoImpugnacao
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoPedidoImpugnacao($idArquivoPedidoImpugnacao)
    {
        /** @var ArquivoPedidoImpugnacao $arquivoPedidoImpugnacao */
        $arquivoPedidoImpugnacao = $this->getArquivoPedidoImpugnacaoRepository()->find($idArquivoPedidoImpugnacao);

        if (!empty($arquivoPedidoImpugnacao)) {
            $caminho = $this->getArquivoService()->getCaminhoRepositorioPedidoImpugnacao(
                $arquivoPedidoImpugnacao->getPedidoImpugnacao()->getId()
            );

            return $this->getArquivoService()->getArquivo(
                $caminho,
                $arquivoPedidoImpugnacao->getNomeFisico(),
                $arquivoPedidoImpugnacao->getNome()
            );
        }
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
     * Retorna uma nova instância de 'ArquivoPedidoImpugnacaoRepository'.
     *
     * @return ArquivoPedidoImpugnacaoRepository|mixed
     */
    private function getArquivoPedidoImpugnacaoRepository()
    {
        if (empty($this->arquivoPedidoImpugnacaoRepository)) {
            $this->arquivoPedidoImpugnacaoRepository = $this->getRepository(ArquivoPedidoImpugnacao::class);
        }

        return $this->arquivoPedidoImpugnacaoRepository;
    }

}




