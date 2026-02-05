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
 * Entidade de representação de 'Arquivo Denúncia Admitida'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoDenunciaAdmitidaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_DENUNCIA_ADMITIDA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDenunciaAdmitida extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_DENUNCIA_ADMITIDA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_denuncia_admitida_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\DenunciaAdmitida")
     * @ORM\JoinColumn(name="ID_DENUNCIA_ADMITIDA", referencedColumnName="ID_DENUNCIA_ADMITIDA", nullable=false)
     *
     * @var \App\Entities\DenunciaAdmitida
     */
    private $denunciaAdmitida;

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
     * Fábrica de instância de 'Arquivo Denúncia Admitida'.
     *
     * @param array $data
     * @return \App\Entities\ArquivoDenunciaAdmitida
     */
    public static function newInstance($data = null)
    {
        $arquivoDenuncia = new ArquivoDenunciaAdmitida();
        if ($data != null) {
            $arquivoDenuncia->setId(Utils::getValue('id', $data));
            $arquivoDenuncia->setNome(Utils::getValue('nome', $data));
            $arquivoDenuncia->setNomeFisico(Utils::getValue('nomeFisico', $data));
            $arquivoDenuncia->setTamanho(Utils::getValue('tamanho', $data));
            $arquivoDenuncia->setArquivo(Utils::getValue('arquivo', $data));

            $denunciaAdmitida = Utils::getValue('denunciaAdmitida', $data);
            if (!empty($denunciaAdmitida)) {
                $arquivoDenuncia->setDenunciaAdmitida(DenunciaAdmitida::newInstance($denunciaAdmitida));
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
     * @return DenunciaAdmitida
     */
    public function getDenunciaAdmitida(): DenunciaAdmitida
    {
        return $this->denunciaAdmitida;
    }

    /**
     * @param DenunciaAdmitida $denunciaAdmitida
     */
    public function setDenunciaAdmitida(DenunciaAdmitida $denunciaAdmitida): void
    {
        $this->denunciaAdmitida = $denunciaAdmitida;
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