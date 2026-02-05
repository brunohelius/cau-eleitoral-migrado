<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use App\Config\Constants;
use ArrayObject;
use stdClass;

/**
 * Entidade de representação de 'Plataforma Chapa Historico'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\PlataformaChapaHistoricoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_PLATAFORMA_CHAPA_HISTORICO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class PlataformaChapaHistorico extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_PLATAFORMA_CHAPA_HISTORICO_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_PLATAFORMA", type="text", nullable=false)
     *
     * @var string
     */
    private $descricaoPlataforma;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\ChapaEleicao", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="ID_CHAPA_ELEICAO", referencedColumnName="ID_CHAPA_ELEICAO", nullable=false)
     *
     * @var ChapaEleicao
     */
    private $chapaEleicao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Profissional")
     * @ORM\JoinColumn(name="ID_PROFISSIONAL_INCLUSAO", referencedColumnName="id", nullable=true)
     * @var Profissional
     */
    private $profissionalInclusaoPlataforma;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Usuario")
     * @ORM\JoinColumn(name="ID_USUARIO_INCLUSAO", referencedColumnName="id", nullable=true)
     * @var Usuario
     */
    private $usuarioInclusaoPlataforma;

    /**
     * @ORM\Column(name="DT_CADASTRO", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $dataCadastro;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\RedeSocialHistoricoPlataforma", mappedBy="plataformaChapaHistorico", fetch="EXTRA_LAZY")
     *
     * @var array|ArrayCollection
     */
    private $redesSociaisHistoricoPlataforma;

    /**
     * Fábrica de instância de 'Chapa Eleição'.
     *
     * @param array $data
     * @return PlataformaChapaHistorico
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $plataformaChapaHistorico = new self();

        if ($data != null) {
            $plataformaChapaHistorico->setId(Utils::getValue('id', $data));
            $plataformaChapaHistorico->setDescricaoPlataforma(Utils::getValue('descricaoPlataforma', $data));
            $plataformaChapaHistorico->setDataCadastro(Utils::getValue('dataCadastro', $data, Utils::getData()));

            $chapa = Utils::getValue('chapaEleicao', $data, []);
            if (!empty($chapa)) {
                $plataformaChapaHistorico->setChapaEleicao(ChapaEleicao::newInstance($chapa));
            }

            $profissionalInclusaoPlataforma = Utils::getValue('profissionalInclusaoPlataforma', $data, []);
            if (!empty($profissionalInclusaoPlataforma)) {
                $plataformaChapaHistorico->setProfissionalInclusaoPlataforma(Profissional::newInstance(
                    $profissionalInclusaoPlataforma
                ));
            }

            $usuarioInclusaoPlataforma = Utils::getValue('usuarioInclusaoPlataforma', $data, []);
            if (!empty($usuarioInclusaoPlataforma)) {
                $plataformaChapaHistorico->setUsuarioInclusaoPlataforma(Usuario::newInstance($usuarioInclusaoPlataforma));
            }

            $redeSociais = Utils::getValue('redesSociaisHistoricoPlataforma', $data, []);
            if (!empty($redeSociais)) {
                $plataformaChapaHistorico->setRedesSociaisHistoricoPlataforma(array_map(function ($redeSocial) {
                    return RedeSocialHistoricoPlataforma::newInstance($redeSocial);
                }, $redeSociais));
            }
        }

        return $plataformaChapaHistorico;
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
    public function getDescricaoPlataforma()
    {
        return $this->descricaoPlataforma;
    }

    /**
     * @param string $descricaoPlataforma
     */
    public function setDescricaoPlataforma($descricaoPlataforma): void
    {
        $this->descricaoPlataforma = $descricaoPlataforma;
    }

    /**
     * @return ChapaEleicao
     */
    public function getChapaEleicao()
    {
        return $this->chapaEleicao;
    }

    /**
     * @param ChapaEleicao $chapaEleicao
     */
    public function setChapaEleicao($chapaEleicao): void
    {
        $this->chapaEleicao = $chapaEleicao;
    }

    /**
     * @return Profissional
     */
    public function getProfissionalInclusaoPlataforma()
    {
        return $this->profissionalInclusaoPlataforma;
    }

    /**
     * @param Profissional $profissionalInclusaoPlataforma
     */
    public function setProfissionalInclusaoPlataforma($profissionalInclusaoPlataforma): void
    {
        $this->profissionalInclusaoPlataforma = $profissionalInclusaoPlataforma;
    }

    /**
     * @return Usuario
     */
    public function getUsuarioInclusaoPlataforma()
    {
        return $this->usuarioInclusaoPlataforma;
    }

    /**
     * @param Usuario $usuarioInclusaoPlataforma
     */
    public function setUsuarioInclusaoPlataforma($usuarioInclusaoPlataforma): void
    {
        $this->usuarioInclusaoPlataforma = $usuarioInclusaoPlataforma;
    }

    /**
     * @return DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @param DateTime $dataCadastro
     */
    public function setDataCadastro($dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getRedesSociaisHistoricoPlataforma()
    {
        return $this->redesSociaisHistoricoPlataforma;
    }

    /**
     * @param array|ArrayCollection $redesSociaisHistoricoPlataforma
     */
    public function setRedesSociaisHistoricoPlataforma($redesSociaisHistoricoPlataforma): void
    {
        $this->redesSociaisHistoricoPlataforma = $redesSociaisHistoricoPlataforma;
    }

}
