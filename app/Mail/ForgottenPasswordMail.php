<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ForgottenPasswordMail
 * Mail sent to the users that start the password reset process
 * @package App\Mail
 */
class ForgottenPasswordMail extends Mailable
{
    use Queueable, SerializesModels;
    public string $pwd;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $pwd)
    {
        $this->pwd = $pwd;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        $this->theme = 'blue-grey';
        return $this->markdown('emails.forgotten-password')
            ->with(['pwd' => $this->pwd]);
    }
}
