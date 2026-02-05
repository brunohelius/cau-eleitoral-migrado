<?php
/*
 * Declaracao.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Entidade de representação da 'Declaracao' no portal SICCAU.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DeclaracaoRepository")
 * @ORM\Table(schema="portal", name="TB_DECLARACAO")
 *
 * @OA\Schema(schema="Declaracao")
 *
 * @package App\Entities
 */
class Declaracao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="ID_DECLARACAO", type="integer")
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="portal.TB_DECLARACAO_ID_SEQ", initialValue=1, allocationSize=1)
     *
     * @OA\Property()
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Modulo")
     * @ORM\JoinColumn(name="ID_MODULO", referencedColumnName="ID_MODULO", nullable=false)
     * @var \App\Entities\Modulo
     */
    private $modulo;

    /**
     * @ORM\Column(name="NR_SEQ", type="integer", nullable=false)
     *
     * @OA\Property()
     * @var integer
     */
    private $sequencial;

    /**
     * @ORM\Column(name="NM_DECLARACAO", type="string", nullable=false)
     *
     * @OA\Property()
     * @var string
     */
    private $nome;

    /**
     * @ORM\Column(name="ST_ATIVO", type="integer", nullable=false)
     *
     * @OA\Property()
     * @var boolean
     */
    private $ativo;

    /**
     * @ORM\Column(name="DS_OBJETIVO", type="string", nullable=true)
     *
     * @OA\Property()
     * @var string
     */
    private $objetivo;

    /**
     * @ORM\Column(name="DS_TITULO", type="string", nullable=true)
     *
     * @OA\Property()
     * @var string
     */
    private $titulo;

    /**
     * @ORM\Column(name="DS_TEXTO_INICIAL", type="string", nullable=false)
     *
     * @OA\Property()
     * @var string
     */
    private $textoInicial;

    /**
     * @ORM\Column(name="ID_TP_RESPOSTA", type="integer", nullable=false)
     *
     * @OA\Property()
     * @var integer
     */
    private $tipoResposta;

    /**
     * @ORM\Column(name="ST_PERMITE_UPLOAD", type="integer", nullable=false)
     *
     * @OA\Property()
     * @var boolean
     */
    private $permiteUpload;

    /**
     * @ORM\Column(name="ST_UPLOAD_OBRIG", type="integer", nullable=false)
     *
     * @OA\Property()
     * @var boolean
     */
    private $uploadObrigatorio;

    /**
     * @ORM\Column(name="ST_PDF", type="integer", nullable=false)
     *
     * @OA\Property()
     * @var boolean
     */
    private $permitePDF;

    /**
     * @ORM\Column(name="ST_DOC_DOCX", type="integer", nullable=false)
     *
     * @OA\Property()
     * @var boolean
     */
    private $permiteDOC;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ItemDeclaracao", mappedBy="declaracao", cascade={"persist"}, orphanRemoval=true)
     *
     * @OA\Property()
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $itensDeclaracao;

    /**
     * Transient.
     *
     * @OA\Property()
     * @var mixed
     */
    private $itensExcluidos;

    /**
     * Fábrica de instância de 'Declaracao'.
     *
     * @param array $data
     * @return \App\Entities\Declaracao
     */
    public static function newInstance($data = null)
    {
        $declaracao = new Declaracao();

        if ($data != null) {
            $declaracao->setId(Utils::getValue('id', $data));
            $modulo = Modulo::newInstance(Utils::getValue('modulo', $data));
            $declaracao->setModulo($modulo);
            $declaracao->setSequencial(Utils::getValue('sequencial', $data));
            $declaracao->setNome(Utils::getValue('nome', $data));
            $declaracao->setAtivo(Utils::getValue('ativo', $data));
            if ($declaracao->getAtivo() == null) {
                $declaracao->setAtivo(true);
            }
            $declaracao->setObjetivo(Utils::getValue('objetivo', $data));
            $declaracao->setTitulo(Utils::getValue('titulo', $data));
            $declaracao->setTextoInicial(Utils::getValue('textoInicial', $data));
            $declaracao->setTipoResposta(Utils::getValue('tipoResposta', $data));
            $declaracao->setPermiteUpload(Utils::getValue('permiteUpload', $data));
            if ($declaracao->getPermiteUpload() == null) {
                $declaracao->setPermiteUpload(false);
            }
            $declaracao->setUploadObrigatorio(Utils::getValue('uploadObrigatorio', $data));
            if ($declaracao->getUploadObrigatorio() == null) {
                $declaracao->setUploadObrigatorio(false);
            }
            $declaracao->setPermitePDF(Utils::getValue('permitePDF', $data));
            if ($declaracao->getPermitePDF() == null) {
                $declaracao->setPermitePDF(false);
            }
            $declaracao->setPermiteDOC(Utils::getValue('permiteDOC', $data));
            if ($declaracao->getPermiteDOC() == null) {
                $declaracao->setPermiteDOC(false);
            }

            $itensDeclaracao = Utils::getValue('itensDeclaracao', $data);
            if (!empty($itensDeclaracao)) {
                foreach ($itensDeclaracao as $itemDeclaracao) {
                    $declaracao->adicionarItemDeclaracao(
                        ItemDeclaracao::newInstance($itemDeclaracao)
                    );
                }
            }

            $declaracao->setItensExcluidos(Utils::getValue('itensExcluidos', $data));
        }

        return $declaracao;
    }

    /**
     * Adiciona um 'ItemDeclaracao' à sua respectiva coleção.
     *
     * @param AtividadePrincipalCalendario $itemDeclaracao
     */
    private function adicionarItemDeclaracao(ItemDeclaracao $itemDeclaracao)
    {
        if ($this->getItensDeclaracao() == null) {
            $this->setItensDeclaracao(new ArrayCollection());
        }

        if (!empty($itemDeclaracao)) {
            $itemDeclaracao->setDeclaracao($this);
            $this->getItensDeclaracao()->add($itemDeclaracao);
        }
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
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param int $nome
     */
    public function setNome($nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return Modulo
     */
    public function getModulo()
    {
        return $this->modulo;
    }

    /**
     * @param Modulo $modulo
     */
    public function setModulo($modulo): void
    {
        $this->modulo = $modulo;
    }

    /**
     * @return int
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param int $ativo
     */
    public function setAtivo($ativo): void
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
    public function setObjetivo($objetivo): void
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
    public function setTitulo($titulo): void
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
    public function setTextoInicial($textoInicial): void
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
    public function setTipoResposta($tipoResposta): void
    {
        $this->tipoResposta = $tipoResposta;
    }

    /**
     * @return int
     */
    public function getPermiteUpload()
    {
        return $this->permiteUpload;
    }

    /**
     * @param int $permiteUpload
     */
    public function setPermiteUpload($permiteUpload): void
    {
        $this->permiteUpload = $permiteUpload;
    }

    /**
     * @return int
     */
    public function getUploadObrigatorio()
    {
        return $this->uploadObrigatorio;
    }

    /**
     * @param int $uploadObrigatorio
     */
    public function setUploadObrigatorio($uploadObrigatorio): void
    {
        $this->uploadObrigatorio = $uploadObrigatorio;
    }

    /**
     * @return int
     */
    public function getPermitePDF()
    {
        return $this->permitePDF;
    }

    /**
     * @param int $permitePDF
     */
    public function setPermitePDF($permitePDF): void
    {
        $this->permitePDF = $permitePDF;
    }

    /**
     * @return int
     */
    public function getPermiteDOC()
    {
        return $this->permiteDOC;
    }

    /**
     * @param int $permiteDOC
     */
    public function setPermiteDOC($permiteDOC): void
    {
        $this->permiteDOC = $permiteDOC;
    }

    /**
     * @return array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getItensDeclaracao()
    {
        return $this->itensDeclaracao;
    }

    /**
     * @param array|\Doctrine\Common\Collections\ArrayCollection $itensDeclaracao
     */
    public function setItensDeclaracao($itensDeclaracao): void
    {
        $this->itensDeclaracao = $itensDeclaracao;
    }

    /**
     * @return mixed
     */
    public function getItensExcluidos()
    {
        return $this->itensExcluidos;
    }

    /**
     * @param mixed $itensExcluidos
     */
    public function setItensExcluidos($itensExcluidos): void
    {
        $this->itensExcluidos = $itensExcluidos;
    }
}