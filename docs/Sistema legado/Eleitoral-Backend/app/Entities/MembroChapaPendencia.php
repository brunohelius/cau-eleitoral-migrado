<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade de representação de 'Pendencias do Membro da Chapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\MembroChapaPendenciaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_MEMBRO_CHAPA_PENDENCIA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class MembroChapaPendencia extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_MEMBRO_CHAPA_PENDENCIA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_membro_chapa_pendencia_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\MembroChapa")
     * @ORM\JoinColumn(name="ID_MEMBRO_CHAPA", referencedColumnName="ID_MEMBRO_CHAPA")
     *
     * @var \App\Entities\MembroChapa
     */
    private $membroChapa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\TipoPendencia")
     * @ORM\JoinColumn(name="ID_TP_PENDENCIA", referencedColumnName="ID_TP_PENDENCIA")
     *
     * @var \App\Entities\TipoPendencia
     */
    private $tipoPendencia;

    /**
     * Fábrica de instância de 'Pendencias do Membro da Chapa'.
     *
     * @param array $data
     *
     * @return MembroChapaPendencia
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data != null) {
            $instance->setId(Utils::getValue('id', $data));

            $membroChapa = Utils::getValue('membroChapa', $data);
            if (!empty($membroChapa)) {
                $instance->setMembroChapa(MembroChapa::newInstance($membroChapa));
            }

            $tipoPendencia = Utils::getValue('tipoPendencia', $data);
            if (!empty($tipoPendencia)) {
                $instance->setTipoPendencia(TipoPendencia::newInstance($tipoPendencia));
            }
        }
        return $instance;
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
     * @return MembroChapa
     */
    public function getMembroChapa()
    {
        return $this->membroChapa;
    }

    /**
     * @param MembroChapa $membroChapa
     */
    public function setMembroChapa(MembroChapa $membroChapa): void
    {
        $this->membroChapa = $membroChapa;
    }

    /**
     * @return TipoPendencia
     */
    public function getTipoPendencia()
    {
        return $this->tipoPendencia;
    }

    /**
     * @param TipoPendencia $tipoPendencia
     */
    public function setTipoPendencia(TipoPendencia $tipoPendencia): void
    {
        $this->tipoPendencia = $tipoPendencia;
    }
}
