<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
    public function __construct(
        private string $token
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        ], absolute: false));

        $passwordBroker = (string) config('auth.defaults.passwords');
        $expireMinutes = (int) config(
            "auth.passwords.{$passwordBroker}.expire",
            60
        );

        $appName = str_replace(
            '_',
            ' ',
            (string) config('app.name', 'Household Budget')
        );

        $name = trim((string) ($notifiable->name ?? ''));

        $greeting = $name !== ''
            ? __('notifications.password_reset.greeting', ['name' => $name])
            : __('notifications.password_reset.greeting_without_name');

        return (new MailMessage)
            ->subject(__('notifications.password_reset.subject', [
                'app' => $appName,
            ]))
            ->greeting($greeting)
            ->line(__('notifications.password_reset.request_received'))
            ->line(__('notifications.password_reset.instructions'))
            ->action(
                __('notifications.password_reset.action'),
                $url
            )
            ->line(trans_choice(
                'notifications.password_reset.expires',
                $expireMinutes,
                ['count' => $expireMinutes]
            ))
            ->line(__('notifications.password_reset.ignore'))
            ->salutation(__('notifications.password_reset.salutation', [
                'app' => $appName,
            ]));
    }
}