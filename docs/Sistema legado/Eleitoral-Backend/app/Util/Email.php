<?php
/*
 * Email.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Util;

use App\Config\AppConfig;
use App\Mail\AtividadeSecundariaMail;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Swift_Image;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

/**
 * Classe utilitária para apoio ao envio de e-mails.
 *
 * @author Squadra Tecnologia S/A.
 */
class Email
{

    /**
     *
     * @var string
     */
    private $assunto;

    /**
     *
     * @var string
     */
    private $corpoEmail;

    /**
     *
     * @var string
     */
    private $remetente;

    /**
     *
     * @var array
     */
    private $destinatarios;

    /**
     *
     * @var array
     */
    private $destinatariosCopia;

    /**
     *
     * @var Swift_SmtpTransport
     */
    private $transport;

    /**
     *
     * @var Swift_Message
     */
    private $message;

    /**
     *
     * @var array
     */
    private $streamOptions = [
        'ssl' => [
            'allow_self_signed' => true,
            'verify_peer' => false
        ]
    ];

    /**
     * Construtor privado para garantir o singleton.
     */
    private function __construct()
    {
        $this->inicializarTransport();
        $this->inicializarMessage();
    }

    /**
     * Inicializa o atributo 'Transport' com os parâmetros relevantes ao ambiente.
     */
    private function inicializarTransport()
    {
        $host = env('MAIL_HOST');
        $port = env('MAIL_PORT', 25);
        $newTransport = new Swift_SmtpTransport($host, $port);

        // Username
        $username = env('MAIL_USERNAME', null);

        if ($username != null) {
            $newTransport->setUsername($username);
        }

        // Password
        $password = env('MAIL_PASSWORD', null);

        if ($password != null) {
            $newTransport->setPassword($password);
        }

        // Encryption(
        $encryption = env('MAIL_ENCRYPTION', null);

        if ($encryption != null) {
            $newTransport->setEncryption($encryption);
        }

        // Disable SSL validate.
        if ($encryption == 'tls') {
            $newTransport->setStreamOptions($this->streamOptions);
        }

        // AuthMode
        $authmode = env('MAIL_AUTHMODE', null);

        if ($authmode != null) {
            $newTransport->setAuthMode($authmode);
        }

        $this->transport = $newTransport;
    }

    /**
     * Inicializa o atributo 'Message'
     */
    private function inicializarMessage()
    {
        $this->message = new Swift_Message();
    }

    /**
     * Fabrica de instância de 'Email'.
     *
     * @return Email
     */
    public static function newInstance()
    {
        return new Email();
    }

    /**
     * Envia o email conforme os dados informados.
     */
    public function enviar()
    {
        $this->prepararMessage();

        $mailer = new Swift_Mailer($this->transport);
        $mailer->send($this->message);
    }

    /**
     * Prepara a mensagem para realizar o envio
     */
    private function prepararMessage()
    {
        $this->message->setSubject($this->assunto);

        $remetente = array();
        $remetente[] = $this->getRemetente();
        $this->message->setFrom($remetente);
        $this->destinatarios = self::organizeDestinatariosToSend($this->destinatarios);

        if (AppConfig::isEnvPrd()) {
            count($this->destinatarios) > 1 ? $this->message->setBcc($this->destinatarios) : $this->message->setTo($this->destinatarios);

            if (!empty($this->getDestinatariosCopia())) {
                $this->message->setCc($this->destinatariosCopia);
            }
        } else {
            count(AppConfig::getEmailsDestinariosTeste()) > 1 ? $this->message->setBcc(AppConfig::getEmailsDestinariosTeste()) : $this->message->setTo(AppConfig::getEmailsDestinariosTeste());

            if (!empty($this->getDestinatariosCopia())) {
                $this->message->setCc(AppConfig::getEmailsDestinariosTeste());
            }
            $this->corpoEmail .= $this->getRealMailContextHtml($this->destinatarios, $this->destinatariosCopia);
        }

        $this->message->setBody($this->corpoEmail, 'text/html');
    }

    /**
     * Método utilado para incorporar uma imagem ao e-mail e retorna o ID para ser usado no atributo src
     *
     * @param string $path
     * @return string
     */
    public function incluirImagemInline($path)
    {
        return $this->message->embed(Swift_Image::fromPath($path));
    }

