<?php
/*
 * Utils.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Util;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Config\Constants;
use App\Models\DiplomaTermo;
use InvalidArgumentException;
use MessageFormatter;

/**
 * Classe utilitária de apoio geral á codificação da aplicação,
 * essa classe contém todos os métodos que sejam comuns à várias outras classes da aplicação.
 *
 * @package App\Util
 * @author Squadra Tecnologia
 */
class Utils
{

    const TIMEZONE_PADRAO = "America/Sao_Paulo";

    const FORMATO_CPF = "###.###.###-##";

    const FORMATO_CEP = "#####-###";

    private static $numeroMembroChapa = 1;

    /**
     * Construtor privado para garantir o Singleton.
     */
    private function __construct()
    {
    }

    /**
     * Retorna a mensagem ($message) formatada considerando os parâmetros ($params).
     * Caso o atributo '$concatParams' seja verdadeiro os parâmetros ($params), serão
     * concatenados com virgula ','.
     *
     * @param string $message
     * @param mixed $params
     * @param boolean $concatParams
     *
     * @return string
     */
    public static function getMessageFormated($message, $params = null, $concatParams = false)
    {
        if ($params == null) {
            return $message;
        }

        $value = null;
        $formatter = new MessageFormatter('pt_br', $message);

        if (!is_array($params)) {
            $value = $params;
        } elseif ($concatParams) {
            $value = implode(", ", $params);
        }

        if ($value != null) {
            $params = array();
            $params[] = $value;
        }

        return $formatter->format($params);
    }

    /**
     * Retorna o valor existente no array ($data) conforme o índice ($index).
     * Obs: Caso o índice não exista o retorno será 'nulo'.
     *
     * @param mixed $index
     * @param array $array
     * @param mixed $default
     *
     * @return mixed
     */
    public static function getValue($index, $array, $default = null)
    {
        return Arr::get($array, $index, $default);
    }

    /**
     * Retorna os números conforme o valor informado.
     *
     * @param string $value
     *
     * @return mixed
     */
    public static function getOnlyNumbers($value)
    {
        $numbers = null;

        if (!empty($value)) {
            $numbers = preg_replace('/[^0-9]/', '', $value);
            $numbers = strlen($numbers) == 0 ? null : $numbers;
        }

        return $numbers;
    }

    /**
     * Retorna a instância de DateTime com a data corrente.
     *
     * @return DateTime
     */
    public static function getData()
    {
        $data = new DateTime();
        $data->setTimezone(new DateTimeZone(static::TIMEZONE_PADRAO));

        return $data;
    }

    /**
     * Retorna o ano da 'Data' informada.
     *
     * @param DateTime|null $value
     *
     * @return string|null
     */
    public static function getAnoData(DateTime $value = null)
    {
        $ano = null;
        if ($value != null) {
            $ano = $value->format('Y');
        }
        return $ano;
    }

    /**
     * Retorna a instância de DateTime considerando hora zero.
     * Obs: Caso o parâmetro informado seja null, o retorno será a data atual.
     *
     * @param DateTime $value
     *
     * @return DateTime
     */
    public static function getDataHoraZero(DateTime $value = null)
    {
        if ($value == null) {
            $data = static::getData();
        } else {
            $data = clone $value;
        }
        date_time_set($data, 0, 0, 0);

        return $data;
    }

    /**
     * Retorna a instância de DateTime considerando hora Final 23:59:59.
     * Obs: Caso o parâmetro informado seja null, o retorno será a data atual.
     *
     * @param DateTime $value
     *
     * @return DateTime
     * @throws Exception
     */
    public static function getDataHoraFinal(DateTime $value = null)
    {
        if ($value == null) {
            $data = static::getData();
        } else {
            $data = clone $value;
        }
        date_time_set($data, 23, 59, 59);

        return $data;
    }

    /**
     * Retorna a instância de DateTime considerando o 'valor' informado.
     *
     * @param string $value
     * @param string $format
     * @param string $timezone
     *
     * @return DateTime|null
     * @throws InvalidArgumentException
     */
    public static function getDataToString($value, $format = "Y-m-d\TH:i:s+", $timezone = self::TIMEZONE_PADRAO)
    {
        $data = null;

        if (!empty($value)) {
            $data = DateTime::createFromFormat($format, $value, new DateTimeZone($timezone));

            if (!$data) {
                $msg = 'Não foi possível converter o valor "' . $value;
                $msg .= '", para o formato esperado: "' . $format . '".';

                throw new InvalidArgumentException($msg);
            }

            $data->setTimezone(new DateTimeZone(static::TIMEZONE_PADRAO));
        }

        return $data;
    }

