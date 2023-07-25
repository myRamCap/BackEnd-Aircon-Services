<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingInTransit extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $rn;
    public $sc;
    public $services;
    public $sc_address;
    public $booking_date;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $rn, $sc, $services, $sc_address, $booking_date)
    {
        $this->email = $email;
        $this->rn = $rn;
        $this->sc = $sc;
        $this->services = $services;
        $this->sc_address = $sc_address;
        $this->booking_date = $booking_date;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.bookingInTransit',
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
