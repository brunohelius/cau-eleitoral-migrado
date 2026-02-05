<?php
/*
 * DenunciaProvas.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'Denuncia Provas'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DenunciaProvasRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DENUNCIA_PROVAS")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DenunciaProvas extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DENUNCIA_PROVAS", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_denuncia_provas_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     * @var Denuncia
     */
    private $denuncia;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\EncaminhamentoDenuncia")
     * @ORM\JoinColumn(name="ID_ENCAMINHAMENTO_DENUNCIA", referencedColumnName="ID_ENCAMINHAMENTO_DENUNCIA", nullable=false)
     * @var EncaminhamentoDenuncia
     */
    private $encaminhamentoDenuncia;

    /**
     * @ORM\Column(name="DS_PROVAS_APRESENTADAS", type="string", length=2000, nullable=false)
     *
     * @var string
     */
    private $descricaoProvasApresentadas;

    /**
     * @ORM\Column(name="DT_PROVAS", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $dataProva;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoDenunciaProvas", mappedBy="denunciaProvas", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $arquivosDenunciaProvas;

    /**
     * Fábrica de instância de 'Denuncia Provas'.
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $denunciaProvas = new DenunciaProvas();

        if ($data != null) {
            $denunciaProvas->setId(Utils::getValue('id', $data));
            $denunciaProvas->setDescricaoProvasApresentadas(Utils::getValue('descricaoProvasApresentadas', $data));

            $dataHora = Utils::getValue('dataProva', $data);
            if (!empty($dataHora)) {
                $denunciaProvas->setDataProva($dataHora);
            }

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $denunciaProvas->setDenuncia(Denuncia::newInstance($denuncia));
            }

            $encaminhamentoDenuncia = Utils::getValue('encaminhamentoDenuncia', $data);
            if (!empty($encaminhamentoDenuncia)) {
                $denunciaProvas->setEncaminhamentoDenuncia(EncaminhamentoDenuncia::newInstance($encaminhamentoDenuncia));
            }

            $arquivosDenunciaProvas = Utils::getValue('arquivosDenunciaProvas', $data);
            if (!empty($arquivosDenunciaProvas)) {
                foreach ($arquivosDenunciaProvas as $arquivoDenunciaProva) {
                    $denunciaProvas->adicionarArquivoDenunciaProvas(ArquivoDenunciaProvas::newInstance($arquivoDenunciaProva));
                }
            }
        }

        return $denunciaProvas;
    }

    /**
     * Adiciona o 'ArquivoDenunciaProvas' à sua respectiva coleção.
     *
     * @param ArquivoDenunciaProvas $arquivoDenunciaProvas
     */
    private function adicionarArquivoDenunciaProvas(ArquivoDenunciaProvas $arquivoDenunciaProvas)
    {
        if (empty($this->getArquivosDenunciaProvas())) {
            $this->setArquivosDenunciaProvas(new ArrayCollection());
        }

        if (!empty($arquivoDenunciaProvas)) {
            $arquivoDenunciaProvas->setDenunciaProvas($this);
            $this->getArquivosDenunciaProvas()->add($arquivoDenunciaProvas);
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
     * @return Denuncia
     */
    public function getDenuncia()
    {
        return $this->denuncia;
    }

    /**
     * @param $denuncia
     */
    public function setDenuncia($denuncia)
    {
        $this->denuncia = $denuncia;
    }

    /**
     * @return EncaminhamentoDenuncia
     */
    public function getEncaminhamentoDenuncia()
    {
        return $this->encaminhamentoDenuncia;
    }

    /**
     * @param $encaminhamentoDenuncia
     */
    public function setEncaminhamentoDenuncia($encaminhamentoDenuncia)
    {
        $this->encaminhamentoDenuncia = $encaminhamentoDenuncia;
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
     * @return \DateTime
     */
    public function getDataProva()
    {
        return $this->dataProva;
    }

    /**
     * @param $dataProva
     */
    public function setDataProva($dataProva)
    {
        $this->dataProva = $dataProva;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivosDenunciaProvas()
    {
        return $this->arquivosDenunciaProvas;
    }

    /**
     * @param array|ArrayCollection $arquivosDenunciaProvas
     */
    public function setArquivosDenunciaProvas($arquivosDenunciaProvas)
    {
        $this->arquivosDenunciaProvas = $arquivosDenunciaProvas;
    }

    /**
     * Método para retirar os binários de arquivos
     */
    public function removerFiles()
    {
        if (!empty($this->arquivosDenunciaProvas)) {
            foreach ($this->arquivosDenunciaProvas as $arquivo) {
                $arquivo->setArquivo(null);
            }
        }
    }
}