    /**
     * Adiciona uma quantidade de anos a data informada.
     *
     * @param DateTime $data
     * @param integer $quantidadeAnos
     * @return null
     * @throws Exception
     */
    public static function adicionarAnosToData(DateTime $data, $quantidadeAnos)
    {
        $intervalo = 'P' . $quantidadeAnos . 'Y';
        return self::adicionarIntervaloToData($data, $intervalo);
    }

    /**
     * Adiciona uma quantidade de dias a data informada.
     *
     * @param DateTime $data
     * @param integer $qtdDiasAdicionar
     * @return DateTime|null
     * @throws Exception
     */
    public static function adicionarDiasData(DateTime $data, $qtdDiasAdicionar)
    {
        $intervalo = "P" . $qtdDiasAdicionar . "D";
        return self::adicionarIntervaloToData($data, $intervalo);
    }

    /**
     * Remove uma quantidade de dias a data informada.
     *
     * @param DateTime $data
     * @param integer $qtdDiasAdicionar
     * @return null
     * @throws Exception
     */
    public static function removerDiasData(DateTime $data, $qtdDiasRemover)
    {
        $intervalo = "P" . $qtdDiasRemover . "D";
        return self::removerIntervaloToData($data, $intervalo);
    }

    /**
     * Adiciona uma quantidade de dias úteis a data informada.
     *
     * @param DateTime $data
     * @param integer $qtdDiasAdicionar
     * @param array $feriados
     * @return null
     * @throws Exception
     */
    public static function adicionarDiasUteisData(DateTime $data, $qtdDiasAdicionar, array $feriados)
    {
        if( !empty($feriados) && is_array($feriados[Constants::FERIADOS_NACIONAIS])) {
            $feriados = self::getFeriadosOnlyStringDate($feriados);
        }
        $dia = 1;

        //Guarda o horário antes de zerar o horário da $data
        $horario = [$data->format('H'), $data->format('i'), $data->format('s')];

        $data = Utils::getDataHoraZero($data);

        while ($dia <= $qtdDiasAdicionar) {
            $intervalo = "P1D";
            $dataIncrementada = self::adicionarIntervaloToData($data, $intervalo);

            $numDia = $dataIncrementada->format('w');
            $dataComparativa = $dataIncrementada->format('d/m/Y');
            if (!in_array($dataComparativa, $feriados) && $numDia != 0 && $numDia != 6) {
                $dia++;
            }

            $data = $dataIncrementada;
        }

        //adiciona as horas guardada anteriormente antes de retornar a data incrementada
        $data->setTime($horario[0], $horario[1], $horario[2]);

        return $data;
    }

    /**
     * Recebe uma lista de feriados no padrão do serviço do calendario api e retorna apenas as datas em string
     * @param $feriados
     * @return array
     */
    public static function getFeriadosOnlyStringDate($feriados) {
        $arrFeriados = null;
        if(!empty($feriados)) {
            foreach($feriados[Constants::FERIADOS_NACIONAIS] as $feriado) {
                $arrFeriados[] = $feriado['date'];
            }
            foreach($feriados[Constants::FERIADOS_FACULTATIVOS] as $feriado) {
                $arrFeriados[] = $feriado['date'];
            }
        }
        return $arrFeriados;
    }

    /**
     * Adiciona uma quantidade de dias úteis a data informada.
     *
     * @param DateTime $data
     * @param $qtdDiasRemover
     * @param array $feriados
     * @return null
     * @throws Exception
     */
    public static function removeDiasUteisData(DateTime $data, $qtdDiasRemover, array $feriados)
    {
        $feriados = self::getFeriadosOnlyStringDate($feriados);
        $dia = 1;

        //Guarda o horário antes de zerar o horário da $data
        $horario = [$data->format('H'), $data->format('i'), $data->format('s')];

        $data = Utils::getDataHoraZero($data);

        while ($dia <= $qtdDiasRemover) {
            $intervalo = "P1D";
            $dataIncrementada = self::removerIntervaloToData($data, $intervalo);

            $numDia = $dataIncrementada->format('w');
            $dataDecrementada = $dataIncrementada->format('d/m/Y');
            if (!in_array($dataDecrementada, $feriados) && $numDia != 0 && $numDia != 6) {
                $dia++;
            }

            $data = $dataIncrementada;
        }

        //adiciona as horas guardada anteriormente antes de retornar a data incrementada
        $data->setTime($horario[0], $horario[1], $horario[2]);

        return $data;
    }

