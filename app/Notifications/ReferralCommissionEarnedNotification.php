<?php

namespace App\Notifications;

use App\Models\ReferralSale;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReferralCommissionEarnedNotification extends Notification
{
    use Queueable;

    public ReferralSale $sale;

    public function __construct(ReferralSale $sale)
    {
        $this->sale = $sale;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Referral Commission Earned!')
            ->line("Congratulations! A user purchased the {$this->sale->plan?->name} plan using your coupon code.")
            ->line("Commission Earned: ₹{$this->sale->commission_amount}")
            ->action('View Referrer Dashboard', route('referrer.dashboard'));
    }

    public function toArray($notifiable): array
    {
        return [
            'sale_id' => $this->sale->id,
            'commission_amount' => $this->sale->commission_amount,
            'plan_name' => $this->sale->plan?->name,
            'message' => "Earned ₹{$this->sale->commission_amount} commission for referral sale.",
        ];
    }
}
