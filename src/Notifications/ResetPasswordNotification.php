<?php

namespace MetaverseSystems\AuthApi\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    // This notification is sent as an email
    public function via($notifiable)
    {
        return ['mail'];
    }

    // This method returns the email representation of the notification
    public function toMail($notifiable)
    {
        $url = url(config('metaverse-auth-api.app-domain').'/password/reset/'.$this->token.'?email='.urlencode($this->email));

        return (new MailMessage)
            ->subject('Reset Password Notification')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $url)
            ->line('If you did not request a password reset, no further action is required.');
    }
}
