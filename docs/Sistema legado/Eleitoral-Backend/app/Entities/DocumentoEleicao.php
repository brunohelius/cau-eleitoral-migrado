<?php
/*
 * Calendario.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * Entidade de representação de 'DocumentoEleicao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DocumentoEleicaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DOC_ELEICAO")
 *
 * @OA\Schema(schema="DocumentoEleicao")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DocumentoEleicao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DOC_ELEICAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_DOC_ELEICAO_ID_SEQ", initialValue=1, allocationSize=1)
     *
     * @OA\Property()
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Eleicao")
     * @ORM\JoinColumn(name="ID_ELEICAO", referencedColumnName="ID_ELEICAO", nullable=false)
     *
     * @OA\Property()
     * @var \App\Entities\Eleicao
     */
    private $eleicao;

    /**
     * @ORM\Column(name="ST_CORPORATIVO", type="boolean", nullable=false)
     *
     * @OA\Property()
     * @var boolean
     */
    private $corporativo;

    /**
     * @ORM\Column(name="ST_PROFISSIONAL", type="boolean", nullable=false)
     *
     * @OA\Property()
     * @var boolean
     */
    private $profissional;

    /**
     * @ORM\Column(name="ST_PUBLICO", type="boolean", nullable=false)
     *
     * @OA\Property()
     * @var boolean
     */
    private $publico;

    /**
     * @ORM\Column(name="NM_ARQUIVO", type="string", nullable=false)
     *
     * @OA\Property()
     * @var string
     */
    private $nomeArquivo;

    /**
     * @ORM\Column(name="NM_FIS_ARQUIVO", type="string", nullable=false)
     *
     * @OA\Property()
     * @var string
     */
    private $nomeArquivoFisico;

    /**
     * @ORM\Column(name="NR_SEQ", type="integer", nullable=false)
     *
     * @OA\Property()
     * @var integer
     */
    private $sequencial;

    /**
     * @ORM\Column(name="ID_USUARIO", type="integer", nullable=false)
     *
     * @OA\Property()
     * @var integer
     */
    private $idUsuario;

    /**
     * @ORM\Column(name="NM_USUARIO", type="string", nullable=false)
     *
     * @OA\Property()
     * @var string
     */
    private $nomeUsuario;

    /**
     * Transient.
     *
     * @OA\Property()
     * @var mixed
     */
    private $arquivo;

    /**
     * Transient.
     *
     * @OA\Property()
     * @var mixed
     */
    private $tamanho;

    /**
     * @ORM\Column(name="DT_PUBLICACAO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $dataPublicacao;

    /**
     * Fábrica de instância de 'DocumentoEleicao'.
     *
     * @param array $data
     * @return \App\Entities\DocumentoEleicao
     */
    public static function newInstance($data = null)
    {
        $documentoEleicao = new DocumentoEleicao();

        if ($data != null) {
            $documentoEleicao->setId(Utils::getValue('id', $data));
            $eleicao = Eleicao::newInstance(Utils::getValue('eleicao', $data));
            $documentoEleicao->setEleicao($eleicao);
            $documentoEleicao->setCorporativo(Utils::getValue('corporativo', $data));
            $documentoEleicao->setProfissional(Utils::getValue('profissional', $data));
            $documentoEleicao->setPublico(Utils::getValue('publico', $data));
            $documentoEleicao->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $documentoEleicao->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));
            $documentoEleicao->setSequencial(Utils::getValue('sequencial', $data));
            $documentoEleicao->setIdUsuario(Utils::getValue('idUsuario', $data));
            $documentoEleicao->setNomeUsuario(Utils::getValue('nomeUsuario', $data));
            $documentoEleicao->setArquivo(Utils::getValue('arquivo', $data));
            $documentoEleicao->setTamanho(Utils::getValue('tamanho', $data));
            $documentoEleicao->setDataPublicacao(Utils::getValue('dataPublicacao', $data));
        }

        return $documentoEleicao;
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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return Eleicao
     */
    public function getEleicao()
    {
        return $this->eleicao;
    }

    /**
     * @param Eleicao $eleicao
     */
    public function setEleicao($eleicao): void
    {
        $this->eleicao = $eleicao;
    }

    /**
     * @return bool
     */
    public function isCorporativo()
    {
        return $this->corporativo;
    }

    /**
     * @param bool $corporativo
     */
    public function setCorporativo($corporativo): void
    {
        $this->corporativo = $corporativo;
    }

    /**
     * @return bool
     */
    public function isProfissional()
    {
        return $this->profissional;
    }

    /**
     * @param bool $profissional
     */
    public function setProfissional($profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @return bool
     */
    public function isPublico()
    {
        return $this->publico;
    }

    /**
     * @param bool $publico
     */
    public function setPublico($publico): void
    {
        $this->publico = $publico;
    }

    /**
     * @return string
     */
    public function getNomeArquivo()
    {
        return $this->nomeArquivo;
    }

    /**
     * @param string $nomeArquivo
     */
    public function setNomeArquivo($nomeArquivo): void
    {
        $this->nomeArquivo = $nomeArquivo;
    }

    /**
     * @return string
     */
    public function getNomeArquivoFisico()
    {
        return $this->nomeArquivoFisico;
    }

    /**
     * @param string $nomeArquivoFisico
     */
    public function setNomeArquivoFisico($nomeArquivoFisico): void
    {
        $this->nomeArquivoFisico = $nomeArquivoFisico;
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
    public function setSequencial($sequencial): void
    {
        $this->sequencial = $sequencial;
    }

    /**
     * @return int
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * @param int $idUsuario
     */
    public function setIdUsuario($idUsuario): void
    {
        $this->idUsuario = $idUsuario;
    }

    /**
     * @return string
     */
    public function getNomeUsuario()
    {
        return $this->nomeUsuario;
    }

    /**
     * @param string $nomeUsuario
     */
    public function setNomeUsuario($nomeUsuario): void
    {
        $this->nomeUsuario = $nomeUsuario;
    }

    /**
     * @return mixed
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * @param mixed $arquivo
     */
    public function setArquivo($arquivo): void
    {
        $this->arquivo = $arquivo;
    }

    /**
     * @return mixed
     */
    public function getTamanho()
    {
        return $this->tamanho;
    }

    /**
     * @param mixed $tamanho
     */
    public function setTamanho($tamanho): void
    {
        $this->tamanho = $tamanho;
    }

    /**
     * @return mixed
     */
    public function getDataPublicacao()
    {
        return $this->dataPublicacao;
    }

    /**
     * @param mixed $dataPublicacao
     */
    public function setDataPublicacao($dataPublicacao)
    {
        $this->dataPublicacao = $dataPublicacao;
    }
}