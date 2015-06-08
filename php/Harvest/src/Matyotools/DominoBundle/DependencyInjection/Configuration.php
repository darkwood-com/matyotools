<?php

namespace Matyotools\DominoBundle\DependencyInjection;

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
		$rootNode = $treeBuilder->root('matyotools_domino');

		$rootNode
			->children()
			->scalarNode('user')->isRequired()->cannotBeEmpty()->end()
			->scalarNode('password')->isRequired()->cannotBeEmpty()->end()
			->end()
		;

        return $treeBuilder;
    }
}
