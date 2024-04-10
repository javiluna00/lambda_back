<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;
    public string $name, $email, $reset_password_token;
    /**
     * Create a new message instance.
     */
    public function __construct(string $user_id)
    {
        $this->user = User::find($user_id)->first();
        if($this->user == null){
            return;
        }
        else{
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->reset_password_token = $this->user->reset_password_token;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Lambda Beats] Restablece tu contraseña',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ForgotPassword',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
