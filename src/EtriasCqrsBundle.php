<?php

declare(strict_types=1);


namespace Etrias\CqrsBundle;

use Etrias\CqrsBundle\DependencyInjection\Compiler\CacheTagProviderPass;
use Etrias\CqrsBundle\DependencyInjection\Compiler\HandlerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EtriasCqrsBundle extends Bundle
{
}
