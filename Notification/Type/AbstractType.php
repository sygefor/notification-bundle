<?php

namespace NotificationBundle\Notification\Type;

use Monolog\Logger;
use NotificationBundle\Notification\NotificationTypeInterface;

/**
 * Class AbstractType.
 */
abstract class AbstractType implements NotificationTypeInterface
{
    protected $subject;

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    abstract public function getMessage();

    public function getLevel()
    {
        return Logger::NOTICE;
    }

    public function getContext()
    {
        return array();
    }

    public function getDescription()
    {
        return null;
    }

    public function getRoute()
    {
        return null;
    }

    public function getRouteParameters()
    {
        return [];
    }

    public function getRecipients()
    {
        return null;
    }

    public function getExcludedRecipients()
    {
        return null;
    }

    public function getEntity()
    {
        return null;
    }
}
