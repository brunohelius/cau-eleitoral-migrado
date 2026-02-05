<?php

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Entidade de representação de 'ArquivoDefesaImpugnacao'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArquivoDefesaImpugnacaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_ARQUIVO_DEFESA_IMPUGNACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDefesaImpugnacao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_ARQUIVO_DEFESA_IMPUGNACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_arquivo_defesa_impugnacao_id_seq", initialValue=1, allocationSize=1)
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
     * @ORM\ManyToOne(targetEntity="App\Entities\DefesaImpugnacao")
     * @ORM\JoinColumn(name="ID_DEFESA_IMPUGNACAO", referencedColumnName="ID_DEFESA_IMPUGNACAO", nullable=false)
     *
     * @var DefesaImpugnacao
     */
    private $defesaImpugnacao;

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
     * Fábrica de instância de 'Arquivo Defesa Impugnação'.
     *
     * @param array $data
     * @return ArquivoDefesaImpugnacao
     * @throws Exception
     */
    public static function newInstance($data = null)
    {
        $arquivoDefesaImpugnacao = new ArquivoDefesaImpugnacao();

        if ($data != null) {
            $arquivoDefesaImpugnacao->setId(Utils::getValue('id', $data));
            $arquivoDefesaImpugnacao->setNome(Utils::getValue('nome', $data));
            $arquivoDefesaImpugnacao->setArquivo(Utils::getValue('arquivo', $data));
            $arquivoDefesaImpugnacao->setTamanho(Utils::getValue('tamanho', $data));
            $arquivoDefesaImpugnacao->setNomeFisico(Utils::getValue('nomeFisico', $data));

            $defesaImpugnacao = Utils::getValue('defesaImpugnacao', $data);
            if(!empty($defesaImpugnacao)) {
                $arquivoDefesaImpugnacao->setDefesaImpugnacao(DefesaImpugnacao::newInstance($defesaImpugnacao));
            }
        }
        return $arquivoDefesaImpugnacao;
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
    public function getNomeFisico()
    {
        return $this->nomeFisico;
    }

    /**
     * @param string $nomeFisico
     */
    public function setNomeFisico($nomeFisico): void
    {
        $this->nomeFisico = $nomeFisico;
    }

    /**
     * @return DefesaImpugnacao
     */
    public function getDefesaImpugnacao(): ?DefesaImpugnacao
    {
        return $this->defesaImpugnacao;
    }

    /**
     * @param DefesaImpugnacao $defesaImpugnacao
     */
    public function setDefesaImpugnacao(DefesaImpugnacao $defesaImpugnacao): void
    {
        $this->defesaImpugnacao = $defesaImpugnacao;
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
