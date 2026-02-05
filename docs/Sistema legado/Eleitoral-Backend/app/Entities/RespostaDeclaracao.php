<?php
/*
 * TipoProcesso.php
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

/**
 * Entidade de representação de 'Resposta Declaração'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\RespostaDeclaracaoRepository")
 * @ORM\Table(schema="eleitoral", name="TB_RESPOSTA_DECLARACAO")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class RespostaDeclaracao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="ID_RESPOSTA_DECLARACAO", type="integer")
     * @ORM\SequenceGenerator(sequenceName="eleitoral.tb_resposta_declaracao_id_seq", initialValue=1, allocationSize=1)
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_TITULO", type="string", length=150, nullable=true)
     *
     * @var string
     */
    private $titulo;

    /**
     * @ORM\Column(name="DS_TEXTO_INICIAL", type="text", nullable=true)
     *
     * @var string
     */
    private $textoInicial;

    /**
     * @ORM\Column(name="ID_TP_RESPOSTA", type="integer", nullable=true)
     *
     * @var integer
     */
    private $tipoResposta;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\ItemRespostaDeclaracao", mappedBy="respostaDeclaracao")
     *
     * @var array|ArrayCollection
     */
    private $itensResposta;

    /**
     * Fábrica de instância de RespostaDeclaracao.
     *
     * @param array $data
     * @return RespostaDeclaracao
     */
    public static function newInstance($data = null)
    {
        $respostaDeclaracao = new RespostaDeclaracao();

        if ($data != null) {
            $respostaDeclaracao->setId(Utils::getValue('id', $data));
            $respostaDeclaracao->setTitulo(Utils::getValue('titulo', $data));
            $respostaDeclaracao->setTextoInicial(Utils::getValue('textoInicial', $data));
            $respostaDeclaracao->setTipoResposta(Utils::getValue('tipoResposta', $data));
        }
        return $respostaDeclaracao;
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
     * @return string
     */
    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    /**
     * @param string $titulo
     */
    public function setTitulo(?string $titulo): void
    {
        $this->titulo = $titulo;
    }

    /**
     * @return string
     */
    public function getTextoInicial(): ?string
    {
        return $this->textoInicial;
    }

    /**
     * @param string $textoInicial
     */
    public function setTextoInicial(?string $textoInicial): void
    {
        $this->textoInicial = $textoInicial;
    }

    /**
     * @return int
     */
    public function getTipoResposta(): ?int
    {
        return $this->tipoResposta;
    }

    /**
     * @param int $tipoResposta
     */
    public function setTipoResposta(?int $tipoResposta): void
    {
        $this->tipoResposta = $tipoResposta;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getItensResposta()
    {
        return $this->itensResposta;
    }

    /**
     * @param array|ArrayCollection $itensResposta
     */
    public function setItensResposta($itensResposta): void
    {
        $this->itensResposta = $itensResposta;
    }

}