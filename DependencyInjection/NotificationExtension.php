<?php

namespace NotificationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class NotificationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $files = [
            'config.yml',
        ];

        foreach ($files as $file) {
            $loader->load($file);
        }

        $this->loadMailerConfig($config['mailer'], $container);
    }

    /**
     * Load mailer config.
     */
    private function loadMailerConfig(array $config, ContainerBuilder $container)
    {
        $container->setParameter('mailer.sender_name', $config['sender']['name']);
        $container->setParameter('mailer.sender_address', $config['sender']['address']);
        $container->setParameter('mailer.emails', $config['emails']);
        $container->setParameter('mailer.subject_template', @$config['subject_template']);
    }
}
