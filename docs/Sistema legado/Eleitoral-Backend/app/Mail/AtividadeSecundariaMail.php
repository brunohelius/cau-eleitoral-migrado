<?php

namespace App\Mail;

use App\To\EmailTO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AtividadeSecundariaMail extends Mailable
{
    use SerializesModels;

    /**
     *
     * @var EmailTO
     */
    public $email;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $email
     */
    public function __construct(EmailTO $email)
    {
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.padrao')->subject($this->email->getAssunto());
    }
}
