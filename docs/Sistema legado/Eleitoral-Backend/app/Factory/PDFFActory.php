<?php
/*
 * PDFFActory.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */


namespace App\Factory;

use App\Config\AppConfig;
use App\Config\Constants;
use App\Exceptions\NegocioException;
use App\Service\ArquivoService;
use App\To\ArquivoTO;
use App\To\DocumentoComissaoMembroTO;
use App\To\ExtratoProfissionaisTO;
use App\Util\ImageUtils;
use App\Util\Utils;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use PDFDOM;
use Twig_Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;
use Twig_Loader_Filesystem;

/**
 * Classe responsável por encapsular as gerações de 'PDFs' do sistema.
 *
 * @author Squadra Tecnologia S/A.
 */
class PDFFActory
{
    const MPDF_TTFONTDATAPATH = '_MPDF_TTFONTDATAPATH';

    /**
     *
     * @var Twig_Environment
     */
    private $twig;

    /**
     *
     * @var \App\Service\ArquivoService
     */
    private $arquivoService;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $path = base_path('resources' . DIRECTORY_SEPARATOR . 'relatorios' . DIRECTORY_SEPARATOR);
        $loader = new Twig_Loader_Filesystem($path);
        $this->twig = new Twig_Environment($loader);
    }

    /**
     * Gera documento para testes.
     *
     * @param $membrosComissao
     * @return ArquivoTO
     * @throws MpdfException
     * @throws NegocioException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function gerarDocumentoRelacaoMembros($membrosComissao)
    {
        $caubrId = 165;

        $cabecalhoHtml = $this->twig->render('pdf/cabecalho_comissoes_membro_eleicao.html',[
            'imgheader' => ImageUtils::getImageBase64($this->getArquivoService()->getCaminhoDefaultCabecalho())
        ]);

        $conteudoHtml = $this->twig->render('pdf/conteudo_comissoes_membro_eleicao.html', [
            'caubr' => $caubrId,
            'comissoes' => $membrosComissao
        ]);

        $rodapeHtml = $this->twig->render('pdf/rodape_comissoes_membro_eleicao.html', [
            'imgfooter' => ImageUtils::getImageBase64($this->getArquivoService()->getCaminhoDefaultRodape())
        ]);

        $timestamp = time();
        $nomeDocumento = "Doc Comissao Membros" . $timestamp. ".pdf";
        $caminho = sys_get_temp_dir() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $nomeDocumento . ".pdf";

        $this->gerarPDF($cabecalhoHtml, $conteudoHtml, $rodapeHtml, false, $caminho, null);

        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, $nomeDocumento);
    }

    /**
     * Gera o documento de comissão membro.
     *
     * @param DocumentoComissaoMembroTO $documentoComissaoMembro
     * @param $numeroMembrosComissao
     * @param $caminho
     * @param $nomeDocumento
     * @return ArquivoTO
     * @throws MpdfException
     * @throws NegocioException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function gerarDocumentoPublicacaoComissaoMembro(
        DocumentoComissaoMembroTO $documentoComissaoMembro,
        $numeroMembrosComissao,
        $caminho = null,
        $nomeDocumento = null
    ) {
        $rodapeHtml = '';
        $conteudoHtml = '';
        $cabecalhoHtml = '';

        if ($documentoComissaoMembro->isSituacaoCabecalhoAtivo()) {
            $cabecalhoHtml = $this->twig->render('/pdf/cabecalho_publicacao_comissao_membro.html', [
                'cabecalho' => $documentoComissaoMembro->getDescricaoCabecalho()
            ]);
        }

        $conteudoHtml = $this->twig->render('/pdf/conteudo_publicacao_comissao_membro.html', [
            'textoInicial' => ($documentoComissaoMembro->isSituacaoTextoInicial())? $documentoComissaoMembro->getDescricaoTextoInicial() : '',
            'numeroMembrosComissao' => $numeroMembrosComissao,
            'textoFinal' => ($documentoComissaoMembro->isSituacaoTextoFinal())? $documentoComissaoMembro->getDescricaoTextoFinal() : ''
        ]);

        if ($documentoComissaoMembro->isSituacaoTextoRodape()) {
            $rodapeHtml = $this->twig->render('/pdf/rodape_publicacao_comissao_membro.html', [
                'rodape' => $documentoComissaoMembro->getDescricaoTextoRodape(),
                'isPaginacaoPdf' => true
            ]);
        }

        if (empty($caminho)) {
            $timestamp = time();
            $nomeDocumento = "Doc Comissao Membros" . $timestamp. ".pdf";
            $caminho = sys_get_temp_dir() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $nomeDocumento . ".pdf";
        }

        $this->gerarPDF($cabecalhoHtml, $conteudoHtml, $rodapeHtml, false, $caminho, null);

        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, $nomeDocumento);
    }

    /**
     * Gera o documento PDF com a lista de Conselheiros por UF
     *
     * @param ExtratoProfissionaisTO $extratoProfisssionais
     * @param $nomeArquivo
     * @throws MpdfException
     * @throws NegocioException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function gerarDocumentoListaConselheiros($extratoProfisssionais, $nomeArquivo)
    {
        $cabecalhoHtml = $this->twig->render('pdf/cabecalho_comissoes_membro_eleicao.html',[
            'imgheader' => ImageUtils::getImageBase64($this->getArquivoService()->getCaminhoDefaultCabecalho())
        ]);

        $conteudoHtml = $this->twig->render('conteudo_conselheiros_uf.html', [
            'conselheiros' => $extratoProfisssionais->getProfissionais(),
            'totalProf' => $extratoProfisssionais->getTotalProfissionais(),
            'totalProfAtivos' => $extratoProfisssionais->getTotalProfissionaisAtivos()]);

        $timestamp = time();
        $nomeDocumento = str_replace('/','_', $nomeArquivo) . "_" . $timestamp. ".pdf";
        $caminho = sys_get_temp_dir() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $nomeDocumento;


        $this->gerarPDF($cabecalhoHtml, $conteudoHtml, null, false, $caminho, null);

        //return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, $nomeDocumento);
    }

    /**
     *
     * @param $listaConselheiros
     * @return ArquivoTO
     * @throws MpdfException
     * @throws NegocioException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function gerarDocumentoPDFListaTotalConselheiros($listaConselheiros)
    {
        $conteudoHtml = $this->twig->render('pdf/conteudo_proporcao_conselheiro.html', ['profissionais' => $listaConselheiros]);

        $timestamp = time();
        $nomeDocumento = str_replace('/','_', 'lista_conselheiros_ativos') . "_" . $timestamp. ".pdf";
        $caminho = sys_get_temp_dir() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $nomeDocumento;

        $this->gerarPDF(null, $conteudoHtml, null, false, $caminho, null);

        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, $nomeDocumento);
    }

    /**
     * Gerar PDF de extrato de lista de estados.
     *
     * @param array $listaQuantidadeChapasEstadoTO
     * @return ArquivoTO
     * @throws MpdfException
     * @throws NegocioException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function gerarDocumentoPDFExtratoQuantidadeChapa(array $listaQuantidadeChapasEstadoTO)
    {
        $cabecalhoHtml = $this->twig->render('pdf/cabecalho_comissoes_membro_eleicao.html',[
            'imgheader' => ImageUtils::getImageBase64($this->getArquivoService()->getCaminhoDefaultCabecalho())
        ]);

        $rodapeHtml = $this->twig->render('pdf/rodape_comissoes_membro_eleicao.html', [
            'imgfooter' => ImageUtils::getImageBase64($this->getArquivoService()->getCaminhoDefaultRodape())
        ]);

        $totalChapa = 0;
        foreach ($listaQuantidadeChapasEstadoTO as $estadoTo) {
            $totalChapa += $estadoTo->getQuantidadeTotalChapas();
        }

        $conteudoHtml = $this->twig->render('pdf/conteudo_extrato_quantidade_chapa.html', [
            'ufs' => $listaQuantidadeChapasEstadoTO,
            'totalChapas' => $totalChapa
        ]);

        $timestamp = time();
        $nomeDocumento = str_replace('/','_', 'extrato_quantidade_chapa') . "_" . $timestamp. ".pdf";
        $caminho = sys_get_temp_dir() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $nomeDocumento;

        $this->gerarPDF($cabecalhoHtml, $conteudoHtml, $rodapeHtml, false, $caminho, null);
        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, $nomeDocumento);
    }

    /**
     * Gerar Extrato da chapa em formato PDF
     *
     * @param $dadosExtrato
     * @return Response
     */
    public function gerarDocumentoPDFExtratoChapa($dadosExtrato, $usuarioLogado)
    {
        set_time_limit(0);
        $usuario = $this->getArquivoService()->getCaminhoDefaultUsuarioMasculino();
        $imageUsuario64 = ImageUtils::getImageBase64($usuario);

        $dadosExtrato['foto'] = $imageUsuario64;

        /** @var \Knp\Snappy\Pdf $pdf */
        $pdf = app()->make('snappy.pdf.wrapper');

        $headerHtml = view()->make('relatorios.extrato_chapa.header')->render();
        $footerHtml = view()->make('relatorios.extrato_chapa.footer')->render();

        $pdf->loadView('relatorios.extrato_chapa.extrato', $dadosExtrato)
            ->setOption('encoding', 'utf-8');

        return $pdf->download('Extrato Chapa.pdf');
    }

    /**
     * Gerar Extrato da denúncia em formato PDF
     *
     * @param $dadosExtrato
     * @return Response
     */
    public function gerarDocumentoExtratoDenuncia($dadosExtrato)
    {
        /** @var PDF $pdf */
        $pdf = app()->make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('relatorios.extrato_denuncia.extrato', $dadosExtrato);

        $nome_documento = "documento_denuncia_" . $dadosExtrato["documentoDenuncia"]->getDenuncia()
                ->getNumeroSequencial() . ".pdf";

        return $pdf->download($nome_documento);
    }

    /**
     * Gera documento no formato PDF do pedido de substituição.
     *
     * @param $dadosExtrato
     * @param $usuarioLogado
     * @return ArquivoTO
     * @throws MpdfException
     * @throws NegocioException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws \Exception
     */
    public function gerarDocumentoPDFPedidoSubstituicaoMembro($dadosExtrato, $usuarioLogado)
    {
        $cabecalhoHtml = $this->twig->render('pdf/cabecalho_pedido_substituicao_membro_chapa.html',[
            'imgheader' => ImageUtils::getImageBase64($this->getArquivoService()->getCaminhoDefaultCabecalho())
        ]);

        $rodapeHtml = $this->twig->render('pdf/rodape_pedido_substituicao_membro_chapa.html', [
            'data' => Utils::getStringFromDate(Utils::getData(), 'd/m/Y'),
            'hora' => Utils::getStringFromDate(Utils::getData(), 'H:i'),
            'nomeUsuario' => $usuarioLogado->nome
        ]);

        $conteudoHtml = $this->twig->render('pdf/conteudo_pedido_substituicao_membro_chapa.html', [
            'dadosExtrato' => $dadosExtrato,
            'idChapaConcluida' => Constants::SITUACAO_CHAPA_CONCLUIDA,
            'idChapaPendente' => Constants::SITUACAO_CHAPA_PENDENTE,
            'idTipoCandidaturaIES' => Constants::TIPO_CANDIDATURA_IES,
            'idTipoCandidaturaUFBR' => Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR
        ]);

        $timestamp = time();
        $nomeDocumento = str_replace('/','_', 'pedido_substituicao_membro_chapa') . "_" . $timestamp. ".pdf";
        $caminho = sys_get_temp_dir() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $nomeDocumento;

        $this->gerarPDF($cabecalhoHtml, $conteudoHtml, $rodapeHtml, false, $caminho, null);
        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, $nomeDocumento);
    }

    /**
     * Gera documento no formato PDF do julgamento de substituição.
     *
     * @param $dadosExtrato
     * @param $usuarioLogado
     * @return ArquivoTO
     * @throws MpdfException
     * @throws NegocioException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws \Exception
     */
    public function gerarDocumentoPDFJulgamentoSubstituicao($dadosExtrato, $usuarioLogado)
    {
        $cabecalhoHtml = $this->twig->render('pdf/cabecalho_julgamento_substituicao.html',[
            'imgheader' => ImageUtils::getImageBase64($this->getArquivoService()->getCaminhoDefaultCabecalho())
        ]);

        $rodapeHtml = $this->twig->render('pdf/rodape_julgamento_substituicao.html', [
            'data' => Utils::getStringFromDate(Utils::getData(), 'd/m/Y'),
            'hora' => Utils::getStringFromDate(Utils::getData(), 'H:i'),
            'nomeUsuario' => $usuarioLogado->nome
        ]);

        $conteudoHtml = $this->twig->render('pdf/conteudo_julgamento_substituicao.html', [
            'dadosExtrato' => $dadosExtrato,
            'idChapaConcluida' => Constants::SITUACAO_CHAPA_CONCLUIDA,
            'idChapaPendente' => Constants::SITUACAO_CHAPA_PENDENTE,
            'idTipoCandidaturaIES' => Constants::TIPO_CANDIDATURA_IES,
            'idTipoCandidaturaUFBR' => Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR
        ]);

        $timestamp = time();
        $nomeDocumento = str_replace('/','_', 'julgamento_substituicao') . "_" . $timestamp. ".pdf";
        $caminho = sys_get_temp_dir() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $nomeDocumento;

        $this->gerarPDF($cabecalhoHtml, $conteudoHtml, $rodapeHtml, false, $caminho, null);
        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, $nomeDocumento);
    }

    /**
     * Gera documento no formato PDF do julgamento de substituição.
     *
     * @param $dadosExtrato
     * @param $usuarioLogado
     * @return ArquivoTO
     * @throws MpdfException
     * @throws NegocioException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws \Exception
     */
    public function gerarDocumentoPDFJulgamentoImpugnnacao($dadosExtrato, $usuarioLogado)
    {
        $cabecalhoHtml = $this->twig->render('pdf/cabecalho_padrao.html',[
            'imgheader' => ImageUtils::getImageBase64($this->getArquivoService()->getCaminhoDefaultCabecalho())
        ]);

        $rodapeHtml = $this->twig->render('pdf/rodape_padrao.html', [
            'data' => Utils::getStringFromDate(Utils::getData(), 'd/m/Y'),
            'hora' => Utils::getStringFromDate(Utils::getData(), 'H:i'),
            'nomeUsuario' => $usuarioLogado->nome
        ]);

        $conteudoHtml = $this->twig->render('pdf/conteudo_julgamento_impugnacao.html', [
            'dadosExtrato' => $dadosExtrato,
            'idChapaConcluida' => Constants::SITUACAO_CHAPA_CONCLUIDA,
            'idChapaPendente' => Constants::SITUACAO_CHAPA_PENDENTE,
            'idTipoCandidaturaIES' => Constants::TIPO_CANDIDATURA_IES
        ]);

        $timestamp = time();
        $nomeDocumento = str_replace('/','_', 'julgamento_pedido_impugnacao') . "_" . $timestamp. ".pdf";
        $caminho = sys_get_temp_dir() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $nomeDocumento;

        $this->gerarPDF($cabecalhoHtml, $conteudoHtml, $rodapeHtml, false, $caminho, null);
        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, $nomeDocumento);
    }

    /**
     * Gera o PDF de acordo com os valores informados.
     *
     * @param $cabecalhoHtml
     * @param $conteudoHtml
     * @param $rodapeHtml
     * @param bool $isPrevia
     * @param null $caminho
     * @param null $waterMarkText
     * @throws MpdfException
     */
    private function gerarPDF(
        $cabecalhoHtml,
        $conteudoHtml,
        $rodapeHtml,
        $isPrevia = true,
        $caminho = null,
        $waterMarkText = null
    )
    {
        // Solução para evitar o problema: 'failed to open stream: Permission denied'.
        define(self::MPDF_TTFONTDATAPATH, sys_get_temp_dir() . DIRECTORY_SEPARATOR);

        $mPdf = new Mpdf(['setAutoTopMargin' => 'stretch']);
        $mPdf->setAutoBottomMargin = 'stretch';

        if ($isPrevia) {
            $mPdf->SetWatermarkText($waterMarkText);
            $mPdf->showWatermarkText = true;
        }

        $mPdf->SetDisplayMode('fullpage');
        $mPdf->SetHTMLHeader($cabecalhoHtml);

        // O Footer deve vir sempre antes do conteúdo HTML, pois se não for assim só gera na última página.
        $mPdf->SetHTMLFooter($rodapeHtml);
        ini_set("pcre.backtrack_limit", "1000000000");
        $mPdf->WriteHTML($conteudoHtml);

        if ($caminho == null) {
            $mPdf->Output();
        } else {
            $mPdf->Output($caminho, 'F');
        }
    }

    /**
     * Retorna a instância 'ArquivoService' conforme o padrão lazy inicialization.
     *
     * @return ArquivoService|mixed
     */
    private function getArquivoService()
    {
        if ($this->arquivoService == null) {
            $this->arquivoService = app()->make(ArquivoService::class);
        }

        return $this->arquivoService;
    }
}
