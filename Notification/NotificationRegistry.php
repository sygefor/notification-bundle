<?php

namespace NotificationBundle\Notification;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NotificationRegistry.
 */
class NotificationRegistry
{
    private $container;

    /**
     * @var NotificationTypeInterface[]
     */
    private $types = array();

    /**
     * NotificationRegistry constructor.
     *
     * @param ContainerInterface          $container
     * @param NotificationTypeInterface[] $types
     */
    public function __construct(ContainerInterface $container, array $types)
    {
        $this->container = $container;
        $this->types = $types;
    }

    /**
     * @return NotificationTypeInterface
     */
    public function getType($name)
    {
        if (!isset($this->types[$name])) {
            // Support fully-qualified class names
            if (class_exists($name) && in_array(NotificationTypeInterface::class, class_implements($name))) {
                $type = new $name();
            } else {
                throw new \InvalidArgumentException(sprintf('Could not load type "%s"', $name));
            }
            $this->types[$name] = $type;
        }

        if (is_string($this->types[$name])) {
            $this->types[$name] = $this->container->get($this->types[$name]);
        }

        return $this->types[$name];
    }
}
