<?php

namespace App\Notifications;

use App\Models\PriceAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PriceDropNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public PriceAlert $alert,
        public float $currentPrice
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $perfume = $this->alert->perfume;
        $savings = $this->alert->target_price - $this->currentPrice;

        return (new MailMessage)
            ->subject("🎉 Price Alert: {$perfume->name} dropped to ₹{$this->currentPrice}!")
            ->greeting("Great news, {$notifiable->username}!")
            ->line("A perfume you're watching has dropped below your target price.")
            ->line('')
            ->line("**{$perfume->name}**")
            ->line("by {$perfume->brand}")
            ->line('')
            ->line("**Current Price:** ₹" . number_format((float) $this->currentPrice, 2))
            ->line("**Your Target:** ₹" . number_format((float) $this->alert->target_price, 2))
            ->line($savings > 0 ? "**You save:** ₹" . number_format((float) $savings, 2) : '')
            ->action('View Perfume', url("/perfumes/{$perfume->id}"))
            ->line('')
            ->line('This alert has been triggered and will not notify you again unless you reset it.')
            ->salutation('Happy Shopping! 🛒');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'perfume_id' => $this->alert->perfume_id,
            'perfume_name' => $this->alert->perfume->name,
            'current_price' => $this->currentPrice,
            'target_price' => $this->alert->target_price,
        ];
    }
}
