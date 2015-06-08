<?php

namespace Matyotools\DominoBundle\DependencyInjection;

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
class MatyotoolsDominoExtension extends Extension
{
	protected $resources = array(
		'domino' => 'domino.xml',
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
			$container->setAlias($config['alias'], 'matyotools_domino');
		}

		foreach (array('user', 'password') as $attribute) {
			if (isset($config[$attribute])) {
				$container->setParameter('matyotools_domino.'.$attribute, $config[$attribute]);
				$container->setParameter('matyotools_domino_reports.'.$attribute, $config[$attribute]);
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
