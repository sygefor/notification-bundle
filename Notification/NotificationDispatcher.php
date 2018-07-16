<?php

namespace NotificationBundle\Notification;

use Monolog\Logger;
use Doctrine\ORM\EntityManager;
use NotificationBundle\Notification\Type\AbstractType;

/**
 * Class NotificationDispatcher.
 */
class NotificationDispatcher
{
    /**
     * @var EntityManager
     */
    protected $manager;

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
     * @param EntityManager $manager
     * @param NotificationRegistry $registry
     * @param Logger               $logger
     * @param \Twig_Environment    $twig
     */
    public function __construct(EntityManager $manager, NotificationRegistry $registry, Logger $logger, \Twig_Environment $twig)
    {
        $this->manager = $manager;
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
        /** @var AbstractType $notification */
        $notification = $this->registry->getType($type);
        $notification->setManager($this->manager);
        $notification->setSubject($subject);

        $context = $notification->getContext();
        $context['notification'] = $notification;

        $this->logger->addRecord($level ? $level : $notification->getLevel(), $notification->getMessage(), $context);
    }
}
