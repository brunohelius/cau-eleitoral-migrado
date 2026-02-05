<?php
/*
 * ConfiguracaoFacade.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Entities\DeclaracaoAtividade;
use App\Entities\EmailAtividadeSecundaria;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as JMS;
use App\Util\Utils;

/**
 * Classe de transferência associada aos dados para definição de emails e/ou declarações.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 */
class DefinicaoDeclaracoesEmailsAtivSecundariaTO
{
    /**
     * Lista de emails da configuração.
     *
     * @var array
     */
    private $emails;

    /**
     * Lista de declaracões da configuração
     *
     * @var array
     */
    private $declaracoes;

    /**
     * ID da atividade secundaria.
     *
     * @var integer
     */
    private $idAtividadeSecundaria;

    /**
     * Fabricação estática de 'DefinicaoDeclaracoesEmailsAtivSecundariaTO'.
     *
     * @param array|null $data
     *
     * @return DefinicaoDeclaracoesEmailsAtivSecundariaTO
     */
    public static function newInstance($data = null)
    {
        $configuracaoDeclaracoesEmailTO = new DefinicaoDeclaracoesEmailsAtivSecundariaTO();

        if ($data != null) {
            $configuracaoDeclaracoesEmailTO->setIdAtividadeSecundaria(
                Utils::getValue("idAtividadeSecundaria", $data)
            );

            $emails = Utils::getValue('emails', $data);
            if (!empty($emails)) {
                foreach ($emails as $email) {
                    $configuracaoDeclaracoesEmailTO->emails[] = EmailAtividadeSecundariaTO::newInstance($email);
                }
            }

            $declaracoes = Utils::getValue('declaracoes', $data);
            if (!empty($declaracoes)) {
                foreach ($declaracoes as $declaracao) {
                    $configuracaoDeclaracoesEmailTO->declaracoes[] = DeclaracaoAtividadeTO::newInstance($declaracao);
                }
            }
        }

        return $configuracaoDeclaracoesEmailTO;
    }

    /**
     * @return array
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * @param array $emails
     */
    public function setEmails(array $emails)
    {
        $this->emails = $emails;
    }

    /**
     * @return array
     */
    public function getDeclaracoes()
    {
        return $this->declaracoes;
    }

    /**
     * @param array $declaracoes
     */
    public function setDeclaracoes(array $declaracoes)
    {
        $this->declaracoes = $declaracoes;
    }

    /**
     * @return int
     */
    public function getIdAtividadeSecundaria()
    {
        return $this->idAtividadeSecundaria;
    }

    /**
     * @param int $idAtividadeSecundaria
     */
    public function setIdAtividadeSecundaria(int $idAtividadeSecundaria)
    {
        $this->idAtividadeSecundaria = $idAtividadeSecundaria;
    }

}