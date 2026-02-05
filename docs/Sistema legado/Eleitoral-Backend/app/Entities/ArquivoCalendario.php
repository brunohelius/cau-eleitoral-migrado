<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 15/08/2019
 * Time: 09:45
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Arquivo Calendario'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoCalendarioRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_CALENDARIO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoCalendario extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_CALENDARIO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_calendario_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Calendario")
     * @ORM\JoinColumn(name="ID_CALENDARIO", referencedColumnName="ID_CALENDARIO", nullable=false)
     *
     * @var \App\Entities\Calendario
     */
    private $calendario;

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
     * Fábrica de instância de 'Arquivo Calendario'.
     *
     * @param array $data
     * @return ArquivoCalendario
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $arquivoCalendario = new ArquivoCalendario();

        if ($data != null) {
            $arquivoCalendario->setId(Utils::getValue('id', $data));
            $calendario = Calendario::newInstance(Utils::getValue('calendario', $data));
            $arquivoCalendario->setCalendario($calendario);
            $arquivoCalendario->setNome(Utils::getValue('nome', $data));
            $arquivoCalendario->setNomeFisico(Utils::getValue('nomeFisico', $data));
            $arquivoCalendario->setTamanho(Utils::getValue('tamanho', $data));
            $arquivoCalendario->setArquivo(Utils::getValue('arquivo', $data));
        }
        return $arquivoCalendario;
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
     * @return Calendario
     */
    public function getCalendario()
    {
        return $this->calendario;
    }

    /**
     * @param Calendario $calendario
     */
    public function setCalendario($calendario): void
    {
        $this->calendario = $calendario;
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