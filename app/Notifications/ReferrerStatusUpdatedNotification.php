<?php

namespace App\Notifications;

use App\Models\Referrer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReferrerStatusUpdatedNotification extends Notification
{
    use Queueable;

    public Referrer $referrer;

    public function __construct(Referrer $referrer)
    {
        $this->referrer = $referrer;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $status = ucfirst($this->referrer->status);
        $mail = (new MailMessage)
            ->subject("Referrer Account Status: {$status}")
            ->line("Hello {$this->referrer->full_name},")
            ->line("Your referrer account status has been updated to: {$status}.");

        if ($this->referrer->admin_remarks) {
            $mail->line("Admin Remarks: {$this->referrer->admin_remarks}");
        }

        if ($this->referrer->status === 'approved') {
            $code = $this->referrer->coupon?->code ?? 'N/A';
            $mail->line("Your unique referral code is: {$code}")
                ->action('Login to Referrer Dashboard', route('referrer.login'));
        }

        return $mail;
    }

    public function toArray($notifiable): array
    {
        return [
            'referrer_id' => $this->referrer->id,
            'status' => $this->referrer->status,
            'remarks' => $this->referrer->admin_remarks,
            'message' => "Your referrer status is now {$this->referrer->status}.",
        ];
    }
}
