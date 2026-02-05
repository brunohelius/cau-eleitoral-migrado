<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 27/02/2020
 * Time: 14:00
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Arquivo Denúncia'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoDenunciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_DENUNCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDenuncia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_DENUNCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_denuncia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\Denuncia
     */
    private $denuncia;

    /**
     * @ORM\Column(name="NM_ARQUIVO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $nome;

    /**
     * @ORM\Column(name="NM_FIS_ARQUIVO", type="string", length=20, nullable=false)
     *
     * @var string
     */
    private $nomeFisico;

    /**
     * Transient
     *
     * @var mixed
     */
    private $arquivo;

    /**
     * Transient
     *
     * @var mixed
     */
    private $tamanho;

    /**
     * Fábrica de instância de 'Arquivo Denúncia'.
     *
     * @param array $data
     * @return \App\Entities\ArquivoDenuncia
     */
    public static function newInstance($data = null)
    {
        $arquivoDenuncia = new ArquivoDenuncia();
        if ($data != null) {
            $arquivoDenuncia->setId(Utils::getValue('id', $data));
            $arquivoDenuncia->setNome(Utils::getValue('nome', $data));
            $arquivoDenuncia->setNomeFisico(Utils::getValue('nomeFisico', $data));
            $arquivoDenuncia->setTamanho(Utils::getValue('tamanho', $data));
            $arquivoDenuncia->setArquivo(Utils::getValue('arquivo', $data));
            
            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $arquivoDenuncia->setDenuncia(Denuncia::newInstance($denuncia));
            }
        }
        return $arquivoDenuncia;
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
     * @return Denuncia
     */
    public function getDenuncia()
    {
        return $this->denuncia;
    }

    /**
     * @param Denuncia $denuncia
     */
    public function setDenuncia($denuncia): void
    {
        $this->denuncia = $denuncia;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome($nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return string
     */
    public function getNomeFisico()
    {
        return $this->nomeFisico;
    }

    /**
     * @param string $nomeFisico
     */
    public function setNomeFisico($nomeFisico): void
    {
        $this->nomeFisico = $nomeFisico;
    }

    /**
     * @return mixed
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * @param mixed $arquivo
     */
    public function setArquivo($arquivo): void
    {
        $this->arquivo = $arquivo;
    }

    /**
     * @return mixed
     */
    public function getTamanho()
    {
        return $this->tamanho;
    }

    /**
     * @param mixed $tamanho
     */
    public function setTamanho($tamanho): void
    {
        $this->tamanho = $tamanho;
    }
}