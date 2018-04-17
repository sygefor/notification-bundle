<?php

namespace NotificationBundle\Notification\Handler;

use NotificationBundle\Mailer\Mailer;
use Monolog\Handler\AbstractProcessingHandler;
use NotificationBundle\Notification\NotificationTypeInterface;

/**
 * Class MailHandler.
 */
class MailerHandler extends AbstractProcessingHandler
{
    /** @var Mailer */
    private $mailer;

    /**
     * @param Mailer $mailer
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    protected function write(array $record)
    {
        $context = $record['context'];
        if (!isset($context['notification'])) {
            return false;
        }

        /** @var NotificationTypeInterface $notification */
        $notification = $context['notification'];

        // get recipients
        $recipients = $notification->getRecipients();

        // send mail
        if ($recipients && count($recipients)) {
            $this->mailer->send('notification', $recipients, $record);
        }
    }
}
