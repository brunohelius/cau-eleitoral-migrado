<?php
namespace App\To;

use App\Util\Utils;
use App\Entities\EmailAtividadeSecundaria;

/**
 * Classe de transferência associada a “E-mail Atividade Secundária”, utilizada na parametrização de e-mail.
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class DeclaracaoAtividadeTO
{
    /**
     * @var integer
     */
    private $idDeclaracao;
    
    /**
     * @var integer
     */
    private $idTipoDeclaracaoAtividade;

    /**
     * @var string
     */
    private $descricaoTipoDeclaracaoAtividade;

    /**
     * @param null $data
     * @return DeclaracaoAtividadeTO
     */
    public static function newInstance($data = null){
        $declaracaoAtividadeTO = new DeclaracaoAtividadeTO();
        if(!empty($data)){
            $declaracaoAtividadeTO->setIdDeclaracao(Utils::getValue('idDeclaracao', $data));
            $declaracaoAtividadeTO->setIdTipoDeclaracaoAtividade(Utils::getValue(
                'idTipoDeclaracaoAtividade',
                $data
            ));
            $declaracaoAtividadeTO->setDescricaoTipoDeclaracaoAtividade(Utils::getValue(
                'descricaoTipoDeclaracaoAtividade',
                $data
            ));
        }
        return $declaracaoAtividadeTO;
    }

    /**
     * @return int
     */
    public function getIdDeclaracao(): ?int
    {
        return $this->idDeclaracao;
    }

    /**
     * @param int $idDeclaracao
     */
    public function setIdDeclaracao(?int $idDeclaracao): void
    {
        $this->idDeclaracao = $idDeclaracao;
    }

    /**
     * @return int
     */
    public function getIdTipoDeclaracaoAtividade(): ?int
    {
        return $this->idTipoDeclaracaoAtividade;
    }

    /**
     * @param int $idTipoDeclaracaoAtividade
     */
    public function setIdTipoDeclaracaoAtividade(?int $idTipoDeclaracaoAtividade): void
    {
        $this->idTipoDeclaracaoAtividade = $idTipoDeclaracaoAtividade;
    }

    /**
     * @return string
     */
    public function getDescricaoTipoDeclaracaoAtividade(): ?string
    {
        return $this->descricaoTipoDeclaracaoAtividade;
    }

    /**
     * @param string $descricaoTipoDeclaracaoAtividade
     */
    public function setDescricaoTipoDeclaracaoAtividade(?string $descricaoTipoDeclaracaoAtividade): void
    {
        $this->descricaoTipoDeclaracaoAtividade = $descricaoTipoDeclaracaoAtividade;
    }
}

