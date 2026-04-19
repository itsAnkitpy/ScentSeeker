<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $sellerName,
        public string $resetUrl
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Welcome to ScentCents — Your Seller Portal Account")
            ->greeting("Hello {$notifiable->username},")
            ->line("An account has been created for you on the ScentCents Seller Portal for **{$this->sellerName}**.")
            ->line('To get started, please set your password using the link below:')
            ->action('Set Your Password', $this->resetUrl)
            ->line('This link will expire in 60 minutes.')
            ->line('Once you set your password, you can log in at ' . url('/seller') . ' to upload your product data and track your prices.')
            ->salutation('Welcome aboard!');
    }
}
