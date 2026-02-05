<?php
/*
 * ArquivoService.php
 * Copyright (c) Ministério da Educação.
 * Este software é confidencial e propriedade do MEC.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do MEC.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Service;

use App\Business\DeclaracaoBO;
use App\Config\AppConfig;
use App\Config\Constants;
use App\Entities\ArquivoDecMembroComissao;
use App\Entities\Declaracao;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\To\ArquivoCalendarioTO;
use App\To\ArquivoDescricaoTO;
use App\To\ArquivoGenericoTO;
use App\To\ArquivoTO;
use App\To\ArquivoValidarTO;
use App\To\ChapaEleicaoExtratoFiltroTO;
use App\Util\Utils;
use App\Service\CorporativoService;
use finfo;
use stdClass;

/**
 * Classe de serviço responsável pela manipulação de 'arquivos'.
 *
 * @author Squadra Tecnologia S/A.
 */
class ArquivoService
{
    /**
     * Instancia de 'CorporativoService'
     */
    private $corporativoService;

    /**
     * @var DeclaracaoBO
     */
    private $declaracaoBO;

    /**
     * @param string $diretorio
     * @param string|null $nomeArquivo
     * @return string
     */
    public static function criarDiretorio($diretorio): void
    {
        if (!file_exists($diretorio)) {
            mkdir($diretorio, 0777, true);
        }
    }

    /**
     * Salva o arquivo no repositório de arquivos da aplicação.
     *
     * @param string $pasta
     * @param string $nome
     * @param mixed $arquivo
     */
    public function salvar($pasta, $nome, $arquivo)
    {
        $path = AppConfig::getRepositorio($pasta);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $arquivo->move($path, $nome);
    }

    public function salvarBase64ToArquivo($base64, $pasta, $nome, $extensao)
    {
        $data = explode(',', $base64);

        if(!empty($data) && count($data) > 1){
            $bin = base64_decode($data[1]);

            $size = getImageSizeFromString($bin);

            if (!(empty($size['mime']) || strpos($size['mime'], 'image/') !== 0)) {
                $ext = substr($size['mime'], 6);

                $pathArquivo = AppConfig::getRepositorio($pasta);

                $extArquivo = empty($extensao) ? $ext : $extensao;

                $filename = "{$nome}.{$extArquivo}";

                if (!is_dir($pathArquivo)) {
                    mkdir($pathArquivo, 0744);
                }

                $file = fopen($pathArquivo.$filename, "w+");

                fwrite($file, $bin);
                fclose($file);
            }
        }
    }

    /**
     * Copia o arquivo no repositório de arquivos da aplicação.
     *
     * @param string $sourcePath
     * @param string $destPath
     * @param string $sourceName
     * @param string $destName
     *
     * @throws NegocioException
     */
    public function copiar($sourcePath, $destPath, $sourceName, $destName)
    {
        $sourcePath = AppConfig::getRepositorio($sourcePath, $sourceName);
        $destPath = AppConfig::getRepositorio($destPath, $destName);

        if (!file_exists($sourcePath)) {
            throw new NegocioException(Message::MSG_ARQUIVO_NAO_ENCONTRADO);
        }

        copy($sourcePath, $destPath);
    }

    /**
     * Realiza uma cópia do arquivo já existente na aplicação para outro diretorio e nome de arquivo.
     *
     * @param string $path
     * @param string $sourceName
     * @param string $destName
     *
     * @throws NegocioException
     */
    public function copiarArquivoExistente($sourcePath, $destPath, $sourceName, $destName)
    {
        $destPathAbsolute = AppConfig::getRepositorio($destPath);
        $sourcePath = AppConfig::getRepositorio($sourcePath, $sourceName);
        $destPath = AppConfig::getRepositorio($destPath, $destName);

        if (!file_exists($sourcePath)) {
            throw new NegocioException('MSG_ARQUIVO_NAO_ENCONTRADO');
        }

        if (!file_exists($destPathAbsolute)) {
            mkdir($destPathAbsolute, 0777, true);
        }

        copy($sourcePath, $destPath);
    }


    /**
     * Realiza o(s) copia do(s) arquivo(s) do Calendário, quando houver ação de replicação.
     *
     * @param $arquivoOrigem
     * @param $arquivoDestino
     * @throws NegocioException
     */
    public function copiarArquivo($arquivoOrigem, $arquivoDestino)
    {
        $caminhoOrigem = $this->getCaminhoRepositorioDocumentos($arquivoOrigem->getCalendario()->getId());
        $caminhoDestino = $this->getCaminhoRepositorioDocumentos($arquivoDestino->getCalendario()->getId());

        $path = AppConfig::getRepositorio($caminhoDestino);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($arquivoOrigem != null && $arquivoDestino != null) {
            if (!empty($arquivoOrigem->getNomeFisico()) && !empty($arquivoDestino->getNomeFisico())) {
                $this->copiar($caminhoOrigem, $caminhoDestino, $arquivoOrigem->getNomeFisico(),
                    $arquivoDestino->getNomeFisico());
            }
        }
    }