    /**
     * Adiciona uma quantidade de dias a data informada.
     *
     * @param DateTime $data
     * @param integer $qtdDiasSubtrair
     * @return null
     * @throws Exception
     */
    public static function subtrairDiasData(DateTime $data, $qtdDiasSubtrair)
    {
        $intervalo = "P" . $qtdDiasSubtrair . "D";

        return self::removerIntervaloToData($data, $intervalo);
    }

    /**
     * Retorna o DateTime informado em formato 'string'.
     *
     * @param DateTime $value
     * @param string $format
     *
     * @return string|null
     */
    public static function getStringFromDate(DateTime $value, $format = "Y-m-d\TH:i:s+")
    {
        $data = null;

        if (!empty($value)) {
            $data = $value->format($format);
        }

        return $data;
    }

    /**
     * Retorna a string informada em formato 'array'.
     *
     * @param string $value
     * @param string $pattern
     *
     * @return array|null
     */
    public static function getArrayFromString(string $value, $pattern = ',')
    {
        $array = [];

        if (!empty($value)) {
            $array = explode($pattern, $value);
        }

        return $array;
    }

    /**
     * Retorna o 'cpf' informado formatado.
     *
     * @param string $cpf
     *
     * @return string
     */
    public static function getCpfFormatado($cpf)
    {
        $cpfFormatado = '';

        if (!empty($cpf)) {
            $cpfFormatado = Utils::mask($cpf, static::FORMATO_CPF);
        }

        return $cpfFormatado;
    }

    /**
     * Retorna o 'cpf' informado formatado.
     *
     * @param string $cpf
     *
     * @return string
     */
    public static function getRegistroNacionalFormatado($registroNacional)
    {
        $registroNacionalFormatado = null;
        if(!empty($registroNacional)) {
            $registroNacional = ltrim($registroNacional, '0');

            $tamanho = strlen($registroNacional);

            $inicioRegistro = substr($registroNacional, 0, $tamanho - 1);
            $fimRegistro = substr($registroNacional, $tamanho - 1);

            $registroNacionalFormatado =  "{$inicioRegistro}-{$fimRegistro}";
        }
        return $registroNacionalFormatado;
    }

    /**
     * Verifica se a string começa com o prefixo especificado.
     *
     * @param $value
     * @param $prefix
     *
     * @return bool
     */
    public static function startsWith($value, $prefix)
    {
        return Str::startsWith($value, $prefix);
    }

    /**
     * Recupera a extensão do arquivo considerando o nome do arquivo informado.
     * Caso o nome do arquivo seja vazio o retorno será nulo.
     * Caso o nome não contenha a extensão o retorno será nulo.
     *
     * @param string $nome
     *
     * @return mixed|null|string
     */
    public static function getExtensaoArquivoPorNome($nome)
    {
        $extensao = null;

        if (!empty($nome)) {
            $valores = explode('.', $nome);

            if (count($valores) > 1) {
                $extensao = end($valores);
                $extensao = strtolower($extensao);
            }
        }

        return $extensao;
    }

    /**
     * Retorno o valor boolean conforme os parâmetros informados.
     * Obs: Caso o índice não exista o retorno será 'false'.
     *
     * @param $index
     * @param $data
     *
     * @return mixed
     */
    public static function getBooleanValue($index, $data)
    {
        $value = Utils::getValue($index, $data, false);

        if ($value !== false) {
            $value = json_decode($value);
        }

        return $value;
    }

