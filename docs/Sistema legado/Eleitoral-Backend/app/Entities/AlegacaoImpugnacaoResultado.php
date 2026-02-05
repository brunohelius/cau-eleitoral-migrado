<?php
/*
 * ImpugnacaoResultado.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\To\ImpugnacaoResultadoTO;
use App\Util\Utils;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use DoctrineProxies\__CG__\App\Entities\PedidoImpugnacaoResultado;
use Illuminate\Support\Arr;
use OpenApi\Annotations as OA;
use Exception;

/**
 * Entidade de representação da AlegacaoImpugnacaoResultado
 *
 * @ORM\Entity(repositoryClass="App\Repository\AlegacaoImpugnacaoResultadoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ALEGACAO_IMPUGNACAO_RESULTADO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class AlegacaoImpugnacaoResultado extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_ALEGACAO_IMPUGNACAO_RESULTADO_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="NARRACAO_FATOS", type="string",nullable=false)
     *
     * @var string
     */
    private $narracao;

    /**
     * @ORM\Column(name="DT_CADASTRO", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $dataCadastro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\ImpugnacaoResultado")
     * @ORM\JoinColumn(name="ID_PEDIDO_IMPUGNACAO_RESULTADO", referencedColumnName="ID", nullable=false)
     *
     * @var ImpugnacaoResultado
     */
    private $impugnacaoResultado;


    /**
     * @ORM\Column(name="NM_ARQUIVO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $nomeArquivo;

    /**
     * @ORM\Column(name="NM_FIS_ARQUIVO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $nomeArquivoFisico;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_PROFISSIONAL_INCLUSAO", referencedColumnName="id", nullable=false)
     *
     * @var Profissional
     */
    private $profissional;

    /**
     * @ORM\Column(name="NUMERO", type="integer", nullable=false)
     *
     * @var integer
     */
    private $numero;

    /**
     * Fábrica de instância de 'AlegacaoImpugnacaoResultado'.
     *
     * @param array $data
     * @return AlegacaoImpugnacaoResultado
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $alegacaoImpugnacaoResultado = new AlegacaoImpugnacaoResultado();

        if ($data != null) {
            $alegacaoImpugnacaoResultado->setId(Utils::getValue('id', $data));
            $alegacaoImpugnacaoResultado->setNumero(Utils::getValue('numero', $data));
            $alegacaoImpugnacaoResultado->setNarracao(Utils::getValue('narracao', $data));
            $alegacaoImpugnacaoResultado->setDataCadastro(Utils::getValue('dataCadastro', $data));
            $alegacaoImpugnacaoResultado->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $alegacaoImpugnacaoResultado->setNomeArquivoFisico(Utils::getValue('nomeArquivoFisico', $data));

            $profissional = Utils::getValue('profissional', $data);
            if(!empty($profissional)) {
                $alegacaoImpugnacaoResultado->setProfissional(Profissional::newInstance($profissional));
            }

            $impugnacaoResultado = Utils::getValue('impugnacaoResultado', $data);
            if(!empty($impugnacaoResultado)) {
                $alegacaoImpugnacaoResultado->setImpugnacaoResultado(ImpugnacaoResultado::newInstance($impugnacaoResultado));
            }

        }
        return $alegacaoImpugnacaoResultado;
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
     * @return string
     */
    public function getNarracao()
    {
        return $this->narracao;
    }

    /**
     * @param string $narracao
     */
    public function setNarracao($narracao)
    {
        $this->narracao = $narracao;
    }

    /**
     * @return DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @param DateTime $dataCadastro
     */
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return ImpugnacaoResultado
     */
    public function getImpugnacaoResultado()
    {
        return $this->impugnacaoResultado;
    }

    /**
     * @param ImpugnacaoResultado $impugnacaoResultado
     */
    public function setImpugnacaoResultado($impugnacaoResultado)
    {
        $this->impugnacaoResultado = $impugnacaoResultado;
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
    public function setNomeArquivo($nomeArquivo)
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
    public function setNomeArquivoFisico($nomeArquivoFisico)
    {
        $this->nomeArquivoFisico = $nomeArquivoFisico;
    }

    /**
     * @return Profissional
     */
    public function getProfissional()
    {
        return $this->profissional;
    }

    /**
     * @param Profissional $profissional
     */
    public function setProfissional($profissional)
    {
        $this->profissional = $profissional;
    }

    /**
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param int $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

}
