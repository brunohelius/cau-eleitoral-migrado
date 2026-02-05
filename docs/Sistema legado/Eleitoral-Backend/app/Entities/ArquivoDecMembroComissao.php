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
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'ArquivoDecMembroComissao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoDecMembroComissaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_DEC_MEMBRO_COMISSAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDecMembroComissao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_DEC_MEMBRO_COMISSAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_ARQUIVO_DEC_MEMBRO_COMISSAO_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\MembroComissao")
     * @ORM\JoinColumn(name="ID_MEMBRO_COMISSAO", referencedColumnName="ID_MEMBRO_COMISSAO", nullable=false)
     *
     * @var \App\Entities\MembroComissao
     */
    private $membroComissao;

    /**
     * @ORM\Column(name="NM_ARQUIVO", type="string", length=200, nullable=false)
     *
     * @var string
     */
    private $nome;

    /**
     * @ORM\Column(name="NM_FIS_ARQUIVO", type="string", length=200, nullable=false)
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
     * Fábrica de instância de 'ArquivoDecMembroComissao'.
     *
     * @param array $data
     * @return \App\Entities\ArquivoDecMembroComissao
     */
    public static function newInstance($data = null)
    {
        $ArquivoDecMembroComissao = new ArquivoDecMembroComissao();

        if ($data != null) {
            $ArquivoDecMembroComissao->setId(Utils::getValue('id', $data));
            
            $membroComissao = MembroComissao::newInstance(Utils::getValue('membroComissao', $data));
            $ArquivoDecMembroComissao->setMembroComissao($membroComissao);
            
            $ArquivoDecMembroComissao->setNome(Utils::getValue('nome', $data));
            $ArquivoDecMembroComissao->setNomeFisico(Utils::getValue('nomeFisico', $data));
            $ArquivoDecMembroComissao->setTamanho(Utils::getValue('tamanho', $data));
            $ArquivoDecMembroComissao->setArquivo(Utils::getValue('arquivo', $data));
        }
        return $ArquivoDecMembroComissao;
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
     * @return MembroComissao
     */
    public function getMembroComissao()
    {
        return $this->membroComissao;
    }

    /**
     * @param MembroComissao $membroComissao
     */
    public function setMembroComissao($membroComissao)
    {
        $this->membroComissao = $membroComissao;
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
    public function setNome($nome)
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
    public function setNomeFisico($nomeFisico)
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
    public function setArquivo($arquivo)
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
    public function setTamanho($tamanho)
    {
        $this->tamanho = $tamanho;
    }
}