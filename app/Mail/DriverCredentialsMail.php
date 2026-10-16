<?php

namespace App\Mail;

use App\Models\Driver;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent when an admin creates a driver account, carrying the login credentials.
 */
class DriverCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Driver $driver,
        public string $plainPassword,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Fleet Operations driver account',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.driver-credentials',
            with: [
                'driverName' => $this->driver->user?->name ?? 'Driver',
                'badgeId' => $this->driver->display_id,
                'loginEmail' => $this->driver->user?->email,
                'loginPassword' => $this->plainPassword,
                'loginUrl' => url('/login'),
            ],
        );
    }
}
