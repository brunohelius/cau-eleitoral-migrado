<?php

namespace App\To;

use App\Entities\JulgamentoRecursoAdmissibilidade;
use App\Util\Utils;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

/**
 * Class JulgamentoRecursoAdmissibilidade
 * @package App\To
 *
 * @OA\Schema(schema="JulgamentoRecursoAdmissibilidade")
 */
class JulgamentoRecursoAdmissibilidadeTO
{
    /**
     * ID
     * @var integer
     * @OA\Property()
     */
    private $id;

    /**
     * Nome do Arquivo
     * @var string
     * @OA\Property()
     */
    private $descricao;

    /**
     * Data criacao
     * @var \DateTime
     * @OA\Property()
     */
    private $dataCriacao;

    /**
     * Parecer do julgamento
     * @var ParecerJulgamentoRecursoAdmissibilidadeTO
     * @OA\Property()
     */
    private $parecer;

    /**
     * @var ArquivoRecursoJulgamentoAdmissibilidadeTO[]
     */
    private $arquivos = [];

    /**
     * @var ArquivoDescricaoTO[]|null
     */
    private $descricaoArquivo;

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoAdmissibilidadeTO'.
     *
     * @param JulgamentoRecursoAdmissibilidade $julgamentoRecursoAdmissibilidade
     * @return self
     */
    public static function newInstanceFromEntity(JulgamentoRecursoAdmissibilidade $julgamentoRecursoAdmissibilidade = null)
    {
        $instance = new self;

        if ($julgamentoRecursoAdmissibilidade != null) {
            $instance->setId($julgamentoRecursoAdmissibilidade->getId());
            $instance->setDescricao($julgamentoRecursoAdmissibilidade->getDescricao());
            $instance->setDataCriacao(Carbon::instance($julgamentoRecursoAdmissibilidade->getData())->toDateTime());

            if($julgamentoRecursoAdmissibilidade->getParecer()){
                $instance->setParecer(ParecerJulgamentoRecursoAdmissibilidadeTO::newInstanceFromEntity($julgamentoRecursoAdmissibilidade->getParecer()));
            }

            foreach ($julgamentoRecursoAdmissibilidade->getArquivos() as $arquivo) {
                $instance->arquivos[] = ArquivoJulgamentoRecursoAdmissibilidadeTO::newInstanceFromEntity($arquivo);
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
     * @return JulgamentoRecursoAdmissibilidadeTO
     */
    public function setId( $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     * @return JulgamentoRecursoAdmissibilidadeTO
     */
    public function setDescricao( $descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDataCriacao()
    {
        return $this->dataCriacao;
    }

    /**
     * @param \DateTime $dataCriacao
     * @return JulgamentoRecursoAdmissibilidadeTO
     */
    public function setDataCriacao( $dataCriacao)
    {
        $this->dataCriacao = $dataCriacao;
        return $this;
    }

    /**
     * @return ParecerJulgamentoRecursoAdmissibilidadeTO
     */
    public function getParecer()
    {
        return $this->parecer;
    }

    /**
     * @param ParecerJulgamentoRecursoAdmissibilidadeTO $parecer
     * @return JulgamentoRecursoAdmissibilidadeTO
     */
    public function setParecer( $parecer)
    {
        $this->parecer = $parecer;
        return $this;
    }

    /**
     * @return ArquivoRecursoJulgamentoAdmissibilidadeTO[]
     */
    public function getArquivos(): array
    {
        return $this->arquivos;
    }

    /**
     * @param ArquivoRecursoJulgamentoAdmissibilidadeTO[] $arquivos
     * @return JulgamentoRecursoAdmissibilidadeTO
     */
    public function setArquivos(array $arquivos): JulgamentoRecursoAdmissibilidadeTO
    {
        $this->arquivos = $arquivos;
        return $this;
    }

    /**
     * @return ArquivoDescricaoTO[]|null
     */
    public function getDescricaoArquivo(): ?array
    {
        return $this->descricaoArquivo;
    }

    /**
     * @param ArquivoDescricaoTO[]|null $descricaoArquivo
     */
    public function setDescricaoArquivo(?array $descricaoArquivo): void
    {
        $this->descricaoArquivo = $descricaoArquivo;
    }

}