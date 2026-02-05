<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'Documento Comprobatório da Sintese de Currículo'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DocumentoComprobatorioSinteseCurriculoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DOC_COMPROB_SINTESE_CURRICULO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DocumentoComprobatorioSinteseCurriculo extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DOC_COMPROB_SINTESE_CURRICULO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_doc_comprob_sintese_curriculo_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\MembroChapa")
     * @ORM\JoinColumn(name="ID_MEMBRO_CHAPA", referencedColumnName="ID_MEMBRO_CHAPA", nullable=false)
     *
     * @var \App\Entities\MembroChapa
     */
    private $membroChapa;

    /**
     * @ORM\Column(name="NM_ARQUIVO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $nomeArquivo;


    /**
     * @ORM\Column(name="TP_DOC_COMPROB_SINTESE_CURRICULO", type="integer", nullable=false)
     *
     * @var integer
     */
    private $tipoDocumentoComprobatorioSinteseCurriculo;

    /**
     * Fábrica de instância de 'Documento Comprobatório da Sintese de Currículo'.
     *
     * @param array $data
     *
     * @return DocumentoComprobatorioSinteseCurriculo
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setNomeArquivo(Utils::getValue('nomeArquivo', $data));
            $instance->setTipoDocumentoComprobatorioSinteseCurriculo(
                Utils::getValue('tipoDocumentoComprobatorioSinteseCurriculo', $data));

            $membroChapa = Utils::getValue('membroChapa', $data);
            if (!empty($membroChapa)) {
                $instance->setMembroChapa(MembroChapa::newInstance($membroChapa));
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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return MembroChapa
     */
    public function getMembroChapa()
    {
        return $this->membroChapa;
    }

    /**
     * @param MembroChapa $membroChapa
     */
    public function setMembroChapa(MembroChapa $membroChapa): void
    {
        $this->membroChapa = $membroChapa;
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
    public function setNomeArquivo($nomeArquivo): void
    {
        $this->nomeArquivo = $nomeArquivo;
    }

    /**
     * @return int
     */
    public function getTipoDocumentoComprobatorioSinteseCurriculo(): int
    {
        return $this->tipoDocumentoComprobatorioSinteseCurriculo;
    }

    /**
     * @param int $tipoDocumentoComprobatorioSinteseCurriculo
     */
    public function setTipoDocumentoComprobatorioSinteseCurriculo(int $tipoDocumentoComprobatorioSinteseCurriculo): void
    {
        $this->tipoDocumentoComprobatorioSinteseCurriculo = $tipoDocumentoComprobatorioSinteseCurriculo;
    }
}
