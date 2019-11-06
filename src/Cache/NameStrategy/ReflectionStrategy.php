<?php

declare(strict_types=1);


namespace Etrias\CqrsBundle\Cache\NameStrategy;


use Etrias\CqrsBundle\Command\QueryInterface;

class ReflectionStrategy implements NameStrategyInterface
{
    /**
     * @var string
     */
    protected $separator = '_';

    /**
     * @param QueryInterface $command
     * @return string
     */
    public function getName(QueryInterface $command): string
    {
        $reflection = new \ReflectionClass($command);

        $stringProperties = array_map(function (\ReflectionProperty $property) use ($command, $reflection) {
            $property->setAccessible(true);

            $value = $property->getValue($command);

            if (is_array($value)) {
                $newValues = array_map(function($value) {
                    return (string) $value;
                }, $value);

                $value = implode($this->separator, $newValues);
            }

            if ($value instanceof \DateTime) {
                return $value->format('YmdHis');
            } else {
                return (string)$value;
            }
        }, $reflection->getProperties());

        $cacheName = get_class($command) . $this->separator . implode($this->separator, $stringProperties);

        return strtr($cacheName, '{}()/\@:', '########');
    }
}
