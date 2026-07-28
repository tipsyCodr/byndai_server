<?php

namespace App\Notifications;

use App\Models\CommissionPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CommissionPayoutNotification extends Notification
{
    use Queueable;

    public CommissionPayout $payout;

    public function __construct(CommissionPayout $payout)
    {
        $this->payout = $payout;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Referral Payout Processed')
            ->line("Your payout of ₹{$this->payout->amount} has been processed via {$this->payout->payment_method}.")
            ->line("Transaction Reference: " . ($this->payout->transaction_reference ?: 'N/A'))
            ->action('View Earnings History', route('referrer.dashboard'));
    }

    public function toArray($notifiable): array
    {
        return [
            'payout_id' => $this->payout->id,
            'amount' => $this->payout->amount,
            'reference' => $this->payout->transaction_reference,
            'message' => "Payout of ₹{$this->payout->amount} processed.",
        ];
    }
}
