<?php

declare(strict_types=1);


namespace Etrias\CqrsBundle\Cache\NameStrategy;


use Etrias\CqrsBundle\Command\QueryInterface;

interface NameStrategyInterface
{
    /**
     * @param QueryInterface $command
     * @return string
     */
    public function getName(QueryInterface $command): string;
}
