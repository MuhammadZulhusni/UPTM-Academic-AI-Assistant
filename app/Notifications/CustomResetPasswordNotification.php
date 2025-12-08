<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPasswordNotification
{
    /**
     * Build the mail message.
     */
    public function toMail($notifiable): MailMessage
    {
        // Generates the correct URL for the reset link
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            // UPTM Branded Subject
            ->subject(__('UPTM Account Password Reset'))

            // Custom, Minimalist, User-Friendly Content
            ->greeting('Hello!')
            ->line('You recently requested to reset the password for your UPTM Academic AI Assistant account.') 
            ->line('Please click the button below to complete the process:')

            // Custom Action Button
            ->action(__('Reset My Password'), $resetUrl)

            // Custom Security/Expiry Notice
            ->line('For security, this password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, you can safely ignore this email.')

            ->salutation('Best Regards, UPTM University');
    }
}