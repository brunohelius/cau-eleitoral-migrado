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
 * Entidade de representação de 'Arquivo Denúncia Inadmitida'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoDenunciaInadmitidaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_DENUNCIA_INADMITIDA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDenunciaInadmitida extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_DENUNCIA_INADMITIDA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_denuncia_inadmitida_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\DenunciaInadmitida")
     * @ORM\JoinColumn(name="ID_DENUNCIA_INADMITIDA", referencedColumnName="ID_DENUNCIA_INADMITIDA", nullable=false)
     *
     * @var \App\Entities\DenunciaInadmitida
     */
    private $denunciaInadmitida;

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
     * Fábrica de instância de 'Arquivo Denúncia Inadmitida'.
     *
     * @param array $data
     * @return \App\Entities\ArquivoDenunciaInadmitida
     */
    public static function newInstance($data = null)
    {
        $arquivoDenunciaInadmitida = new ArquivoDenunciaInadmitida();
        if ($data != null) {
            $arquivoDenunciaInadmitida->setId(Utils::getValue('id', $data));
            $arquivoDenunciaInadmitida->setNome(Utils::getValue('nome', $data));
            $arquivoDenunciaInadmitida->setNomeFisico(Utils::getValue('nomeFisico', $data));
            $arquivoDenunciaInadmitida->setTamanho(Utils::getValue('tamanho', $data));
            $arquivoDenunciaInadmitida->setArquivo(Utils::getValue('arquivo', $data));

            $denunciaInadmitida = Utils::getValue('denunciaInadmitida', $data);
            if (!empty($denunciaInadmitida)) {
                $arquivoDenunciaInadmitida->setDenunciaInadmitida(DenunciaInadmitida::newInstance($denunciaInadmitida));
            }
        }
        return $arquivoDenunciaInadmitida;
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
     * @return DenunciaInadmitida
     */
    public function getDenunciaInadmitida(): DenunciaInadmitida
    {
        return $this->denunciaInadmitida;
    }

    /**
     * @param DenunciaInadmitida $denunciaInadmitida
     */
    public function setDenunciaInadmitida(DenunciaInadmitida $denunciaInadmitida): void
    {
        $this->denunciaInadmitida = $denunciaInadmitida;
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