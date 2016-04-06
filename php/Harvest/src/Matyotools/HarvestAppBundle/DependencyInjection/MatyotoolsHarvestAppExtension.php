<?php

namespace Matyotools\HarvestAppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MatyotoolsHarvestAppExtension extends Extension
{
    protected $resources = array(
        'harvest_app' => 'harvest_app.xml',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $this->loadDefaults($container);

        if (isset($config['alias'])) {
            $container->setAlias($config['alias'], 'matyotools_harvest_app');
        }

        if (isset($config['domino_projects'])) {
            $container->setParameter('matyotools_domino_app.projects', $config['domino_projects']);
        }

        foreach (array('user', 'password', 'account', 'mode') as $attribute) {
            if (isset($config[$attribute])) {
                $container->setParameter('matyotools_harvest_app.'.$attribute, $config[$attribute]);
                $container->setParameter('matyotools_harvest_app_reports.'.$attribute, $config[$attribute]);
            }
        }
    }

    protected function loadDefaults($container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(array(__DIR__.'/../Resources/config', __DIR__.'/Resources/config')));

        foreach ($this->resources as $resource) {
            $loader->load($resource);
        }
    }
}