    /**
     *
     * @param string $remetente
     * @return Email
     */
    public function setRemetente($remetente)
    {
        $this->remetente = $remetente;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getRemetente()
    {
        return empty($this->remetente) ? env('MAIL_SENDER') : $this->remetente;
    }

    /**
     *
     * @param string $email
     * @return Email
     */
    public function setDestinatario($email)
    {
        if (empty($this->destinatarios)) {
            $this->destinatarios = [];
        }

        $this->destinatarios[] = $email;
        return $this;
    }

    /**
     *
     * @param array $emails
     */
    public function setDestinatarios(array $emails)
    {
        if (empty($this->destinatarios)) {
            $this->destinatarios = [];
        }

        $this->destinatarios = array_merge($this->destinatarios, $emails);
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getDestinatarios()
    {
        return $this->destinatarios;
    }

    /**
     *
     * @return array
     */
    public function getDestinatariosCopia()
    {
        return $this->destinatariosCopia;
    }

    /**
     *
     * @param array $emails
     * @return Email
     */
    public function setDestinatariosCopia(array $emails)
    {
        if (empty($this->destinatariosCopia)) {
            $this->destinatariosCopia = [];
        }

        $this->destinatariosCopia = array_merge($this->destinatariosCopia, $emails);
        return $this;
    }

    /**
     *
     * @param string $assunto
     * @return Email
     */
    public function setAssunto($assunto)
    {
        $this->assunto = $assunto;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getAssunto()
    {
        return $this->assunto;
    }

    /**
     *
     * @param string $corpoEmail
     * @return Email
     */
    public function setCorpoEmail($corpoEmail)
    {
        $this->corpoEmail = $corpoEmail;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getCorpoEmail()
    {
        return $this->corpoEmail;
    }

    public static function enviarMail(AtividadeSecundariaMail $mail)
    {
        $mail->email->setDestinatarios(self::organizeDestinatariosToSend($mail->email->getDestinatarios()));
        if (!AppConfig::isEnvPrd()) {
            $textoCorpo = $mail->email->getTextoCorpo();
            $mail->email->setTextoCorpo($textoCorpo. self::getRealMailContextHtml($mail->email->getDestinatarios()));
            $mail->email->setDestinatarios(AppConfig::getEmailsDestinariosTeste());
        }

        Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Iniciou envio');
        if (!empty($mail->email->getDestinatarios())){
            if (!AppConfig::isEnvPrd()) {
                Log::info('ENVIO EMAIL IMPUGNACAO RESULTADO: Iniciou envio ', ['emails' => print_r
                ($mail->email->getDestinatarios
                (), true)]);
                Mail::to($mail->email->getDestinatarios())->send($mail);
            }
            else {
                foreach ($mail->email->getDestinatarios() as $destinatario) {
                    $mail->to = [
                        ["address" => $destinatario, "name"=> ""]
                    ];
                    Mail::send($mail);
                }
            }
        }
    }

    /**
     * Recebe um array de destinatarios, e valida se nao a uma sub-estrutura com um attr email
     * @param $destinatarios
     * @return null
     */
    public static function organizeDestinatariosToSend($destinatarios) {
        $organizedMails = null;
        if(!empty($destinatarios)) {
            foreach($destinatarios as $destinatario) {
                $validMail = $destinatario;
                if(is_array($destinatario)) {
                    $validMail = $destinatario['email'];
                }
                $organizedMails[] = $validMail;
            }
        }

        $organizedMails = !empty($organizedMails) && sizeof($organizedMails) > 0 ? array_values(array_unique($organizedMails)) : $organizedMails;

        return $organizedMails;
    }

    /**
     * Processa a lista de destinatarios / destinatariosCopia e retorna uma string html com ul
     * @return string
     */
    public static function getRealMailContextHtml($destinatarios, $destsCopy = null) {
        $htmlContext = " <br /> <hr /> <h2>Este e-mail seria enviado para: </h2>";
        $listDest = "";

        foreach($destinatarios as $destinatario) {
            $listDest .= "<li>{$destinatario}</li>";
        }

        $htmlContext .= "<ul>{$listDest}</ul>";

        $listDest = "";
        if(!empty($destsCopy)) {
            foreach($destsCopy as $destinatarioCopia) {
                $listDest .= "<li>{$destinatarioCopia}</li>";
            }
            $htmlContext .= "<h3>Lista Cc: </h3><ul>{$listDest}</ul>";
        }

        return $htmlContext;
    }
}
