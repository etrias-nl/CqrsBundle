<?php

declare(strict_types=1);


namespace Etrias\CqrsBundle\DependencyInjection\Compiler;


use Etrias\CqrsBundle\Cache\TagProvider\CacheTagProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @deprecated use DI tags + autoconfigure
 */
class CacheTagProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $definition) {
            if(is_a($definition->getClass(), CacheTagProviderInterface::class, true)) {
                $definition->setPublic(true);
            }
        }
    }
}
