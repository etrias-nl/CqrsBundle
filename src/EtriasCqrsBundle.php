<?php

declare(strict_types=1);


namespace Etrias\CqrsBundle;

use Etrias\CqrsBundle\DependencyInjection\Compiler\CacheTagProviderPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EtriasCqrsBundle extends Bundle
{
    /**
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CacheTagProviderPass(), PassConfig::TYPE_AFTER_REMOVING);
    }
}
