<?php

declare(strict_types=1);


namespace Etrias\CqrsBundle\DependencyInjection\Compiler;


use Etrias\CqrsBundle\Handlers\HandlerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HandlerPass implements CompilerPassInterface
{

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $definition) {
            if(is_a($definition->getClass(), HandlerInterface::class, true)) {
                $definition->addTag('tactician.handler', ['typehints' => true]);
            }
        }
    }
}