    /**
     * Exclui o arquivo conforme o 'caminho' e o 'nome' informado.
     *
     * @param string $pasta
     * @param string $nome
     */
    public function excluir($pasta, $nome)
    {
        if (!empty($pasta) && !empty($nome)) {
            $path = AppConfig::getRepositorio($pasta, $nome);

            if (file_exists($path)) {
                unlink($path);
                $this->excluirPasta($pasta);
            }
        }
    }

    /**
     * Verifica se o diretório passado está vazio.
     *
     * @param string $pasta
     *
     * @return boolean
     */
    public function isDiretorioVazio($pasta)
    {
        $path = AppConfig::getRepositorio($pasta);

        $arquivos = scandir($path);

        return count($arquivos) <= 2;
    }

    /**
     * Verifica se o arquivo existe
     *
     * @param string $path
     *
     * @return boolean
     */
    public function fileExiste($path)
    {
        return file_exists($path);
    }

    /**
     * Retorna o arquivo conforme o 'caminho' e o 'nome' informado.
     *
     * @param string $pasta
     * @param string $nomeFisico
     *
     * @param null $nome
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivo($pasta, $nomeFisico, $nome = null)
    {
        $path = AppConfig::getRepositorio($pasta, $nomeFisico);

        if (!file_exists($path)) {
            throw new NegocioException('MSG_ARQUIVO_NAO_ENCONTRADO');
        }

        $arquivoTO = new ArquivoTO();
        $arquivoTO->name = $nomeFisico;

        if (!empty($nome)) {
            $arquivoTO->name = $nome;
        }

        $info = new finfo(FILEINFO_MIME_TYPE);
        $arquivoTO->type = $info->file($path);
        $arquivoTO->file = file_get_contents($path);

        return $arquivoTO;
    }

    /**
     * Retorna o arquivo conforme o 'caminho' e o 'nome' informado e deleta o arquivo indicado.
     *
     * @param string $pasta
     * @param string $nome
     *
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoTemporario($pasta, $nome)
    {
        $path = AppConfig::getRepositorio($pasta, $nome);

        if (!file_exists($path)) {
            throw new NegocioException('MSG_ARQUIVO_NAO_ENCONTRADO');
        }

        $arquivoTO = new ArquivoTO();
        $arquivoTO->name = $nome;

        $info = new finfo(FILEINFO_MIME_TYPE);
        $arquivoTO->type = $info->file($path);
        $arquivoTO->file = file_get_contents($path);

        unlink($path);
        return $arquivoTO;
    }

    /**
     * Verifica se o 'Arquivo' possui um 'Tamanho' válido conforme os parâmetros informados.
     *
     * @param integer $tamanho
     * @param integer $tamanhoLimiteArquivo
     * @param string $message
     *
     * @throws NegocioException
     */
    public function validarTamanhoArquivo($tamanho, $tamanhoLimiteArquivo, $message)
    {
        if (!$this->isTamanhoArquivoValido($tamanho, $tamanhoLimiteArquivo)) {
            throw new NegocioException($message);
        }
    }

    /**
     * Verifica se o 'Arquivo' possui um 'Tamanho' válido conforme os parâmetros informados.
     *
     * @param integer $tamanho
     * @param integer $tamanhoLimiteArquivo
     *
     * @return boolean
     */
    public function isTamanhoArquivoValido($tamanho, $tamanhoLimiteArquivo)
    {
        return !empty($tamanho) && $tamanho <= $tamanhoLimiteArquivo;
    }

    /**
     * Verifica se o 'Arquivo' possui uma das extensões informadas como parâmetros.
     *
     * @param string $nome
     * @param array $extensoesAnexo
     * @param boolean $isImportarCSV
     *
     * @throws NegocioException
     */
    public function validarExtensaoArquivo($nome, $extensoesAnexo, $isImportarCSV = false)
    {
        $extensao = Utils::getExtensaoArquivoPorNome($nome);

        if (!empty($extensoesAnexo) && !in_array($extensao, $extensoesAnexo)) {

            if ($isImportarCSV) {
                throw new NegocioException('MSG_ANEXO_EXTENSAO_CSV');
            } else {
                throw new NegocioException('MSG_ANEXO_EXTENSAO', $extensoesAnexo, true, true);
            }
        }
    }

