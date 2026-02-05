<?php


namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'RedeSocialHistoricoPlataforma'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\RedeSocialHistoricoPlataformaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_REDE_SOCIAL_HISTORICO_PLATAFORMA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class RedeSocialHistoricoPlataforma extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.TB_REDE_SOCIAL_HISTORICO_PLATAFORMA_ID_SEQ", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DESCRICAO", type="string", length=100, nullable=false)
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
     * @ORM\ManyToOne(targetEntity="App\Entities\PlataformaChapaHistorico")
     * @ORM\JoinColumn(name="ID_PLATAFORMA_CHAPA_HISTORICO", referencedColumnName="ID", nullable=false)
     *
     * @var PlataformaChapaHistorico
     */
    private $plataformaChapaHistorico;

    /**
     * @ORM\Column(name="ST_ATIVO", type="boolean", nullable=false, options={"default":true})
     *
     * @var bool
     */
    private $isAtivo;

    /**
     * Fábrica de instância de 'RedeSocialHistoricoPlataforma'.
     *
     * @param array $data
     * @return RedeSocialHistoricoPlataforma
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $redeSocialHistoricoPlataforma = new RedeSocialHistoricoPlataforma();

        if ($data != null) {
            $redeSocialHistoricoPlataforma->setId(Utils::getValue('id', $data));
            $redeSocialHistoricoPlataforma->setDescricao(Utils::getValue('descricao', $data));
            $redeSocialHistoricoPlataforma->setIsAtivo(Utils::getBooleanValue('isAtivo', $data));

            $tipoRedeSocial = Utils::getValue('tipoRedeSocial', $data);
            if (!empty($tipoRedeSocial)) {
                $redeSocialHistoricoPlataforma->setTipoRedeSocial(TipoRedeSocial::newInstance($tipoRedeSocial));
            }

            $plataformaChapaHistorico = Utils::getValue('plataformaChapaHistorico', $data);
            if (!empty($plataformaChapaHistorico)) {
                $redeSocialHistoricoPlataforma->setPlataformaChapaHistorico(PlataformaChapaHistorico::newInstance(
                    $plataformaChapaHistorico
                ));
            }

        }

        return $redeSocialHistoricoPlataforma;
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
     * @return PlataformaChapaHistorico
     */
    public function getPlataformaChapaHistorico()
    {
        return $this->plataformaChapaHistorico;
    }

    /**
     * @param PlataformaChapaHistorico $plataformaChapaHistorico
     */
    public function setPlataformaChapaHistorico($plataformaChapaHistorico): void
    {
        $this->plataformaChapaHistorico = $plataformaChapaHistorico;
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
