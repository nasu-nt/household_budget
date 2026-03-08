<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $token
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
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expireMinutes = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        return (new MailMessage)
            ->subject('【HOUSEHOLD_BUDGET】Reset Password Notification')
            ->greeting('こんにちは、' . $notifiable->name . 'さん！')
            ->line('パスワード再設定のリクエストを受け付けました。')
            ->line('下のボタンから、パスワードを再設定してください。')
            ->action('Reset Password', $url)
            ->line("このリンクの有効期限は{$expireMinutes}分です。")
            ->line('このメールに心当たりがない場合は、対応不要です。');
    }
}