    /**
     * Verifica se o 'Arquivo' possui a extensão PDF.
     *
     * @param string $nome
     *
     * @throws NegocioException
     */
    public function validarExtensaoArquivoPDF($nome)
    {
        $extensao = Utils::getExtensaoArquivoPorNome($nome);

        if (Constants::EXTENCAO_PDF != $extensao) {
            throw new NegocioException(Message::MSG_ANEXO_EXTENSAO_PDF);
        }
    }

    /**
     * Verifica se o 'Anexo' está em conformidade com os seguintes critérios:
     *
     * O anexo deve possuir um dos seguintes formatos: pdf.
     * O anexo não pode ser maior que 10Mb.
     *
     * @param string $nome
     * @param string $tamanho
     * @throws NegocioException
     */
    public function validarArquivoPDF($nome, $tamanho)
    {
        $this->validarExtensaoArquivoPDF($nome);
        $this->validarTamanhoArquivo(
            $tamanho,
            Constants::TAMANHO_LIMITE_ARQUIVO,
            'MSG_ANEXO_LIMITE_TAMANHO'
        );
    }

    /**
     * Retorna o nome do arquivo considerando o seguinte padrão:
     * <i>[prefixo]_[epoctime].[extensao]</i>
     * <b>ex:</b>
     * doc_comprobatorio_123456665.jpg
     *
     * @param string $nomeOriginal
     * @param string $prefixo
     *
     * @return string
     */
    public function getNomeArquivoFormatado($nomeOriginal, $prefixo)
    {
        $extensao = Utils::getExtensaoArquivoPorNome($nomeOriginal);

        return $prefixo . '_' . rand(1111111111, mt_getrandmax()) . '.' . $extensao;
    }

    /**
     * Retorna o arquivo conforme o 'caminho' e o 'nome' informado.
     *
     * @param $caminho
     * @param $nome
     *
     * @return ArquivoTO
     * @throws NegocioException
     */
    public function getArquivoCaminhoAbsoluto($caminho, $nome)
    {
        if (!file_exists($caminho)) {
            throw new NegocioException(Message::MSG_ARQUIVO_NAO_ENCONTRADO);
        }

        $arquivoTO = new ArquivoTO();
        $arquivoTO->name = $nome;

        $info = new finfo(FILEINFO_MIME_TYPE);
        $arquivoTO->type = $info->file($caminho);
        $arquivoTO->file = file_get_contents($caminho);

        return $arquivoTO;
    }

    /**
     * Verifica se o arquivo existe no storage conforme o caminho e o nome informado.
     *
     * @param $pasta
     * @param $nome
     * @return bool
     */
    public function hasArquivo($pasta, $nome)
    {
        $path = AppConfig::getRepositorio($pasta, $nome);
        return file_exists($path);
    }

    /**
     * Exclui o diretório informado caso esteja vazio.
     *
     * @param string $pasta
     */
    private function excluirPasta($pasta)
    {
        $path = AppConfig::getRepositorio($pasta);

        if ($this->isDiretorioVazio($pasta)) {
            rmdir($path);
        }
    }

    /**
     * Verifica se o 'Arquivo' está em conformidade com os seguintes critérios:
     * O arquivo deve possuir o formato: pdf.
     * O arquivo não pode ser maior que 10Mb.
     *
     * @param ArquivoCalendarioTO $arquivoTO
     *
     * @throws NegocioException
     */
    public function validarResolucaoPDF(ArquivoCalendarioTO $arquivoTO)
    {
        $this->validarExtensaoArquivoPDF($arquivoTO->getNome());
        $this->validarTamanhoArquivo(
            $arquivoTO->getTamanho(),
            Constants::TAMANHO_LIMITE_ARQUIVO,
            Message::MSG_ANEXO_RESOLUCAO_LIMITE_TAMANHO
        );
    }

    /**
     * Retorna meta informações de imagem, as mete informações são altura, largura e tipo.
     *
     * @param string $pathImg
     * @return array
     */
    public function getImagemDimensoes($pathImg)
    {
        list($width, $height, $type, $attr) = getimagesize($pathImg);
        $size = filesize($pathImg);
        return [$width, $height, $type, $size];
    }