    /**
     * Remove apenas as tags dadas de uma determinada String HTML
     */
    public static function removerHtmlTags($html, $tags, $apagarConteudoTag = false)
    {
        foreach ($tags as $tag) {
            $regex = '#<\s*' . $tag . '[^>]*>.*?<\s*/\s*' . $tag . '>#msi';

            if (!$apagarConteudoTag) {
                $regex = '#<\s*' . $tag . '[^>]*>#msi';
                $regex2 = '#<\s*/\s*' . $tag . '>#msi';
                $html = preg_replace($regex2, '', $html);
            }
            $html = preg_replace($regex, '', $html);
        }
        return $html;
    }

    /**
     * Formata o valor considerando o formato informado.
     *
     * @param string $value
     * @param string $pattern
     *
     * @return string
     */
    private static function mask($value, $pattern)
    {
        $count = 0;
        $formatted = '';

        for ($index = 0; $index <= strlen($pattern) - 1; $index++) {

            if ($pattern[$index] == '#') {
                $formatted .= isset($value[$count]) ? $value[$count] : '';
                $count = $count + 1;
            } else {
                $formatted .= isset($pattern[$index]) ? $pattern[$index] : '';
            }
        }

        return $formatted;
    }

    /**
     * Adiciona um intervalor a uma data
     *
     * @param DateTime $data
     * @param string $intervalo
     * @return DateTime|null
     * @throws Exception
     */
    private static function adicionarIntervaloToData(DateTime $data, string $intervalo)
    {
        $dataResult = null;
        if (!empty($data)) {
            $dataResult = clone $data;
            $dataResult->add(new DateInterval($intervalo));
        }

        return $dataResult;
    }

    /**
     * Adiciona um intervalor a uma data
     *
     * @param DateTime $data
     * @param string $intervalo
     * @return null
     * @throws Exception
     */
    private static function removerIntervaloToData(DateTime $data, string $intervalo)
    {
        $dataResult = null;
        if (!empty($data)) {
            $dataResult = clone $data;
            $dataResult->sub(new DateInterval($intervalo));
        }

        return $dataResult;
    }

    /**
     * Retorna o valor ou o valor NULO (caso não exista o valor);
     *
     * @param $objeto
     * @param $variavel
     * @param null $default
     *
     * @return mixed|null
     */
    public static function getValorAtributo($objeto, $variavel, $default = null)
    {
        return !empty($objeto) && !empty($objeto->$variavel) ? $objeto->$variavel : $default;
    }

    /**
     * Recebe uma lista de cau uf e atribui os nomes (prefixo e descricao) para o array recebido
     *
     * @param array $filiaisCauUf
     * @param array $lista
     *
     * @return array|null
     */
    public static function organizeIdCauUfParaLista($filiaisCauUf, $lista)
    {
        $listaNova = array();
        if (!empty($filiaisCauUf)) {
            $itemCAUBR = [];
            $itemIES = [];
            foreach ($lista as $i => $item) {
                foreach ($filiaisCauUf as $filialCauUf) {
                    if ($filialCauUf->id == $item['idCauUf']) {
                        if ($filialCauUf->id == Constants::COMISSAO_MEMBRO_CAU_BR_ID) {
                            $itemCAUBR['idCauUf'] = $filialCauUf->id;
                            $itemCAUBR['prefixo'] = Constants::PREFIXO_CONSELHO_ELEITORAL_NACIONAL;
                            $itemCAUBR['descricao'] = Constants::PREFIXO_CONSELHO_ELEITORAL_NACIONAL;
                        } else if ($filialCauUf->id == Constants::IES_ID) {
                            $itemIES['idCauUf'] = $filialCauUf->id;
                            $itemIES['prefixo'] = $filialCauUf->prefixo;
                            $itemIES['descricao'] = $filialCauUf->descricao;
                        } else {
                            $itemTemp['idCauUf'] = $filialCauUf->id;
                            $itemTemp['prefixo'] = $filialCauUf->prefixo;
                            $itemTemp['descricao'] = $filialCauUf->descricao;

                            $listaNova[] = $itemTemp;
                        }
                    }
                }
            }
            uasort($listaNova, function ($a, $b) {
                return strnatcmp($a['prefixo'], $b['prefixo']);
            });

            if (!empty($itemCAUBR)) {
                $listaNova[] = $itemCAUBR;
            }
            if (!empty($itemIES)) {
                $listaNova[] = $itemIES;
            }
        }

        return array_values($listaNova);
    }

    /**
     * Substitui todos os acentos de uma determinada string e o substitui
     *
     * @param string $string
     * @return string
     */
    public static function tirarAcentos($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
    }

