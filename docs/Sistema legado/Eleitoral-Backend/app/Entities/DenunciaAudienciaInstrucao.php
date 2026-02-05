<?php
/*
 * DenunciaAudienciaInstrucao.php
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
 * Entidade de representação de 'DenunciaAudienciaInstrucao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\DenunciaAudienciaInstrucaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_DENUNCIA_AUDIENCIA_INSTRUCAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class DenunciaAudienciaInstrucao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_DENUNCIA_AUDIENCIA_INSTRUCAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_denuncia_audiencia_instrucao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Denuncia")
     * @ORM\JoinColumn(name="ID_DENUNCIA", referencedColumnName="ID_DENUNCIA", nullable=false)
     * @var Denuncia
     */
    private $denuncia;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\EncaminhamentoDenuncia")
     * @ORM\JoinColumn(name="ID_ENCAMINHAMENTO_DENUNCIA", referencedColumnName="ID_ENCAMINHAMENTO_DENUNCIA", nullable=false)
     * @var EncaminhamentoDenuncia
     */
    private $encaminhamentoDenuncia;

    /**
     * @ORM\Column(name="DS_DENUNCIA_AUDIENCIA_INSTRUCAO", type="string", length=2000, nullable=false)
     *
     * @var string
     */
    private $descricaoDenunciaAudienciaInstrucao;

    /**
     * @ORM\Column(name="DT_AUDIENCIA_INSTRUCAO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $dataAudienciaInstrucao;

    /**
     * @ORM\Column(name="DT_CADASTRO", type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    private $dataCadastro;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ArquivoDenunciaAudienciaInstrucao", mappedBy="denunciaAudienciaInstrucao", fetch="EXTRA_LAZY")
     *
     * @var array|\Doctrine\Common\Collections\ArrayCollection
     */
    private $arquivosDenunciaAudienciaInstrucao;

    /**
     * Fábrica de instância de 'DenunciaAudienciaInstrucao'.
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $denunciaAudienciaInstrucao = new DenunciaAudienciaInstrucao();

        if ($data != null) {
            $denunciaAudienciaInstrucao->setId(Utils::getValue('id', $data));
            $denunciaAudienciaInstrucao->setDescricaoDenunciaAudienciaInstrucao(
                Utils::getValue('descricaoDenunciaAudienciaInstrucao', $data)
            );

            $dataHora = Utils::getValue('dataAudienciaInstrucao', $data);
            if (!empty($dataHora)) {
                $denunciaAudienciaInstrucao->setDataAudienciaInstrucao($dataHora);
                $denunciaAudienciaInstrucao->setDataCadastro($dataHora);
            }

            $denuncia = Utils::getValue('denuncia', $data);
            if (!empty($denuncia)) {
                $denunciaAudienciaInstrucao->setDenuncia(Denuncia::newInstance($denuncia));
            }

            $encaminhamentoDenuncia = Utils::getValue('encaminhamentoDenuncia', $data);
            if (!empty($encaminhamentoDenuncia)) {
                $denunciaAudienciaInstrucao->setEncaminhamentoDenuncia(
                    EncaminhamentoDenuncia::newInstance($encaminhamentoDenuncia)
                );
            }

            $arquivosDenunciaAudienciaInstrucao = Utils::getValue('arquivosDenunciaAudienciaInstrucao', $data);
            if (!empty($arquivosDenunciaAudienciaInstrucao)) {
                foreach ($arquivosDenunciaAudienciaInstrucao as $arquivoDenunciaAudienciaInstrucao) {
                    $denunciaAudienciaInstrucao->adicionarArquivoAudienciaInstrucao(
                        ArquivoDenunciaAudienciaInstrucao::newInstance($arquivoDenunciaAudienciaInstrucao)
                    );
                }
            }
        }

        return $denunciaAudienciaInstrucao;
    }

    /**
     * Adiciona o 'ArquivoDenunciaAudienciaInstrucao' à sua respectiva coleção.
     *
     * @param ArquivoDenunciaAudienciaInstrucao $arquivoDenunciaAudienciaInstrucao
     */
    private function adicionarArquivoAudienciaInstrucao(
        ArquivoDenunciaAudienciaInstrucao $arquivoDenunciaAudienciaInstrucao
    )
    {
        if (empty($this->getArquivosAudienciaInstrucao())) {
            $this->setArquivosAudienciaInstrucao(new ArrayCollection());
        }

        if (!empty($arquivoDenunciaAudienciaInstrucao)) {
            $arquivoDenunciaAudienciaInstrucao->setDenunciaAudienciaInstrucao($this);
            $this->getArquivosAudienciaInstrucao()->add($arquivoDenunciaAudienciaInstrucao);
        }
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
     * @return Denuncia
     */
    public function getDenuncia()
    {
        return $this->denuncia;
    }

    /**
     * @param $denuncia
     */
    public function setDenuncia($denuncia)
    {
        $this->denuncia = $denuncia;
    }

    /**
     * @return EncaminhamentoDenuncia
     */
    public function getEncaminhamentoDenuncia()
    {
        return $this->encaminhamentoDenuncia;
    }

    /**
     * @param $encaminhamentoDenuncia
     */
    public function setEncaminhamentoDenuncia($encaminhamentoDenuncia)
    {
        $this->encaminhamentoDenuncia = $encaminhamentoDenuncia;
    }

    /**
     * @return string
     */
    public function getDescricaoDenunciaAudienciaInstrucao()
    {
        return $this->descricaoDenunciaAudienciaInstrucao;
    }

    /**
     * @param string $descricaoDenunciaAudienciaInstrucao
     */
    public function setDescricaoDenunciaAudienciaInstrucao($descricaoDenunciaAudienciaInstrucao)
    {
        $this->descricaoDenunciaAudienciaInstrucao = $descricaoDenunciaAudienciaInstrucao;
    }

    /**
     * @return \DateTime
     */
    public function getDataAudienciaInstrucao()
    {
        return $this->dataAudienciaInstrucao;
    }

    /**
     * @param \DateTime $dataAudienciaInstrucao
     */
    public function setDataAudienciaInstrucao($dataAudienciaInstrucao)
    {
        if (is_string($dataAudienciaInstrucao)) {
            $dataAudienciaInstrucao = new \DateTime($dataAudienciaInstrucao);
        }
        $this->dataAudienciaInstrucao = $dataAudienciaInstrucao;
    }

    /**
     * @return \DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @param \DateTime $dataCadastro
     */
    public function setDataCadastro($dataCadastro)
    {
        if (is_string($dataCadastro)){
            $dataCadastro = new \DateTime($dataCadastro);
        }
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivosAudienciaInstrucao()
    {
        return $this->arquivosDenunciaAudienciaInstrucao;
    }

    /**
     * @param array|ArrayCollection $arquivosAudienciaInstrucao
     */
    public function setArquivosAudienciaInstrucao($arquivosAudienciaInstrucao)
    {
        $this->arquivosDenunciaAudienciaInstrucao = $arquivosAudienciaInstrucao;
    }

    /**
     * Método para retirar os binários de arquivos
     */
    public function removerFiles()
    {
        if (!empty($this->arquivosAudienciaInstrucao)) {
            foreach ($this->arquivosAudienciaInstrucao as $arquivo) {
                $arquivo->setArquivo(null);
            }
        }
    }
}