    /**
     * Retorna a descrição do arquivo(nome, extensão, tamanho).
     *
     * @param string $caminho
     * @param string $nomeFisico
     * @param string $nome
     *
     * @return ArquivoDescricaoTO
     * @throws \Exception
     */
    public function getDescricaoArquivo($caminho, $nomeFisico, $nome)
    {
        $path = AppConfig::getRepositorio($caminho, $nomeFisico);
        $tamanho = "0";
        if(file_exists($path)) {
            $tamanho = filesize($path);
        }

        return ArquivoDescricaoTO::newInstance([
            'nome' => Utils::getNomeArquivoSemExtensao($nome),
            'extensao' => strtoupper(Utils::getExtensaoArquivoPorNome($nome)),
            'tamanho' => Utils::formatarTamanhoArquivo($tamanho)
        ]);
    }

    /**
     * Retorna o caminho relativo de acordo com um diretorio e uma pasta
     *
     * @param $diretorio
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorio($diretorio, $pasta)
    {
        return $diretorio . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioDocumentos($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_RESOLUCAO . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos das denuncias.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioDenuncia($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_DENUNCIA . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos dos julgamentos das denuncias.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoAdmissibilidade($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_JULGAMENTO_ADMISSIBILIDADE . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos dos julgamentos das denuncias.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioRecursoJulgamentoAdmissibilidade($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_RECURSO_JULGAMENTO_ADMISSIBILIDADE . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos dos julgamentos de recurso admissibilidade.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoRecursoAdmissibilidade($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_JULGAMENTO_RECURSO_ADMISSIBILIDADE . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos dos recursos da denuncia.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioRecursoDenuncia($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_RECURSO_CONTRARRAZAO_DENUNCIA . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos das denuncias Defesa.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioDenunciaDefesa($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_DENUNCIA_DEFESA . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos das denuncias Provas.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioDenunciaProvas($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_DENUNCIA_PROVAS . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos das denuncias Audiencia Instrução.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioDenunciaAudienciaInstrucao($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_DENUNCIA_AUDIENCIA_INSTRUCAO . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos das declarações.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioEleicao($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_ELEICAO . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioEmail($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_EMAIL . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo da imagem de Cabeçalho de E-mail.
     *
     * @param string $pasta
     * @return string
     */
    public function getCaminhoDefaultCabecalho()
    {
        return resource_path(Constants::PATH_DEFAULT_CABECALHO);
    }

    /**
     * Retorna o caminho relativo da imagem de Rodapé de E-mail.
     *
     * @param string $pasta
     * @return string
     */
    public function getCaminhoDefaultRodape()
    {
        return resource_path(Constants::PATH_DEFAULT_RODAPE);
    }

    /**
     * Retorna o caminho relativo da imagem de Usuário Masculino.
     *
     * @param string $pasta
     * @return string
     */
    public function getCaminhoDefaultUsuarioMasculino()
    {
        return resource_path(Constants::PATH_M_USER);
    }


