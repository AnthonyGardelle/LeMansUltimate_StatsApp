<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return new ResetPasswordMail($this->token, $notifiable);
    }
}

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $user;
    public $resetUrl;
    public $expireTime;

    public function __construct($token, $user)
    {
        $this->token = $token;
        $this->user = $user;
        $this->resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->getEmailForPasswordReset(),
        ], false));
        $this->expireTime = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');
    }

    public function build()
    {
        return $this->to($this->user->email)
            ->subject('🔐 Réinitialisation de votre mot de passe - Le Mans Ultimate Stats App')
            ->view('emails.reset-password')
            ->withHeaders([
                'List-Unsubscribe' => '<mailto:unsubscribe@le-mans-ultimate-stats-app.com>',
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
            ]);
    }
}