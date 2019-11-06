<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 */

namespace Etrias\CqrsBundle\DependencyInjection;

use Etrias\CqrsBundle\Cache\NameStrategy\ReflectionStrategy;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('etrias_cqrs');

        $rootNode
            ->isRequired()
            ->children()
                ->arrayNode('cache')
                    ->children()
                        ->scalarNode('items_adapter')
                            ->isRequired()
                            ->info('service to store items (Symfony\Component\Cache\Adapter\AdapterInterface)')
                        ->end()
                        ->scalarNode('tags_adapter')
                            ->isRequired()
                            ->info('service to store tags (Symfony\Component\Cache\Adapter\AdapterInterface)')
                        ->end()
                        ->scalarNode('encoder')
                            ->defaultValue('serialize')
                            ->isRequired()
                            ->info('service to store tags (Symfony\Component\Cache\Adapter\AdapterInterface)')
                        ->end()
                        ->arrayNode('default')
                            ->isRequired()
                            ->children()
                                ->integerNode('expires_after')
                                    ->defaultValue(0)
                                    ->info('TTL in seconds')
                                ->end()
                                ->scalarNode('expires_at')
                                    ->defaultNull()
                                    ->info('A datetime string format')
                                ->end()
                                ->scalarNode('name_strategy')
                                    ->defaultValue(ReflectionStrategy::class)
                                ->end()
                                ->scalarNode('name_xpr')->defaultNull()->end()
                                ->arrayNode('tags')
                                    ->prototype('scalar')->end()
                                    ->info('Expression format (services and security is available)')
                                ->end()
                            ->end()
                        ->end()
                    ->arrayNode('commands')
                        ->useAttributeAsKey('command')
                        ->arrayPrototype()
                            ->children()
                                ->integerNode('expires_after')
                                    ->defaultValue(0)
                                    ->info('TTL in seconds')
                                ->end()
                                ->scalarNode('expires_at')
                                    ->defaultNull()
                                    ->info('A datetime string format')
                                ->end()
                                ->scalarNode('name_strategy')
                                    ->defaultValue(ReflectionStrategy::class)
                                    ->end()
                                ->scalarNode('name_xpr')->defaultNull()->end()
                                ->arrayNode('tags')
                                    ->scalarPrototype()
                                    ->info('Expression format (services and security is available)')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
