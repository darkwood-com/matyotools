<?php

namespace Matyotools\HarvestAppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('matyotools_harvest_app');

        $rootNode
            ->children()
            ->scalarNode('user')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('password')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('account')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('ssl')->defaultValue(true)->end()
            ->scalarNode('mode')->defaultValue('FAIL')->end()
            ->scalarNode('alias')->defaultNull()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
