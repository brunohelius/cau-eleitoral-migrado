<?php

namespace App\To;


use App\Entities\JulgamentoAdmissibilidade;
use App\Entities\RecursoJulgamentoAdmissibilidade;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

/**
 * Class RecursoJulgamentoAdmissibilidadeTO
 * @package App\To
 *
 * @OA\Schema(schema="RecursoJulgamentoAdmissibilidade")
 */
class RecursoJulgamentoAdmissibilidadeTO
{
    /**
     * @var integer
     * @OA\Property()
     */
    private $id;

    /**
     * @var string
     * @OA\Property()
     */
    private $descricao;

    /**
     * @var \DateTime
     * @OA\Property()
     */
    private $dataCriacao;

    /**
     * @var ArquivoRecursoJulgamentoAdmissibilidadeTO[]
     */
    private $arquivos = [];

    /**
     * @var JulgamentoRecursoAdmissibilidadeTO
     * @OA\Property()
     */
    private $julgamentoRecurso;

    /**
     * @var mixed
     */
    private $solicitante;

    /**
     * @param RecursoJulgamentoAdmissibilidade|null $recursoJulgamentoAdmissibilidade
     * @return RecursoJulgamentoAdmissibilidadeTO
     */
    public static function newInstanceFromEntity($recursoJulgamentoAdmissibilidade)
    {
        $instance = new self;
        if ($recursoJulgamentoAdmissibilidade) {
            $instance->setId($recursoJulgamentoAdmissibilidade->getId());
            $instance->setDataCriacao(Carbon::instance($recursoJulgamentoAdmissibilidade->getData())->toDateTime());
            $instance->setDescricao($recursoJulgamentoAdmissibilidade->getDescricao());
            foreach ($recursoJulgamentoAdmissibilidade->getArquivos() as $arquivo) {
                $instance->arquivos[] = ArquivoRecursoJulgamentoAdmissibilidadeTO::newInstanceFromEntity($arquivo);
            }

            if ($recursoJulgamentoAdmissibilidade->getJulgamentoRecursoAdmissibilidade()) {
                $instance->setJulgamentoRecurso(JulgamentoRecursoAdmissibilidadeTO::newInstanceFromEntity($recursoJulgamentoAdmissibilidade->getJulgamentoRecursoAdmissibilidade()));
            }
            $instance->setSolicitante(null);
        }

        return $instance;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return RecursoJulgamentoAdmissibilidadeTO
     */
    public function setId(int $id): RecursoJulgamentoAdmissibilidadeTO
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     * @return RecursoJulgamentoAdmissibilidadeTO
     */
    public function setDescricao(string $descricao): RecursoJulgamentoAdmissibilidadeTO
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDataCriacao(): \DateTime
    {
        return $this->dataCriacao;
    }

    /**
     * @param \DateTime $dataCriacao
     * @return RecursoJulgamentoAdmissibilidadeTO
     */
    public function setDataCriacao(\DateTime $dataCriacao): RecursoJulgamentoAdmissibilidadeTO
    {
        $this->dataCriacao = $dataCriacao;
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
     * @return RecursoJulgamentoAdmissibilidadeTO
     */
    public function setArquivos(array $arquivos): RecursoJulgamentoAdmissibilidadeTO
    {
        $this->arquivos = $arquivos;
        return $this;
    }

    /**
     * @return JulgamentoRecursoAdmissibilidadeTO
     */
    public function getJulgamentoRecurso()
    {
        return $this->julgamentoRecurso;
    }

    /**
     * @param JulgamentoRecursoAdmissibilidadeTO $julgamentoRecurso
     * @return RecursoJulgamentoAdmissibilidadeTO
     */
    public function setJulgamentoRecurso($julgamentoRecurso)
    {
        $this->julgamentoRecurso = $julgamentoRecurso;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getSolicitante()
    {
        return $this->solicitante;
    }

    /**
     * @param mixed|null $solicitante
     */
    public function setSolicitante($solicitante): void
    {
        $this->solicitante = $solicitante;
    }

}
