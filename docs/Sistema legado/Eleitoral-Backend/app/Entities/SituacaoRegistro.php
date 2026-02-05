<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 08/11/2019
 * Time: 17:02
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'ProfissionalRegistro'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\SituacaoRegistroRepository")
 * @ORM\Table(schema="public", name="tb_situacaoregistro")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class SituacaoRegistro extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="id", type="integer")
     * @ORM\SequenceGenerator(sequenceName="tb_situacaoregistro_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="codigo", type="string", nullable=false)
     * @var string
     */
    private $codigo;

    /**
     * @ORM\Column(name="descricao", type="string", nullable=false)
     * @var string
     */
    private $descricao;

    /**
     * @ORM\Column(name="situacaofinal", type="boolean")
     *
     * @var boolean
     */
    private $situacaoFinal;

    /**
     * @ORM\Column(name="bloqueiaacesso", type="boolean")
     *
     * @var boolean
     */
    private $bloqueiaAcesso;

    /**
     * @ORM\Column(name="situacaoativo", type="boolean")
     *
     * @var boolean
     */
    private $situacaoAtivo;

    /**
     * @ORM\Column(name="ativo", type="boolean")
     *
     * @var boolean
     */
    private $ativo;

    /**
     * @ORM\Column(name="disponivel", type="boolean")
     *
     * @var boolean
     */
    private $disponivel;

    /**
     * Fábrica de instância de 'SituacaoRegistro'.
     *
     * @param array $data
     * @return SituacaoRegistro
     */
    public static function newInstance($data = null)
    {
        $situacaoRegistro = new SituacaoRegistro();

        if (!empty($data)) {
            $situacaoRegistro->setId(Utils::getValue('id'));
            $situacaoRegistro->setDescricao(Utils::getValue('descricao'));
            $situacaoRegistro->setCodigo(Utils::getValue('codigo'));
            $situacaoRegistro->setDisponivel(Utils::getBooleanValue('disponivel'));
            $situacaoRegistro->setAtivo(Utils::getBooleanValue('ativo'));
            $situacaoRegistro->setBloqueiaAcesso(Utils::getBooleanValue('bloqueiaAcesso'));
            $situacaoRegistro->setSituacaoAtivo(Utils::getBooleanValue('situacaoAtivo'));
            $situacaoRegistro->setSituacaoFinal(Utils::getBooleanValue('situacaoFinal'));
        }
        return $situacaoRegistro;
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
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param string $codigo
     */
    public function setCodigo($codigo): void
    {
        $this->codigo = $codigo;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return bool
     */
    public function isSituacaoFinal()
    {
        return $this->situacaoFinal;
    }

    /**
     * @param bool $situacaoFinal
     */
    public function setSituacaoFinal($situacaoFinal): void
    {
        $this->situacaoFinal = $situacaoFinal;
    }

    /**
     * @return bool
     */
    public function isBloqueiaAcesso()
    {
        return $this->bloqueiaAcesso;
    }

    /**
     * @param bool $bloqueiaAcesso
     */
    public function setBloqueiaAcesso($bloqueiaAcesso): void
    {
        $this->bloqueiaAcesso = $bloqueiaAcesso;
    }

    /**
     * @return bool
     */
    public function isSituacaoAtivo()
    {
        return $this->situacaoAtivo;
    }

    /**
     * @param bool $situacaoativo
     */
    public function setSituacaoAtivo($situacaoativo): void
    {
        $this->situacaoAtivo = $situacaoativo;
    }

    /**
     * @return bool
     */
    public function isAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param bool $ativo
     */
    public function setAtivo($ativo): void
    {
        $this->ativo = $ativo;
    }

    /**
     * @return bool
     */
    public function isDisponivel()
    {
        return $this->disponivel;
    }

    /**
     * @param bool $disponivel
     */
    public function setDisponivel($disponivel): void
    {
        $this->disponivel = $disponivel;
    }
}