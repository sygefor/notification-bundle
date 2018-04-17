<?php

namespace NotificationBundle\Notification;

use Monolog\Logger;

/**
 * Class NotificationDispatcher.
 */
class NotificationDispatcher
{
    /**
     * @var NotificationRegistry
     */
    protected $registry;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * NotificationDispatcher constructor.
     *
     * @param NotificationRegistry $registry
     * @param Logger               $logger
     * @param \Twig_Environment    $twig
     */
    public function __construct(NotificationRegistry $registry, Logger $logger, \Twig_Environment $twig)
    {
        $this->registry = $registry;
        $this->logger = $logger;
        $this->twig = $twig;
    }

    /**
     * @param $type
     * @param int|null $level
     */
    public function dispatch($type, $subject, $level = null)
    {
        $notification = $this->registry->getType($type);
        $notification->setSubject($subject);

        $context = $notification->getContext();
        $context['notification'] = $notification;

        $this->logger->addRecord($level ? $level : $notification->getLevel(), $notification->getMessage(), $context);
    }
}
