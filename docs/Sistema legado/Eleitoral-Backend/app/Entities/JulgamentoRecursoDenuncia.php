<?php
/*
 * JulgamentoRecursoDenuncia.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'JulgamentoRecursoDenuncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoRecursoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_JULGAMENTO_RECURSO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_JULGAMENTO_RECURSO_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_julgamento_recurso_denuncia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_JULGAMENTO_RECURSO_DENUNCIA", type="string", length=2000, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="QT_DIAS_SUSPENSAO_PROPAGANDA", type="integer", length=999, nullable=true)
     *
     * @var integer
     */
    private $quantidadeDiasSuspensaoPropaganda;

    /**
     * @ORM\Column(name="MULTA", type="boolean", nullable=true)
     *
     * @var boolean
     */
    private $multa;

    /**
     * @ORM\Column(name="VL_PERCENTUAL_MULTA", type="integer", length=999, nullable=true)
     *
     * @var integer
     */
    private $valorPercentualMulta;

    /**
     * @ORM\Column(name="DT_JULGAMENTO_RECURSO_DENUNCIA", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $data;

    /**
     * @ORM\Column(name="DS_JUSTIFICATIVA", type="string", length=2000, nullable=true)
     *
     * @var string
     */
    private $justificativa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Usuario")
     * @ORM\JoinColumn(name="ID_USUARIO", referencedColumnName="id", nullable=false)
     *
     * @var Usuario
     */
    private $usuario;

    /**
     * @ORM\Column(name="SANCAO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $sancao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoJulgamento")
     * @ORM\JoinColumn(name="ID_TIPO_JULGAMENTO_DENUNCIADO", referencedColumnName="ID_TIPO_JULGAMENTO", nullable=false)
     *
     * @var TipoJulgamento
     */
    private $tipoJulgamentoDenunciado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoJulgamento")
     * @ORM\JoinColumn(name="ID_TIPO_JULGAMENTO_DENUNCIANTE", referencedColumnName="ID_TIPO_JULGAMENTO", nullable=false)
     *
     * @var TipoJulgamento
     */
    private $tipoJulgamentoDenunciante;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\RecursoDenuncia")
     * @ORM\JoinColumn(name="ID_RECURSO_DENUNCIADO", referencedColumnName="ID_RECURSO_CONTRARRAZAO_DENUNCIA", nullable=true)
     *
     * @var RecursoDenuncia
     */
    private $recursoDenunciado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\RecursoDenuncia")
     * @ORM\JoinColumn(name="ID_RECURSO_DENUNCIANTE", referencedColumnName="ID_RECURSO_CONTRARRAZAO_DENUNCIA", nullable=true)
     *
     * @var RecursoDenuncia
     */
    private $recursoDenunciante;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoSentencaJulgamento")
     * @ORM\JoinColumn(name="ID_TIPO_SENTENCA_JULGAMENTO", referencedColumnName="ID_TIPO_SENTENCA_JULGAMENTO", nullable=true)
     *
     * @var TipoSentencaJulgamento|null
     */
    private $tipoSentencaJulgamento;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoJulgamentoRecursoDenuncia", mappedBy="julgamentoRecursoDenuncia")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $arquivosJulgamentoRecursoDenuncia;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\RetificacaoJulgamentoRecursoDenuncia", mappedBy="julgamentoRetificado")
     *
     * @var RetificacaoJulgamentoRecursoDenuncia|null
     */
    private $retificacaoJulgamento;

    /**
     * Transient
     *
     * @var mixed
     */
    private $retificacao;

    /**
     * Fábrica de instância de 'JulgamentoSegundaInstanciaDenuncia'.
     *
     * @param null $data
     *
     * @return self
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data !== null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setData(Utils::getValue('data', $data));
            $instance->setMulta(Utils::getBooleanValue('multa', $data));
            $instance->setDescricao(Utils::getValue('descricao', $data));
            $instance->setSancao(Utils::getBooleanValue('sancao', $data));
            $instance->setJustificativa(Utils::getValue('justificativa', $data));

            if (isset($data['retificacao'])) {
                $instance->setRetificacao(Utils::getBooleanValue('retificacao', $data));
            }

            $usuario = Utils::getValue('usuario', $data);
            if (!empty($usuario)) {
                $instance->setUsuario(Usuario::newInstance($usuario));
            }

            $vlPercentualMulta = Utils::getValue('valorPercentualMulta', $data);
            if (!empty($vlPercentualMulta)) {
                $instance->setValorPercentualMulta($vlPercentualMulta);
            }

            $recursoDenunciado = Utils::getValue('recursoDenunciado', $data);
            if (!empty($recursoDenunciado)) {
                $instance->setRecursoDenunciado(RecursoDenuncia::newInstance($recursoDenunciado));
            }

            $recursoDenunciante = Utils::getValue('recursoDenunciante', $data);
            if (!empty($recursoDenunciante)) {
                $instance->setRecursoDenunciante(RecursoDenuncia::newInstance($recursoDenunciante));
            }

            $tipoJulgamentoDenunciado = Utils::getValue('tipoJulgamentoDenunciado', $data);
            if (!empty($tipoJulgamentoDenunciado)) {
                $instance->setTipoJulgamentoDenunciado(TipoJulgamento::newInstance($tipoJulgamentoDenunciado));
            }

            $tipoJulgamentoDenunciante = Utils::getValue('tipoJulgamentoDenunciante', $data);
            if (!empty($tipoJulgamentoDenunciante)) {
                $instance->setTipoJulgamentoDenunciante(TipoJulgamento::newInstance($tipoJulgamentoDenunciante));
            }

            $qtDiasSuspensaoPropaganda = Utils::getValue('quantidadeDiasSuspensaoPropaganda', $data);
            if (!empty($qtDiasSuspensaoPropaganda)) {
                $instance->setQuantidadeDiasSuspensaoPropaganda($qtDiasSuspensaoPropaganda);
            }

            $tipoSentencaJulgamento = Utils::getValue('tipoSentencaJulgamento', $data);
            if (!empty($tipoSentencaJulgamento)) {
                $instance->setTipoSentencaJulgamento(TipoSentencaJulgamento::newInstance($tipoSentencaJulgamento));
            }

            $retificacaoJulgamentoRecursoDenuncia = Utils::getValue('retificacaoJulgamento', $data);
            if (!empty($retificacaoJulgamentoRecursoDenuncia)) {
                $instance->setRetificacaoJulgamento(
                    RetificacaoJulgamentoRecursoDenuncia::newInstance($retificacaoJulgamentoRecursoDenuncia)
                );
            }

            $arquivos = Utils::getValue('arquivosJulgamentoRecursoDenuncia', $data);
            if (!empty($arquivos)) {
                foreach ($arquivos as $arquivo) {
                    $instance->adicionarArquivoJulgamentoRecursoDenuncia(
                        ArquivoJulgamentoRecursoDenuncia::newInstance($arquivo)
                    );
                }
            }
        }

        return $instance;
    }

    /**
     * Adiciona um Arquivo no array de Arquivos
     *
     * @param ArquivoJulgamentoRecursoDenuncia $arquivoJulgamentoRecursoDenuncia
     */
    private function adicionarArquivoJulgamentoRecursoDenuncia(ArquivoJulgamentoRecursoDenuncia $arquivoJulgamentoRecursoDenuncia)
    {
        if ($this->getArquivosJulgamentoRecursoDenuncia() === null) {
            $this->setArquivosJulgamentoRecursoDenuncia(new ArrayCollection());
        }

        if ($arquivoJulgamentoRecursoDenuncia !== null) {
            $arquivoJulgamentoRecursoDenuncia->setJulgamentoRecursoDenuncia($this);
            $this->getArquivosJulgamentoRecursoDenuncia()->add($arquivoJulgamentoRecursoDenuncia);
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
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \DateTime|string $data
     *
     * @throws \Exception
     */
    public function setData($data): void
    {
        if (is_string($data)) {
            $data = new \DateTime($data);
        }
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getJustificativa()
    {
        return $this->justificativa;
    }

    /**
     * @param string $justificativa
     */
    public function setJustificativa($justificativa): void
    {
        $this->justificativa = $justificativa;
    }

    /**
     * @return int
     */
    public function getQuantidadeDiasSuspensaoPropaganda()
    {
        return $this->quantidadeDiasSuspensaoPropaganda;
    }

    /**
     * @param int $quantidadeDiasSuspensaoPropaganda
     */
    public function setQuantidadeDiasSuspensaoPropaganda($quantidadeDiasSuspensaoPropaganda): void
    {
        $this->quantidadeDiasSuspensaoPropaganda = $quantidadeDiasSuspensaoPropaganda;
    }

    /**
     * @return bool
     */
    public function isMulta()
    {
        return $this->multa ?? false;
    }

    /**
     * @param bool $multa
     */
    public function setMulta($multa): void
    {
        $this->multa = $multa;
    }

    /**
     * @return int
     */
    public function getValorPercentualMulta()
    {
        return $this->valorPercentualMulta;
    }

    /**
     * @param int $valorPercentualMulta
     */
    public function setValorPercentualMulta($valorPercentualMulta): void
    {
        $this->valorPercentualMulta = $valorPercentualMulta;
    }

    /**
     * @return bool
     */
    public function isSancao()
    {
        return $this->sancao ?? false;
    }

    /**
     * @param bool $sancao
     */
    public function setSancao($sancao): void
    {
        $this->sancao = $sancao;
    }

    /**
     * @return Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param Usuario $usuario
     */
    public function setUsuario($usuario): void
    {
        $this->usuario = $usuario;
    }

    /**
     * @return TipoJulgamento
     */
    public function getTipoJulgamentoDenunciado()
    {
        return $this->tipoJulgamentoDenunciado;
    }

    /**
     * @param TipoJulgamento $tipoJulgamentoDenunciado
     */
    public function setTipoJulgamentoDenunciado($tipoJulgamentoDenunciado): void
    {
        $this->tipoJulgamentoDenunciado = $tipoJulgamentoDenunciado;
    }

    /**
     * @return TipoJulgamento
     */
    public function getTipoJulgamentoDenunciante()
    {
        return $this->tipoJulgamentoDenunciante;
    }

    /**
     * @param TipoJulgamento $tipoJulgamentoDenunciante
     */
    public function setTipoJulgamentoDenunciante($tipoJulgamentoDenunciante): void
    {
        $this->tipoJulgamentoDenunciante = $tipoJulgamentoDenunciante;
    }

    /**
     * @return RecursoDenuncia
     */
    public function getRecursoDenunciado()
    {
        return $this->recursoDenunciado;
    }

    /**
     * @param RecursoDenuncia $recursoDenunciado
     */
    public function setRecursoDenunciado($recursoDenunciado): void
    {
        $this->recursoDenunciado = $recursoDenunciado;
    }

    /**
     * @return RecursoDenuncia|null
     */
    public function getRecursoDenunciante()
    {
        return $this->recursoDenunciante;
    }

    /**
     * @param RecursoDenuncia $recursoDenunciante
     */
    public function setRecursoDenunciante($recursoDenunciante): void
    {
        $this->recursoDenunciante = $recursoDenunciante;
    }

    /**
     * @return TipoSentencaJulgamento
     */
    public function getTipoSentencaJulgamento()
    {
        return $this->tipoSentencaJulgamento;
    }

    /**
     * @param TipoSentencaJulgamento $tipoSentencaJulgamento
     */
    public function setTipoSentencaJulgamento($tipoSentencaJulgamento): void
    {
        $this->tipoSentencaJulgamento = $tipoSentencaJulgamento;
    }

    /**
     * @return array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getArquivosJulgamentoRecursoDenuncia()
    {
        return $this->arquivosJulgamentoRecursoDenuncia;
    }

    /**
     * @param array|\Doctrine\Common\Collections\ArrayCollection $arquivosJulgamentoRecursoDenuncia
     */
    public function setArquivosJulgamentoRecursoDenuncia($arquivosJulgamentoRecursoDenuncia): void
    {
        $this->arquivosJulgamentoRecursoDenuncia = $arquivosJulgamentoRecursoDenuncia;
    }

    /**
     * @return RetificacaoJulgamentoRecursoDenuncia|null
     */
    public function getRetificacaoJulgamento()
    {
        return $this->retificacaoJulgamento;
    }

    /**
     * @param $retificacaoJulgamento
     */
    public function setRetificacaoJulgamento($retificacaoJulgamento)
    {
        $this->retificacaoJulgamento = $retificacaoJulgamento;
    }

    /**
     * @return mixed
     */
    public function isRetificacao()
    {
        return $this->retificacao;
    }

    /**
     * @param bool $retificacao
     */
    public function setRetificacao($retificacao): void
    {
        $this->retificacao = $retificacao;
    }

    /**
     * Método para retirar os binários de arquivos
     */
    public function removerFiles()
    {
        if (!empty($this->arquivos)) {
            foreach ($this->arquivos as $arquivo) {
                $arquivo->setArquivo(null);
            }
        }
    }
}
