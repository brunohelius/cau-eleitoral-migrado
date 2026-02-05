<?php


namespace App\To;


use App\Util\Utils;

/**
 * Classe de transferência associada a 'Declaração'
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class DeclaracaoTO
{

    /**
     * id da declarac
     * @var integer
     */
    private $id;

    /**
     * Sequencia da declaração
     * @var integer
     */
    private $sequencial;

    /**
     * Nome da declaração
     * @var string
     */
    private $nome;

    /**
     * Status da declaração
     * @var boolean
     */
    private $ativo;

    /**
     * Objetivo da declaracao
     * @var string
     */
    private $objetivo;

    /**
     * Título da declaração
     * @var string
     */
    private $titulo;

    /**
     * Texto inicial da declaração
     * @var string
     */
    private $textoInicial;

    /**
     * Tipo resposta declaração
     * @var integer
     */
    private $tipoResposta;

    /**
     * Permite upload
     * @var boolean
     */
    private $permiteUpload;

    /**
     * Upload obrigatório
     * @var boolean
     */
    private $uploadObrigatorio;

    /**
     * Permite PDF
     * @var boolean
     */
    private $permitePDF;

    /**
     * Permite DOC
     * @var boolean
     */
    private $permiteDOC;

    /**
     * Array de ItemDeclaracaoTO
     * @var array
     */
    private $itensDeclaracao;

    public static function newInstance($data = null)
    {
        $declaracaoTO = new DeclaracaoTO();

        if ($data != null) {
            $declaracaoTO->setId(Utils::getValue("id", $data));
            $declaracaoTO->setNome(Utils::getValue("nome", $data));
            $declaracaoTO->setAtivo(Utils::getValue("ativo", $data));
            $declaracaoTO->setTitulo(Utils::getValue("titulo", $data));
            $declaracaoTO->setObjetivo(Utils::getValue("objetivo", $data));
            $declaracaoTO->setSequencial(Utils::getValue("sequencial", $data));
            $declaracaoTO->setPermiteDOC(Utils::getValue("permiteDOC", $data));
            $declaracaoTO->setPermitePDF(Utils::getValue("permitePDF", $data));
            $declaracaoTO->setTextoInicial(Utils::getValue("textoInicial", $data));
            $declaracaoTO->setTipoResposta(Utils::getValue("tipoResposta", $data));
            $declaracaoTO->setPermiteUpload(Utils::getValue("permiteUpload", $data));
            $declaracaoTO->setUploadObrigatorio(Utils::getValue("uploadObrigatorio", $data));

            $itensResposta = Utils::getValue('itensResposta', $data, []);
            $itensDeclaracao = Utils::getValue('itensDeclaracao', $data, $itensResposta);

            if (!empty($itensDeclaracao)) {
                $declaracaoTO->setItensDeclaracao(array_map(function ($data) {
                    return ItemDeclaracaoTO::newInstance($data);
                }, $itensDeclaracao));
            }
        }

        return $declaracaoTO;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(?int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getSequencial()
    {
        return $this->sequencial;
    }

    /**
     * @param int $sequencial
     */
    public function setSequencial(?int $sequencial)
    {
        $this->sequencial = $sequencial;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome(?string $nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return bool
     */
    public function isAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param bool $ativo
     */
    public function setAtivo(?bool $ativo)
    {
        $this->ativo = $ativo;
    }

    /**
     * @return string
     */
    public function getObjetivo()
    {
        return $this->objetivo;
    }

    /**
     * @param string $objetivo
     */
    public function setObjetivo(?string $objetivo)
    {
        $this->objetivo = $objetivo;
    }

    /**
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * @param string $titulo
     */
    public function setTitulo(?string $titulo)
    {
        $this->titulo = $titulo;
    }

    /**
     * @return string
     */
    public function getTextoInicial()
    {
        return $this->textoInicial;
    }

    /**
     * @param string $textoInicial
     */
    public function setTextoInicial(?string $textoInicial)
    {
        $this->textoInicial = $textoInicial;
    }

    /**
     * @return int
     */
    public function getTipoResposta()
    {
        return $this->tipoResposta;
    }

    /**
     * @param int $tipoResposta
     */
    public function setTipoResposta(?int $tipoResposta)
    {
        $this->tipoResposta = $tipoResposta;
    }

    /**
     * @return bool
     */
    public function isPermiteUpload()
    {
        return $this->permiteUpload;
    }

    /**
     * @param bool $permiteUpload
     */
    public function setPermiteUpload(?bool $permiteUpload)
    {
        $this->permiteUpload = $permiteUpload;
    }

    /**
     * @return bool
     */
    public function isUploadObrigatorio()
    {
        return $this->uploadObrigatorio;
    }

    /**
     * @param bool $uploadObrigatorio
     */
    public function setUploadObrigatorio(?bool $uploadObrigatorio)
    {
        $this->uploadObrigatorio = $uploadObrigatorio;
    }

    /**
     * @return bool
     */
    public function isPermitePDF()
    {
        return $this->permitePDF;
    }

    /**
     * @param bool $permitePDF
     */
    public function setPermitePDF(?bool $permitePDF)
    {
        $this->permitePDF = $permitePDF;
    }

    /**
     * @return bool
     */
    public function isPermiteDOC()
    {
        return $this->permiteDOC;
    }

    /**
     * @param bool $permiteDOC
     */
    public function setPermiteDOC(?bool $permiteDOC)
    {
        $this->permiteDOC = $permiteDOC;
    }

    /**
     * @return array
     */
    public function getItensDeclaracao()
    {
        return $this->itensDeclaracao;
    }

    /**
     * @param array $itensDeclaracao
     */
    public function setItensDeclaracao(?array $itensDeclaracao): void
    {
        $this->itensDeclaracao = $itensDeclaracao;
    }
}
