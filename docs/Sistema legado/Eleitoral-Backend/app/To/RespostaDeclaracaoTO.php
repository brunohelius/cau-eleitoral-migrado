<?php

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada a Resposta da Declaração
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class RespostaDeclaracaoTO
{

    /**
     * @var integer
     */
    private $idDeclaracao;

    /**
     * @var array
     */
    private $itensRespostaDeclaracao;

    /**
     * Retorna uma nova instância de 'RespostaDeclaracaoTO'.
     *
     * @param null $data
     * @return RespostaDeclaracaoTO
     */
    public static function newInstance($data = null)
    {
        $respostaDeclaracaoTO = new RespostaDeclaracaoTO();

        if ($data != null) {
            $respostaDeclaracaoTO->setIdDeclaracao(Utils::getValue('idDeclaracao', $data));

            $itensRespostaDeclaracao = Utils::getValue('itensRespostaDeclaracao', $data);
            if(!empty($itensRespostaDeclaracao)) {
                $respostaDeclaracaoTO->setItensRespostaDeclaracao(array_map(function($itemRespostaDeclaracao){
                    return ItemRespostaDeclaracaoTO::newInstance($itemRespostaDeclaracao);
                },$itensRespostaDeclaracao));
            }
        }

        return $respostaDeclaracaoTO;
    }

    /**
     * @return int
     */
    public function getIdDeclaracao()
    {
        return $this->idDeclaracao;
    }

    /**
     * @param int $idDeclaracao
     */
    public function setIdDeclaracao($idDeclaracao): void
    {
        $this->idDeclaracao = $idDeclaracao;
    }

    /**
     * @return array
     */
    public function getItensRespostaDeclaracao()
    {
        return $this->itensRespostaDeclaracao;
    }

    /**
     * @param array $itensRespostaDeclaracao
     */
    public function setItensRespostaDeclaracao($itensRespostaDeclaracao): void
    {
        $this->itensRespostaDeclaracao = $itensRespostaDeclaracao;
    }
}
