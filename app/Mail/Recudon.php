<?php

namespace App\Mail;

use App\Models\Don;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecuDon extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Don $don) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reçu de votre don — Merci pour votre générosité 💚',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recu-don',
            with: [
                'montant'   => $this->don->montant,
                'frequence' => $this->don->frequence,
                'message'   => $this->don->message,
                'donId'     => $this->don->id,
                'date'      => $this->don->created_at->format('d/m/Y'),
            ],
        );
    }
}
