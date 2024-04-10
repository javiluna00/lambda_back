<?php

namespace App\Mail;

use App\Models\BeatLicense;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuccessfulPurchaseLogged extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;


    /**
     * Create a new message instance.
     */
    public function __construct(public string $title, public string $user_id, public array $licenses_bought)
    {
        $this->title = $title;
        $this->user = User::find($user_id)->first();
        $this->licenses_bought = $licenses_bought;


    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.SuccessfulPurchaseLogged',
        );
    }
    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // $res = [];
        // foreach ($this->licenses as $license) {
        //     $licenseDB = BeatLicense::where('id', $license['id'])->first();
        //     $res = $res + Attachment::fromStorage($licenseDB->file_url);
        // }

        return [];
    }
}
