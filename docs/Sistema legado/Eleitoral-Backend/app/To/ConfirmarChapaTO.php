<?php


namespace App\To;


use App\Entities\ArquivoRespostaDeclaracaoChapa;
use App\Util\Utils;

/**
 * Classe de transferência para associada a confirmação da criação da chapa
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ConfirmarChapaTO
{

    /**
     * @var array|ItemDeclaracaoTO[]
     */
    private $itensDeclaracao;

    /**
     * Sequencia da declaração
     * @var array|ArquivoRespostaDeclaracaoChapa[]
     */
    private $arquivosRespostaDeclaracaoChapa;

    /**
     * Fábrica de instância de 'ConfirmarChapaTO'.
     *
     * @param null $data
     * @return ConfirmarChapaTO
     */
    public static function newInstance($data = null)
    {
        $confirmarChapaTO = new ConfirmarChapaTO();

        if ($data != null) {

            $itensDeclaracaoTO = Utils::getValue('itensDeclaracao', $data, []);
            if(!empty($itensDeclaracaoTO)) {
                $confirmarChapaTO->setItensDeclaracao(array_map(function ($data){
                    return ItemDeclaracaoTO::newInstance($data);
                }, $itensDeclaracaoTO));
            }

            $arquivosRespostaDeclaracaoChapa = Utils::getValue('arquivosRespostaDeclaracaoChapa', $data, []);
            if(!empty($arquivosRespostaDeclaracaoChapa)) {
                $confirmarChapaTO->setArquivosRespostaDeclaracaoChapa(array_map(function ($data){
                    return ArquivoRespostaDeclaracaoChapa::newInstance($data);
                }, $arquivosRespostaDeclaracaoChapa));
            }
        }

        return $confirmarChapaTO;
    }

    /**
     * @return ArquivoRespostaDeclaracaoChapa[]|array
     */
    public function getArquivosRespostaDeclaracaoChapa()
    {
        return $this->arquivosRespostaDeclaracaoChapa;
    }

    /**
     * @param ArquivoRespostaDeclaracaoChapa[]|array $arquivosRespostaDeclaracaoChapa
     */
    public function setArquivosRespostaDeclaracaoChapa($arquivosRespostaDeclaracaoChapa): void
    {
        $this->arquivosRespostaDeclaracaoChapa = $arquivosRespostaDeclaracaoChapa;
    }

    /**
     * @return array
     */
    public function getItensDeclaracao()
    {
        return $this->itensDeclaracao;
    }

    /**
     * @param array $itensDeclaracao
     */
    public function setItensDeclaracao($itensDeclaracao): void
    {
        $this->itensDeclaracao = $itensDeclaracao;
    }
}