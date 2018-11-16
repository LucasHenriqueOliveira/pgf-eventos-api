<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangePassword extends Mailable
{
    use Queueable, SerializesModels;
    public $email;
    public $message;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $message)
    {
        $this->email = $email;
        $this->message = $message;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('changePassword')->with([
            'email' => $this->email,
            'message' => $this->message
        ]);
    }
}