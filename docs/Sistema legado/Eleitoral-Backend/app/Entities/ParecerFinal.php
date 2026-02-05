<?php
/*
 * ParecerFinal.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entidade de representação de 'ParecerFinal'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ParecerFinalRepository")
 * @ORM\Table(schema="eleitoral", name="TB_PARECER_FINAL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ParecerFinal extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_PARECER_FINAL", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_parecer_final_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="QT_DIAS_SUSPENSAO_PROPAGANDA", type="integer", length=999, nullable=true)
     *
     * @var integer
     */
    private $quantidadeDiasSuspensaoPropaganda;

    /**
     * @ORM\Column(name="MULTA", type="boolean", nullable=true)
     *
     * @var boolean
     */
    private $multa;

    /**
     * @ORM\Column(name="VL_PERCENTUAL_MULTA", type="integer", length=999, nullable=true)
     *
     * @var integer
     */
    private $valorPercentualMulta;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\EncaminhamentoDenuncia", inversedBy="parecerFinal", cascade={"persist"})
     * @ORM\JoinColumn(name="ID_ENCAMINHAMENTO_DENUNCIA", referencedColumnName="ID_ENCAMINHAMENTO_DENUNCIA", nullable=false)
     *
     * @var EncaminhamentoDenuncia
     */
    private $encaminhamentoDenuncia;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\TipoJulgamento")
     * @ORM\JoinColumn(name="ID_TIPO_JULGAMENTO", referencedColumnName="ID_TIPO_JULGAMENTO", nullable=false)
     *
     * @var TipoJulgamento
     */
    private $tipoJulgamento;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\TipoSentencaJulgamento")
     * @ORM\JoinColumn(name="ID_TIPO_SENTENCA_JULGAMENTO", referencedColumnName="ID_TIPO_SENTENCA_JULGAMENTO", nullable=false)
     *
     * @var TipoSentencaJulgamento
     */
    private $tipoSentencaJulgamento;

    /**
     * Fábrica de instância de 'JulgamentoDenuncia'.
     *
     * @param null $data
     *
     * @return self
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data !== null) {
            $instance->setId(Utils::getValue('id', $data));
            $instance->setMulta(Utils::getBooleanValue('multa', $data, false));
            $instance->setValorPercentualMulta(Utils::getValue('vlPercentualMulta', $data));
            $instance->setQuantidadeDiasSuspensaoPropaganda(Utils::getValue('qtDiasSuspensaoPropaganda', $data));

            $encaminhametoDenuncia = Utils::getValue('encaminhamentoDenuncia', $data);
            if (!empty($encaminhametoDenuncia)) {
                $instance->setEncaminhamentoDenuncia(EncaminhamentoDenuncia::newInstance($encaminhametoDenuncia));
            }

            $tipoJulgamento = Utils::getValue('tipoJulgamento', $data);
            if (!empty($tipoJulgamento)) {
                $instance->setTipoJulgamento(TipoJulgamento::newInstance($tipoJulgamento));
            }

            $tipoSentencaJulgamento = Utils::getValue('tipoSentencaJulgamento', $data);
            if (!empty($tipoSentencaJulgamento)) {
                $instance->setTipoSentencaJulgamento(TipoSentencaJulgamento::newInstance($tipoSentencaJulgamento));
            }

        }

        return $instance;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getQuantidadeDiasSuspensaoPropaganda(): ?int
    {
        return $this->quantidadeDiasSuspensaoPropaganda;
    }

    /**
     * @param int $quantidadeDiasSuspensaoPropaganda
     */
    public function setQuantidadeDiasSuspensaoPropaganda(?int $quantidadeDiasSuspensaoPropaganda): void
    {
        $this->quantidadeDiasSuspensaoPropaganda = $quantidadeDiasSuspensaoPropaganda;
    }

    /**
     * @return bool
     */
    public function isMulta(): ?bool
    {
        return $this->multa;
    }

    /**
     * @param bool $multa
     */
    public function setMulta(?bool $multa): void
    {
        $this->multa = $multa;
    }

    /**
     * @return int
     */
    public function getValorPercentualMulta(): ?int
    {
        return $this->valorPercentualMulta;
    }

    /**
     * @param int $valorPercentualMulta
     */
    public function setValorPercentualMulta(?int $valorPercentualMulta): void
    {
        $this->valorPercentualMulta = $valorPercentualMulta;
    }

    /**
     * @return EncaminhamentoDenuncia
     */
    public function getEncaminhamentoDenuncia(): EncaminhamentoDenuncia
    {
        return $this->encaminhamentoDenuncia;
    }

    /**
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     */
    public function setEncaminhamentoDenuncia(EncaminhamentoDenuncia $encaminhamentoDenuncia): void
    {
        $this->encaminhamentoDenuncia = $encaminhamentoDenuncia;
    }

    /**
     * @return TipoJulgamento
     */
    public function getTipoJulgamento(): TipoJulgamento
    {
        return $this->tipoJulgamento;
    }

    /**
     * @param TipoJulgamento $tipoJulgamento
     */
    public function setTipoJulgamento(TipoJulgamento $tipoJulgamento): void
    {
        $this->tipoJulgamento = $tipoJulgamento;
    }

    /**
     * @return TipoSentencaJulgamento
     */
    public function getTipoSentencaJulgamento(): ?TipoSentencaJulgamento
    {
        return $this->tipoSentencaJulgamento;
    }

    /**
     * @param TipoSentencaJulgamento $tipoSentencaJulgamento
     */
    public function setTipoSentencaJulgamento(?TipoSentencaJulgamento $tipoSentencaJulgamento): void
    {
        $this->tipoSentencaJulgamento = $tipoSentencaJulgamento;
    }

}
