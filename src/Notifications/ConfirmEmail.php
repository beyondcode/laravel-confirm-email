<?php

namespace BeyondCode\EmailConfirmation\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class ConfirmEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('confirmation::confirmation.confirmation_subject'))
            ->line(__('confirmation::confirmation.confirmation_subject_title'))
            ->line(__('confirmation::confirmation.confirmation_body'))
            ->action(__('confirmation::confirmation.confirmation_button'), $this->confirmationUrl($notifiable));
    }

    protected function confirmationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            "auth.confirm",
            now()->addMinutes(config('confirmation.timeout',60)),
            ['id' => $notifiable->getKey()]
        );
    }
}
