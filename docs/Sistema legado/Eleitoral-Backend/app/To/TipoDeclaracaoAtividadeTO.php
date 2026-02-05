<?php


namespace App\To;

use App\Entities\TipoDeclaracaoAtividade;
use App\Util\Utils;

/**
 * Classe de transferência associada ao “Tipo Declaração Atividade”, utilizada na parametrização de declarações.
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class TipoDeclaracaoAtividadeTO
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $descricao;

    public static function newInstance($data = null){
        $tipoDeclaracaoAtividadeTO = new TipoDeclaracaoAtividadeTO();
        if(!empty($data)){
            $tipoDeclaracaoAtividadeTO->setId(Utils::getValue('id', $data));
            $tipoDeclaracaoAtividadeTO->setDescricao(Utils::getValue('descricao', $data));
        }
        return $tipoDeclaracaoAtividadeTO;
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
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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
     */
    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }
}