<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 08/11/2019
 * Time: 16:13
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * Entidade de representação de 'ProfissionalRegistro'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProfissionalRegistroRepository")
 * @ORM\Table(schema="public", name="tb_profissional_registro")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ProfissionalRegistro extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="id", type="integer")
     * @ORM\SequenceGenerator(sequenceName="tb_profissional_registro_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="data_ini_registro", type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     * @var \DateTime
     */
    private $dataInicio;

    /**
     * @ORM\Column(name="data_fim_registro", type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     * @var \DateTime
     */
    private $dataFim;

    /**
     * @ORM\Column(name="tiporegistro_id", type="integer", nullable=true)
     * @var integer
     */
    private $tipo;

    /**
     * @ORM\Column(name="observacao", type="string", nullable=false)
     * @var string
     */
    private $observacao;

    /**
     * @ORM\Column(name="protocolo_id", type="integer", nullable=true)
     * @var integer
     */
    private $protocolo;

    /**
     * @ORM\Column(name="uf_registro", type="string", length=2, nullable=false)
     * @var string
     */
    private $uf;

    /**
     * @ORM\Column(name="registro_regional", type="string", length=250, nullable=false)
     * @var string
     */
    private $registroRegional;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\SituacaoRegistro")
     * @ORM\JoinColumn(name="situacaoregistro_id", referencedColumnName="id", nullable=false)
     * @var \App\Entities\SituacaoRegistro
     */
    private $situacaoRegistro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Pessoa")
     * @ORM\JoinColumn(name="profissional_id", referencedColumnName="id", nullable=false)
     * @var \App\Entities\Pessoa
     */
    private $pessoa;

    /**
     * Fábrica de instância de 'ProfissionalRegistro'.
     *
     * @param array $data
     * @return ProfissionalRegistro
     */
    public static function newInstance($data = null)
    {
        $profissionalRegistro = new ProfissionalRegistro();

        if (!empty($data)) {
            $profissionalRegistro->setId(Utils::getValue('id'));
            $profissionalRegistro->setDataInicio(Utils::getValue('dataInicio'));
            $profissionalRegistro->setDataFim(Utils::getValue('dataFim'));
            $profissionalRegistro->setObservacao(Utils::getValue('observacao'));
            $profissionalRegistro->setProtocolo(Utils::getValue('protocolo'));
            $profissionalRegistro->setRegistroRegional(Utils::getValue('registroRegional'));
            $profissionalRegistro->setTipo(Utils::getValue('tipo'));
            $profissionalRegistro->setUf(Utils::getValue('uf'));

            $pessoa = Pessoa::newInstance(Utils::getValue('pessoa'));
            if (!empty($pessoa->getId())) {
                $profissionalRegistro->setPessoa($pessoa);
            }

            $situacao = SituacaoRegistro::newInstance(Utils::getValue('situacaoRegistro'));
            if (!empty($situacao->getId())) {
                $profissionalRegistro->setSituacaoRegistro($situacao);
            }
        }

        return $profissionalRegistro;
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
     * @return \DateTime
     */
    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    /**
     * @param \DateTime $dataInicio
     */
    public function setDataInicio($dataInicio): void
    {
        $this->dataInicio = $dataInicio;
    }

    /**
     * @return \DateTime
     */
    public function getDataFim()
    {
        return $this->dataFim;
    }

    /**
     * @param \DateTime $dataFim
     */
    public function setDataFim($dataFim): void
    {
        $this->dataFim = $dataFim;
    }

    /**
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param int $tipo
     */
    public function setTipo($tipo): void
    {
        $this->tipo = $tipo;
    }

    /**
     * @return string
     */
    public function getObservacao()
    {
        return $this->observacao;
    }

    /**
     * @param string $observacao
     */
    public function setObservacao($observacao): void
    {
        $this->observacao = $observacao;
    }

    /**
     * @return int
     */
    public function getProtocolo()
    {
        return $this->protocolo;
    }

    /**
     * @param int $protocolo
     */
    public function setProtocolo($protocolo): void
    {
        $this->protocolo = $protocolo;
    }

    /**
     * @return string
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * @param string $uf
     */
    public function setUf($uf): void
    {
        $this->uf = $uf;
    }

    /**
     * @return string
     */
    public function getRegistroRegional()
    {
        return $this->registroRegional;
    }

    /**
     * @param string $registroRegional
     */
    public function setRegistroRegional($registroRegional): void
    {
        $this->registroRegional = $registroRegional;
    }

    /**
     * @return SituacaoRegistro
     */
    public function getSituacaoRegistro()
    {
        return $this->situacaoRegistro;
    }

    /**
     * @param SituacaoRegistro $situacaoRegistro
     */
    public function setSituacaoRegistro($situacaoRegistro): void
    {
        $this->situacaoRegistro = $situacaoRegistro;
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
}