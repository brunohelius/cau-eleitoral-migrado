<?php
/*
 * JulgamentoDenuncia.php
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
 * Entidade de representação de 'JulgamentoDenuncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\JulgamentoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_JULGAMENTO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_JULGAMENTO_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_julgamento_denuncia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_JULGAMENTO_DENUNCIA", type="string", length=2000, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="NIVEL", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $nivel;

    /**
     * @ORM\Column(name="QT_DIAS_SUSPENSAO_PROPAGANDA", type="integer", length=999, nullable=true)
     *
     * @var integer
     */
    private $quantidadeDiasSuspensaoPropaganda;

    /**
     * @ORM\Column(name="MULTA", type="boolean", nullable=false)
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
     * @ORM\Column(name="DT_JULGAMENTO_DENUNCIA", type="datetime", nullable=false)
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
     * @ORM\ManyToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     *
     * @var Denuncia
     */
    private $denuncia;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoJulgamento")
     * @ORM\JoinColumn(name="ID_TIPO_JULGAMENTO", referencedColumnName="ID_TIPO_JULGAMENTO", nullable=false)
     *
     * @var TipoJulgamento
     */
    private $tipoJulgamento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoSentencaJulgamento")
     * @ORM\JoinColumn(name="ID_TIPO_SENTENCA_JULGAMENTO", referencedColumnName="ID_TIPO_SENTENCA_JULGAMENTO", nullable=false)
     *
     * @var TipoSentencaJulgamento
     */
    private $tipoSentencaJulgamento;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoJulgamentoDenuncia", mappedBy="julgamentoDenuncia", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $arquivosJulgamentoDenuncia;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\RetificacaoJulgamentoDenuncia", mappedBy="julgamentoRetificado")
     *
     * @var RetificacaoJulgamentoDenuncia|null
     */
    private $retificacaoJulgamento;

    /**
     * Fábrica de instância de 'JulgamentoDenuncia'.
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
            $instance->setNivel(Utils::getValue('nivel', $data));
            $instance->setMulta(Utils::getBooleanValue('multa', $data));
            $instance->setDescricao(Utils::getValue('descricao', $data));
            $instance->setJustificativa(Utils::getValue('justificativa', $data));

            $pessoa = Utils::getValue('pessoa', $data);
            if (!empty($pessoa)) {
                $instance->setUsuario(Usuario::newInstance($pessoa));
            }

            $vlPercentualMulta = Utils::getValue('valorPercentualMulta', $data);
            if (!empty($vlPercentualMulta)) {
                $instance->setValorPercentualMulta($vlPercentualMulta);
            }

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $instance->setDenuncia(Denuncia::newInstance($denuncia));
            }

            $tipoJulgamento = Utils::getValue('tipoJulgamento', $data);
            if (!empty($tipoJulgamento)) {
                $instance->setTipoJulgamento(TipoJulgamento::newInstance($tipoJulgamento));
            }

            $qtDiasSuspensaoPropaganda = Utils::getValue('quantidadeDiasSuspensaoPropaganda', $data);
            if (!empty($qtDiasSuspensaoPropaganda)) {
                $instance->setQuantidadeDiasSuspensaoPropaganda($qtDiasSuspensaoPropaganda);
            }

            $tipoSentencaJulgamento = Utils::getValue('tipoSentencaJulgamento', $data);
            if (!empty($tipoSentencaJulgamento)) {
                $instance->setTipoSentencaJulgamento(TipoSentencaJulgamento::newInstance($tipoSentencaJulgamento));
            }

            $retificacaoJulgamentoDenuncia = Utils::getValue('retificacaoJulgamentoDenuncia', $data);
            if (!empty($retificacaoJulgamentoDenuncia)) {
                $instance->setRetificacaoJulgamento(
                    RetificacaoJulgamentoDenuncia::newInstance($retificacaoJulgamentoDenuncia)
                );
            }

            $arquivos = Utils::getValue('arquivosJulgamentoDenuncia', $data);
            if (!empty($arquivos)) {
                foreach ($arquivos as $arquivo) {
                    $instance->adicionarArquivoJulgamentoDenuncia(
                        ArquivoJulgamentoDenuncia::newInstance($arquivo)
                    );
                }
            }
        }

        return $instance;
    }

    /**
     * Adiciona um Arquivo no array de Arquivos
     *
     * @param ArquivoJulgamentoDenuncia $arquivoJulgamentoDenuncia
     */
    private function adicionarArquivoJulgamentoDenuncia(ArquivoJulgamentoDenuncia $arquivoJulgamentoDenuncia)
    {
        if ($this->getArquivosJulgamentoDenuncia() === null) {
            $this->setArquivosJulgamentoDenuncia(new ArrayCollection());
        }

        if ($arquivoJulgamentoDenuncia !== null) {
            $arquivoJulgamentoDenuncia->setJulgamentoDenuncia($this);
            $this->getArquivosJulgamentoDenuncia()->add($arquivoJulgamentoDenuncia);
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
     * @return integer
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * @param integer $nivel
     */
    public function setNivel($nivel): void
    {
        $this->nivel = $nivel;
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
     * @return Denuncia
     */
    public function getDenuncia()
    {
        return $this->denuncia;
    }

    /**
     * @param Denuncia $denuncia
     */
    public function setDenuncia($denuncia): void
    {
        $this->denuncia = $denuncia;
    }

    /**
     * @return TipoJulgamento
     */
    public function getTipoJulgamento()
    {
        return $this->tipoJulgamento;
    }

    /**
     * @param TipoJulgamento $tipoJulgamento
     */
    public function setTipoJulgamento($tipoJulgamento): void
    {
        $this->tipoJulgamento = $tipoJulgamento;
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
    public function getArquivosJulgamentoDenuncia()
    {
        return $this->arquivosJulgamentoDenuncia;
    }

    /**
     * @param array|\Doctrine\Common\Collections\ArrayCollection $arquivosJulgamentoDenuncia
     */
    public function setArquivosJulgamentoDenuncia($arquivosJulgamentoDenuncia): void
    {
        $this->arquivosJulgamentoDenuncia = $arquivosJulgamentoDenuncia;
    }

    /**
     * @return RetificacaoJulgamentoDenuncia|null
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
