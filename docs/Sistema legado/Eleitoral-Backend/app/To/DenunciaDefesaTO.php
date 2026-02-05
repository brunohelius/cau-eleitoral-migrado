<?php

namespace App\To;

use App\Entities\ArquivoDenunciaDefesa;
use App\Entities\DenunciaDefesa;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'DenunciaDefesa'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="DenunciaDefesa")
 */
class DenunciaDefesaTO
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
    private $dataDefesa;

    /**
     * @var string
     */
    private $descricaoDefesa;

    /**
     * @var integer
     * @OA\Property()
     */
    private $idDenuncia;

    /**
     * @var string|null
     */
    private $usuario;

    /**
     * @var ArquivoDescricaoTO[]|null
     */
    private $descricaoArquivo;

    /**
     * Retorna uma nova instância de 'DenunciaDefesaTO'.
     *
     * @param DenunciaDefesa $denunciaDefesa
     * @return self
     */
    public static function newInstanceFromEntity($denunciaDefesa = null)
    {
        $instance = new self;

        if (null !== $denunciaDefesa) {
            $instance->setId($denunciaDefesa->getId());
            $instance->setDataDefesa($denunciaDefesa->getDataDefesa());
            $instance->setDescricaoDefesa($denunciaDefesa->getDescricaoDefesa());

            $denuncia = $denunciaDefesa->getDenuncia();
            if (null !== $denuncia) {
                $instance->setIdDenuncia($denuncia->getId());
            }

            $arquivos = $denunciaDefesa->getArquivosDenunciaDefesa();
            if (!is_array($arquivos) && !empty($arquivos)) {
                $arquivos = $arquivos->toArray();
            }
            if (!empty($arquivos)) {
                $instance->setArquivos(array_map(static function (ArquivoDenunciaDefesa $arquivo) {
                    return ArquivoGenericoTO::newInstance([
                        'id' => $arquivo->getId(),
                        'nome' => $arquivo->getNome(),
                        'nomeFisico' => $arquivo->getNomeFisicoArquivo()
                    ]);
                }, $arquivos));
            } else {
                $instance->setArquivos([]);
            }
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
    public function getDataDefesa()
    {
        return $this->dataDefesa;
    }

    /**
     * @param \DateTime $dataDefesa
     */
    public function setDataDefesa($dataDefesa)
    {
        $this->dataDefesa = $dataDefesa;
    }

    /**
     * @return string
     */
    public function getDescricaoDefesa()
    {
        return $this->descricaoDefesa;
    }

    /**
     * @param $descricaoDefesa
     */
    public function setDescricaoDefesa($descricaoDefesa)
    {
        $this->descricaoDefesa = $descricaoDefesa;
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
     * @return string|null
     */
    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    /**
     * @param string|null $usuario
     */
    public function setUsuario(?string $usuario): void
    {
        $this->usuario = $usuario;
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
