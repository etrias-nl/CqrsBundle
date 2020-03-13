<?php

declare(strict_types=1);


namespace Etrias\CqrsBundle\DependencyInjection\Compiler;


use Etrias\CqrsBundle\Cache\TagProvider\CacheTagProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CacheTagProviderPass implements CompilerPassInterface
{

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $definition) {
            if(is_a($definition->getClass(), CacheTagProviderInterface::class, true)) {
                $definition->setPublic(true);
            }
        }
    }
}
