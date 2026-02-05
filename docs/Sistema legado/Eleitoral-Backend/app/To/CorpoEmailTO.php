<?php

namespace App\To;

use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'CorpoEmail'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="CorpoEmail")
 */
class CorpoEmailTO
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $assunto;

    /**
     * @var string
     */
    private $descricao;

    /**
     * @var boolean
     */
    private $ativo;

    /**
     * @var CabecalhoEmailTO
     */
    private $cabecalhoEmail;

    /**
     * @var ArrayCollection
     */
    private $atividadesSecundarias;

    /**
     * Retorna uma nova instância de 'CorpoEmailTO'.
     *
     * @param null $data
     * @return CorpoEmailTO
     */
    public static function newInstance($data = null)
    {
        $corpoEmailTO = new CorpoEmailTO();

        if ($data != null) {
            $corpoEmailTO->setId(Utils::getValue('id', $data));
            $corpoEmailTO->setAssunto(Utils::getValue('assunto', $data));
            $corpoEmailTO->setAtivo(Utils::getBooleanValue('ativo', $data));
            $corpoEmailTO->setDescricao(Utils::getValue('descricao', $data));

            $cabecalhoEmailTO = CabecalhoEmailTO::newInstance(Utils::getValue('cabecalhoEmail', $data));
            $corpoEmailTO->setCabecalhoEmail($cabecalhoEmailTO);

            $atividadesSecundaria = new ArrayCollection();
            $emailsAtividadeSecundaria = Utils::getValue('emailsAtividadeSecundaria', $data);

            foreach ($emailsAtividadeSecundaria as $emailAtividadeSecundaria) {
                $idEmail =  empty($emailAtividadeSecundaria['id']) ? null : $emailAtividadeSecundaria['id'];
                $emailAtividadeSecundaria['atividadeSecundaria']['emailAtividadeSecundaria'] = $idEmail;
                $emailAtividadeSecundaria['atividadeSecundaria']['hasDefinicao'] = !empty(
                    $emailAtividadeSecundaria['emailsTipos']
                );
                $atividadesSecundaria->add($emailAtividadeSecundaria['atividadeSecundaria']);
            }

            $corpoEmailTO->setAtividadesSecundarias($atividadesSecundaria);
        }

        return $corpoEmailTO;
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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getAssunto()
    {
        return $this->assunto;
    }

    /**
     * @param string $assunto
     */
    public function setAssunto($assunto)
    {
        $this->assunto = $assunto;
    }

    /**
     * @return string|null
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return bool
     */
    public function isAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param bool $ativo
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    /**
     * @return CabecalhoEmailTO|null
     */
    public function getCabecalhoEmail()
    {
        return $this->cabecalhoEmail;
    }

    /**
     * @param CabecalhoEmailTO $cabecalhoEmail
     */
    public function setCabecalhoEmail(CabecalhoEmailTO $cabecalhoEmail)
    {
        $this->cabecalhoEmail = $cabecalhoEmail;
    }

    /**
     * @return ArrayCollection
     */
    public function getAtividadesSecundarias()
    {
        return $this->atividadesSecundarias;
    }

    /**
     * @param ArrayCollection $atividadesSecundarias
     */
    public function setAtividadesSecundarias($atividadesSecundarias)
    {
        $this->atividadesSecundarias = $atividadesSecundarias;
    }

}
