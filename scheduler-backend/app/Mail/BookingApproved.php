<?php

namespace App\Mail;

use App\Models\BookingRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $user;
    public $resourceName;

    public function __construct(BookingRequest $booking, User $user)
    {
        $this->booking = $booking;
        $this->user = $user;
        $this->resourceName = $booking->resource ? $booking->resource->name : 'Unknown Resource';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Approved — ' . $this->resourceName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking.approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
