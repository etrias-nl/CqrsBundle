<?php

declare(strict_types=1);


namespace Etrias\CqrsBundle\Cache;


use Etrias\CqrsBundle\Cache\CacheConfig;
use Etrias\CqrsBundle\Exception\MissingConfigurationException;

class CacheConfigRegistry
{
    /**
     * @var CacheConfig[]
     */
    protected $items = [];

    public function addConfig(string $commandClassName, CacheConfig $item)
    {
        $this->items[$commandClassName] = $item;

        return $this;
    }

    public function removeConfig(string $commandClassName)
    {
        if (array_key_exists($commandClassName, $this->items)) {
            unset($this->items[$commandClassName]);
        }

        return $this;
    }

    public function hasConfig(string $commandClassName)
    {
        return array_key_exists($commandClassName, $this->items);
    }

    public function getConfig(string $commandClassName)
    {
        if (!array_key_exists($commandClassName, $this->items)) {
            if ($commandClassName === 'default') {
                throw new MissingConfigurationException();
            } else {
                return $this->getConfig('default');
            }
        }

        return $this->items[$commandClassName];
    }
}
