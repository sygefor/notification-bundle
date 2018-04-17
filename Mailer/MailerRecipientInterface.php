<?php

namespace NotificationBundle\Mailer;

interface MailerRecipientInterface
{
    public function getEmail();
}
