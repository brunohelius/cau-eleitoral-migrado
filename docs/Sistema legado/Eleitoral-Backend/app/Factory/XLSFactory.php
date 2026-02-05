<?php


namespace App\Factory;

use App\Business\ProfissionalBO;
use App\Config\AppConfig;
use App\Config\Constants;
use App\Entities\ParametroConselheiro;
use App\Exceptions\NegocioException;
use App\Service\ArquivoService;
use App\To\ArquivoTO;
use App\To\ExtratoProfissionaisTO;
use App\Util\Utils;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Classe responsável por encapsular as gerações de 'xls/xlsx' do sistema.
 *
 * @author Squadra Tecnologia S/A.
 */
class XLSFactory
{
    /**
     * @var ProfissionalBO
     */
    private $profissionalBO;

    /**
     *
     * @var Twig_Environment
     */
    private $twig;

    /**
     *
     * @var ArquivoService
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
     * Retorna o arquivo conforme o 'caminho' e o 'nome' informado.
     *
     * @param $listaConselheiros
     * @param $nomeArquivo
     * @return ArquivoTO
     * @throws NegocioException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws Exception
     */
    public function gerarDocumentoXSLListaConselheiros($listaConselheiros, $nomeArquivo)
    {
        $linhaAtual = 6;
        $spreadsheet = new Spreadsheet();
        /** @var  $sheet Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        $this->gerarCabecalhoListaProfissionais($sheet, $listaConselheiros);

        foreach ($listaConselheiros->detalhada as $conselheiro) {
            $dsDataFim = (!empty($conselheiro->dataFim)) ? $conselheiro->dataFim : "Sem Limite";

            $sheet->setCellValue('A' . $linhaAtual, $conselheiro->nome);
            //Setando o campo CPF como String
            $sheet->setCellValueExplicit('B' . $linhaAtual, Utils::getCpfFormatado($conselheiro->cpf) , DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $linhaAtual, $conselheiro->registroRegional);
            $sheet->setCellValue('D' . $linhaAtual, $dsDataFim);
            $sheet->setCellValue('E' . $linhaAtual, $conselheiro->descricao);
            $sheet->setCellValue('F' . $linhaAtual, ($conselheiro->cauUf == Constants::DESCRICAO_CAU_BR) ? Constants::PREFIXO_CAU_BR : $conselheiro->cauUf);

            $this->adicionarBorda($sheet, 'A' . $linhaAtual, 'B' . $linhaAtual);
            $this->adicionarBorda($sheet, 'B' . $linhaAtual, 'C' . $linhaAtual);
            $this->adicionarBorda($sheet, 'C' . $linhaAtual, 'D' . $linhaAtual);
            $this->adicionarBorda($sheet, 'D' . $linhaAtual, 'E' . $linhaAtual);
            $this->adicionarBorda($sheet, 'E' . $linhaAtual, 'F' . $linhaAtual);

            $linhaAtual++;
        }

        //Setando o tamanho automático das colunas
        foreach(range('A','F') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $timestamp = time();
        $nomeDocumento = str_replace('/','_', $nomeArquivo) . "_" . $timestamp. ".xls";
        $caminho = sys_get_temp_dir() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $nomeDocumento . ".xls";

        $this->gerarXLS($spreadsheet, $caminho);
        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, $nomeDocumento);
    }

    /**
     * Gera o cabeçalho para a funcionalidade de listagemd e conselheiros.
     *
     * @param $spreadsheet
     * @param $sheet
     * @param ExtratoProfissionaisTO $extratoProfisssionais
     * @param string|null $sigla
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function gerarCabecalhoListaProfissionais(&$spreadsheet, &$sheet, ExtratoProfissionaisTO $extratoProfisssionais, $sigla = null)
    {
        $titulo = 'Extrato com Número de Profissionais Ativos';
        if(!empty($sigla)) {
            $titulo .= ' - '. $sigla;
        }

        /** @var $sheet Worksheet */
        $sheet->setCellValue('A1', $titulo);

        if (!empty($extratoProfisssionais->getTotalProfissionaisAtivos())) {
            $sheet->setCellValue('A3', 'Total de Profissionais Ativos: '.$extratoProfisssionais->getTotalProfissionaisAtivos());
        }

        if (!empty($extratoProfisssionais->getTotalProfissionais())) {
            $sheet->setCellValue('A4', 'Total de Profissionais: '.$extratoProfisssionais->getTotalProfissionais());
        }

        $sheet->mergeCells("A1:F2");
        $sheet->mergeCells("A3:F3");
        $sheet->mergeCells("A4:F4");

        $sheet->setCellValue('A5', 'Nome do Profissional');
        $sheet->setCellValue('A5', 'Nome do Profissional');
        $sheet->setCellValue('B5', 'CPF');
        $sheet->setCellValue('C5', 'Registro CAU');
        $sheet->setCellValue('D5', 'Data Fim do Registro');
        $sheet->setCellValue('E5', 'Situação do Registro');
        $sheet->setCellValue('F5', 'UF');

        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A5:F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $this->adicionarBorda($sheet, 'A5', 'B5');
        $this->adicionarBorda($sheet, 'B5', 'C5');
        $this->adicionarBorda($sheet, 'C5', 'D5');
        $this->adicionarBorda($sheet, 'D5', 'E5');
        $this->adicionarBorda($sheet, 'E5', 'F5');

        //Setando o tamanho automático das colunas
        foreach(range('A','F') as $columnID) {
            $sheet->getColumnDimension($columnID)
                ->setAutoSize(true);

        }
    }

