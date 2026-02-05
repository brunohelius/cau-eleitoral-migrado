<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 04/12/2019
 * Time: 15:45
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * Entidade de representação de 'Conselheiro'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ConselheiroRepository")
 * @ORM\Table(schema="public", name="tb_conselheiro")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class Conselheiro extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="id", type="integer")
     * @ORM\SequenceGenerator(sequenceName="tb_conselheiro_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Pessoa")
     * @ORM\JoinColumn(name="pessoa_id", referencedColumnName="id", nullable=false)
     * @var \App\Entities\Pessoa
     */
    private $pessoa;

    /**
     * @ORM\Column(name="dt_inicio_mandato", type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     * @var \DateTime
     */
    private $dataInicioMandato;

    /**
     * @ORM\Column(name="dt_fim_mandato", type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     * @var \DateTime
     */
    private $dataFimMandato;

    /**
     * @ORM\Column(name="tipo_conselheiro_id", type="integer", nullable=true)
     * @var integer
     */
    private $tipoConselheiroId;

    /**
     * @ORM\Column(name="representacao_conselheiro_id", type="integer", nullable=true)
     * @var integer
     */
    private $representacaoConselheiroId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Filial")
     * @ORM\JoinColumn(name="filial_id", referencedColumnName="id", nullable=false)
     * @var \App\Entities\Filial
     */
    private $filial;

    /**
     * @var boolean
     * @ORM\Column(name="ies", type="boolean", nullable=true)
     */
    private $ies;

    /**
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     * @var \DateTime
     */
    private $dataCadastro;

    /**
     * @ORM\Column(name="email", type="string", nullable=true)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(name="processo_eleitoral_id", type="integer", nullable=true)
     * @var integer
     */
    private $processoEleitoralId;

    /**
     * @var boolean
     * @ORM\Column(name="recomposicao_mandato", type="boolean", nullable=false)
     */
    private $recomposicaoMandato;

    /**
     * @ORM\Column(name="ano_eleicao", type="string", nullable=true)
     * @var string
     */
    private $anoEleicao;

    /**
     * @var boolean
     * @ORM\Column(name="ativo", type="boolean", nullable=false)
     */
    private $ativo;

    /**
     * Fábrica de instância de 'Conselheiro'.
     *
     * @param array $data
     * @return \App\Entities\Conselheiro
     */
    public static function newInstance($data = null)
    {
        $conselheiro = new Conselheiro();

        if ($data != null) {
            $conselheiro->setId(Utils::getValue('id', $data));
            $conselheiro->setDataFimMandato(Utils::getValue('dataFimMandato', $data));
            $conselheiro->setDataInicioMandato(Utils::getValue('dataInicioMandato', $data));
            $conselheiro->setAnoEleicao(Utils::getValue('anoEleicao', $data));
            $conselheiro->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $conselheiro->setEmail(Utils::getValue('email', $data));
            $conselheiro->setProcessoEleitoralId(Utils::getValue('processoEleitoralId', $data));
            $conselheiro->setRepresentacaoConselheiroId(Utils::getValue('representacaoConselheiroId', $data));
            $conselheiro->setTipoConselheiroId(Utils::getValue('tipoConselheiroId', $data));
            $conselheiro->setAtivo(Utils::getBooleanValue('ativo', $data));
            $conselheiro->setIes(Utils::getBooleanValue('ies', $data));
            $conselheiro->setRecomposicaoMandato(Utils::getBooleanValue('recomposicaoMandato', $data));

            $pessoa = Pessoa::newInstance(Utils::getValue('pessoa', $data));
            if (!empty($pessoa->getId())) {
                $conselheiro->setPessoa($pessoa);
            }

            $filial = Filial::newInstance(Utils::getValue('filial', $data));
            if (!empty($filial->getId())) {
                $conselheiro->setFilial($filial);
            }
        }

        return $conselheiro;
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
     * @return Pessoa
     */
    public function getPessoa()
    {
        return $this->pessoa;
    }

    /**
     * @param Pessoa $pessoa
     */
    public function setPessoa($pessoa): void
    {
        $this->pessoa = $pessoa;
    }

    /**
     * @return \DateTime
     */
    public function getDataInicioMandato()
    {
        return $this->dataInicioMandato;
    }

    /**
     * @param \DateTime $dataInicioMandato
     */
    public function setDataInicioMandato($dataInicioMandato): void
    {
        $this->dataInicioMandato = $dataInicioMandato;
    }

    /**
     * @return \DateTime
     */
    public function getDataFimMandato()
    {
        return $this->dataFimMandato;
    }

    /**
     * @param \DateTime $dataFimMandato
     */
    public function setDataFimMandato($dataFimMandato): void
    {
        $this->dataFimMandato = $dataFimMandato;
    }

    /**
     * @return int
     */
    public function getTipoConselheiroId()
    {
        return $this->tipoConselheiroId;
    }

    /**
     * @param int $tipoConselheiroId
     */
    public function setTipoConselheiroId($tipoConselheiroId): void
    {
        $this->tipoConselheiroId = $tipoConselheiroId;
    }

    /**
     * @return int
     */
    public function getRepresentacaoConselheiroId()
    {
        return $this->representacaoConselheiroId;
    }

    /**
     * @param int $representacaoConselheiroId
     */
    public function setRepresentacaoConselheiroId($representacaoConselheiroId): void
    {
        $this->representacaoConselheiroId = $representacaoConselheiroId;
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
     * @return bool
     */
    public function isIes()
    {
        return $this->ies;
    }

    /**
     * @param bool $ies
     */
    public function setIes($ies): void
    {
        $this->ies = $ies;
    }

    /**
     * @return \DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro($dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getProcessoEleitoralId()
    {
        return $this->processoEleitoralId;
    }

    /**
     * @param int $processoEleitoralId
     */
    public function setProcessoEleitoralId($processoEleitoralId): void
    {
        $this->processoEleitoralId = $processoEleitoralId;
    }

    /**
     * @return bool
     */
    public function isRecomposicaoMandato()
    {
        return $this->recomposicaoMandato;
    }

    /**
     * @param bool $recomposicaoMandato
     */
    public function setRecomposicaoMandato($recomposicaoMandato): void
    {
        $this->recomposicaoMandato = $recomposicaoMandato;
    }

    /**
     * @return string
     */
    public function getAnoEleicao()
    {
        return $this->anoEleicao;
    }

    /**
     * @param string $anoEleicao
     */
    public function setAnoEleicao($anoEleicao): void
    {
        $this->anoEleicao = $anoEleicao;
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
    public function setAtivo($ativo): void
    {
        $this->ativo = $ativo;
    }
}