<?php

namespace App\To;

use App\Entities\DenunciaAudienciaInstrucao;
use App\Entities\DenunciaDefesa;
use App\Entities\DenunciaProvas;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'DenunciaAudienciaInstrucao'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="DenunciaAudienciaInstrucao")
 */
class DenunciaAudienciaInstrucaoTO
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var null|array[]
     */
    private $arquivos;

    /**
     * @var \DateTime
     */
    private $dataAudienciaInstrucao;

    /**
     * @var \DateTime
     */
    private $dataCadastro;

    /**
     * @var string
     */
    private $descricaoDenunciaAudienciaInstrucao;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idDenuncia;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idEncaminhamento;

    /**
     * @var ArquivoDescricaoTO[]|null
     * @OA\Property()
     */
    private $descricaoArquivo;

    /**
     * Retorna uma nova instância de 'DenunciaAudienciaInstrucaoTO'.
     *
     * @param DenunciaAudienciaInstrucao|null $audienciaInstrucao
     * @return self
     */
    public static function newInstanceFromEntity(DenunciaAudienciaInstrucao $audienciaInstrucao = null)
    {
        $instance = new self;

        if (null !== $audienciaInstrucao) {
            $instance->setId($audienciaInstrucao->getId());
            $instance->setDataCadastro($audienciaInstrucao->getDataCadastro());
            $instance->setDataAudienciaInstrucao($audienciaInstrucao->getDataAudienciaInstrucao());
            $instance->setDescricaoDenunciaAudienciaInstrucao(
                $audienciaInstrucao->getDescricaoDenunciaAudienciaInstrucao()
            );

            $denuncia = $audienciaInstrucao->getDenuncia();
            if (!empty($denuncia)) {
                $instance->setIdDenuncia($denuncia->getId());
            }

            $encaminhamento = $audienciaInstrucao->getEncaminhamentoDenuncia();
            if (!empty($encaminhamento)) {
                $instance->setIdEncaminhamento($encaminhamento->getId());
            }

            $arquivos = $audienciaInstrucao->getArquivosAudienciaInstrucao() ?? [];
            if (!is_array($arquivos)) {
                $arquivos = $arquivos->toArray();
            }

            $instance->setArquivos($arquivos);
        }
        return $instance;
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
     * @return int
     */
    public function getIdEncaminhamento()
    {
        return $this->idEncaminhamento;
    }

    /**
     * @param int $idEncaminhamento
     */
    public function setIdEncaminhamento($idEncaminhamento)
    {
        $this->idEncaminhamento = $idEncaminhamento;
    }

    /**
     * @return null|array[]
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param array[] $arquivos
     */
    public function setArquivos($arquivos)
    {
        $this->arquivos = $arquivos;
    }

    /**
     * @return int
     */
    public function getIdDenuncia()
    {
        return $this->idDenuncia;
    }

    /**
     * @param $idDenuncia
     */
    public function setIdDenuncia($idDenuncia)
    {
        $this->idDenuncia = $idDenuncia;
    }

    /**
     * @return \DateTime
     */
    public function getDataAudienciaInstrucao()
    {
        return $this->dataAudienciaInstrucao;
    }

    /**
     * @param \DateTime $dataAudienciaInstrucao
     */
    public function setDataAudienciaInstrucao($dataAudienciaInstrucao)
    {
        $this->dataAudienciaInstrucao = $dataAudienciaInstrucao;
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
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return string
     */
    public function getDescricaoDenunciaAudienciaInstrucao()
    {
        return $this->descricaoDenunciaAudienciaInstrucao;
    }

    /**
     * @param string $descricaoDenunciaAudienciaInstrucao
     */
    public function setDescricaoDenunciaAudienciaInstrucao($descricaoDenunciaAudienciaInstrucao)
    {
        $this->descricaoDenunciaAudienciaInstrucao = $descricaoDenunciaAudienciaInstrucao;
    }

    /**
     * @return ArquivoDescricaoTO[]|null
     */
    public function getDescricaoArquivo(): ?array
    {
        return $this->descricaoArquivo;
    }

    /**
     * @param ArquivoDescricaoTO[]|null $descricaoArquivo
     */
    public function setDescricaoArquivo(?array $descricaoArquivo): void
    {
        $this->descricaoArquivo = $descricaoArquivo;
    }

}
