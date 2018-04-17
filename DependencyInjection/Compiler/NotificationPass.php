<?php

namespace NotificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Class NotificationPass.
 */
class NotificationPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('notification.registry')) {
            return;
        }

        $definition = $container->getDefinition('notification.registry');

        // Builds an array with fully-qualified type class names as keys and service IDs as values
        $types = array();
        foreach ($container->findTaggedServiceIds('notification.type') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new InvalidArgumentException(sprintf('The service "%s" must be public as notification types are lazy-loaded.', $serviceId));
            }

            // Support type access by FQCN
            $types[$serviceDefinition->getClass()] = $serviceId;
        }

        $definition->replaceArgument(1, $types);
    }
}
