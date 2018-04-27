<?php

namespace NotificationBundle\Notification;

use NotificationBundle\Mailer\MailerRecipientInterface;
use NotificationBundle\Notification\ORM\NotificationEntityInterface;

/**
 * Interface NotificationTypeInterface.
 */
interface NotificationTypeInterface
{
    /**
     * @param $subject
     */
    public function setSubject($subject);

    /**
     * @return mixed
     */
    public function getSubject();

    /**
     * @return array
     */
    public function getContext();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getRoute();

    /**
     * @return array
     */
    public function getRouteParameters();

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @return MailerRecipientInterface[]
     */
    public function getRecipients();

    /**
     * @return MailerRecipientInterface[]
     */
    public function getExcludedRecipients();

    /**
     * @return array|NotificationEntityInterface
     */
    public function getEntity();
}
