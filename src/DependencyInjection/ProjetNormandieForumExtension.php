<?php

namespace ProjetNormandie\ForumBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Exception;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class ProjetNormandieForumExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('admin.yml');
        $loader->load('services.yml');
        $loader->load('services/controllers.yml');
        $loader->load('services/doctrine_extension.yml');
        $loader->load('services/event_listener.yml');
        $loader->load('services/managers.yml');
        $loader->load('services/repositories.yml');
    }
}
