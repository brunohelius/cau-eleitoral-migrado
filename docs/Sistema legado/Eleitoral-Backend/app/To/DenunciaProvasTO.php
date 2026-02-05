<?php

namespace App\To;

use App\Entities\DenunciaDefesa;
use App\Entities\DenunciaProvas;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'DenunciaProvas'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="DenunciaProvas")
 */
class DenunciaProvasTO
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
    private $dataProva;

    /**
     * @var string
     */
    private $descricaoProvasApresentadas;

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
     * @var string|null
     * @OA\Property()
     */
    private $destinatario;

    /**
     * Retorna uma nova instância de 'DenunciaDefesaTO'.
     *
     * @param DenunciaProvas|null $denunciaProvas
     * @return self
     */
    public static function newInstanceFromEntity(DenunciaProvas $denunciaProvas = null)
    {
        $instance = new self;

        if (null !== $denunciaProvas) {
            $instance->setId($denunciaProvas->getId());
            $instance->setDataProva($denunciaProvas->getDataProva());
            $instance->setDescricaoProvasApresentadas($denunciaProvas->getDescricaoProvasApresentadas());

            $denuncia = $denunciaProvas->getDenuncia();
            if (!empty($denuncia)) {
                $instance->setIdDenuncia($denuncia->getId());
            }

            $encaminhamento = $denunciaProvas->getEncaminhamentoDenuncia();
            if (!empty($encaminhamento)) {
                $instance->setIdEncaminhamento($encaminhamento->getId());
            }

            $arquivos = $denunciaProvas->getArquivosDenunciaProvas() ?? [];
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
     * @return \DateTime
     */
    public function getDataProva()
    {
        return $this->dataProva;
    }

    /**
     * @param \DateTime $dataProva
     */
    public function setDataProva($dataProva)
    {
        $this->dataProva = $dataProva;
    }

    /**
     * @return string
     */
    public function getDescricaoProvasApresentadas()
    {
        return $this->descricaoProvasApresentadas;
    }

    /**
     * @param string $descricaoProvasApresentadas
     */
    public function setDescricaoProvasApresentadas($descricaoProvasApresentadas)
    {
        $this->descricaoProvasApresentadas = $descricaoProvasApresentadas;
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

    /**
     * @return string|null
     */
    public function getDestinatario(): ?string
    {
        return $this->destinatario;
    }

    /**
     * @param string|null $destinatario
     */
    public function setDestinatario(?string $destinatario): void
    {
        $this->destinatario = $destinatario;
    }

}
