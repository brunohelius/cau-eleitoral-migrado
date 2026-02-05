<?php


namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'Rede Social Chapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\RedeSocialChapaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_REDE_SOCIAL_CHAPA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class RedeSocialChapa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_REDE_SOCIAL_CHAPA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_rede_social_chapa_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_REDE_SOCIAL_CHAPA", type="string", length=100, nullable=false)
     *
     * @var string
     */
    private $descricao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoRedeSocial")
     * @ORM\JoinColumn(name="ID_TP_REDE_SOCIAL", referencedColumnName="ID_TP_REDE_SOCIAL", nullable=false)
     *
     * @var \App\Entities\TipoRedeSocial
     */
    private $tipoRedeSocial;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\ChapaEleicao")
     * @ORM\JoinColumn(name="ID_CHAPA_ELEICAO", referencedColumnName="ID_CHAPA_ELEICAO", nullable=false)
     *
     * @var \App\Entities\ChapaEleicao
     */
    private $chapaEleicao;

    /**
     * @ORM\Column(name="ST_ATIVO", type="boolean", nullable=false, options={"default":true})
     *
     * @var bool
     */
    private $isAtivo;

    /**
     * Fábrica de instância de 'Rede Social Chapa'.
     *
     * @param array $data
     * @return \App\Entities\RedeSocialChapa
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $redeSocialChapa = new RedeSocialChapa();

        if ($data != null) {
            $redeSocialChapa->setId(Utils::getValue('id', $data));
            $redeSocialChapa->setDescricao(Utils::getValue('descricao', $data));
            $redeSocialChapa->setIsAtivo(Utils::getValue('isAtivo', $data));

            $tipoRedeSocial = TipoRedeSocial::newInstance(Utils::getValue('tipoRedeSocial', $data));
            $redeSocialChapa->setTipoRedeSocial($tipoRedeSocial);

            $chapaEleicao = ChapaEleicao::newInstance(Utils::getValue('chapaEleicao', $data));
            $redeSocialChapa->setChapaEleicao($chapaEleicao);
        }

        return $redeSocialChapa;
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
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return TipoRedeSocial
     */
    public function getTipoRedeSocial()
    {
        return $this->tipoRedeSocial;
    }

    /**
     * @param TipoRedeSocial $tipoRedeSocial
     */
    public function setTipoRedeSocial(TipoRedeSocial $tipoRedeSocial): void
    {
        $this->tipoRedeSocial = $tipoRedeSocial;
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
    public function setChapaEleicao(ChapaEleicao $chapaEleicao): void
    {
        $this->chapaEleicao = $chapaEleicao;
    }

    /**
     * @return bool
     */
    public function isAtivo()
    {
        return $this->isAtivo;
    }

    /**
     * @param bool $isAtivo
     */
    public function setIsAtivo($isAtivo): void
    {
        $this->isAtivo = $isAtivo;
    }
}