    /**
     * Retorna o caminho relativo do repositório de documentos.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioPublicacaoComissaoMembro($pasta)
    {
        $diretorio = Constants::PATH_STORAGE_DOCUMENTO_PUBLICACAO_COMISSAO_MEMBRO . DIRECTORY_SEPARATOR;
        $diretorio .= $pasta . DIRECTORY_SEPARATOR;
        return $diretorio;
    }

    /**
     * Retorna o caminho relativo do repositório de Cabeçalho de E-mail.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioCabecalhoEmail($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_EMAIL . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Sintese de Currículo.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioSinteseCurriculo($pasta)
    {
        return Constants::PATH_STORAGE_SINTESE_CURRICULO . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Arquivos de Resposta Declaração Chapa.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioRespDeclaracaoChapa($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_RESP_DECLARACAO_CHAPA . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Pedidos Substituições Chapa.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioPedidoSubstituicaoChapa($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_PEDIDO_SUBSTITUICAO_CHAPA . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Pedidos Impugnacao.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioPedidoImpugnacao($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_PEDIDO_IMPUGNACAO . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Pedidos Impugnacao.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioDefesaImpugnacao($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_DEFESA_IMPUGNACAO . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Julgamento Substituições.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoSubstituicao($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_JULGAMENTO_SUBSTITUICAO . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Julgamento Impugnações.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoImpugnacao($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_JULGAMENTO_IMPUGNACAO . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Recurso do Julgamento de Impugnações.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioRecursoImpugnacao($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_RECURSO_JULGAMENTO_IMPUG . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Recurso do Julgamento Substituições.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioRecursoSubstituicao($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_RECURSO_JULGAMENTO_SUBST . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Recurso do Julgamento Substituições.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioImpugnacaoResultado($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_IMPUGN_RESULTADO . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Alegação da Impugnação de Resultado.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioAlegacaoImpugnacaoResultado($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_ALEGACAO_IMPUGNACAO_RESULTADO . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Julgamento do Recurso de Substituições.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoRecursoSubstituicao($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_JULGAMENTO_RECURSO_SUBST . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Julgamento do Recurso de Impugnacão.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoRecursoImpugnacao($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_JULGAMENTO_RECURSO_IMPUGN . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * @param $pasta
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoDenuncia($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_JULGAMENTO_DENUNCIA . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de contrarrazao do Recurso de Impugnacão.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioContrarrazaoRecursoImpugnacao($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_CONTRARRAZAO_RECURSO_IMPUGN . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de contrarrazao do Recurso de Impugnacão.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoFinal($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_JULGAMENTO_FINAL . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de contrarrazao do Recurso de Impugnacão.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoAlegacaoImpugResultado($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de contrarrazao do Recurso de Impugnacão.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoSegundaInstanciaRecurso($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de contrarrazao do Substituicao de Impugnacão.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoSegundaInstanciaSubstituicao($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Substituição no Julgamento Final.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioSubstituicaoJulgamentoFinal($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_SUBSTITUICOES_JULGAMENTOS_FINAIS . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de recurso do julgamento final.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioRecursoJulgamentoFinal($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_RECURSO_JULGAMENTO_FINAL . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de recurso do julgamento final.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioRecursoSegundoJulgamentoSubstituicao($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_RECURSO_SEGUNDO_JULGAMENTO . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Julgamento Recurso Segunda Instancia.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoRecursoSegundaInstancia($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Julgamento Recurso Segunda Instancia.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgamentoRecursoDaSubstituicao($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_JULGAMENTO_RECURSO_DA_SUBST_FINAL . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Julgamento Recurso Segunda Instancia.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioContrarrazaoImpugnacaoResultado($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_CONTRARRAZAO_IMPUGNACAO_RESULTADO . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Julgamento Recurso Segunda Instancia Impugnação de Resultadp.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioJulgRecursoImpugResultado($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_JULG_RECURSO_IMPUG_RESULT . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de Pedidos Substituições Chapa.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioBandeirasFiliais()
    {
        return Constants::PATH_STORAGE_BANDEIRAS_FILIAIS;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos das declarações.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoExtratoChapaJson($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_EXTRATO_CHAPA . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo do repositório de Alegações Finais.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioAlegacaoFinal($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_ALEGACAO_FINAL_ENCAMINHAMENTO . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Retorna o caminho relativo do repositório de documentos das denuncias admitidas.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioDenunciaAdmitida($pasta)
    {
        return Constants::PATH_STORAGE_ARQUIVO_DENUNCIA_ADMITIDA . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR;
    }

    /**
     * Retorna o caminho relativo do repositório de Parecer Final.
     *
     * @param string $pasta
     *
     * @return string
     */
    public function getCaminhoRepositorioParecerFinal($pasta)
    {
        return Constants::PATH_STORAGE_ARQ_PARECER_FINAL_ENCAMINHAMENTO . DIRECTORY_SEPARATOR . $pasta;
    }

    /**
     * Recupera e retorna o arquivo Json do extrato de chapas por UF.
     *
     * @param ChapaEleicaoExtratoFiltroTO $chapaEleicaoExtratoFiltroTO
     * @return string
     * @throws NegocioException
     */
    public function getArquivoExtratoChapaJson(ChapaEleicaoExtratoFiltroTO $chapaEleicaoExtratoFiltroTO)
    {

        $pathArquivo = $this->getCaminhoExtratoChapaJson(
            $chapaEleicaoExtratoFiltroTO->getIdCalendario() . "-" . $chapaEleicaoExtratoFiltroTO->getIdCauUf()
        );
        $arquivo = $this->getArquivo($pathArquivo, Constants::NAME_ARQUIVO_EXTRATO_CHAPA);

        return $arquivo;
    }

