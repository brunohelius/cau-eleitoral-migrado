<?php
/*
 * Denuncia.php
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
 * Entidade de representação de 'Denuncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DenunciaDefesaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DENUNCIA_DEFESA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DenunciaDefesa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DENUNCIA_DEFESA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_denuncia_defesa_id_seq", initialValue=1, allocationSize=1)
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
     * @ORM\Column(name="DS_DEFESA", type="string", length=2000, nullable=false)
     *
     * @var string
     */
    private $descricaoDefesa;

    /**
     * @ORM\Column(name="DT_DEFESA", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $dataDefesa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoDenunciaDefesa", mappedBy="denunciaDefesa", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $arquivosDenunciaDefesa;

    /**
     * Fábrica de instância de 'Denuncia Defesa'.
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $denunciaDefesa = new DenunciaDefesa();

        if ($data != null) {
            $denunciaDefesa->setId(Utils::getValue('id', $data));
            $denunciaDefesa->setDescricaoDefesa(Utils::getValue('descricaoDefesa', $data));

            $dataHora = Utils::getValue('dataDefesa', $data);
            if (!empty($dataHora)) {
                $denunciaDefesa->setDataDefesa($dataHora);
            }

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $denunciaDefesa->setDenuncia(Denuncia::newInstance($denuncia));
            }

            $arquivosDenunciaDefesa = Utils::getValue('arquivoDenunciaDefesa', $data);
            if (!empty($arquivosDenunciaDefesa)) {
                foreach ($arquivosDenunciaDefesa as $arquivoDenunciaDefesa) {
                    $denunciaDefesa->adicionarArquivoDenunciaDefesa(ArquivoDenunciaDefesa::newInstance($arquivoDenunciaDefesa));
                }
            }
        }

        return $denunciaDefesa;
    }

    /**
     * Adiciona o 'ArquivoDenunciaDefesa' à sua respectiva coleção.
     *
     * @param ArquivoDenunciaDefesa $arquivoDenunciaDefesa
     */
    private function adicionarArquivoDenunciaDefesa(ArquivoDenunciaDefesa $arquivoDenunciaDefesa)
    {
        if (empty($this->getArquivosDenunciaDefesa())) {
            $this->setArquivosDenunciaDefesa(new ArrayCollection());
        }

        if (!empty($arquivoDenunciaDefesa)) {
            $arquivoDenunciaDefesa->setDenunciaDefesa($this);
            $this->getArquivosDenunciaDefesa()->add($arquivoDenunciaDefesa);
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
     * @return string
     */
    public function getDescricaoDefesa()
    {
        return $this->descricaoDefesa;
    }

    /**
     * @param string $descricaoDefesa
     */
    public function setDescricaoDefesa($descricaoDefesa)
    {
        $this->descricaoDefesa = $descricaoDefesa;
    }

    /**
     * @return \DateTime
     */
    public function getDataDefesa()
    {
        return $this->dataDefesa;
    }

    /**
     * @param $dataDefesa
     */
    public function setDataDefesa($dataDefesa)
    {
        $this->dataDefesa = $dataDefesa;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivosDenunciaDefesa()
    {
        return $this->arquivosDenunciaDefesa;
    }

    /**
     * @param array|ArrayCollection $arquivosDenunciaDefesa
     */
    public function setArquivosDenunciaDefesa($arquivosDenunciaDefesa): void
    {
        $this->arquivosDenunciaDefesa = $arquivosDenunciaDefesa;
    }

    /**
     * Método para retirar os binários de arquivos
     */
    public function removerFiles()
    {
        if (!empty($this->arquivosDenunciaDefesa)) {
            foreach ($this->arquivosDenunciaDefesa as $arquivo) {
                $arquivo->setArquivo(null);
            }
        }
    }
}
