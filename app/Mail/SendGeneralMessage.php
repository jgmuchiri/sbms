<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendGeneralMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $fromUser;
    public $user;
    public $msg;

    /**
     * SendGeneralMessage constructor.
     *
     * @param string $from
     * @param string $user
     * @param string $msg
     */
    public function __construct($from='', $user = '',$msg='')
    {
        $this->user = $user;
        $this->message = $msg;
        $this->fromUser=$from;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->fromUser)
            ->view('emails.general');
    }
}