    /**
     * Cria as pastas caso não existam e Retorna o Caminho do Path do Arquivo.
     *
     * @param $jsonChapaExtrato
     * @param ChapaEleicaoExtratoFiltroTO $chapaEleicaoExtratoFiltroTO
     * @return string
     */
    public function salvaArquivoExtratoChapaJson(
        $jsonChapaExtrato,
        ChapaEleicaoExtratoFiltroTO $chapaEleicaoExtratoFiltroTO
    ) {

        $patchExtrato = getenv('SICCAU_STORAGE_PATH') . DIRECTORY_SEPARATOR . Constants::PATH_STORAGE_ARQUIVO_EXTRATO_CHAPA;
        $pathArquivo = getenv('SICCAU_STORAGE_PATH') . DIRECTORY_SEPARATOR . $this->getCaminhoExtratoChapaJson(
                $chapaEleicaoExtratoFiltroTO->getIdCalendario() . "-" . $chapaEleicaoExtratoFiltroTO->getIdCauUf()
            );
        $nomeArquivo = Constants::NAME_ARQUIVO_EXTRATO_CHAPA;

        if (!is_dir($patchExtrato)) {
            mkdir($patchExtrato, 0744);
        }

        if (!is_dir($pathArquivo)) {
            mkdir($pathArquivo, 0744);
        }

        $path = $pathArquivo . $nomeArquivo;

        $fp = fopen($path, 'w+');
        fwrite($fp, $jsonChapaExtrato);
        fclose($fp);
    }

    /**
     * Validação de arquivos de imagem de cabeçalho e rodapé.
     * @param $arquivo
     * @throws NegocioException
     */
    public function validarImagemCabecalhoEmail($arquivo)
    {
        if (!empty($arquivo)) {
            $pathImg = $arquivo->path();
            list($largura, $altura, $extensao, $tamanho) = $this->getImagemDimensoes($pathImg);
            if ($largura > Constants::LARGURA_MAXIMA_IMAGE_CABECALHO) {
                throw new NegocioException(Message::MGS_LARGURA_MAXIMA_PERMITIDA,
                    [Constants::LARGURA_MAXIMA_IMAGE_CABECALHO], true);
            }
            if ($altura > Constants::ALTURA_MAXIMA_IMAGEM_CABECALHO) {
                throw new NegocioException(Message::MSG_ALTURA_MAXIMA_PERMITIDA,
                    [Constants::ALTURA_MAXIMA_IMAGEM_CABECALHO], true);
            }
            if (!in_array($arquivo->extension(), Constants::$extensoesImagem)) {
                throw new NegocioException(Message::MSG_FORMATO_ARQUIVO_INVALIDO_SOMENTE);
            }
            $this->validarTamanhoArquivo($tamanho, Constants::TAMANHO_LIMITE_ARQUIVO,
                'MSG_MAXIMO_TAMANHO_PERMITIDO_ARQUIVOS_CABECALHO_RODAPE');
        }
    }


    /**
     * Verifica se o 'Arquivo' está em conformidade com os seguintes critérios:
     * O arquivo deve possuir o formato: pdf ou doc ou docx
     * O arquivo não pode ser maior que 40Mb.
     *
     * @param ArquivoDecMembroComissao $arquivo
     * @param $id
     * @throws NegocioException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validarArquivoViaDeclaracao(ArquivoDecMembroComissao $arquivo, $id)
    {
        $declaracao = $this->getDeclaracaoBO()->getDeclaracao($id);

        $extensoesAceitas = array();
        $msgErro = "";

        if (!empty($declaracao->getPermitePDF()) && !empty($declaracao->getPermiteDOC())) {
            array_push($extensoesAceitas, Constants::EXTENCAO_PDF);
            array_push($extensoesAceitas, Constants::EXTENCAO_DOC);
            array_push($extensoesAceitas, Constants::EXTENCAO_DOCX);
            $msgErro = Message::MSG_ANEXO_DECLARACAO_DOC_E_PDF;

        } else {
            if (!empty($declaracao->getPermitePDF())) {
                array_push($extensoesAceitas, Constants::EXTENCAO_PDF);
                $msgErro = Message::MSG_ANEXO_EXTENSAO_PDF_DECLARACAO;

            } else {
                if (!empty($declaracao->getPermiteDOC())) {
                    array_push($extensoesAceitas, Constants::EXTENCAO_DOC);
                    array_push($extensoesAceitas, Constants::EXTENCAO_DOCX);
                    $msgErro = Message::MSG_ANEXO_EXTENSAO_DOC_DECLARACAO;
                }
            }
        }

        $extensao = Utils::getExtensaoArquivoPorNome($arquivo->getNome());

        if (!in_array($extensao, $extensoesAceitas)) {
            throw new NegocioException($msgErro);
        }

        $this->validarTamanhoArquivo(
            $arquivo->getTamanho(),
            Constants::TAMANHO_LIMITE_ARQUIVO_DECLARACAO,
            Message::MSG_ANEXO_RESOLUCAO_LIMITE_TAMANHO_DECLARACAO
        );
    }

    /**
     * Verifica se o 'Arquivo' está em conformidade com os seguintes critérios:
     * O arquivo deve possuir o formato: pdf ou doc ou docx
     * O arquivo não pode ser maior que 10Mb.
     * @param $arquivoTO
     * @throws NegocioException
     */
    public function validarArquivoEleicao($arquivoTO)
    {
        $nomeArquivo = Utils::getValue("nomeArquivo", $arquivoTO);
        $tamanhoArquivo = Utils::getValue("tamanhoArquivo", $arquivoTO);

        if (!empty($nomeArquivo) && !empty($tamanhoArquivo)) {
            $extensoesAnexo = ['pdf', 'doc', 'docx'];
            $extensao = Utils::getExtensaoArquivoPorNome($nomeArquivo);

            if (!empty($extensoesAnexo) && !in_array($extensao, $extensoesAnexo)) {
                throw new NegocioException(Message::MSG_ANEXO_CALENDARIO_EXTENSAO, [], true);
            }

            $this->validarTamanhoArquivo($tamanhoArquivo, Constants::TAMANHO_LIMITE_ARQUIVO,
                Message::MSG_DOCUMENTO_ELEICAO_LIMITE_TAMANHO);

        } else {
            throw new NegocioException(Message::APLICACAO_ENCONTROU_ERRO_INESPERADO, [], true);
        }

    }


