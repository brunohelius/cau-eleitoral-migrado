<?php
/*
 * ArquivoDenunciaAudienciaInstrucao.php
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
 * Entidade de representação de 'ArquivoDenunciaAudienciaInstrucao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoDenunciaAudienciaInstrucaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_AUDIENCIA_INSTRUCAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDenunciaAudienciaInstrucao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_AUDIENCIA_INSTRUCAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_audiencia_instrucao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\DenunciaAudienciaInstrucao")
     * @ORM\JoinColumn(name="ID_DENUNCIA_AUDIENCIA_INSTRUCAO", referencedColumnName="ID_DENUNCIA_AUDIENCIA_INSTRUCAO", nullable=false)
     * @var DenunciaProvas
     */
    private $denunciaAudienciaInstrucao;

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
    private $nomeFisicoArquivo;

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
     * Fábrica de instância de 'ArquivoDenunciaAudienciaInstrucao'.
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $arquivoDenunciaAudienciaInstrucao = new ArquivoDenunciaAudienciaInstrucao();

        if ($data != null) {
            $arquivoDenunciaAudienciaInstrucao->setId(Utils::getValue('id', $data));

            $arquivoDenunciaAudienciaInstrucao->setArquivo(Utils::getValue('arquivo', $data));
            $arquivoDenunciaAudienciaInstrucao->setTamanho(Utils::getValue('tamanho', $data));

            $arquivoDenunciaAudienciaInstrucao->setNome(Utils::getValue('nome', $data));
            $arquivoDenunciaAudienciaInstrucao->setNomeFisicoArquivo(Utils::getValue('nomeFisicoArquivo', $data));

            $denunciaAudienciaInstrucao = Utils::getValue('denunciaAudienciaInstrucao', $data);
            if (!empty($denunciaAudienciaInstrucao)) {
                $arquivoDenunciaAudienciaInstrucao->setDenunciaAudienciaInstrucao(
                    DenunciaAudienciaInstrucao::newInstance($denunciaAudienciaInstrucao)
                );
            }
        }

        return $arquivoDenunciaAudienciaInstrucao;
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
     * @return DenunciaAudienciaInstrucao
     */
    public function getDenunciaAudienciaInstrucao()
    {
        return $this->denunciaAudienciaInstrucao;
    }

    /**
     * @param DenunciaAudienciaInstrucao $denunciaAudienciaInstrucao
     */
    public function setDenunciaAudienciaInstrucao(DenunciaAudienciaInstrucao $denunciaAudienciaInstrucao)
    {
        $this->denunciaAudienciaInstrucao = $denunciaAudienciaInstrucao;
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
    public function getNomeFisicoArquivo()
    {
        return $this->nomeFisicoArquivo;
    }

    /**
     * @param $nomeFisicoArquivo
     */
    public function setNomeFisicoArquivo($nomeFisicoArquivo)
    {
        $this->nomeFisicoArquivo = $nomeFisicoArquivo;
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
