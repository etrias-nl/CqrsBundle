<?php

declare(strict_types=1);

namespace Etrias\CqrsBundle\DependencyInjection;

use DateTime;
use Etrias\CqrsBundle\Cache\CacheConfig;
use Etrias\CqrsBundle\Cache\CacheConfigRegistry;
use Etrias\CqrsBundle\Cache\CacheMiddleware;
use Etrias\CqrsBundle\Cache\NameStrategy\ExpressionStrategy;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class EtriasCqrsExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.xml');


        $commandCache = $container->getDefinition('etrias.cqrs.command.cache');
        $commandCache->setArgument(0, new Reference($mergedConfig['cache']['items_adapter']));
        $commandCache->setArgument(1, new Reference($mergedConfig['cache']['tags_adapter']));

        $this->configEncoder($container, $mergedConfig);

        $registryDefinition = $container->getDefinition(CacheConfigRegistry::class);

        $defaultConfig = $mergedConfig['cache']['default'];
        $this->addCacheConfig('default', $defaultConfig, $container, $registryDefinition);

        $cacheConfig = $mergedConfig['cache']['commands'];
        foreach ($cacheConfig as $commandClassName => $config) {
            $this->addCacheConfig($commandClassName, $config, $container, $registryDefinition);
        }
    }

    protected function addCacheConfig(string $commandClassName, array $config, ContainerBuilder $container, Definition $registryDefinition)
    {
        if ($config['name_xpr'] !== null) {
            $nameStrategy = new Definition(ExpressionStrategy::class, [$config['name_xpr']]);
        } else if ($container->has($config['name_strategy'])) {
            $nameStrategy = $container->getDefinition($config['name_strategy']);
        } else {
            throw new InvalidConfigurationException('The given name_strategy service id does not exist for command "'.$commandClassName.'".');
        }

        $configDefinition = new Definition(CacheConfig::class, [$nameStrategy]);

        if ($config['expires_at'] !== null) {
            $configDefinition->addMethodCall('setExpiresAt', [new DateTime($config['expires_at'])]);
        } else if ($config['expires_after'] !== null) {
            $configDefinition->addMethodCall('setExpiresAfter', [$config['expires_after']]);
        } else {
            throw new InvalidConfigurationException('You have to specify "expires_at" or "expires_after for command "'.$commandClassName.'"."');
        }

        $configDefinition->addMethodCall('setTagExpressions', [$config['tags']]);


        $registryDefinition->addMethodCall('addConfig', [$commandClassName, $configDefinition]);
    }

    protected function configEncoder(ContainerBuilder $container, array $config)
    {
        $cacheMiddleware = $container->getDefinition(CacheMiddleware::class);
        $cacheMiddleware->setArgument(3, $config['cache']['encoder']);
    }
}
