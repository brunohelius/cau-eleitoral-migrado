<?php


namespace App\Factory;

use App\Config\AppConfig;
use App\Exceptions\NegocioException;
use App\Service\ArquivoService;
use App\To\DocumentoComissaoMembroTO;
use App\Util\ImageUtils;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use stdClass;
use Twig_Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;
use Twig_Loader_Filesystem;

/**
 * Classe responsável por encapsular as gerações de 'doc/docx' do sistema.
 *
 * @author Squadra Tecnologia S/A.
 */
class DocFactory
{

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
        $this->arquivoService = app()->make(ArquivoService::class);

        $path = base_path('resources' . DIRECTORY_SEPARATOR . 'relatorios' . DIRECTORY_SEPARATOR);
        $loader = new Twig_Loader_Filesystem($path);
        $this->twig = new Twig_Environment($loader);
    }

    /**
     * Gera o docx para a relação de membros.
     *
     * @param $membrosComissao
     * @return \App\To\ArquivoTO
     * @throws Exception
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws NegocioException
     */
    public function gerarDocumentoRelacaoMembros($membrosComissao)
    {
        $caubrId = 165;

        $cabecalhoHtml = $this->twig->render('doc/cabecalho_comissoes_membro_eleicao.html', [
            'imgheader' => ImageUtils::getImageBase64($this->getArquivoService()->getCaminhoDefaultCabecalho())
        ]);

        $conteudoHtml = $this->twig->render('doc/conteudo_comissoes_membro_eleicao.html', [
            'comissoes' => $membrosComissao,
            'caubr' => $caubrId
        ]);

        $rodapeHtml = $this->twig->render('doc/rodape_comissoes_membro_eleicao.html', [
            'imgfooter' => ImageUtils::getImageBase64($this->getArquivoService()->getCaminhoDefaultRodape())
        ]);

        $timestamp = time();
        $nomeDocumento = "Doc Relacao Membros" . $timestamp. ".docx";
        $caminho = sys_get_temp_dir() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $nomeDocumento . ".docx";

        $this->gerarDoc($cabecalhoHtml, $conteudoHtml, $rodapeHtml, $caminho);
        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, $nomeDocumento);
    }

    /**
     * Gera o documento de publicação de comissão membro.
     *
     * @param DocumentoComissaoMembroTO $documentoComissaoMembro
     * @param $numeroMembrosComissao
     * @param null $caminho
     * @param null $nomeDocumento
     * @return \App\To\ArquivoTO
     * @throws Exception
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws NegocioException
     */
    public function gerarDocumentoPublicacaoComissaoEleitoral(
        DocumentoComissaoMembroTO $documentoComissaoMembro,
        $numeroMembrosComissao,
        $caminho = null,
        $nomeDocumento = null
    ) {
        $rodapeHtml = '';
        $cabecalhoHtml = '';
        $conteudoFinalHtml = '';
        $conteudoInicialHtml = '';

        if ($documentoComissaoMembro->isSituacaoCabecalhoAtivo()) {
            $cabecalhoHtml = $this->twig->render('/doc/cabecalho_publicacao_comissao_membro.html', [
                'cabecalho' => $documentoComissaoMembro->getDescricaoCabecalho()
            ]);
        }

        if ($documentoComissaoMembro->isSituacaoTextoInicial()) {
            $conteudoInicialHtml = $this->twig->render('/doc/conteudo_publicacao_comissao_membro_texto_inicial.html', [
                'textoInicial' => $documentoComissaoMembro->getDescricaoTextoInicial(),
            ]);
        }

        if ($documentoComissaoMembro->isSituacaoTextoFinal()) {
            $conteudoFinalHtml = $this->twig->render('/doc/conteudo_publicacao_comissao_membro_texto_final.html', [
                'textoFinal' => $documentoComissaoMembro->getDescricaoTextoFinal(),
            ]);
        }

        if ($documentoComissaoMembro->isSituacaoTextoRodape()) {
            $rodapeHtml = $this->twig->render('/doc/rodape_publicacao_comissao_membro.html', [
                'rodape' => $documentoComissaoMembro->getDescricaoTextoRodape()
            ]);
        }

        $numeroMembroComissaoFormatado = new ArrayCollection();

        for ($index = 0; $index < count($numeroMembrosComissao[0]); $index++) {
            $elemento = new ArrayCollection();

            if (!empty($numeroMembrosComissao[0]->get($index))) {
                $elemento->add($numeroMembrosComissao[0]->get($index));
            }

            if (!empty($numeroMembrosComissao[1]->get($index))) {
                $elemento->add($numeroMembrosComissao[1]->get($index));
            }

            if (!empty($numeroMembrosComissao[2]->get($index))) {
                $elemento->add($numeroMembrosComissao[2]->get($index));
            }

            if (!empty($numeroMembrosComissao[3]->get($index))) {
                $elemento->add($numeroMembrosComissao[3]->get($index));
            }

            $numeroMembroComissaoFormatado->add($elemento);
        }

        if (empty($caminho)) {
            $timestamp = time();
            $nomeDocumento = "Doc Comissao Membros" . $timestamp. ".docx";
            $caminho = sys_get_temp_dir() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $nomeDocumento . ".docx";
        }

        $this->gerarDocPublicacaoComissaoMembro(
            $cabecalhoHtml,
            $conteudoInicialHtml,
            $numeroMembroComissaoFormatado,
            $conteudoFinalHtml,
            $rodapeHtml,
            $caminho
        );

        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, $nomeDocumento);
    }

    /**
     * Gera o documento.
     *
     * @throws Exception
     */
    private function gerarDoc($cabecalhoHtml, $conteudoHtml, $rodapeHtml, $caminho)
    {
        $phpWord = new PhpWord();

        $properties = $phpWord->getDocInfo();
        $properties->setCreator('CAU/BR');
        $properties->setCompany('CAU/BR');

        $section = $phpWord->addSection([
            'pageNumberingStart' => 1
        ]);

        $header = $section->addHeader();
        $footer = $section->addFooter();

        $cabecalhoHtmlFormatado = '<body>' . $cabecalhoHtml . '</body>';
        $conteudoHtmlFormatado = '<body>' . $conteudoHtml . '</body>';
        $rodapeHtmlFormatado = '<body>' . $rodapeHtml . '</body>';

        Html::addHtml($header, $cabecalhoHtmlFormatado, true, false);
        Html::addHtml($section, $conteudoHtmlFormatado, true, false);
        Html::addHtml($footer, $rodapeHtmlFormatado, true, false);

        $footer->addPreserveText('{PAGE}', array(), array('align' => 'right'));

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($caminho);
    }

    /**
     * Responsável por gerar o documento de publicação de comissão membro.
     *
     * @param $cabecalhoHtml
     * @param $conteudoInicioHtml
     * @param $numeroMembrosComissao
     * @param $conteudoFinalHtml
     * @param $rodapeHtml
     * @param $caminho
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    private function gerarDocPublicacaoComissaoMembro(
        $cabecalhoHtml,
        $conteudoInicioHtml,
        $numeroMembrosComissao,
        $conteudoFinalHtml,
        $rodapeHtml,
        $caminho
    ) {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection([
            'pageNumberingStart' => 1
        ]);

        $header = $section->addHeader();
        $footer = $section->addFooter();

        $cabecalhoHtmlFormatado = '<body>' . $cabecalhoHtml . '</body>';
        $conteudoInicialHtmlFormatado = '<body>' . $conteudoInicioHtml . '</body>';
        $conteudoFinalHtmlFormatado = '<body>' . $conteudoFinalHtml . '</body>';
        $rodapeHtmlFormatado = '<body>' . $rodapeHtml . '</body>';

        Html::addHtml($header, $cabecalhoHtmlFormatado, true, false);
        Html::addHtml($section, $conteudoInicialHtmlFormatado, true, false);

        $estiloFonte = array('color' => 'ffffff');
        $estiloCelulaBgCau = array('align' => 'center', 'valign' => 'bottom', 'bgcolor' => '019ca4');
        $estiloCelulaBgCauEscuro = array('align' => 'center', 'valign' => 'bottom', 'bgcolor' => '016C71');
        $estiloTabela = array(
            'width' => 70 * 70,
            'unit' => 'pct',
            'borderSize' => 6,
            'borderColor' => '006699',
            'cellMargin' => 80
        );

        $table = $section->addTable($estiloTabela);
        $table->addRow(1000);
        $table->addCell(500, $estiloCelulaBgCauEscuro)->addText('UF', $estiloFonte, array('align' => 'center', 'spaceAfter' => 15));
        $table->addCell(500, $estiloCelulaBgCau)->addText('Quant', $estiloFonte, array('align' => 'center', 'spaceAfter' => 15));
        $table->addCell(500, $estiloCelulaBgCauEscuro)->addText('UF', $estiloFonte, array('align' => 'center', 'spaceAfter' => 15));
        $table->addCell(500, $estiloCelulaBgCau)->addText('Quant', $estiloFonte, array('align' => 'center', 'spaceAfter' => 15));
        $table->addCell(500, $estiloCelulaBgCauEscuro)->addText('UF', $estiloFonte, array('align' => 'center', 'spaceAfter' => 15));
        $table->addCell(500, $estiloCelulaBgCau)->addText('Quant', $estiloFonte, array('align' => 'center', 'spaceAfter' => 15));
        $table->addCell(500, $estiloCelulaBgCauEscuro)->addText('UF', $estiloFonte, array('align' => 'center', 'spaceAfter' => 15));
        $table->addCell(500, $estiloCelulaBgCau)->addText('Quant', $estiloFonte, array('align' => 'center', 'spaceAfter' => 15));

        foreach ($numeroMembrosComissao as $nuMembroComissao) {
            $table->addRow();

            foreach ($nuMembroComissao as $elemento) {
                $corTexto = null;
                $quantidade = $elemento->getQuantidade();

                if ($quantidade == 0) {
                    $quantidade = 'N/A';
                    $corTexto = array('color' => 'red');
                }

                $table->addCell(1000)->addText(htmlspecialchars($elemento->getDescricao()), array(), array('align' => 'center'));
                $table->addCell(1000)->addText(htmlspecialchars($quantidade), $corTexto, array('align' => 'center'));
            }
        }

        Html::addHtml($section, $conteudoFinalHtmlFormatado, true, false);
        Html::addHtml($footer, $rodapeHtmlFormatado, true, false);

        $footer->addPreserveText('{PAGE}', array(), array('align' => 'right'));

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($caminho);
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
