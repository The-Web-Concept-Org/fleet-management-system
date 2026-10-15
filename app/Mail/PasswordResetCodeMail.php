<?php

namespace App\Mail;

use App\Models\Driver;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent when a driver requests a password reset. Carries the one-time code
 * they enter in the app to choose a new password.
 */
class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Driver $driver,
        public string $code,
        public int $expiresInMinutes,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your ' . config('app.name') . ' password reset code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset-code',
            with: [
                'driverName' => $this->driver->user?->name ?? 'Driver',
                'loginEmail' => $this->driver->user?->email,
                'code' => $this->code,
                'expiresInMinutes' => $this->expiresInMinutes,
                'appName' => config('app.name'),
            ],
        );
    }
}
