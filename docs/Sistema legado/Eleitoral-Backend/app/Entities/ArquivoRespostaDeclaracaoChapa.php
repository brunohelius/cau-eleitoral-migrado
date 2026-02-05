<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'Arquivo Resposta Declaração da Chapa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoRespostaDeclaracaoChapaRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_RESP_DEC_CHAPA")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoRespostaDeclaracaoChapa extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_RESP_DEC_CHAPA", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_resp_dec_chapa_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

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
    private $nomeFisico;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\ChapaEleicao")
     * @ORM\JoinColumn(name="ID_CHAPA_ELEICAO", referencedColumnName="ID_CHAPA_ELEICAO", nullable=false)
     *
     * @var \App\Entities\ChapaEleicao
     */
    private $chapaEleicao;

    /**
     * Transient.
     *
     * @var mixed
     */
    private $arquivo;

    /**
     * Transient.
     *
     * @var mixed
     */
    private $tamanho;

    /**
     * Fábrica de instância de 'Arquivo Resposta Declaração da Chapa'.
     *
     * @param array $data
     * @return ArquivoRespostaDeclaracaoChapa
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $arquivoRespostaDeclaracaoChapa = new ArquivoRespostaDeclaracaoChapa();

        if ($data != null) {
            $arquivoRespostaDeclaracaoChapa->setId(Utils::getValue('id', $data));
            $arquivoRespostaDeclaracaoChapa->setNome(Utils::getValue('nome', $data));
            $arquivoRespostaDeclaracaoChapa->setNomeFisico(Utils::getValue('nomeFisico', $data));
            $chapaEleicao = ChapaEleicao::newInstance(Utils::getValue('chapaEleicao', $data));
            $arquivoRespostaDeclaracaoChapa->setChapaEleicao($chapaEleicao);
            $arquivoRespostaDeclaracaoChapa->setArquivo(Utils::getValue('arquivo', $data));
            $arquivoRespostaDeclaracaoChapa->setTamanho(Utils::getValue('tamanho', $data));
        }
        return $arquivoRespostaDeclaracaoChapa;
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
    public function setId(?int $id): void
    {
        $this->id = $id;
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
    public function setNome(?string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return string
     */
    public function getNomeFisico()
    {
        return $this->nomeFisico;
    }

    /**
     * @param string $nomeFisico
     */
    public function setNomeFisico(?string $nomeFisico): void
    {
        $this->nomeFisico = $nomeFisico;
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
    public function setChapaEleicao(?ChapaEleicao $chapaEleicao): void
    {
        $this->chapaEleicao = $chapaEleicao;
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
    public function setArquivo($arquivo): void
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
    public function setTamanho($tamanho): void
    {
        $this->tamanho = $tamanho;
    }
}