    /**
     * Método responsável por executar um JOB e tratar exceções
     *
     * @param $instanciaJOB
     * @return mixed|null
     */
    public static function executarJOB($instanciaJOB)
    {
        try {
            dispatch($instanciaJOB);
        } catch (Exception $e) {
        }
    }

    /**
     * Retorna o primeiro e ultimo nome do nome completo informado.
     *
     * @param $nomeCompleto
     * @return string
     */
    public static function getPrimeiraEUltimaPalavra($nomeCompleto)
    {
        $texto = !empty($nomeCompleto) ? trim($nomeCompleto) : "";

        $palavras = explode(" ", $texto);
        $primeraPalavra = "";
        $ultimaPalavra = "";

        if (!empty($palavras)) {
            $primeraPalavra = array_shift($palavras);
        }

        if (!empty($palavras)) {
            $ultimaPalavra = array_pop($palavras);
        }

        return $primeraPalavra . " " . $ultimaPalavra;
    }

    /**
     * valida a data informada.
     *
     * @param $date
     * @param string $format
     * @return bool
     */
    public static function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }


    /**
     * Retorna o campo ofuscado.
     *
     * @param string $campo
     * @return string|null
     */
    public static function ofuscarCampo(string $campo)
    {
        $campo = str_shuffle($campo);
        return substr(crypt($campo, '$2a$08$Cf1f11ePArKlBJomM0F6aJ$'), 0, strlen($campo));
    }

    /**
     * Recupera o nome do arquivo sem a extensão considerando o nome do arquivo informado.
     * Caso o nome do arquivo seja vazio o retorno será nulo.
     *
     * @param string $nome
     *
     * @return mixed|null|string
     */
    public static function getNomeArquivoSemExtensao($nome)
    {
        $nomeArquivo = null;

        if (!empty($nome)) {
            $valores = explode('.', $nome);

            if (count($valores) > 1) {
                $nomeArquivo = current($valores);
            }
        }

        return $nomeArquivo;
    }

    /**
     * Retorna o tamanho do arquivo em Bytes, KB, MB, GB.
     * Caso o nome do arquivo seja vazio o retorno será nulo.
     *
     * @param int $tamanho
     *
     * @return mixed|null|string
     */
    public static function formatarTamanhoArquivo($tamanho)
    {
        $nomeTamanho = [" Bytes", " KB", " MB", " GB"];
        $potencia = pow(1024, ($i = floor(log($tamanho, 1024))));

        return $tamanho ? round($tamanho/$potencia, 2) . $nomeTamanho[$i] : '0 Bytes';
    }

    public static function getCepFormatado(string $cep) {
        $cepFormatado = '';

        if (!empty($cep)) {
            $cepFormatado = Utils::mask($cep, static::FORMATO_CEP);
        }

        return $cepFormatado;
    }

    public static function getNumeroMembroChapa(){
        return static::$numeroMembroChapa++;
    }

    /**
     * Gera codigo autenticador do Diploma/termo
     *
     * @param int $id
     */
    public static function gerarCodigoAutenticidade($idDiplomaTermo, $tipo)
    {
        $coAutenticidade = null;
        $diplomaEleitoral = DiplomaTermo::where('id', $idDiplomaTermo)
            ->with('conselheiro.profissional')
            ->first();
        
        $coAutenticidade = ($diplomaEleitoral['conselheiro']['ies'] ? 'IES' : $diplomaEleitoral['conselheiro']['profissional']['uf_eleitoral']) . substr($diplomaEleitoral['conselheiro']['ano_eleicao'][2], -2) . substr($diplomaEleitoral['conselheiro']['profissional']['registro_nacional'], -3) . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 1) . rand(0, 9);
        
        return str_replace(' ', '', $coAutenticidade);
    }

    /**
     * Retorna array com data da resolução
     *
     * @param int $id
     */
    public static function getDataDesmembrada($data)
    {
        $data = explode(" ", $data);
        return explode("-", $data[0]);
    }

     /**
     * Retorna mês referente a número
     *
     * @param int $id
     */
    public static function monthNumberToStringEncode($numero)
    {
		$numero = intval ( $numero ); // Retiro o zero
		$month = array (1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro' );

		return $month [$numero];
	}
}
