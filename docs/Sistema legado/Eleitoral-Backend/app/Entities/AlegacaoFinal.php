<?php
/*
 * Denuncia.php
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
 * Entidade de representação de 'Alegação Final do Encaminhamento'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\AlegacaoFinalRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ALEGACAO_FINAL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class AlegacaoFinal extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ALEGACAO_FINAL", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_alegacao_final_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_ALEGACAO_FINAL", type="string", length=2000, nullable=false)
     *
     * @var string
     */
    private $descricaoAlegacaoFinal;

    /**
     * @ORM\Column(name="DT_ALEGACAO_FINAL", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $dataHora;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\EncaminhamentoDenuncia")
     * @ORM\JoinColumn(name="ID_ENCAMINHAMENTO_DENUNCIA", referencedColumnName="ID_ENCAMINHAMENTO_DENUNCIA", nullable=false)
     * @var \App\Entities\EncaminhamentoDenuncia
     */
    private $encaminhamentoDenuncia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoAlegacaoFinal", mappedBy="alegacaoFinal", cascade={"persist"}, fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $arquivosAlegacaoFinal;

    /**
     * Fábrica de instância de 'Denuncia Defesa'.
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $alegacaoFinal = new AlegacaoFinal();

        if ($data != null) {
            $alegacaoFinal->setId(Utils::getValue('id', $data));
            $alegacaoFinal->setDescricaoAlegacaoFinal(Utils::getValue('descricaoAlegacaoFinal', $data));
            $alegacaoFinal->setDataHora(Utils::getValue('dataHora', $data));

            $alegacaoFinal->setEncaminhamentoDenuncia(
                EncaminhamentoDenuncia::newInstance(Utils::getValue('encaminhamentoDenuncia', $data))
            );
            
            $arquivosAlegacaoFinal = Utils::getValue('arquivosAlegacaoFinal', $data);
            if (!empty($arquivosAlegacaoFinal)) {
                $alegacaoFinal->setArquivosAlegacaoFinal(array_map(function ($arquivo) use ($alegacaoFinal) {
                    $arquivoAlegacao = ArquivoAlegacaoFinal::newInstance($arquivo);

                    /** @var ArquivoAlegacaoFinal $arquivoAlegacao */
                    $arquivoAlegacao->setAlegacaoFinal($alegacaoFinal);

                    return $arquivoAlegacao;
                }, $arquivosAlegacaoFinal));
            }
        }

        return $alegacaoFinal;
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
    public function setId(?int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDescricaoAlegacaoFinal()
    {
        return $this->descricaoAlegacaoFinal;
    }

    /**
     * @param string $descricaoAlegacaoFinal
     */
    public function setDescricaoAlegacaoFinal(string $descricaoAlegacaoFinal)
    {
        $this->descricaoAlegacaoFinal = $descricaoAlegacaoFinal;
    }

    /**
     * @return EncaminhamentoDenuncia
     */
    public function getEncaminhamentoDenuncia()
    {
        return $this->encaminhamentoDenuncia;
    }

    /**
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     */
    public function setEncaminhamentoDenuncia(EncaminhamentoDenuncia $encaminhamentoDenuncia)
    {
        $this->encaminhamentoDenuncia = $encaminhamentoDenuncia;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivosAlegacaoFinal()
    {
        return $this->arquivosAlegacaoFinal;
    }

    /**
     * @param array|ArrayCollection $arquivosAlegacaoFinal
     */
    public function setArquivosAlegacaoFinal($arquivosAlegacaoFinal)
    {
        $this->arquivosAlegacaoFinal = $arquivosAlegacaoFinal;
    }

    /**
     * @return \DateTime
     */
    public function getDataHora()
    {
        return $this->dataHora;
    }

    /**
     * @param \DateTime $dataHora
     */
    public function setDataHora($dataHora): void
    {
        if (is_string($dataHora)) {
            $dataHora = new \DateTime($dataHora);
        }
        $this->dataHora = $dataHora;
    }
}
