<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CodigoRecuperacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombreDestinatario,
        public string $codigo,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Código de recuperación de contraseña - VOID',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.codigo-recuperacion',
            text: 'emails.codigo-recuperacion-texto',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
