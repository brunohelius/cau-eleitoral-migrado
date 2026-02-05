<?php

namespace App\To;

use App\Entities\ArquivoDenuncia;
use App\Entities\Denuncia;
use App\Entities\TestemunhaDenuncia;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada a tabela de 'AcompanhamentoDenunciaTO'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class AcompanhamentoDenunciaTO
{

    /**
     * @var DenunciaViewTO
     */
    private $denuncia;

    /**
     * @var ArquivoDenunciaTO[]
     */
    private $arquivoDenuncia;

    /**
     * @var TestemunhaDenunciaTO[]
     */
    private $testemunhaDenuncia;

    /**
     * Retorna uma nova instância de 'DenunciaTO'.
     *
     * @param Denuncia $denuncia
     * @return self
     */
    public static function newInstanceFromEntity($denuncia = null)
    {
        $instance = new self;

        if (null !== $denuncia) {
            $instance->setDenuncia(DenunciaViewTO::newInstanceFromEntity($denuncia));

            $testemunhas = $denuncia->getTestemunhas() ?? [];
            if (!is_array($testemunhas)) {
                $testemunhas = $testemunhas->toArray();
            }

            $instance->setTestemunhaDenuncia(array_map(static function(TestemunhaDenuncia $testemunha) {
                return TestemunhaDenunciaTO::newInstanceFromEntity($testemunha);
            }, $testemunhas));

            $arquivos = $denuncia->getArquivoDenuncia() ?? [];
            if (!is_array($arquivos)) {
                $arquivos = $arquivos->toArray();
            }

            $instance->setArquivoDenuncia(array_map(static function(ArquivoDenuncia $arquivoDenuncia) {
                return ArquivoDenunciaTO::newInstanceFromEntity($arquivoDenuncia);
            }, $arquivos));
        }

        return $instance;
    }

    /**
     * @return DenunciaViewTO
     */
    public function getDenuncia(): DenunciaViewTO
    {
        return $this->denuncia;
    }

    /**
     * @param DenunciaViewTO $denuncia
     */
    public function setDenuncia(DenunciaViewTO $denuncia): void
    {
        $this->denuncia = $denuncia;
    }

    /**
     * @return ArquivoDenunciaTO[]
     */
    public function getArquivoDenuncia(): array
    {
        return $this->arquivoDenuncia;
    }

    /**
     * @param $arquivoDenuncia
     */
    public function setArquivoDenuncia($arquivoDenuncia): void
    {
        $this->arquivoDenuncia = $arquivoDenuncia;
    }

    /**
     * @return TestemunhaDenunciaTO[]
     */
    public function getTestemunhaDenuncia(): array
    {
        return $this->testemunhaDenuncia;
    }

    /**
     * @param $testemunhaDenuncia
     */
    public function setTestemunhaDenuncia($testemunhaDenuncia): void
    {
        $this->testemunhaDenuncia = $testemunhaDenuncia;
    }
}
