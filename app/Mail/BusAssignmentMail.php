<?php

namespace App\Mail;

use App\Models\Bus;
use App\Models\Driver;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent when a bus is assigned to a driver. Carries the bus details and a
 * reminder of the driver's login email (the password is issued separately
 * when the account is created).
 */
class BusAssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Bus $bus,
        public Driver $driver,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bus #' . $this->bus->bus_number . ' has been assigned to you',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bus-assigned',
            with: [
                'driverName' => $this->driver->user?->name ?? 'Driver',
                'loginEmail' => $this->driver->user?->email,
                'loginUrl' => url('/login'),
                'busNumber' => $this->bus->bus_number,
                'busPlate' => $this->bus->plate,
                'busModel' => $this->bus->model,
                'busRoute' => $this->bus->route,
                'busStatus' => $this->bus->status,
            ],
        );
    }
}
