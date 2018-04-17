<?php

namespace NotificationBundle\Mailer;

interface MailerRecipientsInterface
{
    /**
     * @return MailerRecipientInterface[]
     */
    public function getRecipients();
}