    /**
     * Recupera o cabeçalho da lista do total de conselheiros.
     *
     * @param $sheet
     * @param $listaConselheiros
     * @return mixed
     */
    private function gerarCabecalhoListaTotalConselheiros($sheet, $listaConselheiros)
    {
        $sheet->setCellValue('A1', 'Número de Conselheiros Ativos');
        $sheet->mergeCells("A1:G2");
        $sheet->getStyle("A1")->getFont()->setSize(18);
        $sheet->getStyle("A1")->getFont()->setBold(true);
        $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

        $sheet->getStyle("A")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("B")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("E")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("F")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("G")->getAlignment()->setHorizontal('center');

        $sheet->getStyle("A")->getAlignment()->setVertical('center');
        $sheet->getStyle("B")->getAlignment()->setVertical('center');
        $sheet->getStyle("C")->getAlignment()->setVertical('center');
        $sheet->getStyle("D")->getAlignment()->setVertical('center');
        $sheet->getStyle("E")->getAlignment()->setVertical('center');
        $sheet->getStyle("F")->getAlignment()->setVertical('center');
        $sheet->getStyle("G")->getAlignment()->setVertical('center');

        $sheet->setCellValue('A3', 'UF');
        $sheet->setCellValue('B3', "Qtd Profissional\n Ativo");
        $sheet->setCellValue('C3', "Proporção\n Conselheiro");
        $sheet->setCellValue('D3', 'Lei 12378, art 32');
        $sheet->setCellValue('E3', 'Editado');
        $sheet->setCellValue('F3', "Observação\n da Edição");
        $sheet->setCellValue('G3', "Responsável\n pela Edição");

        $sheet->getStyle('A3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('B3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('E3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('G3')->getAlignment()->setWrapText(true);

        $sheet->getStyle("A3")->getFont()->setBold(true);
        $sheet->getStyle("B3")->getFont()->setBold(true);
        $sheet->getStyle("C3")->getFont()->setBold(true);
        $sheet->getStyle("D3")->getFont()->setBold(true);
        $sheet->getStyle("E3")->getFont()->setBold(true);
        $sheet->getStyle("F3")->getFont()->setBold(true);
        $sheet->getStyle("G3")->getFont()->setBold(true);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);

        return $sheet;
    }

    /**
     *
     * @param $listaConselheiros
     * @return mixed
     * @throws NegocioException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws Exception
     */
    public function gerarDocumentoXSLListaTotalConselheiros($listaConselheiros)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet = $this->gerarCabecalhoListaTotalConselheiros($sheet, $listaConselheiros['listaProfissionais']);
        $colorRed = new Color(Color::COLOR_RED);

        $linhaAtual = 4;
        /** @var ParametroConselheiro $conselheiro */
        foreach ($listaConselheiros['listaProfissionais'] as $conselheiro) {
            $descricaoLei = $conselheiro->getIdCauUf() != Constants::ID_IES_LISTA_CONSELHEIROS
                ? $conselheiro->getLei()->getDescricao()
                : '-';

            $sheet->setCellValue('A'.$linhaAtual, $conselheiro->getPrefixo());
            $sheet->setCellValue('B'.$linhaAtual, $conselheiro->getQtdProfissional());
            $sheet->setCellValue('C'.$linhaAtual, $conselheiro->getNumeroProporcaoConselheiro());
            $sheet->setCellValue('D'.$linhaAtual, $descricaoLei);
            $sheet->setCellValue('E'.$linhaAtual, ($conselheiro->isSituacaoEditado() )? 'Sim' : 'Não');
            $sheet->setCellValue('F'.$linhaAtual, (!empty($conselheiro->getHistoricoParametroRecente()))? $conselheiro->getHistoricoParametroRecente()->getDescricao() : 'N/A' );
            $sheet->setCellValue('G'.$linhaAtual, (!empty($conselheiro->getHistoricoParametroRecente()))? $conselheiro->getHistoricoParametroRecente()->getNomeResponsavel() : 'N/A');

            if ($conselheiro->isSituacaoEditado()) {
                $sheet->getStyle('C'.$linhaAtual)->getFont()->setColor($colorRed);
                $sheet->getStyle('E'.$linhaAtual)->getFont()->setColor($colorRed);
            }

            $linhaAtual++;
        }

        if (!empty($listaConselheiros['totalProfAtivo'])) {
            $sheet->setCellValue('A'.($linhaAtual+1), 'Total de Profissionais Ativos: '.$listaConselheiros['totalProfAtivo']);
        }
        if (!empty($listaConselheiros['totalProf'])) {
            $sheet->setCellValue('A'.($linhaAtual+2), 'Total de Profissionais: '.$listaConselheiros['totalProf']);
        }

        $timestamp = time();
        $nomeDocumento = str_replace('/','_', 'lista_conselheiros_ativos') . "_" . $timestamp. ".xls";
        $caminho = sys_get_temp_dir() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $nomeDocumento . ".xls";

        $this->gerarXLS($spreadsheet, $caminho);
        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, $nomeDocumento);
    }

    /**
     * Adiciona a borda no XLS entre a coordenada inicial e a coordenada final informada.
     *
     * @param Worksheet $sheet
     * @param string $coordInicial
     * @param string $coordFinal
     */
    private function adicionarBorda(Worksheet &$sheet, string $coordInicial, string $coordFinal)
    {
        try {
            $sheet->getStyle($coordInicial . ':' . $coordFinal)->applyFromArray(
                $this->getEstiloBordaListaConselheiros()
            );
        } catch (\Exception $e) {
            new NegocioException($e);
        }
    }


    /**
     *
     * @param ExtratoProfissionaisTO $extratoProfisssionais
     * @param $nomeArquivo
     * @param $idHistoricoExtratoConselheiro
     * @return void
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function gerarDocumentoXSLXTotalProfissionais($extratoProfisssionais, $nomeArquivo, $idHistoricoExtratoConselheiro, $sigla = null)
    {
        ini_set("memory_limit", "-1");
        set_time_limit(0);
        $linhaAtual = 6;
        $spreadsheet = new Spreadsheet();

        /** @var $sheet Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        $this->gerarCabecalhoListaProfissionais($spreadsheet, $sheet, $extratoProfisssionais, $sigla);
        $this->adicionaProfissionais($extratoProfisssionais, $spreadsheet, $sheet, $linhaAtual);

        AppConfig::getRepositorio(Constants::PATH_STORAGE_ARQUIVO_EXTRATO_PROFISSIONAIS);
        $caminhoCompleto = AppConfig::getRepositorio(
            Constants::PATH_STORAGE_ARQUIVO_EXTRATO_PROFISSIONAIS
            . DIRECTORY_SEPARATOR . $idHistoricoExtratoConselheiro,
            $nomeArquivo);

        $this->gerarXLS($spreadsheet, $caminhoCompleto.'.xlsx');
    }

    /**
     * Retorna a configuração de borda utilizada na exportação do XLS.
     *
     * @return array
     */
    private function getEstiloBordaListaConselheiros()
    {
        return array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );
    }

    /**
     * Gera o arquivo XLS.
     *
     * @param $spreadsheet
     * @param $caminho
     * @throws Exception
     */
    public function gerarXLS($spreadsheet, $caminho)
    {
        $writer = new Xlsx($spreadsheet);
        $writer->save($caminho);
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

    /**
     * @param ExtratoProfissionaisTO $extratoProfisssionais
     * @param Spreadsheet $spreadsheet
     * @param Worksheet $sheet
     * @param int $linhaAtual
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function adicionaProfissionais($extratoProfisssionais, &$spreadsheet, Worksheet &$sheet, int $linhaAtual)
    {
        foreach ($extratoProfisssionais->getProfissionais() as $profissionalTO) {

            $dsDataFim = "Sem Limite";
            if (!empty($profissionalTO->getDataFimRegistro()) && !($profissionalTO->getDataFimRegistro() instanceof \DateTime)) {
                $date = Utils::getDataToString($profissionalTO->getDataFimRegistro(), 'Y-m-d H:i:s');

                $dsDataFim = Date::PHPToExcel($date);

                $spreadsheet->getActiveSheet()->getStyle("D{$linhaAtual}")->getNumberFormat()->setFormatCode(
                    NumberFormat::FORMAT_DATE_DDMMYYYY
                );
            }

            $sheet->setCellValue('A' . $linhaAtual, $profissionalTO->getNome());
            //Setando o campo CPF como String
            $sheet->setCellValueExplicit('B' . $linhaAtual, Utils::getCpfFormatado($profissionalTO->getCpf()), DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $linhaAtual, Utils::getRegistroNacionalFormatado($profissionalTO->getNumeroRegistro()));

            $sheet->setCellValue('D' . $linhaAtual, $dsDataFim);
            $sheet->setCellValue('E' . $linhaAtual, $profissionalTO->getSituacaoRegistro());
            $sheet->setCellValue('F' . $linhaAtual, (
                $profissionalTO->getUf() == Constants::DESCRICAO_CAU_BR)
                ? Constants::PREFIXO_CAU_BR
                : $profissionalTO->getUf());

            $sheet->getStyle('C' . $linhaAtual)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('D' . $linhaAtual)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('E' . $linhaAtual)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('F' . $linhaAtual)->getAlignment()->setHorizontal('center');

            $this->adicionarBorda($sheet, 'A' . $linhaAtual, 'B' . $linhaAtual);
            $this->adicionarBorda($sheet, 'B' . $linhaAtual, 'C' . $linhaAtual);
            $this->adicionarBorda($sheet, 'C' . $linhaAtual, 'D' . $linhaAtual);
            $this->adicionarBorda($sheet, 'D' . $linhaAtual, 'E' . $linhaAtual);
            $this->adicionarBorda($sheet, 'E' . $linhaAtual, 'F' . $linhaAtual);

            $linhaAtual++;
        }
    }

    /**
     * Retorna a instancia de ProfissionalBO
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
}
