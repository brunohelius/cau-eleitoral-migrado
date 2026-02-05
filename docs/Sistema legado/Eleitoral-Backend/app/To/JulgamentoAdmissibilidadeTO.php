<?php

namespace App\To;

use App\Entities\DenunciaAdmitida;
use App\Entities\JulgamentoAdmissibilidade;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

/**
 * Class JulgamentoAdmissibilidadeTO
 * @package App\To
 *
 * @OA\Schema(schema="JulgamentoAdmissibilidade")
 */
 class JulgamentoAdmissibilidadeTO
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
    private $julgamento;

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
      * @var ArquivoJulgamentoAdmissibilidadeTO[]
      */
    private $arquivos;

     /**
      * @var RecursoJulgamentoAdmissibilidadeTO
      */
    private $recursoJulgamentoAdmissibilidade;

     /**
      * @var boolean
      */
    private $hasPrazoRecursoJulgamentoAdmissibilidade;

     /**
      * @var integer
      */
    private $idTipoJulgamento;

     /**
      * @param JulgamentoAdmissibilidade|null $julgamentoAdmissibilidade
      * @return JulgamentoAdmissibilidadeTO
      */
    public static function newInstanceFromEntity(?JulgamentoAdmissibilidade $julgamentoAdmissibilidade)
    {
        $instance = new self;

        if ($julgamentoAdmissibilidade) {
            $instance->setId($julgamentoAdmissibilidade->getId());
            $instance->setDataCriacao(Carbon::instance($julgamentoAdmissibilidade->getDataCriacao())->toDateTime());
            $instance->setJulgamento($julgamentoAdmissibilidade->getTipoJulgamento()->getDescricao());
            $instance->setIdTipoJulgamento($julgamentoAdmissibilidade->getTipoJulgamento()->getId());
            $instance->setDescricao($julgamentoAdmissibilidade->getDescricao());

            if ($julgamentoAdmissibilidade->getRecursoJulgamento()) {
                $instance->setRecursoJulgamentoAdmissibilidade(RecursoJulgamentoAdmissibilidadeTO::newInstanceFromEntity($julgamentoAdmissibilidade->getRecursoJulgamento()));
            }

            $arquivos = null;
            foreach ($julgamentoAdmissibilidade->getArquivos() as $arquivo) {
                $arquivos[] = ArquivoJulgamentoAdmissibilidadeTO::newInstanceFromEntity($arquivo);
            }

            if (null !== $arquivos) {
                $instance->setArquivos($arquivos);
            }

            $instance->setHasPrazoRecursoJulgamentoAdmissibilidade(true);
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
      * @return JulgamentoAdmissibilidadeTO
      */
     public function setId(int $id): JulgamentoAdmissibilidadeTO
     {
         $this->id = $id;
         return $this;
     }

     /**
      * @return string
      */
     public function getJulgamento(): string
     {
         return $this->julgamento;
     }

     /**
      * @param string $julgamento
      * @return JulgamentoAdmissibilidadeTO
      */
     public function setJulgamento(string $julgamento): JulgamentoAdmissibilidadeTO
     {
         $this->julgamento = $julgamento;
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
      * @return JulgamentoAdmissibilidadeTO
      */
     public function setDescricao(string $descricao): JulgamentoAdmissibilidadeTO
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
      * @return JulgamentoAdmissibilidadeTO
      */
     public function setDataCriacao($dataCriacao)
     {
         $this->dataCriacao = $dataCriacao;
         return $this;
     }

     /**
      * @return RecursoJulgamentoAdmissibilidadeTO
      */
     public function getRecursoJulgamentoAdmissibilidade()
     {
         return $this->recursoJulgamentoAdmissibilidade;
     }

     /**
      * @param RecursoJulgamentoAdmissibilidadeTO $recursoJulgamentoAdmissibilidade
      * @return JulgamentoAdmissibilidadeTO
      */
     public function setRecursoJulgamentoAdmissibilidade(RecursoJulgamentoAdmissibilidadeTO $recursoJulgamentoAdmissibilidade): JulgamentoAdmissibilidadeTO
     {
         $this->recursoJulgamentoAdmissibilidade = $recursoJulgamentoAdmissibilidade;
         return $this;
     }

     /**
      * @return int
      */
     public function getIdTipoJulgamento(): int
     {
         return $this->idTipoJulgamento;
     }

     /**
      * @param int $idTipoJulgamento
      */
     public function setIdTipoJulgamento(int $idTipoJulgamento): void
     {
         $this->idTipoJulgamento = $idTipoJulgamento;
     }

     /**
      * @return ArquivoJulgamentoAdmissibilidadeTO[]
      */
     public function getArquivos()
     {
         return $this->arquivos;
     }

     /**
      * @param ArquivoJulgamentoAdmissibilidadeTO[] $arquivos
      */
     public function setArquivos($arquivos): void
     {
         $this->arquivos = $arquivos;
     }

     /**
      * @return bool
      */
     public function hasPrazoRecursoJulgamentoAdmissibilidade(): bool
     {
         return $this->hasPrazoRecursoJulgamentoAdmissibilidade;
     }

     /**
      * @param bool $hasPrazoRecursoJulgamentoAdmissibilidade
      */
     public function setHasPrazoRecursoJulgamentoAdmissibilidade(bool $hasPrazoRecursoJulgamentoAdmissibilidade): void
     {
         $this->hasPrazoRecursoJulgamentoAdmissibilidade = $hasPrazoRecursoJulgamentoAdmissibilidade;
     }

}
