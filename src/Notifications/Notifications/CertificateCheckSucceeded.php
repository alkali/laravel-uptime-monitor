<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use NotificationChannels\Telegram\TelegramMessage;
use Spatie\UptimeMonitor\Notifications\BaseNotification;
use Spatie\UptimeMonitor\Events\CertificateCheckSucceeded as ValidCertificateFoundEvent;

class CertificateCheckSucceeded extends BaseNotification
{
    /** @var \Spatie\UptimeMonitor\Events\CertificateCheckSucceeded */
    public $event;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject($this->getMessageText())
            ->line($this->getMessageText());

        foreach ($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name.': '.$value);
        }

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title($this->getMessageText())
                    ->content("Expires {$this->getMonitor()->formattedCertificateExpirationDate('forHumans')}")
                    ->fallback($this->getMessageText())
                    ->footer($this->getMonitor()->certificate_issuer)
                    ->timestamp(Carbon::now());
            });
    }

    public function toTelegram($notifiable)
    {
        return (new TelegramMessage())
            ->content("\u{2705} *{$this->getMessageText()}*\nExpires {$this->getMonitor()->formattedCertificateExpirationDate('forHumans')}");
    }

    public function setEvent(ValidCertificateFoundEvent $event)
    {
        $this->event = $event;

        return $this;
    }

    public function getMessageText(): string
    {
        return "SSL certificate for {$this->event->monitor->url} is valid";
    }
}
