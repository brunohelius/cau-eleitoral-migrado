<?php

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada a Resposta da Declaração
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class ItemRespostaDeclaracaoTO
{

    /**
     * @var integer
     */
    private $idItemDeclaracao;

    /**
     * @var boolean
     */
    private $situacaoResposta;

    /**
     * Retorna uma nova instância de 'ItemRespostaDeclaracaoTO'.
     *
     * @param null $data
     * @return ItemRespostaDeclaracaoTO
     */
    public static function newInstance($data = null)
    {
        $itemRespostaDeclaracaoTO = new ItemRespostaDeclaracaoTO();

        if ($data != null) {
            $itemRespostaDeclaracaoTO->setIdItemDeclaracao(Utils::getValue('idItemDeclaracao', $data));
            $itemRespostaDeclaracaoTO->setSituacaoResposta(Utils::getValue('situacaoResposta', $data));
        }

        return $itemRespostaDeclaracaoTO;
    }

    /**
     * @return int
     */
    public function getIdItemDeclaracao()
    {
        return $this->idItemDeclaracao;
    }

    /**
     * @param int $idItemDeclaracao
     */
    public function setIdItemDeclaracao($idItemDeclaracao): void
    {
        $this->idItemDeclaracao = $idItemDeclaracao;
    }

    /**
     * @return boolean
     */
    public function getSituacaoResposta()
    {
        return $this->situacaoResposta;
    }

    /**
     * @param boolean $situacaoResposta
     */
    public function setSituacaoResposta($situacaoResposta): void
    {
        $this->situacaoResposta = $situacaoResposta;
    }
}
