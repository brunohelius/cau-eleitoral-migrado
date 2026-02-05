<?php
/*
 * InformacaoComissaoMembro.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'Informação de Comissão Membro'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\InformacaoComissaoMembroRepository")
 * @ORM\Table(schema="eleitoral", name="TB_INF_COMISSAO_MEMBRO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class InformacaoComissaoMembro extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_INF_COMISSAO_MEMBRO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_inf_comissao_membro_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="ST_MAJORITARIO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $situacaoMajoritario;

    /**
     * @ORM\Column(name="ST_CONSELHEIRO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $situacaoConselheiro;

    /**
     * @ORM\Column(name="TP_OPCAO", type="integer")
     *
     * @var integer
     */
    private $tipoOpcao;

    /**
     * @ORM\Column(name="QTDE_MINIMA", type="integer")
     *
     * @var integer
     */
    private $quantidadeMinima;

    /**
     * @ORM\Column(name="QTDE_MAXIMA", type="integer")
     *
     * @var integer
     */
    private $quantidadeMaxima;

    /**
     * @ORM\Column(name="ST_CONCLUIDO", type="boolean", nullable=false)
     *
     * @var bool
     */
    private $situacaoConcluido;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\AtividadeSecundariaCalendario")
     * @ORM\JoinColumn(name="ID_ATIV_SECUNDARIA", referencedColumnName="ID_ATIV_SECUNDARIA", nullable=false)
     *
     * @var \App\Entities\AtividadeSecundariaCalendario
     */
    private $atividadeSecundaria;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\DocumentoComissaoMembro", mappedBy="informacaoComissaoMembro", fetch="EAGER")
     *
     * @var DocumentoComissaoMembro
     */
    private $documentoComissaoMembro;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\MembroComissao", mappedBy="informacaoComissaoMembro", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $membrosComissao;

    /**
     * Fábrica de instância de Informação Comissão Membro'.
     *
     * @param array $data
     * @return InformacaoComissaoMembro
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $informacaoComissaoMembro = new InformacaoComissaoMembro();
        if ($data != null) {

            $atividadeSecundaria = AtividadeSecundariaCalendario::newInstance(
                Utils::getValue('atividadeSecundaria', $data)
            );

            $informacaoComissaoMembro->setId(Utils::getValue('id', $data));
            $informacaoComissaoMembro->setAtividadeSecundaria($atividadeSecundaria);
            $informacaoComissaoMembro->setTipoOpcao(Utils::getValue('tipoOpcao', $data));
            $informacaoComissaoMembro->setQuantidadeMaxima(Utils::getValue('quantidadeMaxima', $data));
            $informacaoComissaoMembro->setQuantidadeMinima(Utils::getValue('quantidadeMinima', $data));
            $informacaoComissaoMembro->setSituacaoConcluido(Utils::getBooleanValue('situacaoConcluido', $data));
            $informacaoComissaoMembro->setSituacaoConselheiro(Utils::getBooleanValue('situacaoConselheiro', $data));
            $informacaoComissaoMembro->setSituacaoMajoritario(Utils::getBooleanValue('situacaoMajoritario', $data));

            $documentoComissaoMembro = DocumentoComissaoMembro::newInstance(Utils::getValue('documentoComissaoMembro', $data));
            /** DocumentoComissaoMembro $documentoComissaoMembro*/
            if (!empty($documentoComissaoMembro->getId())) {
                $informacaoComissaoMembro->setDocumentoComissaoMembro(DocumentoComissaoMembro::newInstance($documentoComissaoMembro));
            }

            $membrosComissao = Utils::getValue('membrosComissao', $data);
            if (!empty($membrosComissao)) {
                foreach ($membrosComissao as $membroComissao) {
                    $informacaoComissaoMembro->adicionarMembrosComissao(MembroComissao::newInstance($membroComissao));
                }
            }
        }

        return $informacaoComissaoMembro;
    }

    /**
     * Adiciona a 'DocumentoComissaoMembro' à sua respectiva coleção.
     *
     * @param DocumentoComissaoMembro $documentoComissaoMembro
     */
    private function adicionarDocumentoComissaoMembro($documentoComissaoMembro)
    {
        if ($this->getDocumentoComissaoMembro() == null) {
            $this->setDocumentoComissaoMembro(new ArrayCollection());
        }

        if (!empty($documentoComissaoMembro)) {
            $documentoComissaoMembro->setInformacaoComissaoMembro($this);
            $this->getDocumentoComissaoMembro()->add($documentoComissaoMembro);
        }
    }

    /**
     * Adiciona a 'MembroComissao' a sua respectiva coleção.
     *
     * @param MembroComissao $membroComissao
     */
    private function adicionarMembrosComissao($membroComissao)
    {
        if ($this->getMembrosComissao() == null) {
            $this->setMembrosComissao(new ArrayCollection());
        }

        if (!empty($membroComissao)) {
            $membroComissao->setInformacaoComissaoMembro($this);
            $this->getMembrosComissao()->add($membroComissao);
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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isSituacaoMajoritario()
    {
        return $this->situacaoMajoritario;
    }

    /**
     * @param bool $situacaoMajoritario
     */
    public function setSituacaoMajoritario($situacaoMajoritario)
    {
        $this->situacaoMajoritario = $situacaoMajoritario;
    }

    /**
     * @return bool
     */
    public function isSituacaoConselheiro()
    {
        return $this->situacaoConselheiro;
    }

    /**
     * @param bool $situacaoConselheiro
     */
    public function setSituacaoConselheiro($situacaoConselheiro)
    {
        $this->situacaoConselheiro = $situacaoConselheiro;
    }

    /**
     * @return int
     */
    public function getTipoOpcao()
    {
        return $this->tipoOpcao;
    }

    /**
     * @param int $tipoOpcao
     */
    public function setTipoOpcao($tipoOpcao)
    {
        $this->tipoOpcao = $tipoOpcao;
    }

    /**
     * @return int
     */
    public function getQuantidadeMinima()
    {
        return $this->quantidadeMinima;
    }

    /**
     * @param int $quantidadeMinima
     */
    public function setQuantidadeMinima($quantidadeMinima)
    {
        $this->quantidadeMinima = $quantidadeMinima;
    }

    /**
     * @return int
     */
    public function getQuantidadeMaxima()
    {
        return $this->quantidadeMaxima;
    }

    /**
     * @param int $quantidadeMaxima
     */
    public function setQuantidadeMaxima($quantidadeMaxima)
    {
        $this->quantidadeMaxima = $quantidadeMaxima;
    }

    /**
     * @return bool
     */
    public function getSituacaoConcluido()
    {
        return $this->situacaoConcluido;
    }

    /**
     * @param bool $situacaoConcluido
     */
    public function setSituacaoConcluido($situacaoConcluido)
    {
        $this->situacaoConcluido = $situacaoConcluido;
    }

    /**
     * @return AtividadeSecundariaCalendario
     */
    public function getAtividadeSecundaria()
    {
        return $this->atividadeSecundaria;
    }

    /**
     * @param AtividadeSecundariaCalendario $atividadeSecundaria
     */
    public function setAtividadeSecundaria($atividadeSecundaria)
    {
        $this->atividadeSecundaria = $atividadeSecundaria;
    }

    /**
     * @return DocumentoComissaoMembro
     */
    public function getDocumentoComissaoMembro()
    {
        return $this->documentoComissaoMembro;
    }

    /**
     * @param $documentoComissaoMembro
     */
    public function setDocumentoComissaoMembro($documentoComissaoMembro)
    {
        $this->documentoComissaoMembro = $documentoComissaoMembro;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getMembrosComissao()
    {
        return $this->membrosComissao;
    }

    /**
     * @param array|ArrayCollection $membrosComissao
     */
    public function setMembrosComissao($membrosComissao): void
    {
        $this->membrosComissao = $membrosComissao;
    }
}
