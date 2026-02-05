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
 * Entidade de representação de 'Arquivo Alegação Final'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoAlegacaoFinalRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_ALEGACAO_FINAL")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoAlegacaoFinal extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_ALEGACAO_FINAL", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_alegacao_final_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\AlegacaoFinal")
     * @ORM\JoinColumn(name="ID_ALEGACAO_FINAL", referencedColumnName="ID_ALEGACAO_FINAL", nullable=false)
     * @var \App\Entities\AlegacaoFinal
     */
    private $alegacaoFinal;

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
     * Fábrica de instância de 'Arquivo Denuncia Defesa'.
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $arquivoAlegacaoFinal = new ArquivoAlegacaoFinal();

        if ($data != null) {
            $arquivoAlegacaoFinal->setId(Utils::getValue('id', $data));
            $arquivoAlegacaoFinal->setArquivo(Utils::getValue('arquivo', $data));
            $arquivoAlegacaoFinal->setTamanho(Utils::getValue('tamanho', $data));
            $arquivoAlegacaoFinal->setNome(Utils::getValue('nome', $data));
            $arquivoAlegacaoFinal->setNomeFisicoArquivo(Utils::getValue('nomeFisicoArquivo', $data));

            $alegacaoFinal = Utils::getValue('alegacaoFinal', $data);
            if (!empty($alegacaoFinal)) {
                $arquivoAlegacaoFinal->setAlegacaoFinal(AlegacaoFinal::newInstance($alegacaoFinal));
            }
        }

        return $arquivoAlegacaoFinal;
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
     * @return AlegacaoFinal
     */
    public function getAlegacaoFinal()
    {
        return $this->alegacaoFinal;
    }

    /**
     * @param AlegacaoFinal $alegacaoFinal
     */
    public function setAlegacaoFinal(AlegacaoFinal $alegacaoFinal)
    {
        $this->alegacaoFinal = $alegacaoFinal;
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