    /**
     * Lazy instance de CorporativoService
     */
    private function getCorporativoService()
    {
        if (empty($this->corporativoService)) {
            $this->corporativoService = new CorporativoService();
        }

        return $this->corporativoService;
    }

    /**
     * @param $arquivo
     *
     * @throws NegocioException
     */
    public function validarFotoSinteseCurriculo($arquivo)
    {
        if (!empty($arquivo)) {
            $pathImg = $arquivo->path();
            list($largura, $altura, $extensao, $tamanho) = $this->getImagemDimensoes($pathImg);

            if (!in_array($arquivo->extension(), Constants::$extensoesFotoSinteseCurriculo)) {
                throw new NegocioException(Message::MSG_FORMATO_ARQUIVO_INVALIDO_SINTESE_CURRICULO);
            }

            $this->validarTamanhoArquivo($tamanho, Constants::TAMANHO_LIMITE_ARQUIVO_2MB,
                Message::MSG_TAMANHO_MAXIMO_PERMITIDO_FOTO_SINTESE_CURRICULO);
        }
    }

    /**
     * Verifica se o 'Arquivo' está em conformidade com os seguintes critérios:
     * O arquivo deve possuir o formato: pdf.
     * O arquivo não pode ser maior que 10Mb.
     *
     * @param $arquivo
     *
     * @throws NegocioException
     */
    public function validarDocsComprobatoriosSinteseCurriculo($arquivo)
    {
        $this->validarExtensaoArquivoPDF($arquivo->getClientOriginalName());
        $this->validarTamanhoArquivo(
            $arquivo->getSize(),
            Constants::TAMANHO_LIMITE_ARQUIVO,
            Message::MSG_ANEXO_RESOLUCAO_LIMITE_TAMANHO
        );
    }

    /**
     * Verifica se o 'Arquivo' está em conformidade com os seguintes critérios:
     * O arquivo deve possuir o formato: pdf ou doc/docx
     * O arquivo não pode ser maior que 10Mb.
     *
     * @param Declaracao $declaracao
     * @param ArquivoValidarTO $arquivoValidarTO
     * @throws NegocioException
     */
    public function validarArquivoDeclaracao(Declaracao $declaracao, ArquivoValidarTO $arquivoValidarTO)
    {
        $extensao = Utils::getExtensaoArquivoPorNome($arquivoValidarTO->getNomeArquivo());


        $msgErro = Message::MSG_ANEXO_EXTENSAO_PDF_DOC_DECLARACAO;

        $extensoesAceitas = [];

        if ($declaracao->getPermiteDOC()) {
            $msgErro = !$declaracao->getPermitePDF() ? Message::MSG_ANEXO_EXTENSAO_DOC_DECLARACAO : $msgErro;
            $extensoesAceitas[] = Constants::EXTENCAO_DOC;
            $extensoesAceitas[] = Constants::EXTENCAO_DOCX;
        }

        if ($declaracao->getPermitePDF()) {
            $msgErro = !$declaracao->getPermiteDOC() ? Message::MSG_ANEXO_EXTENSAO_PDF_DECLARACAO : $msgErro;
            $extensoesAceitas[] = Constants::EXTENCAO_PDF;
        }

        if (!in_array($extensao, $extensoesAceitas)) {
            throw new NegocioException($msgErro);
        }

        $this->validarTamanhoArquivo(
            $arquivoValidarTO->getTamanhoArquivo(),
            $arquivoValidarTO->getTamanhoPermitido(),
            $arquivoValidarTO->getCodigoMsgTamanhoArquivo()
        );
    }

