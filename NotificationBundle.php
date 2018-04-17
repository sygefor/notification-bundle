<?php

namespace NotificationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use NotificationBundle\DependencyInjection\Compiler\NotificationPass;

class NotificationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new NotificationPass());
    }
}
