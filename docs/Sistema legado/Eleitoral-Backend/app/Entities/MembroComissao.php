<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 07/10/2019
 * Time: 15:30
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use stdClass;

/**
 * Entidade de representação de 'Membro da Comissão'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\MembroComissaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_MEMBRO_COMISSAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class MembroComissao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_MEMBRO_COMISSAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_membro_comissao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoParticipacaoMembro")
     * @ORM\JoinColumn(name="ID_TIPO_PARTICIPACAO", referencedColumnName="ID_TIPO_PARTICIPACAO", nullable=false)
     *
     * @var \App\Entities\TipoParticipacaoMembro
     */
    private $tipoParticipacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\InformacaoComissaoMembro")
     * @ORM\JoinColumn(name="ID_INF_COMISSAO_MEMBRO", referencedColumnName="ID_INF_COMISSAO_MEMBRO", nullable=false)
     *
     * @var \App\Entities\InformacaoComissaoMembro
     */
    private $informacaoComissaoMembro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\MembroComissao")
     * @ORM\JoinColumn(name="ID_MEMBRO_SUBSTITUTO", referencedColumnName="ID_MEMBRO_COMISSAO", nullable=false)
     *
     * @var MembroComissao
     */
    private $membroSubstituto;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\MembroComissaoSituacao", mappedBy="membroComissao", cascade={"persist"})
     *
     * @var array|ArrayCollection
     */
    private $membroComissaoSituacao;

    /**
     * @ORM\Column(name="ID_PESSOA", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $pessoa;

    /**
     * @ORM\Column(name="ID_CAU_UF", type="integer", length=11, nullable=false)
     *
     * @var integer
     */
    private $idCauUf;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Filial")
     * @ORM\JoinColumn(name="ID_CAU_UF", referencedColumnName="id", nullable=false)
     * @var \App\Entities\Filial
     */
    private $filial;

    /**
     * @ORM\Column(name="ST_EXCLUIDO", type="boolean", nullable=false)
     *
     * @var boolean
     */
    private $excluido;

    /**
     * @ORM\Column(name="ST_RESPOSTA_DECLARACAO", type="boolean", nullable=true)
     *
     * @var boolean
     */
    private $sitRespostaDeclaracao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Pessoa")
     * @ORM\JoinColumn(name="ID_PESSOA", referencedColumnName="id")
     *
     * @var \App\Entities\Pessoa
     */
    private $pessoaEntity;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_PESSOA", referencedColumnName="id")
     *
     * @var \App\Entities\Profissional
     */
    private $profissionalEntity;

    /**
     * @var integer
     */
    private $idSituacao;

    /**
     * Situação Vigente
     *
     * @var stdClass
     */
    private $situacaoVigente;

    /**
     * Dados do Profissional
     *
     * @var stdClass
     */
    private $profissional;

    /**
     * Dados da Declaração
     *
     * @var stdClass
     */
    private $declaracao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoDecMembroComissao", mappedBy="membroComissao")
     *
     * @var array|ArrayCollection
     */
    private $arquivos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\DenunciaAdmitida", mappedBy="membroComissao",  fetch="EXTRA_LAZY")
     *
     * @var array|ArrayCollection
     */
    private $denunciaAdmitida;

    /**
     * Transient
     * @var boolean
     */
    private $isInserido = false;

    /**
     * Fábrica de instância de 'MembroComissao'.
     *
     * @param array $data
     * @return MembroComissao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $membroComissao = new MembroComissao();

        if ($data != null) {
            $idCauUf = Utils::getValue('idCauUf', $data);
            $idPessoa = Utils::getValue('pessoa', $data);
            $membroComissao->setId(Utils::getValue('id', $data));
            $membroComissao->setPessoa($idPessoa);
            $membroComissao->setIdSituacao(Utils::getValue('idSituacao', $data));
            $membroComissao->setIdCauUf($idCauUf);
            $membroComissao->setExcluido(Utils::getBooleanValue('excluido', $data));
            $membroComissao->setDeclaracao(Utils::getValue('declaracao', $data));
            $membroComissao->setIsInserido(Utils::getValue('inserido', $data));

            $membroComissao->setSitRespostaDeclaracao(Utils::getValue('sitRespostaDeclaracao',$data));
            if(!empty($membroComissao->getSitRespostaDeclaracao())) {
                $membroComissao->setSitRespostaDeclaracao(false);
            }

            $tipoParticipacao = Utils::getValue('tipoParticipacao', $data);
            if (!empty($tipoParticipacao)) {
                $membroComissao->setTipoParticipacao(TipoParticipacaoMembro::newInstance($tipoParticipacao));
            }

            $informacaoComissaoMembro = Utils::getValue('informacaoComissaoMembro', $data);
            if (!empty($informacaoComissaoMembro)) {
                $membroComissao->setInformacaoComissaoMembro(InformacaoComissaoMembro::newInstance($informacaoComissaoMembro));
            }

            $membroSubstitutoArray = Utils::getValue('membroSubstituto', $data);
            if (!empty($membroSubstitutoArray)) {
                $membroSubstituto = MembroComissao::newInstance($membroSubstitutoArray);
                if (!empty($membroSubstituto->getPessoa())) {
                    $membroComissao->setMembroSubstituto($membroSubstituto);
                }
            }

            $membrosComissaoSituacao = Utils::getValue('membroComissaoSituacao', $data);
            if (!empty($membrosComissaoSituacao)) {
                foreach ($membrosComissaoSituacao as $membroComissaoSituacao) {
                    $membroComissao->adicionarMembroComissaoSituacao(MembroComissaoSituacao::newInstance($membroComissaoSituacao));
                }
            }

            $arquivos = Utils::getValue('arquivos', $data);
            if (!empty($arquivos)) {
                foreach ($arquivos as $dataArquivo) {
                    $membroComissao->adicionarArquivo(ArquivoDecMembroComissao::newInstance($dataArquivo));
                }
            }

            $filial = Utils::getValue('filial', $data);
            if(empty($filial) && !empty($idCauUf)){
                $filial = [
                    "id" => $idCauUf
                ];
            }
            if (!empty($filial)) {
                $membroComissao->setFilial(Filial::newInstance($filial));
            }

            $profissionaEntity = Utils::getValue('profissionalEntity', $data);
            if(empty($profissionaEntity) && !empty($idPessoa)){
                $profissionaEntity = [
                    "id" => $idPessoa
                ];
            }
            if (!empty($profissionaEntity)) {
                $membroComissao->setProfissionalEntity(Profissional::newInstance($profissionaEntity));
            }

            $pessoaEntity = Utils::getValue('pessoaEntity', $data);
            if(empty($pessoaEntity) && !empty($idPessoa)){
                $pessoaEntity = [
                    "id" => $idPessoa
                ];
            }
            if (!empty($pessoaEntity)) {
                $membroComissao->setPessoaEntity(Pessoa::newInstance($pessoaEntity));
            }

            $denunciasAdmitidas = Utils::getValue('denunciaAdmitida', $data);
            if (!empty($denunciasAdmitidas)) {
                foreach ($denunciasAdmitidas as $denunciaAdmitida) {
                    $membroComissao->adicionarDenunciaAdmitida(DenunciaAdmitida::newInstance($denunciaAdmitida));
                }
            }
        }

        return $membroComissao;
    }

    /**
     * Adiciona o 'ArquivoDecMembroComissao' à sua respectiva coleção.
     *
     * @param ArquivoDecMembroComissao $arquivo
     */
    private function adicionarArquivo(ArquivoDecMembroComissao $arquivo)
    {
        if ($this->getArquivos() == null) {
            $this->setArquivos(new ArrayCollection());
        }

        if (!empty($arquivo)) {
            $arquivo->setCalendario($this);
            $this->getArquivos()->add($arquivo);
        }
    }

    /**
     * Adiciona o 'MembroComissaoSituacao' à sua respectiva coleção.
     *
     * @param MembroComissaoSituacao $membroComissaoSituacao
     */
    private function adicionarMembroComissaoSituacao(MembroComissaoSituacao $membroComissaoSituacao)
    {
        if ($this->getMembroComissaoSituacao() == null) {
            $this->setMembroComissaoSituacao(new ArrayCollection());
        }

        if (!empty($membroComissaoSituacao)) {
            $membroComissaoSituacao->setMembroComissao($this);
            $this->getMembroComissaoSituacao()->add($membroComissaoSituacao);
        }
    }

    /**
     * Adiciona o 'MembroComissaoSituacao' à sua respectiva coleção.
     *
     * @param DenunciaAdmitida $denunciaAdmitida
     */
    private function adicionarDenunciaAdmitida(DenunciaAdmitida $denunciaAdmitida)
    {
        if ($this->getDenunciaAdmitida() == null) {
            $this->setDenunciaAdmitida(new ArrayCollection());
        }

        if (!empty($denunciaAdmitida)) {
            $denunciaAdmitida->setMembroComissao($this);
            $this->getDenunciaAdmitida()->add($denunciaAdmitida);
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
     * @return TipoParticipacaoMembro
     */
    public function getTipoParticipacao()
    {
        return $this->tipoParticipacao;
    }

    /**
     * @param TipoParticipacaoMembro $tipoParticipacao
     */
    public function setTipoParticipacao($tipoParticipacao): void
    {
        $this->tipoParticipacao = $tipoParticipacao;
    }

    /**
     * @return InformacaoComissaoMembro
     */
    public function getInformacaoComissaoMembro()
    {
        return $this->informacaoComissaoMembro;
    }

    /**
     * @param InformacaoComissaoMembro $informacaoComissaoMembro
     */
    public function setInformacaoComissaoMembro($informacaoComissaoMembro): void
    {
        $this->informacaoComissaoMembro = $informacaoComissaoMembro;
    }

    /**
     * @return MembroComissao
     */
    public function getMembroSubstituto()
    {
        return $this->membroSubstituto;
    }

    /**
     * @param MembroComissao $membroSubstituto
     */
    public function setMembroSubstituto($membroSubstituto): void
    {
        $this->membroSubstituto = $membroSubstituto;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getMembroComissaoSituacao()
    {
        return $this->membroComissaoSituacao;
    }

    /**
     * @param array|ArrayCollection $membroComissaoSituacao
     */
    public function setMembroComissaoSituacao($membroComissaoSituacao): void
    {
        $this->membroComissaoSituacao = $membroComissaoSituacao;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getHistoricoMembroComissao()
    {
        return $this->historicoMembroComissao;
    }

    /**
     * @param array|ArrayCollection $historicoMembroComissao
     */
    public function setHistoricoMembroComissao($historicoMembroComissao): void
    {
        $this->historicoMembroComissao = $historicoMembroComissao;
    }

    /**
     * @return int
     */
    public function getPessoa()
    {
        return $this->pessoa;
    }

    /**
     * @param int $pessoa
     */
    public function setPessoa($pessoa): void
    {
        $this->pessoa = $pessoa;
    }

    /**
     * @return int
     */
    public function getIdCauUf()
    {
        return $this->idCauUf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf($idCauUf): void
    {
        $this->idCauUf = $idCauUf;
    }

    /**
     * @return Filial
     */
    public function getFilial()
    {
        return $this->filial;
    }

    /**
     * @param Filial $filial
     */
    public function setFilial($filial): void
    {
        $this->filial = $filial;
    }

    /**
     * @return int
     */
    public function getIdSituacao()
    {
        return $this->idSituacao;
    }

    /**
     * @param int $idSituacao
     */
    public function setIdSituacao($idSituacao): void
    {
        $this->idSituacao = $idSituacao;
    }

    /**
     * @return bool
     */
    public function isExcluido()
    {
        return $this->excluido;
    }

    /**
     * @param bool $excluido
     */
    public function setExcluido($excluido): void
    {
        $this->excluido = $excluido;
    }

    /**
     * @param stdClass $declaracao
     */
    public function setDeclaracao($declaracao)
    {
        $this->declaracao = $declaracao;
    }

    /**
     * @return stdClass
     */
    public function getDeclaracao()
    {
        return $this->declaracao;
    }

    /**
     * @return stdClass
     */
    public function getProfissional()
    {
        return $this->profissional;
    }

    /**
     * @param stdClass $profissional
     */
    public function setProfissional($profissional): void
    {
        $this->profissional = $profissional;
    }

    /**
     * @param $sitRespostaDeclaracao
     */
    public function setSitRespostaDeclaracao($sitRespostaDeclaracao)
    {
      $this->sitRespostaDeclaracao = $sitRespostaDeclaracao;
    }

    /**
     * @return bool
     */
    public function getSitRespostaDeclaracao()
    {
        return $this->sitRespostaDeclaracao;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param array|ArrayCollection $arquivos
     */
    public function setArquivos($arquivos)
    {
        $this->arquivos = $arquivos;
    }

    /**
     * Retorna a situação vigente do calendário.
     *
     * @return MembroComissaoSituacao |null
     */
    public function setSituacaoVigente()
    {
        $situacaoMembro = null;

        $situacoes = $this->getSituacoesOrdenadasPorData($this->getMembroComissaoSituacao());

        if (!empty($situacoes) && count($situacoes) > 0) {
            $membroSituacao = $situacoes->last();
            $situacaoMembro = $membroSituacao->getSituacaoMembroComissao();
        }

        $this->situacaoVigente = $situacaoMembro;
    }

    /**
     * Seta a situaçao manual
     *
     * @param SituacaoMembroComissao $situacaoMembroComissao
     * @return void
     */
    public function setSituacao(SituacaoMembroComissao $situacaoMembroComissao)
    {
        $this->situacaoVigente = $situacaoMembroComissao;
    }

    /**
     * Retorna as situações ordenadas por data.
     *
     * @param $situacoes
     * @return mixed
     */
    private function getSituacoesOrdenadasPorData($situacoes)
    {
        $situacoesOrdenadas = new ArrayCollection();

        if (!empty($situacoes)) {
            $iterator = $situacoes->getIterator();

            $iterator->uasort(function ($a, $b) {
                return ($a->getData() < $b->getData()) ? -1 : 1;
            });

            $situacoesOrdenadas = new ArrayCollection(iterator_to_array($iterator));
        }

        return $situacoesOrdenadas;
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

    /**
    * @return \App\Entities\Pessoa
    */
    public function getPessoaEntity()
    {
        return $this->pessoaEntity;
    }

    /**
     * @param \App\Entities\Pessoa $pessoaEntity
     */
    public function setPessoaEntity($pessoaEntity)
    {
        $this->pessoaEntity = $pessoaEntity;
    }

    /**
     * @return \App\Entities\Profissional
     */
    public function getProfissionalEntity()
    {
        return $this->profissionalEntity;
    }

    /**
     * @param \App\Entities\Profissional $profissionalEntity
     */
    public function setProfissionalEntity($profissionalEntity): void
    {
        $this->profissionalEntity = $profissionalEntity;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getDenunciaAdmitida()
    {
        return $this->denunciaAdmitida;
    }

    /**
     * @param array|ArrayCollection $denunciaAdmitida
     */
    public function setDenunciaAdmitida($denunciaAdmitida): void
    {
        $this->denunciaAdmitida = $denunciaAdmitida;
    }

    /**
     * @return bool
     */
    public function isInserido()
    {
        return $this->isInserido;
    }

    /**
     * @param bool $isInserido
     */
    public function setIsInserido($isInserido): void
    {
        $this->isInserido = $isInserido;
    }
}