    /**
     * Valida previamente arquivo se possui tamnaho e extensão válida.
     *
     * @param $arquivoTO
     * @throws NegocioException
     */
    public function validarArquivoDenuncia($arquivoTO)
    {
        $this->validarTamanhoArquivo($arquivoTO->getTamanho(), Constants::TAMANHO_LIMITE_ARQUIVO_DENUNCIA_25MB,
                                    Message::MSG_ARQUIVO_INVALIDO);

        $extensoesAnexo = ['pdf', 'zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'mp4', 'avi', 'wmv', 'mp3', 'wav', 'jpg', 'jpeg', 'png'];

        $extensao = Utils::getExtensaoArquivoPorNome($arquivoTO->getNome());

        if (!empty($extensoesAnexo) && !in_array($extensao, $extensoesAnexo)) {
            throw new NegocioException(Message::MSG_ARQUIVO_INVALIDO,[], true);
        }
    }

    /**
     * Realiza o(s) copia do(s) arquivo(s) da Denuncia, quando houver ação de replicação.
     *
     * @param $arquivoOrigem
     * @param $arquivoDestino
     * @throws NegocioException
     */
    public function copiarArquivoDenuncia($arquivoOrigem, $arquivoDestino)
    {
        $caminhoOrigem = $this->getCaminhoRepositorioDenuncia($arquivoOrigem->getDenuncia()->getId());
        $caminhoDestino = $this->getCaminhoRepositorioDenuncia($arquivoDestino->getDenuncia()->getId());

        $path = AppConfig::getRepositorio($caminhoDestino);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($arquivoOrigem != null && $arquivoDestino != null) {
            if (!empty($arquivoOrigem->getNomeFisico()) && !empty($arquivoDestino->getNomeFisico())) {
                $this->copiar($caminhoOrigem, $caminhoDestino, $arquivoOrigem->getNomeFisico(), $arquivoDestino->getNomeFisico());
            }
        }
    }
    /**
     * Verifica se o 'Arquivo' está em conformidade com os parâmetros
     *
     * @param stdClass $arquivoTO
     *
     * @throws NegocioException
     */
    public function validarArquivo(stdClass $arquivoTO)
    {
        if (empty($arquivoTO->nome) || empty($arquivoTO->tamanho) || empty($arquivoTO->tipoValidacao)) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (!empty(Constants::$configValidacaoArquivo[$arquivoTO->tipoValidacao])) {
            $config = Constants::$configValidacaoArquivo[$arquivoTO->tipoValidacao];

            $extensao = Utils::getExtensaoArquivoPorNome($arquivoTO->nome);

            if (!in_array($extensao, $config['extensoes_aceitas'])) {
                throw new NegocioException($config['msg_extensoes_aceitas']);
            }

            $this->validarTamanhoArquivo($arquivoTO->tamanho, $config['tamanho_limite'], $config['msg_tamanho_limite']);
        }
    }

    /**
     * Verifica se o 'Arquivo' está em conformidade com os parâmetros
     *
     * @param ArquivoGenericoTO $arquivoGenericoTO
     * @param int $tipoValidacao
     * @throws NegocioException
     */
    public function validarArquivoGenrico(ArquivoGenericoTO $arquivoGenericoTO, $tipoValidacao)
    {
        if (
            empty($arquivoGenericoTO->getNome())
            || empty($arquivoGenericoTO->getTamanho())
        ) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO);
        }

        if (!empty(Constants::$configValidacaoArquivo[$tipoValidacao])) {
            $config = Constants::$configValidacaoArquivo[$tipoValidacao];

            $extensao = Utils::getExtensaoArquivoPorNome($arquivoGenericoTO->getNome());

            if (!in_array($extensao, $config['extensoes_aceitas'])) {
                throw new NegocioException($config['msg_extensoes_aceitas']);
            }

            $this->validarTamanhoArquivo(
                $arquivoGenericoTO->getTamanho(),
                $config['tamanho_limite'],
                $config['msg_tamanho_limite']
            );
        }
    }

    /**
     * Retorna uma nova instância de 'DeclaracaoBO'.
     *
     * @return DeclaracaoBO|mixed
     */
    private function getDeclaracaoBO()
    {
        if (empty($this->declaracaoBO)) {
            $this->declaracaoBO = app()->make(DeclaracaoBO::class);
        }

        return $this->declaracaoBO;
    }

}
