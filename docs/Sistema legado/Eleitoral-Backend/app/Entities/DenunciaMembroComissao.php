<?php
/*
 * DenunciaChapa.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Config\Constants;
use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade de representação de 'Denuncia Membro Comissao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DenunciaMembroComissaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DENUNCIA_MEMBRO_COMISSAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DenunciaMembroComissao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DENUNCIA_MEMBRO_COMISSAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_denuncia_membro_comissao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     *
     * @var \App\Entities\Denuncia
     */
    private $denuncia;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\MembroComissao")
     * @ORM\JoinColumn(name="ID_MEMBRO_COMISSAO", referencedColumnName="ID_MEMBRO_COMISSAO", nullable=false)
     *
     * @var \App\Entities\MembroComissao
     */
    private $membroComissao;

    /**
     * Fábrica de instância de Denuncia Membro Comissao'.
     *
     * @param array $data
     *
     * @return \App\Entities\DenunciaMembroComissao
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $denunciaMembroComissao = new DenunciaMembroComissao();

        if ($data != null) {
            $denunciaMembroComissao->setId(Utils::getValue('id', $data));

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $denunciaMembroComissao->setDenuncia(Denuncia::newInstance($denuncia));
            }

            $membroComissao = Utils::getValue('membroComissao', $data);
            if (!empty($membroComissao)) {
                $denunciaMembroComissao->setMembroComissao(MembroComissao::newInstance($membroComissao));
            }
        }
        return $denunciaMembroComissao;
    }

    /**
     * @return  integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  integer  $id
     */
    public function setId($id)
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
     * @param Denuncia  $denuncia
     */
    public function setDenuncia(Denuncia $denuncia)
    {
        $this->denuncia = $denuncia;
    }

    /**
     * @return  MembroComissao
     */
    public function getMembroComissao()
    {
        return $this->membroComissao;
    }

    /**
     * @param MembroComissao  $membroComissao
     */
    public function setMembroComissao(MembroComissao $membroComissao)
    {
        $this->membroComissao = $membroComissao;
    }
}
?>